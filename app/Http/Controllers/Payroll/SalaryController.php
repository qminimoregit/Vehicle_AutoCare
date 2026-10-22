<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CompanySetting;
use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\EmployeeAttendance;
use App\Models\PayrollItem;
use App\Models\PayrollPayment;
use App\Models\PayrollPeriod;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SalaryController extends Controller
{
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
            ->with(['items.employee', 'items.payments', 'approver'])
            ->first();

        if ($period) {
            $period->items->each(function ($item) use ($period) {
                $item->setRelation('period', $period);
            });
        }

        // Handle "Generate Salary" button action
        if ($request->has('generate') || $request->input('action') === 'generate') {
            $this->authorizeAdmin();

            if ($period && ! $period->isDraft()) {
                return redirect()->route('payroll.salary.index', ['year' => $year, 'month' => $month])
                    ->with('error', 'Approved or paid payroll periods cannot be regenerated.');
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

        return view('payroll.salary.index', compact(
            'year',
            'month',
            'periodDate',
            'period',
            'unmarkedDaysWarning',
            'years',
            'pendingAdvances',
            'activeEmployees'
        ));
    }

    /**
     * Generate or update draft PayrollPeriod and PayrollItems based on actual attendance and wage rates.
     */
    private function generateOrUpdateDraftPeriod(int $year, int $month): PayrollPeriod
    {
        $periodDate = Carbon::createFromDate($year, $month, 1);
        $monthStart = $periodDate->copy()->startOfMonth()->toDateString();
        $monthEnd = $periodDate->copy()->endOfMonth()->toDateString();

        $period = PayrollPeriod::firstOrCreate(
            ['year' => $year, 'month' => $month],
            ['status' => PayrollPeriod::STATUS_DRAFT]
        );

        // Active employees eligible in this period (joined on or before month end), plus any employee with recorded attendance in this month
        $empIdsWithAttendance = EmployeeAttendance::whereBetween('attendance_date', [$monthStart, $monthEnd])
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
            ->orderBy('name')
            ->get();

        foreach ($employees as $employee) {
            // Find existing draft item if present to preserve manual adjustments
            $existingItem = PayrollItem::where('payroll_period_id', $period->id)
                ->where('employee_id', $employee->id)
                ->first();

            $overtime = $existingItem ? (float) $existingItem->overtime_amount : 0.00;
            $bonus = $existingItem ? (float) $existingItem->bonus_amount : 0.00;
            $deductions = $existingItem ? (float) $existingItem->deductions : 0.00;
            $notes = $existingItem ? $existingItem->notes : null;

            // Fetch all attendance for this employee in this month
            $attendances = EmployeeAttendance::where('employee_id', $employee->id)
                ->whereBetween('attendance_date', [$monthStart, $monthEnd])
                ->orderBy('attendance_date', 'asc')
                ->get();

            $fullDays = 0;
            $halfDays = 0;
            $absentDays = 0;
            $leaveDays = 0;
            $dayOffDays = 0;
            $baseEarnings = 0.00;
            $payableDays = 0.00;
            $breakdown = [];

            foreach ($attendances as $att) {
                switch ($att->status) {
                    case EmployeeAttendance::STATUS_FULL_DAY:
                        $fullDays++;
                        break;
                    case EmployeeAttendance::STATUS_HALF_DAY:
                        $halfDays++;
                        break;
                    case EmployeeAttendance::STATUS_ABSENT:
                        $absentDays++;
                        break;
                    case EmployeeAttendance::STATUS_LEAVE:
                        $leaveDays++;
                        break;
                    case EmployeeAttendance::STATUS_DAY_OFF:
                        $dayOffDays++;
                        break;
                }

                $payableDays += (float) $att->payable_day_fraction;
                $baseEarnings += (float) $att->daily_earnings;

                $breakdown[] = [
                    'date' => $att->attendance_date->toDateString(),
                    'status' => $att->status,
                    'fraction' => (float) $att->payable_day_fraction,
                    'wage' => (float) $att->applied_daily_wage,
                    'earnings' => (float) $att->daily_earnings,
                    'notes' => $att->notes,
                ];
            }

            // Calculate pending advances for this employee (up to end of month)
            // Advances that have not been deducted in any previous finalized payroll
            $pendingAdvancesSum = (float) EmployeeAdvance::where('employee_id', $employee->id)
                ->where('status', EmployeeAdvance::STATUS_PENDING)
                ->where('advance_date', '<=', $monthEnd)
                ->sum('amount');

            $grossSalary = round($baseEarnings + $overtime + $bonus, 2);
            $totalDeductions = round($deductions + $pendingAdvancesSum, 2);
            $netSalary = round(max(0, $grossSalary - $totalDeductions), 2);

            PayrollItem::updateOrCreate(
                [
                    'payroll_period_id' => $period->id,
                    'employee_id' => $employee->id,
                ],
                [
                    'employee_name_snapshot' => $employee->name,
                    'daily_wage_snapshot' => $employee->daily_wage,
                    'full_days_count' => $fullDays,
                    'half_days_count' => $halfDays,
                    'absent_days_count' => $absentDays,
                    'leave_days_count' => $leaveDays,
                    'day_off_days_count' => $dayOffDays,
                    'payable_days' => $payableDays,
                    'base_earnings' => $baseEarnings,
                    'overtime_amount' => $overtime,
                    'bonus_amount' => $bonus,
                    'deductions' => $deductions,
                    'advances_applied' => $pendingAdvancesSum,
                    'gross_salary' => $grossSalary,
                    'net_salary' => $netSalary,
                    'paid_amount' => $existingItem ? (float) $existingItem->paid_amount : 0.00,
                    'balance_due' => round(max(0, $netSalary - ($existingItem ? (float) $existingItem->paid_amount : 0.00)), 2),
                    'status' => PayrollItem::STATUS_DRAFT,
                    'attendance_breakdown' => $breakdown,
                    'notes' => $notes,
                ]
            );
        }

        $period->recalculateTotals();

        return $period->fresh(['items.employee']);
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

        // If payroll item has linked advances, load them; if draft, find pending advances matching amount
        $advances = $payrollItem->advances;
        if ($advances->isEmpty() && $payrollItem->period->isDraft()) {
            $monthEnd = Carbon::createFromDate($payrollItem->period->year, $payrollItem->period->month, 1)->endOfMonth()->toDateString();
            $advances = EmployeeAdvance::where('employee_id', $payrollItem->employee_id)
                ->where('status', EmployeeAdvance::STATUS_PENDING)
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
        if ($advances->isEmpty() && $payrollItem->period->isDraft()) {
            $monthEnd = Carbon::createFromDate($payrollItem->period->year, $payrollItem->period->month, 1)->endOfMonth()->toDateString();
            $advances = EmployeeAdvance::where('employee_id', $payrollItem->employee_id)
                ->where('status', EmployeeAdvance::STATUS_PENDING)
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

        if (! $payrollItem->period->isDraft()) {
            return back()->with('error', 'Adjustments can only be updated when payroll is in draft status.');
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

        ActivityLog::log(
            'update_payroll_item',
            "Updated payroll adjustments for {$payrollItem->employee_name_snapshot} ({$payrollItem->period->formatted_period})",
            'Payroll'
        );

        return back()->with('success', "Payroll adjustments for {$payrollItem->employee_name_snapshot} updated successfully.");
    }

    /**
     * Finalize and approve the monthly payroll period.
     */
    public function approve(Request $request, PayrollPeriod $period): RedirectResponse
    {
        $this->authorizeAdmin();

        if (! $period->isDraft()) {
            return back()->with('error', 'This payroll period is already approved.');
        }

        $periodDate = Carbon::createFromDate($period->year, $period->month, 1);
        $monthEnd = $periodDate->copy()->endOfMonth()->toDateString();

        DB::transaction(function () use ($period, $monthEnd) {
            $period->loadMissing('items');

            foreach ($period->items as $item) {
                // Link pending advances for this employee and mark them deducted
                $advancesToDeduct = EmployeeAdvance::where('employee_id', $item->employee_id)
                    ->where('status', EmployeeAdvance::STATUS_PENDING)
                    ->where('advance_date', '<=', $monthEnd)
                    ->get();

                foreach ($advancesToDeduct as $adv) {
                    $adv->update([
                        'status' => EmployeeAdvance::STATUS_DEDUCTED,
                        'payroll_item_id' => $item->id,
                    ]);
                }

                $item->update([
                    'status' => PayrollItem::STATUS_APPROVED,
                ]);
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
     * Record an employee salary advance.
     */
    public function storeAdvance(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'advance_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:cash,bank_transfer,cheque,other',
            'notes' => 'nullable|string|max:500',
        ]);

        $advanceNumber = EmployeeAdvance::generateNextAdvanceNumber();

        $advance = EmployeeAdvance::create([
            'advance_number' => $advanceNumber,
            'employee_id' => $validated['employee_id'],
            'advance_date' => $validated['advance_date'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'] ?? null,
            'status' => EmployeeAdvance::STATUS_PENDING,
            'created_by' => Auth::id(),
        ]);

        $employee = Employee::find($validated['employee_id']);

        ActivityLog::log(
            'record_advance',
            "Recorded salary advance {$advanceNumber} of LKR ".number_format($advance->amount, 2)." for {$employee->name}",
            'Payroll'
        );

        return back()->with('success', "Salary advance {$advanceNumber} of LKR ".number_format($advance->amount, 2)." recorded for {$employee->name}.");
    }
}
