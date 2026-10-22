<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AdvanceReceipt_{{ $jobCard->job_number }}</title>
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
            letter-spacing: -0.2px;
            text-transform: uppercase;
        }

        .branch-name {
            font-size: 11.5px;
            font-weight: 700;
            margin-top: 1px;
        }

        .company-meta {
            font-size: 10px;
            color: #333333;
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
            font-size: 11px;
            font-weight: 900;
            text-align: center;
            letter-spacing: 0.3px;
            padding: 2px 0;
            text-transform: uppercase;
        }

        .receipt-subtitle {
            font-size: 9.5px;
            font-weight: 700;
            text-align: center;
            color: #333333;
            margin-top: 2px;
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

        /* Items / Payments Table */
        .payments-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            margin: 6px 0;
        }

        .payments-table th {
            text-align: left;
            border-bottom: 1px dashed #000000;
            padding: 3px 0;
            font-weight: 800;
            font-size: 10px;
            text-transform: uppercase;
        }

        .payments-table td {
            padding: 3px 0;
            vertical-align: top;
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
            font-size: 12px;
            font-weight: 900;
            padding: 4px 0;
            border-top: 1px solid #000000;
            border-bottom: 1px solid #000000;
        }

        .status-badge {
            display: inline-block;
            font-weight: 900;
            font-size: 10.5px;
            padding: 2px 6px;
            border: 1px solid #000000;
            margin-top: 4px;
            text-transform: uppercase;
        }

        .footer-note {
            text-align: center;
            font-size: 9.5px;
            margin-top: 10px;
            line-height: 1.4;
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
        <a href="{{ route('operations.job-cards.show', $jobCard) }}" class="btn btn-secondary">
            &larr; Back to Job
        </a>
        <div style="display: flex; gap: 6px;">
            <button onclick="handleAdvancePrint()" class="btn btn-primary" id="btn-print-advance">
                Print Bill
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
            <div class="branch-name">{{ $jobCard->branch?->name ?? 'Main Service Branch' }}</div>
            <div class="company-meta">
                {{ $jobCard->branch?->address ?? ($company->address ?? 'Colombo, Sri Lanka') }}
            </div>
            <div class="company-meta">
                Tel: {{ $jobCard->branch?->phone ?? ($company->phone ?? '011-2345678') }}
                @if(!empty($company->email)) | {{ $company->email }} @endif
            </div>
        </div>

        <div class="divider"></div>

        <div class="receipt-title">
            Advance Payment Receipt
        </div>
        <div class="receipt-subtitle">
            Advance Payment Receipt – Final Invoice Pending
        </div>

        <div class="divider"></div>

        <!-- Meta Information -->
        @php
            $latestPayment = $jobCard->payments->last();
        @endphp
        @if($latestPayment)
            <div class="meta-row">
                <span class="meta-label">Receipt No:</span>
                <span class="meta-val">{{ $latestPayment->payment_number }}</span>
            </div>
        @endif

        <div class="meta-row">
            <span class="meta-label">Job Card No:</span>
            <span class="meta-val">{{ $jobCard->job_number }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Date & Time:</span>
            <span class="meta-val">{{ $latestPayment?->created_at ? $latestPayment->created_at->format('Y-m-d H:i') : now()->format('Y-m-d H:i') }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Received By:</span>
            <span class="meta-val">{{ $latestPayment?->creator?->name ?? Auth::user()?->name ?? 'Staff' }}</span>
        </div>

        <!-- Customer & Vehicle -->
        <div class="divider"></div>

        <div class="meta-row">
            <span class="meta-label">Customer:</span>
            <span class="meta-val">{{ $jobCard->customer?->customer_name ?? 'Walk-in Customer' }}</span>
        </div>

        @if($jobCard->customer?->phone)
            <div class="meta-row">
                <span class="meta-label">Phone:</span>
                <span class="meta-val">{{ $jobCard->customer->phone }}</span>
            </div>
        @endif

        @if($jobCard->vehicle)
            <div class="meta-row">
                <span class="meta-label">Vehicle No:</span>
                <span class="meta-val uppercase font-black">{{ $jobCard->vehicle->vehicle_number }}</span>
            </div>
            @if($jobCard->vehicleType)
                <div class="meta-row">
                    <span class="meta-label">Vehicle Type:</span>
                    <span class="meta-val">{{ $jobCard->vehicleType->name }}</span>
                </div>
            @endif
        @endif

        <div class="divider"></div>

        <!-- Recorded Advance Payments -->
        <div class="font-bold text-left uppercase" style="font-size: 10px; margin-bottom: 4px;">Advance Collections:</div>
        <table class="payments-table">
            <thead>
                <tr>
                    <th style="width: 30%;">DATE</th>
                    <th style="width: 35%;">METHOD</th>
                    <th style="width: 35%; text-align: right;">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jobCard->payments as $p)
                    <tr>
                        <td>{{ $p->payment_date ? $p->payment_date->format('Y-m-d') : '' }}</td>
                        <td class="uppercase">{{ ucfirst(str_replace('_', ' ', $p->payment_method)) }}</td>
                        <td class="text-right font-bold">{{ number_format($p->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- Summary Totals -->
        <table class="totals-table">
            <tr class="grand-total-row">
                <td>TOTAL ADVANCE RECEIVED:</td>
                <td class="text-right">LKR {{ number_format($totalAdvance, 2) }}</td>
            </tr>
            @if($estimatedTotal > 0)
                <tr>
                    <td style="padding-top: 4px;">Current Estimated Total:</td>
                    <td class="text-right font-bold" style="padding-top: 4px;">LKR {{ number_format($estimatedTotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="font-bold">Estimated Outstanding:</td>
                    <td class="text-right font-bold">LKR {{ number_format($estimatedBalance, 2) }}</td>
                </tr>
            @endif
        </table>

        <!-- Status Notice -->
        <div class="text-center" style="margin-top: 8px;">
            <div class="status-badge">ADVANCE DEPOSIT - FINAL INVOICE PENDING</div>
        </div>

        <div class="divider"></div>

        <!-- Important Notice -->
        <div class="footer-note">
            <div class="font-bold">Notice:</div>
            <div>This receipt acknowledges customer advance deposit funds. It is not an official tax invoice. Final billing will be issued upon service completion.</div>
            <div style="margin-top: 6px; font-weight: 700;">Thank you for trusting AutoCare!</div>
        </div>

    </div>

    <!-- Universal QZ Tray Printer Support -->
    <script src="{{ asset('vendor/qz/qz-tray.js') }}"></script>
    <script src="{{ asset('js/autocare-printer.js') }}"></script>
    <script>
        function handleAdvancePrint() {
            if (window.AutoCarePrinter && window.AutoCarePrinter.isConnected) {
                window.AutoCarePrinter.printDocument({
                    type: 'advance_receipt',
                    html: document.querySelector('.thermal-receipt').outerHTML,
                    title: 'AdvanceReceipt_{{ $jobCard->job_number }}',
                    documentId: {{ $jobCard->id }},
                    documentNumber: '{{ $jobCard->job_number }}',
                    fallbackUrl: window.location.href
                });
            } else {
                window.print();
            }
        }
    </script>
</body>
</html>
