@extends('layouts.app')

@section('title', 'Service Lists')

@section('content')
    <!-- Page Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Service Lists</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                    Total: {{ $totalServices }} {{ Str::plural('service', $totalServices) }}
                </span>
            </div>
            <p class="mt-1 text-sm text-gray-500">Manage auto care services.</p>
        </div>
        @if(Auth::user()->isAdmin())
            <div>
                <a 
                    href="{{ route('master-data.services.create') }}" 
                    class="inline-flex items-center justify-center px-5 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-sm rounded-lg shadow-xs transition duration-150"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    + Add Service
                </a>
            </div>
        @endif
    </div>

    <!-- Simple Search Bar -->
    <div class="mb-6 bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
        <form method="GET" action="{{ route('master-data.services.index') }}" class="flex flex-col sm:flex-row gap-3">
            <!-- Search Input -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search Services..." 
                    class="block w-full pl-9 pr-3 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                />
            </div>

            <!-- Status Filter -->
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

            <!-- Actions -->
            <div class="flex items-center space-x-2">
                <button 
                    type="submit" 
                    class="px-5 py-2.5 text-sm font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg transition shadow-xs"
                >
                    Search
                </button>
                @if(request('search') || request('status'))
                    <a 
                        href="{{ route('master-data.services.index') }}" 
                        class="px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Service Lists Data Table -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
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
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Actions
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

                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-status-badge :status="$service->status" />
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if(Auth::user()->isAdmin())
                                    <div class="inline-flex items-center space-x-2">
                                        <!-- Edit Action -->
                                        <a 
                                            href="{{ route('master-data.services.edit', $service) }}" 
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 transition"
                                        >
                                            <svg class="w-3.5 h-3.5 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </a>

                                        <!-- Activate / Deactivate Toggle -->
                                        <form 
                                            method="POST" 
                                            action="{{ route('master-data.services.toggle-status', $service) }}" 
                                            onsubmit="return confirm('Are you sure you want to {{ $service->isActive() ? 'deactivate' : 'activate' }} {{ $service->name }}?')"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            @if($service->isActive())
                                                <button 
                                                    type="submit" 
                                                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-md text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition"
                                                >
                                                    Deactivate
                                                </button>
                                            @else
                                                <button 
                                                    type="submit" 
                                                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-md text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition"
                                                >
                                                    Activate
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">View Only</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <x-empty-state 
                                    title="No services added yet."
                                    description="Get started by creating your first workshop service."
                                >
                                    @if(Auth::user()->isAdmin())
                                        <x-slot:action>
                                            <a 
                                                href="{{ route('master-data.services.create') }}" 
                                                class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg text-white bg-[#0B1849] hover:bg-[#15286e] shadow-xs transition"
                                            >
                                                + Add Service
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

        @if($services->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                {{ $services->links() }}
            </div>
        @endif
    </div>
@endsection
