@extends('layouts.app')

@section('title', 'Job Card #' . $jobCard->job_number)

@section('content')
@php
    $invoice = $jobCard->invoice;
    $invoiceTotal = $invoice ? (float)$invoice->grand_total : (float)$jobCard->grand_total;
    $advancesAmount = (float) $jobCard->payments()->where('status', 'completed')->sum('amount');
    $paidAmount = $invoice ? (float)$invoice->payments()->where('status', 'completed')->sum('amount') : $advancesAmount;
    $balanceDue = max(0, round($invoiceTotal - $paidAmount, 2));
    $isPaid = ($invoiceTotal > 0 && $balanceDue <= 0.0001);

    $customer = $jobCard->customer;
    $creditAccount = $customer?->creditAccount;
    $isCompanyCredit = $creditAccount && $creditAccount->isActive() && !$creditAccount->wouldExceedLimit($balanceDue);
    $canDeliver = $isPaid || $isCompanyCredit;
    $isAdmin = Auth::user()->isAdmin();

    $paymentStatusStr = 'Unpaid';
    if ($isPaid) {
        $paymentStatusStr = 'Paid in Full';
    } elseif ($paidAmount > 0) {
        $paymentStatusStr = 'Partially Paid';
    }

    $branchName = $jobCard->branch?->name ?? 'AutoCare Service Center';
    $vehiclePlate = $jobCard->vehicle?->vehicle_number ?? 'Vehicle';
    $jobNum = $jobCard->job_number;

    $waMessage = "Hello,\n\n"
        . "Your vehicle {$vehiclePlate} has completed its service at {$branchName}.\n\n"
        . "Job No: {$jobNum}\n"
        . "Service Amount: LKR " . number_format($invoiceTotal, 2) . "\n"
        . "Payment Status: {$paymentStatusStr}\n"
        . "Balance Due: LKR " . number_format($balanceDue, 2) . "\n\n"
        . "Your vehicle is ready for collection, subject to final release confirmation.\n\n"
        . "Thank you for choosing our service center.";

    $rawPhone = $customer?->phone ?? '';
    $normPhone = \App\Services\PhoneNumberHelper::normalizeForWhatsApp($rawPhone) ?? '';
@endphp

<div 
    x-data="jobCardController({{ json_encode([
        'printBaseUrl' => route('operations.job-cards.print', $jobCard),
        'customerPhone' => $jobCard->customer?->phone ?? '',
        'whatsappPhone' => $normPhone,
        'whatsappText' => $waMessage,
        'technicianName' => $jobCard->assignedTechnician?->name ?? '',
        'selectedServices' => session('newly_added_service_id') ? [(int) session('newly_added_service_id')] : [],
        'selectedProducts' => session('newly_added_product_id') ? [(int) session('newly_added_product_id')] : [],
        'servicePriceUrl' => route('operations.job-cards.service-price', [$jobCard, '__SERVICE_ID__']),
        'totalItems' => $jobCard->services->count() + $jobCard->products->count() + $jobCard->issues->count() + $jobCard->carpets->count(),
        'hasCarpets' => $jobCard->carpets->isNotEmpty(),
    ]) }})"
    class="max-w-6xl mx-auto space-y-6"
