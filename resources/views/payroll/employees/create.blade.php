@extends('layouts.app')

@section('title', 'Register Employee')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">Register New Employee</h1>
            <p class="text-xs text-gray-500 mt-0.5">Add an employee profile and set their base daily wage rate.</p>
        </div>
        <a 
            href="{{ route('payroll.employees.index') }}" 
            class="inline-flex items-center text-xs font-semibold text-gray-600 hover:text-gray-900 bg-white border border-gray-200 px-3 py-1.5 rounded-lg shadow-xs transition"
        >
            <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to List
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-6">
        <form method="POST" action="{{ route('payroll.employees.store') }}" class="space-y-5">
            @csrf

            <!-- Employee ID & Name -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Employee ID
                    </label>
                    <input 
                        type="text" 
                        value="{{ $nextEmployeeNumber }}" 
                        disabled 
                        class="block w-full py-2 px-3 text-sm font-mono font-bold bg-gray-50 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed"
                    >
                    <p class="text-[11px] text-gray-400 mt-1">Generated automatically</p>
                </div>

                <div class="sm:col-span-2">
                    <label for="name" class="block text-xs font-semibold text-gray-700 mb-1">
                        Employee Name <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name') }}" 
                        required 
                        placeholder="e.g. Nilantha Kumara" 
                        class="block w-full py-2 px-3 text-sm text-gray-900 border @error('name') border-rose-300 bg-rose-50/30 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                    >
                    @error('name')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Daily Wage & Joining Date -->
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
                            value="{{ old('daily_wage') }}" 
                            required 
                            placeholder="3200.00" 
                            class="block w-full pl-12 pr-3 py-2 text-sm text-gray-900 font-mono font-semibold border @error('daily_wage') border-rose-300 bg-rose-50/30 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                        >
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1">Rate paid for 1 full working day</p>
                    @error('daily_wage')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="joining_date" class="block text-xs font-semibold text-gray-700 mb-1">
                        Joining Date
                    </label>
                    <input 
                        type="date" 
                        name="joining_date" 
                        id="joining_date" 
                        value="{{ old('joining_date', today()->toDateString()) }}" 
                        class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                    >
                    @error('joining_date')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
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
                        value="{{ old('mobile') }}" 
                        placeholder="e.g. 0771234567" 
                        class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                    >
                    @error('mobile')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="position" class="block text-xs font-semibold text-gray-700 mb-1">
                        Employee Position / Role
                    </label>
                    <input 
                        type="text" 
                        name="position" 
                        id="position" 
                        value="{{ old('position') }}" 
                        placeholder="e.g. Senior Mechanic, Washer, Electrician" 
                        class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                    >
                    @error('position')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Address -->
            <div>
                <label for="address" class="block text-xs font-semibold text-gray-700 mb-1">
                    Residential Address
                </label>
                <textarea 
                    name="address" 
                    id="address" 
                    rows="2" 
                    placeholder="Enter employee address..." 
                    class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                >{{ old('address') }}</textarea>
                @error('address')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Optional System User Link -->
            <div class="border-t border-gray-100 pt-4">
                <div class="flex items-center justify-between mb-2">
                    <label for="user_id" class="block text-xs font-semibold text-gray-700">
                        Link to System Login Account (Optional)
                    </label>
                    <span class="text-[11px] text-gray-400">Only if this employee uses the software</span>
                </div>
                <select 
                    name="user_id" 
                    id="user_id" 
                    class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                >
                    <option value="">-- No Login Account (Operational / Workshop Staff) --</option>
                    @foreach($availableUsers as $u)
                        <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->email }}) - {{ ucfirst($u->role) }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-gray-500 mt-1">
                    Employee records and system login accounts are separate. Most workshop staff do not require a software login.
                </p>
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
                    placeholder="Any additional remarks..." 
                    class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                >{{ old('notes') }}</textarea>
            </div>

            <!-- Form Actions -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <a 
                    href="{{ route('payroll.employees.index') }}" 
                    class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                >
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-5 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white text-sm font-semibold rounded-lg shadow-xs transition"
                >
                    Save Employee
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
