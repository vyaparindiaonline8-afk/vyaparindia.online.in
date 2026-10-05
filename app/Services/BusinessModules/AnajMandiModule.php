<?php

namespace App\Services\BusinessModules;

use App\Models\Product;
use App\Models\SellerPage;

class AnajMandiModule implements BusinessModuleInterface
{
    public function getId(): string
    {
        return 'anaj_mandi';
    }

    public function getName(): string
    {
        return 'अनाज मंडी & थोक राशन';
    }

    public function getSubtitle(): string
    {
        return 'होलसेल कमोडिटी थीम (दैनिक मंडी भाव टिकर, प्रति क्विंटल / बोरी / कट्टा रेट्स)';
    }

    public function getIcon(): string
    {
        return 'fa-solid fa-wheat-awn';
    }

    public function getThemeBadge(): string
    {
        return 'Mandi Bhav & Bulk Trading';
    }

    public function getCardComponent(): string
    {
        return 'seller-site.modules.card_anaj_mandi';
    }

    public function getAttributeSchema(): array
    {
        return [
            ['key' => 'mandi_unit', 'label' => 'Trading Unit', 'type' => 'select', 'options' => ['quintal' => 'प्रति क्विंटल (100 Kg)', 'bori_50' => 'प्रति बोरी (50 Kg)', 'katta_30' => 'प्रति कट्टा (30 Kg)', 'kg' => 'प्रति किलो']],
            ['key' => 'mandi_location', 'label' => 'Mandi Yard / Location', 'type' => 'text', 'placeholder' => 'e.g. Krishi Upaj Mandi Yard 2'],
            ['key' => 'min_order_qty', 'label' => 'Minimum Order Lot', 'type' => 'number', 'placeholder' => 'e.g. 5 क्विंटल / 10 बोरी'],
            ['key' => 'moisture_percent', 'label' => 'Moisture / Quality Grade', 'type' => 'text', 'placeholder' => 'Moisture < 12%, Grade A+ Sharbati'],
        ];
    }

    public function formatWhatsappOrder(Product $product, SellerPage $sellerPage, array $state): string
    {
        $prodUrl = url("/{$sellerPage->slug}/product/{$product->slug}");
        $varTxt = !empty($state['variant_name']) ? " [{$state['variant_name']}]" : '';

        return "*Namaste! Mandi Sauda / Bulk Order Inquiry from {$sellerPage->page_title}*\n\n" .
               "🌾 *Commodity / Jins:* {$product->name}{$varTxt}\n" .
               "🔢 *Loot / Quantity:* {$state['quantity']}\n" .
               "💰 *Current Sauda Bhav:* ₹" . number_format($state['unitPrice'], 2) . "\n" .
               "💵 *Total Sauda Value:* ₹" . number_format($state['total'], 2) . "\n" .
               "🔗 *Live Bhav Link:* {$prodUrl}\n\n" .
               "Kripya mandi gate pass, loading point aur payment terms confirm karein.";
    }
}
