<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAdvance extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_DEDUCTED = 'deducted';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'advance_number',
        'employee_id',
        'advance_date',
        'amount',
        'payment_method',
        'notes',
        'status',
        'payroll_item_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'advance_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollItem(): BelongsTo
    {
        return $this->belongsTo(PayrollItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateNextAdvanceNumber(): string
    {
        $latest = static::orderByDesc('id')->first();
        if (! $latest) {
            return 'ADV-0001';
        }

        if (preg_match('/ADV-(\d+)/i', $latest->advance_number, $matches)) {
            $next = ((int) $matches[1]) + 1;

            return sprintf('ADV-%04d', $next);
        }

        return sprintf('ADV-%04d', $latest->id + 1);
    }
}
