@extends('seller-site.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">

    <!-- 🌟 Luxury Brand Showcase Billboard (If Brand or Sanitaryware Selected) -->
    @if(request('brand'))
        @include('seller-site.components.brand-luxury-banner', ['brandName' => request('brand')])
    @elseif(request('group') === 'Sanitaryware' || stripos(request('search', ''), 'cava') !== false)
        @include('seller-site.components.brand-luxury-banner', ['brandName' => 'Cava'])
    @endif

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
                            @php $s = request('sort', 'smart'); @endphp
                            <option value="smart" {{ $s === 'smart' ? 'selected' : '' }}>⭐ AI Smart (सर्वाधिक सर्च व ऑर्डर)</option>
                            <option value="popular" {{ $s === 'popular' ? 'selected' : '' }}>🔥 Top Selling (सबसे ज्यादा बिकने वाले)</option>
                            <option value="latest" {{ $s === 'latest' ? 'selected' : '' }}>🆕 Newly Added First</option>
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

    <!-- 🛁 Quick Department / Group Sliders & Pills (सैनिटरी, प्लाईवुड, पाइप्स आदि) -->
    <div class="mb-6 p-3.5 rounded-2xl bg-white border border-gray-200/90 shadow-2xs">
        <div class="flex items-center justify-between mb-2 px-1">
            <span class="text-[11px] font-black text-gray-700 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-layer-group text-indigo-600"></i>
                <span>Department / Group Navigator • विभाग अनुसार तुरंत खोजें</span>
            </span>
            <span class="text-[10px] text-gray-400 font-bold hidden sm:inline">1-Click to view items in group</span>
        </div>
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-thin">
            <a href="{{ route('minisite.products', ['sellerPage' => $sellerPage->slug]) }}" 
               class="shrink-0 px-3.5 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-1.5 {{ (!request('group') && !request('search')) ? 'bg-indigo-600 text-white shadow-xs' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' }}">
                <i class="fa-solid fa-boxes-stacked text-[11px]"></i>
                <span>All Items (सभी)</span>
            </a>
            @if(isset($availableGroups))
                @foreach($availableGroups as $grp)
                    @php
                        $isGrpActive = (request('group') === $grp);
                        $grpUrl = route('minisite.products', array_merge(['sellerPage' => $sellerPage->slug], request()->except('page', 'group'), ['group' => $grp]));
                    @endphp
                    <a href="{{ $grpUrl }}" 
                       class="shrink-0 px-3.5 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-1.5 {{ $isGrpActive ? 'bg-indigo-600 text-white shadow-xs' : 'bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200/60' }}">
                        <span>📂 {{ $grp }}</span>
                    </a>
                @endforeach
            @endif
        </div>
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

    <!-- 🏢 3.5 Brand-Wise Running Product Sliders (When browsing all brands) -->
    @php
        $brandsGroup = $products->groupBy('brand')->filter(fn($items, $k) => !empty($k));
    @endphp

    @if(!request('brand') && !request('search') && $brandsGroup->count() > 1)
        <div class="mb-10 space-y-6">
            <div class="flex items-center justify-between pb-2 border-b border-gray-200">
                <div class="flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full bg-indigo-600"></span>
                    <h2 class="text-xs sm:text-sm font-black text-gray-900 tracking-tight uppercase">
                        Brand Showcases & Running Lines • ब्रांड अनुसार रनिंग मॉडल्स
                    </h2>
                </div>
                <span class="text-[11px] text-gray-500 font-bold hidden sm:inline">Swipe horizontally &rarr;</span>
            </div>

            @foreach($brandsGroup as $bName => $bProducts)
                <div class="bg-white rounded-3xl border border-gray-200/90 p-4 sm:p-5 shadow-xs space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-700 flex items-center justify-center font-black text-xs">
                                {{ substr($bName, 0, 2) }}
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-gray-900">{{ $bName }}</h3>
                                <span class="text-[10px] font-bold text-gray-400">
                                    {{ $bProducts->count() }} Running Products
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('minisite.products', array_merge(['sellerPage' => $sellerPage->slug], request()->except('page'), ['brand' => $bName])) }}" 
                           class="text-xs font-black text-indigo-600 hover:text-indigo-800 flex items-center gap-1 group">
                            <span>View All {{ $bName }}</span>
                            <i class="fa-solid fa-chevron-right text-[10px] group-hover:translate-x-0.5 transition"></i>
                        </a>
                    </div>

                    <!-- Horizontal Running Slider -->
                    <div class="flex items-stretch gap-3.5 overflow-x-auto pb-2 scrollbar-thin snap-x">
                        @foreach($bProducts as $bp)
                            <div class="shrink-0 w-48 sm:w-56 bg-gray-50/80 rounded-2xl border border-gray-200/80 p-3 flex flex-col justify-between hover:shadow-md transition snap-start group">
                                <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $bp->slug]) }}" class="block">
                                    <div class="aspect-square studio-card-box rounded-xl overflow-hidden mb-2 p-2 flex items-center justify-center">
                                        <img src="{{ $bp->image_url }}" alt="{{ $bp->name }}" class="max-w-full max-h-full object-contain crisp-img group-hover:scale-105 transition duration-300">
                                    </div>
                                    <div class="text-[10px] font-bold text-indigo-600 mb-0.5 truncate">{{ $bp->category->name ?? 'Sanitary' }}</div>
                                    <h4 class="text-xs font-black text-gray-900 line-clamp-2 leading-snug group-hover:text-indigo-600 transition">{{ $bp->name }}</h4>
                                </a>
                                <div class="mt-2 pt-2 border-t border-gray-200/60 flex items-center justify-between">
                                    <div>
                                        <span class="text-xs font-black text-emerald-700">₹{{ number_format($bp->price, 2) }}</span>
                                        @if($bp->mrp > $bp->price)
                                            <span class="text-[10px] text-gray-400 line-through block">₹{{ number_format($bp->mrp, 2) }}</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $bp->slug]) }}" class="px-2.5 py-1 rounded-lg bg-gray-900 hover:bg-indigo-600 text-white font-black text-[10px] transition">
                                        View
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- 🌪️ Interspersed Luxury Feature Spotlight (Tornado 360° Flush) -->
    @include('seller-site.components.interspersed-spotlight-banner', ['type' => 'tornado'])

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
            @php
                $cardComponent = isset($businessModule) ? $businessModule->getCardComponent() : 'seller-site.modules.card_hardware_pipes';
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2.5 sm:gap-4 md:gap-6">
                @foreach($products as $product)
                    @include($cardComponent, ['product' => $product, 'sellerPage' => $sellerPage])
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-10">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <!-- 📦 Interspersed Builder / Project Package Wholesale Banner -->
    @include('seller-site.components.interspersed-spotlight-banner', ['type' => 'project_package'])
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
        const discEl = document.getElementById(`card_disc_${prodId}`);
        const totalEl = document.getElementById(`card_total_price_${prodId}`);

        if (unitEl) unitEl.innerText = '₹' + state.unitPrice.toFixed(2);
        if (mrpEl) {
            if (state.mrp > state.unitPrice) {
                mrpEl.classList.remove('hidden');
                mrpEl.innerText = '₹' + state.mrp.toFixed(2);
                if (discEl) {
                    const disc = Math.round(((state.mrp - state.unitPrice) / state.mrp) * 100);
                    if (disc > 0) {
                        discEl.classList.remove('hidden');
                        discEl.innerText = disc + '% OFF';
                    } else {
                        discEl.classList.add('hidden');
                    }
                }
            } else {
                mrpEl.classList.add('hidden');
                if (discEl) discEl.classList.add('hidden');
            }
        }
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

    // 👗 Fashion Size Chip Selector
    function selectFashionSizeChip(prodId, varId, price, mrp, varName) {
        const varInput = document.getElementById(`card_variant_${prodId}`);
        if (varInput) {
            varInput.value = varId;
            varInput.setAttribute('data-price', price);
            varInput.setAttribute('data-mrp', mrp);
            varInput.setAttribute('data-name', varName);
        }
        document.querySelectorAll(`.fashion-size-chip[id^="chip_${prodId}_"]`).forEach(el => {
            el.className = "fashion-size-chip px-2.5 py-1 rounded-xl border text-[11px] font-black transition bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100";
        });
        const activeChip = document.getElementById(`chip_${prodId}_${varId}`);
        if (activeChip) {
            activeChip.className = "fashion-size-chip px-2.5 py-1 rounded-xl border text-[11px] font-black transition bg-gray-900 text-white border-gray-900 shadow-xs";
        }
        recalcCardTotal(prodId);
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
</script>
@endpush
@endsection
