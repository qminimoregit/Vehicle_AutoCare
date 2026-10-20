<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Supplier Payment Receipt - {{ $payment->payment_number ?? ('PAY-' . $payment->id) }}</title>
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
    <div class="max-w-3xl mx-auto space-y-4">
        <!-- Print / Back Toolbar -->
        <div class="no-print flex items-center justify-between bg-white px-4 py-3 rounded-xl border border-gray-200 shadow-xs">
            <a 
                href="{{ route('inventory.grn.show', $payment->grn_id) }}" 
                class="text-xs font-semibold text-[#0B1849] hover:underline inline-flex items-center"
            >
                &larr; Back to GRN #{{ $payment->grn->grn_number }}
            </a>
            <div class="flex items-center space-x-2">
                <button 
                    onclick="window.print()" 
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg text-white bg-[#0B1849] hover:bg-[#15286e] transition shadow-xs cursor-pointer"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Voucher / Receipt
                </button>
            </div>
        </div>

        <!-- Receipt Card -->
        <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-xs print-shadow-none space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-start border-b border-gray-200 pb-6">
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-[#0B1849]">
                        {{ $company->company_name ?? 'AutoCare Service Center (Pvt) Ltd' }}
                    </h1>
                    <p class="text-xs text-gray-500 mt-1 max-w-sm">
                        {{ $company->address ?? 'No. 124, High Level Road, Colombo, Sri Lanka' }}
                    </p>
                    <div class="text-xs text-gray-500 mt-1 space-x-2">
                        @if(!empty($company->phone)) <span>Tel: {{ $company->phone }}</span> @endif
                        @if(!empty($company->tax_number)) <span>| VAT Reg: {{ $company->tax_number }}</span> @endif
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200 mb-1">
                        Supplier Payment Voucher
                    </span>
                    <div class="text-lg font-mono font-bold text-gray-900">
                        {{ $payment->payment_number ?? sprintf('SPAY-%04d', $payment->id) }}
                    </div>
                    <div class="text-xs text-gray-500">
                        Date: <span class="font-medium text-gray-800">{{ $payment->payment_date->format('F d, Y') }}</span>
                    </div>
                    @if($payment->isReversed())
                        <div class="mt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 uppercase">
                                REVERSED PAYMENT
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payee & GRN Details -->
            <div class="grid grid-cols-2 gap-6 bg-gray-50/70 p-4 rounded-lg border border-gray-100 text-xs">
                <div>
                    <span class="text-gray-400 font-bold uppercase tracking-wider block mb-1">Paid To (Supplier):</span>
                    <div class="text-sm font-bold text-gray-900">{{ $payment->supplier->name }}</div>
                    @if($payment->supplier->registration_number)
                        <div class="text-gray-500 mt-0.5">Reg No: {{ $payment->supplier->registration_number }}</div>
                    @endif
                    @if($payment->supplier->address)
                        <div class="text-gray-500 mt-0.5">{{ $payment->supplier->address }}</div>
                    @endif
                    @if($payment->supplier->mobile)
                        <div class="text-gray-500 mt-0.5">Tel: {{ $payment->supplier->mobile }}</div>
                    @endif
                </div>

                <div class="text-right sm:text-left">
                    <span class="text-gray-400 font-bold uppercase tracking-wider block mb-1">Against Purchase (GRN):</span>
                    <div class="font-semibold text-gray-900">GRN #: <span class="font-mono text-[#0B1849]">{{ $payment->grn->grn_number }}</span></div>
                    <div class="text-gray-600 mt-0.5">Vendor Invoice: <span class="font-medium">{{ $payment->grn->supplier_invoice_number ?: 'N/A' }}</span></div>
                    <div class="text-gray-600 mt-0.5">Received Date: {{ $payment->grn->received_date->format('M d, Y') }}</div>
                    @if($payment->branch)
                        <div class="text-gray-600 mt-0.5">Branch: {{ $payment->branch->branch_name }}</div>
                    @endif
                </div>
            </div>

            <!-- Payment Amount Highlight -->
            <div class="border border-gray-200 rounded-lg p-5 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-gray-100 pb-3">
                    <span class="text-xs font-bold uppercase text-gray-500 tracking-wider">Amount Paid</span>
                    <div class="text-2xl font-mono font-bold text-gray-900">
                        LKR {{ number_format($payment->amount, 2) }}
                    </div>
                </div>

                <div class="text-xs text-gray-600">
                    <span class="font-semibold text-gray-700">Amount in Words:</span>
                    <span class="italic text-gray-800">
                        {{ \App\Services\NumberToWordsHelper::spell($payment->amount) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 text-xs border-t border-gray-100">
                    <div>
                        <span class="text-gray-500 block">Payment Method</span>
                        <span class="font-semibold text-gray-900 capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Reference / Cheque Number</span>
                        <span class="font-mono font-medium text-gray-900">{{ $payment->reference_number ?: 'None / Cash' }}</span>
                    </div>
                </div>

                @if(!empty($payment->notes))
                    <div class="pt-2 text-xs border-t border-gray-100">
                        <span class="text-gray-500 block">Notes:</span>
                        <span class="text-gray-700">{{ $payment->notes }}</span>
                    </div>
                @endif

                @if($payment->isReversed())
                    <div class="p-3 bg-rose-50 border border-rose-200 rounded text-xs text-rose-800 space-y-1">
                        <div class="font-bold">Reversal Information:</div>
                        <div>Reversed on: {{ $payment->reversed_at?->format('M d, Y H:i') }} by {{ $payment->reversedBy?->name ?? 'Admin' }}</div>
                        <div>Reason: {{ $payment->reversal_reason }}</div>
                    </div>
                @endif
            </div>

            <!-- GRN Financial Statement Summary -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-xs">
                <div class="font-bold text-gray-700 uppercase tracking-wider mb-2">GRN Settlement Summary</div>
                <div class="grid grid-cols-3 gap-2 font-mono">
                    <div>
                        <span class="text-gray-500 block">GRN Total</span>
                        <span class="font-semibold text-gray-900">LKR {{ number_format($payment->grn->total_amount, 2) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Total Paid To Date</span>
                        <span class="font-semibold text-emerald-700">LKR {{ number_format($payment->grn->paid_amount, 2) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Remaining Due</span>
                        <span class="font-bold {{ $payment->grn->outstanding_amount > 0 ? 'text-rose-700' : 'text-gray-700' }}">
                            LKR {{ number_format($payment->grn->outstanding_amount, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Signatures -->
            <div class="grid grid-cols-3 gap-8 pt-10 text-center text-xs">
                <div class="space-y-1">
                    <div class="border-b border-gray-300 pb-12 font-medium text-gray-800">
                        {{ $payment->createdBy?->name ?? 'Authorized Staff' }}
                    </div>
                    <span class="text-gray-500 font-medium">Prepared / Recorded By</span>
                </div>
                <div class="space-y-1">
                    <div class="border-b border-gray-300 pb-12"></div>
                    <span class="text-gray-500 font-medium">Authorized Signature</span>
                </div>
                <div class="space-y-1">
                    <div class="border-b border-gray-300 pb-12"></div>
                    <span class="text-gray-500 font-medium">Payee / Supplier Signature</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-gray-100 pt-3 text-[10px] text-gray-400 text-center">
                System generated payment voucher &bull; {{ config('app.name', 'AutoCare System') }} &bull; Printed {{ now()->format('Y-m-d H:i') }}
            </div>
        </div>
    </div>
</body>
</html>
