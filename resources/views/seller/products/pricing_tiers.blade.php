@extends('seller-site.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg shadow-lg shadow-emerald-600/30">
                    <i class="fa-solid fa-layer-group"></i>
                </span>
                B2B Tiered Slab Pricing (Quantity-Based MOQ)
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Configure volume pricing slabs for <strong>{{ $product->name }}</strong> (Standard Price: ₹{{ $product->price }}).</p>
        </div>
        <a href="{{ route('seller.products.index') }}" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow transition">
            <i class="fa-solid fa-arrow-left"></i> Back to Products
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add Tier Form -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-emerald-500"></i> Add Pricing Slab
            </h3>

            <form action="{{ route('seller.products.store_tier', $product->id) }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Tier Name</label>
                    <input type="text" name="tier_name" placeholder="e.g. Small Wholesale (11-50 Units)" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Min Qty</label>
                        <input type="number" name="min_quantity" value="11" min="1" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Max Qty (Optional)</label>
                        <input type="number" name="max_quantity" value="50" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white">
                    </div>
                </div>
                <div>
                    <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Slab Unit Price (₹)</label>
                    <input type="number" step="0.01" name="unit_price" value="{{ round($product->price * 0.90, 2) }}" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white font-bold font-mono">
                </div>
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl shadow transition">
                    Save Volume Pricing Slab
                </button>
            </form>
        </div>

        <!-- Active Pricing Slabs -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-200 dark:border-slate-700">
                <h3 class="font-bold text-slate-900 dark:text-white text-sm">Active Volume Pricing Table</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-400 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="p-4">Slab Name</th>
                            <th class="p-4">Quantity Range</th>
                            <th class="p-4">Unit Price</th>
                            <th class="p-4">Discount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                        <tr>
                            <td class="p-4 font-bold text-slate-900 dark:text-white">Standard Retail MOQ</td>
                            <td class="p-4">1 - 10 Units</td>
                            <td class="p-4 font-mono font-bold text-slate-900 dark:text-white">₹{{ number_format($product->price, 2) }}</td>
                            <td class="p-4 text-slate-400">Base Rate</td>
                        </tr>
                        @forelse($tiers as $t)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-750 transition">
                            <td class="p-4 font-bold text-slate-900 dark:text-white">{{ $t->tier_name }}</td>
                            <td class="p-4">{{ $t->min_quantity }} {{ $t->max_quantity ? '- ' . $t->max_quantity : '+ Units' }}</td>
                            <td class="p-4 font-mono font-bold text-emerald-500">₹{{ number_format($t->unit_price, 2) }}</td>
                            <td class="p-4"><span class="bg-emerald-500/20 text-emerald-500 font-bold px-2 py-0.5 rounded text-[10px]">{{ $t->discount_percent }}% OFF</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-slate-400">No additional volume tiers set yet. Add one using the form.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection