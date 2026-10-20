@extends('layouts.app')

@section('title', 'Purchasing (GRN)')

@section('content')
    <div>
        <!-- Page Header -->
        <x-page-header 
            title="Purchasing (GRN)" 
            description="Manage goods received notes, vendor invoices, and stock receipts."
        >
            <x-slot:actions>
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                        Total: {{ $totalCount }} {{ Str::plural('GRN', $totalCount) }}
                    </span>

                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono">
                        Received: LKR {{ number_format($confirmedTotalValue, 2) }}
                    </span>

                    <a 
                        href="{{ route('inventory.grn.create') }}" 
                        class="inline-flex items-center justify-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-sm rounded-lg shadow-xs transition duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B1849]"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create GRN
                    </a>
                </div>
            </x-slot:actions>
        </x-page-header>

        <!-- Search Bar with Supplier & Status Filters -->
        <div class="mb-6 bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <form method="GET" action="{{ route('inventory.grn.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search text -->
                <div class="lg:col-span-2 relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search }}" 
                        placeholder="Search GRN #, Invoice #, Supplier..."
                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                    />
                </div>

                <!-- Supplier filter -->
                <div>
                    <select 
                        name="supplier_id" 
                        class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ $supplierId == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status filter -->
                <div>
                    <select 
                        name="status" 
                        class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                        <option value="">All Statuses</option>
                        <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed Only</option>
                        <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft Only</option>
                    </select>
                </div>

                <!-- Action buttons -->
                <div class="flex items-center space-x-2">
                    <button 
                        type="submit" 
                        class="flex-1 px-4 py-2 text-sm font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg transition shadow-xs text-center cursor-pointer"
                    >
                        Filter
                    </button>
                    @if($search || $supplierId || $status || $dateFrom || $dateTo)
                        <a 
                            href="{{ route('inventory.grn.index') }}" 
                            class="px-3 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- GRN Table -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                GRN Number
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Supplier
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Invoice # & Date
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Total (LKR)
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Stock Status
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Payment
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($grns as $grn)
                            <tr class="hover:bg-gray-50/70 transition">
                                <!-- GRN Number -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">
                                        <a href="{{ route('inventory.grn.show', $grn) }}" class="text-[#0B1849] hover:underline">
                                            {{ $grn->grn_number }}
                                        </a>
                                    </div>
                                    @if($grn->postedBy)
                                        <div class="text-[11px] text-gray-400">Posted by {{ $grn->postedBy->name }}</div>
                                    @endif
                                </td>

                                <!-- Supplier -->
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $grn->supplier ? $grn->supplier->name : 'N/A' }}
                                    </div>
                                    @if($grn->supplier && $grn->supplier->mobile)
                                        <div class="text-xs text-gray-500">{{ $grn->supplier->mobile }}</div>
                                    @endif
                                </td>

                                <!-- Invoice # & Date -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <div class="font-medium text-gray-900">{{ $grn->supplier_invoice_number ?: 'No Inv #' }}</div>
                                    <div class="text-xs text-gray-500">{{ $grn->received_date->format('M d, Y') }}</div>
                                </td>

                                <!-- Total Amount -->
                                <td class="px-6 py-4 whitespace-nowrap text-right font-mono font-bold text-sm text-gray-900">
                                    {{ number_format($grn->total_amount, 2) }}
                                </td>

                                <!-- Stock Status -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($grn->status === 'confirmed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Confirmed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            Draft
                                        </span>
                                    @endif
                                </td>

                                <!-- Payment Status -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php $payStatus = $grn->payment_status; @endphp
                                    @if($payStatus === 'Paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                            Paid
                                        </span>
                                    @elseif($payStatus === 'Partially Paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                            Partial
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            Unpaid
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a 
                                        href="{{ route('inventory.grn.show', $grn) }}" 
                                        class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-md text-[#0B1849] bg-blue-50 hover:bg-blue-100 border border-blue-200 transition"
                                    >
                                        View
                                    </a>

                                    <a 
                                        href="{{ route('inventory.grn.print', $grn) }}" 
                                        target="_blank"
                                        class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 transition"
                                        title="Print GRN"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <x-empty-state 
                                        title="No Goods Received Notes found." 
                                        description="Receive purchase orders from vendors into your warehouse and inventory."
                                    >
                                        <x-slot:action>
                                            <a 
                                                href="{{ route('inventory.grn.create') }}" 
                                                class="inline-flex items-center justify-center px-5 py-2.5 text-xs font-semibold rounded-lg text-white bg-[#0B1849] hover:bg-[#15286e] shadow-xs transition cursor-pointer"
                                            >
                                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Create First GRN
                                            </a>
                                        </x-slot:action>
                                    </x-empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($grns->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                    {{ $grns->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
