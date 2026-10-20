<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_number',
        'grn_id',
        'grn_item_id',
        'product_id',
        'supplier_id',
        'purchase_date',
        'supplier_lot_number',
        'received_quantity',
        'free_quantity',
        'remaining_quantity',
        'original_purchase_unit_price',
        'inventory_unit_cost',
        'selling_price_at_receipt',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'received_quantity' => 'decimal:2',
            'free_quantity' => 'decimal:2',
            'remaining_quantity' => 'decimal:2',
            'original_purchase_unit_price' => 'decimal:2',
            'inventory_unit_cost' => 'decimal:4',
            'selling_price_at_receipt' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function grn(): BelongsTo
    {
        return $this->belongsTo(GoodsReceivedNote::class, 'grn_id');
    }

    public function grnItem(): BelongsTo
    {
        return $this->belongsTo(GoodsReceivedNoteItem::class, 'grn_item_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getBatchValueAttribute(): float
    {
        return round((float) $this->remaining_quantity * (float) $this->inventory_unit_cost, 2);
    }

    public function isAvailable(): bool
    {
        return (float) $this->remaining_quantity > 0 && $this->status === 'active';
    }
}
