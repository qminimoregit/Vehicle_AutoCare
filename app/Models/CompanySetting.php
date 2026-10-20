<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'registration_number',
        'address',
        'phone',
        'email',
        'tax_number',
        'logo_path',
    ];

    /**
     * Get or create the singleton instance of company settings.
     */
    public static function getSettings(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'company_name' => 'AutoCare Service Center',
                'registration_number' => 'PV-2026-98765',
                'address' => 'No. 124, High Level Road, Colombo, Sri Lanka',
                'phone' => '+94 11 234 5678',
                'email' => 'info@autocare.local',
                'tax_number' => 'VAT-123456789-000',
                'logo_path' => null,
            ]
        );
    }
}
