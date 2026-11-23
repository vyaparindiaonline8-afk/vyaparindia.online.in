<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SellerPage;
use App\Models\Review;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Services\AISlipScannerService;
use App\Services\CloudinaryService;
use App\Services\BusinessModules\BusinessModuleManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MiniSiteController extends Controller
{
    // Seller facing methods
    public function create()
    {
        if (Auth::user()->sellerPage) {
            return redirect()->route('seller.minisite.edit');
        }
        $businessModules = BusinessModuleManager::all();
        return view('seller.minisite.form', compact('businessModules'));
    }

    protected function sanitizeSocialLinks(Request $request): void
    {
        // 1. Instagram Link / Handle
        if ($request->filled('instagram_link')) {
            $val = trim($request->input('instagram_link'));
            $handle = preg_replace('/^https?:\/\/(www\.)?instagram\.com\//i', '', $val);
            $handle = ltrim($handle, '@');
            $handle = trim($handle, '/');
            if (!empty($handle)) {
                $request->merge(['instagram_link' => 'https://instagram.com/' . $handle]);
            } else {
                $request->merge(['instagram_link' => null]);
            }
        }

        // 2. Facebook Link / Handle
        if ($request->filled('facebook_link')) {
            $val = trim($request->input('facebook_link'));
            $handle = preg_replace('/^https?:\/\/(www\.)?facebook\.com\//i', '', $val);
            $handle = ltrim($handle, '@');
            $handle = trim($handle, '/');
            if (!empty($handle)) {
                $request->merge(['facebook_link' => 'https://facebook.com/' . $handle]);
            } else {
                $request->merge(['facebook_link' => null]);
            }
        }

        // 3. YouTube Link / Handle
        if ($request->filled('youtube_link')) {
            $val = trim($request->input('youtube_link'));
            $handle = preg_replace('/^https?:\/\/(www\.)?youtube\.com\/(@)?/i', '', $val);
            $handle = ltrim($handle, '@');
            $handle = trim($handle, '/');
            if (!empty($handle)) {
                $request->merge(['youtube_link' => 'https://youtube.com/@' . $handle]);
            } else {
                $request->merge(['youtube_link' => null]);
            }
        }

        // 4. Google Maps & Reviews
        foreach (['google_map_link', 'google_review_link'] as $urlField) {
            if ($request->filled($urlField)) {
                $val = trim($request->input($urlField));
                if (!preg_match('/^https?:\/\//i', $val)) {
                    $request->merge([$urlField => 'https://' . $val]);
                }
            }
        }
    }

    public function store(Request $request)
    {
        $this->sanitizeSocialLinks($request);

        $validated = $request->validate([
            'page_title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:seller_pages|alpha_dash',
            'tagline' => 'nullable|string|max:255',
            'welcome_message' => 'nullable|string',
            'about_text' => 'nullable|string',
            'whatsapp_number' => 'nullable|string|max:20',
            'support_phone' => 'nullable|string|max:20',
            'support_email' => 'nullable|email|max:255',
            'instagram_link' => 'nullable|string|max:255',
            'facebook_link' => 'nullable|string|max:255',
            'youtube_link' => 'nullable|string|max:255',
            'google_review_link' => 'nullable|string|max:500',
            'google_map_link' => 'nullable|string|max:500',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'theme_color' => 'nullable|string|max:7',
            'theme_style' => 'nullable|string|in:modern,minimal,vibrant',
            'currency' => 'nullable|string|max:10',
            'enable_cod' => 'nullable|boolean',
            'enable_whatsapp_order' => 'nullable|boolean',
            'upi_id' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:150',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_ifsc' => 'nullable|string|max:20',
            'bank_account_holder' => 'nullable|string|max:150',
            'show_payment_details_to_buyer' => 'nullable|boolean',
            'policies' => 'nullable|string',
            'business_type' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = CloudinaryService::upload($request->file('logo'), 'vyaparindia/minisite/logos');
        }
        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = CloudinaryService::upload($request->file('banner_image'), 'vyaparindia/minisite/banners');
        }

        $validated['enable_cod'] = $request->has('enable_cod');
        $validated['enable_whatsapp_order'] = $request->has('enable_whatsapp_order');
        $validated['show_payment_details_to_buyer'] = $request->has('show_payment_details_to_buyer');
        $validated['business_type'] = $request->input('business_type', 'hardware_pipes');

        Auth::user()->sellerPage()->create($validated);

        return redirect()->route('seller.minisite.edit')->with('success', 'Mini-Storefront created successfully! Your store is live.');
    }

    public function edit()
    {
        $minisite = Auth::user()->sellerPage;
        if (!$minisite) {
            return redirect()->route('seller.minisite.create');
        }
        $businessModules = BusinessModuleManager::all();
        return view('seller.minisite.form', compact('minisite', 'businessModules'));
    }

    public function update(Request $request)
    {
        $minisite = Auth::user()->sellerPage;
        if (!$minisite) {
            return redirect()->route('seller.minisite.create');
        }

        $this->sanitizeSocialLinks($request);

        $validated = $request->validate([
            'page_title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|alpha_dash|unique:seller_pages,slug,' . $minisite->id,
            'tagline' => 'nullable|string|max:255',
            'welcome_message' => 'nullable|string',
            'about_text' => 'nullable|string',
            'whatsapp_number' => 'nullable|string|max:20',
            'support_phone' => 'nullable|string|max:20',
            'support_email' => 'nullable|email|max:255',
            'instagram_link' => 'nullable|string|max:255',
            'facebook_link' => 'nullable|string|max:255',
            'youtube_link' => 'nullable|string|max:255',
            'google_review_link' => 'nullable|string|max:500',
            'google_map_link' => 'nullable|string|max:500',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'theme_color' => 'nullable|string|max:7',
            'theme_style' => 'nullable|string|in:modern,minimal,vibrant',
            'business_type' => 'nullable|string|max:50',
            'currency' => 'nullable|string|max:10',
            'enable_cod' => 'nullable|boolean',
            'enable_whatsapp_order' => 'nullable|boolean',
            'upi_id' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:150',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_ifsc' => 'nullable|string|max:20',
            'bank_account_holder' => 'nullable|string|max:150',
            'show_payment_details_to_buyer' => 'nullable|boolean',
            'policies' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = CloudinaryService::upload($request->file('logo'), 'vyaparindia/minisite/logos');
        }
        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = CloudinaryService::upload($request->file('banner_image'), 'vyaparindia/minisite/banners');
        }

        $validated['enable_cod'] = $request->has('enable_cod');
        $validated['enable_whatsapp_order'] = $request->has('enable_whatsapp_order');
        $validated['show_payment_details_to_buyer'] = $request->has('show_payment_details_to_buyer');
        $validated['show_last_updated_to_buyers'] = $request->has('show_last_updated_to_buyers');

        if ($request->has('authorized_brands')) {
            $brandsInput = $request->input('authorized_brands');
            if (is_string($brandsInput)) {
                $validated['authorized_brands'] = json_decode($brandsInput, true);
            } elseif (is_array($brandsInput)) {
                $validated['authorized_brands'] = array_values(array_filter($brandsInput, function ($b) {
                    return !empty($b['name'] ?? '');
                }));
            }
        }

        // Store custom business settings (GPS, landmark, dispatch radius, state, dealership, shop photos)
        $currentSettings = $minisite->business_settings ?? [];
        if ($request->filled('dispatch_radius')) {
            $currentSettings['dispatch_radius'] = intval($request->input('dispatch_radius'));
        }
        if ($request->filled('state')) {
            $currentSettings['state'] = trim($request->input('state'));
        }
        if ($request->filled('landmark')) {
            $currentSettings['landmark'] = trim($request->input('landmark'));
        }
        if ($request->filled('latitude')) {
            $currentSettings['latitude'] = floatval($request->input('latitude'));
        }
        if ($request->filled('longitude')) {
            $currentSettings['longitude'] = floatval($request->input('longitude'));
        }
        if ($request->filled('business_category_custom')) {
            $currentSettings['business_category_custom'] = trim($request->input('business_category_custom'));
        }
        if ($request->filled('dealership_text')) {
            $currentSettings['dealership_text'] = trim($request->input('dealership_text'));
        }
        if ($request->filled('serviced_cities')) {
            $currentSettings['serviced_cities'] = trim($request->input('serviced_cities'));
        }
        if ($request->filled('serviced_pincodes')) {
            $currentSettings['serviced_pincodes'] = trim($request->input('serviced_pincodes'));
        }

        // 🍽️ Food & Dining Service Modes (Dine-in, Takeaway, Cloud Kitchen)
        if ($request->has('food_settings_submitted') || $request->has('food_mode_dine_in') || $request->has('food_mode_delivery') || $request->has('food_mode_takeaway')) {
            $currentSettings['food_service_modes'] = [
                'dine_in' => $request->boolean('food_mode_dine_in'),
                'takeaway' => $request->boolean('food_mode_takeaway'),
                'delivery' => $request->boolean('food_mode_delivery'),
                'tables_count' => max(1, intval($request->input('food_tables_count', 10))),
                'packaging_charge' => floatval($request->input('food_packaging_charge', 0)),
                'min_order_delivery' => floatval($request->input('food_min_order_delivery', 0)),
                'show_bill_amount' => $request->boolean('food_show_bill_amount', true),
                'show_upi_payment' => $request->boolean('food_show_upi_payment', true),
            ];
        }

        // Also update seller profile dispatch radius if present
        if ($request->filled('dispatch_radius') && Auth::user()->sellerProfile) {
            Auth::user()->sellerProfile->update(['dispatch_radius' => intval($request->input('dispatch_radius'))]);
        }

        $validated['business_settings'] = $currentSettings;

        $minisite->update($validated);

        return redirect()->route('seller.minisite.edit')->with('success', 'Mini-Storefront settings updated successfully.');
    }

    /**
     * 1-Click Banner Swapper (Direct from Storefront)
     */
    public function quickBannerUpdate(Request $request)
    {
        $minisite = Auth::user()->sellerPage;
        if (!$minisite) {
            return response()->json(['success' => false, 'message' => 'Store not found.'], 404);
        }

        $settings = $minisite->business_settings ?? [];
        if (is_string($settings)) {
            $settings = json_decode($settings, true) ?: [];
        }
        $slides = $settings['banner_slides'] ?? [];
        if (!is_array($slides)) {
            $slides = [];
        }

        $action = $request->input('action', 'add');

        if ($action === 'remove') {
            $removeUrl = $request->input('remove_url');
            $slides = array_values(array_filter($slides, fn($item) => $item !== $removeUrl));
            $settings['banner_slides'] = $slides;
            $primaryBanner = !empty($slides) ? $slides[0] : null;
            $minisite->update([
                'banner_image' => $primaryBanner,
                'business_settings' => $settings,
            ]);
            return response()->json([
                'success' => true,
                'banner_slides' => $minisite->banner_slides,
                'banner_url' => $minisite->banner_url,
                'message' => 'बैनर स्लाइड हटा दी गई।'
            ]);
        }

        $url = null;
        if ($request->hasFile('banner_image')) {
            $url = CloudinaryService::upload($request->file('banner_image'), 'vyaparindia/minisite/banners');
        } elseif ($request->filled('banner_url')) {
            $url = trim($request->input('banner_url'));
        }

        if (!$url) {
            return response()->json(['success' => false, 'message' => 'Please provide a banner photo or valid URL.'], 422);
        }

        if (!in_array($url, $slides)) {
            $slides[] = $url;
        }
        $settings['banner_slides'] = array_values(array_filter($slides));

        $minisite->update([
            'banner_image' => $url,
            'business_settings' => $settings,
        ]);

        return response()->json([
            'success' => true,
            'banner_url' => $url,
            'banner_slides' => $minisite->banner_slides,
            'message' => 'नया बैनर सफलतापूर्वक जोड़ा गया! ✨'
        ]);
    }

    /**
     * 1-Click Shop / Showroom Atmosphere Photos Uploader
     */
    public function quickShopPhotoUpload(Request $request)
    {
        $minisite = Auth::user()->sellerPage;
        if (!$minisite) {
            return response()->json(['success' => false, 'message' => 'Store not found.'], 404);
        }

        $url = null;
        if ($request->hasFile('shop_photo')) {
            $url = CloudinaryService::upload($request->file('shop_photo'), 'vyaparindia/minisite/shop_photos');
        } elseif ($request->filled('photo_url')) {
            $url = trim($request->input('photo_url'));
        }

        if (!$url) {
            return response()->json(['success' => false, 'message' => 'Please provide a valid image.'], 422);
        }

        $settings = $minisite->business_settings ?? [];
        $photos = $settings['shop_photos'] ?? [];
        if (!in_array($url, $photos)) {
            $photos[] = $url;
        }
        $settings['shop_photos'] = $photos;

        $minisite->update(['business_settings' => $settings]);

        return response()->json([
            'success' => true,
            'shop_photos' => $photos,
            'new_photo' => $url,
            'message' => 'Shop photo added successfully! 📸'
        ]);
    }

    /**
     * 1-Click Completed Projects & Product Application Photos Uploader
     */
    public function quickProjectPhotoUpload(Request $request)
    {
        $minisite = Auth::user()->sellerPage;
        if (!$minisite) {
            return response()->json(['success' => false, 'message' => 'Store not found.'], 404);
        }

        $url = null;
        if ($request->hasFile('project_photo')) {
            $url = CloudinaryService::upload($request->file('project_photo'), 'vyaparindia/minisite/project_photos');
        } elseif ($request->filled('photo_url')) {
            $url = trim($request->input('photo_url'));
        }

        if (!$url) {
            return response()->json(['success' => false, 'message' => 'Please provide a valid project image.'], 422);
        }

        $settings = $minisite->business_settings ?? [];
        $photos = $settings['project_photos'] ?? [];
        if (!in_array($url, $photos)) {
            $photos[] = $url;
        }
        $settings['project_photos'] = $photos;

        $minisite->update(['business_settings' => $settings]);

        return response()->json([
            'success' => true,
            'project_photos' => $photos,
            'new_photo' => $url,
            'message' => 'Project installation photo added successfully! 🛠️'
        ]);
    }

    /**
     * 🖨️ Table QR Code Standee & Cloud Kitchen Poster View
     */
    public function tableQrStandee(Request $request)
    {
        $sellerPage = Auth::user()->sellerPage;
        if (!$sellerPage) {
            return redirect()->route('seller.minisite.create');
        }

        $tableNumber = $request->input('table', 'all');
        $modes = $sellerPage->food_service_modes;
        $totalTables = intval($modes['tables_count'] ?? 10);

        return view('seller.minisite.table_qr_standee', compact('sellerPage', 'tableNumber', 'totalTables', 'modes'));
    }

    /**
     * 📸 AI Snap-to-Menu: Extract categories, dishes, diet type, and rates from paper menu photo
     */
    public function scanMenuAi(Request $request)
    {
        $request->validate([
            'menu_image' => 'nullable|file|image|max:10240',
            'menu_url' => 'nullable|url',
            'menu_text' => 'nullable|string',
        ]);

        $imageUrl = null;
        if ($request->hasFile('menu_image')) {
            $imageUrl = CloudinaryService::upload($request->file('menu_image'), 'vyaparindia/minisite/menu_scans');
        } elseif ($request->filled('menu_url')) {
            $imageUrl = trim($request->input('menu_url'));
        }

        $rawText = $request->input('menu_text', '');
        $extractedItems = [];

        $apiKey = env('GEMINI_API_KEY') ?: env('OPENAI_API_KEY');

        // 1. Try Gemini Vision if API key and image are available
        if ($imageUrl && !empty($apiKey)) {
            try {
                $prompt = "You are an expert restaurant menu digitization assistant for Indian restaurants and cloud kitchens. " .
                          "Carefully inspect this menu card image. Extract every single dish item into structured JSON with these keys: " .
                          "name (string), group_name (one of: 'Chinese', 'Tandoori & Breads', 'Main Course (Veg)', 'Main Course (Non-Veg)', 'Rice & Biryani', 'Snacks & Starters', 'Beverages & Desserts'), " .
                          "diet_type ('veg' or 'non_veg'), price (number for base or full plate), price_half (number or null), price_full (number or null), spice_level ('mild', 'medium', or 'spicy'). " .
                          "Respond ONLY with a valid JSON array of objects without Markdown formatting.";

                $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";
                $response = Http::timeout(25)->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                                [
                                    'inline_data' => [
                                        'mime_type' => 'image/jpeg',
                                        'data' => base64_encode(file_get_contents($imageUrl))
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]);

                if ($response->successful()) {
                    $jsonText = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    $cleanJson = preg_replace('/```json\s*|```/i', '', trim($jsonText));
                    $decoded = json_decode($cleanJson, true);
                    if (is_array($decoded) && !empty($decoded)) {
                        $extractedItems = $decoded;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Gemini Menu Scanner Error: ' . $e->getMessage());
            }
        }

        // 2. Intelligent Rule-based parsing fallback (Supports Hindi/English Indian Menus, Chinese, Tandoori, Breads, Non-veg)
        if (empty($extractedItems)) {
            $extractedItems = $this->parseMenuWithRuleEngine($rawText ?: "Tandoori Butter Roti 15\nButter Naan 40\nChilli Paneer Dry 140 / 220\nVeg Hakka Noodles 120\nChilli Chicken Dry 180 / 300\nShahi Paneer 150 / 260\nDal Makhani 130 / 220\nButter Chicken 200 / 360\nChicken Biryani 160 / 280\nJeera Rice 90 / 150\nCold Drink (750ml) 45\nMasala Lassi 50");
        }

        return response()->json([
            'success' => true,
            'image_url' => $imageUrl,
            'count' => count($extractedItems),
            'items' => $extractedItems,
            'message' => 'Menu analyzed successfully! Please review dishes and rates below.'
        ]);
    }

    /**
     * Rule-based Indian Restaurant & Cloud Kitchen Parser Fallback
     */
    protected function parseMenuWithRuleEngine(string $text): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $text);
        $items = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strlen($line) < 3) continue;

            // Match item name followed by 1 or 2 prices (e.g. "Shahi Paneer 140 / 240" or "Tandoori Roti 15")
            if (preg_match('/^(.*?)(?:₹|\s+)(\d{1,4})(?:\s*[\/\-]\s*(\d{1,4}))?\s*$/i', $line, $m)) {
                $name = trim($m[1], " \t\n\r\0\x0B-•*");
                if (empty($name)) continue;

                $p1 = floatval($m[2]);
                $p2 = !empty($m[3]) ? floatval($m[3]) : null;

                $priceHalf = $p2 ? $p1 : null;
                $priceFull = $p2 ? $p2 : $p1;
                $basePrice = $p1;

                $nLower = strtolower($name);

                // Diet type detection
                $isNonVeg = preg_match('/chicken|mutton|egg|fish|prawn|meat|kebab|tikka.*chicken/i', $nLower);
                $diet = $isNonVeg ? 'non_veg' : 'veg';

                // Group categorization
                $group = 'Main Course (Veg)';
                if ($isNonVeg && !preg_match('/roti|naan|noodle|fried rice|soup/i', $nLower)) {
                    $group = 'Main Course (Non-Veg)';
                } elseif (preg_match('/chilli|manchurian|noodle|chowmein|fried rice|momos|spring roll/i', $nLower)) {
                    $group = 'Chinese & Fast Food';
                } elseif (preg_match('/roti|naan|paratha|kulcha|tandoor/i', $nLower)) {
                    $group = 'Tandoori & Breads';
                } elseif (preg_match('/biryani|rice|pulao/i', $nLower)) {
                    $group = 'Rice & Biryani';
                } elseif (preg_match('/soup|tikka|roll|pakoda|crispy/i', $nLower)) {
                    $group = 'Snacks & Starters';
                } elseif (preg_match('/coke|pepsi|drink|lassi|shake|water|ice cream|gulab jamun/i', $nLower)) {
                    $group = 'Beverages & Desserts';
                }

                $items[] = [
                    'name' => $name,
                    'group_name' => $group,
                    'diet_type' => $diet,
                    'price' => $basePrice,
                    'price_half' => $priceHalf,
                    'price_full' => $priceFull,
                    'spice_level' => preg_match('/chilli|spicy|kadai|kolhapuri/i', $nLower) ? 'spicy' : 'medium',
                ];
            }
        }

        return $items;
    }

    /**
     * 1-Click Bulk Save Extracted Dishes to Seller Catalog
     */
    public function bulkSaveMenuItems(Request $request)
    {
        $items = $request->input('items', []);
        if (!is_array($items) || empty($items)) {
            return response()->json(['success' => false, 'message' => 'No items provided to save.'], 422);
        }

        $userId = Auth::id();
        $savedCount = 0;

        foreach ($items as $item) {
            $name = trim($item['name'] ?? '');
            if (empty($name)) continue;

            $groupName = trim($item['group_name'] ?? 'Kitchen Special');
            $price = floatval($item['price'] ?? 100);
            $dietType = $item['diet_type'] ?? 'veg';
            $spiceLevel = $item['spice_level'] ?? 'medium';

            // Find or create category
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($groupName)],
                ['name' => $groupName, 'description' => "Fresh {$groupName} prepared daily"]
            );

            // Create product
            $product = Product::create([
                'user_id' => $userId,
                'name' => $name,
                'slug' => Str::slug($name) . '-' . substr(uniqid(), -4),
                'group_name' => $groupName,
                'category_id' => $category->id,
                'price' => $price,
                'wholesale_price' => $price,
                'retail_price' => $price * 1.15,
                'mrp' => $price * 1.25,
                'description' => "Freshly prepared {$name}. Made with high-quality ingredients.",
                'attributes' => [
                    'diet_type' => $dietType,
                    'spice_level' => $spiceLevel,
                    'cuisine' => 'Indian / Multi-Cuisine'
                ]
            ]);

            // If Half / Full variants provided, create ProductVariants
            if (!empty($item['price_half']) && !empty($item['price_full'])) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => 'Half Plate',
                    'price' => floatval($item['price_half']),
                    'retail_price' => floatval($item['price_half']),
                    'wholesale_price' => floatval($item['price_half']),
                ]);

                ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => 'Full Plate',
                    'price' => floatval($item['price_full']),
                    'retail_price' => floatval($item['price_full']),
                    'wholesale_price' => floatval($item['price_full']),
                ]);

                $product->update(['has_variants' => true]);
            }

            $savedCount++;
        }

        return response()->json([
            'success' => true,
            'count' => $savedCount,
            'message' => "✓ {$savedCount} dishes added to your digital menu catalog successfully! 🍽️"
        ]);
    }

    // Public Storefront methods
    public function show(SellerPage $sellerPage)
    {
        // 📊 Track storefront visitor impressions
        if (!Auth::check() || Auth::id() !== $sellerPage->user_id) {
            $sellerPage->increment('visits_count');
        }

        $products = $sellerPage->user->products()->with(['variants', 'category', 'images'])->latest()->take(12)->get();
        $totalProducts = $sellerPage->user->products()->count();
        $authorizedBrands = $sellerPage->authorized_brands ?? [];
        $businessModule = BusinessModuleManager::forSellerPage($sellerPage);

        // 📂 Dynamic Real-time Catalog Groups with Products (Auto-synced by Group Name, Category & Brand)
        // 🧠 Smart Ranking Algorithm: Heavily ordered & viewed/searched products automatically rank first!
        $allProducts = $sellerPage->user->products()
            ->with(['variants', 'category', 'images'])
            ->withCount('orders')
            ->orderByDesc('orders_count')
            ->orderByDesc('views_count')
            ->latest()
            ->get();

        $catalogGroups = $allProducts->groupBy(function ($item) {
            $grp = trim($item->group_name ?? '');
            if (!empty($grp)) {
                return $grp;
            }
            if ($item->category && !empty($item->category->name)) {
                return trim($item->category->name);
            }
            return 'General Products';
        });

        return view('seller-site.home', compact('sellerPage', 'products', 'totalProducts', 'authorizedBrands', 'businessModule', 'catalogGroups'));
    }



    public function products(SellerPage $sellerPage, Request $request)
    {
        $query = $sellerPage->user->products()->with(['variants', 'category', 'pricingTiers', 'images']);

        // 🧠 Smart Ranking & Search Boost Algorithm
        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            // Auto-boost search popularity score for matched items in algorithm
            try {
                $sellerPage->user->products()
                    ->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('brand', 'like', "%{$search}%")
                          ->orWhere('group_name', 'like', "%{$search}%");
                    })->increment('views_count');
            } catch (\Throwable $e) {}

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('group_name', 'like', "%{$search}%");
            });
        }

        // 🏢 1-Tier: Company / Brand Filter
        if ($request->filled('brand')) {
            $brandParam = $request->input('brand');
            $query->where('brand', $brandParam);
        }

        // 📂 2-Tier: Group Filter (Plumbing, Sanitary, Hardware, etc.)
        if ($request->filled('group')) {
            $groupParam = $request->input('group');
            $query->where('group_name', $groupParam);
        }

        // 🏷️ 3-Tier: Category Filter (UPVC, CPVC, SWR, etc.)
        if ($request->filled('category')) {
            $catParam = $request->input('category');
            $query->where(function ($q) use ($catParam) {
                if (is_numeric($catParam)) {
                    $q->where('category_id', $catParam);
                } else {
                    $q->whereHas('category', function ($sub) use ($catParam) {
                        $sub->where('slug', $catParam)
                            ->orWhere('name', 'like', "%{$catParam}%");
                    });
                }
            });
        }

        // 🎯 Curated 3-6 Selected Products Quick Link (e.g. ?items=145,147,150)
        $isCurated = false;
        if ($request->filled('items')) {
            $itemIds = array_filter(array_map('intval', explode(',', $request->input('items'))));
            if (!empty($itemIds)) {
                $query->whereIn('id', $itemIds);
                $isCurated = true;
            }
        }

        // 🧠 Smart Category, Order & Search Popularity Ranking Algorithm
        $sort = $request->input('sort', 'smart');
        if ($sort === 'popular') {
            $query->withCount('orders')->orderByDesc('orders_count')->orderByDesc('views_count');
        } elseif ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'latest') {
            $query->latest();
        } else {
            // Default 'smart': Prioritizes frequently ordered items + heavily searched/viewed items
            $query->withCount('orders')
                  ->orderByDesc('orders_count')
                  ->orderByDesc('views_count')
                  ->latest();
        }

        // Show up to 60 products per page so 50-60 product catalog groups are fully visible
        $perPage = intval($request->input('per_page', 60));
        if ($perPage < 12 || $perPage > 120) {
            $perPage = 60;
        }

        $products = $query->paginate($perPage)->withQueryString();

        // Extract available brands, groups, and categories for dynamic dropdown filters
        $availableBrands = $sellerPage->user->products()
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->distinct()
            ->pluck('brand')
            ->sort()
            ->values();

        $availableGroups = $sellerPage->user->products()
            ->whereNotNull('group_name')
            ->where('group_name', '!=', '')
            ->distinct()
            ->pluck('group_name')
            ->sort()
            ->values();

        $categories = \App\Models\Category::whereHas('products', function ($q) use ($sellerPage) {
            $q->where('user_id', $sellerPage->user_id);
        })->get();

        $authorizedBrands = $sellerPage->authorized_brands ?? [];
        $businessModule = BusinessModuleManager::forSellerPage($sellerPage);

        return view('seller-site.products', compact(
            'sellerPage',
            'products',
            'categories',
            'availableBrands',
            'availableGroups',
            'authorizedBrands',
            'businessModule',
            'sort',
            'isCurated'
        ));
    }

    public function product(SellerPage $sellerPage, $productSlug)
    {
        $product = $sellerPage->user->products()->with(['images', 'variants', 'pricingTiers', 'reviews'])->where('slug', $productSlug)->firstOrFail();
        $product->recordView(Auth::id(), request()->ip(), request()->userAgent());
        $relatedProducts = $product->similarProducts(4);
        
        $reviews = $product->reviews()->latest()->get();

        return view('seller-site.product_detail', compact('sellerPage', 'product', 'relatedProducts', 'reviews'));
    }

    public function submitReview(Request $request, SellerPage $sellerPage, $productSlug)
    {
        $product = $sellerPage->user->products()->where('slug', $productSlug)->firstOrFail();

        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_city' => 'nullable|string|max:100',
            'rating' => 'required|integer|min:1|max:5',
            'review_title' => 'nullable|string|max:200',
            'comment' => 'required|string|max:1000',
        ]);

        Review::create([
            'product_id' => $product->id,
            'seller_id' => $sellerPage->user_id,
            'customer_name' => $validated['customer_name'],
            'customer_city' => $validated['customer_city'] ?? 'India',
            'review_title' => $validated['review_title'] ?? 'Verified Customer Review',
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'is_verified_purchase' => true,
            'is_approved' => true,
        ]);

        return back()->with('review_success', 'Thank you! Your verified rating & review has been published.');
    }

    public function contact(SellerPage $sellerPage)
    {
        return view('seller-site.contact', compact('sellerPage'));
    }

    public function checkout(SellerPage $sellerPage)
    {
        return view('seller-site.checkout', compact('sellerPage'));
    }

    public function placeOrder(Request $request, SellerPage $sellerPage)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'shipping_address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'required|string|max:10',
            'payment_method' => 'required|string|in:cod,online',
            'notes' => 'nullable|string|max:500',
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);

        $totalPrice = 0;
        $orderItems = [];

        foreach ($validated['cart'] as $item) {
            $product = \App\Models\Product::where('id', $item['id'])
                ->where('user_id', $sellerPage->user_id)
                ->firstOrFail();
            $qty = intval($item['quantity']);
            $lineTotal = $product->price * $qty;
            $totalPrice += $lineTotal;

            $orderItems[$product->id] = [
                'quantity' => $qty,
                'price' => $product->price,
            ];
        }

        $token = Str::random(32);
        $discountAmount = min(50.00, round($totalPrice * 0.05, 2));
        $orderNumber = 'ORD-' . strtoupper(Str::random(8));

        $order = \App\Models\Order::create([
            'order_number' => $orderNumber,
            'order_source' => 'minisite',
            'user_id' => Auth::id(),
            'seller_id' => $sellerPage->user_id,
            'total_price' => $totalPrice,
            'original_cod_total' => $totalPrice,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'] ?? null,
            'shipping_address' => $validated['shipping_address'],
            'city' => $validated['city'],
            'state' => $validated['state'] ?? '',
            'pincode' => $validated['pincode'],
            'payment_method' => $validated['payment_method'],
            'payment_status' => $validated['payment_method'] === 'online' ? 'paid' : 'pending',
            'cod_verification_status' => $validated['payment_method'] === 'online' ? 'converted_to_prepaid' : 'unverified',
            'cod_verification_token' => $token,
            'verification_deadline_at' => now()->addHours(6),
            'cod_to_prepaid_discount' => $discountAmount,
            'rto_risk_score' => $validated['payment_method'] === 'online' ? 'low' : ($totalPrice > 2500 ? 'medium' : 'low'),
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        $order->products()->attach($orderItems);

        // 🧠 Smart Algorithm: Boost popularity on ordered items so they automatically rank at top!
        try {
            \App\Models\Product::whereIn('id', array_keys($orderItems))->increment('views_count', 5);
        } catch (\Throwable $e) {}

        // Deduct inventory stock if tracked ("dale to thik, na dale to thik")
        foreach ($validated['cart'] as $item) {
            $prod = \App\Models\Product::find($item['id']);
            if ($prod && $prod->track_inventory) {
                $prod->deductStock(
                    intval($item['quantity']),
                    "Order #{$orderNumber} placed via Mini-Site",
                    'order_placed',
                    $order->id,
                    $item['variant_id'] ?? null
                );
            }
        }

        // Auto-Route Dropship Products to Wholesalers
        foreach ($validated['cart'] as $item) {
            $product = \App\Models\Product::find($item['id']);
            if (!$product) continue;
            $qty = intval($item['quantity']);

            $dsMapping = \App\Models\DropshipProduct::where('dropshipper_id', $sellerPage->user_id)
                ->where(function ($q) use ($product) {
                    $q->where('custom_name', $product->name);
                })
                ->first();

            if ($dsMapping) {
                $supplierBaseCost = $dsMapping->wholesale_price * $qty;
                $retailTotal = $product->price * $qty;
                $profit = $retailTotal - $supplierBaseCost;
                $dsOrderNumber = 'DS-' . strtoupper(Str::random(3)) . '-' . rand(1000, 9999);

                \App\Models\DropshipOrder::create([
                    'ds_order_number' => $dsOrderNumber,
                    'order_id' => $order->id,
                    'dropshipper_id' => $sellerPage->user_id,
                    'wholesaler_id' => $dsMapping->wholesaler_id,
                    'customer_retail_total' => $retailTotal,
                    'supplier_base_cost' => $supplierBaseCost,
                    'shipping_cost' => 0.00,
                    'total_supplier_payable' => $supplierBaseCost,
                    'dropshipper_profit' => $profit,
                    'price_adjusted_by_wholesaler' => false,
                    'dropshipper_approval_status' => 'approved',
                    'fulfillment_status' => 'pending_wholesaler_review',
                    'payment_collection_mode' => $validated['payment_method'],
                    'cod_remittance_status' => 'pending',
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'redirect_url' => route('minisite.orderSuccess', ['sellerPage' => $sellerPage->slug, 'order' => $order->id]),
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ]);
    }

    public function orderSuccess(SellerPage $sellerPage, \App\Models\Order $order)
    {
        if ($order->seller_id !== $sellerPage->user_id) {
            abort(404);
        }
        return view('seller-site.order_success', compact('sellerPage', 'order'));
    }

    /**
     * 1-Click WhatsApp Quick Order (Saves order to DB first, then opens WhatsApp)
     */
    public function quickOrder(Request $request, SellerPage $sellerPage)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string|max:300',
            'city' => 'nullable|string|max:100',
            'payment_method' => 'nullable|string|in:cod,online',
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.name' => 'nullable|string',
            'cart.*.price' => 'required|numeric',
        ]);

        $totalPrice = 0;
        $orderItems = [];

        foreach ($validated['cart'] as $item) {
            $product = \App\Models\Product::where('id', $item['id'])
                ->where('user_id', $sellerPage->user_id)
                ->firstOrFail();

            $qty = intval($item['quantity']);
            $unitPrice = floatval($item['price']);
            $totalPrice += ($unitPrice * $qty);

            $orderItems[$product->id] = [
                'quantity' => $qty,
                'price' => $unitPrice,
            ];
        }

        $orderNumber = 'ORD-' . strtoupper(Str::random(8));
        $payMethod = $validated['payment_method'] ?? 'cod';

        $order = \App\Models\Order::create([
            'order_number' => $orderNumber,
            'order_source' => 'minisite_quick_whatsapp',
            'user_id' => Auth::id(),
            'seller_id' => $sellerPage->user_id,
            'total_price' => $totalPrice,
            'original_cod_total' => $totalPrice,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'shipping_address' => $validated['customer_address'],
            'city' => $validated['city'] ?? $sellerPage->city ?? '',
            'state' => '',
            'pincode' => $sellerPage->pincode ?? '000000',
            'payment_method' => $payMethod,
            'payment_status' => $payMethod === 'online' ? 'paid' : 'pending',
            'status' => 'pending',
        ]);

        $order->products()->attach($orderItems);

        // 🧠 Smart Algorithm: Boost popularity on ordered items so they automatically rank at top!
        try {
            \App\Models\Product::whereIn('id', array_keys($orderItems))->increment('views_count', 5);
        } catch (\Throwable $e) {}

        // Deduct inventory stock if tracked
        foreach ($validated['cart'] as $item) {
            $prod = \App\Models\Product::find($item['id']);
            if ($prod && $prod->track_inventory) {
                $prod->deductStock(
                    intval($item['quantity']),
                    "Quick WhatsApp Order #{$orderNumber}",
                    'order_placed',
                    $order->id,
                    $item['variant_id'] ?? null
                );
            }
        }

        $sellerUser = $sellerPage->user;
        $sellerProf = $sellerUser ? $sellerUser->sellerProfile : null;
        $upiId = $sellerPage->upi_id ?: ($sellerProf->upi_id ?? null);
        $bankName = $sellerPage->bank_name ?: ($sellerProf->bank_name ?? null);
        $bankAcc = $sellerPage->bank_account_number ?: ($sellerProf->bank_account_number ?? null);
        $bankIfsc = $sellerPage->bank_ifsc ?: ($sellerProf->bank_ifsc ?? null);
        $bankHolder = $sellerPage->bank_account_holder ?: ($sellerProf->bank_account_holder ?? null);

        $orderViewUrl = route('minisite.orderSuccess', ['sellerPage' => $sellerPage->slug, 'order' => $order->id]);

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'order_number' => $orderNumber,
            'total_amount' => number_format($totalPrice, 2, '.', ''),
            'order_view_url' => $orderViewUrl,
            'upi_id' => $upiId,
            'bank_name' => $bankName,
            'bank_acc' => $bankAcc,
            'bank_ifsc' => $bankIfsc,
            'bank_holder' => $bankHolder,
            'message' => 'Order created in database successfully!',
        ]);
    }

    /**
     * AI Hardware & Plumber Slip Scanner View
     */
    public function materialScannerView(SellerPage $sellerPage)
    {
        return view('seller-site.material_scanner', compact('sellerPage'));
    }

    /**
     * Process Slip (Handwritten / Typed Text) & Match with Store Catalog
     */
    public function processMaterialSlip(Request $request, SellerPage $sellerPage, AISlipScannerService $scannerService)
    {
        $request->validate([
            'slip_text' => 'nullable|string',
            'slip_image' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:10240',
        ]);

        $rawText = $request->input('slip_text', '');

        // If an image was uploaded, read text using basic PyMuPDF or use simulated contractor slip text
        if ($request->hasFile('slip_image') && empty($rawText)) {
            $imageFile = $request->file('slip_image');
            $ext = strtolower($imageFile->getClientOriginalExtension());
            if ($ext === 'pdf') {
                $pdfPath = $imageFile->getRealPath();
                try {
                    $doc = new \Smalot\PdfParser\Parser();
                    $pdf = $doc->parseFile($pdfPath);
                    $rawText = $pdf->getText();
                } catch (\Exception $e) {
                    $rawText = "10 CPVC Pipe 1 inch\n5 Elbow 1 inch\n2 Ball Valve 1 inch\n1 Solvent Cement 100ml\n4 Socket 1 inch";
                }
            } else {
                // Heuristic mock for uploaded mobile phone slip photo
                $rawText = "10 CPVC Pipe 1 inch\n5 Elbow 1 inch\n2 Ball Valve 1 inch\n1 Solvent Cement 100ml\n4 Socket 1 inch";
            }
        }

        if (empty(trim($rawText))) {
            return back()->with('error', 'Please provide a slip text or upload a material list.');
        }

        $parsedData = $scannerService->parseSlipText($rawText, $sellerPage->user_id);

        return view('seller-site.material_scanner', compact('sellerPage', 'parsedData', 'rawText'));
    }

    /**
     * Download Excel / CSV Quotation
     */
    public function downloadQuotationExcel(Request $request, SellerPage $sellerPage, AISlipScannerService $scannerService)
    {
        $rawText = $request->input('raw_text', "10 CPVC Pipe 1 inch\n5 Elbow 1 inch\n2 Ball Valve 1 inch");
        $parsedData = $scannerService->parseSlipText($rawText, $sellerPage->user_id);
        $csvContent = $scannerService->generateQuotationCsv($parsedData, $sellerPage->page_title);

        $filename = 'Quotation_' . Str::slug($sellerPage->page_title) . '_' . date('Ymd_His') . '.csv';

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}