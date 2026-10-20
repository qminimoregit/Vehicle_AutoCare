@extends('layouts.app')

@section('title', 'GRN Details - ' . $grn->grn_number)

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
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

            <div class="overflow-x-auto">
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
                                    {{ number_format($item->purchase_quantity, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-sm text-emerald-700 font-semibold">
                                    {{ $item->free_quantity > 0 ? '+ ' . number_format($item->free_quantity, 2) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-sm text-gray-900 font-bold bg-gray-50/60">
                                    {{ number_format($item->total_physical_quantity, 2) }}
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

                <div class="overflow-x-auto">
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
                                        {{ number_format($batch->received_quantity, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono text-sm font-bold {{ $batch->remaining_quantity > 0 ? 'text-emerald-700' : 'text-gray-400' }}">
                                        {{ number_format($batch->remaining_quantity, 2) }}
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
        <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-6 py-4 bg-gray-50/80 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Supplier Invoice Payment Tracking</h3>
                    <p class="text-xs text-gray-500">Track paid amounts and settle vendor balances independently from stock receipts.</p>
                </div>
            </div>

            <!-- Financial Summary Bar -->
            <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-gray-200 border-b border-gray-200 bg-gray-50/30">
                <div class="p-4 text-center">
                    <span class="text-xs font-semibold text-gray-500 uppercase">Invoice Total</span>
                    <div class="text-lg font-mono font-bold text-gray-900 mt-0.5">
                        LKR {{ number_format($grn->total_amount, 2) }}
                    </div>
                </div>
                <div class="p-4 text-center">
                    <span class="text-xs font-semibold text-emerald-700 uppercase">Paid Amount</span>
                    <div class="text-lg font-mono font-bold text-emerald-700 mt-0.5">
                        LKR {{ number_format($grn->paid_amount, 2) }}
                    </div>
                </div>
                <div class="p-4 text-center">
                    <span class="text-xs font-semibold {{ $grn->outstanding_amount > 0 ? 'text-rose-700' : 'text-gray-500' }} uppercase">
                        Outstanding Balance
                    </span>
                    <div class="text-lg font-mono font-bold {{ $grn->outstanding_amount > 0 ? 'text-rose-700' : 'text-gray-700' }} mt-0.5">
                        LKR {{ number_format($grn->outstanding_amount, 2) }}
                    </div>
                </div>
            </div>

            <!-- Payments List -->
            <div class="p-6 space-y-4">
                @if($grn->payments->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="text-left text-xs font-bold text-gray-600 uppercase">
                                    <th class="py-2">Payment Date</th>
                                    <th class="py-2">Method</th>
                                    <th class="py-2">Reference / Cheque</th>
                                    <th class="py-2">Notes</th>
                                    <th class="py-2 text-right">Amount (LKR)</th>
                                    <th class="py-2 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 text-sm">
                                @foreach($grn->payments as $payment)
                                    <tr>
                                        <td class="py-2.5 font-medium text-gray-900">{{ $payment->payment_date->format('Y-m-d') }}</td>
                                        <td class="py-2.5 capitalize text-gray-700">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                                        <td class="py-2.5 font-mono text-gray-600">{{ $payment->reference_number ?: '-' }}</td>
                                        <td class="py-2.5 text-gray-500 text-xs">{{ $payment->notes ?: '-' }}</td>
                                        <td class="py-2.5 text-right font-mono font-bold text-gray-900">{{ number_format($payment->amount, 2) }}</td>
                                        <td class="py-2.5 text-right">
                                            @if(Auth::user()->isAdmin())
                                                <form 
                                                    method="POST" 
                                                    action="{{ route('inventory.supplier-payments.destroy', $payment) }}" 
                                                    class="inline-block"
                                                    onsubmit="return confirm('Void this payment record of LKR {{ number_format($payment->amount, 2) }}?')"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-semibold cursor-pointer">
                                                        Void
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-xs text-gray-400 italic">No payment transactions recorded for this supplier invoice yet.</p>
                @endif

                @if(Auth::user()->isAdmin() && $grn->outstanding_amount > 0)
                    <!-- Record Payment Form -->
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-3">Record Payment to Supplier</h4>
                        <form method="POST" action="{{ route('inventory.grn.payments.store', $grn) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end" novalidate>
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Payment Date *</label>
                                <input 
                                    type="date" 
                                    name="payment_date" 
                                    required 
                                    value="{{ date('Y-m-d') }}" 
                                    class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849]"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Amount (LKR) *</label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    min="0.01" 
                                    name="amount" 
                                    required 
                                    value="{{ $grn->outstanding_amount }}" 
                                    class="block w-full px-3 py-2 text-sm font-mono border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849]"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Payment Method *</label>
                                <select 
                                    name="payment_method" 
                                    required 
                                    class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] bg-white"
                                >
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="card">Card</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Reference / Cheque #</label>
                                <div class="flex items-center space-x-2">
                                    <input 
                                        type="text" 
                                        name="reference_number" 
                                        placeholder="Optional ref" 
                                        class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849]"
                                    />
                                    <button 
                                        type="submit" 
                                        class="px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-sm rounded-lg shadow-xs transition cursor-pointer whitespace-nowrap"
                                    >
                                        Save
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
