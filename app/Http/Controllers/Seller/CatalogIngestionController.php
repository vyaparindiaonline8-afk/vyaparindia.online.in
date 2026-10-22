<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\CatalogIngestionJob;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Services\AICatalogIngestionService;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
            'extraction_mode' => 'nullable|string|in:text_only,with_images',
            'page_selection' => 'nullable|string|in:all,custom',
            'custom_pages' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        $file = $request->file('catalog_file') ?? $request->file('catalog_pdf');

        if (!$file) {
            return back()->withErrors(['catalog_file' => 'Please select a valid PDF brochure or Excel (.xlsx/.csv) rate list file.']);
        }

        $mode = $request->input('extraction_mode', 'text_only');
        $pageRange = 'all';
        if ($request->input('page_selection') === 'custom') {
            $customPages = trim((string)$request->input('custom_pages', ''));
            if (!empty($customPages)) {
                $pageRange = $customPages;
            }
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

        // Process PDF or Excel via AI Ingestion Service (with selected mode & page range)
        $job = $this->ingestionService->processCatalog($job, $mode, $pageRange);

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

        $extractedData = $job->extracted_data ?? [];
        $products = $extractedData['products'] ?? [];

        if (empty($products)) {
            $masterExcel = public_path('catalogs/PLASTO_WITH_IMAGES_MASTER.xlsx');
            if (file_exists($masterExcel)) {
                return response()->download($masterExcel, 'VyaparIndia_Plasto_Master_Catalog.xlsx');
            }
            return back()->with('error', 'इस कैटलॉग में कोई प्रोडक्ट्स डेटा उपलब्ध नहीं है।');
        }

        $cleanTitle = preg_replace('/[^A-Za-z0-9_-]/', '_', pathinfo($job->filename, PATHINFO_FILENAME));
        $filename = "VyaparIndia_{$cleanTitle}_Formula_Sheet.csv";

        $rows = [];
        $headers = [
            'ITAM CODE',
            'PRODUCT NAME',
            'CATEGORY',
            'GROUP',
            'SIZE',
            'MRP',
            'LESS',
            'GST',
            'P.COST',
            'SALE A',
            'SALE B',
            'SALE C',
            'OPN.STOCK',
            'DESCRIPTION'
        ];
        $rows[] = $headers;

        $codeCounter = 1;
        foreach ($products as $prod) {
            $baseName = trim($prod['name'] ?? 'Product');
            $category = $prod['category'] ?? 'General';
            $group = $prod['group_name'] ?? $category;
            $disc = floatval($prod['trade_discount_percent'] ?? 47);
            $gst = floatval($prod['gst_percent'] ?? 18);
            $desc = $prod['description'] ?? '';

            $variants = $prod['variants'] ?? [];
            if (!empty($variants)) {
                foreach ($variants as $v) {
                    $rawRate = floatval($v['raw_rate'] ?? ($prod['base_price'] ?? 0));
                    $pCost = round($rawRate * (1 - ($disc / 100)) * (1 + ($gst / 100)), 2);
                    $saleA = floatval($v['retail_price'] ?? round($pCost * 1.30, 2));
                    $saleB = floatval($v['wholesale_price'] ?? round($pCost * 1.15, 2));
                    $saleC = round($pCost * 1.10, 2);
                    $size = $v['size'] ?? ($v['variant_name'] ?? 'Standard');
                    $mrp = floatval($v['mrp'] ?? $rawRate);
                    $code = !empty($v['sku']) ? $v['sku'] : ("ITM-" . str_pad($codeCounter++, 3, '0', STR_PAD_LEFT));

                    $rows[] = [
                        $code,
                        $baseName . ' ' . $size,
                        $category,
                        $group,
                        $size,
                        $mrp,
                        $disc,
                        $gst,
                        $pCost,
                        $saleA,
                        $saleB,
                        $saleC,
                        $v['stock_quantity'] ?? 0,
                        $desc
                    ];
                }
            } else {
                $rawRate = floatval($prod['base_price'] ?? 0);
                $pCost = round($rawRate * (1 - ($disc / 100)) * (1 + ($gst / 100)), 2);
                $saleA = round($pCost * 1.30, 2);
                $saleB = round($pCost * 1.15, 2);
                $saleC = round($pCost * 1.10, 2);
                $mrp = floatval($prod['mrp'] ?? $rawRate);
                $code = !empty($prod['sku']) ? $prod['sku'] : ("ITM-" . str_pad($codeCounter++, 3, '0', STR_PAD_LEFT));

                $rows[] = [
                    $code,
                    $baseName,
                    $category,
                    $group,
                    'Standard',
                    $mrp,
                    $disc,
                    $gst,
                    $pCost,
                    $saleA,
                    $saleB,
                    $saleC,
                    $prod['stock_quantity'] ?? 0,
                    $desc
                ];
            }
        }

        // Generate UTF-8 CSV with Byte Order Mark (BOM)
        $output = "\xEF\xBB\xBF";
        $handle = fopen('php://memory', 'r+');
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $output .= stream_get_contents($handle);
        fclose($handle);

        return response($output, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Delete an unneeded draft / previous catalog ingestion job safely.
     * Note: Gallery images, Media Vault crops, and published products are NOT affected.
     */
    public function deleteJob(CatalogIngestionJob $job)
    {
        if ($job->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // Clean up temporary catalog source file if it exists in local storage
        if ($job->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($job->file_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($job->file_path);
        }

        $jobTitle = $job->filename ?: "Job #{$job->id}";
        $job->delete();

        return back()->with('success', "Catalog draft \"{$jobTitle}\" successfully deleted. Media Vault photos remain completely safe.");
    }

    /**
     * Save Client-Side Cropped Image
     */
    public function cropImage(Request $request)
    {
        $request->validate([
            'image_data' => 'nullable|string',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            'product_index' => 'nullable|integer',
        ]);

        $cropsDir = public_path('storage/catalog_extracted/custom_crops');
        if (!is_dir($cropsDir)) {
            mkdir($cropsDir, 0755, true);
        }

        // 1. Direct file upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $ext = $file->getClientOriginalExtension() ?: 'jpg';
            $fileName = 'prod_' . Auth::id() . '_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $file->move($cropsDir, $fileName);
            $relativeUrl = 'storage/catalog_extracted/custom_crops/' . $fileName;

            return response()->json([
                'success' => true,
                'image_url' => $relativeUrl,
                'asset_url' => asset($relativeUrl),
            ]);
        }

        // 2. Base64 crop data
        $data = $request->input('image_data');
        if ($data && preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = substr($data, strpos($data, ',') + 1);
            $type = strtolower($type[1]);
            if (!in_array($type, ['jpg', 'jpeg', 'png', 'webp'])) {
                return response()->json(['success' => false, 'message' => 'Invalid image format'], 422);
            }
            $data = base64_decode($data);
            if ($data === false) {
                return response()->json(['success' => false, 'message' => 'Base64 decode failed'], 422);
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

        return response()->json(['success' => false, 'message' => 'No image data or file provided'], 422);
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
        @set_time_limit(300);
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

        DB::transaction(function () use ($validated, $sellerId, $defaultCategory, $job, &$publishedCount) {
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

                $product = Product::updateOrCreate(
                    [
                        'user_id' => $sellerId,
                        'name' => $prodData['name'],
                    ],
                    [
                        'category_id' => $catId,
                        'brand' => $prodData['brand'] ?? 'Plasto',
                        'group_name' => $prodData['group_name'] ?? ($prodData['group_type'] ?? 'Plumbing'),
                        'slug' => Str::slug($prodData['name']) . '-' . Str::random(5),
                        'description' => $prodData['description'] ?? '',
                        'hsn_code' => $prodHsn = ($prodData['hsn_code'] ?? '39174000'),
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
                    ]
                );

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

                // Clean Variants
                ProductVariant::where('product_id', $product->id)->delete();
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
        });

        return redirect()->route('seller.inventory.index')
            ->with('success', "🎉 Successfully published {$publishedCount} products with dynamic variants into your live catalog!");
    }

    /**
     * Unified Inventory & Stock Management View.
     */
    public function inventory(Request $request)
    {
        $sellerId = Auth::id();
        $query = Product::where('user_id', $sellerId)
            ->with(['variants', 'stockMovements' => function ($q) {
                $q->latest()->take(5);
            }]);

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('sku', 'like', "%{$s}%");
            });
        }

        $products = $query->latest()->paginate(25)->withQueryString();

        $totalCatalogItems = Product::where('user_id', $sellerId)->count();
        $lowStockCount = Product::where('user_id', $sellerId)
            ->where('track_inventory', true)
            ->where('stock_quantity', '<=', 5)
            ->count();
        $outOfStockCount = Product::where('user_id', $sellerId)
            ->where('track_inventory', true)
            ->where('stock_quantity', '<=', 0)
            ->count();

        $sellerCategories = Category::whereHas('products', function ($q) use ($sellerId) {
            $q->where('user_id', $sellerId);
        })->get();

        $sellerPage = Auth::user()->sellerPage;

        $galleryImages = self::getAllGalleryImages($sellerId);

        return view('seller.inventory.index', compact('products', 'totalCatalogItems', 'lowStockCount', 'outOfStockCount', 'sellerCategories', 'sellerPage', 'galleryImages'));
    }

    /**
     * Download Price List / Rate Revision Template for logged-in seller.
     */
    public function exportPriceTemplate()
    {
        $sellerId = Auth::id();
        $products = Product::where('user_id', $sellerId)->with('variants')->get();

        $csvData = [];
        $csvData[] = ['Product ID', 'Variant ID', 'SKU / Code', 'Product Name', 'Size / Variant Name', 'Cost (Purchase Rate)', 'Wholesale Price (B2B)', 'Retail Price (D2C)', 'MRP', 'Current Stock'];

        foreach ($products as $p) {
            if ($p->variants->isNotEmpty()) {
                foreach ($p->variants as $v) {
                    $csvData[] = [
                        $p->id,
                        $v->id,
                        $v->sku ?: ($p->sku ?: "PRD-{$p->id}"),
                        $p->name,
                        $v->variant_name ?: ($v->size ?: 'Standard'),
                        $v->purchase_price ?: $v->raw_rate,
                        $v->wholesale_price,
                        $v->retail_price ?: $p->price,
                        $v->mrp ?: $p->mrp,
                        $v->stock_quantity ?? $p->stock_quantity ?? 100,
                    ];
                }
            } else {
                $csvData[] = [
                    $p->id,
                    '',
                    $p->sku ?: "PRD-{$p->id}",
                    $p->name,
                    'Standard',
                    $p->purchase_price,
                    $p->wholesale_price,
                    $p->price,
                    $p->mrp,
                    $p->stock_quantity ?? 0,
                ];
            }
        }

        $filename = "VyaparIndia_RateList_Update_" . date('Y_m_d_His') . ".csv";
        $handle = fopen('php://memory', 'w+');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        fseek($handle, 0);

        return response(stream_get_contents($handle), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Fast Bulk Price List / Rate Revision via Excel or CSV.
     */
    public function bulkRateUpdate(Request $request)
    {
        @set_time_limit(300);
        $sellerId = Auth::id();
        $rows = [];

        // Check if rows sent as JSON (from SheetJS client parser)
        if ($request->filled('rate_rows')) {
            $decoded = json_decode($request->input('rate_rows'), true);
            if (is_array($decoded)) {
                $rows = $decoded;
            }
        } elseif ($request->hasFile('price_file')) {
            // Native CSV parser fallback
            $file = $request->file('price_file');
            $path = $file->getRealPath();
            if (($handle = fopen($path, 'r')) !== false) {
                $header = null;
                while (($data = fgetcsv($handle, 2000, ',')) !== false) {
                    if (!$header) {
                        $header = array_map(fn($h) => strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '', str_replace([' ', '/', '-'], '_', $h)))), $data);
                        continue;
                    }
                    if (count($data) >= 3) {
                        $row = [];
                        foreach ($header as $idx => $key) {
                            $row[$key] = $data[$idx] ?? '';
                        }
                        $rows[] = $row;
                    }
                }
                fclose($handle);
            }
        }

        if (empty($rows)) {
            return back()->with('error', 'No valid rate rows found to update. Please upload a valid CSV or Excel sheet.');
        }

        $updatedVariants = 0;
        $updatedProducts = [];

        DB::transaction(function () use ($rows, $sellerId, &$updatedVariants, &$updatedProducts) {
            $sellerProducts = Product::where('user_id', $sellerId)->with('variants')->get();

            $productsById = $sellerProducts->keyBy('id');
            $variantsById = [];
            $variantsBySku = [];
            $variantsByNameSize = [];
            $productsByName = [];

            foreach ($sellerProducts as $p) {
                $cleanPName = strtolower(trim($p->name));
                $productsByName[$cleanPName] = $p;

                foreach ($p->variants as $v) {
                    $variantsById[$v->id] = $v;
                    if ($v->sku) {
                        $variantsBySku[strtolower(trim($v->sku))] = $v;
                    }
                    $cleanVName = strtolower(trim($v->variant_name ?: ($v->size ?: 'standard')));
                    $variantsByNameSize[$cleanPName . '___' . $cleanVName] = $v;
                }
            }

            foreach ($rows as $row) {
                $rowProdId = intval($row['product_id'] ?? ($row['id'] ?? ($row['product_id_do_not_change'] ?? 0)));
                $rowVarId = intval($row['variant_id'] ?? ($row['variant_id_optional'] ?? 0));
                $sku = strtolower(trim($row['sku__code'] ?? ($row['sku_code'] ?? ($row['sku'] ?? ($row['product_code'] ?? '')))));
                $pNameRaw = trim($row['product_name'] ?? ($row['name'] ?? ($row['item_name'] ?? '')));
                $pName = strtolower($pNameRaw);
                $vName = strtolower(trim($row['size__variant_name'] ?? ($row['size_variant_name'] ?? ($row['size'] ?? ($row['variant_name'] ?? ($row['variant'] ?? 'standard'))))));

                // Clean rates helper
                $getVal = function(array $keys) use ($row) {
                    foreach ($keys as $k) {
                        if (isset($row[$k]) && $row[$k] !== '' && !is_null($row[$k])) {
                            return floatval($row[$k]);
                        }
                    }
                    return null;
                };

                $cost = $getVal(['cost__purchase_rate_', 'cost_purchase_rate', 'cost', 'purchase_price', 'raw_rate']);
                $retail = $getVal(['retail_price__d2c_', 'retail_price_d2c', 'retail_price', 'price', 'rate_a', 'selling_price']);
                $wholesale = $getVal(['wholesale_price__b2b_', 'wholesale_price_b2b', 'wholesale_price', 'rate_b']);
                $mrp = $getVal(['mrp']);
                $stockRaw = $getVal(['current_stock', 'stock', 'stock_quantity']);
                $stock = !is_null($stockRaw) ? intval($stockRaw) : null;

                // Find matching variant or product - ID takes top priority!
                $matchedVariant = null;
                $matchedProduct = null;

                if ($rowVarId && isset($variantsById[$rowVarId])) {
                    $matchedVariant = $variantsById[$rowVarId];
                } elseif ($rowProdId && isset($productsById[$rowProdId])) {
                    $matchedProduct = $productsById[$rowProdId];
                    if ($matchedProduct->variants->isNotEmpty() && $vName) {
                        foreach ($matchedProduct->variants as $pv) {
                            $pvName = strtolower(trim($pv->variant_name ?: $pv->size));
                            if ($pvName === $vName || str_contains($pvName, $vName) || str_contains($vName, $pvName)) {
                                $matchedVariant = $pv;
                                break;
                            }
                        }
                    }
                } elseif ($sku && isset($variantsBySku[$sku])) {
                    $matchedVariant = $variantsBySku[$sku];
                } elseif (isset($variantsByNameSize[$pName . '___' . $vName])) {
                    $matchedVariant = $variantsByNameSize[$pName . '___' . $vName];
                } elseif (isset($productsByName[$pName]) && $productsByName[$pName]->variants->isNotEmpty()) {
                    foreach ($productsByName[$pName]->variants as $pv) {
                        $pvName = strtolower(trim($pv->variant_name ?: $pv->size));
                        if ($vName && (str_contains($pvName, $vName) || str_contains($vName, $pvName))) {
                            $matchedVariant = $pv;
                            break;
                        }
                    }
                } elseif (isset($productsByName[$pName])) {
                    $matchedProduct = $productsByName[$pName];
                }

                if ($matchedVariant) {
                    $updates = [];
                    if (!is_null($cost) && $cost > 0) {
                        $updates['raw_rate'] = $cost;
                        $updates['purchase_price'] = $cost;
                    }
                    if (!is_null($retail) && $retail > 0) $updates['retail_price'] = $retail;
                    if (!is_null($wholesale) && $wholesale > 0) $updates['wholesale_price'] = $wholesale;
                    if (!is_null($mrp) && $mrp > 0) $updates['mrp'] = $mrp;
                    if (!is_null($stock)) $updates['stock_quantity'] = $stock;

                    if (!empty($updates)) {
                        $matchedVariant->update($updates);
                        $updatedVariants++;
                        $updatedProducts[$matchedVariant->product_id] = true;
                    }
                } elseif ($matchedProduct) {
                    $pObj = $matchedProduct;
                    $updates = [];
                    if (!empty($pNameRaw) && $pNameRaw !== $pObj->name) {
                        $updates['name'] = $pNameRaw;
                        $updates['slug'] = \Illuminate\Support\Str::slug($pNameRaw) ?: $pObj->slug;
                    }
                    if (!is_null($cost) && $cost > 0) $updates['purchase_price'] = $cost;
                    if (!is_null($retail) && $retail > 0) $updates['price'] = $retail;
                    if (!is_null($wholesale) && $wholesale > 0) $updates['wholesale_price'] = $wholesale;
                    if (!is_null($mrp) && $mrp > 0) $updates['mrp'] = $mrp;
                    if (!is_null($stock)) $updates['stock_quantity'] = $stock;

                    if (!empty($updates)) {
                        $pObj->update($updates);
                        $updatedProducts[$pObj->id] = true;
                    }
                }
            }

            // Sync parent products baseline from first variant
            foreach (array_keys($updatedProducts) as $pId) {
                $p = Product::with('variants')->find($pId);
                if ($p && $p->variants->isNotEmpty()) {
                    $first = $p->variants->first();
                    $p->update([
                        'purchase_price' => $first->purchase_price ?: $first->raw_rate,
                        'price' => $first->retail_price ?: $p->price,
                        'wholesale_price' => $first->wholesale_price ?: $p->wholesale_price,
                        'mrp' => $first->mrp ?: $p->mrp,
                    ]);
                }
            }
        });

        $prodCount = count($updatedProducts);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "🎉 Shandar! Nayi Rate List ke anusar {$updatedVariants} sizes aur {$prodCount} products ke rates turant update ho gaye hain!",
                'updated_variants' => $updatedVariants,
                'updated_products' => $prodCount,
            ]);
        }

        return redirect()->route('seller.inventory.index')
            ->with('success', "🎉 Shandar! Nayi Rate List ke anusar {$updatedVariants} sizes aur {$prodCount} products ke rates turant update ho gaye hain!");
    }

    /**
     * ✏️ Fast In-Place Update for Rates, Sizes, MRP & Stocks from Inventory Page.
     */
    public function quickUpdateRates(Request $request)
    {
        $sellerId = Auth::id();
        $productId = $request->input('product_id');

        $product = Product::where('id', $productId)->where('user_id', $sellerId)->with('variants')->first();
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product nahi mila ya aapke account se authorized nahi hai.',
            ], 404);
        }

        $prodName = trim($request->input('product_name') ?: $product->name);
        $prodHsn = trim($request->input('hsn_code') ?: ($product->hsn_code ?: '39174000'));
        $variantsInput = $request->input('variants', []);

        DB::transaction(function () use ($product, $prodName, $prodHsn, $variantsInput) {
            // Update parent base info
            $product->name = $prodName;
            $product->hsn_code = $prodHsn;

            if (!empty($variantsInput) && is_array($variantsInput)) {
                $product->has_variants = true;
                $lowestRetail = null;
                $lowestWholesale = null;
                $lowestMrp = null;
                $totalVariantStock = 0;
                $hasVariantStock = false;

                foreach ($variantsInput as $vData) {
                    $vId = intval($vData['id'] ?? 0);
                    $vSize = trim($vData['size'] ?? ($vData['variant_name'] ?? 'Standard'));
                    $vMrp = floatval($vData['mrp'] ?? 0);
                    $vWholesale = floatval($vData['wholesale_price'] ?? 0);
                    $vRetail = floatval($vData['retail_price'] ?? 0);
                    $vPack1 = trim($vData['packing_1'] ?? '');
                    $vPack2 = trim($vData['packing_2'] ?? '');
                    $vStock = isset($vData['stock_quantity']) && $vData['stock_quantity'] !== '' ? intval($vData['stock_quantity']) : null;
                    $vSku = trim($vData['sku'] ?? '');

                    $attributes = [
                        'size' => $vSize,
                        'packing_1' => $vPack1,
                        'packing_2' => $vPack2,
                    ];

                    $variant = null;
                    if ($vId > 0) {
                        $variant = ProductVariant::where('id', $vId)->where('product_id', $product->id)->first();
                    }

                    if ($variant) {
                        $variant->variant_name = $vSize;
                        $variant->mrp = $vMrp;
                        $variant->wholesale_price = $vWholesale;
                        $variant->retail_price = $vRetail;
                        if (!empty($vSku)) $variant->sku = $vSku;
                        if (!is_null($vStock)) {
                            $variant->stock_quantity = $vStock;
                            $variant->track_inventory = true;
                            $totalVariantStock += $vStock;
                            $hasVariantStock = true;
                        }
                        $variant->attributes = $attributes;
                        $variant->save();
                    } else {
                        // Create new variant if added
                        $variant = ProductVariant::create([
                            'product_id' => $product->id,
                            'variant_name' => $vSize,
                            'sku' => $vSku ?: ('VAR-' . strtoupper(Str::random(7))),
                            'mrp' => $vMrp,
                            'wholesale_price' => $vWholesale,
                            'retail_price' => $vRetail,
                            'stock_quantity' => $vStock ?? 0,
                            'track_inventory' => !is_null($vStock),
                            'attributes' => $attributes,
                        ]);
                        if (!is_null($vStock)) {
                            $totalVariantStock += $vStock;
                            $hasVariantStock = true;
                        }
                    }

                    // Track baselines
                    if ($vRetail > 0 && ($lowestRetail === null || $vRetail < $lowestRetail)) $lowestRetail = $vRetail;
                    if ($vWholesale > 0 && ($lowestWholesale === null || $vWholesale < $lowestWholesale)) $lowestWholesale = $vWholesale;
                    if ($vMrp > 0 && ($lowestMrp === null || $vMrp < $lowestMrp)) $lowestMrp = $vMrp;
                }

                if ($lowestRetail !== null) $product->price = $lowestRetail;
                if ($lowestWholesale !== null) $product->wholesale_price = $lowestWholesale;
                if ($lowestMrp !== null) $product->mrp = $lowestMrp;
                if ($hasVariantStock) {
                    $product->stock_quantity = $totalVariantStock;
                    $product->track_inventory = true;
                }
            } else {
                // Single product rate update
                if ($request->filled('mrp')) $product->mrp = floatval($request->input('mrp'));
                if ($request->filled('wholesale_price')) $product->wholesale_price = floatval($request->input('wholesale_price'));
                if ($request->filled('price')) $product->price = floatval($request->input('price'));
                if ($request->filled('stock_quantity')) {
                    $product->stock_quantity = intval($request->input('stock_quantity'));
                    $product->track_inventory = true;
                }
                if ($request->filled('size')) {
                    $custom = $product->attributes ?? [];
                    if (!is_array($custom)) $custom = [];
                    $custom['size'] = trim($request->input('size'));
                    $product->attributes = $custom;
                }
            }

            $product->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Rates aur sizes safalta se update ho gaye!',
            'product' => $product->fresh(['variants']),
        ]);
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
     * JSON Registry Helpers for persisting Cloudinary and Custom Crops across deploys.
     */
    public static function getCropsRegistry($userId): array
    {
        $regFile = storage_path('app/gallery_crops_' . $userId . '.json');
        if (file_exists($regFile)) {
            $data = json_decode(file_get_contents($regFile), true);
            return is_array($data) ? $data : [];
        }
        return [];
    }

    public static function addCropToRegistry($userId, array $cropItem): void
    {
        $regFile = storage_path('app/gallery_crops_' . $userId . '.json');
        $crops = self::getCropsRegistry($userId);
        array_unshift($crops, $cropItem);
        $crops = array_slice($crops, 0, 500);
        @file_put_contents($regFile, json_encode($crops, JSON_PRETTY_PRINT));
    }

    public static function removeCropFromRegistry($userId, string $url): void
    {
        $regFile = storage_path('app/gallery_crops_' . $userId . '.json');
        $crops = self::getCropsRegistry($userId);
        $crops = array_filter($crops, fn($item) => ($item['url'] ?? '') !== $url && ($item['asset_url'] ?? '') !== $url);
        @file_put_contents($regFile, json_encode(array_values($crops), JSON_PRETTY_PRINT));
    }

    /**
     * Smart folder detector / resolver for catalog media
     */
    public static function detectFolderForImage(string $name, ?string $explicitCategory = null): string
    {
        if (!empty($explicitCategory) && !in_array(strtolower(trim($explicitCategory)), ['general', 'uncategorized', 'other', 'default', 'null', ''])) {
            return trim($explicitCategory);
        }

        return 'General';
    }

    /**
     * Helper to collect all available gallery images for seller.
     */
    public static function getAllGalleryImages($userId = null): array
    {
        $images = [];
        $seenUrls = [];
        $seenHashes = [];

        // 1. Registered SellerMedia from Database:
        // - Personal: ALWAYS include all images owned by $userId
        // - Universal Central: Include images from other sellers ONLY IF permission_granted == true AND is_universal == true AND NOT fashion/clothing
        try {
            $dbMedia = \App\Models\SellerMedia::where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhere(function ($sub) {
                      $sub->where('is_universal', true)
                          ->where('permission_granted', true);
                  });
            })->latest()->get();

            $fashionKeywords = ['cloth', 'dress', 'shirt', 'pant', 'sari', 'saree', 'suit', 'fashion', 'kurti', 'tshirt', 'jeans', 'fabric', 'apparel', 'garment', 'dupatta', 'top', 'tunic'];

            foreach ($dbMedia as $m) {
                // If it belongs to another seller, ensure no personal fashion/clothes enter the Central Hub
                if ($m->user_id != $userId) {
                    $fn = strtolower($m->filename);
                    $ct = strtolower($m->category_type ?? '');
                    $isFashion = false;
                    foreach ($fashionKeywords as $kw) {
                        if (str_contains($fn, $kw) || str_contains($ct, $kw)) {
                            $isFashion = true;
                            break;
                        }
                    }
                    if ($isFashion) continue;
                }

                // ⚡ Smart content hash deduplication: prevent showing duplicate uploaded/cropped photos
                if (!empty($m->content_hash)) {
                    if (isset($seenHashes[$m->content_hash])) continue;
                    $seenHashes[$m->content_hash] = true;
                }

                $url = $m->file_path;
                if (isset($seenUrls[$url])) continue;
                if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
                    $cleanPath = public_path(ltrim($url, '/'));
                    if (!file_exists($cleanPath)) continue;
                }
                $seenUrls[$url] = true;
                $assetUrl = (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) ? $url : asset($url);
                $name = $m->filename ? ucwords(str_replace(['crop_', 'p_', '_', '-'], ' ', pathinfo($m->filename, PATHINFO_FILENAME))) : 'Catalog Image';
                $isMine = ($m->user_id == $userId);

                $images[] = [
                    'id' => md5($url),
                    'filename' => $m->filename,
                    'name' => trim($name),
                    'url' => $url,
                    'asset_url' => $assetUrl,
                    'size_kb' => 35.0,
                    'created_at' => $m->created_at ? $m->created_at->format('d M Y, H:i') : date('d M Y, H:i'),
                    'source' => $isMine ? 'personal_vault' : 'universal_central',
                    'is_universal' => (bool)$m->is_universal,
                    'permission_granted' => (bool)$m->permission_granted,
                    'is_mine' => $isMine,
                    'deletable' => $isMine,
                    'category' => $m->category_type,
                    'folder' => $m->category_type,
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('SellerMedia DB fetch warning: ' . $e->getMessage());
        }

        // 2. Live Cloudinary Sync (Fetches all crops uploaded across all sessions)
        $cloudResources = [];
        try {
            $cloudResources = \Illuminate\Support\Facades\Cache::store('file')->get('cloudinary_resources_vyaparindia');
        } catch (\Throwable $e) {}

        if (empty($cloudResources)) {
            try {
                $cloudResources = CloudinaryService::listResources('vyaparindia', 500);
                if (empty($cloudResources)) {
                    $cloudResources = CloudinaryService::listResources('', 500);
                }
                if (!empty($cloudResources)) {
                    try {
                        \Illuminate\Support\Facades\Cache::store('file')->put('cloudinary_resources_vyaparindia', $cloudResources, 300);
                    } catch (\Throwable $e) {}
                }
            } catch (\Throwable $e) {
                Log::warning('Cloudinary listResources sync error: ' . $e->getMessage());
            }
        }

        if (!empty($cloudResources)) {
            $newMediaToInsert = [];

            foreach ($cloudResources as $res) {
                $secUrl = $res['secure_url'] ?? $res['url'] ?? '';
                if (!$secUrl || isset($seenUrls[$secUrl])) continue;

                $publicId = $res['public_id'] ?? '';
                if (!str_contains($publicId, 'vyaparindia') && !str_contains($publicId, 'seller_') && !str_contains($publicId, 'catalog')) {
                    continue;
                }

                $seenUrls[$secUrl] = true;
                $format = $res['format'] ?? 'jpg';
                $baseName = basename($publicId);
                $fileName = $baseName . '.' . $format;
                $name = ucwords(str_replace(['crop_', 'p_', '_', '-'], ' ', $baseName));
                $sizeKb = !empty($res['bytes']) ? round($res['bytes'] / 1024, 1) : 35.0;
                $createdAt = !empty($res['created_at']) ? date('d M Y, H:i', strtotime($res['created_at'])) : date('d M Y, H:i');

                $images[] = [
                    'id' => md5($secUrl),
                    'filename' => $fileName,
                    'name' => trim($name),
                    'url' => $secUrl,
                    'asset_url' => $secUrl,
                    'size_kb' => $sizeKb,
                    'created_at' => $createdAt,
                    'source' => 'custom_crop',
                    'deletable' => true,
                ];

                $newMediaToInsert[] = [
                    'user_id' => $userId ?: 1,
                    'filename' => $fileName,
                    'file_path' => $secUrl,
                    'is_assigned' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($newMediaToInsert)) {
                try {
                    \App\Models\SellerMedia::insertOrIgnore($newMediaToInsert);
                } catch (\Throwable $e) {}
            }
        }

        // 0b. Registered Cloudinary & Custom Crop URLs from JSON
        $registryCrops = self::getCropsRegistry($userId);
        foreach ($registryCrops as $regImg) {
            $u = $regImg['url'] ?? '';
            if ($u && !isset($seenUrls[$u])) {
                if (!str_starts_with($u, 'http://') && !str_starts_with($u, 'https://')) {
                    $cleanPath = public_path(ltrim($u, '/'));
                    if (!file_exists($cleanPath)) continue;
                }
                $seenUrls[$u] = true;
                $images[] = $regImg;
            }
        }

        // 0c. User Cropped Images Library
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

        // 4. Crops from PDF Studio Vault
        $cropDirs = [
            public_path('images/catalog/crops/seller_' . $userId) => 'images/catalog/crops/seller_' . $userId,
            public_path('images/catalog/crops') => 'images/catalog/crops',
        ];
        foreach ($cropDirs as $cDir => $relBase) {
            if (is_dir($cDir)) {
                $files = scandir($cDir);
                foreach ($files as $f) {
                    if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'])) {
                        $fullPath = $cDir . DIRECTORY_SEPARATOR . $f;
                        $relUrl = $relBase . '/' . $f;
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
        }

        // 5. Exclude external brand master and store products from personal media vault
        // (Ensures seller only sees their genuine cropped and uploaded images)

        // Attach resolved folder & category to all images (preserving custom user folders)
        $customFolders = [];
        try {
            $customFolders = \App\Models\SellerMedia::where('user_id', $userId)
                ->whereNotNull('category_type')
                ->where('category_type', '!=', '')
                ->pluck('category_type', 'file_path')
                ->toArray();
        } catch (\Throwable $e) {}

        $finalImages = [];
        $uniqueKeys = [];
        foreach ($images as $img) {
            $u = $img['url'] ?? '';
            $uClean = ltrim($u, '/');
            if (empty($u) || isset($deletedList[$u]) || isset($deletedList[$uClean])) {
                continue;
            }

            // Normalization key: base filename (prevents identical crops appearing in multiple folders)
            $baseFn = strtolower(basename(parse_url($u, PHP_URL_PATH)));
            if (isset($uniqueKeys[$baseFn])) {
                continue;
            }
            $uniqueKeys[$baseFn] = true;

            $explicit = $customFolders[$u] ?? ($customFolders[$uClean] ?? ($img['category'] ?? null));
            if (!$explicit) {
                foreach ($customFolders as $cfPath => $cfFolder) {
                    if (strtolower(basename(parse_url($cfPath, PHP_URL_PATH))) === $baseFn) {
                        $explicit = $cfFolder;
                        break;
                    }
                }
            }

            $folder = self::detectFolderForImage($img['name'] ?? '', $explicit);

            $deletedFolders = \Illuminate\Support\Facades\Cache::get("deleted_gallery_folders_{$userId}", []);
            if (!empty($deletedFolders) && isset($deletedFolders[strtolower(trim($folder))])) {
                $folder = 'General';
            }

            $img['folder'] = $folder;
            $img['category'] = $folder;
            $img['image'] = $img['asset_url'] ?? ($img['url'] ?? '');
            $finalImages[] = $img;
        }

        // Sort latest first
        usort($finalImages, function ($a, $b) {
            return ($b['created_at'] ?? 0) <=> ($a['created_at'] ?? 0);
        });

        return $finalImages;
    }

    /**
     * 📸 Page 1: Media Vault & Bulk Image Gallery (Store, Preview, Upload, Delete, Folders)
     */
    public function gallery(Request $request)
    {
        $userId = Auth::id();
        $allImages = $this->getAllGalleryImages($userId);
        
        // Calculate folders with counts
        $folders = [];
        foreach ($allImages as $img) {
            $f = $img['folder'] ?? 'General';
            $folders[$f] = ($folders[$f] ?? 0) + 1;
        }

        // Include custom created folders (even if 0 images currently)
        try {
            $customFolders = \App\Models\SellerFolder::where('user_id', $userId)->pluck('name');
            foreach ($customFolders as $cf) {
                if (!isset($folders[$cf])) {
                    $folders[$cf] = 0;
                }
            }
        } catch (\Throwable $e) {}

        ksort($folders);

        $selectedFolder = trim($request->input('folder', 'ALL'));
        if ($selectedFolder !== 'ALL' && $selectedFolder !== '') {
            $allImages = array_filter($allImages, function ($img) use ($selectedFolder) {
                return strcasecmp($img['folder'] ?? 'General', $selectedFolder) === 0;
            });
        }

        $query = trim($request->input('q', ''));
        if ($query !== '') {
            $allImages = array_filter($allImages, function ($img) use ($query) {
                return stripos($img['name'], $query) !== false || stripos($img['filename'], $query) !== false;
            });
        }

        $totalImages = count($allImages);
        $plastoCount = count(array_filter($allImages, fn($i) => ($i['source'] ?? '') === 'plasto_master'));
        $customCount = count(array_filter($allImages, fn($i) => ($i['source'] ?? '') === 'custom_crop'));

        return view('seller.catalog.gallery', [
            'images' => array_values($allImages),
            'totalImages' => $totalImages,
            'plastoCount' => $plastoCount,
            'customCount' => $customCount,
            'searchQuery' => $query,
            'folders' => $folders,
            'selectedFolder' => $selectedFolder,
        ]);
    }

    /**
     * Return all Photo Bank Gallery images as JSON for dynamic frontend UI.
     */
    public function galleryJson(Request $request)
    {
        $userId = Auth::id() ?: 1;
        $images = $this->getAllGalleryImages($userId);

        $folders = [];
        foreach ($images as $i) {
            $f = $i['folder'] ?? 'General';
            $folders[$f] = ($folders[$f] ?? 0) + 1;
        }

        try {
            $customFolders = \App\Models\SellerFolder::where('user_id', $userId)->pluck('name');
            foreach ($customFolders as $cf) {
                if (!isset($folders[$cf])) {
                    $folders[$cf] = 0;
                }
            }
        } catch (\Throwable $e) {}

        ksort($folders);

        $selectedFolder = trim($request->input('folder', 'ALL'));
        if ($selectedFolder !== 'ALL' && $selectedFolder !== '') {
            $images = array_filter($images, function ($img) use ($selectedFolder) {
                return strcasecmp($img['folder'] ?? 'General', $selectedFolder) === 0;
            });
        }

        return response()->json([
            'success' => true,
            'count' => count($images),
            'images' => array_values($images),
            'folders' => $folders,
        ]);
    }

    /**
     * Upload an image directly into the Media Vault with Folder Assignment.
     */
    public function uploadToGallery(Request $request)
    {
        $request->validate([
            'image_file' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $userId = Auth::id() ?: 1;
        $file = $request->file('image_file');
        $originalName = $file->getClientOriginalName();
        $fileName = 'upload_' . time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

        $contentHash = hash_file('sha256', $file->getRealPath());
        $fileSize = $file->getSize();

        $shareToCentral = $request->boolean('share_to_central', false);

        // Read folder or category
        $customFolder = trim($request->input('custom_folder') ?? '');
        $folderChoice = trim($request->input('folder', $request->input('category_type', 'General')));
        $folder = (!empty($customFolder) && $folderChoice === '__NEW__') ? $customFolder : (($folderChoice !== '__NEW__') ? $folderChoice : 'General');
        $categoryType = $folder;

        // ⚡ META-STYLE DEDUPLICATION:
        // If this exact photo was already uploaded, re-link to the existing media instead of creating redundant uploads
        $existingMedia = \App\Models\SellerMedia::where('user_id', $userId)
            ->where('content_hash', $contentHash)
            ->first();

        if ($existingMedia) {
            if ($categoryType && $categoryType !== 'General' && $existingMedia->category_type !== $categoryType) {
                $existingMedia->update(['category_type' => $categoryType]);
            }
            $existingUrl = $existingMedia->file_path;
            $assetUrl = (str_starts_with($existingUrl, 'http://') || str_starts_with($existingUrl, 'https://')) ? $existingUrl : asset($existingUrl);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'is_duplicate' => true,
                    'message' => "✓ Yeh photo pehle se folder '{$existingMedia->category_type}' me maujood hai! Purani photo se link kar diya gaya hai (Duplicate avoided).",
                    'image' => [
                        'filename' => $existingMedia->filename,
                        'url' => $existingUrl,
                        'asset_url' => $assetUrl,
                        'folder' => $existingMedia->category_type ?: $folder,
                        'category' => $existingMedia->category_type ?: $folder,
                        'is_universal' => (bool)$existingMedia->is_universal,
                    ],
                ]);
            }

            return back()->with('info', "Yeh photo pehle se aapke vault me maujood hai!");
        }

        // Protect Central Universal Hub: clothes/fashion/unbranded must never enter central hub
        $fashionKeywords = ['cloth', 'dress', 'shirt', 'pant', 'sari', 'saree', 'suit', 'fashion', 'kurti', 'tshirt', 'jeans', 'fabric', 'apparel', 'garment', 'dupatta', 'top', 'tunic'];
        $isFashion = false;
        $nameLower = strtolower($originalName . ' ' . $categoryType);
        foreach ($fashionKeywords as $kw) {
            if (str_contains($nameLower, $kw)) {
                $isFashion = true;
                break;
            }
        }
        $isUniversal = $shareToCentral && !$isFashion;

        // 1. Try Cloudinary CDN first
        $cloudinaryUrl = CloudinaryService::upload($file, "vyaparindia/catalog/seller_{$userId}");

        // 2. Local fallback if Cloudinary not available
        if (!$cloudinaryUrl) {
            $targetDir = public_path('storage/catalog_extracted/seller_' . $userId);
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $file->move($targetDir, $fileName);
            $storedUrl = 'storage/catalog_extracted/seller_' . $userId . '/' . $fileName;
        } else {
            $storedUrl = $cloudinaryUrl;
        }

        try {
            \App\Models\SellerMedia::create([
                'user_id' => $userId,
                'filename' => $fileName,
                'file_path' => $storedUrl,
                'content_hash' => $contentHash,
                'file_size' => $fileSize,
                'is_assigned' => false,
                'is_universal' => $isUniversal,
                'permission_granted' => $shareToCentral,
                'category_type' => $categoryType,
            ]);

            if (!in_array(strtolower($categoryType), ['general', 'all', ''])) {
                \App\Models\SellerFolder::firstOrCreate([
                    'user_id' => $userId,
                    'name' => $categoryType,
                ]);
            }
            \Illuminate\Support\Facades\Cache::forget('cloudinary_resources_vyaparindia');
        } catch (\Throwable $e) {
            Log::warning('SellerMedia insert error: ' . $e->getMessage());
        }

        $assetUrl = (str_starts_with($storedUrl, 'http://') || str_starts_with($storedUrl, 'https://')) ? $storedUrl : asset($storedUrl);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $isUniversal ? "Image saved to folder '{$folder}' & contributed to Universal Central Bank!" : "Image saved 100% PRIVATE to folder '{$folder}'.",
                'image' => [
                    'filename' => $fileName,
                    'url' => $storedUrl,
                    'asset_url' => $assetUrl,
                    'folder' => $folder,
                    'category' => $folder,
                    'is_universal' => $isUniversal,
                ],
            ]);
        }

        return back()->with('success', "Image uploaded successfully to folder '{$folder}'!");
    }

    /**
     * Move / Assign multiple images to a specific folder / category.
     */
    public function assignFolder(Request $request)
    {
        $request->validate([
            'image_urls' => 'required|array|min:1',
            'image_urls.*' => 'required|string',
            'folder_name' => 'required|string|max:100',
        ]);

        $userId = Auth::id() ?: 1;
        $folderName = ucwords(strtolower(trim($request->input('folder_name'))));
        $urls = $request->input('image_urls');

        // Ensure folder is registered in SellerFolder table for 100% sync
        if (!in_array(strtolower($folderName), ['general', 'all', ''])) {
            try {
                \App\Models\SellerFolder::firstOrCreate([
                    'user_id' => $userId,
                    'name' => $folderName,
                ]);
                $deletedFolders = \Illuminate\Support\Facades\Cache::get("deleted_gallery_folders_{$userId}", []);
                unset($deletedFolders[strtolower(trim($folderName))]);
                \Illuminate\Support\Facades\Cache::put("deleted_gallery_folders_{$userId}", $deletedFolders, now()->addYears(1));
            } catch (\Throwable $e) {}
        }

        $movedCount = 0;
        $alreadyCount = 0;

        foreach ($urls as $url) {
            $cleanUrl = ltrim($url, '/');
            $baseFn = strtolower(basename(parse_url($url, PHP_URL_PATH)));

            $media = \App\Models\SellerMedia::where('user_id', $userId)
                ->where(function($q) use ($url, $cleanUrl, $baseFn) {
                    $q->where('file_path', $url)
                      ->orWhere('file_path', $cleanUrl)
                      ->orWhere('file_path', '/' . $cleanUrl)
                      ->orWhere('file_path', 'LIKE', '%' . $baseFn);
                })->first();

            if ($media) {
                if (strcasecmp(trim($media->category_type ?? ''), $folderName) === 0) {
                    $alreadyCount++;
                    continue;
                }
                \App\Models\SellerMedia::where('user_id', $userId)
                    ->where(function($q) use ($url, $cleanUrl, $baseFn) {
                        $q->where('file_path', $url)
                          ->orWhere('file_path', $cleanUrl)
                          ->orWhere('file_path', '/' . $cleanUrl)
                          ->orWhere('file_path', 'LIKE', '%' . $baseFn);
                    })->update(['category_type' => $folderName]);
                $movedCount++;
            } else {
                $currentFolder = $this->detectFolderForImage(basename($url), null);
                if (strcasecmp($currentFolder, $folderName) === 0) {
                    $alreadyCount++;
                    continue;
                }

                \App\Models\SellerMedia::create([
                    'user_id' => $userId,
                    'filename' => basename($url),
                    'file_path' => $url,
                    'category_type' => $folderName,
                    'is_assigned' => false,
                    'is_universal' => false,
                    'permission_granted' => false,
                ]);
                $movedCount++;
            }
        }

        \Illuminate\Support\Facades\Cache::forget('cloudinary_resources_vyaparindia');

        if ($movedCount === 0 && $alreadyCount > 0) {
            return response()->json([
                'success' => false,
                'is_duplicate' => true,
                'message' => "Yeh photo(s) pehle se hi '{$folderName}' folder me maujood hain!",
                'already_count' => $alreadyCount,
                'moved_count' => 0,
                'folder_name' => $folderName,
            ]);
        }

        $msg = "{$movedCount} photo(s) successfully '{$folderName}' me move kar di gayi hain!";
        if ($alreadyCount > 0) {
            $msg .= " ({$alreadyCount} photo(s) pehle se hi is folder me thi).";
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            'moved_count' => $movedCount,
            'already_count' => $alreadyCount,
            'folder_name' => $folderName,
        ]);
    }

    /**
     * Update details (Title/Name and Folder/Category) of an individual Media Vault photo.
     */
    public function updateMediaDetails(Request $request)
    {
        $request->validate([
            'image_url' => 'required|string',
            'name' => 'required|string|max:150',
            'folder' => 'nullable|string|max:100',
        ]);

        $userId = Auth::id() ?: 1;
        $url = $request->input('image_url');
        $name = trim($request->input('name'));
        $folder = trim($request->input('folder') ?? 'General') ?: 'General';
        $cleanUrl = ltrim($url, '/');

        $media = \App\Models\SellerMedia::where('user_id', $userId)
            ->where(function($q) use ($url, $cleanUrl) {
                $q->where('file_path', $url)
                  ->orWhere('file_path', $cleanUrl)
                  ->orWhere('file_path', '/' . $cleanUrl);
            })->first();

        if ($media) {
            $media->category_type = $folder;
            $media->filename = $name;
            $media->save();
        } else {
            \App\Models\SellerMedia::create([
                'user_id' => $userId,
                'filename' => $name,
                'file_path' => $url,
                'category_type' => $folder,
                'is_assigned' => false,
                'is_universal' => false,
                'permission_granted' => false,
            ]);
        }

        \Illuminate\Support\Facades\Cache::forget('cloudinary_resources_vyaparindia');

        return response()->json([
            'success' => true,
            'message' => 'Photo details saved successfully!',
            'name' => $name,
            'folder' => $folder,
            'url' => $url,
        ]);
    }

    /**
     * Delete an image from Gallery Vault.
     */
    public function deleteFromGallery(Request $request)
    {
        $request->validate([
            'image_url' => 'required|string',
        ]);

        $userId = Auth::id() ?: 1;
        $rawUrl = $request->input('image_url');

        // 1. Remove from local JSON crop registry
        $this->removeCropFromRegistry($userId, $rawUrl);

        // 2. Delete from Cloudinary if it's a Cloudinary URL
        if (str_contains($rawUrl, 'cloudinary.com')) {
            CloudinaryService::deleteResource($rawUrl);
        }

        // 3. Delete from SellerMedia DB
        try {
            \App\Models\SellerMedia::where(function($q) use ($rawUrl) {
                $q->where('file_path', $rawUrl)
                  ->orWhere('file_path', ltrim($rawUrl, '/'));
            })->delete();
        } catch (\Throwable $e) {}

        // 4. Delete from BrandMasterProduct if exists
        try {
            \App\Models\BrandMasterProduct::where('image_url', $rawUrl)->delete();
        } catch (\Throwable $e) {}

        // 5. Delete physical file if local
        $relUrl = ltrim($rawUrl, '/');
        $fullPath = public_path($relUrl);
        if (file_exists($fullPath) && is_file($fullPath)) {
            @unlink($fullPath);
        }

        // 6. Invalidate Cloudinary cache
        \Illuminate\Support\Facades\Cache::forget('cloudinary_resources_vyaparindia');

        // 7. Add to deleted URLs blacklist so it never resurfaces
        $deletedList = \Illuminate\Support\Facades\Cache::get('vyapar_deleted_image_urls', []);
        $deletedList[$rawUrl] = true;
        $deletedList[$relUrl] = true;
        \Illuminate\Support\Facades\Cache::put('vyapar_deleted_image_urls', $deletedList, now()->addYears(1));

        return response()->json([
            'success' => true,
            'message' => 'Image permanently deleted from Gallery Vault.',
        ]);
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

        $userId = Auth::id() ?: 1;
        $deletedCount = 0;
        $deletedList = \Illuminate\Support\Facades\Cache::get('vyapar_deleted_image_urls', []);

        foreach ($request->input('image_urls') as $url) {
            $this->removeCropFromRegistry($userId, $url);

            if (str_contains($url, 'cloudinary.com')) {
                CloudinaryService::deleteResource($url);
            }

            try {
                \App\Models\SellerMedia::where(function($q) use ($url) {
                    $q->where('file_path', $url)
                      ->orWhere('file_path', ltrim($url, '/'));
                })->delete();
            } catch (\Throwable $e) {}

            try {
                \App\Models\BrandMasterProduct::where('image_url', $url)->delete();
            } catch (\Throwable $e) {}

            $relUrl = ltrim($url, '/');
            $fullPath = public_path($relUrl);
            if (file_exists($fullPath) && is_file($fullPath)) {
                @unlink($fullPath);
            }

            $deletedList[$url] = true;
            $deletedList[$relUrl] = true;
            $deletedCount++;
        }

        \Illuminate\Support\Facades\Cache::forget('cloudinary_resources_vyaparindia');
        \Illuminate\Support\Facades\Cache::put('vyapar_deleted_image_urls', $deletedList, now()->addYears(1));

        return response()->json([
            'success' => true,
            'deleted_count' => $deletedCount,
            'message' => "Successfully deleted {$deletedCount} images.",
        ]);
    }

    /**
     * Create an empty custom Image Folder for seller.
     */
    public function createFolder(Request $request)
    {
        $request->validate([
            'folder_name' => 'required|string|max:100',
        ]);

        $userId = Auth::id() ?: 1;
        $rawFolder = trim($request->input('folder_name'));
        $folderName = trim(html_entity_decode($rawFolder, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $folderName = ucwords(strtolower(preg_replace('/\s+/', ' ', $folderName)));

        if (in_array(strtolower($folderName), ['general', 'all', '']) || strlen($folderName) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Kripya ek valid folder ka naam likhein.',
            ], 422);
        }

        try {
            \App\Models\SellerFolder::firstOrCreate([
                'user_id' => $userId,
                'name' => $folderName,
            ]);

            // Un-blacklist folder if it was previously deleted
            $deletedFolders = \Illuminate\Support\Facades\Cache::get("deleted_gallery_folders_{$userId}", []);
            unset($deletedFolders[strtolower(trim($folderName))]);
            unset($deletedFolders[strtolower(trim($rawFolder))]);
            \Illuminate\Support\Facades\Cache::put("deleted_gallery_folders_{$userId}", $deletedFolders, now()->addYears(1));

            return response()->json([
                'success' => true,
                'message' => "Folder '{$folderName}' safaltapoorvak ban gaya!",
                'folder_name' => $folderName,
            ]);
        } catch (\Throwable $e) {
            Log::error('Create folder error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Folder create karne me error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete an entire image folder / category in Gallery.
     */
    public function deleteFolder(Request $request)
    {
        $request->validate([
            'folder_name' => 'required|string|max:100',
            'action' => 'nullable|string|in:move_to_general,delete_images',
        ]);

        $userId = Auth::id() ?: 1;
        $rawFolder = trim($request->input('folder_name'));
        $folderName = trim(html_entity_decode($rawFolder, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $action = $request->input('action', 'move_to_general');

        if (in_array(strtolower($folderName), ['general', 'all']) || in_array(strtolower($rawFolder), ['general', 'all'])) {
            return response()->json([
                'success' => false,
                'message' => 'General folder ko delete nahi kiya ja sakta.',
            ], 422);
        }

        if ($action === 'delete_images') {
            $medias = \App\Models\SellerMedia::where('user_id', $userId)
                ->where(function($q) use ($rawFolder, $folderName) {
                    $q->whereRaw('LOWER(TRIM(category_type)) = ?', [strtolower($rawFolder)])
                      ->orWhereRaw('LOWER(TRIM(category_type)) = ?', [strtolower($folderName)]);
                })
                ->get();

            $deletedList = \Illuminate\Support\Facades\Cache::get('vyapar_deleted_image_urls', []);
            foreach ($medias as $m) {
                $url = $m->file_path;
                if (str_contains($url, 'cloudinary.com')) {
                    CloudinaryService::deleteResource($url);
                }
                $rel = ltrim($url, '/');
                $fp = public_path($rel);
                if (file_exists($fp) && is_file($fp)) {
                    @unlink($fp);
                }
                $deletedList[$url] = true;
                $deletedList[$rel] = true;
                $m->delete();
            }
            \Illuminate\Support\Facades\Cache::put('vyapar_deleted_image_urls', $deletedList, now()->addYears(1));
            $msg = "Folder '{$folderName}' aur uske saare photos delete kar diye gaye hain!";
        } else {
            \App\Models\SellerMedia::where('user_id', $userId)
                ->where(function($q) use ($rawFolder, $folderName) {
                    $q->whereRaw('LOWER(TRIM(category_type)) = ?', [strtolower($rawFolder)])
                      ->orWhereRaw('LOWER(TRIM(category_type)) = ?', [strtolower($folderName)]);
                })
                ->update(['category_type' => 'General']);
            $msg = "Folder '{$folderName}' delete ho gaya aur photos 'General' me move ho gaye hain!";
        }

        try {
            \App\Models\SellerFolder::where('user_id', $userId)
                ->where(function($q) use ($rawFolder, $folderName) {
                    $q->whereRaw('LOWER(TRIM(name)) = ?', [strtolower($rawFolder)])
                      ->orWhereRaw('LOWER(TRIM(name)) = ?', [strtolower($folderName)]);
                })->delete();
        } catch (\Throwable $e) {}

        // Blacklist deleted folder permanently so it never shows up as empty ghost folder
        $deletedFolders = \Illuminate\Support\Facades\Cache::get("deleted_gallery_folders_{$userId}", []);
        $deletedFolders[strtolower(trim($rawFolder))] = true;
        $deletedFolders[strtolower(trim($folderName))] = true;
        \Illuminate\Support\Facades\Cache::put("deleted_gallery_folders_{$userId}", $deletedFolders, now()->addYears(1));

        \Illuminate\Support\Facades\Cache::forget('cloudinary_resources_vyaparindia');

        return response()->json([
            'success' => true,
            'message' => $msg,
        ]);
    }

    /**
     * Auto-save / Backup Staged Excel Sheet rows for seller so it never gets lost across restarts.
     */
    public function saveStagedSheetDraft(Request $request)
    {
        $userId = Auth::id() ?: 1;
        $rows = $request->input('staged_rows', []);

        \Illuminate\Support\Facades\Cache::put("seller_staged_sheet_draft_{$userId}", [
            'rows' => $rows,
            'updated_at' => now()->toDateTimeString(),
        ], now()->addDays(60));

        return response()->json([
            'success' => true,
            'count' => count($rows),
            'message' => 'Staged sheet draft saved safely to server!',
        ]);
    }

    /**
     * Retrieve Staged Excel Sheet draft from server.
     */
    public function getStagedSheetDraft(Request $request)
    {
        $userId = Auth::id() ?: 1;
        $draft = \Illuminate\Support\Facades\Cache::get("seller_staged_sheet_draft_{$userId}", [
            'rows' => [],
            'updated_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'rows' => $draft['rows'] ?? [],
            'count' => count($draft['rows'] ?? []),
            'updated_at' => $draft['updated_at'] ?? null,
        ]);
    }

    /**
     * 📄 Page 2: Interactive PDF Studio (Side-by-Side Canvas + Real-Time Right Gallery)
     */
    public function pdfStudio(Request $request)
    {
        $userId = Auth::id() ?: 1;
        $galleryImages = $this->getAllGalleryImages($userId);

        $folders = ['General'];
        foreach ($galleryImages as $img) {
            $f = $img['folder'] ?? $img['category'] ?? 'General';
            if ($f && !in_array($f, $folders)) {
                $folders[] = $f;
            }
        }
        try {
            $customFolders = \App\Models\SellerFolder::where('user_id', $userId)->pluck('name')->toArray();
            $folders = array_values(array_unique(array_merge($folders, $customFolders)));
        } catch (\Throwable $e) {}
        sort($folders);

        return view('seller.catalog.pdf_studio', compact('galleryImages', 'folders'));
    }

    /**
     * Crop region from PDF and immediately push to Live Gallery (Cloudinary + Local Storage fallback).
     */
    public function savePdfCropToGallery(Request $request)
    {
        try {
            $request->validate([
                'image_data' => 'required|string',
                'title' => 'nullable|string|max:100',
                'page' => 'nullable|integer',
                'folder' => 'nullable|string|max:100',
            ]);

            $rawData = $request->input('image_data');
            $rawTitle = trim($request->input('title') ?? '');
            $page = $request->input('page', 1);
            $userId = Auth::id() ?: 1;
            $name = $rawTitle ? ucwords(str_replace(['_', '-'], ' ', $rawTitle)) : "Crop (Page {$page})";

            // Extract binary data for SHA-256 hash & local fallback
            $binary = null;
            $ext = 'jpg';
            if (preg_match('/^data:image\/(\w+);base64,/', $rawData, $type)) {
                $ext = strtolower($type[1]) === 'png' ? 'png' : 'jpg';
                $binary = base64_decode(substr($rawData, strpos($rawData, ',') + 1));
            } else {
                $binary = base64_decode($rawData);
            }

            if ($binary === false || empty($binary)) {
                return response()->json(['success' => false, 'message' => 'Malformed image payload'], 422);
            }

            $contentHash = hash('sha256', $binary);
            $fileSize = strlen($binary);
            $shareToCentral = $request->boolean('share_to_central', false);
            $categoryType = $request->input('folder') ?: $request->input('category_type', 'General');

            // ⚡ META/GOOGLE-STYLE SMART DEDUPLICATION:
            // If the seller cropped the exact same area/image before, link to existing image instantly (skip duplicate upload)
            $existingMedia = \App\Models\SellerMedia::where('user_id', $userId)
                ->where('content_hash', $contentHash)
                ->first();

            if ($existingMedia) {
                if ($categoryType && $categoryType !== 'General' && $existingMedia->category_type !== $categoryType) {
                    $existingMedia->update(['category_type' => $categoryType]);
                }

                $existingUrl = $existingMedia->file_path;
                $assetUrl = (str_starts_with($existingUrl, 'http://') || str_starts_with($existingUrl, 'https://')) ? $existingUrl : asset($existingUrl);
                $existingName = $rawTitle ? ucwords(str_replace(['_', '-'], ' ', $rawTitle)) : ucwords(str_replace(['crop_', 'p_', '_', '-'], ' ', pathinfo($existingMedia->filename, PATHINFO_FILENAME)));

                return response()->json([
                    'success' => true,
                    'is_duplicate' => true,
                    'message' => '✓ Yeh photo pehle se aapke vault me maujood hai! Purani photo se link kar diya gaya hai (Duplicate avoided).',
                    'image' => [
                        'id' => md5($existingUrl),
                        'filename' => $existingMedia->filename,
                        'name' => $existingName,
                        'url' => $existingUrl,
                        'asset_url' => $assetUrl,
                        'size_kb' => round(($existingMedia->file_size ?: $fileSize) / 1024, 1),
                        'created_at' => $existingMedia->created_at ? $existingMedia->created_at->format('d M Y, H:i') : date('d M Y, H:i'),
                        'source' => 'personal_vault',
                        'is_universal' => (bool)$existingMedia->is_universal,
                        'permission_granted' => (bool)$existingMedia->permission_granted,
                        'is_mine' => true,
                        'deletable' => true,
                        'folder' => $existingMedia->category_type ?: $categoryType,
                        'category' => $existingMedia->category_type ?: $categoryType,
                    ],
                ]);
            }

            // 1. Cloudinary Priority (Production Cloud CDN)
            $cloudinaryUrl = CloudinaryService::uploadBase64($rawData, "vyaparindia/catalog/seller_{$userId}");
            if ($cloudinaryUrl) {
                $fileName = basename(parse_url($cloudinaryUrl, PHP_URL_PATH));

                // Protect Central Hub from clothes/fashion
                $fashionKeywords = ['cloth', 'dress', 'shirt', 'pant', 'sari', 'saree', 'suit', 'fashion', 'kurti', 'tshirt', 'jeans', 'fabric', 'apparel', 'garment', 'dupatta', 'top', 'tunic'];
                $isFashion = false;
                $nameLower = strtolower($name . ' ' . $categoryType);
                foreach ($fashionKeywords as $kw) {
                    if (str_contains($nameLower, $kw)) {
                        $isFashion = true;
                        break;
                    }
                }
                $isUniversal = $shareToCentral && !$isFashion;

                $newImage = [
                    'id' => md5($cloudinaryUrl),
                    'filename' => $fileName,
                    'name' => $name,
                    'url' => $cloudinaryUrl,
                    'asset_url' => $cloudinaryUrl,
                    'size_kb' => round($fileSize / 1024, 1),
                    'created_at' => date('d M Y, H:i'),
                    'source' => 'personal_vault',
                    'is_universal' => $isUniversal,
                    'permission_granted' => $shareToCentral,
                    'is_mine' => true,
                    'deletable' => true,
                    'folder' => $categoryType,
                    'category' => $categoryType,
                ];
                $this->addCropToRegistry($userId, $newImage);
                try {
                    \App\Models\SellerMedia::create([
                        'user_id' => $userId,
                        'filename' => $fileName,
                        'file_path' => $cloudinaryUrl,
                        'content_hash' => $contentHash,
                        'file_size' => $fileSize,
                        'is_assigned' => false,
                        'is_universal' => $isUniversal,
                        'permission_granted' => $shareToCentral,
                        'category_type' => $categoryType,
                    ]);

                    if (!in_array(strtolower($categoryType), ['general', 'all', ''])) {
                        \App\Models\SellerFolder::firstOrCreate([
                            'user_id' => $userId,
                            'name' => $categoryType,
                        ]);
                    }
                } catch (\Throwable $e) {
                    Log::warning('SellerMedia insert error: ' . $e->getMessage());
                }

                \Illuminate\Support\Facades\Cache::forget('cloudinary_resources_vyaparindia');

                return response()->json([
                    'success' => true,
                    'message' => $isUniversal ? 'Crop saved to your vault & contributed to Universal Central Bank!' : 'Crop saved 100% PRIVATE to your personal vault.',
                    'image' => $newImage,
                ]);
            }

            // 2. Safe Local Storage Fallback

            $targetDir = public_path('storage/catalog_extracted/seller_' . $userId);
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0777, true);
            }

            if (!is_dir($targetDir)) {
                $targetDir = storage_path('app/public/catalog_extracted/seller_' . $userId);
                if (!is_dir($targetDir)) {
                    @mkdir($targetDir, 0777, true);
                }
            }

            $titleSlug = Str::slug($rawTitle ?: 'item') ?: 'item';
            $fileName = "crop_p{$page}_{$titleSlug}_" . time() . '.' . $ext;
            $fullPath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

            @file_put_contents($fullPath, $binary);

            $relUrl = 'storage/catalog_extracted/seller_' . $userId . '/' . $fileName;
            $sizeKb = file_exists($fullPath) ? round(filesize($fullPath) / 1024, 1) : 30.0;

            $newImage = [
                'id' => md5($relUrl),
                'filename' => $fileName,
                'name' => $name,
                'url' => $relUrl,
                'asset_url' => asset($relUrl),
                'size_kb' => $sizeKb,
                'created_at' => date('d M Y, H:i'),
                'source' => 'custom_crop',
                'deletable' => true,
                'folder' => $categoryType,
                'category' => $categoryType,
            ];
            $this->addCropToRegistry($userId, $newImage);
            try {
                \App\Models\SellerMedia::create([
                    'user_id' => $userId,
                    'filename' => $fileName,
                    'file_path' => $relUrl,
                    'content_hash' => $contentHash,
                    'file_size' => $fileSize,
                    'category_type' => $categoryType,
                    'is_assigned' => false,
                ]);

                if (!in_array(strtolower($categoryType), ['general', 'all', ''])) {
                    \App\Models\SellerFolder::firstOrCreate([
                        'user_id' => $userId,
                        'name' => $categoryType,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('SellerMedia local insert error: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Image saved to your Gallery Vault!',
                'image' => $newImage,
            ]);
        } catch (\Throwable $e) {
            Log::error('PDF Crop Save Exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Save error: ' . $e->getMessage(),
            ], 500);
        }
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
            'page_image' => 'nullable|string',
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
        $pageImage = $request->input('page_image');

        $masterFile = storage_path("app/catalogs/master_sheets/seller_" . Auth::id() . "_master.json");
        $masterContext = "";
        if (\Illuminate\Support\Facades\File::exists($masterFile)) {
            try {
                $mData = json_decode(\Illuminate\Support\Facades\File::get($masterFile), true);
                $auditCtrl = new \App\Http\Controllers\Seller\CatalogAuditController();
                $audit = $auditCtrl->performAudit(Auth::id(), $mData['rows'] ?? []);
                $masterContext = "\nSeller's Saved Master Excel Sheet: '{$mData['filename']}' ({$audit['total_excel_rows']} rows). Store Status: {$audit['total_live_cards']} Live Cards, {$audit['total_live_variants']} Items. Audit Result: {$audit['matched_count']} Items 100% Matched, {$audit['mismatch_count']} Rate Mismatches, {$audit['missing_count']} Missing.\n";
            } catch (\Throwable $e) {}
        }

        $hasImage = ($pageImage && str_starts_with($pageImage, 'data:image'));

        $systemPrompt = "You are VyaparIndia AI Catalog Copilot, an expert universal AI catalog digitizer and business assistant for ALL wholesale B2B and retail sectors across India (Hardware, Hand Tools, Measuring Tapes, Blades, Sanitaryware, Electricals, Pipes & Fittings, FMCG, Grocery, Clothing & Apparel, Paints, Building Materials, Agriculture, Electronics, and Automobiles).
{$masterContext}Current Catalog Page: Page {$page}." . ($hasImage ? "\n(A high-resolution image of this active catalog page is provided. You MUST examine the page image directly with Optical Character Recognition (OCR) to extract every visible product name, series, size, dimension, and MRP even if digital text is empty or scanned.)" : "") . "
Extracted digital text lines on active page (ordered line-by-line horizontally from top to bottom):
\"\"\"
" . substr($pageText, 0, 12000) . "
\"\"\"

User question: \"{$prompt}\"

CRITICAL INSTRUCTIONS FOR UNIVERSAL CATALOG ROW, SPECS & PRICE MATCHING:
1. In the text or image above, each line represents a single horizontal row across the catalog page.
2. The price/MRP on a line STRICTLY BELONGS to the product variant or size on THAT SAME LINE.
3. NEVER mix, swap, or borrow prices from other lines or lower down the page! Do NOT assign bottom table rates to items at the top.
4. When a product heading appears above a table of items/sizes, use that heading as the \"Product Name\" for all rows in that section.
5. NEVER IGNORE DESCRIPTIONS, SERIES, OR SPECIFICATIONS: Always extract the product's series, model name, material or sub-description (e.g. 'Auto Stop', 'Neo Lock', 'First Choice', 'Pro Model', 'Jupiter', 'Steel Strip', 'Schedule 40', 'Brass Insert', 'High Gloss') into the Description / Series column!
6. ACCURATE SIZES & DIMENSIONS (NO HALLUCINATIONS):
   - For measuring tapes and tools: extract tape dimensions (e.g. '3 Meter x 16mm', '5 Meter x 19mm', '10m x 25mm' from codes like 3016, 5019, 1025 or text). NEVER hallucinate plumbing pipe sizes (like 1/2\", 3/4\") for non-pipe items!
   - For plumbing/pipes: extract pipe sizes (e.g. '1/2 inch (15mm)', '1 inch (25mm)', '110mm').
   - For paints/chemicals: extract volume/weight (e.g. '1 Ltr', '4 Ltr', '20 Ltr', '250ml').
   - DO NOT default everything to 'Standard' if size, length, or dimension is identifiable.
7. If no price is visible for an item on its row, mark MRP as '-' or '0'. Never invent or hallucinate prices.
8. Always format product listings as a neat Markdown Table with these 5 columns:
| Product Name | Description / Series | Size / Dimension | MRP (INR) | Pack / Code |";

        // If OpenAI key is present (supports Vision if pageImage is sent)
        if ($openAiKey && (str_starts_with($openAiKey, 'sk-') || !$geminiKey)) {
            try {
                $userContent = [];
                if ($hasImage) {
                    $userContent = [
                        [
                            'type' => 'text',
                            'text' => "USER QUESTION: \"{$prompt}\"\n\nPlease analyze Page {$page} using the attached image and extracted reference text.",
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $pageImage,
                                'detail' => 'high',
                            ],
                        ],
                    ];
                } else {
                    $userContent = $prompt;
                }

                $response = Http::withToken($openAiKey)
                    ->timeout(45)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => $userContent],
                        ],
                        'temperature' => 0.1,
                    ]);

                if ($response->successful()) {
                    $reply = $response->json('choices.0.message.content');
                    return response()->json([
                        'success' => true,
                        'reply' => $reply,
                        'provider' => 'OpenAI (GPT-4o mini' . ($hasImage ? ' Vision' : '') . ')',
                    ]);
                } else {
                    $errMsg = $response->json('error.message') ?? 'OpenAI API error occurred.';
                    return response()->json(['success' => false, 'message' => $errMsg], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'OpenAI connection error: ' . $e->getMessage()], 500);
            }
        }

        // If Google Gemini key is present (supports Vision if pageImage is sent)
        if ($geminiKey) {
            try {
                $parts = [];
                if ($hasImage) {
                    $imgParts = explode(',', $pageImage, 2);
                    $base64Only = $imgParts[1] ?? $pageImage;
                    $mime = 'image/jpeg';
                    if (str_contains($imgParts[0], 'image/png')) $mime = 'image/png';

                    $parts = [
                        ['text' => $systemPrompt . "\n\nUser Question: \"{$prompt}\""],
                        [
                            'inline_data' => [
                                'mime_type' => $mime,
                                'data' => $base64Only,
                            ],
                        ],
                    ];
                } else {
                    $parts = [
                        ['text' => $systemPrompt . "\n\nUser Question: \"{$prompt}\""]
                    ];
                }

                $response = Http::timeout(45)
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$geminiKey}", [
                        'contents' => [
                            ['parts' => $parts]
                        ],
                        'generationConfig' => [
                            'temperature' => 0.1,
                        ]
                    ]);

                if ($response->successful()) {
                    $reply = $response->json('candidates.0.content.parts.0.text');
                    return response()->json([
                        'success' => true,
                        'reply' => $reply,
                        'provider' => 'Google Gemini 1.5 Flash' . ($hasImage ? ' Vision' : ''),
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
     * 🤖 Extract Tabular Product Rows from ONLY the Single Active PDF Page via AI.
     * Guaranteed single-page context — strictly conserves tokens as requested by user.
     */
    public function aiExtractTableFromPage(Request $request)
    {
        $request->validate([
            'page' => 'required|integer',
            'page_text' => 'nullable|string',
            'page_image' => 'nullable|string',
            'api_key' => 'nullable|string',
        ]);

        $userKey = trim($request->input('api_key') ?? '');
        $openAiKey = (str_starts_with($userKey, 'sk-')) 
            ? $userKey 
            : (env('OPENAI_KEY') ?: env('OPENAI_API_KEY') ?: ($userKey ?: null));

        $geminiKey = (str_starts_with($userKey, 'AIza'))
            ? $userKey
            : (env('GEMINI_API_KEY') ?: env('GEMINI_KEY') ?: ($userKey ?: null));

        $page = $request->input('page');
        $pageText = substr($request->input('page_text') ?? '', 0, 20000);
        $pageImage = $request->input('page_image'); // Base64 data URL

        $prompt = "You are a precise data-extraction engine. You will be shown ONE page of a product catalogue/price list (image or text). Extract every product on this page into JSON. Accuracy matters more than completeness: never guess.

STEP 1: Understand the page layout first.
Look at the page and identify:
- Is it a table, a grid of product cards, or a plain list?
- What are the exact column headings or labels printed on the page (copy them exactly as printed)?
- Any page-level info that applies to all products (company/brand name, category, series name, GST %, \"all prices in INR\", unit like pcs/box). 
Put this in \"layout\".

STEP 2: Extract products.
Rules:
1. One row in the table or one product card = one object in \"products\". If one product has multiple variants (sizes, colors, wattages) each with its own code or price, make a SEPARATE object for each variant and repeat the common product_name.
2. Copy text EXACTLY as printed: same spelling, same hyphens, same capital letters, same digits. Do not correct, translate, or reformat codes.
3. Field meaning:
   - model_no: only if the page labels it \"Model\", \"Model No\", \"Design No\", \"Type\".
   - item_code: only if labeled \"Code\", \"Item Code\", \"SKU\", \"Cat No\", \"Part No\".
   - If the page has only ONE kind of code and the label is unclear, put it in item_code and leave model_no null.
   - NEVER put the same value in both model_no and item_code. NEVER swap them.
4. If a value is not visibly printed for that product, use null. Do not infer, calculate, or copy from neighbouring rows.
5. Merged or blank cells: if a cell spans multiple rows (e.g., a category or product name), apply that value to all rows it spans.
6. Prices: numbers only, no currency symbol or commas (e.g., \"₹1,250\" becomes 1250). Keep MRP and selling/dealer price separate according to column labels. If unsure which is which, put it in \"price\" and add a note.
7. Ignore headers, footers, page numbers, phone numbers, addresses, logos, and advertisements. They are not products.
8. Read row by row, left to right, top to bottom. Do not skip rows. Do not repeat rows. Do not invent rows to fill space.
9. If text is blurry or unreadable, set that field to null and add a note. Do not guess characters.

OUTPUT: Return ONLY valid JSON, no markdown, no explanation, in exactly this shape:

{
  \"layout\": {
    \"type\": \"table | cards | list\",
    \"column_labels_found\": [\"...\"],
    \"brand\": null,
    \"category\": null,
    \"common_notes\": null
  },
  \"products\": [
    {
      \"row_no\": 1,
      \"product_name\": null,
      \"model_no\": null,
      \"item_code\": null,
      \"size\": null,
      \"color\": null,
      \"specifications\": null,
      \"unit\": null,
      \"mrp\": null,
      \"price\": null,
      \"gst_percent\": null,
      \"description\": null,
      \"notes\": null
    }
  ],
  \"total_products_on_page\": 0
}

Before answering, count the products on the page and make sure \"total_products_on_page\" equals the length of \"products\".";

        $reply = null;
        $provider = null;

        // 1. Try OpenAI (Supports Vision if page_image is present)
        if ($openAiKey && (str_starts_with($openAiKey, 'sk-') || !$geminiKey)) {
            try {
                $userMessages = [];
                if ($pageImage && str_starts_with($pageImage, 'data:image')) {
                    $userMessages = [
                        [
                            'type' => 'text',
                            'text' => $prompt . "\n\nPAGE {$page} EXTRACTED TEXT (Reference):\n\"\"\"\n{$pageText}\n\"\"\""
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $pageImage,
                                'detail' => 'high'
                            ]
                        ]
                    ];
                } else {
                    $userMessages = $prompt . "\n\nPAGE {$page} TEXT CONTENT:\n\"\"\"\n{$pageText}\n\"\"\"";
                }

                $response = Http::withToken($openAiKey)
                    ->timeout(45)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            ['role' => 'system', 'content' => 'You are a precise data-extraction engine that outputs ONLY valid JSON.'],
                            ['role' => 'user', 'content' => $userMessages],
                        ],
                        'temperature' => 0.0,
                    ]);

                if ($response->successful()) {
                    $reply = $response->json('choices.0.message.content');
                    $provider = 'OpenAI (GPT-4o mini' . ($pageImage ? ' Vision' : '') . ')';
                }
            } catch (\Throwable $e) {
                Log::warning('AI Table extract OpenAI error: ' . $e->getMessage());
            }
        }

        // 2. Try Gemini if no reply yet (Supports Vision if page_image is present)
        if (!$reply && $geminiKey) {
            try {
                $parts = [];
                if ($pageImage && str_starts_with($pageImage, 'data:image')) {
                    $imgParts = explode(',', $pageImage, 2);
                    $base64Only = $imgParts[1] ?? $pageImage;
                    $mime = 'image/jpeg';
                    if (str_contains($imgParts[0], 'image/png')) $mime = 'image/png';

                    $parts = [
                        ['text' => $prompt . "\n\nPAGE {$page} EXTRACTED TEXT (Reference):\n\"\"\"\n{$pageText}\n\"\"\""],
                        [
                            'inline_data' => [
                                'mime_type' => $mime,
                                'data' => $base64Only
                            ]
                        ]
                    ];
                } else {
                    $parts = [
                        ['text' => $prompt . "\n\nPAGE {$page} TEXT CONTENT:\n\"\"\"\n{$pageText}\n\"\"\""]
                    ];
                }

                $response = Http::timeout(45)
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$geminiKey}", [
                        'contents' => [
                            ['parts' => $parts]
                        ],
                        'generationConfig' => [
                            'temperature' => 0.0,
                            'responseMimeType' => 'application/json'
                        ]
                    ]);

                if ($response->successful()) {
                    $reply = $response->json('candidates.0.content.parts.0.text');
                    $provider = 'Google Gemini 1.5 Flash' . ($pageImage ? ' Vision' : '');
                }
            } catch (\Throwable $e) {
                Log::warning('AI Table extract Gemini error: ' . $e->getMessage());
            }
        }

        if (!$reply) {
            return response()->json([
                'success' => false,
                'message' => 'AI API Key nahi mili ya connection fail hua. Kripya .env ya Render me OPENAI_KEY / GEMINI_API_KEY dalein, ya AI Copilot panel me key paste karein.',
            ], 400);
        }

        // Clean JSON response (strip markdown fences if present)
        $cleanJson = trim($reply);
        $cleanJson = preg_replace('/^```(?:json)?\s*/i', '', $cleanJson);
        $cleanJson = preg_replace('/\s*```$/i', '', $cleanJson);
        $cleanJson = trim($cleanJson);

        $parsed = json_decode($cleanJson, true);
        if (!is_array($parsed)) {
            // Fallback: regex match json object or array
            if (preg_match('/\{[\s\S]*\}/', $cleanJson, $matches)) {
                $parsed = json_decode($matches[0], true);
            } elseif (preg_match('/\[[\s\S]*\]/', $cleanJson, $matches)) {
                $parsed = json_decode($matches[0], true);
            }
        }

        if (!is_array($parsed)) {
            return response()->json([
                'success' => false,
                'message' => 'AI is page se structured table extract nahi kar paya.',
                'raw_reply' => $reply,
            ], 422);
        }

        // Extract layout and products array
        $layout = $parsed['layout'] ?? [];
        $rawProducts = $parsed['products'] ?? (isset($parsed[0]) ? $parsed : []);

        if (!is_array($rawProducts) || empty($rawProducts)) {
            return response()->json([
                'success' => false,
                'message' => 'Is page par koi product rows detect nahi huin.',
                'raw_reply' => $reply,
            ], 422);
        }

        // Normalize rows strictly preserving separate model_no and item_code
        $normalizedRows = [];
        foreach ($rawProducts as $idx => $p) {
            $pName = trim($p['product_name'] ?? ($p['name'] ?? ''));
            if (!$pName) {
                $pName = "Product " . ($idx + 1);
            }

            $modelNo = !empty($p['model_no']) ? trim($p['model_no']) : null;
            $itemCode = !empty($p['item_code']) ? trim($p['item_code']) : (!empty($p['sku']) ? trim($p['sku']) : null);

            // Rule 3: If only one code exists, ensure it is in item_code / sku, never swap
            $sku = $itemCode ?: ($modelNo ?: '');

            $mrp = is_numeric($p['mrp'] ?? null) ? (float)$p['mrp'] : (is_numeric($p['price'] ?? null) ? (float)$p['price'] : 0);
            $price = is_numeric($p['price'] ?? null) ? (float)$p['price'] : $mrp;

            $normalizedRows[] = [
                'row_no' => $p['row_no'] ?? ($idx + 1),
                'name' => $pName,
                'product_name' => $pName,
                'model_no' => $modelNo,
                'item_code' => $itemCode,
                'sku' => $sku,
                'size' => !empty($p['size']) ? trim($p['size']) : 'Standard',
                'color' => !empty($p['color']) ? trim($p['color']) : '',
                'description' => !empty($p['description']) ? trim($p['description']) : (!empty($p['specifications']) ? trim($p['specifications']) : ''),
                'specifications' => !empty($p['specifications']) ? trim($p['specifications']) : '',
                'unit' => !empty($p['unit']) ? trim($p['unit']) : '',
                'packing_1' => !empty($p['unit']) ? trim($p['unit']) : (!empty($p['packing_1']) ? trim($p['packing_1']) : ''),
                'packing_2' => !empty($p['packing_2']) ? trim($p['packing_2']) : '',
                'mrp' => $mrp,
                'price' => $price,
                'gst_percent' => is_numeric($p['gst_percent'] ?? null) ? (float)$p['gst_percent'] : 18,
                'category' => !empty($layout['category']) ? trim($layout['category']) : (!empty($p['category']) ? trim($p['category']) : 'General Hardware'),
                'brand' => !empty($layout['brand']) ? trim($layout['brand']) : (!empty($p['brand']) ? trim($p['brand']) : ''),
                'notes' => $p['notes'] ?? null,
            ];
        }

        return response()->json([
            'success' => true,
            'page' => $page,
            'layout' => $layout,
            'rows' => $normalizedRows,
            'total_rows' => count($normalizedRows),
            'provider' => $provider,
        ]);
    }

    /**
     * 📱 AI Universal Raw Text / WhatsApp Price List Parser
     * Extracts multi-item, multi-thickness, multi-unit price lists from raw unstructured messages.
     * Understands Indian trade notations: "46/-", "30/25", "18m(14)-46/-", "Single - 79/-", "As Per Order Garjan Face Available +2/50 Sqft".
     */
    public function aiParseRawPriceText(Request $request)
    {
        $request->validate([
            'raw_text' => 'required|string',
            'api_key' => 'nullable|string',
            'category' => 'nullable|string',
        ]);

        $rawText = trim($request->input('raw_text'));
        if (empty($rawText)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide raw text to parse.',
            ], 400);
        }

        $userKey = trim($request->input('api_key') ?? '');
        $openAiKey = (str_starts_with($userKey, 'sk-')) 
            ? $userKey 
            : (env('OPENAI_KEY') ?: env('OPENAI_API_KEY') ?: ($userKey ?: null));

        $geminiKey = (str_starts_with($userKey, 'AIza'))
            ? $userKey
            : (env('GEMINI_API_KEY') ?: env('GEMINI_KEY') ?: ($userKey ?: null));

        $prompt = "You are an expert Indian wholesale B2B and trade price list extraction AI.
