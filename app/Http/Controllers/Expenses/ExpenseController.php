<?php

namespace App\Http\Controllers\Expenses;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        if (!$user->isAdmin() && !$user->hasPermission('view_expenses')) {
            abort(403, 'Unauthorized access to expense records.');
        }

        // Seed default categories if none exist
        if (ExpenseCategory::count() === 0) {
            ExpenseCategory::seedDefaults();
        }

        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $expenseType = $request->input('expense_type'); // all, staff, shop
        $paymentMethod = $request->input('payment_method');
        $status = $request->input('status', 'active'); // active, voided, all
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $month = $request->input('month');

        $query = Expense::with(['category', 'employee', 'advance', 'creator']);

        if (!empty($search)) {
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

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if (!empty($expenseType) && $expenseType !== 'all') {
            $query->where('expense_type', $expenseType);
        }

        if (!empty($paymentMethod) && $paymentMethod !== 'all') {
            $query->where('payment_method', $paymentMethod);
        }

        if ($status === 'active') {
            $query->where('status', Expense::STATUS_ACTIVE);
        } elseif ($status === 'voided') {
            $query->where('status', Expense::STATUS_VOIDED);
        }

        if (!empty($fromDate)) {
            $query->whereDate('expense_date', '>=', $fromDate);
        }

        if (!empty($toDate)) {
            $query->whereDate('expense_date', '<=', $toDate);
        }

        if (!empty($month)) {
            $query->where('related_month', $month);
        }

        // Calculate summary totals from active (non-voided) records matching current filters
        $summaryQuery = clone $query;
        if ($status !== 'voided') {
            $summaryQuery->where('status', Expense::STATUS_ACTIVE);
        }

        $totalExpenses = (float) (clone $summaryQuery)->sum('amount');
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
            'staffTotal',
            'shopTotal',
            'search',
            'categoryId',
            'expenseType',
            'paymentMethod',
            'status',
            'fromDate',
            'toDate',
            'month'
        ));
    }

    /**
     * Show form for creating a new staff expense.
     */
    public function createStaff(): View
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->hasPermission('create_expenses')) {
            abort(403, 'Unauthorized action.');
        }

        $employees = Employee::where('is_active', true)->orderBy('name')->get();
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        $currentMonth = today()->format('F Y');

        return view('expenses.records.create-staff', compact('employees', 'categories', 'currentMonth'));
    }

    /**
     * Store a newly created staff expense.
     */
    public function storeStaff(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->hasPermission('create_expenses')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'category_id' => 'required|exists:expense_categories,id',
            'payment_date' => 'required|date',
            'related_month' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:cash,bank_transfer,card,other',
            'payment_reference' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:5120',
        ], [
            'employee_id.required' => 'Please select an employee.',
            'category_id.required' => 'Please select an expense category.',
            'amount.required' => 'Please enter an amount.',
            'amount.min' => 'Amount must be greater than zero.',
            'payment_date.required' => 'Please select the payment date.',
            'payment_method.required' => 'Please choose a payment method.',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        $category = ExpenseCategory::findOrFail($validated['category_id']);
        $employee = Employee::findOrFail($validated['employee_id']);

        $expenseNumber = Expense::generateNextExpenseNumber();
        $employeeAdvanceId = null;

        DB::transaction(function () use (
            $validated,
            $category,
            $employee,
            $expenseNumber,
            $receiptPath,
            $user,
            &$employeeAdvanceId
        ) {
            // Salary Advance Integration (Section 5)
            // If category is "Salary Advance", link seamlessly to Stage 4 EmployeeAdvance
            if (strtolower(trim($category->name)) === 'salary advance') {
                $advanceNumber = EmployeeAdvance::generateNextAdvanceNumber();

                $advance = EmployeeAdvance::create([
                    'advance_number' => $advanceNumber,
                    'employee_id' => $employee->id,
                    'advance_date' => $validated['payment_date'],
                    'amount' => $validated['amount'],
                    'payment_method' => $validated['payment_method'],
                    'notes' => $validated['description'] ?? 'Salary advance disbursed via Expense Management',
                    'status' => EmployeeAdvance::STATUS_PENDING,
                    'created_by' => $user->id,
                ]);

                $employeeAdvanceId = $advance->id;
            }

            Expense::create([
                'expense_number' => $expenseNumber,
                'category_id' => $category->id,
                'expense_type' => Expense::TYPE_STAFF,
                'employee_id' => $employee->id,
                'employee_advance_id' => $employeeAdvanceId,
                'expense_date' => $validated['payment_date'],
                'related_month' => $validated['related_month'],
                'description' => $validated['description'] ?? null,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_date' => $validated['payment_date'],
                'payment_reference' => $validated['payment_reference'] ?? null,
                'receipt_path' => $receiptPath,
                'status' => Expense::STATUS_ACTIVE,
                'created_by' => $user->id,
            ]);
        });

        ActivityLog::log(
            'create_staff_expense',
            "Recorded staff expense {$expenseNumber} for {$employee->name} (LKR " . number_format($validated['amount'], 2) . ")",
            'Expenses'
        );

        $successMsg = "Staff expense {$expenseNumber} of LKR " . number_format($validated['amount'], 2) . " saved successfully.";
        if ($employeeAdvanceId) {
            $successMsg .= " Linked to Employee Salary Advance for payroll deduction.";
        }

        return redirect()->route('expenses.records.index')->with('success', $successMsg);
    }

    /**
     * Show form for creating a new shop operating expense.
     */
    public function createShop(): View
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->hasPermission('create_expenses')) {
            abort(403, 'Unauthorized action.');
        }

        $categories = ExpenseCategory::where('is_active', true)
            ->where('name', '!=', 'Salary Advance')
            ->orderBy('name')
            ->get();
        $currentMonth = today()->format('F Y');

        return view('expenses.records.create-shop', compact('categories', 'currentMonth'));
    }

    /**
     * Store a newly created shop operating expense.
     */
    public function storeShop(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->hasPermission('create_expenses')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'expense_date' => 'required|date',
            'related_month' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:cash,bank_transfer,card,other',
            'payment_date' => 'nullable|date',
            'payment_reference' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:5120',
        ], [
            'category_id.required' => 'Please select an expense category.',
            'amount.required' => 'Please enter an amount.',
            'amount.min' => 'Amount must be greater than zero.',
            'expense_date.required' => 'Please select the expense date.',
            'payment_method.required' => 'Please choose a payment method.',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        $category = ExpenseCategory::findOrFail($validated['category_id']);
        $expenseNumber = Expense::generateNextExpenseNumber();

        Expense::create([
            'expense_number' => $expenseNumber,
            'category_id' => $category->id,
            'expense_type' => Expense::TYPE_SHOP,
            'employee_id' => null,
            'employee_advance_id' => null,
            'expense_date' => $validated['expense_date'],
            'related_month' => $validated['related_month'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_date' => $validated['payment_date'] ?? $validated['expense_date'],
            'payment_reference' => $validated['payment_reference'] ?? null,
            'receipt_path' => $receiptPath,
            'status' => Expense::STATUS_ACTIVE,
            'created_by' => $user->id,
        ]);

        ActivityLog::log(
            'create_shop_expense',
            "Recorded shop expense {$expenseNumber} for {$category->name} (LKR " . number_format($validated['amount'], 2) . ")",
            'Expenses'
        );

        return redirect()->route('expenses.records.index')
            ->with('success', "Shop expense {$expenseNumber} of LKR " . number_format($validated['amount'], 2) . " saved successfully.");
    }

    /**
     * Display details of an expense record.
     */
    public function show(Expense $expense): View
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->hasPermission('view_expenses')) {
            abort(403, 'Unauthorized access.');
        }

        $expense->load(['category', 'employee', 'advance', 'creator', 'voider']);

        return view('expenses.records.show', compact('expense'));
    }

    /**
     * Show form for editing an expense record.
     */
    public function edit(Expense $expense): View
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can edit financial records.');
        }

        if ($expense->isVoided()) {
            return redirect()->route('expenses.records.show', $expense)
                ->with('error', 'Voided expense records cannot be modified.');
        }

        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        $employees = Employee::where('is_active', true)->orderBy('name')->get();

        return view('expenses.records.edit', compact('expense', 'categories', 'employees'));
    }

    /**
     * Update an expense record with audit tracking.
     */
    public function update(Request $request, Expense $expense): RedirectResponse
    {
        if (!Auth::user()->isAdmin()) {
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
            'payment_method' => 'required|string|in:cash,bank_transfer,card,other',
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

        DB::transaction(function () use ($expense, $validated, $receiptPath, $newAmount, $oldAmount) {
            $expense->update([
                'category_id' => $validated['category_id'],
                'expense_date' => $validated['expense_date'],
                'related_month' => $validated['related_month'],
                'amount' => $newAmount,
                'payment_method' => $validated['payment_method'],
                'payment_date' => $validated['payment_date'] ?? $validated['expense_date'],
                'payment_reference' => $validated['payment_reference'] ?? null,
                'description' => $validated['description'] ?? null,
                'receipt_path' => $receiptPath,
            ]);

            // If linked to an employee advance that is still pending, update advance amount
            if ($expense->advance && $expense->advance->status === EmployeeAdvance::STATUS_PENDING) {
                $expense->advance->update([
                    'amount' => $newAmount,
                    'advance_date' => $validated['expense_date'],
                    'payment_method' => $validated['payment_method'],
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
        if (!Auth::user()->isAdmin()) {
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
            "Voided expense {$expense->expense_number} of LKR " . number_format($expense->amount, 2) . ". Reason: {$validated['void_reason']}",
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
        if (!$user->isAdmin() && !$user->hasPermission('view_expenses')) {
            abort(403, 'Unauthorized access.');
        }

        if (!$expense->receipt_path || !Storage::disk('public')->exists($expense->receipt_path)) {
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
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $expenseType = $request->input('expense_type');
        $paymentMethod = $request->input('payment_method');
        $status = $request->input('status', 'active');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $query = Expense::with(['category', 'employee']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('expense_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }
        if (!empty($expenseType) && $expenseType !== 'all') {
            $query->where('expense_type', $expenseType);
        }
        if (!empty($paymentMethod) && $paymentMethod !== 'all') {
            $query->where('payment_method', $paymentMethod);
        }
        if ($status === 'active') {
            $query->where('status', Expense::STATUS_ACTIVE);
        } elseif ($status === 'voided') {
            $query->where('status', Expense::STATUS_VOIDED);
        }
        if (!empty($fromDate)) {
            $query->whereDate('expense_date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('expense_date', '<=', $toDate);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=Expense_Records_' . date('Y-m-d') . '.csv',
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
                'Employee',
                'Description',
                'Amount (LKR)',
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
                    $e->employee ? $e->employee->name : '',
                    $e->description,
                    number_format($e->amount, 2, '.', ''),
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
