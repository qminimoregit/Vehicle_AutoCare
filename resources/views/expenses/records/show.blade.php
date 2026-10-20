@extends('layouts.app')

@section('title', 'Expense Details - ' . $expense->expense_number)

@section('content')
<div class="max-w-4xl mx-auto" x-data="{
    showVoidModal: false,
    voidReason: ''
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
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
        <!-- Card Top Bar with Amount -->
        <div class="p-6 bg-gray-50/60 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="font-mono text-sm font-bold text-gray-500">Record ID:</span>
                    <span class="font-mono text-sm font-extrabold text-[#0B1849]">{{ $expense->expense_number }}</span>
                    @if($expense->isActive())
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-emerald-500"></span> Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                            Voided
                        </span>
                    @endif
                </div>
                <div class="mt-1 text-xs text-gray-500">
                    Recorded on {{ $expense->created_at->format('d M Y, h:i A') }} by {{ $expense->creator ? $expense->creator->name : 'System' }}
                </div>
            </div>

            <div class="sm:text-right">
                <span class="text-xs uppercase font-bold text-gray-400">Total Amount</span>
                <div class="text-3xl font-extrabold text-gray-900">
                    LKR {{ number_format($expense->amount, 2) }}
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            <!-- Grid of Key Information -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
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

                <!-- Related Month -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Related Month</label>
                    <div class="mt-1 text-sm font-semibold text-gray-900">
                        {{ $expense->related_month }}
                    </div>
                </div>

                <!-- Expense Date -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Expense Date</label>
                    <div class="mt-1 text-sm font-semibold text-gray-900">
                        {{ $expense->expense_date->format('d F Y') }}
                    </div>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Payment Method</label>
                    <div class="mt-1 text-sm font-semibold text-gray-900">
                        {{ ucwords(str_replace('_', ' ', $expense->payment_method)) }}
                    </div>
                </div>

                <!-- Payment Date -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Payment Date</label>
                    <div class="mt-1 text-sm font-semibold text-gray-900">
                        {{ $expense->payment_date ? $expense->payment_date->format('d F Y') : $expense->expense_date->format('d F Y') }}
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
                        <span class="text-xs text-gray-400">Secure storage file: {{ basename($expense->receipt_path) }}</span>
                    </div>
                </div>
            @endif
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
