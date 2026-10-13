<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSpreadsheetController extends Controller
{
    /**
     * Export products into Excel/CSV format.
     * Modes:
     * - 'rates': Quick lightweight rate revision sheet (दैनिक भाव / रेट रिवीज़न शीट)
     * - 'full': Complete catalog master sheet (पूरा कैटलॉग शीट)
     */
    public function export(Request $request)
    {
        $seller = Auth::user();
        $type = $request->query('type', 'rates');
        
        $query = Product::where('user_id', $seller->id)
            ->with(['category', 'variants'])
            ->orderBy('id', 'asc');

        $ids = [];
        if ($request->filled('items')) {
            $ids = array_filter(array_map('intval', explode(',', $request->query('items'))));
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
        }

        $folderParam = trim($request->query('folder', ''));
        if ($folderParam !== '' && $folderParam !== 'all') {
            $query->where('group_name', $folderParam);
        }

        $products = $query->get();

        $rows = [];
        $timestamp = date('Y_m_d_His');
        $prefix = "VyaparIndia";
        if (!empty($ids)) {
            $prefix .= "_Selected_" . count($ids) . "_Items";
        } elseif (!empty($folderParam) && $folderParam !== 'all') {
            $cleanFolder = preg_replace('/[^a-zA-Z0-9_-]/', '_', Str::ascii($folderParam)) ?: 'Folder';
            $prefix .= "_{$cleanFolder}";
        }

        if ($type === 'full') {
            $filename = "{$prefix}_Catalog_Master_{$timestamp}.csv";
            $headers = [
                'Product ID (Do Not Change)',
                'Variant ID (Optional)',
                'Product Name (आइटम नाम)',
                'Category (कैटेगरी)',
                'Brand (कंपनी)',
                'Group (ग्रुप)',
                'MRP',
                'Purchase Price (खरीद भाव / Cost)',
                'Selling Price (बिक्री भाव / Retail)',
                'Wholesale Price (थोक भाव / B2B)',
                'Stock Quantity (स्टॉक)',
                'Unit (इकाई)',
                'SKU / Code',
                'Description (विवरण)',
                'Image URL',
            ];
            $rows[] = $headers;

            foreach ($products as $p) {
                if ($p->variants && $p->variants->isNotEmpty()) {
                    foreach ($p->variants as $v) {
                        $rows[] = [
                            $p->id,
                            $v->id,
                            $p->name,
                            $p->category->name ?? 'General',
                            $p->brand ?? '',
                            $p->group_name ?? '',
                            $v->mrp ?: ($p->mrp ?: $p->price),
                            $v->purchase_price ?: ($v->raw_rate ?: ($p->purchase_price ?: '')),
                            $v->retail_price ?: $p->price,
                            $v->wholesale_price ?: ($p->wholesale_price ?: $p->price),
                            $v->stock_quantity ?? ($p->stock_quantity ?? 100),
                            'Pcs',
                            $v->sku ?: ($p->sku ?: "PRD-{$p->id}"),
                            $p->description ?? '',
                            $p->image_url ?? '',
                        ];
                    }
                } else {
                    $rows[] = [
                        $p->id,
                        '',
                        $p->name,
                        $p->category->name ?? 'General',
                        $p->brand ?? '',
                        $p->group_name ?? '',
                        $p->mrp ?: $p->price,
                        $p->purchase_price ?: '',
                        $p->price,
                        $p->wholesale_price ?: $p->price,
                        $p->stock_quantity ?? 0,
                        'Pcs',
                        $p->sku ?: "PRD-{$p->id}",
                        $p->description ?? '',
                        $p->image_url ?? '',
                    ];
                }
            }
        } else {
            // Default: 'rates' - Daily Dynamic Rate Revision Sheet
            $filename = "{$prefix}_Daily_Rates_{$timestamp}.csv";
            $headers = [
                'Product ID (Do Not Change)',
                'Variant ID (Optional)',
                'Product Name (आइटम नाम)',
                'Size / Variant Name (साइज़ / प्रकार)',
                'Brand (कंपनी)',
                'Group (ग्रुप)',
                'Selling Price (बिक्री भाव)',
                'Wholesale Price (थोक भाव B2B)',
                'Purchase Price (खरीद भाव Cost)',
                'MRP',
                'Current Stock (स्टॉक संख्या)',
            ];
            $rows[] = $headers;

            foreach ($products as $p) {
                if ($p->variants && $p->variants->isNotEmpty()) {
                    foreach ($p->variants as $v) {
                        $rows[] = [
                            $p->id,
                            $v->id,
                            $p->name,
                            $v->variant_name ?: ($v->size ?: 'Standard'),
                            $p->brand ?? '',
                            $p->group_name ?? '',
                            $v->retail_price ?: $p->price,
                            $v->wholesale_price ?: ($p->wholesale_price ?: $p->price),
                            $v->purchase_price ?: ($v->raw_rate ?: ($p->purchase_price ?: '')),
                            $v->mrp ?: ($p->mrp ?: $p->price),
                            $v->stock_quantity ?? ($p->stock_quantity ?? 100),
                        ];
                    }
                } else {
                    $rows[] = [
                        $p->id,
                        '',
                        $p->name,
                        'Standard',
                        $p->brand ?? '',
                        $p->group_name ?? '',
                        $p->price,
                        $p->wholesale_price ?: $p->price,
                        $p->purchase_price ?: '',
                        $p->mrp ?: $p->price,
                        $p->stock_quantity ?? 0,
                    ];
                }
            }
        }

        // Generate UTF-8 CSV with Byte Order Mark (BOM) so Excel opens Hindi & English flawlessly
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
     * Import edited Excel/CSV and sync product details, prices, stock, or create new items.
     */
    public function import(Request $request)
    {
        @set_time_limit(300);
        $seller = Auth::user();
        $rawRows = [];

        // 1. Check if parsed JSON rows passed from frontend SheetJS
        if ($request->filled('spreadsheet_rows')) {
            $decoded = json_decode($request->input('spreadsheet_rows'), true);
            if (is_array($decoded)) {
                $rawRows = $decoded;
            }
        } elseif ($request->hasFile('spreadsheet_file')) {
            // 2. Fallback: Parse uploaded CSV file on server
            $file = $request->file('spreadsheet_file');
            $path = $file->getRealPath();
            if (($handle = fopen($path, 'r')) !== false) {
                // Strip possible UTF-8 BOM
                $bom = fread($handle, 3);
                if ($bom !== "\xEF\xBB\xBF") {
                    rewind($handle);
                }

                $header = null;
                while (($data = fgetcsv($handle, 3000, ',')) !== false) {
                    if (!$header) {
                        $header = array_map(function ($h) {
                            $clean = preg_replace('/[^a-zA-Z0-9_]/', '', str_replace([' ', '/', '-', '(', ')'], '_', strtolower(trim($h))));
                            return trim($clean, '_');
                        }, $data);
                        continue;
                    }

                    if (count($data) >= 2) {
                        $row = [];
                        foreach ($header as $idx => $key) {
                            $row[$key] = $data[$idx] ?? '';
                        }
                        $rawRows[] = $row;
                    }
                }
                fclose($handle);
            }
        }

        if (empty($rawRows)) {
            return back()->with('error', 'Kripya ek valid Excel ya CSV file upload karein jisme rows mojud hon.');
        }

        $updatedProductsCount = 0;
        $updatedVariantsCount = 0;
        $unchangedCount = 0;
        $createdProductsCount = 0;
        $skippedCount = 0;
        $affectedProductIds = [];
        $allowCreateNew = $request->boolean('allow_create_new', false);

        $normalizeForMatching = function(?string $str): string {
            if (!$str) return '';
            $s = strtolower(trim($str));
            // Remove common brand noise if present
            $s = preg_replace('/\b(plasto|supreme|astral|ashirvad|finolex|prince)\b/i', '', $s);
            // Standardize degree
            $s = str_replace(['°', 'deg.', 'degree'], 'deg', $s);
            // Standardize schedules
            $s = preg_replace('/sch\s*(\d+)/i', '$1 sch', $s);
            $s = preg_replace('/(\d+)\s*sch/i', '$1 sch', $s);
            // Strip compound dimensions: 110*75*50, 25*20, 20x15, 1/2", 3/4"
            $s = preg_replace('/\b\d+(\.\d+)?\s*[\*xX/]\s*\d+(\.\d+)?\s*[\*xX/]\s*\d+(\.\d+)?(\s*mm)?\b/i', '', $s);
            $s = preg_replace('/\b\d+(\.\d+)?\s*[\*xX/]\s*\d+(\.\d+)?(\s*mm)?\b/i', '', $s);
            // Strip fractional inch & simple mm/inch/ft
            $s = preg_replace('/\b\d+\s*[-–]?\s*\d+\/\d+(")?\b/i', '', $s);
            $s = preg_replace('/\b\d+\/\d+(")?\b/i', '', $s);
            $s = preg_replace('/\b\d+(\.\d+)?\s*(mm|inch|feet|ft|")\b/i', '', $s);
            $s = preg_replace('/[^a-z0-9]/', ' ', $s);
            return trim(preg_replace('/\s+/', ' ', $s));
        };

        $sellerProducts = Product::where('user_id', $seller->id)->with('variants')->get();

        DB::transaction(function () use ($rawRows, $seller, $sellerProducts, $allowCreateNew, $normalizeForMatching, &$updatedProductsCount, &$updatedVariantsCount, &$unchangedCount, &$createdProductsCount, &$skippedCount, &$affectedProductIds) {
            foreach ($rawRows as $row) {
                // Pre-build normalized key-value map for robust header lookup (handles raw Excel, Hindi, snake_case)
                $normMap = [];
                foreach ($row as $k => $v) {
                    $rawK = trim((string)$k);
                    $normMap[$rawK] = $v;
                    $lowerK = strtolower($rawK);
                    $normMap[$lowerK] = $v;
                    $cleanK = trim(preg_replace('/_+/', '_', preg_replace('/[^a-z0-9_]/', '', str_replace([' ', '/', '-', '(', ')', '[', ']', '.', ':', '*'], '_', $lowerK))), '_');
                    if ($cleanK !== '') {
                        $normMap[$cleanK] = $v;
                    }
                }

                // Normalized row keys lookup helper
                $getVal = function(array $aliases) use ($normMap) {
                    // 1. Direct match on normalized map
                    foreach ($aliases as $alias) {
                        if (isset($normMap[$alias]) && trim((string)$normMap[$alias]) !== '') {
                            return trim((string)$normMap[$alias]);
                        }
                    }
                    // 2. Fallback: Check if any key contains the alias
                    foreach ($normMap as $k => $val) {
                        if (trim((string)$val) === '') continue;
                        foreach ($aliases as $alias) {
                            if (strlen($alias) >= 4 && str_contains($k, $alias)) {
                                return trim((string)$val);
                            }
                        }
                    }
                    return null;
                };

                $getFloat = function(array $aliases) use ($getVal) {
                    $val = $getVal($aliases);
                    if ($val !== null) {
                        $clean = preg_replace('/[^0-9.]/', '', $val);
                        return is_numeric($clean) ? floatval($clean) : null;
                    }
                    return null;
                };

                $getInt = function(array $aliases) use ($getVal) {
                    $val = $getVal($aliases);
                    if ($val !== null) {
                        $clean = preg_replace('/[^0-9]/', '', $val);
                        return is_numeric($clean) ? intval($clean) : null;
                    }
                    return null;
                };

                $rawId = $getInt([
                    'product_id', 'product_id_do_not_change', 'id', 'item_id', 'prod_id',
                    'productid', 'product_id__do_not_change_'
                ]);

                $variantId = $getInt([
                    'variant_id', 'variant_id_optional', 'variantid', 'var_id', 'variant_id__optional_'
                ]);

                $name = $getVal([
                    'product_name', 'product_name_aaitm_naam', 'name', 'item_name', 'title',
                    'product_name__aaitm_naam_', 'aaitm_naam'
                ]);

                $categoryName = $getVal([
                    'category', 'category_kaitaigarii', 'category_name', 'category__kaitaigarii_'
                ]);

                $brand = $getVal([
                    'brand', 'brand_kanpnii', 'brand_company', 'company', 'brand__kanpnii_'
                ]);

                $groupName = $getVal([
                    'group', 'group_grup', 'group_name', 'group__grup_'
                ]);

                $sizeVariant = $getVal([
                    'size__variant_name__saaiz___prkaar_', 'size_variant_name', 'size_variant',
                    'size', 'variant_name', 'variant', 'dimension', 'saaiz'
                ]);

                $sellingPrice = $getFloat([
                    'selling_price', 'selling_price_bikrii_bhaav', 'retail_price', 'price',
                    'selling_price__bikrii_bhaav_', 'rate_a', 'sale_price'
                ]);

                $wholesalePrice = $getFloat([
                    'wholesale_price', 'wholesale_price_thok_bhaav_b2b', 'wholesale_price_thok_bhaav',
                    'wholesale_price__thok_bhaav_b2b_', 'wholesale_price__thok_bhaav_', 'rate_b', 'b2b_price'
                ]);

                $purchasePrice = $getFloat([
                    'purchase_price', 'purchase_price_khariid_bhaav_cost', 'purchase_price_cost',
                    'purchase_price_khariid_bhaav', 'purchase_price__khariid_bhaav___cost_', 'cost', 'raw_rate'
                ]);

                $mrp = $getFloat(['mrp', 'maximum_retail_price']);

                $stock = $getInt([
                    'current_stock', 'current_stock_sttok_sankhyaa', 'stock_quantity',
                    'stock_quantity_sttok', 'stock', 'stock_quantity__sttok_', 'current_stock__sttok_sankhyaa_'
                ]);

                $desc = $getVal([
                    'description', 'description_vivrn', 'desc', 'description__vivrn_'
                ]);

                // Resolve Product: Case 1 by ID, Case 2 by Name / Base Family (In-Memory for maximum speed)
                $product = null;
                if ($rawId && $rawId > 0) {
                    $product = $sellerProducts->firstWhere('id', $rawId);
                }

                if (!$product && !empty($name)) {
                    $cleanName = strtolower(trim($name));
                    $nameSlug = Str::slug($name);
                    $product = $sellerProducts->first(function($p) use ($cleanName, $nameSlug) {
                        return strtolower(trim($p->name)) === $cleanName
                            || (!empty($nameSlug) && $p->slug === $nameSlug);
                    });

                    // Fuzzy base matching using normalizeForMatching (handles brand prefix, degree °, schedules, compound sizes)
                    if (!$product) {
                        $normName = $normalizeForMatching($name);
                        if (!empty($normName)) {
                            $product = $sellerProducts->first(function($p) use ($normName, $normalizeForMatching) {
                                $pNorm = $normalizeForMatching($p->name);
                                return !empty($pNorm) && ($pNorm === $normName || str_contains($pNorm, $normName) || str_contains($normName, $pNorm));
                            });
                        }
                    }

                    // Extract sizeVariant from $name if not already present
                    if ($product && empty($sizeVariant)) {
                        if (preg_match('~(\d+(?:\.\d+)?(?:\s*[\*xX/]\s*\d+(?:\.\d+)?)+\s*(?:mm|inch|feet|ft)?|\d+(?:\.\d+)?\s*(?:mm|inch|feet|ft|")|\d+\/\d+(?:")?)~i', $name, $dm)) {
                            $sizeVariant = trim($dm[0]);
                        }
                    }
                }

                if ($product) {
                    $prodUpdates = [];

                    // 1. Allow updating Product Name if explicitly provided and matched by ID
                    if ($rawId && !empty($name) && $name !== $product->name) {
                        $prodUpdates['name'] = $name;
                        $newSlug = Str::slug($name) ?: ('prod-' . $product->id . '-' . time());
                        if ($newSlug !== $product->slug) {
                            $slugCandidate = $newSlug;
                            $suffix = 1;
                            while (Product::where('slug', $slugCandidate)->where('id', '!=', $product->id)->exists()) {
                                $slugCandidate = $newSlug . '-' . $seller->id . ($suffix > 1 ? '-' . $suffix : '');
                                if (Product::where('slug', $slugCandidate)->where('id', '!=', $product->id)->exists()) {
                                    $slugCandidate = $newSlug . '-' . Str::random(5);
                                    break;
                                }
                                $suffix++;
                            }
                            $prodUpdates['slug'] = $slugCandidate;
                        }
                    }

                    if (!empty($brand)) {
                        $prodUpdates['brand'] = $brand;
                    }
                    if (!empty($groupName)) {
                        $prodUpdates['group_name'] = $groupName;
                    }
                    if (!empty($desc)) {
                        $prodUpdates['description'] = $desc;
                    }

                    if (!empty($categoryName)) {
                        $cat = Category::firstOrCreate(
                            ['name' => ucwords($categoryName)],
                            ['slug' => Str::slug($categoryName) ?: ('cat-' . time())]
                        );
                        $prodUpdates['category_id'] = $cat->id;
                    }

                    // 2. Check if product has variants or matches a variant row
                    $hasVariants = $product->variants && $product->variants->isNotEmpty();
                    $matchedVariant = null;

                    if ($hasVariants) {
                        if ($variantId && $variantId > 0) {
                            $matchedVariant = $product->variants->firstWhere('id', $variantId);
                        } elseif (!empty($sizeVariant)) {
                            $reqClean = preg_replace('/[^a-z0-9]/', '', strtolower(trim($sizeVariant)));
                            foreach ($product->variants as $pv) {
                                $pvClean = preg_replace('/[^a-z0-9]/', '', strtolower(trim($pv->variant_name ?: ($pv->clean_size ?: ''))));
                                if ($pvClean === $reqClean || str_contains($pvClean, $reqClean) || str_contains($reqClean, $pvClean)) {
                                    $matchedVariant = $pv;
                                    break;
                                }
                            }
                        }
                    }

                    $isChanged = false;
                    if ($matchedVariant) {
                        // Update variant-specific fields cleanly with exact Excel values
                        $varUpdates = [];
                        if (!is_null($sellingPrice) && $sellingPrice > 0) {
                            if (abs(floatval($matchedVariant->retail_price ?? $matchedVariant->price) - $sellingPrice) > 0.01) {
                                $isChanged = true;
                            }
                            $varUpdates['retail_price'] = $sellingPrice;
                        }
                        if (!is_null($wholesalePrice) && $wholesalePrice > 0) {
                            if (abs(floatval($matchedVariant->wholesale_price) - $wholesalePrice) > 0.01) {
                                $isChanged = true;
                            }
                            $varUpdates['wholesale_price'] = $wholesalePrice;
                        }
                        if (!is_null($purchasePrice) && $purchasePrice > 0) {
                            if (abs(floatval($matchedVariant->purchase_price) - $purchasePrice) > 0.01) {
                                $isChanged = true;
                            }
                            $varUpdates['purchase_price'] = $purchasePrice;
                            $varUpdates['raw_rate'] = $purchasePrice;
                        }
                        if (!is_null($mrp) && $mrp > 0) {
                            if (abs(floatval($matchedVariant->mrp) - $mrp) > 0.01) {
                                $isChanged = true;
                            }
                            $varUpdates['mrp'] = $mrp;
                        }
                        if (!is_null($stock)) {
                            if (intval($matchedVariant->stock_quantity) !== $stock) {
                                $isChanged = true;
                            }
                            $varUpdates['stock_quantity'] = $stock;
                            $varUpdates['track_inventory'] = true;
                        }
                        if (!empty($sizeVariant)) {
                            $varUpdates['variant_name'] = $sizeVariant;
                            $attrs = $matchedVariant->custom_attributes ?? [];
                            $attrs['size'] = $sizeVariant;
                            $varUpdates['attributes'] = $attrs;
                        }

                        if (!empty($varUpdates)) {
                            $matchedVariant->update($varUpdates);
                            if ($isChanged) {
                                $updatedVariantsCount++;
                            } else {
                                $unchangedCount++;
                            }
                            $affectedProductIds[$product->id] = true;
                        }
                    } elseif ($hasVariants && !empty($sizeVariant)) {
                        // Product has variants, but this particular size (e.g. 110*75*50) is new -> Add as new variant
                        $newVar = ProductVariant::create([
                            'product_id' => $product->id,
                            'variant_name' => $sizeVariant,
                            'sku' => 'VAR-' . $product->id . '-' . strtoupper(Str::random(5)),
                            'retail_price' => $sellingPrice ?? ($mrp ?? $product->price),
                            'wholesale_price' => $wholesalePrice ?? ($sellingPrice ?? $product->wholesale_price),
                            'purchase_price' => $purchasePrice ?? 0,
                            'mrp' => $mrp ?? ($sellingPrice ?? $product->mrp),
                            'stock_quantity' => $stock ?? 100,
                            'track_inventory' => !is_null($stock),
                            'attributes' => ['size' => $sizeVariant],
                        ]);
                        $product->variants->push($newVar);
                        $updatedVariantsCount++;
                        $affectedProductIds[$product->id] = true;
                    } else {
                        // Single Product rate update (when product has no variants)
                        if (!$hasVariants) {
                            if (!is_null($sellingPrice) && $sellingPrice > 0) {
                                if (abs(floatval($product->price) - $sellingPrice) > 0.01) $isChanged = true;
                                $prodUpdates['price'] = $sellingPrice;
                            }
                            if (!is_null($wholesalePrice) && $wholesalePrice > 0) {
                                if (abs(floatval($product->wholesale_price) - $wholesalePrice) > 0.01) $isChanged = true;
                                $prodUpdates['wholesale_price'] = $wholesalePrice;
                            }
                            if (!is_null($purchasePrice) && $purchasePrice > 0) {
                                if (abs(floatval($product->purchase_price) - $purchasePrice) > 0.01) $isChanged = true;
                                $prodUpdates['purchase_price'] = $purchasePrice;
                            }
                            if (!is_null($mrp) && $mrp > 0) {
                                if (abs(floatval($product->mrp) - $mrp) > 0.01) $isChanged = true;
                                $prodUpdates['mrp'] = $mrp;
                            }
                            if (!is_null($stock)) {
                                if (intval($product->stock_quantity) !== $stock) $isChanged = true;
                                $prodUpdates['stock_quantity'] = $stock;
                                $prodUpdates['track_inventory'] = true;
                            }
                        }
                    }

                    if (!empty($prodUpdates)) {
                        $product->update($prodUpdates);
                        if ($isChanged) {
                            $updatedProductsCount++;
                        } else {
                            $unchangedCount++;
                        }
                    } elseif (!$matchedVariant && !$hasVariants) {
                        $unchangedCount++;
                    }
                } else {
                    // Case B: No product_id provided and not matched
                    if ($allowCreateNew && !empty($name) && (!is_null($sellingPrice) || !is_null($mrp))) {
                        if ($seller->canAddProduct()) {
                            $catId = null;
                            if (!empty($categoryName)) {
                                $cat = Category::firstOrCreate(
                                    ['name' => ucwords($categoryName)],
                                    ['slug' => Str::slug($categoryName) ?: ('cat-' . time())]
                                );
                                $catId = $cat->id;
                            }

                            $finalPrice = $sellingPrice ?? ($mrp ?? 0);

                            // Generate guaranteed globally unique slug to prevent Postgres 23505 Unique Violation
                            $baseSlug = Str::slug($name);
                            if (empty($baseSlug)) {
                                $baseSlug = 'prod-' . $seller->id . '-' . time();
                            }
                            $uniqueSlug = $baseSlug;
                            $suffix = 1;
                            while (Product::where('slug', $uniqueSlug)->exists()) {
                                $uniqueSlug = $baseSlug . '-' . $seller->id . ($suffix > 1 ? '-' . $suffix : '');
                                if (Product::where('slug', $uniqueSlug)->exists()) {
                                    $uniqueSlug = $baseSlug . '-' . Str::random(5);
                                    break;
                                }
                                $suffix++;
                            }

                            $newProduct = Product::create([
                                'user_id' => $seller->id,
                                'name' => $name,
                                'slug' => $uniqueSlug,
                                'description' => !empty($desc) ? $desc : $name,
                                'category_id' => $catId,
                                'brand' => !empty($brand) ? $brand : null,
                                'group_name' => !empty($groupName) ? $groupName : null,
                                'price' => $finalPrice,
                                'wholesale_price' => $wholesalePrice ?? $finalPrice,
                                'purchase_price' => $purchasePrice ?? 0,
                                'mrp' => $mrp ?? $finalPrice,
                                'stock_quantity' => $stock ?? 100,
                                'track_inventory' => !is_null($stock),
                                'has_variants' => !empty($sizeVariant),
                            ]);

                            $createdProductsCount++;
                            $sellerProducts->push($newProduct);

                            if (!empty($sizeVariant)) {
                                ProductVariant::create([
                                    'product_id' => $newProduct->id,
                                    'size' => $sizeVariant,
                                    'color' => null,
                                    'price' => $finalPrice,
                                    'wholesale_price' => $wholesalePrice ?? $finalPrice,
                                    'purchase_price' => $purchasePrice ?? 0,
                                    'mrp' => $mrp ?? $finalPrice,
                                    'stock_quantity' => $stock ?? 100,
                                ]);
                            }
                        } else {
                            $skippedCount++;
                        }
                    } else {
                        // Skipped because row is empty or allow_create_new is off
                        $skippedCount++;
                    }
                }
            }

            // Re-sync base prices and stock for all affected products that have variants
            foreach (array_keys($affectedProductIds) as $pId) {
                $p = Product::with('variants')->find($pId);
                if ($p && $p->variants->isNotEmpty()) {
                    $minRetail = $p->variants->where('retail_price', '>', 0)->min('retail_price');
                    $minWholesale = $p->variants->where('wholesale_price', '>', 0)->min('wholesale_price');
                    $minMrp = $p->variants->where('mrp', '>', 0)->min('mrp');
                    $minPurchase = $p->variants->where('purchase_price', '>', 0)->min('purchase_price');
                    $totalStock = $p->variants->sum('stock_quantity');

                    $p->update([
                        'price' => $minRetail ?: $p->price,
                        'wholesale_price' => $minWholesale ?: $p->wholesale_price,
                        'mrp' => $minMrp ?: $p->mrp,
                        'purchase_price' => $minPurchase ?: $p->purchase_price,
                        'stock_quantity' => $totalStock > 0 ? $totalStock : $p->stock_quantity,
                    ]);
                }
            }
        });

        $totalRows = count($rawRows);
        $parts = [];
        if ($updatedProductsCount > 0) {
            $parts[] = "✅ {$updatedProductsCount} प्रोडक्ट्स के भाव बदले गए";
        }
        if ($updatedVariantsCount > 0) {
            $parts[] = "📦 {$updatedVariantsCount} साइज़ वेरिएंट्स के भाव बदले गए";
        }
        if ($unchangedCount > 0) {
            $parts[] = "🟢 {$unchangedCount} आइटम्स पहले से अप-टू-डेट थे (भाव में कोई बदलाव नहीं था)";
        }
        if ($createdProductsCount > 0) {
            $parts[] = "✨ {$createdProductsCount} नए प्रोडक्ट्स जोड़े गए";
        }
        if ($skippedCount > 0) {
            $parts[] = "⚪ {$skippedCount} पंक्तियाँ छोड़ी गईं (खाली/हेडिंग लाइनें या बिना ID)";
        }

        $msg = "Excel Sync रिपोर्ट (कुल {$totalRows} पंक्तियाँ): " . implode(' | ', $parts) . '।';

        return back()->with('success', $msg);
    }

    /**
     * Batch assign selected products to a specific Folder / Rate Group (e.g. "दैनिक भाव", "मंडी भाव", "पाइप फिटिंग्स").
     */
    public function assignFolder(Request $request)
    {
        $seller = Auth::user();
        $request->validate([
            'product_ids' => 'required',
            'folder_name' => 'required|string|max:100',
        ]);

        $rawIds = $request->input('product_ids');
        $ids = is_array($rawIds) ? $rawIds : array_filter(array_map('intval', explode(',', (string)$rawIds)));
        $folderName = trim($request->input('folder_name'));

        if (empty($ids)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No products selected'], 422);
            }
            return back()->with('error', 'Kripya kam se kam ek product select karein.');
        }

        $updated = Product::where('user_id', $seller->id)
            ->whereIn('id', $ids)
            ->update(['group_name' => $folderName]);

        $msg = "Safalta! " . count($ids) . " items ko '{$folderName}' folder me set kar diya gaya hai.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'folder_name' => $folderName,
                'updated_count' => $updated,
            ]);
        }

        return back()->with('success', $msg);
    }
}
