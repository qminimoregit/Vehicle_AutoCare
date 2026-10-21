<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Book - AutoCare System</title>
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
                <p class="text-xs text-gray-500 mt-0.5">Daily Money Book & Cash Flow Register</p>
                <p class="text-xs text-gray-600 mt-1">Period: {{ $dateFrom }} to {{ $dateTo }}</p>
                @if($selectedBranch)
                    <p class="text-xs font-semibold text-gray-700">Branch: {{ $selectedBranch->branch_name }}</p>
                @else
                    <p class="text-xs font-semibold text-gray-700">Branch: All Branches (Company-wide)</p>
                @endif
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-500">Generated: {{ now()->format('d M Y, h:i A') }}</div>
                <div class="text-xs font-bold text-emerald-700 mt-1">Total Cash: LKR {{ number_format($cashReceived, 2) }}</div>
                <div class="text-xs font-bold text-blue-700">Total Card: LKR {{ number_format($cardReceived, 2) }}</div>
                <div class="text-xs font-bold text-purple-700">Credit Sales Created: LKR {{ number_format($creditSalesCreated, 2) }}</div>
                <button onclick="window.print()" class="no-print mt-2 px-3 py-1 bg-[#0B1849] text-white rounded text-xs">Print</button>
            </div>
        </div>

        <!-- Collections Table -->
        <div>
            <div class="font-bold text-[#0B1849] mb-2 uppercase tracking-wider text-xs">Receipts & Collections</div>
            <table class="w-full text-left border-collapse border border-gray-300 text-xs">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-300 font-bold text-gray-800">
                        <th class="p-2 border-r border-gray-300">Date</th>
                        <th class="p-2 border-r border-gray-300">Pay #</th>
                        <th class="p-2 border-r border-gray-300">Invoice #</th>
                        <th class="p-2 border-r border-gray-300">Customer</th>
                        <th class="p-2 border-r border-gray-300">Method</th>
                        <th class="p-2 border-r border-gray-300">Reference</th>
                        <th class="p-2 text-right">Amount (LKR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($payments as $p)
                        <tr class="{{ $p->isReversed() ? 'line-through text-gray-400' : '' }}">
                            <td class="p-2 border-r border-gray-200">{{ $p->payment_date?->format('Y-m-d') }}</td>
                            <td class="p-2 border-r border-gray-200 font-mono">{{ $p->payment_number }}</td>
                            <td class="p-2 border-r border-gray-200 font-mono">{{ $p->invoice?->invoice_number ?? ($p->jobCard ? $p->jobCard->job_number . ' (Advance)' : '-') }}</td>
                            <td class="p-2 border-r border-gray-200">{{ $p->invoice?->customer?->display_name ?? ($p->jobCard?->customer?->display_name ?? ($p->customer?->display_name ?? 'Guest')) }}</td>
                            <td class="p-2 border-r border-gray-200">{{ ucfirst(str_replace('_', ' ', $p->payment_method)) }}</td>
                            <td class="p-2 border-r border-gray-200 font-mono">{{ $p->reference_number ?? '-' }}</td>
                            <td class="p-2 text-right font-medium">LKR {{ number_format($p->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-500">No payment transactions recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Credit Sales Created Table -->
        @if($creditSales->count() > 0)
            <div>
                <div class="font-bold text-purple-900 mb-2 uppercase tracking-wider text-xs">New Credit Sales Issued (Receivables)</div>
                <table class="w-full text-left border-collapse border border-gray-300 text-xs">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-300 font-bold text-gray-800">
                            <th class="p-2 border-r border-gray-300">Date</th>
                            <th class="p-2 border-r border-gray-300">Invoice #</th>
                            <th class="p-2 border-r border-gray-300">Customer</th>
                            <th class="p-2 border-r border-gray-300 text-right">Invoice Total</th>
                            <th class="p-2 border-r border-gray-300 text-right">Paid</th>
                            <th class="p-2 text-right">Balance Added to Credit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($creditSales as $cs)
                            <tr>
                                <td class="p-2 border-r border-gray-200">{{ $cs->invoice_date?->format('Y-m-d') }}</td>
                                <td class="p-2 border-r border-gray-200 font-mono font-bold">{{ $cs->invoice_number }}</td>
                                <td class="p-2 border-r border-gray-200">{{ $cs->customer?->display_name }}</td>
                                <td class="p-2 border-r border-gray-200 text-right">LKR {{ number_format($cs->grand_total, 2) }}</td>
                                <td class="p-2 border-r border-gray-200 text-right">LKR {{ number_format($cs->amount_paid, 2) }}</td>
                                <td class="p-2 text-right font-bold text-purple-800">LKR {{ number_format($cs->balance_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Footer -->
        <div class="pt-6 border-t border-gray-300 flex justify-between text-[11px] text-gray-500">
            <span>Money Book receipts recorded on payment transaction dates.</span>
            <span>AutoCare System - Cash & Credit Management</span>
        </div>
    </div>
</body>
</html>
