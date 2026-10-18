<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix MTA, FTA, and Reducer variant sizes, merge fragmented cards, and purge duplicate phantoms.
     */
    public function up(): void
    {
        // 1. Correct MTA & FTA Reducer variant sizes from SKU codes
        $sizeMap = [
            'UPLASTO 97'  => '20*15',
            'UPLASTO 98'  => '25*20',
            'UPLASTO 99'  => '25*15',
            'UPLASTO 106' => '20*15',
            'UPLASTO 107' => '25*15',
            'cpvc67'      => '25*20',
            'cpvc68'      => '25*15',
            'cpvc69'      => '20*15',
            'cpvc70'      => '32*25',
            'cpvc71'      => '40*25',
            'cpvc72'      => '20*15',
            'cpvc73'      => '25*20',
            'cpvc74'      => '25*15',
            'cpvc75'      => '32*25',
            'cpvc76'      => '40*25',
            'cpvc20'      => '40MM',
        ];

        foreach ($sizeMap as $sku => $correctSize) {
            $updateData = [
                'variant_name' => $correctSize,
                'attributes'   => json_encode(['size' => $correctSize]),
            ];
            if (\Illuminate\Support\Facades\Schema::hasColumn('product_variants', 'size')) {
                $updateData['size'] = $correctSize;
            }
            ProductVariant::whereRaw('LOWER(TRIM(sku)) = ?', [strtolower(trim($sku))])
                ->update($updateData);
        }

        // 2. Merge fragmented cards (e.g. "BRASS REDUSER FTA25*20" into "BRASS REDUSER FTA")
        try {
            $userProducts = Product::with('variants')->get();

            // Find primary parent cards for Reducer FTA & MTA
            $primaryFta = $userProducts->first(function ($p) {
                $n = strtolower(trim($p->name));
                return (str_contains($n, 'brass') && (str_contains($n, 'reducer') || str_contains($n, 'reduser')) && str_contains($n, 'fta'))
                    && !str_contains($n, '25') && !str_contains($n, '20') && !str_contains($n, '15');
            });

            $primaryMta = $userProducts->first(function ($p) {
                $n = strtolower(trim($p->name));
                return (str_contains($n, 'brass') && (str_contains($n, 'reducer') || str_contains($n, 'reduser')) && str_contains($n, 'mta'))
                    && !str_contains($n, '25') && !str_contains($n, '20') && !str_contains($n, '15');
            });

            foreach ($userProducts as $p) {
                $n = strtolower(trim($p->name));
                $isFragmentFta = (str_contains($n, 'brass') && (str_contains($n, 'reducer') || str_contains($n, 'reduser')) && str_contains($n, 'fta'))
                    && (str_contains($n, '25*20') || str_contains($n, '25*15') || str_contains($n, '20*15') || str_contains($n, 'fta25'));

                $isFragmentMta = (str_contains($n, 'brass') && (str_contains($n, 'reducer') || str_contains($n, 'reduser')) && str_contains($n, 'mta'))
                    && (str_contains($n, '25*20') || str_contains($n, '25*15') || str_contains($n, '20*15') || str_contains($n, 'mta25'));

                if ($isFragmentFta && $primaryFta && $p->id !== $primaryFta->id) {
                    ProductVariant::where('product_id', $p->id)->update(['product_id' => $primaryFta->id]);
                    $p->delete();
                } elseif ($isFragmentMta && $primaryMta && $p->id !== $primaryMta->id) {
                    ProductVariant::where('product_id', $p->id)->update(['product_id' => $primaryMta->id]);
                    $p->delete();
                }
            }

            // 3. Purge duplicate variants within the same product (keep lowest ID)
            $allCards = Product::with('variants')->get();
            foreach ($allCards as $prod) {
                if ($prod->variants && $prod->variants->count() > 1) {
                    $seenNames = [];
                    foreach ($prod->variants as $v) {
                        $vSize = is_array($v->attributes) ? ($v->attributes['size'] ?? '') : '';
                        $vKey = strtolower(trim($v->variant_name ?: $vSize));
                        if (isset($seenNames[$vKey])) {
                            // Delete duplicate
                            $v->delete();
                        } else {
                            $seenNames[$vKey] = true;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Migration cleanup error: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
