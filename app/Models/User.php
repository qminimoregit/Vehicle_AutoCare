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
            'view_employees',
            'mark_attendance',
            'view_expenses',
            'create_expenses',
        ];

        return in_array($permission, $staffAllowed);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
