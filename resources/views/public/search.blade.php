<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search VyaparIndia - Verified B2B & Retail Products, Wholesalers & Dealers</title>
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
                        <a href="{{ route('home') }}" class="font-black text-gray-900 text-lg tracking-tight">VyaparIndia</a>
                        <span class="hidden sm:inline-block ml-2 text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">Search Portal</span>
                    </div>
                </div>

                <!-- Navigation Actions -->
                <div class="flex items-center gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-bold text-gray-700 hover:text-blue-600 transition">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-600/20 transition">
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

    <!-- Search Header Filter Bar -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white py-8 px-4 shadow-inner">
        <div class="max-w-7xl mx-auto">
            <form action="{{ route('search') }}" method="GET" class="bg-white p-3 rounded-2xl shadow-xl flex flex-col md:flex-row items-center gap-2 text-gray-900">
                <!-- Keyword search -->
                <div class="flex-1 flex items-center gap-2 w-full px-3 py-2 border-b md:border-b-0 md:border-r border-gray-200">
                    <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                    <input type="text" name="query" value="{{ $query ?? '' }}" placeholder="Search products, materials, plumbing, hardware..." class="w-full text-sm outline-hidden font-medium placeholder-gray-400">
                </div>

                <!-- Category search & datalist input -->
                <div class="flex items-center gap-2 w-full md:w-64 px-3 py-2 border-b md:border-b-0 md:border-r border-gray-200">
                    <i class="fa-solid fa-tags text-gray-400"></i>
                    <input list="category_datalist" name="category" value="{{ $categoryName ?? '' }}" placeholder="Type / Pick Category..." class="w-full text-xs font-bold bg-transparent outline-hidden text-gray-800 placeholder-gray-400">
                    <datalist id="category_datalist">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}">({{ $cat->products_count ?? $cat->products()->count() }} items)</option>
                        @endforeach
                    </datalist>
                </div>

                <!-- GPS / Location Input -->
                <div class="flex items-center gap-2 w-full md:w-64 px-3 py-2">
                    <i class="fa-solid fa-location-dot text-rose-500"></i>
                    <input type="text" id="location-input" name="location" value="{{ $location ?? '' }}" placeholder="City, State or Pincode" class="w-full text-xs font-medium outline-hidden">
                    <button type="button" onclick="detectGPSLocation()" title="Use GPS Location" class="px-2 py-1 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg text-xs font-bold flex items-center gap-1 transition">
                        <i class="fa-solid fa-crosshairs"></i>
                        <span>GPS</span>
                    </button>
                </div>

                <!-- Search Button -->
                <button type="submit" class="w-full md:w-auto px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider shadow-md transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-filter"></i>
                    <span>Apply Filter</span>
                </button>
            </form>

            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-xs text-blue-200">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-white">Active Filters:</span>
                    @if(!empty($query))
                        <span class="bg-white/10 px-2 py-1 rounded-md text-white">Keyword: "{{ $query }}"</span>
                    @endif
                    @if(!empty($categoryName))
                        <span class="bg-white/10 px-2 py-1 rounded-md text-amber-300"><i class="fa-solid fa-tag mr-1"></i> Category: "{{ $categoryName }}"</span>
                    @endif
                    @if(!empty($location))
                        <span class="bg-white/10 px-2 py-1 rounded-md text-emerald-300"><i class="fa-solid fa-location-dot mr-1"></i> Near: {{ $location }}</span>
                    @endif
                    @if(empty($query) && empty($location) && empty($categoryId) && empty($categoryName))
                        <span class="text-blue-300">Showing all verified suppliers & listings</span>
                    @endif
                </div>
                <div>
                    <span>Found <strong>{{ $products->total() }}</strong> Products & <strong>{{ $shops->count() }}</strong> Local Dealers</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Results Area -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

        <!-- 🏪 Verified Local Dealers & Wholesalers Section -->
        @if($shops->isNotEmpty())
            <div class="mb-10 bg-white border border-gray-200 rounded-2xl p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Verified Local Shops & Wholesalers</h2>
                            <p class="text-xs text-gray-500">Contact directly on WhatsApp or visit their digital mini-store</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                        {{ $shops->count() }} Near You
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($shops as $shop)
                        @php
                            $sp = $shop->sellerProfile;
                            $site = $shop->sellerPage;
                            $company = $sp->company_name ?? $shop->name;
                            $city = $sp->city ?? ($site->city ?? 'Local City');
                            $state = $sp->state ?? '';
                            $phone = $sp->phone ?? ($site->phone ?? '');
                            $subdomain = $site->subdomain ?? null;
                        @endphp
                        <div class="border border-gray-100 rounded-xl p-4 bg-gray-50/50 hover:bg-white hover:border-blue-300 hover:shadow-md transition">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-bold text-sm text-gray-900 flex items-center gap-1.5">
                                        {{ $company }}
                                        <i class="fa-solid fa-circle-check text-blue-500 text-xs" title="Verified Seller"></i>
                                    </h3>
                                    <p class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                        <i class="fa-solid fa-location-dot text-rose-500"></i>
                                        <span>{{ $city }} {{ $state ? ', ' . $state : '' }}</span>
                                    </p>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ $shop->business_tier === 'dropship' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ ucfirst($shop->business_tier ?? 'Dealer') }}
                                </span>
                            </div>

                            <p class="text-xs text-gray-600 mt-2 line-clamp-2">
                                {{ $sp->description ?? ($site->description ?? 'Reliable local supplier for plumbing, hardware and building materials.') }}
                            </p>

                            <div class="mt-3 pt-3 border-t border-gray-200 flex items-center gap-2">
                                @if($subdomain)
                                    <a href="{{ route('minisite.home', $subdomain) }}" target="_blank" class="flex-1 py-1.5 px-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold text-center transition flex items-center justify-center gap-1">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                        <span>Mini-Store</span>
                                    </a>
                                @endif
                                @if($phone)
                                    <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $phone) }}?text=Hello%20{{ urlencode($company) }},%20I%20found%20your%20shop%20on%20VyaparIndia" target="_blank" class="py-1.5 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold text-center transition flex items-center gap-1">
                                        <i class="fa-brands fa-whatsapp"></i>
                                        <span>Chat</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 📦 Product Results Grid -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Matching Products & Materials</h2>
                        <p class="text-xs text-gray-500">Compare prices, stock availability & delivery terms</p>
                    </div>
                </div>
            </div>

            @if($products->isEmpty())
                <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center max-w-lg mx-auto shadow-xs">
                    <div class="w-16 h-16 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center text-2xl mx-auto mb-4">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">No matching products found</h3>
                    <p class="text-xs text-gray-500 mt-1">We couldn't find any products matching your specific query and location. Try searching for a broader term or clear your location filter.</p>
                    <div class="mt-6 flex justify-center gap-3">
                        <a href="{{ route('search') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition">
                            Clear Filters
                        </a>
                        <a href="{{ route('home') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition">
                            Back to Home
                        </a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($products as $product)
                        @php
                            $seller = $product->seller;
                            $subdomain = $seller->sellerPage->subdomain ?? null;
                            $primaryImg = $product->images->first()->image_path ?? $product->image_url;
                            $imgSrc = $primaryImg 
                                ? (Str::startsWith($primaryImg, ['http://', 'https://']) ? $primaryImg : asset('storage/' . $primaryImg)) 
                                : 'https://placehold.co/400x400/e2e8f0/475569?text=No+Photo';
                        @endphp
                        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:shadow-lg transition flex flex-col group">
                            <!-- Image -->
                            <div class="relative bg-gray-100 aspect-square overflow-hidden">
                                <img src="{{ $imgSrc }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @if($product->category)
                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md bg-white/90 backdrop-blur-xs text-[10px] font-bold text-gray-800 shadow-xs">
                                        {{ $product->category->name }}
                                    </span>
                                @endif
                                @if($product->stock !== null)
                                    <span class="absolute bottom-2 right-2 px-2 py-0.5 rounded-md text-[10px] font-bold {{ $product->stock > 0 ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white' }}">
                                        {{ $product->stock > 0 ? $product->stock . ' In Stock' : 'Out of Stock' }}
                                    </span>
                                @endif
                            </div>

                            <!-- Details -->
                            <div class="p-4 flex-1 flex flex-col justify-between">
                                <div>
                                    <h4 class="font-bold text-sm text-gray-900 group-hover:text-blue-600 transition line-clamp-2">
                                        <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                                    </h4>
                                    
                                    <!-- Seller info -->
                                    <p class="text-[11px] text-gray-500 mt-1 flex items-center gap-1">
                                        <i class="fa-solid fa-store text-gray-400"></i>
                                        <span>{{ $seller->sellerProfile->company_name ?? ($seller->name ?? 'Verified Store') }}</span>
                                    </p>

                                    <!-- Price -->
                                    <div class="mt-2 flex items-baseline gap-2">
                                        <span class="text-base font-black text-gray-900">₹{{ number_format($product->price, 2) }}</span>
                                        @if($product->wholesale_price)
                                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">
                                                Wholesale: ₹{{ number_format($product->wholesale_price, 2) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-2">
                                    <a href="{{ route('product.show', $product->slug) }}" class="flex-1 py-1.5 px-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-xs font-bold text-center transition">
                                        View Details
                                    </a>
                                    @if($subdomain)
                                        <a href="{{ route('minisite.home', $subdomain) }}" class="py-1.5 px-2.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-bold transition" title="Visit Mini-Store">
                                            <i class="fa-solid fa-store"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->withQueryString()->links() }}
                </div>
            @endif
        </div>

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
                <a href="{{ route('search') }}" class="hover:text-white transition">Browse Categories</a>
                <a href="{{ route('register') }}" class="hover:text-white transition">Register Shop</a>
            </div>
        </div>
    </footer>

    <!-- GPS Geolocation Script -->
    <script>
        function detectGPSLocation() {
            if (!navigator.geolocation) {
                alert("Geolocation is not supported by your browser");
                return;
            }

            const input = document.getElementById('location-input');
            const originalVal = input.value;
            input.value = "Detecting location...";

            navigator.geolocation.getCurrentPosition(
                async function(position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    
                    try {
                        // Reverse geocode using OpenStreetMap Nominatim
                        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`);
                        const data = await response.json();
                        
                        const city = data.address.city || data.address.town || data.address.village || data.address.county || data.address.state_district || 'My Location';
                        const pincode = data.address.postcode ? ` - ${data.address.postcode}` : '';
                        input.value = `${city}${pincode}`;
                    } catch (e) {
                        input.value = `${lat.toFixed(4)}, ${lon.toFixed(4)}`;
                    }
                },
                function(error) {
                    input.value = originalVal;
                    alert("Unable to retrieve location. Please check browser permissions or type your city/pincode.");
                },
                { timeout: 10000 }
            );
        }
    </script>
</body>
</html>
