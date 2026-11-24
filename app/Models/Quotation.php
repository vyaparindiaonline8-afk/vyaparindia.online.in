<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'items' => 'array',
        'subtotal' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'valid_until' => 'date',
        'approved_at' => 'datetime',
    ];

    public function sellerPage()
    {
        return $this->belongsTo(SellerPage::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function convertedOrder()
    {
        return $this->belongsTo(Order::class, 'converted_order_id');
    }

    public function isApproved(): bool
    {
        return in_array($this->status, ['approved', 'converted_to_order']);
    }

    public function getFormattedStatusAttribute(): string
    {
        return match ($this->status) {
            'approved' => '✅ Approved by Store Owner',
            'converted_to_order' => '🛍️ Converted to Order',
            'rejected' => '❌ Rejected',
            default => '⏳ Pending Owner Approval',
        };
    }
}
