@extends('layouts.app')

@section('title', $service->name . ' - Vehicle Prices')

@section('content')
    <div class="space-y-6">
        <!-- Header & Breadcrumbs -->
        <div>
            <a 
                href="{{ route('master-data.service-payments.index') }}" 
                class="text-xs font-semibold text-[#0B1849] hover:underline inline-flex items-center mb-2"
            >
                &larr; Back to Service Payment List
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center space-x-3">
                        <span class="font-mono text-xs font-bold text-gray-900 bg-gray-200 px-2.5 py-1 rounded">
                            {{ $service->code }}
                        </span>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                            {{ $service->name }} - Vehicle Prices
                        </h1>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        Configure customer service prices and internal workshop costs by vehicle category.
                    </p>
                </div>

                @if(!$canEdit)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                        View Only Mode (Admin access required to edit)
                    </span>
                @endif
            </div>
        </div>

        <!-- Search Bar for Vehicle Types -->
        <x-search-bar 
            :action="route('master-data.service-payments.edit', $service)" 
            :search="$search" 
            placeholder="Search Vehicle Type..."
            :resetUrl="route('master-data.service-payments.edit', $service)"
        />

        <!-- Form & Table Container -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
            <form method="POST" action="{{ route('master-data.service-payments.update', $service) }}" novalidate>
                @csrf
                @method('PUT')

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-20">
                                    Icon
                                </th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Vehicle Type
                                </th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Service Price (LKR)
                                    <span class="text-[11px] font-normal text-gray-400 block normal-case">Customer Charge</span>
                                </th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Service Cost (LKR)
                                    <span class="text-[11px] font-normal text-gray-400 block normal-case">Internal Cost</span>
                                </th>
                                <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($vehicleTypes as $type)
                                @php
                                    $priceRecord = $existingPrices->get($type->id);
                                    $currentPrice = old("prices.{$type->id}.price", $priceRecord?->service_price !== null ? number_format($priceRecord->service_price, 2, '.', '') : '');
                                    $currentCost = old("prices.{$type->id}.cost", $priceRecord?->service_cost !== null ? number_format($priceRecord->service_cost, 2, '.', '') : '');
                                    $typeName = strtolower($type->name);
                                @endphp
                                <tr class="hover:bg-gray-50/70 transition">
                                    <!-- Vehicle Icon -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600">
                                            @if(str_contains($typeName, 'bike') || str_contains($typeName, 'motor'))
                                                <!-- Motorcycle Icon -->
                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <circle cx="6" cy="17" r="3" stroke-width="2"/>
                                                    <circle cx="18" cy="17" r="3" stroke-width="2"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 17h4l3-6h5l2 3M13 11l-3-6H7M15 5h3"/>
                                                </svg>
                                            @elseif(str_contains($typeName, 'van') || str_contains($typeName, 'bus'))
                                                <!-- Van / Bus Icon -->
                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <rect x="3" y="6" width="18" height="12" rx="2" stroke-width="2"/>
                                                    <circle cx="7" cy="18" r="2" stroke-width="2"/>
                                                    <circle cx="17" cy="18" r="2" stroke-width="2"/>
                                                    <path stroke-linecap="round" stroke-width="2" d="M3 11h18M9 6v5M15 6v5"/>
                                                </svg>
                                            @elseif(str_contains($typeName, 'suv') || str_contains($typeName, 'jeep'))
                                                <!-- SUV / Jeep Icon -->
                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 14l2-5h12l3 5H3z"/>
                                                    <rect x="2" y="13" width="20" height="5" rx="1" stroke-width="2"/>
                                                    <circle cx="6" cy="18" r="2" stroke-width="2"/>
                                                    <circle cx="18" cy="18" r="2" stroke-width="2"/>
                                                </svg>
                                            @elseif(str_contains($typeName, 'truck') || str_contains($typeName, 'lorry'))
                                                <!-- Truck / Lorry Icon -->
                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <rect x="2" y="7" width="12" height="10" stroke-width="2"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 11h5l3 3v3h-8v-6z"/>
                                                    <circle cx="6" cy="17" r="2" stroke-width="2"/>
                                                    <circle cx="18" cy="17" r="2" stroke-width="2"/>
                                                </svg>
                                            @else
                                                <!-- Standard Car Icon -->
                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l2-4h10l2 4M3 13h18v4H3v-4z"/>
                                                    <circle cx="7" cy="17" r="2" stroke-width="2"/>
                                                    <circle cx="17" cy="17" r="2" stroke-width="2"/>
                                                </svg>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Vehicle Type Name -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">
                                            {{ $type->name }}
                                        </div>
                                        @if($type->description)
                                            <div class="text-xs text-gray-500 mt-0.5 line-clamp-1">
                                                {{ $type->description }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Service Price (Customer Charge) -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-44">
                                            <div class="relative rounded-lg shadow-2xs">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-xs text-gray-500 font-semibold">
                                                    Rs.
                                                </div>
                                                <input 
                                                    type="number" 
                                                    step="0.01" 
                                                    min="0"
                                                    name="prices[{{ $type->id }}][price]" 
                                                    value="{{ $currentPrice }}" 
                                                    placeholder="0.00"
                                                    @disabled(!$canEdit)
                                                    class="block w-full pl-9 pr-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] font-mono disabled:bg-gray-100 disabled:text-gray-500"
                                                />
                                            </div>
                                            <x-input-error for="prices.{{ $type->id }}.price" />
                                        </div>
                                    </td>

                                    <!-- Service Cost (Internal Cost) -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($canEdit)
                                            <div class="w-44">
                                                <div class="relative rounded-lg shadow-2xs">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-xs text-gray-500 font-semibold">
                                                        Rs.
                                                    </div>
                                                    <input 
                                                        type="number" 
                                                        step="0.01" 
                                                        min="0"
                                                        name="prices[{{ $type->id }}][cost]" 
                                                        value="{{ $currentCost }}" 
                                                        placeholder="0.00"
                                                        class="block w-full pl-9 pr-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] font-mono"
                                                    />
                                                </div>
                                                <x-input-error for="prices.{{ $type->id }}.cost" />
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic font-mono">
                                                Protected
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Status / Indicator -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs">
                                        @if($priceRecord && $priceRecord->service_price !== null)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Configured
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                Not Configured
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <x-empty-state 
                                            title="No vehicle types found." 
                                            description="Register vehicle types under Vehicle Types master data first."
                                        />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($vehicleTypes->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                        {{ $vehicleTypes->links() }}
                    </div>
                @endif

                <!-- Buttons: [Cancel] [Save Changes] -->
                <div class="px-6 py-4 bg-gray-50/80 border-t border-gray-200 flex items-center justify-end space-x-3">
                    <a 
                        href="{{ route('master-data.service-payments.index') }}" 
                        class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition cursor-pointer"
                    >
                        Cancel
                    </a>
                    @if($canEdit)
                        <button 
                            type="submit" 
                            class="px-6 py-2.5 text-sm font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-xs transition duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B1849]"
                        >
                            Save Changes
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
