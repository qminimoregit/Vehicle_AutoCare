@extends('layouts.app')

@section('title', 'Job Card #' . $jobCard->job_number)

@section('content')
<div 
    x-data="{
        // Modals
        showAddServiceModal: false,
        showAddProductModal: false,
        showCancelModal: false,
        showEditModal: false,

        // Service selection state
        selectedServiceId: '',
        servicePrice: null,
        serviceCost: null,
        serviceConfigured: false,
        serviceStatusMessage: '',
        customPrice: '',
        serviceQty: 1,
        technicianName: '{{ $jobCard->assignedTechnician?->name ?? '' }}',

        // Product selection state
        selectedProductId: '',
        productAvailableStock: 0,
        productSellingPrice: 0,
        productQty: 1,

        async onServiceChange() {
            if (!this.selectedServiceId) {
                this.servicePrice = null;
                this.serviceConfigured = false;
                this.serviceStatusMessage = '';
                return;
            }

            try {
                let url = '{{ route('operations.job-cards.service-price', [$jobCard, ':serviceId']) }}'.replace(':serviceId', this.selectedServiceId);
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
        }
    }"
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
            @endif

            @if(in_array($jobCard->status, ['completed', 'delivered']))
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
                @endif
            @endif

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

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('warning'))
        <x-alert type="warning" :message="session('warning')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

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
                            class="inline-flex items-center px-3 py-1.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-semibold rounded-md shadow-xs transition min-h-[36px]"
                        >
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Service
                        </button>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-left text-xs sm:text-sm">
                        <thead class="bg-gray-50/50 text-gray-500 font-bold uppercase text-[10px] tracking-wider">
                            <tr>
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
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-4 py-3">
                                        <span class="font-semibold text-gray-900 block">{{ $srv->service_name }}</span>
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
                                        {{ (float) $srv->quantity }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-900 whitespace-nowrap">
                                        LKR {{ number_format($srv->line_total, 2) }}
                                    </td>
                                    @if(!in_array($jobCard->status, ['completed', 'delivered', 'cancelled']))
                                        <td class="px-3 py-3 text-center">
                                            <form action="{{ route('operations.job-cards.services.remove', [$jobCard, $srv]) }}" method="POST" onsubmit="return confirm('Remove service {{ addslashes($srv->service_name) }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-400 hover:text-red-600 transition p-1 cursor-pointer" title="Remove">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-xs">
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
                        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900">Products & Parts Issued</h3>
                    </div>

                    @if(!in_array($jobCard->status, ['completed', 'delivered', 'cancelled']))
                        <button 
                            type="button" 
                            @click="showAddProductModal = true" 
                            class="inline-flex items-center px-3 py-1.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-semibold rounded-md shadow-xs transition min-h-[36px]"
                        >
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Issue Product
                        </button>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-left text-xs sm:text-sm">
                        <thead class="bg-gray-50/50 text-gray-500 font-bold uppercase text-[10px] tracking-wider">
                            <tr>
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
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-4 py-3">
                                        <span class="font-semibold text-gray-900 block">{{ $prod->product_name }}</span>
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
                                        {{ (float) $prod->quantity }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-900 whitespace-nowrap">
                                        LKR {{ number_format($prod->line_total, 2) }}
                                    </td>
                                    @if(!in_array($jobCard->status, ['completed', 'delivered', 'cancelled']))
                                        <td class="px-3 py-3 text-center">
                                            <form action="{{ route('operations.job-cards.products.remove', [$jobCard, $prod]) }}" method="POST" onsubmit="return confirm('Remove product {{ addslashes($prod->product_name) }}? Issued stock will be returned to inventory.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-400 hover:text-red-600 transition p-1 cursor-pointer" title="Remove & Reverse Stock">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-xs">
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

    <!-- 3. FINANCIAL SUMMARY & INVOICE STATUS CARD -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-xs p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-gray-900">Job Cost Summary</h3>
                <p class="text-xs text-gray-500 mt-0.5">Summary of all services, materials, applicable discounts, and grand total.</p>
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
                                >
                                    {{ $prod->product_name }} (Stock: {{ (float) $prod->available_stock }} | LKR {{ number_format($prod->selling_price, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Available Stock & Price Notice -->
                    <div x-show="selectedProductId" class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-900">
                        <div class="flex justify-between font-semibold">
                            <span>Available Stock:</span>
                            <span class="font-bold" x-text="productAvailableStock + ' units'"></span>
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

                    <div>
                        <label for="edit_service_notes" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Service / Work Notes
                        </label>
                        <textarea 
                            name="service_notes" 
                            id="edit_service_notes" 
                            rows="2"
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
</div>
@endsection
