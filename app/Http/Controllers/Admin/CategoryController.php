<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function merge(Request $request)
    {
        $request->validate([
            'source_category_id' => 'required|exists:categories,id',
            'target_category_id' => 'required|exists:categories,id|different:source_category_id',
        ]);

        $source = Category::findOrFail($request->source_category_id);
        $target = Category::findOrFail($request->target_category_id);

        $sourceName = $source->name;
        $targetName = $target->name;

        // Reassign products to target category
        $updatedCount = \App\Models\Product::where('category_id', $source->id)->update([
            'category_id' => $target->id
        ]);

        // If many-to-many pivot exists, update pivot too
        if (\Illuminate\Support\Facades\Schema::hasTable('category_product')) {
            \Illuminate\Support\Facades\DB::table('category_product')
                ->where('category_id', $source->id)
                ->update(['category_id' => $target->id]);
        }

        // Delete the merged duplicate category
        $source->delete();

        return redirect()->route('admin.categories.index')->with(
            'success',
            "Category '{$sourceName}' successfully merged into '{$targetName}'. ({$updatedCount} products reassigned)"
        );
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
