@extends('layouts.app')

@section('title', 'Company Details')

@section('content')
    <div class="max-w-3xl mx-auto">
        <!-- Page Header -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Company Details</h1>
                <p class="mt-1 text-sm text-gray-500">Auto care center business information, official registration, and invoice branding.</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                    Workshop Master Profile
                </span>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 sm:p-8 shadow-xs">
            <form method="POST" action="{{ route('master-data.company-details.update') }}" enctype="multipart/form-data" class="space-y-6" novalidate>
                @csrf
                @method('PUT')

                <!-- Logo Section -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Company Logo / Workshop Branding
                    </label>
                    <div class="flex items-center space-x-6">
                        <!-- Current Logo Preview -->
                        <div class="w-24 h-24 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0 shadow-inner">
                            @if($company->logo_path)
                                <img src="{{ asset('storage/' . $company->logo_path') }}" alt="Company Logo" class="max-h-full max-w-full object-contain p-2" />
                            @else
                                <div class="text-center p-2 text-gray-400">
                                    <svg class="mx-auto h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-[10px] mt-1 block">No Logo</span>
                                </div>
                            @endif
                        </div>

                        <!-- Upload Input -->
                        <div class="flex-1">
                            <input 
                                type="file" 
                                name="logo" 
                                id="logo" 
                                accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
                            />
                            <p class="mt-1.5 text-xs text-gray-400">Recommended: PNG or SVG with transparent background, max size 2MB.</p>
                            @error('logo')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Company Name -->
                    <div class="md:col-span-2">
                        <label for="company_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Company / Center Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input 
                                type="text" 
                                name="company_name" 
                                id="company_name" 
                                required 
                                value="{{ old('company_name', $company->company_name) }}" 
                                placeholder="e.g. AutoCare Service Center Pvt Ltd"
                                class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 @error('company_name') border-rose-400 bg-rose-50/30 @enderror"
                            />
                        </div>
                        @error('company_name')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Business Registration Number -->
                    <div>
                        <label for="registration_number" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Business Registration Number (BRN)
                        </label>
                        <div class="mt-1">
                            <input 
                                type="text" 
                                name="registration_number" 
                                id="registration_number" 
                                value="{{ old('registration_number', $company->registration_number) }}" 
                                placeholder="e.g. PV-12345678"
                                class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 font-mono @error('registration_number') border-rose-400 bg-rose-50/30 @enderror"
                            />
                        </div>
                        @error('registration_number')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tax / VAT Number (Optional) -->
                    <div>
                        <label for="tax_number" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Tax / VAT Number <span class="text-gray-400 font-normal">(Optional)</span>
                        </label>
                        <div class="mt-1">
                            <input 
                                type="text" 
                                name="tax_number" 
                                id="tax_number" 
                                value="{{ old('tax_number', $company->tax_number) }}" 
                                placeholder="e.g. VAT-987654321"
                                class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 font-mono @error('tax_number') border-rose-400 bg-rose-50/30 @enderror"
                            />
                        </div>
                        @error('tax_number')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contact Phone -->
                    <div>
                        <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Phone Number
                        </label>
                        <div class="mt-1">
                            <input 
                                type="text" 
                                name="phone" 
                                id="phone" 
                                value="{{ old('phone', $company->phone) }}" 
                                placeholder="e.g. +94 11 234 5678"
                                class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 @error('phone') border-rose-400 bg-rose-50/30 @enderror"
                            />
                        </div>
                        @error('phone')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contact Email -->
                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Official Email Address
                        </label>
                        <div class="mt-1">
                            <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                value="{{ old('email', $company->email) }}" 
                                placeholder="e.g. service@autocare.com"
                                class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 @error('email') border-rose-400 bg-rose-50/30 @enderror"
                            />
                        </div>
                        @error('email')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Physical Workshop Address -->
                    <div class="md:col-span-2">
                        <label for="address" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Center Workshop Address
                        </label>
                        <div class="mt-1">
                            <textarea 
                                name="address" 
                                id="address" 
                                rows="3" 
                                placeholder="Street address, city, postal code..."
                                class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 @error('address') border-rose-400 bg-rose-50/30 @enderror"
                            >{{ old('address', $company->address) }}</textarea>
                        </div>
                        @error('address')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a 
                        href="{{ route('dashboard') }}" 
                        class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                    >
                        Back to Dashboard
                    </a>
                    <button 
                        type="submit" 
                        class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-xs focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
                    >
                        Save Company Details
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
