@extends('layouts.app')

@section('title', 'Quotation #' . $quotation->quotation_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-start sm:items-center space-x-3">
            <a href="{{ route('billing.quotations.index') }}" class="text-gray-400 hover:text-gray-600 transition mt-1 sm:mt-0 p-1 -ml-1">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">
                        Quotation #{{ $quotation->quotation_number }}
                    </h1>
                    @if($quotation->status === 'draft')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">Draft</span>
                    @elseif($quotation->status === 'sent')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Sent to Client</span>
                    @elseif($quotation->status === 'accepted')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Accepted</span>
                    @elseif($quotation->status === 'converted')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">Invoiced</span>
                    @elseif($quotation->status === 'rejected')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">Rejected</span>
                    @elseif($quotation->status === 'expired')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Expired</span>
                    @endif
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    Created on {{ $quotation->quotation_date ? $quotation->quotation_date->format('M d, Y') : '' }}
                    @if($quotation->branch)
                        • {{ $quotation->branch->branch_name }}
                    @endif
                    @if($quotation->creator)
                        • Prepared by {{ $quotation->creator->name }}
                    @endif
                </p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <!-- Print & PDF -->
            <a 
                href="{{ route('billing.quotations.print', $quotation) }}" 
                target="_blank"
                class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 text-gray-700 font-bold text-xs rounded-lg hover:bg-gray-50 shadow-xs transition"
                title="Print standard A4 Quotation"
            >
                <svg class="w-3.5 h-3.5 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                A4
            </a>

            <a 
                href="{{ route('billing.quotations.thermal', $quotation) }}" 
                @click.prevent="window.AutoCarePrinter ? window.AutoCarePrinter.printUrl('{{ route('billing.quotations.thermal', $quotation) }}', { type: 'bill', title: 'Quotation #{{ $quotation->quotation_number }}', documentId: {{ $quotation->id }}, documentNumber: '{{ $quotation->quotation_number }}', fallbackUrl: '{{ route('billing.quotations.thermal', $quotation) }}' }) : window.open('{{ route('billing.quotations.thermal', $quotation) }}', '_blank')"
                target="_blank"
                class="inline-flex items-center px-3 py-2 bg-indigo-50 border border-indigo-200 text-indigo-700 font-bold text-xs rounded-lg hover:bg-indigo-100 shadow-xs transition"
                title="Print 80mm Thermal Quotation"
            >
                <svg class="w-3.5 h-3.5 mr-1 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </a>

            <a 
                href="{{ route('billing.quotations.pdf', $quotation) }}" 
                class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 text-gray-700 font-bold text-xs rounded-lg hover:bg-gray-50 shadow-xs transition"
            >
                <svg class="w-3.5 h-3.5 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                PDF
            </a>

            <!-- Status Actions -->
            @if($quotation->status === 'draft')
                <form method="POST" action="{{ route('billing.quotations.send', $quotation) }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-xs rounded-lg transition cursor-pointer">
                        Mark Sent
                    </button>
                </form>
            @endif

            @if(in_array($quotation->status, ['draft', 'sent']))
                <form method="POST" action="{{ route('billing.quotations.accept', $quotation) }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs rounded-lg transition cursor-pointer">
                        Accept
                    </button>
                </form>

                <form method="POST" action="{{ route('billing.quotations.reject', $quotation) }}" class="inline" onsubmit="return confirm('Mark this quotation as rejected?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-xs rounded-lg transition cursor-pointer">
                        Reject
                    </button>
                </form>
            @endif

            @if($quotation->canEdit())
                <a 
                    href="{{ route('billing.quotations.edit', $quotation) }}" 
                    class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 text-gray-700 font-bold text-xs rounded-lg hover:bg-gray-50 shadow-xs transition"
                >
                    Edit
                </a>
            @endif

            <!-- Conversions -->
            @if($quotation->canConvert())
                <form method="POST" action="{{ route('billing.quotations.convert-to-invoice', $quotation) }}" class="inline" onsubmit="return confirm('Convert this quotation to an official Invoice?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white font-bold text-xs rounded-lg shadow-xs transition cursor-pointer">
                        Convert to Invoice
                    </button>
                </form>
            @endif

            @if($quotation->vehicle_id && $quotation->canCreateJobCard())
                <form method="POST" action="{{ route('billing.quotations.create-job-card', $quotation) }}" class="inline" onsubmit="return confirm('Create an active Job Card for vehicle {{ $quotation->vehicle->vehicle_number }}?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow-xs transition cursor-pointer">
                        Create Job Card
                    </button>
                </form>
            @endif

            <!-- Clone Action -->
            <form method="POST" action="{{ route('billing.quotations.clone', $quotation) }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-lg transition cursor-pointer" title="Clone as new draft">
                    Clone
                </button>
            </form>
        </div>
    </div>

    <!-- Conversion Notice Banners -->
    @if($quotation->convertedInvoice)
        <div class="p-4 rounded-xl bg-purple-50 border border-purple-200 text-purple-900 flex items-center justify-between">
            <div class="flex items-center space-x-2 text-xs">
                <svg class="w-5 h-5 text-purple-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>Converted to official Invoice: <strong>#{{ $quotation->convertedInvoice->invoice_number }}</strong></span>
            </div>
            <a href="{{ route('operations.invoices.show', $quotation->convertedInvoice) }}" class="text-xs font-bold text-purple-700 hover:underline">
                View Invoice &rarr;
            </a>
        </div>
    @endif

    @if($quotation->convertedJobCard)
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center justify-between">
            <div class="flex items-center space-x-2 text-xs">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>Workshop Job Card created: <strong>#{{ $quotation->convertedJobCard->job_number }}</strong></span>
            </div>
            <a href="{{ route('operations.job-cards.show', $quotation->convertedJobCard) }}" class="text-xs font-bold text-emerald-700 hover:underline">
                View Job Card &rarr;
            </a>
        </div>
    @endif

    <!-- Official Quotation Sheet Card -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-xs overflow-hidden p-6 sm:p-10 space-y-8">
        <!-- Header: Company & Quotation Meta -->
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
                    @if($company->registration_number)
                        <div><strong>BRN:</strong> {{ $company->registration_number }}</div>
                    @endif
                    @if($company->tax_number)
                        <div><strong>Tax/VAT:</strong> {{ $company->tax_number }}</div>
                    @endif
                </div>
            </div>

            <div class="sm:text-right">
                <span class="text-xs font-black uppercase tracking-wider text-gray-400">Price Quotation</span>
                <div class="text-2xl font-black text-[#0B1849] tracking-tight mt-0.5">
                    #{{ $quotation->quotation_number }}
                </div>
                <div class="mt-2 text-xs text-gray-600 space-y-1">
                    <div><strong>Date:</strong> {{ $quotation->quotation_date ? $quotation->quotation_date->format('M d, Y') : '' }}</div>
                    @if($quotation->expiry_date)
                        <div><strong>Valid Until:</strong> {{ $quotation->expiry_date->format('M d, Y') }}</div>
                    @endif
                    @if($quotation->reference)
                        <div><strong>Reference:</strong> {{ $quotation->reference }}</div>
                    @endif
                    @if($quotation->branch)
                        <div><strong>Branch:</strong> {{ $quotation->branch->branch_name }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recipient & Vehicle Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-50/75 p-5 rounded-xl border border-gray-100">
            <div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Prepared For</span>
                <div class="text-sm font-bold text-gray-900">
                    {{ $quotation->customer->customer_name }}
                </div>
                @if($quotation->customer->customer_type === 'company' && $quotation->customer->company_name)
                    <div class="text-xs text-blue-700 font-semibold mt-0.5 flex items-center space-x-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>{{ $quotation->customer->company_name }}</span>
                    </div>
                @endif
                <div class="text-xs text-gray-600 mt-1 space-y-0.5">
                    @if($quotation->customer->phone)
                        <div>Phone: {{ $quotation->customer->phone }}</div>
                    @endif
                    @if($quotation->customer->email)
                        <div>Email: {{ $quotation->customer->email }}</div>
                    @endif
                    @if($quotation->customer->address)
                        <div>Address: {{ $quotation->customer->address }}</div>
                    @endif
                </div>
            </div>

            <div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Target Vehicle / Scope</span>
                @if($quotation->vehicle)
                    <div class="flex items-center space-x-2">
                        <span class="px-2.5 py-1 rounded text-xs font-black bg-gray-900 text-white tracking-wide">
                            {{ $quotation->vehicle->vehicle_number }}
                        </span>
                        @if($quotation->vehicle->vehicleType)
                            <span class="text-xs text-gray-500 font-medium">({{ $quotation->vehicle->vehicleType->name }})</span>
                        @endif
                    </div>
                    <div class="text-xs text-gray-600 mt-1.5 space-y-0.5">
                        @if($quotation->vehicle->make_model)
                            <div>Model: {{ $quotation->vehicle->make_model }}</div>
                        @endif
                        @if($quotation->vehicle->current_mileage)
                            <div>Mileage: {{ number_format($quotation->vehicle->current_mileage) }} KM</div>
                        @endif
                    </div>
                @else
                    <div class="text-xs text-gray-400 italic">No vehicle assigned (General product or customer sale quote)</div>
                @endif

                @if($quotation->subject)
                    <div class="mt-2 text-xs">
                        <span class="text-gray-400">Subject:</span>
                        <span class="font-semibold text-gray-800 ml-1">{{ $quotation->subject }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="responsive-table-wrapper">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-200">
                        <th class="py-2.5 text-left w-8">#</th>
                        <th class="py-2.5 text-left">Description</th>
                        <th class="py-2.5 text-center w-20">Qty</th>
                        <th class="py-2.5 text-right w-28">Rate (LKR)</th>
                        <th class="py-2.5 text-right w-24">Discount</th>
                        <th class="py-2.5 text-right w-32">Total (LKR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs text-gray-800">
                    @foreach($quotation->items as $index => $item)
                        <tr>
                            <td class="py-3 text-gray-400">{{ $index + 1 }}</td>
                            <td class="py-3">
                                <div class="font-bold text-gray-900 flex items-center space-x-1.5">
                                    <span>{{ $item->item_name }}</span>
                                    @if($item->item_type === 'service')
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-blue-50 text-blue-700">SRV</span>
                                    @elseif($item->item_type === 'product')
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700">PART</span>
                                    @endif
                                </div>
                                @if($item->description)
                                    <p class="text-gray-500 text-[11px] mt-0.5 leading-relaxed">{{ $item->description }}</p>
                                @endif
                            </td>
                            <td class="py-3 text-center">
                                @quantity($item->quantity) {{ $item->unit }}
                            </td>
                            <td class="py-3 text-right">
                                {{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="py-3 text-right text-gray-500">
                                @if($item->discount_amount > 0)
                                    -{{ number_format($item->discount_amount, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-3 text-right font-bold text-gray-900">
                                {{ number_format($item->line_total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Financial Summary -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-6 border-t border-gray-100 pt-6">
            <div class="max-w-md space-y-3">
                @if($amountInWords)
                    <div class="p-3 rounded-lg bg-gray-50 text-xs text-gray-600 border border-gray-100">
                        <span class="font-bold text-gray-700 block text-[10px] uppercase tracking-wider mb-0.5">Amount in Words</span>
                        {{ $amountInWords }}
                    </div>
                @endif

                @if($quotation->notes)
                    <div class="text-xs text-gray-600">
                        <strong class="text-gray-700 block text-[10px] uppercase tracking-wider mb-0.5">Notes</strong>
                        <p class="whitespace-pre-line leading-relaxed">{{ $quotation->notes }}</p>
                    </div>
                @endif

                @if($quotation->terms_conditions)
                    <div class="text-xs text-gray-500">
                        <strong class="text-gray-700 block text-[10px] uppercase tracking-wider mb-0.5">Terms & Conditions</strong>
                        <p class="whitespace-pre-line leading-relaxed text-[11px]">{{ $quotation->terms_conditions }}</p>
                    </div>
                @endif
            </div>

            <div class="w-full sm:w-72 space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-gray-50">
                    <span class="text-gray-500">Subtotal</span>
                    <span class="font-bold text-gray-900">{{ number_format($quotation->subtotal, 2) }}</span>
                </div>

                @if($quotation->discount_amount > 0)
                    <div class="flex justify-between py-1 text-emerald-700 border-b border-gray-50">
                        <span>Discount {{ $quotation->discount_type === 'percentage' ? '(' . $quotation->discount_value . '%)' : '' }}</span>
                        <span class="font-bold">-{{ number_format($quotation->discount_amount, 2) }}</span>
                    </div>
                @endif

                @if($quotation->tax_amount > 0)
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Tax / VAT ({{ $quotation->tax_percentage }}%)</span>
                        <span class="font-bold text-gray-900">+{{ number_format($quotation->tax_amount, 2) }}</span>
                    </div>
                @endif

                @if($quotation->additional_charges > 0)
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Additional Charges</span>
                        <span class="font-bold text-gray-900">+{{ number_format($quotation->additional_charges, 2) }}</span>
                    </div>
                @endif

                <div class="flex justify-between py-2 border-t-2 border-gray-900 text-sm font-black">
                    <span class="text-gray-900">Grand Total (LKR)</span>
                    <span class="text-[#0B1849]">{{ number_format($quotation->grand_total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
