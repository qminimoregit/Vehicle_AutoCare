<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'description',
        'enable_carpets',
        'status',
    ];

    protected $casts = [
        'enable_carpets' => 'boolean',
    ];

    /**
     * Scope for active vehicle types.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for searching vehicle types by name or description.
     */
    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function servicePrices()
    {
        return $this->hasMany(ServiceVehiclePrice::class);
    }

    public function carpetCategories()
    {
        return $this->belongsToMany(CarpetCategory::class, 'carpet_category_vehicle_type');
    }

    public function hasCarpets(): bool
    {
        return (bool) $this->enable_carpets;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/'.$this->image) : null;
    }
}
