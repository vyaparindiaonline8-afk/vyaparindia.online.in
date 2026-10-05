<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Order;
use App\Models\ProductView;
use App\Models\AbandonedCart;
use App\Models\Wishlist;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $sellerId = Auth::id();
        $user = Auth::user();
        $sellerPage = $user->sellerPage;

        // 1. 🌐 Mini-Website Storefront Visits
        $storeVisits = $sellerPage ? ($sellerPage->visits_count ?? 0) : 0;

        // 2. 👁️ Product Views & Catalog Metrics
        $products = Product::where('user_id', $sellerId)
            ->withCount(['productViews', 'orders', 'wishlists'])
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

        // 3. 🛒 Abandoned / Dropped Checkouts (ऑर्डर तक गए पर पेमेंट नहीं किया)
        $abandonedCarts = AbandonedCart::where('seller_id', $sellerId)->latest()->get();
        $unpaidOrders = Order::where(function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId)
              ->orWhereHas('products', function ($pq) use ($sellerId) {
                  $pq->where('user_id', $sellerId);
              });
        })->where(function ($oq) {
            $oq->where('payment_status', 'unpaid')
               ->orWhere('status', 'pending');
        })->latest()->take(10)->get();

        $totalAbandonedCount = $abandonedCarts->count() + $unpaidOrders->count();
        $totalAbandonedAmount = $abandonedCarts->sum('total_amount') + $unpaidOrders->sum('total_price');

        // 4. ❤️ Wishlist Tracking (कितने लोगों ने कौन सा प्रोडक्ट विशलिस्ट में रखा है)
        $productIds = $products->pluck('id');
        $totalWishlistsCount = Wishlist::whereIn('product_id', $productIds)->count();
        $topWishlistProducts = $products->where('wishlists_count', '>', 0)->sortByDesc('wishlists_count')->take(5);

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
            'storeVisits',
            'totalViews',
            'todayViews',
            'monthViews',
            'totalOrdersCount',
            'todayOrders',
            'monthOrders',
            'totalRevenue',
            'totalAbandonedCount',
            'totalAbandonedAmount',
            'abandonedCarts',
            'unpaidOrders',
            'totalWishlistsCount',
            'topWishlistProducts',
            'conversionRate',
            'topSelling',
            'topViewed',
            'slowMoving',
            'sellerPage'
        ));
    }
}
