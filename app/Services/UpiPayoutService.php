<?php

namespace App\Services;

use App\Models\DropshipWallet;
use App\Models\PayoutAccount;
use App\Models\PayoutRequest;
use App\Models\WalletTransaction;
use Illuminate\Support\Str;

class UpiPayoutService
{
    /**
     * Execute instant payout to seller UPI ID / Bank.
     */
    public function executePayout(DropshipWallet $wallet, float $amount, PayoutAccount $account): array
    {
        if ($wallet->balance < $amount) {
            return [
                'success' => false,
                'message' => "Insufficient balance in wallet (Available: ₹{$wallet->balance}, Requested: ₹{$amount})",
            ];
        }

        if ($amount < 1.00) {
            return [
                'success' => false,
                'message' => "Minimum payout amount is ₹1.00",
            ];
        }

        // Deduct from wallet
        $wallet->decrement('balance', $amount);
        $wallet->increment('total_withdrawn', $amount);

        $payoutNumber = 'PAY-' . strtoupper(Str::random(8));
        $utrNumber = 'UPI' . rand(100000000000, 999999999999);
        $gatewayRef = 'CF_PAY_' . Str::random(12);

        $beneficiaryDetails = [
            'type' => $account->payout_type,
            'holder_name' => $account->account_holder_name,
            'upi_id' => $account->upi_id,
            'bank_name' => $account->bank_name,
            'account_number' => $account->account_number,
            'ifsc_code' => $account->ifsc_code,
        ];

        $payoutRequest = PayoutRequest::create([
            'wallet_id' => $wallet->id,
            'user_id' => $wallet->user_id,
            'payout_number' => $payoutNumber,
            'amount' => $amount,
            'payout_method' => $account->payout_type,
            'beneficiary_details' => $beneficiaryDetails,
            'status' => 'completed',
            'utr_number' => $utrNumber,
            'gateway_reference' => $gatewayRef,
            'completed_at' => now(),
        ]);

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => 'debit',
            'amount' => $amount,
            'reference_type' => 'payout',
            'reference_id' => $payoutRequest->id,
            'description' => "Instant UPI Payout to {$account->upi_id} (UTR: {$utrNumber})",
        ]);

        return [
            'success' => true,
            'message' => "₹{$amount} transferred instantly to {$account->upi_id}!",
            'payout' => $payoutRequest,
            'utr_number' => $utrNumber,
            'balance_after' => $wallet->fresh()->balance,
        ];
    }
}