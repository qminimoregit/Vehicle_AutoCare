@extends('layouts.app')

@section('title', 'Manage Service Payment')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <x-page-header 
            title="Manage Service Payment" 
            description="Manage auto care service rates, payment parameters, and billing configuration."
        >
            <x-slot:actions>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                    <span class="w-2 h-2 bg-amber-500 rounded-full mr-2"></span>
                    Calculation Rules Pending Confirmation
                </span>
            </x-slot:actions>
        </x-page-header>

        <!-- Information / Confirmation Alert Banner -->
        <div class="bg-amber-50/80 border border-amber-200 rounded-xl p-4 text-amber-900 text-xs leading-relaxed flex items-start space-x-3">
            <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="font-bold text-amber-950">Notice on Calculation Rules:</p>
                <p class="mt-0.5">
                    As specified, financial calculations (such as fixed rate vs hourly labor, commission distributions, and tax inclusion rules) require your explicit business confirmation before being calculated automatically. This page shell is configured with the standard structure and controls ready for activation once your formulas are confirmed.
                </p>
            </div>
        </div>

        <!-- Search & Filter Controls -->
        <x-search-bar 
            :action="route('master-data.service-payments.index')" 
            :search="$search" 
            placeholder="Search Services by code or name..."
            :resetUrl="route('master-data.service-payments.index')"
        >
            <!-- Vehicle Type Filter -->
            <div class="w-full sm:w-56">
                <select 
                    name="vehicle_type_id" 
                    onchange="this.form.submit()"
                    class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                >
                    <option value="">All Vehicle Categories</option>
                    @foreach($vehicleTypes as $type)
                        <option value="{{ $type->id }}" {{ (string)$vehicleTypeId === (string)$type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </x-search-bar>

        <!-- Form Layout with Easy-to-Read Records Table -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
            <form method="POST" action="#" onsubmit="event.preventDefault(); alert('Calculation rules are pending user confirmation before financial parameters can be saved.');">
                @csrf
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Service Code
                                </th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Service Name
                                </th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Standard Rate / Fee (LKR)
                                </th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Calculation Mode
                                </th>
                                <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($services as $service)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <!-- Service Code -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-mono text-xs font-bold text-gray-900 bg-gray-100 px-2 py-1 rounded">
                                            {{ $service->code }}
                                        </span>
                                    </td>

                                    <!-- Service Name -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $service->name }}</div>
                                        @if($service->description)
                                            <div class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $service->description }}</div>
                                        @endif
                                    </td>

                                    <!-- Rate Field Shell -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-36">
                                            <div class="relative rounded-md shadow-xs">
                                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-xs text-gray-400 font-medium">
                                                    Rs.
                                                </div>
                                                <input 
                                                    type="text" 
                                                    placeholder="0.00" 
                                                    readonly
                                                    class="block w-full pl-8 pr-2 py-1.5 text-xs text-gray-800 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed font-mono"
                                                    title="Pending calculation rule confirmation"
                                                />
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Calculation Mode -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center text-xs text-gray-500 italic">
                                            Awaiting Rules
                                        </span>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <x-status-badge :status="$service->status" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <x-empty-state 
                                            title="No services found." 
                                            description="No matching active services available for rate management."
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

                <!-- Save and Cancel Buttons -->
                <div class="px-6 py-4 bg-gray-50/80 border-t border-gray-200 flex items-center justify-end space-x-3">
                    <a 
                        href="{{ route('master-data.services.index') }}" 
                        class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition cursor-pointer"
                    >
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-xs transition duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B1849]"
                    >
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
