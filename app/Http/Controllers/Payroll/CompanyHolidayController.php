<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\CompanyHoliday;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CompanyHolidayController extends Controller
{
    private function authorizeView(): void
    {
        $user = Auth::user();
        if (! $user || (! $user->isAdmin() && ! $user->hasPermission('view_holidays'))) {
            abort(403, 'Unauthorized access to company holidays.');
        }
    }

    private function authorizeAdmin(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Administrator access required to manage company holidays.');
        }
    }

    /**
     * Display listing of company holidays with month/year and branch filters.
     */
    public function index(Request $request): View
    {
        $this->authorizeView();

        $selectedYear = (int) $request->input('year', Carbon::now('Asia/Colombo')->year);
        $typeFilter = $request->input('type', 'all');
        $branchFilter = $request->input('branch_id', 'all');
        $statusFilter = $request->input('status', 'all');

        $isTableReady = Schema::hasTable('company_holidays');

        $holidays = collect();
        if ($isTableReady) {
            $query = CompanyHoliday::with(['branch', 'employees', 'creator']);

            if ($selectedYear) {
                $query->where(function ($q) use ($selectedYear) {
                    $q->whereYear('start_date', $selectedYear)
                        ->orWhereYear('end_date', $selectedYear);
                });
            }

            if ($typeFilter !== 'all') {
                $query->where('holiday_type', $typeFilter);
            }

            if ($branchFilter !== 'all') {
                $query->where(function ($q) use ($branchFilter) {
                    $q->where('branch_id', $branchFilter)
                        ->orWhereNull('branch_id');
                });
            }

            if ($statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }

            $holidays = $query->orderBy('start_date', 'asc')->get();
        }

        $years = range(Carbon::now('Asia/Colombo')->year - 1, Carbon::now('Asia/Colombo')->year + 2);
        $branches = Branch::where('is_active', true)->orderBy('branch_name')->get();
        $employees = Employee::where('is_active', true)->orderBy('name')->get();

        $totalHolidays = $holidays->count();
        $paidCount = $holidays->where('status', CompanyHoliday::STATUS_ACTIVE)->where('holiday_type', CompanyHoliday::TYPE_PAID)->count();
        $unpaidCount = $holidays->where('status', CompanyHoliday::STATUS_ACTIVE)->where('holiday_type', CompanyHoliday::TYPE_UNPAID)->count();

        return view('payroll.holidays.index', compact(
            'holidays',
            'selectedYear',
            'typeFilter',
            'branchFilter',
            'statusFilter',
            'years',
            'branches',
            'employees',
            'isTableReady',
            'totalHolidays',
            'paidCount',
            'unpaidCount'
        ));
    }

    /**
     * Store a newly created company holiday.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        if (! Schema::hasTable('company_holidays')) {
            return back()->with('error', 'The company_holidays table does not exist in the database yet. Please run migrations first.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'duration' => 'required|string|in:full_day,half_day',
            'holiday_type' => 'required|string|in:paid,unpaid',
            'branch_id' => 'nullable|exists:branches,id',
            'applies_to_all' => 'nullable|boolean',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:employees,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'] ?: $startDate;
        $appliesToAll = $request->boolean('applies_to_all', true);

        // Duplicate prevention: check if holiday with same name overlaps date range on same branch
        $duplicateQuery = CompanyHoliday::active()
            ->where('name', $validated['name'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($sub) use ($startDate, $endDate) {
                        $sub->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            });

        if (! empty($validated['branch_id'])) {
            $duplicateQuery->where(function ($q) use ($validated) {
                $q->where('branch_id', $validated['branch_id'])
                    ->orWhereNull('branch_id');
            });
        }

        if ($duplicateQuery->exists()) {
            return back()->withInput()->with('error', 'A holiday with this name and overlapping dates already exists.');
        }

        DB::transaction(function () use ($validated, $startDate, $endDate, $appliesToAll) {
            $holiday = CompanyHoliday::create([
                'branch_id' => $validated['branch_id'] ?? null,
                'name' => $validated['name'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'duration' => $validated['duration'],
                'holiday_type' => $validated['holiday_type'],
                'applies_to_all' => $appliesToAll,
                'status' => CompanyHoliday::STATUS_ACTIVE,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            if (! $appliesToAll && ! empty($validated['employee_ids'])) {
                $holiday->employees()->sync($validated['employee_ids']);
            }
        });

        ActivityLog::log(
            'create_holiday',
            "Declared company holiday '{$validated['name']}' from {$startDate} to {$endDate} (".ucfirst($validated['holiday_type']).', '.ucfirst(str_replace('_', ' ', $validated['duration'])).')',
            'Payroll'
        );

        return redirect()->route('payroll.holidays.index')
            ->with('success', "Company holiday '{$validated['name']}' created successfully.");
    }

    /**
     * Update an existing company holiday.
     */
    public function update(Request $request, CompanyHoliday $holiday): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'duration' => 'required|string|in:full_day,half_day',
            'holiday_type' => 'required|string|in:paid,unpaid',
            'branch_id' => 'nullable|exists:branches,id',
            'applies_to_all' => 'nullable|boolean',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:employees,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'] ?: $startDate;
        $appliesToAll = $request->boolean('applies_to_all', true);

        DB::transaction(function () use ($holiday, $validated, $startDate, $endDate, $appliesToAll) {
            $holiday->update([
                'branch_id' => $validated['branch_id'] ?? null,
                'name' => $validated['name'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'duration' => $validated['duration'],
                'holiday_type' => $validated['holiday_type'],
                'applies_to_all' => $appliesToAll,
                'notes' => $validated['notes'] ?? null,
            ]);

            if (! $appliesToAll && ! empty($validated['employee_ids'])) {
                $holiday->employees()->sync($validated['employee_ids']);
            } else {
                $holiday->employees()->detach();
            }
        });

        ActivityLog::log(
            'update_holiday',
            "Updated company holiday '{$holiday->name}'",
            'Payroll'
        );

        return redirect()->route('payroll.holidays.index')
            ->with('success', "Company holiday '{$holiday->name}' updated successfully.");
    }

    /**
     * Cancel an active company holiday (soft cancel).
     */
    public function cancel(CompanyHoliday $holiday): RedirectResponse
    {
        $this->authorizeAdmin();

        $holiday->status = $holiday->status === CompanyHoliday::STATUS_ACTIVE
            ? CompanyHoliday::STATUS_CANCELLED
            : CompanyHoliday::STATUS_ACTIVE;
        $holiday->save();

        $action = $holiday->status === CompanyHoliday::STATUS_CANCELLED ? 'cancelled' : 'restored';

        ActivityLog::log(
            'cancel_holiday',
            "Company holiday '{$holiday->name}' has been {$action}.",
            'Payroll'
        );

        return back()->with('success', "Company holiday '{$holiday->name}' has been {$action} successfully.");
    }

    /**
     * Delete a company holiday permanently.
     */
    public function destroy(CompanyHoliday $holiday): RedirectResponse
    {
        $this->authorizeAdmin();

        $name = $holiday->name;
        $holiday->employees()->detach();
        $holiday->delete();

        ActivityLog::log(
            'delete_holiday',
            "Deleted company holiday '{$name}'",
            'Payroll'
        );

        return redirect()->route('payroll.holidays.index')
            ->with('success', "Company holiday '{$name}' deleted successfully.");
    }
}
