@extends('seller-site.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-lg shadow-lg shadow-indigo-600/30">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                </span>
                Instant UPI & Bank Margin Payouts
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Direct instant settlement to your GooglePay / PhonePe / Bank Account on delivery.</p>
        </div>
        <a href="{{ route('seller.dropship.wallet') }}" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow transition">
            <i class="fa-solid fa-wallet"></i> View Wallet Ledger
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Request Payout Card -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm space-y-5">
            <div class="bg-gradient-to-br from-indigo-900 to-slate-900 p-5 rounded-xl text-white text-center space-y-1">
                <span class="text-xs text-indigo-300 font-bold uppercase tracking-wider">Available for Instant Transfer</span>
                <div class="text-3xl font-black font-mono">₹{{ number_format($wallet->balance, 2) }}</div>
                <span class="text-[11px] text-emerald-400 font-semibold flex items-center justify-center gap-1">
                    <i class="fa-solid fa-bolt text-yellow-400"></i> Instant IMPS/UPI Payout Engine
                </span>
            </div>

            @if($accounts->count() > 0)
            <form action="{{ route('seller.dropship.payouts.request') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Withdrawal Amount (₹)</label>
                    <input type="number" step="0.01" name="amount" value="{{ $wallet->balance }}" max="{{ $wallet->balance }}" min="1" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white font-mono font-bold">
                </div>
                <div>
                    <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Select Beneficiary UPI / Account</label>
                    <select name="payout_account_id" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->account_holder_name }} ({{ $acc->upi_id ?: $acc->account_number }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl shadow transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Instant Transfer to UPI / Bank
                </button>
            </form>
            @else
            <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-600 text-xs">
                Please add a UPI ID or Bank Account on the right before requesting withdrawal.
            </div>
            @endif
        </div>

        <!-- Add Beneficiary Form & History -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm space-y-4">
                <h3 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-indigo-500"></i> Add Beneficiary UPI / Bank Account
                </h3>

                <form action="{{ route('seller.dropship.payouts.storeAccount') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    @csrf
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Account Holder Name</label>
                        <input type="text" name="account_holder_name" value="Amit Verma" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Payout Method</label>
                        <select name="payout_type" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white">
                            <option value="upi">UPI ID (Instant Transfer)</option>
                            <option value="bank_account">Bank Account (IMPS/NEFT)</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">UPI ID (e.g. name@okhdfcbank)</label>
                        <input type="text" name="upi_id" value="trendymart@oksbi" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white font-mono">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2.5 rounded-xl shadow transition">
                            Save Beneficiary Account
                        </button>
                    </div>
                </form>
            </div>

            <!-- Recent Payout Transactions -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm">Recent Instant Payout History</h3>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($recentPayouts as $p)
                    <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-750 transition flex justify-between items-center text-xs">
                        <div>
                            <div class="font-bold text-slate-900 dark:text-white">Payout #{{ $p->payout_number }}</div>
                            <span class="text-[11px] text-slate-400 font-mono">UTR: {{ $p->utr_number }}</span>
                        </div>
                        <div class="text-right">
                            <div class="font-mono font-bold text-emerald-500 text-sm">₹{{ number_format($p->amount, 2) }}</div>
                            <span class="bg-emerald-500/20 text-emerald-500 border border-emerald-500/30 text-[10px] px-2 py-0.5 rounded font-bold">Transferred</span>
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-slate-400 text-xs">
                        No previous payouts yet. Your completed instant payouts will appear here.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection