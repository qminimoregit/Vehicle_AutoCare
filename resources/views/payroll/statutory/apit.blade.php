@extends('layouts.app')

@section('title', 'Monthly APIT Schedule (IRD)')

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
            <h1 class="text-base font-bold text-gray-900">Inland Revenue Department — Monthly APIT Schedule</h1>
        </div>

        <button 
            type="button" 
            onclick="window.print()" 
            class="inline-flex items-center px-4 py-2 text-xs font-semibold text-white bg-purple-700 hover:bg-purple-800 rounded-lg transition"
        >
            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print APIT Schedule
        </button>
    </div>

    <!-- Official IRD APIT Container -->
    <div class="bg-white p-8 rounded-xl border border-gray-200 shadow-xs print:border-none print:shadow-none print:p-0">
        <!-- Header -->
        <div class="text-center pb-6 border-b border-gray-300">
            <h2 class="text-lg font-bold text-gray-900 uppercase tracking-wider">Department of Inland Revenue — Sri Lanka</h2>
            <h3 class="text-sm font-semibold text-purple-900 uppercase mt-1">Advance Personal Income Tax (APIT) Monthly Remittance Schedule</h3>
            <p class="text-xs text-gray-500 mt-1">Under Section 83 of the Inland Revenue Act No. 24 of 2017 & Amendments (APIT Table 01 / Regular Monthly Employment Profits)</p>
        </div>

        <!-- Employer & Period Details -->
        <div class="grid grid-cols-2 gap-4 my-6 text-xs text-gray-800">
            <div class="space-y-1">
                <div><strong>Employer Name:</strong> {{ $company->company_name ?? 'Viran Auto Care' }}</div>
                <div><strong>Employer TIN:</strong> {{ $company->tax_number ?? 'TIN-2026-123456789' }}</div>
                <div><strong>Address:</strong> {{ $company->address ?? 'Colombo, Sri Lanka' }}</div>
                <div><strong>Standard Relief:</strong> LKR {{ number_format($statutorySetting->apit_monthly_relief, 2) }} / month (Resident Primary Employment)</div>
            </div>
            <div class="space-y-1 text-right">
                <div><strong>Assessment Period:</strong> <span class="text-sm font-bold text-purple-900">{{ $periodDate->format('F Y') }}</span></div>
                <div><strong>Tax Rules:</strong> {{ $periodDate->format('Y-m-d') >= '2025-04-01' ? 'IRD 2025/26 (Relief LKR 150k, Table 01)' : 'IRD Pre-2025 (Relief LKR 100k, Table 01)' }}</div>
                <div><strong>Number of Employees:</strong> {{ $items->count() }}</div>
                <div><strong>Date Generated:</strong> {{ now()->format('Y-m-d H:i') }}</div>
            </div>
        </div>

        <!-- Member Records Table -->
        <table class="w-full border-collapse text-xs border border-gray-300 my-4">
            <thead>
                <tr class="bg-gray-100 text-gray-700 font-bold uppercase text-[10px] border-b border-gray-300">
                    <th class="border border-gray-300 p-2 text-center w-10">#</th>
                    <th class="border border-gray-300 p-2 text-left w-24">TIN / Staff No.</th>
                    <th class="border border-gray-300 p-2 text-left">Employee Name</th>
                    <th class="border border-gray-300 p-2 text-center w-20">Residency</th>
                    <th class="border border-gray-300 p-2 text-right w-28">Gross Profits (LKR)</th>
                    <th class="border border-gray-300 p-2 text-right w-24">Relief (LKR)</th>
                    <th class="border border-gray-300 p-2 text-right w-28">Taxable Income</th>
                    <th class="border border-gray-300 p-2 text-right w-28 text-purple-900">APIT Deducted</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sumGross = 0;
                    $sumRelief = 0;
                    $sumTaxable = 0;
                    $sumApit = 0;
                @endphp
                @forelse($items as $index => $item)
                    @php
                        $emp = $item->employee;
                        $stat = $item->attendance_breakdown['statutory_breakdown'] ?? [];

                        $grossProfits = (float) ($stat['apit_taxable_earnings'] ?? $item->gross_salary);
                        $relief = (float) ($stat['apit_tax_relief'] ?? 0.00);
                        $taxableInc = (float) ($stat['apit_taxable_income'] ?? 0.00);
                        $apitDeducted = (float) $item->apit_amount;

                        $sumGross += $grossProfits;
                        $sumRelief += $relief;
                        $sumTaxable += $taxableInc;
                        $sumApit += $apitDeducted;
                    @endphp
                    <tr class="border-b border-gray-200">
                        <td class="border border-gray-300 p-2 text-center">{{ $index + 1 }}</td>
                        <td class="border border-gray-300 p-2 font-mono">{{ $emp?->tax_file_number ?? ($emp?->employee_number ?? '-') }}</td>
                        <td class="border border-gray-300 p-2 font-medium">{{ $item->employee_name_snapshot }}</td>
                        <td class="border border-gray-300 p-2 text-center">
                            {{ ($emp?->is_resident_for_tax ?? true) ? 'Resident' : 'Non-Resident' }}
                        </td>
                        <td class="border border-gray-300 p-2 text-right font-medium">{{ number_format($grossProfits, 2) }}</td>
                        <td class="border border-gray-300 p-2 text-right text-gray-600">{{ number_format($relief, 2) }}</td>
                        <td class="border border-gray-300 p-2 text-right font-medium">{{ number_format($taxableInc, 2) }}</td>
                        <td class="border border-gray-300 p-2 text-right font-bold text-purple-900">{{ number_format($apitDeducted, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="border border-gray-300 p-6 text-center text-gray-500">
                            No employment records found for this assessment period.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-100 font-bold border-t-2 border-gray-400">
                <tr>
                    <td colspan="4" class="border border-gray-300 p-2.5 text-right uppercase">Total Remittance:</td>
                    <td class="border border-gray-300 p-2.5 text-right font-bold text-gray-900">{{ number_format($sumGross, 2) }}</td>
                    <td class="border border-gray-300 p-2.5 text-right text-gray-700">{{ number_format($sumRelief, 2) }}</td>
                    <td class="border border-gray-300 p-2.5 text-right font-bold text-gray-900">{{ number_format($sumTaxable, 2) }}</td>
                    <td class="border border-gray-300 p-2.5 text-right font-bold text-purple-950 text-sm">{{ number_format($sumApit, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Certification & Signatures -->
        <div class="mt-12 pt-6 border-t border-gray-300 grid grid-cols-2 gap-8 text-xs text-gray-700">
            <div>
                <p class="font-medium">I declare to the best of my knowledge and belief that the particulars given in this return are true, correct and complete, and computed under official IRD APIT specifications.</p>
                <div class="mt-12 border-t border-gray-400 w-48 text-center pt-1 font-semibold">Authorized Employer / Officer</div>
            </div>
            <div class="text-right">
                <p><strong>Remittance:</strong> Inland Revenue Department Account</p>
                <div class="mt-12 border-t border-gray-400 w-48 ml-auto text-center pt-1 font-semibold">Date & Official Stamp</div>
            </div>
        </div>
    </div>
</div>
@endsection
