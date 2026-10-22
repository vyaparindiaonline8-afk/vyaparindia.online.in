<?php

namespace App\Services\AICopilot;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SellerMedia;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AICopilotService
{
    /**
     * Main conversation handler. Supports Text, Voice transcript, and Image attachment.
     */
    public function handleMessage(string $message, ?string $imageData = null, ?User $user = null, array $context = []): array
    {
        $role = $user ? ($user->is_seller() ? 'seller' : 'buyer') : 'guest';
        $userId = $user ? $user->id : null;

        // 1. Check for specific direct intent actions (Product creation, pricing updates, quotation, upgrade)
        $actionResult = $this->detectAndExecuteAction($message, $imageData, $user, $role, $context);

        // 2. Call LLM (Gemini or OpenAI) with system prompt & business context
        $aiReply = $this->generateAIResponse($message, $imageData, $role, $user, $actionResult);

        return [
            'reply' => $aiReply,
            'role' => $role,
            'action' => $actionResult['action'] ?? null,
            'action_data' => $actionResult['data'] ?? null,
            'suggested_replies' => $this->getSuggestedReplies($role, $actionResult['action'] ?? null),
        ];
    }

    /**
     * Detect actionable intent from user message or image upload.
     */
    protected function detectAndExecuteAction(string $message, ?string $imageData, ?User $user, string $role, array $context): array
    {
        $msgLower = strtolower($message);

        // ACTION A: Seller Wants to List / Create Product (from image or command)
        if ($role === 'seller' && ($imageData || preg_match('/(list|add|bana|create|upload|rate).*product|plywood|pipe|fitting|door|hardware/i', $msgLower))) {
            return $this->handleProductDraft($message, $imageData, $user);
        }

        // ACTION B: Profile / Plan Upgrade to Mini-Website
        if ($role === 'seller' && preg_match('/upgrade|mini[- ]website|domain|pro plan|premium|limit badha/i', $msgLower)) {
            return [
                'action' => 'upgrade_profile',
                'data' => [
                    'title' => 'Mini-Website & Custom Domain Upgrade',
                    'benefits' => [
                        'Unlimited product listings (no 50-item limit)',
                        'Your own branded web store (e.g., yourname.vyaparindia.online)',
                        'Custom domain connection (e.g., www.yourbusiness.com)',
                        'Direct customer WhatsApp ordering & zero commission'
                    ],
                    'action_url' => route('seller.minisite.settings'),
                    'action_label' => 'Upgrade to Mini-Website Now'
                ]
            ];
        }

        // ACTION C: Buyer Searching for Wholesale Rates & Verified Sellers
        if (preg_match('/rate|price|bhav|kya rate|chahiye|supplier|seller|wholesale/i', $msgLower)) {
            return $this->handleCatalogSearch($message);
        }

        // ACTION D: Generate B2B Wholesale Quotation
        if (preg_match('/quotation|quote|estimate|parcha|bill estimate/i', $msgLower)) {
            return [
                'action' => 'quotation_ready',
                'data' => [
                    'title' => 'B2B Quotation Generator',
                    'description' => 'Aap kisi bhi product ka item name aur quantity batayein, VyaparIndia Copilot instant GST invoice quotation calculate kar dega.',
                ]
            ];
        }

        return ['action' => null, 'data' => null];
    }

