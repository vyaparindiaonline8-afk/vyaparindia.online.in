<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductPricingTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'tier_name',
        'min_quantity',
        'max_quantity',
        'unit_price',
        'discount_percent',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}