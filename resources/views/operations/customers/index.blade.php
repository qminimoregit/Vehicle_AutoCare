@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Customer Management</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Manage registered customers, view linked vehicles and job history.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('operations.customers.create') }}" class="inline-flex items-center px-4 py-2.5 bg-[#0B1849] hover:bg-[#112366] text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Customer
            </a>
        </div>
    </div>

    <!-- Search Card -->
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs">
        <form action="{{ route('operations.customers.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Search by name, phone, email, or company..."
                    class="block w-full pl-10 pr-4 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[40px]"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            <button type="submit" class="px-5 py-2 bg-gray-900 hover:bg-black text-white text-xs font-bold uppercase tracking-wider rounded-lg transition min-h-[40px]">
                Search
            </button>
            @if($search)
                <a href="{{ route('operations.customers.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold uppercase tracking-wider rounded-lg transition min-h-[40px] inline-flex items-center">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Customer Table -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
        <div class="responsive-table-wrapper">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                    <tr>
                        <th class="py-3 px-4">Customer Name</th>
                        <th class="py-3 px-4">Phone / Contact</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4 text-center">Vehicles</th>
                        <th class="py-3 px-4 text-center">Job Cards</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50/75 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-gray-900">{{ $customer->customer_name }}</div>
                                @if($customer->company_name)
                                    <div class="text-xs text-gray-500">{{ $customer->company_name }}</div>
                                @endif
                                @if($customer->address)
                                    <div class="text-xs text-gray-400 truncate max-w-xs">{{ $customer->address }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs text-gray-900">
                                <div>{{ $customer->phone }}</div>
                                @if($customer->email)
                                    <div class="text-[11px] text-gray-500">{{ $customer->email }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $customer->customer_type === 'company' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($customer->customer_type ?? 'individual') }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center justify-center px-2 py-1 bg-gray-100 text-gray-800 font-bold text-xs rounded-full min-w-[28px]">
                                    {{ $customer->vehicles_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center justify-center px-2 py-1 bg-gray-100 text-gray-800 font-bold text-xs rounded-full min-w-[28px]">
                                    {{ $customer->job_cards_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-2">
                                <a href="{{ route('operations.vehicles.index', ['customer_id' => $customer->id]) }}" class="inline-flex items-center text-xs font-semibold text-[#0B1849] hover:underline">
                                    + Add Vehicle / Job
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-sm text-gray-500">
                                No customers found. Click <strong>New Customer</strong> to add one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
