@extends('layouts.app')

@section('title', 'Search Client SMS')

@section('content')
    <div x-data="{
        selectedCustomers: [],
        selectAll: false,
        toggleAll(customerIds) {
            if (this.selectAll) {
                this.selectedCustomers = [...customerIds];
            } else {
                this.selectedCustomers = [];
            }
        },
        updateSelectAll(total) {
            this.selectAll = total > 0 && this.selectedCustomers.length === total;
        }
    }">
        <!-- Page Header -->
        <x-page-header 
            title="Search Client SMS" 
            description="Find customers for SMS communication."
        />

        <!-- Simple Search Form -->
        <div class="mb-6 bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <form method="GET" action="{{ route('master-data.client-sms.index') }}" class="flex flex-col sm:flex-row gap-3">
                <!-- Customer Name / Phone Number Input -->
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
                        placeholder="Customer Name / Phone Number" 
                        class="block w-full pl-10 pr-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] placeholder:text-gray-400"
                    />
                </div>

                <!-- Search Button -->
                <div class="flex items-center space-x-2">
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg transition shadow-xs cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B1849]"
                    >
                        Search
                    </button>
                    @if($search)
                        <a 
                            href="{{ route('master-data.client-sms.index') }}" 
                            class="px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Records Table -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
            <div class="responsive-table-wrapper">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Customer Name
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Mobile Number
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-700 uppercase tracking-wider w-24">
                                Select
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="px-6 py-4 text-sm font-bold text-gray-900">
                                    {{ $customer->name }}
                                </td>
                                <td class="px-6 py-4 text-sm font-mono text-gray-600">
                                    {{ $customer->phone }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <input 
                                        type="checkbox" 
                                        value="{{ $customer->id }}" 
                                        x-model="selectedCustomers"
                                        @change="updateSelectAll({{ $customers->count() }})"
                                        class="h-4 w-4 rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849]"
                                    />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <x-empty-state 
                                        title="No customer records found."
                                        description="Customer management is not developed yet. When customer profiles are registered in Phase 2, they will appear here for SMS communication."
                                    >
                                        <x-slot:icon>
                                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </x-slot:icon>
                                    </x-empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Selected Customers Summary Bar -->
            <div class="px-6 py-4 bg-gray-50/70 border-t border-gray-200 flex items-center justify-between text-sm">
                <div class="flex items-center space-x-2 text-gray-700 font-medium">
                    <span>Selected Customers:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#0B1849] text-white" x-text="selectedCustomers.length">
                        0
                    </span>
                </div>
                <div class="text-xs text-gray-400">
                    Live SMS broadcasting will be connected in an upcoming phase.
                </div>
            </div>
        </div>

        <!-- Privacy & Consent Policy Box -->
        <div class="mt-6 rounded-lg bg-gray-50 border border-gray-200 p-4 text-xs text-gray-600 leading-relaxed flex items-start space-x-3">
            <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <div>
                <span class="font-bold text-gray-700">SMS Consent & Privacy Protection:</span>
                In compliance with customer communication regulations, promotional and transactional SMS will only be sent to customers who have granted explicit consent and have not opted out.
            </div>
        </div>
    </div>
@endsection
