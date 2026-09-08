<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GstInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'seller_id',
        'invoice_number',
        'invoice_type',
        'seller_gstin',
        'seller_legal_name',
        'seller_state_code',
        'seller_state_name',
        'buyer_gstin',
        'buyer_legal_name',
        'buyer_state_code',
        'buyer_state_name',
        'is_interstate',
        'taxable_amount',
        'cgst_rate',
        'cgst_amount',
        'sgst_rate',
        'sgst_amount',
        'igst_rate',
        'igst_amount',
        'total_tax',
        'invoice_total',
        'invoice_date',
    ];

    protected $casts = [
        'is_interstate' => 'boolean',
        'invoice_date' => 'date',
        'taxable_amount' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'igst_amount' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'invoice_total' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}