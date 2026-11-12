<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    /**
     * 🔍 Public & Seller Order Tracking Page (/track)
     * Supports searching by Order ID (ORD-...), Barcode/Tracking Number, or Order #ID.
     */
    public function index(Request $request, ?string $code = null)
    {
        $query = trim((string)($code ?: $request->query('q', '')));
        $order = null;
        $notFound = false;

        if ($query !== '') {
            $clean = ltrim($query, '#');
            $cleanUpper = strtoupper($clean);

            // 1. Direct match by order_number or dispatch tracking_number
            $order = Order::with(['products', 'dispatch', 'seller'])
                ->where(function ($q) use ($clean, $cleanUpper) {
                    $q->whereRaw('UPPER(order_number) = ?', [$cleanUpper])
                      ->orWhereHas('dispatch', function ($dq) use ($clean, $cleanUpper) {
                          $dq->whereRaw('UPPER(tracking_number) = ?', [$cleanUpper]);
                      });

                    if (is_numeric($clean)) {
                        $q->orWhere('id', (int)$clean);
                    } elseif (preg_match('/^ORD-0*(\d+)$/i', $clean, $m)) {
                        $q->orWhere('id', (int)$m[1]);
                    }
                })
                ->first();

            // 2. Fallback match for pre-dispatch VP-XXXXXXXXXX barcode generated on shipping label
            if (!$order && str_starts_with($cleanUpper, 'VP-')) {
                $recentOrders = Order::with(['products', 'dispatch', 'seller'])
                    ->latest()
                    ->limit(200)
                    ->get();
                foreach ($recentOrders as $cand) {
                    $genTrack = 'VP-' . strtoupper(substr(md5($cand->id . $cand->created_at), 0, 10));
                    if ($genTrack === $cleanUpper) {
                        $order = $cand;
                        break;
                    }
                }
            }

            if (!$order) {
                $notFound = true;
            }
        }

        return view('public.track_order', compact('query', 'order', 'notFound'));
    }
}
