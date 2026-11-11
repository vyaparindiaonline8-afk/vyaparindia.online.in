<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerOtherIncome extends Model
{
    use HasFactory;

    protected $table = 'seller_other_incomes';

    protected $fillable = [
        'user_id',
        'income_type',
        'amount',
        'income_date',
        'platform',
        'received_from',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'income_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
