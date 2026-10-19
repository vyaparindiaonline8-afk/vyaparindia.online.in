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
                    <form action="{{ route('seller.catalog.excel_mapper.load_live_cards') }}" method="POST" class="inline" onsubmit="const btn = this.querySelector('button'); btn.disabled = true; btn.innerHTML = '<i class=\'fa-solid fa-spinner fa-spin\'></i> लोड हो रहा है...';">
                        @csrf
                        <button type="submit" class="px-3.5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-xs shadow-md shadow-blue-500/20 flex items-center gap-1.5 transition active:scale-95" title="लाइव स्टोर के सभी 78 कार्ड्स व साइज़ को एक्सेल शीट में लोड करें">
                            <i class="fa-solid fa-rotate text-amber-300"></i>
                            <span>🔄 लाइव कार्ड्स लोड करें (Master Sync)</span>
                        </button>
                    </form>
                    <button type="button" onclick="toggleDynamicMode(true)" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-600/30 flex items-center gap-2 transition active:scale-95">
                        <i class="fa-solid fa-file-excel"></i>
                        <span>+ Import New Excel</span>
                    </button>
                    <button type="button" onclick="resetDynamicTable()" class="px-3.5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-black text-xs border border-rose-200 flex items-center gap-1.5 transition active:scale-95 cursor-pointer" title="Purana data hatakar nayi list shuru karein">
                        <i class="fa-solid fa-broom text-rose-600"></i>
                        <span>Start Fresh (Nayi List)</span>
                    </button>
                    <button type="button" onclick="downloadSampleExcelTemplate()" class="px-3.5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition flex items-center gap-1.5" title="Download Excel template (.xlsx)">
                        <i class="fa-solid fa-download text-emerald-600"></i>
                        <span>Excel Template</span>
                    </button>
                    @if($job)
                        @if($job->status === 'published')
                            <a href="{{ route('seller.inventory.index') }}" class="px-3.5 py-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 font-extrabold text-xs flex items-center gap-1.5 hover:bg-emerald-100 transition shadow-xs" title="View published items in store inventory">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                <span>Published Live</span>
                            </a>
                        @endif
                        <form action="{{ route('seller.catalog.excel_mapper.publish_direct') }}" method="POST" id="directPublishForm" onsubmit="if(!confirm('Kya aap in sabhi products ko apne live store par publish karna chahte hain?')) return false; const btn = this.querySelector('button'); btn.disabled = true; btn.innerHTML = '<i class=\'fa-solid fa-spinner fa-spin\'></i> Publishing...'; return true;">
                            @csrf
                            <input type="hidden" name="job_id" value="{{ $job->id }}">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-md shadow-indigo-600/30 flex items-center gap-2 transition active:scale-95">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <span>{{ $job->status === 'published' ? 'Update & Re-Publish #' . $job->id : 'Publish Saved Job #' . $job->id }}</span>
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
            <!-- ⚡ PDF Import Success Alert Banner -->
            <div id="pdfImportSuccessAlert" class="hidden p-4 rounded-3xl bg-gradient-to-r from-emerald-600 to-teal-700 text-white shadow-lg flex items-center justify-between gap-3 animate-fade-in border border-emerald-400">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-white/20 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-file-excel text-amber-300"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black" id="pdfImportAlertTitle">PDF Lines Successfully Imported!</h4>
                        <p class="text-[11px] text-emerald-100" id="pdfImportAlertDesc">Niche spreadsheet table me sabhi rows taiyar hain. Ab aap kisi bhi row ko select karke photo jod sakte hain ya direct publish kar sakte hain.</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('pdfImportSuccessAlert').classList.add('hidden')" class="px-3.5 py-1.5 rounded-xl bg-white/20 hover:bg-white/30 text-white text-xs font-bold transition shrink-0">
                    Samajh Gaya (Dismiss)
                </button>
            </div>

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

                    <button type="button" onclick="openSmartPasteModal()" class="px-3.5 py-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-extrabold text-xs shadow-md shadow-violet-600/30 flex items-center gap-1.5 transition active:scale-95" title="Copy kiya hua text ya table direct paste karein (Images & junk auto-ignored)">
                        <i class="fa-solid fa-paste"></i>
                        <span>📋 Smart Paste</span>
                    </button>

                    <button type="button" onclick="openBulkCategoryModal()" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-gray-950 font-black text-xs shadow-md shadow-amber-500/20 flex items-center gap-1.5 transition active:scale-95" title="Sabhi rows ka Category 1-click me badlein">
                        <i class="fa-solid fa-tags"></i>
                        <span>⚡ Bulk Category</span>
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
                    <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                        <!-- Folder Filter Select -->
                        <div class="flex items-center gap-1.5 bg-indigo-50 border border-indigo-200 rounded-xl px-2.5 py-1 text-xs">
                            <i class="fa-solid fa-folder-tree text-indigo-600"></i>
                            <select id="onPageFolderSelect" onchange="filterOnPagePhotoByFolder(this.value)" class="bg-transparent font-black text-indigo-950 text-xs focus:outline-none cursor-pointer">
                                <option value="ALL">📁 All Folders ({{ count($galleryImages) }})</option>
                                @foreach($imageFolders ?? [] as $fName => $fCount)
                                    <option value="{{ $fName }}">📁 {{ $fName }} ({{ $fCount }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="relative w-44 sm:w-56">
                            <input type="text" id="onPagePhotoSearch" placeholder="Search in folder..." oninput="filterOnPagePhotoBank(this.value)" class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-gray-300 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-indigo-600 bg-gray-50 focus:bg-white">
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
                            <div class="on-page-photo-card flex items-center gap-2.5 p-2 bg-gray-50 hover:bg-indigo-50 border border-gray-200 hover:border-indigo-400 rounded-2xl cursor-pointer transition shrink-0 group" onclick="handleOnPagePhotoClick('{{ $g['url'] }}', '{{ $g['asset_url'] }}')" data-name="{{ strtolower($g['name']) }}" data-folder="{{ strtolower($g['folder'] ?? 'general') }}" data-raw-folder="{{ $g['folder'] ?? 'General' }}">
                                <div class="h-14 w-14 bg-white rounded-xl p-1 flex items-center justify-center overflow-hidden border border-gray-200 group-hover:scale-105 transition">
                                    <img src="{{ $g['asset_url'] }}" alt="{{ $g['name'] }}" class="max-h-full max-w-full object-contain">
                                </div>
                                <div class="max-w-[120px]">
                                    <h5 class="text-[11px] font-bold text-gray-800 truncate" title="{{ $g['name'] }}">{{ $g['name'] }}</h5>
                                    <span class="text-[9px] text-indigo-600 font-semibold block">{{ ($g['source'] ?? '') === 'plasto_master' ? 'Master' : 'Crop' }}</span>
                                    <span class="text-[8px] text-gray-500 font-bold truncate block">📁 {{ $g['folder'] ?? 'General' }}</span>
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

            <!-- ⚡ Workflow Progress & Status Banner -->
            <div class="bg-indigo-50/80 border border-indigo-200 rounded-3xl p-4 flex flex-wrap items-center justify-between gap-4 shadow-xs">
                <div class="flex items-center gap-3">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black text-gray-900">Pending to Link:</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 font-mono" id="pendingRowsCountText">0 Rows Left</span>
                            <span class="text-gray-300">|</span>
                            <span class="text-xs font-black text-gray-900">Completed Cards:</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-indigo-100 text-indigo-800 border border-indigo-300 font-mono" id="completedCardsCountText">0 Cards Ready</span>
                        </div>
                        <p class="text-[11px] text-gray-600 mt-0.5">
                            <b>Superfast Workflow:</b> <b>"Next 6"</b> dabayein &rarr; Photo par click karein &rarr; Rows card me save ho kar table se hat jayengi!
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="saveSelectedIntoCardAndRemove()" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-600/30 flex items-center gap-2 transition active:scale-95" title="Selected rows ko 1 card me group karke table se hata do">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save into Card & Remove from List</span>
                    </button>
                </div>
            </div>

            <!-- ⚡ 1-CLICK AUTO-DISTRIBUTE SPEED BANNER -->
            <div class="p-4 bg-gradient-to-r from-amber-500/10 via-emerald-500/10 to-indigo-500/10 border-2 border-emerald-400/80 rounded-3xl flex flex-wrap items-center justify-between gap-4 shadow-sm" id="autoDistributeBanner">
                <div class="flex items-center gap-3.5 flex-1 min-w-[280px]">
                    <div class="h-11 w-11 rounded-2xl bg-gradient-to-br from-amber-500 to-emerald-600 text-white flex items-center justify-center text-lg shadow-md shadow-emerald-500/20 shrink-0">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-gray-900 flex items-center gap-2">
                            <span>⚡ 1-Click Auto-Distribute into Product Family Cards</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-extrabold">Instant Magic</span>
                        </h4>
                        <p class="text-[11px] text-gray-600 mt-0.5">
                            291 items ko 1-1 karke select karne ki zaroorat nahi! Click karte hi sabhi items (jaise 90° Elbow, Pipe 40 SCH, Tee, Union) apne-apne <b>alag Cards</b> me distribute ho jayenge — <b>fir aapko sirf 1-1 Photo link karni hogi!</b>
                        </p>
                    </div>
                </div>
                <button type="button" onclick="autoGroupAllDynamicRowsIntoCards()" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-indigo-600 hover:from-emerald-500 hover:to-indigo-500 text-white font-black text-xs shadow-lg shadow-emerald-600/30 flex items-center gap-2 transition active:scale-95 shrink-0">
                    <i class="fa-solid fa-bolt text-amber-300"></i>
                    <span>Auto-Distribute into Separate Cards &rarr;</span>
                </button>
            </div>

            <!-- 🏷️ Dynamic Category Filter Tabs Bar (Filter UPVC, CPVC, SWR, etc.) -->
            <div class="bg-white p-3.5 rounded-3xl border border-gray-200 shadow-xs space-y-2.5" id="dynamicCategoryTabsWrapper">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black text-gray-900 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-layer-group text-indigo-600"></i>
                            <span>Category Wise Pending Items:</span>
                        </span>
                        <span class="text-[11px] text-gray-500 font-medium">(Category par click karke sirf bache huye unmapped items dekhein)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-amber-50 text-amber-800 border border-amber-200 flex items-center gap-1.5 shadow-2xs" id="dynamicPendingStatsBadge">
                            <i class="fa-solid fa-clock-rotate-left text-amber-600"></i>
                            <span>0 Unmapped Left</span>
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2 overflow-x-auto pb-1" id="dynamicCategoryFilterContainer">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>

            <!-- Dynamic Table Card -->
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden" id="dynamicTableCard">
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
                        <button type="button" onclick="openBulkItemNameModal()" class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-900 font-extrabold text-xs border border-indigo-300 flex items-center gap-1 transition" title="Selected rows ka Product Name ek sath set karein">
                            <i class="fa-solid fa-pen-to-square text-indigo-600"></i>
                            <span>Set Item Name</span>
                        </button>
                        <button type="button" onclick="openBulkCategoryModal()" class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 font-extrabold text-xs border border-amber-300 flex items-center gap-1 transition" title="Sabhi rows ka Category 1 click me badlein">
                            <i class="fa-solid fa-tags text-amber-600"></i>
                            <span>Bulk Category</span>
                        </button>
                        <button type="button" onclick="clearOnlyPendingRows()" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-300 flex items-center gap-1 transition" title="Bache huye pending rows ko saaf karein (Aapke cards surakshit rahenge)">
                            <i class="fa-solid fa-eraser text-slate-600"></i>
                            <span>Clear Pending List</span>
                        </button>
                        <button type="button" onclick="resetDynamicTable()" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 flex items-center gap-1 transition" title="Purane sabhi rows aur cards dono ko delete karein">
                            <i class="fa-solid fa-trash-can"></i>
                            <span>Reset Everything</span>
                        </button>
                        <button type="button" onclick="openColumnMapperModal()" id="btnOpenColumnMapper" class="px-3 py-1 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 font-extrabold text-xs border border-teal-200 flex items-center gap-1.5 transition ml-1" title="Excel Sheet ke columns ko dobara map karein">
                            <i class="fa-solid fa-table-columns text-teal-600"></i>
                            <span>Map Columns</span>
                        </button>
                        <button type="button" onclick="autoGroupAllDynamicRowsIntoCards()" class="px-3 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-sm flex items-center gap-1.5 transition ml-1" title="Sabhi rows ko unki family ke alag-alag cards me distribute karein">
                            <i class="fa-solid fa-bolt text-amber-300"></i>
                            <span>Auto-Distribute Cards</span>
                        </button>
                        <button type="button" onclick="autoFixAllExistingCards()" class="px-3 py-1 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-sm flex items-center gap-1.5 transition ml-1 active:scale-95" title="Bina manual mehnat ke Cards ke Descriptions aur Tape sizes ko auto-fix karein">
                            <i class="fa-solid fa-wand-magic-sparkles text-yellow-200"></i>
                            <span>Auto-Fix Cards</span>
                        </button>
                        <button type="button" onclick="saveSelectedIntoCardAndRemove()" class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-sm flex items-center gap-1.5 transition ml-1 active:scale-95" title="Selected rows ko 1 card me group karke table se hata do taaki next items samne aa sakein">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Save Card & Next &rarr;</span>
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
                                <th class="p-3 w-24">HSN Code</th>
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
                <div class="flex flex-wrap items-center justify-between gap-3 pb-2 border-b border-gray-200">
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
                    <div class="flex items-center gap-2 flex-wrap">
                        <button type="button" onclick="aiFixAllCardDescriptions()" id="btnAiFixDescriptions" class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-black text-xs shadow-md hover:shadow-lg flex items-center gap-2 transition active:scale-95" title="Bina names ya codes badle sirf descriptions ko AI se har product ke sahi hisab se set karein">
                            <i class="fa-solid fa-wand-magic-sparkles text-yellow-300"></i>
                            <span>🪄 AI Fix Descriptions Only</span>
                        </button>
                        <button type="button" onclick="autoFixAllExistingCards()" id="btnAutoFixAllCards" class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-amber-500 via-orange-500 to-indigo-600 hover:from-amber-600 hover:to-indigo-700 text-white font-black text-xs shadow-md hover:shadow-lg flex items-center gap-2 transition active:scale-95" title="Bina manual mehnat ke sare cards ke Descriptions, Tape Sizes (3M x 16mm, 5M x 19mm) aur Series Names auto-fix karein">
                            <i class="fa-solid fa-wand-magic-sparkles text-yellow-200"></i>
                            <span>✨ 1-Click Auto-Fix All Cards</span>
                        </button>
                    </div>
                </div>

                <!-- 🔗 Batch Bar for Selected Cards -->
                <div id="groupedCardsBatchBar" class="hidden bg-indigo-50 border-2 border-indigo-200 rounded-2xl p-3 flex flex-wrap items-center justify-between gap-3 text-xs shadow-sm">
                    <div class="flex items-center gap-2">
                        <div class="h-7 w-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs">
                            <i class="fa-solid fa-code-merge"></i>
                        </div>
                        <span class="font-extrabold text-indigo-900" id="groupedCardsSelectedCountText">0 Cards Selected</span>
                        <span class="text-gray-500 text-[11px] hidden sm:inline">(In cards ke sabhi sizes ko ek single card me jod sakte hain)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openMergeSelectedCardsModal()" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-md shadow-indigo-600/30 flex items-center gap-1.5 transition active:scale-95">
                            <i class="fa-solid fa-code-merge"></i>
                            <span>🔗 Merge Selected Cards into 1</span>
                        </button>
                        <button type="button" onclick="clearGroupedCardSelection()" class="px-3 py-2 rounded-xl bg-white hover:bg-gray-100 text-gray-700 font-bold text-xs border border-gray-200 transition">
                            Cancel
                        </button>
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
            <button type="button" onclick="saveSelectedIntoCardAndRemove()" class="px-4 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-gray-950 font-black text-xs shadow-lg shadow-emerald-500/30 flex items-center gap-1.5 transition active:scale-95" title="Selected rows ka ALAG SE NAYA CARD banayein aur table se hata dein">
                <i class="fa-solid fa-plus-circle"></i>
                <span>✨ Create NEW Card (<span id="btnBatchCount">0</span>)</span>
            </button>
            <button type="button" onclick="openGalleryDrawerForBatch()" class="px-4 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs shadow-lg shadow-indigo-600/30 flex items-center gap-2 transition active:scale-95" title="Photo choose karke naya card save karein">
                <i class="fa-solid fa-images"></i>
                <span>Pick Photo & Save Card</span>
            </button>
            <div id="addToExistingCardWrapper" class="hidden flex items-center gap-1.5 bg-gray-800/80 p-1 rounded-2xl border border-gray-700">
                <span class="text-[10px] text-gray-400 pl-1">or merge into:</span>
                <select id="selectTargetCard" class="bg-gray-900 text-amber-300 text-[11px] font-bold py-1 px-2 rounded-xl border border-gray-700 focus:outline-none max-w-[150px] truncate">
                    <!-- Populated dynamically -->
                </select>
                <button type="button" onclick="addSelectedRowsToTargetCard()" class="px-2.5 py-1 rounded-xl bg-amber-500 hover:bg-amber-400 text-gray-950 font-black text-[11px] flex items-center gap-1 transition" title="Selected rows ko is existing card me merge karein">
                    <span>Merge</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 📊 EXCEL COLUMN MAPPING MODAL              -->
    <!-- ========================================== -->
    <div id="excelColumnMapModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[9999] hidden flex items-center justify-center p-2 sm:p-4 md:p-6" onclick="closeColumnMapperModal()">
        <div class="bg-white rounded-2xl sm:rounded-3xl max-w-3xl w-full shadow-2xl border border-gray-200 flex flex-col h-[94vh] sm:h-[88vh] overflow-hidden" onclick="event.stopPropagation()">
            
            <!-- Modal Header (Always Sticky Top) -->
            <div class="p-3.5 sm:p-4 border-b border-gray-200 bg-gradient-to-r from-emerald-50 via-teal-50 to-indigo-50 flex items-center justify-between shrink-0 sticky top-0 z-30">
                <div class="flex items-center gap-2.5">
                    <div class="h-9 w-9 sm:h-10 sm:w-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-base sm:text-lg shadow-md shadow-emerald-600/30 shrink-0">
                        <i class="fa-solid fa-table-columns"></i>
                    </div>
                    <div>
                        <h3 class="text-xs sm:text-base font-black text-gray-900 flex items-center gap-1.5 sm:gap-2">
                            <span>Excel Column Mapper</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-bold" id="mapTotalRowsCount">0 Rows Found</span>
                        </h3>
                        <p class="text-[10px] sm:text-xs text-gray-600 font-medium">
                            कॉलम मैच करें ताकि <b>MRP</b> व <b>Sizes</b> सही लोड हों!
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="applyExcelColumnMapping()" class="px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-600/30 flex items-center gap-1.5 transition active:scale-95" title="Click to apply column mapping immediately">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Apply Mapping</span>
                    </button>
                    <button type="button" onclick="closeColumnMapperModal()" class="h-8 w-8 rounded-xl bg-white hover:bg-gray-100 text-gray-500 hover:text-gray-800 flex items-center justify-center text-sm shadow-xs transition" title="Close">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body (Scrollable with smooth scrolling) -->
            <div class="p-4 sm:p-6 space-y-6 overflow-y-auto flex-1 overscroll-contain bg-slate-50/40">
                
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

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
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
                            <p class="text-[10px] text-gray-500">Item Code, SKU, Art No (jaise: UPVC-01)</p>
                        </div>

                        <!-- 5b. HSN / SAC Code -->
                        <div class="bg-slate-50 p-3.5 rounded-2xl border border-indigo-200 space-y-1">
                            <label class="text-xs font-black text-indigo-950 flex items-center justify-between">
                                <span>📋 HSN / SAC Code:</span>
                                <span class="text-[10px] text-gray-500 font-bold">(Optional)</span>
                            </label>
                            <select id="mapColHsn" onchange="updateMappingPreview()" class="w-full text-xs font-bold py-2 px-2.5 rounded-xl border border-indigo-300 bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <!-- Populated dynamically -->
                            </select>
                            <p class="text-[10px] text-gray-500">GST HSN (Default: 39174000)</p>
                        </div>

                        <!-- 6. Packing 1 (Box / Inner Pack) -->
                        <div class="bg-slate-50 p-3.5 rounded-2xl border border-gray-200 space-y-1">
                            <label class="text-xs font-black text-gray-900 flex items-center justify-between">
                                <span>📦 Packing 1 (Box):</span>
                                <span class="text-[10px] text-gray-500 font-bold">(Optional)</span>
                            </label>
                            <select id="mapColPack1" onchange="updateMappingPreview()" class="w-full text-xs font-bold py-2 px-2.5 rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <!-- Populated dynamically -->
                            </select>
                            <p class="text-[10px] text-gray-500">Box Pack (jaise: 20 pcs, 50 pcs)</p>
                        </div>

                        <!-- 7. Packing 2 (Carton / Master Bag) -->
                        <div class="bg-slate-50 p-3.5 rounded-2xl border border-gray-200 space-y-1">
                            <label class="text-xs font-black text-gray-900 flex items-center justify-between">
                                <span>📦 Packing 2 (Bag):</span>
                                <span class="text-[10px] text-gray-500 font-bold">(Optional)</span>
                            </label>
                            <select id="mapColPack2" onchange="updateMappingPreview()" class="w-full text-xs font-bold py-2 px-2.5 rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <!-- Populated dynamically -->
                            </select>
                            <p class="text-[10px] text-gray-500">Master Bag, Carton (200 pcs)</p>
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

                <!-- Section 4: PRODUCT DETAILS & IMAGE (OPTIONAL) -->
                <div class="space-y-2 pt-2 border-t border-gray-100">
                    <div class="flex items-center justify-between gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-purple-100 text-purple-900 border border-purple-300">
                            🟣 OPTIONAL: DESCRIPTION & IMAGE URL (Preserve Links on Re-upload)
                        </span>
                        <span class="text-xs text-purple-700 font-medium">Export ki gayi sheet wapas upload karte waqt photo links aur details safe rahenge</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- 13. Description -->
                        <div class="bg-slate-50 p-3 rounded-2xl border border-gray-200 space-y-1">
                            <label class="text-xs font-black text-gray-900 flex items-center justify-between">
                                <span>📝 Description / Specs:</span>
                                <span class="text-[10px] text-gray-500 font-bold">(Optional)</span>
                            </label>
                            <select id="mapColDesc" onchange="updateMappingPreview()" class="w-full text-xs font-bold py-1.5 px-2 rounded-xl border border-gray-300 bg-white focus:ring-1 focus:ring-purple-500 focus:outline-none">
                                <!-- Populated dynamically -->
                            </select>
                            <p class="text-[9px] text-gray-500">Product specifications, series, details</p>
                        </div>

                        <!-- 14. Image URL -->
                        <div class="bg-slate-50 p-3 rounded-2xl border border-indigo-300 space-y-1">
                            <label class="text-xs font-black text-gray-900 flex items-center justify-between">
                                <span>🖼️ Image URL / Photo Link:</span>
                                <span class="text-[10px] text-indigo-700 font-bold">(Optional)</span>
                            </label>
                            <select id="mapColImage" onchange="updateMappingPreview()" class="w-full text-xs font-bold py-1.5 px-2 rounded-xl border border-indigo-400 bg-white focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                                <!-- Populated dynamically -->
                            </select>
                            <p class="text-[9px] text-gray-500">Cloudinary / Web Image URL</p>
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
                                    <th class="p-2.5">HSN</th>
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

            <!-- Modal Footer (Always Sticky Bottom) -->
            <div class="p-3.5 sm:p-4 border-t border-gray-200 bg-white flex items-center justify-between shrink-0 sticky bottom-0 z-30 shadow-xs">
                <button type="button" onclick="closeColumnMapperModal()" class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs border border-gray-200 transition">
                    Cancel
                </button>
                <button type="button" onclick="applyExcelColumnMapping()" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs sm:text-sm shadow-md shadow-emerald-600/30 flex items-center gap-2 transition active:scale-95">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Apply Mapping & Load All Products</span>
                </button>
            </div>

        </div>
    </div>

    <!-- ========================================== -->
    <!-- 🔗 MERGE CARDS MODAL                       -->
    <!-- ========================================== -->
    <div id="mergeCardsModal" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-[9999] hidden flex items-center justify-center p-4" onclick="closeMergeCardsModal()">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 w-full max-w-lg overflow-hidden animate-scale-up" onclick="event.stopPropagation()">
            <!-- Header -->
            <div class="p-5 border-b border-gray-100 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-code-merge"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black">2 Cards Ko Ek Card Me Merge Karein</h3>
                        <p class="text-[11px] text-gray-400">Alag huye sizes ko 1 single card me jodh dein</p>
                    </div>
                </div>
                <button type="button" onclick="closeMergeCardsModal()" class="h-8 w-8 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-5 space-y-4">
                <input type="hidden" id="mergeSourceCardId" value="">
                <div class="bg-indigo-50/70 border border-indigo-100 rounded-2xl p-3.5 space-y-1">
                    <div class="text-xs font-bold text-indigo-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-layer-group text-indigo-600"></i>
                        <span id="mergeSourceCardTitle">Source Card</span>
                    </div>
                    <p class="text-[11px] text-indigo-700 font-medium" id="mergeSourceCardDetails">0 sizes</p>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-gray-800 flex items-center justify-between">
                        <span>🎯 Destination Card (Is card me sabhi sizes jodh diye jayenge):</span>
                    </label>
                    <select id="mergeTargetCardSelect" class="w-full text-xs font-bold py-2.5 px-3 rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-2xs">
                        <!-- Populated dynamically -->
                    </select>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-3 text-[11px] text-amber-800 space-y-1">
                    <p class="font-bold flex items-center gap-1.5"><i class="fa-solid fa-circle-info text-amber-600"></i> Merge hone ke baad:</p>
                    <p>• Dono cards ke sabhi sizes ek hi card ke andar jud jayenge (jaise 3M + 5M ek sath).</p>
                    <p>• Destination card ka Title aur Photo barqarar rahega.</p>
                    <p>• Khali ho chuka duplicate card apne aap hat jayega.</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                <button type="button" onclick="closeMergeCardsModal()" class="px-4 py-2 rounded-xl bg-white hover:bg-gray-100 text-gray-700 font-bold text-xs border border-gray-200">
                    Cancel
                </button>
                <button type="button" onclick="confirmMergeCards()" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-md shadow-indigo-600/30 flex items-center gap-1.5 transition active:scale-95">
                    <i class="fa-solid fa-code-merge"></i>
                    <span>Merge All Sizes Now</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- ➡️ MOVE VARIANT TO CARD MODAL              -->
    <!-- ========================================== -->
    <div id="moveVariantModal" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-[9999] hidden flex items-center justify-center p-4" onclick="closeMoveVariantModal()">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 w-full max-w-md overflow-hidden animate-scale-up" onclick="event.stopPropagation()">
            <!-- Header -->
            <div class="p-5 border-b border-gray-100 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black">Variant Ko Dusre Card Me Bhejein</h3>
                        <p class="text-[11px] text-gray-400">Is specific size ko doosre card me transfer karein</p>
                    </div>
                </div>
                <button type="button" onclick="closeMoveVariantModal()" class="h-8 w-8 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-5 space-y-4">
                <input type="hidden" id="moveVariantSourceCardId" value="">
                <input type="hidden" id="moveVariantTargetVariantId" value="">
                <div class="bg-purple-50/70 border border-purple-100 rounded-2xl p-3 space-y-1">
                    <div class="text-xs font-bold text-purple-900" id="moveVariantInfoText">Size: 7.5 Meter</div>
                    <p class="text-[11px] text-purple-700 font-medium" id="moveVariantSourceCardText">Current Card: Yorker Tape</p>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-gray-800">🎯 Destination Card Chunein:</label>
                    <select id="moveVariantTargetSelect" class="w-full text-xs font-bold py-2.5 px-3 rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-purple-500 focus:outline-none shadow-2xs">
                        <!-- Populated dynamically -->
                    </select>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                <button type="button" onclick="closeMoveVariantModal()" class="px-4 py-2 rounded-xl bg-white hover:bg-gray-100 text-gray-700 font-bold text-xs border border-gray-200">
                    Cancel
                </button>
                <button type="button" onclick="confirmMoveVariantToCard()" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-black text-xs shadow-md shadow-purple-600/30 flex items-center gap-1.5 transition active:scale-95">
                    <i class="fa-solid fa-check"></i>
                    <span>Transfer Variant</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- ✏️ SET ITEM NAME FOR SELECTED ROWS MODAL    -->
    <!-- ========================================== -->
    <div id="bulkItemNameModal" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-[9999] hidden flex items-center justify-center p-4" onclick="closeBulkItemNameModal()">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 w-full max-w-md overflow-hidden animate-scale-up" onclick="event.stopPropagation()">
            <!-- Header -->
            <div class="p-5 border-b border-gray-100 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black">Set Product Item Name</h3>
                        <p class="text-[11px] text-gray-400">Selected rows ko ek sath clean name dein</p>
                    </div>
                </div>
                <button type="button" onclick="closeBulkItemNameModal()" class="h-8 w-8 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5 flex items-center justify-between">
                        <span>Product / Item Name:</span>
                        <span class="text-[11px] text-indigo-600 font-bold" id="bulkNameSelectedCountText">0 Rows Selected</span>
                    </label>
                    <input type="text" id="bulkItemNameInput" placeholder="e.g. UPVC 90° Elbow, Ball Valve, Brass Tee..." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs font-bold text-gray-900 bg-white" onkeydown="if(event.key === 'Enter') applyBulkItemName()">
                    <p class="text-[10px] text-gray-400 mt-1">Sabhi selected rows ka Product Name ye ban jayega, unke Sizes aur Rates wese hi rahenge!</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                <button type="button" onclick="closeBulkItemNameModal()" class="px-4 py-2 rounded-xl bg-white hover:bg-gray-100 text-gray-700 font-bold text-xs border border-gray-200">
                    Cancel
                </button>
                <button type="button" onclick="applyBulkItemName()" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-md shadow-indigo-600/30 flex items-center gap-2 transition active:scale-95">
                    <i class="fa-solid fa-check"></i>
                    <span>Apply Name to Selected Rows</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- ⚡ BULK CATEGORY CHANGER MODAL              -->
    <!-- ========================================== -->
    <div id="bulkCategoryModal" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-[9999] hidden flex items-center justify-center p-4" onclick="closeBulkCategoryModal()">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 w-full max-w-md overflow-hidden animate-scale-up" onclick="event.stopPropagation()">
            <!-- Header -->
            <div class="p-5 border-b border-gray-100 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-tags"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black">Change / Set Category</h3>
                        <p class="text-[11px] text-gray-400">1-click me sabhi rows ka category update karein</p>
                    </div>
                </div>
                <button type="button" onclick="closeBulkCategoryModal()" class="h-8 w-8 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">New Category Name:</label>
                    <input type="text" id="bulkCategoryInput" list="allCategoriesList" placeholder="e.g. CPVC, UPVC, Sanitaryware, General Hardware..." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500 text-xs font-bold text-gray-900 bg-white">
                    <p class="text-[10px] text-gray-400 mt-1">Preset chunein ya apna category naam likhein.</p>
                </div>

                <!-- Quick Preset Pills -->
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider">Quick Presets:</label>
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" onclick="setBulkCategoryInput('CPVC')" class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 text-xs font-bold border border-amber-200">CPVC</button>
                        <button type="button" onclick="setBulkCategoryInput('UPVC')" class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-900 text-xs font-bold border border-blue-200">UPVC</button>
                        <button type="button" onclick="setBulkCategoryInput('Pipes & Fittings')" class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-900 text-xs font-bold border border-emerald-200">Pipes & Fittings</button>
                        <button type="button" onclick="setBulkCategoryInput('SWR & Drainage')" class="px-2.5 py-1 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-900 text-xs font-bold border border-purple-200">SWR & Drainage</button>
                        <button type="button" onclick="setBulkCategoryInput('Sanitaryware')" class="px-2.5 py-1 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-900 text-xs font-bold border border-teal-200">Sanitaryware</button>
                        <button type="button" onclick="setBulkCategoryInput('General Hardware')" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold">General Hardware</button>
                        <button type="button" onclick="setBulkCategoryInput('Electrical & Wiring')" class="px-2.5 py-1 rounded-lg bg-yellow-50 hover:bg-yellow-100 text-yellow-900 text-xs font-bold border border-yellow-200">Electrical</button>
                    </div>
                </div>

                <!-- Scope: All vs Selected -->
                <div class="p-3.5 bg-slate-50 rounded-2xl border border-gray-200 space-y-2">
                    <label class="block text-xs font-black text-gray-800">Apply Scope:</label>
                    <div class="space-y-1.5">
                        <label class="flex items-center gap-2 text-xs font-bold text-gray-700 cursor-pointer">
                            <input type="radio" name="bulkCategoryScope" value="all" checked class="text-amber-600 focus:ring-amber-500">
                            <span>Apply to ALL rows in sheet (<span id="bulkAllRowCountText">0</span> Rows)</span>
                        </label>
                        <label class="flex items-center gap-2 text-xs font-bold text-gray-700 cursor-pointer">
                            <input type="radio" name="bulkCategoryScope" value="selected" class="text-amber-600 focus:ring-amber-500">
                            <span>Apply to SELECTED rows only (<span id="bulkSelectedRowCountText">0</span> Selected)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                <button type="button" onclick="closeBulkCategoryModal()" class="px-4 py-2 rounded-xl bg-white hover:bg-gray-100 text-gray-700 font-bold text-xs border border-gray-200">
                    Cancel
                </button>
                <button type="button" onclick="applyBulkCategory()" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-gray-950 font-black text-xs shadow-md shadow-amber-500/20 flex items-center gap-2 transition active:scale-95">
                    <i class="fa-solid fa-check"></i>
                    <span>Apply Category Now</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 📋 SMART PASTE TEXT / TABLE MODAL           -->
    <!-- ========================================== -->
    <div id="smartPasteModal" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-[9999] hidden flex items-center justify-center p-4" onclick="closeSmartPasteModal()">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 w-full max-w-2xl overflow-hidden animate-scale-up max-h-[92vh] flex flex-col" onclick="event.stopPropagation()">
            <!-- Header -->
            <div class="p-5 border-b border-gray-100 bg-slate-900 text-white flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-violet-500/20 text-violet-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-paste"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black">Smart Paste Text / Table</h3>
                        <p class="text-[11px] text-gray-400">PDF, Excel ya WhatsApp se copy kiya text paste karein — middle image aur noise auto-ignore honge!</p>
                    </div>
                </div>
                <button type="button" onclick="closeSmartPasteModal()" class="h-8 w-8 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Body (Scrollable) -->
            <div class="p-6 space-y-4 overflow-y-auto flex-1">
                <!-- Textarea -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5 flex items-center justify-between">
                        <span>Copied Text Paste Karein:</span>
                        <span class="text-[11px] text-gray-400 font-mono" id="smartPasteStatsText">0 lines detected</span>
                    </label>
                    <textarea id="smartPasteInput" rows="7" oninput="previewSmartPaste()" placeholder="Yahan mouse se copy kiya hua data paste karein (Ctrl + V)...&#10;&#10;Jaise:&#10;UPVC Elbow 90° 25mm ₹45.00&#10;UPVC Elbow 90° 32mm ₹68.00&#10;UPVC Tee Equal 25mm ₹55.00" class="w-full p-3.5 rounded-2xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-violet-500 font-mono text-xs bg-slate-50 focus:bg-white text-gray-800 leading-relaxed"></textarea>
                </div>

                <!-- Settings Controls -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 p-3.5 bg-violet-50/60 rounded-2xl border border-violet-100">
                    <div>
                        <label class="block text-[11px] font-bold text-violet-950 mb-1">Base Item Name (Optional):</label>
                        <input type="text" id="smartPasteBaseName" oninput="previewSmartPaste()" placeholder="e.g. UPVC 90° Elbow" class="w-full px-3 py-1.5 rounded-xl border border-violet-200 bg-white font-bold text-xs text-gray-800 focus:ring-1 focus:ring-violet-500" title="Agar likhenge to sabhi rows ko ye naam milega aur unke sizes alag rahenge">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-violet-950 mb-1">Target Category:</label>
                        <input type="text" id="smartPasteCategory" list="allCategoriesList" value="General Hardware" oninput="previewSmartPaste()" placeholder="e.g. CPVC, Hardware, Brass" class="w-full px-3 py-1.5 rounded-xl border border-violet-200 bg-white font-bold text-xs text-violet-900 focus:ring-1 focus:ring-violet-500">
                    </div>
                    <div class="flex items-center gap-2 pt-4">
                        <label class="flex items-center gap-2 text-xs font-bold text-gray-700 cursor-pointer select-none">
                            <input type="checkbox" id="smartPasteFilterNoise" checked onchange="previewSmartPaste()" class="rounded text-violet-600 focus:ring-violet-500 cursor-pointer">
                            <span>Filter Noise ("Available" etc.)</span>
                        </label>
                    </div>
                </div>

                <!-- Live Preview Table -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-black text-gray-800">
                            Parsed Rows Preview (<span id="smartPastePreviewCount">0</span> items):
                        </label>
                    </div>
                    <div class="border border-gray-200 rounded-2xl overflow-hidden max-h-48 overflow-y-auto">
                        <table class="w-full text-left text-xs font-medium">
                            <thead class="bg-gray-50 text-[10px] font-black text-gray-500 uppercase border-b border-gray-200">
                                <tr>
                                    <th class="p-2 w-8 text-center">#</th>
                                    <th class="p-2 w-24">Category</th>
                                    <th class="p-2">Product Name</th>
                                    <th class="p-2 w-20">Size</th>
                                    <th class="p-2 w-20">MRP (₹)</th>
                                </tr>
                            </thead>
                            <tbody id="smartPastePreviewBody" class="divide-y divide-gray-100">
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-gray-400 text-xs">Upar text paste karein to yahan preview dikhega...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex flex-wrap items-center justify-between gap-2 shrink-0">
                <button type="button" onclick="closeSmartPasteModal()" class="px-4 py-2 rounded-xl bg-white hover:bg-gray-100 text-gray-700 font-bold text-xs border border-gray-200">
                    Cancel
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="insertSmartPasteRows(false)" class="px-4 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-black text-xs shadow-md shadow-violet-600/30 flex items-center gap-1.5 transition active:scale-95 cursor-pointer">
                        <i class="fa-solid fa-plus"></i>
                        <span>+ Add to Existing Rows</span>
                    </button>
                    <button type="button" onclick="insertSmartPasteRows(true)" class="px-4 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-extrabold text-xs border border-indigo-200 transition active:scale-95 cursor-pointer" title="Purani rows hata kar sirf naye pasted items rakhein">
                        <i class="fa-solid fa-arrows-rotate"></i>
                        <span>Replace Table</span>
                    </button>
                </div>
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

            <!-- Drawer Folder Filter Tabs -->
            <div class="px-4 py-2 border-b border-gray-200 bg-white flex items-center gap-1.5 overflow-x-auto" id="drawerFolderChips">
                <button type="button" onclick="filterDrawerByFolder('ALL')" id="drawerFolderTab_ALL" class="drawer-folder-btn px-2.5 py-1 rounded-lg text-[11px] font-black bg-indigo-600 text-white shadow-2xs whitespace-nowrap">
                    📁 All ({{ count($galleryImages) }})
                </button>
                @foreach($imageFolders ?? [] as $fName => $fCount)
                    <button type="button" onclick="filterDrawerByFolder('{{ addslashes($fName) }}')" class="drawer-folder-btn px-2.5 py-1 rounded-lg text-[11px] font-bold bg-gray-100 hover:bg-gray-200 text-gray-700 whitespace-nowrap" data-folder="{{ $fName }}">
                        📁 {{ $fName }} ({{ $fCount }})
                    </button>
                @endforeach
            </div>

            <!-- Drawer Search -->
            <div class="p-3 border-b border-gray-100 bg-gray-50">
                <div class="relative">
                    <input type="text" id="drawerSearchInput" placeholder="Search gallery photo (e.g. Elbow, Tee)..." oninput="filterDrawerGallery(this.value)" class="w-full pl-8 pr-3 py-2 rounded-xl border border-gray-300 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-indigo-600 bg-white">
                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-gray-400 text-xs"></i>
                </div>
            </div>

            <!-- Drawer Gallery Grid -->
            <div class="flex-1 overflow-y-auto p-4">
                <div class="grid grid-cols-3 gap-3" id="drawerImagesGrid">
                    @forelse($galleryImages as $g)
                        <div class="drawer-img-card border border-gray-200 rounded-2xl p-2 bg-gray-50 hover:bg-indigo-50 hover:border-indigo-400 cursor-pointer text-center group transition" onclick="selectDrawerImage('{{ $g['url'] }}', '{{ $g['asset_url'] }}')" data-name="{{ strtolower($g['name']) }}" data-folder="{{ strtolower($g['folder'] ?? 'general') }}" data-raw-folder="{{ $g['folder'] ?? 'General' }}">
                            <div class="h-20 w-full bg-white rounded-xl p-1 mb-1.5 flex items-center justify-center overflow-hidden border border-gray-100 group-hover:scale-105 transition">
                                <img src="{{ $g['asset_url'] }}" alt="{{ $g['name'] }}" class="max-h-full max-w-full object-contain">
                            </div>
                            <h5 class="text-[11px] font-bold text-gray-800 truncate" title="{{ $g['name'] }}">{{ $g['name'] }}</h5>
                            <span class="text-[9px] text-indigo-600 font-semibold block">{{ ($g['source'] ?? '') === 'plasto_master' ? 'Master' : 'Crop' }}</span>
                            <span class="text-[8px] text-gray-500 font-bold truncate block">📁 {{ $g['folder'] ?? 'General' }}</span>
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
        let jobId = {{ $job ? $job->id : 'null' }};
        const serverJobProducts = @json($products ?? []);

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
                const isSelected = (btnCat.toLowerCase() === cat.toLowerCase());
                const baseClass = "cat-pill px-4 py-2 rounded-2xl text-xs font-black flex items-center gap-2 transition whitespace-nowrap active:scale-95 ";
                if (isSelected) {
                    btn.className = baseClass + (cat === 'ALL' ? 'bg-gray-900 text-white shadow-sm ring-2 ring-gray-900' : 'bg-indigo-600 text-white shadow-md ring-2 ring-indigo-500');
                } else {
                    btn.className = baseClass + 'bg-white hover:bg-slate-50 text-gray-700 border border-gray-200 shadow-xs';
                }
            });

            if (isDynamicMode) {
                renderDynamicRows();
            }
            applyCombinedFilter();
        }

        function refreshCategoryFilterTabs() {
            if (typeof deduplicateDynamicRowsAgainstCards === 'function') {
                deduplicateDynamicRowsAgainstCards();
            }

            const dynamicContainer = document.getElementById('dynamicCategoryFilterContainer');
            const savedContainer = document.getElementById('categoryFilterContainer');
            if (!dynamicContainer && !savedContainer) return;

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

            const pendingStats = document.getElementById('dynamicPendingStatsBadge');
            if (pendingStats) {
                pendingStats.innerHTML = `<i class="fa-solid fa-clock-rotate-left text-amber-600"></i> <span>${total} Unmapped Left</span>`;
            }

            const sortedCats = Object.keys(counts).sort((a, b) => {
                const prio = (name) => {
                    const u = name.toUpperCase();
                    if (u.includes('UPVC')) return 1;
                    if (u.includes('CPVC')) return 2;
                    if (u.includes('SWR')) return 3;
                    return 10;
                };
                return prio(a) - prio(b) || a.localeCompare(b);
            });

            const buildTabsHtml = (isForDynamic) => {
                let html = `
                    <button type="button" onclick="filterByCategory('ALL')" data-cat-key="ALL" id="catTab_ALL" class="cat-pill px-4 py-2 rounded-2xl text-xs font-black ${activeCategory === 'ALL' ? 'bg-gray-900 text-white shadow-sm ring-2 ring-gray-900' : 'bg-white hover:bg-slate-50 text-gray-700 border border-gray-200 shadow-xs'} flex items-center gap-2 transition whitespace-nowrap active:scale-95">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>${isForDynamic ? 'All Pending' : 'All Categories'}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] ${activeCategory === 'ALL' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-800'} font-mono font-bold" id="catCount_ALL">${total}</span>
                    </button>
                `;

                for (const catName of sortedCats) {
                    const count = counts[catName];
                    const isActive = (activeCategory.toLowerCase() === catName.toLowerCase());
                    let icon = 'fa-tag';
                    let activeColor = 'bg-indigo-600 text-white shadow-md ring-2 ring-indigo-500';
                    let dotColor = 'text-indigo-500';

                    const u = catName.toUpperCase();
                    if (u.includes('UPVC')) {
                        icon = 'fa-droplet';
                        activeColor = 'bg-blue-600 text-white shadow-md ring-2 ring-blue-500';
                        dotColor = 'text-blue-500';
                    } else if (u.includes('CPVC')) {
                        icon = 'fa-fire-flame-curved';
                        activeColor = 'bg-amber-600 text-white shadow-md ring-2 ring-amber-500';
                        dotColor = 'text-amber-500';
                    } else if (u.includes('SWR')) {
                        icon = 'fa-faucet-drip';
                        activeColor = 'bg-emerald-600 text-white shadow-md ring-2 ring-emerald-500';
                        dotColor = 'text-emerald-500';
                    }

                    html += `
                        <button type="button" onclick="filterByCategory('${escapeHtml(catName)}')" data-cat-key="${escapeHtml(catName)}" class="cat-pill px-4 py-2 rounded-2xl text-xs font-extrabold ${isActive ? activeColor : 'bg-white hover:bg-slate-50 text-gray-700 border border-gray-200 shadow-xs'} flex items-center gap-2 transition whitespace-nowrap active:scale-95">
                            <i class="fa-solid ${icon} text-[11px] ${isActive ? 'text-white' : dotColor}"></i>
                            <span>${escapeHtml(catName)}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] ${isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-800'} font-mono font-bold">${count}</span>
                        </button>
                    `;
                }
                return html;
            };

            if (dynamicContainer) dynamicContainer.innerHTML = buildTabsHtml(true);
            if (savedContainer) savedContainer.innerHTML = buildTabsHtml(false);
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

        let activeDrawerFolder = 'ALL';
        let activeOnPageFolder = 'ALL';

        function filterDrawerByFolder(folder) {
            activeDrawerFolder = folder;
            document.querySelectorAll('.drawer-folder-btn').forEach(btn => {
                btn.className = "drawer-folder-btn px-2.5 py-1 rounded-lg text-[11px] font-bold bg-gray-100 hover:bg-gray-200 text-gray-700 whitespace-nowrap";
            });
            const activeTab = (folder === 'ALL') ? document.getElementById('drawerFolderTab_ALL') : document.querySelector(`.drawer-folder-btn[data-folder="${folder}"]`);
            if (activeTab) {
                activeTab.className = "drawer-folder-btn px-2.5 py-1 rounded-lg text-[11px] font-black bg-indigo-600 text-white shadow-2xs whitespace-nowrap";
            }
            applyDrawerFilters();
        }

        function filterDrawerGallery(q) {
            applyDrawerFilters();
        }

        function applyDrawerFilters() {
            const query = (document.getElementById('drawerSearchInput')?.value || '').toLowerCase().trim();
            let visible = 0;
            document.querySelectorAll('.drawer-img-card').forEach(card => {
                const name = (card.getAttribute('data-name') || '').toLowerCase();
                const folder = (card.getAttribute('data-raw-folder') || 'General');

                const matchFolder = (activeDrawerFolder === 'ALL' || folder.toLowerCase() === activeDrawerFolder.toLowerCase());
                const matchQuery = (!query || name.includes(query));

                if (matchFolder && matchQuery) {
                    card.style.display = '';
                    visible++;
                } else {
                    card.style.display = 'none';
                }
            });
            const countEl = document.getElementById('drawerImagesCount');
            if (countEl) countEl.innerText = `${visible} Photos`;
        }

        function filterOnPagePhotoByFolder(folder) {
            activeOnPageFolder = folder;
            applyOnPagePhotoFilters();
        }

        function filterOnPagePhotoBank(q) {
            applyOnPagePhotoFilters();
        }

        function applyOnPagePhotoFilters() {
            const query = (document.getElementById('onPagePhotoSearch')?.value || '').toLowerCase().trim();
            let visible = 0;
            document.querySelectorAll('.on-page-photo-card').forEach(card => {
                const name = (card.getAttribute('data-name') || '').toLowerCase();
                const folder = (card.getAttribute('data-raw-folder') || 'General');

                const matchFolder = (activeOnPageFolder === 'ALL' || folder.toLowerCase() === activeOnPageFolder.toLowerCase());
                const matchQuery = (!query || name.includes(query));

                if (matchFolder && matchQuery) {
                    card.style.display = '';
                    visible++;
                } else {
                    card.style.display = 'none';
                }
            });
            const countEl = document.getElementById('onPagePhotoCount');
            if (countEl) countEl.innerText = `${visible} Photos`;
        }

        function handleOnPagePhotoClick(relUrl, assetUrl) {
            if (isDynamicMode) {
                const checked = Array.from(document.querySelectorAll('.dynamic-row-checkbox:checked')).map(cb => parseInt(cb.value));
                if (checked.length > 0) {
                    saveSelectedIntoCardAndRemove(relUrl, assetUrl);
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
            if (!currentTargetType || currentTargetType === 'browse' || currentTargetType === 'dynamic_batch') {
                if (isDynamicMode) {
                    const checked = Array.from(document.querySelectorAll('.dynamic-row-checkbox:checked')).map(cb => parseInt(cb.value));
                    if (checked.length > 0) {
                        saveSelectedIntoCardAndRemove(relUrl, assetUrl);
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

        function saveToLocalStorage() {
            try {
                localStorage.setItem('vyapar_grouped_cards', JSON.stringify(groupedProductCards));
                localStorage.setItem('vyapar_dynamic_rows', JSON.stringify(dynamicRows));
                localStorage.setItem('vyapar_grouped_next_id', String(groupedCardNextId));
                localStorage.setItem('vyapar_dynamic_next_id', String(dynamicRowNextId));
            } catch (e) {}
        }

        function hydrateFromJobProducts(jobProds) {
            if (!Array.isArray(jobProds) || jobProds.length === 0) return false;
            groupedProductCards = [];
            dynamicRows = [];

            jobProds.forEach((prod) => {
                const variants = prod.variants || [];
                const img = prod.image_url || '';
                const assetUrl = img ? (img.startsWith('http') ? img : ('/' + img.replace(/^\//, ''))) : '';
                const cat = prod.category || prod.group_type || 'General Hardware';

                if (variants.length > 1 || (variants.length === 1 && img)) {
                    groupedProductCards.push({
                        card_id: groupedCardNextId++,
                        parent_name: prod.name,
                        category: cat,
                        description: prod.description || '',
                        image_url: img,
                        asset_url: assetUrl,
                        variants: variants.map((v) => ({
                            id: dynamicRowNextId++,
                            product_code: v.product_code || v.sku || '',
                            hsn_code: v.hsn_code || '39174000',
                            size: v.size || v.variant_name || 'Standard',
                            packing_1: v.packing_1 || '',
                            packing_2: v.packing_2 || '',
                            mrp: parseFloat(v.mrp) || 100,
                            purchase_cost: parseFloat(v.raw_rate || v.purchase_cost) || 60,
                            cost_price_2: parseFloat(v.cost_price_2) || 0,
                            cost_price_3: parseFloat(v.cost_price_3) || 0,
                            retail_price: parseFloat(v.retail_price || v.wholesale_price) || 85,
                            stock: parseInt(v.stock_quantity || v.stock) || 100
                        }))
                    });
                } else if (variants.length === 1) {
                    const v = variants[0];
                    dynamicRows.push({
                        id: dynamicRowNextId++,
                        product_code: v.product_code || v.sku || '',
                        hsn_code: v.hsn_code || '39174000',
                        product_name: prod.name,
                        description: prod.description || '',
                        size: v.size || v.variant_name || 'Standard',
                        packing_1: v.packing_1 || '',
                        packing_2: v.packing_2 || '',
                        group_type: cat,
                        category: cat,
                        mrp: parseFloat(v.mrp) || 100,
                        purchase_cost: parseFloat(v.raw_rate || v.purchase_cost) || 60,
                        cost_price_2: parseFloat(v.cost_price_2) || 0,
                        cost_price_3: parseFloat(v.cost_price_3) || 0,
                        retail_price: parseFloat(v.retail_price || v.wholesale_price) || 85,
                        stock: parseInt(v.stock_quantity || v.stock) || 100,
                        image_url: img,
                        asset_url: assetUrl
                    });
                } else {
                    dynamicRows.push({
                        id: dynamicRowNextId++,
                        product_code: '',
                        hsn_code: prod.hsn_code || '39174000',
                        product_name: prod.name,
                        size: 'Standard',
                        packing_1: '',
                        packing_2: '',
                        group_type: cat,
                        category: cat,
                        mrp: 100,
                        purchase_cost: 60,
                        cost_price_2: 0,
                        cost_price_3: 0,
                        retail_price: 85,
                        stock: 100,
                        image_url: img,
                        asset_url: assetUrl
                    });
                }
            });

            saveToLocalStorage();
            return true;
        }

        let autoSaveTimer = null;
        function autoSaveDraftToBackend() {
            if (autoSaveTimer) clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                if (dynamicRows.length === 0 && groupedProductCards.length === 0) return;
                const sheetTitle = (document.getElementById('dynamicSheetName')?.value || 'Catalog Sheet').trim();
                fetch("{{ route('seller.catalog.excel_mapper.create_sheet') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        job_id: jobId,
                        sheet_name: sheetTitle,
                        rows: dynamicRows,
                        grouped_cards: groupedProductCards
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.job_id) {
                        jobId = data.job_id;
                        console.log('✅ Auto-saved catalog sheet draft to database, job_id:', jobId);
                    }
                })
                .catch(err => console.warn('Auto-save background sync error:', err));
            }, 600);
        }

        function loadFromLocalStorage() {
            try {
                const storedCards = localStorage.getItem('vyapar_grouped_cards');
                const storedRows = localStorage.getItem('vyapar_dynamic_rows');
                let hasLoaded = false;
                if (storedCards) {
                    const parsedCards = JSON.parse(storedCards);
                    if (Array.isArray(parsedCards) && parsedCards.length > 0) {
                        groupedProductCards = parsedCards;
                        groupedCardNextId = parseInt(localStorage.getItem('vyapar_grouped_next_id') || '100');
                        hasLoaded = true;
                    }
                }
                if (storedRows) {
                    const parsedRows = JSON.parse(storedRows);
                    if (Array.isArray(parsedRows) && parsedRows.length > 0) {
                        dynamicRows = parsedRows;
                        dynamicRowNextId = parseInt(localStorage.getItem('vyapar_dynamic_next_id') || '1000');
                        hasLoaded = true;
                    }
                }
                if (hasLoaded && typeof deduplicateDynamicRowsAgainstCards === 'function') {
                    deduplicateDynamicRowsAgainstCards();
                }
                return hasLoaded;
            } catch (e) {
                return false;
            }
        }

        function isNoiseCatalogLine(line) {
            if (!line) return true;
            const clean = line.trim();
            if (clean.length <= 1) return true;

            // Pure numbers or bullet symbols (e.g. "1", "2.", "•")
            if (/^[\d\.\-\*\•\(\)]+$/.test(clean)) return true;

            // Common non-product noise phrases
            const noisePatterns = [
                /^(available\s*(in)?|availability)[\s\:\-]*$/i,
                /^(sizes?|dimensions?|specifications?|description|particulars?|details?)[\s\:\-]*$/i,
                /^(mrp|rate|price|pkg|packing|std\s*pack|box\s*qty|carton\s*qty)[\s\:\-]*$/i,
                /^(sr\.?\s*no\.?|s\.?\s*no\.?|item\s*code|cat\.?\s*no\.?|code|product\s*name)[\s\:\-]*$/i,
                /^(page\s+\d+|price\s*list|w\.?e\.?f\.?|effective\s*from|all\s*rates\s*are|gst\s*extra|terms?\s*&?\s*conditions?)[\s\:\-0-9\/]*$/i,
                /^(hsn\s*code|hsn\s*:\s*\d+|sac\s*code)[\s\:\-0-9]*$/i
            ];

            for (const pat of noisePatterns) {
                if (pat.test(clean)) return true;
            }

            return false;
        }

        function parseCatalogRowText(lineText, lastSectionHeader = '', forcedBaseName = '') {
            let raw = (lineText || '').trim();
            if (!raw) return null;

            if (forcedBaseName && forcedBaseName.trim().length > 0) {
                lastSectionHeader = forcedBaseName.trim();
            }

            // 1. Detect and strip Price / MRP
            let mrp = 0;
            const priceMatch = raw.match(/(?:rs\.?|₹|mrp[\:\s]*|\/)\s*(\d+(?:\.\d+)?)/i);
            if (priceMatch) {
                mrp = parseFloat(priceMatch[1]);
                raw = raw.replace(priceMatch[0], ' ').trim();
            } else {
                const tokens = raw.split(/\s+/);
                if (tokens.length >= 2) {
                    const lastToken = tokens[tokens.length - 1];
                    if (/^\d+(\.\d{1,2})?$/.test(lastToken) && parseFloat(lastToken) > 0) {
                        mrp = parseFloat(lastToken);
                        tokens.pop();
                        raw = tokens.join(' ').trim();
                    }
                }
            }

            // 2. Detect and strip SKU
            let sku = '';
            const skuMatch = raw.match(/\b([A-Z]{2,}[0-9]+[A-Z0-9\-]*|[A-Z0-9]+\-[0-9]+)\b/);
            if (skuMatch && !/^(MM|INCH|CM|MRP|HSN|BOX|BAG)$/i.test(skuMatch[0])) {
                sku = skuMatch[0];
                raw = raw.replace(skuMatch[0], ' ').trim();
            }

            // 3. Detect and strip Description in parentheses e.g. (Auto Stop)
            let description = '';
            const parenMatch = raw.match(/\(([^)]+)\)/);
            if (parenMatch) {
                description = parenMatch[1].trim();
                raw = raw.replace(parenMatch[0], ' ').trim();
            }

            // 4. Detect and strip Size (mm, inch, meter, dimension)
            let size = '';
            const sizePatterns = [
                /\b\d+(\.\d+)?\s*(m|mtr|meter)\s*[xX*]\s*\d+(\.\d+)?\s*(mm)?\b/i,
                /\b\d+(\.\d+)?\s*(mm|cm|mtr|meter)\b/i,
                /\b\d+(\.\d+)?\s*(inch|\"|'')\b/i,
                /\b\d+\s*x\s*\d+(\.\d+)?\s*(mm|inch|\")?\b/i,
                /\b\d+\/\d+\s*(\"|inch)?\b/i,
                /\b\d+\s*[-–]\s*\d+\/\d+\s*(\"|inch)?\b/i
            ];
            for (const pat of sizePatterns) {
                const sm = raw.match(pat);
                if (sm) {
                    size = size ? (size + ' (' + sm[0] + ')') : sm[0];
                    raw = raw.replace(sm[0], ' ').trim();
                }
            }

            // Check measuring tape SKU model codes (3016 -> 3m x 16mm, 5019 -> 5m x 19mm, 1025 -> 10m x 25mm)
            if (!size && sku) {
                if (/3016/.test(sku)) size = '3 Meter x 16mm';
                else if (/5019/.test(sku)) size = '5 Meter x 19mm';
                else if (/1025/.test(sku)) size = '10 Meter x 25mm';
            }

            // 5. Detect packing numbers
            let pack1 = '';
            let pack2 = '';
            const boxM = raw.match(/box[\:\s]*(\d+)/i);
            if (boxM) {
                pack1 = boxM[1];
                raw = raw.replace(boxM[0], ' ').trim();
            }
            const bagM = raw.match(/(?:bag|carton|outer)[\:\s]*(\d+)/i);
            if (bagM) {
                pack2 = bagM[1];
                raw = raw.replace(bagM[0], ' ').trim();
            }

            // Check standalone integers at end
            const remTokens = raw.split(/\s+/);
            const numTokens = [];
            for (let i = remTokens.length - 1; i >= 0; i--) {
                if (/^\d+$/.test(remTokens[i])) {
                    numTokens.unshift(remTokens[i]);
                    remTokens.splice(i, 1);
                    if (numTokens.length >= 2) break;
                } else {
                    break;
                }
            }
            if (!pack1 && !pack2) {
                if (numTokens.length === 2) {
                    pack1 = numTokens[0];
                    pack2 = numTokens[1];
                    raw = remTokens.join(' ').trim();
                } else if (numTokens.length === 1) {
                    pack1 = numTokens[0];
                    raw = remTokens.join(' ').trim();
                }
            } else if (!pack1 && numTokens.length >= 1) {
                pack1 = numTokens[0];
                raw = remTokens.join(' ').trim();
            }

            // Clean up remaining text to form Clean Product Name
            let cleanName = raw.replace(/[\|\,\;\:\-\–\—\t]+/g, ' ').replace(/\s{2,}/g, ' ').trim();

            if (forcedBaseName && forcedBaseName.trim().length > 0) {
                cleanName = forcedBaseName.trim();
            } else if (!cleanName || cleanName.length <= 1 || /^[\d\s\.\-]+$/.test(cleanName)) {
                cleanName = lastSectionHeader || 'Standard Item';
            }

            return {
                name: cleanName,
                description: description,
                size: size || 'Standard',
                pack1: pack1,
                pack2: pack2,
                mrp: mrp > 0 ? mrp : 100,
                sku: sku
            };
        }

        function loadPdfExtractedDataIntoDynamicRows(data) {
            if (!data) return false;
            let lines = Array.isArray(data) ? data : (data.lines || []);
            const structuredRows = data.structured_rows || [];
            const pageNum = data.page || 1;
            const targetCategory = (data && data.category && data.category !== '-') ? data.category.trim() : 'General Hardware';

            // Filter noise words ("Available", headers etc.)
            lines = lines.filter(l => !isNoiseCatalogLine(l));

            if (lines.length === 0 && structuredRows.length === 0) return false;

            dynamicRows = [];
            dynamicRowNextId = 1;

            if (structuredRows && structuredRows.length > 0) {
                structuredRows.forEach((r, idx) => {
                    const name = (r.name || r.product_name || `Item ${idx + 1}`).trim();
                    let size = (r.size && r.size !== '-') ? r.size.trim() : '';
                    const sku = (r.sku && r.sku !== '-') ? r.sku.trim() : `ITM-${100 + idx}`;
                    if (!size || size === 'Standard') {
                        if (/3016/.test(sku)) size = '3 Meter x 16mm';
                        else if (/5019/.test(sku)) size = '5 Meter x 19mm';
                        else if (/1025/.test(sku)) size = '10 Meter x 25mm';
                        else size = size || 'Standard';
                    }
                    const mrp = parseFloat(r.mrp) || (100 + (idx * 20));
                    const cost = Math.round(mrp * 0.65);
                    const retail = Math.round(mrp * 0.88);
                    const category = (r.category && r.category !== '-') ? r.category.trim() : targetCategory;
                    const pack1 = (r.packing_1 && r.packing_1 !== '-') ? r.packing_1.trim() : (r.pack1 || '');
                    const pack2 = (r.packing_2 && r.packing_2 !== '-') ? r.packing_2.trim() : (r.pack2 || '');
                    const desc = (r.description && r.description !== '-') ? r.description.trim() : '';

                    dynamicRows.push({
                        id: dynamicRowNextId++,
                        product_code: sku,
                        hsn_code: '39174000',
                        product_name: name,
                        description: desc,
                        size: size,
                        packing_1: pack1,
                        packing_2: pack2,
                        group_type: category,
                        category: category,
                        mrp: mrp,
                        purchase_cost: cost,
                        cost_price_2: 0,
                        cost_price_3: 0,
                        retail_price: retail,
                        stock: 100,
                        image_url: preselectedImg || '',
                        asset_url: preselectedImg ? ('/' + preselectedImg.replace(/^\//, '')) : ''
                    });
                });
            } else {
                let currentSectionHeader = '';

                lines.forEach((lineText, idx) => {
                    const parsed = parseCatalogRowText(lineText, currentSectionHeader);
                    if (!parsed) return;

                    // Update section header if this line has a meaningful name
                    if (parsed.name && parsed.name !== 'Standard Item' && !/^\d+/.test(parsed.name)) {
                        currentSectionHeader = parsed.name;
                    }

                    let category = targetCategory;
                    const combined = (parsed.name + ' ' + parsed.size).toUpperCase();
                    if (/CPVC/i.test(combined)) category = 'CPVC';
                    else if (/SWR|TRAP|DRAIN/i.test(combined)) category = 'SWR';
                    else if (/AGRI|SOLVENT/i.test(combined)) category = 'Agri & Solvents';
                    else if (/UPVC/i.test(combined)) category = 'UPVC';

                    const cost = Math.round(parsed.mrp * 0.65);
                    const retail = Math.round(parsed.mrp * 0.88);

                    dynamicRows.push({
                        id: dynamicRowNextId++,
                        product_code: parsed.sku || `ITM-${100 + idx}`,
                        hsn_code: '39174000',
                        product_name: parsed.name,
                        description: parsed.description || '',
                        size: parsed.size,
                        packing_1: parsed.pack1,
                        packing_2: parsed.pack2,
                        group_type: category,
                        category: category,
                        mrp: parsed.mrp,
                        purchase_cost: cost,
                        cost_price_2: 0,
                        cost_price_3: 0,
                        retail_price: retail,
                        stock: 100,
                        image_url: preselectedImg || '',
                        asset_url: preselectedImg ? ('/' + preselectedImg.replace(/^\//, '')) : ''
                    });
                });
            }

            if (typeof deduplicateDynamicRowsAgainstCards === 'function') {
                deduplicateDynamicRowsAgainstCards();
            }

            saveToLocalStorage();

            const sheetNameInput = document.getElementById('dynamicSheetName');
            if (sheetNameInput) {
                sheetNameInput.value = `PDF Page ${pageNum} Extracted Sheet (${dynamicRows.length} Items)`;
            }

            renderDynamicRows();
            renderGroupedProductCards();
            refreshCategoryFilterTabs();

            const alertEl = document.getElementById('pdfImportSuccessAlert');
            const alertTitle = document.getElementById('pdfImportAlertTitle');
            const alertDesc = document.getElementById('pdfImportAlertDesc');
            if (alertEl) {
                alertEl.classList.remove('hidden');
                if (alertTitle) alertTitle.innerText = `🎉 PDF Page ${pageNum} se ${dynamicRows.length} Items Excel Sheet Me Aa Gaye!`;
                if (alertDesc) alertDesc.innerText = `Niche table me sabhi rows list ho gayi hain. Ab aap kisi bhi row ko select karke photo link karein ya publish karein.`;
            }

            return true;
        }

        function initDynamicSheet() {
            const urlParams = new URLSearchParams(window.location.search);
            const isFromPdf = urlParams.get('source') === 'pdf_lines';
            const isFreshStart = urlParams.get('new') === '1' || urlParams.get('fresh') === '1';

            if (isFreshStart) {
                dynamicRows = [];
                groupedProductCards = [];
                localStorage.removeItem('vyapar_custom_excel_lines');
                localStorage.removeItem('vyapar_grouped_cards');
                localStorage.removeItem('vyapar_dynamic_rows');
                localStorage.removeItem('vyapar_grouped_next_id');
                localStorage.removeItem('vyapar_dynamic_next_id');
                renderDynamicRows();
                renderGroupedProductCards();
                refreshCategoryFilterTabs();
                toggleDynamicMode(true);
                return;
            }

            // ALWAYS restore existing saved cards & rows from localStorage first!
            loadFromLocalStorage();

            const rawStored = localStorage.getItem('vyapar_custom_excel_lines');

            // 1. TOP PRIORITY: If coming from PDF Studio with source=pdf_lines, import fresh PDF lines!
            if (isFromPdf && rawStored) {
                try {
                    const parsed = JSON.parse(rawStored);
                    if (loadPdfExtractedDataIntoDynamicRows(parsed)) {
                        // Clean the URL query param immediately so F5/Refresh doesn't reset or re-import
                        if (window.history && window.history.replaceState) {
                            const cleanUrl = window.location.pathname + (window.location.search.replace(/[?&]source=pdf_lines(&|$)/, '$1').replace(/[?&]$/, ''));
                            window.history.replaceState({}, document.title, cleanUrl || window.location.pathname);
                        }
                        toggleDynamicMode(true);
                        return;
                    }
                } catch (e) {
                    console.error('Error loading PDF custom lines:', e);
                }
            }

            // 2. PRIORITY 2: If we have existing saved dynamic spreadsheet rows or grouped cards in localStorage
            if (groupedProductCards.length > 0 || dynamicRows.length > 0) {
                renderDynamicRows();
                renderGroupedProductCards();
                refreshCategoryFilterTabs();
                toggleDynamicMode(true);
                return;
            }

            // 3. PRIORITY 3: If vyapar_custom_excel_lines is in storage even without query param
            if (rawStored) {
                try {
                    const parsed = JSON.parse(rawStored);
                    if (loadPdfExtractedDataIntoDynamicRows(parsed)) {
                        toggleDynamicMode(true);
                        return;
                    }
                } catch (e) {}
            }

            // 4. PRIORITY 4: Hydrate from server database job if available
            if (serverJobProducts && serverJobProducts.length > 0) {
                if (hydrateFromJobProducts(serverJobProducts)) {
                    renderDynamicRows();
                    renderGroupedProductCards();
                    refreshCategoryFilterTabs();
                    return;
                }
            }

            // 5. Default Fallback: Clean empty workspace! Never load fake UPVC sample rows!
            dynamicRows = [];
            groupedProductCards = [];
            renderDynamicRows();
            renderGroupedProductCards();
            refreshCategoryFilterTabs();
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

            // All 15 Column Selectors
            const colSelects = [
                'mapColName', 'mapColMrp', 'mapColRetail',
                'mapColSize', 'mapColCode', 'mapColHsn', 'mapColPack1', 'mapColPack2',
                'mapColCost', 'mapColCost2', 'mapColCost3', 'mapColStock', 'mapColCategory',
                'mapColDesc', 'mapColImage'
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
                hsn: -1,
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
                category: -1,
                desc: -1,
                image: -1
            };

            headerRow.forEach((col, idx) => {
                const c = String(col || '').toLowerCase().trim();
                const isSrNo = (c === 's.no' || c === 's.no.' || c === 'sr.no' || c === 'sr.no.' || c === 'sr no' || c === 'sl.no' || c === 'sl no' || c === 'no' || c === '#' || c === 'sn' || c === 's no');
                if (isSrNo) return; // Prevent serial numbers (1, 2) from becoming MRP, code or name!

                // Image URL
                if (selectedCols.image === -1 && (c.includes('image') || c.includes('photo') || c.includes('pic') || c.includes('img') || c.includes('url') || c.includes('link'))) {
                    selectedCols.image = idx;
                }
                // Product Code / SKU
                else if (selectedCols.code === -1 && (c.includes('item code') || c.includes('product code') || c.includes('item_code') || c.includes('sku') || c.includes('cat no') || c.includes('cat.no') || c.includes('art no') || c.includes('art.no') || c === 'code' || c === 'part no')) {
                    selectedCols.code = idx;
                }
                // HSN / SAC Code
                else if (selectedCols.hsn === -1 && (c.includes('hsn') || c.includes('sac'))) {
                    selectedCols.hsn = idx;
                }
                // Product Name
                else if (selectedCols.name === -1 && (c.includes('particular') || c.includes('item name') || c.includes('product name') || c === 'item' || c === 'product' || c.includes('material'))) {
                    selectedCols.name = idx;
                }
                // Description (if not already picked as product name)
                else if (selectedCols.desc === -1 && (c.includes('description') || c.includes('desc') || c.includes('specs') || c.includes('details'))) {
                    selectedCols.desc = idx;
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
                    const isSrNo = (c === 's.no' || c === 's.no.' || c === 'sr.no' || c === 'sr.no.' || c === 'sr no' || c === 'sl.no' || c === 'no' || c === '#' || c === 'sn');
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
            if (selectedCols.hsn !== -1 && document.getElementById('mapColHsn')) document.getElementById('mapColHsn').value = selectedCols.hsn;
            if (selectedCols.pack1 !== -1) document.getElementById('mapColPack1').value = selectedCols.pack1;
            if (selectedCols.pack2 !== -1) document.getElementById('mapColPack2').value = selectedCols.pack2;
            if (selectedCols.cost !== -1) document.getElementById('mapColCost').value = selectedCols.cost;
            if (selectedCols.cost2 !== -1) document.getElementById('mapColCost2').value = selectedCols.cost2;
            if (selectedCols.cost3 !== -1) document.getElementById('mapColCost3').value = selectedCols.cost3;
            if (selectedCols.stock !== -1) document.getElementById('mapColStock').value = selectedCols.stock;
            if (selectedCols.category !== -1) document.getElementById('mapColCategory').value = selectedCols.category;
            if (selectedCols.desc !== -1 && document.getElementById('mapColDesc')) document.getElementById('mapColDesc').value = selectedCols.desc;
            if (selectedCols.image !== -1 && document.getElementById('mapColImage')) document.getElementById('mapColImage').value = selectedCols.image;

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
            const hsnIdx = parseInt(document.getElementById('mapColHsn')?.value ?? -1);
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
                const rawHsn = (hsnIdx !== -1 && row[hsnIdx] !== undefined && String(row[hsnIdx]).trim() !== '') ? String(row[hsnIdx]).trim() : '39174000';

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
                        <td class="p-2.5 font-mono text-[11px] text-indigo-700">${escapeHtml(rawHsn)}</td>
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
                previewHtml = `<tr><td colspan="10" class="p-4 text-center text-gray-400">Is row number par koi data nahi mila. "Data Starts At Row" number check karein.</td></tr>`;
            }

            tbody.innerHTML = previewHtml;
        }

        function applyExcelColumnMapping() {
            const nameIdx = parseInt(document.getElementById('mapColName')?.value ?? -1);
            const mrpIdx = parseInt(document.getElementById('mapColMrp')?.value ?? -1);
            const retailIdx = parseInt(document.getElementById('mapColRetail')?.value ?? -1);
            const sizeIdx = parseInt(document.getElementById('mapColSize')?.value ?? -1);
            const codeIdx = parseInt(document.getElementById('mapColCode')?.value ?? -1);
            const hsnIdx = parseInt(document.getElementById('mapColHsn')?.value ?? -1);
            const pack1Idx = parseInt(document.getElementById('mapColPack1')?.value ?? -1);
            const pack2Idx = parseInt(document.getElementById('mapColPack2')?.value ?? -1);
            const costIdx = parseInt(document.getElementById('mapColCost')?.value ?? -1);
            const cost2Idx = parseInt(document.getElementById('mapColCost2')?.value ?? -1);
            const cost3Idx = parseInt(document.getElementById('mapColCost3')?.value ?? -1);
            const stockIdx = parseInt(document.getElementById('mapColStock')?.value ?? -1);
            const catIdx = parseInt(document.getElementById('mapColCategory')?.value ?? -1);
            const descIdx = parseInt(document.getElementById('mapColDesc')?.value ?? -1);
            const imageIdx = parseInt(document.getElementById('mapColImage')?.value ?? -1);
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
                const rawHsn = (hsnIdx !== -1 && row[hsnIdx] !== undefined && String(row[hsnIdx]).trim() !== '') ? String(row[hsnIdx]).trim() : '39174000';
                const rawDesc = (descIdx !== -1 && row[descIdx] !== undefined) ? String(row[descIdx]).trim() : '';
                const rawImage = (imageIdx !== -1 && row[imageIdx] !== undefined) ? String(row[imageIdx]).trim() : '';

                let rawSize = (sizeIdx !== -1 && row[sizeIdx] !== undefined) ? String(row[sizeIdx]).trim() : '';
                if (!rawSize) {
                    const sizeMatch = rawName.match(/\b(\d+[\*xX/]\d+[\*xX/]\d+|\d+[\*xX/]\d+|\d+(\.\d+)?\s*(mm|inch|")|\d+\/\d+(")?)\b/i);
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
                    hsn_code: rawHsn,
                    product_name: rawName,
                    description: rawDesc,
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
                    image_url: rawImage,
                    asset_url: rawImage
                });
            }

            if (newRows.length === 0) {
                alert('Chune gaye mapping ke anusaar koi valid rows nahi mili.');
                return;
            }

            dynamicRows = newRows;
            if (typeof deduplicateDynamicRowsAgainstCards === 'function') {
                deduplicateDynamicRowsAgainstCards();
            }
            saveToLocalStorage();
            autoSaveDraftToBackend();
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

            alert(`🎉 Success! Excel sheet se ${newRows.length} products sahi MRP, Selling Price, Code, Packing aur Category ke sath map ho gaye hain!\n\n💡 Tip: Ab aap seedhe "⚡ Auto-Distribute Cards" dabakar sabhi 291 products ko unki Product Families (Elbow, Tee, Pipe etc.) me alag-alag Cards me baat sakte hain!`);
        }

        function showToastNotification(message) {
            let toast = document.getElementById('vyaparLiveToast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'vyaparLiveToast';
                toast.className = 'fixed top-20 right-6 z-[99999] max-w-md bg-gray-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-emerald-500/50 text-xs font-bold flex items-center gap-3 transform transition-all duration-300 translate-y-[-20px] opacity-0 pointer-events-none';
                document.body.appendChild(toast);
            }
            toast.innerHTML = `<i class="fa-solid fa-circle-check text-emerald-400 text-base shrink-0"></i><span class="flex-1">${message}</span>`;
            toast.classList.remove('translate-y-[-20px]', 'opacity-0', 'pointer-events-none');
            toast.classList.add('translate-y-0', 'opacity-100');
            setTimeout(() => {
                toast.classList.add('translate-y-[-20px]', 'opacity-0', 'pointer-events-none');
                toast.classList.remove('translate-y-0', 'opacity-100');
            }, 4500);
        }

        function extractBaseFamilyName(fullName, currentSize) {
            let str = (fullName || '').trim();
            if (currentSize && currentSize.toLowerCase() !== 'standard') {
                const escaped = currentSize.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                str = str.replace(new RegExp('\\b' + escaped + '\\b', 'gi'), ' ');
            }
            // Standardize schedules: e.g. 40SCH -> 40 SCH, 80SCH -> 80 SCH
            str = str.replace(/(\d+)\s*SCH\b/gi, '$1 SCH');
            str = str.replace(/SCH\s*(\d+)\b/gi, '$1 SCH');

            // 1. Triple & Double dimensions: 110*75*50, 25X20, 20*15, 25 x 20mm, 1 x 1/2"
            str = str.replace(/\b\d+(\.\d+)?\s*[*xX/]\s*\d+(\.\d+)?\s*[*xX/]\s*\d+(\.\d+)?(\s*mm)?\b/gi, ' ');
            str = str.replace(/\b\d+(\.\d+)?\s*[*xX]\s*\d+(\.\d+)?(\s*mm)?\b/gi, ' ');
            str = str.replace(/\b\d+(\.\d+)?\s*[*xX]\s*\d+\/\d+(")?\b/gi, ' ');
            // 2. Fractional inch: 1/2", 3/4", 1-1/4", 1 1/2"
            str = str.replace(/\b\d+\s*[-–]?\s*\d+\/\d+(")?\b/gi, ' ');
            str = str.replace(/\b\d+\/\d+(")?\b/gi, ' ');
            // 3. Simple mm / inch / single quote / double quote: 15MM, 20 mm, 1", 2"
            str = str.replace(/\b\d+(\.\d+)?\s*(mm|inch|")\b/gi, ' ');
            // 4. Standalone inches: e.g. 1" or 2"
            str = str.replace(/\b\d+(")\b/gi, ' ');

            let base = str.replace(/\s+/g, ' ').trim().replace(/[-–,\s*]+$/, '').trim();
            return base || fullName;
        }

        function isRowAlreadyMappedInCards(r) {
            if (!groupedProductCards || groupedProductCards.length === 0) return false;
            const rId = r.id;
            const rCode = (r.product_code || '').trim().toLowerCase();
            const rFamily = (typeof extractBaseFamilyName === 'function' ? extractBaseFamilyName(r.product_name, r.size) : (r.product_name || '')).trim().toLowerCase();
            const rName = (r.product_name || '').trim().toLowerCase();
            const rSize = (r.size || 'Standard').trim().toLowerCase();
            const rCat = (r.group_type || r.category || '').trim().toLowerCase();

            const rFamilyNorm = rFamily.replace(/[^a-z0-9]/g, '');
            const rNameNorm = rName.replace(/[^a-z0-9]/g, '');
            const rSizeNorm = rSize.replace(/[^a-z0-9]/g, '');

            for (const card of groupedProductCards) {
                const cardCat = (card.category || '').trim().toLowerCase();
                const cardParent = (card.parent_name || '').trim().toLowerCase();
                const cardParentNorm = cardParent.replace(/[^a-z0-9]/g, '');
                const catMatches = (!rCat || !cardCat || rCat === cardCat || rCat.includes(cardCat) || cardCat.includes(rCat));

                for (const v of (card.variants || [])) {
                    // Check 1: direct variant id match
                    if (rId && v.id && rId === v.id) return true;

                    // Check 2: exact SKU code match if not placeholder
                    if (rCode && !rCode.startsWith('itm-') && (v.product_code || '').trim().toLowerCase() === rCode) {
                        return true;
                    }

                    // Check 3: Family/Name + Size match in same category
                    if (catMatches) {
                        const vSize = (v.size || 'Standard').trim().toLowerCase();
                        const vSizeNorm = vSize.replace(/[^a-z0-9]/g, '');
                        if (vSizeNorm === rSizeNorm && rSizeNorm !== '') {
                            if (cardParentNorm === rFamilyNorm || cardParentNorm === rNameNorm) {
                                return true;
                            }
                        }
                    }
                }
            }
            return false;
        }

        function deduplicateDynamicRowsAgainstCards() {
            if (!groupedProductCards || groupedProductCards.length === 0 || !dynamicRows || dynamicRows.length === 0) return;
            const origLength = dynamicRows.length;
            dynamicRows = dynamicRows.filter(r => !isRowAlreadyMappedInCards(r));
            if (dynamicRows.length !== origLength) {
                saveToLocalStorage();
                if (typeof autoSaveDraftToBackend === 'function') autoSaveDraftToBackend();
            }
        }

        function clearOnlyPendingRows() {
            if (dynamicRows.length === 0) {
                alert('Pending rows table pehle se hi bilkul khali hai.');
                return;
            }
            if (confirm(`Kya aap bache huye ${dynamicRows.length} pending rows ko table se hatana chahte hain?\n\n✨ Note: Aapke dwara banaye gaye ${groupedProductCards.length} Product Cards surakshit rahenge!`)) {
                dynamicRows = [];
                localStorage.removeItem('vyapar_dynamic_rows');
                localStorage.removeItem('vyapar_custom_excel_lines');
                renderDynamicRows();
                refreshCategoryFilterTabs();
                clearDynamicRowSelection();
                saveToLocalStorage();
                if (typeof autoSaveDraftToBackend === 'function') autoSaveDraftToBackend();
                showToastNotification('🧹 Pending list saaf ho gayi! Aapke Product Cards bilkul surakshit hain.');
            }
        }

        function autoGroupAllDynamicRowsIntoCards() {
            deduplicateDynamicRowsAgainstCards();
            if (dynamicRows.length === 0) {
                alert('Table me auto-distribute karne ke liye koi pending rows nahi hain. Pehle Excel import karein ya "+ Add Line" dabayein.');
                return;
            }

            const targetRows = (activeCategory === 'ALL')
                ? [...dynamicRows]
                : dynamicRows.filter(r => (r.group_type || r.category || 'General Hardware').trim().toLowerCase() === activeCategory.toLowerCase());

            if (targetRows.length === 0) {
                alert(`"${activeCategory}" category me koi pending unmapped row nahi hai.`);
                return;
            }

            const isScoped = (activeCategory !== 'ALL');
            const promptTitle = isScoped
                ? `Kya aap sirf "${activeCategory}" category ke ${targetRows.length} items ko unki Product Family ke alag-alag Cards me distribute karna chahte hain?`
                : `Kya aap sabhi ${targetRows.length} items ko unki Product Family (Elbow, Pipe 40 SCH, Tee, Union, etc.) ke alag-alag Cards me distribute karna chahte hain?`;

            if (!confirm(promptTitle + `\n\n✨ Isse har product family ka apna alag Card ban jayega aur pending table se ye items hat jayenge!`)) {
                return;
            }

            const targetIds = new Set(targetRows.map(r => r.id));
            const familyMap = new Map();

            targetRows.forEach(row => {
                const familyKey = extractBaseFamilyName(row.product_name, row.size);
                if (!familyMap.has(familyKey)) {
                    familyMap.set(familyKey, []);
                }
                familyMap.get(familyKey).push(row);
            });

            let newCardsCreated = 0;
            familyMap.forEach((rowsInFamily, familyName) => {
                const firstRow = rowsInFamily[0];
                const assignedImg = rowsInFamily.find(r => r.image_url)?.image_url || '';
                const assignedAsset = rowsInFamily.find(r => r.asset_url)?.asset_url || (assignedImg ? ('/' + assignedImg.replace(/^\//, '')) : '');
                const rowWithDesc = rowsInFamily.find(r => r.description && r.description.trim().length > 0);
                const assignedDesc = rowWithDesc ? rowWithDesc.description.trim() : '';

                const newCard = {
                    card_id: groupedCardNextId++,
                    parent_name: familyName,
                    category: firstRow.category || firstRow.group_type || (isScoped ? activeCategory : 'General Hardware'),
                    description: assignedDesc,
                    image_url: assignedImg,
                    asset_url: assignedAsset,
                    variants: rowsInFamily.map(r => ({
                        id: r.id,
                        product_code: r.product_code || '',
                        hsn_code: r.hsn_code || '39174000',
                        size: r.size || 'Standard',
                        description: r.description || '',
                        packing_1: r.packing_1 || '',
                        packing_2: r.packing_2 || '',
                        mrp: r.mrp,
                        purchase_cost: r.purchase_cost,
                        cost_price_2: r.cost_price_2 || 0,
                        cost_price_3: r.cost_price_3 || 0,
                        retail_price: r.retail_price,
                        stock: (r.stock !== undefined && r.stock !== null && r.stock !== '') ? parseInt(r.stock) : 0
                    }))
                };

                groupedProductCards.push(newCard);
                newCardsCreated++;
            });

            // Remove only the distributed rows from dynamicRows!
            dynamicRows = dynamicRows.filter(r => !targetIds.has(r.id));

            clearDynamicRowSelection();
            renderDynamicRows();
            renderGroupedProductCards();
            refreshCategoryFilterTabs();
            updateFloatingBatchBar();
            saveToLocalStorage();
            autoSaveDraftToBackend();

            showToastNotification(`🚀 Zabardast! ${targetRows.length} items ${familyMap.size} alag-alag Cards me distribute ho gaye hain aur pending list se hat gaye!`);

            // Scroll down to cards section smoothly
            setTimeout(() => {
                document.getElementById('groupedCardsSection')?.scrollIntoView({ behavior: 'smooth' });
            }, 300);
        }

        function saveSelectedIntoCardAndRemove(specificImgUrl, specificAssetUrl) {
            let checkedIds = [];
            if (isDynamicMode) {
                checkedIds = Array.from(document.querySelectorAll('.dynamic-row-checkbox:checked')).map(cb => parseInt(cb.value));
            } else {
                checkedIds = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => parseInt(cb.value));
            }

            if (checkedIds.length === 0) {
                alert('Kripya pehle 1 ya usse zyada rows (jaise 4, 6 ya 8 sizes) select karein jinhe Card me save karna hai.');
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
            let parentTitle = extractBaseFamilyName(firstRow.product_name, firstRow.size);

            const assignedImg = specificImgUrl || selectedRows.find(r => r.image_url)?.image_url || preselectedImg || '';
            const assignedAsset = specificAssetUrl || selectedRows.find(r => r.asset_url)?.asset_url || (preselectedImg ? ('/' + preselectedImg.replace(/^\//, '')) : '');
            const rowWithDesc = selectedRows.find(r => r.description && r.description.trim().length > 0);
            const assignedDesc = rowWithDesc ? rowWithDesc.description.trim() : '';

            const newCard = {
                card_id: groupedCardNextId++,
                parent_name: parentTitle,
                category: firstRow.category || firstRow.group_type || 'General Hardware',
                description: assignedDesc,
                image_url: assignedImg,
                asset_url: assignedAsset,
                variants: selectedRows.map(r => ({
                    id: r.id,
                    product_code: r.product_code || '',
                    hsn_code: r.hsn_code || '39174000',
                    size: r.size || 'Standard',
                    description: r.description || '',
                    packing_1: r.packing_1 || '',
                    packing_2: r.packing_2 || '',
                    mrp: r.mrp,
                    purchase_cost: r.purchase_cost,
                    cost_price_2: r.cost_price_2 || 0,
                    cost_price_3: r.cost_price_3 || 0,
                    retail_price: r.retail_price,
                    stock: (r.stock !== undefined && r.stock !== null && r.stock !== '') ? parseInt(r.stock) : 0
                }))
            };

            groupedProductCards.push(newCard);

            // REMOVE SAVED ROWS FROM PENDING LIST SO USER'S WORKFLOW ADVANCES SMOOTHLY
            dynamicRows = dynamicRows.filter(r => !checkedIds.includes(r.id));

            clearDynamicRowSelection();
            renderDynamicRows();
            renderGroupedProductCards();
            refreshCategoryFilterTabs();
            updateFloatingBatchBar();
            saveToLocalStorage();
            autoSaveDraftToBackend();

            showToastNotification(`🎉 Shabaash! ${selectedRows.length} items "${parentTitle}" card me save ho gaye aur pending table se hat gaye! Ab table me bache ${dynamicRows.length} items me se "Next 6" select karein.`);
        }

        function groupSelectedRowsIntoCard() {
            saveSelectedIntoCardAndRemove();
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
                            <td class="p-2 font-mono">
                                <input type="text" value="${escapeHtml(v.hsn_code || '39174000')}" placeholder="39174000" oninput="updateGroupedVariantField(${c.card_id}, ${v.id}, 'hsn_code', this.value)" class="w-20 text-[10px] font-mono text-indigo-700 border border-transparent hover:border-gray-300 focus:border-indigo-600 rounded p-1 bg-transparent" title="HSN Code">
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
                                <input type="number" value="${(v.stock !== undefined && v.stock !== null && v.stock !== '') ? v.stock : 0}" oninput="updateGroupedVariantField(${c.card_id}, ${v.id}, 'stock', parseInt(this.value) || 0)" class="w-14 text-xs font-mono text-gray-700 border border-transparent hover:border-gray-300 focus:border-indigo-600 rounded p-1 bg-transparent">
                            </td>
                            <td class="p-2 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" onclick="sendVariantBackToDynamicRows(${c.card_id}, ${v.id})" class="h-6 px-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 text-[10px] font-bold border border-amber-200 flex items-center gap-1 transition" title="Card se nikal kar wapas Table List me bhejein (taaki alag card bana sakein)">
                                        <i class="fa-solid fa-arrow-turn-up text-amber-600"></i>
                                        <span>List me bhejein</span>
                                    </button>
                                    <button type="button" onclick="moveVariantToNewCard(${c.card_id}, ${v.id})" class="h-6 px-2 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-800 text-[10px] font-bold border border-indigo-200 flex items-center gap-1 transition" title="Is size ka alag se naya card banayein (Company / Brand ke hisab se)">
                                        <i class="fa-solid fa-plus text-indigo-600"></i>
                                        <span>Naya Card</span>
                                    </button>
                                    <button type="button" onclick="openMoveVariantToCardModal(${c.card_id}, ${v.id})" class="h-6 px-2 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-800 text-[10px] font-bold border border-purple-200 flex items-center gap-1 transition" title="Is size ko kisi doosre card me transfer karein">
                                        <i class="fa-solid fa-arrow-right-to-bracket text-purple-600"></i>
                                        <span>Dusre Card</span>
                                    </button>
                                    <button type="button" onclick="deleteVariantFromCard(${c.card_id}, ${v.id})" class="h-6 w-6 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition inline-flex items-center justify-center text-[10px]" title="Permanently delete this size"><i class="fa-solid fa-trash-can"></i></button>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                const cardCategory = c.category || 'General Hardware';
                const cardSearch = `${c.parent_name} ${cardCategory} ${c.variants.map(v => (v.product_code || '') + ' ' + (v.size || '')).join(' ')}`.toLowerCase();

                html += `
                    <div class="bg-white rounded-3xl border-2 border-indigo-100 p-5 shadow-sm hover:shadow-md transition space-y-4" id="grouped_card_box_${c.card_id}" data-category="${escapeHtml(cardCategory)}" data-search="${escapeHtml(cardSearch)}">
                        <!-- Card Top Bar: Checkbox, Badge & Merge Card Action -->
                        <div class="flex items-center justify-between pb-2.5 border-b border-gray-100 flex-wrap gap-2 bg-slate-50/60 -mx-5 -mt-5 p-4 rounded-t-3xl border-b border-indigo-50">
                            <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                <input type="checkbox" class="grouped-card-checkbox h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" value="${c.card_id}" onchange="updateGroupedCardSelectionBar()" title="Select this card to merge">
                                <span class="text-xs font-black text-gray-800">Card #${c.card_id}: ${escapeHtml(c.parent_name)}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-indigo-100 text-indigo-700 font-bold border border-indigo-200">${c.variants.length} Sizes</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="openMergeCardModal(${c.card_id})" class="px-3 py-1.5 rounded-xl bg-white hover:bg-indigo-600 hover:text-white text-indigo-700 font-extrabold text-[11px] border border-indigo-200 flex items-center gap-1.5 transition active:scale-95 shadow-2xs" title="Is card ke sabhi sizes ko kisi doosre card me merge karein">
                                    <i class="fa-solid fa-code-merge text-indigo-500"></i>
                                    <span>🔗 Merge With Another Card</span>
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-4 pb-3 border-b border-gray-100">
                            <div class="flex items-center gap-4 flex-1 min-w-[280px]">
                                <!-- Product Photo Slot -->
                                <div class="relative group h-20 w-20 bg-gray-50 rounded-2xl border-2 border-dashed border-indigo-200 hover:border-indigo-500 p-1 flex items-center justify-center overflow-hidden cursor-pointer transition shrink-0" onclick="openGalleryDrawer('grouped_card', ${c.card_id})" title="Click to choose image from Photo Bank" id="grouped_thumb_${c.card_id}">
                                    ${imgThumb}
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-[10px] font-bold transition rounded-xl">
                                        Change
                                    </div>
                                </div>

                                <div class="flex-1 min-w-[280px] space-y-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[10px] font-bold text-gray-400 uppercase">Category:</span>
                                            <input list="allCategoriesList" value="${escapeHtml(cardCategory)}" onchange="updateGroupedCardField(${c.card_id}, 'category', this.value); refreshCategoryFilterTabs();" class="text-xs font-bold py-1 px-2.5 rounded-xl border border-indigo-200 bg-white text-indigo-900 focus:ring-2 focus:ring-indigo-500 w-44 shadow-2xs" placeholder="Category">
                                        </div>
                                        <span class="px-2.5 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 font-extrabold text-[10px] border border-indigo-100 flex items-center gap-1">
                                            <i class="fa-solid fa-layer-group text-[9px]"></i> ${c.variants.length} Sizes Included
                                        </span>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-0.5">Product Title / Name (Click to edit)</label>
                                        <input type="text" value="${escapeHtml(c.parent_name)}" oninput="updateGroupedCardField(${c.card_id}, 'parent_name', this.value)" class="w-full text-sm font-black text-gray-900 border border-gray-200 hover:border-indigo-300 focus:border-indigo-600 focus:bg-white rounded-xl px-3 py-1.5 transition bg-slate-50/70" placeholder="e.g. Yorker Measuring Tape / UPVC 90° Elbow">
                                    </div>
                                    <div>
                                        <div class="flex items-center justify-between mb-0.5">
                                            <label class="text-[10px] font-bold text-indigo-700 uppercase tracking-wider flex items-center gap-1">
                                                <i class="fa-solid fa-align-left text-indigo-500"></i> Product Description / Specifications / Series:
                                            </label>
                                            <button type="button" onclick="aiGenerateSingleCardDesc(${c.card_id})" class="text-[10px] text-indigo-700 hover:text-indigo-900 font-extrabold flex items-center gap-1 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 px-2 py-0.5 rounded-lg transition active:scale-95" title="Is product family ke hisab se AI description generate karein">
                                                <i class="fa-solid fa-wand-magic-sparkles text-amber-500"></i> <span>AI Generate</span>
                                            </button>
                                        </div>
                                        <textarea rows="2" id="card_desc_input_${c.card_id}" oninput="updateGroupedCardField(${c.card_id}, 'description', this.value)" class="w-full text-xs text-gray-800 border border-indigo-100 hover:border-indigo-300 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-500 rounded-xl p-2.5 bg-indigo-50/20 focus:bg-white transition resize-y" placeholder="e.g. Heavy Duty Auto Stop Steel Tape / Schedule 40 ISI standard high pressure fitting...">${escapeHtml(c.description || '')}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" onclick="openGalleryDrawer('grouped_card', ${c.card_id})" class="px-3.5 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-extrabold border border-indigo-200 flex items-center gap-1.5 transition">
                                    <i class="fa-solid fa-images"></i>
                                    <span>Pick Photo</span>
                                </button>
                                <button type="button" onclick="saveSingleCardChanges(${c.card_id})" id="btn_save_card_${c.card_id}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black shadow-md shadow-emerald-600/30 flex items-center gap-1.5 transition active:scale-95" title="Save this card with all edited sizes & rates">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    <span>Save Card</span>
                                </button>
                                <button type="button" onclick="ungroupCard(${c.card_id})" class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-black border border-amber-200 flex items-center gap-1.5 transition" title="Is card ke sabhi items ko wapas table listing me bhej do taaki company-wise alag cards bana sakein">
                                    <i class="fa-solid fa-arrow-rotate-left text-amber-600"></i>
                                    <span>Wapas List Me Kholein (Ungroup)</span>
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
                                        <th class="p-2.5">HSN</th>
                                        <th class="p-2.5">Packing</th>
                                        <th class="p-2.5">MRP</th>
                                        <th class="p-2.5">Cost Price</th>
                                        <th class="p-2.5">Selling Price</th>
                                        <th class="p-2.5">Stock</th>
                                        <th class="p-2.5 text-right min-w-[210px]">Actions (Move / Delete)</th>
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
                                <button type="button" onclick="saveSingleCardChanges(${c.card_id})" class="px-4 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs flex items-center gap-1.5 transition">
                                    <i class="fa-solid fa-check"></i>
                                    <span>Save Changes to Card</span>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            grid.innerHTML = html;
        }

        function saveSingleCardChanges(cardId) {
            const card = groupedProductCards.find(c => c.card_id === cardId);
            if (!card) return;

            saveToLocalStorage();
            if (typeof autoSaveDraftToBackend === 'function') autoSaveDraftToBackend();

            const btn = document.getElementById(`btn_save_card_${cardId}`);
            if (btn) {
                const orig = btn.innerHTML;
                btn.innerHTML = `<i class="fa-solid fa-circle-check text-white"></i> <span>Saved ✓</span>`;
                btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
                btn.classList.add('bg-emerald-700');
                setTimeout(() => {
                    btn.innerHTML = orig;
                    btn.classList.remove('bg-emerald-700');
                    btn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
                }, 2000);
            }
            if (typeof showToast === 'function') {
                showToast(`Card "${card.parent_name}" saved successfully!`, 'success');
            }
        }

        function updateGroupedCardField(cardId, field, val) {
            const card = groupedProductCards.find(c => c.card_id === cardId);
            if (card) {
                card[field] = val;
                saveToLocalStorage();
                if (typeof autoSaveDraftToBackend === 'function') autoSaveDraftToBackend();
            }
        }

        function updateGroupedVariantField(cardId, variantId, field, val) {
            const card = groupedProductCards.find(c => c.card_id === cardId);
            if (card) {
                const v = card.variants.find(item => item.id === variantId);
                if (v) {
                    v[field] = val;
                    saveToLocalStorage();
                    if (typeof autoSaveDraftToBackend === 'function') autoSaveDraftToBackend();
                }
            }
        }

        function deleteVariantFromCard(cardId, variantId) {
            const card = groupedProductCards.find(c => c.card_id === cardId);
            if (!card) return;
            const v = card.variants.find(item => item.id === variantId);
            const sizeName = v ? (v.size || v.product_code || 'variant') : 'variant';

            if (!confirm(`Kya aap "${sizeName}" size ko PERMANENTLY delete karna chahte hain?\n\nTip: Agar iska alag card banana hai, toh "List me bhejein" dabayein taaki ye table me wapas aa jaye!`)) {
                return;
            }

            card.variants = card.variants.filter(v => v.id !== variantId);
            if (card.variants.length === 0) {
                groupedProductCards = groupedProductCards.filter(c => c.card_id !== cardId);
            }
            renderGroupedProductCards();
            saveToLocalStorage();
            autoSaveDraftToBackend();
            showToastNotification(`🗑️ Size "${sizeName}" delete ho gaya.`);
        }

        function sendVariantBackToDynamicRows(cardId, variantId) {
            const card = groupedProductCards.find(c => c.card_id === cardId);
            if (!card) return;

            const vIndex = card.variants.findIndex(item => item.id === variantId);
            if (vIndex === -1) return;

            const v = card.variants[vIndex];
            card.variants.splice(vIndex, 1);

            const rowName = (v.size && v.size !== 'Standard' && !card.parent_name.includes(v.size))
                ? `${card.parent_name} - ${v.size}`
                : card.parent_name;

            dynamicRows.unshift({
                id: dynamicRowNextId++,
                product_code: v.product_code || '',
                hsn_code: v.hsn_code || '39174000',
                product_name: rowName,
                description: v.description || card.description || '',
                size: v.size || 'Standard',
                packing_1: v.packing_1 || '',
                packing_2: v.packing_2 || '',
                group_type: card.category || 'General Hardware',
                category: card.category || 'General Hardware',
                mrp: v.mrp,
                purchase_cost: v.purchase_cost,
                cost_price_2: v.cost_price_2 || 0,
                cost_price_3: v.cost_price_3 || 0,
                retail_price: v.retail_price,
                stock: (v.stock !== undefined && v.stock !== null && v.stock !== '') ? parseInt(v.stock) : 100,
                image_url: card.image_url || '',
                asset_url: card.asset_url || ''
            });

            if (card.variants.length === 0) {
                groupedProductCards = groupedProductCards.filter(c => c.card_id !== cardId);
            }

            renderDynamicRows();
            renderGroupedProductCards();
            refreshCategoryFilterTabs();
            updateFloatingBatchBar();
            saveToLocalStorage();
            autoSaveDraftToBackend();

            showToastNotification(`↩️ Item "${v.size || v.product_code}" wapas Table List me aa gaya! Ab aap iska alag card bana sakte hain.`);
        }

        function moveVariantToNewCard(cardId, variantId) {
            const card = groupedProductCards.find(c => c.card_id === cardId);
            if (!card) return;

            const vIndex = card.variants.findIndex(item => item.id === variantId);
            if (vIndex === -1) return;

            const v = card.variants[vIndex];
            const sizeLabel = v.size || v.product_code || '';
            const suggestedTitle = prompt('Naye Card ka Title / Company Name dalein:', `${card.parent_name} (${sizeLabel})`.trim());
            if (suggestedTitle === null) return; // User cancelled

            card.variants.splice(vIndex, 1);

            const newCard = {
                card_id: groupedCardNextId++,
                parent_name: suggestedTitle.trim() || `${card.parent_name} (${sizeLabel})`.trim(),
                category: card.category || 'General Hardware',
                description: v.description || card.description || '',
                image_url: card.image_url || '',
                asset_url: card.asset_url || '',
                variants: [v]
            };

            groupedProductCards.push(newCard);

            if (card.variants.length === 0) {
                groupedProductCards = groupedProductCards.filter(c => c.card_id !== cardId);
            }

            renderDynamicRows();
            renderGroupedProductCards();
            refreshCategoryFilterTabs();
            saveToLocalStorage();
            autoSaveDraftToBackend();

            showToastNotification(`✨ Naya card "${newCard.parent_name}" ban gaya!`);
        }

        function ungroupCard(cardId) {
            const card = groupedProductCards.find(c => c.card_id === cardId);
            if (!card) return;

            if (!confirm(`Kya aap "${card.parent_name}" card ko dismantle karke iske sabhi ${card.variants.length} items wapas Table Listing me bhejna chahte hain?\n\nIsse ye sabhi items wapas table me aa jayenge aur aap inke alag-alag company cards bana sakenge.`)) {
                return;
            }

            card.variants.forEach(v => {
                dynamicRows.unshift({
                    id: dynamicRowNextId++,
                    product_code: v.product_code || '',
                    hsn_code: v.hsn_code || '39174000',
                    product_name: `${card.parent_name} ${v.size === 'Standard' ? '' : v.size}`.trim() || card.parent_name,
                    description: card.description || v.description || '',
                    size: v.size || 'Standard',
                    packing_1: v.packing_1 || '',
                    packing_2: v.packing_2 || '',
                    group_type: card.category,
                    category: card.category,
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
            saveToLocalStorage();
            autoSaveDraftToBackend();
            showToastNotification(`↩️ "${card.parent_name}" ke sabhi items wapas Table Listing me bhej diye gaye!`);
        }

        function addVariantToCard(cardId) {
            const card = groupedProductCards.find(c => c.card_id === cardId);
            if (!card) return;
            const lastVar = card.variants[card.variants.length - 1];
            const newVariant = {
                id: dynamicRowNextId++,
                product_code: '',
                hsn_code: lastVar ? (lastVar.hsn_code || '39174000') : '39174000',
                size: '',
                packing_1: lastVar ? lastVar.packing_1 : '',
                packing_2: lastVar ? lastVar.packing_2 : '',
                mrp: lastVar ? lastVar.mrp : 100,
                purchase_cost: lastVar ? lastVar.purchase_cost : 60,
                cost_price_2: lastVar ? (lastVar.cost_price_2 || 0) : 0,
                cost_price_3: lastVar ? (lastVar.cost_price_3 || 0) : 0,
                retail_price: lastVar ? lastVar.retail_price : 85,
                stock: 0
            };
            card.variants.push(newVariant);
            renderGroupedProductCards();
            saveToLocalStorage();
            autoSaveDraftToBackend();
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
                    hsn_code: r.hsn_code || '39174000',
                    size: r.size || 'Standard',
                    packing_1: r.packing_1 || '',
                    packing_2: r.packing_2 || '',
                    mrp: r.mrp,
                    purchase_cost: r.purchase_cost,
                    cost_price_2: r.cost_price_2 || 0,
                    cost_price_3: r.cost_price_3 || 0,
                    retail_price: r.retail_price,
                    stock: (r.stock !== undefined && r.stock !== null && r.stock !== '') ? parseInt(r.stock) : 0
                });
            });

            // Remove transferred rows from flat table
            dynamicRows = dynamicRows.filter(r => !checkedIds.includes(r.id));

            clearDynamicRowSelection();
            renderDynamicRows();
            renderGroupedProductCards();
            refreshCategoryFilterTabs();
            updateFloatingBatchBar();
            saveToLocalStorage();
            autoSaveDraftToBackend();

            alert(`🎉 Success! ${rowsToAdd.length} row(s) ko "${card.parent_name}" card me jod diya gaya hai.`);
        }

        function deleteGroupedCard(cardId) {
            if (!confirm('Kya aap is Grouped Product Card ko delete karna chahte hain?')) return;
            groupedProductCards = groupedProductCards.filter(c => c.card_id !== cardId);
            renderGroupedProductCards();
            saveToLocalStorage();
            autoSaveDraftToBackend();
        }

        // ==========================================
        // 🔗 CARD MERGING & VARIANT TRANSFER LOGIC
        // ==========================================
        function updateGroupedCardSelectionBar() {
            const checkedBoxes = document.querySelectorAll('.grouped-card-checkbox:checked');
            const bar = document.getElementById('groupedCardsBatchBar');
            const countText = document.getElementById('groupedCardsSelectedCountText');
            if (!bar) return;

            if (checkedBoxes.length >= 2) {
                bar.classList.remove('hidden');
                if (countText) countText.innerText = `${checkedBoxes.length} Cards Selected`;
            } else {
                bar.classList.add('hidden');
            }
        }

        function clearGroupedCardSelection() {
            document.querySelectorAll('.grouped-card-checkbox').forEach(cb => { cb.checked = false; });
            const bar = document.getElementById('groupedCardsBatchBar');
            if (bar) bar.classList.add('hidden');
        }

        function openMergeCardModal(sourceCardId) {
            const srcCard = groupedProductCards.find(c => c.card_id === sourceCardId);
            if (!srcCard) return;

            const otherCards = groupedProductCards.filter(c => c.card_id !== sourceCardId);
            if (otherCards.length === 0) {
                alert('Merge karne ke liye kam se kam 2 cards hone zaroori hain.');
                return;
            }

            const inputSrc = document.getElementById('mergeSourceCardId');
            const titleEl = document.getElementById('mergeSourceCardTitle');
            const detailsEl = document.getElementById('mergeSourceCardDetails');
            const selectEl = document.getElementById('mergeTargetCardSelect');

            if (inputSrc) inputSrc.value = sourceCardId;
            if (titleEl) titleEl.innerText = `Card #${srcCard.card_id}: ${srcCard.parent_name}`;
            if (detailsEl) {
                const sizesStr = srcCard.variants.map(v => v.size || 'Standard').join(', ');
                detailsEl.innerText = `Category: ${srcCard.category || 'General'} • ${srcCard.variants.length} Sizes: [${sizesStr}]`;
            }

            if (selectEl) {
                selectEl.innerHTML = otherCards.map(c => {
                    const sizes = c.variants.map(v => v.size || 'Standard').join(', ');
                    return `<option value="${c.card_id}">Card #${c.card_id}: ${escapeHtml(c.parent_name)} (${escapeHtml(c.category || 'General')}) — [${sizes}]</option>`;
                }).join('');
            }

            const modal = document.getElementById('mergeCardsModal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeMergeCardsModal() {
            const modal = document.getElementById('mergeCardsModal');
            if (modal) modal.classList.add('hidden');
        }

        function openMergeSelectedCardsModal() {
            const checkedBoxes = Array.from(document.querySelectorAll('.grouped-card-checkbox:checked'));
            if (checkedBoxes.length < 2) {
                alert('Kam se kam 2 cards ke checkbox tick karein jinhe ek karna hai.');
                return;
            }

            const selectedIds = checkedBoxes.map(cb => parseInt(cb.value));
            const firstId = selectedIds[0];
            const firstCard = groupedProductCards.find(c => c.card_id === firstId);

            const inputSrc = document.getElementById('mergeSourceCardId');
            const titleEl = document.getElementById('mergeSourceCardTitle');
            const detailsEl = document.getElementById('mergeSourceCardDetails');
            const selectEl = document.getElementById('mergeTargetCardSelect');

            if (inputSrc) inputSrc.value = selectedIds.join(',');
            if (titleEl) titleEl.innerText = `${selectedIds.length} Selected Cards Ek Sath Merge Honge`;
            if (detailsEl) {
                detailsEl.innerText = `In sabhi ${selectedIds.length} cards ke sizes ko neeche chune gaye Destination Card me jodh diya jayega.`;
            }

            if (selectEl) {
                // Populate with the checked cards so user chooses which one is the master
                selectEl.innerHTML = selectedIds.map(id => {
                    const c = groupedProductCards.find(card => card.card_id === id);
                    if (!c) return '';
                    const sizes = c.variants.map(v => v.size || 'Standard').join(', ');
                    return `<option value="${c.card_id}">Card #${c.card_id}: ${escapeHtml(c.parent_name)} (Is card me merge karein) — [${sizes}]</option>`;
                }).join('');
            }

            const modal = document.getElementById('mergeCardsModal');
            if (modal) modal.classList.remove('hidden');
        }

        function confirmMergeCards() {
            const rawSrc = document.getElementById('mergeSourceCardId')?.value;
            const targetCardId = parseInt(document.getElementById('mergeTargetCardSelect')?.value);

            if (!rawSrc || isNaN(targetCardId)) {
                alert('Target card chunein.');
                return;
            }

            const targetCard = groupedProductCards.find(c => c.card_id === targetCardId);
            if (!targetCard) {
                alert('Destination card nahi mila.');
                return;
            }

            let sourceIds = [];
            if (rawSrc.includes(',')) {
                sourceIds = rawSrc.split(',').map(s => parseInt(s.trim())).filter(id => id !== targetCardId);
            } else {
                sourceIds = [parseInt(rawSrc)].filter(id => id !== targetCardId);
            }

            let totalMovedVariants = 0;
            sourceIds.forEach(sId => {
                const sCard = groupedProductCards.find(c => c.card_id === sId);
                if (sCard) {
                    sCard.variants.forEach(v => {
                        targetCard.variants.push(v);
                        totalMovedVariants++;
                    });
                    // Preserve photo if target doesn't have one
                    if (!targetCard.image_url && sCard.image_url) {
                        targetCard.image_url = sCard.image_url;
                        targetCard.asset_url = sCard.asset_url;
                    }
                }
            });

            // Remove merged source cards
            groupedProductCards = groupedProductCards.filter(c => !sourceIds.includes(c.card_id));

            clearGroupedCardSelection();
            closeMergeCardsModal();
            renderGroupedProductCards();
            refreshCategoryFilterTabs();
            saveToLocalStorage();
            autoSaveDraftToBackend();

            showToastNotification(`🎉 Success! ${totalMovedVariants} sizes ab "${targetCard.parent_name}" card me jud chuke hain!`);
            alert(`🎉 Cards Merge Ho Gaye!\n\n• ${totalMovedVariants} sizes safalta-purvak "${targetCard.parent_name}" me jud gaye hain.\n• Purane alag-alag duplicate cards saaf ho gaye.`);
        }

        function openMoveVariantToCardModal(sourceCardId, variantId) {
            const srcCard = groupedProductCards.find(c => c.card_id === sourceCardId);
            if (!srcCard) return;
            const v = srcCard.variants.find(item => item.id === variantId);
            if (!v) return;

            const otherCards = groupedProductCards.filter(c => c.card_id !== sourceCardId);
            if (otherCards.length === 0) {
                alert('Transfer karne ke liye kam se kam ek doosra card hona zaroori hai.');
                return;
            }

            document.getElementById('moveVariantSourceCardId').value = sourceCardId;
            document.getElementById('moveVariantTargetVariantId').value = variantId;

            const infoEl = document.getElementById('moveVariantInfoText');
            const cardEl = document.getElementById('moveVariantSourceCardText');
            const selectEl = document.getElementById('moveVariantTargetSelect');

            if (infoEl) infoEl.innerText = `Size: ${v.size || 'Standard'} • Code: ${v.product_code || '-'} • MRP: ₹${v.mrp}`;
            if (cardEl) cardEl.innerText = `Abhi is card me hai: Card #${srcCard.card_id} (${srcCard.parent_name})`;

            if (selectEl) {
                selectEl.innerHTML = otherCards.map(c => {
                    return `<option value="${c.card_id}">Card #${c.card_id}: ${escapeHtml(c.parent_name)} (${escapeHtml(c.category || 'General')})</option>`;
                }).join('');
            }

            const modal = document.getElementById('moveVariantModal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeMoveVariantModal() {
            const modal = document.getElementById('moveVariantModal');
            if (modal) modal.classList.add('hidden');
        }

        function confirmMoveVariantToCard() {
            const sourceCardId = parseInt(document.getElementById('moveVariantSourceCardId')?.value);
            const variantId = parseInt(document.getElementById('moveVariantTargetVariantId')?.value);
            const targetCardId = parseInt(document.getElementById('moveVariantTargetSelect')?.value);

            if (isNaN(sourceCardId) || isNaN(variantId) || isNaN(targetCardId)) return;

            const srcCard = groupedProductCards.find(c => c.card_id === sourceCardId);
            const tgtCard = groupedProductCards.find(c => c.card_id === targetCardId);
            if (!srcCard || !tgtCard) return;

            const vIdx = srcCard.variants.findIndex(item => item.id === variantId);
            if (vIdx === -1) return;

            const v = srcCard.variants.splice(vIdx, 1)[0];
            tgtCard.variants.push(v);

            if (srcCard.variants.length === 0) {
                groupedProductCards = groupedProductCards.filter(c => c.card_id !== sourceCardId);
            }

            closeMoveVariantModal();
            renderGroupedProductCards();
            refreshCategoryFilterTabs();
            saveToLocalStorage();
            autoSaveDraftToBackend();

            showToastNotification(`✨ Size "${v.size}" ko Card #${targetCardId} ("${tgtCard.parent_name}") me transfer kar diya gaya!`);
        }

        function getSmartAiDescriptionForProduct(parentTitle, category) {
            const title = (parentTitle || '').toUpperCase();
            const cat = (category || '').toUpperCase();

            // 1. Measuring Tapes (Hardware & Tools) - STRICT check so plumbing never matches
            const isTape = /TAPE|MEASURING/i.test(title);
            if (isTape) {
                if (title.includes('AUTO') || title.includes('AS-')) return 'Heavy Duty Steel Strip Measuring Tape with Auto-Stop mechanism, shockproof rubber casing, ISI grade.';
                if (title.includes('NEO') || title.includes('NL-')) return 'Self-Locking Heavy Duty Measuring Tape with smooth recoil and dual metric/inch markings.';
                if (title.includes('PRO') || title.includes('PR-')) return 'Yorker Professional Heavy Duty Tape with high tensile steel blade and impact resistant body.';
                if (title.includes('PERFECT') || title.includes('PF-')) return 'Yorker Perfect Series compact measuring tape with belt clip and ergonomic grip.';
                if (title.includes('JUPITER') || title.includes('JP-')) return 'Jupiter Series Heavy Duty Measuring Tape with smooth blade payout and belt clip.';
                if (title.includes('BLACK') || title.includes('BW-')) return 'Black & White Contractor Grade high contrast measuring tape.';
                if (title.includes('FIRST CHOICE') || title.includes('FC-')) return 'First Choice Contractor Grade Measuring Tape with heavy duty spring mechanism.';
                return 'Yorker Heavy Duty Measuring Tape with high tensile steel blade, durable compact case and belt clip.';
            }

            // 2. SWR Drainage & Soil Fittings
            if (title.includes('SWR') || cat.includes('SWR') || title.includes('TRAP') || title.includes('DRAIN')) {
                if (title.includes('TRAP')) return 'SWR Drainage Nahani / P-Trap with deep water seal to prevent foul sewer gases and pests conforming to IS:14735.';
                if (title.includes('BEND') || title.includes('ELBOW')) return 'SWR 87.5° / 45° Drainage Bend with leak-proof rubber ring / solvent socket for soil and waste discharge.';
                if (title.includes('TEE') || title.includes('Y-TEE')) return 'SWR Single / Double Tee with inspection door cap for easy maintenance as per IS:14735.';
                if (title.includes('PIPE')) return 'SWR Ringfit / Selfit Soil, Waste & Rainwater Pipe manufactured with high-impact UV stabilized PVC as per IS:14735.';
                return 'SWR Soil, Waste & Rainwater drainage fitting with precision leak-proof socket as per IS:14735 standards.';
            }

            // 3. CPVC High Temp Hot & Cold Water Fittings
            if (title.includes('CPVC') || cat.includes('CPVC')) {
                if (title.includes('BRASS') || title.includes('MTA') || title.includes('FTA')) return 'CPVC Brass Threaded Fitting with heavy duty brass insert for secure, leak-proof tap and shower connection as per ASTM D2846.';
                if (title.includes('ELBOW') || title.includes('BEND')) return 'CPVC High Temperature & Pressure 90° Socket Elbow for hot and cold potable water plumbing as per ASTM D2846 (SDR 11).';
                if (title.includes('TEE')) return 'CPVC Equal / Reducing Tee fitting engineered for hot and cold pressurized water distribution as per ASTM D2846.';
                if (title.includes('PIPE')) return 'CPVC Copper Tube Size (CTS) High Pressure Water Pipe conforming to ASTM D2846 (SDR 11 Class 1) for hot & cold lines.';
                if (title.includes('UNION') || title.includes('VALVE')) return 'CPVC Precision Molded Valve / Union for reliable flow control and easy line maintenance.';
                return 'CPVC High Temperature & Pressure Fitting manufactured as per ASTM D2846 (SDR 11 Class 1) for potable hot and cold water distribution.';
            }

            // 4. UPVC Pressure Fittings & Pipes
            if (title.includes('UPVC') || cat.includes('UPVC') || cat.includes('PIPE') || title.includes('PIPE') || title.includes('ELBOW') || title.includes('TEE') || title.includes('SOCKET') || title.includes('COUPLER') || title.includes('MTA') || title.includes('FTA') || title.includes('UNION')) {
                if (title.includes('PIPE')) return 'Heavy Duty UPVC Lead-Free Pressure Pipe conforming to ASTM D1785 / Schedule 40 ISI standard for reliable potable water distribution.';
                if (title.includes('ELBOW') || title.includes('BEND')) return 'UPVC 90° / 45° Precision Socket Elbow for smooth high-pressure directional flow as per ASTM D2466 / Schedule 40.';
                if (title.includes('TEE')) return 'UPVC Equal / Reducing Socket Tee for balanced branch water distribution conforming to ASTM D2466 industrial grade.';
                if (title.includes('MTA') || title.includes('FTA') || title.includes('ADAPTOR')) return 'UPVC Threaded Adaptor with precision molded threads for leak-proof pipe to fixture connection.';
                if (title.includes('UNION')) return 'UPVC Socket Union for easy line disconnection, servicing and repair without pipe cutting.';
                if (title.includes('COUPLER') || title.includes('SOCKET')) return 'UPVC Straight Coupler / Socket with precision tight tolerance for jointing pressure pipes.';
                return 'Heavy Duty UPVC Lead-Free Pressure Pipe Fitting manufactured as per ASTM D2466 / Schedule 40 ISI standard.';
            }

            // 5. Solvents & Adhesive
            if (title.includes('SOLVENT') || title.includes('CEMENT') || cat.includes('SOLVENT') || title.includes('ADHESIVE')) {
                return 'Heavy Duty Fast Setting Solvent Cement for leak-proof high pressure bonding of PVC / CPVC / SWR piping systems.';
            }

            // 6. Hardware & Tools
            if (cat.includes('HARDWARE') || cat.includes('TOOL')) {
                return `Industrial Grade Heavy Duty ${parentTitle} engineered for contractor use, high durability, and precision fit.`;
            }

            // 7. Paints & Coatings
            if (cat.includes('PAINT') || title.includes('PAINT') || title.includes('DISTEMPER') || title.includes('EMULSION') || title.includes('PRIMER')) {
                return `High Performance ${parentTitle} with superior coverage, smooth finish, and weather-resistant durability.`;
            }

            // Fallback default
            return `Premium Quality ${parentTitle} engineered with high-grade durable materials conforming to standard industrial specifications.`;
        }

        function aiGenerateSingleCardDesc(cardId) {
            const card = groupedProductCards.find(c => c.card_id === cardId);
            if (!card) return;

            const smartDesc = getSmartAiDescriptionForProduct(card.parent_name, card.category);
            card.description = smartDesc;
            if (Array.isArray(card.variants)) {
                card.variants.forEach(v => { v.description = smartDesc; });
            }

            const inputEl = document.getElementById(`card_desc_input_${cardId}`);
            if (inputEl) inputEl.value = smartDesc;

            saveToLocalStorage();
            autoSaveDraftToBackend();
            showToastNotification(`✨ "${card.parent_name}" ke liye AI Description set ho gaya!`);
        }

        function aiFixAllCardDescriptions() {
            if (!groupedProductCards || groupedProductCards.length === 0) {
                alert('Pehle cards banayein ya auto-distribute karein.');
                return;
            }

            let fixedCount = 0;
            groupedProductCards.forEach(card => {
                const smartDesc = getSmartAiDescriptionForProduct(card.parent_name, card.category);
                card.description = smartDesc;
                if (Array.isArray(card.variants)) {
                    card.variants.forEach(v => { v.description = smartDesc; });
                }
                fixedCount++;
            });

            // Also update any dynamicRows if present
            if (Array.isArray(dynamicRows)) {
                dynamicRows.forEach(r => {
                    r.description = getSmartAiDescriptionForProduct(r.product_name, r.group_type || r.category);
                });
            }

            saveToLocalStorage();
            autoSaveDraftToBackend();
            renderGroupedProductCards();
            renderDynamicRows();

            alert(`🎉 Success!\n\n• Sabhi ${fixedCount} Cards ke Descriptions AI se unke sahi Product Family (UPVC, CPVC, SWR, Tape) ke hisab se set ho gaye hain!\n• Product Titles, Codes aur Rates BILKUL SAFE hain (koi change nahi kiya gaya).`);
            showToastNotification(`🎉 Sabhi ${fixedCount} Cards ke Descriptions AI se fix ho gaye!`);
        }

        async function autoFixAllExistingCards() {
            const btn = document.getElementById('btnAutoFixAllCards');
            const origHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> <span>Fixing All Cards...</span>`;
            }

            try {
                let fixedCardsCount = 0;
                let fixedVariantsCount = 0;
                let fixedRowsCount = 0;

                const tapeSeriesMap = {
                    'AS': { series: 'Auto Stop Series', desc: 'Heavy Duty Steel Strip Tape with Auto-Stop mechanism, shockproof rubber casing, ISI grade' },
                    'NL': { series: 'Neo Lock Series', desc: 'Self-Locking Heavy Duty Measuring Tape with smooth recoil and dual metric/inch markings' },
                    'PR': { series: 'Pro Model Heavy Duty', desc: 'Yorker Professional Heavy Duty Tape with high tensile steel blade and impact resistant body' },
                    'PF': { series: 'Perfect Series', desc: 'Yorker Perfect Series compact measuring tape with belt clip and ergonomic grip' },
                    'BW': { series: 'Black & White Series', desc: 'Black & White Contractor Grade high contrast measuring tape' },
                    'FC': { series: 'First Choice Series', desc: 'First Choice Contractor Grade Measuring Tape with heavy duty spring mechanism' },
                    'JP': { series: 'Jupiter Series', desc: 'Jupiter Series Heavy Duty Measuring Tape with smooth blade payout and belt clip' },
                    'YK': { series: 'Yorker Y-Series', desc: 'Yorker Standard Y-Series Measuring Tape with durable compact case' },
                    '2IN1': { series: 'Yorker 2-in-1 Dual Scale', desc: 'Yorker 2-in-1 Dual Scale Measuring Tape with metric and imperial units' },
                    '2in1': { series: 'Yorker 2-in-1 Dual Scale', desc: 'Yorker 2-in-1 Dual Scale Measuring Tape with metric and imperial units' },
                    'SS': { series: 'Stainless Steel Strip Series', desc: 'Stainless Steel Strip Measuring Tape with rustproof blade for heavy outdoor work' },
                    'SP': { series: 'High Tensile Spring Series', desc: 'Yorker High Tensile Spring Mechanism Measuring Tape with shockproof bumper' }
                };

                const inchMap = {
                    '15': '1/2"', '20': '3/4"', '25': '1"', '32': '1-1/4"',
                    '40': '1-1/2"', '50': '2"', '65': '2-1/2"', '80': '3"', '100': '4"'
                };

                // 1. Process all Grouped Product Cards safely
                if (Array.isArray(groupedProductCards)) {
                    groupedProductCards.forEach(card => {
                        let cardModified = false;
                        const parentUp = (card.parent_name || '').toUpperCase();
                        const catUp = (card.category || '').toUpperCase();
                        const cardText = `${parentUp} ${catUp} ${card.variants ? card.variants.map(v => v.product_code || '').join(' ') : ''}`.toUpperCase();

                        // SAFE Plumbing Check: NEVER treat plumbing as Tape!
                        const isPlumbing = /UPVC|CPVC|SWR|PIPE|ELBOW|TEE|SOCKET|MTA|FTA|TRAP|DRAIN|COUPLER|UNION|NIPPLE|BUSH|BEND|PLASTO/i.test(parentUp) || /PIPE|UPVC|CPVC|SWR|PLUMBING/i.test(catUp);

                        let isTape = false;
                        let matchedSeries = null;

                        if (!isPlumbing && (/TAPE|MEASURING/i.test(parentUp) || (catUp === 'HARDWARE & TOOLS' && /TAPE/i.test(cardText)))) {
                            isTape = true;
                            for (const [codeKey, sObj] of Object.entries(tapeSeriesMap)) {
                                const upperKey = codeKey.toUpperCase();
                                if (cardText.includes(upperKey + '-3016') || cardText.includes(upperKey + '-5019') || cardText.includes(sObj.series.toUpperCase())) {
                                    matchedSeries = sObj;
                                    break;
                                }
                            }
                        }

                        if (isTape) {
                            if (card.category !== 'Hardware & Tools') {
                                card.category = 'Hardware & Tools';
                                cardModified = true;
                            }

                            if (matchedSeries) {
                                const currentTitle = card.parent_name || '';
                                if (!currentTitle.includes(matchedSeries.series) || currentTitle.includes('-3016') || currentTitle.includes('-5019')) {
                                    card.parent_name = `Yorker Measuring Tape (${matchedSeries.series})`;
                                    cardModified = true;
                                }
                            }

                            card.description = matchedSeries ? matchedSeries.desc : 'Heavy Duty Measuring Tape with high tensile steel blade and shockproof rubber casing.';
                            cardModified = true;

                            if (Array.isArray(card.variants)) {
                                card.variants.forEach(v => {
                                    const code = (v.product_code || '').toUpperCase();
                                    const oldSize = v.size || '';
                                    let newSize = oldSize;

                                    if (code.includes('3016') || oldSize === 'Standard' || oldSize === '1/2 inch' || oldSize === '1/2"') {
                                        newSize = '3 Meter x 16mm';
                                    } else if (code.includes('5019') || oldSize === '3/4 inch' || oldSize === '3/4"') {
                                        newSize = '5 Meter x 19mm';
                                    } else if (code.includes('1025')) {
                                        newSize = '10 Meter x 25mm';
                                    }

                                    if (newSize !== oldSize) {
                                        v.size = newSize;
                                        fixedVariantsCount++;
                                        cardModified = true;
                                    }
                                    v.description = card.description;
                                });
                            }
                        } else {
                            // Smart category & description enrichment without corrupting titles
                            const smartDesc = getSmartAiDescriptionForProduct(card.parent_name, card.category);
                            if (!card.description || card.description.includes('Measuring Tape') || card.description.length < 15) {
                                card.description = smartDesc;
                                cardModified = true;
                            }

                            if (Array.isArray(card.variants)) {
                                card.variants.forEach(v => {
                                    if (!v.size || v.size === 'Standard') {
                                        const combo = `${v.product_code || ''} ${card.parent_name || ''}`;
                                        const match = combo.match(/\b(15|20|25|32|40|50|65|75|80|90|100|110|160)\s*(?:mm)?\b/i);
                                        if (match) {
                                            const mm = match[1];
                                            const resolved = inchMap[mm] ? `${mm}mm (${inchMap[mm]})` : `${mm}mm`;
                                            v.size = resolved;
                                            fixedVariantsCount++;
                                            cardModified = true;
                                        }
                                    }
                                    if (!v.description || v.description.includes('Measuring Tape')) {
                                        v.description = card.description || smartDesc;
                                    }
                                });
                            }
                        }

                        if (cardModified) {
                            fixedCardsCount++;
                        }
                }

                // 2. Also heal any pending dynamicRows
                if (Array.isArray(dynamicRows)) {
                    dynamicRows.forEach(r => {
                        const rCode = (r.product_code || '').toUpperCase();
                        const rName = (r.product_name || '').toUpperCase();
                        let rModified = false;

                        if (rCode.includes('3016') || rName.includes('3016')) {
                            if (r.size === 'Standard' || !r.size || r.size === '1/2 inch') {
                                r.size = '3 Meter x 16mm';
                                rModified = true;
                            }
                            if (r.category !== 'Hardware & Tools') {
                                r.category = 'Hardware & Tools';
                                r.group_type = 'Hardware & Tools';
                                rModified = true;
                            }
                        } else if (rCode.includes('5019') || rName.includes('5019')) {
                            if (r.size === 'Standard' || !r.size || r.size === '3/4 inch') {
                                r.size = '5 Meter x 19mm';
                                rModified = true;
                            }
                            if (r.category !== 'Hardware & Tools') {
                                r.category = 'Hardware & Tools';
                                r.group_type = 'Hardware & Tools';
                                rModified = true;
                            }
                        }

                        if (rModified) fixedRowsCount++;
                    });
                }

                // 3. Trigger backend database healing asynchronously
                let dbHealResult = null;
                try {
                    const healUrl = "{{ route('seller.catalog.excel_mapper.heal_existing_products') }}";
                    const resp = await fetch(healUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    if (resp.ok) {
                        dbHealResult = await resp.json();
                    }
                } catch (e) {
                    console.warn('Backend DB heal notice:', e);
                }

                // 4. Save and re-render everything
                saveToLocalStorage();
                renderGroupedProductCards();
                renderDynamicRows();
                refreshCategoryFilterTabs();
                autoSaveDraftToBackend();

                let msg = `✨ Zabardast! ${fixedCardsCount} Grouped Cards, ${fixedVariantsCount} Variant Sizes aur Description auto-fix ho gaye bina kisi manual mehnat ke!`;
                if (dbHealResult && dbHealResult.healed_products_count > 0) {
                    msg += ` (${dbHealResult.healed_products_count} database products bhi sync ho gaye)`;
                }
                showToastNotification(msg);
                alert(`✅ Auto-Fix Complete!\n\n• ${fixedCardsCount} Cards ke Titles & Descriptions theek hue\n• ${fixedVariantsCount} Sizes (3M x 16mm, 5M x 19mm) auto-update ho gaye\n• Category "Hardware & Tools" me classify ho gayi\n\nAapko kuch bhi manually type karne ki zaroorat nahi padi!`);
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                }
            }
        }

        function loadLinesIntoDynamicRows(lines) {
            dynamicRows = [];
            lines.forEach((lineText, idx) => {
                const text = (lineText || '').trim();
                if (!text) return;

                let category = 'General Hardware';
                if (/CPVC/i.test(text)) category = 'CPVC';
                else if (/SWR|TRAP|DRAIN/i.test(text)) category = 'SWR';
                else if (/AGRI|SOLVENT/i.test(text)) category = 'Agri & Solvents';
                else if (/UPVC/i.test(text)) category = 'UPVC';

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
                hsn_code: '39174000',
                product_name: 'New Product Item',
                size: 'Standard',
                packing_1: '',
                packing_2: '',
                group_type: 'General Hardware',
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

        // ==========================================
        // ⚡ BULK CATEGORY CHANGER FUNCTIONS
        // ==========================================
        function openBulkCategoryModal() {
            const modal = document.getElementById('bulkCategoryModal');
            if (!modal) return;
            const allCount = document.getElementById('bulkAllRowCountText');
            const selCount = document.getElementById('bulkSelectedRowCountText');
            
            const checkedBoxes = document.querySelectorAll('.dynamic-row-checkbox:checked');
            if (allCount) allCount.innerText = dynamicRows.length;
            if (selCount) selCount.innerText = checkedBoxes.length;

            const radios = document.getElementsByName('bulkCategoryScope');
            if (checkedBoxes.length > 0) {
                radios.forEach(r => { if (r.value === 'selected') r.checked = true; });
            } else {
                radios.forEach(r => { if (r.value === 'all') r.checked = true; });
            }

            modal.classList.remove('hidden');
            const input = document.getElementById('bulkCategoryInput');
            if (input) input.focus();
        }

        function closeBulkCategoryModal() {
            const modal = document.getElementById('bulkCategoryModal');
            if (modal) modal.classList.add('hidden');
        }

        function setBulkCategoryInput(cat) {
            const input = document.getElementById('bulkCategoryInput');
            if (input) input.value = cat;
        }

        function applyBulkCategory() {
            const input = document.getElementById('bulkCategoryInput');
            const newCat = (input?.value || '').trim();
            if (!newCat) {
                alert('Kripya koi Category name likhein ya preset chunein.');
                return;
            }

            let scope = 'all';
            document.getElementsByName('bulkCategoryScope').forEach(r => {
                if (r.checked) scope = r.value;
            });

            let count = 0;
            if (scope === 'selected') {
                const checkedIds = Array.from(document.querySelectorAll('.dynamic-row-checkbox:checked')).map(cb => parseInt(cb.value));
                if (checkedIds.length === 0) {
                    alert('Table me se kam se kam 1 row checkbox se select karein ya "Apply to ALL rows" chunein.');
                    return;
                }
                dynamicRows.forEach(r => {
                    if (checkedIds.includes(r.id)) {
                        r.group_type = newCat;
                        r.category = newCat;
                        count++;
                    }
                });
            } else {
                dynamicRows.forEach(r => {
                    r.group_type = newCat;
                    r.category = newCat;
                    count++;
                });
            }

            saveToLocalStorage();
            if (typeof autoSaveDraftToBackend === 'function') autoSaveDraftToBackend();
            renderDynamicRows();
            refreshCategoryFilterTabs();
            closeBulkCategoryModal();
            showToastNotification(`🎉 Success! ${count} rows ka Category badalkar "${newCat}" set kar diya gaya hai.`);
        }

        // ==========================================
        // 📋 SMART PASTE TEXT / TABLE FUNCTIONS
        // ==========================================
        let smartPasteParsedItems = [];

        function openSmartPasteModal() {
            const modal = document.getElementById('smartPasteModal');
            if (!modal) return;
            modal.classList.remove('hidden');
            const textarea = document.getElementById('smartPasteInput');
            if (textarea) textarea.focus();
            previewSmartPaste();
        }

        function closeSmartPasteModal() {
            const modal = document.getElementById('smartPasteModal');
            if (modal) modal.classList.add('hidden');
        }

        // ==========================================
        // ✏️ BULK ITEM NAME CHANGER FUNCTIONS
        // ==========================================
        function openBulkItemNameModal() {
            const modal = document.getElementById('bulkItemNameModal');
            if (!modal) return;
            const checkedBoxes = document.querySelectorAll('.dynamic-row-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Pehle table me se kam se kam 1 ya zyada rows select karein (jaise "Next 6" dabakar).');
                return;
            }
            const countText = document.getElementById('bulkNameSelectedCountText');
            if (countText) countText.innerText = `${checkedBoxes.length} Rows Selected`;

            // Pre-fill with first selected row's name if available
            const firstRowId = parseInt(checkedBoxes[0].value);
            const firstRow = dynamicRows.find(r => r.id === firstRowId);
            const input = document.getElementById('bulkItemNameInput');
            if (input && firstRow) {
                input.value = firstRow.product_name || '';
            }

            modal.classList.remove('hidden');
            if (input) input.focus();
        }

        function closeBulkItemNameModal() {
            const modal = document.getElementById('bulkItemNameModal');
            if (modal) modal.classList.add('hidden');
        }

        function applyBulkItemName() {
            const input = document.getElementById('bulkItemNameInput');
            const newName = (input?.value || '').trim();
            if (!newName) {
                alert('Kripya koi Product Name likhein.');
                return;
            }

            const checkedBoxes = document.querySelectorAll('.dynamic-row-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Table me se rows select nahi hain.');
                return;
            }

            const checkedIds = Array.from(checkedBoxes).map(cb => parseInt(cb.value));
            let count = 0;
            dynamicRows.forEach(r => {
                if (checkedIds.includes(r.id)) {
                    r.product_name = newName;
                    count++;
                }
            });

            saveToLocalStorage();
            if (typeof autoSaveDraftToBackend === 'function') autoSaveDraftToBackend();
            renderDynamicRows();
            closeBulkItemNameModal();
            showToastNotification(`🎉 Success! ${count} rows ka Product Name badalkar "${newName}" set ho gaya hai!`);
        }

        function parseRawPasteText(text, targetCategory, filterNoise, forcedBaseName = '') {
            if (!text) return [];
            const rawLines = text.split(/\r?\n/).map(l => l.trim()).filter(Boolean);
            const items = [];
            let currentSectionHeader = forcedBaseName || '';

            rawLines.forEach((line, idx) => {
                if (filterNoise && isNoiseCatalogLine(line)) return;

                const parsed = parseCatalogRowText(line, currentSectionHeader, forcedBaseName);
                if (!parsed) return;

                if (parsed.name && parsed.name !== 'Standard Item' && !/^\d+/.test(parsed.name)) {
                    currentSectionHeader = parsed.name;
                }

                let cat = targetCategory || 'General Hardware';
                const combined = (parsed.name + ' ' + parsed.size).toUpperCase();
                if (/CPVC/i.test(combined)) cat = 'CPVC';
                else if (/SWR|TRAP|DRAIN/i.test(combined)) cat = 'SWR';
                else if (/AGRI|SOLVENT/i.test(combined)) cat = 'Agri & Solvents';
                else if (/UPVC/i.test(combined)) cat = 'UPVC';

                items.push({
                    name: parsed.name,
                    size: parsed.size,
                    pack1: parsed.pack1,
                    pack2: parsed.pack2,
                    mrp: parsed.mrp,
                    sku: parsed.sku,
                    category: cat
                });
            });

            return items;
        }

        function previewSmartPaste() {
            const rawText = document.getElementById('smartPasteInput')?.value || '';
            const cat = (document.getElementById('smartPasteCategory')?.value || '').trim() || 'General Hardware';
            const baseName = (document.getElementById('smartPasteBaseName')?.value || '').trim();
            const filterNoise = document.getElementById('smartPasteFilterNoise')?.checked ?? true;

            smartPasteParsedItems = parseRawPasteText(rawText, cat, filterNoise, baseName);

            const countEl = document.getElementById('smartPastePreviewCount');
            const statsEl = document.getElementById('smartPasteStatsText');
            const tbody = document.getElementById('smartPastePreviewBody');

            const totalRawLines = rawText.split(/\r?\n/).filter(l => l.trim().length > 0).length;
            if (statsEl) statsEl.innerText = `${totalRawLines} raw lines (${smartPasteParsedItems.length} valid products)`;
            if (countEl) countEl.innerText = smartPasteParsedItems.length;

            if (!tbody) return;

            if (smartPasteParsedItems.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="p-4 text-center text-gray-400 text-xs">Upar text paste karein to yahan preview dikhega...</td></tr>`;
                return;
            }

            let previewHtml = '';
            smartPasteParsedItems.slice(0, 15).forEach((item, idx) => {
                const packStr = [item.pack1 ? `Box: ${item.pack1}` : '', item.pack2 ? `Bag: ${item.pack2}` : ''].filter(Boolean).join(' | ');
                previewHtml += `
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-2 text-center text-gray-400 font-mono text-[10px]">${idx + 1}</td>
                        <td class="p-2"><span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700">${escapeHtml(item.category)}</span></td>
                        <td class="p-2 font-bold text-gray-900 truncate max-w-[200px]" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</td>
                        <td class="p-2 font-mono text-gray-700 font-bold">${escapeHtml(item.size)}</td>
                        <td class="p-2 font-mono text-[10px] text-indigo-700">${escapeHtml(packStr || '-')}</td>
                        <td class="p-2 font-mono font-bold text-emerald-700">₹${item.mrp.toFixed(2)}</td>
                    </tr>
                `;
            });
            if (smartPasteParsedItems.length > 15) {
                previewHtml += `<tr><td colspan="6" class="p-2 text-center text-gray-400 text-[11px] font-medium bg-gray-50">+ ${smartPasteParsedItems.length - 15} aur items hain...</td></tr>`;
            }
            tbody.innerHTML = previewHtml;
        }

        function insertSmartPasteRows(replaceMode) {
            if (smartPasteParsedItems.length === 0) {
                alert('Pehle upar text paste karein jisme kam se kam 1 valid product line ho.');
                return;
            }

            if (replaceMode) {
                dynamicRows = [];
                dynamicRowNextId = 1;
            }

            smartPasteParsedItems.forEach((item, idx) => {
                const cost = Math.round(item.mrp * 0.65);
                const retail = Math.round(item.mrp * 0.88);

                dynamicRows.push({
                    id: dynamicRowNextId++,
                    product_code: item.sku || `ITM-${100 + idx}`,
                    hsn_code: '39174000',
                    product_name: item.name,
                    size: item.size,
                    packing_1: item.pack1 || '',
                    packing_2: item.pack2 || '',
                    group_type: item.category,
                    category: item.category,
                    mrp: item.mrp,
                    purchase_cost: cost,
                    cost_price_2: 0,
                    cost_price_3: 0,
                    retail_price: retail,
                    stock: 100,
                    image_url: preselectedImg || '',
                    asset_url: preselectedImg ? ('/' + preselectedImg.replace(/^\//, '')) : ''
                });
            });

            saveToLocalStorage();
            if (typeof autoSaveDraftToBackend === 'function') autoSaveDraftToBackend();
            renderDynamicRows();
            refreshCategoryFilterTabs();
            closeSmartPasteModal();

            const textarea = document.getElementById('smartPasteInput');
            if (textarea) textarea.value = '';

            showToastNotification(`🎉 Shabaash! ${smartPasteParsedItems.length} products clean name aur sizes ke sath table me jodh diye gaye hain!`);
        }

        function renderDynamicRows() {
            if (typeof deduplicateDynamicRowsAgainstCards === 'function') {
                deduplicateDynamicRowsAgainstCards();
            }

            const tbody = document.getElementById('dynamicTableBody');
            const badge = document.getElementById('dynamicRowCountBadge');
            const pendingEl = document.getElementById('pendingRowsCountText');
            const completedEl = document.getElementById('completedCardsCountText');
            const statsBadge = document.getElementById('dynamicPendingStatsBadge');

            if (pendingEl) pendingEl.innerText = `${dynamicRows.length} Rows Left`;
            if (completedEl) completedEl.innerText = `${groupedProductCards.length} Cards Ready`;
            if (statsBadge) statsBadge.innerHTML = `<i class="fa-solid fa-clock-rotate-left text-amber-600"></i> <span>${dynamicRows.length} Unmapped Left</span>`;

            if (!tbody) return;

            if (dynamicRows.length === 0) {
                if (badge) badge.innerText = `0 Rows`;
                tbody.innerHTML = `
                    <tr>
                        <td colspan="13" class="p-10 text-center">
                            <div class="max-w-md mx-auto space-y-3">
                                <div class="h-12 w-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl mx-auto shadow-inner">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <h4 class="text-sm font-black text-gray-900">Sabhi Items ke Cards Ban Chuke Hain!</h4>
                                <p class="text-xs text-gray-500">
                                    Aapki spreadsheet ke sabhi items successfully Product Cards me save ho gaye hain. Koi pending unmapped row bachi nahi hai!
                                </p>
                                <div class="pt-2 flex items-center justify-center gap-3">
                                    <button type="button" onclick="document.getElementById('groupedCardsSection')?.scrollIntoView({behavior:'smooth'})" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-md shadow-indigo-600/20 flex items-center gap-2">
                                        <i class="fa-solid fa-layer-group"></i>
                                        <span>View Grouped Cards (${groupedProductCards.length})</span>
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            // Filter dynamic rows based on activeCategory
            const filteredRows = (activeCategory === 'ALL')
                ? dynamicRows
                : dynamicRows.filter(r => (r.group_type || r.category || 'General Hardware').trim().toLowerCase() === activeCategory.toLowerCase());

            if (badge) {
                badge.innerText = (activeCategory === 'ALL')
                    ? `${dynamicRows.length} Rows`
                    : `${filteredRows.length} of ${dynamicRows.length} Rows`;
            }

            if (filteredRows.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="13" class="p-8 text-center text-gray-500 text-xs">
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold mb-2">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                <span>"${escapeHtml(activeCategory)}" Category Complete!</span>
                            </div>
                            <p class="font-bold text-gray-800 text-sm">Is category ke sabhi items ke cards ban chuke hain!</p>
                            <p class="text-[11px] text-gray-500 mt-1">Baki bachi hui categories dekhne ke liye upar <b>"All Pending"</b> ya dusre Category tab par click karein.</p>
                            <button type="button" onclick="filterByCategory('ALL')" class="mt-3.5 px-4 py-1.5 rounded-xl bg-gray-900 hover:bg-gray-800 text-white font-extrabold text-xs shadow-sm flex items-center gap-2 mx-auto active:scale-95 transition">
                                <i class="fa-solid fa-layer-group"></i>
                                <span>Show All Pending Items (${dynamicRows.length})</span>
                            </button>
                        </td>
                    </tr>
                `;
                return;
            }

            let html = '';
            filteredRows.forEach((r) => {
                const imgThumb = r.image_url 
                    ? `<img src="${r.asset_url || r.image_url}" class="max-h-full max-w-full object-contain" id="dyn_img_${r.id}">`
                    : `<span class="text-gray-300 text-xs" id="dyn_img_${r.id}"><i class="fa-solid fa-camera"></i></span>`;

                const catVal = r.group_type || r.category || 'General Hardware';
                const searchStr = `${r.product_name} ${r.product_code || ''} ${r.hsn_code || ''} ${r.size || ''} ${catVal}`.toLowerCase();

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
                        <td class="p-3 min-w-[220px]">
                            <input type="text" value="${escapeHtml(r.product_name)}" oninput="updateDynamicRowField(${r.id}, 'product_name', this.value)" class="w-full text-xs font-bold text-gray-900 border border-transparent hover:border-gray-300 focus:border-emerald-600 focus:bg-white rounded-lg p-1 transition bg-transparent" placeholder="Product Name">
                            <input type="text" value="${escapeHtml(r.description || '')}" oninput="updateDynamicRowField(${r.id}, 'description', this.value)" class="w-full text-[10px] text-gray-500 border border-transparent hover:border-gray-300 focus:border-indigo-500 focus:bg-white rounded px-1 py-0.5 transition bg-transparent mt-0.5" placeholder="Specs / Series / Desc" title="Product Description / Series">
                        </td>
                        <td class="p-3">
                            <input type="text" value="${escapeHtml(r.product_code || '')}" placeholder="Item Code" oninput="updateDynamicRowField(${r.id}, 'product_code', this.value)" class="w-24 text-[11px] font-mono text-gray-700 border border-transparent hover:border-gray-300 focus:border-emerald-600 focus:bg-white rounded-lg p-1 transition bg-transparent" title="Product / Item Code (SKU)">
                        </td>
                        <td class="p-3">
                            <input type="text" value="${escapeHtml(r.hsn_code || '39174000')}" placeholder="39174000" oninput="updateDynamicRowField(${r.id}, 'hsn_code', this.value)" class="w-20 text-[11px] font-mono text-indigo-700 border border-transparent hover:border-gray-300 focus:border-emerald-600 focus:bg-white rounded-lg p-1 transition bg-transparent" title="HSN / SAC Code (Default: 39174000)">
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
                            <input type="number" value="${(r.stock !== undefined && r.stock !== null && r.stock !== '') ? r.stock : 0}" oninput="updateDynamicRowField(${r.id}, 'stock', parseInt(this.value) || 0)" class="w-14 text-xs font-mono text-gray-700 border border-transparent hover:border-gray-300 focus:border-emerald-600 rounded-lg p-1 bg-transparent" title="Stock Quantity">
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
                alert('Table pehle se hi bilkul saaf aur khali hai.');
                return;
            }
            if (confirm('Kya aap table ke sabhi purane rows aur cards ko hatakar nayi list ke liye fresh shuru karna chahte hain?')) {
                dynamicRows = [];
                groupedProductCards = [];
                localStorage.removeItem('vyapar_custom_excel_lines');
                localStorage.removeItem('vyapar_grouped_cards');
                localStorage.removeItem('vyapar_dynamic_rows');
                localStorage.removeItem('vyapar_grouped_next_id');
                localStorage.removeItem('vyapar_dynamic_next_id');
                renderDynamicRows();
                renderGroupedProductCards();
                refreshCategoryFilterTabs();
                clearDynamicRowSelection();
                showToastNotification('✨ Sabhi purane rows aur cards saaf ho gaye! Ab aap nayi list shuru kar sakte hain.');
            }
        }

        function exportDynamicToCsv() {
            if (dynamicRows.length === 0 && groupedProductCards.length === 0) {
                alert('Export karne ke liye koi rows ya cards nahi hain.');
                return;
            }

            let targetCards = groupedProductCards;
            let targetRows = dynamicRows;
            let catSuffix = '';

            if (typeof activeCategory !== 'undefined' && activeCategory && activeCategory !== 'ALL') {
                const doFilter = confirm(`Aap abhi "${activeCategory}" category filter me hain.\n\n• [OK] dabayein: Sirf "${activeCategory}" ke cards aur items export karne ke liye\n• [Cancel] dabayein: Sabhi categories ka complete catalog export karne ke liye`);
                if (doFilter) {
                    const normCat = activeCategory.trim().toLowerCase();
                    targetCards = groupedProductCards.filter(c => (c.category || '').trim().toLowerCase() === normCat);
                    targetRows = dynamicRows.filter(r => (r.group_type || r.category || 'General Hardware').trim().toLowerCase() === normCat);
                    catSuffix = `_${activeCategory.trim().replace(/[^a-zA-Z0-9_-]/g, '_')}`;
                }
            }

            if (targetCards.length === 0 && targetRows.length === 0) {
                alert(`Chuni hui category ("${activeCategory}") me export karne ke liye koi cards ya rows nahi hain.`);
                return;
            }

            const sheetName = (document.getElementById('dynamicSheetName')?.value || 'catalog').trim().replace(/[^a-zA-Z0-9_-]/g, '_');
            let csv = "Category,Product Code,HSN Code,Product Name,Description,Size/Dimension,Packing 1 (Box),Packing 2 (Carton),MRP,Purchase Cost 1,Cost Price 2,Cost Price 3,Selling Price,Stock,Image URL\n";

            // Add grouped cards
            targetCards.forEach(c => {
                const escapeCsv = (str) => `"${(str || '').toString().replace(/"/g, '""')}"`;
                c.variants.forEach(v => {
                    csv += [
                        escapeCsv(c.category || 'General Hardware'),
                        escapeCsv(v.product_code || ''),
                        escapeCsv(v.hsn_code || '39174000'),
                        escapeCsv(c.parent_name),
                        escapeCsv(c.description || v.description || ''),
                        escapeCsv(v.size || 'Standard'),
                        escapeCsv(v.packing_1 || ''),
                        escapeCsv(v.packing_2 || ''),
                        v.mrp,
                        v.purchase_cost,
                        v.cost_price_2 || 0,
                        v.cost_price_3 || 0,
                        v.retail_price,
                        (v.stock !== undefined && v.stock !== null) ? v.stock : 0,
                        escapeCsv(c.image_url || c.asset_url || '')
                    ].join(',') + "\n";
                });
            });

            // Add flat rows
            targetRows.forEach(r => {
                const escapeCsv = (str) => `"${(str || '').toString().replace(/"/g, '""')}"`;
                csv += [
                    escapeCsv(r.group_type || r.category || 'General Hardware'),
                    escapeCsv(r.product_code || ''),
                    escapeCsv(r.hsn_code || '39174000'),
                    escapeCsv(r.product_name),
                    escapeCsv(r.description || ''),
                    escapeCsv(r.size || 'Standard'),
                    escapeCsv(r.packing_1 || ''),
                    escapeCsv(r.packing_2 || ''),
                    r.mrp,
                    r.purchase_cost,
                    r.cost_price_2 || 0,
                    r.cost_price_3 || 0,
                    r.retail_price,
                    (r.stock !== undefined && r.stock !== null) ? r.stock : 0,
                    escapeCsv(r.image_url || r.asset_url || '')
                ].join(',') + "\n";
            });

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.setAttribute('download', `${sheetName}${catSuffix}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            showToastNotification(`📥 Catalog sheet "${sheetName}${catSuffix}.csv" download ho gayi hai!`);
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
                    localStorage.removeItem('vyapar_grouped_cards');
                    localStorage.removeItem('vyapar_dynamic_rows');
                    localStorage.removeItem('vyapar_custom_excel_lines');
                    alert(data.message || 'Sheet saved successfully!');
                    if (data.redirect_url) {
                        const targetUrl = data.redirect_url + (data.redirect_url.includes('?') ? '&' : '?') + 'mode=saved_job';
                        window.location.href = targetUrl;
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
            if (urlParams.get('source') === 'pdf_lines') {
                toggleDynamicMode(true);
            } else if (urlParams.get('mode') === 'saved_job') {
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
