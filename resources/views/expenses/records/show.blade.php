@extends('layouts.app')

@section('title', 'Expense Details - ' . $expense->expense_number)

@section('content')
<div class="max-w-4xl mx-auto" x-data="{
    showVoidModal: false,
    voidReason: '',

    showPayModal: false,
    paymentAmount: '{{ (float)$expense->balance_amount }}',
    paymentDate: '{{ today()->toDateString() }}',
    paymentMethod: 'cash',
    paymentReference: '',
    paymentNotes: '',
    submittingPayment: false
}">
    <!-- Page Header -->
    <x-page-header 
        title="Expense {{ $expense->expense_number }}" 
        description="Detailed record of {{ $expense->isStaff() ? 'staff payment' : 'shop operating expense' }}."
    >
        <x-slot:actions>
            <div class="flex flex-wrap items-center gap-2">
                <a 
                    href="{{ route('expenses.records.index') }}" 
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-lg transition"
                >
                    &larr; Back to Records
                </a>

                @if($expense->receipt_path)
                    <a 
                        href="{{ route('expenses.records.receipt', $expense) }}" 
                        target="_blank" 
                        class="inline-flex items-center px-3.5 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold text-sm rounded-lg border border-blue-200 transition"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                        View Receipt
                    </a>
                @endif

                @if($expense->hasOutstandingBalance() && (Auth::user()->isAdmin() || Auth::user()->hasPermission('create_expenses')))
                    <button 
                        type="button" 
                        @click="showPayModal = true"
                        class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-lg shadow-xs transition cursor-pointer"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Record Payment
                    </button>
                @endif

                @if(Auth::user()->isAdmin() && $expense->isActive())
                    <a 
                        href="{{ route('expenses.records.edit', $expense) }}" 
                        class="inline-flex items-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-sm rounded-lg shadow-xs transition"
                    >
                        Edit Expense
                    </a>

                    <button 
                        type="button" 
                        @click="showVoidModal = true"
                        class="inline-flex items-center px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold text-sm rounded-lg border border-rose-200 transition cursor-pointer"
                    >
                        Void Record
                    </button>
                @endif
            </div>
        </x-slot:actions>
    </x-page-header>

    @if($expense->isVoided())
        <!-- Void Alert Banner -->
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start space-x-3">
            <svg class="w-5 h-5 text-rose-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
                <h5 class="font-bold text-rose-900">This financial record has been VOIDED</h5>
                <p class="mt-0.5 text-xs text-rose-700">
                    Voided on {{ $expense->voided_at ? $expense->voided_at->format('d M Y, h:i A') : '—' }} 
                    by {{ $expense->voider ? $expense->voider->name : 'Administrator' }}.
                </p>
                <p class="mt-1 text-xs text-rose-800 bg-white/70 p-2 rounded border border-rose-200">
                    <strong>Reason:</strong> {{ $expense->void_reason }}
                </p>
            </div>
        </div>
    @endif

    <!-- Main Details Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden mb-6">
        <!-- Card Top Bar with Financial Summary -->
        <div class="p-6 bg-gray-50/60 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="font-mono text-sm font-bold text-gray-500">Record ID:</span>
                    <span class="font-mono text-sm font-extrabold text-[#0B1849]">{{ $expense->expense_number }}</span>
                    @if($expense->isVoided())
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                            Voided
                        </span>
                    @elseif($expense->isPaid())
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-emerald-500"></span> Paid
                        </span>
                    @elseif($expense->isPartiallyPaid())
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-amber-500"></span> Partially Paid
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-rose-500"></span> Unpaid
                        </span>
                    @endif
                </div>
                <div class="mt-1 text-xs text-gray-500">
                    Recorded on {{ $expense->created_at->format('d M Y, h:i A') }} by {{ $expense->creator ? $expense->creator->name : 'System' }}
                    @if($expense->branch)
                        &bull; Branch: <span class="font-semibold text-gray-700">{{ $expense->branch->name }}</span>
                    @endif
                </div>
            </div>

            <!-- 3 Mini Metric Badges -->
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <span class="text-[10px] uppercase font-bold text-gray-400">Total Amount</span>
                    <div class="text-lg font-extrabold text-gray-900">
                        LKR {{ number_format($expense->amount, 2) }}
                    </div>
                </div>
                <div class="text-right border-l border-gray-200 pl-4">
                    <span class="text-[10px] uppercase font-bold text-gray-400">Paid Amount</span>
                    <div class="text-lg font-bold text-emerald-700">
                        LKR {{ number_format($expense->paid_amount, 2) }}
                    </div>
                </div>
                <div class="text-right border-l border-gray-200 pl-4">
                    <span class="text-[10px] uppercase font-bold text-gray-400">Balance</span>
                    <div class="text-lg font-extrabold {{ (float)$expense->balance_amount > 0 ? 'text-rose-700' : 'text-gray-400' }}">
                        LKR {{ number_format($expense->balance_amount, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            <!-- Grid of Key Information -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Category -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Expense Category</label>
                    <div class="mt-1 text-sm font-semibold text-gray-900">
                        {{ $expense->category ? $expense->category->name : '—' }}
                    </div>
                </div>

                <!-- Expense Type -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Expense Type</label>
                    <div class="mt-1">
                        @if($expense->isStaff())
                            <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                Staff Payment
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Shop Operating Expense
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Expense Date -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Expense Date</label>
                    <div class="mt-1 text-sm font-semibold text-gray-900">
                        {{ $expense->expense_date->format('d F Y') }}
                    </div>
                </div>

                <!-- Branch -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Branch</label>
                    <div class="mt-1 text-sm font-semibold text-gray-900">
                        {{ $expense->branch ? $expense->branch->name : 'Main Center' }}
                    </div>
                </div>
            </div>

            @if($expense->payment_reference)
                <!-- Payment Reference -->
                <div class="pt-4 border-t border-gray-100">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Payment Reference</label>
                    <div class="mt-1 text-sm font-mono text-gray-800 bg-gray-50 p-2.5 rounded-lg inline-block">
                        {{ $expense->payment_reference }}
                    </div>
                </div>
            @endif

            @if($expense->isStaff() && $expense->employee)
                <!-- Employee Details Section -->
                <div class="pt-6 border-t border-gray-100">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Employee Information</h4>
                    <div class="bg-[#F6F7F9] p-4 rounded-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <div class="text-sm font-bold text-gray-900">{{ $expense->employee->name }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">
                                Emp #: <span class="font-mono font-semibold">{{ $expense->employee->employee_number }}</span> | 
                                Role: {{ $expense->employee->designation ?? 'Staff Member' }}
                            </div>
                        </div>

                        @if($expense->advance)
                            <!-- Salary Advance Linkage -->
                            <div class="text-xs bg-white p-2.5 rounded-lg border border-indigo-200">
                                <span class="font-semibold text-indigo-900">Linked Salary Advance:</span>
                                <span class="font-mono font-bold text-indigo-700 ml-1">{{ $expense->advance->advance_number }}</span>
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ $expense->advance->status === 'deducted' ? 'bg-emerald-50 text-emerald-700' : ($expense->advance->status === 'cancelled' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                                    {{ ucfirst($expense->advance->status) }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Description / Comment -->
            <div class="pt-6 border-t border-gray-100">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Description / Comment</label>
                <div class="p-4 rounded-xl bg-gray-50 text-sm text-gray-800 leading-relaxed">
                    {{ $expense->description ?: 'No comment provided.' }}
                </div>
            </div>

            <!-- Receipt Attachment Preview -->
            @if($expense->receipt_path)
                <div class="pt-6 border-t border-gray-100">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Attached Receipt</label>
                    <div class="flex items-center space-x-3">
                        <a 
                            href="{{ route('expenses.records.receipt', $expense) }}" 
                            target="_blank" 
                            class="inline-flex items-center px-4 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold text-sm rounded-xl border border-blue-200 transition"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Open / Download Attachment
                        </a>
                        <span class="text-xs text-gray-400">File: {{ basename($expense->receipt_path) }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- PAYMENT HISTORY SECTION (Section 5) -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h4 class="text-base font-bold text-[#0B1849]">Payment History</h4>
                <p class="text-xs text-gray-500 mt-0.5">Chronological record of all payments made towards this expense.</p>
            </div>
            @if($expense->hasOutstandingBalance() && (Auth::user()->isAdmin() || Auth::user()->hasPermission('create_expenses')))
                <button 
                    type="button" 
                    @click="showPayModal = true"
                    class="inline-flex items-center px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-lg shadow-xs transition cursor-pointer"
                >
                    + Add Payment
                </button>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50/80 text-xs font-bold text-gray-600 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3">Payment #</th>
                        <th class="px-5 py-3">Date</th>
                        <th class="px-5 py-3">Method</th>
                        <th class="px-5 py-3">Reference</th>
                        <th class="px-5 py-3">Recorded By</th>
                        <th class="px-5 py-3">Notes</th>
                        <th class="px-5 py-3 text-right">Amount (LKR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($expense->payments as $payment)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="px-5 py-3 whitespace-nowrap font-mono text-xs font-bold text-[#0B1849]">
                                {{ $payment->payment_number }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-xs text-gray-700">
                                {{ $payment->payment_date->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-xs">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-xs text-gray-600 font-mono">
                                {{ $payment->reference_number ?: '—' }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-xs text-gray-700">
                                {{ $payment->creator ? $payment->creator->name : 'System' }}
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-500 max-w-xs truncate">
                                {{ $payment->notes ?: '—' }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-right font-bold text-emerald-700">
                                LKR {{ number_format($payment->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 text-xs">
                                No payments recorded yet. Current outstanding balance is <strong class="text-rose-600">LKR {{ number_format($expense->balance_amount, 2) }}</strong>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
                class="inline-block w-full max-w-lg p-6 my-8 text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl border border-gray-100"
            >
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-[#0B1849] flex items-center">
                        <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Pay Expense: {{ $expense->expense_number }}
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
                        <span class="font-medium">Expense:</span>
                        <span class="font-bold text-gray-900">{{ $expense->category ? $expense->category->name : 'Expense' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-gray-700 pt-1 border-t border-gray-200">
                        <span class="font-medium">Total Amount:</span>
                        <span class="font-bold text-gray-900">LKR {{ number_format($expense->amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-emerald-700">
                        <span class="font-medium">Already Paid:</span>
                        <span class="font-bold">LKR {{ number_format($expense->paid_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-rose-700 pt-1 border-t border-gray-200">
                        <span class="font-bold">Outstanding:</span>
                        <span class="font-extrabold text-base">LKR {{ number_format($expense->balance_amount, 2) }}</span>
                    </div>
                </div>

                <!-- Payment Form -->
                <form 
                    action="{{ route('expenses.records.payments.store', $expense) }}" 
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
                                @click="paymentAmount = '{{ (float)$expense->balance_amount }}'"
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
                                max="{{ (float)$expense->balance_amount }}"
                                required 
                                placeholder="0.00" 
                                class="block w-full pl-12 pr-3.5 py-2.5 text-sm font-semibold text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                            >
                        </div>
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
                            placeholder="e.g. 1st installment, balance settlement" 
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
                            :disabled="submittingPayment || Number(paymentAmount) <= 0 || Number(paymentAmount) > {{ (float)$expense->balance_amount }}"
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

                <form action="{{ route('expenses.records.void', $expense) }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <p class="text-sm text-gray-600">
                        Are you sure you want to void expense <strong class="text-gray-900">{{ $expense->expense_number }}</strong> of <strong>LKR {{ number_format($expense->amount, 2) }}</strong>?
                    </p>
                    <p class="text-xs text-amber-700 bg-amber-50 p-2.5 rounded-lg border border-amber-200">
                        <strong>Important:</strong> Financial records are never deleted permanently. Voiding records audit history and cancels any linked pending salary advances.
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
                            class="px-5 py-2 text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-xs transition cursor-pointer"
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
