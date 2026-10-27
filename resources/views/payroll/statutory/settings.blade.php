@extends('layouts.app')

@section('title', 'Statutory Rules Configuration')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <x-page-header 
        title="Statutory Rules Configuration" 
        description="Configure effective-dated statutory contribution rates for EPF, ETF, APIT, and No-Pay calculation policies."
    >
        <x-slot:actions>
            <a 
                href="{{ route('payroll.statutory.index') }}" 
                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg shadow-xs transition"
            >
                <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Statutory
            </a>
        </x-slot:actions>
    </x-page-header>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-xs font-semibold">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Configuration Form (Admin Only) -->
        <div class="lg:col-span-1 bg-white p-6 rounded-xl border border-gray-200 shadow-xs">
            <h3 class="text-sm font-bold text-gray-900 border-b border-gray-100 pb-3 mb-4">
                Set Effective Statutory Rule
            </h3>

            <form method="POST" action="{{ route('payroll.statutory.settings.update') }}" class="space-y-4">
                @csrf

                <!-- Effective From -->
                <div>
                    <label for="effective_from" class="block text-xs font-semibold text-gray-700 mb-1">Effective From Date *</label>
                    <input 
                        type="date" 
                        name="effective_from" 
                        id="effective_from" 
                        value="{{ old('effective_from', today()->toDateString()) }}" 
                        required 
                        class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"
                    >
                </div>

                <!-- Effective To -->
                <div>
                    <label for="effective_to" class="block text-xs font-semibold text-gray-700 mb-1">Effective To Date (Optional)</label>
                    <input 
                        type="date" 
                        name="effective_to" 
                        id="effective_to" 
                        value="{{ old('effective_to') }}" 
                        class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"
                    >
                    <p class="text-[10px] text-gray-400 mt-0.5">Leave blank for ongoing indefinite rule.</p>
                </div>

                <!-- EPF Rates -->
                <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-100">
                    <div>
                        <label for="epf_employee_rate" class="block text-xs font-semibold text-gray-700 mb-1">Member EPF (%)</label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="epf_employee_rate" 
                            id="epf_employee_rate" 
                            value="{{ old('epf_employee_rate', $currentSetting->epf_employee_rate ?? 8.00) }}" 
                            required 
                            class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"
                        >
                    </div>

                    <div>
                        <label for="epf_employer_rate" class="block text-xs font-semibold text-gray-700 mb-1">Employer EPF (%)</label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="epf_employer_rate" 
                            id="epf_employer_rate" 
                            value="{{ old('epf_employer_rate', $currentSetting->epf_employer_rate ?? 12.00) }}" 
                            required 
                            class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"
                        >
                    </div>
                </div>

                <!-- ETF Rate -->
                <div>
                    <label for="etf_employer_rate" class="block text-xs font-semibold text-gray-700 mb-1">Employer ETF Rate (%)</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="etf_employer_rate" 
                        id="etf_employer_rate" 
                        value="{{ old('etf_employer_rate', $currentSetting->etf_employer_rate ?? 3.00) }}" 
                        required 
                        class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"
                    >
                </div>

                <!-- No-Pay Divisor Policy -->
                <div class="pt-2 border-t border-gray-100">
                    <label for="no_pay_divisor_type" class="block text-xs font-semibold text-gray-700 mb-1">No-Pay Deduction Divisor</label>
                    <select 
                        name="no_pay_divisor_type" 
                        id="no_pay_divisor_type" 
                        class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white"
                    >
                        <option value="calendar_days" {{ old('no_pay_divisor_type', $currentSetting->no_pay_divisor_type) === 'calendar_days' ? 'selected' : '' }}>Actual Calendar Days (e.g., 28, 30, 31)</option>
                        <option value="fixed_30" {{ old('no_pay_divisor_type', $currentSetting->no_pay_divisor_type) === 'fixed_30' ? 'selected' : '' }}>Fixed 30 Days</option>
                        <option value="fixed_26" {{ old('no_pay_divisor_type', $currentSetting->no_pay_divisor_type) === 'fixed_26' ? 'selected' : '' }}>Fixed 26 Working Days (Shop & Office)</option>
                        <option value="fixed_22" {{ old('no_pay_divisor_type', $currentSetting->no_pay_divisor_type) === 'fixed_22' ? 'selected' : '' }}>Fixed 22 Working Days (5-Day Week)</option>
                    </select>
                </div>

                <!-- Overtime Parameters -->
                <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-100">
                    <div>
                        <label for="overtime_normal_multiplier" class="block text-xs font-semibold text-gray-700 mb-1">OT Normal Rate</label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="overtime_normal_multiplier" 
                            id="overtime_normal_multiplier" 
                            value="{{ old('overtime_normal_multiplier', $currentSetting->overtime_normal_multiplier ?? 1.50) }}" 
                            required 
                            class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"
                        >
                    </div>

                    <div>
                        <label for="overtime_holiday_multiplier" class="block text-xs font-semibold text-gray-700 mb-1">OT Holiday Rate</label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="overtime_holiday_multiplier" 
                            id="overtime_holiday_multiplier" 
                            value="{{ old('overtime_holiday_multiplier', $currentSetting->overtime_holiday_multiplier ?? 2.00) }}" 
                            required 
                            class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"
                        >
                    </div>
                </div>

                <div>
                    <label for="overtime_monthly_divisor_hours" class="block text-xs font-semibold text-gray-700 mb-1">OT Monthly Divisor (Hours)</label>
                    <input 
                        type="number" 
                        step="0.5" 
                        name="overtime_monthly_divisor_hours" 
                        id="overtime_monthly_divisor_hours" 
                        value="{{ old('overtime_monthly_divisor_hours', $currentSetting->overtime_monthly_divisor_hours ?? 200.00) }}" 
                        required 
                        class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"
                    >
                    <p class="text-[10px] text-gray-400 mt-0.5">Standard 200 hours divisor for monthly salary hourly wage.</p>
                </div>

                <!-- APIT Configuration -->
                <div class="pt-2 border-t border-gray-100 space-y-3">
                    <div class="flex items-center">
                        <input 
                            type="hidden" 
                            name="apit_enabled" 
                            value="0"
                        >
                        <input 
                            type="checkbox" 
                            name="apit_enabled" 
                            id="apit_enabled" 
                            value="1" 
                            {{ old('apit_enabled', $currentSetting->apit_enabled) ? 'checked' : '' }} 
                            class="h-4 w-4 text-[#0B1849] rounded border-gray-300 focus:ring-[#0B1849]"
                        >
                        <label for="apit_enabled" class="ml-2 text-xs font-semibold text-gray-700">Enable APIT (PAYE) Tax Deductions</label>
                    </div>

                    <div>
                        <label for="apit_monthly_relief" class="block text-xs font-semibold text-gray-700 mb-1">APIT Monthly Relief (LKR)</label>
                        <input 
                            type="number" 
                            step="1000" 
                            name="apit_monthly_relief" 
                            id="apit_monthly_relief" 
                            value="{{ old('apit_monthly_relief', $currentSetting->apit_monthly_relief ?? 150000.00) }}" 
                            required 
                            class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"
                        >
                        <p class="text-[10px] text-gray-400 mt-0.5">Sri Lanka IRD resident tax-free threshold: LKR 150,000 / month (effective April 1, 2025; previously LKR 100,000 / month).</p>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-xs font-semibold text-gray-700 mb-1">Administrative Notes / Circular Ref</label>
                    <textarea 
                        name="notes" 
                        id="notes" 
                        rows="2" 
                        class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]" 
                        placeholder="e.g., Gazette / IRD Circular No. SEC/2023/01"
                    >{{ old('notes', $currentSetting->notes) }}</textarea>
                </div>

                <button 
                    type="submit" 
                    class="w-full py-2.5 px-4 text-xs font-semibold text-white bg-[#0B1849] hover:bg-[#0B1849]/90 rounded-lg transition shadow-xs"
                >
                    Save Statutory Configuration
                </button>
            </form>
        </div>

        <!-- Effective Rules History Table -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-gray-200 shadow-xs">
            <h3 class="text-sm font-bold text-gray-900 border-b border-gray-100 pb-3 mb-4">
                Statutory Configuration History
            </h3>

            @if($settings->isEmpty())
                <div class="p-6 text-center text-xs text-gray-500 bg-gray-50 rounded-xl">
                    <p>No custom statutory configurations saved yet.</p>
                    <p class="mt-1 text-gray-400">System is operating on default Sri Lanka statutory reference values (EPF 8%/12%, ETF 3%, APIT Relief LKR 150,000 for April 2025+ / LKR 100,000 prior).</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase tracking-wider text-[10px]">
                                <th class="py-2.5 px-3">Effective Period</th>
                                <th class="py-2.5 px-3">EPF Rates</th>
                                <th class="py-2.5 px-3">ETF Rate</th>
                                <th class="py-2.5 px-3">No-Pay Policy</th>
                                <th class="py-2.5 px-3">APIT Relief</th>
                                <th class="py-2.5 px-3">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($settings as $row)
                                <tr class="hover:bg-gray-50/60 transition">
                                    <td class="py-3 px-3">
                                        <div class="font-semibold text-gray-900">{{ $row->effective_from->format('Y-m-d') }}</div>
                                        <div class="text-[10px] text-gray-500">
                                            {{ $row->effective_to ? 'to '.$row->effective_to->format('Y-m-d') : 'Ongoing' }}
                                        </div>
                                    </td>
                                    <td class="py-3 px-3 font-medium">
                                        Emp: {{ number_format($row->epf_employee_rate, 1) }}%<br>
                                        Co: {{ number_format($row->epf_employer_rate, 1) }}%
                                    </td>
                                    <td class="py-3 px-3 font-medium">
                                        {{ number_format($row->etf_employer_rate, 1) }}%
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-800">
                                            {{ str_replace('_', ' ', $row->no_pay_divisor_type) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3">
                                        {{ $row->apit_enabled ? 'LKR '.number_format($row->apit_monthly_relief, 0) : 'Disabled' }}
                                    </td>
                                    <td class="py-3 px-3 text-gray-500 max-w-xs truncate">
                                        {{ $row->notes ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Sri Lanka Statutory Information Box -->
            <div class="mt-6 p-4 bg-blue-50/60 border border-blue-200 rounded-xl text-xs text-blue-900 space-y-2">
                <div class="font-bold flex items-center">
                    <svg class="w-4 h-4 mr-1.5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Sri Lanka Statutory & Labor Law Reference Summary
                </div>
                <ul class="list-disc pl-5 space-y-1 text-blue-800/90 text-[11px]">
                    <li><strong>Employees' Provident Fund (EPF Act No. 15 of 1958):</strong> Minimum legal employee contribution is 8% of total qualifying earnings; employer contribution is 12%. Overtime and travel reimbursements are strictly excluded from EPF/ETF.</li>
                    <li><strong>Employees' Trust Fund (ETF Act No. 46 of 1980):</strong> Employer contributes 3% of total qualifying earnings. Under no circumstances may employer ETF or EPF be deducted from employee salary.</li>
                    <li><strong>Advance Personal Income Tax (APIT / Inland Revenue Act No. 24 of 2017 & 2025 Amendments):</strong> Table 01 applies to resident employees furnishing primary employment declarations (TD 1). Effective April 1, 2025 (YA 2025/26), the monthly tax-free personal relief is LKR 150,000 (annual LKR 1,800,000), followed by progressive slabs (first LKR 83,333.33 @ 6%, next LKR 41,666.67 @ 18%, next LKR 41,666.67 @ 24%, next LKR 41,666.67 @ 30%, and balance @ 36%). Prior to April 1, 2025, relief was LKR 100,000/month with 6 slabs of LKR 41,666.67 (6% to 36%).</li>
                    <li><strong>Mandatory Coverage & Shortfalls:</strong> Private sector employees under contract of employment are mandatorily covered by EPF/ETF. Mandatory statutory liabilities must not be artificially capped to force nonnegative net pay; shortfalls must be flagged for payroll review.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
