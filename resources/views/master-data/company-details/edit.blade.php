@extends('layouts.app')

@section('title', 'Company Details')

@section('content')
    <div class="max-w-3xl mx-auto">
        <!-- Page Header -->
        <x-page-header 
            title="Company Details" 
            description="Manage the auto care center's own business details."
        />

        @if(!($canEdit ?? Auth::user()->isAdmin()))
            <div class="mb-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-amber-800 text-xs">
                <strong>View Only Mode:</strong> You are logged in as staff. Administrator access is required to modify company details.
            </div>
        @endif

        <!-- Company Information Card -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 sm:p-8 shadow-xs">
            <h2 class="text-base font-bold text-gray-900 mb-6 pb-3 border-b border-gray-100">Company Information</h2>

            <form method="POST" action="{{ route('master-data.company-details.update') }}" enctype="multipart/form-data" class="space-y-6" novalidate>
                @csrf
                @method('PUT')

                <!-- 1. Company Name -->
                <div>
                    <label for="company_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Company Name <span class="text-rose-500">*</span>
                    </label>
                    <div class="mt-1.5">
                        <input 
                            type="text" 
                            name="company_name" 
                            id="company_name" 
                            required 
                            @disabled(!($canEdit ?? Auth::user()->isAdmin()))
                            value="{{ old('company_name', $company->company_name) }}" 
                            placeholder="Enter Company Name"
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] disabled:bg-gray-100 disabled:text-gray-500 @error('company_name') border-rose-400 bg-rose-50/30 @enderror"
                        />
                    </div>
                    <x-input-error for="company_name" />
                </div>

                <!-- 2. Registration Number -->
                <div>
                    <label for="registration_number" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Registration Number
                    </label>
                    <div class="mt-1.5">
                        <input 
                            type="text" 
                            name="registration_number" 
                            id="registration_number" 
                            @disabled(!($canEdit ?? Auth::user()->isAdmin()))
                            value="{{ old('registration_number', $company->registration_number) }}" 
                            placeholder="Enter Business Registration Number"
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] font-mono disabled:bg-gray-100 disabled:text-gray-500 @error('registration_number') border-rose-400 bg-rose-50/30 @enderror"
                        />
                    </div>
                    <x-input-error for="registration_number" />
                </div>

                <!-- 3. Contact Number -->
                <div>
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Contact Number
                    </label>
                    <div class="mt-1.5">
                        <input 
                            type="text" 
                            name="phone" 
                            id="phone" 
                            @disabled(!($canEdit ?? Auth::user()->isAdmin()))
                            value="{{ old('phone', $company->phone) }}" 
                            placeholder="e.g. +94 11 234 5678"
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] disabled:bg-gray-100 disabled:text-gray-500 @error('phone') border-rose-400 bg-rose-50/30 @enderror"
                        />
                    </div>
                    <x-input-error for="phone" />
                </div>

                <!-- 4. Email -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Email
                    </label>
                    <div class="mt-1.5">
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            @disabled(!($canEdit ?? Auth::user()->isAdmin()))
                            value="{{ old('email', $company->email) }}" 
                            placeholder="e.g. info@autocare.local"
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] disabled:bg-gray-100 disabled:text-gray-500 @error('email') border-rose-400 bg-rose-50/30 @enderror"
                        />
                    </div>
                    <x-input-error for="email" />
                </div>

                <!-- 5. Address -->
                <div>
                    <label for="address" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Address
                    </label>
                    <div class="mt-1.5">
                        <textarea 
                            name="address" 
                            id="address" 
                            rows="3" 
                            @disabled(!($canEdit ?? Auth::user()->isAdmin()))
                            placeholder="Enter Physical Workshop Address"
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] disabled:bg-gray-100 disabled:text-gray-500 @error('address') border-rose-400 bg-rose-50/30 @enderror"
                        >{{ old('address', $company->address) }}</textarea>
                    </div>
                    <x-input-error for="address" />
                </div>

                <!-- 6. Tax Number (Optional) -->
                <div>
                    <label for="tax_number" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Tax Number <span class="text-gray-400 font-normal">(Optional)</span>
                    </label>
                    <div class="mt-1.5">
                        <input 
                            type="text" 
                            name="tax_number" 
                            id="tax_number" 
                            @disabled(!($canEdit ?? Auth::user()->isAdmin()))
                            value="{{ old('tax_number', $company->tax_number) }}" 
                            placeholder="e.g. VAT-123456789-000"
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] font-mono disabled:bg-gray-100 disabled:text-gray-500 @error('tax_number') border-rose-400 bg-rose-50/30 @enderror"
                        />
                    </div>
                    <x-input-error for="tax_number" />
                </div>

                <!-- 7. Company Logo -->
                <div>
                    <label for="logo" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Company Logo
                    </label>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <!-- Current Logo Preview -->
                        <div class="w-24 h-24 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                            @if($company->logo_path)
                                <img src="{{ asset('storage/' . $company->logo_path) }}" alt="Company Logo" class="max-h-full max-w-full object-contain p-2" />
                            @else
                                <div class="text-center p-2 text-gray-400">
                                    <svg class="mx-auto h-7 w-7 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-[10px] mt-1 block">No Logo</span>
                                </div>
                            @endif
                        </div>

                        <!-- Upload Control -->
                        @if($canEdit ?? Auth::user()->isAdmin())
                            <div class="flex-1">
                                <input 
                                    type="file" 
                                    name="logo" 
                                    id="logo" 
                                    accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                    class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#0B1849] file:text-white hover:file:bg-[#15286e] cursor-pointer"
                                />
                                <p class="mt-1.5 text-xs text-gray-400">Supported: PNG, JPG, JPEG, SVG, WEBP (Max 2MB).</p>
                                <x-input-error for="logo" />
                            </div>
                        @else
                            <div class="text-xs text-gray-400">
                                Logo uploads restricted to administrators.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Submit Button: [Save Changes] -->
                @if($canEdit ?? Auth::user()->isAdmin())
                    <div class="pt-6 border-t border-gray-100 flex items-center justify-end">
                        <button 
                            type="submit" 
                            class="px-6 py-2.5 text-sm font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-xs transition duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B1849]"
                        >
                            Save Changes
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>
@endsection
