<?php

namespace App\Services\BusinessModules;

use App\Models\Product;
use App\Models\SellerPage;

class IronSteelCementModule implements BusinessModuleInterface
{
    public function getId(): string
    {
        return 'iron_cement';
    }

    public function getName(): string
    {
        return 'लोहा, सरिया (TMT) & सीमेंट व्यापार';
    }

    public function getSubtitle(): string
    {
        return 'बिल्डिंग मटेरियल व सरिया-सीमेंट थीम (mm वजन प्रति रोड/टन कैलकुलेटर, 50Kg बोरी & मिस्त्री/ठेकेदार रेट)';
    }

    public function getIcon(): string
    {
        return 'fa-solid fa-trowel-bricks';
    }

    public function getThemeBadge(): string
    {
        return 'TMT Steel & Cement Live Rate';
    }

    public function getCardComponent(): string
    {
        return 'seller-site.modules.card_iron_cement';
    }

    public function getAttributeSchema(): array
    {
        return [
            ['key' => 'material_type', 'label' => 'Material Type', 'type' => 'select', 'options' => ['tmt_bar' => 'सरिया (TMT Steel Bar)', 'cement_bag' => 'सीमेंट बोरी (50 Kg Bag)', 'angle_channel' => 'एंगल / चैनल / गार्डर', 'sand_aggregate' => 'रेत / गिट्टी / डस्ट']],
            ['key' => 'rod_weight_kg', 'label' => 'Weight Per Piece / Bag (Kg)', 'type' => 'number', 'placeholder' => 'e.g. 10.66 (12mm TMT) or 50 (Cement Bag)'],
            ['key' => 'steel_cement_grade', 'label' => 'Grade / Standard', 'type' => 'text', 'placeholder' => 'e.g. Fe-550D / OPC 53 / PPC'],
            ['key' => 'loading_hamali', 'label' => 'Loading / Hamali Per Unit (₹)', 'type' => 'number', 'placeholder' => 'e.g. 5'],
        ];
    }

    public function formatWhatsappOrder(Product $product, SellerPage $sellerPage, array $state): string
    {
        $prodUrl = url("/{$sellerPage->slug}/product/{$product->slug}");
        $varTxt = !empty($state['variant_name']) ? " [{$state['variant_name']}]" : '';

        return "*Namaste! Iron / TMT / Cement Order Inquiry from {$sellerPage->page_title}*\n\n" .
               "🏗️ *Material:* {$product->name}{$varTxt}\n" .
               (!empty($product->brand) ? "🏭 *Brand:* {$product->brand}\n" : '') .
               "🔢 *Qty (Pcs / Bags / Ton):* {$state['quantity']}\n" .
               "💰 *Live Rate:* ₹" . number_format($state['unitPrice'], 2) . "\n" .
               "💵 *Estimated Bill:* ₹" . number_format($state['total'], 2) . "\n" .
               "🔗 *Live Rate Link:* {$prodUrl}\n\n" .
               "Kripya aaj ka stock, unloading site distance aur gadi/tempo bhada confirm karein.";
    }
}
