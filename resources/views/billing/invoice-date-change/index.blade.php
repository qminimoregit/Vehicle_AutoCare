@extends('layouts.app')

@section('title', 'Invoice Date Change')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-4">
        <h1 class="text-2xl font-bold text-[#0B1849] tracking-tight">Invoice Date Change</h1>
        <p class="text-sm text-gray-500 mt-1">Authorized correction of invoice issue dates with preserved audit trail.</p>
    </div>

    <!-- Search Card -->
    <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-5">
        <form method="GET" action="{{ route('billing.invoice-date-change.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
            <div class="flex-1">
                <label for="search" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                    Invoice Number
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        id="search" 
                        value="{{ $search }}" 
                        placeholder="e.g. INV-202610-0001" 
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
                        href="{{ route('billing.invoice-date-change.index') }}" 
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

    <!-- Invoice Details & Date Change Form -->
    @if($invoice)
        <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden divide-y divide-gray-100">
            <!-- Header Summary -->
            <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-50/50">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Invoice Details</span>
                    <h2 class="text-lg font-bold text-gray-900 mt-0.5">
                        Invoice #{{ $invoice->invoice_number }}
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Customer: <span class="font-semibold text-gray-700">{{ $invoice->customer->customer_name }}</span>
                        @if($invoice->jobCard && $invoice->jobCard->vehicle)
                            • Vehicle: <span class="font-semibold text-gray-700">{{ $invoice->jobCard->vehicle->vehicle_number }}</span>
                        @endif
                    </p>
                </div>
                <div class="text-left sm:text-right">
                    <span class="text-xs text-gray-400 block">Grand Total</span>
                    <span class="text-base font-bold text-[#0B1849]">LKR {{ number_format($invoice->grand_total, 2) }}</span>
                </div>
            </div>

            @if($invoice->status === 'cancelled')
                <div class="p-5 bg-red-50 border-l-4 border-red-500 text-sm text-red-900">
                    <p class="font-bold">This invoice is cancelled.</p>
                    <p class="text-xs mt-1">Date changes cannot be performed on cancelled or voided invoices.</p>
                </div>
            @else
                <!-- Edit Form -->
                <form method="POST" action="{{ route('billing.invoice-date-change.update', $invoice) }}" class="p-6 space-y-6">
                    @csrf

                    <!-- Current Date Display -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 block">Current Invoice Date</span>
                                <span class="text-lg font-bold text-gray-900">
                                    {{ $invoice->invoice_date ? $invoice->invoice_date->format('d F Y') : '-' }}
                                </span>
                            </div>
                            @if($invoice->original_invoice_date && $invoice->original_invoice_date->toDateString() !== $invoice->invoice_date->toDateString())
                                <div class="text-xs text-gray-500">
                                    <span class="block">Original Issue Date:</span>
                                    <span class="font-semibold text-gray-700">{{ $invoice->original_invoice_date->format('d M Y') }}</span>
                                </div>
                            @endif
                        </div>

                        @if($invoice->date_changed_at)
                            <div class="pt-2 border-t border-slate-200 text-xs text-gray-500">
                                <span>Last modified on {{ $invoice->date_changed_at->format('d M Y, h:i A') }}</span>
                                @if($invoice->dateChangedBy)
                                    <span>by {{ $invoice->dateChangedBy->name }}</span>
                                @endif
                                @if($invoice->date_change_reason)
                                    <span class="block text-gray-600 italic mt-0.5">"{{ $invoice->date_change_reason }}"</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- New Date Input -->
                    <div>
                        <label for="new_invoice_date" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                            New Invoice Date <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="new_invoice_date" 
                            id="new_invoice_date" 
                            max="{{ date('Y-m-d') }}" 
                            value="{{ old('new_invoice_date', $invoice->invoice_date ? $invoice->invoice_date->toDateString() : date('Y-m-d')) }}" 
                            class="w-full sm:w-72 px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm text-gray-900 focus:bg-white focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] transition"
                            required
                        >
                        <p class="text-xs text-gray-400 mt-1">Invoice dates cannot be set in the future or in locked accounting periods.</p>
                    </div>

                    <!-- Reason Input -->
                    <div>
                        <label for="reason" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                            Reason for Change <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="reason" 
                            id="reason" 
                            value="{{ old('reason') }}" 
                            placeholder="e.g. Authorized correction of customer billing date per manager approval..." 
                            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm text-gray-900 focus:bg-white focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] transition"
                            required
                        >
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a 
                            href="{{ route('billing.invoice-date-change.index') }}" 
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition"
                        >
                            Cancel
                        </a>
                        <button 
                            type="submit" 
                            class="px-6 py-2.5 bg-[#0B1849] hover:bg-[#122368] text-white text-sm font-bold rounded-lg shadow-xs transition cursor-pointer"
                        >
                            Update Date
                        </button>
                    </div>
                </form>
            @endif
        </div>
    @endif
</div>
@endsection
