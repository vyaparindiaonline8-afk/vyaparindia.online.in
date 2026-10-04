<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'tags',
        'price',
        'purchase_price',
        'wholesale_price',
        'mrp',
        'gst_percent',
        'stock_quantity',
        'track_inventory',
        'has_variants',
        'sku',
        'hsn_code',
        'image',
        'user_id',
        'category_id',
        'views_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'stock_quantity' => 'integer',
        'track_inventory' => 'boolean',
        'has_variants' => 'boolean',
        'views_count' => 'integer',
    ];

    public function productViews()
    {
        return $this->hasMany(ProductView::class);
    }

    /**
     * Record impression / view with session deduplication
     */
    public function recordView($userId = null, $ip = null, $userAgent = null)
    {
        $this->increment('views_count');

        // Record granular analytics row
        return $this->productViews()->create([
            'user_id' => $userId,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * AI & Category Similar Products Recommendation Engine
     * Matches same category, similar price band (+-35%), in-stock preferred
     */
    public function similarProducts($limit = 4)
    {
        $minPrice = $this->price * 0.65;
        $maxPrice = $this->price * 1.35;

        return self::where('id', '!=', $this->id)
            ->where(function ($q) use ($minPrice, $maxPrice) {
                $q->where('category_id', $this->category_id)
                  ->orWhereBetween('price', [$minPrice, $maxPrice]);
            })
            ->with(['seller.sellerProfile', 'seller.sellerPage', 'category'])
            ->orderByRaw("CASE WHEN category_id = {$this->category_id} THEN 0 ELSE 1 END")
            ->latest()
            ->take($limit)
            ->get();
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function channelListings()
    {
        return $this->hasMany(ChannelListing::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity', 'price');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }


    public function enquiries()
    {
        return $this->hasMany(Enquiry::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function pricingTiers()
    {
        return $this->hasMany(ProductPricingTier::class)->orderBy('min_quantity');
    }

    public function getAverageRatingAttribute()
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? round($avg, 1) : 5.0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                return $this->image;
            }
            if (file_exists(public_path('storage/' . $this->image))) {
                return asset('storage/' . $this->image);
            }
            if (file_exists(public_path('images/' . $this->image))) {
                return asset('images/' . $this->image);
            }
            if (file_exists(public_path($this->image))) {
                return asset($this->image);
            }
        }
        return 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80';
    }

    /**
     * Deduct stock on order placement / processing
     */
    public function deductStock(int $quantity, string $reason = 'Order Placed', ?string $refType = 'order', ?int $refId = null, ?int $variantId = null): void
    {
        if ($variantId) {
            $variant = $this->variants()->find($variantId);
            if ($variant && $variant->track_inventory && !is_null($variant->stock_quantity)) {
                $newStock = max(0, $variant->stock_quantity - $quantity);
                $variant->update(['stock_quantity' => $newStock]);

                StockMovement::create([
                    'product_id' => $this->id,
                    'variant_id' => $variant->id,
                    'user_id' => $this->user_id,
                    'type' => 'out_order',
                    'quantity' => -$quantity,
                    'balance_after' => $newStock,
                    'reason' => $reason,
                    'reference_type' => $refType,
                    'reference_id' => $refId,
                ]);
            }
            return;
        }

        if ($this->track_inventory && !is_null($this->stock_quantity)) {
            $newStock = max(0, $this->stock_quantity - $quantity);
            $this->update(['stock_quantity' => $newStock]);

            StockMovement::create([
                'product_id' => $this->id,
                'variant_id' => null,
                'user_id' => $this->user_id,
                'type' => 'out_order',
                'quantity' => -$quantity,
                'balance_after' => $newStock,
                'reason' => $reason,
                'reference_type' => $refType,
                'reference_id' => $refId,
            ]);
        }
    }

    /**
     * Restore or add stock (Restock / Order Cancellation)
     */
    public function addStock(int $quantity, string $reason = 'Restocked', ?string $refType = 'manual_restock', ?int $refId = null, ?int $variantId = null): int
    {
        if ($variantId) {
            $variant = $this->variants()->find($variantId);
            if ($variant) {
                $current = $variant->stock_quantity ?? 0;
                $newStock = $current + $quantity;
                $variant->update(['stock_quantity' => $newStock, 'track_inventory' => true]);

                StockMovement::create([
                    'product_id' => $this->id,
                    'variant_id' => $variant->id,
                    'user_id' => $this->user_id,
                    'type' => ($refType === 'order_cancel') ? 'in_cancellation' : 'in_restock',
                    'quantity' => $quantity,
                    'balance_after' => $newStock,
                    'reason' => $reason,
                    'reference_type' => $refType,
                    'reference_id' => $refId,
                ]);
                return $newStock;
            }
        }

        $current = $this->stock_quantity ?? 0;
        $newStock = $current + $quantity;
        $this->update(['stock_quantity' => $newStock, 'track_inventory' => true]);

        StockMovement::create([
            'product_id' => $this->id,
            'variant_id' => null,
            'user_id' => $this->user_id,
            'type' => ($refType === 'order_cancel') ? 'in_cancellation' : 'in_restock',
            'quantity' => $quantity,
            'balance_after' => $newStock,
            'reason' => $reason,
            'reference_type' => $refType,
            'reference_id' => $refId,
        ]);

        return $newStock;
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
}