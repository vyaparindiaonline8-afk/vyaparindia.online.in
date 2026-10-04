@extends('seller-site.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    <!-- Header & Search -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-gray-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Product Catalog</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Showing all items available directly from {{ $sellerPage->page_title }}</p>
        </div>

        <!-- Search Input -->
        <form action="{{ route('minisite.products', $sellerPage->slug) }}" method="GET" class="flex items-center gap-2 max-w-md w-full">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or keyword..." class="w-full pl-10 pr-4 py-2 text-sm bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent shadow-xs">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-gray-400 text-xs"></i>
            </div>
            <button type="submit" class="py-2 px-4 rounded-xl bg-gray-900 text-white font-semibold text-sm hover:bg-gray-800 transition-colors shadow-xs">
                Search
            </button>
            @if(request('search') || request('category'))
                <a href="{{ route('minisite.products', $sellerPage->slug) }}" class="p-2 text-gray-400 hover:text-gray-700" title="Clear Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Category Filter Pills & Smart Sort -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 py-4 border-b border-gray-100">
        @if(isset($categories) && $categories->isNotEmpty())
            <div class="flex items-center gap-2 overflow-x-auto scrollbar-none flex-1">
                <a href="{{ route('minisite.products', array_merge(['sellerPage' => $sellerPage->slug], request()->except('category', 'page'))) }}" class="px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-colors {{ !request('category') ? 'bg-gray-900 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                    All Items ({{ $sellerPage->user->products()->count() }})
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('minisite.products', array_merge(['sellerPage' => $sellerPage->slug], request()->except('page'), ['category' => $cat->id])) }}" class="px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-colors {{ request('category') == $cat->id ? 'bg-gray-900 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Sorting Selector -->
        <div class="flex items-center gap-2 shrink-0">
            <span class="text-xs font-bold text-gray-500"><i class="fa-solid fa-arrow-down-short-wide mr-1"></i> Sort:</span>
            <select onchange="window.location.href=this.value" class="text-xs font-bold bg-white border border-gray-300 rounded-xl px-3 py-1.5 focus:outline-hidden shadow-xs cursor-pointer">
                @php $s = request('sort', 'latest'); @endphp
                <option value="{{ route('minisite.products', array_merge(['sellerPage' => $sellerPage->slug], request()->except('page'), ['sort' => 'latest'])) }}" {{ $s === 'latest' ? 'selected' : '' }}>
                    🆕 Newly Added First
                </option>
                <option value="{{ route('minisite.products', array_merge(['sellerPage' => $sellerPage->slug], request()->except('page'), ['sort' => 'popular'])) }}" {{ $s === 'popular' ? 'selected' : '' }}>
                    🔥 Top Selling / Most Popular
                </option>
                <option value="{{ route('minisite.products', array_merge(['sellerPage' => $sellerPage->slug], request()->except('page'), ['sort' => 'price_asc'])) }}" {{ $s === 'price_asc' ? 'selected' : '' }}>
                    💰 Price: Low to High
                </option>
                <option value="{{ route('minisite.products', array_merge(['sellerPage' => $sellerPage->slug], request()->except('page'), ['sort' => 'price_desc'])) }}" {{ $s === 'price_desc' ? 'selected' : '' }}>
                    💎 Price: High to Low
                </option>
                <option value="{{ route('minisite.products', array_merge(['sellerPage' => $sellerPage->slug], request()->except('page'), ['sort' => 'oldest'])) }}" {{ $s === 'oldest' ? 'selected' : '' }}>
                    📅 Oldest First
                </option>
            </select>
        </div>
    </div>

    <!-- 🎯 Curated 3-6 Items Special Collection Banner -->
    @if(request('items') || !empty($isCurated))
        @php
            $curatedUrl = request()->fullUrl();
            $waCuratedMsg = urlencode("Namaste! Aapke liye chune hue khaas products ({$products->total()} items) ki list aur photos yahan dekhein:\n{$curatedUrl}");
        @endphp
        <div class="mt-6 p-4 rounded-3xl bg-gradient-to-r from-amber-50 via-indigo-50 to-emerald-50 border border-indigo-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="h-11 w-11 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-xl shadow-md shadow-indigo-600/20 shrink-0">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-gray-900 flex items-center gap-2">
                        <span>Special Curated Selection</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-indigo-100 text-indigo-700">{{ $products->total() }} Selected Items</span>
                    </h3>
                    <p class="text-xs text-gray-600 mt-0.5">
                        These items were specially hand-picked by {{ $sellerPage->page_title }} for your direct review.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="https://api.whatsapp.com/send?text={{ $waCuratedMsg }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition">
                    <i class="fa-brands fa-whatsapp text-sm"></i> Share via WhatsApp
                </a>
                <a href="{{ route('minisite.products', $sellerPage->slug) }}" class="px-3 py-2 rounded-xl bg-white hover:bg-gray-100 text-gray-700 font-bold text-xs border border-gray-200 shadow-2xs transition">
                    View Full Catalog &rarr;
                </a>
            </div>
        </div>
    @endif

    <!-- 📂 Category-wise Public Share Tool -->
    @if(request('category'))
        @php
            $currCat = $categories->firstWhere('id', request('category')) ?? $categories->firstWhere('slug', request('category'));
            $catTitle = $currCat ? $currCat->name : 'Selected Category';
            $catShareUrl = request()->fullUrl();
            $waCatMsg = urlencode("Namaste! Hamare store {$sellerPage->page_title} par {$catTitle} ki poori range aur latest rates yahan dekhein:\n{$catShareUrl}");
        @endphp
        <div class="mt-4 p-3.5 rounded-2xl bg-emerald-50/80 border border-emerald-200 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="h-8 w-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div>
                    <span class="text-xs font-black text-emerald-950">{{ $catTitle }} Range</span>
                    <span class="ml-1.5 text-[11px] text-emerald-700 font-bold">({{ $products->total() }} products found)</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="https://api.whatsapp.com/send?text={{ $waCatMsg }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-xs transition">
                    <i class="fa-brands fa-whatsapp text-sm"></i> Share Category Link
                </a>
                <button type="button" onclick="navigator.clipboard.writeText('{{ $catShareUrl }}'); this.innerHTML = '<i class=\'fa-solid fa-check text-emerald-600\'></i> Copied!'; setTimeout(() => this.innerHTML = '<i class=\'fa-solid fa-copy\'></i> Copy Link', 2000);" class="px-3 py-1.5 rounded-xl bg-white hover:bg-gray-50 text-gray-700 font-bold text-xs border border-gray-200 shadow-2xs transition flex items-center gap-1">
                    <i class="fa-solid fa-copy"></i> Copy Link
                </button>
            </div>
        </div>
    @endif

    <!-- Product Grid -->
    <div class="mt-6">
        @if($products->isEmpty())
            <div class="bg-white rounded-2xl p-16 text-center border border-gray-200 max-w-lg mx-auto my-8">
                <i class="fa-solid fa-magnifying-glass text-5xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-bold text-gray-900">No products found</h3>
                <p class="text-xs text-gray-500 mt-1">Try clearing your search query or choosing a different category.</p>
                <a href="{{ route('minisite.products', $sellerPage->slug) }}" class="mt-4 inline-block px-5 py-2 rounded-xl bg-gray-900 text-white font-semibold text-xs hover:bg-gray-800">
                    Reset All Filters
                </a>
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
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-4 pt-3 border-t border-gray-100 grid grid-cols-2 gap-2 mt-auto">
                                <button onclick='addToCart(@json($product))' class="py-2 px-2.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold text-xs transition-colors flex items-center justify-center gap-1.5" title="Add to Bag">
                                    <i class="fa-solid fa-bag-shopping text-xs"></i>
                                    <span class="hidden sm:inline">Add</span>
                                </button>

                                <button onclick="buySingleOnWhatsapp('{{ addslashes($product->name) }}', '{{ $product->price }}', '{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}')" class="py-2 px-2.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white font-semibold text-xs transition-colors flex items-center justify-center gap-1.5" title="Order via WhatsApp">
                                    <i class="fa-brands fa-whatsapp text-sm"></i>
                                    <span class="hidden sm:inline">Order</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-10">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
