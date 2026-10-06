<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'business_tier',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function is_admin()
    {
        return (int)$this->role_id === 1 || ($this->role && $this->role->name === 'admin');
    }

    public function is_seller()
    {
        return (int)$this->role_id === 2 
            || ($this->role && $this->role->name === 'seller')
            || $this->sellerProfile()->exists()
            || $this->sellerPage()->exists();
    }

    public function is_buyer()
    {
        return (int)$this->role_id === 3 || ($this->role && $this->role->name === 'buyer');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function enquiries()
    {
        return $this->hasMany(Enquiry::class);
    }

    public function sellerProfile()
    {
        return $this->hasOne(SellerProfile::class);
    }

    public function sellerPage()
    {
        return $this->hasOne(SellerPage::class);
    }

    public function sentPartnershipRequests()
    {
        return $this->hasMany(DropshipPartnership::class, 'retailer_id');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlists');
    }

    public function hasWishlisted(Product $product)
    {
        return $this->wishlists()->where('product_id', $product->id)->exists();
    }

    public function receivedPartnershipRequests()
    {
        return $this->hasMany(DropshipPartnership::class, 'wholesaler_id');
    }

    /**
     * Check if user is on Profile-Only Tier (Capped at 50 products)
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Check if user is eligible to add another product
     * Promotional Rule: First 1,000 sellers get free mini-website with up to 100 products free!
     * Premium subscription required only for > 100 products.
     */
    public function canAddProduct(): bool
    {
        // 1. If active paid subscription exists, unlimited products
        if ($this->subscriptions()->where('status', 'active')->where('end_date', '>=', now())->exists()) {
            return true;
        }

        // 2. Promotional Tier: Up to 100 products free for mini-website owners
        if ($this->sellerPage()->exists()) {
            return $this->products()->count() < 100;
        }

        // 3. Profile-only tier: 50 products free
        return $this->products()->count() < 50;
    }
}

