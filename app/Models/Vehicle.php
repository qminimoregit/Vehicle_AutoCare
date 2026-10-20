<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'vehicle_type_id',
        'vehicle_number',
        'vehicle_number_normalized',
        'make_model',
        'year_of_manufacture',
        'color',
        'chassis_number',
        'engine_number',
        'current_mileage',
        'next_service_mileage',
        'notes',
    ];

    protected $casts = [
        'current_mileage' => 'integer',
        'next_service_mileage' => 'integer',
    ];

    public static function normalizeNumber(string $number): string
    {
        return strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', $number));
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function jobCards()
    {
        return $this->hasMany(JobCard::class);
    }

    public function ongoingJobs()
    {
        return $this->hasMany(JobCard::class)->whereIn('status', ['pending', 'in_progress']);
    }

    public function completedJobs()
    {
        return $this->hasMany(JobCard::class)->whereIn('status', ['completed', 'delivered'])->orderBy('date', 'desc');
    }
}
