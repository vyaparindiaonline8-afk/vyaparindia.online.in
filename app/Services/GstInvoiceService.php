<?php

namespace App\Services;

use App\Models\GstInvoice;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Str;

class GstInvoiceService
{
    /**
     * Generate GST-compliant Tax Invoice for B2B or B2C order.
     */
    public function generateInvoice(Order $order, ?User $seller = null, ?string $buyerGstin = null): GstInvoice
    {
        $seller = $seller ?: $order->seller ?: User::find($order->user_id);
        
        $sellerGstin = '09AAECV9812A1Z5'; // Default or seller profile GSTIN
        $sellerLegalName = $seller ? $seller->name : 'Vyapar Retailer Pvt Ltd';
        $sellerStateCode = '09'; // Uttar Pradesh
        $sellerStateName = 'Uttar Pradesh';

        $buyerLegalName = $order->customer_name ?: 'Valued Customer';
        $buyerStateName = $order->state ?: 'Uttar Pradesh';
        $buyerStateCode = $this->getStateCode($buyerStateName);

        $isInterstate = ($sellerStateCode !== $buyerStateCode);

        // Reverse-calculate 18% GST from inclusive retail total
        $grossTotal = (float) $order->total_price;
        $taxableAmount = round($grossTotal / 1.18, 2);
        $totalTax = round($grossTotal - $taxableAmount, 2);

        $cgstRate = 0.0;
        $cgstAmount = 0.0;
        $sgstRate = 0.0;
        $sgstAmount = 0.0;
        $igstRate = 0.0;
        $igstAmount = 0.0;

        if ($isInterstate) {
            $igstRate = 18.00;
            $igstAmount = $totalTax;
        } else {
            $cgstRate = 9.00;
            $cgstAmount = round($totalTax / 2, 2);
            $sgstRate = 9.00;
            $sgstAmount = round($totalTax - $cgstAmount, 2);
        }

        $invoiceNumber = 'TAX-' . date('Y') . '-' . strtoupper(Str::random(6));

        return GstInvoice::create([
            'order_id' => $order->id,
            'seller_id' => $seller ? $seller->id : 1,
            'invoice_number' => $invoiceNumber,
            'invoice_type' => $buyerGstin ? 'b2b' : 'b2c',
            'seller_gstin' => $sellerGstin,
            'seller_legal_name' => $sellerLegalName,
            'seller_state_code' => $sellerStateCode,
            'seller_state_name' => $sellerStateName,
            'buyer_gstin' => $buyerGstin,
            'buyer_legal_name' => $buyerLegalName,
            'buyer_state_code' => $buyerStateCode,
            'buyer_state_name' => $buyerStateName,
            'is_interstate' => $isInterstate,
            'taxable_amount' => $taxableAmount,
            'cgst_rate' => $cgstRate,
            'cgst_amount' => $cgstAmount,
            'sgst_rate' => $sgstRate,
            'sgst_amount' => $sgstAmount,
            'igst_rate' => $igstRate,
            'igst_amount' => $igstAmount,
            'total_tax' => $totalTax,
            'invoice_total' => $grossTotal,
            'invoice_date' => now()->toDateString(),
        ]);
    }

    private function getStateCode(string $stateName): string
    {
        $states = [
            'delhi' => '07',
            'uttar pradesh' => '09',
            'haryana' => '06',
            'punjab' => '03',
            'rajasthan' => '08',
            'maharashtra' => '27',
            'karnataka' => '29',
            'gujarat' => '24',
            'tamil nadu' => '33',
            'west bengal' => '19',
        ];

        $key = strtolower(trim($stateName));
        return $states[$key] ?? '09';
    }
}