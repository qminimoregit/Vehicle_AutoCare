<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill_{{ $directSale->bill_number }}</title>
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

        .btn-email {
            background-color: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        .btn-email:hover {
            background-color: #dbeafe;
        }

        .btn-whatsapp {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .btn-whatsapp:hover {
            background-color: #d1fae5;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.55);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            padding: 16px;
        }
        .modal-overlay.active {
            display: flex;
        }
        .modal-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            max-width: 440px;
            width: 100%;
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
            text-align: left;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        .modal-title {
            font-size: 14px;
            font-weight: 800;
            color: #0B1849;
        }
        .modal-close {
            background: none;
            border: none;
            font-size: 18px;
            color: #9ca3af;
            cursor: pointer;
            line-height: 1;
        }
        .modal-close:hover {
            color: #111827;
        }
        .form-group {
            margin-bottom: 12px;
        }
        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .form-input {
            width: 100%;
            padding: 8px 10px;
            font-size: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            box-sizing: border-box;
        }
        .form-textarea {
            width: 100%;
            padding: 8px 10px;
            font-size: 11px;
            font-family: monospace;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            box-sizing: border-box;
            resize: vertical;
        }
        .alert-box {
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .alert-success {
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }
        .alert-error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
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

        /* 60–65mm centered logo for 80mm thermal paper */
        .logo-img {
            width: 62mm;
            max-width: 65mm;
            height: auto;
            margin: 0 auto 6px auto;
            display: block;
            object-fit: contain;
            filter: grayscale(100%) contrast(150%);
            -webkit-filter: grayscale(100%) contrast(150%);
        }

        .company-name {
            font-size: 13.5px;
            font-weight: 900;
            letter-spacing: 0.2px;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .branch-name {
            font-size: 11.5px;
            font-weight: 800;
            letter-spacing: 0.5px;
            margin-top: 2px;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .branch-meta {
            font-size: 10px;
            color: #111111;
            margin-top: 3px;
            line-height: 1.3;
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
            width: 32%;
        }

        .meta-val {
            font-weight: 700;
            text-align: right;
            width: 68%;
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

        .status-badge {
            display: inline-block;
            font-weight: 900;
            font-size: 10.5px;
            padding: 2px 6px;
            border: 1px solid #000000;
            margin-top: 5px;
            text-transform: uppercase;
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

            tr, .meta-row, .signature-block, .company-header, .footer-note {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .logo-img {
                width: 62mm !important;
                max-width: 65mm !important;
                height: auto !important;
                margin: 0 auto 2mm auto !important;
                display: block !important;
                filter: grayscale(100%) contrast(150%) !important;
                -webkit-filter: grayscale(100%) contrast(150%) !important;
            }

            .company-name {
                font-size: 13.5px !important;
                font-weight: 900 !important;
                letter-spacing: 0.2px !important;
                line-height: 1.2 !important;
            }

            .branch-name {
                font-size: 11.5px !important;
                font-weight: 800 !important;
                letter-spacing: 0.5px !important;
                margin-top: 1.5px !important;
                line-height: 1.2 !important;
            }

            .branch-meta {
                font-size: 10px !important;
                color: #000000 !important;
                margin-top: 2.5px !important;
                line-height: 1.25 !important;
            }

            .signature-img {
                filter: grayscale(100%) contrast(150%) !important;
                -webkit-filter: grayscale(100%) contrast(150%) !important;
            }
        }
    </style>
</head>
<body>

    <!-- No-Print Toolbar -->
    <div class="no-print-bar">
        @if(session('success'))
            <div class="alert-box alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert-box alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 6px;">
            <a href="{{ route('operations.direct-sales.index') }}" class="btn btn-secondary">
                &larr; POS Terminal
            </a>
            <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
                @if($directSale->invoice)
                    <a href="{{ route('operations.invoices.show', $directSale->invoice) }}" class="btn btn-secondary" style="color: #0B1849;">
                        View Invoice
                    </a>
                @elseif(Auth::user()?->isAdmin() || Auth::user()?->hasPermission('create_invoices'))
                    <form action="{{ route('operations.direct-sales.generate-invoice', $directSale) }}" method="POST" onsubmit="return confirm('Generate an official tax/commercial Invoice for this Direct Sale?');" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary" title="Convert to official invoice upon request">
                            Generate Invoice
                        </button>
                    </form>
                @endif
                <a href="{{ route('operations.direct-sales.pdf', $directSale) }}" class="btn btn-secondary" download title="Download Sales Bill PDF">
                    PDF
                </a>
                <button onclick="handlePrint()" class="btn btn-primary" id="btn-print-bill">
                    Print Bill
                </button>
            </div>
        </div>

        <div style="display: flex; gap: 6px; border-top: 1px solid #f3f4f6; padding-top: 8px;">
            <button type="button" onclick="openEmailModal()" class="btn btn-email" style="flex: 1; justify-content: center;">
                &#9993; Email Bill
            </button>
            <button type="button" onclick="openWhatsAppModal()" class="btn btn-whatsapp" style="flex: 1; justify-content: center;">
                &#128172; WhatsApp
            </button>
        </div>
    </div>

    <!-- 80mm Thermal Sales Bill -->
    <div class="thermal-receipt">
        
        <!-- Company Logo & Details (Deduplicated Header) -->
        <x-thermal-header :company="$company" :branch="$directSale->branch" />

        <div class="divider"></div>

        <!-- Document Title: SALES BILL (NOT INVOICE) -->
        <div class="receipt-title" data-doc-type="SALES BILL">
            SALES BILL
        </div>

        <div class="divider"></div>

        <!-- Bill Number, Date/Time, Cashier & Customer Info -->
        <div class="meta-row">
            <span class="meta-label">Bill No:</span>
            <span class="meta-val font-black">{{ $directSale->bill_number }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Date:</span>
            <span class="meta-val">{{ $directSale->sale_date ? \Carbon\Carbon::parse($directSale->sale_date)->format('d/m/Y') : ($directSale->created_at ? $directSale->created_at->setTimezone('Asia/Colombo')->format('d/m/Y') : \Carbon\Carbon::now('Asia/Colombo')->format('d/m/Y')) }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Time:</span>
            <span class="meta-val">{{ $directSale->created_at ? $directSale->created_at->setTimezone('Asia/Colombo')->format('h:i A') : \Carbon\Carbon::now('Asia/Colombo')->format('h:i A') }}</span>
        </div>

        <div class="meta-row">
            <span class="meta-label">Cashier:</span>
            <span class="meta-val">{{ $directSale->creator?->name ?? (Auth::user()?->name ?? 'Cashier') }}</span>
        </div>

        @php
            $custName = $directSale->customer?->customer_name ?? 'Walk-in Customer';
            $custPhone = $directSale->customer?->phone ?? null;
        @endphp

        <div class="meta-row">
            <span class="meta-label">Customer:</span>
            <span class="meta-val font-bold">{{ $custName }}</span>
        </div>

        @if($custPhone && $custPhone !== '0000000000')
            <div class="meta-row">
                <span class="meta-label">Phone:</span>
                <span class="meta-val">{{ $custPhone }}</span>
            </div>
        @endif

        @if($directSale->notes)
            <div class="meta-row">
                <span class="meta-label">Ref/Notes:</span>
                <span class="meta-val">{{ $directSale->notes }}</span>
            </div>
        @endif

        <div class="divider"></div>

        <!-- Sold Products & Quantities -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-desc">Description</th>
                    <th class="col-qty">Qty</th>
                    <th class="col-price">Price</th>
                    <th class="col-amt">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($directSale->items as $item)
                    <tr>
                        <td class="col-desc">
                            {{ $item->product_name }}
                        </td>
                        <td class="col-qty">{{ (float)$item->quantity == (int)$item->quantity ? (int)$item->quantity : number_format($item->quantity, 2) }}</td>
                        <td class="col-price">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="col-amt">{{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- Subtotal, Discount/Tax, Grand Total, Paid, Balance -->
        <table class="totals-table">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right font-bold">{{ number_format($directSale->subtotal, 2) }}</td>
            </tr>
            @if((float)$directSale->discount_amount > 0)
                <tr>
                    <td>Discount:</td>
                    <td class="text-right font-bold">-{{ number_format($directSale->discount_amount, 2) }}</td>
                </tr>
            @endif
            @if((float)$directSale->tax_amount > 0)
                <tr>
                    <td>Tax:</td>
                    <td class="text-right font-bold">+{{ number_format($directSale->tax_amount, 2) }}</td>
                </tr>
            @endif
            <tr class="total-highlight">
                <td>GRAND TOTAL:</td>
                <td class="text-right">LKR {{ number_format($directSale->grand_total, 2) }}</td>
            </tr>
            <tr>
                <td style="padding-top: 4px;">Payment Method:</td>
                <td class="text-right font-bold uppercase" style="padding-top: 4px;">{{ str_replace('_', ' ', $directSale->payment_method ?? 'cash') }}</td>
            </tr>
            <tr>
                <td>Paid Amount:</td>
                <td class="text-right font-bold">LKR {{ number_format($directSale->amount_paid, 2) }}</td>
            </tr>
            @if((float)$directSale->change_amount > 0)
                <tr>
                    <td class="font-bold">Change Returned:</td>
                    <td class="text-right font-bold">LKR {{ number_format($directSale->change_amount, 2) }}</td>
                </tr>
            @endif
            @php
                $balanceAmount = max(0, (float)$directSale->grand_total - (float)$directSale->amount_paid);
            @endphp
            <tr>
                <td class="font-black">Outstanding Balance:</td>
                <td class="text-right font-black">LKR {{ number_format($balanceAmount, 2) }}</td>
            </tr>
        </table>

        <!-- Payment Status Badge -->
        <div class="text-center" style="margin-top: 6px;">
            @php
                $statusLabel = 'PARTIALLY PAID';
                if ($directSale->status === 'cancelled') {
                    $statusLabel = 'CANCELLED';
                } elseif ($balanceAmount <= 0.0001 || (float)$directSale->amount_paid >= (float)$directSale->grand_total) {
                    $statusLabel = 'PAID IN FULL';
                } elseif ($directSale->payment_method === 'credit') {
                    $customerCredit = $directSale->customer?->creditAccount;
                    if ($customerCredit && $customerCredit->isActive()) {
                        $statusLabel = 'APPROVED COMPANY CREDIT';
                    } else {
                        $statusLabel = 'CREDIT SALE';
                    }
                } elseif ((float)$directSale->amount_paid <= 0) {
                    $statusLabel = 'UNPAID';
                }
            @endphp
            <div class="status-badge">Status: {{ $statusLabel }}</div>
        </div>

        <div class="divider"></div>

        <!-- Authorized Signature -->
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

        <!-- Thank-you message -->
        <div class="footer-note">
            <div class="font-bold">Thank you for your business!</div>
            <div class="font-bold uppercase" style="margin-top: 2px;">{{ strtoupper($company->company_name ?? 'VIRAN AUTO CARE') }}</div>
        </div>

        <div class="barcode-area">
            *{{ $directSale->bill_number }}*
        </div>
    </div>

    <!-- Email Bill Modal -->
    <div id="emailBillModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <span class="modal-title">&#9993; Email Sales Bill (PDF Attached)</span>
                <button type="button" class="modal-close" onclick="closeEmailModal()">&times;</button>
            </div>
            <form action="{{ route('operations.direct-sales.email', $directSale) }}" method="POST" id="formEmailBill">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="billEmailInput">Recipient Email Address:</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="billEmailInput" 
                        required 
                        value="{{ $directSale->customer?->email ?? '' }}"
                        placeholder="customer@example.com"
                        class="form-input"
                    >
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
                    <button type="button" onclick="closeEmailModal()" class="btn btn-secondary">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSendEmailSubmit">
                        Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- WhatsApp Share Modal -->
    <div id="whatsAppModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <span class="modal-title">&#128172; Share via WhatsApp (Manual)</span>
                <button type="button" class="modal-close" onclick="closeWhatsAppModal()">&times;</button>
            </div>
            @php
                $rawPhone = $directSale->customer?->phone && $directSale->customer->phone !== '0000000000' ? $directSale->customer->phone : '';
                $normPhone = \App\Services\PhoneNumberHelper::normalizeForWhatsApp($rawPhone) ?? '';
                $waText = $directSale->getWhatsAppMessage($company);
            @endphp
            <div class="form-group">
                <label class="form-label" for="waPhoneInput">Customer Mobile Number:</label>
                <input 
                    type="text" 
                    id="waPhoneInput" 
                    value="{{ $rawPhone }}"
                    placeholder="e.g. 077 123 4567"
                    class="form-input"
                    oninput="updateWhatsAppPreview()"
                >
                <p id="waNormIndicator" style="font-size: 10.5px; color: #059669; margin-top: 3px; font-weight: 600;">
                    @if($normPhone)
                        &bull; Normalized: +{{ $normPhone }} (Ready to open WhatsApp)
                    @else
                        &bull; Enter standard 10-digit Sri Lankan number (e.g. 077 123 4567)
                    @endif
                </p>
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <span class="form-label" style="margin-bottom: 0;">Message Preview:</span>
                    <button type="button" onclick="copyWhatsAppMessage()" id="btnCopyWa" style="background: none; border: none; font-size: 11px; color: #059669; font-weight: 700; cursor: pointer; text-decoration: underline;">
                        Copy Text
                    </button>
                </div>
                <textarea id="waMessageText" rows="6" class="form-textarea" readonly>{{ $waText }}</textarea>
            </div>

            <p style="font-size: 10px; color: #6b7280; font-style: italic; margin-bottom: 12px;">
                Note: WhatsApp click-to-chat opens WhatsApp with recipient & prefilled message. Cashier manually presses Send.
            </p>

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; border-top: 1px solid #e5e7eb; padding-top: 12px;">
                <a href="{{ route('operations.direct-sales.pdf', $directSale) }}" class="btn btn-secondary" download title="Download PDF to manually attach in WhatsApp">
                    Download Bill PDF
                </a>
                <div style="display: flex; gap: 6px;">
                    <button type="button" onclick="closeWhatsAppModal()" class="btn btn-secondary">
                        Close
                    </button>
                    <button type="button" onclick="openWhatsAppWeb()" class="btn btn-whatsapp" style="font-weight: 800;">
                        Open in WhatsApp &rarr;
                    </button>
                </div>
            </div>
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
                    title: 'Bill_{{ $directSale->bill_number }}',
                    documentId: {{ $directSale->id }},
                    documentNumber: '{{ $directSale->bill_number }}',
                    fallbackUrl: window.location.href
                });
            } else {
                window.print();
            }
        }

        function openEmailModal() {
            document.getElementById('emailBillModal').classList.add('active');
        }
        function closeEmailModal() {
            document.getElementById('emailBillModal').classList.remove('active');
        }
        function openWhatsAppModal() {
            document.getElementById('whatsAppModal').classList.add('active');
            updateWhatsAppPreview();
        }
        function closeWhatsAppModal() {
            document.getElementById('whatsAppModal').classList.remove('active');
        }

        function normalizeSLPhone(phone) {
            if (!phone) return '';
            let clean = phone.toString().replace(/[\s\-\(\)\.]/g, '').trim();
            if (clean.startsWith('+')) clean = clean.substring(1);
            if (clean.startsWith('00')) clean = clean.substring(2);
            if (/^0([1-9][0-9]{8})$/.test(clean)) return '94' + clean.substring(1);
            if (/^([1-9][0-9]{8})$/.test(clean)) return '94' + clean;
            if (/^94[0-9]{9}$/.test(clean)) return clean;
            let digits = clean.replace(/\D/g, '');
            return digits || '';
        }

        function updateWhatsAppPreview() {
            let phone = document.getElementById('waPhoneInput').value;
            let norm = normalizeSLPhone(phone);
            let indicator = document.getElementById('waNormIndicator');
            if (norm && norm.length >= 9) {
                indicator.innerHTML = '&bull; Normalized: +' + norm + ' (Ready to open WhatsApp)';
                indicator.style.color = '#059669';
            } else {
                indicator.innerHTML = '&bull; Enter standard 10-digit Sri Lankan number (e.g. 077 123 4567)';
                indicator.style.color = '#b45309';
            }
        }

        function copyWhatsAppMessage() {
            let msg = document.getElementById('waMessageText').value;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(msg).then(() => {
                    let btn = document.getElementById('btnCopyWa');
                    btn.innerText = 'Copied!';
                    setTimeout(() => { btn.innerText = 'Copy Text'; }, 2000);
                });
            }
        }

        function openWhatsAppWeb() {
            let phone = document.getElementById('waPhoneInput').value;
            let norm = normalizeSLPhone(phone);
            if (!norm || norm.length < 9) {
                alert('Please enter a valid customer mobile number (e.g. 077 123 4567).');
                return;
            }
            let msg = document.getElementById('waMessageText').value;
            let url = 'https://wa.me/' + encodeURIComponent(norm) + '?text=' + encodeURIComponent(msg);
            window.open(url, '_blank');
        }

        document.getElementById('formEmailBill')?.addEventListener('submit', function() {
            let btn = document.getElementById('btnSendEmailSubmit');
            if (btn) {
                btn.disabled = true;
                btn.innerText = 'Sending PDF Bill...';
            }
        });
    </script>
</body>
</html>
