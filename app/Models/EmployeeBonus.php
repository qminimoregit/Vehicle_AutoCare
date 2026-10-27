<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeBonus extends Model
{
    use HasFactory;

    public const TYPE_PERFORMANCE = 'performance';

    public const TYPE_ATTENDANCE = 'attendance';

    public const TYPE_SPECIAL = 'special';

    public const TYPE_FESTIVAL = 'festival';

    public const TYPE_INCENTIVE = 'incentive';

    public const TYPE_OTHER = 'other';

    public const CALC_FIXED = 'fixed';

    public const CALC_PERCENTAGE = 'percentage';

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_APPLIED = 'applied';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'bonus_number',
        'employee_id',
        'branch_id',
        'bonus_type',
        'calculation_type',
        'percentage_rate',
        'amount',
        'period_year',
        'period_month',
        'effective_date',
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
        'is_epf_eligible',
        'is_taxable',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'percentage_rate' => 'decimal:2',
            'amount' => 'decimal:2',
            'period_year' => 'integer',
            'period_month' => 'integer',
            'is_epf_eligible' => 'boolean',
            'is_taxable' => 'boolean',
            'effective_date' => 'date',
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

    public static function generateNextBonusNumber(): string
    {
        $latest = static::orderByDesc('id')->first();
        if (! $latest) {
            return 'BON-0001';
        }

        if (preg_match('/BON-(\d+)/i', $latest->bonus_number, $matches)) {
            $next = ((int) $matches[1]) + 1;

            return sprintf('BON-%04d', $next);
        }

        return sprintf('BON-%04d', $latest->id + 1);
    }

    /**
     * Calculate bonus amount for an employee based on calculation type.
     */
    public static function calculateAmount(Employee $employee, string $calcType, float $fixedAmount, ?float $percentage = null): float
    {
        if ($calcType === self::CALC_PERCENTAGE && $percentage !== null && $percentage > 0) {
            $base = $employee->isMonthlySalary()
                ? (float) ($employee->monthly_basic_salary ?? 0.00)
                : ((float) ($employee->daily_wage ?? 0.00) * 26); // Standard 26 work days benchmark

            return round(($base * $percentage) / 100, 2);
        }

        return round($fixedAmount, 2);
    }

    public function getFormattedPeriodAttribute(): string
    {
        return Carbon::createFromDate($this->period_year, $this->period_month, 1)->format('F Y');
    }

    public function getFormattedTypeAttribute(): string
    {
        return match ($this->bonus_type) {
            self::TYPE_PERFORMANCE => 'Performance Bonus',
            self::TYPE_ATTENDANCE => 'Attendance Bonus',
            self::TYPE_SPECIAL => 'Special Bonus',
            self::TYPE_FESTIVAL => 'Festival Bonus',
            self::TYPE_INCENTIVE => 'Incentive',
            default => 'Other Bonus',
        };
    }
}
