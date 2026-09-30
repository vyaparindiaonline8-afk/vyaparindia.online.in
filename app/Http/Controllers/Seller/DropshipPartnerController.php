<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\DropshipPartnership;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DropshipPartnerController extends Controller
{
    /**
     * Retailer submits a request to partner with a wholesaler.
     */
    public function requestPartnership(Request $request)
    {
        $request->validate([
            'wholesaler_id' => 'required|exists:users,id',
            'request_note' => 'nullable|string|max:300',
        ]);

        $retailerId = Auth::id();
        $wholesalerId = intval($request->wholesaler_id);

        if ($retailerId === $wholesalerId) {
            return back()->with('error', 'Aap apne hi account ke sath partnership request nahi bhej sakte.');
        }

        $partnership = DropshipPartnership::updateOrCreate(
            ['retailer_id' => $retailerId, 'wholesaler_id' => $wholesalerId],
            [
                'status' => 'pending',
                'request_note' => $request->request_note ?? 'I want to sell your products on my store.',
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Partnership request sent successfully! Wholesaler review karega.',
            ]);
        }

        return back()->with('success', '🤝 Dropshipping Partnership Request bhej di gayi hai! Wholesaler ke accept karte hi aap unke products bech sakenge.');
    }

    /**
     * Wholesaler views all incoming partnership requests and approved partners.
     */
    public function wholesalerPartners()
    {
        $wholesalerId = Auth::id();

        $partnerships = DropshipPartnership::where('wholesaler_id', $wholesalerId)
            ->with(['retailer.sellerProfile', 'retailer.sellerPage'])
            ->latest()
            ->paginate(15);

        $pendingCount = DropshipPartnership::where('wholesaler_id', $wholesalerId)->where('status', 'pending')->count();
        $approvedCount = DropshipPartnership::where('wholesaler_id', $wholesalerId)->where('status', 'approved')->count();

        return view('seller.wholesaler.partners', compact('partnerships', 'pendingCount', 'approvedCount'));
    }

    /**
     * Wholesaler approves or rejects partnership request.
     */
    public function updatePartnershipStatus(Request $request, DropshipPartnership $partnership)
    {
        if ($partnership->wholesaler_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $partnership->update([
            'status' => $request->status,
            'approved_at' => ($request->status === 'approved') ? now() : null,
        ]);

        $msg = ($request->status === 'approved') 
            ? "Retailer ko approve kar diya gaya hai! Ab wo aapke products ko dropship kar sakega."
            : "Request reject kar di gayi hai.";

        return back()->with('success', $msg);
    }
}