>
    <!-- Instant Payment Receipt Print Alert -->
    @if(session('recent_payment_id'))
        @php
            $recentPayment = \App\Models\Payment::find(session('recent_payment_id'));
        @endphp
        @if($recentPayment)
            <div class="p-4 bg-emerald-50 border border-emerald-300 rounded-xl shadow-xs flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center space-x-2 text-emerald-900 text-sm font-semibold">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Payment of <strong>LKR {{ number_format($recentPayment->amount, 2) }}</strong> recorded successfully (Receipt #{{ $recentPayment->payment_number }}).</span>
                </div>
                <a 
                    href="{{ route('billing.payments.thermal', $recentPayment) }}" 
                    @click.prevent="AutoCarePrinter.printUrl('{{ route('billing.payments.thermal', $recentPayment) }}', { type: 'payment_receipt', title: 'Payment Receipt #{{ $recentPayment->payment_number }}', documentId: {{ $recentPayment->id }}, documentNumber: '{{ $recentPayment->payment_number }}', fallbackUrl: '{{ route('billing.payments.thermal', $recentPayment) }}' })"
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow-xs transition"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Payment Receipt
                </a>
            </div>
        @endif
    @endif

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <a href="{{ route('operations.job-cards.index') }}" class="text-gray-400 hover:text-gray-600 transition p-1 -ml-1">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-lg sm:text-2xl font-black text-gray-900 tracking-tight flex items-center">
                    Job Card #{{ $jobCard->job_number }}
                </h1>
                
                <!-- Status Badge -->
                @if($jobCard->status === 'pending')
                    <span class="px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Pending</span>
                @elseif($jobCard->status === 'in_progress')
                    <span class="px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">In Progress</span>
                @elseif($jobCard->status === 'additional_work')
                    <span class="px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">Additional Work</span>
                @elseif($jobCard->status === 'completed')
                    <span class="px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Completed</span>
                @elseif($jobCard->status === 'delivered')
                    <span class="px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800">Delivered</span>
                @elseif($jobCard->status === 'cancelled')
                    <span class="px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">Cancelled</span>
                @endif
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1 ml-0 sm:ml-8">
                Created on {{ $jobCard->date ? $jobCard->date->format('M d, Y') : '' }} by {{ $jobCard->creator?->name ?? 'Staff' }}
                @if($jobCard->assignedTechnician)
                    &bull; <span class="font-semibold text-gray-700">Technician: {{ $jobCard->assignedTechnician->name }}</span>
                @endif
            </p>
        </div>

        <!-- Primary Action Buttons (Job Card Actions) -->
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <!-- 1. Print Job Card -->
            <a 
                href="{{ route('operations.job-cards.print', $jobCard) }}" 
                @click.prevent="AutoCarePrinter.printUrl('{{ route('operations.job-cards.print', $jobCard) }}', { type: 'job_card', title: 'Job Card #{{ $jobCard->job_number }}', documentId: {{ $jobCard->id }}, documentNumber: '{{ $jobCard->job_number }}', fallbackUrl: '{{ route('operations.job-cards.print', $jobCard) }}' })"
                target="_blank"
                class="hidden sm:inline-flex items-center justify-center px-3.5 py-2.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold text-xs sm:text-sm rounded-lg shadow-xs transition min-h-[44px]"
                title="Print Job Card"
            >
                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Job Card
            </a>

            <!-- 2. Print Selected Items -->
            <button 
                type="button" 
                @click="printSelectedItems()" 
                :disabled="totalItemsCount === 0"
                class="hidden sm:inline-flex items-center justify-center px-3.5 py-2.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold text-xs sm:text-sm rounded-lg shadow-xs transition min-h-[44px] disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                title="Print only selected services, products, or issues"
            >
                <svg class="w-4 h-4 mr-1.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                Print Selected Items
            </button>

            <!-- 3. Pay Button -->
            @if($jobCard->status !== 'cancelled')
                <button 
                    type="button" 
                    @click="showPayModal = true"
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm rounded-lg shadow-xs transition cursor-pointer min-h-[44px] flex-1 sm:flex-initial"
                    title="Record customer payment, advance, or deposit"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Pay
                </button>
            @endif

            <!-- 4. Print Bill (Directly generates 80mm Sales Bill without invoice dependency) -->
            <a 
                href="{{ route('operations.job-cards.print-bill', $jobCard) }}" 
                @click.prevent="AutoCarePrinter.printUrl('{{ route('operations.job-cards.print-bill', $jobCard) }}', { type: 'bill', title: 'Sales Bill - Job #{{ $jobCard->job_number }}', documentId: {{ $jobCard->id }}, documentNumber: '{{ $jobCard->job_number }}', fallbackUrl: '{{ route('operations.job-cards.print-bill', $jobCard) }}' })"
                target="_blank"
                class="inline-flex items-center justify-center px-3.5 py-2.5 bg-indigo-50 border border-indigo-200 text-indigo-700 hover:bg-indigo-100 font-bold text-xs sm:text-sm rounded-lg shadow-xs transition min-h-[44px] flex-1 sm:flex-initial"
                title="Print 80mm Sales Bill"
            >
                <svg class="w-4 h-4 mr-1.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Print Bill
            </a>

            <!-- 5. Generate Invoice (Independent official billing document) -->
            @if(!$jobCard->invoice_id)
                <form action="{{ route('operations.job-cards.generate-invoice', $jobCard) }}" method="POST" class="hidden sm:inline">
                    @csrf
                    <button 
                        type="submit" 
                        class="inline-flex items-center justify-center px-4 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white font-bold text-xs sm:text-sm rounded-lg shadow-xs transition cursor-pointer min-h-[44px]"
                        title="Generate official invoice"
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
                    class="hidden sm:inline-flex items-center justify-center px-3.5 py-2.5 bg-white border border-gray-300 text-gray-800 font-bold text-xs sm:text-sm rounded-lg hover:bg-gray-50 shadow-xs transition min-h-[44px]"
                    title="View Official Invoice"
                >
                    <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Invoice ({{ $jobCard->invoice->invoice_number }})
                </a>
            @endif

            <!-- Workflow Status Actions -->
            @if($jobCard->status === 'pending')
                <form action="{{ route('operations.job-cards.status', $jobCard) }}" method="POST" class="inline flex-1 sm:flex-initial">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="start_work">
                    <button 
                        type="submit" 
                        class="w-full sm:w-auto inline-flex items-center justify-center px-3.5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm rounded-lg shadow-xs transition cursor-pointer min-h-[44px]"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Start Work
                    </button>
                </form>
            @elseif(in_array($jobCard->status, ['in_progress', 'additional_work']))
                <button 
                    type="button" 
                    @click="showAddServiceModal = true"
                    class="hidden sm:inline-flex items-center justify-center px-3 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold text-xs sm:text-sm rounded-lg shadow-xs transition min-h-[44px]"
                >
                    <svg class="w-4 h-4 mr-1 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Service
                </button>
                <button 
                    type="button" 
                    @click="showAddProductModal = true"
                    class="hidden sm:inline-flex items-center justify-center px-3 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold text-xs sm:text-sm rounded-lg shadow-xs transition min-h-[44px]"
                >
                    <svg class="w-4 h-4 mr-1 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Product
                </button>
                @if($jobCard->status === 'in_progress')
                    <form action="{{ route('operations.job-cards.status', $jobCard) }}" method="POST" class="inline flex-1 sm:flex-initial">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="action" value="complete_job">
                        <button 
                            type="submit" 
                            class="w-full sm:w-auto inline-flex items-center justify-center px-3.5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm rounded-lg shadow-xs transition cursor-pointer min-h-[44px]"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Complete Job
                        </button>
                    </form>
                @else
                    <form action="{{ route('operations.job-cards.additional-work.complete', $jobCard) }}" method="POST" class="inline flex-1 sm:flex-initial">
                        @csrf
                        <button 
                            type="submit" 
                            class="w-full sm:w-auto inline-flex items-center justify-center px-3.5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm rounded-lg shadow-xs transition cursor-pointer min-h-[44px]"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Complete Job
                        </button>
                    </form>
                @endif
            @elseif(in_array($jobCard->status, ['completed', 'delivered']))
                <button 
                    type="button" 
                    @click="showWhatsAppModal = true"
                    class="hidden sm:inline-flex items-center px-3 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 font-semibold text-xs sm:text-sm rounded-lg shadow-xs transition min-h-[44px]"
                    title="Prepare manual WhatsApp message for customer"
                >
                    <svg class="w-4 h-4 mr-1 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                    WhatsApp
                </button>
                @if($jobCard->status === 'completed')
                    @if($canDeliver)
                        <form action="{{ route('operations.job-cards.status', $jobCard) }}" method="POST" class="inline flex-1 sm:flex-initial">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="mark_delivered">
                            <button 
                                type="submit" 
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs sm:text-sm rounded-lg shadow-xs transition cursor-pointer min-h-[44px]"
                            >
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Mark Delivered
                            </button>
                        </form>
                    @elseif($isAdmin)
                        <button 
                            type="button" 
                            @click="showDeliveryOverrideModal = true"
                            class="inline-flex items-center justify-center px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs sm:text-sm rounded-lg shadow-xs transition cursor-pointer min-h-[44px] flex-1 sm:flex-initial"
                            title="Outstanding balance LKR {{ number_format($balanceDue, 2) }}. Admin override required."
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Deliver (Admin Override)
                        </button>
                    @else
                        <button 
                            type="button" 
                            disabled
                            class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-200 text-gray-500 font-bold text-xs sm:text-sm rounded-lg shadow-xs cursor-not-allowed min-h-[44px] flex-1 sm:flex-initial"
                            title="Unpaid balance LKR {{ number_format($balanceDue, 2) }}. Delivery requires full payment or Admin override."
                        >
                            <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Delivery Locked
                        </button>
                    @endif
                @endif
            @endif
        </div>

            <!-- Secondary Actions Dropdown or Menu Button -->
            <div class="relative" x-data="{ openMenu: false }">
                <button 
                    type="button" 
                    @click="openMenu = !openMenu" 
                    class="inline-flex items-center justify-center px-3 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold text-xs sm:text-sm rounded-lg hover:bg-gray-50 shadow-xs transition min-h-[44px]"
                    title="More actions"
                >
                    <span>More</span>
                    <svg class="w-4 h-4 ml-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div 
                    x-show="openMenu" 
                    @click.away="openMenu = false"
                    class="absolute right-0 mt-2 w-56 sm:w-60 bg-white rounded-xl shadow-lg border border-gray-200 py-1.5 z-40 text-xs"
                    style="display: none;"
                >
                    <!-- Mobile-only secondary quick actions (< sm) -->
                    <div class="sm:hidden border-b border-gray-100 pb-1 mb-1">
                        <!-- Print Job Card -->
                        <a 
                            href="{{ route('operations.job-cards.print', $jobCard) }}" 
                            @click="openMenu = false; AutoCarePrinter.printUrl('{{ route('operations.job-cards.print', $jobCard) }}', { type: 'job_card', title: 'Job Card #{{ $jobCard->job_number }}', documentId: {{ $jobCard->id }}, documentNumber: '{{ $jobCard->job_number }}', fallbackUrl: '{{ route('operations.job-cards.print', $jobCard) }}' })"
                            target="_blank"
                            class="w-full text-left px-4 py-2 hover:bg-gray-50 text-gray-700 flex items-center"
                        >
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Print Job Card
                        </a>

                        <!-- Print Selected Items -->
                        <button 
                            type="button" 
                            @click="openMenu = false; printSelectedItems()" 
                            :disabled="totalItemsCount === 0"
                            class="w-full text-left px-4 py-2 hover:bg-gray-50 text-gray-700 flex items-center disabled:opacity-50"
                        >
                            <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            Print Selected Items
                        </button>

                        <!-- Invoice -->
                        @if(!$jobCard->invoice_id)
                            <form action="{{ route('operations.job-cards.generate-invoice', $jobCard) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-50 text-gray-700 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-[#0B1849]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Generate Invoice
                                </button>
                            </form>
                        @else
                            <a href="{{ route('operations.invoices.show', $jobCard->invoice) }}" class="w-full text-left px-4 py-2 hover:bg-gray-50 text-emerald-700 flex items-center font-medium">
                                <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                View Invoice
                            </a>
                        @endif

                        @if(in_array($jobCard->status, ['in_progress', 'additional_work']))
                            <button type="button" @click="showAddServiceModal = true; openMenu = false" class="w-full text-left px-4 py-2 hover:bg-gray-50 text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add Service
                            </button>
                            <button type="button" @click="showAddProductModal = true; openMenu = false" class="w-full text-left px-4 py-2 hover:bg-gray-50 text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                Add Product
                            </button>
                        @endif

                        @if(in_array($jobCard->status, ['completed', 'delivered']))
                            <button type="button" @click="showWhatsAppModal = true; openMenu = false" class="w-full text-left px-4 py-2 hover:bg-gray-50 text-emerald-700 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                </svg>
                                WhatsApp Customer
                            </button>
                        @endif
                    </div>

                    <!-- Assign / Change Tech -->
                    <button 
                        type="button" 
                        @click="showEditModal = true; openMenu = false" 
                        class="w-full text-left px-4 py-2 hover:bg-gray-50 text-gray-700 flex items-center"
                    >
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ $jobCard->assigned_to ? 'Change Technician' : 'Assign Technician' }}
                    </button>
                    <!-- Edit Info -->
                    @if($jobCard->status !== 'cancelled')
                        <button 
                            type="button" 
                            @click="showEditModal = true; openMenu = false" 
                            class="w-full text-left px-4 py-2 hover:bg-gray-50 text-gray-700 flex items-center"
                        >
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Edit Job Details
                        </button>
                    @endif

                    @if($jobCard->status === 'delivered')
                        <form action="{{ route('operations.job-cards.follow-up', $jobCard) }}" method="POST">
                            @csrf
                            <button 
                                type="submit" 
                                class="w-full text-left px-4 py-2 hover:bg-gray-50 text-gray-700 flex items-center"
                            >
                                <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Follow-up Job Card
                            </button>
                        </form>
                    @endif

                    <!-- Cancel Job Button -->
                    @if($jobCard->status !== 'cancelled' && $jobCard->status !== 'delivered' && (Auth::user()->isAdmin() || Auth::user()->hasPermission('cancel_jobs')))
                        <div class="border-t border-gray-100 my-1"></div>
                        <button 
                            type="button" 
                            @click="showCancelModal = true; openMenu = false" 
                            class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600 flex items-center"
                        >
                            <svg class="w-4 h-4 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancel Job Card
                        </button>
                    @endif
                </div>
            </div>
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

        <!-- CARPET DETAILS (COMPACT & OPTIONAL) -->
        @if($jobCard->carpets->isNotEmpty())
            <div class="px-5 py-3 bg-slate-50 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex items-center space-x-2">
                    <span class="font-bold uppercase tracking-wider text-gray-700 text-[11px]">CARPET DETAILS:</span>
                    <div class="flex flex-wrap items-center gap-2">
                        @foreach($jobCard->carpets as $carpet)
                            <span class="inline-flex items-center px-2.5 py-1 bg-white border border-gray-200 rounded-md font-semibold text-gray-800 shadow-2xs">
                                {{ $carpet->category_name }}: <span class="ml-1 text-[#0B1849] font-black">{{ $carpet->quantity }}</span>
                            </span>
                        @endforeach
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <label class="inline-flex items-center space-x-1.5 cursor-pointer text-gray-600 font-medium select-none" title="Include carpet details when printing selected items">
                        <input 
                            type="checkbox" 
                            x-model="includeCarpetsInPrint"
                            class="rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849] cursor-pointer w-3.5 h-3.5"
                        >
                        <span class="text-[11px]">Include in Print Selected</span>
                    </label>

                    @if(!in_array($jobCard->status, ['completed', 'delivered', 'cancelled']))
                        <button 
                            type="button" 
                            @click="showEditCarpetsModal = true"
                            class="text-xs text-[#0B1849] hover:underline font-bold inline-flex items-center cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Edit Carpets
                        </button>
                    @endif
                </div>
            </div>
        @elseif($jobCard->vehicleType && $jobCard->vehicleType->enable_carpets && !in_array($jobCard->status, ['completed', 'delivered', 'cancelled']))
            <div class="px-5 py-2.5 bg-slate-50/50 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span class="italic text-[11px]">No carpet details recorded for this visit.</span>
                <button 
                    type="button" 
                    @click="showEditCarpetsModal = true"
                    class="text-xs text-[#0B1849] hover:underline font-bold cursor-pointer"
                >
                    + Add Carpet Details
                </button>
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
                        <th class="px-3 py-2.5 w-8 text-center">
                            <input 
                                type="checkbox" 
                                @change="selectedIssues = $event.target.checked ? [{{ $jobCard->issues->pluck('id')->implode(',') }}] : []"
                                :checked="selectedIssues.length === {{ $jobCard->issues->count() }} && {{ $jobCard->issues->count() }} > 0"
                                class="rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849] cursor-pointer"
                                title="Select/Deselect all issues"
                            >
                        </th>
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
                        <tr class="hover:bg-slate-50/60 transition" :class="selectedIssues.includes({{ $issue->id }}) ? 'bg-indigo-50/30' : ''">
                            <td class="px-3 py-3 text-center">
                                <input 
                                    type="checkbox" 
                                    value="{{ $issue->id }}" 
                                    x-model.number="selectedIssues"
                                    class="rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849] cursor-pointer"
                                    title="Select issue for printing"
                                >
                            </td>
                            <td class="px-5 py-3 font-medium text-gray-900">
                                <div>{{ $issue->description }}</div>
                                <div class="text-[11px] text-gray-400 mt-0.5">Recorded by {{ $issue->recordedByUser?->name ?? 'Staff' }} on {{ $issue->created_at ? $issue->created_at->setTimezone('Asia/Colombo')->format('M d, Y') : '' }}</div>
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
                            <td colspan="7" class="px-5 py-6 text-center text-xs sm:text-sm text-gray-400">
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

    <!-- 4. PAYMENTS & ADVANCES SECTION -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-gray-50/50">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="p-1.5 bg-emerald-100 text-emerald-800 rounded-md">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </span>
                    <h3 class="text-base font-bold text-gray-900">Payments & Advances Received</h3>
                </div>
                <p class="text-xs text-gray-500 mt-0.5">Record customer advance deposits before work, progress payments, or final balance settlements.</p>
            </div>
            <div class="flex items-center gap-2">
                <a 
                    href="{{ route('operations.job-cards.print-bill', $jobCard) }}" 
                    @click.prevent="AutoCarePrinter.printUrl('{{ route('operations.job-cards.print-bill', $jobCard) }}', { type: 'bill', title: 'Sales Bill - Job #{{ $jobCard->job_number }}', documentId: {{ $jobCard->id }}, documentNumber: '{{ $jobCard->job_number }}', fallbackUrl: '{{ route('operations.job-cards.print-bill', $jobCard) }}' })"
                    target="_blank"
                    class="inline-flex items-center px-3 py-1.5 bg-indigo-50 border border-indigo-200 text-indigo-700 hover:bg-indigo-100 font-bold text-xs rounded-lg shadow-xs transition"
                >
                    <svg class="w-3.5 h-3.5 mr-1 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Bill
                </a>
                <button 
                    type="button" 
                    @click="showPayModal = true"
                    class="inline-flex items-center px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow-xs transition cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Pay
                </button>
            </div>
        </div>

        <!-- Financial Status Cards -->
        <div class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-3 gap-3 bg-slate-50/60 border-b border-gray-200 text-xs">
            <div class="p-3 bg-white border border-gray-200 rounded-lg">
                <span class="text-gray-500 uppercase tracking-wider font-semibold text-[11px] block">
                    {{ $invoice ? 'Final Invoice Total' : 'Estimated Total' }}
                </span>
                <span class="text-base sm:text-lg font-black text-gray-900 mt-0.5 block">
                    LKR {{ number_format($invoiceTotal, 2) }}
                </span>
            </div>
            <div class="p-3 bg-white border border-gray-200 rounded-lg">
                <span class="text-gray-500 uppercase tracking-wider font-semibold text-[11px] block">Total Received</span>
                <span class="text-base sm:text-lg font-black text-emerald-600 mt-0.5 block">
                    LKR {{ number_format($paidAmount, 2) }}
                </span>
            </div>
            <div class="p-3 bg-white border border-gray-200 rounded-lg">
                <span class="text-gray-500 uppercase tracking-wider font-semibold text-[11px] block">Remaining Balance</span>
                <span class="text-base sm:text-lg font-black {{ $balanceDue > 0 ? 'text-amber-600' : 'text-gray-900' }} mt-0.5 block">
                    LKR {{ number_format($balanceDue, 2) }}
                </span>
            </div>
        </div>

        <!-- Payments Table -->
        @php
            $paymentsList = $jobCard->payments()->with('creator')->orderBy('id', 'asc')->get();
        @endphp
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 border-b border-gray-200">
                        <th class="px-4 py-2.5 font-bold uppercase tracking-wider text-[11px]">Receipt No</th>
                        <th class="px-4 py-2.5 font-bold uppercase tracking-wider text-[11px]">Date</th>
                        <th class="px-4 py-2.5 font-bold uppercase tracking-wider text-[11px]">Type</th>
                        <th class="px-4 py-2.5 font-bold uppercase tracking-wider text-[11px]">Method</th>
                        <th class="px-4 py-2.5 font-bold uppercase tracking-wider text-[11px]">Reference / Remarks</th>
                        <th class="px-4 py-2.5 font-bold uppercase tracking-wider text-[11px]">Received By</th>
                        <th class="px-4 py-2.5 text-right font-bold uppercase tracking-wider text-[11px]">Amount</th>
                        <th class="px-4 py-2.5 text-right font-bold uppercase tracking-wider text-[11px]">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($paymentsList as $paymentItem)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="px-4 py-3 font-mono font-bold text-gray-900">
                                {{ $paymentItem->payment_number }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                {{ $paymentItem->payment_date ? \Carbon\Carbon::parse($paymentItem->payment_date)->format('M d, Y') : '—' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($paymentItem->is_advance || !$paymentItem->invoice_id)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Advance Deposit</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Invoice Payment</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700 capitalize font-medium">
                                {{ str_replace('_', ' ', $paymentItem->payment_method) }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 max-w-xs truncate">
                                @if($paymentItem->reference_number)
                                    <span class="font-semibold text-gray-700">Ref: {{ $paymentItem->reference_number }}</span>
                                @endif
                                @if($paymentItem->notes)
                                    <div class="text-[11px] text-gray-400 truncate">{{ $paymentItem->notes }}</div>
                                @endif
                                @if(!$paymentItem->reference_number && !$paymentItem->notes)
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $paymentItem->creator?->name ?? 'Staff' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-black text-gray-900 whitespace-nowrap">
                                LKR {{ number_format($paymentItem->amount, 2) }}
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a 
                                    href="{{ route('billing.payments.thermal', $paymentItem) }}" 
                                    @click.prevent="AutoCarePrinter.printUrl('{{ route('billing.payments.thermal', $paymentItem) }}', { type: 'payment_receipt', title: 'Payment Receipt #{{ $paymentItem->payment_number }}', documentId: {{ $paymentItem->id }}, documentNumber: '{{ $paymentItem->payment_number }}', fallbackUrl: '{{ route('billing.payments.thermal', $paymentItem) }}' })"
                                    target="_blank"
                                    class="inline-flex items-center px-2.5 py-1 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded text-[11px] font-semibold transition shadow-2xs"
                                    title="Print Payment Receipt"
                                >
                                    <svg class="w-3 h-3 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    Receipt
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-6 text-center text-xs sm:text-sm text-gray-400">
                                No payments recorded yet for this job. Click <span class="font-semibold text-emerald-700">"Pay"</span> to record an advance or balance payment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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

            <div class="relative bg-white rounded-2xl w-full max-w-[calc(100vw-2rem)] sm:max-w-lg p-4 sm:p-6 max-h-[88vh] overflow-y-auto shadow-xl border border-gray-200 space-y-5">
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

            <div class="relative bg-white rounded-2xl w-full max-w-[calc(100vw-2rem)] sm:max-w-lg p-4 sm:p-6 max-h-[88vh] overflow-y-auto shadow-xl border border-gray-200 space-y-5">
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

            <div class="relative bg-white rounded-2xl w-full max-w-[calc(100vw-2rem)] sm:max-w-lg p-4 sm:p-6 max-h-[88vh] overflow-y-auto shadow-xl border border-gray-200 space-y-4">
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

            <div class="relative bg-white rounded-2xl w-full max-w-[calc(100vw-2rem)] sm:max-w-lg p-4 sm:p-6 max-h-[88vh] overflow-y-auto shadow-xl border border-gray-200 space-y-4">
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

            <div class="relative bg-white rounded-2xl w-full max-w-[calc(100vw-2rem)] sm:max-w-lg p-4 sm:p-6 max-h-[88vh] overflow-y-auto shadow-xl border border-gray-200 space-y-4">
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

    <!-- MODAL: ADMIN DELIVERY OVERRIDE -->
    <div 
        x-show="showDeliveryOverrideModal" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" @click="showDeliveryOverrideModal = false"></div>

            <div class="relative bg-white rounded-2xl w-full max-w-[calc(100vw-2rem)] sm:max-w-lg p-4 sm:p-6 max-h-[88vh] overflow-y-auto shadow-xl border border-gray-200 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="p-2 bg-amber-100 text-amber-800 rounded-lg">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Authorize Delivery (Admin Override)</h3>
                            <p class="text-xs text-gray-500">Unpaid / Partially Paid Vehicle Release</p>
                        </div>
                    </div>
                    <button type="button" @click="showDeliveryOverrideModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800 space-y-1">
                    <p class="font-bold">Notice:</p>
                    <p>This vehicle has an outstanding balance of <span class="font-extrabold">LKR {{ number_format($balanceDue, 2) }}</span>. Center policy requires full payment or an active company credit limit before release.</p>
                    <p>As an Admin, you may authorize release. An override reason is required and will be permanently recorded in the system audit log.</p>
                </div>

                <form action="{{ route('operations.job-cards.status', $jobCard) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="mark_delivered">

                    <div>
                        <label for="delivery_override_reason" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Override Reason <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="delivery_override_reason" 
                            id="delivery_override_reason" 
                            rows="3"
                            required
                            minlength="5"
                            placeholder="e.g. Authorized by Branch Manager. Client promised cheque payment tomorrow morning."
                            class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-3 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="showDeliveryOverrideModal = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition min-h-[40px]"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-lg shadow-xs transition min-h-[44px]"
                        >
                            Confirm Override & Deliver
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: MANUAL WHATSAPP MESSAGE PREPARATION -->
    <div 
        x-show="showWhatsAppModal" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" @click="showWhatsAppModal = false"></div>

            <div class="relative bg-white rounded-2xl w-full max-w-[calc(100vw-2rem)] sm:max-w-lg p-4 sm:p-6 max-h-[88vh] overflow-y-auto shadow-xl border border-gray-200 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="p-2 bg-emerald-100 text-emerald-800 rounded-lg">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Manual WhatsApp Message</h3>
                            <p class="text-xs text-gray-500">Service Completion Notification</p>
                        </div>
                    </div>
                    <button type="button" @click="showWhatsAppModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Customer Phone / WhatsApp Number
                        </label>
                        <div class="flex items-center space-x-2">
                            <input 
                                type="text" 
                                x-model="whatsappPhone"
                                placeholder="e.g. 94712345678"
                                class="block w-full py-2.5 px-3 text-sm font-mono text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                            >
                        </div>
                        <p class="text-[11px] text-gray-500 mt-1">Sri Lankan numbers are automatically normalized to international format (e.g. 94712345678).</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                Message Preview
                            </label>
                            <button 
                                type="button" 
                                @click="copyWhatsAppText()"
                                class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition"
                            >
                                <span x-text="copiedWhatsApp ? 'Copied to Clipboard!' : 'Copy Text'"></span>
                            </button>
                        </div>
                        <textarea 
                            x-model="whatsappText"
                            rows="7"
                            class="block w-full py-2.5 px-3 text-xs font-mono text-gray-800 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-slate-50"
                        ></textarea>
                    </div>

                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-800">
                        <p class="font-semibold">Workflow note:</p>
                        <p class="mt-0.5">Clicking "Open in WhatsApp" will open WhatsApp Web or the app with this message prefilled. You will review and manually click Send in WhatsApp.</p>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-gray-100">
                    <button 
                        type="button" 
                        @click="showWhatsAppModal = false"
                        class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition min-h-[40px]"
                    >
                        Close
                    </button>
                    <button 
                        type="button" 
                        @click="openWhatsApp()"
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-xs transition flex items-center min-h-[44px]"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        Open in WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 8: RECORD PAYMENT / ADVANCE -->
    <div 
        x-show="showPayModal" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" @click="showPayModal = false"></div>

            <div class="relative bg-white rounded-2xl w-full max-w-[calc(100vw-2rem)] sm:max-w-lg p-4 sm:p-6 max-h-[88vh] overflow-y-auto shadow-xl border border-gray-200 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="p-2 bg-emerald-100 text-emerald-800 rounded-lg">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">
                                {{ $invoice ? 'Record Invoice Payment' : 'Record Customer Payment / Advance' }}
                            </h3>
                            <p class="text-xs text-gray-500">Job Card #{{ $jobCard->job_number }}</p>
                        </div>
                    </div>
                    <button type="button" @click="showPayModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Job Card Financial Overview Box -->
                <div class="p-3.5 bg-slate-50 border border-gray-200 rounded-xl text-xs space-y-2">
                    <div class="grid grid-cols-2 gap-2 text-gray-700">
                        <div>
                            <span class="text-gray-500 block text-[11px]">Job Card:</span>
                            <span class="font-bold text-gray-900">#{{ $jobCard->job_number }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block text-[11px]">Vehicle:</span>
                            <span class="font-bold text-gray-900">{{ $jobCard->vehicle?->vehicle_number ?? 'N/A' }} ({{ $jobCard->vehicleType?->name ?? 'Vehicle' }})</span>
                        </div>
                    </div>
                    <div class="text-gray-700">
                        <span class="text-gray-500 block text-[11px]">Customer:</span>
                        <span class="font-bold text-gray-900">
                            {{ $jobCard->customer?->customer_name }}
                            @if($jobCard->customer?->company_name)
                                <span class="text-gray-500 font-normal">({{ $jobCard->customer->company_name }})</span>
                            @endif
                        </span>
                    </div>
                    <div class="pt-2 border-t border-gray-200 grid grid-cols-3 gap-2 text-center">
                        <div class="p-2 bg-white rounded border border-gray-100">
                            <span class="text-[10px] text-gray-500 block uppercase font-bold">{{ $invoice ? 'Invoice Total' : 'Est. Total' }}</span>
                            <span class="font-black text-gray-900 text-xs">LKR {{ number_format($invoiceTotal, 2) }}</span>
                        </div>
                        <div class="p-2 bg-white rounded border border-gray-100">
                            <span class="text-[10px] text-gray-500 block uppercase font-bold">Total Paid</span>
                            <span class="font-black text-emerald-600 text-xs">LKR {{ number_format($paidAmount, 2) }}</span>
                        </div>
                        <div class="p-2 bg-white rounded border border-gray-100">
                            <span class="text-[10px] text-gray-500 block uppercase font-bold">Remaining</span>
                            <span class="font-black {{ $balanceDue > 0 ? 'text-amber-600' : 'text-gray-900' }} text-xs">LKR {{ number_format($balanceDue, 2) }}</span>
                        </div>
                    </div>

                    @if($creditAccount && $creditAccount->isActive())
                        <div class="p-2 bg-blue-50/80 border border-blue-200 rounded text-blue-900 text-[11px] flex justify-between items-center">
                            <span><span class="font-bold">Active Company Credit:</span> {{ $jobCard->customer->company_name ?? $jobCard->customer->customer_name }}</span>
                            <span class="font-bold">Avail: LKR {{ number_format($creditAccount->available_credit, 2) }}</span>
                        </div>
                    @endif
                </div>

                <form 
                    action="{{ route('operations.job-cards.payments.store', $jobCard) }}" 
                    method="POST" 
                    class="space-y-3.5"
                    @submit="if (isSubmittingPayment) { $event.preventDefault(); return false; } isSubmittingPayment = true;"
                >
                    @csrf

                    <div>
                        <label for="pay_amount" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Payment Amount (LKR) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="amount" 
                            id="pay_amount" 
                            x-model="paymentAmount"
                            placeholder="Enter amount"
                            min="0.01"
                            class="block w-full py-2.5 px-3 text-sm font-mono font-bold text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                            required
                        >
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="pay_method" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Payment Method <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="payment_method" 
                                id="pay_method" 
                                x-model="paymentMethod"
                                class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                                required
                            >
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                                <option value="credit">Approved Company Credit</option>
                            </select>
                        </div>
                        <div>
                            <label for="pay_date" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Payment Date <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                name="payment_date" 
                                id="pay_date" 
                                value="{{ date('Y-m-d') }}"
                                max="{{ date('Y-m-d') }}"
                                class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                                required
                            >
                        </div>
                    </div>

                    <!-- Company Credit Notice -->
                    <div x-show="paymentMethod === 'credit'" class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-900">
                        <p class="font-bold">Company Credit Policy:</p>
                        <p class="mt-0.5">Approved Company Credit is a billing arrangement and will not appear as liquid cash in the Money Book. It will be billed to the company's credit account on their monthly statement.</p>
                    </div>

                    <!-- Cheque Policy Notice -->
                    <div x-show="paymentMethod === 'cheque'" class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-900">
                        <p class="font-bold">Cheque Policy:</p>
                        <p class="mt-0.5">Cheques are subject to bank clearance. Please enter the cheque number and bank details in the reference field below.</p>
                    </div>

                    <div>
                        <label for="pay_reference" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Payment Reference <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <input 
                            type="text" 
                            name="reference_number" 
                            id="pay_reference" 
                            placeholder="e.g. Card Slip / Bank Ref / Cheque #123456"
                            class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                        >
                    </div>

                    <div>
                        <label for="pay_notes" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Remarks <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <textarea 
                            name="notes" 
                            id="pay_notes" 
                            rows="2" 
                            placeholder="Additional payment notes or deposit instructions"
                            class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-3 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="showPayModal = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition min-h-[40px]"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            :disabled="isSubmittingPayment"
                            class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-xs transition disabled:opacity-50 min-h-[44px] cursor-pointer flex items-center"
                        >
                            <span x-show="!isSubmittingPayment">Save Payment</span>
                            <span x-show="isSubmittingPayment" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Carpet Details Modal -->
    @if($jobCard->vehicleType && $jobCard->vehicleType->enable_carpets && !in_array($jobCard->status, ['completed', 'delivered', 'cancelled']))
        <div 
            x-show="showEditCarpetsModal" 
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-4"
            x-cloak
            style="display: none;"
        >
            <div 
                @click.away="showEditCarpetsModal = false" 
                class="bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden"
            >
                <div class="px-6 py-4 bg-[#0B1849] text-white flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider">Edit Carpet Details</h3>
                        <p class="text-xs text-gray-300 mt-0.5">Vehicle Type: {{ $jobCard->vehicleType->name }}</p>
                    </div>
                    <button type="button" @click="showEditCarpetsModal = false" class="text-gray-300 hover:text-white cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('operations.job-cards.carpets.update', $jobCard) }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PATCH')

                    <p class="text-xs text-gray-500">
                        Tick the applicable carpets and specify positive whole-number quantities. Unchecked categories will be removed.
                    </p>

                    <div class="space-y-3">
                        @foreach($jobCard->vehicleType->carpetCategories as $cat)
                            @php
                                $existingCarpet = $jobCard->carpets->firstWhere('carpet_category_id', $cat->id);
                                $isChecked = $existingCarpet !== null;
                                $existingQty = $existingCarpet ? (int)$existingCarpet->quantity : 1;
                            @endphp
                            <div 
                                x-data="{ checked: {{ $isChecked ? 'true' : 'false' }}, qty: {{ $existingQty }} }" 
                                class="flex items-center justify-between p-3 rounded-lg border transition-all"
                                :class="checked ? 'bg-[#EEF1FA]/70 border-[#0B1849]/30 ring-1 ring-[#0B1849]/20' : 'bg-gray-50 border-gray-200'"
                            >
                                <label class="flex items-center space-x-2.5 cursor-pointer select-none text-xs font-bold text-gray-800 flex-1 mr-2">
                                    <input 
                                        type="checkbox" 
                                        x-model="checked"
                                        class="w-4 h-4 text-[#0B1849] rounded border-gray-300 focus:ring-[#0B1849] cursor-pointer"
                                    >
                                    <span>{{ $cat->name }}</span>
                                </label>

                                <div x-show="checked" class="flex items-center space-x-1.5 shrink-0">
                                    <span class="text-[11px] font-bold text-gray-600">Qty:</span>
                                    <input 
                                        type="number" 
                                        min="1" 
                                        step="1"
                                        x-model.number="qty"
                                        :name="checked ? 'carpets[{{ $cat->id }}][quantity]' : ''"
                                        class="w-16 py-1 px-2 text-xs font-black text-center text-gray-900 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-1 focus:ring-[#0B1849] min-h-[30px]"
                                    >
                                    <input 
                                        type="hidden" 
                                        :name="checked ? 'carpets[{{ $cat->id }}][category_id]' : ''" 
                                        value="{{ $cat->id }}"
                                    >
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-3 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="showEditCarpetsModal = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition min-h-[40px] cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-bold rounded-lg shadow-xs transition min-h-[44px] cursor-pointer"
                        >
                            Save Carpet Details
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
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
            showDeliveryOverrideModal: false,
            showWhatsAppModal: false,
            showPayModal: false,
            showEditCarpetsModal: false,

            // Payment modal state
            paymentAmount: '{{ $balanceDue > 0 ? $balanceDue : "" }}',
            paymentMethod: 'cash',
            isSubmittingPayment: false,

            // WhatsApp state
            whatsappPhone: config.whatsappPhone || '',
            whatsappText: config.whatsappText || '',
            copiedWhatsApp: false,

            copyWhatsAppText() {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(this.whatsappText).then(() => {
                        this.copiedWhatsApp = true;
                        setTimeout(() => { this.copiedWhatsApp = false; }, 2000);
                    });
                }
            },

            openWhatsApp() {
                if (!this.whatsappPhone) {
                    alert('Please enter or verify the customer mobile phone number.');
                    return;
                }
                let url = 'https://wa.me/' + encodeURIComponent(this.whatsappPhone) + '?text=' + encodeURIComponent(this.whatsappText);
                window.open(url, '_blank');
            },

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
            selectedIssues: [],
            includeCarpetsInPrint: config.hasCarpets || false,
            totalItemsCount: config.totalItems || 0,

            printSelectedItems() {
                const hasSelected = this.selectedServices.length > 0 || this.selectedProducts.length > 0 || this.selectedIssues.length > 0 || this.includeCarpetsInPrint;
                if (!hasSelected) {
                    alert('Please select at least one item or carpet section to print.');
                    return;
                }
                const params = new URLSearchParams();
                params.append('selected', '1');
                this.selectedServices.forEach(id => params.append('services[]', id));
                this.selectedProducts.forEach(id => params.append('products[]', id));
                this.selectedIssues.forEach(id => params.append('issues[]', id));
                if (this.includeCarpetsInPrint) {
                    params.append('carpets', '1');
                }
                const targetUrl = config.printBaseUrl + '?' + params.toString();
                if (window.AutoCarePrinter && window.AutoCarePrinter.currentConfig?.auto_route) {
                    window.AutoCarePrinter.printUrl(targetUrl, {
                        type: 'job_card',
                        title: 'Selected Items - Job #' + (config.jobNumber || ''),
                        documentId: config.jobCardId,
                        documentNumber: config.jobNumber,
                        fallbackUrl: targetUrl
                    });
                } else {
                    window.open(targetUrl, '_blank');
                }
            }
        };
    };
</script>
@endsection
