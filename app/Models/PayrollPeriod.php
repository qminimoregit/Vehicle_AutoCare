<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class PayrollPeriod extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_REVIEWED = 'reviewed';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_PARTIALLY_PAID = 'partially_paid';

    public const STATUS_PAID = 'paid';

    protected $fillable = [
        'year',
        'month',
        'status',
        'total_base_earnings',
        'total_overtime',
        'total_bonuses',
        'total_deductions',
        'total_advances',
        'total_gross',
        'total_net',
        'total_paid',
        'total_epf_employee',
        'total_epf_employer',
        'total_etf_employer',
        'total_apit',
        'notes',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'total_base_earnings' => 'decimal:2',
            'total_overtime' => 'decimal:2',
            'total_bonuses' => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'total_advances' => 'decimal:2',
            'total_gross' => 'decimal:2',
            'total_net' => 'decimal:2',
            'total_paid' => 'decimal:2',
            'total_epf_employee' => 'decimal:2',
            'total_epf_employer' => 'decimal:2',
            'total_etf_employer' => 'decimal:2',
            'total_apit' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getMonthNameAttribute(): string
    {
        return Carbon::createFromDate($this->year, $this->month, 1)->format('F');
    }

    public function getFormattedPeriodAttribute(): string
    {
        return Carbon::createFromDate($this->year, $this->month, 1)->format('F Y');
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isReviewed(): bool
    {
        return $this->status === self::STATUS_REVIEWED;
    }

    public function isModifiable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REVIEWED]);
    }

    public function isApproved(): bool
    {
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_PARTIALLY_PAID, self::STATUS_PAID]);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function getTotalEpfEmployeeAttribute($value): float
    {
        if ($value !== null) {
            return (float) $value;
        }

        return (float) $this->items->sum('epf_employee_amount');
    }

    public function getTotalEpfEmployerAttribute($value): float
    {
        if ($value !== null) {
            return (float) $value;
        }

        return (float) $this->items->sum('epf_employer_amount');
    }

    public function getTotalEtfEmployerAttribute($value): float
    {
        if ($value !== null) {
            return (float) $value;
        }

        return (float) $this->items->sum('etf_employer_amount');
    }

    public function getTotalApitAttribute($value): float
    {
        if ($value !== null) {
            return (float) $value;
        }

        return (float) $this->items->sum('apit_amount');
    }

    /**
     * Total cost to company = Gross Salary + Employer EPF (12%) + Employer ETF (3%)
     */
    public function getTotalCostToCompanyAttribute(): float
    {
        return round((float) $this->total_gross + (float) $this->total_epf_employer + (float) $this->total_etf_employer, 2);
    }

    /**
     * Recalculate totals from items
     */
    public function recalculateTotals(): void
    {
        $this->loadMissing('items');

        $totalBase = (float) $this->items->sum('base_earnings');
        $totalOvertime = (float) $this->items->sum('overtime_amount');
        $totalBonuses = (float) $this->items->sum('bonus_amount');
        $totalEpfEmployee = (float) $this->items->sum('epf_employee_amount');
        $totalEpfEmployer = (float) $this->items->sum('epf_employer_amount');
        $totalEtfEmployer = (float) $this->items->sum('etf_employer_amount');
        $totalApit = (float) $this->items->sum('apit_amount');

        $totalDeductions = (float) $this->items->sum(function ($item) {
            return (float) $item->deductions + (float) $item->no_pay_deduction + (float) $item->epf_employee_amount + (float) $item->apit_amount;
        });
        $totalAdvances = (float) $this->items->sum('advances_applied');
        $totalGross = (float) $this->items->sum('gross_salary');
        $totalNet = (float) $this->items->sum('net_salary');
        $totalPaid = (float) $this->items->sum('paid_amount');

        $status = $this->status;
        if (! in_array($status, [self::STATUS_DRAFT, self::STATUS_REVIEWED])) {
            if ($totalPaid >= $totalNet && $totalNet > 0) {
                $status = self::STATUS_PAID;
            } elseif ($totalPaid > 0) {
                $status = self::STATUS_PARTIALLY_PAID;
            } else {
                $status = self::STATUS_APPROVED;
            }
        }

        $payload = [
            'total_base_earnings' => $totalBase,
            'total_overtime' => $totalOvertime,
            'total_bonuses' => $totalBonuses,
            'total_deductions' => $totalDeductions,
            'total_advances' => $totalAdvances,
            'total_gross' => $totalGross,
            'total_net' => $totalNet,
            'total_paid' => $totalPaid,
            'status' => $status,
        ];

        if (Schema::hasColumn('payroll_periods', 'total_epf_employee')) {
            $payload['total_epf_employee'] = $totalEpfEmployee;
        }
        if (Schema::hasColumn('payroll_periods', 'total_epf_employer')) {
            $payload['total_epf_employer'] = $totalEpfEmployer;
        }
        if (Schema::hasColumn('payroll_periods', 'total_etf_employer')) {
            $payload['total_etf_employer'] = $totalEtfEmployer;
        }
        if (Schema::hasColumn('payroll_periods', 'total_apit')) {
            $payload['total_apit'] = $totalApit;
        }

        $this->update($payload);
    }
}
