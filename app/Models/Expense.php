<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expense extends Model
{
    use HasFactory;

    public const TYPE_STAFF = 'staff';

    public const TYPE_SHOP = 'shop';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_VOIDED = 'voided';

    public const PAYMENT_STATUS_PAID = 'paid';

    public const PAYMENT_STATUS_PARTIALLY_PAID = 'partially_paid';

    public const PAYMENT_STATUS_UNPAID = 'unpaid';

    protected $fillable = [
        'expense_number',
        'branch_id',
        'category_id',
        'expense_type',
        'employee_id',
        'employee_advance_id',
        'expense_date',
        'related_month',
        'description',
        'amount',
        'paid_amount',
        'balance_amount',
        'payment_status',
        'payment_method',
        'payment_date',
        'payment_reference',
        'receipt_path',
        'status',
        'void_reason',
        'voided_by',
        'voided_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance_amount' => 'decimal:2',
            'expense_date' => 'date',
            'payment_date' => 'date',
            'voided_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function advance(): BelongsTo
    {
        return $this->belongsTo(EmployeeAdvance::class, 'employee_advance_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function voider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isVoided(): bool
    {
        return $this->status === self::STATUS_VOIDED;
    }

    public function isStaffExpense(): bool
    {
        return $this->expense_type === self::TYPE_STAFF;
    }

    public function isStaff(): bool
    {
        return $this->expense_type === self::TYPE_STAFF;
    }

    public function isShopExpense(): bool
    {
        return $this->expense_type === self::TYPE_SHOP;
    }

    public function isShop(): bool
    {
        return $this->expense_type === self::TYPE_SHOP;
    }

    public function isSalaryAdvance(): bool
    {
        return $this->employee_advance_id !== null || ($this->category && strtolower($this->category->name) === 'salary advance');
    }

    public static function generateNextExpenseNumber(): string
    {
        $latest = static::orderByDesc('id')->first();
        if (! $latest) {
            return 'EXP-0001';
        }

        if (preg_match('/EXP-(\d+)/i', $latest->expense_number, $matches)) {
            $next = ((int) $matches[1]) + 1;

            return sprintf('EXP-%04d', $next);
        }

        return sprintf('EXP-%04d', $latest->id + 1);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ExpensePayment::class, 'expense_id')->orderBy('payment_date', 'asc')->orderBy('id', 'asc');
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_PAID || (float) $this->balance_amount <= 0.001;
    }

    public function isPartiallyPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_PARTIALLY_PAID;
    }

    public function isUnpaid(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_UNPAID;
    }

    public function hasOutstandingBalance(): bool
    {
        return (float) $this->balance_amount > 0.001 && $this->isActive();
    }

    /**
     * Recalculate and persist paid amount, balance amount, and payment status based on recorded payments.
     */
    public function recalculatePaymentStatus(): void
    {
        $totalPaid = (float) $this->payments()->sum('amount');
        $totalAmount = (float) $this->amount;
        $balance = max(0.00, round($totalAmount - $totalPaid, 2));

        if ($balance <= 0.001) {
            $status = self::PAYMENT_STATUS_PAID;
        } elseif ($totalPaid > 0) {
            $status = self::PAYMENT_STATUS_PARTIALLY_PAID;
        } else {
            $status = self::PAYMENT_STATUS_UNPAID;
        }

        $this->updateQuietly([
            'paid_amount' => $totalPaid,
            'balance_amount' => $balance,
            'payment_status' => $status,
        ]);
    }
}
