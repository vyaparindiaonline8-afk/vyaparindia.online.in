<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SellerMedia;
use App\Services\AIProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PhotoStudioController extends Controller
{
    /**
     * Display the Photo Studio & Bulk Media Canvas
     */
    public function index()
    {
        $sellerId = Auth::id();
        $mediaItems = SellerMedia::where('user_id', $sellerId)
            ->latest()
            ->get();

        $categories = Category::all();
        $totalUploaded = $mediaItems->count();
        $unassignedCount = $mediaItems->where('is_assigned', false)->count();

        return view('seller.studio.index', compact('mediaItems', 'categories', 'totalUploaded', 'unassignedCount'));
    }

    /**
     * Batch Upload 20-30 Photos into Seller Media Gallery
     */
    public function uploadMedia(Request $request)
    {
        $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $sellerId = Auth::id();
        $savedMedia = [];

        foreach ($request->file('photos') as $file) {
            $filename = $file->getClientOriginalName();
            $path = $file->store('seller_media/' . $sellerId, 'public');

            $media = SellerMedia::create([
                'user_id' => $sellerId,
                'filename' => $filename,
                'file_path' => 'storage/' . $path,
                'is_assigned' => false,
            ]);

            $savedMedia[] = [
                'id' => $media->id,
                'filename' => $media->filename,
                'url' => asset($media->file_path),
            ];
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => count($savedMedia) . ' photos uploaded to your Studio Gallery!',
                'media' => $savedMedia,
            ]);
        }

        return back()->with('success', count($savedMedia) . ' photos uploaded to your Studio Gallery!');
    }

    /**
     * Quick-Publish Product from 1 or 2-3 Selected Gallery Photos
     */
    public function publishProduct(Request $request, AIProductService $aiService)
    {
        $user = Auth::user();

        // Enforce 50-product quota for Profile-Only sellers
        if (!$user->canAddProduct()) {
            return response()->json([
                'success' => false,
                'message' => 'Quota reached (50/50). Upgrade to Mini-Website for unlimited product listings.',
            ], 403);
        }

        $validated = $request->validate([
            'media_ids' => 'required|array|min:1',
            'media_ids.*' => 'required|exists:seller_media,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'mrp' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'nullable|exists:categories,id',
            'new_category' => 'nullable|string|max:100',
            'hsn_code' => 'nullable|string|max:50',
        ]);

        // 1. Process Custom Typed Category (Creates in DB so it becomes searchable for everyone!)
        $categoryIds = $validated['category_ids'] ?? [];
        if (!empty($validated['new_category'])) {
            $newCatName = trim($validated['new_category']);
            if (strlen($newCatName) >= 2) {
                $customCategory = Category::firstOrCreate(
                    ['name' => ucwords($newCatName)],
                    ['slug' => Str::slug($newCatName)]
                );
                $categoryIds[] = $customCategory->id;
            }
        }

        $categoryIds = array_unique($categoryIds);
        $primaryCategoryId = !empty($categoryIds) ? $categoryIds[0] : (Category::first()->id ?? 1);

        // 2. Fetch Selected Media Items
        $selectedMedia = SellerMedia::where('user_id', $user->id)
            ->whereIn('id', $validated['media_ids'])
            ->get();

        if ($selectedMedia->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No valid media selected.'], 400);
        }

        $mainMedia = $selectedMedia->first();
        $slug = Str::slug($validated['name']) . '-' . Str::random(5);

        // 3. Create Product
        $product = Product::create([
            'user_id' => $user->id,
            'category_id' => $primaryCategoryId,
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? '',
            'price' => floatval($validated['price']),
            'mrp' => !empty($validated['mrp']) ? floatval($validated['mrp']) : round(floatval($validated['price']) * 1.35, 2),
            'stock_quantity' => !is_null($validated['stock_quantity']) && $validated['stock_quantity'] !== '' ? intval($validated['stock_quantity']) : null,
            'track_inventory' => !is_null($validated['stock_quantity']) && $validated['stock_quantity'] !== '',
            'image' => $mainMedia->file_path,
            'hsn_code' => $validated['hsn_code'] ?? '85176290',
            'sku' => 'PRD-' . strtoupper(Str::random(6)),
        ]);

        // 4. Attach Multi-Categories to Pivot Table
        if (!empty($categoryIds)) {
            $product->categories()->sync($categoryIds);
        }

        // 5. Attach Multiple Angles / Images to Product Images
        foreach ($selectedMedia as $idx => $media) {
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $media->file_path,
                'is_primary' => ($idx === 0),
                'sort_order' => $idx,
            ]);

            // Mark media as assigned
            $media->update([
                'is_assigned' => true,
                'product_id' => $product->id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "🎉 Product '{$product->name}' published live with " . count($selectedMedia) . " photo(s)!",
            'product_id' => $product->id,
            'assigned_media_ids' => $selectedMedia->pluck('id')->toArray(),
        ]);
    }

    /**
     * AI Assistant endpoint to auto-generate title & description from keyword/category
     */
    public function aiAssist(Request $request, AIProductService $aiService)
    {
        $prompt = $request->input('prompt', 'Hardware Product');
        $details = $aiService->generateProductDetails($prompt);

        return response()->json([
            'success' => true,
            'title' => $details['enhanced_title'],
            'description' => $details['description'],
            'hsn_code' => $details['hsn_code'],
            'suggested_price' => $details['suggested_price'],
            'suggested_mrp' => $details['suggested_mrp'],
        ]);
    }
}