The user pasted raw text from a WhatsApp message, dealer quotation, or trade SMS.
The data can be from ANY industry: Plywood, Timber, Block Boards, Flush Doors, Laminates, Hardware, Sanitary, Pipes & Fittings, Electricals, Tiles, Steel, FMCG, etc.
Extract every product and its pricing accurately into JSON.

IMPORTANT RULES & TRADE CONVENTIONS:
1. NEVER guess or invent data. If a field is not present, use null or appropriate default.
2. Trade price notation conventions in India:
   - '46/-' or '46' or '46.00' = 46.0
   - '30/25' = 30.25 (30 rupees 25 paise)
   - '52/50' = 52.50
   - '71/75' = 71.75
   - '18m(14)-46/-' means thickness '18mm (14)' with base rate 46.0
   - 'Single - 79/-' means variant 'Single' with base rate 79.0
3. Multiple variants on a single line:
   - For example: '18m(14)-46/-, 12m(8)-30/25, 6m(5)-22/-' under 'Super Local'
   - Split this into 3 separate product variant objects:
     1) product_name: 'Super Local', variant: '18mm (14)', base_rate: 46.0
     2) product_name: 'Super Local', variant: '12mm (8)', base_rate: 30.25
     3) product_name: 'Super Local', variant: '6mm (5)', base_rate: 22.0
