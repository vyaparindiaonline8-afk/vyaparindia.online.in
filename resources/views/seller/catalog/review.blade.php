<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review & Costing Staging Canvas - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
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
                            Source: {{ $job->filename }} ({{ count($extractedData['products'] ?? []) }} products detected)
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
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
                        <input type="number" id="global_discount" value="25" min="0" max="90" step="0.5" class="w-full bg-white/10 border border-white/20 rounded-xl px-3 py-2 text-white text-sm font-bold focus:outline-none focus:ring-2 focus:ring-blue-400">
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

        <!-- Main Form -->
        <form action="{{ route('seller.catalog.publish', $job->id) }}" method="POST" id="catalogPublishForm" class="space-y-8">
            @csrf

            @forelse($extractedData['products'] as $pIdx => $prod)
                <div class="product-card bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden" id="product_card_{{ $pIdx }}" data-pidx="{{ $pIdx }}">
                    
                    <!-- Card Top Header -->
                    <div class="bg-gray-50 border-b border-gray-200 px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="h-7 w-7 rounded-lg bg-blue-600 text-white text-xs font-black flex items-center justify-center">
                                #{{ $pIdx + 1 }}
                            </span>
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Commercial Product Entry
                            </span>
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
                            
                            <!-- Product Image (Auto Extracted from PDF) -->
                            <div class="lg:col-span-3 space-y-2">
                                <label class="text-xs font-bold text-gray-700 block">
                                    Product Photo (1 Image)
                                </label>
                                <div class="w-full aspect-square rounded-2xl bg-gray-100 border border-gray-200 overflow-hidden relative group flex items-center justify-center">
                                    @if(!empty($prod['image_url']))
                                        <img src="{{ asset($prod['image_url']) }}" alt="{{ $prod['name'] }}" class="w-full h-full object-contain p-2">
                                        <input type="hidden" name="products[{{ $pIdx }}][image_url]" value="{{ $prod['image_url'] }}">
                                    @else
                                        <div class="text-center p-4 text-gray-400">
                                            <i class="fa-solid fa-image text-3xl mb-1"></i>
                                            <p class="text-[11px]">No image isolated</p>
                                        </div>
                                    @endif
                                </div>
                                <p class="text-[11px] text-gray-400 text-center">
                                    Same photo used for all sizes & range variants below
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
                                                <option value="{{ $category->id }}" {{ (stripos($prod['name'], $category->name) !== false) ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                    <div>
                                        <label class="text-xs font-bold text-gray-700 block mb-1">
                                            Trade Discount %
                                        </label>
                                        <div class="relative">
                                            <input type="number" step="0.5" min="0" max="95" name="products[{{ $pIdx }}][trade_discount_percent]" value="{{ $prod['trade_discount_percent'] ?? 25 }}" class="prod-discount w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold text-gray-900" oninput="recalculateProduct({{ $pIdx }})">
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
                                        <input type="text" name="products[{{ $pIdx }}][hsn_code]" value="{{ $prod['hsn_code'] ?? '39174000' }}" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-mono font-bold text-gray-900">
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
                                            <th class="py-2.5 px-3">Variant / Name</th>
                                            <th class="py-2.5 px-3">Size</th>
                                            <th class="py-2.5 px-3">Grade / Spec</th>
                                            <th class="py-2.5 px-3">List Price (₹)</th>
                                            <th class="py-2.5 px-3 bg-blue-50/50 text-blue-900">Landing Cost (Net)</th>
                                            <th class="py-2.5 px-3 bg-indigo-50/50 text-indigo-900">Wholesale (B2B)</th>
                                            <th class="py-2.5 px-3 bg-emerald-50/50 text-emerald-900">Retail (D2C)</th>
                                            <th class="py-2.5 px-3">MRP (₹)</th>
                                            <th class="py-2.5 px-3 text-center">Stock (Units)</th>
                                            <th class="py-2.5 px-3 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @foreach($prod['variants'] as $vIdx => $v)
                                            <tr class="variant-row hover:bg-gray-50/80 transition" id="vrow_{{ $pIdx }}_{{ $vIdx }}" data-vidx="{{ $vIdx }}">
                                                <td class="py-2 px-3">
                                                    <input type="text" name="products[{{ $pIdx }}][variants][{{ $vIdx }}][variant_name]" value="{{ $v['variant_name'] }}" required class="w-36 px-2 py-1.5 rounded-lg border border-gray-300 font-bold text-gray-900 text-xs">
                                                </td>
                                                <td class="py-2 px-3">
                                                    <input type="text" name="products[{{ $pIdx }}][variants][{{ $vIdx }}][size]" value="{{ $v['size'] ?? '' }}" placeholder='1/2", 1"' class="w-20 px-2 py-1.5 rounded-lg border border-gray-300 text-gray-800 text-xs">
                                                </td>
                                                <td class="py-2 px-3">
                                                    <input type="text" name="products[{{ $pIdx }}][variants][{{ $vIdx }}][grade]" value="{{ $v['grade'] ?? '' }}" placeholder="SDR 11" class="w-20 px-2 py-1.5 rounded-lg border border-gray-300 text-gray-800 text-xs">
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
                                                <td class="py-2 px-3">
                                                    <div class="relative w-20">
                                                        <span class="absolute left-2 top-1.5 text-gray-400">₹</span>
                                                        <input type="number" step="0.5" name="products[{{ $pIdx }}][variants][{{ $vIdx }}][mrp]" value="{{ $v['mrp'] ?? '' }}" class="v-mrp w-full pl-5 pr-1 py-1.5 rounded-lg border border-gray-300 text-gray-700 text-xs">
                                                    </div>
                                                </td>
                                                <td class="py-2 px-3 text-center">
                                                    <input type="number" min="0" name="products[{{ $pIdx }}][variants][{{ $vIdx }}][stock_quantity]" value="{{ $v['stock_quantity'] ?? '' }}" placeholder="Optional" class="w-16 px-2 py-1.5 rounded-lg border border-gray-300 text-center font-bold text-xs text-gray-900 placeholder:text-gray-300 placeholder:font-normal">
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
                        <p class="text-xs text-gray-500">The PDF did not contain recognizable commercial tables.</p>
                    </div>
                    <a href="{{ route('seller.catalog.upload') }}" class="inline-flex px-6 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs">
                        Try Another PDF
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
                            Stock & GST fields are optional ("Dale to thik, na dale to thik")
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('seller.catalog.upload') }}" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition">
                            Cancel
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

    <script>
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
                const mrpInput = row.querySelector('.v-mrp');

                if (wholesaleInput) wholesaleInput.value = Math.round(landingWithGst * (1 + wholesaleMargin));
                if (retailInput) retailInput.value = Math.round(landingWithGst * (1 + retailMargin));
                if (mrpInput) mrpInput.value = Math.round(landingWithGst * 1.60);
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
                    alert('Har product me kam se kam 1 variant hona zaroori hai.');
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
                    <input type="text" name="products[${pIdx}][variants][${vIdx}][variant_name]" value="Variant ${vIdx + 1}" required class="w-36 px-2 py-1.5 rounded-lg border border-gray-300 font-bold text-gray-900 text-xs">
                </td>
                <td class="py-2 px-3">
                    <input type="text" name="products[${pIdx}][variants][${vIdx}][size]" placeholder='Size' class="w-20 px-2 py-1.5 rounded-lg border border-gray-300 text-gray-800 text-xs">
                </td>
                <td class="py-2 px-3">
                    <input type="text" name="products[${pIdx}][variants][${vIdx}][grade]" placeholder="Grade" class="w-20 px-2 py-1.5 rounded-lg border border-gray-300 text-gray-800 text-xs">
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
                <td class="py-2 px-3">
                    <div class="relative w-20">
                        <span class="absolute left-2 top-1.5 text-gray-400">₹</span>
                        <input type="number" step="0.5" name="products[${pIdx}][variants][${vIdx}][mrp]" class="v-mrp w-full pl-5 pr-1 py-1.5 rounded-lg border border-gray-300 text-gray-700 text-xs">
                    </div>
                </td>
                <td class="py-2 px-3 text-center">
                    <input type="number" min="0" name="products[${pIdx}][variants][${vIdx}][stock_quantity]" placeholder="Optional" class="w-16 px-2 py-1.5 rounded-lg border border-gray-300 text-center font-bold text-xs text-gray-900 placeholder:text-gray-300 placeholder:font-normal">
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
        }
    </script>
</body>
</html>
