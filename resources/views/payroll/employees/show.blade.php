@extends('layouts.app')

@section('title', $employee->name . ' - Employee Details')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'attendance', showAdvanceModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl bg-[#0B1849] text-white font-bold text-xl flex items-center justify-center shrink-0 shadow-xs">
                {{ strtoupper(substr($employee->name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center space-x-3">
                    <h1 class="text-xl font-bold text-gray-900 tracking-tight">{{ $employee->name }}</h1>
                    @if($employee->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                            Inactive
                        </span>
                    @endif
                </div>
                <p class="text-xs text-gray-500 font-mono mt-0.5">
                    {{ $employee->employee_number }} &bull; {{ $employee->position ?: 'General Staff' }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if(Auth::user()->isAdmin())
                <button 
                    type="button" 
                    @click="showAdvanceModal = true"
                    class="inline-flex items-center px-3.5 py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Record Advance
                </button>

                <a 
                    href="{{ route('payroll.employees.edit', $employee) }}" 
                    class="inline-flex items-center px-3.5 py-2 text-xs font-semibold text-[#0B1849] bg-[#EEF1FA] hover:bg-[#dfe4f5] rounded-lg transition"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Profile
                </a>
            @endif

            <a 
                href="{{ route('payroll.employees.index') }}" 
                class="inline-flex items-center px-3.5 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-lg shadow-xs transition"
            >
                Back to List
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs text-gray-500 block">Current Daily Wage</span>
            <span class="text-lg font-bold text-gray-900 font-mono">LKR {{ number_format($employee->daily_wage, 2) }}</span>
            <span class="text-[10px] text-gray-400 block mt-0.5">Base per working day</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs text-gray-500 block">Joining Date</span>
            <span class="text-base font-bold text-gray-900">
                {{ $employee->joining_date ? $employee->joining_date->format('d M Y') : 'Not Set' }}
            </span>
            <span class="text-[10px] text-gray-400 block mt-0.5">
                {{ $employee->joining_date ? $employee->joining_date->diffForHumans() : '' }}
            </span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs text-gray-500 block">Contact Phone</span>
            <span class="text-base font-bold text-gray-900">{{ $employee->mobile ?: '—' }}</span>
            <span class="text-[10px] text-gray-400 block mt-0.5">{{ $employee->address ?: 'Address not provided' }}</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs text-gray-500 block">Pending Advances</span>
            @php
                $pendingSum = $employee->advances->where('status', 'pending')->sum('amount');
            @endphp
            <span class="text-lg font-bold {{ $pendingSum > 0 ? 'text-amber-600' : 'text-gray-900' }} font-mono">
                LKR {{ number_format($pendingSum, 2) }}
            </span>
            <span class="text-[10px] text-gray-400 block mt-0.5">To deduct on next payroll</span>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 bg-white rounded-t-xl px-4">
        <nav class="flex space-x-6">
            <button 
                type="button" 
                @click="activeTab = 'attendance'" 
                :class="activeTab === 'attendance' ? 'border-[#0B1849] text-[#0B1849] font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="py-3 px-1 border-b-2 text-xs font-semibold uppercase tracking-wider transition cursor-pointer"
            >
                Attendance History
            </button>
            <button 
                type="button" 
                @click="activeTab = 'salary'" 
                :class="activeTab === 'salary' ? 'border-[#0B1849] text-[#0B1849] font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="py-3 px-1 border-b-2 text-xs font-semibold uppercase tracking-wider transition cursor-pointer"
            >
                Salary History
            </button>
            <button 
                type="button" 
                @click="activeTab = 'wages'" 
                :class="activeTab === 'wages' ? 'border-[#0B1849] text-[#0B1849] font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="py-3 px-1 border-b-2 text-xs font-semibold uppercase tracking-wider transition cursor-pointer"
            >
                Wage Rate History
            </button>
            <button 
                type="button" 
                @click="activeTab = 'advances'" 
                :class="activeTab === 'advances' ? 'border-[#0B1849] text-[#0B1849] font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="py-3 px-1 border-b-2 text-xs font-semibold uppercase tracking-wider transition cursor-pointer"
            >
                Advances ({{ $employee->advances->count() }})
            </button>
        </nav>
    </div>

    <!-- Tab 1: Attendance History -->
    <div x-show="activeTab === 'attendance'" class="bg-white rounded-b-xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Recent Attendance (Last 30 Records)</h3>
            <a 
                href="{{ route('payroll.attendance.index') }}" 
                class="text-xs font-semibold text-[#0B1849] hover:underline"
            >
                Open Daily Attendance &rarr;
            </a>
        </div>
        @if($employee->attendances->count() > 0)
            <div class="responsive-table-wrapper">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Payable Day</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Applied Daily Wage</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Daily Earnings</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($employee->attendances as $att)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $att->attendance_date->format('d M Y') }}
                                    <span class="text-xs text-gray-400 ml-1">({{ $att->attendance_date->format('D') }})</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $badgeClasses = match($att->status) {
                                            'full_day' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'half_day' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'absent' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            'leave' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'day_off' => 'bg-gray-100 text-gray-700 border-gray-200',
                                            default => 'bg-gray-100 text-gray-700 border-gray-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $badgeClasses }}">
                                        {{ \App\Models\EmployeeAttendance::getStatusLabel($att->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center font-mono">
                                    {{ number_format($att->payable_day_fraction, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-gray-600">
                                    LKR {{ number_format($att->applied_daily_wage, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-semibold text-gray-900">
                                    LKR {{ number_format($att->daily_earnings, 2) }}
                                </td>
                                <td class="px-4 py-3 text-gray-500 text-xs">
                                    {{ $att->notes ?: '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-gray-400 text-xs">
                No attendance recorded yet for this employee.
            </div>
        @endif
    </div>

    <!-- Tab 2: Salary History -->
    <div x-show="activeTab === 'salary'" class="bg-white rounded-b-xl border border-gray-200 shadow-xs overflow-hidden" style="display: none;">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Monthly Salary History</h3>
            <span class="text-xs text-gray-400">All generated monthly payroll runs</span>
        </div>
        @if($employee->payrollItems->count() > 0)
            <div class="responsive-table-wrapper">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Month / Year</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Payable Days</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Base Earnings</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Advances</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Gross</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Net Pay</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($employee->payrollItems as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-semibold text-gray-900 whitespace-nowrap">
                                    {{ $item->period->formatted_period }}
                                </td>
                                <td class="px-4 py-3 text-center font-mono">
                                    {{ number_format($item->payable_days, 1) }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-gray-700">
                                    LKR {{ number_format($item->base_earnings, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-amber-700">
                                    {{ $item->advances_applied > 0 ? '- LKR ' . number_format($item->advances_applied, 2) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-medium text-gray-900">
                                    LKR {{ number_format($item->gross_salary, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-emerald-700">
                                    LKR {{ number_format($item->net_salary, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($item->status === 'paid')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Paid
                                        </span>
                                    @elseif($item->status === 'partially_paid')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                            Partially Paid
                                        </span>
                                    @elseif($item->status === 'approved')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            Approved
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                            Draft
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a 
                                        href="{{ route('payroll.salary.sheet', $item) }}" 
                                        class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-[#0B1849] bg-[#EEF1FA] hover:bg-[#dfe4f5] rounded-md transition"
                                    >
                                        Salary Sheet
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-gray-400 text-xs">
                No salary sheets generated yet for this employee.
            </div>
        @endif
    </div>

    <!-- Tab 3: Wage Rate History -->
    <div x-show="activeTab === 'wages'" class="bg-white rounded-b-xl border border-gray-200 shadow-xs overflow-hidden" style="display: none;">
        <div class="p-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Daily Wage History & Historical Snapshots</h3>
            <p class="text-xs text-gray-500 mt-0.5">Wage rate periods are immutable to safeguard historical payroll calculations.</p>
        </div>
        <div class="responsive-table-wrapper">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Effective From</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Effective To</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Daily Wage</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Reason / Notes</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($employee->wageRates as $rate)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold text-gray-900 whitespace-nowrap">
                                {{ $rate->effective_from->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                {{ $rate->effective_to ? $rate->effective_to->format('d M Y') : 'Present (Current)' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-gray-900">
                                LKR {{ number_format($rate->daily_wage, 2) }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 text-xs">
                                {{ $rate->reason ?: '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if(is_null($rate->effective_to))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                        Historical
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-400 text-xs">
                                No separate wage rate history recorded.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tab 4: Advances History -->
    <div x-show="activeTab === 'advances'" class="bg-white rounded-b-xl border border-gray-200 shadow-xs overflow-hidden" style="display: none;">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Salary Advances</h3>
                <p class="text-xs text-gray-500 mt-0.5">Disbursed cash advances and deduction status.</p>
            </div>
            @if(Auth::user()->isAdmin())
                <button 
                    type="button" 
                    @click="showAdvanceModal = true"
                    class="inline-flex items-center px-3 py-1.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Record Advance
                </button>
            @endif
        </div>
        <div class="responsive-table-wrapper">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Advance #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Date</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Payment Method</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($employee->advances as $adv)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono font-semibold text-gray-900">
                                {{ $adv->advance_number }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $adv->advance_date->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-gray-900">
                                LKR {{ number_format($adv->amount, 2) }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 capitalize">
                                {{ str_replace('_', ' ', $adv->payment_method) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($adv->status === 'deducted')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Deducted in Payroll
                                    </span>
                                @elseif($adv->status === 'cancelled')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                        Cancelled
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Pending Deduction
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $adv->notes ?: '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-gray-400 text-xs">
                                No salary advances recorded for this employee.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Record Advance Modal -->
    <div 
        x-show="showAdvanceModal" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div @click="showAdvanceModal = false" class="fixed inset-0 transition-opacity bg-gray-900/50 backdrop-blur-xs"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-xl text-left shadow-xl transform transition-all sm:my-8 sm:align-middle w-full max-w-[calc(100vw-2rem)] sm:max-w-md max-h-[88vh] overflow-y-auto p-4 sm:p-6">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                    <h3 class="text-base font-bold text-gray-900">Record Salary Advance</h3>
                    <button type="button" @click="showAdvanceModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('payroll.advances.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Employee</label>
                        <input type="text" value="{{ $employee->name }} ({{ $employee->employee_number }})" disabled class="block w-full py-2 px-3 text-xs bg-gray-50 border border-gray-300 rounded-lg text-gray-600">
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
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Disbursement Method *</label>
                        <select name="payment_method" class="block w-full py-2 px-3 text-sm border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]">
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Notes (Optional)</label>
                        <textarea name="notes" rows="2" placeholder="e.g. Mid-month personal advance" class="block w-full py-2 px-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"></textarea>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="showAdvanceModal = false" class="px-3 py-1.5 text-xs font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-1.5 text-xs font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg">
                            Record Advance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
