<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\BrandMasterProduct;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BrandMasterController extends Controller
{
    /**
     * Display central brand master catalog with live less & costing calculator.
     */
    public function index(Request $request)
    {
        $selectedBrand = $request->query('brand', 'Plasto');
        $selectedCategory = $request->query('category');

        $brands = BrandMasterProduct::distinct()->pluck('brand_name');
        
        $categoriesQuery = BrandMasterProduct::where('brand_name', $selectedBrand);
        $categories = $categoriesQuery->distinct()->pluck('category_name');

        $query = BrandMasterProduct::where('brand_name', $selectedBrand)->where('is_active', true);
        if ($selectedCategory) {
            $query->where('category_name', $selectedCategory);
        }

        $products = $query->orderBy('category_name')->orderBy('item_type')->orderBy('list_price')->get();

        // Get count of products already imported by current seller
        $importedSkus = [];
        if (Auth::check()) {
            $importedSkus = Product::where('user_id', Auth::id())
                ->whereNotNull('sku')
                ->pluck('sku')
                ->toArray();
        }

        return view('seller.brand_master.index', compact(
            'products',
            'brands',
            'categories',
            'selectedBrand',
            'selectedCategory',
            'importedSkus'
        ));
    }

    /**
     * Bulk import selected products from brand master into seller's live catalog.
     */
    public function import(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'exists:brand_master_products,id',
            'items' => 'required|array',
        ], [
            'selected_ids.required' => 'कृपया कम से कम एक प्रोडक्ट चुनें जिसे आप अपनी दुकान में जोड़ना चाहते हैं।',
        ]);

        $user = Auth::user();
        $importedCount = 0;

        foreach ($request->selected_ids as $id) {
            $master = BrandMasterProduct::find($id);
            if (!$master) continue;

            $itemData = $request->items[$id] ?? [];
            
            $purchaseDiscount = isset($itemData['purchase_discount']) ? (float)$itemData['purchase_discount'] : 45.0;
            $gstPercent = isset($itemData['gst_percent']) ? (float)$itemData['gst_percent'] : 18.0;
            $sellingPrice = isset($itemData['selling_price']) ? (float)$itemData['selling_price'] : 0;
            $purchasePrice = isset($itemData['purchase_price']) ? (float)$itemData['purchase_price'] : 0;

            // If selling price was not calculated or empty, compute default
            if ($sellingPrice <= 0) {
                // Default: List Price - 35% selling less
                $sellingPrice = $master->list_price * 0.65;
            }

            if ($purchasePrice <= 0) {
                $baseNet = $master->list_price * (1 - ($purchaseDiscount / 100));
                $purchasePrice = $baseNet * (1 + ($gstPercent / 100));
            }

            // Find or create category
            $category = Category::firstOrCreate(
                ['name' => $master->category_name],
                ['slug' => Str::slug($master->category_name) ?: ('cat-' . time())]
            );

            $sku = strtoupper($master->brand_name) . '-' . ($master->product_code ?: $master->id);
            $baseSlug = Str::slug($master->product_name);
            $slug = $baseSlug . '-' . $user->id;

            Product::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'sku' => $sku,
                ],
                [
                    'name' => $master->product_name,
                    'slug' => $slug,
                    'description' => $master->description ?: ($master->brand_name . ' ' . $master->product_name . ' - Official Standard Grade Material.'),
                    'category_id' => $category->id,
                    'price' => round($sellingPrice, 2),
                    'wholesale_price' => round($sellingPrice * 0.95, 2),
                    'purchase_price' => round($purchasePrice, 2),
                    'mrp' => $master->list_price,
                    'gst_percent' => $gstPercent,
                    'hsn_code' => $master->hsn_code,
                    'image' => $master->image_url,
                    'stock_quantity' => 100,
                    'track_inventory' => false,
                ]
            );

            $importedCount++;
        }

        return redirect()->route('seller.products.index')
            ->with('success', "बधाई हो! {$importedCount} {$request->brand_name} प्रोडक्ट्स सफलतापूर्वक आपके सेलर स्टोर में जुड़ चुके हैं।");
    }
}
