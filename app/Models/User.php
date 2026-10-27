<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        // Staff default permissions for Stage 3:
        // Staff can register vehicles, create jobs, update jobs, process sales, generate invoices.
        // Price override and job cancellations require admin role or specific permission.
        $staffAllowed = [
            'register_vehicles',
            'view_history',
            'create_jobs',
            'update_jobs',
            'process_sales',
            'generate_invoices',
            'view_quotations',
            'create_quotations',
            'edit_quotations',
            'convert_quotations',
            'view_invoices',
            'record_payments',
            'view_employees',
            'mark_attendance',
            'view_holidays',
            'view_expenses',
            'create_expenses',
            'view_stock_reports',
            'view_sales_reports',
            'view_vehicle_service_reports',
            'view_money_book',
            'view_credit_management',
            'view_advances',
            'view_bonuses_allowances',
        ];

        return in_array($permission, $staffAllowed);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Branches assigned to this user.
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_user')->withTimestamps();
    }

    /**
     * Branches managed by this user.
     */
    public function managedBranches()
    {
        return $this->hasMany(Branch::class, 'manager_id');
    }

    /**
     * Check if user can access a specific branch.
     */
    public function canAccessBranch(int|string|null $branchId): bool
    {
        if (! $branchId) {
            return false;
        }

        if ($this->isAdmin()) {
            return Branch::where('id', $branchId)->where('is_active', true)->exists();
        }

        return $this->branches()->where('branches.id', $branchId)->where('branches.is_active', true)->exists();
    }

    /**
     * Get collection of branches accessible to this user.
     */
    public function getAccessibleBranches()
    {
        if ($this->isAdmin()) {
            return Branch::where('is_active', true)
                ->orderByDesc('is_main_branch')
                ->orderBy('branch_name')
                ->get();
        }

        return $this->branches()
            ->where('branches.is_active', true)
            ->orderByDesc('branches.is_main_branch')
            ->orderBy('branches.branch_name')
            ->get();
    }

    /**
     * Check if user has permission to manage branches.
     */
    public function canManageBranches(): bool
    {
        return $this->isAdmin();
    }
}
