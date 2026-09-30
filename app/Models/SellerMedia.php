<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'filename',
        'file_path',
        'is_assigned',
        'product_id',
    ];

    protected $casts = [
        'is_assigned' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getUrlAttribute(): string
    {
        return asset($this->file_path);
    }
}
