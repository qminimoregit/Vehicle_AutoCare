@extends('layouts.app')

@section('title', 'Profit & Loss Statement')

@section('content')
<div class="space-y-6">
    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold text-[#0B1849] tracking-tight">Profit & Loss Statement</h1>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-purple-100 text-purple-800">
                    Executive Financial Summary
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-1">Accrual-based financial performance with FIFO batch cost accounting.</p>
        </div>
        <div class="flex items-center gap-2">
            <a 
                href="{{ route('reports.profit-loss.print', request()->query()) }}" 
                target="_blank" 
                class="inline-flex items-center px-3.5 py-2 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition shadow-xs"
            >
                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Statement
            </a>
            <a 
                href="{{ route('reports.profit-loss.export', request()->query()) }}" 
                class="inline-flex items-center px-3.5 py-2 rounded-lg bg-[#0B1849] text-xs font-semibold text-white hover:bg-[#0B1849]/90 transition shadow-xs"
            >
                <svg class="w-4 h-4 mr-1.5 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Net Sales Revenue</span>
            <div class="text-lg font-bold text-[#0B1849] mt-1">LKR {{ number_format($netRevenue, 2) }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Cost of Goods Sold</span>
            <div class="text-lg font-bold text-gray-700 mt-1">LKR {{ number_format($totalCostOfSales, 2) }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Profit</span>
            <div class="text-lg font-bold {{ $grossProfit >= 0 ? 'text-emerald-700' : 'text-rose-600' }} mt-1">
                LKR {{ number_format($grossProfit, 2) }}
            </div>
            <div class="text-[11px] text-gray-400 mt-0.5">{{ number_format($grossProfitMargin, 1) }}% Margin</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Operating Expenses</span>
            <div class="text-lg font-bold text-gray-700 mt-1">LKR {{ number_format($totalOperatingExpenses, 2) }}</div>
            <div class="text-[11px] text-gray-400 mt-0.5">Salaries + Utilities + Repairs</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Net Operating Profit</span>
            <div class="text-lg font-bold {{ $netProfit >= 0 ? 'text-emerald-700' : 'text-rose-600' }} mt-1">
                LKR {{ number_format($netProfit, 2) }}
            </div>
            <div class="text-[11px] {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-rose-500' }} font-semibold mt-0.5">
                {{ number_format($netProfitMargin, 1) }}% Net Margin
            </div>
        </div>
    </div>

    <!-- Date Range & Branch Filters -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
        <form method="GET" action="{{ route('reports.profit-loss.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-end">
            <!-- Date From -->
            <div>
                <label for="date_from" class="block text-xs font-semibold text-gray-600 mb-1">Period From</label>
                <input 
                    type="date" 
                    id="date_from" 
                    name="date_from" 
                    value="{{ $dateFrom }}" 
                    class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]"
                >
            </div>

            <!-- Date To -->
            <div>
                <label for="date_to" class="block text-xs font-semibold text-gray-600 mb-1">Period To</label>
                <input 
                    type="date" 
                    id="date_to" 
                    name="date_to" 
                    value="{{ $dateTo }}" 
                    class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]"
                >
            </div>

            <!-- Branch -->
            <div>
                <label for="branch_id" class="block text-xs font-semibold text-gray-600 mb-1">Branch</label>
                <select id="branch_id" name="branch_id" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]">
                    <option value="all" {{ $branchId === 'all' ? 'selected' : '' }}>All Branches (Company-wide)</option>
                    @foreach($accessibleBranches as $b)
                        <option value="{{ $b->id }}" {{ (string)$branchId === (string)$b->id ? 'selected' : '' }}>
                            {{ $b->branch_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-[#0B1849] text-white text-xs font-semibold rounded-lg hover:bg-[#0B1849]/90 transition">
                    Generate
                </button>
                <a href="{{ route('reports.profit-loss.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                    Current Month
                </a>
            </div>
        </form>
    </div>

    <!-- Structured Statement Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-6 md:p-8 max-w-4xl mx-auto space-y-6">
        <!-- Statement Title Block -->
        <div class="text-center border-b border-gray-200 pb-5">
            <h2 class="text-xl font-bold text-[#0B1849]">AutoCare Service Center</h2>
            <div class="text-sm font-semibold text-gray-700 mt-1">Income & Expenditure Statement (Profit & Loss)</div>
            <div class="text-xs text-gray-500 mt-1">
                For the period: <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}</span> to <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</span>
                @if($selectedBranch)
                    | Branch: <span class="font-medium text-gray-800">{{ $selectedBranch->branch_name }}</span>
                @else
                    | Scope: <span class="font-medium text-gray-800">All Operating Branches</span>
                @endif
            </div>
        </div>

        <!-- 1. REVENUE SECTION -->
        <div>
            <div class="text-xs font-bold text-[#0B1849] uppercase tracking-wider pb-2 border-b border-gray-200 flex justify-between">
                <span>1. Operating Revenue</span>
                <span>Amount (LKR)</span>
            </div>
            <div class="divide-y divide-gray-100 text-xs">
                <div class="py-2.5 flex justify-between pl-4 text-gray-700">
                    <span>Direct Sales Revenue (POS Counter)</span>
                    <span class="font-medium">LKR {{ number_format($directSalesRevenue, 2) }}</span>
                </div>
                <div class="py-2.5 flex justify-between pl-4 text-gray-700">
                    <span>Service Sales Revenue (Job Cards & Workshop)</span>
                    <span class="font-medium">LKR {{ number_format($serviceSalesRevenue, 2) }}</span>
                </div>
                @if($otherRevenue > 0)
                    <div class="py-2.5 flex justify-between pl-4 text-gray-700">
                        <span>Other Operating Invoiced Revenue</span>
                        <span class="font-medium">LKR {{ number_format($otherRevenue, 2) }}</span>
                    </div>
                @endif
                <div class="py-2.5 flex justify-between pl-4 text-rose-600">
                    <span>Less: Customer Discounts & Concessions</span>
                    <span class="font-medium">- LKR {{ number_format($salesDiscounts, 2) }}</span>
                </div>
                <div class="py-3 flex justify-between font-bold text-[#0B1849] bg-slate-50/70 px-2 rounded-lg">
                    <span>NET OPERATING REVENUE</span>
                    <span>LKR {{ number_format($netRevenue, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- 2. COST OF SALES SECTION -->
        <div>
            <div class="text-xs font-bold text-[#0B1849] uppercase tracking-wider pb-2 border-b border-gray-200 flex justify-between">
                <span>2. Cost of Sales (Direct Production Costs)</span>
                <span>Amount (LKR)</span>
            </div>
            <div class="divide-y divide-gray-100 text-xs">
                <div class="py-2.5 flex justify-between pl-4 text-gray-700">
                    <div>
                        <span class="font-medium">Product Cost of Goods Sold (FIFO Batches)</span>
                        <div class="text-[11px] text-gray-400">Actual unit cost at purchase, less cancellations</div>
                    </div>
                    <span class="font-medium">LKR {{ number_format($cogsProduct, 2) }}</span>
                </div>
                <div class="py-2.5 flex justify-between pl-4 text-gray-700">
                    <div>
                        <span class="font-medium">Direct Service Labor & Sub-contracted Costs</span>
                        <div class="text-[11px] text-gray-400">Directly attributed subcontracting charges</div>
                    </div>
                    <span class="font-medium">LKR {{ number_format($directServiceCosts, 2) }}</span>
                </div>
                <div class="py-3 flex justify-between font-bold text-gray-900 bg-slate-50/70 px-2 rounded-lg">
                    <span>TOTAL COST OF SALES</span>
                    <span>LKR {{ number_format($totalCostOfSales, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- GROSS PROFIT CALLOUT -->
        <div class="p-3.5 bg-blue-50 border border-blue-200 rounded-xl flex justify-between items-center text-sm font-bold text-[#0B1849]">
            <div>
                <span>GROSS OPERATING PROFIT</span>
                <span class="text-xs text-blue-700 block font-normal mt-0.5">Margin: {{ number_format($grossProfitMargin, 2) }}%</span>
            </div>
            <span class="text-base {{ $grossProfit >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                LKR {{ number_format($grossProfit, 2) }}
            </span>
        </div>

        <!-- 3. OPERATING EXPENSES SECTION -->
        <div>
            <div class="text-xs font-bold text-[#0B1849] uppercase tracking-wider pb-2 border-b border-gray-200 flex justify-between">
                <span>3. Operating Expenses</span>
                <span>Amount (LKR)</span>
            </div>
            <div class="divide-y divide-gray-100 text-xs">
                <div class="py-2.5 flex justify-between pl-4 text-gray-700">
                    <div>
                        <span class="font-medium">Employee Salaries & Wages</span>
                        <div class="text-[11px] text-gray-400">Approved payroll (Salary advances excluded from double-counting)</div>
                    </div>
                    <span class="font-medium">LKR {{ number_format($salaryExpenses, 2) }}</span>
                </div>
                @foreach($categorizedExpenses as $cat => $amt)
                    <div class="py-2.5 flex justify-between pl-4 text-gray-700">
                        <span>{{ $cat }}</span>
                        <span class="font-medium">LKR {{ number_format($amt, 2) }}</span>
                    </div>
                @endforeach
                <div class="py-3 flex justify-between font-bold text-gray-900 bg-slate-50/70 px-2 rounded-lg">
                    <span>TOTAL OPERATING EXPENSES</span>
                    <span>LKR {{ number_format($totalOperatingExpenses, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- 4. NET OPERATING PROFIT / LOSS -->
        <div class="p-4 {{ $netProfit >= 0 ? 'bg-emerald-50 border-emerald-300 text-emerald-900' : 'bg-rose-50 border-rose-300 text-rose-900' }} border-2 rounded-xl flex justify-between items-center">
            <div>
                <span class="text-sm font-bold tracking-tight uppercase">Net Operating Profit / (Loss)</span>
                <span class="text-xs block text-gray-600 mt-0.5">Net Profit Margin: {{ number_format($netProfitMargin, 2) }}%</span>
            </div>
            <div class="text-xl font-bold {{ $netProfit >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                LKR {{ number_format($netProfit, 2) }}
            </div>
        </div>

        <!-- Note on Standards -->
        <div class="pt-4 border-t border-gray-200 text-[11px] text-gray-500 space-y-1 leading-relaxed">
            <p><span class="font-semibold">Accounting Compliance:</span> COGS uses exact FIFO batch purchase costs allocated at stock issue. Inventory purchases are capitalized in inventory asset batches and not expensed until sold. Salary advances and raw supplier payments are excluded to prevent duplicate cost recognition.</p>
        </div>
    </div>
</div>
@endsection
