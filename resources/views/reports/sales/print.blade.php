<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - AutoCare System</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-white text-gray-900 p-8 text-xs font-sans">
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between border-b-2 border-[#0B1849] pb-4">
            <div>
                <h1 class="text-2xl font-bold text-[#0B1849]">AutoCare Service Center</h1>
                <p class="text-xs text-gray-500 mt-0.5">Sales Report (Invoice-Based Revenue Recognition)</p>
                @if($selectedBranch)
                    <p class="text-xs font-semibold text-gray-700 mt-1">Branch: {{ $selectedBranch->branch_name }}</p>
                @else
                    <p class="text-xs font-semibold text-gray-700 mt-1">Branch: All Branches (Company-wide)</p>
                @endif
                <p class="text-xs text-gray-500">Period: {{ $dateFrom }} to {{ $dateTo }}</p>
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-500">Generated: {{ now()->format('d M Y, h:i A') }}</div>
                <div class="text-xs font-bold text-[#0B1849] mt-1">Total Sales: LKR {{ number_format($totalSales, 2) }}</div>
                <div class="text-xs text-emerald-700">Collected: LKR {{ number_format($totalCollected, 2) }}</div>
                <div class="text-xs text-rose-600">Outstanding: LKR {{ number_format($totalOutstanding, 2) }}</div>
                <button onclick="window.print()" class="no-print mt-2 px-3 py-1 bg-[#0B1849] text-white rounded text-xs">Print</button>
            </div>
        </div>

        <!-- Table -->
        <table class="w-full text-left border-collapse border border-gray-300 text-xs">
            <thead>
                <tr class="bg-gray-100 border-b border-gray-300 font-bold text-gray-800">
                    <th class="p-2 border-r border-gray-300">Date</th>
                    <th class="p-2 border-r border-gray-300">Invoice #</th>
                    <th class="p-2 border-r border-gray-300">Customer / Company</th>
                    <th class="p-2 border-r border-gray-300">Vehicle</th>
                    <th class="p-2 border-r border-gray-300">Type</th>
                    <th class="p-2 border-r border-gray-300 text-right">Total (LKR)</th>
                    <th class="p-2 border-r border-gray-300 text-right">Paid (LKR)</th>
                    <th class="p-2 border-r border-gray-300 text-right">Balance (LKR)</th>
                    <th class="p-2 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($invoices as $inv)
                    @php
                        $isDirect = ($inv->invoice_type === 'direct_sale' || $inv->directSale !== null);
                    @endphp
                    <tr>
                        <td class="p-2 border-r border-gray-200">{{ $inv->invoice_date?->format('Y-m-d') }}</td>
                        <td class="p-2 border-r border-gray-200 font-mono font-bold">{{ $inv->invoice_number }}</td>
                        <td class="p-2 border-r border-gray-200">{{ $inv->customer?->display_name ?? 'Guest' }}</td>
                        <td class="p-2 border-r border-gray-200">{{ $inv->jobCard?->vehicle?->vehicle_number ?? '-' }}</td>
                        <td class="p-2 border-r border-gray-200">{{ $isDirect ? 'Direct' : 'Service' }}</td>
                        <td class="p-2 border-r border-gray-200 text-right font-medium">LKR {{ number_format($inv->grand_total, 2) }}</td>
                        <td class="p-2 border-r border-gray-200 text-right text-emerald-700">LKR {{ number_format($inv->amount_paid, 2) }}</td>
                        <td class="p-2 border-r border-gray-200 text-right text-rose-600">LKR {{ number_format($inv->balance_amount, 2) }}</td>
                        <td class="p-2 text-center">{{ $inv->payment_status }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-100 font-bold border-t border-gray-300">
                <tr>
                    <td colspan="5" class="p-2 text-right">Total:</td>
                    <td class="p-2 text-right text-[#0B1849]">LKR {{ number_format($totalSales, 2) }}</td>
                    <td class="p-2 text-right text-emerald-800">LKR {{ number_format($totalCollected, 2) }}</td>
                    <td class="p-2 text-right text-rose-700">LKR {{ number_format($totalOutstanding, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <!-- Footer -->
        <div class="pt-6 border-t border-gray-300 flex justify-between text-[11px] text-gray-500">
            <span>Sales revenue recognized on confirmed invoices. Quotations and cancelled invoices excluded.</span>
            <span>AutoCare System - Sales & Revenue Statement</span>
        </div>
    </div>
</body>
</html>
