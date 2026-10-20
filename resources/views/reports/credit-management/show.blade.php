@extends('layouts.app')

@section('title', 'Credit Account - ' . $customer->display_name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold text-[#0B1849] tracking-tight">{{ $customer->display_name }}</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $customer->isCompany() ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $customer->isCompany() ? 'Corporate Fleet Account' : 'Individual Credit' }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-1">Contact: {{ $customer->phone ?? 'N/A' }} | Email: {{ $customer->email ?? 'N/A' }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a 
                href="{{ route('reports.credit-management.statement', $creditAccount) }}" 
                class="inline-flex items-center px-3.5 py-2 bg-[#0B1849] text-xs font-semibold text-white rounded-lg hover:bg-[#0B1849]/90 transition shadow-xs"
            >
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Monthly Statement
            </a>
            @if($creditAccount->outstanding_balance > 0)
                <a 
                    href="{{ route('billing.multiple-invoice-payment.index', ['customer_id' => $customer->id]) }}" 
                    class="inline-flex items-center px-3.5 py-2 bg-emerald-600 text-xs font-semibold text-white rounded-lg hover:bg-emerald-700 transition shadow-xs"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Record Payment
                </a>
            @endif
            <a href="{{ route('reports.credit-management.index') }}" class="px-3 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                Back
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Credit Limit</span>
            <div class="text-xl font-bold text-[#0B1849] mt-1">LKR {{ number_format($creditAccount->credit_limit, 2) }}</div>
            <div class="text-[11px] text-gray-400 mt-0.5">Terms: {{ $creditAccount->payment_terms_days }} Days</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Outstanding Balance</span>
            <div class="text-xl font-bold {{ $creditAccount->outstanding_balance > (float)$creditAccount->credit_limit && $creditAccount->credit_limit > 0 ? 'text-rose-600' : 'text-amber-700' }} mt-1">
                LKR {{ number_format($creditAccount->outstanding_balance, 2) }}
            </div>
            <div class="text-[11px] text-gray-400 mt-0.5">Opening: LKR {{ number_format($creditAccount->opening_balance, 2) }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Available Credit</span>
            <div class="text-xl font-bold {{ $creditAccount->available_credit > 0 ? 'text-emerald-700' : 'text-rose-500' }} mt-1">
                LKR {{ number_format($creditAccount->available_credit, 2) }}
            </div>
            <div class="text-[11px] text-gray-400 mt-0.5">Safe spending headroom</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Fleet Strength</span>
            <div class="text-xl font-bold text-blue-700 mt-1">{{ $customer->vehicles->count() }} Vehicles</div>
            <div class="text-[11px] text-gray-400 mt-0.5">Billed under this account</div>
        </div>
    </div>

    <!-- Aging Breakdown Strip -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
        <span class="text-xs font-bold uppercase tracking-wider text-gray-500 block mb-2">Invoice Due Date Aging</span>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-center text-xs">
            <div class="p-2 rounded-lg bg-emerald-50 text-emerald-900 border border-emerald-100">
                <span class="text-[10px] uppercase font-bold block">Current</span>
                <span class="font-bold text-sm">LKR {{ number_format($aging['current'], 2) }}</span>
            </div>
            <div class="p-2 rounded-lg bg-blue-50 text-blue-900 border border-blue-100">
                <span class="text-[10px] uppercase font-bold block">1–30 Days</span>
                <span class="font-bold text-sm">LKR {{ number_format($aging['days_1_30'], 2) }}</span>
            </div>
            <div class="p-2 rounded-lg bg-amber-50 text-amber-900 border border-amber-100">
                <span class="text-[10px] uppercase font-bold block">31–60 Days</span>
                <span class="font-bold text-sm">LKR {{ number_format($aging['days_31_60'], 2) }}</span>
            </div>
            <div class="p-2 rounded-lg bg-orange-50 text-orange-900 border border-orange-100">
                <span class="text-[10px] uppercase font-bold block">61–90 Days</span>
                <span class="font-bold text-sm">LKR {{ number_format($aging['days_61_90'], 2) }}</span>
            </div>
            <div class="p-2 rounded-lg bg-rose-50 text-rose-900 border border-rose-100">
                <span class="text-[10px] uppercase font-bold block">> 90 Days</span>
                <span class="font-bold text-sm">LKR {{ number_format($aging['over_90'], 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Section 8.2: Registered Fleet Vehicles -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-[#0B1849]">Registered Company Fleet Vehicles ({{ $customer->vehicles->count() }})</span>
                <span class="text-xs text-gray-500 block mt-0.5">Services performed on all fleet vehicles are unified under this credit account.</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase tracking-wider">
                        <th class="py-2.5 px-4">Vehicle Plate #</th>
                        <th class="py-2.5 px-4">Make & Model</th>
                        <th class="py-2.5 px-4">Type</th>
                        <th class="py-2.5 px-4 text-center">Recorded Mileage</th>
                        <th class="py-2.5 px-4 text-center">Completed Visits</th>
                        <th class="py-2.5 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($customer->vehicles as $v)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-2.5 px-4 font-mono font-bold text-[#0B1849]">
                                {{ $v->vehicle_number }}
                            </td>
                            <td class="py-2.5 px-4 text-gray-800">
                                {{ $v->make_model ?? 'Vehicle' }} {{ $v->year_of_manufacture ? '('.$v->year_of_manufacture.')' : '' }}
                            </td>
                            <td class="py-2.5 px-4 text-gray-600">
                                {{ $v->vehicleType?->name ?? 'Standard' }}
                            </td>
                            <td class="py-2.5 px-4 text-center font-mono">
                                {{ $v->current_mileage ? number_format($v->current_mileage).' km' : '-' }}
                            </td>
                            <td class="py-2.5 px-4 text-center font-bold text-gray-800">
                                {{ $v->jobCards()->where('status', '!=', 'cancelled')->count() }}
                            </td>
                            <td class="py-2.5 px-4 text-center">
                                <a 
                                    href="{{ route('reports.vehicle-service.index', ['vehicle_id' => $v->id, 'search' => $v->vehicle_number]) }}" 
                                    class="text-xs text-blue-600 hover:underline font-medium"
                                >
                                    Service History
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500 text-xs">
                                No registered vehicles found for this account.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Outstanding & Recent Invoices -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-[#0B1849]">Invoices & Credit Charges</span>
            <span class="text-xs text-gray-500">{{ $invoices->count() }} invoices on record</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase tracking-wider">
                        <th class="py-2.5 px-4">Invoice Date</th>
                        <th class="py-2.5 px-4">Due Date</th>
                        <th class="py-2.5 px-4">Invoice #</th>
                        <th class="py-2.5 px-4">Vehicle</th>
                        <th class="py-2.5 px-4 text-right">Total Amount</th>
                        <th class="py-2.5 px-4 text-right">Amount Paid</th>
                        <th class="py-2.5 px-4 text-right">Balance Due</th>
                        <th class="py-2.5 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-2.5 px-4 text-gray-600">{{ $inv->invoice_date?->format('d M Y') }}</td>
                            <td class="py-2.5 px-4 text-gray-500">{{ $inv->due_date?->format('d M Y') ?? '-' }}</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-[#0B1849]">
                                <a href="{{ route('billing.invoices.show', $inv) }}" class="hover:underline">
                                    {{ $inv->invoice_number }}
                                </a>
                            </td>
                            <td class="py-2.5 px-4 font-mono text-gray-700">
                                {{ $inv->jobCard?->vehicle?->vehicle_number ?? '-' }}
                            </td>
                            <td class="py-2.5 px-4 text-right font-medium text-gray-900">
                                LKR {{ number_format($inv->grand_total, 2) }}
                            </td>
                            <td class="py-2.5 px-4 text-right font-medium text-emerald-700">
                                LKR {{ number_format($inv->amount_paid, 2) }}
                            </td>
                            <td class="py-2.5 px-4 text-right font-bold {{ $inv->balance_amount > 0 ? 'text-rose-600' : 'text-gray-400' }}">
                                LKR {{ number_format($inv->balance_amount, 2) }}
                            </td>
                            <td class="py-2.5 px-4 text-center">
                                @if($inv->balance_amount <= 0.0001)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">
                                        Settled
                                    </span>
                                @elseif($inv->amount_paid > 0.0001)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700">
                                        Partial
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700">
                                        Unpaid
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-6 text-center text-gray-500 text-xs">
                                No invoices found for this account.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payments Ledger -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-[#0B1849]">Payment History</span>
            <span class="text-xs text-gray-500">{{ $payments->count() }} settlements</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold uppercase tracking-wider">
                        <th class="py-2.5 px-4">Payment Date</th>
                        <th class="py-2.5 px-4">Receipt / Pay #</th>
                        <th class="py-2.5 px-4">Invoice #</th>
                        <th class="py-2.5 px-4">Method</th>
                        <th class="py-2.5 px-4">Reference</th>
                        <th class="py-2.5 px-4 text-right">Amount Settled</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($payments as $p)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-2.5 px-4 text-gray-600">{{ $p->payment_date?->format('d M Y') }}</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-[#0B1849]">{{ $p->payment_number }}</td>
                            <td class="py-2.5 px-4 font-mono text-gray-700">{{ $p->invoice?->invoice_number ?? 'Multi-Invoice' }}</td>
                            <td class="py-2.5 px-4 capitalize">{{ str_replace('_', ' ', $p->payment_method) }}</td>
                            <td class="py-2.5 px-4 font-mono text-gray-500">{{ $p->reference_number ?? '-' }}</td>
                            <td class="py-2.5 px-4 text-right font-bold text-emerald-700">LKR {{ number_format($p->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500 text-xs">No payments recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Admin Account Settings Update -->
    @if(Auth::user()->isAdmin())
        <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-5 max-w-2xl">
            <h3 class="text-xs font-bold uppercase tracking-wider text-[#0B1849] mb-3">Update Credit Terms (Admin)</h3>
            <form method="POST" action="{{ route('reports.credit-management.update', $creditAccount) }}" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Credit Limit (LKR)</label>
                        <input type="number" step="0.01" min="0" name="credit_limit" value="{{ old('credit_limit', $creditAccount->credit_limit) }}" required class="w-full text-xs rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Payment Terms (Days)</label>
                        <input type="number" min="0" max="365" name="payment_terms_days" value="{{ old('payment_terms_days', $creditAccount->payment_terms_days) }}" required class="w-full text-xs rounded-lg border-gray-300">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Opening Balance (LKR)</label>
                        <input type="number" step="0.01" min="0" name="opening_balance" value="{{ old('opening_balance', $creditAccount->opening_balance) }}" class="w-full text-xs rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Account Status</label>
                        <select name="status" class="w-full text-xs rounded-lg border-gray-300">
                            <option value="active" {{ $creditAccount->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="suspended" {{ $creditAccount->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="closed" {{ $creditAccount->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-600 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full text-xs rounded-lg border-gray-300">{{ old('notes', $creditAccount->notes) }}</textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-[#0B1849] text-white font-semibold rounded-lg hover:bg-[#0B1849]/90 transition">
                        Update Settings
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
@endsection
