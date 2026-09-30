<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="h-9 w-9 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-base shadow-sm">
                        V
                    </a>
                    <div>
                        <a href="{{ route('home') }}" class="font-black text-gray-900 text-base tracking-tight">VyaparIndia</a>
                        <span class="ml-2 text-xs font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200">
                            <i class="fa-solid fa-heart mr-1"></i> My Wishlist
                        </span>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('search') }}" class="text-xs font-bold text-gray-600 hover:text-blue-600 flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>Explore Marketplace</span>
                    </a>
                    <a href="{{ route('home') }}" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition">
                        Home
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

        <!-- Title & Stats -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-heart text-rose-500"></i>
                    <span>My Saved Wishlist</span>
                </h1>
                <p class="text-xs text-gray-500 mt-1">Bookmark your favorite factory materials, compare wholesale prices, and reorder anytime.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-700 bg-white border border-gray-200 px-3 py-1.5 rounded-xl shadow-xs">
                    Total Saved: <strong>{{ $wishlists->total() }}</strong> items
                </span>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($wishlists->isEmpty())
            <div class="bg-white border border-gray-200 rounded-3xl p-12 text-center max-w-lg mx-auto shadow-xs my-8">
                <div class="w-16 h-16 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center text-2xl mx-auto mb-4">
                    <i class="fa-regular fa-heart"></i>
                </div>
                <h3 class="text-base font-bold text-gray-900">Your wishlist is currently empty</h3>
                <p class="text-xs text-gray-500 mt-1">You haven't saved any products yet. Browse through the marketplace and click the heart icon on any product to save it here.</p>
                <div class="mt-6 flex justify-center gap-3">
                    <a href="{{ route('search') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>Start Browsing Products</span>
                    </a>
                </div>
            </div>
        @else
            <!-- Wishlist Products Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($wishlists as $wishlist)
                    @php
                        $product = $wishlist->product;
                        $seller = $product->seller;
                        $sellerPage = $seller->sellerPage ?? null;
                        $sellerProfile = $seller->sellerProfile ?? null;
                        $company = $sellerProfile->company_name ?? ($sellerPage->store_name ?? ($seller->name ?? 'Verified Store'));
                        $city = $sellerProfile->city ?? ($sellerPage->city ?? '');
                        $phone = $sellerProfile->phone ?? ($sellerPage->phone ?? '');
                        $subdomain = $sellerPage->subdomain ?? null;
                        
                        $primaryImg = $product->images->first()->image_path ?? $product->image_url;
                        $imgSrc = $primaryImg 
                            ? (Str::startsWith($primaryImg, ['http://', 'https://']) ? $primaryImg : asset('storage/' . $primaryImg)) 
                            : 'https://placehold.co/400x400/e2e8f0/475569?text=No+Photo';
                    @endphp
                    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:shadow-lg transition flex flex-col group relative">
                        <!-- Remove button (top right) -->
                        <form action="{{ route('wishlist.destroy', $product) }}" method="POST" class="absolute top-2.5 right-2.5 z-10" onsubmit="return confirm('Remove this item from your wishlist?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Remove from wishlist" class="h-8 w-8 rounded-full bg-white/90 hover:bg-rose-50 text-gray-400 hover:text-rose-600 flex items-center justify-center shadow-md backdrop-blur-xs transition">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </form>

                        <!-- Product Photo -->
                        <div class="relative bg-gray-100 aspect-square overflow-hidden">
                            <a href="{{ route('product.show', $product->slug) }}">
                                <img src="{{ $imgSrc }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            </a>
                            @if($product->category)
                                <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded-md bg-white/90 backdrop-blur-xs text-[10px] font-bold text-gray-800 shadow-xs">
                                    {{ $product->category->name }}
                                </span>
                            @endif
                        </div>

                        <!-- Card Content -->
                        <div class="p-4 flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="font-bold text-sm text-gray-900 group-hover:text-blue-600 transition line-clamp-2">
                                    <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                                </h3>

                                <!-- Store / Dealer Info -->
                                <p class="text-[11px] text-gray-500 mt-1 flex items-center gap-1">
                                    <i class="fa-solid fa-store text-gray-400"></i>
                                    <span>{{ $company }}</span>
                                    @if($city)
                                        <span class="text-gray-400">({{ $city }})</span>
                                    @endif
                                </p>

                                <!-- Price -->
                                <div class="mt-2.5 flex items-baseline gap-2">
                                    <span class="text-base font-black text-gray-900">₹{{ number_format($product->price, 2) }}</span>
                                    @if($product->wholesale_price)
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">
                                            Wholesale: ₹{{ number_format($product->wholesale_price, 2) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center gap-2">
                                <a href="{{ route('product.show', $product->slug) }}" class="flex-1 py-2 px-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold text-center transition flex items-center justify-center gap-1.5 shadow-sm">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                    <span>View Item</span>
                                </a>

                                @if($phone)
                                    <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $phone) }}?text=Hello%20{{ urlencode($company) }},%20I%20saved%20your%20product%20'{{ urlencode($product->name) }}'%20on%20my%20VyaparIndia%20Wishlist%20and%20want%20to%20place%20an%20order." target="_blank" class="py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition" title="Order on WhatsApp">
                                        <i class="fa-brands fa-whatsapp text-sm"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $wishlists->links() }}
            </div>
        @endif

    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-6 border-t border-gray-800 mt-auto text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <span>© {{ date('Y') }} VyaparIndia. All rights reserved.</span>
            <div class="flex gap-4">
                <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
                <a href="{{ route('search') }}" class="hover:text-white transition">Search</a>
            </div>
        </div>
    </footer>

</body>
</html>
