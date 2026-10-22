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
use App\Models\BroadcastCampaign;

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
            ->with(['category', 'productViews', 'variants'])
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

        // 💰 Real Net Profit & Loss (P&L) Calculator Engine
        $grossSales = (clone $ordersQuery)->whereNotIn('status', ['cancelled', 'returned', 'rto'])->sum('total_price');

        // Returned / RTO Orders & Reverse Courier Loss
        $returnedOrders = Order::where(function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId)
              ->orWhereHas('products', function ($pq) use ($sellerId) {
                  $pq->where('user_id', $sellerId);
              });
        })->whereIn('status', ['returned', 'rto'])->get();

        $returnOrdersCount = $returnedOrders->count();
        $returnProductLoss = $returnedOrders->sum('total_price');
        $returnReverseCourierLoss = $returnOrdersCount * 75; // Approx ₹75 reverse logistics charge per RTO
        $totalReturnLoss = $returnProductLoss + $returnReverseCourierLoss;

        // Cost of Goods Sold (COGS / Maal Ki Lagat) & Marketplace Platform Fees
        $totalCogs = 0;
        $shippedOrdersCount = 0;
        $marketplaceFees = 0;

        $validOrders = (clone $ordersQuery)->whereNotIn('status', ['cancelled', 'returned', 'rto'])->with('products')->get();

        foreach ($validOrders as $ord) {
            $shippedOrdersCount++;
            $orderChannel = strtolower($ord->order_source ?? 'vyaparindia');

            // Marketplace commission: 15% on Amazon / Meesho / Flipkart, 0% on VyaparIndia direct
            if (in_array($orderChannel, ['amazon', 'flipkart', 'meesho', 'woocommerce', 'shopify'])) {
                $marketplaceFees += ($ord->total_price * 0.15);
            }

            foreach ($ord->products as $p) {
                $qty = $p->pivot->quantity ?? 1;
                $unitCost = ($p->purchase_price > 0) ? (float)$p->purchase_price : ((float)$p->price * 0.60);
                $totalCogs += ($unitCost * $qty);
            }
        }

        if ($totalCogs == 0 && $grossSales > 0) {
            $totalCogs = $grossSales * 0.60;
        }

        // Courier & Packaging Cost (Approx ₹60 per dispatched order)
        $courierPackagingCost = $shippedOrdersCount * 60;

        // Ads & Promotions (WhatsApp broadcast msgs @ ₹0.80 or promotional campaign spend)
        $broadcastSent = BroadcastCampaign::where('seller_id', $sellerId)->sum('sent_count');
        $adsSpend = ($broadcastSent * 0.80);
        if ($adsSpend == 0 && $grossSales > 0) {
            $adsSpend = round($grossSales * 0.03, 2); // 3% default marketing estimate
        }

        // Net In-Hand Profit (Asli Shuddh Munafa)
        $netProfit = $grossSales - $totalReturnLoss - $totalCogs - $courierPackagingCost - $marketplaceFees - $adsSpend;
        $netProfitMargin = $grossSales > 0 ? round(($netProfit / $grossSales) * 100, 1) : 0;

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
            'sellerPage',
            'grossSales',
            'returnOrdersCount',
            'totalReturnLoss',
            'totalCogs',
            'courierPackagingCost',
            'marketplaceFees',
            'adsSpend',
            'netProfit',
            'netProfitMargin'
        ));
    }
}
