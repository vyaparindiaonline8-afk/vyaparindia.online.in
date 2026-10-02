<?php

namespace App\Services;

use App\Models\CatalogIngestionJob;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class AICatalogIngestionService
{
    protected AIProductService $aiProductService;

    public function __construct(AIProductService $aiProductService)
    {
        $this->aiProductService = $aiProductService;
    }

    /**
     * Process an uploaded PDF catalog through PyMuPDF and AI extraction.
     */
    public function processCatalog(CatalogIngestionJob $job): CatalogIngestionJob
    {
        $job->update(['status' => 'processing']);

        $pdfPath = storage_path('app/public/' . $job->file_path);
        if (!file_exists($pdfPath)) {
            $pdfPath = storage_path('app/' . $job->file_path);
        }

        $outputDir = public_path('storage/catalog_extracted');
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $pythonExec = (PHP_OS_FAMILY === 'Windows' && file_exists('C:\\Python313\\python.exe')) ? 'C:\\Python313\\python.exe' : 'python3';
        
        $ext = strtolower(pathinfo($job->filename, PATHINFO_EXTENSION));
        $isExcel = in_array($ext, ['xlsx', 'xls', 'csv']);
        $pythonScript = $isExcel ? base_path('app/Services/excel_catalog_parser.py') : base_path('app/Services/pdf_catalog_parser.py');

        $extractedData = null;

        if (file_exists($pdfPath) && file_exists($pythonScript)) {
            try {
                $process = new Process([$pythonExec, $pythonScript, $pdfPath, $outputDir, (string)$job->id]);
                $process->setTimeout(180);
                $process->run();

                $output = $process->getOutput();
                $jsonOutputFile = $outputDir . DIRECTORY_SEPARATOR . "job_{$job->id}_extracted.json";

                if (file_exists($jsonOutputFile)) {
                    $jsonContent = file_get_contents($jsonOutputFile);
                    $extractedData = json_decode($jsonContent, true);
                }
            } catch (\Exception $e) {
                Log::warning("Catalog parser error for job #{$job->id}: " . $e->getMessage());
            }
        }

        // Fallback or intelligent enrichment if python was skipped or didn't extract products
        if (!$extractedData || empty($extractedData['products'])) {
            $cleanName = pathinfo($job->filename, PATHINFO_FILENAME);
            $cleanName = ucwords(str_replace(['_', '-', '.'], ' ', $cleanName));

            $aiDetails = $this->aiProductService->generateProductDetails($cleanName);

            $extractedData = [
                'job_id' => $job->id,
                'total_products' => 1,
                'total_images_extracted' => 0,
                'products' => [
                    [
                        'name' => $aiDetails['enhanced_title'],
                        'category' => 'Industrial & Commercial',
                        'description' => $aiDetails['description'],
                        'base_price' => $aiDetails['suggested_price'],
                        'mrp' => $aiDetails['suggested_mrp'],
                        'image_url' => null,
                        'hsn_code' => $aiDetails['hsn_code'],
                        'sku' => $aiDetails['sku'],
                        'stock_quantity' => null, // Optional ("dale to thik na dale to thik")
                        'variants' => [
                            [
                                'variant_name' => 'Standard Size (Base)',
                                'size' => 'Standard',
                                'grade' => 'Heavy Duty',
                                'raw_rate' => $aiDetails['suggested_price'],
                                'sku' => $aiDetails['sku'] . '-STD',
                                'stock_quantity' => null,
                            ]
                        ]
                    ]
                ]
            ];
        }

        // Enrich and standardize each product & variant with GST costing matrix
        foreach ($extractedData['products'] as &$prod) {
            $isPipe = preg_match('/pipe|fitting|cpvc|pvc|valve|plumb/i', $prod['name']);
            $isTextile = preg_match('/saree|kurta|shirt|cloth|apparel|silk/i', $prod['name']);
            $isElectrical = preg_match('/wire|cable|switch|light|bulb|fan/i', $prod['name']);

            $defaultGst = 18.0;
            if ($isTextile) {
                $defaultGst = 5.0;
            } elseif ($isPipe) {
                $defaultGst = 18.0;
            } elseif ($isElectrical) {
                $defaultGst = 18.0;
            }

            $prod['gst_percent'] = $prod['gst_percent'] ?? $defaultGst;
            $prod['trade_discount_percent'] = $prod['trade_discount_percent'] ?? 0.0;
            $prod['stock_quantity'] = $prod['stock_quantity'] ?? null;

            if (empty($prod['description']) || strlen($prod['description']) < 30) {
                $ai = $this->aiProductService->generateProductDetails($prod['name']);
                $prod['description'] = $ai['description'];
            }

            foreach ($prod['variants'] as &$var) {
                $rawRate = floatval($var['raw_rate'] ?? $prod['base_price'] ?? 100);
                $discount = floatval($prod['trade_discount_percent'] ?? 0);
                $gst = floatval($prod['gst_percent'] ?? 18);

                // Landing cost calculation: List Price - Trade Discount %
                $landingCostWithoutGst = round($rawRate * (1 - ($discount / 100)), 2);
                $gstAmount = round($landingCostWithoutGst * ($gst / 100), 2);
                $landingCostWithGst = round($landingCostWithoutGst + $gstAmount, 2);

                $var['raw_rate'] = $rawRate;
                $var['trade_discount_percent'] = $discount;
                $var['gst_percent'] = $gst;
                $var['landing_cost_without_gst'] = $landingCostWithoutGst;
                $var['landing_cost_with_gst'] = $landingCostWithGst;

                // Suggested Margins: Wholesale ~15%, Retail ~35%, MRP ~60%
                $var['wholesale_price'] = round($landingCostWithGst * 1.15, 2);
                $var['retail_price'] = round($landingCostWithGst * 1.35, 2);
                $var['mrp'] = round($landingCostWithGst * 1.60, 2);
                $var['stock_quantity'] = $var['stock_quantity'] ?? null; // Optional
            }
        }

        $job->update([
            'status' => 'ready_for_review',
            'total_products_detected' => count($extractedData['products']),
            'extracted_data' => $extractedData,
        ]);

        return $job;
    }
}
