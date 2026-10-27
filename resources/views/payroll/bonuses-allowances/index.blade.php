@extends('layouts.app')

@section('title', 'Bonuses, Allowances & Overtime')

@section('content')
<div 
    class="space-y-6"
    x-data="{
        activeTab: '{{ $currentTab }}',
        showAddBonusModal: false,
        showAddAllowanceModal: false,
        showAddOvertimeModal: false,
        showRejectModal: false,
        showCancelModal: false,

        selectedEntity: null,
        rejectActionUrl: '',
        cancelActionUrl: '',
        cancelEntityType: '',

        // Bonus modal state
        bonusTarget: 'single',
        bonusCalcType: 'fixed',
        bonusPercentage: '',
        bonusAmount: '',

        // Overtime modal live calculation
        otEmployeeId: '',
        otHours: '',
        otMultiplier: '1.50',
        otCustomRate: '',
        otHourlyRate: 0.00,
        otCalculatedAmount: 0.00,
        otLoadingRate: false,

        fetchOvertimeRate() {
            if (!this.otEmployeeId) {
                this.otHourlyRate = 0.00;
                this.otCalculatedAmount = 0.00;
                return;
            }
            this.otLoadingRate = true;
            const params = new URLSearchParams({
                employee_id: this.otEmployeeId,
                hours: this.otHours || 0,
                rate_multiplier: this.otMultiplier || 1.5,
                custom_hourly_rate: this.otCustomRate || ''
            });
            fetch(`{{ route('payroll.bonuses-allowances.overtime-rate') }}?${params}`)
                .then(r => r.json())
                .then(d => {
                    this.otHourlyRate = d.hourly_rate;
                    this.otCalculatedAmount = d.amount;
                    this.otLoadingRate = false;
                })
                .catch(() => {
                    this.otLoadingRate = false;
                });
        },

        openReject(entity, type) {
            this.selectedEntity = entity;
            if (type === 'bonus') {
                this.rejectActionUrl = `/payroll/bonuses-allowances/bonuses/${entity.id}/reject`;
            } else if (type === 'overtime') {
                this.rejectActionUrl = `/payroll/bonuses-allowances/overtime/${entity.id}/reject`;
            }
            this.showRejectModal = true;
        },

        openCancel(entity, type) {
            this.selectedEntity = entity;
            this.cancelEntityType = type;
            if (type === 'bonus') {
                this.cancelActionUrl = `/payroll/bonuses-allowances/bonuses/${entity.id}/cancel`;
            } else if (type === 'allowance') {
                this.cancelActionUrl = `/payroll/bonuses-allowances/allowances/${entity.id}/cancel`;
            } else if (type === 'overtime') {
                this.cancelActionUrl = `/payroll/bonuses-allowances/overtime/${entity.id}/cancel`;
            }
            this.showCancelModal = true;
        }
    }"
