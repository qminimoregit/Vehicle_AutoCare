<?php

namespace App\Models;

use App\Models\EmployeeAttendance;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollItem extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_PARTIALLY_PAID = 'partially_paid';

    public const STATUS_PAID = 'paid';

    protected $fillable = [
        'payroll_period_id',
        'employee_id',
        'employee_name_snapshot',
        'daily_wage_snapshot',
        'full_days_count',
        'half_days_count',
        'absent_days_count',
        'leave_days_count',
        'day_off_days_count',
        'payable_days',
        'base_earnings',
        'overtime_amount',
        'bonus_amount',
        'deductions',
        'epf_employee_amount',
        'epf_employer_amount',
        'etf_employer_amount',
        'advances_applied',
        'gross_salary',
        'net_salary',
        'paid_amount',
        'balance_due',
        'status',
        'attendance_breakdown',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'daily_wage_snapshot' => 'decimal:2',
            'payable_days' => 'decimal:2',
            'base_earnings' => 'decimal:2',
            'overtime_amount' => 'decimal:2',
            'bonus_amount' => 'decimal:2',
            'deductions' => 'decimal:2',
            'epf_employee_amount' => 'decimal:2',
            'epf_employer_amount' => 'decimal:2',
            'etf_employer_amount' => 'decimal:2',
            'advances_applied' => 'decimal:2',
            'gross_salary' => 'decimal:2',
            'net_salary' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance_due' => 'decimal:2',
            'attendance_breakdown' => 'array',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function advances(): HasMany
    {
        return $this->hasMany(EmployeeAdvance::class, 'payroll_item_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PayrollPayment::class, 'payroll_item_id');
    }

    /**
     * Cache for resolved attendance counts to avoid repeated database lookups in a single request.
     */
    protected ?array $resolvedAttendanceData = null;

    protected function resolveAttendanceData(): array
    {
        if ($this->resolvedAttendanceData !== null) {
            return $this->resolvedAttendanceData;
        }

        $full = 0;
        $half = 0;
        $absent = 0;
        $leave = 0;
        $dayOff = 0;
        $breakdown = [];

        // 1. If breakdown is already stored in DB, calculate counts from it
        $rawBreakdown = $this->attributes['attendance_breakdown'] ?? null;
        if (!empty($rawBreakdown)) {
            $parsed = is_string($rawBreakdown) ? json_decode($rawBreakdown, true) : (array) $rawBreakdown;
            if (is_array($parsed) && count($parsed) > 0) {
                foreach ($parsed as $row) {
                    $st = $row['status'] ?? '';
                    if ($st === EmployeeAttendance::STATUS_FULL_DAY) $full++;
                    elseif ($st === EmployeeAttendance::STATUS_HALF_DAY) $half++;
                    elseif ($st === EmployeeAttendance::STATUS_ABSENT) $absent++;
                    elseif ($st === EmployeeAttendance::STATUS_LEAVE) $leave++;
                    elseif ($st === EmployeeAttendance::STATUS_DAY_OFF) $dayOff++;
                }
                $this->resolvedAttendanceData = [
                    'full' => $full,
                    'half' => $half,
                    'absent' => $absent,
                    'leave' => $leave,
                    'day_off' => $dayOff,
                    'breakdown' => $parsed,
                ];
                return $this->resolvedAttendanceData;
            }
        }

        // 2. Otherwise, look up attendance records directly for the period
        $period = $this->period;
        if ($this->employee_id && $period) {
            $year = $period->year;
            $month = $period->month;
            $periodDate = Carbon::createFromDate($year, $month, 1, 'Asia/Colombo');
            $start = $periodDate->copy()->startOfMonth()->toDateString();
            $end = $periodDate->copy()->endOfMonth()->toDateString();

            $attendances = EmployeeAttendance::where('employee_id', $this->employee_id)
                ->whereBetween('attendance_date', [$start, $end])
                ->orderBy('attendance_date', 'asc')
                ->get();

            foreach ($attendances as $att) {
                switch ($att->status) {
                    case EmployeeAttendance::STATUS_FULL_DAY:
                        $full++;
                        break;
                    case EmployeeAttendance::STATUS_HALF_DAY:
                        $half++;
                        break;
                    case EmployeeAttendance::STATUS_ABSENT:
                        $absent++;
                        break;
                    case EmployeeAttendance::STATUS_LEAVE:
                        $leave++;
                        break;
                    case EmployeeAttendance::STATUS_DAY_OFF:
                        $dayOff++;
                        break;
                }

                $breakdown[] = [
                    'date' => $att->attendance_date ? (is_string($att->attendance_date) ? $att->attendance_date : $att->attendance_date->toDateString()) : '',
                    'status' => $att->status,
                    'fraction' => (float) $att->payable_day_fraction,
                    'wage' => (float) $att->applied_daily_wage,
                    'earnings' => (float) $att->daily_earnings,
                    'notes' => $att->notes,
                ];
            }
        }

        $this->resolvedAttendanceData = [
            'full' => $full,
            'half' => $half,
            'absent' => $absent,
            'leave' => $leave,
            'day_off' => $dayOff,
            'breakdown' => $breakdown,
        ];

        return $this->resolvedAttendanceData;
    }

    public function getFullDaysCountAttribute($value): int
    {
        if ((empty($value) || (int) $value === 0) && (float) ($this->attributes['payable_days'] ?? 0) > 0) {
            $data = $this->resolveAttendanceData();
            if ($data['full'] > 0) {
                return $data['full'];
            }
        }
        return (int) $value;
    }

    public function getHalfDaysCountAttribute($value): int
    {
        if ((empty($value) || (int) $value === 0) && (float) ($this->attributes['payable_days'] ?? 0) > 0) {
            $data = $this->resolveAttendanceData();
            if ($data['half'] > 0) {
                return $data['half'];
            }
        }
        return (int) $value;
    }

    public function getAbsentDaysCountAttribute($value): int
    {
        if ((empty($value) || (int) $value === 0) && (float) ($this->attributes['payable_days'] ?? 0) > 0) {
            $data = $this->resolveAttendanceData();
            if ($data['absent'] > 0) {
                return $data['absent'];
            }
        }
        return (int) $value;
    }

    public function getLeaveDaysCountAttribute($value): int
    {
        if ((empty($value) || (int) $value === 0) && (float) ($this->attributes['payable_days'] ?? 0) > 0) {
            $data = $this->resolveAttendanceData();
            if ($data['leave'] > 0) {
                return $data['leave'];
            }
        }
        return (int) $value;
    }

    public function getDayOffDaysCountAttribute($value): int
    {
        if ((empty($value) || (int) $value === 0) && (float) ($this->attributes['payable_days'] ?? 0) > 0) {
            $data = $this->resolveAttendanceData();
            if ($data['day_off'] > 0) {
                return $data['day_off'];
            }
        }
        return (int) $value;
    }

    public function getAttendanceBreakdownAttribute($value): array
    {
        if (!empty($value)) {
            return is_string($value) ? json_decode($value, true) : (array) $value;
        }

        $data = $this->resolveAttendanceData();
        return $data['breakdown'];
    }

    /**
     * Recalculate gross, net, paid, and balance due
     */
    public function recalculate(): void
    {
        $base = (float) $this->base_earnings;
        $overtime = (float) $this->overtime_amount;
        $bonus = (float) $this->bonus_amount;
        $deductions = (float) $this->deductions;
        $epfEmployee = (float) $this->epf_employee_amount;
        $advances = (float) $this->advances_applied;

        $gross = round($base + $overtime + $bonus, 2);
        $totalDeductions = round($deductions + $epfEmployee + $advances, 2);
        $net = round(max(0, $gross - $totalDeductions), 2);

        $paid = (float) $this->payments()->sum('amount');
        $balance = round(max(0, $net - $paid), 2);

        $status = $this->status;
        if ($this->period && $this->period->status !== PayrollPeriod::STATUS_DRAFT) {
            if ($paid >= $net && $net > 0) {
                $status = self::STATUS_PAID;
            } elseif ($paid > 0) {
                $status = self::STATUS_PARTIALLY_PAID;
            } else {
                $status = self::STATUS_APPROVED;
            }
        }

        $this->update([
            'gross_salary' => $gross,
            'net_salary' => $net,
            'paid_amount' => $paid,
            'balance_due' => $balance,
            'status' => $status,
        ]);

        if ($this->period) {
            $this->period->recalculateTotals();
        }
    }
}
