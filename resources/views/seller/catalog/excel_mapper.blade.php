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
                            <tr class="hover:bg-slate-50 transition table-row-item" id="row_{{ $row['row_id'] }}" data-search="{{ strtolower($row['product_name'] . ' ' . $row['variant_name'] . ' ' . $row['size']) }}" data-parent-idx="{{ $row['parent_idx'] }}">
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
                <div class="product-family-card bg-white rounded-3xl border border-gray-200 p-5 shadow-xs hover:shadow-md transition space-y-4" data-card-idx="{{ $pIdx }}" data-search="{{ strtolower($prod['name']) }}">
                    
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
                                <h3 class="text-base font-black text-gray-900">{{ $prod['name'] }}</h3>
                                <div class="flex items-center gap-2 text-xs text-gray-500 mt-1">
                                    <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-bold text-[10px]">{{ $prod['category'] ?? 'Industrial' }}</span>
                                    <span>•</span>
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
        const jobId = {{ $job->id }};

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

        // Live Search Filter
        function filterItemsLive(q) {
            const query = q.toLowerCase().trim();
            // Table view
            document.querySelectorAll('.table-row-item').forEach(tr => {
                const text = tr.getAttribute('data-search');
                tr.style.display = (!query || text.includes(query)) ? '' : 'none';
            });
            // Cards view
            document.querySelectorAll('.product-family-card').forEach(card => {
                const text = card.getAttribute('data-search');
                card.style.display = (!query || text.includes(query)) ? '' : 'none';
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
            const checked = document.querySelectorAll('.row-checkbox:checked');
            const bar = document.getElementById('floatingBatchBar');
            const count = checked.length;
            
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
            if (type === 'row') {
                info.innerText = `Attaching to Row #${id}`;
            } else if (type === 'card') {
                info.innerText = `Attaching to Product Card #${id}`;
            } else {
                info.innerText = `Click any image to attach to selection`;
            }

            document.getElementById('galleryDrawerOverlay').classList.remove('hidden');
        }

        function openGalleryDrawerForBatch() {
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
    </script>
</body>
</html>
