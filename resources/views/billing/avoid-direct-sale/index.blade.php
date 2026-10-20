@extends('layouts.app')

@section('title', 'Avoid Direct Sale')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ showConfirmModal: false }">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-4">
        <h1 class="text-2xl font-bold text-[#0B1849] tracking-tight">Avoid Direct Sale</h1>
        <p class="text-sm text-gray-500 mt-1">Permanently reverse an incorrect Direct POS Sale and restore inventory.</p>
    </div>

    <!-- Search Card -->
    <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-5">
        <form method="GET" action="{{ route('billing.avoid-direct-sale.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
            <div class="flex-1">
                <label for="search" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                    Invoice or Sale Number
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        id="search" 
                        value="{{ $search }}" 
                        placeholder="e.g. INV-202610-0001 or DS-202610-0001" 
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm text-gray-900 focus:bg-white focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] transition"
                        required
                        autofocus
                    >
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button 
                    type="submit" 
                    class="w-full sm:w-auto px-5 py-2.5 bg-[#0B1849] hover:bg-[#122368] text-white text-sm font-semibold rounded-lg shadow-xs transition flex items-center justify-center gap-2 cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Search</span>
                </button>
                @if($search !== '')
                    <a 
                        href="{{ route('billing.avoid-direct-sale.index') }}" 
                        class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if(isset($error) && $error)
        <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-sm flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ $error }}</span>
        </div>
    @endif

    <!-- Direct Sale Result Details -->
    @if($directSale)
        <div class="bg-white rounded-xl shadow-xs border border-gray-200 divide-y divide-gray-100">
            <!-- Header details banner -->
            <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Sale Details</span>
                    <h2 class="text-lg font-bold text-gray-900 mt-0.5">
                        Sale #{{ $directSale->sale_number }}
                        @if($directSale->invoice)
                            <span class="text-gray-400 font-normal">|</span> Invoice #{{ $directSale->invoice->invoice_number }}
                        @endif
                    </h2>
                </div>
                <div>
                    @if($directSale->status === 'cancelled')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                            Cancelled / Avoided
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                            Active Completed Sale
                        </span>
                    @endif
                </div>
            </div>

            <!-- Cancellation Info Banner (if cancelled) -->
            @if($directSale->status === 'cancelled')
                <div class="bg-red-50 p-4 border-l-4 border-red-500 text-sm text-red-900">
                    <p class="font-bold">This sale was avoided on {{ $directSale->cancelled_at ? $directSale->cancelled_at->format('d M Y, h:i A') : 'N/A' }}.</p>
                    <p class="mt-1"><span class="font-semibold">Reason:</span> {{ $directSale->cancellation_reason ?? 'Not specified' }}</p>
                    @if($directSale->cancelledBy)
                        <p class="text-xs text-red-700 mt-0.5">Authorised by: {{ $directSale->cancelledBy->name }}</p>
                    @endif
                    @if($directSale->stock_reversed)
                        <p class="text-xs font-semibold text-emerald-700 mt-1">✓ Stock restored to original FIFO batches.</p>
                    @endif
                </div>
            @endif

            <!-- Metadata Grid -->
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 bg-gray-50/50 text-sm">
                <div>
                    <span class="text-xs text-gray-500 block">Sale Date</span>
                    <span class="font-semibold text-gray-800">{{ $directSale->sale_date ? $directSale->sale_date->format('d M Y') : '-' }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Customer</span>
                    <span class="font-semibold text-gray-800">{{ $directSale->customer?->customer_name ?? 'Walk-in Customer' }}</span>
                    @if($directSale->customer?->phone)
                        <span class="text-xs text-gray-400 block">{{ $directSale->customer->phone }}</span>
                    @endif
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Payment Method</span>
                    <span class="font-semibold text-gray-800 capitalize">{{ str_replace('_', ' ', $directSale->payment_method) }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Total Amount</span>
                    <span class="font-bold text-gray-900 text-base">LKR {{ number_format($directSale->grand_total, 2) }}</span>
                </div>
            </div>

            <!-- Products Sold Table -->
            <div class="p-5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Sold Products</h3>
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-2.5">Product</th>
                                <th class="px-4 py-2.5 text-center">Quantity</th>
                                <th class="px-4 py-2.5 text-right">Unit Price</th>
                                <th class="px-4 py-2.5 text-right">Total</th>
                                <th class="px-4 py-2.5 text-left">FIFO Batches</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse($directSale->items as $item)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-gray-900">{{ $item->product_name }}</div>
                                        @if($item->product_sku)
                                            <div class="text-xs text-gray-400">SKU: {{ $item->product_sku }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center font-medium">
                                        {{ number_format($item->quantity, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        LKR {{ number_format($item->unit_price, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-900">
                                        LKR {{ number_format($item->line_total, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-500">
                                        @if(!empty($item->batch_allocations) && is_array($item->batch_allocations))
                                            @foreach($item->batch_allocations as $alloc)
                                                <span class="inline-block bg-gray-100 text-gray-700 px-1.5 py-0.5 rounded text-[11px] mr-1">
                                                    {{ $alloc['batch_number'] ?? ('Batch #' . $alloc['batch_id']) }} ({{ $alloc['quantity'] }})
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-center text-gray-400">No items recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-50 font-bold text-gray-900 text-sm border-t border-gray-200">
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-right">Subtotal:</td>
                                <td class="px-4 py-2 text-right">LKR {{ number_format($directSale->subtotal, 2) }}</td>
                                <td></td>
                            </tr>
                            @if($directSale->discount_amount > 0)
                            <tr>
                                <td colspan="3" class="px-4 py-1 text-right text-red-600 font-medium">Discount:</td>
                                <td class="px-4 py-1 text-right text-red-600">- LKR {{ number_format($directSale->discount_amount, 2) }}</td>
                                <td></td>
                            </tr>
                            @endif
                            @if($directSale->tax_amount > 0)
                            <tr>
                                <td colspan="3" class="px-4 py-1 text-right text-gray-600 font-medium">Tax:</td>
                                <td class="px-4 py-1 text-right">LKR {{ number_format($directSale->tax_amount, 2) }}</td>
                                <td></td>
                            </tr>
                            @endif
                            <tr class="border-t border-gray-200 text-base">
                                <td colspan="3" class="px-4 py-2 text-right">Grand Total:</td>
                                <td class="px-4 py-2 text-right text-[#0B1849]">LKR {{ number_format($directSale->grand_total, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Action Buttons Footer -->
            <div class="p-5 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
                <a 
                    href="{{ route('billing.avoid-direct-sale.index') }}" 
                    class="w-full sm:w-auto px-4 py-2.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-semibold rounded-lg text-center transition"
                >
                    Cancel
                </a>

                @if($directSale->status !== 'cancelled')
                    <button 
                        type="button" 
                        @click="showConfirmModal = true"
                        class="w-full sm:w-auto px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg shadow-xs transition flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Avoid Sale</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Avoid Sale Confirmation Modal -->
        @if($directSale->status !== 'cancelled')
            <div 
                x-show="showConfirmModal" 
                x-cloak 
                class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4"
                style="display: none;"
            >
                <div 
                    @click.away="showConfirmModal = false" 
                    class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-4"
                >
                    <div class="flex items-start gap-3">
                        <div class="p-2.5 bg-red-100 text-red-600 rounded-full shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Confirm Avoid Direct Sale</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Sale #{{ $directSale->sale_number }} • LKR {{ number_format($directSale->grand_total, 2) }}</p>
                        </div>
                    </div>

                    <p class="text-sm text-gray-600 leading-relaxed">
                        This action permanently cancels this direct sale and its linked financial records. Active sales revenue will be reversed.
                    </p>

                    <form method="POST" action="{{ route('billing.avoid-direct-sale.cancel', $directSale) }}" class="space-y-4">
                        @csrf

                        <!-- Cancellation Reason -->
                        <div>
                            <label for="cancellation_reason" class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">
                                Cancellation Reason <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                name="cancellation_reason" 
                                id="cancellation_reason" 
                                rows="3" 
                                class="w-full p-3 bg-gray-50 border border-gray-300 rounded-lg text-sm text-gray-900 focus:bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 transition" 
                                placeholder="Explain why this sale is being cancelled (e.g. incorrect items scanned, customer returned goods)..."
                                required
                            ></textarea>
                        </div>

                        <!-- FIFO Stock Restoration Checkbox -->
                        <div class="p-3 bg-slate-50 border border-gray-200 rounded-lg space-y-2">
                            <label class="flex items-start gap-2.5 cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    name="restore_stock" 
                                    value="1" 
                                    checked 
                                    class="mt-1 h-4 w-4 rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849]"
                                >
                                <div class="text-xs">
                                    <span class="font-bold text-gray-800 block">Restore physical stock to original FIFO batches</span>
                                    <span class="text-gray-500">Adds returned items back to their exact original batches and purchase costs.</span>
                                </div>
                            </label>
                        </div>

                        <!-- Confirmation Checkbox -->
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="confirm_avoid" 
                                value="1" 
                                required 
                                class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500"
                            >
                            <span class="text-xs font-semibold text-gray-700">
                                I confirm the permanent cancellation of this sale
                            </span>
                        </label>

                        <!-- Modal Action Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                            <button 
                                type="button" 
                                @click="showConfirmModal = false" 
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition"
                            >
                                Back
                            </button>
                            <button 
                                type="submit" 
                                class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg shadow-xs transition"
                            >
                                Yes, Avoid Sale
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
