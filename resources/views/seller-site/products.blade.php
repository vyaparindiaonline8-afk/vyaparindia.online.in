@extends('seller-site.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">

    <!-- 🏢 1. Authorized Brands & Dealerships Showcase (With Logos) -->
    @if(!empty($authorizedBrands) && count($authorizedBrands) > 0)
        <div class="mb-6 p-4 rounded-3xl bg-white border border-gray-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h2 class="text-xs sm:text-sm font-black text-gray-900 tracking-tight uppercase">
                        Authorized Brands & Dealerships • हमारे अधिकृत ब्रांड्स
                    </h2>
                </div>
                <span class="text-[11px] font-bold text-gray-400 hidden sm:inline">Click brand logo to filter products</span>
            </div>
            <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-thin">
                <a href="{{ route('minisite.products', array_merge(['sellerPage' => $sellerPage->slug], request()->except('brand', 'page'))) }}" class="shrink-0 px-3.5 py-2 rounded-2xl border transition-all flex items-center gap-2 {{ !request('brand') ? 'bg-gray-900 text-white border-gray-900 shadow-xs' : 'bg-gray-50 border-gray-200 text-gray-700 hover:bg-gray-100' }}">
                    <i class="fa-solid fa-layer-group text-xs"></i>
                    <span class="text-xs font-black">All Brands</span>
                </a>
                @foreach($authorizedBrands as $b)
                    @php
                        $bName = is_array($b) ? ($b['name'] ?? '') : $b;
                        $bLogo = is_array($b) ? ($b['logo'] ?? '') : '';
                        $bTag = is_array($b) ? ($b['tag'] ?? 'Authorized') : 'Authorized';
                        $isSelected = request('brand') === $bName;
                    @endphp
                    @if(!empty($bName))
                        <a href="{{ route('minisite.products', array_merge(['sellerPage' => $sellerPage->slug], request()->except('brand', 'page'), ['brand' => $bName])) }}" class="shrink-0 px-3 py-2 rounded-2xl border transition-all flex items-center gap-2.5 {{ $isSelected ? 'bg-indigo-50 border-indigo-500 ring-2 ring-indigo-500/20 shadow-xs' : 'bg-white border-gray-200 hover:border-gray-300 hover:shadow-2xs' }}">
                            @if(!empty($bLogo))
                                <img src="{{ $bLogo }}" alt="{{ $bName }}" class="h-6 w-auto max-w-[70px] object-contain crisp-img">
                            @else
                                <div class="h-6 w-6 rounded-lg bg-indigo-600 text-white text-[10px] font-black flex items-center justify-center">
                                    {{ substr($bName, 0, 1) }}
                                </div>
                            @endif
                            <div class="text-left">
                                <div class="text-xs font-black text-gray-900 leading-tight">{{ $bName }}</div>
                                <div class="text-[9px] font-bold {{ $isSelected ? 'text-indigo-600' : 'text-gray-400' }}">{{ $bTag }}</div>
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- 🔍 2. 3-Tier Hierarchy Search & Filter Bar (Company, Group, Category) -->
    <div class="bg-white rounded-3xl border border-gray-200 p-4 sm:p-5 shadow-xs mb-6">
        <form action="{{ route('minisite.products', $sellerPage->slug) }}" method="GET" id="catalog-filter-form" class="space-y-4">
            
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-gray-100">
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                        <span>Product Catalog</span>
                        <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                            {{ $products->total() }} Products
                        </span>
                    </h1>
                    <p class="text-xs text-gray-500 mt-0.5">Filter by Brand, Group (Plumbing/Sanitary), and Category to find exact items</p>
                </div>

                <!-- Search Input Box -->
                <div class="relative flex-1 max-w-md">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by product name, code, or keyword..." class="w-full pl-9 pr-4 py-2 text-xs bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                </div>
            </div>

            <!-- The 3 Dropdowns (Brand / Company, Group, Category) + Sort -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                
                <!-- 🏢 1. Company / Brand Dropdown -->
                <div>
                    <label class="block text-[11px] font-extrabold text-gray-700 uppercase tracking-wider mb-1">
                        <i class="fa-solid fa-building text-blue-600 mr-1"></i> Company / Brand
                    </label>
                    <select name="brand" onchange="document.getElementById('catalog-filter-form').submit()" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-3 py-2 font-bold text-gray-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                        <option value="">All Brands (सभी कंपनियां)</option>
                        @if(isset($availableBrands))
                            @foreach($availableBrands as $brandName)
                                <option value="{{ $brandName }}" {{ request('brand') === $brandName ? 'selected' : '' }}>
                                    {{ $brandName }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- 📂 2. Group Dropdown -->
                <div>
                    <label class="block text-[11px] font-extrabold text-gray-700 uppercase tracking-wider mb-1">
                        <i class="fa-solid fa-layer-group text-purple-600 mr-1"></i> Group / Division
                    </label>
                    <select name="group" onchange="document.getElementById('catalog-filter-form').submit()" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-3 py-2 font-bold text-gray-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                        <option value="">All Groups (Plumbing, Sanitary...)</option>
                        @if(isset($availableGroups))
                            @foreach($availableGroups as $grpName)
                                <option value="{{ $grpName }}" {{ request('group') === $grpName ? 'selected' : '' }}>
                                    {{ $grpName }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- 🏷️ 3. Category Dropdown -->
                <div>
                    <label class="block text-[11px] font-extrabold text-gray-700 uppercase tracking-wider mb-1">
                        <i class="fa-solid fa-tags text-emerald-600 mr-1"></i> Category
                    </label>
                    <select name="category" onchange="document.getElementById('catalog-filter-form').submit()" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-3 py-2 font-bold text-gray-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                        <option value="">All Categories (UPVC, CPVC, SWR...)</option>
                        @if(isset($categories))
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- 🔃 4. Smart Sort Selector & Clear -->
                <div>
                    <label class="block text-[11px] font-extrabold text-gray-700 uppercase tracking-wider mb-1">
                        <i class="fa-solid fa-arrow-down-short-wide text-amber-600 mr-1"></i> Sort By
                    </label>
                    <div class="flex items-center gap-1.5">
                        <select name="sort" onchange="document.getElementById('catalog-filter-form').submit()" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-3 py-2 font-bold text-gray-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                            @php $s = request('sort', 'latest'); @endphp
                            <option value="latest" {{ $s === 'latest' ? 'selected' : '' }}>🆕 Newly Added First</option>
                            <option value="popular" {{ $s === 'popular' ? 'selected' : '' }}>🔥 Top Selling</option>
                            <option value="price_asc" {{ $s === 'price_asc' ? 'selected' : '' }}>💰 Price: Low to High</option>
                            <option value="price_desc" {{ $s === 'price_desc' ? 'selected' : '' }}>💎 Price: High to Low</option>
                            <option value="oldest" {{ $s === 'oldest' ? 'selected' : '' }}>📅 Oldest First</option>
                        </select>

                        @if(request('search') || request('brand') || request('group') || request('category'))
                            <a href="{{ route('minisite.products', $sellerPage->slug) }}" class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition flex items-center justify-center shrink-0" title="Reset All Filters">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </form>
    </div>

    <!-- 📢 3. Active Hierarchy Filter & 1-Click WhatsApp Share Suite -->
    @php
        $activeBrand = request('brand');
        $activeGroup = request('group');
        $activeCatId = request('category');
        $activeCat = $activeCatId ? ($categories->firstWhere('id', $activeCatId) ?? $categories->firstWhere('slug', $activeCatId)) : null;
        $isAnyFilterActive = $activeBrand || $activeGroup || $activeCat || request('search');
        $filterShareUrl = request()->fullUrl();

        $filterTitleParts = [];
        if ($activeBrand) $filterTitleParts[] = $activeBrand;
        if ($activeGroup) $filterTitleParts[] = $activeGroup;
        if ($activeCat) $filterTitleParts[] = $activeCat->name;
        $filterTitle = !empty($filterTitleParts) ? implode(' • ', $filterTitleParts) : 'Our Catalog';

        $waCatalogShareMsg = urlencode("Namaste! Hamare store {$sellerPage->page_title} par [{$filterTitle}] ke sabhi products aur unke wholesale rates yahan dekhein:\n{$filterShareUrl}");
    @endphp

    @if($isAnyFilterActive)
        <div class="mb-6 p-4 rounded-2xl bg-gradient-to-r from-emerald-50 via-teal-50 to-blue-50 border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg shadow-sm shrink-0">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-black text-gray-900">Current Selection:</span>
                        @if($activeBrand)
                            <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 text-[11px] font-black">🏢 {{ $activeBrand }}</span>
                        @endif
                        @if($activeGroup)
                            <span class="px-2 py-0.5 rounded-md bg-purple-100 text-purple-800 text-[11px] font-black">📂 {{ $activeGroup }}</span>
                        @endif
                        @if($activeCat)
                            <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[11px] font-black">🏷️ {{ $activeCat->name }}</span>
                        @endif
                        @if(request('search'))
                            <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 text-[11px] font-black">🔍 "{{ request('search') }}"</span>
                        @endif
                        <span class="text-[11px] font-bold text-gray-500">({{ $products->total() }} items)</span>
                    </div>
                    <p class="text-[11px] text-gray-600 mt-0.5">Share this exact filtered catalog view directly with clients on WhatsApp.</p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="https://api.whatsapp.com/send?text={{ $waCatalogShareMsg }}" target="_blank" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs flex items-center gap-1.5 shadow-md shadow-emerald-600/20 transition">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>Share on WhatsApp</span>
                </a>
                <button type="button" onclick="navigator.clipboard.writeText('{{ $filterShareUrl }}'); this.innerHTML = '<i class=\'fa-solid fa-check text-emerald-600\'></i> Copied!'; setTimeout(() => this.innerHTML = '<i class=\'fa-solid fa-copy\'></i> Copy Link', 2000);" class="px-3 py-2 rounded-xl bg-white hover:bg-gray-50 text-gray-700 font-bold text-xs border border-gray-200 shadow-2xs transition flex items-center gap-1">
                    <i class="fa-solid fa-copy"></i>
                    <span>Copy Link</span>
                </button>
            </div>
        </div>
    @endif

    <!-- 🎯 Curated 3-6 Items Special Collection Banner (if ?items=... used) -->
    @if(request('items') || !empty($isCurated))
        @php
            $curatedUrl = request()->fullUrl();
            $waCuratedMsg = urlencode("Namaste! Aapke liye chune hue khaas products ({$products->total()} items) ki list aur photos yahan dekhein:\n{$curatedUrl}");
        @endphp
        <div class="mb-6 p-4 rounded-3xl bg-gradient-to-r from-amber-50 via-indigo-50 to-emerald-50 border border-indigo-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
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

    <!-- 📦 4. Product Grid -->
    <div>
        @if($products->isEmpty())
            <div class="bg-white rounded-3xl p-16 text-center border border-gray-200 max-w-lg mx-auto my-8 shadow-xs">
                <div class="h-16 w-16 mx-auto rounded-full bg-gray-100 flex items-center justify-center text-gray-400 text-2xl mb-4">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h3 class="text-base font-bold text-gray-900">No products found</h3>
                <p class="text-xs text-gray-500 mt-1">Try resetting your filters or selecting a different Brand or Group.</p>
                <a href="{{ route('minisite.products', $sellerPage->slug) }}" class="mt-4 inline-block px-5 py-2.5 rounded-xl bg-gray-900 text-white font-bold text-xs hover:bg-gray-800 transition">
                    Reset All Filters
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                @foreach($products as $product)
                    @php
                        $variants = $product->variants;
                        $firstVar = $variants->first();
                        $initPrice = $firstVar ? ($firstVar->retail_price ?: ($firstVar->wholesale_price ?: $product->price)) : $product->price;
                        $initMrp = $firstVar ? ($firstVar->mrp ?: ($initPrice * 1.35)) : ($product->mrp ?: ($product->price * 1.35));
                    @endphp
                    <div class="bg-white rounded-2xl border border-gray-200 hover:border-indigo-300 hover:shadow-lg transition-all flex flex-col justify-between overflow-hidden group p-3.5 space-y-3 relative" id="card_box_{{ $product->id }}">
                        
                        <!-- Top Image & Quick Badges -->
                        <div>
                            <div class="relative block aspect-square bg-slate-50 rounded-xl overflow-hidden border border-slate-100 mb-3 flex items-center justify-center p-2">
                                <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" class="w-full h-full flex items-center justify-center">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" id="prod_img_{{ $product->id }}" class="w-full h-full object-contain crisp-img group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="text-gray-300 text-center" id="prod_img_{{ $product->id }}">
                                            <i class="fa-solid fa-image text-3xl"></i>
                                        </div>
                                    @endif
                                </a>

                                <!-- Brand Badge (Top Left) -->
                                @if($product->brand)
                                    <span class="absolute top-2 left-2 bg-blue-600/90 backdrop-blur-xs text-white text-[10px] font-black px-2 py-0.5 rounded-md shadow-2xs">
                                        {{ $product->brand }}
                                    </span>
                                @elseif($product->category)
                                    <span class="absolute top-2 left-2 bg-gray-900/90 backdrop-blur-xs text-white text-[10px] font-black px-2 py-0.5 rounded-md shadow-2xs">
                                        {{ $product->category->name }}
                                    </span>
                                @endif

                                <!-- 📲 Individual Card 1-Click WhatsApp Share Button (Top Right) -->
                                <button type="button" onclick="shareProductCardWhatsapp({{ $product->id }})" class="absolute top-2 right-2 h-7 w-7 rounded-full bg-white/90 hover:bg-emerald-500 hover:text-white text-emerald-600 shadow-sm flex items-center justify-center text-xs transition" title="Share this Product on WhatsApp">
                                    <i class="fa-brands fa-whatsapp text-sm"></i>
                                </button>

                                <!-- 📷 Store Owner Instant Photo Swapper Button (Bottom Right) -->
                                @auth
                                    @if(Auth::id() === $sellerPage->user_id || Auth::user()->is_seller())
                                        <button type="button" onclick="openPhotoSwapper({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->image_url }}')" class="absolute bottom-2 right-2 px-2 py-1 rounded-md bg-slate-900/80 hover:bg-slate-900 text-white font-bold text-[10px] flex items-center gap-1 shadow-sm backdrop-blur-xs transition" title="Change Photo directly on this card">
                                            <i class="fa-solid fa-camera"></i>
                                            <span>Photo</span>
                                        </button>
                                    @endif
                                @endauth
                            </div>

                            <!-- Group / Category Meta Tags -->
                            <div class="flex items-center gap-1.5 flex-wrap mb-1.5">
                                @if($product->group_name)
                                    <span class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 font-bold text-[10px] border border-purple-200">
                                        📂 {{ $product->group_name }}
                                    </span>
                                @endif
                                @if($product->category)
                                    <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 font-bold text-[10px]">
                                        {{ $product->category->name }}
                                    </span>
                                @endif
                            </div>

                            <!-- Product Title -->
                            <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" class="font-black text-xs sm:text-sm text-gray-900 hover:text-indigo-600 line-clamp-2 transition-colors leading-tight" title="{{ $product->name }}">
                                {{ $product->name }}
                            </a>

                            <!-- Flexible Size / Variant Selection on Card -->
                            @if($variants && $variants->count() > 1)
                                <div class="mt-2.5">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">
                                        Choose Size / Variant:
                                    </label>
                                    <select id="card_variant_{{ $product->id }}" onchange="onCardVariantChanged({{ $product->id }})" class="w-full text-xs font-bold border border-gray-200 rounded-xl px-2.5 py-1.5 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:outline-none transition">
                                        @foreach($variants as $idx => $v)
                                            @php
                                                $vPrice = $v->retail_price ?: ($v->wholesale_price ?: $product->price);
                                                $vMrp = $v->mrp ?: ($vPrice * 1.35);
                                            @endphp
                                            <option value="{{ $v->id }}" data-price="{{ $vPrice }}" data-mrp="{{ $vMrp }}" data-name="{{ $v->variant_name }}" data-stock="{{ $v->stock_quantity ?? 0 }}" {{ $idx === 0 ? 'selected' : '' }}>
                                                {{ $v->variant_name }} (₹{{ number_format($vPrice, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif($variants && $variants->count() === 1)
                                <div class="mt-2 text-[11px] font-bold text-gray-500 flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 font-mono text-[10px]">{{ $variants[0]->variant_name }}</span>
                                    <input type="hidden" id="card_variant_{{ $product->id }}" value="{{ $variants[0]->id }}" data-price="{{ $initPrice }}" data-mrp="{{ $initMrp }}" data-name="{{ $variants[0]->variant_name }}">
                                </div>
                            @endif

                            <!-- Dynamic Live Price & Calculated Total -->
                            <div class="mt-3 flex items-baseline justify-between">
                                <div>
                                    <div class="flex items-baseline gap-1.5">
                                        <span class="text-base sm:text-lg font-black text-gray-900" id="card_unit_price_{{ $product->id }}">₹{{ number_format($initPrice, 2) }}</span>
                                        <span class="text-xs text-gray-400 line-through" id="card_mrp_{{ $product->id }}">₹{{ number_format($initMrp, 2) }}</span>
                                    </div>
                                    <div class="text-[11px] font-bold text-emerald-700" id="card_total_box_{{ $product->id }}">
                                        Total: <span id="card_total_price_{{ $product->id }}" class="font-extrabold">₹{{ number_format($initPrice, 2) }}</span>
                                    </div>
                                </div>

                                <!-- Quantity Stepper [-] [ 1 ] [+] on Card -->
                                <div class="inline-flex items-center border border-gray-200 rounded-xl bg-gray-50 overflow-hidden shadow-2xs">
                                    <button type="button" onclick="stepCardQty({{ $product->id }}, -1)" class="px-2.5 py-1 text-xs font-bold text-gray-600 hover:bg-gray-200 transition-colors">-</button>
                                    <input type="number" id="card_qty_{{ $product->id }}" value="1" min="1" onchange="onCardQtyInput({{ $product->id }})" class="w-8 text-center text-xs font-bold text-gray-900 border-none bg-transparent focus:outline-none p-0" readonly>
                                    <button type="button" onclick="stepCardQty({{ $product->id }}, 1)" class="px-2.5 py-1 text-xs font-bold text-gray-600 hover:bg-gray-200 transition-colors">+</button>
                                </div>
                            </div>
                        </div>

                        <!-- 1-Click Action Buttons Right on the Card -->
                        <div class="pt-3 border-t border-gray-100 grid grid-cols-2 gap-2 mt-auto">
                            <button type="button" onclick="addCardToBag({{ $product->id }})" id="btn_add_bag_{{ $product->id }}" class="py-2 px-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs transition flex items-center justify-center gap-1.5" title="Add to Shopping Bag">
                                <i class="fa-solid fa-bag-shopping text-xs"></i>
                                <span>Add to Bag</span>
                            </button>

                            <button type="button" onclick="orderCardOnWhatsapp({{ $product->id }})" class="py-2 px-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-xs shadow-emerald-500/20" title="Direct WhatsApp Order">
                                <i class="fa-brands fa-whatsapp text-sm"></i>
                                <span>WhatsApp</span>
                            </button>
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

<!-- 📸 Photo Swapper Modal for Store Owner -->
<div id="photo-swapper-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-200 animate-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <h3 class="text-sm font-black text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-camera text-indigo-600"></i>
                <span>Change Product Photo</span>
            </h3>
            <button type="button" onclick="closePhotoSwapper()" class="text-gray-400 hover:text-gray-600 text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="photo-swapper-form" onsubmit="submitPhotoSwap(event)" class="mt-4 space-y-4">
            <input type="hidden" id="swap_product_id">
            
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1" id="swap_product_name_label">Product Name</label>
                <div class="text-xs text-gray-500 mb-2">Paste a high-resolution image URL or upload a file:</div>
                <input type="text" id="swap_image_url" placeholder="https://example.com/item.jpg" class="w-full text-xs font-mono border border-gray-300 rounded-xl px-3 py-2 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="relative flex py-1 items-center">
                <div class="flex-grow border-t border-gray-200"></div>
                <span class="flex-shrink mx-3 text-[11px] font-bold text-gray-400">OR Upload</span>
                <div class="flex-grow border-t border-gray-200"></div>
            </div>

            <div>
                <input type="file" id="swap_image_file" accept="image/*" class="w-full text-xs file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                <button type="button" onclick="closePhotoSwapper()" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition">
                    Cancel
                </button>
                <button type="submit" id="btn-save-photo-swap" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-md shadow-indigo-600/20 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Save Photo</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const STORE_PRODUCTS = @json($products->items());
    const STORE_PAGE_SLUG = @json($sellerPage->slug);
    const STORE_PAGE_TITLE = @json($sellerPage->page_title);
    const STORE_WA_NUM = @json($sellerPage->whatsapp_number ?? '');

    function getCardSelectedState(prodId) {
        const prod = STORE_PRODUCTS.find(p => p.id === prodId);
        if (!prod) return null;

        const qtyEl = document.getElementById(`card_qty_${prodId}`);
        const qty = Math.max(1, parseInt(qtyEl?.value || 1));

        let unitPrice = parseFloat(prod.price || 0);
        let mrp = parseFloat(prod.mrp || (unitPrice * 1.35));
        let variantName = '';
        let variantId = null;

        const varSelect = document.getElementById(`card_variant_${prodId}`);
        if (varSelect) {
            if (varSelect.tagName === 'SELECT') {
                const opt = varSelect.options[varSelect.selectedIndex];
                if (opt) {
                    unitPrice = parseFloat(opt.getAttribute('data-price') || unitPrice);
                    mrp = parseFloat(opt.getAttribute('data-mrp') || mrp);
                    variantName = opt.getAttribute('data-name') || '';
                    variantId = parseInt(opt.value);
                }
            } else if (varSelect.tagName === 'INPUT') {
                unitPrice = parseFloat(varSelect.getAttribute('data-price') || unitPrice);
                mrp = parseFloat(varSelect.getAttribute('data-mrp') || mrp);
                variantName = varSelect.getAttribute('data-name') || '';
                variantId = parseInt(varSelect.value);
            }
        }

        const total = unitPrice * qty;

        return {
            product: prod,
            quantity: qty,
            variantId: variantId,
            variantName: variantName,
            unitPrice: unitPrice,
            mrp: mrp,
            total: total
        };
    }

    function onCardVariantChanged(prodId) {
        recalcCardTotal(prodId);
    }

    function stepCardQty(prodId, delta) {
        const qtyEl = document.getElementById(`card_qty_${prodId}`);
        if (!qtyEl) return;
        let val = Math.max(1, (parseInt(qtyEl.value) || 1) + delta);
        qtyEl.value = val;
        recalcCardTotal(prodId);
    }

    function onCardQtyInput(prodId) {
        const qtyEl = document.getElementById(`card_qty_${prodId}`);
        if (qtyEl) {
            qtyEl.value = Math.max(1, parseInt(qtyEl.value) || 1);
        }
        recalcCardTotal(prodId);
    }

    function recalcCardTotal(prodId) {
        const state = getCardSelectedState(prodId);
        if (!state) return;

        const unitEl = document.getElementById(`card_unit_price_${prodId}`);
        const mrpEl = document.getElementById(`card_mrp_${prodId}`);
        const totalEl = document.getElementById(`card_total_price_${prodId}`);

        if (unitEl) unitEl.innerText = '₹' + state.unitPrice.toFixed(2);
        if (mrpEl) mrpEl.innerText = '₹' + state.mrp.toFixed(2);
        if (totalEl) totalEl.innerText = '₹' + state.total.toFixed(2);
    }

    function addCardToBag(prodId) {
        const state = getCardSelectedState(prodId);
        if (!state) return;

        const cartItem = {
            id: state.product.id,
            name: state.product.name + (state.variantName ? ` (${state.variantName})` : ''),
            price: state.unitPrice,
            image: state.product.image_url,
            variant_id: state.variantId,
            variant_name: state.variantName
        };

        if (typeof addToCart === 'function') {
            addToCart(cartItem, state.quantity, true);
        }

        const btn = document.getElementById(`btn_add_bag_${prodId}`);
        if (btn) {
            const origHtml = btn.innerHTML;
            btn.innerHTML = `<i class="fa-solid fa-check text-emerald-600"></i> Added!`;
            setTimeout(() => { btn.innerHTML = origHtml; }, 1800);
        }
    }

    function orderCardOnWhatsapp(prodId) {
        const state = getCardSelectedState(prodId);
        if (!state) return;

        if (!STORE_WA_NUM) {
            alert('WhatsApp number store owner dwara set nahi kiya gaya hai.');
            return;
        }

        const prodUrl = window.location.origin + '/' + STORE_PAGE_SLUG + '/product/' + state.product.slug;
        const varTxt = state.variantName ? ` (${state.variantName})` : '';

        const msg = `*Namaste! New Order Inquiry from ${STORE_PAGE_TITLE}*\n\n` +
                    `🛍️ *Product:* ${state.product.name}${varTxt}\n` +
                    `🔢 *Quantity:* ${state.quantity}\n` +
                    `💰 *Rate:* ₹${state.unitPrice.toFixed(2)} per unit\n` +
                    `💵 *Total Amount:* ₹${state.total.toFixed(2)}\n` +
                    `🔗 *Item Link:* ${prodUrl}\n\n` +
                    `Kripya payment details aur delivery time confirm karein.`;

        window.open(`https://api.whatsapp.com/send?phone=${STORE_WA_NUM}&text=${encodeURIComponent(msg)}`, '_blank');
    }

    // 📲 1-Click WhatsApp Share for Individual Product Card
    function shareProductCardWhatsapp(prodId) {
        const prod = STORE_PRODUCTS.find(p => p.id === prodId);
        if (!prod) return;

        const prodUrl = window.location.origin + '/' + STORE_PAGE_SLUG + '/product/' + prod.slug;
        const msg = `*Namaste! Check out this product on ${STORE_PAGE_TITLE}:*\n\n` +
                    `🛍️ *${prod.name}*\n` +
                    (prod.brand ? `🏢 *Brand:* ${prod.brand}\n` : '') +
                    `💰 *Price:* ₹${parseFloat(prod.price || 0).toFixed(2)}\n\n` +
                    `🔗 *View Full Specifications & Order Online:*\n${prodUrl}`;

        window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(msg)}`, '_blank');
    }

    // 📷 Photo Swapper Functions
    function openPhotoSwapper(prodId, prodName, currentImg) {
        document.getElementById('swap_product_id').value = prodId;
        document.getElementById('swap_product_name_label').innerText = prodName;
        document.getElementById('swap_image_url').value = currentImg || '';
        document.getElementById('photo-swapper-modal').classList.remove('hidden');
    }

    function closePhotoSwapper() {
        document.getElementById('photo-swapper-modal').classList.add('hidden');
        document.getElementById('photo-swapper-form').reset();
    }

    async function submitPhotoSwap(e) {
        e.preventDefault();
        const prodId = document.getElementById('swap_product_id').value;
        const imgUrl = document.getElementById('swap_image_url').value;
        const fileInput = document.getElementById('swap_image_file');
        const saveBtn = document.getElementById('btn-save-photo-swap');

        if (!imgUrl && (!fileInput.files || fileInput.files.length === 0)) {
            alert('Please provide an image URL or choose a photo to upload.');
            return;
        }

        saveBtn.disabled = true;
        saveBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Saving...`;

        const formData = new FormData();
        if (imgUrl) formData.append('image_url', imgUrl);
        if (fileInput.files && fileInput.files[0]) formData.append('image_file', fileInput.files[0]);

        try {
            const resp = await fetch(`/seller/products/${prodId}/quick-image-update`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            const data = await resp.json();
            if (data.success) {
                // Update image on page directly
                const imgEl = document.getElementById(`prod_img_${prodId}`);
                if (imgEl && imgEl.tagName === 'IMG') {
                    imgEl.src = data.image_url;
                } else if (imgEl) {
                    imgEl.outerHTML = `<img src="${data.image_url}" id="prod_img_${prodId}" class="w-full h-full object-contain crisp-img group-hover:scale-105 transition-transform duration-300">`;
                }
                closePhotoSwapper();
            } else {
                alert(data.message || 'Failed to update image.');
            }
        } catch (err) {
            console.error(err);
            alert('Error updating product photo.');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = `<i class="fa-solid fa-check"></i> <span>Save Photo</span>`;
        }
    }
</script>
@endpush
@endsection
