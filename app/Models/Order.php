<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'order_source',
        'user_id',
        'seller_id',
        'total_price',
        'customer_name',
        'customer_phone',
        'customer_email',
        'shipping_address',
        'city',
        'state',
        'pincode',
        'payment_method',
        'payment_status',
        'cod_verification_status',
        'cod_verification_token',
        'verification_deadline_at',
        'cod_to_prepaid_discount',
        'original_cod_total',
        'rto_risk_score',
        'status',
        'notes',
    ];

    protected $casts = [
        'verification_deadline_at' => 'datetime',
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity', 'price');
    }

    public function dispatch()
    {
        return $this->hasOne(Dispatch::class);
    }
}
