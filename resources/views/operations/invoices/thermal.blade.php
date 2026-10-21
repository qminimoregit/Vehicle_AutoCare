<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt_{{ $invoice->invoice_number }}</title>
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

        .double-divider {
            border-top: 2px solid #000000;
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

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            margin: 6px 0;
        }

        .items-table th {
            text-align: left;
            border-bottom: 1px dashed #000000;
            padding: 3px 0;
            font-weight: 800;
            font-size: 10px;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .col-qty {
            width: 14%;
            text-align: left;
            font-weight: 700;
        }

        .col-desc {
            width: 54%;
            word-break: break-word;
            padding-right: 4px;
        }

        .col-amt {
            width: 32%;
            text-align: right;
            font-weight: 700;
            white-space: nowrap;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 4px;
        }

        .totals-table td {
            padding: 2px 0;
        }

        .totals-table .grand-total-row td {
            font-size: 13px;
            font-weight: 900;
            padding: 4px 0;
            border-top: 1px solid #000000;
            border-bottom: 1px solid #000000;
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
        <a href="{{ route('operations.invoices.show', $invoice) }}" class="btn btn-secondary">
            &larr; Back
        </a>
        <div style="display: flex; gap: 6px;">
            <button onclick="window.print()" class="btn btn-primary">
                Print 80mm
            </button>
        </div>
    </div>

    <!-- 80mm Thermal Receipt -->
    <div class="thermal-receipt">
        
        <!-- Header: Company & Branch -->
        <div class="company-header">
            @if(!empty($company->logo) && file_exists(public_path('storage/' . $company->logo)))
                <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="logo-img">
            @endif
            <div class="company-name">{{ $company->company_name ?? 'AutoCare Service Center' }}</div>
            <div class="branch-name">{{ $invoice->branch?->name ?? 'Main Service Branch' }}</div>
            <div class="company-meta">
                {{ $invoice->branch?->address ?? ($company->address ?? 'Colombo, Sri Lanka') }}
            </div>
            <div class="company-meta">
                Tel: {{ $invoice->branch?->phone ?? ($company->phone ?? '011-2345678') }}
                @if(!empty($company->email)) | {{ $company->email }} @endif
            </div>
        </div>

        <div class="divider"></div>

        <div class="receipt-title">
            {{ $invoice->invoice_type === 'direct_sale' ? 'SALES RECEIPT' : 'INVOICE / RECEIPT' }}
        </div>

        <div class="divider"></div>

        <!-- Meta Information -->
        <div class="meta-row">
            <span class="meta-label">Invoice No:</span>
            <span class="meta-val">{{ $invoice->invoice_number }}</span>
        </div>

        @if($invoice->reference)
            <div class="meta-row">
                <span class="meta-label">Ref / Sale No:</span>
                <span class="meta-val">{{ $invoice->reference }}</span>
            </div>
        @endif

        @if($invoice->jobCard)
            <div class="meta-row">
                <span class="meta-label">Job Card No:</span>
                <span class="meta-val">{{ $invoice->jobCard->job_number }}</span>
            </div>
        @endif

        <div class="meta-row">
            <span class="meta-label">Date & Time:</span>
            <span class="meta-val">{{ $invoice->created_at ? $invoice->created_at->format('Y-m-d H:i') : now()->format('Y-m-d H:i') }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Cashier:</span>
            <span class="meta-val">{{ $invoice->creator?->name ?? Auth::user()?->name ?? 'Staff' }}</span>
        </div>

        <!-- Customer & Vehicle -->
        <div class="divider"></div>

        <div class="meta-row">
            <span class="meta-label">Customer:</span>
            <span class="meta-val">{{ $invoice->customer?->customer_name ?? ($invoice->customer_snapshot['customer_name'] ?? 'Walk-in Customer') }}</span>
        </div>

        @php
            $custPhone = $invoice->customer?->phone ?? ($invoice->customer_snapshot['phone'] ?? null);
            $vehNumber = $invoice->jobCard?->vehicle?->vehicle_number ?? ($invoice->customer_snapshot['vehicle_number'] ?? null);
        @endphp

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

        <div class="divider"></div>

        <!-- Line Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-qty">QTY</th>
                    <th class="col-desc">DESCRIPTION</th>
                    <th class="col-amt">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td class="col-qty">{{ (float)$item->quantity == (int)$item->quantity ? (int)$item->quantity : number_format($item->quantity, 2) }}</td>
                        <td class="col-desc">
                            {{ $item->item_name }}
                            @if(!empty($item->item_code) && $item->item_code !== 'SERVICE')
                                <div style="font-size: 9px; color: #444;">{{ $item->item_code }}</div>
                            @endif
                        </td>
                        <td class="col-amt">{{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- Totals & Balances -->
        <table class="totals-table">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right font-bold">{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if((float)$invoice->discount_amount > 0)
                <tr>
                    <td>Discount:</td>
                    <td class="text-right font-bold">-{{ number_format($invoice->discount_amount, 2) }}</td>
                </tr>
            @endif
            @if((float)$invoice->tax_amount > 0)
                <tr>
                    <td>Tax:</td>
                    <td class="text-right font-bold">+{{ number_format($invoice->tax_amount, 2) }}</td>
                </tr>
            @endif
            <tr class="grand-total-row">
                <td>TOTAL:</td>
                <td class="text-right">LKR {{ number_format($invoice->grand_total, 2) }}</td>
            </tr>
            <tr>
                <td style="padding-top: 4px;">Paid:</td>
                <td class="text-right font-bold" style="padding-top: 4px;">{{ number_format($invoice->amount_paid, 2) }}</td>
            </tr>
            <tr>
                <td class="font-bold">BALANCE:</td>
                <td class="text-right font-bold">LKR {{ number_format($invoice->balance_amount, 2) }}</td>
            </tr>
        </table>

        <!-- Status & Payment Method -->
        <div class="text-center" style="margin-top: 6px;">
            @php
                $statusLabel = 'PARTIALLY PAID';
                if ($invoice->status === 'paid' || (float)$invoice->balance_amount <= 0.0001) {
                    $statusLabel = 'PAID IN FULL';
                } elseif ($invoice->status === 'cancelled') {
                    $statusLabel = 'CANCELLED';
                } elseif ($invoice->amount_paid <= 0) {
                    $statusLabel = 'UNPAID / ISSUED';
                }
            @endphp
            <div class="status-badge">{{ $statusLabel }}</div>
            @if(!empty($invoice->payment_method))
                <div style="font-size: 10px; margin-top: 3px; font-weight: 700;">
                    Payment: {{ strtoupper(str_replace('_', ' ', $invoice->payment_method)) }}
                </div>
            @endif
        </div>

        <div class="divider"></div>

        <!-- Thank you & Footer -->
        <div class="footer-note">
            <div class="font-bold">Thank you for choosing us!</div>
            <div>Please retain this bill for your warranty & service records.</div>
        </div>

        <div class="barcode-area">
            *{{ $invoice->invoice_number }}*
        </div>
    </div>

</body>
</html>
