@extends('layouts.app')

@section('title', 'Stock Value Report')

@section('content')
<div class="space-y-6" x-data="{ viewType: '{{ $viewType }}' }">
    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold text-[#0B1849] tracking-tight">Stock Value Report</h1>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-purple-100 text-purple-800">
                    Admin Confidential
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-1">Total purchase-cost inventory valuation calculated using exact FIFO batch costs (<code class="text-xs bg-gray-100 px-1 py-0.5 rounded text-gray-700">Remaining Qty × Actual Batch Unit Cost</code>).</p>
        </div>
        <div class="flex items-center gap-2">
            <a 
                href="{{ route('reports.stock-value.print', request()->query()) }}" 
                target="_blank" 
                class="inline-flex items-center px-3.5 py-2 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition shadow-xs"
            >
                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Valuation
            </a>
            <a 
                href="{{ route('reports.stock-value.export', request()->query()) }}" 
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
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Stock Value (Cost)</span>
            <div class="text-xl font-bold text-emerald-700 mt-1">LKR {{ number_format($grandTotalStockValue, 2) }}</div>
            <div class="text-[11px] text-gray-400 mt-0.5">Based on actual batch unit costs</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Stock Summary</span>
            @if(!empty($hasMultipleUoms))
                <div class="text-base font-bold text-[#0B1849] mt-1 flex items-baseline gap-1" title="{{ $uomSummaryText }}">
                    <span>Mixed Units</span>
                    <span class="text-xs font-medium text-gray-400">({{ count(explode('•', $uomSummaryText)) }} UOMs)</span>
                </div>
                <div class="text-[11px] text-gray-500 mt-0.5 truncate" title="{{ $uomSummaryText }}">
                    {{ $uomSummaryText }}
                </div>
            @else
                <div class="text-xl font-bold text-[#0B1849] mt-1">@quantity($grandTotalUnits) {{ $singleUomName ?? '' }}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">Across {{ $productWise->count() }} distinct products</div>
            @endif
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Active Batches</span>
            <div class="text-xl font-bold text-blue-600 mt-1">{{ number_format($grandTotalBatches) }}</div>
            <div class="text-[11px] text-gray-400 mt-0.5">Confirmed FIFO active lots</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Potential Sales Revenue</span>
            <div class="text-xl font-bold text-purple-700 mt-1">LKR {{ number_format($grandTotalPotentialRevenue, 2) }}</div>
            <div class="text-[11px] text-emerald-600 font-semibold mt-0.5">Margin: LKR {{ number_format(max(0, $grandTotalPotentialRevenue - $grandTotalStockValue), 2) }}</div>
        </div>
    </div>

    <!-- Filters & View Toggle -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs space-y-4">
        <form method="GET" action="{{ route('reports.stock-value.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end">
            <!-- View Mode -->
            <div>
                <label for="view_type" class="block text-xs font-semibold text-gray-600 mb-1">View Format</label>
                <select id="view_type" name="view_type" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]">
                    <option value="product" {{ $viewType === 'product' ? 'selected' : '' }}>Product-wise Summary</option>
                    <option value="batch" {{ $viewType === 'batch' ? 'selected' : '' }}>Detailed Batch-wise Breakdown</option>
                </select>
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

            <!-- Branch -->
            <div>
                <label for="branch_id" class="block text-xs font-semibold text-gray-600 mb-1">Branch</label>
                <select id="branch_id" name="branch_id" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]">
                    <option value="all" {{ $branchId === 'all' ? 'selected' : '' }}>All Branches (Company-wide)</option>
                    @foreach($accessibleBranches as $b)
                        <option value="{{ $b->id }}" {{ (string)$branchId === (string)$b->id ? 'selected' : '' }}>
                            {{ $b->branch_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- As of Date -->
            <div>
                <label for="as_of_date" class="block text-xs font-semibold text-gray-600 mb-1">Valuation As Of Date</label>
                <input 
                    type="date" 
                    id="as_of_date" 
                    name="as_of_date" 
                    value="{{ $asOfDate }}" 
                    class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]"
                >
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-[#0B1849] text-white text-xs font-semibold rounded-lg hover:bg-[#0B1849]/90 transition">
                    Filter
                </button>
                <a href="{{ route('reports.stock-value.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- 1. Product-wise Table -->
    @if($viewType === 'product')
        <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                <span class="text-xs font-bold text-[#0B1849] uppercase tracking-wider">Product Valuation Summary</span>
                <span class="text-xs text-gray-500">{{ $productWise->count() }} items listed</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase tracking-wider">
                            <th class="py-3 px-4">Product Name</th>
                            <th class="py-3 px-4">SKU</th>
                            <th class="py-3 px-4">Supplier</th>
                            <th class="py-3 px-4 text-center">Active Batches</th>
                            <th class="py-3 px-4 text-center">Available Units</th>
                            <th class="py-3 px-4 text-right">Weighted Avg Cost</th>
                            <th class="py-3 px-4 text-right">Selling Price</th>
                            <th class="py-3 px-4 text-right">Total Stock Value</th>
                            <th class="py-3 px-4 text-right">Unrealized Margin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($productWise as $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-4 font-semibold text-[#0B1849]">
                                    {{ $item['product']->name }}
                                </td>
                                <td class="py-3 px-4 font-mono text-gray-500">
                                    {{ $item['product']->sku ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-gray-600">
                                    {{ $item['supplier_name'] }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-blue-50 text-blue-700">
                                        {{ $item['batches_count'] }} {{ Str::plural('batch', $item['batches_count']) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-gray-800">
                                    @quantity($item['total_quantity']) {{ $item['product']->uom?->name ?? '' }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono text-gray-600">
                                    LKR {{ number_format($item['weighted_avg_cost'], 2) }}
                                </td>
                                <td class="py-3 px-4 text-right font-medium text-gray-800">
                                    LKR {{ number_format($item['selling_price'], 2) }}
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-emerald-700">
                                    LKR {{ number_format($item['total_valuation'], 2) }}
                                </td>
                                <td class="py-3 px-4 text-right font-semibold text-purple-700">
                                    LKR {{ number_format($item['unrealized_margin'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-gray-500 text-xs">
                                    No inventory found to evaluate.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold border-t border-gray-200 text-gray-900">
                        <tr>
                            <td colspan="4" class="py-3 px-4 text-right uppercase tracking-wider text-xs">Grand Total Valuation:</td>
                            <td class="py-3 px-4 text-center text-xs text-[#0B1849]">
                                @if(!empty($hasMultipleUoms))
                                    <span class="text-gray-500 font-normal">Mixed Units</span>
                                @else
                                    @quantity($grandTotalUnits) {{ $singleUomName ?? '' }}
                                @endif
                            </td>
                            <td colspan="2"></td>
                            <td class="py-3 px-4 text-right text-xs text-emerald-700">LKR {{ number_format($grandTotalStockValue, 2) }}</td>
                            <td class="py-3 px-4 text-right text-xs text-purple-700">LKR {{ number_format(max(0, $grandTotalPotentialRevenue - $grandTotalStockValue), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @else
        <!-- 2. Detailed Batch-wise Table -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                <span class="text-xs font-bold text-[#0B1849] uppercase tracking-wider">Detailed FIFO Batch Valuation</span>
                <span class="text-xs text-gray-500">{{ $batches->count() }} active lots</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase tracking-wider">
                            <th class="py-3 px-4">Batch Number</th>
                            <th class="py-3 px-4">Product Name</th>
                            <th class="py-3 px-4">GRN #</th>
                            <th class="py-3 px-4">Purchase Date</th>
                            <th class="py-3 px-4">Supplier</th>
                            <th class="py-3 px-4 text-right">Remaining Qty</th>
                            <th class="py-3 px-4 text-right">Actual Unit Cost</th>
                            <th class="py-3 px-4 text-right">Total Batch Value</th>
                            <th class="py-3 px-4">Branch</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($batches as $b)
                            @php
                                $val = (float)$b->remaining_quantity * (float)$b->inventory_unit_cost;
                            @endphp
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-4 font-mono font-bold text-[#0B1849]">
                                    {{ $b->batch_number }}
                                </td>
                                <td class="py-3 px-4 font-semibold text-gray-800">
                                    {{ $b->product?->name ?? 'N/A' }}
                                </td>
                                <td class="py-3 px-4 text-gray-600">
                                    {{ $b->grn?->grn_number ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-gray-500">
                                    {{ $b->purchase_date?->format('Y-m-d') ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-gray-600 truncate max-w-[150px]">
                                    {{ $b->supplier?->name ?? 'N/A' }}
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-gray-900">
                                    @quantity($b->remaining_quantity)
                                </td>
                                <td class="py-3 px-4 text-right font-mono text-gray-700">
                                    LKR {{ number_format($b->inventory_unit_cost, 2) }}
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-emerald-700">
                                    LKR {{ number_format($val, 2) }}
                                </td>
                                <td class="py-3 px-4 text-gray-500">
                                    {{ $b->branch?->branch_name ?? 'Main Branch' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-gray-500 text-xs">
                                    No active stock batches found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold border-t border-gray-200 text-gray-900">
                        <tr>
                            <td colspan="5" class="py-3 px-4 text-right uppercase tracking-wider text-xs">Grand Total Valuation:</td>
                            <td class="py-3 px-4 text-right text-xs text-[#0B1849]">
                                @if(!empty($hasMultipleUoms))
                                    <span class="text-gray-500 font-normal">Mixed Units</span>
                                @else
                                    @quantity($grandTotalUnits) {{ $singleUomName ?? '' }}
                                @endif
                            </td>
                            <td></td>
                            <td class="py-3 px-4 text-right text-xs text-emerald-700">LKR {{ number_format($grandTotalStockValue, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
