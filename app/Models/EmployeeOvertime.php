<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeOvertime extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_APPLIED = 'applied';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'overtime_number',
        'employee_id',
        'branch_id',
        'overtime_date',
        'hours',
        'hourly_rate',
        'rate_multiplier',
        'amount',
        'status',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'cancelled_by',
        'cancelled_at',
        'cancellation_reason',
        'payroll_item_id',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'overtime_date' => 'date',
            'hours' => 'decimal:2',
            'hourly_rate' => 'decimal:2',
            'rate_multiplier' => 'decimal:2',
            'amount' => 'decimal:2',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function payrollItem(): BelongsTo
    {
        return $this->belongsTo(PayrollItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isApplied(): bool
    {
        return $this->status === self::STATUS_APPLIED;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED], true) && $this->payroll_item_id === null;
    }

    public static function generateNextOvertimeNumber(): string
    {
        $latest = static::orderByDesc('id')->first();
        if (! $latest) {
            return 'OT-0001';
        }

        if (preg_match('/OT-(\d+)/i', $latest->overtime_number, $matches)) {
            $next = ((int) $matches[1]) + 1;

            return sprintf('OT-%04d', $next);
        }

        return sprintf('OT-%04d', $latest->id + 1);
    }

    /**
     * Compute standard base hourly rate for an employee.
     * Daily Wage employee: Daily Wage / 8 hours.
     * Monthly Salary employee: Monthly Basic / 200 hours (or 25 days * 8h).
     */
    public static function calculateHourlyRate(Employee $employee, ?float $customRate = null): float
    {
        if ($customRate !== null && $customRate > 0) {
            return round($customRate, 2);
        }

        if ($employee->isMonthlySalary()) {
            $monthlyBasic = (float) ($employee->monthly_basic_salary ?? 0.00);
            if ($monthlyBasic > 0) {
                return round($monthlyBasic / 200, 2); // Standard statutory Sri Lankan 200 hours/month divisor
            }
        }

        $dailyWage = (float) ($employee->daily_wage ?? 0.00);
        if ($dailyWage > 0) {
            return round($dailyWage / 8, 2); // 8 hours normal work day
        }

        return 0.00;
    }

    /**
     * Calculate overtime payout: Hours * Hourly Rate * Rate Multiplier.
     */
    public static function calculateAmount(float $hours, float $hourlyRate, float $multiplier = 1.50): float
    {
        return round($hours * $hourlyRate * $multiplier, 2);
    }
}