>
    <!-- Header -->
    <x-page-header 
        title="Bonuses, Allowances & Overtime" 
        description="Manage employee incentive bonuses, monthly recurring allowances, and verified overtime hours."
    >
        <x-slot:actions>
            @if(Auth::user()->isAdmin())
                <div class="flex items-center gap-2">
                    <button 
                        type="button" 
                        @click="showAddBonusModal = true"
                        class="inline-flex items-center px-3 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Bonus
                    </button>
                    <button 
                        type="button" 
                        @click="showAddAllowanceModal = true"
                        class="inline-flex items-center px-3 py-2 bg-indigo-700 hover:bg-indigo-800 text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Allowance
                    </button>
                    <button 
                        type="button" 
                        @click="showAddOvertimeModal = true; fetchOvertimeRate();"
                        class="inline-flex items-center px-3 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Record Overtime
                    </button>
                </div>
            @endif
        </x-slot:actions>
    </x-page-header>

    @if(! \Illuminate\Support\Facades\Schema::hasTable('employee_bonuses'))
        <!-- Migration Notice Banner -->
        <div class="bg-amber-50 border-l-4 border-amber-500 rounded-r-xl p-4 shadow-xs">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-amber-600 mr-3 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h4 class="text-sm font-bold text-amber-900">Database Migration Prepared</h4>
                    <p class="text-xs text-amber-800 mt-1">
                        The Bonuses, Allowances & Overtime migration file is prepared at 
                        <code class="bg-amber-100/80 px-1 py-0.5 rounded font-mono text-[11px]">database/migrations/2026_10_27_103000_create_bonuses_allowances_overtime_tables.php</code>.
                        Once you run <code class="bg-amber-100/80 px-1 py-0.5 rounded font-mono text-[11px]">php artisan migrate</code> on your server, detailed bonus, allowance, and overtime records will be permanently tracked in your database.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 block">Total Approved Bonuses</span>
            <span class="text-xl font-bold text-gray-900 font-mono mt-1 block">LKR {{ number_format($totalApprovedBonuses, 2) }}</span>
            <span class="text-[11px] text-gray-400 block mt-0.5">Approved for selected period</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 block">Active Monthly Allowances</span>
            <span class="text-xl font-bold text-indigo-700 font-mono mt-1 block">LKR {{ number_format($totalActiveAllowances, 2) }}</span>
            <span class="text-[11px] text-gray-400 block mt-0.5">Monthly recurring allowances</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 block">Approved Overtime</span>
            <span class="text-xl font-bold text-emerald-700 font-mono mt-1 block">LKR {{ number_format($totalApprovedOvertimeAmount, 2) }}</span>
            <span class="text-[11px] text-gray-400 block mt-0.5">{{ number_format($totalApprovedOvertimeHours, 1) }} approved hours</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs font-medium text-gray-500 block">Pending Approvals</span>
            <span class="text-xl font-bold text-[#0B1849] font-mono mt-1 block">{{ $pendingApprovalsCount }}</span>
            <span class="text-[11px] text-gray-400 block mt-0.5">Awaiting admin sign-off</span>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-4">
        <form method="GET" action="{{ route('payroll.bonuses-allowances.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
            <input type="hidden" name="tab" :value="activeTab">

            <!-- Employee Filter -->
            <div class="lg:col-span-2">
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

            <!-- Year Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Year</label>
                <select name="year" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]">
                    @for($y = (int) now()->format('Y') + 1; $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ ($year ?? (int) now()->format('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Month Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Month</label>
                <select name="month" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]">
                    <option value="">All Months</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(2026, $m, 1)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                <select name="status" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]">
                    <option value="all">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="applied" {{ request('status') === 'applied' ? 'selected' : '' }}>Applied in Payroll</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active (Allowances)</option>
                    <option value="paused" {{ request('status') === 'paused' ? 'selected' : '' }}>Paused</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-semibold rounded-lg transition text-center cursor-pointer">
                    Filter
                </button>
                <a href="{{ route('payroll.bonuses-allowances.index', ['tab' => $currentTab]) }}" class="py-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-6" aria-label="Tabs">
            <button 
                type="button" 
                @click="activeTab = 'bonuses'" 
                :class="activeTab === 'bonuses' ? 'border-[#0B1849] text-[#0B1849] font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'"
                class="whitespace-nowrap py-3 px-1 border-b-2 text-xs transition flex items-center gap-2 cursor-pointer"
            >
                <span>Bonuses</span>
                <span class="px-2 py-0.5 rounded-full text-[10px]" :class="activeTab === 'bonuses' ? 'bg-[#EEF1FA] text-[#0B1849]' : 'bg-gray-100 text-gray-600'">
                    {{ $bonuses->total() }}
                </span>
            </button>

            <button 
                type="button" 
                @click="activeTab = 'allowances'" 
                :class="activeTab === 'allowances' ? 'border-[#0B1849] text-[#0B1849] font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'"
                class="whitespace-nowrap py-3 px-1 border-b-2 text-xs transition flex items-center gap-2 cursor-pointer"
            >
                <span>Allowances</span>
                <span class="px-2 py-0.5 rounded-full text-[10px]" :class="activeTab === 'allowances' ? 'bg-[#EEF1FA] text-[#0B1849]' : 'bg-gray-100 text-gray-600'">
                    {{ $allowances->total() }}
                </span>
            </button>

            <button 
                type="button" 
                @click="activeTab = 'overtime'" 
                :class="activeTab === 'overtime' ? 'border-[#0B1849] text-[#0B1849] font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'"
                class="whitespace-nowrap py-3 px-1 border-b-2 text-xs transition flex items-center gap-2 cursor-pointer"
            >
                <span>Overtime</span>
                <span class="px-2 py-0.5 rounded-full text-[10px]" :class="activeTab === 'overtime' ? 'bg-[#EEF1FA] text-[#0B1849]' : 'bg-gray-100 text-gray-600'">
                    {{ $overtimes->total() }}
                </span>
            </button>
        </nav>
    </div>

    <!-- TAB 1: BONUSES -->
    <div x-show="activeTab === 'bonuses'" class="space-y-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">Employee Bonuses</h3>
                <span class="text-xs text-gray-500">Showing {{ $bonuses->count() }} of {{ $bonuses->total() }}</span>
            </div>

            @if($bonuses->isEmpty())
                <div class="py-12 text-center text-xs text-gray-500">
                    No bonus records found for the selected filter criteria.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-600 font-semibold uppercase tracking-wider text-[11px]">
                                <th class="py-3 px-4">Bonus #</th>
                                <th class="py-3 px-4">Employee</th>
                                <th class="py-3 px-4">Type</th>
                                <th class="py-3 px-4">Payroll Month</th>
                                <th class="py-3 px-4 text-right">Amount</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4">Approved By</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($bonuses as $b)
                                <tr class="hover:bg-gray-50/60 transition">
                                    <td class="py-3 px-4 font-mono font-bold text-[#0B1849]">{{ $b->bonus_number }}</td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-gray-900">{{ $b->employee->name }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $b->employee->employee_number }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-gray-700 capitalize">
                                        {{ $b->formatted_type }}
                                        @if($b->calculation_type === 'percentage')
                                            <span class="text-[10px] text-blue-600 block">({{ number_format($b->percentage_rate, 1) }}% Rate)</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-gray-700 whitespace-nowrap">{{ $b->formatted_period }}</td>
                                    <td class="py-3 px-4 font-mono font-bold text-emerald-700 text-right">
                                        LKR {{ number_format($b->amount, 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        @if($b->status === 'pending')
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800">Pending</span>
                                        @elseif($b->status === 'approved')
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">Approved</span>
                                        @elseif($b->status === 'applied')
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">Applied in Payroll</span>
                                        @elseif($b->status === 'rejected')
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-red-100 text-red-800">Rejected</span>
                                        @elseif($b->status === 'cancelled')
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700">Cancelled</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 whitespace-nowrap">
                                        {{ $b->approver ? $b->approver->name : '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if(Auth::user()->isAdmin())
                                                @if($b->status === 'pending')
                                                    <form method="POST" action="{{ route('payroll.bonuses-allowances.bonuses.approve', $b) }}" class="inline" onsubmit="return confirm('Approve bonus {{ $b->bonus_number }}?');">
                                                        @csrf
                                                        <button type="submit" class="p-1 text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 rounded transition cursor-pointer" title="Approve Bonus">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                    <button type="button" @click="openReject({{ $b }}, 'bonus')" class="p-1 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition cursor-pointer" title="Reject Bonus">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                @endif
                                                @if($b->canBeCancelled())
                                                    <button type="button" @click="openCancel({{ $b }}, 'bonus')" class="p-1 text-gray-400 hover:text-red-600 hover:bg-gray-100 rounded transition cursor-pointer" title="Cancel Bonus">
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
                @if($bonuses->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $bonuses->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- TAB 2: ALLOWANCES -->
    <div x-show="activeTab === 'allowances'" class="space-y-4" style="display: none;">
        <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">Configured Employee Allowances</h3>
                <span class="text-xs text-gray-500">Showing {{ $allowances->count() }} of {{ $allowances->total() }}</span>
            </div>

            @if($allowances->isEmpty())
                <div class="py-12 text-center text-xs text-gray-500">
                    No allowance records found.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-600 font-semibold uppercase tracking-wider text-[11px]">
                                <th class="py-3 px-4">Allowance #</th>
                                <th class="py-3 px-4">Employee</th>
                                <th class="py-3 px-4">Type / Title</th>
                                <th class="py-3 px-4">Schedule</th>
                                <th class="py-3 px-4 text-center">Statutory EPF/ETF</th>
                                <th class="py-3 px-4 text-right">Amount</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($allowances as $a)
                                <tr class="hover:bg-gray-50/60 transition">
                                    <td class="py-3 px-4 font-mono font-bold text-[#0B1849]">{{ $a->allowance_number }}</td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-gray-900">{{ $a->employee->name }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $a->employee->employee_number }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-gray-700">
                                        <div class="font-semibold">{{ $a->formatted_type }}</div>
                                        @if($a->title)
                                            <div class="text-[11px] text-gray-500">{{ $a->title }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-gray-700">
                                        @if($a->is_recurring)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700">
                                                Recurring Monthly
                                            </span>
                                            <div class="text-[10px] text-gray-400 mt-0.5">From {{ $a->effective_from->format('d M Y') }}</div>
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700">
                                                One-Off
                                            </span>
                                            <div class="text-[10px] text-gray-500 mt-0.5">
                                                {{ $a->period_year && $a->period_month ? \Carbon\Carbon::create($a->period_year, $a->period_month, 1)->format('F Y') : $a->effective_from->format('d M Y') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($a->is_epf_eligible)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700">EPF Eligible</span>
                                        @else
                                            <span class="text-gray-400 text-[11px]">Exempt</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 font-mono font-bold text-indigo-700 text-right">
                                        LKR {{ number_format($a->amount, 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        @if($a->status === 'active' || $a->status === 'approved')
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">Active</span>
                                        @elseif($a->status === 'paused')
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800">Paused</span>
                                        @elseif($a->status === 'cancelled')
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700">Cancelled</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if(Auth::user()->isAdmin() && $a->canBeCancelled())
                                                <form method="POST" action="{{ route('payroll.bonuses-allowances.allowances.toggle', $a) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-2 py-1 text-[11px] font-semibold rounded transition cursor-pointer {{ $a->status === 'active' ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                                        {{ $a->status === 'active' ? 'Pause' : 'Activate' }}
                                                    </button>
                                                </form>

                                                <button type="button" @click="openCancel({{ $a }}, 'allowance')" class="p-1 text-gray-400 hover:text-red-600 rounded transition cursor-pointer" title="Cancel Allowance">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($allowances->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $allowances->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- TAB 3: OVERTIME -->
    <div x-show="activeTab === 'overtime'" class="space-y-4" style="display: none;">
        <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">Overtime Hours & Payouts</h3>
                <span class="text-xs text-gray-500">Showing {{ $overtimes->count() }} of {{ $overtimes->total() }}</span>
            </div>

            @if($overtimes->isEmpty())
                <div class="py-12 text-center text-xs text-gray-500">
                    No overtime records found.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-600 font-semibold uppercase tracking-wider text-[11px]">
                                <th class="py-3 px-4">OT #</th>
                                <th class="py-3 px-4">Employee</th>
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4 text-center">Hours</th>
                                <th class="py-3 px-4 text-right">Hourly Rate</th>
                                <th class="py-3 px-4 text-center">Multiplier</th>
                                <th class="py-3 px-4 text-right">Calculated Payout</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($overtimes as $ot)
                                <tr class="hover:bg-gray-50/60 transition">
                                    <td class="py-3 px-4 font-mono font-bold text-[#0B1849]">{{ $ot->overtime_number }}</td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-gray-900">{{ $ot->employee->name }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $ot->employee->employee_number }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-gray-700 whitespace-nowrap">{{ $ot->overtime_date->format('d M Y') }}</td>
                                    <td class="py-3 px-4 text-center font-bold font-mono">{{ number_format($ot->hours, 1) }} hrs</td>
                                    <td class="py-3 px-4 text-right font-mono">LKR {{ number_format($ot->hourly_rate, 2) }}</td>
                                    <td class="py-3 px-4 text-center font-semibold text-blue-700">{{ number_format($ot->rate_multiplier, 2) }}x</td>
                                    <td class="py-3 px-4 font-mono font-bold text-emerald-700 text-right">
                                        LKR {{ number_format($ot->amount, 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        @if($ot->status === 'pending')
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800">Pending</span>
                                        @elseif($ot->status === 'approved')
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">Approved</span>
                                        @elseif($ot->status === 'applied')
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">Applied in Payroll</span>
                                        @elseif($ot->status === 'rejected')
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-red-100 text-red-800">Rejected</span>
                                        @elseif($ot->status === 'cancelled')
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700">Cancelled</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if(Auth::user()->isAdmin())
                                                @if($ot->status === 'pending')
                                                    <form method="POST" action="{{ route('payroll.bonuses-allowances.overtime.approve', $ot) }}" class="inline" onsubmit="return confirm('Approve overtime {{ $ot->overtime_number }}?');">
                                                        @csrf
                                                        <button type="submit" class="p-1 text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 rounded transition cursor-pointer" title="Approve Overtime">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                    <button type="button" @click="openReject({{ $ot }}, 'overtime')" class="p-1 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition cursor-pointer" title="Reject Overtime">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                @endif
                                                @if($ot->canBeCancelled())
                                                    <button type="button" @click="openCancel({{ $ot }}, 'overtime')" class="p-1 text-gray-400 hover:text-red-600 hover:bg-gray-100 rounded transition cursor-pointer" title="Cancel Overtime">
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
                @if($overtimes->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $overtimes->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- 1. ADD BONUS MODAL -->
    <div x-show="showAddBonusModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity" @click="showAddBonusModal = false"></div>
            <div class="relative inline-block w-full max-w-lg p-6 my-8 text-left bg-white rounded-2xl shadow-2xl transform transition-all border border-gray-100">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">Add Employee Bonus</h3>
                    <button type="button" @click="showAddBonusModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('payroll.bonuses-allowances.bonuses.store') }}" class="space-y-4 mt-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Target Eligibility *</label>
                        <select name="target_type" x-model="bonusTarget" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]">
                            <option value="single">Single Employee</option>
                            <option value="selected">Selected Employees</option>
                            <option value="all">All Eligible Active Employees</option>
                        </select>
                    </div>

                    <div x-show="bonusTarget === 'single'">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Select Employee *</label>
                        <x-searchable-select 
                            name="employee_id" 
                            placeholder="-- Choose Employee --" 
                            searchPlaceholder="Search employee name or mobile..."
                            :endpoint="route('search.employees')" 
                        />
                    </div>

                    <div x-show="bonusTarget === 'selected'" class="space-y-1">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Select Employees *</label>
                        <div class="max-h-36 overflow-y-auto border border-gray-200 rounded-lg p-2 space-y-1 bg-gray-50">
                            @foreach($employees as $emp)
                                <label class="flex items-center text-xs text-gray-700 cursor-pointer hover:bg-white p-1 rounded">
                                    <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" class="mr-2 rounded text-[#0B1849] focus:ring-[#0B1849]">
                                    <span>{{ $emp->name }} ({{ $emp->employee_number }})</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Bonus Type *</label>
                            <select name="bonus_type" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]">
                                <option value="performance">Performance Bonus</option>
                                <option value="attendance">Attendance Bonus</option>
                                <option value="special">Special Bonus</option>
                                <option value="festival">Festival Bonus</option>
                                <option value="incentive">Incentive</option>
                                <option value="other">Other Bonus</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Calculation Method *</label>
                            <select name="calculation_type" x-model="bonusCalcType" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]">
                                <option value="fixed">Fixed Amount (LKR)</option>
                                <option value="percentage">Percentage (%) of Basic/Wage</option>
                            </select>
                        </div>
                    </div>

                    <div x-show="bonusCalcType === 'fixed'">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Bonus Amount (LKR) *</label>
                        <input type="number" name="amount" step="0.01" min="1" placeholder="5000.00" class="block w-full py-2 px-3 text-xs font-mono font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
                    </div>

                    <div x-show="bonusCalcType === 'percentage'">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Percentage Rate (%) *</label>
                        <input type="number" name="percentage_rate" step="0.01" min="0.01" max="100" placeholder="10.00" class="block w-full py-2 px-3 text-xs font-mono font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
                        <span class="text-[10px] text-gray-400 block mt-0.5">Calculated based on Monthly Basic Salary or 26 days of Daily Wage.</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Payroll Year *</label>
                            <input type="number" name="period_year" value="{{ (int) now()->format('Y') }}" required class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Payroll Month *</label>
                            <select name="period_month" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ (int) now()->format('n') == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(2026, $m, 1)->format('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Initial Status</label>
                        <select name="status" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]">
                            <option value="approved">Pre-Approved (Ready for Payroll)</option>
                            <option value="pending">Pending Approval</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Notes (Optional)</label>
                        <textarea name="notes" rows="2" placeholder="e.g. Sinhala/Tamil New Year festive bonus" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"></textarea>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="showAddBonusModal = false" class="px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-xs transition">
                            Save Bonus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. ADD ALLOWANCE MODAL -->
    <div x-show="showAddAllowanceModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity" @click="showAddAllowanceModal = false"></div>
            <div class="relative inline-block w-full max-w-lg p-6 my-8 text-left bg-white rounded-2xl shadow-2xl transform transition-all border border-gray-100">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">Configure Allowance</h3>
                    <button type="button" @click="showAddAllowanceModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('payroll.bonuses-allowances.allowances.store') }}" class="space-y-4 mt-4" x-data="{ isRecurring: '1' }">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Target *</label>
                        <select name="target_type" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]">
                            <option value="single">Single Employee</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Employee *</label>
                        <x-searchable-select 
                            name="employee_id" 
                            placeholder="-- Choose Employee --" 
                            searchPlaceholder="Search employee name or mobile..."
                            :endpoint="route('search.employees')" 
                            :required="true" 
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Allowance Type *</label>
                            <select name="allowance_type" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]">
                                <option value="transport">Transport Allowance</option>
                                <option value="meal">Meal Allowance</option>
                                <option value="special">Special Allowance</option>
                                <option value="other">Other Allowance</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Amount (LKR) *</label>
                            <input type="number" name="amount" step="0.01" min="1" required placeholder="3000.00" class="block w-full py-2 px-3 text-xs font-mono font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Title / Label</label>
                        <input type="text" name="title" placeholder="e.g. Fuel Subsidy / Outstation Duty" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Schedule Frequency *</label>
                            <select name="is_recurring" x-model="isRecurring" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]">
                                <option value="1">Recurring Every Month</option>
                                <option value="0">One-Off (Single Month)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Effective Date *</label>
                            <input type="date" name="effective_from" value="{{ today()->toDateString() }}" required class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
                        </div>
                    </div>

                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 space-y-2">
                        <span class="text-xs font-bold text-gray-800 block">Statutory Earning Classifications</span>
                        <div class="flex items-center gap-4 text-xs text-gray-700">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="is_epf_eligible" value="1" class="mr-1.5 rounded text-[#0B1849] focus:ring-[#0B1849]">
                                <span>Subject to EPF/ETF</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="is_taxable" value="1" class="mr-1.5 rounded text-[#0B1849] focus:ring-[#0B1849]">
                                <span>Taxable (APIT)</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="showAddAllowanceModal = false" class="px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-700 hover:bg-indigo-800 rounded-lg shadow-xs transition">
                            Save Allowance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 3. RECORD OVERTIME MODAL -->
    <div x-show="showAddOvertimeModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity" @click="showAddOvertimeModal = false"></div>
            <div class="relative inline-block w-full max-w-lg p-6 my-8 text-left bg-white rounded-2xl shadow-2xl transform transition-all border border-gray-100">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">Record Overtime</h3>
                    <button type="button" @click="showAddOvertimeModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('payroll.bonuses-allowances.overtime.store') }}" class="space-y-4 mt-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Employee *</label>
                        <x-searchable-select 
                            name="employee_id" 
                            placeholder="-- Choose Employee --" 
                            searchPlaceholder="Search employee name or mobile..."
                            :endpoint="route('search.employees')" 
                            :required="true" 
                            @selected="otEmployeeId = $event.detail.id; fetchOvertimeRate();"
                            @cleared="otEmployeeId = ''; otHourlyRate = 0;"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Overtime Date *</label>
                            <input type="date" name="overtime_date" value="{{ today()->toDateString() }}" required class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">OT Hours *</label>
                            <input type="number" name="hours" step="0.5" min="0.5" max="24" x-model="otHours" @input="fetchOvertimeRate()" required placeholder="2.5" class="block w-full py-2 px-3 text-xs font-mono font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Rate Multiplier *</label>
                            <select name="rate_multiplier" x-model="otMultiplier" @change="fetchOvertimeRate()" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]">
                                <option value="1.50">1.5x (Standard Normal Day OT)</option>
                                <option value="2.00">2.0x (Holiday / Rest Day OT)</option>
                                <option value="1.00">1.0x (Single Rate)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Custom Hourly Rate (Optional)</label>
                            <input type="number" name="custom_hourly_rate" step="0.01" min="0" x-model="otCustomRate" @input="fetchOvertimeRate()" placeholder="Auto-calculated" class="block w-full py-2 px-3 text-xs font-mono border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]">
                        </div>
                    </div>

                    <!-- Live Calculation Preview Card -->
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl space-y-1 text-xs">
                        <div class="flex justify-between text-emerald-900">
                            <span>Base Hourly Rate:</span>
                            <span class="font-mono font-bold" x-text="'LKR ' + Number(otHourlyRate).toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-emerald-900">
                            <span>Calculated Overtime Payout:</span>
                            <span class="font-mono font-bold text-emerald-800 text-sm" x-text="'LKR ' + Number(otCalculatedAmount).toFixed(2)"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Approval Status</label>
                        <select name="status" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]">
                            <option value="approved">Pre-Approved (Ready for Payroll)</option>
                            <option value="pending">Pending Review</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Notes (Optional)</label>
                        <textarea name="notes" rows="2" placeholder="e.g. Urgent evening brake overhaul job" class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"></textarea>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="showAddOvertimeModal = false" class="px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-emerald-700 hover:bg-emerald-800 rounded-lg shadow-xs transition">
                            Save Overtime
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- REJECTION MODAL -->
    <div x-show="showRejectModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity" @click="showRejectModal = false"></div>
            <div class="relative inline-block w-full max-w-md p-6 my-8 text-left bg-white rounded-2xl shadow-2xl transform transition-all border border-gray-100">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">Reject Request</h3>
                    <button type="button" @click="showRejectModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="rejectActionUrl" method="POST" class="space-y-4 mt-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Reason for Rejection *</label>
                        <textarea name="rejection_reason" required rows="3" placeholder="Provide reason..." class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"></textarea>
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
            </div>
        </div>
    </div>

    <!-- CANCELLATION MODAL -->
    <div x-show="showCancelModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity" @click="showCancelModal = false"></div>
            <div class="relative inline-block w-full max-w-md p-6 my-8 text-left bg-white rounded-2xl shadow-2xl transform transition-all border border-gray-100">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">Cancel Record</h3>
                    <button type="button" @click="showCancelModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="cancelActionUrl" method="POST" class="space-y-4 mt-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Reason for Cancellation *</label>
                        <textarea name="cancellation_reason" required rows="3" placeholder="Provide cancellation reason..." class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-700"></textarea>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="showCancelModal = false" class="px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            Back
                        </button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-gray-800 hover:bg-black rounded-lg shadow-xs transition">
                            Confirm Cancellation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
