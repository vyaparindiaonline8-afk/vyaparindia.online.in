<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\Product;
use App\Models\AmazonFeeCache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AmazonFeeService
{
    const SP_API_EU_ENDPOINT = 'https://sellingpartnerapi-eu.amazon.com';
    const MARKETPLACE_ID_INDIA = 'A21TJRUUN4KGV';

    protected AmazonSpApiService $spApiService;

    public function __construct(AmazonSpApiService $spApiService)
    {
        $this->spApiService = $spApiService;
    }

    /**
     * Get Fee Estimate for a SKU/ASIN at a target selling price.
     * 1. Checks local cache (valid for 6 hours).
     * 2. If miss & channel configured: calls live SP-API Fees Estimate with backoff.
     * 3. Falls back to Amazon India 2026 Category Matrix if offline/unlisted.
     */
    public function getFeesEstimate(
        ?Channel $channel,
        string $skuOrAsin,
        float $price,
        float $weightKg = 0.5,
        ?string $category = null,
        string $idType = 'SKU',
        ?Product $product = null
    ): array {
        $price = round(max(1.0, $price), 2);
        $weightKg = round(max(0.1, $weightKg), 3);

        // 1. Check Cache (within 6 hours & ±₹1 price window)
        $cached = AmazonFeeCache::validFor($skuOrAsin, $price, 6, 'amazon')->first();
        if ($cached) {
            return [
                'success' => true,
                'source' => 'cache',
                'is_actual' => (bool)$cached->is_actual,
                'status' => $cached->is_actual ? 'asli_verified' : 'estimate',
                'sku' => $cached->sku ?: $skuOrAsin,
                'asin' => $cached->asin ?: $skuOrAsin,
                'listing_price' => (float)$cached->listing_price,
                'referral_fee' => (float)$cached->referral_fee,
                'closing_fee' => (float)$cached->closing_fee,
                'shipping_fee' => (float)$cached->shipping_fee,
                'tax_fee' => (float)$cached->tax_fee,
                'total_fee' => (float)$cached->total_fee,
                'effective_fee_pct' => (float)$cached->effective_fee_pct,
                'breakdown' => $cached->fee_breakdown ?? [],
                'fetched_at' => $cached->fetched_at ? $cached->fetched_at->toIso8601String() : null,
            ];
        }

        // 2. Attempt Amazon SP-API if channel credentials are active
        $spApiResult = null;
        if ($channel && !empty($channel->api_key) && !empty($channel->access_token)) {
            $spApiResult = $this->callSpApiFeesEstimate($channel, $skuOrAsin, $price, $idType);
        }

        if ($spApiResult && $spApiResult['success']) {
            $res = $spApiResult['data'];
            $res['is_actual'] = true;
            $res['source'] = 'sp_api';
            $res['status'] = 'asli_verified';

            $this->saveFeeToCache($channel, $product, $skuOrAsin, $price, $weightKg, $res, true, 'sp_api');
            return $res;
        }

        // 3. Fallback: Amazon India 2026 Category Schedule & Slab Solver
        $matrixResult = $this->calculateDeterministicAmazonMatrix($price, $weightKg, $category);
        $matrixResult['sku'] = $skuOrAsin;
        $matrixResult['asin'] = ($idType === 'ASIN') ? $skuOrAsin : null;
        $matrixResult['is_actual'] = false;
        $matrixResult['source'] = 'matrix_estimate';
        $matrixResult['status'] = 'estimate';

        $this->saveFeeToCache($channel, $product, $skuOrAsin, $price, $weightKg, $matrixResult, false, 'matrix_estimate');
        return $matrixResult;
    }

    /**
     * Call Amazon SP-API /products/fees/v0/items/{Asin}/feesEstimate or SKU endpoint
     */
    protected function callSpApiFeesEstimate(Channel $channel, string $skuOrAsin, float $price, string $idType = 'SKU'): ?array
    {
        $auth = $this->spApiService->getLwaAccessToken($channel);
        if (!$auth['success']) {
            Log::info("SP-API Fees Auth Skipped/Failed: " . ($auth['message'] ?? 'Token error'));
            return null;
        }

        $accessToken = $auth['access_token'];
        $cleanId = trim($skuOrAsin);

        // Endpoint selection based on ID type
        if ($idType === 'ASIN' || preg_match('/^B0[A-Z0-9]{8}$/i', $cleanId)) {
            $url = self::SP_API_EU_ENDPOINT . "/products/fees/v0/items/{$cleanId}/feesEstimate";
        } else {
            $encodedSku = rawurlencode($cleanId);
            $url = self::SP_API_EU_ENDPOINT . "/products/fees/v0/listings/{$encodedSku}/feesEstimate";
        }

        $payload = [
            'FeesEstimateRequest' => [
                'MarketplaceId' => self::MARKETPLACE_ID_INDIA,
                'PriceToEstimateFees' => [
                    'ListingPrice' => [
                        'CurrencyCode' => 'INR',
                        'Amount' => round($price, 2)
                    ],
                    'Shipping' => [
                        'CurrencyCode' => 'INR',
                        'Amount' => 0.00
                    ]
                ],
                'Identifier' => 'EST-' . uniqid()
            ]
        ];

        // Rate-limit safety: 1 request/sec token with retry
        for ($attempt = 1; $attempt <= 2; $attempt++) {
            try {
                $response = Http::withToken($accessToken)
                    ->withHeaders([
                        'x-amz-access-token' => $accessToken,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ])
                    ->timeout(12)
                    ->post($url, $payload);

                if ($response->successful()) {
                    $json = $response->json();
                    $feesResult = $json['payload']['FeesEstimateResult']['FeesEstimate'] ?? null;
                    if ($feesResult) {
                        return [
                            'success' => true,
                            'data' => $this->parseSpApiFeeResponse($feesResult, $price)
                        ];
                    }
                }

                if ($response->status() === 429) {
                    // Rate limit hit - backoff 1.5 seconds
                    usleep(1500000);
                    continue;
                }

                Log::warning("SP-API Fees returned HTTP {$response->status()} for {$cleanId}: " . substr($response->body(), 0, 180));
                break;
            } catch (\Throwable $e) {
                Log::warning("SP-API Fees Exception for {$cleanId}: " . $e->getMessage());
                break;
            }
        }

        return null;
    }

    /**
     * Parse Amazon SP-API FeeDetailList response structure
     */
    protected function parseSpApiFeeResponse(array $feesResult, float $price): array
    {
        $feeDetails = $feesResult['FeeDetailList'] ?? [];
        $referralFee = 0.0;
        $closingFee = 0.0;
        $shippingFee = 0.0;
        $taxFee = 0.0;

        foreach ($feeDetails as $fee) {
            $feeType = $fee['FeeType'] ?? '';
            $amount = (float)($fee['FeeAmount']['Amount'] ?? 0);
            $taxAmount = (float)($fee['TaxAmount']['Amount'] ?? 0);

            $taxFee += $taxAmount;

            if (stripos($feeType, 'Referral') !== false) {
                $referralFee += $amount;
            } elseif (stripos($feeType, 'Closing') !== false) {
                $closingFee += $amount;
            } elseif (stripos($feeType, 'Weight') !== false || stripos($feeType, 'Delivery') !== false || stripos($feeType, 'Shipping') !== false) {
                $shippingFee += $amount;
            }
        }

        $totalFee = (float)($feesResult['TotalFeesEstimate']['Amount'] ?? ($referralFee + $closingFee + $shippingFee + $taxFee));
        $effectiveFeePct = ($price > 0) ? round((($referralFee + $closingFee) / $price) * 100, 2) : 12.0;

        return [
            'success' => true,
            'listing_price' => $price,
            'referral_fee' => round($referralFee, 2),
            'closing_fee' => round($closingFee, 2),
            'shipping_fee' => round($shippingFee, 2),
            'tax_fee' => round($taxFee, 2),
            'total_fee' => round($totalFee, 2),
            'effective_fee_pct' => $effectiveFeePct,
            'breakdown' => [
                'referral_fee' => round($referralFee, 2),
                'closing_fee' => round($closingFee, 2),
                'shipping_fee' => round($shippingFee, 2),
                'tax_fee' => round($taxFee, 2),
                'fee_details' => $feeDetails,
            ],
            'fetched_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Deterministic Amazon India 2026 Category Schedule & Slab Solver
     * Covers Plumbing, Hardware, Faucets, Home Improvement, and Standard Products.
     */
    public function calculateDeterministicAmazonMatrix(float $price, float $weightKg = 0.5, ?string $category = null): array
    {
        $catNorm = strtolower($category ?? '');

        // 1. Referral Fee Percentage by Category & Price Slab
        // Amazon India Standard Schedule for Hardware / Plumbing / Home Improvement:
        if (str_contains($catNorm, 'plumb') || str_contains($catNorm, 'pipe') || str_contains($catNorm, 'fitting')) {
            $referralPct = ($price <= 500) ? 6.5 : 8.0;
        } elseif (str_contains($catNorm, 'faucet') || str_contains($catNorm, 'tap') || str_contains($catNorm, 'cock') || str_contains($catNorm, 'bath')) {
            if ($price <= 300) $referralPct = 7.5;
            elseif ($price <= 500) $referralPct = 9.0;
            elseif ($price <= 1000) $referralPct = 10.5;
            else $referralPct = 12.0;
        } elseif (str_contains($catNorm, 'tool') || str_contains($catNorm, 'hardw')) {
            $referralPct = ($price <= 500) ? 9.5 : 11.5;
        } else {
            // Default Baseline: 12%
            $referralPct = 12.0;
        }

        $referralFee = round($price * ($referralPct / 100), 2);

        // 2. Fixed Closing Fee (Amazon India Easy Ship Slabs)
        if ($price <= 250) {
            $closingFee = 5.00;
        } elseif ($price <= 500) {
            $closingFee = 10.00;
        } elseif ($price <= 1000) {
            $closingFee = 25.00;
        } else {
            $closingFee = 50.00;
        }

        // 3. Easy Ship Courier Fee (India standard national base 0.5kg slab = ₹55)
        $baseWeightSlab = 0.5;
        $shippingFee = 55.00;
        if ($weightKg > $baseWeightSlab) {
            $additionalSlabs = ceil(($weightKg - $baseWeightSlab) / 0.5);
            $shippingFee += ($additionalSlabs * 25.00);
        }

        // 4. GST on Amazon Services (18%)
        $taxFee = round(($referralFee + $closingFee + $shippingFee) * 0.18, 2);
        $totalFee = round($referralFee + $closingFee + $shippingFee + $taxFee, 2);
        $effectiveFeePct = ($price > 0) ? round((($referralFee + $closingFee) / $price) * 100, 2) : $referralPct;

        return [
            'success' => true,
            'listing_price' => $price,
            'weight_kg' => $weightKg,
            'referral_fee' => $referralFee,
            'referral_pct' => $referralPct,
            'closing_fee' => $closingFee,
            'shipping_fee' => $shippingFee,
            'tax_fee' => $taxFee,
            'total_fee' => $totalFee,
            'effective_fee_pct' => $effectiveFeePct,
            'breakdown' => [
                'referral_fee' => $referralFee,
                'referral_pct' => $referralPct,
                'closing_fee' => $closingFee,
                'shipping_fee' => $shippingFee,
                'tax_fee' => $taxFee,
                'rule' => 'Amazon India 2026 Category Schedule (Easy-Ship Base)',
            ],
            'fetched_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Fixed-point iterative Price Solver:
     * P_new = (cost/1.18 + rto + packing + courier + ads + profit + feeExGst) / (1 - 18/118)
     * Loops max 4 times until |P_new - P| < 1.
     * Enforces Legal Metrology Box MRP constraints.
     */
    public function solveOptimalSellingPrice(array $params): array
    {
        $cost = (float)($params['cost'] ?? 0);
        $mrp = (float)($params['mrp'] ?? 0);
        $gstRate = (float)($params['gst_rate'] ?? 18);
        $marginPct = (float)($params['margin_pct'] ?? 25);
        $rto = (float)($params['rto'] ?? 20);
        $packaging = (float)($params['packaging'] ?? 15);
        $courier = (float)($params['courier'] ?? 55);
        $ads = (float)($params['ads'] ?? 10);
        $others = (float)($params['others'] ?? 0);
        $weightKg = (float)($params['weight_kg'] ?? 0.5);
        $category = $params['category'] ?? null;
        $skuOrAsin = $params['sku'] ?? $params['asin'] ?? ('SKU-' . ($params['product_id'] ?? rand(100, 999)));
        $channel = $params['channel'] ?? null;

        $g = $gstRate / 100;
        $taxFactor = (1 + $g);
        $cTax = ($g > 0) ? ($cost / $taxFactor) : $cost;
        $desiredProfit = round($cost * ($marginPct / 100), 2);

        // Fixed non-tax expenses
        // Courier service charges ₹55 base + 18% GST = ₹64.90 (GST claimed back via ITC in tax net)
        $courierWithGst = $courier * 1.18;
        $fixedExp = $rto + $packaging + $courierWithGst + $ads + $others;

        // Step 0: Initial Guess P0 with assumed 12% fee
        $denom0 = max(0.05, (1 / $taxFactor) - 0.12);
        $currentPrice = ceil(($cTax + $fixedExp + $desiredProfit) / $denom0);
        $p0 = $currentPrice;

        $iterations = [];
        $latestFeeData = null;
        $converged = false;

        // Step 1: Iterative Loop (Max 4 times)
        for ($iter = 1; $iter <= 4; $iter++) {
            $feeResult = $this->getFeesEstimate($channel, $skuOrAsin, $currentPrice, $weightKg, $category);
            $latestFeeData = $feeResult;

            // Fee without GST (since fee GST is fully claimed back as ITC input tax credit)
            $feeExGst = ($feeResult['referral_fee'] ?? 0) + ($feeResult['closing_fee'] ?? 0);

            // User's exact mathematical formula:
            // P_new = (cost/1.18 + rto + packing + courier + ads + profit + feeExGst) / (1 - 18/118)
            // Note: 1 - 18/118 = 100/118 = 1 / 1.18
            // Therefore: P_new = (cTax + fixedExp + desiredProfit + feeExGst) * 1.18
            $pNew = ceil(($cTax + $fixedExp + $desiredProfit + $feeExGst) * $taxFactor);

            $diff = abs($pNew - $currentPrice);
            $iterations[] = [
                'iteration' => $iter,
                'p_in' => $currentPrice,
                'fee_ex_gst' => $feeExGst,
                'p_out' => $pNew,
                'diff' => $diff
            ];

            if ($diff < 1.0) {
                $currentPrice = $pNew;
                $converged = true;
                break;
            }

            $currentPrice = $pNew;
        }

        $finalPrice = $currentPrice;
        $diffFromP0 = $finalPrice - $p0;

        // Effective Fee Percentage
        $referralFee = (float)($latestFeeData['referral_fee'] ?? 0);
        $closingFee = (float)($latestFeeData['closing_fee'] ?? 0);
        $shippingFee = (float)($latestFeeData['shipping_fee'] ?? $courier);
        $taxFee = (float)($latestFeeData['tax_fee'] ?? 0);
        $totalFee = (float)($latestFeeData['total_fee'] ?? 0);
        $effectiveFeePct = ($finalPrice > 0) ? round((($referralFee + $closingFee) / $finalPrice) * 100, 2) : 12.0;

        // Check Legal Metrology Box MRP Limit
        $status = 'optimal';
        $mrpViolation = false;
        $gapFromMrp = 0;
        $mrpWarning = null;

        if ($mrp > 0 && $finalPrice > $mrp) {
            $mrpViolation = true;
            $gapFromMrp = round($finalPrice - $mrp, 2);
            $status = 'mrp_violation';
            $mrpWarning = "🚨 अंतिम सेलिंग प्राइस (₹{$finalPrice}) MRP (₹{$mrp}) से ₹{$gapFromMrp} अधिक है! घाटा या लीगल मेट्रोलॉजी पेनल्टी से बचने के लिए 1P स्किप करें या 2P/4P कॉम्बो पैक बेचें।";
        } elseif ($mrp > 0) {
            $gapFromMrp = round($mrp - $finalPrice, 2);
        }

        return [
            'success' => true,
            'product_id' => $params['product_id'] ?? null,
            'sku' => $skuOrAsin,
            'status' => $status,
            'is_actual' => $latestFeeData['is_actual'] ?? false,
            'fee_status' => ($latestFeeData['is_actual'] ?? false) ? 'asli_verified' : 'estimate',
            'fee_source' => $latestFeeData['source'] ?? 'matrix',
            'p0_assumed' => $p0,
            'solved_price' => $finalPrice,
            'price_diff' => $diffFromP0,
            'mrp' => $mrp,
            'mrp_violation' => $mrpViolation,
            'gap_from_mrp' => $gapFromMrp,
            'mrp_warning' => $mrpWarning,
            'effective_fee_pct' => $effectiveFeePct,
            'converged' => $converged,
            'iterations' => $iterations,
            'fees_breakdown' => [
                'referral_fee' => $referralFee,
                'closing_fee' => $closingFee,
                'shipping_fee' => $shippingFee,
                'tax_fee' => $taxFee,
                'total_fee' => $totalFee,
            ],
            'costing_breakdown' => [
                'cost' => $cost,
                'cost_ex_gst' => round($cTax, 2),
                'desired_profit' => $desiredProfit,
                'rto' => $rto,
                'packaging' => $packaging,
                'courier_with_gst' => round($courierWithGst, 2),
                'ads' => $ads,
                'others' => $others,
                'platform_fee_ex_gst' => round($referralFee + $closingFee, 2),
            ]
        ];
    }

    /**
     * 🛒 MEESHO PRICE ENGINE (0% Commission Model)
     * Meesho charges 0% commission and ₹0 closing fee.
     * Expenses: Base Courier + 18% GST + High RTO Buffer + Packing.
     */
    public function solveMeeshoPrice(array $params): array
    {
        $cost = (float)($params['cost'] ?? 0);
        $mrp = (float)($params['mrp'] ?? 0);
        $gstRate = (float)($params['gst_rate'] ?? 18);
        $marginPct = (float)($params['margin_pct'] ?? 25);
        $rto = (float)($params['rto'] ?? 20); // Meesho RTO safety
        $packaging = (float)($params['packaging'] ?? 15);
        $courier = (float)($params['courier'] ?? 55);
        $others = (float)($params['others'] ?? 0);

        $g = $gstRate / 100;
        $cTax = ($g > 0) ? ($cost / (1 + $g)) : $cost;
        $desiredProfit = round($cost * ($marginPct / 100), 2);
        $courierWithGst = $courier * 1.18;
        $fixedExp = $rto + $packaging + $courierWithGst + $others;

        // Meesho formula: (cTax + fixedExp + profit) * (1 + g)
        $solvedPrice = ceil(($cTax + $fixedExp + $desiredProfit) * (1 + $g));

        return [
            'marketplace' => 'meesho',
            'commission_pct' => 0.0,
            'commission_amt' => 0.0,
            'closing_fee' => 0.0,
            'courier_with_gst' => round($courierWithGst, 2),
            'rto_buffer' => $rto,
            'solved_price' => $solvedPrice,
            'mrp_violation' => ($mrp > 0 && $solvedPrice > $mrp),
            'mrp' => $mrp,
        ];
    }

    /**
     * ⚡ FLIPKART PRICE ENGINE (Commission + Fixed Fee + Collection Fee + Shipping)
     */
    public function solveFlipkartPrice(array $params): array
    {
        $cost = (float)($params['cost'] ?? 0);
        $mrp = (float)($params['mrp'] ?? 0);
        $gstRate = (float)($params['gst_rate'] ?? 18);
        $marginPct = (float)($params['margin_pct'] ?? 25);
        $rto = (float)($params['rto'] ?? 20);
        $packaging = (float)($params['packaging'] ?? 15);
        $courier = (float)($params['courier'] ?? 55);
        $ads = (float)($params['ads'] ?? 10);
        $others = (float)($params['others'] ?? 0);
        $category = $params['category'] ?? '';

        $g = $gstRate / 100;
        $cTax = ($g > 0) ? ($cost / (1 + $g)) : $cost;
        $desiredProfit = round($cost * ($marginPct / 100), 2);
        $courierWithGst = $courier * 1.18;
        $fixedExp = $rto + $packaging + $courierWithGst + $ads + $others;

        // Flipkart commission: typically 9.5% for Hardware/Plumbing
        $flipkartCommPct = 9.5;
        $collectionFeePct = 2.0; // 2% prepaid/COD collection

        // Initial estimate
        $currentPrice = ceil(($cTax + $fixedExp + $desiredProfit) / max(0.05, (1 / (1 + $g)) - (($flipkartCommPct + $collectionFeePct) / 100)));

        for ($i = 0; $i < 3; $i++) {
            // Flipkart Fixed fee slab
            if ($currentPrice <= 300) $fixedFee = 13.0;
            elseif ($currentPrice <= 500) $fixedFee = 15.0;
            elseif ($currentPrice <= 1000) $fixedFee = 30.0;
            else $fixedFee = 50.0;

            $commFee = $currentPrice * ($flipkartCommPct / 100);
            $collectionFee = $currentPrice * ($collectionFeePct / 100);
            $totalFeeExGst = $commFee + $collectionFee + $fixedFee;

            $newPrice = ceil(($cTax + $fixedExp + $desiredProfit + $totalFeeExGst) * (1 + $g));
            if (abs($newPrice - $currentPrice) < 1.0) {
                $currentPrice = $newPrice;
                break;
            }
            $currentPrice = $newPrice;
        }

        return [
            'marketplace' => 'flipkart',
            'commission_pct' => $flipkartCommPct,
            'collection_fee_pct' => $collectionFeePct,
            'fixed_fee' => $fixedFee ?? 15.0,
            'courier_with_gst' => round($courierWithGst, 2),
            'solved_price' => $currentPrice,
            'mrp_violation' => ($mrp > 0 && $currentPrice > $mrp),
            'mrp' => $mrp,
        ];
    }

    /**
     * 👜 MYNTRA PRICE ENGINE
     */
    public function solveMyntraPrice(array $params): array
    {
        $cost = (float)($params['cost'] ?? 0);
        $mrp = (float)($params['mrp'] ?? 0);
        $gstRate = (float)($params['gst_rate'] ?? 18);
        $marginPct = (float)($params['margin_pct'] ?? 25);
        $rto = (float)($params['rto'] ?? 25);
        $packaging = (float)($params['packaging'] ?? 20);
        $courier = (float)($params['courier'] ?? 65);
        $others = (float)($params['others'] ?? 0);

        $g = $gstRate / 100;
        $cTax = ($g > 0) ? ($cost / (1 + $g)) : $cost;
        $desiredProfit = round($cost * ($marginPct / 100), 2);
        $courierWithGst = $courier * 1.18;
        $fixedExp = $rto + $packaging + $courierWithGst + $others;

        $myntraCommPct = 20.0; // Standard Myntra take-rate
        $solvedPrice = ceil(($cTax + $fixedExp + $desiredProfit) / max(0.05, (1 / (1 + $g)) - ($myntraCommPct / 100)));

        return [
            'marketplace' => 'myntra',
            'commission_pct' => $myntraCommPct,
            'courier_with_gst' => round($courierWithGst, 2),
            'solved_price' => $solvedPrice,
            'mrp_violation' => ($mrp > 0 && $solvedPrice > $mrp),
            'mrp' => $mrp,
        ];
    }

    /**
     * ⚡ BLINKIT QUICK-COMMERCE ENGINE
     */
    public function solveBlinkitPrice(array $params): array
    {
        $cost = (float)($params['cost'] ?? 0);
        $mrp = (float)($params['mrp'] ?? 0);
        $gstRate = (float)($params['gst_rate'] ?? 18);
        $marginPct = (float)($params['margin_pct'] ?? 20);
        $packaging = (float)($params['packaging'] ?? 8);
        $darkStoreHandling = 12.0; // Dark store inward & picking charge
        $damageRisk = 5.0;

        $g = $gstRate / 100;
        $cTax = ($g > 0) ? ($cost / (1 + $g)) : $cost;
        $desiredProfit = round($cost * ($marginPct / 100), 2);
        $fixedExp = $packaging + $darkStoreHandling + $damageRisk;

        $blinkitMarginPct = 16.0; // Blinkit platform margin take-rate
        $solvedPrice = ceil(($cTax + $fixedExp + $desiredProfit) / max(0.05, (1 / (1 + $g)) - ($blinkitMarginPct / 100)));

        return [
            'marketplace' => 'blinkit',
            'margin_take_pct' => $blinkitMarginPct,
            'dark_store_handling' => $darkStoreHandling,
            'solved_price' => $solvedPrice,
            'mrp_violation' => ($mrp > 0 && $solvedPrice > $mrp),
            'mrp' => $mrp,
        ];
    }

    /**
     * Helper to persist fee cache in DB
     */
    protected function saveFeeToCache(?Channel $channel, ?Product $product, string $skuOrAsin, float $price, float $weightKg, array $data, bool $isActual, string $source): void
    {
        try {
            AmazonFeeCache::updateOrCreate(
                [
                    'sku' => $skuOrAsin,
                    'listing_price' => $price,
                    'marketplace' => 'amazon',
                ],
                [
                    'channel_id' => $channel ? $channel->id : null,
                    'product_id' => $product ? $product->id : null,
                    'asin' => $data['asin'] ?? null,
                    'weight_kg' => $weightKg,
                    'referral_fee' => $data['referral_fee'] ?? 0,
                    'closing_fee' => $data['closing_fee'] ?? 0,
                    'shipping_fee' => $data['shipping_fee'] ?? 0,
                    'tax_fee' => $data['tax_fee'] ?? 0,
                    'total_fee' => $data['total_fee'] ?? 0,
                    'effective_fee_pct' => $data['effective_fee_pct'] ?? 0,
                    'is_actual' => $isActual,
                    'source' => $source,
                    'fee_breakdown' => $data['breakdown'] ?? [],
                    'fetched_at' => now(),
                ]
            );
        } catch (\Throwable $e) {
            Log::warning("Failed to save AmazonFeeCache: " . $e->getMessage());
        }
    }
}
