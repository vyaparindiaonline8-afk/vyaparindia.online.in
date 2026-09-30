<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $wishlists = Auth::user()->wishlists()
            ->with(['product.seller.sellerProfile', 'product.seller.sellerPage', 'product.category'])
            ->latest()
            ->paginate(12);

        return view('buyer.wishlist.index', compact('wishlists'));
    }

    public function toggle(Request $request, Product $product)
    {
        $userId = Auth::id();
        $existing = Wishlist::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $status = 'removed';
            $message = 'Item removed from wishlist.';
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $product->id,
            ]);
            $status = 'added';
            $message = 'Item added to your wishlist! ❤️';
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $status,
                'message' => $message,
                'count' => Auth::user()->wishlists()->count(),
            ]);
        }

        return back()->with('success', $message);
    }

    public function destroy(Product $product)
    {
        Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->delete();

        return back()->with('success', 'Product removed from your wishlist.');
    }
}
