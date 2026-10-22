<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Services\BranchContext;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AttendanceReminderController extends Controller
{
    /**
     * Check if the daily attendance reminder should be displayed.
     * Authority for time: Laravel server in Asia/Colombo timezone.
     */
    public function status(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user || (! $user->isAdmin() && ! $user->hasPermission('mark_attendance'))) {
            return response()->json([
                'show_reminder' => false,
                'authorized' => false,
            ]);
        }

        // Server authority: Current Sri Lanka local time (Asia/Colombo, UTC +05:30)
        $now = Carbon::now('Asia/Colombo');
        $today = $now->toDateString();
        $currentHour = (int) $now->hour;

        // Condition 1: Current Sri Lanka time >= 15:00 (3:00 PM)
        if ($currentHour < 15) {
            return response()->json([
                'show_reminder' => false,
                'reason' => 'before_3pm',
                'server_time' => $now->format('H:i'),
                'timezone' => 'Asia/Colombo',
                'unmarked_count' => 0,
            ]);
        }

        // Branch scoping: Check attendance only for the selected branch
        $activeBranchId = BranchContext::getActiveBranchId();

        // Check if previously dismissed today by this user for this branch
        $branchKey = $activeBranchId ?? 'all';
        $sessionKey = "attendance_dismissed_{$today}_{$branchKey}";
        $cacheKey = "attendance_dismissed_user_{$user->id}_{$today}_{$branchKey}";

        if (session($sessionKey) || Cache::get($cacheKey)) {
            return response()->json([
                'show_reminder' => false,
                'reason' => 'dismissed',
                'server_time' => $now->format('H:i'),
                'timezone' => 'Asia/Colombo',
                'unmarked_count' => 0,
            ]);
        }

        // Query active employees for the selected branch (excluding inactive/unscheduled staff)
        $employeeQuery = Employee::where('is_active', true);
        if ($activeBranchId) {
            $employeeQuery->where('branch_id', $activeBranchId);
        }
        $activeEmployees = $employeeQuery->get(['id', 'name', 'branch_id']);
        $totalActive = $activeEmployees->count();

        if ($totalActive === 0) {
            return response()->json([
                'show_reminder' => false,
                'reason' => 'no_active_employees',
                'server_time' => $now->format('H:i'),
                'timezone' => 'Asia/Colombo',
                'unmarked_count' => 0,
            ]);
        }

        // All saved attendance statuses except Not Marked count as marked:
        // Full Day, Half Day, Absent, Leave, Day Off.
        $markedStatuses = [
            EmployeeAttendance::STATUS_FULL_DAY,
            EmployeeAttendance::STATUS_HALF_DAY,
            EmployeeAttendance::STATUS_ABSENT,
            EmployeeAttendance::STATUS_LEAVE,
            EmployeeAttendance::STATUS_DAY_OFF,
        ];

        $markedEmployeeIds = EmployeeAttendance::whereDate('attendance_date', $today)
            ->whereIn('status', $markedStatuses)
            ->pluck('employee_id')
            ->toArray();

        // If an employee has no attendance record or status is not_marked, count as Not Marked
        $unmarkedEmployees = $activeEmployees->reject(function ($employee) use ($markedEmployeeIds) {
            return in_array($employee->id, $markedEmployeeIds);
        });

        $unmarkedCount = $unmarkedEmployees->count();

        // Condition 2: One or more employees have attendance not marked for today
        if ($unmarkedCount > 0) {
            $message = $unmarkedCount === 1
                ? '1 employee has not been marked today.'
                : "{$unmarkedCount} employees have not been marked today.";

            return response()->json([
                'show_reminder' => true,
                'unmarked_count' => $unmarkedCount,
                'message' => $message,
                'server_time' => $now->format('H:i'),
                'timezone' => 'Asia/Colombo',
                'attendance_url' => route('payroll.attendance.index', ['date' => $today]),
            ]);
        }

        // All required attendance marked
        return response()->json([
            'show_reminder' => false,
            'reason' => 'all_marked',
            'server_time' => $now->format('H:i'),
            'timezone' => 'Asia/Colombo',
            'unmarked_count' => 0,
        ]);
    }

    /**
     * Dismiss the attendance reminder for the current user and attendance date.
     */
    public function dismiss(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false], 401);
        }

        $now = Carbon::now('Asia/Colombo');
        $today = $now->toDateString();
        $activeBranchId = BranchContext::getActiveBranchId();

        $branchKey = $activeBranchId ?? 'all';
        $sessionKey = "attendance_dismissed_{$today}_{$branchKey}";
        $cacheKey = "attendance_dismissed_user_{$user->id}_{$today}_{$branchKey}";

        session([$sessionKey => true]);
        Cache::put($cacheKey, true, $now->copy()->endOfDay());

        return response()->json([
            'success' => true,
            'message' => 'Attendance reminder dismissed for today.',
            'date' => $today,
        ]);
    }
}
