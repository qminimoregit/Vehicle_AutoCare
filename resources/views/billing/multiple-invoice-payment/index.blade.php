@extends('layouts.app')

@section('title', 'Multiple Invoice Payment')

@section('content')
<div 
    class="max-w-4xl mx-auto space-y-6"
    x-data="multiplePaymentApp({
        initialTab: '{{ $initialTab ?? (request()->has('customer_id') ? 'bulk' : 'split') }}',
        preloadedCustomerId: '{{ $selectedCustomerId ?? request('customer_id') ?? '' }}',
        preloadedInvoice: @js($splitInvoice ? [
            'id' => $splitInvoice->id,
            'invoice_number' => $splitInvoice->invoice_number,
            'invoice_date' => $splitInvoice->invoice_date ? $splitInvoice->invoice_date->format('d M Y') : '—',
            'customer_name' => $splitInvoice->customer?->customer_name ?? 'N/A',
            'company_name' => $splitInvoice->customer?->company_name ?? null,
            'vehicle_number' => $splitInvoice->jobCard?->vehicle?->vehicle_number ?? 'N/A',
            'grand_total' => (float) $splitInvoice->grand_total,
            'amount_paid' => (float) $splitInvoice->amount_paid,
            'balance_amount' => (float) $splitInvoice->balance_amount,
            'status' => $splitInvoice->status,
        ] : null)
    })"
