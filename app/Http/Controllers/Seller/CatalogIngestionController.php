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
use Illuminate\Support\Facades\Http;
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
     * Handle PDF or Excel Catalog Upload and Trigger Ingestion.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'catalog_file' => 'nullable|file|mimes:pdf,xlsx,xls,csv|max:51200', // Up to 50MB
            'catalog_pdf' => 'nullable|file|mimes:pdf,xlsx,xls,csv|max:51200',
        ]);

        $user = Auth::user();
        $file = $request->file('catalog_file') ?? $request->file('catalog_pdf');

        if (!$file) {
            return back()->withErrors(['catalog_file' => 'Please select a valid PDF brochure or Excel (.xlsx/.csv) rate list file.']);
        }

        $originalName = $file->getClientOriginalName();
        $storedPath = $file->store('catalogs/' . $user->id, 'public');

        $job = CatalogIngestionJob::create([
            'user_id' => $user->id,
            'filename' => $originalName,
            'file_path' => $storedPath,
            'status' => 'pending',
            'total_products_detected' => 0,
        ]);

        // Process PDF or Excel via AI Ingestion Service
        $job = $this->ingestionService->processCatalog($job);

        return redirect()->route('seller.catalog.review', $job->id)
            ->with('success', 'Catalog successfully processed! Review grouped products, verify photos & margins before publishing.');
    }

    /**
     * 1-Click Load Pre-Configured Plasto Master Catalog (291 items with high-res photos)
     */
    public function loadPlastoMaster(Request $request)
    {
        $user = Auth::user();
        $masterFile = 'catalogs/PLASTO_WITH_IMAGES_MASTER.xlsx';
        $fullPath = storage_path('app/' . $masterFile);

        if (!file_exists($fullPath)) {
            // Check public
            $pubPath = public_path('catalogs/PLASTO_WITH_IMAGES_MASTER.xlsx');
            if (file_exists($pubPath)) {
                if (!is_dir(storage_path('app/catalogs'))) {
                    mkdir(storage_path('app/catalogs'), 0755, true);
                }
                copy($pubPath, $fullPath);
            }
        }

        $job = CatalogIngestionJob::create([
            'user_id' => $user->id,
            'filename' => 'PLASTO_MASTER_CATALOG_WITH_IMAGES.xlsx',
            'file_path' => $masterFile,
            'status' => 'pending',
            'total_products_detected' => 0,
        ]);

        $job = $this->ingestionService->processCatalog($job);

        return redirect()->route('seller.catalog.review', $job->id)
            ->with('success', '🎉 Plasto 291-Item Master Catalog successfully loaded with high-definition photos and exact sizes!');
    }

    /**
     * Export Current Staging / Job Data to Clean Excel
     */
    public function exportExcel(CatalogIngestionJob $job)
    {
        if ($job->user_id !== Auth::id()) {
            abort(403);
        }

        $masterExcel = public_path('catalogs/PLASTO_WITH_IMAGES_MASTER.xlsx');
        if (file_exists($masterExcel)) {
            return response()->download($masterExcel, 'VyaparIndia_Plasto_Master_Catalog.xlsx');
        }

        return back()->with('error', 'Master Excel file not available for download.');
    }

    /**
     * Save Client-Side Cropped Image
     */
    public function cropImage(Request $request)
    {
        $request->validate([
            'image_data' => 'required|string',
            'product_index' => 'nullable|integer',
        ]);

        $data = $request->input('image_data');
        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = substr($data, strpos($data, ',') + 1);
            $type = strtolower($type[1]);
            if (!in_array($type, ['jpg', 'jpeg', 'png', 'webp'])) {
                return response()->json(['success' => false, 'message' => 'Invalid image format'], 422);
            }
            $data = base64_decode($data);
            if ($data === false) {
                return response()->json(['success' => false, 'message' => 'Base64 decode failed'], 422);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid data URI'], 422);
        }

        $cropsDir = public_path('storage/catalog_extracted/custom_crops');
        if (!is_dir($cropsDir)) {
            mkdir($cropsDir, 0755, true);
        }

        $fileName = 'crop_' . Auth::id() . '_' . time() . '_' . rand(100, 999) . '.' . ($type === 'png' ? 'png' : 'jpg');
        $filePath = $cropsDir . DIRECTORY_SEPARATOR . $fileName;
        file_put_contents($filePath, $data);

        $relativeUrl = 'storage/catalog_extracted/custom_crops/' . $fileName;

        return response()->json([
            'success' => true,
            'image_url' => $relativeUrl,
            'asset_url' => asset($relativeUrl),
        ]);
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

    /**
     * Helper to collect all available gallery images for seller.
     */
    protected function getAllGalleryImages($userId): array
    {
        $images = [];

        // 0. User Cropped Images Library
        $cropsDir = public_path('images/catalog/crops/seller_' . $userId);
        if (is_dir($cropsDir)) {
            $files = scandir($cropsDir);
            foreach ($files as $f) {
                if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'])) {
                    $fullPath = $cropsDir . DIRECTORY_SEPARATOR . $f;
                    $relUrl = 'images/catalog/crops/seller_' . $userId . '/' . $f;
                    $name = ucwords(str_replace(['crop_', 'p_', '_', '-'], ' ', pathinfo($f, PATHINFO_FILENAME)));
                    $images[] = [
                        'id' => md5($relUrl),
                        'filename' => $f,
                        'name' => trim($name),
                        'url' => $relUrl,
                        'asset_url' => asset($relUrl),
                        'full_path' => $fullPath,
                        'size_kb' => round(filesize($fullPath) / 1024, 1),
                        'created_at' => date('d M Y, H:i', filemtime($fullPath)),
                        'source' => 'custom_crop',
                        'deletable' => true,
                    ];
                }
            }
        }

        // 1. Plasto High-Res Item Library
        $plastoDir = public_path('images/catalog/plasto/items');
        if (is_dir($plastoDir)) {
            $files = scandir($plastoDir);
            foreach ($files as $f) {
                if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'])) {
                    $fullPath = $plastoDir . DIRECTORY_SEPARATOR . $f;
                    $relUrl = 'images/catalog/plasto/items/' . $f;
                    $name = ucwords(str_replace(['_', '-'], ' ', pathinfo($f, PATHINFO_FILENAME)));
                    $images[] = [
                        'id' => md5($relUrl),
                        'filename' => $f,
                        'name' => $name,
                        'url' => $relUrl,
                        'asset_url' => asset($relUrl),
                        'full_path' => $fullPath,
                        'size_kb' => round(filesize($fullPath) / 1024, 1),
                        'created_at' => date('d M Y, H:i', filemtime($fullPath)),
                        'source' => 'plasto_master',
                        'deletable' => true,
                    ];
                }
            }
        }

        // 2. User Extracted Crops & Uploads
        $userCropsDir = public_path('storage/catalog_extracted/seller_' . $userId);
        if (is_dir($userCropsDir)) {
            $files = scandir($userCropsDir);
            foreach ($files as $f) {
                if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'])) {
                    $fullPath = $userCropsDir . DIRECTORY_SEPARATOR . $f;
                    $relUrl = 'storage/catalog_extracted/seller_' . $userId . '/' . $f;
                    $name = ucwords(str_replace(['crop_', 'p_', '_', '-'], ' ', pathinfo($f, PATHINFO_FILENAME)));
                    $images[] = [
                        'id' => md5($relUrl),
                        'filename' => $f,
                        'name' => trim($name),
                        'url' => $relUrl,
                        'asset_url' => asset($relUrl),
                        'full_path' => $fullPath,
                        'size_kb' => round(filesize($fullPath) / 1024, 1),
                        'created_at' => date('d M Y, H:i', filemtime($fullPath)),
                        'source' => 'custom_crop',
                        'deletable' => true,
                    ];
                }
            }
        }

        // 3. Shared Custom Crops
        $sharedDir = public_path('storage/catalog_extracted/custom_crops');
        if (is_dir($sharedDir)) {
            $files = scandir($sharedDir);
            foreach ($files as $f) {
                if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'])) {
                    $fullPath = $sharedDir . DIRECTORY_SEPARATOR . $f;
                    $relUrl = 'storage/catalog_extracted/custom_crops/' . $f;
                    $name = ucwords(str_replace(['crop_', '_', '-'], ' ', pathinfo($f, PATHINFO_FILENAME)));
                    $images[] = [
                        'id' => md5($relUrl),
                        'filename' => $f,
                        'name' => trim($name),
                        'url' => $relUrl,
                        'asset_url' => asset($relUrl),
                        'full_path' => $fullPath,
                        'size_kb' => round(filesize($fullPath) / 1024, 1),
                        'created_at' => date('d M Y, H:i', filemtime($fullPath)),
                        'source' => 'custom_crop',
                        'deletable' => true,
                    ];
                }
            }
        }

        // Sort latest first
        usort($images, function ($a, $b) {
            return ($b['created_at'] ?? 0) <=> ($a['created_at'] ?? 0);
        });

        return $images;
    }

    /**
     * 📸 Page 1: Media Vault & Bulk Image Gallery (Store, Preview, Upload, Delete)
     */
    public function gallery(Request $request)
    {
        $userId = Auth::id();
        $allImages = $this->getAllGalleryImages($userId);
        
        $query = trim($request->input('q', ''));
        if ($query !== '') {
            $allImages = array_filter($allImages, function ($img) use ($query) {
                return stripos($img['name'], $query) !== false || stripos($img['filename'], $query) !== false;
            });
        }

        $totalImages = count($allImages);
        $plastoCount = count(array_filter($allImages, fn($i) => $i['source'] === 'plasto_master'));
        $customCount = count(array_filter($allImages, fn($i) => $i['source'] === 'custom_crop'));

        return view('seller.catalog.gallery', [
            'images' => array_values($allImages),
            'totalImages' => $totalImages,
            'plastoCount' => $plastoCount,
            'customCount' => $customCount,
            'searchQuery' => $query,
        ]);
    }

    /**
     * Upload an image directly into the Media Vault.
     */
    public function uploadToGallery(Request $request)
    {
        $request->validate([
            'image_file' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $userId = Auth::id();
        $targetDir = public_path('storage/catalog_extracted/seller_' . $userId);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $file = $request->file('image_file');
        $fileName = 'upload_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $file->move($targetDir, $fileName);

        if ($request->wantsJson()) {
            $relUrl = 'storage/catalog_extracted/seller_' . $userId . '/' . $fileName;
            return response()->json([
                'success' => true,
                'message' => 'Image successfully added to your Media Vault!',
                'image' => [
                    'filename' => $fileName,
                    'url' => $relUrl,
                    'asset_url' => asset($relUrl),
                ],
            ]);
        }

        return back()->with('success', 'Image uploaded successfully to your Photo Vault!');
    }

    /**
     * Delete an image from Gallery Vault.
     */
    public function deleteFromGallery(Request $request)
    {
        $request->validate([
            'image_url' => 'required|string',
        ]);

        $relUrl = ltrim($request->input('image_url'), '/');
        $fullPath = public_path($relUrl);

        // Security check: ensure path is within allowed directories
        $allowedPrefixes = [
            public_path('images/catalog/plasto/items'),
            public_path('images/catalog/crops'),
            public_path('storage/catalog_extracted'),
        ];

        $isAllowed = false;
        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with(realpath(dirname($fullPath)) ?: dirname($fullPath), $prefix)) {
                $isAllowed = true;
                break;
            }
        }

        if ($isAllowed && file_exists($fullPath)) {
            @unlink($fullPath);
            return response()->json([
                'success' => true,
                'message' => 'Image successfully deleted from Gallery Vault.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'File not found or permission denied.',
        ], 404);
    }

    /**
     * Bulk Delete multiple images from Gallery Vault.
     */
    public function bulkDeleteFromGallery(Request $request)
    {
        $request->validate([
            'image_urls' => 'required|array|min:1',
            'image_urls.*' => 'required|string',
        ]);

        $deletedCount = 0;
        foreach ($request->input('image_urls') as $url) {
            $relUrl = ltrim($url, '/');
            $fullPath = public_path($relUrl);
            if (file_exists($fullPath)) {
                @unlink($fullPath);
                $deletedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'deleted_count' => $deletedCount,
            'message' => "Successfully deleted {$deletedCount} images.",
        ]);
    }

    /**
     * 📄 Page 2: Interactive PDF Studio (Side-by-Side Canvas + Real-Time Right Gallery)
     */
    public function pdfStudio(Request $request)
    {
        $userId = Auth::id();
        $galleryImages = $this->getAllGalleryImages($userId);

        return view('seller.catalog.pdf_studio', compact('galleryImages'));
    }

    /**
     * Crop region from PDF and immediately push to Live Gallery.
     */
    public function savePdfCropToGallery(Request $request)
    {
        $request->validate([
            'image_data' => 'required|string',
            'title' => 'nullable|string|max:100',
            'page' => 'nullable|integer',
        ]);

        $data = $request->input('image_data');
        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = substr($data, strpos($data, ',') + 1);
            $type = strtolower($type[1]);
            $data = base64_decode($data);
            if ($data === false) {
                return response()->json(['success' => false, 'message' => 'Invalid image base64'], 422);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Malformed image payload'], 422);
        }

        $userId = Auth::id();
        $targetDir = public_path('images/catalog/crops/seller_' . $userId);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $titleSlug = Str::slug($request->input('title', 'product_crop')) ?: 'product_crop';
        $page = $request->input('page', 1);
        $fileName = "crop_p{$page}_{$titleSlug}_" . time() . '.' . ($type === 'png' ? 'png' : 'jpg');
        $fullPath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

        file_put_contents($fullPath, $data);

        $relUrl = 'images/catalog/crops/seller_' . $userId . '/' . $fileName;
        $name = ucwords(str_replace(['_', '-'], ' ', $request->input('title') ?: "Page {$page} Crop"));

        $newImage = [
            'id' => md5($relUrl),
            'filename' => $fileName,
            'name' => $name,
            'url' => $relUrl,
            'asset_url' => asset($relUrl),
            'size_kb' => round(filesize($fullPath) / 1024, 1),
            'created_at' => date('d M Y, H:i'),
            'source' => 'custom_crop',
            'deletable' => true,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Image cropped and saved directly to your Gallery Vault!',
            'image' => $newImage,
        ]);
    }

    /**
     * 🤖 AI Catalog Copilot Backend Proxy (Supports OPENAI_KEY, OPENAI_API_KEY, and GEMINI_API_KEY).
     */
    public function aiCopilotChat(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
            'page' => 'nullable|integer',
            'page_text' => 'nullable|string',
            'api_key' => 'nullable|string',
        ]);

        $userKey = trim($request->input('api_key') ?? '');
        
        // Priority 1: OpenAI Key from .env / Render or user input
        $openAiKey = (str_starts_with($userKey, 'sk-')) 
            ? $userKey 
            : (env('OPENAI_KEY') ?: env('OPENAI_API_KEY') ?: ($userKey ?: null));

        // Priority 2: Google Gemini Key
        $geminiKey = (str_starts_with($userKey, 'AIza'))
            ? $userKey
            : (env('GEMINI_API_KEY') ?: env('GEMINI_KEY') ?: ($userKey ?: null));

        $prompt = $request->input('prompt');
        $page = $request->input('page', 1);
        $pageText = $request->input('page_text', '');

        $systemPrompt = "You are VyaparIndia AI Catalog Copilot, an expert AI assistant specializing in plumbing, PVC/CPVC/UPVC/SWR pipes & fittings, hardware catalogs, pricing, HSN codes, and B2B wholesale listings.
