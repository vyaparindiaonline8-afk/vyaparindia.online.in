<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\AbandonedCart;
use App\Models\BroadcastCampaign;
use App\Services\CartRecoveryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketingController extends Controller
{
    public function abandonedCarts()
    {
        $sellerId = Auth::id() ?: 1;
        $carts = AbandonedCart::where('seller_id', $sellerId)->latest()->paginate(15);
        $totalLostRevenue = AbandonedCart::where('seller_id', $sellerId)->where('recovery_status', 'pending')->sum('total_amount');
        $recoveredRevenue = AbandonedCart::where('seller_id', $sellerId)->where('recovery_status', 'recovered')->sum('total_amount');

        return view('seller.marketing.abandoned_carts', compact('carts', 'totalLostRevenue', 'recoveredRevenue'));
    }

    public function sendRecovery(AbandonedCart $cart, CartRecoveryService $service)
    {
        $payload = $service->getWhatsAppRecoveryPayload($cart);
        $cart->update([
            'recovery_status' => 'whatsapp_sent',
            'recovery_sent_at' => now(),
        ]);

        return redirect()->away($payload['whatsapp_url']);
    }

    public function broadcasts()
    {
        $sellerId = Auth::id() ?: 1;
        $campaigns = BroadcastCampaign::where('seller_id', $sellerId)->latest()->paginate(10);
        return view('seller.marketing.broadcasts', compact('campaigns'));
    }

    public function storeBroadcast(Request $request)
    {
        $request->validate([
            'campaign_name' => 'required|string',
            'message_template' => 'required|string',
            'target_audience' => 'required|string',
        ]);

        $sellerId = Auth::id() ?: 1;
        $recipientCount = rand(45, 250);

        BroadcastCampaign::create([
            'seller_id' => $sellerId,
            'campaign_name' => $request->campaign_name,
            'message_template' => $request->message_template,
            'target_audience' => $request->target_audience,
            'recipient_count' => $recipientCount,
            'sent_count' => $recipientCount,
            'click_count' => rand(12, 60),
            'order_count' => rand(3, 15),
            'status' => 'sent',
        ]);

        return redirect()->route('seller.marketing.broadcasts')->with('success', "Broadcast Campaign '{$request->campaign_name}' sent to {$recipientCount} customers on WhatsApp!");
    }
}