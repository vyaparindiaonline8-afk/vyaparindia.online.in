<?php

namespace App\Services\BusinessModules;

use App\Models\Product;
use App\Models\SellerPage;

class FoodDiningModule implements BusinessModuleInterface
{
    public function getId(): string
    {
        return 'food_dining';
    }

    public function getName(): string
    {
        return 'रेस्टोरेंट & क्लाउड किचन';
    }

    public function getSubtitle(): string
    {
        return 'फ़ूड मेनू थीम (🟢 वेज / 🔴 नॉन-वेज मार्कर, स्पाइस रेटिंग, आज का स्पेशल)';
    }

    public function getIcon(): string
    {
        return 'fa-solid fa-utensils';
    }

    public function getThemeBadge(): string
    {
        return 'Food Menu & Fast Delivery';
    }

    public function getCardComponent(): string
    {
        return 'seller-site.modules.card_food_dining';
    }

    public function getAttributeSchema(): array
    {
        return [
            ['key' => 'food_type', 'label' => 'Diet Type', 'type' => 'select', 'options' => ['veg' => '🟢 Pure Veg', 'non_veg' => '🔴 Non-Veg', 'egg' => '🟡 Contains Egg']],
            ['key' => 'spice_level', 'label' => 'Spice Level', 'type' => 'select', 'options' => ['mild' => '🌶️ Mild', 'medium' => '🌶️🌶️ Medium Spicy', 'spicy' => '🌶️🌶️🌶️ Extra Spicy']],
            ['key' => 'prep_time', 'label' => 'Preparation Time (mins)', 'type' => 'number', 'placeholder' => '15 - 20 mins'],
            ['key' => 'portion_size', 'label' => 'Portion Size', 'type' => 'text', 'placeholder' => 'Serves 1-2 Persons (500ml)'],
        ];
    }

    public function formatWhatsappOrder(Product $product, SellerPage $sellerPage, array $state): string
    {
        $prodUrl = url("/{$sellerPage->slug}/product/{$product->slug}");
        $varTxt = !empty($state['variant_name']) ? " ({$state['variant_name']})" : '';

        return "*Namaste! Fresh Food Order from {$sellerPage->page_title}*\n\n" .
               "🍽️ *Dish:* {$product->name}{$varTxt}\n" .
               "🔢 *Portions:* {$state['quantity']}\n" .
               "💰 *Rate:* ₹" . number_format($state['unitPrice'], 2) . "\n" .
               "💵 *Bill Amount:* ₹" . number_format($state['total'], 2) . "\n" .
               "🔗 *Menu Link:* {$prodUrl}\n\n" .
               "Kripya delivery address / table number aur preparation time confirm karein.";
    }
}
