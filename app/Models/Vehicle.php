<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'branch_id',
        'vehicle_type_id',
        'vehicle_number',
        'vehicle_number_normalized',
        'make_model',
        'brand',
        'model',
        'year_of_manufacture',
        'color',
        'chassis_number',
        'engine_number',
        'driver_name',
        'driver_mobile',
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

    protected static function booted(): void
    {
        static::saving(function ($vehicle) {
            if (! empty($vehicle->vehicle_number) && empty($vehicle->vehicle_number_normalized)) {
                $vehicle->vehicle_number_normalized = static::normalizeNumber($vehicle->vehicle_number);
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function company()
    {
        return $this->belongsTo(Customer::class, 'customer_id')->where('customer_type', 'company');
    }

    public function lastJobCard()
    {
        return $this->hasOne(JobCard::class)->latestOfMany();
    }

    public function lastCompletedJob()
    {
        return $this->hasOne(JobCard::class)->ofMany([
            'date' => 'max',
            'id' => 'max',
        ], function ($query) {
            $query->whereIn('status', ['completed', 'delivered']);
        });
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

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function issues()
    {
        return $this->hasMany(JobIssue::class, 'vehicle_id')->latest();
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'vehicle_id');
    }
}
