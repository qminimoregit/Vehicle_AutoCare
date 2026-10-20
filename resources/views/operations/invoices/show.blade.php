@extends('layouts.app')

@section('title', 'Invoice #' . $invoice->invoice_number)

@section('content')
<div 
    x-data="{ 
        showPaymentModal: false,
        paymentAmount: '{{ $invoice->balance_amount }}',
        paymentMethod: 'cash',
        refNumber: '',
        notes: ''
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
                
                @if($invoice->status === 'paid')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Paid in Full</span>
                @elseif($invoice->status === 'partially_paid')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Partially Paid</span>
                @elseif($invoice->status === 'issued')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Issued</span>
                @elseif($invoice->status === 'cancelled')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">Cancelled</span>
                @endif
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
            @endif
        </div>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

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

        <!-- Payments Log (if any payments recorded) -->
        @if($invoice->payments->count() > 0)
            <div class="pt-6 border-t border-gray-100">
                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Recorded Payments</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-xs">
                        <thead class="bg-slate-50 text-gray-500 font-semibold uppercase text-[10px]">
                            <tr>
                                <th class="py-2 px-3 text-left">Payment #</th>
                                <th class="py-2 px-3 text-left">Date</th>
                                <th class="py-2 px-3 text-left">Method</th>
                                <th class="py-2 px-3 text-left">Reference</th>
                                <th class="py-2 px-3 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-700">
                            @foreach($invoice->payments as $pay)
                                <tr>
                                    <td class="py-2 px-3 font-bold text-gray-900">{{ $pay->payment_number }}</td>
                                    <td class="py-2 px-3 text-gray-500">{{ $pay->payment_date ? $pay->payment_date->format('Y-m-d') : '' }}</td>
                                    <td class="py-2 px-3 uppercase font-semibold text-[11px]">{{ str_replace('_', ' ', $pay->payment_method) }}</td>
                                    <td class="py-2 px-3 text-gray-500">{{ $pay->reference_number ?? '—' }}</td>
                                    <td class="py-2 px-3 text-right font-bold text-emerald-700">LKR {{ number_format($pay->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
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
                    <h3 class="text-base font-bold text-gray-900">Record Payment</h3>
                    <button type="button" @click="showPaymentModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('operations.invoices.payment', $invoice) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Amount to Pay (LKR) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="amount" 
                            x-model="paymentAmount"
                            max="{{ $invoice->balance_amount }}"
                            min="0.01"
                            class="block w-full py-2.5 px-3 text-base font-bold text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                            required
                        >
                        <p class="mt-1 text-[11px] text-gray-500">Remaining Balance: LKR {{ number_format($invoice->balance_amount, 2) }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Payment Method <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="payment_method" 
                            x-model="paymentMethod"
                            class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
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
                            Reference / Cheque #
                        </label>
                        <input 
                            type="text" 
                            name="reference_number" 
                            x-model="refNumber"
                            placeholder="Optional ref or receipt number"
                            class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                        >
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-3 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="showPaymentModal = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition min-h-[40px]"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-bold rounded-lg shadow-xs transition min-h-[44px]"
                        >
                            Confirm Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
