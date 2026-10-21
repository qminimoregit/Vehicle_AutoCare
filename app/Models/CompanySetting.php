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

    /**
     * Branches belonging to this company.
     */
    public function branches()
    {
        return $this->hasMany(Branch::class, 'company_id');
    }

    /**
     * The designated main branch of this company.
     */
    public function mainBranch()
    {
        return $this->hasOne(Branch::class, 'company_id')->where('is_main_branch', true);
    }

    /**
     * Accessor for logo attribute alias to logo_path.
     */
    public function getLogoAttribute(): ?string
    {
        return $this->logo_path;
    }

    /**
     * Get accessible public URL for the company logo with automatic fallback.
     */
    public function getLogoUrl(): string
    {
        if (!empty($this->logo_path) && file_exists(public_path('storage/' . $this->logo_path))) {
            return asset('storage/' . $this->logo_path);
        }
        if (file_exists(public_path('images/branding/viran_auto_care_logo.png'))) {
            return asset('images/branding/viran_auto_care_logo.png');
        }
        return asset('images/branding/autocare-logo-transparent.png');
    }

    /**
     * Resolve base64 data URI for logo to ensure 100% reliable DomPDF rendering.
     */
    public function getLogoBase64(): ?string
    {
        if (empty($this->logo_path)) {
            return null;
        }

        if (str_starts_with($this->logo_path, 'data:image/')) {
            return $this->logo_path;
        }

        $path = storage_path('app/public/'.ltrim(str_replace(['storage/', 'public/'], '', $this->logo_path), '/'));
        if (file_exists($path) && is_file($path)) {
            $contents = @file_get_contents($path);
            if ($contents) {
                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $mime = match ($ext) {
                    'png' => 'image/png',
                    'jpg', 'jpeg' => 'image/jpeg',
                    'svg' => 'image/svg+xml',
                    'webp' => 'image/webp',
                    default => 'image/png',
                };

                return 'data:'.$mime.';base64,'.base64_encode($contents);
            }
        }

        return null;
    }
}
