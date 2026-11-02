<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CatalogAuditController extends Controller
{
    /**
     * Show the AI Catalog Auditor dashboard.
     */
    public function index(Request $request)
    {
        $seller = Auth::user();
        $masterFile = storage_path("app/catalogs/master_sheets/seller_{$seller->id}_master.json");

        $hasMaster = File::exists($masterFile);
        $masterMeta = null;
        $audit = null;

        if ($hasMaster) {
            $masterData = json_decode(File::get($masterFile), true);
            $masterMeta = [
                'filename' => $masterData['filename'] ?? 'Master_Sheet.xlsx',
                'updated_at' => $masterData['updated_at'] ?? null,
                'row_count' => $masterData['row_count'] ?? count($masterData['rows'] ?? []),
            ];

            $audit = $this->performAudit($seller->id, $masterData['rows'] ?? []);
        }

        return view('seller.catalog.auditor', compact('hasMaster', 'masterMeta', 'audit'));
    }

    /**
     * Upload / Save a Master Excel Sheet for the seller.
     */
    public function uploadMasterSheet(Request $request)
    {
        $seller = Auth::user();
        $rawRows = [];
        $filename = 'Master_Sheet.xlsx';

        if ($request->filled('spreadsheet_rows')) {
            $decoded = json_decode($request->input('spreadsheet_rows'), true);
            if (is_array($decoded)) {
                $rawRows = $decoded;
            }
            if ($request->filled('spreadsheet_filename')) {
                $filename = $request->input('spreadsheet_filename');
            }
        } elseif ($request->hasFile('spreadsheet_file')) {
            $file = $request->file('spreadsheet_file');
            $filename = $file->getClientOriginalName();
            $path = $file->getRealPath();

            // Parse CSV directly if csv
            if (($handle = fopen($path, 'r')) !== false) {
                $bom = fread($handle, 3);
                if ($bom !== "\xEF\xBB\xBF") {
                    rewind($handle);
                }
                $header = null;
                while (($data = fgetcsv($handle, 3000, ',')) !== false) {
                    if (!$header) {
                        $header = array_map('trim', $data);
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
            return back()->with('error', 'Kripya ek valid Excel ya CSV file select karein.');
        }

        $storageDir = storage_path('app/catalogs/master_sheets');
        if (!File::exists($storageDir)) {
            File::makeDirectory($storageDir, 0777, true, true);
        }

        $payload = [
            'updated_at' => now()->toIso8601String(),
            'filename' => $filename,
            'row_count' => count($rawRows),
            'rows' => $rawRows,
        ];

        File::put("{$storageDir}/seller_{$seller->id}_master.json", json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return redirect()->route('seller.catalog.auditor')->with('success', "Master Excel Sheet '{$filename}' (कुल " . count($rawRows) . " पंक्तियाँ) सफलतापूर्वक सेव हो गई और AI ऑडिट तैयार है!");
    }

    /**
     * Perform deep comparison between Master Excel rows and Live DB records.
     */
    public function performAudit(int $sellerId, array $excelRows): array
    {
        $products = Product::where('user_id', $sellerId)->with('variants')->get();

        $dbBySku = [];
        $dbByNameSize = [];
        $dbByVarId = [];
        $dbByProdId = [];
        $totalLiveVariants = 0;
        $totalLiveCards = $products->count();

        foreach ($products as $p) {
            $dbByProdId[$p->id] = [
                'type' => 'product',
                'product' => $p,
                'variant' => null,
            ];

            if ($p->sku) {
                $dbBySku[strtolower(trim($p->sku))] = [
                    'type' => 'product',
                    'product' => $p,
                    'variant' => null,
                ];
            }

            if ($p->variants && $p->variants->isNotEmpty()) {
                foreach ($p->variants as $v) {
                    $totalLiveVariants++;
                    $dbByVarId[$v->id] = [
                        'type' => 'variant',
                        'product' => $p,
                        'variant' => $v,
                    ];
                    if ($v->sku) {
                        $dbBySku[strtolower(trim($v->sku))] = [
                            'type' => 'variant',
                            'product' => $p,
                            'variant' => $v,
                        ];
                    }
                    $vName = strtolower(trim($v->variant_name ?: $v->size));
                    $pName = strtolower(trim($p->name));
                    $dbByNameSize["{$pName}___{$vName}"] = [
                        'type' => 'variant',
                        'product' => $p,
                        'variant' => $v,
                    ];
                }
            } else {
                $totalLiveVariants++;
                $pName = strtolower(trim($p->name));
                $dbByNameSize["{$pName}___standard"] = [
                    'type' => 'product',
                    'product' => $p,
                    'variant' => null,
                ];
            }
        }

        $matched = [];
        $rateMismatches = [];
        $missingInDb = [];
        $matchedDbKeys = [];

        foreach ($excelRows as $row) {
            // Build normalized keys map
            $normRow = [];
            foreach ($row as $k => $v) {
                $cleanK = preg_replace('/[^a-zA-Z0-9_]/', '', str_replace([' ', '/', '-', '(', ')', '%'], '_', strtolower(trim((string)$k))));
                $cleanK = trim($cleanK, '_');
                $normRow[$cleanK] = $v;
            }

            $getVal = function(array $aliases) use ($normRow) {
                foreach ($aliases as $k) {
                    $clean = trim(preg_replace('/[^a-zA-Z0-9_]/', '', str_replace([' ', '/', '-', '(', ')', '%'], '_', strtolower($k))), '_');
                    if (isset($normRow[$clean]) && $normRow[$clean] !== '' && !is_null($normRow[$clean])) {
                        return trim((string)$normRow[$clean]);
                    }
                }
                foreach ($normRow as $rk => $rv) {
                    if (trim((string)$rv) === '') continue;
                    foreach ($aliases as $k) {
                        $clean = trim(preg_replace('/[^a-zA-Z0-9_]/', '', str_replace([' ', '/', '-', '(', ')', '%'], '_', strtolower($k))), '_');
                        if (strlen($clean) >= 4 && str_contains($rk, $clean)) {
                            return trim((string)$rv);
                        }
                    }
                }
                return '';
            };

            $prodId = intval(preg_replace('/[^0-9]/', '', $getVal(['product_id', 'product_id_do_not_change', 'id', 'item_id']) ?: '0'));
            $varId = intval(preg_replace('/[^0-9]/', '', $getVal(['variant_id', 'variant_id_optional', 'var_id']) ?: '0'));
            $code = $getVal(['sku', 'code', 'product_code', 'sku_code', 'itam_code', 'item_code']);
            $name = $getVal(['product_name', 'name', 'item_name', 'title']);

            if (!$name || str_contains(strtoupper($name), '---') || str_contains(strtoupper($name), 'HEADING')) {
                continue;
            }

            // Detect prices from Excel
            $excelPriceRaw = $getVal(['selling_price', 'sale_a', 'retail_price', 'price', 'rate_a', 'selling_price_sale_a']);
            $excelPrice = is_numeric(preg_replace('/[^0-9.]/', '', $excelPriceRaw)) ? (float)preg_replace('/[^0-9.]/', '', $excelPriceRaw) : 0.0;

            $excelMrpRaw = $getVal(['mrp', 'maximum_retail_price', 'mrp_list_bhaav']);
            $excelMrp = is_numeric(preg_replace('/[^0-9.]/', '', $excelMrpRaw)) ? (float)preg_replace('/[^0-9.]/', '', $excelMrpRaw) : 0.0;

            // Find matching item in DB - Priority: Variant ID > Product ID > SKU > Name/Size
            $dbItem = null;
            if ($varId && isset($dbByVarId[$varId])) {
                $dbItem = $dbByVarId[$varId];
                $matchedDbKeys["var_{$varId}"] = true;
            } elseif ($prodId && isset($dbByProdId[$prodId])) {
                $dbItem = $dbByProdId[$prodId];
                $matchedDbKeys["prod_{$prodId}"] = true;
            } elseif ($code) {
                $codeKey = strtolower($code);
                if (isset($dbBySku[$codeKey])) {
                    $dbItem = $dbBySku[$codeKey];
                    $matchedDbKeys[$codeKey] = true;
                }
            }

            if (!$dbItem) {
                // Try to extract base name and size
                $size = '';
                if (preg_match('~\b(\d+[\*xX/]\d+|\d+(\.\d+)?\s*(mm|inch|")?)\b~i', $name, $sm)) {
                    $size = strtolower(trim($sm[1]));
                }
                $cleanName = strtolower(trim(preg_replace('~\b(plasto|supreme|astral)\b~i', '', $name)));
                $cleanName = trim(preg_replace('~\b\d+[\*xX/]\d+\b~', '', $cleanName));
                $cleanName = trim(preg_replace('~\b\d+\s*mm\b~i', '', $cleanName));
                $key = "{$cleanName}___{$size}";

                if (isset($dbByNameSize[$key])) {
                    $dbItem = $dbByNameSize[$key];
                }
            }

            if ($dbItem) {
                $prod = $dbItem['product'];
                $var = $dbItem['variant'];

                $dbPrice = (float)($var ? ($var->retail_price ?: $var->price) : ($prod->price ?: 0));
                $dbMrp = (float)($var ? ($var->mrp ?: $prod->mrp) : ($prod->mrp ?: 0));
                $dbName = $prod->name;
                $dbVariantName = $var ? ($var->variant_name ?: $var->size) : 'Standard';

                $priceDiff = abs($dbPrice - $excelPrice);
                $mrpDiff = abs($dbMrp - $excelMrp);

                $itemInfo = [
                    'code' => $code,
                    'excel_name' => $name,
                    'db_name' => $dbName,
                    'db_variant' => $dbVariantName,
                    'excel_price' => $excelPrice,
                    'db_price' => $dbPrice,
                    'price_diff' => $excelPrice - $dbPrice,
                    'excel_mrp' => $excelMrp,
                    'db_mrp' => $dbMrp,
                    'prod_id' => $prod->id,
                    'var_id' => $var ? $var->id : null,
                ];

                if ($excelPrice > 0 && ($priceDiff > 0.5 || ($excelMrp > 0 && $mrpDiff > 0.5))) {
                    $rateMismatches[] = $itemInfo;
                } else {
                    $matched[] = $itemInfo;
                }
            } else {
                $missingInDb[] = [
                    'code' => $code,
                    'excel_name' => $name,
                    'excel_price' => $excelPrice,
                    'excel_mrp' => $excelMrp,
                ];
            }
        }

        return [
            'total_excel_rows' => count($excelRows),
            'total_live_cards' => $totalLiveCards,
            'total_live_variants' => $totalLiveVariants,
            'matched_count' => count($matched),
            'mismatch_count' => count($rateMismatches),
            'missing_count' => count($missingInDb),
            'matched' => $matched,
            'rate_mismatches' => $rateMismatches,
            'missing' => $missingInDb,
        ];
    }

    /**
     * 1-Click Sync: Apply All Excel Rates to Live DB cards.
     */
    public function syncMismatches(Request $request)
    {
        $seller = Auth::user();
        $masterFile = storage_path("app/catalogs/master_sheets/seller_{$seller->id}_master.json");

        if (!File::exists($masterFile)) {
            return back()->with('error', 'Master Excel Sheet nahi mili. Kripya pehle sheet upload karein.');
        }

        $masterData = json_decode(File::get($masterFile), true);
        $audit = $this->performAudit($seller->id, $masterData['rows'] ?? []);

        $updatedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($audit['rate_mismatches'] as $item) {
                if ($item['var_id']) {
                    ProductVariant::where('id', $item['var_id'])->update([
                        'retail_price' => $item['excel_price'],
                        'mrp' => $item['excel_mrp'] ?: DB::raw('mrp'),
                        'wholesale_price' => round($item['excel_price'] * 0.88, 2),
                        'updated_at' => now(),
                    ]);
                    $updatedCount++;
                } elseif ($item['prod_id']) {
                    Product::where('id', $item['prod_id'])->update([
                        'price' => $item['excel_price'],
                        'mrp' => $item['excel_mrp'] ?: DB::raw('mrp'),
                        'wholesale_price' => round($item['excel_price'] * 0.88, 2),
                        'updated_at' => now(),
                    ]);
                    $updatedCount++;
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Sync error: ' . $e->getMessage());
        }

        return redirect()->route('seller.catalog.auditor')->with('success', "🎉 बधाई हो! कुल {$updatedCount} आइटम्स के भाव एक्सेल शीट के मुताबिक 1-क्लिक में लाइव अपडेट कर दिए गए हैं। अब पूरा स्टोर 100% सिंक है!");
    }
}
