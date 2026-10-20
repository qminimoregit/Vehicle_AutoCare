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

        .invoice-card {
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
            color: #1f2937;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            margin-bottom: 30px;
        }

        .totals-left {
            flex: 1;
        }

        .totals-table {
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

        .grand-total-row {
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
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('operations.invoices.pdf', $invoice) }}" class="btn btn-secondary">
                Download PDF
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                Print Invoice
            </button>
        </div>
    </div>

    <div class="invoice-card">
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
                        @if($company->email) • Email: {{ $company->email }} @endif
                    </div>
                    @if($company->tax_number)
                        <div>Tax/VAT Reg: {{ $company->tax_number }}</div>
                    @endif
                    @if($invoice->branch)
                        <div>Branch: <strong>{{ $invoice->branch->branch_name }}</strong></div>
                    @endif
                </div>
            </div>

            <div class="invoice-title">
                <h1>INVOICE</h1>
                <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
                <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">
                    Date: {{ $invoice->invoice_date ? $invoice->invoice_date->format('M d, Y') : '' }}
                </div>
                @if($invoice->due_date)
                    <div style="font-size: 11px; color: #6b7280;">
                        Due: {{ $invoice->due_date->format('M d, Y') }}
                    </div>
                @endif
                @if($invoice->reference)
                    <div style="font-size: 11px; color: #6b7280;">
                        Ref: {{ $invoice->reference }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Meta Grid -->
        <div class="meta-grid">
            <div>
                <div class="meta-label">Customer Information</div>
                <div class="meta-value">{{ $invoice->customer->customer_name }}</div>
                @if($invoice->customer->customer_type === 'company' && $invoice->customer->company_name)
                    <div style="font-size: 12px; font-weight: 600; color: #1e3a8a; margin-top: 2px;">
                        Company: {{ $invoice->customer->company_name }}
                    </div>
                @endif
                <div style="font-size: 12px; color: #4b5563; margin-top: 2px;">
                    @if($invoice->customer->phone)
                        <div>Phone: {{ $invoice->customer->phone }}</div>
                    @endif
                    @if($invoice->customer->address)
                        <div>{{ $invoice->customer->address }}</div>
                    @endif
                </div>
            </div>

            <div style="text-align: right;">
                @if($invoice->jobCard && $invoice->jobCard->vehicle)
                    <div class="meta-label">Vehicle & Service Job</div>
                    <div class="meta-value" style="letter-spacing: 1px;">{{ $invoice->jobCard->vehicle->vehicle_number }}</div>
                    <div style="font-size: 12px; color: #4b5563; margin-top: 2px;">
                        @if($invoice->jobCard->vehicleType)
                            {{ $invoice->jobCard->vehicleType->name }}
                        @endif
                        @if($invoice->jobCard->current_mileage > 0)
                            • {{ number_format($invoice->jobCard->current_mileage) }} km
                        @endif
                    </div>
                    <div>Linked Job Card: <strong>#{{ $invoice->jobCard->job_number }}</strong></div>
                @elseif($invoice->quotation && $invoice->quotation->vehicle)
                    <div class="meta-label">Vehicle Details</div>
                    <div class="meta-value" style="letter-spacing: 1px;">{{ $invoice->quotation->vehicle->vehicle_number }}</div>
                    <div style="font-size: 12px; color: #4b5563; margin-top: 2px;">
                        Quotation: <strong>#{{ $invoice->quotation->quotation_number }}</strong>
                    </div>
                @elseif($invoice->invoice_type === 'direct_sale')
                    <div class="meta-label">Transaction Type</div>
                    <div class="meta-value">Direct Sale (Over-The-Counter)</div>
                    @if($invoice->reference)
                        <div style="font-size: 12px; color: #4b5563;">Ref: {{ $invoice->reference }}</div>
                    @endif
                @else
                    <div class="meta-label">Transaction Type</div>
                    <div class="meta-value">Direct Invoice</div>
                @endif

                @if($invoice->subject)
                    <div style="margin-top: 8px; padding-top: 6px; border-top: 1px dashed #e5e7eb;">
                        <span class="meta-label">Subject</span>
                        <div style="font-weight: 600; color: #1f2937;">{{ $invoice->subject }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Table of Items -->
        <table>
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">#</th>
                    <th style="text-align: left;">Item Description</th>
                    <th style="width: 100px; text-align: left;">Type</th>
                    <th class="text-right" style="width: 120px;">Unit Price</th>
                    <th class="text-center" style="width: 60px;">Qty</th>
                    <th class="text-right" style="width: 130px;">Total (LKR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                    <tr>
                        <td style="color: #9ca3af; font-size: 11px; text-align: center;">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->item_name }}</strong>
                            @if($item->item_code)
                                <span style="font-size: 10px; color: #6b7280; font-family: monospace; display: block;">{{ $item->item_code }}</span>
                            @endif
                            @if($item->description)
                                <div style="font-size: 11px; color: #6b7280; margin-top: 1px;">{{ $item->description }}</div>
                            @endif
                        </td>
                        <td style="text-transform: capitalize; font-size: 11px; color: #4b5563;">
                            {{ $item->item_type }}
                        </td>
                        <td class="text-right">LKR {{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-center">@quantity($item->quantity)</td>
                        <td class="text-right" style="font-weight: 700;">LKR {{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals & Summary -->
        <div class="totals-section">
            <div class="totals-left">
                @if(!empty($amountInWords))
                    <div class="words-box">
                        <strong>Amount in Words:</strong><br>
                        {{ $amountInWords }}
                    </div>
                @endif

                @if($invoice->notes)
                    <div class="notes-card">
                        <strong style="color: #374151; display: block; margin-bottom: 2px;">Notes:</strong>
                        {{ $invoice->notes }}
                    </div>
                @endif

                @if($invoice->terms_conditions)
                    <div class="notes-card">
                        <strong style="color: #374151; display: block; margin-bottom: 2px;">Terms & Conditions:</strong>
                        {{ $invoice->terms_conditions }}
                    </div>
                @endif
            </div>

            <div class="totals-table">
                <div class="summary-row">
                    <span style="color: #6b7280;">Subtotal:</span>
                    <span style="font-weight: 600;">LKR {{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                @if($invoice->discount_amount > 0)
                    <div class="summary-row" style="color: #059669;">
                        <span>Discount:</span>
                        <span style="font-weight: 600;">- LKR {{ number_format($invoice->discount_amount, 2) }}</span>
                    </div>
                @endif
                @if($invoice->tax_amount > 0)
                    <div class="summary-row">
                        <span style="color: #6b7280;">Tax:</span>
                        <span style="font-weight: 600;">LKR {{ number_format($invoice->tax_amount, 2) }}</span>
                    </div>
                @endif
                @if($invoice->additional_charges > 0)
                    <div class="summary-row">
                        <span style="color: #6b7280;">Additional Charges:</span>
                        <span style="font-weight: 600;">LKR {{ number_format($invoice->additional_charges, 2) }}</span>
                    </div>
                @endif
                <div class="summary-row grand-total-row">
                    <span>Grand Total:</span>
                    <span>LKR {{ number_format($invoice->grand_total, 2) }}</span>
                </div>
                <div class="summary-row" style="color: #059669; font-weight: 600; margin-top: 6px;">
                    <span>Amount Paid:</span>
                    <span>LKR {{ number_format($invoice->amount_paid, 2) }}</span>
                </div>
                <div class="summary-row" style="font-weight: 700; color: {{ $invoice->balance_amount > 0 ? '#dc2626' : '#6b7280' }};">
                    <span>Balance Due:</span>
                    <span>LKR {{ number_format($invoice->balance_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Payments Log (if any payments recorded) -->
        @if($invoice->payments->count() > 0)
            <div style="margin-bottom: 24px;">
                <h4 style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #6b7280; letter-spacing: 0.5px; margin-bottom: 8px;">Recorded Payments</h4>
                <table>
                    <thead>
                        <tr>
                            <th style="text-align: left; padding: 6px 8px; font-size: 10px;">Payment #</th>
                            <th style="text-align: left; padding: 6px 8px; font-size: 10px;">Date</th>
                            <th style="text-align: left; padding: 6px 8px; font-size: 10px;">Method</th>
                            <th style="text-align: left; padding: 6px 8px; font-size: 10px;">Reference</th>
                            <th class="text-right" style="padding: 6px 8px; font-size: 10px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->payments as $pay)
                            <tr>
                                <td style="padding: 8px; font-weight: 600;">{{ $pay->payment_number }}</td>
                                <td style="padding: 8px; color: #4b5563;">{{ $pay->payment_date ? $pay->payment_date->format('M d, Y') : '' }}</td>
                                <td style="padding: 8px; text-transform: uppercase; font-size: 11px;">{{ str_replace('_', ' ', $pay->payment_method) }}</td>
                                <td style="padding: 8px; color: #4b5563;">{{ $pay->reference_number ?? '—' }}</td>
                                <td class="text-right" style="padding: 8px; font-weight: 700; color: #059669;">LKR {{ number_format($pay->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

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
