<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px;">
        <h2 style="color: #0B1849; margin-top: 0;">{{ $company->company_name ?? 'AutoCare Service Center' }}</h2>
        <p>Dear {{ $payment->customer?->customer_name ?? ($payment->invoice->customer?->customer_name ?? 'Valued Customer') }},</p>
        <p>We acknowledge receipt of your payment. Thank you for your business.</p>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0; background-color: #f9fafb; border-radius: 6px;">
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; font-weight: bold;">Receipt Number:</td>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb;">{{ $payment->payment_number }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; font-weight: bold;">Invoice Number:</td>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb;">{{ $payment->invoice->invoice_number }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; font-weight: bold;">Payment Date:</td>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb;">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') : now()->format('Y-m-d') }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; font-weight: bold;">Payment Method:</td>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; text-transform: uppercase;">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; font-weight: bold;">Amount Paid:</td>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; font-weight: bold; color: #059669; font-size: 15px;">LKR {{ number_format($payment->amount, 2) }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; font-weight: bold;">Remaining Invoice Balance:</td>
                <td style="padding: 10px; font-weight: bold; color: #dc2626;">LKR {{ number_format($payment->invoice->balance_amount, 2) }}</td>
            </tr>
        </table>

        <p>A PDF copy of your receipt is attached to this email.</p>
        
        <p style="margin-top: 30px; font-size: 13px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 15px;">
            {{ $company->company_name ?? 'AutoCare Service Center' }}<br>
            {{ $company->address ?? 'Colombo, Sri Lanka' }}<br>
            Tel: {{ $company->phone ?? '011-2345678' }}
        </p>
    </div>
</body>
</html>
