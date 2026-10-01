<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Catalog Products - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.dashboard') }}" class="h-9 w-9 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-base shadow-sm">
                        V
                    </a>
                    <div>
                        <span class="font-black text-gray-900 text-base tracking-tight">VyaparIndia</span>
                        <span class="ml-2 text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Product Catalog</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('seller.catalog.upload') }}" class="px-3.5 py-1.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-file-pdf"></i> Upload Catalog PDF
                    </a>
                    <a href="{{ route('seller.dashboard') }}" class="text-xs font-bold text-gray-600 hover:text-blue-600 transition">
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Tier Status & Quota Banner -->
        @php
            $isProfileOnly = Auth::user()->isProfileOnly();
            $productCount = $products->count();
        @endphp

        @if($isProfileOnly)
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-3xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full bg-amber-200 text-amber-900 font-black text-xs">
                            Basic Profile Tier
                        </span>
                        <span class="text-xs font-bold text-amber-800">
                            Quota: <strong>{{ $productCount }} / 50</strong> products listed
                        </span>
                    </div>
                    <p class="text-xs text-amber-700">
                        Aapka Basic Profile plan active hai (Limit: 50 products). Unlimited products, custom domain, aur WhatsApp cart checkout ke liye Mini-Website activate karein.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('seller.minisite.create') }}" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-black text-xs shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-rocket"></i>
                        <span>Upgrade to Mini-Website</span>
                    </a>
                </div>
            </div>
        @else
            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-3xl p-4 sm:p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black text-emerald-900">Mini-Website Tier Active</span>
                            <span class="px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-800 text-[10px] font-bold">Unlimited Products</span>
                        </div>
                        <p class="text-[11px] text-emerald-700">Aapka branded online storefront live hai with direct WhatsApp order & cart checkout.</p>
                    </div>
                </div>
                <a href="{{ route('seller.minisite.edit') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 underline">
                    Customize Storefront
                </a>
            </div>
        @endif

        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900">Products ({{ $productCount }})</h1>
                <p class="text-xs text-gray-500">Manage pricing, variants, and stock of your catalog items.</p>
            </div>

            <div class="flex items-center gap-3">
                @if(Auth::user()->canAddProduct())
                    <a href="{{ route('seller.products.create') }}" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add Single Product</span>
                    </a>
                @else
                    <button disabled class="px-5 py-2.5 rounded-xl bg-gray-200 text-gray-400 font-bold text-xs cursor-not-allowed flex items-center gap-2" title="Limit reached (50/50)">
                        <i class="fa-solid fa-lock"></i>
                        <span>50 Products Limit Reached</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Product Table -->
        <div class="bg-white rounded-3xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 font-bold border-b border-gray-200">
                            <th class="py-3 px-4">Item</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Selling Price</th>
                            <th class="py-3 px-4">Variants</th>
                            <th class="py-3 px-4">Stock Status</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-gray-100 overflow-hidden flex items-center justify-center shrink-0 border border-gray-100">
                                            @if($product->image_url)
                                                <img src="{{ $product->image_url }}" class="h-full w-full object-contain">
                                            @else
                                                <i class="fa-solid fa-cube text-gray-300"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-extrabold text-gray-900 text-sm">{{ $product->name }}</div>
                                            <div class="text-[10px] text-gray-400 font-mono">{{ $product->sku ?? 'PRD-STD' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-semibold text-gray-700">
                                    {{ $product->category->name ?? 'General' }}
                                </td>
                                <td class="py-3 px-4 font-black text-gray-900">
                                    ₹{{ number_format($product->price, 2) }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($product->variants && $product->variants->count() > 0)
                                        <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-bold text-[10px]">
                                            {{ $product->variants->count() }} Variants
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-[11px]">Single Item</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if(!$product->track_inventory)
                                        <span class="text-gray-500 font-medium text-[11px]">On-Demand</span>
                                    @elseif($product->stock_quantity <= 0)
                                        <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold text-[10px]">Out of Stock</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px]">{{ $product->stock_quantity }} in stock</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('seller.products.edit', $product) }}" class="p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition" title="Edit Product">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('seller.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this product?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition" title="Delete Product">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-400">
                                    <i class="fa-solid fa-box-open text-4xl mb-2 text-gray-300"></i>
                                    <p class="font-bold text-gray-600">No products added yet</p>
                                    <p class="text-xs mt-1">Start listing items or upload a catalog PDF.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>
