<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectSaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'direct_sale_id',
        'product_id',
        'product_sku',
        'product_name',
        'quantity',
        'unit_price',
        'unit_cost',
        'line_total',
        'batch_allocations',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'line_total' => 'decimal:2',
        'batch_allocations' => 'array',
    ];

    public function directSale()
    {
        return $this->belongsTo(DirectSale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
