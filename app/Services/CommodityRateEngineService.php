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
     * Default TMT Die Charges (₹ per Ton extra over 12mm Base)
     */
    public const DEFAULT_TMT_DIE_PER_TON = [
        '6mm' => 2500,
        '8mm' => 1500,
        '10mm' => 500,
        '12mm' => 0,
        '16mm' => 0,
        '20mm' => 500,
        '25mm' => 1000,
        '32mm' => 1500,
    ];

    /**
     * Standard Structural Steel (Angle / Channel / Flat) 6-Meter (20ft) Weights (Kg per Length)
     */
    public const FABRICATION_SECTION_WEIGHTS_KG = [
        '25x25x3' => 6.80,
        '30x30x3' => 8.20,
        '35x35x5' => 15.60,
        '40x40x5' => 18.00,
        '50x50x5' => 22.80,
        '50x50x6' => 27.00,
        '65x65x6' => 34.80,
        '75x75x6' => 40.80,
        '75x40' => 42.80,
        '100x50' => 57.40,
        '125x65' => 78.60,
    ];

    /**
     * Master Commodity Catalog Definitions (with GST, Brand, Plant, Loading, Insurance, Freight & Die defaults)
     */
    public static function getCommodityDefinitions(): array
    {
        return [
            // 💎 Jewellery / Bullion
            'gold_24k' => [
                'key' => 'gold_24k',
                'group' => 'jewellery',
                'label' => '24K शुद्ध सोना (999 Gold)',
                'brand' => 'Bullion 999',
                'plant_location' => 'Local Sarafa Bazar',
                'unit' => 'प्रति 10 ग्राम',
                'rate_unit' => 'per_10gm',
                'default_rate' => 78500.00,
                'gst_mode' => 'extra',
                'gst_pct' => 3.0,
                'loading_charge' => 0,
                'insurance_charge' => 50.0, // HUID + Hallmarking / Insurance per pc
                'local_freight' => 0,
                'default_profit_type' => 'percent',
                'default_profit_val' => 0, // Making charge covers profit, optional extra profit
                'icon' => 'fa-gem',
                'color' => 'amber',
            ],
            'gold_22k' => [
                'key' => 'gold_22k',
                'group' => 'jewellery',
                'label' => '22K हॉलमार्क सोना (916)',
                'brand' => 'BIS HUID 916',
                'plant_location' => 'Local Sarafa Bazar',
                'unit' => 'प्रति 10 ग्राम',
                'rate_unit' => 'per_10gm',
                'default_rate' => 72000.00,
                'gst_mode' => 'extra',
                'gst_pct' => 3.0,
                'loading_charge' => 0,
                'insurance_charge' => 45.0,
                'local_freight' => 0,
                'default_profit_type' => 'percent',
                'default_profit_val' => 0,
                'icon' => 'fa-ring',
                'color' => 'yellow',
            ],
            'gold_18k' => [
                'key' => 'gold_18k',
                'group' => 'jewellery',
                'label' => '18K सोना (750 Gold)',
                'brand' => 'BIS HUID 750',
                'plant_location' => 'Local Sarafa Bazar',
                'unit' => 'प्रति 10 ग्राम',
                'rate_unit' => 'per_10gm',
                'default_rate' => 58900.00,
                'gst_mode' => 'extra',
                'gst_pct' => 3.0,
                'loading_charge' => 0,
                'insurance_charge' => 45.0,
                'local_freight' => 0,
                'default_profit_type' => 'percent',
                'default_profit_val' => 0,
                'icon' => 'fa-crown',
                'color' => 'orange',
            ],
            'silver_999' => [
                'key' => 'silver_999',
                'group' => 'jewellery',
                'label' => 'शुद्ध चांदी (999 Silver)',
                'brand' => 'Pure 999 Silver',
                'plant_location' => 'Local Sarafa Bazar',
                'unit' => 'प्रति 1 Kg (1000g)',
                'rate_unit' => 'per_kg',
                'default_rate' => 92000.00,
                'gst_mode' => 'extra',
                'gst_pct' => 3.0,
                'loading_charge' => 0,
                'insurance_charge' => 25.0,
                'local_freight' => 0,
                'default_profit_type' => 'percent',
                'default_profit_val' => 0,
                'icon' => 'fa-coins',
                'color' => 'slate',
            ],

            // 🏗️ Iron, Steel & Cement (Multi-Brand & Plant Location Aware)
            'tmt_base_kg' => [
                'key' => 'tmt_base_kg',
                'group' => 'iron_cement',
                'label' => 'सामान्य सरिया (TMT Base)',
                'brand' => 'Local / Standard Mill',
                'plant_location' => 'Raipur / Regional Mill',
                'unit' => 'प्रति टन (MT)',
                'rate_unit' => 'per_ton', // Supports per_ton or per_kg
                'default_rate' => 54500.00,
                'gst_mode' => 'extra',
                'gst_pct' => 18.0,
                'loading_charge' => 250.0, // ₹/Ton
                'insurance_charge' => 100.0, // ₹/Ton
                'local_freight' => 1200.0, // Plant to Godown Bhada ₹/Ton
                'default_profit_type' => 'percent',
                'default_profit_val' => 4.0,
                'die_rates' => self::DEFAULT_TMT_DIE_PER_TON,
                'icon' => 'fa-bars-staggered',
                'color' => 'slate',
            ],
            'tmt_kamdhenu' => [
                'key' => 'tmt_kamdhenu',
                'group' => 'iron_cement',
                'label' => 'Kamdhenu TMT (कामधेनु सरिया)',
                'brand' => 'Kamdhenu',
                'plant_location' => 'Raipur / Bhiwadi Plant',
                'unit' => 'प्रति टन (MT)',
                'rate_unit' => 'per_ton',
                'default_rate' => 56500.00,
                'gst_mode' => 'extra',
                'gst_pct' => 18.0,
                'loading_charge' => 250.0,
                'insurance_charge' => 100.0,
                'local_freight' => 1300.0,
                'default_profit_type' => 'percent',
                'default_profit_val' => 4.0,
                'die_rates' => self::DEFAULT_TMT_DIE_PER_TON,
                'icon' => 'fa-industry',
                'color' => 'orange',
            ],
            'tmt_jindal_tata' => [
                'key' => 'tmt_jindal_tata',
                'group' => 'iron_cement',
                'label' => 'Jindal Panther / Tata Tiscon',
                'brand' => 'Jindal / Tata',
                'plant_location' => 'Patratu / Jamshedpur Plant',
                'unit' => 'प्रति टन (MT)',
                'rate_unit' => 'per_ton',
                'default_rate' => 62000.00,
                'gst_mode' => 'extra',
                'gst_pct' => 18.0,
                'loading_charge' => 300.0,
                'insurance_charge' => 150.0,
                'local_freight' => 1500.0,
                'default_profit_type' => 'percent',
                'default_profit_val' => 3.5,
                'die_rates' => self::DEFAULT_TMT_DIE_PER_TON,
                'icon' => 'fa-shield-halved',
                'color' => 'blue',
            ],
            'cement_bag_base' => [
                'key' => 'cement_bag_base',
                'group' => 'iron_cement',
                'label' => 'सामान्य / लोकल सीमेंट (50 Kg)',
                'brand' => 'Standard PPC/OPC',
                'plant_location' => 'Local Rake / Godown',
                'unit' => 'प्रति 50 Kg बैग',
                'rate_unit' => 'per_bag',
                'default_rate' => 280.00,
                'gst_mode' => 'extra',
                'gst_pct' => 28.0,
                'loading_charge' => 5.0, // Hamali per bag
                'insurance_charge' => 0,
                'local_freight' => 12.0, // Plant/Rake to Godown bhada per bag
                'default_profit_type' => 'percent',
                'default_profit_val' => 5.0,
                'icon' => 'fa-cubes',
                'color' => 'orange',
            ],
            'cement_ultratech_acc' => [
                'key' => 'cement_ultratech_acc',
                'group' => 'iron_cement',
                'label' => 'UltraTech / ACC / Ambuja (50 Kg)',
                'brand' => 'UltraTech / ACC / Ambuja',
                'plant_location' => 'Direct Plant / Depot',
                'unit' => 'प्रति 50 Kg बैग',
                'rate_unit' => 'per_bag',
                'default_rate' => 385.00,
                'gst_mode' => 'included',
                'gst_pct' => 28.0,
                'loading_charge' => 5.0,
                'insurance_charge' => 0,
                'local_freight' => 10.0,
                'default_profit_type' => 'percent',
                'default_profit_val' => 4.5,
                'icon' => 'fa-cubes-stacked',
                'color' => 'amber',
            ],

            // 🛠️ Fabrication Structure, Pipes & Roofing Sheets
            'fab_ms_angle_channel' => [
                'key' => 'fab_ms_angle_channel',
                'group' => 'fabrication_structure',
                'label' => 'MS एंगल, चैनल, पट्टी & गार्डर',
                'brand' => 'Structural Steel / Rolling Mill',
                'plant_location' => 'Raipur / Gobindgarh Mill',
                'unit' => 'प्रति टन (MT)',
                'rate_unit' => 'per_ton',
                'default_rate' => 55000.00,
                'gst_mode' => 'extra',
                'gst_pct' => 18.0,
                'loading_charge' => 250.0,
                'insurance_charge' => 100.0,
                'local_freight' => 1200.0,
                'default_profit_type' => 'percent',
                'default_profit_val' => 5.0,
                'icon' => 'fa-shapes',
                'color' => 'indigo',
            ],
            'fab_ms_gp_pipe' => [
                'key' => 'fab_ms_gp_pipe',
                'group' => 'fabrication_structure',
                'label' => 'MS / GP चौकोर व गोल पाइप (20ft)',
                'brand' => 'APL Apollo / Surya / Jindal',
                'plant_location' => 'Tube Mill / Depot',
                'unit' => 'प्रति Kg',
                'rate_unit' => 'per_kg',
                'default_rate' => 64.00,
                'gst_mode' => 'extra',
                'gst_pct' => 18.0,
                'loading_charge' => 0.30,
                'insurance_charge' => 0.10,
                'local_freight' => 1.50,
                'default_profit_type' => 'percent',
                'default_profit_val' => 6.0,
                'icon' => 'fa-grip-lines',
                'color' => 'blue',
            ],
            'fab_tin_sheet' => [
                'key' => 'fab_tin_sheet',
                'group' => 'fabrication_structure',
                'label' => 'रंगीन प्रोफाइल / टिन चादर (JSW/GI)',
                'brand' => 'JSW Colouron / Bhushan',
                'plant_location' => 'Coating Coil Plant',
                'unit' => 'प्रति फीट (Running Ft)',
                'rate_unit' => 'per_ft',
                'default_rate' => 105.00,
                'gst_mode' => 'extra',
                'gst_pct' => 18.0,
                'loading_charge' => 2.0,
                'insurance_charge' => 1.0,
                'local_freight' => 5.0,
                'default_profit_type' => 'percent',
                'default_profit_val' => 6.0,
                'icon' => 'fa-layer-group',
                'color' => 'cyan',
            ],
            'fab_cement_sheet' => [
                'key' => 'fab_cement_sheet',
                'group' => 'fabrication_structure',
                'label' => 'सीमेंट रूफिंग चादर (Everest/AC)',
                'brand' => 'Everest / Charminar',
                'plant_location' => 'Plant / Regional Godown',
                'unit' => 'प्रति फीट (Running Ft)',
                'rate_unit' => 'per_ft',
                'default_rate' => 56.00,
                'gst_mode' => 'extra',
                'gst_pct' => 18.0,
                'loading_charge' => 8.0,
                'insurance_charge' => 5.0, // Breakage insurance per sheet
                'local_freight' => 15.0,
                'default_profit_type' => 'percent',
                'default_profit_val' => 6.0,
                'icon' => 'fa-sheet-plastic',
                'color' => 'slate',
            ],

            // 🌾 Anaj Mandi
            'anaj_gehu' => [
                'key' => 'anaj_gehu',
                'group' => 'anaj_mandi',
                'label' => 'गेहूं (Wheat / Sharbati)',
                'brand' => 'Mandi Loose / Packed',
                'plant_location' => 'Krishi Upaj Mandi',
                'unit' => 'प्रति क्विंटल (100 Kg)',
                'rate_unit' => 'per_quintal',
                'default_rate' => 2650.00,
                'gst_mode' => 'included',
                'gst_pct' => 0.0,
                'loading_charge' => 25.0, // Hamali + Tulai per quintal
                'insurance_charge' => 0,
                'local_freight' => 40.0, // Mandi to Godown bhada per quintal
                'default_profit_type' => 'percent',
                'default_profit_val' => 4.0,
                'icon' => 'fa-wheat-awn',
                'color' => 'emerald',
            ],
            'anaj_chana' => [
                'key' => 'anaj_chana',
                'group' => 'anaj_mandi',
                'label' => 'चना (Desi / Kabuli Chana)',
                'brand' => 'Mandi Sortex',
                'plant_location' => 'Krishi Upaj Mandi',
                'unit' => 'प्रति क्विंटल (100 Kg)',
                'rate_unit' => 'per_quintal',
                'default_rate' => 6200.00,
                'gst_mode' => 'included',
                'gst_pct' => 0.0,
                'loading_charge' => 25.0,
                'insurance_charge' => 0,
                'local_freight' => 40.0,
                'default_profit_type' => 'percent',
                'default_profit_val' => 4.0,
                'icon' => 'fa-seedling',
                'color' => 'lime',
            ],
            'anaj_soyabean' => [
                'key' => 'anaj_soyabean',
                'group' => 'anaj_mandi',
                'label' => 'सोयाबीन (Soyabean)',
                'brand' => 'Mandi Bold',
                'plant_location' => 'Indore / Ujjain Mandi',
                'unit' => 'प्रति क्विंटल (100 Kg)',
                'rate_unit' => 'per_quintal',
                'default_rate' => 4650.00,
                'gst_mode' => 'included',
                'gst_pct' => 0.0,
                'loading_charge' => 25.0,
                'insurance_charge' => 0,
                'local_freight' => 40.0,
                'default_profit_type' => 'percent',
                'default_profit_val' => 4.0,
                'icon' => 'fa-leaf',
                'color' => 'green',
            ],
            'anaj_sarson' => [
                'key' => 'anaj_sarson',
                'group' => 'anaj_mandi',
                'label' => 'सरसों / राई (Mustard)',
                'brand' => '42% Oil Condition',
                'plant_location' => 'Alwar / Kota Mandi',
                'unit' => 'प्रति क्विंटल (100 Kg)',
                'rate_unit' => 'per_quintal',
                'default_rate' => 5800.00,
                'gst_mode' => 'included',
                'gst_pct' => 0.0,
                'loading_charge' => 25.0,
                'insurance_charge' => 0,
                'local_freight' => 40.0,
                'default_profit_type' => 'percent',
                'default_profit_val' => 4.0,
                'icon' => 'fa-sun',
                'color' => 'yellow',
            ],
            'anaj_rice' => [
                'key' => 'anaj_rice',
                'group' => 'anaj_mandi',
                'label' => 'चावल / बासमती धान',
                'brand' => 'Packaged / Loose',
                'plant_location' => 'Rice Mill Dispatch',
                'unit' => 'प्रति क्विंटल (100 Kg)',
                'rate_unit' => 'per_quintal',
                'default_rate' => 4200.00,
                'gst_mode' => 'extra',
                'gst_pct' => 5.0,
                'loading_charge' => 20.0,
                'insurance_charge' => 0,
                'local_freight' => 50.0,
                'default_profit_type' => 'percent',
                'default_profit_val' => 5.0,
                'icon' => 'fa-bowl-rice',
                'color' => 'teal',
            ],
            'anaj_dal' => [
                'key' => 'anaj_dal',
                'group' => 'anaj_mandi',
                'label' => 'तुअर / मूंग / मसूर दाल',
                'brand' => 'Sortex Mill',
                'plant_location' => 'Dal Mill / Mandi',
                'unit' => 'प्रति क्विंटल (100 Kg)',
                'rate_unit' => 'per_quintal',
                'default_rate' => 9600.00,
                'gst_mode' => 'extra',
                'gst_pct' => 5.0,
                'loading_charge' => 25.0,
                'insurance_charge' => 0,
                'local_freight' => 50.0,
                'default_profit_type' => 'percent',
                'default_profit_val' => 5.0,
                'icon' => 'fa-jar',
                'color' => 'amber',
            ],
        ];
    }

    /**
     * Load Seller's Saved Commodity Profiles (Rates + GST + Die + Loading + Insurance + Local Bhada + Profit)
     */
    public function getSellerState(User $user): array
    {
        $defs = self::getCommodityDefinitions();
        $savedProfiles = [];
        $history = [];
        $customDefs = [];

        $sellerPage = $user->sellerPage;
        if ($sellerPage && is_array($sellerPage->business_settings)) {
            $savedProfiles = $sellerPage->business_settings['commodity_profiles'] ?? [];
            $legacyRates = $sellerPage->business_settings['commodity_rates'] ?? [];
            $history = $sellerPage->business_settings['commodity_rate_history'] ?? [];
            $customDefs = $sellerPage->business_settings['custom_commodities'] ?? [];
        } else {
            $jsonPath = storage_path("app/commodity_rates_user_{$user->id}.json");
            if (file_exists($jsonPath)) {
                $decoded = json_decode(file_get_contents($jsonPath), true) ?: [];
                $savedProfiles = $decoded['commodity_profiles'] ?? [];
                $legacyRates = $decoded['commodity_rates'] ?? [];
                $history = $decoded['commodity_rate_history'] ?? [];
                $customDefs = $decoded['custom_commodities'] ?? [];
            } else {
                $legacyRates = [];
            }
        }

        $allDefs = array_merge($defs, $customDefs);
        $profiles = [];
        $rates = [];

        foreach ($allDefs as $k => $def) {
            $sp = $savedProfiles[$k] ?? [];
            $baseRate = isset($sp['rate']) ? floatval($sp['rate']) : (isset($legacyRates[$k]) ? floatval($legacyRates[$k]) : floatval($def['default_rate']));

            // Backwards compatibility: if old saved tmt rate was per kg (like 60) while new default is per_ton
            if (in_array($k, ['tmt_base_kg', 'tmt_kamdhenu', 'tmt_jindal_tata']) && ($sp['rate_unit'] ?? $def['rate_unit']) === 'per_ton' && $baseRate > 0 && $baseRate < 500) {
                $baseRate = $baseRate * 1000;
            }

            $profiles[$k] = [
                'rate' => $baseRate,
                'rate_unit' => $sp['rate_unit'] ?? ($def['rate_unit'] ?? 'per_unit'),
                'gst_mode' => $sp['gst_mode'] ?? ($def['gst_mode'] ?? 'extra'),
                'gst_pct' => isset($sp['gst_pct']) ? floatval($sp['gst_pct']) : floatval($def['gst_pct'] ?? 0),
                'loading_charge' => isset($sp['loading_charge']) ? floatval($sp['loading_charge']) : floatval($def['loading_charge'] ?? 0),
                'insurance_charge' => isset($sp['insurance_charge']) ? floatval($sp['insurance_charge']) : floatval($def['insurance_charge'] ?? 0),
                'local_freight' => isset($sp['local_freight']) ? floatval($sp['local_freight']) : floatval($def['local_freight'] ?? 0),
                'profit_type' => $sp['profit_type'] ?? ($def['default_profit_type'] ?? 'percent'),
                'profit_val' => isset($sp['profit_val']) ? floatval($sp['profit_val']) : floatval($def['default_profit_val'] ?? 4.0),
                'brand' => $sp['brand'] ?? ($def['brand'] ?? ''),
                'plant_location' => $sp['plant_location'] ?? ($def['plant_location'] ?? ''),
                'die_rates' => $sp['die_rates'] ?? ($def['die_rates'] ?? self::DEFAULT_TMT_DIE_PER_TON),
            ];
            $rates[$k] = $baseRate;
        }

        return [
            'rates' => $rates,
            'profiles' => $profiles,
            'history' => $history,
            'custom_commodities' => $customDefs,
            'definitions' => $allDefs,
        ];
    }

    /**
     * Persist Seller's Full Commodity Profiles, History & Custom Brand/Plant Groups
     */
    public function saveSellerState(User $user, array $profiles, array $history, array $customDefs = []): void
    {
        $ratesOnly = [];
        foreach ($profiles as $k => $p) {
            $ratesOnly[$k] = floatval($p['rate'] ?? 0);
        }

        $sellerPage = $user->sellerPage;
        if ($sellerPage) {
            $settings = $sellerPage->business_settings ?? [];
            $settings['commodity_profiles'] = $profiles;
            $settings['commodity_rates'] = $ratesOnly;
            $settings['commodity_rate_history'] = array_slice($history, 0, 30);
            if (!empty($customDefs)) {
                $settings['custom_commodities'] = $customDefs;
            }
            $sellerPage->business_settings = $settings;
            $sellerPage->save();
        } else {
            $jsonPath = storage_path("app/commodity_rates_user_{$user->id}.json");
            file_put_contents($jsonPath, json_encode([
                'commodity_profiles' => $profiles,
                'commodity_rates' => $ratesOnly,
                'commodity_rate_history' => array_slice($history, 0, 30),
                'custom_commodities' => $customDefs,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * Auto-Detect or Read Formula Configuration for a Product / Variant (including Brand/Plant & mm Die)
     */
    public function detectFormula(Product $product, ?ProductVariant $variant = null, array $allDefinitions = []): array
    {
        $pAttrs = is_array($product->attributes) ? $product->attributes : (json_decode($product->attributes ?? '[]', true) ?: []);
        $vAttrs = $variant ? (is_array($variant->attributes) ? $variant->attributes : (json_decode($variant->attributes ?? '[]', true) ?: [])) : [];

        if (!empty($vAttrs['rate_formula']['commodity_key'])) {
            return array_merge(['source' => 'saved_variant'], $vAttrs['rate_formula']);
        }
        if (!empty($pAttrs['rate_formula']['commodity_key']) && !$variant) {
            return array_merge(['source' => 'saved_product'], $pAttrs['rate_formula']);
        }

        $savedParent = $pAttrs['rate_formula'] ?? [];
        $haystack = strtolower($product->name . ' ' . ($product->brand ?? '') . ' ' . ($variant ? $variant->variant_name : '') . ' ' . ($product->category->name ?? '') . ' ' . ($product->group_name ?? ''));

        $commodityKey = $savedParent['commodity_key'] ?? null;

        if (!$commodityKey) {
            // Check custom brand/plant commodities first by matching brand or label keywords
            foreach ($allDefinitions as $defKey => $def) {
                if (!empty($def['is_custom'])) {
                    $bWord = strtolower(trim($def['brand'] ?? $def['label'] ?? ''));
                    if ($bWord && str_contains($haystack, $bWord)) {
                        $commodityKey = $defKey;
                        break;
                    }
                }
            }
        }

        if (!$commodityKey) {
            // 1. Jewellery
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
            // 2. Fabrication Roofing Sheets (Check Cement Sheet & Tin Sheet BEFORE generic Cement/Steel!)
            elseif (preg_match('/(cement\s*sheet|asbestos|everest|charminar|ramco|ac\s*sheet|सीमेंट\s*चादर)/i', $haystack)) {
                $commodityKey = 'fab_cement_sheet';
            }
            elseif (preg_match('/(profile\s*sheet|tin\s*sheet|colour\s*sheet|color\s*sheet|jsw\s*sheet|gi\s*sheet|roofing\s*sheet|chadar|टिन\s*चादर)/i', $haystack)) {
                $commodityKey = 'fab_tin_sheet';
            }
            elseif (preg_match('/(ms\s*pipe|gp\s*pipe|square\s*pipe|apollo\s*pipe|hollow\s*section|round\s*pipe)/i', $haystack)) {
                $commodityKey = 'fab_ms_gp_pipe';
            }
            elseif (preg_match('/(angle|channel|guarder|girder|i-beam|patti|flat\s*bar|एंगल|चैनल|पट्टी)/i', $haystack)) {
                $commodityKey = 'fab_ms_angle_channel';
            }
            // 3. Iron & TMT Steel (Brand-Aware!)
            elseif (preg_match('/(sariya|saria|tmt|rebar|loha|iron|steel rod|fe500|fe550|kamdhenu|jindal|tiscon|sail|prime gold)/i', $haystack)) {
                if (str_contains($haystack, 'kamdhenu')) {
                    $commodityKey = 'tmt_kamdhenu';
                } elseif (str_contains($haystack, 'jindal') || str_contains($haystack, 'tata') || str_contains($haystack, 'tiscon')) {
                    $commodityKey = 'tmt_jindal_tata';
                } else {
                    $commodityKey = 'tmt_base_kg';
                }
            }
            // 4. Cement Bags (Brand-Aware!)
            elseif (preg_match('/(cement|ultratech|ambuja|acc|bangur|birla|dalmia|shree|jk super|wonder|ppc|opc 43|opc 53)/i', $haystack)) {
                if (preg_match('/(ultratech|acc|ambuja|birla)/i', $haystack)) {
                    $commodityKey = 'cement_ultratech_acc';
                } else {
                    $commodityKey = 'cement_bag_base';
                }
            }
            // 5. Anaj Mandi
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

        // Trade-Type Formula Assembly
        if (in_array($commodityKey, ['gold_24k', 'gold_22k', 'gold_18k', 'silver_999']) || ($allDefinitions[$commodityKey]['group'] ?? '') === 'jewellery') {
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
                'source' => !empty($savedParent['commodity_key']) ? 'saved_product' : 'auto_detected',
            ];
        }

        if (in_array($commodityKey, ['fab_tin_sheet', 'fab_cement_sheet'])) {
            $lengthFt = floatval($savedParent['length_ft'] ?? ($pAttrs['length_ft'] ?? 0));
            if ($lengthFt <= 0) {
                if (preg_match('/(\d+(?:\.\d+)?)\s*(?:ft|feet|फीट|फुट)/i', $haystack, $fm)) {
                    $lengthFt = floatval($fm[1]);
                } elseif (preg_match('/(\d+(?:\.\d+)?)\s*(?:m|mtr|meter|मीटर)\b/i', $haystack, $mm)) {
                    $lengthFt = round(floatval($mm[1]) * 3.28084, 2); // convert meter to ft
                } else {
                    $lengthFt = 10.0; // standard 10 ft sheet
                }
            }

            return [
                'commodity_key' => $commodityKey,
                'trade_type' => 'fab_sheet',
                'length_ft' => $lengthFt,
                'source' => !empty($savedParent['commodity_key']) ? 'saved_product' : 'auto_detected',
            ];
        }

        if (in_array($commodityKey, ['fab_ms_angle_channel', 'fab_ms_gp_pipe']) || ($allDefinitions[$commodityKey]['group'] ?? '') === 'fabrication_structure') {
            $weightKg = floatval($savedParent['weight_kg'] ?? ($pAttrs['rod_weight_kg'] ?? 0));
            $sectionLabel = $savedParent['section_size'] ?? '';
            if ($weightKg <= 0) {
                foreach (self::FABRICATION_SECTION_WEIGHTS_KG as $secKey => $secW) {
                    if (str_contains(str_replace(' ', '', $haystack), $secKey)) {
                        $weightKg = $secW;
                        $sectionLabel = $secKey;
                        break;
                    }
                }
            }
            if ($weightKg <= 0 && preg_match('/(\d+(?:\.\d+)?)\s*kg/i', $haystack, $kgm)) {
                $weightKg = floatval($kgm[1]);
            }
            if ($weightKg <= 0) $weightKg = 18.0; // default 20ft structural piece

            return [
                'commodity_key' => $commodityKey,
                'trade_type' => 'fab_steel',
                'section_size' => $sectionLabel ?: '6m/20ft Piece',
                'weight_kg' => $weightKg,
                'source' => !empty($savedParent['commodity_key']) ? 'saved_product' : 'auto_detected',
            ];
        }

        if (str_starts_with($commodityKey, 'tmt_') || (($allDefinitions[$commodityKey]['group'] ?? '') === 'iron_cement' && !str_contains($commodityKey, 'cement') && !preg_match('/cement/i', $haystack))) {
            $mmKey = $savedParent['mm_size'] ?? null;
            $weightKg = floatval($savedParent['weight_kg'] ?? ($pAttrs['rod_weight_kg'] ?? 0));
            if (preg_match('/\b(6|8|10|12|16|20|25|32)\s*mm\b/i', $haystack, $mmMatch)) {
                $mmKey = strtolower($mmMatch[1]) . 'mm';
                if ($weightKg <= 0) {
                    $weightKg = self::TMT_ROD_WEIGHTS_KG[$mmKey] ?? 10.66;
                }
            }
            if (preg_match('/quintal|100\s*kg/i', $haystack)) {
                $weightKg = 100.0;
            } elseif (preg_match('/ton|1000\s*kg|mt\b/i', $haystack)) {
                $weightKg = 1000.0;
            } elseif ($weightKg <= 0 && preg_match('/(\d+(?:\.\d+)?)\s*kg/i', $haystack, $kgm)) {
                $weightKg = floatval($kgm[1]);
            }
            if ($weightKg <= 0) $weightKg = 10.66;

            return [
                'commodity_key' => $commodityKey,
                'trade_type' => 'tmt_steel',
                'mm_size' => $mmKey ?: '12mm',
                'weight_kg' => $weightKg,
                'die_override_per_ton' => isset($savedParent['die_override_per_ton']) ? floatval($savedParent['die_override_per_ton']) : null,
                'source' => !empty($savedParent['commodity_key']) ? 'saved_product' : 'auto_detected',
            ];
        }

        if (str_contains($commodityKey, 'cement') || preg_match('/cement/i', $haystack)) {
            $bagsCount = floatval($savedParent['bags_count'] ?? 1.0);
            return [
                'commodity_key' => $commodityKey,
                'trade_type' => 'cement',
                'bags_count' => $bagsCount,
                'source' => !empty($savedParent['commodity_key']) ? 'saved_product' : 'auto_detected',
            ];
        }

        // Grain / Mandi
        $unitKg = floatval($savedParent['weight_kg'] ?? 0);
        if ($unitKg <= 0) {
            if (preg_match('/quintal|100\s*kg/i', $haystack)) $unitKg = 100.0;
            elseif (preg_match('/bori|50\s*kg/i', $haystack)) $unitKg = 50.0;
            elseif (preg_match('/katta|30\s*kg/i', $haystack)) $unitKg = 30.0;
            elseif (preg_match('/25\s*kg/i', $haystack)) $unitKg = 25.0;
            elseif (preg_match('/10\s*kg/i', $haystack)) $unitKg = 10.0;
            elseif (preg_match('/(\d+(?:\.\d+)?)\s*kg/i', $haystack, $m)) $unitKg = floatval($m[1]);
            else $unitKg = 100.0;
        }

        return [
            'commodity_key' => $commodityKey,
            'trade_type' => 'mandi_grain',
            'weight_kg' => $unitKg,
            'source' => !empty($savedParent['commodity_key']) ? 'saved_product' : 'auto_detected',
        ];
    }

    /**
     * 🧮 Full Costing Pipeline Calculator (Base + Die + Loading + Insurance + GST Mode + Local Plant-to-Godown Bhada + Profit)
     */
    public function calculatePrice(array $formula, array $profiles): array
    {
        $key = $formula['commodity_key'] ?? null;
        if (!$key || !isset($profiles[$key])) {
            return ['valid' => false];
        }

        $prof = $profiles[$key];
        $baseRate = floatval($prof['rate'] ?? 0);
        $rateUnit = $prof['rate_unit'] ?? 'per_unit';
        $gstMode = $formula['gst_mode'] ?? ($prof['gst_mode'] ?? 'extra'); // 'extra' or 'included'
        $gstPct = isset($formula['gst_pct']) ? floatval($formula['gst_pct']) : floatval($prof['gst_pct'] ?? 0);
        $loading = floatval($formula['loading_override'] ?? ($prof['loading_charge'] ?? 0));
        $insurance = floatval($formula['insurance_override'] ?? ($prof['insurance_charge'] ?? 0));
        $localBhada = floatval($formula['freight_override'] ?? ($prof['local_freight'] ?? 0));
        $profitType = $formula['profit_type'] ?? ($prof['profit_type'] ?? 'percent');
        $profitVal = isset($formula['profit_val']) ? floatval($formula['profit_val']) : floatval($prof['profit_val'] ?? 4.0);

        $type = $formula['trade_type'] ?? 'mandi_grain';

        // 1. 💎 JEWELLERY ENGINE
        if ($type === 'jewellery') {
            $perGramBase = str_contains($key, 'silver') ? ($baseRate / 1000.0) : ($baseRate / 10.0);
            $weightGm = floatval($formula['weight_gm'] ?? 10.0);
            $metalValue = $weightGm * $perGramBase;

            $makingType = $formula['making_type'] ?? 'percent';
            $makingVal = floatval($formula['making_val'] ?? 12.0);
            $makingFee = ($makingType === 'percent')
                ? ($metalValue * ($makingVal / 100.0))
                : (($makingType === 'per_gm') ? ($weightGm * $makingVal) : $makingVal);

            $wastagePct = floatval($formula['wastage_pct'] ?? 0);
            $wastageFee = $metalValue * ($wastagePct / 100.0);

            // Subtotal + Hallmark/Insurance + Local Bhada
            $taxableSub = $metalValue + $makingFee + $wastageFee + $insurance + $loading;
            $gstMult = ($gstMode === 'extra' && $gstPct > 0) ? (1 + $gstPct / 100.0) : 1.0;
            $afterGst = $taxableSub * $gstMult;

            // Optional Extra Profit + Local Bhada
            $afterBhada = $afterGst + $localBhada;
            $profitAmt = ($profitType === 'flat') ? $profitVal : ($afterBhada * ($profitVal / 100.0));
            $retail = round($afterBhada + $profitAmt);
            $wholesale = round($afterBhada + ($profitAmt * 0.5));

            $gstText = ($gstMode === 'extra' && $gstPct > 0) ? "+{$gstPct}% GST" : "GST Included";
            $extraTxt = ($insurance > 0) ? " + ₹{$insurance} Ins/HUID" : "";

            return [
                'valid' => true,
                'purchase_price' => round($afterBhada, 2),
                'wholesale_price' => $wholesale,
                'retail_price' => $retail,
                'explanation' => "{$weightGm}g × ₹" . number_format($perGramBase, 1) . "/g + मेकिंग ₹" . round($makingFee) . "{$extraTxt} ({$gstText})",
            ];
        }

        // 2. 🏗️ IRON / TMT STEEL ENGINE (Basic + Die + Loading + Insurance -> GST -> Local Plant-to-Godown Bhada -> Profit)
        if ($type === 'tmt_steel') {
            $weightKg = floatval($formula['weight_kg'] ?? 10.66);
            $mmSize = $formula['mm_size'] ?? '12mm';

            // Normalize basic rate to per Ton
            $basicPerTon = ($rateUnit === 'per_kg') ? ($baseRate * 1000.0) : $baseRate;

            // Die rate per Ton
            $dieTable = $prof['die_rates'] ?? self::DEFAULT_TMT_DIE_PER_TON;
            $diePerTon = isset($formula['die_override_per_ton']) && $formula['die_override_per_ton'] !== null
                ? floatval($formula['die_override_per_ton'])
                : floatval($dieTable[$mmSize] ?? 0);

            // Ex-Mill Taxable per Ton
            $millPerTon = $basicPerTon + $diePerTon + $loading + $insurance;

            // Apply GST if Basic is Without GST ('extra')
            $gstMult = ($gstMode === 'extra' && $gstPct > 0) ? (1 + $gstPct / 100.0) : 1.0;
            $millWithGstPerTon = $millPerTon * $gstMult;

            // Add Local Plant-to-Godown Bhada (₹/Ton) -> True Godown Landing Cost!
            $godownLandingPerTon = $millWithGstPerTon + $localBhada;
            $godownLandingPerKg = $godownLandingPerTon / 1000.0;
            $rodLandingCost = $godownLandingPerKg * $weightKg;

            // Add Profit
            $profitPerKg = ($profitType === 'flat_kg') ? $profitVal : (($profitType === 'flat_ton') ? ($profitVal / 1000.0) : ($godownLandingPerKg * ($profitVal / 100.0)));
            $finalPerKg = $godownLandingPerKg + $profitPerKg;
            $retail = round($finalPerKg * $weightKg);
            $wholesale = round(($godownLandingPerKg + ($profitPerKg * 0.5)) * $weightKg);

            $gstLabel = ($gstMode === 'extra' && $gstPct > 0) ? "+{$gstPct}% GST" : "GST सहित";
            $profLabel = ($profitType === 'flat_kg') ? "+₹{$profitVal}/Kg" : (($profitType === 'flat_ton') ? "+₹{$profitVal}/T" : "+{$profitVal}%");

            return [
                'valid' => true,
                'purchase_price' => round($rodLandingCost, 2),
                'wholesale_price' => $wholesale,
                'retail_price' => $retail,
                'explanation' => "{$mmSize} ({$weightKg}Kg): Mill ₹" . number_format($basicPerTon) . " + Die ₹{$diePerTon} + Load/Ins ₹" . ($loading + $insurance) . " ({$gstLabel}) + भाड़ा ₹{$localBhada}/T = पड़तल ₹" . number_format($godownLandingPerKg, 2) . "/Kg {$profLabel}",
            ];
        }

        // 2B. 🛠️ FABRICATION STRUCTURAL STEEL (MS Angle / Channel / Flat / MS-GP Pipe)
        if ($type === 'fab_steel') {
            $weightKg = floatval($formula['weight_kg'] ?? 18.0);
            $sectionSize = $formula['section_size'] ?? '20ft Piece';

            if ($rateUnit === 'per_ton') {
                $basicPerTon = $baseRate;
                $millPerTon = $basicPerTon + $loading + $insurance;
                $gstMult = ($gstMode === 'extra' && $gstPct > 0) ? (1 + $gstPct / 100.0) : 1.0;
                $godownPerTon = ($millPerTon * $gstMult) + $localBhada;
                $godownPerKg = $godownPerTon / 1000.0;
            } else {
                // per_kg (like MS/GP Pipes ₹64/Kg)
                $millPerKg = $baseRate + $loading + $insurance;
                $gstMult = ($gstMode === 'extra' && $gstPct > 0) ? (1 + $gstPct / 100.0) : 1.0;
                $godownPerKg = ($millPerKg * $gstMult) + $localBhada;
            }

            $pieceLanding = $godownPerKg * $weightKg;
            $profitPerKg = ($profitType === 'flat_kg') ? $profitVal : (($profitType === 'flat_ton') ? ($profitVal / 1000.0) : ($godownPerKg * ($profitVal / 100.0)));
            $finalPerKg = $godownPerKg + $profitPerKg;
            $retail = round($finalPerKg * $weightKg);
            $wholesale = round(($godownPerKg + ($profitPerKg * 0.5)) * $weightKg);
            $gstLabel = ($gstMode === 'extra' && $gstPct > 0) ? "+{$gstPct}% GST" : "GST सहित";

            return [
                'valid' => true,
                'purchase_price' => round($pieceLanding, 2),
                'wholesale_price' => $wholesale,
                'retail_price' => $retail,
                'explanation' => "{$sectionSize} ({$weightKg}Kg): पड़तल ₹" . number_format($godownPerKg, 2) . "/Kg ({$gstLabel} + Load/Ins + भाड़ा) + {$profitVal}% लाभ",
            ];
        }

        // 2C. 🏠 FABRICATION ROOFING SHEETS (Color Coated Tin Sheet / Cement Everest AC Sheet)
        if ($type === 'fab_sheet') {
            $lengthFt = floatval($formula['length_ft'] ?? 10.0);
            $rawSheetCost = $baseRate * $lengthFt;
            $gstMult = ($gstMode === 'extra' && $gstPct > 0) ? (1 + $gstPct / 100.0) : 1.0;
            // Loading + Breakage Insurance + Local Bhada per sheet
            $sheetLanding = ($rawSheetCost * $gstMult) + $loading + $insurance + $localBhada;

            $profitAmt = ($profitType === 'flat') ? $profitVal : ($sheetLanding * ($profitVal / 100.0));
            $retail = round($sheetLanding + $profitAmt);
            $wholesale = round($sheetLanding + ($profitAmt * 0.5));
            $gstLabel = ($gstMode === 'extra' && $gstPct > 0) ? "+{$gstPct}% GST" : "GST सहित";

            return [
                'valid' => true,
                'purchase_price' => round($sheetLanding, 2),
                'wholesale_price' => $wholesale,
                'retail_price' => $retail,
                'explanation' => "{$lengthFt} Ft शीट × ₹{$baseRate}/Ft ({$gstLabel}) + Load ₹{$loading} + Ins ₹{$insurance} + भाड़ा ₹{$localBhada} + {$profitVal}% लाभ",
            ];
        }

        // 3. 🧱 CEMENT ENGINE (Company Base -> GST -> Loading/Hamali + Plant-to-Godown Bhada -> Profit)
        if ($type === 'cement') {
            $bags = floatval($formula['bags_count'] ?? 1.0);
            $gstMult = ($gstMode === 'extra' && $gstPct > 0) ? (1 + $gstPct / 100.0) : 1.0;
            $exPlantWithGst = $baseRate * $gstMult;
            $landingPerBag = $exPlantWithGst + $loading + $insurance + $localBhada;

            $profitPerBag = ($profitType === 'flat') ? $profitVal : ($landingPerBag * ($profitVal / 100.0));
            $retail = round(($landingPerBag + $profitPerBag) * $bags);
            $wholesale = round(($landingPerBag + ($profitPerBag * 0.5)) * $bags);

            $gstLabel = ($gstMode === 'extra' && $gstPct > 0) ? "+{$gstPct}% GST" : "GST सहित";

            return [
                'valid' => true,
                'purchase_price' => round($landingPerBag * $bags, 2),
                'wholesale_price' => $wholesale,
                'retail_price' => $retail,
                'explanation' => "{$bags} बैग × Base ₹{$baseRate} ({$gstLabel}) + हमाली ₹{$loading} + भाड़ा ₹{$localBhada} = पड़तल ₹" . round($landingPerBag) . "/बैग + {$profitVal}% लाभ",
            ];
        }

        // 4. 🌾 ANAJ MANDI ENGINE (Mandi Quintal -> Hamali/Tulai + GST -> Mandi-to-Godown Bhada -> Profit)
        $unitKg = floatval($formula['weight_kg'] ?? 100.0);
        $scale = $unitKg / 100.0; // relative to 1 Quintal (100 Kg)
        $gstMult = ($gstMode === 'extra' && $gstPct > 0) ? (1 + $gstPct / 100.0) : 1.0;
        $quintalWithGst = ($baseRate + $loading + $insurance) * $gstMult + $localBhada;
        $packLanding = $quintalWithGst * $scale;

        $profitAmt = ($profitType === 'flat') ? $profitVal : ($packLanding * ($profitVal / 100.0));
        $retail = round($packLanding + $profitAmt);
        $wholesale = round($packLanding + ($profitAmt * 0.5));
        $gstLabel = ($gstMode === 'extra' && $gstPct > 0) ? "+{$gstPct}% GST" : "Nil/Incl GST";

        return [
            'valid' => true,
            'purchase_price' => round($packLanding, 2),
            'wholesale_price' => $wholesale,
            'retail_price' => $retail,
            'explanation' => "{$unitKg}Kg @ ₹{$baseRate}/Q + हमाली ₹{$loading} + भाड़ा ₹{$localBhada} ({$gstLabel}) + {$profitVal}% लाभ",
        ];
    }

    /**
     * ⚡ Apply Updated Commodity Profiles (Rates, GST Mode, Die, Loading, Insurance, Bhada, Profit) to All Linked Products
     */
    public function applyMasterProfiles(User $user, array $incomingProfiles, string $source = 'Master Rate Panel'): array
    {
        $state = $this->getSellerState($user);
        $oldProfiles = $state['profiles'];
        $mergedProfiles = $oldProfiles;
        $defs = $state['definitions'];

        foreach ($incomingProfiles as $k => $inProf) {
            if (!isset($mergedProfiles[$k])) continue;
            if (is_array($inProf)) {
                $mergedProfiles[$k] = array_merge($mergedProfiles[$k], $inProf);
                if (isset($inProf['rate'])) $mergedProfiles[$k]['rate'] = floatval($inProf['rate']);
                if (isset($inProf['gst_pct'])) $mergedProfiles[$k]['gst_pct'] = floatval($inProf['gst_pct']);
                if (isset($inProf['loading_charge'])) $mergedProfiles[$k]['loading_charge'] = floatval($inProf['loading_charge']);
                if (isset($inProf['insurance_charge'])) $mergedProfiles[$k]['insurance_charge'] = floatval($inProf['insurance_charge']);
                if (isset($inProf['local_freight'])) $mergedProfiles[$k]['local_freight'] = floatval($inProf['local_freight']);
                if (isset($inProf['profit_val'])) $mergedProfiles[$k]['profit_val'] = floatval($inProf['profit_val']);
            } elseif (is_numeric($inProf)) {
                $mergedProfiles[$k]['rate'] = floatval($inProf);
            }
        }

        $updatedProductsCount = 0;
        $updatedVariantsCount = 0;
        $affectedDetails = [];
        $updatedKeys = array_keys($incomingProfiles);

        DB::transaction(function () use ($user, $mergedProfiles, $defs, $updatedKeys, &$updatedProductsCount, &$updatedVariantsCount, &$affectedDetails) {
            $products = Product::where('user_id', $user->id)->with(['variants', 'category'])->get();

            foreach ($products as $prod) {
                $parentChanged = false;
                $variants = $prod->variants;

                if ($variants && $variants->isNotEmpty()) {
                    $lowestRetail = null;
                    $lowestWholesale = null;
                    $lowestPurchase = null;

                    foreach ($variants as $var) {
                        $formula = $this->detectFormula($prod, $var, $defs);
                        if (empty($formula['commodity_key']) || !in_array($formula['commodity_key'], $updatedKeys)) {
                            continue;
                        }

                        $calc = $this->calculatePrice($formula, $mergedProfiles);
                        if (!$calc['valid']) continue;

                        $oldPrice = (float)$var->retail_price;
                        $var->purchase_price = $calc['purchase_price'];
                        $var->wholesale_price = $calc['wholesale_price'];
                        $var->retail_price = $calc['retail_price'];
                        $var->gst_percent = $mergedProfiles[$formula['commodity_key']]['gst_pct'] ?? $var->gst_percent;
                        $var->price_effective_date = now()->toDateString();

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
                    $formula = $this->detectFormula($prod, null, $defs);
                    if (!empty($formula['commodity_key']) && in_array($formula['commodity_key'], $updatedKeys)) {
                        $calc = $this->calculatePrice($formula, $mergedProfiles);
                        if ($calc['valid']) {
                            $oldPrice = (float)$prod->price;
                            $prod->purchase_price = $calc['purchase_price'];
                            $prod->wholesale_price = $calc['wholesale_price'];
                            $prod->price = $calc['retail_price'];
                            $prod->gst_percent = $mergedProfiles[$formula['commodity_key']]['gst_pct'] ?? $prod->gst_percent;
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

        // Build History Log
        $history = $state['history'];
        $changesSummary = [];
        foreach ($incomingProfiles as $k => $inProf) {
            if (!isset($defs[$k])) continue;
            $oldR = floatval($oldProfiles[$k]['rate'] ?? 0);
            $newR = floatval($mergedProfiles[$k]['rate'] ?? 0);
            $gstModeTxt = ($mergedProfiles[$k]['gst_mode'] ?? 'extra') === 'included' ? 'GST Incl' : ('+' . ($mergedProfiles[$k]['gst_pct'] ?? 0) . '% GST');
            $lbl = $defs[$k]['label'] ?? $k;
            if ($oldR != $newR || count($incomingProfiles) <= 3) {
                $changesSummary[] = "{$lbl} ({$gstModeTxt}): ₹" . number_format($oldR) . " → ₹" . number_format($newR);
            }
        }

        if (!empty($changesSummary) || $updatedProductsCount > 0) {
            array_unshift($history, [
                'timestamp' => now()->format('d M Y, h:i A'),
                'source' => $source,
                'summary' => implode(' | ', $changesSummary) ?: 'Master Costing Update',
                'products_updated' => $updatedProductsCount,
                'variants_updated' => $updatedVariantsCount,
            ]);
        }

        $this->saveSellerState($user, $mergedProfiles, $history, $state['custom_commodities']);

        return [
            'profiles' => $mergedProfiles,
            'updated_products' => $updatedProductsCount,
            'updated_variants' => $updatedVariantsCount,
            'affected_details' => $affectedDetails,
            'changes_summary' => $changesSummary,
        ];
    }

    /**
     * Backwards-compatible wrapper for applyMasterRates
     */
    public function applyMasterRates(User $user, array $updatedRates, string $source = 'Manual Update'): array
    {
        $profilesPatch = [];
        foreach ($updatedRates as $k => $v) {
            $profilesPatch[$k] = is_array($v) ? $v : ['rate' => floatval($v)];
        }
        return $this->applyMasterProfiles($user, $profilesPatch, $source);
    }

    /**
     * 🤖 AI Voice & Messy Broker WhatsApp Rate Message Parser v2
     * Understands Brand, Plant Location, GST Included/Extra, Die Rates, Loading, Insurance & Local Bhada!
     */
    public function parseVoiceCommand(string $transcript, User $user, ?string $apiKey = null): array
    {
        $state = $this->getSellerState($user);
        $profiles = $state['profiles'];
        $defs = $state['definitions'];

        $transcriptClean = trim($transcript);
        $parsedUpdates = [];
        $provider = 'Smart Broker & Hinglish Engine';

        $openAiKey = ($apiKey && str_starts_with($apiKey, 'sk-')) ? $apiKey : (env('OPENAI_KEY') ?: env('OPENAI_API_KEY'));
        $geminiKey = ($apiKey && str_starts_with($apiKey, 'AIza')) ? $apiKey : (env('GEMINI_API_KEY') ?: env('GEMINI_KEY'));

        $keysDesc = [];
        foreach ($defs as $k => $d) {
            $p = $profiles[$k];
            $keysDesc[] = "- key: '{$k}' | name: '{$d['label']}' | brand: '{$p['brand']}' | plant: '{$p['plant_location']}' | unit: '{$d['unit']}' | current_rate: {$p['rate']} | gst_mode: {$p['gst_mode']} | gst_pct: {$p['gst_pct']}";
        }
        $keysBlock = implode("\n", $keysDesc);

        $aiPrompt = "You are an expert Indian Mandi, Steel/TMT Mill, Cement & Sarafa Bullion Broker AI Parser.
A merchant pasted a WhatsApp broker message or spoke this Hindi/Hinglish rate update:
\"\"\"
{$transcriptClean}
\"\"\"

Available Commodity/Brand Keys:
{$keysBlock}

Extract EVERY commodity rate update mentioned, including:
1. key: best matching commodity key (notice brand differences like Kamdhenu vs Jindal vs Tata vs UltraTech/ACC vs general!).
2. new_rate: base rate number (for TMT if > 1000 treat as Per Ton; for Cement per Bag; for Gold per 10gm; for Silver per Kg; for Grains per Quintal).
3. gst_mode: 'included' if message says 'gst paid / gst sahit / with gst / incl gst', OR 'extra' if '+ gst / plus gst / bina gst / without gst' (keep current if unspecified).
4. gst_pct: numeric GST % if mentioned (e.g. 18, 28, 3, 5).
5. loading_charge, insurance_charge, local_freight (bhada/transport): numeric if mentioned!
6. plant_location: if a plant city like Raipur, Bhiwadi, Jalna, Patratu, Indore is mentioned.
7. die_8mm, die_10mm: if die rates mentioned (e.g. '8mm 1500 die').
8. reason_hi: short crisp Hindi explanation of what was parsed.

Return ONLY valid JSON:
{\"updates\": [{\"key\": \"...\", \"new_rate\": 0, \"gst_mode\": \"extra\", \"gst_pct\": null, \"loading_charge\": null, \"insurance_charge\": null, \"local_freight\": null, \"plant_location\": null, \"die_8mm\": null, \"die_10mm\": null, \"reason_hi\": \"...\"}]}";

        if ($openAiKey && (str_starts_with($openAiKey, 'sk-') || !$geminiKey)) {
            try {
                $resp = Http::withToken($openAiKey)->timeout(18)->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Output strict JSON only.'],
                        ['role' => 'user', 'content' => $aiPrompt],
                    ],
                    'temperature' => 0.0,
                ]);
                if ($resp->successful()) {
                    $decoded = $this->extractJson($resp->json('choices.0.message.content'));
                    if (!empty($decoded['updates'])) {
                        $parsedUpdates = $decoded['updates'];
                        $provider = 'OpenAI Broker AI';
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Voice/Broker OpenAI error: ' . $e->getMessage());
            }
        }

        if (empty($parsedUpdates) && $geminiKey) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$geminiKey}";
                $resp = Http::timeout(18)->post($url, [
                    'contents' => [['parts' => [['text' => $aiPrompt]]]],
                    'generationConfig' => ['temperature' => 0.0],
                ]);
                if ($resp->successful()) {
                    $decoded = $this->extractJson($resp->json('candidates.0.content.parts.0.text'));
                    if (!empty($decoded['updates'])) {
                        $parsedUpdates = $decoded['updates'];
                        $provider = 'Gemini Broker AI';
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Voice/Broker Gemini error: ' . $e->getMessage());
            }
        }

        if (empty($parsedUpdates)) {
            $parsedUpdates = $this->fallbackHindiBrokerParse($transcriptClean, $profiles, $defs);
        }

        $products = Product::where('user_id', $user->id)->with(['variants', 'category'])->get();
        $enriched = [];

        foreach ($parsedUpdates as $upd) {
            $k = $upd['key'] ?? '';
            if (!isset($defs[$k])) continue;
            $newVal = round(floatval($upd['new_rate'] ?? 0), 2);
            if ($newVal <= 0) continue;

            // Count affected items
            $matchCount = 0;
            foreach ($products as $p) {
                if ($p->variants && $p->variants->isNotEmpty()) {
                    foreach ($p->variants as $v) {
                        $f = $this->detectFormula($p, $v, $defs);
                        if (($f['commodity_key'] ?? '') === $k) $matchCount++;
                    }
                } else {
                    $f = $this->detectFormula($p, null, $defs);
                    if (($f['commodity_key'] ?? '') === $k) $matchCount++;
                }
            }

            $oldProf = $profiles[$k];
            $patch = ['rate' => $newVal];
            if (!empty($upd['gst_mode'])) $patch['gst_mode'] = $upd['gst_mode'];
            if (isset($upd['gst_pct']) && is_numeric($upd['gst_pct'])) $patch['gst_pct'] = floatval($upd['gst_pct']);
            if (isset($upd['loading_charge']) && is_numeric($upd['loading_charge'])) $patch['loading_charge'] = floatval($upd['loading_charge']);
            if (isset($upd['insurance_charge']) && is_numeric($upd['insurance_charge'])) $patch['insurance_charge'] = floatval($upd['insurance_charge']);
            if (isset($upd['local_freight']) && is_numeric($upd['local_freight'])) $patch['local_freight'] = floatval($upd['local_freight']);
            if (!empty($upd['plant_location'])) $patch['plant_location'] = $upd['plant_location'];

            $enriched[] = [
                'key' => $k,
                'label' => $defs[$k]['label'],
                'unit' => $defs[$k]['unit'],
                'old_rate' => floatval($oldProf['rate']),
                'new_rate' => $newVal,
                'diff' => round($newVal - floatval($oldProf['rate']), 2),
                'gst_mode' => $patch['gst_mode'] ?? $oldProf['gst_mode'],
                'gst_pct' => $patch['gst_pct'] ?? $oldProf['gst_pct'],
                'patch' => $patch,
                'affected_items_count' => $matchCount,
                'reason_hi' => $upd['reason_hi'] ?? "पार्स किया गया नया भाव ₹{$newVal}",
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
     * Built-in Broker WhatsApp + Hindi Speech Fallback Parser
     */
    private function fallbackHindiBrokerParse(string $text, array $profiles, array $defs): array
    {
        $normalized = mb_strtolower($text, 'UTF-8');
        $normalized = preg_replace('/(\d),(\d)/', '$1$2', $normalized);
        $normalized = preg_replace_callback('/(\d+(?:\.\d+)?)\s*k\b/i', function ($m) {
            if (in_array($m[1], ['24', '22', '18', '14', '9'])) {
                return $m[0];
            }
            return (string)(floatval($m[1]) * 1000);
        }, $normalized);
        $normalized = preg_replace_callback('/(\d+(?:\.\d+)?)\s*(?:hazar|hajar|हजार|हज़ार)\s*(\d+)?/iu', function ($m) {
            return (string)(floatval($m[1]) * 1000 + (isset($m[2]) ? floatval($m[2]) : 0));
        }, $normalized);

        $synonymPatterns = [
            'gold_24k' => '/(?:24\s*k|24\s*carat|24\s*कैरेट|शुद्ध सोना|pure gold)/iu',
            'gold_18k' => '/(?:18\s*k|18\s*carat|18\s*कैरेट)/iu',
            'gold_22k' => '/(?:22\s*k|22\s*carat|22\s*कैरेट|916|sona|सोना|gold|गोल्ड)/iu',
            'silver_999' => '/(?:chandi|चांदी|चाँदी|silver|सिल्वर)/iu',
            'fab_cement_sheet' => '/(?:cement\s*sheet|asbestos|everest|charminar|ramco|सीमेंट\s*चादर|एवरेस्ट)/iu',
            'fab_tin_sheet' => '/(?:tin\s*sheet|profile\s*sheet|jsw\s*sheet|colour\s*sheet|color\s*sheet|chadar|टिन\s*चादर|प्रोफाइल\s*शीट)/iu',
            'fab_ms_gp_pipe' => '/(?:ms\s*pipe|gp\s*pipe|square\s*pipe|apollo\s*pipe|चौकोर\s*पाइप|गोल\s*पाइप)/iu',
            'fab_ms_angle_channel' => '/(?:angle|channel|guarder|patti|एंगल|चैनल|पट्टी|गार्डर)/iu',
            'tmt_kamdhenu' => '/(?:kamdhenu|कामधेनु)/iu',
            'tmt_jindal_tata' => '/(?:jindal|tata|tiscon|sail|जिंदल|टाटा)/iu',
            'tmt_base_kg' => '/(?:sariya|saria|सरिया|loha|लोहा|tmt|steel|स्टील)/iu',
            'cement_ultratech_acc' => '/(?:ultratech|acc|ambuja|अल्ट्राटेक|अंबुजा)/iu',
            'cement_bag_base' => '/(?:cement|सीमेंट|bangur)/iu',
            'anaj_gehu' => '/(?:gehu|gehun|गेहूं|गेहूँ|wheat|sharbati)/iu',
            'anaj_chana' => '/(?:chana|चना|kabuli)/iu',
            'anaj_soyabean' => '/(?:soyabean|soya|सोयाबीन|सोया)/iu',
            'anaj_sarson' => '/(?:sarson|सरसों|mustard|rai)/iu',
            'anaj_rice' => '/(?:rice|chawal|चावल|basmati|बासमती|dhan|धान)/iu',
            'anaj_dal' => '/(?:dal|दाल|toor|तुअर|moong|मूंग|masoor|मसूर)/iu',
        ];

        // Also scan custom definitions
        foreach ($defs as $dk => $dv) {
            if (!empty($dv['is_custom'])) {
                $bw = preg_quote(mb_strtolower($dv['brand'] ?: $dv['label'], 'UTF-8'), '/');
                $synonymPatterns[$dk] = "/{$bw}/iu";
            }
        }

        // Split by newline, comma+space (not inside 73,500), or conjunctions
        $clauses = preg_split('/(?:\r\n|\r|\n|,\s+|\s+aur\s+|\s+और\s+|&|\s+tatha\s+)/iu', $normalized);
        $results = [];
        $usedKeys = [];

        foreach ($clauses as $clause) {
            $clause = trim($clause);
            if (empty($clause)) continue;

            // Detect GST Mode in clause
            $gstMode = null;
            if (preg_match('/(?:gst\s*paid|gst\s*sahit|with\s*gst|incl(?:uding)?\s*gst|जीएसटी\s*सहित)/iu', $clause)) {
                $gstMode = 'included';
            } elseif (preg_match('/(?:\+\s*gst|plus\s*gst|without\s*gst|bina\s*gst|extra\s*gst|जीएसटी\s*अलग|बिन\s*जीएसटी)/iu', $clause)) {
                $gstMode = 'extra';
            }

            // Detect Loading / Insurance / Bhada explicitly mentioned in clause
            $loadingVal = null;
            if (preg_match('/(?:loading|लोडिंग|hamali|हमाली)\s*[:₹]?\s*(\d+(?:\.\d+)?)/iu', $clause, $lm)) {
                $loadingVal = floatval($lm[1]);
            }
            $insVal = null;
            if (preg_match('/(?:insurance|ins|बीमा|इंश्योरेंस)\s*[:₹]?\s*(\d+(?:\.\d+)?)/iu', $clause, $im)) {
                $insVal = floatval($im[1]);
            }
            $bhadaVal = null;
            if (preg_match('/(?:bhada|भाड़ा|freight|transport|किराया)\s*[:₹]?\s*(\d+(?:\.\d+)?)/iu', $clause, $bm)) {
                $bhadaVal = floatval($bm[1]);
            }

            // Strip recognized auxiliary fields & carat before grabbing main base price
            $cleanForNum = preg_replace('/\b(?:24|22|18)\s*(?:k|ct|carat|कैरेट)/iu', '', $clause);
            $cleanForNum = preg_replace('/(?:loading|लोडिंग|hamali|हमाली|insurance|ins|बीमा|bhada|भाड़ा|freight|transport)\s*[:₹]?\s*\d+(?:\.\d+)?/iu', '', $cleanForNum);
            $cleanForNum = preg_replace('/\b(?:8|10|12|16|20|25|32)\s*mm\s*\+?\s*\d+/iu', '', $cleanForNum);

            if (!preg_match('/(\d+(?:\.\d+)?)/', $cleanForNum, $numMatch)) {
                continue;
            }
            $numVal = floatval($numMatch[1]);

            $matchedKey = null;
            foreach ($synonymPatterns as $key => $regex) {
                if (preg_match($regex, $clause)) {
                    if ($key === 'gold_22k' && (preg_match($synonymPatterns['gold_24k'], $clause) || preg_match($synonymPatterns['gold_18k'], $clause))) {
                        continue;
                    }
                    $matchedKey = $key;
                    break;
                }
            }

            if ($matchedKey && !isset($usedKeys[$matchedKey])) {
                $curProf = $profiles[$matchedKey];
                $cur = floatval($curProf['rate']);

                // Convert kg input like "sariya 62 rupay kilo" to per_ton if profile is per_ton
                $isTmtTon = ($curProf['rate_unit'] ?? '') === 'per_ton';
                $notes = [];

                if (preg_match('/(badha|बढ़ा|extra|\+)/iu', $clause) && !preg_match('/\+\s*gst/iu', $clause) && $numVal < ($cur * 0.25)) {
                    $delta = ($isTmtTon && $numVal < 100) ? ($numVal * 1000) : $numVal;
                    $newRate = $cur + $delta;
                    $notes[] = "₹{$delta} बढ़ाया";
                } elseif (preg_match('/(ghata|घटा|kam|कम|\-)/iu', $clause) && $numVal < ($cur * 0.25)) {
                    $delta = ($isTmtTon && $numVal < 100) ? ($numVal * 1000) : $numVal;
                    $newRate = max(1, $cur - $delta);
                    $notes[] = "₹{$delta} घटाया";
                } else {
                    if ($isTmtTon && $numVal < 500) {
                        $numVal = $numVal * 1000;
                        $notes[] = "प्रति Kg से ₹{$numVal}/Ton में बदला";
                    }
                    $newRate = $numVal;
                }

                if ($gstMode) $notes[] = ($gstMode === 'included' ? 'GST सहित' : 'बिन GST (+Extra)');
                if ($loadingVal !== null) $notes[] = "लोडिंग ₹{$loadingVal}";
                if ($insVal !== null) $notes[] = "इंश्योरेंस ₹{$insVal}";
                if ($bhadaVal !== null) $notes[] = "लोकल भाड़ा ₹{$bhadaVal}";

                $usedKeys[$matchedKey] = true;
                $results[] = [
                    'key' => $matchedKey,
                    'new_rate' => $newRate,
                    'gst_mode' => $gstMode,
                    'loading_charge' => $loadingVal,
                    'insurance_charge' => $insVal,
                    'local_freight' => $bhadaVal,
                    'reason_hi' => !empty($notes) ? implode(' | ', $notes) : "नया भाव ₹{$newRate}",
                ];
            }
        }

        return $results;
    }
}
