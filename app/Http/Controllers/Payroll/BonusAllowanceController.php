<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeAllowance;
use App\Models\EmployeeBonus;
use App\Models\EmployeeOvertime;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class BonusAllowanceController extends Controller
{
    private function authorizeAdmin(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Unauthorized action. Only administrators can perform this action.');
        }
    }

    /**
     * Display listing of bonuses, allowances, and overtime with filters and metrics.
     */
    public function index(Request $request): View
    {
        $currentTab = $request->query('tab', 'bonuses');
        if (! in_array($currentTab, ['bonuses', 'allowances', 'overtime'], true)) {
            $currentTab = 'bonuses';
        }

        $employeeId = $request->filled('employee_id') ? $request->integer('employee_id') : null;
        $branchId = $request->filled('branch_id') ? $request->integer('branch_id') : null;
        $status = $request->filled('status') && $request->status !== 'all' ? $request->string('status')->toString() : null;
        $year = $request->filled('year') ? $request->integer('year') : (int) now()->format('Y');
        $month = $request->filled('month') ? $request->integer('month') : null;

        // 1. Query Bonuses
        $bonusesQuery = EmployeeBonus::with(['employee', 'branch', 'approver', 'creator', 'payrollItem'])
            ->latest('period_year')
            ->latest('period_month')
            ->latest('id');

        if ($employeeId) {
            $bonusesQuery->where('employee_id', $employeeId);
        }
        if ($branchId) {
            $bonusesQuery->where('branch_id', $branchId);
        }
        if ($status) {
            $bonusesQuery->where('status', $status);
        }
        if ($year) {
            $bonusesQuery->where('period_year', $year);
        }
        if ($month) {
            $bonusesQuery->where('period_month', $month);
        }
        $bonuses = $bonusesQuery->paginate(15, ['*'], 'bonuses_page')->withQueryString();

        // 2. Query Allowances
        $allowancesQuery = EmployeeAllowance::with(['employee', 'branch', 'approver', 'creator'])
            ->latest('id');

        if ($employeeId) {
            $allowancesQuery->where('employee_id', $employeeId);
        }
        if ($branchId) {
            $allowancesQuery->where('branch_id', $branchId);
        }
        if ($status) {
            $allowancesQuery->where('status', $status);
        }
        if ($year && $month) {
            $allowancesQuery->where(function ($q) use ($year, $month) {
                $q->where('is_recurring', true)
                    ->orWhere(function ($sub) use ($year, $month) {
                        $sub->where('period_year', $year)->where('period_month', $month);
                    });
            });
        }
        $allowances = $allowancesQuery->paginate(15, ['*'], 'allowances_page')->withQueryString();

        // 3. Query Overtime
        $overtimeQuery = EmployeeOvertime::with(['employee', 'branch', 'approver', 'creator', 'payrollItem'])
            ->latest('overtime_date')
            ->latest('id');

        if ($employeeId) {
            $overtimeQuery->where('employee_id', $employeeId);
        }
        if ($branchId) {
            $overtimeQuery->where('branch_id', $branchId);
        }
        if ($status) {
            $overtimeQuery->where('status', $status);
        }
        if ($year) {
            $overtimeQuery->whereYear('overtime_date', $year);
        }
        if ($month) {
            $overtimeQuery->whereMonth('overtime_date', $month);
        }
        $overtimes = $overtimeQuery->paginate(15, ['*'], 'overtimes_page')->withQueryString();

        // Metrics calculations
        $totalApprovedBonuses = 0.00;
        $totalActiveAllowances = 0.00;
        $totalApprovedOvertimeAmount = 0.00;
        $totalApprovedOvertimeHours = 0.00;
        $pendingApprovalsCount = 0;

        if (Schema::hasTable('employee_bonuses')) {
            $totalApprovedBonuses = (float) EmployeeBonus::whereIn('status', [EmployeeBonus::STATUS_APPROVED, EmployeeBonus::STATUS_APPLIED])
                ->when($year, fn ($q) => $q->where('period_year', $year))
                ->when($month, fn ($q) => $q->where('period_month', $month))
                ->sum('amount');

            $pendingApprovalsCount += EmployeeBonus::where('status', EmployeeBonus::STATUS_PENDING)->count();
        }

        if (Schema::hasTable('employee_allowances')) {
            $totalActiveAllowances = (float) EmployeeAllowance::whereIn('status', [EmployeeAllowance::STATUS_ACTIVE, EmployeeAllowance::STATUS_APPROVED])
                ->where('is_recurring', true)
                ->sum('amount');

            $pendingApprovalsCount += EmployeeAllowance::where('status', EmployeeAllowance::STATUS_PENDING)->count();
        }

        if (Schema::hasTable('employee_overtimes')) {
            $otApprovedQuery = EmployeeOvertime::whereIn('status', [EmployeeOvertime::STATUS_APPROVED, EmployeeOvertime::STATUS_APPLIED])
                ->when($year, fn ($q) => $q->whereYear('overtime_date', $year))
                ->when($month, fn ($q) => $q->whereMonth('overtime_date', $month));

            $totalApprovedOvertimeAmount = (float) (clone $otApprovedQuery)->sum('amount');
            $totalApprovedOvertimeHours = (float) (clone $otApprovedQuery)->sum('hours');
            $pendingApprovalsCount += EmployeeOvertime::where('status', EmployeeOvertime::STATUS_PENDING)->count();
        }

        $employees = Employee::where('is_active', true)->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->orderBy('branch_name')->get();

        return view('payroll.bonuses-allowances.index', compact(
            'currentTab',
            'bonuses',
            'allowances',
            'overtimes',
            'employees',
            'branches',
            'totalApprovedBonuses',
            'totalActiveAllowances',
            'totalApprovedOvertimeAmount',
            'totalApprovedOvertimeHours',
            'pendingApprovalsCount',
            'year',
            'month'
        ));
    }

    /**
     * Store new employee bonus(es). Supports single employee, selected employees, or all eligible employees.
     */
    public function storeBonus(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'target_type' => 'required|in:single,selected,all',
            'employee_id' => 'required_if:target_type,single|nullable|exists:employees,id',
            'employee_ids' => 'required_if:target_type,selected|nullable|array',
            'employee_ids.*' => 'exists:employees,id',
            'branch_id' => 'nullable|exists:branches,id',
            'bonus_type' => 'required|string|in:performance,attendance,special,festival,incentive,other',
            'calculation_type' => 'required|in:fixed,percentage',
            'amount' => 'required_if:calculation_type,fixed|nullable|numeric|min:0.01',
            'percentage_rate' => 'required_if:calculation_type,percentage|nullable|numeric|min:0.01|max:100',
            'period_year' => 'required|integer|min:2020|max:2035',
            'period_month' => 'required|integer|min:1|max:12',
            'effective_date' => 'nullable|date',
            'status' => 'required|in:pending,approved',
            'notes' => 'nullable|string|max:500',
        ]);

        $employees = collect();
        if ($validated['target_type'] === 'single') {
            $employees = Employee::where('id', $validated['employee_id'])->get();
        } elseif ($validated['target_type'] === 'selected') {
            $employees = Employee::whereIn('id', $validated['employee_ids'] ?? [])->get();
        } else {
            // All active employees, filtered optionally by branch
            $q = Employee::where('is_active', true);
            if (! empty($validated['branch_id'])) {
                $q->where('branch_id', $validated['branch_id']);
            }
            $employees = $q->get();
        }

        if ($employees->isEmpty()) {
            return back()->with('error', 'No eligible employees were found for the selected bonus criteria.');
        }

        $createdCount = 0;
        $user = Auth::user();

        DB::transaction(function () use ($employees, $validated, $user, &$createdCount) {
            foreach ($employees as $emp) {
                // Prevent duplicate bonuses of the same type for the same employee in the same period
                $exists = EmployeeBonus::where('employee_id', $emp->id)
                    ->where('bonus_type', $validated['bonus_type'])
                    ->where('period_year', $validated['period_year'])
                    ->where('period_month', $validated['period_month'])
                    ->whereIn('status', [EmployeeBonus::STATUS_PENDING, EmployeeBonus::STATUS_APPROVED, EmployeeBonus::STATUS_APPLIED])
                    ->exists();

                if ($exists) {
                    continue; // Skip already registered bonus
                }

                $fixedAmt = (float) ($validated['amount'] ?? 0.00);
                $pct = isset($validated['percentage_rate']) ? (float) $validated['percentage_rate'] : null;
                $finalAmount = EmployeeBonus::calculateAmount($emp, $validated['calculation_type'], $fixedAmt, $pct);

                if ($finalAmount <= 0) {
                    continue;
                }

                $isApproved = $validated['status'] === EmployeeBonus::STATUS_APPROVED;

                EmployeeBonus::create([
                    'bonus_number' => EmployeeBonus::generateNextBonusNumber(),
                    'employee_id' => $emp->id,
                    'branch_id' => $emp->branch_id ?? $validated['branch_id'],
                    'bonus_type' => $validated['bonus_type'],
                    'calculation_type' => $validated['calculation_type'],
                    'percentage_rate' => $pct,
                    'amount' => $finalAmount,
                    'period_year' => $validated['period_year'],
                    'period_month' => $validated['period_month'],
                    'effective_date' => $validated['effective_date'] ?? Carbon::createFromDate($validated['period_year'], $validated['period_month'], 1)->toDateString(),
                    'status' => $validated['status'],
                    'approved_by' => $isApproved ? $user->id : null,
                    'approved_at' => $isApproved ? now() : null,
                    'notes' => $validated['notes'] ?? null,
                    'created_by' => $user->id,
                ]);

                $createdCount++;
            }
        });

        ActivityLog::log(
            'register_bonus',
            "Registered {$createdCount} bonus(es) for period {$validated['period_year']}-{$validated['period_month']} [Type: {$validated['bonus_type']}]",
            'Payroll'
        );

        return back()->with('success', "Successfully registered {$createdCount} employee bonus(es).");
    }

    /**
     * Approve a pending bonus.
     */
    public function approveBonus(EmployeeBonus $bonus): RedirectResponse
    {
        $this->authorizeAdmin();

        if ($bonus->status !== EmployeeBonus::STATUS_PENDING) {
            return back()->with('error', 'Only pending bonuses can be approved.');
        }

        $bonus->update([
            'status' => EmployeeBonus::STATUS_APPROVED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        ActivityLog::log(
            'approve_bonus',
            "Approved bonus {$bonus->bonus_number} of LKR ".number_format($bonus->amount, 2)." for {$bonus->employee->name}",
            'Payroll'
        );

        return back()->with('success', "Bonus {$bonus->bonus_number} for {$bonus->employee->name} has been approved.");
    }

    /**
     * Reject a pending bonus.
     */
    public function rejectBonus(Request $request, EmployeeBonus $bonus): RedirectResponse
    {
        $this->authorizeAdmin();

        if ($bonus->status !== EmployeeBonus::STATUS_PENDING) {
            return back()->with('error', 'Only pending bonuses can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $bonus->update([
            'status' => EmployeeBonus::STATUS_REJECTED,
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        ActivityLog::log(
            'reject_bonus',
            "Rejected bonus {$bonus->bonus_number} for {$bonus->employee->name}. Reason: {$validated['rejection_reason']}",
            'Payroll'
        );

        return back()->with('success', "Bonus {$bonus->bonus_number} has been rejected.");
    }

    /**
     * Cancel an unpaid / unapplied bonus.
     */
    public function cancelBonus(Request $request, EmployeeBonus $bonus): RedirectResponse
    {
        $this->authorizeAdmin();

        if (! $bonus->canBeCancelled()) {
            return back()->with('error', "Bonus {$bonus->bonus_number} cannot be cancelled because it has already been applied to finalized payroll.");
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $bonus->update([
            'status' => EmployeeBonus::STATUS_CANCELLED,
            'cancelled_by' => Auth::id(),
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        ActivityLog::log(
            'cancel_bonus',
            "Cancelled bonus {$bonus->bonus_number} for {$bonus->employee->name}. Reason: {$validated['cancellation_reason']}",
            'Payroll'
        );

        return back()->with('success', "Bonus {$bonus->bonus_number} has been cancelled.");
    }

    /**
     * Store new allowance for employee(s).
     */
    public function storeAllowance(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'target_type' => 'required|in:single,selected',
            'employee_id' => 'required_if:target_type,single|nullable|exists:employees,id',
            'employee_ids' => 'required_if:target_type,selected|nullable|array',
            'employee_ids.*' => 'exists:employees,id',
            'allowance_type' => 'required|string|in:transport,meal,special,other',
            'title' => 'nullable|string|max:150',
            'amount' => 'required|numeric|min:0.01',
            'is_recurring' => 'required|boolean',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'period_year' => 'nullable|integer|min:2020|max:2035',
            'period_month' => 'nullable|integer|min:1|max:12',
            'is_epf_eligible' => 'nullable|boolean',
            'is_etf_eligible' => 'nullable|boolean',
            'is_taxable' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $employees = ($validated['target_type'] === 'single')
            ? Employee::where('id', $validated['employee_id'])->get()
            : Employee::whereIn('id', $validated['employee_ids'] ?? [])->get();

        $user = Auth::user();
        $createdCount = 0;

        DB::transaction(function () use ($employees, $validated, $user, &$createdCount) {
            foreach ($employees as $emp) {
                EmployeeAllowance::create([
                    'allowance_number' => EmployeeAllowance::generateNextAllowanceNumber(),
                    'employee_id' => $emp->id,
                    'branch_id' => $emp->branch_id,
                    'allowance_type' => $validated['allowance_type'],
                    'title' => $validated['title'] ?? null,
                    'amount' => $validated['amount'],
                    'is_recurring' => (bool) $validated['is_recurring'],
                    'effective_from' => $validated['effective_from'],
                    'effective_to' => $validated['effective_to'] ?? null,
                    'period_year' => ! empty($validated['period_year']) ? (int) $validated['period_year'] : null,
                    'period_month' => ! empty($validated['period_month']) ? (int) $validated['period_month'] : null,
                    'is_epf_eligible' => (bool) ($validated['is_epf_eligible'] ?? false),
                    'is_etf_eligible' => (bool) ($validated['is_etf_eligible'] ?? false),
                    'is_taxable' => (bool) ($validated['is_taxable'] ?? false),
                    'status' => EmployeeAllowance::STATUS_ACTIVE,
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                    'notes' => $validated['notes'] ?? null,
                    'created_by' => $user->id,
                ]);

                $createdCount++;
            }
        });

        ActivityLog::log(
            'register_allowance',
            "Configured {$createdCount} allowance(s) [Type: {$validated['allowance_type']}, Amount: LKR ".number_format($validated['amount'], 2).']',
            'Payroll'
        );

        return back()->with('success', "Configured {$createdCount} employee allowance(s) successfully.");
    }

    /**
     * Toggle allowance status between Active and Paused.
     */
    public function toggleAllowanceStatus(EmployeeAllowance $allowance): RedirectResponse
    {
        $this->authorizeAdmin();

        $newStatus = ($allowance->status === EmployeeAllowance::STATUS_ACTIVE)
            ? EmployeeAllowance::STATUS_PAUSED
            : EmployeeAllowance::STATUS_ACTIVE;

        $allowance->update(['status' => $newStatus]);

        ActivityLog::log(
            'toggle_allowance_status',
            "Toggled allowance {$allowance->allowance_number} for {$allowance->employee->name} to {$newStatus}",
            'Payroll'
        );

        return back()->with('success', "Allowance {$allowance->allowance_number} status updated to ".ucfirst($newStatus).'.');
    }

    /**
     * Cancel an allowance.
     */
    public function cancelAllowance(Request $request, EmployeeAllowance $allowance): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $allowance->update([
            'status' => EmployeeAllowance::STATUS_CANCELLED,
            'cancelled_by' => Auth::id(),
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        ActivityLog::log(
            'cancel_allowance',
            "Cancelled allowance {$allowance->allowance_number} for {$allowance->employee->name}",
            'Payroll'
        );

        return back()->with('success', "Allowance {$allowance->allowance_number} has been cancelled.");
    }

    /**
     * Record Overtime for an employee.
     */
    public function storeOvertime(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'overtime_date' => 'required|date',
            'hours' => 'required|numeric|min:0.5|max:24',
            'rate_multiplier' => 'required|numeric|min:1.0|max:4.0',
            'custom_hourly_rate' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,approved',
            'notes' => 'nullable|string|max:500',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

        // Prevent duplicate overtime entry on same date unless previously cancelled or rejected
        $existing = EmployeeOvertime::where('employee_id', $employee->id)
            ->where('overtime_date', $validated['overtime_date'])
            ->whereIn('status', [EmployeeOvertime::STATUS_PENDING, EmployeeOvertime::STATUS_APPROVED, EmployeeOvertime::STATUS_APPLIED])
            ->first();

        if ($existing) {
            return back()->with('error', "Overtime record ({$existing->overtime_number}) already exists for {$employee->name} on {$validated['overtime_date']}.");
        }

        $hourlyRate = ! empty($validated['custom_hourly_rate']) && (float) $validated['custom_hourly_rate'] > 0
            ? (float) $validated['custom_hourly_rate']
            : EmployeeOvertime::calculateHourlyRate($employee);

        $hours = (float) $validated['hours'];
        $multiplier = (float) $validated['rate_multiplier'];
        $amount = EmployeeOvertime::calculateAmount($hours, $hourlyRate, $multiplier);

        $isApproved = $validated['status'] === EmployeeOvertime::STATUS_APPROVED;
        $user = Auth::user();

        $overtime = EmployeeOvertime::create([
            'overtime_number' => EmployeeOvertime::generateNextOvertimeNumber(),
            'employee_id' => $employee->id,
            'branch_id' => $employee->branch_id,
            'overtime_date' => $validated['overtime_date'],
            'hours' => $hours,
            'hourly_rate' => $hourlyRate,
            'rate_multiplier' => $multiplier,
            'amount' => $amount,
            'status' => $validated['status'],
            'approved_by' => $isApproved ? $user->id : null,
            'approved_at' => $isApproved ? now() : null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => $user->id,
        ]);

        ActivityLog::log(
            'record_overtime',
            "Recorded {$hours}h overtime for {$employee->name} on {$validated['overtime_date']} (LKR ".number_format($amount, 2).')',
            'Payroll'
        );

        return back()->with('success', "Overtime of {$hours} hrs recorded successfully for {$employee->name} ({$overtime->overtime_number}).");
    }

    /**
     * Approve pending overtime.
     */
    public function approveOvertime(EmployeeOvertime $overtime): RedirectResponse
    {
        $this->authorizeAdmin();

        if ($overtime->status !== EmployeeOvertime::STATUS_PENDING) {
            return back()->with('error', 'Only pending overtime can be approved.');
        }

        $overtime->update([
            'status' => EmployeeOvertime::STATUS_APPROVED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        ActivityLog::log(
            'approve_overtime',
            "Approved overtime {$overtime->overtime_number} for {$overtime->employee->name}",
            'Payroll'
        );

        return back()->with('success', "Overtime {$overtime->overtime_number} approved successfully.");
    }

    /**
     * Reject pending overtime.
     */
    public function rejectOvertime(Request $request, EmployeeOvertime $overtime): RedirectResponse
    {
        $this->authorizeAdmin();

        if ($overtime->status !== EmployeeOvertime::STATUS_PENDING) {
            return back()->with('error', 'Only pending overtime can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $overtime->update([
            'status' => EmployeeOvertime::STATUS_REJECTED,
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        ActivityLog::log(
            'reject_overtime',
            "Rejected overtime {$overtime->overtime_number} for {$overtime->employee->name}. Reason: {$validated['rejection_reason']}",
            'Payroll'
        );

        return back()->with('success', "Overtime {$overtime->overtime_number} has been rejected.");
    }

    /**
     * Cancel an overtime record.
     */
    public function cancelOvertime(Request $request, EmployeeOvertime $overtime): RedirectResponse
    {
        $this->authorizeAdmin();

        if (! $overtime->canBeCancelled()) {
            return back()->with('error', "Overtime {$overtime->overtime_number} cannot be cancelled because it is already finalized in approved payroll.");
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $overtime->update([
            'status' => EmployeeOvertime::STATUS_CANCELLED,
            'cancelled_by' => Auth::id(),
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        ActivityLog::log(
            'cancel_overtime',
            "Cancelled overtime {$overtime->overtime_number} for {$overtime->employee->name}",
            'Payroll'
        );

        return back()->with('success', "Overtime {$overtime->overtime_number} has been cancelled.");
    }

    /**
     * API helper to calculate hourly rate and estimate amount for live UI preview.
     */
    public function calculateOvertimeRate(Request $request): JsonResponse
    {
        $employeeId = $request->integer('employee_id');
        $hours = (float) $request->input('hours', 0);
        $multiplier = (float) $request->input('rate_multiplier', 1.5);
        $customRate = $request->filled('custom_hourly_rate') ? (float) $request->input('custom_hourly_rate') : null;

        $employee = Employee::find($employeeId);
        if (! $employee) {
            return response()->json([
                'hourly_rate' => 0.00,
                'amount' => 0.00,
            ]);
        }

        $hourlyRate = EmployeeOvertime::calculateHourlyRate($employee, $customRate);
        $amount = EmployeeOvertime::calculateAmount($hours, $hourlyRate, $multiplier);

        return response()->json([
            'hourly_rate' => $hourlyRate,
            'amount' => $amount,
            'salary_type' => $employee->salary_type,
            'daily_wage' => (float) $employee->daily_wage,
            'monthly_basic' => (float) $employee->monthly_basic_salary,
        ]);
    }
}
