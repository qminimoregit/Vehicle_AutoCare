@extends('layouts.app')

@section('title', 'Job Cards')

@section('content')
<div 
    x-data="{
        showCompleteModal: false,
        selectedJob: { id: null, jobNumber: '', vehicleNumber: '', customerName: '', grandTotal: '', actionUrl: '' },
        openCompleteModal(job) {
            this.selectedJob = job;
            this.showCompleteModal = true;
        },
        showDeleteModal: false,
        deleteJob: { id: null, jobNumber: '', vehicleNumber: '', customerName: '', grandTotal: '', invoiceNumber: '', paymentStatus: '', outstandingBalance: '', actionUrl: '' },
        manageAction: 'permanently_delete',
        deleteReason: '',
        deleteExplanation: '',
        openDeleteModal(job) {
            this.deleteJob = job;
            this.manageAction = 'permanently_delete';
            this.deleteReason = '';
            this.deleteExplanation = '';
            this.showDeleteModal = true;
        }
    }"
    class="space-y-6"
>
    <!-- Page Header -->
    <x-page-header 
        title="Job Cards" 
        description="Monitor vehicle services, progress, technician assignments, and billing."
    >
        <x-slot:actions>
            <a 
                href="{{ route('operations.vehicles.index') }}" 
                class="inline-flex items-center justify-center px-4 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-sm rounded-lg shadow-xs transition duration-150 cursor-pointer min-h-[44px]"
            >
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Vehicle Job
            </a>
        </x-slot:actions>
    </x-page-header>

    <!-- Quick Status Tabs -->
    <div class="flex items-center overflow-x-auto space-x-2 pb-1 border-b border-gray-200 text-xs sm:text-sm font-semibold scrollbar-none">
        <a 
            href="{{ route('operations.job-cards.index') }}" 
            class="px-3.5 py-2 rounded-lg transition whitespace-nowrap {{ $status === 'all' && empty($dateFilter) ? 'bg-[#0B1849] text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}"
        >
            All Jobs ({{ $counts['all'] }})
        </a>
        <a 
            href="{{ route('operations.job-cards.index', ['filter' => 'today']) }}" 
            class="px-3.5 py-2 rounded-lg transition whitespace-nowrap {{ $dateFilter === 'today' ? 'bg-[#0B1849] text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}"
        >
            Today's Jobs ({{ $counts['today'] }})
        </a>
        <a 
            href="{{ route('operations.job-cards.index', ['status' => 'pending']) }}" 
            class="px-3.5 py-2 rounded-lg transition whitespace-nowrap {{ $status === 'pending' && empty($dateFilter) ? 'bg-amber-500 text-white shadow-xs' : 'text-amber-700 hover:bg-amber-50' }}"
        >
            Pending ({{ $counts['pending'] }})
        </a>
        <a 
            href="{{ route('operations.job-cards.index', ['status' => 'in_progress']) }}" 
            class="px-3.5 py-2 rounded-lg transition whitespace-nowrap {{ $status === 'in_progress' ? 'bg-blue-600 text-white shadow-xs' : 'text-blue-700 hover:bg-blue-50' }}"
        >
            In Progress ({{ $counts['in_progress'] }})
        </a>
        <a 
            href="{{ route('operations.job-cards.index', ['status' => 'completed']) }}" 
            class="px-3.5 py-2 rounded-lg transition whitespace-nowrap {{ $status === 'completed' ? 'bg-emerald-600 text-white shadow-xs' : 'text-emerald-700 hover:bg-emerald-50' }}"
        >
            Completed ({{ $counts['completed'] }})
        </a>
        <a 
            href="{{ route('operations.job-cards.index', ['filter' => 'previous_pending']) }}" 
            class="px-3.5 py-2 rounded-lg transition whitespace-nowrap {{ $dateFilter === 'previous_pending' ? 'bg-red-600 text-white shadow-xs' : 'text-red-700 hover:bg-red-50' }}"
        >
            Previous Pending ({{ $counts['previous_pending'] }})
        </a>
    </div>

    <!-- Search Bar -->
    <x-search-bar 
        :action="route('operations.job-cards.index')" 
        :search="$search" 
        placeholder="Search by Job #, Vehicle Number, Customer, Phone..."
        :resetUrl="route('operations.job-cards.index', array_filter(['status' => $status !== 'all' ? $status : null, 'filter' => $dateFilter]))"
    >
        @if($status !== 'all')
            <input type="hidden" name="status" value="{{ $status }}">
        @endif
        @if($dateFilter)
            <input type="hidden" name="filter" value="{{ $dateFilter }}">
        @endif
    </x-search-bar>

    <!-- Desktop Data Table View -->
    <div class="hidden md:block bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
        <div class="responsive-table-wrapper">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 font-bold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5">Job Number</th>
                        <th class="px-4 py-3.5">Date</th>
                        <th class="px-4 py-3.5">Vehicle</th>
                        <th class="px-4 py-3.5">Customer</th>
                        <th class="px-4 py-3.5">Mileage</th>
                        <th class="px-4 py-3.5">Assigned To</th>
                        <th class="px-4 py-3.5">Grand Total</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-800">
                    @forelse($jobs as $job)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3.5 font-bold text-[#0B1849]">
                                <a href="{{ route('operations.job-cards.show', $job) }}" class="hover:underline">
                                    {{ $job->job_number }}
                                </a>
                            </td>
                            <td class="px-4 py-3.5 text-xs text-gray-500 whitespace-nowrap">
                                {{ $job->date ? $job->date->format('Y-m-d') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-gray-900 tracking-wide">{{ $job->vehicle->vehicle_number }}</div>
                                <div class="text-xs text-gray-500">{{ $job->vehicleType->name }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-gray-900">{{ $job->customer->customer_name }}</div>
                                <div class="text-xs text-gray-500">{{ $job->customer->phone }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-xs font-semibold text-gray-700 whitespace-nowrap">
                                {{ $job->current_mileage > 0 ? number_format($job->current_mileage) . ' km' : 'N/A' }}
                            </td>
                            <td class="px-4 py-3.5 text-xs text-gray-600">
                                {{ $job->assignedTechnician?->name ?? 'Unassigned' }}
                            </td>
                            <td class="px-4 py-3.5 font-bold text-gray-900 whitespace-nowrap">
                                LKR {{ number_format($job->grand_total, 2) }}
                            </td>
                            <td class="px-4 py-3.5">
                                @if($job->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Pending</span>
                                @elseif($job->status === 'in_progress')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">In Progress</span>
                                @elseif($job->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Completed</span>
                                @elseif($job->status === 'delivered')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">Delivered</span>
                                @elseif($job->status === 'cancelled')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">Cancelled</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end space-x-2">
                                    <a 
                                        href="{{ route('operations.job-cards.show', $job) }}" 
                                        class="inline-flex items-center px-3 py-1.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-semibold rounded-md shadow-xs transition"
                                    >
                                        View Job
                                    </a>

                                    @if(in_array($job->status, ['pending', 'in_progress', 'additional_work']))
                                        <button 
                                            type="button" 
                                            @click="openCompleteModal({
                                                id: {{ $job->id }},
                                                jobNumber: '{{ $job->job_number }}',
                                                vehicleNumber: '{{ $job->vehicle->vehicle_number }}',
                                                customerName: '{{ addslashes($job->customer->customer_name) }}',
                                                grandTotal: 'LKR {{ number_format($job->grand_total, 2) }}',
                                                actionUrl: '{{ route('operations.job-cards.status', $job) }}'
                                            })"
                                            class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-md shadow-xs transition cursor-pointer"
                                            title="Complete Job"
                                        >
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Complete Job
                                        </button>
                                    @elseif(in_array($job->status, ['completed', 'delivered']))
                                        @if(Auth::user()?->isAdmin())
                                            <button 
                                                type="button" 
                                                @click="openDeleteModal({
                                                    id: {{ $job->id }},
                                                    jobNumber: '{{ $job->job_number }}',
                                                    vehicleNumber: '{{ $job->vehicle->vehicle_number }}',
                                                    customerName: '{{ addslashes($job->customer->customer_name) }}',
                                                    grandTotal: 'LKR {{ number_format($job->grand_total, 2) }}',
                                                    invoiceNumber: '{{ $job->invoice?->invoice_number ?? 'N/A' }}',
                                                    paymentStatus: '{{ $job->invoice?->payment_status ?? 'Unpaid' }}',
                                                    outstandingBalance: 'LKR {{ number_format($job->outstanding_balance, 2) }}',
                                                    actionUrl: '{{ route('operations.job-cards.destroy', $job) }}'
                                                })"
                                                class="inline-flex items-center justify-center p-1.5 text-red-600 hover:text-red-700 bg-white hover:bg-red-50 border border-red-300 rounded-md transition shadow-2xs cursor-pointer"
                                                title="Delete Job"
                                                aria-label="Delete Job"
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
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-10 w-10 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="text-sm font-semibold text-gray-700">No Job Cards found.</p>
                                <p class="text-xs text-gray-400 mt-0.5">Register a vehicle above to initiate the first job card.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile / Tablet Responsive Card View (No Horizontal Overflow) -->
    <div class="block md:hidden space-y-3">
        @forelse($jobs as $job)
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <a href="{{ route('operations.job-cards.show', $job) }}" class="text-base font-bold text-[#0B1849]">
                            {{ $job->job_number }}
                        </a>
                        <div class="text-xs text-gray-500 mt-0.5">
                            {{ $job->date ? $job->date->format('M d, Y') : 'N/A' }}
                        </div>
                    </div>

                    <div>
                        @if($job->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Pending</span>
                        @elseif($job->status === 'in_progress')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">In Progress</span>
                        @elseif($job->status === 'completed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Completed</span>
                        @elseif($job->status === 'delivered')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">Delivered</span>
                        @elseif($job->status === 'cancelled')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">Cancelled</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs bg-slate-50 p-2.5 rounded-lg border border-gray-100">
                    <div>
                        <span class="text-gray-400 uppercase text-[10px] font-bold block">Vehicle</span>
                        <span class="font-bold text-gray-900">{{ $job->vehicle->vehicle_number }}</span>
                        <span class="text-gray-500 block text-[11px]">{{ $job->vehicleType->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 uppercase text-[10px] font-bold block">Customer</span>
                        <span class="font-semibold text-gray-900 block truncate">{{ $job->customer->customer_name }}</span>
                        <span class="text-gray-500 block text-[11px]">{{ $job->customer->phone }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 uppercase text-[10px] font-bold block">Mileage</span>
                        <span class="font-semibold text-gray-800">{{ $job->current_mileage > 0 ? number_format($job->current_mileage) . ' km' : 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 uppercase text-[10px] font-bold block">Grand Total</span>
                        <span class="font-bold text-gray-900">LKR {{ number_format($job->grand_total, 2) }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <span class="text-xs text-gray-500">
                        Assigned: <strong class="text-gray-700">{{ $job->assignedTechnician?->name ?? 'Unassigned' }}</strong>
                    </span>
                    <div class="flex items-center space-x-2">
                        <a 
                            href="{{ route('operations.job-cards.show', $job) }}" 
                            class="inline-flex items-center px-3 py-1.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-bold rounded-lg shadow-xs transition min-h-[38px]"
                        >
                            View Job
                        </a>

                        @if(in_array($job->status, ['pending', 'in_progress', 'additional_work']))
                            <button 
                                type="button" 
                                @click="openCompleteModal({
                                    id: {{ $job->id }},
                                    jobNumber: '{{ $job->job_number }}',
                                    vehicleNumber: '{{ $job->vehicle->vehicle_number }}',
                                    customerName: '{{ addslashes($job->customer->customer_name) }}',
                                    grandTotal: 'LKR {{ number_format($job->grand_total, 2) }}',
                                    actionUrl: '{{ route('operations.job-cards.status', $job) }}'
                                })"
                                class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-xs transition min-h-[38px] cursor-pointer"
                                title="Complete Job"
                            >
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Complete Job
                            </button>
                        @elseif(in_array($job->status, ['completed', 'delivered']))
                            @if(Auth::user()?->isAdmin())
                                <button 
                                    type="button" 
                                    @click="openDeleteModal({
                                        id: {{ $job->id }},
                                        jobNumber: '{{ $job->job_number }}',
                                        vehicleNumber: '{{ $job->vehicle->vehicle_number }}',
                                        customerName: '{{ addslashes($job->customer->customer_name) }}',
                                        grandTotal: 'LKR {{ number_format($job->grand_total, 2) }}',
                                        invoiceNumber: '{{ $job->invoice?->invoice_number ?? 'N/A' }}',
                                        paymentStatus: '{{ $job->invoice?->payment_status ?? 'Unpaid' }}',
                                        outstandingBalance: 'LKR {{ number_format($job->outstanding_balance, 2) }}',
                                        actionUrl: '{{ route('operations.job-cards.destroy', $job) }}'
                                    })"
                                    class="inline-flex items-center justify-center p-2 text-red-600 hover:text-red-700 bg-white hover:bg-red-50 border border-red-300 rounded-lg transition shadow-2xs min-h-[38px] min-w-[38px] cursor-pointer"
                                    title="Delete Job"
                                    aria-label="Delete Job"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-xl p-8 text-center text-gray-500">
                <p class="text-sm font-semibold text-gray-700">No Job Cards found.</p>
                <p class="text-xs text-gray-400 mt-1">Register a vehicle above to start a job.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $jobs->links() }}
    </div>

    <!-- Complete Job Confirmation Modal -->
    <div 
        x-show="showCompleteModal" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" 
        role="dialog" 
        aria-modal="true"
        style="display: none;"
    >
        <!-- Backdrop -->
        <div 
            x-show="showCompleteModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"
            @click="showCompleteModal = false"
        ></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div 
                x-show="showCompleteModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                @click.stop
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100"
            >
                <!-- Modal Header -->
                <div class="bg-slate-50/80 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900" id="modal-title">Complete Job?</h3>
                            <p class="text-xs text-gray-500">Confirm job finalization and billing generation</p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="showCompleteModal = false" 
                        class="text-gray-400 hover:text-gray-600 transition"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form :action="selectedJob.actionUrl" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="complete_job">

                    <p class="text-sm text-gray-700">
                        Are you sure you want to mark this job as Completed?
                    </p>

                    <!-- Job Summary Card -->
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-2 text-xs">
                        <div class="flex justify-between items-center py-1 border-b border-gray-100">
                            <span class="text-gray-500 font-medium">Job Number:</span>
                            <span class="font-bold text-gray-900" x-text="selectedJob.jobNumber"></span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-gray-100">
                            <span class="text-gray-500 font-medium">Vehicle Number:</span>
                            <span class="font-bold text-gray-900" x-text="selectedJob.vehicleNumber"></span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-gray-100">
                            <span class="text-gray-500 font-medium">Customer:</span>
                            <span class="font-semibold text-gray-900" x-text="selectedJob.customerName"></span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-gray-500 font-medium">Job Total:</span>
                            <span class="font-bold text-base text-gray-900" x-text="selectedJob.grandTotal"></span>
                        </div>
                    </div>

                    <div class="flex items-start p-3 bg-amber-50 border border-amber-200 rounded-lg text-amber-900 text-xs">
                        <svg class="w-4 h-4 mr-2 mt-0.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>This action will finalize the job and generate its billing invoice.</span>
                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-2 flex items-center justify-end space-x-3">
                        <button 
                            type="button" 
                            @click="showCompleteModal = false" 
                            class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="inline-flex items-center px-5 py-2 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-xs transition cursor-pointer"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Yes, Complete Job
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Permanently Delete Job Confirmation Modal (Admin Only) -->
    @if(Auth::user()?->isAdmin())
    <div 
        x-show="showDeleteModal" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="delete-modal-title" 
        role="dialog" 
        aria-modal="true"
        style="display: none;"
    >
        <!-- Backdrop -->
        <div 
            x-show="showDeleteModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"
            @click="showDeleteModal = false"
        ></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div 
                x-show="showDeleteModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                @click.stop
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100"
            >
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b flex items-center justify-between transition-colors duration-150" :class="manageAction === 'recreate_for_editing' ? 'bg-blue-50/80 border-blue-100' : 'bg-red-50/80 border-red-100'">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 transition-colors duration-150" :class="manageAction === 'recreate_for_editing' ? 'bg-blue-100 text-blue-600' : 'bg-red-100 text-red-600'">
                            <template x-if="manageAction === 'recreate_for_editing'">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </template>
                            <template x-if="manageAction !== 'recreate_for_editing'">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </template>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900" id="delete-modal-title">Manage Completed Job</h3>
                            <p class="text-xs" :class="manageAction === 'recreate_for_editing' ? 'text-blue-700' : 'text-red-700'">Administrator workflow management action</p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="showDeleteModal = false" 
                        class="text-gray-400 hover:text-gray-600 transition"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form :action="deleteJob.actionUrl" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('DELETE')

                    <!-- Database Values Summary Card -->
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-2 text-xs">
                        <div class="flex justify-between items-center py-1 border-b border-gray-100">
                            <span class="text-gray-500 font-medium">Job Number:</span>
                            <span class="font-bold text-gray-900" x-text="deleteJob.jobNumber"></span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-gray-100">
                            <span class="text-gray-500 font-medium">Vehicle Number:</span>
                            <span class="font-bold text-gray-900" x-text="deleteJob.vehicleNumber"></span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-gray-100">
                            <span class="text-gray-500 font-medium">Customer:</span>
                            <span class="font-semibold text-gray-900" x-text="deleteJob.customerName"></span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-gray-100">
                            <span class="text-gray-500 font-medium">Job Total:</span>
                            <span class="font-bold text-gray-900" x-text="deleteJob.grandTotal"></span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-gray-100">
                            <span class="text-gray-500 font-medium">Linked Invoice:</span>
                            <span class="font-bold text-indigo-700" x-text="deleteJob.invoiceNumber"></span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-gray-100">
                            <span class="text-gray-500 font-medium">Invoice Payment Status:</span>
                            <span class="font-bold" :class="deleteJob.paymentStatus === 'Paid in Full' ? 'text-emerald-700' : (deleteJob.paymentStatus === 'Partially Paid' ? 'text-amber-700' : 'text-gray-700')" x-text="deleteJob.paymentStatus"></span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-gray-500 font-medium">Outstanding Balance:</span>
                            <span class="font-bold text-gray-900" x-text="deleteJob.outstandingBalance"></span>
                        </div>
                    </div>

                    <!-- Select Action -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Select Action <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2">
                            <!-- Option 1: Permanently Delete Job -->
                            <label class="flex items-center p-2.5 border rounded-lg cursor-pointer transition" :class="manageAction === 'permanently_delete' ? 'border-red-500 bg-red-50/30' : 'border-gray-200 hover:bg-gray-50'">
                                <input 
                                    type="radio" 
                                    name="manage_action" 
                                    value="permanently_delete" 
                                    x-model="manageAction" 
                                    class="text-red-600 focus:ring-red-500"
                                >
                                <span class="ml-2.5 text-sm font-semibold text-gray-900">Option 1 — Permanently Delete Job</span>
                            </label>

                            <!-- Option 2: Recreate Job for Editing -->
                            <label class="flex items-center p-2.5 border rounded-lg cursor-pointer transition" :class="manageAction === 'recreate_for_editing' ? 'border-blue-500 bg-blue-50/30' : 'border-gray-200 hover:bg-gray-50'">
                                <input 
                                    type="radio" 
                                    name="manage_action" 
                                    value="recreate_for_editing" 
                                    x-model="manageAction" 
                                    class="text-blue-600 focus:ring-blue-500"
                                >
                                <span class="ml-2.5 text-sm font-semibold text-gray-900">Option 2 — Recreate Job for Editing</span>
                            </label>
                        </div>
                    </div>

                    <!-- Reason for Deletion (Option 1 Only) -->
                    <div x-show="manageAction === 'permanently_delete'">
                        <label for="delete_reason" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Reason for Deletion <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="reason" 
                            id="delete_reason" 
                            x-model="deleteReason" 
                            :required="manageAction === 'permanently_delete'" 
                            class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white"
                        >
                            <option value="" disabled selected>Select Reason</option>
                            <option value="Incorrect services added">Incorrect services added</option>
                            <option value="Incorrect products added">Incorrect products added</option>
                            <option value="Wrong vehicle selected">Wrong vehicle selected</option>
                            <option value="Incorrect pricing">Incorrect pricing</option>
                            <option value="Customer requested cancellation">Customer requested cancellation</option>
                            <option value="Duplicate Job Card">Duplicate Job Card</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-2 flex items-center justify-end space-x-3">
                        <button 
                            type="button" 
                            @click="showDeleteModal = false" 
                            class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition cursor-pointer"
                        >
                            Cancel
                        </button>

                        <template x-if="manageAction === 'permanently_delete'">
                            <button 
                                type="submit" 
                                class="inline-flex items-center px-5 py-2 text-sm font-bold text-white bg-red-600 hover:bg-red-700 rounded-lg shadow-xs transition cursor-pointer"
                            >
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Permanently Delete Job
                            </button>
                        </template>

                        <template x-if="manageAction === 'recreate_for_editing'">
                            <button 
                                type="submit" 
                                class="inline-flex items-center px-5 py-2 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-xs transition cursor-pointer"
                            >
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Recreate Job for Editing
                            </button>
                        </template>
                    </div>

                </form>
            </div>
    </div>
</div>
@endif
@endsection
