@extends('layouts.app')

@section('title', 'Service Lists')

@section('content')
    <div x-data="{ 
        showAddModal: {{ $errors->any() && old('_form_type') === 'create' ? 'true' : 'false' }},
        suggestedCode: 'SRV-{{ str_pad((string)($totalServices + 1), 3, '0', STR_PAD_LEFT) }}'
    }">
        <!-- Page Header -->
        <x-page-header 
            title="Service Lists" 
            description="Manage auto care services."
        >
            <x-slot:actions>
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                        Total: {{ $totalServices }} {{ Str::plural('service', $totalServices) }}
                    </span>
                    @if(Auth::user()->isAdmin())
                        <button 
                            type="button" 
                            @click="showAddModal = true" 
                            class="inline-flex items-center justify-center px-5 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-sm rounded-lg shadow-xs transition duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B1849]"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            + Add Service
                        </button>
                    @endif
                </div>
            </x-slot:actions>
        </x-page-header>

        <!-- Search Bar -->
        <x-search-bar 
            :action="route('master-data.services.index')" 
            :search="$search" 
            placeholder="Search Services..."
            :resetUrl="route('master-data.services.index')"
        >
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
        </x-search-bar>

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
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 transition cursor-pointer"
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
                                                        class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-md text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition cursor-pointer"
                                                    >
                                                        Deactivate
                                                    </button>
                                                @else
                                                    <button 
                                                        type="submit" 
                                                        class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-md text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition cursor-pointer"
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
                                        title="No services found." 
                                        description="Get started by creating your first workshop service."
                                    >
                                        @if(Auth::user()->isAdmin())
                                            <x-slot:action>
                                                <button 
                                                    type="button" 
                                                    @click="showAddModal = true" 
                                                    class="inline-flex items-center px-5 py-2.5 text-xs font-semibold rounded-lg text-white bg-[#0B1849] hover:bg-[#15286e] shadow-xs transition"
                                                >
                                                    + Add Service
                                                </button>
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
            @if($services->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                    {{ $services->links() }}
                </div>
            @endif
        </div>

        @if(Auth::user()->isAdmin())
            <!-- Add Service Modal Dialog -->
            <div 
                x-show="showAddModal" 
                class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
                style="display: none;"
                x-on:keydown.escape.window="showAddModal = false"
            >
                <!-- Backdrop -->
                <div 
                    x-show="showAddModal" 
                    @click="showAddModal = false"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs"
                ></div>

                <!-- Modal Window -->
                <div 
                    x-show="showAddModal" 
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="relative bg-white rounded-xl shadow-xl max-w-lg w-full border border-gray-200 z-10 overflow-hidden"
                >
                    <div class="px-6 py-4 bg-gray-50/80 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Add Service</h3>
                        <button 
                            type="button" 
                            @click="showAddModal = false" 
                            class="text-gray-400 hover:text-gray-600 transition p-1 rounded-lg"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('master-data.services.store') }}" class="p-6 space-y-4" novalidate>
                        @csrf
                        <input type="hidden" name="_form_type" value="create">

                        <!-- 1. Service Code -->
                        <div>
                            <label for="modal_code" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                Service Code <span class="text-rose-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input 
                                    type="text" 
                                    name="code" 
                                    id="modal_code" 
                                    required 
                                    value="{{ old('code') }}" 
                                    placeholder="e.g. SRV001"
                                    class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] font-mono @error('code') border-rose-400 bg-rose-50/30 @enderror"
                                />
                            </div>
                            <x-input-error for="code" />
                        </div>

                        <!-- 2. Service Name -->
                        <div>
                            <label for="modal_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                Service Name <span class="text-rose-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input 
                                    type="text" 
                                    name="name" 
                                    id="modal_name" 
                                    required 
                                    value="{{ old('name') }}" 
                                    placeholder="e.g. Full Service, Oil Change, Brake Repair"
                                    class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] @error('name') border-rose-400 bg-rose-50/30 @enderror"
                                />
                            </div>
                            <x-input-error for="name" />
                        </div>

                        <!-- 3. Description (Optional) -->
                        <div>
                            <label for="modal_description" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                Description <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <div class="mt-1">
                                <textarea 
                                    name="description" 
                                    id="modal_description" 
                                    rows="3" 
                                    placeholder="Brief description of the service..."
                                    class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] @error('description') border-rose-400 bg-rose-50/30 @enderror"
                                >{{ old('description') }}</textarea>
                            </div>
                            <x-input-error for="description" />
                        </div>

                        <!-- 4. Status -->
                        <div>
                            <label for="modal_status" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                Status <span class="text-rose-500">*</span>
                            </label>
                            <div class="mt-1">
                                <select 
                                    name="status" 
                                    id="modal_status" 
                                    required
                                    class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                                >
                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <x-input-error for="status" />
                        </div>

                        <!-- Buttons: [Cancel] [Save Service] -->
                        <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                            <button 
                                type="button" 
                                @click="showAddModal = false" 
                                class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit" 
                                class="px-5 py-2 text-sm font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-xs transition"
                            >
                                Save Service
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection
