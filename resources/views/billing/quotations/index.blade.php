@extends('layouts.app')

@section('title', 'Quotations')

@section('content')
<div class="space-y-6">
    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Quotations</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Manage price estimates, service proposals, and conversions for customer vehicles.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <a 
                href="{{ route('billing.quotations.create') }}" 
                class="inline-flex items-center justify-center px-4 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white font-bold text-sm rounded-lg shadow-xs transition min-h-[44px] cursor-pointer"
            >
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Quotation
            </a>
        </div>
    </div>

    <!-- Quick Status Filter Tabs -->
    <div class="border-b border-gray-200 overflow-x-auto thin-scrollbar">
        <nav class="flex space-x-4 min-w-max">
            @php
                $tabs = [
                    'all' => ['label' => 'All Quotations', 'count' => $counts['all']],
                    'draft' => ['label' => 'Drafts', 'count' => $counts['draft']],
                    'sent' => ['label' => 'Sent', 'count' => $counts['sent']],
                    'accepted' => ['label' => 'Accepted', 'count' => $counts['accepted']],
                    'converted' => ['label' => 'Converted', 'count' => $counts['converted']],
                    'rejected' => ['label' => 'Rejected', 'count' => $counts['rejected']],
                ];
            @endphp

            @foreach($tabs as $key => $tab)
                <a 
                    href="{{ route('billing.quotations.index', array_merge(request()->query(), ['status' => $key, 'page' => 1])) }}"
                    class="py-3 px-2 border-b-2 font-semibold text-xs sm:text-sm flex items-center space-x-2 transition {{ $status === $key ? 'border-[#0B1849] text-[#0B1849]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                >
                    <span>{{ $tab['label'] }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold {{ $status === $key ? 'bg-[#EEF1FA] text-[#0B1849]' : 'bg-gray-100 text-gray-600' }}">
                        {{ $tab['count'] }}
                    </span>
                </a>
            @endforeach
        </nav>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs">
        <form method="GET" action="{{ route('billing.quotations.index') }}" class="flex flex-col sm:flex-row gap-3">
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
                    placeholder="Search by Quotation #, Customer Name, Phone, Vehicle Number..." 
                    class="block w-full pl-10 pr-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] placeholder:text-gray-400"
                />
            </div>

            <div class="flex items-center space-x-2">
                <button 
                    type="submit" 
                    class="px-5 py-2.5 text-sm font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg transition shadow-xs cursor-pointer focus:outline-none min-h-[44px]"
                >
                    Search
                </button>

                @if($search !== '')
                    <a 
                        href="{{ route('billing.quotations.index', ['status' => $status]) }}" 
                        class="px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition min-h-[44px] flex items-center justify-center"
                    >
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Quotations Table Card -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
        <div class="responsive-table-wrapper">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/75">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Quotation #</th>
                        <th scope="col" class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date & Validity</th>
                        <th scope="col" class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Customer / Company</th>
                        <th scope="col" class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Vehicle</th>
                        <th scope="col" class="px-5 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Grand Total (LKR)</th>
                        <th scope="col" class="px-5 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-5 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($quotations as $quotation)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <a href="{{ route('billing.quotations.show', $quotation) }}" class="text-sm font-bold text-[#0B1849] hover:underline">
                                    #{{ $quotation->quotation_number }}
                                </a>
                                @if($quotation->reference)
                                    <p class="text-xs text-gray-400 mt-0.5">Ref: {{ $quotation->reference }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-gray-600">
                                <div>{{ $quotation->quotation_date ? $quotation->quotation_date->format('M d, Y') : '' }}</div>
                                @if($quotation->expiry_date)
                                    <div class="text-[11px] text-gray-400 mt-0.5">Exp: {{ $quotation->expiry_date->format('M d, Y') }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $quotation->customer->customer_name }}
                                </div>
                                @if($quotation->customer->customer_type === 'company' && $quotation->customer->company_name)
                                    <div class="text-xs text-blue-700 font-medium mt-0.5 flex items-center space-x-1">
                                        <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span>{{ $quotation->customer->company_name }}</span>
                                    </div>
                                @endif
                                <div class="text-xs text-gray-400 mt-0.5">{{ $quotation->customer->phone }}</div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($quotation->vehicle)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-gray-100 text-gray-800 border border-gray-200">
                                        {{ $quotation->vehicle->vehicle_number }}
                                    </span>
                                    @if($quotation->vehicle->make_model)
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $quotation->vehicle->make_model }}</div>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400 italic">No Vehicle Linked</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-black text-gray-900">
                                    {{ number_format($quotation->grand_total, 2) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                @if($quotation->status === 'draft')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">Draft</span>
                                @elseif($quotation->status === 'sent')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Sent</span>
                                @elseif($quotation->status === 'accepted')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Accepted</span>
                                @elseif($quotation->status === 'converted')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">Invoiced</span>
                                @elseif($quotation->status === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">Rejected</span>
                                @elseif($quotation->status === 'expired')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Expired</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600">{{ ucfirst($quotation->status) }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right text-xs font-semibold space-x-2">
                                <a 
                                    href="{{ route('billing.quotations.show', $quotation) }}" 
                                    class="text-[#0B1849] hover:underline"
                                >
                                    View
                                </a>

                                <a 
                                    href="{{ route('billing.quotations.pdf', $quotation) }}" 
                                    class="text-gray-500 hover:text-gray-800"
                                    title="Download PDF"
                                >
                                    PDF
                                </a>

                                <a 
                                    href="{{ route('billing.quotations.thermal', $quotation) }}" 
                                    target="_blank"
                                    class="inline-flex items-center text-gray-500 hover:text-[#0B1849]"
                                    title="Print 80mm Thermal Quotation"
                                >
                                    <svg class="w-3.5 h-3.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    Print
                                </a>

                                @if($quotation->canEdit())
                                    <a 
                                        href="{{ route('billing.quotations.edit', $quotation) }}" 
                                        class="text-blue-600 hover:underline"
                                    >
                                        Edit
                                    </a>
                                @endif

                                @if($quotation->canConvert())
                                    <form method="POST" action="{{ route('billing.quotations.convert-to-invoice', $quotation) }}" class="inline" onsubmit="return confirm('Convert Quotation #{{ $quotation->quotation_number }} to an official Invoice?');">
                                        @csrf
                                        <button type="submit" class="text-emerald-700 hover:underline font-bold cursor-pointer">
                                            Invoice
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">
                                <svg class="w-10 h-10 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
                                </svg>
                                No quotations found matching your criteria.
                                <div class="mt-2">
                                    <a href="{{ route('billing.quotations.create') }}" class="text-[#0B1849] font-bold hover:underline">
                                        Create the first quotation &rarr;
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($quotations->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $quotations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