>
    <!-- Header -->
    <div class="border-b border-gray-200 pb-4">
        <h1 class="text-2xl font-bold text-[#0B1849] tracking-tight">Multiple Invoice Payment</h1>
        <p class="text-sm text-gray-500 mt-1">Split payments for a single invoice across multiple methods or allocate one customer payment across multiple invoices.</p>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-gray-200 gap-2">
        <button 
            type="button" 
            @click="activeTab = 'split'" 
            :class="activeTab === 'split' ? 'border-[#0B1849] text-[#0B1849] font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'"
            class="py-3 px-4 border-b-2 text-sm transition flex items-center gap-2 cursor-pointer"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span>Option 1: Split Invoice Payment</span>
        </button>
        <button 
            type="button" 
            @click="activeTab = 'bulk'" 
            :class="activeTab === 'bulk' ? 'border-[#0B1849] text-[#0B1849] font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'"
            class="py-3 px-4 border-b-2 text-sm transition flex items-center gap-2 cursor-pointer"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Option 2: Pay Multiple Invoices</span>
        </button>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: SPLIT INVOICE PAYMENT (Option 1)    -->
    <!-- ========================================== -->
    <div x-show="activeTab === 'split'" class="space-y-6">
        <!-- Search Invoice Input -->
        <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-5">
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                Invoice Search
            </label>
            <div class="flex flex-col sm:flex-row gap-3">
                <input 
                    type="text" 
                    x-model="splitSearchNumber" 
                    @keydown.enter.prevent="lookupSplitInvoice()"
                    placeholder="Enter Invoice Number (e.g. INV-202610-0013 or #INV-202610-0013)..." 
                    class="flex-1 px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm text-gray-900 focus:bg-white focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] transition"
                >
                <button 
                    type="button" 
                    @click="lookupSplitInvoice()" 
                    :disabled="isSearching"
                    class="px-5 py-2.5 bg-[#0B1849] hover:bg-[#122368] text-white text-sm font-semibold rounded-lg shadow-xs transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
                >
                    <span x-show="!isSearching">Search Invoice</span>
                    <span x-show="isSearching">Searching...</span>
                </button>
            </div>
            <div x-show="splitSearchError" class="mt-2 text-xs font-semibold text-red-600" x-text="splitSearchError"></div>
        </div>

        <!-- Split Invoice Details & Dynamic Payment Rows -->
        <template x-if="splitInvoice">
            <div class="bg-white rounded-xl shadow-xs border border-gray-200 divide-y divide-gray-100">
                <!-- Invoice Info Header -->
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/50">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Selected Invoice</span>
                            <span 
                                class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider"
                                :class="splitInvoice.status === 'partially_paid' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800'"
                                x-text="splitInvoice.status === 'partially_paid' ? 'Partially Paid' : 'Issued'"
                            ></span>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 mt-1" x-text="'Invoice #' + splitInvoice.invoice_number"></h2>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Customer: <span class="font-semibold text-gray-800" x-text="splitInvoice.customer_name"></span>
                            <template x-if="splitInvoice.company_name">
                                <span class="text-gray-500" x-text="' (' + splitInvoice.company_name + ')'"></span>
                            </template>
                            • Vehicle: <span class="font-semibold text-gray-800" x-text="splitInvoice.vehicle_number"></span>
                            • Date: <span x-text="splitInvoice.invoice_date"></span>
                        </p>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-right">
                        <div>
                            <span class="text-xs text-gray-400 block">Total</span>
                            <span class="text-sm font-bold text-gray-800" x-text="'LKR ' + Number(splitInvoice.grand_total).toFixed(2)"></span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400 block">Already Paid</span>
                            <span class="text-sm font-bold text-emerald-700" x-text="'LKR ' + Number(splitInvoice.amount_paid).toFixed(2)"></span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400 block">Outstanding</span>
                            <span class="text-base font-extrabold text-red-600" x-text="'LKR ' + Number(splitInvoice.balance_amount).toFixed(2)"></span>
                        </div>
                    </div>
                </div>

                <!-- Split Payments Form -->
                <form method="POST" action="{{ route('billing.multiple-invoice-payment.split') }}" class="p-5 space-y-6">
                    @csrf
                    <input type="hidden" name="invoice_id" :value="splitInvoice.id">

                    <!-- Payment Date -->
                    <div class="w-full sm:w-60">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">
                            Payment Date <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="payment_date" 
                            value="{{ date('Y-m-d') }}" 
                            max="{{ date('Y-m-d') }}" 
                            class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm text-gray-900 focus:bg-white focus:ring-2 focus:ring-[#0B1849]"
                            required
                        >
                    </div>

                    <!-- Payment Rows Table -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600">
                                Payment Breakdown (Split Methods)
                            </label>
                            <button 
                                type="button" 
                                @click="addSplitRow()" 
                                class="text-xs font-bold text-[#0B1849] hover:text-blue-700 flex items-center gap-1 cursor-pointer"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>+ Add Payment Method</span>
                            </button>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(row, index) in splitRows" :key="index">
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 p-2.5 bg-gray-50 border border-gray-200 rounded-lg">
                                    <div class="w-full sm:w-44">
                                        <select 
                                            :name="'payments[' + index + '][payment_method]'" 
                                            x-model="row.method" 
                                            class="w-full px-2.5 py-1.5 bg-white border border-gray-300 rounded-md text-xs font-medium text-gray-800 focus:ring-2 focus:ring-[#0B1849]"
                                        >
                                            <option value="cash">Cash</option>
                                            <option value="card">Card</option>
                                            <option value="bank_transfer">Bank Transfer</option>
                                            <option value="cheque">Cheque</option>
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <input 
                                            type="text" 
                                            :name="'payments[' + index + '][reference_number]'" 
                                            x-model="row.reference" 
                                            :placeholder="row.method === 'cash' ? 'Optional Reference / Note' : 'Reference / Cheque / Txn # (Required)'"
                                            :required="row.method !== 'cash'"
                                            class="w-full px-2.5 py-1.5 bg-white border border-gray-300 rounded-md text-xs text-gray-800 focus:ring-2 focus:ring-[#0B1849]"
                                        >
                                    </div>
                                    <div class="w-full sm:w-40">
                                        <input 
                                            type="number" 
                                            step="0.01" 
                                            min="0.01" 
                                            :max="splitInvoice.balance_amount"
                                            :name="'payments[' + index + '][amount]'" 
                                            x-model.number="row.amount" 
                                            placeholder="Amount (LKR)" 
                                            class="w-full px-2.5 py-1.5 bg-white border border-gray-300 rounded-md text-xs font-bold text-gray-900 text-right focus:ring-2 focus:ring-[#0B1849]"
                                            required
                                        >
                                    </div>
                                    <div class="sm:shrink-0 text-right">
                                        <button 
                                            type="button" 
                                            @click="removeSplitRow(index)" 
                                            :disabled="splitRows.length === 1"
                                            class="p-1.5 text-gray-400 hover:text-red-600 disabled:opacity-30 transition cursor-pointer"
                                            title="Remove row"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Live Calculation Totals -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                        <div class="flex justify-between text-xs text-gray-600">
                            <span>Outstanding Balance:</span>
                            <span class="font-bold text-red-600" x-text="'LKR ' + Number(splitInvoice.balance_amount).toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-700">
                            <span class="font-semibold">Total New Payment:</span>
                            <span class="font-bold text-blue-900 text-sm" x-text="'LKR ' + splitTotalPaying().toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-xs font-bold" :class="splitRemainingBalance() < 0 ? 'text-red-600' : 'text-gray-800'">
                            <span>Remaining Balance:</span>
                            <span x-text="'LKR ' + Math.max(0, splitRemainingBalance()).toFixed(2)"></span>
                        </div>
                        <div x-show="splitRemainingBalance() < 0" class="text-xs text-red-600 font-bold pt-1">
                            ⚠ Payment amount exceeds the invoice balance by LKR <span x-text="Math.abs(splitRemainingBalance()).toFixed(2)"></span>.
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="splitInvoice = null" 
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            :disabled="splitTotalPaying() <= 0 || splitRemainingBalance() < 0"
                            class="px-6 py-2.5 bg-[#0B1849] hover:bg-[#122368] text-white text-sm font-bold rounded-lg shadow-xs transition disabled:opacity-50 cursor-pointer"
                        >
                            Save Payment
                        </button>
                    </div>
                </form>
            </div>
        </template>
    </div>

    <!-- ========================================== -->
    <!-- TAB 2: PAY MULTIPLE INVOICES (Option 2)    -->
    <!-- ========================================== -->
    <div x-show="activeTab === 'bulk'" class="space-y-6">
        <!-- Step 1: Select Customer or Company Card -->
        <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600">
                    Step 1: Select Customer / Company
                </label>
                <span class="text-xs text-gray-500">{{ $customers->count() }} Customer(s) with Unpaid Invoices</span>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <select 
                    x-model="selectedCustomerId" 
                    @change="loadCustomerInvoices()" 
                    class="flex-1 px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm text-gray-900 focus:bg-white focus:ring-2 focus:ring-[#0B1849]"
                >
                    <option value="">-- Choose Customer / Company with Outstanding Invoices --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">
                            {{ $c->customer_name }}
                            @if($c->company_name) - [{{ $c->company_name }}] @endif
                            ({{ $c->unpaid_invoices_count }} unpaid invoice{{ $c->unpaid_invoices_count != 1 ? 's' : '' }})
                        </option>
                    @endforeach
                </select>
                <button 
                    type="button" 
                    @click="loadCustomerInvoices()" 
                    :disabled="!selectedCustomerId || isLoadingCustomer"
                    class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold rounded-lg transition disabled:opacity-50 cursor-pointer flex items-center justify-center gap-1.5"
                >
                    <span x-show="!isLoadingCustomer">Reload Invoices</span>
                    <span x-show="isLoadingCustomer">Loading...</span>
                </button>
            </div>
            <div x-show="bulkSearchError" class="mt-2 text-xs font-semibold text-red-600" x-text="bulkSearchError"></div>
        </div>

        <!-- Placeholder when no customer is selected yet -->
        <div x-show="!customerData && !isLoadingCustomer" class="bg-white rounded-xl shadow-xs border border-dashed border-gray-300 p-10 text-center space-y-3">
            <div class="w-12 h-12 bg-blue-50 text-[#0B1849] rounded-full mx-auto flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <h3 class="text-sm font-bold text-gray-800">Select Customer to Pay Multiple Invoices</h3>
            <p class="text-xs text-gray-500 max-w-md mx-auto">
                Choose a customer or corporate account from the dropdown above to load all outstanding invoices within your branch scope. You can select invoices, enter a payment receipt, and allocate amounts across them.
            </p>
        </div>

        <!-- Loading spinner -->
        <div x-show="isLoadingCustomer" class="bg-white rounded-xl shadow-xs border border-gray-200 p-8 text-center space-y-2">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-[#0B1849] border-t-transparent"></div>
            <p class="text-xs text-gray-500 font-semibold">Loading outstanding invoices...</p>
        </div>

        <!-- Customer Unpaid Invoices & Bulk Allocation Form -->
        <template x-if="customerData">
            <div class="bg-white rounded-xl shadow-xs border border-gray-200 divide-y divide-gray-100">
                <!-- Customer Header -->
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/50">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Customer Account</span>
                        <h2 class="text-lg font-bold text-gray-900" x-text="customerData.customer.name"></h2>
                        <p class="text-xs text-gray-500 mt-0.5">
                            <span x-text="customerData.customer.company_name ? customerData.customer.company_name + ' • ' : ''"></span>
                            Phone: <span class="font-medium text-gray-700" x-text="customerData.customer.phone"></span>
                            • Type: <span class="font-medium text-gray-700" x-text="customerData.customer.type"></span>
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-400 block">Total Outstanding Balance</span>
                        <span class="text-xl font-extrabold text-red-600" x-text="'LKR ' + Number(customerData.total_outstanding).toFixed(2)"></span>
                    </div>
                </div>

                <form method="POST" action="{{ route('billing.multiple-invoice-payment.bulk') }}" class="p-5 space-y-6">
                    @csrf
                    <input type="hidden" name="customer_id" :value="customerData.customer.id">

                    <!-- Steps 3 & 4: Outstanding Invoices Table -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600">
                                    Steps 2 & 3: Select Invoices to Pay
                                </label>
                                <span class="text-[11px] text-gray-400">Check the invoices you want to include in this payment receipt.</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    @click="toggleSelectAll(true)" 
                                    class="text-[11px] text-blue-700 hover:underline font-semibold cursor-pointer"
                                >
                                    Select All
                                </button>
                                <span class="text-gray-300">|</span>
                                <button 
                                    type="button" 
                                    @click="toggleSelectAll(false)" 
                                    class="text-[11px] text-gray-500 hover:underline font-semibold cursor-pointer"
                                >
                                    Deselect All
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                    <tr>
                                        <th class="px-3 py-2.5 w-10 text-center">
                                            <input 
                                                type="checkbox" 
                                                @change="toggleSelectAll($event.target.checked)"
                                                :checked="customerData.invoices.length > 0 && customerData.invoices.every(i => i.selected)"
                                                class="rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849]"
                                                title="Select / Deselect All"
                                            >
                                        </th>
                                        <th class="px-3 py-2.5">Invoice #</th>
                                        <th class="px-3 py-2.5">Date</th>
                                        <th class="px-3 py-2.5">Vehicle</th>
                                        <th class="px-3 py-2.5 text-right">Total</th>
                                        <th class="px-3 py-2.5 text-right">Paid</th>
                                        <th class="px-3 py-2.5 text-right">Balance</th>
                                        <th class="px-3 py-2.5 text-right w-36">Allocate (LKR)</th>
                                        <th class="px-3 py-2.5 text-right">Remaining</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-gray-700">
                                    <template x-for="(inv, index) in customerData.invoices" :key="inv.id">
                                        <tr :class="inv.selected ? (inv.allocated > 0 ? 'bg-blue-50/50' : 'bg-white') : 'bg-gray-50/50 opacity-60'">
                                            <td class="px-3 py-3 text-center">
                                                <input 
                                                    type="checkbox" 
                                                    x-model="inv.selected" 
                                                    @change="onToggleInvoice(inv)"
                                                    class="rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849]"
                                                >
                                            </td>
                                            <td class="px-3 py-3">
                                                <input type="hidden" :name="'allocations[' + index + '][invoice_id]'" :value="inv.id">
                                                <span class="font-bold text-gray-900" x-text="inv.invoice_number"></span>
                                                <span class="text-[11px] text-gray-400 block" x-text="inv.invoice_type"></span>
                                            </td>
                                            <td class="px-3 py-3 text-xs text-gray-600" x-text="inv.invoice_date"></td>
                                            <td class="px-3 py-3 font-medium text-gray-800" x-text="inv.vehicle_number"></td>
                                            <td class="px-3 py-3 text-right text-gray-600" x-text="Number(inv.grand_total).toFixed(2)"></td>
                                            <td class="px-3 py-3 text-right text-emerald-700 font-medium" x-text="Number(inv.amount_paid).toFixed(2)"></td>
                                            <td class="px-3 py-3 text-right font-semibold text-red-600" x-text="Number(inv.balance_amount).toFixed(2)"></td>
                                            <td class="px-3 py-3 text-right">
                                                <input 
                                                    type="number" 
                                                    step="0.01" 
                                                    min="0" 
                                                    :max="inv.balance_amount"
                                                    :name="'allocations[' + index + '][allocated_amount]'" 
                                                    x-model.number="inv.allocated" 
                                                    :disabled="!inv.selected"
                                                    class="w-full px-2 py-1 bg-white border border-gray-300 rounded text-xs font-bold text-right text-gray-900 focus:ring-2 focus:ring-[#0B1849] disabled:bg-gray-100 disabled:text-gray-400"
                                                    placeholder="0.00"
                                                >
                                            </td>
                                            <td class="px-3 py-3 text-right text-xs font-semibold" :class="Number(inv.balance_amount - (inv.allocated || 0)) <= 0 ? 'text-emerald-700 font-bold' : 'text-gray-600'">
                                                <span x-text="'LKR ' + Math.max(0, Number(inv.balance_amount - (inv.allocated || 0))).toFixed(2)"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Step 5: Receipt Payment Details -->
                    <div class="space-y-4 pt-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600">
                            Step 4: Enter Payment Received
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">
                                    Total Received Amount <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    min="0.01" 
                                    name="total_received" 
                                    x-model.number="bulkReceivedAmount" 
                                    placeholder="0.00" 
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-[#0B1849]"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">
                                    Payment Method <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="payment_method" 
                                    x-model="bulkPaymentMethod"
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm text-gray-900 focus:bg-white focus:ring-2 focus:ring-[#0B1849]"
                                    required
                                >
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">
                                    Payment Date <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    name="payment_date" 
                                    value="{{ date('Y-m-d') }}" 
                                    max="{{ date('Y-m-d') }}" 
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm text-gray-900 focus:bg-white focus:ring-2 focus:ring-[#0B1849]"
                                    required
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">
                                    Reference / Cheque Details <span x-show="bulkPaymentMethod !== 'cash'" class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="reference_number" 
                                    x-model="bulkReference"
                                    :required="bulkPaymentMethod !== 'cash'"
                                    :placeholder="bulkPaymentMethod === 'cash' ? 'Optional cash reference' : 'Cheque #, Bank Txn #, or Card Approval Code (Required)'" 
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm text-gray-900 focus:bg-white focus:ring-2 focus:ring-[#0B1849]"
                                >
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Notes</label>
                                <input 
                                    type="text" 
                                    name="notes" 
                                    placeholder="Optional notes for this payment receipt" 
                                    class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm text-gray-900 focus:bg-white focus:ring-2 focus:ring-[#0B1849]"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Step 6: Allocation Helpers & Real-Time Calculation -->
                    <div class="space-y-3 pt-2">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600">
                                Step 5: Allocate Payment
                            </label>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    @click="autoAllocateFifo()" 
                                    class="px-3 py-1.5 bg-blue-50 text-blue-800 hover:bg-blue-100 border border-blue-200 rounded-md text-xs font-bold transition flex items-center gap-1 cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <span>Auto-Allocate (FIFO)</span>
                                </button>
                                <button 
                                    type="button" 
                                    @click="clearAllocations()" 
                                    class="px-3 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-300 rounded-md text-xs font-bold transition cursor-pointer"
                                >
                                    Clear Allocations
                                </button>
                            </div>
                        </div>

                        <!-- Summary Bar -->
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2 text-xs">
                            <div class="flex justify-between text-gray-600">
                                <span>Total Received Amount:</span>
                                <span class="font-bold text-gray-900" x-text="'LKR ' + Number(bulkReceivedAmount || 0).toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Total Allocated:</span>
                                <span class="font-bold text-blue-700" x-text="'LKR ' + totalBulkAllocated().toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between font-bold" :class="bulkUnallocatedAmount() < 0 ? 'text-red-600' : 'text-gray-800'">
                                <span>Unallocated Amount:</span>
                                <span x-text="'LKR ' + Math.max(0, bulkUnallocatedAmount()).toFixed(2)"></span>
                            </div>
                            <div x-show="bulkUnallocatedAmount() < 0" class="text-xs text-red-600 font-bold pt-1">
                                ⚠ Allocated total exceeds received amount by LKR <span x-text="Math.abs(bulkUnallocatedAmount()).toFixed(2)"></span>.
                            </div>
                        </div>
                    </div>

                    <!-- Step 7: Submit Button -->
                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="customerData = null; selectedCustomerId = ''" 
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            :disabled="bulkReceivedAmount <= 0 || totalBulkAllocated() <= 0 || bulkUnallocatedAmount() < 0 || countSelectedInvoices() === 0"
                            class="px-6 py-2.5 bg-[#0B1849] hover:bg-[#122368] text-white text-sm font-bold rounded-lg shadow-xs transition disabled:opacity-50 cursor-pointer"
                        >
                            Save Payment Allocation
                        </button>
                    </div>
                </form>
            </div>
        </template>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.multiplePaymentApp = function(config) {
    return {
        activeTab: config.initialTab || 'split',
        
        // Option 1 state
        splitSearchNumber: config.preloadedInvoice ? config.preloadedInvoice.invoice_number : '',
        splitInvoice: config.preloadedInvoice || null,
        isSearching: false,
        splitSearchError: '',
        splitRows: [
            { method: 'cash', reference: '', amount: config.preloadedInvoice ? Number(config.preloadedInvoice.balance_amount) : 0 }
        ],

        // Option 2 state
        selectedCustomerId: config.preloadedCustomerId || '',
        customerData: null,
        isLoadingCustomer: false,
        bulkSearchError: '',
        bulkReceivedAmount: 0,
        bulkPaymentMethod: 'cash',
        bulkReference: '',

        init() {
            if (this.splitInvoice && this.splitInvoice.balance_amount > 0) {
                this.splitRows = [
                    { method: 'cash', reference: '', amount: Number(this.splitInvoice.balance_amount) }
                ];
            }

            if (this.selectedCustomerId) {
                this.loadCustomerInvoices();
            }
        },

        async lookupSplitInvoice() {
            const raw = this.splitSearchNumber.trim();
            if (!raw) {
                this.splitSearchError = 'Please enter an invoice number to search.';
                return;
            }
            this.isSearching = true;
            this.splitSearchError = '';

            try {
                const res = await fetch(`{{ route('billing.multiple-invoice-payment.search-invoice') }}?number=${encodeURIComponent(raw)}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json().catch(() => null);

                this.isSearching = false;
                if (res.ok && data && data.success) {
                    this.splitInvoice = data.invoice;
                    this.splitRows = [
                        { method: 'cash', reference: '', amount: Number(data.invoice.balance_amount) }
                    ];
                } else {
                    this.splitInvoice = null;
                    if (data && data.message) {
                        this.splitSearchError = data.message;
                    } else if (res.status >= 500) {
                        this.splitSearchError = 'Server error occurred while searching invoice. Please try again.';
                    } else {
                        this.splitSearchError = `Invoice '${raw}' could not be loaded.`;
                    }
                }
            } catch (err) {
                this.isSearching = false;
                this.splitInvoice = null;
                this.splitSearchError = 'Network error looking up invoice. Please check your connection.';
            }
        },

        addSplitRow() {
            const rem = Math.max(0, this.splitRemainingBalance());
            this.splitRows.push({
                method: 'cash',
                reference: '',
                amount: rem > 0 ? rem : 0
            });
        },

        removeSplitRow(index) {
            if (this.splitRows.length > 1) {
                this.splitRows.splice(index, 1);
            }
        },

        splitTotalPaying() {
            return this.splitRows.reduce((acc, row) => acc + (Number(row.amount) || 0), 0);
        },

        splitRemainingBalance() {
            if (!this.splitInvoice) return 0;
            return Number(this.splitInvoice.balance_amount) - this.splitTotalPaying();
        },

        async loadCustomerInvoices() {
            if (!this.selectedCustomerId) {
                this.customerData = null;
                return;
            }

            this.isLoadingCustomer = true;
            this.bulkSearchError = '';

            try {
                const res = await fetch(`{{ url('billing/multiple-invoice-payment/customers') }}/${this.selectedCustomerId}/invoices`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json().catch(() => null);

                this.isLoadingCustomer = false;
                if (res.ok && data && data.success) {
                    data.invoices = data.invoices.map(inv => ({
                        ...inv,
                        selected: true,
                        allocated: 0
                    }));
                    this.customerData = data;
                    this.bulkReceivedAmount = Number(data.total_outstanding);
                    this.autoAllocateFifo();
                } else {
                    this.customerData = null;
                    this.bulkSearchError = data?.message || 'Failed to load outstanding invoices for this customer.';
                }
            } catch (err) {
                this.isLoadingCustomer = false;
                this.customerData = null;
                this.bulkSearchError = 'Network error loading customer invoices.';
            }
        },

        toggleSelectAll(checked) {
            if (!this.customerData || !this.customerData.invoices) return;
            this.customerData.invoices.forEach(inv => {
                inv.selected = checked;
                if (!checked) {
                    inv.allocated = 0;
                }
            });
            if (checked) {
                this.autoAllocateFifo();
            }
        },

        onToggleInvoice(inv) {
            if (!inv.selected) {
                inv.allocated = 0;
            }
        },

        autoAllocateFifo() {
            if (!this.customerData || !this.customerData.invoices) return;
            let available = Number(this.bulkReceivedAmount) || 0;

            this.customerData.invoices.forEach(inv => {
                if (inv.selected) {
                    const bal = Number(inv.balance_amount);
                    if (available > 0) {
                        const toAlloc = Math.min(bal, available);
                        inv.allocated = Math.round(toAlloc * 100) / 100;
                        available -= inv.allocated;
                    } else {
                        inv.allocated = 0;
                    }
                } else {
                    inv.allocated = 0;
                }
            });
        },

        clearAllocations() {
            if (!this.customerData || !this.customerData.invoices) return;
            this.customerData.invoices.forEach(inv => {
                inv.allocated = 0;
            });
        },

        totalBulkAllocated() {
            if (!this.customerData || !this.customerData.invoices) return 0;
            return this.customerData.invoices.reduce((acc, inv) => {
                return acc + (inv.selected ? (Number(inv.allocated) || 0) : 0);
            }, 0);
        },

        bulkUnallocatedAmount() {
            const rec = Number(this.bulkReceivedAmount) || 0;
            return Math.round((rec - this.totalBulkAllocated()) * 100) / 100;
        },

        countSelectedInvoices() {
            if (!this.customerData || !this.customerData.invoices) return 0;
            return this.customerData.invoices.filter(i => i.selected).length;
        }
    };
};
</script>
@endpush
