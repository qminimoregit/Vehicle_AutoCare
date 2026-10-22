<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Sales Bill</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 20px; background-color: #f8fafc;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 28px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
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
            $customerName = ($directSale->customer && $directSale->customer->customer_name && $directSale->customer->customer_name !== 'Walk-in Customer')
                ? $directSale->customer->customer_name
                : 'Customer';
        @endphp

        <p style="font-size: 15px; margin-top: 0; margin-bottom: 16px; color: #111827;">Dear {{ $customerName }},</p>
        
        <p style="font-size: 14px; margin-top: 0; margin-bottom: 12px; color: #374151;">Thank you for choosing {{ $companyName }}.</p>
        
        <p style="font-size: 14px; margin-top: 0; margin-bottom: 20px; color: #374151;">Your payment has been recorded successfully.</p>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 13.5px;">
            <tr>
                <td style="padding: 10px 14px; border-bottom: 1px solid #e5e7eb; font-weight: bold; color: #4b5563; width: 45%;">Bill Number:</td>
                <td style="padding: 10px 14px; border-bottom: 1px solid #e5e7eb; font-weight: bold; color: #0B1849; font-family: monospace;">{{ $directSale->bill_number }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 14px; border-bottom: 1px solid #e5e7eb; font-weight: bold; color: #4b5563;">Total Amount:</td>
                <td style="padding: 10px 14px; border-bottom: 1px solid #e5e7eb; font-weight: bold; color: #111827;">LKR {{ number_format((float) $directSale->grand_total, 2) }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 14px; border-bottom: 1px solid #e5e7eb; font-weight: bold; color: #4b5563;">Amount Paid:</td>
                <td style="padding: 10px 14px; border-bottom: 1px solid #e5e7eb; color: #059669; font-weight: bold;">LKR {{ number_format((float) $directSale->total_paid, 2) }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 14px; border-bottom: 1px solid #e5e7eb; font-weight: bold; color: #4b5563;">Balance:</td>
                <td style="padding: 10px 14px; border-bottom: 1px solid #e5e7eb; color: {{ $directSale->balance > 0 ? '#dc2626' : '#111827' }}; font-weight: bold;">LKR {{ number_format((float) $directSale->balance, 2) }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 14px; font-weight: bold; color: #4b5563;">Payment Status:</td>
                <td style="padding: 10px 14px; font-weight: bold; color: {{ $directSale->payment_status === 'Paid in Full' ? '#059669' : '#d97706' }};">{{ $directSale->payment_status }}</td>
            </tr>
        </table>

        <p style="font-size: 14px; margin-top: 16px; margin-bottom: 24px; color: #374151;">Please find your Sales Bill attached.</p>
        
        <p style="margin-top: 28px; font-size: 13.5px; color: #374151; line-height: 1.5; border-top: 1px solid #e5e7eb; padding-top: 16px;">
            Thank you,<br>
            <strong>{{ $companyName }}</strong><br>
            {{ $location }}<br>
            Tel: {{ $phone }}
        </p>
    </div>
</body>
</html>
