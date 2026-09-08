<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayoutAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payout_type',
        'upi_id',
        'account_holder_name',
        'bank_name',
        'account_number',
        'ifsc_code',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}