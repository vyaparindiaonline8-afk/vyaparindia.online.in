<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800 antialiased min-h-screen">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-base shadow-sm">
                        V
                    </div>
                    <div>
                        <span class="font-black text-gray-900 text-base tracking-tight">VyaparIndia</span>
                        <span class="ml-2 text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Seller Hub</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <span class="text-xs font-semibold text-gray-600 hidden sm:inline">
                        <i class="fa-solid fa-user-circle text-gray-400 mr-1"></i> {{ Auth::user()->name }}
                    </span>
                    <a href="{{ route('home') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-900">
                        Marketplace
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-700">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Storefront Spotlight Banner -->
        <div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-xl relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-xs font-bold backdrop-blur-xs">
                    <i class="fa-solid fa-store text-emerald-400"></i>
                    <span>Your Standalone Mini-Website</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight">
                    {{ $minisite ? $minisite->page_title : 'Build Your D2C Mini-Website' }}
                </h2>
                <p class="text-xs sm:text-sm text-blue-100 leading-relaxed">
                    {{ $minisite ? 'Your custom online storefront is live with 1-click WhatsApp ordering, cart checkout, and custom branding.' : 'Create your branded online storefront in 2 minutes with custom logo, WhatsApp button, and express checkout.' }}
                </p>
                @if($minisite)
                    <div class="pt-2 flex items-center gap-2 text-xs text-blue-200 font-mono">
                        <i class="fa-solid fa-link"></i>
                        <span>{{ url('/' . $minisite->slug) }}</span>
                    </div>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0 relative z-10">
                @if($minisite)
                    <a href="{{ route('minisite.show', $minisite->slug) }}" target="_blank" class="px-5 py-3 rounded-2xl bg-white text-gray-900 font-bold text-xs shadow-md hover:bg-gray-100 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-arrow-up-right-from-square text-emerald-600"></i>
                        <span>Open Live Store</span>
                    </a>
                    <a href="{{ route('seller.minisite.edit') }}" class="px-5 py-3 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2 border border-white/20">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        <span>Customize Storefront</span>
                    </a>
                @else
                    <a href="{{ route('seller.minisite.create') }}" class="px-6 py-3.5 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-black text-xs shadow-lg transition-all flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        <span>Create Mini-Site Now</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Products</span>
                    <div class="text-2xl sm:text-3xl font-black text-gray-900 mt-1">{{ $totalProducts }}</div>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-box-open"></i>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Orders</span>
                    <div class="text-2xl sm:text-3xl font-black text-gray-900 mt-1">{{ $totalOrders }}</div>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Pending Orders</span>
                    <div class="text-2xl sm:text-3xl font-black text-amber-600 mt-1">{{ $pendingOrders }}</div>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Revenue</span>
                    <div class="text-2xl sm:text-3xl font-black text-emerald-600 mt-1">₹{{ number_format($totalRevenue, 2) }}</div>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-indian-rupee-sign"></i>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Recent Orders Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Quick Actions (1 Col) -->
            <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 space-y-4 shadow-xs">
                <h3 class="font-extrabold text-base text-gray-900 pb-3 border-b border-gray-100">
                    Seller Management
                </h3>

                <div class="space-y-2">
                    <a href="{{ route('seller.shipping.index') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-truck-fast text-emerald-600 text-lg"></i>
                            <div>
                                <div class="text-xs font-bold text-gray-900">Smart Shipping & Anti-RTO Suite</div>
                                <div class="text-[11px] text-gray-500">COD verification, live rates & 4x6 labels</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                    </a>

                    <a href="{{ route('seller.channels.index') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-colors">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-share-nodes text-blue-600 text-lg"></i>
                            <div>
                                <div class="text-xs font-bold text-gray-900">Multi-Channel Integrations</div>
                                <div class="text-[11px] text-gray-500">Shopify, WooCommerce, Amazon & Flipkart</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                    </a>

                    <a href="{{ route('seller.channels.publisher') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-colors">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-wand-magic-sparkles text-indigo-600 text-lg"></i>
                            <div>
                                <div class="text-xs font-bold text-gray-900">AI Universal Product Publisher</div>
                                <div class="text-[11px] text-gray-500">AI auto-fills SEO copy & 1-click publish</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                    </a>

                    <a href="{{ route('seller.dropship.hub') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-purple-50 hover:bg-purple-100 border border-purple-200 transition-colors">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-layer-group text-purple-600 text-lg"></i>
                            <div>
                                <div class="text-xs font-bold text-gray-900">Dropship Sourcing Hub</div>
                                <div class="text-[11px] text-gray-500">1-Click source products from wholesalers</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                    </a>

                    <a href="{{ route('seller.wholesaler.orders') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-amber-50 hover:bg-amber-100 border border-amber-200 transition-colors">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-dolly text-amber-600 text-lg"></i>
                            <div>
                                <div class="text-xs font-bold text-gray-900">Wholesale Fulfillment Requests</div>
                                <div class="text-[11px] text-gray-500">Pack, dispatch & courier tracking</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                    </a>

                    <a href="{{ route('seller.dropship.wallet') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-wallet text-emerald-600 text-lg"></i>
                            <div>
                                <div class="text-xs font-bold text-gray-900">Dropship Profit Wallet</div>
                                <div class="text-[11px] text-gray-500">Track profits & request payouts</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                    </a>

                    <a href="{{ route('seller.products.create') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-gray-50 hover:bg-gray-100 border border-gray-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-plus-circle text-blue-600 text-lg"></i>
                            <div>
                                <div class="text-xs font-bold text-gray-900">Add New Product</div>
                                <div class="text-[11px] text-gray-500">List an item in your store</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                    </a>

                    <a href="{{ route('seller.products.index') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-gray-50 hover:bg-gray-100 border border-gray-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-boxes-stacked text-purple-600 text-lg"></i>
                            <div>
                                <div class="text-xs font-bold text-gray-900">Manage Catalog</div>
                                <div class="text-[11px] text-gray-500">View & edit {{ $totalProducts }} items</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                    </a>

                    <a href="{{ route('seller.orders.index') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-gray-50 hover:bg-gray-100 border border-gray-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-truck-ramp-box text-emerald-600 text-lg"></i>
                            <div>
                                <div class="text-xs font-bold text-gray-900">Orders & Dispatches</div>
                                <div class="text-[11px] text-gray-500">Process shipping & tracking</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                    </a>

                    <a href="{{ route('seller.profile.edit') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-gray-50 hover:bg-gray-100 border border-gray-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-building text-slate-600 text-lg"></i>
                            <div>
                                <div class="text-xs font-bold text-gray-900">Business Profile</div>
                                <div class="text-[11px] text-gray-500">GST, Address & Shipping Radius</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                    </a>
                </div>
            </div>

            <!-- Recent Orders (2 Cols) -->
            <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 space-y-6 shadow-xs">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="font-extrabold text-base text-gray-900">Recent Store Orders</h3>
                    <a href="{{ route('seller.orders.index') }}" class="text-xs font-bold text-blue-600 hover:underline">
                        View All Orders &rarr;
                    </a>
                </div>

                @if($recentOrders->isEmpty())
                    <div class="p-12 text-center text-gray-400">
                        <i class="fa-solid fa-inbox text-4xl mb-3 text-gray-300"></i>
                        <p class="text-xs font-bold text-gray-700">No orders received yet</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Share your mini-storefront link to start getting orders</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="text-[11px] uppercase font-bold text-gray-500 bg-gray-50 rounded-xl">
                                <tr>
                                    <th class="p-3">Order</th>
                                    <th class="p-3">Customer</th>
                                    <th class="p-3">Total</th>
                                    <th class="p-3">Source</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($recentOrders as $ord)
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="p-3 font-mono font-bold text-gray-900">#{{ $ord->order_number ?: $ord->id }}</td>
                                        <td class="p-3 text-gray-700">{{ $ord->customer_name ?: ($ord->buyer->name ?? 'Buyer') }}</td>
                                        <td class="p-3 font-bold text-gray-900">₹{{ number_format($ord->total_price, 2) }}</td>
                                        <td class="p-3">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $ord->order_source === 'minisite' ? 'bg-purple-50 text-purple-700' : 'bg-blue-50 text-blue-700' }}">
                                                {{ ucfirst($ord->order_source ?? 'Direct') }}
                                            </span>
                                        </td>
                                        <td class="p-3">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $ord->status === 'pending' ? 'bg-amber-50 text-amber-700' : ($ord->status === 'delivered' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700') }}">
                                                {{ ucfirst($ord->status) }}
                                            </span>
                                        </td>
                                        <td class="p-3 text-right">
                                            <a href="{{ route('seller.orders.show', $ord->id) }}" class="text-blue-600 hover:text-blue-800 font-bold">
                                                Manage &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
