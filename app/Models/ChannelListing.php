<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'product_id',
        'external_product_id',
        'sync_status',
        'listing_url',
        'channel_price',
        'synced_stock',
        'last_synced_at',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}