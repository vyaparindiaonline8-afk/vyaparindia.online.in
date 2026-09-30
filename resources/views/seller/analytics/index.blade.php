<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store & Product Analytics - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.dashboard') }}" class="h-9 w-9 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-base shadow-sm">
                        V
                    </a>
                    <div>
                        <span class="font-black text-gray-900 text-base tracking-tight">VyaparIndia</span>
                        <span class="ml-2 text-xs font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full">AI Performance Analytics</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.dashboard') }}" class="text-xs font-bold text-gray-600 hover:text-gray-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-black text-gray-900">Product Views & Sales Performance</h1>
                <p class="text-xs text-gray-500 mt-1">Real-time daily and monthly buyer interest, conversion rates, and fast/slow-moving inventory insights.</p>
            </div>
        </div>

        <!-- 4 Top KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Total Views -->
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase">Product Views</span>
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-eye"></i>
                    </div>
                </div>
                <div class="mt-3 text-2xl font-black text-gray-900">{{ number_format($totalViews) }}</div>
                <div class="mt-1 flex items-center gap-2 text-[11px] text-gray-500">
                    <span class="font-bold text-blue-600"><i class="fa-solid fa-calendar-day mr-0.5"></i> Today: {{ $todayViews }}</span>
                    <span>•</span>
                    <span>This Month: {{ $monthViews }}</span>
                </div>
            </div>

            <!-- Orders Count -->
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase">Confirmed Orders</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                </div>
                <div class="mt-3 text-2xl font-black text-gray-900">{{ number_format($totalOrdersCount) }}</div>
                <div class="mt-1 flex items-center gap-2 text-[11px] text-gray-500">
                    <span class="font-bold text-emerald-600"><i class="fa-solid fa-calendar-day mr-0.5"></i> Today: {{ $todayOrders }}</span>
                    <span>•</span>
                    <span>This Month: {{ $monthOrders }}</span>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase">Total Sales Volume</span>
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-indian-rupee-sign"></i>
                    </div>
                </div>
                <div class="mt-3 text-2xl font-black text-gray-900">₹{{ number_format($totalRevenue, 2) }}</div>
                <div class="mt-1 text-[11px] text-gray-500">Direct sales generated via store</div>
            </div>

            <!-- Conversion Rate -->
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase">Store Conversion Rate</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                </div>
                <div class="mt-3 text-2xl font-black text-gray-900">{{ $conversionRate }}%</div>
                <div class="mt-1 text-[11px] text-gray-500">Orders / Total Impressions ratio</div>
            </div>
        </div>

        <!-- 3 Insight Cards (Top Selling, Most Viewed, Slow Moving) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Top Selling -->
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-fire"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-xs text-gray-900">Top Selling Products</h3>
                        <p class="text-[10px] text-gray-400">Items with highest customer demand</p>
                    </div>
                </div>
                <div class="space-y-3">
                    @forelse($topSelling as $item)
                        <div class="flex items-center justify-between text-xs">
                            <div class="truncate max-w-[180px]">
                                <span class="font-bold text-gray-900 block truncate">{{ $item->name }}</span>
                                <span class="text-[10px] text-gray-400">{{ $item->category->name ?? 'General' }} • ₹{{ number_format($item->price, 2) }}</span>
                            </div>
                            <span class="font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md text-[11px]">
                                {{ $item->orders_count }} Sold
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 py-3 text-center">No orders recorded yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Most Viewed -->
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                    <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-xs text-gray-900">Most Viewed / Trending</h3>
                        <p class="text-[10px] text-gray-400">Products getting maximum customer clicks</p>
                    </div>
                </div>
                <div class="space-y-3">
                    @forelse($topViewed as $item)
                        <div class="flex items-center justify-between text-xs">
                            <div class="truncate max-w-[180px]">
                                <span class="font-bold text-gray-900 block truncate">{{ $item->name }}</span>
                                <span class="text-[10px] text-gray-400">{{ $item->category->name ?? 'General' }} • ₹{{ number_format($item->price, 2) }}</span>
                            </div>
                            <span class="font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md text-[11px]">
                                {{ $item->views_count }} Views
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 py-3 text-center">No views tracked yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Slow Moving Stock Alert -->
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                    <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-xs text-gray-900">Slow Moving / Review Price</h3>
                        <p class="text-[10px] text-gray-400">Viewed by buyers but 0 orders</p>
                    </div>
                </div>
                <div class="space-y-3">
                    @forelse($slowMoving as $item)
                        <div class="flex items-center justify-between text-xs">
                            <div class="truncate max-w-[180px]">
                                <span class="font-bold text-gray-900 block truncate">{{ $item->name }}</span>
                                <span class="text-[10px] text-amber-700">{{ $item->views_count }} Views • ₹{{ number_format($item->price, 2) }}</span>
                            </div>
                            <a href="{{ route('seller.products.edit', $item) }}" class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md hover:bg-indigo-100 transition">
                                Edit Price
                            </a>
                        </div>
                    @empty
                        <p class="text-xs text-emerald-600 py-3 text-center font-bold">Great! No high-view zero-order products.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Detailed Product Table -->
        <div class="bg-white border border-gray-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-sm text-gray-900">Catalog Items Performance Table</h2>
                    <p class="text-xs text-gray-500">Track views, sales units, and stock status for every listed product</p>
                </div>
                <span class="text-xs font-bold text-gray-500">{{ $products->count() }} Total Items</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-[11px] font-bold text-gray-500 uppercase">
                            <th class="py-3 px-4">Product Name</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Selling Price</th>
                            <th class="py-3 px-4 text-center">Views</th>
                            <th class="py-3 px-4 text-center">Orders Sold</th>
                            <th class="py-3 px-4 text-right">Revenue (₹)</th>
                            <th class="py-3 px-4 text-center">Performance Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        @foreach($products as $p)
                            @php
                                $pRevenue = $p->orders->sum(fn($o) => ($p->pivot->price ?? $p->price) * ($p->pivot->quantity ?? 1));
                                $isTop = $p->orders_count >= 5;
                                $isSlow = $p->orders_count == 0 && $p->views_count >= 10;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="py-3.5 px-4 font-bold text-gray-900">
                                    {{ $p->name }}
                                </td>
                                <td class="py-3.5 px-4 text-gray-500">
                                    {{ $p->category->name ?? 'Uncategorized' }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-gray-900">
                                    ₹{{ number_format($p->price, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-blue-600">
                                    {{ $p->views_count }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-emerald-600">
                                    {{ $p->orders_count }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-gray-900">
                                    ₹{{ number_format($pRevenue, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($isTop)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            🔥 Best Seller
                                        </span>
                                    @elseif($isSlow)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            ⚠️ Review Rate
                                        </span>
                                    @elseif($p->orders_count > 0)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                            📈 Regular
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">
                                            New Listing
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>
</html>
