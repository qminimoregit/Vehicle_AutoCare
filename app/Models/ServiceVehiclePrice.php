<?php

namespace App\Models;

use Database\Factories\ServiceVehiclePriceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceVehiclePrice extends Model
{
    /** @use HasFactory<ServiceVehiclePriceFactory> */
    use HasFactory;

    protected $fillable = [
        'service_id',
        'vehicle_type_id',
        'service_price',
        'service_cost',
    ];

    protected function casts(): array
    {
        return [
            'service_price' => 'decimal:2',
            'service_cost' => 'decimal:2',
        ];
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function isConfigured(): bool
    {
        return $this->service_price !== null;
    }
}
