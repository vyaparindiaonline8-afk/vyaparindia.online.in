<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropshipPartnership extends Model
{
    use HasFactory;

    protected $fillable = [
        'retailer_id',
        'wholesaler_id',
        'status',
        'request_note',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function retailer()
    {
        return $this->belongsTo(User::class, 'retailer_id');
    }

    public function wholesaler()
    {
        return $this->belongsTo(User::class, 'wholesaler_id');
    }
}
