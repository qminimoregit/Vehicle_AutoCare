@extends('layouts.app')

@section('title', 'Add Shop Expense')

@section('content')
<div class="max-w-3xl mx-auto" x-data="{
    paymentStatusChoice: '{{ old('payment_status_choice', 'paid_now') }}',
    amount: '{{ old('amount', '') }}',
    paymentAmount: '{{ old('payment_amount', '') }}',
    
    syncAmount() {
        if (this.paymentStatusChoice === 'paid_now' && (!this.paymentAmount || this.paymentAmount === '')) {
            this.paymentAmount = this.amount;
        }
    }
}">
    <!-- Page Header -->
    <x-page-header 
        title="Add Shop Expense" 
        description="Record shop operating expenses, utility bills, facility repairs, and business costs."
    >
        <x-slot:actions>
            <a 
                href="{{ route('expenses.records.index') }}" 
                class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-lg transition"
            >
                &larr; Back to Records
            </a>
        </x-slot:actions>
    </x-page-header>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
        <form action="{{ route('expenses.shop.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Hidden Related Month for backend compatibility -->
            <input type="hidden" name="related_month" value="{{ old('related_month', $currentMonth) }}">

            <!-- Expense Category & Branch -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Expense Category -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Expense Category <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        name="category_id" 
                        required 
                        class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                        <option value="">Select Category...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Branch -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Branch <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        name="branch_id" 
                        required 
                        class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ (string)old('branch_id', $activeBranchId) === (string)$b->id ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Expense Date & Amount -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Expense Date -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Expense Date <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        name="expense_date" 
                        value="{{ old('expense_date', today()->toDateString()) }}" 
                        required 
                        class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                    @error('expense_date')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount (LKR) -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Amount (LKR) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs font-bold text-gray-400">
                            LKR
                        </span>
                        <input 
                            type="number" 
                            name="amount" 
                            x-model="amount"
                            @input="syncAmount()"
                            step="0.01" 
                            min="0.01" 
                            placeholder="0.00" 
                            required 
                            class="block w-full pl-12 pr-3.5 py-2.5 text-sm font-semibold text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                        >
                    </div>
                    @error('amount')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Description <span class="text-gray-400 font-normal">(Optional)</span>
                </label>
                <textarea 
                    name="description" 
                    rows="2" 
                    placeholder="e.g. Monthly electricity bill for service bays, generator diesel, water supply..." 
                    class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- PAYMENT STATUS CHOICE: Paid Now vs Pay Later (Section 4) -->
            <div class="pt-4 border-t border-gray-100">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">
                    Payment Status <span class="text-rose-500">*</span>
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Option 1: Paid Now -->
                    <label 
                        @click="paymentStatusChoice = 'paid_now'; syncAmount();"
                        :class="paymentStatusChoice === 'paid_now' ? 'border-[#0B1849] bg-[#EEF1FA]/30 ring-2 ring-[#0B1849]' : 'border-gray-200 hover:border-gray-300 bg-white'"
                        class="flex items-center p-4 border rounded-xl cursor-pointer transition relative"
                    >
                        <input 
                            type="radio" 
                            name="payment_status_choice" 
                            value="paid_now" 
                            x-model="paymentStatusChoice"
                            class="h-4 w-4 text-[#0B1849] border-gray-300 focus:ring-[#0B1849]"
                        >
                        <div class="ml-3">
                            <span class="block text-sm font-bold text-gray-900">Paid Now</span>
                            <span class="block text-xs text-gray-500 mt-0.5">Payment was made immediately</span>
                        </div>
                    </label>

                    <!-- Option 2: Pay Later -->
                    <label 
                        @click="paymentStatusChoice = 'pay_later'"
                        :class="paymentStatusChoice === 'pay_later' ? 'border-[#0B1849] bg-[#EEF1FA]/30 ring-2 ring-[#0B1849]' : 'border-gray-200 hover:border-gray-300 bg-white'"
                        class="flex items-center p-4 border rounded-xl cursor-pointer transition relative"
                    >
                        <input 
                            type="radio" 
                            name="payment_status_choice" 
                            value="pay_later" 
                            x-model="paymentStatusChoice"
                            class="h-4 w-4 text-[#0B1849] border-gray-300 focus:ring-[#0B1849]"
                        >
                        <div class="ml-3">
                            <span class="block text-sm font-bold text-gray-900">Pay Later</span>
                            <span class="block text-xs text-gray-500 mt-0.5">Save with outstanding balance</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- CONDITIONAL PAYMENT FIELDS (If Paid Now is selected) -->
            <div 
                x-show="paymentStatusChoice === 'paid_now'" 
                x-transition 
                class="p-5 bg-gray-50/70 border border-gray-200 rounded-xl space-y-5"
            >
                <div class="flex items-center space-x-2 text-xs font-bold text-[#0B1849] uppercase tracking-wider pb-2 border-b border-gray-200">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Payment Details</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Payment Method -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Payment Method <span class="text-rose-500">*</span>
                        </label>
                        <select 
                            name="payment_method" 
                            :required="paymentStatusChoice === 'paid_now'"
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                        >
                            <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>Card</option>
                            <option value="cheque" {{ old('payment_method') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="other" {{ old('payment_method') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('payment_method')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Date -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Payment Date <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="payment_date" 
                            value="{{ old('payment_date', today()->toDateString()) }}" 
                            :required="paymentStatusChoice === 'paid_now'"
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                        >
                        @error('payment_date')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Payment Amount -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Payment Amount (LKR)
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs font-bold text-gray-400">
                                LKR
                            </span>
                            <input 
                                type="number" 
                                name="payment_amount" 
                                x-model="paymentAmount"
                                step="0.01" 
                                min="0.01" 
                                placeholder="Defaults to full amount" 
                                class="block w-full pl-12 pr-3.5 py-2.5 text-sm font-semibold text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                            >
                        </div>
                        <p class="mt-1 text-[11px] text-gray-500">Leave blank or equal to amount for full payment.</p>
                        @error('payment_amount')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Reference (optional) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Reference <span class="text-gray-400 font-normal">(Optional)</span>
                        </label>
                        <input 
                            type="text" 
                            name="payment_reference" 
                            value="{{ old('payment_reference') }}" 
                            placeholder="e.g. Cheque #, Bank transfer slip #, Bill account #" 
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                        >
                        @error('payment_reference')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Receipt Image (optional) -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Receipt Image / File <span class="text-gray-400 font-normal">(Optional, max 5MB)</span>
                    </label>
                    <input 
                        type="file" 
                        name="receipt" 
                        accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" 
                        class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
                    >
                    @error('receipt')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- PAY LATER INFO NOTICE -->
            <div 
                x-show="paymentStatusChoice === 'pay_later'" 
                x-transition 
                class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start space-x-3 text-amber-900"
                style="display: none;"
            >
                <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-xs space-y-1">
                    <p class="font-bold">Outstanding Balance Recorded</p>
                    <p class="text-amber-800">
                        This expense will be saved with status <span class="font-semibold underline">Unpaid</span>. No cash or bank outflow will be recorded until an employee makes a payment using the <strong>Pay</strong> action on the Expense Records list.
                    </p>
                </div>
            </div>

            <!-- Form Submit Buttons -->
            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-gray-100">
                <a 
                    href="{{ route('expenses.records.index') }}" 
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                >
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-2.5 text-sm font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-xs transition cursor-pointer"
                >
                    Save Expense
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
