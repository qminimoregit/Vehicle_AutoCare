<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCardService extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_card_id',
        'service_id',
        'service_code',
        'service_name',
        'unit_price',
        'quantity',
        'line_total',
        'is_price_custom',
        'is_price_pending',
        'is_additional',
        'job_issue_id',
        'invoice_id',
        'technician_name',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'decimal:2',
        'line_total' => 'decimal:2',
        'is_price_custom' => 'boolean',
        'is_price_pending' => 'boolean',
        'is_additional' => 'boolean',
    ];

    public function jobCard()
    {
        return $this->belongsTo(JobCard::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function issue()
    {
        return $this->belongsTo(JobIssue::class, 'job_issue_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