    /**
     * Handle Product Draft Creation from chat text or uploaded image.
     */
    protected function handleProductDraft(string $message, ?string $imageData, ?User $user): array
    {
        $name = 'New Wholesale Item';
        $price = 0;
        $category = 'General Hardware';
        $thicknessOrSize = '';

        // Extract price if specified (e.g., "75 rs", "rate 120", "price 450")
        if (preg_match('/(?:rate|price|rs\.?|₹)\s*[:=]?\s*(\d+(?:\.\d+)?)/i', $message, $m)) {
            $price = floatval($m[1]);
        } elseif (preg_match('/(\d+(?:\.\d+)?)\s*(?:rs|rupaye|inr)/i', $message, $m)) {
            $price = floatval($m[1]);
        }

        // Extract sizes (e.g., "18mm", "12 mm", "30mm", "1 inch", "110mm")
        if (preg_match('/(\d+\s*(?:mm|inch|\"|meter|mtr|cm))/i', $message, $m)) {
            $thicknessOrSize = trim($m[1]);
        }

        // Infer category & product name
        if (stripos($message, 'ply') !== false || stripos($message, 'plywood') !== false) {
            $category = 'Plywood & Boards';
            $name = ($thicknessOrSize ? "{$thicknessOrSize} " : "") . 'Commercial Grade Plywood';
        } elseif (stripos($message, 'flush door') !== false || stripos($message, 'door') !== false) {
            $category = 'Flush Doors';
            $name = ($thicknessOrSize ? "{$thicknessOrSize} " : "") . 'Pine Wood Flush Door';
        } elseif (stripos($message, 'pipe') !== false || stripos($message, 'pvc') !== false || stripos($message, 'fitting') !== false) {
            $category = 'Plumbing & Pipes';
            $name = ($thicknessOrSize ? "{$thicknessOrSize} " : "") . 'UPVC High Pressure Pipe / Fitting';
        } else {
            // Clean title from prompt
            $cleanTitle = trim(preg_replace('/(list|add|karo|karna|hai|rate|price|rs|rupaye|\d+)/i', '', $message));
            if (!empty($cleanTitle) && strlen($cleanTitle) > 3) {
                $name = ucwords(trim($cleanTitle));
            }
        }

        // Save image if attached
        $imageUrl = null;
        if ($imageData) {
            if (str_starts_with($imageData, 'data:image')) {
                $uploaded = CloudinaryService::uploadBase64($imageData, 'vyaparindia/copilot');
                $imageUrl = $uploaded ?: null;
            } elseif (filter_var($imageData, FILTER_VALIDATE_URL)) {
                $imageUrl = $imageData;
            }
        }

        return [
            'action' => 'product_draft_created',
            'data' => [
                'name' => $name,
                'category' => $category,
                'price' => $price ?: 100,
                'size' => $thicknessOrSize ?: 'Standard',
                'image_url' => $imageUrl,
                'sku' => 'AI-' . strtoupper(Str::random(6)),
                'message' => 'Product draft taiyar hai! Niche diye button par click karke ek click me publish karein.'
            ]
        ];
    }

    /**
     * Search verified wholesale rates & catalog products.
     */
    protected function handleCatalogSearch(string $query): array
    {
        $cleanTerm = trim(preg_replace('/(rate|kya|hai|chahiye|price|wholesale|bhav)/i', '', $query));
        $products = Product::with(['seller.sellerPage', 'category'])
            ->where(function ($q) use ($cleanTerm) {
                $q->where('name', 'LIKE', "%{$cleanTerm}%")
                  ->orWhere('description', 'LIKE', "%{$cleanTerm}%");
            })
            ->latest()
            ->limit(3)
            ->get();

        $items = [];
        foreach ($products as $p) {
            $items[] = [
                'name' => $p->name,
                'price' => '₹' . number_format($p->price, 2),
                'seller' => $p->seller->business_name ?? ($p->seller->sellerPage->page_title ?? 'Verified Seller'),
                'url' => route('product.show', $p->slug),
            ];
        }

        return [
            'action' => 'catalog_results',
            'data' => [
                'term' => $cleanTerm,
                'count' => count($items),
                'items' => $items,
            ]
        ];
    }

    /**
     * Generate LLM or Smart Fallback Response.
     */
    protected function generateAIResponse(string $prompt, ?string $imageData, string $role, ?User $user, array $actionResult): string
    {
        $apiKey = env('GEMINI_API_KEY') ?: env('OPENAI_KEY') ?: env('OPENAI_API_KEY');

        // If Gemini or OpenAI is configured, invoke model
        if (!empty($apiKey)) {
            try {
                if (str_starts_with($apiKey, 'AIza') || env('GEMINI_API_KEY')) {
                    return $this->callGemini($prompt, env('GEMINI_API_KEY') ?: $apiKey, $role, $user, $actionResult);
                } else {
                    return $this->callOpenAI($prompt, $apiKey, $role, $user, $actionResult);
                }
            } catch (\Throwable $e) {
                Log::warning('AI Copilot LLM call failed, falling back to rule-based engine: ' . $e->getMessage());
            }
        }

        // Rule-Based Expert Business Engine Fallback
        return $this->generateRuleBasedResponse($prompt, $role, $user, $actionResult);
    }

