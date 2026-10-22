@extends('layouts.app')

@section('title', 'Expense Records')

@section('content')
<div x-data="{ 
    showVoidModal: false,
    voidAction: '',
    expenseNumber: '',
    voidReason: '',

    showPayModal: false,
    payAction: '',
    payExpenseNumber: '',
    payCategoryName: '',
    payDescription: '',
    payTotalAmount: 0,
    payAlreadyPaid: 0,
    payOutstanding: 0,
    paymentAmount: '',
    paymentDate: '{{ today()->toDateString() }}',
    paymentMethod: 'cash',
    paymentReference: '',
    paymentNotes: '',
    submittingPayment: false,

    openPayModal(action, expNo, category, desc, total, paid, balance) {
        this.payAction = action;
        this.payExpenseNumber = expNo;
        this.payCategoryName = category;
        this.payDescription = desc;
        this.payTotalAmount = total;
        this.payAlreadyPaid = paid;
        this.payOutstanding = balance;
        this.paymentAmount = balance; // Pre-fill with outstanding for quick 1-click full payment
        this.paymentDate = '{{ today()->toDateString() }}';
        this.paymentMethod = 'cash';
        this.paymentReference = '';
        this.paymentNotes = '';
        this.submittingPayment = false;
        this.showPayModal = true;
    }
}">
    <!-- Page Header -->
    <x-page-header 
        title="Expense Records" 
        description="Record, track and pay shop operating expenses and business costs."
    >
        <x-slot:actions>
            <div class="flex flex-wrap items-center gap-2">
                @if(Auth::user()->isAdmin())
                    <a 
                        href="{{ route('expenses.records.export', request()->query()) }}" 
                        class="inline-flex items-center justify-center px-3.5 py-2 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-sm rounded-lg border border-gray-300 shadow-xs transition duration-150 cursor-pointer"
                    >
                        <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export
                    </a>
                @endif

                @if(Auth::user()->isAdmin() || Auth::user()->hasPermission('create_expenses'))
                    <a 
                        href="{{ route('expenses.staff.create') }}" 
                        class="inline-flex items-center justify-center px-3.5 py-2 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-sm rounded-lg border border-gray-300 shadow-xs transition duration-150 cursor-pointer"
                    >
                        <svg class="w-4 h-4 mr-1.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Staff Expense
                    </a>

                    <a 
                        href="{{ route('expenses.shop.create') }}" 
                        class="inline-flex items-center justify-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-sm rounded-lg shadow-xs transition duration-150 cursor-pointer"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Expense
                    </a>
                @endif
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- 3 Simple Summary Cards (Total Expenses, Paid, Balance) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <!-- Total Expenses -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Expenses</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">LKR {{ number_format($totalExpenses, 2) }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Recorded operating expenses</p>
                </div>
                <div class="p-3 bg-blue-50 text-[#0B1849] rounded-xl">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Paid Amount -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Paid Amount</p>
                    <h3 class="text-2xl font-bold text-emerald-700 mt-1">LKR {{ number_format($totalPaid, 2) }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Actual cash & bank outflow</p>
                </div>
                <div class="p-3 bg-emerald-50 text-emerald-700 rounded-xl">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Outstanding Balance -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Outstanding Balance</p>
                    <h3 class="text-2xl font-bold text-rose-700 mt-1">LKR {{ number_format($totalOutstanding, 2) }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Pending payments to be settled</p>
                </div>
                <div class="p-3 bg-rose-50 text-rose-700 rounded-xl">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="mb-6 bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
        <form method="GET" action="{{ route('expenses.records.index') }}" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Search Keyword -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Search</label>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search }}" 
                        placeholder="Expense #, description, ref..." 
                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white placeholder-gray-400"
                    >
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Expense Category</label>
                    <select 
                        name="category_id" 
                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string)$categoryId === (string)$cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Payment Status -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Payment Status</label>
                    <select 
                        name="payment_status" 
                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                        <option value="all">All Payment Statuses</option>
                        <option value="paid" {{ $paymentStatus === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="partially_paid" {{ $paymentStatus === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                        <option value="unpaid" {{ $paymentStatus === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    </select>
                </div>

                <!-- Branch Filter -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Branch</label>
                    <select 
                        name="branch_id" 
                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                        @if(Auth::user()->isAdmin())
                            <option value="all" {{ $branchId === 'all' || empty($branchId) ? 'selected' : '' }}>All Branches</option>
                        @endif
                        @foreach($accessibleBranches as $b)
                            <option value="{{ $b->id }}" {{ (string)$branchId === (string)$b->id ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3 pt-1">
                <!-- From Date -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">From Date</label>
                    <input 
                        type="date" 
                        name="from_date" 
                        value="{{ $fromDate }}" 
                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                </div>

                <!-- To Date -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">To Date</label>
                    <input 
                        type="date" 
                        name="to_date" 
                        value="{{ $toDate }}" 
                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                </div>

                <!-- Record Status -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Record Status</label>
                    <select 
                        name="status" 
                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active Only</option>
                        <option value="voided" {{ $status === 'voided' ? 'selected' : '' }}>Voided Records</option>
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Records</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-end gap-2">
                    <button 
                        type="submit" 
                        class="w-full py-2 px-4 bg-[#0B1849] hover:bg-[#15286e] text-white text-sm font-semibold rounded-lg transition shadow-xs cursor-pointer"
                    >
                        Filter
                    </button>
                    @if(!empty($search) || !empty($categoryId) || ($paymentStatus && $paymentStatus !== 'all') || !empty($fromDate) || !empty($toDate) || $status !== 'active')
                        <a 
                            href="{{ route('expenses.records.index') }}" 
                            class="py-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition text-center shrink-0"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- EXPENSE RECORDS: Desktop Table (Section 3) -->
    <div class="hidden md:block bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
        <div class="responsive-table-wrapper">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50/80 text-xs font-bold text-gray-600 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5">Date</th>
                        <th class="px-4 py-3.5">Expense Category</th>
                        <th class="px-4 py-3.5">Description</th>
                        <th class="px-4 py-3.5 text-right">Total Amount</th>
                        <th class="px-4 py-3.5 text-right">Paid Amount</th>
                        <th class="px-4 py-3.5 text-right">Balance</th>
                        <th class="px-4 py-3.5 text-center">Payment Status</th>
                        <th class="px-4 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-gray-50/70 transition {{ $expense->isVoided() ? 'opacity-60 bg-gray-50/40' : '' }}">
                            <!-- Date -->
                            <td class="px-4 py-3.5 whitespace-nowrap text-xs text-gray-600">
                                <div class="font-semibold text-gray-900">{{ $expense->expense_date->format('d M Y') }}</div>
                                <div class="text-[11px] text-gray-400 font-mono">{{ $expense->expense_number }}</div>
                            </td>

                            <!-- Expense Category -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="font-semibold text-gray-900">{{ $expense->category ? $expense->category->name : '—' }}</div>
                                @if($expense->branch)
                                    <span class="inline-block text-[10px] text-gray-500 font-medium bg-gray-100 px-1.5 py-0.5 rounded">
                                        {{ $expense->branch->name }}
                                    </span>
                                @endif
                                @if($expense->isSalaryAdvance())
                                    <span class="inline-block text-[10px] text-indigo-700 font-medium bg-indigo-50 px-1.5 py-0.5 rounded ml-1">
                                        Salary Advance
                                    </span>
                                @endif
                            </td>

                            <!-- Description -->
                            <td class="px-4 py-3.5 text-xs text-gray-600 max-w-xs truncate" title="{{ $expense->description }}">
                                {{ $expense->description ?: '—' }}
                                @if($expense->employee)
                                    <span class="block text-[11px] text-gray-400 mt-0.5">Staff: {{ $expense->employee->name }}</span>
                                @endif
                            </td>

                            <!-- Total Amount -->
                            <td class="px-4 py-3.5 whitespace-nowrap text-right font-bold text-gray-900">
                                LKR {{ number_format($expense->amount, 2) }}
                            </td>

                            <!-- Paid Amount -->
                            <td class="px-4 py-3.5 whitespace-nowrap text-right font-semibold text-emerald-700">
                                LKR {{ number_format($expense->paid_amount, 2) }}
                            </td>

                            <!-- Balance -->
                            <td class="px-4 py-3.5 whitespace-nowrap text-right font-bold {{ (float)$expense->balance_amount > 0 ? 'text-rose-700' : 'text-gray-400' }}">
                                LKR {{ number_format($expense->balance_amount, 2) }}
                            </td>

                            <!-- Payment Status -->
                            <td class="px-4 py-3.5 whitespace-nowrap text-center">
                                @if($expense->isVoided())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200" title="Voided: {{ $expense->void_reason }}">
                                        Voided
                                    </span>
                                @elseif($expense->isPaid())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-emerald-500"></span> Paid
                                    </span>
                                @elseif($expense->isPartiallyPaid())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-amber-500"></span> Partially Paid
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-rose-500"></span> Unpaid
                                    </span>
                                @endif
                            </td>

                            <!-- Actions (View, Edit, Pay) -->
                            <td class="px-4 py-3.5 text-right whitespace-nowrap space-x-1.5 text-xs">
                                <!-- View -->
                                <a 
                                    href="{{ route('expenses.records.show', $expense) }}" 
                                    class="inline-flex items-center px-2.5 py-1 font-semibold rounded text-gray-700 bg-gray-100 hover:bg-gray-200 transition"
                                >
                                    View
                                </a>

                                <!-- Edit -->
                                @if(Auth::user()->isAdmin() && $expense->isActive())
                                    <a 
                                        href="{{ route('expenses.records.edit', $expense) }}" 
                                        class="inline-flex items-center px-2.5 py-1 font-semibold rounded text-[#0B1849] bg-[#EEF1FA] hover:bg-[#d8e0f5] transition"
                                    >
                                        Edit
                                    </a>
                                @endif

                                <!-- Pay: Show Pay only when an expense has an outstanding balance -->
                                @if($expense->hasOutstandingBalance() && (Auth::user()->isAdmin() || Auth::user()->hasPermission('create_expenses')))
                                    <button 
                                        type="button" 
                                        @click="openPayModal(
                                            '{{ route('expenses.records.payments.store', $expense) }}',
                                            '{{ $expense->expense_number }}',
                                            '{{ addslashes($expense->category ? $expense->category->name : 'Expense') }}',
                                            '{{ addslashes($expense->description ?? '') }}',
                                            {{ (float)$expense->amount }},
                                            {{ (float)$expense->paid_amount }},
                                            {{ (float)$expense->balance_amount }}
                                        )"
                                        class="inline-flex items-center px-2.5 py-1 font-semibold rounded text-white bg-emerald-600 hover:bg-emerald-700 shadow-xs transition cursor-pointer"
                                    >
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Pay
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <div class="max-w-sm mx-auto">
                                    <svg class="mx-auto h-10 w-10 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <h4 class="text-sm font-semibold text-gray-900">No expense records found</h4>
                                    <p class="mt-1 text-xs text-gray-500">Use the "Add Expense" button above to record a new business expense.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>

    <!-- EXPENSE RECORDS: Mobile Friendly Cards -->
    <div class="md:hidden space-y-3">
        @forelse($expenses as $expense)
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs space-y-3 {{ $expense->isVoided() ? 'opacity-60 bg-gray-50/40' : '' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-mono font-bold text-[#0B1849]">{{ $expense->expense_number }}</span>
                        <div class="text-xs text-gray-500">{{ $expense->expense_date->format('d M Y') }}</div>
                    </div>
                    <div>
                        @if($expense->isVoided())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                Voided
                            </span>
                        @elseif($expense->isPaid())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Paid
                            </span>
                        @elseif($expense->isPartiallyPaid())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                Partially Paid
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                Unpaid
                            </span>
                        @endif
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                    <div>
                        <div class="text-sm font-bold text-gray-900">{{ $expense->category ? $expense->category->name : '—' }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            @if($expense->branch)
                                <span>{{ $expense->branch->name }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-500">Total: <span class="font-bold text-gray-900">LKR {{ number_format($expense->amount, 2) }}</span></div>
                        <div class="text-xs text-emerald-700 font-semibold">Paid: LKR {{ number_format($expense->paid_amount, 2) }}</div>
                        @if((float)$expense->balance_amount > 0)
                            <div class="text-xs text-rose-700 font-bold">Balance: LKR {{ number_format($expense->balance_amount, 2) }}</div>
                        @endif
                    </div>
                </div>

                @if($expense->description)
                    <p class="text-xs text-gray-600 italic bg-gray-50 p-2 rounded-lg">
                        {{ $expense->description }}
                    </p>
                @endif

                <div class="pt-2 border-t border-gray-100 flex items-center justify-end space-x-2 text-xs">
                    <a 
                        href="{{ route('expenses.records.show', $expense) }}" 
                        class="px-3 py-1.5 font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition"
                    >
                        View
                    </a>

                    @if(Auth::user()->isAdmin() && $expense->isActive())
                        <a 
                            href="{{ route('expenses.records.edit', $expense) }}" 
                            class="px-3 py-1.5 font-semibold rounded-lg bg-[#EEF1FA] text-[#0B1849] hover:bg-[#d8e0f5] transition"
                        >
                            Edit
                        </a>
                    @endif

                    @if($expense->hasOutstandingBalance() && (Auth::user()->isAdmin() || Auth::user()->hasPermission('create_expenses')))
                        <button 
                            type="button" 
                            @click="openPayModal(
                                '{{ route('expenses.records.payments.store', $expense) }}',
                                '{{ $expense->expense_number }}',
                                '{{ addslashes($expense->category ? $expense->category->name : 'Expense') }}',
                                '{{ addslashes($expense->description ?? '') }}',
                                {{ (float)$expense->amount }},
                                {{ (float)$expense->paid_amount }},
                                {{ (float)$expense->balance_amount }}
                            )"
                            class="px-3 py-1.5 font-semibold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 shadow-xs transition"
                        >
                            Pay
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white p-8 rounded-xl border border-gray-200 text-center text-gray-500">
                <p class="text-sm font-semibold text-gray-900">No expense records found</p>
                <p class="text-xs text-gray-500 mt-1">Try adjusting your filters or record a new expense.</p>
            </div>
        @endforelse

        @if($expenses->hasPages())
            <div class="pt-2">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>

    <!-- PAY EXPENSE MODAL (Section 5) -->
    <div 
        x-show="showPayModal" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div 
                x-show="showPayModal" 
                @click="showPayModal = false"
                class="fixed inset-0 transition-opacity bg-gray-900/50 backdrop-blur-xs"
            ></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div 
                x-show="showPayModal"
                class="inline-block w-full max-w-[calc(100vw-2rem)] sm:max-w-lg p-4 sm:p-6 my-8 text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl border border-gray-100 max-h-[88vh] overflow-y-auto"
            >
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-[#0B1849] flex items-center">
                        <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Pay Expense: <span class="ml-1 text-[#0B1849]" x-text="payExpenseNumber"></span>
                    </h3>
                    <button @click="showPayModal = false" type="button" class="text-gray-400 hover:text-gray-600 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Expense Summary Info Box (Section 5 requirements) -->
                <div class="my-4 p-4 rounded-xl bg-gray-50 border border-gray-200 text-sm space-y-2">
                    <div class="flex justify-between items-center text-gray-700">
                        <span class="font-medium">Expense Category:</span>
                        <span class="font-bold text-gray-900" x-text="payCategoryName"></span>
                    </div>
                    <template x-if="payDescription">
                        <div class="flex justify-between items-start text-xs text-gray-500">
                            <span class="font-medium">Description:</span>
                            <span class="text-right italic max-w-xs truncate" x-text="payDescription"></span>
                        </div>
                    </template>
                    <div class="flex justify-between items-center text-gray-700 pt-1 border-t border-gray-200">
                        <span class="font-medium">Total Amount:</span>
                        <span class="font-bold text-gray-900">LKR <span x-text="Number(payTotalAmount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span>
                    </div>
                    <div class="flex justify-between items-center text-emerald-700">
                        <span class="font-medium">Already Paid:</span>
                        <span class="font-bold">LKR <span x-text="Number(payAlreadyPaid).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span>
                    </div>
                    <div class="flex justify-between items-center text-rose-700 pt-1 border-t border-gray-200">
                        <span class="font-bold">Outstanding:</span>
                        <span class="font-extrabold text-base">LKR <span x-text="Number(payOutstanding).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span>
                    </div>
                </div>

                <!-- Payment Form -->
                <form 
                    :action="payAction" 
                    method="POST" 
                    @submit="submittingPayment = true"
                    class="space-y-4"
                >
                    @csrf

                    <!-- Payment Method -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Payment Method <span class="text-rose-500">*</span>
                        </label>
                        <select 
                            name="payment_method" 
                            x-model="paymentMethod" 
                            required 
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                        >
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="card">Card</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>

                    <!-- Payment Amount -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Payment Amount (LKR) <span class="text-rose-500">*</span>
                            </label>
                            <button 
                                type="button" 
                                @click="paymentAmount = payOutstanding"
                                class="text-[11px] font-semibold text-[#0B1849] hover:underline cursor-pointer"
                            >
                                Pay Full Outstanding
                            </button>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs font-bold text-gray-400">
                                LKR
                            </span>
                            <input 
                                type="number" 
                                name="payment_amount" 
                                x-model="paymentAmount" 
                                step="0.01" 
                                min="0.01" 
                                :max="payOutstanding"
                                required 
                                placeholder="0.00" 
                                class="block w-full pl-12 pr-3.5 py-2.5 text-sm font-semibold text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                            >
                        </div>
                        <template x-if="Number(paymentAmount) > Number(payOutstanding)">
                            <p class="mt-1 text-xs text-rose-600 font-medium">Payment amount cannot exceed the outstanding balance.</p>
                        </template>
                    </div>

                    <!-- Payment Date -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Payment Date <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="payment_date" 
                            x-model="paymentDate" 
                            required 
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                        >
                    </div>

                    <!-- Reference (optional) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Reference <span class="text-gray-400 font-normal">(Optional)</span>
                        </label>
                        <input 
                            type="text" 
                            name="payment_reference" 
                            x-model="paymentReference" 
                            placeholder="e.g. Cheque #, Bank slip ref, Receipt #" 
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                        >
                    </div>

                    <!-- Notes (optional) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Notes <span class="text-gray-400 font-normal">(Optional)</span>
                        </label>
                        <input 
                            type="text" 
                            name="notes" 
                            x-model="paymentNotes" 
                            placeholder="e.g. 1st installment, final settlement" 
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                        >
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 flex items-center justify-end space-x-3 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="showPayModal = false" 
                            class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            :disabled="submittingPayment || Number(paymentAmount) <= 0 || Number(paymentAmount) > Number(payOutstanding)"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg shadow-xs transition cursor-pointer flex items-center"
                        >
                            <span x-show="!submittingPayment">Save Payment</span>
                            <span x-show="submittingPayment" style="display: none;">Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
