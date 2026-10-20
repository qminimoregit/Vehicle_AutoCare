<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Product;
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

class InvoiceController extends Controller
{
    public function __construct(
        protected StockService $stockService
    ) {}

    /**
     * Display listing of invoices with status filters, branch scoping, and search.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $invoiceType = $request->query('type', 'all');
        $search = trim((string) $request->query('search', ''));
        $activeBranchId = BranchContext::getActiveBranchId();

        $query = Invoice::with(['customer', 'jobCard.vehicle', 'quotation', 'branch', 'creator', 'payments'])
            ->orderBy('id', 'desc');

        // Scoping by Branch if user has an active branch
        if ($activeBranchId) {
            $query->where(function ($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId)
                    ->orWhereNull('branch_id');
            });
        }

        // Status Filter
        if ($status !== 'all' && in_array($status, ['draft', 'issued', 'partially_paid', 'paid', 'cancelled'])) {
            $query->where('status', $status);
        }

        // Invoice Type Filter
        if ($invoiceType !== 'all' && in_array($invoiceType, ['job_card', 'direct_sale', 'quotation', 'manual'])) {
            $query->where('invoice_type', $invoiceType);
        }

        // Search Filter
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('customer_name', 'like', "%{$search}%")
                            ->orWhere('company_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('jobCard.vehicle', function ($vq) use ($search) {
                        $vq->where('vehicle_number', 'like', "%{$search}%");
                    });
            });
        }

        $invoices = $query->paginate(15)->withQueryString();

        // Status counts for quick filter tabs
        $baseCountQuery = Invoice::query();
        if ($activeBranchId) {
            $baseCountQuery->where(function ($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId)
                    ->orWhereNull('branch_id');
            });
        }

        $counts = [
            'all' => (clone $baseCountQuery)->count(),
            'issued' => (clone $baseCountQuery)->where('status', 'issued')->count(),
            'partially_paid' => (clone $baseCountQuery)->where('status', 'partially_paid')->count(),
            'paid' => (clone $baseCountQuery)->where('status', 'paid')->count(),
            'cancelled' => (clone $baseCountQuery)->where('status', 'cancelled')->count(),
        ];

        // Total Outstanding Balance for active branch
        $totalOutstanding = (clone $baseCountQuery)->where('status', '!=', 'cancelled')->sum('balance_amount');

        return view('operations.invoices.index', compact('invoices', 'status', 'invoiceType', 'search', 'counts', 'totalOutstanding'));
    }

    /**
     * Show form for creating a manual invoice (Admin Only).
     */
    public function create(Request $request): View|RedirectResponse
    {
        // Enforce requirement: manual invoice creation restricted to Admin
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Manual invoice creation is restricted to administrators.');
        }

        $customers = Customer::where('status', 'active')->orderBy('customer_name')->get();
        $vehicles = Vehicle::with(['customer', 'vehicleType'])->orderBy('vehicle_number')->get();
        $services = Service::active()->orderBy('name')->get();
        
        $products = Product::active()->orderBy('name')->get()->map(function ($product) {
            $product->available_stock = $this->stockService->getAvailableStock($product);
            return $product;
        });

        $company = CompanySetting::getSettings();
        $suggestedNumber = Invoice::generateInvoiceNumber();

