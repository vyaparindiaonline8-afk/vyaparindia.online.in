<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class DispatchViewController extends Controller
{
    public function show(Order $order)
    {
        if (Auth::id() !== $order->user_id) {
            abort(403);
        }

        return view('buyer.dispatch.show', compact('order'));
    }
}
