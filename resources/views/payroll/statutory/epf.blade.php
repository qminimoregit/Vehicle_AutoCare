@extends('layouts.app')

@section('title', 'Monthly EPF Return (Form C)')

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
            <h1 class="text-base font-bold text-gray-900">Employees' Provident Fund — Monthly Return (Form C)</h1>
        </div>

        <button 
            type="button" 
            onclick="window.print()" 
            class="inline-flex items-center px-4 py-2 text-xs font-semibold text-white bg-[#0B1849] hover:bg-[#0B1849]/90 rounded-lg transition"
        >
            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print Form C
        </button>
    </div>

    <!-- Official Form C Container -->
    <div class="bg-white p-8 rounded-xl border border-gray-200 shadow-xs print:border-none print:shadow-none print:p-0">
        <!-- Form C Header -->
        <div class="text-center pb-6 border-b border-gray-300">
            <h2 class="text-lg font-bold text-gray-900 uppercase tracking-wider">Employees' Provident Fund Department — Central Bank of Sri Lanka</h2>
            <h3 class="text-sm font-semibold text-gray-700 uppercase mt-1">Monthly Remittance Statement / Form 'C'</h3>
            <p class="text-xs text-gray-500 mt-1">Under Section 10 of the Employees' Provident Fund Act No. 15 of 1958</p>
        </div>

        <!-- Employer & Period Details -->
        <div class="grid grid-cols-2 gap-4 my-6 text-xs text-gray-800">
            <div class="space-y-1">
                <div><strong>Employer Name:</strong> {{ $company->company_name ?? 'Viran Auto Care' }}</div>
                <div><strong>Employer Registration No:</strong> {{ $company->registration_number ?? 'PV-2026-98765' }}</div>
                <div><strong>Address:</strong> {{ $company->address ?? 'Colombo, Sri Lanka' }}</div>
                <div><strong>Contact Phone:</strong> {{ $company->phone ?? '+94 11 234 5678' }}</div>
            </div>
            <div class="space-y-1 text-right">
                <div><strong>Contribution Period:</strong> <span class="text-sm font-bold text-blue-900">{{ $periodDate->format('F Y') }}</span></div>
                <div><strong>Contribution Rates:</strong> Employee {{ number_format($statutorySetting->epf_employee_rate, 0) }}% | Employer {{ number_format($statutorySetting->epf_employer_rate, 0) }}% | Total 20%</div>
                <div><strong>Number of Members:</strong> {{ $items->count() }}</div>
                <div><strong>Date Generated:</strong> {{ now()->format('Y-m-d H:i') }}</div>
            </div>
        </div>

        <!-- Member Records Table -->
        <table class="w-full border-collapse text-xs border border-gray-300 my-4">
            <thead>
                <tr class="bg-gray-100 text-gray-700 font-bold uppercase text-[10px] border-b border-gray-300">
                    <th class="border border-gray-300 p-2 text-center w-12">#</th>
                    <th class="border border-gray-300 p-2 text-left w-24">EPF No.</th>
                    <th class="border border-gray-300 p-2 text-left">Member Full Name</th>
                    <th class="border border-gray-300 p-2 text-center w-28">NIC No.</th>
                    <th class="border border-gray-300 p-2 text-right w-28">Total Remuneration (LKR)</th>
                    <th class="border border-gray-300 p-2 text-right w-24">Member ({{ number_format($statutorySetting->epf_employee_rate, 0) }}%)</th>
                    <th class="border border-gray-300 p-2 text-right w-24">Employer ({{ number_format($statutorySetting->epf_employer_rate, 0) }}%)</th>
                    <th class="border border-gray-300 p-2 text-right w-28">Total (20%)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sumRemuneration = 0;
                    $sumEmpEpf = 0;
                    $sumCoEpf = 0;
                    $sumTotalEpf = 0;
                @endphp
                @forelse($items as $index => $item)
                    @php
                        $emp = $item->employee;
                        $remuneration = (float) $item->epf_qualifying_earnings;
                        $empEpf = (float) $item->epf_employee_amount;
                        $coEpf = (float) $item->epf_employer_amount;
                        $totalRowEpf = round($empEpf + $coEpf, 2);

                        $sumRemuneration += $remuneration;
                        $sumEmpEpf += $empEpf;
                        $sumCoEpf += $coEpf;
                        $sumTotalEpf += $totalRowEpf;
                    @endphp
                    <tr class="border-b border-gray-200">
                        <td class="border border-gray-300 p-2 text-center">{{ $index + 1 }}</td>
                        <td class="border border-gray-300 p-2 font-mono font-medium">{{ $emp?->epf_number ?? 'EPF-'.$item->employee_id }}</td>
                        <td class="border border-gray-300 p-2 font-medium">{{ $item->employee_name_snapshot }}</td>
                        <td class="border border-gray-300 p-2 text-center font-mono">{{ $emp?->employee_number ?? '-' }}</td>
                        <td class="border border-gray-300 p-2 text-right font-medium">{{ number_format($remuneration, 2) }}</td>
                        <td class="border border-gray-300 p-2 text-right">{{ number_format($empEpf, 2) }}</td>
                        <td class="border border-gray-300 p-2 text-right">{{ number_format($coEpf, 2) }}</td>
                        <td class="border border-gray-300 p-2 text-right font-bold">{{ number_format($totalRowEpf, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="border border-gray-300 p-6 text-center text-gray-500">
                            No member records found for this period.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-100 font-bold border-t-2 border-gray-400">
                <tr>
                    <td colspan="4" class="border border-gray-300 p-2.5 text-right uppercase">Total Remittance:</td>
                    <td class="border border-gray-300 p-2.5 text-right font-bold text-gray-900">{{ number_format($sumRemuneration, 2) }}</td>
                    <td class="border border-gray-300 p-2.5 text-right font-bold text-blue-900">{{ number_format($sumEmpEpf, 2) }}</td>
                    <td class="border border-gray-300 p-2.5 text-right font-bold text-indigo-900">{{ number_format($sumCoEpf, 2) }}</td>
                    <td class="border border-gray-300 p-2.5 text-right font-bold text-gray-950">{{ number_format($sumTotalEpf, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Certification & Signatures -->
        <div class="mt-12 pt-6 border-t border-gray-300 grid grid-cols-2 gap-8 text-xs text-gray-700">
            <div>
                <p class="font-medium">I certify that the information given above is true and correct, and that the contributions remitted are in accordance with the Employees' Provident Fund Act.</p>
                <div class="mt-12 border-t border-gray-400 w-48 text-center pt-1 font-semibold">Authorized Signatory / Employer</div>
            </div>
            <div class="text-right">
                <p><strong>Remittance Method:</strong> Bank Direct Remittance / SLIPS / Cheque</p>
                <div class="mt-12 border-t border-gray-400 w-48 ml-auto text-center pt-1 font-semibold">Date & Official Stamp</div>
            </div>
        </div>
    </div>
</div>
@endsection
