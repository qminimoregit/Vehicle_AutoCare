<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Show daily attendance marking interface and monthly completion calendar.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('mark_attendance')) {
            abort(403, 'Unauthorized access to employee attendance.');
        }

        $today = Carbon::today('Asia/Colombo');
        $dateStr = $request->input('date', $today->toDateString());
        $selectedDate = Carbon::parse($dateStr, 'Asia/Colombo');

        // Staff can only mark for today or yesterday; Admin can mark/edit any date
        $canEdit = $user->isAdmin() || $selectedDate->isSameDay($today) || $selectedDate->isYesterday();

        // Active employees list, plus any inactive employees who have a record for this date
        $recordedEmpIds = EmployeeAttendance::whereDate('attendance_date', $selectedDate->toDateString())
            ->pluck('employee_id');

        $employees = Employee::where('is_active', true)
            ->orWhereIn('id', $recordedEmpIds)
            ->orderBy('name', 'asc')
            ->get();

        // Existing records for selected date
        $existingRecords = EmployeeAttendance::whereDate('attendance_date', $selectedDate->toDateString())
            ->get()
            ->keyBy('employee_id');

        // Attendance summary for selected date
        $summary = [
            'total' => $employees->count(),
            'full_day' => 0,
            'half_day' => 0,
            'absent' => 0,
            'leave' => 0,
            'day_off' => 0,
            'not_marked' => 0,
        ];

        foreach ($employees as $employee) {
            $record = $existingRecords->get($employee->id);
            if ($record) {
                $statusKey = $record->status;
                if (isset($summary[$statusKey])) {
                    $summary[$statusKey]++;
                }
            } else {
                $summary['not_marked']++;
            }
        }

        // Calendar Month calculation
        $calMonth = (int) $request->input('cal_month', $selectedDate->month);
        $calYear = (int) $request->input('cal_year', $selectedDate->year);
        $calendarDate = Carbon::createFromDate($calYear, $calMonth, 1);

        $daysInMonth = $calendarDate->daysInMonth;
        $activeEmployeesCount = Employee::where('is_active', true)->count();

        // Fetch all attendance counts grouped by date for this calendar month
        $monthStart = $calendarDate->copy()->startOfMonth()->toDateString();
        $monthEnd = $calendarDate->copy()->endOfMonth()->toDateString();

        $monthAttendanceCounts = EmployeeAttendance::whereBetween('attendance_date', [$monthStart, $monthEnd])
            ->select('attendance_date', DB::raw('count(*) as marked_count'))
            ->groupBy('attendance_date')
            ->pluck('marked_count', 'attendance_date')
            ->toArray();

        // Build calendar days status map
        $calendarDays = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDay = Carbon::createFromDate($calYear, $calMonth, $day);
            $dayKey = $currentDay->toDateString();
            $markedCount = $monthAttendanceCounts[$dayKey] ?? 0;
            $isSunday = $currentDay->isSunday();
            $isFuture = $currentDay->isAfter($today);

            if ($isFuture || ($isSunday && $markedCount === 0)) {
                $color = 'grey'; // Future or non-working day
                $statusText = $isFuture ? 'Future' : 'Non-working';
            } elseif ($activeEmployeesCount > 0 && $markedCount >= $activeEmployeesCount) {
                $color = 'green'; // All marked
                $statusText = 'Completed';
            } elseif ($markedCount > 0) {
                $color = 'amber'; // Partially marked
                $statusText = "Partial ({$markedCount}/{$activeEmployeesCount})";
            } else {
                $color = 'red'; // Past working day with missing attendance
                $statusText = 'Missing';
            }

            $calendarDays[] = [
                'day' => $day,
                'date' => $dayKey,
                'day_name' => $currentDay->format('D'),
                'is_today' => $currentDay->isSameDay($today),
                'is_selected' => $currentDay->isSameDay($selectedDate),
                'marked_count' => $markedCount,
                'color' => $color,
                'status_text' => $statusText,
            ];
        }

        return view('payroll.attendance.index', compact(
            'selectedDate',
            'canEdit',
            'employees',
            'existingRecords',
            'summary',
            'calendarDate',
            'calendarDays',
            'calMonth',
            'calYear'
        ));
    }

    /**
     * Store or bulk-update daily attendance records.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('mark_attendance')) {
            abort(403, 'Unauthorized to record attendance.');
        }

        $validated = $request->validate([
            'attendance_date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.status' => 'nullable|string|in:full_day,half_day,absent,leave,day_off,not_marked',
            'attendances.*.present' => 'nullable|boolean',
            'attendances.*.half_day' => 'nullable|boolean',
            'attendances.*.notes' => 'nullable|string|max:255',
            'attendances.*.is_paid_leave' => 'nullable|boolean',
        ]);

        $today = Carbon::today('Asia/Colombo');
        $attendanceDate = Carbon::parse($validated['attendance_date'], 'Asia/Colombo')->toDateString();
        $targetDate = Carbon::parse($attendanceDate, 'Asia/Colombo');

        // Security check for staff: only today or yesterday
        if (! $user->isAdmin() && ! $targetDate->isSameDay($today) && ! $targetDate->isYesterday()) {
            abort(403, 'Staff members cannot modify historical attendance beyond yesterday.');
        }

        $savedCount = 0;
        $clearedCount = 0;

        DB::transaction(function () use ($validated, $attendanceDate, $user, &$savedCount, &$clearedCount) {
            foreach ($validated['attendances'] as $employeeId => $data) {
                $employee = Employee::find($employeeId);
                if (! $employee) {
                    continue;
                }

                // Determine attendance status: either from direct status or from simplified checkboxes
                if (isset($data['status'])) {
                    $status = $data['status'];
                } else {
                    $isPresent = ! empty($data['present']);
                    $isHalfDay = ! empty($data['half_day']);

                    if ($isPresent && $isHalfDay) {
                        $status = EmployeeAttendance::STATUS_HALF_DAY;
                    } elseif ($isPresent) {
                        $status = EmployeeAttendance::STATUS_FULL_DAY;
                    } else {
                        $status = EmployeeAttendance::STATUS_ABSENT;
                    }
                }

                $notes = $data['notes'] ?? null;
                $isPaidLeave = ! empty($data['is_paid_leave']);

                if ($status === 'not_marked') {
                    // Remove record if existing
                    $deleted = EmployeeAttendance::where('employee_id', $employeeId)
                        ->whereDate('attendance_date', $attendanceDate)
                        ->delete();
                    if ($deleted) {
                        $clearedCount++;
                    }

                    continue;
                }

                // Retrieve wage rate applicable on that specific date
                $dailyWage = $employee->getWageForDate($attendanceDate);
                $payableFraction = EmployeeAttendance::getStatusFraction($status, $isPaidLeave);
                $dailyEarnings = round($payableFraction * $dailyWage, 2);

                $existingAttendance = EmployeeAttendance::where('employee_id', $employeeId)
                    ->whereDate('attendance_date', $attendanceDate)
                    ->first();

                if ($existingAttendance) {
                    $existingAttendance->update([
                        'status' => $status,
                        'payable_day_fraction' => $payableFraction,
                        'applied_daily_wage' => $dailyWage,
                        'daily_earnings' => $dailyEarnings,
                        'notes' => $notes,
                        'recorded_by' => $user->id,
                    ]);
                } else {
                    EmployeeAttendance::create([
                        'employee_id' => $employeeId,
                        'attendance_date' => $attendanceDate,
                        'status' => $status,
                        'payable_day_fraction' => $payableFraction,
                        'applied_daily_wage' => $dailyWage,
                        'daily_earnings' => $dailyEarnings,
                        'notes' => $notes,
                        'recorded_by' => $user->id,
                    ]);
                }

                $savedCount++;
            }
        });

        ActivityLog::log(
            'record_attendance',
            "Recorded daily attendance for {$attendanceDate} ({$savedCount} employees updated)",
            'Payroll'
        );

        return redirect()->route('payroll.attendance.index', ['date' => $attendanceDate])
            ->with('success', "Attendance for {$attendanceDate} recorded successfully ({$savedCount} updated).");
    }
}
