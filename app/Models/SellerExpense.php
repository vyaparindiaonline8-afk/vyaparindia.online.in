<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerExpense extends Model
{
    use HasFactory;

    protected $table = 'seller_expenses';

    protected $fillable = [
        'user_id',
        'expense_type',
        'category',
        'amount',
        'expense_date',
        'platform',
        'payment_mode',
        'paid_to',
        'invoice_receipt_no',
        'notes',
        'is_recurring',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'is_recurring' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
