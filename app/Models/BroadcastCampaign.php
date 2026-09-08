<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BroadcastCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'campaign_name',
        'message_template',
        'target_audience',
        'recipient_count',
        'sent_count',
        'click_count',
        'order_count',
        'status',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}