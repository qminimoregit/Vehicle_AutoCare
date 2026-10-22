<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobCard_{{ $jobCard->job_number }}{{ $isSelective ? '_Selected' : '' }}</title>
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

        .logo-img {
            max-height: 52px;
            max-width: 160px;
            margin: 0 auto 4px auto;
            display: block;
            object-fit: contain;
        }

        .company-name {
            font-size: 13px;
            font-weight: 900;
            letter-spacing: -0.2px;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .branch-meta {
            font-size: 10px;
            color: #111111;
            margin-top: 2px;
            line-height: 1.25;
        }

        .divider {
            border-top: 1px dashed #000000;
            margin: 7px 0;
        }

        .receipt-title {
            font-size: 13px;
            font-weight: 900;
            text-align: center;
            letter-spacing: 0.5px;
            padding: 2px 0;
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
            color: #111;
            width: 38%;
        }

        .meta-val {
            font-weight: 700;
            text-align: right;
            width: 62%;
            word-break: break-word;
        }

        .meta-row-note {
            font-size: 10px;
            margin-top: 3px;
            line-height: 1.3;
        }

        .section-header {
            font-size: 10.5px;
            font-weight: 900;
            text-transform: uppercase;
            padding: 4px 0 2px 0;
            border-bottom: 1px dashed #000000;
            margin-top: 6px;
            letter-spacing: 0.3px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            margin: 4px 0 6px 0;
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

        .col-desc {
            width: 78%;
            word-break: break-word;
            padding-right: 4px;
        }

        .col-qty {
            width: 22%;
            text-align: right;
            font-weight: 700;
            white-space: nowrap;
        }

        .col-action {
            width: 45%;
            word-break: break-word;
            padding-left: 4px;
        }

        .text-sub {
            font-size: 9px;
            color: #333333;
            margin-top: 1px;
        }

        .badge-sub {
            display: inline-block;
            font-size: 8.5px;
            font-weight: 700;
            padding: 1px 4px;
            border: 1px solid #444;
            margin-top: 2px;
            text-transform: uppercase;
        }

        .signatures-area {
            margin-top: 18px;
            padding-top: 8px;
            display: flex;
            justify-content: space-between;
            font-size: 9.5px;
            text-align: center;
        }

        .sig-block {
            width: 46%;
            border-top: 1px solid #000000;
            padding-top: 3px;
            font-weight: 700;
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
            &larr; Back to Job Card
        </a>
        <div style="display: flex; gap: 6px;">
            <a 
                href="{{ route('operations.job-cards.pdf', array_merge(['jobCard' => $jobCard->id], request()->all())) }}" 
                class="btn btn-secondary"
                title="Download standard A4 PDF"
            >
                A4 PDF
            </a>
            <button onclick="handleJobPrint()" class="btn btn-primary" id="btn-print-job">
                Print 80mm
            </button>
        </div>
    </div>

    <!-- 80mm Thermal Job Card -->
    <div class="thermal-receipt">
        
        <!-- Header: Actual Company Logo & Name -->
        <div class="company-header">
            @if(!empty($company->logo) && file_exists(public_path('storage/' . $company->logo)))
                <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->company_name ?? 'Logo' }}" class="logo-img">
            @endif
            <div class="company-name">{{ $jobCard->branch?->name ?? ($company->company_name ?? 'Viran Auto Care') }}</div>
            @if($jobCard->branch?->address || $company->address)
                <div class="branch-meta">{{ $jobCard->branch?->address ?? $company->address }}</div>
            @endif
            @if($jobCard->branch?->phone || $company->phone)
                <div class="branch-meta">Tel: {{ $jobCard->branch?->phone ?? $company->phone }}</div>
            @endif
        </div>

        <div class="divider"></div>

        <div class="receipt-title">
            JOB CARD
        </div>

        <div class="divider"></div>

        <!-- Meta Information -->
        <div class="meta-row">
            <span class="meta-label">Job No:</span>
            <span class="meta-val font-black">{{ $jobCard->job_number }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Date:</span>
            <span class="meta-val">{{ $jobCard->date ? $jobCard->date->format('d/m/Y') : ($jobCard->created_at ? $jobCard->created_at->format('d/m/Y') : now()->format('d/m/Y')) }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Vehicle:</span>
            <span class="meta-val font-black uppercase" style="font-size: 12px;">
                {{ $jobCard->vehicle?->vehicle_number ?? 'N/A' }}
                @if($jobCard->vehicle?->make_model)
                    <span style="font-size: 10px; font-weight: 500; text-transform: none; display: block;">{{ $jobCard->vehicle->make_model }}</span>
                @endif
            </span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Customer:</span>
            <span class="meta-val font-bold">{{ $jobCard->customer?->customer_name ?? 'Customer Name' }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Phone:</span>
            <span class="meta-val">{{ $jobCard->customer?->phone ?? '—' }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Technician:</span>
            <span class="meta-val">{{ $jobCard->assignedTechnician?->name ?? 'Assigned Employee' }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Mileage:</span>
            <span class="meta-val">{{ $jobCard->current_mileage > 0 ? number_format($jobCard->current_mileage) . ' km' : 'Current Mileage' }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Status:</span>
            <span class="meta-val uppercase font-bold">{{ strtoupper(str_replace('_', ' ', $jobCard->status)) }}</span>
        </div>

        @if($jobCard->status === 'cancelled')
            <div style="text-align: center; font-weight: 900; font-size: 13px; border: 1.5px solid #000; padding: 3px 0; margin: 4px 0;">
                *** CANCELLED ***
            </div>
        @endif

        <!-- SERVICES Section -->
        @if($services->isNotEmpty())
            <div class="section-header">SERVICES</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="col-desc">Service Name</th>
                        <th class="col-qty">Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $srv)
                        <tr>
                            <td class="col-desc">
                                <div class="font-bold">{{ $srv->service_name }}</div>
                                @if($srv->is_additional)
                                    <span class="badge-sub">Additional Work</span>
                                @endif
                                @if(!empty($srv->notes))
                                    <div class="text-sub">{{ $srv->notes }}</div>
                                @endif
                            </td>
                            <td class="col-qty">{{ (float)$srv->quantity == (int)$srv->quantity ? (int)$srv->quantity : number_format($srv->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- PRODUCTS / PARTS Section -->
        @if($products->isNotEmpty())
            <div class="section-header">PRODUCTS / PARTS</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="col-desc">Product Name</th>
                        <th class="col-qty">Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $prod)
                        <tr>
                            <td class="col-desc">
                                <div class="font-bold">{{ $prod->product_name }}</div>
                                @if(!empty($prod->product_sku))
                                    <div class="text-sub">SKU: {{ $prod->product_sku }}</div>
                                @endif
                            </td>
                            <td class="col-qty">{{ (float)$prod->quantity == (int)$prod->quantity ? (int)$prod->quantity : number_format($prod->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- VEHICLE ISSUES Section -->
        @if(isset($issues) && $issues->isNotEmpty())
            <div class="section-header">VEHICLE ISSUES</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 55%;">Issue Description</th>
                        <th style="width: 45%; text-align: right;">Recommended Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($issues as $issue)
                        <tr>
                            <td style="width: 55%; padding-right: 4px;">
                                <div class="font-bold">{{ $issue->description }}</div>
                                <div class="text-sub">Status: {{ ucfirst(str_replace('_', ' ', $issue->status)) }}</div>
                            </td>
                            <td style="width: 45%; text-align: right; vertical-align: top;">
                                {{ $issue->recommended_repair ?: '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- CARPET DETAILS Section -->
        @if(isset($carpets) && $carpets->isNotEmpty())
            <div class="section-header">CARPET DETAILS</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="col-desc">Carpet Item</th>
                        <th class="col-qty">Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($carpets as $carpet)
                        @if($carpet->quantity > 0)
                            <tr>
                                <td class="col-desc">
                                    <div class="font-bold">{{ $carpet->category_name }}</div>
                                </td>
                                <td class="col-qty">{{ (int) $carpet->quantity }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        @endif

        @if($services->isEmpty() && $products->isEmpty() && (!isset($issues) || $issues->isEmpty()) && (!isset($carpets) || $carpets->isEmpty()))
            <div style="text-align: center; padding: 10px 0; color: #555; font-style: italic;">
                No items recorded for this print.
            </div>
        @endif

        @if($grandTotal > 0)
            <div class="divider"></div>
            <div class="meta-row font-bold" style="font-size: 11px;">
                <span class="meta-label">{{ $isSelective ? 'Selection Total:' : 'Estimated Total:' }}</span>
                <span class="meta-val">LKR {{ number_format($grandTotal, 2) }}</span>
            </div>
        @endif

        <!-- Customer & Service Notes -->
        @if(!empty($jobCard->customer_notes) || !empty($jobCard->service_notes))
            <div class="divider"></div>
            @if(!empty($jobCard->customer_notes))
                <div class="meta-row-note"><strong>Customer Note:</strong> {{ $jobCard->customer_notes }}</div>
            @endif
            @if(!empty($jobCard->service_notes))
                <div class="meta-row-note"><strong>Instructions:</strong> {{ $jobCard->service_notes }}</div>
            @endif
        @endif

        <!-- Signatures Area -->
        <div class="signatures-area">
            <div class="sig-block">
                Customer Signature
            </div>
            <div class="sig-block">
                Technician / Staff
            </div>
        </div>

    </div>

    <!-- Universal QZ Tray Printer Support -->
    <script src="{{ asset('vendor/qz/qz-tray.js') }}"></script>
    <script src="{{ asset('js/autocare-printer.js') }}"></script>
    <script>
        function handleJobPrint() {
            if (window.AutoCarePrinter && window.AutoCarePrinter.isConnected) {
                window.AutoCarePrinter.printDocument({
                    type: 'job_card',
                    html: document.querySelector('.thermal-receipt').outerHTML,
                    title: 'JobCard_{{ $jobCard->job_number }}',
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
