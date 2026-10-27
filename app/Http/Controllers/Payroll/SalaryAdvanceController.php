<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpensePayment;
use App\Services\BranchContext;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SalaryAdvanceController extends Controller
{
    private function authorizeAdmin(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Unauthorized action. Only administrators can perform this action.');
        }
    }

    /**
     * Display a listing of salary advances with metrics and filters.
     */
    public function index(Request $request): View
    {
        $query = EmployeeAdvance::with([
            'employee',
            'branch',
            'creator',
            'approver',
            'disburser',
            'recoveries.payrollPeriod',
        ])->latest('advance_date')->latest('id');

        // Filter: Employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->integer('employee_id'));
        }

        // Filter: Month (YYYY-MM)
        if ($request->filled('month')) {
            $monthParts = explode('-', $request->string('month'));
            if (count($monthParts) === 2) {
                $query->whereYear('advance_date', (int) $monthParts[0])
                    ->whereMonth('advance_date', (int) $monthParts[1]);
            }
        }

        // Filter: Status
        if ($request->filled('status') && $request->status !== 'all') {
            $status = $request->string('status');
            $query->where('status', $status);
        }

        // Filter: Branch
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->integer('branch_id'));
        }

        $advances = $query->paginate(15)->withQueryString();

        // Summary metrics
        $allAdvances = EmployeeAdvance::all();
        $totalDisbursed = 0.00;
        $totalRecovered = 0.00;
        $outstandingBalance = 0.00;
        $pendingApprovalsCount = 0;

        foreach ($allAdvances as $adv) {
            if ($adv->status === EmployeeAdvance::STATUS_PENDING) {
                $pendingApprovalsCount++;
            }

            if ($adv->isDisbursed()) {
                $totalDisbursed += (float) $adv->amount;
                $recovered = $adv->recovered_amount;
                $totalRecovered += $recovered;
                $outstandingBalance += $adv->remaining_balance;
            }
        }

        $employees = Employee::orderBy('name')->get();
        $branches = Branch::where('is_active', true)->orderBy('branch_name')->get();

        return view('payroll.advances.index', compact(
            'advances',
            'employees',
            'branches',
            'totalDisbursed',
            'totalRecovered',
            'outstandingBalance',
            'pendingApprovalsCount'
        ));
    }

    /**
     * Register a new salary advance.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'branch_id' => 'nullable|exists:branches,id',
            'advance_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'monthly_installment' => 'nullable|numeric|min:0.01|max:'.$request->input('amount'),
            'payment_method' => 'required|string|in:cash,bank_transfer,cheque,other',
            'approval_status' => 'nullable|string|in:pending,approved,disbursed',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $employee = Employee::findOrFail($validated['employee_id']);
        $advanceNumber = EmployeeAdvance::generateNextAdvanceNumber();

        $status = EmployeeAdvance::STATUS_PENDING;
        $approvedBy = null;
        $approvedAt = null;
        $disbursedAt = null;
        $disbursedBy = null;

        $chosenApproval = $validated['approval_status'] ?? EmployeeAdvance::STATUS_PENDING;

        if ($chosenApproval === EmployeeAdvance::STATUS_APPROVED) {
            $this->authorizeAdmin();
            $status = EmployeeAdvance::STATUS_APPROVED;
            $approvedBy = $user->id;
            $approvedAt = now();
        } elseif ($chosenApproval === EmployeeAdvance::STATUS_DISBURSED) {
            $this->authorizeAdmin();
            $status = EmployeeAdvance::STATUS_DISBURSED;
            $approvedBy = $user->id;
            $approvedAt = now();
            $disbursedAt = $validated['advance_date'];
            $disbursedBy = $user->id;
        }

        $branchId = $validated['branch_id'] ?? $employee->branch_id ?? BranchContext::getActiveBranchId();

        $advance = null;

        DB::transaction(function () use (
            &$advance,
            $advanceNumber,
            $validated,
            $branchId,
            $status,
            $approvedBy,
            $approvedAt,
            $disbursedAt,
            $disbursedBy,
            $user

        ) {
            $advanceData = [
                'advance_number' => $advanceNumber,
                'employee_id' => $validated['employee_id'],
                'branch_id' => $branchId,
                'advance_date' => $validated['advance_date'],
                'amount' => $validated['amount'],
                'monthly_installment' => $validated['monthly_installment'] ?? null,
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
                'status' => $status,
                'created_by' => $user->id,
            ];

            if (Schema::hasColumn('employee_advances', 'approved_by')) {
                $advanceData['approved_by'] = $approvedBy;
                $advanceData['approved_at'] = $approvedAt;
                $advanceData['disbursed_at'] = $disbursedAt;
                $advanceData['disbursed_by'] = $disbursedBy;
            }

            $advance = EmployeeAdvance::create($advanceData);

            // If disbursed right away, create the financial outflow in Expense and ExpensePayment
            if ($status === EmployeeAdvance::STATUS_DISBURSED) {
                $this->createDisbursementExpense($advance, $validated['payment_method'], $validated['advance_date'], $user);
            }
        });

        ActivityLog::log(
            'register_advance',
            "Registered salary advance {$advanceNumber} of LKR ".number_format($validated['amount'], 2)." for {$employee->name} [Status: {$status}]",
            'Payroll'
        );

        return back()->with('success', "Salary advance {$advanceNumber} of LKR ".number_format($validated['amount'], 2)." registered for {$employee->name}.");
    }

    /**
     * Approve a pending salary advance (Admin only).
     */
    public function approve(Request $request, EmployeeAdvance $advance): RedirectResponse
    {
        $this->authorizeAdmin();

        if ($advance->status !== EmployeeAdvance::STATUS_PENDING) {
            return back()->with('error', "Cannot approve advance {$advance->advance_number} because its current status is '{$advance->status}'.");
        }

        $updateData = [
            'status' => EmployeeAdvance::STATUS_APPROVED,
        ];

        if (Schema::hasColumn('employee_advances', 'approved_by')) {
            $updateData['approved_by'] = Auth::id();
            $updateData['approved_at'] = now();
        }

        $advance->update($updateData);

        ActivityLog::log(
            'approve_advance',
            "Approved salary advance {$advance->advance_number} for {$advance->employee->name}",
            'Payroll'
        );

        return back()->with('success', "Salary advance {$advance->advance_number} has been approved. You can now record its disbursement when funds are issued.");
    }

    /**
     * Reject a pending salary advance (Admin only).
     */
    public function reject(Request $request, EmployeeAdvance $advance): RedirectResponse
    {
        $this->authorizeAdmin();

        if ($advance->status !== EmployeeAdvance::STATUS_PENDING) {
            return back()->with('error', 'Only pending advances can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $updateData = [
            'status' => EmployeeAdvance::STATUS_REJECTED,
        ];

        if (Schema::hasColumn('employee_advances', 'rejected_by')) {
            $updateData['rejected_by'] = Auth::id();
            $updateData['rejected_at'] = now();
            $updateData['rejection_reason'] = $validated['rejection_reason'];
        }

        $advance->update($updateData);

        ActivityLog::log(
            'reject_advance',
            "Rejected salary advance {$advance->advance_number} for {$advance->employee->name}. Reason: {$validated['rejection_reason']}",
            'Payroll'
        );

        return back()->with('success', "Salary advance {$advance->advance_number} has been rejected.");
    }

    /**
     * Record disbursement of an approved advance (Admin only).
     * Connects to Money Book via Expense & ExpensePayment without duplicate P&L impact.
     */
    public function disburse(Request $request, EmployeeAdvance $advance): RedirectResponse
    {
        $this->authorizeAdmin();

        if ($advance->status !== EmployeeAdvance::STATUS_APPROVED && $advance->status !== EmployeeAdvance::STATUS_PENDING) {
            return back()->with('error', "Advance {$advance->advance_number} is not eligible for disbursement.");
        }

        $validated = $request->validate([
            'disbursed_at' => 'required|date',
            'payment_method' => 'required|string|in:cash,bank_transfer,cheque,other',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($advance, $validated) {
            $user = Auth::user();

            $updateData = [
                'status' => EmployeeAdvance::STATUS_DISBURSED,
                'payment_method' => $validated['payment_method'],
            ];

            if (Schema::hasColumn('employee_advances', 'disbursed_at')) {
                $updateData['disbursed_at'] = $validated['disbursed_at'];
                $updateData['disbursed_by'] = $user->id;
            }
            if (! empty($validated['notes'])) {
                $updateData['notes'] = trim(($advance->notes ? $advance->notes."\n" : '').'Disbursement: '.$validated['notes']);
            }

            $advance->update($updateData);

            // Create financial outflow via Expense & ExpensePayment
            $this->createDisbursementExpense($advance, $validated['payment_method'], $validated['disbursed_at'], $user);
        });

        ActivityLog::log(
            'disburse_advance',
            "Disbursed salary advance {$advance->advance_number} of LKR ".number_format($advance->amount, 2)." to {$advance->employee->name}",
            'Payroll'
        );

        return back()->with('success', 'Disbursement of LKR '.number_format($advance->amount, 2)." recorded for {$advance->advance_number}. Money Book updated successfully.");
    }

    /**
     * Cancel an eligible advance (Admin only).
     */
    public function cancel(Request $request, EmployeeAdvance $advance): RedirectResponse
    {
        $this->authorizeAdmin();

        if (! $advance->canBeCancelled()) {
            return back()->with('error', "Advance {$advance->advance_number} cannot be cancelled because it has already been disbursed or recovered.");
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($advance, $validated) {
            $updateData = [
                'status' => EmployeeAdvance::STATUS_CANCELLED,
            ];

            if (Schema::hasColumn('employee_advances', 'cancelled_by')) {
                $updateData['cancelled_by'] = Auth::id();
                $updateData['cancelled_at'] = now();
                $updateData['cancellation_reason'] = $validated['cancellation_reason'];
            }

            $advance->update($updateData);

            // Void associated expense if any exists
            if ($advance->expense) {
                $advance->expense->update([
                    'status' => Expense::STATUS_VOIDED,
                    'void_reason' => 'Salary advance cancelled: '.$validated['cancellation_reason'],
                    'voided_by' => Auth::id(),
                    'voided_at' => now(),
                ]);
            }
        });

        ActivityLog::log(
            'cancel_advance',
            "Cancelled salary advance {$advance->advance_number} for {$advance->employee->name}. Reason: {$validated['cancellation_reason']}",
            'Payroll'
        );

        return back()->with('success', "Salary advance {$advance->advance_number} has been cancelled.");
    }

    /**
     * Audited reversal of a disbursement made in error (Admin only).
     */
    public function reverseDisbursement(Request $request, EmployeeAdvance $advance): RedirectResponse
    {
        $this->authorizeAdmin();

        if ($advance->recovered_amount > 0.001) {
            return back()->with('error', "Cannot reverse disbursement for advance {$advance->advance_number} because recoveries have already occurred via payroll.");
        }

        $validated = $request->validate([
            'reversal_reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($advance, $validated) {
            $user = Auth::user();

            // Void expense and expense payment
            if ($advance->expense) {
                $expense = $advance->expense;
                $expense->update([
                    'status' => Expense::STATUS_VOIDED,
                    'void_reason' => 'Disbursement reversed in error: '.$validated['reversal_reason'],
                    'voided_by' => $user->id,
                    'voided_at' => now(),
                ]);
            }

            $updateData = [
                'status' => EmployeeAdvance::STATUS_APPROVED,
            ];

            if (Schema::hasColumn('employee_advances', 'disbursed_at')) {
                $updateData['disbursed_at'] = null;
                $updateData['disbursed_by'] = null;
            }

            $advance->update($updateData);
        });

        ActivityLog::log(
            'reverse_advance_disbursement',
            "Reversed disbursement for salary advance {$advance->advance_number}. Reason: {$validated['reversal_reason']}",
            'Payroll'
        );

        return back()->with('success', "Disbursement reversed successfully for {$advance->advance_number}. Status reverted to Approved.");
    }

    /**
     * Return JSON history of an advance for details modal.
     */
    public function history(EmployeeAdvance $advance): JsonResponse
    {
        $advance->loadMissing([
            'employee',
            'branch',
            'creator',
            'approver',
            'disburser',
            'recoveries.payrollPeriod',
            'expense.payments',
        ]);

        return response()->json([
            'advance_number' => $advance->advance_number,
            'employee_name' => $advance->employee->name,
            'employee_code' => $advance->employee->employee_number,
            'amount' => (float) $advance->amount,
            'monthly_installment' => (float) ($advance->monthly_installment ?? 0),
            'recovered_amount' => $advance->recovered_amount,
            'remaining_balance' => $advance->remaining_balance,
            'status' => $advance->status,
            'advance_date' => $advance->advance_date->format('Y-m-d'),
            'disbursed_at' => $advance->disbursed_at ? $advance->disbursed_at->format('Y-m-d') : null,
            'disbursed_by_name' => $advance->disburser ? $advance->disburser->name : null,
            'approved_at' => $advance->approved_at ? $advance->approved_at->format('Y-m-d H:i') : null,
            'approved_by_name' => $advance->approver ? $advance->approver->name : null,
            'notes' => $advance->notes,
            'rejection_reason' => $advance->rejection_reason ?? null,
            'cancellation_reason' => $advance->cancellation_reason ?? null,
            'recoveries' => $advance->recoveries->map(fn ($r) => [
                'id' => $r->id,
                'period' => $r->payrollPeriod ? $r->payrollPeriod->formatted_period : 'N/A',
                'recovery_date' => $r->recovery_date->format('Y-m-d'),
                'amount' => (float) $r->amount,
                'status' => $r->status,
            ]),
        ]);
    }

    /**
     * Helper to create one actual financial outflow via Expense & ExpensePayment.
     * Ensures Money Book records the cash/bank outflow while P&L ignores it.
     */
    private function createDisbursementExpense(EmployeeAdvance $advance, string $paymentMethod, string $disbursedDate, $user): void
    {
        // Avoid creating duplicate expenses if one already exists
        if (Expense::where('employee_advance_id', $advance->id)->where('status', Expense::STATUS_ACTIVE)->exists()) {
            return;
        }

        $category = ExpenseCategory::firstOrCreate(
            ['name' => 'Salary Advance'],
            [
                'description' => 'Staff salary advance payouts',
                'is_active' => true,
            ]
        );

        $carbonDate = Carbon::parse($disbursedDate);
        $amount = (float) $advance->amount;
        $branchId = $advance->branch_id ?? $advance->employee->branch_id ?? BranchContext::getActiveBranchId();

        $expense = Expense::create([
            'expense_number' => Expense::generateNextExpenseNumber(),
            'branch_id' => $branchId,
            'category_id' => $category->id,
            'expense_type' => Expense::TYPE_STAFF,
            'employee_id' => $advance->employee_id,
            'employee_advance_id' => $advance->id,
            'expense_date' => $disbursedDate,
            'related_month' => $carbonDate->format('F Y'),
            'description' => "Salary advance disbursement: {$advance->advance_number}",
            'amount' => $amount,
            'paid_amount' => $amount,
            'balance_amount' => 0.00,
            'payment_status' => Expense::PAYMENT_STATUS_PAID,
            'payment_method' => $paymentMethod,
            'payment_date' => $disbursedDate,
            'status' => Expense::STATUS_ACTIVE,
            'created_by' => $user->id,
        ]);

        ExpensePayment::create([
            'expense_id' => $expense->id,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'payment_date' => $disbursedDate,
            'notes' => "Disbursement for salary advance {$advance->advance_number}",
            'created_by' => $user->id,
        ]);
    }
}
