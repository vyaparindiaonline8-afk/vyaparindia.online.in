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

<!-- Products Showcase -->
<section id="products-grid" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
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
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 flex flex-col group">
                    <!-- Image -->
                    <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" class="aspect-square bg-gray-100 relative overflow-hidden block">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @if($product->category)
                            <span class="absolute top-2.5 left-2.5 bg-white/90 backdrop-blur-xs text-gray-800 text-[10px] font-bold px-2 py-0.5 rounded-md shadow-xs">
                                {{ $product->category->name }}
                            </span>
                        @endif
                    </a>

                    <!-- Product Info -->
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="flex items-center gap-1 text-amber-400 text-[11px] mb-1 font-semibold">
                            <i class="fa-solid fa-star"></i>
                            <span class="text-gray-900 font-bold ml-0.5">{{ $product->average_rating }}</span>
                            <span class="text-gray-400 font-normal">({{ $product->reviews_count }})</span>
                        </div>
                        <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" class="font-bold text-xs sm:text-sm text-gray-900 hover:text-brand-custom line-clamp-2 transition-colors">
                            {{ $product->name }}
                        </a>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-base font-extrabold text-gray-900">₹{{ number_format($product->price, 2) }}</span>
                            <span class="text-xs text-gray-400 line-through">₹{{ number_format($product->price * 1.3, 2) }}</span>
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">30% OFF</span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 pt-3 border-t border-gray-100 grid grid-cols-2 gap-2 mt-auto">
                            <!-- Add to Cart -->
                            <button onclick='addToCart(@json($product))' class="py-2 px-2.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold text-xs transition-colors flex items-center justify-center gap-1.5" title="Add to Bag">
                                <i class="fa-solid fa-bag-shopping text-xs"></i>
                                <span class="hidden sm:inline">Add</span>
                            </button>

                            <!-- WhatsApp Buy -->
                            <button onclick="buySingleOnWhatsapp('{{ addslashes($product->name) }}', '{{ $product->price }}', '{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}')" class="py-2 px-2.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white font-semibold text-xs transition-colors flex items-center justify-center gap-1.5" title="Order via WhatsApp">
                                <i class="fa-brands fa-whatsapp text-sm"></i>
                                <span class="hidden sm:inline">Order</span>
                            </button>
                        </div>
                    </div>
                </div>
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
            @if($sellerPage->address)
                <div class="mt-6 inline-flex items-center gap-2 text-xs font-medium text-gray-500 bg-white px-4 py-2 rounded-full border border-gray-200 shadow-xs">
                    <i class="fa-solid fa-location-dot text-brand-custom"></i>
                    <span>Located at: {{ $sellerPage->address }}, {{ $sellerPage->city }}</span>
                </div>
            @endif
        </div>
    </section>
@endif
@endsection