@extends('seller-site.layout')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gray-900 text-white overflow-hidden">
    @if($sellerPage->banner_image_url)
        <div class="absolute inset-0 z-0">
            <img src="{{ $sellerPage->banner_image_url }}" alt="{{ $sellerPage->page_title }}" class="w-full h-full object-cover opacity-30 filter blur-xs">
            <div class="absolute inset-0 bg-gradient-to-r from-gray-950 via-gray-900/90 to-transparent"></div>
        </div>
    @else
        <div class="absolute inset-0 z-0 bg-gradient-to-r from-gray-950 via-gray-900 to-gray-800"></div>
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
            </div>
        </div>
    </div>
</section>

<!-- Trust Badges Bar -->
<section class="border-b border-gray-200 bg-white py-4 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs font-medium text-gray-600">
            <div class="flex items-center justify-center sm:justify-start gap-2.5">
                <i class="fa-solid fa-truck-fast text-brand-custom text-base"></i>
                <span>Fast Express Delivery</span>
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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach($products as $product)
                    @include($cardComponent, ['product' => $product, 'sellerPage' => $sellerPage])
                @endforeach
            </div>
    @endif
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

                    @if($sellerPage->google_map_link)
                        <a href="{{ $sellerPage->google_map_link }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-full border border-blue-200 shadow-xs transition">
                            <i class="fa-solid fa-map-location-dot text-blue-600"></i>
                            <span>View on Google Maps</span>
                        </a>
                    @endif

                    @if($sellerPage->google_review_link)
                        <a href="{{ $sellerPage->google_review_link }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-800 bg-amber-50 hover:bg-amber-100 px-4 py-2 rounded-full border border-amber-200 shadow-xs transition">
                            <i class="fa-brands fa-google text-amber-500"></i>
                            <span>Google Verified & Reviews</span>
                        </a>
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
@endsection