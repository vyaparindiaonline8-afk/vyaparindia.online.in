@extends('seller-site.layout')

@section('content')
<!-- Hero Section -->
<div class="relative bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white overflow-hidden py-12 md:py-20">
    @if($sellerPage->banner_url)
        <div class="absolute inset-0 z-0 opacity-25">
            <img src="{{ $sellerPage->banner_url }}" alt="{{ $sellerPage->page_title }}" class="w-full h-full object-cover">
        </div>
    @endif
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-white text-xs font-semibold backdrop-blur-sm mb-4 border border-white/10">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Direct from Manufacturer / Wholesaler</span>
            </div>
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight">
                {{ $sellerPage->page_title }}
            </h1>
            <p class="mt-4 text-sm sm:text-base text-gray-300 leading-relaxed">
                {{ $sellerPage->welcome_message ?: ($sellerPage->tagline ?: 'Explore our premium collection of products with verified quality, best wholesale pricing, and instant dispatch.') }}
            </p>
            
            <div class="mt-8 flex flex-wrap items-center gap-4">
                <a href="{{ route('minisite.products', $sellerPage->slug) }}" class="px-6 py-3 rounded-xl bg-brand-custom text-white font-bold text-sm shadow-lg hover:opacity-90 active:scale-95 transition-all flex items-center gap-2">
                    <span>Explore Catalog</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
                @if($sellerPage->whatsapp_number)
                    <a href="https://wa.me/{{ $sellerPage->clean_whatsapp_number }}" target="_blank" class="px-5 py-3 rounded-xl bg-emerald-600/90 text-white font-semibold text-sm hover:bg-emerald-600 transition-all flex items-center gap-2">
                        <i class="fa-brands fa-whatsapp text-base"></i>
                        <span>WhatsApp Connect</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Trust Bar / Benefits -->
<div class="bg-white border-b border-gray-200 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center md:text-left">
            <div class="flex items-center gap-3 justify-center md:justify-start">
                <div class="h-10 w-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-shield-check text-lg"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900">100% Genuine</h4>
                    <p class="text-[11px] text-gray-500">Direct from seller</p>
                </div>
            </div>
            <div class="flex items-center gap-3 justify-center md:justify-start">
                <div class="h-10 w-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-truck-fast text-lg"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900">Fast Dispatch</h4>
                    <p class="text-[11px] text-gray-500">Reliable pan-India delivery</p>
                </div>
            </div>
            <div class="flex items-center gap-3 justify-center md:justify-start">
                <div class="h-10 w-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-hand-holding-dollar text-lg"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900">Cash on Delivery</h4>
                    <p class="text-[11px] text-gray-500">Pay when you receive</p>
                </div>
            </div>
            <div class="flex items-center gap-3 justify-center md:justify-start">
                <div class="h-10 w-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i class="fa-brands fa-whatsapp text-lg text-emerald-600"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900">WhatsApp Support</h4>
                    <p class="text-[11px] text-gray-500">Instant answers</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Products Section -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex items-end justify-between mb-8">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-brand-custom">Featured Catalog</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mt-1">Trending Products</h2>
        </div>
        <a href="{{ route('minisite.products', $sellerPage->slug) }}" class="text-xs font-bold text-brand-custom hover:underline flex items-center gap-1">
            <span>View All ({{ $totalProducts }})</span>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
        </a>
    </div>

    @if($products->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-gray-200">
            <i class="fa-solid fa-boxes-stacked text-5xl text-gray-300 mb-3"></i>
            <h3 class="text-base font-bold text-gray-800">No products uploaded yet</h3>
            <p class="text-xs text-gray-500 mt-1">The seller will add products soon. Please check back shortly!</p>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-2xl border border-gray-200 hover:border-gray-300 hover:shadow-lg transition-all flex flex-col overflow-hidden group">
                    <!-- Product Image -->
                    <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" class="relative block aspect-square bg-gray-100 overflow-hidden">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @if($product->category)
                            <span class="absolute top-2.5 left-2.5 bg-white/90 backdrop-blur-xs text-gray-800 text-[10px] font-bold px-2 py-0.5 rounded-md shadow-xs">
                                {{ $product->category->name }}
                            </span>
                        @endif
                    </a>

                    <!-- Product Info -->
                    <div class="p-4 flex-1 flex flex-col">
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
