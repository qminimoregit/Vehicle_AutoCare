@extends('layouts.app')

@section('title', 'Edit Expense - ' . $expense->expense_number)

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Page Header -->
    <x-page-header 
        title="Edit Expense {{ $expense->expense_number }}" 
        description="Authorized financial record modification."
    >
        <x-slot:actions>
            <a 
                href="{{ route('expenses.records.show', $expense) }}" 
                class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-lg transition"
            >
                &larr; Cancel & Back
            </a>
        </x-slot:actions>
    </x-page-header>

    @if($expense->advance)
        <div class="mb-6 p-4 rounded-xl bg-indigo-50 border border-indigo-200 text-xs text-indigo-900 flex items-start space-x-3">
            <svg class="w-5 h-5 text-indigo-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h5 class="font-bold text-indigo-900">Linked Salary Advance: {{ $expense->advance->advance_number }}</h5>
                <p class="mt-0.5 text-indigo-700">
                    Modifying the amount here will automatically synchronize with the linked employee advance (Status: {{ ucfirst($expense->advance->status) }}).
                </p>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
        <form action="{{ route('expenses.records.update', $expense) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Category & Related Month -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Category -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Expense Category <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        name="category_id" 
                        required 
                        class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $expense->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Related Month -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Related Month <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="related_month" 
                        value="{{ old('related_month', $expense->related_month) }}" 
                        required 
                        class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                    @error('related_month')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Expense Date & Payment Date -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Expense Date -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Expense Date <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        name="expense_date" 
                        value="{{ old('expense_date', $expense->expense_date->toDateString()) }}" 
                        required 
                        class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                    @error('expense_date')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Date -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Payment Date
                    </label>
                    <input 
                        type="date" 
                        name="payment_date" 
                        value="{{ old('payment_date', $expense->payment_date ? $expense->payment_date->toDateString() : $expense->expense_date->toDateString()) }}" 
                        class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                    @error('payment_date')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Amount & Payment Method -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Amount -->
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
                            step="0.01" 
                            min="0.01" 
                            value="{{ old('amount', $expense->amount) }}" 
                            required 
                            class="block w-full pl-12 pr-3.5 py-2.5 text-sm font-semibold text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                        >
                    </div>
                    @error('amount')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Payment Method <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        name="payment_method" 
                        required 
                        class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                        <option value="cash" {{ old('payment_method', $expense->payment_method) === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="bank_transfer" {{ old('payment_method', $expense->payment_method) === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="card" {{ old('payment_method', $expense->payment_method) === 'card' ? 'selected' : '' }}>Card</option>
                        <option value="other" {{ old('payment_method', $expense->payment_method) === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('payment_method')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Payment Reference -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Payment Reference <span class="text-gray-400 font-normal">(Optional)</span>
                </label>
                <input 
                    type="text" 
                    name="payment_reference" 
                    value="{{ old('payment_reference', $expense->payment_reference) }}" 
                    placeholder="e.g. Cheque #, Bank slip ref" 
                    class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                >
                @error('payment_reference')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description / Comment -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Description / Comment <span class="text-gray-400 font-normal">(Optional)</span>
                </label>
                <textarea 
                    name="description" 
                    rows="3" 
                    class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                >{{ old('description', $expense->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Receipt Attachment -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Replace Receipt <span class="text-gray-400 font-normal">(Optional - JPG, PNG, WEBP, PDF up to 5MB)</span>
                </label>
                @if($expense->receipt_path)
                    <p class="text-xs text-gray-500 mb-2">
                        Current file: <span class="font-mono text-gray-700">{{ basename($expense->receipt_path) }}</span>
                    </p>
                @endif
                <input 
                    type="file" 
                    name="receipt" 
                    accept=".jpg,.jpeg,.png,.webp,.pdf"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#EEF1FA] file:text-[#0B1849] hover:file:bg-[#d8e0f5] cursor-pointer"
                >
                @error('receipt')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="pt-6 border-t border-gray-200 flex items-center justify-end space-x-3">
                <a 
                    href="{{ route('expenses.records.show', $expense) }}" 
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                >
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-2.5 text-sm font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-xs transition cursor-pointer"
                >
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
