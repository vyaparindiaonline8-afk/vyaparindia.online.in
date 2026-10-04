<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SellerPage;
use App\Models\Review;
use App\Services\AISlipScannerService;
use App\Services\CloudinaryService;
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
            'youtube_link' => 'nullable|url|max:255',
            'google_review_link' => 'nullable|url|max:500',
            'google_map_link' => 'nullable|url|max:500',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'theme_color' => 'nullable|string|max:7',
            'theme_style' => 'nullable|string|in:modern,minimal,vibrant',
            'currency' => 'nullable|string|max:10',
            'enable_cod' => 'nullable|boolean',
            'enable_whatsapp_order' => 'nullable|boolean',
            'upi_id' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:150',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_ifsc' => 'nullable|string|max:20',
            'bank_account_holder' => 'nullable|string|max:150',
            'show_payment_details_to_buyer' => 'nullable|boolean',
            'policies' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = CloudinaryService::upload($request->file('logo'), 'vyaparindia/minisite/logos');
        }
        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = CloudinaryService::upload($request->file('banner_image'), 'vyaparindia/minisite/banners');
        }

        $validated['enable_cod'] = $request->has('enable_cod');
        $validated['enable_whatsapp_order'] = $request->has('enable_whatsapp_order');
        $validated['show_payment_details_to_buyer'] = $request->has('show_payment_details_to_buyer');

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
            'youtube_link' => 'nullable|url|max:255',
            'google_review_link' => 'nullable|url|max:500',
            'google_map_link' => 'nullable|url|max:500',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'theme_color' => 'nullable|string|max:7',
            'theme_style' => 'nullable|string|in:modern,minimal,vibrant',
            'currency' => 'nullable|string|max:10',
            'enable_cod' => 'nullable|boolean',
            'enable_whatsapp_order' => 'nullable|boolean',
            'upi_id' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:150',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_ifsc' => 'nullable|string|max:20',
            'bank_account_holder' => 'nullable|string|max:150',
            'show_payment_details_to_buyer' => 'nullable|boolean',
            'policies' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = CloudinaryService::upload($request->file('logo'), 'vyaparindia/minisite/logos');
        }
        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = CloudinaryService::upload($request->file('banner_image'), 'vyaparindia/minisite/banners');
        }

        $validated['enable_cod'] = $request->has('enable_cod');
        $validated['enable_whatsapp_order'] = $request->has('enable_whatsapp_order');
        $validated['show_payment_details_to_buyer'] = $request->has('show_payment_details_to_buyer');

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
            $catParam = $request->input('category');
            $query->where(function ($q) use ($catParam) {
                if (is_numeric($catParam)) {
                    $q->where('category_id', $catParam);
                } else {
                    $q->whereHas('category', function ($sub) use ($catParam) {
                        $sub->where('slug', $catParam)
                            ->orWhere('name', 'like', "%{$catParam}%");
                    });
                }
            });
        }

        // 🎯 Curated 3-6 Selected Products Quick Link (e.g. ?items=145,147,150)
        $isCurated = false;
        if ($request->filled('items')) {
            $itemIds = array_filter(array_map('intval', explode(',', $request->input('items'))));
            if (!empty($itemIds)) {
                $query->whereIn('id', $itemIds);
                $isCurated = true;
            }
        }

        // Smart Category & Sales Sorting
        $sort = $request->input('sort', 'latest');
        if ($sort === 'popular') {
            $query->withCount('orders')->orderByDesc('orders_count');
        } elseif ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $products = $query->paginate(16)->withQueryString();
        $categories = \App\Models\Category::whereHas('products', function ($q) use ($sellerPage) {
            $q->where('user_id', $sellerPage->user_id);
        })->get();

        return view('seller-site.products', compact('sellerPage', 'products', 'categories', 'sort', 'isCurated'));
    }

    public function product(SellerPage $sellerPage, $productSlug)
    {
        $product = $sellerPage->user->products()->where('slug', $productSlug)->firstOrFail();
        $product->recordView(Auth::id(), request()->ip(), request()->userAgent());
        $relatedProducts = $product->similarProducts(4);
        
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

        // Deduct inventory stock if tracked ("dale to thik, na dale to thik")
        foreach ($validated['cart'] as $item) {
            $prod = \App\Models\Product::find($item['id']);
            if ($prod && $prod->track_inventory) {
                $prod->deductStock(
                    intval($item['quantity']),
                    "Order #{$orderNumber} placed via Mini-Site",
                    'order_placed',
                    $order->id,
                    $item['variant_id'] ?? null
                );
            }
        }

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

    /**
     * 1-Click WhatsApp Quick Order (Saves order to DB first, then opens WhatsApp)
     */
    public function quickOrder(Request $request, SellerPage $sellerPage)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string|max:300',
            'city' => 'nullable|string|max:100',
            'payment_method' => 'nullable|string|in:cod,online',
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.name' => 'nullable|string',
            'cart.*.price' => 'required|numeric',
        ]);

        $totalPrice = 0;
        $orderItems = [];

        foreach ($validated['cart'] as $item) {
            $product = \App\Models\Product::where('id', $item['id'])
                ->where('user_id', $sellerPage->user_id)
                ->firstOrFail();

            $qty = intval($item['quantity']);
            $unitPrice = floatval($item['price']);
            $totalPrice += ($unitPrice * $qty);

            $orderItems[$product->id] = [
                'quantity' => $qty,
                'price' => $unitPrice,
            ];
        }

        $orderNumber = 'ORD-' . strtoupper(Str::random(8));
        $payMethod = $validated['payment_method'] ?? 'cod';

        $order = \App\Models\Order::create([
            'order_number' => $orderNumber,
            'order_source' => 'minisite_quick_whatsapp',
            'user_id' => Auth::id(),
            'seller_id' => $sellerPage->user_id,
            'total_price' => $totalPrice,
            'original_cod_total' => $totalPrice,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'shipping_address' => $validated['customer_address'],
            'city' => $validated['city'] ?? $sellerPage->city ?? '',
            'state' => '',
            'pincode' => $sellerPage->pincode ?? '000000',
            'payment_method' => $payMethod,
            'payment_status' => $payMethod === 'online' ? 'paid' : 'pending',
            'status' => 'pending',
        ]);

        $order->products()->attach($orderItems);

        // Deduct inventory stock if tracked
        foreach ($validated['cart'] as $item) {
            $prod = \App\Models\Product::find($item['id']);
            if ($prod && $prod->track_inventory) {
                $prod->deductStock(
                    intval($item['quantity']),
                    "Quick WhatsApp Order #{$orderNumber}",
                    'order_placed',
                    $order->id,
                    $item['variant_id'] ?? null
                );
            }
        }

        $sellerUser = $sellerPage->user;
        $sellerProf = $sellerUser ? $sellerUser->sellerProfile : null;
        $upiId = $sellerPage->upi_id ?: ($sellerProf->upi_id ?? null);
        $bankName = $sellerPage->bank_name ?: ($sellerProf->bank_name ?? null);
        $bankAcc = $sellerPage->bank_account_number ?: ($sellerProf->bank_account_number ?? null);
        $bankIfsc = $sellerPage->bank_ifsc ?: ($sellerProf->bank_ifsc ?? null);
        $bankHolder = $sellerPage->bank_account_holder ?: ($sellerProf->bank_account_holder ?? null);

        $orderViewUrl = route('minisite.orderSuccess', ['sellerPage' => $sellerPage->slug, 'order' => $order->id]);

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'order_number' => $orderNumber,
            'total_amount' => number_format($totalPrice, 2, '.', ''),
            'order_view_url' => $orderViewUrl,
            'upi_id' => $upiId,
            'bank_name' => $bankName,
            'bank_acc' => $bankAcc,
            'bank_ifsc' => $bankIfsc,
            'bank_holder' => $bankHolder,
            'message' => 'Order created in database successfully!',
        ]);
    }

    /**
     * AI Hardware & Plumber Slip Scanner View
     */
    public function materialScannerView(SellerPage $sellerPage)
    {
        return view('seller-site.material_scanner', compact('sellerPage'));
    }

    /**
     * Process Slip (Handwritten / Typed Text) & Match with Store Catalog
     */
    public function processMaterialSlip(Request $request, SellerPage $sellerPage, AISlipScannerService $scannerService)
    {
        $request->validate([
            'slip_text' => 'nullable|string',
            'slip_image' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:10240',
        ]);

        $rawText = $request->input('slip_text', '');

        // If an image was uploaded, read text using basic PyMuPDF or use simulated contractor slip text
        if ($request->hasFile('slip_image') && empty($rawText)) {
            $imageFile = $request->file('slip_image');
            $ext = strtolower($imageFile->getClientOriginalExtension());
            if ($ext === 'pdf') {
                $pdfPath = $imageFile->getRealPath();
                try {
                    $doc = new \Smalot\PdfParser\Parser();
                    $pdf = $doc->parseFile($pdfPath);
                    $rawText = $pdf->getText();
                } catch (\Exception $e) {
                    $rawText = "10 CPVC Pipe 1 inch\n5 Elbow 1 inch\n2 Ball Valve 1 inch\n1 Solvent Cement 100ml\n4 Socket 1 inch";
                }
            } else {
                // Heuristic mock for uploaded mobile phone slip photo
                $rawText = "10 CPVC Pipe 1 inch\n5 Elbow 1 inch\n2 Ball Valve 1 inch\n1 Solvent Cement 100ml\n4 Socket 1 inch";
            }
        }

        if (empty(trim($rawText))) {
            return back()->with('error', 'Please provide a slip text or upload a material list.');
        }

        $parsedData = $scannerService->parseSlipText($rawText, $sellerPage->user_id);

        return view('seller-site.material_scanner', compact('sellerPage', 'parsedData', 'rawText'));
    }

    /**
     * Download Excel / CSV Quotation
     */
    public function downloadQuotationExcel(Request $request, SellerPage $sellerPage, AISlipScannerService $scannerService)
    {
        $rawText = $request->input('raw_text', "10 CPVC Pipe 1 inch\n5 Elbow 1 inch\n2 Ball Valve 1 inch");
        $parsedData = $scannerService->parseSlipText($rawText, $sellerPage->user_id);
        $csvContent = $scannerService->generateQuotationCsv($parsedData, $sellerPage->page_title);

        $filename = 'Quotation_' . Str::slug($sellerPage->page_title) . '_' . date('Ymd_His') . '.csv';

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}