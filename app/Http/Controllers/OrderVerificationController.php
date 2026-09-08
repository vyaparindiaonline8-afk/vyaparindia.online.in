<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\WhatsAppVerificationService;

class OrderVerificationController extends Controller
{
    public function show(string $token)
    {
        $order = Order::where('cod_verification_token', $token)->firstOrFail();
        return view('public.order_verification', compact('order'));
    }

    public function payPrepaid(string $token, WhatsAppVerificationService $verificationService)
    {
        $order = Order::where('cod_verification_token', $token)->firstOrFail();
        $result = $verificationService->convertOrderToPrepaid($order);

        return redirect()->route('verification.show', $token)->with('success', 'Payment successful! ₹' . number_format($result['discount_applied'], 2) . ' Instant Discount applied. Order converted to 100% Prepaid.');
    }

    public function confirmCod(string $token, WhatsAppVerificationService $verificationService)
    {
        $order = Order::where('cod_verification_token', $token)->firstOrFail();
        $verificationService->confirmCodOrder($order);

        return redirect()->route('verification.show', $token)->with('success', 'COD Order Verified! Your package will be packed and dispatched shortly.');
    }
}