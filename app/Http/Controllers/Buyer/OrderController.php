<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Auth::user()->orders;
        return view('buyer.orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::all();
        return view('buyer.orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*' => 'integer|min:0',
        ]);

        $totalPrice = 0;
        $productsInOrder = [];

        foreach ($request->products as $productId => $quantity) {
            if ($quantity > 0) {
                $product = Product::findOrFail($productId);
                $totalPrice += $product->price * $quantity;
                $productsInOrder[$productId] = ['quantity' => $quantity, 'price' => $product->price];
            }
        }

        if (empty($productsInOrder)) {
            return back()->withErrors(['products' => 'You must select at least one product.']);
        }

        $order = Auth::user()->orders()->create([
            'total_price' => $totalPrice,
        ]);

        $order->products()->attach($productsInOrder);

        return redirect()->route('buyer.orders.index')->with('success', 'Order created successfully.');
    }

    public function show(Order $order)
    {
        if (Auth::id() !== $order->user_id) {
            abort(403);
        }

        return view('buyer.orders.show', compact('order'));
    }
}
