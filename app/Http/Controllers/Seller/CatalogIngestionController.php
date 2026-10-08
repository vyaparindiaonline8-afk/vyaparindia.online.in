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

        return view('seller.inventory.index', compact('products', 'totalCatalogItems', 'lowStockCount', 'outOfStockCount', 'sellerCategories', 'sellerPage'));
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
                            'stock_quantity' => $vStock ?? 100,
                            'track_inventory' => !is_null($vStock),
                            'attributes' => $attributes,
                        ]);
                    }

                    // Track baselines
                    if ($vRetail > 0 && ($lowestRetail === null || $vRetail < $lowestRetail)) $lowestRetail = $vRetail;
                    if ($vWholesale > 0 && ($lowestWholesale === null || $vWholesale < $lowestWholesale)) $lowestWholesale = $vWholesale;
                    if ($vMrp > 0 && ($lowestMrp === null || $vMrp < $lowestMrp)) $lowestMrp = $vMrp;
                }

                if ($lowestRetail !== null) $product->price = $lowestRetail;
                if ($lowestWholesale !== null) $product->wholesale_price = $lowestWholesale;
                if ($lowestMrp !== null) $product->mrp = $lowestMrp;
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
    protected function getCropsRegistry($userId): array
    {
        $regFile = storage_path('app/gallery_crops_' . $userId . '.json');
        if (file_exists($regFile)) {
            $data = json_decode(file_get_contents($regFile), true);
            return is_array($data) ? $data : [];
        }
        return [];
    }

    protected function addCropToRegistry($userId, array $cropItem): void
    {
        $regFile = storage_path('app/gallery_crops_' . $userId . '.json');
        $crops = $this->getCropsRegistry($userId);
        array_unshift($crops, $cropItem);
        $crops = array_slice($crops, 0, 500);
        @file_put_contents($regFile, json_encode($crops, JSON_PRETTY_PRINT));
    }

    protected function removeCropFromRegistry($userId, string $url): void
    {
        $regFile = storage_path('app/gallery_crops_' . $userId . '.json');
        $crops = $this->getCropsRegistry($userId);
        $crops = array_filter($crops, fn($item) => ($item['url'] ?? '') !== $url && ($item['asset_url'] ?? '') !== $url);
        @file_put_contents($regFile, json_encode(array_values($crops), JSON_PRETTY_PRINT));
    }

    /**
     * Smart folder detector / resolver for catalog media
     */
    protected function detectFolderForImage(string $name, ?string $explicitCategory = null): string
    {
        if (!empty($explicitCategory) && !in_array(strtolower($explicitCategory), ['general', 'uncategorized', 'other', 'default', 'null', ''])) {
            return trim($explicitCategory);
        }

        $n = strtolower($name);
        if (str_contains($n, 'cpvc')) return 'CPVC Fittings';
        if (str_contains($n, 'upvc')) return 'UPVC Fittings';
        if (str_contains($n, 'swr') || str_contains($n, 'drain') || str_contains($n, 'trap') || str_contains($n, 'nahani') || str_contains($n, 'cowl')) return 'SWR Drainage';
        if (str_contains($n, 'tank') || str_contains($n, 'water tank') || str_contains($n, 'loft')) return 'Water Tanks';
        if (str_contains($n, 'valve') || str_contains($n, 'cock') || str_contains($n, 'brass') || str_contains($n, 'bib') || str_contains($n, 'pillar')) return 'Valves & Brass';
        if (str_contains($n, 'bath') || str_contains($n, 'shower') || str_contains($n, 'faucet') || str_contains($n, 'basin') || str_contains($n, 'toilet') || str_contains($n, 'commode') || str_contains($n, 'sanitary')) return 'Bathroom & Sanitary';
        if (str_contains($n, 'pump') || str_contains($n, 'motor') || str_contains($n, 'submersible') || str_contains($n, 'monobloc')) return 'Pumps & Motors';
        if (str_contains($n, 'paint') || str_contains($n, 'distemper') || str_contains($n, 'primer') || str_contains($n, 'enamel') || str_contains($n, 'emulsion')) return 'Paints & Coatings';
        if (str_contains($n, 'wire') || str_contains($n, 'switch') || str_contains($n, 'cable') || str_contains($n, 'mcb') || str_contains($n, 'socket') || str_contains($n, 'electrical')) return 'Electrical & Wiring';
        if (str_contains($n, 'ply') || str_contains($n, 'door') || str_contains($n, 'hinge') || str_contains($n, 'lock') || str_contains($n, 'hardware')) return 'Hardware & Tools';
        if (str_contains($n, 'pipe') || str_contains($n, 'conduit')) return 'Pipes';
        if (str_contains($n, 'agri') || str_contains($n, 'solvent') || str_contains($n, 'cement') || str_contains($n, 'teflon')) return 'Agri & Solvents';

        return !empty($explicitCategory) ? trim($explicitCategory) : 'General';
    }

    /**
     * Helper to collect all available gallery images for seller.
     */
    protected function getAllGalleryImages($userId): array
    {
        $images = [];
        $seenUrls = [];

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

                $url = $m->file_path;
                if (isset($seenUrls[$url])) continue;
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
        $registryCrops = $this->getCropsRegistry($userId);
        foreach ($registryCrops as $regImg) {
            $u = $regImg['url'] ?? '';
            if ($u && !isset($seenUrls[$u])) {
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

        // Attach resolved folder & category to all images (preserving custom user folders)
        $customFolders = [];
        try {
            $customFolders = \App\Models\SellerMedia::where('user_id', $userId)
                ->whereNotNull('category_type')
                ->where('category_type', '!=', '')
                ->pluck('category_type', 'file_path')
                ->toArray();
        } catch (\Throwable $e) {}

        foreach ($images as &$img) {
            $u = $img['url'] ?? '';
            $uClean = ltrim($u, '/');
            $explicit = $customFolders[$u] ?? ($customFolders[$uClean] ?? ($img['category'] ?? null));
            $folder = $this->detectFolderForImage($img['name'] ?? '', $explicit);
            $img['folder'] = $folder;
            $img['category'] = $folder;
        }
        unset($img);

        // Sort latest first
        usort($images, function ($a, $b) {
            return ($b['created_at'] ?? 0) <=> ($a['created_at'] ?? 0);
        });

        return $images;
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

        $shareToCentral = $request->boolean('share_to_central', false);

        // Read folder or category
        $customFolder = trim($request->input('custom_folder') ?? '');
        $folderChoice = trim($request->input('folder', $request->input('category_type', 'General')));
        $folder = (!empty($customFolder) && $folderChoice === '__NEW__') ? $customFolder : (($folderChoice !== '__NEW__') ? $folderChoice : 'General');
        $categoryType = $folder;

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
                'is_assigned' => false,
                'is_universal' => $isUniversal,
                'permission_granted' => $shareToCentral,
                'category_type' => $categoryType,
            ]);
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
        $folderName = trim($request->input('folder_name'));
        $urls = $request->input('image_urls');

        $movedCount = 0;
        $alreadyCount = 0;

        foreach ($urls as $url) {
            $cleanUrl = ltrim($url, '/');
            $media = \App\Models\SellerMedia::where('user_id', $userId)
                ->where(function($q) use ($url, $cleanUrl) {
                    $q->where('file_path', $url)
                      ->orWhere('file_path', $cleanUrl)
                      ->orWhere('file_path', '/' . $cleanUrl);
                })->first();

            if ($media) {
                if (strcasecmp(trim($media->category_type ?? ''), $folderName) === 0) {
                    $alreadyCount++;
                    continue;
                }
                $media->category_type = $folderName;
                $media->save();
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

        // If it's a Cloudinary / HTTP URL or registered crop
        $this->removeCropFromRegistry($userId, $rawUrl);
        try {
            \App\Models\SellerMedia::where('user_id', $userId)
                ->where(function($q) use ($rawUrl) {
                    $q->where('file_path', $rawUrl)
                      ->orWhere('file_path', ltrim($rawUrl, '/'));
                })->delete();
        } catch (\Throwable $e) {}

        \Illuminate\Support\Facades\Cache::forget('cloudinary_resources_vyaparindia');

        if (str_starts_with($rawUrl, 'http://') || str_starts_with($rawUrl, 'https://')) {
            return response()->json([
                'success' => true,
                'message' => 'Image successfully removed from Gallery Vault.',
            ]);
        }

        $relUrl = ltrim($rawUrl, '/');
        $fullPath = public_path($relUrl);

        if (file_exists($fullPath)) {
            @unlink($fullPath);
            return response()->json([
                'success' => true,
                'message' => 'Image successfully deleted from Gallery Vault.',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Image removed from gallery view.',
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
        foreach ($request->input('image_urls') as $url) {
            $this->removeCropFromRegistry($userId, $url);
            if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
                $deletedCount++;
                continue;
            }
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
        $userId = Auth::id() ?: 1;
        $galleryImages = $this->getAllGalleryImages($userId);

        $folders = ['General'];
        foreach ($galleryImages as $img) {
            $f = $img['folder'] ?? $img['category'] ?? 'General';
            if ($f && !in_array($f, $folders)) {
                $folders[] = $f;
            }
        }
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

            // 1. Cloudinary Priority (Production Cloud CDN)
            $cloudinaryUrl = CloudinaryService::uploadBase64($rawData, "vyaparindia/catalog/seller_{$userId}");
            if ($cloudinaryUrl) {
                $fileName = basename(parse_url($cloudinaryUrl, PHP_URL_PATH));
                $shareToCentral = $request->boolean('share_to_central', false);
                $categoryType = $request->input('folder') ?: $request->input('category_type', 'General');

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
                    'size_kb' => 35.0,
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
                        'is_assigned' => false,
                        'is_universal' => $isUniversal,
                        'permission_granted' => $shareToCentral,
                        'category_type' => $categoryType,
                    ]);
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
            if (preg_match('/^data:image\/(\w+);base64,/', $rawData, $type)) {
                $binary = substr($rawData, strpos($rawData, ',') + 1);
                $ext = strtolower($type[1]) === 'png' ? 'png' : 'jpg';
                $binary = base64_decode($binary);
                if ($binary === false) {
                    return response()->json(['success' => false, 'message' => 'Invalid image base64'], 422);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Malformed image payload'], 422);
            }

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
                    'category_type' => $categoryType,
                    'is_assigned' => false,
                ]);
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
     * 🤖 Extract Tabular Product Rows from ONLY the Single Active PDF Page via AI.
     * Guaranteed single-page context — strictly conserves tokens as requested by user.
     */
    public function aiExtractTableFromPage(Request $request)
    {
        $request->validate([
            'page' => 'required|integer',
            'page_text' => 'required|string',
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
        $pageText = substr($request->input('page_text'), 0, 15000);

        $prompt = "You are an expert product catalog digitizer for wholesale B2B hardware, plumbing, sanitary, and electrical price lists.
Convert the following text from ONLY Page {$page} into a structured JSON array of table rows.

Each item in the JSON array must be an object with these exact keys:
- \"name\": Clean Product Item Name WITHOUT size or price attached (e.g. \"UPVC 90° Elbow\", \"UPVC Brass MTA\", \"SWR Door Bend\").
- \"size\": Dimension or variant size (e.g. \"20mm\", \"25mm (1 inch)\", \"32mm x 1/2\\\"\", \"-\").
- \"packing_1\": Box or inner pack quantity if present (e.g. \"100\" or \"-\").
- \"packing_2\": Bag or master carton pack quantity if present (e.g. \"800\" or \"-\").
- \"mrp\": Numeric MRP or Price (e.g. 45.00 or 120.50). Numbers only, no currency symbols.
- \"sku\": Item Code, Cat No, or Art No if visible (e.g. \"UP-101\" or \"-\").
- \"category\": Category classification (e.g. \"UPVC Fittings\", \"CPVC Fittings\", \"Pipes\", \"Hardware\").

CRITICAL RULES FOR CATALOG LAYOUTS:
1. In product catalogs, an item heading (e.g. \"UPVC ELBOW 90° HEAVY\") usually appears once above a table of 5 to 15 size rows. You MUST assign this full product name into the \"name\" field for EVERY corresponding size row.
2. DO NOT mix sizes, packaging numbers, or prices into the \"name\" field. The \"name\" must be clean.
3. Ignore noise words, headers, and footnotes like \"Available in:\", \"Price List\", \"Terms & Conditions\", \"Page X of Y\".
4. Return ONLY the raw valid JSON array starting with [ and ending with ]. No explanation, no conversational text, no markdown codeblocks.

PAGE {$page} TEXT CONTENT:
\"\"\"
{$pageText}
\"\"\"";

        $reply = null;
        $provider = null;

        // Try OpenAI
        if ($openAiKey && (str_starts_with($openAiKey, 'sk-') || !$geminiKey)) {
            try {
                $response = Http::withToken($openAiKey)
                    ->timeout(35)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            ['role' => 'system', 'content' => 'You are an accurate catalog extraction engine that outputs only valid JSON arrays.'],
                            ['role' => 'user', 'content' => $prompt],
                        ],
                        'temperature' => 0.1,
                    ]);

                if ($response->successful()) {
                    $reply = $response->json('choices.0.message.content');
                    $provider = 'OpenAI (GPT-4o mini)';
                }
            } catch (\Throwable $e) {
                Log::warning('AI Table extract OpenAI error: ' . $e->getMessage());
            }
        }

        // Try Gemini if no reply yet
        if (!$reply && $geminiKey) {
            try {
                $response = Http::timeout(35)
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$geminiKey}", [
                        'contents' => [
                            ['parts' => [['text' => $prompt]]]
                        ]
                    ]);

                if ($response->successful()) {
                    $reply = $response->json('candidates.0.content.parts.0.text');
                    $provider = 'Google Gemini 1.5 Flash';
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

        $parsedRows = json_decode($cleanJson, true);
        if (!is_array($parsedRows)) {
            // Fallback: regex match json array
            if (preg_match('/\[.*\]/s', $cleanJson, $matches)) {
                $parsedRows = json_decode($matches[0], true);
            }
        }

        if (!is_array($parsedRows) || empty($parsedRows)) {
            return response()->json([
                'success' => false,
                'message' => 'AI is page se structured table extract nahi kar paya. Aap "Extract Text Lines" (Free Native) ka use karke lines select kar sakte hain.',
                'raw_reply' => $reply,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'page' => $page,
            'rows' => $parsedRows,
            'total_rows' => count($parsedRows),
            'provider' => $provider,
        ]);
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

        // Auto-load latest job so seller never loses their uploaded excel data
        if (!$job) {
            $job = CatalogIngestionJob::where('user_id', $userId)->latest()->first();
        }

        $recentJobs = CatalogIngestionJob::where('user_id', $userId)->latest()->take(5)->get();

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
                'upvcCount' => 0,
                'cpvcCount' => 0,
                'swrCount' => 0,
                'otherCount' => 0,
            ]);
        }

        $extractedData = $job->extracted_data ?? ['products' => []];
        $products = $extractedData['products'] ?? [];
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
     * 📑 Create a new Catalog Job & Excel Sheet dynamically from PDF extracted lines or user input.
     */
    public function createSheetFromRows(Request $request)
    {
        $userId = Auth::id() ?: 1;

        $request->validate([
            'sheet_name' => 'nullable|string|max:150',
            'rows' => 'nullable|array',
            'grouped_cards' => 'nullable|array',
        ]);

        $sheetName = trim($request->input('sheet_name') ?: ('Catalog Sheet ' . date('d M Y, H:i')));
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
            $groupType = !empty($card['category']) ? $card['category'] : (!empty($card['group_type']) ? $card['group_type'] : 'UPVC');
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
                    'wholesale_price' => $vCost2 > 0 ? $vCost2 : round($vCost * 1.15, 2),
                    'retail_price' => $vRetail,
                    'mrp' => $vMrp,
                    'stock_quantity' => $vStock,
                ];
            }

            if (!empty($cardVariants)) {
                $products[] = [
                    'name' => $cardName,
                    'category' => $groupType,
                    'group_type' => $groupType,
                    'image_url' => $img,
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

            if (!isset($flatGrouped[$prodName])) {
                $flatGrouped[$prodName] = [
                    'name' => $prodName,
                    'category' => $categoryName,
                    'group_type' => $groupType,
                    'image_url' => $img,
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
                'file_path' => 'dynamic_pdf_import',
                'status' => 'parsed',
                'total_products_detected' => count($products),
                'extracted_data' => [
                    'sheet_name' => $sheetName,
                    'products' => $products,
                ],
            ]);
        } else {
            $job->update([
                'filename' => $sheetName,
                'total_products_detected' => count($products),
                'extracted_data' => [
                    'sheet_name' => $sheetName,
                    'products' => $products,
                ],
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Successfully saved '{$sheetName}' with " . count($products) . " products!",
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

        $publishedCount = 0;
        $categoriesCache = [];

        DB::transaction(function () use ($products, $userId, $job, &$publishedCount, &$categoriesCache) {
            foreach ($products as $prodData) {
                $catName = !empty($prodData['category']) ? trim($prodData['category']) : (!empty($prodData['group_type']) ? trim($prodData['group_type']) : 'General Hardware');
                
                if (!isset($categoriesCache[$catName])) {
                    $category = Category::firstOrCreate(
                        ['name' => $catName],
                        ['slug' => Str::slug($catName) . '-' . Str::random(4)]
                    );
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
                $prodHsn = !empty($prodData['hsn_code']) ? $prodData['hsn_code'] : (!empty($firstVar['hsn_code']) ? $firstVar['hsn_code'] : '39174000');
                $prodBrand = !empty($prodData['brand']) ? trim($prodData['brand']) : (stripos($prodData['name'], 'Astral') !== false ? 'Astral' : (stripos($prodData['name'], 'Supreme') !== false ? 'Supreme' : 'Plasto'));
                $prodGroup = !empty($prodData['group_type']) ? trim($prodData['group_type']) : (!empty($prodData['group']) ? trim($prodData['group']) : (stripos($catName, 'swr') !== false || stripos($prodData['name'], 'trap') !== false ? 'Sanitary & Drainage' : 'Plumbing'));

                $product = Product::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'name' => $prodData['name'],
                    ],
                    [
                        'category_id' => $catId,
                        'brand' => $prodBrand,
                        'group_name' => $prodGroup,
                        'slug' => Str::slug($prodData['name']) . '-' . Str::random(5),
                        'description' => "High grade {$prodData['name']} manufactured to industrial specifications.",
                        'hsn_code' => $prodHsn,
                        'image' => $prodData['image_url'] ?? null,
                        'purchase_price' => $basePurchase,
                        'wholesale_price' => $baseWholesale,
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
                    
                    $attributes = [
                        'size' => $v['size'] ?? 'Standard',
                        'packing_1' => $v['packing_1'] ?? '',
                        'packing_2' => $v['packing_2'] ?? '',
                        'cost_price_2' => $v['cost_price_2'] ?? 0,
                        'cost_price_3' => $v['cost_price_3'] ?? 0,
                    ];

                    $variantRecords[] = [
                        'product_id' => $product->id,
                        'variant_name' => $v['variant_name'] ?? 'Standard',
                        'size' => $v['size'] ?? 'Standard',
                        'grade' => $v['grade'] ?? 'Industrial',
                        'purchase_price' => $rawRate,
                        'wholesale_price' => $wPrice,
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
            }

            $job->update(['status' => 'published']);
        });

        return redirect()->route('seller.inventory.index')
            ->with('success', "🎉 Mubarakan! {$publishedCount} grouped products (291 sizes) live store me successfully publish ho gaye hain!");
    }
}

