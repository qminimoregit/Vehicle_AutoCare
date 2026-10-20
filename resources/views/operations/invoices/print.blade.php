<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice_{{ $invoice->invoice_number }}</title>
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
            max-width: 800px;
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
        }

        .btn-primary {
            background-color: #0B1849;
            color: #ffffff;
        }

        .btn-secondary {
            background-color: #ffffff;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .invoice-card {
            max-width: 800px;
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

        .invoice-title {
            text-align: right;
        }

        .invoice-title h1 {
            font-size: 24px;
            font-weight: 900;
            color: #111827;
            letter-spacing: 1px;
        }

        .invoice-number {
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
            color: #1f2937;
        }

        .text-right {
            text-align: right;
        }

        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }

        .totals-table {
            width: 280px;
        }

        .totals-table tr td {
            padding: 6px 0;
            border: none;
        }

        .grand-total-row td {
            font-size: 15px;
            font-weight: 900;
            color: #0B1849;
            border-top: 2px solid #0B1849;
            padding-top: 10px;
        }

        .footer {
            border-top: 1px dashed #d1d5db;
            padding-top: 16px;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .no-print-bar {
                display: none !important;
            }

            .invoice-card {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="no-print-bar">
        <a href="{{ route('operations.invoices.show', $invoice) }}" class="btn btn-secondary">
            &larr; Back to System
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            &#128438; Print / Save as PDF
        </button>
    </div>

    <div class="invoice-card">
        <!-- Header -->
        <div class="header">
            <div>
                <div class="company-name">{{ $company->company_name }}</div>
                <div class="company-details">
                    {{ $company->address }}<br>
                    @if($company->phone) Tel: {{ $company->phone }} @endif
                    @if($company->email) • Email: {{ $company->email }} @endif
                    @if($company->tax_number) <br>Tax/VAT Reg: {{ $company->tax_number }} @endif
                </div>
            </div>

            <div class="invoice-title">
                <h1>INVOICE</h1>
                <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">
                    Date: {{ $invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : '' }}
                </div>
            </div>
        </div>

        <!-- Meta Grid -->
        <div class="meta-grid">
            <div>
                <div class="meta-label">Customer Information</div>
                <div class="meta-value">{{ $invoice->customer->customer_name }}</div>
                @if($invoice->customer->phone)
                    <div style="font-size: 12px; color: #4b5563;">Mobile: {{ $invoice->customer->phone }}</div>
                @endif
                @if($invoice->customer->address)
                    <div style="font-size: 11px; color: #6b7280;">{{ $invoice->customer->address }}</div>
                @endif
            </div>

            <div style="text-align: right;">
                @if($invoice->jobCard)
                    <div class="meta-label">Vehicle & Service Job</div>
                    <div class="meta-value" style="letter-spacing: 1px;">{{ $invoice->jobCard->vehicle->vehicle_number }}</div>
                    <div style="font-size: 12px; color: #4b5563;">
                        {{ $invoice->jobCard->vehicleType->name }}
                        @if($invoice->jobCard->current_mileage > 0)
                            • {{ number_format($invoice->jobCard->current_mileage) }} km
                        @endif
                    </div>
                    @if($invoice->jobCard->next_service_mileage > 0)
                        <div style="font-size: 11px; color: #0B1849; font-weight: 700; margin-top: 2px;">
                            Next Service Due: {{ number_format($invoice->jobCard->next_service_mileage) }} km
                        </div>
                    @endif
                @else
                    <div class="meta-label">Transaction Type</div>
                    <div class="meta-value">Direct Sale (Over-The-Counter)</div>
                    <div style="font-size: 12px; color: #4b5563;">Ref: {{ $invoice->reference }}</div>
                @endif
            </div>
        </div>

        <!-- Table of Items -->
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">#</th>
                    <th style="text-align: left;">Item Description</th>
                    <th style="width: 100px; text-align: left;">Type</th>
                    <th class="text-right" style="width: 110px;">Unit Price</th>
                    <th class="text-right" style="width: 60px;">Qty</th>
                    <th class="text-right" style="width: 120px;">Total (LKR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                    <tr>
                        <td style="color: #9ca3af; font-size: 11px;">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->item_name }}</strong>
                            @if($item->item_code)
                                <span style="font-size: 10px; color: #6b7280; font-family: monospace; display: block;">{{ $item->item_code }}</span>
                            @endif
                        </td>
                        <td style="text-transform: capitalize; font-size: 11px; color: #4b5563;">
                            {{ $item->item_type }}
                        </td>
                        <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">{{ (float) $item->quantity }}</td>
                        <td class="text-right" style="font-weight: 700;">{{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td style="color: #6b7280;">Subtotal:</td>
                    <td class="text-right" style="font-weight: 600;">LKR {{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                @if($invoice->discount_amount > 0)
                    <tr>
                        <td style="color: #059669;">Discount:</td>
                        <td class="text-right" style="font-weight: 600; color: #059669;">- LKR {{ number_format($invoice->discount_amount, 2) }}</td>
                    </tr>
                @endif
                @if($invoice->tax_amount > 0)
                    <tr>
                        <td style="color: #6b7280;">Tax:</td>
                        <td class="text-right" style="font-weight: 600;">LKR {{ number_format($invoice->tax_amount, 2) }}</td>
                    </tr>
                @endif
                <tr class="grand-total-row">
                    <td>Grand Total:</td>
                    <td class="text-right">LKR {{ number_format($invoice->grand_total, 2) }}</td>
                </tr>
                <tr>
                    <td style="color: #059669; font-weight: 600;">Amount Paid:</td>
                    <td class="text-right" style="color: #059669; font-weight: 700;">LKR {{ number_format($invoice->amount_paid, 2) }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 700; color: {{ $invoice->balance_amount > 0 ? '#dc2626' : '#6b7280' }};">Balance Due:</td>
                    <td class="text-right" style="font-weight: 700; color: {{ $invoice->balance_amount > 0 ? '#dc2626' : '#6b7280' }};">LKR {{ number_format($invoice->balance_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for trusting {{ $company->company_name }} with your vehicle!</p>
            <p style="margin-top: 2px;">This is a computer generated invoice and does not require a physical signature.</p>
        </div>
    </div>

    @if(request('autoprint'))
        <script>
            window.addEventListener('load', function() {
                window.print();
            });
        </script>
    @endif
</body>
</html>
