@extends('seller-site.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-lg shadow-lg shadow-indigo-600/30">
                    <i class="fa-solid fa-bullhorn"></i>
                </span>
                WhatsApp Customer Broadcast Campaigns
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Send 1-click festival offers, discounts, and product drops to all past buyers.</p>
        </div>
        <a href="{{ route('seller.marketing.abandonedCarts') }}" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow transition">
            <i class="fa-solid fa-cart-arrow-down"></i> View Abandoned Carts
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Create Broadcast Form -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                <i class="fa-solid fa-paper-plane text-indigo-500"></i> New WhatsApp Broadcast
            </h3>

            <form action="{{ route('seller.marketing.storeBroadcast') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Campaign Name</label>
                    <input type="text" name="campaign_name" value="⚡ Festival Flash Sale 20% OFF" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Target Audience</label>
                    <select name="target_audience" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white">
                        <option value="all_customers">All Past Buyers (100% Reach)</option>
                        <option value="repeat_buyers">Repeat Customers (VIP Club)</option>
                        <option value="cod_buyers">COD Customers (Prepaid Incentive)</option>
                    </select>
                </div>
                <div>
                    <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Message Template</label>
                    <textarea name="message_template" rows="4" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white">🎉 Special Festival Offer for you! Get Flat 20% OFF on all smart electronics with Free Fast Dispatch. Click link to claim now!</textarea>
                </div>
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl shadow transition flex items-center justify-center gap-2">
                    <i class="fa-brands fa-whatsapp text-sm"></i> Send 1-Click WhatsApp Broadcast
                </button>
            </form>
        </div>

        <!-- Broadcasts History List -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-200 dark:border-slate-700">
                <h3 class="font-bold text-slate-900 dark:text-white text-sm">Campaign Analytics & Delivery</h3>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($campaigns as $camp)
                <div class="p-5 hover:bg-slate-50 dark:hover:bg-slate-750 transition flex flex-col sm:flex-row justify-between sm:items-center gap-4 text-xs">
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $camp->campaign_name }}</h4>
                        <p class="text-slate-400 mt-1 max-w-md line-clamp-1">{{ $camp->message_template }}</p>
                        <span class="text-[10px] text-slate-500 mt-1 block">Sent on {{ $camp->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                    <div class="flex items-center gap-4 text-right">
                        <div>
                            <span class="font-black text-slate-900 dark:text-white font-mono">{{ $camp->recipient_count }}</span>
                            <span class="text-[10px] text-slate-400 block">Sent</span>
                        </div>
                        <div>
                            <span class="font-black text-emerald-500 font-mono">{{ $camp->order_count }}</span>
                            <span class="text-[10px] text-slate-400 block">Orders</span>
                        </div>
                        <span class="bg-emerald-500/20 text-emerald-500 border border-emerald-500/30 text-[10px] px-2 py-0.5 rounded font-bold">Delivered</span>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    No broadcast campaigns created yet. Launch your first WhatsApp broadcast above!
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection