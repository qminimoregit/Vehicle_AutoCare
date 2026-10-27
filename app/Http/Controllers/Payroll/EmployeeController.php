<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\User;
use App\Services\BranchContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display a paginated listing of employees with search and status filtering.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('view_employees')) {
            abort(403, 'Unauthorized access to employee records.');
        }

        $search = $request->input('search');
        $status = $request->input('status', 'all');

        $query = Employee::query()->with('wageRates');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('employee_number', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $employees = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();

        $activeCount = Employee::where('is_active', true)->count();
        $inactiveCount = Employee::where('is_active', false)->count();

        return view('payroll.employees.index', compact('employees', 'search', 'status', 'activeCount', 'inactiveCount'));
    }

    /**
     * Show the form for creating a new employee.
     */
    /**
     * Show the form for creating a new employee.
     */
    public function create(): View
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can register new employees.');
        }

        $nextEmployeeNumber = Employee::generateNextEmployeeNumber();
        // Available users that are not already linked to an employee
        $linkedUserIds = Employee::whereNotNull('user_id')->pluck('user_id');
        $availableUsers = User::whereNotIn('id', $linkedUserIds)->where('status', 'active')->get();
        $branches = Branch::where('is_active', true)->orderBy('branch_name')->get();

        return view('payroll.employees.create', compact('nextEmployeeNumber', 'availableUsers', 'branches'));
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can register new employees.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'mobile' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'position' => 'nullable|string|max:100',
            'branch_id' => 'nullable|exists:branches,id',
            'salary_type' => 'required|string|in:daily,monthly',
            'daily_wage' => 'nullable|required_if:salary_type,daily|numeric|min:0',
            'monthly_basic_salary' => 'nullable|required_if:salary_type,monthly|numeric|min:0',
            'joining_date' => 'nullable|date',
            'payment_method' => 'required|string|in:cash,bank_transfer,cheque,other',
            'bank_name' => 'nullable|string|max:100',
            'bank_branch' => 'nullable|string|max:100',
            'bank_account_name' => 'nullable|string|max:150',
            'bank_account_number' => 'nullable|string|max:50',
            'epf_number' => 'nullable|string|max:50',
            'is_epf_eligible' => 'nullable|boolean',
            'is_etf_eligible' => 'nullable|boolean',
            'eligible_for_off_days' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $salaryType = $validated['salary_type'];
        $dailyWage = $salaryType === Employee::SALARY_TYPE_DAILY ? (float) ($validated['daily_wage'] ?? 0) : 0.00;
        $monthlySalary = $salaryType === Employee::SALARY_TYPE_MONTHLY ? (float) ($validated['monthly_basic_salary'] ?? 0) : 0.00;

        $employeeNumber = Employee::generateNextEmployeeNumber();

        $employee = Employee::create([
            'employee_number' => $employeeNumber,
            'branch_id' => $validated['branch_id'] ?? BranchContext::getActiveBranchId(),
            'name' => $validated['name'],
            'mobile' => $validated['mobile'] ?? null,
            'address' => $validated['address'] ?? null,
            'position' => $validated['position'] ?? null,
            'salary_type' => $salaryType,
            'daily_wage' => $dailyWage,
            'monthly_basic_salary' => $monthlySalary,
            'payment_method' => $validated['payment_method'],
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_branch' => $validated['bank_branch'] ?? null,
            'bank_account_name' => $validated['bank_account_name'] ?? null,
            'bank_account_number' => $validated['bank_account_number'] ?? null,
            'epf_number' => $validated['epf_number'] ?? null,
            'is_epf_eligible' => $request->boolean('is_epf_eligible', true),
            'is_etf_eligible' => $request->boolean('is_etf_eligible', true),
            'eligible_for_off_days' => $request->boolean('eligible_for_off_days', true),
            'joining_date' => $validated['joining_date'] ?? today(),
            'is_active' => true,
            'notes' => $validated['notes'] ?? null,
            'user_id' => $validated['user_id'] ?? null,
        ]);

        // Record initial wage/salary rate in history
        $effectiveDate = $validated['joining_date'] ?? today()->toDateString();
        $employee->wageRates()->create([
            'salary_type' => $salaryType,
            'daily_wage' => $dailyWage,
            'monthly_basic_salary' => $monthlySalary,
            'effective_from' => $effectiveDate,
            'effective_to' => null,
            'reason' => 'Initial salary settings upon registration',
        ]);

        $rateDisplay = $salaryType === Employee::SALARY_TYPE_MONTHLY
            ? 'Monthly Basic LKR '.number_format($monthlySalary, 2)
            : 'Daily Wage LKR '.number_format($dailyWage, 2);

        ActivityLog::log(
            'create_employee',
            "Registered new employee {$employee->name} ({$employee->employee_number}) as ".ucfirst($salaryType)." employee ({$rateDisplay})",
            'Payroll'
        );

        return redirect()->route('payroll.employees.index')
            ->with('success', "Employee {$employee->name} ({$employee->employee_number}) registered successfully.");
    }

    /**
     * Display employee details, attendance history, and salary history.
     */
    public function show(Employee $employee): View
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('view_employees')) {
            abort(403, 'Unauthorized access to employee details.');
        }

        $employee->load([
            'wageRates' => fn ($q) => $q->orderBy('effective_from', 'desc'),
            'attendances' => fn ($q) => $q->orderBy('attendance_date', 'desc')->take(30),
            'advances' => fn ($q) => $q->orderBy('advance_date', 'desc'),
            'payrollItems.period',
            'user',
            'branch',
        ]);

        return view('payroll.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing an employee.
     */
    public function edit(Employee $employee): View
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can edit employee details.');
        }

        // Available users that are not already linked to another employee
        $linkedUserIds = Employee::whereNotNull('user_id')
            ->where('id', '!=', $employee->id)
            ->pluck('user_id');
        $availableUsers = User::whereNotIn('id', $linkedUserIds)->where('status', 'active')->get();
        $branches = Branch::where('is_active', true)->orderBy('branch_name')->get();

        return view('payroll.employees.edit', compact('employee', 'availableUsers', 'branches'));
    }

    /**
     * Update employee details and track daily wage / monthly salary changes.
     */
    public function update(Request $request, Employee $employee): RedirectResponse
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can update employee details.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'mobile' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'position' => 'nullable|string|max:100',
            'branch_id' => 'nullable|exists:branches,id',
            'salary_type' => 'required|string|in:daily,monthly',
            'daily_wage' => 'nullable|required_if:salary_type,daily|numeric|min:0',
            'monthly_basic_salary' => 'nullable|required_if:salary_type,monthly|numeric|min:0',
            'wage_effective_from' => 'nullable|date',
            'wage_reason' => 'nullable|string|max:255',
            'joining_date' => 'nullable|date',
            'is_active' => 'required|boolean',
            'payment_method' => 'required|string|in:cash,bank_transfer,cheque,other',
            'bank_name' => 'nullable|string|max:100',
            'bank_branch' => 'nullable|string|max:100',
            'bank_account_name' => 'nullable|string|max:150',
            'bank_account_number' => 'nullable|string|max:50',
            'epf_number' => 'nullable|string|max:50',
            'is_epf_eligible' => 'nullable|boolean',
            'is_etf_eligible' => 'nullable|boolean',
            'eligible_for_off_days' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $newSalaryType = $validated['salary_type'];
        $newDailyWage = $newSalaryType === Employee::SALARY_TYPE_DAILY ? (float) ($validated['daily_wage'] ?? 0) : 0.00;
        $newMonthlySalary = $newSalaryType === Employee::SALARY_TYPE_MONTHLY ? (float) ($validated['monthly_basic_salary'] ?? 0) : 0.00;

        $oldSalaryType = $employee->salary_type ?? Employee::SALARY_TYPE_DAILY;
        $oldDailyWage = (float) $employee->daily_wage;
        $oldMonthlySalary = (float) ($employee->monthly_basic_salary ?? 0);

        // Check if salary type or relevant rate changed
        $typeChanged = $newSalaryType !== $oldSalaryType;
        $rateChanged = $newSalaryType === Employee::SALARY_TYPE_DAILY
            ? (abs($newDailyWage - $oldDailyWage) >= 0.01)
            : (abs($newMonthlySalary - $oldMonthlySalary) >= 0.01);

        if ($typeChanged || $rateChanged) {
            $effectiveFrom = $validated['wage_effective_from'] ?: today()->toDateString();
            $reason = $validated['wage_reason'] ?: 'Salary settings updated by Admin';

            $employee->updateSalaryRate($newSalaryType, $newDailyWage, $newMonthlySalary, $effectiveFrom, $reason);

            ActivityLog::log(
                'update_employee_wage',
                "Updated salary settings for {$employee->name}: Type {$newSalaryType}, Daily LKR {$newDailyWage}, Monthly LKR {$newMonthlySalary} effective {$effectiveFrom}",
                'Payroll'
            );
        }

        $employee->update([
            'name' => $validated['name'],
            'mobile' => $validated['mobile'] ?? null,
            'address' => $validated['address'] ?? null,
            'joining_date' => $validated['joining_date'] ?? $employee->joining_date,
            'position' => $validated['position'] ?? null,
            'branch_id' => $validated['branch_id'] ?? $employee->branch_id,
            'is_active' => (bool) $validated['is_active'],
            'payment_method' => $validated['payment_method'],
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_branch' => $validated['bank_branch'] ?? null,
            'bank_account_name' => $validated['bank_account_name'] ?? null,
            'bank_account_number' => $validated['bank_account_number'] ?? null,
            'epf_number' => $validated['epf_number'] ?? null,
            'is_epf_eligible' => $request->boolean('is_epf_eligible'),
            'is_etf_eligible' => $request->boolean('is_etf_eligible'),
            'eligible_for_off_days' => $request->boolean('eligible_for_off_days'),
            'notes' => $validated['notes'] ?? null,
            'user_id' => $validated['user_id'] ?? null,
        ]);

        ActivityLog::log(
            'update_employee',
            "Updated employee details for {$employee->name} ({$employee->employee_number})",
            'Payroll'
        );

        return redirect()->route('payroll.employees.show', $employee)
            ->with('success', "Employee {$employee->name} updated successfully.");
    }

    /**
     * Toggle active/inactive status safely.
     */
    public function toggleStatus(Employee $employee): RedirectResponse
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can change employee status.');
        }

        $employee->is_active = ! $employee->is_active;
        $employee->save();

        $statusStr = $employee->is_active ? 'activated' : 'deactivated';

        ActivityLog::log(
            'toggle_employee_status',
            "Employee {$employee->name} was {$statusStr}.",
            'Payroll'
        );

        return back()->with('success', "Employee {$employee->name} was {$statusStr} successfully.");
    }
}
