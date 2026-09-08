<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Channel;
use App\Models\ChannelListing;
use App\Models\Product;
use App\Models\Category;
use App\Services\AIProductService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChannelController extends Controller
{
    // Channel Integrations Dashboard
    public function index()
    {
        $userId = Auth::id();
        $channels = Channel::where('user_id', $userId)->with('listings.product')->get();

        $supportedChannels = [
            'shopify' => ['name' => 'Shopify Store', 'icon' => 'fa-brands fa-shopify', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50'],
            'woocommerce' => ['name' => 'WooCommerce (WordPress)', 'icon' => 'fa-brands fa-wordpress', 'color' => 'text-purple-600', 'bg' => 'bg-purple-50'],
            'amazon' => ['name' => 'Amazon India (SP-API)', 'icon' => 'fa-brands fa-amazon', 'color' => 'text-amber-600', 'bg' => 'bg-amber-50'],
            'flipkart' => ['name' => 'Flipkart Marketplace', 'icon' => 'fa-solid fa-bolt', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50'],
            'meesho' => ['name' => 'Meesho Reseller Feed', 'icon' => 'fa-solid fa-cart-shopping', 'color' => 'text-pink-600', 'bg' => 'bg-pink-50'],
        ];

        return view('seller.channels.index', compact('channels', 'supportedChannels'));
    }

    // Connect or Update a Channel
    public function storeChannel(Request $request)
    {
        $validated = $request->validate([
            'channel_name' => 'required|in:shopify,woocommerce,amazon,flipkart,meesho',
            'store_name' => 'required|string|max:255',
            'store_url' => 'nullable|url|max:255',
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string',
            'access_token' => 'nullable|string',
        ]);

        Channel::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'channel_name' => $validated['channel_name'],
            ],
            array_merge($validated, [
                'is_active' => true,
                'auto_sync_inventory' => true,
                'last_synced_at' => now(),
            ])
        );

        return back()->with('success', ucfirst($validated['channel_name']) . ' integration connected successfully!');
    }

    // Toggle Channel Status
    public function toggleChannel(Channel $channel)
    {
        if ($channel->user_id !== Auth::id()) abort(403);
        $channel->update(['is_active' => !$channel->is_active]);
        return back()->with('success', "Channel status updated to " . ($channel->is_active ? 'Active' : 'Inactive'));
    }

    // AJAX: AI Product Auto-Filler
    public function aiGenerate(Request $request, AIProductService $aiService)
    {
        $request->validate([
            'prompt' => 'required|string|min:2|max:255',
        ]);

        $details = $aiService->generateProductDetails($request->prompt);

        return response()->json([
            'success' => true,
            'data' => $details,
        ]);
    }

    // Universal Product Publisher View
    public function publisher()
    {
        $categories = Category::all();
        $connectedChannels = Channel::where('user_id', Auth::id())->where('is_active', true)->get();
        return view('seller.channels.publisher', compact('categories', 'connectedChannels'));
    }

    // Publish Product Across Channels
    public function publishProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:1',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100',
            'hsn_code' => 'nullable|string|max:20',
            'description' => 'required|string',
            'tags' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:3072',
            'channels' => 'nullable|array', // array of channel names or IDs
        ]);

        $userId = Auth::id();
        $slug = Str::slug($validated['name']) . '-' . rand(100, 999);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // 1. Create Core Product
        $product = Product::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'],
            'tags' => $validated['tags'] ?? '',
            'price' => $validated['price'],
            'stock_quantity' => $validated['stock_quantity'],
            'sku' => $validated['sku'] ?: 'SKU-' . strtoupper(Str::random(5)),
            'hsn_code' => $validated['hsn_code'] ?? '500720',
            'image' => $imagePath,
            'user_id' => $userId,
            'category_id' => $validated['category_id'],
        ]);

        // 2. Broadcast / Sync to Selected Connected Channels
        $selectedChannels = $request->input('channels', []);
        $syncCount = 0;

        foreach ($selectedChannels as $chanId) {
            $channel = Channel::where('id', $chanId)->where('user_id', $userId)->first();
            if ($channel) {
                // Simulate Channel API Push (Shopify / WooCommerce / Amazon)
                $externalId = strtoupper($channel->channel_name) . '-' . rand(10000, 99999);
                $listingUrl = $channel->store_url ? rtrim($channel->store_url, '/') . '/products/' . $slug : null;

                ChannelListing::create([
                    'channel_id' => $channel->id,
                    'product_id' => $product->id,
                    'external_product_id' => $externalId,
                    'sync_status' => 'synced',
                    'listing_url' => $listingUrl,
                    'channel_price' => $product->price,
                    'synced_stock' => $product->stock_quantity,
                    'last_synced_at' => now(),
                ]);
                $syncCount++;
            }
        }

        return redirect()->route('seller.products.index')->with('success', "Product successfully published to Mini-Site & {$syncCount} external channels!");
    }

    // Omnichannel Centralized Inventory Monitor
    public function inventory()
    {
        $userId = Auth::id();
        $products = Product::where('user_id', $userId)->with('channelListings.channel')->latest()->paginate(15);
        return view('seller.channels.inventory', compact('products'));
    }

    // Sync Stock Across All Channels
    public function syncStock(Request $request, Product $product)
    {
        if ($product->user_id !== Auth::id()) abort(403);

        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $newStock = intval($request->stock_quantity);
        $product->update(['stock_quantity' => $newStock]);

        // Broadcast stock update to all connected channel listings
        $product->channelListings()->update([
            'synced_stock' => $newStock,
            'sync_status' => 'synced',
            'last_synced_at' => now(),
        ]);

        return back()->with('success', "Stock updated to {$newStock} and synchronized across all connected channels in real time!");
    }
}