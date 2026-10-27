@extends('layouts.app')

@section('title', 'Statutory & Tax (EPF, ETF, APIT)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <x-page-header 
        title="Statutory & Tax (EPF / ETF / APIT)" 
        description="Sri Lanka statutory contributions, Employees' Provident Fund (EPF), Employees' Trust Fund (ETF), and Advance Personal Income Tax (APIT)."
    >
        <x-slot:actions>
            <div class="flex flex-wrap items-center gap-2">
                <a 
                    href="{{ route('payroll.statutory.epf', ['year' => $year, 'month' => $month]) }}" 
                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition"
                >
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    EPF Form C
                </a>

                <a 
                    href="{{ route('payroll.statutory.etf', ['year' => $year, 'month' => $month]) }}" 
                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition"
                >
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    ETF Form 04
                </a>

                <a 
                    href="{{ route('payroll.statutory.apit', ['year' => $year, 'month' => $month]) }}" 
                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded-lg transition"
                >
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                    </svg>
                    IRD APIT Schedule
                </a>

                <a 
                    href="{{ route('payroll.statutory.employer-summary', ['year' => $year, 'month' => $month]) }}" 
                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-lg transition"
                >
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Employer CTC
                </a>

                @if(Auth::user()->isAdmin())
                    <a 
                        href="{{ route('payroll.statutory.settings') }}" 
                        class="inline-flex items-center px-3.5 py-1.5 text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg shadow-xs transition"
                    >
                        <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Statutory Rules
                    </a>
                @endif
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- Month & Year Selector Card -->
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs">
        <form method="GET" action="{{ route('payroll.statutory.index') }}" class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
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
                        class="px-4 py-2 text-xs font-semibold text-white bg-[#0B1849] hover:bg-[#0B1849]/90 rounded-lg transition"
                    >
                        Filter Period
                    </button>
                </div>
            </div>

            <!-- Active Rule Badge -->
            <div class="flex items-center gap-2 text-xs text-gray-600 bg-gray-50 border border-gray-200 px-3 py-2 rounded-lg">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Active Rule: <strong>EPF {{ number_format($statutorySetting->epf_employee_rate, 0) }}% / {{ number_format($statutorySetting->epf_employer_rate, 0) }}%</strong>, <strong>ETF {{ number_format($statutorySetting->etf_employer_rate, 0) }}%</strong>, APIT: {{ $statutorySetting->apit_enabled ? 'Active (Relief LKR '.number_format($statutorySetting->apit_monthly_relief, 0).')' : 'Disabled' }}</span>
            </div>
        </form>
    </div>

    <!-- Statutory Summary KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
        <!-- EPF Qualifying Base -->
        <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-[11px] font-medium text-gray-500 uppercase tracking-wider block">EPF Base</span>
            <span class="text-base font-bold text-gray-900 block mt-1">LKR {{ number_format($totalEpfQualifying, 2) }}</span>
            <span class="text-[10px] text-gray-400 block mt-0.5">Total Remuneration</span>
        </div>

        <!-- Employee EPF 8% -->
        <div class="bg-blue-50/50 p-3.5 rounded-xl border border-blue-200 shadow-xs">
            <span class="text-[11px] font-semibold text-blue-800 uppercase tracking-wider block">Employee EPF (8%)</span>
            <span class="text-base font-bold text-blue-900 block mt-1">LKR {{ number_format($totalEpfEmployee, 2) }}</span>
            <span class="text-[10px] text-blue-600 block mt-0.5">Deducted from salary</span>
        </div>

        <!-- Employer EPF 12% -->
        <div class="bg-indigo-50/50 p-3.5 rounded-xl border border-indigo-200 shadow-xs">
            <span class="text-[11px] font-semibold text-indigo-800 uppercase tracking-wider block">Employer EPF (12%)</span>
            <span class="text-base font-bold text-indigo-900 block mt-1">LKR {{ number_format($totalEpfEmployer, 2) }}</span>
            <span class="text-[10px] text-indigo-600 block mt-0.5">Company contribution</span>
        </div>

        <!-- Total EPF Remittance 20% -->
        <div class="bg-blue-100/60 p-3.5 rounded-xl border border-blue-300 shadow-xs">
            <span class="text-[11px] font-semibold text-blue-900 uppercase tracking-wider block">Total EPF (20%)</span>
            <span class="text-base font-bold text-blue-950 block mt-1">LKR {{ number_format($totalEpfEmployee + $totalEpfEmployer, 2) }}</span>
            <span class="text-[10px] text-blue-700 block mt-0.5">Form C Remittance</span>
        </div>

        <!-- Employer ETF 3% -->
        <div class="bg-emerald-50/50 p-3.5 rounded-xl border border-emerald-200 shadow-xs">
            <span class="text-[11px] font-semibold text-emerald-800 uppercase tracking-wider block">Employer ETF (3%)</span>
            <span class="text-base font-bold text-emerald-900 block mt-1">LKR {{ number_format($totalEtfEmployer, 2) }}</span>
            <span class="text-[10px] text-emerald-600 block mt-0.5">Form 04 Remittance</span>
        </div>

        <!-- Total APIT -->
        <div class="bg-purple-50/50 p-3.5 rounded-xl border border-purple-200 shadow-xs">
            <span class="text-[11px] font-semibold text-purple-800 uppercase tracking-wider block">APIT (PAYE)</span>
            <span class="text-base font-bold text-purple-900 block mt-1">LKR {{ number_format($totalApit, 2) }}</span>
            <span class="text-[10px] text-purple-600 block mt-0.5">IRD Tax Deductions</span>
        </div>

        <!-- Total Cost to Company (CTC) -->
        <div class="bg-amber-50/50 p-3.5 rounded-xl border border-amber-200 shadow-xs">
            <span class="text-[11px] font-semibold text-amber-800 uppercase tracking-wider block">Employer CTC</span>
            <span class="text-base font-bold text-amber-900 block mt-1">LKR {{ number_format($totalCostToCompany, 2) }}</span>
            <span class="text-[10px] text-amber-700 block mt-0.5">Gross + EPF12% + ETF3%</span>
        </div>
    </div>

    <!-- Employee Statutory Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-50/50">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Period Statutory Breakdown: {{ $periodDate->format('F Y') }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">Employee and Employer contributions computed under Sri Lankan statutory rules.</p>
            </div>
            @if($period)
                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full {{ $period->isDraft() ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                    Status: {{ ucfirst($period->status) }}
                </span>
            @endif
        </div>

        @if($items->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h4 class="text-sm font-semibold text-gray-900 mb-1">No Payroll Generated for this Period</h4>
                <p class="text-xs text-gray-500 max-w-sm mx-auto mb-4">
                    Please generate the draft payroll period in Employee Salary to calculate statutory contributions.
                </p>
                <a 
                    href="{{ route('payroll.salary.index', ['year' => $year, 'month' => $month, 'generate' => 1]) }}" 
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold text-white bg-[#0B1849] hover:bg-[#0B1849]/90 rounded-lg transition"
                >
                    Generate Draft for {{ $periodDate->format('F Y') }}
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-3">Employee</th>
                            <th class="py-3 px-2">Type</th>
                            <th class="py-3 px-3 text-right">EPF/ETF Base</th>
                            <th class="py-3 px-3 text-right text-blue-700 bg-blue-50/40">EPF 8% (Emp)</th>
                            <th class="py-3 px-3 text-right text-indigo-700 bg-indigo-50/40">EPF 12% (Co)</th>
                            <th class="py-3 px-3 text-right text-emerald-700 bg-emerald-50/40">ETF 3% (Co)</th>
                            <th class="py-3 px-3 text-right text-purple-700 bg-purple-50/40">APIT (PAYE)</th>
                            <th class="py-3 px-3 text-right font-bold text-gray-900">Take-Home (Net)</th>
                            <th class="py-3 px-3 text-right font-bold text-amber-900 bg-amber-50/40">Total CTC</th>
                            <th class="py-3 px-3 text-center">Status / Flag</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($items as $item)
                            @php
                                $emp = $item->employee;
                                $epfBase = (float) $item->epf_qualifying_earnings;
                                $epfEmp = (float) $item->epf_employee_amount;
                                $epfCo = (float) $item->epf_employer_amount;
                                $etfCo = (float) $item->etf_employer_amount;
                                $apit = (float) $item->apit_amount;
                                $ctc = round((float) $item->gross_salary + $epfCo + $etfCo, 2);

                                $breakdown = $item->attendance_breakdown['statutory_breakdown'] ?? [];
                                $flags = $breakdown['flagged_for_admin_review'] ?? [];
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="py-3 px-3">
                                    <div class="font-semibold text-gray-900">{{ $item->employee_name_snapshot }}</div>
                                    <div class="text-[10px] text-gray-500 font-mono">
                                        {{ $emp?->employee_number ?? 'EMP-'.$item->employee_id }} 
                                        @if($emp?->epf_number) • EPF: {{ $emp->epf_number }} @endif
                                    </div>
                                </td>
                                <td class="py-3 px-2">
                                    @if($item->isMonthly())
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">Monthly</span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-800">Daily</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right font-medium text-gray-900">
                                    {{ number_format($epfBase, 2) }}
                                </td>
                                <td class="py-3 px-3 text-right font-semibold text-blue-700 bg-blue-50/20">
                                    {{ number_format($epfEmp, 2) }}
                                </td>
                                <td class="py-3 px-3 text-right font-semibold text-indigo-700 bg-indigo-50/20">
                                    {{ number_format($epfCo, 2) }}
                                </td>
                                <td class="py-3 px-3 text-right font-semibold text-emerald-700 bg-emerald-50/20">
                                    {{ number_format($etfCo, 2) }}
                                </td>
                                <td class="py-3 px-3 text-right font-semibold text-purple-700 bg-purple-50/20">
                                    {{ number_format($apit, 2) }}
                                </td>
                                <td class="py-3 px-3 text-right font-bold text-gray-900">
                                    {{ number_format($item->net_salary, 2) }}
                                </td>
                                <td class="py-3 px-3 text-right font-bold text-amber-900 bg-amber-50/20">
                                    {{ number_format($ctc, 2) }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    @if(!empty($flags))
                                        <span 
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800 cursor-help"
                                            title="{{ implode('; ', $flags) }}"
                                        >
                                            <svg class="w-3 h-3 mr-1 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            Review
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-emerald-100 text-emerald-800">
                                            Compliant
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100 font-bold border-t-2 border-gray-300 text-gray-900 text-xs">
                        <tr>
                            <td colspan="2" class="py-3 px-3">Total Remuneration & Contributions</td>
                            <td class="py-3 px-3 text-right font-bold">{{ number_format($totalEpfQualifying, 2) }}</td>
                            <td class="py-3 px-3 text-right text-blue-900">{{ number_format($totalEpfEmployee, 2) }}</td>
                            <td class="py-3 px-3 text-right text-indigo-900">{{ number_format($totalEpfEmployer, 2) }}</td>
                            <td class="py-3 px-3 text-right text-emerald-900">{{ number_format($totalEtfEmployer, 2) }}</td>
                            <td class="py-3 px-3 text-right text-purple-900">{{ number_format($totalApit, 2) }}</td>
                            <td class="py-3 px-3 text-right font-bold">{{ number_format($totalNet, 2) }}</td>
                            <td class="py-3 px-3 text-right font-bold text-amber-950">{{ number_format($totalCostToCompany, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
