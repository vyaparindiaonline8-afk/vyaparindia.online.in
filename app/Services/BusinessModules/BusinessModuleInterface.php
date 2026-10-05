<?php

namespace App\Services\BusinessModules;

use App\Models\Product;
use App\Models\SellerPage;

interface BusinessModuleInterface
{
    /**
     * Unique module identifier (e.g. 'hardware_pipes', 'fashion_lifestyle')
     */
    public function getId(): string;

    /**
     * Human-readable name (Hindi/English)
     */
    public function getName(): string;

    /**
     * Short subtitle or industry description
     */
    public function getSubtitle(): string;

    /**
     * FontAwesome icon class
     */
    public function getIcon(): string;

    /**
     * Theme style badge
     */
    public function getThemeBadge(): string;

    /**
     * Theme card blade view path
     */
    public function getCardComponent(): string;

    /**
     * Dynamic attributes / schema variations
     */
    public function getAttributeSchema(): array;

    /**
     * Pre-formatted WhatsApp order text customized for this industry
     */
    public function formatWhatsappOrder(Product $product, SellerPage $sellerPage, array $state): string;
}
