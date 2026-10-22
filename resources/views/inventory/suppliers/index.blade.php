@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
    <div>
        <!-- Page Header -->
        <x-page-header 
            title="Suppliers" 
            description="Manage vendor and supplier directory."
        >
            <x-slot:actions>
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                        Total: {{ $totalSuppliers }} {{ Str::plural('supplier', $totalSuppliers) }}
                    </span>

                    <a 
                        href="{{ route('inventory.suppliers.export', request()->query()) }}" 
                        class="inline-flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 shadow-2xs transition"
                    >
                        <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Excel
                    </a>

                    @if(Auth::user()->isAdmin())
                        <a 
                            href="{{ route('inventory.suppliers.create') }}" 
                            class="inline-flex items-center justify-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-sm rounded-lg shadow-xs transition duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B1849]"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Supplier
                        </a>
                    @endif
                </div>
            </x-slot:actions>
        </x-page-header>

        <!-- Search Bar with Status Filter -->
        <x-search-bar 
            :action="route('inventory.suppliers.index')" 
            :search="$search" 
            placeholder="Search Suppliers by name, mobile, reg number..."
            :resetUrl="route('inventory.suppliers.index')"
        >
            <div class="w-full sm:w-44">
                <select 
                    name="status" 
                    onchange="this.form.submit()"
                    class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                >
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                </select>
            </div>
        </x-search-bar>

        <!-- Suppliers Table -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
            <div class="responsive-table-wrapper">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Supplier
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Contact Numbers
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Address
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($suppliers as $supplier)
                            <tr class="hover:bg-gray-50/70 transition">
                                <!-- Supplier Name & Reg -->
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900">
                                        <a href="{{ route('inventory.suppliers.show', $supplier) }}" class="hover:underline text-[#0B1849]">
                                            {{ $supplier->name }}
                                        </a>
                                    </div>
                                    @if($supplier->registration_number)
                                        <div class="text-xs text-gray-500 mt-0.5">Reg: {{ $supplier->registration_number }}</div>
                                    @endif
                                </td>

                                <!-- Contact Numbers -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    @if($supplier->mobile)
                                        <div class="font-medium text-gray-900">{{ $supplier->mobile }}</div>
                                    @endif
                                    @if($supplier->office_phone)
                                        <div class="text-xs text-gray-500">Office: {{ $supplier->office_phone }}</div>
                                    @endif
                                    @if(!$supplier->mobile && !$supplier->office_phone)
                                        <span class="text-gray-400 italic text-xs">No phone</span>
                                    @endif
                                </td>

                                <!-- Address -->
                                <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                    {{ $supplier->address ?: '-' }}
                                </td>

                                <!-- Status Badge -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($supplier->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-300">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a 
                                        href="{{ route('inventory.suppliers.show', $supplier) }}" 
                                        class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 transition"
                                        title="View Details"
                                    >
                                        View
                                    </a>

                                    @if(Auth::user()->isAdmin())
                                        <a 
                                            href="{{ route('inventory.suppliers.edit', $supplier) }}" 
                                            class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-md text-[#0B1849] bg-blue-50 hover:bg-blue-100 border border-blue-200 transition"
                                        >
                                            Edit
                                        </a>

                                        <form 
                                            method="POST" 
                                            action="{{ route('inventory.suppliers.toggle-status', $supplier) }}" 
                                            class="inline-block"
                                            onsubmit="return confirm('Change status for {{ $supplier->name }}?')"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            <button 
                                                type="submit" 
                                                class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-md {{ $supplier->is_active ? 'text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200' }} transition cursor-pointer"
                                            >
                                                {{ $supplier->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <x-empty-state 
                                        title="No suppliers found." 
                                        description="Register your parts, oil, and component vendors to begin purchasing."
                                    >
                                        @if(Auth::user()->isAdmin())
                                            <x-slot:action>
                                                <a 
                                                    href="{{ route('inventory.suppliers.create') }}" 
                                                    class="inline-flex items-center justify-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-sm rounded-lg shadow-xs transition duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B1849]"
                                                >
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                    Add Supplier
                                                </a>
                                            </x-slot:action>
                                        @endif
                                    </x-empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($suppliers->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                    {{ $suppliers->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