    /**
     * Call Google Gemini API
     */
    protected function callGemini(string $prompt, string $apiKey, string $role, ?User $user, array $actionResult): string
    {
        $systemPrompt = $this->buildSystemPrompt($role, $user, $actionResult);
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";

        $response = Http::timeout(10)->post($url, [
            'system_instruction' => [
                'parts' => [['text' => $systemPrompt]]
            ],
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'temperature' => 0.4,
                'maxOutputTokens' => 350,
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if ($reply) return trim($reply);
        }

        return $this->generateRuleBasedResponse($prompt, $role, $user, $actionResult);
    }

    /**
     * Call OpenAI API
     */
    protected function callOpenAI(string $prompt, string $apiKey, string $role, ?User $user, array $actionResult): string
    {
        $systemPrompt = $this->buildSystemPrompt($role, $user, $actionResult);
        $response = Http::withToken($apiKey)->timeout(10)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.4,
            'max_tokens' => 350,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $reply = $data['choices'][0]['message']['content'] ?? null;
            if ($reply) return trim($reply);
        }

        return $this->generateRuleBasedResponse($prompt, $role, $user, $actionResult);
    }

    /**
     * Rule-Based Intelligent Fallback Response (Never crashes, instant response).
     */
    protected function generateRuleBasedResponse(string $prompt, string $role, ?User $user, array $actionResult): string
    {
        $p = strtolower($prompt);

        if ($actionResult['action'] === 'product_draft_created') {
            $d = $actionResult['data'];
            return "Maine aapka product draft taiyar kar diya hai: **{$d['name']}** (Category: {$d['category']}, Rate: ₹{$d['price']}). Niche card par 'Publish' daba kar aap ise direct apne store me live kar sakte hain!";
        }

        if ($actionResult['action'] === 'upgrade_profile') {
            return "Namaste! Mini-Website me upgrade karke aapko unlimited products listing, custom domain, aur apna branded online storefront milta hai. Niche diye button se aap turant upgrade shuru kar sakte hain.";
        }

        if ($actionResult['action'] === 'catalog_results' && !empty($actionResult['data']['items'])) {
            return "Humne VyaparIndia ke verified sellers se aapke liye top rates khoj liye hain. Niche cards me aap live prices dekh kar direct seller se baat kar sakte hain.";
        }

        if ($role === 'seller') {
            return "Namaste Seller Partner! Main VyaparIndia ka AI Business Assistant hoon. Aap mujhse kisi bhi product ki photo bhej kar live card banwa sakte hain, rates change karwa sakte hain, ya apne store ko upgrade kar sakte hain. Batayein aaj kya list karna hai?";
        }

        return "Namaste! Main VyaparIndia AI Assistant hoon. Main aapko verified manufacturers aur wholesale rates dhoondhne me madad kar sakta hoon. Aap kis product ya category ke baare me janna chahte hain?";
    }

    /**
     * Build System Prompt for LLMs.
     */
    protected function buildSystemPrompt(string $role, ?User $user, array $actionResult): string
    {
        $userName = $user ? $user->name : 'Customer';
        return "You are 'VyaparIndia AI Copilot', an expert, highly supportive Indian B2B marketplace assistant.
User Role: {$role} (Name: {$userName}).
Language: Friendly, professional Hindi + English mix (Hinglish).
Key Capabilities:
1. Help buyers discover wholesale products, live factory rates, and verified suppliers across India.
2. Help sellers list products instantly (creating product cards, setting prices, categorizing), managing inventory, and upgrading to Mini-Website.
3. Keep answers concise, helpful, and action-oriented. Never hallucinate fake prices.
Action State: " . json_encode($actionResult);
    }

    /**
     * Contextual Quick Reply Pills
     */
    protected function getSuggestedReplies(string $role, ?string $action): array
    {
        if ($role === 'seller') {
            return [
                '📦 Naya Product List Karo',
                '⭐ Mini-Website me Upgrade Karo',
                '💰 Aaj ke Wholesale Rates Dekho',
                '📑 B2B Quotation Estimate Banao'
            ];
        }

        return [
            '🔍 Top Wholesale Products',
            '🏢 Verified Sellers se Connect',
            '📋 Bulk Order Quotation',
            '🛡️ VyaparIndia Trust & Safety'
        ];
    }
}
