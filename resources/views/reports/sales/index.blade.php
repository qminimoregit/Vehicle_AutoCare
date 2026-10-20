@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
<div class="space-y-6">
    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#0B1849] tracking-tight">Sales Report</h1>
            <p class="text-sm text-gray-500 mt-1">Operational sales ledger and revenue recognition for direct sales and service jobs.</p>
        </div>
        <div class="flex items-center gap-2">
            <a 
                href="{{ route('reports.sales.print', request()->query()) }}" 
                target="_blank" 
                class="inline-flex items-center px-3.5 py-2 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition shadow-xs"
            >
                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Report
            </a>
            <a 
                href="{{ route('reports.sales.export', request()->query()) }}" 
                class="inline-flex items-center px-3.5 py-2 rounded-lg bg-[#0B1849] text-xs font-semibold text-white hover:bg-[#0B1849]/90 transition shadow-xs"
            >
                <svg class="w-4 h-4 mr-1.5 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </a>
        </div>
    </div>

    <!-- Revenue Recognition Note -->
    <div class="p-3.5 bg-blue-50/70 border border-blue-200 rounded-xl flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-700 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div class="text-xs text-blue-900 leading-relaxed">
            <span class="font-bold">Sales Recognition Policy:</span> Revenue is recognized on the date the invoice is issued for confirmed direct sales and finished service jobs. Cancelled invoices are excluded, unaccepted quotations are not counted, and subsequent customer payments reduce accounts receivable without duplicating revenue.
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Direct Sales</span>
            <div class="text-lg font-bold text-gray-800 mt-1">LKR {{ number_format($totalDirectSales, 2) }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Service Sales</span>
            <div class="text-lg font-bold text-gray-800 mt-1">LKR {{ number_format($totalServiceSales, 2) }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Recognized Sales</span>
            <div class="text-lg font-bold text-[#0B1849] mt-1">LKR {{ number_format($totalSales, 2) }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Collected</span>
            <div class="text-lg font-bold text-emerald-700 mt-1">LKR {{ number_format($totalCollected, 2) }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Outstanding</span>
            <div class="text-lg font-bold text-rose-600 mt-1">LKR {{ number_format($totalOutstanding, 2) }}</div>
        </div>
    </div>

    <!-- Filters Form -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
        <form method="GET" action="{{ route('reports.sales.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3 items-end">
            <!-- Date From -->
            <div>
                <label for="date_from" class="block text-xs font-semibold text-gray-600 mb-1">Date From</label>
                <input 
                    type="date" 
                    id="date_from" 
                    name="date_from" 
                    value="{{ $dateFrom }}" 
                    class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]"
                >
            </div>

            <!-- Date To -->
            <div>
                <label for="date_to" class="block text-xs font-semibold text-gray-600 mb-1">Date To</label>
                <input 
                    type="date" 
                    id="date_to" 
                    name="date_to" 
                    value="{{ $dateTo }}" 
                    class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]"
                >
            </div>

            <!-- Sale Type -->
            <div>
                <label for="sale_type" class="block text-xs font-semibold text-gray-600 mb-1">Sale Type</label>
                <select id="sale_type" name="sale_type" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]">
                    <option value="all" {{ $saleType === 'all' ? 'selected' : '' }}>All Sales</option>
                    <option value="direct_sale" {{ $saleType === 'direct_sale' ? 'selected' : '' }}>Direct Sales (POS)</option>
                    <option value="service_sale" {{ $saleType === 'service_sale' ? 'selected' : '' }}>Service Sales (Job Cards)</option>
                    <option value="credit_sale" {{ $saleType === 'credit_sale' ? 'selected' : '' }}>Credit Sales (Unpaid/Partially)</option>
                </select>
            </div>

            <!-- Payment Status -->
            <div>
                <label for="payment_status" class="block text-xs font-semibold text-gray-600 mb-1">Payment Status</label>
                <select id="payment_status" name="payment_status" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]">
                    <option value="all" {{ $paymentStatus === 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="paid" {{ $paymentStatus === 'paid' ? 'selected' : '' }}>Paid in Full</option>
                    <option value="partially_paid" {{ $paymentStatus === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="unpaid" {{ $paymentStatus === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>
            </div>

            <!-- Branch -->
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

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-[#0B1849] text-white text-xs font-semibold rounded-lg hover:bg-[#0B1849]/90 transition">
                    Search
                </button>
                <a href="{{ route('reports.sales.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase tracking-wider">
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Invoice #</th>
                        <th class="py-3 px-4">Customer / Company</th>
                        <th class="py-3 px-4">Vehicle</th>
                        <th class="py-3 px-4">Sale Type</th>
                        <th class="py-3 px-4 text-right">Total</th>
                        <th class="py-3 px-4 text-right">Paid</th>
                        <th class="py-3 px-4 text-right">Balance</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($invoices as $inv)
                        @php
                            $isDirect = ($inv->invoice_type === 'direct_sale' || $inv->directSale !== null);
                            $vehicle = $inv->jobCard?->vehicle?->vehicle_number ?? '-';
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 text-gray-600 whitespace-nowrap">
                                {{ $inv->invoice_date?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-[#0B1849]">
                                <a href="{{ route('billing.invoices.show', $inv) }}" class="hover:underline">
                                    {{ $inv->invoice_number }}
                                </a>
                            </td>
                            <td class="py-3 px-4 font-medium text-gray-900">
                                {{ $inv->customer?->display_name ?? 'Guest Customer' }}
                            </td>
                            <td class="py-3 px-4 font-mono text-gray-600">
                                {{ $vehicle }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium {{ $isDirect ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                                    {{ $isDirect ? 'Direct Sale' : 'Service Sale' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-gray-900">
                                LKR {{ number_format($inv->grand_total, 2) }}
                            </td>
                            <td class="py-3 px-4 text-right font-medium text-emerald-700">
                                LKR {{ number_format($inv->amount_paid, 2) }}
                            </td>
                            <td class="py-3 px-4 text-right font-medium {{ $inv->balance_amount > 0 ? 'text-rose-600 font-bold' : 'text-gray-400' }}">
                                LKR {{ number_format($inv->balance_amount, 2) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($inv->balance_amount <= 0.0001 && $inv->grand_total > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                                        Paid in Full
                                    </span>
                                @elseif($inv->amount_paid > 0.0001)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700">
                                        Partially Paid
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700">
                                        Unpaid
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-gray-500 text-xs">
                                No sales invoices found for the selected dates and filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 font-bold border-t border-gray-200 text-gray-900">
                    <tr>
                        <td colspan="5" class="py-3 px-4 text-right uppercase tracking-wider text-xs">Total Recognized:</td>
                        <td class="py-3 px-4 text-right text-xs text-[#0B1849]">LKR {{ number_format($totalSales, 2) }}</td>
                        <td class="py-3 px-4 text-right text-xs text-emerald-700">LKR {{ number_format($totalCollected, 2) }}</td>
                        <td class="py-3 px-4 text-right text-xs text-rose-600">LKR {{ number_format($totalOutstanding, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
