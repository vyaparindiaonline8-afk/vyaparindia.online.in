@extends('seller-site.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-lg shadow-lg shadow-amber-500/30">
                    <i class="fa-solid fa-cart-arrow-down"></i>
                </span>
                WhatsApp Abandoned Cart Auto-Recovery
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Recover lost checkout drop-offs automatically with 1-click WhatsApp discount links (10% OFF).</p>
        </div>
        <a href="{{ route('seller.marketing.broadcasts') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow transition">
            <i class="fa-solid fa-bullhorn"></i> WhatsApp Broadcasts
        </a>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Unrecovered Lost Sales</span>
            <div class="text-3xl font-black text-rose-500 mt-1 font-mono">₹{{ number_format($totalLostRevenue, 2) }}</div>
            <span class="text-xs text-slate-400 mt-1 block">Customer drop-offs awaiting WhatsApp offer</span>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Recovered Revenue</span>
            <div class="text-3xl font-black text-emerald-500 mt-1 font-mono">₹{{ number_format($recoveredRevenue, 2) }}</div>
            <span class="text-xs text-emerald-500 font-semibold mt-1 block">Converted via WhatsApp Discount links</span>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Recovery Incentive</span>
            <div class="text-3xl font-black text-indigo-500 mt-1">Flat 10% OFF</div>
            <span class="text-xs text-slate-400 mt-1 block">Auto-calculated discount applied at checkout</span>
        </div>
    </div>

    <!-- Abandoned Carts Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
            <h3 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-amber-500"></i> Recent Abandoned Sessions
            </h3>
            <span class="text-xs text-slate-400">Auto-expires after 48 hours</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-400 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="p-4">Customer Details</th>
                        <th class="p-4">Cart Value</th>
                        <th class="p-4">10% Recovery Offer</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                    @forelse($carts as $cart)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-750 transition">
                        <td class="p-4">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $cart->customer_name }}</div>
                            <div class="text-slate-400 text-[11px] font-mono">{{ $cart->customer_phone }}</div>
                        </td>
                        <td class="p-4 font-mono font-bold text-slate-900 dark:text-white">
                            ₹{{ number_format($cart->total_amount, 2) }}
                        </td>
                        <td class="p-4">
                            <span class="text-emerald-500 font-bold font-mono">₹{{ number_format($cart->total_amount - $cart->recovery_discount_amount, 2) }}</span>
                            <span class="text-[10px] text-slate-400 block">Save ₹{{ $cart->recovery_discount_amount }}</span>
                        </td>
                        <td class="p-4">
                            @if($cart->recovery_status === 'recovered')
                                <span class="bg-emerald-500/20 text-emerald-500 border border-emerald-500/30 text-[10px] px-2 py-0.5 rounded font-bold">Recovered</span>
                            @elseif($cart->recovery_status === 'whatsapp_sent')
                                <span class="bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 text-[10px] px-2 py-0.5 rounded font-bold">WhatsApp Sent</span>
                            @else
                                <span class="bg-amber-500/20 text-amber-500 border border-amber-500/30 text-[10px] px-2 py-0.5 rounded font-bold">Pending</span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('seller.marketing.sendRecovery', $cart->id) }}" target="_blank" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] px-3 py-1.5 rounded-lg shadow transition">
                                <i class="fa-brands fa-whatsapp"></i> Send WhatsApp Recovery
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400">
                            No abandoned carts captured yet. Drop-off sessions will appear here automatically.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection