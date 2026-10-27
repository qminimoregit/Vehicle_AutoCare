@extends('layouts.app')

@section('title', 'Company Holidays')

@section('content')
<div 
    class="space-y-6"
    x-data="{
        showCreateModal: false,
        showEditModal: false,
        editHoliday: {
            id: null,
            name: '',
            start_date: '',
            end_date: '',
            duration: 'full_day',
            holiday_type: 'paid',
            branch_id: '',
            applies_to_all: true,
            employee_ids: [],
            notes: ''
        },
        openEdit(holiday) {
            this.editHoliday = {
                id: holiday.id,
                name: holiday.name,
                start_date: holiday.start_date ? holiday.start_date.substring(0, 10) : '',
                end_date: holiday.end_date ? holiday.end_date.substring(0, 10) : (holiday.start_date ? holiday.start_date.substring(0, 10) : ''),
                duration: holiday.duration || 'full_day',
                holiday_type: holiday.holiday_type || 'paid',
                branch_id: holiday.branch_id || '',
                applies_to_all: Boolean(holiday.applies_to_all),
                employee_ids: holiday.employees ? holiday.employees.map(e => e.id) : [],
                notes: holiday.notes || ''
            };
            this.showEditModal = true;
        }
    }"
>
    <!-- Header -->
    <x-page-header 
        title="Company Holidays" 
        description="Configure paid and unpaid company holidays, branch coverage, and holiday staff eligibility."
    >
        <x-slot:actions>
            @if(Auth::user()->isAdmin())
                <button 
                    type="button" 
                    @click="showCreateModal = true"
                    class="inline-flex items-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-semibold rounded-lg shadow-xs transition"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Holiday
                </button>
            @endif
        </x-slot:actions>
    </x-page-header>

    @if(! $isTableReady)
        <!-- Migration Notice Banner -->
        <div class="bg-amber-50 border-l-4 border-amber-500 rounded-r-xl p-4 shadow-xs">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-amber-600 mr-3 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h4 class="text-sm font-bold text-amber-900">Database Migration Prepared</h4>
                    <p class="text-xs text-amber-800 mt-1">
                        The holiday management migration file is prepared at 
                        <code class="bg-amber-100/80 px-1 py-0.5 rounded font-mono text-[11px]">database/migrations/2026_10_27_083000_create_company_holidays_tables.php</code>.
                        Once you run <code class="bg-amber-100/80 px-1 py-0.5 rounded font-mono text-[11px]">php artisan migrate</code> on your server, declared holidays will be stored and tracked in your database.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs text-gray-500 block">Total Holidays ({{ $selectedYear }})</span>
            <span class="text-xl font-bold text-gray-900 font-mono">{{ $totalHolidays }}</span>
            <span class="text-[10px] text-gray-400 block mt-0.5">Declared in calendar</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs text-gray-500 block">Paid Holidays</span>
            <span class="text-xl font-bold text-emerald-700 font-mono">{{ $paidCount }}</span>
            <span class="text-[10px] text-gray-400 block mt-0.5">Eligible for salary credit</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <span class="text-xs text-gray-500 block">Unpaid Holidays</span>
            <span class="text-xl font-bold text-gray-600 font-mono">{{ $unpaidCount }}</span>
            <span class="text-[10px] text-gray-400 block mt-0.5">Non-payable company off days</span>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
        <form method="GET" action="{{ route('payroll.holidays.index') }}" class="flex flex-col sm:flex-row sm:items-end justify-between gap-3">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 flex-1">
                <div>
                    <label for="year" class="block text-xs font-semibold text-gray-700 mb-1">Year</label>
                    <select 
                        name="year" 
                        id="year" 
                        class="w-full py-2 px-3 text-xs font-semibold text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white"
                    >
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="type" class="block text-xs font-semibold text-gray-700 mb-1">Holiday Type</label>
                    <select 
                        name="type" 
                        id="type" 
                        class="w-full py-2 px-3 text-xs font-semibold text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white"
                    >
                        <option value="all" {{ $typeFilter === 'all' ? 'selected' : '' }}>All Types</option>
                        <option value="paid" {{ $typeFilter === 'paid' ? 'selected' : '' }}>Paid Holidays</option>
                        <option value="unpaid" {{ $typeFilter === 'unpaid' ? 'selected' : '' }}>Unpaid Holidays</option>
                    </select>
                </div>

                <div>
                    <label for="branch_id" class="block text-xs font-semibold text-gray-700 mb-1">Branch</label>
                    <select 
                        name="branch_id" 
                        id="branch_id" 
                        class="w-full py-2 px-3 text-xs font-semibold text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white"
                    >
                        <option value="all" {{ $branchFilter === 'all' ? 'selected' : '' }}>All Branches</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ $branchFilter == $b->id ? 'selected' : '' }}>
                                {{ $b->branch_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                    <select 
                        name="status" 
                        id="status" 
                        class="w-full py-2 px-3 text-xs font-semibold text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white"
                    >
                        <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active Only</option>
                        <option value="cancelled" {{ $statusFilter === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-semibold rounded-lg transition"
                >
                    Apply Filter
                </button>
                <a 
                    href="{{ route('payroll.holidays.index') }}" 
                    class="px-3 py-2 text-xs font-semibold text-gray-500 hover:text-gray-700 transition"
                >
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Holidays List Table (Desktop) -->
    <div class="hidden md:block bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
        <div class="responsive-table-wrapper">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Holiday Name</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date / Range</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Duration</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Pay Type</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Coverage & Branch</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($holidays as $holiday)
                        <tr class="hover:bg-slate-50/70 transition {{ ! $holiday->isActive() ? 'opacity-60 bg-gray-50/40' : '' }}">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="font-semibold text-gray-900 text-sm">
                                    {{ $holiday->name }}
                                </div>
                                @if($holiday->notes)
                                    <div class="text-xs text-gray-500 max-w-xs truncate" title="{{ $holiday->notes }}">
                                        {{ $holiday->notes }}
                                    </div>
                                @endif
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-700 font-mono">
                                @if($holiday->start_date->isSameDay($holiday->end_date))
                                    {{ $holiday->start_date->format('d M Y') }}
                                    <span class="text-xs text-gray-400 block font-sans">({{ $holiday->start_date->format('l') }})</span>
                                @else
                                    {{ $holiday->start_date->format('d M') }} &ndash; {{ $holiday->end_date->format('d M Y') }}
                                    <span class="text-xs text-gray-400 block font-sans">({{ $holiday->start_date->diffInDays($holiday->end_date) + 1 }} days)</span>
                                @endif
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                @if($holiday->isFullDay())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Full Day
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Half Day
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                @if($holiday->isPaid())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        Paid Holiday
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                        Unpaid Holiday
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap text-xs text-gray-600">
                                <div class="font-semibold text-gray-900">
                                    {{ $holiday->branch?->branch_name ?? 'All Branches' }}
                                </div>
                                <div class="text-[11px] text-gray-500 mt-0.5">
                                    @if($holiday->applies_to_all)
                                        All Employees
                                    @else
                                        {{ $holiday->employees->count() }} Selected Employees
                                    @endif
                                </div>
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                @if($holiday->isActive())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700">
                                        Cancelled
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap text-right text-xs space-x-1.5 font-medium">
                                @if(Auth::user()->isAdmin())
                                    <button 
                                        type="button" 
                                        @click="openEdit({{ json_encode($holiday) }})"
                                        class="inline-flex items-center px-2.5 py-1 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition"
                                    >
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('payroll.holidays.cancel', $holiday) }}" class="inline-block" onsubmit="return confirm('{{ $holiday->isActive() ? 'Cancel' : 'Restore' }} holiday {{ $holiday->name }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <button 
                                            type="submit" 
                                            class="inline-flex items-center px-2.5 py-1 {{ $holiday->isActive() ? 'text-amber-700 bg-amber-50 hover:bg-amber-100' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }} rounded-md transition"
                                        >
                                            {{ $holiday->isActive() ? 'Cancel' : 'Restore' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('payroll.holidays.destroy', $holiday) }}" class="inline-block" onsubmit="return confirm('Delete holiday {{ $holiday->name }} permanently?')">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="inline-flex items-center px-2 py-1 text-rose-600 hover:text-rose-800 transition"
                                            title="Delete Permanently"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">
                                No company holidays declared for {{ $selectedYear }}. Click "Add Holiday" to declare a holiday.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="block md:hidden space-y-3">
        @forelse($holidays as $holiday)
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-xs space-y-3 {{ ! $holiday->isActive() ? 'opacity-60 bg-gray-50' : '' }}">
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-gray-900">{{ $holiday->name }}</h4>
                        <span class="text-xs text-gray-500 font-mono">
                            {{ $holiday->start_date->format('d M Y') }}
                            @if(! $holiday->start_date->isSameDay($holiday->end_date))
                                &ndash; {{ $holiday->end_date->format('d M Y') }}
                            @endif
                        </span>
                    </div>
                    <div>
                        @if($holiday->isPaid())
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Paid</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-600">Unpaid</span>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 text-xs">
                    <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">
                        {{ $holiday->isFullDay() ? 'Full Day' : 'Half Day' }}
                    </span>
                    <span class="px-2 py-0.5 rounded bg-slate-100 text-gray-800">
                        {{ $holiday->branch?->branch_name ?? 'All Branches' }}
                    </span>
                    <span class="px-2 py-0.5 rounded bg-slate-100 text-gray-800">
                        {{ $holiday->applies_to_all ? 'All Employees' : $holiday->employees->count() . ' Selected' }}
                    </span>
                </div>

                @if(Auth::user()->isAdmin())
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="openEdit({{ json_encode($holiday) }})"
                            class="px-2.5 py-1 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition"
                        >
                            Edit
                        </button>
                        <form method="POST" action="{{ route('payroll.holidays.cancel', $holiday) }}" onsubmit="return confirm('Change status for {{ $holiday->name }}?')">
                            @csrf
                            @method('PATCH')
                            <button 
                                type="submit" 
                                class="px-2.5 py-1 text-xs font-semibold {{ $holiday->isActive() ? 'text-amber-700 bg-amber-50' : 'text-emerald-700 bg-emerald-50' }} rounded-md transition"
                            >
                                {{ $holiday->isActive() ? 'Cancel' : 'Restore' }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 p-6 text-center text-gray-400 text-xs">
                No company holidays declared for {{ $selectedYear }}.
            </div>
        @endforelse
    </div>

    <!-- Modal 1: Add Holiday -->
    <div 
        x-show="showCreateModal" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;"
    >
        <div 
            @click.away="showCreateModal = false"
            class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-gray-200 space-y-4"
            x-data="{ appliesToAll: true, isRange: false }"
        >
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Add Company Holiday</h3>
                <button type="button" @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('payroll.holidays.store') }}" class="space-y-4">
                @csrf

                <!-- Holiday Name -->
                <div>
                    <label for="create_name" class="block text-xs font-semibold text-gray-700 mb-1">
                        Holiday Name <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="create_name" 
                        required 
                        placeholder="e.g. Deepavali Festival, Sinhala New Year" 
                        class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                    >
                </div>

                <!-- Date or Date Range -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-semibold text-gray-700">Date Configuration <span class="text-rose-500">*</span></label>
                        <label class="flex items-center space-x-1 text-xs text-gray-600 cursor-pointer">
                            <input type="checkbox" x-model="isRange" class="rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849]">
                            <span>Multi-day Date Range</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="create_start_date" class="block text-[11px] text-gray-500 mb-0.5">Start Date</label>
                            <input 
                                type="date" 
                                name="start_date" 
                                id="create_start_date" 
                                required 
                                value="{{ today()->toDateString() }}" 
                                class="block w-full py-1.5 px-3 text-xs text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"
                            >
                        </div>
                        <div x-show="isRange">
                            <label for="create_end_date" class="block text-[11px] text-gray-500 mb-0.5">End Date</label>
                            <input 
                                type="date" 
                                name="end_date" 
                                id="create_end_date" 
                                class="block w-full py-1.5 px-3 text-xs text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"
                            >
                        </div>
                    </div>
                </div>

                <!-- Duration & Type -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Duration</label>
                        <select 
                            name="duration" 
                            class="block w-full py-1.5 px-2.5 text-xs text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]"
                        >
                            <option value="full_day">Full Day</option>
                            <option value="half_day">Half Day</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Payment Type</label>
                        <select 
                            name="holiday_type" 
                            class="block w-full py-1.5 px-2.5 text-xs text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]"
                        >
                            <option value="paid">Paid Holiday</option>
                            <option value="unpaid">Unpaid Holiday</option>
                        </select>
                    </div>
                </div>

                <!-- Branch Scope -->
                <div>
                    <label for="create_branch_id" class="block text-xs font-semibold text-gray-700 mb-1">Applicable Branch</label>
                    <select 
                        name="branch_id" 
                        id="create_branch_id" 
                        class="block w-full py-1.5 px-2.5 text-xs text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#0B1849]"
                    >
                        <option value="">All Branches</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->branch_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Coverage: All vs Selected Staff -->
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-gray-700">Applicable Employees</label>
                    <div class="flex items-center space-x-4 text-xs">
                        <label class="flex items-center space-x-1.5 cursor-pointer">
                            <input type="radio" name="applies_to_all" value="1" x-model="appliesToAll" :value="true" class="text-[#0B1849] focus:ring-[#0B1849]">
                            <span>All Employees</span>
                        </label>
                        <label class="flex items-center space-x-1.5 cursor-pointer">
                            <input type="radio" name="applies_to_all" value="0" x-model="appliesToAll" :value="false" class="text-[#0B1849] focus:ring-[#0B1849]">
                            <span>Selected Staff Only</span>
                        </label>
                    </div>

                    <!-- Staff Selection Box -->
                    <div x-show="! appliesToAll" class="border border-gray-200 rounded-lg p-2 max-h-36 overflow-y-auto space-y-1 bg-gray-50/50" style="display: none;">
                        @foreach($employees as $emp)
                            <label class="flex items-center space-x-2 text-xs text-gray-700 p-1 hover:bg-white rounded cursor-pointer">
                                <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" class="rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849]">
                                <span>{{ $emp->name }} ({{ $emp->employee_number }}) &bull; {{ $emp->position ?: 'Staff' }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="create_notes" class="block text-xs font-semibold text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea 
                        name="notes" 
                        id="create_notes" 
                        rows="2" 
                        placeholder="Any additional remarks..." 
                        class="block w-full py-1.5 px-3 text-xs text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"
                    ></textarea>
                </div>

                <!-- Actions -->
                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button 
                        type="button" 
                        @click="showCreateModal = false" 
                        class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-semibold rounded-lg shadow-xs transition"
                    >
                        Save Holiday
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Edit Holiday -->
    <div 
        x-show="showEditModal" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;"
    >
        <div 
            @click.away="showEditModal = false"
            class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-gray-200 space-y-4"
        >
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Edit Company Holiday</h3>
                <button type="button" @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form :action="'{{ url('payroll/holidays') }}/' + editHoliday.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Holiday Name -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Holiday Name <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        name="name" 
                        x-model="editHoliday.name"
                        required 
                        class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"
                    >
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] text-gray-500 mb-0.5">Start Date <span class="text-rose-500">*</span></label>
                        <input 
                            type="date" 
                            name="start_date" 
                            x-model="editHoliday.start_date"
                            required 
                            class="block w-full py-1.5 px-3 text-xs text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"
                        >
                    </div>
                    <div>
                        <label class="block text-[11px] text-gray-500 mb-0.5">End Date</label>
                        <input 
                            type="date" 
                            name="end_date" 
                            x-model="editHoliday.end_date"
                            class="block w-full py-1.5 px-3 text-xs text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"
                        >
                    </div>
                </div>

                <!-- Duration & Type -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Duration</label>
                        <select 
                            name="duration" 
                            x-model="editHoliday.duration"
                            class="block w-full py-1.5 px-2.5 text-xs text-gray-900 border border-gray-300 rounded-lg bg-white"
                        >
                            <option value="full_day">Full Day</option>
                            <option value="half_day">Half Day</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Payment Type</label>
                        <select 
                            name="holiday_type" 
                            x-model="editHoliday.holiday_type"
                            class="block w-full py-1.5 px-2.5 text-xs text-gray-900 border border-gray-300 rounded-lg bg-white"
                        >
                            <option value="paid">Paid Holiday</option>
                            <option value="unpaid">Unpaid Holiday</option>
                        </select>
                    </div>
                </div>

                <!-- Branch Scope -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Applicable Branch</label>
                    <select 
                        name="branch_id" 
                        x-model="editHoliday.branch_id"
                        class="block w-full py-1.5 px-2.5 text-xs text-gray-900 border border-gray-300 rounded-lg bg-white"
                    >
                        <option value="">All Branches</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->branch_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Coverage -->
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-gray-700">Applicable Employees</label>
                    <div class="flex items-center space-x-4 text-xs">
                        <label class="flex items-center space-x-1.5 cursor-pointer">
                            <input type="radio" name="applies_to_all" value="1" x-model="editHoliday.applies_to_all" :value="true" class="text-[#0B1849] focus:ring-[#0B1849]">
                            <span>All Employees</span>
                        </label>
                        <label class="flex items-center space-x-1.5 cursor-pointer">
                            <input type="radio" name="applies_to_all" value="0" x-model="editHoliday.applies_to_all" :value="false" class="text-[#0B1849] focus:ring-[#0B1849]">
                            <span>Selected Staff Only</span>
                        </label>
                    </div>

                    <div x-show="! editHoliday.applies_to_all" class="border border-gray-200 rounded-lg p-2 max-h-36 overflow-y-auto space-y-1 bg-gray-50/50">
                        @foreach($employees as $emp)
                            <label class="flex items-center space-x-2 text-xs text-gray-700 p-1 hover:bg-white rounded cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    name="employee_ids[]" 
                                    value="{{ $emp->id }}" 
                                    :checked="editHoliday.employee_ids.includes({{ $emp->id }})"
                                    class="rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849]"
                                >
                                <span>{{ $emp->name }} ({{ $emp->employee_number }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Notes</label>
                    <textarea 
                        name="notes" 
                        x-model="editHoliday.notes"
                        rows="2" 
                        class="block w-full py-1.5 px-3 text-xs text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849]"
                    ></textarea>
                </div>

                <!-- Actions -->
                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button 
                        type="button" 
                        @click="showEditModal = false" 
                        class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-semibold rounded-lg shadow-xs transition"
                    >
                        Update Holiday
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
