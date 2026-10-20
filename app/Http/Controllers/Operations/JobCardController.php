<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JobCard;
use App\Models\JobCardProduct;
use App\Models\JobCardService;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceVehiclePrice;
use App\Models\User;
use App\Services\Inventory\StockService;
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
                           ->orWhere('vehicle_number_normalized', 'like', "%" . strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $search)) . "%");
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
            'services.service',
            'products.product',
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

        if (!$pricing || $pricing->service_price === null) {
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
        ]);

        $service = Service::findOrFail($validated['service_id']);

        // Check if price is configured for this vehicle type
        $pricing = ServiceVehiclePrice::where('service_id', $service->id)
            ->where('vehicle_type_id', $jobCard->vehicle_type_id)
            ->first();

        $user = Auth::user();
        $isPriceCustom = false;
        $unitPrice = 0.0;

        if (!empty($validated['custom_price']) && ($user->isAdmin() || $user->hasPermission('override_prices'))) {
            $unitPrice = (float) $validated['custom_price'];
            $isPriceCustom = true;
        } elseif ($pricing && $pricing->service_price !== null) {
            $unitPrice = (float) $pricing->service_price;
        } else {
            return back()->with('error', "Price not configured for '{$service->name}' on {$jobCard->vehicleType->name}. Please configure it under Master Data → Manage Service Payment, or request an Admin override.");
        }

        $qty = !empty($validated['quantity']) ? (float) $validated['quantity'] : 1.0;
        $lineTotal = round($unitPrice * $qty, 2);

        $jobCard->services()->create([
            'service_id' => $service->id,
            'service_code' => $service->code,
            'service_name' => $service->name,
            'unit_price' => $unitPrice,
            'quantity' => $qty,
            'line_total' => $lineTotal,
            'is_price_custom' => $isPriceCustom,
            'technician_name' => $validated['technician_name'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $jobCard->recalculateTotals();

        return back()->with('success', "Service '{$service->name}' added to Job Card.");
    }

    /**
     * Remove a service from the job card.
     */
    public function removeService(JobCard $jobCard, JobCardService $serviceItem): RedirectResponse
    {
        if (in_array($jobCard->status, ['completed', 'delivered', 'cancelled'])) {
            return back()->with('error', 'Cannot modify services on a finished or cancelled job.');
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
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $quantity = (float) $validated['quantity'];

        // Selling price defaults to product selling price
        $unitPrice = !empty($validated['unit_price']) ? (float) $validated['unit_price'] : (float) $product->selling_price;
        $lineTotal = round($unitPrice * $quantity, 2);

        try {
            DB::transaction(function () use ($jobCard, $product, $quantity, $unitPrice, $lineTotal, $validated) {
                // Deduct stock using FIFO atomically
                $issueResult = $this->stockService->issueStockFifo(
                    $product,
                    $quantity,
                    'job_card_issue',
                    $jobCard,
                    Auth::user()
                );

                $unitCost = $quantity > 0 ? round($issueResult['total_cost'] / $quantity, 4) : 0.0;

                $jobCard->products()->create([
                    'product_id' => $product->id,
                    'product_sku' => $product->sku,
                    'product_name' => $product->product_name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'unit_cost' => $unitCost,
                    'line_total' => $lineTotal,
                    'is_stock_deducted' => true,
                    'batch_allocations' => $issueResult['batches'],
                    'notes' => $validated['notes'] ?? null,
                ]);

                $jobCard->recalculateTotals();
            });

            return back()->with('success', "Product '{$product->product_name}' (Qty: {$quantity}) issued to Job Card.");
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

        if ($productItem->job_card_id !== $jobCard->id) {
            abort(403);
        }

        try {
            DB::transaction(function () use ($jobCard, $productItem) {
                // Reverse stock if deducted
                if ($productItem->is_stock_deducted && !empty($productItem->batch_allocations)) {
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

            return back()->with('success', "Product removed and stock returned to inventory.");
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

        $jobCard->update([
            'assigned_to' => $validated['assigned_to'] ?? $jobCard->assigned_to,
            'current_mileage' => $validated['current_mileage'] ?? $jobCard->current_mileage,
            'next_service_mileage' => $validated['next_service_mileage'] ?? $jobCard->next_service_mileage,
            'discount_amount' => $validated['discount_amount'] ?? $jobCard->discount_amount,
            'service_notes' => $validated['service_notes'] ?? $jobCard->service_notes,
            'customer_notes' => $validated['customer_notes'] ?? $jobCard->customer_notes,
        ]);

        // Keep vehicle mileage up-to-date
        if (!empty($validated['current_mileage']) && $validated['current_mileage'] > $jobCard->vehicle->current_mileage) {
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
            if (!in_array($jobCard->status, ['completed', 'delivered'])) {
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
        if (!$user->isAdmin() && !$user->hasPermission('cancel_jobs')) {
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
                    if ($productItem->is_stock_deducted && !empty($productItem->batch_allocations)) {
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
                        'notes' => ($jobCard->invoice->notes ? $jobCard->invoice->notes . "\n" : '') . "Cancelled due to Job cancellation: {$validated['cancellation_reason']}",
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

            // Link invoice to job card
            $jobCard->update(['invoice_id' => $invoice->id]);

            return redirect()->route('operations.invoices.show', $invoice)
                ->with('success', "Invoice #{$invoice->invoice_number} generated for Job #{$jobCard->job_number}.");
        });
    }
}
