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
        $pLower = strtolower($cleanPrompt);

        // 1. Detect Domain & Industry
        $isPtmt = preg_match('/ptmt|nal|tap|cock|bib|pillar|faucet|sink|shower|sanitary/i', $pLower);
        $isPlumbing = preg_match('/cpvc|upvc|pvc|pipe|fitting|elbow|tee|coupler|union|reducer|brass|valve/i', $pLower);
        $isTiles = preg_match('/tile|vitrified|ceramic|gvt|pgvt|slab|marble|granite|flooring/i', $pLower);
        $isPaints = preg_match('/paint|primer|distemper|emulsion|enamel|color|asian|berger/i', $pLower);
        $isElectrical = preg_match('/wire|cable|switch|socket|mcb|led|bulb|light|fan/i', $pLower);
        $isClothing = preg_match('/saree|kurta|shirt|dress|silk|cotton|fabric|dupatta|wear|apparel/i', $pLower);

        // 2. Resolve HSN Code, Category, and Default Pricing
        if ($isPtmt) {
            $hsnCode = '39229000'; // Plastic & PTMT sanitaryware / taps
            $catLabel = 'Sanitaryware & Taps';
            $defaultPrice = 189;
            $defaultMrp = 399;
            $searchVolume = '65,000+ मंथली सर्च (Amazon & Meesho)';
            $sweetSpot = '₹149 - ₹249 (Pack of 1 या 2)';
            $demandLevel = 'High (दैनिक आवश्यक सामान - हर घर में ज़रूरत)';
        } elseif ($isPlumbing) {
            $hsnCode = '39174000'; // Plastic pipes and fittings
            $catLabel = 'Plumbing & Hardware';
            $defaultPrice = 145;
            $defaultMrp = 290;
            $searchVolume = '95,000+ मंथली सर्च (India-wide)';
            $sweetSpot = '₹120 - ₹220 प्रति यूनिट';
            $demandLevel = 'Very High (प्लंबर व कॉन्ट्रैक्टर थोक मांग)';
        } elseif ($isTiles) {
            $hsnCode = '69072100'; // Ceramic & Vitrified tiles
            $catLabel = 'Tiles & Flooring';
            $defaultPrice = 380;
            $defaultMrp = 650;
            $searchVolume = '1,20,000+ मंथली सर्च';
            $sweetSpot = '₹320 - ₹480 प्रति बॉक्स';
            $demandLevel = 'Massive (घर निर्माण & रेनोवेशन सीजन)';
        } elseif ($isPaints) {
            $hsnCode = '32091000'; // Paints & Varnishes
            $catLabel = 'Paints & Wall Finishes';
            $defaultPrice = 499;
            $defaultMrp = 899;
            $searchVolume = '80,000+ मंथली सर्च';
            $sweetSpot = '₹450 - ₹750 प्रति बाल्टी (4L / 10L)';
            $demandLevel = 'Seasonal High (दीवाली व शादी सीजन)';
        } elseif ($isElectrical) {
            $hsnCode = '85444990'; // Insulated electric wire
            $catLabel = 'Electrical & Wiring';
            $defaultPrice = 299;
            $defaultMrp = 599;
            $searchVolume = '1,10,000+ मंथली सर्च';
            $sweetSpot = '₹250 - ₹450';
            $demandLevel = 'Consistently High';
        } elseif ($isClothing) {
            $hsnCode = '500720';
            $catLabel = 'Apparel & Fashion';
            $defaultPrice = 799;
            $defaultMrp = 1999;
            $searchVolume = '2,50,000+ मंथली सर्च';
            $sweetSpot = '₹599 - ₹999';
            $demandLevel = 'Extremely High on Meesho';
        } else {
            $hsnCode = '84818090';
            $catLabel = 'General Hardware';
            $defaultPrice = 249;
            $defaultMrp = 499;
            $searchVolume = '40,000+ मंथली सर्च';
            $sweetSpot = '₹199 - ₹399';
            $demandLevel = 'Moderate to High';
        }

        $skuPrefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $cleanPrompt), 0, 4));
        $sku = ($skuPrefix ?: 'PROD') . '-' . rand(100, 999) . '-IN';

        // 3. Generate High-Converting Marketplace Title
        $uc = ucwords($cleanPrompt);
        if ($isPtmt) {
            $enhancedTitle = "Heavy Duty Unbreakable {$uc} (15mm / 1/2 Inch) with Wall Flange - Anti-Drip Foam Flow for Bathroom, Kitchen & Wash Basin";
        } elseif ($isPlumbing) {
            $enhancedTitle = "Commercial Grade Heavy Duty {$uc} (High Pressure & Leak Proof) - ASTM Standard Fitting";
        } elseif ($isTiles) {
            $enhancedTitle = "Premium Gloss Finish {$uc} (Stain Resistant & Anti-Skid) - Indoor Floor & Wall Tile";
        } elseif ($isPaints) {
            $enhancedTitle = "High Sheen Washable {$uc} with Anti-Fungal Protection - Interior & Exterior Finish";
        } else {
            $enhancedTitle = "Premium Quality {$uc} - Durable & High Performance";
        }

        // 4. Generate 5-Point Amazon/Meesho Bullet Points & Description
        if ($isPtmt) {
            $description = "✨ **Upgrade your bathroom & kitchen fittings with the heavy-duty unbreakable {$uc}.**\n\n" .
                "Manufactured with 100% virgin high-grade engineering polymers (PTMT), this tap is rust-proof, salt-water resistant, and built to withstand harsh water conditions without leaking.\n\n" .
                "🌟 **Key Highlights & Technical Specifications:**\n" .
                "• **Material & Grade:** 100% Virgin Food-Grade PTMT Polymer (Unbreakable & Corrosion-Free)\n" .
                "• **Flow & Aerator:** German-engineered foam flow aerator saves water while ensuring smooth splash-free stream\n" .
                "• **Cartridge:** Heavy-duty ceramic disc quarter-turn spindle tested for 5,00,000+ on/off cycles\n" .
                "• **Fitting Size:** Standard 15mm (1/2 Inch BSP Thread) suitable for all Indian bathroom & kitchen connections\n" .
                "• **Package Contents:** 1 Unit PTMT Tap + 1 Heavy Matching Wall Flange\n\n" .
                "📦 **Seller Guarantee:** 100% Quality Inspected before dispatch. 3-Year Leakage Warranty support.";
        } elseif ($isPlumbing) {
            $description = "⚡ **Engineered for peak pressure and long-life durability — {$uc}.**\n\n" .
                "Manufactured using certified non-toxic raw material adhering to ASTM D2846 & IS standards. Designed for both hot and cold water plumbing lines.\n\n" .
                "🌟 **Key Highlights:**\n" .
                "• **Zero Corrosion:** Does not scale, pit, or corrode even in hard water\n" .
                "• **Precision Molded:** Smooth internal surface for maximum hydraulic water flow\n" .
                "• **Pressure Rating:** Tested up to 25 bar working pressure\n" .
                "• **Package Contains:** High grade fitting in secure wholesale branded packing\n\n" .
                "📦 Direct factory supply for contractors, plumbers, and home builders.";
        } else {
            $description = "⚡ **Experience superior performance with the all-new {$uc}.**\n\n" .
                "Engineered for daily durability and peak efficiency, offering premium commercial build quality at unbeatable value.\n\n" .
                "🌟 **Key Highlights & Features:**\n" .
                "• **Build & Design:** Ergonomic, lightweight, and modern aesthetic\n" .
                "• **Performance:** High-speed efficiency with seamless compatibility\n" .
                "• **Warranty:** 1-Year Manufacturer Replacement Support\n" .
                "• **Included in Box:** 1 Unit, User Guide, Safety Warranty Card\n\n" .
                "📦 Fast pan-India shipping with secure transit protection.";
        }

        // 5. Generate AI Studio Photography Prompt
        $imagePrompt = $this->generateImagePrompt($cleanPrompt, $catLabel);

        // 6. Generate Search Terms (Backend Keywords)
        $tags = [
            $cleanPrompt,
            'best ' . $cleanPrompt,
            $cleanPrompt . ' price',
            'online buy ' . $cleanPrompt,
            $catLabel,
            'wholesale ' . $cleanPrompt,
            'durable ' . $cleanPrompt
        ];

        return [
            'enhanced_title' => $enhancedTitle,
            'description' => $description,
            'sku' => $sku,
            'hsn_code' => $hsnCode,
            'category_label' => $catLabel,
            'tags' => implode(', ', $tags),
            'suggested_price' => $defaultPrice,
            'suggested_mrp' => $defaultMrp,
            'image_prompt' => $imagePrompt,
            'market_intelligence' => [
                'search_volume' => $searchVolume,
                'sweet_spot_price' => $sweetSpot,
                'demand_level' => $demandLevel,
                'top_competitor_features' => 'Leak-Proof Seal, 3-Year Warranty, Pack of 2 Combos, Heavy Flange Included',
                'buyer_search_queries' => [
                    $cleanPrompt . ' combo pack',
                    'best ' . $cleanPrompt . ' for home',
                    $cleanPrompt . ' 1/2 inch',
                    'water tap plastic unbreakable',
                ]
            ]
        ];
    }

    /**
     * Generate an ultra-realistic commercial studio photography prompt tailored for Image AIs.
     */
    public function generateImagePrompt(string $productName, string $category): string
    {
        $p = ucwords(trim($productName));
        $pLower = strtolower($productName);

        if (preg_match('/ptmt|nal|tap|cock|bib|faucet|sink/i', $pLower)) {
            return "Ultra-realistic commercial product photograph of a modern white {$p} with chrome accent, installed on an elegant dark grey textured tile wall in a luxury contemporary Indian bathroom. Crisp water stream flowing smoothly into a modern wash basin, tiny natural water droplets on the matte surface. Professional studio softbox lighting, soft rim light, clean 45-degree angle advertising shot, 8k resolution, Hasselblad 100mm lens, photorealistic product catalogue quality --ar 1:1 --v 6.0";
        } elseif (preg_match('/pipe|fitting|cpvc|brass|valve/i', $pLower)) {
            return "Professional commercial studio product shot of a high-grade {$p}, isolated on a clean pure white background with soft natural contact drop-shadow. Metallic brass thread gleaming under crisp studio lighting, high precision molding details, sharp focus, 8k commercial catalogue photo for Amazon/Flipkart --ar 1:1";
        } elseif (preg_match('/tile|flooring|slab/i', $pLower)) {
            return "High-end interior architecture photograph of a luxury Indian living room with spacious floor laid out in seamless {$p}, warm ambient lighting reflecting gently off the high-gloss surface, luxury furniture in the background with soft depth of field, architectural digest quality, 8k --ar 16:9";
        } elseif (preg_match('/paint|color|primer/i', $pLower)) {
            return "Commercial advertisement shot of an authentic branded paint bucket of {$p} placed on a clean wooden studio table, with vibrant splash of rich satin color paint arching dynamically behind it. Soft directional studio rim light, premium commercial branding, 8k photorealistic --ar 1:1";
        }

        return "Professional studio product photography of {$p}, placed on a minimalist pedestal with pure white seamless background, soft studio lighting, crisp reflections, commercial e-commerce advertising quality, 8k resolution --ar 1:1";
    }
}
