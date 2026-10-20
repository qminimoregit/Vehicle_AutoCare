@extends('layouts.app')

@section('title', 'Monthly Statement - ' . $customer->display_name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#0B1849] tracking-tight">Monthly Company Statement</h1>
            <p class="text-sm text-gray-500 mt-1">Consolidated billing statement for {{ $customer->display_name }} across all corporate vehicles.</p>
        </div>
        <div class="flex items-center gap-2">
            <a 
                href="{{ route('reports.credit-management.statement.print', ['creditAccount' => $creditAccount->id, 'month' => $month, 'year' => $year]) }}" 
                target="_blank" 
                class="inline-flex items-center px-3.5 py-2 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition shadow-xs"
            >
                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Statement
            </a>
            <a 
                href="{{ route('reports.credit-management.statement.export', ['creditAccount' => $creditAccount->id, 'month' => $month, 'year' => $year]) }}" 
                class="inline-flex items-center px-3.5 py-2 rounded-lg bg-[#0B1849] text-xs font-semibold text-white hover:bg-[#0B1849]/90 transition shadow-xs"
            >
                <svg class="w-4 h-4 mr-1.5 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </a>
            <a href="{{ route('reports.credit-management.show', $creditAccount) }}" class="px-3 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                Back to Fleet
            </a>
        </div>
    </div>

    <!-- Month & Year Selector -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
        <form method="GET" action="{{ route('reports.credit-management.statement', $creditAccount) }}" class="flex flex-wrap items-end gap-3 text-xs">
            <div>
                <label for="month" class="block font-semibold text-gray-600 mb-1">Statement Month</label>
                <select id="month" name="month" class="text-xs rounded-lg border-gray-300">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ Carbon\Carbon::create(null, $m, 1)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="year" class="block font-semibold text-gray-600 mb-1">Year</label>
                <select id="year" name="year" class="text-xs rounded-lg border-gray-300">
                    @for($y = Carbon\Carbon::now()->year; $y >= Carbon\Carbon::now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-[#0B1849] text-white font-semibold rounded-lg hover:bg-[#0B1849]/90 transition">
                Generate Statement
            </button>
        </form>
    </div>

    <!-- Statement Paper Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-6 md:p-8 max-w-4xl mx-auto space-y-6">
        <!-- Statement Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 pb-5 gap-4">
            <div>
                <h2 class="text-xl font-bold text-[#0B1849]">AutoCare Service Center</h2>
                <div class="text-xs text-gray-500 mt-0.5">Corporate Credit & Fleet Billing Department</div>
                <div class="text-xs text-gray-500 mt-1">Statement Period: <span class="font-bold text-gray-800">{{ $statementStart->format('d M Y') }} – {{ $statementEnd->format('d M Y') }}</span></div>
            </div>
            <div class="text-left sm:text-right">
                <div class="text-xs text-gray-500 uppercase font-semibold">Account Statement For</div>
                <div class="text-base font-bold text-[#0B1849] mt-0.5">{{ $customer->display_name }}</div>
                <div class="text-xs text-gray-600">{{ $customer->address ?? 'Corporate Client' }}</div>
                <div class="text-xs text-gray-500">Contact: {{ $customer->phone }}</div>
            </div>
        </div>

        <!-- Account Movement Summary Strips -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-center">
            <div class="p-3 rounded-lg bg-gray-50 border border-gray-200">
                <span class="text-[11px] text-gray-500 uppercase font-semibold block">Opening Balance</span>
                <span class="text-base font-bold text-gray-800 mt-0.5 block">LKR {{ number_format($openingBalance, 2) }}</span>
            </div>
            <div class="p-3 rounded-lg bg-blue-50 border border-blue-100">
                <span class="text-[11px] text-blue-700 uppercase font-semibold block">+ Invoices This Month</span>
                <span class="text-base font-bold text-blue-900 mt-0.5 block">LKR {{ number_format($monthInvoicesTotal, 2) }}</span>
            </div>
            <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-100">
                <span class="text-[11px] text-emerald-700 uppercase font-semibold block">- Payments Received</span>
                <span class="text-base font-bold text-emerald-900 mt-0.5 block">LKR {{ number_format($monthPaymentsTotal, 2) }}</span>
            </div>
            <div class="p-3 rounded-lg bg-purple-50 border border-purple-200">
                <span class="text-[11px] text-purple-800 uppercase font-bold block">= Closing Due Balance</span>
                <span class="text-base font-bold text-purple-900 mt-0.5 block">LKR {{ number_format($closingBalance, 2) }}</span>
            </div>
        </div>

        <!-- Invoices During Month -->
        <div>
            <div class="text-xs font-bold uppercase tracking-wider text-[#0B1849] mb-2 flex justify-between">
                <span>Invoices Issued During {{ $statementStart->format('F Y') }} ({{ $monthInvoices->count() }})</span>
                <span>Total: LKR {{ number_format($monthInvoicesTotal, 2) }}</span>
            </div>
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold">
                        <tr>
                            <th class="py-2.5 px-3">Date</th>
                            <th class="py-2.5 px-3">Vehicle</th>
                            <th class="py-2.5 px-3">Invoice #</th>
                            <th class="py-2.5 px-3 text-right">Amount</th>
                            <th class="py-2.5 px-3 text-right">Paid</th>
                            <th class="py-2.5 px-3 text-right">Balance Due</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($monthInvoices as $inv)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2.5 px-3">{{ $inv->invoice_date?->format('d M Y') }}</td>
                                <td class="py-2.5 px-3 font-mono font-medium text-gray-900">
                                    {{ $inv->jobCard?->vehicle?->vehicle_number ?? 'Counter POS' }}
                                </td>
                                <td class="py-2.5 px-3 font-mono text-blue-600 font-medium">
                                    {{ $inv->invoice_number }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-medium">LKR {{ number_format($inv->grand_total, 2) }}</td>
                                <td class="py-2.5 px-3 text-right text-emerald-700">LKR {{ number_format($inv->amount_paid, 2) }}</td>
                                <td class="py-2.5 px-3 text-right font-bold {{ $inv->balance_amount > 0 ? 'text-rose-600' : 'text-gray-400' }}">
                                    LKR {{ number_format($inv->balance_amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500">No invoices issued during this month.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payments During Month -->
        <div>
            <div class="text-xs font-bold uppercase tracking-wider text-[#0B1849] mb-2 flex justify-between">
                <span>Payments Received During {{ $statementStart->format('F Y') }} ({{ $monthPayments->count() }})</span>
                <span>Total: LKR {{ number_format($monthPaymentsTotal, 2) }}</span>
            </div>
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold">
                        <tr>
                            <th class="py-2.5 px-3">Date</th>
                            <th class="py-2.5 px-3">Receipt / Pay #</th>
                            <th class="py-2.5 px-3">Invoice #</th>
                            <th class="py-2.5 px-3">Payment Method</th>
                            <th class="py-2.5 px-3">Reference</th>
                            <th class="py-2.5 px-3 text-right">Amount Settled</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($monthPayments as $p)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2.5 px-3">{{ $p->payment_date?->format('d M Y') }}</td>
                                <td class="py-2.5 px-3 font-mono font-bold text-[#0B1849]">{{ $p->payment_number }}</td>
                                <td class="py-2.5 px-3 font-mono text-gray-600">{{ $p->invoice?->invoice_number ?? 'Multi-Invoice' }}</td>
                                <td class="py-2.5 px-3 capitalize">{{ str_replace('_', ' ', $p->payment_method) }}</td>
                                <td class="py-2.5 px-3 font-mono text-gray-500">{{ $p->reference_number ?? '-' }}</td>
                                <td class="py-2.5 px-3 text-right font-bold text-emerald-700">LKR {{ number_format($p->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500">No payments received during this month.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Instructions & Stamp -->
        <div class="pt-6 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-end gap-6">
            <div class="text-xs text-gray-500 space-y-1">
                <p class="font-bold text-gray-700">Payment Instructions:</p>
                <p>Please settle the outstanding closing balance within your agreed {{ $creditAccount->payment_terms_days }}-day credit terms.</p>
                <p>Cheques should be drawn in favor of <strong class="text-gray-700">AutoCare Service Center</strong>.</p>
            </div>
            <div class="text-right">
                <div class="w-44 border-b border-gray-300 pb-8 text-center text-[11px] text-gray-400">
                    Accounts Department Stamp
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
