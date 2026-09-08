<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create a new order.
     *
     * @param User $user
     * @param array $productIdsWithQuantities Associative array: [product_id => quantity]
     * @param array $shippingDetails
     * @return Order
     * @throws \Exception
     */
    public function createOrder(User $user, array $productIdsWithQuantities, array $shippingDetails): Order
    {
        return DB::transaction(function () use ($user, $productIdsWithQuantities, $shippingDetails) {
            $order = $user->orders()->create([
                'status' => 'pending', // Initial status
                'shipping_address' => $shippingDetails['address'],
                'shipping_city' => $shippingDetails['city'],
                'shipping_state' => $shippingDetails['state'],
                'shipping_zip' => $shippingDetails['zip'],
                'total_amount' => 0, // Will be calculated after adding products
            ]);

            $totalAmount = 0;
            foreach ($productIdsWithQuantities as $productId => $quantity) {
                // Assuming Product model exists and has a 'price' attribute
                $product = \App\Models\Product::find($productId);

                if (!$product) {
                    throw new \Exception("Product with ID {$productId} not found.");
                }

                $order->products()->attach($productId, ['quantity' => $quantity, 'price' => $product->price]);
                $totalAmount += ($product->price * $quantity);
            }

            $order->total_amount = $totalAmount;
            $order->save();

            return $order;
        });
    }

    /**
     * Update the status of an order.
     *
     * @param Order $order
     * @param string $status
     * @return bool
     */
    public function updateOrderStatus(Order $order, string $status): bool
    {
        $order->status = $status;
        return $order->save();
    }

    /**
     * Get a specific order by its ID.
     *
     * @param int $id
     * @return Order|null
     */
    public function getOrderById(int $id): ?Order
    {
        return Order::with('products')->find($id);
    }

    /**
     * Get all orders placed by a user.
     *
     * @param User $user
     * @return Collection<int, Order>
     */
    public function getOrdersByUser(User $user): Collection
    {
        return $user->orders()->with('products')->get();
    }

    /**
     * Cancel an order.
     *
     * @param Order $order
     * @return bool
     */
    public function cancelOrder(Order $order): bool
    {
        // Add any specific cancellation logic here (e.g., restock items, refund)
        $order->status = 'cancelled';
        return $order->save();
    }
}
