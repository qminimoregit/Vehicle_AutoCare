<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number',
        'branch_id',
        'customer_id',
        'receipt_date',
        'total_amount',
        'payment_method',
        'reference_number',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public static function generateReceiptNumber(): string
    {
        $prefix = 'REC-'.date('Ym').'-';
        $lastReceipt = self::where('receipt_number', 'LIKE', $prefix.'%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastReceipt) {
            $lastSeq = (int) substr($lastReceipt->receipt_number, strlen($prefix));
            $newSeq = str_pad((string) ($lastSeq + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newSeq = '0001';
        }

        return $prefix.$newSeq;
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
