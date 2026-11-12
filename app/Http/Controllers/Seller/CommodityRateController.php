<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CommodityRateEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CommodityRateController extends Controller
{
    public function __construct(protected CommodityRateEngineService $engine)
    {
    }

    /**
     * Master Daily Base-Rate, Multi-Brand Costing & AI Voice/Broker Message Updater Dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $state = $this->engine->getSellerState($user);
        $rates = $state['rates'];
        $profiles = $state['profiles'];
        $definitions = $state['definitions'];
        $history = $state['history'];

        $activeTrade = $request->query('trade', 'all');

        $products = Product::where('user_id', $user->id)
            ->with(['variants', 'category'])
            ->latest()
            ->get();

        $linkedItems = [];
        $unlinkedProducts = [];
        $countsByKey = [];

        foreach ($products as $prod) {
            $variants = $prod->variants;
            $hasAnyLink = false;

            if ($variants && $variants->isNotEmpty()) {
                foreach ($variants as $var) {
                    $formula = $this->engine->detectFormula($prod, $var, $definitions);
                    if (!empty($formula['commodity_key'])) {
                        $hasAnyLink = true;
                        $k = $formula['commodity_key'];
                        $countsByKey[$k] = ($countsByKey[$k] ?? 0) + 1;
                        $calc = $this->engine->calculatePrice($formula, $profiles);
                        $linkedItems[] = [
                            'product_id' => $prod->id,
                            'variant_id' => $var->id,
                            'name' => $prod->name . ' — ' . $var->variant_name,
                            'brand' => $prod->brand ?: ($definitions[$k]['brand'] ?? ''),
                            'commodity_key' => $k,
                            'commodity_label' => $definitions[$k]['label'] ?? $k,
                            'group' => $definitions[$k]['group'] ?? 'anaj_mandi',
                            'formula' => $formula,
                            'current_price' => (float)$var->retail_price,
                            'calculated_price' => $calc['retail_price'] ?? (float)$var->retail_price,
                            'landing_cost' => $calc['purchase_price'] ?? 0,
                            'explanation' => $calc['explanation'] ?? '',
                            'source' => $formula['source'] ?? 'auto_detected',
                        ];
                    }
                }
            } else {
                $formula = $this->engine->detectFormula($prod, null, $definitions);
                if (!empty($formula['commodity_key'])) {
                    $hasAnyLink = true;
                    $k = $formula['commodity_key'];
                    $countsByKey[$k] = ($countsByKey[$k] ?? 0) + 1;
                    $calc = $this->engine->calculatePrice($formula, $profiles);
                    $linkedItems[] = [
                        'product_id' => $prod->id,
                        'variant_id' => null,
                        'name' => $prod->name,
                        'brand' => $prod->brand ?: ($definitions[$k]['brand'] ?? ''),
                        'commodity_key' => $k,
                        'commodity_label' => $definitions[$k]['label'] ?? $k,
                        'group' => $definitions[$k]['group'] ?? 'anaj_mandi',
                        'formula' => $formula,
                        'current_price' => (float)$prod->price,
                        'calculated_price' => $calc['retail_price'] ?? (float)$prod->price,
                        'landing_cost' => $calc['purchase_price'] ?? 0,
                        'explanation' => $calc['explanation'] ?? '',
                        'source' => $formula['source'] ?? 'auto_detected',
                    ];
                }
            }

            if (!$hasAnyLink) {
                $unlinkedProducts[] = $prod;
            }
        }

        return view('seller.rates.index', compact(
            'rates', 'profiles', 'definitions', 'history', 'activeTrade',
            'linkedItems', 'unlinkedProducts', 'countsByKey', 'products'
        ));
    }

    /**
     * ⚡ Apply Updated Profiles (Rate, GST Mode/%, Die, Loading, Insurance, Plant-to-Godown Bhada, Profit)
     */
    public function applyRates(Request $request)
    {
        $incomingProfiles = [];

        if ($request->has('profiles') && is_array($request->input('profiles'))) {
            foreach ($request->input('profiles') as $k => $prof) {
                if (!is_array($prof) || empty($prof['rate']) || floatval($prof['rate']) <= 0) continue;
                $clean = [
                    'rate' => round(floatval($prof['rate']), 2),
                    'rate_unit' => $prof['rate_unit'] ?? 'per_unit',
                    'gst_mode' => in_array($prof['gst_mode'] ?? 'extra', ['extra', 'included']) ? $prof['gst_mode'] : 'extra',
                    'gst_pct' => floatval($prof['gst_pct'] ?? 0),
                    'loading_charge' => floatval($prof['loading_charge'] ?? 0),
                    'insurance_charge' => floatval($prof['insurance_charge'] ?? 0),
                    'local_freight' => floatval($prof['local_freight'] ?? 0),
                    'profit_type' => $prof['profit_type'] ?? 'percent',
                    'profit_val' => floatval($prof['profit_val'] ?? 0),
                ];
                if (!empty($prof['plant_location'])) {
                    $clean['plant_location'] = trim($prof['plant_location']);
                }
                if (!empty($prof['brand'])) {
                    $clean['brand'] = trim($prof['brand']);
                }
                if (!empty($prof['die_rates']) && is_array($prof['die_rates'])) {
                    $cleanDie = [];
                    foreach ($prof['die_rates'] as $mm => $dv) {
                        $cleanDie[$mm] = floatval($dv);
                    }
                    $clean['die_rates'] = $cleanDie;
                }
                $incomingProfiles[$k] = $clean;
            }
        } elseif ($request->has('rates') && is_array($request->input('rates'))) {
            foreach ($request->input('rates') as $k => $val) {
                if (is_array($val)) {
                    $incomingProfiles[$k] = $val;
                } elseif (is_numeric($val) && floatval($val) > 0) {
                    $incomingProfiles[$k] = ['rate' => round(floatval($val), 2)];
                }
            }
        }

        if (empty($incomingProfiles)) {
            return response()->json([
                'success' => false,
                'message' => 'कृपया कम से कम एक मान्य मार्केट रेट दर्ज करें।'
            ], 422);
        }

        $result = $this->engine->applyMasterProfiles(
            Auth::user(),
            $incomingProfiles,
            $request->input('source', 'Master Rate & Costing Engine')
        );

        return response()->json([
            'success' => true,
            'message' => "✅ {$result['updated_products']} मुख्य प्रोडक्ट्स और {$result['updated_variants']} वेरिएंट्स (GST + Die + Loading + Insurance + भाड़ा + Profit सहित) लाइव अपडेट हो गए!",
            'result' => $result,
        ]);
    }

    /**
     * 🎙️ AI Voice & Broker WhatsApp Message Parser
     */
    public function parseVoice(Request $request)
    {
        $request->validate([
            'transcript' => 'required|string|max:2000',
            'api_key' => 'nullable|string',
        ]);

        $parsed = $this->engine->parseVoiceCommand(
            $request->input('transcript'),
            Auth::user(),
            $request->input('api_key')
        );

        return response()->json($parsed);
    }

    /**
     * 🔗 Link or Customize Formula for Any Product / Variant
     */
    public function linkProductFormula(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'variant_id' => 'nullable|integer',
            'commodity_key' => 'required|string|max:100',
            'trade_type' => 'required|string|in:jewellery,tmt_steel,cement,mandi_grain,fab_steel,fab_sheet',
            'weight_val' => 'required|numeric|min:0.01',
            'mm_size' => 'nullable|string|max:30',
            'section_size' => 'nullable|string|max:50',
            'die_override_per_ton' => 'nullable|numeric|min:0',
            'making_type' => 'nullable|string|in:percent,per_gm,flat',
            'making_val' => 'nullable|numeric|min:0',
            'loading_override' => 'nullable|numeric|min:0',
            'insurance_override' => 'nullable|numeric|min:0',
            'freight_override' => 'nullable|numeric|min:0',
            'profit_type' => 'nullable|string|in:percent,flat,flat_kg,flat_ton',
            'profit_val' => 'nullable|numeric|min:0',
            'gst_pct' => 'nullable|numeric|min:0',
            'gst_mode' => 'nullable|string|in:included,extra',
        ]);

        $user = Auth::user();
        $product = Product::where('id', $validated['product_id'])->where('user_id', $user->id)->firstOrFail();

        $formula = [
            'commodity_key' => $validated['commodity_key'],
            'trade_type' => $validated['trade_type'],
            'source' => 'manual_linked',
        ];

        if ($request->filled('gst_mode')) $formula['gst_mode'] = $validated['gst_mode'];
        if ($request->filled('gst_pct')) $formula['gst_pct'] = (float)$validated['gst_pct'];
        if ($request->filled('loading_override')) $formula['loading_override'] = (float)$validated['loading_override'];
        if ($request->filled('insurance_override')) $formula['insurance_override'] = (float)$validated['insurance_override'];
        if ($request->filled('freight_override')) $formula['freight_override'] = (float)$validated['freight_override'];
        if ($request->filled('profit_type')) $formula['profit_type'] = $validated['profit_type'];
        if ($request->filled('profit_val')) $formula['profit_val'] = (float)$validated['profit_val'];

        if ($validated['trade_type'] === 'jewellery') {
            $formula['weight_gm'] = (float)$validated['weight_val'];
            $formula['making_type'] = $validated['making_type'] ?? 'percent';
            $formula['making_val'] = (float)($validated['making_val'] ?? 12);
        } elseif ($validated['trade_type'] === 'tmt_steel') {
            $formula['weight_kg'] = (float)$validated['weight_val'];
            $formula['mm_size'] = $validated['mm_size'] ?? '12mm';
            if ($request->filled('die_override_per_ton')) {
                $formula['die_override_per_ton'] = (float)$validated['die_override_per_ton'];
            }
        } elseif ($validated['trade_type'] === 'fab_sheet') {
            $formula['length_ft'] = (float)$validated['weight_val'];
        } elseif ($validated['trade_type'] === 'fab_steel') {
            $formula['weight_kg'] = (float)$validated['weight_val'];
            $formula['section_size'] = $validated['section_size'] ?? ($validated['mm_size'] ?? '20ft Piece');
        } elseif ($validated['trade_type'] === 'cement') {
            $formula['bags_count'] = (float)$validated['weight_val'];
        } else {
            $formula['weight_kg'] = (float)$validated['weight_val'];
        }

        $state = $this->engine->getSellerState($user);
        $calc = $this->engine->calculatePrice($formula, $state['profiles']);

        if (!empty($validated['variant_id'])) {
            $variant = ProductVariant::where('id', $validated['variant_id'])->where('product_id', $product->id)->firstOrFail();
            $vAttrs = is_array($variant->attributes) ? $variant->attributes : (json_decode($variant->attributes ?? '[]', true) ?: []);
            $vAttrs['rate_formula'] = $formula;
            $variant->attributes = $vAttrs;
            if ($calc['valid']) {
                $variant->purchase_price = $calc['purchase_price'];
                $variant->wholesale_price = $calc['wholesale_price'];
                $variant->retail_price = $calc['retail_price'];
                $variant->price_effective_date = now()->toDateString();
            }
            $variant->save();
        } else {
            $pAttrs = is_array($product->attributes) ? $product->attributes : (json_decode($product->attributes ?? '[]', true) ?: []);
            $pAttrs['rate_formula'] = $formula;
            $product->attributes = $pAttrs;
            if ($calc['valid']) {
                $product->purchase_price = $calc['purchase_price'];
                $product->wholesale_price = $calc['wholesale_price'];
                $product->price = $calc['retail_price'];
                $product->price_effective_date = now()->toDateString();
            }
            $product->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'प्रोडक्ट सफलतापूर्वक लाइव कमोडिटी इंजन से लिंक व री-कैलकुलेट हो गया!',
            'new_price' => $calc['retail_price'] ?? 0,
            'explanation' => $calc['explanation'] ?? '',
        ]);
    }

    /**
     * ➕ Add Brand & Plant-Location Specific Rate Group (e.g. "Tata Tiscon - Raipur", "Bangur Cement - Pali")
     */
    public function addCustomCommodity(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:120',
            'brand' => 'nullable|string|max:80',
            'plant_location' => 'nullable|string|max:100',
            'unit' => 'required|string|max:60',
            'rate_unit' => 'nullable|string|in:per_ton,per_kg,per_bag,per_quintal,per_10gm,per_ft,per_unit',
            'group' => 'required|string|in:anaj_mandi,iron_cement,jewellery,fabrication_structure',
            'default_rate' => 'required|numeric|min:1',
            'gst_mode' => 'nullable|string|in:extra,included',
            'gst_pct' => 'nullable|numeric|min:0|max:40',
            'loading_charge' => 'nullable|numeric|min:0',
            'insurance_charge' => 'nullable|numeric|min:0',
            'local_freight' => 'nullable|numeric|min:0',
            'profit_val' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        $state = $this->engine->getSellerState($user);
        $key = 'custom_' . Str::slug($validated['label'] . '_' . ($validated['plant_location'] ?? ''), '_');

        $customDefs = $state['custom_commodities'];
        $customDefs[$key] = [
            'key' => $key,
            'group' => $validated['group'],
            'label' => $validated['label'],
            'brand' => $validated['brand'] ?: $validated['label'],
            'plant_location' => $validated['plant_location'] ?: 'Regional Plant / Mandi',
            'unit' => $validated['unit'],
            'rate_unit' => $validated['rate_unit'] ?? ($validated['group'] === 'iron_cement' ? 'per_ton' : 'per_unit'),
            'default_rate' => (float)$validated['default_rate'],
            'gst_mode' => $validated['gst_mode'] ?? 'extra',
            'gst_pct' => (float)($validated['gst_pct'] ?? ($validated['group'] === 'jewellery' ? 3 : 18)),
            'loading_charge' => (float)($validated['loading_charge'] ?? 0),
            'insurance_charge' => (float)($validated['insurance_charge'] ?? 0),
            'local_freight' => (float)($validated['local_freight'] ?? 0),
            'default_profit_type' => 'percent',
            'default_profit_val' => (float)($validated['profit_val'] ?? 4),
            'die_rates' => CommodityRateEngineService::DEFAULT_TMT_DIE_PER_TON,
            'icon' => $validated['group'] === 'jewellery' ? 'fa-gem' : ($validated['group'] === 'iron_cement' ? 'fa-industry' : 'fa-wheat-awn'),
            'color' => 'indigo',
            'is_custom' => true,
        ];

        $profiles = $state['profiles'];
        $profiles[$key] = [
            'rate' => (float)$validated['default_rate'],
            'rate_unit' => $customDefs[$key]['rate_unit'],
            'gst_mode' => $customDefs[$key]['gst_mode'],
            'gst_pct' => $customDefs[$key]['gst_pct'],
            'loading_charge' => $customDefs[$key]['loading_charge'],
            'insurance_charge' => $customDefs[$key]['insurance_charge'],
            'local_freight' => $customDefs[$key]['local_freight'],
            'profit_type' => 'percent',
            'profit_val' => $customDefs[$key]['default_profit_val'],
            'brand' => $customDefs[$key]['brand'],
            'plant_location' => $customDefs[$key]['plant_location'],
            'die_rates' => CommodityRateEngineService::DEFAULT_TMT_DIE_PER_TON,
        ];

        $this->engine->saveSellerState($user, $profiles, $state['history'], $customDefs);

        return response()->json([
            'success' => true,
            'message' => "नया ब्रांड/प्लांट कमोडिटी ग्रुप '{$validated['label']}' जोड़ दिया गया!",
            'key' => $key,
        ]);
    }

    /**
     * ⚡ Load Realistic Multi-Brand Sample Products across Jewellery, Iron/TMT (Kamdhenu vs Jindal), Cement & Anaj Mandi
     */
    public function loadDemoSamples(Request $request)
    {
        $user = Auth::user();
        $trade = $request->input('trade', 'all');
        $created = 0;

        $catJewel = Category::firstOrCreate(['slug' => 'jewellery-gold-silver'], ['name' => 'Jewellery & Bullion']);
        $catIron = Category::firstOrCreate(['slug' => 'iron-tmt-cement'], ['name' => 'Iron, TMT Steel & Cement']);
        $catMandi = Category::firstOrCreate(['slug' => 'anaj-mandi-grains'], ['name' => 'Anaj Mandi & Bulk Grains']);

        $samples = [];
        if ($trade === 'all' || $trade === 'jewellery') {
            $samples[] = [
                'name' => '22K Hallmark Gold Solitaire Ring (5g)',
                'cat' => $catJewel->id,
                'group' => 'Jewellery Gold 22K',
                'brand' => 'HUID 916',
                'sku' => 'JWL-RNG-22K-5G',
                'formula' => ['commodity_key' => 'gold_22k', 'trade_type' => 'jewellery', 'weight_gm' => 5.0, 'making_type' => 'percent', 'making_val' => 12],
                'attrs' => ['metal_purity' => 'gold_22k', 'net_weight_gm' => 5.0, 'making_charge_type' => 'percent', 'making_charge_val' => 12, 'hallmark_huid' => 'BIS HUID 916 Verified'],
            ];
            $samples[] = [
                'name' => '999 Pure Silver Bridal Payal Pair (60g)',
                'cat' => $catJewel->id,
                'group' => 'Jewellery Silver 999',
                'brand' => '999 Pure Silver',
                'sku' => 'SLV-PYL-60G',
                'formula' => ['commodity_key' => 'silver_999', 'trade_type' => 'jewellery', 'weight_gm' => 60.0, 'making_type' => 'per_gm', 'making_val' => 15],
                'attrs' => ['metal_purity' => 'silver_999', 'net_weight_gm' => 60.0, 'making_charge_type' => 'per_gm', 'making_charge_val' => 15, 'hallmark_huid' => '999 Purity Stamped'],
            ];
        }

        if ($trade === 'all' || $trade === 'iron_cement') {
            $samples[] = [
                'name' => 'Kamdhenu NXT TMT Sariya 8mm (4.74 Kg Rod)',
                'cat' => $catIron->id,
                'group' => 'Kamdhenu TMT (Raipur)',
                'brand' => 'Kamdhenu',
                'sku' => 'KMD-8MM-550D',
                'formula' => ['commodity_key' => 'tmt_kamdhenu', 'trade_type' => 'tmt_steel', 'mm_size' => '8mm', 'weight_kg' => 4.74],
                'attrs' => ['material_type' => 'tmt_bar', 'rod_weight_kg' => 4.74, 'steel_cement_grade' => 'Fe-550D Raipur'],
            ];
            $samples[] = [
                'name' => 'Kamdhenu NXT TMT Sariya 12mm (10.66 Kg Rod)',
                'cat' => $catIron->id,
                'group' => 'Kamdhenu TMT (Raipur)',
                'brand' => 'Kamdhenu',
                'sku' => 'KMD-12MM-550D',
                'formula' => ['commodity_key' => 'tmt_kamdhenu', 'trade_type' => 'tmt_steel', 'mm_size' => '12mm', 'weight_kg' => 10.66],
                'attrs' => ['material_type' => 'tmt_bar', 'rod_weight_kg' => 10.66, 'steel_cement_grade' => 'Fe-550D Raipur'],
            ];
            $samples[] = [
                'name' => 'Jindal Panther TMT Sariya 12mm (10.66 Kg Rod)',
                'cat' => $catIron->id,
                'group' => 'Jindal Panther TMT',
                'brand' => 'Jindal',
                'sku' => 'JND-12MM-550D',
                'formula' => ['commodity_key' => 'tmt_jindal_tata', 'trade_type' => 'tmt_steel', 'mm_size' => '12mm', 'weight_kg' => 10.66],
                'attrs' => ['material_type' => 'tmt_bar', 'rod_weight_kg' => 10.66, 'steel_cement_grade' => 'Fe-550D Patratu'],
            ];
            $samples[] = [
                'name' => 'UltraTech Weather Plus Cement (50 Kg Bag)',
                'cat' => $catIron->id,
                'group' => 'UltraTech / ACC Cement',
                'brand' => 'UltraTech',
                'sku' => 'CMT-UT-50KG',
                'formula' => ['commodity_key' => 'cement_ultratech_acc', 'trade_type' => 'cement', 'bags_count' => 1.0],
                'attrs' => ['material_type' => 'cement_bag', 'rod_weight_kg' => 50, 'steel_cement_grade' => 'PPC 50Kg Bag'],
            ];
        }

        if ($trade === 'all' || $trade === 'anaj_mandi') {
            $samples[] = [
                'name' => 'MP Sharbati Gehu Super Clean (50 Kg Bori)',
                'cat' => $catMandi->id,
                'group' => 'मंडी भाव (Commodity)',
                'brand' => 'Sharbati A+',
                'sku' => 'MND-GEHU-50KG',
                'formula' => ['commodity_key' => 'anaj_gehu', 'trade_type' => 'mandi_grain', 'weight_kg' => 50.0],
                'attrs' => ['mandi_unit' => 'bori_50', 'moisture_percent' => '< 11% Clean Sortex'],
            ];
        }

        if ($trade === 'all' || $trade === 'fabrication_structure') {
            $catFab = Category::firstOrCreate(['slug' => 'fabrication-steel-sheets'], ['name' => 'Fabrication Angles, Pipes & Roofing Sheets']);
            $samples[] = [
                'name' => 'MS Angle 50x50x6mm Structural Bar (27 Kg / 6m)',
                'cat' => $catFab->id,
                'group' => 'Fabrication Angles & Channels',
                'brand' => 'Structural MS',
                'sku' => 'FAB-ANG-50X50X6',
                'formula' => ['commodity_key' => 'fab_ms_angle_channel', 'trade_type' => 'fab_steel', 'section_size' => '50x50x6', 'weight_kg' => 27.0],
                'attrs' => ['material_type' => 'angle_channel', 'rod_weight_kg' => 27.0, 'steel_cement_grade' => '6m / 20ft Length'],
            ];
            $samples[] = [
                'name' => 'Apollo Square GP Pipe 2x2 Inch (18 Kg / 20ft)',
                'cat' => $catFab->id,
                'group' => 'MS & GP Pipes',
                'brand' => 'APL Apollo',
                'sku' => 'FAB-GP-2X2-18KG',
                'formula' => ['commodity_key' => 'fab_ms_gp_pipe', 'trade_type' => 'fab_steel', 'section_size' => '2x2 Inch 16G', 'weight_kg' => 18.0],
                'attrs' => ['material_type' => 'angle_channel', 'rod_weight_kg' => 18.0, 'steel_cement_grade' => 'GP 20ft Length'],
            ];
            $samples[] = [
                'name' => 'JSW Colouron Profile Tin Sheet 0.45mm (12 Ft)',
                'cat' => $catFab->id,
                'group' => 'Roofing Tin & Cement Sheets',
                'brand' => 'JSW Colouron',
                'sku' => 'FAB-TIN-JSW-12FT',
                'formula' => ['commodity_key' => 'fab_tin_sheet', 'trade_type' => 'fab_sheet', 'length_ft' => 12.0],
                'attrs' => ['material_type' => 'tin_sheet', 'length_ft' => 12.0, 'steel_cement_grade' => '0.45mm Color Coated'],
            ];
            $samples[] = [
                'name' => 'Everest AC Cement Roofing Sheet (10 Ft)',
                'cat' => $catFab->id,
                'group' => 'Roofing Tin & Cement Sheets',
                'brand' => 'Everest',
                'sku' => 'FAB-CMT-SH-10FT',
                'formula' => ['commodity_key' => 'fab_cement_sheet', 'trade_type' => 'fab_sheet', 'length_ft' => 10.0],
                'attrs' => ['material_type' => 'cement_sheet', 'length_ft' => 10.0, 'steel_cement_grade' => 'ISI Fibre Cement Sheet'],
            ];
        }

        $state = $this->engine->getSellerState($user);
        foreach ($samples as $s) {
            $calc = $this->engine->calculatePrice($s['formula'], $state['profiles']);
            $attrs = array_merge($s['attrs'], ['rate_formula' => $s['formula']]);

            Product::updateOrCreate(
                ['user_id' => $user->id, 'sku' => $s['sku']],
                [
                    'name' => $s['name'],
                    'slug' => Str::slug($s['name']) . '-' . Str::random(4),
                    'category_id' => $s['cat'],
                    'group_name' => $s['group'],
                    'brand' => $s['brand'],
                    'purchase_price' => $calc['purchase_price'] ?? 0,
                    'wholesale_price' => $calc['wholesale_price'] ?? 0,
                    'price' => $calc['retail_price'] ?? 0,
                    'gst_percent' => $state['profiles'][$s['formula']['commodity_key']]['gst_pct'] ?? 5,
                    'track_inventory' => false,
                    'attributes' => $attrs,
                    'price_effective_date' => now()->toDateString(),
                ]
            );
            $created++;
        }

        return response()->json([
            'success' => true,
            'message' => "{$created} मल्टी-ब्रांड कमोडिटी प्रोडक्ट्स लाइव रेट इंजन से जुड़ गए!",
        ]);
    }
}
