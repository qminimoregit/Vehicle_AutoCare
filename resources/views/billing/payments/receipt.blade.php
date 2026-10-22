<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Receipt - {{ $payment->payment_number }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body { background: #ffffff !important; }
            .no-print { display: none !important; }
            .print-shadow-none { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 antialiased min-h-screen py-8 px-4 sm:px-6">
    @php
        $jobCard = $payment->jobCard ?? $payment->invoice?->jobCard;
        $invoice = $payment->invoice;
        $customer = $payment->customer ?? ($jobCard?->customer ?? $invoice?->customer);
        $jobTotal = $jobCard ? (float)$jobCard->grand_total : ($invoice ? (float)$invoice->grand_total : (float)$payment->amount);

        if ($jobCard) {
            $totalPaidToDate = (float) \App\Models\Payment::where('job_card_id', $jobCard->id)
                ->where('status', 'completed')
                ->where('id', '<=', $payment->id)
                ->sum('amount');
        } elseif ($invoice) {
            $totalPaidToDate = (float) \App\Models\Payment::where('invoice_id', $invoice->id)
                ->where('status', 'completed')
                ->where('id', '<=', $payment->id)
                ->sum('amount');
        } else {
            $totalPaidToDate = (float) $payment->amount;
        }

        $remDue = max(0, round($jobTotal - $totalPaidToDate, 2));
        $payStatusStr = ($remDue <= 0.0001) ? 'Paid in Full' : 'Partially Paid';
        $branch = $jobCard?->branch ?? ($invoice?->branch ?? $company->mainBranch);
    @endphp

    <div class="max-w-3xl mx-auto space-y-4">
        <!-- Print / Navigation Toolbar -->
        <div class="no-print flex items-center justify-between bg-white px-5 py-3 rounded-xl border border-gray-200 shadow-xs">
            <div class="flex items-center space-x-3">
                @if($payment->job_card_id)
                    <a 
                        href="{{ route('operations.job-cards.show', $payment->job_card_id) }}" 
                        class="text-xs font-semibold text-[#0B1849] hover:underline inline-flex items-center"
                    >
                        &larr; Back to Job Card #{{ $jobCard?->job_number }}
                    </a>
                @elseif($payment->invoice_id && $invoice)
                    <a 
                        href="{{ route('operations.invoices.show', $payment->invoice_id) }}" 
                        class="text-xs font-semibold text-[#0B1849] hover:underline inline-flex items-center"
                    >
                        &larr; Back to Invoice #{{ $invoice->invoice_number }}
                    </a>
                @endif
                <span class="text-gray-300">|</span>
                <a 
                    href="{{ route('billing.payments.index') }}" 
                    class="text-xs font-semibold text-gray-600 hover:underline"
                >
                    Payments List
                </a>
            </div>
            <div class="flex items-center space-x-2">
                <button 
                    onclick="window.print()" 
                    class="inline-flex items-center px-4 py-2 text-xs font-bold rounded-lg text-white bg-[#0B1849] hover:bg-[#15286e] transition shadow-xs cursor-pointer min-h-[38px]"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Receipt
                </button>
            </div>
        </div>

        <!-- Official Receipt Document Card -->
        <div class="bg-white border border-gray-200 rounded-2xl p-8 sm:p-10 shadow-xs print-shadow-none space-y-6">
            <!-- Header: Company & Receipt Meta -->
            <div class="flex justify-between items-start border-b border-gray-200 pb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight text-[#0B1849]">
                        {{ $company->company_name ?? 'AutoCare Service Center (Pvt) Ltd' }}
                    </h1>
                    <p class="text-xs text-gray-500 mt-1 max-w-sm leading-relaxed">
                        {{ $branch?->address ?? ($company->address ?? 'Mattegoda, Sri Lanka') }}
                    </p>
                    <div class="text-xs text-gray-500 mt-1 space-x-2">
                        @if(!empty($branch?->phone ?? $company->phone)) <span><strong>Tel:</strong> {{ $branch?->phone ?? $company->phone }}</span> @endif
                        @if(!empty($company->email)) <span>| <strong>Email:</strong> {{ $company->email }}</span> @endif
                        @if(!empty($company->tax_number)) <span>| <strong>Tax:</strong> {{ $company->tax_number }}</span> @endif
                    </div>
                </div>

                <div class="text-right space-y-1">
                    <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200">
                        Payment Receipt
                    </span>
                    <div class="text-xl font-mono font-black text-gray-900">
                        {{ $payment->payment_number }}
                    </div>
                    <div class="text-xs text-gray-500">
                        Date: <span class="font-semibold text-gray-800">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('F d, Y') : 'N/A' }}</span>
                    </div>
                    @if($payment->status === 'reversed')
                        <div class="mt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 uppercase">
                                REVERSED PAYMENT
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Customer & Job/Invoice Details -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-50/80 p-5 rounded-xl border border-slate-200/80 text-xs sm:text-sm">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Received From:</span>
                    <div class="text-base font-bold text-gray-900">{{ $customer?->customer_name ?? 'Walk-in Customer' }}</div>
                    @if($customer && $customer->customer_type === 'company' && $customer->company_name)
                        <div class="text-xs text-blue-700 font-semibold mt-0.5">
                            {{ $customer->company_name }}
                        </div>
                    @endif
                    @if($customer?->phone)
                        <div class="text-xs text-gray-600 mt-1">
                            <strong>Phone:</strong> {{ $customer->phone }}
                        </div>
                    @endif
                    @if($customer?->address)
                        <div class="text-xs text-gray-500 mt-0.5">
                            {{ $customer->address }}
                        </div>
                    @endif
                </div>

                <div class="sm:text-right">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Applied Against:</span>
                    @if($jobCard)
                        <div class="text-sm font-bold text-gray-900">
                            Job Card #: <span class="font-mono text-[#0B1849]">{{ $jobCard->job_number }}</span>
                        </div>
                    @endif
                    @if($invoice)
                        <div class="text-sm font-bold text-gray-900 mt-0.5">
                            Invoice #: <span class="font-mono text-[#0B1849]">{{ $invoice->invoice_number }}</span>
                        </div>
                    @endif
                    @if($jobCard?->vehicle)
                        <div class="text-xs text-gray-600 mt-0.5">
                            Vehicle: <span class="font-semibold text-gray-800 uppercase">{{ $jobCard->vehicle->vehicle_number }}</span>
                        </div>
                    @endif
                    @if($branch)
                        <div class="text-xs text-gray-500 mt-0.5">
                            Branch: {{ $branch->name }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Breakdown Details -->
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-xs sm:text-sm">
                    <thead class="bg-gray-50 text-gray-600 font-bold uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4 text-left">Description</th>
                            <th class="py-3 px-4 text-left">Payment Method</th>
                            <th class="py-3 px-4 text-left">Reference #</th>
                            <th class="py-3 px-4 text-right">Amount (LKR)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-800 bg-white">
                        <tr>
                            <td class="py-3.5 px-4 font-bold text-gray-900">
                                {{ $invoice ? "Settlement on Invoice #{$invoice->invoice_number}" : "Payment for Job Card #{$jobCard?->job_number}" }}
                                @if($payment->notes)
                                    <div class="text-xs text-gray-500 font-normal mt-0.5">{{ $payment->notes }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 uppercase font-semibold text-xs text-gray-700">
                                {{ str_replace('_', ' ', $payment->payment_method) }}
                            </td>
                            <td class="py-3.5 px-4 text-gray-600 font-mono text-xs">
                                {{ $payment->reference_number ?: '—' }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-extrabold text-base text-emerald-700 whitespace-nowrap">
                                LKR {{ number_format($payment->amount, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Amount in Words -->
            @if(!empty($amountInWords))
                <div class="bg-slate-50 border-l-4 border-[#0B1849] p-3 rounded-r-lg text-xs text-gray-700">
                    <span class="font-bold text-gray-900 block text-[10px] uppercase tracking-wider mb-0.5">Amount in Words</span>
                    {{ $amountInWords }}
                </div>
            @endif

            <!-- Balances Summary Card -->
            <div class="bg-gray-50/80 rounded-xl p-4 border border-gray-200/80 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs">
                <div class="grid grid-cols-3 gap-6 text-center w-full sm:w-auto">
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Job / Invoice Total</span>
                        <span class="font-bold text-gray-900 text-sm">
                            LKR {{ number_format($jobTotal, 2) }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Total Paid to Date</span>
                        <span class="font-bold text-emerald-700 text-sm">
                            LKR {{ number_format($totalPaidToDate, 2) }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Remaining Due</span>
                        <span class="font-extrabold text-sm {{ $remDue > 0 ? 'text-amber-600' : 'text-emerald-700' }}">
                            LKR {{ number_format($remDue, 2) }}
                        </span>
                    </div>
                </div>

                <div class="text-right w-full sm:w-auto">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Payment Status</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold {{ $remDue <= 0.0001 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $payStatusStr }}
                    </span>
                </div>
            </div>

            <!-- Signatures / Footer -->
            <div class="grid grid-cols-2 gap-8 pt-10 border-t border-gray-200 text-xs">
                <div>
                    <div class="h-10 border-b border-gray-300 w-44"></div>
                    <span class="text-gray-500 mt-1 block">Customer Signature</span>
                </div>

                <div class="text-right">
                    <div class="h-10 border-b border-gray-300 w-44 ml-auto"></div>
                    <span class="text-gray-500 mt-1 block">
                        Authorized By: <strong class="text-gray-800">{{ $payment->creator->name ?? 'Staff' }}</strong>
                    </span>
                </div>
            </div>

            <div class="text-center text-[10px] text-gray-400 pt-4 border-t border-gray-100">
                This is an official computer-generated payment receipt from {{ $company->company_name ?? 'AutoCare Service Center' }}.
                Payment Reference: {{ $payment->payment_number }}.
            </div>
        </div>
    </div>
</body>
</html>
