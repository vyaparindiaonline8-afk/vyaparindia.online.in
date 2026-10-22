<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Channel;
use App\Models\ChannelListing;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Log;

class ChannelWebhookController extends Controller
{
    /**
     * Inbound Multi-Channel Webhook Receiver
     * Accepts order events from Amazon, Meesho, Flipkart, Shopify, WooCommerce
     * Automatically syncs and minuses stock across all listings in 2-way real time
     */
    public function handleWebhook(Request $request, string $channel)
    {
        $channel = strtolower($channel);
        $payload = $request->all();

        Log::info("Inbound Webhook received for channel: {$channel}", ['payload' => $payload]);

        $event = $payload['event'] ?? $payload['event_type'] ?? $payload['type'] ?? 'order.created';
        $externalOrderId = $payload['order_id'] ?? $payload['id'] ?? $payload['order_number'] ?? ('EXT-' . strtoupper($channel) . '-' . time());

        // 1. ORDER CANCELLATION / RETURN EVENT
        if (in_array($event, ['order.cancelled', 'order.canceled', 'order.returned', 'ORDER_CANCELLED'])) {
            $existingOrder = Order::where('order_number', $externalOrderId)
                ->where('order_source', $channel)
                ->first();

            if ($existingOrder && $existingOrder->status !== 'cancelled') {
                $existingOrder->update(['status' => 'cancelled']);

                foreach ($existingOrder->products as $prod) {
                    $qty = $prod->pivot->quantity ?? 1;
                    $prod->addStock($qty, "Restored due to {$channel} Order #{$externalOrderId} cancellation", 'order_cancel', $existingOrder->id);
                }

                return response()->json([
                    'success' => true,
                    'message' => "Order #{$externalOrderId} cancelled and inventory restored across all channels.",
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Order not found or already cancelled.",
            ]);
        }

        // 2. ORDER CREATED / PLACED EVENT
        $customerName = $payload['customer_name'] ?? $payload['buyer_name'] ?? ($payload['shipping_address']['name'] ?? 'Marketplace Buyer');
        $customerPhone = $payload['customer_phone'] ?? ($payload['shipping_address']['phone'] ?? '');
        $customerEmail = $payload['customer_email'] ?? null;
        $shippingAddress = is_array($payload['shipping_address'] ?? null)
            ? implode(', ', array_filter([
                $payload['shipping_address']['address_line1'] ?? '',
                $payload['shipping_address']['address_line2'] ?? '',
                $payload['shipping_address']['city'] ?? '',
                $payload['shipping_address']['state'] ?? '',
                $payload['shipping_address']['pincode'] ?? ''
              ]))
            : ($payload['shipping_address'] ?? 'Channel Provided Address');

        $city = $payload['city'] ?? ($payload['shipping_address']['city'] ?? 'Delhi');
        $state = $payload['state'] ?? ($payload['shipping_address']['state'] ?? 'Delhi');
        $pincode = $payload['pincode'] ?? ($payload['shipping_address']['pincode'] ?? '110001');
        $paymentMethod = strtolower($payload['payment_method'] ?? 'online');
        $totalPrice = floatval($payload['total_price'] ?? $payload['amount'] ?? 0);

        // Process items in the order
        $rawItems = $payload['items'] ?? $payload['line_items'] ?? $payload['products'] ?? [];
        if (empty($rawItems) && isset($payload['sku'])) {
            $rawItems = [[
                'sku' => $payload['sku'],
                'quantity' => $payload['quantity'] ?? 1,
                'price' => $totalPrice,
            ]];
        }

        $itemsProcessed = [];
        $sellerId = null;

        foreach ($rawItems as $item) {
            $sku = $item['sku'] ?? null;
            $extId = $item['external_product_id'] ?? null;
            $qty = intval($item['quantity'] ?? 1);
            $itemPrice = floatval($item['price'] ?? 0);

            // Match product by SKU or ChannelListing external_product_id
            $product = null;
            if ($sku) {
                $product = Product::where('sku', $sku)->first();
            }
            if (!$product && $extId) {
                $listing = ChannelListing::where('external_product_id', $extId)->first();
                if ($listing) {
                    $product = $listing->product;
                }
            }

            // Fallback: match by product ID if passed
            if (!$product && isset($item['product_id'])) {
                $product = Product::find($item['product_id']);
            }

            if ($product) {
                if (!$sellerId) {
                    $sellerId = $product->user_id;
                }
                $itemsProcessed[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'price' => $itemPrice > 0 ? $itemPrice : $product->price,
                ];
            }
        }

        if (empty($itemsProcessed)) {
            // Find a seller associated with the channel
            $channelModel = Channel::where('channel_name', $channel)->first();
            $sellerId = $channelModel ? $channelModel->user_id : (Product::first()->user_id ?? 1);
        }

        // Calculate total price if missing
        if ($totalPrice <= 0 && !empty($itemsProcessed)) {
            foreach ($itemsProcessed as $it) {
                $totalPrice += ($it['price'] * $it['quantity']);
            }
        }

        // Create the Inbound Order
        $order = Order::create([
            'order_number' => $externalOrderId,
            'order_source' => $channel,
            'seller_id' => $sellerId,
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone,
            'customer_email' => $customerEmail,
            'shipping_address' => $shippingAddress,
            'city' => $city,
            'state' => $state,
            'pincode' => $pincode,
            'payment_method' => ($paymentMethod === 'cod') ? 'cod' : 'online',
            'payment_status' => ($paymentMethod === 'cod') ? 'unpaid' : 'paid',
            'total_price' => $totalPrice,
            'status' => 'processing',
            'notes' => "Real-Time Inbound Webhook Order from {$channel}. Multi-channel stock auto-deducted.",
        ]);

        // Attach products and auto-deduct stock
        foreach ($itemsProcessed as $it) {
            $p = $it['product'];
            $order->products()->attach($p->id, [
                'quantity' => $it['quantity'],
                'price' => $it['price'],
            ]);

            // Auto-deduct stock and auto-sync other channels
            $p->deductStock($it['quantity'], "Auto-deducted due to inbound {$channel} Order #{$externalOrderId}", 'channel_order', $order->id);
        }

        return response()->json([
            'success' => true,
            'message' => "Order #{$externalOrderId} received from {$channel}. Stock deducted and synchronized across all active channels.",
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'channel' => $channel,
            'items_count' => count($itemsProcessed),
        ]);
    }

    /**
     * Live Interactive Order Simulation for Sellers
     * Allows sellers to test 2-way real-time auto-stock subtraction from the dashboard
     */
    public function simulateOrder(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'channel' => 'required|in:amazon,meesho,flipkart,shopify,woocommerce',
            'quantity' => 'nullable|integer|min:1|max:10',
        ]);

        $product = Product::findOrFail($request->product_id);
        $qty = intval($request->quantity ?: 1);
        $channel = $request->channel;
        $orderId = strtoupper($channel) . '-' . rand(100000, 999999);

        // Fake payload simulating external webhook
        $payload = [
            'event' => 'order.created',
            'order_id' => $orderId,
            'customer_name' => 'Demo Buyer (' . ucfirst($channel) . ')',
            'customer_phone' => '9876543210',
            'shipping_address' => 'Shop 42, Wholesale Market, Connaught Place, New Delhi',
            'city' => 'New Delhi',
            'state' => 'Delhi',
            'pincode' => '110001',
            'payment_method' => 'cod',
            'total_price' => $product->price * $qty,
            'items' => [
                [
                    'sku' => $product->sku,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $product->price,
                ]
            ],
        ];

        $simRequest = new Request($payload);
        $result = $this->handleWebhook($simRequest, $channel);

        return redirect()->route('seller.orders.index', ['channel' => $channel])
            ->with('success', "🎉 Simulated {$channel} Order #{$orderId} received! Stock automatically decreased by {$qty} unit(s) and synchronized across all platforms.");
    }
}
