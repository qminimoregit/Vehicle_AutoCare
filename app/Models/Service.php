<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'status',
    ];

    /**
     * Scope for active services.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for searching services by code, name, or description.
     */
    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('code', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function vehiclePrices()
    {
        return $this->hasMany(ServiceVehiclePrice::class);
    }

    /**
     * Alias for vehiclePrices relationship for backward compatibility and clean property access.
     */
    public function prices()
    {
        return $this->vehiclePrices();
    }

    /**
     * Safely resolve configured price for a specific vehicle type without throwing null exceptions.
     */
    public function getPriceForVehicleType(?int $vehicleTypeId): ?float
    {
        if (! $vehicleTypeId) {
            return null;
        }

        $pricingCollection = $this->relationLoaded('vehiclePrices')
            ? $this->vehiclePrices
            : ($this->relationLoaded('prices') ? $this->prices : null);

        if ($pricingCollection) {
            $pricing = $pricingCollection->firstWhere('vehicle_type_id', $vehicleTypeId);
        } else {
            $pricing = $this->vehiclePrices()->where('vehicle_type_id', $vehicleTypeId)->first();
        }

        return ($pricing && $pricing->service_price !== null) ? (float) $pricing->service_price : null;
    }
}
