<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
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
        
        $products = Product::where('user_id', $seller->id)
            ->with(['category', 'variants'])
            ->orderBy('id', 'asc')
            ->get();

        $rows = [];
        $timestamp = date('Y_m_d_His');

        if ($type === 'full') {
            $filename = "VyaparIndia_Catalog_Master_{$timestamp}.csv";
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
            $filename = "VyaparIndia_Daily_Rates_{$timestamp}.csv";
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
        $createdProductsCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($rawRows, $seller, &$updatedProductsCount, &$updatedVariantsCount, &$createdProductsCount, &$skippedCount) {
            foreach ($rawRows as $row) {
                // Normalized row keys lookup helper
                $getVal = function(array $aliases) use ($row) {
                    foreach ($aliases as $alias) {
                        if (isset($row[$alias]) && trim((string)$row[$alias]) !== '') {
                            return trim((string)$row[$alias]);
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
                    'size', 'variant_name', 'variant'
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

                // Case A: Existing Product identified by ID
                if ($rawId && $rawId > 0) {
                    $product = Product::where('id', $rawId)->where('user_id', $seller->id)->first();
                    if ($product) {
                        $prodUpdates = [];

                        // 1. Allow updating Product Name directly from spreadsheet
                        if (!empty($name) && $name !== $product->name) {
                            $prodUpdates['name'] = $name;
                            $prodUpdates['slug'] = Str::slug($name) ?: ('prod-' . $product->id . '-' . time());
                        }

                        if (!is_null($sellingPrice) && $sellingPrice > 0) {
                            $prodUpdates['price'] = $sellingPrice;
                        }
                        if (!is_null($wholesalePrice) && $wholesalePrice > 0) {
                            $prodUpdates['wholesale_price'] = $wholesalePrice;
                        }
                        if (!is_null($purchasePrice) && $purchasePrice > 0) {
                            $prodUpdates['purchase_price'] = $purchasePrice;
                        }
                        if (!is_null($mrp) && $mrp > 0) {
                            $prodUpdates['mrp'] = $mrp;
                        }
                        if (!is_null($stock)) {
                            $prodUpdates['stock_quantity'] = $stock;
                            $prodUpdates['track_inventory'] = true;
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

                        if (!empty($prodUpdates)) {
                            $product->update($prodUpdates);
                            $updatedProductsCount++;
                        }

                        // 2. If row specifies variant_id, update variant specific fields
                        if ($variantId && $variantId > 0) {
                            $variant = $product->variants()->where('id', $variantId)->first();
                            if ($variant) {
                                $varUpdates = [];
                                if (!is_null($sellingPrice) && $sellingPrice > 0) $varUpdates['retail_price'] = $sellingPrice;
                                if (!is_null($wholesalePrice) && $wholesalePrice > 0) $varUpdates['wholesale_price'] = $wholesalePrice;
                                if (!is_null($purchasePrice) && $purchasePrice > 0) {
                                    $varUpdates['purchase_price'] = $purchasePrice;
                                    $varUpdates['raw_rate'] = $purchasePrice;
                                }
                                if (!is_null($mrp) && $mrp > 0) $varUpdates['mrp'] = $mrp;
                                if (!is_null($stock)) {
                                    $varUpdates['stock_quantity'] = $stock;
                                    $varUpdates['track_inventory'] = true;
                                }
                                if (!empty($sizeVariant)) $varUpdates['variant_name'] = $sizeVariant;

                                if (!empty($varUpdates)) {
                                    $variant->update($varUpdates);
                                    $updatedVariantsCount++;
                                }
                            }
                        }
                    } else {
                        $skippedCount++;
                    }
                } else {
                    // Case B: No product_id provided (e.g. newly typed row at bottom of sheet) -> Create New Product
                    if (!empty($name) && (!is_null($sellingPrice) || !is_null($mrp))) {
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
                            $newProduct = Product::create([
                                'user_id' => $seller->id,
                                'name' => $name,
                                'slug' => Str::slug($name) ?: ('prod-' . time() . '-' . rand(100, 999)),
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
                                'has_variants' => false,
                            ]);

                            $createdProductsCount++;
                        } else {
                            $skippedCount++;
                        }
                    } else {
                        $skippedCount++;
                    }
                }
            }
        });

        $msg = "Excel Sync Safalta Purvak Poora Hua: {$updatedProductsCount} products ke rates & details update hue";
        if ($updatedVariantsCount > 0) {
            $msg .= " ({$updatedVariantsCount} size variants updated)";
        }
        if ($createdProductsCount > 0) {
            $msg .= ", {$createdProductsCount} naye items add hue";
        }
        if ($skippedCount > 0) {
            $msg .= ". Note: {$skippedCount} rows skip hui (ID match na hone ya khali hone ke karan).";
        } else {
            $msg .= "!";
        }

        return back()->with('success', $msg);
    }
}
