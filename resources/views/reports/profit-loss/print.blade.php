<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit & Loss Statement - AutoCare System</title>
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
                <p class="text-xs text-gray-500 mt-0.5">Profit & Loss Statement (Financial Performance)</p>
                <p class="text-xs text-gray-600 mt-1">Period: {{ $dateFrom }} to {{ $dateTo }}</p>
                @if($selectedBranch)
                    <p class="text-xs font-semibold text-gray-700">Branch: {{ $selectedBranch->branch_name }}</p>
                @else
                    <p class="text-xs font-semibold text-gray-700">Branch: All Branches (Company-wide)</p>
                @endif
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-500">Generated: {{ now()->format('d M Y, h:i A') }}</div>
                <div class="text-xs font-bold text-[#0B1849] mt-1">Net Revenue: LKR {{ number_format($netRevenue, 2) }}</div>
                <div class="text-xs font-bold {{ $netProfit >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                    Net Profit: LKR {{ number_format($netProfit, 2) }}
                </div>
                <button onclick="window.print()" class="no-print mt-2 px-3 py-1 bg-[#0B1849] text-white rounded text-xs">Print</button>
            </div>
        </div>

        <!-- Statement Body -->
        <div class="space-y-4">
            <!-- 1. Revenue -->
            <div class="border border-gray-300 rounded p-3">
                <div class="font-bold border-b border-gray-300 pb-1 text-[#0B1849]">1. REVENUE</div>
                <div class="mt-2 space-y-1">
                    <div class="flex justify-between">
                        <span>Direct Sales (Counter POS)</span>
                        <span>LKR {{ number_format($directSalesRevenue, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Service Sales (Workshop Job Cards)</span>
                        <span>LKR {{ number_format($serviceSalesRevenue, 2) }}</span>
                    </div>
                    @if($otherRevenue > 0)
                        <div class="flex justify-between">
                            <span>Other Operating Revenue</span>
                            <span>LKR {{ number_format($otherRevenue, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-rose-600">
                        <span>Less: Sales Discounts & Concessions</span>
                        <span>- LKR {{ number_format($salesDiscounts, 2) }}</span>
                    </div>
                    <div class="flex justify-between font-bold border-t border-gray-200 pt-1 text-gray-900">
                        <span>NET REVENUE</span>
                        <span>LKR {{ number_format($netRevenue, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. Cost of Sales -->
            <div class="border border-gray-300 rounded p-3">
                <div class="font-bold border-b border-gray-300 pb-1 text-[#0B1849]">2. COST OF SALES</div>
                <div class="mt-2 space-y-1">
                    <div class="flex justify-between">
                        <span>Product Cost of Goods Sold (FIFO Batches)</span>
                        <span>LKR {{ number_format($cogsProduct, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Direct Service Costs</span>
                        <span>LKR {{ number_format($directServiceCosts, 2) }}</span>
                    </div>
                    <div class="flex justify-between font-bold border-t border-gray-200 pt-1 text-gray-900">
                        <span>TOTAL COST OF SALES</span>
                        <span>LKR {{ number_format($totalCostOfSales, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Gross Profit -->
            <div class="bg-gray-100 border border-gray-300 rounded p-3 flex justify-between font-bold text-sm">
                <span>GROSS OPERATING PROFIT (Margin: {{ number_format($grossProfitMargin, 2) }}%)</span>
                <span class="{{ $grossProfit >= 0 ? 'text-emerald-800' : 'text-rose-700' }}">LKR {{ number_format($grossProfit, 2) }}</span>
            </div>

            <!-- 3. Operating Expenses -->
            <div class="border border-gray-300 rounded p-3">
                <div class="font-bold border-b border-gray-300 pb-1 text-[#0B1849]">3. OPERATING EXPENSES</div>
                <div class="mt-2 space-y-1">
                    <div class="flex justify-between">
                        <span>Employee Salaries & Wages</span>
                        <span>LKR {{ number_format($salaryExpenses, 2) }}</span>
                    </div>
                    @foreach($categorizedExpenses as $cat => $amt)
                        <div class="flex justify-between">
                            <span>{{ $cat }}</span>
                            <span>LKR {{ number_format($amt, 2) }}</span>
                        </div>
                    @endforeach
                    <div class="flex justify-between font-bold border-t border-gray-200 pt-1 text-gray-900">
                        <span>TOTAL OPERATING EXPENSES</span>
                        <span>LKR {{ number_format($totalOperatingExpenses, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- 4. Net Operating Profit -->
            <div class="border-2 {{ $netProfit >= 0 ? 'border-emerald-600 bg-emerald-50' : 'border-rose-600 bg-rose-50' }} rounded p-4 flex justify-between font-bold text-base">
                <span>NET OPERATING PROFIT / (LOSS) (Margin: {{ number_format($netProfitMargin, 2) }}%)</span>
                <span class="{{ $netProfit >= 0 ? 'text-emerald-800' : 'text-rose-700' }}">LKR {{ number_format($netProfit, 2) }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="pt-6 border-t border-gray-300 flex justify-between text-[11px] text-gray-500">
            <span>Prepared according to FIFO inventory valuation rules. Confidential internal management document.</span>
            <span>AutoCare System - Financial Statements</span>
        </div>
    </div>
</body>
</html>
