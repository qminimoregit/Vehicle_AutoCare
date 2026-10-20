<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_receipt_id',
        'invoice_id',
        'allocated_amount',
        'notes',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
    ];

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(PaymentReceipt::class, 'payment_receipt_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
