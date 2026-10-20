<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use App\Models\StockBatch;
use App\Models\Supplier;
use App\Services\BranchContext;
use App\Services\QuantityFormatter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StockValueReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->isAdmin(), 403, 'Unauthorized access to financial valuation reports.');

        $accessibleBranches = BranchContext::getAccessibleBranches($user);
        $branchId = $request->input('branch_id', 'all');
        $supplierId = $request->input('supplier_id');
        $asOfDate = $request->input('as_of_date');
        $viewType = $request->input('view_type', 'product'); // 'product' or 'batch'

        // Prepare query for active batches
        $query = StockBatch::with(['product.uom', 'supplier', 'grn', 'branch'])
            ->where('remaining_quantity', '>', 0);

        if ($branchId && $branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        if ($asOfDate) {
            $parsedDate = Carbon::parse($asOfDate)->endOfDay();
            $query->where('purchase_date', '<=', $parsedDate);
        }

        $batches = $query->orderBy('product_id')->orderBy('purchase_date')->get();

        // Group by product for product-wise valuation
        $productWise = $batches->groupBy('product_id')->map(function ($productBatches) {
            $first = $productBatches->first();
            $product = $first->product;

            $totalQty = (float) $productBatches->sum('remaining_quantity');
            $totalValuation = (float) $productBatches->sum(function ($b) {
                return (float) $b->remaining_quantity * (float) $b->inventory_unit_cost;
            });
            $weightedAvgCost = $totalQty > 0 ? round($totalValuation / $totalQty, 4) : 0.0;
            $sellingPrice = (float) $product->selling_price;
            $potentialRevenue = round($totalQty * $sellingPrice, 2);
            $unrealizedMargin = round(max(0, $potentialRevenue - $totalValuation), 2);

            return [
                'product' => $product,
                'supplier_name' => $product->preferredSupplier?->name ?? 'N/A',
                'batches_count' => $productBatches->count(),
                'total_quantity' => $totalQty,
                'weighted_avg_cost' => $weightedAvgCost,
                'selling_price' => $sellingPrice,
                'total_valuation' => round($totalValuation, 2),
                'potential_revenue' => $potentialRevenue,
                'unrealized_margin' => $unrealizedMargin,
                'batches' => $productBatches,
            ];
        })->values();

        // Overall summary totals
        $grandTotalStockValue = (float) $productWise->sum('total_valuation');
        $grandTotalPotentialRevenue = (float) $productWise->sum('potential_revenue');
        $grandTotalUnits = (float) $productWise->sum('total_quantity');
        $grandTotalBatches = $batches->count();

        // Analyze Units of Measurement (UOM) for inventory valuation summary
        $uomGroups = $batches->groupBy(fn ($b) => $b->product?->uom?->name ?? 'Units');
        $hasMultipleUoms = $uomGroups->count() > 1;
        $singleUomName = $uomGroups->count() === 1 ? $uomGroups->keys()->first() : null;

        $uomSummaryList = [];
        foreach ($uomGroups as $uomName => $grpBatches) {
            $sumQty = $grpBatches->sum('remaining_quantity');
            $uomSummaryList[] = QuantityFormatter::format($sumQty).' '.$uomName;
        }
        $uomSummaryText = implode(' • ', $uomSummaryList);

        $suppliers = Supplier::orderBy('name', 'asc')->get();
        $selectedBranch = ($branchId && $branchId !== 'all') ? Branch::find($branchId) : null;

        return view('reports.stock-value.index', compact(
            'productWise',
            'batches',
            'grandTotalStockValue',
            'grandTotalPotentialRevenue',
            'grandTotalUnits',
            'grandTotalBatches',
            'suppliers',
            'supplierId',
            'branchId',
            'asOfDate',
            'viewType',
            'accessibleBranches',
            'selectedBranch',
            'hasMultipleUoms',
            'singleUomName',
            'uomSummaryText'
        ));
    }

    public function print(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->isAdmin(), 403, 'Unauthorized access.');

        $branchId = $request->input('branch_id', 'all');
        $supplierId = $request->input('supplier_id');
        $asOfDate = $request->input('as_of_date');
        $viewType = $request->input('view_type', 'product');

        $query = StockBatch::with(['product.uom', 'supplier', 'grn', 'branch'])
            ->where('remaining_quantity', '>', 0);

        if ($branchId && $branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }
        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }
        if ($asOfDate) {
            $query->where('purchase_date', '<=', Carbon::parse($asOfDate)->endOfDay());
        }

        $batches = $query->orderBy('product_id')->orderBy('purchase_date')->get();

        $productWise = $batches->groupBy('product_id')->map(function ($productBatches) {
            $first = $productBatches->first();
            $product = $first->product;
            $totalQty = (float) $productBatches->sum('remaining_quantity');
            $totalValuation = (float) $productBatches->sum(function ($b) {
                return (float) $b->remaining_quantity * (float) $b->inventory_unit_cost;
            });

            return [
                'product' => $product,
                'supplier_name' => $product->preferredSupplier?->name ?? 'N/A',
                'batches_count' => $productBatches->count(),
                'total_quantity' => $totalQty,
                'weighted_avg_cost' => $totalQty > 0 ? round($totalValuation / $totalQty, 4) : 0.0,
                'selling_price' => (float) $product->selling_price,
                'total_valuation' => round($totalValuation, 2),
                'batches' => $productBatches,
            ];
        })->values();

        $grandTotalStockValue = (float) $productWise->sum('total_valuation');
        $grandTotalUnits = (float) $productWise->sum('total_quantity');

        $uomGroups = $batches->groupBy(fn ($b) => $b->product?->uom?->name ?? 'Units');
        $hasMultipleUoms = $uomGroups->count() > 1;
        $singleUomName = $uomGroups->count() === 1 ? $uomGroups->keys()->first() : null;

        $uomSummaryList = [];
        foreach ($uomGroups as $uomName => $grpBatches) {
            $sumQty = $grpBatches->sum('remaining_quantity');
            $uomSummaryList[] = QuantityFormatter::format($sumQty).' '.$uomName;
        }
        $uomSummaryText = implode(' • ', $uomSummaryList);

        $selectedBranch = ($branchId && $branchId !== 'all') ? Branch::find($branchId) : null;

        return view('reports.stock-value.print', compact(
            'productWise',
            'batches',
            'grandTotalStockValue',
            'grandTotalUnits',
            'asOfDate',
            'viewType',
            'selectedBranch',
            'hasMultipleUoms',
            'singleUomName',
            'uomSummaryText'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $user = $request->user();
        abort_unless($user->isAdmin(), 403, 'Unauthorized access.');

        $branchId = $request->input('branch_id', 'all');
        $supplierId = $request->input('supplier_id');
        $asOfDate = $request->input('as_of_date');
        $viewType = $request->input('view_type', 'product');

        $query = StockBatch::with(['product.uom', 'supplier', 'grn', 'branch'])
            ->where('remaining_quantity', '>', 0);

        if ($branchId && $branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }
        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }
        if ($asOfDate) {
            $query->where('purchase_date', '<=', Carbon::parse($asOfDate)->endOfDay());
        }

        $batches = $query->orderBy('product_id')->orderBy('purchase_date')->get();
        $fileName = 'stock_value_report_'.now()->format('Y_m_d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        return response()->streamDownload(function () use ($batches, $viewType) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($viewType === 'batch') {
                fputcsv($file, [
                    'Product Name',
                    'SKU',
                    'Batch Number',
                    'GRN Number',
                    'Supplier',
                    'Purchase Date',
                    'Remaining Quantity',
                    'Actual Batch Unit Cost (LKR)',
                    'Total Batch Value (LKR)',
                    'Selling Price (LKR)',
                    'Branch',
                ]);

                foreach ($batches as $b) {
                    $batchVal = round((float) $b->remaining_quantity * (float) $b->inventory_unit_cost, 2);
                    fputcsv($file, [
                        $b->product?->name ?? 'N/A',
                        $b->product?->sku ?? '-',
                        $b->batch_number,
                        $b->grn?->grn_number ?? '-',
                        $b->supplier?->name ?? 'N/A',
                        $b->purchase_date?->format('Y-m-d') ?? '-',
                        QuantityFormatter::format($b->remaining_quantity, null, false),
                        number_format($b->inventory_unit_cost, 2, '.', ''),
                        number_format($batchVal, 2, '.', ''),
                        number_format($b->selling_price_at_receipt ?: ($b->product?->selling_price ?? 0), 2, '.', ''),
                        $b->branch?->branch_name ?? 'Main Branch',
                    ]);
                }
            } else {
                fputcsv($file, [
                    'Product Name',
                    'SKU',
                    'Active Batches',
                    'Supplier',
                    'Available Quantity',
                    'Weighted Avg Cost (LKR)',
                    'Selling Price (LKR)',
                    'Total Valuation (Cost Value LKR)',
                    'Potential Revenue (LKR)',
                ]);

                $productWise = $batches->groupBy('product_id');
                foreach ($productWise as $prodBatches) {
                    $prod = $prodBatches->first()->product;
                    $qty = (float) $prodBatches->sum('remaining_quantity');
                    $val = (float) $prodBatches->sum(fn ($b) => (float) $b->remaining_quantity * (float) $b->inventory_unit_cost);
                    $avgCost = $qty > 0 ? $val / $qty : 0.0;
                    $potRev = $qty * (float) $prod->selling_price;

                    fputcsv($file, [
                        $prod?->name ?? 'N/A',
                        $prod?->sku ?? '-',
                        $prodBatches->count(),
                        $prod?->preferredSupplier?->name ?? 'N/A',
                        QuantityFormatter::format($qty, null, false),
                        number_format($avgCost, 2, '.', ''),
                        number_format($prod->selling_price, 2, '.', ''),
                        number_format($val, 2, '.', ''),
                        number_format($potRev, 2, '.', ''),
                    ]);
                }
            }

            fclose($file);
        }, $fileName, $headers);
    }
}
