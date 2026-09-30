<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $sellerId = Auth::id();
        $query = Order::where(function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId)
              ->orWhereHas('products', function ($pq) use ($sellerId) {
                  $pq->where('user_id', $sellerId);
              });
        })->with(['buyer', 'products']);

        // Date Filter (Per-day order tracking)
        $dateFilter = $request->input('date', 'all');
        if ($dateFilter === 'today') {
            $query->whereDate('created_at', now()->today());
        } elseif ($dateFilter === 'yesterday') {
            $query->whereDate('created_at', now()->yesterday());
        } elseif ($dateFilter === 'this_week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($dateFilter === 'this_month') {
            $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        }

        // Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        // Metrics for today's orders
        $todayOrdersCount = Order::where(function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId)
              ->orWhereHas('products', function ($pq) use ($sellerId) {
                  $pq->where('user_id', $sellerId);
              });
        })->whereDate('created_at', now()->today())->count();

        $todayRevenue = Order::where(function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId)
              ->orWhereHas('products', function ($pq) use ($sellerId) {
                  $pq->where('user_id', $sellerId);
              });
        })->whereDate('created_at', now()->today())->where('status', '!=', 'cancelled')->sum('total_price');

        return view('seller.orders.index', compact('orders', 'dateFilter', 'todayOrdersCount', 'todayRevenue'));
    }

    public function show(Order $order)
    {
        $sellerId = Auth::id();
        $isMyOrder = ($order->seller_id == $sellerId) || $order->products()->where('user_id', $sellerId)->exists();

        if (!$isMyOrder) {
            abort(403);
        }

        return view('seller.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $sellerId = Auth::id();
        $isMyOrder = ($order->seller_id == $sellerId) || $order->products()->where('user_id', $sellerId)->exists();

        if (!$isMyOrder) {
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
