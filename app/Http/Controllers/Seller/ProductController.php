<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\CloudinaryService;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = $user->products()->with(['category', 'variants']);

        $activeFolder = $request->query('folder');
        $activeCategory = $request->query('category');
        $search = trim((string)$request->query('search'));
        $hasGroupCol = \Illuminate\Support\Facades\Schema::hasColumn('products', 'group_name');

        // 🛡️ SMART CONFLICT RESOLUTION:
        // If both folder and category were supplied in URL (e.g. from previous filter state),
        // check if ANY product actually belongs to BOTH. If none match both, the user clicked
        // a category while browsing or vice-versa. Do NOT let the stale folder zero-out the category results!
        if ($hasGroupCol && $activeFolder && $activeFolder !== 'all' && $activeCategory && $activeCategory !== 'all') {
            $folderTerm = strtolower(trim($activeFolder));
            $catTerm = strtolower(trim($activeCategory));
            $cleanTerm = str_replace('-', ' ', $catTerm);

            $bothMatchCount = (clone $query)->whereRaw('LOWER(group_name) = ?', [$folderTerm])
                ->where(function($q) use ($catTerm, $cleanTerm) {
                    $q->whereHas('category', function($cq) use ($catTerm, $cleanTerm) {
                        $cq->whereRaw('LOWER(slug) = ?', [$catTerm])
                           ->orWhereRaw('LOWER(name) = ?', [$catTerm])
                           ->orWhereRaw('LOWER(name) = ?', [$cleanTerm])
                           ->orWhereRaw('LOWER(slug) LIKE ?', [$catTerm . '%']);
                    })
                    ->orWhereRaw('LOWER(group_name) = ?', [$catTerm])
                    ->orWhereRaw('LOWER(group_name) = ?', [$cleanTerm])
                    ->orWhereRaw("LOWER(REPLACE(group_name, ' ', '-')) = ?", [$catTerm]);
                })->count();

            if ($bothMatchCount === 0) {
                // Category was clicked or requested, clear conflicting folder so products show up!
                $activeFolder = null;
            }
        }

        if ($hasGroupCol && $activeFolder && $activeFolder !== 'all') {
            $folderTerm = strtolower(trim($activeFolder));
            $query->whereRaw('LOWER(group_name) = ?', [$folderTerm]);
        }

        if ($activeCategory && $activeCategory !== 'all') {
            $catTerm = strtolower(trim($activeCategory));
            $cleanTerm = str_replace('-', ' ', $catTerm);
            $query->where(function($q) use ($catTerm, $cleanTerm) {
                $q->whereHas('category', function($cq) use ($catTerm, $cleanTerm) {
                    $cq->whereRaw('LOWER(slug) = ?', [$catTerm])
                       ->orWhereRaw('LOWER(name) = ?', [$catTerm])
                       ->orWhereRaw('LOWER(name) = ?', [$cleanTerm])
                       ->orWhereRaw('LOWER(slug) LIKE ?', [$catTerm . '%']);
                })
                ->orWhereRaw('LOWER(group_name) = ?', [$catTerm])
                ->orWhereRaw('LOWER(group_name) = ?', [$cleanTerm])
                ->orWhereRaw("LOWER(REPLACE(group_name, ' ', '-')) = ?", [$catTerm]);
            });
        }

        if (!empty($search)) {
            $term = '%' . strtolower($search) . '%';
            $query->where(function($q) use ($term) {
                $q->whereRaw('LOWER(name) LIKE ?', [$term])
                  ->orWhereRaw('LOWER(sku) LIKE ?', [$term])
                  ->orWhereHas('variants', function($vq) use ($term) {
                      $vq->whereRaw('LOWER(variant_name) LIKE ?', [$term])
                         ->orWhereRaw('LOWER(sku) LIKE ?', [$term]);
                  });
            });
        }

        $allSellerProducts = $user->products()->with(['category', 'variants'])->get();
        $productCardsCount = $allSellerProducts->count();
        $totalVariantsCount = $allSellerProducts->sum(function($p) {
            return ($p->variants && $p->variants->count() > 0) ? $p->variants->count() : 1;
        });

        $products = $query->latest()->get();

        $folders = collect();
        if ($hasGroupCol) {
            $folders = $allSellerProducts
                ->whereNotNull('group_name')
                ->where('group_name', '!=', '')
                ->pluck('group_name')
                ->unique()
                ->values();
        }

        // 🏷️ Build accurate, complete category tabs from both category relation AND product group_name
        $categoriesMap = [];
        foreach ($allSellerProducts as $prod) {
            $catId = null;
            $catName = null;

            if ($prod->category) {
                $catId = $prod->category->id;
                $catName = trim($prod->category->name);
            } elseif (!empty($prod->group_name)) {
                $catId = 'grp_' . \Illuminate\Support\Str::slug($prod->group_name);
                $catName = trim($prod->group_name);
            }

            if (!empty($catName)) {
                // Group by normalized slug so "Flush Doors", "Flush Doors ", and case variations collapse into one unified tab!
                $normKey = strtolower(\Illuminate\Support\Str::slug($catName));
                if (!isset($categoriesMap[$normKey])) {
                    $categoriesMap[$normKey] = (object)[
                        'id' => $catId,
                        'name' => ucwords(strtolower($catName)),
                        'slug' => $normKey,
                        'products_count' => 0
                    ];
                }
                $categoriesMap[$normKey]->products_count++;
            }
        }

        // Exclude empty categories (count = 0) and sort alphabetically
        $categories = collect(array_values($categoriesMap))
            ->where('products_count', '>', 0)
            ->sortBy('name')
            ->values();

        $galleryImages = CatalogIngestionController::getAllGalleryImages($user->id);

        return view('seller.products.index', compact(
            'products', 'folders', 'categories', 'activeFolder', 'activeCategory', 'search',
            'productCardsCount', 'totalVariantsCount', 'galleryImages'
        ));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->canAddProduct()) {
            return redirect()->route('seller.products.index')
                ->with('error', 'Aapka 100 products ka free promotional quota poora ho chuka hai. Unlimited items add karne ke liye Premium Plan me upgrade karein.');
        }

        $categories = Category::all();
        return view('seller.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->canAddProduct()) {
            return redirect()->route('seller.products.index')
                ->with('error', 'Aapka 100 products ka free promotional quota poora ho chuka hai. Unlimited items add karne ke liye Premium Plan me upgrade karein.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'new_category_name' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'video_url' => 'nullable|url|max:500',
        ]);

        $categoryId = $request->category_id;
        if ($request->filled('new_category_name')) {
            $catName = trim($request->new_category_name);
            $cat = Category::firstOrCreate(
                ['name' => ucwords($catName)],
                ['slug' => Str::slug($catName) ?: ('cat-' . time())]
            );
            $categoryId = $cat->id;
        }

        if (!$categoryId) {
            return back()->withErrors(['category_id' => 'Please select an existing category or enter a new category name.'])->withInput();
        }

        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = CloudinaryService::upload($request->file('image'), 'vyaparindia/products');
        }

        $product = Auth::user()->products()->create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) ?: ('prod-' . time()),
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $categoryId,
            'image' => $imageName,
            'video_url' => $request->video_url,
        ]);

        // 🖼️ Multiple Gallery Images (up to 4-5 images)
        if ($request->hasFile('images')) {
            $sort = 1;
            foreach ($request->file('images') as $extraFile) {
                if ($extraFile) {
                    $uploadedPath = CloudinaryService::upload($extraFile, 'vyaparindia/products');
                    $product->images()->create([
                        'image_path' => $uploadedPath,
                        'is_primary' => false,
                        'sort_order' => $sort++,
                    ]);
                }
            }
        }

        return redirect()->route('seller.products.index')->with('success', 'Product created successfully with gallery & video.');
    }

    public function edit(Product $product)
    {
        if (Auth::id() !== $product->user_id) {
            abort(403);
        }

        $product->load('images');
        $categories = Category::all();
        $galleryImages = CatalogIngestionController::getAllGalleryImages(Auth::id());

        return view('seller.products.edit', compact('product', 'categories', 'galleryImages'));
    }

    public function update(Request $request, Product $product)
    {
        if (Auth::id() !== $product->user_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'new_category_name' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'existing_image_url' => 'nullable|string|max:1000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'video_url' => 'nullable|url|max:500',
        ]);

        $categoryId = $request->category_id;
        if ($request->filled('new_category_name')) {
            $catName = trim($request->new_category_name);
            $cat = Category::firstOrCreate(
                ['name' => ucwords($catName)],
                ['slug' => Str::slug($catName) ?: ('cat-' . time())]
            );
            $categoryId = $cat->id;
        }

        if (!$categoryId) {
            $categoryId = $product->category_id;
        }

        $imageName = $product->image;
        if ($request->hasFile('image')) {
            $imageName = CloudinaryService::upload($request->file('image'), 'vyaparindia/products');
        } elseif ($request->filled('existing_image_url')) {
            $imageName = $request->input('existing_image_url');
        }

        $product->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name) ?: $product->slug,
            'description' => $request->description,
            'price' => $request->price,
            'brand' => $request->input('brand', $product->brand),
            'group_name' => $request->input('group_name', $product->group_name),
            'category_id' => $categoryId,
            'image' => $imageName,
            'video_url' => $request->video_url,
        ]);

        // 🖼️ Upload New Additional Gallery Images
        if ($request->hasFile('images')) {
            $nextSort = ($product->images()->max('sort_order') ?? 0) + 1;
            foreach ($request->file('images') as $extraFile) {
                if ($extraFile) {
                    $uploadedPath = CloudinaryService::upload($extraFile, 'vyaparindia/products');
                    $product->images()->create([
                        'image_path' => $uploadedPath,
                        'is_primary' => false,
                        'sort_order' => $nextSort++,
                    ]);
                }
            }
        }

        // 🗑️ Delete gallery image if requested
        if ($request->filled('delete_image_id')) {
            $product->images()->where('id', $request->delete_image_id)->delete();
        }

        return redirect()->route('seller.products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * 1-Click Instant Photo Swapper / Image Updater from Product Cards
     */
    public function quickImageUpdate(Request $request, Product $product)
    {
        if (Auth::id() !== $product->user_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'image_url' => 'nullable|string',
            'image_file' => 'nullable|image|max:4096',
        ]);

        $imageUrl = $request->input('image_url');
        if ($request->hasFile('image_file')) {
            $imageUrl = CloudinaryService::upload($request->file('image_file'), 'vyaparindia/products');
        }

        if (empty($imageUrl)) {
            return response()->json(['success' => false, 'message' => 'No image provided'], 422);
        }

        $product->update(['image' => $imageUrl]);

        return response()->json([
            'success' => true,
            'message' => 'Product image updated successfully!',
            'image_url' => $product->image_url,
        ]);
    }

    public function deleteImage(Product $product, ProductImage $image)
    {
        if (Auth::id() !== $product->user_id || $image->product_id !== $product->id) {
            abort(403);
        }

        $image->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Image removed from gallery.');
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            if (request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Product pehle hi delete ho chuka hai.']);
            }
            return redirect()->route('seller.products.index')->with('warning', 'Product pehle hi hat chuka hai.');
        }

        if (Auth::id() !== $product->user_id && !Auth::user()->isOwnerOrAdmin()) {
            abort(403);
        }

        // Clean up variants and images first to avoid database FK constraints
        $product->variants()->delete();
        $product->images()->delete();
        $product->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Product deleted successfully.']);
        }

        return redirect()->route('seller.products.index')->with('success', 'Product deleted successfully.');
    }

    /**
     * Clear all catalog products and variants for current seller.
     * Allows a completely fresh sync with zero duplicates.
     */
    public function clearAll(Request $request)
    {
        $user = Auth::user();
        $products = $user->products()->with(['variants', 'images'])->get();
        $count = $products->count();

        foreach ($products as $p) {
            $p->variants()->delete();
            $p->images()->delete();
            $p->delete();
        }

        return redirect()->route('seller.products.index')
            ->with('success', "आपके सेलर स्टोर के सभी {$count} पुराने प्रोडक्ट कार्ड्स व वेरिएंट्स सफलतापूर्वक साफ़ (Clear) कर दिए गए हैं। अब आप अपनी नई फाइनल एक्सेल शीट पूरी तरह फ्रेश अपलोड कर सकते हैं!");
    }
}
