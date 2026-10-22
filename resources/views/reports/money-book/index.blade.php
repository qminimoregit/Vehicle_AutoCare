@extends('layouts.app')

@section('title', 'Money Book')

@section('content')
<div class="space-y-6">
    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#0B1849] tracking-tight">Money Book</h1>
            <p class="text-sm text-gray-500 mt-1">Daily register of cash, card, bank collections, cheque tracking and credit sales.</p>
        </div>
        <div class="flex items-center gap-2">
            <a 
                href="{{ route('reports.money-book.print', request()->query()) }}" 
                target="_blank" 
                class="inline-flex items-center px-3.5 py-2 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition shadow-xs"
            >
                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Money Book
            </a>
            <a 
                href="{{ route('reports.money-book.export', request()->query()) }}" 
                class="inline-flex items-center px-3.5 py-2 rounded-lg bg-[#0B1849] text-xs font-semibold text-white hover:bg-[#0B1849]/90 transition shadow-xs"
            >
                <svg class="w-4 h-4 mr-1.5 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
        <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-[11px] font-medium text-gray-500 uppercase tracking-wider">Cash Received</span>
            <div class="text-base font-bold text-emerald-700 mt-1">LKR {{ number_format($cashReceived, 2) }}</div>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-[11px] font-medium text-gray-500 uppercase tracking-wider">Card Received</span>
            <div class="text-base font-bold text-blue-700 mt-1">LKR {{ number_format($cardReceived, 2) }}</div>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-[11px] font-medium text-gray-500 uppercase tracking-wider">Bank Transfer</span>
            <div class="text-base font-bold text-indigo-700 mt-1">LKR {{ number_format($bankTransferReceived, 2) }}</div>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-[11px] font-medium text-gray-500 uppercase tracking-wider">Cheques</span>
            <div class="text-base font-bold text-amber-700 mt-1">LKR {{ number_format($chequeReceived, 2) }}</div>
            <div class="text-[10px] text-gray-400 mt-0.5">Subject to clearance</div>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-[11px] font-medium text-gray-500 uppercase tracking-wider">Net Liquid Inflow</span>
            <div class="text-base font-bold text-[#0B1849] mt-1">LKR {{ number_format($totalLiquidInflow, 2) }}</div>
            <div class="text-[10px] text-gray-400 mt-0.5">Cash + Card + Bank</div>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-[11px] font-medium text-gray-500 uppercase tracking-wider">Credit Sales Created</span>
            <div class="text-base font-bold text-purple-700 mt-1">LKR {{ number_format($creditSalesCreated, 2) }}</div>
            <div class="text-[10px] text-gray-400 mt-0.5">Receivables added</div>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-[11px] font-medium text-gray-500 uppercase tracking-wider">Credit Collected</span>
            <div class="text-base font-bold text-teal-700 mt-1">LKR {{ number_format($creditPaymentsCollected, 2) }}</div>
            <div class="text-[10px] text-gray-400 mt-0.5">Towards credit bills</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
        <form method="GET" action="{{ route('reports.money-book.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end">
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

            <!-- Payment Method -->
            <div>
                <label for="payment_method" class="block text-xs font-semibold text-gray-600 mb-1">Payment Method</label>
                <select id="payment_method" name="payment_method" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]">
                    <option value="all" {{ $methodFilter === 'all' ? 'selected' : '' }}>All Payment Methods</option>
                    <option value="cash" {{ $methodFilter === 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="card" {{ $methodFilter === 'card' ? 'selected' : '' }}>Card (Credit/Debit)</option>
                    <option value="bank_transfer" {{ $methodFilter === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="cheque" {{ $methodFilter === 'cheque' ? 'selected' : '' }}>Cheque</option>
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
                    Filter
                </button>
                <a href="{{ route('reports.money-book.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                    Today
                </a>
            </div>
        </form>
    </div>

    <!-- Collections & Payments Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-[#0B1849]">Customer Collections & Cash Receipts</span>
            <span class="text-xs text-gray-500">{{ $payments->count() }} transactions</span>
        </div>
        <div class="responsive-table-wrapper">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase tracking-wider">
                        <th class="py-3 px-4">Payment Date</th>
                        <th class="py-3 px-4">Receipt / Pay #</th>
                        <th class="py-3 px-4">Invoice #</th>
                        <th class="py-3 px-4">Customer / Company</th>
                        <th class="py-3 px-4">Method</th>
                        <th class="py-3 px-4">Reference / Cheque</th>
                        <th class="py-3 px-4 text-right">Amount Received</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($payments as $p)
                        @php
                            $method = strtolower($p->payment_method ?? 'cash');
                            $isCheque = str_contains($method, 'cheque');
                            $isCard = str_contains($method, 'card');
                            $isBank = str_contains($method, 'bank') || str_contains($method, 'transfer');
                            $isCash = str_contains($method, 'cash');
                        @endphp
                        <tr class="hover:bg-slate-50 transition {{ $p->isReversed() ? 'opacity-50 line-through' : '' }}">
                            <td class="py-3 px-4 text-gray-600 whitespace-nowrap">
                                {{ $p->payment_date?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-[#0B1849]">
                                {{ $p->payment_number }}
                            </td>
                            <td class="py-3 px-4 font-mono">
                                @if($p->invoice)
                                    <a href="{{ route('operations.invoices.show', $p->invoice) }}" class="text-blue-600 hover:underline">
                                        {{ $p->invoice->invoice_number }}
                                    </a>
                                @elseif($p->directSale)
                                    <a href="{{ route('operations.direct-sales.bill', $p->directSale) }}" target="_blank" class="text-emerald-700 hover:underline font-semibold" title="Sales Bill">
                                        {{ $p->directSale->bill_number }} <span class="text-xs text-emerald-600 font-bold">(Bill)</span>
                                    </a>
                                @elseif($p->jobCard)
                                    <a href="{{ route('operations.job-cards.show', $p->jobCard) }}" class="text-indigo-600 hover:underline font-semibold" title="Advance payment for Job Card">
                                        {{ $p->jobCard->job_number }} <span class="text-xs text-amber-600 font-bold">(Advance)</span>
                                    </a>
                                @else
                                    <span class="text-gray-400">Multiple / Split</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-medium text-gray-900">
                                {{ $p->invoice?->customer?->display_name ?? ($p->directSale?->customer?->display_name ?? ($p->jobCard?->customer?->display_name ?? ($p->customer?->display_name ?? 'Guest'))) }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ $isCash ? 'bg-emerald-50 text-emerald-800' : ($isCard ? 'bg-blue-50 text-blue-800' : ($isBank ? 'bg-indigo-50 text-indigo-800' : 'bg-amber-50 text-amber-800')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $p->payment_method ?? 'cash')) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-600 font-mono">
                                {{ $p->reference_number ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-right font-bold {{ $p->isReversed() ? 'text-gray-400' : 'text-emerald-700' }}">
                                LKR {{ number_format($p->amount, 2) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($p->isReversed())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700">
                                        Reversed
                                    </span>
                                @elseif($isCheque)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700">
                                        Cheque Received
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                                        Cleared
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500 text-xs">
                                No payment receipts recorded on the selected dates.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Credit Sales Created Section -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="px-4 py-3 bg-purple-50/50 border-b border-purple-100 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-purple-900">New Credit Sales Created (Accounts Receivable)</span>
                <span class="text-xs text-purple-700 block mt-0.5">Invoices and Sales Bills issued during this period with unpaid or credit balances.</span>
            </div>
            <span class="text-xs font-bold text-purple-900">Total: LKR {{ number_format($creditSalesCreated, 2) }}</span>
        </div>
        <div class="responsive-table-wrapper">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase tracking-wider">
                        <th class="py-2.5 px-4">Date</th>
                        <th class="py-2.5 px-4">Invoice / Bill #</th>
                        <th class="py-2.5 px-4">Customer / Company</th>
                        <th class="py-2.5 px-4 text-right">Total</th>
                        <th class="py-2.5 px-4 text-right">Paid Now</th>
                        <th class="py-2.5 px-4 text-right">Credit Balance</th>
                        <th class="py-2.5 px-4">Branch</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($creditSales as $cs)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-2.5 px-4 text-gray-600">{{ $cs->invoice_date?->format('d M Y') }}</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-[#0B1849]">
                                @if(isset($cs->is_bill) && $cs->is_bill)
                                    <a href="{{ route('operations.direct-sales.bill', $cs->id) }}" target="_blank" class="hover:underline text-emerald-800" title="Sales Bill">
                                        {{ $cs->invoice_number }}
                                    </a>
                                @else
                                    <a href="{{ route('billing.invoices.show', $cs) }}" class="hover:underline">
                                        {{ $cs->invoice_number }}
                                    </a>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 font-medium text-gray-900">{{ $cs->customer?->display_name ?? 'Customer' }}</td>
                            <td class="py-2.5 px-4 text-right font-medium text-gray-800">LKR {{ number_format($cs->grand_total, 2) }}</td>
                            <td class="py-2.5 px-4 text-right font-medium text-emerald-700">LKR {{ number_format($cs->amount_paid, 2) }}</td>
                            <td class="py-2.5 px-4 text-right font-bold text-purple-700">LKR {{ number_format($cs->balance_amount, 2) }}</td>
                            <td class="py-2.5 px-4 text-gray-500">{{ $cs->branch?->branch_name ?? 'Main Branch' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-500 text-xs">
                                No new credit sales created during this period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Expense Payments & Outflows (Actual cash/bank spent) -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="text-sm font-bold text-gray-900">Expense Payments & Outflows</h3>
                <span class="text-xs text-gray-500">(Only settled expense payments; unpaid expenses excluded)</span>
            </div>
            <div class="text-xs font-bold text-rose-700">
                Total Outflow: LKR {{ number_format($totalExpenseOutflow, 2) }}
                <span class="font-normal text-gray-500 ml-1">(Cash: LKR {{ number_format($expenseCashOutflow, 2) }}, Bank: LKR {{ number_format($expenseBankOutflow, 2) }})</span>
            </div>
        </div>
        <div class="responsive-table-wrapper">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase tracking-wider">
                        <th class="py-2.5 px-4">Payment Date</th>
                        <th class="py-2.5 px-4">Payment #</th>
                        <th class="py-2.5 px-4">Expense # & Category</th>
                        <th class="py-2.5 px-4">Method</th>
                        <th class="py-2.5 px-4">Reference</th>
                        <th class="py-2.5 px-4 text-right">Amount Paid</th>
                        <th class="py-2.5 px-4">Branch</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($expensePayments as $ep)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-2.5 px-4 text-gray-600">{{ $ep->payment_date?->format('d M Y') }}</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-[#0B1849]">{{ $ep->payment_number }}</td>
                            <td class="py-2.5 px-4">
                                <span class="font-semibold text-gray-900">{{ $ep->expense?->category?->name ?? 'Operating Cost' }}</span>
                                <span class="block text-[11px] text-gray-400 font-mono">{{ $ep->expense?->expense_number }}</span>
                            </td>
                            <td class="py-2.5 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-800">
                                    {{ ucwords(str_replace('_', ' ', $ep->payment_method)) }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-gray-500 font-mono">{{ $ep->reference_number ?: '—' }}</td>
                            <td class="py-2.5 px-4 text-right font-bold text-rose-700">LKR {{ number_format($ep->amount, 2) }}</td>
                            <td class="py-2.5 px-4 text-gray-500">{{ $ep->branch?->name ?? 'Main Branch' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-500 text-xs">
                                No expense payments or outflows recorded during this period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
