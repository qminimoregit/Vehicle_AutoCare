@extends('layouts.app')

@section('title', 'Edit Employee - ' . $employee->name)

@section('content')
<div 
    class="max-w-3xl mx-auto space-y-6"
    x-data="{
        currentSalaryType: '{{ $employee->salary_type ?? 'daily' }}',
        salaryType: '{{ old('salary_type', $employee->salary_type ?? 'daily') }}',
        currentDailyWage: '{{ (float) $employee->daily_wage }}',
        inputDailyWage: '{{ old('daily_wage', (float) $employee->daily_wage) }}',
        currentMonthlySalary: '{{ (float) ($employee->monthly_basic_salary ?? 0) }}',
        inputMonthlySalary: '{{ old('monthly_basic_salary', (float) ($employee->monthly_basic_salary ?? 0)) }}',
        paymentMethod: '{{ old('payment_method', $employee->payment_method ?? 'cash') }}',
        isRateChanged() {
            if (this.salaryType !== this.currentSalaryType) return true;
            if (this.salaryType === 'daily') {
                return parseFloat(this.inputDailyWage || 0) !== parseFloat(this.currentDailyWage || 0);
            } else {
                return parseFloat(this.inputMonthlySalary || 0) !== parseFloat(this.currentMonthlySalary || 0);
            }
        }
    }"
