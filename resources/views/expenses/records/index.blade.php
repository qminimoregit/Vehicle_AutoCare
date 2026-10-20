@extends('layouts.app')

@section('title', 'Expense Records')

@section('content')
<div x-data="{ 
    showVoidModal: false,
    voidAction: '',
    expenseNumber: '',
    voidReason: ''
}">
    <!-- Page Header -->
    <x-page-header 
        title="Expense Records" 
        description="Track all shop operating expenses and staff payments."
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
                        class="inline-flex items-center justify-center px-4 py-2 bg-indigo-700 hover:bg-indigo-800 text-white font-semibold text-sm rounded-lg shadow-xs transition duration-150 cursor-pointer"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Staff Expense
                    </a>

                    <a 
                        href="{{ route('expenses.shop.create') }}" 
                        class="inline-flex items-center justify-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-sm rounded-lg shadow-xs transition duration-150 cursor-pointer"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Shop Expense
                    </a>
                @endif
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- 3 Simple Summary Cards (Calculated from Real Records) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <!-- Total Expenses -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Expenses</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">LKR {{ number_format($totalExpenses, 2) }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Active records for current filter</p>
                </div>
                <div class="p-3 bg-blue-50 text-[#0B1849] rounded-xl">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Staff Payments -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Staff Payments</p>
                    <h3 class="text-2xl font-bold text-indigo-700 mt-1">LKR {{ number_format($staffTotal, 2) }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Advances, welfare & staff costs</p>
                </div>
                <div class="p-3 bg-indigo-50 text-indigo-700 rounded-xl">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Shop Expenses -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Shop Operating Costs</p>
                    <h3 class="text-2xl font-bold text-emerald-700 mt-1">LKR {{ number_format($shopTotal, 2) }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Electricity, water, repairs, chemicals</p>
                </div>
                <div class="p-3 bg-emerald-50 text-emerald-700 rounded-xl">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
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
                    <div class="relative">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ $search }}" 
                            placeholder="Expense #, employee, memo..." 
                            class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white placeholder-gray-400"
                        >
                    </div>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Category</label>
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

                <!-- Type -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Expense Type</label>
                    <select 
                        name="expense_type" 
                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                        <option value="all">All Types</option>
                        <option value="staff" {{ $expenseType === 'staff' ? 'selected' : '' }}>Staff Expense</option>
                        <option value="shop" {{ $expenseType === 'shop' ? 'selected' : '' }}>Shop Expense</option>
                    </select>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Payment Method</label>
                    <select 
                        name="payment_method" 
                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                        <option value="all">All Methods</option>
                        <option value="cash" {{ $paymentMethod === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="bank_transfer" {{ $paymentMethod === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="card" {{ $paymentMethod === 'card' ? 'selected' : '' }}>Card</option>
                        <option value="other" {{ $paymentMethod === 'other' ? 'selected' : '' }}>Other</option>
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

                <!-- Status -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</label>
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
                    @if(!empty($search) || !empty($categoryId) || ($expenseType && $expenseType !== 'all') || ($paymentMethod && $paymentMethod !== 'all') || !empty($fromDate) || !empty($toDate) || $status !== 'active')
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

    <!-- EXPENSE LIST: Desktop Table -->
    <div class="hidden md:block bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50/80 text-xs font-bold text-gray-600 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Date</th>
                        <th class="px-5 py-3.5">Expense #</th>
                        <th class="px-5 py-3.5">Category</th>
                        <th class="px-5 py-3.5">Type / Employee</th>
                        <th class="px-5 py-3.5">Details</th>
                        <th class="px-5 py-3.5">Payment</th>
                        <th class="px-5 py-3.5 text-right">Amount</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-gray-50/70 transition {{ $expense->isVoided() ? 'opacity-60 bg-gray-50/40' : '' }}">
                            <!-- Date -->
                            <td class="px-5 py-3.5 whitespace-nowrap text-xs text-gray-600">
                                <div class="font-semibold text-gray-900">{{ $expense->expense_date->format('d M Y') }}</div>
                                <div class="text-[11px] text-gray-400">{{ $expense->related_month }}</div>
                            </td>

                            <!-- Expense Number -->
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <a href="{{ route('expenses.records.show', $expense) }}" class="font-mono text-xs font-bold text-[#0B1849] hover:underline">
                                    {{ $expense->expense_number }}
                                </a>
                            </td>

                            <!-- Category -->
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="font-semibold text-gray-900">{{ $expense->category ? $expense->category->name : '—' }}</span>
                                @if($expense->isSalaryAdvance())
                                    <span class="block text-[11px] text-indigo-600 font-medium">Payroll Advance</span>
                                @endif
                            </td>

                            <!-- Type / Employee -->
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @if($expense->isStaff())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        Staff
                                    </span>
                                    <div class="text-xs font-medium text-gray-800 mt-0.5">
                                        {{ $expense->employee ? $expense->employee->name : '—' }}
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Shop
                                    </span>
                                @endif
                            </td>

                            <!-- Details / Memo -->
                            <td class="px-5 py-3.5 text-xs text-gray-600 max-w-xs truncate" title="{{ $expense->description }}">
                                {{ $expense->description ?: '—' }}
                            </td>

                            <!-- Payment Method -->
                            <td class="px-5 py-3.5 whitespace-nowrap text-xs text-gray-600">
                                <div class="font-medium text-gray-800">{{ ucwords(str_replace('_', ' ', $expense->payment_method)) }}</div>
                                @if($expense->payment_reference)
                                    <div class="text-[11px] text-gray-400 truncate max-w-[120px]">{{ $expense->payment_reference }}</div>
                                @endif
                            </td>

                            <!-- Amount -->
                            <td class="px-5 py-3.5 whitespace-nowrap text-right font-bold text-gray-900">
                                LKR {{ number_format($expense->amount, 2) }}
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-3.5 whitespace-nowrap text-center">
                                @if($expense->isActive())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200" title="Voided: {{ $expense->void_reason }}">
                                        Voided
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-3.5 text-right whitespace-nowrap space-x-1.5 text-xs">
                                <a 
                                    href="{{ route('expenses.records.show', $expense) }}" 
                                    class="inline-flex items-center px-2.5 py-1 font-semibold rounded text-gray-700 bg-gray-100 hover:bg-gray-200 transition"
                                >
                                    View
                                </a>

                                @if($expense->receipt_path)
                                    <a 
                                        href="{{ route('expenses.records.receipt', $expense) }}" 
                                        target="_blank" 
                                        class="inline-flex items-center px-2.5 py-1 font-semibold rounded text-blue-700 bg-blue-50 hover:bg-blue-100 transition"
                                        title="View Receipt Attachment"
                                    >
                                        Receipt
                                    </a>
                                @endif

                                @if(Auth::user()->isAdmin() && $expense->isActive())
                                    <a 
                                        href="{{ route('expenses.records.edit', $expense) }}" 
                                        class="inline-flex items-center px-2.5 py-1 font-semibold rounded text-[#0B1849] bg-[#EEF1FA] hover:bg-[#d8e0f5] transition"
                                    >
                                        Edit
                                    </a>

                                    <button 
                                        type="button" 
                                        @click="
                                            expenseNumber = '{{ $expense->expense_number }}';
                                            voidAction = '{{ route('expenses.records.void', $expense) }}';
                                            voidReason = '';
                                            showVoidModal = true;
                                        "
                                        class="inline-flex items-center px-2.5 py-1 font-semibold rounded text-rose-700 bg-rose-50 hover:bg-rose-100 transition"
                                    >
                                        Void
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                <div class="max-w-sm mx-auto">
                                    <svg class="mx-auto h-10 w-10 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <h4 class="text-sm font-semibold text-gray-900">No expense records found</h4>
                                    <p class="mt-1 text-xs text-gray-500">Use the buttons above to record a new staff payment or shop expense.</p>
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

    <!-- EXPENSE LIST: Mobile Friendly Cards -->
    <div class="md:hidden space-y-3">
        @forelse($expenses as $expense)
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs space-y-3 {{ $expense->isVoided() ? 'opacity-60 bg-gray-50/40' : '' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-mono font-bold text-[#0B1849]">{{ $expense->expense_number }}</span>
                        <div class="text-xs text-gray-500">{{ $expense->expense_date->format('d M Y') }}</div>
                    </div>
                    <div>
                        @if($expense->isActive())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                Voided
                            </span>
                        @endif
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                    <div>
                        <div class="text-sm font-bold text-gray-900">{{ $expense->category ? $expense->category->name : '—' }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            @if($expense->isStaff())
                                Staff: <span class="font-medium text-gray-800">{{ $expense->employee ? $expense->employee->name : '—' }}</span>
                            @else
                                <span class="font-medium text-emerald-700">Shop Expense</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-base font-extrabold text-gray-900">LKR {{ number_format($expense->amount, 2) }}</div>
                        <div class="text-[11px] text-gray-500">{{ ucwords(str_replace('_', ' ', $expense->payment_method)) }}</div>
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

                    @if($expense->receipt_path)
                        <a 
                            href="{{ route('expenses.records.receipt', $expense) }}" 
                            target="_blank" 
                            class="px-3 py-1.5 font-semibold rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition"
                        >
                            Receipt
                        </a>
                    @endif

                    @if(Auth::user()->isAdmin() && $expense->isActive())
                        <a 
                            href="{{ route('expenses.records.edit', $expense) }}" 
                            class="px-3 py-1.5 font-semibold rounded-lg bg-[#EEF1FA] text-[#0B1849] hover:bg-[#d8e0f5] transition"
                        >
                            Edit
                        </a>

                        <button 
                            type="button" 
                            @click="
                                expenseNumber = '{{ $expense->expense_number }}';
                                voidAction = '{{ route('expenses.records.void', $expense) }}';
                                voidReason = '';
                                showVoidModal = true;
                            "
                            class="px-3 py-1.5 font-semibold rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 transition"
                        >
                            Void
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

    <!-- VOID CONFIRMATION MODAL -->
    <div 
        x-show="showVoidModal" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div 
                x-show="showVoidModal" 
                @click="showVoidModal = false"
                class="fixed inset-0 transition-opacity bg-gray-900/50 backdrop-blur-xs"
            ></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div 
                x-show="showVoidModal"
                class="inline-block w-full max-w-md p-6 my-8 text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl border border-gray-100"
            >
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-rose-700 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Void Financial Record
                    </h3>
                    <button @click="showVoidModal = false" type="button" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form :action="voidAction" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <p class="text-sm text-gray-600">
                        Are you sure you want to void expense <strong class="text-gray-900" x-text="expenseNumber"></strong>?
                    </p>
                    <p class="text-xs text-amber-700 bg-amber-50 p-2.5 rounded-lg border border-amber-200">
                        <strong>Warning:</strong> Confirmed financial records are never deleted permanently. Voiding preserves audit history and cancels any linked pending salary advances.
                    </p>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Reason for Voiding *</label>
                        <textarea 
                            name="void_reason" 
                            x-model="voidReason" 
                            rows="2" 
                            required 
                            placeholder="e.g. Duplicate entry, incorrect amount recorded, transaction cancelled"
                            class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                        ></textarea>
                    </div>

                    <div class="pt-3 flex items-center justify-end space-x-3 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="showVoidModal = false" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2 text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-xs transition"
                        >
                            Confirm Void
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
