<?php

namespace App\Services\BusinessModules;

use App\Models\Product;
use App\Models\SellerPage;

class FashionLifestyleModule implements BusinessModuleInterface
{
    public function getId(): string
    {
        return 'fashion_lifestyle';
    }

    public function getName(): string
    {
        return 'फैशन & क्लोदिंग';
    }

    public function getSubtitle(): string
    {
        return 'इंस्टाग्राम / कैटलॉग थीम (बड़ी वर्टिकल फोटो, साइज S/M/L/XL & कलर चिप्स)';
    }

    public function getIcon(): string
    {
        return 'fa-solid fa-shirt';
    }

    public function getThemeBadge(): string
    {
        return 'Trendy Lookbook / Boutique';
    }

    public function getCardComponent(): string
    {
        return 'seller-site.modules.card_fashion_lifestyle';
    }

    public function getAttributeSchema(): array
    {
        return [
            ['key' => 'sizes', 'label' => 'Available Sizes', 'type' => 'text', 'placeholder' => 'S, M, L, XL, XXL'],
            ['key' => 'colors', 'label' => 'Color Options', 'type' => 'text', 'placeholder' => 'Navy Blue, Maroon, Olive Green'],
            ['key' => 'fabric', 'label' => 'Fabric / Material', 'type' => 'text', 'placeholder' => '100% Pure Cotton, Chanderi Silk'],
            ['key' => 'fit_type', 'label' => 'Fit Type', 'type' => 'text', 'placeholder' => 'Regular Fit, Slim Fit'],
        ];
    }

    public function formatWhatsappOrder(Product $product, SellerPage $sellerPage, array $state): string
    {
        $prodUrl = url("/{$sellerPage->slug}/product/{$product->slug}");
        $varTxt = !empty($state['variant_name']) ? " (Size: {$state['variant_name']})" : '';

        return "*Namaste! Fashion Order Inquiry from {$sellerPage->page_title}*\n\n" .
               "👗 *Outfit / Item:* {$product->name}{$varTxt}\n" .
               (!empty($product->brand) ? "✨ *Label:* {$product->brand}\n" : '') .
               "🔢 *Quantity:* {$state['quantity']} pcs\n" .
               "💰 *Price:* ₹" . number_format($state['unitPrice'], 2) . "\n" .
               "💵 *Total:* ₹" . number_format($state['total'], 2) . "\n" .
               "🔗 *Lookbook Link:* {$prodUrl}\n\n" .
               "Kripya available size aur dispatch time confirm karein.";
    }
}
