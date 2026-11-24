<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\SellerPage;
use App\Services\AISlipScannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QuotationController extends Controller
{
    /**
     * Create a new Quotation from Storefront Cart or AI Slip
     */
    public function createQuotation(Request $request, SellerPage $sellerPage, AISlipScannerService $scannerService)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'nullable|string|max:300',
            'project_name' => 'nullable|string|max:150',
            'customer_notes' => 'nullable|string|max:500',
            'cart' => 'nullable|array',
            'cart.*.id' => 'nullable|integer',
            'cart.*.name' => 'nullable|string',
            'cart.*.price' => 'nullable|numeric',
            'cart.*.quantity' => 'nullable|integer|min:1',
            'cart.*.size' => 'nullable|string',
            'slip_text' => 'nullable|string',
        ]);

        $items = [];
        $subtotal = 0;

        // Mode 1: Items from Cart
        if (!empty($validated['cart']) && is_array($validated['cart'])) {
            foreach ($validated['cart'] as $cItem) {
                $qty = max(1, intval($cItem['quantity'] ?? 1));
                $unitPrice = floatval($cItem['price'] ?? 0);
                $lineTotal = $unitPrice * $qty;
                $subtotal += $lineTotal;

                $items[] = [
                    'product_id' => $cItem['id'] ?? null,
                    'name' => $cItem['name'] ?? 'Product Item',
                    'size' => $cItem['size'] ?? 'Standard',
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                    'image' => $cItem['image'] ?? null,
                    'is_matched' => true,
                ];
            }
        }
        // Mode 2: Items from AI Slip Text (Contractor / Plumber list)
        elseif (!empty($validated['slip_text'])) {
            $parsed = $scannerService->parseSlipText($validated['slip_text'], $sellerPage->user_id);
            foreach ($parsed['items'] as $pItem) {
                $items[] = [
                    'product_id' => $pItem['matched_product_id'] ?? null,
                    'name' => $pItem['is_matched'] ? $pItem['matched_product_name'] : $pItem['item_name'],
                    'size' => $pItem['detected_size'] ?? 'Standard',
                    'quantity' => $pItem['quantity'],
                    'unit_price' => $pItem['unit_price'],
                    'line_total' => $pItem['line_total'],
                    'image' => $pItem['image'] ?? null,
                    'is_matched' => $pItem['is_matched'],
                ];
                $subtotal += $pItem['line_total'];
            }
        }

        if (empty($items)) {
            return response()->json([
                'success' => false,
                'message' => 'कृपया कोटेशन हेतु कम से कम 1 आइटम कार्ट में जोड़ें या पर्ची का सामान लिखें।'
            ], 422);
        }

        // Generate formal unique quotation number
        $quotationNumber = 'QT-' . date('ymd') . '-' . strtoupper(Str::random(4));

        $discountPercent = 0;
        $discountAmount = 0;
        $taxAmount = 0; // Wholesale plumbing items can be tax inclusive or calculated on approval
        $grandTotal = $subtotal;

        $quotation = Quotation::create([
            'quotation_number' => $quotationNumber,
            'seller_page_id' => $sellerPage->id,
            'seller_id' => $sellerPage->user_id,
            'user_id' => Auth::id(),
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_address' => $validated['customer_address'] ?? '',
            'project_name' => $validated['project_name'] ?? 'General Material Estimate',
            'items' => $items,
            'subtotal' => $subtotal,
            'discount_percent' => $discountPercent,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'grand_total' => $grandTotal,
            'status' => 'pending',
            'valid_until' => now()->addDays(7),
            'customer_notes' => $validated['customer_notes'] ?? '',
        ]);

        $viewUrl = route('minisite.showQuotation', ['sellerPage' => $sellerPage->slug, 'quotationNumber' => $quotationNumber]);
        $ownerManageUrl = route('seller.quotations.show', $quotationNumber);

        // Pre-formatted WhatsApp text for Store Owner
        $ownerPhone = preg_replace('/[^0-9]/', '', $sellerPage->whatsapp_number ?: ($sellerPage->phone ?: ''));
        $ownerMsg = "📄 *नया कोटेशन अनुरोध (#{$quotationNumber})*\n" .
                    "🏪 *दुकान:* {$sellerPage->page_title}\n\n" .
                    "👤 *ग्राहक:* {$validated['customer_name']}\n" .
                    "📞 *मोबाइल:* {$validated['customer_phone']}\n" .
                    ($quotation->project_name ? "🏗️ *साइट:* {$quotation->project_name}\n" : "") .
                    "📦 *कुल आइटम्स:* " . count($items) . "\n" .
                    "💰 *अनुमानित बिल:* ₹" . number_format($grandTotal, 2) . "\n\n" .
                    "👉 *कोटेशन देखें व रेट/डिस्काउंट अप्रूव करें:*\n{$ownerManageUrl}\n\n" .
                    "⚡ *VyaparIndia AI Quotation Engine*";

        $ownerWaUrl = $ownerPhone ? "https://wa.me/{$ownerPhone}?text=" . urlencode($ownerMsg) : null;

        return response()->json([
            'success' => true,
            'quotation_number' => $quotationNumber,
            'view_url' => $viewUrl,
            'owner_wa_url' => $ownerWaUrl,
            'grand_total' => number_format($grandTotal, 2),
            'items_count' => count($items),
            'message' => 'कोटेशन अनुरोध सफलतापूर्वक दर्ज हो गया है! दुकान मालिक द्वारा अप्रूव होते ही आपको सूचित किया जाएगा।'
        ]);
    }

    /**
     * Public View of Quotation (Official Printable / Shareable View)
     */
    public function showQuotation(SellerPage $sellerPage, $quotationNumber)
    {
        $quotation = Quotation::where('quotation_number', $quotationNumber)
            ->where('seller_page_id', $sellerPage->id)
            ->firstOrFail();

        return view('seller-site.quotation_view', compact('sellerPage', 'quotation'));
    }

    /**
     * 1-Click Convert Approved Quotation to Confirmed Order
     */
    public function convertQuotationToOrder(Request $request, SellerPage $sellerPage, $quotationNumber)
    {
        $quotation = Quotation::where('quotation_number', $quotationNumber)
            ->where('seller_page_id', $sellerPage->id)
            ->firstOrFail();

        if ($quotation->status === 'converted_to_order' && $quotation->converted_order_id) {
            return redirect()->route('minisite.orderSuccess', [
                'sellerPage' => $sellerPage->slug,
                'order' => $quotation->converted_order_id,
            ])->with('info', 'यह कोटेशन पहले ही ऑर्डर में बदला जा चुका है।');
        }

        $orderNumber = 'ORD-QT-' . strtoupper(Str::random(6));

        $order = Order::create([
            'order_number' => $orderNumber,
            'order_source' => 'quotation_converted',
            'user_id' => $quotation->user_id ?: Auth::id(),
            'seller_id' => $sellerPage->user_id,
            'total_price' => $quotation->grand_total,
            'original_cod_total' => $quotation->grand_total,
            'customer_name' => $quotation->customer_name,
            'customer_phone' => $quotation->customer_phone,
            'shipping_address' => $quotation->customer_address ?: ($quotation->project_name ?: $sellerPage->address),
            'city' => $sellerPage->city ?? '',
            'state' => $sellerPage->state ?? '',
            'pincode' => $sellerPage->pincode ?? '496445',
            'payment_method' => $request->input('payment_method', 'cod'),
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        // Attach products if matched
        $orderItems = [];
        foreach ($quotation->items as $item) {
            if (!empty($item['product_id'])) {
                $orderItems[$item['product_id']] = [
                    'quantity' => $item['quantity'],
                    'price' => $item['unit_price'],
                ];
            }
        }
        if (!empty($orderItems)) {
            $order->products()->attach($orderItems);
        }

        $quotation->update([
            'status' => 'converted_to_order',
            'converted_order_id' => $order->id,
        ]);

        return redirect()->route('minisite.orderSuccess', [
            'sellerPage' => $sellerPage->slug,
            'order' => $order->id,
        ])->with('success', "कोटेशन #{$quotation->quotation_number} सफलतापूर्वक कन्फ़र्म ऑर्डर में बदल दिया गया है!");
    }
}
