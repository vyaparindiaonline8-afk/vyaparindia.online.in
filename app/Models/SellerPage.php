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

    public function getBannerImageUrlAttribute()
    {
        return $this->banner_url;
    }

    public function getBannerSlidesAttribute()
    {
        $settings = $this->business_settings ?? [];
        if (is_string($settings)) {
            $settings = json_decode($settings, true) ?: [];
        }

        $slides = $settings['banner_slides'] ?? [];
        if (!empty($slides) && is_array($slides)) {
            return array_values(array_filter($slides));
        }

        if (!empty($this->banner_url)) {
            return [$this->banner_url];
        }

        return [
            'https://images.unsplash.com/photo-1581094794329-c8112a89af12?auto=format&fit=crop&w=1600&q=80',
            'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=1600&q=80',
            'https://images.unsplash.com/photo-1620626011761-996317b8d101?auto=format&fit=crop&w=1600&q=80',
            'https://images.unsplash.com/photo-1507652313519-d4e9174996dd?auto=format&fit=crop&w=1600&q=80',
        ];
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
        return $this->business_settings['dispatch_radius'] ?? ($this->user?->sellerProfile?->dispatch_radius ?? 500);
    }

    public function getShopPhotosAttribute()
    {
        return $this->business_settings['shop_photos'] ?? [];
    }

    public function getStateAttribute()
    {
        return $this->business_settings['state'] ?? ($this->user?->sellerProfile?->state ?? '');
    }

    public function getLandmarkAttribute()
    {
        return $this->business_settings['landmark'] ?? '';
    }

    public function getLatitudeAttribute()
    {
        return $this->business_settings['latitude'] ?? null;
    }

    public function getLongitudeAttribute()
    {
        return $this->business_settings['longitude'] ?? null;
    }

    public function getBusinessCategoryCustomAttribute()
    {
        return $this->business_settings['business_category_custom'] ?? '';
    }

    public function getDealershipTextAttribute()
    {
        $val = $this->business_settings['dealership_text'] ?? null;
        if (!empty($val)) return $val;
        return $this->attributes['dealership_text'] ?? '';
    }

    public function getWhatsappNumberAttribute($value)
    {
        if (!empty($value)) return $value;
        return $this->user?->sellerProfile?->whatsapp_number ?? ($this->user?->mobile_number ?? '');
    }

    public function getSupportPhoneAttribute($value)
    {
        if (!empty($value)) return $value;
        return $this->user?->sellerProfile?->office_phone ?? ($this->user?->mobile_number ?? '');
    }

    public function getSupportEmailAttribute($value)
    {
        if (!empty($value)) return $value;
        return $this->user?->email ?? '';
    }

    public function getAddressAttribute($value)
    {
        if (!empty($value)) return $value;
        return $this->user?->sellerProfile?->address ?? '';
    }

    public function getCityAttribute($value)
    {
        if (!empty($value)) return $value;
        return $this->user?->sellerProfile?->city ?? '';
    }

    public function getPincodeAttribute($value)
    {
        if (!empty($value)) return $value;
        return $this->user?->sellerProfile?->pincode ?? '';
    }

    public function getGoogleMapLinkAttribute($value)
    {
        if (!empty($value)) return $value;
        return $this->business_settings['google_map_link'] ?? ($this->user?->sellerProfile?->google_map_url ?? '');
    }

    public function getGoogleReviewLinkAttribute($value)
    {
        if (!empty($value)) return $value;
        return $this->business_settings['google_review_link'] ?? ($this->user?->sellerProfile?->google_business_url ?? '');
    }

    public function getInstagramLinkAttribute($value)
    {
        if (!empty($value)) return $value;
        return $this->business_settings['instagram_link'] ?? ($this->user?->sellerProfile?->instagram_url ?? '');
    }

    public function getFacebookLinkAttribute($value)
    {
        if (!empty($value)) return $value;
        return $this->business_settings['facebook_link'] ?? ($this->user?->sellerProfile?->facebook_url ?? '');
    }

    public function getYoutubeLinkAttribute($value)
    {
        if (!empty($value)) return $value;
        return $this->business_settings['youtube_link'] ?? ($this->user?->sellerProfile?->youtube_url ?? '');
    }

    public function getUpiIdAttribute($value)
    {
        if (!empty($value)) return $value;
        return $this->user?->sellerProfile?->upi_id ?? '';
    }

    public function getBankNameAttribute($value)
    {
        if (!empty($value)) return $value;
        return $this->user?->sellerProfile?->bank_name ?? '';
    }

    public function getBankAccountNumberAttribute($value)
    {
        if (!empty($value)) return $value;
        return $this->user?->sellerProfile?->bank_account_number ?? '';
    }

    public function getBankIfscAttribute($value)
    {
        if (!empty($value)) return $value;
        return $this->user?->sellerProfile?->bank_ifsc ?? '';
    }

    public function getBankAccountHolderAttribute($value)
    {
        if (!empty($value)) return $value;
        return $this->user?->sellerProfile?->bank_account_holder ?? '';
    }

    public function getProjectPhotosAttribute()
    {
        return $this->business_settings['project_photos'] ?? [];
    }

    public function getServicedCitiesAttribute()
    {
        return $this->business_settings['serviced_cities'] ?? 'सारंगढ़, रायगढ़, बिलासपुर, बरमकेला, सरिया, चंद्रपुर, रायपुर';
    }

    public function getServicedPincodesAttribute()
    {
        return $this->business_settings['serviced_pincodes'] ?? '496445, 496001, 495001, 492001';
    }

    public function getFoodServiceModesAttribute(): array
    {
        $settings = $this->business_settings ?? [];
        return $settings['food_service_modes'] ?? [
            'dine_in' => true,
            'takeaway' => true,
            'delivery' => true,
            'tables_count' => 10,
            'packaging_charge' => 0,
            'min_order_delivery' => 0,
        ];
    }

    public function isDineInActive(): bool
    {
        return !empty($this->food_service_modes['dine_in']);
    }

    public function isTakeawayActive(): bool
    {
        return !empty($this->food_service_modes['takeaway']);
    }

    public function isDeliveryActive(): bool
    {
        return !empty($this->food_service_modes['delivery']);
    }
}
