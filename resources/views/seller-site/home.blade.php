@extends('seller-site.layout')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gray-900 text-white overflow-hidden group/hero" id="hero-banner-section">
    @if($sellerPage->banner_image_url)
        <div class="absolute inset-0 z-0">
            <img src="{{ $sellerPage->banner_image_url }}" alt="{{ $sellerPage->page_title }}" id="live_hero_banner_img" class="w-full h-full object-cover opacity-40 filter blur-xs transition duration-500">
            <div class="absolute inset-0 bg-gradient-to-r from-gray-950 via-gray-900/90 to-transparent"></div>
        </div>
    @else
        <div class="absolute inset-0 z-0 bg-gradient-to-r from-gray-950 via-gray-900 to-gray-800" id="live_hero_banner_img"></div>
    @endif

    <!-- 📷 Store Owner Quick Banner Swapper Button -->
    @if(Auth::check() && Auth::id() === $sellerPage->user_id)
        <div class="absolute top-4 right-4 z-20">
            <button type="button" onclick="openBannerSwapper()" class="px-3.5 py-2 rounded-xl bg-slate-900/90 hover:bg-slate-900 text-white font-black text-xs backdrop-blur-md border border-amber-400/50 shadow-xl flex items-center gap-2 transition transform hover:scale-105" title="Change Hero Banner directly on this page">
                <i class="fa-solid fa-camera text-amber-400"></i>
                <span>बैनर बदलें / Change Banner</span>
            </button>
        </div>
    @endif

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
        <div class="max-w-2xl space-y-4">
            @if($sellerPage->tagline)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-brand-custom text-white shadow-xs">
                    <i class="fa-solid fa-sparkles text-[10px]"></i>
                    {{ $sellerPage->tagline }}
                </span>
            @endif

            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight">
                {{ $sellerPage->page_title }}
            </h1>

            @if($sellerPage->dealership_text)
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-blue-900/60 border border-blue-400/40 text-blue-200 text-xs font-bold">
                    <i class="fa-solid fa-certificate text-amber-400"></i>
                    <span>{{ $sellerPage->dealership_text }}</span>
                </div>
            @endif

            <p class="text-sm sm:text-base text-gray-300 leading-relaxed">
                {{ $sellerPage->welcome_message ?: 'Welcome to our official direct-to-consumer store. Browse quality products delivered right to your doorstep.' }}
            </p>

            <div class="pt-4 flex flex-wrap items-center gap-3">
                <a href="#products-grid" class="px-6 py-3 rounded-xl bg-white text-gray-900 font-bold text-sm hover:bg-gray-100 transition-colors shadow-sm">
                    Browse Catalog
                </a>
                @if($sellerPage->whatsapp_number)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sellerPage->whatsapp_number) }}" target="_blank" class="px-6 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm transition-colors shadow-sm flex items-center gap-2">
                        <i class="fa-brands fa-whatsapp text-base"></i>
                        <span>Chat on WhatsApp</span>
                    </a>
                @endif
                @if(Auth::check() && Auth::id() === $sellerPage->user_id)
                    <button type="button" onclick="openBannerSwapper()" class="px-4 py-3 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-amber-300 font-bold text-xs border border-amber-400/30 flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-image"></i>
                        <span>बैनर बदलें</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Trust Badges Bar & Local Delivery Radius with Coverage Check Modal Button -->
<div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-blue-700 text-white text-xs sm:text-sm font-bold py-2.5 px-4 shadow-sm">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2.5">
        <div class="flex items-center gap-2 text-center sm:text-left">
            <i class="fa-solid fa-truck-fast text-amber-300 text-base"></i>
            <span>🚚 Direct Local Delivery available within <strong>{{ $sellerPage->dispatch_radius ?: 500 }} KM</strong> of {{ $sellerPage->city ?: 'our warehouse' }}! Prompt site dispatch.</span>
        </div>
        <button type="button" onclick="openDeliveryCoverageModal()" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white/20 hover:bg-white text-white hover:text-slate-900 font-extrabold text-xs transition backdrop-blur-xs border border-white/30 shadow-xs shrink-0">
            <i class="fa-solid fa-location-crosshairs text-amber-300"></i>
            <span>📍 डिलीवरी दायरा व दूरी जांचें (Check Coverage)</span>
        </button>
    </div>
