<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreProductRequest;
use App\Http\Requests\Inventory\UpdateProductRequest;
use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\UnitOfMeasurement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $supplierId = $request->input('supplier_id');

        $query = Product::with(['uom', 'preferredSupplier']);

        if ($search !== '') {
            $query->search($search);
        }

        if ($supplierId) {
            $query->where('preferred_supplier_id', $supplierId);
        }

        $totalProducts = Product::count();
        $products = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();
        $suppliers = Supplier::active()->orderBy('name', 'asc')->get();

        return view('inventory.products.index', compact('products', 'search', 'supplierId', 'totalProducts', 'suppliers'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(Request $request): View
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized. Administrator access is required to add products.');
        }

        $uoms = UnitOfMeasurement::active()->orderBy('name', 'asc')->get();
        $suppliers = Supplier::active()->orderBy('name', 'asc')->get();

        return view('inventory.products.create', compact('uoms', 'suppliers'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $product = Product::create($data);

        ActivityLog::log(
            action: 'created',
            description: "Created product: {$product->name} (SKU: {$product->sku}).",
            module: 'Products'
        );

        return redirect()->route('inventory.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Request $request, Product $product): View
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized. Administrator access is required to edit products.');
        }

        $uoms = UnitOfMeasurement::active()->orderBy('name', 'asc')->get();
        $suppliers = Supplier::active()->orderBy('name', 'asc')->get();

        return view('inventory.products.edit', compact('product', 'uoms', 'suppliers'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $product->update($data);

        ActivityLog::log(
            action: 'updated',
            description: "Updated product details: {$product->name}.",
            module: 'Products'
        );

        return redirect()->route('inventory.products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Toggle the active/inactive status of a product.
     */
    public function toggleStatus(Request $request, Product $product): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized. Administrator access is required.');
        }

        $newStatus = ! $product->is_active;
        $product->update(['is_active' => $newStatus]);

        $statusLabel = $newStatus ? 'activated' : 'deactivated';

        ActivityLog::log(
            action: 'status_changed',
            description: "Product {$product->name} was {$statusLabel}.",
            module: 'Products'
        );

        return back()->with('success', "Product '{$product->name}' has been {$statusLabel}.");
    }

    /**
     * Delete product (safely protected if stock batches exist).
     */
    public function destroy(Request $request, Product $product): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized. Administrator access is required.');
        }

        if ($product->stockBatches()->exists() || $product->grnItems()->exists()) {
            return back()->with('error', "Cannot delete product '{$product->name}' because stock batches or purchase history exists. You may deactivate it instead.");
        }

        $product->delete();

        ActivityLog::log(
            action: 'deleted',
            description: "Deleted product: {$product->name}.",
            module: 'Products'
        );

        return redirect()->route('inventory.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
