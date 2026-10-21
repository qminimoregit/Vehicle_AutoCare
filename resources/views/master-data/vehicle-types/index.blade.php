@extends('layouts.app')

@section('title', 'Vehicle Types')

@section('content')
    <div>
        <!-- Page Header -->
        <x-page-header 
            title="Vehicle Types" 
            description="Manage vehicle types and images."
        >
            <x-slot:actions>
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                        Total: {{ $totalVehicleTypes }} {{ Str::plural('type', $totalVehicleTypes) }}
                    </span>
                    @if(Auth::user()->isAdmin())
                        <a 
                            href="{{ route('master-data.vehicle-types.create') }}" 
                            class="inline-flex items-center justify-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-sm rounded-lg shadow-xs transition duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B1849]"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Vehicle Type
                        </a>
                    @endif
                </div>
            </x-slot:actions>
        </x-page-header>

        <!-- Search Bar -->
        <x-search-bar 
            :action="route('master-data.vehicle-types.index')" 
            :search="$search" 
            placeholder="Search Vehicle Types..."
            :resetUrl="route('master-data.vehicle-types.index')"
        />

        <!-- Vehicle Types Table -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Vehicle Type
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-center text-xs font-bold text-gray-700 uppercase tracking-wider w-32">
                                Vehicle Image
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-700 uppercase tracking-wider w-32">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($vehicleTypes as $type)
                            <tr class="hover:bg-gray-50/70 transition">
                                <!-- 1. Vehicle Type -->
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $type->name }}</div>
                                    <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                        @if($type->enable_carpets)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                Carpets: Enabled ({{ $type->carpetCategories->count() }})
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                                                Carpets: Disabled
                                            </span>
                                        @endif
                                    </div>
                                    @if($type->description)
                                        <div class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $type->description }}</div>
                                    @endif
                                </td>

                                <!-- 2. Vehicle Image -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center justify-center">
                                        @if($type->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($type->image))
                                            <div class="w-14 h-14 rounded-lg bg-gray-50 border border-gray-200 p-1 flex items-center justify-center overflow-hidden shadow-2xs">
                                                <img 
                                                    src="{{ asset('storage/' . $type->image) }}" 
                                                    alt="{{ $type->name }}" 
                                                    class="w-full h-full object-contain"
                                                    loading="lazy"
                                                />
                                            </div>
                                        @else
                                            <div class="w-14 h-14 rounded-lg bg-gray-100 border border-dashed border-gray-300 flex flex-col items-center justify-center text-gray-400 group">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 17a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4zM4 11h16M4 11l2-5h12l2 5M4 11v6h2m12 0h2v-6" />
                                                </svg>
                                                <span class="text-[9px] uppercase tracking-wider font-semibold text-gray-400 mt-0.5">No img</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- 3. Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if(Auth::user()->isAdmin())
                                        <a 
                                            href="{{ route('master-data.vehicle-types.edit', $type) }}" 
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 transition cursor-pointer"
                                        >
                                            <svg class="w-3.5 h-3.5 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400 italic">View Only</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <x-empty-state 
                                        title="No vehicle types found." 
                                        description="Start by registering the first category of vehicles supported by the center."
                                    >
                                        @if(Auth::user()->isAdmin())
                                            <x-slot:action>
                                                <a 
                                                    href="{{ route('master-data.vehicle-types.create') }}" 
                                                    class="inline-flex items-center justify-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-sm rounded-lg shadow-xs transition duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B1849]"
                                                >
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                    Add Vehicle Type
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
            @if($vehicleTypes->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                    {{ $vehicleTypes->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
