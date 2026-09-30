<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'variant_name',
        'sku',
        'purchase_price',
        'trade_discount_percent',
        'gst_percent',
        'net_landing_cost',
        'wholesale_price',
        'retail_price',
        'mrp',
        'stock_quantity',
        'track_inventory',
        'is_active',
        'image_url',
        'attributes',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'trade_discount_percent' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'net_landing_cost' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'retail_price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'stock_quantity' => 'integer',
        'track_inventory' => 'boolean',
        'is_active' => 'boolean',
        'attributes' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'variant_id');
    }
}