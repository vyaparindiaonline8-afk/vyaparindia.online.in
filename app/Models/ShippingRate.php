<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    use HasFactory;

    protected $table = 'shipping_rates';

    protected $fillable = [
        'carrier',
        'zone',
        'weight',
        'rate',
        'gst_percent',
        'label',
        'is_active',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'rate' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get rate for a given weight in kg.
     */
    public static function getRateForWeight(float $weightKg): ?self
    {
        return self::where('is_active', true)
            ->where('weight', '>=', $weightKg)
            ->orderBy('weight', 'asc')
            ->first() ?? self::where('is_active', true)->orderBy('weight', 'desc')->first();
    }
}
