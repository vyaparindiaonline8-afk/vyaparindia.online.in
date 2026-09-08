<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbandonedCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'cart_items',
        'total_amount',
        'recovery_token',
        'recovery_discount_percent',
        'recovery_discount_amount',
        'recovery_status',
        'recovery_sent_at',
        'recovered_at',
        'expires_at',
    ];

    protected $casts = [
        'cart_items' => 'array',
        'recovery_sent_at' => 'datetime',
        'recovered_at' => 'datetime',
        'expires_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'recovery_discount_percent' => 'decimal:2',
        'recovery_discount_amount' => 'decimal:2',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}