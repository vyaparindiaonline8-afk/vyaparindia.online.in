<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log; // Assuming Laravel's Log facade for logging

class PaymentService
{
    /**
     * Process a payment for a given order.
     * In a real application, this would integrate with a payment gateway (e.g., Stripe, PayPal).
     *
     * @param Order $order
     * @param array $paymentDetails
     * @return bool True if payment is successful, false otherwise.
     */
    public function processPayment(Order $order, array $paymentDetails): bool
    {
        try {
            // Simulate payment gateway interaction
            // In a real scenario, this would involve API calls to a payment provider
            Log::info("Processing payment for Order ID: {$order->id}", [
                'amount' => $order->total_amount,
                'payment_method' => $paymentDetails['method'] ?? 'unknown',
                'card_last_four' => $paymentDetails['card_last_four'] ?? null,
            ]);

            // Simulate a successful payment
            $isPaymentSuccessful = (bool)rand(0, 1); // 50/50 chance of success for simulation

            if ($isPaymentSuccessful) {
                $order->status = 'processing'; // Update order status
                $order->save();
                Log::info("Payment successful for Order ID: {$order->id}. Transaction ID: " . uniqid('txn_'));
                return true;
            } else {
                Log::warning("Payment failed for Order ID: {$order->id}.");
                // Potentially update order status to 'failed' or 'pending_payment'
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Error processing payment for Order ID: {$order->id}. Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Process a refund for an order.
     *
     * @param Order $order
     * @return bool True if refund is successful, false otherwise.
     */
    public function refundPayment(Order $order): bool
    {
        if ($order->status !== 'processing' && $order->status !== 'completed') {
            Log::warning("Cannot refund order ID: {$order->id}. Order is not in a refundable state.");
            return false;
        }

        try {
            // Simulate refund gateway interaction
            Log::info("Processing refund for Order ID: {$order->id}", ['amount' => $order->total_amount]);

            // Simulate a successful refund
            $isRefundSuccessful = (bool)rand(0, 1);

            if ($isRefundSuccessful) {
                $order->status = 'refunded'; // Update order status
                $order->save();
                Log::info("Refund successful for Order ID: {$order->id}.");
                return true;
            } else {
                Log::warning("Refund failed for Order ID: {$order->id}.");
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Error processing refund for Order ID: {$order->id}. Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the status of a payment by its transaction ID.
     * In a real application, this would query the payment gateway.
     *
     * @param string $transactionId
     * @return string Current payment status (e.g., 'completed', 'pending', 'failed', 'refunded').
     */
    public function getPaymentStatus(string $transactionId): string
    {
        // Simulate checking status from a payment gateway
        Log::info("Checking payment status for Transaction ID: {$transactionId}");

        $statuses = ['completed', 'pending', 'failed', 'refunded'];
        return $statuses[array_rand($statuses)]; // Return a random status for simulation
    }
}
