<?php

namespace App\Services;

use App\Models\Order;
use App\Models\DropshipOrder;
use Illuminate\Support\Str;

class WhatsAppVerificationService
{
    /**
     * Generate anti-RTO verification payload & 4-6 hour deadline.
     */
    public function setupOrderVerification(Order $order, int $timeoutHours = 6): array
    {
        $token = Str::random(32);
        $deadline = now()->addHours($timeoutHours);
        $discountAmount = min(50.00, round($order->total_price * 0.05, 2)); // ₹50 or 5% discount

        $order->update([
            'cod_verification_token' => $token,
            'cod_verification_status' => 'unverified',
            'verification_deadline_at' => $deadline,
            'cod_to_prepaid_discount' => $discountAmount,
            'original_cod_total' => $order->total_price,
            'rto_risk_score' => $order->total_price > 3000 ? 'medium' : 'low',
        ]);

        $verifyUrl = route('verification.show', $token);

        $whatsappText = "👋 *Hi {$order->customer_name}!*\n\n" .
            "Thank you for placing Order *#{$order->order_number}* worth *₹{$order->total_price}* (COD).\n\n" .
            "⚡ *Special Instant Discount:*\n" .
            "Pay online now & get *₹{$discountAmount} Instant Discount* + Zero COD fee!\n\n" .
            "👉 *Confirm or Pay Online Here:* {$verifyUrl}\n\n" .
            "⏰ *Note:* Please confirm within {$timeoutHours} hours to ensure immediate dispatch.";

        return [
            'token' => $token,
            'verify_url' => $verifyUrl,
            'deadline' => $deadline,
            'discount' => $discountAmount,
            'whatsapp_message' => $whatsappText,
        ];
    }

    /**
     * Convert COD order to Prepaid with Instant Discount applied in Real Time!
     */
    public function convertOrderToPrepaid(Order $order): array
    {
        if ($order->cod_verification_status === 'converted_to_prepaid') {
            return ['success' => true, 'already_converted' => true];
        }

        $discount = $order->cod_to_prepaid_discount ?: 50.00;
        $newTotal = max(0, $order->total_price - $discount);

        $order->update([
            'payment_method' => 'online',
            'payment_status' => 'paid',
            'cod_verification_status' => 'converted_to_prepaid',
            'total_price' => $newTotal,
            'rto_risk_score' => 'low', // Zero RTO risk for prepaid!
        ]);

        // Real-time update in associated DropshipOrder
        $dsOrder = DropshipOrder::where('order_id', $order->id)->first();
        if ($dsOrder) {
            $dsOrder->update([
                'payment_collection_mode' => 'prepaid',
                'cod_remittance_status' => 'remitted_to_dropshipper',
                'customer_retail_total' => $newTotal,
            ]);
        }

        return [
            'success' => true,
            'order' => $order,
            'discount_applied' => $discount,
            'new_total' => $newTotal,
        ];
    }

    /**
     * Confirm COD Order without discount
     */
    public function confirmCodOrder(Order $order): array
    {
        $order->update([
            'cod_verification_status' => 'verified',
            'rto_risk_score' => 'low',
        ]);

        $dsOrder = DropshipOrder::where('order_id', $order->id)->first();
        if ($dsOrder && $dsOrder->fulfillment_status === 'pending_wholesaler_review') {
            $dsOrder->update(['fulfillment_status' => 'ready_to_pack']);
        }

        return ['success' => true, 'order' => $order];
    }
}