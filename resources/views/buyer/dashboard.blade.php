<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Dashboard - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-gray-800 antialiased min-h-screen flex flex-col justify-between">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                        <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black flex items-center justify-center text-base shadow-sm">
                            V
                        </div>
                        <div>
                            <span class="font-extrabold text-gray-900 text-base tracking-tight">Vyapar<span class="text-blue-600">India</span></span>
                            <span class="ml-2 text-[10px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded-full">Buyer Hub</span>
                        </div>
                    </a>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" class="text-xs font-semibold text-gray-600 hover:text-blue-600 flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-store"></i>
                        <span>Marketplace</span>
                    </a>
                    <a href="{{ route('buyer.orders.index') }}" class="text-xs font-semibold text-gray-600 hover:text-blue-600 flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-box-open"></i>
                        <span>My Orders</span>
                    </a>
                    <div class="h-4 w-[1px] bg-gray-200"></div>
                    <span class="text-xs font-semibold text-gray-700 hidden sm:inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-user text-blue-600 text-sm"></i>
                        <span>{{ Auth::user()->name }}</span>
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-700 hover:bg-rose-50 px-2.5 py-1.5 rounded-lg transition flex items-center gap-1">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8 flex-1 w-full">
        
        <!-- Welcome Hero Banner -->
        <div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-xl relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-xs font-bold backdrop-blur-xs">
                    <i class="fa-solid fa-badge-check text-emerald-400"></i>
                    <span>Verified Buyer Account</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                    Welcome, {{ Auth::user()->name }} 👋
                </h1>
                <p class="text-xs sm:text-sm text-blue-100 leading-relaxed">
                    Source wholesale goods directly from manufacturers, track order shipments, request custom rate quotes, and manage your commercial purchases.
                </p>
                <div class="pt-2 flex flex-wrap items-center gap-3">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-blue-700 hover:bg-blue-50 font-bold text-xs shadow-md transition transform hover:-translate-y-0.5">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>Explore Marketplace</span>
                    </a>
                    <a href="{{ route('buyer.orders.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs border border-white/20 backdrop-blur-xs transition">
                        <i class="fa-solid fa-truck-fast"></i>
                        <span>Track My Orders</span>
                    </a>
                </div>
            </div>

            <!-- Profile Summary Card -->
            <div class="relative z-10 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-5 text-xs space-y-2.5 w-full md:w-72">
                <div class="text-[11px] font-bold uppercase tracking-wider text-blue-200">Account Details</div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-300">Name:</span>
                    <span class="font-bold text-white">{{ Auth::user()->name }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-300">Email:</span>
                    <span class="font-mono text-white text-[11px] truncate max-w-[150px]" title="{{ Auth::user()->email }}">{{ Auth::user()->email }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-300">Role:</span>
                    <span class="px-2 py-0.5 rounded-full bg-blue-500/30 text-blue-200 font-bold text-[10px]">Buyer (व्यापारी)</span>
                </div>
                <div class="pt-2 border-t border-white/10">
                    <span class="text-gray-300">Need to sell products?</span>
                    <a href="{{ route('register') }}?role=seller" class="block mt-1 text-emerald-300 hover:underline font-bold">
                        Create Seller Store &rarr;
                    </a>
                </div>
            </div>

            <!-- Background decorative circles -->
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/3 -top-10 w-48 h-48 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none"></div>
        </div>

        <!-- Quick Action Tiles Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Card 1: Wholesale Marketplace -->
            <a href="{{ route('home') }}" class="group bg-white p-5 rounded-2xl border border-gray-200 hover:border-blue-500 hover:shadow-lg transition-all duration-200 flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <span class="text-xs text-blue-600 font-bold group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
                <div class="mt-4">
                    <h3 class="font-bold text-gray-900 text-sm">Wholesale Marketplace</h3>
                    <p class="text-gray-500 text-xs mt-1">Browse 1000+ verified factories, dealers & wholesale prices.</p>
                </div>
            </a>

            <!-- Card 2: My Orders -->
            <a href="{{ route('buyer.orders.index') }}" class="group bg-white p-5 rounded-2xl border border-gray-200 hover:border-indigo-500 hover:shadow-lg transition-all duration-200 flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-cart-flatbed"></i>
                    </div>
                    <span class="text-xs text-indigo-600 font-bold group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
                <div class="mt-4">
                    <h3 class="font-bold text-gray-900 text-sm">My Wholesale Orders</h3>
                    <p class="text-gray-500 text-xs mt-1">View invoices, order dispatch tracking, and delivery status.</p>
                </div>
            </a>

            <!-- Card 3: Wishlist / Saved Items -->
            @if(\Illuminate\Support\Facades\Route::has('wishlist.index'))
            <a href="{{ route('wishlist.index') }}" class="group bg-white p-5 rounded-2xl border border-gray-200 hover:border-rose-500 hover:shadow-lg transition-all duration-200 flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-heart"></i>
                    </div>
                    <span class="text-xs text-rose-600 font-bold group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
                <div class="mt-4">
                    <h3 class="font-bold text-gray-900 text-sm">Saved Wishlist</h3>
                    <p class="text-gray-500 text-xs mt-1">Shortlisted products and suppliers saved for later purchase.</p>
                </div>
            </a>
            @else
            <div class="bg-white p-5 rounded-2xl border border-gray-200 flex flex-col justify-between opacity-80">
                <div class="w-12 h-12 rounded-xl bg-gray-50 text-gray-500 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-shield-heart"></i>
                </div>
                <div class="mt-4">
                    <h3 class="font-bold text-gray-900 text-sm">Direct Manufacturer Rates</h3>
                    <p class="text-gray-500 text-xs mt-1">Get bulk discounts directly without middleman markups.</p>
                </div>
            </div>
            @endif

            <!-- Card 4: Switch to Seller Account -->
            <a href="{{ route('register') }}?role=seller" class="group bg-gradient-to-br from-emerald-500 to-teal-700 text-white p-5 rounded-2xl shadow-md hover:shadow-lg transition-all duration-200 flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 rounded-xl bg-white/20 text-white flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-shop"></i>
                    </div>
                    <span class="text-xs text-white font-bold group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
                <div class="mt-4">
                    <h3 class="font-bold text-white text-sm">Become a Seller</h3>
                    <p class="text-emerald-100 text-xs mt-1">Have products to sell? Create a store & list brand catalogs.</p>
                </div>
            </a>
        </div>

        <!-- Recent Activity Section -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Recent Commercial Orders</h2>
                    <p class="text-xs text-gray-500">Track and review your purchases</p>
                </div>
                <a href="{{ route('buyer.orders.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                    View All &rarr;
                </a>
            </div>

            <div class="p-8 text-center border-2 border-dashed border-gray-200 rounded-xl">
                <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center mx-auto mb-3 text-lg">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
                <h4 class="text-sm font-bold text-gray-700">No orders placed yet</h4>
                <p class="text-xs text-gray-500 max-w-sm mx-auto mt-1">
                    Explore products from verified suppliers across India and place wholesale orders with guaranteed dispatch.
                </p>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 transition">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Browse Products</span>
                </a>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-500">
        <div class="max-w-7xl mx-auto px-4">
            <p>&copy; {{ date('Y') }} VyaparIndia.online - India's Smart B2B & D2C Business Marketplace</p>
        </div>
    </footer>

</body>
</html>
