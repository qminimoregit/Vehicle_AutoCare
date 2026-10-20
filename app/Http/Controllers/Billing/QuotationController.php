<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JobCard;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Service;
use App\Models\Vehicle;
use App\Services\BranchContext;
use App\Services\Inventory\StockService;
use App\Services\NumberToWordsHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function __construct(
        protected StockService $stockService
    ) {}

    /**
     * Display listing of quotations with status filters, branch scoping, and search.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $search = trim((string) $request->query('search', ''));
        $activeBranchId = BranchContext::getActiveBranchId();

        $query = Quotation::with(['customer', 'vehicle', 'branch', 'creator', 'convertedInvoice', 'convertedJobCard'])
            ->orderBy('id', 'desc');

        // Scoping by Branch if user is not global admin or active branch selected
        if ($activeBranchId) {
            $query->where(function ($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId)
                    ->orWhereNull('branch_id');
            });
        }

        // Status Filter
        if ($status !== 'all' && in_array($status, ['draft', 'sent', 'accepted', 'rejected', 'expired', 'converted', 'cancelled'])) {
            $query->where('status', $status);
        }

        // Search Filter
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('customer_name', 'like', "%{$search}%")
                            ->orWhere('company_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('vehicle', function ($vq) use ($search) {
                        $vq->where('vehicle_number', 'like', "%{$search}%");
                    });
            });
        }

        $quotations = $query->paginate(15)->withQueryString();

        // Status counts for quick tabs
        $baseCountQuery = Quotation::query();
        if ($activeBranchId) {
            $baseCountQuery->where(function ($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId)
                    ->orWhereNull('branch_id');
            });
        }

        $counts = [
            'all' => (clone $baseCountQuery)->count(),
            'draft' => (clone $baseCountQuery)->where('status', 'draft')->count(),
            'sent' => (clone $baseCountQuery)->where('status', 'sent')->count(),
            'accepted' => (clone $baseCountQuery)->where('status', 'accepted')->count(),
            'converted' => (clone $baseCountQuery)->where('status', 'converted')->count(),
            'rejected' => (clone $baseCountQuery)->where('status', 'rejected')->count(),
        ];

        return view('billing.quotations.index', compact('quotations', 'status', 'search', 'counts'));
    }

    /**
     * Show form for creating a new quotation.
     */
    public function create(Request $request): View
    {
        $customers = Customer::where('status', 'active')->orderBy('customer_name')->get();
        $vehicles = Vehicle::with(['customer', 'vehicleType'])->orderBy('vehicle_number')->get();
        $services = Service::active()->orderBy('name')->get();
        
        $products = Product::active()->orderBy('name')->get()->map(function ($product) {
            $product->available_stock = $this->stockService->getAvailableStock($product);
            return $product;
        });

        $company = CompanySetting::getSettings();
        $suggestedNumber = Quotation::generateQuotationNumber();

        // Optional preselected customer or vehicle
        $selectedCustomerId = $request->query('customer_id');
        $selectedVehicleId = $request->query('vehicle_id');

        return view('billing.quotations.create', compact(
            'customers',
            'vehicles',
            'services',
            'products',
            'company',
            'suggestedNumber',
            'selectedCustomerId',
            'selectedVehicleId'
        ));
    }

    /**
     * Store a newly created quotation.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
            'quotation_date' => ['required', 'date'],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:quotation_date'],
            'subject' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'discount_type' => ['nullable', 'in:none,percentage,fixed'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'tax_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'additional_charges' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'terms_conditions' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_type' => ['required', 'in:service,product,custom'],
            'items.*.item_id' => ['nullable', 'integer'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit' => ['nullable', 'string', 'max:50'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount_type' => ['nullable', 'in:none,percentage,fixed'],
            'items.*.discount_value' => ['nullable', 'numeric', 'min:0'],
        ]);

        $activeBranchId = BranchContext::getActiveBranchId();
        $customer = Customer::findOrFail($validated['customer_id']);
        $vehicle = !empty($validated['vehicle_id']) ? Vehicle::find($validated['vehicle_id']) : null;
        $company = CompanySetting::getSettings();

        DB::beginTransaction();
        try {
            $quotationNumber = Quotation::generateQuotationNumber($activeBranchId);

            // Calculate item totals
            $subtotal = 0.0;
            $itemsData = [];

            foreach ($validated['items'] as $index => $itemInput) {
                $qty = (float) $itemInput['quantity'];
                $unitPrice = (float) $itemInput['unit_price'];
                $itemSubtotal = round($qty * $unitPrice, 2);

                $itemDiscType = $itemInput['discount_type'] ?? 'none';
                $itemDiscVal = (float) ($itemInput['discount_value'] ?? 0);
                $itemDiscAmount = 0.0;

                if ($itemDiscType === 'percentage') {
                    $itemDiscAmount = round($itemSubtotal * ($itemDiscVal / 100), 2);
                } elseif ($itemDiscType === 'fixed') {
                    $itemDiscAmount = min($itemSubtotal, $itemDiscVal);
                }

                $lineTotal = max(0, $itemSubtotal - $itemDiscAmount);
                $subtotal += $lineTotal;

                $itemsData[] = [
                    'sort_order' => $index + 1,
                    'item_type' => $itemInput['item_type'],
                    'item_id' => $itemInput['item_id'] ?? null,
                    'item_code' => $itemInput['item_code'] ?? null,
                    'item_name' => $itemInput['item_name'],
                    'description' => $itemInput['description'] ?? null,
                    'quantity' => $qty,
                    'unit' => $itemInput['unit'] ?? 'Unit',
                    'unit_price' => $unitPrice,
                    'discount_type' => $itemDiscType,
                    'discount_value' => $itemDiscVal,
                    'discount_amount' => $itemDiscAmount,
                    'line_total' => $lineTotal,
                ];
            }

            // Document-level calculations
            $docDiscType = $validated['discount_type'] ?? 'none';
            $docDiscVal = (float) ($validated['discount_value'] ?? 0);
            $docDiscAmount = 0.0;

            if ($docDiscType === 'percentage') {
                $docDiscAmount = round($subtotal * ($docDiscVal / 100), 2);
            } elseif ($docDiscType === 'fixed') {
                $docDiscAmount = min($subtotal, $docDiscVal);
            }

            $taxableSubtotal = max(0, $subtotal - $docDiscAmount);
            $taxPercentage = (float) ($validated['tax_percentage'] ?? 0);
            $taxAmount = 0.0;

            if ($taxPercentage > 0) {
                $taxAmount = round($taxableSubtotal * ($taxPercentage / 100), 2);
            }

            $additionalCharges = (float) ($validated['additional_charges'] ?? 0);
            $grandTotal = max(0, $taxableSubtotal + $taxAmount + $additionalCharges);

            // Snapshots
            $customerSnapshot = [
                'name' => $customer->customer_name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'customer_type' => $customer->customer_type,
                'company_name' => $customer->company_name,
                'tax_number' => $customer->tax_number,
                'address' => $customer->address,
            ];

            $vehicleSnapshot = $vehicle ? [
                'vehicle_number' => $vehicle->vehicle_number,
                'make_model' => $vehicle->make_model,
                'vehicle_type' => $vehicle->vehicleType?->name,
                'current_mileage' => $vehicle->current_mileage,
            ] : null;

            $companySnapshot = [
                'company_name' => $company->company_name,
                'registration_number' => $company->registration_number,
                'address' => $company->address,
                'phone' => $company->phone,
                'email' => $company->email,
                'tax_number' => $company->tax_number,
            ];

            $quotation = Quotation::create([
                'branch_id' => $activeBranchId,
                'customer_id' => $customer->id,
                'vehicle_id' => $vehicle?->id,
                'quotation_number' => $quotationNumber,
                'quotation_date' => $validated['quotation_date'],
                'expiry_date' => $validated['expiry_date'] ?? null,
                'subject' => $validated['subject'] ?? ($vehicle ? "Service Quotation for {$vehicle->vehicle_number}" : "Quotation for {$customer->customer_name}"),
                'reference' => $validated['reference'] ?? null,
                'subtotal' => $subtotal,
                'discount_type' => $docDiscType,
                'discount_value' => $docDiscVal,
                'discount_amount' => $docDiscAmount,
                'tax_percentage' => $taxPercentage,
                'tax_amount' => $taxAmount,
                'additional_charges' => $additionalCharges,
                'grand_total' => $grandTotal,
                'status' => 'draft',
                'notes' => $validated['notes'] ?? null,
                'terms_conditions' => $validated['terms_conditions'] ?? 'Valid for 14 days from issue date. Prices subject to workshop parts availability.',
                'customer_snapshot' => $customerSnapshot,
                'vehicle_snapshot' => $vehicleSnapshot,
                'company_snapshot' => $companySnapshot,
                'created_by' => Auth::id(),
            ]);

            foreach ($itemsData as $itemRow) {
                $itemRow['quotation_id'] = $quotation->id;
                QuotationItem::create($itemRow);
            }

            DB::commit();

            return redirect()->route('billing.quotations.show', $quotation)
                ->with('success', "Quotation #{$quotation->quotation_number} created successfully.");
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', "Failed to create quotation: {$e->getMessage()}");
        }
    }

    /**
     * Display the specified quotation with line items and operational actions.
     */
    public function show(Quotation $quotation): View
    {
        $quotation->load([
            'customer',
            'vehicle.vehicleType',
            'branch',
            'creator',
            'items',
            'convertedInvoice',
            'convertedJobCard',
        ]);

        $company = CompanySetting::getSettings();
        $amountInWords = NumberToWordsHelper::spell($quotation->grand_total, 'LKR');

        return view('billing.quotations.show', compact('quotation', 'company', 'amountInWords'));
    }

    /**
     * Show form for editing an existing draft quotation.
     */
    public function edit(Quotation $quotation): View|RedirectResponse
    {
        if (!$quotation->canEdit()) {
            return redirect()->route('billing.quotations.show', $quotation)
                ->with('error', 'Only draft or sent quotations can be modified.');
        }

        $quotation->load(['items', 'customer', 'vehicle']);
        $customers = Customer::where('status', 'active')->orderBy('customer_name')->get();
        $vehicles = Vehicle::with(['customer', 'vehicleType'])->orderBy('vehicle_number')->get();
        $services = Service::active()->orderBy('name')->get();
        
        $products = Product::active()->orderBy('name')->get()->map(function ($product) {
            $product->available_stock = $this->stockService->getAvailableStock($product);
            return $product;
        });

        return view('billing.quotations.edit', compact('quotation', 'customers', 'vehicles', 'services', 'products'));
    }

    /**
     * Update an existing draft quotation.
     */
    public function update(Request $request, Quotation $quotation): RedirectResponse
    {
        if (!$quotation->canEdit()) {
            return redirect()->route('billing.quotations.show', $quotation)
                ->with('error', 'Only draft or sent quotations can be modified.');
        }

        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
            'quotation_date' => ['required', 'date'],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:quotation_date'],
            'subject' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'discount_type' => ['nullable', 'in:none,percentage,fixed'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'tax_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'additional_charges' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'terms_conditions' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_type' => ['required', 'in:service,product,custom'],
            'items.*.item_id' => ['nullable', 'integer'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit' => ['nullable', 'string', 'max:50'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount_type' => ['nullable', 'in:none,percentage,fixed'],
            'items.*.discount_value' => ['nullable', 'numeric', 'min:0'],
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);
        $vehicle = !empty($validated['vehicle_id']) ? Vehicle::find($validated['vehicle_id']) : null;

        DB::beginTransaction();
        try {
            // Delete old items and re-insert updated items
            $quotation->items()->delete();

            $subtotal = 0.0;
            foreach ($validated['items'] as $index => $itemInput) {
                $qty = (float) $itemInput['quantity'];
                $unitPrice = (float) $itemInput['unit_price'];
                $itemSubtotal = round($qty * $unitPrice, 2);

                $itemDiscType = $itemInput['discount_type'] ?? 'none';
                $itemDiscVal = (float) ($itemInput['discount_value'] ?? 0);
                $itemDiscAmount = 0.0;

                if ($itemDiscType === 'percentage') {
                    $itemDiscAmount = round($itemSubtotal * ($itemDiscVal / 100), 2);
                } elseif ($itemDiscType === 'fixed') {
                    $itemDiscAmount = min($itemSubtotal, $itemDiscVal);
                }

                $lineTotal = max(0, $itemSubtotal - $itemDiscAmount);
                $subtotal += $lineTotal;

                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'sort_order' => $index + 1,
                    'item_type' => $itemInput['item_type'],
                    'item_id' => $itemInput['item_id'] ?? null,
                    'item_code' => $itemInput['item_code'] ?? null,
                    'item_name' => $itemInput['item_name'],
                    'description' => $itemInput['description'] ?? null,
                    'quantity' => $qty,
                    'unit' => $itemInput['unit'] ?? 'Unit',
                    'unit_price' => $unitPrice,
                    'discount_type' => $itemDiscType,
                    'discount_value' => $itemDiscVal,
                    'discount_amount' => $itemDiscAmount,
                    'line_total' => $lineTotal,
                ]);
            }

            // Document-level calculations
            $docDiscType = $validated['discount_type'] ?? 'none';
            $docDiscVal = (float) ($validated['discount_value'] ?? 0);
            $docDiscAmount = 0.0;

            if ($docDiscType === 'percentage') {
                $docDiscAmount = round($subtotal * ($docDiscVal / 100), 2);
            } elseif ($docDiscType === 'fixed') {
                $docDiscAmount = min($subtotal, $docDiscVal);
            }

            $taxableSubtotal = max(0, $subtotal - $docDiscAmount);
            $taxPercentage = (float) ($validated['tax_percentage'] ?? 0);
            $taxAmount = 0.0;

            if ($taxPercentage > 0) {
                $taxAmount = round($taxableSubtotal * ($taxPercentage / 100), 2);
            }

            $additionalCharges = (float) ($validated['additional_charges'] ?? 0);
            $grandTotal = max(0, $taxableSubtotal + $taxAmount + $additionalCharges);

            $quotation->update([
                'customer_id' => $customer->id,
                'vehicle_id' => $vehicle?->id,
                'quotation_date' => $validated['quotation_date'],
                'expiry_date' => $validated['expiry_date'] ?? null,
                'subject' => $validated['subject'] ?? null,
                'reference' => $validated['reference'] ?? null,
                'subtotal' => $subtotal,
                'discount_type' => $docDiscType,
                'discount_value' => $docDiscVal,
                'discount_amount' => $docDiscAmount,
                'tax_percentage' => $taxPercentage,
                'tax_amount' => $taxAmount,
                'additional_charges' => $additionalCharges,
                'grand_total' => $grandTotal,
                'notes' => $validated['notes'] ?? null,
                'terms_conditions' => $validated['terms_conditions'] ?? null,
            ]);

            DB::commit();

            return redirect()->route('billing.quotations.show', $quotation)
                ->with('success', "Quotation #{$quotation->quotation_number} updated successfully.");
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', "Failed to update quotation: {$e->getMessage()}");
        }
    }

    /**
     * Download Quotation as formatted PDF document.
     */
    public function downloadPdf(Quotation $quotation)
    {
        $quotation->load(['customer', 'vehicle.vehicleType', 'branch', 'items']);
        $company = CompanySetting::getSettings();
        $amountInWords = NumberToWordsHelper::spell($quotation->grand_total, 'LKR');

        $pdf = Pdf::loadView('billing.quotations.pdf', compact('quotation', 'company', 'amountInWords'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("Quotation_{$quotation->quotation_number}.pdf");
    }

    /**
     * Printable browser view of quotation.
     */
    public function print(Quotation $quotation): View
    {
        $quotation->load(['customer', 'vehicle.vehicleType', 'branch', 'items']);
        $company = CompanySetting::getSettings();
        $amountInWords = NumberToWordsHelper::spell($quotation->grand_total, 'LKR');

        return view('billing.quotations.print', compact('quotation', 'company', 'amountInWords'));
    }

    /**
     * Mark quotation status as SENT.
     */
    public function markAsSent(Quotation $quotation): RedirectResponse
    {
        if ($quotation->status !== 'draft') {
            return back()->with('error', 'Only draft quotations can be marked as sent.');
        }

        $quotation->update(['status' => 'sent']);
        return back()->with('success', "Quotation #{$quotation->quotation_number} marked as Sent.");
    }

    /**
     * Mark quotation as ACCEPTED by customer.
     */
    public function accept(Quotation $quotation): RedirectResponse
    {
        if (in_array($quotation->status, ['converted', 'cancelled'])) {
            return back()->with('error', 'Cannot accept an already finalized or cancelled quotation.');
        }

        $quotation->update(['status' => 'accepted']);
        return back()->with('success', "Quotation #{$quotation->quotation_number} marked as Accepted.");
    }

    /**
     * Mark quotation as REJECTED.
     */
    public function reject(Quotation $quotation): RedirectResponse
    {
        if ($quotation->status === 'converted') {
            return back()->with('error', 'Cannot reject an already converted quotation.');
        }

        $quotation->update(['status' => 'rejected']);
        return back()->with('success', "Quotation #{$quotation->quotation_number} marked as Rejected.");
    }

    /**
     * Convert an accepted quotation directly into an official AutoCare Invoice.
     */
    public function convertToInvoice(Quotation $quotation): RedirectResponse
    {
        if (!$quotation->canConvert()) {
            return back()->with('error', 'This quotation cannot be converted to an invoice.');
        }

        return DB::transaction(function () use ($quotation) {
            $invoiceNumber = Invoice::generateInvoiceNumber();

            $customerSnapshot = $quotation->customer_snapshot ?? [
                'customer_name' => $quotation->customer->customer_name,
                'phone' => $quotation->customer->phone,
                'email' => $quotation->customer->email,
                'vehicle_number' => $quotation->vehicle?->vehicle_number,
            ];

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'branch_id' => $quotation->branch_id,
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(7)->toDateString(),
                'customer_id' => $quotation->customer_id,
                'quotation_id' => $quotation->id,
                'invoice_type' => 'quotation',
                'subject' => $quotation->subject ?? "Invoice from Quotation #{$quotation->quotation_number}",
                'reference' => $quotation->quotation_number,
                'subtotal' => $quotation->subtotal,
                'discount_type' => $quotation->discount_type,
                'discount_value' => $quotation->discount_value,
                'discount_amount' => $quotation->discount_amount,
                'tax_percentage' => $quotation->tax_percentage,
                'tax_amount' => $quotation->tax_amount,
                'additional_charges' => $quotation->additional_charges,
                'grand_total' => $quotation->grand_total,
                'amount_paid' => 0.00,
                'balance_amount' => $quotation->grand_total,
                'status' => 'issued',
                'notes' => $quotation->notes,
                'terms_conditions' => $quotation->terms_conditions,
                'customer_snapshot' => $customerSnapshot,
                'created_by' => Auth::id(),
            ]);

            // Copy items to invoice
            foreach ($quotation->items as $qItem) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'source_quotation_item_id' => $qItem->id,
                    'sort_order' => $qItem->sort_order,
                    'item_type' => $qItem->item_type,
                    'item_id' => $qItem->item_id,
                    'item_code' => $qItem->item_code,
                    'item_name' => $qItem->item_name,
                    'description' => $qItem->description,
                    'quantity' => $qItem->quantity,
                    'unit' => $qItem->unit,
                    'unit_price' => $qItem->unit_price,
                    'discount_type' => $qItem->discount_type,
                    'discount_value' => $qItem->discount_value,
                    'discount_amount' => $qItem->discount_amount,
                    'line_total' => $qItem->line_total,
                ]);
            }

            // Mark quotation as converted
            $quotation->update([
                'status' => 'converted',
                'converted_invoice_id' => $invoice->id,
            ]);

            return redirect()->route('operations.invoices.show', $invoice)
                ->with('success', "Quotation #{$quotation->quotation_number} successfully converted to Invoice #{$invoice->invoice_number}.");
        });
    }

    /**
     * Create an active Job Card from an accepted vehicle quotation.
     */
    public function createJobCard(Quotation $quotation): RedirectResponse
    {
        if (!$quotation->vehicle_id) {
            return back()->with('error', 'Cannot create a Job Card for a quotation without an assigned vehicle.');
        }

        if (!$quotation->canCreateJobCard()) {
            return back()->with('error', 'This quotation has already been converted to a Job Card.');
        }

        return DB::transaction(function () use ($quotation) {
            $vehicle = $quotation->vehicle;
            $jobNumber = JobCard::generateJobNumber();

            $jobCard = JobCard::create([
                'branch_id' => $quotation->branch_id,
                'job_number' => $jobNumber,
                'date' => now()->toDateString(),
                'customer_id' => $quotation->customer_id,
                'vehicle_id' => $vehicle->id,
                'vehicle_type_id' => $vehicle->vehicle_type_id,
                'current_mileage' => $vehicle->current_mileage,
                'status' => 'pending',
                'service_notes' => "Initiated from Quotation #{$quotation->quotation_number}\n" . ($quotation->subject ? "Subject: {$quotation->subject}\n" : ''),
                'customer_notes' => $quotation->notes,
                'service_total' => 0,
                'product_total' => 0,
                'discount_amount' => $quotation->discount_amount,
                'tax_amount' => $quotation->tax_amount,
                'grand_total' => 0,
                'created_by' => Auth::id(),
            ]);

            // Add quoted services to job card
            foreach ($quotation->items->where('item_type', 'service') as $item) {
                $jobCard->services()->create([
                    'service_id' => $item->item_id,
                    'service_code' => $item->item_code ?? 'SRV-CUSTOM',
                    'service_name' => $item->item_name,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'line_total' => $item->line_total,
                    'notes' => $item->description,
                ]);
            }

            // Recalculate totals
            $jobCard->recalculateTotals();

            // Link to quotation and mark converted
            $quotation->update([
                'status' => 'converted',
                'converted_job_card_id' => $jobCard->id,
            ]);

            return redirect()->route('operations.job-cards.show', $jobCard)
                ->with('success', "Job Card #{$jobCard->job_number} created from Quotation #{$quotation->quotation_number}.");
        });
    }

    /**
     * Clone quotation into a new draft.
     */
    public function clone(Quotation $quotation): RedirectResponse
    {
        return DB::transaction(function () use ($quotation) {
            $newNumber = Quotation::generateQuotationNumber($quotation->branch_id);

            $cloned = $quotation->replicate([
                'quotation_number',
                'status',
                'converted_invoice_id',
                'converted_job_card_id',
                'created_at',
                'updated_at',
            ]);

            $cloned->quotation_number = $newNumber;
            $cloned->quotation_date = now()->toDateString();
            $cloned->expiry_date = now()->addDays(14)->toDateString();
            $cloned->status = 'draft';
            $cloned->created_by = Auth::id();
            $cloned->save();

            foreach ($quotation->items as $item) {
                $clonedItem = $item->replicate(['quotation_id', 'created_at', 'updated_at']);
                $clonedItem->quotation_id = $cloned->id;
                $clonedItem->save();
            }

            return redirect()->route('billing.quotations.show', $cloned)
                ->with('success', "Quotation cloned as new Draft #{$cloned->quotation_number}.");
        });
    }
}
