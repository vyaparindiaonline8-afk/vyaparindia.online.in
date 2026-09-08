<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\DropshipProduct;
use App\Models\DropshipOrder;
use App\Models\DropshipWallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DropshipController extends Controller
{
    // Browse Wholesaler Catalog Hub
    public function hub(Request $request)
    {
        $currentUserId = Auth::id();
        
        $query = Product::where('user_id', '!=', $currentUserId)->with(['seller', 'category']);

        if ($request->filled('wholesaler_id')) {
            $query->where('user_id', $request->wholesaler_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%");
            });
        }

        $products = $query->latest()->paginate(12);
        
        $wholesalers = User::whereHas('products', function ($q) use ($currentUserId) {
            $q->where('user_id', '!=', $currentUserId);
        })->with('sellerProfile')->get();

        $categories = Category::all();

        // Get IDs already imported by this dropshipper
        $importedProductIds = DropshipProduct::where('dropshipper_id', $currentUserId)
            ->pluck('supplier_product_id')
            ->toArray();

        return view('seller.dropship.hub', compact('products', 'wholesalers', 'categories', 'importedProductIds'));
    }

    // 1-Click Single Product Import
    public function importSingle(Request $request)
    {
        $validated = $request->validate([
            'supplier_product_id' => 'required|exists:products,id',
            'retail_price' => 'required|numeric|min:1',
            'custom_name' => 'nullable|string|max:255',
        ]);

        $dropshipperId = Auth::id();
        $supplierProduct = Product::findOrFail($validated['supplier_product_id']);

        if ($supplierProduct->user_id == $dropshipperId) {
            return back()->with('error', 'You cannot import your own product.');
        }

        $wholesalePrice = $supplierProduct->price;
        $retailPrice = floatval($validated['retail_price']);
        $profitMargin = $retailPrice - $wholesalePrice;

        // 1. Create or update DropshipProduct mapping
        DropshipProduct::updateOrCreate(
            [
                'dropshipper_id' => $dropshipperId,
                'supplier_product_id' => $supplierProduct->id,
            ],
            [
                'wholesaler_id' => $supplierProduct->user_id,
                'custom_name' => $validated['custom_name'] ?: $supplierProduct->name,
                'wholesale_price' => $wholesalePrice,
                'retail_price' => $retailPrice,
                'profit_margin' => $profitMargin,
                'is_active' => true,
            ]
        );

        // 2. Clone into dropshipper's own Product catalog for their Mini-Site
        $existingCatalogProduct = Product::where('user_id', $dropshipperId)
            ->where('slug', $supplierProduct->slug . '-ds-' . $dropshipperId)
            ->first();

        if (!$existingCatalogProduct) {
            Product::create([
                'name' => $validated['custom_name'] ?: $supplierProduct->name,
                'slug' => $supplierProduct->slug . '-ds-' . $dropshipperId,
                'description' => $supplierProduct->description,
                'price' => $retailPrice,
                'image' => $supplierProduct->image,
                'user_id' => $dropshipperId,
                'category_id' => $supplierProduct->category_id,
            ]);
        } else {
            $existingCatalogProduct->update([
                'name' => $validated['custom_name'] ?: $supplierProduct->name,
                'price' => $retailPrice,
            ]);
        }

        return back()->with('success', 'Product imported successfully to your store catalog with ₹' . number_format($profitMargin, 2) . ' profit margin!');
    }

    // 1-Click Bulk Import All Products
    public function importBulk(Request $request)
    {
        $validated = $request->validate([
            'wholesaler_id' => 'required|exists:users,id',
            'margin_type' => 'required|in:percentage,fixed',
            'margin_value' => 'required|numeric|min:1',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $dropshipperId = Auth::id();
        $wholesalerId = $validated['wholesaler_id'];

        if ($wholesalerId == $dropshipperId) {
            return back()->with('error', 'Invalid wholesaler selected.');
        }

        $query = Product::where('user_id', $wholesalerId);
        if (!empty($validated['category_id'])) {
            $query->where('category_id', $validated['category_id']);
        }

        $supplierProducts = $query->get();
        $importedCount = 0;

        foreach ($supplierProducts as $sp) {
            $wholesalePrice = $sp->price;
            if ($validated['margin_type'] === 'percentage') {
                $markup = $wholesalePrice * (floatval($validated['margin_value']) / 100);
            } else {
                $markup = floatval($validated['margin_value']);
            }

            $retailPrice = round($wholesalePrice + $markup, 2);
            $profitMargin = $retailPrice - $wholesalePrice;

            DropshipProduct::updateOrCreate(
                [
                    'dropshipper_id' => $dropshipperId,
                    'supplier_product_id' => $sp->id,
                ],
                [
                    'wholesaler_id' => $wholesalerId,
                    'custom_name' => $sp->name,
                    'wholesale_price' => $wholesalePrice,
                    'retail_price' => $retailPrice,
                    'profit_margin' => $profitMargin,
                    'is_active' => true,
                ]
            );

            // Clone / update in dropshipper catalog
            $slug = $sp->slug . '-ds-' . $dropshipperId;
            Product::updateOrCreate(
                [
                    'user_id' => $dropshipperId,
                    'slug' => $slug,
                ],
                [
                    'name' => $sp->name,
                    'description' => $sp->description,
                    'price' => $retailPrice,
                    'image' => $sp->image,
                    'category_id' => $sp->category_id,
                ]
            );

            $importedCount++;
        }

        return back()->with('success', "{$importedCount} products bulk imported to your store catalog in 1 click!");
    }

    // Dropshipper My Products View
    public function myProducts()
    {
        $dropshipperId = Auth::id();
        $importedProducts = DropshipProduct::where('dropshipper_id', $dropshipperId)
            ->with(['supplierProduct', 'wholesaler'])
            ->latest()
            ->paginate(15);

        return view('seller.dropship.my_products', compact('importedProducts'));
    }

    // Dropshipper Track Fulfillment Orders
    public function orders()
    {
        $dropshipperId = Auth::id();
        $dropshipOrders = DropshipOrder::where('dropshipper_id', $dropshipperId)
            ->with(['order.products', 'wholesaler.sellerProfile'])
            ->latest()
            ->paginate(15);

        return view('seller.dropship.orders', compact('dropshipOrders'));
    }

    // Dropshipper Approves Price / Shipping Adjustment by Wholesaler
    public function approvePriceAdjustment(DropshipOrder $dsOrder)
    {
        if ($dsOrder->dropshipper_id !== Auth::id()) {
            abort(403);
        }

        $dsOrder->update([
            'dropshipper_approval_status' => 'approved',
            'fulfillment_status' => 'ready_to_pack',
        ]);

        return back()->with('success', 'You approved the updated pricing & shipping quote. Wholesaler will now pack and dispatch.');
    }

    // Dropshipper Rejects Price Adjustment
    public function rejectPriceAdjustment(DropshipOrder $dsOrder)
    {
        if ($dsOrder->dropshipper_id !== Auth::id()) {
            abort(403);
        }

        $dsOrder->update([
            'dropshipper_approval_status' => 'rejected',
            'fulfillment_status' => 'cancelled',
        ]);

        return back()->with('error', 'You rejected the wholesaler adjustment. Order cancelled.');
    }

    // Dropshipper Wallet & Payout Hub
    public function wallet()
    {
        $userId = Auth::id();
        $wallet = DropshipWallet::firstOrCreate(['user_id' => $userId]);
        $transactions = $wallet->transactions()->paginate(10);

        return view('seller.dropship.wallet', compact('wallet', 'transactions'));
    }
}