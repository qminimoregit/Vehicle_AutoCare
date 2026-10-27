@extends('layouts.app')

@section('title', 'Salary Advances')

@section('content')
<div 
    class="space-y-6"
    x-data="{
        showRegisterModal: false,
        showDisburseModal: false,
        showRejectModal: false,
        showCancelModal: false,
        showReverseModal: false,
        showHistoryModal: false,

        selectedAdvance: null,
        historyData: null,
        loadingHistory: false,

        openDisburse(adv) {
            this.selectedAdvance = adv;
            this.showDisburseModal = true;
        },
        openReject(adv) {
            this.selectedAdvance = adv;
            this.showRejectModal = true;
        },
        openCancel(adv) {
            this.selectedAdvance = adv;
            this.showCancelModal = true;
        },
        openReverse(adv) {
            this.selectedAdvance = adv;
            this.showReverseModal = true;
        },
        openHistory(adv) {
            this.selectedAdvance = adv;
            this.loadingHistory = true;
            this.showHistoryModal = true;
            this.historyData = null;

            fetch(`/payroll/advances/${adv.id}/history`)
                .then(res => res.json())
                .then(data => {
                    this.historyData = data;
                    this.loadingHistory = false;
                })
                .catch(() => {
                    this.loadingHistory = false;
                });
        }
    }"
