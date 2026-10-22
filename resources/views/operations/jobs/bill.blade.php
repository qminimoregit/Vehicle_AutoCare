<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SalesBill_{{ $jobCard->job_number }}</title>
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

        .double-divider {
            border-top: 1px double #000000;
            border-bottom: 1px double #000000;
            height: 3px;
            margin: 6px 0;
        }

        .receipt-title {
            font-size: 13px;
            font-weight: 900;
            text-align: center;
            letter-spacing: 1px;
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

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin: 4px 0;
        }

        .items-table th {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 4px 1px;
            font-size: 9.5px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 3px 1px;
            vertical-align: top;
        }

        .col-qty {
            width: 12%;
            text-align: left;
        }
        .col-desc {
            width: 58%;
            text-align: left;
            word-break: break-word;
        }
        .col-total {
            width: 30%;
            text-align: right;
            white-space: nowrap;
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

        .status-container {
            text-align: center;
            margin: 8px 0;
        }

        .status-badge {
            display: inline-block;
            font-weight: 900;
            font-size: 11px;
            padding: 3px 8px;
            border: 1.5px solid #000000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .signature-section {
            margin-top: 24px;
            text-align: center;
        }

        .signature-line {
            width: 50mm;
            margin: 0 auto 4px auto;
            border-top: 1px dotted #000000;
        }

        .signature-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .footer-note {
            text-align: center;
            font-size: 10px;
            margin-top: 10px;
            line-height: 1.35;
            font-weight: 700;
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

            tr, .meta-row, .signature-section, .company-header, .footer-note, .status-container {
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
        <a href="{{ route('operations.job-cards.show', $jobCard) }}" class="btn btn-secondary">
            &larr; Back to Job Card
        </a>
        <div style="display: flex; gap: 6px;">
            <button onclick="handleBillPrint()" class="btn btn-primary" id="btn-print-bill">
                Print Sales Bill
            </button>
        </div>
    </div>

    <!-- 80mm Thermal Receipt -->
    <div class="thermal-receipt">
        
        <!-- Header: Large Company Logo & Branch Details -->
        <x-thermal-header :company="$company" :branch="$jobCard->branch" />

        <div class="divider"></div>

        <!-- Document Heading: SALES BILL -->
        <div class="receipt-title">
            SALES BILL
        </div>

        <div class="divider"></div>

        <!-- Bill Metadata -->
        <div class="meta-row">
            <span class="meta-label">Bill Number:</span>
            <span class="meta-val font-black">BILL-{{ $jobCard->job_number }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Date & Time:</span>
            <span class="meta-val">{{ now()->format('d/m/Y h:i A') }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Cashier:</span>
            <span class="meta-val">{{ Auth::user()?->name ?? ($jobCard->creator?->name ?? 'Cashier') }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Vehicle Number:</span>
            <span class="meta-val font-black uppercase">{{ $jobCard->vehicle?->vehicle_number ?? '—' }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Customer:</span>
            <span class="meta-val">{{ $jobCard->customer?->customer_name ?? 'Walk-in Customer' }}</span>
        </div>

        <!-- Items Table: QTY DESCRIPTION TOTAL -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-qty">QTY</th>
                    <th class="col-desc">DESCRIPTION</th>
                    <th class="col-total">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jobCard->services as $svc)
                    <tr>
                        <td class="col-qty">{{ (int) $svc->quantity == $svc->quantity ? (int) $svc->quantity : $svc->quantity }}</td>
                        <td class="col-desc">
                            <span class="font-bold">{{ $svc->service_name }}</span>
                        </td>
                        <td class="col-total">{{ number_format($svc->line_total, 2) }}</td>
                    </tr>
                @endforeach

                @foreach($jobCard->products as $prod)
                    <tr>
                        <td class="col-qty">{{ (int) $prod->quantity == $prod->quantity ? (int) $prod->quantity : $prod->quantity }}</td>
                        <td class="col-desc">
                            <span>{{ $prod->product_name }}</span>
                        </td>
                        <td class="col-total">{{ number_format($prod->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- Totals Summary -->
        <table class="totals-table">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right font-bold">LKR {{ number_format($subtotal, 2) }}</td>
            </tr>
            @if($discount > 0)
                <tr>
                    <td>Discount:</td>
                    <td class="text-right font-bold">- LKR {{ number_format($discount, 2) }}</td>
                </tr>
            @endif
            @if($tax > 0)
                <tr>
                    <td>Tax:</td>
                    <td class="text-right font-bold">LKR {{ number_format($tax, 2) }}</td>
                </tr>
            @endif
            <tr style="border-top: 1px dashed #000;">
                <td class="font-black" style="padding-top: 3px; font-size: 11px;">TOTAL:</td>
                <td class="text-right font-black" style="padding-top: 3px; font-size: 11px;">LKR {{ number_format($grandTotal, 2) }}</td>
            </tr>
            <tr>
                <td class="font-bold">Paid:</td>
                <td class="text-right font-bold">LKR {{ number_format($totalPaid, 2) }}</td>
            </tr>
            <tr style="border-top: 1px solid #000;">
                <td class="font-black" style="padding-top: 2px;">BALANCE:</td>
                <td class="text-right font-black" style="padding-top: 2px;">LKR {{ number_format($balance, 2) }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- Payment Status Badge -->
        <div class="status-container">
            <span style="font-size: 9.5px; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 2px;">Payment Status</span>
            <div class="status-badge">
                {{ $paymentStatus }}
            </div>
        </div>

        <!-- Authorized Signature -->
        <div class="signature-section">
            <div class="signature-line"></div>
            <div class="signature-label">Authorized Signature</div>
        </div>

        <div class="divider" style="margin-top: 16px;"></div>

        <!-- Footer -->
        <div class="footer-note">
            Thank you for choosing Viran Auto Care!
        </div>

        <div class="barcode-area">
            *BILL-{{ $jobCard->job_number }}*
        </div>
    </div>

    <!-- Universal QZ Tray Printer Support -->
    <script src="{{ asset('vendor/qz/qz-tray.js') }}"></script>
    <script src="{{ asset('js/autocare-printer.js') }}"></script>
    <script>
        function handleBillPrint() {
            if (window.AutoCarePrinter && window.AutoCarePrinter.isConnected) {
                window.AutoCarePrinter.printDocument({
                    type: 'bill',
                    html: document.querySelector('.thermal-receipt').outerHTML,
                    title: 'SalesBill_{{ $jobCard->job_number }}',
                    documentId: {{ $jobCard->id }},
                    documentNumber: 'BILL-{{ $jobCard->job_number }}',
                    fallbackUrl: window.location.href
                });
            } else {
                window.print();
            }
        }
    </script>
</body>
</html>
