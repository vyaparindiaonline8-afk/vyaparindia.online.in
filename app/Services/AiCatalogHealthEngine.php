<?php

namespace App\Services;

use App\Models\CatalogHealthAudit;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SellerPage;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AiCatalogHealthEngine
{
    /**
     * Audit a seller's entire catalog and generate/update health report.
     *
     * @param int $userId
     * @param bool $force Force re-computation even if recent audit exists
     * @return CatalogHealthAudit
     */
    public function auditSeller(int $userId, bool $force = false): CatalogHealthAudit
    {
        $existing = CatalogHealthAudit::where('user_id', $userId)->first();

        // If an audit was performed within the last 4 hours and not forced, return cached
        if (!$force && $existing && $existing->audited_at && $existing->audited_at->gt(now()->subHours(4))) {
            return $existing;
        }

        $sellerPage = SellerPage::where('user_id', $userId)->first();
        $sellerProfile = SellerProfile::where('user_id', $userId)->first();

        $products = Product::where('user_id', $userId)
            ->with(['variants', 'images', 'category'])
            ->get();

        $totalProducts = $products->count();

        if ($totalProducts === 0) {
            $emptyAudit = [
                'health_score' => 20,
                'breakdown' => [
                    'images_score' => 0,
                    'stock_score' => 0,
                    'pricing_score' => 0,
                    'content_score' => 0,
                    'profile_score' => $this->evaluateProfileScore($sellerPage, $sellerProfile),
                ],
                'stats' => [
                    'total_products' => 0,
                    'in_stock_count' => 0,
                    'out_of_stock_count' => 0,
                    'untracked_count' => 0,
                    'missing_images_count' => 0,
                    'single_image_count' => 0,
                    'multi_images_count' => 0,
                    'missing_prices_count' => 0,
                    'total_stock_units' => 0,
                    'total_stock_value' => 0,
                ],
                'recommendations' => [
                    [
                        'id' => 'no_products',
                        'priority' => 'critical',
                        'title' => 'कैटलॉग में प्रोडक्ट्स जोड़ें',
                        'points_gain' => 80,
                        'description' => 'आपकी दुकान में अभी कोई प्रोडक्ट नहीं है। एक्सेल, पीडीएफ या फोटो स्टूडियो से प्रोडक्ट्स जोड़ें।',
                        'action_label' => 'प्रोडक्ट जोड़ें',
                        'action_url' => route('seller.products.create'),
                    ],
                ],
                'products_audit' => [],
                'audited_at' => now(),
            ];

            return CatalogHealthAudit::updateOrCreate(
                ['user_id' => $userId],
                array_merge($emptyAudit, ['seller_page_id' => $sellerPage?->id])
            );
        }

        // Metrics aggregation
        $productsAudit = [];
        $totalStockUnits = 0;
        $totalStockValue = 0.0;
        $inStockCount = 0;
        $outOfStockCount = 0;
        $untrackedCount = 0;
        $missingImagesCount = 0;
        $singleImageCount = 0;
        $multiImagesCount = 0;
        $missingPricesCount = 0;

        $sumImageScore = 0;
        $sumStockScore = 0;
        $sumPricingScore = 0;
        $sumContentScore = 0;

        foreach ($products as $prod) {
            $analysis = $this->analyzeProduct($prod);
            $productsAudit[] = $analysis;

            $totalStockUnits += $analysis['stock_quantity'];
            $totalStockValue += ($analysis['stock_quantity'] * $analysis['selling_price']);

            if ($analysis['stock_status'] === 'in_stock' || $analysis['stock_status'] === 'low_stock') {
                $inStockCount++;
            } elseif ($analysis['stock_status'] === 'out_of_stock') {
                $outOfStockCount++;
            } else {
                $untrackedCount++;
            }

            if ($analysis['image_count'] === 0) {
                $missingImagesCount++;
            } elseif ($analysis['image_count'] === 1) {
                $singleImageCount++;
            } else {
                $multiImagesCount++;
            }

            if ($analysis['selling_price'] <= 0) {
                $missingPricesCount++;
            }

            $sumImageScore += $analysis['score_images'];
            $sumStockScore += $analysis['score_stock'];
            $sumPricingScore += $analysis['score_pricing'];
            $sumContentScore += $analysis['score_content'];
        }

        // Sub scores averaged out of 25 each
        $avgImageScore = round($sumImageScore / $totalProducts);
        $avgStockScore = round($sumStockScore / $totalProducts);
        $avgPricingScore = round($sumPricingScore / $totalProducts);
        $avgContentScore = round($sumContentScore / $totalProducts);

        $profileScore = $this->evaluateProfileScore($sellerPage, $sellerProfile);

        // Overall score out of 100: weighted average (90% catalog + 10% profile)
        $catalogRawScore = $avgImageScore + $avgStockScore + $avgPricingScore + $avgContentScore;
        $finalScore = min(100, max(5, round(($catalogRawScore * 0.90) + ($profileScore * 0.10))));

        // Generate Actionable Recommendations ("100 अंक पाने के लिए क्या करें")
        $recommendations = $this->generateRecommendations([
            'total_products' => $totalProducts,
            'missing_images_count' => $missingImagesCount,
            'single_image_count' => $singleImageCount,
            'out_of_stock_count' => $outOfStockCount,
            'untracked_count' => $untrackedCount,
            'missing_prices_count' => $missingPricesCount,
            'avg_content_score' => $avgContentScore,
            'profile_score' => $profileScore,
            'current_score' => $finalScore,
        ]);

        $auditData = [
            'user_id' => $userId,
            'seller_page_id' => $sellerPage?->id,
            'health_score' => $finalScore,
            'breakdown' => [
                'images_score' => $avgImageScore,
                'stock_score' => $avgStockScore,
                'pricing_score' => $avgPricingScore,
                'content_score' => $avgContentScore,
                'profile_score' => $profileScore,
            ],
            'stats' => [
                'total_products' => $totalProducts,
                'in_stock_count' => $inStockCount,
                'out_of_stock_count' => $outOfStockCount,
                'untracked_count' => $untrackedCount,
                'missing_images_count' => $missingImagesCount,
                'single_image_count' => $singleImageCount,
                'multi_images_count' => $multiImagesCount,
                'missing_prices_count' => $missingPricesCount,
                'total_stock_units' => $totalStockUnits,
                'total_stock_value' => round($totalStockValue, 2),
            ],
            'recommendations' => $recommendations,
            'products_audit' => $productsAudit,
            'audited_at' => now(),
        ];

        $auditRecord = CatalogHealthAudit::updateOrCreate(
            ['user_id' => $userId],
            $auditData
        );

        // Sync quick score cache to SellerPage for instantaneous dashboard displays
        if ($sellerPage) {
            $settings = is_array($sellerPage->business_settings) ? $sellerPage->business_settings : [];
            $settings['catalog_health_score'] = $finalScore;
            $settings['catalog_health_audited_at'] = now()->toIso8601String();
            $settings['catalog_health_summary'] = [
                'score' => $finalScore,
                'total' => $totalProducts,
                'in_stock' => $inStockCount,
                'missing_images' => $missingImagesCount,
                'missing_prices' => $missingPricesCount,
            ];
            $sellerPage->business_settings = $settings;
            $sellerPage->saveQuietly();
        }

        return $auditRecord;
    }

    /**
     * Deep inspection of a single product card & its variants.
     */
    public function analyzeProduct(Product $product): array
    {
        $hasVariants = $product->has_variants && $product->variants && $product->variants->isNotEmpty();

        // 1. Stock / Quantity Inspection
        $stockQuantity = 0;
        $trackInventory = (bool)$product->track_inventory;
        $variantCount = 0;

        if ($hasVariants) {
            $variantCount = $product->variants->count();
            foreach ($product->variants as $var) {
                if ($var->track_inventory) {
                    $trackInventory = true;
                }
                $stockQuantity += intval($var->stock_quantity ?? 0);
            }
        } else {
            $stockQuantity = intval($product->stock_quantity ?? 0);
        }

        $stockStatus = 'untracked';
        if ($trackInventory) {
            if ($stockQuantity <= 0) {
                $stockStatus = 'out_of_stock';
            } elseif ($stockQuantity <= 5) {
                $stockStatus = 'low_stock';
            } else {
                $stockStatus = 'in_stock';
            }
        } else {
            // If inventory not strictly tracked but stock_quantity was given
            if (!is_null($product->stock_quantity) && $product->stock_quantity > 0) {
                $stockStatus = 'in_stock';
            }
        }

        // Score Stock (0 - 25)
        $scoreStock = 0;
        if ($stockStatus === 'in_stock') {
            $scoreStock = 25;
        } elseif ($stockStatus === 'low_stock') {
            $scoreStock = 18;
        } elseif ($stockStatus === 'untracked') {
            $scoreStock = 12; // partial credit
        } else {
            $scoreStock = 0; // out of stock
        }

        // 2. Pricing & MRP Inspection
        $sellingPrice = (float)$product->price;
        $mrp = (float)($product->mrp ?? 0);
        $wholesalePrice = (float)($product->wholesale_price ?? 0);

        if ($hasVariants && $sellingPrice <= 0) {
            $minVarPrice = $product->variants->where('retail_price', '>', 0)->min('retail_price');
            if ($minVarPrice) $sellingPrice = (float)$minVarPrice;
        }

        $discountPercent = 0;
        if ($mrp > 0 && $sellingPrice > 0 && $mrp >= $sellingPrice) {
            $discountPercent = round((($mrp - $sellingPrice) / $mrp) * 100);
        }

        // Score Pricing (0 - 25)
        $scorePricing = 0;
        if ($sellingPrice > 0) {
            $scorePricing += 15;
            if ($mrp > 0 && $mrp >= $sellingPrice) {
                $scorePricing += 10;
            } elseif ($mrp > 0) {
                $scorePricing += 5;
            }
        }

        // 3. Image & Visuals Inspection
        $imageCount = 0;
        $imageType = 'none';
        $primaryImageUrl = null;

        if (!empty($product->image)) {
            $imageCount++;
            $primaryImageUrl = $product->image_url;
            $imageType = 'uploaded';
        }

        if ($product->images && $product->images->isNotEmpty()) {
            $galleryCount = $product->images->whereNotNull('image_path')->count();
            $imageCount += $galleryCount;
            if (!$primaryImageUrl && $product->images->first()) {
                $primaryImageUrl = $product->images->first()->url;
            }
        }

        // Check fallback auto-matched image if no custom image
        if ($imageCount === 0) {
            $fallback = $product->catalog_image_url ?? null;
            if ($fallback) {
                $primaryImageUrl = $fallback;
                $imageType = 'catalog_fallback';
                $imageCount = 1;
            }
        }

        // Score Images (0 - 25)
        $scoreImages = 0;
        if ($imageCount >= 3) {
            $scoreImages = 25;
        } elseif ($imageCount === 2) {
            $scoreImages = 22;
        } elseif ($imageCount === 1) {
            $scoreImages = ($imageType === 'uploaded') ? 18 : 14;
        } else {
            $scoreImages = 0;
        }

        // 4. Content & Listing Completeness (0 - 25)
        $scoreContent = 0;
        $titleLen = mb_strlen(trim($product->name ?? ''));
        if ($titleLen >= 8) {
            $scoreContent += 7;
        } elseif ($titleLen > 0) {
            $scoreContent += 4;
        }

        $descLen = mb_strlen(trim(strip_tags($product->description ?? '')));
        if ($descLen >= 40) {
            $scoreContent += 8;
        } elseif ($descLen >= 10) {
            $scoreContent += 4;
        }

        if ($product->category_id) {
            $scoreContent += 5;
        }

        if (!empty($product->sku) || !empty($product->hsn_code) || !empty($product->brand)) {
            $scoreContent += 5;
        }

        $totalProdScore = min(100, $scoreImages + $scoreStock + $scorePricing + $scoreContent);

        // Issues & Badges
        $issues = [];
        if ($imageCount === 0) {
            $issues[] = ['type' => 'danger', 'code' => 'missing_image', 'text' => '❌ फोटो नहीं है (+18 अंक)', 'impact' => 18];
        } elseif ($imageCount === 1 && $imageType === 'catalog_fallback') {
            $issues[] = ['type' => 'warning', 'code' => 'fallback_image', 'text' => '⚠️ ऑटो कैटलॉग फोटो (असली फोटो लगाएं +7 अंक)', 'impact' => 7];
        }

        if ($sellingPrice <= 0) {
            $issues[] = ['type' => 'danger', 'code' => 'missing_price', 'text' => '❌ सेलिंग रेट ₹0 है (+15 अंक)', 'impact' => 15];
        } elseif ($mrp <= 0) {
            $issues[] = ['type' => 'info', 'code' => 'missing_mrp', 'text' => 'ℹ️ MRP नहीं डली (+10 अंक)', 'impact' => 10];
        }

        if ($stockStatus === 'out_of_stock') {
            $issues[] = ['type' => 'danger', 'code' => 'out_of_stock', 'text' => '❌ स्टॉक शून्य (0) है (+25 अंक)', 'impact' => 25];
        } elseif ($stockStatus === 'untracked') {
            $issues[] = ['type' => 'warning', 'code' => 'untracked_stock', 'text' => '⚠️ क्वांटिटी अनट्रैक्ड है (+13 अंक)', 'impact' => 13];
        } elseif ($stockStatus === 'low_stock') {
            $issues[] = ['type' => 'warning', 'code' => 'low_stock', 'text' => '⚠️ कम स्टॉक (<= 5)', 'impact' => 7];
        }

        if ($descLen < 20) {
            $issues[] = ['type' => 'info', 'code' => 'short_desc', 'text' => 'ℹ️ विवरण (Description) खाली या बहुत छोटा है (+8 अंक)', 'impact' => 8];
        }

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'sku' => $product->sku ?: '—',
            'brand' => $product->brand ?: 'Standard',
            'category' => $product->category?->name ?: 'General',
            'selling_price' => $sellingPrice,
            'mrp' => $mrp,
            'discount_percent' => $discountPercent,
            'stock_quantity' => $stockQuantity,
            'stock_status' => $stockStatus,
            'track_inventory' => $trackInventory,
            'has_variants' => $hasVariants,
            'variant_count' => $variantCount,
            'image_count' => $imageCount,
            'image_type' => $imageType,
            'primary_image_url' => $primaryImageUrl,
            'score_images' => $scoreImages,
            'score_stock' => $scoreStock,
            'score_pricing' => $scorePricing,
            'score_content' => $scoreContent,
            'total_score' => $totalProdScore,
            'issues' => $issues,
            'edit_url' => route('seller.products.edit', $product->id),
        ];
    }

    /**
     * Profile level completeness score (0 to 100).
     */
    private function evaluateProfileScore(?SellerPage $page, ?SellerProfile $profile): int
    {
        $pts = 0;
        if ($page) {
            if (!empty($page->page_title)) $pts += 15;
            if (!empty($page->logo)) $pts += 15;
            if (!empty($page->banner_image)) $pts += 10;
            if (!empty($page->whatsapp_number)) $pts += 15;
            if (!empty($page->google_map_link)) $pts += 15;
            if (!empty($page->upi_id) || !empty($page->bank_account_number)) $pts += 15;
            if (!empty($page->about_text) || !empty($page->tagline)) $pts += 15;
        } elseif ($profile) {
            if (!empty($profile->company_name)) $pts += 20;
            if (!empty($profile->whatsapp_number)) $pts += 20;
            if (!empty($profile->address)) $pts += 20;
            if (!empty($profile->upi_id)) $pts += 20;
            if (!empty($profile->gst_number)) $pts += 20;
        }

        return min(100, $pts);
    }

    /**
     * Generate prioritize roadmap to reach 100/100 score.
     */
    private function generateRecommendations(array $context): array
    {
        $recs = [];
        $neededPoints = 100 - $context['current_score'];

        if ($neededPoints <= 0) {
            $recs[] = [
                'id' => 'perfect',
                'priority' => 'success',
                'title' => 'शानदार! आपका कैटलॉग 100/100 परफेक्ट है 🎉',
                'points_gain' => 0,
                'description' => 'सभी प्रोडक्ट्स में प्रॉपर फोटो, रेट, MRP और स्टॉक डला हुआ है। ग्राहक जब भी स्टोर देखेंगे उन्हें हर जानकारी सटीक मिलेगी!',
                'action_label' => 'कैटलॉग देखें',
                'action_url' => route('seller.products.index'),
            ];
            return $recs;
        }

        // 1. Missing Images
        if ($context['missing_images_count'] > 0) {
            $gain = min(25, round(($context['missing_images_count'] / max(1, $context['total_products'])) * 25));
            $gain = max(5, $gain);
            $recs[] = [
                'id' => 'add_images',
                'priority' => 'critical',
                'title' => "📸 {$context['missing_images_count']} प्रोडक्ट्स में फोटो नहीं है (असली फोटो लगाएं)",
                'points_gain' => $gain,
                'description' => "तस्वीरें देखकर ग्राहक तुरंत फैसला लेते हैं। बिना फोटो वाले प्रोडक्ट्स पर फोटो अपलोड करने से आपके कैटलॉग स्कोर में +{$gain} अंक तुरंत बढ़ेंगे।",
                'action_label' => 'फोटो अपलोड करें',
                'action_url' => route('seller.products.index') . '?filter=no_image',
            ];
        }

        // 2. Out of stock / 0 Quantity
        if ($context['out_of_stock_count'] > 0 || $context['untracked_count'] > 0) {
            $zeroCount = $context['out_of_stock_count'] + $context['untracked_count'];
            $gain = min(25, round(($zeroCount / max(1, $context['total_products'])) * 25));
            $gain = max(5, $gain);
            $recs[] = [
                'id' => 'update_stock',
                'priority' => 'critical',
                'title' => "📦 {$zeroCount} प्रोडक्ट्स में क्वांटिटी (Stock) 0 या खाली है",
                'points_gain' => $gain,
                'description' => "हर आइटम में कितनी पीस/यूनिट उपलब्ध है, यह डालें। इससे ग्राहक को लाइव स्टॉक दिखेगा और आपको +{$gain} अंक मिलेंगे।",
                'action_label' => 'स्टॉक अपडेट करें',
                'action_url' => route('seller.products.index') . '?filter=out_of_stock',
            ];
        }

        // 3. Missing Prices
        if ($context['missing_prices_count'] > 0) {
            $gain = min(25, round(($context['missing_prices_count'] / max(1, $context['total_products'])) * 25));
            $gain = max(5, $gain);
            $recs[] = [
                'id' => 'add_prices',
                'priority' => 'critical',
                'title' => "💰 {$context['missing_prices_count']} प्रोडक्ट्स में विक्रय मूल्य (Sale Price) ₹0 है",
                'points_gain' => $gain,
                'description' => "जिन प्रोडक्ट्स का सेलिंग भाव ₹0 है, उनमें सही रेट डालें ताकि ग्राहक सीधे कार्ट में जोड़ सकें (+{$gain} अंक)।",
                'action_label' => 'रेट भरें',
                'action_url' => route('seller.products.index') . '?filter=zero_price',
            ];
        }

        // 4. Single image to multi-images
        if ($context['single_image_count'] > 0) {
            $recs[] = [
                'id' => 'add_more_photos',
                'priority' => 'medium',
                'title' => "🖼️ {$context['single_image_count']} प्रोडक्ट्स में केवल 1 फोटो है (अतिरिक्त ऐंगल जोड़ें)",
                'points_gain' => 8,
                'description' => "प्रोडक्ट की 2 से 3 एंगल की फोटो लगाने से खरीदार का कन्वर्शन 40% तक बढ़ जाता है (+8 अंक)।",
                'action_label' => 'फोटो स्टूडियो खोलें',
                'action_url' => route('seller.studio.index'),
            ];
        }

        // 5. Product Descriptions
        if ($context['avg_content_score'] < 20) {
            $recs[] = [
                'id' => 'add_descriptions',
                'priority' => 'medium',
                'title' => '📝 साइज़, वज़न और मटीरियल की जानकारी (Description) जोड़ें',
                'points_gain' => 10,
                'description' => 'आइटम का सही नाप (जैसे 1/2", 1", 500L), रंग और ब्रांड स्पेसिफिकेशन लिखने से Google सर्च में आपकी दुकान ऊपर आएगी (+10 अंक)।',
                'action_label' => 'प्रोडक्ट्स एडिट करें',
                'action_url' => route('seller.products.index'),
            ];
        }

        // 6. Profile & Store details
        if ($context['profile_score'] < 90) {
            $recs[] = [
                'id' => 'complete_profile',
                'priority' => 'low',
                'title' => '🏪 दुकान प्रोफाइल और पेमेंट्स विवरण (UPI / Google Map) पूरा करें',
                'points_gain' => 10,
                'description' => 'अपनी दुकान का बैनर, लोगो, WhatsApp नंबर और UPI ID पूरी तरह भरें जिससे स्टोर 100% वेरिफाइड दिखे।',
                'action_label' => 'प्रोफाइल एडिट करें',
                'action_url' => route('seller.minisite.edit'),
            ];
        }

        return $recs;
    }
}
