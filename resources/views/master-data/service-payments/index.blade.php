@extends('layouts.app')

@section('title', 'Manage Service Payment')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <x-page-header 
            title="Manage Service Payment" 
            description="Manage service prices and internal costs for different vehicle types."
        />

        <!-- Search Bar -->
        <x-search-bar 
            :action="route('master-data.service-payments.index')" 
            :search="$search" 
            placeholder="Search Service..."
            :resetUrl="route('master-data.service-payments.index')"
        />

        <!-- Service List Table -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Service Name
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Configured Vehicle Types
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($services as $service)
                            <tr class="hover:bg-gray-50/70 transition">
                                <!-- Service Name -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <span class="font-mono text-xs font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded">
                                            {{ $service->code }}
                                        </span>
                                        <span class="text-sm font-bold text-gray-900">
                                            {{ $service->name }}
                                        </span>
                                    </div>
                                    @if($service->description)
                                        <div class="text-xs text-gray-500 mt-1 line-clamp-1">
                                            {{ $service->description }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Configured Vehicle Types -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $configured = $service->configured_count ?? 0;
                                        $total = $totalVehicleTypes;
                                    @endphp
                                    @if($configured > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-emerald-500"></span>
                                            {{ $configured }} / {{ $total }} Types Configured
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-gray-400"></span>
                                            0 / {{ $total }} Configured
                                        </span>
                                    @endif
                                </td>

                                <!-- Action Button -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a 
                                        href="{{ route('master-data.service-payments.edit', $service) }}" 
                                        class="inline-flex items-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-semibold rounded-lg shadow-xs transition duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B1849]"
                                    >
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Set Prices
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <x-empty-state 
                                        title="No services found." 
                                        description="No active services available to manage pricing."
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($services->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                    {{ $services->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
