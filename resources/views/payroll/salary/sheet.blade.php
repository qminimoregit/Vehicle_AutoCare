@extends('layouts.app')

@section('title', 'Salary Sheet - ' . $payrollItem->employee_name_snapshot . ' (' . $payrollItem->period->formatted_period . ')')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ showPaymentModal: false }">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-xl font-bold text-gray-900 tracking-tight">Monthly Salary Sheet</h1>
                @if($payrollItem->period->isDraft())
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                        DRAFT
                    </span>
                @elseif($payrollItem->period->isReviewed())
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-300">
                        REVIEWED
                    </span>
                @elseif($payrollItem->status === 'paid')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        FULLY PAID
                    </span>
                @elseif($payrollItem->status === 'partially_paid')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-300">
                        PARTIALLY PAID
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-300">
                        APPROVED
                    </span>
                @endif
            </div>
            <p class="text-xs text-gray-500 font-mono mt-0.5">
                Period: {{ $payrollItem->period->formatted_period }} &bull; Employee: {{ $payrollItem->employee_name_snapshot }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a 
                href="{{ route('payroll.salary.print', $payrollItem) }}" 
                target="_blank" 
                class="inline-flex items-center px-3.5 py-2 text-xs font-bold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-xs transition"
            >
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Payslip
            </a>

            @if($payrollItem->period->isApproved() && $payrollItem->balance_due > 0 && Auth::user()->isAdmin())
                <button 
                    type="button" 
                    @click="showPaymentModal = true"
                    class="inline-flex items-center px-3.5 py-2 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Record Payment
                </button>
            @endif

            <a 
                href="{{ route('payroll.salary.index', ['year' => $payrollItem->period->year, 'month' => $payrollItem->period->month]) }}" 
                class="inline-flex items-center px-3.5 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-lg shadow-xs transition"
            >
                &larr; Back to Report
            </a>
        </div>
    </div>

    @if($payrollItem->period->isDraft())
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 flex items-center text-xs text-amber-800">
            <svg class="w-4 h-4 mr-2 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span><strong>DRAFT NOTICE:</strong> This salary sheet is in draft review mode. You can adjust overtime, bonuses, or deductions on the monthly report page before final approval.</span>
        </div>
    @endif

    <!-- Main Salary Sheet Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
        <!-- Header Banner -->
        <div class="bg-slate-50 border-b border-gray-100 p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ $company->company_name }}</h2>
                <p class="text-xs text-gray-500">{{ $company->address }} &bull; {{ $company->phone }}</p>
            </div>
            <div class="sm:text-right">
                <span class="text-xs text-gray-400 font-mono block">SALARY MONTH</span>
                <span class="text-base font-bold text-[#0B1849] font-mono">{{ $payrollItem->period->formatted_period }}</span>
            </div>
        </div>

        <!-- Employee Info Summary Grid -->
        <div class="p-6 border-b border-gray-100 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-gray-400 block uppercase tracking-wider font-semibold">Employee Name</span>
                <span class="font-bold text-gray-900 text-sm mt-0.5 block">{{ $payrollItem->employee_name_snapshot }}</span>
            </div>
            <div>
                <span class="text-gray-400 block uppercase tracking-wider font-semibold">Employee ID</span>
                <span class="font-bold text-gray-900 text-sm mt-0.5 font-mono block">{{ $payrollItem->employee->employee_number }}</span>
            </div>
            <div>
                <span class="text-gray-400 block uppercase tracking-wider font-semibold">Designation / Role</span>
                <span class="font-medium text-gray-900 text-sm mt-0.5 block">{{ $payrollItem->employee->position ?: 'Staff' }}</span>
            </div>
            <div>
                <span class="text-gray-400 block uppercase tracking-wider font-semibold">
                    {{ $payrollItem->isMonthly() ? 'Monthly Basic Salary' : 'Snapshot Daily Wage' }}
                </span>
                <span class="font-bold text-gray-900 text-sm mt-0.5 font-mono block">
                    @if($payrollItem->isMonthly())
                        LKR {{ number_format($payrollItem->monthly_basic_snapshot, 2) }}
                        <span class="text-[10px] text-purple-700 font-semibold block">(Monthly Salary)</span>
                    @else
                        LKR {{ number_format($payrollItem->daily_wage_snapshot, 2) }}
                        <span class="text-[10px] text-slate-500 font-semibold block">(Daily Wage)</span>
                    @endif
                </span>
            </div>
        </div>

        <!-- Attendance Breakdown Summary -->
        <div class="p-6 border-b border-gray-100 space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Reconciled Monthly Attendance Breakdown</h3>
            <div class="grid grid-cols-2 sm:grid-cols-7 gap-2.5 text-center">
                <div class="bg-emerald-50/50 border border-emerald-200 rounded-lg p-2.5">
                    <span class="text-[10px] text-emerald-800 block font-semibold uppercase">Full Days</span>
                    <span class="text-lg font-bold text-emerald-800 font-mono">{{ $payrollItem->full_days_count }}</span>
                </div>
                <div class="bg-blue-50/50 border border-blue-200 rounded-lg p-2.5">
                    <span class="text-[10px] text-blue-800 block font-semibold uppercase">Half Days</span>
                    <span class="text-lg font-bold text-blue-800 font-mono">{{ $payrollItem->half_days_count }}</span>
                </div>
                <div class="bg-purple-50/50 border border-purple-200 rounded-lg p-2.5">
                    <span class="text-[10px] text-purple-800 block font-semibold uppercase">Company Off-Days</span>
                    <span class="text-lg font-bold text-purple-800 font-mono">{{ number_format($payrollItem->company_off_days_count, 1) }}</span>
                    <span class="text-[9px] text-purple-600 block">Max 5 / mo</span>
                </div>
                <div class="bg-indigo-50/50 border border-indigo-200 rounded-lg p-2.5">
                    <span class="text-[10px] text-indigo-800 block font-semibold uppercase">Paid Holidays</span>
                    <span class="text-lg font-bold text-indigo-800 font-mono">{{ number_format($payrollItem->paid_holidays_count, 1) }}</span>
                    <span class="text-[9px] text-indigo-600 block">Protected</span>
                </div>
                <div class="bg-amber-50/50 border border-amber-200 rounded-lg p-2.5">
                    <span class="text-[10px] text-amber-800 block font-semibold uppercase">Other Leave</span>
                    <span class="text-lg font-bold text-amber-800 font-mono">{{ number_format($payrollItem->approved_leave_count, 1) }}</span>
                    <span class="text-[9px] text-amber-600 block">Approved</span>
                </div>
                <div class="{{ $payrollItem->no_pay_days_count > 0 ? 'bg-rose-50 border-rose-300' : 'bg-gray-50/50 border-gray-200' }} border rounded-lg p-2.5">
                    <span class="text-[10px] {{ $payrollItem->no_pay_days_count > 0 ? 'text-rose-800 font-bold' : 'text-gray-500' }} block uppercase">No-Pay Days</span>
                    <span class="text-lg font-bold {{ $payrollItem->no_pay_days_count > 0 ? 'text-rose-800' : 'text-gray-700' }} font-mono">{{ number_format($payrollItem->no_pay_days_count, 1) }}</span>
                    <span class="text-[9px] {{ $payrollItem->no_pay_days_count > 0 ? 'text-rose-600 font-semibold' : 'text-gray-400' }} block">Excess Absence</span>
                </div>
                <div class="bg-[#EEF1FA] border border-[#0B1849]/20 rounded-lg p-2.5">
                    <span class="text-[10px] text-[#0B1849] block font-bold uppercase">Payable Days</span>
                    <span class="text-lg font-extrabold text-[#0B1849] font-mono">{{ number_format($payrollItem->payable_days, 1) }}</span>
                </div>
            </div>
        </div>

        <!-- Earnings & Deductions Tables -->
        <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100">
            <!-- Left: Earnings -->
            <div class="p-6 space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-700">Earnings</h3>
                <div class="space-y-2 text-sm">
                    @if($payrollItem->isMonthly())
                        <div class="flex justify-between py-1 border-b border-gray-50">
                            <div>
                                <span class="text-gray-800 font-medium block">Monthly Basic Salary</span>
                                <span class="text-[11px] text-gray-400">Fixed monthly wage entitlement</span>
                            </div>
                            <span class="font-mono font-bold text-gray-900">LKR {{ number_format($payrollItem->base_earnings, 2) }}</span>
                        </div>
                    @else
                        @php
                            $paidHolidayWage = (float) ($payrollItem->reconciliation_summary['paid_holiday_wage'] ?? 0);
                            $attendanceWage = max(0, (float) $payrollItem->base_earnings - $paidHolidayWage);
                        @endphp
                        <div class="flex justify-between py-1 border-b border-gray-50">
                            <div>
                                <span class="text-gray-800 font-medium block">Attendance Daily Wages</span>
                                <span class="text-[11px] text-gray-400">{{ $payrollItem->full_days_count }} full + {{ $payrollItem->half_days_count }} half days worked</span>
                            </div>
                            <span class="font-mono font-semibold text-gray-900">LKR {{ number_format($attendanceWage, 2) }}</span>
                        </div>
                        @if($paidHolidayWage > 0)
                            <div class="flex justify-between py-1 border-b border-gray-50">
                                <div>
                                    <span class="text-indigo-800 font-medium block">Paid Company Holidays</span>
                                    <span class="text-[11px] text-indigo-500">{{ number_format($payrollItem->paid_holidays_count, 1) }} day(s) credited</span>
                                </div>
                                <span class="font-mono font-semibold text-indigo-900">LKR {{ number_format($paidHolidayWage, 2) }}</span>
                            </div>
                        @endif
                    @endif

                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-600">Overtime Pay</span>
                        <span class="font-mono text-gray-900">LKR {{ number_format($payrollItem->overtime_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-600">Bonuses</span>
                        <span class="font-mono text-gray-900">LKR {{ number_format($payrollItem->bonus_amount, 2) }}</span>
                    </div>
                    @if($payrollItem->allowances_amount > 0)
                        <div class="flex justify-between py-1 border-b border-gray-50">
                            <span class="text-gray-600">Allowances (Transport, Meal, etc.)</span>
                            <span class="font-mono text-gray-900">LKR {{ number_format($payrollItem->allowances_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between pt-2 border-t border-gray-200 text-base font-bold">
                        <span class="text-gray-900">Total Gross Salary</span>
                        <span class="font-mono text-[#0B1849]">LKR {{ number_format($payrollItem->gross_salary, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Right: Deductions -->
            <div class="p-6 space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-rose-700">Deductions</h3>
                <div class="space-y-2 text-sm">
                    @if($payrollItem->no_pay_deduction > 0)
                        <div class="flex justify-between py-1 border-b border-gray-50 bg-rose-50/50 px-2 rounded">
                            <div>
                                <span class="text-rose-800 font-bold block">No-Pay Deduction</span>
                                <span class="text-[11px] text-rose-600">{{ number_format($payrollItem->no_pay_days_count, 1) }} excess absence day(s) beyond 5 off-days</span>
                            </div>
                            <span class="font-mono font-bold text-rose-700">- LKR {{ number_format($payrollItem->no_pay_deduction, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-600">Salary Advances Deducted</span>
                        <span class="font-mono font-semibold text-amber-700">
                            {{ $payrollItem->advances_applied > 0 ? '- LKR ' . number_format($payrollItem->advances_applied, 2) : 'LKR 0.00' }}
                        </span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-600">Other Authorized Deductions</span>
                        <span class="font-mono text-gray-900">
                            {{ $payrollItem->deductions > 0 ? '- LKR ' . number_format($payrollItem->deductions, 2) : 'LKR 0.00' }}
                        </span>
                    </div>
                    @if($payrollItem->epf_employee_amount > 0)
                        <div class="flex justify-between py-1 border-b border-gray-50">
                            <div>
                                <span class="text-blue-800 font-medium block">EPF (Employee 8%)</span>
                                <span class="text-[11px] text-gray-400">Qualifying Base: LKR {{ number_format($payrollItem->epf_qualifying_earnings, 2) }}</span>
                            </div>
                            <span class="font-mono text-blue-900 font-semibold">- LKR {{ number_format($payrollItem->epf_employee_amount, 2) }}</span>
                        </div>
                    @endif
                    @if($payrollItem->apit_amount > 0)
                        <div class="flex justify-between py-1 border-b border-gray-50 bg-purple-50/50 px-2 rounded">
                            <div>
                                <span class="text-purple-800 font-bold block">APIT (PAYE Tax)</span>
                                <span class="text-[11px] text-purple-600">Taxable: LKR {{ number_format($payrollItem->apit_taxable_earnings, 2) }}</span>
                            </div>
                            <span class="font-mono font-bold text-purple-900">- LKR {{ number_format($payrollItem->apit_amount, 2) }}</span>
                        </div>
                    @endif
                    @php
                        $totalDeductionsCalculated = (float) $payrollItem->advances_applied + (float) $payrollItem->deductions + (float) $payrollItem->no_pay_deduction + (float) $payrollItem->epf_employee_amount + (float) $payrollItem->apit_amount;
                    @endphp
                    <div class="flex justify-between pt-2 border-t border-gray-200 text-base font-bold">
                        <span class="text-gray-900">Total Deductions</span>
                        <span class="font-mono text-rose-700">
                            - LKR {{ number_format($totalDeductionsCalculated, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statutory Contributions & Employer Liability Card -->
        <div class="p-6 bg-slate-50 border-t border-gray-100 space-y-3">
            <div class="flex items-center justify-between">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center">
                    <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Statutory Contributions & Employer Liabilities (EPF / ETF / APIT)
                </h4>
                <span class="text-[11px] text-gray-500">Employer costs do not reduce employee take-home pay</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 pt-2">
                <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-2xs">
                    <span class="text-[10px] text-gray-500 uppercase block font-semibold">EPF/ETF Base</span>
                    <span class="text-sm font-bold text-gray-900 font-mono block mt-1">LKR {{ number_format($payrollItem->epf_qualifying_earnings, 2) }}</span>
                </div>
                <div class="bg-blue-50/50 p-3 rounded-lg border border-blue-200 shadow-2xs">
                    <span class="text-[10px] text-blue-700 uppercase block font-semibold">Employee EPF (8%)</span>
                    <span class="text-sm font-bold text-blue-900 font-mono block mt-1">LKR {{ number_format($payrollItem->epf_employee_amount, 2) }}</span>
                </div>
                <div class="bg-indigo-50/50 p-3 rounded-lg border border-indigo-200 shadow-2xs">
                    <span class="text-[10px] text-indigo-700 uppercase block font-semibold">Employer EPF (12%)</span>
                    <span class="text-sm font-bold text-indigo-900 font-mono block mt-1">LKR {{ number_format($payrollItem->epf_employer_amount, 2) }}</span>
                </div>
                <div class="bg-emerald-50/50 p-3 rounded-lg border border-emerald-200 shadow-2xs">
                    <span class="text-[10px] text-emerald-700 uppercase block font-semibold">Employer ETF (3%)</span>
                    <span class="text-sm font-bold text-emerald-900 font-mono block mt-1">LKR {{ number_format($payrollItem->etf_employer_amount, 2) }}</span>
                </div>
                <div class="bg-purple-50/50 p-3 rounded-lg border border-purple-200 shadow-2xs">
                    <span class="text-[10px] text-purple-700 uppercase block font-semibold">APIT (PAYE Tax)</span>
                    <span class="text-sm font-bold text-purple-900 font-mono block mt-1">LKR {{ number_format($payrollItem->apit_amount, 2) }}</span>
                </div>
                <div class="bg-amber-50/60 p-3 rounded-lg border border-amber-200 shadow-2xs">
                    <span class="text-[10px] text-amber-800 uppercase block font-bold">Total CTC</span>
                    <span class="text-sm font-extrabold text-amber-950 font-mono block mt-1">LKR {{ number_format($payrollItem->total_cost_to_company, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Net Pay & Payment Status Highlight Bar -->
        <div class="p-6 {{ $payrollItem->net_salary < 0 ? 'bg-rose-950' : 'bg-[#0B1849]' }} text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs uppercase tracking-wider text-slate-300 block font-semibold">
                    {{ $payrollItem->net_salary < 0 ? 'Net Salary Deficit / Shortfall' : 'Net Salary Payable' }}
                </span>
                <div class="flex items-center gap-3">
                    <span class="text-2xl font-extrabold font-mono {{ $payrollItem->net_salary < 0 ? 'text-rose-300' : 'text-white' }}">
                        {{ $payrollItem->net_salary < 0 ? '- LKR '.number_format(abs($payrollItem->net_salary), 2) : 'LKR '.number_format($payrollItem->net_salary, 2) }}
                    </span>
                    @if($payrollItem->net_salary < 0)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/30 text-rose-200 border border-rose-400/40">
                            Shortfall Requires Review
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-6 text-sm font-mono">
                <div>
                    <span class="text-[11px] text-slate-300 block">Amount Paid</span>
                    <span class="font-bold text-emerald-400">LKR {{ number_format($payrollItem->paid_amount, 2) }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-slate-300 block">Outstanding Balance</span>
                    <span class="font-bold {{ $payrollItem->balance_due > 0 ? 'text-amber-300' : ($payrollItem->balance_due < 0 ? 'text-rose-300' : 'text-slate-300') }}">
                        {{ $payrollItem->balance_due < 0 ? '- LKR '.number_format(abs($payrollItem->balance_due), 2) : 'LKR '.number_format($payrollItem->balance_due, 2) }}
                    </span>
                </div>
            </div>
        </div>

        @php
            $statFlags = $payrollItem->attendance_breakdown['statutory_breakdown']['flagged_for_admin_review'] ?? [];
        @endphp
        @if(!empty($statFlags))
            <div class="p-4 bg-amber-50 border-t border-amber-200 text-xs text-amber-900">
                <div class="font-bold flex items-center mb-1">
                    <svg class="w-4 h-4 mr-1 text-amber-700" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Statutory / Payroll Compliance Review Flags:
                </div>
                <ul class="list-disc pl-5 space-y-0.5 text-amber-800 text-[11px]">
                    @foreach($statFlags as $flag)
                        <li>{{ $flag }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($payrollItem->notes)
            <div class="p-4 bg-gray-50 border-t border-gray-100 text-xs text-gray-600">
                <strong>Notes / Remarks:</strong> {{ $payrollItem->notes }}
            </div>
        @endif
    </div>

    <!-- Deducted Advances Breakdown Card -->
    @if($advances && $advances->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">Salary Advances Deducted in this Payroll</h3>
                <span class="text-xs text-gray-500 font-mono">Total: LKR {{ number_format($advances->sum('amount'), 2) }}</span>
            </div>
            <div class="responsive-table-wrapper">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Advance #</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Disbursement Date</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Method</th>
                            <th class="px-4 py-2.5 text-right font-semibold text-gray-600">Amount</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($advances as $adv)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2.5 font-mono font-semibold text-gray-900">{{ $adv->advance_number }}</td>
                                <td class="px-4 py-2.5 text-gray-600">{{ $adv->advance_date->format('d M Y') }}</td>
                                <td class="px-4 py-2.5 text-gray-600 capitalize">{{ str_replace('_', ' ', $adv->payment_method) }}</td>
                                <td class="px-4 py-2.5 text-right font-mono font-bold text-gray-900">LKR {{ number_format($adv->amount, 2) }}</td>
                                <td class="px-4 py-2.5 text-gray-500">{{ $adv->notes ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Salary Payments History Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Payment History</h3>
            @if(!$payrollItem->period->isDraft() && $payrollItem->balance_due > 0 && Auth::user()->isAdmin())
                <button 
                    type="button" 
                    @click="showPaymentModal = true"
                    class="inline-flex items-center px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Record Payment
                </button>
            @endif
        </div>
        @if($payrollItem->payments->count() > 0)
            <div class="responsive-table-wrapper">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Payment #</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Date</th>
                            <th class="px-4 py-2.5 text-right font-semibold text-gray-600">Amount</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Method</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Reference</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Recorded By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($payrollItem->payments as $pay)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2.5 font-mono font-semibold text-gray-900">{{ $pay->payment_number }}</td>
                                <td class="px-4 py-2.5 text-gray-600">{{ $pay->payment_date->format('d M Y') }}</td>
                                <td class="px-4 py-2.5 text-right font-mono font-bold text-emerald-700">LKR {{ number_format($pay->amount, 2) }}</td>
                                <td class="px-4 py-2.5 text-gray-600 capitalize">{{ str_replace('_', ' ', $pay->payment_method) }}</td>
                                <td class="px-4 py-2.5 text-gray-500 font-mono">{{ $pay->reference_number ?: '—' }}</td>
                                <td class="px-4 py-2.5 text-gray-600">{{ $pay->recorder ? $pay->recorder->name : 'System' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6 text-center text-gray-400 text-xs">
                No salary payments recorded yet for this salary sheet.
            </div>
        @endif
    </div>

    <!-- Daily Attendance Detailed Audit Log -->
    @php
        $auditRecords = $payrollItem->getDailyAuditRecords();
    @endphp
    @if(!empty($auditRecords))
        <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden" x-data="{ openBreakdown: false }">
            <div 
                @click="openBreakdown = !openBreakdown" 
                class="p-4 border-b border-gray-100 flex items-center justify-between cursor-pointer hover:bg-gray-50 transition"
            >
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Daily Attendance & Reconciliation Audit Log ({{ count($auditRecords) }} days reconciled)</h3>
                    <p class="text-xs text-gray-500">Day-by-day record of working days, statutory rest days, company holidays, off-days, and absences</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-gray-500" x-text="openBreakdown ? 'Collapse Log' : 'Expand Log'"></span>
                    <svg :class="openBreakdown ? 'rotate-180' : ''" class="w-4 h-4 text-gray-400 transform transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <div x-show="openBreakdown" x-cloak class="responsive-table-wrapper">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Date</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Classification</th>
                            <th class="px-4 py-2.5 text-center font-semibold text-gray-600">Payable Day</th>
                            <th class="px-4 py-2.5 text-right font-semibold text-gray-600">Daily Wage</th>
                            <th class="px-4 py-2.5 text-right font-semibold text-gray-600">Calculated Earnings</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600">Holiday / Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($auditRecords as $row)
                            @php
                                $statusKey = $row['status'] ?? '';
                                $rowBadgeClass = match($statusKey) {
                                    'full_day' => 'bg-emerald-50 text-emerald-800 border border-emerald-200',
                                    'half_day' => 'bg-blue-50 text-blue-800 border border-blue-200',
                                    'paid_holiday' => 'bg-indigo-50 text-indigo-800 border border-indigo-200',
                                    'unpaid_holiday' => 'bg-purple-50 text-purple-800 border border-purple-200',
                                    'day_off' => 'bg-purple-50 text-purple-700 border border-purple-200',
                                    'sunday_rest' => 'bg-gray-100 text-gray-600',
                                    'absent' => 'bg-rose-50 text-rose-700 border border-rose-200',
                                    'missing_attendance' => 'bg-amber-50 text-amber-800 border border-amber-200',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2 font-mono text-gray-900 whitespace-nowrap">
                                    {{ $row['date'] }} <span class="text-gray-400 font-sans">({{ $row['day_name'] ?? '' }})</span>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $rowBadgeClass }}">
                                        {{ $row['category'] ?? ucfirst(str_replace('_', ' ', $statusKey)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-center font-mono">{{ number_format($row['fraction'] ?? 0, 1) }}</td>
                                <td class="px-4 py-2 text-right font-mono text-gray-600">LKR {{ number_format($row['wage'] ?? 0, 2) }}</td>
                                <td class="px-4 py-2 text-right font-mono font-bold text-gray-900">LKR {{ number_format($row['earnings'] ?? 0, 2) }}</td>
                                <td class="px-4 py-2 text-gray-500">
                                    @if(!empty($row['holiday_name']))
                                        <span class="font-semibold text-indigo-700">[{{ $row['holiday_name'] }}]</span>
                                    @endif
                                    {{ $row['notes'] ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Record Salary Payment Modal -->
    <div 
        x-show="showPaymentModal" 
        x-cloak 
        @keydown.escape.window="showPaymentModal = false"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
        role="dialog"
        aria-modal="true"
        aria-labelledby="payment-modal-title"
    >
        <!-- 1. Backdrop Overlay (Dimming and blur applied strictly behind the modal) -->
        <div 
            x-show="showPaymentModal"
            @click="showPaymentModal = false"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"
            aria-hidden="true"
        ></div>

        <!-- 2. Centering Wrapper (pointer-events-none lets outside clicks pass to backdrop) -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 pointer-events-none">
            <!-- 3. Modal Dialog Panel (Sharp, elevated with relative z-10 and pointer-events-auto) -->
            <div 
                x-show="showPaymentModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                @click.stop
                class="relative z-10 pointer-events-auto w-full max-w-[calc(100vw-2rem)] sm:max-w-md max-h-[88vh] overflow-y-auto p-4 sm:p-6 my-8 text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl border border-gray-100"
            >
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                    <h3 class="text-base font-bold text-gray-900" id="payment-modal-title">Record Salary Payment</h3>
                    <button type="button" @click="showPaymentModal = false" class="text-gray-400 hover:text-gray-600 transition cursor-pointer p-1 rounded-lg" aria-label="Close dialog">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('payroll.salary.payments.store', $payrollItem) }}" class="space-y-4">
                    @csrf

                    <div class="p-3 bg-gray-50 rounded-lg text-xs space-y-1">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Employee:</span>
                            <span class="font-bold text-gray-900">{{ $payrollItem->employee_name_snapshot }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Net:</span>
                            <span class="font-mono font-bold text-gray-900">LKR {{ number_format($payrollItem->net_salary, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-amber-700 font-bold">
                            <span>Balance Due:</span>
                            <span class="font-mono">LKR {{ number_format($payrollItem->balance_due, 2) }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Payment Amount (LKR) *</label>
                        <input 
                            type="number" 
                            name="amount" 
                            step="0.01" 
                            min="0.01" 
                            max="{{ $payrollItem->balance_due }}" 
                            value="{{ $payrollItem->balance_due }}" 
                            required 
                            class="block w-full py-2 px-3 text-sm font-mono font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Payment Date *</label>
                        <input type="date" name="payment_date" value="{{ today()->toDateString() }}" required class="block w-full py-2 px-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Payment Method *</label>
                        <select name="payment_method" class="block w-full py-2 px-3 text-sm border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]">
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Reference / Cheque #</label>
                        <input type="text" name="reference_number" placeholder="Optional transaction ref" class="block w-full py-2 px-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" placeholder="Optional payment remarks..." class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"></textarea>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="showPaymentModal = false" class="px-3 py-1.5 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-xs transition cursor-pointer">
                            Confirm Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
