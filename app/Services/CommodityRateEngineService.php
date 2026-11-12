<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CommodityRateEngineService
{
    /**
     * Standard ISI 12-Meter TMT Steel Rod Weights (Kg per Piece)
     */
    public const TMT_ROD_WEIGHTS_KG = [
        '6mm' => 2.66,
        '8mm' => 4.74,
        '10mm' => 7.40,
        '12mm' => 10.66,
        '16mm' => 18.96,
        '20mm' => 29.60,
        '25mm' => 46.20,
        '32mm' => 75.70,
    ];

    /**
     * Master Commodity Catalog Definitions
     */
    public static function getCommodityDefinitions(): array
    {
        return [
            // 💎 Jewellery / Bullion
            'gold_24k' => [
                'key' => 'gold_24k',
                'group' => 'jewellery',
                'label' => '24K शुद्ध सोना (999 Gold)',
                'unit' => 'प्रति 10 ग्राम',
                'default_rate' => 78500.00,
                'icon' => 'fa-gem',
                'color' => 'amber',
            ],
            'gold_22k' => [
                'key' => 'gold_22k',
                'group' => 'jewellery',
                'label' => '22K हॉलमार्क सोना (916)',
                'unit' => 'प्रति 10 ग्राम',
                'default_rate' => 72000.00,
                'icon' => 'fa-ring',
                'color' => 'yellow',
            ],
            'gold_18k' => [
                'key' => 'gold_18k',
                'group' => 'jewellery',
                'label' => '18K सोना (750 Gold)',
                'unit' => 'प्रति 10 ग्राम',
                'default_rate' => 58900.00,
                'icon' => 'fa-crown',
                'color' => 'orange',
            ],
            'silver_999' => [
                'key' => 'silver_999',
                'group' => 'jewellery',
                'label' => 'शुद्ध चांदी (999 Silver)',
                'unit' => 'प्रति 1 Kg (1000g)',
                'default_rate' => 92000.00,
                'icon' => 'fa-coins',
                'color' => 'slate',
            ],

            // 🏗️ Iron, Steel & Cement
            'tmt_base_kg' => [
                'key' => 'tmt_base_kg',
                'group' => 'iron_cement',
                'label' => 'लोहा / सरिया बेसिक (TMT Steel)',
                'unit' => 'प्रति Kg (1000 Kg = 1 Ton)',
                'default_rate' => 60.00,
                'icon' => 'fa-bars-staggered',
                'color' => 'slate',
            ],
            'cement_bag_base' => [
                'key' => 'cement_bag_base',
                'group' => 'iron_cement',
                'label' => 'सीमेंट बेसिक (50 Kg बोरी)',
                'unit' => 'प्रति 50 Kg बैग',
                'default_rate' => 355.00,
                'icon' => 'fa-cubes',
                'color' => 'orange',
            ],

            // 🌾 Anaj Mandi
            'anaj_gehu' => [
                'key' => 'anaj_gehu',
                'group' => 'anaj_mandi',
                'label' => 'गेहूं (Wheat / Sharbati)',
                'unit' => 'प्रति क्विंटल (100 Kg)',
                'default_rate' => 2650.00,
                'icon' => 'fa-wheat-awn',
                'color' => 'emerald',
            ],
            'anaj_chana' => [
                'key' => 'anaj_chana',
                'group' => 'anaj_mandi',
                'label' => 'चना (Desi / Kabuli Chana)',
                'unit' => 'प्रति क्विंटल (100 Kg)',
                'default_rate' => 6200.00,
                'icon' => 'fa-seedling',
                'color' => 'lime',
            ],
            'anaj_soyabean' => [
                'key' => 'anaj_soyabean',
                'group' => 'anaj_mandi',
                'label' => 'सोयाबीन (Soyabean)',
                'unit' => 'प्रति क्विंटल (100 Kg)',
                'default_rate' => 4650.00,
                'icon' => 'fa-leaf',
                'color' => 'green',
            ],
            'anaj_sarson' => [
                'key' => 'anaj_sarson',
                'group' => 'anaj_mandi',
                'label' => 'सरसों / राई (Mustard)',
                'unit' => 'प्रति क्विंटल (100 Kg)',
                'default_rate' => 5800.00,
                'icon' => 'fa-sun',
                'color' => 'yellow',
            ],
            'anaj_rice' => [
                'key' => 'anaj_rice',
                'group' => 'anaj_mandi',
                'label' => 'चावल / बासमती धान',
                'unit' => 'प्रति क्विंटल (100 Kg)',
                'default_rate' => 4200.00,
                'icon' => 'fa-bowl-rice',
                'color' => 'teal',
            ],
            'anaj_dal' => [
                'key' => 'anaj_dal',
                'group' => 'anaj_mandi',
                'label' => 'तुअर / मूंग / मसूर दाल',
                'unit' => 'प्रति क्विंटल (100 Kg)',
                'default_rate' => 9600.00,
                'icon' => 'fa-jar',
                'color' => 'amber',
            ],
        ];
    }

    /**
     * Load Seller's Saved Commodity Rates & History
     */
    public function getSellerState(User $user): array
    {
        $defs = self::getCommodityDefinitions();
        $defaults = [];
        foreach ($defs as $k => $d) {
            $defaults[$k] = $d['default_rate'];
        }

        $saved = [];
        $history = [];
        $customDefs = [];

        $sellerPage = $user->sellerPage;
        if ($sellerPage && is_array($sellerPage->business_settings)) {
            $saved = $sellerPage->business_settings['commodity_rates'] ?? [];
            $history = $sellerPage->business_settings['commodity_rate_history'] ?? [];
            $customDefs = $sellerPage->business_settings['custom_commodities'] ?? [];
        } else {
            $jsonPath = storage_path("app/commodity_rates_user_{$user->id}.json");
            if (file_exists($jsonPath)) {
                $decoded = json_decode(file_get_contents($jsonPath), true) ?: [];
                $saved = $decoded['commodity_rates'] ?? [];
                $history = $decoded['commodity_rate_history'] ?? [];
                $customDefs = $decoded['custom_commodities'] ?? [];
            }
        }

        return [
            'rates' => array_merge($defaults, $saved),
            'history' => $history,
            'custom_commodities' => $customDefs,
            'definitions' => array_merge($defs, $customDefs),
        ];
    }

    /**
     * Save Seller's Commodity State persistently
     */
    public function saveSellerState(User $user, array $rates, array $history, array $customDefs = []): void
    {
        $sellerPage = $user->sellerPage;
        if ($sellerPage) {
            $settings = $sellerPage->business_settings ?? [];
            $settings['commodity_rates'] = $rates;
            $settings['commodity_rate_history'] = array_slice($history, 0, 25);
            if (!empty($customDefs)) {
                $settings['custom_commodities'] = $customDefs;
            }
            $sellerPage->business_settings = $settings;
            $sellerPage->save();
        } else {
            $jsonPath = storage_path("app/commodity_rates_user_{$user->id}.json");
            file_put_contents($jsonPath, json_encode([
                'commodity_rates' => $rates,
                'commodity_rate_history' => array_slice($history, 0, 25),
                'custom_commodities' => $customDefs,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * Auto-Detect or Read Formula Configuration for a Product (or Variant)
     */
    public function detectFormula(Product $product, ?ProductVariant $variant = null): array
    {
        $pAttrs = is_array($product->attributes) ? $product->attributes : (json_decode($product->attributes ?? '[]', true) ?: []);
        $vAttrs = $variant ? (is_array($variant->attributes) ? $variant->attributes : (json_decode($variant->attributes ?? '[]', true) ?: [])) : [];

        // Explicit saved formula takes priority
        if (!empty($vAttrs['rate_formula']['commodity_key'])) {
            return array_merge(['source' => 'saved_variant'], $vAttrs['rate_formula']);
        }
        if (!empty($pAttrs['rate_formula']['commodity_key']) && !$variant) {
            return array_merge(['source' => 'saved_product'], $pAttrs['rate_formula']);
        }

        // If parent has saved commodity_key, inherit defaults and extract variant-specific weight/size
        $savedParent = $pAttrs['rate_formula'] ?? [];
        $haystack = strtolower($product->name . ' ' . ($variant ? $variant->variant_name : '') . ' ' . ($product->category->name ?? '') . ' ' . ($product->group_name ?? ''));

        $commodityKey = $savedParent['commodity_key'] ?? null;

        if (!$commodityKey) {
            // 1. Detect Jewellery
            if (preg_match('/(gold|sona|24k|22k|18k|916|ring|chain|bangle|haar|necklace|jhumka|mangalsutra|silver|chandi|payal|bichiya|coin)/i', $haystack)) {
                if (preg_match('/(silver|chandi|payal|bichiya|999 silver)/i', $haystack)) {
                    $commodityKey = 'silver_999';
                } elseif (preg_match('/(24k|24ct|999)/i', $haystack)) {
                    $commodityKey = 'gold_24k';
                } elseif (preg_match('/(18k|18ct|750)/i', $haystack)) {
                    $commodityKey = 'gold_18k';
                } else {
                    $commodityKey = 'gold_22k';
                }
            }
            // 2. Detect TMT / Iron / Steel
            elseif (preg_match('/(sariya|saria|tmt|rebar|loha|iron|steel rod|angle|channel|fe500|fe550|kamdhenu|jindal|tata tiscon)/i', $haystack)) {
                $commodityKey = 'tmt_base_kg';
            }
            // 3. Detect Cement
            elseif (preg_match('/(cement|ultratech|ambuja|acc|bangur|birla|dalmia|shree|jk super|wonder|ppc|opc 43|opc 53)/i', $haystack)) {
                $commodityKey = 'cement_bag_base';
            }
            // 4. Detect Anaj Mandi
            elseif (preg_match('/(gehu|wheat|sharbati|lokwan)/i', $haystack)) {
                $commodityKey = 'anaj_gehu';
            } elseif (preg_match('/(chana|gram|besan)/i', $haystack)) {
                $commodityKey = 'anaj_chana';
            } elseif (preg_match('/(soyabean|soya|soybean)/i', $haystack)) {
                $commodityKey = 'anaj_soyabean';
            } elseif (preg_match('/(sarson|mustard|rai)/i', $haystack)) {
                $commodityKey = 'anaj_sarson';
            } elseif (preg_match('/(rice|chawal|basmati|dhan|paddy)/i', $haystack)) {
                $commodityKey = 'anaj_rice';
            } elseif (preg_match('/(dal|toor|arhar|moong|masoor|urad|pulses)/i', $haystack)) {
                $commodityKey = 'anaj_dal';
            }
        }

        if (!$commodityKey) {
            return ['commodity_key' => null, 'source' => 'unlinked'];
        }

        // Extract Trade-Specific Multipliers
        if (in_array($commodityKey, ['gold_24k', 'gold_22k', 'gold_18k', 'silver_999'])) {
            $weightGm = floatval($savedParent['weight_gm'] ?? ($pAttrs['net_weight_gm'] ?? 0));
            if ($weightGm <= 0 && preg_match('/(\d+(?:\.\d+)?)\s*(?:g|gm|gram|grams)\b/i', $haystack, $m)) {
                $weightGm = floatval($m[1]);
            }
            if ($weightGm <= 0) $weightGm = 10.0;

            return [
                'commodity_key' => $commodityKey,
                'trade_type' => 'jewellery',
                'weight_gm' => $weightGm,
                'making_type' => $savedParent['making_type'] ?? ($pAttrs['making_charge_type'] ?? 'percent'),
                'making_val' => floatval($savedParent['making_val'] ?? ($pAttrs['making_charge_val'] ?? 12.0)),
                'wastage_pct' => floatval($savedParent['wastage_pct'] ?? 0),
                'gst_pct' => floatval($savedParent['gst_pct'] ?? 3.0),
                'source' => !empty($savedParent['commodity_key']) ? 'saved_product' : 'auto_detected',
            ];
        }

        if ($commodityKey === 'tmt_base_kg') {
            $weightKg = floatval($savedParent['weight_kg'] ?? ($pAttrs['rod_weight_kg'] ?? 0));
            if (preg_match('/\b(6|8|10|12|16|20|25|32)\s*mm\b/i', $haystack, $mmMatch)) {
                $mmKey = strtolower($mmMatch[1]) . 'mm';
                $weightKg = self::TMT_ROD_WEIGHTS_KG[$mmKey] ?? $weightKg;
            }
            if (preg_match('/quintal|100\s*kg/i', $haystack)) {
                $weightKg = 100.0;
            } elseif (preg_match('/ton|1000\s*kg|mt\b/i', $haystack)) {
                $weightKg = 1000.0;
            } elseif ($weightKg <= 0 && preg_match('/(\d+(?:\.\d+)?)\s*kg/i', $haystack, $kgm)) {
                $weightKg = floatval($kgm[1]);
            }
            if ($weightKg <= 0) $weightKg = 10.66; // default 12mm rod

            return [
                'commodity_key' => 'tmt_base_kg',
                'trade_type' => 'tmt_steel',
                'weight_kg' => $weightKg,
                'extra_per_kg' => floatval($savedParent['extra_per_kg'] ?? 1.50), // size diff / loading per kg
                'margin_pct' => floatval($savedParent['margin_pct'] ?? 4.0),
                'gst_pct' => floatval($savedParent['gst_pct'] ?? 18.0),
                'gst_mode' => $savedParent['gst_mode'] ?? 'extra', // 'included' or 'extra'
                'source' => !empty($savedParent['commodity_key']) ? 'saved_product' : 'auto_detected',
            ];
        }

        if ($commodityKey === 'cement_bag_base') {
            $bagsCount = floatval($savedParent['bags_count'] ?? 1.0);
            return [
                'commodity_key' => 'cement_bag_base',
                'trade_type' => 'cement',
                'bags_count' => $bagsCount,
                'hamali_freight' => floatval($savedParent['hamali_freight'] ?? ($pAttrs['loading_hamali'] ?? 10.0)), // ₹10 loading/unloading
                'margin_pct' => floatval($savedParent['margin_pct'] ?? 5.0),
                'wholesale_margin_pct' => floatval($savedParent['wholesale_margin_pct'] ?? 2.5),
                'source' => !empty($savedParent['commodity_key']) ? 'saved_product' : 'auto_detected',
            ];
        }

        // Grain / Mandi or custom commodity
        $unitKg = floatval($savedParent['weight_kg'] ?? 0);
        if ($unitKg <= 0) {
            if (preg_match('/quintal|100\s*kg/i', $haystack)) $unitKg = 100.0;
            elseif (preg_match('/bori|50\s*kg/i', $haystack)) $unitKg = 50.0;
            elseif (preg_match('/katta|30\s*kg/i', $haystack)) $unitKg = 30.0;
            elseif (preg_match('/25\s*kg/i', $haystack)) $unitKg = 25.0;
            elseif (preg_match('/10\s*kg/i', $haystack)) $unitKg = 10.0;
            elseif (preg_match('/5\s*kg/i', $haystack)) $unitKg = 5.0;
            elseif (preg_match('/(\d+(?:\.\d+)?)\s*kg/i', $haystack, $m)) $unitKg = floatval($m[1]);
            else $unitKg = 100.0; // Default 1 Quintal
        }

        return [
            'commodity_key' => $commodityKey,
            'trade_type' => 'mandi_grain',
            'weight_kg' => $unitKg,
            'hamali_bardana' => floatval($savedParent['hamali_bardana'] ?? ($unitKg >= 50 ? 25.0 : 5.0)),
            'margin_pct' => floatval($savedParent['margin_pct'] ?? 4.0),
            'wholesale_margin_pct' => floatval($savedParent['wholesale_margin_pct'] ?? 2.0),
            'source' => !empty($savedParent['commodity_key']) ? 'saved_product' : 'auto_detected',
        ];
    }

    /**
     * Calculate Live Retail, Wholesale & Purchase Price using Master Rates
     */
    public function calculatePrice(array $formula, array $masterRates): array
    {
        $key = $formula['commodity_key'] ?? null;
        if (!$key || !isset($masterRates[$key])) {
            return ['valid' => false];
        }

        $baseRate = floatval($masterRates[$key]);
        $type = $formula['trade_type'] ?? 'mandi_grain';

        if ($type === 'jewellery') {
            $perGramBase = str_contains($key, 'silver') ? ($baseRate / 1000.0) : ($baseRate / 10.0);
            $weightGm = floatval($formula['weight_gm'] ?? 10.0);
            $metalValue = $weightGm * $perGramBase;

            $makingType = $formula['making_type'] ?? 'percent';
            $makingVal = floatval($formula['making_val'] ?? 12.0);
            $makingFee = 0;
            if ($makingType === 'percent') {
                $makingFee = $metalValue * ($makingVal / 100.0);
            } elseif ($makingType === 'per_gm') {
                $makingFee = $weightGm * $makingVal;
            } else {
                $makingFee = $makingVal;
            }

            $wastagePct = floatval($formula['wastage_pct'] ?? 0);
            $wastageFee = $metalValue * ($wastagePct / 100.0);

            $subtotal = $metalValue + $makingFee + $wastageFee;
            $gstPct = floatval($formula['gst_pct'] ?? 3.0);
            $gstAmt = $subtotal * ($gstPct / 100.0);
            $retail = round($subtotal + $gstAmt);
            $wholesale = round($metalValue + ($makingFee * 0.65) + $gstAmt);

            return [
                'valid' => true,
                'purchase_price' => round($metalValue, 2),
                'wholesale_price' => $wholesale,
                'retail_price' => $retail,
                'explanation' => "{$weightGm}g × ₹" . number_format($perGramBase, 1) . "/g + मेकिंग ₹" . round($makingFee) . " + {$gstPct}% GST",
            ];
        }

        if ($type === 'tmt_steel') {
            $weightKg = floatval($formula['weight_kg'] ?? 10.66);
            $extraPerKg = floatval($formula['extra_per_kg'] ?? 1.5);
            $effPerKg = $baseRate + $extraPerKg;
            $rawRodCost = $weightKg * $effPerKg;
            $gstPct = floatval($formula['gst_pct'] ?? 18.0);
            $gstMult = ($formula['gst_mode'] ?? 'extra') === 'extra' ? (1 + $gstPct / 100.0) : 1.0;

            $landing = $rawRodCost * $gstMult;
            $marginPct = floatval($formula['margin_pct'] ?? 4.0);
            $retail = round($landing * (1 + $marginPct / 100.0));
            $wholesale = round($landing * (1 + max(1.0, $marginPct / 2.0) / 100.0));

            return [
                'valid' => true,
                'purchase_price' => round($landing, 2),
                'wholesale_price' => $wholesale,
                'retail_price' => $retail,
                'explanation' => "{$weightKg}Kg × ₹" . number_format($effPerKg, 2) . "/Kg" . ($gstMult > 1 ? " + {$gstPct}% GST" : "") . " + {$marginPct}% मुनाफा",
            ];
        }

        if ($type === 'cement') {
            $bags = floatval($formula['bags_count'] ?? 1.0);
            $hamali = floatval($formula['hamali_freight'] ?? 10.0);
            $landing = ($baseRate + $hamali) * $bags;
            $marginPct = floatval($formula['margin_pct'] ?? 5.0);
            $wsPct = floatval($formula['wholesale_margin_pct'] ?? 2.5);

            $retail = round($landing * (1 + $marginPct / 100.0));
            $wholesale = round($landing * (1 + $wsPct / 100.0));

            return [
                'valid' => true,
                'purchase_price' => round($landing, 2),
                'wholesale_price' => $wholesale,
                'retail_price' => $retail,
                'explanation' => "{$bags} बैग × ₹{$baseRate} + ₹{$hamali} हमाली + {$marginPct}% मार्जिन",
            ];
        }

        // Grain / Mandi (Base Rate is per 100 Kg Quintal)
        $unitKg = floatval($formula['weight_kg'] ?? 100.0);
        $ratePerKg = $baseRate / 100.0;
        $hamaliBardana = floatval($formula['hamali_bardana'] ?? 25.0);
        $landing = ($ratePerKg * $unitKg) + $hamaliBardana;
        $marginPct = floatval($formula['margin_pct'] ?? 4.0);
        $wsPct = floatval($formula['wholesale_margin_pct'] ?? 2.0);

        $retail = round($landing * (1 + $marginPct / 100.0));
        $wholesale = round($landing * (1 + $wsPct / 100.0));

        return [
            'valid' => true,
            'purchase_price' => round($landing, 2),
            'wholesale_price' => $wholesale,
            'retail_price' => $retail,
            'explanation' => "{$unitKg}Kg @ ₹" . number_format($ratePerKg, 2) . "/Kg + ₹{$hamaliBardana} बारदाना/हमाली + {$marginPct}% मार्जिन",
        ];
    }

    /**
     * Apply Master Base Rates to ALL Linked / Auto-Detected Seller Products & Variants
     */
    public function applyMasterRates(User $user, array $updatedRates, string $source = 'Manual Update'): array
    {
        $state = $this->getSellerState($user);
        $oldRates = $state['rates'];
        $mergedRates = array_merge($oldRates, $updatedRates);
        $defs = $state['definitions'];

        $updatedProductsCount = 0;
        $updatedVariantsCount = 0;
        $affectedDetails = [];

        DB::transaction(function () use ($user, $mergedRates, &$updatedProductsCount, &$updatedVariantsCount, &$affectedDetails) {
            $products = Product::where('user_id', $user->id)->with(['variants', 'category'])->get();

            foreach ($products as $prod) {
                $parentChanged = false;
                $variants = $prod->variants;

                if ($variants && $variants->isNotEmpty()) {
                    $lowestRetail = null;
                    $lowestWholesale = null;
                    $lowestPurchase = null;

                    foreach ($variants as $var) {
                        $formula = $this->detectFormula($prod, $var);
                        if (empty($formula['commodity_key']) || !isset($updatedRates[$formula['commodity_key']])) {
                            continue;
                        }

                        $calc = $this->calculatePrice($formula, $mergedRates);
                        if (!$calc['valid']) continue;

                        $oldPrice = (float)$var->retail_price;
                        $var->purchase_price = $calc['purchase_price'];
                        $var->wholesale_price = $calc['wholesale_price'];
                        $var->retail_price = $calc['retail_price'];
                        $var->price_effective_date = now()->toDateString();

                        // Persist detected/used formula on variant
                        $vAttrs = is_array($var->attributes) ? $var->attributes : (json_decode($var->attributes ?? '[]', true) ?: []);
                        $vAttrs['rate_formula'] = $formula;
                        $var->attributes = $vAttrs;
                        $var->save();

                        $updatedVariantsCount++;
                        $parentChanged = true;

                        if ($lowestRetail === null || $calc['retail_price'] < $lowestRetail) $lowestRetail = $calc['retail_price'];
                        if ($lowestWholesale === null || $calc['wholesale_price'] < $lowestWholesale) $lowestWholesale = $calc['wholesale_price'];
                        if ($lowestPurchase === null || $calc['purchase_price'] < $lowestPurchase) $lowestPurchase = $calc['purchase_price'];

                        $affectedDetails[] = [
                            'product_name' => $prod->name . ' (' . $var->variant_name . ')',
                            'old_price' => $oldPrice,
                            'new_price' => $calc['retail_price'],
                            'formula' => $calc['explanation'],
                        ];
                    }

                    if ($parentChanged && $lowestRetail !== null) {
                        $prod->price = $lowestRetail;
                        $prod->wholesale_price = $lowestWholesale ?? $lowestRetail;
                        $prod->purchase_price = $lowestPurchase ?? $prod->purchase_price;
                        $prod->price_effective_date = now()->toDateString();
                        $prod->save();
                        $updatedProductsCount++;
                    }
                } else {
                    $formula = $this->detectFormula($prod, null);
                    if (!empty($formula['commodity_key']) && isset($updatedRates[$formula['commodity_key']])) {
                        $calc = $this->calculatePrice($formula, $mergedRates);
                        if ($calc['valid']) {
                            $oldPrice = (float)$prod->price;
                            $prod->purchase_price = $calc['purchase_price'];
                            $prod->wholesale_price = $calc['wholesale_price'];
                            $prod->price = $calc['retail_price'];
                            $prod->price_effective_date = now()->toDateString();

                            $pAttrs = is_array($prod->attributes) ? $prod->attributes : (json_decode($prod->attributes ?? '[]', true) ?: []);
                            $pAttrs['rate_formula'] = $formula;
                            $prod->attributes = $pAttrs;
                            $prod->save();

                            $updatedProductsCount++;
                            $affectedDetails[] = [
                                'product_name' => $prod->name,
                                'old_price' => $oldPrice,
                                'new_price' => $calc['retail_price'],
                                'formula' => $calc['explanation'],
                            ];
                        }
                    }
                }
            }
        });

        // Record in history
        $history = $state['history'];
        $changesSummary = [];
        foreach ($updatedRates as $k => $v) {
            $old = $oldRates[$k] ?? 0;
            if ((float)$old != (float)$v) {
                $lbl = $defs[$k]['label'] ?? $k;
                $changesSummary[] = "{$lbl}: ₹" . number_format((float)$old) . " → ₹" . number_format((float)$v);
            }
        }

        if (!empty($changesSummary) || $updatedProductsCount > 0) {
            array_unshift($history, [
                'timestamp' => now()->format('d M Y, h:i A'),
                'source' => $source,
                'summary' => implode(' | ', $changesSummary) ?: 'Master Rate Sync',
                'products_updated' => $updatedProductsCount,
                'variants_updated' => $updatedVariantsCount,
            ]);
        }

        $this->saveSellerState($user, $mergedRates, $history, $state['custom_commodities']);

        return [
            'rates' => $mergedRates,
            'updated_products' => $updatedProductsCount,
            'updated_variants' => $updatedVariantsCount,
            'affected_details' => $affectedDetails,
            'changes_summary' => $changesSummary,
        ];
    }

    /**
     * 🤖 AI Voice & Natural Hindi/Hinglish Rate Command Parser
     */
    public function parseVoiceCommand(string $transcript, User $user, ?string $apiKey = null): array
    {
        $state = $this->getSellerState($user);
        $currentRates = $state['rates'];
        $defs = $state['definitions'];

        $transcriptClean = trim($transcript);
        $parsedUpdates = [];
        $provider = 'Smart Hinglish Engine';

        // 1. Try LLM (OpenAI / Gemini) if available for ultra-natural spoken Hindi phrasing
        $openAiKey = ($apiKey && str_starts_with($apiKey, 'sk-')) ? $apiKey : (env('OPENAI_KEY') ?: env('OPENAI_API_KEY'));
        $geminiKey = ($apiKey && str_starts_with($apiKey, 'AIza')) ? $apiKey : (env('GEMINI_API_KEY') ?: env('GEMINI_KEY'));

        $keysDesc = [];
        foreach ($defs as $k => $d) {
            $cur = $currentRates[$k] ?? $d['default_rate'];
            $keysDesc[] = "- key: '{$k}' | name: '{$d['label']}' | unit: '{$d['unit']}' | current_rate: {$cur}";
        }
        $keysBlock = implode("\n", $keysDesc);

        $aiPrompt = "You are VyaparIndia's Indian Commodity Rate Voice Assistant.
A merchant spoke/typed this Hindi/Hinglish command to update today's market rates:
\"{$transcriptClean}\"

Available commodity base keys & current rates:
{$keysBlock}

Rules:
- Match every commodity & rate change mentioned in the command (supports absolute rate like 'gehu 2750', 'sona 73500', OR relative increase/decrease like 'sariya 2 rupay badha do' -> current + 2, or 'cement 10 rupay ghata do' -> current - 10).
- Understand Indian shorthand like '73.5k' = 73500, '72 hazar' = 72000, '360 rupay' = 360.
- Also detect if the user specifies '24k' vs '22k' vs '18k' vs general 'gold/sona' (default to 'gold_22k' if unspecified or update 24k if 24k mentioned).
- Return ONLY valid JSON:
{\"updates\": [{\"key\": \"...\", \"new_rate\": 0, \"confidence\": \"high\", \"reason_hi\": \"...\"}]}";

        if ($openAiKey && (str_starts_with($openAiKey, 'sk-') || !$geminiKey)) {
            try {
                $resp = Http::withToken($openAiKey)->timeout(15)->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Output strict JSON only.'],
                        ['role' => 'user', 'content' => $aiPrompt],
                    ],
                    'temperature' => 0.0,
                ]);
                if ($resp->successful()) {
                    $raw = $resp->json('choices.0.message.content');
                    $decoded = $this->extractJson($raw);
                    if (!empty($decoded['updates'])) {
                        $parsedUpdates = $decoded['updates'];
                        $provider = 'OpenAI Voice AI';
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Voice Rate OpenAI error: ' . $e->getMessage());
            }
        }

        if (empty($parsedUpdates) && $geminiKey) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$geminiKey}";
                $resp = Http::timeout(15)->post($url, [
                    'contents' => [['parts' => [['text' => $aiPrompt]]]],
                    'generationConfig' => ['temperature' => 0.0],
                ]);
                if ($resp->successful()) {
                    $raw = $resp->json('candidates.0.content.parts.0.text');
                    $decoded = $this->extractJson($raw);
                    if (!empty($decoded['updates'])) {
                        $parsedUpdates = $decoded['updates'];
                        $provider = 'Gemini Voice AI';
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Voice Rate Gemini error: ' . $e->getMessage());
            }
        }

        // 2. Deterministic Hinglish/Hindi Smart Regex Parser Fallback (Instant, 0ms, works 100% offline too)
        if (empty($parsedUpdates)) {
            $parsedUpdates = $this->fallbackHindiRegexParse($transcriptClean, $currentRates, $defs);
        }

        // Enrich parsed updates with labels, old rates, and count of products affected
        $products = Product::where('user_id', $user->id)->with(['variants', 'category'])->get();
        $enriched = [];

        foreach ($parsedUpdates as $upd) {
            $k = $upd['key'] ?? '';
            if (!isset($defs[$k])) continue;
            $newVal = round(floatval($upd['new_rate'] ?? 0), 2);
            if ($newVal <= 0) continue;

            // Count how many products/variants will recalculate
            $matchCount = 0;
            foreach ($products as $p) {
                if ($p->variants && $p->variants->isNotEmpty()) {
                    foreach ($p->variants as $v) {
                        $f = $this->detectFormula($p, $v);
                        if (($f['commodity_key'] ?? '') === $k) $matchCount++;
                    }
                } else {
                    $f = $this->detectFormula($p, null);
                    if (($f['commodity_key'] ?? '') === $k) $matchCount++;
                }
            }

            $enriched[] = [
                'key' => $k,
                'label' => $defs[$k]['label'],
                'unit' => $defs[$k]['unit'],
                'old_rate' => floatval($currentRates[$k] ?? $defs[$k]['default_rate']),
                'new_rate' => $newVal,
                'diff' => round($newVal - floatval($currentRates[$k] ?? $defs[$k]['default_rate']), 2),
                'affected_items_count' => $matchCount,
                'reason_hi' => $upd['reason_hi'] ?? "आवाज़ कमांड से ₹{$newVal} पर सेट",
            ];
        }

        return [
            'success' => true,
            'provider' => $provider,
            'transcript' => $transcriptClean,
            'updates' => $enriched,
        ];
    }

    private function extractJson(?string $text): array
    {
        if (!$text) return [];
        $text = trim($text);
        $text = preg_replace('/^```json\s*/i', '', $text);
        $text = preg_replace('/```$/', '', $text);
        $decoded = json_decode(trim($text), true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Built-in Hindi / Hinglish Commodity & Number Regex Parser
     */
    private function fallbackHindiRegexParse(string $text, array $currentRates, array $defs): array
    {
        $normalized = mb_strtolower($text, 'UTF-8');
        // Normalize commas inside numbers like 73,500 -> 73500
        $normalized = preg_replace('/(\d),(\d)/', '$1$2', $normalized);
        // Handle "73.5k" -> "73500"
        $normalized = preg_replace_callback('/(\d+(?:\.\d+)?)\s*k\b/i', function ($m) {
            return (string)(floatval($m[1]) * 1000);
        }, $normalized);
        // Handle "73 hazar 500" or "72 hazar"
        $normalized = preg_replace_callback('/(\d+(?:\.\d+)?)\s*(?:hazar|hajar|हजार|हज़ार)\s*(\d+)?/iu', function ($m) {
            $base = floatval($m[1]) * 1000;
            $extra = isset($m[2]) ? floatval($m[2]) : 0;
            return (string)($base + $extra);
        }, $normalized);

        $synonymPatterns = [
            'gold_24k' => '/(?:24\s*k|24\s*carat|24\s*कैरेट|शुद्ध सोना|pure gold)/iu',
            'gold_18k' => '/(?:18\s*k|18\s*carat|18\s*कैरेट)/iu',
            'gold_22k' => '/(?:22\s*k|22\s*carat|22\s*कैरेट|916|sona|सोना|gold|गोल्ड)/iu',
            'silver_999' => '/(?:chandi|चांदी|चाँदी|silver|सिल्वर)/iu',
            'tmt_base_kg' => '/(?:sariya|saria|सरिया|loha|लोहा|tmt|steel|स्टील)/iu',
            'cement_bag_base' => '/(?:cement|सीमेंट|ultratech|ambuja|acc|bangur|बोरी सीमेंट)/iu',
            'anaj_gehu' => '/(?:gehu|gehun|गेहूं|गेहूँ|wheat)/iu',
            'anaj_chana' => '/(?:chana|चना|kabuli)/iu',
            'anaj_soyabean' => '/(?:soyabean|soya|सोयाबीन|सोया)/iu',
            'anaj_sarson' => '/(?:sarson|सरसों|mustard|rai)/iu',
            'anaj_rice' => '/(?:rice|chawal|चावल|basmati|बासमती|dhan|धान)/iu',
            'anaj_dal' => '/(?:dal|दाल|toor|तुअर|moong|मूंग|masoor|मसूर)/iu',
        ];

        // Split transcript by conjunctions ("aur", "और", ",", "तथा", "&")
        $clauses = preg_split('/(?:\s+aur\s+|\s+और\s+|,|&|\s+tatha\s+|\s+तथा\s+)/iu', $normalized);
        $results = [];
        $usedKeys = [];

        foreach ($clauses as $clause) {
            $clause = trim($clause);
            if (empty($clause)) continue;

            // Find all numbers in this clause (ignore 24, 22, 18 when followed immediately by k/carat/कैरेट)
            $clauseNoCarat = preg_replace('/\b(?:24|22|18)\s*(?:k|ct|carat|कैरेट)/iu', '', $clause);
            if (!preg_match('/(\d+(?:\.\d+)?)/', $clauseNoCarat, $numMatch)) {
                continue;
            }
            $numVal = floatval($numMatch[1]);

            $matchedKey = null;
            foreach ($synonymPatterns as $key => $regex) {
                if (preg_match($regex, $clause)) {
                    // Prevent gold_22k stealing 24k/18k if already matched
                    if ($key === 'gold_22k' && (preg_match($synonymPatterns['gold_24k'], $clause) || preg_match($synonymPatterns['gold_18k'], $clause))) {
                        continue;
                    }
                    $matchedKey = $key;
                    break;
                }
            }

            if ($matchedKey && !isset($usedKeys[$matchedKey])) {
                $cur = floatval($currentRates[$matchedKey] ?? $defs[$matchedKey]['default_rate']);
                // Check relative increase/decrease words
                if (preg_match('/(badha|बढ़ा|bada|extra|ज्यादा|up|\+)/iu', $clause) && $numVal < ($cur * 0.4)) {
                    $newRate = $cur + $numVal;
                    $reason = "₹{$numVal} बढ़ाया गया ({$cur} + {$numVal})";
                } elseif (preg_match('/(ghata|घटा|kam|कम|down|\-)/iu', $clause) && $numVal < ($cur * 0.4)) {
                    $newRate = max(1, $cur - $numVal);
                    $reason = "₹{$numVal} घटाया गया ({$cur} - {$numVal})";
                } else {
                    // Special TMT ton vs kg check: if user says "sariya 60000" -> convert /1000 to per kg
                    if ($matchedKey === 'tmt_base_kg' && $numVal > 1000) {
                        $numVal = round($numVal / 1000, 2);
                    }
                    $newRate = $numVal;
                    $reason = "बोलकर सीधा नया भाव ₹{$newRate} पकड़ा गया";
                }

                $usedKeys[$matchedKey] = true;
                $results[] = [
                    'key' => $matchedKey,
                    'new_rate' => $newRate,
                    'reason_hi' => $reason,
                ];
            }
        }

        return $results;
    }
}
