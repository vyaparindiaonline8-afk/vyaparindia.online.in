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
                    @auth
                        @if(Auth::user()->is_seller())
                            <a href="{{ route('seller.dashboard') }}" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold shadow-sm">
                                Seller Hub
                            </a>
                        @endif
                    @else
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold shadow-sm">
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
                <div class="aspect-square bg-gray-100 rounded-2xl overflow-hidden border border-gray-200 flex items-center justify-center">
                    <img src="{{ $imgSrc }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
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
                                <span>Order Directly on WhatsApp</span>
                            </a>
                        @endif

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

</body>
</html>
