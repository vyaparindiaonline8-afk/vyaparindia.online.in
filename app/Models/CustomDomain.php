<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomDomain extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_page_id',
        'user_id',
        'domain',
        'cname_target',
        'verification_txt',
        'dns_status',
        'ssl_status',
        'is_primary',
        'verified_at',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function sellerPage()
    {
        return $this->belongsTo(SellerPage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}