<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Enquiry;
use Illuminate\Support\Facades\Auth;

class EnquiryController extends Controller
{
    public function create(Product $product)
    {
        return view('buyer.enquiry.create', compact('product'));
    }

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $product->enquiries()->create([
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return redirect()->route('buyer.dashboard')->with('success', 'Enquiry sent successfully.');
    }
}
