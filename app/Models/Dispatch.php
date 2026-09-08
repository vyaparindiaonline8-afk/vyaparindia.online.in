<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'courier_name',
        'tracking_number',
        'notes',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
