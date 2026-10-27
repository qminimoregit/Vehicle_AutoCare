<?php

namespace App\Services\Payroll;

use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\EmployeeAllowance;
use App\Models\EmployeeAttendance;
use App\Models\EmployeeBonus;
use App\Models\EmployeeOvertime;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Models\PayrollStatutorySetting;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PayrollCalculationService
{
    public function __construct(
        public AttendanceReconciliationService $reconciliationService
    ) {}

    /**
     * Calculate and synchronize draft payroll period and its employee items.
     *
     * @throws DomainException If payroll period is already approved and locked.
     */
    public function calculatePeriod(int $year, int $month): PayrollPeriod
    {
        $periodDate = Carbon::createFromDate($year, $month, 1, 'Asia/Colombo');
        $monthStart = $periodDate->copy()->startOfMonth()->toDateString();
        $monthEnd = $periodDate->copy()->endOfMonth()->toDateString();

        return DB::transaction(function () use ($year, $month, $monthStart, $monthEnd) {
            $period = PayrollPeriod::where('year', $year)
                ->where('month', $month)
                ->lockForUpdate()
                ->first();

            if (! $period) {
                $period = PayrollPeriod::create([
                    'year' => $year,
                    'month' => $month,
                    'status' => PayrollPeriod::STATUS_DRAFT,
                ]);
            }

            // Financial Rule: Approved or Paid payroll must not be modified automatically without authorized rollback
            if (! $period->isModifiable()) {
                throw new DomainException("Payroll period {$period->formatted_period} is already {$period->status} and cannot be recalculated without an authorized rollback.");
            }

            // If previously reviewed, reset to draft so recalculation changes are re-reviewed
            if ($period->isReviewed()) {
                $period->update(['status' => PayrollPeriod::STATUS_DRAFT]);
            }

            // Retrieve statutory configuration setting effective for this month
            $statutorySetting = PayrollStatutorySetting::forPeriod($year, $month);

            // Identify eligible employees for this period
            $empIdsWithAttendance = EmployeeAttendance::whereBetween('attendance_date', [$monthStart, $monthEnd])
                ->pluck('employee_id');

            $existingItemEmpIds = PayrollItem::where('payroll_period_id', $period->id)
                ->pluck('employee_id');

            $employees = Employee::query()
                ->where(function ($query) use ($monthEnd) {
                    $query->where('is_active', true)
                        ->where(function ($sub) use ($monthEnd) {
                            $sub->whereDate('joining_date', '<=', $monthEnd)
                                ->orWhereNull('joining_date');
                        });
                })
                ->orWhereIn('id', $empIdsWithAttendance)
                ->orWhereIn('id', $existingItemEmpIds)
                ->orderBy('name')
                ->get();

            foreach ($employees as $employee) {
                $existingItem = PayrollItem::where('payroll_period_id', $period->id)
                    ->where('employee_id', $employee->id)
                    ->first();

                $calculation = $this->calculateEmployeeItem($period, $employee, $existingItem, $statutorySetting);

                PayrollItem::updateOrCreate(
                    [
                        'payroll_period_id' => $period->id,
                        'employee_id' => $employee->id,
                    ],
                    $calculation['attributes']
                );
            }

            $period->recalculateTotals();

            return $period->fresh(['items.employee']);
        });
    }

    /**
     * Calculate a single employee's monthly payroll item with all earnings, deductions, and breakdowns.
     *
     * @return array{
     *     attributes: array<string, mixed>,
     *     summary: array<string, mixed>,
     *     breakdown_payload: array<string, mixed>
     * }
     */
    public function calculateEmployeeItem(
        PayrollPeriod $period,
        Employee $employee,
        ?PayrollItem $existingItem = null,
        ?PayrollStatutorySetting $statutorySetting = null
    ): array {
        $year = $period->year;
        $month = $period->month;
        $periodDate = Carbon::createFromDate($year, $month, 1, 'Asia/Colombo');
        $monthStart = $periodDate->copy()->startOfMonth()->toDateString();
        $monthEnd = $periodDate->copy()->endOfMonth()->toDateString();

        $statutorySetting = $statutorySetting ?: PayrollStatutorySetting::forPeriod($year, $month);

        // 1. Reconcile calendar attendance, 5-off-days policy, and holiday wages
        $reconciliation = $this->reconciliationService->reconcileEmployee($employee, $year, $month);
        $summary = $reconciliation['summary'];
        $dayRecords = $reconciliation['day_records'];

        $isMonthly = ($summary['salary_type'] === Employee::SALARY_TYPE_MONTHLY);
        $baseEarnings = (float) $summary['base_earnings'];
        $noPayDeduction = (float) $summary['no_pay_deduction'];

        // 2. Preserve manual adjustments if already present on draft item
        $manualDeductions = $existingItem ? (float) $existingItem->deductions : 0.00;
        $notes = $existingItem ? $existingItem->notes : null;

        // 3. Resolve Overtime Pay
        $otData = $this->resolveOvertime($employee, $monthStart, $monthEnd, $existingItem);
        $overtimeAmount = $otData['amount'];
        $overtimeBreakdown = $otData['breakdown'];

        // 4. Resolve Bonuses (separating EPF-eligible and taxable components)
        $bonusData = $this->resolveBonuses($employee, $year, $month, $existingItem);
        $bonusAmount = $bonusData['amount'];
        $epfEligibleBonuses = $bonusData['epf_eligible_amount'];
        $taxableBonuses = $bonusData['taxable_amount'];
        $bonusesBreakdown = $bonusData['breakdown'];

        // 5. Resolve Allowances (separating EPF-eligible and taxable components)
        $alwData = $this->resolveAllowances($employee, $year, $month, $existingItem);
        $allowancesAmount = $alwData['amount'];
        $epfEligibleAllowances = $alwData['epf_eligible_amount'];
        $taxableAllowances = $alwData['taxable_amount'];
        $allowancesBreakdown = $alwData['breakdown'];

        // 6. Calculate Gross Salary
        // Calculation Rule: Gross = Base Attendance/Basic + Overtime + Bonuses + Allowances
        $grossSalary = round($baseEarnings + $overtimeAmount + $bonusAmount + $allowancesAmount, 2);

        // 7. Calculate Statutory Deductions (EPF 8%, EPF 12%, ETF 3%) and APIT
        $statutory = $this->calculateStatutory(
            $employee,
            $baseEarnings,
            $noPayDeduction,
            $epfEligibleAllowances,
            $epfEligibleBonuses,
            $overtimeAmount,
            $taxableBonuses,
            $taxableAllowances,
            $isMonthly,
            $statutorySetting
        );

        // 8. Calculate Salary Advance Recovery
        // Financial Rule: Never deduct more than net payable earnings prior to advances.
        // Pending advances must never be recovered unless actually disbursed.
        $netPriorToAdvances = round(
            $grossSalary - $manualDeductions - $noPayDeduction - $statutory['epf_employee_amount'] - $statutory['apit_amount'],
            2
        );
        $maxDeductibleForAdvances = max(0.0, $netPriorToAdvances);

        $advanceData = $this->resolveAdvanceRecovery($employee, $monthEnd, $maxDeductibleForAdvances);
        $advancesApplied = $advanceData['advances_applied'];
        $plannedAdvanceRecoveries = $advanceData['planned_recoveries'];

        // 9. Calculate Total Deductions & Net Salary
        // Financial & Statutory Safety Rule:
        // Mandatory statutory liabilities (EPF 8%, APIT) must NOT be artificially capped to force net pay >= 0.
        // If mandatory statutory deductions and no-pay exceed gross salary, calculate actual net salary and flag an unresolved shortfall.
        $totalDeductions = round(
            $manualDeductions + $noPayDeduction + $statutory['epf_employee_amount'] + $statutory['apit_amount'] + $advancesApplied,
            2
        );
        $netSalary = round($grossSalary - $totalDeductions, 2);

        if ($netSalary < 0) {
            $shortfall = abs($netSalary);
            $statutory['flagged_for_admin_review'][] = 'Unresolved deduction shortfall of LKR '.number_format($shortfall, 2).': mandatory statutory deductions (EPF/APIT) and no-pay exceed gross earnings. Requires administrator review.';
        }

        $paidAmount = $existingItem ? (float) $existingItem->paid_amount : 0.00;
        $balanceDue = round($netSalary - $paidAmount, 2);

        // 10. Compile metadata and summary breakdown
        $summary['overtime_amount'] = $overtimeAmount;
        $summary['bonus_amount'] = $bonusAmount;
        $summary['allowances_amount'] = $allowancesAmount;
        $summary['epf_qualifying_salary'] = $statutory['epf_qualifying_salary'];
        $summary['etf_qualifying_salary'] = $statutory['etf_qualifying_salary'];
        $summary['apit_taxable_earnings'] = $statutory['apit_taxable_earnings'];
        $summary['apit_amount'] = $statutory['apit_amount'];

        $breakdownPayload = [
            'summary' => $summary,
            'reconciled_days' => $dayRecords,
            'planned_advance_recoveries' => $plannedAdvanceRecoveries,
            'bonuses_breakdown' => $bonusesBreakdown,
            'allowances_breakdown' => $allowancesBreakdown,
            'overtime_breakdown' => $overtimeBreakdown,
            'statutory_breakdown' => $statutory,
        ];

        $attributes = [
            'employee_name_snapshot' => $employee->name,
            'daily_wage_snapshot' => $summary['daily_wage'],
            'full_days_count' => $summary['full_days_count'],
            'half_days_count' => $summary['half_days_count'],
            'absent_days_count' => $summary['absent_days_count'] ?? 0,
            'leave_days_count' => $summary['leave_days_count'] ?? $summary['approved_leave_count'] ?? 0,
            'day_off_days_count' => (int) round($summary['company_off_days_used'] ?? 0),
            'payable_days' => $summary['payable_days'] ?? 0,
            'base_earnings' => $baseEarnings,
            'overtime_amount' => $overtimeAmount,
            'bonus_amount' => $bonusAmount,
            'allowances_amount' => $allowancesAmount,
            'deductions' => $manualDeductions,
            'no_pay_deduction' => $noPayDeduction,
            'epf_employee_amount' => $statutory['epf_employee_amount'],
            'epf_employer_amount' => $statutory['epf_employer_amount'],
            'etf_employer_amount' => $statutory['etf_employer_amount'],
            'advances_applied' => $advancesApplied,
            'gross_salary' => $grossSalary,
            'net_salary' => $netSalary,
            'paid_amount' => $paidAmount,
            'balance_due' => $balanceDue,
            'status' => PayrollItem::STATUS_DRAFT,
            'attendance_breakdown' => $breakdownPayload,
            'notes' => $notes,
        ];

        if (Schema::hasColumn('payroll_items', 'salary_type_snapshot')) {
            $attributes['salary_type_snapshot'] = $summary['salary_type'];
        }
        if (Schema::hasColumn('payroll_items', 'monthly_basic_snapshot')) {
            $attributes['monthly_basic_snapshot'] = $summary['monthly_basic_salary'];
        }
        if (Schema::hasColumn('payroll_items', 'company_off_days_count')) {
            $attributes['company_off_days_count'] = $summary['company_off_days_used'];
        }
        if (Schema::hasColumn('payroll_items', 'paid_holidays_count')) {
            $attributes['paid_holidays_count'] = $summary['paid_holidays_count'];
        }
        if (Schema::hasColumn('payroll_items', 'approved_leave_count')) {
            $attributes['approved_leave_count'] = $summary['approved_leave_count'];
        }
        if (Schema::hasColumn('payroll_items', 'no_pay_days_count')) {
            $attributes['no_pay_days_count'] = $summary['no_pay_days_count'];
        }
        if (Schema::hasColumn('payroll_items', 'apit_amount')) {
            $attributes['apit_amount'] = $statutory['apit_amount'];
        }
        if (Schema::hasColumn('payroll_items', 'epf_qualifying_earnings')) {
            $attributes['epf_qualifying_earnings'] = $statutory['epf_qualifying_salary'];
        }
        if (Schema::hasColumn('payroll_items', 'etf_qualifying_earnings')) {
            $attributes['etf_qualifying_earnings'] = $statutory['etf_qualifying_salary'];
        }
        if (Schema::hasColumn('payroll_items', 'apit_taxable_earnings')) {
            $attributes['apit_taxable_earnings'] = $statutory['apit_taxable_earnings'];
        }

        return [
            'attributes' => $attributes,
            'summary' => $summary,
            'breakdown_payload' => $breakdownPayload,
        ];
    }

    /**
     * Resolve approved overtime records or fallback to existing draft adjustment.
     *
     * @return array{amount: float, breakdown: array<int, array<string, mixed>>}
     */
    protected function resolveOvertime(Employee $employee, string $monthStart, string $monthEnd, ?PayrollItem $existingItem): array
    {
        $breakdown = [];
        $amount = $existingItem ? (float) $existingItem->overtime_amount : 0.00;

        if (Schema::hasTable('employee_overtimes')) {
            $approvedOvertime = EmployeeOvertime::where('employee_id', $employee->id)
                ->whereBetween('overtime_date', [$monthStart, $monthEnd])
                ->whereIn('status', [EmployeeOvertime::STATUS_APPROVED, EmployeeOvertime::STATUS_APPLIED])
                ->get();

            if ($approvedOvertime->isNotEmpty()) {
                $amount = (float) $approvedOvertime->sum('amount');
                $breakdown = $approvedOvertime->map(fn ($ot) => [
                    'id' => $ot->id,
                    'overtime_number' => $ot->overtime_number,
                    'date' => $ot->overtime_date->format('Y-m-d'),
                    'hours' => (float) $ot->hours,
                    'hourly_rate' => (float) $ot->hourly_rate,
                    'multiplier' => (float) $ot->rate_multiplier,
                    'amount' => (float) $ot->amount,
                ])->all();
            }
        }

        return [
            'amount' => round($amount, 2),
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Resolve approved bonuses, tracking EPF eligibility and taxability.
     *
     * @return array{
     *     amount: float,
     *     epf_eligible_amount: float,
     *     taxable_amount: float,
     *     breakdown: array<int, array<string, mixed>>
     * }
     */
    protected function resolveBonuses(Employee $employee, int $year, int $month, ?PayrollItem $existingItem): array
    {
        $breakdown = [];
        $amount = $existingItem ? (float) $existingItem->bonus_amount : 0.00;
        $epfEligibleAmount = 0.00;
        $taxableAmount = 0.00;

        if (Schema::hasTable('employee_bonuses')) {
            $approvedBonuses = EmployeeBonus::where('employee_id', $employee->id)
                ->where('period_year', $year)
                ->where('period_month', $month)
                ->whereIn('status', [EmployeeBonus::STATUS_APPROVED, EmployeeBonus::STATUS_APPLIED])
                ->get();

            if ($approvedBonuses->isNotEmpty()) {
                $amount = (float) $approvedBonuses->sum('amount');
                $breakdown = $approvedBonuses->map(function ($b) use (&$epfEligibleAmount, &$taxableAmount) {
                    $bAmt = (float) $b->amount;

                    // Bonuses related to attendance, incentive, or performance can be deemed earnings for EPF
                    $isEpfEligible = false;
                    if (isset($b->is_epf_eligible)) {
                        $isEpfEligible = (bool) $b->is_epf_eligible;
                    } elseif (in_array($b->bonus_type, [EmployeeBonus::TYPE_ATTENDANCE, EmployeeBonus::TYPE_INCENTIVE, EmployeeBonus::TYPE_PERFORMANCE])) {
                        $isEpfEligible = true;
                    }

                    // Profits from employment are taxable under Inland Revenue Act
                    $isTaxable = isset($b->is_taxable) ? (bool) $b->is_taxable : true;

                    if ($isEpfEligible) {
                        $epfEligibleAmount += $bAmt;
                    }
                    if ($isTaxable) {
                        $taxableAmount += $bAmt;
                    }

                    return [
                        'id' => $b->id,
                        'bonus_number' => $b->bonus_number,
                        'type' => $b->formatted_type,
                        'amount' => $bAmt,
                        'is_epf_eligible' => $isEpfEligible,
                        'is_taxable' => $isTaxable,
                    ];
                })->all();
            }
        }

        return [
            'amount' => round($amount, 2),
            'epf_eligible_amount' => round($epfEligibleAmount, 2),
            'taxable_amount' => round($taxableAmount, 2),
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Resolve active recurring and one-off allowances, tracking EPF eligibility and taxability.
     *
     * @return array{
     *     amount: float,
     *     epf_eligible_amount: float,
     *     taxable_amount: float,
     *     breakdown: array<int, array<string, mixed>>
     * }
     */
    protected function resolveAllowances(Employee $employee, int $year, int $month, ?PayrollItem $existingItem): array
    {
        $breakdown = [];
        $amount = $existingItem ? (float) $existingItem->allowances_amount : 0.00;
        $epfEligibleAmount = 0.00;
        $taxableAmount = 0.00;

        if (Schema::hasTable('employee_allowances')) {
            $activeAllowances = EmployeeAllowance::where('employee_id', $employee->id)
                ->whereIn('status', [EmployeeAllowance::STATUS_ACTIVE, EmployeeAllowance::STATUS_APPROVED])
                ->get()
                ->filter(fn (EmployeeAllowance $alw) => $alw->isApplicableForPeriod($year, $month));

            if ($activeAllowances->isNotEmpty()) {
                $amount = (float) $activeAllowances->sum('amount');
                $breakdown = $activeAllowances->map(function (EmployeeAllowance $a) use (&$epfEligibleAmount, &$taxableAmount) {
                    $aAmt = (float) $a->amount;
                    if ($a->is_epf_eligible) {
                        $epfEligibleAmount += $aAmt;
                    }
                    if ($a->is_taxable ?? true) {
                        $taxableAmount += $aAmt;
                    }

                    return [
                        'id' => $a->id,
                        'allowance_number' => $a->allowance_number,
                        'type' => $a->formatted_type,
                        'title' => $a->title,
                        'amount' => $aAmt,
                        'is_recurring' => $a->is_recurring,
                        'is_epf_eligible' => $a->is_epf_eligible,
                        'is_etf_eligible' => $a->is_etf_eligible,
                        'is_taxable' => $a->is_taxable ?? true,
                    ];
                })->values()->all();
            }
        }

        return [
            'amount' => round($amount, 2),
            'epf_eligible_amount' => round($epfEligibleAmount, 2),
            'taxable_amount' => round($taxableAmount, 2),
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Calculate statutory deductions (EPF 8%, EPF 12%, ETF 3%) and APIT according to Sri Lankan tax & labor law.
     *
     * @return array{
     *     epf_qualifying_salary: float,
     *     etf_qualifying_salary: float,
     *     epf_employee_rate: float,
     *     epf_employer_rate: float,
     *     etf_employer_rate: float,
     *     epf_employee_amount: float,
     *     epf_employer_amount: float,
     *     etf_employer_amount: float,
     *     apit_taxable_earnings: float,
     *     apit_tax_relief: float,
     *     apit_taxable_income: float,
     *     apit_amount: float,
     *     apit_slabs_breakdown: array<int, array<string, mixed>>,
     *     flagged_for_admin_review: array<int, string>
     * }
     */
    protected function calculateStatutory(
        Employee $employee,
        float $baseEarnings,
        float $noPayDeduction,
        float $epfEligibleAllowances,
        float $epfEligibleBonuses,
        float $overtimeAmount,
        float $taxableBonuses,
        float $taxableAllowances,
        bool $isMonthly,
        PayrollStatutorySetting $statutorySetting
    ): array {
        $flaggedReviews = [];

        // 1. Calculate EPF/ETF Qualifying Base:
        // Monthly worker: (Monthly Basic Salary - No Pay Deduction) + EPF-eligible allowances + EPF-eligible bonuses
        // Daily worker: Payable Attendance Wages + Paid Holiday Wages + EPF-eligible allowances + EPF-eligible bonuses
        // Note: Under Section 47 of EPF Act, Overtime is strictly EXCLUDED from EPF/ETF.
        $baseQualifying = $isMonthly
            ? max(0.0, round($baseEarnings - $noPayDeduction, 2))
            : max(0.0, round($baseEarnings, 2));

        $epfQualifyingSalary = round($baseQualifying + $epfEligibleAllowances + $epfEligibleBonuses, 2);
        $etfQualifyingSalary = $epfQualifyingSalary;

        $epfEmployeeRate = (float) $statutorySetting->epf_employee_rate;
        $epfEmployerRate = (float) $statutorySetting->epf_employer_rate;
        $etfEmployerRate = (float) $statutorySetting->etf_employer_rate;

        // Mandatory Coverage Verification (Requirement 8)
        // Under Sri Lankan law, private sector employees under contract of employment are mandatorily covered by EPF/ETF.
        $isEpfCovered = $employee->is_epf_eligible ?? true;
        $isEtfCovered = $employee->is_etf_eligible ?? true;

        if (! $isEpfCovered && empty($employee->statutory_exemption_reason)) {
            $flaggedReviews[] = 'Employee marked non-EPF eligible without legally registered statutory exemption reason; verify compliance.';
        }

        $epfEmployee = 0.00;
        $epfEmployer = 0.00;
        $etfEmployer = 0.00;

        if ($isEpfCovered && $epfQualifyingSalary > 0) {
            $epfEmployee = round($epfQualifyingSalary * ($epfEmployeeRate / 100), 2);
            $epfEmployer = round($epfQualifyingSalary * ($epfEmployerRate / 100), 2);
        }

        if ($isEtfCovered && $etfQualifyingSalary > 0) {
            $etfEmployer = round($etfQualifyingSalary * ($etfEmployerRate / 100), 2);
        }

        // 2. Calculate APIT (Advance Personal Income Tax)
        // Regular monthly employment income subject to APIT:
        // (Base Earnings - No Pay) + Overtime + Taxable Bonuses + Taxable Allowances
        $regularGrossForTax = max(0.0, round(($baseEarnings - $noPayDeduction) + $overtimeAmount + $taxableBonuses + $taxableAllowances, 2));

        $apitTaxableEarnings = $regularGrossForTax;
        $apitTaxRelief = 0.00;
        $apitTaxableIncome = 0.00;
        $apitAmount = 0.00;
        $slabsBreakdown = [];

        if ($statutorySetting->apit_enabled && $regularGrossForTax > 0) {
            $isResident = $employee->is_resident_for_tax ?? true;
            $hasPrimaryDeclaration = $employee->primary_employment_declaration ?? true;
            $effectiveDateStr = $statutorySetting->effective_from?->toDateString();

            if ($isResident && $hasPrimaryDeclaration) {
                // Table 01: Resident Employee with Primary Employment Declaration (TD 1)
                $apitTaxRelief = (float) $statutorySetting->apit_monthly_relief;
                $apitTaxableIncome = max(0.00, round($regularGrossForTax - $apitTaxRelief, 2));

                if ($apitTaxableIncome > 0) {
                    $taxSlabs = $statutorySetting->apit_tax_slabs ?: PayrollStatutorySetting::defaultTaxSlabs($effectiveDateStr);
                    $remainingTaxable = $apitTaxableIncome;

                    foreach ($taxSlabs as $slab) {
                        if ($remainingTaxable <= 0.001) {
                            break;
                        }

                        $limit = $slab['limit'] ?? null;
                        $rate = (float) $slab['rate'];

                        $slabTaxable = ($limit !== null) ? min($remainingTaxable, (float) $limit) : $remainingTaxable;
                        $slabTax = round($slabTaxable * ($rate / 100), 2);

                        $apitAmount += $slabTax;
                        $slabsBreakdown[] = [
                            'slab' => $slab['slab'] ?? count($slabsBreakdown) + 1,
                            'taxable_amount' => $slabTaxable,
                            'rate' => $rate,
                            'tax_amount' => $slabTax,
                        ];

                        $remainingTaxable = max(0.0, round($remainingTaxable - $slabTaxable, 2));
                    }
                }
            } elseif (! $isResident && ! $hasPrimaryDeclaration) {
                // Table 7.2: Non-Resident Secondary Employment: flat 36%
                $apitTaxRelief = 0.00;
                $apitTaxableIncome = $regularGrossForTax;
                $apitAmount = round($regularGrossForTax * 0.36, 2);
                $slabsBreakdown[] = [
                    'slab' => 'Table 7.2',
                    'taxable_amount' => $regularGrossForTax,
                    'rate' => 36.00,
                    'tax_amount' => $apitAmount,
                    'notes' => 'Non-resident secondary employment flat rate (Table 7.2)',
                ];
                $flaggedReviews[] = 'Non-resident employee in secondary employment: taxed at flat 36% under IRD Table 7.2.';
            } else {
                // Resident Secondary Employment (Table 7.1) or Non-resident Primary (Table 04):
                // No personal relief available under TD 1; taxed progressively from first rupee
                $apitTaxRelief = 0.00;
                $apitTaxableIncome = $regularGrossForTax;

                $taxSlabs = $statutorySetting->apit_tax_slabs ?: PayrollStatutorySetting::defaultTaxSlabs($effectiveDateStr);
                $remainingTaxable = $apitTaxableIncome;

                foreach ($taxSlabs as $slab) {
                    if ($remainingTaxable <= 0.001) {
                        break;
                    }

                    $limit = $slab['limit'] ?? null;
                    $rate = (float) $slab['rate'];

                    $slabTaxable = ($limit !== null) ? min($remainingTaxable, (float) $limit) : $remainingTaxable;
                    $slabTax = round($slabTaxable * ($rate / 100), 2);

                    $apitAmount += $slabTax;
                    $slabsBreakdown[] = [
                        'slab' => $slab['slab'] ?? count($slabsBreakdown) + 1,
                        'taxable_amount' => $slabTaxable,
                        'rate' => $rate,
                        'tax_amount' => $slabTax,
                    ];

                    $remainingTaxable = max(0.0, round($remainingTaxable - $slabTaxable, 2));
                }

                if (! $isResident) {
                    $flaggedReviews[] = 'Non-resident employee: assessed under IRD Table 04 without resident personal tax relief.';
                } else {
                    $flaggedReviews[] = 'Secondary employment (no TD 1 primary declaration): assessed under IRD Table 07 without monthly personal relief.';
                }
            }

            $apitAmount = round($apitAmount, 2);
        }

        return [
            'epf_qualifying_salary' => $epfQualifyingSalary,
            'etf_qualifying_salary' => $etfQualifyingSalary,
            'epf_employee_rate' => $epfEmployeeRate,
            'epf_employer_rate' => $epfEmployerRate,
            'etf_employer_rate' => $etfEmployerRate,
            'epf_employee_amount' => $epfEmployee,
            'epf_employer_amount' => $epfEmployer,
            'etf_employer_amount' => $etfEmployer,
            'apit_taxable_earnings' => $apitTaxableEarnings,
            'apit_tax_relief' => $apitTaxRelief,
            'apit_taxable_income' => $apitTaxableIncome,
            'apit_amount' => $apitAmount,
            'apit_slabs_breakdown' => $slabsBreakdown,
            'flagged_for_admin_review' => $flaggedReviews,
        ];
    }

    /**
     * Calculate planned advance recoveries for active/disbursed advances.
     * Prevents duplicate deductions and never deducts beyond authorized/available net pay.
     * Requirement 5: Pending salary advances must not be recovered unless actually disbursed.
     *
     * @return array{
     *     advances_applied: float,
     *     planned_recoveries: array<int, array<string, mixed>>
     * }
     */
    protected function resolveAdvanceRecovery(Employee $employee, string $monthEnd, float $maxDeductible): array
    {
        $eligibleAdvances = EmployeeAdvance::where('employee_id', $employee->id)
            ->whereIn('status', [
                EmployeeAdvance::STATUS_DISBURSED,
                EmployeeAdvance::STATUS_PARTIALLY_RECOVERED,
            ])
            ->where(function ($q) use ($monthEnd) {
                $q->whereNull('disbursed_at')
                    ->orWhere('disbursed_at', '<=', $monthEnd);
            })
            ->where('advance_date', '<=', $monthEnd)
            ->orderBy('advance_date')
            ->get();

        $plannedRecoveries = [];
        $advancesAppliedSum = 0.00;

        foreach ($eligibleAdvances as $adv) {
            $balance = $adv->remaining_balance;
            if ($balance <= 0.001) {
                continue;
            }

            $installment = (float) ($adv->monthly_installment ?? 0);
            $plannedForThis = ($installment > 0) ? min($balance, $installment) : $balance;

            $availableAllowance = max(0.0, round($maxDeductible - $advancesAppliedSum, 2));
            $actualDeduction = min($plannedForThis, $availableAllowance);

            if ($actualDeduction > 0) {
                $advancesAppliedSum += $actualDeduction;
                $plannedRecoveries[] = [
                    'advance_id' => $adv->id,
                    'advance_number' => $adv->advance_number,
                    'advance_amount' => (float) $adv->amount,
                    'previous_balance' => $balance,
                    'recovery_amount' => $actualDeduction,
                    'balance_after' => max(0.0, round($balance - $actualDeduction, 2)),
                ];
            }
        }

        return [
            'advances_applied' => round($advancesAppliedSum, 2),
            'planned_recoveries' => $plannedRecoveries,
        ];
    }
}
