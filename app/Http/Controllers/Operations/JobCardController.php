<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JobCard;
use App\Models\JobCardProduct;
use App\Models\JobCardService;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceVehiclePrice;
use App\Models\User;
use App\Services\BranchContext;
use App\Services\Inventory\StockService;
use App\Services\QuantityFormatter;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class JobCardController extends Controller
{
    public function __construct(
        protected StockService $stockService
    ) {}

    /**
     * Display listing of job cards with status filters and quick search.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $search = trim((string) $request->query('search', ''));
        $dateFilter = $request->query('filter', '');

        $query = JobCard::with(['vehicle', 'customer', 'vehicleType', 'assignedTechnician', 'invoice'])
            ->orderBy('id', 'desc');

        // Apply Tab / Status Filters
        if ($dateFilter === 'today') {
            $query->whereDate('date', now()->toDateString());
        } elseif ($dateFilter === 'previous_pending') {
            $query->where('status', 'pending')->whereDate('date', '<', now()->toDateString());
        } elseif ($status !== 'all' && in_array($status, ['pending', 'in_progress', 'completed', 'delivered', 'cancelled'])) {
            $query->where('status', $status);
        }

        // Search Filter
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('job_number', 'like', "%{$search}%")
                    ->orWhereHas('vehicle', function ($vq) use ($search) {
                        $vq->where('vehicle_number', 'like', "%{$search}%")
                            ->orWhere('vehicle_number_normalized', 'like', '%'.strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $search)).'%');
                    })
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('customer_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $jobs = $query->paginate(15)->withQueryString();

        // Counters for Quick Status Tabs
        $counts = [
            'all' => JobCard::count(),
            'today' => JobCard::whereDate('date', now()->toDateString())->count(),
            'pending' => JobCard::where('status', 'pending')->count(),
            'in_progress' => JobCard::where('status', 'in_progress')->count(),
            'completed' => JobCard::where('status', 'completed')->count(),
            'previous_pending' => JobCard::where('status', 'pending')->whereDate('date', '<', now()->toDateString())->count(),
        ];

        return view('operations.jobs.index', compact('jobs', 'status', 'search', 'dateFilter', 'counts'));
    }

    /**
     * Display a specific job card with all services, products, and available actions.
     */
    public function show(JobCard $jobCard): View
    {
        $jobCard->load([
            'vehicle',
            'customer',
            'vehicleType',
            'assignedTechnician',
            'creator',
            'invoice',
            'supplementaryInvoices',
            'services.service',
            'products.product',
            'issues.approvedByUser',
            'issues.recordedByUser',
        ]);

        $availableServices = Service::active()->orderBy('name')->get();

        // Products with stock availability
        $availableProducts = Product::active()->orderBy('name')->get()->map(function ($product) {
            $product->available_stock = $this->stockService->getAvailableStock($product);

            return $product;
        });

        // Technicians for assignment
        $technicians = User::where('status', 'active')->orderBy('name')->get();

        return view('operations.jobs.show', compact('jobCard', 'availableServices', 'availableProducts', 'technicians'));
    }

    /**
     * Fetch service price configured for the job card's vehicle type.
     */
    public function getServicePrice(JobCard $jobCard, Service $service): JsonResponse
    {
        $pricing = ServiceVehiclePrice::where('service_id', $service->id)
            ->where('vehicle_type_id', $jobCard->vehicle_type_id)
            ->first();

        if (! $pricing || $pricing->service_price === null) {
            return response()->json([
                'configured' => false,
                'message' => "Price not configured for {$jobCard->vehicleType->name}.",
                'price' => null,
            ]);
        }

        return response()->json([
            'configured' => true,
            'price' => (float) $pricing->service_price,
            'cost' => (float) ($pricing->service_cost ?? 0),
        ]);
    }

    /**
     * Add a service to the job card.
     */
    public function addService(Request $request, JobCard $jobCard): RedirectResponse
    {
        if (in_array($jobCard->status, ['completed', 'delivered', 'cancelled'])) {
            return back()->with('error', 'Cannot modify services on a finished or cancelled job.');
        }

        $validated = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'custom_price' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'numeric', 'min:0.1'],
            'technician_name' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
            'job_issue_id' => ['nullable', 'exists:job_issues,id'],
        ]);

        $service = Service::findOrFail($validated['service_id']);

        // Check if price is configured for this vehicle type
        $pricing = ServiceVehiclePrice::where('service_id', $service->id)
            ->where('vehicle_type_id', $jobCard->vehicle_type_id)
            ->first();

        $user = Auth::user();
        $isPriceCustom = false;
        $unitPrice = 0.0;

        if (! empty($validated['custom_price']) && ($user->isAdmin() || $user->hasPermission('override_prices'))) {
            $unitPrice = (float) $validated['custom_price'];
            $isPriceCustom = true;
        } elseif ($pricing && $pricing->service_price !== null) {
            $unitPrice = (float) $pricing->service_price;
        } else {
            return back()->with('error', "Price not configured for '{$service->name}' on {$jobCard->vehicleType->name}. Please configure it under Master Data → Manage Service Payment, or request an Admin override.");
        }

        $qty = ! empty($validated['quantity']) ? (float) $validated['quantity'] : 1.0;
        $lineTotal = round($unitPrice * $qty, 2);
        $isAdditional = ($jobCard->status === 'additional_work') || ($jobCard->invoice_id !== null);

        $serviceItem = $jobCard->services()->create([
            'service_id' => $service->id,
            'service_code' => $service->code,
            'service_name' => $service->name,
            'unit_price' => $unitPrice,
            'quantity' => $qty,
            'line_total' => $lineTotal,
            'is_price_custom' => $isPriceCustom,
            'is_additional' => $isAdditional,
            'job_issue_id' => $validated['job_issue_id'] ?? null,
            'technician_name' => $validated['technician_name'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $jobCard->recalculateTotals();

        return back()->with('success', "Service '{$service->name}' added to Job Card.")
            ->with('newly_added_service_id', $serviceItem->id);
    }

    /**
     * Remove a service from the job card.
     */
    public function removeService(JobCard $jobCard, JobCardService $serviceItem): RedirectResponse
    {
        if (in_array($jobCard->status, ['completed', 'delivered', 'cancelled'])) {
            return back()->with('error', 'Cannot modify services on a finished or cancelled job.');
        }

        if ($serviceItem->invoice_id) {
            return back()->with('error', 'Cannot remove a service that has already been invoiced.');
        }

        if ($serviceItem->job_card_id !== $jobCard->id) {
            abort(403);
        }

        $serviceName = $serviceItem->service_name;
        $serviceItem->delete();
        $jobCard->recalculateTotals();

        return back()->with('success', "Service '{$serviceName}' removed from Job Card.");
    }

    /**
     * Add product/part to job card and confirm stock deduction using FIFO.
     */
    public function addProduct(Request $request, JobCard $jobCard): RedirectResponse
    {
        if (in_array($jobCard->status, ['completed', 'delivered', 'cancelled'])) {
            return back()->with('error', 'Cannot add products to a finished or cancelled job.');
        }

        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
            'job_issue_id' => ['nullable', 'exists:job_issues,id'],
        ]);

        $product = Product::with('uom')->findOrFail($validated['product_id']);
        $quantity = (float) $validated['quantity'];

        if (! $product->allowsFractionalQuantity() && floor($quantity) != $quantity) {
            return back()->with('error', "Product '{$product->product_name}' ({$product->uom?->name}) requires a whole-number quantity.")->withInput();
        }

        // Selling price defaults to product selling price
        $unitPrice = ! empty($validated['unit_price']) ? (float) $validated['unit_price'] : (float) $product->selling_price;
        $lineTotal = round($unitPrice * $quantity, 2);
        $isAdditional = ($jobCard->status === 'additional_work') || ($jobCard->invoice_id !== null);

        $productItem = null;
        try {
            DB::transaction(function () use ($jobCard, $product, $quantity, $unitPrice, $lineTotal, $validated, $isAdditional, &$productItem) {
                // Deduct stock using FIFO atomically
                $issueResult = $this->stockService->issueStockFifo(
                    $product,
                    $quantity,
                    'job_card_issue',
                    $jobCard,
                    Auth::user()
                );

                $unitCost = $quantity > 0 ? round($issueResult['total_cost'] / $quantity, 4) : 0.0;

                $productItem = $jobCard->products()->create([
                    'product_id' => $product->id,
                    'product_sku' => $product->sku,
                    'product_name' => $product->product_name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'unit_cost' => $unitCost,
                    'line_total' => $lineTotal,
                    'is_stock_deducted' => true,
                    'is_additional' => $isAdditional,
                    'job_issue_id' => $validated['job_issue_id'] ?? null,
                    'batch_allocations' => $issueResult['batches'],
                    'notes' => $validated['notes'] ?? null,
                ]);

                $jobCard->recalculateTotals();
            });

            $fmtQty = QuantityFormatter::format($quantity);

            return back()->with('success', "Product '{$product->product_name}' (Qty: {$fmtQty}) issued to Job Card.")
                ->with('newly_added_product_id', $productItem?->id);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove a product from job card and reverse allocated FIFO stock back into batches.
     */
    public function removeProduct(JobCard $jobCard, JobCardProduct $productItem): RedirectResponse
    {
        if (in_array($jobCard->status, ['completed', 'delivered', 'cancelled'])) {
            return back()->with('error', 'Cannot remove products from a finished or cancelled job.');
        }

        if ($productItem->invoice_id) {
            return back()->with('error', 'Cannot remove a product that has already been invoiced.');
        }

        if ($productItem->job_card_id !== $jobCard->id) {
            abort(403);
        }

        try {
            DB::transaction(function () use ($jobCard, $productItem) {
                // Reverse stock if deducted
                if ($productItem->is_stock_deducted && ! empty($productItem->batch_allocations)) {
                    $this->stockService->reverseStockFifo(
                        $productItem->batch_allocations,
                        $jobCard,
                        Auth::user(),
                        "Removed from Job Card #{$jobCard->job_number}"
                    );
                }

                $productItem->delete();
                $jobCard->recalculateTotals();
            });

            return back()->with('success', 'Product removed and stock returned to inventory.');
        } catch (Exception $e) {
            return back()->with('error', "Failed to remove product: {$e->getMessage()}");
        }
    }

    /**
     * Update job details (assigned technician, mileage, discount, notes).
     */
    public function update(Request $request, JobCard $jobCard): RedirectResponse
    {
        if ($jobCard->status === 'cancelled') {
            return back()->with('error', 'Cannot edit a cancelled job.');
        }

        $validated = $request->validate([
            'assigned_to' => ['nullable', 'exists:users,id'],
            'current_mileage' => ['nullable', 'integer', 'min:0'],
            'next_service_mileage' => ['nullable', 'integer', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'service_notes' => ['nullable', 'string'],
            'customer_notes' => ['nullable', 'string'],
        ]);

        $updateData = [
            'assigned_to' => $validated['assigned_to'] ?? $jobCard->assigned_to,
            'current_mileage' => $validated['current_mileage'] ?? $jobCard->current_mileage,
            'next_service_mileage' => $validated['next_service_mileage'] ?? $jobCard->next_service_mileage,
            'service_notes' => $validated['service_notes'] ?? $jobCard->service_notes,
            'customer_notes' => $validated['customer_notes'] ?? $jobCard->customer_notes,
        ];

        // Do not allow staff to rewrite financial discount if already invoiced
        if (! $jobCard->invoice_id && array_key_exists('discount_amount', $validated)) {
            $updateData['discount_amount'] = $validated['discount_amount'] !== null ? (float) $validated['discount_amount'] : 0.0;
        }

        $jobCard->update($updateData);

        // Keep vehicle mileage up-to-date
        if (! empty($validated['current_mileage']) && $validated['current_mileage'] > $jobCard->vehicle->current_mileage) {
            $jobCard->vehicle->update([
                'current_mileage' => $validated['current_mileage'],
                'next_service_mileage' => $validated['next_service_mileage'] ?? $jobCard->vehicle->next_service_mileage,
            ]);
        }

        $jobCard->recalculateTotals();

        return back()->with('success', 'Job Card updated successfully.');
    }

    /**
     * Transition job status: Start Work, Complete Job, Mark Delivered.
     */
    public function updateStatus(Request $request, JobCard $jobCard): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:start_work,complete_job,mark_delivered'],
        ]);

        $action = $validated['action'];

        if ($action === 'start_work') {
            if ($jobCard->status !== 'pending') {
                return back()->with('error', 'Only pending jobs can be started.');
            }
            $jobCard->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);

            return back()->with('success', "Service started for Job #{$jobCard->job_number}.");
        }

        if ($action === 'complete_job') {
            if ($jobCard->status !== 'in_progress') {
                return back()->with('error', 'Only in-progress jobs can be completed.');
            }
            if ($jobCard->services()->count() === 0 && $jobCard->products()->count() === 0) {
                return back()->with('error', 'Cannot complete a job with no services or parts.');
            }
            $jobCard->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return back()->with('success', "Job #{$jobCard->job_number} marked as Completed. Ready for billing.");
        }

        if ($action === 'mark_delivered') {
            if (! in_array($jobCard->status, ['completed', 'delivered'])) {
                return back()->with('error', 'Only completed jobs can be delivered.');
            }
            $jobCard->update([
                'status' => 'delivered',
                'delivered_at' => now(),
            ]);

            return back()->with('success', "Vehicle handed over to customer. Job #{$jobCard->job_number} delivered.");
        }

        return back();
    }

    /**
     * Cancel a job card and reverse any stock deductions.
     */
    public function cancel(Request $request, JobCard $jobCard): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('cancel_jobs')) {
            abort(403, 'Unauthorized to cancel job cards.');
        }

        if ($jobCard->status === 'cancelled') {
            return back()->with('error', 'Job is already cancelled.');
        }

        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'min:3', 'max:500'],
        ]);

        try {
            DB::transaction(function () use ($jobCard, $validated, $user) {
                // Reverse all issued products stock
                foreach ($jobCard->products as $productItem) {
                    if ($productItem->is_stock_deducted && ! empty($productItem->batch_allocations)) {
                        $this->stockService->reverseStockFifo(
                            $productItem->batch_allocations,
                            $jobCard,
                            $user,
                            "Cancelled Job #{$jobCard->job_number}: {$validated['cancellation_reason']}"
                        );
                        $productItem->update(['is_stock_deducted' => false]);
                    }
                }

                // If invoice was created, mark it cancelled as well
                if ($jobCard->invoice) {
                    $jobCard->invoice->update([
                        'status' => 'cancelled',
                        'notes' => ($jobCard->invoice->notes ? $jobCard->invoice->notes."\n" : '')."Cancelled due to Job cancellation: {$validated['cancellation_reason']}",
                    ]);
                }

                $jobCard->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => $validated['cancellation_reason'],
                    'cancelled_by' => $user->id,
                    'cancelled_at' => now(),
                ]);
            });

            return redirect()->route('operations.job-cards.index')
                ->with('success', "Job #{$jobCard->job_number} cancelled and issued stock reversed to inventory.");
        } catch (Exception $e) {
            return back()->with('error', "Failed to cancel job: {$e->getMessage()}");
        }
    }

    /**
     * Generate an official Invoice from a Completed Job Card.
     */
    public function generateInvoice(JobCard $jobCard): RedirectResponse
    {
        if ($jobCard->status === 'cancelled') {
            return back()->with('error', 'Cannot generate invoice for a cancelled job.');
        }

        // If invoice already exists, redirect to it
        if ($jobCard->invoice_id && $jobCard->invoice) {
            return redirect()->route('operations.invoices.show', $jobCard->invoice);
        }

        // Credit Limit Validation (Stage 7)
        $customer = $jobCard->customer;
        $creditAccount = $customer?->creditAccount()->where('status', 'active')->first();
        if ($creditAccount && $creditAccount->wouldExceedLimit((float) $jobCard->grand_total)) {
            if (! Auth::user()->isAdmin() && ! request()->boolean('credit_override')) {
                return back()->with('error', 'Credit limit exceeded. Available credit: LKR '.number_format($creditAccount->available_credit, 2).', Job total: LKR '.number_format($jobCard->grand_total, 2).'. Admin override required.');
            }
        }

        return DB::transaction(function () use ($jobCard) {
            $invoiceNumber = Invoice::generateInvoiceNumber();

            $customerSnapshot = [
                'customer_name' => $jobCard->customer->customer_name,
                'phone' => $jobCard->customer->phone,
                'vehicle_number' => $jobCard->vehicle->vehicle_number,
                'vehicle_type' => $jobCard->vehicleType->name,
                'current_mileage' => $jobCard->current_mileage,
                'next_service_mileage' => $jobCard->next_service_mileage,
            ];

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'branch_id' => $jobCard->branch_id,
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->toDateString(),
                'customer_id' => $jobCard->customer_id,
                'job_card_id' => $jobCard->id,
                'invoice_type' => 'job_card',
                'subject' => "Service Invoice for {$jobCard->vehicle->vehicle_number} (Job #{$jobCard->job_number})",
                'reference' => $jobCard->job_number,
                'subtotal' => $jobCard->service_total + $jobCard->product_total,
                'discount_amount' => $jobCard->discount_amount,
                'tax_amount' => $jobCard->tax_amount,
                'grand_total' => $jobCard->grand_total,
                'amount_paid' => 0,
                'balance_amount' => $jobCard->grand_total,
                'status' => 'issued',
                'customer_snapshot' => $customerSnapshot,
                'created_by' => Auth::id(),
            ]);

            // Add services as invoice items
            foreach ($jobCard->services as $serviceItem) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_type' => 'service',
                    'item_id' => $serviceItem->service_id,
                    'item_code' => $serviceItem->service_code,
                    'item_name' => $serviceItem->service_name,
                    'quantity' => $serviceItem->quantity,
                    'unit_price' => $serviceItem->unit_price,
                    'line_total' => $serviceItem->line_total,
                    'notes' => $serviceItem->notes,
                ]);
            }

            // Add products as invoice items
            foreach ($jobCard->products as $productItem) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_type' => 'product',
                    'item_id' => $productItem->product_id,
                    'item_code' => $productItem->product_sku,
                    'item_name' => $productItem->product_name,
                    'quantity' => $productItem->quantity,
                    'unit_price' => $productItem->unit_price,
                    'unit_cost' => $productItem->unit_cost,
                    'line_total' => $productItem->line_total,
                    'notes' => $productItem->notes,
                ]);
            }

            // Link invoice to job card and items
            $jobCard->update(['invoice_id' => $invoice->id]);
            $jobCard->services()->update(['invoice_id' => $invoice->id]);
            $jobCard->products()->update(['invoice_id' => $invoice->id]);
            $jobCard->issues()->where('status', 'approved')->update(['status' => 'fixed']);

            return redirect()->route('operations.invoices.show', $invoice)
                ->with('success', "Invoice #{$invoice->invoice_number} generated for Job #{$jobCard->job_number}.");
        });
    }

    /**
     * Reopen a completed or in-progress job for approved additional repair work.
     */
    public function startAdditionalWork(JobCard $jobCard): RedirectResponse
    {
        if (in_array($jobCard->status, ['delivered', 'cancelled'])) {
            return back()->with('error', 'Cannot add additional work to delivered or cancelled jobs. Please create a follow-up job card instead.');
        }

        // Must have at least one approved issue or allow opening
        $hasApprovedIssues = $jobCard->issues()->where('status', 'approved')->exists();
        if (! $hasApprovedIssues && $jobCard->status === 'completed') {
            return back()->with('error', 'Please record and approve a vehicle issue before starting additional work.');
        }

        $jobCard->update([
            'status' => 'additional_work',
        ]);

        return back()->with('success', "Job #{$jobCard->job_number} workflow changed to Additional Work In Progress. You can now add approved services and parts.");
    }

    /**
     * Mark additional repair work completed.
     */
    public function completeAdditionalWork(JobCard $jobCard): RedirectResponse
    {
        if ($jobCard->status !== 'additional_work') {
            return back()->with('error', 'Job is not currently in Additional Work status.');
        }

        $jobCard->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Mark any approved issues as fixed
        $jobCard->issues()->where('status', 'approved')->update([
            'status' => 'fixed',
        ]);

        $jobCard->recalculateTotals();

        return back()->with('success', "Additional work for Job #{$jobCard->job_number} completed. Ready for billing.");
    }

    /**
     * Generate a supplementary invoice for additional repair work on an already-invoiced job card.
     */
    public function generateSupplementaryInvoice(JobCard $jobCard): RedirectResponse
    {
        if (! $jobCard->invoice_id) {
            // If original invoice was never created, redirect to standard generate invoice
            return $this->generateInvoice($jobCard);
        }

        // Get unbilled services and products
        $unbilledServices = $jobCard->services()->whereNull('invoice_id')->get();
        $unbilledProducts = $jobCard->products()->whereNull('invoice_id')->get();

        if ($unbilledServices->isEmpty() && $unbilledProducts->isEmpty()) {
            return back()->with('error', 'No unbilled additional services or products found on this Job Card.');
        }

        return DB::transaction(function () use ($jobCard, $unbilledServices, $unbilledProducts) {
            $invoiceNumber = Invoice::generateInvoiceNumber();
            $subtotal = (float) $unbilledServices->sum('line_total') + (float) $unbilledProducts->sum('line_total');

            $customerSnapshot = [
                'customer_name' => $jobCard->customer->customer_name,
                'phone' => $jobCard->customer->phone,
                'vehicle_number' => $jobCard->vehicle->vehicle_number,
                'vehicle_type' => $jobCard->vehicleType->name,
                'current_mileage' => $jobCard->current_mileage,
                'next_service_mileage' => $jobCard->next_service_mileage,
                'is_supplementary' => true,
                'parent_invoice' => $jobCard->invoice?->invoice_number,
            ];

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'branch_id' => $jobCard->branch_id,
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->toDateString(),
                'customer_id' => $jobCard->customer_id,
                'job_card_id' => $jobCard->id,
                'invoice_type' => 'job_card',
                'subject' => "Supplementary Invoice – Additional Work for {$jobCard->vehicle->vehicle_number} (Job #{$jobCard->job_number})",
                'reference' => "{$jobCard->job_number}-SUP",
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'grand_total' => $subtotal,
                'amount_paid' => 0,
                'balance_amount' => $subtotal,
                'status' => 'issued',
                'customer_snapshot' => $customerSnapshot,
                'notes' => "Supplementary billing for additional repair work on Job #{$jobCard->job_number} (Original Invoice #{$jobCard->invoice?->invoice_number})",
                'created_by' => Auth::id(),
            ]);

            // Add unbilled services
            foreach ($unbilledServices as $serviceItem) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_type' => 'service',
                    'item_id' => $serviceItem->service_id,
                    'item_code' => $serviceItem->service_code,
                    'item_name' => $serviceItem->service_name.' (Additional Work)',
                    'quantity' => $serviceItem->quantity,
                    'unit_price' => $serviceItem->unit_price,
                    'line_total' => $serviceItem->line_total,
                    'notes' => $serviceItem->notes,
                ]);

                $serviceItem->update(['invoice_id' => $invoice->id]);
            }

            // Add unbilled products
            foreach ($unbilledProducts as $productItem) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_type' => 'product',
                    'item_id' => $productItem->product_id,
                    'item_code' => $productItem->product_sku,
                    'item_name' => $productItem->product_name.' (Additional Work)',
                    'quantity' => $productItem->quantity,
                    'unit_price' => $productItem->unit_price,
                    'unit_cost' => $productItem->unit_cost,
                    'line_total' => $productItem->line_total,
                    'notes' => $productItem->notes,
                ]);

                $productItem->update(['invoice_id' => $invoice->id]);
            }

            // Mark approved issues as fixed
            $jobCard->issues()->where('status', 'approved')->update([
                'status' => 'fixed',
            ]);

            return redirect()->route('operations.invoices.show', $invoice)
                ->with('success', "Supplementary Invoice #{$invoice->invoice_number} generated for additional repair work.");
        });
    }

    /**
     * Create a linked follow-up Job Card for a delivered vehicle returning on another date.
     */
    public function createFollowUp(JobCard $jobCard): RedirectResponse
    {
        $newJob = JobCard::create([
            'job_number' => JobCard::generateJobNumber(),
            'branch_id' => $jobCard->branch_id,
            'customer_id' => $jobCard->customer_id,
            'vehicle_id' => $jobCard->vehicle_id,
            'vehicle_type_id' => $jobCard->vehicle_type_id,
            'date' => now()->toDateString(),
            'current_mileage' => $jobCard->vehicle->current_mileage,
            'next_service_mileage' => $jobCard->vehicle->next_service_mileage,
            'service_notes' => "Follow-up visit for previous Job #{$jobCard->job_number}",
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('operations.job-cards.show', $newJob)
            ->with('success', "Follow-up Job Card #{$newJob->job_number} created for {$jobCard->vehicle->vehicle_number}.");
    }

    /**
     * Printable view of Job Card (Full or Selected Items).
     * Pure read-only operation: no inventory movement, no invoice generation, no status change.
     */
    public function print(Request $request, JobCard $jobCard): View|RedirectResponse
    {
        $user = Auth::user();

        // 1. Branch authorization check
        if ($jobCard->branch_id && ! $user->isAdmin()) {
            $accessible = BranchContext::getAccessibleBranches($user)->pluck('id')->toArray();
            if (! in_array($jobCard->branch_id, $accessible)) {
                abort(403, 'Unauthorized to view or print records from another branch.');
            }
        }

        // 2. Cancelled job authorization check
        if ($jobCard->status === 'cancelled') {
            if (! $user->isAdmin() && ! $user->hasPermission('cancel_jobs')) {
                abort(403, 'Unauthorized to view or print cancelled job cards.');
            }
        }

        // 3. Determine if printing selected items
        $isSelective = $request->boolean('selected') || $request->has('services') || $request->has('products');
        $selectedServiceIds = $request->input('services', []);
        $selectedProductIds = $request->input('products', []);

        if (is_string($selectedServiceIds)) {
            $selectedServiceIds = array_filter(array_map('trim', explode(',', $selectedServiceIds)));
        }
        if (is_string($selectedProductIds)) {
            $selectedProductIds = array_filter(array_map('trim', explode(',', $selectedProductIds)));
        }

        if ($isSelective && empty($selectedServiceIds) && empty($selectedProductIds)) {
            return back()->with('error', 'Please select at least one service or product to print.');
        }

        // 4. Load relationships safely
        $jobCard->load([
            'customer',
            'vehicle.vehicleType',
            'vehicleType',
            'assignedTechnician',
            'creator',
            'branch',
            'issues' => fn ($q) => $q->where('status', 'approved'),
            'invoice.payments',
        ]);

        $services = $jobCard->services;
        $products = $jobCard->products;

        if ($isSelective) {
            $services = $services->whereIn('id', $selectedServiceIds);
            $products = $products->whereIn('id', $selectedProductIds);

            if ($services->isEmpty() && $products->isEmpty()) {
                return back()->with('error', 'Please select at least one service or product to print.');
            }
        }

        $company = CompanySetting::getSettings();

        // Calculate totals for print view (never exposing FIFO costs)
        $servicesTotal = (float) $services->sum('line_total');
        $productsTotal = (float) $products->sum('line_total');
        $grandTotal = $isSelective 
            ? ($servicesTotal + $productsTotal) 
            : (float) $jobCard->grand_total;

        return view('operations.jobs.print', compact(
            'jobCard',
            'company',
            'services',
            'products',
            'isSelective',
            'servicesTotal',
            'productsTotal',
            'grandTotal',
            'selectedServiceIds',
            'selectedProductIds'
        ));
    }

    /**
     * Download Job Card as PDF (Full or Selected Items).
     */
    public function downloadPdf(Request $request, JobCard $jobCard)
    {
        $user = Auth::user();

        // 1. Branch authorization check
        if ($jobCard->branch_id && ! $user->isAdmin()) {
            $accessible = BranchContext::getAccessibleBranches($user)->pluck('id')->toArray();
            if (! in_array($jobCard->branch_id, $accessible)) {
                abort(403, 'Unauthorized to view or download records from another branch.');
            }
        }

        // 2. Cancelled job authorization check
        if ($jobCard->status === 'cancelled') {
            if (! $user->isAdmin() && ! $user->hasPermission('cancel_jobs')) {
                abort(403, 'Unauthorized to view or download cancelled job cards.');
            }
        }

        // 3. Determine if printing selected items
        $isSelective = $request->boolean('selected') || $request->has('services') || $request->has('products');
        $selectedServiceIds = $request->input('services', []);
        $selectedProductIds = $request->input('products', []);

        if (is_string($selectedServiceIds)) {
            $selectedServiceIds = array_filter(array_map('trim', explode(',', $selectedServiceIds)));
        }
        if (is_string($selectedProductIds)) {
            $selectedProductIds = array_filter(array_map('trim', explode(',', $selectedProductIds)));
        }

        if ($isSelective && empty($selectedServiceIds) && empty($selectedProductIds)) {
            return back()->with('error', 'Please select at least one service or product to print.');
        }

        $jobCard->load([
            'customer',
            'vehicle.vehicleType',
            'vehicleType',
            'assignedTechnician',
            'creator',
            'branch',
            'issues' => fn ($q) => $q->where('status', 'approved'),
            'invoice.payments',
        ]);

        $services = $jobCard->services;
        $products = $jobCard->products;

        if ($isSelective) {
            $services = $services->whereIn('id', $selectedServiceIds);
            $products = $products->whereIn('id', $selectedProductIds);

            if ($services->isEmpty() && $products->isEmpty()) {
                return back()->with('error', 'Please select at least one service or product to print.');
            }
        }

        $company = CompanySetting::getSettings();
        $servicesTotal = (float) $services->sum('line_total');
        $productsTotal = (float) $products->sum('line_total');
        $grandTotal = $isSelective 
            ? ($servicesTotal + $productsTotal) 
            : (float) $jobCard->grand_total;

        $pdf = Pdf::loadView('operations.jobs.pdf', compact(
            'jobCard',
            'company',
            'services',
            'products',
            'isSelective',
            'servicesTotal',
            'productsTotal',
            'grandTotal'
        ));
        $pdf->setPaper('a4', 'portrait');

        $suffix = $isSelective ? '_Selected' : '_Full';

        return $pdf->download("JobCard_{$jobCard->job_number}{$suffix}.pdf");
    }
}
