<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatalogIngestionJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'filename',
        'file_path',
        'status',
        'total_products_detected',
        'extracted_data',
        'error_message',
    ];

    protected $casts = [
        'extracted_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}