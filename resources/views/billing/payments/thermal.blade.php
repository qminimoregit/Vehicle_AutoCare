<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PaymentReceipt_{{ $payment->payment_number }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Courier New", Courier, monospace;
        }

        body {
            background-color: #f3f4f6;
            color: #000000;
            padding: 20px 10px;
            font-size: 11px;
            line-height: 1.35;
        }

        .no-print-bar {
            width: 76mm;
            max-width: 100%;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.15s ease;
        }

        .btn-primary {
            background-color: #0B1849;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #15286e;
        }

        .btn-secondary {
            background-color: #ffffff;
            color: #374151;
            border: 1px solid #d1d5db;
        }
        .btn-secondary:hover {
            background-color: #f9fafb;
        }

        /* 80mm Thermal Receipt Sheet */
        .thermal-receipt {
            width: 76mm;
            max-width: 76mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 12px 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            color: #000000;
            position: relative;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: 700; }
        .font-black { font-weight: 900; }
        .uppercase { text-transform: uppercase; }

        .company-header {
            text-align: center;
            margin-bottom: 6px;
        }

        /* 60–65mm centered logo for 80mm thermal paper */
        .logo-img {
            width: 62mm;
            max-width: 65mm;
            height: auto;
            margin: 0 auto 6px auto;
            display: block;
            object-fit: contain;
            filter: grayscale(100%) contrast(150%);
            -webkit-filter: grayscale(100%) contrast(150%);
        }

        .company-name {
            font-size: 13.5px;
            font-weight: 900;
            letter-spacing: 0.2px;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .branch-name {
            font-size: 11.5px;
            font-weight: 800;
            letter-spacing: 0.5px;
            margin-top: 2px;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .branch-meta {
            font-size: 10px;
            color: #111111;
            margin-top: 3px;
            line-height: 1.3;
        }

        .divider {
            border-top: 1px dashed #000000;
            margin: 6px 0;
        }

        .receipt-title {
            font-size: 13px;
            font-weight: 900;
            text-align: center;
            letter-spacing: 0.5px;
            padding: 3px 0;
            text-transform: uppercase;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 10.5px;
            margin-bottom: 2px;
        }

        .meta-label {
            font-weight: 600;
            color: #222;
        }

        .meta-val {
            font-weight: 700;
            text-align: right;
        }

        .payment-box {
            border: 1.5px solid #000000;
            padding: 8px 10px;
            margin: 8px 0;
            text-align: center;
        }

        .payment-amount {
            font-size: 16px;
            font-weight: 900;
            margin: 3px 0;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            margin-top: 4px;
        }

        .totals-table td {
            padding: 2px 1px;
        }

        .status-badge {
            display: inline-block;
            font-weight: 900;
            font-size: 11px;
            padding: 3px 8px;
            border: 1.5px solid #000000;
            margin-top: 4px;
            text-transform: uppercase;
        }

        .footer-note {
            text-align: center;
            font-size: 10px;
            margin-top: 10px;
            line-height: 1.35;
        }

        .barcode-area {
            text-align: center;
            margin-top: 8px;
            font-family: monospace;
            font-size: 9px;
            letter-spacing: 2px;
        }

        /* PRINT MEDIA STYLES - 80MM ROLL PAPER */
        @page {
            size: 80mm auto;
            margin: 0;
        }

        @media print {
            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 80mm !important;
                max-width: 80mm !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print-bar {
                display: none !important;
            }

            .thermal-receipt {
                width: 72mm !important;
                max-width: 76mm !important;
                margin: 0 auto !important;
                padding: 2mm 1mm !important;
                box-shadow: none !important;
                border: none !important;
                page-break-inside: auto !important;
            }

            tr, .meta-row, .payment-box, .company-header, .footer-note {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .logo-img {
                width: 62mm !important;
                max-width: 65mm !important;
                height: auto !important;
                margin: 0 auto 2mm auto !important;
                display: block !important;
                filter: grayscale(100%) contrast(150%) !important;
                -webkit-filter: grayscale(100%) contrast(150%) !important;
            }

            .company-name {
                font-size: 13.5px !important;
                font-weight: 900 !important;
                letter-spacing: 0.2px !important;
                line-height: 1.2 !important;
            }

            .branch-name {
                font-size: 11.5px !important;
                font-weight: 800 !important;
                letter-spacing: 0.5px !important;
                margin-top: 1.5px !important;
                line-height: 1.2 !important;
            }

            .branch-meta {
                font-size: 10px !important;
                color: #000000 !important;
                margin-top: 2.5px !important;
                line-height: 1.25 !important;
            }
        }
    </style>
</head>
<body>

    <!-- No-Print Toolbar -->
    <div class="no-print-bar">
        @if($payment->job_card_id)
            <a href="{{ route('operations.job-cards.show', $payment->job_card_id) }}" class="btn btn-secondary">
                &larr; Back to Job Card
            </a>
        @elseif($payment->invoice_id)
            <a href="{{ route('operations.invoices.show', $payment->invoice_id) }}" class="btn btn-secondary">
                &larr; Back to Invoice
            </a>
        @else
            <a href="{{ route('billing.payments.index') }}" class="btn btn-secondary">
                &larr; Back to Payments
            </a>
        @endif

        <div style="display: flex; gap: 6px;">
            <button onclick="handlePaymentPrint()" class="btn btn-primary" id="btn-print-payment">
                Print Payment Receipt
            </button>
        </div>
    </div>

    <!-- 80mm Thermal Payment Receipt -->
    <div class="thermal-receipt">
        
        @php
            $logoUrl = $company->getLogoUrl();
            $branch = $payment->jobCard?->branch ?? ($payment->invoice?->branch ?? $company->mainBranch);
            $branchName = $branch ? $branch->name : 'Mattegoda';

            $custName = $payment->customer?->customer_name 
                ?? ($payment->jobCard?->customer?->customer_name 
                ?? ($payment->invoice?->customer?->customer_name 
                ?? ($payment->invoice?->customer_snapshot['customer_name'] ?? 'Walk-in Customer')));

            $custPhone = $payment->customer?->phone 
                ?? ($payment->jobCard?->customer?->phone 
                ?? ($payment->invoice?->customer?->phone 
                ?? ($payment->invoice?->customer_snapshot['phone'] ?? null)));

            $vehNumber = $payment->jobCard?->vehicle?->vehicle_number 
                ?? ($payment->invoice?->jobCard?->vehicle?->vehicle_number 
                ?? ($payment->invoice?->customer_snapshot['vehicle_number'] ?? null));

            $jobCardNumber = $payment->jobCard?->job_number 
                ?? ($payment->invoice?->jobCard?->job_number ?? null);

            $paymentDateStr = $payment->payment_date 
                ? \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') 
                : ($payment->created_at ? $payment->created_at->setTimezone('Asia/Colombo')->format('d/m/Y') : \Carbon\Carbon::now('Asia/Colombo')->format('d/m/Y'));
        @endphp

        <!-- Header: Company & Branch -->
        <x-thermal-header :company="$company" :branch="$branch" />

        <div class="divider"></div>

        <!-- Heading: PAYMENT RECEIPT -->
        <div class="receipt-title">
            PAYMENT RECEIPT
        </div>

        <div class="divider"></div>

        <!-- Meta Information -->
        <div class="meta-row">
            <span class="meta-label">Receipt Reference:</span>
            <span class="meta-val font-black">{{ $payment->payment_number }}</span>
        </div>

        @if($jobCardNumber)
            <div class="meta-row">
                <span class="meta-label">Job Card Number:</span>
                <span class="meta-val font-black">{{ $jobCardNumber }}</span>
            </div>
        @endif

        @if($payment->invoice_id && $payment->invoice)
            <div class="meta-row">
                <span class="meta-label">Invoice Number:</span>
                <span class="meta-val">{{ $payment->invoice->invoice_number }}</span>
            </div>
        @endif

        <div class="meta-row">
            <span class="meta-label">Payment Date:</span>
            <span class="meta-val">{{ $paymentDateStr }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Cashier:</span>
            <span class="meta-val">{{ $payment->creator?->name ?? 'Cashier' }}</span>
        </div>

        <div class="divider"></div>

        <!-- Customer & Vehicle -->
        <div class="meta-row">
            <span class="meta-label">Customer Name:</span>
            <span class="meta-val font-bold">{{ $custName }}</span>
        </div>

        @if($custPhone)
            <div class="meta-row">
                <span class="meta-label">Phone:</span>
                <span class="meta-val">{{ $custPhone }}</span>
            </div>
        @endif

        @if($vehNumber)
            <div class="meta-row">
                <span class="meta-label">Vehicle Number:</span>
                <span class="meta-val uppercase font-black">{{ $vehNumber }}</span>
            </div>
        @endif

        <!-- Payment Highlight Box -->
        <div class="payment-box">
            <div style="font-size: 10px; font-weight: 700; text-transform: uppercase;">Amount Received</div>
            <div class="payment-amount">LKR {{ number_format($payment->amount, 2) }}</div>
            <div style="font-size: 10px; font-weight: 700;">
                Payment Method: {{ strtoupper(str_replace('_', ' ', $payment->payment_method)) }}
            </div>
            @if(!empty($payment->reference_number))
                <div style="font-size: 9.5px; font-weight: 600; margin-top: 2px;">
                    Ref: {{ $payment->reference_number }}
                </div>
            @endif
            @if(!empty($payment->notes))
                <div style="font-size: 9.5px; color: #333; margin-top: 2px;">{{ $payment->notes }}</div>
            @endif
        </div>

        <!-- Balances Summary -->
        <table class="totals-table">
            <tr>
                <td>Job Total:</td>
                <td class="text-right font-bold">LKR {{ number_format($jobTotal, 2) }}</td>
            </tr>
            <tr>
                <td>Total Paid to Date:</td>
                <td class="text-right font-bold">LKR {{ number_format($totalPaidToDate, 2) }}</td>
            </tr>
            <tr style="border-top: 1px dashed #000;">
                <td class="font-black" style="padding-top: 3px;">REMAINING BALANCE:</td>
                <td class="text-right font-black" style="padding-top: 3px;">LKR {{ number_format($remainingBalance, 2) }}</td>
            </tr>
        </table>

        <!-- Status Badge -->
        <div class="text-center" style="margin-top: 6px;">
            @php
                $statusLabel = ($remainingBalance <= 0.0001) ? 'PAID IN FULL' : 'PARTIALLY PAID';
            @endphp
            <div class="status-badge">{{ $statusLabel }}</div>
        </div>

        <div class="divider"></div>

        <!-- Thank you & Footer -->
        <div class="footer-note">
            <div class="font-bold">Thank you for your payment!</div>
            <div>Official acknowledgment of payment received.</div>
        </div>

        <div class="barcode-area">
            *{{ $payment->payment_number }}*
        </div>
    </div>

    <!-- Universal QZ Tray Printer Support -->
    <script src="{{ asset('vendor/qz/qz-tray.js') }}"></script>
    <script src="{{ asset('js/autocare-printer.js') }}"></script>
    <script>
        function handlePaymentPrint() {
            if (window.AutoCarePrinter && window.AutoCarePrinter.isConnected) {
                window.AutoCarePrinter.printDocument({
                    type: 'payment_receipt',
                    html: document.querySelector('.thermal-receipt').outerHTML,
                    title: 'Receipt_{{ $payment->payment_number }}',
                    documentId: {{ $payment->id }},
                    documentNumber: '{{ $payment->payment_number }}',
                    fallbackUrl: window.location.href
                });
            } else {
                window.print();
            }
        }
    </script>
</body>
</html>
