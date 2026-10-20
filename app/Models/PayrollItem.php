<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollItem extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PARTIALLY_PAID = 'partially_paid';
    public const STATUS_PAID = 'paid';

    protected $fillable = [
        'payroll_period_id',
        'employee_id',
        'employee_name_snapshot',
        'daily_wage_snapshot',
        'full_days_count',
        'half_days_count',
        'absent_days_count',
        'leave_days_count',
        'day_off_days_count',
        'payable_days',
        'base_earnings',
        'overtime_amount',
        'bonus_amount',
        'deductions',
        'epf_employee_amount',
        'epf_employer_amount',
        'etf_employer_amount',
        'advances_applied',
        'gross_salary',
        'net_salary',
        'paid_amount',
        'balance_due',
        'status',
        'attendance_breakdown',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'daily_wage_snapshot' => 'decimal:2',
            'payable_days' => 'decimal:2',
            'base_earnings' => 'decimal:2',
            'overtime_amount' => 'decimal:2',
            'bonus_amount' => 'decimal:2',
            'deductions' => 'decimal:2',
            'epf_employee_amount' => 'decimal:2',
            'epf_employer_amount' => 'decimal:2',
            'etf_employer_amount' => 'decimal:2',
            'advances_applied' => 'decimal:2',
            'gross_salary' => 'decimal:2',
            'net_salary' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance_due' => 'decimal:2',
            'attendance_breakdown' => 'array',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function advances(): HasMany
    {
        return $this->hasMany(EmployeeAdvance::class, 'payroll_item_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PayrollPayment::class, 'payroll_item_id');
    }

    /**
     * Recalculate gross, net, paid, and balance due
     */
    public function recalculate(): void
    {
        $base = (float) $this->base_earnings;
        $overtime = (float) $this->overtime_amount;
        $bonus = (float) $this->bonus_amount;
        $deductions = (float) $this->deductions;
        $epfEmployee = (float) $this->epf_employee_amount;
        $advances = (float) $this->advances_applied;

        $gross = round($base + $overtime + $bonus, 2);
        $totalDeductions = round($deductions + $epfEmployee + $advances, 2);
        $net = round(max(0, $gross - $totalDeductions), 2);

        $paid = (float) $this->payments()->sum('amount');
        $balance = round(max(0, $net - $paid), 2);

        $status = $this->status;
        if ($this->period && $this->period->status !== PayrollPeriod::STATUS_DRAFT) {
            if ($paid >= $net && $net > 0) {
                $status = self::STATUS_PAID;
            } elseif ($paid > 0) {
                $status = self::STATUS_PARTIALLY_PAID;
            } else {
                $status = self::STATUS_APPROVED;
            }
        }

        $this->update([
            'gross_salary' => $gross,
            'net_salary' => $net,
            'paid_amount' => $paid,
            'balance_due' => $balance,
            'status' => $status,
        ]);

        if ($this->period) {
            $this->period->recalculateTotals();
        }
    }
}