</div>

<section class="border-b border-gray-200 bg-white py-4 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs font-medium text-gray-600">
            <div class="flex items-center justify-center sm:justify-start gap-2.5">
                <i class="fa-solid fa-truck-fast text-brand-custom text-base"></i>
                @if($sellerPage->dispatch_radius)
                    <span class="font-bold text-gray-800">{{ $sellerPage->dispatch_radius }} KM Local Radius</span>
                @else
                    <span>Fast Express Delivery</span>
                @endif
            </div>
            <div class="flex items-center justify-center sm:justify-start gap-2.5">
                <i class="fa-solid fa-shield-check text-brand-custom text-base"></i>
                <span>100% Genuine Products</span>
            </div>
            <div class="flex items-center justify-center sm:justify-start gap-2.5">
                <i class="fa-solid fa-hand-holding-dollar text-brand-custom text-base"></i>
                <span>Cash on Delivery (COD)</span>
            </div>
            <div class="flex items-center justify-center sm:justify-start gap-2.5">
                <i class="fa-solid fa-star text-amber-500 text-base"></i>
                <span>4.8★ Verified Buyer Ratings</span>
            </div>
        </div>
    </div>
</section>

<!-- 🏢 Authorized Brands & Official Dealerships Showcase -->
@if(!empty($authorizedBrands) && count($authorizedBrands) > 0)
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-4">
    <div class="bg-gradient-to-br from-white to-gray-50 rounded-3xl border border-gray-200/90 p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h2 class="text-base sm:text-lg font-black text-gray-900 uppercase tracking-tight">
                        Authorized Brands & Dealerships • हमारे अधिकृत ब्रांड्स
                    </h2>
                </div>
                <p class="text-xs text-gray-500 mt-0.5">We are official authorized distributors and dealers for top verified brands.</p>
            </div>
            <a href="{{ route('minisite.products', $sellerPage->slug) }}" class="text-xs font-black text-indigo-600 hover:text-indigo-800 flex items-center gap-1 shrink-0">
                <span>View Brand Catalog</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach($authorizedBrands as $brand)
                @php
                    $bName = is_array($brand) ? ($brand['name'] ?? '') : $brand;
                    $bLogo = is_array($brand) ? ($brand['logo'] ?? '') : '';
                    $bTag = is_array($brand) ? ($brand['tag'] ?? 'Authorized Dealer') : 'Authorized Dealer';
                @endphp
                @if(!empty($bName))
                    <a href="{{ route('minisite.products', ['sellerPage' => $sellerPage->slug, 'brand' => $bName]) }}" class="group bg-white rounded-2xl border border-gray-200 hover:border-indigo-400 p-4 flex flex-col items-center justify-center text-center transition-all hover:shadow-md">
                        <div class="h-12 w-28 flex items-center justify-center mb-2.5">
                            @if(!empty($bLogo))
                                <img src="{{ $bLogo }}" alt="{{ $bName }}" class="max-h-full max-w-full object-contain crisp-img group-hover:scale-105 transition-transform">
                            @else
                                <div class="h-10 w-10 rounded-xl bg-indigo-600 text-white font-black text-base flex items-center justify-center shadow-xs">
                                    {{ substr($bName, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <span class="text-xs font-black text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $bName }}</span>
                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full mt-1">{{ $bTag }}</span>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- 📂 Hierarchical Group -> Category -> Brand Explorer -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-2">
    <div class="bg-white rounded-3xl border border-gray-200/90 p-5 sm:p-7 shadow-2xs">
        <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-5">
            <div>
                <h3 class="text-sm sm:text-base font-black text-gray-900 flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-indigo-600"></span>
                    <span>Browse by Group & Category (ग्रुप व कैटेगरी अनुसार उत्पाद देखें)</span>
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">Explore our wide selection structured by Department, Category and Brand</p>
            </div>
            <a href="{{ route('minisite.products', $sellerPage->slug) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                <span>View Full Catalog</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <!-- Department / Group Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-4">
            <a href="{{ route('minisite.products', ['sellerPage' => $sellerPage->slug, 'search' => 'commode']) }}" class="group p-3.5 sm:p-4 rounded-2xl bg-gradient-to-br from-indigo-50/70 to-blue-50/70 border border-indigo-100 hover:border-indigo-400 hover:shadow-sm transition">
                <div class="h-9 w-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-base mb-2 group-hover:scale-105 transition">
                    <i class="fa-solid fa-toilet"></i>
                </div>
                <div class="font-black text-xs text-gray-900 group-hover:text-indigo-600 transition">Sanitaryware</div>
                <div class="text-[10px] text-gray-500 mt-0.5">One-Piece, Wall Hung, Pan</div>
            </a>

            <a href="{{ route('minisite.products', ['sellerPage' => $sellerPage->slug, 'search' => 'basin']) }}" class="group p-3.5 sm:p-4 rounded-2xl bg-gradient-to-br from-emerald-50/70 to-teal-50/70 border border-emerald-100 hover:border-emerald-400 hover:shadow-sm transition">
                <div class="h-9 w-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-base mb-2 group-hover:scale-105 transition">
                    <i class="fa-solid fa-sink"></i>
                </div>
                <div class="font-black text-xs text-gray-900 group-hover:text-emerald-700 transition">Wash Basins</div>
                <div class="text-[10px] text-gray-500 mt-0.5">Table Top, Pedestal & Bowl</div>
            </a>

            <a href="{{ route('minisite.products', ['sellerPage' => $sellerPage->slug, 'search' => 'cock']) }}" class="group p-3.5 sm:p-4 rounded-2xl bg-gradient-to-br from-cyan-50/70 to-sky-50/70 border border-cyan-100 hover:border-cyan-400 hover:shadow-sm transition">
                <div class="h-9 w-9 rounded-xl bg-cyan-600 text-white flex items-center justify-center text-base mb-2 group-hover:scale-105 transition">
                    <i class="fa-solid fa-faucet-drip"></i>
                </div>
                <div class="font-black text-xs text-gray-900 group-hover:text-cyan-700 transition">Bath Fittings & Taps</div>
                <div class="text-[10px] text-gray-500 mt-0.5">Diverter, Bib Cock, Showers</div>
            </a>

            <a href="{{ route('minisite.products', ['sellerPage' => $sellerPage->slug, 'group' => 'Plumbing']) }}" class="group p-3.5 sm:p-4 rounded-2xl bg-gradient-to-br from-amber-50/70 to-orange-50/70 border border-amber-100 hover:border-amber-400 hover:shadow-sm transition">
                <div class="h-9 w-9 rounded-xl bg-amber-600 text-white flex items-center justify-center text-base mb-2 group-hover:scale-105 transition">
                    <i class="fa-solid fa-wrench"></i>
                </div>
                <div class="font-black text-xs text-gray-900 group-hover:text-amber-700 transition">Pipes & Plumbing</div>
                <div class="text-[10px] text-gray-500 mt-0.5">CPVC, UPVC, SWR & Fittings</div>
            </a>
        </div>
    </div>
</section>

<!-- Products Showcase -->
<section id="products-grid" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900">Featured Products</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Showing trending verified items ready for immediate dispatch</p>
        </div>
        <a href="{{ route('minisite.products', $sellerPage->slug) }}" class="text-xs font-bold text-brand-custom hover:underline flex items-center gap-1">
            <span>View All ({{ $totalProducts }})</span>
            <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
    </div>

    @if($products->isEmpty())
        <div class="text-center py-16 bg-white rounded-3xl border border-gray-200">
            <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center text-gray-400 text-2xl mb-4">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <h3 class="text-base font-bold text-gray-900">No products published yet</h3>
            <p class="text-xs text-gray-500 max-w-sm mx-auto mt-1">This seller is currently setting up their catalog. Please check back soon!</p>
        </div>
    @else
            @php
                $cardComponent = isset($businessModule) ? $businessModule->getCardComponent() : 'seller-site.modules.card_hardware_pipes';
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4 md:gap-6">
                @foreach($products as $product)
                    @include($cardComponent, ['product' => $product, 'sellerPage' => $sellerPage])
                @endforeach
            </div>
    @endif
</section>

<!-- 🏪 Showroom & Warehouse Photos Scroller -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-3xl border border-gray-200/90 p-6 sm:p-8 shadow-2xs">
        <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
            <div>
                <h3 class="text-base sm:text-lg font-black text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-store text-indigo-600"></i>
                    <span>Our Showroom & Warehouse (दुकान व गोदाम की झलक)</span>
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">Physical store presence, counter displays, and ready dispatch warehouse inventory</p>
            </div>
            @if(Auth::check() && Auth::id() === $sellerPage->user_id)
                <button type="button" onclick="openShopPhotoModal()" class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center gap-1.5 shadow-xs transition">
                    <i class="fa-solid fa-camera text-amber-400"></i>
                    <span>+ Add Shop Photo</span>
                </button>
            @endif
        </div>

        @php
            $shopPhotos = $sellerPage->shop_photos ?? [];
            if (empty($shopPhotos)) {
                // Default high-grade showroom images
                $shopPhotos = [
                    'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1620626011761-996317b8d101?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1507652313519-d4e9174996dd?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1581094794329-c8112a89af12?auto=format&fit=crop&w=800&q=80',
                ];
            }
        @endphp

        <div class="flex items-center gap-4 overflow-x-auto pb-3 snap-x no-scrollbar" id="shop-photos-scroller">
            @foreach($shopPhotos as $idx => $pUrl)
                <div class="snap-start shrink-0 w-64 sm:w-80 aspect-4/3 rounded-2xl overflow-hidden border border-gray-200 relative group shadow-2xs">
                    <img src="{{ $pUrl }}" alt="Store Photo {{ $idx + 1 }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-950/80 via-transparent to-transparent flex items-end p-3.5">
                        <span class="text-white text-xs font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check text-emerald-400 text-[10px]"></i>
                            <span>Verified Store Facility</span>
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- About Brand Section -->
@if($sellerPage->about_text || $sellerPage->welcome_message)
    <section class="bg-gray-100 border-y border-gray-200 py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-xs font-bold uppercase tracking-wider text-brand-custom">About Our Store</span>
            <h2 class="text-2xl font-extrabold text-gray-900 mt-1">Why Shop with {{ $sellerPage->page_title }}</h2>
            <p class="mt-4 text-sm text-gray-600 leading-relaxed max-w-3xl mx-auto">
                {{ $sellerPage->about_text ?: $sellerPage->welcome_message }}
            </p>
            @if($sellerPage->address || $sellerPage->city)
                <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                    <div class="inline-flex items-center gap-2 text-xs font-medium text-gray-700 bg-white px-4 py-2 rounded-full border border-gray-200 shadow-xs">
                        <i class="fa-solid fa-location-dot text-rose-500"></i>
                        <span>{{ $sellerPage->address ? $sellerPage->address . ', ' : '' }}{{ $sellerPage->city }}{{ $sellerPage->pincode ? ' - ' . $sellerPage->pincode : '' }}</span>
                    </div>

                    @php
                        $mapUrl = $sellerPage->google_map_link ?: ('https://www.google.com/maps/search/?api=1&query=' . urlencode($sellerPage->page_title . ' ' . $sellerPage->address . ' ' . $sellerPage->city));
                    @endphp
                    <a href="{{ $mapUrl }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-full border border-blue-200 shadow-xs transition">
                        <i class="fa-solid fa-map-location-dot text-blue-600"></i>
                        <span>📍 Open GPS on Google Maps</span>
                    </a>

                    @if($sellerPage->google_review_link)
                        <a href="{{ $sellerPage->google_review_link }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-800 bg-amber-50 hover:bg-amber-100 px-4 py-2 rounded-full border border-amber-200 shadow-xs transition">
                            <i class="fa-brands fa-google text-amber-500"></i>
                            <span>Google Verified & Reviews</span>
                        </a>
                    @endif
                </div>
            @endif

            <!-- 💳 Direct Payment & Bank Account Details Box -->
            @if(!empty($sellerPage->upi_id) || !empty($sellerPage->bank_account_number))
                <div class="mt-8 max-w-xl mx-auto bg-white rounded-2xl border border-gray-200 p-5 shadow-xs text-left">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-3">
                        <div class="flex items-center gap-2">
                            <div class="h-7 w-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-building-columns"></i>
                            </div>
                            <span class="text-xs font-black text-gray-900">Official Wholesale Payment Details</span>
                        </div>
                        <span class="text-[10px] font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">100% Verified Account</span>
                    </div>

                    @if($sellerPage->upi_id)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-emerald-50/60 border border-emerald-200 mb-3">
                            <div>
                                <span class="text-[10px] font-bold text-emerald-800 uppercase block">Storefront UPI ID</span>
                                <span class="text-xs font-mono font-black text-gray-900">{{ $sellerPage->upi_id }}</span>
                            </div>
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $sellerPage->upi_id }}'); alert('UPI ID copied!')" class="px-2.5 py-1 rounded-lg bg-white border border-emerald-300 hover:bg-emerald-100 text-emerald-800 text-[10px] font-bold transition">
                                <i class="fa-solid fa-copy"></i> Copy
                            </button>
                        </div>
                    @endif

                    @if($sellerPage->bank_account_number)
                        <div class="grid grid-cols-2 gap-2 text-[11px] text-gray-600 bg-gray-50 p-3 rounded-xl border border-gray-200 font-mono">
                            <div><span class="text-gray-400 block text-[9px] font-sans uppercase">Bank Name</span><strong class="text-gray-900 font-bold">{{ $sellerPage->bank_name ?: 'Bank Account' }}</strong></div>
                            <div><span class="text-gray-400 block text-[9px] font-sans uppercase">Account No</span><strong class="text-gray-900 font-bold">{{ $sellerPage->bank_account_number }}</strong></div>
                            <div><span class="text-gray-400 block text-[9px] font-sans uppercase">IFSC Code</span><strong class="text-gray-900 font-bold">{{ $sellerPage->bank_ifsc ?: 'N/A' }}</strong></div>
                            <div><span class="text-gray-400 block text-[9px] font-sans uppercase">Account Holder</span><strong class="text-gray-900 font-bold">{{ $sellerPage->bank_account_holder ?: $sellerPage->page_title }}</strong></div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Social Links -->
            @if($sellerPage->instagram_link || $sellerPage->facebook_link || $sellerPage->youtube_link)
                <div class="mt-6 flex items-center justify-center gap-4 text-lg">
                    @if($sellerPage->instagram_link)
                        <a href="{{ $sellerPage->instagram_link }}" target="_blank" class="h-10 w-10 rounded-full bg-white text-rose-600 flex items-center justify-center border border-gray-200 shadow-xs hover:scale-110 transition">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                    @endif
                    @if($sellerPage->facebook_link)
                        <a href="{{ $sellerPage->facebook_link }}" target="_blank" class="h-10 w-10 rounded-full bg-white text-blue-600 flex items-center justify-center border border-gray-200 shadow-xs hover:scale-110 transition">
                            <i class="fa-brands fa-facebook"></i>
                        </a>
                    @endif
                    @if($sellerPage->youtube_link)
                        <a href="{{ $sellerPage->youtube_link }}" target="_blank" class="h-10 w-10 rounded-full bg-white text-red-600 flex items-center justify-center border border-gray-200 shadow-xs hover:scale-110 transition">
                            <i class="fa-brands fa-youtube"></i>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </section>
@endif

<!-- 📷 1-Click Banner Swapper Modal for Store Owner -->
<div id="banner-swapper-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-gray-200 animate-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <h3 class="text-sm font-black text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-camera text-indigo-600"></i>
                <span>Change Hero Banner Image (बैनर बदलें)</span>
            </h3>
            <button type="button" onclick="closeBannerSwapper()" class="text-gray-400 hover:text-gray-600 text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="banner-swapper-form" onsubmit="submitBannerSwap(event)" class="mt-4 space-y-4">
            <!-- Preset Luxury Templates -->
            <div>
                <label class="block text-[11px] font-black text-gray-700 uppercase tracking-wider mb-1.5">
                    1. Or Choose a Curated HD Template (तैयार टेम्पलेट चुनें):
                </label>
                <div class="grid grid-cols-2 gap-2 text-left">
                    <button type="button" onclick="selectBannerTemplate('https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=1400&q=80')" class="p-2 rounded-xl border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50/50 flex items-center gap-2 text-xs font-bold text-gray-800 transition">
                        <span>🚽 Luxury Sanitaryware</span>
                    </button>
                    <button type="button" onclick="selectBannerTemplate('https://images.unsplash.com/photo-1620626011761-996317b8d101?auto=format&fit=crop&w=1400&q=80')" class="p-2 rounded-xl border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50/50 flex items-center gap-2 text-xs font-bold text-gray-800 transition">
                        <span>🧱 Designer Tiles & Marble</span>
                    </button>
                    <button type="button" onclick="selectBannerTemplate('https://images.unsplash.com/photo-1581094794329-c8112a89af12?auto=format&fit=crop&w=1400&q=80')" class="p-2 rounded-xl border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50/50 flex items-center gap-2 text-xs font-bold text-gray-800 transition">
                        <span>🔧 CPVC Pipes & Hardware</span>
                    </button>
                    <button type="button" onclick="selectBannerTemplate('https://images.unsplash.com/photo-1507652313519-d4e9174996dd?auto=format&fit=crop&w=1400&q=80')" class="p-2 rounded-xl border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50/50 flex items-center gap-2 text-xs font-bold text-gray-800 transition">
                        <span>🚰 Modern Bath Fittings</span>
                    </button>
                </div>
            </div>

            <!-- Paste Image URL -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">2. Paste Image URL (वेब लिंक डालें):</label>
                <input type="url" id="swap_banner_url" placeholder="https://..." class="w-full px-3 py-2 text-xs rounded-xl border border-gray-300 focus:outline-hidden focus:border-indigo-500">
            </div>

            <!-- Upload File -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">3. Or Upload from Phone/Computer (फ़ाइल चुनें):</label>
                <input type="file" id="swap_banner_file" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="closeBannerSwapper()" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs">Cancel</button>
                <button type="submit" id="btn_submit_banner" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-check"></i>
                    <span>Apply Banner</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 🚚 Delivery Coverage & Distance Checker Modal -->
<div id="delivery-coverage-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-200 animate-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <h3 class="text-sm font-black text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-truck-fast text-indigo-600"></i>
                <span>Delivery Coverage & Radius (डिलीवरी दायरा)</span>
            </h3>
            <button type="button" onclick="closeDeliveryCoverageModal()" class="text-gray-400 hover:text-gray-600 text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="mt-4 space-y-4">
            <div class="p-3.5 rounded-2xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200">
                <div class="flex items-center justify-between text-xs font-bold text-blue-900 mb-1">
                    <span>Store Dispatch Hub:</span>
                    <span class="font-black">{{ $sellerPage->city ?: 'Our Central Warehouse' }}</span>
                </div>
                <div class="flex items-center justify-between text-xs font-bold text-blue-900">
                    <span>Guaranteed Direct Radius:</span>
                    <span class="font-black text-indigo-700">{{ $sellerPage->dispatch_radius ?: 500 }} KM</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Check Distance from Your City / PIN Code:</label>
                <div class="flex items-center gap-2">
                    <input type="text" id="buyer_location_input" placeholder="Enter your City or Pincode" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-300 font-bold focus:border-indigo-500">
                    <button type="button" onclick="checkBuyerCoverage()" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shrink-0">
                        Check
                    </button>
                </div>
            </div>

            <div id="coverage_result_box" class="hidden p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold">
                <!-- Result dynamically rendered -->
            </div>

            @if($sellerPage->whatsapp_number)
                <a href="https://wa.me/{{ $sellerPage->clean_whatsapp_number }}?text={{ urlencode('Namaste! Mujhe apne site par delivery ke bare me puchhna hai.') }}" target="_blank" class="w-full py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-sm transition">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>Inquire for Vehicle Dispatch on WhatsApp</span>
                </a>
            @endif
        </div>
    </div>
</div>

<!-- 🏪 1-Click Shop Photo Modal -->
<div id="shop-photo-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-200 animate-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <h3 class="text-sm font-black text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-camera text-indigo-600"></i>
                <span>Add Shop / Showroom Photo (दुकान की फोटो जोड़ें)</span>
            </h3>
            <button type="button" onclick="closeShopPhotoModal()" class="text-gray-400 hover:text-gray-600 text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="shop-photo-form" onsubmit="submitShopPhoto(event)" class="mt-4 space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Paste Image URL:</label>
                <input type="url" id="shop_photo_url" placeholder="https://..." class="w-full px-3 py-2 text-xs rounded-xl border border-gray-300">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Or Choose Photo File:</label>
                <input type="file" id="shop_photo_file" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700">
            </div>
            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="closeShopPhotoModal()" class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 font-bold text-xs">Cancel</button>
                <button type="submit" id="btn_submit_shop_photo" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs">
                    Upload & Add
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openBannerSwapper() {
        document.getElementById('banner-swapper-modal').classList.remove('hidden');
    }
    function closeBannerSwapper() {
        document.getElementById('banner-swapper-modal').classList.add('hidden');
    }
    function selectBannerTemplate(url) {
        document.getElementById('swap_banner_url').value = url;
    }

    function submitBannerSwap(e) {
        e.preventDefault();
        const urlInput = document.getElementById('swap_banner_url');
        const fileInput = document.getElementById('swap_banner_file');
        const submitBtn = document.getElementById('btn_submit_banner');

        const formData = new FormData();
        if (fileInput.files.length > 0) {
            formData.append('banner_image', fileInput.files[0]);
        } else if (urlInput.value.trim()) {
            formData.append('banner_url', urlInput.value.trim());
        } else {
            alert('Please select a template, enter URL or choose a photo file.');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Updating...';

        fetch("{{ route('seller.minisite.quick_banner_update') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Apply Banner';
            if (data.success) {
                closeBannerSwapper();
                const bannerEl = document.getElementById('live_hero_banner_img');
                if (bannerEl) {
                    if (bannerEl.tagName.toLowerCase() === 'img') {
                        bannerEl.src = data.banner_url;
                    } else {
                        bannerEl.innerHTML = `<img src="${data.banner_url}" class="w-full h-full object-cover opacity-40 filter blur-xs">`;
                    }
                }
                alert(data.message || 'Banner updated successfully!');
            } else {
                alert(data.message || 'Failed to update banner.');
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Apply Banner';
            alert('Error updating banner: ' + err.message);
        });
    }

    function openDeliveryCoverageModal() {
        document.getElementById('delivery-coverage-modal').classList.remove('hidden');
    }
    function closeDeliveryCoverageModal() {
        document.getElementById('delivery-coverage-modal').classList.add('hidden');
    }
    function checkBuyerCoverage() {
        const inp = document.getElementById('buyer_location_input').value.trim();
        const resBox = document.getElementById('coverage_result_box');
        if (!inp) {
            alert('Please enter your city or pincode.');
            return;
        }
        resBox.classList.remove('hidden');
        resBox.innerHTML = `✅ Direct Transport & Doorstep Delivery is Available for <strong>${inp}</strong> within our {{ $sellerPage->dispatch_radius ?: 500 }} KM delivery zone! Estimated delivery within 24-48 hours.`;
    }

    function openShopPhotoModal() {
        document.getElementById('shop-photo-modal').classList.remove('hidden');
    }
    function closeShopPhotoModal() {
        document.getElementById('shop-photo-modal').classList.add('hidden');
    }
    function submitShopPhoto(e) {
        e.preventDefault();
        const urlInput = document.getElementById('shop_photo_url');
        const fileInput = document.getElementById('shop_photo_file');
        const submitBtn = document.getElementById('btn_submit_shop_photo');

        const formData = new FormData();
        if (fileInput.files.length > 0) {
            formData.append('shop_photo', fileInput.files[0]);
        } else if (urlInput.value.trim()) {
            formData.append('photo_url', urlInput.value.trim());
        } else {
            alert('Please choose a photo or enter image URL.');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Uploading...';

        fetch("{{ route('seller.minisite.quick_shop_photo_upload') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Upload & Add';
            if (data.success) {
                closeShopPhotoModal();
                alert(data.message || 'Photo added!');
                location.reload();
            } else {
                alert(data.message || 'Failed to upload photo.');
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Upload & Add';
            alert('Upload error: ' + err.message);
        });
    }
</script>
@endsection