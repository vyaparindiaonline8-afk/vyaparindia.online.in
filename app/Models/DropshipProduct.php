<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropshipProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'dropshipper_id',
        'wholesaler_id',
        'supplier_product_id',
        'custom_name',
        'wholesale_price',
        'retail_price',
        'profit_margin',
        'is_active',
    ];

    public function dropshipper()
    {
        return $this->belongsTo(User::class, 'dropshipper_id');
    }

    public function wholesaler()
    {
        return $this->belongsTo(User::class, 'wholesaler_id');
    }

    public function supplierProduct()
    {
        return $this->belongsTo(Product::class, 'supplier_product_id');
    }
}