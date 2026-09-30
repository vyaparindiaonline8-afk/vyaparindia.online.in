<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\CatalogIngestionJob;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Services\AICatalogIngestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CatalogIngestionController extends Controller
{
    protected AICatalogIngestionService $ingestionService;

    public function __construct(AICatalogIngestionService $ingestionService)
    {
        $this->ingestionService = $ingestionService;
    }

    /**
     * Display the catalog brochure upload page.
     */
    public function uploadForm()
    {
        $user = Auth::user();
        $recentJobs = CatalogIngestionJob::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return view('seller.catalog.upload', compact('recentJobs'));
    }

    /**
     * Handle PDF Catalog Upload and Trigger Ingestion.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'catalog_pdf' => 'required|file|mimes:pdf|max:51200', // Up to 50MB
        ]);

        $user = Auth::user();
        $file = $request->file('catalog_pdf');
        $originalName = $file->getClientOriginalName();
        $storedPath = $file->store('catalogs/' . $user->id, 'public');

        $job = CatalogIngestionJob::create([
            'user_id' => $user->id,
            'filename' => $originalName,
            'file_path' => $storedPath,
            'status' => 'pending',
            'total_products_detected' => 0,
        ]);

        // Process PDF via AI Ingestion Service
        $job = $this->ingestionService->processCatalog($job);

        return redirect()->route('seller.catalog.review', $job->id)
            ->with('success', 'PDF Catalog successfully parsed! Review extracted items, set GST & margins before publishing.');
    }

    /**
     * Visual Review Staging Canvas.
     */
    public function review(CatalogIngestionJob $job)
    {
        if ($job->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this catalog ingestion draft.');
        }

        $categories = Category::all();
        $extractedData = $job->extracted_data ?? ['products' => []];

        return view('seller.catalog.review', compact('job', 'extractedData', 'categories'));
    }

    /**
     * Batch Publish Edited Draft into Live Products & Variants.
     */
    public function publish(Request $request, CatalogIngestionJob $job)
    {
        if ($job->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'products' => 'required|array|min:1',
            'products.*.name' => 'required|string|max:255',
            'products.*.description' => 'nullable|string',
            'products.*.category_id' => 'nullable|exists:categories,id',
            'products.*.hsn_code' => 'nullable|string|max:50',
            'products.*.image_url' => 'nullable|string',
            'products.*.gst_percent' => 'nullable|numeric|min:0|max:40',
            'products.*.trade_discount_percent' => 'nullable|numeric|min:0|max:100',
            'products.*.stock_quantity' => 'nullable|integer|min:0',
            'products.*.variants' => 'required|array|min:1',
            'products.*.variants.*.variant_name' => 'required|string|max:150',
            'products.*.variants.*.size' => 'nullable|string|max:50',
            'products.*.variants.*.grade' => 'nullable|string|max:50',
            'products.*.variants.*.raw_rate' => 'required|numeric|min:0',
            'products.*.variants.*.wholesale_price' => 'nullable|numeric|min:0',
            'products.*.variants.*.retail_price' => 'nullable|numeric|min:0',
            'products.*.variants.*.mrp' => 'nullable|numeric|min:0',
            'products.*.variants.*.stock_quantity' => 'nullable|integer|min:0',
        ]);

        $sellerId = Auth::id();
        $defaultCategory = Category::firstOrCreate(['name' => 'Industrial & Commercial'], ['slug' => 'industrial-commercial']);
        $publishedCount = 0;

        foreach ($validated['products'] as $prodData) {
            $catId = $prodData['category_id'] ?? $defaultCategory->id;
            $gst = floatval($prodData['gst_percent'] ?? 18);
            $hasMultipleVariants = count($prodData['variants']) > 1;

            // Optional stock tracking ("dale to thik na dale to thik")
            $trackStock = !is_null($prodData['stock_quantity']) && $prodData['stock_quantity'] !== '';
            $initialStock = $trackStock ? intval($prodData['stock_quantity']) : null;

            // First variant prices used as parent product baseline
            $firstVar = $prodData['variants'][0];
            $basePurchase = floatval($firstVar['raw_rate'] ?? 0);
            $baseWholesale = floatval($firstVar['wholesale_price'] ?? ($basePurchase * 1.15));
            $baseRetail = floatval($firstVar['retail_price'] ?? ($basePurchase * 1.35));
            $baseMrp = floatval($firstVar['mrp'] ?? ($basePurchase * 1.60));

            $product = Product::create([
                'user_id' => $sellerId,
                'category_id' => $catId,
                'name' => $prodData['name'],
                'slug' => Str::slug($prodData['name']) . '-' . Str::random(5),
                'description' => $prodData['description'] ?? '',
                'hsn_code' => $prodData['hsn_code'] ?? '39174000',
                'image' => $prodData['image_url'] ?? null,
                'purchase_price' => $basePurchase,
                'wholesale_price' => $baseWholesale,
                'price' => $baseRetail,
                'mrp' => $baseMrp,
                'gst_percent' => $gst,
                'stock_quantity' => $initialStock,
                'track_inventory' => $trackStock,
                'has_variants' => $hasMultipleVariants,
                'sku' => 'PRD-' . strtoupper(Str::random(6)),
            ]);

            // If product has initial stock, create initial stock movement audit
            if ($trackStock && $initialStock > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'variant_id' => null,
                    'user_id' => $sellerId,
                    'type' => 'in_restock',
                    'quantity' => $initialStock,
                    'balance_after' => $initialStock,
                    'reason' => 'Initial catalog ingestion import',
                    'reference_type' => 'catalog_ingestion',
                    'reference_id' => $job->id,
                ]);
            }

            // Create Variants
            $totalVariantStock = 0;
            $hasAnyVariantStock = false;

            foreach ($prodData['variants'] as $vIndex => $vData) {
                $vRawRate = floatval($vData['raw_rate'] ?? $basePurchase);
                $tradeDisc = floatval($prodData['trade_discount_percent'] ?? 0);
                $landingCostNet = round($vRawRate * (1 - ($tradeDisc / 100)), 2);
                $landingCostWithGst = round($landingCostNet * (1 + ($gst / 100)), 2);

                $vWholesale = !empty($vData['wholesale_price']) ? floatval($vData['wholesale_price']) : round($landingCostWithGst * 1.15, 2);
                $vRetail = !empty($vData['retail_price']) ? floatval($vData['retail_price']) : round($landingCostWithGst * 1.35, 2);
                $vMrp = !empty($vData['mrp']) ? floatval($vData['mrp']) : round($landingCostWithGst * 1.60, 2);

                $vTrackStock = !is_null($vData['stock_quantity']) && $vData['stock_quantity'] !== '';
                $vStock = $vTrackStock ? intval($vData['stock_quantity']) : null;

                if ($vTrackStock) {
                    $totalVariantStock += ($vStock ?? 0);
                    $hasAnyVariantStock = true;
                }

                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => $vData['variant_name'],
                    'sku' => 'SKU-' . $product->id . '-' . ($vIndex + 1),
                    'purchase_price' => $vRawRate,
                    'trade_discount_percent' => $tradeDisc,
                    'gst_percent' => $gst,
                    'net_landing_cost' => $landingCostWithGst,
                    'wholesale_price' => $vWholesale,
                    'retail_price' => $vRetail,
                    'mrp' => $vMrp,
                    'stock_quantity' => $vStock,
                    'track_inventory' => $vTrackStock,
                    'is_active' => true,
                    'attributes' => [
                        'size' => $vData['size'] ?? null,
                        'grade' => $vData['grade'] ?? null,
                    ],
                ]);

                if ($vTrackStock && $vStock > 0) {
                    StockMovement::create([
                        'product_id' => $product->id,
                        'variant_id' => $variant->id,
                        'user_id' => $sellerId,
                        'type' => 'in_restock',
                        'quantity' => $vStock,
                        'balance_after' => $vStock,
                        'reason' => 'Initial catalog ingestion import',
                        'reference_type' => 'catalog_ingestion',
                        'reference_id' => $job->id,
                    ]);
                }
            }

            // If variants had stock specified, sync aggregate stock to parent
            if ($hasAnyVariantStock) {
                $product->update([
                    'stock_quantity' => $totalVariantStock,
                    'track_inventory' => true,
                ]);
            }

            $publishedCount++;
        }

        $job->update([
            'status' => 'published',
            'extracted_data' => $validated,
        ]);

        return redirect()->route('seller.inventory.index')
            ->with('success', "🎉 Successfully published {$publishedCount} products with dynamic variants into your live catalog!");
    }

    /**
     * Unified Inventory & Stock Management View.
     */
    public function inventory()
    {
        $sellerId = Auth::id();
        $products = Product::where('user_id', $sellerId)
            ->with(['variants', 'stockMovements' => function ($q) {
                $q->latest()->take(5);
            }])
            ->latest()
            ->paginate(15);

        $totalCatalogItems = Product::where('user_id', $sellerId)->count();
        $lowStockCount = Product::where('user_id', $sellerId)
            ->where('track_inventory', true)
            ->where('stock_quantity', '<=', 5)
            ->count();
        $outOfStockCount = Product::where('user_id', $sellerId)
            ->where('track_inventory', true)
            ->where('stock_quantity', '<=', 0)
            ->count();

        return view('seller.inventory.index', compact('products', 'totalCatalogItems', 'lowStockCount', 'outOfStockCount'));
    }

    /**
     * 1-Click Quick Restock Endpoint.
     */
    public function restock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:100',
        ]);

        $product = Product::where('id', $request->product_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $qty = intval($request->quantity);
        $reason = $request->reason ?? 'Manual Seller Restock';
        $variantId = $request->variant_id ? intval($request->variant_id) : null;

        $newStock = $product->addStock($qty, $reason, 'manual_restock', null, $variantId);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Successfully added +{$qty} units to inventory!",
                'new_stock' => $newStock,
            ]);
        }

        return back()->with('success', "Successfully restocked +{$qty} units (Current balance: {$newStock})!");
    }
}
