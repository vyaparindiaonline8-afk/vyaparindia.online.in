<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\DropshipWallet;
use App\Models\PayoutAccount;
use App\Models\PayoutRequest;
use App\Services\UpiPayoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayoutController extends Controller
{
    public function settings()
    {
        $user = Auth::user();
        $accounts = PayoutAccount::where('user_id', $user->id)->get();
        $wallet = DropshipWallet::firstOrCreate(['user_id' => $user->id]);
        $recentPayouts = PayoutRequest::where('user_id', $user->id)->latest()->take(10)->get();

        return view('seller.dropship.payout_settings', compact('accounts', 'wallet', 'recentPayouts'));
    }

    public function storeAccount(Request $request)
    {
        $request->validate([
            'account_holder_name' => 'required|string',
            'payout_type' => 'required|in:upi,bank_account',
        ]);

        $user = Auth::user();

        PayoutAccount::create([
            'user_id' => $user->id,
            'payout_type' => $request->payout_type,
            'upi_id' => $request->upi_id,
            'account_holder_name' => $request->account_holder_name,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'ifsc_code' => $request->ifsc_code,
            'is_default' => true,
        ]);

        return redirect()->route('seller.dropship.payouts.settings')->with('success', 'Beneficiary account added successfully!');
    }

    public function requestPayout(Request $request, UpiPayoutService $service)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payout_account_id' => 'required|exists:payout_accounts,id',
        ]);

        $user = Auth::user();
        $wallet = DropshipWallet::where('user_id', $user->id)->first();
        $account = PayoutAccount::where('id', $request->payout_account_id)->where('user_id', $user->id)->firstOrFail();

        if (!$wallet) {
            return back()->with('error', 'Wallet not found.');
        }

        $res = $service->executePayout($wallet, (float) $request->amount, $account);

        if ($res['success']) {
            return redirect()->route('seller.dropship.wallet')->with('success', $res['message']);
        }

        return back()->with('error', $res['message']);
    }
}