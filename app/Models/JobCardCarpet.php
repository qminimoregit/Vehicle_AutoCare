<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCardCarpet extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_card_id',
        'carpet_category_id',
        'category_name',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function jobCard()
    {
        return $this->belongsTo(JobCard::class);
    }

    public function carpetCategory()
    {
        return $this->belongsTo(CarpetCategory::class, 'carpet_category_id');
    }
}
