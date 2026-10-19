@extends('layouts.app')

@section('title', 'Add New Vehicle Type')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Page Title & Description -->
        <div class="mb-6">
            <a href="{{ route('master-data.vehicle-types.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 inline-flex items-center mb-2">
                &larr; Back to Vehicle Types
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Add New Vehicle Type</h1>
            <p class="mt-1 text-sm text-gray-500">Register a new vehicle category supported by the care center.</p>
        </div>

        <!-- Short Form Card -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 sm:p-8 shadow-xs">
            <form method="POST" action="{{ route('master-data.vehicle-types.store') }}" class="space-y-6" novalidate>
                @csrf

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
                            value="{{ old('name') }}" 
                            placeholder="e.g. Car, Van, SUV, Motorbike, Lorry"
                            class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 @error('name') border-rose-400 bg-rose-50/30 @enderror"
                        />
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Common examples: Car, Sedan, SUV, Van, Commercial Lorry, Motorcycle.</p>
                    @error('name')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
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
                            placeholder="Optional notes regarding vehicle size, bay requirements, or classification..."
                            class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 @error('description') border-rose-400 bg-rose-50/30 @enderror"
                        >{{ old('description') }}</textarea>
                    </div>
                    @error('description')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
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
                            class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 bg-white"
                        >
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active (Accepted for service)</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive (Temporarily suspended)</option>
                        </select>
                    </div>
                    @error('status')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons: Save and Cancel -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a 
                        href="{{ route('master-data.vehicle-types.index') }}" 
                        class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                    >
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-xs focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
                    >
                        Save Vehicle Type
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
