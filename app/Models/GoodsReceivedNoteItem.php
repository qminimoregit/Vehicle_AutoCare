<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GoodsReceivedNoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'grn_id',
        'product_id',
        'purchase_quantity',
        'free_quantity',
        'purchase_unit_price',
        'selling_price_snapshot',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'purchase_quantity' => 'decimal:2',
            'free_quantity' => 'decimal:2',
            'purchase_unit_price' => 'decimal:2',
            'selling_price_snapshot' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function grn(): BelongsTo
    {
        return $this->belongsTo(GoodsReceivedNote::class, 'grn_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function stockBatch(): HasOne
    {
        return $this->hasOne(StockBatch::class, 'grn_item_id');
    }

    public function getTotalPhysicalQuantityAttribute(): float
    {
        return (float) $this->purchase_quantity + (float) $this->free_quantity;
    }

    public function getEffectiveUnitCostAttribute(): float
    {
        $totalPhysical = $this->total_physical_quantity;
        if ($totalPhysical <= 0) {
            return (float) $this->purchase_unit_price;
        }

        return round((float) $this->line_total / $totalPhysical, 4);
    }
}
