<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'job_card_id',
        'customer_id',
        'payment_receipt_id',
        'payment_number',
        'payment_date',
        'amount',
        'payment_method',
        'reference_number',
        'notes',
        'is_advance',
        'status',
        'reversed_at',
        'reversal_reason',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'reversed_at' => 'datetime',
        'amount' => 'decimal:2',
        'is_advance' => 'boolean',
    ];

    public static function generatePaymentNumber(): string
    {
        $prefix = 'PAY-'.date('Ym').'-';
        $payments = self::where('payment_number', 'LIKE', $prefix.'%')->pluck('payment_number');
        $maxSeq = 0;
        $regex = '/^PAY-'.date('Ym').'-(\d+)$/';

        foreach ($payments as $num) {
            if (preg_match($regex, $num, $matches)) {
                $val = (int) $matches[1];
                if ($val > $maxSeq) {
                    $maxSeq = $val;
                }
            }
        }

        $nextSeq = $maxSeq + 1;
        $candidate = $prefix.str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
        while (self::where('payment_number', $candidate)->exists()) {
            $nextSeq++;
            $candidate = $prefix.str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function jobCard()
    {
        return $this->belongsTo(JobCard::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function receipt()
    {
        return $this->belongsTo(PaymentReceipt::class, 'payment_receipt_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isReversed(): bool
    {
        return $this->status === 'reversed';
    }

    public function getBranchAttribute()
    {
        return $this->invoice?->branch ?? $this->jobCard?->branch;
    }
}
