@extends('layouts.app')

@section('title', 'Stock Report')

@section('content')
    <div 
        x-data="{
            batchModalOpen: false,
            historyModalOpen: false,
            modalLoading: false,
            modalProduct: { name: '', sku: '', current_price: '' },
            batches: [],
            history: [],
            openBatchModal(productId) {
                this.batchModalOpen = true;
                this.modalLoading = true;
                this.batches = [];
                fetch(`/inventory/stock-report/${productId}/batches`)
                    .then(res => res.json())
                    .then(data => {
                        this.modalProduct = { name: data.product_name, sku: data.sku };
                        this.batches = data.batches;
                        this.modalLoading = false;
                    })
                    .catch(() => {
                        this.modalLoading = false;
                    });
            },
            openHistoryModal(productId) {
                this.historyModalOpen = true;
                this.modalLoading = true;
                this.history = [];
                fetch(`/inventory/stock-report/${productId}/price-history`)
                    .then(res => res.json())
                    .then(data => {
                        this.modalProduct = { 
                            name: data.product_name, 
                            sku: data.sku, 
                            current_price: data.current_selling_price 
                        };
                        this.history = data.history;
                        this.modalLoading = false;
                    })
                    .catch(() => {
                        this.modalLoading = false;
                    });
            }
        }"
    >
        <!-- Page Header -->
        <x-page-header 
            title="Stock Report" 
            description="Real-time warehouse stock balance, FIFO batch valuation, and price histories."
        >
            <x-slot:actions>
                <div class="flex items-center space-x-3">
                    <a 
                        href="{{ route('inventory.stock-report.export', request()->query()) }}" 
                        class="inline-flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 shadow-2xs transition"
                    >
                        <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Excel
                    </a>
                </div>
            </x-slot:actions>
        </x-page-header>

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Products</span>
                <div class="text-2xl font-bold text-gray-900 mt-1">{{ $totalProducts }}</div>
                <span class="text-xs text-gray-500">Catalog items registered</span>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Total Inventory Value (FIFO)</span>
                <div class="text-2xl font-bold font-mono text-emerald-800 mt-1">
                    LKR {{ number_format($totalInventoryValue, 2) }}
                </div>
                <span class="text-xs text-emerald-600">Sum of all remaining batches at cost</span>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold {{ $lowStockCount > 0 ? 'text-amber-700' : 'text-gray-400' }} uppercase tracking-wider">
                    Low Stock Alerts
                </span>
                <div class="text-2xl font-bold {{ $lowStockCount > 0 ? 'text-amber-700' : 'text-gray-900' }} mt-1">
                    {{ $lowStockCount }}
                </div>
                <span class="text-xs text-gray-500">Items at or below reorder level</span>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <x-search-bar 
            :action="route('inventory.stock-report.index')" 
            :search="$search" 
            placeholder="Search Product by name, code/SKU..."
            :resetUrl="route('inventory.stock-report.index')"
        >
            <div class="w-full sm:w-56">
                <select 
                    name="supplier_id" 
                    onchange="this.form.submit()"
                    class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                >
                    <option value="">All Preferred Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ $supplierId == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </x-search-bar>

        <!-- Stock Report Table -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
            <div class="responsive-table-wrapper">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Product Code / SKU
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Product Name
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Preferred Supplier
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Selling Price (LKR)
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Available Stock
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-center text-xs font-bold text-gray-700 uppercase tracking-wider w-44">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($products as $product)
                            @php 
                                $stock = $product->available_stock;
                                $isLow = $product->isLowStock();
                            @endphp
                            <tr class="hover:bg-gray-50/70 transition {{ $isLow ? 'bg-amber-50/30' : '' }}">
                                <!-- Product Code -->
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-gray-700">
                                    {{ $product->sku ?: '-' }}
                                </td>

                                <!-- Product Name -->
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $product->name }}</div>
                                    <span class="text-xs text-gray-400">Unit: {{ $product->uom ? $product->uom->name : 'Unit' }}</span>
                                </td>

                                <!-- Supplier -->
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $product->preferredSupplier ? $product->preferredSupplier->name : '-' }}
                                </td>

                                <!-- Selling Price -->
                                <td class="px-6 py-4 whitespace-nowrap text-right font-mono font-bold text-sm text-gray-900">
                                    {{ number_format($product->selling_price, 2) }}
                                </td>

                                <!-- Available Stock -->
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="font-mono font-bold text-sm {{ $stock <= 0 ? 'text-gray-400' : ($isLow ? 'text-amber-700' : 'text-emerald-700') }}">
                                        @quantity($stock)
                                    </div>
                                    @if($isLow)
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-amber-100 text-amber-800">
                                            Low Stock
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-center text-xs font-medium space-x-1.5">
                                    <button 
                                        type="button" 
                                        @click="openBatchModal({{ $product->id }})"
                                        class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-md text-[#0B1849] bg-blue-50 hover:bg-blue-100 border border-blue-200 transition cursor-pointer"
                                    >
                                        [Batches]
                                    </button>

                                    <button 
                                        type="button" 
                                        @click="openHistoryModal({{ $product->id }})"
                                        class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-200 transition cursor-pointer"
                                    >
                                        [Price History]
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    No products found matching your filter criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

        <!-- 10. BATCH DETAILS POPUP MODAL -->
        <div 
            x-show="batchModalOpen" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
            x-on:keydown.escape.window="batchModalOpen = false"
        >
            <div 
                x-show="batchModalOpen" 
                @click="batchModalOpen = false"
                class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs"
            ></div>

            <div 
                x-show="batchModalOpen" 
                class="relative bg-white rounded-xl shadow-xl w-full max-w-[calc(100vw-2rem)] sm:max-w-4xl max-h-[88vh] overflow-y-auto border border-gray-200 z-10"
            >
                <div class="px-6 py-4 bg-gray-50/80 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Batch Details</span>
                        <h3 class="text-lg font-bold text-gray-900" x-text="modalProduct.name"></h3>
                    </div>
                    <button 
                        type="button" 
                        @click="batchModalOpen = false" 
                        class="text-gray-400 hover:text-gray-600 p-1 rounded-lg"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <div x-show="modalLoading" class="py-8 text-center text-gray-500 text-sm">
                        Loading active batches...
                    </div>

                    <div x-show="!modalLoading && batches.length === 0" class="py-8 text-center text-gray-400 text-sm">
                        No purchase batches recorded yet for this product.
                    </div>

                    <div x-show="!modalLoading && batches.length > 0" class="responsive-table-wrapper">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-xs font-bold text-gray-600 uppercase">
                                <tr>
                                    <th class="px-3 py-2 text-left">Batch</th>
                                    <th class="px-3 py-2 text-left">GRN</th>
                                    <th class="px-3 py-2 text-left">Supplier</th>
                                    <th class="px-3 py-2 text-left">Date</th>
                                    <th class="px-3 py-2 text-right">Cost (LKR)</th>
                                    <th class="px-3 py-2 text-right">Stock</th>
                                    <th class="px-3 py-2 text-right">Batch Value</th>
                                    <th class="px-3 py-2 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="b in batches" :key="b.id">
                                    <tr :class="b.remaining_quantity > 0 ? 'bg-white' : 'bg-gray-50/50 text-gray-400'">
                                        <td class="px-3 py-2.5 font-mono font-bold" x-text="b.batch_number"></td>
                                        <td class="px-3 py-2.5 font-semibold text-[#0B1849]" x-text="b.grn_number"></td>
                                        <td class="px-3 py-2.5" x-text="b.supplier_name"></td>
                                        <td class="px-3 py-2.5 text-xs text-gray-500" x-text="b.purchase_date"></td>
                                        <td class="px-3 py-2.5 text-right font-mono font-semibold" x-text="b.inventory_unit_cost"></td>
                                        <td class="px-3 py-2.5 text-right font-mono font-bold" :class="b.remaining_quantity > 0 ? 'text-emerald-700' : 'text-gray-400'" x-text="b.remaining_quantity"></td>
                                        <td class="px-3 py-2.5 text-right font-mono font-bold text-gray-900" x-text="b.batch_value"></td>
                                        <td class="px-3 py-2.5 text-center">
                                            <a :href="b.grn_url" target="_blank" class="text-xs text-blue-600 hover:underline">
                                                View GRN
                                            </a>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 text-right">
                    <button 
                        type="button" 
                        @click="batchModalOpen = false" 
                        class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- 11. PRICE HISTORY POPUP MODAL -->
        <div 
            x-show="historyModalOpen" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
            x-on:keydown.escape.window="historyModalOpen = false"
        >
            <div 
                x-show="historyModalOpen" 
                @click="historyModalOpen = false"
                class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs"
            ></div>

            <div 
                x-show="historyModalOpen" 
                class="relative bg-white rounded-xl shadow-xl w-full max-w-[calc(100vw-2rem)] sm:max-w-3xl max-h-[88vh] overflow-y-auto border border-gray-200 z-10"
            >
                <div class="px-6 py-4 bg-gray-50/80 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Purchase Price History</span>
                        <h3 class="text-lg font-bold text-gray-900" x-text="modalProduct.name"></h3>
                        <p class="text-xs text-gray-500 mt-0.5">Current Selling Price: LKR <span class="font-bold text-gray-900 font-mono" x-text="modalProduct.current_price"></span></p>
                    </div>
                    <button 
                        type="button" 
                        @click="historyModalOpen = false" 
                        class="text-gray-400 hover:text-gray-600 p-1 rounded-lg"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <div x-show="modalLoading" class="py-8 text-center text-gray-500 text-sm">
                        Loading price records...
                    </div>

                    <div x-show="!modalLoading && history.length === 0" class="py-8 text-center text-gray-400 text-sm">
                        No historical purchases recorded yet for this product.
                    </div>

                    <div x-show="!modalLoading && history.length > 0" class="responsive-table-wrapper">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-xs font-bold text-gray-600 uppercase">
                                <tr>
                                    <th class="px-4 py-2 text-left">GRN Date</th>
                                    <th class="px-4 py-2 text-left">GRN #</th>
                                    <th class="px-4 py-2 text-left">Supplier</th>
                                    <th class="px-4 py-2 text-right">Received Qty</th>
                                    <th class="px-4 py-2 text-right">Invoice Unit Price</th>
                                    <th class="px-4 py-2 text-right">Effective Cost</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="(h, idx) in history" :key="idx">
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-4 py-2.5 font-medium text-gray-900" x-text="h.purchase_date"></td>
                                        <td class="px-4 py-2.5 font-mono text-[#0B1849]" x-text="h.grn_number"></td>
                                        <td class="px-4 py-2.5 text-gray-700" x-text="h.supplier_name"></td>
                                        <td class="px-4 py-2.5 text-right font-mono" x-text="h.received_quantity"></td>
                                        <td class="px-4 py-2.5 text-right font-mono font-bold text-gray-900" x-text="h.purchase_price"></td>
                                        <td class="px-4 py-2.5 text-right font-mono font-semibold text-blue-900" x-text="h.effective_unit_cost"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 text-right">
                    <button 
                        type="button" 
                        @click="historyModalOpen = false" 
                        class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
