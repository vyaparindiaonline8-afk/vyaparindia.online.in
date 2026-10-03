<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - VyaparIndia Verified B2B Marketplace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">

    <!-- Top Navigation -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="h-10 w-10 rounded-2xl bg-blue-600 text-white font-black flex items-center justify-center text-lg shadow-sm">
                        V
                    </a>
                    <div>
                        <a href="{{ route('home') }}" class="font-black text-gray-900 text-lg tracking-tight">VyaparIndia</a>
                        <span class="hidden sm:inline-block ml-2 text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">Product Marketplace</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('search') }}" class="text-xs font-bold text-gray-700 hover:text-blue-600 transition flex items-center gap-1">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>Search Catalog</span>
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="text-xs font-bold text-gray-700 hover:text-rose-600 transition flex items-center gap-1" title="My Saved Wishlist">
                        <i class="fa-solid fa-heart text-rose-500"></i>
                        <span class="hidden sm:inline">Wishlist</span>
                    </a>
                    @auth
                        @if(Auth::user()->is_seller())
                            <a href="{{ route('seller.dashboard') }}" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold shadow-sm">
                                Seller Hub
                            </a>
                        @endif
                    @else
                        <a href="{{ route('register', ['role' => 'seller']) }}" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold shadow-sm">
                            Seller Registration
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    @php
        $seller = $product->seller;
        $sp = $seller ? $seller->sellerProfile : null;
        $site = $seller ? $seller->sellerPage : null;
        $primaryImg = $product->images->first()->image_path ?? $product->image_url;
        $imgSrc = $primaryImg 
            ? (Str::startsWith($primaryImg, ['http://', 'https://']) ? $primaryImg : asset('storage/' . $primaryImg)) 
            : 'https://placehold.co/600x600/e2e8f0/475569?text=No+Photo';
        $sellerPhone = $sp->phone_number ?? ($site->whatsapp_number ?? '');
        $sellerPhoneClean = preg_replace('/[^0-9]/', '', $sellerPhone);
    @endphp

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-blue-600">Home</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="{{ route('search', ['category_id' => $product->category_id]) }}" class="hover:text-blue-600">
                {{ $product->category->name ?? 'General Category' }}
            </a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-gray-900 font-bold truncate max-w-xs">{{ $product->name }}</span>
        </nav>

        <!-- Product Hero Card -->
        <div class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-8 shadow-xs grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
            <!-- Product Image -->
            <div class="space-y-4">
                <div class="aspect-square bg-gray-100 rounded-2xl overflow-hidden border border-gray-200 flex items-center justify-center relative group">
                    <img src="{{ $imgSrc }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    <!-- Floating Wishlist Heart Button -->
                    <form action="{{ route('wishlist.toggle', $product) }}" method="POST" class="absolute top-3 right-3 z-10">
                        @csrf
                        @php $isFav = auth()->check() && auth()->user()->hasWishlisted($product); @endphp
                        <button type="submit" title="{{ $isFav ? 'Remove from Wishlist' : 'Save to Wishlist' }}" class="h-10 w-10 rounded-full bg-white/90 hover:bg-white text-{{ $isFav ? 'rose-600' : 'gray-400' }} hover:text-rose-600 flex items-center justify-center shadow-md backdrop-blur-xs transition">
                            <i class="fa-{{ $isFav ? 'solid' : 'regular' }} fa-heart text-base"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Product Details -->
            <div class="flex flex-col justify-between space-y-6">
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        @if($product->category)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $product->category->name }}
                            </span>
                        @endif
                        @if($product->stock_quantity !== null)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $product->stock_quantity > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                {{ $product->stock_quantity > 0 ? $product->stock_quantity . ' Units In Stock' : 'Out of Stock' }}
                            </span>
                        @endif
                        <span class="text-[11px] text-gray-400">
                            <i class="fa-solid fa-eye mr-1"></i> {{ $product->views_count }} views
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
                        {{ $product->name }}
                    </h1>

                    <!-- Pricing Box -->
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-2">
                        <div class="flex items-baseline gap-3">
                            <span class="text-3xl font-black text-gray-900">₹{{ number_format($product->price, 2) }}</span>
                            @if($product->mrp && $product->mrp > $product->price)
                                <span class="text-sm text-gray-400 line-through">MRP ₹{{ number_format($product->mrp, 2) }}</span>
                            @endif
                        </div>
                        @if($product->wholesale_price)
                            <div class="text-xs font-bold text-emerald-700 bg-emerald-100/60 px-3 py-1.5 rounded-xl inline-block">
                                <i class="fa-solid fa-handshake mr-1"></i> Bulk Wholesale Price: ₹{{ number_format($product->wholesale_price, 2) }}
                            </div>
                        @endif
                        @if($product->gst_percent)
                            <p class="text-[11px] text-gray-500">GST: {{ $product->gst_percent }}% Applicable</p>
                        @endif
                    </div>

                    <!-- Description -->
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Product Description</h3>
                        <p class="text-xs sm:text-sm text-gray-700 leading-relaxed">
                            {{ $product->description ?: 'High quality verified commercial product listed directly by verified Indian manufacturer/wholesaler.' }}
                        </p>
                    </div>

                    <!-- 📦 B2B Multi-Size Wholesale Matrix Order Table -->
                    @if($product->variants && $product->variants->count() > 0)
                        <div class="mt-4 bg-slate-50 border border-slate-200 rounded-3xl p-4 sm:p-5 shadow-xs space-y-3">
                            <div class="flex items-center justify-between pb-2.5 border-b border-gray-200">
                                <div>
                                    <h3 class="text-xs sm:text-sm font-black text-gray-900 flex items-center gap-2">
                                        <i class="fa-solid fa-layer-group text-blue-600"></i>
                                        <span>Sizes & Wholesale Quantity Order Form</span>
                                    </h3>
                                    <p class="text-[11px] text-gray-500">Zaroorat ke sizes ke aage quantity dalein aur 1-click me order karein</p>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 font-mono">
                                    {{ $product->variants->count() }} Sizes
                                </span>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-gray-50 text-gray-500 text-[10px] font-bold uppercase border-b border-gray-200">
                                        <tr>
                                            <th class="p-2.5">Size / Dimension</th>
                                            <th class="p-2.5">Rate (₹)</th>
                                            <th class="p-2.5">MRP</th>
                                            <th class="p-2.5 text-center w-36">Quantity (Pcs)</th>
                                            <th class="p-2.5 text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 font-medium">
                                        @foreach($product->variants as $v)
                                            @php
                                                $vPrice = floatval($v->retail_price ?: ($v->wholesale_price ?: $product->price));
                                                $vMrp = floatval($v->mrp ?: ($vPrice * 1.35));
                                                $vSize = $v->size ?: ($v->variant_name ?: 'Standard');
                                            @endphp
                                            <tr class="hover:bg-blue-50/40 transition variant-matrix-row" data-id="{{ $v->id }}" data-size="{{ $vSize }}" data-price="{{ $vPrice }}">
                                                <td class="p-2.5 font-black text-gray-900 font-mono text-xs">
                                                    {{ $vSize }}
                                                </td>
                                                <td class="p-2.5 font-mono font-bold text-emerald-700">
                                                    ₹{{ number_format($vPrice, 2) }}
                                                </td>
                                                <td class="p-2.5 font-mono text-gray-400 text-[11px]">
                                                    @if($vMrp > $vPrice)
                                                        <span class="line-through">₹{{ number_format($vMrp, 2) }}</span>
                                                    @else
                                                        ₹{{ number_format($vPrice, 2) }}
                                                    @endif
                                                </td>
                                                <td class="p-2.5 text-center">
                                                    <div class="inline-flex items-center border border-gray-300 rounded-xl overflow-hidden bg-white shadow-2xs">
                                                        <button type="button" onclick="adjustMatrixQty({{ $v->id }}, -1)" class="w-7 h-7 flex items-center justify-center text-gray-600 hover:bg-gray-100 font-bold active:bg-gray-200 transition">-</button>
                                                        <input type="number" min="0" value="0" id="matrix_qty_{{ $v->id }}" oninput="recalcMatrixTotal()" class="w-12 h-7 text-center text-xs font-bold font-mono border-x border-gray-200 focus:outline-none focus:ring-1 focus:ring-blue-600">
                                                        <button type="button" onclick="adjustMatrixQty({{ $v->id }}, 1)" class="w-7 h-7 flex items-center justify-center text-gray-600 hover:bg-gray-100 font-bold active:bg-gray-200 transition">+</button>
                                                    </div>
                                                </td>
                                                <td class="p-2.5 text-right font-mono font-bold text-gray-900" id="matrix_subtotal_{{ $v->id }}">
                                                    ₹0.00
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Matrix Order Live Total Bar -->
                            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-gray-600">Selected Quantity:</span>
                                        <span class="text-xs font-black text-emerald-800" id="matrixSummaryQty">0 Pcs</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-gray-600">Estimated Total:</span>
                                        <span class="text-sm font-black text-gray-900" id="matrixSummaryTotal">₹0.00</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    @if($sellerPhoneClean)
                                        <button type="button" onclick="orderMatrixOnWhatsApp()" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 flex items-center gap-1.5 transition active:scale-95">
                                            <i class="fa-brands fa-whatsapp text-sm"></i>
                                            <span>Order Sizes on WhatsApp</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Seller Profile & WhatsApp Direct Action -->
                <div class="pt-4 border-t border-gray-100 space-y-4">
                    <div class="flex items-center justify-between p-3.5 rounded-2xl bg-blue-50/50 border border-blue-100">
                        <div>
                            <span class="text-[10px] font-bold text-blue-600 uppercase">Verified Supplier:</span>
                            <div class="font-bold text-xs text-gray-900 flex items-center gap-1.5 mt-0.5">
                                <span>{{ $sp->company_name ?? ($seller->name ?? 'Verified Dealer') }}</span>
                                <i class="fa-solid fa-circle-check text-blue-600 text-xs"></i>
                            </div>
                            <div class="text-[11px] text-gray-500 mt-0.5">
                                <i class="fa-solid fa-location-dot text-rose-500 mr-1"></i>
                                {{ $sp->city ?? ($site->city ?? 'Local Store') }} {{ $sp->dispatch_radius ? '(Dispatch radius: ' . $sp->dispatch_radius . 'km)' : '' }}
                            </div>
                        </div>

                        @if($site && $site->slug)
                            <a href="{{ route('minisite.show', $site->slug) }}" class="px-3 py-1.5 bg-white border border-blue-200 text-blue-700 rounded-xl text-xs font-bold hover:bg-blue-50 transition shadow-2xs">
                                Visit Store
                            </a>
                        @endif
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        @if($sellerPhoneClean)
                            <a href="https://wa.me/91{{ $sellerPhoneClean }}?text=Hello%20{{ urlencode($sp->company_name ?? 'Seller') }},%20I%20am%20interested%20in%20buying%20*{{ urlencode($product->name) }}*%20(Price:%20INR%20{{ $product->price }})%20on%20VyaparIndia.%20Please%20confirm%20availability." target="_blank" class="flex-1 py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md transition">
                                <i class="fa-brands fa-whatsapp text-base"></i>
                                <span>Order on WhatsApp</span>
                            </a>
                        @endif

                        <!-- Wishlist Action Button -->
                        <form action="{{ route('wishlist.toggle', $product) }}" method="POST" class="inline">
                            @csrf
                            @php $isFav = auth()->check() && auth()->user()->hasWishlisted($product); @endphp
                            <button type="submit" class="w-full sm:w-auto py-3 px-4 rounded-xl border border-rose-200 {{ $isFav ? 'bg-rose-50 text-rose-600' : 'bg-white text-gray-700 hover:bg-rose-50 hover:text-rose-600' }} font-bold text-xs flex items-center justify-center gap-2 shadow-xs transition">
                                <i class="fa-{{ $isFav ? 'solid' : 'regular' }} fa-heart text-sm"></i>
                                <span>{{ $isFav ? 'Wishlisted' : 'Save to Wishlist' }}</span>
                            </button>
                        </form>

                        @if($site && $site->slug)
                            <a href="{{ route('minisite.product', ['sellerPage' => $site->slug, 'productSlug' => $product->slug]) }}" class="py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md transition">
                                <i class="fa-solid fa-cart-plus"></i>
                                <span>Add to Mini-Store Cart</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 🎯 Similar & Recommended Products in this Category & Price Band -->
        @if(isset($similarProducts) && $similarProducts->isNotEmpty())
            <div class="mt-12">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-black text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-blue-600"></i>
                            <span>Similar & Recommended Products in this Price Range</span>
                        </h2>
                        <p class="text-xs text-gray-500">Compare matching alternatives and complementary items</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach($similarProducts as $sim)
                        @php
                            $sImg = $sim->images->first()->image_path ?? $sim->image_url;
                            $sImgSrc = $sImg 
                                ? (Str::startsWith($sImg, ['http://', 'https://']) ? $sImg : asset('storage/' . $sImg)) 
                                : 'https://placehold.co/400x400/e2e8f0/475569?text=No+Photo';
                        @endphp
                        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:shadow-md transition flex flex-col group">
                            <div class="aspect-square bg-gray-100 overflow-hidden relative">
                                <img src="{{ $sImgSrc }}" alt="{{ $sim->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @if($sim->category)
                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md bg-white/90 text-[10px] font-bold text-gray-800 shadow-2xs">
                                        {{ $sim->category->name }}
                                    </span>
                                @endif
                            </div>
                            <div class="p-3.5 flex-1 flex flex-col justify-between">
                                <div>
                                    <h4 class="font-bold text-xs text-gray-900 group-hover:text-blue-600 transition line-clamp-2">
                                        <a href="{{ route('product.show', $sim->slug) }}">{{ $sim->name }}</a>
                                    </h4>
                                    <div class="mt-2 text-sm font-black text-gray-900">
                                        ₹{{ number_format($sim->price, 2) }}
                                    </div>
                                </div>
                                <div class="mt-3 pt-2 border-t border-gray-100">
                                    <a href="{{ route('product.show', $sim->slug) }}" class="block text-center py-1.5 bg-gray-100 hover:bg-blue-600 hover:text-white rounded-lg text-xs font-bold text-gray-700 transition">
                                        View Alternative
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

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
                <a href="{{ route('search') }}" class="hover:text-white transition">Catalog</a>
                <a href="{{ route('marketplace.algorithm') }}" class="hover:text-white transition">Algorithm Guide</a>
                <a href="{{ route('register') }}" class="hover:text-white transition">Register Shop</a>
            </div>
        </div>
    </footer>

    <script>
        function adjustMatrixQty(id, delta) {
            const input = document.getElementById('matrix_qty_' + id);
            if (!input) return;
            let current = parseInt(input.value) || 0;
            current = Math.max(0, current + delta);
            input.value = current;
            recalcMatrixTotal();
        }

        function recalcMatrixTotal() {
            const rows = document.querySelectorAll('.variant-matrix-row');
            let totalQty = 0;
            let totalAmount = 0;

            rows.forEach(tr => {
                const id = tr.getAttribute('data-id');
                const price = parseFloat(tr.getAttribute('data-price') || 0);
                const input = document.getElementById('matrix_qty_' + id);
                const qty = input ? (parseInt(input.value) || 0) : 0;
                const subtotal = qty * price;

                const subtotalEl = document.getElementById('matrix_subtotal_' + id);
                if (subtotalEl) {
                    subtotalEl.innerText = '₹' + subtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }

                if (qty > 0) {
                    tr.classList.add('bg-blue-50/70');
                } else {
                    tr.classList.remove('bg-blue-50/70');
                }

                totalQty += qty;
                totalAmount += subtotal;
            });

            const summaryQtyEl = document.getElementById('matrixSummaryQty');
            const summaryTotalEl = document.getElementById('matrixSummaryTotal');
            if (summaryQtyEl) summaryQtyEl.innerText = `${totalQty} Pcs`;
            if (summaryTotalEl) summaryTotalEl.innerText = '₹' + totalAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function orderMatrixOnWhatsApp() {
            const rows = document.querySelectorAll('.variant-matrix-row');
            const selected = [];
            let totalQty = 0;
            let totalVal = 0;

            rows.forEach(tr => {
                const id = tr.getAttribute('data-id');
                const size = tr.getAttribute('data-size');
                const price = parseFloat(tr.getAttribute('data-price') || 0);
                const input = document.getElementById('matrix_qty_' + id);
                const qty = input ? (parseInt(input.value) || 0) : 0;
                if (qty > 0) {
                    const lineTotal = qty * price;
                    selected.push(`• *${size}*: ${qty} pcs @ ₹${price.toFixed(2)} = ₹${lineTotal.toFixed(2)}`);
                    totalQty += qty;
                    totalVal += lineTotal;
                }
            });

            if (selected.length === 0) {
                alert('Kripya kam se kam 1 size ki quantity dalein.');
                return;
            }

            const sellerName = "{{ $sp->company_name ?? ($seller->name ?? 'Seller') }}";
            const prodName = "{{ $product->name }}";
            const phone = "{{ $sellerPhoneClean }}";

            let msg = `Hello ${sellerName},\n\nI want to place an order for *${prodName}* via VyaparIndia:\n\n${selected.join('\n')}\n\n*Total Quantity:* ${totalQty} pcs\n*Estimated Value:* ₹${totalVal.toFixed(2)}\n\nPlease confirm availability and payment/delivery terms.`;

            window.open(`https://wa.me/91${phone}?text=${encodeURIComponent(msg)}`, '_blank');
        }
    </script>
</body>
</html>
