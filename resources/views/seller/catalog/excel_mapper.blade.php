<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel Multi-Row & Variant Card Mapper - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
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
                    <button type="button" onclick="toggleDynamicMode(true)" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-600/30 flex items-center gap-2 transition active:scale-95">
                        <i class="fa-solid fa-file-excel"></i>
                        <span>+ Import New Excel</span>
                    </button>
                    <button type="button" onclick="downloadSampleExcelTemplate()" class="px-3.5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition flex items-center gap-1.5" title="Download Excel template (.xlsx)">
                        <i class="fa-solid fa-download text-emerald-600"></i>
                        <span>Excel Template</span>
                    </button>
                    @if($job)
                        <form action="{{ route('seller.catalog.excel_mapper.publish_direct') }}" method="POST" id="directPublishForm" onsubmit="return confirm('Kya aap in sabhi products ko apne live store par publish karna chahte hain?');">
                            @csrf
                            <input type="hidden" name="job_id" value="{{ $job->id }}">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-md shadow-indigo-600/30 flex items-center gap-2 transition active:scale-95">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <span>Publish Saved Job #{{ $job->id }}</span>
                            </button>
                        </form>
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
        <!-- 🎛️ TOP MODE SWITCHER TABS                  -->
        <!-- ========================================== -->
        <div class="flex flex-wrap items-center gap-3 bg-white p-2 rounded-2xl border border-gray-200 shadow-2xs">
            <button type="button" onclick="toggleDynamicMode(true)" id="tabDynamicExcelMode" class="flex-1 py-2.5 rounded-xl text-xs font-black transition flex items-center justify-center gap-2 bg-emerald-600 text-white shadow-xs">
                <i class="fa-solid fa-file-excel"></i>
                <span>1. Fresh Excel Upload & Photo Linker</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-bold">Active</span>
            </button>
            @if($job && !empty($products))
                <button type="button" onclick="toggleDynamicMode(false)" id="tabSavedJobMode" class="flex-1 py-2.5 rounded-xl text-xs font-black transition flex items-center justify-center gap-2 bg-gray-100 text-gray-700 hover:bg-gray-200">
                    <i class="fa-solid fa-box-archive text-indigo-600"></i>
                    <span>2. View Saved Job (#{{ $job->id }}: {{ $job->filename }})</span>
                </button>
            @endif
        </div>

        <!-- ========================================== -->
        <!-- 📑 DYNAMIC SAAS SPREADSHEET TABLE          -->
        <!-- (Supports PDF Extracted Lines & 6-8 Link)  -->
        <!-- ========================================== -->
        <div id="dynamicSheetContainer" class="space-y-4">
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
                    <button type="button" onclick="document.getElementById('excelFileInput').click()" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-600/30 flex items-center gap-1.5 transition active:scale-95" title="Import full Excel sheet (.xlsx, .xls, .csv)">
                        <i class="fa-solid fa-file-excel"></i>
                        <span>Import Excel File</span>
                    </button>
                    <input type="file" id="excelFileInput" accept=".xlsx, .xls, .csv" class="hidden" onchange="handleExcelFileUpload(event)">

                    <button type="button" onclick="openGalleryDrawer(null, null)" class="px-3.5 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-extrabold text-xs border border-indigo-200 flex items-center gap-1.5 transition" title="Browse all photos from Central Media Bank">
                        <i class="fa-solid fa-images"></i>
                        <span>Browse Photo Bank</span>
                    </button>

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

            <!-- ======================================================== -->
            <!-- 📥 EXCEL RATE LIST UPLOAD DROPZONE                        -->
            <!-- ======================================================== -->
            <div id="excelDropzoneCard" class="bg-gradient-to-r from-emerald-50 via-teal-50 to-indigo-50 border-2 border-dashed border-emerald-300 hover:border-emerald-500 rounded-3xl p-6 text-center cursor-pointer transition shadow-xs group" onclick="document.getElementById('excelFileInput').click()" ondragover="event.preventDefault(); this.classList.add('border-emerald-600', 'bg-emerald-100/40');" ondragleave="this.classList.remove('border-emerald-600', 'bg-emerald-100/40');" ondrop="event.preventDefault(); this.classList.remove('border-emerald-600', 'bg-emerald-100/40'); if(event.dataTransfer.files.length) handleExcelDroppedFile(event.dataTransfer.files[0]);">
                <div class="max-w-xl mx-auto space-y-2">
                    <div class="h-14 w-14 mx-auto rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-emerald-600/30 group-hover:scale-110 transition">
                        <i class="fa-solid fa-file-excel"></i>
                    </div>
                    <h3 class="text-base font-black text-gray-900">
                        Upload Your Excel Sheet Here (.xlsx, .xls, .csv)
                    </h3>
                    <p class="text-xs text-gray-600 font-medium">
                        Click karein ya apni Excel Rate List / Price Sheet yahan Drag & Drop karein. Sabhi products aur sizes table me turant list ho jayenge!
                    </p>
                    <div class="pt-2 flex flex-wrap items-center justify-center gap-3">
                        <button type="button" class="px-5 py-2.5 rounded-xl bg-emerald-600 group-hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-600/20 flex items-center gap-2 transition">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span>Choose Excel File</span>
                        </button>
                        <button type="button" onclick="event.stopPropagation(); loadSampleDynamicRows();" class="px-4 py-2.5 rounded-xl bg-white hover:bg-gray-100 text-gray-700 font-bold text-xs border border-gray-200 shadow-2xs flex items-center gap-1.5 transition">
                            <i class="fa-solid fa-wand-magic-sparkles text-amber-500"></i>
                            <span>Try Demo Sheet (12 Sample Rows)</span>
                        </button>
                        <button type="button" onclick="event.stopPropagation(); downloadSampleExcelTemplate();" class="px-4 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs border border-indigo-200 shadow-2xs flex items-center gap-1.5 transition">
                            <i class="fa-solid fa-file-excel text-emerald-600"></i>
                            <span>Download Blank Template (.xlsx)</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- 📷 LIVE PHOTO BANK TRAY (DIRECT ON-PAGE ACCESS)           -->
            <!-- ======================================================== -->
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-4 space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="h-9 w-9 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base shadow-xs">
                            <i class="fa-solid fa-camera-retro"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-gray-900 flex items-center gap-2">
                                <span>Live Photo Bank</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] bg-indigo-100 text-indigo-800 font-black" id="onPagePhotoCount">{{ count($galleryImages) }} Photos</span>
                            </h3>
                            <p class="text-[11px] text-gray-500">
                                Select 1 or more rows below & click any photo to attach. (Cloudinary + Central Bank)
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="relative w-48 sm:w-64">
                            <input type="text" id="onPagePhotoSearch" placeholder="Search photos (Elbow, Tee, Plumber)..." oninput="filterOnPagePhotoBank(this.value)" class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-gray-300 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-indigo-600 bg-gray-50 focus:bg-white">
                            <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-gray-400 text-xs"></i>
                        </div>
                        <button type="button" onclick="openGalleryDrawer('browse', null)" class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold border border-indigo-200 flex items-center gap-1.5 transition">
                            <i class="fa-solid fa-expand"></i>
                            <span>Full View Drawer</span>
                        </button>
                    </div>
                </div>

                <!-- Horizontal Photo Strip -->
                <div class="overflow-x-auto pb-2 border-t border-gray-100 pt-2" id="onPagePhotoStripContainer">
                    <div class="flex items-center gap-3 min-w-max py-1" id="onPagePhotoGrid">
                        @forelse($galleryImages as $g)
                            <div class="on-page-photo-card flex items-center gap-2.5 p-2 bg-gray-50 hover:bg-indigo-50 border border-gray-200 hover:border-indigo-400 rounded-2xl cursor-pointer transition shrink-0 group" onclick="handleOnPagePhotoClick('{{ $g['url'] }}', '{{ $g['asset_url'] }}')" data-name="{{ strtolower($g['name']) }}">
                                <div class="h-14 w-14 bg-white rounded-xl p-1 flex items-center justify-center overflow-hidden border border-gray-200 group-hover:scale-105 transition">
                                    <img src="{{ $g['asset_url'] }}" alt="{{ $g['name'] }}" class="max-h-full max-w-full object-contain">
                                </div>
                                <div class="max-w-[120px]">
                                    <h5 class="text-[11px] font-bold text-gray-800 truncate" title="{{ $g['name'] }}">{{ $g['name'] }}</h5>
                                    <span class="text-[9px] text-indigo-600 font-semibold block">{{ $g['source'] === 'plasto_master' ? 'Master' : 'Crop' }}</span>
                                    <span class="text-[9px] text-emerald-600 font-bold group-hover:underline">Attach &rarr;</span>
                                </div>
                            </div>
                        @empty
                            <div class="py-4 px-4 text-center text-gray-400 text-xs flex items-center gap-2" id="onPagePhotoLoadingMsg">
                                <i class="fa-solid fa-spinner fa-spin text-indigo-500"></i>
                                <span>Loading Photo Bank from Cloudinary...</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Dynamic Table Card -->
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3 bg-slate-50/70">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black text-gray-800 uppercase tracking-wider">Dynamic Spreadsheet Lines:</span>
                        <span class="text-xs text-gray-500" id="dynamicTableSubtitle">(Select 6-8 lines to link photo or group into 1 card)</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs flex-wrap">
                        <span class="text-gray-500 font-semibold">Quick Select:</span>
                        <button type="button" onclick="selectNDynamicRows(4)" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 hover:bg-gray-100 font-bold text-gray-700" title="Top ke 4 rows select karein">Next 4</button>
                        <button type="button" onclick="selectNDynamicRows(6)" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 hover:bg-gray-100 font-bold text-gray-700" title="Top ke 6 rows select karein">Next 6</button>
                        <button type="button" onclick="selectNDynamicRows(8)" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 hover:bg-gray-100 font-bold text-gray-700" title="Top ke 8 rows select karein">Next 8</button>
                        <button type="button" onclick="selectSameDynamicFamily()" class="px-2.5 py-1 rounded-lg bg-blue-50 border border-blue-200 hover:bg-blue-100 font-bold text-blue-700" title="Ek hi item ke sabhi sizes ek sath select karein">Same Family</button>
                        <button type="button" onclick="clearDynamicRowSelection()" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 font-bold text-gray-600" title="Selected checkboxes uncheck karein">Clear</button>
                        <button type="button" onclick="resetDynamicTable()" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 flex items-center gap-1 transition" title="Purane sabhi rows ko delete karein">
                            <i class="fa-solid fa-trash-can"></i>
                            <span>Clear All Rows</span>
                        </button>
                        <button type="button" onclick="openColumnMapperModal()" id="btnOpenColumnMapper" class="px-3 py-1 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 font-extrabold text-xs border border-teal-200 flex items-center gap-1.5 transition ml-1" title="Excel Sheet ke columns ko dobara map karein">
                            <i class="fa-solid fa-table-columns text-teal-600"></i>
                            <span>Map Columns</span>
                        </button>
                        <button type="button" onclick="groupSelectedRowsIntoCard()" class="px-3 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-sm flex items-center gap-1.5 transition ml-1">
                            <i class="fa-solid fa-layer-group"></i>
                            <span>Group Selected into Card</span>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs" id="dynamicSpreadsheetTable">
                        <thead class="bg-gray-50 text-gray-500 text-[11px] font-bold uppercase border-b border-gray-200">
                            <tr>
                                <th class="p-3 w-10 text-center">
                                    <input type="checkbox" id="dynamicSelectAll" onchange="toggleSelectAllDynamic(this.checked)" class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                </th>
                                <th class="p-3 w-12">Photo</th>
                                <th class="p-3 w-24">Category</th>
                                <th class="p-3 min-w-[200px]">Product / Item Name</th>
                                <th class="p-3 w-28">Item Code</th>
                                <th class="p-3 w-24">Size</th>
                                <th class="p-3 w-28">Packing</th>
                                <th class="p-3 w-20">MRP (₹)</th>
                                <th class="p-3 w-20">Cost (₹)</th>
                                <th class="p-3 w-20">Selling (₹)</th>
                                <th class="p-3 w-16">Stock</th>
                                <th class="p-3 w-20 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="dynamicTableBody" class="divide-y divide-gray-100 font-medium">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Grouped Product Cards Container (When user groups 6-8 sizes into 1 card) -->
            <div id="groupedCardsSection" class="space-y-4 pt-2">
                <div class="flex items-center justify-between pb-2 border-b border-gray-200">
                    <div class="flex items-center gap-2">
                        <div class="h-8 w-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shadow-xs">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-gray-900 flex items-center gap-2">
                                <span>Grouped Product Cards</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-indigo-100 text-indigo-800 font-bold" id="groupedCardCountBadge">0 Cards</span>
                            </h3>
                            <p class="text-[11px] text-gray-500">6-8 sizes ko jodkar banaye gaye cards. Live store par customer in sabhi sizes ke aage quantity dalker order karega.</p>
                        </div>
                    </div>
                </div>

                <div id="groupedCardsGrid" class="space-y-4">
                    <!-- Populated via JS -->
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
        <div id="savedJobContainer" class="space-y-6 hidden">

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

        <!-- 🏷️ Dynamic Category Filter Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1" id="categoryFilterContainer">
            <button type="button" onclick="filterByCategory('ALL')" data-cat-key="ALL" id="catTab_ALL" class="cat-pill px-4 py-2.5 rounded-2xl text-xs font-black bg-gray-900 text-white shadow-sm flex items-center gap-2 transition whitespace-nowrap">
                <i class="fa-solid fa-layer-group"></i>
                <span>All Categories</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-mono" id="catCount_ALL">{{ count($products) }}</span>
            </button>
            @if(isset($categoryCounts) && count($categoryCounts) > 0)
                @foreach($categoryCounts as $catName => $count)
                    <button type="button" onclick="filterByCategory('{{ addslashes($catName) }}')" data-cat-key="{{ $catName }}" id="catTab_{{ Str::slug($catName) }}" class="cat-pill px-4 py-2.5 rounded-2xl text-xs font-extrabold bg-white hover:bg-slate-50 text-gray-700 border border-gray-200 shadow-xs flex items-center gap-2 transition whitespace-nowrap">
                        <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                        <span>{{ $catName }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-800 font-mono font-bold">{{ $count }}</span>
                    </button>
                @endforeach
            @else
                <button type="button" onclick="filterByCategory('UPVC')" data-cat-key="UPVC" id="catTab_upvc" class="cat-pill px-4 py-2.5 rounded-2xl text-xs font-extrabold bg-white hover:bg-blue-50 text-blue-700 border border-blue-200 shadow-xs flex items-center gap-2 transition whitespace-nowrap">
                    <span class="h-2.5 w-2.5 rounded-full bg-blue-600"></span>
                    <span>💧 UPVC Pipes & Fittings</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-blue-100 text-blue-800 font-mono font-bold">{{ $upvcCount ?? 0 }}</span>
                </button>
                <button type="button" onclick="filterByCategory('CPVC')" data-cat-key="CPVC" id="catTab_cpvc" class="cat-pill px-4 py-2.5 rounded-2xl text-xs font-extrabold bg-white hover:bg-amber-50 text-amber-700 border border-amber-200 shadow-xs flex items-center gap-2 transition whitespace-nowrap">
                    <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                    <span>🔥 CPVC Pipes & Fittings</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-100 text-amber-800 font-mono font-bold">{{ $cpvcCount ?? 0 }}</span>
                </button>
                <button type="button" onclick="filterByCategory('SWR')" data-cat-key="SWR" id="catTab_swr" class="cat-pill px-4 py-2.5 rounded-2xl text-xs font-extrabold bg-white hover:bg-purple-50 text-purple-700 border border-purple-200 shadow-xs flex items-center gap-2 transition whitespace-nowrap">
                    <span class="h-2.5 w-2.5 rounded-full bg-purple-600"></span>
                    <span>🚰 SWR Drainage & Traps</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-purple-100 text-purple-800 font-mono font-bold">{{ $swrCount ?? 0 }}</span>
                </button>
                <button type="button" onclick="filterByCategory('Other')" data-cat-key="Other" id="catTab_other" class="cat-pill px-4 py-2.5 rounded-2xl text-xs font-extrabold bg-white hover:bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-xs flex items-center gap-2 transition whitespace-nowrap">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-600"></span>
                    <span>🌿 Agri, Solvents & Others</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-mono font-bold">{{ $otherCount ?? 0 }}</span>
                </button>
            @endif
        </div>

        <!-- Global Autocomplete Datalist for Hardware & Industrial Categories -->
        <datalist id="allCategoriesList">
            @foreach($categories as $c)
                <option value="{{ $c->name }}"></option>
            @endforeach
            <option value="Pipes & Fittings"></option>
            <option value="CPVC Pipes & Fittings"></option>
            <option value="UPVC Pipes & Fittings"></option>
            <option value="SWR Drainage"></option>
            <option value="Agri & Solvents"></option>
            <option value="Paints & Coatings"></option>
            <option value="Electrical & Wiring"></option>
            <option value="Plywood & Hardware"></option>
            <option value="Pumps & Motors"></option>
            <option value="Sanitaryware & Bath"></option>
            <option value="General Hardware"></option>
        </datalist>

        <!-- ========================================== -->
        <!-- VIEW 1: MULTI-ROW SPREADSHEET TABLE VIEW   -->
        <!-- ========================================== -->
        <div id="viewTableContainer" class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3 bg-slate-50/60">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-black text-gray-800 uppercase tracking-wider">Excel Spreadsheet Rows:</span>
                    <span class="text-xs text-gray-500">({{ count($flatRows) }} size variants total)</span>
                </div>
                <div class="flex items-center gap-2 text-xs flex-wrap">
                    <span class="text-gray-500 font-semibold">Quick Select:</span>
                    <button type="button" onclick="selectNRows(4)" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 hover:bg-gray-100 font-bold text-gray-700" title="Top ke 4 rows select karein">Next 4</button>
                    <button type="button" onclick="selectNRows(6)" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 hover:bg-gray-100 font-bold text-gray-700" title="Top ke 6 rows select karein">Next 6</button>
                    <button type="button" onclick="selectNRows(8)" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 hover:bg-gray-100 font-bold text-gray-700" title="Top ke 8 rows select karein">Next 8</button>
                    <button type="button" onclick="selectSameProductRows()" class="px-2.5 py-1 rounded-lg bg-blue-50 border border-blue-200 hover:bg-blue-100 font-bold text-blue-700" title="Ek hi item ke sabhi sizes ek sath select karein">Same Family</button>
                    <button type="button" onclick="clearRowSelection(); clearDynamicRowSelection();" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 font-bold text-gray-600">Clear</button>
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

        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" onclick="clearRowSelection(); clearDynamicRowSelection();" class="px-3 py-2 rounded-xl text-gray-400 hover:text-white text-xs font-bold transition">
                Deselect
            </button>
            <div id="addToExistingCardWrapper" class="hidden flex items-center gap-1.5 bg-gray-800/80 p-1 rounded-2xl border border-amber-400/30">
                <select id="selectTargetCard" class="bg-gray-900 text-amber-300 text-xs font-bold py-1.5 px-2 rounded-xl border border-gray-700 focus:outline-none max-w-[180px] truncate">
                    <!-- Populated dynamically -->
                </select>
                <button type="button" onclick="addSelectedRowsToTargetCard()" class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-gray-950 font-black text-xs shadow-md flex items-center gap-1 transition active:scale-95" title="Selected rows ko is existing card me jod do">
                    <i class="fa-solid fa-plus"></i>
                    <span>Add to Card</span>
                </button>
            </div>
            <button type="button" onclick="groupSelectedRowsIntoCard()" class="px-4 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs shadow-lg shadow-indigo-600/30 flex items-center gap-1.5 transition active:scale-95" title="Combine selected rows into a new Product Card">
                <i class="fa-solid fa-layer-group"></i>
                <span>Group into New Card</span>
            </button>
            <button type="button" onclick="openGalleryDrawerForBatch()" class="px-4 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-gray-900 font-black text-xs shadow-lg shadow-emerald-500/30 flex items-center gap-2 transition active:scale-95">
                <i class="fa-solid fa-images"></i>
                <span>Assign Image (<span id="btnBatchCount">0</span>)</span>
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 📊 EXCEL COLUMN MAPPING MODAL              -->
    <!-- ========================================== -->
    <div id="excelColumnMapModal" class="fixed inset-0 bg-black/70 backdrop-blur-xs z-[9999] hidden flex items-center justify-center p-4 overflow-y-auto" onclick="closeColumnMapperModal()">
        <div class="bg-white rounded-3xl max-w-3xl w-full shadow-2xl border border-gray-200 overflow-hidden flex flex-col my-8 max-h-[90vh]" onclick="event.stopPropagation()">
            
            <!-- Modal Header -->
            <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-emerald-50 via-teal-50 to-indigo-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-lg shadow-md shadow-emerald-600/30">
                        <i class="fa-solid fa-table-columns"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                            <span>Excel Column Mapper</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-bold" id="mapTotalRowsCount">0 Rows Found</span>
                        </h3>
                        <p class="text-xs text-gray-600 font-medium">
                            Apni Excel sheet ke columns ko sahi fields ke sath match karein taaki <b>MRP (₹)</b> aur <b>Sizes</b> accurate load hon!
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeColumnMapperModal()" class="h-8 w-8 rounded-xl bg-white hover:bg-gray-100 text-gray-500 hover:text-gray-800 flex items-center justify-center text-sm shadow-2xs transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-6 space-y-6 overflow-y-auto flex-1">
                
                <!-- Quick Settings: Header Row -->
                <div class="bg-amber-50/70 border border-amber-200 rounded-2xl p-4 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-lightbulb text-amber-500 text-base"></i>
                        <span class="font-bold text-amber-900">Sheet me Data (Items) kis Row number se shuru hota hai?</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="font-bold text-gray-700">Data Starts At Row:</label>
                        <input type="number" id="mapDataStartRow" value="2" min="1" max="50" onchange="updateMappingPreview()" class="w-16 px-2 py-1 rounded-lg border border-amber-300 font-bold text-center bg-white">
                    </div>
                </div>

                <!-- Section 1: MANDATORY CORE FIELDS (🟢 ZAROORI COLUMNS) -->
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                            🟢 MANDATORY COLUMNS (Zaroori)
                        </span>
                        <span class="text-xs text-gray-500 font-medium">Sirf Product Name aur Prices zaroori hain</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <!-- 1. Product Name -->
                        <div class="bg-emerald-50/40 p-3.5 rounded-2xl border-2 border-emerald-200 space-y-1">
                            <label class="text-xs font-black text-gray-900 flex items-center justify-between">
                                <span>📦 Product / Item Name:</span>
                                <span class="text-[10px] text-rose-500 font-bold">*Required</span>
                            </label>
                            <select id="mapColName" onchange="updateMappingPreview()" class="w-full text-xs font-bold py-2 px-2.5 rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                <!-- Populated dynamically -->
                            </select>
                            <p class="text-[10px] text-gray-500">Item description ya pipe/fitting ka naam (jaise: UPVC Agri Elbow)</p>
                        </div>

                        <!-- 2. MRP (₹) -->
                        <div class="bg-emerald-50/60 p-3.5 rounded-2xl border-2 border-emerald-300 space-y-1">
                            <label class="text-xs font-black text-emerald-950 flex items-center justify-between">
                                <span>🏷️ MRP / List Price (₹):</span>
                                <span class="text-[10px] text-rose-500 font-bold">*Required</span>
                            </label>
                            <select id="mapColMrp" onchange="updateMappingPreview()" class="w-full text-xs font-bold py-2 px-2.5 rounded-xl border border-emerald-400 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                <!-- Populated dynamically -->
                            </select>
                            <p class="text-[10px] text-emerald-800 font-semibold">⚠️ S.No (1, 2) select na ho, asli MRP column select karein!</p>
                        </div>

                        <!-- 3. Retail / Selling Price (₹) -->
                        <div class="bg-emerald-50/40 p-3.5 rounded-2xl border-2 border-emerald-200 space-y-1">
                            <label class="text-xs font-black text-gray-900 flex items-center justify-between">
                                <span>🛒 Selling Price / Rate A (₹):</span>
                                <span class="text-[10px] text-emerald-700 font-bold">*Mandatory</span>
                            </label>
                            <select id="mapColRetail" onchange="updateMappingPreview()" class="w-full text-xs font-bold py-2 px-2.5 rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                <!-- Populated dynamically -->
                            </select>
                            <p class="text-[10px] text-emerald-800 font-semibold">Storefront par customer ko sirf yahi price dikhega (MRP cross hoke: ~~₹100~~ ₹85).</p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: HARDWARE & PLUMBING ATTRIBUTES (🟡 OPTIONAL - MILE TO THIK, NA MILE TO THIK) -->
                <div class="space-y-2 pt-2 border-t border-gray-100">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-amber-100 text-amber-900 border border-amber-300">
                            🟡 HARDWARE ATTRIBUTES (Optional - Mile to thik, na mile to thik)
                        </span>
                        <span class="text-xs text-gray-500 font-medium">Hardware items (Solvents, Tapes) me size nahi hota, to blank reh sakta hai</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <!-- 4. Size / Dimension -->
                        <div class="bg-slate-50 p-3.5 rounded-2xl border border-gray-200 space-y-1">
                            <label class="text-xs font-black text-gray-900 flex items-center justify-between">
                                <span>📏 Size / Dimension:</span>
                                <span class="text-[10px] text-gray-500 font-bold">(Optional)</span>
                            </label>
                            <select id="mapColSize" onchange="updateMappingPreview()" class="w-full text-xs font-bold py-2 px-2.5 rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <!-- Populated dynamically -->
                            </select>
                            <p class="text-[10px] text-gray-500">15mm, 1 inch, 25x20. Khali rehne par 'Standard' banega.</p>
                        </div>

                        <!-- 5. Product / Item Code -->
                        <div class="bg-slate-50 p-3.5 rounded-2xl border border-gray-200 space-y-1">
                            <label class="text-xs font-black text-gray-900 flex items-center justify-between">
                                <span>🔖 Product / Item Code:</span>
                                <span class="text-[10px] text-gray-500 font-bold">(Optional)</span>
                            </label>
                            <select id="mapColCode" onchange="updateMappingPreview()" class="w-full text-xs font-bold py-2 px-2.5 rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <!-- Populated dynamically -->
                            </select>
                            <p class="text-[10px] text-gray-500">Item Code, SKU, Art No (jaise: UPVC-01, PL-101)</p>
                        </div>

                        <!-- 6. Packing 1 (Box / Inner Pack) -->
                        <div class="bg-slate-50 p-3.5 rounded-2xl border border-gray-200 space-y-1">
                            <label class="text-xs font-black text-gray-900 flex items-center justify-between">
                                <span>📦 Packing 1 (Box / Inner):</span>
                                <span class="text-[10px] text-gray-500 font-bold">(Optional)</span>
                            </label>
                            <select id="mapColPack1" onchange="updateMappingPreview()" class="w-full text-xs font-bold py-2 px-2.5 rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <!-- Populated dynamically -->
                            </select>
                            <p class="text-[10px] text-gray-500">Std Pkg, Box Pack (jaise: 20 pcs, 50 pcs)</p>
                        </div>

                        <!-- 7. Packing 2 (Carton / Master Bag) -->
                        <div class="bg-slate-50 p-3.5 rounded-2xl border border-gray-200 space-y-1">
                            <label class="text-xs font-black text-gray-900 flex items-center justify-between">
                                <span>📦 Packing 2 (Carton/Bag):</span>
                                <span class="text-[10px] text-gray-500 font-bold">(Optional)</span>
                            </label>
                            <select id="mapColPack2" onchange="updateMappingPreview()" class="w-full text-xs font-bold py-2 px-2.5 rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <!-- Populated dynamically -->
                            </select>
                            <p class="text-[10px] text-gray-500">Master Bag, Outer Carton (jaise: 200 pcs, 500 pcs)</p>
                        </div>
                    </div>
                </div>

                <!-- Section 3: PURCHASE RATES & STOCK (🔵 MULTI-TIER COST & INVENTORY - OPTIONAL) -->
                <div class="space-y-2 pt-2 border-t border-gray-100">
                    <div class="flex items-center justify-between gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-blue-100 text-blue-900 border border-blue-300">
                            🔵 OPTIONAL TIERS: RATE B, C, STOCK & CATEGORY (Customer se Hidden)
                        </span>
                        <span class="text-xs text-blue-700 font-medium">⚠️ Ye rates public customer ko KABHI nahi dikhte (Wholesale/B2B ke liye hain)</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                        <!-- 8. Cost Price 1 -->
                        <div class="bg-slate-50 p-3 rounded-2xl border border-gray-200 space-y-1">
                            <label class="text-xs font-black text-gray-900 flex items-center justify-between">
                                <span>💰 Cost Price 1:</span>
                                <span class="text-[10px] text-gray-500">(Optional)</span>
                            </label>
                            <select id="mapColCost" onchange="updateMappingPreview()" class="w-full text-xs font-bold py-1.5 px-2 rounded-xl border border-gray-300 bg-white focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                <!-- Populated dynamically -->
                            </select>
                            <p class="text-[9px] text-gray-500">Kharid rate (Cost Price)</p>
                        </div>

                        <!-- 9. Rate B (Wholesale / Plumber Rate) -->
                        <div class="bg-slate-50 p-3 rounded-2xl border border-gray-200 space-y-1">
                            <label class="text-xs font-black text-gray-900 flex items-center justify-between">
                                <span>💰 Rate B (Wholesale):</span>
                                <span class="text-[10px] text-gray-500">(Optional)</span>
                            </label>
                            <select id="mapColCost2" onchange="updateMappingPreview()" class="w-full text-xs font-bold py-1.5 px-2 rounded-xl border border-gray-300 bg-white focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                <!-- Populated dynamically -->
                            </select>
                            <p class="text-[9px] text-gray-500">Plumber / Dealer rate (Hidden)</p>
                        </div>

                        <!-- 10. Rate C (Bulk / Contractor Rate) -->
                        <div class="bg-slate-50 p-3 rounded-2xl border border-gray-200 space-y-1">
                            <label class="text-xs font-black text-gray-900 flex items-center justify-between">
                                <span>💰 Rate C (Bulk/Bag):</span>
                                <span class="text-[10px] text-gray-500">(Optional)</span>
                            </label>
                            <select id="mapColCost3" onchange="updateMappingPreview()" class="w-full text-xs font-bold py-1.5 px-2 rounded-xl border border-gray-300 bg-white focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                <!-- Populated dynamically -->
                            </select>
                            <p class="text-[9px] text-gray-500">Master carton / Bulk slab (Hidden)</p>
                        </div>

                        <!-- 11. Stock / Quantity -->
                        <div class="bg-slate-50 p-3 rounded-2xl border border-gray-200 space-y-1">
                            <label class="text-xs font-black text-gray-900 flex items-center justify-between">
                                <span>📊 Stock / Qty:</span>
                                <span class="text-[10px] text-gray-500">(Optional)</span>
                            </label>
                            <select id="mapColStock" onchange="updateMappingPreview()" class="w-full text-xs font-bold py-1.5 px-2 rounded-xl border border-gray-300 bg-white focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                <!-- Populated dynamically -->
                            </select>
                            <p class="text-[9px] text-gray-500">Opening stock (Default: 100)</p>
                        </div>

                        <!-- 12. Category -->
                        <div class="bg-slate-50 p-3 rounded-2xl border border-emerald-300 space-y-1">
                            <label class="text-xs font-black text-gray-900 flex items-center justify-between">
                                <span>📂 Category:</span>
                                <span class="text-[10px] text-emerald-700 font-bold">(Optional)</span>
                            </label>
                            <select id="mapColCategory" onchange="updateMappingPreview()" class="w-full text-xs font-bold py-1.5 px-2 rounded-xl border border-emerald-400 bg-white focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                                <!-- Populated dynamically -->
                            </select>
                            <p class="text-[9px] text-gray-500">Pipes, Paints, Electrical, Ply etc.</p>
                        </div>
                    </div>
                </div>

                <!-- Live 3-Row Preview Table -->
                <div class="space-y-2 pt-2 border-t border-gray-100">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-black text-gray-800 flex items-center gap-1.5">
                            <i class="fa-solid fa-eye text-indigo-600"></i>
                            <span>Live Preview (Sheet ke First 3 Items Kese Load Honge):</span>
                        </h4>
                        <span class="text-[10px] text-gray-500 font-medium">Verify kar lein ki MRP aur Selling Price sahi aa rahi hai</span>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white shadow-2xs">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 text-[10px] uppercase font-bold text-gray-500 border-b border-gray-200">
                                <tr>
                                    <th class="p-2.5">Category</th>
                                    <th class="p-2.5">Code</th>
                                    <th class="p-2.5">Product Name</th>
                                    <th class="p-2.5">Size</th>
                                    <th class="p-2.5">Packing (Box/Bag)</th>
                                    <th class="p-2.5 font-bold text-emerald-800">MRP (₹)</th>
                                    <th class="p-2.5">Cost 1 (₹)</th>
                                    <th class="p-2.5 font-bold text-emerald-700">Selling (₹)</th>
                                    <th class="p-2.5">Stock</th>
                                </tr>
                            </thead>
                            <tbody id="mappingPreviewTbody" class="divide-y divide-gray-100 font-medium">
                                <!-- Populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                <button type="button" onclick="closeColumnMapperModal()" class="px-4 py-2.5 rounded-xl bg-white hover:bg-gray-100 text-gray-700 font-bold text-xs border border-gray-200 transition">
                    Cancel
                </button>
                <button type="button" onclick="applyExcelColumnMapping()" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-600/30 flex items-center gap-2 transition active:scale-95">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Apply Mapping & Load All Products</span>
                </button>
            </div>

        </div>
    </div>

    <!-- ========================================== -->
    <!-- SLIDE-OVER PHOTO GALLERY DRAWER            -->
    <!-- ========================================== -->
    <div id="galleryDrawerOverlay" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-[9999] hidden flex justify-end" onclick="closeGalleryDrawer()">
        <div class="drawer-slide w-full max-w-lg bg-white h-full shadow-2xl flex flex-col" onclick="event.stopPropagation()">
            
            <!-- Drawer Header -->
            <div class="p-5 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-images text-indigo-600"></i>
                        <span>Select Photo from Media Bank</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-indigo-100 text-indigo-800 font-bold" id="drawerImagesCount">{{ count($galleryImages) }} Photos</span>
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
                    <input type="text" placeholder="Search gallery photo (e.g. Elbow, Tee)..." oninput="filterDrawerGallery(this.value)" class="w-full pl-8 pr-3 py-2 rounded-xl border border-gray-300 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-indigo-600 bg-white">
                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-gray-400 text-xs"></i>
                </div>
            </div>

            <!-- Drawer Gallery Grid -->
            <div class="flex-1 overflow-y-auto p-4">
                <div class="grid grid-cols-3 gap-3" id="drawerImagesGrid">
                    @forelse($galleryImages as $g)
                        <div class="drawer-img-card border border-gray-200 rounded-2xl p-2 bg-gray-50 hover:bg-indigo-50 hover:border-indigo-400 cursor-pointer text-center group transition" onclick="selectDrawerImage('{{ $g['url'] }}', '{{ $g['asset_url'] }}')" data-name="{{ strtolower($g['name']) }}">
                            <div class="h-20 w-full bg-white rounded-xl p-1 mb-1.5 flex items-center justify-center overflow-hidden border border-gray-100 group-hover:scale-105 transition">
                                <img src="{{ $g['asset_url'] }}" alt="{{ $g['name'] }}" class="max-h-full max-w-full object-contain">
                            </div>
                            <h5 class="text-[11px] font-bold text-gray-800 truncate" title="{{ $g['name'] }}">{{ $g['name'] }}</h5>
                            <span class="text-[9px] text-indigo-600 font-semibold">{{ $g['source'] === 'plasto_master' ? 'Master' : 'Crop' }}</span>
                        </div>
                    @empty
                        <div class="col-span-3 py-16 text-center space-y-2" id="drawerEmptyPrompt">
                            <div class="h-12 w-12 mx-auto rounded-2xl bg-indigo-50 text-indigo-400 flex items-center justify-center text-xl">
                                <i class="fa-solid fa-images"></i>
                            </div>
                            <h4 class="text-xs font-bold text-gray-700">Photo Bank Loading / Empty</h4>
                            <p class="text-[11px] text-gray-400 max-w-xs mx-auto">
                                PDF Studio se photos crop karein ya Page refresh karein.
                            </p>
                        </div>
                    @endforelse
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

            document.querySelectorAll('.cat-pill').forEach(btn => {
                const btnCat = btn.getAttribute('data-cat-key');
                if (!btnCat) return;
                const baseClass = "cat-pill px-4 py-2.5 rounded-2xl text-xs font-black flex items-center gap-2 transition whitespace-nowrap ";
                if (btnCat.toLowerCase() === cat.toLowerCase()) {
                    btn.className = baseClass + (cat === 'ALL' ? 'bg-gray-900 text-white shadow-sm' : 'bg-indigo-600 text-white shadow-md');
                } else {
                    btn.className = baseClass + 'bg-white hover:bg-slate-50 text-gray-700 border border-gray-200 shadow-xs';
                }
            });

            applyCombinedFilter();
        }

        function refreshCategoryFilterTabs() {
            const container = document.getElementById('categoryFilterContainer');
            if (!container) return;

            const counts = {};
            let total = 0;

            if (isDynamicMode) {
                dynamicRows.forEach(r => {
                    const c = (r.group_type || r.category || 'General Hardware').trim();
                    counts[c] = (counts[c] || 0) + 1;
                    total++;
                });
            } else {
                document.querySelectorAll('.table-row-item').forEach(tr => {
                    const c = (tr.getAttribute('data-category') || 'General Hardware').trim();
                    counts[c] = (counts[c] || 0) + 1;
                    total++;
                });
            }

            let html = `
                <button type="button" onclick="filterByCategory('ALL')" data-cat-key="ALL" id="catTab_ALL" class="cat-pill px-4 py-2.5 rounded-2xl text-xs font-black ${activeCategory === 'ALL' ? 'bg-gray-900 text-white shadow-sm' : 'bg-white hover:bg-slate-50 text-gray-700 border border-gray-200 shadow-xs'} flex items-center gap-2 transition whitespace-nowrap">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>All Categories</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] ${activeCategory === 'ALL' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-800'} font-mono" id="catCount_ALL">${total}</span>
                </button>
            `;

            for (const [catName, count] of Object.entries(counts)) {
                const isActive = (activeCategory.toLowerCase() === catName.toLowerCase());
                html += `
                    <button type="button" onclick="filterByCategory('${escapeHtml(catName)}')" data-cat-key="${escapeHtml(catName)}" class="cat-pill px-4 py-2.5 rounded-2xl text-xs font-extrabold ${isActive ? 'bg-indigo-600 text-white shadow-md' : 'bg-white hover:bg-slate-50 text-gray-700 border border-gray-200 shadow-xs'} flex items-center gap-2 transition whitespace-nowrap">
                        <span class="h-2 w-2 rounded-full ${isActive ? 'bg-white' : 'bg-indigo-500'}"></span>
                        <span>${escapeHtml(catName)}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] ${isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-800'} font-mono font-bold">${count}</span>
                    </button>
                `;
            }

            container.innerHTML = html;
        }

        // Live Search Filter
        function filterItemsLive(q) {
            searchQuery = q.toLowerCase().trim();
            applyCombinedFilter();
        }

        function applyCombinedFilter() {
            // Table view (both static .table-row-item and dynamic .dynamic-row-item)
            const rows = document.querySelectorAll(isDynamicMode ? '.dynamic-row-item' : '.table-row-item');
            rows.forEach(tr => {
                const text = (tr.getAttribute('data-search') || '').toLowerCase();
                const category = (tr.getAttribute('data-category') || '').toLowerCase();
                const matchSearch = (!searchQuery || text.includes(searchQuery));
                const matchCat = (activeCategory === 'ALL' || category === activeCategory.toLowerCase());
                tr.style.display = (matchSearch && matchCat) ? '' : 'none';
            });
            // Cards view
            document.querySelectorAll('.product-family-card, [id^="grouped_card_box_"]').forEach(card => {
                const text = (card.getAttribute('data-search') || '').toLowerCase();
                const category = (card.getAttribute('data-category') || '').toLowerCase();
                const matchSearch = (!searchQuery || text.includes(searchQuery));
                const matchCat = (activeCategory === 'ALL' || category === activeCategory.toLowerCase());
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
                if (cb.closest('tr')) cb.closest('tr').classList.remove('row-selected');
            });
            const selAll = document.getElementById('selectAllCheckbox');
            if (selAll) selAll.checked = false;
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
            const selCountEl = document.getElementById('floatingSelectedCount');
            const btnBatchEl = document.getElementById('btnBatchCount');
            const addToCardWrap = document.getElementById('addToExistingCardWrapper');
            const selectTargetCard = document.getElementById('selectTargetCard');
            
            if (selCountEl) selCountEl.innerText = count;
            if (btnBatchEl) btnBatchEl.innerText = count;

            if (addToCardWrap && selectTargetCard) {
                if (groupedProductCards.length > 0 && count > 0) {
                    addToCardWrap.classList.remove('hidden');
                    let opts = '';
                    groupedProductCards.forEach(c => {
                        opts += `<option value="${c.card_id}">${escapeHtml(c.parent_name)} (${c.variants.length} sizes)</option>`;
                    });
                    selectTargetCard.innerHTML = opts;
                } else {
                    addToCardWrap.classList.add('hidden');
                }
            }

            if (bar) {
                if (count > 0) {
                    bar.classList.remove('hidden');
                } else {
                    bar.classList.add('hidden');
                }
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
            } else if (type === 'grouped_card') {
                info.innerText = `Attaching photo to Grouped Product Card #${id}`;
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
            let visible = 0;
            document.querySelectorAll('.drawer-img-card').forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const match = (!query || name.includes(query));
                card.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            const countEl = document.getElementById('drawerImagesCount');
            if (countEl) countEl.innerText = `${visible} Photos`;
        }

        function filterOnPagePhotoBank(q) {
            const query = q.toLowerCase().trim();
            let visible = 0;
            document.querySelectorAll('.on-page-photo-card').forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const match = (!query || name.includes(query));
                card.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            const countEl = document.getElementById('onPagePhotoCount');
            if (countEl) countEl.innerText = `${visible} Photos`;
        }

        function handleOnPagePhotoClick(relUrl, assetUrl) {
            if (isDynamicMode) {
                const checked = Array.from(document.querySelectorAll('.dynamic-row-checkbox:checked')).map(cb => parseInt(cb.value));
                if (checked.length > 0) {
                    checked.forEach(rId => updateDynamicRowThumbnail(rId, relUrl, assetUrl));
                    clearDynamicRowSelection();
                    return;
                }
            } else {
                const checked = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => parseInt(cb.value));
                if (checked.length > 0) {
                    assignImageToBatch(relUrl, checked);
                    return;
                }
            }
            // If no rows currently checked, open drawer so user can inspect or select
            openGalleryDrawer('browse', null);
        }

        // User picks an image from drawer
        function selectDrawerImage(relUrl, assetUrl) {
            if (!currentTargetType || currentTargetType === 'browse') {
                if (isDynamicMode) {
                    const checked = Array.from(document.querySelectorAll('.dynamic-row-checkbox:checked')).map(cb => parseInt(cb.value));
                    if (checked.length > 0) {
                        checked.forEach(rId => updateDynamicRowThumbnail(rId, relUrl, assetUrl));
                        clearDynamicRowSelection();
                        closeGalleryDrawer();
                        return;
                    }
                } else {
                    const checked = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => parseInt(cb.value));
                    if (checked.length > 0) {
                        assignImageToBatch(relUrl, checked);
                        closeGalleryDrawer();
                        return;
                    }
                }
                closeGalleryDrawer();
                return;
            }

            // 0. Grouped Product Card Assign
            if (currentTargetType === 'grouped_card') {
                updateGroupedCardThumbnail(currentTargetId, relUrl, assetUrl);
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

        function updateGroupedCardThumbnail(cardId, relUrl, assetUrl) {
            const card = groupedProductCards.find(c => c.card_id === cardId);
            if (card) {
                card.image_url = relUrl;
                card.asset_url = assetUrl;
            }
            const box = document.getElementById('grouped_thumb_' + cardId);
            if (box) {
                box.innerHTML = `<img src="${assetUrl}" class="max-h-full max-w-full object-contain">`;
            }
        }

        // ==========================================
        // 📑 DYNAMIC SAAS SPREADSHEET TABLE LOGIC
        // ==========================================
        let isDynamicMode = true;
        let dynamicRows = [];
        let dynamicRowNextId = 1;
        let groupedProductCards = [];
        let groupedCardNextId = 1;

        function toggleDynamicMode(enable) {
            isDynamicMode = enable;
            const dynContainer = document.getElementById('dynamicSheetContainer');
            const savedContainer = document.getElementById('savedJobContainer');
            const tabDyn = document.getElementById('tabDynamicExcelMode');
            const tabSaved = document.getElementById('tabSavedJobMode');

            if (enable) {
                if (dynContainer) dynContainer.classList.remove('hidden');
                if (savedContainer) savedContainer.classList.add('hidden');
                if (tabDyn) {
                    tabDyn.className = "flex-1 py-2.5 rounded-xl text-xs font-black transition flex items-center justify-center gap-2 bg-emerald-600 text-white shadow-xs";
                }
                if (tabSaved) {
                    tabSaved.className = "flex-1 py-2.5 rounded-xl text-xs font-black transition flex items-center justify-center gap-2 bg-gray-100 text-gray-700 hover:bg-gray-200";
                }
            } else {
                if (dynContainer) dynContainer.classList.add('hidden');
                if (savedContainer) savedContainer.classList.remove('hidden');
                if (tabDyn) {
                    tabDyn.className = "flex-1 py-2.5 rounded-xl text-xs font-black transition flex items-center justify-center gap-2 bg-gray-100 text-gray-700 hover:bg-gray-200";
                }
                if (tabSaved) {
                    tabSaved.className = "flex-1 py-2.5 rounded-xl text-xs font-black transition flex items-center justify-center gap-2 bg-emerald-600 text-white shadow-xs";
                }
            }
            clearDynamicRowSelection();
            clearRowSelection();
        }

        function downloadSampleExcelTemplate() {
            if (typeof XLSX === 'undefined') {
                alert('Excel generator load ho raha hai, kripya 2 second baad dobara click karein.');
                return;
            }
            const sampleData = [
                ["Product Code", "Product / Item Name", "Size / Dimension", "Packing 1 (Box)", "Packing 2 (Carton)", "MRP (₹)", "Cost Price 1 (₹)", "Cost Price 2 (₹)", "Selling Price (₹)", "Stock", "Category"],
                ["UPVC-ELB-15", "UPVC Agri Elbow 90 Degree", "15 mm", "50 Pcs", "500 Pcs", 45.00, 22.00, 26.00, 35.00, 250, "UPVC Pipes & Fittings"],
                ["UPVC-ELB-20", "UPVC Agri Elbow 90 Degree", "20 mm", "40 Pcs", "400 Pcs", 55.00, 28.00, 32.00, 42.00, 200, "UPVC Pipes & Fittings"],
                ["UPVC-ELB-25", "UPVC Agri Elbow 90 Degree", "25 mm", "30 Pcs", "300 Pcs", 75.00, 38.00, 44.00, 58.00, 150, "UPVC Pipes & Fittings"],
                ["CPVC-BR-15", "CPVC Brass Elbow", "15 mm x 1/2\"", "25 Pcs", "250 Pcs", 120.00, 62.00, 72.00, 95.00, 100, "CPVC Pipes & Fittings"],
                ["SWR-TRAP-110", "SWR Nahani Trap", "110 mm x 75 mm", "12 Pcs", "72 Pcs", 180.00, 92.00, 105.00, 140.00, 60, "SWR Drainage"],
                ["SLV-HEAVY-250", "Heavy Duty UPVC Solvent Cement 250ml", "", "24 Cans", "144 Cans", 185.00, 98.00, 115.00, 155.00, 120, "Agri & Solvents"],
                ["TEF-TAPE-12", "PTFE Thread Seal Teflon Tape 12mm", "", "100 Pcs", "1000 Pcs", 25.00, 11.00, 13.00, 18.00, 500, "General Hardware"],
                ["BP-WAL-01", "Berger Walmasta Exterior Antifungal Emulsion", "1 Ltr", "4 Cans", "16 Cans", 320.00, 195.00, 215.00, 260.00, 40, "Paints & Coatings"],
                ["EL-SW-06A", "Anchor Roma 6A 1-Way Modular Switch", "1 Module", "20 Pcs", "200 Pcs", 38.00, 18.00, 21.00, 28.00, 300, "Electrical & Wiring"]
            ];
            const ws = XLSX.utils.aoa_to_sheet(sampleData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Catalog_Template");
            XLSX.writeFile(wb, "VyaparIndia_Product_Catalog_Template.xlsx");
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
                            renderGroupedProductCards();
                            return;
                        }
                    }
                } catch (e) {
                    console.error('Failed to parse vyapar_custom_excel_lines:', e);
                }
            }

            if (dynamicRows.length === 0) {
                loadSampleDynamicRows();
            }
            renderGroupedProductCards();
        }

        function handleExcelDroppedFile(file) {
            if (!file) return;
            handleExcelFileUpload({ target: { files: [file] } });
        }

        let uploadedExcelRawRows = [];
        let uploadedExcelFileName = '';

        function handleExcelFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const firstSheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[firstSheetName];
                    const jsonRows = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

                    if (!jsonRows || jsonRows.length === 0) {
                        alert('Excel file me koi data nahi mila!');
                        return;
                    }

                    uploadedExcelRawRows = jsonRows;
                    uploadedExcelFileName = file.name;

                    // Immediately open the interactive Column Mapper Modal!
                    openColumnMapperModal();
                } catch (err) {
                    console.error('Excel parse error:', err);
                    alert('Excel file read karne me error: ' + err.message);
                }
            };
            reader.readAsArrayBuffer(file);
            event.target.value = '';
        }

        function openColumnMapperModal() {
            if (!uploadedExcelRawRows || uploadedExcelRawRows.length === 0) {
                document.getElementById('excelFileInput').click();
                return;
            }

            const modal = document.getElementById('excelColumnMapModal');
            if (!modal) return;

            const totalRows = uploadedExcelRawRows.length;
            const countEl = document.getElementById('mapTotalRowsCount');
            if (countEl) countEl.innerText = `${totalRows} Rows Found`;

            // Detect header row by scanning first 8 rows
            let headerRowIdx = 0;
            let maxScore = -1;
            for (let r = 0; r < Math.min(8, totalRows); r++) {
                const row = uploadedExcelRawRows[r];
                if (!Array.isArray(row)) continue;
                let score = 0;
                row.forEach(c => {
                    const str = String(c || '').toLowerCase().trim();
                    if (str.includes('particular') || str.includes('item') || str.includes('product') || str.includes('name') || str.includes('desc') || str.includes('description')) score += 3;
                    if (str.includes('size') || str.includes('dim') || str.includes('dia') || str.includes('inch') || str.includes('mm')) score += 3;
                    if (str.includes('mrp') || str.includes('price') || str.includes('rate') || str.includes('list')) score += 3;
                    if (str.includes('cost') || str.includes('purchase') || str.includes('basic') || str.includes('net') || str.includes('dealer')) score += 2;
                    if (str.includes('code') || str.includes('sku') || str.includes('pack') || str.includes('box') || str.includes('carton') || str.includes('bag')) score += 2;
                });
                if (score > maxScore) {
                    maxScore = score;
                    headerRowIdx = r;
                }
            }

            const startRowInput = document.getElementById('mapDataStartRow');
            if (startRowInput) startRowInput.value = headerRowIdx + 2;

            const headerRow = uploadedExcelRawRows[headerRowIdx] || [];
            const sampleDataRow = uploadedExcelRawRows[headerRowIdx + 1] || [];

            // All 12 Column Selectors
            const colSelects = [
                'mapColName', 'mapColMrp', 'mapColRetail',
                'mapColSize', 'mapColCode', 'mapColPack1', 'mapColPack2',
                'mapColCost', 'mapColCost2', 'mapColCost3', 'mapColStock', 'mapColCategory'
            ];
            const colLetters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

            colSelects.forEach(selId => {
                const sel = document.getElementById(selId);
                if (!sel) return;
                // Only Name and MRP are strictly required
                const isRequired = (selId === 'mapColName' || selId === 'mapColMrp');
                let opts = isRequired ? '<option value="">-- Choose Column --</option>' : '<option value="-1">-- None (Optional) --</option>';

                const maxCols = Math.max(headerRow.length, sampleDataRow.length);
                for (let c = 0; c < maxCols; c++) {
                    const colLetter = c < 26 ? colLetters[c] : `Col ${c + 1}`;
                    const headName = String(headerRow[c] || '').trim();
                    const sampleVal = String(sampleDataRow[c] || '').trim();
                    const label = headName ? `${colLetter}: "${headName}" (Sample: "${sampleVal.slice(0, 18)}")` : `${colLetter}: (Sample: "${sampleVal.slice(0, 18)}")`;
                    opts += `<option value="${c}">${escapeHtml(label)}</option>`;
                }
                sel.innerHTML = opts;
            });

            // Smart auto-selection for B2B plumbing & hardware sheets
            let selectedCols = {
                code: -1,
                name: -1,
                size: -1,
                pack1: -1,
                pack2: -1,
                mrp: -1,
                cost: -1,
                cost2: -1,
                cost3: -1,
                retail: -1,
                stock: -1,
                category: -1
            };

            headerRow.forEach((col, idx) => {
                const c = String(col || '').toLowerCase().trim();
                const isSrNo = (c === 's.no' || c === 's.no.' || c === 'sr.no' || c === 'sr.no.' || c === 'sr no' || c === 'sl.no' || c === 'sl no' || c === 'no' || c === '#' || c === 'sn' || c === 's no');
                if (isSrNo) return; // Prevent serial numbers (1, 2) from becoming MRP, code or name!

                // Product Code / SKU
                if (selectedCols.code === -1 && (c.includes('item code') || c.includes('product code') || c.includes('item_code') || c.includes('sku') || c.includes('cat no') || c.includes('cat.no') || c.includes('art no') || c.includes('art.no') || c === 'code' || c === 'part no')) {
                    selectedCols.code = idx;
                }
                // Product Name
                else if (selectedCols.name === -1 && (c.includes('particular') || c.includes('item name') || c.includes('product name') || c.includes('description') || c.includes('desc') || c === 'item' || c === 'product' || c.includes('material'))) {
                    selectedCols.name = idx;
                }
                // Size / Dimension
                else if (selectedCols.size === -1 && (c.includes('size') || c.includes('dimension') || c.includes('dim') || c.includes('dia') || c.includes('inch') || c.includes('mm'))) {
                    selectedCols.size = idx;
                }
                // Packing 1 (Box / Inner)
                else if (selectedCols.pack1 === -1 && (c.includes('std pkg') || c.includes('std pack') || c.includes('box pack') || c.includes('box_pack') || c.includes('inner') || c.includes('pkg 1') || c.includes('pack 1') || c.includes('unit pack') || c === 'box' || c.includes('pkg1'))) {
                    selectedCols.pack1 = idx;
                }
                // Packing 2 (Carton / Master Bag)
                else if (selectedCols.pack2 === -1 && (c.includes('master bag') || c.includes('master pkg') || c.includes('carton pack') || c.includes('bag pack') || c.includes('outer') || c.includes('pkg 2') || c.includes('pack 2') || c === 'carton' || c === 'master' || c.includes('pkg2'))) {
                    selectedCols.pack2 = idx;
                }
                // MRP / List Price
                else if (selectedCols.mrp === -1 && (c.includes('mrp') || c.includes('list price') || c.includes('price list') || c.includes('m.r.p') || c === 'list rate')) {
                    selectedCols.mrp = idx;
                }
                // Cost Price 1 / Dealer Rate
                else if (selectedCols.cost === -1 && (c.includes('purchase') || c.includes('dealer') || c.includes('basic rate') || c.includes('cost 1') || c.includes('rate 1') || c === 'cost' || c === 'basic' || c === 'net rate')) {
                    selectedCols.cost = idx;
                }
                // Cost Price 2 / Wholesale Rate
                else if (selectedCols.cost2 === -1 && (c.includes('cost 2') || c.includes('rate 2') || c.includes('wholesale') || c.includes('distributor') || c.includes('tier 2'))) {
                    selectedCols.cost2 = idx;
                }
                // Cost Price 3 / Bulk Rate
                else if (selectedCols.cost3 === -1 && (c.includes('cost 3') || c.includes('rate 3') || c.includes('bulk') || c.includes('tier 3'))) {
                    selectedCols.cost3 = idx;
                }
                // Retail / Selling Price
                else if (selectedCols.retail === -1 && (c.includes('retail') || c.includes('sell') || c.includes('sale') || c.includes('selling price') || c.includes('market price'))) {
                    selectedCols.retail = idx;
                }
                // Stock / Qty
                else if (selectedCols.stock === -1 && (c.includes('stock') || c.includes('qty') || c.includes('quantity') || c.includes('opening stock') || c.includes('inventory'))) {
                    selectedCols.stock = idx;
                }
                // Category
                else if (selectedCols.category === -1 && (c.includes('cat') || c.includes('group') || c.includes('type') || c.includes('series'))) {
                    selectedCols.category = idx;
                }
            });

            // Fallback for MRP: any column with 'rate' or 'price' if not serial no
            if (selectedCols.mrp === -1) {
                headerRow.forEach((col, idx) => {
                    const c = String(col || '').toLowerCase().trim();
                    const isSrNo = (c === 's.no' || c === 's.no.' || c === 'sr.no' || c === 'sr no' || c === 'sl.no' || c === 'no' || c === '#' || c === 'sn');
                    if (!isSrNo && (c.includes('rate') || c.includes('price')) && selectedCols.cost !== idx) {
                        selectedCols.mrp = idx;
                    }
                });
            }

            // Defaults if still not found
            if (selectedCols.name !== -1) document.getElementById('mapColName').value = selectedCols.name;
            else if (headerRow.length > 1) document.getElementById('mapColName').value = 1;

            if (selectedCols.mrp !== -1) document.getElementById('mapColMrp').value = selectedCols.mrp;
            if (selectedCols.retail !== -1) document.getElementById('mapColRetail').value = selectedCols.retail;
            if (selectedCols.size !== -1) document.getElementById('mapColSize').value = selectedCols.size;
            if (selectedCols.code !== -1) document.getElementById('mapColCode').value = selectedCols.code;
            if (selectedCols.pack1 !== -1) document.getElementById('mapColPack1').value = selectedCols.pack1;
            if (selectedCols.pack2 !== -1) document.getElementById('mapColPack2').value = selectedCols.pack2;
            if (selectedCols.cost !== -1) document.getElementById('mapColCost').value = selectedCols.cost;
            if (selectedCols.cost2 !== -1) document.getElementById('mapColCost2').value = selectedCols.cost2;
            if (selectedCols.cost3 !== -1) document.getElementById('mapColCost3').value = selectedCols.cost3;
            if (selectedCols.stock !== -1) document.getElementById('mapColStock').value = selectedCols.stock;
            if (selectedCols.category !== -1) document.getElementById('mapColCategory').value = selectedCols.category;

            updateMappingPreview();
            modal.classList.remove('hidden');
        }

        function closeColumnMapperModal() {
            const modal = document.getElementById('excelColumnMapModal');
            if (modal) modal.classList.add('hidden');
        }

        function updateMappingPreview() {
            const tbody = document.getElementById('mappingPreviewTbody');
            if (!tbody || !uploadedExcelRawRows || uploadedExcelRawRows.length === 0) return;

            const nameIdx = parseInt(document.getElementById('mapColName')?.value ?? -1);
            const mrpIdx = parseInt(document.getElementById('mapColMrp')?.value ?? -1);
            const retailIdx = parseInt(document.getElementById('mapColRetail')?.value ?? -1);
            const sizeIdx = parseInt(document.getElementById('mapColSize')?.value ?? -1);
            const codeIdx = parseInt(document.getElementById('mapColCode')?.value ?? -1);
            const pack1Idx = parseInt(document.getElementById('mapColPack1')?.value ?? -1);
            const pack2Idx = parseInt(document.getElementById('mapColPack2')?.value ?? -1);
            const costIdx = parseInt(document.getElementById('mapColCost')?.value ?? -1);
            const stockIdx = parseInt(document.getElementById('mapColStock')?.value ?? -1);
            const catIdx = parseInt(document.getElementById('mapColCategory')?.value ?? -1);
            const startRow = Math.max(0, parseInt(document.getElementById('mapDataStartRow')?.value || 2) - 1);

            let previewHtml = '';
            let rowsShown = 0;

            for (let r = startRow; r < uploadedExcelRawRows.length && rowsShown < 3; r++) {
                const row = uploadedExcelRawRows[r];
                if (!row || row.length === 0 || row.every(c => c === null || c === undefined || String(c).trim() === '')) continue;

                const name = (nameIdx !== -1 && row[nameIdx] !== undefined) ? String(row[nameIdx]).trim() : 'Sample Product';
                const code = (codeIdx !== -1 && row[codeIdx] !== undefined) ? String(row[codeIdx]).trim() : '-';

                let size = (sizeIdx !== -1 && row[sizeIdx] !== undefined) ? String(row[sizeIdx]).trim() : '';
                if (!size) {
                    const sizeMatch = name.match(/\b(\d+(\.\d+)?\s*(mm|inch|")|\d+\/\d+(")?|\d+x\d+)\b/i);
                    size = sizeMatch ? sizeMatch[0] : 'Standard';
                }

                const pack1 = (pack1Idx !== -1 && row[pack1Idx] !== undefined) ? String(row[pack1Idx]).trim() : '';
                const pack2 = (pack2Idx !== -1 && row[pack2Idx] !== undefined) ? String(row[pack2Idx]).trim() : '';
                const packText = [pack1 ? `Box: ${pack1}` : '', pack2 ? `Bag: ${pack2}` : ''].filter(Boolean).join(' | ') || '-';

                const rawMrpStr = (mrpIdx !== -1 && row[mrpIdx] !== undefined) ? String(row[mrpIdx]).replace(/[^0-9.]/g, '') : '0';
                const parsedMrp = parseFloat(rawMrpStr) || 0;

                const rawRetailStr = (retailIdx !== -1 && row[retailIdx] !== undefined && String(row[retailIdx]).trim() !== '') ? String(row[retailIdx]).replace(/[^0-9.]/g, '') : '';
                const parsedRetail = parseFloat(rawRetailStr) || 0;

                const mrp = parsedMrp > 0 ? parsedMrp : (parsedRetail > 0 ? Math.round(parsedRetail / 0.88) : 100);
                const retail = parsedRetail > 0 ? parsedRetail : Math.round(mrp * 0.88);

                const rawCostStr = (costIdx !== -1 && row[costIdx] !== undefined && String(row[costIdx]).trim() !== '') ? String(row[costIdx]).replace(/[^0-9.]/g, '') : '';
                const cost = rawCostStr ? (parseFloat(rawCostStr) || Math.round(mrp * 0.65)) : Math.round(mrp * 0.65);

                const rawStockStr = (stockIdx !== -1 && row[stockIdx] !== undefined) ? String(row[stockIdx]).replace(/[^0-9]/g, '') : '100';
                const stock = parseInt(rawStockStr) || 100;

                let cat = 'Pipes & Fittings';
                if (catIdx !== -1 && row[catIdx] !== undefined && String(row[catIdx]).trim() !== '') {
                    cat = String(row[catIdx]).trim();
                } else {
                    const combined = (name + ' ' + size).toUpperCase();
                    if (combined.includes('PAINT') || combined.includes('EMULSION') || combined.includes('DISTEMPER') || combined.includes('PRIMER') || combined.includes('ENAMEL')) cat = 'Paints & Coatings';
                    else if (combined.includes('SWITCH') || combined.includes('SOCKET') || combined.includes('WIRE') || combined.includes('CABLE') || combined.includes('MCB')) cat = 'Electrical & Wiring';
                    else if (combined.includes('PLY') || combined.includes('DOOR') || combined.includes('BEAT') || combined.includes('LAMINATE')) cat = 'Plywood & Hardware';
                    else if (combined.includes('PUMP') || combined.includes('MOTOR') || combined.includes('SUBMERSIBLE')) cat = 'Pumps & Motors';
                    else if (combined.includes('CPVC')) cat = 'CPVC';
                    else if (combined.includes('SWR') || combined.includes('TRAP') || combined.includes('DRAIN')) cat = 'SWR';
                    else if (combined.includes('UPVC')) cat = 'UPVC';
                    else if (combined.includes('AGRI') || combined.includes('SOLVENT') || combined.includes('CEMENT')) cat = 'Agri & Solvents';
                    else cat = 'General Hardware';
                }

                previewHtml += `
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-2.5">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-indigo-100 text-indigo-800 border border-indigo-200">${escapeHtml(cat)}</span>
                        </td>
                        <td class="p-2.5 font-mono text-[11px] text-gray-600">${escapeHtml(code)}</td>
                        <td class="p-2.5 font-bold text-gray-900">${escapeHtml(name)}</td>
                        <td class="p-2.5 font-mono text-gray-700">${escapeHtml(size)}</td>
                        <td class="p-2.5 text-[11px] text-indigo-700 font-mono">${escapeHtml(packText)}</td>
                        <td class="p-2.5 font-mono font-black text-emerald-800 text-sm">₹${mrp.toFixed(2)}</td>
                        <td class="p-2.5 font-mono text-gray-600">₹${cost.toFixed(2)}</td>
                        <td class="p-2.5 font-mono font-bold text-emerald-700">₹${retail.toFixed(2)}</td>
                        <td class="p-2.5 font-mono text-gray-700">${stock}</td>
                    </tr>
                `;
                rowsShown++;
            }

            if (!previewHtml) {
                previewHtml = `<tr><td colspan="9" class="p-4 text-center text-gray-400">Is row number par koi data nahi mila. "Data Starts At Row" number check karein.</td></tr>`;
            }

            tbody.innerHTML = previewHtml;
        }

        function applyExcelColumnMapping() {
            const nameIdx = parseInt(document.getElementById('mapColName')?.value ?? -1);
            const mrpIdx = parseInt(document.getElementById('mapColMrp')?.value ?? -1);
            const retailIdx = parseInt(document.getElementById('mapColRetail')?.value ?? -1);
            const sizeIdx = parseInt(document.getElementById('mapColSize')?.value ?? -1);
            const codeIdx = parseInt(document.getElementById('mapColCode')?.value ?? -1);
            const pack1Idx = parseInt(document.getElementById('mapColPack1')?.value ?? -1);
            const pack2Idx = parseInt(document.getElementById('mapColPack2')?.value ?? -1);
            const costIdx = parseInt(document.getElementById('mapColCost')?.value ?? -1);
            const cost2Idx = parseInt(document.getElementById('mapColCost2')?.value ?? -1);
            const cost3Idx = parseInt(document.getElementById('mapColCost3')?.value ?? -1);
            const stockIdx = parseInt(document.getElementById('mapColStock')?.value ?? -1);
            const catIdx = parseInt(document.getElementById('mapColCategory')?.value ?? -1);
            const startRow = Math.max(0, parseInt(document.getElementById('mapDataStartRow')?.value || 2) - 1);

            if (isNaN(nameIdx) || nameIdx === -1) {
                alert('Kripya "Product / Item Name" column select karein.');
                return;
            }
            if ((isNaN(mrpIdx) || mrpIdx === -1) && (isNaN(retailIdx) || retailIdx === -1)) {
                alert('Kripya "MRP / List Price" ya "Selling Price" column select karein.');
                return;
            }

            const newRows = [];
            for (let i = startRow; i < uploadedExcelRawRows.length; i++) {
                const row = uploadedExcelRawRows[i];
                if (!row || row.length === 0 || row.every(c => c === null || c === undefined || String(c).trim() === '')) continue;

                const rawName = String(row[nameIdx] || '').trim();
                if (!rawName) continue;

                const rawCode = (codeIdx !== -1 && row[codeIdx] !== undefined) ? String(row[codeIdx]).trim() : '';

                let rawSize = (sizeIdx !== -1 && row[sizeIdx] !== undefined) ? String(row[sizeIdx]).trim() : '';
                if (!rawSize) {
                    const sizeMatch = rawName.match(/\b(\d+(\.\d+)?\s*(mm|inch|")|\d+\/\d+(")?|\d+x\d+)\b/i);
                    rawSize = sizeMatch ? sizeMatch[0] : 'Standard';
                }

                const rawPack1 = (pack1Idx !== -1 && row[pack1Idx] !== undefined) ? String(row[pack1Idx]).trim() : '';
                const rawPack2 = (pack2Idx !== -1 && row[pack2Idx] !== undefined) ? String(row[pack2Idx]).trim() : '';

                const parsedMrp = (mrpIdx !== -1 && row[mrpIdx] !== undefined) ? (parseFloat(String(row[mrpIdx]).replace(/[^0-9.]/g, '')) || 0) : 0;
                const parsedRetail = (retailIdx !== -1 && row[retailIdx] !== undefined && String(row[retailIdx]).trim() !== '')
                    ? (parseFloat(String(row[retailIdx]).replace(/[^0-9.]/g, '')) || 0)
                    : 0;

                const rawMrp = parsedMrp > 0 ? parsedMrp : (parsedRetail > 0 ? Math.round(parsedRetail / 0.88) : 100);
                const rawRetail = parsedRetail > 0 ? parsedRetail : Math.round(rawMrp * 0.88);

                const rawCost = (costIdx !== -1 && row[costIdx] !== undefined && String(row[costIdx]).trim() !== '') 
                    ? (parseFloat(String(row[costIdx]).replace(/[^0-9.]/g, '')) || Math.round(rawMrp * 0.65))
                    : Math.round(rawMrp * 0.65);

                const rawCost2 = (cost2Idx !== -1 && row[cost2Idx] !== undefined && String(row[cost2Idx]).trim() !== '')
                    ? (parseFloat(String(row[cost2Idx]).replace(/[^0-9.]/g, '')) || 0)
                    : 0;

                const rawCost3 = (cost3Idx !== -1 && row[cost3Idx] !== undefined && String(row[cost3Idx]).trim() !== '')
                    ? (parseFloat(String(row[cost3Idx]).replace(/[^0-9.]/g, '')) || 0)
                    : 0;

                const rawStock = (stockIdx !== -1 && row[stockIdx] !== undefined && String(row[stockIdx]).trim() !== '')
                    ? (parseInt(String(row[stockIdx]).replace(/[^0-9]/g, '')) || 100)
                    : 100;

                let cat = 'Pipes & Fittings';
                if (catIdx !== -1 && row[catIdx] !== undefined && String(row[catIdx]).trim() !== '') {
                    cat = String(row[catIdx]).trim();
                } else {
                    const combined = (rawName + ' ' + rawSize).toUpperCase();
                    if (combined.includes('PAINT') || combined.includes('EMULSION') || combined.includes('DISTEMPER') || combined.includes('PRIMER') || combined.includes('ENAMEL')) cat = 'Paints & Coatings';
                    else if (combined.includes('SWITCH') || combined.includes('SOCKET') || combined.includes('WIRE') || combined.includes('CABLE') || combined.includes('MCB')) cat = 'Electrical & Wiring';
                    else if (combined.includes('PLY') || combined.includes('DOOR') || combined.includes('BEAT') || combined.includes('LAMINATE')) cat = 'Plywood & Hardware';
                    else if (combined.includes('PUMP') || combined.includes('MOTOR') || combined.includes('SUBMERSIBLE')) cat = 'Pumps & Motors';
                    else if (combined.includes('CPVC')) cat = 'CPVC';
                    else if (combined.includes('SWR') || combined.includes('TRAP') || combined.includes('DRAIN')) cat = 'SWR';
                    else if (combined.includes('UPVC')) cat = 'UPVC';
                    else if (combined.includes('AGRI') || combined.includes('SOLVENT') || combined.includes('CEMENT')) cat = 'Agri & Solvents';
                    else cat = 'General Hardware';
                }

                newRows.push({
                    id: dynamicRowNextId++,
                    product_code: rawCode,
                    product_name: rawName,
                    size: rawSize,
                    packing_1: rawPack1,
                    packing_2: rawPack2,
                    group_type: cat,
                    category: cat,
                    mrp: rawMrp,
                    purchase_cost: rawCost,
                    cost_price_2: rawCost2,
                    cost_price_3: rawCost3,
                    retail_price: rawRetail,
                    stock: rawStock,
                    image_url: '',
                    asset_url: ''
                });
            }

            if (newRows.length === 0) {
                alert('Chune gaye mapping ke anusaar koi valid rows nahi mili.');
                return;
            }

            dynamicRows = newRows;
            renderDynamicRows();
            refreshCategoryFilterTabs();
            toggleDynamicMode(true);
            closeColumnMapperModal();

            if (uploadedExcelFileName) {
                const sheetNameInput = document.getElementById('dynamicSheetName');
                if (sheetNameInput) {
                    sheetNameInput.value = uploadedExcelFileName.replace(/\.[^/.]+$/, "");
                }
            }

            alert(`🎉 Success! Excel sheet se ${newRows.length} products sahi MRP, Selling Price, Code, Packing aur Category ke sath map ho gaye hain!`);
        }

        function groupSelectedRowsIntoCard() {
            let checkedIds = [];
            if (isDynamicMode) {
                checkedIds = Array.from(document.querySelectorAll('.dynamic-row-checkbox:checked')).map(cb => parseInt(cb.value));
            } else {
                checkedIds = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => parseInt(cb.value));
            }

            if (checkedIds.length === 0) {
                alert('Kripya 1 ya usse zyada rows (jaise 6-8 sizes) select karein jinhe 1 Product Card me combine karna hai.');
                return;
            }

            if (!isDynamicMode) {
                toggleDynamicMode(true);
            }

            const selectedRows = dynamicRows.filter(r => checkedIds.includes(r.id));
            if (selectedRows.length === 0) {
                alert('Selected rows dynamic table me nahi mili.');
                return;
            }

            let firstRow = selectedRows[0];
            let parentTitle = firstRow.product_name;
            if (firstRow.size && parentTitle.includes(firstRow.size)) {
                parentTitle = parentTitle.replace(firstRow.size, '').trim();
            }
            parentTitle = parentTitle.replace(/[-–,\s]+$/, '').trim() || firstRow.product_name;

            const assignedImg = selectedRows.find(r => r.image_url)?.image_url || preselectedImg || '';
            const assignedAsset = selectedRows.find(r => r.asset_url)?.asset_url || (preselectedImg ? ('/' + preselectedImg.replace(/^\//, '')) : '');

            const newCard = {
                card_id: groupedCardNextId++,
                parent_name: parentTitle,
                category: firstRow.category || firstRow.group_type || 'General Hardware',
                image_url: assignedImg,
                asset_url: assignedAsset,
                variants: selectedRows.map(r => ({
                    id: r.id,
                    product_code: r.product_code || '',
                    size: r.size || 'Standard',
                    packing_1: r.packing_1 || '',
                    packing_2: r.packing_2 || '',
                    mrp: r.mrp,
                    purchase_cost: r.purchase_cost,
                    cost_price_2: r.cost_price_2 || 0,
                    cost_price_3: r.cost_price_3 || 0,
                    retail_price: r.retail_price,
                    stock: r.stock || 100
                }))
            };

            groupedProductCards.push(newCard);

            dynamicRows = dynamicRows.filter(r => !checkedIds.includes(r.id));

            clearDynamicRowSelection();
            renderDynamicRows();
            renderGroupedProductCards();
            refreshCategoryFilterTabs();
            updateFloatingBatchBar();

            const gSection = document.getElementById('groupedCardsSection');
            if (gSection) {
                gSection.scrollIntoView({ behavior: 'smooth' });
            }
        }

        function renderGroupedProductCards() {
            const grid = document.getElementById('groupedCardsGrid');
            const badge = document.getElementById('groupedCardCountBadge');
            if (badge) badge.innerText = `${groupedProductCards.length} Cards`;
            if (!grid) return;

            if (groupedProductCards.length === 0) {
                grid.innerHTML = `
                    <div class="p-6 text-center bg-gray-50/50 rounded-2xl border border-dashed border-gray-200 text-gray-400 text-xs">
                        <i class="fa-solid fa-layer-group text-lg mb-1"></i>
                        <p>Abhi tak koi grouped card nahi banaya gaya. Upar table me se 6-8 sizes select karke <b>"Group (6-8) Rows into 1 Card"</b> dabayein.</p>
                    </div>
                `;
                return;
            }

            let html = '';
            groupedProductCards.forEach((c) => {
                const imgThumb = (c.asset_url || c.image_url)
                    ? `<img src="${c.asset_url || c.image_url}" class="max-h-full max-w-full object-contain">`
                    : `<div class="text-center text-gray-400 group-hover:text-indigo-600"><i class="fa-solid fa-camera text-base"></i><p class="text-[9px] font-bold mt-0.5">Attach Photo</p></div>`;

                let variantRowsHtml = '';
                c.variants.forEach((v) => {
                    const packStr = [v.packing_1 ? `Box: ${v.packing_1}` : '', v.packing_2 ? `Bag: ${v.packing_2}` : ''].filter(Boolean).join(' | ');
                    variantRowsHtml += `
                        <tr class="hover:bg-indigo-50/30 transition">
                            <td class="p-2">
                                <input type="text" value="${escapeHtml(v.size || 'Standard')}" oninput="updateGroupedVariantField(${c.card_id}, ${v.id}, 'size', this.value)" class="w-full text-xs font-mono font-bold text-gray-900 border border-transparent hover:border-gray-300 focus:border-indigo-600 rounded p-1 bg-transparent">
                            </td>
                            <td class="p-2 font-mono">
                                <input type="text" value="${escapeHtml(v.product_code || '')}" placeholder="SKU" oninput="updateGroupedVariantField(${c.card_id}, ${v.id}, 'product_code', this.value)" class="w-20 text-[11px] font-mono text-gray-600 border border-transparent hover:border-gray-300 focus:border-indigo-600 rounded p-1 bg-transparent">
                            </td>
                            <td class="p-2 font-mono text-[10px] text-indigo-700">
                                <input type="text" value="${escapeHtml(packStr)}" placeholder="Box/Bag" oninput="updateGroupedVariantField(${c.card_id}, ${v.id}, 'packing_1', this.value)" class="w-24 text-[10px] font-mono text-indigo-700 border border-transparent hover:border-gray-300 focus:border-indigo-600 rounded p-1 bg-transparent">
                            </td>
                            <td class="p-2 font-mono">
                                <div class="flex items-center"><span class="text-gray-400 mr-0.5">₹</span><input type="number" step="0.5" value="${v.mrp}" oninput="updateGroupedVariantField(${c.card_id}, ${v.id}, 'mrp', parseFloat(this.value) || 0)" class="w-16 text-xs font-bold text-gray-800 border border-transparent hover:border-gray-300 focus:border-indigo-600 rounded p-1 bg-transparent"></div>
                            </td>
                            <td class="p-2 font-mono">
                                <div class="flex items-center"><span class="text-gray-400 mr-0.5">₹</span><input type="number" step="0.5" value="${v.purchase_cost}" oninput="updateGroupedVariantField(${c.card_id}, ${v.id}, 'purchase_cost', parseFloat(this.value) || 0)" class="w-16 text-xs text-gray-600 border border-transparent hover:border-gray-300 focus:border-indigo-600 rounded p-1 bg-transparent"></div>
                            </td>
                            <td class="p-2 font-mono">
                                <div class="flex items-center"><span class="text-gray-400 mr-0.5">₹</span><input type="number" step="0.5" value="${v.retail_price}" oninput="updateGroupedVariantField(${c.card_id}, ${v.id}, 'retail_price', parseFloat(this.value) || 0)" class="w-16 text-xs font-bold text-emerald-700 border border-transparent hover:border-gray-300 focus:border-indigo-600 rounded p-1 bg-transparent"></div>
                            </td>
                            <td class="p-2 font-mono">
                                <input type="number" value="${v.stock || 100}" oninput="updateGroupedVariantField(${c.card_id}, ${v.id}, 'stock', parseInt(this.value) || 0)" class="w-14 text-xs font-mono text-gray-700 border border-transparent hover:border-gray-300 focus:border-indigo-600 rounded p-1 bg-transparent">
                            </td>
                            <td class="p-2 text-right">
                                <button type="button" onclick="deleteVariantFromCard(${c.card_id}, ${v.id})" class="h-6 w-6 rounded bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition inline-flex items-center justify-center text-[10px]" title="Remove size"><i class="fa-solid fa-xmark"></i></button>
                            </td>
                        </tr>
                    `;
                });

                const cardCategory = c.category || 'General Hardware';
                const cardSearch = `${c.parent_name} ${cardCategory} ${c.variants.map(v => (v.product_code || '') + ' ' + (v.size || '')).join(' ')}`.toLowerCase();

                html += `
                    <div class="bg-white rounded-3xl border-2 border-indigo-100 p-5 shadow-sm hover:shadow-md transition space-y-4" id="grouped_card_box_${c.card_id}" data-category="${escapeHtml(cardCategory)}" data-search="${escapeHtml(cardSearch)}">
                        <div class="flex flex-wrap items-center justify-between gap-4 pb-3 border-b border-gray-100">
                            <div class="flex items-center gap-4 flex-1 min-w-[280px]">
                                <!-- Product Photo Slot -->
                                <div class="relative group h-20 w-20 bg-gray-50 rounded-2xl border-2 border-dashed border-indigo-200 hover:border-indigo-500 p-1 flex items-center justify-center overflow-hidden cursor-pointer transition shrink-0" onclick="openGalleryDrawer('grouped_card', ${c.card_id})" title="Click to choose image from Photo Bank" id="grouped_thumb_${c.card_id}">
                                    ${imgThumb}
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-[10px] font-bold transition rounded-xl">
                                        Change
                                    </div>
                                </div>

                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <input list="allCategoriesList" value="${escapeHtml(cardCategory)}" onchange="updateGroupedCardField(${c.card_id}, 'category', this.value); refreshCategoryFilterTabs();" class="text-xs font-bold py-1 px-2.5 rounded-xl border border-indigo-200 bg-white text-indigo-900 focus:ring-2 focus:ring-indigo-500 w-44" placeholder="Category">
                                        <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 font-extrabold text-[10px]">${c.variants.length} Sizes Included</span>
                                    </div>
                                    <input type="text" value="${escapeHtml(c.parent_name)}" oninput="updateGroupedCardField(${c.card_id}, 'parent_name', this.value)" class="w-full text-base font-black text-gray-900 border border-transparent hover:border-gray-300 focus:border-indigo-600 focus:bg-white rounded-lg px-2 py-1 transition bg-transparent" placeholder="e.g. UPVC 90° Elbow Heavy Duty">
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" onclick="openGalleryDrawer('grouped_card', ${c.card_id})" class="px-3.5 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-extrabold border border-indigo-200 flex items-center gap-1.5 transition">
                                    <i class="fa-solid fa-images"></i>
                                    <span>Pick Photo</span>
                                </button>
                                <button type="button" onclick="ungroupCard(${c.card_id})" class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-extrabold border border-amber-200 flex items-center gap-1.5 transition" title="Restore back to flat table rows">
                                    <i class="fa-solid fa-arrow-rotate-left"></i>
                                    <span>Ungroup</span>
                                </button>
                                <button type="button" onclick="deleteGroupedCard(${c.card_id})" class="h-8 w-8 rounded-xl bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 flex items-center justify-center text-xs transition" title="Delete Card">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Sizes Matrix Sub-Table -->
                        <div class="overflow-x-auto rounded-2xl border border-gray-100 bg-slate-50/50">
                            <table class="w-full text-left text-xs">
                                <thead class="text-[10px] text-gray-400 uppercase font-bold border-b border-gray-200">
                                    <tr>
                                        <th class="p-2.5">Size / Dimension</th>
                                        <th class="p-2.5">SKU / Code</th>
                                        <th class="p-2.5">Packing</th>
                                        <th class="p-2.5">MRP</th>
                                        <th class="p-2.5">Cost Price</th>
                                        <th class="p-2.5">Selling Price</th>
                                        <th class="p-2.5">Stock</th>
                                        <th class="p-2.5 text-right w-12">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 font-medium">
                                    ${variantRowsHtml}
                                </tbody>
                            </table>
                            <div class="p-2.5 border-t border-gray-100 flex items-center justify-between bg-white/70">
                                <button type="button" onclick="addVariantToCard(${c.card_id})" class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center gap-1.5 transition">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                    <span>+ Add Missed Size Variant</span>
                                </button>
                                <span class="text-[11px] text-gray-400 font-medium">Agar koi size chhut gaya ho to yahan click karke direct add karein</span>
                            </div>
                        </div>
                    </div>
                `;
            });

            grid.innerHTML = html;
        }

        function updateGroupedCardField(cardId, field, val) {
            const card = groupedProductCards.find(c => c.card_id === cardId);
            if (card) card[field] = val;
        }

        function updateGroupedVariantField(cardId, variantId, field, val) {
            const card = groupedProductCards.find(c => c.card_id === cardId);
            if (card) {
                const v = card.variants.find(item => item.id === variantId);
                if (v) v[field] = val;
            }
        }

        function deleteVariantFromCard(cardId, variantId) {
            const card = groupedProductCards.find(c => c.card_id === cardId);
            if (card) {
                card.variants = card.variants.filter(v => v.id !== variantId);
                if (card.variants.length === 0) {
                    deleteGroupedCard(cardId);
                    return;
                }
                renderGroupedProductCards();
            }
        }

        function ungroupCard(cardId) {
            const card = groupedProductCards.find(c => c.card_id === cardId);
            if (!card) return;

            card.variants.forEach(v => {
                dynamicRows.push({
                    id: dynamicRowNextId++,
                    product_code: v.product_code || '',
                    product_name: `${card.parent_name} ${v.size === 'Standard' ? '' : v.size}`.trim() || card.parent_name,
                    size: v.size || 'Standard',
                    packing_1: v.packing_1 || '',
                    packing_2: v.packing_2 || '',
                    group_type: card.category,
                    mrp: v.mrp,
                    purchase_cost: v.purchase_cost,
                    cost_price_2: v.cost_price_2 || 0,
                    cost_price_3: v.cost_price_3 || 0,
                    retail_price: v.retail_price,
                    stock: v.stock || 100,
                    image_url: card.image_url || '',
                    asset_url: card.asset_url || ''
                });
            });

            groupedProductCards = groupedProductCards.filter(c => c.card_id !== cardId);
            renderDynamicRows();
            renderGroupedProductCards();
            refreshCategoryFilterTabs();
            updateFloatingBatchBar();
        }

        function addVariantToCard(cardId) {
            const card = groupedProductCards.find(c => c.card_id === cardId);
            if (!card) return;
            const lastVar = card.variants[card.variants.length - 1];
            const newVariant = {
                id: dynamicRowNextId++,
                product_code: '',
                size: '',
                packing_1: lastVar ? lastVar.packing_1 : '',
                packing_2: lastVar ? lastVar.packing_2 : '',
                mrp: lastVar ? lastVar.mrp : 100,
                purchase_cost: lastVar ? lastVar.purchase_cost : 60,
                cost_price_2: lastVar ? (lastVar.cost_price_2 || 0) : 0,
                cost_price_3: lastVar ? (lastVar.cost_price_3 || 0) : 0,
                retail_price: lastVar ? lastVar.retail_price : 85,
                stock: 100
            };
            card.variants.push(newVariant);
            renderGroupedProductCards();
        }

        function addSelectedRowsToTargetCard() {
            const selectEl = document.getElementById('selectTargetCard');
            const targetCardId = parseInt(selectEl?.value);
            if (isNaN(targetCardId)) {
                alert('Pehle koi Product Card select karein.');
                return;
            }

            const card = groupedProductCards.find(c => c.card_id === targetCardId);
            if (!card) {
                alert('Chuna gaya Product Card nahi mila.');
                return;
            }

            let checkedIds = [];
            if (isDynamicMode) {
                checkedIds = Array.from(document.querySelectorAll('.dynamic-row-checkbox:checked')).map(cb => parseInt(cb.value));
            } else {
                checkedIds = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => parseInt(cb.value));
            }

            if (checkedIds.length === 0) {
                alert('Table me se kam se kam 1 row select karein.');
                return;
            }

            const rowsToAdd = dynamicRows.filter(r => checkedIds.includes(r.id));
            rowsToAdd.forEach(r => {
                card.variants.push({
                    id: r.id,
                    product_code: r.product_code || '',
                    size: r.size || 'Standard',
                    packing_1: r.packing_1 || '',
                    packing_2: r.packing_2 || '',
                    mrp: r.mrp,
                    purchase_cost: r.purchase_cost,
                    cost_price_2: r.cost_price_2 || 0,
                    cost_price_3: r.cost_price_3 || 0,
                    retail_price: r.retail_price,
                    stock: r.stock || 100
                });
            });

            // Remove transferred rows from flat table
            dynamicRows = dynamicRows.filter(r => !checkedIds.includes(r.id));

            clearDynamicRowSelection();
            renderDynamicRows();
            renderGroupedProductCards();
            refreshCategoryFilterTabs();
            updateFloatingBatchBar();

            alert(`🎉 Success! ${rowsToAdd.length} row(s) ko "${card.parent_name}" card me jod diya gaya hai.`);
        }

        function deleteGroupedCard(cardId) {
            if (!confirm('Kya aap is Grouped Product Card ko delete karna chahte hain?')) return;
            groupedProductCards = groupedProductCards.filter(c => c.card_id !== cardId);
            renderGroupedProductCards();
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
                const size = sizeMatch ? sizeMatch[0] : 'Standard';

                const priceMatch = text.match(/(?:rs\.?|₹|\/)\s*(\d+(?:\.\d+)?)/i);
                const price = priceMatch ? parseFloat(priceMatch[1]) : (50 + (idx * 15));
                const cost = Math.round(price * 0.65);
                const retail = Math.round(price * 0.88);

                dynamicRows.push({
                    id: dynamicRowNextId++,
                    product_code: `ITM-${100 + idx}`,
                    product_name: text,
                    size: size,
                    packing_1: '',
                    packing_2: '',
                    group_type: category,
                    mrp: price,
                    purchase_cost: cost,
                    cost_price_2: 0,
                    cost_price_3: 0,
                    retail_price: retail,
                    stock: 100,
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
                product_code: '',
                product_name: 'New Product Item',
                size: 'Standard',
                packing_1: '',
                packing_2: '',
                group_type: 'UPVC',
                mrp: 100,
                purchase_cost: 65,
                cost_price_2: 0,
                cost_price_3: 0,
                retail_price: 85,
                stock: 100,
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
                        <td colspan="12" class="p-8 text-center text-gray-400 text-xs">
                            Koi flat rows nahi hain. Nayi row jodne ke liye <b>"+ Add Line"</b> ya Excel Import karein.
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

                const catVal = r.group_type || r.category || 'General Hardware';
                const searchStr = `${r.product_name} ${r.product_code || ''} ${r.size || ''} ${catVal}`.toLowerCase();

                html += `
                    <tr class="hover:bg-slate-50 transition dynamic-row-item" id="dyn_row_${r.id}" data-category="${escapeHtml(catVal)}" data-search="${escapeHtml(searchStr)}">
                        <td class="p-3 text-center">
                            <input type="checkbox" value="${r.id}" onchange="handleDynamicCheckboxChange(this)" class="dynamic-row-checkbox h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        </td>
                        <td class="p-3">
                            <div class="h-10 w-10 bg-gray-50 rounded-xl border border-gray-200 p-0.5 flex items-center justify-center overflow-hidden cursor-pointer hover:border-emerald-500 transition" onclick="openGalleryDrawer('dynamic_row', ${r.id})" title="Click to attach photo">
                                ${imgThumb}
                            </div>
                        </td>
                        <td class="p-3">
                            <input list="allCategoriesList" value="${escapeHtml(catVal)}" onchange="updateDynamicRowField(${r.id}, 'group_type', this.value); updateDynamicRowField(${r.id}, 'category', this.value); refreshCategoryFilterTabs();" class="text-xs font-bold py-1 px-2 rounded-lg border border-gray-200 bg-white text-gray-800 focus:ring-1 focus:ring-emerald-500 w-28" placeholder="Category" title="Select or type Category">
                        </td>
                        <td class="p-3 min-w-[200px]">
                            <input type="text" value="${escapeHtml(r.product_name)}" oninput="updateDynamicRowField(${r.id}, 'product_name', this.value)" class="w-full text-xs font-bold text-gray-900 border border-transparent hover:border-gray-300 focus:border-emerald-600 focus:bg-white rounded-lg p-1 transition bg-transparent" placeholder="Product Name">
                        </td>
                        <td class="p-3">
                            <input type="text" value="${escapeHtml(r.product_code || '')}" placeholder="Item Code" oninput="updateDynamicRowField(${r.id}, 'product_code', this.value)" class="w-24 text-[11px] font-mono text-gray-700 border border-transparent hover:border-gray-300 focus:border-emerald-600 focus:bg-white rounded-lg p-1 transition bg-transparent" title="Product / Item Code (SKU)">
                        </td>
                        <td class="p-3">
                            <input type="text" value="${escapeHtml(r.size || 'Standard')}" placeholder="Standard" oninput="updateDynamicRowField(${r.id}, 'size', this.value)" class="w-20 text-xs font-mono font-bold text-gray-800 border border-transparent hover:border-gray-300 focus:border-emerald-600 focus:bg-white rounded-lg p-1 transition bg-transparent" title="Size / Dimension">
                        </td>
                        <td class="p-3">
                            <div class="space-y-0.5 min-w-[90px]">
                                <input type="text" value="${escapeHtml(r.packing_1 || '')}" placeholder="Box: -" oninput="updateDynamicRowField(${r.id}, 'packing_1', this.value)" class="w-full text-[10px] font-mono text-indigo-700 border border-transparent hover:border-gray-300 focus:border-indigo-500 rounded px-1 py-0.5 bg-transparent" title="Box / Inner Pack">
                                <input type="text" value="${escapeHtml(r.packing_2 || '')}" placeholder="Bag: -" oninput="updateDynamicRowField(${r.id}, 'packing_2', this.value)" class="w-full text-[10px] font-mono text-purple-700 border border-transparent hover:border-gray-300 focus:border-purple-500 rounded px-1 py-0.5 bg-transparent" title="Master Carton / Bag Pack">
                            </div>
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
                                <input type="number" step="0.5" value="${r.purchase_cost}" oninput="updateDynamicRowField(${r.id}, 'purchase_cost', parseFloat(this.value) || 0)" class="w-16 text-xs text-gray-600 border border-transparent hover:border-gray-300 focus:border-emerald-600 rounded-lg p-1 bg-transparent" title="Cost Price 1">
                            </div>
                        </td>
                        <td class="p-3 font-mono">
                            <div class="flex items-center">
                                <span class="text-gray-400 mr-0.5">₹</span>
                                <input type="number" step="0.5" value="${r.retail_price}" oninput="updateDynamicRowField(${r.id}, 'retail_price', parseFloat(this.value) || 0)" class="w-16 text-xs font-bold text-emerald-700 border border-transparent hover:border-gray-300 focus:border-emerald-600 rounded-lg p-1 bg-transparent" title="Selling Price">
                            </div>
                        </td>
                        <td class="p-3 font-mono">
                            <input type="number" value="${r.stock || 100}" oninput="updateDynamicRowField(${r.id}, 'stock', parseInt(this.value) || 0)" class="w-14 text-xs font-mono text-gray-700 border border-transparent hover:border-gray-300 focus:border-emerald-600 rounded-lg p-1 bg-transparent" title="Stock Quantity">
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
            const visibleRows = Array.from(document.querySelectorAll('.dynamic-row-item')).filter(tr => tr.style.display !== 'none');
            for (let i = 0; i < Math.min(n, visibleRows.length); i++) {
                const cb = visibleRows[i].querySelector('.dynamic-row-checkbox');
                if (cb) {
                    cb.checked = true;
                    visibleRows[i].classList.add('row-selected');
                }
            }
            updateFloatingBatchBar();
        }

        function selectSameDynamicFamily() {
            clearDynamicRowSelection();
            const visibleRows = Array.from(document.querySelectorAll('.dynamic-row-item')).filter(tr => tr.style.display !== 'none');
            if (visibleRows.length === 0) return;
            const firstRowInput = visibleRows[0].querySelector('input[placeholder="Product Name"]');
            const firstName = firstRowInput ? firstRowInput.value.trim() : '';
            // Strip dimensions to extract base product name
            const baseFamily = firstName.replace(/\b(\d+(\.\d+)?\s*(mm|inch|")|\d+\/\d+(")?|\d+x\d+)\b/ig, '').trim().toLowerCase();

            visibleRows.forEach(tr => {
                const nameInput = tr.querySelector('input[placeholder="Product Name"]');
                const name = nameInput ? nameInput.value.trim().toLowerCase() : '';
                if (name.includes(baseFamily) || (baseFamily.length > 5 && baseFamily.includes(name.slice(0, 10)))) {
                    const cb = tr.querySelector('.dynamic-row-checkbox');
                    if (cb) {
                        cb.checked = true;
                        tr.classList.add('row-selected');
                    }
                }
            });
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

        function resetDynamicTable() {
            if (dynamicRows.length === 0 && groupedProductCards.length === 0) {
                alert('Table pehle se hi khali hai.');
                return;
            }
            if (confirm('Kya aap table ke sabhi purane rows ko delete karke fresh shuru karna chahte hain?')) {
                dynamicRows = [];
                groupedProductCards = [];
                localStorage.removeItem('vyapar_custom_excel_lines');
                renderDynamicRows();
                renderGroupedProductCards();
                clearDynamicRowSelection();
            }
        }

        function exportDynamicToCsv() {
            if (dynamicRows.length === 0 && groupedProductCards.length === 0) {
                alert('Export karne ke liye koi rows nahi hain.');
                return;
            }

            const sheetName = (document.getElementById('dynamicSheetName')?.value || 'catalog').trim().replace(/[^a-zA-Z0-9_-]/g, '_');
            let csv = "Category,Product Code,Product Name,Size/Dimension,Packing 1 (Box),Packing 2 (Carton),MRP,Purchase Cost 1,Cost Price 2,Cost Price 3,Selling Price,Stock,Image URL\n";

            // Add grouped cards
            groupedProductCards.forEach(c => {
                const escapeCsv = (str) => `"${(str || '').toString().replace(/"/g, '""')}"`;
                c.variants.forEach(v => {
                    csv += [
                        escapeCsv(c.category),
                        escapeCsv(v.product_code || ''),
                        escapeCsv(c.parent_name),
                        escapeCsv(v.size || 'Standard'),
                        escapeCsv(v.packing_1 || ''),
                        escapeCsv(v.packing_2 || ''),
                        v.mrp,
                        v.purchase_cost,
                        v.cost_price_2 || 0,
                        v.cost_price_3 || 0,
                        v.retail_price,
                        v.stock || 100,
                        escapeCsv(c.image_url)
                    ].join(',') + "\n";
                });
            });

            // Add flat rows
            dynamicRows.forEach(r => {
                const escapeCsv = (str) => `"${(str || '').toString().replace(/"/g, '""')}"`;
                csv += [
                    escapeCsv(r.group_type),
                    escapeCsv(r.product_code || ''),
                    escapeCsv(r.product_name),
                    escapeCsv(r.size || 'Standard'),
                    escapeCsv(r.packing_1 || ''),
                    escapeCsv(r.packing_2 || ''),
                    r.mrp,
                    r.purchase_cost,
                    r.cost_price_2 || 0,
                    r.cost_price_3 || 0,
                    r.retail_price,
                    r.stock || 100,
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
            if (dynamicRows.length === 0 && groupedProductCards.length === 0) {
                alert('Save karne ke liye kam se kam 1 row ya 1 Grouped Card hona zaroori hai.');
                return;
            }

            const sheetTitle = (document.getElementById('dynamicSheetName')?.value || 'Catalog Sheet').trim();
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
                    rows: dynamicRows,
                    grouped_cards: groupedProductCards
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

        function renderPhotosIntoUi(images) {
            if (!Array.isArray(images) || images.length === 0) return;

            const grid = document.getElementById('drawerImagesGrid');
            const onPageGrid = document.getElementById('onPagePhotoGrid');
            const emptyPrompt = document.getElementById('drawerEmptyPrompt');
            const onPageLoading = document.getElementById('onPagePhotoLoadingMsg');

            if (emptyPrompt) emptyPrompt.remove();
            if (onPageLoading) onPageLoading.remove();

            images.forEach(img => {
                const imgUrl = img.url || img.asset_url;
                const assetUrl = img.asset_url || img.url;
                const name = img.name || 'Catalog Item';
                const source = img.source === 'plasto_master' ? 'Master' : 'Crop';

                // Check if already in drawer
                if (grid && !grid.querySelector(`.drawer-img-card[onclick*="${imgUrl}"]`)) {
                    const card = document.createElement('div');
                    card.className = "drawer-img-card border border-gray-200 rounded-2xl p-2 bg-gray-50 hover:bg-indigo-50 hover:border-indigo-400 cursor-pointer text-center group transition";
                    card.setAttribute('data-name', name.toLowerCase());
                    card.onclick = () => selectDrawerImage(imgUrl, assetUrl);
                    card.innerHTML = `
                        <div class="h-20 w-full bg-white rounded-xl p-1 mb-1.5 flex items-center justify-center overflow-hidden border border-gray-100 group-hover:scale-105 transition">
                            <img src="${assetUrl}" alt="${escapeHtml(name)}" class="max-h-full max-w-full object-contain">
                        </div>
                        <h5 class="text-[11px] font-bold text-gray-800 truncate" title="${escapeHtml(name)}">${escapeHtml(name)}</h5>
                        <span class="text-[9px] text-indigo-600 font-semibold">${source}</span>
                    `;
                    grid.appendChild(card);
                }

                // Check if already in on-page tray
                if (onPageGrid && !onPageGrid.querySelector(`.on-page-photo-card[onclick*="${imgUrl}"]`)) {
                    const trayCard = document.createElement('div');
                    trayCard.className = "on-page-photo-card flex items-center gap-2.5 p-2 bg-gray-50 hover:bg-indigo-50 border border-gray-200 hover:border-indigo-400 rounded-2xl cursor-pointer transition shrink-0 group";
                    trayCard.setAttribute('data-name', name.toLowerCase());
                    trayCard.onclick = () => handleOnPagePhotoClick(imgUrl, assetUrl);
                    trayCard.innerHTML = `
                        <div class="h-14 w-14 bg-white rounded-xl p-1 flex items-center justify-center overflow-hidden border border-gray-200 group-hover:scale-105 transition">
                            <img src="${assetUrl}" alt="${escapeHtml(name)}" class="max-h-full max-w-full object-contain">
                        </div>
                        <div class="max-w-[120px]">
                            <h5 class="text-[11px] font-bold text-gray-800 truncate" title="${escapeHtml(name)}">${escapeHtml(name)}</h5>
                            <span class="text-[9px] text-indigo-600 font-semibold block">${source}</span>
                            <span class="text-[9px] text-emerald-600 font-bold group-hover:underline">Attach &rarr;</span>
                        </div>
                    `;
                    onPageGrid.appendChild(trayCard);
                }
            });

            const totalDrawer = document.querySelectorAll('.drawer-img-card').length;
            const countEl = document.getElementById('drawerImagesCount');
            if (countEl) countEl.innerText = `${totalDrawer} Photos`;

            const onPageCount = document.querySelectorAll('.on-page-photo-card').length;
            const onPageCountEl = document.getElementById('onPagePhotoCount');
            if (onPageCountEl) onPageCountEl.innerText = `${onPageCount} Photos`;
        }

        function loadGalleryImagesAjax() {
            fetch("{{ route('seller.catalog.gallery_json') }}")
                .then(res => res.json())
                .then(data => {
                    if (data && data.success && Array.isArray(data.images) && data.images.length > 0) {
                        renderPhotosIntoUi(data.images);
                    }
                })
                .catch(err => {
                    console.warn('AJAX photo bank fetch error:', err);
                });
        }

        // Initialize when DOM is ready
        window.addEventListener('DOMContentLoaded', () => {
            initDynamicSheet();
            loadGalleryImagesAjax();

            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('mode') === 'saved_job') {
                toggleDynamicMode(false);
            } else {
                toggleDynamicMode(true);
            }

            // Restore any locally cached crops into Photo Bank Drawer if missing
            try {
                const cached = JSON.parse(localStorage.getItem('vyapar_cached_crops') || '[]');
                if (cached.length > 0) {
                    renderPhotosIntoUi(cached);
                }
            } catch (e) {}
        });
    </script>
</body>
</html>
