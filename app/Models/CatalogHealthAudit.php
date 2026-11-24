<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogHealthAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'seller_page_id',
        'health_score',
        'breakdown',
        'stats',
        'recommendations',
        'products_audit',
        'audited_at',
    ];

    protected $casts = [
        'health_score' => 'integer',
        'breakdown' => 'array',
        'stats' => 'array',
        'recommendations' => 'array',
        'products_audit' => 'array',
        'audited_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sellerPage()
    {
        return $this->belongsTo(SellerPage::class);
    }
}
