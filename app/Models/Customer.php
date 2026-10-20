<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'phone',
        'email',
        'customer_type',
        'company_name',
        'registration_number',
        'tax_number',
        'address',
        'notes',
        'sms_consent',
        'status',
    ];

    protected $casts = [
        'sms_consent' => 'boolean',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function jobCards()
    {
        return $this->hasMany(JobCard::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function directSales()
    {
        return $this->hasMany(DirectSale::class);
    }
}
