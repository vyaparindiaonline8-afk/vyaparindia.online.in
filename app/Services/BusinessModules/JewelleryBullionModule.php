<?php

namespace App\Services\BusinessModules;

use App\Models\Product;
use App\Models\SellerPage;

class JewelleryBullionModule implements BusinessModuleInterface
{
    public function getId(): string
    {
        return 'jewellery_bullion';
    }

    public function getName(): string
    {
        return 'ज्वेलरी, सोना-चांदी & हॉलमार्क';
    }

    public function getSubtitle(): string
    {
        return 'सराफा व बुलियन थीम (24K/22K/18K गोल्ड, 999 सिल्वर, नेट वजन gm, मेकिंग चार्ज + 3% GST)';
    }

    public function getIcon(): string
    {
        return 'fa-solid fa-gem';
    }

    public function getThemeBadge(): string
    {
        return 'Bullion Live Rate & Hallmark';
    }

    public function getCardComponent(): string
    {
        return 'seller-site.modules.card_jewellery';
    }

    public function getAttributeSchema(): array
    {
        return [
            ['key' => 'metal_purity', 'label' => 'Metal & Purity', 'type' => 'select', 'options' => ['gold_24k' => '24K Pure Gold (999)', 'gold_22k' => '22K Hallmark Gold (916)', 'gold_18k' => '18K Gold (750)', 'silver_999' => '999 Pure Silver', 'silver_925' => '925 Sterling Silver']],
            ['key' => 'net_weight_gm', 'label' => 'Net Weight (Grams)', 'type' => 'number', 'placeholder' => 'e.g. 8.50'],
            ['key' => 'making_charge_type', 'label' => 'Making Charge Type', 'type' => 'select', 'options' => ['percent' => 'प्रतिशत (%)', 'per_gm' => 'प्रति ग्राम (₹/gm)', 'flat' => 'फिक्स रकम (₹)']],
            ['key' => 'making_charge_val', 'label' => 'Making Charge Value', 'type' => 'number', 'placeholder' => 'e.g. 12 (% or ₹)'],
            ['key' => 'hallmark_huid', 'label' => 'Hallmark / HUID Code', 'type' => 'text', 'placeholder' => 'e.g. BIS HUID 916 Verified'],
        ];
    }

    public function formatWhatsappOrder(Product $product, SellerPage $sellerPage, array $state): string
    {
        $prodUrl = url("/{$sellerPage->slug}/product/{$product->slug}");
        $varTxt = !empty($state['variant_name']) ? " [{$state['variant_name']}]" : '';

        return "*Namaste! Jewellery Rate & Booking Inquiry from {$sellerPage->page_title}*\n\n" .
               "💎 *Design:* {$product->name}{$varTxt}\n" .
               "🔢 *Quantity:* {$state['quantity']}\n" .
               "💰 *Live Bullion Quote (Incl. Making & 3% GST):* ₹" . number_format($state['unitPrice'], 2) . "\n" .
               "💵 *Total Value:* ₹" . number_format($state['total'], 2) . "\n" .
               "🔗 *Design Link:* {$prodUrl}\n\n" .
               "Kripya HUID certification, availability aur aaj ka booking rate confirm karein.";
    }
}
