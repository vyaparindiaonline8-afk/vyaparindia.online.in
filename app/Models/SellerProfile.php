<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'phone_number',
        'address',
        'city',
        'state',
        'country',
        'gst_number',
        'dispatch_radius',
        'upi_id',
        'bank_name',
        'bank_account_number',
        'bank_ifsc',
        'bank_account_holder',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
