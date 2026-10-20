<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statement - {{ $customer->customer_name }} - {{ $statementStart->format('F Y') }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-white text-gray-900 p-8 text-xs font-sans">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between border-b-2 border-[#0B1849] pb-4">
            <div>
                <h1 class="text-2xl font-bold text-[#0B1849]">AutoCare Service Center</h1>
                <p class="text-xs text-gray-500 mt-0.5">Corporate Credit & Fleet Billing Department</p>
                <p class="text-xs text-gray-500">Statement Period: {{ $statementStart->format('d M Y') }} – {{ $statementEnd->format('d M Y') }}</p>
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-400 uppercase font-semibold">Client Statement</div>
                <div class="text-base font-bold text-[#0B1849]">{{ $customer->display_name }}</div>
                <div class="text-xs text-gray-600">{{ $customer->address }}</div>
                <button onclick="window.print()" class="no-print mt-2 px-3 py-1 bg-[#0B1849] text-white rounded text-xs">Print A4 Statement</button>
            </div>
        </div>

        <!-- Account Balance Summary -->
        <div class="grid grid-cols-4 gap-3 text-center border border-gray-300 rounded p-3">
            <div>
                <div class="text-[10px] text-gray-500 uppercase font-bold">Opening Balance</div>
                <div class="text-sm font-bold mt-1">LKR {{ number_format($openingBalance, 2) }}</div>
            </div>
            <div>
                <div class="text-[10px] text-blue-700 uppercase font-bold">+ Current Invoices</div>
                <div class="text-sm font-bold text-blue-900 mt-1">LKR {{ number_format($monthInvoicesTotal, 2) }}</div>
            </div>
            <div>
                <div class="text-[10px] text-emerald-700 uppercase font-bold">- Current Payments</div>
                <div class="text-sm font-bold text-emerald-900 mt-1">LKR {{ number_format($monthPaymentsTotal, 2) }}</div>
            </div>
            <div class="bg-gray-100 rounded p-1">
                <div class="text-[10px] text-gray-800 uppercase font-bold">= Closing Balance Due</div>
                <div class="text-base font-bold text-[#0B1849] mt-0.5">LKR {{ number_format($closingBalance, 2) }}</div>
            </div>
        </div>

        <!-- Invoices List -->
        <div>
            <div class="font-bold text-[#0B1849] mb-2 uppercase tracking-wider text-xs">Invoices Billed During Month</div>
            <table class="w-full text-left border-collapse border border-gray-300 text-xs">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-300 font-bold text-gray-800">
                        <th class="p-2 border-r border-gray-300">Date</th>
                        <th class="p-2 border-r border-gray-300">Vehicle</th>
                        <th class="p-2 border-r border-gray-300">Invoice #</th>
                        <th class="p-2 border-r border-gray-300 text-right">Amount (LKR)</th>
                        <th class="p-2 border-r border-gray-300 text-right">Paid (LKR)</th>
                        <th class="p-2 text-right">Balance Due (LKR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($monthInvoices as $inv)
                        <tr>
                            <td class="p-2 border-r border-gray-200">{{ $inv->invoice_date?->format('Y-m-d') }}</td>
                            <td class="p-2 border-r border-gray-200 font-mono font-medium">{{ $inv->jobCard?->vehicle?->vehicle_number ?? 'Counter POS' }}</td>
                            <td class="p-2 border-r border-gray-200 font-mono font-bold">{{ $inv->invoice_number }}</td>
                            <td class="p-2 border-r border-gray-200 text-right">LKR {{ number_format($inv->grand_total, 2) }}</td>
                            <td class="p-2 border-r border-gray-200 text-right text-emerald-700">LKR {{ number_format($inv->amount_paid, 2) }}</td>
                            <td class="p-2 text-right font-bold {{ $inv->balance_amount > 0 ? 'text-rose-600' : 'text-gray-400' }}">LKR {{ number_format($inv->balance_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500 italic">No invoices issued during this period.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-100 font-bold border-t border-gray-300">
                    <tr>
                        <td colspan="3" class="p-2 text-right">Total Invoices:</td>
                        <td class="p-2 text-right">LKR {{ number_format($monthInvoicesTotal, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Payments List -->
        <div>
            <div class="font-bold text-[#0B1849] mb-2 uppercase tracking-wider text-xs">Payments Credited During Month</div>
            <table class="w-full text-left border-collapse border border-gray-300 text-xs">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-300 font-bold text-gray-800">
                        <th class="p-2 border-r border-gray-300">Date</th>
                        <th class="p-2 border-r border-gray-300">Receipt / Pay #</th>
                        <th class="p-2 border-r border-gray-300">Invoice #</th>
                        <th class="p-2 border-r border-gray-300">Payment Method</th>
                        <th class="p-2 border-r border-gray-300">Reference</th>
                        <th class="p-2 text-right">Amount Settled (LKR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($monthPayments as $p)
                        <tr>
                            <td class="p-2 border-r border-gray-200">{{ $p->payment_date?->format('Y-m-d') }}</td>
                            <td class="p-2 border-r border-gray-200 font-mono">{{ $p->payment_number }}</td>
                            <td class="p-2 border-r border-gray-200 font-mono">{{ $p->invoice?->invoice_number ?? 'Multi-Invoice' }}</td>
                            <td class="p-2 border-r border-gray-200">{{ ucfirst(str_replace('_', ' ', $p->payment_method)) }}</td>
                            <td class="p-2 border-r border-gray-200 font-mono">{{ $p->reference_number ?? '-' }}</td>
                            <td class="p-2 text-right font-bold text-emerald-800">LKR {{ number_format($p->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500 italic">No payments received during this period.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-100 font-bold border-t border-gray-300">
                    <tr>
                        <td colspan="5" class="p-2 text-right">Total Payments Credited:</td>
                        <td class="p-2 text-right text-emerald-800">LKR {{ number_format($monthPaymentsTotal, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Footer -->
        <div class="pt-8 grid grid-cols-2 gap-8 border-t border-gray-300">
            <div class="text-[11px] text-gray-500 space-y-1">
                <p><strong>Terms:</strong> Payment is due within {{ $creditAccount->payment_terms_days }} days from statement date.</p>
                <p>This statement reflects all confirmed invoices and payments across your corporate fleet.</p>
            </div>
            <div class="text-right flex flex-col justify-end items-end space-y-8">
                <div class="w-48 border-b border-gray-400"></div>
                <div class="text-xs font-semibold text-gray-700">Accounts Department Signature & Stamp</div>
            </div>
        </div>
    </div>
</body>
</html>
