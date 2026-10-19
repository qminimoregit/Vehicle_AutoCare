@extends('layouts.app')

@section('title', 'Service Lists')

@section('content')
    <!-- Page Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Service Lists</h1>
            <p class="mt-1 text-sm text-gray-500">View and manage workshop services offered to customers.</p>
        </div>
        <div>
            <a 
                href="{{ route('master-data.services.create') }}" 
                class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-lg shadow-xs text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
            >
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New Service
            </a>
        </div>
    </div>

    <!-- Search & Filter Card -->
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
                    placeholder="Search by service name, code, or description..." 
                    class="block w-full pl-9 pr-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600"
                />
            </div>

            <!-- Status Filter -->
            <div class="w-full sm:w-44">
                <select 
                    name="status" 
                    onchange="this.form.submit()"
                    class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 bg-white"
                >
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                </select>
            </div>

            <!-- Search Action Buttons -->
            <div class="flex items-center space-x-2">
                <button 
                    type="submit" 
                    class="px-4 py-2 text-sm font-medium text-white bg-gray-800 hover:bg-gray-900 rounded-lg transition"
                >
                    Search
                </button>
                @if(request('search') || request('status'))
                    <a 
                        href="{{ route('master-data.services.index') }}" 
                        class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Simple Data Table -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Details
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($services as $service)
                        <tr class="hover:bg-gray-50/60 transition">
                            <!-- Column 1: Name -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $service->name }}</div>
                                <div class="text-xs font-mono text-gray-400 mt-0.5">{{ $service->code }}</div>
                            </td>

                            <!-- Column 2: Details -->
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600 max-w-md line-clamp-2">
                                    {{ $service->description ?: 'No description provided.' }}
                                </div>
                            </td>

                            <!-- Column 3: Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-status-badge :status="$service->status" />
                            </td>

                            <!-- Column 4: Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
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

                                    <!-- Activate / Deactivate Action -->
                                    <form 
                                        method="POST" 
                                        action="{{ route('master-data.services.toggle-status', $service) }}" 
                                        onsubmit="return confirm('Are you sure you want to {{ $service->isActive() ? 'deactivate' : 'activate' }} this service?')"
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
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="max-w-sm mx-auto text-center">
                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 text-gray-400 mb-3">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-900">No services added yet.</h3>
                                    <p class="mt-1 text-xs text-gray-500">Get started by creating your first workshop service offering.</p>
                                    <div class="mt-4">
                                        <a 
                                            href="{{ route('master-data.services.create') }}" 
                                            class="inline-flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg text-white bg-blue-600 hover:bg-blue-700 shadow-xs transition"
                                        >
                                            + Add Service
                                        </a>
                                    </div>
                                </div>
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
