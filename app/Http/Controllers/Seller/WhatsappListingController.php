<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WhatsappListingController extends Controller
{
    /**
     * Show dedicated WhatsApp-style AI Product Listing interface
     */
    public function index()
    {
        $user = Auth::user();
        $recentProducts = Product::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('seller.whatsapp_listing.index', compact('recentProducts'));
    }

    /**
     * Handle incoming multi-image + text / voice message
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string|max:3000',
            'images' => 'nullable|array',
            'images.*' => 'string', // Base64 data URIs or image URLs
            'api_key' => 'nullable|string',
        ]);

        $user = Auth::user();
        $rawMessage = trim($request->input('message', ''));
        $rawImages = $request->input('images', []);
        $customApiKey = trim($request->input('api_key', ''));

        // 1. Process and upload images
        $uploadedImages = [];
        foreach ($rawImages as $img) {
            if (empty($img)) continue;
            if (str_starts_with($img, 'data:image')) {
                $cloudUrl = CloudinaryService::uploadBase64($img, 'vyaparindia/whatsapp_listing');
                if ($cloudUrl) {
                    $uploadedImages[] = $cloudUrl;
                }
            } elseif (filter_var($img, FILTER_VALIDATE_URL)) {
                $uploadedImages[] = $img;
            }
        }

        // 2. Perform AI Multimodal Analysis (Gemini / OpenAI or Smart Engine)
        $analysis = $this->analyzeProductData($rawMessage, $uploadedImages, $rawImages, $customApiKey);

        return response()->json([
            'success' => true,
            'reply' => $analysis['reply'],
            'draft' => $analysis['draft'],
        ]);
    }

    /**
     * One-Click Instant Live Publish
     */
    public function publish(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->is_seller()) {
            return response()->json(['success' => false, 'message' => 'केवल विक्रेता (Sellers) ही प्रोडक्ट लाइव कर सकते हैं।'], 403);
        }

        if (!$user->canAddProduct()) {
            return response()->json([
                'success' => false,
                'message' => 'प्रोडक्ट लिमिट पूरी हो गई है (50/50)। असीमित प्रोडक्ट्स के लिए मिनी-वेबसाइट अपग्रेड करें।',
                'upgrade_required' => true,
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'mrp' => 'nullable|numeric|min:0.01',
            'category' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'image_url' => 'nullable|string',
            'gallery_images' => 'nullable|array',
            'size' => 'nullable|string',
            'packaging' => 'nullable|string',
            'description' => 'nullable|string',
            'stock_quantity' => 'nullable|integer|min:0',
            'variants' => 'nullable|array',
        ]);

        $catName = trim($request->input('category', 'Hardware & Sanitary'));
        $category = Category::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($catName)])->first();
        if (!$category) {
            $category = Category::create([
                'name' => $catName,
                'slug' => Str::slug($catName) . '-' . Str::random(4),
            ]);
        }

        $name = trim($request->input('name'));
        $price = floatval($request->input('price'));
        $mrp = floatval($request->input('mrp', 0));
        if ($mrp <= 0) {
            $mrp = round($price * 1.35, 2);
        }

        $brand = trim($request->input('brand', ''));
        $imageUrl = $request->input('image_url');
        $galleryImages = $request->input('gallery_images', []);
        $size = trim($request->input('size', 'Standard'));
        $packaging = trim($request->input('packaging', 'Box Packing'));
        $description = trim($request->input('description', ''));
        if (empty($description)) {
            $description = "प्रीमियम क्वालिटी " . ($brand ? "{$brand} " : "") . "{$name}। थोक व रीटेल दोनों व्यापारियों के लिए तैयार।";
        }
        $stockQuantity = intval($request->input('stock_quantity', 100));
        $variants = $request->input('variants', []);

        $product = Product::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'sku' => 'WA-' . strtoupper(Str::random(7)),
            'brand' => $brand,
            'price' => $price,
            'mrp' => $mrp,
            'wholesale_price' => $price,
            'retail_price' => $price,
            'stock_quantity' => $stockQuantity,
            'description' => $description,
            'image' => $imageUrl,
            'attributes' => [
                'size' => $size,
                'packaging' => $packaging,
                'gallery_images' => $galleryImages,
                'source' => 'whatsapp_ai_chat',
            ],
            'is_active' => true,
        ]);

        // If multiple variants or sizes were detected
        if (!empty($variants) && is_array($variants)) {
            $product->has_variants = true;
            $product->save();

            foreach ($variants as $v) {
                if (empty($v['variant_name'])) continue;
                ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => $v['variant_name'],
                    'sku' => 'WAV-' . strtoupper(Str::random(6)),
                    'retail_price' => floatval($v['price'] ?? $price),
                    'wholesale_price' => floatval($v['price'] ?? $price),
                    'mrp' => floatval($v['mrp'] ?? $mrp),
                    'stock_quantity' => intval($v['stock'] ?? 50),
                    'is_active' => true,
                    'image_url' => $v['image_url'] ?? $imageUrl,
                    'attributes' => [
                        'size' => $v['size'] ?? $size,
                        'packaging' => $v['packaging'] ?? $packaging,
                    ]
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "✓ '{$product->name}' सफलतापूर्वक लाइव स्टोर में पब्लिश हो गया!",
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'mrp' => $product->mrp,
                'url' => route('product.show', $product->slug),
                'image' => $product->image,
            ]
        ]);
    }

    /**
     * Multimodal Vision AI Analyzer (Gemini / OpenAI with Smart Rule Engine Fallback)
     */
    protected function analyzeProductData(string $message, array $uploadedImages, array $rawBase64Images, string $customApiKey): array
    {
        $primaryImage = !empty($uploadedImages) ? $uploadedImages[0] : null;
        $apiKey = $customApiKey ?: env('GEMINI_API_KEY') ?: env('OPENAI_API_KEY');

        // Attempt Vision API if available and user provided images or detailed prompt
        if (!empty($apiKey)) {
            try {
                if (str_starts_with($apiKey, 'AIza') || env('GEMINI_API_KEY')) {
                    $aiResult = $this->callGeminiVision($message, $rawBase64Images, env('GEMINI_API_KEY') ?: $apiKey);
                    if ($aiResult) {
                        return $this->formatDraftResult($aiResult, $uploadedImages);
                    }
                } else {
                    $aiResult = $this->callOpenAiVision($message, $rawBase64Images, $apiKey);
                    if ($aiResult) {
                        return $this->formatDraftResult($aiResult, $uploadedImages);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('WhatsApp AI Vision call failed: ' . $e->getMessage());
            }
        }

        // Smart Indian B2B Hardware/Sanitary Rule-Based Engine
        return $this->analyzeWithSmartEngine($message, $uploadedImages);
    }

    /**
     * Gemini 1.5 Flash Multimodal Vision
     */
    protected function callGeminiVision(string $message, array $base64Images, string $apiKey): ?array
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";

        $systemInstruction = "You are VyaparIndia B2B Catalog AI Assistant for Indian hardware, sanitaryware, plumbing, plywood, and electricals merchants.
The merchant sent product photos (which may include product, packaging carton box with MRP / barcode / specifications) and instructions in Hindi or English: \"{$message}\".
Extract and return STRICT JSON ONLY (no markdown ticks, no backticks, just raw json) with keys:
{
  \"name\": \"Clear professional product title in English with size (e.g. Florex Dual Flow Slim Cistern 10 Litre, Plasto 15mm Brass Bib Cock, Asian Paints Apex 20L)\",
  \"brand\": \"Brand name (e.g. Florex, Plasto, Astral, Supreme, Greenply, Asian Paints, etc.)\",
  \"category\": \"Category name (e.g. Bathroom Fittings, Plumbing & Pipes, Sanitaryware, Plywood & Boards, Hardware, Paints)\",
  \"mrp\": 1200.00,
  \"price\": 660.00,
  \"size\": \"10 Litre / 15mm / 18mm / Standard\",
  \"packaging\": \"4 Pcs / Box or 1 Pc\",
  \"description\": \"2-line crisp B2B wholesale description in Hindi or English\",
  \"variants\": []
}
Note on price calculation:
- If user mentions discount (e.g., '45% less' or '40% off'), calculate price = mrp * (1 - discount/100).
- If user mentions explicit selling rate (e.g., 'selling 660' or 'rate 660'), use that as price.
- If MRP is on box/sticker, extract exact MRP.";

        $parts = [];
        $parts[] = ['text' => $systemInstruction . "\n\nUser Instruction: " . ($message ?: 'Inspect the attached photos and prepare product listing')];

        foreach (array_slice($base64Images, 0, 3) as $b64) {
            if (preg_match('/^data:image\/(\w+);base64,(.+)$/', $b64, $matches)) {
                $mimeType = 'image/' . ($matches[1] === 'jpg' ? 'jpeg' : $matches[1]);
                $data = $matches[2];
                $parts[] = [
                    'inline_data' => [
                        'mime_type' => $mimeType,
                        'data' => $data
                    ]
                ];
            }
        }

        $res = Http::timeout(18)->post($url, [
            'contents' => [
                ['parts' => $parts]
            ],
            'generationConfig' => [
                'temperature' => 0.2,
                'maxOutputTokens' => 600,
            ]
        ]);

        if ($res->successful()) {
            $raw = $res->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $raw = trim(preg_replace('/```json|```/', '', $raw));
            $json = json_decode($raw, true);
            if (is_array($json) && !empty($json['name'])) {
                return $json;
            }
        }

        return null;
    }

    /**
     * OpenAI GPT-4o-mini Multimodal Vision
     */
    protected function callOpenAiVision(string $message, array $base64Images, string $apiKey): ?array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are VyaparIndia B2B Catalog AI Assistant. Output strict JSON only with keys: name, brand, category, mrp, price, size, packaging, description, variants.'
            ]
        ];

        $content = [
            ['type' => 'text', 'text' => "Inspect these product photos and merchant instructions: \"{$message}\". Return strict JSON."]
        ];

        foreach (array_slice($base64Images, 0, 3) as $b64) {
            if (str_starts_with($b64, 'data:image')) {
                $content[] = [
                    'type' => 'image_url',
                    'image_url' => ['url' => $b64, 'detail' => 'low']
                ];
            }
        }

        $messages[] = ['role' => 'user', 'content' => $content];

        $res = Http::withToken($apiKey)->timeout(18)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => $messages,
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.2,
            'max_tokens' => 600,
        ]);

        if ($res->successful()) {
            $raw = $res->json()['choices'][0]['message']['content'] ?? '';
            $json = json_decode($raw, true);
            if (is_array($json) && !empty($json['name'])) {
                return $json;
            }
        }

        return null;
    }

    /**
     * Format Vision Output into Clean Response
     */
    protected function formatDraftResult(array $data, array $uploadedImages): array
    {
        $primary = $uploadedImages[0] ?? null;
        $mrp = floatval($data['mrp'] ?? 0);
        $price = floatval($data['price'] ?? 0);

        if ($price <= 0 && $mrp > 0) {
            $price = round($mrp * 0.65, 2);
        }
        if ($mrp <= 0 && $price > 0) {
            $mrp = round($price * 1.35, 2);
        }

        $discountPercent = ($mrp > 0 && $price > 0 && $mrp >= $price) 
            ? round((($mrp - $price) / $mrp) * 100) 
            : 0;

        $name = trim($data['name'] ?? 'Industrial Grade Hardware');
        $brand = trim($data['brand'] ?? '');
        $category = trim($data['category'] ?? 'Hardware & Sanitary');

        return [
            'reply' => "समझ गया! मैंने आपके प्रोडक्ट का ड्राफ्ट तैयार कर दिया है 👇\nनिचे दिए गए कार्ड पर **'🟢 1-Click Live'** बटन दबाएं और यह तुरंत आपकी दुकान व मार्केटप्लेस पर लाइव हो जाएगा!",
            'draft' => [
                'name' => $name,
                'brand' => $brand,
                'category' => $category,
                'mrp' => $mrp,
                'price' => $price,
                'discount_percent' => $discountPercent,
                'size' => $data['size'] ?? 'Standard',
                'packaging' => $data['packaging'] ?? '1 Box',
                'description' => $data['description'] ?? "Verified wholesale quality {$name}.",
                'image_url' => $primary,
                'gallery_images' => $uploadedImages,
                'variants' => $data['variants'] ?? [],
            ]
        ];
    }

    /**
     * Smart Rule-Based Engine (Guaranteed zero failure, 100% offline ready)
     */
    protected function analyzeWithSmartEngine(string $message, array $uploadedImages): array
    {
        $primary = $uploadedImages[0] ?? null;
        $msgLower = strtolower($message);

        // Detect Known Brands
        $brands = ['Florex', 'Plasto', 'Astral', 'Supreme', 'Ashirvad', 'Prince', 'Finolex', 'Parryware', 'Hindware', 'Cera', 'Jaquar', 'Asian Paints', 'Tata Tiscon', 'Tata', 'Greenply', 'Century'];
        $detectedBrand = '';
        foreach ($brands as $b) {
            if (stripos($msgLower, strtolower($b)) !== false) {
                $detectedBrand = $b;
                break;
            }
        }

        // Detect Category & Base Name
        $category = 'Hardware & Sanitary';
        $baseName = 'Industrial Fitting';

        if (stripos($msgLower, 'cistern') !== false || stripos($msgLower, 'flush tank') !== false) {
            $category = 'Bathroom Fittings';
            $baseName = 'Dual Flow Slim Cistern (10 Ltr)';
        } elseif (stripos($msgLower, 'seat cover') !== false || stripos($msgLower, 'commode') !== false) {
            $category = 'Sanitaryware';
            $baseName = 'Soft Close Heavy Commode Seat Cover';
        } elseif (stripos($msgLower, 'bib cock') !== false || stripos($msgLower, 'tap') !== false || stripos($msgLower, 'nal') !== false) {
            $category = 'Bathroom Fittings';
            $baseName = 'Brass Core Bib Cock Tap';
        } elseif (stripos($msgLower, 'ball valve') !== false) {
            $category = 'Plumbing & Pipes';
            $baseName = 'Heavy Duty PVC Ball Valve';
        } elseif (stripos($msgLower, 'pipe') !== false || stripos($msgLower, 'fitting') !== false) {
            $category = 'Plumbing & Pipes';
            $baseName = 'UPVC High Pressure Pipe / Fitting';
        } elseif (stripos($msgLower, 'ply') !== false || stripos($msgLower, 'plywood') !== false) {
            $category = 'Plywood & Boards';
            $baseName = 'Commercial Grade Plywood';
        } elseif (!empty($message)) {
            // Clean title from prompt
            $clean = trim(preg_replace('/(list|add|karo|kar|do|dalo|bhai|ye|hai|ka|ki|ke|rate|price|selling|mrp|rs|rupaye|\d+)/i', '', $message));
            if (strlen($clean) > 3) {
                $baseName = ucwords($clean);
            }
        }

        // Detect Size
        $size = 'Standard';
        if (preg_match('/(\d+\s*(?:mm|inch|\"|mtr|meter|ltr|litre|l))/i', $message, $m)) {
            $size = trim($m[1]);
        }

        // Detect Packaging
        $packaging = 'Box Packing';
        if (preg_match('/(\d+\s*(?:piece|pc|pcs|piece ka|pcs ka|box|carton|packet))/i', $message, $m)) {
            $packaging = trim($m[1]);
        }

        // Detect Pricing (MRP, Selling Price, Discount %)
        $mrp = 0;
        $price = 0;
        $discount = 0;

        // Check for MRP
        if (preg_match('/mrp\s*[:=]?\s*(\d+(?:\.\d+)?)/i', $message, $m)) {
            $mrp = floatval($m[1]);
        }

        // Check for discount % (e.g. 45% less, 40% discount)
        if (preg_match('/(\d+(?:\.\d+)?)\s*%\s*(?:less|discount|off)/i', $message, $m)) {
            $discount = floatval($m[1]);
        }

        // Check for selling rate / price
        if (preg_match('/(?:selling|rate|price|apna rate|selling price|bhav)\s*[:=]?\s*(\d+(?:\.\d+)?)/i', $message, $m)) {
            $price = floatval($m[1]);
        } elseif (preg_match('/(\d+(?:\.\d+)?)\s*(?:rs|rupaye|me|me dal do)/i', $message, $m)) {
            if ($price == 0 && floatval($m[1]) != $mrp) {
                $price = floatval($m[1]);
            }
        }

        // Calculate missing price or MRP
        if ($mrp > 0 && $discount > 0 && $price == 0) {
            $price = round($mrp * (1 - ($discount / 100)), 2);
        } elseif ($price > 0 && $mrp == 0) {
            $mrp = round($price * 1.35, 2);
            $discount = 26;
        } elseif ($mrp > 0 && $price > 0 && $discount == 0) {
            $discount = round((($mrp - $price) / $mrp) * 100);
        }

        if ($price <= 0 && $mrp <= 0) {
            $price = 450;
            $mrp = 650;
            $discount = 30;
        }

        $hasSizeInBase = preg_match('/\d+\s*(?:mm|inch|ltr|litre|l)/i', $baseName);
        $fullTitle = trim(($detectedBrand ? "{$detectedBrand} " : "") . "{$baseName}" . ($size !== 'Standard' && !$hasSizeInBase ? " ({$size})" : ""));

        return [
            'reply' => "समझ गया! मैंने आपके प्रोडक्ट का विवरण तैयार कर दिया है 👇\nकृपया नीचे दिए गए कार्ड को चेक करें और **'🟢 1-Click Live'** पर टैप करें:",
            'draft' => [
                'name' => $fullTitle,
                'brand' => $detectedBrand ?: 'Verified Brand',
                'category' => $category,
                'mrp' => $mrp,
                'price' => $price,
                'discount_percent' => $discount,
                'size' => $size,
                'packaging' => $packaging,
                'description' => "प्रीमियम गुणवत्ता " . ($detectedBrand ? "{$detectedBrand} " : "") . "{$baseName}। B2B थोक व रीटेल व्यापारियों के लिए तुरंत डिस्पैच हेतु उपलब्ध।",
                'image_url' => $primary,
                'gallery_images' => $uploadedImages,
                'variants' => [],
            ]
        ];
    }
}
