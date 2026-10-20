<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_item_id',
        'payment_number',
        'payment_date',
        'amount',
        'payment_method',
        'reference_number',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function payrollItem(): BelongsTo
    {
        return $this->belongsTo(PayrollItem::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public static function generateNextPaymentNumber(): string
    {
        $latest = static::orderByDesc('id')->first();
        if (! $latest) {
            return 'PAY-0001';
        }

        if (preg_match('/PAY-(\d+)/i', $latest->payment_number, $matches)) {
            $next = ((int) $matches[1]) + 1;

            return sprintf('PAY-%04d', $next);
        }

        return sprintf('PAY-%04d', $latest->id + 1);
    }
}
