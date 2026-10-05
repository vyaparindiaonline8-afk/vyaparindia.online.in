<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slug',
        'page_title',
        'logo',
        'banner_image',
        'tagline',
        'welcome_message',
        'about_text',
        'whatsapp_number',
        'support_phone',
        'support_email',
        'instagram_link',
        'facebook_link',
        'youtube_link',
        'google_review_link',
        'google_map_link',
        'address',
        'city',
        'pincode',
        'theme_color',
        'theme_style',
        'currency',
        'enable_cod',
        'enable_whatsapp_order',
        'upi_id',
        'bank_name',
        'bank_account_number',
        'bank_ifsc',
        'bank_account_holder',
        'show_payment_details_to_buyer',
        'policies',
        'authorized_brands',
        'business_type',
        'visits_count',
        'show_last_updated_to_buyers',
        'business_settings',
    ];

    protected $casts = [
        'enable_cod' => 'boolean',
        'enable_whatsapp_order' => 'boolean',
        'show_payment_details_to_buyer' => 'boolean',
        'show_last_updated_to_buyers' => 'boolean',
        'authorized_brands' => 'array',
        'business_settings' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, User::class, 'id', 'user_id', 'user_id', 'id');
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            if (filter_var($this->logo, FILTER_VALIDATE_URL)) {
                return $this->logo;
            }
            if (file_exists(public_path('storage/' . $this->logo))) {
                return asset('storage/' . $this->logo);
            }
            if (file_exists(public_path($this->logo))) {
                return asset($this->logo);
            }
        }
        return null;
    }

    public function getBannerUrlAttribute()
    {
        if ($this->banner_image) {
            if (filter_var($this->banner_image, FILTER_VALIDATE_URL)) {
                return $this->banner_image;
            }
            if (file_exists(public_path('storage/' . $this->banner_image))) {
                return asset('storage/' . $this->banner_image);
            }
            if (file_exists(public_path($this->banner_image))) {
                return asset($this->banner_image);
            }
        }
        return null;
    }

    public function getCleanWhatsappNumberAttribute()
    {
        $num = preg_replace('/[^0-9]/', '', $this->whatsapp_number ?? '');
        if (strlen($num) === 10) {
            $num = '91' . $num;
        }
        return $num;
    }

    public function getDispatchRadiusAttribute()
    {
        return $this->user?->sellerProfile?->dispatch_radius;
    }
}
