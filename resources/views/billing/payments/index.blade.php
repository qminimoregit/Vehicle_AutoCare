@extends('layouts.app')

@section('title', 'Invoice Payments - Sales & Billing')

@section('content')
<div 
    x-data="{ 
        showPaymentModal: false,
        activeInvoice: {
            id: null,
            number: '',
            customer: '',
            company: '',
            total: 0,
            paid: 0,
            balance: 0
        },
        paymentAmount: 0,
        paymentMethod: 'cash',
        paymentDate: '{{ date('Y-m-d') }}',
        referenceNumber: '',
        notes: '',
        submitting: false,

        openPaymentModal(inv) {
            this.activeInvoice = inv;
            this.paymentAmount = inv.balance;
            this.paymentMethod = 'cash';
            this.paymentDate = '{{ date('Y-m-d') }}';
            this.referenceNumber = '';
            this.notes = '';
            this.submitting = false;
            this.showPaymentModal = true;
        },

        get remainingBalance() {
            let amt = parseFloat(this.paymentAmount) || 0;
            let bal = parseFloat(this.activeInvoice.balance) || 0;
            let rem = bal - amt;
            return rem > 0 ? rem.toFixed(2) : '0.00';
        }
    }"
    class="space-y-6"
>
    <!-- Top Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                <span>Sales & Billing</span>
                <span>&bull;</span>
                <span class="text-[#0B1849]">Payments</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Invoice Payments</h1>
            <p class="text-xs sm:text-sm text-[#4A4A4A] mt-0.5">
                Record single and partial payments, view payment receipts, and manage outstanding balances.
            </p>
        </div>

        <div class="flex items-center space-x-2.5">
            <!-- Reuse Stage 6 Multiple Invoice Payment -->
            <a 
                href="{{ route('billing.multiple-invoice-payment.index') }}" 
                class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs sm:text-sm font-bold rounded-lg transition shadow-xs min-h-[44px]"
                title="Pay multiple invoices or split across multiple payment methods"
            >
                <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Multiple Invoice Payment
            </a>
        </div>
    </div>

    <!-- Instant Payment Receipt & Updated Bill Print Alert -->
    @if(session('recent_payment_id'))
        @php
            $recentPayment = \App\Models\Payment::with(['invoice.jobCard'])->find(session('recent_payment_id'));
        @endphp
        @if($recentPayment)
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs sm:text-sm text-emerald-900 shadow-xs">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Payment of <strong>LKR {{ number_format($recentPayment->amount, 2) }}</strong> recorded successfully (Receipt #{{ $recentPayment->payment_number }}).
                        @if($recentPayment->invoice)
                            New Balance: <strong>LKR {{ number_format($recentPayment->invoice->balance_amount, 2) }}</strong>
                        @endif
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <a 
                        href="{{ route('billing.payments.thermal', $recentPayment) }}" 
                        @click.prevent="AutoCarePrinter.printUrl('{{ route('billing.payments.thermal', $recentPayment) }}', { type: 'payment_receipt', title: 'Payment Receipt #{{ $recentPayment->payment_number }}', documentId: {{ $recentPayment->id }}, documentNumber: '{{ $recentPayment->payment_number }}', fallbackUrl: '{{ route('billing.payments.thermal', $recentPayment) }}' })"
                        class="inline-flex items-center px-3.5 py-1.5 bg-white border border-emerald-300 text-emerald-800 hover:bg-emerald-100 font-bold text-xs rounded-lg shadow-2xs transition"
                        title="Print 80mm Payment Receipt"
                    >
                        <svg class="w-4 h-4 mr-1.5 text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Receipt
                    </a>
                    @if($recentPayment->invoice?->job_card_id)
                        <a 
                            href="{{ route('operations.job-cards.print-bill', $recentPayment->invoice->job_card_id) }}" 
                            @click.prevent="AutoCarePrinter.printUrl('{{ route('operations.job-cards.print-bill', $recentPayment->invoice->job_card_id) }}', { type: 'bill', title: 'Sales Bill #{{ $recentPayment->invoice->jobCard?->job_number }}', documentId: {{ $recentPayment->invoice->job_card_id }}, documentNumber: '{{ $recentPayment->invoice->jobCard?->job_number }}', fallbackUrl: '{{ route('operations.job-cards.print-bill', $recentPayment->invoice->job_card_id) }}' })"
                            class="inline-flex items-center px-3.5 py-1.5 bg-[#0B1849] text-white hover:bg-[#15286e] font-bold text-xs rounded-lg shadow-2xs transition"
                            title="Print Updated 80mm Sales Bill"
                        >
                            <svg class="w-4 h-4 mr-1.5 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Print Updated Bill
                        </a>
                    @endif
                </div>
            </div>
        @endif
    @endif

    <!-- Stats Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs">
            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Total Outstanding</span>
            <div class="text-xl sm:text-2xl font-black text-rose-600 mt-1">
                LKR {{ number_format($totalOutstanding, 2) }}
            </div>
            <span class="text-[11px] text-gray-500 mt-0.5 block">Across all pending invoices</span>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs">
            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Unpaid Invoices</span>
            <div class="text-xl sm:text-2xl font-black text-gray-900 mt-1">
                {{ number_format($counts['unpaid']) }}
            </div>
            <span class="text-[11px] text-rose-600 font-semibold mt-0.5 block">Awaiting first payment</span>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs">
            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Partially Paid</span>
            <div class="text-xl sm:text-2xl font-black text-amber-600 mt-1">
                {{ number_format($counts['partially_paid']) }}
            </div>
            <span class="text-[11px] text-gray-500 mt-0.5 block">Has remaining balance</span>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs">
            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Fully Settled</span>
            <div class="text-xl sm:text-2xl font-black text-emerald-600 mt-1">
                {{ number_format($counts['paid']) }}
            </div>
            <span class="text-[11px] text-emerald-700 font-semibold mt-0.5 block">Total Paid: LKR {{ number_format($totalCollected, 2) }}</span>
        </div>
    </div>

    <!-- Filter Tabs & Search -->
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs space-y-4">
        <!-- Status Filter Tabs -->
        <div class="flex items-center space-x-1 border-b border-gray-200 pb-3 overflow-x-auto">
            <a 
                href="{{ route('billing.payments.index', ['status' => 'all', 'search' => $search]) }}"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap {{ $status === 'all' ? 'bg-[#0B1849] text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100' }}"
            >
                All Invoices ({{ $counts['all'] }})
            </a>
            <a 
                href="{{ route('billing.payments.index', ['status' => 'unpaid', 'search' => $search]) }}"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap {{ $status === 'unpaid' ? 'bg-[#0B1849] text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100' }}"
            >
                Unpaid ({{ $counts['unpaid'] }})
            </a>
            <a 
                href="{{ route('billing.payments.index', ['status' => 'partially_paid', 'search' => $search]) }}"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap {{ $status === 'partially_paid' ? 'bg-[#0B1849] text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100' }}"
            >
                Partially Paid ({{ $counts['partially_paid'] }})
            </a>
            <a 
                href="{{ route('billing.payments.index', ['status' => 'paid', 'search' => $search]) }}"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap {{ $status === 'paid' ? 'bg-[#0B1849] text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100' }}"
            >
                Paid in Full ({{ $counts['paid'] }})
            </a>
        </div>

        <!-- Search Input -->
        <form method="GET" action="{{ route('billing.payments.index') }}" class="flex flex-col sm:flex-row gap-3">
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
                    placeholder="Search invoice number, customer name, phone, or company..." 
                    class="block w-full pl-10 pr-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] placeholder:text-gray-400 min-h-[44px]"
                />
            </div>

            <div class="flex items-center space-x-2">
                <button 
                    type="submit" 
                    class="px-5 py-2.5 text-sm font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg transition shadow-xs cursor-pointer min-h-[44px]"
                >
                    Search
                </button>

                @if($search !== '')
                    <a 
                        href="{{ route('billing.payments.index', ['status' => $status]) }}" 
                        class="px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition min-h-[44px] flex items-center justify-center"
                    >
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
        <div class="responsive-table-wrapper">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/75">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Invoice #</th>
                        <th scope="col" class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Customer</th>
                        <th scope="col" class="px-5 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total (LKR)</th>
                        <th scope="col" class="px-5 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Paid (LKR)</th>
                        <th scope="col" class="px-5 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Balance (LKR)</th>
                        <th scope="col" class="px-5 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-5 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($invoices as $inv)
                        @php
                            $bal = (float) $inv->balance_amount;
                            $paid = (float) $inv->amount_paid;
                            $total = (float) $inv->grand_total;
                            $statusLabel = $inv->payment_status;
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- Invoice # & Meta -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <a 
                                    href="{{ route('operations.invoices.show', $inv) }}" 
                                    class="font-mono text-sm font-bold text-[#0B1849] hover:underline"
                                >
                                    {{ $inv->invoice_number }}
                                </a>
                                <div class="text-[11px] text-gray-400 mt-0.5">
                                    {{ $inv->invoice_date ? $inv->invoice_date->format('d M Y') : 'N/A' }}
                                    @if($inv->invoice_type === 'job_card')
                                        &bull; Job Card
                                    @elseif($inv->invoice_type === 'direct_sale')
                                        &bull; POS Sale
                                    @elseif($inv->invoice_type === 'quotation')
                                        &bull; Quotation
                                    @else
                                        &bull; Manual
                                    @endif
                                </div>
                            </td>

                            <!-- Customer / Company -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $inv->customer->customer_name }}
                                </div>
                                @if($inv->customer->customer_type === 'company' && $inv->customer->company_name)
                                    <div class="text-xs text-blue-700 font-medium">
                                        {{ $inv->customer->company_name }}
                                    </div>
                                @endif
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ $inv->customer->phone ?? 'N/A' }}
                                </div>
                            </td>

                            <!-- Total -->
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-extrabold text-gray-900">
                                    {{ number_format($total, 2) }}
                                </span>
                            </td>

                            <!-- Paid -->
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-bold text-emerald-700">
                                    {{ number_format($paid, 2) }}
                                </span>
                            </td>

                            <!-- Balance -->
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-extrabold {{ $bal > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                    {{ number_format($bal, 2) }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                @if($statusLabel === 'Paid in Full')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Paid in Full
                                    </span>
                                @elseif($statusLabel === 'Partially Paid')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        Partially Paid
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        Unpaid
                                    </span>
                                @endif
                            </td>

                            <!-- Action -->
                            <td class="px-5 py-4 whitespace-nowrap text-right text-xs font-semibold space-x-1.5">
                                @if($bal > 0)
                                    <button 
                                        type="button" 
                                        @click="openPaymentModal({
                                            id: {{ $inv->id }},
                                            number: '{{ $inv->invoice_number }}',
                                            customer: '{{ addslashes($inv->customer->customer_name) }}',
                                            company: '{{ addslashes($inv->customer->company_name ?? '') }}',
                                            total: {{ $total }},
                                            paid: {{ $paid }},
                                            balance: {{ $bal }}
                                        })"
                                        class="inline-flex items-center px-3 py-1.5 bg-[#0B1849] hover:bg-[#15286e] text-white rounded-lg transition shadow-xs cursor-pointer min-h-[36px]"
                                    >
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Record Payment
                                    </button>
                                @else
                                    <a 
                                        href="{{ route('operations.invoices.show', $inv) }}#payment-history" 
                                        class="inline-flex items-center px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition min-h-[36px]"
                                    >
                                        View Payments
                                    </a>
                                @endif

                                @if($inv->job_card_id)
                                    <a 
                                        href="{{ route('operations.job-cards.print-bill', $inv->job_card_id) }}" 
                                        @click.prevent="AutoCarePrinter.printUrl('{{ route('operations.job-cards.print-bill', $inv->job_card_id) }}', { type: 'bill', title: 'Sales Bill #{{ $inv->jobCard?->job_number }}', documentId: {{ $inv->job_card_id }}, documentNumber: '{{ $inv->jobCard?->job_number }}', fallbackUrl: '{{ route('operations.job-cards.print-bill', $inv->job_card_id) }}' })"
                                        class="inline-flex items-center px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg transition min-h-[36px]"
                                        title="Print 80mm Sales Bill"
                                    >
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        Print Bill
                                    </a>
                                @endif

                                <a 
                                    href="{{ route('operations.invoices.show', $inv) }}" 
                                    class="inline-flex items-center px-2 py-1.5 text-gray-500 hover:text-gray-900 transition" 
                                    title="View Invoice Details"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm font-semibold text-gray-600">No invoices found matching criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    <!-- Recent Payment Transactions History Table -->
    @if($recentPayments->count() > 0)
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs space-y-3">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Recent Payment Transactions</h3>
                    <p class="text-xs text-gray-500">Latest completed payment receipts recorded across invoices.</p>
                </div>
            </div>

            <div class="responsive-table-wrapper">
                <table class="min-w-full divide-y divide-gray-100 text-xs">
                    <thead class="bg-slate-50 text-gray-500 font-semibold uppercase text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3 text-left">Receipt / Ref #</th>
                            <th class="py-2.5 px-3 text-left">Date</th>
                            <th class="py-2.5 px-3 text-left">Invoice #</th>
                            <th class="py-2.5 px-3 text-left">Customer</th>
                            <th class="py-2.5 px-3 text-left">Method</th>
                            <th class="py-2.5 px-3 text-right">Amount (LKR)</th>
                            <th class="py-2.5 px-3 text-right">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-gray-700">
                        @foreach($recentPayments as $rp)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-2.5 px-3 font-mono font-bold text-gray-900">{{ $rp->payment_number }}</td>
                                <td class="py-2.5 px-3 text-gray-500">{{ $rp->payment_date ? $rp->payment_date->format('d M Y') : '—' }}</td>
                                <td class="py-2.5 px-3">
                                    <a href="{{ route('operations.invoices.show', $rp->invoice_id) }}" class="font-mono text-[#0B1849] hover:underline font-bold">
                                        {{ $rp->invoice->invoice_number ?? 'N/A' }}
                                    </a>
                                </td>
                                <td class="py-2.5 px-3 font-medium text-gray-800">
                                    {{ $rp->invoice->customer->customer_name ?? 'N/A' }}
                                </td>
                                <td class="py-2.5 px-3 uppercase font-semibold text-[11px] text-gray-600">
                                    {{ str_replace('_', ' ', $rp->payment_method) }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-bold text-emerald-700">
                                    {{ number_format($rp->amount, 2) }}
                                </td>
                                <td class="py-2.5 px-3 text-right">
                                    <a 
                                        href="{{ route('billing.payments.receipt', $rp) }}" 
                                        target="_blank"
                                        class="inline-flex items-center px-2 py-1 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded text-[11px] font-semibold transition"
                                    >
                                        <svg class="w-3 h-3 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        Receipt
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- MODAL: RECORD PAYMENT -->
    <div 
        x-show="showPaymentModal" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" @click="showPaymentModal = false"></div>

            <div class="relative bg-white rounded-2xl w-full max-w-[calc(100vw-2rem)] sm:max-w-md p-4 sm:p-6 shadow-xl border border-gray-200 space-y-4 max-h-[88vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Record Invoice Payment</h3>
                        <p class="text-xs text-gray-500">Record full or partial settlement against this invoice.</p>
                    </div>
                    <button type="button" @click="showPaymentModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Invoice Meta Overview Card -->
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 space-y-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 font-medium">Invoice Number:</span>
                        <span class="font-mono font-bold text-[#0B1849]" x-text="activeInvoice.number"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 font-medium">Customer:</span>
                        <span class="font-bold text-gray-900 truncate max-w-[200px]" x-text="activeInvoice.customer"></span>
                    </div>
                    <div class="border-t border-slate-200/60 pt-2 grid grid-cols-3 gap-2 text-center">
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase block">Total</span>
                            <span class="font-extrabold text-gray-800" x-text="'LKR ' + Number(activeInvoice.total).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase block">Already Paid</span>
                            <span class="font-bold text-emerald-700" x-text="'LKR ' + Number(activeInvoice.paid).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase block">Outstanding</span>
                            <span class="font-extrabold text-red-600" x-text="'LKR ' + Number(activeInvoice.balance).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <form 
                    :action="'/billing/invoices/' + activeInvoice.id + '/payments'" 
                    method="POST" 
                    class="space-y-4"
                    @submit="submitting = true"
                >
                    @csrf

                    <!-- Payment Amount -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Payment Amount (LKR) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="amount" 
                            x-model="paymentAmount"
                            :max="activeInvoice.balance"
                            min="0.01"
                            class="block w-full py-2.5 px-3 text-base font-bold text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white min-h-[44px]"
                            required
                        >
                        <!-- Live Calculation Preview -->
                        <div class="mt-1.5 flex items-center justify-between text-xs">
                            <span class="text-gray-500">Remaining After Payment:</span>
                            <span 
                                class="font-extrabold"
                                :class="remainingBalance == '0.00' ? 'text-emerald-700' : 'text-amber-700'"
                                x-text="'LKR ' + Number(remainingBalance).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"
                            ></span>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Payment Method <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="payment_method" 
                            x-model="paymentMethod"
                            class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white min-h-[44px]"
                            required
                        >
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="credit">Company Credit (Authorized Account)</option>
                        </select>
                    </div>

                    <!-- Payment Date -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Payment Date <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="payment_date" 
                            x-model="paymentDate"
                            max="{{ date('Y-m-d') }}"
                            class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white min-h-[44px]"
                            required
                        >
                    </div>

                    <!-- Payment Reference -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Payment Reference (Optional)
                        </label>
                        <input 
                            type="text" 
                            name="reference_number" 
                            x-model="referenceNumber"
                            placeholder="e.g. Card slip #, Bank transfer ref, Cheque #"
                            class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white min-h-[44px]"
                        >
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Notes (Optional)
                        </label>
                        <textarea 
                            name="notes" 
                            x-model="notes"
                            rows="2"
                            placeholder="Additional payment details..."
                            class="block w-full py-2 px-3 text-xs text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                        ></textarea>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-3 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="showPaymentModal = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition min-h-[40px] cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            :disabled="submitting || paymentAmount <= 0 || paymentAmount > activeInvoice.balance"
                            class="px-5 py-2.5 bg-[#0B1849] hover:bg-[#15286e] disabled:opacity-50 text-white text-xs font-bold rounded-lg shadow-xs transition min-h-[44px] cursor-pointer flex items-center"
                        >
                            <svg x-show="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            Save Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
