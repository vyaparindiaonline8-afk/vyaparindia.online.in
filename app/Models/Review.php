<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'seller_id',
        'order_id',
        'customer_name',
        'customer_city',
        'review_title',
        'rating',
        'comment',
        'is_verified_purchase',
        'is_approved',
        'helpful_votes',
    ];

    protected $casts = [
        'is_verified_purchase' => 'boolean',
        'is_approved' => 'boolean',
        'rating' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}