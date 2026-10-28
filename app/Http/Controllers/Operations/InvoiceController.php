<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceMail;
use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Service;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Services\BranchContext;
use App\Services\Inventory\StockService;
use App\Services\NumberToWordsHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
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
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Manual invoice creation is restricted to administrators.');
        }

        $customers = Customer::where('status', 'active')->orderBy('customer_name')->get();
        $vehicles = Vehicle::with(['customer', 'vehicleType'])->orderBy('vehicle_number')->get();
        $vehicleTypes = VehicleType::active()->orderBy('name')->get();
        $vehicleTypes = VehicleType::active()->orderBy('name')->get();
        $services = Service::active()->orderBy('name')->get();

        $products = Product::active()->orderBy('name')->get()->map(function ($product) {
            $product->available_stock = $this->stockService->getAvailableStock($product);

            return $product;
        });

        $company = CompanySetting::getSettings();
        $suggestedNumber = Invoice::generateInvoiceNumber();
        $selectedCustomerId = $request->query('customer_id');
        $selectedVehicleId = $request->query('vehicle_id');

        return view('operations.invoices.create', compact('customers', 'vehicles', 'vehicleTypes', 'services', 'products', 'company', 'suggestedNumber', 'selectedCustomerId', 'selectedVehicleId'));
    }

    /**
     * Store a manually created invoice (Admin Only).
     */
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Manual invoice creation is restricted to administrators.');
        }

        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
            'customer_search' => ['nullable', 'string'],
            'new_customer_name' => ['nullable', 'string', 'max:150'],
            'new_customer_mobile' => ['nullable', 'string', 'max:50'],
            'new_customer_email' => ['nullable', 'email', 'max:150'],
            'new_vehicle_number' => ['nullable', 'string', 'max:50'],
            'new_vehicle_type_id' => ['nullable', 'exists:vehicle_types,id'],
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

        DB::beginTransaction();
        try {
            // 1. Resolve Customer & Vehicle from direct inputs or customer_search fallback
            $customerId = $validated['customer_id'] ?? null;
            $vehicleId = $validated['vehicle_id'] ?? null;

            if (empty($customerId) && empty($vehicleId) && ! empty($request->input('customer_search'))) {
                $searchVal = trim((string) $request->input('customer_search'));
                if (str_starts_with($searchVal, 'v_')) {
                    $parsedVehId = substr($searchVal, 2);
                    if (is_numeric($parsedVehId) && Vehicle::where('id', $parsedVehId)->exists()) {
                        $vehicleId = (int) $parsedVehId;
                    }
                } elseif (str_starts_with($searchVal, 'c_')) {
                    $parsedCustId = substr($searchVal, 2);
                    if (is_numeric($parsedCustId) && Customer::where('id', $parsedCustId)->exists()) {
                        $customerId = (int) $parsedCustId;
                    }
                } elseif (is_numeric($searchVal) && Customer::where('id', $searchVal)->exists()) {
                    $customerId = (int) $searchVal;
                }
            }

            $customer = null;
            $vehicle = null;

            // Pre-load vehicle if vehicle_id is provided
            if (! empty($vehicleId) && is_numeric($vehicleId)) {
                $vehicle = Vehicle::with(['customer', 'vehicleType'])->find($vehicleId);
            }

            // Resolve Customer
            if (! empty($customerId)) {
                $customer = Customer::findOrFail($customerId);
            } elseif ($vehicle && $vehicle->customer) {
                // Auto-resolve customer from vehicle owner relation
                $customer = $vehicle->customer;
                $customerId = $customer->id;
            } elseif ($vehicle && $vehicle->customer_id) {
                // Direct lookup fallback for vehicle owner
                $customer = Customer::find($vehicle->customer_id);
                if ($customer) {
                    $customerId = $customer->id;
                }
            }
            
            if (! $customer && ! empty($validated['new_customer_name'])) {
                $mobile = trim((string) ($validated['new_customer_mobile'] ?? ''));
                if ($mobile === '') {
                    throw ValidationException::withMessages([
                        'new_customer_mobile' => 'Customer mobile number is required when adding a new customer.',
                    ]);
                }

                $matchingCustomers = Customer::where('phone', $mobile)->get();
                if ($matchingCustomers->count() === 1) {
                    $customer = $matchingCustomers->first();
                } elseif ($matchingCustomers->count() > 1) {
                    throw ValidationException::withMessages([
                        'new_customer_mobile' => 'Multiple customers found with mobile number '.$mobile.'. Please select from search.',
                    ]);
                } else {
                    $customer = Customer::create([
                        'customer_name' => trim((string) $validated['new_customer_name']),
                        'phone' => $mobile,
                        'email' => ! empty($validated['new_customer_email']) ? trim((string) $validated['new_customer_email']) : null,
                        'customer_type' => 'individual',
                        'status' => 'active',
                    ]);
                }
                $customerId = $customer->id;
            } elseif (! $customer) {
                throw ValidationException::withMessages([
                    'customer_id' => 'Please select an existing customer or vehicle, or add a new customer.',
                ]);
            }

            // 2. Resolve Vehicle & Validate Ownership
            if ($vehicle) {
                $vehicleId = $vehicle->id;
                // Enforce vehicle ownership: Cannot reassign vehicle belonging to another customer
                if ($vehicle->customer_id && $customer && $vehicle->customer_id !== $customer->id) {
                    $otherOwner = $vehicle->customer?->customer_name ?? 'another customer';
                    throw ValidationException::withMessages([
                        'vehicle_id' => "This vehicle is registered to {$otherOwner}. It cannot be assigned to {$customer->customer_name}.",
                    ]);
                }
            } elseif (! empty($validated['new_vehicle_number'])) {
                $rawPlate = trim((string) $validated['new_vehicle_number']);
                $normalized = Vehicle::normalizeNumber($rawPlate);

                if (empty($validated['new_vehicle_type_id'])) {
                    throw ValidationException::withMessages([
                        'new_vehicle_type_id' => 'Vehicle type is required when specifying a vehicle number.',
                    ]);
                }

                $existingVehicle = Vehicle::with(['customer', 'vehicleType'])->where('vehicle_number_normalized', $normalized)->first();
                if ($existingVehicle) {
                    if ($existingVehicle->customer_id === $customer->id) {
                        $vehicle = $existingVehicle;
                        $vehicleId = $existingVehicle->id;
                    } else {
                        $otherOwner = $existingVehicle->customer?->customer_name ?? 'another customer';
                        throw ValidationException::withMessages([
                            'new_vehicle_number' => "This vehicle is already registered to {$otherOwner}. Please select it from search or verify the vehicle number.",
                        ]);
                    }
                } else {
                    $vehicle = Vehicle::create([
                        'customer_id' => $customer->id,
                        'branch_id' => $activeBranchId,
                        'vehicle_type_id' => $validated['new_vehicle_type_id'],
                        'vehicle_number' => $rawPlate,
                        'vehicle_number_normalized' => $normalized,
                        'current_mileage' => 0,
                    ]);
                    $vehicle->load('vehicleType');
                    $vehicleId = $vehicle->id;
                }
            }

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

            $displayCustomerName = $customer->customer_name;
            $displayVehiclePlate = $vehicle ? $vehicle->vehicle_number : null;
            $displayVehicleType = $vehicle ? $vehicle->vehicleType?->name : null;
            $displayMakeModel = $vehicle ? ($vehicle->make_model ?: ($vehicle->brand ? trim($vehicle->brand.' '.$vehicle->model) : null)) : null;

            $customerSnapshot = [
                'customer_name' => $displayCustomerName,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'customer_type' => $customer->customer_type,
                'company_name' => $customer->company_name,
                'tax_number' => $customer->tax_number,
                'address' => $customer->address,
                'is_manual' => false,
                'vehicle_number' => $displayVehiclePlate,
                'vehicle_type' => $displayVehicleType,
                'vehicle_make_model' => $displayMakeModel,
            ];

            $defaultSubject = $displayVehiclePlate
                ? "Direct Invoice for {$displayCustomerName} ({$displayVehiclePlate})"
                : "Direct Invoice for {$displayCustomerName}";

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'branch_id' => $activeBranchId,
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'] ?? null,
                'customer_id' => $customer->id,
                'invoice_type' => 'manual',
                'subject' => $validated['subject'] ?: $defaultSubject,
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
        } catch (ValidationException $ve) {
            DB::rollBack();
            throw $ve;
        } catch (\Throwable $e) {
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
     * 80mm compact thermal receipt view.
     */
    public function thermalReceipt(Invoice $invoice): View
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

        return view('operations.invoices.thermal', compact('invoice', 'company'));
    }

    /**
     * Send invoice PDF via email to customer.
     */
    public function sendEmail(Request $request, Invoice $invoice): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isAdmin() && ! $user->hasPermission('view_invoices')) {
            abort(403, 'Unauthorized to email invoices.');
        }

        $email = $invoice->customer?->email ?? ($invoice->customer_snapshot['email'] ?? null);

        if (empty($email)) {
            return back()->with('error', 'No email address registered for this customer.');
        }

        $invoice->load([
            'customer',
            'jobCard.vehicle',
            'jobCard.vehicleType',
            'branch',
            'items',
            'payments',
        ]);

        $company = CompanySetting::getSettings();

        try {
            Mail::to($email)->send(new InvoiceMail($invoice, $company));

            ActivityLog::log(
                action: 'invoice_emailed',
                description: "Emailed Invoice #{$invoice->invoice_number} to {$email}.",
                module: 'Sales & Invoices',
                userId: $user->id
            );

            return back()->with('success', "Invoice emailed successfully to {$email}.");
        } catch (Exception $e) {
            ActivityLog::log(
                action: 'invoice_email_failed',
                description: "Failed to email Invoice #{$invoice->invoice_number} to {$email}: {$e->getMessage()}",
                module: 'Sales & Invoices',
                userId: $user->id
            );

            return back()->with('error', "Failed to send email: {$e->getMessage()}");
        }
    }

    /**
     * Record a payment against an outstanding invoice.
     */
    public function addPayment(Request $request, Invoice $invoice): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isAdmin() && ! $user->hasPermission('record_payments')) {
            abort(403, 'Unauthorized to record payments.');
        }

        if ($invoice->branch_id && ! $user->canAccessBranch($invoice->branch_id)) {
            abort(403, 'Unauthorized access to this branch.');
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,card,bank_transfer,cheque'],
            'payment_date' => ['nullable', 'date', 'before_or_equal:today'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $payment = DB::transaction(function () use ($invoice, $validated, $user) {
                $freshInvoice = Invoice::where('id', $invoice->id)->lockForUpdate()->firstOrFail();

                if ($freshInvoice->status === 'cancelled') {
                    throw new Exception("Invoice #{$freshInvoice->invoice_number} is cancelled and cannot accept payments.");
                }

                $currentBalance = round((float) $freshInvoice->balance_amount, 2);
                $payAmount = round((float) $validated['amount'], 2);

                if ($currentBalance <= 0.0001) {
                    throw new Exception("Invoice #{$freshInvoice->invoice_number} is already paid in full.");
                }

                if ($payAmount > $currentBalance) {
                    throw new Exception('Payment amount (LKR '.number_format($payAmount, 2).') cannot exceed the outstanding balance of LKR '.number_format($currentBalance, 2).'.');
                }

                $paymentNumber = Payment::generatePaymentNumber();

                $payment = Payment::create([
                    'invoice_id' => $freshInvoice->id,
                    'job_card_id' => $freshInvoice->job_card_id,
                    'customer_id' => $freshInvoice->customer_id,
                    'payment_number' => $paymentNumber,
                    'payment_date' => $validated['payment_date'] ?? now()->toDateString(),
                    'amount' => $payAmount,
                    'payment_method' => $validated['payment_method'],
                    'reference_number' => $validated['reference_number'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'completed',
                    'created_by' => $user->id,
                ]);

                $totalPaid = (float) Payment::where('invoice_id', $freshInvoice->id)
                    ->where('status', 'completed')
                    ->sum('amount');

                $totalPaid = round($totalPaid, 2);
                $grandTotal = round((float) $freshInvoice->grand_total, 2);
                $newBalance = max(0, round($grandTotal - $totalPaid, 2));

                $newStatus = ($newBalance <= 0.0001 || $totalPaid >= $grandTotal)
                    ? 'paid'
                    : ($totalPaid > 0 ? 'partially_paid' : 'issued');

                $freshInvoice->update([
                    'amount_paid' => $totalPaid,
                    'balance_amount' => $newBalance,
                    'status' => $newStatus,
                ]);

                ActivityLog::log(
                    action: 'payment_recorded',
                    description: "Recorded payment {$paymentNumber} of LKR ".number_format($payAmount, 2)." for Invoice #{$freshInvoice->invoice_number}. New balance: LKR ".number_format($newBalance, 2).'.',
                    module: 'Sales & Billing',
                    userId: $user->id
                );

                return $payment;
            });

            return back()->with('success', "Payment {$payment->payment_number} of LKR ".number_format($payment->amount, 2).' recorded successfully.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', "Payment failed: {$e->getMessage()}");
        }
    }
}
