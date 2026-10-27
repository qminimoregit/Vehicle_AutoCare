<?php

namespace App\Models;

use App\Services\GrnNumberService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoodsReceivedNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'grn_number',
        'branch_id',
        'supplier_id',
        'supplier_invoice_number',
        'received_date',
        'status',
        'total_amount',
        'notes',
        'invoice_attachment',
        'posted_at',
        'posted_by',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'received_date' => 'date',
            'posted_at' => 'datetime',
            'total_amount' => 'decimal:2',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(GoodsReceivedNoteItem::class, 'grn_id');
    }

    public function stockBatches(): HasMany
    {
        return $this->hasMany(StockBatch::class, 'grn_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class, 'grn_id');
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function getPaidAmountAttribute(): float
    {
        return (float) $this->payments()->where('status', '!=', 'reversed')->sum('amount');
    }

    public function getOutstandingAmountAttribute(): float
    {
        return max(0.0, round((float) $this->total_amount - $this->paid_amount, 2));
    }

    public function getPaymentStatusAttribute(): string
    {
        $paid = $this->paid_amount;
        $total = (float) $this->total_amount;

        if ($total > 0 && $paid >= $total) {
            return 'Paid';
        }

        if ($paid > 0) {
            return 'Partially Paid';
        }

        return 'Unpaid';
    }

    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('grn_number', 'like', "%{$term}%")
                ->orWhere('supplier_invoice_number', 'like', "%{$term}%")
                ->orWhereHas('supplier', function ($sq) use ($term) {
                    $sq->where('name', 'like', "%{$term}%");
                });
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Generate the next concurrency-safe, sequential GRN number.
     */
    public static function generateGrnNumber(?string $date = null): string
    {
        return GrnNumberService::generateNextGrnNumber($date);
    }
}
