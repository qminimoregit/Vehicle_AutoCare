<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'signature_path',
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
     * Accessor for signature attribute alias to signature_path.
     */
    public function getSignatureAttribute(): ?string
    {
        return $this->signature_path;
    }

    /**
     * Get accessible public URL for the authorized signature if uploaded.
     */
    public function getSignatureUrl(): ?string
    {
        if (! empty($this->signature_path)) {
            if (file_exists(public_path('storage/'.$this->signature_path))) {
                return asset('storage/'.$this->signature_path);
            }
            if (Storage::disk('public')->exists($this->signature_path)) {
                return asset('storage/'.$this->signature_path);
            }
        }

        return null;
    }

    /**
     * Resolve base64 data URI for authorized signature.
     */
    public function getSignatureBase64(): ?string
    {
        if (empty($this->signature_path)) {
            return null;
        }

        if (str_starts_with($this->signature_path, 'data:image/')) {
            return $this->signature_path;
        }

        $path = storage_path('app/public/'.ltrim(str_replace(['storage/', 'public/'], '', $this->signature_path), '/'));
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

    /**
     * Get accessible public URL for the company logo with automatic fallback.
     */
    public function getLogoUrl(): string
    {
        if (! empty($this->logo_path) && file_exists(public_path('storage/'.$this->logo_path))) {
            return asset('storage/'.$this->logo_path);
        }
        if (file_exists(public_path('images/branding/viran_auto_care_logo.png'))) {
            return asset('images/branding/viran_auto_care_logo.png');
        }

        return asset('images/branding/autocare-logo-transparent.png');
    }

    /**
     * Get accessible public URL for the 80mm thermal printing logo (trimmed copy with minimum whitespace).
     */
    public function getThermalLogoUrl(): string
    {
        if (file_exists(public_path('images/branding/viran_auto_care_logo_thermal.png'))) {
            return asset('images/branding/viran_auto_care_logo_thermal.png');
        }

        return $this->getLogoUrl();
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
