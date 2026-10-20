<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation_{{ $quotation->quotation_number }}</title>
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
            max-width: 820px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: background 0.15s ease;
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

        .quotation-card {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0B1849;
            padding-bottom: 20px;
            margin-bottom: 24px;
        }

        .company-name {
            font-size: 22px;
            font-weight: 900;
            color: #0B1849;
            letter-spacing: -0.5px;
        }

        .company-details {
            font-size: 12px;
            color: #4b5563;
            margin-top: 4px;
            line-height: 1.4;
        }

        .doc-title {
            text-align: right;
        }

        .doc-title h1 {
            font-size: 24px;
            font-weight: 900;
            color: #111827;
            letter-spacing: 1px;
        }

        .doc-number {
            font-size: 13px;
            font-weight: 700;
            color: #0B1849;
            font-family: monospace;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            background-color: #f9fafb;
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 24px;
            border: 1px solid #f3f4f6;
        }

        .meta-label {
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            color: #6b7280;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .meta-value {
            font-weight: 700;
            color: #111827;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 12px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            margin-bottom: 24px;
        }

        .summary-left {
            flex: 1;
        }

        .summary-box {
            width: 320px;
            background: #f9fafb;
            padding: 16px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .summary-total {
            border-top: 2px solid #0B1849;
            padding-top: 8px;
            margin-top: 8px;
            font-weight: 900;
            font-size: 15px;
            color: #0B1849;
        }

        .words-box {
            background-color: #f8fafc;
            border-left: 3px solid #0B1849;
            padding: 10px 12px;
            font-size: 11.5px;
            color: #334155;
            border-radius: 0 4px 4px 0;
            margin-bottom: 16px;
        }

        .notes-card {
            background: #ffffff;
            border: 1px dashed #d1d5db;
            border-radius: 6px;
            padding: 12px;
            font-size: 11.5px;
            color: #4b5563;
            margin-bottom: 12px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 24px;
            border-top: 1px dashed #d1d5db;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .sig-box {
            text-align: center;
            width: 200px;
        }

        .sig-line {
            border-top: 1px solid #9ca3af;
            margin-top: 50px;
            padding-top: 6px;
            font-size: 11px;
            font-weight: 600;
            color: #4b5563;
        }

        .badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-service { background: #e0f2fe; color: #0369a1; }
        .badge-product { background: #dcfce7; color: #15803d; }
        .badge-custom { background: #f3f4f6; color: #4b5563; }

        @media print {
            body {
                background-color: #ffffff;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .quotation-card {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="no-print-bar">
        <a href="{{ route('billing.quotations.show', $quotation) }}" class="btn btn-secondary">
            &larr; Back to Quotation
        </a>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('billing.quotations.pdf', $quotation) }}" class="btn btn-secondary">
                Download PDF
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                Print Quotation
            </button>
        </div>
    </div>

    <div class="quotation-card">
        <!-- Header -->
        <div class="header">
            <div>
                <div class="company-name">{{ $company->company_name }}</div>
                <div class="company-details">
                    @if($company->address)
                        <div>{{ $company->address }}</div>
                    @endif
                    <div>
                        @if($company->phone) Tel: {{ $company->phone }} @endif
                        @if($company->email) | Email: {{ $company->email }} @endif
                    </div>
                    @if($company->tax_number)
                        <div>Tax / VAT: {{ $company->tax_number }}</div>
                    @endif
                    @if($quotation->branch)
                        <div>Branch: <strong>{{ $quotation->branch->branch_name }}</strong></div>
                    @endif
                </div>
            </div>
            <div class="doc-title">
                <h1>QUOTATION</h1>
                <div class="doc-number">#{{ $quotation->quotation_number }}</div>
                <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">
                    Date: {{ $quotation->quotation_date ? $quotation->quotation_date->format('M d, Y') : '' }}
                </div>
                @if($quotation->expiry_date)
                    <div style="font-size: 11px; color: #6b7280;">
                        Valid Until: {{ $quotation->expiry_date->format('M d, Y') }}
                    </div>
                @endif
                @if($quotation->reference)
                    <div style="font-size: 11px; color: #6b7280;">
                        Ref: {{ $quotation->reference }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Meta Grid -->
        <div class="meta-grid">
            <div>
                <div class="meta-label">Customer Details</div>
                <div class="meta-value">{{ $quotation->customer->customer_name }}</div>
                @if($quotation->customer->customer_type === 'company' && $quotation->customer->company_name)
                    <div style="font-size: 12px; font-weight: 600; color: #1e3a8a; margin-top: 2px;">
                        Company: {{ $quotation->customer->company_name }}
                    </div>
                @endif
                <div style="font-size: 12px; color: #4b5563; margin-top: 2px;">
                    @if($quotation->customer->phone)
                        <div>Phone: {{ $quotation->customer->phone }}</div>
                    @endif
                    @if($quotation->customer->address)
                        <div>{{ $quotation->customer->address }}</div>
                    @endif
                </div>
            </div>
            <div>
                <div class="meta-label">Vehicle Details</div>
                @if($quotation->vehicle)
                    <div class="meta-value">{{ $quotation->vehicle->vehicle_number }}</div>
                    <div style="font-size: 12px; color: #4b5563; margin-top: 2px;">
                        @if($quotation->vehicle->make_model)
                            <div>Make/Model: {{ $quotation->vehicle->make_model }}</div>
                        @endif
                        @if($quotation->vehicle->vehicleType)
                            <div>Type: {{ $quotation->vehicle->vehicleType->name }}</div>
                        @endif
                    </div>
                @else
                    <div style="color: #9ca3af; font-style: italic; font-size: 12px;">General Quotation (No Vehicle)</div>
                @endif

                @if($quotation->subject)
                    <div style="margin-top: 8px; padding-top: 6px; border-top: 1px dashed #e5e7eb;">
                        <span class="meta-label">Subject</span>
                        <div style="font-weight: 600; color: #1f2937;">{{ $quotation->subject }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 45%;">Description</th>
                    <th style="width: 15%; text-align: right;">Unit Price</th>
                    <th style="width: 8%; text-align: center;">Qty</th>
                    <th style="width: 12%; text-align: right;">Discount</th>
                    <th style="width: 15%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $index => $item)
                    <tr>
                        <td class="text-center" style="color: #9ca3af;">{{ $index + 1 }}</td>
                        <td>
                            <div style="font-weight: 600; color: #111827;">
                                {{ $item->item_name }}
                                <span class="badge badge-{{ $item->item_type }}">{{ ucfirst($item->item_type) }}</span>
                            </div>
                            @if($item->description)
                                <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">{{ $item->description }}</div>
                            @endif
                        </td>
                        <td class="text-right">LKR {{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-center">@quantity($item->quantity)</td>
                        <td class="text-right">
                            @if($item->discount_amount > 0)
                                <span style="color: #16a34a;">- LKR {{ number_format($item->discount_amount, 2) }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-right" style="font-weight: 700;">LKR {{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary & Totals -->
        <div class="summary-container">
            <div class="summary-left">
                @if(!empty($amountInWords))
                    <div class="words-box">
                        <strong>Amount in Words:</strong><br>
                        {{ $amountInWords }}
                    </div>
                @endif

                @if($quotation->notes)
                    <div class="notes-card">
                        <strong style="color: #374151; display: block; margin-bottom: 2px;">Notes:</strong>
                        {{ $quotation->notes }}
                    </div>
                @endif

                @if($quotation->terms_conditions)
                    <div class="notes-card">
                        <strong style="color: #374151; display: block; margin-bottom: 2px;">Terms & Conditions:</strong>
                        {{ $quotation->terms_conditions }}
                    </div>
                @endif
            </div>

            <div class="summary-box">
                <div class="summary-row">
                    <span style="color: #6b7280;">Subtotal:</span>
                    <span style="font-weight: 600;">LKR {{ number_format($quotation->subtotal, 2) }}</span>
                </div>
                @if($quotation->discount_amount > 0)
                    <div class="summary-row" style="color: #16a34a;">
                        <span>
                            Discount 
                            @if($quotation->discount_type === 'percentage')
                                ({{ (float)$quotation->discount_value }}%):
                            @else
                                :
                            @endif
                        </span>
                        <span style="font-weight: 600;">- LKR {{ number_format($quotation->discount_amount, 2) }}</span>
                    </div>
                @endif
                @if($quotation->tax_amount > 0)
                    <div class="summary-row">
                        <span style="color: #6b7280;">Tax ({{ (float)$quotation->tax_percentage }}%):</span>
                        <span style="font-weight: 600;">LKR {{ number_format($quotation->tax_amount, 2) }}</span>
                    </div>
                @endif
                @if($quotation->additional_charges > 0)
                    <div class="summary-row">
                        <span style="color: #6b7280;">Additional Charges:</span>
                        <span style="font-weight: 600;">LKR {{ number_format($quotation->additional_charges, 2) }}</span>
                    </div>
                @endif
                <div class="summary-row summary-total">
                    <span>Grand Total:</span>
                    <span>LKR {{ number_format($quotation->grand_total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer Signatures -->
        <div class="footer">
            <div class="sig-box">
                <div class="sig-line">Authorized Signature</div>
            </div>
            <div class="sig-box">
                <div class="sig-line">Customer Acceptance</div>
            </div>
        </div>
    </div>
</body>
</html>
