<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpensePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_id',
        'branch_id',
        'payment_number',
        'payment_date',
        'amount',
        'payment_method',
        'reference_number',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Generate sequential unique payment number: EPAY-YYYYMM-XXXX.
     */
    public static function generatePaymentNumber(): string
    {
        $prefix = 'EPAY-'.date('Ym').'-';
        $latest = static::where('payment_number', 'LIKE', $prefix.'%')->orderByDesc('id')->first();
        if ($latest && preg_match('/EPAY-\d{6}-(\d+)/', $latest->payment_number, $matches)) {
            $seq = (int) $matches[1] + 1;
        } else {
            $seq = 1;
        }

        return sprintf('%s%04d', $prefix, $seq);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class, 'expense_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
