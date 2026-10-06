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
        $hasGroupCol = \Illuminate\Support\Facades\Schema::hasColumn('products', 'group_name');

        if ($hasGroupCol && $activeFolder && $activeFolder !== 'all') {
            $query->where('group_name', $activeFolder);
        }

        $products = $query->latest()->get();

        $folders = collect();
        if ($hasGroupCol) {
            $folders = $user->products()
                ->whereNotNull('group_name')
                ->where('group_name', '!=', '')
                ->distinct()
                ->pluck('group_name');
        }

        return view('seller.products.index', compact('products', 'folders', 'activeFolder'));
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
        return view('seller.products.edit', compact('product', 'categories'));
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

    public function destroy(Product $product)
    {
        if (Auth::id() !== $product->user_id) {
            abort(403);
        }

        $product->delete();

        return redirect()->route('seller.products.index')->with('success', 'Product deleted successfully.');
    }
}
