@extends('layouts.app')

@section('title', 'Edit Employee - ' . $employee->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">Edit Employee</h1>
            <p class="text-xs text-gray-500 mt-0.5">Update employee details or daily wage rate.</p>
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
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-6" x-data="{ currentWage: '{{ (float) $employee->daily_wage }}', inputWage: '{{ old('daily_wage', (float) $employee->daily_wage) }}' }">
        <form method="POST" action="{{ route('payroll.employees.update', $employee) }}" class="space-y-5">
            @csrf
            @method('PUT')

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

            <!-- Daily Wage & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
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
                            x-model="inputWage"
                            required 
                            class="block w-full pl-12 pr-3 py-2 text-sm text-gray-900 font-mono font-semibold border @error('daily_wage') border-rose-300 bg-rose-50/30 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                        >
                    </div>
                    @error('daily_wage')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
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

            <!-- Conditional Wage Rate Change Notice & Effective Date -->
            <div 
                x-show="parseFloat(inputWage) !== parseFloat(currentWage)" 
                x-transition 
                class="bg-amber-50 border border-amber-200 rounded-xl p-4 space-y-3"
                style="display: none;"
            >
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-amber-600 mr-2 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h4 class="text-xs font-bold text-amber-900">Wage Rate Adjustment Detected</h4>
                        <p class="text-xs text-amber-800 mt-0.5">
                            You are changing the wage rate from <strong>LKR {{ number_format($employee->daily_wage, 2) }}</strong> to a new amount. A new wage history entry will be saved to preserve past salary and attendance calculations.
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
                            value="{{ old('wage_reason', 'Wage increment') }}" 
                            placeholder="e.g. Annual revision, Promotion" 
                            class="block w-full py-1.5 px-3 text-xs text-gray-900 border border-amber-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]"
                        >
                    </div>
                </div>
            </div>

            <!-- Mobile & Position -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                    <label for="position" class="block text-xs font-semibold text-gray-700 mb-1">
                        Employee Position / Role
                    </label>
                    <input 
                        type="text" 
                        name="position" 
                        id="position" 
                        value="{{ old('position', $employee->position) }}" 
                        placeholder="e.g. Senior Mechanic, Washer, Electrician" 
                        class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                    >
                </div>
            </div>

            <!-- Joining Date & Address -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                    <textarea 
                        name="address" 
                        id="address" 
                        rows="2" 
                        class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                    >{{ old('address', $employee->address) }}</textarea>
                </div>
            </div>

            <!-- Optional System User Link -->
            <div class="border-t border-gray-100 pt-4">
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

            <!-- Notes -->
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