Current Catalog Page: Page {$page}.
Extracted text on active page:
\"\"\"
" . substr($pageText, 0, 3000) . "
\"\"\"

User question: \"{$prompt}\"
Please respond clearly in simple professional Hinglish/English with bullet points, product names, dimensions, or calculations as needed.";

        // If OpenAI key is present (starts with sk- or env variable set)
        if ($openAiKey && (str_starts_with($openAiKey, 'sk-') || !$geminiKey)) {
            try {
                $response = Http::withToken($openAiKey)
                    ->timeout(35)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => $prompt],
                        ],
                        'temperature' => 0.7,
                    ]);

                if ($response->successful()) {
                    $reply = $response->json('choices.0.message.content');
                    return response()->json([
                        'success' => true,
                        'reply' => $reply,
                        'provider' => 'OpenAI (GPT-4o mini)',
                    ]);
                } else {
                    $errMsg = $response->json('error.message') ?? 'OpenAI API error occurred.';
                    return response()->json(['success' => false, 'message' => $errMsg], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'OpenAI connection error: ' . $e->getMessage()], 500);
            }
        }

        // If Google Gemini key is present
        if ($geminiKey) {
            try {
                $response = Http::timeout(35)
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$geminiKey}", [
                        'contents' => [
                            ['parts' => [['text' => $systemPrompt]]]
                        ]
                    ]);

                if ($response->successful()) {
                    $reply = $response->json('candidates.0.content.parts.0.text');
                    return response()->json([
                        'success' => true,
                        'reply' => $reply,
                        'provider' => 'Google Gemini 1.5 Flash',
                    ]);
                } else {
                    $errMsg = $response->json('error.message') ?? 'Gemini API error occurred.';
                    return response()->json(['success' => false, 'message' => $errMsg], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Gemini connection error: ' . $e->getMessage()], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Koi AI API Key nahi mili. Kripya apne .env ya Render me OPENAI_KEY ya OPENAI_API_KEY dalein, ya Copilot settings me key paste karein.',
        ], 400);
    }

    /**
     * 📊 Page 3: Excel Multi-Row Mapper (Select 6-8 Lines -> 1-Click Batch Image Link)
     */
    public function excelMapper(Request $request, $jobId = null)
    {
        $userId = Auth::id();
        $job = null;

        if ($jobId) {
            $job = CatalogIngestionJob::where('id', $jobId)->where('user_id', $userId)->first();
        }

        if (!$job) {
            $galleryImages = $this->getAllGalleryImages($userId);
            return view('seller.catalog.excel_mapper', [
                'job' => null,
                'products' => [],
                'flatRows' => [],
                'categories' => Category::all(),
                'galleryImages' => $galleryImages,
                'upvcCount' => 0,
                'cpvcCount' => 0,
                'swrCount' => 0,
                'otherCount' => 0,
            ]);
        }

        $extractedData = $job->extracted_data ?? ['products' => []];
        $products = $extractedData['products'] ?? [];
        $categories = Category::all();

        // Categorize each product into UPVC, CPVC, SWR, or AGRI_OTHER
        foreach ($products as $pIdx => &$prod) {
            $pName = strtoupper($prod['name'] ?? '');
            $pCat = strtoupper($prod['category'] ?? '');

            if (str_contains($pName, 'CPVC') || str_contains($pCat, 'CPVC')) {
                $groupType = 'CPVC';
            } elseif (str_contains($pName, 'UPVC') || str_contains($pCat, 'UPVC')) {
                $groupType = 'UPVC';
            } elseif (str_contains($pName, 'SWR') || str_contains($pCat, 'SWR') || str_contains($pName, 'TRAP') || str_contains($pName, 'VENT') || str_contains($pName, 'COWL')) {
                $groupType = 'SWR';
            } else {
                $groupType = 'AGRI_OTHER';
            }

            $prod['group_type'] = $groupType;
        }
        unset($prod);

        // Build flat rows list for 6-8 row table selector
        $flatRows = [];
        $rowIndex = 0;
        foreach ($products as $pIdx => $prod) {
            $prodName = $prod['name'];
            $cat = $prod['category'] ?? 'Industrial & Commercial';
            $img = $prod['image_url'] ?? null;
            $groupType = $prod['group_type'] ?? 'UPVC';
            $variants = $prod['variants'] ?? [];

            foreach ($variants as $vIdx => $v) {
                $flatRows[] = [
                    'row_id' => $rowIndex,
                    'parent_idx' => $pIdx,
                    'variant_idx' => $vIdx,
                    'product_name' => $prodName,
                    'variant_name' => $v['variant_name'] ?? 'Standard',
                    'size' => $v['size'] ?? 'Standard',
                    'category' => $cat,
                    'group_type' => $groupType,
                    'mrp' => floatval($v['mrp'] ?? 0),
                    'purchase_cost' => floatval($v['raw_rate'] ?? 0),
                    'wholesale_price' => floatval($v['wholesale_price'] ?? 0),
                    'retail_price' => floatval($v['retail_price'] ?? 0),
                    'image_url' => $img,
                ];
                $rowIndex++;
            }
        }

        $upvcCount = count(array_filter($products, fn($p) => ($p['group_type'] ?? '') === 'UPVC'));
        $cpvcCount = count(array_filter($products, fn($p) => ($p['group_type'] ?? '') === 'CPVC'));
        $swrCount = count(array_filter($products, fn($p) => ($p['group_type'] ?? '') === 'SWR'));
        $otherCount = count(array_filter($products, fn($p) => ($p['group_type'] ?? '') === 'AGRI_OTHER'));

        $galleryImages = $this->getAllGalleryImages($userId);

        return view('seller.catalog.excel_mapper', compact(
            'job',
            'products',
            'flatRows',
            'galleryImages',
            'categories',
            'upvcCount',
            'cpvcCount',
            'swrCount',
            'otherCount'
        ));
    }

    /**
     * Batch assign an image to multiple selected rows / products in Excel Mapper.
     */
    public function assignBatchImage(Request $request)
    {
        $request->validate([
            'job_id' => 'required|exists:catalog_ingestion_jobs,id',
            'image_url' => 'required|string',
            'parent_indexes' => 'nullable|array',
            'row_indexes' => 'nullable|array',
        ]);

        $job = CatalogIngestionJob::where('id', $request->input('job_id'))
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $data = $job->extracted_data;
        $imageUrl = $request->input('image_url');
        $updatedCount = 0;

        // Mode 1: Assign by Parent Product Indexes (e.g. Card IDs)
        if ($request->has('parent_indexes') && is_array($request->input('parent_indexes'))) {
            foreach ($request->input('parent_indexes') as $idx) {
                if (isset($data['products'][$idx])) {
                    $data['products'][$idx]['image_url'] = $imageUrl;
                    $updatedCount++;
                }
            }
        }

        // Mode 2: Assign by Flat Row Indexes (e.g. 6-8 selected spreadsheet rows)
        if ($request->has('row_indexes') && is_array($request->input('row_indexes'))) {
            // Re-flatten to find the parent index of each row
            $rowIndex = 0;
            $parentToUpdate = [];
            foreach ($data['products'] as $pIdx => $prod) {
                foreach ($prod['variants'] as $vIdx => $v) {
                    if (in_array($rowIndex, $request->input('row_indexes'))) {
                        $parentToUpdate[$pIdx] = true;
                    }
                    $rowIndex++;
                }
            }

            foreach (array_keys($parentToUpdate) as $pIdx) {
                if (isset($data['products'][$pIdx])) {
                    $data['products'][$pIdx]['image_url'] = $imageUrl;
                    $updatedCount++;
                }
            }
        }

        $job->update(['extracted_data' => $data]);

        return response()->json([
            'success' => true,
            'updated_count' => $updatedCount,
            'image_url' => $imageUrl,
            'asset_url' => asset($imageUrl),
            'message' => "Successfully assigned image to selected products/rows!",
        ]);
    }

    /**
     * Direct Publish from Excel Mapper into Supabase DB.
     */
    public function publishDirectFromMapper(Request $request)
    {
        $userId = Auth::id();
        $jobId = $request->input('job_id');

        $job = CatalogIngestionJob::where('id', $jobId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $data = $job->extracted_data;
        $products = $data['products'] ?? [];

        if (empty($products)) {
            return back()->withErrors(['No products found in draft to publish.']);
        }

        $defaultCategory = Category::firstOrCreate(['name' => 'Industrial & Commercial'], ['slug' => 'industrial-commercial']);
        $publishedCount = 0;

        foreach ($products as $prodData) {
            $catId = $defaultCategory->id;
            $gst = 18;
            $variants = $prodData['variants'] ?? [];
            $hasMultipleVariants = count($variants) > 1;

            $firstVar = $variants[0] ?? [];
            $basePurchase = floatval($firstVar['raw_rate'] ?? 0);
            $baseWholesale = floatval($firstVar['wholesale_price'] ?? ($basePurchase * 1.15));
            $baseRetail = floatval($firstVar['retail_price'] ?? ($basePurchase * 1.35));
            $baseMrp = floatval($firstVar['mrp'] ?? ($basePurchase * 1.60));

            $product = Product::create([
                'user_id' => $userId,
                'category_id' => $catId,
                'name' => $prodData['name'],
                'slug' => Str::slug($prodData['name']) . '-' . Str::random(5),
                'description' => "High grade {$prodData['name']} manufactured to industrial specifications.",
                'hsn_code' => '39174000',
                'image' => $prodData['image_url'] ?? null,
                'purchase_price' => $basePurchase,
                'wholesale_price' => $baseWholesale,
                'price' => $baseRetail,
                'mrp' => $baseMrp,
                'gst_percent' => $gst,
                'stock_quantity' => 100,
                'track_inventory' => false,
                'has_variants' => $hasMultipleVariants,
                'sku' => 'PLST-' . strtoupper(Str::random(6)),
            ]);

            foreach ($variants as $v) {
                $rawRate = floatval($v['raw_rate'] ?? 0);
                $wPrice = floatval($v['wholesale_price'] ?? ($rawRate * 1.15));
                $rPrice = floatval($v['retail_price'] ?? ($rawRate * 1.35));
                $vMrp = floatval($v['mrp'] ?? ($rawRate * 1.60));

                ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => $v['variant_name'] ?? 'Standard',
                    'size' => $v['size'] ?? 'Standard',
                    'grade' => $v['grade'] ?? 'Industrial',
                    'raw_rate' => $rawRate,
                    'wholesale_price' => $wPrice,
                    'retail_price' => $rPrice,
                    'mrp' => $vMrp,
                    'stock_quantity' => 100,
                    'sku' => 'VAR-' . strtoupper(Str::random(7)),
                ]);
            }

            $publishedCount++;
        }

        $job->update(['status' => 'published']);

        return redirect()->route('seller.inventory.index')
            ->with('success', "🎉 Mubarakan! {$publishedCount} grouped products (291 sizes) live store me successfully publish ho gaye hain!");
    }
}

