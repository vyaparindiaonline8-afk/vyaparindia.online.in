<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmazonFeeCache extends Model
{
    use HasFactory;

    protected $table = 'amazon_fee_caches';

    protected $fillable = [
        'channel_id',
        'product_id',
        'marketplace',
        'sku',
        'asin',
        'listing_price',
        'weight_kg',
        'referral_fee',
        'closing_fee',
        'shipping_fee',
        'tax_fee',
        'total_fee',
        'effective_fee_pct',
        'is_actual',
        'source',
        'fee_breakdown',
        'fetched_at',
    ];

    protected $casts = [
        'listing_price' => 'decimal:2',
        'weight_kg' => 'decimal:3',
        'referral_fee' => 'decimal:2',
        'closing_fee' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'tax_fee' => 'decimal:2',
        'total_fee' => 'decimal:2',
        'effective_fee_pct' => 'decimal:2',
        'is_actual' => 'boolean',
        'fee_breakdown' => 'array',
        'fetched_at' => 'datetime',
    ];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope to find valid cache entry within specified hours
     */
    public function scopeValidFor($query, $skuOrAsin, $price, $hours = 6, $marketplace = 'amazon')
    {
        return $query->where('marketplace', $marketplace)
            ->where(function ($q) use ($skuOrAsin) {
                $q->where('sku', $skuOrAsin)->orWhere('asin', $skuOrAsin);
            })
            ->whereBetween('listing_price', [max(0, $price - 1.0), $price + 1.0])
            ->where('fetched_at', '>=', now()->subHours($hours))
            ->latest('fetched_at');
    }
}
