<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel Multi-Row & Variant Card Mapper - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .drawer-slide {
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .row-selected {
            background-color: #eff6ff !important;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen pb-32">

    <!-- Top Sticky Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.catalog.pdf_studio') }}" class="h-9 w-9 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 flex items-center justify-center text-sm transition">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-black text-gray-900 text-base tracking-tight">Excel Multi-Row Mapper</span>
                            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                6-8 Line Bulk Linker
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-500">
                            Select 6-8 sizes from Excel and link 1 photo to all of them with 1-click!
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @if($job)
                        <a href="{{ route('seller.catalog.export_excel', $job->id) }}" class="px-3.5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition flex items-center gap-1.5">
                            <i class="fa-solid fa-file-excel text-emerald-600"></i>
                            <span>Download Excel</span>
                        </a>
                        
                        <form action="{{ route('seller.catalog.excel_mapper.publish_direct') }}" method="POST" id="directPublishForm" onsubmit="return confirm('Kya aap in sabhi products ko apne live store par publish karna chahte hain?');">
                            @csrf
                            <input type="hidden" name="job_id" value="{{ $job->id }}">
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-600/30 flex items-center gap-2 transition active:scale-95">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <span>Publish to Live Store</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('seller.catalog.upload') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-600/30 flex items-center gap-2 transition">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span>Upload Fresh Excel / PDF</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Suite Navigation Tabs -->
            <div class="flex items-center gap-6 border-t border-gray-100 pt-2 pb-1 overflow-x-auto">
                <a href="{{ route('seller.catalog.gallery') }}" class="pb-2 text-xs font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-900 flex items-center gap-2 whitespace-nowrap transition">
                    <i class="fa-solid fa-images"></i>
                    <span>1. Bulk Media Vault & Gallery</span>
                </a>
                <a href="{{ route('seller.catalog.pdf_studio') }}" class="pb-2 text-xs font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-900 flex items-center gap-2 whitespace-nowrap transition">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>2. Side-by-Side PDF Studio</span>
                </a>
                <a href="{{ route('seller.catalog.excel_mapper') }}" class="pb-2 text-xs font-extrabold border-b-2 border-emerald-600 text-emerald-600 flex items-center gap-2 whitespace-nowrap">
                    <i class="fa-solid fa-table-cells"></i>
                    <span>3. Excel Multi-Row Mapper</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-bold">{{ count($products) }} Cards ({{ count($flatRows) }} Sizes)</span>
                </a>
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

        <!-- ========================================== -->
        <!-- 📑 DYNAMIC SAAS SPREADSHEET TABLE          -->
        <!-- (Supports PDF Extracted Lines & 6-8 Link)  -->
        <!-- ========================================== -->
        <div id="dynamicSheetContainer" class="{{ (!$job || empty($products)) ? '' : 'hidden' }} space-y-4">
            <!-- Dynamic Controls Header -->
            <div class="bg-white p-5 rounded-3xl border border-gray-200 shadow-xs flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-table"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <input type="text" id="dynamicSheetName" value="PDF Extracted Catalog Sheet" class="text-sm font-black text-gray-900 border-b border-dashed border-gray-300 focus:border-emerald-600 focus:outline-none bg-transparent">
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-bold" id="dynamicRowCountBadge">0 Rows</span>
                        </div>
                        <p class="text-[11px] text-gray-500">
                            PDF se nikale gaye text lines yahan editable rows me aa gaye hain. 6-8 rows select karke 1-click me photo link karein!
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" onclick="loadSampleDynamicRows()" class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition flex items-center gap-1.5" title="Load sample UPVC / CPVC fittings">
                        <i class="fa-solid fa-wand-magic-sparkles text-amber-500"></i>
                        <span>Load Sample Lines</span>
                    </button>
                    <a href="{{ route('seller.catalog.pdf_studio') }}" class="px-3.5 py-2 rounded-xl bg-violet-50 hover:bg-violet-100 text-violet-700 font-bold text-xs transition flex items-center gap-1.5 border border-violet-200">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>Extract from PDF Studio</span>
                    </a>
                    <button type="button" onclick="addDynamicRow()" class="px-3.5 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs transition flex items-center gap-1.5 border border-blue-200">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add Row</span>
                    </button>
                    <button type="button" onclick="exportDynamicToCsv()" class="px-3.5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition flex items-center gap-1.5">
                        <i class="fa-solid fa-file-csv text-emerald-600"></i>
                        <span>Export CSV</span>
                    </button>
                    <button type="button" onclick="saveDynamicSheetToBackend()" id="btnSaveDynamicSheet" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-600/30 flex items-center gap-1.5 transition active:scale-95">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Save to Catalog & Publish</span>
                    </button>
                    @if($job && !empty($products))
                        <button type="button" onclick="toggleDynamicMode(false)" class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs">
                            &larr; Back to Uploaded Excel
                        </button>
                    @endif
                </div>
            </div>

            <!-- Dynamic Table Card -->
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3 bg-slate-50/70">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black text-gray-800 uppercase tracking-wider">Dynamic Spreadsheet Lines:</span>
                        <span class="text-xs text-gray-500" id="dynamicTableSubtitle">(Select 6-8 lines to link photo)</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-gray-500">Quick Select:</span>
                        <button type="button" onclick="selectNDynamicRows(6)" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 hover:bg-gray-100 font-bold text-gray-700">First 6</button>
                        <button type="button" onclick="selectNDynamicRows(8)" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 hover:bg-gray-100 font-bold text-gray-700">First 8</button>
                        <button type="button" onclick="toggleSelectAllDynamic(true)" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 hover:bg-gray-100 font-bold text-gray-700">All</button>
                        <button type="button" onclick="clearDynamicRowSelection()" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 font-bold text-gray-600">Clear</button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs" id="dynamicSpreadsheetTable">
                        <thead class="bg-gray-50 text-gray-500 text-[11px] font-bold uppercase border-b border-gray-200">
                            <tr>
                                <th class="p-3 w-12 text-center">
                                    <input type="checkbox" id="dynamicSelectAll" onchange="toggleSelectAllDynamic(this.checked)" class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                </th>
                                <th class="p-3 w-14">Photo</th>
                                <th class="p-3 w-28">Category</th>
                                <th class="p-3 min-w-[200px]">Product Name (Line Text)</th>
                                <th class="p-3 w-32">Size / Variant</th>
                                <th class="p-3 w-24">MRP (₹)</th>
                                <th class="p-3 w-24">Cost (₹)</th>
                                <th class="p-3 w-24">Retail (₹)</th>
                                <th class="p-3 w-28 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="dynamicTableBody" class="divide-y divide-gray-100 font-medium">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($job && !empty($products))
            <!-- Banner to switch to Dynamic PDF Mode if user has extracted lines -->
            <div id="dynamicPdfPromptBanner" class="hidden p-3.5 bg-violet-50 border border-violet-200 rounded-2xl flex items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-file-lines text-violet-600 text-sm"></i>
                    <span class="font-bold text-violet-900" id="dynamicPdfPromptText">PDF Studio se nikale gaye text lines uplabdh hain!</span>
                </div>
                <button type="button" onclick="toggleDynamicMode(true)" class="px-3.5 py-1.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-extrabold text-xs shadow-sm transition">
                    Open PDF Extracted Sheet &rarr;
                </button>
            </div>
        @endif

        @if($job && !empty($products))

        <!-- Controls & View Mode Bar -->
        <div class="bg-white p-4 rounded-3xl border border-gray-200 shadow-xs flex flex-wrap items-center justify-between gap-4">
            
            <!-- Search & Filters -->
            <div class="flex items-center gap-3 flex-1 min-w-[300px]">
                <div class="relative flex-1">
                    <input type="text" id="filterInput" placeholder="Search product or size (e.g. Elbow, Tee, 25mm, 1 inch)..." oninput="filterItemsLive(this.value)" class="w-full pl-10 pr-4 py-2.5 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-600 font-medium">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                </div>
                <button type="button" onclick="openGalleryDrawer(null, null)" class="px-4 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-extrabold border border-indigo-200 flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-images"></i>
                    <span>Browse Photo Bank</span>
                </button>
            </div>

            <!-- View Switcher -->
            <div class="flex items-center bg-gray-100 p-1 rounded-2xl">
                <button type="button" onclick="switchView('table')" id="btnViewTable" class="px-4 py-2 rounded-xl text-xs font-black bg-white text-emerald-700 shadow-xs flex items-center gap-2 transition">
                    <i class="fa-solid fa-table-list"></i>
                    <span>Multi-Row Table (Select 6-8 Lines)</span>
                </button>
                <button type="button" onclick="switchView('cards')" id="btnViewCards" class="px-4 py-2 rounded-xl text-xs font-bold text-gray-500 hover:text-gray-800 flex items-center gap-2 transition">
                    <i class="fa-solid fa-grip"></i>
                    <span>Grouped Product Cards (134 Cards)</span>
                </button>
            </div>

        </div>

        <!-- 🏷️ Category Filter Tabs: UPVC, CPVC, SWR, Agri/Others -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1">
            <button type="button" onclick="filterByCategory('ALL')" id="catTab_ALL" class="cat-pill px-4 py-2.5 rounded-2xl text-xs font-black bg-gray-900 text-white shadow-sm flex items-center gap-2 transition whitespace-nowrap">
                <i class="fa-solid fa-layer-group"></i>
                <span>All Categories</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-mono">{{ count($products) }}</span>
            </button>
            <button type="button" onclick="filterByCategory('UPVC')" id="catTab_UPVC" class="cat-pill px-4 py-2.5 rounded-2xl text-xs font-extrabold bg-white hover:bg-blue-50 text-blue-700 border border-blue-200 shadow-xs flex items-center gap-2 transition whitespace-nowrap">
                <span class="h-2.5 w-2.5 rounded-full bg-blue-600"></span>
                <span>💧 UPVC Pipes & Fittings</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-blue-100 text-blue-800 font-mono font-bold">{{ $upvcCount ?? 0 }}</span>
            </button>
            <button type="button" onclick="filterByCategory('CPVC')" id="catTab_CPVC" class="cat-pill px-4 py-2.5 rounded-2xl text-xs font-extrabold bg-white hover:bg-amber-50 text-amber-700 border border-amber-200 shadow-xs flex items-center gap-2 transition whitespace-nowrap">
                <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                <span>🔥 CPVC Pipes & Fittings</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-100 text-amber-800 font-mono font-bold">{{ $cpvcCount ?? 0 }}</span>
            </button>
            <button type="button" onclick="filterByCategory('SWR')" id="catTab_SWR" class="cat-pill px-4 py-2.5 rounded-2xl text-xs font-extrabold bg-white hover:bg-purple-50 text-purple-700 border border-purple-200 shadow-xs flex items-center gap-2 transition whitespace-nowrap">
                <span class="h-2.5 w-2.5 rounded-full bg-purple-600"></span>
                <span>🚰 SWR Drainage & Traps</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-purple-100 text-purple-800 font-mono font-bold">{{ $swrCount ?? 0 }}</span>
            </button>
            <button type="button" onclick="filterByCategory('AGRI_OTHER')" id="catTab_AGRI_OTHER" class="cat-pill px-4 py-2.5 rounded-2xl text-xs font-extrabold bg-white hover:bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-xs flex items-center gap-2 transition whitespace-nowrap">
                <span class="h-2.5 w-2.5 rounded-full bg-emerald-600"></span>
                <span>🌿 Agri, Solvents & Others</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-mono font-bold">{{ $otherCount ?? 0 }}</span>
            </button>
        </div>

        <!-- ========================================== -->
        <!-- VIEW 1: MULTI-ROW SPREADSHEET TABLE VIEW   -->
        <!-- ========================================== -->
        <div id="viewTableContainer" class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3 bg-slate-50/60">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-black text-gray-800 uppercase tracking-wider">Excel Spreadsheet Rows:</span>
                    <span class="text-xs text-gray-500">({{ count($flatRows) }} size variants total)</span>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-gray-500">Quick Select:</span>
                    <button type="button" onclick="selectNRows(6)" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 hover:bg-gray-100 font-bold text-gray-700">First 6</button>
                    <button type="button" onclick="selectNRows(8)" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 hover:bg-gray-100 font-bold text-gray-700">First 8</button>
                    <button type="button" onclick="selectSameProductRows()" class="px-2.5 py-1 rounded-lg bg-blue-50 border border-blue-200 hover:bg-blue-100 font-bold text-blue-700">Same Family</button>
                    <button type="button" onclick="clearRowSelection()" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 font-bold text-gray-600">Clear</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs" id="spreadsheetTable">
                    <thead class="bg-gray-50 text-gray-500 text-[11px] font-bold uppercase border-b border-gray-200">
                        <tr>
                            <th class="p-3 w-12 text-center">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAllRows(this.checked)" class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            </th>
                            <th class="p-3 w-14">Photo</th>
                            <th class="p-3">Type</th>
                            <th class="p-3">Product Name (Base Family)</th>
                            <th class="p-3">Size / Variant</th>
                            <th class="p-3">MRP (₹)</th>
                            <th class="p-3">Cost Rate (₹)</th>
                            <th class="p-3">Retail (₹)</th>
                            <th class="p-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium">
                        @foreach($flatRows as $row)
                            <tr class="hover:bg-slate-50 transition table-row-item" id="row_{{ $row['row_id'] }}" data-category="{{ $row['group_type'] ?? 'UPVC' }}" data-search="{{ strtolower($row['product_name'] . ' ' . $row['variant_name'] . ' ' . $row['size'] . ' ' . ($row['group_type'] ?? '')) }}" data-parent-idx="{{ $row['parent_idx'] }}">
                                <td class="p-3 text-center">
                                    <input type="checkbox" value="{{ $row['row_id'] }}" onchange="handleRowCheckboxChange(this)" class="row-checkbox h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                </td>
                                <td class="p-3">
                                    <div class="h-10 w-10 bg-gray-50 rounded-xl border border-gray-200 p-0.5 flex items-center justify-center overflow-hidden cursor-pointer" onclick="openGalleryDrawer('row', {{ $row['row_id'] }})" title="Click to change photo">
                                        @if($row['image_url'])
                                            <img src="{{ asset($row['image_url']) }}" class="max-h-full max-w-full object-contain" id="img_thumb_row_{{ $row['row_id'] }}">
                                        @else
                                            <span class="text-gray-300 text-xs" id="img_thumb_row_{{ $row['row_id'] }}"><i class="fa-solid fa-image"></i></span>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-3">
                                    @php
                                        $gt = $row['group_type'] ?? 'UPVC';
                                        $pillClass = $gt === 'UPVC' ? 'bg-blue-100 text-blue-800 border-blue-200' : ($gt === 'CPVC' ? 'bg-amber-100 text-amber-800 border-amber-200' : ($gt === 'SWR' ? 'bg-purple-100 text-purple-800 border-purple-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200'));
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black border {{ $pillClass }}">
                                        {{ $gt === 'AGRI_OTHER' ? 'OTHER' : $gt }}
                                    </span>
                                </td>
                                <td class="p-3 font-bold text-gray-900">
                                    {{ $row['product_name'] }}
                                </td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded-lg bg-gray-100 text-gray-800 font-mono font-bold text-[11px]">
                                        {{ $row['size'] ?: $row['variant_name'] }}
                                    </span>
                                </td>
                                <td class="p-3 font-mono font-bold text-gray-800">
                                    ₹{{ number_format($row['mrp'], 2) }}
                                </td>
                                <td class="p-3 font-mono text-gray-600">
                                    ₹{{ number_format($row['purchase_cost'], 2) }}
                                </td>
                                <td class="p-3 font-mono font-bold text-emerald-700">
                                    ₹{{ number_format($row['retail_price'] ?: ($row['purchase_cost'] * 1.35), 2) }}
                                </td>
                                <td class="p-3 text-right">
                                    <button type="button" onclick="openGalleryDrawer('row', {{ $row['row_id'] }})" class="px-3 py-1 rounded-lg bg-gray-100 hover:bg-emerald-50 hover:text-emerald-700 text-[11px] font-bold transition">
                                        <i class="fa-regular fa-image mr-1"></i> Pick Photo
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- VIEW 2: GROUPED PRODUCT CARDS (134 FAMILIES) -->
        <!-- ========================================== -->
        <div id="viewCardsContainer" class="space-y-6 hidden">
            @foreach($products as $pIdx => $prod)
                <div class="product-family-card bg-white rounded-3xl border border-gray-200 p-5 shadow-xs hover:shadow-md transition space-y-4" data-card-idx="{{ $pIdx }}" data-category="{{ $prod['group_type'] ?? 'UPVC' }}" data-search="{{ strtolower($prod['name'] . ' ' . ($prod['group_type'] ?? '')) }}">
                    
                    <div class="flex flex-wrap items-center justify-between gap-4 pb-3 border-b border-gray-100">
                        <div class="flex items-center gap-4">
                            <!-- Product Photo Slot -->
                            <div class="relative group h-20 w-20 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-300 hover:border-emerald-500 p-1 flex items-center justify-center overflow-hidden cursor-pointer transition shrink-0" onclick="openGalleryDrawer('card', {{ $pIdx }})" title="Click to choose image from Photo Bank">
                                @if($prod['image_url'])
                                    <img src="{{ asset($prod['image_url']) }}" class="max-h-full max-w-full object-contain" id="card_thumb_{{ $pIdx }}">
                                @else
                                    <div class="text-center text-gray-400 group-hover:text-emerald-600" id="card_thumb_{{ $pIdx }}">
                                        <i class="fa-solid fa-camera text-lg"></i>
                                        <p class="text-[9px] font-bold mt-0.5">Add Photo</p>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-bold transition rounded-xl">
                                    Change
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    @php
                                        $cgt = $prod['group_type'] ?? 'UPVC';
                                        $cardPill = $cgt === 'UPVC' ? 'bg-blue-100 text-blue-800 border-blue-200' : ($cgt === 'CPVC' ? 'bg-amber-100 text-amber-800 border-amber-200' : ($cgt === 'SWR' ? 'bg-purple-100 text-purple-800 border-purple-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200'));
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black border {{ $cardPill }}">
                                        {{ $cgt === 'AGRI_OTHER' ? 'AGRI / OTHER' : $cgt }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 font-bold text-[10px]">{{ $prod['category'] ?? 'Industrial' }}</span>
                                </div>
                                <h3 class="text-base font-black text-gray-900">{{ $prod['name'] }}</h3>
                                <div class="flex items-center gap-2 text-xs text-gray-500 mt-0.5">
                                    <span class="font-bold text-gray-700">{{ count($prod['variants'] ?? []) }} Sizes / Variants</span>
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="openGalleryDrawer('card', {{ $pIdx }})" class="px-4 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-extrabold border border-indigo-200 flex items-center gap-1.5 transition">
                            <i class="fa-solid fa-images"></i>
                            <span>Select Photo from Bank</span>
                        </button>
                    </div>

                    <!-- Variants Sub-Table -->
                    <div class="overflow-x-auto rounded-2xl border border-gray-100 bg-gray-50/50">
                        <table class="w-full text-left text-xs">
                            <thead class="text-[10px] text-gray-400 uppercase font-bold border-b border-gray-200">
                                <tr>
                                    <th class="p-2.5">Size / Dimension</th>
                                    <th class="p-2.5">MRP</th>
                                    <th class="p-2.5">Purchase Cost</th>
                                    <th class="p-2.5">Wholesale Rate</th>
                                    <th class="p-2.5">Retail Selling</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-medium">
                                @foreach($prod['variants'] as $v)
                                    <tr class="hover:bg-white transition">
                                        <td class="p-2.5 font-bold text-gray-900 font-mono">{{ $v['size'] ?: $v['variant_name'] }}</td>
                                        <td class="p-2.5 font-mono">₹{{ number_format(floatval($v['mrp'] ?? 0), 2) }}</td>
                                        <td class="p-2.5 font-mono text-gray-600">₹{{ number_format(floatval($v['raw_rate'] ?? 0), 2) }}</td>
                                        <td class="p-2.5 font-mono text-blue-700 font-bold">₹{{ number_format(floatval($v['wholesale_price'] ?? 0), 2) }}</td>
                                        <td class="p-2.5 font-mono text-emerald-700 font-bold">₹{{ number_format(floatval($v['retail_price'] ?? 0), 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            @endforeach
        </div>

        @endif

    </div>

    <!-- ========================================== -->
    <!-- FLOATING MULTI-ROW BATCH ASSIGN ACTION BAR -->
    <!-- ========================================== -->
    <div id="floatingBatchBar" class="fixed bottom-6 inset-x-0 mx-auto max-w-2xl bg-gray-900/95 backdrop-blur-md text-white p-4 rounded-3xl shadow-2xl border border-white/10 z-50 flex items-center justify-between gap-4 hidden transform transition-all duration-300">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-black text-sm">
                <span id="floatingSelectedCount">0</span>
            </div>
            <div>
                <h4 class="text-xs font-black">Lines Selected</h4>
                <p class="text-[10px] text-gray-400">Apply 1 photo from Gallery to all these selected rows</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="clearRowSelection()" class="px-3 py-2 rounded-xl text-gray-400 hover:text-white text-xs font-bold transition">
                Deselect
            </button>
            <button type="button" onclick="openGalleryDrawerForBatch()" class="px-5 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-gray-900 font-black text-xs shadow-lg shadow-emerald-500/30 flex items-center gap-2 transition active:scale-95">
                <i class="fa-solid fa-images"></i>
                <span>Assign Image to (<span id="btnBatchCount">0</span>) Rows</span>
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SLIDE-OVER PHOTO GALLERY DRAWER            -->
    <!-- ========================================== -->
    <div id="galleryDrawerOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 hidden flex justify-end" onclick="closeGalleryDrawer()">
        <div class="drawer-slide w-full max-w-lg bg-white h-full shadow-2xl flex flex-col" onclick="event.stopPropagation()">
            
            <!-- Drawer Header -->
            <div class="p-5 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-images text-indigo-600"></i>
                        <span>Select Photo from Media Bank</span>
                    </h3>
                    <p class="text-[11px] text-gray-500" id="drawerTargetInfo">
                        Click any image to attach it immediately
                    </p>
                </div>
                <button type="button" onclick="closeGalleryDrawer()" class="h-8 w-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Drawer Search -->
            <div class="p-3 border-b border-gray-100 bg-gray-50">
                <div class="relative">
                    <input type="text" placeholder="Search gallery photo..." oninput="filterDrawerGallery(this.value)" class="w-full pl-8 pr-3 py-2 rounded-xl border border-gray-300 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-indigo-600 bg-white">
                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-gray-400 text-xs"></i>
                </div>
            </div>

            <!-- Drawer Gallery Grid -->
            <div class="flex-1 overflow-y-auto p-4">
                <div class="grid grid-cols-3 gap-3" id="drawerImagesGrid">
                    @foreach($galleryImages as $g)
                        <div class="drawer-img-card border border-gray-200 rounded-2xl p-2 bg-gray-50 hover:bg-indigo-50 hover:border-indigo-400 cursor-pointer text-center group transition" onclick="selectDrawerImage('{{ $g['url'] }}', '{{ asset($g['url']) }}')" data-name="{{ strtolower($g['name']) }}">
                            <div class="h-20 w-full bg-white rounded-xl p-1 mb-1.5 flex items-center justify-center overflow-hidden border border-gray-100 group-hover:scale-105 transition">
                                <img src="{{ $g['asset_url'] }}" alt="{{ $g['name'] }}" class="max-h-full max-w-full object-contain">
                            </div>
                            <h5 class="text-[11px] font-bold text-gray-800 truncate" title="{{ $g['name'] }}">{{ $g['name'] }}</h5>
                            <span class="text-[9px] text-indigo-600 font-semibold">{{ $g['source'] === 'plasto_master' ? 'Master' : 'Crop' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Drawer Footer -->
            <div class="p-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                <a href="{{ route('seller.catalog.pdf_studio') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                    <i class="fa-solid fa-crop-simple"></i>
                    <span>Crop more photos in PDF Studio &rarr;</span>
                </a>
                <button type="button" onclick="closeGalleryDrawer()" class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold text-xs">
                    Cancel
                </button>
            </div>

        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const jobId = {{ $job ? $job->id : 'null' }};

        let currentTargetType = null; // 'row', 'card', or 'batch'
        let currentTargetId = null;

        // Check URL for ?assign_img=...
        const urlParams = new URLSearchParams(window.location.search);
        const preselectedImg = urlParams.get('assign_img');
        if (preselectedImg) {
            // Pre-notify user they can select rows to attach this image
            console.log('Preselected image ready to attach:', preselectedImg);
        }

        function switchView(mode) {
            const viewTable = document.getElementById('viewTableContainer');
            const viewCards = document.getElementById('viewCardsContainer');
            const btnTable = document.getElementById('btnViewTable');
            const btnCards = document.getElementById('btnViewCards');

            if (mode === 'table') {
                viewTable.classList.remove('hidden');
                viewCards.classList.add('hidden');
                btnTable.className = "px-4 py-2 rounded-xl text-xs font-black bg-white text-emerald-700 shadow-xs flex items-center gap-2 transition";
                btnCards.className = "px-4 py-2 rounded-xl text-xs font-bold text-gray-500 hover:text-gray-800 flex items-center gap-2 transition";
            } else {
                viewTable.classList.add('hidden');
                viewCards.classList.remove('hidden');
                btnCards.className = "px-4 py-2 rounded-xl text-xs font-black bg-white text-emerald-700 shadow-xs flex items-center gap-2 transition";
                btnTable.className = "px-4 py-2 rounded-xl text-xs font-bold text-gray-500 hover:text-gray-800 flex items-center gap-2 transition";
            }
        }

        // Category & Live Search Filtering
        let activeCategory = 'ALL';
        let searchQuery = '';

        function filterByCategory(cat) {
            activeCategory = cat;

            const tabs = [
                { id: 'catTab_ALL', key: 'ALL', activeClass: 'bg-gray-900 text-white shadow-sm', inactiveClass: 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-200' },
                { id: 'catTab_UPVC', key: 'UPVC', activeClass: 'bg-blue-600 text-white shadow-md', inactiveClass: 'bg-white hover:bg-blue-50 text-blue-700 border border-blue-200' },
                { id: 'catTab_CPVC', key: 'CPVC', activeClass: 'bg-amber-600 text-white shadow-md', inactiveClass: 'bg-white hover:bg-amber-50 text-amber-700 border border-amber-200' },
                { id: 'catTab_SWR', key: 'SWR', activeClass: 'bg-purple-600 text-white shadow-md', inactiveClass: 'bg-white hover:bg-purple-50 text-purple-700 border border-purple-200' },
                { id: 'catTab_AGRI_OTHER', key: 'AGRI_OTHER', activeClass: 'bg-emerald-600 text-white shadow-md', inactiveClass: 'bg-white hover:bg-emerald-50 text-emerald-700 border border-emerald-200' }
            ];

            tabs.forEach(tab => {
                const el = document.getElementById(tab.id);
                if (!el) return;
                const baseClass = "cat-pill px-4 py-2.5 rounded-2xl text-xs font-black flex items-center gap-2 transition whitespace-nowrap ";
                if (tab.key === cat) {
                    el.className = baseClass + tab.activeClass;
                } else {
                    el.className = baseClass + tab.inactiveClass;
                }
            });

            applyCombinedFilter();
        }

        // Live Search Filter
        function filterItemsLive(q) {
            searchQuery = q.toLowerCase().trim();
            applyCombinedFilter();
        }

        function applyCombinedFilter() {
            // Table view
            document.querySelectorAll('.table-row-item').forEach(tr => {
                const text = tr.getAttribute('data-search') || '';
                const category = tr.getAttribute('data-category') || '';
                const matchSearch = (!searchQuery || text.includes(searchQuery));
                const matchCat = (activeCategory === 'ALL' || category === activeCategory);
                tr.style.display = (matchSearch && matchCat) ? '' : 'none';
            });
            // Cards view
            document.querySelectorAll('.product-family-card').forEach(card => {
                const text = card.getAttribute('data-search') || '';
                const category = card.getAttribute('data-category') || '';
                const matchSearch = (!searchQuery || text.includes(searchQuery));
                const matchCat = (activeCategory === 'ALL' || category === activeCategory);
                card.style.display = (matchSearch && matchCat) ? '' : 'none';
            });
        }

        // Multi-Row Selection logic
        function handleRowCheckboxChange(cb) {
            const tr = cb.closest('tr');
            if (cb.checked) {
                tr.classList.add('row-selected');
            } else {
                tr.classList.remove('row-selected');
            }
            updateFloatingBatchBar();
        }

        function toggleSelectAllRows(checked) {
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                const tr = cb.closest('tr');
                if (tr.style.display !== 'none') {
                    cb.checked = checked;
                    if (checked) tr.classList.add('row-selected');
                    else tr.classList.remove('row-selected');
                }
            });
            updateFloatingBatchBar();
        }

        function selectNRows(n) {
            clearRowSelection();
            const visibleCbs = Array.from(document.querySelectorAll('.row-checkbox')).filter(cb => cb.closest('tr').style.display !== 'none');
            for (let i = 0; i < Math.min(n, visibleCbs.length); i++) {
                visibleCbs[i].checked = true;
                visibleCbs[i].closest('tr').classList.add('row-selected');
            }
            updateFloatingBatchBar();
        }

        function selectSameProductRows() {
            clearRowSelection();
            const firstRow = document.querySelector('.table-row-item');
            if (!firstRow) return;
            const parentIdx = firstRow.getAttribute('data-parent-idx');
            document.querySelectorAll(`.table-row-item[data-parent-idx="${parentIdx}"]`).forEach(tr => {
                const cb = tr.querySelector('.row-checkbox');
                cb.checked = true;
                tr.classList.add('row-selected');
            });
            updateFloatingBatchBar();
        }

        function clearRowSelection() {
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = false;
                cb.closest('tr').classList.remove('row-selected');
            });
            document.getElementById('selectAllCheckbox').checked = false;
            updateFloatingBatchBar();
        }

        function updateFloatingBatchBar() {
            let count = 0;
            if (isDynamicMode) {
                count = document.querySelectorAll('.dynamic-row-checkbox:checked').length;
            } else {
                count = document.querySelectorAll('.row-checkbox:checked').length;
            }
            const bar = document.getElementById('floatingBatchBar');
            
            document.getElementById('floatingSelectedCount').innerText = count;
            document.getElementById('btnBatchCount').innerText = count;

            if (count > 0) {
                bar.classList.remove('hidden');
            } else {
                bar.classList.add('hidden');
            }
        }

        // Drawer handling
        function openGalleryDrawer(type, id) {
            currentTargetType = type;
            currentTargetId = id;

            const info = document.getElementById('drawerTargetInfo');
            if (type === 'row' || type === 'dynamic_row') {
                info.innerText = `Attaching photo to Row #${id}`;
            } else if (type === 'card') {
                info.innerText = `Attaching photo to Product Card #${id}`;
            } else {
                info.innerText = `Click any image to attach to selection`;
            }

            document.getElementById('galleryDrawerOverlay').classList.remove('hidden');
        }

        function openGalleryDrawerForBatch() {
            if (isDynamicMode) {
                const checked = Array.from(document.querySelectorAll('.dynamic-row-checkbox:checked')).map(cb => parseInt(cb.value));
                if (checked.length === 0) return;
                currentTargetType = 'dynamic_batch';
                currentTargetId = checked;
                document.getElementById('drawerTargetInfo').innerText = `Attaching photo to all ${checked.length} selected lines`;
                document.getElementById('galleryDrawerOverlay').classList.remove('hidden');
                return;
            }

            const checked = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => parseInt(cb.value));
            if (checked.length === 0) return;

            currentTargetType = 'batch';
            currentTargetId = checked;
            document.getElementById('drawerTargetInfo').innerText = `Attaching photo to all ${checked.length} selected lines`;
            document.getElementById('galleryDrawerOverlay').classList.remove('hidden');
        }

        function closeGalleryDrawer() {
            document.getElementById('galleryDrawerOverlay').classList.add('hidden');
        }

        function filterDrawerGallery(q) {
            const query = q.toLowerCase().trim();
            document.querySelectorAll('.drawer-img-card').forEach(card => {
                const name = card.getAttribute('data-name');
                card.style.display = (!query || name.includes(query)) ? '' : 'none';
            });
        }

        // User picks an image from drawer
        function selectDrawerImage(relUrl, assetUrl) {
            if (!currentTargetType) {
                closeGalleryDrawer();
                return;
            }

            // 1. Dynamic Row Single Assign
            if (currentTargetType === 'dynamic_row') {
                updateDynamicRowThumbnail(currentTargetId, relUrl, assetUrl);
                closeGalleryDrawer();
                return;
            }

            // 2. Dynamic Batch Assign (e.g. 6-8 rows selected!)
            if (currentTargetType === 'dynamic_batch') {
                currentTargetId.forEach(rId => updateDynamicRowThumbnail(rId, relUrl, assetUrl));
                clearDynamicRowSelection();
                closeGalleryDrawer();
                return;
            }

            // 3. Database Job Assign
            let payload = {
                job_id: jobId,
                image_url: relUrl
            };

            if (currentTargetType === 'row') {
                payload.row_indexes = [currentTargetId];
            } else if (currentTargetType === 'card') {
                payload.parent_indexes = [currentTargetId];
            } else if (currentTargetType === 'batch') {
                payload.row_indexes = currentTargetId; // array of checked rows
            }

            fetch("{{ route('seller.catalog.excel_mapper.assign_batch') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json"
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update UI immediately
                    if (currentTargetType === 'row') {
                        updateRowThumbnail(currentTargetId, assetUrl);
                    } else if (currentTargetType === 'card') {
                        updateCardThumbnail(currentTargetId, assetUrl);
                    } else if (currentTargetType === 'batch') {
                        currentTargetId.forEach(rId => updateRowThumbnail(rId, assetUrl));
                        clearRowSelection();
                    }
                    closeGalleryDrawer();
                } else {
                    alert(data.message || 'Error updating product photo.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Network error while assigning image.');
            });
        }

        function updateRowThumbnail(rId, assetUrl) {
            const cell = document.getElementById('img_thumb_row_' + rId);
            if (cell) {
                cell.outerHTML = `<img src="${assetUrl}" class="max-h-full max-w-full object-contain" id="img_thumb_row_${rId}">`;
            }
        }

        function updateCardThumbnail(cId, assetUrl) {
            const box = document.getElementById('card_thumb_' + cId);
            if (box) {
                box.outerHTML = `<img src="${assetUrl}" class="max-h-full max-w-full object-contain" id="card_thumb_${cId}">`;
            }
        }

        // ==========================================
        // 📑 DYNAMIC SAAS SPREADSHEET TABLE LOGIC
        // ==========================================
        let isDynamicMode = {{ (!$job || empty($products)) ? 'true' : 'false' }};
        let dynamicRows = [];
        let dynamicRowNextId = 1;

        function toggleDynamicMode(enable) {
            isDynamicMode = enable;
            const dynContainer = document.getElementById('dynamicSheetContainer');
            const viewTable = document.getElementById('viewTableContainer');
            const viewCards = document.getElementById('viewCardsContainer');

            if (enable) {
                if (dynContainer) dynContainer.classList.remove('hidden');
                if (viewTable) viewTable.classList.add('hidden');
                if (viewCards) viewCards.classList.add('hidden');
            } else {
                if (dynContainer) dynContainer.classList.add('hidden');
                if (viewTable) viewTable.classList.remove('hidden');
            }
            clearDynamicRowSelection();
            clearRowSelection();
        }

        function initDynamicSheet() {
            const rawStored = localStorage.getItem('vyapar_custom_excel_lines');
            if (rawStored) {
                try {
                    const parsed = JSON.parse(rawStored);
                    const lines = Array.isArray(parsed) ? parsed : (parsed.lines || []);
                    if (lines.length > 0) {
                        const promptBanner = document.getElementById('dynamicPdfPromptBanner');
                        const promptText = document.getElementById('dynamicPdfPromptText');
                        if (promptBanner) {
                            promptBanner.classList.remove('hidden');
                            if (promptText) promptText.innerText = `PDF Studio se ${lines.length} lines nikali gayi hain! Inhe dynamic table me use karein.`;
                        }

                        if (!jobId || isDynamicMode) {
                            loadLinesIntoDynamicRows(lines);
                            return;
                        }
                    }
                } catch (e) {
                    console.error('Failed to parse vyapar_custom_excel_lines:', e);
                }
            }

            if (!jobId && dynamicRows.length === 0) {
                loadSampleDynamicRows();
            }
        }

        function loadLinesIntoDynamicRows(lines) {
            dynamicRows = [];
            lines.forEach((lineText, idx) => {
                const text = (lineText || '').trim();
                if (!text) return;

                let category = 'UPVC';
                if (/CPVC/i.test(text)) category = 'CPVC';
                else if (/SWR|TRAP|DRAIN/i.test(text)) category = 'SWR';
                else if (/AGRI|SOLVENT/i.test(text)) category = 'AGRI_OTHER';

                const sizeMatch = text.match(/\b(\d+(\.\d+)?\s*(mm|inch|")|\d+\/\d+(")?|\d+x\d+)\b/i);
                const size = sizeMatch ? sizeMatch[0] : `Var-${idx + 1}`;

                const priceMatch = text.match(/(?:rs\.?|₹|\/)\s*(\d+(?:\.\d+)?)/i);
                const price = priceMatch ? parseFloat(priceMatch[1]) : (50 + (idx * 15));
                const cost = Math.round(price * 0.65);
                const retail = Math.round(price * 0.88);

                dynamicRows.push({
                    id: dynamicRowNextId++,
                    product_name: text,
                    size: size,
                    group_type: category,
                    mrp: price,
                    purchase_cost: cost,
                    retail_price: retail,
                    image_url: preselectedImg || '',
                    asset_url: preselectedImg ? ('/' + preselectedImg.replace(/^\//, '')) : ''
                });
            });

            renderDynamicRows();
            toggleDynamicMode(true);
        }

        function loadSampleDynamicRows() {
            const sampleLines = [
                "UPVC Elbow 90 Degree 25mm (1 inch) Heavy Duty",
                "UPVC Elbow 90 Degree 32mm (1-1/4 inch) Heavy Duty",
                "UPVC Elbow 90 Degree 40mm (1-1/2 inch) Heavy Duty",
                "UPVC Elbow 90 Degree 50mm (2 inch) Heavy Duty",
                "UPVC Brass Elbow 25mm x 1/2\" Threaded",
                "UPVC Brass Elbow 32mm x 1\" Threaded",
                "UPVC Equal Tee 25mm (1 inch) 3-Way",
                "UPVC Equal Tee 32mm (1-1/4 inch) 3-Way"
            ];
            loadLinesIntoDynamicRows(sampleLines);
        }

        function addDynamicRow() {
            dynamicRows.push({
                id: dynamicRowNextId++,
                product_name: 'New Product Item',
                size: '25mm (1")',
                group_type: 'UPVC',
                mrp: 100,
                purchase_cost: 65,
                retail_price: 85,
                image_url: '',
                asset_url: ''
            });
            renderDynamicRows();
        }

        function deleteDynamicRow(id) {
            dynamicRows = dynamicRows.filter(r => r.id !== id);
            renderDynamicRows();
            updateFloatingBatchBar();
        }

        function renderDynamicRows() {
            const tbody = document.getElementById('dynamicTableBody');
            const badge = document.getElementById('dynamicRowCountBadge');
            if (badge) badge.innerText = `${dynamicRows.length} Rows`;

            if (!tbody) return;

            if (dynamicRows.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="p-8 text-center text-gray-400 text-xs">
                            Koi rows nahi hain. Upar <b>"Load Sample Lines"</b> ya <b>"Extract from PDF Studio"</b> dabayein.
                        </td>
                    </tr>
                `;
                return;
            }

            let html = '';
            dynamicRows.forEach((r) => {
                const imgThumb = r.image_url 
                    ? `<img src="${r.asset_url || r.image_url}" class="max-h-full max-w-full object-contain" id="dyn_img_${r.id}">`
                    : `<span class="text-gray-300 text-xs" id="dyn_img_${r.id}"><i class="fa-solid fa-camera"></i></span>`;

                html += `
                    <tr class="hover:bg-slate-50 transition dynamic-row-item" id="dyn_row_${r.id}">
                        <td class="p-3 text-center">
                            <input type="checkbox" value="${r.id}" onchange="handleDynamicCheckboxChange(this)" class="dynamic-row-checkbox h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        </td>
                        <td class="p-3">
                            <div class="h-10 w-10 bg-gray-50 rounded-xl border border-gray-200 p-0.5 flex items-center justify-center overflow-hidden cursor-pointer hover:border-emerald-500 transition" onclick="openGalleryDrawer('dynamic_row', ${r.id})" title="Click to attach photo">
                                ${imgThumb}
                            </div>
                        </td>
                        <td class="p-3">
                            <select onchange="updateDynamicRowField(${r.id}, 'group_type', this.value)" class="text-[11px] font-bold py-1 px-1.5 rounded-lg border border-gray-200 bg-white focus:ring-1 focus:ring-emerald-500">
                                <option value="UPVC" ${r.group_type === 'UPVC' ? 'selected' : ''}>💧 UPVC</option>
                                <option value="CPVC" ${r.group_type === 'CPVC' ? 'selected' : ''}>🔥 CPVC</option>
                                <option value="SWR" ${r.group_type === 'SWR' ? 'selected' : ''}>🚰 SWR</option>
                                <option value="AGRI_OTHER" ${r.group_type === 'AGRI_OTHER' ? 'selected' : ''}>🌿 Other</option>
                            </select>
                        </td>
                        <td class="p-3">
                            <input type="text" value="${escapeHtml(r.product_name)}" oninput="updateDynamicRowField(${r.id}, 'product_name', this.value)" class="w-full text-xs font-bold text-gray-900 border border-transparent hover:border-gray-300 focus:border-emerald-600 focus:bg-white rounded-lg p-1 transition bg-transparent">
                        </td>
                        <td class="p-3">
                            <input type="text" value="${escapeHtml(r.size)}" oninput="updateDynamicRowField(${r.id}, 'size', this.value)" class="w-full text-xs font-mono font-bold text-gray-800 border border-transparent hover:border-gray-300 focus:border-emerald-600 focus:bg-white rounded-lg p-1 transition bg-transparent">
                        </td>
                        <td class="p-3 font-mono">
                            <div class="flex items-center">
                                <span class="text-gray-400 mr-0.5">₹</span>
                                <input type="number" step="0.5" value="${r.mrp}" oninput="updateDynamicRowField(${r.id}, 'mrp', parseFloat(this.value) || 0)" class="w-16 text-xs font-bold text-gray-800 border border-transparent hover:border-gray-300 focus:border-emerald-600 rounded-lg p-1 bg-transparent">
                            </div>
                        </td>
                        <td class="p-3 font-mono">
                            <div class="flex items-center">
                                <span class="text-gray-400 mr-0.5">₹</span>
                                <input type="number" step="0.5" value="${r.purchase_cost}" oninput="updateDynamicRowField(${r.id}, 'purchase_cost', parseFloat(this.value) || 0)" class="w-16 text-xs text-gray-600 border border-transparent hover:border-gray-300 focus:border-emerald-600 rounded-lg p-1 bg-transparent">
                            </div>
                        </td>
                        <td class="p-3 font-mono">
                            <div class="flex items-center">
                                <span class="text-gray-400 mr-0.5">₹</span>
                                <input type="number" step="0.5" value="${r.retail_price}" oninput="updateDynamicRowField(${r.id}, 'retail_price', parseFloat(this.value) || 0)" class="w-16 text-xs font-bold text-emerald-700 border border-transparent hover:border-gray-300 focus:border-emerald-600 rounded-lg p-1 bg-transparent">
                            </div>
                        </td>
                        <td class="p-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button type="button" onclick="openGalleryDrawer('dynamic_row', ${r.id})" class="h-7 w-7 rounded-lg bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 flex items-center justify-center text-xs transition" title="Pick Photo">
                                    <i class="fa-solid fa-image"></i>
                                </button>
                                <button type="button" onclick="deleteDynamicRow(${r.id})" class="h-7 w-7 rounded-lg bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 flex items-center justify-center text-xs transition" title="Delete Line">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        }

        function updateDynamicRowField(id, field, val) {
            const row = dynamicRows.find(r => r.id === id);
            if (row) {
                row[field] = val;
            }
        }

        function updateDynamicRowThumbnail(id, relUrl, assetUrl) {
            const row = dynamicRows.find(r => r.id === id);
            if (row) {
                row.image_url = relUrl;
                row.asset_url = assetUrl;
            }
            const el = document.getElementById('dyn_img_' + id);
            if (el) {
                el.outerHTML = `<img src="${assetUrl}" class="max-h-full max-w-full object-contain" id="dyn_img_${id}">`;
            }
        }

        function handleDynamicCheckboxChange(cb) {
            const tr = cb.closest('tr');
            if (cb.checked) {
                tr.classList.add('row-selected');
            } else {
                tr.classList.remove('row-selected');
            }
            updateFloatingBatchBar();
        }

        function toggleSelectAllDynamic(checked) {
            document.querySelectorAll('.dynamic-row-checkbox').forEach(cb => {
                cb.checked = checked;
                const tr = cb.closest('tr');
                if (checked) tr.classList.add('row-selected');
                else tr.classList.remove('row-selected');
            });
            const selAll = document.getElementById('dynamicSelectAll');
            if (selAll) selAll.checked = checked;
            updateFloatingBatchBar();
        }

        function selectNDynamicRows(n) {
            clearDynamicRowSelection();
            const cbs = document.querySelectorAll('.dynamic-row-checkbox');
            for (let i = 0; i < Math.min(n, cbs.length); i++) {
                cbs[i].checked = true;
                cbs[i].closest('tr').classList.add('row-selected');
            }
            updateFloatingBatchBar();
        }

        function clearDynamicRowSelection() {
            document.querySelectorAll('.dynamic-row-checkbox').forEach(cb => {
                cb.checked = false;
                cb.closest('tr').classList.remove('row-selected');
            });
            const selAll = document.getElementById('dynamicSelectAll');
            if (selAll) selAll.checked = false;
            updateFloatingBatchBar();
        }

        function exportDynamicToCsv() {
            if (dynamicRows.length === 0) {
                alert('Export karne ke liye koi rows nahi hain.');
                return;
            }

            const sheetName = (document.getElementById('dynamicSheetName')?.value || 'catalog').trim().replace(/[^a-zA-Z0-9_-]/g, '_');
            let csv = "Category,Product Name,Size/Dimension,MRP,Purchase Cost,Retail Price,Image URL\n";

            dynamicRows.forEach(r => {
                const escapeCsv = (str) => `"${(str || '').toString().replace(/"/g, '""')}"`;
                csv += [
                    escapeCsv(r.group_type),
                    escapeCsv(r.product_name),
                    escapeCsv(r.size),
                    r.mrp,
                    r.purchase_cost,
                    r.retail_price,
                    escapeCsv(r.image_url)
                ].join(',') + "\n";
            });

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.setAttribute('download', `${sheetName}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function saveDynamicSheetToBackend() {
            if (dynamicRows.length === 0) {
                alert('Save karne ke liye kam se kam 1 row hona zaroori hai.');
                return;
            }

            const sheetTitle = (document.getElementById('dynamicSheetName')?.value || 'PDF Extracted Sheet').trim();
            const btn = document.getElementById('btnSaveDynamicSheet');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Saving...`;
            }

            fetch("{{ route('seller.catalog.excel_mapper.create_sheet') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    sheet_name: sheetTitle,
                    rows: dynamicRows
                })
            })
            .then(res => res.json())
            .then(data => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = `<i class="fa-solid fa-cloud-arrow-up"></i> Save to Catalog & Publish`;
                }

                if (data.success) {
                    alert(data.message || 'Sheet saved successfully!');
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    }
                } else {
                    alert(data.message || 'Error saving sheet.');
                }
            })
            .catch(err => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = `<i class="fa-solid fa-cloud-arrow-up"></i> Save to Catalog & Publish`;
                }
                console.error(err);
                alert('Network error while saving dynamic sheet.');
            });
        }

        function escapeHtml(str) {
            return (str || '').toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        // Initialize when DOM is ready
        window.addEventListener('DOMContentLoaded', () => {
            initDynamicSheet();
        });
    </script>
</body>
</html>
