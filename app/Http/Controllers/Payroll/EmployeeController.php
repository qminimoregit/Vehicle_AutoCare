<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\User;
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
        if (!$user->isAdmin() && !$user->hasPermission('view_employees')) {
            abort(403, 'Unauthorized access to employee records.');
        }

        $search = $request->input('search');
        $status = $request->input('status', 'all');

        $query = Employee::query()->with('wageRates');

        if (!empty($search)) {
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
    public function create(): View
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can register new employees.');
        }

        $nextEmployeeNumber = Employee::generateNextEmployeeNumber();
        // Available users that are not already linked to an employee
        $linkedUserIds = Employee::whereNotNull('user_id')->pluck('user_id');
        $availableUsers = User::whereNotIn('id', $linkedUserIds)->where('status', 'active')->get();

        return view('payroll.employees.create', compact('nextEmployeeNumber', 'availableUsers'));
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can register new employees.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'mobile' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'daily_wage' => 'required|numeric|min:0',
            'joining_date' => 'nullable|date',
            'position' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $employeeNumber = Employee::generateNextEmployeeNumber();

        $employee = Employee::create([
            'employee_number' => $employeeNumber,
            'name' => $validated['name'],
            'mobile' => $validated['mobile'] ?? null,
            'address' => $validated['address'] ?? null,
            'daily_wage' => $validated['daily_wage'],
            'joining_date' => $validated['joining_date'] ?? today(),
            'position' => $validated['position'] ?? null,
            'is_active' => true,
            'notes' => $validated['notes'] ?? null,
            'user_id' => $validated['user_id'] ?? null,
        ]);

        // Record initial wage rate
        $effectiveDate = $validated['joining_date'] ?? today()->toDateString();
        $employee->wageRates()->create([
            'daily_wage' => $validated['daily_wage'],
            'effective_from' => $effectiveDate,
            'effective_to' => null,
            'reason' => 'Initial wage rate upon registration',
        ]);

        ActivityLog::log(
            'create_employee',
            "Registered new employee {$employee->name} ({$employee->employee_number}) with daily wage LKR " . number_format($employee->daily_wage, 2),
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
        if (!$user->isAdmin() && !$user->hasPermission('view_employees')) {
            abort(403, 'Unauthorized access to employee details.');
        }

        $employee->load([
            'wageRates' => fn ($q) => $q->orderBy('effective_from', 'desc'),
            'attendances' => fn ($q) => $q->orderBy('attendance_date', 'desc')->take(30),
            'advances' => fn ($q) => $q->orderBy('advance_date', 'desc'),
            'payrollItems.period',
            'user',
        ]);

        return view('payroll.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing an employee.
     */
    public function edit(Employee $employee): View
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can edit employee details.');
        }

        // Available users that are not already linked to another employee
        $linkedUserIds = Employee::whereNotNull('user_id')
            ->where('id', '!=', $employee->id)
            ->pluck('user_id');
        $availableUsers = User::whereNotIn('id', $linkedUserIds)->where('status', 'active')->get();

        return view('payroll.employees.edit', compact('employee', 'availableUsers'));
    }

    /**
     * Update employee details and track daily wage changes.
     */
    public function update(Request $request, Employee $employee): RedirectResponse
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can update employee details.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'mobile' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'daily_wage' => 'required|numeric|min:0',
            'wage_effective_from' => 'nullable|date',
            'wage_reason' => 'nullable|string|max:255',
            'joining_date' => 'nullable|date',
            'position' => 'nullable|string|max:100',
            'is_active' => 'required|boolean',
            'notes' => 'nullable|string|max:1000',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $newWage = (float) $validated['daily_wage'];
        $oldWage = (float) $employee->daily_wage;

        // If daily wage changed, record in wage rate history with effective date
        if (abs($newWage - $oldWage) >= 0.01) {
            $effectiveFrom = $validated['wage_effective_from'] ?: today()->toDateString();
            $reason = $validated['wage_reason'] ?: 'Daily wage updated by Admin';
            $employee->updateWage($newWage, $effectiveFrom, $reason);

            ActivityLog::log(
                'update_employee_wage',
                "Updated wage for {$employee->name} from LKR " . number_format($oldWage, 2) . " to LKR " . number_format($newWage, 2) . " effective {$effectiveFrom}",
                'Payroll'
            );
        }

        $employee->update([
            'name' => $validated['name'],
            'mobile' => $validated['mobile'] ?? null,
            'address' => $validated['address'] ?? null,
            'joining_date' => $validated['joining_date'] ?? $employee->joining_date,
            'position' => $validated['position'] ?? null,
            'is_active' => (bool) $validated['is_active'],
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
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can change employee status.');
        }

        $employee->is_active = !$employee->is_active;
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
