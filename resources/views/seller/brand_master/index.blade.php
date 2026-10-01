<!DOCTYPE html>
<html lang="hi" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ब्रांड मास्टर कैटलॉग व डिस्काउंट कैलकुलेटर | VyaparIndia</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; }
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
    </style>
</head>
<body class="min-h-full flex flex-col bg-slate-50 text-slate-800 antialiased pb-28">

    <!-- Top Sticky Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.dashboard') }}" class="h-9 w-9 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 flex items-center justify-center text-sm transition">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-black text-gray-900 text-base tracking-tight">केंद्रीय ब्रांड मास्टर कैटलॉग</span>
                            <span class="text-[11px] font-extrabold text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-200">
                                1-Click Less Calculator
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-500">
                            कंपनी लिस्ट प्राइज से अपना खरीद लेस व सेलिंग मार्जिन तय करके एक क्लिक में दुकान में जोड़ें
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <!-- Button to open 24-Page PDF Catalog Viewer -->
                    <button type="button" onclick="openCatalogModal(1)" 
                            class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white font-extrabold text-xs shadow-xs flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-book-open"></i>
                        <span>मूल कैटलॉग देखें (24 Pages PDF)</span>
                    </button>

                    <a href="{{ route('seller.products.index') }}" class="hidden sm:inline-flex text-xs font-bold text-gray-600 hover:text-blue-600 transition items-center gap-1.5 px-3 py-1.5 rounded-xl border border-gray-200 bg-white">
                        <i class="fa-solid fa-boxes-stacked"></i>
                        <span>मेरी दुकान</span>
                    </a>
                </div>

            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        <!-- Alerts -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-xs">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Brand Selector & Stats Bar -->
        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-xs flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">चुना गया ब्रांड:</span>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-blue-600 text-white font-extrabold text-sm shadow-xs">
                    <i class="fa-solid fa-award"></i>
                    <span>PLASTO (R C Plasto Pipes & Tanks)</span>
                </div>
                <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">
                    कुल 291 प्रोडक्ट्स
                </span>
            </div>

            <!-- Instant Search Input -->
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input type="text" id="liveSearchInput" onkeyup="filterLiveProducts()" 
                       placeholder="सर्च करें (e.g. Elbow, UPVC, 25mm, Brass)..." 
                       class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
            </div>
        </div>

        <!-- Category Filter Pills -->
        <div class="flex flex-wrap items-center gap-1.5">
            <a href="{{ route('seller.brand-master.index', ['brand' => $selectedBrand]) }}" 
               class="px-3 py-1 rounded-lg text-xs font-bold transition {{ empty($selectedCategory) ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-100' }}">
                सभी श्रेणियां (All 291)
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('seller.brand-master.index', ['brand' => $selectedBrand, 'category' => $cat]) }}" 
                   class="px-3 py-1 rounded-lg text-xs font-bold transition {{ $selectedCategory == $cat ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-100' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>

        <!-- Master Global Costing & Less Calculator Bar -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-950 rounded-3xl p-6 text-white shadow-xl space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-white/10 pb-4">
                <div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-calculator text-amber-400"></i>
                        <h2 class="text-base font-black">मास्टर डिस्काउंट व लेस कैलकुलेटर (Global Formula)</h2>
                    </div>
                    <p class="text-xs text-slate-300 mt-0.5">
                        कंपनी लिस्ट प्राइज से अपना खरीद लेस % घटाएं, GST जोड़ें और सेलिंग प्राइस एक साथ सेट करें।
                    </p>
                </div>
                <div class="text-xs text-amber-300 bg-amber-500/10 px-3 py-1.5 rounded-xl border border-amber-500/20 flex items-center gap-2">
                    <i class="fa-solid fa-bolt"></i>
                    <span>नीचे हर प्रोडक्ट पर अलग से भी बदल सकते हैं</span>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-4 items-end">
                
                <!-- Purchase Less % -->
                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1">
                        कंपनी खरीद Less %
                    </label>
                    <div class="relative">
                        <input type="number" id="global_purchase_less" value="45" min="0" max="90" step="0.5" 
                               class="w-full bg-white/10 border border-white/20 rounded-xl px-3 py-2 text-white text-sm font-bold focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <span class="absolute right-3 top-2 text-xs text-slate-400">%</span>
                    </div>
                </div>

                <!-- GST Slab -->
                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1">
                        GST Slab
                    </label>
                    <select id="global_gst" class="w-full bg-white/10 border border-white/20 rounded-xl px-3 py-2 text-white text-sm font-bold focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="18" class="text-gray-900" selected>18% (Pipes & Fittings)</option>
                        <option value="12" class="text-gray-900">12% (Hardware Goods)</option>
                        <option value="28" class="text-gray-900">28% (Luxury)</option>
                        <option value="5" class="text-gray-900">5%</option>
                        <option value="0" class="text-gray-900">0% (Nil)</option>
                    </select>
                </div>

                <!-- Selling Mode -->
                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1">
                        बिक्री का तरीका (Selling Mode)
                    </label>
                    <select id="global_selling_mode" class="w-full bg-white/10 border border-white/20 rounded-xl px-3 py-2 text-white text-sm font-bold focus:outline-none focus:ring-2 focus:ring-blue-400" onchange="updateModeLabel()">
                        <option value="less" class="text-gray-900" selected>बिक्री Less % (Market Rate)</option>
                        <option value="margin" class="text-gray-900">मुनाफा जोड़कर (Margin % on Cost)</option>
                    </select>
                </div>

                <!-- Selling Value % -->
                <div>
                    <label id="selling_value_label" class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1">
                        बिक्री Less %
                    </label>
                    <div class="relative">
                        <input type="number" id="global_selling_val" value="35" min="0" max="90" step="0.5" 
                               class="w-full bg-white/10 border border-white/20 rounded-xl px-3 py-2 text-white text-sm font-bold focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <span class="absolute right-3 top-2 text-xs text-slate-400">%</span>
                    </div>
                </div>

                <!-- Apply Button -->
                <div class="col-span-2 sm:col-span-4 lg:col-span-1">
                    <button type="button" onclick="applyGlobalCosting()" 
                            class="w-full py-2.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-md transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-arrows-rotate"></i>
                        <span>सब पर लागू करें</span>
                    </button>
                </div>

            </div>
        </div>

        <!-- Form for Bulk Import -->
        <form id="bulkImportForm" method="POST" action="{{ route('seller.brand-master.import') }}">
            @csrf
            <input type="hidden" name="brand_name" value="{{ $selectedBrand }}">

            <!-- Products Table Container -->
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" 
                                   class="h-4 w-4 rounded text-blue-600 focus:ring-blue-500 border-gray-300" checked>
                            <span class="text-xs font-bold text-gray-700">सभी चुनें (Select All)</span>
                        </label>
                    </div>
                    <div class="text-xs text-gray-500 font-medium">
                        दिख रहे प्रोडक्ट्स: <strong class="text-gray-900 font-bold" id="visibleCount">{{ count($products) }}</strong>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50/75 text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">
                                <th class="py-3 px-3 text-center w-10">चुनें</th>
                                <th class="py-3 px-3 text-center w-14">फ़ोटो</th>
                                <th class="py-3 px-4">प्रोडक्ट का नाम व कैटेगरी</th>
                                <th class="py-3 px-3">साइज़ व कोड</th>
                                <th class="py-3 px-4 text-right">कंपनी MRP (₹)</th>
                                <th class="py-3 px-4 text-center">खरीद Less %</th>
                                <th class="py-3 px-4 text-right">नेट खरीद (GST सहित)</th>
                                <th class="py-3 px-4 text-center">सेलिंग Less / मार्जिन</th>
                                <th class="py-3 px-4 text-right">सेलिंग प्राइस (Selling ₹)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs font-medium" id="productsTableBody">
                            @forelse($products as $p)
                                @php
                                    $sku = strtoupper($p->brand_name) . '-' . ($p->product_code ?: $p->id);
                                    $isAlreadyImported = in_array($sku, $importedSkus);
                                @endphp
                                <tr class="product-row hover:bg-blue-50/30 transition {{ $isAlreadyImported ? 'bg-emerald-50/20' : '' }}" 
                                    data-id="{{ $p->id }}" 
                                    data-list-price="{{ $p->list_price }}"
                                    data-name="{{ strtolower($p->product_name) }}"
                                    data-code="{{ strtolower($p->product_code ?? '') }}"
                                    data-cat="{{ strtolower($p->category_name) }}">
                                    
                                    <!-- Checkbox -->
                                    <td class="py-3 px-3 text-center">
                                        <input type="checkbox" name="selected_ids[]" value="{{ $p->id }}" 
                                               class="item-checkbox h-4 w-4 rounded text-blue-600 focus:ring-blue-500 border-gray-300 cursor-pointer"
                                               onchange="updateSelectionSummary()" checked>
                                    </td>

                                    <!-- Product Photo Thumbnail -->
                                    <td class="py-3 px-3 text-center">
                                        <img src="{{ $p->image_url ?? asset('images/catalog/plasto/page_2.jpg') }}" 
                                             class="w-11 h-11 object-contain rounded-xl border border-gray-200 bg-white p-0.5 shadow-2xs hover:scale-125 transition-transform cursor-pointer mx-auto"
                                             onclick="openImagePreview('{{ $p->image_url ?? asset('images/catalog/plasto/page_2.jpg') }}', '{{ addslashes($p->product_name) }}')"
                                             alt="{{ $p->product_name }}"
                                             loading="lazy">
                                    </td>

                                    <!-- Product Info -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-extrabold text-gray-900 text-sm flex items-center gap-2">
                                            <span>{{ $p->product_name }}</span>
                                            @if($isAlreadyImported)
                                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md">
                                                    दुकान में मौजूद है
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            {{ $p->category_name }} &bull; {{ $p->item_type ?? 'Fitting' }}
                                        </div>
                                    </td>

                                    <!-- Size & Code -->
                                    <td class="py-3.5 px-3">
                                        @if($p->size_mm)
                                            <span class="px-2 py-0.5 rounded-md bg-blue-50 font-bold text-blue-700 font-mono text-[11px]">
                                                {{ $p->size_mm }} MM
                                            </span>
                                        @elseif($p->size_inch)
                                            <span class="px-2 py-0.5 rounded-md bg-gray-100 font-bold text-gray-700 font-mono text-[11px]">
                                                {{ $p->size_inch }}
                                            </span>
                                        @endif
                                        <span class="text-gray-400 text-[10px] ml-1 font-mono">Code: {{ $p->product_code }}</span>
                                    </td>

                                    <!-- List Price -->
                                    <td class="py-3.5 px-4 text-right font-black text-gray-800 text-sm">
                                        ₹{{ number_format($p->list_price, 2) }}
                                    </td>

                                    <!-- Purchase Less % Input -->
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="inline-flex items-center gap-1 bg-gray-50 border border-gray-200 rounded-lg px-2 py-1">
                                            <input type="number" 
                                                   name="items[{{ $p->id }}][purchase_discount]" 
                                                   class="item-purchase-less w-12 text-center text-xs font-bold text-gray-800 bg-transparent focus:outline-none" 
                                                   value="45" step="0.5" 
                                                   oninput="recalculateRow(this.closest('.product-row'))">
                                            <span class="text-[10px] text-gray-400 font-bold">%</span>
                                        </div>
                                        <input type="hidden" name="items[{{ $p->id }}][gst_percent]" class="item-gst" value="{{ $p->default_gst_percent ?? 18 }}">
                                    </td>

                                    <!-- Net Cost with GST -->
                                    <td class="py-3.5 px-4 text-right font-bold text-slate-600">
                                        <span class="item-cost-display">₹0.00</span>
                                        <input type="hidden" name="items[{{ $p->id }}][purchase_price]" class="item-cost-val" value="0">
                                    </td>

                                    <!-- Selling Less / Margin Input -->
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="inline-flex items-center gap-1 bg-gray-50 border border-gray-200 rounded-lg px-2 py-1">
                                            <input type="number" 
                                                   class="item-selling-val w-12 text-center text-xs font-bold text-blue-700 bg-transparent focus:outline-none" 
                                                   value="35" step="0.5" 
                                                   oninput="recalculateRow(this.closest('.product-row'))">
                                            <span class="text-[10px] text-gray-400 font-bold">%</span>
                                        </div>
                                    </td>

                                    <!-- Final Selling Price Input (Directly Overridable) -->
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="inline-flex items-center gap-1 bg-emerald-50 border border-emerald-300 rounded-xl px-2.5 py-1">
                                            <span class="text-xs font-extrabold text-emerald-700">₹</span>
                                            <input type="number" 
                                                   name="items[{{ $p->id }}][selling_price]" 
                                                   class="item-selling-price w-16 text-right text-xs font-black text-emerald-800 bg-transparent focus:outline-none" 
                                                   value="0" step="0.5" 
                                                   oninput="updateSelectionSummary()">
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 text-center text-gray-400">
                                        <i class="fa-solid fa-box-open text-4xl mb-3 block"></i>
                                        कोई प्रोडक्ट नहीं मिला।
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sticky Bottom Floating Action Bar -->
            <div class="fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-gray-200 py-3.5 px-4 sm:px-8 z-40 shadow-2xl">
                <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
                    
                    <div class="flex items-center gap-4 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="font-extrabold text-gray-900 text-sm" id="selectedCountDisplay">0</span>
                            <span class="text-gray-500 font-medium">प्रोडक्ट्स चुने गए</span>
                        </div>
                        <span class="text-gray-300">|</span>
                        <div class="text-gray-500">
                            कुल कैटलॉग मूल्य: <strong class="text-gray-900 text-sm font-black" id="totalSellingValueDisplay">₹0.00</strong>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <a href="{{ route('seller.dashboard') }}" class="w-1/2 sm:w-auto px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-bold text-xs hover:bg-gray-100 transition text-center">
                            रद्द करें
                        </a>
                        <button type="submit" 
                                class="w-1/2 sm:w-auto px-7 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-xs shadow-lg shadow-blue-500/25 flex items-center justify-center gap-2 transition active:scale-[0.99]">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span>दुकान में जोड़ें (Import to Store) 🚀</span>
                        </button>
                    </div>

                </div>
            </div>

        </form>

    </div>

    <!-- 24-Page Official Plasto PDF Catalog Viewer Modal -->
    <div id="catalogModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-2 sm:p-4">
        <div class="bg-white rounded-3xl w-full max-w-5xl h-[92vh] flex flex-col shadow-2xl overflow-hidden">
            <!-- Modal Header -->
            <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-slate-900 text-white">
                <div class="flex items-center gap-3">
                    <span class="h-8 w-8 rounded-xl bg-rose-600 flex items-center justify-center text-white text-sm font-black">
                        <i class="fa-solid fa-file-pdf"></i>
                    </span>
                    <div>
                        <h3 class="font-extrabold text-sm text-white">PLASTO Official Product Catalog (24 Pages)</h3>
                        <p class="text-[11px] text-slate-300">RCPL Product Catalog - All Pipes, Fittings, Tanks & Solvents</p>
                    </div>
                </div>

                <!-- Page Navigation Controls -->
                <div class="flex items-center gap-2">
                    <button type="button" onclick="changeCatalogPage(-1)" class="px-2.5 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <span class="text-xs font-bold text-amber-300">
                        Page <span id="currentPageNum">1</span> / 24
                    </span>
                    <button type="button" onclick="changeCatalogPage(1)" class="px-2.5 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>

                    <!-- Jump To Dropdown -->
                    <select id="pageSelectDropdown" onchange="jumpToCatalogPage(this.value)" class="bg-white/10 border border-white/20 text-white text-xs font-bold rounded-lg px-2 py-1 focus:outline-none">
                        @for($p = 1; $p <= 24; $p++)
                            <option value="{{ $p }}" class="text-gray-900">Page {{ $p }}</option>
                        @endfor
                    </select>

                    <button type="button" onclick="closeCatalogModal()" class="ml-3 h-8 w-8 rounded-xl bg-white/10 hover:bg-rose-600 text-white flex items-center justify-center transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body (Image Container) -->
            <div class="flex-1 bg-slate-950 p-4 overflow-auto flex items-center justify-center">
                <img id="catalogPageImage" src="{{ asset('images/catalog/plasto/page_1.jpg') }}" 
                     class="max-h-full max-w-full object-contain rounded-lg shadow-2xl transition-all duration-150" 
                     alt="Plasto Catalog Page">
            </div>

            <!-- Modal Footer -->
            <div class="p-3 bg-slate-900 border-t border-white/10 flex items-center justify-between text-xs text-slate-400">
                <span>Tip: Click Previous/Next or select page number to browse full company rate list.</span>
                <button type="button" onclick="closeCatalogModal()" class="px-4 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold">
                    Close Viewer
                </button>
            </div>
        </div>
    </div>

    <!-- Single Image Zoom Preview Modal -->
    <div id="imagePreviewModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4" onclick="closeImagePreview()">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 text-center space-y-4 shadow-2xl" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b pb-2">
                <h4 id="previewModalTitle" class="font-extrabold text-sm text-gray-900 truncate"></h4>
                <button type="button" onclick="closeImagePreview()" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-2xl p-4">
                <img id="previewModalImg" src="" class="max-h-full max-w-full object-contain" alt="">
            </div>
            <button type="button" onclick="closeImagePreview()" class="w-full py-2 bg-blue-600 text-white text-xs font-bold rounded-xl hover:bg-blue-700">
                Close
            </button>
        </div>
    </div>

    <!-- Live Calculation & Filter JavaScript -->
    <script>
        let currentCatalogPage = 1;
        const totalCatalogPages = 24;

        function openCatalogModal(page = 1) {
            currentCatalogPage = page;
            updateCatalogModalImage();
            document.getElementById('catalogModal').classList.remove('hidden');
        }

        function closeCatalogModal() {
            document.getElementById('catalogModal').classList.add('hidden');
        }

        function changeCatalogPage(delta) {
            currentCatalogPage += delta;
            if (currentCatalogPage < 1) currentCatalogPage = 1;
            if (currentCatalogPage > totalCatalogPages) currentCatalogPage = totalCatalogPages;
            updateCatalogModalImage();
        }

        function jumpToCatalogPage(val) {
            currentCatalogPage = parseInt(val) || 1;
            updateCatalogModalImage();
        }

        function updateCatalogModalImage() {
            document.getElementById('currentPageNum').innerText = currentCatalogPage;
            document.getElementById('pageSelectDropdown').value = currentCatalogPage;
            document.getElementById('catalogPageImage').src = `/images/catalog/plasto/page_${currentCatalogPage}.jpg`;
        }

        function openImagePreview(url, title) {
            document.getElementById('previewModalImg').src = url;
            document.getElementById('previewModalTitle').innerText = title;
            document.getElementById('imagePreviewModal').classList.remove('hidden');
        }

        function closeImagePreview() {
            document.getElementById('imagePreviewModal').classList.add('hidden');
        }

        function filterLiveProducts() {
            const query = document.getElementById('liveSearchInput').value.toLowerCase().trim();
            const rows = document.querySelectorAll('.product-row');
            let visible = 0;

            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const code = row.getAttribute('data-code') || '';
                const cat = row.getAttribute('data-cat') || '';

                if (!query || name.includes(query) || code.includes(query) || cat.includes(query)) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('visibleCount').innerText = visible;
        }

        function updateModeLabel() {
            const mode = document.getElementById('global_selling_mode').value;
            const label = document.getElementById('selling_value_label');
            if (mode === 'less') {
                label.innerText = 'बिक्री Less %';
            } else {
                label.innerText = 'मुनाफा (Margin %)';
            }
        }

        function recalculateRow(row) {
            const listPrice = parseFloat(row.getAttribute('data-list-price')) || 0;
            const purchaseLess = parseFloat(row.querySelector('.item-purchase-less').value) || 0;
            const gst = parseFloat(document.getElementById('global_gst').value) || 18;
            const sellingMode = document.getElementById('global_selling_mode').value;
            const sellingVal = parseFloat(row.querySelector('.item-selling-val').value) || 0;

            // Purchase Cost = ListPrice - PurchaseLess% + GST%
            const netBase = listPrice * (1 - (purchaseLess / 100));
            const netCostWithGst = netBase * (1 + (gst / 100));

            // Selling Price
            let sellingPrice = 0;
            if (sellingMode === 'less') {
                // Selling at List Price - Selling Less %
                sellingPrice = listPrice * (1 - (sellingVal / 100));
            } else {
                // Selling at Cost + Margin %
                sellingPrice = netCostWithGst * (1 + (sellingVal / 100));
            }

            // Update UI
            row.querySelector('.item-cost-display').innerText = '₹' + netCostWithGst.toFixed(2);
            row.querySelector('.item-cost-val').value = netCostWithGst.toFixed(2);
            row.querySelector('.item-selling-price').value = Math.round(sellingPrice * 10) / 10;

            updateSelectionSummary();
        }

        function applyGlobalCosting() {
            const globalPurchaseLess = document.getElementById('global_purchase_less').value;
            const globalSellingVal = document.getElementById('global_selling_val').value;
            const globalGst = document.getElementById('global_gst').value;

            document.querySelectorAll('.product-row').forEach(row => {
                row.querySelector('.item-purchase-less').value = globalPurchaseLess;
                row.querySelector('.item-selling-val').value = globalSellingVal;
                row.querySelector('.item-gst').value = globalGst;
                recalculateRow(row);
            });
        }

        function toggleSelectAll(masterCheckbox) {
            document.querySelectorAll('.item-checkbox').forEach(cb => {
                cb.checked = masterCheckbox.checked;
            });
            updateSelectionSummary();
        }

        function updateSelectionSummary() {
            let count = 0;
            let totalVal = 0;

            document.querySelectorAll('.product-row').forEach(row => {
                const cb = row.querySelector('.item-checkbox');
                if (cb && cb.checked) {
                    count++;
                    const price = parseFloat(row.querySelector('.item-selling-price').value) || 0;
                    totalVal += price;
                }
            });

            document.getElementById('selectedCountDisplay').innerText = count;
            document.getElementById('totalSellingValueDisplay').innerText = '₹' + totalVal.toLocaleString('en-IN', { maximumFractionDigits: 2 });
        }

        // Initialize calculations on page load
        document.addEventListener('DOMContentLoaded', function() {
            applyGlobalCosting();
        });
    </script>

</body>
</html>
