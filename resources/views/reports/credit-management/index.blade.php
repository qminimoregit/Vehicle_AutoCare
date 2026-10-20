@extends('layouts.app')

@section('title', 'Credit Management')

@section('content')
<div class="space-y-6">
    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#0B1849] tracking-tight">Credit Management</h1>
            <p class="text-sm text-gray-500 mt-1">Manage individual and corporate fleet credit accounts, credit limits, aging and monthly statements.</p>
        </div>
        <div class="flex items-center gap-2">
            @if(Auth::user()->isAdmin())
                <a 
                    href="{{ route('reports.credit-management.create') }}" 
                    class="inline-flex items-center px-4 py-2 bg-[#0B1849] text-xs font-semibold text-white rounded-lg hover:bg-[#0B1849]/90 transition shadow-xs"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Register Credit Account
                </a>
            @endif
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Approved Credit</span>
            <div class="text-xl font-bold text-[#0B1849] mt-1">LKR {{ number_format($totalCreditLimit, 2) }}</div>
            <div class="text-[11px] text-gray-400 mt-0.5">{{ $activeAccountsCount }} active accounts</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Outstanding Balance</span>
            <div class="text-xl font-bold text-rose-600 mt-1">LKR {{ number_format($totalOutstanding, 2) }}</div>
            <div class="text-[11px] text-rose-500 mt-0.5">Total customer receivables</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining Available Credit</span>
            <div class="text-xl font-bold text-emerald-700 mt-1">LKR {{ number_format($totalAvailableCredit, 2) }}</div>
            <div class="text-[11px] text-emerald-600 mt-0.5">Safe credit headroom</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Over 30 Days Overdue</span>
            <div class="text-xl font-bold text-amber-700 mt-1">
                LKR {{ number_format($overallAging['days_31_60'] + $overallAging['days_61_90'] + $overallAging['over_90'], 2) }}
            </div>
            <div class="text-[11px] text-amber-600 mt-0.5">Requires collection follow-up</div>
        </div>
    </div>

    <!-- Credit Aging Analysis Panel -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
        <div class="flex items-center justify-between border-b border-gray-100 pb-2 mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-[#0B1849]">Credit Aging Breakdown (Based on Due Dates)</span>
            <span class="text-xs text-gray-400">Total: LKR {{ number_format($totalOutstanding, 2) }}</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-center text-xs">
            <div class="p-2.5 rounded-lg bg-emerald-50 border border-emerald-100">
                <span class="text-[11px] text-emerald-800 font-medium block">Current / Not Due</span>
                <span class="text-sm font-bold text-emerald-900 mt-0.5 block">LKR {{ number_format($overallAging['current'], 2) }}</span>
            </div>
            <div class="p-2.5 rounded-lg bg-blue-50 border border-blue-100">
                <span class="text-[11px] text-blue-800 font-medium block">1–30 Days Overdue</span>
                <span class="text-sm font-bold text-blue-900 mt-0.5 block">LKR {{ number_format($overallAging['days_1_30'], 2) }}</span>
            </div>
            <div class="p-2.5 rounded-lg bg-amber-50 border border-amber-100">
                <span class="text-[11px] text-amber-800 font-medium block">31–60 Days Overdue</span>
                <span class="text-sm font-bold text-amber-900 mt-0.5 block">LKR {{ number_format($overallAging['days_31_60'], 2) }}</span>
            </div>
            <div class="p-2.5 rounded-lg bg-orange-50 border border-orange-100">
                <span class="text-[11px] text-orange-800 font-medium block">61–90 Days Overdue</span>
                <span class="text-sm font-bold text-orange-900 mt-0.5 block">LKR {{ number_format($overallAging['days_61_90'], 2) }}</span>
            </div>
            <div class="p-2.5 rounded-lg bg-rose-50 border border-rose-100">
                <span class="text-[11px] text-rose-800 font-medium block">Over 90 Days Overdue</span>
                <span class="text-sm font-bold text-rose-900 mt-0.5 block">LKR {{ number_format($overallAging['over_90'], 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
        <form method="GET" action="{{ route('reports.credit-management.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <!-- Search -->
            <div class="sm:col-span-2">
                <label for="search" class="block text-xs font-semibold text-gray-600 mb-1">Search Customer / Company / Phone</label>
                <input 
                    type="text" 
                    id="search" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Search name, company, phone..." 
                    class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]"
                >
            </div>

            <!-- Type -->
            <div>
                <label for="type" class="block text-xs font-semibold text-gray-600 mb-1">Account Type</label>
                <select id="type" name="type" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0B1849] focus:ring-1 focus:ring-[#0B1849]">
                    <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Accounts</option>
                    <option value="company" {{ $type === 'company' ? 'selected' : '' }}>Company Fleets</option>
                    <option value="individual" {{ $type === 'individual' ? 'selected' : '' }}>Individual Customers</option>
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-[#0B1849] text-white text-xs font-semibold rounded-lg hover:bg-[#0B1849]/90 transition">
                    Filter
                </button>
                <a href="{{ route('reports.credit-management.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Credit Accounts Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase tracking-wider">
                        <th class="py-3 px-4">Customer / Company</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4 text-center">Fleet Vehicles</th>
                        <th class="py-3 px-4 text-right">Credit Limit</th>
                        <th class="py-3 px-4 text-right">Outstanding</th>
                        <th class="py-3 px-4 text-right">Available Credit</th>
                        <th class="py-3 px-4 text-center">Payment Terms</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($accounts as $acc)
                        @php
                            $cust = $acc->customer;
                            $outstanding = $acc->outstanding_balance;
                            $available = $acc->available_credit;
                            $limit = (float)$acc->credit_limit;
                            $isOverLimit = ($limit > 0 && $outstanding > $limit);
                            $fleetCount = $cust ? $cust->vehicles->count() : 0;
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4">
                                <a href="{{ route('reports.credit-management.show', $acc) }}" class="font-bold text-[#0B1849] hover:underline block">
                                    {{ $cust?->display_name ?? 'Customer' }}
                                </a>
                                <span class="text-[11px] text-gray-500 font-mono">{{ $cust?->phone }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ $cust?->isCompany() ? 'bg-purple-50 text-purple-800' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $cust?->isCompany() ? 'Company Fleet' : 'Individual' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($fleetCount > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-blue-700">
                                        {{ $fleetCount }} {{ Str::plural('vehicle', $fleetCount) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">0</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-medium text-gray-900">
                                LKR {{ number_format($limit, 2) }}
                            </td>
                            <td class="py-3 px-4 text-right font-bold {{ $outstanding > 0 ? ($isOverLimit ? 'text-rose-600' : 'text-amber-700') : 'text-gray-400' }}">
                                LKR {{ number_format($outstanding, 2) }}
                            </td>
                            <td class="py-3 px-4 text-right font-bold {{ $available > 0 ? 'text-emerald-700' : 'text-rose-500' }}">
                                LKR {{ number_format($available, 2) }}
                            </td>
                            <td class="py-3 px-4 text-center text-gray-600">
                                {{ $acc->payment_terms_days }} Days
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($acc->isActive())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                                        Active
                                    </span>
                                @elseif($acc->isSuspended())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700">
                                        Suspended
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700">
                                        Closed
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a 
                                        href="{{ route('reports.credit-management.show', $acc) }}" 
                                        class="px-2.5 py-1 text-xs font-semibold text-[#0B1849] bg-blue-50 hover:bg-blue-100 rounded transition"
                                        title="View Account Details & Fleet"
                                    >
                                        Details
                                    </a>
                                    <a 
                                        href="{{ route('reports.credit-management.statement', $acc) }}" 
                                        class="px-2 py-1 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded transition"
                                        title="Monthly Statement"
                                    >
                                        Statement
                                    </a>
                                    @if($outstanding > 0)
                                        <a 
                                            href="{{ route('billing.multiple-invoice-payment.index', ['customer_id' => $acc->customer_id]) }}" 
                                            class="px-2 py-1 text-xs font-semibold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 rounded transition"
                                            title="Record Payment"
                                        >
                                            Pay
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-10 text-center text-gray-500 text-xs">
                                No registered credit accounts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
