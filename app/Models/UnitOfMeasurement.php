<?php

namespace App\Models;

use App\Services\QuantityFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitOfMeasurement extends Model
{
    use HasFactory;

    protected $table = 'units_of_measurement';

    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'uom_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function allowsFractional(): bool
    {
        return QuantityFormatter::allowsFractional($this);
    }
}
