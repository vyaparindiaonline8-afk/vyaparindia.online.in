<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerQuotationController extends Controller
{
    /**
     * List all Quotations requested for this Seller
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $query = Quotation::where('seller_id', Auth::id())
            ->with(['sellerPage'])
            ->latest();

        if ($status !== 'all' && in_array($status, ['pending', 'approved', 'converted_to_order', 'rejected'])) {
            $query->where('status', $status);
        }

        $quotations = $query->paginate(15)->withQueryString();

        $pendingCount = Quotation::where('seller_id', Auth::id())->where('status', 'pending')->count();
        $approvedCount = Quotation::where('seller_id', Auth::id())->where('status', 'approved')->count();
        $totalCount = Quotation::where('seller_id', Auth::id())->count();

        return view('seller.quotations.index', compact('quotations', 'pendingCount', 'approvedCount', 'totalCount', 'status'));
    }

    /**
     * Show / Review Quotation for Approval
     */
    public function show($quotationNumber)
    {
        $quotation = Quotation::where('quotation_number', $quotationNumber)
            ->where('seller_id', Auth::id())
            ->with(['sellerPage'])
            ->firstOrFail();

        return view('seller.quotations.show', compact('quotation'));
    }

    /**
     * Store Owner Approves Quotation & Formats Customer WhatsApp Message
     */
    public function approve(Request $request, $quotationNumber)
    {
        $quotation = Quotation::where('quotation_number', $quotationNumber)
            ->where('seller_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'valid_days' => 'nullable|integer|min:1|max:90',
            'owner_notes' => 'nullable|string|max:1000',
        ]);

        $subtotal = floatval($quotation->subtotal);
        $discountPct = floatval($validated['discount_percent'] ?? 0);
        $discountAmt = floatval($validated['discount_amount'] ?? 0);

        if ($discountPct > 0 && $discountAmt == 0) {
            $discountAmt = round(($subtotal * $discountPct) / 100, 2);
        }

        $taxAmt = floatval($validated['tax_amount'] ?? 0);
        $grandTotal = max(0, ($subtotal - $discountAmt) + $taxAmt);

        $validDays = intval($validated['valid_days'] ?? 7);

        $quotation->update([
            'discount_percent' => $discountPct,
            'discount_amount' => $discountAmt,
            'tax_amount' => $taxAmt,
            'grand_total' => $grandTotal,
            'valid_until' => now()->addDays($validDays),
            'owner_notes' => $validated['owner_notes'] ?? 'Approved by store owner. Best wholesale rates applied.',
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $sellerPage = $quotation->sellerPage;
        $storeName = $sellerPage ? $sellerPage->page_title : 'Ganesh Traders';
        $viewUrl = $sellerPage ? route('minisite.showQuotation', ['sellerPage' => $sellerPage->slug, 'quotationNumber' => $quotation->quotation_number]) : url('/');

        // Format direct WhatsApp notification for the customer
        $custPhone = preg_replace('/[^0-9]/', '', $quotation->customer_phone);
        if (strlen($custPhone) === 10) {
            $custPhone = '91' . $custPhone;
        }

        $waText = "✅ *{$storeName} - आपका कोटेशन अप्रूव हो गया है!*\n\n" .
                  "नमस्ते *{$quotation->customer_name}* जी,\n" .
                  "आपके द्वारा माँगा गया कोटेशन (*#{$quotation->quotation_number}*) स्टोर द्वारा रिव्यू करके अप्रूव कर दिया गया है।\n\n" .
                  "📦 *कुल सामग्री:* " . count($quotation->items ?? []) . " आइटम्स\n" .
                  "💰 *फाइनल अप्रूव्ड बिल:* ₹" . number_format($grandTotal, 2) . "\n" .
                  ($discountAmt > 0 ? "🏷️ *डिस्काउंट:* ₹" . number_format($discountAmt, 2) . " छूट लागू की गई है।\n" : "") .
                  "⏳ *वैधता:* " . $quotation->valid_until->format('d M, Y') . " तक मान्य\n\n" .
                  "📄 *ऑफिशियल कोटेशन स्लिप देखने व PDF डाउनलोड करने हेतु यहाँ क्लिक करें:*\n{$viewUrl}\n\n" .
                  "🛒 *1-Click ऑर्डर:* ऊपर दिए गए लिंक पर जाकर आप सीधे 'Convert to Order' दबाकर ऑर्डर बुक कर सकते हैं। धन्यवाद!";

        $customerWaUrl = "https://wa.me/{$custPhone}?text=" . urlencode($waText);

        return redirect()->route('seller.quotations.show', $quotation->quotation_number)
            ->with('success', "कोटेशन #{$quotation->quotation_number} सफलतापूर्वक अप्रूव हो गया है!")
            ->with('customer_wa_url', $customerWaUrl)
            ->with('customer_wa_name', $quotation->customer_name);
    }

    /**
     * Store Owner Rejects Quotation
     */
    public function reject(Request $request, $quotationNumber)
    {
        $quotation = Quotation::where('quotation_number', $quotationNumber)
            ->where('seller_id', Auth::id())
            ->firstOrFail();

        $quotation->update([
            'status' => 'rejected',
            'owner_notes' => $request->input('reason', 'Items currently out of stock or price mismatch.'),
        ]);

        return redirect()->route('seller.quotations.show', $quotation->quotation_number)
            ->with('info', "कोटेशन #{$quotation->quotation_number} को रिजेक्ट कर दिया गया है।");
    }
}
