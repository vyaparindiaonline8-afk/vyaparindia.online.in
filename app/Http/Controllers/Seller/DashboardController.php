<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $sellerId = Auth::id();
        $minisite = Auth::user()->sellerPage;
        $totalProducts = Auth::user()->products()->count();
        
        $ordersQuery = \App\Models\Order::where('seller_id', $sellerId)
            ->orWhereHas('products', function ($query) use ($sellerId) {
                $query->where('user_id', $sellerId);
            });

        $totalOrders = (clone $ordersQuery)->count();
        $pendingOrders = (clone $ordersQuery)->where('status', 'pending')->count();
        $totalRevenue = (clone $ordersQuery)->where('status', '!=', 'cancelled')->sum('total_price');
        $recentOrders = (clone $ordersQuery)->with('products')->latest()->take(5)->get();

        $sellerProfile = Auth::user()->sellerProfile;
        $myProducts = Auth::user()->products()->with(['variants', 'category'])->latest()->take(12)->get();

        return view('seller.dashboard', compact(
            'minisite',
            'sellerProfile',
            'myProducts',
            'totalProducts',
            'totalOrders',
            'pendingOrders',
            'totalRevenue',
            'recentOrders'
        ));
    }
}
