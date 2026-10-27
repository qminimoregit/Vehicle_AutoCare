<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAdvanceRecovery extends Model
{
    use HasFactory;

    public const STATUS_PENDING_PAYROLL = 'pending_payroll';

    public const STATUS_RECOVERED = 'recovered';

    public const STATUS_REVERSED = 'reversed';

    protected $fillable = [
        'employee_advance_id',
        'payroll_period_id',
        'payroll_item_id',
        'employee_id',
        'recovery_date',
        'amount',
        'status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'recovery_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function advance(): BelongsTo
    {
        return $this->belongsTo(EmployeeAdvance::class, 'employee_advance_id');
    }

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function payrollItem(): BelongsTo
    {
        return $this->belongsTo(PayrollItem::class, 'payroll_item_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
