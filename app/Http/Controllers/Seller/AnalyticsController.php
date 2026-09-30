<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Order;
use App\Models\ProductView;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $sellerId = Auth::id();
        $range = $request->input('range', '30_days'); // today, 7_days, 30_days, all

        $products = Product::where('user_id', $sellerId)
            ->withCount(['productViews', 'orders'])
            ->with(['category', 'productViews'])
            ->get();

        $totalViews = $products->sum('views_count');
        
        // Orders calculation
        $ordersQuery = Order::where(function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId)
              ->orWhereHas('products', function ($pq) use ($sellerId) {
                  $pq->where('user_id', $sellerId);
              });
        })->where('status', '!=', 'cancelled');

        $totalOrdersCount = (clone $ordersQuery)->count();
        $totalRevenue = (clone $ordersQuery)->sum('total_price');

        // Today's stats
        $todayViews = ProductView::whereHas('product', function ($q) use ($sellerId) {
            $q->where('user_id', $sellerId);
        })->whereDate('created_at', now()->today())->count();

        $todayOrders = (clone $ordersQuery)->whereDate('created_at', now()->today())->count();

        // This Month stats
        $monthViews = ProductView::whereHas('product', function ($q) use ($sellerId) {
            $q->where('user_id', $sellerId);
        })->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        $monthOrders = (clone $ordersQuery)->whereMonth('created_at', now()->month)->count();

        // Overall conversion rate (Orders / Views)
        $conversionRate = $totalViews > 0 ? round(($totalOrdersCount / $totalViews) * 100, 2) : 0;

        // Top Selling / High Demand products
        $topSelling = $products->sortByDesc('orders_count')->take(5);

        // Most Viewed products
        $topViewed = $products->sortByDesc('views_count')->take(5);

        // Slow Moving products (Have views but 0 or low orders)
        $slowMoving = $products->where('orders_count', 0)->where('views_count', '>', 0)->sortByDesc('views_count')->take(5);

        return view('seller.analytics.index', compact(
            'products',
            'totalViews',
            'todayViews',
            'monthViews',
            'totalOrdersCount',
            'todayOrders',
            'monthOrders',
            'totalRevenue',
            'conversionRate',
            'topSelling',
            'topViewed',
            'slowMoving',
            'range'
        ));
    }
}
