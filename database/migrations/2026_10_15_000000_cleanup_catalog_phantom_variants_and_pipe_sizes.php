<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Product;
use App\Models\ProductVariant;

return new class extends Migration
{
    /**
     * Run the migrations to purge duplicate phantom variants and correct pipe diameters.
     */
    public function up(): void
    {
        // 1. Correct Pipe variant sizes from "10 FEET" to actual diameters (15MM..50MM)
        $pipeMap = [
            'UPLASTO 1' => '15MM',
            'UPLASTO 2' => '20MM',
            'UPLASTO 3' => '25MM',
            'UPLASTO 4' => '32MM',
            'UPLASTO 5' => '40MM',
            'UPLASTO 6' => '50MM',
            'UPLASTO 7' => '15MM',
            'UPLASTO 8' => '20MM',
            'UPLASTO 9' => '25MM',
            'UPLASTO 10' => '32MM',
            'UPLASTO 11' => '40MM',
            'UPLASTO 12' => '50MM',
        ];

        foreach ($pipeMap as $sku => $correctSize) {
            ProductVariant::where('sku', $sku)->update([
                'variant_name' => $correctSize,
                'attributes' => ['size' => $correctSize],
            ]);
        }

        // 2. Remove phantom duplicate variants (SKU LIKE 'VAR-%') on cards that already have real variants
        try {
            $cards = Product::with('variants')->get();
            foreach ($cards as $product) {
                if (!$product->variants || $product->variants->count() <= 1) {
                    continue;
                }

                $realVars = $product->variants->reject(function ($v) {
                    return str_starts_with($v->sku ?? '', 'VAR-');
                });

                // If card has real variants, any VAR- variant is a duplicate phantom and should be purged
                if ($realVars->isNotEmpty()) {
                    $phantomIds = $product->variants->filter(function ($v) {
                        return str_starts_with($v->sku ?? '', 'VAR-');
                    })->pluck('id');

                    if ($phantomIds->isNotEmpty()) {
                        ProductVariant::whereIn('id', $phantomIds)->delete();
                    }
                }

                // Also check for duplicate variant names within the same card
                $product->refresh();
                $seenSizes = [];
                foreach ($product->variants()->orderBy('id')->get() as $v) {
                    $cleanSize = strtolower(trim($v->variant_name ?? ''));
                    if (empty($cleanSize)) {
                        continue;
                    }
                    if (isset($seenSizes[$cleanSize])) {
                        // Duplicate variant for same size! Delete the duplicate
                        $v->delete();
                    } else {
                        $seenSizes[$cleanSize] = $v->id;
                    }
                }

                // Recalculate base card prices
                $product->refresh();
                if ($product->variants && $product->variants->isNotEmpty()) {
                    $minPrice = $product->variants->where('retail_price', '>', 0)->min('retail_price') ?: $product->price;
                    $minMrp = $product->variants->where('mrp', '>', 0)->min('mrp') ?: $product->mrp;
                    $minPurchase = $product->variants->where('purchase_price', '>', 0)->min('purchase_price') ?: $product->purchase_price;
                    $totalStock = $product->variants->sum('stock_quantity');

                    $product->update([
                        'price' => $minPrice,
                        'mrp' => $minMrp,
                        'purchase_price' => $minPurchase,
                        'stock_quantity' => $totalStock > 0 ? $totalStock : $product->stock_quantity,
                        'has_variants' => true,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Cleanup migration warning: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
