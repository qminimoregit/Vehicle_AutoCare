<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\EmployeeAdvanceRecovery;
use App\Models\EmployeeAttendance;
use App\Models\EmployeeBonus;
use App\Models\EmployeeOvertime;
use App\Models\PayrollItem;
use App\Models\PayrollPayment;
use App\Models\PayrollPeriod;
use App\Services\Payroll\PayrollCalculationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SalaryController extends Controller
{
    public function __construct(
        public PayrollCalculationService $calculationService
    ) {}

    /**
     * Ensure only authorized users access salary modules.
     */
    private function authorizeSalaryView(): void
    {
        $user = Auth::user();
        if (! $user || (! $user->isAdmin() && ! $user->hasPermission('view_salary'))) {
            abort(403, 'Unauthorized access to employee salary and payroll records.');
        }
    }

    private function authorizeAdmin(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Administrator access required for this payroll operation.');
        }
    }

    /**
     * Monthly salary report & draft salary generator.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $this->authorizeSalaryView();

        $now = Carbon::now('Asia/Colombo');
        $year = (int) $request->input('year', $now->year);
        $month = (int) $request->input('month', $now->month);

        $periodDate = Carbon::createFromDate($year, $month, 1, 'Asia/Colombo');
        $monthStart = $periodDate->copy()->startOfMonth()->toDateString();
        $monthEnd = $periodDate->copy()->endOfMonth()->toDateString();
        $today = Carbon::today('Asia/Colombo');

        // Check if payroll period exists in database
        $period = PayrollPeriod::where('year', $year)
            ->where('month', $month)
            ->with(['items.employee.branch', 'items.payments', 'approver'])
            ->first();

        if ($period) {
            $period->items->each(function ($item) use ($period) {
                $item->setRelation('period', $period);
            });
        }

        // Handle "Generate Salary" button action
        if ($request->has('generate') || $request->input('action') === 'generate') {
            $this->authorizeAdmin();

            if ($period && ! $period->isModifiable()) {
                return redirect()->route('payroll.salary.index', ['year' => $year, 'month' => $month])
                    ->with('error', 'Approved or paid payroll periods cannot be regenerated without an authorized rollback.');
            }

            $period = $this->generateOrUpdateDraftPeriod($year, $month);

            ActivityLog::log(
                'generate_payroll',
                "Generated draft salary report for {$period->formatted_period}",
                'Payroll'
            );

            return redirect()->route('payroll.salary.index', ['year' => $year, 'month' => $month])
                ->with('success', "Draft salary for {$period->formatted_period} generated successfully.");
        }

        // Filter items based on user criteria
        $filteredItems = $period ? $period->items : collect();

        $selectedBranchId = $request->input('branch_id');
        if ($request->filled('branch_id')) {
            $bId = (int) $selectedBranchId;
            $filteredItems = $filteredItems->filter(fn ($item) => (int) ($item->employee?->branch_id) === $bId);
        }

        $selectedEmployeeId = $request->input('employee_id');
        if ($request->filled('employee_id')) {
            $eId = (int) $selectedEmployeeId;
            $filteredItems = $filteredItems->filter(fn ($item) => (int) $item->employee_id === $eId);
        }

        $selectedSalaryType = $request->input('salary_type');
        if ($request->filled('salary_type')) {
            $sType = strtolower($selectedSalaryType);
            $filteredItems = $filteredItems->filter(fn ($item) => strtolower($item->salary_type_snapshot ?? $item->employee?->salary_type ?? '') === $sType);
        }

        $selectedStatus = $request->input('status');
        if ($request->filled('status')) {
            $sStatus = strtolower($selectedStatus);
            $filteredItems = $filteredItems->filter(fn ($item) => strtolower($item->status) === $sStatus);
        }

        // Calculate payroll summary cards (based on current filtered items)
        $summary = [
            'total_employees' => $filteredItems->count(),
            'total_gross' => (float) $filteredItems->sum('gross_salary'),
            'total_deductions' => (float) $filteredItems->sum(function ($item) {
                return (float) $item->deductions + (float) $item->no_pay_deduction + (float) $item->epf_employee_amount + (float) $item->apit_amount + (float) $item->advances_applied;
            }),
            'total_net' => (float) $filteredItems->sum('net_salary'),
            'total_paid' => (float) $filteredItems->sum('paid_amount'),
            'outstanding_salary' => max(0.0, round((float) $filteredItems->sum('net_salary') - (float) $filteredItems->sum('paid_amount'), 2)),
            'total_epf_employer' => (float) $filteredItems->sum('epf_employer_amount'),
            'total_etf_employer' => (float) $filteredItems->sum('etf_employer_amount'),
        ];

        // Perform pre-approval verification
        $validation = $period ? $this->validateForApproval($period) : [
            'can_approve' => false,
            'blockers' => [],
            'warnings' => [],
        ];

        // Attendance missing days warning check for this month
        $unmarkedDaysWarning = [];
        $checkUntil = $periodDate->isCurrentMonth() ? $today->copy()->subDay() : ($periodDate->isPast() ? $periodDate->copy()->endOfMonth() : null);

        if ($checkUntil && $checkUntil->greaterThanOrEqualTo($periodDate->copy()->startOfMonth())) {
            $activeEmpCount = Employee::where('is_active', true)->count();
            $cursor = $periodDate->copy()->startOfMonth();

            while ($cursor->lessThanOrEqualTo($checkUntil)) {
                if (! $cursor->isSunday()) {
                    $dayStr = $cursor->toDateString();
                    $markedCount = EmployeeAttendance::where('attendance_date', $dayStr)->count();
                    if ($markedCount < $activeEmpCount) {
                        $unmarkedDaysWarning[] = [
                            'date' => $dayStr,
                            'marked' => $markedCount,
                            'expected' => $activeEmpCount,
                            'missing' => $activeEmpCount - $markedCount,
                        ];
                    }
                }
                $cursor->addDay();
            }
        }

        // Year options for selector: 2024 to 2028
        $years = range(today()->year - 2, today()->year + 2);

        // Pending advances available for employees
        $pendingAdvances = EmployeeAdvance::where('status', EmployeeAdvance::STATUS_PENDING)
            ->with('employee')
            ->orderBy('advance_date', 'desc')
            ->get();

        $activeEmployees = Employee::where('is_active', true)->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->orderBy('branch_name')->get();

        return view('payroll.salary.index', compact(
            'year',
            'month',
            'periodDate',
            'period',
            'filteredItems',
            'summary',
            'validation',
            'unmarkedDaysWarning',
            'years',
            'pendingAdvances',
            'activeEmployees',
            'branches',
            'selectedBranchId',
            'selectedEmployeeId',
            'selectedSalaryType',
            'selectedStatus'
        ));
    }

    /**
     * Generate or update draft PayrollPeriod and PayrollItems based on actual attendance,
     * company 5-off-days reconciliation, and wage rates via PayrollCalculationService.
     */
    private function generateOrUpdateDraftPeriod(int $year, int $month): PayrollPeriod
    {
        return $this->calculationService->calculatePeriod($year, $month);
    }

    /**
     * Show single employee detailed salary sheet / payslip.
     */
    public function sheet(PayrollItem $payrollItem): View
    {
        $this->authorizeSalaryView();

        $payrollItem->load([
            'period.approver',
            'employee.wageRates',
            'payments.recorder',
            'advances',
        ]);

        $company = CompanySetting::getSettings();

        // Load advances or recoveries
        $advances = $payrollItem->advances;
        if ($advances->isEmpty() && Schema::hasTable('employee_advance_recoveries')) {
            $recoveredAdvanceIds = EmployeeAdvanceRecovery::where('payroll_item_id', $payrollItem->id)->pluck('employee_advance_id');
            if ($recoveredAdvanceIds->isNotEmpty()) {
                $advances = EmployeeAdvance::whereIn('id', $recoveredAdvanceIds)->get();
            }
        }
        if ($advances->isEmpty() && $payrollItem->period->isDraft()) {
            $monthEnd = Carbon::createFromDate($payrollItem->period->year, $payrollItem->period->month, 1)->endOfMonth()->toDateString();
            $advances = EmployeeAdvance::where('employee_id', $payrollItem->employee_id)
                ->whereIn('status', [
                    EmployeeAdvance::STATUS_PENDING,
                    EmployeeAdvance::STATUS_DISBURSED,
                    EmployeeAdvance::STATUS_PARTIALLY_RECOVERED,
                ])
                ->where('advance_date', '<=', $monthEnd)
                ->get();
        }

        return view('payroll.salary.sheet', compact('payrollItem', 'company', 'advances'));
    }

    /**
     * Printable payslip layout.
     */
    public function print(PayrollItem $payrollItem): View
    {
        $this->authorizeSalaryView();

        $payrollItem->load([
            'period.approver',
            'employee',
            'payments.recorder',
            'advances',
        ]);

        $company = CompanySetting::getSettings();

        $advances = $payrollItem->advances;
        if ($advances->isEmpty() && Schema::hasTable('employee_advance_recoveries')) {
            $recoveredAdvanceIds = EmployeeAdvanceRecovery::where('payroll_item_id', $payrollItem->id)->pluck('employee_advance_id');
            if ($recoveredAdvanceIds->isNotEmpty()) {
                $advances = EmployeeAdvance::whereIn('id', $recoveredAdvanceIds)->get();
            }
        }
        if ($advances->isEmpty() && $payrollItem->period->isDraft()) {
            $monthEnd = Carbon::createFromDate($payrollItem->period->year, $payrollItem->period->month, 1)->endOfMonth()->toDateString();
            $advances = EmployeeAdvance::where('employee_id', $payrollItem->employee_id)
                ->whereIn('status', [
                    EmployeeAdvance::STATUS_PENDING,
                    EmployeeAdvance::STATUS_DISBURSED,
                    EmployeeAdvance::STATUS_PARTIALLY_RECOVERED,
                ])
                ->where('advance_date', '<=', $monthEnd)
                ->get();
        }

        return view('payroll.salary.print', compact('payrollItem', 'company', 'advances'));
    }

    /**
     * Update adjustments (Overtime, Bonus, Deductions, Notes) on draft payroll item.
     */
    public function updateItem(Request $request, PayrollItem $payrollItem): RedirectResponse
    {
        $this->authorizeAdmin();

        if (! $payrollItem->period->isModifiable()) {
            return back()->with('error', 'Adjustments can only be updated when payroll is in draft or reviewed status.');
        }

        $validated = $request->validate([
            'overtime_amount' => 'required|numeric|min:0',
            'bonus_amount' => 'required|numeric|min:0',
            'deductions' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $payrollItem->overtime_amount = $validated['overtime_amount'];
        $payrollItem->bonus_amount = $validated['bonus_amount'];
        $payrollItem->deductions = $validated['deductions'];
        $payrollItem->notes = $validated['notes'] ?? null;
        $payrollItem->save();

        $payrollItem->recalculate();

        // If period was previously marked as reviewed, reset it to draft so modified figures are re-reviewed
        if ($payrollItem->period->isReviewed()) {
            $payrollItem->period->update(['status' => PayrollPeriod::STATUS_DRAFT]);
        }

        ActivityLog::log(
            'update_payroll_item',
            "Updated payroll adjustments for {$payrollItem->employee_name_snapshot} ({$payrollItem->period->formatted_period})",
            'Payroll'
        );

        return back()->with('success', "Payroll adjustments for {$payrollItem->employee_name_snapshot} updated successfully.");
    }

    /**
     * Comprehensive pre-approval audit: validate rates, net salary, attendance, advances & taxes.
     */
    public function validateForApproval(PayrollPeriod $period): array
    {
        $period->loadMissing(['items.employee']);
        $blockers = [];
        $warnings = [];

        if ($period->items->isEmpty()) {
            $blockers[] = 'Payroll has no employee items to approve.';
        }

        foreach ($period->items as $item) {
            $empName = $item->employee_name_snapshot ?: ($item->employee?->name ?: "Employee #{$item->employee_id}");

            // 1. Critical Blocker: Negative net salary (unresolved deduction shortfall)
            if ((float) $item->net_salary < 0) {
                $blockers[] = "{$empName} has negative net pay (LKR ".number_format($item->net_salary, 2).'). Unresolved deduction shortfall must be adjusted before approval.';
            }

            // 2. Critical Blocker: Zero or negative wage/basic rates
            $isMonthly = $item->isMonthly();
            if ($isMonthly && (float) $item->monthly_basic_snapshot <= 0) {
                $blockers[] = "{$empName} (Monthly) has zero basic salary configured in payroll snapshot.";
            } elseif (! $isMonthly && (float) $item->daily_wage_snapshot <= 0) {
                $blockers[] = "{$empName} (Daily) has zero daily wage configured in payroll snapshot.";
            }

            // 3. Warning: EPF eligible but missing EPF number
            if ($item->employee && $item->employee->is_epf_eligible && empty($item->employee->epf_number)) {
                $warnings[] = "{$empName} is EPF eligible but does not have an EPF member number recorded.";
            }
        }

        // 4. Warning: Incomplete attendance dates
        $periodDate = Carbon::createFromDate($period->year, $period->month, 1, 'Asia/Colombo');
        $today = Carbon::today('Asia/Colombo');
        $checkUntil = $periodDate->isCurrentMonth() ? $today->copy()->subDay() : ($periodDate->isPast() ? $periodDate->copy()->endOfMonth() : null);
        if ($checkUntil && $checkUntil->greaterThanOrEqualTo($periodDate->copy()->startOfMonth())) {
            $activeEmpCount = Employee::where('is_active', true)->count();
            $cursor = $periodDate->copy()->startOfMonth();
            $unmarkedDays = 0;
            while ($cursor->lessThanOrEqualTo($checkUntil)) {
                if (! $cursor->isSunday()) {
                    $markedCount = EmployeeAttendance::where('attendance_date', $cursor->toDateString())->count();
                    if ($markedCount < $activeEmpCount) {
                        $unmarkedDays++;
                    }
                }
                $cursor->addDay();
            }
            if ($unmarkedDays > 0) {
                $warnings[] = "{$unmarkedDays} working dates have unrecorded or incomplete staff attendance records.";
            }
        }

        // 5. Warning: Pending unapproved bonuses
        if (Schema::hasTable('employee_bonuses')) {
            $pendingBonuses = EmployeeBonus::where('period_year', $period->year)
                ->where('period_month', $period->month)
                ->where('status', EmployeeBonus::STATUS_PENDING)
                ->count();
            if ($pendingBonuses > 0) {
                $warnings[] = "{$pendingBonuses} employee bonus request(s) are still pending approval for this month.";
            }
        }

        // 6. Warning: Pending unapproved overtime
        if (Schema::hasTable('employee_overtimes')) {
            $monthStart = $periodDate->copy()->startOfMonth()->toDateString();
            $monthEnd = $periodDate->copy()->endOfMonth()->toDateString();
            $pendingOT = EmployeeOvertime::whereBetween('overtime_date', [$monthStart, $monthEnd])
                ->where('status', EmployeeOvertime::STATUS_PENDING)
                ->count();
            if ($pendingOT > 0) {
                $warnings[] = "{$pendingOT} employee overtime record(s) are still pending approval for this month.";
            }
        }

        return [
            'can_approve' => empty($blockers),
            'blockers' => $blockers,
            'warnings' => $warnings,
        ];
    }

    /**
     * Mark draft payroll as reviewed after performing pre-approval verification.
     */
    public function markReviewed(PayrollPeriod $period): RedirectResponse
    {
        $this->authorizeAdmin();

        if (! $period->isDraft()) {
            return back()->with('error', 'Only draft payroll periods can be marked as reviewed.');
        }

        $validation = $this->validateForApproval($period);
        if (! $validation['can_approve']) {
            return back()->with('error', 'Cannot mark payroll as reviewed. Critical blockers remain: '.implode(' | ', $validation['blockers']));
        }

        $period->update([
            'status' => PayrollPeriod::STATUS_REVIEWED,
        ]);

        ActivityLog::log(
            'review_payroll',
            "Verified and marked monthly payroll for {$period->formatted_period} as Reviewed.",
            'Payroll'
        );

        return redirect()->route('payroll.salary.index', ['year' => $period->year, 'month' => $period->month])
            ->with('success', "Payroll for {$period->formatted_period} has been reviewed and verified. Ready for Admin approval.");
    }

    /**
     * Finalize and approve the monthly payroll period.
     */
    public function approve(Request $request, PayrollPeriod $period): RedirectResponse
    {
        $this->authorizeAdmin();

        if (! $period->isModifiable()) {
            return back()->with('error', 'This payroll period is already approved or locked.');
        }

        $validation = $this->validateForApproval($period);
        if (! $validation['can_approve']) {
            return back()->with('error', 'Payroll approval blocked! Please resolve: '.implode(' | ', $validation['blockers']));
        }

        $periodDate = Carbon::createFromDate($period->year, $period->month, 1);
        $monthEnd = $periodDate->copy()->endOfMonth()->toDateString();

        DB::transaction(function () use ($period, $monthEnd) {
            $period->loadMissing('items');

            foreach ($period->items as $item) {
                $breakdown = $item->attendance_breakdown ?? [];
                $plannedRecoveries = $breakdown['planned_advance_recoveries'] ?? [];

                if (! empty($plannedRecoveries)) {
                    foreach ($plannedRecoveries as $plan) {
                        $adv = EmployeeAdvance::find($plan['advance_id']);
                        if (! $adv) {
                            continue;
                        }

                        $amountToRecover = (float) $plan['recovery_amount'];

                        if (Schema::hasTable('employee_advance_recoveries')) {
                            EmployeeAdvanceRecovery::create([
                                'employee_advance_id' => $adv->id,
                                'payroll_period_id' => $period->id,
                                'payroll_item_id' => $item->id,
                                'employee_id' => $item->employee_id,
                                'recovery_date' => $monthEnd,
                                'amount' => $amountToRecover,
                                'status' => EmployeeAdvanceRecovery::STATUS_RECOVERED,
                                'created_by' => Auth::id(),
                            ]);
                        }

                        $newRemaining = max(0.0, round($adv->remaining_balance - $amountToRecover, 2));

                        if ($newRemaining <= 0.001) {
                            $adv->update([
                                'status' => EmployeeAdvance::STATUS_DEDUCTED,
                                'payroll_item_id' => $item->id,
                            ]);
                        } else {
                            $adv->update([
                                'status' => EmployeeAdvance::STATUS_PARTIALLY_RECOVERED,
                            ]);
                        }
                    }
                } else {
                    // Fallback for legacy items or test scenarios without planned_advance_recoveries
                    $advancesToDeduct = EmployeeAdvance::where('employee_id', $item->employee_id)
                        ->whereIn('status', [
                            EmployeeAdvance::STATUS_DISBURSED,
                            EmployeeAdvance::STATUS_PARTIALLY_RECOVERED,
                        ])
                        ->where('advance_date', '<=', $monthEnd)
                        ->get();

                    foreach ($advancesToDeduct as $adv) {
                        $balance = $adv->remaining_balance;
                        if ($balance <= 0) {
                            continue;
                        }

                        if (Schema::hasTable('employee_advance_recoveries')) {
                            EmployeeAdvanceRecovery::create([
                                'employee_advance_id' => $adv->id,
                                'payroll_period_id' => $period->id,
                                'payroll_item_id' => $item->id,
                                'employee_id' => $item->employee_id,
                                'recovery_date' => $monthEnd,
                                'amount' => $balance,
                                'status' => EmployeeAdvanceRecovery::STATUS_RECOVERED,
                                'created_by' => Auth::id(),
                            ]);
                        }

                        $adv->update([
                            'status' => EmployeeAdvance::STATUS_DEDUCTED,
                            'payroll_item_id' => $item->id,
                        ]);
                    }
                }

                $item->update([
                    'status' => PayrollItem::STATUS_APPROVED,
                ]);

                // Link and update approved bonuses to applied
                if (Schema::hasTable('employee_bonuses')) {
                    EmployeeBonus::where('employee_id', $item->employee_id)
                        ->where('period_year', $period->year)
                        ->where('period_month', $period->month)
                        ->where('status', EmployeeBonus::STATUS_APPROVED)
                        ->update([
                            'status' => EmployeeBonus::STATUS_APPLIED,
                            'payroll_item_id' => $item->id,
                        ]);
                }

                // Link and update approved overtime to applied
                if (Schema::hasTable('employee_overtimes')) {
                    EmployeeOvertime::where('employee_id', $item->employee_id)
                        ->whereBetween('overtime_date', [$monthEnd ? Carbon::parse($monthEnd)->startOfMonth()->toDateString() : today()->startOfMonth()->toDateString(), $monthEnd])
                        ->where('status', EmployeeOvertime::STATUS_APPROVED)
                        ->update([
                            'status' => EmployeeOvertime::STATUS_APPLIED,
                            'payroll_item_id' => $item->id,
                        ]);
                }
            }

            $period->update([
                'status' => PayrollPeriod::STATUS_APPROVED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
        });

        ActivityLog::log(
            'approve_payroll',
            "Approved payroll for {$period->formatted_period} (Total Net: LKR ".number_format($period->total_net, 2).')',
            'Payroll'
        );

        return redirect()->route('payroll.salary.index', ['year' => $period->year, 'month' => $period->month])
            ->with('success', "Payroll for {$period->formatted_period} has been approved successfully.");
    }

    /**
     * Record a salary payment (full or partial) for an approved payroll item.
     */
    public function recordPayment(Request $request, PayrollItem $payrollItem): RedirectResponse
    {
        $this->authorizeAdmin();

        if ($payrollItem->period->isDraft()) {
            return back()->with('error', 'Cannot record payment for a draft payroll. Please approve payroll first.');
        }

        if ($payrollItem->balance_due <= 0) {
            return back()->with('error', 'This salary has already been fully paid.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:'.$payrollItem->balance_due,
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|in:cash,bank_transfer,cheque,other',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $paymentNumber = PayrollPayment::generateNextPaymentNumber();

        DB::transaction(function () use ($payrollItem, $validated, $paymentNumber) {
            PayrollPayment::create([
                'payroll_item_id' => $payrollItem->id,
                'payment_number' => $paymentNumber,
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'recorded_by' => Auth::id(),
            ]);

            $payrollItem->recalculate();
        });

        ActivityLog::log(
            'record_salary_payment',
            'Recorded salary payment of LKR '.number_format($validated['amount'], 2)." for {$payrollItem->employee_name_snapshot} ({$payrollItem->period->formatted_period})",
            'Payroll'
        );

        return back()->with('success', 'Payment of LKR '.number_format($validated['amount'], 2)." recorded successfully ({$paymentNumber}).");
    }

    /**
     * Rollback an approved payroll period back to draft (Admin only).
     * Reverses any employee advance recoveries applied during approval.
     */
    public function rollbackApproval(PayrollPeriod $period): RedirectResponse
    {
        $this->authorizeAdmin();

        if ($period->status !== PayrollPeriod::STATUS_APPROVED) {
            return back()->with('error', 'Only approved payroll periods can be rolled back.');
        }

        // Check if any salary payments have already been disbursed for this period
        $hasPayments = PayrollPayment::whereIn('payroll_item_id', $period->items()->pluck('id'))->exists();
        if ($hasPayments) {
            return back()->with('error', 'Cannot rollback payroll because payments have already been recorded. Please reverse recorded payments first.');
        }

        DB::transaction(function () use ($period) {
            // Find recoveries recorded for this payroll period
            if (Schema::hasTable('employee_advance_recoveries')) {
                $recoveries = EmployeeAdvanceRecovery::where('payroll_period_id', $period->id)->get();
                $advanceIds = $recoveries->pluck('employee_advance_id')->unique();

                // Delete recoveries recorded for this payroll period
                EmployeeAdvanceRecovery::where('payroll_period_id', $period->id)->delete();

                // Reset advance statuses based on remaining recoveries
                foreach ($advanceIds as $advId) {
                    $adv = EmployeeAdvance::find($advId);
                    if (! $adv) {
                        continue;
                    }

                    $recovered = $adv->recovered_amount;

                    if ($recovered > 0.001) {
                        $adv->update([
                            'status' => EmployeeAdvance::STATUS_PARTIALLY_RECOVERED,
                            'payroll_item_id' => null,
                        ]);
                    } else {
                        $newStatus = ($adv->disbursed_at || $adv->disbursed_by)
                            ? EmployeeAdvance::STATUS_DISBURSED
                            : EmployeeAdvance::STATUS_APPROVED;

                        $adv->update([
                            'status' => $newStatus,
                            'payroll_item_id' => null,
                        ]);
                    }
                }
            } else {
                // Legacy fallback: reset advances that pointed to this period's items
                EmployeeAdvance::whereIn('payroll_item_id', $period->items()->pluck('id'))
                    ->update([
                        'status' => EmployeeAdvance::STATUS_PENDING,
                        'payroll_item_id' => null,
                    ]);
            }

            // Revert applied bonuses back to approved
            if (Schema::hasTable('employee_bonuses')) {
                EmployeeBonus::whereIn('payroll_item_id', $period->items()->pluck('id'))
                    ->update([
                        'status' => EmployeeBonus::STATUS_APPROVED,
                        'payroll_item_id' => null,
                    ]);
            }

            // Revert applied overtime back to approved
            if (Schema::hasTable('employee_overtimes')) {
                EmployeeOvertime::whereIn('payroll_item_id', $period->items()->pluck('id'))
                    ->update([
                        'status' => EmployeeOvertime::STATUS_APPROVED,
                        'payroll_item_id' => null,
                    ]);
            }

            // Reset items and period back to draft
            $period->items()->update([
                'status' => PayrollItem::STATUS_DRAFT,
            ]);

            $period->update([
                'status' => PayrollPeriod::STATUS_DRAFT,
                'approved_by' => null,
                'approved_at' => null,
            ]);
        });

        ActivityLog::log(
            'rollback_payroll',
            "Rolled back approved payroll for {$period->formatted_period} to draft. Advance recoveries reverted.",
            'Payroll'
        );

        return redirect()->route('payroll.salary.index', ['year' => $period->year, 'month' => $period->month])
            ->with('success', "Payroll for {$period->formatted_period} has been rolled back to draft.");
    }

    /**
     * Record an employee salary advance.
     */
    public function storeAdvance(Request $request): RedirectResponse
    {
        return app(SalaryAdvanceController::class)->store($request);
    }
}
