<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_number',
        'name',
        'mobile',
        'address',
        'daily_wage',
        'joining_date',
        'position',
        'is_active',
        'notes',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'daily_wage' => 'decimal:2',
            'joining_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function wageRates(): HasMany
    {
        return $this->hasMany(EmployeeWageRate::class)->orderBy('effective_from', 'desc');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(EmployeeAttendance::class);
    }

    public function advances(): HasMany
    {
        return $this->hasMany(EmployeeAdvance::class);
    }

    public function payrollItems(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
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
     * Update daily wage and record in wage history without altering past rates.
     */
    public function updateWage(float $newWage, string $effectiveFrom, ?string $reason = null): EmployeeWageRate
    {
        $effectiveDate = Carbon::parse($effectiveFrom);
        $previousEndDate = $effectiveDate->copy()->subDay()->toDateString();

        // Close currently active open-ended wage rate
        $this->wageRates()
            ->whereNull('effective_to')
            ->where('effective_from', '<', $effectiveFrom)
            ->update(['effective_to' => $previousEndDate]);

        // Create new wage rate record
        $newRate = $this->wageRates()->create([
            'daily_wage' => $newWage,
            'effective_from' => $effectiveFrom,
            'effective_to' => null,
            'reason' => $reason,
        ]);

        // Update current daily wage on employee
        $this->update(['daily_wage' => $newWage]);

        return $newRate;
    }

    /**
     * Generate next sequential Employee ID e.g. EMP-0001
     */
    public static function generateNextEmployeeNumber(): string
    {
        $latest = static::orderByDesc('id')->first();
        if (!$latest) {
            return 'EMP-0001';
        }

        if (preg_match('/EMP-(\d+)/i', $latest->employee_number, $matches)) {
            $next = ((int) $matches[1]) + 1;
            return sprintf('EMP-%04d', $next);
        }

        return sprintf('EMP-%04d', $latest->id + 1);
    }
}
