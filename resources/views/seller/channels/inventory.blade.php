<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Omnichannel Centralized Inventory - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800 antialiased min-h-screen">

    <!-- Top Nav -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.channels.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-lg text-gray-900 leading-tight">Omnichannel Inventory Manager</h1>
                        <p class="text-xs text-gray-500">Centralized real-time stock sync across Mini-Site, Shopify, WooCommerce & Marketplaces</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.channels.publisher') }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        <span>New Product Listing</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="font-black text-base text-gray-900">Live Inventory Stock Levels</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Changing stock here instantly updates all connected channels</p>
                </div>
                <span class="text-xs font-semibold bg-gray-100 text-gray-700 px-3 py-1 rounded-full">
                    {{ $products->total() }} Products Tracked
                </span>
            </div>

            @if($products->isEmpty())
                <div class="p-16 text-center text-gray-400">
                    <i class="fa-solid fa-boxes-stacked text-5xl mb-4 text-gray-300"></i>
                    <h3 class="text-base font-bold text-gray-700">No products found</h3>
                    <a href="{{ route('seller.channels.publisher') }}" class="mt-4 inline-block px-5 py-2.5 bg-blue-600 text-white font-bold text-xs rounded-xl">
                        Publish First Product
                    </a>
                </div>
            @else
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-[11px] uppercase font-bold text-gray-500 border-b border-gray-100">
                        <tr>
                            <th class="p-4">Product</th>
                            <th class="p-4">SKU / HSN</th>
                            <th class="p-4">Price</th>
                            <th class="p-4">Connected Channels</th>
                            <th class="p-4">Central Stock</th>
                            <th class="p-4 text-right">Quick Sync Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($products as $p)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $p->image_url }}" class="h-12 w-12 object-cover rounded-xl border border-gray-100 shrink-0">
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $p->name }}</div>
                                            <div class="text-[11px] text-gray-400">{{ $p->category->name ?? 'General' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 font-mono font-bold text-gray-700">
                                    <div>{{ $p->sku ?: 'SKU-NONE' }}</div>
                                    <div class="text-[10px] text-gray-400 font-normal">HSN: {{ $p->hsn_code ?: '500720' }}</div>
                                </td>
                                <td class="p-4 font-extrabold text-gray-900 text-sm">₹{{ number_format($p->price, 2) }}</td>
                                <td class="p-4">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                            Mini-Site
                                        </span>
                                        @foreach($p->channelListings as $cl)
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700" title="{{ $cl->channel->store_name }}">
                                                {{ ucfirst($cl->channel->channel_name) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="font-black text-sm {{ $p->stock_quantity < 10 ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $p->stock_quantity }} Units
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <form action="{{ route('seller.channels.syncStock', $p->id) }}" method="POST" class="inline-flex items-center gap-2">
                                        @csrf
                                        <input type="number" name="stock_quantity" value="{{ $p->stock_quantity }}" min="0" class="w-16 px-2.5 py-1.5 bg-gray-50 border border-gray-300 rounded-lg text-center font-bold text-xs focus:bg-white focus:outline-none">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-gray-900 hover:bg-gray-800 text-white font-bold text-xs transition-colors" title="Sync stock to all channels">
                                            Sync
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <div class="p-4 border-t border-gray-100">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</body>
</html>