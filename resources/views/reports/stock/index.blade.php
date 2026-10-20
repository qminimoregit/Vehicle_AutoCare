@extends('layouts.app')

@section('title', 'Stock Report')

@section('content')
<div class="space-y-6" x-data="stockReportApp()">
    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#0B1849] tracking-tight">Stock Report</h1>
            <p class="text-sm text-gray-500 mt-1">Current inventory stock levels based on confirmed FIFO batches.</p>
        </div>
        <div class="flex items-center gap-2">
            <a 
                href="{{ route('reports.stock.print', request()->query()) }}" 
                target="_blank" 
                class="inline-flex items-center px-3.5 py-2 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition shadow-xs"
            >
                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Report
            </a>
            <a 
                href="{{ route('reports.stock.export', request()->query()) }}" 
                class="inline-flex items-center px-3.5 py-2 rounded-lg bg-[#0B1849] text-xs font-semibold text-white hover:bg-[#0B1849]/90 transition shadow-xs"
            >
                <svg class="w-4 h-4 mr-1.5 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </a>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Products</span>
            <div class="text-xl font-bold text-[#0B1849] mt-1">{{ number_format($totalProducts) }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Stock Summary</span>
            @if(!empty($hasMultipleUoms))
                <div class="text-base font-bold text-blue-600 mt-1 flex items-baseline gap-1" title="{{ $uomSummaryText }}">
                    <span>Mixed Units</span>
                    <span class="text-xs font-medium text-gray-400">({{ count(explode('•', $uomSummaryText)) }} UOMs)</span>
                </div>
                <div class="text-[11px] text-gray-500 mt-0.5 truncate" title="{{ $uomSummaryText }}">
                    {{ $uomSummaryText }}
                </div>
            @else
                <div class="text-xl font-bold text-blue-600 mt-1">
                    @quantity($totalStockUnits) {{ $singleUomName ?? '' }}
                </div>
            @endif
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Low Stock Alert</span>
            <div class="text-xl font-bold text-amber-600 mt-1">{{ number_format($lowStockCount) }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Out of Stock</span>
            <div class="text-xl font-bold text-rose-600 mt-1">{{ number_format($outOfStockCount) }}</div>
        </div>
    </div>

    <!-- Compact Filter Form -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
        <form method="GET" action="{{ route('reports.stock.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end">
            <!-- Search -->
            <div>
                <label for="search" class="block text-xs font-semibold text-gray-600 mb-1">Search Product / SKU</label>
                <input 
                    type="text" 
                    id="search" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Search name or SKU..." 
                    class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]"
                >
            </div>

            <!-- Supplier -->
            <div>
                <label for="supplier_id" class="block text-xs font-semibold text-gray-600 mb-1">Supplier</label>
                <select id="supplier_id" name="supplier_id" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ (string)$supplierId === (string)$supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Stock Status -->
            <div>
                <label for="stock_status" class="block text-xs font-semibold text-gray-600 mb-1">Stock Status</label>
                <select id="stock_status" name="stock_status" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]">
                    <option value="all" {{ $stockStatus === 'all' ? 'selected' : '' }}>All Products</option>
                    <option value="in_stock" {{ $stockStatus === 'in_stock' ? 'selected' : '' }}>In Stock Only</option>
                    <option value="low_stock" {{ $stockStatus === 'low_stock' ? 'selected' : '' }}>Low Stock Alerts</option>
                    <option value="out_of_stock" {{ $stockStatus === 'out_of_stock' ? 'selected' : '' }}>Out of Stock Only</option>
                </select>
            </div>

            <!-- Branch Filter -->
            <div>
                <label for="branch_id" class="block text-xs font-semibold text-gray-600 mb-1">Branch</label>
                <select id="branch_id" name="branch_id" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]">
                    @if(Auth::user()->isAdmin())
                        <option value="all" {{ $branchId === 'all' ? 'selected' : '' }}>All Branches (Company-wide)</option>
                    @endif
                    @foreach($accessibleBranches as $b)
                        <option value="{{ $b->id }}" {{ (string)$branchId === (string)$b->id ? 'selected' : '' }}>
                            {{ $b->branch_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-[#0B1849] text-white text-xs font-semibold rounded-lg hover:bg-[#0B1849]/90 transition">
                    Filter
                </button>
                <a href="{{ route('reports.stock.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Main Stock Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase tracking-wider">
                        <th class="py-3 px-4">Product Name</th>
                        <th class="py-3 px-4">SKU</th>
                        <th class="py-3 px-4">Supplier</th>
                        <th class="py-3 px-4 text-center">Available Stock</th>
                        <th class="py-3 px-4">UOM</th>
                        <th class="py-3 px-4 text-right">Selling Price</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-4 font-semibold text-[#0B1849]">
                                {{ $product->name }}
                            </td>
                            <td class="py-3 px-4 font-mono text-gray-500">
                                {{ $product->sku ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-gray-600">
                                {{ $product->preferredSupplier?->name ?? 'N/A' }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold {{ $product->computed_available_stock <= 0 ? 'text-rose-600' : ($product->min_stock > 0 && $product->computed_available_stock <= (float)$product->min_stock ? 'text-amber-600' : 'text-emerald-700') }}">
                                @quantity($product->computed_available_stock)
                            </td>
                            <td class="py-3 px-4 text-gray-500">
                                {{ $product->uom?->name ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-right font-medium text-gray-900">
                                LKR {{ number_format($product->selling_price, 2) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($product->computed_available_stock <= 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        Out of Stock
                                    </span>
                                @elseif($product->min_stock > 0 && $product->computed_available_stock <= (float)$product->min_stock)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Low Stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        In Stock
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button 
                                    type="button" 
                                    @click="openBatches({{ $product->id }})" 
                                    class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-[#0B1849] bg-blue-50 hover:bg-blue-100 rounded-md transition"
                                >
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    Batches
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500 text-xs">
                                No products found matching your filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Batch Details Modal -->
    <div 
        x-show="modalOpen" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4"
    >
        <div 
            @click.away="modalOpen = false" 
            class="bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-3xl overflow-hidden animate-in fade-in zoom-in-95 duration-150"
        >
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-[#0B1849] text-white flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold" x-text="batchData.product_name + ' – FIFO Batches'"></h3>
                    <p class="text-xs text-blue-200 mt-0.5" x-text="'SKU: ' + (batchData.sku || '-') + ' | Selling Price: LKR ' + batchData.selling_price"></p>
                </div>
                <button type="button" @click="modalOpen = false" class="text-gray-300 hover:text-white p-1">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <template x-if="loading">
                    <div class="py-12 text-center text-gray-500 text-xs">
                        Loading active batches...
                    </div>
                </template>

                <template x-if="!loading && batchData.batches && batchData.batches.length === 0">
                    <div class="py-12 text-center text-gray-500 text-xs">
                        No active stock batches available for this product.
                    </div>
                </template>

                <template x-if="!loading && batchData.batches && batchData.batches.length > 0">
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200">
                                <tr>
                                    <th class="py-2.5 px-3">Batch #</th>
                                    <th class="py-2.5 px-3">GRN #</th>
                                    <th class="py-2.5 px-3">Date</th>
                                    <th class="py-2.5 px-3">Supplier</th>
                                    <th class="py-2.5 px-3 text-right">Original</th>
                                    <th class="py-2.5 px-3 text-right">Remaining</th>
                                    @if(Auth::user()->isAdmin())
                                        <th class="py-2.5 px-3 text-right">Unit Cost</th>
                                    @endif
                                    <th class="py-2.5 px-3">Branch</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700">
                                <template x-for="b in batchData.batches" :key="b.id">
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-2.5 px-3 font-mono font-medium text-[#0B1849]" x-text="b.batch_number"></td>
                                        <td class="py-2.5 px-3 text-gray-600" x-text="b.grn_number"></td>
                                        <td class="py-2.5 px-3 text-gray-500" x-text="b.purchase_date"></td>
                                        <td class="py-2.5 px-3 text-gray-600 truncate max-w-[150px]" x-text="b.supplier_name"></td>
                                        <td class="py-2.5 px-3 text-right text-gray-500" x-text="b.original_quantity"></td>
                                        <td class="py-2.5 px-3 text-right font-bold text-emerald-700" x-text="b.remaining_quantity"></td>
                                        @if(Auth::user()->isAdmin())
                                            <td class="py-2.5 px-3 text-right font-medium text-gray-800" x-text="'LKR ' + b.purchase_cost"></td>
                                        @endif
                                        <td class="py-2.5 px-3 text-gray-500" x-text="b.branch_name"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex justify-end">
                <button type="button" @click="modalOpen = false" class="px-4 py-2 text-xs font-semibold bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function stockReportApp() {
    return {
        modalOpen: false,
        loading: false,
        batchData: {},
        currentBranchId: '{{ $branchId }}',

        openBatches(productId) {
            this.modalOpen = true;
            this.loading = true;
            this.batchData = {};

            fetch(`/reports/stock/${productId}/batches?branch_id=${this.currentBranchId}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                this.batchData = data;
                this.loading = false;
            })
            .catch(err => {
                console.error(err);
                this.loading = false;
            });
        }
    };
}
</script>
@endpush
@endsection
