<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\GstInvoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductPricingTier;
use App\Services\GstInvoiceService;
use Illuminate\Http\Request;

class GstInvoiceController extends Controller
{
    public function show(Order $order, GstInvoiceService $service)
    {
        $invoice = GstInvoice::where('order_id', $order->id)->first();
        if (!$invoice) {
            $invoice = $service->generateInvoice($order);
        }

        return view('seller.invoices.gst_tax_invoice', compact('order', 'invoice'));
    }

    public function pricingTiers(Product $product)
    {
        $tiers = ProductPricingTier::where('product_id', $product->id)->orderBy('min_quantity')->get();
        return view('seller.products.pricing_tiers', compact('product', 'tiers'));
    }

    public function storeTier(Request $request, Product $product)
    {
        $request->validate([
            'tier_name' => 'required|string',
            'min_quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $discount = max(0, round((($product->price - $request->unit_price) / $product->price) * 100, 2));

        ProductPricingTier::create([
            'product_id' => $product->id,
            'tier_name' => $request->tier_name,
            'min_quantity' => $request->min_quantity,
            'max_quantity' => $request->max_quantity,
            'unit_price' => $request->unit_price,
            'discount_percent' => $discount,
        ]);

        return redirect()->route('seller.products.pricing_tiers', $product->id)->with('success', "Slab tier '{$request->tier_name}' saved successfully!");
    }
}