>
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">Edit Employee</h1>
            <p class="text-xs text-gray-500 mt-0.5">Update employee profile, salary configuration, and statutory settings.</p>
        </div>
        <div class="flex gap-2">
            <a 
                href="{{ route('payroll.employees.show', $employee) }}" 
                class="inline-flex items-center text-xs font-semibold text-gray-600 hover:text-gray-900 bg-white border border-gray-200 px-3 py-1.5 rounded-lg shadow-xs transition"
            >
                View Profile
            </a>
            <a 
                href="{{ route('payroll.employees.index') }}" 
                class="inline-flex items-center text-xs font-semibold text-gray-600 hover:text-gray-900 bg-white border border-gray-200 px-3 py-1.5 rounded-lg shadow-xs transition"
            >
                Back to List
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-6">
        <form method="POST" action="{{ route('payroll.employees.update', $employee) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1: Basic Information -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider pb-1 border-b border-gray-100">
                    Basic Profile
                </h3>

                <!-- Employee ID & Name -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Employee ID
                        </label>
                        <input 
                            type="text" 
                            value="{{ $employee->employee_number }}" 
                            disabled 
                            class="block w-full py-2 px-3 text-sm font-mono font-bold bg-gray-50 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed"
                        >
                    </div>

                    <div class="sm:col-span-2">
                        <label for="name" class="block text-xs font-semibold text-gray-700 mb-1">
                            Employee Name <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name', $employee->name) }}" 
                            required 
                            class="block w-full py-2 px-3 text-sm text-gray-900 border @error('name') border-rose-300 bg-rose-50/30 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                        >
                        @error('name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Position & Branch & Status -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="position" class="block text-xs font-semibold text-gray-700 mb-1">
                            Position / Role
                        </label>
                        <input 
                            type="text" 
                            name="position" 
                            id="position" 
                            value="{{ old('position', $employee->position) }}" 
                            placeholder="e.g. Senior Mechanic" 
                            class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                        >
                    </div>

                    <div>
                        <label for="branch_id" class="block text-xs font-semibold text-gray-700 mb-1">
                            Branch
                        </label>
                        <select 
                            name="branch_id" 
                            id="branch_id" 
                            class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                        >
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', $employee->branch_id) == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->branch_name }} {{ $branch->is_main_branch ? '(Main)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="is_active" class="block text-xs font-semibold text-gray-700 mb-1">
                            Employment Status <span class="text-rose-500">*</span>
                        </label>
                        <select 
                            name="is_active" 
                            id="is_active" 
                            class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                        >
                            <option value="1" {{ old('is_active', $employee->is_active) ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !old('is_active', $employee->is_active) ? 'selected' : '' }}>Inactive / Suspended</option>
                        </select>
                    </div>
                </div>

                <!-- Mobile & Address & Joining Date -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="mobile" class="block text-xs font-semibold text-gray-700 mb-1">
                            Mobile Number
                        </label>
                        <input 
                            type="text" 
                            name="mobile" 
                            id="mobile" 
                            value="{{ old('mobile', $employee->mobile) }}" 
                            placeholder="e.g. 0771234567" 
                            class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                        >
                    </div>

                    <div>
                        <label for="joining_date" class="block text-xs font-semibold text-gray-700 mb-1">
                            Joining Date
                        </label>
                        <input 
                            type="date" 
                            name="joining_date" 
                            id="joining_date" 
                            value="{{ old('joining_date', $employee->joining_date ? $employee->joining_date->toDateString() : '') }}" 
                            class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                        >
                    </div>

                    <div>
                        <label for="address" class="block text-xs font-semibold text-gray-700 mb-1">
                            Residential Address
                        </label>
                        <input 
                            type="text" 
                            name="address" 
                            id="address" 
                            value="{{ old('address', $employee->address) }}" 
                            placeholder="Enter employee address..." 
                            class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                        >
                    </div>
                </div>
            </div>

            <!-- Section 2: Salary Configuration -->
            <div class="space-y-4 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between pb-1 border-b border-gray-100">
                    <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">
                        Salary Structure
                    </h3>
                    <span class="text-[11px] text-gray-400">Current: {{ ucfirst($employee->salary_type ?? 'daily') }} Wage/Salary</span>
                </div>

                <!-- Salary Type Buttons -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">
                        Salary Type <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3 max-w-md">
                        <label 
                            class="relative flex items-center justify-center p-3 rounded-xl border cursor-pointer transition"
                            :class="salaryType === 'daily' ? 'border-[#0B1849] bg-[#EEF1FA] text-[#0B1849] font-bold ring-2 ring-[#0B1849]' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'"
                        >
                            <input 
                                type="radio" 
                                name="salary_type" 
                                value="daily" 
                                x-model="salaryType" 
                                class="sr-only"
                            >
                            <div class="text-center">
                                <span class="block text-sm">Daily Wage</span>
                                <span class="block text-[11px] text-gray-500 mt-0.5">Calculated by daily attendance</span>
                            </div>
                        </label>

                        <label 
                            class="relative flex items-center justify-center p-3 rounded-xl border cursor-pointer transition"
                            :class="salaryType === 'monthly' ? 'border-[#0B1849] bg-[#EEF1FA] text-[#0B1849] font-bold ring-2 ring-[#0B1849]' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'"
                        >
                            <input 
                                type="radio" 
                                name="salary_type" 
                                value="monthly" 
                                x-model="salaryType" 
                                class="sr-only"
                            >
                            <div class="text-center">
                                <span class="block text-sm">Monthly Salary</span>
                                <span class="block text-[11px] text-gray-500 mt-0.5">Fixed basic monthly salary</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Conditional Rate Field: Daily Wage -->
                <div x-show="salaryType === 'daily'" x-transition class="max-w-md">
                    <label for="daily_wage" class="block text-xs font-semibold text-gray-700 mb-1">
                        Daily Wage (LKR) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-gray-400 pointer-events-none">
                            LKR
                        </span>
                        <input 
                            type="number" 
                            name="daily_wage" 
                            id="daily_wage" 
                            step="0.01" 
                            min="0" 
                            x-model="inputDailyWage"
                            class="block w-full pl-12 pr-3 py-2 text-sm text-gray-900 font-mono font-semibold border @error('daily_wage') border-rose-300 bg-rose-50/30 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                        >
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1">Rate paid for 1 full working day. Half day is calculated as 50%.</p>
                </div>

                <!-- Conditional Rate Field: Monthly Basic Salary -->
                <div x-show="salaryType === 'monthly'" x-transition class="max-w-md" style="display: none;">
                    <label for="monthly_basic_salary" class="block text-xs font-semibold text-gray-700 mb-1">
                        Monthly Basic Salary (LKR) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-gray-400 pointer-events-none">
                            LKR
                        </span>
                        <input 
                            type="number" 
                            name="monthly_basic_salary" 
                            id="monthly_basic_salary" 
                            step="0.01" 
                            min="0" 
                            x-model="inputMonthlySalary"
                            class="block w-full pl-12 pr-3 py-2 text-sm text-gray-900 font-mono font-semibold border @error('monthly_basic_salary') border-rose-300 bg-rose-50/30 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                        >
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1">Fixed basic monthly salary before overtime, bonuses, or valid payroll deductions.</p>
                </div>

                <!-- Conditional Wage Rate Change Notice & Effective Date -->
                <div 
                    x-show="isRateChanged()" 
                    x-transition 
                    class="bg-amber-50 border border-amber-200 rounded-xl p-4 space-y-3"
                    style="display: none;"
                >
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-amber-600 mr-2 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h4 class="text-xs font-bold text-amber-900">Salary / Wage Adjustment Detected</h4>
                            <p class="text-xs text-amber-800 mt-0.5">
                                You are modifying the employee's salary type or rate. A new immutable salary rate record will be saved to preserve past historical payroll and attendance calculations.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-amber-200/60">
                        <div>
                            <label for="wage_effective_from" class="block text-xs font-semibold text-amber-900 mb-1">
                                Effective Date
                            </label>
                            <input 
                                type="date" 
                                name="wage_effective_from" 
                                id="wage_effective_from" 
                                value="{{ old('wage_effective_from', today()->toDateString()) }}" 
                                class="block w-full py-1.5 px-3 text-xs text-gray-900 border border-amber-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]"
                            >
                        </div>
                        <div>
                            <label for="wage_reason" class="block text-xs font-semibold text-amber-900 mb-1">
                                Reason for Change
                            </label>
                            <input 
                                type="text" 
                                name="wage_reason" 
                                id="wage_reason" 
                                value="{{ old('wage_reason', 'Salary revision') }}" 
                                placeholder="e.g. Annual revision, Promotion, Type switch" 
                                class="block w-full py-1.5 px-3 text-xs text-gray-900 border border-amber-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Statutory & Policy Entitlements -->
            <div class="space-y-4 pt-4 border-t border-gray-100">
                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider pb-1 border-b border-gray-100">
                    Statutory & Policy Entitlements
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="epf_number" class="block text-xs font-semibold text-gray-700 mb-1">
                            EPF Membership Number
                        </label>
                        <input 
                            type="text" 
                            name="epf_number" 
                            id="epf_number" 
                            value="{{ old('epf_number', $employee->epf_number) }}" 
                            placeholder="e.g. EPF-1045" 
                            class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                        >
                    </div>

                    <!-- Policy Checkboxes -->
                    <div class="space-y-2.5 pt-1">
                        <label class="flex items-center space-x-2 text-xs font-semibold text-gray-700 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="is_epf_eligible" 
                                value="1" 
                                {{ old('is_epf_eligible', $employee->is_epf_eligible ?? true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849]"
                            >
                            <span>Eligible for EPF (8% Employee / 12% Employer)</span>
                        </label>

                        <label class="flex items-center space-x-2 text-xs font-semibold text-gray-700 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="is_etf_eligible" 
                                value="1" 
                                {{ old('is_etf_eligible', $employee->is_etf_eligible ?? true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849]"
                            >
                            <span>Eligible for ETF (3% Employer Contribution)</span>
                        </label>

                        <label class="flex items-center space-x-2 text-xs font-semibold text-gray-700 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="eligible_for_off_days" 
                                value="1" 
                                {{ old('eligible_for_off_days', $employee->eligible_for_off_days ?? true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849]"
                            >
                            <span>Eligible for Company 5-Off-Day Policy per month</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Section 4: Payment Method & Banking -->
            <div class="space-y-4 pt-4 border-t border-gray-100">
                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider pb-1 border-b border-gray-100">
                    Payment Method & Banking
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="payment_method" class="block text-xs font-semibold text-gray-700 mb-1">
                            Salary Payment Method <span class="text-rose-500">*</span>
                        </label>
                        <select 
                            name="payment_method" 
                            id="payment_method" 
                            x-model="paymentMethod"
                            class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                        >
                            <option value="cash">Cash Payment</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="bank_name" class="block text-xs font-semibold text-gray-700 mb-1">
                            Bank Name
                        </label>
                        <input 
                            type="text" 
                            name="bank_name" 
                            id="bank_name" 
                            value="{{ old('bank_name', $employee->bank_name) }}" 
                            placeholder="e.g. Commercial Bank, BOC, Sampath" 
                            class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                        >
                    </div>

                    <div>
                        <label for="bank_branch" class="block text-xs font-semibold text-gray-700 mb-1">
                            Bank Branch
                        </label>
                        <input 
                            type="text" 
                            name="bank_branch" 
                            id="bank_branch" 
                            value="{{ old('bank_branch', $employee->bank_branch) }}" 
                            placeholder="e.g. Negombo Branch" 
                            class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                        >
                    </div>

                    <div>
                        <label for="bank_account_number" class="block text-xs font-semibold text-gray-700 mb-1">
                            Account Number
                        </label>
                        <input 
                            type="text" 
                            name="bank_account_number" 
                            id="bank_account_number" 
                            value="{{ old('bank_account_number', $employee->bank_account_number) }}" 
                            placeholder="e.g. 8001234567" 
                            class="block w-full py-2 px-3 text-sm text-gray-900 font-mono border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                        >
                    </div>

                    <div class="sm:col-span-2">
                        <label for="bank_account_name" class="block text-xs font-semibold text-gray-700 mb-1">
                            Account Holder Name
                        </label>
                        <input 
                            type="text" 
                            name="bank_account_name" 
                            id="bank_account_name" 
                            value="{{ old('bank_account_name', $employee->bank_account_name) }}" 
                            placeholder="e.g. N. K. Perera" 
                            class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                        >
                    </div>
                </div>
            </div>

            <!-- Section 5: System User Link & Notes -->
            <div class="space-y-4 pt-4 border-t border-gray-100">
                <div>
                    <label for="user_id" class="block text-xs font-semibold text-gray-700 mb-1">
                        Linked System Login Account
                    </label>
                    <select 
                        name="user_id" 
                        id="user_id" 
                        class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                        <option value="">-- No Linked Login Account --</option>
                        @if($employee->user)
                            <option value="{{ $employee->user_id }}" selected>
                                {{ $employee->user->name }} ({{ $employee->user->email }}) - Current Link
                            </option>
                        @endif
                        @foreach($availableUsers as $u)
                            <option value="{{ $u->id }}" {{ old('user_id', $employee->user_id) == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->email }}) - {{ ucfirst($u->role) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-semibold text-gray-700 mb-1">
                        Notes
                    </label>
                    <textarea 
                        name="notes" 
                        id="notes" 
                        rows="2" 
                        class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                    >{{ old('notes', $employee->notes) }}</textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <a 
                    href="{{ route('payroll.employees.show', $employee) }}" 
                    class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                >
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-5 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white text-sm font-semibold rounded-lg shadow-xs transition"
                >
                    Update Employee
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
