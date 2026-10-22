<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Mail\SalesBillMail;
use App\Models\ActivityLog;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\DirectSale;
use App\Models\DirectSaleItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\StockBatch;
use App\Services\BranchContext;
use App\Services\Inventory\StockService;
use App\Services\PhoneNumberHelper;
use App\Services\QuantityFormatter;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class DirectSaleController extends Controller
{
    public function __construct(
        protected StockService $stockService
    ) {}

    /**
     * Display POS direct sale terminal.
     */
    public function index(Request $request): View
    {
        // Get all active products with aggregated available stock across all active batches
        $products = Product::active()
            ->with(['uom'])
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                $product->available_stock = (float) StockBatch::where('product_id', $product->id)
                    ->where('status', 'active')
                    ->sum('remaining_quantity');
                $product->formatted_stock = QuantityFormatter::format($product->available_stock);
                $product->allows_fractional = $product->allowsFractionalQuantity();
                $product->quantity_step = $product->quantity_step;

                return $product;
            });

        $customers = Customer::where('status', 'active')->orderBy('customer_name')->get();

        return view('operations.direct-sales.index', compact('products', 'customers'));
    }

    /**
     * JSON search endpoint for quick POS product lookup.
     */
    public function searchProducts(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('query', ''));

        $query = Product::active()->with(['uom']);

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('sku', 'like', "%{$term}%")
                    ->orWhere('barcode', 'like', "%{$term}%");
            });
        }

        $products = $query->limit(30)->get()->map(function ($product) {
            $stock = (float) StockBatch::where('product_id', $product->id)
                ->where('status', 'active')
                ->sum('remaining_quantity');

            return [
                'id' => $product->id,
                'name' => $product->name,
                'product_name' => $product->name,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'unit' => $product->uom?->abbreviation ?? 'Unit',
                'selling_price' => (float) $product->selling_price,
                'available_stock' => $stock,
                'formatted_stock' => QuantityFormatter::format($stock),
                'allows_fractional' => $product->allowsFractionalQuantity(),
                'quantity_step' => $product->quantity_step,
                'image_url' => $product->image_url,
            ];
        });

        return response()->json($products);
    }

    /**
     * Atomic POS checkout: validates stock, issues FIFO stock, creates invoice, records payment, and creates direct sale.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'customer_name' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,card,bank_transfer,cheque,credit'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $result = DB::transaction(function () use ($validated, $request) {
                // 1. Resolve or create customer (optional for direct walk-in)
                $customer = null;
                if (! empty($validated['customer_id'])) {
                    $customer = Customer::find($validated['customer_id']);
                    if ($customer) {
                        $dirty = false;
                        if (! empty($validated['email']) && empty($customer->email)) {
                            $customer->email = $validated['email'];
                            $dirty = true;
                        }
                        if (! empty($validated['phone']) && empty($customer->phone)) {
                            $customer->phone = $validated['phone'];
                            $dirty = true;
                        }
                        if ($dirty) {
                            $customer->save();
                        }
                    }
                } elseif (! empty($validated['customer_name']) || ! empty($validated['phone']) || ! empty($validated['email'])) {
                    $customerName = ! empty($validated['customer_name']) ? trim($validated['customer_name']) : 'Walk-in Customer';
                    $customer = Customer::create([
                        'customer_name' => $customerName,
                        'phone' => $validated['phone'] ?? null,
                        'email' => $validated['email'] ?? null,
                        'customer_type' => 'individual',
                        'status' => 'active',
                    ]);
                } else {
                    // Default Walk-in Customer
                    $customer = Customer::firstOrCreate(
                        ['phone' => '0000000000'],
                        [
                            'customer_name' => 'Walk-in Customer',
                            'customer_type' => 'individual',
                            'status' => 'active',
                        ]
                    );
                }

                // 2. Validate availability and calculate totals
                $subtotal = 0.0;
                $lineItems = [];

                foreach ($validated['items'] as $itemData) {
                    $product = Product::findOrFail($itemData['product_id']);
                    $qty = (float) $itemData['quantity'];
                    $unitPrice = (float) $itemData['unit_price'];

                    if (! $product->allowsFractionalQuantity() && floor($qty) != $qty) {
                        throw new Exception("Product '{$product->product_name}' ({$product->uom?->name}) requires a whole-number quantity.");
                    }

                    $availableStock = (float) StockBatch::where('product_id', $product->id)
                        ->where('status', 'active')
                        ->sum('remaining_quantity');

                    if ($availableStock < $qty) {
                        $fmtReq = QuantityFormatter::format($qty);
                        $fmtAvail = QuantityFormatter::format($availableStock);
                        throw new Exception("Insufficient stock for '{$product->product_name}'. Requested: {$fmtReq}, Available: {$fmtAvail}.");
                    }

                    $lineTotal = round($qty * $unitPrice, 2);
                    $subtotal += $lineTotal;

                    $lineItems[] = [
                        'product' => $product,
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'line_total' => $lineTotal,
                    ];
                }

                $discount = (float) ($validated['discount_amount'] ?? 0);
                $tax = (float) ($validated['tax_amount'] ?? 0);
                $grandTotal = max(0, $subtotal - $discount + $tax);
                $amountPaid = (float) $validated['amount_paid'];
                $paymentMethod = $validated['payment_method'];

                if ($paymentMethod === 'credit') {
                    $amountPaid = 0.0;
                }

                $changeAmount = ($paymentMethod === 'credit' || $amountPaid <= $grandTotal)
                    ? 0.0
                    : max(0, round($amountPaid - $grandTotal, 2));

                $actualReceived = min($amountPaid, $grandTotal);
                $unpaidAmount = max(0, round($grandTotal - $actualReceived, 2));

                // Credit Limit Validation (if balance exists)
                if ($unpaidAmount > 0 && $customer) {
                    $creditAccount = $customer->creditAccount()->where('status', 'active')->first();
                    if ($creditAccount && $creditAccount->wouldExceedLimit($unpaidAmount)) {
                        if (! Auth::user()->isAdmin() && ! $request->boolean('credit_override')) {
                            throw new Exception('Credit limit exceeded. Available credit: LKR '.number_format($creditAccount->available_credit, 2).', requested credit: LKR '.number_format($unpaidAmount, 2).'. Admin override required.');
                        }
                    }
                }

                // 3. Create Direct Sale with dedicated Sales Bill Number
                $activeBranchId = BranchContext::getActiveBranchId();
                $saleNumber = DirectSale::generateSaleNumber();
                $billNumber = DirectSale::generateBillNumber($saleNumber);

                $directSale = DirectSale::create([
                    'sale_number' => $saleNumber,
                    'bill_number' => $billNumber,
                    'branch_id' => $activeBranchId,
                    'customer_id' => $customer->id,
                    'sale_date' => now()->toDateString(),
                    'subtotal' => $subtotal,
                    'discount_amount' => $discount,
                    'tax_amount' => $tax,
                    'grand_total' => $grandTotal,
                    'amount_paid' => $actualReceived,
                    'change_amount' => $changeAmount,
                    'payment_method' => $paymentMethod,
                    'status' => 'completed',
                    'notes' => $validated['notes'] ?? null,
                    'created_by' => Auth::id(),
                ]);

                // 4. Deduct FIFO stock and create items
                foreach ($lineItems as $item) {
                    $product = $item['product'];
                    $qty = $item['quantity'];

                    $issueResult = $this->stockService->issueStockFifo(
                        $product,
                        $qty,
                        'direct_sale',
                        $directSale,
                        Auth::user()
                    );

                    $unitCost = $qty > 0 ? round($issueResult['total_cost'] / $qty, 4) : 0.0;

                    DirectSaleItem::create([
                        'direct_sale_id' => $directSale->id,
                        'product_id' => $product->id,
                        'product_sku' => $product->sku,
                        'product_name' => $product->product_name,
                        'quantity' => $qty,
                        'unit_price' => $item['unit_price'],
                        'unit_cost' => $unitCost,
                        'line_total' => $item['line_total'],
                        'batch_allocations' => $issueResult['batches'],
                    ]);
                }

                // 5. Record Payment (Linked to Direct Sale Bill)
                if ($actualReceived > 0) {
                    Payment::create([
                        'direct_sale_id' => $directSale->id,
                        'invoice_id' => null,
                        'customer_id' => $customer->id,
                        'payment_number' => Payment::generatePaymentNumber(),
                        'payment_date' => now()->toDateString(),
                        'amount' => $actualReceived,
                        'payment_method' => $paymentMethod,
                        'reference_number' => $billNumber,
                        'notes' => "POS Checkout for Bill #{$billNumber}",
                        'status' => 'completed',
                        'created_by' => Auth::id(),
                    ]);
                }

                ActivityLog::log(
                    'direct_sale_created',
                    "Completed Direct Sale #{$saleNumber} (Sales Bill #{$billNumber}) for LKR ".number_format($grandTotal, 2),
                    'Sales & Billing',
                    Auth::id()
                );

                return [
                    'direct_sale' => $directSale,
                ];
            });

            $directSale = $result['direct_sale'];
            $directSale->load(['customer', 'branch', 'payments']);
            $company = CompanySetting::getSettings();

            $billUrl = route('operations.direct-sales.bill', $directSale);
            $pdfUrl = route('operations.direct-sales.pdf', $directSale);
            $emailUrl = route('operations.direct-sales.email', $directSale);
            $updateContactUrl = route('operations.direct-sales.update-contact', $directSale);

            $totalPaid = $directSale->total_paid;
            $balance = $directSale->balance;
            $paymentStatus = $directSale->payment_status;
            $waMessage = $directSale->getWhatsAppMessage($company);
            $normPhone = PhoneNumberHelper::normalizeForWhatsApp($directSale->customer?->phone);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Sale completed successfully. Bill #{$directSale->bill_number} generated.",
                    'sale_id' => $directSale->id,
                    'sale_number' => $directSale->sale_number,
                    'bill_number' => $directSale->bill_number,
                    'grand_total' => (float) $directSale->grand_total,
                    'amount_paid' => (float) $totalPaid,
                    'balance' => (float) $balance,
                    'change_amount' => (float) $directSale->change_amount,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $directSale->payment_method,
                    'customer_name' => $directSale->customer?->customer_name ?? '',
                    'customer_phone' => ($directSale->customer?->phone && $directSale->customer->phone !== '0000000000') ? $directSale->customer->phone : '',
                    'customer_email' => $directSale->customer?->email ?? '',
                    'norm_phone' => $normPhone ?? '',
                    'bill_url' => $billUrl,
                    'thermal_url' => $billUrl,
                    'receipt_url' => $billUrl,
                    'pdf_url' => $pdfUrl,
                    'email_url' => $emailUrl,
                    'update_contact_url' => $updateContactUrl,
                    'whatsapp_message' => $waMessage,
                ]);
            }

            return redirect()->route('operations.direct-sales.bill', $directSale)
                ->with('success', "Sale #{$directSale->sale_number} completed. Bill #{$directSale->bill_number} generated.");
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Render the 80mm thermal Sales Bill for a Direct Sale.
     */
    public function printBill(DirectSale $directSale): View
    {
        $directSale->load([
            'customer',
            'branch',
            'items.product.uom',
            'creator',
            'payments.creator',
            'invoice',
        ]);

        $company = CompanySetting::getSettings();

        return view('operations.direct-sales.bill', compact('directSale', 'company'));
    }

    /**
     * Generate an official tax/commercial Invoice for a Direct Sale upon explicit request.
     */
    public function generateInvoice(DirectSale $directSale): RedirectResponse
    {
        $user = Auth::user();
        if (! $user || (! $user->isAdmin() && ! $user->hasPermission('create_invoices'))) {
            abort(403, 'Unauthorized. You do not have permission to generate official invoices.');
        }

        if ($directSale->status === 'cancelled') {
            return back()->with('error', 'Cannot generate invoice for a cancelled Direct Sale.');
        }

        if ($directSale->invoice_id) {
            $existingInvoice = Invoice::find($directSale->invoice_id);
            if ($existingInvoice) {
                return redirect()->route('operations.invoices.show', $existingInvoice)
                    ->with('info', "Official Invoice #{$existingInvoice->invoice_number} already exists for this sale.");
            }
        }

        try {
            $invoice = DB::transaction(function () use ($directSale, $user) {
                $freshSale = DirectSale::where('id', $directSale->id)->lockForUpdate()->firstOrFail();

                if ($freshSale->invoice_id) {
                    return Invoice::findOrFail($freshSale->invoice_id);
                }

                $invoiceNumber = Invoice::generateInvoiceNumber();
                $totalPaid = (float) $freshSale->payments()->where('status', 'completed')->sum('amount');
                $grandTotal = (float) $freshSale->grand_total;
                $balance = max(0, round($grandTotal - $totalPaid, 2));

                $status = ($balance <= 0.0001 || $totalPaid >= $grandTotal)
                    ? 'paid'
                    : ($totalPaid > 0 ? 'partially_paid' : 'issued');

                $invoice = Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'branch_id' => $freshSale->branch_id,
                    'invoice_date' => now()->toDateString(),
                    'due_date' => now()->toDateString(),
                    'customer_id' => $freshSale->customer_id,
                    'invoice_type' => 'direct_sale',
                    'subject' => "Direct POS Sale ({$freshSale->sale_number}) - Bill #{$freshSale->bill_number}",
                    'reference' => $freshSale->bill_number,
                    'subtotal' => $freshSale->subtotal,
                    'discount_amount' => $freshSale->discount_amount,
                    'tax_amount' => $freshSale->tax_amount,
                    'grand_total' => $grandTotal,
                    'amount_paid' => $totalPaid,
                    'balance_amount' => $balance,
                    'status' => $status,
                    'payment_method' => $freshSale->payment_method,
                    'customer_snapshot' => [
                        'customer_name' => $freshSale->customer?->customer_name,
                        'phone' => $freshSale->customer?->phone,
                    ],
                    'created_by' => $user->id,
                ]);

                // Copy items to InvoiceItems without deducting stock again
                foreach ($freshSale->items as $item) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'item_type' => 'product',
                        'item_id' => $item->product_id,
                        'item_code' => $item->product_sku,
                        'item_name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'unit_cost' => $item->unit_cost,
                        'line_total' => $item->line_total,
                    ]);
                }

                // Link existing payments to invoice as well
                $freshSale->payments()->update(['invoice_id' => $invoice->id]);

                // Link invoice to direct sale
                $freshSale->update(['invoice_id' => $invoice->id]);

                ActivityLog::log(
                    'invoice_generated',
                    "Generated official Invoice #{$invoiceNumber} from Sales Bill #{$freshSale->bill_number}",
                    'Sales & Billing',
                    $user->id
                );

                return $invoice;
            });

            return redirect()->route('operations.invoices.show', $invoice)
                ->with('success', "Official Invoice #{$invoice->invoice_number} generated successfully for Sales Bill #{$directSale->bill_number}.");
        } catch (Exception $e) {
            return back()->with('error', "Failed to generate invoice: {$e->getMessage()}");
        }
    }

    /**
     * Download or stream the Sales Bill PDF.
     */
    public function downloadPdf(DirectSale $directSale)
    {
        $user = Auth::user();
        if (! $user || (! $user->isAdmin() && ! $user->hasPermission('process_sales') && ! $user->hasPermission('view_invoices'))) {
            abort(403, 'Unauthorized to view bill PDF.');
        }

        if ($directSale->branch_id && ! $user->canAccessBranch($directSale->branch_id)) {
            abort(403, 'Unauthorized to access this branch sale.');
        }

        $directSale->load([
            'customer',
            'branch',
            'items.product.uom',
            'creator',
            'payments.creator',
        ]);

        $company = CompanySetting::getSettings();

        $pdf = Pdf::loadView('operations.direct-sales.pdf', compact('directSale', 'company'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("SalesBill_{$directSale->bill_number}.pdf");
    }

    /**
     * Send Sales Bill via email to the customer with PDF attachment.
     */
    public function sendEmail(Request $request, DirectSale $directSale)
    {
        $user = Auth::user();
        if (! $user || (! $user->isAdmin() && ! $user->hasPermission('process_sales') && ! $user->hasPermission('view_invoices'))) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized to send bill emails.'], 403);
            }
            abort(403, 'Unauthorized to send bill emails.');
        }

        if ($directSale->branch_id && ! $user->canAccessBranch($directSale->branch_id)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized to access this branch sale.'], 403);
            }
            abort(403, 'Unauthorized to access this branch sale.');
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:150'],
        ]);

        $email = trim($validated['email']);

        // Update customer email if missing without altering financial transaction
        if ($directSale->customer && empty($directSale->customer->email)) {
            $directSale->customer->email = $email;
            $directSale->customer->save();
        }

        $directSale->load([
            'customer',
            'branch',
            'items.product.uom',
            'creator',
            'payments.creator',
        ]);

        $company = CompanySetting::getSettings();

        try {
            Mail::to($email)->send(new SalesBillMail($directSale, $company));

            ActivityLog::log(
                action: 'direct_sale_emailed',
                description: "Emailed Sales Bill #{$directSale->bill_number} to {$email}.",
                module: 'Sales & POS',
                userId: $user->id
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Sales Bill emailed successfully to {$email}.",
                    'email' => $email,
                ]);
            }

            return back()->with('success', "Sales Bill emailed successfully to {$email}.");
        } catch (\Throwable $e) {
            ActivityLog::log(
                action: 'direct_sale_email_failed',
                description: "Failed to email Sales Bill #{$directSale->bill_number} to {$email}: {$e->getMessage()}",
                module: 'Sales & POS',
                userId: $user->id
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Failed to send email: {$e->getMessage()}",
                ], 422);
            }

            return back()->with('error', "Failed to send email: {$e->getMessage()}");
        }
    }

    /**
     * Update customer contact info without modifying the financial transaction.
     */
    public function updateContact(Request $request, DirectSale $directSale): JsonResponse
    {
        $user = Auth::user();
        if (! $user || (! $user->isAdmin() && ! $user->hasPermission('process_sales') && ! $user->hasPermission('view_invoices'))) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        if ($directSale->customer) {
            if (isset($validated['email'])) {
                $directSale->customer->email = $validated['email'];
            }
            if (isset($validated['phone'])) {
                $directSale->customer->phone = $validated['phone'];
            }
            $directSale->customer->save();
        }

        $normPhone = PhoneNumberHelper::normalizeForWhatsApp($directSale->customer?->phone);

        return response()->json([
            'success' => true,
            'message' => 'Contact details updated.',
            'customer_email' => $directSale->customer?->email,
            'customer_phone' => $directSale->customer?->phone,
            'norm_phone' => $normPhone,
        ]);
    }
}
