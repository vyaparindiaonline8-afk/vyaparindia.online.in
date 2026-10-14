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
        $quotaExceededCount = 0;
        $unmatchedCount = 0;
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
            $s = preg_replace('~\b\d+(\.\d+)?\s*[\*xX/]\s*\d+(\.\d+)?\s*[\*xX/]\s*\d+(\.\d+)?(\s*mm)?\b~i', '', $s);
            $s = preg_replace('~\b\d+(\.\d+)?\s*[\*xX/]\s*\d+(\.\d+)?(\s*mm)?\b~i', '', $s);
            // Strip fractional inch & simple mm/inch/ft
            $s = preg_replace('~\b\d+\s*[-–]?\s*\d+/\d+(")?\b~i', '', $s);
            $s = preg_replace('~\b\d+/\d+(")?\b~i', '', $s);
            $s = preg_replace('~\b\d+(\.\d+)?\s*(mm|inch|feet|ft|")\b~i', '', $s);
            $s = preg_replace('~[^a-z0-9]~', ' ', $s);
            return trim(preg_replace('~\s+~', ' ', $s));
        };

        $extractWordSet = function(?string $text): array {
            if (!$text) return [];
            $s = strtolower(trim($text));
            $s = str_replace(['*', '°', '/', '-', '_', '(', ')', '[', ']', '.', ':'], ' ', $s);
            $s = preg_replace('~\b(plasto|supreme|astral|ashirvad|finolex|prince)\b~i', ' ', $s);
            $s = preg_replace('~\b\d+(\.\d+)?\s*[\*xX/]\s*\d+(\.\d+)?\s*[\*xX/]\s*\d+(\.\d+)?(\s*mm)?\b~i', ' ', $s);
            $s = preg_replace('~\b\d+(\.\d+)?\s*[\*xX/]\s*\d+(\.\d+)?(\s*mm)?\b~i', ' ', $s);
            $s = preg_replace('~\b\d+\s*[-–]?\s*\d+/\d+(")?\b~i', ' ', $s);
            $s = preg_replace('~\b\d+/\d+(")?\b~i', ' ', $s);
            $s = preg_replace('~\b\d+(\.\d+)?\s*(mm|inch|feet|ft|")\b~i', ' ', $s);
            $s = preg_replace('~[^a-z0-9]~', ' ', $s);
            $words = array_filter(explode(' ', $s), function($w) {
                return strlen(trim($w)) > 0 && !in_array($w, ['sch', 'item', 'items', 'fitting', 'fittings']);
            });
            return array_unique(array_values($words));
        };

        $sellerProducts = Product::where('user_id', $seller->id)->with('variants')->get();

        // 🚀 High-Performance In-Memory Pre-loading to prevent 502 Bad Gateway timeouts on Render
        $masterByCode = [];
        $masterByName = [];
        try {
            $brandMasters = \App\Models\BrandMasterProduct::select('product_code', 'product_name', 'image_url')->get();
            foreach ($brandMasters as $bm) {
                if (!empty($bm->product_code)) {
                    $masterByCode[strtoupper(trim($bm->product_code))] = $bm->image_url;
                }
                if (!empty($bm->product_name)) {
                    $masterByName[strtolower(trim($bm->product_name))] = $bm->image_url;
                }
            }
        } catch (\Throwable $e) {}

        $allCats = Category::all()->keyBy(function($c) {
            return strtolower(trim($c->name));
        });

        $getOrCreateCat = function(?string $cName) use (&$allCats) {
            if (empty($cName)) return null;
            $clean = ucwords(trim($cName));
            $k = strtolower($clean);
            if (isset($allCats[$k])) {
                return $allCats[$k]->id;
            }
            $cat = Category::firstOrCreate(
                ['name' => $clean],
                ['slug' => Str::slug($clean) ?: ('cat-' . time() . '-' . rand(10, 99))]
            );
            $allCats[$k] = $cat;
            return $cat->id;
        };

        // Comprehensive Size & Base extraction for pipes, fittings, solvents (ML, LTR, GM, KG, TUBE, CANE)
        $extractSizeAndBase = function(?string $full_name): array {
            if (empty($full_name)) return ['', 'Standard'];
            $s = trim($full_name);

            // 1. Cross dimensions: 110*75*50, 25*20, etc.
            if (preg_match('~\b\d+(?:\.\d+)?\s*[\*xX/]\s*\d+(?:\.\d+)?(?:\s*[\*xX/]\s*\d+(?:\.\d+)?)*\s*(?:mm|inch|")?\b~i', $s, $m, PREG_OFFSET_CAPTURE)) {
                $size = trim($m[0][0]);
                $base = trim(substr($s, 0, $m[0][1]) . ' ' . substr($s, $m[0][1] + strlen($m[0][0])));
                return [preg_replace('/\s+/', ' ', $base), $size];
            }

            // 2. Fractional inches: 1/2", 3/4", 1-1/4", etc.
            if (preg_match('~\b(?:\d+\s*[-–]?\s*)?\d+\/\d+\s*(?:"|inch)?\b~i', $s, $m, PREG_OFFSET_CAPTURE)) {
                $size = trim($m[0][0]);
                $base = trim(substr($s, 0, $m[0][1]) . ' ' . substr($s, $m[0][1] + strlen($m[0][0])));
                return [preg_replace('/\s+/', ' ', $base), $size];
            }

            // 3. Metric mm or simple inch: 15MM, 20MM, 110 MM, 2"
            if (preg_match('~\b\d+(?:\.\d+)?\s*(?:mm|inch|")\b~i', $s, $m, PREG_OFFSET_CAPTURE)) {
                $size = trim($m[0][0]);
                $base = trim(substr($s, 0, $m[0][1]) . ' ' . substr($s, $m[0][1] + strlen($m[0][0])));
                return [preg_replace('/\s+/', ' ', $base), $size];
            }

            // 4. Volume / Liquid / Weight: 50ML, 118ML, 1LTR, 15ML TUBE, 50ML CANE, 500GM, 1KG
            if (preg_match('~\b\d+(?:\.\d+)?\s*(?:ml|ltr|gm|kg)(?:\s*(?:tube|cane|tin|can))?\b~i', $s, $m, PREG_OFFSET_CAPTURE)) {
                $size = trim($m[0][0]);
                $base = trim(substr($s, 0, $m[0][1]) . ' ' . substr($s, $m[0][1] + strlen($m[0][0])));
                return [preg_replace('/\s+/', ' ', $base), $size];
            }

            // 5. Packaging words: TUBE, CANE
            if (preg_match('~\b(?:tube|cane|tin|can)\b~i', $s, $m, PREG_OFFSET_CAPTURE)) {
                $size = trim($m[0][0]);
                $base = trim(substr($s, 0, $m[0][1]) . ' ' . substr($s, $m[0][1] + strlen($m[0][0])));
                return [preg_replace('/\s+/', ' ', $base), $size];
            }

            // 6. Length: 10 FEET, 20 FEET
            if (preg_match('~\b\d+(?:\.\d+)?\s*(?:feet|ft|mtr)\b~i', $s, $m, PREG_OFFSET_CAPTURE)) {
                $size = trim($m[0][0]);
                $base = trim(substr($s, 0, $m[0][1]) . ' ' . substr($s, $m[0][1] + strlen($m[0][0])));
                return [preg_replace('/\s+/', ' ', $base), $size];
            }

            return [$s, 'Standard'];
        };

        $detectedCols = [];

        DB::transaction(function () use ($rawRows, $seller, $sellerProducts, $allowCreateNew, $normalizeForMatching, $extractWordSet, $masterByCode, $masterByName, $getOrCreateCat, $extractSizeAndBase, &$updatedProductsCount, &$updatedVariantsCount, &$unchangedCount, &$createdProductsCount, &$skippedCount, &$affectedProductIds, &$quotaExceededCount, &$unmatchedCount, &$detectedCols) {
            foreach ($rawRows as $row) {
                // Pre-build normalized key-value map for robust header lookup
                $normMap = [];
                $originalKeyMap = [];
                foreach ($row as $k => $v) {
                    $rawK = trim((string)$k);
                    $normMap[$rawK] = $v;
                    $lowerK = strtolower($rawK);
                    $normMap[$lowerK] = $v;
                    $cleanK = trim(preg_replace('/_+/', '_', preg_replace('/[^a-z0-9_]/', '', str_replace([' ', '/', '-', '(', ')', '[', ']', '.', ':', '*'], '_', $lowerK))), '_');
                    if ($cleanK !== '') {
                        $normMap[$cleanK] = $v;
                        $originalKeyMap[$cleanK] = $rawK;
                    }
                }

                $getValWithKey = function(array $aliases) use ($normMap, $originalKeyMap) {
                    foreach ($aliases as $alias) {
                        if (isset($normMap[$alias]) && trim((string)$normMap[$alias]) !== '') {
                            return [trim((string)$normMap[$alias]), $originalKeyMap[$alias] ?? $alias];
                        }
                    }
                    foreach ($normMap as $k => $val) {
                        if (trim((string)$val) === '') continue;
                        foreach ($aliases as $alias) {
                            if (strlen($alias) >= 4 && str_contains($k, $alias)) {
                                return [trim((string)$val), $originalKeyMap[$k] ?? $k];
                            }
                        }
                    }
                    return [null, null];
                };

                $getVal = function(array $aliases) use ($getValWithKey) {
                    return $getValWithKey($aliases)[0];
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

                [$name, $nameKey] = $getValWithKey([
                    'product_name', 'product_name_aaitm_naam', 'name', 'item_name', 'title',
                    'product_name__aaitm_naam_', 'aaitm_naam'
                ]);
                if ($nameKey && !isset($detectedCols['Name'])) $detectedCols['Name'] = $nameKey;

                [$categoryName, $catKey] = $getValWithKey([
                    'category', 'category_kaitaigarii', 'category_name', 'category__kaitaigarii_'
                ]);
                if ($catKey && !isset($detectedCols['Category'])) $detectedCols['Category'] = $catKey;

                [$brand, $brandKey] = $getValWithKey([
                    'brand', 'brand_kanpnii', 'brand_company', 'company', 'brand__kanpnii_'
                ]);

                [$groupName, $groupKey] = $getValWithKey([
                    'group', 'group_grup', 'group_name', 'group__grup_'
                ]);
                if ($groupKey && !isset($detectedCols['Group'])) $detectedCols['Group'] = $groupKey;

                [$sizeVariant, $sizeKey] = $getValWithKey([
                    'size__variant_name__saaiz___prkaar_', 'size_variant_name', 'size_variant',
                    'size', 'variant_name', 'variant', 'dimension', 'saaiz'
                ]);
                if ($sizeKey && !isset($detectedCols['Size'])) $detectedCols['Size'] = $sizeKey;

                [$sellingPrice, $spKey] = [
                    $getFloat(['sale_a', 'selling_price', 'selling_price_bikrii_bhaav', 'retail_price', 'price', 'selling_price__bikrii_bhaav_', 'rate_a', 'sale_price']),
                    $getValWithKey(['sale_a', 'selling_price', 'retail_price', 'price', 'rate_a'])[1]
                ];
                if ($spKey && !isset($detectedCols['Price A'])) $detectedCols['Price A'] = $spKey;

                $wholesalePrice = $getFloat([
                    'sale_b', 'wholesale_price', 'wholesale_price_thok_bhaav_b2b', 'wholesale_price_thok_bhaav',
                    'wholesale_price__thok_bhaav_b2b_', 'wholesale_price__thok_bhaav_', 'rate_b', 'b2b_price'
                ]);

                $purchasePrice = $getFloat([
                    'p_cost', 'purchase_price', 'purchase_price_khariid_bhaav_cost', 'purchase_price_cost',
                    'purchase_price_khariid_bhaav', 'purchase_price__khariid_bhaav___cost_', 'cost', 'raw_rate'
                ]);

                $mrp = $getFloat(['mrp', 'maximum_retail_price']);

                $stock = $getInt([
                    'opn_stock', 'current_stock', 'current_stock_sttok_sankhyaa', 'stock_quantity',
                    'stock_quantity_sttok', 'stock', 'stock_quantity__sttok_', 'current_stock__sttok_sankhyaa_',
                    'opnstock', 'open_stock'
                ]);

                $desc = $getVal([
                    'description', 'description_vivrn', 'desc', 'description__vivrn_'
                ]);

                [$productCode, $codeKey] = $getValWithKey([
                    'itam_code', 'item_code', 'product_code', 'code', 'sku', 'itam_kod', 'item_kod'
                ]);
                if ($codeKey && !isset($detectedCols['Code'])) $detectedCols['Code'] = $codeKey;

                // Smart extraction: If sizeVariant is missing, derive base name and size variant
                $extractedBaseName = $name;
                if (!empty($name)) {
                    [$autoBase, $autoSize] = $extractSizeAndBase($name);
                    if (empty($sizeVariant) && $autoSize !== 'Standard') {
                        $sizeVariant = $autoSize;
                    }
                    if (!empty($autoBase)) {
                        $extractedBaseName = $autoBase;
                    }
                }

                // Disambiguate base card name by prefixing Material / Category if missing (e.g. UPVC UNION vs CPVC UNION)
                $combinedMaterial = '';
                $catContext = trim(($categoryName ?? '') . ' ' . ($groupName ?? ''));
                foreach (['UPVC', 'CPVC', 'SWR', 'AGRI'] as $mat) {
                    if (stripos($catContext, $mat) !== false) {
                        $combinedMaterial = $mat;
                        break;
                    }
                }
                if ($combinedMaterial && stripos($extractedBaseName, $combinedMaterial) === false) {
                    $familyBaseName = $combinedMaterial . ' ' . $extractedBaseName;
                } else {
                    $familyBaseName = $extractedBaseName;
                }

                // Resolve Product: Case 1 by ID, Case 2 by SKU/Code, Case 3 by Name / Base Family
                $product = null;
                if ($rawId && $rawId > 0) {
                    $product = $sellerProducts->firstWhere('id', $rawId);
                }

                if (!$product && !empty($productCode)) {
                    $cleanCode = strtoupper(trim($productCode));
                    $product = $sellerProducts->first(function($p) use ($cleanCode) {
                        return !empty($p->sku) && (strtoupper(trim($p->sku)) === $cleanCode || str_contains(strtoupper($p->sku), $cleanCode));
                    });
                }

                if (!$product && !empty($familyBaseName)) {
                    $cleanFam = strtolower(trim($familyBaseName));
                    $famSlug = Str::slug($familyBaseName);
                    $product = $sellerProducts->first(function($p) use ($cleanFam, $famSlug) {
                        return strtolower(trim($p->name)) === $cleanFam
                            || (!empty($famSlug) && $p->slug === $famSlug);
                    });

                    // Fuzzy base matching using normalizeForMatching
                    if (!$product) {
                        $normName = $normalizeForMatching($familyBaseName);
                        if (!empty($normName)) {
                            $product = $sellerProducts->first(function($p) use ($normName, $normalizeForMatching) {
                                $pNorm = $normalizeForMatching($p->name);
                                return !empty($pNorm) && ($pNorm === $normName || str_contains($pNorm, $normName) || str_contains($normName, $pNorm));
                            });
                        }
                    }

                    // Smart Word-set matching with category verification (prevents UPVC vs CPVC collision)
                    if (!$product) {
                        $rowWords = $extractWordSet($familyBaseName . ' ' . $categoryName);
                        if (!empty($rowWords)) {
                            $bestP = null;
                            $bestScore = 0;
                            foreach ($sellerProducts as $p) {
                                $pWords = $extractWordSet($p->name . ' ' . ($p->category->name ?? ''));
                                if (empty($pWords)) continue;
                                $intersect = array_intersect($rowWords, $pWords);
                                if (!empty($intersect)) {
                                    $score = count($intersect);
                                    if ($score >= 2 && ($score === count($rowWords) || $score === count($pWords))) {
                                        if ($score > $bestScore) {
                                            $bestScore = $score;
                                            $bestP = $p;
                                        }
                                    }
                                }
                            }
                            if ($bestP) {
                                $product = $bestP;
                            }
                        }
                    }
                }

                // In-Memory lookup for BrandMaster image (Zero DB queries!)
                $catalogImage = null;
                if (!empty($productCode)) {
                    $uCode = strtoupper(trim($productCode));
                    if (isset($masterByCode[$uCode])) {
                        $catalogImage = $masterByCode[$uCode];
                    }
                }
                if (!$catalogImage && !empty($name)) {
                    $lName = strtolower(trim($name));
                    if (isset($masterByName[$lName])) {
                        $catalogImage = $masterByName[$lName];
                    }
                }

                if ($product) {
                    $prodUpdates = [];

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
                        $catId = $getOrCreateCat($categoryName);
                        if ($catId && $product->category_id !== $catId) {
                            $prodUpdates['category_id'] = $catId;
                        }
                    }

                    // Check if product has variants or matches a variant row
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
                            // Fallback: adopt dummy "Standard" variant if present
                            if (!$matchedVariant) {
                                $matchedVariant = $product->variants->first(function($pv) {
                                    $vName = strtolower(trim($pv->variant_name ?? ''));
                                    return in_array($vName, ['standard', 'std', 'default', '']);
                                });
                            }
                        }
                    }

                    $isChanged = false;
                    if ($matchedVariant) {
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
                        if (!empty($productCode)) {
                            $varUpdates['sku'] = $productCode;
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
                    } elseif (!empty($sizeVariant)) {
                        $vPrice = ($sellingPrice && $sellingPrice > 0) ? $sellingPrice : (($mrp && $mrp > 0) ? $mrp : $product->price);
                        $newVar = ProductVariant::create([
                            'product_id' => $product->id,
                            'variant_name' => $sizeVariant,
                            'sku' => !empty($productCode) ? $productCode : ('VAR-' . $product->id . '-' . strtoupper(Str::random(5))),
                            'retail_price' => $vPrice,
                            'wholesale_price' => ($wholesalePrice && $wholesalePrice > 0) ? $wholesalePrice : $vPrice,
                            'purchase_price' => $purchasePrice ?? 0,
                            'mrp' => ($mrp && $mrp > 0) ? $mrp : $vPrice,
                            'stock_quantity' => !is_null($stock) ? $stock : 0,
                            'track_inventory' => !is_null($stock) && $stock > 0,
                            'attributes' => ['size' => $sizeVariant],
                        ]);
                        if (!$product->has_variants) {
                            $product->update(['has_variants' => true]);
                            $product->has_variants = true;
                        }
                        if (!$product->relationLoaded('variants') || is_null($product->variants)) {
                            $product->setRelation('variants', collect([$newVar]));
                        } else {
                            $product->variants->push($newVar);
                        }
                        $updatedVariantsCount++;
                        $affectedProductIds[$product->id] = true;
                    } else {
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
                                $prodUpdates['track_inventory'] = ($stock > 0);
                            }
                        }
                    }

                    if (empty($product->image) && $catalogImage) {
                        $prodUpdates['image'] = $catalogImage;
                        $isChanged = true;
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
                    // Create new Product if allowed
                    $hasValidPrice = (!is_null($sellingPrice) && $sellingPrice > 0) || (!is_null($mrp) && $mrp > 0);
                    if ($allowCreateNew && !empty($name) && $hasValidPrice) {
                        if ($seller->canAddProduct()) {
                            $catId = !empty($categoryName) ? $getOrCreateCat($categoryName) : null;
                            $finalPrice = ($sellingPrice && $sellingPrice > 0) ? $sellingPrice : $mrp;

                            $baseSlug = Str::slug($familyBaseName);
                            if (empty($baseSlug)) {
                                $baseSlug = 'prod-' . $seller->id . '-' . time();
                            }
                            $uniqueSlug = $baseSlug . '-' . Str::random(5);

                            $newProduct = Product::create([
                                'user_id' => $seller->id,
                                'name' => $familyBaseName,
                                'sku' => !empty($productCode) ? $productCode : ('PLASTO-' . time() . '-' . rand(10, 99)),
                                'slug' => $uniqueSlug,
                                'description' => !empty($desc) ? $desc : $name,
                                'category_id' => $catId,
                                'brand' => !empty($brand) ? $brand : 'Plasto',
                                'group_name' => !empty($groupName) ? $groupName : null,
                                'price' => $finalPrice,
                                'wholesale_price' => ($wholesalePrice && $wholesalePrice > 0) ? $wholesalePrice : $finalPrice,
                                'purchase_price' => $purchasePrice ?? 0,
                                'mrp' => ($mrp && $mrp > 0) ? $mrp : $finalPrice,
                                'stock_quantity' => !is_null($stock) ? $stock : 0,
                                'track_inventory' => !is_null($stock) && $stock > 0,
                                'has_variants' => !empty($sizeVariant),
                                'image' => $catalogImage,
                            ]);

                            $createdProductsCount++;
                            $sellerProducts->push($newProduct);

                            if (!empty($sizeVariant)) {
                                $firstVar = ProductVariant::create([
                                    'product_id' => $newProduct->id,
                                    'variant_name' => $sizeVariant,
                                    'sku' => !empty($productCode) ? $productCode : ('VAR-' . $newProduct->id . '-' . strtoupper(Str::random(5))),
                                    'retail_price' => $finalPrice,
                                    'wholesale_price' => ($wholesalePrice && $wholesalePrice > 0) ? $wholesalePrice : $finalPrice,
                                    'purchase_price' => $purchasePrice ?? 0,
                                    'mrp' => ($mrp && $mrp > 0) ? $mrp : $finalPrice,
                                    'stock_quantity' => !is_null($stock) ? $stock : 0,
                                    'track_inventory' => !is_null($stock) && $stock > 0,
                                    'attributes' => ['size' => $sizeVariant],
                                ]);
                                $newProduct->setRelation('variants', collect([$firstVar]));
                            } else {
                                $newProduct->setRelation('variants', collect());
                            }
                        } else {
                            $quotaExceededCount++;
                            $skippedCount++;
                        }
                    } else {
                        if (!$allowCreateNew && !empty($name) && $hasValidPrice) {
                            $unmatchedCount++;
                        }
                        $skippedCount++;
                    }
                }
            }

            // 🚀 Lightning-Fast 1-Query Re-sync for Base Prices
            if (!empty($affectedProductIds)) {
                $affectedProducts = Product::whereIn('id', array_keys($affectedProductIds))->with('variants')->get();
                foreach ($affectedProducts as $p) {
                    if ($p->variants->isNotEmpty()) {
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
            }
        });

        $totalRows = count($rawRows);
        $parts = [];
        if (!empty($detectedCols)) {
            $mappedList = [];
            foreach ($detectedCols as $target => $source) {
                $mappedList[] = "{$target}='{$source}'";
            }
            $parts[] = "🎯 पहचानी गई मैपिंग: [" . implode(', ', $mappedList) . "]";
        }
        if ($updatedProductsCount > 0) {
            $parts[] = "✅ {$updatedProductsCount} प्रोडक्ट्स के भाव बदले गए";
        }
        if ($updatedVariantsCount > 0) {
            $parts[] = "📦 {$updatedVariantsCount} साइज़ वेरिएंट्स के भाव बदले गए";
        }
        if ($unchangedCount > 0) {
            $parts[] = "🟢 {$unchangedCount} आइटम्स पहले से अप-टू-डेट थे";
        }
        if ($createdProductsCount > 0) {
            $parts[] = "✨ {$createdProductsCount} नए प्रोडक्ट्स जोड़े गए";
        }
        if ($unmatchedCount > 0) {
            $parts[] = "ℹ️ {$unmatchedCount} नए आइटम्स नहीं जुड़े (विकल्प अनचेक था)";
        }
        if ($quotaExceededCount > 0) {
            $limitStr = $seller->productLimit() ? "{$seller->productLimit()} प्रोडक्ट्स" : "100 प्रोडक्ट्स";
            $parts[] = "⚠️ {$quotaExceededCount} प्रोडक्ट्स लिमिट ({$limitStr}) पूरी होने के कारण नहीं जुड़े";
        }
        $otherSkipped = $skippedCount - $quotaExceededCount - $unmatchedCount;
        if ($otherSkipped > 0) {
            $parts[] = "⚪ {$otherSkipped} पंक्तियाँ छोड़ी गईं (खाली/हेडिंग लाइनें)";
        }

        $msg = "Excel Sync रिपोर्ट (कुल {$totalRows} पंक्तियाँ): " . implode(' | ', $parts) . '।';

        if ($quotaExceededCount > 0) {
            $limitStr = $seller->productLimit() ? "{$seller->productLimit()} प्रोडक्ट्स" : "100 प्रोडक्ट्स";
            session()->flash('warning', "सावधानी: आपकी प्रोडक्ट लिमिट ({$limitStr}) पूरी हो चुकी है।");
        }

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
