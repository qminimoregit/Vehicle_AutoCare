<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Schema;

class EmployeeAdvance extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_DISBURSED = 'disbursed';

    public const STATUS_PARTIALLY_RECOVERED = 'partially_recovered';

    public const STATUS_DEDUCTED = 'deducted';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'advance_number',
        'employee_id',
        'branch_id',
        'advance_date',
        'amount',
        'monthly_installment',
        'payment_method',
        'notes',
        'status',
        'payroll_item_id',
        'created_by',
        'approved_by',
        'approved_at',
        'disbursed_at',
        'disbursed_by',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'cancelled_by',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'advance_date' => 'date',
            'amount' => 'decimal:2',
            'monthly_installment' => 'decimal:2',
            'approved_at' => 'datetime',
            'disbursed_at' => 'date',
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

    public function payrollItem(): BelongsTo
    {
        return $this->belongsTo(PayrollItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function disburser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disbursed_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function recoveries(): HasMany
    {
        return $this->hasMany(EmployeeAdvanceRecovery::class, 'employee_advance_id')->orderBy('recovery_date', 'asc');
    }

    public function expense(): HasOne
    {
        return $this->hasOne(Expense::class, 'employee_advance_id');
    }

    public function getRecoveredAmountAttribute(): float
    {
        if (Schema::hasTable('employee_advance_recoveries')) {
            $sum = (float) $this->recoveries()
                ->where('status', EmployeeAdvanceRecovery::STATUS_RECOVERED)
                ->sum('amount');

            if ($sum > 0) {
                return round($sum, 2);
            }
        }

        // Backward compatibility for legacy deducted advances without recoveries table rows
        if ($this->status === self::STATUS_DEDUCTED) {
            return (float) $this->amount;
        }

        return 0.0;
    }

    public function getRemainingBalanceAttribute(): float
    {
        if (in_array($this->status, [self::STATUS_REJECTED, self::STATUS_CANCELLED, self::STATUS_DEDUCTED], true)) {
            return 0.0;
        }

        $balance = (float) $this->amount - $this->recovered_amount;

        return max(0.0, round($balance, 2));
    }

    public function isDisbursed(): bool
    {
        return in_array($this->status, [self::STATUS_DISBURSED, self::STATUS_PARTIALLY_RECOVERED, self::STATUS_DEDUCTED], true)
            || ! empty($this->disbursed_at);
    }

    public function canBeDisbursed(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED], true)
            && $this->recovered_amount <= 0.001;
    }

    public static function generateNextAdvanceNumber(): string
    {
        $latest = static::orderByDesc('id')->first();
        if (! $latest) {
            return 'ADV-0001';
        }

        if (preg_match('/ADV-(\d+)/i', $latest->advance_number, $matches)) {
            $next = ((int) $matches[1]) + 1;

            return sprintf('ADV-%04d', $next);
        }

        return sprintf('ADV-%04d', $latest->id + 1);
    }
}
