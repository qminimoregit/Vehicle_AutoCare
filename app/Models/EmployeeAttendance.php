<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAttendance extends Model
{
    use HasFactory;

    protected $table = 'employee_attendance';

    public const STATUS_FULL_DAY = 'full_day';
    public const STATUS_HALF_DAY = 'half_day';
    public const STATUS_ABSENT = 'absent';
    public const STATUS_LEAVE = 'leave';
    public const STATUS_DAY_OFF = 'day_off';

    protected $fillable = [
        'employee_id',
        'attendance_date',
        'status',
        'payable_day_fraction',
        'applied_daily_wage',
        'daily_earnings',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'payable_day_fraction' => 'decimal:2',
            'applied_daily_wage' => 'decimal:2',
            'daily_earnings' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public static function getStatusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_FULL_DAY => 'Full Day',
            self::STATUS_HALF_DAY => 'Half Day',
            self::STATUS_ABSENT => 'Absent',
            self::STATUS_LEAVE => 'Leave',
            self::STATUS_DAY_OFF => 'Day Off',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    public static function getStatusFraction(string $status, bool $isPaidLeave = false): float
    {
        return match ($status) {
            self::STATUS_FULL_DAY => 1.00,
            self::STATUS_HALF_DAY => 0.50,
            self::STATUS_LEAVE => $isPaidLeave ? 1.00 : 0.00,
            self::STATUS_ABSENT, self::STATUS_DAY_OFF => 0.00,
            default => 0.00,
        };
    }
}
