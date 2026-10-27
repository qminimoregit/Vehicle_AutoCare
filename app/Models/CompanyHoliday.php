<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Schema;

class CompanyHoliday extends Model
{
    use HasFactory;

    public const TYPE_PAID = 'paid';

    public const TYPE_UNPAID = 'unpaid';

    public const DURATION_FULL_DAY = 'full_day';

    public const DURATION_HALF_DAY = 'half_day';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'branch_id',
        'name',
        'start_date',
        'end_date',
        'duration',
        'holiday_type',
        'applies_to_all',
        'status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'applies_to_all' => 'boolean',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'company_holiday_employees')
            ->withTimestamps();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeForBranch(Builder $query, ?int $branchId): Builder
    {
        if (! $branchId) {
            return $query;
        }

        return $query->where(function ($q) use ($branchId) {
            $q->whereNull('branch_id')
                ->orWhere('branch_id', $branchId);
        });
    }

    public function scopeForDate(Builder $query, $date): Builder
    {
        $dateStr = $date instanceof Carbon ? $date->toDateString() : (string) $date;

        return $query->where('start_date', '<=', $dateStr)
            ->where('end_date', '>=', $dateStr);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isPaid(): bool
    {
        return $this->holiday_type === self::TYPE_PAID;
    }

    public function isFullDay(): bool
    {
        return $this->duration === self::DURATION_FULL_DAY;
    }

    public function isHalfDay(): bool
    {
        return $this->duration === self::DURATION_HALF_DAY;
    }

    /**
     * Check if this holiday applies to a specific employee.
     */
    public function appliesToEmployee(Employee $employee): bool
    {
        if ($this->branch_id !== null && $employee->branch_id !== null && (int) $this->branch_id !== (int) $employee->branch_id) {
            return false;
        }

        if ($this->applies_to_all) {
            return true;
        }

        return $this->employees->contains('id', $employee->id);
    }

    /**
     * Safe static check for holidays on a given date (handles if table is not yet migrated).
     */
    public static function getHolidaysForDate($date, ?int $branchId = null)
    {
        if (! Schema::hasTable('company_holidays')) {
            return collect();
        }

        $query = static::active()->forDate($date);

        if ($branchId) {
            $query->forBranch($branchId);
        }

        return $query->with(['branch', 'employees'])->get();
    }

    /**
     * Safe static map of holidays in a month for calendar rendering.
     */
    public static function getHolidaysForMonth(int $year, int $month, ?int $branchId = null): array
    {
        if (! Schema::hasTable('company_holidays')) {
            return [];
        }

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

        $query = static::active()
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($sub) use ($startDate, $endDate) {
                        $sub->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            });

        if ($branchId) {
            $query->forBranch($branchId);
        }

        $holidays = $query->with('branch')->get();
        $map = [];

        foreach ($holidays as $h) {
            $cursor = $h->start_date->copy();
            $last = $h->end_date->copy();
            while ($cursor->lessThanOrEqualTo($last)) {
                $dayStr = $cursor->toDateString();
                if (! isset($map[$dayStr])) {
                    $map[$dayStr] = [];
                }
                $map[$dayStr][] = $h;
                $cursor->addDay();
            }
        }

        return $map;
    }
}
