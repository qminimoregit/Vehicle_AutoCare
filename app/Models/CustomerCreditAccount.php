<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerCreditAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'credit_limit',
        'payment_terms_days',
        'opening_balance',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'payment_terms_days' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Compute current outstanding balance for this credit account:
     * Opening balance + Total unpaid/partially paid balance of non-cancelled invoices.
     */
    public function getOutstandingBalanceAttribute(): float
    {
        $opening = (float) $this->opening_balance;
        $invoicesBalance = (float) $this->customer->invoices()
            ->where('status', '!=', 'cancelled')
            ->sum('balance_amount');

        return round($opening + $invoicesBalance, 2);
    }

    /**
     * Available credit limit remaining:
     * Credit Limit - Outstanding Balance.
     */
    public function getAvailableCreditAttribute(): float
    {
        $limit = (float) $this->credit_limit;
        $outstanding = $this->outstanding_balance;

        return round(max(0, $limit - $outstanding), 2);
    }

    /**
     * Check if a proposed credit amount exceeds credit limit.
     */
    public function wouldExceedLimit(float $additionalAmount): bool
    {
        if ($this->credit_limit <= 0) {
            return false; // Unlimited or unconstrained if 0, or check if limit is strict
        }

        return ($this->outstanding_balance + $additionalAmount) > (float) $this->credit_limit;
    }

    /**
     * Calculate credit aging buckets based on invoice due dates:
     * - Current / Not Yet Due
     * - 1-30 Days Overdue
     * - 31-60 Days Overdue
     * - 61-90 Days Overdue
     * - Over 90 Days Overdue
     */
    public function getAgingSummaryAttribute(): array
    {
        $today = Carbon::today();
        $invoices = $this->customer->invoices()
            ->where('status', '!=', 'cancelled')
            ->where('balance_amount', '>', 0)
            ->get();

        $aging = [
            'current' => 0.0,
            'days_1_30' => 0.0,
            'days_31_60' => 0.0,
            'days_61_90' => 0.0,
            'over_90' => 0.0,
            'total' => (float) $this->opening_balance,
        ];

        // Add opening balance to over_90 or current based on setup
        $aging['over_90'] += (float) $this->opening_balance;

        foreach ($invoices as $inv) {
            $balance = (float) $inv->balance_amount;
            $aging['total'] += $balance;

            $dueDate = $inv->due_date ? Carbon::parse($inv->due_date) : Carbon::parse($inv->invoice_date)->addDays($this->payment_terms_days ?: 30);

            if ($today->lte($dueDate)) {
                $aging['current'] += $balance;
            } else {
                $daysOverdue = $dueDate->diffInDays($today);

                if ($daysOverdue <= 30) {
                    $aging['days_1_30'] += $balance;
                } elseif ($daysOverdue <= 60) {
                    $aging['days_31_60'] += $balance;
                } elseif ($daysOverdue <= 90) {
                    $aging['days_61_90'] += $balance;
                } else {
                    $aging['over_90'] += $balance;
                }
            }
        }

        return [
            'current' => round($aging['current'], 2),
            'days_1_30' => round($aging['days_1_30'], 2),
            'days_31_60' => round($aging['days_31_60'], 2),
            'days_61_90' => round($aging['days_61_90'], 2),
            'over_90' => round($aging['over_90'], 2),
            'total' => round($aging['total'], 2),
        ];
    }
}
