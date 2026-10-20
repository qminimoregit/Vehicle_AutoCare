<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_card_id',
        'vehicle_id',
        'description',
        'recommended_repair',
        'estimated_cost',
        'remarks',
        'status',
        'approval_method',
        'approval_contact',
        'approval_note',
        'approved_at',
        'approved_by',
        'recorded_by',
        'supplementary_invoice_id',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function jobCard(): BelongsTo
    {
        return $this->belongsTo(JobCard::class, 'job_card_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function supplementaryInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'supplementary_invoice_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(JobCardService::class, 'job_issue_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(JobCardProduct::class, 'job_issue_id');
    }

    public function isPendingApproval(): bool
    {
        return $this->status === 'pending_approval';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isDeclined(): bool
    {
        return $this->status === 'declined';
    }

    public function isFixed(): bool
    {
        return $this->status === 'fixed';
    }

    public function isDeferred(): bool
    {
        return $this->status === 'deferred';
    }
}
