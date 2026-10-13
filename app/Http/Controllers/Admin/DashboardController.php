<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalSellers = User::where('role_id', 2)->count();
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalOrders = Order::count();

        return view('admin.dashboard', compact('totalUsers', 'totalSellers', 'totalProducts', 'totalCategories', 'totalOrders'));
    }
}
