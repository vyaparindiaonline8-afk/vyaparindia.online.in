<?php

namespace App\Services;

use App\Models\Order;
use App\Models\DropshipOrder;
use App\Models\SellerProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TaxComplianceService
{
    /**
     * Get Seller Tax Profile details.
     */
    public function getSellerTaxProfile(int $userId): array
    {
        $profile = SellerProfile::where('user_id', $userId)->first();

        $scheme = $profile->tax_scheme ?? 'regular';
        $schemeLabels = [
            'regular' => 'Regular GST Dealer (Full ITC Rebate & All India)',
            'composition' => 'Composition Scheme (1% Flat Tax - No ITC - State Only)',
            'unregistered' => 'Non-GST Unregistered Seller (Meesho Enrolment ID / Local)'
        ];

        return [
            'company_name' => $profile->company_name ?? (Auth::user()->name ?? 'My Business'),
            'gst_number' => $profile->gst_number ?? '',
            'gst_enrolment_id' => $profile->gst_enrolment_id ?? '',
            'state' => $profile->state ?? 'Delhi',
            'tax_scheme' => $scheme,
            'tax_scheme_label' => $schemeLabels[$scheme] ?? 'Regular GST Dealer',
        ];
    }

    /**
     * Compile complete Tax Compliance Data for date range and channel.
     */
    public function compileTaxReport(int $userId, ?string $fromDate = null, ?string $toDate = null, string $channel = 'all'): array
    {
        $taxProfile = $this->getSellerTaxProfile($userId);
        $sellerState = strtolower(trim($taxProfile['state'] ?: 'delhi'));
        $scheme = $taxProfile['tax_scheme'];

        // Normalize dates
        $start = $fromDate ? Carbon::parse($fromDate)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $toDate ? Carbon::parse($toDate)->endOfDay() : Carbon::now()->endOfDay();
        $periodLabel = $start->format('d M Y') . ' to ' . $end->format('d M Y');

        // 1. Fetch Orders
        $orderQuery = Order::where(function ($q) use ($userId) {
            $q->where('seller_id', $userId)->orWhere('user_id', $userId);
        })
        ->whereBetween('created_at', [$start, $end])
        ->with(['products']);

        if ($channel !== 'all' && $channel !== 'dropship') {
            $orderQuery->where('order_source', $channel);
        } elseif ($channel === 'dropship') {
            $orderQuery->where('order_source', 'dropship');
        }

        $orders = $orderQuery->orderBy('created_at', 'desc')->get();

        // 2. Fetch Dropship Orders if needed
        $dsOrders = [];
        if ($channel === 'all' || $channel === 'dropship') {
            $dsOrders = DropshipOrder::where(function ($q) use ($userId) {
                $q->where('dropshipper_id', $userId)->orWhere('wholesaler_id', $userId);
            })
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();
        }

        $salesList = [];
        $expensesList = [];

        $grossSales = 0;
        $taxableSales = 0;
        $totalCgst = 0;
        $totalSgst = 0;
        $totalIgst = 0;
        $totalOutputTax = 0;

        $totalPlatformExpenses = 0;
        $totalItcClaimable = 0;
        $totalTcsDeducted = 0;

        foreach ($orders as $order) {
            $amount = (float)$order->total_price;
            if ($amount <= 0) continue;

            $grossSales += $amount;
            $customerState = strtolower(trim($order->state ?: $sellerState));
            $isIntraState = ($customerState === $sellerState);

            $orderChannel = ucfirst($order->order_source ?: 'VyaparIndia Direct');
            $orderDate = $order->created_at->format('Y-m-d');

            // Product name string
            $productNames = $order->products->pluck('name')->implode(', ') ?: 'Standard Catalog Item';
            $qty = $order->products->sum(function ($p) {
                return $p->pivot->quantity ?? 1;
            }) ?: 1;

            if ($scheme === 'unregistered') {
                $taxable = $amount;
                $cgst = 0;
                $sgst = 0;
                $igst = 0;
                $gstRate = 0;
            } elseif ($scheme === 'composition') {
                $taxable = $amount;
                $compTax = round($amount * 0.01, 2);
                $cgst = round($compTax / 2, 2);
                $sgst = round($compTax / 2, 2);
                $igst = 0;
                $gstRate = 1;
                $totalCgst += $cgst;
                $totalSgst += $sgst;
                $totalOutputTax += $compTax;
            } else {
                // Regular 18% GST (Back-calculated from gross total)
                $gstRate = 18;
                $taxable = round($amount / 1.18, 2);
                $taxAmt = round($amount - $taxable, 2);

                if ($isIntraState) {
                    $cgst = round($taxAmt / 2, 2);
                    $sgst = round($taxAmt / 2, 2);
                    $igst = 0;
                    $totalCgst += $cgst;
                    $totalSgst += $sgst;
                } else {
                    $cgst = 0;
                    $sgst = 0;
                    $igst = $taxAmt;
                    $totalIgst += $igst;
                }
                $totalOutputTax += $taxAmt;
            }

            $taxableSales += $taxable;

            $salesList[] = [
                'order_id' => $order->id,
                'order_number' => $order->order_number ?: ('ORD-' . $order->id),
                'date' => $orderDate,
                'channel' => $orderChannel,
                'customer_name' => $order->customer_name ?: 'Retail Buyer',
                'pos_state' => ucwords($order->state ?: $sellerState),
                'supply_type' => $isIntraState ? 'Intra-State' : 'Inter-State',
                'hsn' => '39229000',
                'product_name' => $productNames,
                'quantity' => $qty,
                'taxable_value' => $taxable,
                'gst_rate' => $gstRate,
                'cgst' => $cgst,
                'sgst' => $sgst,
                'igst' => $igst,
                'total_amount' => $amount
            ];

            // 💳 Calculate Platform Fees & Claimable ITC
            $isMarketplace = in_array(strtolower($order->order_source ?? ''), ['amazon', 'meesho', 'flipkart', 'blinkit']);
            if ($isMarketplace) {
                $refFee = round($amount * 0.12, 2); // 12% average referral
                $shippingFee = 55.0; // ₹55 standard courier
                $taxableFee = $refFee + $shippingFee;
                $itc = ($scheme === 'regular') ? round($taxableFee * 0.18, 2) : 0;
                $tcs = round($amount * 0.01, 2); // 1% TCS
                $totalFee = $taxableFee + ($taxableFee * 0.18);

                $totalPlatformExpenses += $taxableFee;
                $totalItcClaimable += $itc;
                $totalTcsDeducted += $tcs;

                $expensesList[] = [
                    'date' => $orderDate,
                    'provider' => ucfirst($order->order_source) . ' Platform Services',
                    'type' => 'Referral Fee & Shipping',
                    'ref' => $order->order_number ?: ('ORD-' . $order->id),
                    'taxable_fee' => $taxableFee,
                    'itc_amount' => $itc,
                    'tcs_amount' => $tcs,
                    'total_fee' => $totalFee,
                    'note' => ($scheme === 'regular') ? 'Eligible for 100% ITC in GSTR-3B Table 4A' : 'Treated as Direct Business Expense (No ITC in Composition/Non-GST)'
                ];
            } else {
                // Direct or Store order: standard courier fee ITC
                $courierFee = 55.0;
                $itc = ($scheme === 'regular') ? round($courierFee * 0.18, 2) : 0;
                $totalPlatformExpenses += $courierFee;
                $totalItcClaimable += $itc;

                $expensesList[] = [
                    'date' => $orderDate,
                    'provider' => 'Courier Logistics Partner',
                    'type' => 'Logistics & Delivery',
                    'ref' => $order->order_number ?: ('ORD-' . $order->id),
                    'taxable_fee' => $courierFee,
                    'itc_amount' => $itc,
                    'tcs_amount' => 0,
                    'total_fee' => round($courierFee * 1.18, 2),
                    'note' => ($scheme === 'regular') ? 'Logistics ITC' : 'Courier Expense'
                ];
            }
        }

        // Add Dropship Orders if channel is all or dropship
        foreach ($dsOrders as $ds) {
            $dsRetail = (float)($ds->customer_retail_total ?: 0);
            $dsSupplier = (float)($ds->supplier_base_cost ?: 0);
            $dsShipping = (float)($ds->shipping_cost ?: 50);
            $profit = (float)($ds->dropshipper_profit ?: max(0, $dsRetail - $dsSupplier - $dsShipping));

            if ($dsRetail <= 0) continue;

            $grossSales += $dsRetail;
            $taxable = round($dsRetail / 1.18, 2);
            $taxAmt = round($dsRetail - $taxable, 2);

            $salesList[] = [
                'order_id' => $ds->id,
                'order_number' => $ds->ds_order_number ?: ('DS-' . $ds->id),
                'date' => $ds->created_at->format('Y-m-d'),
                'channel' => 'Dropshipping',
                'customer_name' => 'Dropship Buyer',
                'pos_state' => ucwords($taxProfile['state']),
                'supply_type' => 'Intra-State',
                'hsn' => '39229000',
                'product_name' => 'Dropshipped Catalog Item',
                'quantity' => 1,
                'taxable_value' => $taxable,
                'gst_rate' => ($scheme === 'regular') ? 18 : 0,
                'cgst' => ($scheme === 'regular') ? round($taxAmt / 2, 2) : 0,
                'sgst' => ($scheme === 'regular') ? round($taxAmt / 2, 2) : 0,
                'igst' => 0,
                'total_amount' => $dsRetail
            ];

            // Supplier cost has ITC if supplier gives B2B Tax invoice
            $supplierItc = ($scheme === 'regular') ? round(($dsSupplier / 1.18) * 0.18, 2) : 0;
            $totalPlatformExpenses += $dsSupplier;
            $totalItcClaimable += $supplierItc;

            $expensesList[] = [
                'date' => $ds->created_at->format('Y-m-d'),
                'provider' => 'Verified Wholesaler Supplier',
                'type' => 'Wholesale Dropship Purchase & Packing',
                'ref' => $ds->ds_order_number ?: ('DS-' . $ds->id),
                'taxable_fee' => round($dsSupplier / 1.18, 2),
                'itc_amount' => $supplierItc,
                'tcs_amount' => 0,
                'total_fee' => $dsSupplier,
                'note' => 'B2B Purchase Invoice ITC'
            ];
        }

        $netTaxPayable = max(0, $totalOutputTax - $totalItcClaimable);

        $caRecommendation = ($scheme === 'regular')
            ? "File GSTR-1 using Sheet 1 (Sales). In GSTR-3B Table 4A, claim ₹" . number_format($totalItcClaimable, 2) . " eligible ITC to pay only ₹" . number_format($netTaxPayable, 2) . " cash. Also claim ₹" . number_format($totalTcsDeducted, 2) . " TCS in Cash Ledger."
            : (($scheme === 'composition')
                ? "File CMP-08 quarterly turnover statement. Pay 1% flat tax of ₹" . number_format($totalOutputTax, 2) . ". Platform fees are non-creditable business expenses."
                : "Non-GST / Unregistered Seller (Enrolment ID basis). Zero GST liability to file. Maintain Sheet 1 for income tax records.");

        return [
            'seller_info' => $taxProfile,
            'period_label' => $periodLabel,
            'from_date' => $start->format('Y-m-d'),
            'to_date' => $end->format('Y-m-d'),
            'channel' => $channel,
            'summary' => [
                'total_orders' => count($salesList),
                'gross_sales' => round($grossSales, 2),
                'taxable_sales' => round($taxableSales, 2),
                'total_cgst' => round($totalCgst, 2),
                'total_sgst' => round($totalSgst, 2),
                'total_igst' => round($totalIgst, 2),
                'total_output_tax' => round($totalOutputTax, 2),
                'total_platform_expenses' => round($totalPlatformExpenses, 2),
                'total_itc_claimable' => round($totalItcClaimable, 2),
                'total_tcs_deducted' => round($totalTcsDeducted, 2),
                'net_tax_payable' => round($netTaxPayable, 2),
                'ca_recommendation' => $caRecommendation
            ],
            'sales' => $salesList,
            'expenses' => $expensesList,
        ];
    }

    /**
     * Generate 3-Tab Excel Workbook via Python openpyxl.
     */
    public function generateCaPackageExcel(int $userId, ?string $fromDate = null, ?string $toDate = null, string $channel = 'all'): string
    {
        $reportData = $this->compileTaxReport($userId, $fromDate, $toDate, $channel);

        $tempPayload = storage_path('app/ca_req_' . time() . '_' . rand(1000, 9999) . '.json');
        file_put_contents($tempPayload, json_encode($reportData));

        $fileName = 'CA_GST_Package_' . ($reportData['from_date'] ?? 'start') . '_to_' . ($reportData['to_date'] ?? 'end') . '_' . time() . '.xlsx';
        $outputPath = storage_path('app/public/reports/' . $fileName);

        if (!file_exists(dirname($outputPath))) {
            @mkdir(dirname($outputPath), 0777, true);
        }

        $cmd = 'python ' . escapeshellarg(app_path('Services/CaPackageGenerator.py')) . ' ' . escapeshellarg($tempPayload) . ' ' . escapeshellarg($outputPath);
        shell_exec($cmd);
        @unlink($tempPayload);

        return $outputPath;
    }
}
