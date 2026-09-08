<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayoutRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'user_id',
        'payout_number',
        'amount',
        'payout_method',
        'beneficiary_details',
        'status',
        'utr_number',
        'gateway_reference',
        'completed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'beneficiary_details' => 'array',
        'completed_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function wallet()
    {
        return $this->belongsTo(DropshipWallet::class, 'wallet_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}