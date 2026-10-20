@extends('layouts.app')

@section('title', 'Invoice #' . $invoice->invoice_number)

@section('content')
<div 
    x-data="{ 
        showPaymentModal: {{ (request()->query('action') === 'pay' && $invoice->balance_amount > 0 && $invoice->status !== 'cancelled') ? 'true' : 'false' }},
        paymentAmount: '{{ (float)$invoice->balance_amount }}',
        paymentMethod: 'cash',
        paymentDate: '{{ date('Y-m-d') }}',
        refNumber: '',
        notes: '',
        submitting: false,
        showReverseModal: false,
        reversingPaymentId: null,
        reversingPaymentNumber: '',
        reversalReason: '',

        get remainingBalance() {
            let amt = parseFloat(this.paymentAmount) || 0;
            let bal = {{ (float)$invoice->balance_amount }};
            let rem = bal - amt;
            return rem > 0 ? rem.toFixed(2) : '0.00';
        },

        openReverse(payId, payNum) {
            this.reversingPaymentId = payId;
            this.reversingPaymentNumber = payNum;
            this.reversalReason = '';
            this.showReverseModal = true;
        }
    }"
    class="max-w-4xl mx-auto space-y-6"
>
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('operations.invoices.index') }}" class="text-gray-400 hover:text-gray-600 transition" title="Back to Invoices">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">
                    Invoice #{{ $invoice->invoice_number }}
                </h1>
                
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $invoice->status === 'cancelled' ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                        Document: {{ $invoice->document_status }}
                    </span>
                    @php $payStatus = $invoice->payment_status; @endphp
                    @if($payStatus === 'Paid in Full')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            Payment: Paid in Full
                        </span>
                    @elseif($payStatus === 'Partially Paid')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            Payment: Partially Paid
                        </span>
                    @elseif($payStatus === 'Cancelled')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                            Payment: Cancelled
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                            Payment: Unpaid
                        </span>
                    @endif
                </div>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1 ml-8">
                Issued on {{ $invoice->invoice_date ? $invoice->invoice_date->format('M d, Y') : '' }}
                @if($invoice->due_date)
                    • Due: {{ $invoice->due_date->format('M d, Y') }}
                @endif
                @if($invoice->jobCard)
                    • Linked to <a href="{{ route('operations.job-cards.show', $invoice->jobCard) }}" class="text-blue-600 hover:underline font-semibold">Job Card #{{ $invoice->jobCard->job_number }}</a>
                @elseif($invoice->quotation)
                    • Converted from <a href="{{ route('billing.quotations.show', $invoice->quotation) }}" class="text-purple-600 hover:underline font-semibold">Quotation #{{ $invoice->quotation->quotation_number }}</a>
                @elseif($invoice->invoice_type === 'direct_sale')
                    • Direct POS Sale
                @else
                    • Direct Invoice
                @endif
            </p>
        </div>

        <div class="flex items-center space-x-2.5 w-full sm:w-auto">
            <a 
                href="{{ route('operations.invoices.print', $invoice) }}" 
                target="_blank"
                class="inline-flex items-center justify-center px-3.5 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold text-xs rounded-lg hover:bg-gray-50 shadow-xs transition min-h-[44px]"
            >
                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </a>

            <a 
                href="{{ route('operations.invoices.pdf', $invoice) }}" 
                class="inline-flex items-center justify-center px-3.5 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold text-xs rounded-lg hover:bg-gray-50 shadow-xs transition min-h-[44px]"
            >
                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                PDF
            </a>

            <a 
                href="#payment-history" 
                class="inline-flex items-center justify-center px-3.5 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold text-xs rounded-lg hover:bg-gray-50 shadow-xs transition min-h-[44px]"
            >
                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Payment History
            </a>

            @if($invoice->balance_amount > 0 && $invoice->status !== 'cancelled')
                <button 
                    type="button" 
                    @click="showPaymentModal = true"
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white font-bold text-sm rounded-lg shadow-xs transition cursor-pointer min-h-[44px]"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Record Payment
                </button>
            @elseif($invoice->status !== 'cancelled' && $invoice->grand_total > 0)
                <span class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold text-xs rounded-lg shadow-xs min-h-[44px]">
                    <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Paid in Full
                </span>
            @endif
        </div>
    </div>


    <!-- Official Invoice Document Sheet -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-xs overflow-hidden p-6 sm:p-10 space-y-8">
        <!-- Invoice Header: Company & Meta -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-6 border-b border-gray-100 pb-8">
            <div>
                <h2 class="text-xl font-black text-[#0B1849] tracking-tight">{{ $company->company_name }}</h2>
                <p class="text-xs text-gray-500 mt-1 max-w-sm leading-relaxed">{{ $company->address }}</p>
                <div class="mt-2 text-xs text-gray-600 space-y-0.5">
                    @if($company->phone)
                        <div><strong>Phone:</strong> {{ $company->phone }}</div>
                    @endif
                    @if($company->email)
                        <div><strong>Email:</strong> {{ $company->email }}</div>
                    @endif
                    @if($company->tax_number)
                        <div><strong>Tax / VAT:</strong> {{ $company->tax_number }}</div>
                    @endif
                </div>
            </div>

            <div class="sm:text-right space-y-1">
                <span class="text-2xl font-black text-gray-900 block tracking-tight">INVOICE</span>
                <span class="text-xs font-mono font-bold text-[#0B1849] block">{{ $invoice->invoice_number }}</span>
                <div class="text-xs text-gray-500 pt-1">
                    <div><strong>Date:</strong> {{ $invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : '' }}</div>
                    @if($invoice->reference)
                        <div><strong>Ref:</strong> {{ $invoice->reference }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bill To / Customer & Vehicle Info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-50/70 p-5 rounded-xl border border-gray-100 text-xs sm:text-sm">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Billed To</span>
                <h3 class="font-bold text-gray-900 text-base">{{ $invoice->customer->customer_name }}</h3>
                @if($invoice->customer->phone)
                    <p class="text-gray-600 mt-0.5"><strong>Phone:</strong> {{ $invoice->customer->phone }}</p>
                @endif
                @if($invoice->customer->address)
                    <p class="text-gray-500 mt-0.5">{{ $invoice->customer->address }}</p>
                @endif
            </div>

            @if($invoice->jobCard)
                <div class="sm:text-right">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Service Details</span>
                    <div class="font-bold text-gray-900 text-base tracking-wide">{{ $invoice->jobCard->vehicle->vehicle_number }}</div>
                    <div class="text-xs text-gray-600 mt-0.5">
                        {{ $invoice->jobCard->vehicleType->name }}
                        @if($invoice->jobCard->current_mileage > 0)
                            • {{ number_format($invoice->jobCard->current_mileage) }} km
                        @endif
                    </div>
                    @if($invoice->jobCard->next_service_mileage > 0)
                        <div class="text-[11px] text-[#0B1849] font-semibold mt-1">
                            Next Service Due: {{ number_format($invoice->jobCard->next_service_mileage) }} km
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-xs sm:text-sm">
                <thead class="bg-gray-50 text-gray-600 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="py-3 px-3">Description</th>
                        <th class="py-3 px-3">Type</th>
                        <th class="py-3 px-3 text-right">Price</th>
                        <th class="py-3 px-3 text-right">Qty</th>
                        <th class="py-3 px-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-800">
                    @foreach($invoice->items as $item)
                        <tr>
                            <td class="py-3 px-3">
                                <span class="font-bold text-gray-900 block">{{ $item->item_name }}</span>
                                @if($item->item_code)
                                    <span class="text-[11px] text-gray-500 font-mono">{{ $item->item_code }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $item->item_type === 'service' ? 'bg-blue-50 text-blue-700' : 'bg-emerald-50 text-emerald-700' }}">
                                    {{ ucfirst($item->item_type) }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-right font-medium text-gray-700 whitespace-nowrap">
                                LKR {{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="py-3 px-3 text-right font-medium text-gray-600">
                                {{ (float) $item->quantity }}
                            </td>
                            <td class="py-3 px-3 text-right font-bold text-gray-900 whitespace-nowrap">
                                LKR {{ number_format($item->line_total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals & Balances -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-6 pt-4 border-t border-gray-100">
            <!-- Left: Words, Notes, Terms -->
            <div class="flex-1 space-y-4 max-w-md">
                @if(!empty($amountInWords))
                    <div class="bg-slate-50 border-l-4 border-[#0B1849] p-3 rounded-r-lg text-xs text-gray-700">
                        <span class="font-bold text-gray-900 block text-[10px] uppercase tracking-wider mb-0.5">Amount in Words</span>
                        {{ $amountInWords }}
                    </div>
                @endif

                @if($invoice->notes)
                    <div class="bg-gray-50/70 border border-gray-200/80 p-3 rounded-lg text-xs">
                        <span class="font-bold text-gray-700 block text-[10px] uppercase tracking-wider mb-1">Notes / Instructions</span>
                        <p class="text-gray-600 whitespace-pre-line">{{ $invoice->notes }}</p>
                    </div>
                @endif

                @if($invoice->terms_conditions)
                    <div class="bg-gray-50/70 border border-gray-200/80 p-3 rounded-lg text-xs">
                        <span class="font-bold text-gray-700 block text-[10px] uppercase tracking-wider mb-1">Terms & Conditions</span>
                        <p class="text-gray-600 whitespace-pre-line">{{ $invoice->terms_conditions }}</p>
                    </div>
                @endif
            </div>

            <!-- Right: Calculation summary -->
            <div class="w-full sm:w-80 space-y-2 text-xs sm:text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal:</span>
                    <span class="font-semibold text-gray-900">LKR {{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                @if($invoice->discount_amount > 0)
                    <div class="flex justify-between text-emerald-700">
                        <span>Discount:</span>
                        <span class="font-semibold">- LKR {{ number_format($invoice->discount_amount, 2) }}</span>
                    </div>
                @endif
                @if($invoice->tax_amount > 0)
                    <div class="flex justify-between text-gray-600">
                        <span>Tax / VAT ({{ (float)$invoice->tax_percentage }}%):</span>
                        <span class="font-semibold">LKR {{ number_format($invoice->tax_amount, 2) }}</span>
                    </div>
                @endif
                @if($invoice->additional_charges > 0)
                    <div class="flex justify-between text-gray-600">
                        <span>Additional Charges:</span>
                        <span class="font-semibold">LKR {{ number_format($invoice->additional_charges, 2) }}</span>
                    </div>
                @endif
                <div class="pt-2 border-t border-gray-200 flex justify-between text-base font-black text-[#0B1849]">
                    <span>Grand Total:</span>
                    <span>LKR {{ number_format($invoice->grand_total, 2) }}</span>
                </div>
                <div class="flex justify-between text-emerald-700 font-bold">
                    <span>Amount Paid:</span>
                    <span>LKR {{ number_format($invoice->amount_paid, 2) }}</span>
                </div>
                <div class="pt-1 border-t border-gray-200 flex justify-between text-sm font-extrabold {{ $invoice->balance_amount > 0 ? 'text-red-600' : 'text-gray-500' }}">
                    <span>Balance Due:</span>
                    <span>LKR {{ number_format($invoice->balance_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Payment History Section -->
        <div id="payment-history" class="pt-6 border-t border-gray-100 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500">Payment History</h4>
                    <p class="text-xs text-gray-400">All customer payment installments and transaction receipts linked to this invoice.</p>
                </div>

                @if($invoice->balance_amount > 0 && $invoice->status !== 'cancelled')
                    <button 
                        type="button" 
                        @click="showPaymentModal = true"
                        class="inline-flex items-center px-3 py-1.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-bold rounded-lg shadow-xs transition cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Record Payment
                    </button>
                @endif
            </div>

            @if($invoice->payments->count() > 0)
                <div class="overflow-x-auto border border-gray-200/80 rounded-xl">
                    <table class="min-w-full divide-y divide-gray-100 text-xs">
                        <thead class="bg-slate-50 text-gray-500 font-semibold uppercase text-[10px]">
                            <tr>
                                <th class="py-2.5 px-3 text-left">Date</th>
                                <th class="py-2.5 px-3 text-left">Receipt No</th>
                                <th class="py-2.5 px-3 text-left">Method</th>
                                <th class="py-2.5 px-3 text-left">Reference</th>
                                <th class="py-2.5 px-3 text-right">Amount (LKR)</th>
                                <th class="py-2.5 px-3 text-left">Recorded By</th>
                                <th class="py-2.5 px-3 text-center">Status</th>
                                <th class="py-2.5 px-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-700 bg-white">
                            @foreach($invoice->payments as $pay)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-2.5 px-3 text-gray-600 whitespace-nowrap">
                                        {{ $pay->payment_date ? $pay->payment_date->format('d M Y') : '—' }}
                                    </td>
                                    <td class="py-2.5 px-3 font-mono font-bold text-gray-900 whitespace-nowrap">
                                        {{ $pay->payment_number }}
                                    </td>
                                    <td class="py-2.5 px-3 uppercase font-semibold text-[11px] text-gray-600 whitespace-nowrap">
                                        {{ str_replace('_', ' ', $pay->payment_method) }}
                                    </td>
                                    <td class="py-2.5 px-3 text-gray-500 font-mono text-[11px]">
                                        {{ $pay->reference_number ?: '—' }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-bold {{ $pay->status === 'reversed' ? 'text-gray-400 line-through' : 'text-emerald-700' }} whitespace-nowrap">
                                        {{ number_format($pay->amount, 2) }}
                                    </td>
                                    <td class="py-2.5 px-3 text-gray-500 whitespace-nowrap">
                                        {{ $pay->creator->name ?? 'Staff' }}
                                    </td>
                                    <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                        @if($pay->status === 'reversed')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                Reversed
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Completed
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 text-right whitespace-nowrap space-x-1">
                                        <a 
                                            href="{{ route('billing.payments.receipt', $pay) }}" 
                                            target="_blank"
                                            class="inline-flex items-center px-2 py-1 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded text-[11px] font-semibold transition"
                                            title="Print Payment Receipt"
                                        >
                                            <svg class="w-3 h-3 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                            Receipt
                                        </a>

                                        @if(Auth::user() && Auth::user()->isAdmin() && $pay->status !== 'reversed')
                                            <button 
                                                type="button" 
                                                @click="openReverse({{ $pay->id }}, '{{ $pay->payment_number }}')"
                                                class="inline-flex items-center px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded text-[11px] font-semibold transition cursor-pointer"
                                                title="Reverse Payment (Admin Only)"
                                            >
                                                Reverse
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 border-t border-gray-200 text-xs">
                            <tr>
                                <td colspan="4" class="py-2.5 px-3 font-bold text-gray-700 text-right uppercase text-[10px] tracking-wider">
                                    Total Paid:
                                </td>
                                <td class="py-2.5 px-3 text-right font-extrabold text-emerald-700 whitespace-nowrap">
                                    LKR {{ number_format($invoice->amount_paid, 2) }}
                                </td>
                                <td colspan="3" class="py-2.5 px-3 text-right font-extrabold {{ $invoice->balance_amount > 0 ? 'text-red-600' : 'text-gray-400' }} whitespace-nowrap">
                                    Balance Due: LKR {{ number_format($invoice->balance_amount, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="bg-gray-50/60 rounded-xl p-4 text-center border border-gray-200/60 text-xs text-gray-500">
                    No payments recorded yet for this invoice.
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL: RECORD PAYMENT -->
    <div 
        x-show="showPaymentModal" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" @click="showPaymentModal = false"></div>

            <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-200 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Record Payment</h3>
                        <p class="text-xs text-gray-500">Invoice #{{ $invoice->invoice_number }} &bull; {{ $invoice->customer->customer_name }}</p>
                    </div>
                    <button type="button" @click="showPaymentModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Invoice Meta Overview -->
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 space-y-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 font-medium">Invoice Number:</span>
                        <span class="font-mono font-bold text-[#0B1849]">{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 font-medium">Customer:</span>
                        <span class="font-bold text-gray-900 truncate max-w-[200px]">{{ $invoice->customer->customer_name }}</span>
                    </div>
                    <div class="border-t border-slate-200/60 pt-2 grid grid-cols-3 gap-2 text-center">
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase block">Total</span>
                            <span class="font-extrabold text-gray-800">LKR {{ number_format($invoice->grand_total, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase block">Already Paid</span>
                            <span class="font-bold text-emerald-700">LKR {{ number_format($invoice->amount_paid, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase block">Outstanding</span>
                            <span class="font-extrabold text-red-600">LKR {{ number_format($invoice->balance_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <form 
                    action="{{ route('billing.payments.store', $invoice) }}" 
                    method="POST" 
                    class="space-y-4"
                    @submit="submitting = true"
                >
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Payment Amount (LKR) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="amount" 
                            x-model="paymentAmount"
                            max="{{ $invoice->balance_amount }}"
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
                        </select>
                    </div>

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

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Payment Reference (Optional)
                        </label>
                        <input 
                            type="text" 
                            name="reference_number" 
                            x-model="refNumber"
                            placeholder="Card slip #, Cheque #, or Transfer ref"
                            class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white min-h-[44px]"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Notes (Optional)
                        </label>
                        <textarea 
                            name="notes" 
                            x-model="notes"
                            rows="2"
                            placeholder="Additional payment notes..."
                            class="block w-full py-2 px-3 text-xs text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                        ></textarea>
                    </div>

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
                            :disabled="submitting || paymentAmount <= 0 || paymentAmount > {{ (float)$invoice->balance_amount }}"
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

    <!-- MODAL: REVERSE PAYMENT (ADMIN ONLY) -->
    @if(Auth::user() && Auth::user()->isAdmin())
        <div 
            x-show="showReverseModal" 
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;"
        >
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" @click="showReverseModal = false"></div>

                <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-200 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h3 class="text-base font-bold text-gray-900">Reverse Payment</h3>
                        <button type="button" @click="showReverseModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form 
                        :action="'/billing/payments/' + reversingPaymentId + '/reverse'" 
                        method="POST" 
                        class="space-y-4"
                    >
                        @csrf
                        <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700">
                            You are reversing payment <strong class="font-mono" x-text="reversingPaymentNumber"></strong>. This will deduct this payment from the invoice's Total Paid and increase the Outstanding Balance.
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Reversal Reason <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                name="reversal_reason" 
                                x-model="reversalReason"
                                rows="3"
                                placeholder="Explain why this payment is being reversed..."
                                class="block w-full py-2 px-3 text-xs text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                                required
                            ></textarea>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-3 border-t border-gray-100">
                            <button 
                                type="button" 
                                @click="showReverseModal = false"
                                class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition min-h-[40px] cursor-pointer"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit" 
                                class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-xs transition min-h-[44px] cursor-pointer"
                            >
                                Confirm Reversal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
