<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
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
            padding-bottom: 15px;
            margin-bottom: 18px;
        }
        .company-name {
            font-size: 18px;
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
            font-size: 24px;
            font-weight: 900;
            color: #0B1849;
            letter-spacing: 1px;
            text-align: right;
        }
        .doc-meta {
            font-size: 10px;
            color: #4b5563;
            text-align: right;
            margin-top: 4px;
        }
        .doc-meta strong {
            color: #111827;
        }
        .info-table {
            width: 100%;
            margin-bottom: 18px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }
        .info-td {
            padding: 10px 14px;
            vertical-align: top;
            width: 50%;
        }
        .info-heading {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 4px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 2px;
        }
        .customer-name {
            font-size: 12px;
            font-weight: bold;
            color: #0B1849;
        }
        .vehicle-badge {
            display: inline-block;
            background: #0B1849;
            color: #ffffff;
            font-weight: bold;
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 3px;
            margin-top: 2px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items-table th {
            background-color: #0B1849;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 7px 8px;
            text-align: left;
        }
        .items-table td {
            padding: 7px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-table {
            width: 100%;
            margin-bottom: 18px;
        }
        .totals-td-left {
            width: 55%;
            vertical-align: top;
            padding-right: 20px;
        }
        .totals-td-right {
            width: 45%;
            vertical-align: top;
        }
        .summary-box {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-box td {
            padding: 4px 6px;
            font-size: 10px;
        }
        .summary-label {
            color: #4b5563;
        }
        .summary-value {
            text-align: right;
            font-weight: bold;
            color: #111827;
        }
        .grand-total-row td {
            background-color: #0B1849;
            color: #ffffff !important;
            font-weight: bold;
            font-size: 12px;
            padding: 6px 8px;
        }
        .grand-total-row .summary-value {
            color: #ffffff !important;
        }
        .paid-row td {
            color: #15803d !important;
            font-weight: bold;
        }
        .balance-row td {
            font-weight: bold;
            border-top: 1px solid #cbd5e1;
        }
        .balance-due {
            color: #dc2626;
        }
        .words-box {
            background-color: #f1f5f9;
            border-left: 3px solid #0B1849;
            padding: 8px 10px;
            font-size: 9.5px;
            color: #334155;
            margin-top: 6px;
        }
        .section-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 10px;
            margin-bottom: 12px;
        }
        .section-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 3px;
        }
        .section-content {
            font-size: 9px;
            color: #475569;
            white-space: pre-line;
        }
        .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        .payments-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 6px;
            text-align: left;
            border-bottom: 1px solid #cbd5e1;
        }
        .payments-table td {
            padding: 5px 6px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9px;
        }
        .signatures-table {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .sig-line {
            border-top: 1px solid #94a3b8;
            padding-top: 4px;
            font-size: 9px;
            font-weight: bold;
            color: #475569;
            text-align: center;
            width: 200px;
        }
        .badge {
            display: inline-block;
            font-size: 8px;
            font-weight: bold;
            padding: 1px 5px;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .badge-service { background-color: #e0f2fe; color: #0369a1; }
        .badge-product { background-color: #dcfce7; color: #15803d; }
        .badge-custom { background-color: #f1f5f9; color: #475569; }
        .status-stamp {
            display: inline-block;
            padding: 3px 10px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 3px;
            margin-top: 4px;
            border: 1px solid;
        }
        .stamp-issued { color: #0284c7; border-color: #bae6fd; background: #f0f9ff; }
        .stamp-partially_paid { color: #d97706; border-color: #fde68a; background: #fffbeb; }
        .stamp-paid { color: #16a34a; border-color: #bbf7d0; background: #f0fdf4; }
        .stamp-cancelled { color: #dc2626; border-color: #fecaca; background: #fef2f2; }
    </style>
</head>
<body>
    @php
        $logoBase64 = $company->getLogoBase64();
    @endphp

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" style="max-height: 48px; max-width: 180px; margin-bottom: 6px;">
                @endif
                <div class="company-name">{{ $company->company_name }}</div>
                <div class="company-meta">
                    @if($company->address)
                        <div>{{ $company->address }}</div>
                    @endif
                    <div>
                        @if($company->phone) Tel: {{ $company->phone }} @endif
                        @if($company->email) | Email: {{ $company->email }} @endif
                    </div>
                    @if($company->tax_number)
                        <div>Tax / VAT Reg: <strong>{{ $company->tax_number }}</strong></div>
                    @endif
                    @if($invoice->branch)
                        <div>Branch: <strong>{{ $invoice->branch->branch_name }}</strong></div>
                    @endif
                </div>
            </td>
            <td style="width: 45%; vertical-align: top; text-align: right;">
                <div class="doc-title">INVOICE</div>
                <div class="doc-meta">
                    <div>Invoice #: <strong>{{ $invoice->invoice_number }}</strong></div>
                    <div>Date: <strong>{{ $invoice->invoice_date ? $invoice->invoice_date->format('d/m/Y') : '' }}</strong></div>
                    @if($invoice->due_date)
                        <div>Due Date: <strong>{{ $invoice->due_date->format('d/m/Y') }}</strong></div>
                    @endif
                    @if($invoice->reference)
                        <div>Reference: <strong>{{ $invoice->reference }}</strong></div>
                    @endif
                    <div style="margin-top: 4px;">
                        <span class="status-stamp stamp-{{ $invoice->status }}">
                            {{ str_replace('_', ' ', strtoupper($invoice->status)) }}
                        </span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Customer and Source Information -->
    @php
        $pdfCustSnap = $invoice->customer_snapshot ?? [];
        $pdfCustomerName = $pdfCustSnap['customer_name'] ?? $invoice->customer?->customer_name;
        $pdfCustomerPhone = $pdfCustSnap['phone'] ?? $invoice->customer?->phone;
        $pdfCustomerEmail = $pdfCustSnap['email'] ?? $invoice->customer?->email;
        $pdfCustomerTax = $pdfCustSnap['tax_number'] ?? $invoice->customer?->tax_number;
        $pdfCustomerAddress = $pdfCustSnap['address'] ?? $invoice->customer?->address;
        $pdfSnapVehicle = $pdfCustSnap['vehicle_number'] ?? null;
        $pdfSnapType = $pdfCustSnap['vehicle_type'] ?? null;
        $pdfSnapMakeModel = $pdfCustSnap['vehicle_make_model'] ?? null;
    @endphp
    <table class="info-table">
        <tr>
            <td class="info-td">
                <div class="info-heading">Billed To</div>
                <div class="customer-name">{{ $pdfCustomerName }}</div>
                @if($invoice->customer && $invoice->customer->customer_type === 'company' && $invoice->customer->company_name)
                    <div style="font-weight: bold; color: #1e3a8a; margin-top: 2px;">
                        Company: {{ $invoice->customer->company_name }}
                    </div>
                @endif
                <div style="color: #4b5563; margin-top: 3px;">
                    @if($pdfCustomerPhone)
                        <div>Phone: {{ $pdfCustomerPhone }}</div>
                    @endif
                    @if($pdfCustomerEmail)
                        <div>Email: {{ $pdfCustomerEmail }}</div>
                    @endif
                    @if($pdfCustomerTax)
                        <div>Tax No: {{ $pdfCustomerTax }}</div>
                    @endif
                    @if($pdfCustomerAddress)
                        <div>Address: {{ $pdfCustomerAddress }}</div>
                    @endif
                </div>
            </td>
            <td class="info-td">
                <div class="info-heading">Service / Vehicle Details</div>
                @if($invoice->jobCard && $invoice->jobCard->vehicle)
                    <div class="vehicle-badge">{{ $invoice->jobCard->vehicle->vehicle_number }}</div>
                    <div style="color: #4b5563; margin-top: 4px;">
                        @if($invoice->jobCard->vehicleType)
                            <div>Type: {{ $invoice->jobCard->vehicleType->name }}</div>
                        @endif
                        @if($invoice->jobCard->current_mileage > 0)
                            <div>Mileage: {{ number_format($invoice->jobCard->current_mileage) }} km</div>
                        @endif
                        <div>Linked Job Card: <strong>#{{ $invoice->jobCard->job_number }}</strong></div>
                    </div>
                @elseif($invoice->quotation && $invoice->quotation->vehicle)
                    <div class="vehicle-badge">{{ $invoice->quotation->vehicle->vehicle_number }}</div>
                    <div style="color: #4b5563; margin-top: 4px;">
                        <div>Quotation: <strong>#{{ $invoice->quotation->quotation_number }}</strong></div>
                        @if($invoice->quotation->vehicle->make_model)
                            <div>Make/Model: {{ $invoice->quotation->vehicle->make_model }}</div>
                        @endif
                    </div>
                @elseif($pdfSnapVehicle)
                    <div class="vehicle-badge">{{ $pdfSnapVehicle }}</div>
                    <div style="color: #4b5563; margin-top: 4px;">
                        @if($pdfSnapMakeModel)
                            <div>Make/Model: {{ $pdfSnapMakeModel }}</div>
                        @endif
                        @if($pdfSnapType)
                            <div>Type: {{ $pdfSnapType }}</div>
                        @endif
                    </div>
                @elseif($invoice->invoice_type === 'direct_sale')
                    <div style="color: #047857; font-weight: bold; margin-top: 4px;">
                        Direct POS Spare Parts & Counter Sale
                    </div>
                @else
                    <div style="color: #64748b; font-style: italic; margin-top: 6px;">
                        Direct Invoice Billing
                    </div>
                @endif

                @if($invoice->subject)
                    <div style="margin-top: 6px; padding-top: 4px; border-top: 1px dashed #cbd5e1;">
                        <span style="font-size: 9px; color: #64748b;">Subject:</span>
                        <div style="font-weight: bold; color: #1e293b;">{{ $invoice->subject }}</div>
                    </div>
                @endif
            </td>
        </tr>
    </table>

    <!-- Line Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 45%;">Description</th>
                <th style="width: 12%;" class="text-right">Unit Price</th>
                <th style="width: 8%;" class="text-center">Qty</th>
                <th style="width: 12%;" class="text-right">Discount</th>
                <th style="width: 18%;" class="text-right">Total (LKR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <div style="font-weight: bold; color: #111827;">
                            {{ $item->item_name }}
                            <span class="badge badge-{{ $item->item_type }}">{{ ucfirst($item->item_type) }}</span>
                        </div>
                        @if($item->item_code)
                            <div style="font-size: 8.5px; color: #64748b; font-family: monospace;">Code: {{ $item->item_code }}</div>
                        @endif
                        @if($item->description)
                            <div style="font-size: 8.5px; color: #64748b; margin-top: 1px;">{{ $item->description }}</div>
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-center">@quantity($item->quantity)</td>
                    <td class="text-right">
                        @if($item->discount_amount > 0)
                            <span style="color: #16a34a;">-{{ number_format($item->discount_amount, 2) }}</span>
                        @else
                            —
                        @endif
                    </td>
                    <td class="text-right" style="font-weight: bold;">
                        {{ number_format($item->line_total, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals & Payment Summary -->
    <table class="totals-table">
        <tr>
            <td class="totals-td-left">
                @if(!empty($amountInWords))
                    <div class="words-box">
                        <strong>Amount in Words:</strong><br>
                        {{ $amountInWords }}
                    </div>
                @endif

                @if($invoice->notes)
                    <div class="section-box" style="margin-top: 10px;">
                        <div class="section-title">Notes / Instructions</div>
                        <div class="section-content">{{ $invoice->notes }}</div>
                    </div>
                @endif
            </td>
            <td class="totals-td-right">
                <table class="summary-box">
                    <tr>
                        <td class="summary-label">Subtotal:</td>
                        <td class="summary-value">LKR {{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                    @if($invoice->discount_amount > 0)
                        <tr>
                            <td class="summary-label" style="color: #15803d;">
                                Discount 
                                @if($invoice->discount_type === 'percentage')
                                    ({{ (float)$invoice->discount_value }}%):
                                @else
                                    :
                                @endif
                            </td>
                            <td class="summary-value" style="color: #15803d;">
                                - LKR {{ number_format($invoice->discount_amount, 2) }}
                            </td>
                        </tr>
                    @endif
                    @if($invoice->tax_amount > 0)
                        <tr>
                            <td class="summary-label">
                                Tax / VAT ({{ (float)$invoice->tax_percentage }}%):
                            </td>
                            <td class="summary-value">
                                LKR {{ number_format($invoice->tax_amount, 2) }}
                            </td>
                        </tr>
                    @endif
                    @if($invoice->additional_charges > 0)
                        <tr>
                            <td class="summary-label">Additional Charges:</td>
                            <td class="summary-value">
                                LKR {{ number_format($invoice->additional_charges, 2) }}
                            </td>
                        </tr>
                    @endif
                    <tr class="grand-total-row">
                        <td>GRAND TOTAL:</td>
                        <td class="summary-value">LKR {{ number_format($invoice->grand_total, 2) }}</td>
                    </tr>
                    <tr class="paid-row">
                        <td class="summary-label">Amount Paid:</td>
                        <td class="summary-value">LKR {{ number_format($invoice->amount_paid, 2) }}</td>
                    </tr>
                    <tr class="balance-row">
                        <td class="summary-label">Balance Due:</td>
                        <td class="summary-value {{ $invoice->balance_amount > 0 ? 'balance-due' : '' }}">
                            LKR {{ number_format($invoice->balance_amount, 2) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Payment Records Log (if any) -->
    @if($invoice->payments->count() > 0)
        <div class="section-box" style="margin-top: 5px;">
            <div class="section-title">Recorded Payments History</div>
            <table class="payments-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Payment #</th>
                        <th style="width: 20%;">Date</th>
                        <th style="width: 20%;">Method</th>
                        <th style="width: 20%;">Reference</th>
                        <th style="width: 15%; text-align: right;">Amount (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->payments as $pay)
                        <tr>
                            <td><strong>{{ $pay->payment_number }}</strong></td>
                            <td>{{ $pay->payment_date ? $pay->payment_date->format('d/m/Y') : '' }}</td>
                            <td style="text-transform: uppercase;">{{ str_replace('_', ' ', $pay->payment_method) }}</td>
                            <td>{{ $pay->reference_number ?? '—' }}</td>
                            <td style="text-align: right; font-weight: bold; color: #15803d;">
                                {{ number_format($pay->amount, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($invoice->terms_conditions)
        <div class="section-box" style="margin-top: 5px;">
            <div class="section-title">Terms & Conditions</div>
            <div class="section-content">{{ $invoice->terms_conditions }}</div>
        </div>
    @endif

    <!-- Signatures -->
    <table class="signatures-table">
        <tr>
            <td style="width: 50%; vertical-align: bottom;">
                <div class="sig-line">Prepared / Cashier Signature</div>
            </td>
            <td style="width: 50%; vertical-align: bottom; text-align: right;">
                <div class="sig-line" style="margin-left: auto;">Customer Signature & Stamp</div>
            </td>
        </tr>
    </table>
</body>
</html>
