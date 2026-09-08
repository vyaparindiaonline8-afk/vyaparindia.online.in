<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'channel_name',
        'store_name',
        'store_url',
        'api_key',
        'api_secret',
        'access_token',
        'is_active',
        'auto_sync_inventory',
        'last_synced_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'auto_sync_inventory' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listings()
    {
        return $this->hasMany(ChannelListing::class);
    }
}