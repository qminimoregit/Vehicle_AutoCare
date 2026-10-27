<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    public const SALARY_TYPE_DAILY = 'daily';

    public const SALARY_TYPE_MONTHLY = 'monthly';

    protected $fillable = [
        'employee_number',
        'branch_id',
        'name',
        'mobile',
        'address',
        'position',
        'salary_type',
        'daily_wage',
        'monthly_basic_salary',
        'payment_method',
        'bank_name',
        'bank_branch',
        'bank_account_name',
        'bank_account_number',
        'epf_number',
        'is_epf_eligible',
        'is_etf_eligible',
        'eligible_for_off_days',
        'joining_date',
        'is_active',
        'notes',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'daily_wage' => 'decimal:2',
            'monthly_basic_salary' => 'decimal:2',
            'joining_date' => 'date',
            'is_active' => 'boolean',
            'is_epf_eligible' => 'boolean',
            'is_etf_eligible' => 'boolean',
            'eligible_for_off_days' => 'boolean',
        ];
    }

    public function isDailyWage(): bool
    {
        return ($this->salary_type ?? self::SALARY_TYPE_DAILY) === self::SALARY_TYPE_DAILY;
    }

    public function isMonthlySalary(): bool
    {
        return ($this->salary_type ?? self::SALARY_TYPE_DAILY) === self::SALARY_TYPE_MONTHLY;
    }

    public function wageRates(): HasMany
    {
        return $this->hasMany(EmployeeWageRate::class)->orderBy('effective_from', 'desc');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(EmployeeAttendance::class);
    }

    public function companyHolidays(): BelongsToMany
    {
        return $this->belongsToMany(CompanyHoliday::class, 'company_holiday_employees')->withTimestamps();
    }

    public function advances(): HasMany
    {
        return $this->hasMany(EmployeeAdvance::class);
    }

    public function payrollItems(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function bonuses(): HasMany
    {
        return $this->hasMany(EmployeeBonus::class);
    }

    public function allowances(): HasMany
    {
        return $this->hasMany(EmployeeAllowance::class);
    }

    public function overtimes(): HasMany
    {
        return $this->hasMany(EmployeeOvertime::class);
    }

    public function getHourlyRateAttribute(): float
    {
        return EmployeeOvertime::calculateHourlyRate($this);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wage rate applicable on a given date.
     */
    public function getWageForDate($date): float
    {
        $dateStr = $date instanceof Carbon ? $date->toDateString() : (string) $date;

        $wageRate = $this->wageRates()
            ->where('effective_from', '<=', $dateStr)
            ->where(function ($query) use ($dateStr) {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', $dateStr);
            })
            ->latest('effective_from')
            ->first();

        return $wageRate ? (float) $wageRate->daily_wage : (float) $this->daily_wage;
    }

    /**
     * Get the monthly basic salary applicable on a given date.
     */
    public function getMonthlySalaryForDate($date): float
    {
        $dateStr = $date instanceof Carbon ? $date->toDateString() : (string) $date;

        $wageRate = $this->wageRates()
            ->where('effective_from', '<=', $dateStr)
            ->where(function ($query) use ($dateStr) {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', $dateStr);
            })
            ->latest('effective_from')
            ->first();

        return $wageRate ? (float) ($wageRate->monthly_basic_salary ?? 0.00) : (float) ($this->monthly_basic_salary ?? 0.00);
    }

    /**
     * Update salary rate (daily or monthly) and record in wage rate history without altering past rates.
     */
    public function updateSalaryRate(
        string $salaryType,
        float $dailyWage,
        float $monthlySalary,
        string $effectiveFrom,
        ?string $reason = null
    ): EmployeeWageRate {
        $effectiveDate = Carbon::parse($effectiveFrom);
        $previousEndDate = $effectiveDate->copy()->subDay()->toDateString();

        // Close currently active open-ended wage rate
        $this->wageRates()
            ->whereNull('effective_to')
            ->where('effective_from', '<', $effectiveFrom)
            ->update(['effective_to' => $previousEndDate]);

        // Create new wage rate record
        $newRate = $this->wageRates()->create([
            'salary_type' => $salaryType,
            'daily_wage' => $dailyWage,
            'monthly_basic_salary' => $monthlySalary,
            'effective_from' => $effectiveFrom,
            'effective_to' => null,
            'reason' => $reason,
        ]);

        // Update current salary attributes on employee
        $this->update([
            'salary_type' => $salaryType,
            'daily_wage' => $dailyWage,
            'monthly_basic_salary' => $monthlySalary,
        ]);

        return $newRate;
    }

    /**
     * Backward-compatible update daily wage and record in wage history.
     */
    public function updateWage(float $newWage, string $effectiveFrom, ?string $reason = null): EmployeeWageRate
    {
        return $this->updateSalaryRate(
            self::SALARY_TYPE_DAILY,
            $newWage,
            (float) ($this->monthly_basic_salary ?? 0.00),
            $effectiveFrom,
            $reason
        );
    }

    /**
     * Generate next sequential Employee ID e.g. EMP-0001
     */
    public static function generateNextEmployeeNumber(): string
    {
        $latest = static::orderByDesc('id')->first();
        if (! $latest) {
            return 'EMP-0001';
        }

        if (preg_match('/EMP-(\d+)/i', $latest->employee_number, $matches)) {
            $next = ((int) $matches[1]) + 1;

            return sprintf('EMP-%04d', $next);
        }

        return sprintf('EMP-%04d', $latest->id + 1);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
