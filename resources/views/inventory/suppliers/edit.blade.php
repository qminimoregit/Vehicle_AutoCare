@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('inventory.suppliers.index') }}" class="text-xs font-semibold text-[#0B1849] hover:underline inline-flex items-center mb-2">
                &larr; Back to Suppliers
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Edit Supplier</h1>
            <p class="mt-1 text-sm text-gray-500">Update supplier contact and registration information for <span class="font-semibold text-gray-800">{{ $supplier->name }}</span>.</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 sm:p-8 shadow-xs">
            <form method="POST" action="{{ route('inventory.suppliers.update', $supplier) }}" class="space-y-6" novalidate>
                @csrf
                @method('PUT')

                <!-- Supplier Name -->
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Supplier Name <span class="text-rose-500">*</span>
                    </label>
                    <div class="mt-1.5">
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            required 
                            value="{{ old('name', $supplier->name) }}" 
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] @error('name') border-rose-400 bg-rose-50/30 @enderror"
                        />
                    </div>
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Registration Number & Mobile -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="registration_number" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Registration Number <span class="text-gray-400 font-normal normal-case">(Optional)</span>
                        </label>
                        <div class="mt-1.5">
                            <input 
                                type="text" 
                                name="registration_number" 
                                id="registration_number" 
                                value="{{ old('registration_number', $supplier->registration_number) }}" 
                                class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] @error('registration_number') border-rose-400 bg-rose-50/30 @enderror"
                            />
                        </div>
                        @error('registration_number')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mobile" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Mobile Number <span class="text-gray-400 font-normal normal-case">(Optional)</span>
                        </label>
                        <div class="mt-1.5">
                            <input 
                                type="text" 
                                name="mobile" 
                                id="mobile" 
                                value="{{ old('mobile', $supplier->mobile) }}" 
                                class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] @error('mobile') border-rose-400 bg-rose-50/30 @enderror"
                            />
                        </div>
                        @error('mobile')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Office Phone & Fax -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="office_phone" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Office Phone <span class="text-gray-400 font-normal normal-case">(Optional)</span>
                        </label>
                        <div class="mt-1.5">
                            <input 
                                type="text" 
                                name="office_phone" 
                                id="office_phone" 
                                value="{{ old('office_phone', $supplier->office_phone) }}" 
                                class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] @error('office_phone') border-rose-400 bg-rose-50/30 @enderror"
                            />
                        </div>
                        @error('office_phone')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="fax" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Fax Number <span class="text-gray-400 font-normal normal-case">(Optional)</span>
                        </label>
                        <div class="mt-1.5">
                            <input 
                                type="text" 
                                name="fax" 
                                id="fax" 
                                value="{{ old('fax', $supplier->fax) }}" 
                                class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] @error('fax') border-rose-400 bg-rose-50/30 @enderror"
                            />
                        </div>
                        @error('fax')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Address <span class="text-gray-400 font-normal normal-case">(Optional)</span>
                    </label>
                    <div class="mt-1.5">
                        <textarea 
                            name="address" 
                            id="address" 
                            rows="2" 
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] @error('address') border-rose-400 bg-rose-50/30 @enderror"
                        >{{ old('address', $supplier->address) }}</textarea>
                    </div>
                    @error('address')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="is_active" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Status <span class="text-rose-500">*</span>
                    </label>
                    <div class="mt-1.5">
                        <select 
                            name="is_active" 
                            id="is_active" 
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                        >
                            <option value="1" {{ old('is_active', $supplier->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active', $supplier->is_active ? '1' : '0') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    @error('is_active')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons: [Cancel] [Save Supplier] -->
                <div class="pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a 
                        href="{{ route('inventory.suppliers.index') }}" 
                        class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition shadow-2xs text-center"
                    >
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-xs transition cursor-pointer"
                    >
                        Save Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
