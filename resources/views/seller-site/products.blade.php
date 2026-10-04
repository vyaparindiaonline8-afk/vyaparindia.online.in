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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                @foreach($products as $product)
                    @php
                        $variants = $product->variants;
                        $firstVar = $variants->first();
                        $initPrice = $firstVar ? ($firstVar->retail_price ?: ($firstVar->wholesale_price ?: $product->price)) : $product->price;
                        $initMrp = $firstVar ? ($firstVar->mrp ?: ($initPrice * 1.35)) : ($product->mrp ?: ($product->price * 1.35));
                    @endphp
                    <div class="bg-white rounded-2xl border border-gray-200 hover:border-indigo-300 hover:shadow-lg transition-all flex flex-col justify-between overflow-hidden group p-3.5 space-y-3" id="card_box_{{ $product->id }}">
                        <!-- Product Image & Category Badge -->
                        <div>
                            <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" class="relative block aspect-square bg-gray-50 rounded-xl overflow-hidden border border-gray-100 mb-3 flex items-center justify-center">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-contain p-2 group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="text-gray-300 text-center">
                                        <i class="fa-solid fa-image text-3xl"></i>
                                    </div>
                                @endif
                                @if($product->category)
                                    <span class="absolute top-2 left-2 bg-white/95 backdrop-blur-xs text-gray-800 text-[10px] font-black px-2 py-0.5 rounded-md shadow-2xs">
                                        {{ $product->category->name }}
                                    </span>
                                @endif
                            </a>

                            <!-- Product Title -->
                            <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" class="font-extrabold text-xs sm:text-sm text-gray-900 hover:text-indigo-600 line-clamp-2 transition-colors" title="{{ $product->name }}">
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

        // Check if product has volume pricing tiers
        if (prod.pricing_tiers && prod.pricing_tiers.length > 0) {
            const matchingTier = prod.pricing_tiers
                .slice()
                .sort((a,b) => b.min_quantity - a.min_quantity)
                .find(t => qty >= t.min_quantity && (!t.max_quantity || qty <= t.max_quantity));
            if (matchingTier && matchingTier.unit_price > 0) {
                unitPrice = parseFloat(matchingTier.unit_price);
            }
        }

        const total = unitPrice * qty;

        return {
            product: prod,
            variantId: variantId,
            variantName: variantName,
            quantity: qty,
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

        const prodUrl = window.location.origin + '/' + STORE_PAGE_SLUG + '/p/' + state.product.slug;
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
</script>
@endpush
@endsection
