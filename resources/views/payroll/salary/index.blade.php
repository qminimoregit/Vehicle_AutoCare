@extends('layouts.app')

@section('title', 'Employee Salary & Payroll Approval')

@section('content')
<div class="space-y-6" x-data="{ 
    selectedItem: null, 
    showAdjustModal: false, 
    showAdvanceModal: false,
    showPaymentModal: false,
    paymentItem: null,
    showWarningModal: false
}">
    <!-- Header -->
    <x-page-header 
        title="Employee Salary & Payroll Approval" 
        description="Review monthly earnings, attendance reconciliation, statutory deductions, and manage payroll approvals."
    >
        <x-slot:actions>
            <div class="flex flex-wrap items-center gap-2">
                <a 
                    href="{{ route('payroll.statutory.index', ['year' => $year, 'month' => $month]) }}" 
                    class="inline-flex items-center px-3.5 py-2 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Statutory & Tax
                </a>

                @if(Auth::user()->isAdmin())
                    <button 
                        type="button" 
                        @click="showAdvanceModal = true"
                        class="inline-flex items-center px-3.5 py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition cursor-pointer"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Record Advance
                    </button>
                @endif
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- Filters & Actions Card -->
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs space-y-4">
        <form method="GET" action="{{ route('payroll.salary.index') }}" id="salaryFilterForm" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                <!-- 1. Year Filter -->
                <div>
                    <label for="year" class="block text-xs font-semibold text-gray-700 mb-1">Year</label>
                    <select 
                        name="year" 
                        id="year" 
                        class="w-full py-2 px-3 text-xs font-semibold text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white"
                    >
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Month Filter -->
                <div>
                    <label for="month" class="block text-xs font-semibold text-gray-700 mb-1">Month</label>
                    <select 
                        name="month" 
                        id="month" 
                        class="w-full py-2 px-3 text-xs font-semibold text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white"
                    >
                        @foreach(range(1, 12) as $m)
                            @php $mName = \Carbon\Carbon::createFromDate($year, $m, 1)->format('F'); @endphp
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $mName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Branch Filter -->
                <div>
                    <label for="branch_id" class="block text-xs font-semibold text-gray-700 mb-1">Branch</label>
                    <select 
                        name="branch_id" 
                        id="branch_id" 
                        class="w-full py-2 px-3 text-xs font-medium text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white"
                    >
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ (string)$selectedBranchId === (string)$branch->id ? 'selected' : '' }}>
                                {{ $branch->branch_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Employee Filter -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Employee</label>
                    <x-searchable-select 
                        name="employee_id" 
                        :value="$selectedEmployeeId"
                        placeholder="All Employees"
                        :endpoint="route('search.employees')"
                        :clearable="true"
                    />
                </div>

                <!-- 5. Salary Type Filter -->
                <div>
                    <label for="salary_type" class="block text-xs font-semibold text-gray-700 mb-1">Salary Type</label>
                    <select 
                        name="salary_type" 
                        id="salary_type" 
                        class="w-full py-2 px-3 text-xs font-medium text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white"
                    >
                        <option value="">All Types</option>
                        <option value="daily" {{ $selectedSalaryType === 'daily' ? 'selected' : '' }}>Daily Wage</option>
                        <option value="monthly" {{ $selectedSalaryType === 'monthly' ? 'selected' : '' }}>Monthly Salary</option>
                    </select>
                </div>

                <!-- 6. Status Filter -->
                <div>
                    <label for="status" class="block text-xs font-semibold text-gray-700 mb-1">Payroll Status</label>
                    <select 
                        name="status" 
                        id="status" 
                        class="w-full py-2 px-3 text-xs font-medium text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white"
                    >
                        <option value="">All Statuses</option>
                        <option value="draft" {{ $selectedStatus === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="reviewed" {{ $selectedStatus === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        <option value="approved" {{ $selectedStatus === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="partially_paid" {{ $selectedStatus === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                        <option value="paid" {{ $selectedStatus === 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
            </div>

            <!-- Filter Buttons & Workflow Actions Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-gray-100">
                <div class="flex items-center gap-2">
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-lg transition cursor-pointer"
                    >
                        Apply Filters
                    </button>
                    <a 
                        href="{{ route('payroll.salary.index', ['year' => $year, 'month' => $month]) }}" 
                        class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition"
                    >
                        Reset
                    </a>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @if(Auth::user()->isAdmin())
                        <!-- Recalculate / Generate Draft -->
                        @if(!$period || $period->isModifiable())
                            <button 
                                type="submit" 
                                name="generate" 
                                value="1" 
                                class="inline-flex items-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-bold rounded-lg shadow-xs transition cursor-pointer"
                            >
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                {{ $period ? 'Recalculate Draft' : 'Generate Monthly Payroll' }}
                            </button>
                        @endif

                        <!-- Step 2: Mark as Reviewed -->
                        @if($period && $period->isDraft())
                            <button 
                                type="button"
                                onclick="document.getElementById('form-mark-reviewed').submit();"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-xs transition cursor-pointer"
                                title="Verify calculations and mark as reviewed before approval"
                            >
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                Mark as Reviewed
                            </button>
                        @endif

                        <!-- Step 3: Approve Payroll -->
                        @if($period && $period->isModifiable())
                            @if($validation['can_approve'])
                                <button 
                                    type="button"
                                    onclick="if(confirm('Approve monthly payroll for {{ $period->formatted_period }}?\n\nOnce approved:\n- Salary rates and calculations will be locked.\n- Eligible salary advance recoveries will be finalized.\n- Approved bonuses and overtime will be applied to payslips.')) document.getElementById('form-approve-payroll').submit();"
                                    class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-xs transition cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Approve Payroll
                                </button>
                            @else
                                <button 
                                    type="button" 
                                    disabled
                                    class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-500 text-xs font-bold rounded-lg cursor-not-allowed opacity-75"
                                    title="Approval blocked: resolve critical deduction shortfalls or missing rates below"
                                >
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    Approval Blocked
                                </button>
                            @endif
                        @elseif($period && $period->isApproved() && Auth::user()->isAdmin())
                            <!-- Rollback Protection Button -->
                            <button 
                                type="button"
                                onclick="if(confirm('Rollback payroll for {{ $period->formatted_period }} to draft?\n\nWarning: Rollback is only permitted if NO salary payments have been recorded.\nApplied advance recoveries and bonuses will be safely reverted to outstanding state.')) document.getElementById('form-rollback-payroll').submit();"
                                class="inline-flex items-center px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-lg shadow-xs transition cursor-pointer"
                                title="Revert approved payroll to draft state"
                            >
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                                Rollback to Draft
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Hidden Action Forms -->
    @if($period && Auth::user()->isAdmin())
        <form id="form-mark-reviewed" method="POST" action="{{ route('payroll.salary.review', $period) }}" class="hidden">
            @csrf
        </form>

        <form id="form-approve-payroll" method="POST" action="{{ route('payroll.salary.approve', $period) }}" class="hidden">
            @csrf
        </form>

        <form id="form-rollback-payroll" method="POST" action="{{ route('payroll.salary.rollback', $period) }}" class="hidden">
            @csrf
        </form>
    @endif

    <!-- Workflow Progress Stepper (DRAFT -> REVIEWED -> APPROVED -> PAID) -->
    @if($period)
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Payroll Approval Workflow:</span>
                    <span class="text-xs font-bold text-gray-900 font-mono">{{ $period->formatted_period }}</span>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if(!empty($validation['warnings']) || count($unmarkedDaysWarning) > 0)
                        <button 
                            type="button" 
                            @click="showWarningModal = true"
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-900 border border-amber-300 hover:bg-amber-100 transition shadow-2xs cursor-pointer"
                            title="Click to view warning and unrecorded attendance details"
                        >
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>
                                @if(count($unmarkedDaysWarning) > 0)
                                    Attendance: {{ count($unmarkedDaysWarning) }} {{ count($unmarkedDaysWarning) === 1 ? 'date needs' : 'dates need' }} review
                                @else
                                    Payroll: {{ count($validation['warnings']) }} {{ count($validation['warnings']) === 1 ? 'warning' : 'warnings' }} to review
                                @endif
                            </span>
                            <span class="text-xs font-bold text-amber-950 underline ml-0.5">View Details</span>
                        </button>
                    @endif

                    @if($period->status === 'paid')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            &#x2713; Fully Disbursed & Paid
                        </span>
                    @elseif($period->status === 'partially_paid')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-300">
                            Partially Disbursed
                        </span>
                    @elseif($period->status === 'approved')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-300">
                            &#x1F512; Approved & Locked
                        </span>
                    @elseif($period->status === 'reviewed')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-300">
                            Reviewed & Verified
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                            Draft Calculation
                        </span>
                    @endif
                </div>
            </div>

            <!-- Stepper Steps -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                <!-- Step 1: Draft -->
                <div class="p-2.5 rounded-lg border {{ $period->isDraft() ? 'bg-amber-50/70 border-amber-300 text-amber-900' : 'bg-gray-50 border-gray-200 text-gray-600' }} flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 {{ $period->isDraft() ? 'bg-amber-500 text-white font-bold' : ($period->status !== 'draft' ? 'bg-emerald-500 text-white font-bold' : 'bg-gray-200 text-gray-600') }}">
                        @if($period->status !== 'draft') &#x2713; @else 1 @endif
                    </div>
                    <div>
                        <span class="font-bold block">1. Draft Generated</span>
                        <span class="text-[10px] text-gray-500">Attendance calculated</span>
                    </div>
                </div>

                <!-- Step 2: Reviewed -->
                <div class="p-2.5 rounded-lg border {{ $period->isReviewed() ? 'bg-blue-50/70 border-blue-300 text-blue-900' : (in_array($period->status, ['approved', 'partially_paid', 'paid']) ? 'bg-gray-50 border-gray-200 text-gray-600' : 'bg-gray-50/50 border-gray-100 text-gray-400') }} flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 {{ $period->isReviewed() ? 'bg-blue-600 text-white font-bold' : (in_array($period->status, ['approved', 'partially_paid', 'paid']) ? 'bg-emerald-500 text-white font-bold' : 'bg-gray-200 text-gray-600') }}">
                        @if(in_array($period->status, ['approved', 'partially_paid', 'paid'])) &#x2713; @else 2 @endif
                    </div>
                    <div>
                        <span class="font-bold block">2. Reviewed</span>
                        <span class="text-[10px] text-gray-500">Pre-approval verified</span>
                    </div>
                </div>

                <!-- Step 3: Approved -->
                <div class="p-2.5 rounded-lg border {{ $period->isApproved() ? 'bg-indigo-50/70 border-indigo-300 text-indigo-900' : 'bg-gray-50/50 border-gray-100 text-gray-400' }} flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 {{ $period->isApproved() ? 'bg-[#0B1849] text-white font-bold' : 'bg-gray-200 text-gray-600' }}">
                        @if($period->isPaid()) &#x2713; @else 3 @endif
                    </div>
                    <div>
                        <span class="font-bold block">3. Approved & Locked</span>
                        <span class="text-[10px] text-gray-500">
                            @if($period->approver)
                                By {{ $period->approver->name }} ({{ $period->approved_at?->format('d M') }})
                            @else
                                Admin authorized
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Step 4: Disbursed / Paid -->
                <div class="p-2.5 rounded-lg border {{ $period->isPaid() ? 'bg-emerald-50/70 border-emerald-300 text-emerald-900' : ($period->status === 'partially_paid' ? 'bg-blue-50/70 border-blue-300 text-blue-900' : 'bg-gray-50/50 border-gray-100 text-gray-400') }} flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 {{ $period->isPaid() ? 'bg-emerald-600 text-white font-bold' : ($period->status === 'partially_paid' ? 'bg-blue-600 text-white font-bold' : 'bg-gray-200 text-gray-600') }}">
                        @if($period->isPaid()) &#x2713; @else 4 @endif
                    </div>
                    <div>
                        <span class="font-bold block">4. Paid & Settled</span>
                        <span class="text-[10px] text-gray-500">
                            LKR {{ number_format($period->total_paid, 2) }} paid
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- CRITICAL PRE-APPROVAL BLOCKERS (RED BANNER) -->
    @if(!empty($validation['blockers']))
        <div class="bg-rose-50 border-l-4 border-rose-600 rounded-r-xl p-4 shadow-xs">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-rose-600 mr-3 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-rose-900">
                        Critical Pre-Approval Blockers ({{ count($validation['blockers']) }}) &mdash; Approval Disabled
                    </h4>
                    <p class="text-xs text-rose-800 mt-1">
                        The payroll cannot be approved until these critical financial errors and deduction shortfalls are resolved:
                    </p>
                    <ul class="list-disc list-inside mt-2 space-y-1 text-xs text-rose-900 font-medium">
                        @foreach($validation['blockers'] as $blocker)
                            <li>{{ $blocker }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @if($period)
        <!-- PAYROLL SUMMARY (8 Summary Cards) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
            <!-- 1. Total Employees -->
            <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-xs">
                <span class="text-[11px] font-medium text-gray-500 block">Total Staff</span>
                <span class="text-xl font-extrabold text-gray-900 block mt-1">
                    {{ $summary['total_employees'] }}
                </span>
                <span class="text-[10px] text-gray-400 block mt-0.5">Active on sheet</span>
            </div>

            <!-- 2. Total Gross Salary -->
            <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-xs">
                <span class="text-[11px] font-medium text-gray-500 block">Gross Salary</span>
                <span class="text-base font-bold text-gray-900 font-mono block mt-1">
                    LKR {{ number_format($summary['total_gross'], 2) }}
                </span>
                <span class="text-[10px] text-gray-400 block mt-0.5">Base + OT + Bonus</span>
            </div>

            <!-- 3. Total Deductions -->
            <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-xs">
                <span class="text-[11px] font-medium text-gray-500 block">Deductions</span>
                <span class="text-base font-bold text-rose-700 font-mono block mt-1">
                    LKR {{ number_format($summary['total_deductions'], 2) }}
                </span>
                <span class="text-[10px] text-gray-400 block mt-0.5">No-pay, EPF, APIT, Adv</span>
            </div>

            <!-- 4. Total Net Salary -->
            <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-xs">
                <span class="text-[11px] font-medium text-gray-500 block">Net Salary</span>
                <span class="text-base font-extrabold text-emerald-700 font-mono block mt-1">
                    LKR {{ number_format($summary['total_net'], 2) }}
                </span>
                <span class="text-[10px] text-gray-400 block mt-0.5">Payable to staff</span>
            </div>

            <!-- 5. Total Paid -->
            <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-xs">
                <span class="text-[11px] font-medium text-gray-500 block">Total Paid</span>
                <span class="text-base font-bold text-blue-700 font-mono block mt-1">
                    LKR {{ number_format($summary['total_paid'], 2) }}
                </span>
                <span class="text-[10px] text-gray-400 block mt-0.5">Disbursed to date</span>
            </div>

            <!-- 6. Outstanding Salary -->
            <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-xs">
                <span class="text-[11px] font-medium text-gray-500 block">Outstanding</span>
                <span class="text-base font-bold {{ $summary['outstanding_salary'] > 0 ? 'text-amber-700' : 'text-gray-900' }} font-mono block mt-1">
                    LKR {{ number_format($summary['outstanding_salary'], 2) }}
                </span>
                <span class="text-[10px] text-gray-400 block mt-0.5">Balance to pay</span>
            </div>

            <!-- 7. Employer EPF (12%) -->
            <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-xs">
                <span class="text-[11px] font-medium text-gray-500 block">Employer EPF</span>
                <span class="text-base font-bold text-indigo-700 font-mono block mt-1">
                    LKR {{ number_format($summary['total_epf_employer'], 2) }}
                </span>
                <span class="text-[10px] text-gray-400 block mt-0.5">12% Statutory</span>
            </div>

            <!-- 8. Employer ETF (3%) -->
            <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-xs">
                <span class="text-[11px] font-medium text-gray-500 block">Employer ETF</span>
                <span class="text-base font-bold text-purple-700 font-mono block mt-1">
                    LKR {{ number_format($summary['total_etf_employer'], 2) }}
                </span>
                <span class="text-[10px] text-gray-400 block mt-0.5">3% Statutory</span>
            </div>
        </div>

        <!-- EMPLOYEE SALARY TABLE (21 COLUMNS) - DESKTOP VIEW -->
        <div class="hidden md:block bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900">
                        Monthly Employee Salary Sheet &bull; {{ $period->formatted_period }}
                    </h3>
                    <p class="text-xs text-gray-500">
                        Showing {{ $filteredItems->count() }} employee record(s). Scroll horizontally to review detailed attendance breakdown and statutory withholdings.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-medium text-gray-400 hidden lg:inline">
                        &larr; Scroll horizontally inside table &rarr;
                    </span>
                    @if($period->isDraft())
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                            Draft Mode
                        </span>
                    @elseif($period->isReviewed())
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200">
                            Reviewed Mode
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            Approved &bull; Locked
                        </span>
                    @endif
                </div>
            </div>

            <div class="responsive-table-wrapper overflow-x-auto w-full">
                <table class="min-w-full divide-y divide-gray-200 text-xs whitespace-nowrap">
                    <thead class="bg-gray-50/90 text-gray-700 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <!-- 1. Employee -->
                            <th class="px-4 py-3 text-left sticky left-0 bg-gray-50 z-10">Employee</th>
                            <!-- 2. Salary Type -->
                            <th class="px-2.5 py-3 text-center">Type</th>
                            <!-- 3. Basic / Daily Wage -->
                            <th class="px-3 py-3 text-right">Basic / Wage</th>
                            <!-- 4. Full Days -->
                            <th class="px-2 py-3 text-center" title="Full Working Days">Full</th>
                            <!-- 5. Half Days -->
                            <th class="px-2 py-3 text-center" title="Half Working Days">Half</th>
                            <!-- 6. Company Off-Days -->
                            <th class="px-2 py-3 text-center" title="Company Off-Days Applied (Max 5)">Off-Days</th>
                            <!-- 7. Paid Holidays -->
                            <th class="px-2 py-3 text-center" title="Paid Company Holidays">Paid Hol.</th>
                            <!-- 8. No-Pay Days -->
                            <th class="px-2 py-3 text-center" title="Excess Unprotected Absence">No-Pay</th>
                            <!-- 9. No-Pay Deduction -->
                            <th class="px-3 py-3 text-right text-rose-700">No-Pay Ded.</th>
                            <!-- 10. Overtime -->
                            <th class="px-3 py-3 text-right text-blue-800">Overtime</th>
                            <!-- 11. Bonuses -->
                            <th class="px-3 py-3 text-right text-purple-800">Bonuses</th>
                            <!-- 12. Allowances -->
                            <th class="px-3 py-3 text-right text-indigo-800">Allowances</th>
                            <!-- 13. Gross Salary -->
                            <th class="px-3.5 py-3 text-right font-extrabold text-gray-900 bg-gray-100/50">Gross Salary</th>
                            <!-- 14. Employee EPF -->
                            <th class="px-3 py-3 text-right text-rose-700">EPF (8%)</th>
                            <!-- 15. APIT -->
                            <th class="px-3 py-3 text-right text-rose-700">APIT</th>
                            <!-- 16. Advance Recovery -->
                            <th class="px-3 py-3 text-right text-amber-700">Advance</th>
                            <!-- 17. Net Salary -->
                            <th class="px-4 py-3 text-right font-extrabold text-emerald-700 bg-emerald-50/40">Net Salary</th>
                            <!-- 18. Paid Amount -->
                            <th class="px-3 py-3 text-right text-blue-700">Paid</th>
                            <!-- 19. Outstanding Balance -->
                            <th class="px-3 py-3 text-right text-amber-800">Balance</th>
                            <!-- 20. Status -->
                            <th class="px-3 py-3 text-center">Status</th>
                            <!-- 21. Actions -->
                            <th class="px-4 py-3 text-right sticky right-0 bg-gray-50 z-10">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($filteredItems as $item)
                            @php
                                $isNegative = (float) $item->net_salary < 0;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition {{ $isNegative ? 'bg-rose-50/40' : '' }}">
                                <!-- 1. Employee -->
                                <td class="px-4 py-3 whitespace-nowrap sticky left-0 bg-white z-10">
                                    <div class="font-bold text-gray-900">
                                        {{ $item->employee_name_snapshot }}
                                    </div>
                                    <div class="text-[11px] text-gray-500 flex items-center gap-1.5 font-mono">
                                        <span>{{ $item->employee?->employee_number ?? "EMP-{$item->employee_id}" }}</span>
                                        @if($item->employee?->branch)
                                            <span class="text-gray-300">&bull;</span>
                                            <span>{{ $item->employee->branch->branch_name }}</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- 2. Salary Type -->
                                <td class="px-2.5 py-3 text-center whitespace-nowrap">
                                    @if($item->isMonthly())
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-purple-100 text-purple-800 uppercase tracking-wider">
                                            Monthly
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-slate-100 text-slate-700 uppercase tracking-wider">
                                            Daily
                                        </span>
                                    @endif
                                </td>

                                <!-- 3. Basic / Wage Rate -->
                                <td class="px-3 py-3 text-right font-mono text-gray-900">
                                    @if($item->isMonthly())
                                        LKR {{ number_format($item->monthly_basic_snapshot, 2) }}
                                    @else
                                        LKR {{ number_format($item->daily_wage_snapshot, 2) }}
                                    @endif
                                </td>

                                <!-- 4. Full Days -->
                                <td class="px-2 py-3 text-center font-mono text-gray-800">
                                    {{ $item->full_days_count }}
                                </td>

                                <!-- 5. Half Days -->
                                <td class="px-2 py-3 text-center font-mono text-gray-800">
                                    {{ $item->half_days_count }}
                                </td>

                                <!-- 6. Company Off-Days -->
                                <td class="px-2 py-3 text-center font-mono">
                                    <span class="px-1.5 py-0.5 rounded text-xs font-bold {{ $item->company_off_days_count > 0 ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'text-gray-300' }}">
                                        {{ number_format($item->company_off_days_count, 1) }}
                                    </span>
                                </td>

                                <!-- 7. Paid Holidays -->
                                <td class="px-2 py-3 text-center font-mono">
                                    <span class="px-1.5 py-0.5 rounded text-xs font-bold {{ $item->paid_holidays_count > 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'text-gray-300' }}">
                                        {{ number_format($item->paid_holidays_count, 1) }}
                                    </span>
                                </td>

                                <!-- 8. No-Pay Days -->
                                <td class="px-2 py-3 text-center font-mono">
                                    @if($item->no_pay_days_count > 0)
                                        <span class="px-1.5 py-0.5 rounded text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            {{ number_format($item->no_pay_days_count, 1) }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">0.0</span>
                                    @endif
                                </td>

                                <!-- 9. No-Pay Deduction -->
                                <td class="px-3 py-3 text-right font-mono text-rose-700">
                                    @if($item->no_pay_deduction > 0)
                                        -{{ number_format($item->no_pay_deduction, 2) }}
                                    @else
                                        <span class="text-gray-300">&mdash;</span>
                                    @endif
                                </td>

                                <!-- 10. Overtime -->
                                <td class="px-3 py-3 text-right font-mono text-blue-900">
                                    @if($item->overtime_amount > 0)
                                        +{{ number_format($item->overtime_amount, 2) }}
                                    @else
                                        <span class="text-gray-300">&mdash;</span>
                                    @endif
                                </td>

                                <!-- 11. Bonuses -->
                                <td class="px-3 py-3 text-right font-mono text-purple-900">
                                    @if($item->bonus_amount > 0)
                                        +{{ number_format($item->bonus_amount, 2) }}
                                    @else
                                        <span class="text-gray-300">&mdash;</span>
                                    @endif
                                </td>

                                <!-- 12. Allowances -->
                                <td class="px-3 py-3 text-right font-mono text-indigo-900">
                                    @if($item->allowances_amount > 0)
                                        +{{ number_format($item->allowances_amount, 2) }}
                                    @else
                                        <span class="text-gray-300">&mdash;</span>
                                    @endif
                                </td>

                                <!-- 13. Gross Salary -->
                                <td class="px-3.5 py-3 text-right font-mono font-bold text-gray-900 bg-gray-50/50">
                                    LKR {{ number_format($item->gross_salary, 2) }}
                                </td>

                                <!-- 14. Employee EPF (8%) -->
                                <td class="px-3 py-3 text-right font-mono text-rose-700">
                                    @if($item->epf_employee_amount > 0)
                                        -{{ number_format($item->epf_employee_amount, 2) }}
                                    @else
                                        <span class="text-gray-300">&mdash;</span>
                                    @endif
                                </td>

                                <!-- 15. APIT -->
                                <td class="px-3 py-3 text-right font-mono text-rose-700">
                                    @if($item->apit_amount > 0)
                                        -{{ number_format($item->apit_amount, 2) }}
                                    @else
                                        <span class="text-gray-300">&mdash;</span>
                                    @endif
                                </td>

                                <!-- 16. Advance Recovery -->
                                <td class="px-3 py-3 text-right font-mono text-amber-700">
                                    @if($item->advances_applied > 0)
                                        -{{ number_format($item->advances_applied, 2) }}
                                    @else
                                        <span class="text-gray-300">&mdash;</span>
                                    @endif
                                </td>

                                <!-- 17. Net Salary -->
                                <td class="px-4 py-3 text-right font-mono font-extrabold text-sm {{ $isNegative ? 'text-rose-700 bg-rose-100/60' : 'text-emerald-700 bg-emerald-50/40' }}">
                                    LKR {{ number_format($item->net_salary, 2) }}
                                </td>

                                <!-- 18. Paid Amount -->
                                <td class="px-3 py-3 text-right font-mono text-blue-700 font-semibold">
                                    LKR {{ number_format($item->paid_amount, 2) }}
                                </td>

                                <!-- 19. Outstanding Balance -->
                                <td class="px-3 py-3 text-right font-mono font-semibold {{ $item->balance_due > 0 ? 'text-amber-800' : 'text-gray-400' }}">
                                    LKR {{ number_format($item->balance_due, 2) }}
                                </td>

                                <!-- 20. Status -->
                                <td class="px-3 py-3 text-center whitespace-nowrap">
                                    @if($item->status === 'paid')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            Paid
                                        </span>
                                    @elseif($item->status === 'partially_paid')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-300">
                                            Partially Paid
                                        </span>
                                    @elseif($item->status === 'approved')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-300">
                                            Approved
                                        </span>
                                    @elseif($period->isReviewed())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                            Reviewed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                            Draft
                                        </span>
                                    @endif
                                </td>

                                <!-- 21. Actions -->
                                <td class="px-4 py-3 text-right whitespace-nowrap space-x-1.5 sticky right-0 bg-white z-10">
                                    @if($period->isModifiable() && Auth::user()->isAdmin())
                                        <button 
                                            type="button" 
                                            @click="selectedItem = {{ json_encode($item) }}; showAdjustModal = true;"
                                            class="inline-flex items-center px-2 py-1 font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition cursor-pointer"
                                            title="Correct draft overtime, bonus, or other deductions"
                                        >
                                            Adjust
                                        </button>
                                    @endif

                                    <a 
                                        href="{{ route('payroll.salary.sheet', $item) }}" 
                                        class="inline-flex items-center px-2.5 py-1 font-semibold text-[#0B1849] bg-[#EEF1FA] hover:bg-[#dfe4f5] rounded-md transition"
                                        title="Detailed Employee Breakdown & Settlement"
                                    >
                                        Salary Sheet
                                    </a>

                                    <a 
                                        href="{{ route('payroll.salary.print', $item) }}" 
                                        target="_blank"
                                        class="inline-flex items-center px-2 py-1 font-semibold text-gray-600 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-md transition"
                                        title="Print Official Payslip"
                                    >
                                        Print
                                    </a>

                                    @if($period->isApproved() && $item->balance_due > 0 && Auth::user()->isAdmin())
                                        <button 
                                            type="button" 
                                            @click="paymentItem = {{ json_encode($item) }}; showPaymentModal = true;"
                                            class="inline-flex items-center px-2.5 py-1 font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-md transition cursor-pointer"
                                            title="Record salary disbursement"
                                        >
                                            Pay
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="21" class="px-4 py-8 text-center text-xs text-gray-500">
                                    No employee salary records match the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MOBILE RESPONSIVE CARD VIEW (Under 768px) -->
        <div class="block md:hidden space-y-3">
            @forelse($filteredItems as $item)
                @php $isNegative = (float) $item->net_salary < 0; @endphp
                <div class="bg-white rounded-xl border {{ $isNegative ? 'border-rose-300 bg-rose-50/20' : 'border-gray-200' }} p-4 shadow-xs space-y-3">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <h4 class="text-sm font-bold text-gray-900">{{ $item->employee_name_snapshot }}</h4>
                                @if($item->isMonthly())
                                    <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-purple-100 text-purple-800 uppercase">Monthly</span>
                                @else
                                    <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-slate-100 text-slate-700 uppercase">Daily</span>
                                @endif
                            </div>
                            <span class="text-xs text-gray-500 font-mono">
                                {{ $item->employee?->employee_number ?? "EMP-{$item->employee_id}" }} &bull; 
                                Rate: LKR {{ number_format($item->isMonthly() ? $item->monthly_basic_snapshot : $item->daily_wage_snapshot, 2) }}
                            </span>
                        </div>
                        <div>
                            @if($item->status === 'paid')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">Paid</span>
                            @elseif($item->status === 'approved')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">Approved</span>
                            @elseif($period->isReviewed())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">Reviewed</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Draft</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-1.5 text-center py-2 bg-gray-50 rounded-lg text-xs font-mono">
                        <div class="bg-white p-1 rounded border border-gray-100">
                            <span class="text-gray-400 block text-[9px] uppercase">Full / Half</span>
                            <span class="font-bold text-gray-900">{{ $item->full_days_count }} / {{ $item->half_days_count }}</span>
                        </div>
                        <div class="bg-white p-1 rounded border border-gray-100">
                            <span class="text-purple-700 block text-[9px] uppercase font-semibold">Off-Days</span>
                            <span class="font-bold text-purple-900">{{ number_format($item->company_off_days_count, 1) }}</span>
                        </div>
                        <div class="bg-white p-1 rounded border border-gray-100">
                            <span class="text-indigo-700 block text-[9px] uppercase font-semibold">Paid Hol.</span>
                            <span class="font-bold text-indigo-900">{{ number_format($item->paid_holidays_count, 1) }}</span>
                        </div>
                        <div class="bg-white p-1 rounded border border-gray-100">
                            <span class="text-rose-700 block text-[9px] uppercase font-semibold">No-Pay</span>
                            <span class="font-bold text-rose-900">{{ number_format($item->no_pay_days_count, 1) }} d</span>
                        </div>
                        <div class="bg-white p-1 rounded border border-gray-100">
                            <span class="text-gray-500 block text-[9px] uppercase font-semibold">Gross Pay</span>
                            <span class="font-bold text-gray-900">LKR {{ number_format($item->gross_salary, 2) }}</span>
                        </div>
                        <div class="bg-white p-1 rounded border border-gray-100">
                            <span class="text-rose-700 block text-[9px] uppercase font-semibold">Total Ded.</span>
                            <span class="font-bold text-rose-700">
                                -LKR {{ number_format($item->deductions + $item->no_pay_deduction + $item->epf_employee_amount + $item->apit_amount + $item->advances_applied, 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center text-xs py-1 border-t border-gray-100">
                        <div>
                            <span class="text-gray-500 block">Balance Due</span>
                            <span class="font-mono font-bold text-amber-700">LKR {{ number_format($item->balance_due, 2) }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-gray-500 block">Net Salary</span>
                            <span class="font-mono font-extrabold text-sm {{ $isNegative ? 'text-rose-700' : 'text-emerald-700' }}">
                                LKR {{ number_format($item->net_salary, 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                        @if($period->isModifiable() && Auth::user()->isAdmin())
                            <button 
                                type="button" 
                                @click="selectedItem = {{ json_encode($item) }}; showAdjustModal = true;"
                                class="px-2.5 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded-lg cursor-pointer"
                            >
                                Adjust
                            </button>
                        @endif
                        <a 
                            href="{{ route('payroll.salary.sheet', $item) }}" 
                            class="px-3 py-1 text-xs font-bold text-[#0B1849] bg-[#EEF1FA] rounded-lg"
                        >
                            Salary Sheet &rarr;
                        </a>
                        @if($period->isApproved() && $item->balance_due > 0 && Auth::user()->isAdmin())
                            <button 
                                type="button" 
                                @click="paymentItem = {{ json_encode($item) }}; showPaymentModal = true;"
                                class="px-3 py-1 text-xs font-bold text-white bg-emerald-600 rounded-lg cursor-pointer"
                            >
                                Pay
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center text-xs text-gray-500">
                    No employee salary records match the selected filters.
                </div>
            @endforelse
        </div>
    @else
        <!-- No Period Generated Yet Empty State -->
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center shadow-xs">
            <div class="w-14 h-14 mx-auto rounded-full bg-[#EEF1FA] text-[#0B1849] flex items-center justify-center mb-4">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-gray-900">
                No Salary Generated for {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
            </h3>
            <p class="text-xs text-gray-500 mt-1 max-w-md mx-auto">
                Generate the draft monthly salary to compute payable days, statutory withholdings (EPF, ETF, APIT), and deduct recorded salary advances automatically.
            </p>
            @if(Auth::user()->isAdmin())
                <form method="GET" action="{{ route('payroll.salary.index') }}" class="mt-5">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <button 
                        type="submit" 
                        name="generate" 
                        value="1" 
                        class="px-6 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-sm font-bold rounded-lg shadow-xs transition cursor-pointer"
                    >
                        Generate Salary for {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
                    </button>
                </form>
            @endif
        </div>
    @endif

    <!-- 1. ADJUSTMENT MODAL (Overtime, Bonus, Deductions, Notes) -->
    <div 
        x-show="showAdjustModal" 
        x-cloak 
        @keydown.escape.window="showAdjustModal = false"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
        role="dialog"
        aria-modal="true"
        aria-labelledby="adjust-modal-title"
    >
        <div 
            x-show="showAdjustModal"
            @click="showAdjustModal = false"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"
            aria-hidden="true"
        ></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 pointer-events-none">
            <div 
                x-show="showAdjustModal"
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
                    <h3 class="text-base font-bold text-gray-900" id="adjust-modal-title">Payroll Adjustments</h3>
                    <button type="button" @click="showAdjustModal = false" class="text-gray-400 hover:text-gray-600 transition cursor-pointer p-1 rounded-lg" aria-label="Close dialog">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <template x-if="selectedItem">
                    <form :action="'{{ url('payroll/salary') }}/' + selectedItem.id + '/adjustments'" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <span class="text-xs text-gray-500 block">Employee</span>
                            <span class="text-sm font-bold text-gray-900" x-text="selectedItem.employee_name_snapshot"></span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-xs p-3 bg-gray-50 rounded-lg font-mono">
                            <div>
                                <span class="text-gray-500 block">Base Earnings</span>
                                <span class="font-bold text-gray-900" x-text="'LKR ' + Number(selectedItem.base_earnings).toFixed(2)"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">Advances Applied</span>
                                <span class="font-bold text-amber-700" x-text="'- LKR ' + Number(selectedItem.advances_applied).toFixed(2)"></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Overtime Amount (LKR)</label>
                            <input type="number" step="0.01" min="0" name="overtime_amount" :value="selectedItem.overtime_amount" class="block w-full py-2 px-3 text-sm font-mono border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Bonus / Allowances (LKR)</label>
                            <input type="number" step="0.01" min="0" name="bonus_amount" :value="selectedItem.bonus_amount" class="block w-full py-2 px-3 text-sm font-mono border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Other Deductions (LKR)</label>
                            <input type="number" step="0.01" min="0" name="deductions" :value="selectedItem.deductions" class="block w-full py-2 px-3 text-sm font-mono border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
                            <span class="text-[10px] text-gray-400 block mt-0.5">Use this to correct any deduction shortfalls or excessive deductions</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Notes</label>
                            <textarea name="notes" rows="2" :value="selectedItem.notes" placeholder="Explanation for adjustments..." class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"></textarea>
                        </div>

                        <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                            <button type="button" @click="showAdjustModal = false" class="px-3 py-1.5 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg cursor-pointer transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-1.5 text-xs font-bold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-xs cursor-pointer transition">
                                Save Adjustments
                            </button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>

    <!-- 2. RECORD SALARY PAYMENT MODAL -->
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

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 pointer-events-none">
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

                <template x-if="paymentItem">
                    <form :action="'{{ url('payroll/salary') }}/' + paymentItem.id + '/payments'" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <span class="text-xs text-gray-500 block">Employee</span>
                            <span class="text-sm font-bold text-gray-900" x-text="paymentItem.employee_name_snapshot"></span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-xs p-3 bg-gray-50 rounded-lg font-mono">
                            <div>
                                <span class="text-gray-500 block">Net Salary</span>
                                <span class="font-bold text-emerald-700" x-text="'LKR ' + Number(paymentItem.net_salary).toFixed(2)"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">Balance Due</span>
                                <span class="font-bold text-amber-700" x-text="'LKR ' + Number(paymentItem.balance_due).toFixed(2)"></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Disbursement Amount (LKR) *</label>
                            <input 
                                type="number" 
                                name="amount" 
                                step="0.01" 
                                min="0.01" 
                                :max="paymentItem.balance_due" 
                                :value="paymentItem.balance_due" 
                                required 
                                class="block w-full py-2 px-3 text-sm font-mono font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Payment Date *</label>
                            <input 
                                type="date" 
                                name="payment_date" 
                                value="{{ today()->toDateString() }}" 
                                required 
                                class="block w-full py-2 px-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"
                            >
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
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Reference Number</label>
                            <input type="text" name="reference_number" placeholder="e.g. TXN-998822 or Cheque #" class="block w-full py-2 px-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Notes</label>
                            <textarea name="notes" rows="2" placeholder="Disbursement notes..." class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"></textarea>
                        </div>

                        <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                            <button type="button" @click="showPaymentModal = false" class="px-3 py-1.5 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg cursor-pointer transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-xs cursor-pointer transition">
                                Disburse Payment
                            </button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>

    <!-- 3. RECORD SALARY ADVANCE MODAL -->
    <div 
        x-show="showAdvanceModal" 
        x-cloak 
        @keydown.escape.window="showAdvanceModal = false"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
        role="dialog"
        aria-modal="true"
        aria-labelledby="advance-modal-title"
    >
        <div 
            x-show="showAdvanceModal"
            @click="showAdvanceModal = false"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"
            aria-hidden="true"
        ></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 pointer-events-none">
            <div 
                x-show="showAdvanceModal"
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
                    <h3 class="text-base font-bold text-gray-900" id="advance-modal-title">Record Salary Advance</h3>
                    <button type="button" @click="showAdvanceModal = false" class="text-gray-400 hover:text-gray-600 transition cursor-pointer p-1 rounded-lg" aria-label="Close dialog">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('payroll.advances.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Select Employee *</label>
                        <x-searchable-select 
                            name="employee_id" 
                            :endpoint="route('search.employees')" 
                            :required="true" 
                            placeholder="-- Choose Employee --" 
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Advance Amount (LKR) *</label>
                        <input type="number" name="amount" step="0.01" min="1" required placeholder="5000.00" class="block w-full py-2 px-3 text-sm font-mono font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Advance Date *</label>
                        <input type="date" name="advance_date" value="{{ today()->toDateString() }}" required class="block w-full py-2 px-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
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
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Monthly Installment (LKR)</label>
                        <input type="number" name="monthly_installment" step="0.01" min="0" placeholder="Optional partial recovery" class="block w-full py-2 px-3 text-sm font-mono border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
                        <span class="text-[10px] text-gray-400 block mt-0.5">Leave blank to recover full balance in a single payroll</span>
                    </div>

                    @if(Auth::user() && Auth::user()->isAdmin())
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Approval & Disbursement Status</label>
                            <select name="approval_status" class="block w-full py-2 px-3 text-sm border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]">
                                <option value="pending">Pending Approval</option>
                                <option value="approved">Approved (Awaiting Disbursement)</option>
                                <option value="disbursed">Immediate Disbursement (Pay Out & Update Money Book)</option>
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Notes (Optional)</label>
                        <textarea name="notes" rows="2" placeholder="e.g. Mid-month personal advance" class="block w-full py-2 px-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"></textarea>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="showAdvanceModal = false" class="px-3 py-1.5 text-xs font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-1.5 text-xs font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-xs transition cursor-pointer">
                            Record Advance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- PAYROLL REVIEW WARNINGS DETAILS MODAL -->
    <div 
        x-cloak 
        x-show="showWarningModal" 
        class="fixed inset-0 z-50 overflow-y-auto"
        @keydown.escape.window="showWarningModal = false"
        role="dialog"
        aria-modal="true"
    >
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <!-- Backdrop -->
            <div 
                x-show="showWarningModal" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0" 
                x-transition:enter-end="opacity-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100" 
                x-transition:leave-end="opacity-0" 
                @click="showWarningModal = false" 
                class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-xs"
            ></div>

            <!-- Modal Panel -->
            <div 
                x-show="showWarningModal" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                @click.stop 
                class="relative z-10 w-full max-w-2xl max-h-[85vh] flex flex-col p-6 my-8 text-left transition-all transform bg-white shadow-2xl rounded-2xl border border-gray-100 overflow-hidden"
            >
                <!-- Modal Header -->
                <div class="flex items-start justify-between pb-4 border-b border-gray-100 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-bold text-gray-900">Payroll Review Warnings & Attendance Details</h3>
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200 uppercase tracking-wider">
                                    Needs Review
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Review unrecorded attendance dates and non-blocking calculation notices for {{ $period?->formatted_period ?? 'current period' }}.
                            </p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="showWarningModal = false" 
                        class="text-gray-400 hover:text-gray-600 transition cursor-pointer p-1.5 rounded-lg hover:bg-gray-100"
                        aria-label="Close dialog"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Guidance Pills -->
                <div class="py-3 px-4 bg-gray-50 rounded-xl border border-gray-200 my-4 text-xs flex flex-wrap gap-4 items-center shrink-0">
                    <span class="inline-flex items-center gap-1.5 text-blue-700 font-semibold">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span> Information
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-amber-700 font-semibold">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Needs Review (Non-blocking)
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-rose-700 font-semibold">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Blocks Approval (Critical Errors)
                    </span>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="overflow-y-auto space-y-4 pr-1 flex-1">
                    <!-- Unrecorded Attendance Dates Section -->
                    @if(count($unmarkedDaysWarning) > 0)
                        <div class="border border-amber-200 bg-amber-50/50 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-xs font-bold text-amber-900 uppercase tracking-wider flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Unrecorded Attendance Dates ({{ count($unmarkedDaysWarning) }} Dates)
                                </h4>
                                <span class="text-[11px] font-medium text-amber-700">Financial Safety Notice</span>
                            </div>
                            <p class="text-xs text-amber-800 mb-3">
                                Attendance was not recorded for the following dates in this period. Unrecorded dates are NOT automatically marked absent or deducted; workshop supervisors should review and mark attendance as appropriate.
                            </p>

                            <div class="divide-y divide-amber-200/60 border border-amber-200/80 rounded-lg bg-white overflow-hidden max-h-60 overflow-y-auto">
                                @foreach($unmarkedDaysWarning as $warn)
                                    <div class="px-3.5 py-2.5 flex items-center justify-between hover:bg-amber-50/40 transition text-xs">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono font-bold text-gray-900">
                                                {{ \Carbon\Carbon::parse($warn['date'])->format('d M Y') }}
                                            </span>
                                            <span class="text-gray-400 font-normal">({{ \Carbon\Carbon::parse($warn['date'])->format('l') }})</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="text-[11px] font-semibold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-full">
                                                {{ $warn['missing'] }} {{ $warn['missing'] === 1 ? 'employee unrecorded' : 'employees unrecorded' }}
                                            </span>
                                            <a 
                                                href="{{ route('payroll.attendance.index', ['date' => $warn['date']]) }}" 
                                                class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-[#0B1849] hover:bg-[#0B1849] hover:text-white border border-[#0B1849]/30 rounded-md transition"
                                                title="Open attendance marking screen for this date"
                                            >
                                                Mark Attendance &rarr;
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Non-blocking Payroll Warnings Section -->
                    @if(!empty($validation['warnings']))
                        <div class="border border-gray-200 bg-white rounded-xl p-4">
                            <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Calculation & Rate Notices ({{ count($validation['warnings']) }})
                            </h4>
                            <ul class="space-y-2 text-xs text-gray-700">
                                @foreach($validation['warnings'] as $warning)
                                    <li class="p-2.5 rounded-lg bg-gray-50 border border-gray-100 flex items-start gap-2">
                                        <span class="text-blue-500 font-bold shrink-0">&bull;</span>
                                        <span>{{ $warning }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(empty($validation['warnings']) && count($unmarkedDaysWarning) === 0)
                        <div class="p-6 text-center text-xs text-gray-500">
                            <svg class="w-8 h-8 text-emerald-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="font-bold text-gray-800">No Review Warnings</p>
                            <p class="text-gray-500 mt-1">All attendance reconciliation and non-blocking calculations look complete.</p>
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-between shrink-0 mt-4">
                    <span class="text-[11px] text-gray-400">
                        Viran Auto Care &bull; Safe Payroll Engine
                    </span>
                    <button 
                        type="button" 
                        @click="showWarningModal = false" 
                        class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition cursor-pointer"
                    >
                        Close Details
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