        return view('operations.invoices.create', compact('customers', 'vehicles', 'services', 'products', 'company', 'suggestedNumber'));
    }

    /**
     * Store a manually created invoice (Admin Only).
     */
    public function store(Request $request): RedirectResponse
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Manual invoice creation is restricted to administrators.');
        }

        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:invoice_date'],
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

        DB::beginTransaction();
        try {
            $invoiceNumber = Invoice::generateInvoiceNumber();

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

            $customerSnapshot = [
                'customer_name' => $customer->customer_name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'customer_type' => $customer->customer_type,
                'company_name' => $customer->company_name,
                'tax_number' => $customer->tax_number,
                'address' => $customer->address,
            ];

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'branch_id' => $activeBranchId,
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'] ?? null,
                'customer_id' => $customer->id,
                'invoice_type' => 'manual',
                'subject' => $validated['subject'] ?? "Direct Invoice for {$customer->customer_name}",
                'reference' => $validated['reference'] ?? null,
                'subtotal' => $subtotal,
                'discount_type' => $docDiscType,
                'discount_value' => $docDiscVal,
                'discount_amount' => $docDiscAmount,
                'tax_percentage' => $taxPercentage,
                'tax_amount' => $taxAmount,
                'additional_charges' => $additionalCharges,
                'grand_total' => $grandTotal,
                'amount_paid' => 0.00,
                'balance_amount' => $grandTotal,
                'status' => 'issued',
                'notes' => $validated['notes'] ?? null,
                'terms_conditions' => $validated['terms_conditions'] ?? null,
                'customer_snapshot' => $customerSnapshot,
                'created_by' => Auth::id(),
            ]);

            foreach ($itemsData as $itemRow) {
                $itemRow['invoice_id'] = $invoice->id;
                InvoiceItem::create($itemRow);
            }

            DB::commit();

            return redirect()->route('operations.invoices.show', $invoice)
                ->with('success', "Invoice #{$invoice->invoice_number} created successfully.");
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', "Failed to create invoice: {$e->getMessage()}");
        }
    }

    /**
     * Display the specified invoice with line items, payments, and actions.
     */
    public function show(Invoice $invoice): View
    {
        $invoice->load([
            'customer',
            'jobCard.vehicle',
            'jobCard.vehicleType',
            'quotation',
            'branch',
            'items',
            'payments.creator',
            'creator',
        ]);

        $company = CompanySetting::getSettings();
        $amountInWords = NumberToWordsHelper::spell($invoice->grand_total, 'LKR');

        return view('operations.invoices.show', compact('invoice', 'company', 'amountInWords'));
    }

    /**
     * Download Invoice as formatted PDF document.
     */
    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load([
            'customer',
            'jobCard.vehicle',
            'jobCard.vehicleType',
            'quotation',
            'branch',
            'items',
            'payments.creator',
        ]);

        $company = CompanySetting::getSettings();
        $amountInWords = NumberToWordsHelper::spell($invoice->grand_total, 'LKR');

        $pdf = Pdf::loadView('operations.invoices.pdf', compact('invoice', 'company', 'amountInWords'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("Invoice_{$invoice->invoice_number}.pdf");
    }

    /**
     * Printable view of the invoice / receipt.
     */
    public function print(Invoice $invoice): View
    {
        $invoice->load([
            'customer',
            'jobCard.vehicle',
            'jobCard.vehicleType',
            'quotation',
            'branch',
            'items',
            'payments.creator',
            'creator',
        ]);

        $company = CompanySetting::getSettings();
        $amountInWords = NumberToWordsHelper::spell($invoice->grand_total, 'LKR');

        return view('operations.invoices.print', compact('invoice', 'company', 'amountInWords'));
    }

    /**
     * Record a payment against an outstanding invoice.
     */
    public function addPayment(Request $request, Invoice $invoice): RedirectResponse
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Invoice is already fully paid.');
        }

        if ($invoice->status === 'cancelled') {
            return back()->with('error', 'Cannot record payment for a cancelled invoice.');
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', "max:{$invoice->balance_amount}"],
            'payment_method' => ['required', 'in:cash,card,bank_transfer,cheque'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($invoice, $validated) {
            $paymentAmount = (float) $validated['amount'];

            Payment::create([
                'invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer_id,
                'payment_number' => Payment::generatePaymentNumber(),
                'payment_date' => now()->toDateString(),
                'amount' => $paymentAmount,
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $newPaid = (float) $invoice->amount_paid + $paymentAmount;
            $newBalance = max(0, (float) $invoice->grand_total - $newPaid);
            $newStatus = $newBalance <= 0 ? 'paid' : 'partially_paid';

            $invoice->update([
                'amount_paid' => $newPaid,
                'balance_amount' => $newBalance,
                'status' => $newStatus,
            ]);
        });

        return back()->with('success', 'Payment recorded successfully.');
    }
}
