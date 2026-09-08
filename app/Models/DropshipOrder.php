<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropshipOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'ds_order_number',
        'order_id',
        'dropshipper_id',
        'wholesaler_id',
        'customer_retail_total',
        'supplier_base_cost',
        'shipping_cost',
        'total_supplier_payable',
        'dropshipper_profit',
        'price_adjusted_by_wholesaler',
        'wholesaler_adjustment_note',
        'dropshipper_approval_status',
        'fulfillment_status',
        'courier_partner',
        'awb_number',
        'tracking_url',
        'dispatched_at',
        'delivered_at',
        'payment_collection_mode',
        'cod_remittance_status',
    ];

    protected $casts = [
        'price_adjusted_by_wholesaler' => 'boolean',
        'dispatched_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function dropshipper()
    {
        return $this->belongsTo(User::class, 'dropshipper_id');
    }

    public function wholesaler()
    {
        return $this->belongsTo(User::class, 'wholesaler_id');
    }
}