<?php

namespace App\Services\BusinessModules;

use App\Models\Product;
use App\Models\SellerPage;

class HardwarePipesModule implements BusinessModuleInterface
{
    public function getId(): string
    {
        return 'hardware_pipes';
    }

    public function getName(): string
    {
        return 'हार्डवेयर, सेनेटरी, पाइप्स';
    }

    public function getSubtitle(): string
    {
        return 'इंजीनियरिंग & टैबुलर ग्रिड थीम (रेट लिस्ट, मल्टी-साइज mm/inch सेलेक्टर)';
    }

    public function getIcon(): string
    {
        return 'fa-solid fa-wrench';
    }

    public function getThemeBadge(): string
    {
        return 'Engineering / B2B Tabular';
    }

    public function getCardComponent(): string
    {
        return 'seller-site.modules.card_hardware_pipes';
    }

    public function getAttributeSchema(): array
    {
        return [
            ['key' => 'pressure_class', 'label' => 'Pressure Class (kgf/cm²)', 'type' => 'text', 'placeholder' => 'e.g. Class 1 (2.5 kgf), Class 2 (4 kgf)'],
            ['key' => 'size_range', 'label' => 'Standard Size (mm / inch)', 'type' => 'text', 'placeholder' => 'e.g. 20mm (1/2"), 25mm (3/4")'],
            ['key' => 'box_qty', 'label' => 'Box Packing Qty', 'type' => 'number', 'placeholder' => 'e.g. 50, 100 pcs'],
            ['key' => 'hsn_code', 'label' => 'HSN Code', 'type' => 'text', 'placeholder' => '39174000'],
        ];
    }

    public function formatWhatsappOrder(Product $product, SellerPage $sellerPage, array $state): string
    {
        $prodUrl = url("/{$sellerPage->slug}/product/{$product->slug}");
        $varTxt = !empty($state['variant_name']) ? " ({$state['variant_name']})" : '';

        return "*Namaste! New Hardware & Plumbing Order Inquiry from {$sellerPage->page_title}*\n\n" .
               "🛠️ *Item:* {$product->name}{$varTxt}\n" .
               (!empty($product->brand) ? "🏢 *Brand:* {$product->brand}\n" : '') .
               (!empty($product->group_name) ? "📂 *Group:* {$product->group_name}\n" : '') .
               "🔢 *Quantity:* {$state['quantity']}\n" .
               "💰 *Unit Rate:* ₹" . number_format($state['unitPrice'], 2) . "\n" .
               "💵 *Total Estimate:* ₹" . number_format($state['total'], 2) . "\n" .
               "🔗 *Item Link:* {$prodUrl}\n\n" .
               "Kripya delivery schedule aur freight confirm karein.";
    }
}
