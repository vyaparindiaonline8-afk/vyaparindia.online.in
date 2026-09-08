<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enquiry;
use Illuminate\Support\Facades\Auth;

class EnquiryController extends Controller
{
    public function index()
    {
        $sellerId = Auth::id();
        $enquiries = Enquiry::whereHas('product', function ($query) use ($sellerId) {
            $query->where('user_id', $sellerId);
        })->with('buyer', 'product')->get();

        return view('seller.enquiries.index', compact('enquiries'));
    }

    public function reply(Request $request, Enquiry $enquiry)
    {
        $sellerId = Auth::id();
        if ($enquiry->product->user_id !== $sellerId) {
            abort(403);
        }

        $request->validate([
            'reply_message' => 'required|string',
        ]);

        $enquiry->update([
            'reply_message' => $request->reply_message,
        ]);

        return redirect()->route('seller.enquiries.index')->with('success', 'Reply sent successfully.');
    }
}
