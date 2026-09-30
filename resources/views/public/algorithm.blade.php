<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How VyaparIndia Works - Search Algorithm & Marketplace Discovery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">

    <!-- Top Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="h-10 w-10 rounded-2xl bg-blue-600 text-white font-black flex items-center justify-center text-lg shadow-sm">
                        V
                    </a>
                    <div>
                        <span class="font-black text-gray-900 text-lg tracking-tight">VyaparIndia</span>
                        <span class="hidden sm:inline-block ml-2 text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">Fair Algorithm & Discovery</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="text-xs font-bold text-gray-700 hover:text-blue-600 transition">
                        Home
                    </a>
                    <a href="{{ route('search') }}" class="text-xs font-bold text-gray-700 hover:text-blue-600 transition">
                        Search Products
                    </a>
                    @auth
                        <a href="{{ route('seller.dashboard') }}" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold shadow-sm">
                            Seller Hub
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold shadow-sm">
                            Register Free
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Banner -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white py-16 px-4 text-center">
        <div class="max-w-4xl mx-auto space-y-4">
            <span class="text-xs font-bold tracking-widest uppercase text-blue-300 bg-white/10 px-3 py-1 rounded-full">
                Transparency & Fair Matching
            </span>
            <h1 class="text-3xl sm:text-4xl font-black tracking-tight">How VyaparIndia's Algorithm Works</h1>
            <p class="text-sm sm:text-base text-blue-200 max-w-2xl mx-auto">
                A fair, hyper-local discovery engine designed to connect genuine local buyers with verified Indian wholesalers, manufacturers, and retailers.
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">

        <!-- 1. Who appears where? (Profile vs Mini-Website vs Dropshipping) -->
        <section class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-8 shadow-xs">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-lg">
                    1
                </div>
                <div>
                    <h2 class="text-lg font-black text-gray-900">Do All Business Tiers Appear in the Main App?</h2>
                    <p class="text-xs text-gray-500">How Profile-Only, Mini-Websites, and Dropshippers are discovered</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Profile Only -->
                <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-gray-700 uppercase">Profile-Only</span>
                            <span class="text-[10px] font-bold text-gray-500 bg-gray-200 px-2 py-0.5 rounded">Max 50 Items</span>
                        </div>
                        <h3 class="font-bold text-sm text-gray-900 mb-2">Local Shop Listing</h3>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Appears in city search and local directory. Customers can view their contact phone, business address, and up to 50 listed products with direct WhatsApp contact.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-200 text-[11px] font-bold text-blue-700">
                        Ranked by City Proximity
                    </div>
                </div>

                <!-- Mini-Website Tier -->
                <div class="bg-blue-50/50 rounded-2xl p-5 border border-blue-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-blue-800 uppercase">Mini-Website Store</span>
                            <span class="text-[10px] font-bold text-blue-700 bg-blue-100 px-2 py-0.5 rounded">Unlimited</span>
                        </div>
                        <h3 class="font-bold text-sm text-gray-900 mb-2">Verified Digital Storefront</h3>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Gets own subdomain (<code class="bg-white px-1 py-0.5 rounded text-[11px]">store.vyaparindia.online</code>), direct quick cart checkout, and **higher ranking boost** in city search and category listings.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-blue-200 text-[11px] font-bold text-blue-800">
                        Featured on Home & Category Hubs
                    </div>
                </div>

                <!-- Dropshipping Network -->
                <div class="bg-purple-50/50 rounded-2xl p-5 border border-purple-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-purple-800 uppercase">Dropship & B2B</span>
                            <span class="text-[10px] font-bold text-purple-700 bg-purple-100 px-2 py-0.5 rounded">Wholesale</span>
                        </div>
                        <h3 class="font-bold text-sm text-gray-900 mb-2">Dual Supply Network</h3>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Products appear in both retail marketplace and the **B2B Wholesale Sourcing Hub**, allowing other retailers to request partnerships and resell with automated margin splits.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-purple-200 text-[11px] font-bold text-purple-800">
                        Multi-Channel Wholesale Discovery
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. The 5 Ranking Signals -->
        <section class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-8 shadow-xs">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-lg">
                    2
                </div>
                <div>
                    <h2 class="text-lg font-black text-gray-900">The 5 Core Algorithm Ranking Signals</h2>
                    <p class="text-xs text-gray-500">How the platform decides which product appears at the top</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-start gap-4 p-4 rounded-2xl bg-gray-50 border border-gray-100">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold shrink-0 text-sm">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-gray-900">1. Hyper-Local GPS & Dispatch Proximity (Highest Weight)</h3>
                        <p class="text-xs text-gray-600 mt-1">
                            When a customer searches, sellers located within the customer's city or specified **Dispatch Radius (e.g. 25km)** appear first. This ensures same-day local dispatch, lower transport costs, and instant trust.
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-4 rounded-2xl bg-gray-50 border border-gray-100">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold shrink-0 text-sm">
                        <i class="fa-solid fa-fire"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-gray-900">2. Sales Velocity & Order Popularity</h3>
                        <p class="text-xs text-gray-600 mt-1">
                            Products with higher completed orders and positive customer reviews automatically rise to the top of category pages. High-demand items get natural organic momentum.
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-4 rounded-2xl bg-gray-50 border border-gray-100">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold shrink-0 text-sm">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-gray-900">3. Live Stock Freshness</h3>
                        <p class="text-xs text-gray-600 mt-1">
                            Products with verified in-stock quantities rank above out-of-stock listings. Newly added listings get a temporary 7-day boost so new sellers get fair initial visibility.
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-4 rounded-2xl bg-gray-50 border border-gray-100">
                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold shrink-0 text-sm">
                        <i class="fa-solid fa-certificate"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-gray-900">4. Verification & Catalog Richness</h3>
                        <p class="text-xs text-gray-600 mt-1">
                            Listings with multiple high-res photos, clear size/grade variant matrices, HSN codes, and verified GSTIN numbers receive higher quality scores.
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-4 rounded-2xl bg-gray-50 border border-gray-100">
                    <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold shrink-0 text-sm">
                        <i class="fa-solid fa-tags"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-gray-900">5. Competitive Wholesale & Retail Pricing</h3>
                        <p class="text-xs text-gray-600 mt-1">
                            Fair market pricing helps products win discovery in comparison grids. B2B buyers can filter directly by wholesale tier to get factory-direct bulk rates.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. AI Similar Products & Recommendations -->
        <section class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-8 shadow-xs">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                <div class="w-10 h-10 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-lg">
                    3
                </div>
                <div>
                    <h2 class="text-lg font-black text-gray-900">Similar Products Recommendation Engine</h2>
                    <p class="text-xs text-gray-500">How the platform recommends alternative and matching items to buyers</p>
                </div>
            </div>

            <p class="text-xs text-gray-600 leading-relaxed mb-4">
                When a customer views any product (e.g. *CPVC 1 inch Pipe at ₹250*), the algorithm automatically calculates a **Price Band (±30%)** within the same category to show:
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-purple-50/50 border border-purple-100">
                    <h4 class="font-bold text-xs text-purple-950 mb-1">🔗 Direct Category Alternatives</h4>
                    <p class="text-[11px] text-purple-800">
                        Shows other brands or grades in the exact same category (e.g. SDR 11 vs Schedule 40) so the customer can compare specifications and prices without leaving the shop.
                    </p>
                </div>
                <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-100">
                    <h4 class="font-bold text-xs text-blue-950 mb-1">🛒 Complementary Cross-Selling</h4>
                    <p class="text-[11px] text-blue-800">
                        When adding pipes, the system prompts for elbows, tees, and solvent cement, enabling contractors and plumbers to complete their entire material slip in one go.
                    </p>
                </div>
            </div>
        </section>

        <!-- 4. Seller Performance Analytics -->
        <section class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-8 shadow-xs">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-lg">
                    4
                </div>
                <div>
                    <h2 class="text-lg font-black text-gray-900">Vendor Analytics & Insights Dashboard</h2>
                    <p class="text-xs text-gray-500">Every seller gets real-time data on views, conversion, and slow-moving inventory</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                    <span class="block text-2xl font-black text-blue-600 mb-1"><i class="fa-solid fa-chart-line"></i></span>
                    <h4 class="font-bold text-xs text-gray-900">Daily & Monthly Views</h4>
                    <p class="text-[11px] text-gray-500 mt-1">Tracks how many real buyers visited each listed item today vs this month.</p>
                </div>

                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                    <span class="block text-2xl font-black text-emerald-600 mb-1"><i class="fa-solid fa-bolt"></i></span>
                    <h4 class="font-bold text-xs text-gray-900">Conversion Rate %</h4>
                    <p class="text-[11px] text-gray-500 mt-1">Measures the ratio of views that turned into confirmed WhatsApp and online orders.</p>
                </div>

                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                    <span class="block text-2xl font-black text-amber-600 mb-1"><i class="fa-solid fa-triangle-exclamation"></i></span>
                    <h4 class="font-bold text-xs text-gray-900">Slow-Moving Stock Alerts</h4>
                    <p class="text-[11px] text-gray-500 mt-1">Flags products with high views but zero orders so the vendor can optimize pricing or photos.</p>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-8 border-t border-gray-800 mt-auto text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="h-8 w-8 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-sm">V</span>
                <span class="font-bold text-white text-sm">VyaparIndia</span>
                <span>© {{ date('Y') }} All Rights Reserved.</span>
            </div>
            <div class="flex gap-6">
                <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
                <a href="{{ route('search') }}" class="hover:text-white transition">Browse Products</a>
                <a href="{{ route('marketplace.algorithm') }}" class="text-white font-bold">How Algorithm Works</a>
                <a href="{{ route('register') }}" class="hover:text-white transition">Register Shop</a>
            </div>
        </div>
    </footer>

</body>
</html>
