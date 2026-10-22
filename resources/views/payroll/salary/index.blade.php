@extends('layouts.app')

@section('title', 'Employee Salary')

@section('content')
<div class="space-y-6" x-data="{ selectedItem: null, showAdjustModal: false, showAdvanceModal: false }">
    <!-- Header -->
    <x-page-header 
        title="Employee Salary" 
        description="Calculate monthly earnings from daily attendance, track advances, and finalize payroll."
    >
        <x-slot:actions>
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
                @endif
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- Month & Year Selector Card -->
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs">
        <form method="GET" action="{{ route('payroll.salary.index') }}" class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <div>
                    <label for="year" class="block text-xs font-semibold text-gray-700 mb-1">Select Year</label>
                    <select 
                        name="year" 
                        id="year" 
                        class="py-2 px-3 text-sm font-semibold text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-w-[120px]"
                    >
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="month" class="block text-xs font-semibold text-gray-700 mb-1">Select Month</label>
                    <select 
                        name="month" 
                        id="month" 
                        class="py-2 px-3 text-sm font-semibold text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-w-[140px]"
                    >
                        @foreach(range(1, 12) as $m)
                            @php $mName = \Carbon\Carbon::createFromDate($year, $m, 1)->format('F'); @endphp
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $mName }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-5">
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-semibold rounded-lg transition"
                    >
                        View Report
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if(Auth::user()->isAdmin())
                    @if(!$period || $period->isDraft())
                        <button 
                            type="submit" 
                            name="generate" 
                            value="1" 
                            class="px-5 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white text-sm font-bold rounded-lg shadow-xs transition flex items-center"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            {{ $period ? 'Recalculate Draft Salary' : 'Generate Salary' }}
                        </button>
                    @endif

                    @if($period && $period->isDraft())
                        <form method="POST" action="{{ route('payroll.salary.approve', $period) }}" onsubmit="return confirm('Approve monthly payroll for {{ $period->formatted_period }}? Once approved, pending salary advances will be finalized and locked.');">
                            @csrf
                            <button 
                                type="submit" 
                                class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-lg shadow-xs transition"
                            >
                                &#x2713; Approve Payroll
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </form>
    </div>

    <!-- Missing Attendance Warnings -->
    @if(count($unmarkedDaysWarning) > 0)
        <div class="bg-amber-50 border-l-4 border-amber-500 rounded-r-xl p-4 shadow-xs">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-amber-600 mr-3 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-amber-900">
                        Missing Attendance Warning ({{ count($unmarkedDaysWarning) }} past working dates have unmarked staff)
                    </h4>
                    <p class="text-xs text-amber-800 mt-1">
                        Salary calculations rely strictly on recorded daily attendance. Do not silently treat unmarked days as absent days. Please review and mark attendance for:
                    </p>
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        @foreach(array_slice($unmarkedDaysWarning, 0, 8) as $warn)
                            <a 
                                href="{{ route('payroll.attendance.index', ['date' => $warn['date']]) }}" 
                                class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-200/70 text-amber-900 hover:bg-amber-300 transition"
                            >
                                {{ \Carbon\Carbon::parse($warn['date'])->format('d M') }} ({{ $warn['missing'] }} missing) &rarr;
                            </a>
                        @endforeach
                        @if(count($unmarkedDaysWarning) > 8)
                            <span class="text-xs text-amber-700 font-semibold self-center">
                                +{{ count($unmarkedDaysWarning) - 8 }} more dates
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($period)
        <!-- Payroll Period Summary Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
                <span class="text-xs text-gray-500 block">Period Status</span>
                <div class="mt-1">
                    @if($period->status === 'paid')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Fully Paid
                        </span>
                    @elseif($period->status === 'partially_paid')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                            Partially Paid
                        </span>
                    @elseif($period->status === 'approved')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                            Approved
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                            Draft (Reviewable)
                        </span>
                    @endif
                </div>
                <span class="text-[10px] text-gray-400 block mt-1 font-mono">{{ $period->formatted_period }}</span>
            </div>

            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
                <span class="text-xs text-gray-500 block">Total Base Earnings</span>
                <span class="text-lg font-bold text-gray-900 font-mono">
                    LKR {{ number_format($period->total_base_earnings, 2) }}
                </span>
                <span class="text-[10px] text-gray-400 block mt-0.5">Sum of daily wages</span>
            </div>

            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
                <span class="text-xs text-gray-500 block">Total Gross Salary</span>
                <span class="text-lg font-bold text-gray-900 font-mono">
                    LKR {{ number_format($period->total_gross, 2) }}
                </span>
                <span class="text-[10px] text-gray-400 block mt-0.5">+ Overtime & Bonuses</span>
            </div>

            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
                <span class="text-xs text-gray-500 block">Total Net Salary</span>
                <span class="text-lg font-bold text-emerald-700 font-mono">
                    LKR {{ number_format($period->total_net, 2) }}
                </span>
                <span class="text-[10px] text-gray-400 block mt-0.5">Payable to staff</span>
            </div>

            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
                <span class="text-xs text-gray-500 block">Disbursed / Paid</span>
                <span class="text-lg font-bold text-blue-700 font-mono">
                    LKR {{ number_format($period->total_paid, 2) }}
                </span>
                <span class="text-[10px] text-gray-400 block mt-0.5">
                    Remaining: LKR {{ number_format(max(0, $period->total_net - $period->total_paid), 2) }}
                </span>
            </div>
        </div>

        <!-- Monthly Salary Report Table (Desktop) -->
        <div class="hidden md:block bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900">
                        Monthly Salary Sheet &bull; {{ $period->formatted_period }}
                    </h3>
                    <p class="text-xs text-gray-500">
                        Calculated from recorded daily attendance and wage rate snapshots.
                    </p>
                </div>
                @if($period->isDraft())
                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        Draft Report &mdash; Pending Final Approval
                    </span>
                @endif
            </div>

            <div class="responsive-table-wrapper">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Employee</th>
                            <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Full Days</th>
                            <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Half Days</th>
                            <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Absent</th>
                            <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Payable</th>
                            <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Gross Salary</th>
                            <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Advances</th>
                            <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Net Salary</th>
                            <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($period->items as $item)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="font-semibold text-gray-900 text-sm">
                                        {{ $item->employee_name_snapshot }}
                                    </div>
                                    <div class="text-xs text-gray-500 font-mono">
                                        Wage: LKR {{ number_format($item->daily_wage_snapshot, 2) }}
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center font-mono text-sm text-gray-900">
                                    {{ $item->full_days_count }}
                                </td>
                                <td class="px-4 py-3.5 text-center font-mono text-sm text-gray-900">
                                    {{ $item->half_days_count }}
                                </td>
                                <td class="px-4 py-3.5 text-center font-mono text-sm {{ $item->absent_days_count > 0 ? 'text-rose-600 font-bold' : 'text-gray-400' }}">
                                    {{ $item->absent_days_count }}
                                </td>
                                <td class="px-4 py-3.5 text-center font-mono font-bold text-sm text-[#0B1849]">
                                    {{ number_format($item->payable_days, 1) }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono font-medium text-sm text-gray-900">
                                    LKR {{ number_format($item->gross_salary, 2) }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono text-sm {{ $item->advances_applied > 0 ? 'text-amber-700 font-bold' : 'text-gray-400' }}">
                                    {{ $item->advances_applied > 0 ? '- LKR ' . number_format($item->advances_applied, 2) : '—' }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono font-extrabold text-sm text-emerald-700">
                                    LKR {{ number_format($item->net_salary, 2) }}
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
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
                                <td class="px-4 py-3.5 text-right whitespace-nowrap space-x-1.5 text-xs">
                                    @if($period->isDraft() && Auth::user()->isAdmin())
                                        <button 
                                            type="button" 
                                            @click="selectedItem = {{ json_encode($item) }}; showAdjustModal = true;"
                                            class="inline-flex items-center px-2.5 py-1 font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition"
                                        >
                                            Adjust
                                        </button>
                                    @endif
                                    <a 
                                        href="{{ route('payroll.salary.sheet', $item) }}" 
                                        class="inline-flex items-center px-2.5 py-1 font-semibold text-[#0B1849] bg-[#EEF1FA] hover:bg-[#dfe4f5] rounded-md transition"
                                    >
                                        Salary Sheet
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="block md:hidden space-y-3">
            @foreach($period->items as $item)
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-xs space-y-3">
                    <div class="flex items-start justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">{{ $item->employee_name_snapshot }}</h4>
                            <span class="text-xs text-gray-500 font-mono">Daily Wage: LKR {{ number_format($item->daily_wage_snapshot, 2) }}</span>
                        </div>
                        <div>
                            @if($item->status === 'paid')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">Paid</span>
                            @elseif($item->status === 'approved')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">Approved</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Draft</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-1 text-center py-2 bg-gray-50 rounded-lg text-xs font-mono">
                        <div>
                            <span class="text-gray-500 block text-[10px]">Full Days</span>
                            <span class="font-bold text-gray-900">{{ $item->full_days_count }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block text-[10px]">Half Days</span>
                            <span class="font-bold text-gray-900">{{ $item->half_days_count }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block text-[10px]">Absent</span>
                            <span class="font-bold text-rose-600">{{ $item->absent_days_count }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block text-[10px]">Payable</span>
                            <span class="font-bold text-[#0B1849]">{{ number_format($item->payable_days, 1) }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center text-xs py-1 border-t border-gray-100">
                        <div>
                            <span class="text-gray-500 block">Gross Salary</span>
                            <span class="font-mono font-medium text-gray-900">LKR {{ number_format($item->gross_salary, 2) }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-gray-500 block">Net Pay</span>
                            <span class="font-mono font-bold text-emerald-700 text-sm">LKR {{ number_format($item->net_salary, 2) }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                        @if($period->isDraft() && Auth::user()->isAdmin())
                            <button 
                                type="button" 
                                @click="selectedItem = {{ json_encode($item) }}; showAdjustModal = true;"
                                class="px-3 py-1.5 text-xs font-semibold text-gray-700 bg-gray-100 rounded-lg"
                            >
                                Adjust
                            </button>
                        @endif
                        <a 
                            href="{{ route('payroll.salary.sheet', $item) }}" 
                            class="px-3 py-1.5 text-xs font-bold text-[#0B1849] bg-[#EEF1FA] rounded-lg"
                        >
                            Salary Sheet &rarr;
                        </a>
                    </div>
                </div>
            @endforeach
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
                Generate the draft monthly salary to compute payable days, daily wage totals, and deduct recorded salary advances automatically.
            </p>
            @if(Auth::user()->isAdmin())
                <form method="GET" action="{{ route('payroll.salary.index') }}" class="mt-5">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <button 
                        type="submit" 
                        name="generate" 
                        value="1" 
                        class="px-6 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-sm font-bold rounded-lg shadow-xs transition"
                    >
                        Generate Salary for {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
                    </button>
                </form>
            @endif
        </div>
    @endif

    <!-- Adjustment Modal (Overtime, Bonus, Deductions) -->
    <div 
        x-show="showAdjustModal" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div @click="showAdjustModal = false" class="fixed inset-0 transition-opacity bg-gray-900/50 backdrop-blur-xs"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                    <h3 class="text-base font-bold text-gray-900">Payroll Adjustments</h3>
                    <button type="button" @click="showAdjustModal = false" class="text-gray-400 hover:text-gray-600">
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
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Notes</label>
                            <textarea name="notes" rows="2" :value="selectedItem.notes" placeholder="Explanation for adjustments..." class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"></textarea>
                        </div>

                        <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                            <button type="button" @click="showAdjustModal = false" class="px-3 py-1.5 text-xs font-semibold text-gray-600 bg-gray-100 rounded-lg">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-1.5 text-xs font-bold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg">
                                Save Adjustments
                            </button>
                        </div>
                    </form>
                </template>
            </div>
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

            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6">
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
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Select Employee *</label>
                        <select name="employee_id" required class="block w-full py-2 px-3 text-sm border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]">
                            <option value="">-- Choose Employee --</option>
                            @foreach($activeEmployees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->employee_number }}) - LKR {{ number_format($emp->daily_wage, 2) }}/day</option>
                            @endforeach
                        </select>
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
