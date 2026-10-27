<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class PayrollStatutorySetting extends Model
{
    use HasFactory;

    public const DEFAULT_EPF_EMPLOYEE_RATE = 8.00;

    public const DEFAULT_EPF_EMPLOYER_RATE = 12.00;

    public const DEFAULT_ETF_EMPLOYER_RATE = 3.00;

    public const DEFAULT_APIT_MONTHLY_RELIEF_PRE_2025 = 100000.00;

    public const DEFAULT_APIT_MONTHLY_RELIEF_2025 = 150000.00;

    public const DEFAULT_APIT_MONTHLY_RELIEF = 150000.00;

    public const DIVISOR_CALENDAR_DAYS = 'calendar_days';

    public const DIVISOR_FIXED_30 = 'fixed_30';

    public const DIVISOR_FIXED_26 = 'fixed_26';

    public const DIVISOR_FIXED_22 = 'fixed_22';

    public const DIVISOR_CUSTOM = 'custom';

    protected $fillable = [
        'effective_from',
        'effective_to',
        'epf_employee_rate',
        'epf_employer_rate',
        'etf_employer_rate',
        'no_pay_divisor_type',
        'no_pay_custom_divisor',
        'overtime_normal_multiplier',
        'overtime_holiday_multiplier',
        'overtime_monthly_divisor_hours',
        'apit_enabled',
        'apit_monthly_relief',
        'apit_tax_slabs',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'effective_from' => 'date',
            'effective_to' => 'date',
            'epf_employee_rate' => 'decimal:2',
            'epf_employer_rate' => 'decimal:2',
            'etf_employer_rate' => 'decimal:2',
            'overtime_normal_multiplier' => 'decimal:2',
            'overtime_holiday_multiplier' => 'decimal:2',
            'overtime_monthly_divisor_hours' => 'decimal:2',
            'apit_enabled' => 'boolean',
            'apit_monthly_relief' => 'decimal:2',
            'apit_tax_slabs' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Inland Revenue Department (IRD) APIT Table 01 monthly tax slabs prior to April 1, 2025.
     * (Relief LKR 100,000/mo; 6 progressive slabs of LKR 41,666.67 @ 6%, 12%, 18%, 24%, 30%, 36%)
     *
     * @return array<int, array{slab: int, limit: ?float, rate: int|float}>
     */
    public static function defaultPre2025TaxSlabs(): array
    {
        return [
            ['slab' => 1, 'limit' => 41666.67, 'rate' => 6],
            ['slab' => 2, 'limit' => 41666.67, 'rate' => 12],
            ['slab' => 3, 'limit' => 41666.67, 'rate' => 18],
            ['slab' => 4, 'limit' => 41666.67, 'rate' => 24],
            ['slab' => 5, 'limit' => 41666.67, 'rate' => 30],
            ['slab' => 6, 'limit' => null,     'rate' => 36],
        ];
    }

    /**
     * Inland Revenue Department (IRD) APIT Table 01 monthly tax slabs effective April 1, 2025 onwards.
     * (Relief LKR 150,000/mo; Slab 1: 83,333.33 @ 6%, Slab 2: 41,666.67 @ 18%, Slab 3: 41,666.67 @ 24%,
     *  Slab 4: 41,666.67 @ 30%, Slab 5: Balance @ 36%)
     *
     * @return array<int, array{slab: int, limit: ?float, rate: int|float}>
     */
    public static function default2025TaxSlabs(): array
    {
        return [
            ['slab' => 1, 'limit' => 83333.33, 'rate' => 6],
            ['slab' => 2, 'limit' => 41666.67, 'rate' => 18],
            ['slab' => 3, 'limit' => 41666.67, 'rate' => 24],
            ['slab' => 4, 'limit' => 41666.67, 'rate' => 30],
            ['slab' => 5, 'limit' => null,     'rate' => 36],
        ];
    }

    /**
     * Retrieve standard IRD tax slabs based on effective date.
     *
     * @return array<int, array{slab: int, limit: ?float, rate: int|float}>
     */
    public static function defaultTaxSlabs(?string $effectiveDate = null): array
    {
        if ($effectiveDate !== null && $effectiveDate < '2025-04-01') {
            return static::defaultPre2025TaxSlabs();
        }

        return static::default2025TaxSlabs();
    }

    /**
     * Retrieve the effective statutory setting for a specific payroll year and month,
     * falling back to standard statutory reference values if unmigrated or unconfigured.
     */
    public static function forPeriod(int $year, int $month): self
    {
        $periodDate = Carbon::createFromDate($year, $month, 1, 'Asia/Colombo')->toDateString();

        if (Schema::hasTable('payroll_statutory_settings')) {
            $setting = static::where('is_active', true)
                ->where('effective_from', '<=', $periodDate)
                ->where(function ($q) use ($periodDate) {
                    $q->whereNull('effective_to')
                        ->orWhere('effective_to', '>=', $periodDate);
                })
                ->orderBy('effective_from', 'desc')
                ->first();

            if ($setting) {
                return $setting;
            }
        }

        $isPostApril2025 = ($periodDate >= '2025-04-01');

        // In-memory fallback with verified Sri Lanka statutory rates
        $fallback = new static;
        $fallback->effective_from = Carbon::parse($isPostApril2025 ? '2025-04-01' : '2023-01-01');
        $fallback->effective_to = $isPostApril2025 ? null : Carbon::parse('2025-03-31');
        $fallback->epf_employee_rate = self::DEFAULT_EPF_EMPLOYEE_RATE;
        $fallback->epf_employer_rate = self::DEFAULT_EPF_EMPLOYER_RATE;
        $fallback->etf_employer_rate = self::DEFAULT_ETF_EMPLOYER_RATE;
        $fallback->no_pay_divisor_type = self::DIVISOR_CALENDAR_DAYS;
        $fallback->no_pay_custom_divisor = null;
        $fallback->overtime_normal_multiplier = 1.50;
        $fallback->overtime_holiday_multiplier = 2.00;
        $fallback->overtime_monthly_divisor_hours = 200.00;
        $fallback->apit_enabled = true;
        $fallback->apit_monthly_relief = $isPostApril2025 ? self::DEFAULT_APIT_MONTHLY_RELIEF_2025 : self::DEFAULT_APIT_MONTHLY_RELIEF_PRE_2025;
        $fallback->apit_tax_slabs = $isPostApril2025 ? static::default2025TaxSlabs() : static::defaultPre2025TaxSlabs();
        $fallback->is_active = true;
        $fallback->notes = $isPostApril2025
            ? 'Sri Lanka IRD APIT Table 01 (Effective April 1, 2025 onwards, YA 2025/26)'
            : 'Sri Lanka IRD APIT Table 01 (Pre-April 2025, YA 2023/24 - 2024/25)';

        return $fallback;
    }

    /**
     * Calculate divisor integer or count for no-pay deductions.
     */
    public function resolveNoPayDivisor(int $daysInMonth): int
    {
        return match ($this->no_pay_divisor_type) {
            self::DIVISOR_FIXED_30 => 30,
            self::DIVISOR_FIXED_26 => 26,
            self::DIVISOR_FIXED_22 => 22,
            self::DIVISOR_CUSTOM => max(1, (int) ($this->no_pay_custom_divisor ?? $daysInMonth)),
            default => max(1, $daysInMonth),
        };
    }
}
