<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt_{{ $payment->payment_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333333;
            line-height: 1.4;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #0B1849;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #0B1849;
        }
        .company-details {
            font-size: 11px;
            color: #666666;
            margin-top: 3px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #0B1849;
            text-align: right;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            vertical-align: top;
            padding: 4px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .data-table th, .data-table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #f9fafb;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .amount-words {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 10px;
            margin: 15px 0;
            font-style: italic;
        }
        .summary-table {
            width: 50%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 5px 8px;
        }
        .footer {
            margin-top: 40px;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
            font-size: 10px;
            color: #888888;
            text-align: center;
        }
    </style>
</head>
<body>

    <table style="width: 100%; margin-bottom: 15px;">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <div class="company-name">{{ $company->company_name ?? 'AutoCare Service Center' }}</div>
                <div class="company-details">
                    {{ $payment->invoice->branch?->address ?? ($company->address ?? 'Colombo, Sri Lanka') }}<br>
                    Tel: {{ $payment->invoice->branch?->phone ?? ($company->phone ?? '011-2345678') }}
                </div>
            </td>
            <td style="width: 40%; vertical-align: top; text-align: right;">
                <div class="title">Official Receipt</div>
                <div style="font-weight: bold; margin-top: 4px;">{{ $payment->payment_number }}</div>
                <div style="color: #666; font-size: 11px;">Date: {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') : now()->format('Y-m-d') }}</div>
            </td>
        </tr>
    </table>

    <table class="info-table" style="background-color: #f9fafb; border: 1px solid #e5e7eb;">
        <tr>
            <td style="width: 50%; padding: 10px;">
                <strong>Received From:</strong><br>
                {{ $payment->customer?->customer_name ?? ($payment->invoice->customer?->customer_name ?? 'Walk-in Customer') }}<br>
                @if($payment->customer?->phone || $payment->invoice?->customer?->phone)
                    Phone: {{ $payment->customer->phone ?? $payment->invoice->customer->phone }}<br>
                @endif
            </td>
            <td style="width: 50%; padding: 10px; text-align: right;">
                <strong>Applied Against:</strong><br>
                Invoice #: {{ $payment->invoice->invoice_number }}<br>
                @if($payment->invoice->jobCard?->vehicle)
                    Vehicle No: <strong>{{ $payment->invoice->jobCard->vehicle->vehicle_number }}</strong><br>
                @endif
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>Payment Method</th>
                <th>Reference #</th>
                <th>Notes</th>
                <th class="text-right">Amount (LKR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-transform: uppercase;">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                <td>{{ $payment->reference_number ?? '-' }}</td>
                <td>{{ $payment->notes ?? 'Official Payment' }}</td>
                <td class="text-right" style="font-weight: bold; font-size: 13px;">{{ number_format($payment->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    @if(!empty($amountInWords))
        <div class="amount-words">
            <strong>Amount in words:</strong> {{ $amountInWords }}
        </div>
    @endif

    <table class="summary-table">
        <tr>
            <td><strong>Invoice Grand Total:</strong></td>
            <td class="text-right">LKR {{ number_format($payment->invoice->grand_total, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Total Paid to Date:</strong></td>
            <td class="text-right" style="color: #059669;">LKR {{ number_format($payment->invoice->amount_paid, 2) }}</td>
        </tr>
        <tr style="border-top: 1px solid #333;">
            <td><strong>Remaining Balance:</strong></td>
            <td class="text-right" style="font-weight: bold; color: #dc2626;">LKR {{ number_format($payment->invoice->balance_amount, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        Thank you for your business. AutoCare System • Computer Generated Payment Acknowledgment
    </div>

</body>
</html>
