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
     * Master Daily Base-Rate & AI Voice Updater Dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $state = $this->engine->getSellerState($user);
        $rates = $state['rates'];
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
                    $formula = $this->engine->detectFormula($prod, $var);
                    if (!empty($formula['commodity_key'])) {
                        $hasAnyLink = true;
                        $k = $formula['commodity_key'];
                        $countsByKey[$k] = ($countsByKey[$k] ?? 0) + 1;
                        $calc = $this->engine->calculatePrice($formula, $rates);
                        $linkedItems[] = [
                            'product_id' => $prod->id,
                            'variant_id' => $var->id,
                            'name' => $prod->name . ' — ' . $var->variant_name,
                            'commodity_key' => $k,
                            'commodity_label' => $definitions[$k]['label'] ?? $k,
                            'group' => $definitions[$k]['group'] ?? 'anaj_mandi',
                            'formula' => $formula,
                            'current_price' => (float)$var->retail_price,
                            'calculated_price' => $calc['retail_price'] ?? (float)$var->retail_price,
                            'explanation' => $calc['explanation'] ?? '',
                            'source' => $formula['source'] ?? 'auto_detected',
                        ];
                    }
                }
            } else {
                $formula = $this->engine->detectFormula($prod, null);
                if (!empty($formula['commodity_key'])) {
                    $hasAnyLink = true;
                    $k = $formula['commodity_key'];
                    $countsByKey[$k] = ($countsByKey[$k] ?? 0) + 1;
                    $calc = $this->engine->calculatePrice($formula, $rates);
                    $linkedItems[] = [
                        'product_id' => $prod->id,
                        'variant_id' => null,
                        'name' => $prod->name,
                        'commodity_key' => $k,
                        'commodity_label' => $definitions[$k]['label'] ?? $k,
                        'group' => $definitions[$k]['group'] ?? 'anaj_mandi',
                        'formula' => $formula,
                        'current_price' => (float)$prod->price,
                        'calculated_price' => $calc['retail_price'] ?? (float)$prod->price,
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
            'rates', 'definitions', 'history', 'activeTrade',
            'linkedItems', 'unlinkedProducts', 'countsByKey', 'products'
        ));
    }

    /**
     * ⚡ Apply Updated Base Rates across All Linked Products
     */
    public function applyRates(Request $request)
    {
        $request->validate([
            'rates' => 'required|array',
            'source' => 'nullable|string|max:100',
        ]);

        $cleanRates = [];
        foreach ($request->input('rates') as $k => $val) {
            if (is_numeric($val) && floatval($val) > 0) {
                $cleanRates[$k] = round(floatval($val), 2);
            }
        }

        if (empty($cleanRates)) {
            return response()->json([
                'success' => false,
                'message' => 'कृपया कम से कम एक मान्य मार्केट रेट दर्ज करें।'
            ], 422);
        }

        $result = $this->engine->applyMasterRates(
            Auth::user(),
            $cleanRates,
            $request->input('source', 'Master Rate Dashboard')
        );

        return response()->json([
            'success' => true,
            'message' => "✅ {$result['updated_products']} मुख्य प्रोडक्ट्स और {$result['updated_variants']} वेरिएंट्स के रेट्स लाइव अपडेट हो गए!",
            'result' => $result,
        ]);
    }

    /**
     * 🎙️ AI Voice & Natural Hindi/Hinglish Rate Parser
     */
    public function parseVoice(Request $request)
    {
        $request->validate([
            'transcript' => 'required|string|max:1000',
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
            'trade_type' => 'required|string|in:jewellery,tmt_steel,cement,mandi_grain',
            'weight_val' => 'required|numeric|min:0.01',
            'making_type' => 'nullable|string|in:percent,per_gm,flat',
            'making_val' => 'nullable|numeric|min:0',
            'extra_charge' => 'nullable|numeric|min:0',
            'margin_pct' => 'nullable|numeric|min:0',
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

        if ($validated['trade_type'] === 'jewellery') {
            $formula['weight_gm'] = (float)$validated['weight_val'];
            $formula['making_type'] = $validated['making_type'] ?? 'percent';
            $formula['making_val'] = (float)($validated['making_val'] ?? 12);
            $formula['gst_pct'] = (float)($validated['gst_pct'] ?? 3);
        } elseif ($validated['trade_type'] === 'tmt_steel') {
            $formula['weight_kg'] = (float)$validated['weight_val'];
            $formula['extra_per_kg'] = (float)($validated['extra_charge'] ?? 1.5);
            $formula['margin_pct'] = (float)($validated['margin_pct'] ?? 4);
            $formula['gst_pct'] = (float)($validated['gst_pct'] ?? 18);
            $formula['gst_mode'] = $validated['gst_mode'] ?? 'extra';
        } elseif ($validated['trade_type'] === 'cement') {
            $formula['bags_count'] = (float)$validated['weight_val'];
            $formula['hamali_freight'] = (float)($validated['extra_charge'] ?? 10);
            $formula['margin_pct'] = (float)($validated['margin_pct'] ?? 5);
        } else {
            $formula['weight_kg'] = (float)$validated['weight_val'];
            $formula['hamali_bardana'] = (float)($validated['extra_charge'] ?? 20);
            $formula['margin_pct'] = (float)($validated['margin_pct'] ?? 4);
        }

        $state = $this->engine->getSellerState($user);
        $calc = $this->engine->calculatePrice($formula, $state['rates']);

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
     * ➕ Add Custom Commodity Base Group (e.g. Makka, Jeera, Mustard Oil Tin)
     */
    public function addCustomCommodity(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:120',
            'unit' => 'required|string|max:60',
            'group' => 'required|string|in:anaj_mandi,iron_cement,jewellery',
            'default_rate' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();
        $state = $this->engine->getSellerState($user);
        $key = 'custom_' . Str::slug($validated['label'], '_');

        $customDefs = $state['custom_commodities'];
        $customDefs[$key] = [
            'key' => $key,
            'group' => $validated['group'],
            'label' => $validated['label'],
            'unit' => $validated['unit'],
            'default_rate' => (float)$validated['default_rate'],
            'icon' => $validated['group'] === 'jewellery' ? 'fa-gem' : ($validated['group'] === 'iron_cement' ? 'fa-trowel-bricks' : 'fa-wheat-awn'),
            'color' => 'indigo',
            'is_custom' => true,
        ];

        $rates = $state['rates'];
        $rates[$key] = (float)$validated['default_rate'];

        $this->engine->saveSellerState($user, $rates, $state['history'], $customDefs);

        return response()->json([
            'success' => true,
            'message' => "नया कमोडिटी ग्रुप '{$validated['label']}' जोड़ दिया गया!",
            'key' => $key,
        ]);
    }

    /**
     * ⚡ Load Realistic Sample Products across Jewellery, Iron/TMT, Cement & Anaj Mandi for Testing
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
                'formula' => ['commodity_key' => 'gold_22k', 'trade_type' => 'jewellery', 'weight_gm' => 5.0, 'making_type' => 'percent', 'making_val' => 12, 'gst_pct' => 3],
                'attrs' => ['metal_purity' => 'gold_22k', 'net_weight_gm' => 5.0, 'making_charge_type' => 'percent', 'making_charge_val' => 12, 'hallmark_huid' => 'BIS HUID 916 Verified'],
            ];
            $samples[] = [
                'name' => '22K Traditional Gold Chain (15g)',
                'cat' => $catJewel->id,
                'group' => 'Jewellery Gold 22K',
                'brand' => 'HUID 916',
                'sku' => 'JWL-CHN-22K-15G',
                'formula' => ['commodity_key' => 'gold_22k', 'trade_type' => 'jewellery', 'weight_gm' => 15.0, 'making_type' => 'percent', 'making_val' => 10, 'gst_pct' => 3],
                'attrs' => ['metal_purity' => 'gold_22k', 'net_weight_gm' => 15.0, 'making_charge_type' => 'percent', 'making_charge_val' => 10, 'hallmark_huid' => 'BIS HUID 916 Verified'],
            ];
            $samples[] = [
                'name' => '999 Pure Silver Bridal Payal Pair (60g)',
                'cat' => $catJewel->id,
                'group' => 'Jewellery Silver 999',
                'brand' => '999 Pure Silver',
                'sku' => 'SLV-PYL-60G',
                'formula' => ['commodity_key' => 'silver_999', 'trade_type' => 'jewellery', 'weight_gm' => 60.0, 'making_type' => 'per_gm', 'making_val' => 15, 'gst_pct' => 3],
                'attrs' => ['metal_purity' => 'silver_999', 'net_weight_gm' => 60.0, 'making_charge_type' => 'per_gm', 'making_charge_val' => 15, 'hallmark_huid' => '999 Purity Stamped'],
            ];
        }

        if ($trade === 'all' || $trade === 'iron_cement') {
            $samples[] = [
                'name' => 'Kamdhenu TMT Sariya 12mm Fe-550D (10.66 Kg Rod)',
                'cat' => $catIron->id,
                'group' => 'TMT Steel Bars',
                'brand' => 'Kamdhenu',
                'sku' => 'TMT-12MM-550D',
                'formula' => ['commodity_key' => 'tmt_base_kg', 'trade_type' => 'tmt_steel', 'weight_kg' => 10.66, 'extra_per_kg' => 1.5, 'margin_pct' => 4, 'gst_pct' => 18, 'gst_mode' => 'extra'],
                'attrs' => ['material_type' => 'tmt_bar', 'rod_weight_kg' => 10.66, 'steel_cement_grade' => 'Fe-550D ISI'],
            ];
            $samples[] = [
                'name' => 'Kamdhenu TMT Sariya 16mm Fe-550D (18.96 Kg Rod)',
                'cat' => $catIron->id,
                'group' => 'TMT Steel Bars',
                'brand' => 'Kamdhenu',
                'sku' => 'TMT-16MM-550D',
                'formula' => ['commodity_key' => 'tmt_base_kg', 'trade_type' => 'tmt_steel', 'weight_kg' => 18.96, 'extra_per_kg' => 1.0, 'margin_pct' => 4, 'gst_pct' => 18, 'gst_mode' => 'extra'],
                'attrs' => ['material_type' => 'tmt_bar', 'rod_weight_kg' => 18.96, 'steel_cement_grade' => 'Fe-550D ISI'],
            ];
            $samples[] = [
                'name' => 'UltraTech Weather Plus Cement (50 Kg Bag)',
                'cat' => $catIron->id,
                'group' => 'Cement Bags',
                'brand' => 'UltraTech',
                'sku' => 'CMT-UT-50KG',
                'formula' => ['commodity_key' => 'cement_bag_base', 'trade_type' => 'cement', 'bags_count' => 1.0, 'hamali_freight' => 10, 'margin_pct' => 5, 'wholesale_margin_pct' => 2.5],
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
                'formula' => ['commodity_key' => 'anaj_gehu', 'trade_type' => 'mandi_grain', 'weight_kg' => 50.0, 'hamali_bardana' => 25, 'margin_pct' => 4, 'wholesale_margin_pct' => 2],
                'attrs' => ['mandi_unit' => 'bori_50', 'moisture_percent' => '< 11% Clean Sortex'],
            ];
            $samples[] = [
                'name' => 'Desi Chana Bold Sortex (100 Kg Quintal)',
                'cat' => $catMandi->id,
                'group' => 'मंडी भाव (Commodity)',
                'brand' => 'Mandi Yard',
                'sku' => 'MND-CHANA-100KG',
                'formula' => ['commodity_key' => 'anaj_chana', 'trade_type' => 'mandi_grain', 'weight_kg' => 100.0, 'hamali_bardana' => 35, 'margin_pct' => 3.5, 'wholesale_margin_pct' => 1.5],
                'attrs' => ['mandi_unit' => 'quintal', 'moisture_percent' => '< 10% Bold Grade'],
            ];
        }

        $state = $this->engine->getSellerState($user);
        foreach ($samples as $s) {
            $calc = $this->engine->calculatePrice($s['formula'], $state['rates']);
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
                    'gst_percent' => $s['formula']['gst_pct'] ?? 5,
                    'track_inventory' => false,
                    'attributes' => $attrs,
                    'price_effective_date' => now()->toDateString(),
                ]
            );
            $created++;
        }

        return response()->json([
            'success' => true,
            'message' => "{$created} सैंपल कमोडिटी प्रोडक्ट्स लाइव रेट इंजन से जुड़ गए!",
        ]);
    }
}
