@extends('layouts.app')

@section('title', 'Avoid Service Sale')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ showConfirmModal: false }">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-4">
        <h1 class="text-2xl font-bold text-[#0B1849] tracking-tight">Avoid Service Sale</h1>
        <p class="text-sm text-gray-500 mt-1">Cancel an incorrect vehicle service sale while preserving vehicle and service history.</p>
    </div>

    <!-- Search Card -->
    <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-5">
        <form method="GET" action="{{ route('billing.avoid-service-sale.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
            <div class="flex-1">
                <label for="search" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                    Invoice / Job / Vehicle Number
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        id="search" 
                        value="{{ $search }}" 
                        placeholder="e.g. INV-202610-0002, JOB-202610-0001, or WP-CAB-1234" 
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
                        href="{{ route('billing.avoid-service-sale.index') }}" 
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

    <!-- Service Sale Result Details -->
    @if($invoice)
        <div class="bg-white rounded-xl shadow-xs border border-gray-200 divide-y divide-gray-100">
            <!-- Header details banner -->
            <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Service Invoice</span>
                    <h2 class="text-lg font-bold text-gray-900 mt-0.5">
                        Invoice #{{ $invoice->invoice_number }}
                        @if($invoice->jobCard)
                            <span class="text-gray-400 font-normal">|</span> Job #{{ $invoice->jobCard->job_number }}
                        @endif
                    </h2>
                </div>
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

            <!-- Cancellation Ineligibility / Cancellation Info Banner -->
            @if($invoice->status === 'cancelled')
                <div class="bg-red-50 p-4 border-l-4 border-red-500 text-sm text-red-900">
                    <p class="font-bold">Invoice found, but this transaction cannot currently be cancelled. This Service Sale was already avoided on {{ $invoice->cancelled_at ? $invoice->cancelled_at->format('d M Y, h:i A') : 'N/A' }}.</p>
                    <p class="mt-1"><span class="font-semibold">Reason:</span> {{ $invoice->cancellation_reason ?? 'Not specified' }}</p>
                    @if($invoice->cancelledBy)
                        <p class="text-xs text-red-700 mt-0.5">Authorised by: {{ $invoice->cancelledBy->name }}</p>
                    @endif
                    <p class="text-xs text-gray-600 mt-1">Vehicle and Job Card service records remain intact.</p>
                </div>
            @elseif(isset($canCancel) && ! $canCancel)
                <div class="bg-amber-50 p-4 border-l-4 border-amber-500 text-sm text-amber-900">
                    <p class="font-bold">Invoice found, but this transaction cannot currently be cancelled.</p>
                    @if(isset($cancellationIneligibleReason) && $cancellationIneligibleReason)
                        <p class="text-xs text-amber-800 mt-0.5">{{ $cancellationIneligibleReason }}</p>
                    @endif
                </div>
            @endif

            <!-- Service & Vehicle Metadata Grid -->
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 bg-gray-50/50 text-sm">
                <div>
                    <span class="text-xs text-gray-500 block">Job Number</span>
                    <span class="font-bold text-gray-900">{{ $invoice->jobCard?->job_number ?? $invoice->reference ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Vehicle Number</span>
                    <span class="font-bold text-[#0B1849]">{{ $invoice->jobCard?->vehicle?->vehicle_number ?? 'N/A' }}</span>
                    @if($invoice->jobCard?->vehicle?->vehicleType)
                        <span class="text-xs text-gray-400 block">{{ $invoice->jobCard->vehicle->vehicleType->name }}</span>
                    @endif
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Customer</span>
                    <span class="font-semibold text-gray-800">{{ $invoice->customer?->customer_name ?? 'N/A' }}</span>
                    @if($invoice->customer?->phone)
                        <span class="text-xs text-gray-400 block">{{ $invoice->customer->phone }}</span>
                    @endif
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Service Date</span>
                    <span class="font-semibold text-gray-800">{{ $invoice->invoice_date ? $invoice->invoice_date->format('d M Y') : '-' }}</span>
                </div>
            </div>

            <!-- Services Performed Section -->
            <div class="p-5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Services Charges</h3>
                <div class="overflow-x-auto border border-gray-200 rounded-lg mb-4">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-2">Service</th>
                                <th class="px-4 py-2 text-center">Qty</th>
                                <th class="px-4 py-2 text-right">Unit Price</th>
                                <th class="px-4 py-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @php
                                $services = $invoice->items->where('item_type', 'service');
                            @endphp
                            @forelse($services as $item)
                                <tr>
                                    <td class="px-4 py-2.5 font-medium text-gray-900">{{ $item->item_name }}</td>
                                    <td class="px-4 py-2.5 text-center">{{ number_format($item->quantity, 1) }}</td>
                                    <td class="px-4 py-2.5 text-right">LKR {{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-4 py-2.5 text-right font-semibold text-gray-900">LKR {{ number_format($item->line_total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-2 text-center text-gray-400 text-xs">No service items recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Products / Parts Section -->
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Products & Parts Used</h3>
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-2">Part / Product</th>
                                <th class="px-4 py-2 text-center">Qty</th>
                                <th class="px-4 py-2 text-right">Unit Price</th>
                                <th class="px-4 py-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @php
                                $products = $invoice->items->where('item_type', 'product');
                            @endphp
                            @forelse($products as $item)
                                <tr>
                                    <td class="px-4 py-2.5 font-medium text-gray-900">{{ $item->item_name }}</td>
                                    <td class="px-4 py-2.5 text-center">{{ number_format($item->quantity, 1) }}</td>
                                    <td class="px-4 py-2.5 text-right">LKR {{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-4 py-2.5 text-right font-semibold text-gray-900">LKR {{ number_format($item->line_total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-2 text-center text-gray-400 text-xs">No products / parts on this invoice.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-50 font-bold text-gray-900 text-sm border-t border-gray-200">
                            <tr class="text-base">
                                <td colspan="3" class="px-4 py-2 text-right">Grand Total:</td>
                                <td class="px-4 py-2 text-right text-[#0B1849]">LKR {{ number_format($invoice->grand_total, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-4 py-1.5 text-right text-xs font-medium text-gray-500">Amount Paid:</td>
                                <td class="px-4 py-1.5 text-right text-xs font-semibold text-emerald-700">LKR {{ number_format($invoice->amount_paid, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-4 py-1.5 text-right text-xs font-medium text-gray-500">Balance Amount:</td>
                                <td class="px-4 py-1.5 text-right text-xs font-bold text-red-600">LKR {{ number_format($invoice->balance_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Action Buttons Footer -->
            <div class="p-5 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
                <a 
                    href="{{ route('billing.avoid-service-sale.index') }}" 
                    class="w-full sm:w-auto px-4 py-2.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-semibold rounded-lg text-center transition"
                >
                    Cancel
                </a>

                @if(isset($canCancel) && $canCancel)
                    <button 
                        type="button" 
                        @click="showConfirmModal = true"
                        class="w-full sm:w-auto px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg shadow-xs transition flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Avoid Service Sale</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Avoid Service Sale Confirmation Modal -->
        @if(isset($canCancel) && $canCancel)
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
                            <h3 class="text-lg font-bold text-gray-900">Confirm Avoid Service Sale</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Invoice #{{ $invoice->invoice_number }} • LKR {{ number_format($invoice->grand_total, 2) }}</p>
                        </div>
                    </div>

                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-900 leading-relaxed space-y-1">
                        <p class="font-bold">✓ Preserved Data Guarantee:</p>
                        <p>Vehicle Registration ({{ $invoice->jobCard?->vehicle?->vehicle_number ?? 'Vehicle' }}) and the Job Card history are permanently preserved and will NOT be deleted.</p>
                    </div>

                    <form method="POST" action="{{ route('billing.avoid-service-sale.cancel', $invoice) }}" class="space-y-4">
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
                                placeholder="State reason for cancelling this service invoice..."
                                required
                            ></textarea>
                        </div>

                        <!-- Return parts checkbox (optional, unchecked by default) -->
                        <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                            <label class="flex items-start gap-2.5 cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    name="restore_parts_stock" 
                                    value="1" 
                                    class="mt-0.5 h-4 w-4 rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849]"
                                >
                                <div class="text-xs">
                                    <span class="font-bold text-gray-800 block">Physically uninstalled parts returned to stock (FIFO)</span>
                                    <span class="text-gray-500">Only check if parts were removed and accepted back into saleable inventory.</span>
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
                                I confirm the permanent cancellation of this service sale
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
                                Yes, Avoid Service Sale
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
