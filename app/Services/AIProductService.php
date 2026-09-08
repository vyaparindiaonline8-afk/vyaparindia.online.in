<?php

namespace App\Services;

use Illuminate\Support\Str;

class AIProductService
{
    /**
     * Generate rich e-commerce details, SEO copy, tags, and HSN codes from a product name/keyword.
     */
    public function generateProductDetails(string $prompt, ?string $category = null): array
    {
        $cleanPrompt = trim($prompt);
        $titleWords = explode(' ', $cleanPrompt);
        $primaryKeyword = $cleanPrompt;

        // Determine category-specific templates
        $isClothing = preg_match('/saree|kurta|shirt|dress|silk|cotton|fabric|dupatta|wear|apparel/i', $cleanPrompt);
        $isElectronics = preg_match('/watch|headphone|earbud|phone|cable|charger|speaker|gadget/i', $cleanPrompt);
        
        $hsnCode = $isClothing ? '500720' : ($isElectronics ? '851830' : '650500');
        $skuPrefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $cleanPrompt), 0, 4));
        $sku = ($skuPrefix ?: 'PROD') . '-' . rand(100, 999) . '-IN';

        // Generate Enhanced SEO Title
        $enhancedTitle = ucwords($cleanPrompt);
        if ($isClothing && !stripos($enhancedTitle, 'Premium')) {
            $enhancedTitle = "Premium Handcrafted " . $enhancedTitle . " (Festive Edition)";
        } elseif ($isElectronics && !stripos($enhancedTitle, 'Pro')) {
            $enhancedTitle = "Smart " . $enhancedTitle . " with Fast Charge & Deep Bass";
        }

        // Generate High-Converting Description with Bullet Points
        if ($isClothing) {
            $description = "✨ **Elevate your ethnic elegance with this {$cleanPrompt}.**\n\n" .
                "Expertly crafted with traditional weaving techniques, this product combines premium quality fabric with timeless aesthetic charm.\n\n" .
                "🌟 **Key Highlights & Features:**\n" .
                "• **Fabric Material:** 100% Breathable Premium Woven Blend\n" .
                "• **Craftsmanship:** Authentic artisan detailing with reinforced stitching\n" .
                "• **Occasion:** Ideal for Festive gatherings, Weddings, Party wear, and Formal celebrations\n" .
                "• **Package Contains:** 1 Complete Set with protective branded packaging\n" .
                "• **Care Instructions:** Dry clean or gentle hand wash with mild detergent\n\n" .
                "📦 Direct factory wholesale dispatch with 100% quality guarantee.";
        } else {
            $description = "⚡ **Experience superior performance with the all-new {$cleanPrompt}.**\n\n" .
                "Engineered for daily durability and peak efficiency, offering premium build quality at unbeatable value.\n\n" .
                "🌟 **Key Highlights & Features:**\n" .
                "• **Build & Design:** Ergonomic, lightweight, and modern aesthetic\n" .
                "• **Performance:** High-speed efficiency with seamless compatibility\n" .
                "• **Warranty:** 1-Year Manufacturer Replacement Support\n" .
                "• **Included in Box:** 1 Unit, User Guide, Safety Warranty Card\n\n" .
                "📦 Fast pan-India shipping with secure transit protection.";
        }

        // Generate SEO Tags
        $baseTags = [$cleanPrompt, 'best price', 'trending', 'online buy', 'india wholesale'];
        if ($isClothing) {
            $baseTags = array_merge($baseTags, ['ethnic wear', 'festive fashion', 'handcrafted', 'saree collection']);
        }
        $tagsString = implode(', ', $baseTags);

        return [
            'enhanced_title' => $enhancedTitle,
            'description' => $description,
            'sku' => $sku,
            'hsn_code' => $hsnCode,
            'tags' => $tagsString,
            'suggested_price' => rand(499, 2999),
            'suggested_mrp' => rand(3499, 4999),
        ];
    }
}