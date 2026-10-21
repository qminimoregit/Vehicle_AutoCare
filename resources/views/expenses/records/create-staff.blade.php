@extends('layouts.app')

@section('title', 'Add Staff Expense')

@section('content')
<div class="max-w-3xl mx-auto" x-data="{
    selectedCategoryName: '',
    updateCategory(e) {
        let text = e.target.options[e.target.selectedIndex].text.toLowerCase().trim();
        this.selectedCategoryName = text;
    }
}">
    <!-- Page Header -->
    <x-page-header 
        title="Add Staff Expense" 
        description="Record salary advances, staff welfare, and employee-related payments."
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
        <form action="{{ route('expenses.staff.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Salary Advance Notification Banner -->
            <div 
                x-show="selectedCategoryName.includes('advance')" 
                x-cloak 
                class="p-4 rounded-xl bg-indigo-50 border border-indigo-200 text-xs text-indigo-900 flex items-start space-x-3"
            >
                <svg class="w-5 h-5 text-indigo-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h5 class="font-bold text-indigo-900">Payroll Integration Active</h5>
                    <p class="mt-0.5 leading-relaxed text-indigo-700">
                        This payment will be recorded as a <strong>Salary Advance</strong> and automatically linked to the employee's payroll. It will be deducted at month-end salary processing and will <em>not</em> be counted twice in Profit & Loss.
                    </p>
                </div>
            </div>

            <!-- Branch Selection -->
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

            <!-- Employee & Category Selection -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Employee -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Employee Name <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        name="employee_id" 
                        required 
                        class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                        <option value="">Select Employee...</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }} ({{ $emp->employee_number }}) - {{ $emp->designation ?? 'Staff' }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expense Category -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Expense Category <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        name="category_id" 
                        required 
                        @change="updateCategory($event)"
                        class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                        <option value="">Select Category...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id || (strtolower($cat->name) === 'salary advance' && !old('category_id')) ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Date & Related Month -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Payment Date -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Payment Date <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        name="payment_date" 
                        value="{{ old('payment_date', today()->toDateString()) }}" 
                        required 
                        class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                    @error('payment_date')
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
                        value="{{ old('related_month', $currentMonth) }}" 
                        placeholder="e.g. October 2026" 
                        required 
                        class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                    @error('related_month')
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
                            value="{{ old('amount') }}" 
                            placeholder="0.00" 
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
                        <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>Card</option>
                        <option value="other" {{ old('payment_method') === 'other' ? 'selected' : '' }}>Other</option>
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
                    value="{{ old('payment_reference') }}" 
                    placeholder="e.g. Bank slip reference, Cheque #, Transfer confirmation ID" 
                    class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                >
                @error('payment_reference')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description / Memo -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Description / Comment <span class="text-gray-400 font-normal">(Optional)</span>
                </label>
                <textarea 
                    name="description" 
                    rows="3" 
                    placeholder="Provide details about this staff payment or salary advance..." 
                    class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Receipt Attachment -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Receipt Attachment <span class="text-gray-400 font-normal">(Optional - JPG, PNG, WEBP, PDF up to 5MB)</span>
                </label>
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
