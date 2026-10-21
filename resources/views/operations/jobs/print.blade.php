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
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        body {
            background-color: #f3f4f6;
            color: #1f2937;
            padding: 24px;
            font-size: 13px;
            line-height: 1.5;
        }

        .no-print-bar {
            max-width: 840px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 12px 18px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
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

        .badge-mode {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
        }
        .badge-full {
            background-color: #EEF1FA;
            color: #0B1849;
        }
        .badge-selected {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .print-sheet {
            max-width: 840px;
            margin: 0 auto;
            background: #ffffff;
            padding: 36px 40px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            position: relative;
        }

        /* Watermark for Cancelled */
        .cancelled-watermark {
            position: absolute;
            top: 35%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 68px;
            font-weight: 900;
            color: rgba(220, 38, 38, 0.15);
            border: 6px dashed rgba(220, 38, 38, 0.25);
            padding: 10px 40px;
            border-radius: 12px;
            text-transform: uppercase;
            pointer-events: none;
            letter-spacing: 4px;
            z-index: 5;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0B1849;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 20px;
            font-weight: 900;
            color: #0B1849;
            letter-spacing: -0.5px;
        }

        .company-details {
            font-size: 11.5px;
            color: #4b5563;
            margin-top: 3px;
            line-height: 1.4;
        }

        .doc-title-block {
            text-align: right;
        }

        .doc-title {
            font-size: 22px;
            font-weight: 900;
            color: #0B1849;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .doc-sub {
            font-size: 11px;
            font-weight: 700;
            color: #dc2626;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .doc-number {
            font-size: 13px;
            font-weight: 800;
            color: #1f2937;
            margin-top: 4px;
        }

        /* Details Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px 16px;
            margin-bottom: 22px;
        }

        .info-col {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .info-row {
            display: flex;
            font-size: 12px;
        }

        .info-label {
            width: 140px;
            color: #64748b;
            font-weight: 600;
        }

        .info-val {
            color: #0f172a;
            font-weight: 700;
            flex: 1;
        }

        /* Tables */
        .section-header {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0B1849;
            border-bottom: 1.5px solid #0B1849;
            padding-bottom: 4px;
            margin-top: 18px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 10.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            border-bottom: 1px solid #cbd5e1;
        }

        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11.5px;
            color: #1e293b;
        }

        tr {
            page-break-inside: avoid;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .item-notes {
            font-size: 10.5px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Totals */
        .totals-block {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-top: 12px;
            margin-bottom: 24px;
            page-break-inside: avoid;
        }

        .notes-area {
            flex: 1;
            font-size: 11px;
            color: #475569;
            background: #fff;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            padding: 10px 12px;
        }

        .totals-table {
            width: 290px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 12px;
        }

        .summary-row.grand {
            border-top: 2px solid #0B1849;
            padding-top: 6px;
            margin-top: 6px;
            font-weight: 900;
            font-size: 14px;
            color: #0B1849;
        }

        /* Disclaimer & Signatures */
        .disclaimer-box {
            background-color: #f8fafc;
            border-left: 3px solid #0B1849;
            padding: 8px 12px;
            font-size: 10.5px;
            color: #475569;
            margin-bottom: 28px;
            line-height: 1.4;
            page-break-inside: avoid;
        }

        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-top: 36px;
            padding-top: 10px;
            page-break-inside: avoid;
        }

        .sig-block {
            text-align: center;
        }

        .sig-line {
            border-top: 1px dashed #64748b;
            margin-bottom: 6px;
        }

        .sig-label {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            color: #475569;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .no-print-bar {
                display: none !important;
            }

            .print-sheet {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }

            @page {
                size: A4 portrait;
                margin: 10mm 12mm;
            }
        }
    </style>
</head>
<body>
    <!-- Top Action Bar (Hidden on Print) -->
    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('operations.job-cards.show', $jobCard) }}" class="btn btn-secondary">
                &larr; Back to Job Card
            </a>
            <span class="badge-mode {{ $isSelective ? 'badge-selected' : 'badge-full' }}">
                {{ $isSelective ? 'Selected Items Print Slip (' . ($services->count() + $products->count()) . ' items)' : 'Full Job Card Document' }}
            </span>
        </div>

        <div style="display: flex; align-items: center; gap: 8px;">
            <a 
                href="{{ route('operations.job-cards.pdf', array_merge(['jobCard' => $jobCard], request()->query())) }}" 
                class="btn btn-secondary"
            >
                Download PDF
            </a>
            <button type="button" onclick="window.print()" class="btn btn-primary">
                Print Document
            </button>
        </div>
    </div>

    <!-- Main Printable Sheet -->
    <div class="print-sheet">
        @if($jobCard->status === 'cancelled')
            <div class="cancelled-watermark">CANCELLED</div>
        @endif

        <!-- Header -->
        <div class="header">
            <div>
                <div class="company-name">{{ $company->company_name }}</div>
                <div class="company-details">
                    @if($jobCard->branch)
                        <strong>{{ $jobCard->branch->name }}</strong><br>
                        {{ $jobCard->branch->address ?? $company->address }}<br>
                        Phone: {{ $jobCard->branch->phone ?? $company->phone }}
                    @else
                        {{ $company->address }}<br>
                        Phone: {{ $company->phone }} | Email: {{ $company->email }}
                    @endif
                </div>
            </div>

            <div class="doc-title-block">
                <div class="doc-title">
                    {{ $isSelective ? 'Workshop Work Slip' : 'Job Card / Work Order' }}
                </div>
                <div class="doc-sub">
                    @if($jobCard->invoice)
                        Invoice #{{ $jobCard->invoice->invoice_number }}
                    @else
                        Work Order &bull; Not a Tax Invoice
                    @endif
                </div>
                <div class="doc-number">#{{ $jobCard->job_number }}</div>
            </div>
        </div>

        <!-- Vehicle & Job Info Grid -->
        <div class="info-grid">
            <div class="info-col">
                <div class="info-row">
                    <span class="info-label">Vehicle Number:</span>
                    <span class="info-val font-mono" style="font-size: 13px;">{{ $jobCard->vehicle->vehicle_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Vehicle Type / Model:</span>
                    <span class="info-val">{{ $jobCard->vehicleType->name }} &bull; {{ $jobCard->vehicle->make_model ?? 'General' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Customer Name:</span>
                    <span class="info-val">{{ $jobCard->customer->customer_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Customer Contact:</span>
                    <span class="info-val">{{ $jobCard->customer->phone }}</span>
                </div>
            </div>

            <div class="info-col">
                <div class="info-row">
                    <span class="info-label">Job Card Date:</span>
                    <span class="info-val">{{ $jobCard->date ? $jobCard->date->format('Y-m-d') : $jobCard->created_at->format('Y-m-d') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Job Status:</span>
                    <span class="info-val" style="text-transform: capitalize;">
                        {{ str_replace('_', ' ', $jobCard->status) }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Odometer (Mileage):</span>
                    <span class="info-val">
                        {{ $jobCard->current_mileage > 0 ? number_format($jobCard->current_mileage) . ' km' : 'N/A' }}
                        @if($jobCard->next_service_mileage > 0)
                            <span style="color: #64748b; font-weight: normal;">(Next: {{ number_format($jobCard->next_service_mileage) }} km)</span>
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Assigned Technician:</span>
                    <span class="info-val">{{ $jobCard->assignedTechnician?->name ?? 'Workshop Floor' }}</span>
                </div>
            </div>
        </div>

        <!-- 1. SERVICES SECTION (If any selected/present) -->
        @if($services->count() > 0)
            <div class="section-header">
                <span>{{ $isSelective ? 'Selected Services for Work' : 'Workshop Services Performed' }}</span>
                <span style="font-size: 10.5px; font-weight: 600; color: #64748b;">{{ $services->count() }} {{ Str::plural('service', $services->count()) }}</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 35px;" class="text-center">#</th>
                        <th>Service Description</th>
                        <th style="width: 140px;">Technician</th>
                        <th style="width: 90px;" class="text-right">Unit Price</th>
                        <th style="width: 50px;" class="text-center">Qty</th>
                        <th style="width: 100px;" class="text-right">Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $idx => $srv)
                        <tr>
                            <td class="text-center" style="color: #64748b;">{{ $idx + 1 }}</td>
                            <td>
                                <strong>{{ $srv->service_name }}</strong>
                                @if($srv->is_additional)
                                    <span style="font-size: 9px; padding: 1px 4px; background: #fef3c7; color: #92400e; border-radius: 3px; margin-left: 4px;">Additional</span>
                                @endif
                                @if($srv->notes)
                                    <div class="item-notes">{{ $srv->notes }}</div>
                                @endif
                            </td>
                            <td style="color: #475569;">{{ $srv->technician_name ?? $jobCard->assignedTechnician?->name ?? '—' }}</td>
                            <td class="text-right">LKR {{ number_format($srv->unit_price, 2) }}</td>
                            <td class="text-center">{{ (float) $srv->quantity }}</td>
                            <td class="text-right" style="font-weight: 700;">LKR {{ number_format($srv->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- 2. PRODUCTS & PARTS ISSUED SECTION (If any selected/present) -->
        @if($products->count() > 0)
            <div class="section-header">
                <span>{{ $isSelective ? 'Selected Parts Issued' : 'Products & Parts Issued' }}</span>
                <span style="font-size: 10.5px; font-weight: 600; color: #64748b;">{{ $products->count() }} {{ Str::plural('part', $products->count()) }}</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 35px;" class="text-center">#</th>
                        <th>Product / Part Details</th>
                        <th style="width: 120px;">Code / SKU</th>
                        <th style="width: 90px;" class="text-right">Selling Price</th>
                        <th style="width: 60px;" class="text-center">Qty</th>
                        <th style="width: 100px;" class="text-right">Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $pidx => $prod)
                        <tr>
                            <td class="text-center" style="color: #64748b;">{{ $pidx + 1 }}</td>
                            <td>
                                <strong>{{ $prod->product_name }}</strong>
                                @if($prod->is_additional)
                                    <span style="font-size: 9px; padding: 1px 4px; background: #fef3c7; color: #92400e; border-radius: 3px; margin-left: 4px;">Additional</span>
                                @endif
                                @if($prod->notes)
                                    <div class="item-notes">{{ $prod->notes }}</div>
                                @endif
                            </td>
                            <td style="font-family: monospace; font-size: 11px; color: #475569;">{{ $prod->product_sku ?? '—' }}</td>
                            <td class="text-right">LKR {{ number_format($prod->unit_price, 2) }}</td>
                            <td class="text-center">{{ (float) $prod->quantity }}</td>
                            <td class="text-right" style="font-weight: 700;">LKR {{ number_format($prod->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- 3. VEHICLE ISSUES & WORK NOTES (Only in Full Print mode or if notes exist) -->
        @if(! $isSelective && ($jobCard->issues->count() > 0 || $jobCard->service_notes || $jobCard->customer_notes))
            <div class="section-header">
                <span>Vehicle Issues & Workshop Instructions</span>
            </div>
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; font-size: 11.5px;">
                @if($jobCard->issues->count() > 0)
                    <div style="margin-bottom: 6px;">
                        <strong>Approved Vehicle Issues:</strong>
                        <ul style="padding-left: 18px; margin-top: 3px;">
                            @foreach($jobCard->issues as $issue)
                                <li>{{ $issue->description }} <span style="color: #059669; font-weight: 600;">(Approved)</span></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if($jobCard->customer_notes)
                    <div style="margin-top: 4px;"><strong>Customer Request:</strong> {{ $jobCard->customer_notes }}</div>
                @endif
                @if($jobCard->service_notes)
                    <div style="margin-top: 4px;"><strong>Technician Remarks:</strong> {{ $jobCard->service_notes }}</div>
                @endif
            </div>
        @endif

        <!-- Totals Block -->
        <div class="totals-block">
            <div class="notes-area">
                <strong>Payment & Work Order Notice:</strong>
                <p style="margin-top: 3px;">
                    @if($jobCard->invoice && $jobCard->invoice->status === 'paid')
                        <span style="color: #059669; font-weight: 700;">PAID IN FULL</span> &bull; Covered under Invoice #{{ $jobCard->invoice->invoice_number }}.
                    @elseif($jobCard->invoice && $jobCard->invoice->amount_paid > 0)
                        <span style="color: #d97706; font-weight: 700;">PARTIALLY PAID</span> &bull; Paid: LKR {{ number_format($jobCard->invoice->amount_paid, 2) }} | Balance: LKR {{ number_format($jobCard->invoice->balance_amount, 2) }}.
                    @else
                        This document serves as an official service Job Card & Work Order. Final tax invoice will be issued upon job completion.
                    @endif
                </p>
            </div>

            <div class="totals-table">
                @if($services->count() > 0)
                    <div class="summary-row">
                        <span style="color: #64748b;">Services Amount:</span>
                        <span>LKR {{ number_format($servicesTotal, 2) }}</span>
                    </div>
                @endif
                @if($products->count() > 0)
                    <div class="summary-row">
                        <span style="color: #64748b;">Products Amount:</span>
                        <span>LKR {{ number_format($productsTotal, 2) }}</span>
                    </div>
                @endif
                @if(! $isSelective && (float) $jobCard->discount_amount > 0)
                    <div class="summary-row" style="color: #dc2626;">
                        <span>Discount:</span>
                        <span>- LKR {{ number_format($jobCard->discount_amount, 2) }}</span>
                    </div>
                @endif
                @if(! $isSelective && (float) $jobCard->tax_amount > 0)
                    <div class="summary-row">
                        <span style="color: #64748b;">Tax / VAT:</span>
                        <span>LKR {{ number_format($jobCard->tax_amount, 2) }}</span>
                    </div>
                @endif
                <div class="summary-row grand">
                    <span>{{ $isSelective ? 'Selection Total:' : 'Job Grand Total:' }}</span>
                    <span>LKR {{ number_format($grandTotal, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Disclaimer -->
        <div class="disclaimer-box">
            <strong>Terms & Guarantee:</strong> AutoCare guarantees workmanship on installed replacement parts for 30 days or 1,000 km, whichever comes first. Vehicles left over 7 days after completion may incur storage charges.
        </div>

        <!-- Signature Section -->
        <div class="signatures">
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-label">Prepared By (Staff)</div>
            </div>
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-label">Assigned Technician</div>
            </div>
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-label">Customer Signature</div>
            </div>
        </div>
    </div>
</body>
</html>
