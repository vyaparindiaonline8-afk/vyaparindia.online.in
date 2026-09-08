<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyerProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'address',
        'phone_number',
        'company_name',
        'gst_number',
    ];

    /**
     * Get the user that owns the buyer profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
