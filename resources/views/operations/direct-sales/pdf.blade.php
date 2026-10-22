<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Bill - {{ $directSale->bill_number }}</title>
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
            margin-bottom: 16px;
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
            font-size: 22px;
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
            margin-bottom: 16px;
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
        .items-table th.text-right {
            text-align: right;
        }
        .items-table th.text-center {
            text-align: center;
        }
        .items-table td {
            padding: 7px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }
        .items-table td.text-right {
            text-align: right;
        }
        .items-table td.text-center {
            text-align: center;
        }
        .totals-table {
            width: 45%;
            margin-left: auto;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .totals-table td {
            padding: 4px 6px;
            font-size: 10px;
        }
        .totals-table td.label {
            text-align: right;
            color: #4b5563;
        }
        .totals-table td.value {
            text-align: right;
            font-weight: bold;
            color: #111827;
            width: 45%;
        }
        .grand-total-row td {
            font-size: 12px !important;
            font-weight: 900 !important;
            color: #0B1849 !important;
            border-top: 1.5px solid #0B1849;
            border-bottom: 1.5px solid #0B1849;
            padding: 6px !important;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-partial {
            background-color: #fef3c7;
            color: #92400e;
        }
        .footer {
            margin-top: 25px;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
        }
    </style>
</head>
<body>

    @php
        $companyName = $company->company_name ?? 'Viran Auto Care';
        $location = 'Mattegoda';
        if ($directSale->branch) {
            if (str_contains($directSale->branch->address ?? '', 'Mattegoda') || str_contains($directSale->branch->branch_name ?? '', 'Mattegoda')) {
                $location = 'Mattegoda';
            } else {
                $location = $directSale->branch->branch_name;
            }
        }
        $phone = $directSale->branch?->phone ?: ($company->phone ?: '077 620 80 59');
        $address = $directSale->branch?->address ?: ($company->address ?: 'No. 10/B, Mattegoda Road, Mattegoda 10240');
    @endphp

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <div class="company-name">{{ $companyName }}</div>
                <div class="company-meta">
                    <strong>{{ $location }}</strong><br>
                    {{ $address }}<br>
                    Tel: {{ $phone }}
                    @if(!empty($company->email)) &bull; Email: {{ $company->email }} @endif
                </div>
            </td>
            <td style="width: 40%; vertical-align: top;">
                <div class="doc-title">SALES BILL</div>
                <div class="doc-meta">
                    <strong>Bill No:</strong> {{ $directSale->bill_number }}<br>
                    <strong>Sale Ref:</strong> {{ $directSale->sale_number }}<br>
                    <strong>Date:</strong> {{ $directSale->sale_date ? $directSale->sale_date->format('Y-m-d') : ($directSale->created_at ? $directSale->created_at->setTimezone('Asia/Colombo')->format('Y-m-d') : \Carbon\Carbon::now('Asia/Colombo')->format('Y-m-d')) }}<br>
                    <strong>Cashier:</strong> {{ $directSale->creator?->name ?? 'Cashier' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Customer Information -->
    <table class="info-table">
        <tr>
            <td class="info-td">
                <div class="info-heading">Customer Information</div>
                <div class="customer-name">
                    {{ $directSale->customer?->customer_name ?: 'Walk-in Customer' }}
                </div>
                <div style="font-size: 9.5px; color: #4b5563; margin-top: 3px;">
                    @if($directSale->customer?->phone && $directSale->customer->phone !== '0000000000')
                        <strong>Tel:</strong> {{ $directSale->customer->phone }}<br>
                    @endif
                    @if($directSale->customer?->email)
                        <strong>Email:</strong> {{ $directSale->customer->email }}<br>
                    @endif
                </div>
            </td>
            <td class="info-td">
                <div class="info-heading">Payment Information</div>
                <div style="font-size: 10px; color: #111827; margin-top: 2px;">
                    <strong>Method:</strong> {{ strtoupper($directSale->payment_method) }}<br>
                    <strong>Status:</strong>
                    @if($directSale->payment_status === 'Paid in Full')
                        <span class="status-badge status-paid">Paid in Full</span>
                    @else
                        <span class="status-badge status-partial">Partially Paid</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">#</th>
                <th style="width: 50%;">Item / Product Description</th>
                <th style="width: 12%;" class="text-center">Qty</th>
                <th style="width: 15%;" class="text-right">Unit Price (LKR)</th>
                <th style="width: 18%;" class="text-right">Total (LKR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($directSale->items as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $item->product_name }}</strong>
                        @if(!empty($item->product_sku))
                            <br><span style="font-size: 8.5px; color: #64748b;">SKU: {{ $item->product_sku }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        {{ rtrim(rtrim(number_format($item->quantity, 4), '0'), '.') }}
                    </td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals Table -->
    <table class="totals-table">
        <tr>
            <td class="label">Subtotal:</td>
            <td class="value">LKR {{ number_format($directSale->subtotal, 2) }}</td>
        </tr>
        @if((float) $directSale->discount_amount > 0)
            <tr>
                <td class="label">Discount:</td>
                <td class="value">- LKR {{ number_format($directSale->discount_amount, 2) }}</td>
            </tr>
        @endif
        @if((float) $directSale->tax_amount > 0)
            <tr>
                <td class="label">Tax:</td>
                <td class="value">+ LKR {{ number_format($directSale->tax_amount, 2) }}</td>
            </tr>
        @endif
        <tr class="grand-total-row">
            <td class="label" style="color: #0B1849;">Grand Total:</td>
            <td class="value" style="color: #0B1849;">LKR {{ number_format($directSale->grand_total, 2) }}</td>
        </tr>
        <tr>
            <td class="label" style="color: #059669; font-weight: bold;">Amount Paid:</td>
            <td class="value" style="color: #059669;">LKR {{ number_format($directSale->total_paid, 2) }}</td>
        </tr>
        <tr>
            <td class="label" style="font-weight: bold; {{ $directSale->balance > 0 ? 'color: #dc2626;' : '' }}">Remaining Balance:</td>
            <td class="value" style="{{ $directSale->balance > 0 ? 'color: #dc2626;' : '' }}">LKR {{ number_format($directSale->balance, 2) }}</td>
        </tr>
    </table>

    <div style="clear: both;"></div>

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for choosing {{ $companyName }}! This is an official Sales Bill for direct purchases.</p>
        <p style="margin-top: 3px;">{{ $companyName }} &bull; {{ $location }} &bull; Tel: {{ $phone }}</p>
    </div>

</body>
</html>
