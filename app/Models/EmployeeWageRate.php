<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeWageRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'daily_wage',
        'effective_from',
        'effective_to',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'daily_wage' => 'decimal:2',
            'effective_from' => 'date',
            'effective_to' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
