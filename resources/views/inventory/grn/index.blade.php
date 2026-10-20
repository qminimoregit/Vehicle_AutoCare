@extends('layouts.app')

@section('title', 'Purchasing (GRN)')

@section('content')
    <div x-data="{
        paymentModalOpen: false,
        submitting: false,
        selectedGrn: {
            id: null,
            grn_number: '',
            supplier_name: '',
            supplier_invoice_number: '',
            total_amount: '0.00',
            total_amount_raw: 0,
            already_paid: '0.00',
            outstanding_balance: '0.00',
            outstanding_raw: 0,
            action_url: ''
        },
        paymentForm: {
            amount: '',
            payment_method: 'cash',
            payment_date: '{{ date('Y-m-d') }}',
            reference_number: '',
            notes: ''
        },
        openPaymentModal(data) {
            this.selectedGrn = data;
            this.paymentForm.amount = data.outstanding_raw > 0 ? Number(data.outstanding_raw).toFixed(2) : '';
            this.paymentForm.payment_method = 'cash';
            this.paymentForm.payment_date = '{{ date('Y-m-d') }}';
            this.paymentForm.reference_number = '';
            this.paymentForm.notes = '';
            this.submitting = false;
            this.paymentModalOpen = true;
        }
    }">
        <!-- Page Header -->
        <x-page-header 
            title="Purchasing (GRN)" 
            description="Manage goods received notes, vendor invoices, and stock receipts."
        >
            <x-slot:actions>
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                        Total: {{ $totalCount }} {{ Str::plural('GRN', $totalCount) }}
                    </span>

                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono">
                        Received: LKR {{ number_format($confirmedTotalValue, 2) }}
                    </span>

                    <a 
                        href="{{ route('inventory.grn.create') }}" 
                        class="inline-flex items-center justify-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-sm rounded-lg shadow-xs transition duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B1849]"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create GRN
                    </a>
                </div>
            </x-slot:actions>
        </x-page-header>

        <!-- Search Bar with Supplier & Status Filters -->
        <div class="mb-6 bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
            <form method="GET" action="{{ route('inventory.grn.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search text -->
                <div class="lg:col-span-2 relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search }}" 
                        placeholder="Search GRN #, Invoice #, Supplier..."
                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                    />
                </div>

                <!-- Supplier filter -->
                <div>
                    <select 
                        name="supplier_id" 
                        class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ $supplierId == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status filter -->
                <div>
                    <select 
                        name="status" 
                        class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                        <option value="">All Statuses</option>
                        <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed Only</option>
                        <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft Only</option>
                    </select>
                </div>

                <!-- Action buttons -->
                <div class="flex items-center space-x-2">
                    <button 
                        type="submit" 
                        class="flex-1 px-4 py-2 text-sm font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg transition shadow-xs text-center cursor-pointer"
                    >
                        Filter
                    </button>
                    @if($search || $supplierId || $status || $dateFrom || $dateTo)
                        <a 
                            href="{{ route('inventory.grn.index') }}" 
                            class="px-3 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- GRN Table -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                GRN Number
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Supplier
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Invoice # & Date
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Total (LKR)
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Stock Status
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Payment Status
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($grns as $grn)
                            <tr class="hover:bg-gray-50/70 transition">
                                <!-- GRN Number -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">
                                        <a href="{{ route('inventory.grn.show', $grn) }}" class="text-[#0B1849] hover:underline">
                                            {{ $grn->grn_number }}
                                        </a>
                                    </div>
                                    @if($grn->postedBy)
                                        <div class="text-[11px] text-gray-400">Posted by {{ $grn->postedBy->name }}</div>
                                    @endif
                                </td>

                                <!-- Supplier -->
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $grn->supplier ? $grn->supplier->name : 'N/A' }}
                                    </div>
                                    @if($grn->supplier && $grn->supplier->mobile)
                                        <div class="text-xs text-gray-500">{{ $grn->supplier->mobile }}</div>
                                    @endif
                                </td>

                                <!-- Invoice # & Date -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <div class="font-medium text-gray-900">{{ $grn->supplier_invoice_number ?: 'No Inv #' }}</div>
                                    <div class="text-xs text-gray-500">{{ $grn->received_date->format('M d, Y') }}</div>
                                </td>

                                <!-- Total Amount -->
                                <td class="px-6 py-4 whitespace-nowrap text-right font-mono font-bold text-sm text-gray-900">
                                    {{ number_format($grn->total_amount, 2) }}
                                </td>

                                <!-- Stock Status -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($grn->status === 'confirmed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Confirmed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            Draft
                                        </span>
                                    @endif
                                </td>

                                <!-- Payment Status -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php $payStatus = $grn->payment_status; @endphp
                                    @if($payStatus === 'Paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                            Paid
                                        </span>
                                    @elseif($payStatus === 'Partially Paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                            Partial
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            Unpaid
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1.5">
                                    <a 
                                        href="{{ route('inventory.grn.show', $grn) }}" 
                                        class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-md text-[#0B1849] bg-blue-50 hover:bg-blue-100 border border-blue-200 transition"
                                    >
                                        View
                                    </a>

                                    @if($grn->outstanding_amount > 0 && (Auth::user()->isAdmin() || Auth::user()->hasPermission('record_payments')))
                                        <button 
                                            type="button" 
                                            @click="openPaymentModal({
                                                id: {{ $grn->id }},
                                                grn_number: '{{ $grn->grn_number }}',
                                                supplier_name: '{{ addslashes($grn->supplier->name ?? 'N/A') }}',
                                                supplier_invoice_number: '{{ addslashes($grn->supplier_invoice_number ?: 'N/A') }}',
                                                total_amount: '{{ number_format($grn->total_amount, 2) }}',
                                                total_amount_raw: {{ (float) $grn->total_amount }},
                                                already_paid: '{{ number_format($grn->paid_amount, 2) }}',
                                                outstanding_balance: '{{ number_format($grn->outstanding_amount, 2) }}',
                                                outstanding_raw: {{ (float) $grn->outstanding_amount }},
                                                action_url: '{{ route('inventory.grn.payments.store', $grn) }}'
                                            })"
                                            class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-md text-white bg-[#0B1849] hover:bg-[#15286e] shadow-2xs transition cursor-pointer"
                                        >
                                            Record Payment
                                        </button>
                                    @else
                                        <a 
                                            href="{{ route('inventory.grn.show', $grn) }}#payments-section" 
                                            class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-md text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition"
                                        >
                                            View Payments
                                        </a>
                                    @endif

                                    <a 
                                        href="{{ route('inventory.grn.print', $grn) }}" 
                                        target="_blank"
                                        class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 transition"
                                        title="Print GRN"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <x-empty-state 
                                        title="No Goods Received Notes found." 
                                        description="Receive purchase orders from vendors into your warehouse and inventory."
                                    >
                                        <x-slot:action>
                                            <a 
                                                href="{{ route('inventory.grn.create') }}" 
                                                class="inline-flex items-center justify-center px-5 py-2.5 text-xs font-semibold rounded-lg text-white bg-[#0B1849] hover:bg-[#15286e] shadow-xs transition cursor-pointer"
                                            >
                                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Create First GRN
                                            </a>
                                        </x-slot:action>
                                    </x-empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($grns->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                    {{ $grns->links() }}
                </div>
            @endif
        </div>
        <!-- MODAL: RECORD SUPPLIER PAYMENT -->
        <div 
            x-show="paymentModalOpen" 
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;"
            x-cloak
        >
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" @click="paymentModalOpen = false"></div>

                <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-gray-200 space-y-4">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Record Supplier Payment</h3>
                            <p class="text-xs text-gray-500">Record a disbursement against supplier goods received note.</p>
                        </div>
                        <button type="button" @click="paymentModalOpen = false" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- GRN Info Summary (Read-Only) -->
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200/80 text-xs space-y-2">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-gray-500 block">GRN Number</span>
                                <span class="font-bold text-gray-900 font-mono" x-text="selectedGrn.grn_number"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">Supplier Name</span>
                                <span class="font-bold text-gray-900" x-text="selectedGrn.supplier_name"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">Supplier Invoice Number</span>
                                <span class="font-medium text-gray-800 font-mono" x-text="selectedGrn.supplier_invoice_number"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">GRN Total</span>
                                <span class="font-bold text-gray-900 font-mono">LKR <span x-text="selectedGrn.total_amount"></span></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">Already Paid</span>
                                <span class="font-semibold text-emerald-700 font-mono">LKR <span x-text="selectedGrn.already_paid"></span></span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">Outstanding Balance</span>
                                <span class="font-bold text-rose-700 font-mono text-sm">LKR <span x-text="selectedGrn.outstanding_balance"></span></span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form 
                        :action="selectedGrn.action_url" 
                        method="POST" 
                        class="space-y-4"
                        @submit="if(submitting){ $event.preventDefault(); return false; } submitting = true;"
                    >
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                    Payment Amount (LKR) <span class="text-rose-600">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    name="amount" 
                                    step="0.01" 
                                    min="0.01" 
                                    :max="selectedGrn.outstanding_raw" 
                                    x-model="paymentForm.amount"
                                    required
                                    class="block w-full px-3 py-2 text-sm font-mono font-bold text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849]"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                    Payment Method <span class="text-rose-600">*</span>
                                </label>
                                <select 
                                    name="payment_method" 
                                    x-model="paymentForm.payment_method" 
                                    required
                                    class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] bg-white"
                                >
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="card">Card</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                    Payment Date <span class="text-rose-600">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    name="payment_date" 
                                    x-model="paymentForm.payment_date" 
                                    required
                                    class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] bg-white"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                    Payment Reference <span class="text-gray-400 font-normal">(optional)</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="reference_number" 
                                    x-model="paymentForm.reference_number" 
                                    placeholder="e.g. Chq #, Bank slip ref"
                                    class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849]"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Note <span class="text-gray-400 font-normal">(optional)</span>
                            </label>
                            <textarea 
                                name="notes" 
                                rows="2" 
                                x-model="paymentForm.notes" 
                                placeholder="Additional remarks or notes..."
                                class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849]"
                            ></textarea>
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex items-center justify-end space-x-3 pt-3 border-t border-gray-100">
                            <button 
                                type="button" 
                                @click="paymentModalOpen = false" 
                                :disabled="submitting"
                                class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition cursor-pointer"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit" 
                                :disabled="submitting"
                                class="inline-flex items-center px-5 py-2 text-xs font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg transition shadow-xs cursor-pointer disabled:opacity-50"
                            >
                                <span x-show="!submitting">Save Payment</span>
                                <span x-show="submitting" class="inline-flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    Saving...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
