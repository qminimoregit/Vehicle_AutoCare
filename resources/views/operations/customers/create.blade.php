@extends('layouts.app')

@section('title', 'New Customer Registration')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">
                    New Customer Registration
                </h1>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1 ml-8">
                Register a new customer profile. You can link vehicles and start Job Cards immediately after saving.
            </p>
        </div>
    </div>

    <!-- Registration Card -->
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-xs">
        <form action="{{ route('operations.customers.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Customer Type -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                    Customer Type
                </label>
                <div class="flex items-center space-x-6 text-sm" x-data="{ type: '{{ old('customer_type', 'individual') }}' }">
                    <label class="inline-flex items-center cursor-pointer">
                        <input 
                            type="radio" 
                            name="customer_type" 
                            value="individual" 
                            x-model="type"
                            class="text-[#0B1849] focus:ring-[#0B1849]"
                        >
                        <span class="ml-2 text-gray-800 font-medium">Individual</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input 
                            type="radio" 
                            name="customer_type" 
                            value="company" 
                            x-model="type"
                            class="text-[#0B1849] focus:ring-[#0B1849]"
                        >
                        <span class="ml-2 text-gray-800 font-medium">Corporate / Company</span>
                    </label>

                    <!-- Company Name (if corporate) -->
                    <div x-show="type === 'company'" class="w-full mt-3" style="display: none;">
                        <label for="company_name" class="block text-xs font-semibold text-gray-700 mb-1">Company Name</label>
                        <input 
                            type="text" 
                            name="company_name" 
                            id="company_name"
                            value="{{ old('company_name') }}"
                            placeholder="e.g. Ceylon Logistics Ltd"
                            class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                        >
                    </div>
                </div>
            </div>

            <!-- Customer Name & Mobile Number -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="customer_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                        Customer Full Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="customer_name" 
                        id="customer_name"
                        value="{{ old('customer_name') }}"
                        required
                        placeholder="e.g. Kasun Perera"
                        class="block w-full py-2.5 px-3 text-sm text-gray-900 border @error('customer_name') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                    >
                    @error('customer_name')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                        Mobile Number <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="phone" 
                        id="phone"
                        value="{{ old('phone') }}"
                        required
                        placeholder="e.g. 0771234567"
                        class="block w-full py-2.5 px-3 text-sm text-gray-900 border @error('phone') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                    >
                    @error('phone')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Email & Address -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                        Email Address (Optional)
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email"
                        value="{{ old('email') }}"
                        placeholder="e.g. customer@example.com"
                        class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                    >
                    @error('email')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="address" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                        Address (Optional)
                    </label>
                    <input 
                        type="text" 
                        name="address" 
                        id="address"
                        value="{{ old('address') }}"
                        placeholder="e.g. 124 Galle Road, Colombo 03"
                        class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                    >
                    @error('address')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                    Customer Notes (Optional)
                </label>
                <textarea 
                    name="notes" 
                    id="notes" 
                    rows="2"
                    placeholder="Any specific customer preferences, contact instructions, etc."
                    class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white"
                >{{ old('notes') }}</textarea>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                <a 
                    href="{{ route('dashboard') }}" 
                    class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition"
                >
                    Cancel
                </a>

                <button 
                    type="submit" 
                    class="inline-flex items-center justify-center px-6 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white font-bold text-sm rounded-lg shadow-xs transition cursor-pointer min-h-[44px]"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Register Customer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
