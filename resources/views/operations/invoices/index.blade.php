@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="space-y-6">
    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Invoices</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Manage customer billing, payment collections, and official tax documents across branches.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            @if(auth()->user()->isAdmin())
                <a 
                    href="{{ route('operations.invoices.create') }}" 
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white font-bold text-sm rounded-lg shadow-xs transition min-h-[44px] cursor-pointer"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Direct Invoice (Admin)
                </a>
            @else
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                    <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Invoices are auto-generated from Job Cards, Direct POS, or Quotations
                </span>
            @endif
        </div>
    </div>

    <!-- Outstanding Balance & Summary Metric Banner -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Outstanding Balance</p>
                <h3 class="text-2xl font-black text-[#0B1849] mt-1 tracking-tight">
                    LKR {{ number_format($totalOutstanding, 2) }}
                </h3>
                <p class="text-xs text-gray-500 mt-1">Pending collection for active branch</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Unsettled Invoices</p>
                <h3 class="text-2xl font-black text-amber-600 mt-1 tracking-tight">
                    {{ $counts['issued'] + $counts['partially_paid'] }}
                </h3>
                <p class="text-xs text-gray-500 mt-1">{{ $counts['issued'] }} Issued • {{ $counts['partially_paid'] }} Partially Paid</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Settled Invoices</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-1 tracking-tight">
                    {{ $counts['paid'] }}
                </h3>
                <p class="text-xs text-gray-500 mt-1">Paid in full</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Quick Status Filter Tabs -->
    <div class="border-b border-gray-200 overflow-x-auto thin-scrollbar">
        <nav class="flex space-x-4 min-w-max">
            @php
                $statusTabs = [
                    'all' => ['label' => 'All Invoices', 'count' => $counts['all']],
                    'issued' => ['label' => 'Issued', 'count' => $counts['issued']],
                    'partially_paid' => ['label' => 'Partially Paid', 'count' => $counts['partially_paid']],
                    'paid' => ['label' => 'Paid in Full', 'count' => $counts['paid']],
                    'cancelled' => ['label' => 'Cancelled', 'count' => $counts['cancelled']],
                ];
            @endphp

            @foreach($statusTabs as $key => $tab)
                <a 
                    href="{{ route('operations.invoices.index', array_merge(request()->query(), ['status' => $key, 'page' => 1])) }}"
                    class="py-3 px-2 border-b-2 font-semibold text-xs sm:text-sm flex items-center space-x-2 transition {{ $status === $key ? 'border-[#0B1849] text-[#0B1849]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    <span>{{ $tab['label'] }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold {{ $status === $key ? 'bg-[#EEF1FA] text-[#0B1849]' : 'bg-gray-100 text-gray-600' }}">
                        {{ $tab['count'] }}
                    </span>
                </a>
            @endforeach
        </nav>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs">
        <form method="GET" action="{{ route('operations.invoices.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="hidden" name="status" value="{{ $status }}">
            
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Search by Invoice #, Customer Name, Phone, Vehicle Number, Ref..." 
                    class="block w-full pl-10 pr-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] placeholder:text-gray-400"
                />
            </div>

            <!-- Invoice Type Filter -->
            <div class="w-full sm:w-48">
                <select 
                    name="type" 
                    onchange="this.form.submit()"
                    class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                >
                    <option value="all" {{ $invoiceType === 'all' ? 'selected' : '' }}>All Invoice Types</option>
                    <option value="job_card" {{ $invoiceType === 'job_card' ? 'selected' : '' }}>Job Card Invoices</option>
                    <option value="direct_sale" {{ $invoiceType === 'direct_sale' ? 'selected' : '' }}>Direct POS Sales</option>
                    <option value="quotation" {{ $invoiceType === 'quotation' ? 'selected' : '' }}>From Quotation</option>
                    <option value="manual" {{ $invoiceType === 'manual' ? 'selected' : '' }}>Direct Manual</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button 
                    type="submit" 
                    class="px-5 py-2.5 text-sm font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg transition shadow-xs cursor-pointer focus:outline-none min-h-[44px]"
                >
                    Search
                </button>

                @if($search !== '' || $invoiceType !== 'all')
                    <a 
                        href="{{ route('operations.invoices.index', ['status' => $status]) }}" 
                        class="px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition min-h-[44px] flex items-center justify-center"
                    >
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Invoices Table Card -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
        <div class="responsive-table-wrapper">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/75">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Invoice #</th>
                        <th scope="col" class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date & Source</th>
                        <th scope="col" class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Customer / Company</th>
                        <th scope="col" class="px-5 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total (LKR)</th>
                        <th scope="col" class="px-5 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Paid / Balance</th>
                        <th scope="col" class="px-5 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-5 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <a href="{{ route('operations.invoices.show', $invoice) }}" class="text-sm font-bold text-[#0B1849] hover:underline">
                                    #{{ $invoice->invoice_number }}
                                </a>
                                @if($invoice->reference)
                                    <p class="text-xs text-gray-400 mt-0.5">Ref: {{ $invoice->reference }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-gray-600">
                                <div class="font-medium text-gray-900">{{ $invoice->invoice_date ? $invoice->invoice_date->format('M d, Y') : '' }}</div>
                                <div class="mt-1">
                                    @if($invoice->invoice_type === 'job_card' && $invoice->jobCard)
                                        <a href="{{ route('operations.job-cards.show', $invoice->jobCard) }}" class="inline-flex items-center text-[11px] font-semibold text-blue-700 hover:underline">
                                            <svg class="w-3 h-3 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            Job #{{ $invoice->jobCard->job_number }}
                                        </a>
                                    @elseif($invoice->invoice_type === 'quotation' && $invoice->quotation)
                                        <a href="{{ route('billing.quotations.show', $invoice->quotation) }}" class="inline-flex items-center text-[11px] font-semibold text-purple-700 hover:underline">
                                            <svg class="w-3 h-3 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                            </svg>
                                            Quote #{{ $invoice->quotation->quotation_number }}
                                        </a>
                                    @elseif($invoice->invoice_type === 'direct_sale')
                                        <span class="inline-flex items-center text-[11px] font-semibold text-emerald-700">
                                            <svg class="w-3 h-3 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                            POS Direct Sale
                                        </span>
                                    @else
                                        <span class="text-[11px] font-semibold text-slate-500">Direct Manual</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $invoice->customer->customer_name }}
                                </div>
                                @if($invoice->customer->customer_type === 'company' && $invoice->customer->company_name)
                                    <div class="text-xs text-blue-700 font-medium mt-0.5 flex items-center space-x-1">
                                        <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span>{{ $invoice->customer->company_name }}</span>
                                    </div>
                                @endif
                                <div class="text-xs text-gray-400 mt-0.5">{{ $invoice->customer->phone }}</div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-extrabold text-gray-900">
                                    LKR {{ number_format($invoice->grand_total, 2) }}
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right text-xs">
                                <div class="font-bold text-emerald-700">Paid: LKR {{ number_format($invoice->amount_paid, 2) }}</div>
                                <div class="font-semibold {{ $invoice->balance_amount > 0 ? 'text-red-600' : 'text-gray-400' }} mt-0.5">
                                    Due: LKR {{ number_format($invoice->balance_amount, 2) }}
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-center space-y-1">
                                <div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $invoice->status === 'cancelled' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                        Doc: {{ $invoice->document_status }}
                                    </span>
                                </div>
                                <div>
                                    @php $payStatus = $invoice->payment_status; @endphp
                                    @if($payStatus === 'Paid in Full')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Paid in Full
                                        </span>
                                    @elseif($payStatus === 'Partially Paid')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            Partially Paid
                                        </span>
                                    @elseif($payStatus === 'Cancelled')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                            Cancelled
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            Unpaid
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right text-xs font-medium space-x-1">
                                @if($invoice->status !== 'cancelled')
                                    @if($invoice->balance_amount > 0)
                                        <a 
                                            href="{{ route('operations.invoices.show', $invoice) }}?action=pay" 
                                            class="inline-flex items-center px-2.5 py-1.5 bg-[#0B1849] hover:bg-[#15286e] text-white rounded-md font-semibold transition shadow-xs"
                                            title="Record Payment"
                                        >
                                            Record Payment
                                        </a>
                                    @else
                                        <a 
                                            href="{{ route('operations.invoices.show', $invoice) }}#payment-history" 
                                            class="inline-flex items-center px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-md font-semibold transition"
                                            title="View Payments"
                                        >
                                            View Payments
                                        </a>
                                    @endif
                                @endif

                                <a 
                                    href="{{ route('operations.invoices.show', $invoice) }}" 
                                    class="inline-flex items-center px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md font-semibold transition"
                                    title="View Invoice"
                                >
                                    View
                                </a>
                                <a 
                                    href="{{ route('operations.invoices.thermal', $invoice) }}" 
                                    target="_blank"
                                    class="inline-flex items-center px-2 py-1.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-md transition"
                                    title="Print 80mm Receipt"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                </a>
                                <a 
                                    href="{{ route('operations.invoices.pdf', $invoice) }}" 
                                    class="inline-flex items-center px-2 py-1.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-md transition"
                                    title="Download PDF"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700">No invoices found</p>
                                    <p class="text-xs text-gray-400 mt-1">Try adjusting your search criteria or status filter.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="px-5 py-4 border-t border-gray-200 bg-gray-50/50">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
