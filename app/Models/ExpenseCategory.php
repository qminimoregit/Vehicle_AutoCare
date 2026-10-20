<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'category_id');
    }

    public function canBeDeleted(): bool
    {
        return $this->expenses()->count() === 0;
    }

    /**
     * Seed or retrieve default categories.
     */
    public static function seedDefaults(): void
    {
        $defaults = [
            'Salary Advance',
            'Boss Expenses',
            'Electricity',
            'EPF / ETF',
            'Staff Welfare',
            'Water',
            'Telephone / Internet',
            'Chemicals',
            'Repair',
            'Maintenance',
            'Other',
        ];

        foreach ($defaults as $name) {
            static::firstOrCreate(
                ['name' => $name],
                ['is_active' => true]
            );
        }
    }
}
