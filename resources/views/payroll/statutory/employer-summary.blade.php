@extends('layouts.app')

@section('title', 'Employer Contributions & CTC Summary')

@section('content')
<div class="space-y-6">
    <!-- Action Bar (Hidden when printing) -->
    <div class="flex items-center justify-between print:hidden">
        <div class="flex items-center gap-3">
            <a 
                href="{{ route('payroll.statutory.index', ['year' => $year, 'month' => $month]) }}" 
                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg shadow-xs transition"
            >
                <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Statutory
            </a>
            <h1 class="text-base font-bold text-gray-900">Employer Contributions & Total Cost to Company (CTC)</h1>
        </div>

        <button 
            type="button" 
            onclick="window.print()" 
            class="inline-flex items-center px-4 py-2 text-xs font-semibold text-white bg-amber-700 hover:bg-amber-800 rounded-lg transition"
        >
            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print CTC Summary
        </button>
    </div>

    <!-- Official CTC Container -->
    <div class="bg-white p-8 rounded-xl border border-gray-200 shadow-xs print:border-none print:shadow-none print:p-0">
        <!-- Header -->
        <div class="text-center pb-6 border-b border-gray-300">
            <h2 class="text-lg font-bold text-gray-900 uppercase tracking-wider">{{ $company->company_name ?? 'Viran Auto Care' }}</h2>
            <h3 class="text-sm font-semibold text-amber-900 uppercase mt-1">Employer Contribution Liabilities & Cost to Company (CTC)</h3>
            <p class="text-xs text-gray-500 mt-1">Period: {{ $periodDate->format('F Y') }} • Generated: {{ now()->format('Y-m-d H:i') }}</p>
        </div>

        <!-- Summary KPIs -->
        @php
            $sumGross = (float) $items->sum('gross_salary');
            $sumEpfEmployer = (float) $items->sum('epf_employer_amount');
            $sumEtfEmployer = (float) $items->sum('etf_employer_amount');
            $sumTotalEmployerContrib = round($sumEpfEmployer + $sumEtfEmployer, 2);
            $sumCtc = round($sumGross + $sumTotalEmployerContrib, 2);
            $sumNet = (float) $items->sum('net_salary');
        @endphp

        <div class="grid grid-cols-4 gap-4 my-6 text-center">
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                <div class="text-xs font-semibold text-gray-500 uppercase">Gross Salary</div>
                <div class="text-lg font-bold text-gray-900 mt-1">LKR {{ number_format($sumGross, 2) }}</div>
            </div>
            <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-200">
                <div class="text-xs font-semibold text-indigo-700 uppercase">Employer EPF (12%)</div>
                <div class="text-lg font-bold text-indigo-900 mt-1">LKR {{ number_format($sumEpfEmployer, 2) }}</div>
            </div>
            <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-200">
                <div class="text-xs font-semibold text-emerald-700 uppercase">Employer ETF (3%)</div>
                <div class="text-lg font-bold text-emerald-900 mt-1">LKR {{ number_format($sumEtfEmployer, 2) }}</div>
            </div>
            <div class="bg-amber-100/60 p-4 rounded-xl border border-amber-300">
                <div class="text-xs font-bold text-amber-900 uppercase">Total CTC (115%)</div>
                <div class="text-lg font-extrabold text-amber-950 mt-1">LKR {{ number_format($sumCtc, 2) }}</div>
            </div>
        </div>

        <!-- Employee CTC Table -->
        <table class="w-full border-collapse text-xs border border-gray-300 my-4">
            <thead>
                <tr class="bg-gray-100 text-gray-700 font-bold uppercase text-[10px] border-b border-gray-300">
                    <th class="border border-gray-300 p-2 text-center w-10">#</th>
                    <th class="border border-gray-300 p-2 text-left">Employee Name</th>
                    <th class="border border-gray-300 p-2 text-left w-24">Type</th>
                    <th class="border border-gray-300 p-2 text-right w-28">Gross Salary</th>
                    <th class="border border-gray-300 p-2 text-right w-24 text-indigo-800">Employer EPF (12%)</th>
                    <th class="border border-gray-300 p-2 text-right w-24 text-emerald-800">Employer ETF (3%)</th>
                    <th class="border border-gray-300 p-2 text-right w-28 font-bold text-amber-900">Total CTC</th>
                    <th class="border border-gray-300 p-2 text-right w-28 font-bold text-gray-900">Net Take-Home</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                    @php
                        $coEpf = (float) $item->epf_employer_amount;
                        $coEtf = (float) $item->etf_employer_amount;
                        $rowCtc = round((float) $item->gross_salary + $coEpf + $coEtf, 2);
                    @endphp
                    <tr class="border-b border-gray-200">
                        <td class="border border-gray-300 p-2 text-center">{{ $index + 1 }}</td>
                        <td class="border border-gray-300 p-2 font-medium">{{ $item->employee_name_snapshot }}</td>
                        <td class="border border-gray-300 p-2">{{ $item->isMonthly() ? 'Monthly' : 'Daily' }}</td>
                        <td class="border border-gray-300 p-2 text-right font-medium">{{ number_format($item->gross_salary, 2) }}</td>
                        <td class="border border-gray-300 p-2 text-right text-indigo-800 font-medium">{{ number_format($coEpf, 2) }}</td>
                        <td class="border border-gray-300 p-2 text-right text-emerald-800 font-medium">{{ number_format($coEtf, 2) }}</td>
                        <td class="border border-gray-300 p-2 text-right font-bold text-amber-950">{{ number_format($rowCtc, 2) }}</td>
                        <td class="border border-gray-300 p-2 text-right font-bold text-gray-900">{{ number_format($item->net_salary, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="border border-gray-300 p-6 text-center text-gray-500">
                            No records found for this period.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-100 font-bold border-t-2 border-gray-400">
                <tr>
                    <td colspan="3" class="border border-gray-300 p-2.5 text-right uppercase">Period Totals:</td>
                    <td class="border border-gray-300 p-2.5 text-right font-bold text-gray-900">{{ number_format($sumGross, 2) }}</td>
                    <td class="border border-gray-300 p-2.5 text-right font-bold text-indigo-900">{{ number_format($sumEpfEmployer, 2) }}</td>
                    <td class="border border-gray-300 p-2.5 text-right font-bold text-emerald-900">{{ number_format($sumEtfEmployer, 2) }}</td>
                    <td class="border border-gray-300 p-2.5 text-right font-bold text-amber-950 text-sm">{{ number_format($sumCtc, 2) }}</td>
                    <td class="border border-gray-300 p-2.5 text-right font-bold text-gray-900 text-sm">{{ number_format($sumNet, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Signatures -->
        <div class="mt-12 pt-6 border-t border-gray-300 grid grid-cols-2 gap-8 text-xs text-gray-700">
            <div>
                <p>Prepared by Payroll Accountant / HR</p>
                <div class="mt-12 border-t border-gray-400 w-48 text-center pt-1 font-semibold">Signature</div>
            </div>
            <div class="text-right">
                <p>Approved by Director / Managing Partner</p>
                <div class="mt-12 border-t border-gray-400 w-48 ml-auto text-center pt-1 font-semibold">Signature & Official Stamp</div>
            </div>
        </div>
    </div>
</div>
@endsection
