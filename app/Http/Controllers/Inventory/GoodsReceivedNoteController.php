<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreGoodsReceivedNoteRequest;
use App\Models\ActivityLog;
use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\Inventory\StockService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GoodsReceivedNoteController extends Controller
{
    /**
     * Display a listing of Goods Received Notes.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $supplierId = $request->input('supplier_id');
        $status = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = GoodsReceivedNote::with(['supplier', 'postedBy', 'payments']);

        if ($search !== '') {
            $query->search($search);
        }

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        if (in_array($status, ['draft', 'confirmed'])) {
            $query->where('status', $status);
        }

        if ($dateFrom) {
            $query->whereDate('received_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('received_date', '<=', $dateTo);
        }

        $totalCount = GoodsReceivedNote::count();
        $confirmedTotalValue = (float) GoodsReceivedNote::where('status', 'confirmed')->sum('total_amount');
        $grns = $query->latest('received_date')->latest('id')->paginate(10)->withQueryString();

        $suppliers = Supplier::orderBy('name', 'asc')->get();

        return view('inventory.grn.index', compact(
            'grns',
            'search',
            'supplierId',
            'status',
            'dateFrom',
            'dateTo',
            'totalCount',
            'confirmedTotalValue',
            'suppliers'
        ));
    }

    /**
     * Show the form for creating a new GRN.
     */
    public function create(Request $request): View
    {
        $suppliers = Supplier::active()->orderBy('name', 'asc')->get();
        $products = Product::active()
            ->with('uom')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'sku', 'uom_id', 'selling_price', 'preferred_supplier_id']);

        $selectedSupplierId = $request->input('supplier_id');

        return view('inventory.grn.create', compact('suppliers', 'products', 'selectedSupplierId'));
    }

    /**
     * Store a newly created Goods Received Note in storage.
     */
    public function store(StoreGoodsReceivedNoteRequest $request, StockService $stockService): RedirectResponse
    {
        $user = $request->user();
        $action = $request->input('action', 'draft');

        if ($action === 'confirm' && ! $user->isAdmin()) {
            abort(403, 'Unauthorized. Only administrators can confirm and post stock into inventory.');
        }

        try {
            $grn = DB::transaction(function () use ($request, $user) {
                // Generate next GRN Number: GRN-YYYYMM-XXXX
                $prefix = 'GRN-'.date('Ym').'-';
                $lastNumber = GoodsReceivedNote::where('grn_number', 'like', "{$prefix}%")
                    ->lockForUpdate()
                    ->count();

                $grnNumber = sprintf('%s%04d', $prefix, $lastNumber + 1);

                // Handle file attachment
                $attachmentPath = null;
                if ($request->hasFile('invoice_attachment')) {
                    $attachmentPath = $request->file('invoice_attachment')->store('grn-invoices', 'public');
                }

                $grn = GoodsReceivedNote::create([
                    'grn_number' => $grnNumber,
                    'supplier_id' => $request->input('supplier_id'),
                    'supplier_invoice_number' => $request->input('supplier_invoice_number'),
                    'received_date' => $request->input('received_date'),
                    'status' => 'draft',
                    'total_amount' => 0.00,
                    'notes' => $request->input('notes'),
                    'invoice_attachment' => $attachmentPath,
                    'created_by' => $user->id,
                ]);

                $totalAmount = 0.0;

                foreach ($request->input('items', []) as $line) {
                    $purchaseQty = (float) $line['purchase_quantity'];
                    $freeQty = isset($line['free_quantity']) ? (float) $line['free_quantity'] : 0.0;
                    $unitPrice = (float) $line['purchase_unit_price'];
                    $lineTotal = round($purchaseQty * $unitPrice, 2);

                    GoodsReceivedNoteItem::create([
                        'grn_id' => $grn->id,
                        'product_id' => $line['product_id'],
                        'purchase_quantity' => $purchaseQty,
                        'free_quantity' => $freeQty,
                        'purchase_unit_price' => $unitPrice,
                        'selling_price_snapshot' => $line['selling_price_snapshot'] ?? null,
                        'line_total' => $lineTotal,
                    ]);

                    $totalAmount += $lineTotal;
                }

                $grn->update(['total_amount' => $totalAmount]);

                return $grn;
            });

            // If action is confirm, post to stock batches immediately
            if ($action === 'confirm') {
                $stockService->confirmGrn($grn, $user);

                ActivityLog::log(
                    action: 'confirmed',
                    description: "Created and confirmed GRN #{$grn->grn_number} totaling LKR {$grn->total_amount}.",
                    module: 'Purchasing (GRN)'
                );

                return redirect()->route('inventory.grn.show', $grn)
                    ->with('success', "GRN #{$grn->grn_number} confirmed and stock batches created successfully.");
            }

            ActivityLog::log(
                action: 'created',
                description: "Drafted GRN #{$grn->grn_number} totaling LKR {$grn->total_amount}.",
                module: 'Purchasing (GRN)'
            );

            return redirect()->route('inventory.grn.show', $grn)
                ->with('success', "GRN #{$grn->grn_number} saved as draft.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Error creating GRN: '.$e->getMessage());
        }
    }

    /**
     * Display the specified Goods Received Note.
     */
    public function show(GoodsReceivedNote $grn): View
    {
        $grn->load([
            'supplier',
            'items.product.uom',
            'stockBatches',
            'payments.createdBy',
            'payments.reversedBy',
            'postedBy',
            'createdBy',
        ]);

        return view('inventory.grn.show', compact('grn'));
    }

    /**
     * Confirm a draft Goods Received Note and receive inventory.
     */
    public function confirm(Request $request, GoodsReceivedNote $grn, StockService $stockService): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized. Only administrators can confirm GRNs.');
        }

        if ($grn->isConfirmed()) {
            return back()->with('error', 'This GRN has already been confirmed and added to stock.');
        }

        try {
            $stockService->confirmGrn($grn, $request->user());

            ActivityLog::log(
                action: 'confirmed',
                description: "Confirmed GRN #{$grn->grn_number} and generated stock batches.",
                module: 'Purchasing (GRN)'
            );

            return redirect()->route('inventory.grn.show', $grn)
                ->with('success', "GRN #{$grn->grn_number} has been confirmed. Stock batches are now active.");
        } catch (Exception $e) {
            return back()->with('error', 'Could not confirm GRN: '.$e->getMessage());
        }
    }

    /**
     * Print-friendly view of the GRN.
     */
    public function print(GoodsReceivedNote $grn): View
    {
        $grn->load([
            'supplier',
            'items.product.uom',
            'postedBy',
            'createdBy',
        ]);

        return view('inventory.grn.print', compact('grn'));
    }

    /**
     * Cancel or discard a draft GRN.
     */
    public function destroy(Request $request, GoodsReceivedNote $grn): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized. Administrator access is required.');
        }

        if ($grn->isConfirmed()) {
            return back()->with('error', 'Confirmed GRNs cannot be deleted as they represent permanent stock and financial ledger records.');
        }

        $grnNumber = $grn->grn_number;
        $grn->delete();

        ActivityLog::log(
            action: 'deleted',
            description: "Discarded draft GRN #{$grnNumber}.",
            module: 'Purchasing (GRN)'
        );

        return redirect()->route('inventory.grn.index')
            ->with('success', "Draft GRN #{$grnNumber} has been removed.");
    }
}