4. Units:
   - Detect appropriate unit: 'sqft' (common for Plywood, Boards, Doors, Glass, Marble, Tiles), 'pcs', 'meter', 'kg', 'box', 'bag', 'sheet'.
   - If in doubt for Plywood/Block Boards/Doors, default unit is 'sqft'.
5. Notes & Conditions:
   - Preserve special notes like 'As Per Order Garjan Face Available +2/50 Sqft', 'Double Door 10% extra', 'Above 84\" Height plus 5/- sqft' into 'notes'.

RAW TEXT TO PARSE:
\"\"\"
{$rawText}
\"\"\"

OUTPUT STRICTLY IN JSON FORMAT ONLY (no markdown fences, no explanation):
{
  \"title\": \"Detected title or date like '14.9.26 Price List' or null\",
  \"common_notes\": \"General terms like 'Ex Godown Rates, All With Bills Rates' or null\",
  \"detected_unit\": \"sqft\",
  \"items\": [
    {
      \"category\": \"Category name (e.g. Plywood, Block Boards, Shuttering Ply, Flush Doors, Hardware)\",
      \"product_name\": \"Clean Product Name (e.g. Super Local, Kerla Hardwood Best Quality, Gujrat H/W MR ISI)\",
      \"variant\": \"Variant / Thickness / Size / Specification (e.g. 18mm (14), 12mm, 25kg Ambossed RED DAIMOND 19-20kg-10m)\",
      \"unit\": \"sqft | pcs | meter | box | kg | sheet\",
      \"base_rate\": 46.00,
      \"notes\": \"Any specific note or condition or null\"
    }
  ]
}";

        $reply = null;
        $provider = null;

        // 1. Try OpenAI
        if ($openAiKey && (str_starts_with($openAiKey, 'sk-') || !$geminiKey)) {
            try {
                $response = Http::withToken($openAiKey)
                    ->timeout(45)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            ['role' => 'system', 'content' => 'You are a precise data extraction engine. Output ONLY valid JSON.'],
                            ['role' => 'user', 'content' => $prompt],
                        ],
                        'temperature' => 0.0,
                    ]);

                if ($response->successful()) {
                    $reply = $response->json('choices.0.message.content');
                    $provider = 'OpenAI (GPT-4o mini)';
                }
            } catch (\Throwable $e) {
                Log::warning('AI Raw Price parse OpenAI error: ' . $e->getMessage());
            }
        }

        // 2. Try Gemini
        if (!$reply && $geminiKey) {
            try {
                $response = Http::timeout(45)
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$geminiKey}", [
                        'contents' => [
                            ['parts' => [['text' => $prompt]]]
                        ],
                        'generationConfig' => [
                            'temperature' => 0.0,
                            'responseMimeType' => 'application/json'
                        ]
                    ]);

                if ($response->successful()) {
                    $reply = $response->json('candidates.0.content.parts.0.text');
                    $provider = 'Google Gemini 1.5 Flash';
                }
            } catch (\Throwable $e) {
                Log::warning('AI Raw Price parse Gemini error: ' . $e->getMessage());
            }
        }

        if (!$reply) {
            return response()->json([
                'success' => false,
                'message' => 'AI API Key nahi mili ya connection fail hua. Aap "Instant Fast Parse" use kar sakte hain ya AI Copilot me API key configure karein.',
            ], 400);
        }

        // Clean JSON response
        $cleanJson = trim($reply);
        $cleanJson = preg_replace('/^```(?:json)?\s*/i', '', $cleanJson);
        $cleanJson = preg_replace('/\s*```$/i', '', $cleanJson);
        $cleanJson = trim($cleanJson);

        $parsed = json_decode($cleanJson, true);
        if (!is_array($parsed)) {
            if (preg_match('/\{[\s\S]*\}/', $cleanJson, $matches)) {
                $parsed = json_decode($matches[0], true);
            }
        }

        if (!is_array($parsed) || empty($parsed['items'])) {
            return response()->json([
                'success' => false,
                'message' => 'AI raw text se structured products detect nahi kar paya.',
                'raw_reply' => $reply,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'title' => $parsed['title'] ?? null,
            'common_notes' => $parsed['common_notes'] ?? null,
            'detected_unit' => $parsed['detected_unit'] ?? 'sqft',
            'items' => $parsed['items'],
            'total_items' => count($parsed['items']),
            'provider' => $provider,
        ]);
    }

    /**
     * 🔄 Load all existing live product cards and variants directly into Excel Mapper Spreadsheet.
     * Enables 1 single source of truth: changes reflect in the sheet and sync to cards automatically!
     */
    public function loadLiveCardsToSheet(Request $request)
    {
        $userId = Auth::id() ?: 1;
        $products = Product::where('user_id', $userId)
            ->with(['category', 'variants'])
            ->orderBy('name')
            ->get();

        if ($products->isEmpty()) {
            return back()->withErrors(['आपके स्टोर पर अभी कोई लाइव प्रोडक्ट्स उपलब्ध नहीं हैं। पहले एक्सेल शीट अपलोड करें।']);
        }

        $cardItems = [];
        $totalVariants = 0;

        foreach ($products as $p) {
            $variants = [];
            if ($p->variants && $p->variants->isNotEmpty()) {
                foreach ($p->variants as $v) {
                    $totalVariants++;
                    $variants[] = [
                        'variant_name' => $v->variant_name,
                        'size' => $v->size ?: $v->variant_name,
                        'product_code' => $v->sku,
                        'sku' => $v->sku,
                        'hsn_code' => $p->hsn_code ?: '39174000',
                        'mrp' => (float)($v->mrp ?: $p->mrp),
                        'retail_price' => (float)($v->retail_price ?: ($v->price ?: $p->price)),
                        'wholesale_price' => (float)($v->wholesale_price ?: $v->retail_price),
                        'raw_rate' => (float)$v->purchase_price,
                        'cost_price_2' => (float)$v->wholesale_price,
                        'cost_price_3' => 0,
                        'stock_quantity' => (int)$v->stock_quantity,
                        'packing_1' => $v->attributes['packing_1'] ?? '',
                        'packing_2' => $v->attributes['packing_2'] ?? '',
                    ];
                }
            } else {
                $totalVariants++;
                $variants[] = [
                    'variant_name' => 'Standard',
                    'size' => 'Standard',
                    'product_code' => $p->sku,
                    'sku' => $p->sku,
                    'hsn_code' => $p->hsn_code ?: '39174000',
                    'mrp' => (float)$p->mrp,
                    'retail_price' => (float)$p->price,
                    'wholesale_price' => (float)($p->wholesale_price ?: $p->price),
                    'raw_rate' => (float)$p->purchase_price,
                    'cost_price_2' => (float)($p->wholesale_price ?: $p->price),
                    'cost_price_3' => 0,
                    'stock_quantity' => (int)$p->stock_quantity,
                    'packing_1' => '',
                    'packing_2' => '',
                ];
            }

            $cardItems[] = [
                'name' => $p->name,
                'category' => $p->category->name ?? ($p->group_name ?: 'Plumbing'),
                'group_type' => $p->group_name ?: ($p->category->name ?? 'Plumbing'),
                'brand' => $p->brand ?: 'Plasto',
                'image_url' => $p->image,
                'hsn_code' => $p->hsn_code ?: '39174000',
                'variants' => $variants,
            ];
        }

        $sheetName = 'Live Store Cards (' . count($cardItems) . ' Cards, ' . $totalVariants . ' Sizes)';

        $job = CatalogIngestionJob::updateOrCreate(
            [
                'user_id' => $userId,
                'filename' => 'LIVE_CARDS_MASTER_SYNC.xlsx',
            ],
            [
                'status' => 'draft',
                'total_products_detected' => count($cardItems),
                'extracted_data' => [
                    'sheet_name' => $sheetName,
                    'products' => $cardItems,
                ],
            ]
        );

        return redirect()->route('seller.catalog.excel_mapper', $job->id)
            ->with('success', "✅ लाइव स्टोर के सभी " . count($cardItems) . " मुख्य कार्ड्स और " . $totalVariants . " साइज़ वेरिएंट्स एक्सेल शीट में लोड हो गए हैं! अब आप यहाँ से रेट्स या नाम बदलकर 1-क्लिक में सभी कार्ड्स में सिंक कर सकते हैं।");
    }

    /**
     * 📊 Page 3: Excel Multi-Row Mapper (Select 6-8 Lines -> 1-Click Batch Image Link)
     */
    public function excelMapper(Request $request, $jobId = null)
    {
        $userId = Auth::id() ?: 1;
        $job = null;

        if ($jobId) {
            $job = CatalogIngestionJob::where('id', $jobId)->where('user_id', $userId)->first();
        } elseif ($request->has('job_id')) {
            $job = CatalogIngestionJob::where('id', $request->input('job_id'))->where('user_id', $userId)->first();
        }

        // Only load un-published/pending draft job if explicitly requested with ?resume_job=1
        if (!$job && $request->has('resume_job')) {
            $job = CatalogIngestionJob::where('user_id', $userId)
                ->where('status', '!=', 'published')
                ->latest()
                ->first();
        }

        $recentJobs = CatalogIngestionJob::where('user_id', $userId)->latest()->take(5)->get();

        $liveProducts = Product::where('user_id', $userId)
            ->with(['variants', 'category'])
            ->latest()
            ->get();

        if (!$job) {
            $galleryImages = $this->getAllGalleryImages($userId);
            $imageFolders = [];
            foreach ($galleryImages as $img) {
                $f = $img['folder'] ?? 'General';
                $imageFolders[$f] = ($imageFolders[$f] ?? 0) + 1;
            }
            ksort($imageFolders);

            return view('seller.catalog.excel_mapper', [
                'job' => null,
                'products' => [],
                'flatRows' => [],
                'categories' => Category::all(),
                'galleryImages' => $galleryImages,
                'imageFolders' => $imageFolders,
                'recentJobs' => $recentJobs,
                'liveProducts' => $liveProducts,
                'upvcCount' => 0,
                'cpvcCount' => 0,
                'swrCount' => 0,
                'otherCount' => 0,
            ]);
        }

        $isJobPublished = ($job->status === 'published');
        $extractedData = $job->extracted_data ?? ['products' => []];
        // If job is already published, do NOT dump old products back into pending draft workspace
        $products = ($isJobPublished && !$request->has('edit_draft')) ? [] : ($extractedData['products'] ?? []);
        $categories = Category::all();

        // Categorize each product preserving real sheet/custom categories
        $categoryCounts = [];
        foreach ($products as $pIdx => &$prod) {
            $pName = strtoupper($prod['name'] ?? '');
            $pCat = trim($prod['category'] ?? '');

            if (!empty($pCat) && !in_array(strtoupper($pCat), ['INDUSTRIAL & COMMERCIAL', 'UNCATEGORIZED', 'OTHER'])) {
                $groupType = $pCat;
            } elseif (!empty($prod['group_type']) && !in_array($prod['group_type'], ['UPVC', 'CPVC', 'SWR', 'AGRI_OTHER'])) {
                $groupType = $prod['group_type'];
            } else {
                if (str_contains($pName, 'PAINT') || str_contains($pName, 'EMULSION') || str_contains($pName, 'DISTEMPER') || str_contains($pName, 'PRIMER') || str_contains($pName, 'ENAMEL')) {
                    $groupType = 'Paints & Coatings';
                } elseif (str_contains($pName, 'SWITCH') || str_contains($pName, 'WIRE') || str_contains($pName, 'CABLE') || str_contains($pName, 'MCB') || str_contains($pName, 'SOCKET')) {
                    $groupType = 'Electrical & Wiring';
                } elseif (str_contains($pName, 'PLY') || str_contains($pName, 'DOOR') || str_contains($pName, 'BEAT') || str_contains($pName, 'LAMINATE')) {
                    $groupType = 'Plywood & Hardware';
                } elseif (str_contains($pName, 'PUMP') || str_contains($pName, 'MOTOR') || str_contains($pName, 'SUBMERSIBLE')) {
                    $groupType = 'Pumps & Motors';
                } elseif (str_contains($pName, 'CPVC')) {
                    $groupType = 'CPVC';
                } elseif (str_contains($pName, 'UPVC')) {
                    $groupType = 'UPVC';
                } elseif (str_contains($pName, 'SWR') || str_contains($pName, 'TRAP') || str_contains($pName, 'VENT') || str_contains($pName, 'COWL') || str_contains($pName, 'DRAIN')) {
                    $groupType = 'SWR';
                } elseif (str_contains($pName, 'AGRI') || str_contains($pName, 'SOLVENT') || str_contains($pName, 'CEMENT')) {
                    $groupType = 'Agri & Solvents';
                } else {
                    $groupType = !empty($prod['category']) ? $prod['category'] : 'Pipes & Fittings';
                }
            }

            $prod['group_type'] = $groupType;
            $prod['category'] = $groupType;
            $categoryCounts[$groupType] = ($categoryCounts[$groupType] ?? 0) + 1;
        }
        unset($prod);

        // Build flat rows list for 6-8 row table selector
        $flatRows = [];
        $rowIndex = 0;
        foreach ($products as $pIdx => $prod) {
            $prodName = $prod['name'];
            $cat = $prod['category'] ?? ($prod['group_type'] ?? 'Pipes & Fittings');
            $img = $prod['image_url'] ?? null;
            $groupType = $prod['group_type'] ?? $cat;
            $variants = $prod['variants'] ?? [];

            foreach ($variants as $vIdx => $v) {
                $flatRows[] = [
                    'row_id' => $rowIndex,
                    'parent_idx' => $pIdx,
                    'variant_idx' => $vIdx,
                    'product_name' => $prodName,
                    'product_code' => $v['product_code'] ?? ($v['sku'] ?? ''),
                    'variant_name' => $v['variant_name'] ?? 'Standard',
                    'size' => $v['size'] ?? 'Standard',
                    'category' => $cat,
                    'group_type' => $groupType,
                    'packing_1' => $v['packing_1'] ?? '',
                    'packing_2' => $v['packing_2'] ?? '',
                    'mrp' => floatval($v['mrp'] ?? 0),
                    'purchase_cost' => floatval($v['raw_rate'] ?? 0),
                    'cost_price_2' => floatval($v['cost_price_2'] ?? 0),
                    'cost_price_3' => floatval($v['cost_price_3'] ?? 0),
                    'wholesale_price' => floatval($v['wholesale_price'] ?? 0),
                    'retail_price' => floatval($v['retail_price'] ?? 0),
                    'stock' => intval($v['stock_quantity'] ?? 0),
                    'image_url' => $img,
                ];
                $rowIndex++;
            }
        }

        $upvcCount = $categoryCounts['UPVC'] ?? 0;
        $cpvcCount = $categoryCounts['CPVC'] ?? 0;
        $swrCount = $categoryCounts['SWR'] ?? 0;
        $otherCount = $categoryCounts['Agri & Solvents'] ?? ($categoryCounts['AGRI_OTHER'] ?? 0);

        $galleryImages = $this->getAllGalleryImages($userId);
        $imageFolders = [];
        foreach ($galleryImages as $img) {
            $f = $img['folder'] ?? 'General';
            $imageFolders[$f] = ($imageFolders[$f] ?? 0) + 1;
        }
        ksort($imageFolders);

        return view('seller.catalog.excel_mapper', compact(
            'job',
            'products',
            'flatRows',
            'galleryImages',
            'imageFolders',
            'categories',
            'categoryCounts',
            'recentJobs',
            'upvcCount',
            'cpvcCount',
            'swrCount',
            'otherCount',
            'liveProducts'
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
     * 📑 Create a new Catalog Job & Excel Sheet dynamically from PDF extracted lines or user input.
     */
    public function createSheetFromRows(Request $request)
    {
        $userId = Auth::id() ?: 1;

        $request->validate([
            'sheet_name' => 'nullable|string|max:150',
            'effective_date' => 'nullable|string|max:50',
            'publish' => 'nullable|boolean',
            'rows' => 'nullable|array',
            'grouped_cards' => 'nullable|array',
        ]);

        $effectiveDate = trim($request->input('effective_date') ?? '');
        $sheetName = trim($request->input('sheet_name') ?: ('Catalog Sheet ' . ($effectiveDate ? "({$effectiveDate}) " : '') . date('d M Y, H:i')));
        $rawRows = $request->input('rows', []);
        $groupedCards = $request->input('grouped_cards', []);

        if (empty($rawRows) && empty($groupedCards)) {
            return response()->json([
                'success' => false,
                'message' => 'Koi rows ya grouped cards select nahi kiye gaye hain.',
            ], 422);
        }

        $products = [];

        // 1. Process Grouped Cards first
        foreach ($groupedCards as $card) {
            $cardName = trim($card['parent_name'] ?? 'Product Family');
            $groupType = !empty($card['category']) ? $card['category'] : (!empty($card['group_type']) ? $card['group_type'] : 'General Hardware');
            $cardDesc = trim($card['description'] ?? '');
            $img = !empty($card['image_url']) ? $card['image_url'] : null;

            $cardVariants = [];
            foreach ($card['variants'] ?? [] as $v) {
                $vSize = trim($v['size'] ?? 'Standard');
                $vCode = trim($v['product_code'] ?? ($v['sku'] ?? ''));
                $vPack1 = trim($v['packing_1'] ?? '');
                $vPack2 = trim($v['packing_2'] ?? '');
                $vMrp = floatval($v['mrp'] ?? 100);
                $vCost = floatval($v['purchase_cost'] ?? ($vMrp * 0.6));
                $vCost2 = floatval($v['cost_price_2'] ?? 0);
                $vCost3 = floatval($v['cost_price_3'] ?? 0);
                $vRetail = floatval($v['retail_price'] ?? ($vMrp * 0.85));
                $vStock = isset($v['stock']) && $v['stock'] !== '' ? intval($v['stock']) : (isset($v['stock_quantity']) && $v['stock_quantity'] !== '' ? intval($v['stock_quantity']) : 0);

                $vHsn = trim($v['hsn_code'] ?? '39174000');
                $cardVariants[] = [
                    'variant_name' => $vSize ?: 'Standard',
                    'size' => $vSize ?: 'Standard',
                    'sku' => $vCode ?: ('VAR-' . strtoupper(Str::random(6))),
                    'product_code' => $vCode,
                    'hsn_code' => $vHsn ?: '39174000',
                    'packing_1' => $vPack1,
                    'packing_2' => $vPack2,
                    'grade' => 'Industrial',
                    'raw_rate' => $vCost,
                    'cost_price_2' => $vCost2,
                    'cost_price_3' => $vCost3,
                    'price_effective_date' => $effectiveDate ?: ($v['price_effective_date'] ?? null),
                    'wholesale_price' => $vCost2 > 0 ? $vCost2 : round($vCost * 1.15, 2),
                    'retail_price' => $vRetail,
                    'mrp' => $vMrp,
                    'stock_quantity' => $vStock,
                ];
            }

            if (!empty($cardVariants)) {
                $products[] = [
                    'name' => $cardName,
                    'description' => $cardDesc,
                    'category' => $groupType,
                    'group_type' => $groupType,
                    'image_url' => $img,
                    'effective_date' => $effectiveDate ?: null,
                    'hsn_code' => $cardVariants[0]['hsn_code'] ?? '39174000',
                    'variants' => $cardVariants,
                ];
            }
        }

        // 2. Process Flat Rows (Group by product_name)
        $flatGrouped = [];
        foreach ($rawRows as $r) {
            $prodName = trim($r['product_name'] ?? 'Product Item');
            $size = trim($r['size'] ?? 'Standard');
            $code = trim($r['product_code'] ?? ($r['sku'] ?? ''));
            $hsn = trim($r['hsn_code'] ?? '39174000');
            $pack1 = trim($r['packing_1'] ?? '');
            $pack2 = trim($r['packing_2'] ?? '');
            $mrp = floatval($r['mrp'] ?? 100);
            $cost = floatval($r['purchase_cost'] ?? ($mrp * 0.6));
            $cost2 = floatval($r['cost_price_2'] ?? 0);
            $cost3 = floatval($r['cost_price_3'] ?? 0);
            $retail = floatval($r['retail_price'] ?? ($mrp * 0.85));
            $stock = isset($r['stock']) && $r['stock'] !== '' ? intval($r['stock']) : (isset($r['stock_quantity']) && $r['stock_quantity'] !== '' ? intval($r['stock_quantity']) : 0);
            $img = !empty($r['image_url']) ? $r['image_url'] : null;
            $groupType = !empty($r['group_type']) ? trim($r['group_type']) : (!empty($r['category']) ? trim($r['category']) : 'General Hardware');
            $categoryName = !empty($r['category']) ? trim($r['category']) : $groupType;
            $desc = trim($r['description'] ?? '');

            if (!isset($flatGrouped[$prodName])) {
                $flatGrouped[$prodName] = [
                    'name' => $prodName,
                    'description' => $desc,
                    'category' => $categoryName,
                    'group_type' => $groupType,
                    'image_url' => $img,
                    'effective_date' => $effectiveDate ?: null,
                    'hsn_code' => $hsn ?: '39174000',
                    'variants' => [],
                ];
            }

            if ($img && empty($flatGrouped[$prodName]['image_url'])) {
                $flatGrouped[$prodName]['image_url'] = $img;
            }

            $flatGrouped[$prodName]['variants'][] = [
                'variant_name' => $size ?: 'Standard',
                'size' => $size ?: 'Standard',
                'sku' => $code ?: ('VAR-' . strtoupper(Str::random(6))),
                'product_code' => $code,
                'hsn_code' => $hsn ?: '39174000',
                'packing_1' => $pack1,
                'packing_2' => $pack2,
                'grade' => 'Industrial',
                'raw_rate' => $cost,
                'cost_price_2' => $cost2,
                'cost_price_3' => $cost3,
                'price_effective_date' => $effectiveDate ?: null,
                'wholesale_price' => $cost2 > 0 ? $cost2 : round($cost * 1.15, 2),
                'retail_price' => $retail,
                'mrp' => $mrp,
                'stock_quantity' => $stock,
            ];
        }

        foreach ($flatGrouped as $fp) {
            $products[] = $fp;
        }

        $existingJobId = $request->input('job_id');
        $job = null;
        if ($existingJobId) {
            $job = CatalogIngestionJob::where('id', $existingJobId)->where('user_id', $userId)->first();
        }

        if (!$job) {
            $job = CatalogIngestionJob::create([
                'user_id' => $userId,
                'filename' => $sheetName,
                'effective_date' => $effectiveDate ?: null,
                'file_path' => 'dynamic_pdf_import',
                'status' => 'parsed',
                'total_products_detected' => count($products),
                'extracted_data' => [
                    'sheet_name' => $sheetName,
                    'effective_date' => $effectiveDate ?: null,
                    'products' => $products,
                ],
            ]);
        } else {
            $job->update([
                'filename' => $sheetName,
                'effective_date' => $effectiveDate ?: ($job->effective_date ?? null),
                'total_products_detected' => count($products),
                'extracted_data' => [
                    'sheet_name' => $sheetName,
                    'effective_date' => $effectiveDate ?: ($job->effective_date ?? null),
                    'products' => $products,
                ],
            ]);
        }

        // Direct Publish to Live Store & Home Page (Draft saves will NOT publish!)
        $autoPublish = $request->boolean('publish', false);
        if ($autoPublish) {
            $publishedCount = $this->performDirectPublish($job, $products, $userId);
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'published' => true,
                    'message' => "🎉 Mubarakan! {$publishedCount} products live store aur home page par successfully publish ho gaye hain!",
                    'job_id' => $job->id,
                    'redirect_url' => route('seller.inventory.index'),
                ]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'published' => false,
                'message' => "Successfully saved '{$sheetName}' draft with " . count($products) . " products!",
                'job_id' => $job->id,
                'redirect_url' => route('seller.catalog.excel_mapper', $job->id),
            ]);
        }

        return redirect()->route('seller.catalog.excel_mapper', $job->id)
            ->with('success', "Sheet '{$sheetName}' created successfully! You can now link images and publish.");
    }

    /**
     * Direct Publish from Excel Mapper into Supabase DB.
     */
    public function publishDirectFromMapper(Request $request)
    {
        @set_time_limit(300);
        $userId = Auth::id() ?: 1;
        $jobId = $request->input('job_id');

        $job = CatalogIngestionJob::where('id', $jobId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $data = $job->extracted_data;
        $products = $data['products'] ?? [];

        if (empty($products)) {
            return back()->withErrors(['No products found in draft to publish.']);
        }

        $publishedCount = $this->performDirectPublish($job, $products, $userId);

        return redirect()->route('seller.inventory.index')
            ->with('success', "🎉 Mubarakan! {$publishedCount} grouped products live store me successfully publish ho gaye hain!");
    }

    /**
     * 🚀 Reusable Central Engine for publishing products & variants into DB.
     */
    public function performDirectPublish($job, array $products, int $userId)
    {
        $publishedCount = 0;
        $categoriesCache = [];

        @set_time_limit(300);
        try {
            DB::statement("SET statement_timeout = '60s'");
        } catch (\Throwable $e) {}

        foreach ($products as $prodData) {
            $prodName = trim($prodData['name'] ?? '');
            if (empty($prodName)) continue;

            retry(3, function () use ($prodData, $prodName, $userId, $job, &$publishedCount, &$categoriesCache) {
                DB::transaction(function () use ($prodData, $prodName, $userId, $job, &$publishedCount, &$categoriesCache) {
                    $rawCat = !empty($prodData['category']) ? trim($prodData['category']) : (!empty($prodData['group_type']) ? trim($prodData['group_type']) : 'General Hardware');
                    $catName = ucwords(strtolower(trim(preg_replace('/\s+/', ' ', $rawCat))));
                    
                    if (!isset($categoriesCache[$catName])) {
                        $category = Category::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($catName)])->first();
                        if (!$category) {
                            $category = Category::create([
                                'name' => $catName,
                                'slug' => Str::slug($catName) . '-' . Str::random(4)
                            ]);
                        }
                        $categoriesCache[$catName] = $category->id;
                    }
                    $catId = $categoriesCache[$catName];
                    $gst = 18;
                    $variants = $prodData['variants'] ?? [];
                    $hasMultipleVariants = count($variants) > 1;

                    $firstVar = $variants[0] ?? [];
                    $basePurchase = floatval($firstVar['raw_rate'] ?? 0);
                    $baseWholesale = floatval($firstVar['wholesale_price'] ?? ($basePurchase * 1.15));
                    $baseRetail = floatval($firstVar['retail_price'] ?? ($basePurchase * 1.35));
                    $baseMrp = floatval($firstVar['mrp'] ?? ($basePurchase * 1.60));
                    $effectiveDate = $prodData['effective_date'] ?? ($firstVar['price_effective_date'] ?? ($job->effective_date ?? null));
                    $cost2 = floatval($firstVar['cost_price_2'] ?? 0);
                    $cost3 = floatval($firstVar['cost_price_3'] ?? 0);

                    $prodHsn = !empty($prodData['hsn_code']) ? $prodData['hsn_code'] : (!empty($firstVar['hsn_code']) ? $firstVar['hsn_code'] : '39174000');
                    $prodBrand = !empty($prodData['brand']) ? trim($prodData['brand']) : (
                        stripos($prodName, 'Yorker') !== false ? 'Yorker' : (
                            stripos($prodName, 'Astral') !== false ? 'Astral' : (
                                stripos($prodName, 'Supreme') !== false ? 'Supreme' : (
                                    stripos($prodName, 'Plasto') !== false ? 'Plasto' : ''
                                )
                            )
                        )
                    );
                    $prodGroup = !empty($prodData['group_type']) ? trim($prodData['group_type']) : (!empty($prodData['group']) ? trim($prodData['group']) : (stripos($catName, 'swr') !== false || stripos($prodName, 'trap') !== false ? 'Sanitary & Drainage' : 'General Hardware'));
                    $prodDesc = !empty($prodData['description']) ? trim($prodData['description']) : "High grade {$prodName} manufactured to industrial specifications.";

                    $existingProd = Product::where('user_id', $userId)->where('name', $prodName)->first();
                    $finalImage = !empty($prodData['image_url']) ? $prodData['image_url'] : ($existingProd->image ?? null);
                    $prodSku = !empty($prodData['sku']) ? $prodData['sku'] : (!empty($firstVar['product_code']) ? $firstVar['product_code'] : ($existingProd->sku ?? ('PRD-' . strtoupper(Str::random(8)))));

                    $product = Product::updateOrCreate(
                        [
                            'user_id' => $userId,
                            'name' => $prodName,
                        ],
                        [
                            'category_id' => $catId,
                            'brand' => $prodBrand ?: 'Generic',
                            'group_name' => $prodGroup,
                            'slug' => $existingProd->slug ?? (Str::slug($prodName) . '-' . Str::random(5)),
                            'description' => $prodDesc,
                            'hsn_code' => $prodHsn,
                            'image' => $finalImage,
                            'purchase_price' => $basePurchase,
                            'wholesale_price' => $baseWholesale,
                            'cost_price_2' => $cost2,
                            'cost_price_3' => $cost3,
                            'price_effective_date' => $effectiveDate,
                            'price' => $baseRetail,
                            'mrp' => $baseMrp,
                            'gst_percent' => $gst,
                            'stock_quantity' => intval($firstVar['stock_quantity'] ?? 0),
                            'track_inventory' => false,
                            'has_variants' => $hasMultipleVariants,
                            'sku' => $prodSku,
                        ]
                    );

                    // Clean variant sync
                    ProductVariant::where('product_id', $product->id)->delete();

                    $variantRecords = [];
                    $now = now();
                    foreach ($variants as $v) {
                        $rawRate = floatval($v['raw_rate'] ?? 0);
                        $wPrice = floatval($v['wholesale_price'] ?? ($rawRate * 1.15));
                        $rPrice = floatval($v['retail_price'] ?? ($rawRate * 1.35));
                        $vMrp = floatval($v['mrp'] ?? ($rawRate * 1.60));
                        $vSku = !empty($v['product_code']) ? $v['product_code'] : (!empty($v['sku']) ? $v['sku'] : ('VAR-' . strtoupper(Str::random(7))));
                        $vCost2 = floatval($v['cost_price_2'] ?? 0);
                        $vCost3 = floatval($v['cost_price_3'] ?? 0);
                        $vEffDate = $v['price_effective_date'] ?? $effectiveDate;

                        $attributes = [
                            'size' => $v['size'] ?? 'Standard',
                            'packing_1' => $v['packing_1'] ?? '',
                            'packing_2' => $v['packing_2'] ?? '',
                            'cost_price_2' => $vCost2,
                            'cost_price_3' => $vCost3,
                            'price_effective_date' => $vEffDate,
                        ];

                        $variantRecords[] = [
                            'product_id' => $product->id,
                            'variant_name' => $v['size'] ?? ($v['variant_name'] ?? 'Standard'),
                            'purchase_price' => $rawRate,
                            'wholesale_price' => $wPrice,
                            'cost_price_2' => $vCost2,
                            'cost_price_3' => $vCost3,
                            'price_effective_date' => $vEffDate,
                            'retail_price' => $rPrice,
                            'mrp' => $vMrp,
                            'stock_quantity' => intval($v['stock_quantity'] ?? 0),
                            'sku' => $vSku,
                            'attributes' => json_encode($attributes),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    if (!empty($variantRecords)) {
                        ProductVariant::insert($variantRecords);
                    }

                    $publishedCount++;
                });
            }, 200);
        }

        if ($job) {
            $job->update(['status' => 'published']);
        }

        return $publishedCount;
    }

    /**
     * 🩺 Auto-heal & Enrich existing cards and published products.
     * Fixes: Tape model codes (3016 -> 3m x 16mm, 5019 -> 5m x 19mm), series descriptions (Auto Stop, Neo Lock),
     * plumbing dimensions (15mm-110mm), and miscategorized brands.
     */
    public function autoHealExistingProducts(Request $request)
    {
        $userId = Auth::id() ?: 1;
        $healedProductsCount = 0;
        $healedVariantsCount = 0;

        $tapeSeriesMap = [
            'AS' => ['series' => 'Auto Stop', 'desc' => 'Heavy Duty Steel Strip Tape with Auto-Stop mechanism, shockproof rubber casing, ISI grade'],
            'NL' => ['series' => 'Neo Lock', 'desc' => 'Self-Locking Heavy Duty Measuring Tape with smooth recoil and dual metric/inch markings'],
            'PR' => ['series' => 'Pro Model', 'desc' => 'Pro Model Professional Grade Measuring Tape with reinforced steel blade and rugged case'],
            'PF' => ['series' => 'Perfect Series', 'desc' => 'Perfect Series High Accuracy Measuring Tape with impact-resistant outer body'],
            'BW' => ['series' => 'Black & White', 'desc' => 'Black & White High Contrast Measuring Tape with anti-glare wide blade'],
            'FC' => ['series' => 'First Choice', 'desc' => 'First Choice Contractor Grade Measuring Tape with heavy duty spring mechanism'],
            'JP' => ['series' => 'Jupiter', 'desc' => 'Jupiter Series Heavy Duty Measuring Tape with smooth blade payout and belt clip'],
            'YK' => ['series' => 'Y-Series', 'desc' => 'Yorker Standard Y-Series Measuring Tape with durable compact case'],
            '2IN1' => ['series' => 'Yorker 2-in-1', 'desc' => 'Yorker 2-in-1 Dual Scale Measuring Tape with metric and imperial units'],
            'SS' => ['series' => 'Steel Strip', 'desc' => 'Stainless Steel Strip Measuring Tape with rustproof blade for heavy outdoor work'],
            'SP' => ['series' => 'Spring Series', 'desc' => 'Yorker High Tensile Spring Mechanism Measuring Tape']
        ];

        DB::transaction(function () use ($userId, $tapeSeriesMap, &$healedProductsCount, &$healedVariantsCount) {
            $products = Product::where('user_id', $userId)->with('variants')->get();

            // Get or create Hardware category for tools
            $hardwareCategory = Category::firstOrCreate(
                ['name' => 'Hardware & Tools'],
                ['slug' => 'hardware-tools-' . Str::random(4)]
            );

            foreach ($products as $prod) {
                $pChanged = false;
                $allText = strtoupper($prod->name . ' ' . $prod->sku . ' ' . $prod->variants->pluck('sku')->implode(' '));

                $isTape = false;
                $matchedSeries = null;

                foreach ($tapeSeriesMap as $codeKey => $sObj) {
                    if (str_contains($allText, $codeKey . '-3016') || str_contains($allText, $codeKey . '-5019') || str_contains($allText, strtoupper($sObj['series'])) || preg_match('/\b' . $codeKey . '[-_\s\d]/i', $allText)) {
                        $isTape = true;
                        $matchedSeries = $sObj;
                        break;
                    }
                }

                if (!$isTape && (str_contains($allText, 'TAPE') || str_contains($allText, 'YORKER') || str_contains($allText, '3016') || str_contains($allText, '5019'))) {
                    $isTape = true;
                    $matchedSeries = ['series' => 'Measuring Tape', 'desc' => 'Heavy Duty Measuring Tape with high tensile steel blade and shockproof rubber casing.'];
                }

                if ($isTape) {
                    // Fix brand to Yorker
                    if ($prod->brand === 'Plasto' || empty($prod->brand) || $prod->brand === 'Generic') {
                        $prod->brand = 'Yorker';
                        $pChanged = true;
                    }

                    // Fix category
                    if ($prod->category_id !== $hardwareCategory->id && ($prod->group_name === 'UPVC' || $prod->group_name === 'Plumbing' || empty($prod->group_name))) {
                        $prod->category_id = $hardwareCategory->id;
                        $prod->group_name = 'Hardware & Tools';
                        $pChanged = true;
                    }

                    // Fix name
                    if ($matchedSeries && !str_contains($prod->name, $matchedSeries['series'])) {
                        $prod->name = "Yorker Measuring Tape ({$matchedSeries['series']})";
                        $pChanged = true;
                    }

                    // Fix description
                    if (empty($prod->description) || str_contains($prod->description, 'High grade') || strlen($prod->description) < 15) {
                        $prod->description = $matchedSeries ? $matchedSeries['desc'] : 'Heavy Duty Professional Measuring Tape with high tensile steel blade and impact resistant case.';
                        $pChanged = true;
                    }

                    // Fix variants
                    foreach ($prod->variants as $var) {
                        $vSku = strtoupper($var->sku ?? '');
                        $origName = $var->variant_name;
                        $newName = $origName;

                        if (str_contains($vSku, '3016') || $origName === 'Standard' || $origName === '1/2 inch' || $origName === '1/2"') {
                            if (str_contains($vSku, '3016')) $newName = '3 Meter x 16mm';
                        }
                        if (str_contains($vSku, '5019') || $origName === '3/4 inch' || $origName === '3/4"') {
                            if (str_contains($vSku, '5019')) $newName = '5 Meter x 19mm';
                        }
                        if (str_contains($vSku, '1025')) {
                            $newName = '10 Meter x 25mm';
                        }

                        if ($newName !== $origName) {
                            $attrs = json_decode($var->attributes, true) ?: [];
                            $attrs['size'] = $newName;
                            $var->variant_name = $newName;
                            $var->attributes = json_encode($attrs);
                            $var->save();
                            $healedVariantsCount++;
                        }
                    }
                } else {
                    // Plumbing fittings
                    if (empty($prod->description) || str_contains($prod->description, 'manufactured to industrial specifications')) {
                        $nameUp = strtoupper($prod->name);
                        if (str_contains($nameUp, 'CPVC')) {
                            $prod->description = 'CPVC High Temperature & Pressure Fitting manufactured as per ASTM D2846 (SDR 11 Class 1).';
                            $pChanged = true;
                        } elseif (str_contains($nameUp, 'SWR') || str_contains($nameUp, 'TRAP')) {
                            $prod->description = 'SWR Soil, Waste & Rainwater drainage fitting with precision leak-proof socket as per IS:14735.';
                            $pChanged = true;
                        } elseif (str_contains($nameUp, 'UPVC')) {
                            $prod->description = 'Heavy Duty UPVC Lead-Free Pressure Pipe Fitting manufactured as per ASTM D2466 / Schedule 40 ISI standard.';
                            $pChanged = true;
                        }
                    }

                    // Fix variant names if Standard
                    foreach ($prod->variants as $var) {
                        if ($var->variant_name === 'Standard' || empty($var->variant_name)) {
                            $combo = $var->sku . ' ' . $prod->name;
                            if (preg_match('/\b(15|20|25|32|40|50|65|75|80|90|100|110|160)\s*(?:mm)?\b/i', $combo, $m)) {
                                $mm = $m[1];
                                $inchMap = ['15' => '1/2"', '20' => '3/4"', '25' => '1"', '32' => '1-1/4"', '40' => '1-1/2"', '50' => '2"', '65' => '2-1/2"', '80' => '3"', '100' => '4"'];
                                $resolvedSize = isset($inchMap[$mm]) ? "{$mm}mm ({$inchMap[$mm]})" : "{$mm}mm";
                                $attrs = json_decode($var->attributes, true) ?: [];
                                $attrs['size'] = $resolvedSize;
                                $var->variant_name = $resolvedSize;
                                $var->attributes = json_encode($attrs);
                                $var->save();
                                $healedVariantsCount++;
                            }
                        }
                    }
                }

                if ($pChanged) {
                    $prod->save();
                    $healedProductsCount++;
                }
            }
        });

        return response()->json([
            'success' => true,
            'healed_products_count' => $healedProductsCount,
            'healed_variants_count' => $healedVariantsCount,
            'message' => "Zabardast! {$healedProductsCount} Products aur {$healedVariantsCount} Variant Sizes database me auto-heal ho gaye!",
        ]);
    }
}

