<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockBatch;
use App\Models\Supplier;
use App\Services\QuantityFormatter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StockReportController extends Controller
{
    /**
     * Display the inventory stock report.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $supplierId = $request->input('supplier_id');
        $lowStockOnly = $request->boolean('low_stock');

        $query = Product::with(['uom', 'preferredSupplier', 'activeStockBatches.supplier']);

        if ($search !== '') {
            $query->search($search);
        }

        if ($supplierId) {
            $query->where('preferred_supplier_id', $supplierId);
        }

        // Compute overall inventory totals
        $totalProducts = Product::count();
        $totalInventoryValue = (float) StockBatch::where('remaining_quantity', '>', 0)
            ->selectRaw('SUM(remaining_quantity * inventory_unit_cost) as total_val')
            ->value('total_val') ?? 0.0;

        $allProductsForCount = Product::with('activeStockBatches')->get();
        $lowStockCount = $allProductsForCount->filter(fn ($p) => $p->isLowStock())->count();

        $products = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        if ($lowStockOnly) {
            // In-memory filter on current collection if flag checked
            $filtered = $products->getCollection()->filter(fn ($p) => $p->isLowStock());
            $products->setCollection($filtered);
        }

        $suppliers = Supplier::orderBy('name', 'asc')->get();

        return view('inventory.stock-report.index', compact(
            'products',
            'search',
            'supplierId',
            'lowStockOnly',
            'totalProducts',
            'totalInventoryValue',
            'lowStockCount',
            'suppliers'
        ));
    }

    /**
     * Return active stock batches for a product (Batch Details Popup).
     */
    public function batches(Product $product): JsonResponse
    {
        $batches = StockBatch::with(['grn', 'supplier'])
            ->where('product_id', $product->id)
            ->orderBy('purchase_date', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'batch_number' => $b->batch_number,
                    'grn_id' => $b->grn_id,
                    'grn_number' => $b->grn ? $b->grn->grn_number : '-',
                    'supplier_name' => $b->supplier ? $b->supplier->name : 'N/A',
                    'purchase_date' => $b->purchase_date->format('Y-m-d'),
                    'original_purchase_unit_price' => number_format($b->original_purchase_unit_price, 2),
                    'inventory_unit_cost' => number_format($b->inventory_unit_cost, 4),
                    'received_quantity' => QuantityFormatter::format($b->received_quantity),
                    'remaining_quantity' => QuantityFormatter::format($b->remaining_quantity),
                    'batch_value' => number_format($b->batch_value, 2),
                    'status' => $b->status,
                    'grn_url' => route('inventory.grn.show', $b->grn_id),
                ];
            });

        return response()->json([
            'product_name' => $product->name,
            'sku' => $product->sku,
            'batches' => $batches,
        ]);
    }

    /**
     * Return purchase price history for a product (Price History Popup).
     */
    public function priceHistory(Product $product): JsonResponse
    {
        $history = StockBatch::with(['grn', 'supplier'])
            ->where('product_id', $product->id)
            ->orderBy('purchase_date', 'desc')
            ->get()
            ->map(function ($b) {
                return [
                    'purchase_date' => $b->purchase_date->format('Y-m-d'),
                    'grn_number' => $b->grn ? $b->grn->grn_number : '-',
                    'supplier_name' => $b->supplier ? $b->supplier->name : 'N/A',
                    'purchase_price' => number_format($b->original_purchase_unit_price, 2),
                    'effective_unit_cost' => number_format($b->inventory_unit_cost, 4),
                    'received_quantity' => QuantityFormatter::format($b->received_quantity),
                    'selling_price_at_receipt' => $b->selling_price_at_receipt ? number_format($b->selling_price_at_receipt, 2) : '-',
                ];
            });

        return response()->json([
            'product_name' => $product->name,
            'sku' => $product->sku,
            'current_selling_price' => number_format($product->selling_price, 2),
            'history' => $history,
        ]);
    }

    /**
     * Export the stock report to CSV (Excel compatible).
     */
    public function export(Request $request): StreamedResponse
    {
        $search = trim((string) $request->input('search'));
        $query = Product::with(['uom', 'preferredSupplier', 'activeStockBatches']);

        if ($search !== '') {
            $query->search($search);
        }

        $products = $query->orderBy('name', 'asc')->get();
        $fileName = 'stock_report_'.now()->format('Y_m_d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->streamDownload(function () use ($products) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'SKU / Code',
                'Product Name',
                'UOM',
                'Preferred Supplier',
                'Selling Price (LKR)',
                'Available Stock',
                'Min Stock Alert',
                'Total Inventory Value (LKR)',
                'Stock Status',
            ]);

            foreach ($products as $p) {
                fputcsv($file, [
                    $p->sku ?? '',
                    $p->name,
                    $p->uom ? $p->uom->name : '',
                    $p->preferredSupplier ? $p->preferredSupplier->name : '',
                    number_format($p->selling_price, 2, '.', ''),
                    number_format($p->available_stock, 2, '.', ''),
                    number_format($p->min_stock, 2, '.', ''),
                    number_format($p->inventory_value, 2, '.', ''),
                    $p->isLowStock() ? 'Low Stock' : ($p->available_stock > 0 ? 'In Stock' : 'Out of Stock'),
                ]);
            }

            fclose($file);
        }, $fileName, $headers);
    }
}
