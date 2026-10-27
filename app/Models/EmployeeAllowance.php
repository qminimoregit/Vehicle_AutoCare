<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAllowance extends Model
{
    use HasFactory;

    public const TYPE_TRANSPORT = 'transport';

    public const TYPE_MEAL = 'meal';

    public const TYPE_SPECIAL = 'special';

    public const TYPE_OTHER = 'other';

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_PAUSED = 'paused';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'allowance_number',
        'employee_id',
        'branch_id',
        'allowance_type',
        'title',
        'amount',
        'is_recurring',
        'effective_from',
        'effective_to',
        'period_year',
        'period_month',
        'is_epf_eligible',
        'is_etf_eligible',
        'is_taxable',
        'status',
        'approved_by',
        'approved_at',
        'cancelled_by',
        'cancelled_at',
        'cancellation_reason',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_recurring' => 'boolean',
            'effective_from' => 'date',
            'effective_to' => 'date',
            'period_year' => 'integer',
            'period_month' => 'integer',
            'is_epf_eligible' => 'boolean',
            'is_etf_eligible' => 'boolean',
            'is_taxable' => 'boolean',
            'approved_at' => 'datetime',
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

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_APPROVED], true);
    }

    public function canBeCancelled(): bool
    {
        return $this->status !== self::STATUS_CANCELLED;
    }

    public static function generateNextAllowanceNumber(): string
    {
        $latest = static::orderByDesc('id')->first();
        if (! $latest) {
            return 'ALW-0001';
        }

        if (preg_match('/ALW-(\d+)/i', $latest->allowance_number, $matches)) {
            $next = ((int) $matches[1]) + 1;

            return sprintf('ALW-%04d', $next);
        }

        return sprintf('ALW-%04d', $latest->id + 1);
    }

    /**
     * Check if this allowance applies to a given year and month.
     */
    public function isApplicableForPeriod(int $year, int $month): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        $periodDate = Carbon::createFromDate($year, $month, 1, 'Asia/Colombo');
        $monthStart = $periodDate->copy()->startOfMonth()->toDateString();
        $monthEnd = $periodDate->copy()->endOfMonth()->toDateString();

        if ($this->is_recurring) {
            // Recurring: starts on or before month end, and has not ended before month start
            $from = $this->effective_from ? $this->effective_from->toDateString() : null;
            $to = $this->effective_to ? $this->effective_to->toDateString() : null;

            if ($from && $from > $monthEnd) {
                return false;
            }
            if ($to && $to < $monthStart) {
                return false;
            }

            return true;
        }

        // Non-recurring (one-off): matches specific year & month, or effective date in month
        if ($this->period_year && $this->period_month) {
            return (int) $this->period_year === $year && (int) $this->period_month === $month;
        }

        if ($this->effective_from) {
            return $this->effective_from->toDateString() >= $monthStart && $this->effective_from->toDateString() <= $monthEnd;
        }

        return false;
    }

    public function getFormattedTypeAttribute(): string
    {
        return match ($this->allowance_type) {
            self::TYPE_TRANSPORT => 'Transport Allowance',
            self::TYPE_MEAL => 'Meal Allowance',
            self::TYPE_SPECIAL => 'Special Allowance',
            default => 'Other Allowance',
        };
    }
}
