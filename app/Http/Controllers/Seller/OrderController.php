<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $sellerId = Auth::id();
        $orders = Order::where('seller_id', $sellerId)
            ->orWhereHas('products', function ($query) use ($sellerId) {
                $query->where('user_id', $sellerId);
            })
            ->with(['buyer', 'products'])
            ->latest()
            ->paginate(15);

        return view('seller.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $sellerId = Auth::id();
        $orderContainsSellerProduct = $order->products()->where('user_id', $sellerId)->exists();

        if (!$orderContainsSellerProduct) {
            abort(403);
        }

        return view('seller.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $sellerId = Auth::id();
        $orderContainsSellerProduct = $order->products()->where('user_id', $sellerId)->exists();

        if (!$orderContainsSellerProduct) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);

        // Auto-Restock if order is cancelled
        if ($request->status === 'cancelled' && $oldStatus !== 'cancelled') {
            foreach ($order->products as $prod) {
                $qty = $prod->pivot->quantity ?? 1;
                if ($prod->track_inventory) {
                    $prod->addStock($qty, "Restored due to Order #{$order->order_number} cancellation", 'order_cancel', $order->id);
                }
            }
        }

        return redirect()->route('seller.orders.show', $order)->with('success', 'Order status updated successfully.');
    }
}
