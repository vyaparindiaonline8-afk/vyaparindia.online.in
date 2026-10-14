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

    public function getPriceAttribute(): float
    {
        return (float)($this->retail_price ?: ($this->attributes['price'] ?? 0));
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'variant_id');
    }

    public function getCustomAttributesAttribute()
    {
        $raw = $this->getAttributes()['attributes'] ?? null;
        if (is_array($raw)) return $raw;
        if (is_string($raw)) return json_decode($raw, true) ?: [];
        return [];
    }

    public function getCleanSizeAttribute(): string
    {
        if (!empty($this->variant_name) && $this->variant_name !== 'Standard') {
            return $this->variant_name;
        }
        $custom = $this->custom_attributes;
        if (!empty($custom['size']) && $custom['size'] !== 'Standard') {
            return $custom['size'];
        }
        return $this->variant_name ?: ($custom['size'] ?? 'Standard');
    }

    public function getSizeAttribute(): string
    {
        return $this->clean_size;
    }

    public function setSizeAttribute($value): void
    {
        $this->attributes['variant_name'] = (string)$value;
        $attrs = $this->custom_attributes ?? [];
        $attrs['size'] = (string)$value;
        $this->attributes['attributes'] = json_encode($attrs);
    }

    public function getPackingInfoAttribute(): string
    {
        $custom = $this->custom_attributes;
        $p1 = $custom['packing_1'] ?? '';
        $p2 = $custom['packing_2'] ?? '';
        $parts = [];
        if (!empty($p1) && $p1 !== '-') $parts[] = "Box: {$p1}";
        if (!empty($p2) && $p2 !== '-') $parts[] = "Bag: {$p2}";
        return implode(' | ', $parts);
    }
}