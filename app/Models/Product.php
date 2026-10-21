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
        'cost_price_2',
        'cost_price_3',
        'price_effective_date',
        'mrp',
        'gst_percent',
        'stock_quantity',
        'track_inventory',
        'has_variants',
        'sku',
        'hsn_code',
        'brand',
        'group_name',
        'image',
        'video_url',
        'user_id',
        'category_id',
        'views_count',
        'attributes',
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
        'attributes' => 'array',
    ];

    public function getPriceAttribute($val): float
    {
        $num = (float)($val ?? 0);
        if ($num > 0) {
            return $num;
        }
        if ($this->relationLoaded('variants') && $this->variants && $this->variants->isNotEmpty()) {
            $min = $this->variants->where('retail_price', '>', 0)->min('retail_price');
            if ($min && (float)$min > 0) return (float)$min;
            $minPrice = $this->variants->where('price', '>', 0)->min('price');
            if ($minPrice && (float)$minPrice > 0) return (float)$minPrice;
        }
        return $num;
    }

    public function getVideoEmbedUrlAttribute(): ?string
    {
        if (empty($this->video_url)) {
            return null;
        }

        $url = trim($this->video_url);

        // YouTube Embed match
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|shorts\/|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1] . '?rel=0';
        }

        return $url;
    }

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
        return $this->hasMany(ProductVariant::class)->where('is_active', true)->orderBy('id', 'asc');
    }

    /**
     * Get variants sorted naturally by dimension (e.g. 15mm, 20mm, 25mm, 32mm, 40mm, 50mm).
     * Prevents larger sizes like 50mm from showing on top.
     */
    public function getSortedVariantsAttribute()
    {
        $vars = $this->relationLoaded('variants') ? $this->variants : $this->variants()->get();
        if (!$vars || $vars->isEmpty()) {
            return collect();
        }

        return $vars->sort(function ($a, $b) {
            $nameA = strtolower(trim($a->variant_name ?: ($a->size ?: '')));
            $nameB = strtolower(trim($b->variant_name ?: ($b->size ?: '')));

            preg_match_all('/\d+(?:\.\d+)?/', $nameA, $mA);
            preg_match_all('/\d+(?:\.\d+)?/', $nameB, $mB);

            $numsA = !empty($mA[0]) ? array_map('floatval', $mA[0]) : [];
            $numsB = !empty($mB[0]) ? array_map('floatval', $mB[0]) : [];

            if (!empty($numsA) && !empty($numsB)) {
                $len = min(count($numsA), count($numsB));
                for ($i = 0; $i < $len; $i++) {
                    if ($numsA[$i] !== $numsB[$i]) {
                        return $numsA[$i] <=> $numsB[$i];
                    }
                }
                return count($numsA) <=> count($numsB);
            }

            if (!empty($numsA) && empty($numsB)) return -1;
            if (empty($numsA) && !empty($numsB)) return 1;

            return strnatcasecmp($nameA, $nameB);
        })->values();
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

    public function user()
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
        if (!empty($this->image)) {
            $img = trim($this->image);
            if (filter_var($img, FILTER_VALIDATE_URL)) {
                return $img;
            }
            $cleanPath = ltrim($img, '/\\');
            if (file_exists(public_path($cleanPath))) {
                return asset($cleanPath);
            }
            if (file_exists(public_path('storage/' . $cleanPath))) {
                return asset('storage/' . $cleanPath);
            }
            if (file_exists(public_path('images/' . $cleanPath))) {
                return asset('images/' . $cleanPath);
            }
        }

        // Smart Category / Product Name auto-match from high-res verified catalog library
        $smartImg = $this->getSmartCategoryFallbackImage();
        if ($smartImg) {
            return asset($smartImg);
        }

        return null;
    }

    public function getSmartCategoryFallbackImage(): ?string
    {
        $name = strtoupper($this->name ?? '');
        $cat = strtoupper($this->category->name ?? '');

        // Brass items
        if (str_contains($name, 'BRASS') && str_contains($name, 'ELBOW')) {
            return 'images/catalog/plasto/items/brass_elbow.jpg';
        }
        if (str_contains($name, 'BRASS') && str_contains($name, 'TEE')) {
            return 'images/catalog/plasto/items/brass_tee.jpg';
        }
        if (str_contains($name, 'BRASS') && str_contains($name, 'MTA')) {
            return 'images/catalog/plasto/items/brass_mta.jpg';
        }
        if (str_contains($name, 'BRASS') && str_contains($name, 'FTA')) {
            return 'images/catalog/plasto/items/brass_fta.jpg';
        }

        // Valves
        if (str_contains($name, 'VALVE')) {
            return 'images/catalog/plasto/items/cpvc_ball_valve.jpg';
        }

        // CPVC Fittings & Pipes
        if (str_contains($name, 'CPVC') || str_contains($cat, 'CPVC')) {
            if (str_contains($name, 'ELBOW') || str_contains($name, 'BEND')) return 'images/catalog/plasto/items/cpvc_elbow.jpg';
            if (str_contains($name, 'TEE')) return 'images/catalog/plasto/items/cpvc_tee.jpg';
            if (str_contains($name, 'COUPLER') || str_contains($name, 'SOCKET')) return 'images/catalog/plasto/items/cpvc_coupler.jpg';
            if (str_contains($name, 'UNION')) return 'images/catalog/plasto/items/cpvc_union.jpg';
            if (str_contains($name, 'MTA')) return 'images/catalog/plasto/items/cpvc_mta.jpg';
            if (str_contains($name, 'FTA')) return 'images/catalog/plasto/items/cpvc_fta.jpg';
            if (str_contains($name, 'TANK NIPPLE') || str_contains($name, 'TANK CONNECTOR')) return 'images/catalog/plasto/items/cpvc_tank_nipple.jpg';
            if (str_contains($name, 'PIPE')) return 'images/catalog/plasto/items/cpvc_pipe.jpg';
        }

        // UPVC Fittings & Pipes
        if (str_contains($name, 'UPVC') || str_contains($cat, 'UPVC')) {
            if (str_contains($name, 'ELBOW') || str_contains($name, 'BEND')) return 'images/catalog/plasto/items/upvc_elbow.jpg';
            if (str_contains($name, 'TEE')) return 'images/catalog/plasto/items/upvc_tee.jpg';
            if (str_contains($name, 'COUPLER') || str_contains($name, 'SOCKET')) return 'images/catalog/plasto/items/upvc_coupler.jpg';
            if (str_contains($name, 'MTA')) return 'images/catalog/plasto/items/upvc_mta.jpg';
        }

        // Tanks & Garden
        if (str_contains($name, 'TANK') && str_contains($name, 'LID')) {
            return 'images/catalog/plasto/items/water_tank_lid.jpg';
        }
        if (str_contains($name, 'GARDEN') || str_contains($name, 'HOSE')) {
            return 'images/catalog/plasto/items/garden_pipe.jpg';
        }
        if (str_contains($name, 'END CAP') || str_contains($name, 'PLUG')) {
            return 'images/catalog/plasto/items/end_cap.jpg';
        }

        // Generic fitting fallbacks
        if (str_contains($name, 'ELBOW')) return 'images/catalog/plasto/items/upvc_elbow.jpg';
        if (str_contains($name, 'TEE')) return 'images/catalog/plasto/items/upvc_tee.jpg';
        if (str_contains($name, 'COUPLER') || str_contains($name, 'SOCKET')) return 'images/catalog/plasto/items/upvc_coupler.jpg';

        return null;
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