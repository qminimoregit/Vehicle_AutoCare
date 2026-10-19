@extends('layouts.app')

@section('title', 'Edit Vehicle Type - ' . $vehicleType->name)

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('master-data.vehicle-types.index') }}" class="text-xs font-semibold text-[#0B1849] hover:underline inline-flex items-center mb-2">
                &larr; Back to Vehicle Types
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Edit Vehicle Type</h1>
            <p class="mt-1 text-sm text-gray-500">Update vehicle classification details for: <span class="font-semibold text-gray-800">{{ $vehicleType->name }}</span>.</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 sm:p-8 shadow-xs">
            <form method="POST" action="{{ route('master-data.vehicle-types.update', $vehicleType) }}" class="space-y-6" novalidate>
                @csrf
                @method('PUT')

                <!-- Vehicle Type Name -->
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Vehicle Type Name <span class="text-rose-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            required 
                            value="{{ old('name', $vehicleType->name) }}" 
                            class="block w-full px-3 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] @error('name') border-rose-400 bg-rose-50/30 @enderror"
                        />
                    </div>
                    @error('name')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description (Optional) -->
                <div>
                    <label for="description" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Description <span class="text-gray-400 font-normal">(Optional)</span>
                    </label>
                    <div class="mt-1">
                        <textarea 
                            name="description" 
                            id="description" 
                            rows="3" 
                            class="block w-full px-3 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] @error('description') border-rose-400 bg-rose-50/30 @enderror"
                        >{{ old('description', $vehicleType->description) }}</textarea>
                    </div>
                    @error('description')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Status <span class="text-rose-500">*</span>
                    </label>
                    <div class="mt-1">
                        <select 
                            name="status" 
                            id="status" 
                            required
                            class="block w-full px-3 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                        >
                            <option value="active" {{ old('status', $vehicleType->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $vehicleType->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    @error('status')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons: [Cancel] [Save Vehicle Type] -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <x-secondary-button :href="route('master-data.vehicle-types.index')">
                        Cancel
                    </x-secondary-button>
                    <x-primary-button>
                        Save Vehicle Type
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
@endsection
