<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GRN - {{ $grn->grn_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 20px;
            font-size: 13px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0B1849;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #0B1849;
            letter-spacing: 1px;
            margin: 0;
        }
        .subtitle {
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }
        .meta-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .meta-col {
            width: 48%;
        }
        .meta-row {
            margin-bottom: 6px;
        }
        .meta-label {
            font-weight: bold;
            display: inline-block;
            width: 130px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 10px;
        }
        th {
            background-color: #f6f7f9;
            font-weight: bold;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row {
            font-weight: bold;
            background-color: #fdfdfd;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
            padding-top: 20px;
        }
        .sig-box {
            width: 30%;
            text-align: center;
            border-top: 1px dashed #777;
            padding-top: 5px;
            font-size: 11px;
        }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 14px; background: #0B1849; color: #fff; border: none; border-radius: 4px; cursor: pointer;">
            Print / Save PDF
        </button>
    </div>

    <div class="header">
        <h1 class="title">AUTOCARE SYSTEM - GOODS RECEIVED NOTE</h1>
        <div class="subtitle">Warehouse & Inventory Management Receiving Voucher</div>
    </div>

    <div class="meta-grid">
        <div class="meta-col">
            <div class="meta-row"><span class="meta-label">GRN Number:</span> <strong>{{ $grn->grn_number }}</strong></div>
            <div class="meta-row"><span class="meta-label">Supplier:</span> {{ $grn->supplier->name }}</div>
            <div class="meta-row"><span class="meta-label">Supplier Contact:</span> {{ $grn->supplier->mobile ?: 'N/A' }}</div>
            <div class="meta-row"><span class="meta-label">Address:</span> {{ $grn->supplier->address ?: '-' }}</div>
        </div>
        <div class="meta-col">
            <div class="meta-row"><span class="meta-label">Received Date:</span> {{ $grn->received_date->format('Y-m-d') }}</div>
            <div class="meta-row"><span class="meta-label">Supplier Invoice #:</span> {{ $grn->supplier_invoice_number ?: 'N/A' }}</div>
            <div class="meta-row"><span class="meta-label">Status:</span> {{ strtoupper($grn->status) }}</div>
            <div class="meta-row"><span class="meta-label">Prepared By:</span> {{ $grn->createdBy ? $grn->createdBy->name : 'Admin' }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>Product Description</th>
                <th class="text-center" style="width: 60px;">UOM</th>
                <th class="text-right" style="width: 80px;">Invoiced Qty</th>
                <th class="text-right" style="width: 70px;">Free Qty</th>
                <th class="text-right" style="width: 80px;">Total Physical</th>
                <th class="text-right" style="width: 90px;">Unit Price (LKR)</th>
                <th class="text-right" style="width: 100px;">Total (LKR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grn->items as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $item->product->name }}</strong>
                        @if($item->product->sku)
                            <div style="font-size: 11px; color: #666;">SKU: {{ $item->product->sku }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->product->uom ? $item->product->uom->name : '-' }}</td>
                    <td class="text-right">{{ number_format($item->purchase_quantity, 2) }}</td>
                    <td class="text-right">{{ $item->free_quantity > 0 ? number_format($item->free_quantity, 2) : '-' }}</td>
                    <td class="text-right"><strong>{{ number_format($item->total_physical_quantity, 2) }}</strong></td>
                    <td class="text-right">{{ number_format($item->purchase_unit_price, 2) }}</td>
                    <td class="text-right"><strong>{{ number_format($item->line_total, 2) }}</strong></td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="7" class="text-right" style="padding-top: 12px; padding-bottom: 12px;">
                    GRAND TOTAL (PAYABLE TO SUPPLIER):
                </td>
                <td class="text-right" style="padding-top: 12px; padding-bottom: 12px; font-size: 14px;">
                    LKR {{ number_format($grn->total_amount, 2) }}
                </td>
            </tr>
        </tbody>
    </table>

    @if($grn->notes)
        <div style="font-size: 12px; margin-bottom: 20px;">
            <strong>Remarks / Notes:</strong> {{ $grn->notes }}
        </div>
    @endif

    <div class="signatures">
        <div class="sig-box">
            Received By (Store Keeper)
        </div>
        <div class="sig-box">
            Inspected & Verified By
        </div>
        <div class="sig-box">
            Authorized Manager
        </div>
    </div>
</body>
</html>
