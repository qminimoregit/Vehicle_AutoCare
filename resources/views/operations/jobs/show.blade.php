@extends('layouts.app')

@section('title', 'Job Card #' . $jobCard->job_number)

@section('content')
<div 
    x-data="jobCardController({{ json_encode([
        'printBaseUrl' => route('operations.job-cards.print', $jobCard),
        'customerPhone' => $jobCard->customer?->phone ?? '',
        'technicianName' => $jobCard->assignedTechnician?->name ?? '',
        'selectedServices' => session('newly_added_service_id') ? [(int) session('newly_added_service_id')] : [],
        'selectedProducts' => session('newly_added_product_id') ? [(int) session('newly_added_product_id')] : [],
        'servicePriceUrl' => route('operations.job-cards.service-price', [$jobCard, '__SERVICE_ID__']),
        'totalItems' => $jobCard->services->count() + $jobCard->products->count(),
    ]) }})"
    class="max-w-6xl mx-auto space-y-6"
>
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('operations.job-cards.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight flex items-center">
                    Job Card #{{ $jobCard->job_number }}
                </h1>
                
                <!-- Status Badge -->
                @if($jobCard->status === 'pending')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Pending</span>
                @elseif($jobCard->status === 'in_progress')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">In Progress</span>
                @elseif($jobCard->status === 'additional_work')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">Additional Work In Progress</span>
                @elseif($jobCard->status === 'completed')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Completed</span>
                @elseif($jobCard->status === 'delivered')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800">Delivered</span>
                @elseif($jobCard->status === 'cancelled')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">Cancelled</span>
                @endif
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1 ml-8">
                Created on {{ $jobCard->date ? $jobCard->date->format('M d, Y') : '' }} by {{ $jobCard->creator?->name ?? 'Staff' }}
            </p>
        </div>

        <!-- Quick Status Transitions -->
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            @if($jobCard->status === 'pending')
                <form action="{{ route('operations.job-cards.status', $jobCard) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="start_work">
                    <button 
                        type="submit" 
                        class="inline-flex items-center justify-center px-4 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white font-bold text-sm rounded-lg shadow-xs transition cursor-pointer min-h-[44px]"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Start Work
                    </button>
                </form>
            @elseif($jobCard->status === 'in_progress')
                <form action="{{ route('operations.job-cards.status', $jobCard) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="complete_job">
                    <button 
                        type="submit" 
                        class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-lg shadow-xs transition cursor-pointer min-h-[44px]"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Complete Job
                    </button>
                </form>
            @elseif($jobCard->status === 'additional_work')
                <form action="{{ route('operations.job-cards.additional-work.complete', $jobCard) }}" method="POST">
                    @csrf
                    <button 
                        type="submit" 
                        class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-lg shadow-xs transition cursor-pointer min-h-[44px]"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Complete Additional Work
                    </button>
                </form>
            @endif

            @if(in_array($jobCard->status, ['completed', 'delivered']))
                @if($jobCard->status === 'completed' && $jobCard->issues->where('status', 'approved')->count() > 0)
                    <form action="{{ route('operations.job-cards.additional-work.start', $jobCard) }}" method="POST">
                        @csrf
                        <button 
                            type="submit" 
                            class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-lg shadow-xs transition cursor-pointer min-h-[44px]"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Additional Work
                        </button>
                    </form>
                @endif

                @if(!$jobCard->invoice_id)
                    <form action="{{ route('operations.job-cards.generate-invoice', $jobCard) }}" method="POST">
                        @csrf
                        <button 
                            type="submit" 
                            class="inline-flex items-center justify-center px-4 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white font-bold text-sm rounded-lg shadow-xs transition cursor-pointer min-h-[44px]"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Generate Invoice
                        </button>
                    </form>
                @else
                    <a 
                        href="{{ route('operations.invoices.show', $jobCard->invoice) }}" 
                        class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-300 text-gray-800 font-bold text-sm rounded-lg hover:bg-gray-50 shadow-xs transition min-h-[44px]"
                    >
                        <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        View Invoice ({{ $jobCard->invoice->invoice_number }})
                    </a>

                    @if($jobCard->status === 'completed' && ($jobCard->services->whereNull('invoice_id')->count() > 0 || $jobCard->products->whereNull('invoice_id')->count() > 0))
                        <form action="{{ route('operations.job-cards.supplementary-invoice', $jobCard) }}" method="POST">
                            @csrf
                            <button 
                                type="submit" 
                                class="inline-flex items-center justify-center px-4 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white font-bold text-sm rounded-lg shadow-xs transition cursor-pointer min-h-[44px]"
                            >
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Supplementary Invoice
                            </button>
                        </form>
                    @endif
                @endif

                @if($jobCard->status === 'completed')
                    <form action="{{ route('operations.job-cards.status', $jobCard) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="action" value="mark_delivered">
                        <button 
                            type="submit" 
                            class="inline-flex items-center justify-center px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-bold text-sm rounded-lg shadow-xs transition cursor-pointer min-h-[44px]"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Mark Delivered
                        </button>
                    </form>
                @elseif($jobCard->status === 'delivered')
                    <form action="{{ route('operations.job-cards.follow-up', $jobCard) }}" method="POST">
                        @csrf
                        <button 
                            type="submit" 
                            class="inline-flex items-center justify-center px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-lg shadow-xs transition cursor-pointer min-h-[44px]"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Follow-up Job Card
                        </button>
                    </form>
                @endif
            @endif

            <!-- Print Full Job Card Button -->
            <a 
                href="{{ route('operations.job-cards.print', $jobCard) }}" 
                target="_blank"
                class="inline-flex items-center px-3 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold text-xs rounded-lg hover:bg-gray-50 shadow-xs transition min-h-[44px]"
                title="Print Full Job Card"
            >
                <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Full Job Card
            </a>

            <!-- Print Selected Items Button -->
            <button 
                type="button" 
                @click="printSelectedItems()" 
                :disabled="totalItemsCount === 0"
                :class="totalItemsCount === 0 ? 'opacity-50 cursor-not-allowed bg-gray-50' : 'cursor-pointer hover:bg-gray-50 bg-white'"
                class="inline-flex items-center px-3 py-2.5 border border-gray-300 text-gray-700 font-semibold text-xs rounded-lg shadow-xs transition min-h-[44px]"
                :title="totalItemsCount === 0 ? 'No services or products available to select on this job card' : 'Print only checked services or products'"
            >
                <svg class="w-4 h-4 mr-1 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                Print Selected Items
                <span 
                    x-show="selectedServices.length + selectedProducts.length > 0" 
                    x-text="selectedServices.length + selectedProducts.length" 
                    class="ml-1.5 px-1.5 py-0.2 bg-indigo-100 text-indigo-800 rounded-full text-[10px] font-extrabold"
                    style="display: none;"
                ></span>
            </button>

            <!-- Edit Details Button -->
            @if($jobCard->status !== 'cancelled')
                <button 
                    type="button" 
                    @click="showEditModal = true" 
                    class="inline-flex items-center px-3 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold text-xs rounded-lg hover:bg-gray-50 shadow-xs transition min-h-[44px]"
                >
                    <svg class="w-3.5 h-3.5 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Edit Info
                </button>
            @endif

            <!-- Cancel Job Button (Admin or authorized) -->
            @if($jobCard->status !== 'cancelled' && $jobCard->status !== 'delivered' && (Auth::user()->isAdmin() || Auth::user()->hasPermission('cancel_jobs')))
                <button 
                    type="button" 
                    @click="showCancelModal = true" 
                    class="inline-flex items-center px-3 py-2.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-semibold text-xs rounded-lg transition min-h-[44px]"
                >
                    Cancel Job
                </button>
            @endif
        </div>
    </div>

    <!-- Cancelled Job Alert Notice -->
    @if($jobCard->status === 'cancelled')
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-red-800">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-500 mr-2.5 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h4 class="text-sm font-bold text-red-900">This Job Card was Cancelled</h4>
                    <p class="text-xs text-red-700 mt-0.5">
                        <strong>Reason:</strong> {{ $jobCard->cancellation_reason ?? 'No reason recorded' }}
                    </p>
                    <p class="text-[11px] text-red-500 mt-1">
                        Cancelled by {{ $jobCard->cancelledBy?->name ?? 'Admin' }} on {{ $jobCard->cancelled_at ? $jobCard->cancelled_at->format('Y-m-d H:i') : '' }}. Any issued parts were safely returned to inventory.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- 1. VEHICLE & CUSTOMER SUMMARY CARD -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 bg-slate-50/70 flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Vehicle & Customer Overview</span>
            <span class="text-xs font-semibold text-[#0B1849] bg-[#EEF1FA] px-2.5 py-0.5 rounded-full">
                {{ $jobCard->vehicleType->name }}
            </span>
        </div>
        <div class="p-5 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs sm:text-sm">
            <div>
                <span class="text-gray-400 uppercase text-[10px] font-bold block">Vehicle Number</span>
                <span class="font-extrabold text-base text-gray-900 tracking-wider block mt-0.5">{{ $jobCard->vehicle->vehicle_number }}</span>
                <span class="text-gray-500 text-xs">{{ $jobCard->vehicle->make_model ?? 'Model not specified' }}</span>
            </div>
            <div>
                <span class="text-gray-400 uppercase text-[10px] font-bold block">Customer Details</span>
                <span class="font-bold text-gray-900 block mt-0.5">{{ $jobCard->customer->customer_name }}</span>
                <span class="text-gray-500 text-xs">{{ $jobCard->customer->phone }}</span>
            </div>
            <div>
                <span class="text-gray-400 uppercase text-[10px] font-bold block">Mileage Record</span>
                <span class="font-bold text-gray-900 block mt-0.5">
                    Start: {{ $jobCard->current_mileage > 0 ? number_format($jobCard->current_mileage) . ' km' : 'N/A' }}
                </span>
                <span class="text-gray-500 text-xs">
                    Next: {{ $jobCard->next_service_mileage > 0 ? number_format($jobCard->next_service_mileage) . ' km' : 'Not set' }}
                </span>
            </div>
            <div>
                <span class="text-gray-400 uppercase text-[10px] font-bold block">Assigned Technician</span>
                <span class="font-bold text-gray-900 block mt-0.5">
                    {{ $jobCard->assignedTechnician?->name ?? 'Not Assigned' }}
                </span>
                <span class="text-gray-500 text-xs">Technician</span>
            </div>
        </div>

        @if($jobCard->customer_notes || $jobCard->service_notes)
            <div class="px-5 py-2.5 bg-amber-50/50 border-t border-amber-100/50 text-xs text-gray-700 flex flex-wrap gap-4">
                @if($jobCard->customer_notes)
                    <div><strong>Customer Note:</strong> {{ $jobCard->customer_notes }}</div>
                @endif
                @if($jobCard->service_notes)
                    <div><strong>Work Note:</strong> {{ $jobCard->service_notes }}</div>
                @endif
            </div>
        @endif
    </div>

    <!-- 2. TWO-COLUMN LAYOUT: SERVICES & PRODUCTS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- 2A. SERVICES PERFORMED SECTION -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-5 py-4 border-b border-gray-100 bg-slate-50/60 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-[#0B1849]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        </svg>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900">Selected Services</h3>
                    </div>

                    @if(!in_array($jobCard->status, ['completed', 'delivered', 'cancelled']))
                        <button 
                            type="button" 
                            @click="showAddServiceModal = true" 
                            class="inline-flex items-center px-3 py-1.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-semibold rounded-md shadow-xs transition min-h-[36px] cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Service
                        </button>
                    @elseif($jobCard->status === 'completed')
                        <button 
                            type="button" 
                            @click="alert('This Job Card is completed. To perform additional services, record an issue and click \'Add Additional Work\'.')" 
                            class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-500 text-xs font-semibold rounded-md shadow-xs transition min-h-[36px] cursor-pointer"
                            title="Job completed. Use Additional Work for extra services."
                        >
                            <svg class="w-3.5 h-3.5 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Add Service
                        </button>
                    @else
                        <button 
                            type="button" 
                            @click="alert('Services cannot be added because this Job Card is {{ $jobCard->status }}.')" 
                            class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-500 text-xs font-semibold rounded-md shadow-xs transition min-h-[36px] cursor-pointer"
                            title="Services locked for status {{ $jobCard->status }}"
                        >
                            <svg class="w-3.5 h-3.5 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Add Service
                        </button>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-left text-xs sm:text-sm">
                        <thead class="bg-gray-50/50 text-gray-500 font-bold uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="px-3 py-2.5 w-8 text-center">
                                    <input 
                                        type="checkbox" 
                                        @change="selectedServices = $event.target.checked ? [{{ $jobCard->services->pluck('id')->implode(',') }}] : []"
                                        :checked="selectedServices.length === {{ $jobCard->services->count() }} && {{ $jobCard->services->count() }} > 0"
                                        class="rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849] cursor-pointer"
                                        title="Select/Deselect all services"
                                    >
                                </th>
                                <th class="px-4 py-2.5">Service</th>
                                <th class="px-4 py-2.5 text-right">Price</th>
                                <th class="px-4 py-2.5 text-right">Qty</th>
                                <th class="px-4 py-2.5 text-right">Total</th>
                                @if(!in_array($jobCard->status, ['completed', 'delivered', 'cancelled']))
                                    <th class="px-3 py-2.5 text-center w-10"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-800">
                            @forelse($jobCard->services as $srv)
                                <tr class="hover:bg-slate-50/50" :class="selectedServices.includes({{ $srv->id }}) ? 'bg-indigo-50/30' : ''">
                                    <td class="px-3 py-3 text-center">
                                        <input 
                                            type="checkbox" 
                                            value="{{ $srv->id }}" 
                                            x-model.number="selectedServices"
                                            class="rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849] cursor-pointer"
                                            title="Select service for printing"
                                        >
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <span class="font-semibold text-gray-900">{{ $srv->service_name }}</span>
                                            @if($srv->is_additional)
                                                <span class="ml-2 px-1.5 py-0.5 text-[9px] font-bold bg-amber-100 text-amber-800 rounded">Additional Work</span>
                                            @endif
                                        </div>
                                        @if($srv->is_price_custom)
                                            <span class="text-[10px] font-semibold text-amber-700 bg-amber-50 px-1.5 py-0.2 rounded border border-amber-200">Custom Price</span>
                                        @endif
                                        @if($srv->technician_name)
                                            <span class="text-[11px] text-gray-500 block">Tech: {{ $srv->technician_name }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-700 whitespace-nowrap">
                                        LKR {{ number_format($srv->unit_price, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-600">
                                        @quantity($srv->quantity)
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-900 whitespace-nowrap">
                                        LKR {{ number_format($srv->line_total, 2) }}
                                    </td>
                                    @if(!in_array($jobCard->status, ['completed', 'delivered', 'cancelled']))
                                        <td class="px-3 py-3 text-center">
                                            @if(!$srv->invoice_id)
                                                <form action="{{ route('operations.job-cards.services.remove', [$jobCard, $srv]) }}" method="POST" onsubmit="return confirm('Remove service {{ addslashes($srv->service_name) }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition p-1 cursor-pointer" title="Remove">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-[10px] text-gray-400">Invoiced</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-xs">
                                        No services added to this job card yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Services Total Footer -->
            <div class="px-5 py-3 bg-slate-50 border-t border-gray-100 flex items-center justify-between text-xs sm:text-sm font-bold text-gray-800">
                <span>Services Total:</span>
                <span class="text-[#0B1849]">LKR {{ number_format($jobCard->service_total, 2) }}</span>
            </div>
        </div>

        <!-- 2B. PRODUCTS / PARTS ISSUED SECTION -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-5 py-4 border-b border-gray-100 bg-slate-50/60 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-[#0B1849]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900">Products & Parts</h3>
                    </div>

                    @if(!in_array($jobCard->status, ['completed', 'delivered', 'cancelled']))
                        <button 
                            type="button" 
                            @click="showAddProductModal = true" 
                            class="inline-flex items-center px-3 py-1.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-semibold rounded-md shadow-xs transition min-h-[36px] cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Issue Product
                        </button>
                    @elseif($jobCard->status === 'completed')
                        <button 
                            type="button" 
                            @click="alert('This Job Card is completed. To issue additional parts, record an issue and click \'Add Additional Work\'.')" 
                            class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-500 text-xs font-semibold rounded-md shadow-xs transition min-h-[36px] cursor-pointer"
                            title="Job completed. Use Additional Work for extra parts."
                        >
                            <svg class="w-3.5 h-3.5 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Issue Product
                        </button>
                    @else
                        <button 
                            type="button" 
                            @click="alert('Products cannot be issued because this Job Card is {{ $jobCard->status }}.')" 
                            class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-500 text-xs font-semibold rounded-md shadow-xs transition min-h-[36px] cursor-pointer"
                            title="Product issuance locked for status {{ $jobCard->status }}"
                        >
                            <svg class="w-3.5 h-3.5 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Issue Product
                        </button>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-left text-xs sm:text-sm">
                        <thead class="bg-gray-50/50 text-gray-500 font-bold uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="px-3 py-2.5 w-8 text-center">
                                    <input 
                                        type="checkbox" 
                                        @change="selectedProducts = $event.target.checked ? [{{ $jobCard->products->pluck('id')->implode(',') }}] : []"
                                        :checked="selectedProducts.length === {{ $jobCard->products->count() }} && {{ $jobCard->products->count() }} > 0"
                                        class="rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849] cursor-pointer"
                                        title="Select/Deselect all products"
                                    >
                                </th>
                                <th class="px-4 py-2.5">Product</th>
                                <th class="px-4 py-2.5 text-right">Price</th>
                                <th class="px-4 py-2.5 text-right">Qty</th>
                                <th class="px-4 py-2.5 text-right">Total</th>
                                @if(!in_array($jobCard->status, ['completed', 'delivered', 'cancelled']))
                                    <th class="px-3 py-2.5 text-center w-10"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-800">
                            @forelse($jobCard->products as $prod)
                                <tr class="hover:bg-slate-50/50" :class="selectedProducts.includes({{ $prod->id }}) ? 'bg-indigo-50/30' : ''">
                                    <td class="px-3 py-3 text-center">
                                        <input 
                                            type="checkbox" 
                                            value="{{ $prod->id }}" 
                                            x-model.number="selectedProducts"
                                            class="rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849] cursor-pointer"
                                            title="Select product for printing"
                                        >
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <span class="font-semibold text-gray-900">{{ $prod->product_name }}</span>
                                            @if($prod->is_additional)
                                                <span class="ml-2 px-1.5 py-0.5 text-[9px] font-bold bg-amber-100 text-amber-800 rounded">Additional Part</span>
                                            @endif
                                        </div>
                                        <span class="text-[11px] text-gray-500 font-mono">{{ $prod->product_sku ?? 'SKU N/A' }}</span>
                                        @if($prod->is_stock_deducted)
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                FIFO Deducted
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-700 whitespace-nowrap">
                                        LKR {{ number_format($prod->unit_price, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-600">
                                        @quantity($prod->quantity)
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-900 whitespace-nowrap">
                                        LKR {{ number_format($prod->line_total, 2) }}
                                    </td>
                                    @if(!in_array($jobCard->status, ['completed', 'delivered', 'cancelled']))
                                        <td class="px-3 py-3 text-center">
                                            @if(!$prod->invoice_id)
                                                <form action="{{ route('operations.job-cards.products.remove', [$jobCard, $prod]) }}" method="POST" onsubmit="return confirm('Remove product {{ addslashes($prod->product_name) }}? Issued stock will be returned to inventory.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition p-1 cursor-pointer" title="Remove & Reverse Stock">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-[10px] text-gray-400">Invoiced</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-xs">
                                        No parts or products issued to this job card yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Products Total Footer -->
            <div class="px-5 py-3 bg-slate-50 border-t border-gray-100 flex items-center justify-between text-xs sm:text-sm font-bold text-gray-800">
                <span>Products Total:</span>
                <span class="text-[#0B1849]">LKR {{ number_format($jobCard->product_total, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- VEHICLE ISSUES & ADDITIONAL WORK -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
        <!-- Section Header -->
        <div class="px-5 py-4 border-b border-gray-100 bg-slate-50/60 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-[#0B1849]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900">Vehicle Issues & Additional Work</h3>
                @if($jobCard->issues->count() > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                        {{ $jobCard->issues->count() }}
                    </span>
                @endif
            </div>

            <div class="flex items-center space-x-2">
                @if($jobCard->status !== 'cancelled')
                    <button 
                        type="button" 
                        @click="showAddIssueModal = true" 
                        class="inline-flex items-center px-3 py-1.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-semibold rounded-md shadow-xs transition min-h-[36px] cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Issue
                    </button>
                @endif

                @if($jobCard->status === 'completed' && $jobCard->issues->where('status', 'approved')->count() > 0)
                    <form action="{{ route('operations.job-cards.additional-work.start', $jobCard) }}" method="POST">
                        @csrf
                        <button 
                            type="submit" 
                            class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-md shadow-xs transition min-h-[36px] cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Additional Work
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Issues Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-left text-xs sm:text-sm">
                <thead class="bg-gray-50/50 text-gray-500 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-5 py-2.5">Issue Description</th>
                        <th class="px-4 py-2.5">Recommended Repair</th>
                        <th class="px-4 py-2.5 text-right">Estimated Cost</th>
                        <th class="px-4 py-2.5 text-center">Status</th>
                        <th class="px-4 py-2.5">Approval / Notes</th>
                        <th class="px-4 py-2.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-800">
                    @forelse($jobCard->issues as $issue)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-5 py-3 font-medium text-gray-900">
                                <div>{{ $issue->description }}</div>
                                <div class="text-[11px] text-gray-400 mt-0.5">Recorded by {{ $issue->recordedByUser?->name ?? 'Staff' }} on {{ $issue->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-700 font-medium">
                                {{ $issue->recommended_repair }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 whitespace-nowrap">
                                {{ $issue->estimated_cost ? 'LKR ' . number_format($issue->estimated_cost, 2) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if(in_array($issue->status, ['pending_approval', 'notified']))
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">Pending Approval</span>
                                @elseif($issue->status === 'approved')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Approved</span>
                                @elseif($issue->status === 'declined')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-100 text-red-800">Declined</span>
                                @elseif($issue->status === 'fixed')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800">Fixed</span>
                                @elseif($issue->status === 'deferred')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-100 text-purple-800">Deferred</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-gray-100 text-gray-800">{{ ucfirst(str_replace('_', ' ', $issue->status)) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 max-w-xs">
                                @if($issue->approval_method)
                                    <div class="font-medium text-gray-800">
                                        {{ ucfirst($issue->approval_method) }} approval {{ $issue->approved_at ? 'on ' . \Carbon\Carbon::parse($issue->approved_at)->format('M d, Y') : '' }}
                                    </div>
                                @endif
                                @if($issue->approval_note)
                                    <div class="text-[11px] text-gray-500 italic mt-0.5">{{ $issue->approval_note }}</div>
                                @endif
                                @if(!$issue->approval_method && !$issue->approval_note)
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                @if(in_array($issue->status, ['pending_approval', 'notified']))
                                    <div class="inline-flex items-center space-x-1.5">
                                        <button 
                                            type="button" 
                                            @click="openApprovalModal({{ $issue->toJson() }})"
                                            class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded shadow-xs transition cursor-pointer"
                                        >
                                            Approve
                                        </button>
                                        <form action="{{ route('operations.job-issues.approval', $issue) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="customer_decision" value="declined">
                                            <button 
                                                type="submit" 
                                                onclick="return confirm('Record customer decision as Declined?');"
                                                class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-semibold rounded border border-red-200 transition cursor-pointer"
                                            >
                                                Decline
                                            </button>
                                        </form>
                                    </div>
                                @elseif($issue->status === 'approved' && $jobCard->status === 'completed')
                                    <form action="{{ route('operations.job-cards.additional-work.start', $jobCard) }}" method="POST" class="inline">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded border border-indigo-200 transition cursor-pointer"
                                        >
                                            Add Additional Work
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400 font-medium">Logged</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-6 text-center text-xs sm:text-sm text-gray-400">
                                No additional vehicle issues recorded.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jobCard->supplementaryInvoices->count() > 0)
            <div class="px-5 py-3 bg-indigo-50/70 border-t border-indigo-100 flex flex-wrap items-center justify-between gap-2 text-xs">
                <span class="font-bold text-indigo-900">Supplementary Invoices for Additional Work:</span>
                <div class="flex flex-wrap items-center gap-2">
                    @foreach($jobCard->supplementaryInvoices as $supInv)
                        <a href="{{ route('operations.invoices.show', $supInv) }}" class="inline-flex items-center px-2 py-1 bg-white border border-indigo-200 text-indigo-800 rounded font-semibold hover:bg-indigo-100 transition">
                            Invoice #{{ $supInv->invoice_number }} (LKR {{ number_format($supInv->grand_total, 2) }})
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- 3. FINANCIAL SUMMARY & INVOICE STATUS CARD -->
    @php
        $unbilledServicesTotal = (float) $jobCard->services->whereNull('invoice_id')->sum('line_total');
        $unbilledProductsTotal = (float) $jobCard->products->whereNull('invoice_id')->sum('line_total');
        $unbilledTotal = $unbilledServicesTotal + $unbilledProductsTotal;
    @endphp
    <div class="bg-white border border-gray-200 rounded-xl shadow-xs p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-gray-900">Job Cost Summary</h3>
                <p class="text-xs text-gray-500 mt-0.5">Summary of all services, materials, applicable discounts, and grand total.</p>
                @if($jobCard->invoice_id && $unbilledTotal > 0)
                    <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-900">
                        <span class="font-bold">Supplementary Work Notice:</span> Original Invoice #{{ $jobCard->invoice->invoice_number }} was issued for LKR {{ number_format($jobCard->invoice->grand_total, 2) }}. Additional unbilled work of <strong>LKR {{ number_format($unbilledTotal, 2) }}</strong> is ready for supplementary billing.
                    </div>
                @endif
            </div>

            <div class="w-full sm:w-80 space-y-2 bg-[#EEF1FA]/50 p-4 rounded-xl border border-[#0B1849]/10 text-xs sm:text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Services Subtotal:</span>
                    <span class="font-semibold text-gray-900">LKR {{ number_format($jobCard->service_total, 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Products Subtotal:</span>
                    <span class="font-semibold text-gray-900">LKR {{ number_format($jobCard->product_total, 2) }}</span>
                </div>
                @if($jobCard->discount_amount > 0)
                    <div class="flex justify-between text-emerald-700">
                        <span>Discount:</span>
                        <span class="font-semibold">- LKR {{ number_format($jobCard->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="pt-2 border-t border-gray-200 flex justify-between text-base font-black text-[#0B1849]">
                    <span>Grand Total:</span>
                    <span>LKR {{ number_format($jobCard->grand_total, 2) }}</span>
                </div>
                @if($jobCard->invoice_id && $unbilledTotal > 0)
                    <div class="pt-2 border-t border-amber-200 flex justify-between text-xs font-bold text-amber-800">
                        <span>Unbilled Additional Work:</span>
                        <span>LKR {{ number_format($unbilledTotal, 2) }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- MODAL 1: ADD SERVICE -->
    <div 
        x-show="showAddServiceModal" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" @click="showAddServiceModal = false"></div>

            <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-gray-200 space-y-5">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="text-base font-bold text-gray-900">Add Service to Job Card</h3>
                    <button type="button" @click="showAddServiceModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('operations.job-cards.services.add', $jobCard) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="service_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Select Service <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="service_id" 
                            id="service_id" 
                            x-model="selectedServiceId"
                            @change="onServiceChange()"
                            class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                            required
                        >
                            <option value="">-- Choose a Service --</option>
                            @foreach($availableServices as $srv)
                                <option value="{{ $srv->id }}">{{ $srv->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Auto-loaded Service Price Feedback -->
                    <div x-show="selectedServiceId" class="p-3 rounded-lg text-xs" :class="serviceConfigured ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200'">
                        <template x-if="serviceConfigured">
                            <div>
                                <span class="font-bold">Standard Price for {{ $jobCard->vehicleType->name }}:</span>
                                <span class="text-sm font-extrabold ml-1" x-text="'LKR ' + parseFloat(servicePrice).toFixed(2)"></span>
                            </div>
                        </template>
                        <template x-if="!serviceConfigured">
                            <div>
                                <span class="font-bold text-red-600">Price not configured!</span>
                                <p class="mt-0.5" x-text="serviceStatusMessage"></p>
                                @if(Auth::user()->isAdmin())
                                    <p class="mt-1 font-semibold text-gray-700">As an Administrator, you may manually enter an authorized price below:</p>
                                @endif
                            </div>
                        </template>
                    </div>

                    @if(Auth::user()->isAdmin())
                        <div>
                            <label for="custom_price" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Price Override (Admin Authorized)
                            </label>
                            <input 
                                type="number" 
                                step="0.01" 
                                name="custom_price" 
                                id="custom_price"
                                x-model="customPrice"
                                placeholder="Enter price"
                                class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                            >
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="srv_quantity" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Quantity
                            </label>
                            <input 
                                type="number" 
                                step="0.1" 
                                name="quantity" 
                                id="srv_quantity"
                                x-model="serviceQty"
                                class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                            >
                        </div>
                        <div>
                            <label for="srv_technician_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Technician
                            </label>
                            <input 
                                type="text" 
                                name="technician_name" 
                                id="srv_technician_name"
                                x-model="technicianName"
                                placeholder="Name"
                                class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                            >
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-3 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="showAddServiceModal = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition min-h-[40px]"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            :disabled="!serviceConfigured && !customPrice"
                            class="px-5 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-bold rounded-lg shadow-xs transition disabled:opacity-50 min-h-[44px]"
                        >
                            Confirm & Add Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: ADD PRODUCT (FIFO DEDUCTION) -->
    <div 
        x-show="showAddProductModal" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" @click="showAddProductModal = false"></div>

            <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-gray-200 space-y-5">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="text-base font-bold text-gray-900">Issue Product / Part to Job</h3>
                    <button type="button" @click="showAddProductModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('operations.job-cards.products.add', $jobCard) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="prod_search" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Search Product <span class="text-gray-400 font-normal">(by Name or SKU)</span>
                        </label>
                        <div class="relative mb-2">
                            <input 
                                type="text" 
                                id="prod_search"
                                x-model="productSearch" 
                                placeholder="Type to filter products by name or SKU..."
                                class="block w-full py-2 pl-8 pr-3 text-xs text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[38px]"
                            >
                            <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        <label for="product_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Select Product <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="product_id" 
                            id="product_id" 
                            x-model="selectedProductId"
                            @change="onProductChange($event)"
                            class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                            required
                        >
                            <option value="">-- Choose a Product --</option>
                            @foreach($availableProducts as $prod)
                                <option 
                                    value="{{ $prod->id }}" 
                                    data-stock="{{ $prod->available_stock }}"
                                    data-price="{{ $prod->selling_price }}"
                                    data-name="{{ strtolower($prod->product_name) }}"
                                    data-sku="{{ strtolower($prod->sku ?? '') }}"
                                    :hidden="!isProductVisible('{{ strtolower(addslashes($prod->product_name)) }}', '{{ strtolower(addslashes($prod->sku ?? '')) }}')"
                                    :disabled="!isProductVisible('{{ strtolower(addslashes($prod->product_name)) }}', '{{ strtolower(addslashes($prod->sku ?? '')) }}')"
                                >
                                    {{ $prod->product_name }} @if($prod->sku)[{{ $prod->sku }}]@endif (Stock: @quantity($prod->available_stock) | LKR {{ number_format($prod->selling_price, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Available Stock & Price Notice -->
                    <div x-show="selectedProductId" class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-900">
                        <div class="flex justify-between font-semibold">
                            <span>Available Stock:</span>
                            <span class="font-bold" x-text="(Number.isInteger(parseFloat(productAvailableStock)) ? parseFloat(productAvailableStock) : parseFloat(parseFloat(productAvailableStock).toFixed(4))) + ' units'"></span>
                        </div>
                        <div class="flex justify-between font-semibold mt-1">
                            <span>Selling Price:</span>
                            <span class="font-bold" x-text="'LKR ' + productSellingPrice.toFixed(2)"></span>
                        </div>
                        <p class="text-[11px] text-blue-700 mt-1">
                            Stock will be automatically deducted using First-In, First-Out (FIFO) when added.
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="prod_quantity" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Quantity <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                step="0.01" 
                                name="quantity" 
                                id="prod_quantity"
                                x-model="productQty"
                                :max="productAvailableStock"
                                min="0.01"
                                class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                                required
                            >
                        </div>
                        <div>
                            <label for="prod_unit_price" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Unit Price (LKR)
                            </label>
                            <input 
                                type="number" 
                                step="0.01" 
                                name="unit_price" 
                                id="prod_unit_price"
                                x-model="productSellingPrice"
                                class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                            >
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-3 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="showAddProductModal = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition min-h-[40px]"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            :disabled="productAvailableStock <= 0 || productQty > productAvailableStock"
                            class="px-5 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-bold rounded-lg shadow-xs transition disabled:opacity-50 min-h-[44px]"
                        >
                            Deduct & Issue Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 3: CANCEL JOB CARD -->
    <div 
        x-show="showCancelModal" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" @click="showCancelModal = false"></div>

            <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-200 space-y-4">
                <div class="flex items-center space-x-2 text-red-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="text-base font-bold text-gray-900">Cancel Job Card #{{ $jobCard->job_number }}</h3>
                </div>

                <p class="text-xs text-gray-600 leading-relaxed">
                    Cancelling this job will halt all work. Any parts issued to this job will be <strong>automatically returned to inventory</strong> and their FIFO batches restored.
                </p>

                <form action="{{ route('operations.job-cards.cancel', $jobCard) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="cancellation_reason" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Reason for Cancellation <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="cancellation_reason" 
                            id="cancellation_reason" 
                            rows="3" 
                            placeholder="e.g. Customer cancelled the service request"
                            class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 bg-white"
                            required
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-2">
                        <button 
                            type="button" 
                            @click="showCancelModal = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition min-h-[40px]"
                        >
                            Back
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg shadow-xs transition min-h-[44px]"
                        >
                            Confirm Cancellation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 4: EDIT JOB INFO -->
    <div 
        x-show="showEditModal" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" @click="showEditModal = false"></div>

            <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-gray-200 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="text-base font-bold text-gray-900">Update Job Card Info</h3>
                    <button type="button" @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('operations.job-cards.update', $jobCard) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Customer & Vehicle Info Overview -->
                    <div class="p-3 bg-slate-50 border border-gray-200 rounded-lg text-xs space-y-1">
                        <div class="flex justify-between">
                            <span class="text-gray-500 font-medium">Customer:</span>
                            <span class="font-bold text-gray-900">{{ $jobCard->customer->customer_name }} ({{ $jobCard->customer->phone }})</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 font-medium">Vehicle:</span>
                            <span class="font-bold text-gray-900">{{ $jobCard->vehicle->vehicle_number }} &bull; {{ $jobCard->vehicleType->name }}</span>
                        </div>
                    </div>

                    <div>
                        <label for="assigned_to" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Assign Technician
                        </label>
                        <select 
                            name="assigned_to" 
                            id="assigned_to" 
                            class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                        >
                            <option value="">-- Unassigned --</option>
                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}" {{ $jobCard->assigned_to == $tech->id ? 'selected' : '' }}>
                                    {{ $tech->name }} ({{ ucfirst($tech->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="edit_current_mileage" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Current Mileage
                            </label>
                            <input 
                                type="number" 
                                name="current_mileage" 
                                id="edit_current_mileage" 
                                value="{{ $jobCard->current_mileage }}"
                                class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                            >
                        </div>
                        <div>
                            <label for="edit_next_service_mileage" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Next Service Mileage
                            </label>
                            <input 
                                type="number" 
                                name="next_service_mileage" 
                                id="edit_next_service_mileage" 
                                value="{{ $jobCard->next_service_mileage }}"
                                class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                            >
                        </div>
                    </div>

                    @if(!$jobCard->invoice_id)
                        <div>
                            <label for="edit_discount" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Discount Amount (LKR)
                            </label>
                            <input 
                                type="number" 
                                step="0.01" 
                                name="discount_amount" 
                                id="edit_discount" 
                                value="{{ $jobCard->discount_amount }}"
                                class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                            >
                        </div>
                    @else
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">
                                Discount Amount (LKR) — Locked
                            </label>
                            <input 
                                type="text" 
                                value="LKR {{ number_format($jobCard->discount_amount, 2) }} (Locked: Invoiced)" 
                                disabled 
                                class="block w-full py-2 px-3 text-sm text-gray-500 border border-gray-200 rounded-lg bg-gray-50 cursor-not-allowed"
                            >
                            <p class="text-[11px] text-gray-500 mt-1">Financial discount cannot be altered after invoice issuance.</p>
                        </div>
                    @endif

                    <div>
                        <label for="edit_customer_notes" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Customer Notes / Requests
                        </label>
                        <textarea 
                            name="customer_notes" 
                            id="edit_customer_notes" 
                            rows="2"
                            placeholder="Customer specific requests or observations"
                            class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white"
                        >{{ $jobCard->customer_notes }}</textarea>
                    </div>

                    <div>
                        <label for="edit_service_notes" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Service / Work Notes
                        </label>
                        <textarea 
                            name="service_notes" 
                            id="edit_service_notes" 
                            rows="2"
                            placeholder="Technical notes or observations by technician"
                            class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white"
                        >{{ $jobCard->service_notes }}</textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-3 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="showEditModal = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition min-h-[40px]"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-bold rounded-lg shadow-xs transition min-h-[44px]"
                        >
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 5: ADD VEHICLE ISSUE -->
    <div 
        x-show="showAddIssueModal" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" @click="showAddIssueModal = false"></div>

            <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-gray-200 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="text-base font-bold text-gray-900">Record Vehicle Issue</h3>
                    <button type="button" @click="showAddIssueModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('operations.job-cards.issues.store', $jobCard) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="issue_description" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Issue Description <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="description" 
                            id="issue_description" 
                            placeholder="e.g. Front brake pads worn out"
                            class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                            required
                        >
                    </div>

                    <div>
                        <label for="recommended_repair" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Recommended Repair <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="recommended_repair" 
                            id="recommended_repair" 
                            placeholder="e.g. Replace front brake pads"
                            class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                            required
                        >
                    </div>

                    <div>
                        <label for="estimated_cost" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Estimated Cost (LKR - Optional)
                        </label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="estimated_cost" 
                            id="estimated_cost" 
                            placeholder="e.g. 12000"
                            class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                        >
                    </div>

                    <div>
                        <label for="issue_remarks" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Remarks / Technical Notes (Optional)
                        </label>
                        <textarea 
                            name="remarks" 
                            id="issue_remarks" 
                            rows="2"
                            placeholder="e.g. Notified during full service check"
                            class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-3 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="showAddIssueModal = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition min-h-[40px]"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-bold rounded-lg shadow-xs transition min-h-[44px]"
                        >
                            Save Vehicle Issue
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 6: RECORD CUSTOMER APPROVAL -->
    <div 
        x-show="showApprovalModal" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" @click="showApprovalModal = false"></div>

            <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-gray-200 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="text-base font-bold text-gray-900">Record Customer Decision</h3>
                    <button type="button" @click="showApprovalModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-3 bg-slate-50 border border-gray-200 rounded-lg text-xs text-gray-700">
                    <span class="font-bold text-gray-900">Issue:</span> <span x-text="selectedIssueDescription"></span>
                </div>

                <form :action="'{{ url('/operations/job-issues') }}/' + selectedIssueId + '/approval'" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="approval_decision" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Customer Decision <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="customer_decision" 
                            id="approval_decision" 
                            x-model="approvalDecision"
                            class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                            required
                        >
                            <option value="approved">Approved – Proceed with repair</option>
                            <option value="declined">Declined – Do not repair</option>
                            <option value="deferred">Deferred – For later attention</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="approval_method" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Approval Method <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="approval_method" 
                                id="approval_method" 
                                x-model="approvalMethod"
                                class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                                required
                            >
                                <option value="verbal">Verbal</option>
                                <option value="phone">Phone Call</option>
                                <option value="written">Written / Message</option>
                                <option value="whatsapp">WhatsApp</option>
                            </select>
                        </div>
                        <div>
                            <label for="approval_date" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Approval Date <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                name="approval_date" 
                                id="approval_date" 
                                value="{{ now()->toDateString() }}"
                                class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                                required
                            >
                        </div>
                    </div>

                    <div>
                        <label for="approval_contact" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Approved By / Customer Contact
                        </label>
                        <input 
                            type="text" 
                            name="approval_contact" 
                            id="approval_contact" 
                            x-model="approvalContact"
                            placeholder="e.g. Customer Name or Phone Number"
                            class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                        >
                    </div>

                    <div>
                        <label for="approval_note" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Optional Remarks / Note
                        </label>
                        <textarea 
                            name="approval_note" 
                            id="approval_note" 
                            x-model="approvalRemarks"
                            rows="2"
                            placeholder="e.g. Customer authorized replacing front brake pads verbally over the phone"
                            class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-3 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="showApprovalModal = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition min-h-[40px]"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-xs transition min-h-[44px]"
                        >
                            Record Decision
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    window.jobCardController = function(config) {
        return {
            // Modals
            showAddServiceModal: false,
            showAddProductModal: false,
            showCancelModal: false,
            showEditModal: false,
            showAddIssueModal: false,
            showApprovalModal: false,

            // Issue Approval state
            selectedIssueId: null,
            selectedIssueDescription: '',
            approvalDecision: 'approved',
            approvalMethod: 'verbal',
            approvalContact: config.customerPhone || '',
            approvalRemarks: '',

            openApprovalModal(issue) {
                this.selectedIssueId = issue.id;
                this.selectedIssueDescription = issue.description;
                this.approvalDecision = 'approved';
                this.approvalMethod = 'verbal';
                this.approvalContact = config.customerPhone || '';
                this.approvalRemarks = '';
                this.showApprovalModal = true;
            },

            // Service selection state
            selectedServiceId: '',
            servicePrice: null,
            serviceCost: null,
            serviceConfigured: false,
            serviceStatusMessage: '',
            customPrice: '',
            serviceQty: 1,
            technicianName: config.technicianName || '',

            // Product selection state
            productSearch: '',
            selectedProductId: '',
            productAvailableStock: 0,
            productSellingPrice: 0,
            productQty: 1,

            isProductVisible(name, sku) {
                if (!this.productSearch.trim()) return true;
                const q = this.productSearch.toLowerCase().trim();
                return name.includes(q) || sku.includes(q);
            },

            async onServiceChange() {
                if (!this.selectedServiceId) {
                    this.servicePrice = null;
                    this.serviceConfigured = false;
                    this.serviceStatusMessage = '';
                    return;
                }

                try {
                    let url = config.servicePriceUrl.replace('__SERVICE_ID__', this.selectedServiceId);
                    let res = await fetch(url);
                    let data = await res.json();

                    this.serviceConfigured = data.configured;
                    if (data.configured) {
                        this.servicePrice = data.price;
                        this.customPrice = data.price;
                        this.serviceStatusMessage = '';
                    } else {
                        this.servicePrice = null;
                        this.customPrice = '';
                        this.serviceStatusMessage = data.message || 'Price not configured for this vehicle type.';
                    }
                } catch (err) {
                    console.error(err);
                    this.serviceStatusMessage = 'Error fetching service price.';
                }
            },

            onProductChange(event) {
                let select = event.target;
                let opt = select.options[select.selectedIndex];
                if (opt && opt.value) {
                    this.productAvailableStock = parseFloat(opt.dataset.stock) || 0;
                    this.productSellingPrice = parseFloat(opt.dataset.price) || 0;
                    this.productQty = 1;
                } else {
                    this.productAvailableStock = 0;
                    this.productSellingPrice = 0;
                }
            },

            // Print selection state
            selectedServices: config.selectedServices || [],
            selectedProducts: config.selectedProducts || [],
            totalItemsCount: config.totalItems || 0,

            printSelectedItems() {
                if (this.totalItemsCount === 0) {
                    alert('This Job Card has no saved services or products to select for printing.');
                    return;
                }
                if (this.selectedServices.length === 0 && this.selectedProducts.length === 0) {
                    alert('Please select at least one service or product to print.');
                    return;
                }
                const params = new URLSearchParams();
                params.append('selected', '1');
                this.selectedServices.forEach(id => params.append('services[]', id));
                this.selectedProducts.forEach(id => params.append('products[]', id));
                window.open(config.printBaseUrl + '?' + params.toString(), '_blank');
            }
        };
    };
</script>
@endsection
