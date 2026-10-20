<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DirectSale;
use App\Models\DirectSaleItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\StockBatch;
use App\Services\Inventory\StockService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,card,bank_transfer,credit'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $result = DB::transaction(function () use ($validated, $request) {
                // 1. Resolve or create customer (optional for direct walk-in)
                $customer = null;
                if (!empty($validated['customer_id'])) {
                    $customer = Customer::find($validated['customer_id']);
                } elseif (!empty($validated['customer_name'])) {
                    $customer = Customer::create([
                        'customer_name' => $validated['customer_name'],
                        'phone' => $validated['phone'] ?? null,
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

                    $availableStock = (float) StockBatch::where('product_id', $product->id)
                        ->where('status', 'active')
                        ->sum('remaining_quantity');

                    if ($availableStock < $qty) {
                        throw new Exception("Insufficient stock for '{$product->product_name}'. Requested: {$qty}, Available: {$availableStock}.");
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

                if ($paymentMethod !== 'credit' && $amountPaid < $grandTotal) {
                    throw new Exception("Amount paid (LKR " . number_format($amountPaid, 2) . ") is less than the Grand Total (LKR " . number_format($grandTotal, 2) . ").");
                }

                $changeAmount = $paymentMethod === 'credit' ? 0.0 : max(0, $amountPaid - $grandTotal);
                $actualReceived = min($amountPaid, $grandTotal);

                // 3. Create Direct Sale
                $saleNumber = DirectSale::generateSaleNumber();
                $directSale = DirectSale::create([
                    'sale_number' => $saleNumber,
                    'customer_id' => $customer->id,
                    'sale_date' => now()->toDateString(),
                    'subtotal' => $subtotal,
                    'discount_amount' => $discount,
                    'tax_amount' => $tax,
                    'grand_total' => $grandTotal,
                    'amount_paid' => $amountPaid,
                    'change_amount' => $changeAmount,
                    'payment_method' => $paymentMethod,
                    'status' => 'completed',
                    'notes' => $validated['notes'] ?? null,
                    'created_by' => Auth::id(),
                ]);

                // 4. Create Invoice
                $invoiceNumber = Invoice::generateInvoiceNumber();
                $invoice = Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'invoice_date' => now()->toDateString(),
                    'due_date' => now()->toDateString(),
                    'customer_id' => $customer->id,
                    'invoice_type' => 'direct_sale',
                    'subject' => "Direct POS Sale ({$saleNumber})",
                    'reference' => $saleNumber,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discount,
                    'tax_amount' => $tax,
                    'grand_total' => $grandTotal,
                    'amount_paid' => $actualReceived,
                    'balance_amount' => max(0, $grandTotal - $actualReceived),
                    'status' => $paymentMethod === 'credit' ? 'issued' : 'paid',
                    'payment_method' => $paymentMethod,
                    'customer_snapshot' => [
                        'customer_name' => $customer->customer_name,
                        'phone' => $customer->phone,
                    ],
                    'created_by' => Auth::id(),
                ]);

                // 5. Deduct FIFO stock and create items
                foreach ($lineItems as $item) {
                    $product = $item['product'];
                    $qty = $item['quantity'];

                    // FIFO Stock Deduction
                    $issueResult = $this->stockService->issueStockFifo(
                        $product,
                        $qty,
                        'direct_sale',
                        $directSale,
                        Auth::user()
                    );

                    $unitCost = $qty > 0 ? round($issueResult['total_cost'] / $qty, 4) : 0.0;

                    // Direct Sale Item
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

                    // Invoice Item
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'item_type' => 'product',
                        'item_id' => $product->id,
                        'item_code' => $product->sku,
                        'item_name' => $product->product_name,
                        'quantity' => $qty,
                        'unit_price' => $item['unit_price'],
                        'unit_cost' => $unitCost,
                        'line_total' => $item['line_total'],
                    ]);
                }

                // 6. Record Payment
                if ($actualReceived > 0) {
                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'customer_id' => $customer->id,
                        'payment_number' => Payment::generatePaymentNumber(),
                        'payment_date' => now()->toDateString(),
                        'amount' => $actualReceived,
                        'payment_method' => $paymentMethod,
                        'reference_number' => $saleNumber,
                        'notes' => "POS Checkout for Sale #{$saleNumber}",
                        'created_by' => Auth::id(),
                    ]);
                }

                // Link invoice to direct sale
                $directSale->update(['invoice_id' => $invoice->id]);

                return [
                    'direct_sale' => $directSale,
                    'invoice' => $invoice,
                ];
            });

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Sale #{$result['direct_sale']->sale_number} completed successfully.",
                    'sale_id' => $result['direct_sale']->id,
                    'invoice_id' => $result['invoice']->id,
                    'redirect_url' => route('operations.invoices.show', $result['invoice']),
                    'receipt_url' => route('operations.invoices.print', $result['invoice']),
                ]);
            }

            return redirect()->route('operations.invoices.show', $result['invoice'])
                ->with('success', "Sale #{$result['direct_sale']->sale_number} completed successfully.");
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
}