>
    <!-- Header -->
    <x-page-header 
        title="Salary Advances" 
        description="Manage employee salary advances, audited approvals, disbursements, and multi-month payroll recoveries."
    >
        <x-slot:actions>
            @if(Auth::user()->isAdmin())
                <button 
                    type="button" 
                    @click="showRegisterModal = true"
                    class="inline-flex items-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Register Advance
                </button>
            @endif
        </x-slot:actions>
    </x-page-header>

    @if(! \Illuminate\Support\Facades\Schema::hasTable('employee_advance_recoveries'))
        <!-- Migration Notice Banner -->
        <div class="bg-amber-50 border-l-4 border-amber-500 rounded-r-xl p-4 shadow-xs">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-amber-600 mr-3 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h4 class="text-sm font-bold text-amber-900">Database Migration Prepared</h4>
                    <p class="text-xs text-amber-800 mt-1">
                        Salary Advance upgrade migration is prepared at 
                        <code class="bg-amber-100/80 px-1 py-0.5 rounded font-mono text-[11px]">database/migrations/2026_10_27_093000_create_salary_advance_management_tables.php</code>.
                        Once you run <code class="bg-amber-100/80 px-1 py-0.5 rounded font-mono text-[11px]">php artisan migrate</code> on your server, multi-month partial recovery auditing and approval tracking will be enabled in your database.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 block">Total Disbursed</span>
            <span class="text-xl font-bold text-gray-900 font-mono mt-1 block">LKR {{ number_format($totalDisbursed, 2) }}</span>
            <span class="text-[11px] text-gray-400 block mt-0.5">Cumulative disbursed advances</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 block">Amount Recovered</span>
            <span class="text-xl font-bold text-emerald-700 font-mono mt-1 block">LKR {{ number_format($totalRecovered, 2) }}</span>
            <span class="text-[11px] text-gray-400 block mt-0.5">Deducted through approved payroll</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 block">Outstanding Balance</span>
            <span class="text-xl font-bold text-amber-600 font-mono mt-1 block">LKR {{ number_format($outstandingBalance, 2) }}</span>
            <span class="text-[11px] text-gray-400 block mt-0.5">Active advances awaiting recovery</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 block">Pending Approvals</span>
            <span class="text-xl font-bold text-[#0B1849] font-mono mt-1 block">{{ $pendingApprovalsCount }}</span>
            <span class="text-[11px] text-gray-400 block mt-0.5">Awaiting admin review</span>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-4">
        <form method="GET" action="{{ route('payroll.advances.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
            <!-- Employee Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Employee</label>
                <x-searchable-select 
                    name="employee_id" 
                    :value="request('employee_id')" 
                    placeholder="All Employees" 
                    searchPlaceholder="Search employee name or mobile..."
                    :endpoint="route('search.employees')" 
                    :clearable="true" 
                />
            </div>

            <!-- Month Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Month</label>
                <input 
                    type="month" 
                    name="month" 
                    value="{{ request('month') }}"
                    class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]"
                >
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                <select name="status" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]">
                    <option value="all">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="disbursed" {{ request('status') === 'disbursed' ? 'selected' : '' }}>Disbursed</option>
                    <option value="partially_recovered" {{ request('status') === 'partially_recovered' ? 'selected' : '' }}>Partially Recovered</option>
                    <option value="deducted" {{ request('status') === 'deducted' ? 'selected' : '' }}>Fully Deducted</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-semibold rounded-lg transition text-center cursor-pointer">
                    Apply Filter
                </button>
                <a href="{{ route('payroll.advances.index') }}" class="py-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Advances Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Salary Advances List</h3>
            <span class="text-xs text-gray-500">Showing {{ $advances->count() }} of {{ $advances->total() }} records</span>
        </div>

        @if($advances->isEmpty())
            <div class="py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h4 class="mt-2 text-sm font-bold text-gray-900">No Salary Advances Found</h4>
                <p class="mt-1 text-xs text-gray-500">No advances match your filter criteria or have been recorded yet.</p>
            </div>
        @else
            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-600 font-semibold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4">Advance #</th>
                            <th class="py-3 px-4">Employee</th>
                            <th class="py-3 px-4">Advance Date</th>
                            <th class="py-3 px-4 text-right">Advance Amount</th>
                            <th class="py-3 px-4 text-right">Recovered</th>
                            <th class="py-3 px-4 text-right">Remaining Balance</th>
                            <th class="py-3 px-4">Method</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($advances as $adv)
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="py-3 px-4 font-mono font-bold text-[#0B1849]">
                                    {{ $adv->advance_number }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-gray-900">{{ $adv->employee->name }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $adv->employee->employee_number }}</div>
                                </td>
                                <td class="py-3 px-4 text-gray-700 whitespace-nowrap">
                                    {{ $adv->advance_date->format('d M Y') }}
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-gray-900 text-right">
                                    LKR {{ number_format($adv->amount, 2) }}
                                    @if($adv->monthly_installment > 0)
                                        <div class="text-[10px] text-blue-600 font-normal">
                                            LKR {{ number_format($adv->monthly_installment, 2) }}/mo
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-emerald-700 text-right">
                                    LKR {{ number_format($adv->recovered_amount, 2) }}
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-right {{ $adv->remaining_balance > 0 ? 'text-amber-600' : 'text-gray-400' }}">
                                    LKR {{ number_format($adv->remaining_balance, 2) }}
                                </td>
                                <td class="py-3 px-4 capitalize text-gray-700">
                                    {{ str_replace('_', ' ', $adv->payment_method) }}
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    @if($adv->status === 'pending')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800">
                                            Pending
                                        </span>
                                    @elseif($adv->status === 'approved')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">
                                            Approved
                                        </span>
                                    @elseif($adv->status === 'disbursed')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-100 text-indigo-800">
                                            Disbursed
                                        </span>
                                    @elseif($adv->status === 'partially_recovered')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-purple-100 text-purple-800">
                                            Partially Recovered
                                        </span>
                                    @elseif($adv->status === 'deducted')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                                            Deducted
                                        </span>
                                    @elseif($adv->status === 'rejected')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-red-100 text-red-800">
                                            Rejected
                                        </span>
                                    @elseif($adv->status === 'cancelled')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700">
                                            Cancelled
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- View History / Details -->
                                        <button 
                                            type="button" 
                                            @click="openHistory({{ $adv }})"
                                            class="p-1 text-gray-500 hover:text-[#0B1849] hover:bg-gray-100 rounded transition cursor-pointer"
                                            title="View History & Recovery Schedule"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        @if(Auth::user()->isAdmin())
                                            <!-- Approve Button (if pending) -->
                                            @if($adv->status === 'pending')
                                                <form method="POST" action="{{ route('payroll.advances.approve', $adv) }}" class="inline" onsubmit="return confirm('Approve salary advance {{ $adv->advance_number }}?');">
                                                    @csrf
                                                    <button 
                                                        type="submit" 
                                                        class="p-1 text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 rounded transition cursor-pointer"
                                                        title="Approve Advance"
                                                    >
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>
                                                </form>

                                                <button 
                                                    type="button" 
                                                    @click="openReject({{ $adv }})"
                                                    class="p-1 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition cursor-pointer"
                                                    title="Reject Advance"
                                                >
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            @endif

                                            <!-- Disburse Button (if approved or pending) -->
                                            @if($adv->status === 'approved' || ($adv->status === 'pending' && ! $adv->isDisbursed()))
                                                <button 
                                                    type="button" 
                                                    @click="openDisburse({{ $adv }})"
                                                    class="px-2 py-1 bg-[#0B1849] hover:bg-[#15286e] text-white text-[11px] font-semibold rounded shadow-xs transition cursor-pointer"
                                                    title="Record Payout / Disbursement"
                                                >
                                                    Disburse
                                                </button>
                                            @endif

                                            <!-- Reverse Disbursement (if disbursed, but 0 recoveries) -->
                                            @if($adv->status === 'disbursed' && $adv->recovered_amount <= 0.001)
                                                <button 
                                                    type="button" 
                                                    @click="openReverse({{ $adv }})"
                                                    class="p-1 text-amber-600 hover:text-amber-800 hover:bg-amber-50 rounded transition cursor-pointer"
                                                    title="Reverse Disbursement (Audit correction)"
                                                >
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                </button>
                                            @endif

                                            <!-- Cancel Button (if eligible) -->
                                            @if($adv->canBeCancelled())
                                                <button 
                                                    type="button" 
                                                    @click="openCancel({{ $adv }})"
                                                    class="p-1 text-gray-400 hover:text-red-600 hover:bg-gray-100 rounded transition cursor-pointer"
                                                    title="Cancel Advance"
                                                >
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="block lg:hidden divide-y divide-gray-100">
                @foreach($advances as $adv)
                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-mono font-bold text-sm text-[#0B1849]">{{ $adv->advance_number }}</span>
                            @if($adv->status === 'pending')
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800">Pending</span>
                            @elseif($adv->status === 'approved')
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">Approved</span>
                            @elseif($adv->status === 'disbursed')
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-100 text-indigo-800">Disbursed</span>
                            @elseif($adv->status === 'partially_recovered')
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-purple-100 text-purple-800">Partially Recovered</span>
                            @elseif($adv->status === 'deducted')
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">Deducted</span>
                            @elseif($adv->status === 'rejected')
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-red-100 text-red-800">Rejected</span>
                            @elseif($adv->status === 'cancelled')
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700">Cancelled</span>
                            @endif
                        </div>

                        <div>
                            <div class="font-bold text-gray-900 text-sm">{{ $adv->employee->name }}</div>
                            <div class="text-xs text-gray-500">{{ $adv->employee->employee_number }} • {{ $adv->advance_date->format('d M Y') }}</div>
                        </div>

                        <div class="grid grid-cols-3 gap-2 bg-gray-50 p-2.5 rounded-lg text-xs font-mono">
                            <div>
                                <span class="text-[10px] text-gray-400 block font-sans">Amount</span>
                                <span class="font-bold text-gray-900">LKR {{ number_format($adv->amount, 0) }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 block font-sans">Recovered</span>
                                <span class="font-bold text-emerald-700">LKR {{ number_format($adv->recovered_amount, 0) }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 block font-sans">Remaining</span>
                                <span class="font-bold text-amber-600">LKR {{ number_format($adv->remaining_balance, 0) }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <span class="text-xs text-gray-500 capitalize">Method: {{ str_replace('_', ' ', $adv->payment_method) }}</span>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="openHistory({{ $adv }})" class="text-xs font-semibold text-[#0B1849] hover:underline">
                                    History
                                </button>
                                @if(Auth::user()->isAdmin() && ($adv->status === 'approved' || ($adv->status === 'pending' && ! $adv->isDisbursed())))
                                    <button type="button" @click="openDisburse({{ $adv }})" class="px-2 py-1 bg-[#0B1849] text-white text-xs font-semibold rounded">
                                        Disburse
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($advances->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $advances->links() }}
                </div>
            @endif
        @endif
    </div>

    <!-- 1. REGISTER ADVANCE MODAL -->
    <div 
        x-show="showRegisterModal" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity" @click="showRegisterModal = false"></div>

            <div class="relative inline-block w-full max-w-lg p-6 my-8 text-left bg-white rounded-2xl shadow-2xl transform transition-all border border-gray-100">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">Register Salary Advance</h3>
                    <button type="button" @click="showRegisterModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('payroll.advances.store') }}" class="space-y-4 mt-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Employee *</label>
                        <x-searchable-select 
                            name="employee_id" 
                            :value="old('employee_id')" 
                            placeholder="-- Choose Employee --" 
                            searchPlaceholder="Search employee name, number or mobile..."
                            :endpoint="route('search.employees')" 
                            :required="true" 
                        />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Advance Amount (LKR) *</label>
                            <input type="number" name="amount" step="0.01" min="1" required placeholder="10000.00" class="block w-full py-2 px-3 text-xs font-mono font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Monthly Installment (LKR)</label>
                            <input type="number" name="monthly_installment" step="0.01" min="0" placeholder="Optional partial recovery" class="block w-full py-2 px-3 text-xs font-mono border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
                            <span class="text-[10px] text-gray-400 block mt-0.5">Leave blank for full deduction in single month</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Advance Date *</label>
                            <input type="date" name="advance_date" value="{{ today()->toDateString() }}" required class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Payment Method *</label>
                            <select name="payment_method" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]">
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    @if(Auth::user()->isAdmin())
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Initial Approval Status</label>
                            <select name="approval_status" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]">
                                <option value="pending">Pending Approval</option>
                                <option value="approved">Approved (Awaiting Disbursement)</option>
                                <option value="disbursed">Immediate Disbursement (Pay Now & Update Money Book)</option>
                            </select>
                            <span class="text-[10px] text-gray-400 block mt-0.5">Approved advances require disbursement before money is marked as paid out.</span>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Notes / Reason (Optional)</label>
                        <textarea name="notes" rows="2" placeholder="e.g. Festival advance, emergency medical" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"></textarea>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="showRegisterModal = false" class="px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-xs transition">
                            Register Advance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. DISBURSEMENT MODAL -->
    <div 
        x-show="showDisburseModal" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity" @click="showDisburseModal = false"></div>

            <div class="relative inline-block w-full max-w-md p-6 my-8 text-left bg-white rounded-2xl shadow-2xl transform transition-all border border-gray-100">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">Record Advance Disbursement</h3>
                    <button type="button" @click="showDisburseModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <template x-if="selectedAdvance">
                    <form :action="`/payroll/advances/${selectedAdvance.id}/disburse`" method="POST" class="space-y-4 mt-4">
                        @csrf
                        <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl space-y-1">
                            <div class="flex justify-between text-xs">
                                <span class="text-blue-800 font-semibold">Advance #:</span>
                                <span class="font-mono font-bold text-[#0B1849]" x-text="selectedAdvance.advance_number"></span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-blue-800 font-semibold">Employee:</span>
                                <span class="text-gray-900 font-bold" x-text="selectedAdvance.employee ? selectedAdvance.employee.name : ''"></span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-blue-800 font-semibold">Principal Amount:</span>
                                <span class="font-mono font-bold text-gray-900" x-text="'LKR ' + Number(selectedAdvance.amount).toLocaleString(undefined, {minimumFractionDigits: 2})"></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Disbursement Date *</label>
                            <input type="date" name="disbursed_at" value="{{ today()->toDateString() }}" required class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Payment Method *</label>
                            <select name="payment_method" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]">
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Disbursement Notes (Optional)</label>
                            <textarea name="notes" rows="2" placeholder="e.g. Paid from petty cash drawer #1" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"></textarea>
                        </div>

                        <div class="text-[11px] text-gray-500 bg-gray-50 p-2.5 rounded-lg border border-gray-200">
                            <strong>Financial Flow:</strong> Recording disbursement creates 1 actual cash outflow in Money Book and allows the advance to be recovered in payroll. Profit & Loss excludes it from operating expenses.
                        </div>

                        <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                            <button type="button" @click="showDisburseModal = false" class="px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-xs transition">
                                Confirm Disbursement
                            </button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>

    <!-- 3. REJECTION MODAL -->
    <div 
        x-show="showRejectModal" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity" @click="showRejectModal = false"></div>

            <div class="relative inline-block w-full max-w-md p-6 my-8 text-left bg-white rounded-2xl shadow-2xl transform transition-all border border-gray-100">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">Reject Advance Request</h3>
                    <button type="button" @click="showRejectModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <template x-if="selectedAdvance">
                    <form :action="`/payroll/advances/${selectedAdvance.id}/reject`" method="POST" class="space-y-4 mt-4">
                        @csrf
                        <p class="text-xs text-gray-600">
                            Are you sure you want to reject advance <strong class="text-gray-900" x-text="selectedAdvance.advance_number"></strong>?
                        </p>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Reason for Rejection *</label>
                            <textarea name="rejection_reason" required rows="3" placeholder="Explain why this advance request cannot be approved..." class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"></textarea>
                        </div>

                        <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                            <button type="button" @click="showRejectModal = false" class="px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg shadow-xs transition">
                                Confirm Rejection
                            </button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>

    <!-- 4. CANCELLATION MODAL -->
    <div 
        x-show="showCancelModal" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity" @click="showCancelModal = false"></div>

            <div class="relative inline-block w-full max-w-md p-6 my-8 text-left bg-white rounded-2xl shadow-2xl transform transition-all border border-gray-100">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">Cancel Salary Advance</h3>
                    <button type="button" @click="showCancelModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <template x-if="selectedAdvance">
                    <form :action="`/payroll/advances/${selectedAdvance.id}/cancel`" method="POST" class="space-y-4 mt-4">
                        @csrf
                        <p class="text-xs text-gray-600">
                            Cancel advance <strong class="text-gray-900" x-text="selectedAdvance.advance_number"></strong>? This advance will no longer be eligible for disbursement or recovery.
                        </p>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Reason for Cancellation *</label>
                            <textarea name="cancellation_reason" required rows="3" placeholder="Provide cancellation reason..." class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"></textarea>
                        </div>

                        <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                            <button type="button" @click="showCancelModal = false" class="px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                                Back
                            </button>
                            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-gray-800 hover:bg-black rounded-lg shadow-xs transition">
                                Cancel Advance
                            </button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>

    <!-- 5. REVERSE DISBURSEMENT MODAL -->
    <div 
        x-show="showReverseModal" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity" @click="showReverseModal = false"></div>

            <div class="relative inline-block w-full max-w-md p-6 my-8 text-left bg-white rounded-2xl shadow-2xl transform transition-all border border-gray-100">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">Audited Reversal of Disbursement</h3>
                    <button type="button" @click="showReverseModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <template x-if="selectedAdvance">
                    <form :action="`/payroll/advances/${selectedAdvance.id}/reverse-disbursement`" method="POST" class="space-y-4 mt-4">
                        @csrf
                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 space-y-1">
                            <p class="font-bold">Caution: Audited Correction</p>
                            <p>This action will void the financial disbursement in Money Book and reset advance status to <strong>Approved</strong>.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Reason for Reversal *</label>
                            <textarea name="reversal_reason" required rows="3" placeholder="Explain the clerical error or reason for reversing payout..." class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"></textarea>
                        </div>

                        <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                            <button type="button" @click="showReverseModal = false" class="px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-lg shadow-xs transition">
                                Confirm Reversal
                            </button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>

    <!-- 6. ADVANCE HISTORY & RECOVERIES MODAL -->
    <div 
        x-show="showHistoryModal" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity" @click="showHistoryModal = false"></div>

            <div class="relative inline-block w-full max-w-2xl p-6 my-8 text-left bg-white rounded-2xl shadow-2xl transform transition-all border border-gray-100">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Advance Details & Recovery History</h3>
                        <p class="text-xs text-gray-500" x-text="historyData ? historyData.advance_number + ' - ' + historyData.employee_name : 'Loading...'"></p>
                    </div>
                    <button type="button" @click="showHistoryModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div x-show="loadingHistory" class="py-12 text-center text-xs text-gray-500">
                    Loading advance audit trail...
                </div>

                <div x-show="!loadingHistory && historyData" class="space-y-4 mt-4">
                    <!-- Summary Stats -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-gray-50 p-3 rounded-xl border border-gray-200 text-xs">
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase">Advance Amount</span>
                            <span class="font-mono font-bold text-gray-900" x-text="'LKR ' + Number(historyData ? historyData.amount : 0).toLocaleString(undefined, {minimumFractionDigits: 2})"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase">Total Recovered</span>
                            <span class="font-mono font-bold text-emerald-700" x-text="'LKR ' + Number(historyData ? historyData.recovered_amount : 0).toLocaleString(undefined, {minimumFractionDigits: 2})"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase">Remaining Balance</span>
                            <span class="font-mono font-bold text-amber-600" x-text="'LKR ' + Number(historyData ? historyData.remaining_balance : 0).toLocaleString(undefined, {minimumFractionDigits: 2})"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase">Installment</span>
                            <span class="font-mono font-bold text-blue-700" x-text="historyData && historyData.monthly_installment > 0 ? 'LKR ' + Number(historyData.monthly_installment).toLocaleString(undefined, {minimumFractionDigits: 2}) : 'Full Balance'"></span>
                        </div>
                    </div>

                    <!-- Audit info -->
                    <div class="text-xs space-y-1.5 text-gray-600 bg-white p-3 rounded-xl border border-gray-100">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Advance Date:</span>
                            <span class="font-semibold text-gray-800" x-text="historyData ? historyData.advance_date : ''"></span>
                        </div>
                        <template x-if="historyData && historyData.approved_at">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Approved By:</span>
                                <span class="font-semibold text-gray-800" x-text="(historyData.approved_by_name || 'Admin') + ' on ' + historyData.approved_at"></span>
                            </div>
                        </template>
                        <template x-if="historyData && historyData.disbursed_at">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Disbursed On:</span>
                                <span class="font-semibold text-gray-800" x-text="historyData.disbursed_at + (historyData.disbursed_by_name ? ' by ' + historyData.disbursed_by_name : '')"></span>
                            </div>
                        </template>
                        <template x-if="historyData && historyData.notes">
                            <div class="pt-1 border-t border-gray-100">
                                <span class="text-gray-400 block">Notes:</span>
                                <span class="text-gray-800 whitespace-pre-line" x-text="historyData.notes"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Recoveries list -->
                    <div>
                        <h4 class="text-xs font-bold text-gray-900 mb-2">Payroll Recovery History</h4>
                        <template x-if="historyData && historyData.recoveries && historyData.recoveries.length > 0">
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-gray-50 text-gray-600 font-semibold uppercase text-[10px]">
                                        <tr>
                                            <th class="py-2 px-3">Payroll Month</th>
                                            <th class="py-2 px-3">Date</th>
                                            <th class="py-2 px-3 text-right">Amount Recovered</th>
                                            <th class="py-2 px-3 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-for="rec in historyData.recoveries" :key="rec.id">
                                            <tr>
                                                <td class="py-2 px-3 font-semibold text-gray-900" x-text="rec.period"></td>
                                                <td class="py-2 px-3 text-gray-600" x-text="rec.recovery_date"></td>
                                                <td class="py-2 px-3 font-mono font-bold text-emerald-700 text-right" x-text="'LKR ' + Number(rec.amount).toLocaleString(undefined, {minimumFractionDigits: 2})"></td>
                                                <td class="py-2 px-3 text-center">
                                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800 capitalize" x-text="rec.status"></span>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </template>
                        <template x-if="!historyData || !historyData.recoveries || historyData.recoveries.length === 0">
                            <div class="p-4 bg-gray-50 rounded-lg border border-dashed border-gray-200 text-center text-xs text-gray-500">
                                No payroll recoveries recorded yet. The remaining balance will be deducted in upcoming payroll months according to the installment terms.
                            </div>
                        </template>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex justify-end">
                        <button type="button" @click="showHistoryModal = false" class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
