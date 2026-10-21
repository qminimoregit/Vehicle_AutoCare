<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>JobCard_{{ $jobCard->job_number }}{{ $isSelective ? '_Selected' : '' }}</title>
    <style>
        @page {
            margin: 25px 30px;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
        }
        body {
            color: #1f2937;
            font-size: 11px;
            line-height: 1.4;
            background: #ffffff;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0B1849;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }
        .company-name {
            font-size: 17px;
            font-weight: bold;
            color: #0B1849;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .company-meta {
            font-size: 9.5px;
            color: #4b5563;
            margin-top: 3px;
            line-height: 1.35;
        }
        .doc-title {
            font-size: 20px;
            font-weight: bold;
            color: #0B1849;
            text-align: right;
            text-transform: uppercase;
        }
        .doc-meta {
            font-size: 10px;
            color: #4b5563;
            text-align: right;
            margin-top: 4px;
        }
        .doc-sub {
            font-size: 9.5px;
            font-weight: bold;
            color: #dc2626;
            text-align: right;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 14px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .info-td {
            padding: 8px 12px;
            vertical-align: top;
            width: 50%;
        }
        .info-heading {
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 2px;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0B1849;
            border-bottom: 1px solid #0B1849;
            padding-bottom: 3px;
            margin-top: 14px;
            margin-bottom: 6px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .items-table th {
            background-color: #0B1849;
            color: #ffffff;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 8px;
            text-align: left;
        }
        .items-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-table {
            width: 100%;
            margin-top: 6px;
            margin-bottom: 16px;
        }
        .totals-box {
            width: 260px;
            float: right;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
        }
        .totals-row {
            font-size: 10.5px;
            margin-bottom: 4px;
        }
        .totals-row.grand {
            border-top: 1.5px solid #0B1849;
            padding-top: 5px;
            margin-top: 5px;
            font-weight: bold;
            font-size: 12px;
            color: #0B1849;
        }
        .signatures {
            width: 100%;
            margin-top: 40px;
            padding-top: 10px;
        }
        .sig-td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }
        .sig-line {
            border-top: 1px dashed #64748b;
            margin-bottom: 5px;
        }
        .sig-text {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #475569;
        }
        .disclaimer {
            font-size: 9px;
            color: #64748b;
            background: #f8fafc;
            border-left: 2px solid #0B1849;
            padding: 6px 8px;
            margin-top: 15px;
            margin-bottom: 15px;
            clear: both;
        }
        .watermark {
            position: absolute;
            top: 40%;
            left: 25%;
            font-size: 60px;
            color: rgba(220, 38, 38, 0.12);
            transform: rotate(-30deg);
            font-weight: bold;
        }
    </style>
</head>
<body>
    @if($jobCard->status === 'cancelled')
        <div class="watermark">CANCELLED</div>
    @endif

    <table class="header-table">
        <tr>
            <td style="vertical-align: top; width: 60%;">
                <div class="company-name">{{ $company->company_name }}</div>
                <div class="company-meta">
                    @if($jobCard->branch)
                        <strong>{{ $jobCard->branch->name }}</strong><br>
                        {{ $jobCard->branch->address ?? $company->address }}<br>
                        Phone: {{ $jobCard->branch->phone ?? $company->phone }}
                    @else
                        {{ $company->address }}<br>
                        Phone: {{ $company->phone }} | Email: {{ $company->email }}
                    @endif
                </div>
            </td>
            <td style="vertical-align: top; width: 40%;">
                <div class="doc-title">
                    {{ $isSelective ? 'Workshop Work Slip' : 'Job Card' }}
                </div>
                <div class="doc-sub">
                    @if($jobCard->invoice)
                        Invoice #{{ $jobCard->invoice->invoice_number }}
                    @else
                        Work Order &bull; Not a Tax Invoice
                    @endif
                </div>
                <div class="doc-meta">
                    <strong>Job #:</strong> {{ $jobCard->job_number }}<br>
                    <strong>Date:</strong> {{ $jobCard->date ? $jobCard->date->format('Y-m-d') : $jobCard->created_at->format('Y-m-d') }}<br>
                    <strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $jobCard->status)) }}
                </div>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td class="info-td">
                <div class="info-heading">Vehicle Information</div>
                <strong>Vehicle No:</strong> {{ $jobCard->vehicle->vehicle_number }}<br>
                <strong>Type / Model:</strong> {{ $jobCard->vehicleType->name }} &bull; {{ $jobCard->vehicle->make_model ?? 'General' }}<br>
                <strong>Mileage:</strong> {{ $jobCard->current_mileage > 0 ? number_format($jobCard->current_mileage) . ' km' : 'N/A' }}
                @if($jobCard->next_service_mileage > 0)
                    (Next: {{ number_format($jobCard->next_service_mileage) }} km)
                @endif
            </td>
            <td class="info-td">
                <div class="info-heading">Customer & Assignment</div>
                <strong>Customer:</strong> {{ $jobCard->customer->customer_name }}<br>
                <strong>Contact:</strong> {{ $jobCard->customer->phone }}<br>
                <strong>Technician:</strong> {{ $jobCard->assignedTechnician?->name ?? 'Workshop Floor' }}
            </td>
        </tr>
    </table>

    @if($services->count() > 0)
        <div class="section-title">
            {{ $isSelective ? 'Selected Services for Work' : 'Workshop Services' }}
        </div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 25px;" class="text-center">#</th>
                    <th>Service Description</th>
                    <th style="width: 110px;">Technician</th>
                    <th style="width: 80px;" class="text-right">Price</th>
                    <th style="width: 40px;" class="text-center">Qty</th>
                    <th style="width: 90px;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $idx => $srv)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>
                            <strong>{{ $srv->service_name }}</strong>
                            @if($srv->is_additional)
                                <span style="font-size: 8.5px; color: #b45309;">(Additional)</span>
                            @endif
                        </td>
                        <td>{{ $srv->technician_name ?? $jobCard->assignedTechnician?->name ?? '—' }}</td>
                        <td class="text-right">LKR {{ number_format($srv->unit_price, 2) }}</td>
                        <td class="text-center">{{ (float) $srv->quantity }}</td>
                        <td class="text-right"><strong>LKR {{ number_format($srv->line_total, 2) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($products->count() > 0)
        <div class="section-title">
            {{ $isSelective ? 'Selected Parts Issued' : 'Products & Parts Issued' }}
        </div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 25px;" class="text-center">#</th>
                    <th>Product / Part Details</th>
                    <th style="width: 90px;">SKU</th>
                    <th style="width: 80px;" class="text-right">Selling Price</th>
                    <th style="width: 40px;" class="text-center">Qty</th>
                    <th style="width: 90px;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $pidx => $prod)
                    <tr>
                        <td class="text-center">{{ $pidx + 1 }}</td>
                        <td>
                            <strong>{{ $prod->product_name }}</strong>
                            @if($prod->is_additional)
                                <span style="font-size: 8.5px; color: #b45309;">(Additional)</span>
                            @endif
                        </td>
                        <td>{{ $prod->product_sku ?? '—' }}</td>
                        <td class="text-right">LKR {{ number_format($prod->unit_price, 2) }}</td>
                        <td class="text-center">{{ (float) $prod->quantity }}</td>
                        <td class="text-right"><strong>LKR {{ number_format($prod->line_total, 2) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div style="width: 100%; margin-top: 10px;">
        <div class="totals-box">
            @if($services->count() > 0)
                <div class="totals-row">
                    <span style="color: #64748b;">Services Amount:</span>
                    <span style="float: right;">LKR {{ number_format($servicesTotal, 2) }}</span>
                </div>
            @endif
            @if($products->count() > 0)
                <div class="totals-row">
                    <span style="color: #64748b;">Parts Amount:</span>
                    <span style="float: right;">LKR {{ number_format($productsTotal, 2) }}</span>
                </div>
            @endif
            @if(! $isSelective && (float) $jobCard->discount_amount > 0)
                <div class="totals-row" style="color: #dc2626;">
                    <span>Discount:</span>
                    <span style="float: right;">- LKR {{ number_format($jobCard->discount_amount, 2) }}</span>
                </div>
            @endif
            @if(! $isSelective && (float) $jobCard->tax_amount > 0)
                <div class="totals-row">
                    <span style="color: #64748b;">Tax:</span>
                    <span style="float: right;">LKR {{ number_format($jobCard->tax_amount, 2) }}</span>
                </div>
            @endif
            <div class="totals-row grand">
                <span>{{ $isSelective ? 'Selection Total:' : 'Grand Total:' }}</span>
                <span style="float: right;">LKR {{ number_format($grandTotal, 2) }}</span>
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="disclaimer">
        <strong>Notice:</strong> This document is a formal Job Card & Work Order issued by {{ $company->company_name }}. Internal FIFO purchase costs are strictly confidential and omitted.
    </div>

    <table class="signatures">
        <tr>
            <td class="sig-td">
                <div class="sig-line"></div>
                <div class="sig-text">Prepared By</div>
            </td>
            <td class="sig-td">
                <div class="sig-line"></div>
                <div class="sig-text">Technician</div>
            </td>
            <td class="sig-td">
                <div class="sig-line"></div>
                <div class="sig-text">Customer Signature</div>
            </td>
        </tr>
    </table>
</body>
</html>
