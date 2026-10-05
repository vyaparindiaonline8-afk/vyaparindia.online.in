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

        <!-- 5 Top KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <!-- 1. Storefront Visits -->
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase">Store Visitors</span>
                    <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <div class="mt-3 text-2xl font-black text-gray-900">{{ number_format($storeVisits) }}</div>
                <div class="mt-1 flex items-center gap-1.5 text-[11px] text-gray-500">
                    <span class="font-bold text-purple-600"><i class="fa-solid fa-store mr-0.5"></i> Website Footfall</span>
                </div>
            </div>

            <!-- 2. Total Product Views -->
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
                    <span>Month: {{ $monthViews }}</span>
                </div>
            </div>

            <!-- 3. Confirmed Orders -->
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase">Confirmed Orders</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                </div>
                <div class="mt-3 text-2xl font-black text-gray-900">{{ number_format($totalOrdersCount) }}</div>
                <div class="mt-1 flex items-center gap-2 text-[11px] text-gray-500">
                    <span class="font-bold text-emerald-600">₹{{ number_format($totalRevenue, 0) }} Sales</span>
                    <span>•</span>
                    <span>{{ $conversionRate }}% CVR</span>
                </div>
            </div>

            <!-- 4. Dropped / Unpaid Checkouts -->
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase">Dropped Checkouts</span>
                    <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-cart-arrow-down"></i>
                    </div>
                </div>
                <div class="mt-3 text-2xl font-black text-rose-600">{{ number_format($totalAbandonedCount) }}</div>
                <div class="mt-1 text-[11px] text-gray-500">
                    <span class="font-bold text-rose-500">₹{{ number_format($totalAbandonedAmount, 0) }}</span> Unpaid / Left Cart
                </div>
            </div>

            <!-- 5. Wishlist Bookmarks -->
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase">Wishlist Saves</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-heart"></i>
                    </div>
                </div>
                <div class="mt-3 text-2xl font-black text-amber-600">{{ number_format($totalWishlistsCount) }}</div>
                <div class="mt-1 text-[11px] text-gray-500">
                    <span>Products saved by buyers</span>
                </div>
            </div>
        </div>

        <!-- 🤖 AI Growth & Performance Advisor -->
        <div class="mb-8 p-5 bg-gradient-to-r from-indigo-900 via-indigo-800 to-purple-900 text-white rounded-3xl shadow-lg border border-indigo-700/50 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center text-xl text-amber-300 shrink-0">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-black tracking-wider uppercase text-amber-300">AI Performance Advisor</span>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[10px] font-bold">Live Store Health: 94%</span>
                    </div>
                    <p class="text-xs text-indigo-100 font-medium leading-relaxed">
                        @if($slowMoving->count() > 0)
                            Aapke <b>{{ $slowMoving->count() }} products</b> ko buyers dekh rahe hain par order kam hain. Inka selling price 5-10% review karne se conversion turant badhega.
                        @elseif($topSelling->count() > 0)
                            Aapka top item <b>"{{ $topSelling->first()->name }}"</b> sabse zyada bik raha hai! Demand continue rakhne ke liye buffer stock bana kar rakhein.
                        @else
                            Store performance active hai! Naye products add karke aur WhatsApp broadcast bhej kar daily orders badhayein.
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0 w-full md:w-auto">
                <a href="{{ route('seller.catalog.excel_mapper') }}" class="px-4 py-2.5 rounded-xl bg-white text-indigo-950 font-black text-xs hover:bg-indigo-50 transition shadow-sm flex items-center gap-1.5 w-full md:w-auto justify-center">
                    <i class="fa-solid fa-cloud-arrow-up text-indigo-600"></i>
                    <span>Add More Products</span>
                </a>
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

        <!-- 🛒 & ❤️ Recovery & Customer Interest Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- 1. Dropped / Unpaid Checkouts (ऑर्डर तक गए पर पेमेंट नहीं किया) -->
            <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-4 pb-3 border-b border-gray-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-black">
                                <i class="fa-solid fa-cart-arrow-down"></i>
                            </div>
                            <div>
                                <h3 class="font-black text-sm text-gray-900">Dropped & Unpaid Checkouts</h3>
                                <p class="text-[11px] text-gray-500">Customers who reached checkout but didn't finish payment</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-700">
                            {{ $totalAbandonedCount }} Dropped (₹{{ number_format($totalAbandonedAmount, 0) }})
                        </span>
                    </div>

                    <div class="space-y-3">
                        @forelse($unpaidOrders->take(4) as $uo)
                            @php
                                $cleanPhone = preg_replace('/[^0-9]/', '', $uo->customer_phone ?? '');
                                $waReminderText = urlencode("Namaste " . ($uo->customer_name ?? 'Customer') . "! Your order #" . $uo->id . " worth Rs." . number_format($uo->total_price, 2) . " on " . ($sellerPage->page_title ?? 'our store') . " is pending. Reply here or pay via UPI to confirm fast dispatch!");
                            @endphp
                            <div class="p-3 rounded-2xl bg-rose-50/50 border border-rose-100 flex items-center justify-between gap-3 text-xs">
                                <div>
                                    <div class="font-extrabold text-gray-900 flex items-center gap-2">
                                        <span>{{ $uo->customer_name ?: 'Buyer' }}</span>
                                        <span class="text-[10px] text-gray-400 font-normal">#{{ $uo->id }} • {{ $uo->created_at ? $uo->created_at->diffForHumans() : '' }}</span>
                                    </div>
                                    <div class="text-[11px] text-gray-600 mt-0.5">
                                        {{ $uo->products->count() }} items • <span class="font-bold text-rose-700 font-mono">₹{{ number_format($uo->total_price, 2) }}</span>
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 ml-1">Payment Pending</span>
                                    </div>
                                </div>
                                @if($cleanPhone)
                                    <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waReminderText }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-[11px] flex items-center gap-1 shadow-xs shrink-0 transition" title="Send WhatsApp Payment Link / Reminder">
                                        <i class="fa-brands fa-whatsapp"></i> Follow-up
                                    </a>
                                @endif
                            </div>
                        @empty
                            @if($abandonedCarts->count() > 0)
                                @foreach($abandonedCarts->take(3) as $ac)
                                    <div class="p-3 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-between text-xs">
                                        <div>
                                            <span class="font-bold text-gray-900">{{ $ac->customer_name ?: 'Buyer' }}</span>
                                            <div class="text-[10px] text-gray-500">{{ count($ac->cart_items ?? []) }} items in cart • ₹{{ number_format($ac->total_amount, 2) }}</div>
                                        </div>
                                        <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded">Cart Left</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-6 text-gray-400 text-xs">
                                    <i class="fa-solid fa-circle-check text-emerald-500 text-2xl block mb-1"></i>
                                    <span>Awesome! No unpaid or abandoned orders right now.</span>
                                </div>
                            @endif
                        @endforelse
                    </div>
                </div>

                <p class="text-[11px] text-gray-400 mt-4 pt-3 border-t border-gray-100 flex items-center gap-1">
                    <i class="fa-solid fa-shield-halved text-gray-400"></i>
                    <span>Sending a friendly WhatsApp reminder recovers up to 40% of dropped checkouts.</span>
                </p>
            </div>

            <!-- 2. Customer Wishlist Tracking (ग्राहकों ने विशलिस्ट में कौन से प्रोडक्ट्स रखे हैं) -->
            <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-4 pb-3 border-b border-gray-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-black">
                                <i class="fa-solid fa-heart"></i>
                            </div>
                            <div>
                                <h3 class="font-black text-sm text-gray-900">Wishlist & High-Intent Bookmarks</h3>
                                <p class="text-[11px] text-gray-500">Products buyers have saved for later buying</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-800">
                            {{ $totalWishlistsCount }} Total Saves
                        </span>
                    </div>

                    <div class="space-y-3">
                        @forelse($topWishlistProducts as $wItem)
                            <div class="p-3 rounded-2xl bg-amber-50/40 border border-amber-100 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-3 truncate">
                                    <div class="w-9 h-9 rounded-lg bg-white border border-gray-200 overflow-hidden shrink-0 flex items-center justify-center">
                                        @if($wItem->image_url)
                                            <img src="{{ $wItem->image_url }}" alt="" class="w-full h-full object-contain">
                                        @else
                                            <i class="fa-solid fa-box text-gray-300"></i>
                                        @endif
                                    </div>
                                    <div class="truncate">
                                        <span class="font-bold text-gray-900 block truncate">{{ $wItem->name }}</span>
                                        <span class="text-[10px] text-gray-500">₹{{ number_format($wItem->price, 2) }} • {{ $wItem->category->name ?? 'General' }}</span>
                                    </div>
                                </div>
                                <span class="font-black text-amber-700 bg-amber-100/80 px-2.5 py-1 rounded-xl text-xs shrink-0 flex items-center gap-1">
                                    <i class="fa-solid fa-heart text-amber-500 text-[10px]"></i> {{ $wItem->wishlists_count }} Saves
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-6 text-gray-400 text-xs">
                                <i class="fa-regular fa-heart text-gray-300 text-2xl block mb-1"></i>
                                <span>No items added to wishlist yet. Share your store link on WhatsApp to boost buyer interest!</span>
                            </div>
                        @endforelse
                    </div>
                </div>

                <p class="text-[11px] text-gray-400 mt-4 pt-3 border-t border-gray-100 flex items-center gap-1">
                    <i class="fa-solid fa-lightbulb text-amber-500"></i>
                    <span>High wishlist items have strong buying intent. Keeping buffer stock ensures zero loss of sales.</span>
                </p>
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
                                $pRevenue = $p->orders->sum(fn($o) => ($o->pivot->price ?? $p->price) * ($o->pivot->quantity ?? 1));
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
