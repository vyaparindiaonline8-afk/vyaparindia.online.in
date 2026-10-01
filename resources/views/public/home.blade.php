<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VyaparIndia - India's Smart B2B & D2C Business Marketplace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">

    <!-- Top Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="h-10 w-10 rounded-2xl bg-blue-600 text-white font-black flex items-center justify-center text-lg shadow-sm">
                        V
                    </a>
                    <div>
                        <span class="font-black text-gray-900 text-lg tracking-tight">VyaparIndia</span>
                        <span class="hidden sm:inline-block ml-2 text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">B2B & D2C Network</span>
                    </div>
                </div>

                <!-- Navigation Actions -->
                <div class="flex items-center gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-bold text-gray-700 hover:text-blue-600 transition">
                            Login
                        </a>
                        <a href="{{ route('register', ['role' => 'seller']) }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-600/20 transition">
                            Seller Registration
                        </a>
                    @else
                        @if(Auth::user()->is_seller())
                            <a href="{{ route('seller.dashboard') }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md transition flex items-center gap-1.5">
                                <i class="fa-solid fa-store"></i>
                                <span>Seller Hub</span>
                            </a>
                        @elseif(Auth::user()->is_admin())
                            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl bg-gray-900 text-white text-xs font-bold">
                                Admin Hub
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-700 ml-2">
                                Logout
                            </button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Search Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-950 text-white py-12 sm:py-16 px-4 sm:px-6 relative overflow-hidden">
        <div class="max-w-5xl mx-auto space-y-6 text-center relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-xs font-bold backdrop-blur-xs text-amber-300">
                <i class="fa-solid fa-location-dot"></i>
                <span>Find Factories, Wholesalers & Hardware Stores Near You</span>
            </div>

            <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight max-w-3xl mx-auto">
                Search Products, Materials & Verified Local Dealers
            </h1>
            <p class="text-xs sm:text-base text-slate-300 max-w-2xl mx-auto">
                Apne shahar, pincode ya GPS location ke mutabiq factory rates par samaan dhoondhein aur seedhe WhatsApp ya store se order karein.
            </p>

            <!-- Comprehensive GPS & Location Search Bar -->
            <form action="{{ route('search') }}" method="GET" class="bg-white rounded-2xl sm:rounded-3xl p-2 sm:p-3 shadow-2xl max-w-4xl mx-auto text-gray-800 grid grid-cols-1 sm:grid-cols-12 gap-2 text-left">
                
                <!-- Keyword Input -->
                <div class="sm:col-span-4 relative flex items-center">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 text-gray-400 text-sm"></i>
                    <input type="text" name="query" placeholder="Product, CPVC pipe, saree..." class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>

                <!-- Category Input with Datalist -->
                <div class="sm:col-span-3">
                    <input list="home_category_datalist" name="category" placeholder="All Categories (Type / Pick)" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                    <datalist id="home_category_datalist">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}">({{ $cat->products_count }} items)</option>
                        @endforeach
                    </datalist>
                </div>

                <!-- Location / Pincode / GPS -->
                <div class="sm:col-span-3 relative flex items-center">
                    <i class="fa-solid fa-location-crosshairs absolute left-3.5 text-gray-400 text-sm"></i>
                    <input type="text" name="location" id="location_input" placeholder="City or Pincode" class="w-full pl-9 pr-8 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <button type="button" onclick="detectGPSLocation()" class="absolute right-2 text-blue-600 hover:text-blue-800 p-1" title="Detect GPS Location">
                        <i class="fa-solid fa-crosshairs text-sm" id="gps_icon"></i>
                    </button>
                </div>

                <!-- Submit Button -->
                <div class="sm:col-span-2">
                    <button type="submit" class="w-full h-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md transition flex items-center justify-center gap-1.5">
                        <span>Search</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Category Pills Bar -->
    <div class="bg-white border-b border-gray-200 py-3 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center gap-2 overflow-x-auto scrollbar-none text-xs">
            <span class="text-gray-400 font-bold uppercase tracking-wider text-[10px] shrink-0 mr-2">Top Categories:</span>
            @foreach($categories as $cat)
                <a href="{{ route('search', ['category_id' => $cat->id]) }}" class="px-3.5 py-1.5 rounded-full bg-gray-100 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 border border-gray-200 text-gray-700 font-bold whitespace-nowrap transition">
                    {{ $cat->name }}
                    @if($cat->products_count > 0)
                        <span class="ml-1 text-[10px] text-gray-400">({{ $cat->products_count }})</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12 flex-1">
        
        <!-- Featured Local Shops / Sellers Section -->
        @if($featuredSellers->isNotEmpty())
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-black text-gray-900">Verified Local Shops & Distributors</h2>
                        <p class="text-xs text-gray-500">Connect directly with factory suppliers and local hardware dealers</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($featuredSellers as $seller)
                        @php
                            $sp = $seller->sellerProfile;
                            $page = $seller->sellerPage;
                            $shopName = $page->page_title ?? $sp->company_name ?? $seller->name;
                            $city = $page->city ?? $sp->city ?? 'India';
                            $radius = $sp->dispatch_radius ?? 25;
                        @endphp
                        <div class="bg-white rounded-3xl border border-gray-200 p-5 shadow-xs hover:border-blue-400 hover:shadow-md transition flex flex-col justify-between space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="h-11 w-11 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-black text-sm shrink-0 border border-indigo-100">
                                    {{ strtoupper(substr($shopName, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-extrabold text-sm text-gray-900 truncate">{{ $shopName }}</h3>
                                    <div class="text-[11px] text-gray-500 flex items-center gap-1 mt-0.5">
                                        <i class="fa-solid fa-location-dot text-rose-500 text-[10px]"></i>
                                        <span>{{ $city }}</span>
                                        <span class="text-gray-300">•</span>
                                        <span class="text-emerald-700 font-bold">~{{ $radius }}km Delivery</span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                                @if($page)
                                    <a href="{{ route('minisite.show', $page->slug) }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                                        <span>Visit Storefront</span>
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">Verified Profile</span>
                                @endif

                                @if($page && $page->whatsapp_number)
                                    <a href="https://wa.me/{{ $page->clean_whatsapp_number }}" target="_blank" class="h-7 w-7 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center text-xs transition" title="WhatsApp Shop">
                                        <i class="fa-brands fa-whatsapp text-sm"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Trending Products Grid -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-black text-gray-900">Trending Commercial Products</h2>
                    <p class="text-xs text-gray-500">Live stock available from manufacturers and wholesalers</p>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                @forelse($products as $product)
                    <div class="bg-white rounded-2xl border border-gray-200 hover:border-gray-300 hover:shadow-lg transition-all flex flex-col overflow-hidden group">
                        
                        <!-- Image -->
                        <a href="{{ route('product.show', $product->slug) }}" class="relative block aspect-square bg-gray-100 overflow-hidden">
                            @if($product->image)
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-contain p-2 group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <i class="fa-solid fa-cube text-4xl"></i>
                                </div>
                            @endif

                            @if($product->category)
                                <span class="absolute top-2.5 left-2.5 bg-white/90 backdrop-blur-xs text-gray-800 text-[10px] font-bold px-2 py-0.5 rounded-md shadow-xs">
                                    {{ $product->category->name }}
                                </span>
                            @endif
                        </a>

                        <!-- Body -->
                        <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                            <div>
                                <a href="{{ route('product.show', $product->slug) }}" class="font-bold text-xs sm:text-sm text-gray-900 hover:text-blue-600 line-clamp-2 transition-colors">
                                    {{ $product->name }}
                                </a>
                                
                                <div class="mt-1 text-[11px] text-gray-500 flex items-center gap-1">
                                    <i class="fa-solid fa-store text-gray-400 text-[10px]"></i>
                                    <span class="truncate">{{ $product->seller->sellerProfile->company_name ?? $product->seller->name ?? 'Verified Seller' }}</span>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-gray-100 flex items-baseline justify-between">
                                <div>
                                    <span class="text-base font-black text-gray-900">₹{{ number_format($product->price, 2) }}</span>
                                    @if($product->mrp)
                                        <span class="text-[11px] text-gray-400 line-through ml-1">₹{{ number_format($product->mrp, 2) }}</span>
                                    @endif
                                </div>

                                @if($product->seller && $product->seller->sellerPage)
                                    <a href="{{ route('minisite.product', ['sellerPage' => $product->seller->sellerPage->slug, 'productSlug' => $product->slug]) }}" class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 text-[11px] font-bold transition">
                                        View
                                    </a>
                                @endif
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-gray-400">
                        <i class="fa-solid fa-box-open text-4xl mb-2"></i>
                        <p class="font-bold text-gray-600">No products listed yet</p>
                    </div>
                @endforelse
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-gray-500">
            <div>
                © {{ date('Y') }} <strong>VyaparIndia</strong>. All rights reserved. B2B, Mini-Websites & Dropshipping Ecosystem.
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" class="hover:text-gray-900">Marketplace</a>
                <a href="{{ route('login') }}" class="hover:text-gray-900">Seller Portal</a>
            </div>
        </div>
    </footer>

    <!-- GPS Auto-Detection Script -->
    <script>
        function detectGPSLocation() {
            const gpsIcon = document.getElementById('gps_icon');
            const locInput = document.getElementById('location_input');

            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser.');
                return;
            }

            gpsIcon.className = "fa-solid fa-spinner fa-spin text-blue-600 text-sm";

            navigator.geolocation.getCurrentPosition(
                async (pos) => {
                    const lat = pos.coords.latitude;
                    const lon = pos.coords.longitude;

                    try {
                        // OpenStreetMap free reverse geocoder
                        const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`);
                        const data = await res.json();
                        const city = data.address.city || data.address.town || data.address.state_district || 'My Location';
                        const postcode = data.address.postcode || '';
                        
                        locInput.value = postcode ? `${city} (${postcode})` : city;
                        gpsIcon.className = "fa-solid fa-check text-emerald-600 text-sm";
                    } catch (e) {
                        locInput.value = "Current Location";
                        gpsIcon.className = "fa-solid fa-crosshairs text-sm";
                    }
                },
                (err) => {
                    alert('Unable to retrieve location. Please type your city or pincode manually.');
                    gpsIcon.className = "fa-solid fa-crosshairs text-sm";
                },
                { timeout: 10000 }
            );
        }
    </script>
</body>
</html>
