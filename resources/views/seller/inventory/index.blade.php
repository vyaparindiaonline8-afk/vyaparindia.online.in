<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory & Stock Lifecycle Manager - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                        <span class="ml-2 text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Inventory & Restock</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" onclick="openRateUpdateModal()" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black transition flex items-center gap-1.5 shadow-md shadow-indigo-600/20 active:scale-95">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <span>Update Rates via Excel</span>
                    </button>
                    <a href="{{ route('seller.inventory.export_price_template') }}" class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition flex items-center gap-1.5" title="Download current rate list (.csv)">
                        <i class="fa-solid fa-download text-emerald-600"></i>
                        <span class="hidden sm:inline">Export Price List</span>
                    </a>
                    <a href="{{ route('seller.catalog.upload') }}" class="px-3 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold transition flex items-center gap-1.5 border border-blue-200">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span class="hidden sm:inline">Upload PDF</span>
                    </a>
                    <a href="{{ route('seller.dashboard') }}" class="text-xs font-bold text-gray-600 hover:text-blue-600 transition">
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs text-gray-500 font-medium">
            <a href="{{ route('seller.dashboard') }}" class="hover:text-blue-600">Seller Hub</a>
            <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
            <span class="text-gray-900 font-bold">Inventory & 1-Click Restock Manager</span>
        </nav>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Metrics Overview Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Products</span>
                    <div class="text-2xl sm:text-3xl font-black text-gray-900 mt-1">{{ $totalCatalogItems }}</div>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Low Stock Alert</span>
                    <div class="text-2xl sm:text-3xl font-black text-amber-600 mt-1">{{ $lowStockCount }}</div>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Out of Stock</span>
                    <div class="text-2xl sm:text-3xl font-black text-rose-600 mt-1">{{ $outOfStockCount }}</div>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Stock Control</span>
                    <div class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full mt-2 inline-block">
                        Auto-Deduct on Order
                    </div>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-arrows-spin"></i>
                </div>
            </div>
        </div>

        <!-- 📂 Category Filter & Instant WhatsApp Share Bar -->
        <div class="bg-white p-5 rounded-3xl border border-gray-200 shadow-xs space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <!-- Search & Category Filters -->
                <form action="{{ route('seller.inventory.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
                    <div class="relative min-w-[220px] flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products by name or SKU..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 text-xs font-medium focus:ring-2 focus:ring-indigo-600 focus:outline-none">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                    </div>

                    <select name="category" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-gray-200 text-xs font-bold bg-gray-50 focus:bg-white cursor-pointer">
                        <option value="">All Categories ({{ $totalCatalogItems }})</option>
                        @foreach($sellerCategories as $sc)
                            <option value="{{ $sc->id }}" {{ request('category') == $sc->id ? 'selected' : '' }}>
                                {{ $sc->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-4 py-2 rounded-xl bg-gray-900 text-white font-bold text-xs hover:bg-gray-800 transition">
                        Filter
                    </button>
                    @if(request('search') || request('category'))
                        <a href="{{ route('seller.inventory.index') }}" class="p-2 text-gray-400 hover:text-gray-700" title="Reset Filters">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </form>

                <!-- 📂 1-Click Category Share on WhatsApp -->
                @if(request('category') && $sellerPage)
                    @php
                        $selCat = $sellerCategories->firstWhere('id', request('category'));
                        $catTitle = $selCat ? $selCat->name : 'Selected Category';
                        $catPublicUrl = route('minisite.products', ['sellerPage' => $sellerPage->slug, 'category' => request('category')]);
                        $waCatText = urlencode("Namaste! VyaparIndia store se hamari *{$catTitle}* ki poori range aur latest ratelist yahan dekhein:\n{$catPublicUrl}");
                    @endphp
                    <div class="flex items-center gap-2 bg-emerald-50 px-3.5 py-2 rounded-2xl border border-emerald-200 shrink-0">
                        <span class="text-xs font-black text-emerald-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-layer-group text-emerald-600"></i>
                            <span>{{ $catTitle }}</span>
                        </span>
                        <a href="https://api.whatsapp.com/send?text={{ $waCatText }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs flex items-center gap-1.5 shadow-xs transition">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                            <span>Share on WhatsApp</span>
                        </a>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $catPublicUrl }}'); alert('Category link copied: {{ $catPublicUrl }}');" class="px-2.5 py-1.5 rounded-xl bg-white hover:bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-200 shadow-2xs transition" title="Copy Category Link">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Pro-Tip Banner -->
            <div class="pt-2 border-t border-gray-100 flex flex-wrap items-center justify-between gap-2 text-[11px] text-gray-500">
                <span class="flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info text-indigo-500"></i>
                    <span><strong>Pro-Tip:</strong> Niche table me se koi bhi <strong>3 se 6 products select</strong> karke unka direct WhatsApp link banayein aur buyer ko bhejein!</span>
                </span>
                @if($sellerPage)
                    <a href="{{ route('minisite.products', $sellerPage->slug) }}" target="_blank" class="text-indigo-600 font-bold hover:underline flex items-center gap-1">
                        <span>View Public Store</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                    </a>
                @endif
            </div>
        </div>

        <!-- Inventory Table Section -->
        <div class="bg-white rounded-3xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-extrabold text-gray-900">Live Inventory & Variant Matrix</h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Manage stocks per variant or product. Use 1-click +10, +50, +100 buttons to instantly restock.
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 font-bold border-b border-gray-200">
                            <th class="py-3 px-3 w-10 text-center">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAllProducts(this.checked)" class="rounded text-indigo-600 h-4 w-4 cursor-pointer" title="Select All">
                            </th>
                            <th class="py-3 px-4">Product / Item</th>
                            <th class="py-3 px-4">Variants / Sizes</th>
                            <th class="py-3 px-4">HSN & GST</th>
                            <th class="py-3 px-4">Wholesale / Retail</th>
                            <th class="py-3 px-4">Current Stock</th>
                            <th class="py-3 px-4 text-center">1-Click Restock Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50/60 transition">
                                <!-- Checkbox for Quick Curated Share -->
                                <td class="py-3.5 px-3 text-center">
                                    <input type="checkbox" class="product-curate-chk rounded text-indigo-600 h-4 w-4 cursor-pointer" value="{{ $product->id }}" data-name="{{ addslashes($product->name) }}" onchange="updateCuratedBar()">
                                </td>
                                <!-- Product Basic Info -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-12 w-12 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center flex-shrink-0">
                                            @if($product->image_url)
                                                <img src="{{ $product->image_url }}" class="w-full h-full object-contain">
                                            @else
                                                <i class="fa-solid fa-cube text-gray-400"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-extrabold text-gray-900 text-sm">{{ $product->name }}</h4>
                                            <p class="text-[11px] text-gray-400 font-mono">{{ $product->sku ?? 'NO-SKU' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Variants -->
                                <td class="py-3.5 px-4">
                                    @if($product->variants->count() > 0)
                                        <div class="space-y-1">
                                            @foreach($product->variants as $variant)
                                                <div class="flex items-center justify-between gap-3 text-[11px] bg-gray-50 px-2 py-1 rounded-lg border border-gray-100">
                                                    <span class="font-bold text-gray-800">{{ $variant->variant_name }}</span>
                                                    <div class="flex items-center gap-2">
                                                        @if($variant->track_inventory)
                                                            <span class="font-mono font-bold {{ $variant->stock_quantity <= 5 ? 'text-amber-600' : 'text-emerald-600' }}">
                                                                {{ $variant->stock_quantity }} units
                                                            </span>
                                                        @else
                                                            <span class="text-gray-400">On-demand</span>
                                                        @endif
                                                        <button type="button" onclick="quickRestock({{ $product->id }}, {{ $variant->id }}, 10)" class="px-1.5 py-0.5 rounded bg-blue-50 text-blue-600 hover:bg-blue-100 font-bold text-[10px]">
                                                            +10
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400">Single Variant</span>
                                    @endif
                                </td>

                                <!-- HSN & GST -->
                                <td class="py-3.5 px-4">
                                    <div class="font-mono font-bold text-gray-700">{{ $product->hsn_code ?? '39174000' }}</div>
                                    <div class="text-[11px] text-blue-600 font-bold">{{ $product->gst_percent ?? 18 }}% GST</div>
                                </td>

                                <!-- Pricing -->
                                <td class="py-3.5 px-4">
                                    <div class="text-indigo-900 font-bold">B2B: ₹{{ number_format($product->wholesale_price ?? $product->price, 2) }}</div>
                                    <div class="text-emerald-700 font-bold">D2C: ₹{{ number_format($product->price, 2) }}</div>
                                    @if($product->mrp)
                                        <div class="text-gray-400 line-through text-[10px]">MRP: ₹{{ number_format($product->mrp, 2) }}</div>
                                    @endif
                                </td>

                                <!-- Overall Stock Status -->
                                <td class="py-3.5 px-4">
                                    @if(!$product->track_inventory || is_null($product->stock_quantity))
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            <i class="fa-solid fa-infinity text-[10px]"></i>
                                            <span>Unmetered</span>
                                        </span>
                                    @elseif($product->stock_quantity <= 0)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class="fa-solid fa-xmark text-[10px]"></i>
                                            <span>Out of Stock (0)</span>
                                        </span>
                                    @elseif($product->stock_quantity <= 5)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                            <span id="stock_display_{{ $product->id }}">{{ $product->stock_quantity }} units (Low)</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fa-solid fa-check text-[10px]"></i>
                                            <span id="stock_display_{{ $product->id }}">{{ $product->stock_quantity }} units</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- 1-Click Restock Actions -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" onclick="quickRestock({{ $product->id }}, null, 10)" class="px-2.5 py-1.5 rounded-lg bg-gray-100 hover:bg-emerald-50 hover:text-emerald-700 text-gray-700 font-black text-xs transition border border-gray-200">
                                            +10
                                        </button>
                                        <button type="button" onclick="quickRestock({{ $product->id }}, null, 50)" class="px-2.5 py-1.5 rounded-lg bg-gray-100 hover:bg-emerald-50 hover:text-emerald-700 text-gray-700 font-black text-xs transition border border-gray-200">
                                            +50
                                        </button>
                                        <button type="button" onclick="quickRestock({{ $product->id }}, null, 100)" class="px-2.5 py-1.5 rounded-lg bg-gray-100 hover:bg-emerald-50 hover:text-emerald-700 text-gray-700 font-black text-xs transition border border-gray-200">
                                            +100
                                        </button>
                                        <button type="button" onclick="openCustomRestockModal({{ $product->id }}, '{{ addslashes($product->name) }}')" class="p-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Custom Quantity">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-400">
                                    <i class="fa-solid fa-boxes-stacked text-3xl mb-2"></i>
                                    <p class="font-bold text-gray-600">No products in inventory yet</p>
                                    <p class="text-xs mt-1">Upload a PDF catalog brochure to instantly populate your inventory.</p>
                                    <a href="{{ route('seller.catalog.upload') }}" class="inline-block mt-4 px-5 py-2 rounded-xl bg-blue-600 text-white font-bold text-xs">
                                        Upload PDF Catalog
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Custom Restock Modal -->
    <div id="restockModal" class="fixed inset-0 bg-black/50 backdrop-blur-xs hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="font-black text-gray-900 text-sm">Add Stock to Product</h3>
                <button type="button" onclick="closeRestockModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('seller.inventory.restock') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="product_id" id="modal_product_id">
                
                <div>
                    <span class="text-xs text-gray-500 font-semibold block mb-1">Product:</span>
                    <p class="text-xs font-bold text-gray-900" id="modal_product_name"></p>
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-700 block mb-1">Add Quantity (Units) <span class="text-rose-500">*</span></label>
                    <input type="number" name="quantity" min="1" required placeholder="e.g. 250" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 font-bold text-sm focus:border-blue-500">
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-700 block mb-1">Reason / Batch Note</label>
                    <input type="text" name="reason" placeholder="e.g. Factory Batch Received" value="Manual Seller Restock" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="closeRestockModal()" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md">
                        Confirm Restock
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 🎯 Floating Curated Selection Bar (3-6 items WhatsApp Share) -->
    <div id="curatedFloatingBar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-gray-900 text-white px-5 py-3.5 rounded-3xl shadow-2xl border border-gray-700 flex items-center gap-4 transition-all duration-300 hidden">
        <div class="flex items-center gap-2.5">
            <span class="h-8 w-8 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-xs font-black shadow-xs">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
            </span>
            <div class="leading-tight">
                <span class="text-xs font-black block"><span id="selectedProductCount">0</span> Products Selected</span>
                <span class="text-[10px] text-gray-400">Curated link ready to share</span>
            </div>
        </div>
        <div class="h-6 w-px bg-gray-700"></div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="shareCuratedOnWhatsapp()" class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-black text-xs flex items-center gap-1.5 shadow-md shadow-emerald-500/20 active:scale-95 transition">
                <i class="fa-brands fa-whatsapp text-sm"></i>
                <span>Share on WhatsApp</span>
            </button>
            <button type="button" onclick="copyCuratedShareLink()" class="px-3.5 py-2 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 font-bold text-xs flex items-center gap-1.5 border border-gray-600 transition" title="Copy curated link">
                <i class="fa-regular fa-copy"></i>
                <span>Copy Link</span>
            </button>
            <button type="button" onclick="clearCuratedSelection()" class="h-8 w-8 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white flex items-center justify-center text-xs transition" title="Clear selection">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    <!-- 📈 Modal: Quick Rate Revision via Excel/CSV -->
    <div id="rateUpdateModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs hidden items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="h-9 w-9 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Bulk Price / Rate List Update</h3>
                        <p class="text-[11px] text-gray-500">Excel ya CSV upload karein, rates turant update ho jayenge</p>
                    </div>
                </div>
                <button type="button" onclick="closeRateUpdateModal()" class="text-gray-400 hover:text-gray-600 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Download Template Helper -->
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="text-[11px] text-amber-900 leading-snug">
                    <strong>Pehle Current Rate Sheet Download Karein:</strong><br>
                    Isme aapke sabhi live products unke <strong>Product ID</strong> ke sath milenge. Name, rate ya stock badal kar wapas upload karein!
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('seller.products.export_sheet', ['type' => 'rates']) }}" class="px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs flex items-center gap-1 shadow-xs">
                        <i class="fa-solid fa-download"></i>
                        <span>दैनिक भाव (.csv)</span>
                    </a>
                    <a href="{{ route('seller.products.export_sheet', ['type' => 'full']) }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-xs flex items-center gap-1 shadow-xs">
                        <i class="fa-solid fa-download"></i>
                        <span>पूरा कैटलॉग (.csv)</span>
                    </a>
                </div>
            </div>

            <!-- Upload Area -->
            <div class="border-2 border-dashed border-gray-300 hover:border-indigo-500 rounded-2xl p-6 text-center bg-gray-50 cursor-pointer transition group" onclick="document.getElementById('rateFileInput').click()">
                <i class="fa-solid fa-file-excel text-emerald-600 text-3xl mb-2 group-hover:scale-110 transition"></i>
                <h4 class="text-xs font-bold text-gray-800">Upload Updated Excel (.xlsx, .xls, .csv)</h4>
                <p class="text-[11px] text-gray-400 mt-0.5">Click karein ya file yahan drop karein</p>
                <input type="file" id="rateFileInput" accept=".xlsx, .xls, .csv" class="hidden" onchange="handleRateFileSelect(event)">
            </div>

            <!-- Preview box -->
            <div id="rateFilePreviewBox" class="hidden p-3 bg-indigo-50 rounded-2xl border border-indigo-200 space-y-2">
                <div class="flex items-center justify-between text-xs font-bold text-indigo-900">
                    <span id="rateFileName">file.xlsx</span>
                    <span id="rateRowCount" class="px-2 py-0.5 rounded-full bg-indigo-200 text-indigo-800 text-[10px]">0 Rows Found</span>
                </div>
                <p class="text-[11px] text-gray-600">
                    Ready to update! Click below to apply new rates to database.
                </p>
            </div>

            <!-- Form -->
            <form action="{{ route('seller.inventory.bulk_rate_update') }}" method="POST" id="rateUpdateForm" onsubmit="return submitRateUpdateForm(event)">
                @csrf
                <input type="hidden" name="rate_rows" id="rateRowsJson" value="">
                
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" onclick="closeRateUpdateModal()" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold">
                        Cancel
                    </button>
                    <button type="submit" id="btnApplyRates" disabled class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-xs font-black shadow-md shadow-indigo-600/20 flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Apply New Rates</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const sellerSlug = @json($sellerPage ? $sellerPage->slug : null);

        function quickRestock(productId, variantId, qty) {
            fetch("{{ route('seller.inventory.restock') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({
                    product_id: productId,
                    variant_id: variantId,
                    quantity: qty,
                    reason: `1-Click Quick Restock (+${qty})`
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const el = document.getElementById('stock_display_' + productId);
                    if (el) {
                        el.textContent = data.new_stock + ' units';
                    }
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Error updating stock');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Restock request failed.');
            });
        }

        function openCustomRestockModal(productId, productName) {
            document.getElementById('modal_product_id').value = productId;
            document.getElementById('modal_product_name').textContent = productName;
            const modal = document.getElementById('restockModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeRestockModal() {
            const modal = document.getElementById('restockModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // ==========================================
        // 🎯 CURATED 3-6 PRODUCTS QUICK SHARE
        // ==========================================
        function toggleSelectAllProducts(checked) {
            document.querySelectorAll('.product-curate-chk').forEach(cb => {
                cb.checked = checked;
            });
            updateCuratedBar();
        }

        function updateCuratedBar() {
            const checkedBoxes = document.querySelectorAll('.product-curate-chk:checked');
            const count = checkedBoxes.length;
            const bar = document.getElementById('curatedFloatingBar');
            const countEl = document.getElementById('selectedProductCount');

            if (count > 0) {
                countEl.textContent = count;
                bar.classList.remove('hidden');
            } else {
                bar.classList.add('hidden');
            }
        }

        function clearCuratedSelection() {
            document.querySelectorAll('.product-curate-chk').forEach(cb => cb.checked = false);
            const selectAll = document.getElementById('selectAllCheckbox');
            if (selectAll) selectAll.checked = false;
            updateCuratedBar();
        }

        function getCuratedShareUrl() {
            if (!sellerSlug) {
                alert('Pehle apna MiniStore setup karein taaki public link generate ho sake!');
                return null;
            }
            const checked = Array.from(document.querySelectorAll('.product-curate-chk:checked')).map(cb => cb.value);
            if (checked.length === 0) return null;

            return window.location.origin + '/' + sellerSlug + '/products?items=' + checked.join(',');
        }

        function shareCuratedOnWhatsapp() {
            const url = getCuratedShareUrl();
            if (!url) return;

            const count = document.querySelectorAll('.product-curate-chk:checked').length;
            const msg = encodeURIComponent(`Namaste! VyaparIndia store se aapke liye chune hue khaas products (${count} items) ki details aur photo list yahan dekhein:\n${url}`);
            window.open('https://api.whatsapp.com/send?text=' + msg, '_blank');
        }

        function copyCuratedShareLink() {
            const url = getCuratedShareUrl();
            if (!url) return;

            navigator.clipboard.writeText(url).then(() => {
                alert('Curated products share link copied to clipboard!\n\n' + url);
            });
        }

        // ==========================================
        // 📈 BULK RATE UPDATE VIA EXCEL / CSV
        // ==========================================
        function openRateUpdateModal() {
            const modal = document.getElementById('rateUpdateModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeRateUpdateModal() {
            const modal = document.getElementById('rateUpdateModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function handleRateFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            document.getElementById('rateFileName').textContent = file.name;
            const previewBox = document.getElementById('rateFilePreviewBox');
            const rowCountEl = document.getElementById('rateRowCount');
            const btnApply = document.getElementById('btnApplyRates');

            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const sheetName = workbook.SheetNames[0];
                    const sheet = workbook.Sheets[sheetName];
                    const jsonRows = XLSX.utils.sheet_to_json(sheet, { defval: '' });

                    if (jsonRows.length === 0) {
                        alert('Sheet is empty or no valid rows found!');
                        return;
                    }

                    document.getElementById('rateRowsJson').value = JSON.stringify(jsonRows);
                    rowCountEl.textContent = `${jsonRows.length} Rows Detected`;
                    previewBox.classList.remove('hidden');
                    btnApply.disabled = false;
                } catch (err) {
                    console.error(err);
                    alert('Error parsing Excel file. Please ensure it is a valid .xlsx, .xls or .csv file.');
                }
            };
            reader.readAsArrayBuffer(file);
        }

        function submitRateUpdateForm(event) {
            const rowsJson = document.getElementById('rateRowsJson').value;
            if (!rowsJson) {
                alert('Pehle updated Excel sheet select karein!');
                event.preventDefault();
                return false;
            }

            const btn = document.getElementById('btnApplyRates');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Updating Rates...';
            return true;
        }
    </script>
</body>
</html>
