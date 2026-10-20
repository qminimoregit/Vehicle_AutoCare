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
        'batch_allocations',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'line_total' => 'decimal:2',
        'is_stock_deducted' => 'boolean',
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
}
