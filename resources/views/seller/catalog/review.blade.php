<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review & Costing Staging Canvas - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        .crop-canvas-container {
            cursor: crosshair;
            position: relative;
            user-select: none;
        }
        .crop-selection-box {
            position: absolute;
            border: 2px dashed #3b82f6;
            background: rgba(59, 130, 246, 0.2);
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen pb-24">

    <!-- Top Sticky Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.catalog.upload') }}" class="h-9 w-9 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 flex items-center justify-center text-sm transition">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-black text-gray-900 text-base tracking-tight">Review Staging Canvas</span>
                            <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                                Draft Mode
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-500 font-mono truncate max-w-xs sm:max-w-md">
                            Source: {{ $job->filename }} ({{ count($extractedData['products'] ?? []) }} products grouped)
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <a href="{{ route('seller.catalog.export_excel', $job->id) }}" class="px-4 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-300 flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-file-excel text-emerald-600"></i>
                        <span>Download Excel (.xlsx)</span>
                    </a>

                    <button type="button" onclick="document.getElementById('catalogPublishForm').submit()" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-600/20 flex items-center gap-2 transition">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Publish to Live Store</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- 🤖 Vyapar AI Natural Language Prompt Copilot -->
        <div class="bg-gradient-to-r from-violet-950 via-indigo-900 to-slate-900 rounded-3xl p-5 sm:p-6 text-white shadow-xl space-y-3 border border-purple-500/20">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="h-9 w-9 rounded-xl bg-purple-500/30 flex items-center justify-center text-purple-300 text-base">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black tracking-tight">Vyapar AI Natural Language Copilot</h3>
                        <p class="text-[11px] text-purple-200">AI ko bolkar ya likhkar sabhi products ke rates, less discount aur margins auto-adjust karein.</p>
                    </div>
                </div>
                <span class="text-[11px] bg-purple-500/20 text-purple-300 border border-purple-500/30 px-3 py-1 rounded-full font-bold flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>AI Assistant Active</span>
                </span>
            </div>

            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <input type="text" id="ai_prompt_input" placeholder="e.g. Set pipes less 56% and fittings less 47%, wholesale margin 15%, retail 35%" class="w-full bg-white/10 border border-purple-300/30 rounded-2xl pl-10 pr-4 py-3 text-xs text-white placeholder:text-purple-300/60 focus:outline-none focus:ring-2 focus:ring-purple-400 font-medium" onkeydown="if(event.key === 'Enter') executeAiCommand()">
                    <i class="fa-solid fa-wand-magic-sparkles absolute left-3.5 top-3.5 text-purple-400 text-xs"></i>
                </div>
                <button type="button" onclick="executeAiCommand()" class="px-6 py-3 rounded-2xl bg-purple-600 hover:bg-purple-500 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1.5 shrink-0">
                    <i class="fa-solid fa-bolt"></i>
                    <span>Run AI</span>
                </button>
            </div>

            <!-- Quick AI Action Chips -->
            <div class="flex flex-wrap items-center gap-2 pt-1">
                <span class="text-[10px] font-bold text-purple-300 uppercase tracking-wider mr-1">Quick Prompts:</span>
                <button type="button" onclick="quickAiAction('plasto_standard')" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-[11px] text-purple-200 font-semibold border border-white/10 transition">
                    🏷️ Pipes Less 56% & Fittings Less 47%
                </button>
                <button type="button" onclick="quickAiAction('standard_margins')" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-[11px] text-purple-200 font-semibold border border-white/10 transition">
                    💼 Wholesale 15% & Retail 35%
                </button>
                <button type="button" onclick="quickAiAction('clear_stock')" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-[11px] text-purple-200 font-semibold border border-white/10 transition">
                    📦 Set On-Demand Stock (Clear All)
                </button>
                <button type="button" onclick="quickAiAction('gst_18')" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-[11px] text-purple-200 font-semibold border border-white/10 transition">
                    ⚖️ Standard 18% GST
                </button>
            </div>
        </div>

        <!-- Global Batch Costing & GST Rule Bar -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-950 rounded-3xl p-6 text-white shadow-xl space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-white/10 pb-4">
                <div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-calculator text-amber-400"></i>
                        <h2 class="text-base font-black">Global GST & Costing Matrix Controller</h2>
                    </div>
                    <p class="text-xs text-slate-300 mt-0.5">
                        Catalog list price se trade discount hata kar purchase nikalen, GST jodein aur wholesale/retail selling prices ek click me set karein.
                    </p>
                </div>
                <div class="text-xs text-amber-300 bg-amber-500/10 px-3 py-1.5 rounded-xl border border-amber-500/20 flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <span>Ek click me sabhi products par apply hoga</span>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1">
                        Trade Discount %
                    </label>
                    <div class="relative">
                        <input type="number" id="global_discount" value="47" min="0" max="90" step="0.5" class="w-full bg-white/10 border border-white/20 rounded-xl px-3 py-2 text-white text-sm font-bold focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <span class="absolute right-3 top-2 text-xs text-slate-400">%</span>
                    </div>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1">
                        GST Slab
                    </label>
                    <select id="global_gst" class="w-full bg-white/10 border border-white/20 rounded-xl px-3 py-2 text-white text-sm font-bold focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="18" class="text-gray-900" selected>18% (Standard/Pipes)</option>
                        <option value="5" class="text-gray-900">5% (Textiles/Apparel)</option>
                        <option value="12" class="text-gray-900">12% (Hardware/Goods)</option>
                        <option value="28" class="text-gray-900">28% (Luxury/Auto)</option>
                        <option value="0" class="text-gray-900">0% (Nil/Exempt)</option>
                    </select>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1">
                        Wholesale Margin %
                    </label>
                    <div class="relative">
                        <input type="number" id="global_wholesale_margin" value="15" min="0" max="100" class="w-full bg-white/10 border border-white/20 rounded-xl px-3 py-2 text-white text-sm font-bold focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <span class="absolute right-3 top-2 text-xs text-slate-400">%</span>
                    </div>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1">
                        Retail Margin %
                    </label>
                    <div class="relative">
                        <input type="number" id="global_retail_margin" value="35" min="0" max="200" class="w-full bg-white/10 border border-white/20 rounded-xl px-3 py-2 text-white text-sm font-bold focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <span class="absolute right-3 top-2 text-xs text-slate-400">%</span>
                    </div>
                </div>

                <div class="col-span-2 sm:col-span-4 md:col-span-1">
                    <button type="button" onclick="applyGlobalRules()" class="w-full py-2.5 px-4 rounded-xl bg-blue-500 hover:bg-blue-400 text-white font-black text-xs shadow-md transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-arrows-rotate"></i>
                        <span>Apply All</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Live Instant Search Bar -->
        <div class="flex items-center gap-3">
            <div class="relative flex-1">
                <input type="text" id="product_search" placeholder="Search by product name, category, or code (e.g. Elbow, Tee, UPVC Pipe, Brass)..." class="w-full bg-white border border-gray-200 rounded-2xl pl-10 pr-4 py-3 text-xs text-gray-800 placeholder:text-gray-400 shadow-xs focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium" oninput="filterProducts()">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-gray-400 text-xs"></i>
            </div>
            <div class="text-xs font-bold text-gray-500 bg-white border border-gray-200 px-4 py-3 rounded-2xl shadow-xs shrink-0">
                Showing <strong class="text-gray-900" id="visibleCount">{{ count($extractedData['products'] ?? []) }}</strong> Products
            </div>
        </div>

        <!-- Main Form -->
        <form action="{{ route('seller.catalog.publish', $job->id) }}" method="POST" id="catalogPublishForm" class="space-y-8">
            @csrf

            @forelse($extractedData['products'] as $pIdx => $prod)
                <div class="product-card bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden" id="product_card_{{ $pIdx }}" data-pidx="{{ $pIdx }}" data-name="{{ strtolower($prod['name']) }}" data-category="{{ strtolower($prod['category'] ?? '') }}">
                    
                    <!-- Card Top Header -->
                    <div class="bg-gray-50 border-b border-gray-200 px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="h-7 w-7 rounded-lg bg-blue-600 text-white text-xs font-black flex items-center justify-center">
                                #{{ $pIdx + 1 }}
                            </span>
                            <span class="text-xs font-bold text-gray-800 uppercase tracking-wider">
                                {{ $prod['name'] }}
                            </span>
                            <span class="text-[11px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200">
                                {{ count($prod['variants'] ?? []) }} Sizes / Variants
                            </span>
                            @if(!empty($prod['catalog_page']))
                                <span class="text-[11px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-200">
                                    {{ $prod['catalog_page'] }}
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="button" onclick="removeProduct({{ $pIdx }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-lg border border-rose-200 transition flex items-center gap-1">
                                <i class="fa-solid fa-trash-can"></i>
                                <span>Delete Product</span>
                            </button>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6 space-y-6">
                        
                        <!-- Parent Product Info Grid -->
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                            
                            <!-- Product Image Column (Left Side) -->
                            <div class="lg:col-span-3 space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-bold text-gray-700 block">
                                        Product Photo (Group)
                                    </label>
                                    @if(!empty($prod['catalog_page']))
                                        <span class="text-[10px] text-gray-400 font-mono">{{ $prod['catalog_page'] }}</span>
                                    @endif
                                </div>
                                <div class="w-full aspect-square rounded-2xl bg-gray-50 border border-gray-200 overflow-hidden relative group flex items-center justify-center p-2" id="img_box_{{ $pIdx }}">
                                    @if(!empty($prod['image_url']))
                                        <img src="{{ asset($prod['image_url']) }}" alt="{{ $prod['name'] }}" class="w-full h-full object-contain" id="img_tag_{{ $pIdx }}">
                                        <input type="hidden" name="products[{{ $pIdx }}][image_url]" id="img_val_{{ $pIdx }}" value="{{ $prod['image_url'] }}">
                                    @else
                                        <img src="{{ asset('images/catalog/plasto/items/upvc_elbow.jpg') }}" alt="Default" class="w-full h-full object-contain opacity-50" id="img_tag_{{ $pIdx }}">
                                        <input type="hidden" name="products[{{ $pIdx }}][image_url]" id="img_val_{{ $pIdx }}" value="images/catalog/plasto/items/upvc_elbow.jpg">
                                    @endif
                                </div>

                                <!-- Photo Controls -->
                                <div class="grid grid-cols-2 gap-2 pt-1">
                                    <button type="button" onclick="openPdfCropper({{ $pIdx }}, '{{ $prod['catalog_page'] ?? 'Page 3' }}')" class="py-1.5 px-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-[11px] border border-blue-200 flex items-center justify-center gap-1 transition">
                                        <i class="fa-solid fa-crop-simple"></i>
                                        <span>Crop PDF</span>
                                    </button>
                                    <button type="button" onclick="openImageLibrary({{ $pIdx }})" class="py-1.5 px-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] border border-indigo-200 flex items-center justify-center gap-1 transition">
                                        <i class="fa-solid fa-images"></i>
                                        <span>Pick Photo</span>
                                    </button>
                                </div>
                                <p class="text-[10px] text-gray-400 text-center leading-tight">
                                    Same photo used for all {{ count($prod['variants'] ?? []) }} sizes below
                                </p>
                            </div>

                            <!-- Editable Fields (Title, Category, HSN, Optional Stock) -->
                            <div class="lg:col-span-9 space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div class="sm:col-span-2">
                                        <label class="text-xs font-bold text-gray-700 block mb-1">
                                            Product Title <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="text" name="products[{{ $pIdx }}][name]" value="{{ $prod['name'] }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm font-bold text-gray-900">
                                    </div>

                                    <div>
                                        <label class="text-xs font-bold text-gray-700 block mb-1">
                                            Category
                                        </label>
                                        <select name="products[{{ $pIdx }}][category_id]" class="w-full px-3 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 text-xs font-semibold text-gray-800">
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ (stripos($prod['category'] ?? $prod['name'], $category->name) !== false) ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                    <div>
                                        <label class="text-xs font-bold text-gray-700 block mb-1">
                                            Trade Discount % (Less)
                                        </label>
                                        <div class="relative">
                                            <input type="number" step="0.5" min="0" max="95" name="products[{{ $pIdx }}][trade_discount_percent]" value="{{ $prod['trade_discount_percent'] ?? 47 }}" class="prod-discount w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold text-gray-900" oninput="recalculateProduct({{ $pIdx }})">
                                            <span class="absolute right-2.5 top-2 text-xs text-gray-400">%</span>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="text-xs font-bold text-gray-700 block mb-1">
                                            GST Rate %
                                        </label>
                                        <select name="products[{{ $pIdx }}][gst_percent]" class="prod-gst w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold text-gray-900" onchange="recalculateProduct({{ $pIdx }})">
                                            <option value="18" {{ ($prod['gst_percent'] ?? 18) == 18 ? 'selected' : '' }}>18% GST</option>
                                            <option value="5" {{ ($prod['gst_percent'] ?? 18) == 5 ? 'selected' : '' }}>5% GST</option>
                                            <option value="12" {{ ($prod['gst_percent'] ?? 18) == 12 ? 'selected' : '' }}>12% GST</option>
                                            <option value="28" {{ ($prod['gst_percent'] ?? 18) == 28 ? 'selected' : '' }}>28% GST</option>
                                            <option value="0" {{ ($prod['gst_percent'] ?? 18) == 0 ? 'selected' : '' }}>0% GST</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="text-xs font-bold text-gray-700 block mb-1">
                                            HSN / SAC Code
                                        </label>
                                        <input type="text" name="products[{{ $pIdx }}][hsn_code]" value="{{ $prod['hsn_code'] ?? '3917' }}" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-mono font-bold text-gray-900">
                                    </div>

                                    <div>
                                        <label class="text-xs font-bold text-gray-700 block mb-1">
                                            Total Stock <span class="text-gray-400 font-normal">(Optional)</span>
                                        </label>
                                        <input type="number" min="0" name="products[{{ $pIdx }}][stock_quantity]" value="{{ $prod['stock_quantity'] ?? '' }}" placeholder="On-demand (Empty)" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold text-gray-900 placeholder:text-gray-400 placeholder:font-normal">
                                    </div>
                                </div>

                                <div>
                                    <label class="text-xs font-bold text-gray-700 block mb-1">
                                        Commercial Description & Highlights
                                    </label>
                                    <textarea name="products[{{ $pIdx }}][description]" rows="2" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs text-gray-800 leading-relaxed">{{ $prod['description'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Multi-Variant Range Matrix -->
                        <div class="border-t border-gray-100 pt-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-table-list text-blue-600"></i>
                                    <h4 class="text-xs font-black text-gray-900 uppercase tracking-wider">
                                        Multi-Variant & Size Range Matrix ({{ count($prod['variants']) }} Variants)
                                    </h4>
                                </div>

                                <button type="button" onclick="addVariantRow({{ $pIdx }})" class="text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-xl transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                    <span>Add Variant / Size</span>
                                </button>
                            </div>

                            <!-- Variant Table -->
                            <div class="overflow-x-auto rounded-2xl border border-gray-200">
                                <table class="w-full text-left border-collapse text-xs" id="variant_table_{{ $pIdx }}">
                                    <thead>
                                        <tr class="bg-gray-100 text-gray-700 font-bold border-b border-gray-200">
                                            <th class="py-2.5 px-3">Variant / Size</th>
                                            <th class="py-2.5 px-3">Item Code (SKU)</th>
                                            <th class="py-2.5 px-3">List Price / MRP (₹)</th>
                                            <th class="py-2.5 px-3 bg-blue-50/50 text-blue-900">Landing Cost (Net)</th>
                                            <th class="py-2.5 px-3 bg-indigo-50/50 text-indigo-900">Wholesale (B2B)</th>
                                            <th class="py-2.5 px-3 bg-emerald-50/50 text-emerald-900">Retail (D2C)</th>
                                            <th class="py-2.5 px-3 text-center">Stock (Units)</th>
                                            <th class="py-2.5 px-3 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @foreach($prod['variants'] as $vIdx => $v)
                                            <tr class="variant-row hover:bg-gray-50/80 transition" id="vrow_{{ $pIdx }}_{{ $vIdx }}" data-vidx="{{ $vIdx }}">
                                                <td class="py-2 px-3">
                                                    <input type="text" name="products[{{ $pIdx }}][variants][{{ $vIdx }}][variant_name]" value="{{ $v['variant_name'] }}" required class="w-32 px-2 py-1.5 rounded-lg border border-gray-300 font-bold text-gray-900 text-xs">
                                                    <input type="hidden" name="products[{{ $pIdx }}][variants][{{ $vIdx }}][size]" value="{{ $v['size'] ?? $v['variant_name'] }}">
                                                    <input type="hidden" name="products[{{ $pIdx }}][variants][{{ $vIdx }}][grade]" value="{{ $v['grade'] ?? 'Standard' }}">
                                                </td>
                                                <td class="py-2 px-3">
                                                    <input type="text" name="products[{{ $pIdx }}][variants][{{ $vIdx }}][sku]" value="{{ $v['sku'] ?? ('SKU-' . ($vIdx+1)) }}" placeholder="Code" class="w-24 px-2 py-1.5 rounded-lg border border-gray-300 font-mono text-gray-800 text-xs">
                                                </td>
                                                <td class="py-2 px-3">
                                                    <div class="relative w-24">
                                                        <span class="absolute left-2 top-1.5 text-gray-400">₹</span>
                                                        <input type="number" step="0.5" name="products[{{ $pIdx }}][variants][{{ $vIdx }}][raw_rate]" value="{{ $v['raw_rate'] }}" required class="v-rawrate w-full pl-5 pr-1 py-1.5 rounded-lg border border-gray-300 font-bold text-gray-900 text-xs" oninput="recalculateProduct({{ $pIdx }})">
                                                    </div>
                                                </td>
                                                <td class="py-2 px-3 bg-blue-50/30">
                                                    <div class="text-xs font-bold text-blue-900 v-landing">
                                                        ₹{{ number_format($v['landing_cost_with_gst'] ?? 0, 2) }}
                                                    </div>
                                                    <div class="text-[10px] text-gray-400 v-landing-net">
                                                        Excl. GST: ₹{{ number_format($v['landing_cost_without_gst'] ?? 0, 2) }}
                                                    </div>
                                                </td>
                                                <td class="py-2 px-3 bg-indigo-50/30">
                                                    <div class="relative w-24">
                                                        <span class="absolute left-2 top-1.5 text-gray-400">₹</span>
                                                        <input type="number" step="0.5" name="products[{{ $pIdx }}][variants][{{ $vIdx }}][wholesale_price]" value="{{ $v['wholesale_price'] ?? '' }}" class="v-wholesale w-full pl-5 pr-1 py-1.5 rounded-lg border border-indigo-200 font-bold text-indigo-950 text-xs">
                                                    </div>
                                                </td>
                                                <td class="py-2 px-3 bg-emerald-50/30">
                                                    <div class="relative w-24">
                                                        <span class="absolute left-2 top-1.5 text-gray-400">₹</span>
                                                        <input type="number" step="0.5" name="products[{{ $pIdx }}][variants][{{ $vIdx }}][retail_price]" value="{{ $v['retail_price'] ?? '' }}" class="v-retail w-full pl-5 pr-1 py-1.5 rounded-lg border border-emerald-200 font-bold text-emerald-950 text-xs">
                                                    </div>
                                                </td>
                                                <td class="py-2 px-3 text-center">
                                                    <input type="number" min="0" name="products[{{ $pIdx }}][variants][{{ $vIdx }}][stock_quantity]" value="{{ $v['stock_quantity'] ?? '' }}" placeholder="Optional" class="w-16 px-2 py-1.5 rounded-lg border border-gray-300 text-center font-bold text-xs text-gray-900 placeholder:text-gray-300 placeholder:font-normal">
                                                    <input type="hidden" name="products[{{ $pIdx }}][variants][{{ $vIdx }}][mrp]" value="{{ $v['mrp'] ?? $v['raw_rate'] }}">
                                                </td>
                                                <td class="py-2 px-3 text-center">
                                                    <button type="button" onclick="removeVariantRow('vrow_{{ $pIdx }}_{{ $vIdx }}')" class="h-7 w-7 rounded-lg text-rose-500 hover:bg-rose-50 transition flex items-center justify-center">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            @empty
                <div class="bg-white rounded-3xl p-12 text-center border border-gray-200 space-y-4">
                    <div class="h-16 w-16 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto text-2xl">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">No Products Found</h3>
                        <p class="text-xs text-gray-500">The catalog did not contain recognizable commercial tables.</p>
                    </div>
                    <a href="{{ route('seller.catalog.upload') }}" class="inline-flex px-6 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs">
                        Try Another Catalog
                    </a>
                </div>
            @endforelse

            <!-- Sticky Bottom Publish Bar -->
            <div class="fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-md border-t border-gray-200 py-3.5 px-4 z-40 shadow-lg">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold text-gray-600">
                            Total Products: <strong class="text-gray-900" id="totalCount">{{ count($extractedData['products'] ?? []) }}</strong>
                        </span>
                        <span class="hidden sm:inline text-gray-300">•</span>
                        <span class="text-xs text-gray-500 hidden sm:inline">
                            Stock is optional ("Dale to thik, na dale to thik")
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('seller.catalog.export_excel', $job->id) }}" class="px-4 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold border border-emerald-200 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-file-excel text-emerald-600"></i>
                            <span>Export Excel</span>
                        </a>
                        <button type="submit" class="px-8 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-600/20 flex items-center gap-2 transition">
                            <i class="fa-solid fa-check-double"></i>
                            <span>Publish Live Catalog</span>
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </div>

    <!-- 📸 Image Picker & Visual PDF Cropper Modal -->
    <div id="imageModal" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-4xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl bg-blue-600 text-white flex items-center justify-center text-sm font-black">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-gray-900 text-sm">Product Photo Picker & PDF Cropper</h3>
                        <p class="text-[11px] text-gray-500" id="modalSubtext">Choose high-res image from brand gallery or crop from catalog page.</p>
                    </div>
                </div>

                <button type="button" onclick="closeImageModal()" class="h-8 w-8 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Tabs -->
            <div class="px-6 border-b border-gray-200 bg-white flex items-center gap-4">
                <button type="button" onclick="switchModalTab('library')" id="tabBtn_library" class="py-3 font-bold text-xs border-b-2 border-blue-600 text-blue-600 flex items-center gap-2">
                    <i class="fa-solid fa-images"></i>
                    <span>Brand HD Gallery</span>
                </button>
                <button type="button" onclick="switchModalTab('cropper')" id="tabBtn_cropper" class="py-3 font-bold text-xs border-b-2 border-transparent text-gray-500 hover:text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-crop-simple"></i>
                    <span>Crop from PDF Page</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 overflow-y-auto flex-1">
                
                <!-- Tab 1: Library Grid -->
                <div id="tabContent_library" class="space-y-4">
                    <p class="text-xs text-gray-500">Click any image to immediately assign it to this product:</p>
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3" id="libraryGrid">
                        <!-- Populated via JavaScript -->
                    </div>
                </div>

                <!-- Tab 2: PDF Page Cropper Canvas -->
                <div id="tabContent_cropper" class="hidden space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-gray-700">Catalog Page:</span>
                            <select id="cropperPageSelect" class="px-3 py-1.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-800" onchange="loadCropperPage(this.value)">
                                @for($p = 1; $p <= 24; $p++)
                                    <option value="{{ $p }}">Page {{ $p }}</option>
                                @endfor
                            </select>
                        </div>
                        <span class="text-xs text-blue-600 font-bold">
                            <i class="fa-solid fa-mouse-pointer mr-1"></i> Drag box on page to crop
                        </span>
                    </div>

                    <div class="border rounded-2xl bg-gray-100 overflow-auto max-h-[50vh] p-2 flex justify-center">
                        <div class="crop-canvas-container inline-block" id="cropContainer">
                            <img id="cropTargetImage" src="" alt="Catalog Page" class="max-w-none block" crossorigin="anonymous">
                            <div class="crop-selection-box hidden" id="cropSelectionBox"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <span class="text-[11px] text-gray-400" id="cropCoordinates">No region selected</span>
                        <button type="button" onclick="applyCanvasCrop()" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1.5">
                            <i class="fa-solid fa-check"></i>
                            <span>Save & Apply Cropped Photo</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        let currentTargetPidx = null;

        // Pre-defined Plasto high-res items gallery
        const plastoLibrary = [
            { name: "UPVC 90° Elbow", url: "images/catalog/plasto/items/upvc_elbow.jpg" },
            { name: "CPVC 90° Elbow", url: "images/catalog/plasto/items/cpvc_elbow.jpg" },
            { name: "UPVC Tee", url: "images/catalog/plasto/items/upvc_tee.jpg" },
            { name: "CPVC Tee", url: "images/catalog/plasto/items/cpvc_tee.jpg" },
            { name: "UPVC Coupler / Socket", url: "images/catalog/plasto/items/upvc_coupler.jpg" },
            { name: "CPVC Coupler", url: "images/catalog/plasto/items/cpvc_coupler.jpg" },
            { name: "Brass Elbow", url: "images/catalog/plasto/items/brass_elbow.jpg" },
            { name: "Brass Tee", url: "images/catalog/plasto/items/brass_tee.jpg" },
            { name: "Brass FTA", url: "images/catalog/plasto/items/brass_fta.jpg" },
            { name: "Brass MTA", url: "images/catalog/plasto/items/brass_mta.jpg" },
            { name: "CPVC Ball Valve", url: "images/catalog/plasto/items/cpvc_ball_valve.jpg" },
            { name: "UPVC Ball Valve", url: "images/catalog/plasto/items/upvc_ball_valve.jpg" },
            { name: "CPVC Pipe", url: "images/catalog/plasto/items/cpvc_pipe.jpg" },
            { name: "UPVC Pipe", url: "images/catalog/plasto/items/upvc_pipe.jpg" },
            { name: "SWR Pipe", url: "images/catalog/plasto/items/swr_pipe.jpg" },
            { name: "SWR Single Tee", url: "images/catalog/plasto/items/swr_single_tee.jpg" },
            { name: "SWR Bend 87.5°", url: "images/catalog/plasto/items/swr_bend.jpg" },
            { name: "SWR Vent Cowl", url: "images/catalog/plasto/items/swr_vent_cowl.jpg" },
            { name: "Nahani Trap", url: "images/catalog/plasto/items/nahani_trap.jpg" },
            { name: "Solvent Cement Can", url: "images/catalog/plasto/items/solvent_cement.jpg" },
            { name: "Agri Solvent Tube", url: "images/catalog/plasto/items/agri_solvent.jpg" },
            { name: "End Cap", url: "images/catalog/plasto/items/end_cap.jpg" },
            { name: "Tank Nipple", url: "images/catalog/plasto/items/cpvc_tank_nipple.jpg" },
            { name: "Pipe Clip", url: "images/catalog/plasto/items/pipe_clip.jpg" },
            { name: "Garden Pipe", url: "images/catalog/plasto/items/garden_pipe.jpg" },
            { name: "Water Tank Lid", url: "images/catalog/plasto/items/water_tank_lid.jpg" },
        ];

        function renderLibraryGrid() {
            const grid = document.getElementById('libraryGrid');
            grid.innerHTML = '';
            plastoLibrary.forEach(item => {
                const div = document.createElement('div');
                div.className = 'border rounded-2xl p-2 bg-gray-50 hover:bg-blue-50 hover:border-blue-300 cursor-pointer text-center group transition';
                div.onclick = () => selectLibraryImage(item.url);
                div.innerHTML = `
                    <div class="aspect-square flex items-center justify-center p-1 overflow-hidden">
                        <img src="/${item.url}" alt="${item.name}" class="w-full h-full object-contain group-hover:scale-105 transition">
                    </div>
                    <p class="text-[10px] font-bold text-gray-700 mt-1 truncate">${item.name}</p>
                `;
                grid.appendChild(div);
            });
        }
        renderLibraryGrid();

        function openImageLibrary(pIdx) {
            currentTargetPidx = pIdx;
            switchModalTab('library');
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function openPdfCropper(pIdx, catalogPageStr) {
            currentTargetPidx = pIdx;
            const match = catalogPageStr.match(/\d+/);
            const pageNum = match ? parseInt(match[0]) : 3;
            document.getElementById('cropperPageSelect').value = pageNum;
            loadCropperPage(pageNum);
            switchModalTab('cropper');
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        function switchModalTab(tab) {
            if (tab === 'library') {
                document.getElementById('tabContent_library').classList.remove('hidden');
                document.getElementById('tabContent_cropper').classList.add('hidden');
                document.getElementById('tabBtn_library').className = 'py-3 font-bold text-xs border-b-2 border-blue-600 text-blue-600 flex items-center gap-2';
                document.getElementById('tabBtn_cropper').className = 'py-3 font-bold text-xs border-b-2 border-transparent text-gray-500 hover:text-gray-900 flex items-center gap-2';
            } else {
                document.getElementById('tabContent_library').classList.add('hidden');
                document.getElementById('tabContent_cropper').classList.remove('hidden');
                document.getElementById('tabBtn_library').className = 'py-3 font-bold text-xs border-b-2 border-transparent text-gray-500 hover:text-gray-900 flex items-center gap-2';
                document.getElementById('tabBtn_cropper').className = 'py-3 font-bold text-xs border-b-2 border-blue-600 text-blue-600 flex items-center gap-2';
            }
        }

        function selectLibraryImage(url) {
            if (currentTargetPidx !== null) {
                document.getElementById('img_val_' + currentTargetPidx).value = url;
                document.getElementById('img_tag_' + currentTargetPidx).src = '/' + url;
                closeImageModal();
            }
        }

        // Cropper Logic
        let cropStartX = 0, cropStartY = 0, isDragging = false;
        let cropBox = { x: 0, y: 0, w: 0, h: 0 };

        function loadCropperPage(pageNum) {
            const img = document.getElementById('cropTargetImage');
            img.src = `/images/catalog/plasto/page_${pageNum}.jpg`;
            document.getElementById('cropSelectionBox').classList.add('hidden');
            document.getElementById('cropCoordinates').textContent = 'Drag box on page to crop';
        }

        const cropContainer = document.getElementById('cropContainer');
        const selectionBox = document.getElementById('cropSelectionBox');

        cropContainer.addEventListener('mousedown', (e) => {
            const rect = cropContainer.getBoundingClientRect();
            cropStartX = e.clientX - rect.left;
            cropStartY = e.clientY - rect.top;
            isDragging = true;
            selectionBox.style.left = cropStartX + 'px';
            selectionBox.style.top = cropStartY + 'px';
            selectionBox.style.width = '0px';
            selectionBox.style.height = '0px';
            selectionBox.classList.remove('hidden');
        });

        window.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            const rect = cropContainer.getBoundingClientRect();
            const curX = Math.max(0, Math.min(e.clientX - rect.left, rect.width));
            const curY = Math.max(0, Math.min(e.clientY - rect.top, rect.height));

            cropBox.x = Math.min(cropStartX, curX);
            cropBox.y = Math.min(cropStartY, curY);
            cropBox.w = Math.abs(curX - cropStartX);
            cropBox.h = Math.abs(curY - cropStartY);

            selectionBox.style.left = cropBox.x + 'px';
            selectionBox.style.top = cropBox.y + 'px';
            selectionBox.style.width = cropBox.w + 'px';
            selectionBox.style.height = cropBox.h + 'px';

            document.getElementById('cropCoordinates').textContent = `Crop: ${Math.round(cropBox.w)} x ${Math.round(cropBox.h)} px`;
        });

        window.addEventListener('mouseup', () => {
            if (isDragging) {
                isDragging = false;
            }
        });

        function applyCanvasCrop() {
            if (cropBox.w < 20 || cropBox.h < 20) {
                alert('Kripya PDF page par photo ke upar ek rectangle box banayein.');
                return;
            }

            const img = document.getElementById('cropTargetImage');
            const scaleX = img.naturalWidth / img.clientWidth;
            const scaleY = img.naturalHeight / img.clientHeight;

            const canvas = document.createElement('canvas');
            canvas.width = cropBox.w * scaleX;
            canvas.height = cropBox.h * scaleY;
            const ctx = canvas.getContext('2d');

            ctx.drawImage(
                img,
                cropBox.x * scaleX,
                cropBox.y * scaleY,
                cropBox.w * scaleX,
                cropBox.h * scaleY,
                0,
                0,
                canvas.width,
                canvas.height
            );

            const base64Data = canvas.toDataURL('image/jpeg', 0.9);

            // Send to backend via AJAX
            fetch("{{ route('seller.catalog.crop_image') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    image_data: base64Data,
                    product_index: currentTargetPidx
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && currentTargetPidx !== null) {
                    document.getElementById('img_val_' + currentTargetPidx).value = data.image_url;
                    document.getElementById('img_tag_' + currentTargetPidx).src = data.asset_url;
                    closeImageModal();
                } else {
                    alert('Crop save karne me samasya aayi: ' + (data.message || 'Error'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Crop upload error');
            });
        }

        // Live Calculations
        function recalculateProduct(pIdx) {
            const card = document.getElementById('product_card_' + pIdx);
            if (!card) return;

            const discInput = card.querySelector('.prod-discount');
            const gstSelect = card.querySelector('.prod-gst');
            const discount = parseFloat(discInput?.value || 0);
            const gst = parseFloat(gstSelect?.value || 18);

            const wholesaleMargin = parseFloat(document.getElementById('global_wholesale_margin').value || 15) / 100;
            const retailMargin = parseFloat(document.getElementById('global_retail_margin').value || 35) / 100;

            const rows = card.querySelectorAll('.variant-row');
            rows.forEach(row => {
                const rawRateInput = row.querySelector('.v-rawrate');
                const rawRate = parseFloat(rawRateInput?.value || 0);

                const landingNet = Math.round(rawRate * (1 - (discount / 100)) * 100) / 100;
                const landingWithGst = Math.round(landingNet * (1 + (gst / 100)) * 100) / 100;

                const landingEl = row.querySelector('.v-landing');
                const landingNetEl = row.querySelector('.v-landing-net');
                if (landingEl) landingEl.textContent = '₹' + landingWithGst.toFixed(2);
                if (landingNetEl) landingNetEl.textContent = 'Excl. GST: ₹' + landingNet.toFixed(2);

                const wholesaleInput = row.querySelector('.v-wholesale');
                const retailInput = row.querySelector('.v-retail');

                if (wholesaleInput) wholesaleInput.value = Math.round(landingWithGst * (1 + wholesaleMargin));
                if (retailInput) retailInput.value = Math.round(landingWithGst * (1 + retailMargin));
            });
        }

        function applyGlobalRules() {
            const globalDisc = document.getElementById('global_discount').value;
            const globalGst = document.getElementById('global_gst').value;

            document.querySelectorAll('.product-card').forEach(card => {
                const pIdx = card.getAttribute('data-pidx');
                const discInput = card.querySelector('.prod-discount');
                const gstSelect = card.querySelector('.prod-gst');

                if (discInput) discInput.value = globalDisc;
                if (gstSelect) gstSelect.value = globalGst;

                recalculateProduct(pIdx);
            });
        }

        // 🤖 Vyapar AI Copilot Processor
        function executeAiCommand() {
            const prompt = document.getElementById('ai_prompt_input').value.trim();
            if (!prompt) return;

            const lower = prompt.toLowerCase();
            let appliedCount = 0;

            // Pattern 1: Pipes Less X% and Fittings Less Y%
            const pipeMatch = lower.match(/pipe[s]?\s*(?:less|discount)?\s*(\d+(?:\.\d+)?)/);
            const fittingMatch = lower.match(/fitting[s]?\s*(?:less|discount)?\s*(\d+(?:\.\d+)?)/);
            
            // Margins
            const wholesaleMatch = lower.match(/wholesale\s*(?:margin)?\s*(\d+(?:\.\d+)?)/);
            const retailMatch = lower.match(/retail\s*(?:margin)?\s*(\d+(?:\.\d+)?)/);
            const gstMatch = lower.match(/gst\s*(\d+)/);

            if (wholesaleMatch) {
                document.getElementById('global_wholesale_margin').value = wholesaleMatch[1];
            }
            if (retailMatch) {
                document.getElementById('global_retail_margin').value = retailMatch[1];
            }
            if (gstMatch) {
                document.getElementById('global_gst').value = gstMatch[1];
            }

            document.querySelectorAll('.product-card').forEach(card => {
                const pIdx = card.getAttribute('data-pidx');
                const name = card.getAttribute('data-name');
                const discInput = card.querySelector('.prod-discount');
                const gstSelect = card.querySelector('.prod-gst');

                if (pipeMatch && name.includes('pipe')) {
                    if (discInput) discInput.value = pipeMatch[1];
                } else if (fittingMatch && !name.includes('pipe')) {
                    if (discInput) discInput.value = fittingMatch[1];
                }

                if (gstMatch && gstSelect) {
                    gstSelect.value = gstMatch[1];
                }

                // Stock handling
                if (lower.includes('clear stock') || lower.includes('on demand') || lower.includes('zero stock')) {
                    const stockInput = card.querySelector('input[name*="stock_quantity"]');
                    if (stockInput) stockInput.value = '';
                    card.querySelectorAll('.variant-row input[name*="stock_quantity"]').forEach(inp => inp.value = '');
                }

                recalculateProduct(pIdx);
                appliedCount++;
            });

            alert(`✨ Vyapar AI ne safaltapoorvak ${appliedCount} products par aapki instruction apply kar di!`);
        }

        function quickAiAction(action) {
            if (action === 'plasto_standard') {
                document.getElementById('ai_prompt_input').value = "Pipes less 56% and fittings less 47%, wholesale 15%, retail 35%";
                executeAiCommand();
            } else if (action === 'standard_margins') {
                document.getElementById('global_wholesale_margin').value = "15";
                document.getElementById('global_retail_margin').value = "35";
                applyGlobalRules();
            } else if (action === 'clear_stock') {
                document.getElementById('ai_prompt_input').value = "Clear all stock for on-demand mode";
                executeAiCommand();
            } else if (action === 'gst_18') {
                document.getElementById('global_gst').value = "18";
                applyGlobalRules();
            }
        }

        function filterProducts() {
            const query = document.getElementById('product_search').value.toLowerCase().trim();
            let count = 0;
            document.querySelectorAll('.product-card').forEach(card => {
                const name = card.getAttribute('data-name');
                const cat = card.getAttribute('data-category');
                if (name.includes(query) || cat.includes(query)) {
                    card.style.display = '';
                    count++;
                } else {
                    card.style.display = 'none';
                }
            });
            document.getElementById('visibleCount').textContent = count;
        }

        function removeProduct(pIdx) {
            if (confirm('Kya aap is product ko hatana chahte hain?')) {
                const card = document.getElementById('product_card_' + pIdx);
                if (card) {
                    card.remove();
                    updateTotalCount();
                }
            }
        }

        function removeVariantRow(rowId) {
            const row = document.getElementById(rowId);
            if (row) {
                const tbody = row.parentElement;
                if (tbody.children.length <= 1) {
                    alert('Har product me kam se kam 1 size hona zaroori hai.');
                    return;
                }
                row.remove();
            }
        }

        function addVariantRow(pIdx) {
            const tbody = document.querySelector('#variant_table_' + pIdx + ' tbody');
            const vIdx = tbody.children.length;
            const newRowId = `vrow_${pIdx}_${vIdx}`;

            const tr = document.createElement('tr');
            tr.className = 'variant-row hover:bg-gray-50/80 transition';
            tr.id = newRowId;
            tr.setAttribute('data-vidx', vIdx);

            tr.innerHTML = `
                <td class="py-2 px-3">
                    <input type="text" name="products[${pIdx}][variants][${vIdx}][variant_name]" value="New Size" required class="w-32 px-2 py-1.5 rounded-lg border border-gray-300 font-bold text-gray-900 text-xs">
                    <input type="hidden" name="products[${pIdx}][variants][${vIdx}][size]" value="New Size">
                    <input type="hidden" name="products[${pIdx}][variants][${vIdx}][grade]" value="Standard">
                </td>
                <td class="py-2 px-3">
                    <input type="text" name="products[${pIdx}][variants][${vIdx}][sku]" value="SKU-${vIdx+1}" class="w-24 px-2 py-1.5 rounded-lg border border-gray-300 font-mono text-gray-800 text-xs">
                </td>
                <td class="py-2 px-3">
                    <div class="relative w-24">
                        <span class="absolute left-2 top-1.5 text-gray-400">₹</span>
                        <input type="number" step="0.5" name="products[${pIdx}][variants][${vIdx}][raw_rate]" value="100" required class="v-rawrate w-full pl-5 pr-1 py-1.5 rounded-lg border border-gray-300 font-bold text-gray-900 text-xs" oninput="recalculateProduct(${pIdx})">
                    </div>
                </td>
                <td class="py-2 px-3 bg-blue-50/30">
                    <div class="text-xs font-bold text-blue-900 v-landing">₹0.00</div>
                    <div class="text-[10px] text-gray-400 v-landing-net">Excl. GST: ₹0.00</div>
                </td>
                <td class="py-2 px-3 bg-indigo-50/30">
                    <div class="relative w-24">
                        <span class="absolute left-2 top-1.5 text-gray-400">₹</span>
                        <input type="number" step="0.5" name="products[${pIdx}][variants][${vIdx}][wholesale_price]" class="v-wholesale w-full pl-5 pr-1 py-1.5 rounded-lg border border-indigo-200 font-bold text-indigo-950 text-xs">
                    </div>
                </td>
                <td class="py-2 px-3 bg-emerald-50/30">
                    <div class="relative w-24">
                        <span class="absolute left-2 top-1.5 text-gray-400">₹</span>
                        <input type="number" step="0.5" name="products[${pIdx}][variants][${vIdx}][retail_price]" class="v-retail w-full pl-5 pr-1 py-1.5 rounded-lg border border-emerald-200 font-bold text-emerald-950 text-xs">
                    </div>
                </td>
                <td class="py-2 px-3 text-center">
                    <input type="number" min="0" name="products[${pIdx}][variants][${vIdx}][stock_quantity]" placeholder="Optional" class="w-16 px-2 py-1.5 rounded-lg border border-gray-300 text-center font-bold text-xs text-gray-900 placeholder:text-gray-300 placeholder:font-normal">
                    <input type="hidden" name="products[${pIdx}][variants][${vIdx}][mrp]" value="100">
                </td>
                <td class="py-2 px-3 text-center">
                    <button type="button" onclick="removeVariantRow('${newRowId}')" class="h-7 w-7 rounded-lg text-rose-500 hover:bg-rose-50 transition flex items-center justify-center">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);
            recalculateProduct(pIdx);
        }

        function updateTotalCount() {
            const count = document.querySelectorAll('.product-card').length;
            const el = document.getElementById('totalCount');
            if (el) el.textContent = count;
            document.getElementById('visibleCount').textContent = count;
        }
    </script>
</body>
</html>
