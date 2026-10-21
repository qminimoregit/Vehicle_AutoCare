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

        .section-header {
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            padding: 3px 0 2px 0;
            border-bottom: 1px dashed #000;
            margin-top: 6px;
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

        .notes-box {
            border: 1px solid #333;
            padding: 6px 8px;
            margin-top: 6px;
            font-size: 10px;
            line-height: 1.35;
        }

        .signatures-area {
            margin-top: 18px;
            padding-top: 10px;
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
            <button onclick="window.print()" class="btn btn-primary">
                Print 80mm
            </button>
        </div>
    </div>

    <!-- 80mm Thermal Job Card / Workshop Slip -->
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
            </div>
        </div>

        <div class="divider"></div>

        <div class="receipt-title">
            {{ $isSelective ? 'Workshop Work Slip' : 'Job Card / Work Order' }}
        </div>
        <div style="font-size: 9px; text-align: center; color: #555; margin-top: 2px;">
            Work Order &bull; Not a Tax Invoice
        </div>

        <div class="divider"></div>

        <!-- Meta Information -->
        <div class="meta-row">
            <span class="meta-label">Job Card No:</span>
            <span class="meta-val font-black">{{ $jobCard->job_number }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Job Date:</span>
            <span class="meta-val">{{ $jobCard->date ? $jobCard->date->format('Y-m-d') : now()->format('Y-m-d') }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Time:</span>
            <span class="meta-val">{{ $jobCard->created_at ? $jobCard->created_at->format('H:i') : now()->format('H:i') }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Status:</span>
            <span class="meta-val uppercase font-bold">{{ strtoupper(str_replace('_', ' ', $jobCard->status)) }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Technician:</span>
            <span class="meta-val">{{ $jobCard->assignedTechnician?->name ?? 'Unassigned' }}</span>
        </div>

        <div class="divider"></div>

        <!-- Vehicle & Customer Details -->
        <div class="meta-row">
            <span class="meta-label">Vehicle No:</span>
            <span class="meta-val font-black uppercase" style="font-size: 13px;">{{ $jobCard->vehicle?->vehicle_number ?? 'N/A' }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Vehicle Type:</span>
            <span class="meta-val">{{ $jobCard->vehicleType?->name ?? ($jobCard->vehicle?->vehicleType?->name ?? 'General') }}</span>
        </div>

        @if($jobCard->vehicle?->make_model)
            <div class="meta-row">
                <span class="meta-label">Model:</span>
                <span class="meta-val">{{ $jobCard->vehicle->make_model }}</span>
            </div>
        @endif

        @if($jobCard->current_mileage > 0)
            <div class="meta-row">
                <span class="meta-label">Current Mileage:</span>
                <span class="meta-val">{{ number_format($jobCard->current_mileage) }} km</span>
            </div>
        @endif

        @if($jobCard->next_service_mileage > 0)
            <div class="meta-row">
                <span class="meta-label">Next Service Due:</span>
                <span class="meta-val">{{ number_format($jobCard->next_service_mileage) }} km</span>
            </div>
        @endif

        <div class="meta-row">
            <span class="meta-label">Customer:</span>
            <span class="meta-val font-bold">{{ $jobCard->customer?->customer_name ?? 'Walk-in Customer' }}</span>
        </div>

        @if($jobCard->customer?->phone)
            <div class="meta-row">
                <span class="meta-label">Mobile:</span>
                <span class="meta-val">{{ $jobCard->customer->phone }}</span>
            </div>
        @endif

        <div class="divider"></div>

        <!-- Services Section -->
        @if($services->isNotEmpty())
            <div class="section-header">Services ({{ $services->count() }})</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="col-qty">QTY</th>
                        <th class="col-desc">SERVICE</th>
                        <th class="col-amt">AMOUNT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $item)
                        <tr>
                            <td class="col-qty">{{ (float)$item->quantity == (int)$item->quantity ? (int)$item->quantity : number_format($item->quantity, 2) }}</td>
                            <td class="col-desc">
                                {{ $item->service_name }}
                                @if(!empty($item->notes))
                                    <div style="font-size: 9px; color: #444;">Note: {{ $item->notes }}</div>
                                @endif
                            </td>
                            <td class="col-amt">{{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Products / Spare Parts Section -->
        @if($products->isNotEmpty())
            <div class="section-header">Issued Parts & Lubricants ({{ $products->count() }})</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="col-qty">QTY</th>
                        <th class="col-desc">PRODUCT</th>
                        <th class="col-amt">AMOUNT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $item)
                        <tr>
                            <td class="col-qty">{{ (float)$item->quantity == (int)$item->quantity ? (int)$item->quantity : number_format($item->quantity, 2) }}</td>
                            <td class="col-desc">
                                {{ $item->product_name }}
                                @if(!empty($item->product_sku))
                                    <div style="font-size: 9px; color: #444;">SKU: {{ $item->product_sku }}</div>
                                @endif
                            </td>
                            <td class="col-amt">{{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if($services->isEmpty() && $products->isEmpty())
            <div style="text-align: center; padding: 12px 0; color: #555; font-style: italic;">
                No services or spare parts added yet.<br>
                Pending initial inspection.
            </div>
        @endif

        <div class="divider"></div>

        <!-- Totals -->
        <table class="totals-table">
            @if($servicesTotal > 0)
                <tr>
                    <td>Services Total:</td>
                    <td class="text-right font-bold">{{ number_format($servicesTotal, 2) }}</td>
                </tr>
            @endif
            @if($productsTotal > 0)
                <tr>
                    <td>Products Total:</td>
                    <td class="text-right font-bold">{{ number_format($productsTotal, 2) }}</td>
                </tr>
            @endif
            @if(!$isSelective && (float)$jobCard->discount_amount > 0)
                <tr>
                    <td>Discount:</td>
                    <td class="text-right font-bold">-{{ number_format($jobCard->discount_amount, 2) }}</td>
                </tr>
            @endif
            <tr class="grand-total-row">
                <td>ESTIMATED TOTAL:</td>
                <td class="text-right">LKR {{ number_format($grandTotal, 2) }}</td>
            </tr>
        </table>

        <!-- Notes / Instructions if any -->
        @if(!empty($jobCard->customer_notes) || !empty($jobCard->service_notes))
            <div class="notes-box">
                @if(!empty($jobCard->customer_notes))
                    <div><strong>Customer Request:</strong> {{ $jobCard->customer_notes }}</div>
                @endif
                @if(!empty($jobCard->service_notes))
                    <div style="margin-top: 3px;"><strong>Workshop Instructions:</strong> {{ $jobCard->service_notes }}</div>
                @endif
            </div>
        @endif

        <!-- Customer & Supervisor Signatures -->
        <div class="signatures-area">
            <div class="sig-block">
                Customer Signature
            </div>
            <div class="sig-block">
                Technician / Supervisor
            </div>
        </div>

        <div class="divider"></div>

        <!-- Footer -->
        <div class="footer-note">
            <div class="font-bold">AutoCare Workshop Service</div>
            <div>Authorized Job Card • Quality Assured</div>
        </div>

        <div class="barcode-area">
            *{{ $jobCard->job_number }}*
        </div>
    </div>

</body>
</html>
