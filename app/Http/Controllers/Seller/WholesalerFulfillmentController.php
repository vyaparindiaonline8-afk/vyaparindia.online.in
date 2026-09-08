<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DropshipOrder;
use App\Models\DropshipWallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;

class WholesalerFulfillmentController extends Controller
{
    // List incoming fulfillment requests for the wholesaler
    public function orders()
    {
        $wholesalerId = Auth::id();
        $dropshipOrders = DropshipOrder::where('wholesaler_id', $wholesalerId)
            ->with(['order.products', 'dropshipper.sellerPage'])
            ->latest()
            ->paginate(15);

        return view('seller.wholesaler.orders', compact('dropshipOrders'));
    }

    // Wholesaler Adjusts Base Price (low volume override) + Shipping Fee
    public function adjustPricing(Request $request, DropshipOrder $dsOrder)
    {
        if ($dsOrder->wholesaler_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'supplier_base_cost' => 'required|numeric|min:0',
            'shipping_cost' => 'required|numeric|min:0',
            'wholesaler_adjustment_note' => 'nullable|string|max:500',
        ]);

        $baseCost = floatval($validated['supplier_base_cost']);
        $shippingCost = floatval($validated['shipping_cost']);
        $totalSupplierPayable = $baseCost + $shippingCost;
        $dropshipperProfit = $dsOrder->customer_retail_total - $totalSupplierPayable;

        $dsOrder->update([
            'supplier_base_cost' => $baseCost,
            'shipping_cost' => $shippingCost,
            'total_supplier_payable' => $totalSupplierPayable,
            'dropshipper_profit' => $dropshipperProfit,
            'price_adjusted_by_wholesaler' => true,
            'wholesaler_adjustment_note' => $validated['wholesaler_adjustment_note'] ?? 'Wholesaler adjusted price / shipping cost.',
            'dropshipper_approval_status' => 'pending_approval',
            'fulfillment_status' => 'awaiting_dropshipper_approval',
        ]);

        return back()->with('success', 'Updated pricing & shipping quote sent to dropshipper for approval.');
    }

    // Wholesaler Accepts Order Directly (if standard price accepted)
    public function acceptOrder(DropshipOrder $dsOrder)
    {
        if ($dsOrder->wholesaler_id !== Auth::id()) {
            abort(403);
        }

        $dsOrder->update([
            'fulfillment_status' => 'ready_to_pack',
            'dropshipper_approval_status' => 'approved',
        ]);

        return back()->with('success', 'Order accepted! Ready to pack and dispatch.');
    }

    // Wholesaler Marks Dispatched with Courier Name, AWB Number, Tracking URL
    public function markDispatched(Request $request, DropshipOrder $dsOrder)
    {
        if ($dsOrder->wholesaler_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'courier_partner' => 'required|string|max:100',
            'awb_number' => 'required|string|max:100',
            'tracking_url' => 'nullable|url|max:255',
        ]);

        $dsOrder->update([
            'courier_partner' => $validated['courier_partner'],
            'awb_number' => $validated['awb_number'],
            'tracking_url' => $validated['tracking_url'] ?: 'https://track.delhivery.com/p/' . $validated['awb_number'],
            'fulfillment_status' => 'dispatched',
            'dispatched_at' => now(),
        ]);

        // Also update parent order status
        $dsOrder->order->update(['status' => 'shipped']);

        return back()->with('success', "Order marked as Dispatched with AWB #{$validated['awb_number']} ({$validated['courier_partner']}). Dropshipper and customer can now live track.");
    }

    // Wholesaler Marks Delivered & Credits Dropshipper Wallet
    public function markDelivered(DropshipOrder $dsOrder)
    {
        if ($dsOrder->wholesaler_id !== Auth::id()) {
            abort(403);
        }

        $dsOrder->update([
            'fulfillment_status' => 'delivered',
            'delivered_at' => now(),
        ]);

        $dsOrder->order->update(['status' => 'delivered']);

        // Credit Dropshipper Wallet with Profit
        $wallet = DropshipWallet::firstOrCreate(['user_id' => $dsOrder->dropshipper_id]);
        $wallet->increment('balance', $dsOrder->dropshipper_profit);
        $wallet->increment('total_earned', $dsOrder->dropshipper_profit);

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => 'credit',
            'amount' => $dsOrder->dropshipper_profit,
            'reference_type' => 'dropship_order',
            'reference_id' => $dsOrder->id,
            'description' => "Profit earned from Dropship Order #{$dsOrder->ds_order_number}",
        ]);

        return back()->with('success', "Order marked as Delivered! ₹{$dsOrder->dropshipper_profit} profit credited to dropshipper wallet.");
    }

    // Wholesaler Updates COD Remittance Status
    public function updateCodStatus(Request $request, DropshipOrder $dsOrder)
    {
        if ($dsOrder->wholesaler_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'cod_remittance_status' => 'required|in:pending,collected_by_courier,remitted_to_dropshipper',
        ]);

        $dsOrder->update([
            'cod_remittance_status' => $validated['cod_remittance_status'],
        ]);

        return back()->with('success', 'COD Remittance status updated successfully.');
    }

    // White-label Packing Slip & Invoice
    public function invoice(DropshipOrder $dsOrder)
    {
        if ($dsOrder->wholesaler_id !== Auth::id() && $dsOrder->dropshipper_id !== Auth::id()) {
            abort(403);
        }

        return view('seller.wholesaler.invoice', compact('dsOrder'));
    }
}