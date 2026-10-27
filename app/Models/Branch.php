<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_code',
        'branch_name',
        'address',
        'phone',
        'email',
        'manager_id',
        'is_main_branch',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_main_branch' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Alias for branch_name to support $branch->name.
     */
    public function getNameAttribute(): string
    {
        return $this->branch_name ?? '';
    }

    /**
     * Create a new Eloquent query builder for the model, mapping 'name' to 'branch_name'.
     */
    public function newEloquentBuilder($query): Builder
    {
        return new class($query) extends Builder
        {
            public function orderBy($column, $direction = 'asc')
            {
                if ($column === 'name') {
                    $column = 'branch_name';
                }

                return parent::orderBy($column, $direction);
            }
        };
    }

    /**
     * The company this branch belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(CompanySetting::class, 'company_id');
    }

    /**
     * Branch manager assigned to this branch.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Staff users assigned to this branch.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'branch_user')->withTimestamps();
    }

    /**
     * Vehicles registered under this branch.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'branch_id');
    }

    /**
     * Job cards processed under this branch.
     */
    public function jobCards(): HasMany
    {
        return $this->hasMany(JobCard::class, 'branch_id');
    }

    /**
     * Direct POS sales completed at this branch.
     */
    public function directSales(): HasMany
    {
        return $this->hasMany(DirectSale::class, 'branch_id');
    }

    /**
     * Goods received notes created at this branch.
     */
    public function goodsReceivedNotes(): HasMany
    {
        return $this->hasMany(GoodsReceivedNote::class, 'branch_id');
    }

    /**
     * Expenses recorded at this branch.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'branch_id');
    }

    /**
     * Employees assigned to this branch.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'branch_id');
    }

    /**
     * Invoices issued under this branch.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'branch_id');
    }

    /**
     * Quotations prepared under this branch.
     */
    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class, 'branch_id');
    }

    /**
     * Appointments scheduled for this branch.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'branch_id');
    }

    /**
     * Scope query to only active branches.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope query to the main branch.
     */
    public function scopeMain(Builder $query): Builder
    {
        return $query->where('is_main_branch', true);
    }

    /**
     * Set this branch as the main branch exclusively.
     */
    public function setAsMain(): self
    {
        DB::transaction(function () {
            static::where('company_id', $this->company_id)
                ->where('id', '!=', $this->id)
                ->update(['is_main_branch' => false]);

            $this->update([
                'is_main_branch' => true,
                'is_active' => true,
            ]);
        });

        return $this;
    }
}
