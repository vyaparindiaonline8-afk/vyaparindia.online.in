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
    public function index()
    {
        $products = Auth::user()->products;
        return view('seller.products.index', compact('products'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->canAddProduct()) {
            return redirect()->route('seller.products.index')
                ->with('error', 'Aapka Basic Profile plan hai jisme maximum 50 products hi allow hain. Unlimited products ke liye Mini-Website ya Dropshipping me upgrade karein.');
        }

        $categories = Category::all();
        return view('seller.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->canAddProduct()) {
            return redirect()->route('seller.products.index')
                ->with('error', 'Aapka 50 products ka quota poora ho chuka hai. Unlimited items list karne ke liye Mini-Website activate karein.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'new_category_name' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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

        Auth::user()->products()->create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $categoryId,
            'image' => $imageName,
        ]);

        return redirect()->route('seller.products.index')->with('success', 'Product created successfully with category.');
    }

    public function edit(Product $product)
    {
        if (Auth::id() !== $product->user_id) {
            abort(403);
        }

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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $categoryId,
            'image' => $imageName,
        ]);

        return redirect()->route('seller.products.index')->with('success', 'Product updated successfully.');
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
