<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $payrollItem->employee_name_snapshot }} - {{ $payrollItem->period->formatted_period }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            background-color: #f9fafb;
            color: #1f2937;
            padding: 20px;
            font-size: 13px;
            line-height: 1.4;
        }

        .no-print {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 800px;
            margin: 0 auto 20px auto;
            background: #ffffff;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            border: none;
        }

        .btn-primary {
            background-color: #0B1849;
            color: #ffffff;
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
        }

        .payslip-container {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            padding: 35px;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            position: relative;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 72px;
            font-weight: 800;
            color: rgba(220, 38, 38, 0.08);
            text-transform: uppercase;
            pointer-events: none;
            user-select: none;
            z-index: 0;
        }

        .watermark-paid {
            color: rgba(16, 185, 129, 0.08);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0B1849;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .company-title {
            font-size: 20px;
            font-weight: 800;
            color: #0B1849;
            letter-spacing: -0.5px;
        }

        .company-meta {
            font-size: 11px;
            color: #4b5563;
            margin-top: 3px;
        }

        .payslip-title {
            text-align: right;
        }

        .payslip-title h2 {
            font-size: 16px;
            font-weight: 800;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .payslip-title .period {
            font-size: 13px;
            font-weight: 700;
            color: #0B1849;
            margin-top: 2px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .meta-item .label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
        }

        .meta-item .value {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 2px;
        }

        .attendance-summary {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
            text-align: center;
            margin-bottom: 20px;
        }

        .att-box {
            border: 1px solid #e2e8f0;
            padding: 8px;
            border-radius: 6px;
            background: #fafafa;
        }

        .att-box .title {
            font-size: 10px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
        }

        .att-box .num {
            font-size: 15px;
            font-weight: 800;
            color: #0B1849;
            margin-top: 2px;
            font-family: monospace;
        }

        .tables-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background-color: #f1f5f9;
            color: #334155;
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 700;
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px solid #cbd5e1;
        }

        table td {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 12px;
        }

        .text-right {
            text-align: right;
        }

        .font-mono {
            font-family: monospace;
        }

        .total-row td {
            font-weight: 800;
            background-color: #f8fafc;
            border-top: 1px solid #cbd5e1;
            font-size: 13px;
        }

        .net-bar {
            background-color: #0B1849;
            color: #ffffff;
            padding: 15px 20px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .net-bar .label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #cbd5e1;
            font-weight: 700;
        }

        .net-bar .amount {
            font-size: 22px;
            font-weight: 900;
            font-family: monospace;
            color: #ffffff;
        }

        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 45px;
            padding-top: 20px;
        }

        .sign-box {
            text-align: center;
            border-top: 1px dashed #94a3b8;
            padding-top: 8px;
            font-size: 11px;
            color: #475569;
            font-weight: 600;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .payslip-container {
                box-shadow: none;
                padding: 10px 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <span style="font-weight: 600; color: #4b5563;">
            Payslip: {{ $payrollItem->employee_name_snapshot }} &bull; {{ $payrollItem->period->formatted_period }}
        </span>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn btn-primary">
                Print Payslip
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                Close
            </button>
        </div>
    </div>

    <div class="payslip-container">
        @if($payrollItem->period->isDraft())
            <div class="watermark">DRAFT</div>
        @elseif($payrollItem->status === 'paid')
            <div class="watermark watermark-paid">PAID</div>
        @endif

        <!-- Header -->
        <div class="header">
            <div>
                <div class="company-title">{{ $company->company_name }}</div>
                <div class="company-meta">{{ $company->address }}</div>
                <div class="company-meta">Tel: {{ $company->phone }} | Reg: {{ $company->registration_number }}</div>
            </div>
            <div class="payslip-title">
                <h2>Payslip / Salary Slip</h2>
                <div class="period">{{ $payrollItem->period->formatted_period }}</div>
                <div style="font-size: 10px; color: #64748b; margin-top: 3px;">
                    Date: {{ \Carbon\Carbon::now('Asia/Colombo')->format('d/m/Y') }}
                </div>
            </div>
        </div>

        <!-- Meta Grid -->
        <div class="meta-grid">
            <div class="meta-item">
                <div class="label">Employee Name</div>
                <div class="value">{{ $payrollItem->employee_name_snapshot }}</div>
            </div>
            <div class="meta-item">
                <div class="label">Employee ID</div>
                <div class="value font-mono">{{ $payrollItem->employee->employee_number }}</div>
            </div>
            <div class="meta-item">
                <div class="label">Designation</div>
                <div class="value">{{ $payrollItem->employee->position ?: 'Staff' }}</div>
            </div>
            <div class="meta-item">
                <div class="label">{{ $payrollItem->isMonthly() ? 'Monthly Basic Salary' : 'Daily Wage Rate' }}</div>
                <div class="value font-mono">
                    @if($payrollItem->isMonthly())
                        LKR {{ number_format($payrollItem->monthly_basic_snapshot, 2) }}
                    @else
                        LKR {{ number_format($payrollItem->daily_wage_snapshot, 2) }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Attendance Breakdown -->
        <div class="attendance-summary">
            <div class="att-box">
                <div class="title">Full Days</div>
                <div class="num">{{ $payrollItem->full_days_count }}</div>
            </div>
            <div class="att-box">
                <div class="title">Half Days</div>
                <div class="num">{{ $payrollItem->half_days_count }}</div>
            </div>
            <div class="att-box">
                <div class="title">Off-Days</div>
                <div class="num" style="color: #6b21a8;">{{ number_format($payrollItem->company_off_days_count, 1) }}</div>
            </div>
            <div class="att-box">
                <div class="title">Paid Hol.</div>
                <div class="num" style="color: #3730a3;">{{ number_format($payrollItem->paid_holidays_count, 1) }}</div>
            </div>
            <div class="att-box">
                <div class="title">Leave</div>
                <div class="num" style="color: #92400e;">{{ number_format($payrollItem->approved_leave_count, 1) }}</div>
            </div>
            <div class="att-box" style="{{ $payrollItem->no_pay_days_count > 0 ? 'background: #fff1f2; border-color: #fecdd3;' : '' }}">
                <div class="title" style="{{ $payrollItem->no_pay_days_count > 0 ? 'color: #9f1239; font-weight: 700;' : '' }}">No-Pay</div>
                <div class="num" style="{{ $payrollItem->no_pay_days_count > 0 ? 'color: #be123c;' : '' }}">{{ number_format($payrollItem->no_pay_days_count, 1) }}</div>
            </div>
            <div class="att-box" style="background: #eef2ff; border-color: #c7d2fe;">
                <div class="title" style="color: #3730a3;">Payable</div>
                <div class="num" style="color: #3730a3;">{{ number_format($payrollItem->payable_days, 1) }}</div>
            </div>
        </div>

        <!-- Tables Grid: Earnings vs Deductions -->
        <div class="tables-grid">
            <!-- Earnings -->
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>Earnings</th>
                            <th class="text-right">Amount (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($payrollItem->isMonthly())
                            <tr>
                                <td>Monthly Basic Salary</td>
                                <td class="text-right font-mono">{{ number_format($payrollItem->base_earnings, 2) }}</td>
                            </tr>
                        @else
                            @php
                                $paidHolidayWage = (float) ($payrollItem->reconciliation_summary['paid_holiday_wage'] ?? 0);
                                $attendanceWage = max(0, (float) $payrollItem->base_earnings - $paidHolidayWage);
                            @endphp
                            <tr>
                                <td>Base Attendance Daily Wages</td>
                                <td class="text-right font-mono">{{ number_format($attendanceWage, 2) }}</td>
                            </tr>
                            @if($paidHolidayWage > 0)
                                <tr>
                                    <td>Paid Company Holidays ({{ number_format($payrollItem->paid_holidays_count, 1) }} d)</td>
                                    <td class="text-right font-mono">{{ number_format($paidHolidayWage, 2) }}</td>
                                </tr>
                            @endif
                        @endif
                        <tr>
                            <td>Overtime Pay</td>
                            <td class="text-right font-mono">{{ number_format($payrollItem->overtime_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Bonuses</td>
                            <td class="text-right font-mono">{{ number_format($payrollItem->bonus_amount, 2) }}</td>
                        </tr>
                        @if($payrollItem->allowances_amount > 0)
                            <tr>
                                <td>Allowances</td>
                                <td class="text-right font-mono">{{ number_format($payrollItem->allowances_amount, 2) }}</td>
                            </tr>
                        @endif
                        <tr class="total-row">
                            <td>Gross Earnings</td>
                            <td class="text-right font-mono">{{ number_format($payrollItem->gross_salary, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Deductions -->
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>Deductions</th>
                            <th class="text-right">Amount (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($payrollItem->no_pay_deduction > 0)
                            <tr style="background: #fff1f2;">
                                <td style="color: #9f1239; font-weight: 600;">No-Pay Deduction ({{ number_format($payrollItem->no_pay_days_count, 1) }} d)</td>
                                <td class="text-right font-mono" style="color: #be123c;">- {{ number_format($payrollItem->no_pay_deduction, 2) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td>Salary Advances Applied</td>
                            <td class="text-right font-mono">{{ $payrollItem->advances_applied > 0 ? '- ' . number_format($payrollItem->advances_applied, 2) : '0.00' }}</td>
                        </tr>
                        <tr>
                            <td>Other Authorized Deductions</td>
                            <td class="text-right font-mono">{{ $payrollItem->deductions > 0 ? '- ' . number_format($payrollItem->deductions, 2) : '0.00' }}</td>
                        </tr>
                        @if($payrollItem->epf_employee_amount > 0)
                            <tr>
                                <td>EPF (Employee 8%)</td>
                                <td class="text-right font-mono">- {{ number_format($payrollItem->epf_employee_amount, 2) }}</td>
                            </tr>
                        @endif
                        @if($payrollItem->apit_amount > 0)
                            <tr style="background: #faf5ff;">
                                <td style="color: #6b21a8; font-weight: 600;">APIT (PAYE Tax)</td>
                                <td class="text-right font-mono" style="color: #581c87;">- {{ number_format($payrollItem->apit_amount, 2) }}</td>
                            </tr>
                        @endif
                        @php
                            $totDedPrint = (float) $payrollItem->advances_applied + (float) $payrollItem->deductions + (float) $payrollItem->no_pay_deduction + (float) $payrollItem->epf_employee_amount + (float) $payrollItem->apit_amount;
                        @endphp
                        <tr class="total-row">
                            <td>Total Deductions</td>
                            <td class="text-right font-mono" style="color: #be123c;">
                                - {{ number_format($totDedPrint, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Net Pay Bar -->
        <div class="net-bar" style="{{ $payrollItem->net_salary < 0 ? 'background: #450a0a; border-color: #991b1b;' : '' }}">
            <div>
                <div class="label">{{ $payrollItem->net_salary < 0 ? 'Net Salary Deficit / Shortfall' : 'Net Salary Payable' }}</div>
                <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">
                    Amount Paid: LKR {{ number_format($payrollItem->paid_amount, 2) }} | Due: {{ $payrollItem->balance_due < 0 ? '- LKR '.number_format(abs($payrollItem->balance_due), 2) : 'LKR '.number_format($payrollItem->balance_due, 2) }}
                </div>
            </div>
            <div class="amount" style="{{ $payrollItem->net_salary < 0 ? 'color: #fca5a5;' : '' }}">
                {{ $payrollItem->net_salary < 0 ? '- LKR '.number_format(abs($payrollItem->net_salary), 2) : 'LKR '.number_format($payrollItem->net_salary, 2) }}
            </div>
        </div>

        <!-- Statutory Employer Contributions Information Box -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; margin-bottom: 20px; font-size: 11px;">
            <div style="font-weight: 700; color: #334155; margin-bottom: 6px; text-transform: uppercase; font-size: 10px; letter-spacing: 0.5px;">
                Statutory Contributions & Company Liability (Not Deducted From Net Pay)
            </div>
            <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; text-align: center;">
                <div>
                    <span style="color: #64748b; font-size: 10px; display: block;">EPF/ETF Base</span>
                    <strong class="font-mono">LKR {{ number_format($payrollItem->epf_qualifying_earnings, 2) }}</strong>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 10px; display: block;">Member EPF (8%)</span>
                    <strong class="font-mono" style="color: #1e40af;">LKR {{ number_format($payrollItem->epf_employee_amount, 2) }}</strong>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 10px; display: block;">Employer EPF (12%)</span>
                    <strong class="font-mono" style="color: #3730a3;">LKR {{ number_format($payrollItem->epf_employer_amount, 2) }}</strong>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 10px; display: block;">Employer ETF (3%)</span>
                    <strong class="font-mono" style="color: #065f46;">LKR {{ number_format($payrollItem->etf_employer_amount, 2) }}</strong>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 10px; display: block;">Total Cost to Co.</span>
                    <strong class="font-mono" style="color: #78350f;">LKR {{ number_format($payrollItem->total_cost_to_company, 2) }}</strong>
                </div>
            </div>
        </div>

        @if($payrollItem->notes)
            <div style="font-size: 11px; color: #64748b; margin-bottom: 20px;">
                <strong>Remarks:</strong> {{ $payrollItem->notes }}
            </div>
        @endif

        <!-- Signatures -->
        <div class="signatures">
            <div class="sign-box">Prepared By</div>
            <div class="sign-box">Approved By (Management)</div>
            <div class="sign-box">Employee Signature & Date</div>
        </div>
    </div>

</body>
</html>
