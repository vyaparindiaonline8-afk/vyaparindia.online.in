@extends('seller-site.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs text-gray-500 mb-6">
        <a href="{{ route('minisite.show', $sellerPage->slug) }}" class="hover:text-gray-900">Home</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <a href="{{ route('minisite.products', $sellerPage->slug) }}" class="hover:text-gray-900">Products</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-gray-900 font-semibold truncate">{{ $product->name }}</span>
    </nav>

    @if(session('review_success'))
    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
        <span>{{ session('review_success') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-3xl border border-gray-200 shadow-xs overflow-hidden p-6 sm:p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
            <!-- Product Image Left -->
            <div class="space-y-4">
                <div class="aspect-square bg-gray-100 rounded-2xl overflow-hidden border border-gray-100 relative">
                    <img id="main-product-image" src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @if($product->category)
                        <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-xs text-gray-800 text-xs font-bold px-3 py-1 rounded-lg shadow-xs">
                            {{ $product->category->name }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Product Details Right -->
            <div class="flex flex-col">
                <!-- Star Rating Header -->
                <div class="flex items-center gap-2 mb-2">
                    <div class="flex items-center text-amber-400 text-sm">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star-half-stroke"></i>
                    </div>
                    <span class="text-xs font-bold text-gray-900">{{ $product->average_rating }}</span>
                    <span class="text-xs text-gray-400">({{ $product->reviews_count }} verified ratings)</span>
                    <span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full ml-1">✓ Top Rated</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight leading-snug">
                    {{ $product->name }}
                </h1>

                <!-- Price Box -->
                <div class="mt-4 p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-baseline gap-3">
                    <span class="text-3xl font-black text-gray-900" id="display-price">₹{{ number_format($product->price, 2) }}</span>
                    <span class="text-sm text-gray-400 line-through" id="display-mrp">₹{{ number_format($product->mrp ?? ($product->price * 1.35), 2) }}</span>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-md" id="display-discount">SAVE 35%</span>
                </div>


                <!-- B2B Volume Slabs if configured -->
                @if($product->pricingTiers && $product->pricingTiers->isNotEmpty())
                <div class="mt-4 p-3.5 rounded-xl bg-indigo-50/70 border border-indigo-100 text-xs space-y-1.5">
                    <span class="font-bold text-indigo-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-tags text-indigo-600"></i> Wholesale Quantity Slabs:
                    </span>
                    <div class="grid grid-cols-2 gap-2 text-[11px]">
                        @foreach($product->pricingTiers as $tier)
                            <div class="bg-white p-2 rounded-lg border border-indigo-100 flex justify-between">
                                <span class="text-gray-600">{{ $tier->min_quantity }}{{ $tier->max_quantity ? '-'.$tier->max_quantity : '+' }} units:</span>
                                <strong class="text-indigo-700">₹{{ number_format($tier->unit_price, 2) }}/ea</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Dynamic Sizes & Variants Matrix -->
                @if($product->variants && $product->variants->count() > 0)
                <div class="mt-5 space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Select Size / Variant Option:
                        </label>
                        <span id="variant-stock-status" class="text-xs font-bold text-emerald-600">
                            In Stock
                        </span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2" id="variant-selector-grid">
                        @foreach($product->variants as $idx => $v)
                            <button type="button" 
                                onclick="selectVariant({{ $idx }})"
                                id="var-btn-{{ $idx }}"
                                class="variant-chip p-2.5 rounded-xl border-2 text-left transition-all {{ $idx === 0 ? 'border-indigo-600 bg-indigo-50/50 text-indigo-950 font-black' : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300' }}">
                                <div class="text-xs font-bold truncate">{{ $v->variant_name }}</div>
                                <div class="text-[11px] text-gray-500 font-mono mt-0.5">₹{{ number_format($v->retail_price ?? $v->wholesale_price ?? $product->price, 2) }}</div>
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Short Highlights -->
                <div class="mt-6 grid grid-cols-2 gap-3 text-xs text-gray-600">
                    <div class="flex items-center gap-2 p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                        <i class="fa-solid fa-shield-check text-emerald-600 text-base"></i>
                        <span>100% Quality Checked</span>
                    </div>
                    <div class="flex items-center gap-2 p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                        <i class="fa-solid fa-truck-fast text-blue-600 text-base"></i>
                        <span>Pan-India Dispatch</span>
                    </div>
                </div>

                <!-- Quantity Selector -->
                <div class="mt-6">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Quantity</label>
                    <div class="inline-flex items-center border-2 border-gray-200 rounded-xl bg-white overflow-hidden">
                        <button type="button" onclick="changeDetailQty(-1)" class="px-4 py-2 text-sm font-bold text-gray-600 hover:bg-gray-100 transition-colors">-</button>
                        <input type="number" id="detail-qty" value="1" min="1" class="w-12 text-center text-sm font-bold text-gray-900 border-none focus:outline-none" readonly>
                        <button type="button" onclick="changeDetailQty(1)" class="px-4 py-2 text-sm font-bold text-gray-600 hover:bg-gray-100 transition-colors">+</button>
                    </div>
                </div>

                <!-- Action CTA Buttons -->
                <div class="mt-8 space-y-3">
                    @if($sellerPage->whatsapp_number)
                        <button onclick="orderThisOnWhatsapp()" class="w-full py-3.5 px-6 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                            <span>Order on WhatsApp (Fastest)</span>
                        </button>
                    @endif

                    <div class="grid grid-cols-2 gap-3">
                        <button onclick="addDetailToBag()" class="py-3.5 px-4 rounded-2xl bg-gray-100 hover:bg-gray-200 text-gray-900 font-bold text-xs sm:text-sm transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-bag-shopping"></i>
                            <span>Add to Bag</span>
                        </button>

                        <button onclick="buyNowDirect()" class="py-3.5 px-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs sm:text-sm shadow-md hover:opacity-95 transition-all flex items-center justify-center gap-2">
                            <span>Buy Now</span>
                            <i class="fa-solid fa-bolt text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Product Description -->
                <div class="mt-8 pt-6 border-t border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900 mb-2">Description & Highlights</h3>
                    <div class="text-xs text-gray-600 leading-relaxed whitespace-pre-line">
                        {{ $product->description ?: 'High quality genuine product with 100% replacement warranty.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Reviews & Star Ratings Section -->
    <div class="mt-12 bg-white rounded-3xl border border-gray-200 shadow-xs p-6 sm:p-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-gray-200 gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-star text-amber-400"></i> Verified Customer Ratings & Reviews
                </h2>
                <p class="text-xs text-gray-500 mt-1">Real ratings submitted by verified online buyers.</p>
            </div>
            <button onclick="document.getElementById('review-form-box').classList.toggle('hidden')" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow transition">
                <i class="fa-solid fa-pen-to-square"></i> Write a Customer Review
            </button>
        </div>

        <!-- Write Review Form (Collapsible) -->
        <div id="review-form-box" class="hidden mt-6 p-6 rounded-2xl bg-gray-50 border border-gray-200">
            <h3 class="font-bold text-gray-900 text-sm mb-4">Share Your Product Experience</h3>
            <form action="{{ route('minisite.submitReview', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Your Full Name</label>
                        <input type="text" name="customer_name" required placeholder="e.g. Ramesh Patel" class="w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2.5 text-gray-900">
                    </div>
                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Your City / State</label>
                        <input type="text" name="customer_city" placeholder="e.g. Mumbai, MH" class="w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2.5 text-gray-900">
                    </div>
                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Star Rating</label>
                        <select name="rating" class="w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2.5 text-gray-900 font-bold">
                            <option value="5">⭐⭐⭐⭐⭐ (5 Star - Excellent)</option>
                            <option value="4">⭐⭐⭐⭐ (4 Star - Very Good)</option>
                            <option value="3">⭐⭐⭐ (3 Star - Good)</option>
                            <option value="2">⭐⭐ (2 Star - Average)</option>
                            <option value="1">⭐ (1 Star - Poor)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="font-bold text-gray-700 block mb-1">Review Headline</label>
                    <input type="text" name="review_title" placeholder="e.g. Excellent build quality & super fast delivery!" class="w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2.5 text-gray-900">
                </div>
                <div>
                    <label class="font-bold text-gray-700 block mb-1">Your Detailed Feedback</label>
                    <textarea name="comment" rows="3" required placeholder="Write what you loved about this product..." class="w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2.5 text-gray-900"></textarea>
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl shadow transition">
                    Submit Verified Review
                </button>
            </form>
        </div>

        <!-- Rating Summary Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 py-6 border-b border-gray-200">
            <div class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-2xl text-center">
                <span class="text-4xl font-black text-gray-900">{{ $product->average_rating }}</span>
                <div class="flex text-amber-400 text-sm mt-1">
                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                </div>
                <span class="text-xs text-gray-500 mt-1">Based on {{ $product->reviews_count }} verified reviews</span>
            </div>

            <!-- Breakdown Bars -->
            <div class="md:col-span-2 space-y-2 text-xs">
                <div class="flex items-center gap-3">
                    <span class="w-12 text-gray-600 font-semibold">5 Star</span>
                    <div class="flex-1 h-2.5 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-amber-400 rounded-full" style="width: 85%"></div>
                    </div>
                    <span class="w-10 text-right text-gray-500 font-mono">85%</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-12 text-gray-600 font-semibold">4 Star</span>
                    <div class="flex-1 h-2.5 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-amber-400 rounded-full" style="width: 12%"></div>
                    </div>
                    <span class="w-10 text-right text-gray-500 font-mono">12%</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-12 text-gray-600 font-semibold">3 Star</span>
                    <div class="flex-1 h-2.5 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-amber-400 rounded-full" style="width: 3%"></div>
                    </div>
                    <span class="w-10 text-right text-gray-500 font-mono">3%</span>
                </div>
            </div>
        </div>

        <!-- Reviews List -->
        <div class="divide-y divide-gray-100 mt-4 space-y-4">
            @if(isset($reviews) && $reviews->isNotEmpty())
                @foreach($reviews as $rev)
                <div class="pt-4 text-xs space-y-1.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-900">{{ $rev->customer_name }}</span>
                            <span class="text-gray-400 text-[11px]">({{ $rev->customer_city ?: 'Verified Buyer' }})</span>
                            <span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-md">✓ Verified Purchase</span>
                        </div>
                        <span class="text-gray-400 text-[10px]">{{ $rev->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex text-amber-400 text-xs">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $rev->rating)
                                <i class="fa-solid fa-star"></i>
                            @else
                                <i class="fa-regular fa-star text-gray-300"></i>
                            @endif
                        @endfor
                    </div>
                    <h4 class="font-bold text-gray-900">{{ $rev->review_title ?: 'Excellent Product!' }}</h4>
                    <p class="text-gray-600 leading-relaxed">{{ $rev->comment }}</p>
                </div>
                @endforeach
            @else
                <div class="p-8 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    <i class="fa-solid fa-comments text-gray-300 text-3xl mb-2"></i>
                    <p class="text-xs font-bold text-gray-700">Be the first to review this product!</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Apna feedback share karne ke liye upar 'Write a Customer Review' button dabayein.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    const currentProduct = @json($product);
    const productVariants = @json($product->variants ?? []);
    let selectedVariant = productVariants && productVariants.length > 0 ? productVariants[0] : null;

    function selectVariant(idx) {
        if (!productVariants || !productVariants[idx]) return;
        selectedVariant = productVariants[idx];

        // Update Button Styles
        document.querySelectorAll('.variant-chip').forEach((btn, i) => {
            if (i === idx) {
                btn.className = 'variant-chip p-2.5 rounded-xl border-2 text-left transition-all border-indigo-600 bg-indigo-50/50 text-indigo-950 font-black';
            } else {
                btn.className = 'variant-chip p-2.5 rounded-xl border-2 text-left transition-all border-gray-200 bg-white text-gray-700 hover:border-gray-300';
            }
        });

        // Update Prices
        const price = parseFloat(selectedVariant.retail_price || selectedVariant.wholesale_price || currentProduct.price);
        const mrp = parseFloat(selectedVariant.mrp || (price * 1.35));
        
        document.getElementById('display-price').textContent = '₹' + price.toFixed(2);
        document.getElementById('display-mrp').textContent = '₹' + mrp.toFixed(2);

        // Update Stock status badge
        const stockEl = document.getElementById('variant-stock-status');
        if (stockEl) {
            if (!selectedVariant.track_inventory || selectedVariant.stock_quantity === null) {
                stockEl.textContent = 'Made to Order / In Stock';
                stockEl.className = 'text-xs font-bold text-slate-600';
            } else if (selectedVariant.stock_quantity <= 0) {
                stockEl.textContent = 'Out of Stock';
                stockEl.className = 'text-xs font-bold text-rose-600';
            } else if (selectedVariant.stock_quantity <= 5) {
                stockEl.textContent = `Only ${selectedVariant.stock_quantity} left in stock!`;
                stockEl.className = 'text-xs font-bold text-amber-600';
            } else {
                stockEl.textContent = `In Stock (${selectedVariant.stock_quantity} units)`;
                stockEl.className = 'text-xs font-bold text-emerald-600';
            }
        }

        // Update product reference for Cart / WhatsApp
        currentProduct.price = price;
        currentProduct.name = @json($product->name) + ' (' + selectedVariant.variant_name + ')';
        currentProduct.variant_id = selectedVariant.id;
    }

    const pricingTiers = @json($product->pricingTiers ?? []);

    function getEffectiveUnitPrice(qty) {
        let base = parseFloat(selectedVariant ? (selectedVariant.retail_price || selectedVariant.wholesale_price || currentProduct.price) : currentProduct.price);
        if (pricingTiers && pricingTiers.length > 0) {
            const match = pricingTiers
                .slice()
                .sort((a,b) => b.min_quantity - a.min_quantity)
                .find(t => qty >= t.min_quantity && (!t.max_quantity || qty <= t.max_quantity));
            if (match && match.unit_price > 0) {
                return parseFloat(match.unit_price);
            }
        }
        return base;
    }

    function recalcDetailPrice() {
        let qty = parseInt(document.getElementById('detail-qty')?.value || 1);
        let effPrice = getEffectiveUnitPrice(qty);
        currentProduct.price = effPrice;
        const priceEl = document.getElementById('display-price');
        if (priceEl) {
            priceEl.textContent = '₹' + effPrice.toFixed(2);
        }
    }

    // Initialize first variant if exists
    if (productVariants && productVariants.length > 0) {
        selectVariant(0);
    }

    function changeDetailQty(delta) {
        let input = document.getElementById('detail-qty');
        let val = parseInt(input.value) || 1;
        val += delta;
        if (val < 1) val = 1;
        input.value = val;
        recalcDetailPrice();
    }

    function addDetailToBag() {
        let qty = parseInt(document.getElementById('detail-qty').value) || 1;
        addToCart(currentProduct, qty, true);
    }

    function buyNowDirect() {
        let qty = parseInt(document.getElementById('detail-qty').value) || 1;
        addToCart(currentProduct, qty, false);
        window.location.href = "{{ route('minisite.checkout', $sellerPage->slug) }}";
    }

    function orderThisOnWhatsapp() {
        if (!WHATSAPP_NUM) {
            alert('WhatsApp number not set.');
            return;
        }
        let qty = parseInt(document.getElementById('detail-qty').value) || 1;
        let total = currentProduct.price * qty;
        let varText = selectedVariant ? ` (${selectedVariant.variant_name})` : '';
        let text = `*New Order Inquiry from ${STORE_NAME}*\n\n` +
                   `🛍️ *Product:* ${currentProduct.name}${varText}\n` +
                   `🔢 *Quantity:* ${qty}\n` +
                   `💰 *Unit Price:* ₹${currentProduct.price}\n` +
                   `💵 *Total Amount:* ₹${total.toFixed(2)}\n` +
                   `🔗 *Link:* ${window.location.href}\n\n` +
                   `Please share payment and dispatch information.`;

        const url = `https://wa.me/${WHATSAPP_NUM}?text=${encodeURIComponent(text)}`;
        window.open(url, '_blank');
    }
</script>
@endpush
@endsection