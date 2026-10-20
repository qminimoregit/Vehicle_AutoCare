<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use App\Models\StockBatch;
use App\Models\Supplier;
use App\Services\BranchContext;
use App\Services\QuantityFormatter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StockReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $accessibleBranches = BranchContext::getAccessibleBranches($user);

        // Branch scoping
        $branchId = $request->input('branch_id');
        if ($user->isAdmin()) {
            if ($branchId === null) {
                $branchId = BranchContext::getActiveBranchId() ?: 'all';
            }
        } else {
            $branchId = BranchContext::getActiveBranchId();
        }

        $search = trim((string) $request->input('search'));
        $supplierId = $request->input('supplier_id');
        $stockStatus = $request->input('stock_status', 'all');

        $query = Product::with(['uom', 'preferredSupplier']);

        if ($search !== '') {
            $query->search($search);
        }

        if ($supplierId) {
            $query->where('preferred_supplier_id', $supplierId);
        }

        $products = $query->orderBy('name', 'asc')->get();

        // Calculate branch-specific or company-wide stock for each product
        $products->each(function ($product) use ($branchId) {
            $batchQuery = StockBatch::where('product_id', $product->id)
                ->where('remaining_quantity', '>', 0);

            if ($branchId && $branchId !== 'all') {
                $batchQuery->where('branch_id', $branchId);
            }

            $product->computed_available_stock = (float) $batchQuery->sum('remaining_quantity');
            $product->computed_inventory_value = (float) $batchQuery->selectRaw('SUM(remaining_quantity * inventory_unit_cost) as val')->value('val') ?? 0.0;
        });

        // Filter by stock status
        if ($stockStatus === 'low_stock') {
            $products = $products->filter(fn ($p) => $p->min_stock > 0 && $p->computed_available_stock <= (float) $p->min_stock && $p->computed_available_stock > 0);
        } elseif ($stockStatus === 'out_of_stock') {
            $products = $products->filter(fn ($p) => $p->computed_available_stock <= 0);
        } elseif ($stockStatus === 'in_stock') {
            $products = $products->filter(fn ($p) => $p->computed_available_stock > 0);
        }

        $totalProducts = $products->count();
        $totalStockUnits = $products->sum('computed_available_stock');
        $lowStockCount = $products->filter(fn ($p) => $p->min_stock > 0 && $p->computed_available_stock <= (float) $p->min_stock && $p->computed_available_stock > 0)->count();
        $outOfStockCount = $products->filter(fn ($p) => $p->computed_available_stock <= 0)->count();

        // Analyze Units of Measurement (UOM) to avoid presenting a single-unit total for mixed units
        $uomGroups = $products->filter(fn ($p) => $p->computed_available_stock > 0)
            ->groupBy(fn ($p) => $p->uom?->name ?? 'Units');

        $hasMultipleUoms = $uomGroups->count() > 1;
        $singleUomName = $uomGroups->count() === 1 ? $uomGroups->keys()->first() : null;

        $uomSummaryList = [];
        foreach ($uomGroups as $uomName => $prods) {
            $sumQty = $prods->sum('computed_available_stock');
            $uomSummaryList[] = QuantityFormatter::format($sumQty).' '.$uomName;
        }
        $uomSummaryText = implode(' • ', $uomSummaryList);

        // Paginate manually or pass collection for responsive table
        $suppliers = Supplier::orderBy('name', 'asc')->get();
        $selectedBranch = ($branchId && $branchId !== 'all') ? Branch::find($branchId) : null;

        return view('reports.stock.index', compact(
            'products',
            'suppliers',
            'search',
            'supplierId',
            'stockStatus',
            'branchId',
            'accessibleBranches',
            'selectedBranch',
            'totalProducts',
            'totalStockUnits',
            'lowStockCount',
            'outOfStockCount',
            'hasMultipleUoms',
            'singleUomName',
            'uomSummaryText'
        ));
    }

    /**
     * Return active stock batches for a product (Batch Details Modal).
     */
    public function batches(Request $request, Product $product): JsonResponse
    {
        $branchId = $request->input('branch_id');
        $user = $request->user();

        $query = StockBatch::with(['grn', 'supplier', 'branch'])
            ->where('product_id', $product->id)
            ->where('remaining_quantity', '>', 0);

        if (! $user->isAdmin()) {
            $branchId = BranchContext::getActiveBranchId();
        }

        if ($branchId && $branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        $batches = $query->orderBy('purchase_date', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($b) use ($user) {
                return [
                    'id' => $b->id,
                    'batch_number' => $b->batch_number,
                    'grn_number' => $b->grn ? $b->grn->grn_number : '-',
                    'purchase_date' => $b->purchase_date ? $b->purchase_date->format('Y-m-d') : '-',
                    'supplier_name' => $b->supplier ? $b->supplier->name : 'N/A',
                    'original_quantity' => QuantityFormatter::format($b->received_quantity),
                    'remaining_quantity' => QuantityFormatter::format($b->remaining_quantity),
                    'purchase_cost' => $user->isAdmin() ? number_format($b->inventory_unit_cost, 2) : '***',
                    'selling_price' => $b->selling_price_at_receipt ? number_format($b->selling_price_at_receipt, 2) : '-',
                    'branch_name' => $b->branch ? $b->branch->branch_name : 'Main Branch',
                    'status' => $b->status,
                ];
            });

        return response()->json([
            'product_name' => $product->name,
            'sku' => $product->sku,
            'uom' => $product->uom ? $product->uom->name : '',
            'selling_price' => number_format($product->selling_price, 2),
            'batches' => $batches,
        ]);
    }

    /**
     * Print View for Stock Report.
     */
    public function print(Request $request): View
    {
        $user = $request->user();
        $branchId = $request->input('branch_id');

        if (! $user->isAdmin()) {
            $branchId = BranchContext::getActiveBranchId();
        }

        $search = trim((string) $request->input('search'));
        $supplierId = $request->input('supplier_id');
        $stockStatus = $request->input('stock_status', 'all');

        $query = Product::with(['uom', 'preferredSupplier']);
        if ($search !== '') {
            $query->search($search);
        }
        if ($supplierId) {
            $query->where('preferred_supplier_id', $supplierId);
        }

        $products = $query->orderBy('name', 'asc')->get();

        $products->each(function ($product) use ($branchId) {
            $batchQuery = StockBatch::where('product_id', $product->id)
                ->where('remaining_quantity', '>', 0);
            if ($branchId && $branchId !== 'all') {
                $batchQuery->where('branch_id', $branchId);
            }
            $product->computed_available_stock = (float) $batchQuery->sum('remaining_quantity');
        });

        if ($stockStatus === 'low_stock') {
            $products = $products->filter(fn ($p) => $p->min_stock > 0 && $p->computed_available_stock <= (float) $p->min_stock && $p->computed_available_stock > 0);
        } elseif ($stockStatus === 'out_of_stock') {
            $products = $products->filter(fn ($p) => $p->computed_available_stock <= 0);
        } elseif ($stockStatus === 'in_stock') {
            $products = $products->filter(fn ($p) => $p->computed_available_stock > 0);
        }

        $uomGroups = $products->filter(fn ($p) => $p->computed_available_stock > 0)
            ->groupBy(fn ($p) => $p->uom?->name ?? 'Units');
        $hasMultipleUoms = $uomGroups->count() > 1;
        $singleUomName = $uomGroups->count() === 1 ? $uomGroups->keys()->first() : null;

        $uomSummaryList = [];
        foreach ($uomGroups as $uomName => $prods) {
            $sumQty = $prods->sum('computed_available_stock');
            $uomSummaryList[] = QuantityFormatter::format($sumQty).' '.$uomName;
        }
        $uomSummaryText = implode(' • ', $uomSummaryList);

        $selectedBranch = ($branchId && $branchId !== 'all') ? Branch::find($branchId) : null;

        return view('reports.stock.print', compact(
            'products',
            'selectedBranch',
            'stockStatus',
            'hasMultipleUoms',
            'singleUomName',
            'uomSummaryText'
        ));
    }

    /**
     * Export Stock Report to CSV (Excel compatible).
     */
    public function export(Request $request): StreamedResponse
    {
        $user = $request->user();
        $branchId = $request->input('branch_id');
        if (! $user->isAdmin()) {
            $branchId = BranchContext::getActiveBranchId();
        }

        $search = trim((string) $request->input('search'));
        $supplierId = $request->input('supplier_id');
        $stockStatus = $request->input('stock_status', 'all');

        $query = Product::with(['uom', 'preferredSupplier']);
        if ($search !== '') {
            $query->search($search);
        }
        if ($supplierId) {
            $query->where('preferred_supplier_id', $supplierId);
        }

        $products = $query->orderBy('name', 'asc')->get();

        $products->each(function ($product) use ($branchId) {
            $batchQuery = StockBatch::where('product_id', $product->id)
                ->where('remaining_quantity', '>', 0);
            if ($branchId && $branchId !== 'all') {
                $batchQuery->where('branch_id', $branchId);
            }
            $product->computed_available_stock = (float) $batchQuery->sum('remaining_quantity');
        });

        if ($stockStatus === 'low_stock') {
            $products = $products->filter(fn ($p) => $p->min_stock > 0 && $p->computed_available_stock <= (float) $p->min_stock && $p->computed_available_stock > 0);
        } elseif ($stockStatus === 'out_of_stock') {
            $products = $products->filter(fn ($p) => $p->computed_available_stock <= 0);
        } elseif ($stockStatus === 'in_stock') {
            $products = $products->filter(fn ($p) => $p->computed_available_stock > 0);
        }

        $fileName = 'stock_report_'.now()->format('Y_m_d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        return response()->streamDownload(function () use ($products) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Product Name',
                'SKU',
                'Supplier',
                'Unit of Measurement',
                'Available Stock',
                'Min Stock Alert',
                'Selling Price (LKR)',
                'Status',
            ]);

            foreach ($products as $p) {
                $status = $p->computed_available_stock <= 0 ? 'Out of Stock' : ($p->min_stock > 0 && $p->computed_available_stock <= (float) $p->min_stock ? 'Low Stock' : 'In Stock');

                fputcsv($file, [
                    $p->name,
                    $p->sku ?? '-',
                    $p->preferredSupplier ? $p->preferredSupplier->name : 'N/A',
                    $p->uom ? $p->uom->name : '-',
                    QuantityFormatter::format($p->computed_available_stock, null, false),
                    QuantityFormatter::format($p->min_stock, null, false),
                    number_format($p->selling_price, 2, '.', ''),
                    $status,
                ]);
            }

            fclose($file);
        }, $fileName, $headers);
    }
}
