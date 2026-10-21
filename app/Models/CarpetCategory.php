<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarpetCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function vehicleTypes()
    {
        return $this->belongsToMany(VehicleType::class, 'carpet_category_vehicle_type');
    }

    public function jobCardCarpets()
    {
        return $this->hasMany(JobCardCarpet::class);
    }
}
