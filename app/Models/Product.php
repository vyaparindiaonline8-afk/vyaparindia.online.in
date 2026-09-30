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
    ];

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

    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
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
        return $avg ? round($avg, 1) : 4.8;
    }

    public function getReviewsCountAttribute()
    {
        $cnt = $this->reviews()->count();
        return $cnt > 0 ? $cnt : rand(45, 180);
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
}