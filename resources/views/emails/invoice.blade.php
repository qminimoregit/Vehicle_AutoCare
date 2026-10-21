<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px;">
        <h2 style="color: #0B1849; margin-top: 0;">{{ $company->company_name ?? 'AutoCare Service Center' }}</h2>
        <p>Dear {{ $invoice->customer?->customer_name ?? 'Valued Customer' }},</p>
        <p>Thank you for choosing our service center. Please find attached the official invoice for your recent service / purchase.</p>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0; background-color: #f9fafb; border-radius: 6px;">
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; font-weight: bold;">Invoice Number:</td>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb;">{{ $invoice->invoice_number }}</td>
            </tr>
            @if($invoice->jobCard?->vehicle)
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; font-weight: bold;">Vehicle Number:</td>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb;">{{ $invoice->jobCard->vehicle->vehicle_number }}</td>
            </tr>
            @endif
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; font-weight: bold;">Invoice Total:</td>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; font-weight: bold; color: #0B1849;">LKR {{ number_format($invoice->grand_total, 2) }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; font-weight: bold;">Amount Paid:</td>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; color: #059669;">LKR {{ number_format($invoice->amount_paid, 2) }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; font-weight: bold;">Remaining Balance:</td>
                <td style="padding: 10px; font-weight: bold; color: #dc2626;">LKR {{ number_format($invoice->balance_amount, 2) }}</td>
            </tr>
        </table>

        <p>A PDF copy of your invoice is attached to this email for your records.</p>
        
        <p style="margin-top: 30px; font-size: 13px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 15px;">
            {{ $company->company_name ?? 'AutoCare Service Center' }}<br>
            {{ $company->address ?? 'Colombo, Sri Lanka' }}<br>
            Tel: {{ $company->phone ?? '011-2345678' }}
        </p>
    </div>
</body>
</html>
