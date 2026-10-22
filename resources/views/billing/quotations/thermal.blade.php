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
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Courier New", Courier, monospace;
        }

        body {
            background-color: #f3f4f6;
            color: #000000;
            padding: 20px 10px;
            font-size: 11px;
            line-height: 1.35;
        }

        /* Toolbar (hidden on print) */
        .no-print-bar {
            width: 72mm;
            max-width: 76mm;
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

        /* 80mm Thermal Receipt Sheet - Usable content width ~72mm */
        .thermal-receipt {
            width: 72mm;
            max-width: 76mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 10px 4px;
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
            margin-bottom: 6px;
        }

        .logo-img {
            max-height: 48px;
            max-width: 140px;
            width: auto;
            height: auto;
            margin: 0 auto 5px auto;
            display: block;
            object-fit: contain;
            filter: grayscale(100%) contrast(150%);
            -webkit-filter: grayscale(100%) contrast(150%);
        }

        .company-name {
            font-size: 13px;
            font-weight: 900;
            letter-spacing: -0.2px;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .branch-meta {
            font-size: 10px;
            color: #111111;
            margin-top: 2px;
            line-height: 1.25;
        }

        .divider {
            border-top: 1px dashed #000000;
            margin: 7px 0;
        }

        .receipt-title {
            font-size: 13px;
            font-weight: 900;
            text-align: center;
            letter-spacing: 0.5px;
            padding: 2px 0;
            text-transform: uppercase;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 10.5px;
            margin-bottom: 2px;
        }

        .meta-label {
            font-weight: 600;
            color: #111;
            width: 35%;
        }

        .meta-val {
            font-weight: 700;
            text-align: right;
            width: 65%;
            word-break: break-word;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin: 5px 0;
        }

        .items-table th {
            text-align: left;
            border-bottom: 1px dashed #000000;
            padding: 3px 0;
            font-weight: 800;
            font-size: 9.5px;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .col-desc {
            width: 44%;
            word-break: break-word;
            padding-right: 4px;
            text-align: left;
        }

        .col-qty {
            width: 14%;
            text-align: left;
            font-weight: 700;
        }

        .col-price {
            width: 20%;
            text-align: right;
            white-space: nowrap;
        }

        .col-amt {
            width: 22%;
            text-align: right;
            font-weight: 700;
            white-space: nowrap;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 3px;
        }

        .totals-table td {
            padding: 2px 0;
        }

        .totals-table .total-highlight td {
            font-size: 13px;
            font-weight: 900;
            padding: 4px 0;
            border-top: 1px solid #000000;
            border-bottom: 1px solid #000000;
        }

        .notes-block {
            font-size: 9.5px;
            margin-top: 4px;
            line-height: 1.3;
            word-break: break-word;
        }

        /* Authorized Signature */
        .signature-block {
            text-align: center;
            margin: 10px auto 4px auto;
            width: 100%;
        }

        .signature-img-wrap {
            height: 38px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            margin-bottom: 2px;
        }

        .signature-img {
            max-height: 36px;
            max-width: 140px;
            width: auto;
            height: auto;
            object-fit: contain;
            filter: grayscale(100%) contrast(150%);
            -webkit-filter: grayscale(100%) contrast(150%);
        }

        .signature-blank-space {
            height: 30px;
        }

        .signature-rule {
            font-size: 10px;
            line-height: 1;
            letter-spacing: -0.5px;
            overflow: hidden;
            white-space: nowrap;
            color: #000000;
        }

        .signature-text {
            font-size: 10px;
            font-weight: 700;
            padding: 3px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .footer-note {
            text-align: center;
            font-size: 10.5px;
            margin-top: 8px;
            line-height: 1.35;
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
            margin: 0mm;
        }

        @media print {
            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 80mm !important;
                max-width: 80mm !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print-bar {
                display: none !important;
            }

            .thermal-receipt {
                width: 72mm !important;
                max-width: 76mm !important;
                margin: 0 auto !important;
                padding: 2mm 1mm !important;
                box-shadow: none !important;
                border: none !important;
                page-break-inside: auto !important;
            }

            tr, .meta-row, .signature-block, .company-header, .footer-note, .notes-block {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .logo-img, .signature-img {
                filter: grayscale(100%) contrast(150%) !important;
                -webkit-filter: grayscale(100%) contrast(150%) !important;
            }
        }
    </style>
</head>
<body>

    <!-- No-Print Toolbar -->
    <div class="no-print-bar">
        <a href="{{ route('billing.quotations.show', $quotation) }}" class="btn btn-secondary">
            &larr; Back
        </a>
        <div style="display: flex; gap: 6px;">
            <button onclick="handlePrint()" class="btn btn-primary" id="btn-print-quotation">
                Print Quotation
            </button>
        </div>
    </div>

    <!-- 80mm Thermal Quotation Receipt -->
    <div class="thermal-receipt">
        
        <!-- 1. Company Logo & 2. Company Name and Details -->
        <div class="company-header">
            @php
                $hasLogo = !empty($company->logo_path) && (file_exists(public_path('storage/' . $company->logo_path)) || \Illuminate\Support\Facades\Storage::disk('public')->exists($company->logo_path));
            @endphp
            @if($hasLogo)
                <img src="{{ asset('storage/' . $company->logo_path) }}" alt="{{ $company->company_name ?? 'Company Logo' }}" class="logo-img">
            @endif
            <div class="company-name">{{ strtoupper($company->company_name ?? 'VIRAN AUTO CARE') }}</div>
            @if($quotation->branch?->address || $company->address)
                <div class="branch-meta">{{ $quotation->branch?->address ?? $company->address }}</div>
            @endif
            @if($quotation->branch?->phone || $company->phone)
                <div class="branch-meta">Tel: {{ $quotation->branch?->phone ?? $company->phone }}</div>
            @endif
        </div>

        <div class="divider"></div>

        <!-- 3. QUOTATION heading -->
        <div class="receipt-title">
            QUOTATION
        </div>

        <div class="divider"></div>

        <!-- 4. Quotation number & 5. Date & 6. Valid until -->
        <div class="meta-row">
            <span class="meta-label">Quotation No:</span>
            <span class="meta-val font-black">#{{ $quotation->quotation_number }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Date:</span>
            <span class="meta-val">{{ $quotation->quotation_date ? \Carbon\Carbon::parse($quotation->quotation_date)->format('d/m/Y') : now()->format('d/m/Y') }}</span>
        </div>

        @if($quotation->expiry_date)
            <div class="meta-row">
                <span class="meta-label">Valid Until:</span>
                <span class="meta-val">{{ \Carbon\Carbon::parse($quotation->expiry_date)->format('d/m/Y') }}</span>
            </div>
        @else
            <div class="meta-row">
                <span class="meta-label">Valid Until:</span>
                <span class="meta-val">14 Days</span>
            </div>
        @endif

        @if($quotation->creator)
            <div class="meta-row">
                <span class="meta-label">Prepared By:</span>
                <span class="meta-val">{{ $quotation->creator->name }}</span>
            </div>
        @endif

        <!-- 7. Customer details -->
        @php
            $custName = $quotation->customer?->customer_name ?? ($quotation->customer_snapshot['name'] ?? 'Customer');
            $custPhone = $quotation->customer?->phone ?? ($quotation->customer_snapshot['phone'] ?? null);
            $vehNumber = $quotation->vehicle?->vehicle_number ?? ($quotation->vehicle_snapshot['vehicle_number'] ?? null);
            $vehModel = $quotation->vehicle?->make_model ?? ($quotation->vehicle_snapshot['make_model'] ?? null);
        @endphp

        <div class="meta-row">
            <span class="meta-label">Customer:</span>
            <span class="meta-val font-bold">{{ $custName }}</span>
        </div>

        @if($custPhone)
            <div class="meta-row">
                <span class="meta-label">Phone:</span>
                <span class="meta-val">{{ $custPhone }}</span>
            </div>
        @endif

        <!-- 8. Vehicle details -->
        @if($vehNumber)
            <div class="meta-row">
                <span class="meta-label">Vehicle:</span>
                <span class="meta-val font-black uppercase" style="font-size: 11.5px;">{{ $vehNumber }}</span>
            </div>
        @endif

        @if($vehModel)
            <div class="meta-row">
                <span class="meta-label">Model:</span>
                <span class="meta-val">{{ $vehModel }}</span>
            </div>
        @endif

        <div class="divider"></div>

        <!-- 9. Products and services & 10. Quantity & 11. Unit price & 12. Total -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-desc">Description</th>
                    <th class="col-qty">Qty</th>
                    <th class="col-price">Price</th>
                    <th class="col-amt">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $item)
                    <tr>
                        <td class="col-desc">
                            {{ $item->item_name }}
                            @if(!empty($item->description))
                                <div style="font-size: 8.5px; color: #444;">{{ $item->description }}</div>
                            @endif
                        </td>
                        <td class="col-qty">{{ (float)$item->quantity == (int)$item->quantity ? (int)$item->quantity : number_format($item->quantity, 2) }}</td>
                        <td class="col-price">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="col-amt">{{ number_format($item->line_total ?? ($item->total ?? 0), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- 13. Discount and tax, if applicable & 14. Grand total -->
        <table class="totals-table">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right font-bold">{{ number_format($quotation->subtotal, 2) }}</td>
            </tr>
            @if((float)$quotation->discount_amount > 0)
                <tr>
                    <td>Discount:</td>
                    <td class="text-right font-bold">-{{ number_format($quotation->discount_amount, 2) }}</td>
                </tr>
            @endif
            @if((float)$quotation->tax_amount > 0)
                <tr>
                    <td>Tax:</td>
                    <td class="text-right font-bold">+{{ number_format($quotation->tax_amount, 2) }}</td>
                </tr>
            @endif
            @if((float)$quotation->additional_charges > 0)
                <tr>
                    <td>Additional Charges:</td>
                    <td class="text-right font-bold">+{{ number_format($quotation->additional_charges, 2) }}</td>
                </tr>
            @endif
            <tr class="total-highlight">
                <td>ESTIMATED TOTAL:</td>
                <td class="text-right">LKR {{ number_format($quotation->grand_total, 2) }}</td>
            </tr>
        </table>

        <!-- 15. Quotation notes -->
        @if(!empty($quotation->notes) || !empty($quotation->terms_conditions))
            <div class="divider"></div>
            <div class="notes-block">
                @if(!empty($quotation->notes))
                    <div style="margin-bottom: 2px;"><strong>Notes:</strong> {{ $quotation->notes }}</div>
                @endif
                @if(!empty($quotation->terms_conditions))
                    <div><strong>Terms:</strong> {{ $quotation->terms_conditions }}</div>
                @endif
            </div>
        @endif

        <div class="divider"></div>

        <!-- 16. Authorized Signature, if configured -->
        @php
            $hasSignature = !empty($company->signature_path) && (file_exists(public_path('storage/' . $company->signature_path)) || \Illuminate\Support\Facades\Storage::disk('public')->exists($company->signature_path));
        @endphp
        <div class="signature-block">
            @if($hasSignature)
                <div class="signature-img-wrap">
                    <img src="{{ asset('storage/' . $company->signature_path) }}" alt="Authorized Signature" class="signature-img">
                </div>
            @else
                <div class="signature-blank-space"></div>
            @endif
            <div class="signature-rule">---------------------------------------------</div>
            <div class="signature-text">Authorized Signature</div>
            <div class="signature-rule">---------------------------------------------</div>
        </div>

        <div class="divider"></div>

        <!-- 17. Thank-you message -->
        <div class="footer-note">
            <div class="font-bold">Thank you for your inquiry!</div>
            <div class="font-bold uppercase" style="margin-top: 2px;">{{ $company->company_name ?? 'VIRAN AUTO CARE' }}</div>
            <div style="font-size: 9px; margin-top: 2px; color: #333;">This estimate is valid for 14 days</div>
        </div>

        <div class="barcode-area">
            *{{ $quotation->quotation_number }}*
        </div>
    </div>

    <!-- Universal QZ Tray Printer Support -->
    <script src="{{ asset('vendor/qz/qz-tray.js') }}"></script>
    <script src="{{ asset('js/autocare-printer.js') }}"></script>
    <script>
        function handlePrint() {
            if (window.AutoCarePrinter && window.AutoCarePrinter.isConnected) {
                window.AutoCarePrinter.printDocument({
                    type: 'bill',
                    html: document.querySelector('.thermal-receipt').outerHTML,
                    title: 'Quotation_{{ $quotation->quotation_number }}',
                    documentId: {{ $quotation->id }},
                    documentNumber: '{{ $quotation->quotation_number }}',
                    fallbackUrl: window.location.href
                });
            } else {
                window.print();
            }
        }
    </script>
</body>
</html>
