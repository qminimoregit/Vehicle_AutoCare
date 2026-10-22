@extends('layouts.app')

@section('title', 'GRN Details - ' . $grn->grn_number)

@section('content')
    <div 
        class="max-w-5xl mx-auto space-y-6"
        x-data="{
            submittingPayment: false,
            reversalModalOpen: false,
            submittingReversal: false,
            selectedPayment: {
                id: null,
                number: '',
                amount: '',
                action_url: ''
            },
            reversalReason: '',
            openReversalModal(payment) {
                this.selectedPayment = payment;
                this.reversalReason = '';
                this.submittingReversal = false;
                this.reversalModalOpen = true;
            }
        }"
    >
        <!-- Top Navigation & Action Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('inventory.grn.index') }}" class="text-xs font-semibold text-[#0B1849] hover:underline inline-flex items-center mb-1">
                    &larr; Back to GRN List
                </a>
                <div class="flex items-center space-x-3">
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ $grn->grn_number }}</h1>
                    @if($grn->isConfirmed())
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Confirmed & Stocked
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                            Draft (No Stock Added)
                        </span>
                    @endif

                    <!-- Payment Status -->
                    @php $payStatus = $grn->payment_status; @endphp
                    @if($payStatus === 'Paid')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                            Invoice Paid
                        </span>
                    @elseif($payStatus === 'Partially Paid')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                            Partially Paid
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                            Unpaid Invoice
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <a 
                    href="{{ route('inventory.grn.print', $grn) }}" 
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition shadow-2xs"
                >
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print GRN
                </a>

                @if($grn->outstanding_amount > 0 && (Auth::user()->isAdmin() || Auth::user()->hasPermission('record_payments')))
                    <a 
                        href="#payment-form" 
                        class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg text-white bg-[#0B1849] hover:bg-[#15286e] transition shadow-xs"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Record Payment
                    </a>
                @else
                    <a 
                        href="#payments-section" 
                        class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition shadow-2xs"
                    >
                        View Payments
                    </a>
                @endif

                @if($grn->isDraft() && Auth::user()->isAdmin())
                    <form 
                        method="POST" 
                        action="{{ route('inventory.grn.confirm', $grn) }}" 
                        onsubmit="return confirm('Are you sure you want to confirm this GRN and generate live stock batches? This action increases warehouse inventory.')"
                    >
                        @csrf
                        @method('PATCH')
                        <button 
                            type="submit" 
                            class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg text-white bg-emerald-700 hover:bg-emerald-800 transition shadow-xs cursor-pointer"
                        >
                            Confirm & Post to Stock
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Supplier & Invoice Information Card -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Supplier -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs space-y-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Supplier Info</h3>
                <div class="text-base font-bold text-gray-900">
                    <a href="{{ route('inventory.suppliers.show', $grn->supplier) }}" class="text-[#0B1849] hover:underline">
                        {{ $grn->supplier->name }}
                    </a>
                </div>
                <div class="text-xs text-gray-600">
                    <div>Mobile: {{ $grn->supplier->mobile ?: 'N/A' }}</div>
                    <div>Reg: {{ $grn->supplier->registration_number ?: '-' }}</div>
                </div>
            </div>

            <!-- Invoice Details -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs space-y-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Invoice Information</h3>
                <div>
                    <span class="text-xs text-gray-500 block">Supplier Invoice No</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $grn->supplier_invoice_number ?: 'Not provided' }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Received Date</span>
                    <span class="text-sm text-gray-800">{{ $grn->received_date->format('F d, Y') }}</span>
                </div>
            </div>

            <!-- Audit & Attachment -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs space-y-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Record Audit</h3>
                <div class="text-xs text-gray-600 space-y-1">
                    <div>Created by: <span class="font-medium text-gray-900">{{ $grn->createdBy ? $grn->createdBy->name : 'System' }}</span></div>
                    @if($grn->postedBy)
                        <div>Posted by: <span class="font-medium text-gray-900">{{ $grn->postedBy->name }}</span></div>
                        <div>Posted on: <span class="font-medium text-gray-900">{{ $grn->posted_at?->format('M d, Y H:i') }}</span></div>
                    @endif
                </div>

                @if($grn->invoice_attachment)
                    <div class="pt-2">
                        <a 
                            href="{{ asset('storage/' . $grn->invoice_attachment) }}" 
                            target="_blank" 
                            class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline"
                        >
                            <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            View Invoice Attachment
                        </a>
                    </div>
                @endif
            </div>
        </div>

        @if($grn->notes)
            <div class="bg-blue-50/50 border border-blue-200 rounded-xl p-4 text-xs text-blue-900">
                <span class="font-bold uppercase tracking-wider text-blue-800">Delivery Notes:</span> {{ $grn->notes }}
            </div>
        @endif

        <!-- Products Received Table -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-6 py-4 bg-gray-50/80 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">Products Received in this GRN</h3>
                <span class="text-xs text-gray-500">{{ $grn->items->count() }} line items</span>
            </div>

            <div class="responsive-table-wrapper">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Product</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">UOM</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Invoiced Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Free Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Physical Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Invoice Price</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Inventory Unit Cost</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase">Line Total (LKR)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($grn->items as $item)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-3">
                                    <div class="text-sm font-bold text-gray-900">{{ $item->product->name }}</div>
                                    @if($item->product->sku)
                                        <div class="text-xs text-gray-400 font-mono">SKU: {{ $item->product->sku }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-xs text-gray-600 font-semibold">
                                    {{ $item->product->uom ? $item->product->uom->name : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-sm text-gray-900">
                                    @quantity($item->purchase_quantity)
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-sm text-emerald-700 font-semibold">
                                    {{ $item->free_quantity > 0 ? '+ ' . \App\Services\QuantityFormatter::format($item->free_quantity) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-sm text-gray-900 font-bold bg-gray-50/60">
                                    @quantity($item->total_physical_quantity)
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-sm text-gray-700">
                                    {{ number_format($item->purchase_unit_price, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-sm text-blue-900 font-semibold">
                                    {{ number_format($item->effective_unit_cost, 4) }}
                                </td>
                                <td class="px-6 py-3 text-right font-mono font-bold text-sm text-gray-900 whitespace-nowrap">
                                    {{ number_format($item->line_total, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                        <tr>
                            <td colspan="7" class="px-6 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-gray-700">
                                Total Invoice Amount (LKR):
                            </td>
                            <td class="px-6 py-3.5 text-right font-mono font-black text-base text-gray-900 whitespace-nowrap">
                                LKR {{ number_format($grn->total_amount, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($grn->isConfirmed() && $grn->stockBatches->isNotEmpty())
            <!-- Generated Stock Batches Card -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-6 py-4 bg-gray-50/80 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Active Stock Batches Created</h3>
                        <p class="text-xs text-gray-500">Each batch maintains distinct historical costs under First-In, First-Out (FIFO).</p>
                    </div>
                </div>

                <div class="responsive-table-wrapper">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Batch Number</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Product</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Unit Cost (LKR)</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Received Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Remaining Stock</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase">Batch Stock Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($grn->stockBatches as $batch)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-6 py-3 font-mono font-bold text-sm text-[#0B1849]">
                                        {{ $batch->batch_number }}
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-900">
                                        {{ $batch->product->name }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono text-sm text-gray-900">
                                        {{ number_format($batch->inventory_unit_cost, 4) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono text-sm text-gray-600">
                                        @quantity($batch->received_quantity)
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono text-sm font-bold {{ $batch->remaining_quantity > 0 ? 'text-emerald-700' : 'text-gray-400' }}">
                                        @quantity($batch->remaining_quantity)
                                    </td>
                                    <td class="px-6 py-3 text-right font-mono font-bold text-sm text-gray-900">
                                        LKR {{ number_format($batch->batch_value, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Supplier Invoice Payment Tracking (Section 13) -->
        <div id="payments-section" class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-6 py-4 bg-gray-50/80 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Supplier Invoice Payment Tracking</h3>
                    <p class="text-xs text-gray-500">Record settlements against vendor invoice independently from stock receipts.</p>
                </div>
            </div>

            <!-- Financial Summary Bar (6 Requested Data Points) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 divide-y sm:divide-y-0 sm:divide-x divide-gray-200 border-b border-gray-200 bg-gray-50/40 text-xs">
                <div class="p-3.5 text-center">
                    <span class="text-gray-500 block uppercase font-semibold text-[10px]">GRN Number</span>
                    <span class="font-bold text-gray-900 font-mono text-xs block mt-0.5">{{ $grn->grn_number }}</span>
                </div>
                <div class="p-3.5 text-center">
                    <span class="text-gray-500 block uppercase font-semibold text-[10px]">Supplier Name</span>
                    <span class="font-bold text-gray-900 truncate block mt-0.5" title="{{ $grn->supplier->name }}">{{ $grn->supplier->name }}</span>
                </div>
                <div class="p-3.5 text-center">
                    <span class="text-gray-500 block uppercase font-semibold text-[10px]">Supplier Invoice #</span>
                    <span class="font-medium text-gray-800 font-mono text-xs block mt-0.5">{{ $grn->supplier_invoice_number ?: 'N/A' }}</span>
                </div>
                <div class="p-3.5 text-center">
                    <span class="text-gray-500 block uppercase font-semibold text-[10px]">GRN Total</span>
                    <span class="font-bold text-gray-900 font-mono text-xs block mt-0.5">LKR {{ number_format($grn->total_amount, 2) }}</span>
                </div>
                <div class="p-3.5 text-center">
                    <span class="text-emerald-700 block uppercase font-semibold text-[10px]">Already Paid</span>
                    <span class="font-bold text-emerald-700 font-mono text-xs block mt-0.5">LKR {{ number_format($grn->paid_amount, 2) }}</span>
                </div>
                <div class="p-3.5 text-center">
                    <span class="{{ $grn->outstanding_amount > 0 ? 'text-rose-700' : 'text-gray-500' }} block uppercase font-semibold text-[10px]">Outstanding Balance</span>
                    <span class="font-bold {{ $grn->outstanding_amount > 0 ? 'text-rose-700' : 'text-gray-700' }} font-mono text-xs block mt-0.5">
                        LKR {{ number_format($grn->outstanding_amount, 2) }}
                    </span>
                </div>
            </div>

            <!-- Payments List -->
            <div class="p-6 space-y-6">
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-3">Payment History</h4>
                    @if($grn->payments->isNotEmpty())
                        <div class="responsive-table-wrapper border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50/60">
                                    <tr class="text-left text-xs font-bold text-gray-600 uppercase">
                                        <th class="px-4 py-2.5">Date</th>
                                        <th class="px-4 py-2.5 text-right">Amount (LKR)</th>
                                        <th class="px-4 py-2.5">Payment Method</th>
                                        <th class="px-4 py-2.5">Reference</th>
                                        <th class="px-4 py-2.5">Recorded By</th>
                                        <th class="px-4 py-2.5 text-center">Status</th>
                                        <th class="px-4 py-2.5 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 text-xs">
                                    @foreach($grn->payments as $payment)
                                        <tr class="hover:bg-gray-50/50 {{ $payment->isReversed() ? 'bg-rose-50/30 text-gray-400' : 'text-gray-900' }}">
                                            <!-- Date -->
                                            <td class="px-4 py-3 font-medium whitespace-nowrap">
                                                {{ $payment->payment_date->format('d M Y') }}
                                            </td>

                                            <!-- Amount -->
                                            <td class="px-4 py-3 text-right font-mono font-bold whitespace-nowrap {{ $payment->isReversed() ? 'line-through text-gray-400' : 'text-gray-900' }}">
                                                {{ number_format($payment->amount, 2) }}
                                            </td>

                                            <!-- Payment Method -->
                                            <td class="px-4 py-3 capitalize whitespace-nowrap">
                                                {{ str_replace('_', ' ', $payment->payment_method) }}
                                            </td>

                                            <!-- Reference -->
                                            <td class="px-4 py-3 font-mono whitespace-nowrap">
                                                {{ $payment->reference_number ?: '-' }}
                                            </td>

                                            <!-- Recorded By -->
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                {{ $payment->createdBy ? $payment->createdBy->name : 'System' }}
                                            </td>

                                            <!-- Status -->
                                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                                @if($payment->isReversed())
                                                    <span 
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200"
                                                        title="Reason: {{ $payment->reversal_reason }}"
                                                    >
                                                        Reversed
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        Completed
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- Actions -->
                                            <td class="px-4 py-3 text-right whitespace-nowrap space-x-1.5">
                                                <!-- Receipt -->
                                                <a 
                                                    href="{{ route('inventory.supplier-payments.receipt', $payment) }}" 
                                                    target="_blank"
                                                    class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition"
                                                    title="Print Payment Voucher"
                                                >
                                                    Receipt
                                                </a>

                                                <!-- Reversal (Admin Only) -->
                                                @if(Auth::user()->isAdmin() && ! $payment->isReversed())
                                                    <button 
                                                        type="button" 
                                                        @click="openReversalModal({
                                                            id: {{ $payment->id }},
                                                            number: '{{ $payment->payment_number ?? ('PAY-' . $payment->id) }}',
                                                            amount: '{{ number_format($payment->amount, 2) }}',
                                                            action_url: '{{ route('inventory.supplier-payments.reverse', $payment) }}'
                                                        })"
                                                        class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition cursor-pointer"
                                                    >
                                                        Reverse
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($payment->isReversed() && $payment->reversal_reason)
                                            <tr class="bg-rose-50/20 text-[11px] text-rose-800">
                                                <td colspan="7" class="px-4 py-1.5 italic">
                                                    Reversal audit: Reversed on {{ $payment->reversed_at?->format('M d, Y H:i') }} by {{ $payment->reversedBy?->name ?? 'Admin' }}. Reason: {{ $payment->reversal_reason }}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-xs text-gray-400 italic">No payment transactions recorded for this supplier invoice yet.</p>
                    @endif
                </div>

                @if((Auth::user()->isAdmin() || Auth::user()->hasPermission('record_payments')) && $grn->outstanding_amount > 0)
                    <!-- Record Payment Form -->
                    <div id="payment-form" class="mt-6 pt-5 border-t border-gray-200">
                        <div class="mb-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700">Record Payment</h4>
                            <p class="text-xs text-gray-500">Enter payment settlement details for GRN #{{ $grn->grn_number }}.</p>
                        </div>

                        <form 
                            method="POST" 
                            action="{{ route('inventory.grn.payments.store', $grn) }}" 
                            class="space-y-4"
                            @submit="if(submittingPayment){ $event.preventDefault(); return false; } submittingPayment = true;"
                        >
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Payment Amount (LKR) *</label>
                                    <input 
                                        type="number" 
                                        step="0.01" 
                                        min="0.01" 
                                        max="{{ $grn->outstanding_amount }}" 
                                        name="amount" 
                                        required 
                                        value="{{ $grn->outstanding_amount }}" 
                                        class="block w-full px-3 py-2 text-sm font-mono font-bold text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849]"
                                    />
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Payment Method *</label>
                                    <select 
                                        name="payment_method" 
                                        required 
                                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] bg-white"
                                    >
                                        <option value="cash">Cash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="card">Card</option>
                                        <option value="cheque">Cheque</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Payment Date *</label>
                                    <input 
                                        type="date" 
                                        name="payment_date" 
                                        required 
                                        value="{{ date('Y-m-d') }}" 
                                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] bg-white"
                                    />
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Payment Reference <span class="text-gray-400 font-normal">(optional)</span></label>
                                    <input 
                                        type="text" 
                                        name="reference_number" 
                                        placeholder="e.g. Chq #, Bank slip ref" 
                                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849]"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                                <div class="sm:col-span-3">
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Note <span class="text-gray-400 font-normal">(optional)</span></label>
                                    <input 
                                        type="text" 
                                        name="notes" 
                                        placeholder="Optional payment remarks..." 
                                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849]"
                                    />
                                </div>
                                <div class="text-right">
                                    <button 
                                        type="submit" 
                                        :disabled="submittingPayment"
                                        class="w-full sm:w-auto px-6 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-sm rounded-lg shadow-xs transition cursor-pointer disabled:opacity-50"
                                    >
                                        <span x-show="!submittingPayment">Save Payment</span>
                                        <span x-show="submittingPayment" class="inline-flex items-center">
                                            <svg class="animate-spin -ml-1 mr-2 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                            </svg>
                                            Saving...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- MODAL: REVERSE SUPPLIER PAYMENT (ADMIN ONLY) -->
        <div 
            x-show="reversalModalOpen" 
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;"
            x-cloak
        >
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" @click="reversalModalOpen = false"></div>

                <div class="relative bg-white rounded-2xl w-full max-w-[calc(100vw-2rem)] sm:max-w-md max-h-[88vh] overflow-y-auto p-4 sm:p-6 shadow-xl border border-gray-200 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Reverse Supplier Payment</h3>
                            <p class="text-xs text-rose-600 font-medium">Financial Reversal & Audit</p>
                        </div>
                        <button type="button" @click="reversalModalOpen = false" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-900">
                        Reversing payment <span class="font-bold font-mono" x-text="selectedPayment.number"></span> (LKR <span class="font-bold font-mono" x-text="selectedPayment.amount"></span>) will restore the outstanding balance for GRN #{{ $grn->grn_number }}. This transaction is audited.
                    </div>

                    <form 
                        :action="selectedPayment.action_url" 
                        method="POST" 
                        class="space-y-4"
                        @submit="if(submittingReversal){ $event.preventDefault(); return false; } submittingReversal = true;"
                    >
                        @csrf
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Reason for Reversal <span class="text-rose-600">*</span>
                            </label>
                            <textarea 
                                name="reversal_reason" 
                                rows="3" 
                                x-model="reversalReason"
                                required
                                placeholder="State reason (e.g. incorrect amount entered, duplicate entry, cheque bounced)..."
                                class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500"
                            ></textarea>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-3 border-t border-gray-100">
                            <button 
                                type="button" 
                                @click="reversalModalOpen = false" 
                                :disabled="submittingReversal"
                                class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition cursor-pointer"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit" 
                                :disabled="submittingReversal"
                                class="inline-flex items-center px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-lg transition shadow-xs cursor-pointer disabled:opacity-50"
                            >
                                <span x-show="!submittingReversal">Confirm Reversal</span>
                                <span x-show="submittingReversal" class="inline-flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    Reversing...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
