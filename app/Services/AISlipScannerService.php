<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;

class AISlipScannerService
{
    /**
     * Parse raw text (from contractor or OCR) into structured bill-of-materials line items.
     */
    public function parseSlipText(string $rawText, ?int $sellerId = null): array
    {
        $lines = explode("\n", $rawText);
        $extractedItems = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strlen($line) < 2) {
                continue;
            }

            // Detect multiple items separated by comma, plus or semicolons
            $subItems = preg_split('/[,;+]+/', $line);
            foreach ($subItems as $itemStr) {
                $itemStr = trim($itemStr);
                if (empty($itemStr) || strlen($itemStr) < 2) {
                    continue;
                }

                $parsed = $this->extractItemSpecs($itemStr);
                if ($parsed) {
                    $extractedItems[] = $parsed;
                }
            }
        }

        // If nothing extracted, try whole block
        if (empty($extractedItems)) {
            $parsed = $this->extractItemSpecs($rawText);
            if ($parsed) {
                $extractedItems[] = $parsed;
            }
        }

        // Match against Seller's catalog if sellerId provided
        $matchedResults = [];
        $totalEstimated = 0;
        $totalMatchedCount = 0;

        foreach ($extractedItems as $item) {
            $match = null;
            if ($sellerId) {
                $match = $this->findMatchingProduct($item, $sellerId);
            }

            if ($match) {
                $unitPrice = floatval($match->price);
                $lineTotal = $unitPrice * $item['quantity'];
                $totalEstimated += $lineTotal;
                $totalMatchedCount++;

                $matchedResults[] = [
                    'item_name' => $item['raw_name'],
                    'detected_size' => $item['size'],
                    'quantity' => $item['quantity'],
                    'is_matched' => true,
                    'matched_product_id' => $match->id,
                    'matched_product_name' => $match->name,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                    'image' => $match->image,
                    'in_stock' => $match->track_inventory ? ($match->stock_quantity > 0) : true,
                    'stock_quantity' => $match->stock_quantity,
                ];
            } else {
                // Estimate fallback price based on standard hardware rates
                $fallbackPrice = $this->estimateMarketPrice($item['raw_name']);
                $lineTotal = $fallbackPrice * $item['quantity'];
                $totalEstimated += $lineTotal;

                $matchedResults[] = [
                    'item_name' => $item['raw_name'],
                    'detected_size' => $item['size'],
                    'quantity' => $item['quantity'],
                    'is_matched' => false,
                    'matched_product_id' => null,
                    'matched_product_name' => 'Market Item (Custom Sourced)',
                    'unit_price' => $fallbackPrice,
                    'line_total' => $lineTotal,
                    'image' => null,
                    'in_stock' => true,
                    'stock_quantity' => null,
                ];
            }
        }

        return [
            'total_items_detected' => count($matchedResults),
            'matched_in_store' => $totalMatchedCount,
            'total_estimated_amount' => $totalEstimated,
            'items' => $matchedResults,
        ];
    }

    /**
     * Extract quantity, size, and clean name from raw string
     */
    protected function extractItemSpecs(string $text): ?array
    {
        // 1. Quantity detection: "10 pcs", "5 nos", "20 mtr", "4x", "10"
        $qty = 1;
        if (preg_match('/(?:^|\s)(\d+)\s*(?:pcs|nos|no|pkt|packet|bag|mtr|meter|box|units|x)?\b/i', $text, $qtyMatch)) {
            $qty = intval($qtyMatch[1]);
            // Remove quantity portion
            $text = preg_replace('/(?:^|\s)' . preg_quote($qtyMatch[0], '/') . '/i', ' ', $text);
        }

        // 2. Size detection: "1/2 inch", '3/4"', "1 inch", "25mm", "40mm", "SCH 40", "SDR 11"
        $size = null;
        if (preg_match('/(\d+(?:\/\d+)?\s*(?:inch|"|mm|cm|mtr|ft|sdr\s*\d+|sch\s*\d+))\b/i', $text, $sizeMatch)) {
            $size = trim($sizeMatch[1]);
        }

        $cleanName = trim(preg_replace('/\s+/', ' ', $text));
        $cleanName = trim($cleanName, " -:.,\t");

        if (strlen($cleanName) < 2) {
            return null;
        }

        return [
            'raw_name' => ucwords($cleanName),
            'quantity' => max(1, $qty),
            'size' => $size,
        ];
    }

    /**
     * Search Seller's database for closest matching product
     */
    protected function findMatchingProduct(array $item, int $sellerId): ?Product
    {
        $keywords = explode(' ', strtolower($item['raw_name']));
        $query = Product::where('user_id', $sellerId);

        // First attempt: match on primary keywords (e.g. pipe, valve, elbow, socket)
        $primaryKeyword = $keywords[0] ?? '';
        if (strlen($primaryKeyword) >= 3) {
            $query->where('name', 'like', "%{$primaryKeyword}%");
        }

        $candidates = $query->take(5)->get();

        if ($candidates->isNotEmpty()) {
            // Find candidate that best matches size if size detected
            if ($item['size']) {
                foreach ($candidates as $cand) {
                    if (stripos($cand->name, $item['size']) !== false || stripos($cand->description, $item['size']) !== false) {
                        return $cand;
                    }
                }
            }
            return $candidates->first();
        }

        // Fallback: search across all products of seller by any keyword
        foreach ($keywords as $kw) {
            if (strlen($kw) >= 3) {
                $match = Product::where('user_id', $sellerId)
                    ->where('name', 'like', "%{$kw}%")
                    ->first();
                if ($match) {
                    return $match;
                }
            }
        }

        return null;
    }

    /**
     * Fallback standard market rates for hardware items
     */
    protected function estimateMarketPrice(string $name): float
    {
        $lower = strtolower($name);
        if (str_contains($lower, 'pipe')) return 120.0;
        if (str_contains($lower, 'elbow') || str_contains($lower, 'socket') || str_contains($lower, 'tee')) return 35.0;
        if (str_contains($lower, 'valve')) return 180.0;
        if (str_contains($lower, 'cement') || str_contains($lower, 'solvent') || str_contains($lower, 'araldite')) return 95.0;
        if (str_contains($lower, 'tape')) return 25.0;
        if (str_contains($lower, 'wire') || str_contains($lower, 'cable')) return 220.0;
        if (str_contains($lower, 'switch') || str_contains($lower, 'socket')) return 65.0;
        return 75.0;
    }

    /**
     * Generate CSV content for Excel export
     */
    public function generateQuotationCsv(array $parsedData, string $storeName): string
    {
        $output = "VyaparIndia Estimate / Contractor Quotation\n";
        $output .= "Store: " . $storeName . "\n";
        $output .= "Date: " . date('d-M-Y H:i') . "\n\n";
        $output .= "S.No,Item Description,Detected Size,Quantity,Store Matched Item,Unit Rate (INR),Line Total (INR),Status\n";

        foreach ($parsedData['items'] as $index => $item) {
            $status = $item['is_matched'] ? 'In Store Catalog' : 'Custom Estimate';
            $output .= sprintf(
                "%d,\"%s\",\"%s\",%d,\"%s\",%.2f,%.2f,\"%s\"\n",
                $index + 1,
                str_replace('"', '""', $item['item_name']),
                str_replace('"', '""', $item['detected_size'] ?? 'Standard'),
                $item['quantity'],
                str_replace('"', '""', $item['matched_product_name']),
                $item['unit_price'],
                $item['line_total'],
                $status
            );
        }

        $output .= sprintf("\n,,,,Total Estimated Bill:,,%.2f,\n", $parsedData['total_estimated_amount']);
        return $output;
    }
}
