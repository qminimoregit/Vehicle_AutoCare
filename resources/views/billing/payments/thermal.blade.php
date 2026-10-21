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
            margin: 0 auto;
            background: #ffffff;
            padding: 12px 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            color: #000000;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: 700; }
        .font-black { font-weight: 900; }
        .uppercase { text-transform: uppercase; }

        .company-header {
            text-align: center;
            margin-bottom: 8px;
        }

        .logo-img {
            max-height: 42px;
            max-width: 160px;
            margin: 0 auto 6px auto;
            display: block;
            object-fit: contain;
            filter: grayscale(100%) contrast(150%);
        }

        .company-name {
            font-size: 14px;
            font-weight: 900;
            letter-spacing: -0.3px;
            line-height: 1.2;
        }

        .branch-name {
            font-size: 11px;
            font-weight: 700;
            margin-top: 2px;
        }

        .company-meta {
            font-size: 10px;
            color: #111111;
            margin-top: 2px;
            line-height: 1.3;
        }

        .divider {
            border-top: 1px dashed #000000;
            margin: 8px 0;
        }

        .receipt-title {
            font-size: 12px;
            font-weight: 900;
            text-align: center;
            letter-spacing: 0.5px;
            padding: 2px 0;
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
            border: 1px solid #000000;
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
            padding: 2px 0;
        }

        .status-badge {
            display: inline-block;
            font-weight: 900;
            font-size: 11px;
            padding: 2px 6px;
            border: 1px solid #000000;
            margin-top: 4px;
            text-transform: uppercase;
        }

        .footer-note {
            text-align: center;
            font-size: 10px;
            margin-top: 10px;
            line-height: 1.4;
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
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 80mm !important;
            }

            .no-print-bar {
                display: none !important;
            }

            .thermal-receipt {
                width: 76mm !important;
                max-width: 76mm !important;
                margin: 0 auto !important;
                padding: 2mm 1mm !important;
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- No-Print Toolbar -->
    <div class="no-print-bar">
        <a href="{{ route('operations.invoices.show', $payment->invoice_id) }}" class="btn btn-secondary">
            &larr; Back to Invoice
        </a>
        <div style="display: flex; gap: 6px;">
            <button onclick="window.print()" class="btn btn-primary">
                Print Bill
            </button>
        </div>
    </div>

    <!-- 80mm Thermal Payment Receipt -->
    <div class="thermal-receipt">
        
        <!-- Header: Company & Branch -->
        <div class="company-header">
            @if(!empty($company->logo) && file_exists(public_path('storage/' . $company->logo)))
                <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="logo-img">
            @endif
            <div class="company-name">{{ $company->company_name ?? 'AutoCare Service Center' }}</div>
            <div class="branch-name">{{ $payment->invoice->branch?->name ?? 'Main Service Branch' }}</div>
            <div class="company-meta">
                {{ $payment->invoice->branch?->address ?? ($company->address ?? 'Colombo, Sri Lanka') }}
            </div>
            <div class="company-meta">
                Tel: {{ $payment->invoice->branch?->phone ?? ($company->phone ?? '011-2345678') }}
            </div>
        </div>

        <div class="divider"></div>

        <div class="receipt-title">
            OFFICIAL PAYMENT RECEIPT
        </div>

        <div class="divider"></div>

        <!-- Meta Information -->
        <div class="meta-row">
            <span class="meta-label">Receipt No:</span>
            <span class="meta-val">{{ $payment->payment_number }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Invoice No:</span>
            <span class="meta-val">{{ $payment->invoice->invoice_number }}</span>
        </div>

        @if($payment->invoice->jobCard)
            <div class="meta-row">
                <span class="meta-label">Job Card No:</span>
                <span class="meta-val">{{ $payment->invoice->jobCard->job_number }}</span>
            </div>
        @endif

        <div class="meta-row">
            <span class="meta-label">Date & Time:</span>
            <span class="meta-val">{{ $payment->created_at ? $payment->created_at->format('Y-m-d H:i') : now()->format('Y-m-d H:i') }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Cashier:</span>
            <span class="meta-val">{{ $payment->creator?->name ?? 'Cashier' }}</span>
        </div>

        <div class="divider"></div>

        <!-- Customer & Vehicle -->
        @php
            $custName = $payment->customer?->customer_name ?? ($payment->invoice->customer?->customer_name ?? ($payment->invoice->customer_snapshot['customer_name'] ?? 'Walk-in Customer'));
            $custPhone = $payment->customer?->phone ?? ($payment->invoice->customer?->phone ?? ($payment->invoice->customer_snapshot['phone'] ?? null));
            $vehNumber = $payment->invoice->jobCard?->vehicle?->vehicle_number ?? ($payment->invoice->customer_snapshot['vehicle_number'] ?? null);
        @endphp

        <div class="meta-row">
            <span class="meta-label">Received From:</span>
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
                <span class="meta-label">Vehicle No:</span>
                <span class="meta-val uppercase font-black">{{ $vehNumber }}</span>
            </div>
        @endif

        <!-- Payment Highlight Box -->
        <div class="payment-box">
            <div style="font-size: 10px; font-weight: 700; text-transform: uppercase;">Amount Received</div>
            <div class="payment-amount">LKR {{ number_format($payment->amount, 2) }}</div>
            <div style="font-size: 10px; font-weight: 600;">
                Method: {{ strtoupper(str_replace('_', ' ', $payment->payment_method)) }}
                @if(!empty($payment->reference_number))
                    • Ref: {{ $payment->reference_number }}
                @endif
            </div>
            @if(!empty($payment->notes))
                <div style="font-size: 9.5px; color: #444; margin-top: 2px;">{{ $payment->notes }}</div>
            @endif
        </div>

        <!-- Invoice Balance Summary -->
        <table class="totals-table">
            <tr>
                <td>Invoice Total:</td>
                <td class="text-right font-bold">LKR {{ number_format($payment->invoice->grand_total, 2) }}</td>
            </tr>
            <tr>
                <td>Total Paid to Date:</td>
                <td class="text-right font-bold">LKR {{ number_format($payment->invoice->amount_paid, 2) }}</td>
            </tr>
            <tr style="border-top: 1px dashed #000;">
                <td class="font-black" style="padding-top: 3px;">REMAINING BALANCE:</td>
                <td class="text-right font-black" style="padding-top: 3px;">LKR {{ number_format($payment->invoice->balance_amount, 2) }}</td>
            </tr>
        </table>

        <!-- Status Badge -->
        <div class="text-center" style="margin-top: 6px;">
            @php
                $statusLabel = 'PARTIALLY PAID';
                if ($payment->invoice->status === 'paid' || (float)$payment->invoice->balance_amount <= 0.0001) {
                    $statusLabel = 'PAID IN FULL';
                }
            @endphp
            <div class="status-badge">{{ $statusLabel }}</div>
        </div>

        <div class="divider"></div>

        <!-- Thank you & Footer -->
        <div class="footer-note">
            <div class="font-bold">Thank you for your payment!</div>
            <div>This document serves as your official payment acknowledgment.</div>
        </div>

        <div class="barcode-area">
            *{{ $payment->payment_number }}*
        </div>
    </div>

</body>
</html>
