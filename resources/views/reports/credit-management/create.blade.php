@extends('layouts.app')

@section('title', 'Register Credit Account')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-gray-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-[#0B1849] tracking-tight">Register Credit Account</h1>
            <p class="text-sm text-gray-500 mt-1">Assign approved credit facility and terms to an existing customer or company.</p>
        </div>
        <a href="{{ route('reports.credit-management.index') }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 px-3 py-2 rounded-lg transition">
            Back to Accounts
        </a>
    </div>

    <!-- Registration Form -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-6">
        <form method="POST" action="{{ route('reports.credit-management.store') }}" class="space-y-5">
            @csrf

            <!-- Customer Selection -->
            <div>
                <label for="customer_id" class="block text-xs font-semibold text-gray-700 mb-1">Select Customer / Company <span class="text-rose-500">*</span></label>
                <select id="customer_id" name="customer_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]">
                    <option value="">-- Choose a registered customer --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->display_name }} ({{ $c->phone }}) [{{ ucfirst($c->customer_type) }}]
                        </option>
                    @endforeach
                </select>
                @error('customer_id')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Credit Limit & Payment Terms -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="credit_limit" class="block text-xs font-semibold text-gray-700 mb-1">Credit Limit (LKR) <span class="text-rose-500">*</span></label>
                    <input 
                        type="number" 
                        step="0.01" 
                        min="0" 
                        id="credit_limit" 
                        name="credit_limit" 
                        value="{{ old('credit_limit', '100000.00') }}" 
                        required 
                        class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]"
                    >
                    @error('credit_limit')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="payment_terms_days" class="block text-xs font-semibold text-gray-700 mb-1">Payment Terms (Days) <span class="text-rose-500">*</span></label>
                    <input 
                        type="number" 
                        min="0" 
                        max="365" 
                        id="payment_terms_days" 
                        name="payment_terms_days" 
                        value="{{ old('payment_terms_days', 30) }}" 
                        required 
                        class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]"
                    >
                    @error('payment_terms_days')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Opening Balance & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="opening_balance" class="block text-xs font-semibold text-gray-700 mb-1">Opening Balance (LKR, if any)</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        min="0" 
                        id="opening_balance" 
                        name="opening_balance" 
                        value="{{ old('opening_balance', '0.00') }}" 
                        class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]"
                    >
                    <p class="text-[11px] text-gray-400 mt-0.5">Historical balance carried forward from previous system.</p>
                    @error('opening_balance')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold text-gray-700 mb-1">Account Status <span class="text-rose-500">*</span></label>
                    <select id="status" name="status" required class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active (Allow Credit)</option>
                        <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended (Hold Credit)</option>
                        <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                    @error('status')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-xs font-semibold text-gray-700 mb-1">Approval Notes / Terms</label>
                <textarea 
                    id="notes" 
                    name="notes" 
                    rows="3" 
                    placeholder="Enter any special agreement, approval references or contact conditions..." 
                    class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]"
                >{{ old('notes') }}</textarea>
                @error('notes')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                <a href="{{ route('reports.credit-management.index') }}" class="px-4 py-2 text-xs font-semibold bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2 text-xs font-semibold bg-[#0B1849] text-white rounded-lg hover:bg-[#0B1849]/90 transition shadow-xs">
                    Save Credit Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
