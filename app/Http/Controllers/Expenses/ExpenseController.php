<?php

namespace App\Http\Controllers\Expenses;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpensePayment;
use App\Services\BranchContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseController extends Controller
{
    /**
     * Display combined listing of expense records with filters and summary.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('view_expenses')) {
            abort(403, 'Unauthorized access to expense records.');
        }

        // Seed default categories if none exist
        if (ExpenseCategory::count() === 0) {
            ExpenseCategory::seedDefaults();
        }

        $accessibleBranches = BranchContext::getAccessibleBranches($user);

        // Branch scoping
        $branchId = $request->input('branch_id');
        if ($user->isAdmin()) {
            if ($branchId === null) {
                $branchId = BranchContext::getActiveBranchId() ?: 'all';
            }
        } else {
            $branchId = BranchContext::getActiveBranchId();
        }

        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $expenseType = $request->input('expense_type'); // all, staff, shop
        $paymentMethod = $request->input('payment_method');
        $paymentStatus = $request->input('payment_status', 'all'); // all, paid, partially_paid, unpaid
        $status = $request->input('status', 'active'); // active, voided, all
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $month = $request->input('month');

        $query = Expense::with(['category', 'employee', 'advance', 'creator', 'branch', 'payments']);

        if ($branchId && $branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('expense_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('payment_reference', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($eq) use ($search) {
                        $eq->where('name', 'like', "%{$search}%")
                            ->orWhere('employee_number', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if (! empty($expenseType) && $expenseType !== 'all') {
            $query->where('expense_type', $expenseType);
        }

        if (! empty($paymentMethod) && $paymentMethod !== 'all') {
            $query->where('payment_method', $paymentMethod);
        }

        if (! empty($paymentStatus) && $paymentStatus !== 'all') {
            $query->where('payment_status', $paymentStatus);
        }

        if ($status === 'active') {
            $query->where('status', Expense::STATUS_ACTIVE);
        } elseif ($status === 'voided') {
            $query->where('status', Expense::STATUS_VOIDED);
        }

        if (! empty($fromDate)) {
            $query->whereDate('expense_date', '>=', $fromDate);
        }

        if (! empty($toDate)) {
            $query->whereDate('expense_date', '<=', $toDate);
        }

        if (! empty($month)) {
            $query->where('related_month', $month);
        }

        // Calculate summary totals from active (non-voided) records matching current filters
        $summaryQuery = clone $query;
        if ($status !== 'voided') {
            $summaryQuery->where('status', Expense::STATUS_ACTIVE);
        }

        $totalExpenses = (float) (clone $summaryQuery)->sum('amount');
        $totalPaid = (float) (clone $summaryQuery)->sum('paid_amount');
        $totalOutstanding = (float) (clone $summaryQuery)->sum('balance_amount');
        $staffTotal = (float) (clone $summaryQuery)->where('expense_type', Expense::TYPE_STAFF)->sum('amount');
        $shopTotal = (float) (clone $summaryQuery)->where('expense_type', Expense::TYPE_SHOP)->sum('amount');

        $expenses = $query->orderBy('expense_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $categories = ExpenseCategory::orderBy('name', 'asc')->get();

        return view('expenses.records.index', compact(
            'expenses',
            'categories',
            'totalExpenses',
            'totalPaid',
            'totalOutstanding',
            'staffTotal',
            'shopTotal',
            'search',
            'categoryId',
            'expenseType',
            'paymentMethod',
            'paymentStatus',
            'status',
            'fromDate',
            'toDate',
            'month',
            'accessibleBranches',
            'branchId'
        ));
    }

    /**
     * Show form for creating a new staff expense.
     */
    public function createStaff(): View
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('create_expenses')) {
            abort(403, 'Unauthorized action.');
        }

        $employees = Employee::where('is_active', true)->orderBy('name')->get();
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        $branches = BranchContext::getAccessibleBranches($user);
        $activeBranchId = BranchContext::getActiveBranchId();
        $currentMonth = today()->format('F Y');

        return view('expenses.records.create-staff', compact('employees', 'categories', 'branches', 'activeBranchId', 'currentMonth'));
    }

    /**
     * Store a newly created staff expense.
     */
    public function storeStaff(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('create_expenses')) {
            abort(403, 'Unauthorized action.');
        }

        $paymentChoice = $request->input('payment_status_choice', 'paid_now');

        $rules = [
            'employee_id' => 'required|exists:employees,id',
            'category_id' => 'required|exists:expense_categories,id',
            'related_month' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'branch_id' => 'nullable|exists:branches,id',
            'description' => 'nullable|string|max:1000',
            'payment_status_choice' => 'nullable|in:paid_now,pay_later',
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:5120',
        ];

        if ($paymentChoice === 'paid_now') {
            $rules['payment_date'] = 'required|date';
            $rules['payment_method'] = 'required|string|in:cash,bank_transfer,card,cheque,other';
            $rules['payment_amount'] = 'nullable|numeric|min:0.01';
            $rules['payment_reference'] = 'nullable|string|max:100';
        } else {
            $rules['expense_date'] = 'nullable|date';
        }

        $validated = $request->validate($rules, [
            'employee_id.required' => 'Please select an employee.',
            'category_id.required' => 'Please select an expense category.',
            'amount.required' => 'Please enter an amount.',
            'amount.min' => 'Amount must be greater than zero.',
            'payment_date.required' => 'Please select the payment date.',
            'payment_method.required' => 'Please choose a payment method.',
        ]);

        // Branch authorization
        $branchId = $validated['branch_id'] ?? null;
        if ($branchId) {
            if (! $user->isAdmin() && ! $user->canAccessBranch($branchId)) {
                abort(403, 'You do not have access to this branch.');
            }
        } else {
            $branchId = BranchContext::getActiveBranchId();
        }

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        $category = ExpenseCategory::findOrFail($validated['category_id']);
        $employee = Employee::findOrFail($validated['employee_id']);
        $expenseNumber = Expense::generateNextExpenseNumber();
        $totalAmount = (float) $validated['amount'];
        $expenseDate = $validated['payment_date'] ?? $validated['expense_date'] ?? today()->toDateString();
        $employeeAdvanceId = null;

        DB::transaction(function () use (
            $validated,
            $category,
            $employee,
            $branchId,
            $expenseNumber,
            $receiptPath,
            $user,
            $totalAmount,
            $expenseDate,
            $paymentChoice,
            &$employeeAdvanceId
        ) {
            // Salary Advance Integration (Section 5)
            if (strtolower(trim($category->name)) === 'salary advance') {
                $advanceNumber = EmployeeAdvance::generateNextAdvanceNumber();

                $advance = EmployeeAdvance::create([
                    'advance_number' => $advanceNumber,
                    'employee_id' => $employee->id,
                    'advance_date' => $expenseDate,
                    'amount' => $totalAmount,
                    'payment_method' => $validated['payment_method'] ?? 'cash',
                    'notes' => $validated['description'] ?? 'Salary advance disbursed via Expense Management',
                    'status' => EmployeeAdvance::STATUS_PENDING,
                    'created_by' => $user->id,
                ]);

                $employeeAdvanceId = $advance->id;
            }

            if ($paymentChoice === 'paid_now') {
                $payAmount = (float) ($validated['payment_amount'] ?? $totalAmount);
                $balance = max(0.00, round($totalAmount - $payAmount, 2));
                $paymentStatus = $balance <= 0.001 ? Expense::PAYMENT_STATUS_PAID : Expense::PAYMENT_STATUS_PARTIALLY_PAID;
                $paymentMethod = $validated['payment_method'];
                $paymentDate = $validated['payment_date'];
                $paymentRef = $validated['payment_reference'] ?? null;

                $expense = Expense::create([
                    'expense_number' => $expenseNumber,
                    'branch_id' => $branchId,
                    'category_id' => $category->id,
                    'expense_type' => Expense::TYPE_STAFF,
                    'employee_id' => $employee->id,
                    'employee_advance_id' => $employeeAdvanceId,
                    'expense_date' => $expenseDate,
                    'related_month' => $validated['related_month'],
                    'description' => $validated['description'] ?? null,
                    'amount' => $totalAmount,
                    'paid_amount' => $payAmount,
                    'balance_amount' => $balance,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $paymentMethod,
                    'payment_date' => $paymentDate,
                    'payment_reference' => $paymentRef,
                    'receipt_path' => $receiptPath,
                    'status' => Expense::STATUS_ACTIVE,
                    'created_by' => $user->id,
                ]);

                if ($payAmount > 0) {
                    ExpensePayment::create([
                        'expense_id' => $expense->id,
                        'branch_id' => $branchId,
                        'payment_number' => ExpensePayment::generatePaymentNumber(),
                        'payment_date' => $paymentDate,
                        'amount' => $payAmount,
                        'payment_method' => $paymentMethod,
                        'reference_number' => $paymentRef,
                        'notes' => 'Initial payment at recording',
                        'created_by' => $user->id,
                    ]);
                }
            } else {
                // Pay Later
                Expense::create([
                    'expense_number' => $expenseNumber,
                    'branch_id' => $branchId,
                    'category_id' => $category->id,
                    'expense_type' => Expense::TYPE_STAFF,
                    'employee_id' => $employee->id,
                    'employee_advance_id' => $employeeAdvanceId,
                    'expense_date' => $expenseDate,
                    'related_month' => $validated['related_month'],
                    'description' => $validated['description'] ?? null,
                    'amount' => $totalAmount,
                    'paid_amount' => 0.00,
                    'balance_amount' => $totalAmount,
                    'payment_status' => Expense::PAYMENT_STATUS_UNPAID,
                    'payment_method' => 'pending',
                    'payment_date' => null,
                    'payment_reference' => null,
                    'receipt_path' => $receiptPath,
                    'status' => Expense::STATUS_ACTIVE,
                    'created_by' => $user->id,
                ]);
            }
        });

        ActivityLog::log(
            'create_staff_expense',
            "Recorded staff expense {$expenseNumber} for {$employee->name} (LKR ".number_format($totalAmount, 2).')',
            'Expenses'
        );

        $successMsg = "Staff expense {$expenseNumber} of LKR ".number_format($totalAmount, 2).' saved successfully.';
        if ($employeeAdvanceId) {
            $successMsg .= ' Linked to Employee Salary Advance for payroll deduction.';
        }

        return redirect()->route('expenses.records.index')->with('success', $successMsg);
    }

    /**
     * Show form for creating a new shop operating expense.
     */
    public function createShop(): View
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('create_expenses')) {
            abort(403, 'Unauthorized action.');
        }

        $categories = ExpenseCategory::where('is_active', true)
            ->where('name', '!=', 'Salary Advance')
            ->orderBy('name')
            ->get();
        $branches = BranchContext::getAccessibleBranches($user);
        $activeBranchId = BranchContext::getActiveBranchId();
        $currentMonth = today()->format('F Y');

        return view('expenses.records.create-shop', compact('categories', 'branches', 'activeBranchId', 'currentMonth'));
    }

    /**
     * Store a newly created shop operating expense.
     */
    public function storeShop(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('create_expenses')) {
            abort(403, 'Unauthorized action.');
        }

        $paymentChoice = $request->input('payment_status_choice', 'paid_now');

        $rules = [
            'category_id' => 'required|exists:expense_categories,id',
            'expense_date' => 'required|date',
            'related_month' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'branch_id' => 'nullable|exists:branches,id',
            'description' => 'nullable|string|max:1000',
            'payment_status_choice' => 'nullable|in:paid_now,pay_later',
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:5120',
        ];

        if ($paymentChoice === 'paid_now') {
            $rules['payment_method'] = 'required|string|in:cash,bank_transfer,card,cheque,other';
            $rules['payment_amount'] = 'nullable|numeric|min:0.01';
            $rules['payment_date'] = 'nullable|date';
            $rules['payment_reference'] = 'nullable|string|max:100';
        }

        $validated = $request->validate($rules, [
            'category_id.required' => 'Please select an expense category.',
            'amount.required' => 'Please enter an amount.',
            'amount.min' => 'Amount must be greater than zero.',
            'expense_date.required' => 'Please select the expense date.',
            'payment_method.required' => 'Please choose a payment method.',
        ]);

        // Branch authorization
        $branchId = $validated['branch_id'] ?? null;
        if ($branchId) {
            if (! $user->isAdmin() && ! $user->canAccessBranch($branchId)) {
                abort(403, 'You do not have access to this branch.');
            }
        } else {
            $branchId = BranchContext::getActiveBranchId();
        }

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        $category = ExpenseCategory::findOrFail($validated['category_id']);
        $expenseNumber = Expense::generateNextExpenseNumber();
        $totalAmount = (float) $validated['amount'];

        DB::transaction(function () use (
            $validated,
            $category,
            $branchId,
            $expenseNumber,
            $receiptPath,
            $user,
            $totalAmount,
            $paymentChoice
        ) {
            if ($paymentChoice === 'paid_now') {
                $payAmount = (float) ($validated['payment_amount'] ?? $totalAmount);
                $balance = max(0.00, round($totalAmount - $payAmount, 2));
                $paymentStatus = $balance <= 0.001 ? Expense::PAYMENT_STATUS_PAID : Expense::PAYMENT_STATUS_PARTIALLY_PAID;
                $paymentMethod = $validated['payment_method'];
                $paymentDate = $validated['payment_date'] ?? $validated['expense_date'];
                $paymentRef = $validated['payment_reference'] ?? null;

                $expense = Expense::create([
                    'expense_number' => $expenseNumber,
                    'branch_id' => $branchId,
                    'category_id' => $category->id,
                    'expense_type' => Expense::TYPE_SHOP,
                    'employee_id' => null,
                    'employee_advance_id' => null,
                    'expense_date' => $validated['expense_date'],
                    'related_month' => $validated['related_month'],
                    'description' => $validated['description'] ?? null,
                    'amount' => $totalAmount,
                    'paid_amount' => $payAmount,
                    'balance_amount' => $balance,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $paymentMethod,
                    'payment_date' => $paymentDate,
                    'payment_reference' => $paymentRef,
                    'receipt_path' => $receiptPath,
                    'status' => Expense::STATUS_ACTIVE,
                    'created_by' => $user->id,
                ]);

                if ($payAmount > 0) {
                    ExpensePayment::create([
                        'expense_id' => $expense->id,
                        'branch_id' => $branchId,
                        'payment_number' => ExpensePayment::generatePaymentNumber(),
                        'payment_date' => $paymentDate,
                        'amount' => $payAmount,
                        'payment_method' => $paymentMethod,
                        'reference_number' => $paymentRef,
                        'notes' => 'Initial payment at recording',
                        'created_by' => $user->id,
                    ]);
                }
            } else {
                // Pay Later
                Expense::create([
                    'expense_number' => $expenseNumber,
                    'branch_id' => $branchId,
                    'category_id' => $category->id,
                    'expense_type' => Expense::TYPE_SHOP,
                    'employee_id' => null,
                    'employee_advance_id' => null,
                    'expense_date' => $validated['expense_date'],
                    'related_month' => $validated['related_month'],
                    'description' => $validated['description'] ?? null,
                    'amount' => $totalAmount,
                    'paid_amount' => 0.00,
                    'balance_amount' => $totalAmount,
                    'payment_status' => Expense::PAYMENT_STATUS_UNPAID,
                    'payment_method' => 'pending',
                    'payment_date' => null,
                    'payment_reference' => null,
                    'receipt_path' => $receiptPath,
                    'status' => Expense::STATUS_ACTIVE,
                    'created_by' => $user->id,
                ]);
            }
        });

        ActivityLog::log(
            'create_shop_expense',
            "Recorded shop expense {$expenseNumber} for {$category->name} (LKR ".number_format($totalAmount, 2).', Status: '.($paymentChoice === 'paid_now' ? 'Paid' : 'Pay Later').')',
            'Expenses'
        );

        $msg = "Shop expense {$expenseNumber} of LKR ".number_format($totalAmount, 2).' saved successfully.';
        if ($paymentChoice === 'pay_later') {
            $msg .= ' Saved with outstanding balance for later payment.';
        }

        return redirect()->route('expenses.records.index')->with('success', $msg);
    }

    /**
     * Record a payment against an existing expense with outstanding balance.
     */
    public function storePayment(Request $request, Expense $expense): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('create_expenses')) {
            abort(403, 'Unauthorized action.');
        }

        if ($expense->branch_id && ! $user->isAdmin() && ! $user->canAccessBranch($expense->branch_id)) {
            abort(403, 'You do not have access to this branch.');
        }

        if ($expense->isVoided()) {
            return back()->with('error', 'Cannot record payment on a voided expense.');
        }

        if (! $expense->hasOutstandingBalance()) {
            return back()->with('error', 'This expense has already been fully paid.');
        }

        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|in:cash,bank_transfer,card,cheque,other',
            'payment_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ], [
            'payment_amount.required' => 'Please enter the payment amount.',
            'payment_amount.min' => 'Payment amount must be greater than zero.',
            'payment_date.required' => 'Please select the payment date.',
            'payment_method.required' => 'Please choose a payment method.',
        ]);

        $paymentAmount = (float) $validated['payment_amount'];

        DB::transaction(function () use ($expense, $validated, $paymentAmount, $user) {
            $lockedExpense = Expense::where('id', $expense->id)->lockForUpdate()->firstOrFail();

            if ($paymentAmount > (float) $lockedExpense->balance_amount + 0.001) {
                throw ValidationException::withMessages([
                    'payment_amount' => 'Payment amount (LKR '.number_format($paymentAmount, 2).') cannot exceed the outstanding balance of LKR '.number_format($lockedExpense->balance_amount, 2).'.',
                ]);
            }

            $paymentNumber = ExpensePayment::generatePaymentNumber();

            ExpensePayment::create([
                'expense_id' => $lockedExpense->id,
                'branch_id' => $lockedExpense->branch_id,
                'payment_number' => $paymentNumber,
                'payment_date' => $validated['payment_date'],
                'amount' => $paymentAmount,
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['payment_reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => $user->id,
            ]);

            $lockedExpense->recalculatePaymentStatus();
        });

        $freshExpense = $expense->fresh();

        ActivityLog::log(
            'pay_expense',
            'Recorded payment of LKR '.number_format($paymentAmount, 2)." for expense {$expense->expense_number} (Method: ".ucwords(str_replace('_', ' ', $validated['payment_method'])).')',
            'Expenses'
        );

        return back()->with('success', 'Payment of LKR '.number_format($paymentAmount, 2).' saved successfully. Remaining balance: LKR '.number_format($freshExpense->balance_amount, 2));
    }

    /**
     * Display details of an expense record.
     */
    public function show(Expense $expense): View
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('view_expenses')) {
            abort(403, 'Unauthorized access.');
        }

        $expense->load([
            'category',
            'employee',
            'advance',
            'creator',
            'voider',
            'branch',
            'payments.creator',
            'payments.branch',
        ]);

        return view('expenses.records.show', compact('expense'));
    }

    /**
     * Show form for editing an expense record.
     */
    public function edit(Expense $expense): View
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can edit financial records.');
        }

        if ($expense->isVoided()) {
            return redirect()->route('expenses.records.show', $expense)
                ->with('error', 'Voided expense records cannot be modified.');
        }

        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        $employees = Employee::where('is_active', true)->orderBy('name')->get();
        $branches = BranchContext::getAccessibleBranches(Auth::user());

        return view('expenses.records.edit', compact('expense', 'categories', 'employees', 'branches'));
    }

    /**
     * Update an expense record with audit tracking.
     */
    public function update(Request $request, Expense $expense): RedirectResponse
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can edit financial records.');
        }

        if ($expense->isVoided()) {
            return back()->with('error', 'Voided expense records cannot be edited.');
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'expense_date' => 'required|date',
            'related_month' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'branch_id' => 'nullable|exists:branches,id',
            'payment_method' => 'nullable|string|in:cash,bank_transfer,card,cheque,other,pending',
            'payment_date' => 'nullable|date',
            'payment_reference' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:5120',
        ]);

        $receiptPath = $expense->receipt_path;
        if ($request->hasFile('receipt')) {
            // Delete previous receipt file if exists
            if ($receiptPath && Storage::disk('public')->exists($receiptPath)) {
                Storage::disk('public')->delete($receiptPath);
            }
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        $oldAmount = (float) $expense->amount;
        $newAmount = (float) $validated['amount'];
        $currentPaid = (float) $expense->paid_amount;

        if ($newAmount < $currentPaid) {
            throw ValidationException::withMessages([
                'amount' => 'Expense total amount (LKR '.number_format($newAmount, 2).') cannot be less than already paid amount of LKR '.number_format($currentPaid, 2).'.',
            ]);
        }

        DB::transaction(function () use ($expense, $validated, $receiptPath, $newAmount) {
            $branchId = $validated['branch_id'] ?? $expense->branch_id;

            $expense->update([
                'category_id' => $validated['category_id'],
                'branch_id' => $branchId,
                'expense_date' => $validated['expense_date'],
                'related_month' => $validated['related_month'],
                'amount' => $newAmount,
                'payment_method' => $validated['payment_method'] ?? $expense->payment_method,
                'payment_date' => $validated['payment_date'] ?? $expense->payment_date,
                'payment_reference' => $validated['payment_reference'] ?? $expense->payment_reference,
                'description' => $validated['description'] ?? null,
                'receipt_path' => $receiptPath,
            ]);

            $expense->recalculatePaymentStatus();

            // If linked to an employee advance that is still pending, update advance amount
            if ($expense->advance && $expense->advance->status === EmployeeAdvance::STATUS_PENDING) {
                $expense->advance->update([
                    'amount' => $newAmount,
                    'advance_date' => $validated['expense_date'],
                    'payment_method' => $validated['payment_method'] ?? $expense->advance->payment_method,
                ]);
            }
        });

        ActivityLog::log(
            'update_expense',
            "Updated expense {$expense->expense_number} (Amount changed from LKR {$oldAmount} to LKR {$newAmount})",
            'Expenses'
        );

        return redirect()->route('expenses.records.show', $expense)
            ->with('success', "Expense {$expense->expense_number} updated successfully.");
    }

    /**
     * Void an expense record safely with reason tracking.
     */
    public function void(Request $request, Expense $expense): RedirectResponse
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can void financial records.');
        }

        if ($expense->isVoided()) {
            return back()->with('error', 'This expense record is already voided.');
        }

        $validated = $request->validate([
            'void_reason' => 'required|string|max:255',
        ], [
            'void_reason.required' => 'Please provide a reason for voiding this expense.',
        ]);

        // Check if linked to an advance already deducted in payroll
        if ($expense->advance && $expense->advance->status === EmployeeAdvance::STATUS_DEDUCTED) {
            return back()->with('error', 'Cannot void this expense: the linked salary advance has already been finalized and deducted in approved payroll.');
        }

        DB::transaction(function () use ($expense, $validated) {
            // Cancel linked pending advance if any
            if ($expense->advance && $expense->advance->status === EmployeeAdvance::STATUS_PENDING) {
                $expense->advance->update([
                    'status' => EmployeeAdvance::STATUS_CANCELLED,
                ]);
            }

            $expense->update([
                'status' => Expense::STATUS_VOIDED,
                'void_reason' => $validated['void_reason'],
                'voided_by' => Auth::id(),
                'voided_at' => now(),
            ]);
        });

        ActivityLog::log(
            'void_expense',
            "Voided expense {$expense->expense_number} of LKR ".number_format($expense->amount, 2).". Reason: {$validated['void_reason']}",
            'Expenses'
        );

        return back()->with('success', "Expense {$expense->expense_number} has been voided successfully.");
    }

    /**
     * Securely download or view uploaded receipt attachment.
     */
    public function downloadReceipt(Expense $expense): BinaryFileResponse|RedirectResponse
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('view_expenses')) {
            abort(403, 'Unauthorized access.');
        }

        if (! $expense->receipt_path || ! Storage::disk('public')->exists($expense->receipt_path)) {
            return back()->with('error', 'Receipt file not found.');
        }

        $filePath = Storage::disk('public')->path($expense->receipt_path);

        return response()->file($filePath);
    }

    /**
     * Export filtered expenses to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $expenseType = $request->input('expense_type');
        $paymentMethod = $request->input('payment_method');
        $paymentStatus = $request->input('payment_status');
        $status = $request->input('status', 'active');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $query = Expense::with(['category', 'employee', 'branch']);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('expense_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if (! empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }
        if (! empty($expenseType) && $expenseType !== 'all') {
            $query->where('expense_type', $expenseType);
        }
        if (! empty($paymentMethod) && $paymentMethod !== 'all') {
            $query->where('payment_method', $paymentMethod);
        }
        if (! empty($paymentStatus) && $paymentStatus !== 'all') {
            $query->where('payment_status', $paymentStatus);
        }
        if ($status === 'active') {
            $query->where('status', Expense::STATUS_ACTIVE);
        } elseif ($status === 'voided') {
            $query->where('status', Expense::STATUS_VOIDED);
        }
        if (! empty($fromDate)) {
            $query->whereDate('expense_date', '>=', $fromDate);
        }
        if (! empty($toDate)) {
            $query->whereDate('expense_date', '<=', $toDate);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=Expense_Records_'.date('Y-m-d').'.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($expenses) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Expense #',
                'Date',
                'Category',
                'Type',
                'Branch',
                'Employee',
                'Description',
                'Total Amount (LKR)',
                'Paid Amount (LKR)',
                'Balance (LKR)',
                'Payment Status',
                'Payment Method',
                'Payment Reference',
                'Related Month',
                'Status',
            ]);

            foreach ($expenses as $e) {
                fputcsv($file, [
                    $e->expense_number,
                    $e->expense_date->toDateString(),
                    $e->category ? $e->category->name : '',
                    ucfirst($e->expense_type),
                    $e->branch ? $e->branch->name : '',
                    $e->employee ? $e->employee->name : '',
                    $e->description,
                    number_format($e->amount, 2, '.', ''),
                    number_format($e->paid_amount, 2, '.', ''),
                    number_format($e->balance_amount, 2, '.', ''),
                    ucwords(str_replace('_', ' ', $e->payment_status)),
                    ucwords(str_replace('_', ' ', $e->payment_method)),
                    $e->payment_reference,
                    $e->related_month,
                    ucfirst($e->status),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
