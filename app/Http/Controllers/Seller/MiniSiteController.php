<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SellerPage;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MiniSiteController extends Controller
{
    // Seller facing methods
    public function create()
    {
        if (Auth::user()->sellerPage) {
            return redirect()->route('seller.minisite.edit');
        }
        return view('seller.minisite.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'page_title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:seller_pages|alpha_dash',
            'tagline' => 'nullable|string|max:255',
            'welcome_message' => 'nullable|string',
            'about_text' => 'nullable|string',
            'whatsapp_number' => 'nullable|string|max:20',
            'support_phone' => 'nullable|string|max:20',
            'support_email' => 'nullable|email|max:255',
            'instagram_link' => 'nullable|url|max:255',
            'facebook_link' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'theme_color' => 'nullable|string|max:7',
            'theme_style' => 'nullable|string|in:modern,minimal,vibrant',
            'currency' => 'nullable|string|max:10',
            'enable_cod' => 'nullable|boolean',
            'enable_whatsapp_order' => 'nullable|boolean',
            'policies' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('minisite/logos', 'public');
        }
        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('minisite/banners', 'public');
        }

        $validated['enable_cod'] = $request->has('enable_cod');
        $validated['enable_whatsapp_order'] = $request->has('enable_whatsapp_order');

        Auth::user()->sellerPage()->create($validated);

        return redirect()->route('seller.minisite.edit')->with('success', 'Mini-Storefront created successfully! Your store is live.');
    }

    public function edit()
    {
        $minisite = Auth::user()->sellerPage;
        if (!$minisite) {
            return redirect()->route('seller.minisite.create');
        }
        return view('seller.minisite.form', compact('minisite'));
    }

    public function update(Request $request)
    {
        $minisite = Auth::user()->sellerPage;
        if (!$minisite) {
            return redirect()->route('seller.minisite.create');
        }

        $validated = $request->validate([
            'page_title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|alpha_dash|unique:seller_pages,slug,' . $minisite->id,
            'tagline' => 'nullable|string|max:255',
            'welcome_message' => 'nullable|string',
            'about_text' => 'nullable|string',
            'whatsapp_number' => 'nullable|string|max:20',
            'support_phone' => 'nullable|string|max:20',
            'support_email' => 'nullable|email|max:255',
            'instagram_link' => 'nullable|url|max:255',
            'facebook_link' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'theme_color' => 'nullable|string|max:7',
            'theme_style' => 'nullable|string|in:modern,minimal,vibrant',
            'currency' => 'nullable|string|max:10',
            'enable_cod' => 'nullable|boolean',
            'enable_whatsapp_order' => 'nullable|boolean',
            'policies' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('minisite/logos', 'public');
        }
        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('minisite/banners', 'public');
        }

        $validated['enable_cod'] = $request->has('enable_cod');
        $validated['enable_whatsapp_order'] = $request->has('enable_whatsapp_order');

        $minisite->update($validated);

        return redirect()->route('seller.minisite.edit')->with('success', 'Mini-Storefront settings updated successfully.');
    }

    // Public Storefront methods
    public function show(SellerPage $sellerPage)
    {
        $products = $sellerPage->user->products()->latest()->take(12)->get();
        $totalProducts = $sellerPage->user->products()->count();
        return view('seller-site.home', compact('sellerPage', 'products', 'totalProducts'));
    }

    public function products(SellerPage $sellerPage, Request $request)
    {
        $query = $sellerPage->user->products();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        $products = $query->latest()->paginate(12);
        $categories = \App\Models\Category::whereHas('products', function ($q) use ($sellerPage) {
            $q->where('user_id', $sellerPage->user_id);
        })->get();

        return view('seller-site.products', compact('sellerPage', 'products', 'categories'));
    }

    public function product(SellerPage $sellerPage, $productSlug)
    {
        $product = $sellerPage->user->products()->where('slug', $productSlug)->firstOrFail();
        $relatedProducts = $sellerPage->user->products()
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->take(4)
            ->get();
        
        $reviews = $product->reviews()->latest()->get();

        return view('seller-site.product_detail', compact('sellerPage', 'product', 'relatedProducts', 'reviews'));
    }

    public function submitReview(Request $request, SellerPage $sellerPage, $productSlug)
    {
        $product = $sellerPage->user->products()->where('slug', $productSlug)->firstOrFail();

        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_city' => 'nullable|string|max:100',
            'rating' => 'required|integer|min:1|max:5',
            'review_title' => 'nullable|string|max:200',
            'comment' => 'required|string|max:1000',
        ]);

        Review::create([
            'product_id' => $product->id,
            'seller_id' => $sellerPage->user_id,
            'customer_name' => $validated['customer_name'],
            'customer_city' => $validated['customer_city'] ?? 'India',
            'review_title' => $validated['review_title'] ?? 'Verified Customer Review',
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'is_verified_purchase' => true,
            'is_approved' => true,
        ]);

        return back()->with('review_success', 'Thank you! Your verified rating & review has been published.');
    }

    public function contact(SellerPage $sellerPage)
    {
        return view('seller-site.contact', compact('sellerPage'));
    }

    public function checkout(SellerPage $sellerPage)
    {
        return view('seller-site.checkout', compact('sellerPage'));
    }

    public function placeOrder(Request $request, SellerPage $sellerPage)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'shipping_address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'required|string|max:10',
            'payment_method' => 'required|string|in:cod,online',
            'notes' => 'nullable|string|max:500',
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);

        $totalPrice = 0;
        $orderItems = [];

        foreach ($validated['cart'] as $item) {
            $product = \App\Models\Product::where('id', $item['id'])
                ->where('user_id', $sellerPage->user_id)
                ->firstOrFail();
            $qty = intval($item['quantity']);
            $lineTotal = $product->price * $qty;
            $totalPrice += $lineTotal;

            $orderItems[$product->id] = [
                'quantity' => $qty,
                'price' => $product->price,
            ];
        }

        $token = Str::random(32);
        $discountAmount = min(50.00, round($totalPrice * 0.05, 2));
        $orderNumber = 'ORD-' . strtoupper(Str::random(8));

        $order = \App\Models\Order::create([
            'order_number' => $orderNumber,
            'order_source' => 'minisite',
            'user_id' => Auth::id(),
            'seller_id' => $sellerPage->user_id,
            'total_price' => $totalPrice,
            'original_cod_total' => $totalPrice,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'] ?? null,
            'shipping_address' => $validated['shipping_address'],
            'city' => $validated['city'],
            'state' => $validated['state'] ?? '',
            'pincode' => $validated['pincode'],
            'payment_method' => $validated['payment_method'],
            'payment_status' => $validated['payment_method'] === 'online' ? 'paid' : 'pending',
            'cod_verification_status' => $validated['payment_method'] === 'online' ? 'converted_to_prepaid' : 'unverified',
            'cod_verification_token' => $token,
            'verification_deadline_at' => now()->addHours(6),
            'cod_to_prepaid_discount' => $discountAmount,
            'rto_risk_score' => $validated['payment_method'] === 'online' ? 'low' : ($totalPrice > 2500 ? 'medium' : 'low'),
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        $order->products()->attach($orderItems);

        // Auto-Route Dropship Products to Wholesalers
        foreach ($validated['cart'] as $item) {
            $product = \App\Models\Product::find($item['id']);
            if (!$product) continue;
            $qty = intval($item['quantity']);

            $dsMapping = \App\Models\DropshipProduct::where('dropshipper_id', $sellerPage->user_id)
                ->where(function ($q) use ($product) {
                    $q->where('custom_name', $product->name);
                })
                ->first();

            if ($dsMapping) {
                $supplierBaseCost = $dsMapping->wholesale_price * $qty;
                $retailTotal = $product->price * $qty;
                $profit = $retailTotal - $supplierBaseCost;
                $dsOrderNumber = 'DS-' . strtoupper(Str::random(3)) . '-' . rand(1000, 9999);

                \App\Models\DropshipOrder::create([
                    'ds_order_number' => $dsOrderNumber,
                    'order_id' => $order->id,
                    'dropshipper_id' => $sellerPage->user_id,
                    'wholesaler_id' => $dsMapping->wholesaler_id,
                    'customer_retail_total' => $retailTotal,
                    'supplier_base_cost' => $supplierBaseCost,
                    'shipping_cost' => 0.00,
                    'total_supplier_payable' => $supplierBaseCost,
                    'dropshipper_profit' => $profit,
                    'price_adjusted_by_wholesaler' => false,
                    'dropshipper_approval_status' => 'approved',
                    'fulfillment_status' => 'pending_wholesaler_review',
                    'payment_collection_mode' => $validated['payment_method'],
                    'cod_remittance_status' => 'pending',
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'redirect_url' => route('minisite.orderSuccess', ['sellerPage' => $sellerPage->slug, 'order' => $order->id]),
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ]);
    }

    public function orderSuccess(SellerPage $sellerPage, \App\Models\Order $order)
    {
        if ($order->seller_id !== $sellerPage->user_id) {
            abort(404);
        }
        return view('seller-site.order_success', compact('sellerPage', 'order'));
    }
}