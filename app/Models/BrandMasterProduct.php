<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandMasterProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_name',
        'category_name',
        'product_name',
        'item_type',
        'size_mm',
        'size_inch',
        'product_code',
        'list_price',
        'box_qty',
        'pouch_qty',
        'default_gst_percent',
        'hsn_code',
        'image_url',
        'description',
        'is_active',
    ];

    protected $casts = [
        'list_price' => 'decimal:2',
        'default_gst_percent' => 'decimal:2',
        'box_qty' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getFormattedPriceAttribute()
    {
        return '₹' . number_format($this->list_price, 2);
    }
}
