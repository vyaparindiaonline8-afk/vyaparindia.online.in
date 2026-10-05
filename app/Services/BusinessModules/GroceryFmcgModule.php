<?php

namespace App\Services\BusinessModules;

use App\Models\Product;
use App\Models\SellerPage;

class GroceryFmcgModule implements BusinessModuleInterface
{
    public function getId(): string
    {
        return 'grocery_fmcg';
    }

    public function getName(): string
    {
        return 'किराना & जनरल स्टोर';
    }

    public function getSubtitle(): string
    {
        return 'क्विक ग्रोसरी थीम (100g, 250g, 500g, 1kg, 5kg ड्रॉपडाउन, फ़ास्ट ऐड-टू-बैग)';
    }

    public function getIcon(): string
    {
        return 'fa-solid fa-basket-shopping';
    }

    public function getThemeBadge(): string
    {
        return 'Quick Grocery & Supermarket';
    }

    public function getCardComponent(): string
    {
        return 'seller-site.modules.card_grocery_fmcg';
    }

    public function getAttributeSchema(): array
    {
        return [
            ['key' => 'pack_sizes', 'label' => 'Packaging Weights', 'type' => 'text', 'placeholder' => '100g, 250g, 500g, 1kg, 5kg'],
            ['key' => 'shelf_life', 'label' => 'Shelf Life / Expiry', 'type' => 'text', 'placeholder' => 'Best before 6 months'],
            ['key' => 'fssai_number', 'label' => 'FSSAI License No.', 'type' => 'text', 'placeholder' => '100XXXXXXXXXX'],
        ];
    }

    public function formatWhatsappOrder(Product $product, SellerPage $sellerPage, array $state): string
    {
        $prodUrl = url("/{$sellerPage->slug}/product/{$product->slug}");
        $varTxt = !empty($state['variant_name']) ? " ({$state['variant_name']})" : '';

        return "*Namaste! Grocery Ration Order from {$sellerPage->page_title}*\n\n" .
               "🛒 *Item:* {$product->name}{$varTxt}\n" .
               "🔢 *Packs:* {$state['quantity']}\n" .
               "💰 *Rate:* ₹" . number_format($state['unitPrice'], 2) . "\n" .
               "💵 *Item Total:* ₹" . number_format($state['total'], 2) . "\n" .
               "🔗 *Item Link:* {$prodUrl}\n\n" .
               "Kripya home delivery slot confirm karein.";
    }
}
