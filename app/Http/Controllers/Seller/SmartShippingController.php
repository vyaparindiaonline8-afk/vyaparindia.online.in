<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\DropshipOrder;
use App\Services\SmartLogisticsService;
use App\Services\WhatsAppVerificationService;
use Illuminate\Support\Facades\Auth;

class SmartShippingController extends Controller
{
    public function index(Request $request, SmartLogisticsService $logisticsService)
    {
        $sellerId = Auth::id();

        $orders = Order::where(function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId)
              ->orWhereHas('products', fn($p) => $p->where('user_id', $sellerId));
        })->with(['products', 'dispatch'])->latest()->paginate(15);

        $dsOrders = DropshipOrder::where(function ($q) use ($sellerId) {
            $q->where('wholesaler_id', $sellerId)->orWhere('dropshipper_id', $sellerId);
        })->with('order.products')->latest()->get();

        // Anti-RTO Stats
        $totalCodOrders = Order::where('seller_id', $sellerId)->where('payment_method', 'cod')->count();
        $verifiedCodOrders = Order::where('seller_id', $sellerId)->where('cod_verification_status', 'verified')->count();
        $convertedPrepaidOrders = Order::where('seller_id', $sellerId)->where('cod_verification_status', 'converted_to_prepaid')->count();
        $unverifiedCodOrders = Order::where('seller_id', $sellerId)->where('cod_verification_status', 'unverified')->count();

        // Live Rate Comparison Sample
        $sampleRates = $logisticsService->calculateCourierRates('302022', '110001', 500, true);

        return view('seller.shipping.dashboard', compact(
            'orders',
            'dsOrders',
            'totalCodOrders',
            'verifiedCodOrders',
            'convertedPrepaidOrders',
            'unverifiedCodOrders',
            'sampleRates'
        ));
    }

    // Live AJAX Courier Rate Engine
    public function estimateRates(Request $request, SmartLogisticsService $logisticsService)
    {
        $validated = $request->validate([
            'origin_pin' => 'required|string|min:6',
            'dest_pin' => 'required|string|min:6',
            'weight_grams' => 'nullable|integer|min:50',
            'is_cod' => 'nullable|boolean',
        ]);

        $weight = $validated['weight_grams'] ?? 500;
        $isCod = (bool)($validated['is_cod'] ?? true);

        $rates = $logisticsService->calculateCourierRates($validated['origin_pin'], $validated['dest_pin'], $weight, $isCod);

        return response()->json([
            'success' => true,
            'data' => $rates,
        ]);
    }

    // 1-Click Bulk 4x6 Thermal Shipping Labels
    public function bulkThermalLabels(Request $request)
    {
        $sellerId = Auth::id();
        $orderIds = $request->input('order_ids', []);

        $query = Order::where('seller_id', $sellerId)->with('products');
        if (!empty($orderIds)) {
            $query->whereIn('id', $orderIds);
        } else {
            $query->whereIn('status', ['pending', 'processing', 'shipped'])->take(50);
        }

        $orders = $query->get();

        return view('seller.shipping.thermal_labels', compact('orders'));
    }

    // 1-Click Bulk Courier Manifest
    public function bulkManifest(Request $request)
    {
        $sellerId = Auth::id();
        $orders = Order::where('seller_id', $sellerId)
            ->whereIn('status', ['pending', 'processing', 'shipped'])
            ->with('products')
            ->take(50)
            ->get();

        return view('seller.shipping.manifest', compact('orders'));
    }

    // Trigger WhatsApp Verification Message
    public function sendVerification(Order $order, WhatsAppVerificationService $verificationService)
    {
        $result = $verificationService->setupOrderVerification($order, 6);

        // Pre-fill WhatsApp web link for seller to send or webhook trigger
        $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $order->customer_phone) . "?text=" . urlencode($result['whatsapp_message']);

        return redirect($waUrl);
    }

    // Handle NDR (Non-Delivery Report)
    public function handleNdr(Request $request, DropshipOrder $dsOrder)
    {
        $validated = $request->validate([
            'ndr_action' => 'required|in:reattempt,rto,update_address',
            'ndr_reattempt_date' => 'nullable|date',
            'ndr_customer_remarks' => 'nullable|string|max:500',
        ]);

        if ($validated['ndr_action'] === 'reattempt') {
            $dsOrder->update([
                'ndr_status' => 'reattempt_requested',
                'ndr_reattempt_date' => $validated['ndr_reattempt_date'] ?? now()->addDay(),
                'ndr_customer_remarks' => $validated['ndr_customer_remarks'],
            ]);
            $msg = 'Courier re-attempt scheduled successfully for ' . ($validated['ndr_reattempt_date'] ?? 'tomorrow') . '.';
        } elseif ($validated['ndr_action'] === 'rto') {
            $dsOrder->update([
                'ndr_status' => 'rto_initiated',
                'fulfillment_status' => 'cancelled',
            ]);
            $msg = 'Order marked as RTO (Return to Origin). Shipment returning to warehouse.';
        } else {
            $dsOrder->update([
                'ndr_customer_remarks' => $validated['ndr_customer_remarks'],
            ]);
            $msg = 'Customer address & contact instructions updated for delivery boy.';
        }

        return back()->with('success', $msg);
    }
}