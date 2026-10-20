<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'uom_id',
        'preferred_supplier_id',
        'min_stock',
        'max_stock',
        'selling_price',
        'image_path',
        'is_active',
    ];

    protected $appends = [
        'product_name',
        'image_url',
        'available_stock',
    ];

    protected function casts(): array
    {
        return [
            'min_stock' => 'decimal:2',
            'max_stock' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(UnitOfMeasurement::class, 'uom_id');
    }

    public function preferredSupplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'preferred_supplier_id');
    }

    public function grnItems(): HasMany
    {
        return $this->hasMany(GoodsReceivedNoteItem::class);
    }

    public function stockBatches(): HasMany
    {
        return $this->hasMany(StockBatch::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function activeStockBatches(): HasMany
    {
        return $this->hasMany(StockBatch::class)->where('remaining_quantity', '>', 0);
    }

    public function getProductNameAttribute(): string
    {
        return $this->name ?? '';
    }

    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image_path)) {
            return null;
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        return Storage::disk('public')->url($this->image_path);
    }

    public function hasImage(): bool
    {
        return ! empty($this->image_path) && Storage::disk('public')->exists($this->image_path);
    }

    public function getAvailableStockAttribute(): float
    {
        return (float) $this->activeStockBatches()->sum('remaining_quantity');
    }

    public function getInventoryValueAttribute(): float
    {
        return (float) $this->activeStockBatches()
            ->selectRaw('SUM(remaining_quantity * inventory_unit_cost) as total_val')
            ->value('total_val') ?? 0.0;
    }

    public function isLowStock(): bool
    {
        return $this->min_stock > 0 && $this->available_stock <= (float) $this->min_stock;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }
}
