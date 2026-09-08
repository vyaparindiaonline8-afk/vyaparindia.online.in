<?php

namespace App\Services;

use App\Models\AbandonedCart;
use App\Models\Order;
use Illuminate\Support\Str;

class CartRecoveryService
{
    /**
     * Capture an abandoned cart when customer enters details on checkout.
     */
    public function captureCart(int $sellerId, array $customerData, array $items, float $totalAmount): AbandonedCart
    {
        $token = Str::random(32);
        $discountPercent = 10.00;
        $discountAmount = round(($totalAmount * $discountPercent) / 100, 2);

        return AbandonedCart::create([
            'seller_id' => $sellerId,
            'customer_name' => $customerData['name'] ?? 'Shopper',
            'customer_phone' => $customerData['phone'],
            'customer_email' => $customerData['email'] ?? null,
            'cart_items' => $items,
            'total_amount' => $totalAmount,
            'recovery_token' => $token,
            'recovery_discount_percent' => $discountPercent,
            'recovery_discount_amount' => $discountAmount,
            'recovery_status' => 'pending',
            'expires_at' => now()->addDays(2),
        ]);
    }

    /**
     * Generate prefilled WhatsApp Recovery URL with 10% Discount Offer.
     */
    public function getWhatsAppRecoveryPayload(AbandonedCart $cart): array
    {
        $discountedTotal = max(0, $cart->total_amount - $cart->recovery_discount_amount);
        $recoveryUrl = url('/recover-cart/' . $cart->recovery_token);

        $text = "👋 *Hi {$cart->customer_name}!* 🛍️\n\n" .
            "You left items in your shopping bag worth *₹{$cart->total_amount}*.\n\n" .
            "🎁 *Special Recovery Offer:* Complete your order now & get *Flat 10% OFF (-₹{$cart->recovery_discount_amount})*!\n\n" .
            "👉 *Claim Discount & Checkout Now:* {$recoveryUrl}\n\n" .
            "⚡ *Net Price to Pay:* *₹{$discountedTotal}* (Free Fast Shipping included!)";

        $encodedText = urlencode($text);
        $cleanPhone = preg_replace('/[^0-9]/', '', $cart->customer_phone);
        if (strlen($cleanPhone) === 10) {
            $cleanPhone = '91' . $cleanPhone;
        }

        return [
            'cart' => $cart,
            'whatsapp_url' => "https://wa.me/{$cleanPhone}?text={$encodedText}",
            'message_text' => $text,
            'discounted_total' => $discountedTotal,
            'recovery_url' => $recoveryUrl,
        ];
    }

    /**
     * Mark cart as recovered when customer completes checkout.
     */
    public function markAsRecovered(AbandonedCart $cart, Order $order): void
    {
        $cart->update([
            'recovery_status' => 'recovered',
            'recovered_at' => now(),
        ]);
    }
}