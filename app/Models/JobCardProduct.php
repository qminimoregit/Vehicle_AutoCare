<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCardProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_card_id',
        'product_id',
        'product_sku',
        'product_name',
        'quantity',
        'unit_price',
        'unit_cost',
        'line_total',
        'is_stock_deducted',
        'is_additional',
        'job_issue_id',
        'invoice_id',
        'batch_allocations',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'line_total' => 'decimal:2',
        'is_stock_deducted' => 'boolean',
        'is_additional' => 'boolean',
        'batch_allocations' => 'array',
    ];

    public function jobCard()
    {
        return $this->belongsTo(JobCard::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
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
