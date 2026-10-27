<?php

namespace App\Services\Payroll;

use App\Models\CompanyHoliday;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\PayrollStatutorySetting;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceReconciliationService
{
    /**
     * Reconcile an employee's monthly calendar and attendance to calculate:
     * - Full Days, Half Days
     * - Company Off-Days (up to 5 per month for eligible employees)
     * - Paid Holidays (excluded from consuming off-days)
     * - Other Approved Leave
     * - No-Pay Days & No-Pay Deductions
     * - Payable Days & Base Earnings
     *
     * @return array{summary: array, day_records: array}
     */
    public function reconcileEmployee(Employee $employee, int $year, int $month): array
    {
        $periodDate = Carbon::createFromDate($year, $month, 1, 'Asia/Colombo');
        $daysInMonth = $periodDate->daysInMonth;
        $monthStart = $periodDate->copy()->startOfMonth()->toDateString();
        $monthEnd = $periodDate->copy()->endOfMonth()->toDateString();

        // 1. Fetch all attendance records for this employee within the month
        $attendances = EmployeeAttendance::where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$monthStart, $monthEnd])
            ->get()
            ->keyBy(function (EmployeeAttendance $att) {
                return $att->attendance_date instanceof Carbon
                    ? $att->attendance_date->toDateString()
                    : substr((string) $att->attendance_date, 0, 10);
            });

        // 2. Fetch all company holidays for this calendar month and branch
        $monthHolidaysMap = CompanyHoliday::getHolidaysForMonth($year, $month, $employee->branch_id);

        // 3. Daily wage & monthly basic applicable for this payroll period
        $isMonthly = $employee->isMonthlySalary();
        $dailyWageRate = (float) $employee->getWageForDate($monthEnd);
        $monthlyBasicSalary = (float) $employee->getMonthlySalaryForDate($monthEnd);
        $isEligibleForOffDays = (bool) $employee->eligible_for_off_days;

        // Cumulative counters
        $fullDaysCount = 0;
        $halfDaysCount = 0;
        $absentDaysCount = 0;
        $explicitDayOffCount = 0;
        $paidLeaveCount = 0.0;
        $unpaidLeaveCount = 0.0;
        $missingDaysCount = 0;
        $statutoryRestDaysCount = 0;
        $paidHolidaysCount = 0.0;
        $unpaidHolidaysCount = 0.0;
        $earnedAttendanceWage = 0.0;
        $paidHolidayWage = 0.0;

        $dayRecords = [];
        $joiningDate = $employee->joining_date ? Carbon::parse($employee->joining_date, 'Asia/Colombo') : null;

        // 4. Day-by-day calendar reconciliation
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDay = Carbon::createFromDate($year, $month, $day, 'Asia/Colombo');
            $dateStr = $currentDay->toDateString();
            $isSunday = $currentDay->isSunday();

            // Check if employee had joined by this date
            if ($joiningDate && $currentDay->lt($joiningDate->copy()->startOfDay())) {
                $dayRecords[] = [
                    'date' => $dateStr,
                    'day_name' => $currentDay->format('D'),
                    'status' => 'not_employed',
                    'category' => 'Not Employed',
                    'fraction' => 0.0,
                    'wage' => 0.0,
                    'earnings' => 0.0,
                    'notes' => 'Prior to employee joining date',
                ];

                continue;
            }

            /** @var EmployeeAttendance|null $att */
            $att = $attendances->get($dateStr);

            // Check company holidays for this date applicable to this employee
            /** @var Collection $holidaysOnDate */
            $holidaysOnDate = collect($monthHolidaysMap[$dateStr] ?? [])
                ->filter(fn (CompanyHoliday $h) => $h->appliesToEmployee($employee));
            /** @var CompanyHoliday|null $activeHoliday */
            $activeHoliday = $holidaysOnDate->first();

            $category = '';
            $statusStr = '';
            $workedFraction = 0.0;
            $appliedWage = (float) $employee->getWageForDate($dateStr);
            $dayEarnings = 0.0;
            $notes = $att?->notes;

            if ($att !== null) {
                // Record exists in employee_attendance
                $statusStr = $att->status;
                $workedFraction = (float) $att->payable_day_fraction;
                $dayEarnings = (float) $att->daily_earnings;
                $appliedWage = (float) $att->applied_daily_wage;

                switch ($att->status) {
                    case EmployeeAttendance::STATUS_FULL_DAY:
                        $fullDaysCount++;
                        $earnedAttendanceWage += $dayEarnings;
                        $category = $activeHoliday ? 'Worked Full Day on Holiday' : 'Worked Full Day';
                        // Paid holiday does not generate duplicate base earnings if worked
                        break;

                    case EmployeeAttendance::STATUS_HALF_DAY:
                        $halfDaysCount++;
                        $earnedAttendanceWage += $dayEarnings;
                        $category = $activeHoliday ? 'Worked Half Day on Holiday' : 'Worked Half Day';
                        break;

                    case EmployeeAttendance::STATUS_DAY_OFF:
                        $explicitDayOffCount++;
                        $category = 'Explicit Day Off';
                        break;

                    case EmployeeAttendance::STATUS_LEAVE:
                        if ($att->is_paid_leave) {
                            $paidLeaveCount += 1.0;
                            $category = 'Approved Paid Leave';
                            $earnedAttendanceWage += $dayEarnings;
                        } else {
                            $unpaidLeaveCount += 1.0;
                            $category = 'Approved Unpaid Leave';
                        }
                        break;

                    case EmployeeAttendance::STATUS_ABSENT:
                        $absentDaysCount++;
                        $category = 'Absent';
                        break;

                    default:
                        $category = ucfirst(str_replace('_', ' ', (string) $att->status));
                        break;
                }
            } else {
                // No attendance record for this day
                if ($isSunday) {
                    // Statutory weekly rest day
                    $statutoryRestDaysCount++;
                    $statusStr = 'sunday_rest';
                    $category = 'Statutory Rest Day';
                } elseif ($activeHoliday !== null) {
                    // Declared company holiday
                    if ($activeHoliday->isPaid()) {
                        $holidayFraction = $activeHoliday->isFullDay() ? 1.0 : 0.5;
                        $paidHolidaysCount += $holidayFraction;
                        $statusStr = 'paid_holiday';
                        $category = 'Paid Company Holiday: '.$activeHoliday->name;

                        // Daily wage employees receive daily wage credit for unworked paid holidays
                        if (! $isMonthly) {
                            $hWage = round($holidayFraction * $appliedWage, 2);
                            $paidHolidayWage += $hWage;
                            $dayEarnings = $hWage;
                            $workedFraction = $holidayFraction;
                        }
                    } else {
                        $unpaidHolidaysCount += ($activeHoliday->isFullDay() ? 1.0 : 0.5);
                        $statusStr = 'unpaid_holiday';
                        $category = 'Unpaid Company Holiday: '.$activeHoliday->name;
                    }
                    $notes = $notes ?: ($activeHoliday->notes ?? $activeHoliday->name);
                } else {
                    // Missing attendance on a regular working day
                    // Rule: Do not automatically treat missing attendance as absence
                    $missingDaysCount++;
                    $statusStr = 'missing_attendance';
                    $category = 'Missing Attendance';
                }
            }

            $dayRecords[] = [
                'date' => $dateStr,
                'day_name' => $currentDay->format('D'),
                'status' => $statusStr,
                'category' => $category,
                'fraction' => $workedFraction,
                'wage' => $appliedWage,
                'earnings' => $dayEarnings,
                'holiday_name' => $activeHoliday?->name,
                'notes' => $notes,
            ];
        }

        // 5. Calculate Company Off-Days Allocation and No-Pay Days
        // Time off / absences eligible to be absorbed by company off-days:
        // - Explicit day_off records
        // - Absent records
        // - Unpaid leave records
        // - Half-day absence equivalents (0.5 day for each half day worked)
        // Protected days (Statutory rest days, Paid Holidays, Paid Leave) are strictly excluded from consuming off-days.
        $totalAbsenceTaken = $explicitDayOffCount + $absentDaysCount + $unpaidLeaveCount + ($halfDaysCount * 0.5);

        if ($isEligibleForOffDays) {
            // Eligible employees receive up to 5 company off-days per complete month
            $maxAllowedOffDays = 5.0;
            $companyOffDaysUsed = min($maxAllowedOffDays, $totalAbsenceTaken);
            // Excess unprotected absence beyond the 5 allowed off-days constitutes No-Pay days
            $noPayDaysCount = max(0.0, $totalAbsenceTaken - $companyOffDaysUsed);
        } else {
            // Not covered by the company 5-off-day policy
            $companyOffDaysUsed = 0.0;
            $noPayDaysCount = $totalAbsenceTaken;
        }

        // 6. Calculate Base Earnings, Payable Days & No-Pay Deductions
        $statutorySetting = PayrollStatutorySetting::forPeriod($year, $month);
        $noPayDivisor = $statutorySetting->resolveNoPayDivisor($daysInMonth);

        if ($isMonthly) {
            // For Monthly Salary employees:
            // - Retain fixed monthly basic salary as base earnings.
            // - Do not calculate monthly basic by multiplying daily attendance wages.
            // - Calculate valid no-pay deductions separately using configurable divisor.
            $dailyDeductionRate = $noPayDivisor > 0 ? round($monthlyBasicSalary / $noPayDivisor, 2) : 0.00;
            $noPayDeduction = round(min($monthlyBasicSalary, $noPayDaysCount * $dailyDeductionRate), 2);
            $baseEarnings = $monthlyBasicSalary;
            $payableDays = max(0.0, round($daysInMonth - $noPayDaysCount, 2));
        } else {
            // For Daily Wage employees:
            // - Calculate payable attendance accurately.
            // - Include eligible paid holiday wages.
            // - Avoid duplicate earnings.
            $payableDays = round($fullDaysCount + (0.5 * $halfDaysCount) + $paidLeaveCount + $paidHolidaysCount, 2);
            $baseEarnings = round($earnedAttendanceWage + $paidHolidayWage, 2);
            $dailyDeductionRate = $dailyWageRate;
            $noPayDeduction = 0.00; // Daily workers are not paid for unworked days; no negative deduction against a fixed base
        }

        $summary = [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'employee_number' => $employee->employee_number,
            'salary_type' => $employee->salary_type ?? Employee::SALARY_TYPE_DAILY,
            'daily_wage' => $dailyWageRate,
            'monthly_basic_salary' => $monthlyBasicSalary,
            'is_eligible_for_off_days' => $isEligibleForOffDays,
            'days_in_month' => $daysInMonth,
            'no_pay_divisor' => $noPayDivisor,
            'no_pay_divisor_type' => $statutorySetting->no_pay_divisor_type,
            'full_days_count' => $fullDaysCount,
            'half_days_count' => $halfDaysCount,
            'absent_days_count' => $absentDaysCount,
            'explicit_day_off_count' => $explicitDayOffCount,
            'paid_leave_count' => $paidLeaveCount,
            'unpaid_leave_count' => $unpaidLeaveCount,
            'leave_days_count' => $paidLeaveCount,
            'approved_leave_count' => $paidLeaveCount, // Primary approved paid leave
            'missing_attendance_count' => $missingDaysCount,
            'statutory_rest_days_count' => $statutoryRestDaysCount,
            'paid_holidays_count' => $paidHolidaysCount,
            'unpaid_holidays_count' => $unpaidHolidaysCount,
            'total_absence_taken' => $totalAbsenceTaken,
            'company_off_days_used' => $companyOffDaysUsed,
            'no_pay_days_count' => $noPayDaysCount,
            'no_pay_daily_rate' => $dailyDeductionRate,
            'no_pay_deduction' => $noPayDeduction,
            'payable_days' => $payableDays,
            'earned_attendance_wage' => $earnedAttendanceWage,
            'paid_holiday_wage' => $paidHolidayWage,
            'base_earnings' => $baseEarnings,
        ];

        return [
            'summary' => $summary,
            'day_records' => $dayRecords,
        ];
    }
}
