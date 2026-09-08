<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Dispatch;
use Illuminate\Support\Facades\Auth;

class DispatchController extends Controller
{
    public function create(Order $order)
    {
        $sellerId = Auth::id();
        $orderContainsSellerProduct = $order->products()->where('user_id', $sellerId)->exists();

        if (!$orderContainsSellerProduct) {
            abort(403);
        }

        return view('seller.dispatch.create', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        $sellerId = Auth::id();
        $orderContainsSellerProduct = $order->products()->where('user_id', $sellerId)->exists();

        if (!$orderContainsSellerProduct) {
            abort(403);
        }

        $request->validate([
            'courier_name' => 'required|string|max:255',
            'tracking_number' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $order->dispatch()->create($request->all());

        $order->update(['status' => 'shipped']);

        return redirect()->route('seller.orders.show', $order)->with('success', 'Dispatch details added successfully.');
    }
}
