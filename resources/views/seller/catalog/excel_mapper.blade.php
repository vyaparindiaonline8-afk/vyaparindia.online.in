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
                    <button type="button" onclick="restoreDraftCardsAndRows()" class="px-3.5 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-800 font-black text-xs border border-indigo-200 flex items-center gap-1.5 transition active:scale-95 shadow-2xs" title="Browser refresh hone par cards aur list wapas restore karein">
                        <i class="fa-solid fa-rotate-left text-indigo-600"></i>
                        <span>🔄 Restore Draft</span>
                    </button>
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

        <!-- 🎛️ Dual Workspace Switcher: PENDING DRAFT vs LIVE PUBLISHED -->
        <div class="bg-white p-3.5 rounded-3xl border border-gray-200 shadow-xs flex flex-wrap items-center justify-between gap-3 mb-4">
            <div class="flex items-center gap-2 p-1.5 bg-slate-100 rounded-2xl">
                <button type="button" onclick="switchMapperMainView('pending')" id="viewBtnPending" class="px-5 py-2.5 rounded-xl font-black text-xs transition flex items-center gap-2 bg-white text-gray-900 shadow-sm border border-gray-200 cursor-pointer">
                    <span class="h-2.5 w-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>🟡 Pending Draft (Nayi List / In-Progress)</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-100 text-amber-800 font-extrabold" id="pendingTabCountBadge">0 Items</span>
                </button>
                <button type="button" onclick="switchMapperMainView('live')" id="viewBtnLive" class="px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 text-gray-600 hover:text-gray-900 cursor-pointer">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                    <span>🟢 Live Store Products (Published)</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-extrabold" id="liveTabCountBadge">{{ count($liveProducts ?? []) }} Live</span>
                </button>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="openRawPriceFormulaModal()" class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-indigo-600 hover:from-emerald-700 hover:to-indigo-700 text-white font-black text-xs shadow-md shadow-emerald-600/30 flex items-center gap-1.5 transition active:scale-95 cursor-pointer">
                    <i class="fa-brands fa-whatsapp text-sm text-green-300"></i>
                    <span>🏷️ WhatsApp / Raw Rates</span>
                </button>
                <button type="button" onclick="resetDynamicTable()" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-black text-xs border border-rose-200 flex items-center gap-1.5 transition active:scale-95 cursor-pointer" title="Purana data hatakar nayi list shuru karein">
                    <i class="fa-solid fa-broom text-rose-600"></i>
                    <span>Start Fresh</span>
                </button>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- 📑 DYNAMIC SAAS SPREADSHEET TABLE          -->
        <!-- (Supports PDF Extracted Lines & 6-8 Link)  -->
        <!-- ========================================== -->
        <div id="dynamicSheetContainer" class="space-y-4">
            <!-- ⚡ PDF Import Success Alert Banner -->
            <div id="pdfImportSuccessAlert" class="hidden p-4 rounded-3xl bg-gradient-to-r from-emerald-600 to-teal-700 text-white shadow-lg flex flex-wrap items-center justify-between gap-3 animate-fade-in border border-emerald-400">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-white/20 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-file-excel text-amber-300"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black" id="pdfImportAlertTitle">PDF Lines Successfully Imported!</h4>
                        <p class="text-[11px] text-emerald-100" id="pdfImportAlertDesc">Niche spreadsheet table me sabhi rows taiyar hain. Ab aap kisi bhi row ko select karke photo jod sakte hain ya direct publish kar sakte hain.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <div id="pdfImportAlertActions" class="hidden flex flex-wrap items-center gap-2">
                        <button type="button" onclick="clearOldDraftAndKeepOnlyNewPdf()" class="px-3.5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-black transition flex items-center gap-1.5 shadow-sm cursor-pointer" title="Purana electric tools/hardware data hatakar sirf abhi ke naye PDF items par kaam karein">
                            <i class="fa-solid fa-broom text-xs"></i>
                            <span>🧹 Purana Saaf Karein (Sirf Naya PDF)</span>
                        </button>
                        <button type="button" onclick="mergeWithPreviousDraft()" class="px-3.5 py-1.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-gray-950 text-xs font-black transition flex items-center gap-1.5 shadow-sm cursor-pointer" title="Purane draft ko is naye PDF data ke sath jod dein">
                            <i class="fa-solid fa-code-merge text-xs"></i>
                            <span>Purane Draft me Jodein (Merge)</span>
                        </button>
                        <button type="button" onclick="restoreDraftCardsAndRows()" class="px-3.5 py-1.5 rounded-xl bg-white/20 hover:bg-white/30 text-white text-xs font-bold transition flex items-center gap-1.5 cursor-pointer" title="Purana draft wapas laayein">
                            <i class="fa-solid fa-rotate-left text-xs"></i>
                            <span>Sirf Purana Draft Dekhein</span>
                        </button>
                    </div>
                    <button type="button" onclick="document.getElementById('pdfImportSuccessAlert').classList.add('hidden')" class="px-3.5 py-1.5 rounded-xl bg-white/20 hover:bg-white/30 text-white text-xs font-bold transition shrink-0 cursor-pointer">
                        ✕ Samajh Gaya
                    </button>
                </div>
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

                    <button type="button" onclick="openRawPriceFormulaModal()" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-indigo-600 hover:from-emerald-700 hover:to-indigo-700 text-white font-black text-xs shadow-md shadow-emerald-600/30 flex items-center gap-1.5 transition active:scale-95 cursor-pointer" title="WhatsApp ya Raw Text Rate list paste karein aur Bhada + Margin auto-calculate karein">
                        <i class="fa-brands fa-whatsapp text-sm text-green-300"></i>
                        <span>🏷️ WhatsApp / Raw Rates & Formula</span>
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
                    <button type="button" onclick="saveDynamicSheetToBackend(false)" id="btnSaveDynamicSheet" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-600/30 flex items-center gap-1.5 transition active:scale-95 cursor-pointer" title="Category-wise ya complete sheet publish karein">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span id="btnSaveDynamicSheetText">Save & Publish</span>
                    </button>
                    <button type="button" onclick="saveDynamicSheetToBackend(true)" id="btnSaveAllDynamicSheet" class="hidden px-3.5 py-2 rounded-xl bg-gray-900 hover:bg-black text-white font-bold text-xs shadow-xs flex items-center gap-1.5 transition active:scale-95 cursor-pointer" title="Sabhi categories ko ek sath publish karein">
                        <i class="fa-solid fa-layer-group text-amber-300"></i>
                        <span>Publish All</span>
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
                        <button type="button" onclick="triggerDevicePhotoUpload()" class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-black shadow-xs flex items-center gap-1.5 transition cursor-pointer" title="Apne mobile ya computer se photos upload karein">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span>Upload Photo</span>
                        </button>
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
                            <div class="on-page-photo-card flex items-center gap-2.5 p-2 bg-gray-50 hover:bg-indigo-50 border border-gray-200 hover:border-indigo-400 rounded-2xl cursor-pointer transition shrink-0 group" onclick="handleOnPagePhotoClick('{{ $g['url'] }}', '{{ $g['asset_url'] }}')" data-name="{{ strtolower($g['name']) }}" data-folder="{{ strtolower($g['folder'] ?? 'general') }}" data-raw-folder="{{ $g['folder'] ?? 'General' }}" data-img-key="{{ strtolower(basename(parse_url($g['url'], PHP_URL_PATH))) }}">
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
                            Pending items ko 1-1 karke select karne ki zaroorat nahi! Click karte hi sabhi products apne-apne <b>Product Name / Model ke alag Cards</b> me distribute ho jayenge — <b>fir aapko sirf 1-1 Photo link karni hogi!</b>
                        </p>
                    </div>
                </div>
                <button type="button" onclick="autoGroupAllDynamicRowsIntoCards()" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-indigo-600 hover:from-emerald-500 hover:to-indigo-500 text-white font-black text-xs shadow-lg shadow-emerald-600/30 flex items-center gap-2 transition active:scale-95 shrink-0">
                    <i class="fa-solid fa-bolt text-amber-300"></i>
                    <span>Auto-Distribute into Separate Cards &rarr;</span>
                </button>
            </div>

            <!-- 🏢 MULTI-INDUSTRY BUSINESS MODULE SELECTOR -->
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-4 rounded-3xl border border-indigo-500/30 shadow-lg space-y-3" id="industryModuleSelectorBar">
                <div class="flex flex-wrap items-center justify-between gap-3 pb-2 border-b border-indigo-900/60">
                    <div class="flex items-center gap-2.5">
                        <div class="h-8 w-8 rounded-xl bg-indigo-600/30 text-indigo-400 flex items-center justify-center text-sm border border-indigo-500/30 shrink-0">
                            <i class="fa-solid fa-industry"></i>
                        </div>
                        <div>
                            <span class="text-xs font-black text-white uppercase tracking-wider flex items-center gap-2">
                                <span>Multi-Business Industry Modules</span>
                                <span class="px-2 py-0.5 rounded-full text-[9px] bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30">Independent Business Logics</span>
                            </span>
                            <p class="text-[10px] text-gray-400">Plywood, Plumbing, Pumps, Electricals, Sanitaryware & Electronics ke alag-alag formula & size rules</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openIndustryModuleConfigModal()" class="px-3 py-1.5 rounded-xl bg-indigo-800/60 hover:bg-indigo-700 text-indigo-200 text-[11px] font-bold border border-indigo-700/60 flex items-center gap-1.5 transition cursor-pointer">
                            <i class="fa-solid fa-gear text-amber-300"></i>
                            <span>Configure My Business Lines</span>
                        </button>
                    </div>
                </div>

                <!-- Module Selection Pills -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1" id="industryModulePills">
                    <!-- Populated dynamically via JS -->
                </div>
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

            <!-- ⚡ SMART CATEGORY SUB-GROUP MULTI-MARGIN & FREIGHT ENGINE -->
            <div id="smartSubGroupRuleCard" class="hidden bg-slate-900 border border-indigo-800/80 rounded-3xl p-4 sm:p-5 text-white shadow-xl space-y-3.5 transition-all">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-2xl bg-indigo-600/30 text-indigo-400 flex items-center justify-center text-lg border border-indigo-500/30 shrink-0">
                            <i class="fa-solid fa-sliders"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-black uppercase tracking-wider text-indigo-300">Category Multi-Margin & Freight Engine</h4>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-indigo-500/30 text-indigo-200 font-mono font-bold" id="smartSubGroupActiveCatBadge">Category</span>
                            </div>
                            <p class="text-[11px] text-gray-400">
                                Pipes me alag margin, plain fittings me alag aur brass fittings me alag margin & bhada 1-click me set karein
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="applyAllSubGroupRules()" class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-xs shadow-md shadow-emerald-600/30 flex items-center gap-1.5 transition active:scale-95 cursor-pointer">
                            <i class="fa-solid fa-bolt-lightning text-amber-300"></i>
                            <span>⚡ Apply All Sub-Groups</span>
                        </button>
                    </div>
                </div>

                <!-- Sub-Group Rows Container (Populated dynamically) -->
                <div class="space-y-2.5" id="smartSubGroupRowsContainer">
                    <!-- Dynamic Sub-Group rows rendered here -->
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
                        <button type="button" onclick="ungroupAllCardsToTable()" class="px-3.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 font-extrabold text-xs border border-amber-200 flex items-center gap-1.5 transition active:scale-95 shadow-2xs" title="Sabhi cards ko dismantle karke sare items wapas Table Listing me bhejein taaki aap apni marzi se rows select karke cards bana sakein">
                            <i class="fa-solid fa-arrow-rotate-left text-amber-600"></i>
                            <span>↩️ Wapas Sabhi Cards List Me Kholein</span>
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
                                <th class="p-3 w-24">Model No</th>
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

                <!-- 🔗 Floating Bottom Batch Bar for Selected Cards (Always visible anywhere you scroll) -->
                <div id="groupedCardsBatchBar" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-[9990] bg-slate-900/95 backdrop-blur-md text-white border-2 border-indigo-500 rounded-3xl p-3.5 px-6 shadow-2xl flex items-center justify-between gap-5 text-xs max-w-xl w-[92vw]">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-sm shadow-md shadow-indigo-600/40">
                            <i class="fa-solid fa-code-merge"></i>
                        </div>
                        <div>
                            <span class="font-black text-sm text-white" id="groupedCardsSelectedCountText">2 Cards Selected</span>
                            <p class="text-[11px] text-gray-300">In cards ko merge karke naya group banayein</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openMergeSelectedCardsModal()" class="px-4 py-2.5 rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-black text-xs shadow-lg shadow-indigo-500/40 flex items-center gap-2 transition active:scale-95 cursor-pointer">
                            <i class="fa-solid fa-code-merge text-amber-300"></i>
                            <span>🔗 Merge Selected Cards</span>
                        </button>
                        <button type="button" onclick="clearGroupedCardSelection()" class="h-9 w-9 rounded-2xl bg-white/10 hover:bg-white/20 text-gray-300 flex items-center justify-center text-xs transition" title="Cancel Selection">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>

                <div id="groupedCardsGrid" class="space-y-4">
                    <!-- Populated via JS -->
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- 🟢 LIVE STORE PRODUCTS & RATE MATRIX VIEW  -->
        <!-- ========================================== -->
        <div id="liveStoreProductsContainer" class="hidden space-y-6">
            <!-- Header & Filter Bar -->
            <div class="bg-white p-5 rounded-3xl border border-gray-200 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-emerald-500"></span>
                            <span>Live Store Catalog & Rate Matrix</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-800 font-extrabold" id="liveHeaderCountBadge">{{ count($liveProducts ?? []) }} Published</span>
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Ye products aapke live store par published hain. Categories wise filtered hain aur real-time rates dikhate hain.
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="switchMapperMainView('pending')" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-600/30 flex items-center gap-2 transition active:scale-95 cursor-pointer">
                            <i class="fa-solid fa-plus"></i>
                            <span>➕ Add New Rate List (Pending)</span>
                        </button>
                        <a href="{{ route('seller.inventory.index') }}" class="px-3.5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition flex items-center gap-1.5">
                            <i class="fa-solid fa-boxes-stacked text-indigo-600"></i>
                            <span>Full Inventory &rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Live Category Filter Tabs -->
                <div class="flex items-center gap-2 overflow-x-auto pb-2 border-t border-gray-100 pt-3" id="liveCategoryFilterTabs">
                    @php
                        $liveCategories = [];
                        if (isset($liveProducts)) {
                            foreach ($liveProducts as $lp) {
                                $cName = $lp->category->name ?? ($lp->group_name ?? 'General');
                                $liveCategories[$cName] = ($liveCategories[$cName] ?? 0) + 1;
                            }
                        }
                    @endphp
                    <button type="button" onclick="filterLiveProductsByCategory('ALL')" class="live-cat-tab-btn px-4 py-1.5 rounded-xl font-extrabold text-xs transition bg-emerald-600 text-white shadow-xs cursor-pointer" data-cat="ALL">
                        All ({{ count($liveProducts ?? []) }})
                    </button>
                    @foreach($liveCategories as $cName => $cCount)
                        <button type="button" onclick="filterLiveProductsByCategory('{{ addslashes($cName) }}')" class="live-cat-tab-btn px-4 py-1.5 rounded-xl font-bold text-xs transition bg-gray-100 hover:bg-gray-200 text-gray-700 cursor-pointer" data-cat="{{ $cName }}">
                            {{ $cName }} ({{ $cCount }})
                        </button>
                    @endforeach
                </div>

                <!-- Search Input -->
                <div class="relative max-w-md">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" id="liveProductsSearchInput" oninput="filterLiveProductsSearch(this.value)" placeholder="Search live products by name, brand or size..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 text-xs bg-gray-50 focus:bg-white focus:border-emerald-600 focus:outline-none transition">
                </div>
            </div>

            <!-- Live Products List Table -->
            <div class="bg-white rounded-3xl border border-gray-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 font-extrabold border-b border-gray-200">
                                <th class="p-3.5">Product & Details</th>
                                <th class="p-3.5">Category</th>
                                <th class="p-3.5">Variants & Sizes</th>
                                <th class="p-3.5">Rate A (Retail)</th>
                                <th class="p-3.5">Rate B (Wholesale)</th>
                                <th class="p-3.5">Effective Date</th>
                                <th class="p-3.5">Status</th>
                                <th class="p-3.5 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="liveProductsTableBody" class="divide-y divide-gray-100">
                            @forelse($liveProducts ?? [] as $lp)
                                @php
                                    $lpCat = $lp->category->name ?? ($lp->group_name ?? 'General');
                                    $variants = $lp->variants ?? collect();
                                @endphp
                                <tr class="live-product-row hover:bg-gray-50/60 transition" data-category="{{ $lpCat }}" data-name="{{ strtolower($lp->name) }}">
                                    <td class="p-3.5">
                                        <div class="flex items-center gap-3">
                                            @if($lp->image)
                                                <img src="{{ $lp->image }}" alt="{{ $lp->name }}" class="h-10 w-10 rounded-xl object-cover border border-gray-200 shrink-0">
                                            @else
                                                <div class="h-10 w-10 rounded-xl bg-gray-100 text-gray-400 flex items-center justify-center text-sm shrink-0">
                                                    <i class="fa-solid fa-cube"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-black text-gray-900 text-xs">{{ $lp->name }}</div>
                                                <div class="text-[10px] text-gray-500 font-mono">{{ $lp->brand ?? 'Generic' }} • SKU: {{ $lp->sku }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3.5">
                                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            {{ $lpCat }}
                                        </span>
                                    </td>
                                    <td class="p-3.5 font-mono">
                                        @if($variants->count() > 0)
                                            <div class="space-y-0.5">
                                                @foreach($variants->take(3) as $v)
                                                    <div class="text-[11px] text-gray-700"><span class="font-bold text-gray-900">{{ $v->size ?? $v->variant_name }}</span>: ₹{{ number_format($v->retail_price ?? $v->purchase_price, 2) }}</div>
                                                @endforeach
                                                @if($variants->count() > 3)
                                                    <span class="text-[10px] text-gray-400 font-sans">+{{ $variants->count() - 3 }} more sizes</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-500 font-sans">Single Item (₹{{ number_format($lp->price, 2) }})</span>
                                        @endif
                                    </td>
                                    <td class="p-3.5 font-mono font-bold text-emerald-700">
                                        @php
                                            $minP = $variants->count() > 0 ? $variants->min('retail_price') : $lp->price;
                                            $maxP = $variants->count() > 0 ? $variants->max('retail_price') : $lp->price;
                                            $isSqftLive = stripos($lpCat, 'ply') !== false || stripos($lpCat, 'board') !== false || stripos($lpCat, 'door') !== false || stripos($lp->name, 'ply') !== false;
                                            $hasSheetLive = $variants->contains(function($v) {
                                                $s = strtolower($v->size ?? '');
                                                return str_contains($s, 'x') || str_contains($s, 'sheet');
                                            });
                                        @endphp
                                        @if($variants->count() > 1 && $minP != $maxP)
                                            ₹{{ number_format($minP, 2) }} – ₹{{ number_format($maxP, 2) }} <span class="text-[10px] text-gray-500 font-normal">/ Sheet</span>
                                        @elseif($isSqftLive && !$hasSheetLive)
                                            ₹{{ number_format($lp->purchase_price ?: $lp->price, 2) }} <span class="text-[10px] text-gray-500 font-normal">/ sqft</span>
                                        @else
                                            ₹{{ number_format($lp->price, 2) }}
                                        @endif
                                    </td>
                                    <td class="p-3.5 font-mono font-bold text-cyan-700">
                                        ₹{{ number_format($lp->wholesale_price ?? ($lp->price * 0.85), 2) }}
                                    </td>
                                    <td class="p-3.5 text-[11px] text-gray-600 font-mono">
                                        {{ $lp->price_effective_date ?: '-' }}
                                    </td>
                                    <td class="p-3.5">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 flex items-center gap-1 w-fit">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                            <span>LIVE</span>
                                        </span>
                                    </td>
                                    <td class="p-3.5 text-center">
                                        <a href="{{ route('seller.products.edit', $lp->id) }}" class="px-2.5 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition inline-flex items-center gap-1">
                                            <i class="fa-solid fa-pen-to-square text-xs text-indigo-600"></i>
                                            <span>Edit</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-12 text-center text-gray-500">
                                        <div class="h-14 w-14 mx-auto rounded-3xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mb-3 shadow-inner">
                                            <i class="fa-solid fa-store"></i>
                                        </div>
                                        <h4 class="text-sm font-black text-gray-800">Abhi tak koi product Live store me publish nahi hua hai.</h4>
                                        <p class="text-xs text-gray-500 mt-1 max-w-md mx-auto">
                                            "Pending Draft" tab me jakar apni WhatsApp ya Excel rate list paste karein aur "Save to Catalog & Publish" dabayein!
                                        </p>
                                        <button type="button" onclick="switchMapperMainView('pending')" class="mt-4 px-4 py-2 rounded-xl bg-emerald-600 text-white font-black text-xs shadow-md shadow-emerald-600/30">
                                            🟡 Go to Pending Draft
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
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
            <!-- Bulk Category Change for Selected Rows -->
            <div class="flex items-center gap-1.5 bg-gray-800/80 p-1 rounded-2xl border border-gray-700">
                <span class="text-[10px] text-gray-400 pl-1">Category:</span>
                <input list="allCategoriesList" id="batchCategoryInput" placeholder="New Category" class="bg-gray-900 text-indigo-300 text-[11px] font-bold py-1 px-2 rounded-xl border border-gray-700 focus:outline-none w-28">
                <button type="button" onclick="applyBatchCategoryToSelectedRows()" class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-500 text-white text-[10px] font-bold rounded-lg transition" title="Selected sabhi rows ki category update karein">Set</button>
            </div>
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
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 w-full max-w-xl overflow-hidden animate-scale-up" onclick="event.stopPropagation()">
            <!-- Header -->
            <div class="p-5 border-b border-gray-100 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-code-merge"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black" id="mergeModalHeaderTitle">Selected Cards Ko Merge Karein</h3>
                        <p class="text-[11px] text-gray-400">In cards ke sizes ko ek sath jodh kar group banayein</p>
                    </div>
                </div>
                <button type="button" onclick="closeMergeCardsModal()" class="h-8 w-8 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto">
                <input type="hidden" id="mergeSourceCardId" value="">

                <!-- 1. Cards Being Merged Previews -->
                <div>
                    <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">
                        📦 Chune Gaye Cards Jinhe Merge Kiya Ja Raha Hai:
                    </label>
                    <div id="mergeCardsPreviewContainer" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 max-h-44 overflow-y-auto p-1 bg-slate-50 rounded-2xl border border-gray-200">
                        <!-- Populated dynamically -->
                    </div>
                </div>

                <!-- 2. Photo Matching & Conflict Status Box -->
                <div id="mergePhotoStatusBox" class="rounded-2xl p-3.5 border text-xs space-y-2">
                    <!-- Populated dynamically via checkMergePhotos() -->
                </div>

                <!-- 3. Target Mode: Naya Group Card vs Merge into Selected Card -->
                <div class="space-y-3 pt-1">
                    <label class="block text-xs font-black text-gray-700 uppercase tracking-wider">
                        🎯 Merge Ka Tarika Chunein:
                    </label>
                    <div class="grid grid-cols-2 gap-2 bg-slate-100 p-1.5 rounded-2xl">
                        <button type="button" onclick="setMergeTargetMode('new')" id="btnMergeModeNew" class="py-2.5 px-3 rounded-xl text-xs font-black transition flex items-center justify-center gap-1.5 bg-white text-indigo-900 shadow-xs border border-indigo-200">
                            <i class="fa-solid fa-sparkles text-amber-500"></i>
                            <span>✨ Naya Alag Group Card</span>
                        </button>
                        <button type="button" onclick="setMergeTargetMode('existing')" id="btnMergeModeExisting" class="py-2.5 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 text-gray-600 hover:text-gray-900">
                            <i class="fa-solid fa-bullseye text-indigo-600"></i>
                            <span>🎯 Kisi 1 Card Me Jodein</span>
                        </button>
                    </div>

                    <!-- Input for New Group Card -->
                    <div id="mergeNewGroupSection" class="space-y-2 bg-indigo-50/50 p-3.5 rounded-2xl border border-indigo-100">
                        <label class="block text-[11px] font-black text-indigo-950 uppercase">
                            Naye Card Ka Title / Product Name:
                        </label>
                        <input type="text" id="mergeNewGroupTitleInput" class="w-full text-xs font-black p-2.5 rounded-xl border border-indigo-200 bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-500" placeholder="e.g. Yorker Measuring Tape (Auto Stop Series)">
                        <p class="text-[11px] text-indigo-700">
                            ✨ Is naye card ke andar sabhi chune gaye cards ke sizes jud jayenge aur purane alag-alag duplicate cards hat jayenge.
                        </p>
                    </div>

                    <!-- Dropdown for Existing Card (ONLY from selected cards) -->
                    <div id="mergeExistingGroupSection" class="hidden space-y-2 bg-slate-50 p-3.5 rounded-2xl border border-gray-200">
                        <label class="block text-[11px] font-black text-gray-800 uppercase">
                            Master Card (Baki sabhi cards ke sizes is card me jodein):
                        </label>
                        <select id="mergeTargetCardSelect" class="w-full text-xs font-bold py-2.5 px-3 rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-indigo-500 shadow-2xs">
                            <!-- Populated dynamically with ONLY the selected cards -->
                        </select>
                    </div>
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3 text-[11px] text-slate-700 space-y-1">
                    <p class="font-bold text-slate-900 flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-600"></i> Suraksha Note:</p>
                    <p>• Merge hone ke baad sabhi sizes (jaise 3M + 5M) ek hi card ke andar matrix me dikhenge.</p>
                    <p>• Purana koi doosra card affect nahi hoga — sirf wahi cards merge honge jo aapne tick kiye hain.</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                <button type="button" onclick="closeMergeCardsModal()" class="px-4 py-2.5 rounded-xl bg-white hover:bg-gray-100 text-gray-700 font-bold text-xs border border-gray-200">
                    Cancel
                </button>
                <button type="button" id="btnConfirmMergeCards" onclick="confirmMergeCards()" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-md shadow-indigo-600/30 flex items-center gap-2 transition active:scale-95 cursor-pointer">
                    <i class="fa-solid fa-code-merge text-amber-300"></i>
                    <span>Merge Selected Cards Now</span>
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

                <!-- Dynamic Category Selector & Quick Preset Pills -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider">Existing & Available Categories:</label>
                        <span class="text-[10px] text-amber-800 bg-amber-100 font-bold px-2 py-0.5 rounded-full" id="bulkCategoryCountPill">Categories</span>
                    </div>
                    <div class="flex flex-wrap gap-1.5 max-h-36 overflow-y-auto p-1.5 bg-slate-50 rounded-2xl border border-gray-200" id="bulkCategoryPresetPillsContainer">
                        <!-- Populated dynamically with all store, photo folder & draft categories -->
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
    <!-- 📱 WHATSAPP & RAW PRICE FORMULA MODAL      -->
    <!-- ========================================== -->
    <div id="rawPriceFormulaModal" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-[9999] hidden flex items-center justify-center p-3 sm:p-4" onclick="closeRawPriceFormulaModal()">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 w-full max-w-6xl overflow-hidden animate-scale-up max-h-[94vh] flex flex-col" onclick="event.stopPropagation()">
            
            <!-- Header -->
            <div class="p-4 sm:p-5 border-b border-gray-100 bg-slate-900 text-white flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center text-xl shadow-md shadow-emerald-500/20">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-black tracking-tight">WhatsApp / Raw Price List & Formula Engine</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">AI Powered</span>
                        </div>
                        <p class="text-[11px] text-gray-400">WhatsApp message ya dealer quote paste karein — AI items extract karega aur Bhada, Less & Margins auto-calculate honge!</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="loadSampleRawPriceText()" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-gray-200 text-xs font-bold transition flex items-center gap-1.5" title="Sample Plywood message load karein">
                        <i class="fa-solid fa-file-invoice text-amber-400"></i>
                        <span>Load Sample Plywood List</span>
                    </button>
                    <button type="button" onclick="closeRawPriceFormulaModal()" class="h-8 w-8 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 flex items-center justify-center text-xs transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- Body (Scrollable) -->
            <div class="p-4 sm:p-5 space-y-4 overflow-y-auto flex-1">
                
                <!-- 1. Textarea & Parse Action Bar -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-gray-700 flex items-center gap-1.5">
                            <i class="fa-solid fa-paste text-indigo-600"></i>
                            <span>WhatsApp Message / Raw Rates Yahan Paste Karein:</span>
                        </label>
                        <span class="text-[11px] text-gray-400 font-mono" id="rawPriceStatsText">0 lines pasted</span>
                    </div>
                    <textarea id="rawPriceTextInput" rows="5" oninput="updateRawPriceStats()" placeholder="WhatsApp se aaya hua text ya price list yahan paste karein (Ctrl + V)...&#10;&#10;Jaise:&#10;➡️ 1) Super Local&#10;18m(14)-46/-, 12m(8)-30/25, 6m(5)-22/-&#10;➡️ 2) Kerla Hardwood Best Quality&#10;18-57/-, 12-46/25, 9-36/75, 6-24/25&#10;➡️ Raipur Super ISO Single - 79/-, Double Door - 10% extra" class="w-full p-3 rounded-2xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono text-xs bg-slate-50 focus:bg-white text-gray-800 leading-relaxed"></textarea>
                    
                    <!-- Controls Bar -->
                    <div class="flex flex-wrap items-center justify-between gap-2.5 p-3 bg-slate-50 rounded-2xl border border-gray-200">
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <div class="flex items-center gap-1.5">
                                <span class="text-gray-500 font-bold text-[11px]">Category:</span>
                                <input type="text" id="rawPriceTargetCategory" list="allCategoriesList" value="Plywoods & Boards" placeholder="Category" class="px-2.5 py-1 rounded-xl border border-gray-300 bg-white font-bold text-xs text-gray-800 focus:ring-1 focus:ring-emerald-500 w-36">
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-gray-500 font-bold text-[11px]">Base Unit:</span>
                                <select id="rawPriceTargetUnit" class="px-2.5 py-1 rounded-xl border border-gray-300 bg-white font-bold text-xs text-gray-800 focus:ring-1 focus:ring-emerald-500">
                                    <option value="auto">Auto-Detect</option>
                                    <option value="sqft" selected>Per Sq. Ft.</option>
                                    <option value="pcs">Per Piece (pcs)</option>
                                    <option value="meter">Per Meter</option>
                                    <option value="box">Per Box</option>
                                    <option value="sheet">Per Sheet</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" onclick="parseRawPriceFast()" id="btnRawPriceFast" class="px-3.5 py-1.5 rounded-xl bg-gray-900 hover:bg-black text-white font-black text-xs shadow-sm flex items-center gap-1.5 transition active:scale-95" title="Bina internet/AI ke turant parse karein">
                                <i class="fa-solid fa-bolt text-yellow-400"></i>
                                <span>⚡ Instant Fast Parse</span>
                            </button>
                            <button type="button" onclick="parseRawPriceAI()" id="btnRawPriceAI" class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs shadow-md shadow-emerald-600/20 flex items-center gap-1.5 transition active:scale-95 cursor-pointer" title="OpenAI / Gemini Vision engine se deep parse karein">
                                <i class="fa-solid fa-wand-magic-sparkles text-emerald-200"></i>
                                <span>🤖 AI Smart Extract</span>
                            </button>
                        </div>
                    </div>

                    <!-- Status Notification Banner -->
                    <div id="rawPriceStatusAlert" class="hidden p-3 rounded-2xl text-xs font-bold flex items-center justify-between">
                        <div class="flex items-center gap-2" id="rawPriceStatusContent">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                            <span>AI extraction in progress...</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Universal Pricing & Formula Bar (Above Preview Table) -->
                <div class="p-3.5 bg-slate-900 text-white rounded-2xl border border-slate-800 shadow-md">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2.5 pb-2 border-b border-slate-800">
                        <div class="flex items-center gap-2">
                            <div class="h-6 w-6 rounded-lg bg-amber-400/20 text-amber-300 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-calculator"></i>
                            </div>
                            <span class="text-xs font-black tracking-wide text-amber-300 uppercase">
                                Dynamic Pricing Formula (Auto-Calculates All Rates)
                            </span>
                        </div>
                        <span class="text-[10px] text-gray-400 font-mono">
                            Landing = (Base &minus; Less%) + Bhada &nbsp;|&nbsp; Rate A/B/C = Landing + Margin
                        </span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5">
                        <!-- Less % -->
                        <div class="bg-slate-800/80 p-2 rounded-xl border border-slate-700/80">
                            <label class="block text-[10px] font-black text-rose-300 uppercase mb-1 flex items-center justify-between">
                                <span>1. Cash Less</span>
                                <span class="text-gray-400 font-normal">%</span>
                            </label>
                            <div class="flex items-center gap-1">
                                <input type="number" step="0.25" id="formulaLessPercent" value="0" oninput="recalculateRawFormulaRates()" placeholder="0" class="w-full bg-slate-950 px-2 py-1 rounded-lg border border-slate-700 text-white font-mono font-black text-xs focus:ring-1 focus:ring-rose-400">
                                <span class="text-gray-400 text-xs font-bold">%</span>
                            </div>
                        </div>

                        <!-- Bhada / Freight -->
                        <div class="bg-slate-800/80 p-2 rounded-xl border border-slate-700/80">
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-[10px] font-black text-amber-300 uppercase">2. Bhada / Freight</label>
                                <select id="formulaFreightMode" onchange="recalculateRawFormulaRates()" class="text-[9px] bg-slate-900 text-amber-300 rounded border border-slate-700 px-1 py-0.5 cursor-pointer focus:ring-1 focus:ring-amber-400">
                                    <option value="flat">₹/sqft (Flat ₹)</option>
                                    <option value="percent">% of Base</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-1">
                                <span id="formulaFreightUnitPrefix" class="text-amber-400 text-xs font-bold font-mono">₹</span>
                                <input type="number" step="0.25" id="formulaFreightPerUnit" value="0" oninput="recalculateRawFormulaRates()" placeholder="0.00" class="w-full bg-slate-950 px-2 py-1 rounded-lg border border-slate-700 text-white font-mono font-black text-xs focus:ring-1 focus:ring-amber-400">
                            </div>
                        </div>

                        <!-- Rate A (Retail) -->
                        <div class="bg-slate-800/80 p-2 rounded-xl border border-slate-700/80">
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-[10px] font-black text-emerald-300 uppercase">3. Rate A (Retail)</label>
                                <select id="formulaRateAMode" onchange="recalculateRawFormulaRates()" class="text-[9px] bg-slate-900 text-emerald-300 rounded border border-slate-700 px-1 py-0.5">
                                    <option value="percent">+ %</option>
                                    <option value="flat">+ ₹ Flat</option>
                                </select>
                            </div>
                            <input type="number" step="0.5" id="formulaRateAValue" value="15" oninput="recalculateRawFormulaRates()" class="w-full bg-slate-950 px-2 py-1 rounded-lg border border-slate-700 text-white font-mono font-black text-xs focus:ring-1 focus:ring-emerald-400">
                        </div>

                        <!-- Rate B (Wholesale) -->
                        <div class="bg-slate-800/80 p-2 rounded-xl border border-slate-700/80">
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-[10px] font-black text-cyan-300 uppercase">4. Rate B (Wholesale)</label>
                                <select id="formulaRateBMode" onchange="recalculateRawFormulaRates()" class="text-[9px] bg-slate-900 text-cyan-300 rounded border border-slate-700 px-1 py-0.5">
                                    <option value="percent">+ %</option>
                                    <option value="flat">+ ₹ Flat</option>
                                </select>
                            </div>
                            <input type="number" step="0.5" id="formulaRateBValue" value="10" oninput="recalculateRawFormulaRates()" class="w-full bg-slate-950 px-2 py-1 rounded-lg border border-slate-700 text-white font-mono font-black text-xs focus:ring-1 focus:ring-cyan-400">
                        </div>

                        <!-- Rate C (Bulk) -->
                        <div class="bg-slate-800/80 p-2 rounded-xl border border-slate-700/80">
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-[10px] font-black text-indigo-300 uppercase">5. Rate C (Bulk)</label>
                                <select id="formulaRateCMode" onchange="recalculateRawFormulaRates()" class="text-[9px] bg-slate-900 text-indigo-300 rounded border border-slate-700 px-1 py-0.5">
                                    <option value="percent">+ %</option>
                                    <option value="flat">+ ₹ Flat</option>
                                </select>
                            </div>
                            <input type="number" step="0.5" id="formulaRateCValue" value="5" oninput="recalculateRawFormulaRates()" class="w-full bg-slate-950 px-2 py-1 rounded-lg border border-slate-700 text-white font-mono font-black text-xs focus:ring-1 focus:ring-indigo-400">
                        </div>

                        <!-- Sheet Multiplier -->
                        <div class="bg-slate-800/80 p-2 rounded-xl border border-slate-700/80 flex flex-col justify-between">
                            <label class="text-[10px] font-black text-purple-300 uppercase mb-1 flex items-center justify-between">
                                <span>6. Sheet Sizes</span>
                                <span class="text-[9px] text-purple-400 font-bold">Auto</span>
                            </label>
                            <label class="flex items-center gap-1.5 text-[10px] text-gray-200 cursor-pointer font-bold select-none py-1">
                                <input type="checkbox" id="formulaExpandSheetSizes" onchange="toggleSheetSizesConfigUI(); recalculateRawFormulaRates();" class="rounded text-purple-500 focus:ring-purple-400 h-3.5 w-3.5">
                                <span>8x4, 7x4... Area</span>
                            </label>
                        </div>
                    </div>

                    <!-- Size Selection Pills & Presets (Visible when Sheet Multiplier is checked) -->
                    <div id="formulaSheetSizesConfigRow" class="hidden mt-2.5 pt-2 border-t border-slate-800 flex flex-wrap items-center justify-between gap-2 text-xs">
                        <div class="flex flex-wrap items-center gap-1.5">
                            <span class="text-[10px] font-black text-purple-300 uppercase mr-1">Active Sizes:</span>
                            <label class="px-2 py-0.5 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-600 text-purple-200 text-[11px] font-bold cursor-pointer flex items-center gap-1.5 transition select-none">
                                <input type="checkbox" class="sheet-size-cb rounded text-purple-500 focus:ring-purple-400 h-3.5 w-3.5" value="8 x 4" checked onchange="recalculateRawFormulaRates()">
                                <span>8 x 4 (32 sqft)</span>
                            </label>
                            <label class="px-2 py-0.5 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-600 text-purple-200 text-[11px] font-bold cursor-pointer flex items-center gap-1.5 transition select-none">
                                <input type="checkbox" class="sheet-size-cb rounded text-purple-500 focus:ring-purple-400 h-3.5 w-3.5" value="7 x 4" checked onchange="recalculateRawFormulaRates()">
                                <span>7 x 4 (28 sqft)</span>
                            </label>
                            <label class="px-2 py-0.5 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-600 text-purple-200 text-[11px] font-bold cursor-pointer flex items-center gap-1.5 transition select-none">
                                <input type="checkbox" class="sheet-size-cb rounded text-purple-500 focus:ring-purple-400 h-3.5 w-3.5" value="6 x 4" checked onchange="recalculateRawFormulaRates()">
                                <span>6 x 4 (24 sqft)</span>
                            </label>
                            <label class="px-2 py-0.5 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-600 text-purple-200 text-[11px] font-bold cursor-pointer flex items-center gap-1.5 transition select-none">
                                <input type="checkbox" class="sheet-size-cb rounded text-purple-500 focus:ring-purple-400 h-3.5 w-3.5" value="7 x 3" checked onchange="recalculateRawFormulaRates()">
                                <span>7 x 3 (21 sqft)</span>
                            </label>
                            <label class="px-2 py-0.5 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-600 text-purple-200 text-[11px] font-bold cursor-pointer flex items-center gap-1.5 transition select-none">
                                <input type="checkbox" class="sheet-size-cb rounded text-purple-500 focus:ring-purple-400 h-3.5 w-3.5" value="6 x 3" checked onchange="recalculateRawFormulaRates()">
                                <span>6 x 3 (18 sqft)</span>
                            </label>
                            
                            <!-- Container for dynamically added custom size pills -->
                            <span id="customSheetSizePillsContainer" class="flex items-center gap-1.5"></span>

                            <button type="button" onclick="promptAddCustomSheetSize()" class="px-2 py-0.5 rounded-lg bg-purple-900/60 hover:bg-purple-800 text-purple-200 border border-purple-500/50 text-[10px] font-bold transition flex items-center gap-1 cursor-pointer" title="Apni marzi ka naya size jodein (jaise 8x3, 9x4, 10x4)">
                                <i class="fa-solid fa-plus text-[9px]"></i>
                                <span>+ Custom Size</span>
                            </button>
                        </div>

                        <!-- Presets -->
                        <div class="flex items-center gap-1.5 text-[10px]">
                            <span class="text-gray-400 font-bold">Presets:</span>
                            <button type="button" onclick="setSheetSizePreset('all')" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-gray-300 hover:text-white font-bold border border-slate-700 transition">
                                All 5 Sizes
                            </button>
                            <button type="button" onclick="setSheetSizePreset('popular')" class="px-2 py-0.5 rounded bg-cyan-950 hover:bg-cyan-900 text-cyan-300 font-bold border border-cyan-800 transition" title="Sirf 8x4 aur 7x4">
                                🏡 8x4 & 7x4 Only
                            </button>
                            <button type="button" onclick="setSheetSizePreset('shuttering_only')" class="px-2 py-0.5 rounded bg-amber-950 hover:bg-amber-900 text-amber-300 font-bold border border-amber-800 transition" title="Shuttering me sirf 8x4 hota hai">
                                🔨 8x4 Only (Shuttering)
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3. Live Preview Table -->
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center justify-between gap-2 px-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <label class="block text-xs font-black text-gray-900 flex items-center gap-1.5">
                                <span>Calculated Products Preview:</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-indigo-100 text-indigo-800" id="rawPricePreviewCount">0 items</span>
                            </label>
                            
                            <!-- Quick Size Removal Shortcuts -->
                            <div class="flex items-center gap-1.5 pr-2 mr-1 border-r border-gray-200">
                                <span class="text-[10px] text-gray-500 font-bold uppercase">Set All Category:</span>
                                <input list="allCategoriesList" id="bulkPreviewCategoryInput" placeholder="Set for all..." class="px-2 py-0.5 rounded-lg border border-indigo-200 text-xs font-bold text-indigo-900 bg-white w-28">
                                <button type="button" onclick="applyBulkCategoryToPreview()" class="px-2 py-0.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold shadow-xs transition active:scale-95" title="Preview ke sabhi items ki category badlein">Apply All</button>
                            </div>
                            <div class="flex items-center gap-1 pl-2 border-l border-gray-200">
                                <span class="text-[10px] text-gray-400 font-bold">Quick Remove:</span>
                                <button type="button" onclick="removeSpecificSizeFromPreview('6 x 3')" class="px-2 py-0.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-[10px] font-bold border border-rose-200 transition" title="Preview se sabhi 6x3 sizes ek click me hatao">
                                    &times; 6x3
                                </button>
                                <button type="button" onclick="removeSpecificSizeFromPreview('7 x 3')" class="px-2 py-0.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-[10px] font-bold border border-rose-200 transition" title="Preview se sabhi 7x3 sizes ek click me hatao">
                                    &times; 7x3
                                </button>
                                <button type="button" onclick="removeSpecificSizeFromPreview('6 x 4')" class="px-2 py-0.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-[10px] font-bold border border-rose-200 transition" title="Preview se sabhi 6x4 sizes ek click me hatao">
                                    &times; 6x4
                                </button>
                            </div>
                        </div>

                        <div class="text-[11px] text-gray-500 font-mono" id="rawPricePreviewSummaryText">
                            <!-- Populated via JS -->
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-2xl overflow-hidden max-h-72 overflow-y-auto shadow-inner bg-white">
                        <table class="w-full text-left text-xs font-medium">
                            <thead class="bg-slate-50 text-[10px] font-black text-gray-500 uppercase border-b border-gray-200 sticky top-0 z-10 shadow-2xs">
                                <tr>
                                    <th class="p-2.5 w-8 text-center">#</th>
                                    <th class="p-2.5 w-24">Category</th>
                                    <th class="p-2.5 min-w-[160px]">Product Name / Grade</th>
                                    <th class="p-2.5 w-28">Thickness / Size</th>
                                    <th class="p-2.5 w-16">Unit</th>
                                    <th class="p-2.5 w-20">Base (₹)</th>
                                    <th class="p-2.5 w-24 text-amber-700 bg-amber-50/50">Landing (₹)</th>
                                    <th class="p-2.5 w-24 text-emerald-700 bg-emerald-50/50">Rate A (₹)</th>
                                    <th class="p-2.5 w-24 text-cyan-700">Rate B (₹)</th>
                                    <th class="p-2.5 w-24 text-indigo-700">Rate C (₹)</th>
                                    <th class="p-2.5 min-w-[130px]">Notes / Extras</th>
                                    <th class="p-2.5 w-10 text-center">Del</th>
                                </tr>
                            </thead>
                            <tbody id="rawPricePreviewBody" class="divide-y divide-gray-100">
                                <tr>
                                    <td colspan="12" class="p-8 text-center text-gray-400 text-xs">
                                        <div class="max-w-sm mx-auto space-y-1.5">
                                            <i class="fa-brands fa-whatsapp text-3xl text-emerald-400"></i>
                                            <p class="font-bold text-gray-600">Upar WhatsApp message paste karein aur "Fast Parse" ya "AI Smart Extract" dabayein</p>
                                            <p class="text-[11px] text-gray-400">Plywood, Timber, Hardware, Flush Doors ya kisi bhi trade ka message turant calculate hoga.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex flex-wrap items-center justify-between gap-2 shrink-0">
                <button type="button" onclick="closeRawPriceFormulaModal()" class="px-4 py-2 rounded-xl bg-white hover:bg-gray-100 text-gray-700 font-bold text-xs border border-gray-200 transition">
                    Cancel
                </button>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" onclick="exportRawPricePreviewToCsv()" class="px-3.5 py-2 rounded-xl bg-white hover:bg-gray-100 text-gray-800 font-bold text-xs border border-gray-300 flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-file-csv text-emerald-600"></i>
                        <span>Export CSV</span>
                    </button>
                    <button type="button" onclick="insertRawPriceRowsToDynamicTable(true)" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-indigo-600 hover:from-emerald-700 hover:to-indigo-700 text-white font-black text-xs shadow-lg shadow-emerald-600/30 flex items-center gap-2 transition active:scale-95 cursor-pointer" title="Direct table listing me dalein taaki aap apni marzi se rows select karke cards bana sakein!">
                        <i class="fa-solid fa-table-list text-sm"></i>
                        <span>📋 Add to Spreadsheet Listing (Recommended)</span>
                    </button>
                    <button type="button" onclick="insertRawPriceRowsAndAutoGroupCards()" class="px-3.5 py-2 rounded-xl bg-white hover:bg-indigo-50 text-indigo-700 font-bold text-xs border border-indigo-200 flex items-center gap-1.5 transition active:scale-95 cursor-pointer" title="Direct cards me bhejna chahein to">
                        <i class="fa-solid fa-bolt text-indigo-500"></i>
                        <span>⚡ Direct Auto-Cards</span>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- ========================================== -->
    <!-- SLIDE-OVER PHOTO GALLERY DRAWER            -->
    <!-- ========================================== -->
    <!-- ========================================== -->
    <!-- 🏢 CONFIGURE MY BUSINESS LINES MODAL       -->
    <!-- ========================================== -->
    <div id="industryConfigModal" class="fixed inset-0 bg-black/70 backdrop-blur-xs z-[9999] hidden flex items-center justify-center p-4" onclick="closeIndustryModuleConfigModal()">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg shadow-xs">
                        <i class="fa-solid fa-industry"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Configure My Active Business Lines</h3>
                        <p class="text-[11px] text-gray-500">Chunein ki aapki dukan par kaun-kaun se vyapar hote hain</p>
                    </div>
                </div>
                <button type="button" onclick="closeIndustryModuleConfigModal()" class="h-8 w-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center text-sm cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-2.5 max-h-80 overflow-y-auto pr-1" id="industryConfigList">
                <!-- Dynamically generated module check items -->
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                <button type="button" onclick="resetDefaultBusinessModules()" class="text-xs font-bold text-gray-500 hover:text-gray-800 cursor-pointer">
                    Reset Defaults
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeIndustryModuleConfigModal()" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" onclick="saveIndustryModuleConfig()" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black shadow-md shadow-indigo-600/30 cursor-pointer">
                        Save Business Lines
                    </button>
                </div>
            </div>
        </div>
    </div>

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
                <div class="flex items-center gap-2">
                    <button type="button" onclick="triggerDevicePhotoUpload()" class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs shadow-xs flex items-center gap-1.5 transition cursor-pointer" id="drawerUploadBtn" title="Apne mobile ya computer se photos upload karein">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Upload Photo</span>
                    </button>
                    <input type="file" id="drawerDirectPhotoInput" accept="image/*" multiple class="hidden" onchange="handleDrawerDirectPhotoUpload(event)">
                    <button type="button" onclick="closeGalleryDrawer()" class="h-8 w-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
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
                        <div class="drawer-img-card border border-gray-200 rounded-2xl p-2 bg-gray-50 hover:bg-indigo-50 hover:border-indigo-400 cursor-pointer text-center group transition" onclick="selectDrawerImage('{{ $g['url'] }}', '{{ $g['asset_url'] }}')" data-name="{{ strtolower($g['name']) }}" data-folder="{{ strtolower($g['folder'] ?? 'general') }}" data-raw-folder="{{ $g['folder'] ?? 'General' }}" data-img-key="{{ strtolower(basename(parse_url($g['url'], PHP_URL_PATH))) }}">
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
        let serverJobProducts = @json($products ?? []);

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
            if (typeof updatePublishButtonText === 'function') updatePublishButtonText();
            if (typeof renderSmartSubGroupRules === 'function') renderSmartSubGroupRules();
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
                    if (activeIndustryModule !== 'ALL' && typeof detectCardModule === 'function' && detectCardModule(r) !== activeIndustryModule) return;
                    const c = (r.group_type || r.category || 'General Hardware').trim();
                    counts[c] = (counts[c] || 0) + 1;
                    total++;
                });
                // Also ensure categories from groupedProductCards exist in tabs even if all rows are already mapped into cards!
                groupedProductCards.forEach(card => {
                    if (activeIndustryModule !== 'ALL' && typeof detectCardModule === 'function' && detectCardModule(card) !== activeIndustryModule) return;
                    const c = (card.category || card.group_type || 'General Hardware').trim();
                    if (counts[c] === undefined) {
                        counts[c] = 0;
                    }
                });
            } else {
                document.querySelectorAll('.table-row-item').forEach(tr => {
                    const trMod = tr.getAttribute('data-module') || '';
                    if (activeIndustryModule !== 'ALL' && trMod && trMod !== activeIndustryModule) return;
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

                    const badgeContent = count > 0 ? count : '✓ Cards';

                    html += `
                        <button type="button" onclick="filterByCategory('${escapeHtml(catName)}')" data-cat-key="${escapeHtml(catName)}" class="cat-pill px-4 py-2 rounded-2xl text-xs font-extrabold ${isActive ? activeColor : 'bg-white hover:bg-slate-50 text-gray-700 border border-gray-200 shadow-xs'} flex items-center gap-2 transition whitespace-nowrap active:scale-95">
                            <i class="fa-solid ${icon} text-[11px] ${isActive ? 'text-white' : dotColor}"></i>
                            <span>${escapeHtml(catName)}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] ${isActive ? 'bg-white/20 text-white' : (count > 0 ? 'bg-slate-100 text-slate-800' : 'bg-emerald-100 text-emerald-800')} font-mono font-bold">${badgeContent}</span>
                        </button>
                    `;
                }
                return html;
            };

            if (dynamicContainer) dynamicContainer.innerHTML = buildTabsHtml(true);
            if (savedContainer) savedContainer.innerHTML = buildTabsHtml(false);
            if (typeof updatePublishButtonText === 'function') updatePublishButtonText();
            if (typeof renderSmartSubGroupRules === 'function') renderSmartSubGroupRules();
        }

        // =========================================================================
        // 🏢 MULTI-INDUSTRY MODULAR ARCHITECTURE (Vyapar Multi-Business Engine)
        // =========================================================================
        const VyaparIndustryModules = {
            plywood: {
                id: 'plywood',
                name: 'Plywood & Timber',
                shortName: 'Plywood',
                icon: 'fa-tree',
                badgeColor: 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                activePillClass: 'bg-amber-600 text-white shadow-lg ring-2 ring-amber-400',
                unit: 'sqft',
                unitLabel: 'Per Sqft / Sheet',
                keywords: ['PLY', 'PLYWOOD', 'BOARD', 'BLOCK BOARD', 'DOOR', 'FLUSH DOOR', 'SHUTTER', 'SHATTER', 'TIMBER', 'LAMINATE', 'SUNMICA', 'VENEER', 'MDF', 'HDF', 'WPC'],
                standardSizes: [
                    { size: '8 x 4 ft (32 sqft)', area: 32, label: '8x4' },
                    { size: '7 x 4 ft (28 sqft)', area: 28, label: '7x4' },
                    { size: '6 x 4 ft (24 sqft)', area: 24, label: '6x4' },
                    { size: '8 x 3 ft (24 sqft)', area: 24, label: '8x3' },
                    { size: '7 x 3 ft (21 sqft)', area: 21, label: '7x3' },
                    { size: '6 x 3 ft (18 sqft)', area: 18, label: '6x3' }
                ],
                subGroups: [
                    {
                        key: 'shuttering',
                        label: 'Shuttering Ply',
                        icon: 'cubes-stacked',
                        color: 'amber',
                        defaultLess: 0,
                        defaultFreightMode: 'flat',
                        defaultFreightVal: 3.00,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 12,
                        unit: 'sqft',
                        match: (txt) => txt.includes('SHUTTER') || txt.includes('SHATTER')
                    },
                    {
                        key: 'block_board',
                        label: 'Block Boards',
                        icon: 'layer-group',
                        color: 'indigo',
                        defaultLess: 2.0,
                        defaultFreightMode: 'flat',
                        defaultFreightVal: 2.50,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 15,
                        unit: 'sqft',
                        match: (txt) => txt.includes('BLOCK BOARD') || txt.includes('PINE BOARD') || (txt.includes('BOARD') && !txt.includes('SWITCH'))
                    },
                    {
                        key: 'flush_door',
                        label: 'Flush Doors',
                        icon: 'door-open',
                        color: 'emerald',
                        defaultLess: 0,
                        defaultFreightMode: 'flat',
                        defaultFreightVal: 2.00,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 18,
                        unit: 'sqft',
                        match: (txt) => txt.includes('FLUSH DOOR') || txt.includes('DOOR') || txt.includes('WINDOW')
                    },
                    {
                        key: 'plywood_bwp',
                        label: 'Marine BWP 710 Plywood',
                        icon: 'anchor',
                        color: 'blue',
                        defaultLess: 3.0,
                        defaultFreightMode: 'flat',
                        defaultFreightVal: 2.50,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 18,
                        unit: 'sqft',
                        match: (txt) => txt.includes('710') || txt.includes('MARINE') || txt.includes('BWP') || txt.includes('WATERPROOF')
                    },
                    {
                        key: 'plywood',
                        label: 'Plywood (MR Commercial)',
                        icon: 'tree',
                        color: 'blue',
                        defaultLess: 2.0,
                        defaultFreightMode: 'flat',
                        defaultFreightVal: 2.50,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 15,
                        unit: 'sqft',
                        match: () => true
                    }
                ]
            },
            plumbing: {
                id: 'plumbing',
                name: 'Plumbing & Pipes',
                shortName: 'Plumbing',
                icon: 'fa-faucet-drip',
                badgeColor: 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                activePillClass: 'bg-blue-600 text-white shadow-lg ring-2 ring-blue-400',
                unit: 'pc',
                unitLabel: 'Per Piece / Length',
                keywords: ['UPVC', 'CPVC', 'SWR', 'PVC', 'PIPE', 'FITTING', 'PLUMB', 'ELBOW', 'TEE', 'SOCKET', 'REDUCER', 'MTA', 'FTA', 'UNION', 'VALVE', 'SOLVENT', 'NIPPLE', 'END CAP', 'AGRI', 'CONDUIT'],
                standardSizes: [
                    { size: '1/2" (15 mm)', label: '1/2"' },
                    { size: '3/4" (20 mm)', label: '3/4"' },
                    { size: '1" (25 mm)', label: '1"' },
                    { size: '1-1/4" (32 mm)', label: '1-1/4"' },
                    { size: '1-1/2" (40 mm)', label: '1-1/2"' },
                    { size: '2" (50 mm)', label: '2"' },
                    { size: '2-1/2" (65 mm)', label: '2-1/2"' },
                    { size: '3" (80 mm)', label: '3"' },
                    { size: '4" (110 mm)', label: '4"' }
                ],
                subGroups: [
                    {
                        key: 'brass_fittings',
                        label: 'Brass Insert Fittings',
                        icon: 'ring',
                        color: 'amber',
                        defaultLess: 28,
                        defaultFreightMode: 'percent',
                        defaultFreightVal: 0,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 25,
                        unit: 'pc',
                        match: (txt) => txt.includes('BRASS') || txt.includes('MTA') || txt.includes('FTA') || txt.includes('BRASS ELBOW') || txt.includes('BRASS TEE') || txt.includes('B.E') || txt.includes('B.T')
                    },
                    {
                        key: 'pipes',
                        label: 'Pipes (Lengths)',
                        icon: 'grip-lines',
                        color: 'blue',
                        defaultLess: 25,
                        defaultFreightMode: 'percent',
                        defaultFreightVal: 0,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 8,
                        unit: 'length',
                        match: (txt) => txt.includes('PIPE') || txt.includes('SCH 40') || txt.includes('SCH 80') || txt.includes('10M') || txt.includes('6M') || txt.includes('10FT') || txt.includes('CONDUIT')
                    },
                    {
                        key: 'valves_acc',
                        label: 'Valves & Solvent / Accessories',
                        icon: 'faucet-drip',
                        color: 'rose',
                        defaultLess: 20,
                        defaultFreightMode: 'percent',
                        defaultFreightVal: 0,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 15,
                        unit: 'pc',
                        match: (txt) => txt.includes('VALVE') || txt.includes('BALL VALVE') || txt.includes('SOLVENT') || txt.includes('CEMENT') || txt.includes('TEFLON') || txt.includes('TAPE')
                    },
                    {
                        key: 'plain_fittings',
                        label: 'Plain PVC Fittings (Elbow/Tee/Socket)',
                        icon: 'circle-nodes',
                        color: 'emerald',
                        defaultLess: 30,
                        defaultFreightMode: 'percent',
                        defaultFreightVal: 0,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 18,
                        unit: 'pc',
                        match: () => true
                    }
                ]
            },
            pumps: {
                id: 'pumps',
                name: 'Pumps & Motors',
                shortName: 'Pumps',
                icon: 'fa-gears',
                badgeColor: 'bg-cyan-500/20 text-cyan-300 border-cyan-500/30',
                activePillClass: 'bg-cyan-600 text-white shadow-lg ring-2 ring-cyan-400',
                unit: 'pc',
                unitLabel: 'Per Unit / Set',
                keywords: ['PUMP', 'MOTOR', 'SUBMERSIBLE', 'MONOBLOCK', 'OPENWELL', 'BOOSTER', 'JET PUMP', 'SEWAGE PUMP', 'V4', 'V6', 'KIRLOSKAR', 'CROMPTON PUMP', 'LUBI', 'TEXMO', 'STARTER', 'PANEL BOARD'],
                standardSizes: [
                    { size: '0.5 HP (0.37 kW) - 1 Phase', label: '0.5 HP' },
                    { size: '1.0 HP (0.75 kW) - 1 Phase', label: '1.0 HP' },
                    { size: '1.5 HP (1.1 kW) - 1 Phase', label: '1.5 HP' },
                    { size: '2.0 HP (1.5 kW) - 1 Phase', label: '2.0 HP' },
                    { size: '3.0 HP (2.2 kW) - 3 Phase', label: '3.0 HP' },
                    { size: '5.0 HP (3.7 kW) - 3 Phase', label: '5.0 HP' }
                ],
                subGroups: [
                    {
                        key: 'submersible_pump',
                        label: 'Borewell Submersible Pumps',
                        icon: 'arrow-down-up-across-line',
                        color: 'cyan',
                        defaultLess: 15,
                        defaultFreightMode: 'flat',
                        defaultFreightVal: 150,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 15,
                        unit: 'set',
                        match: (txt) => txt.includes('SUBMERSIBLE') || txt.includes('BOREWELL') || txt.includes('V4') || txt.includes('V6')
                    },
                    {
                        key: 'monoblock_pump',
                        label: 'Domestic Monoblock Pumps',
                        icon: 'fan',
                        color: 'blue',
                        defaultLess: 12,
                        defaultFreightMode: 'flat',
                        defaultFreightVal: 100,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 12,
                        unit: 'pc',
                        match: (txt) => txt.includes('MONOBLOCK') || txt.includes('DOMESTIC') || txt.includes('SELF PRIMING')
                    },
                    {
                        key: 'openwell_pump',
                        label: 'Openwell Submersible Pumps',
                        icon: 'water',
                        color: 'teal',
                        defaultLess: 14,
                        defaultFreightMode: 'flat',
                        defaultFreightVal: 150,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 14,
                        unit: 'pc',
                        match: (txt) => txt.includes('OPENWELL') || txt.includes('OPEN WELL')
                    },
                    {
                        key: 'booster_pump',
                        label: 'Pressure Booster & Specialty Pumps',
                        icon: 'gauge-high',
                        color: 'indigo',
                        defaultLess: 10,
                        defaultFreightMode: 'flat',
                        defaultFreightVal: 100,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 18,
                        unit: 'pc',
                        match: (txt) => txt.includes('BOOSTER') || txt.includes('PRESSURE') || txt.includes('SEWAGE') || txt.includes('DRAINAGE')
                    },
                    {
                        key: 'pump_accessories',
                        label: 'Starters, Control Panels & Cables',
                        icon: 'microchip',
                        color: 'amber',
                        defaultLess: 18,
                        defaultFreightMode: 'flat',
                        defaultFreightVal: 50,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 15,
                        unit: 'pc',
                        match: () => true
                    }
                ]
            },
            electricals: {
                id: 'electricals',
                name: 'Electricals & Wiring',
                shortName: 'Electricals',
                icon: 'fa-bolt',
                badgeColor: 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                activePillClass: 'bg-amber-600 text-white shadow-lg ring-2 ring-amber-400',
                unit: 'coil',
                unitLabel: 'Per Coil / Piece',
                keywords: ['WIRE', 'CABLE', 'SWITCH', 'SOCKET', 'MCB', 'RCCB', 'DB', 'DISTRIBUTION', 'POLYCAB', 'HAVELLS', 'ANCHOR', 'ROMA', 'LEGRAND', 'SCHNEIDER', 'FINOLEX', 'ELECTRICAL', 'MODULAR', 'PANEL LIGHT', 'BULB', 'BATTEN', 'LED'],
                standardSizes: [
                    { size: '0.75 sq mm (90m Coil)', label: '0.75mm' },
                    { size: '1.0 sq mm (90m Coil)', label: '1.0mm' },
                    { size: '1.5 sq mm (90m Coil)', label: '1.5mm' },
                    { size: '2.5 sq mm (90m Coil)', label: '2.5mm' },
                    { size: '4.0 sq mm (90m Coil)', label: '4.0mm' },
                    { size: '6.0 sq mm (90m Coil)', label: '6.0mm' },
                    { size: '10.0 sq mm (90m Coil)', label: '10.0mm' }
                ],
                subGroups: [
                    {
                        key: 'wires_cables',
                        label: 'FR / FRLS Copper Wires (90m Coils)',
                        icon: 'lines-leaning',
                        color: 'rose',
                        defaultLess: 22,
                        defaultFreightMode: 'percent',
                        defaultFreightVal: 0,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 10,
                        unit: 'coil',
                        match: (txt) => txt.includes('WIRE') || txt.includes('CABLE') || txt.includes('SQ MM') || txt.includes('COIL') || txt.includes('COPPER')
                    },
                    {
                        key: 'modular_switches',
                        label: 'Modular Switches & Sockets',
                        icon: 'toggle-on',
                        color: 'indigo',
                        defaultLess: 42,
                        defaultFreightMode: 'percent',
                        defaultFreightVal: 0,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 20,
                        unit: 'pc',
                        match: (txt) => (txt.includes('SWITCH') || txt.includes('SOCKET') || txt.includes('FAN REGULATOR')) && !txt.includes('MCB')
                    },
                    {
                        key: 'modular_plates',
                        label: 'Modular Cover Plates (1M - 18M)',
                        icon: 'square',
                        color: 'cyan',
                        defaultLess: 45,
                        defaultFreightMode: 'percent',
                        defaultFreightVal: 0,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 22,
                        unit: 'pc',
                        match: (txt) => txt.includes('PLATE') || txt.includes('MODULAR COVER') || txt.includes('GRID')
                    },
                    {
                        key: 'switchgear_mcb',
                        label: 'MCBs, RCCBs & Distribution Boards',
                        icon: 'shield-halved',
                        color: 'amber',
                        defaultLess: 25,
                        defaultFreightMode: 'percent',
                        defaultFreightVal: 0,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 15,
                        unit: 'pc',
                        match: (txt) => txt.includes('MCB') || txt.includes('ISOLATOR') || txt.includes('RCCB') || txt.includes('DB BOX') || txt.includes('DISTRIBUTION')
                    },
                    {
                        key: 'lighting_led',
                        label: 'LED Lights, Panels & Battens',
                        icon: 'lightbulb',
                        color: 'emerald',
                        defaultLess: 28,
                        defaultFreightMode: 'percent',
                        defaultFreightVal: 0,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 18,
                        unit: 'pc',
                        match: () => true
                    }
                ]
            },
            sanitaryware: {
                id: 'sanitaryware',
                name: 'Sanitaryware & Bath',
                shortName: 'Sanitaryware',
                icon: 'fa-bath',
                badgeColor: 'bg-teal-500/20 text-teal-300 border-teal-500/30',
                activePillClass: 'bg-teal-600 text-white shadow-lg ring-2 ring-teal-400',
                unit: 'pc',
                unitLabel: 'Per Piece / Set',
                keywords: ['BASIN', 'COMMODE', 'TOILET', 'CLOSET', 'EWC', 'ORISSA', 'PAN', 'URINAL', 'CISTERN', 'TAP', 'FAUCET', 'SHOWER', 'DIVERTER', 'SANITARY', 'BATH', 'CP FITTING', 'JAQUAR', 'HINDWARE', 'CERA', 'PARRYWARE', 'VANITY'],
                standardSizes: [
                    { size: 'One Piece Toilet (S-Trap 220mm)', label: '1-Pc 220mm' },
                    { size: 'One Piece Toilet (S-Trap 300mm)', label: '1-Pc 300mm' },
                    { size: 'Wall Hung WC with Soft-Close', label: 'Wall Hung' },
                    { size: 'Table Top Basin (18" x 14")', label: 'Table Top' },
                    { size: 'Counter Basin (20" x 16")', label: 'Counter' },
                    { size: 'Orissa Pan (21 Inch)', label: 'Orissa 21"' }
                ],
                subGroups: [
                    {
                        key: 'ceramic_toilets',
                        label: 'Ceramic Toilets & Closets (EWC/1-Pc)',
                        icon: 'toilet',
                        color: 'teal',
                        defaultLess: 15,
                        defaultFreightMode: 'flat',
                        defaultFreightVal: 200,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 20,
                        unit: 'pc',
                        match: (txt) => txt.includes('TOILET') || txt.includes('COMMODE') || txt.includes('CLOSET') || txt.includes('EWC') || txt.includes('ORISSA') || txt.includes('PAN') || txt.includes('URINAL')
                    },
                    {
                        key: 'wash_basins',
                        label: 'Wash Basins & Vanity Cabinets',
                        icon: 'sink',
                        color: 'blue',
                        defaultLess: 18,
                        defaultFreightMode: 'flat',
                        defaultFreightVal: 100,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 22,
                        unit: 'pc',
                        match: (txt) => txt.includes('BASIN') || txt.includes('VANITY') || txt.includes('SINK')
                    },
                    {
                        key: 'cp_fittings',
                        label: 'CP Brass Taps & Wall Mixers',
                        icon: 'faucet',
                        color: 'amber',
                        defaultLess: 30,
                        defaultFreightMode: 'percent',
                        defaultFreightVal: 0,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 25,
                        unit: 'pc',
                        match: (txt) => txt.includes('TAP') || txt.includes('FAUCET') || txt.includes('MIXER') || txt.includes('DIVERTER') || txt.includes('COCK') || txt.includes('SPOUT')
                    },
                    {
                        key: 'bath_acc',
                        label: 'Showers, Cisterns & Accessories',
                        icon: 'shower',
                        color: 'indigo',
                        defaultLess: 25,
                        defaultFreightMode: 'percent',
                        defaultFreightVal: 0,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 20,
                        unit: 'pc',
                        match: () => true
                    }
                ]
            },
            electronics: {
                id: 'electronics',
                name: 'Appliances & Electronics',
                shortName: 'Electronics',
                icon: 'fa-tv',
                badgeColor: 'bg-purple-500/20 text-purple-300 border-purple-500/30',
                activePillClass: 'bg-purple-600 text-white shadow-lg ring-2 ring-purple-400',
                unit: 'pc',
                unitLabel: 'Per Unit',
                keywords: ['FAN', 'GEYSER', 'WATER HEATER', 'COOLER', 'TV', 'TELEVISION', 'AC', 'AIR CONDITIONER', 'REFRIGERATOR', 'FRIDGE', 'MIXER', 'INDUCTION', 'IRON', 'ELECTRONIC', 'APPLIANCE', 'LED TV', 'CHIMNEY', 'HAVELLS GEYSER', 'BAJAJ'],
                standardSizes: [
                    { size: 'Ceiling Fan 1200 mm (48 Inch)', label: 'Fan 48"' },
                    { size: 'Storage Geyser 15 Litre (5 Star)', label: 'Geyser 15L' },
                    { size: 'Storage Geyser 25 Litre (5 Star)', label: 'Geyser 25L' },
                    { size: 'Desert Air Cooler 70 Litre', label: 'Cooler 70L' },
                    { size: 'Smart LED TV 43 Inch (4K UHD)', label: 'TV 43"' },
                    { size: 'Split AC 1.5 Ton (3 Star Inverter)', label: 'AC 1.5T' }
                ],
                subGroups: [
                    {
                        key: 'cooling_fans',
                        label: 'Ceiling, Exhaust & Table Fans',
                        icon: 'fan',
                        color: 'cyan',
                        defaultLess: 12,
                        defaultFreightMode: 'flat',
                        defaultFreightVal: 50,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 12,
                        unit: 'pc',
                        match: (txt) => txt.includes('FAN')
                    },
                    {
                        key: 'water_heaters',
                        label: 'Storage & Instant Water Geysers',
                        icon: 'fire-burner',
                        color: 'amber',
                        defaultLess: 15,
                        defaultFreightMode: 'flat',
                        defaultFreightVal: 100,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 15,
                        unit: 'pc',
                        match: (txt) => txt.includes('GEYSER') || txt.includes('WATER HEATER') || txt.includes('HEATER')
                    },
                    {
                        key: 'air_coolers',
                        label: 'Air Coolers (Desert / Personal)',
                        icon: 'snowflake',
                        color: 'blue',
                        defaultLess: 10,
                        defaultFreightMode: 'flat',
                        defaultFreightVal: 150,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 14,
                        unit: 'pc',
                        match: (txt) => txt.includes('COOLER')
                    },
                    {
                        key: 'kitchen_appliances',
                        label: 'Kitchen Appliances (Mixer/Induction/Chimney)',
                        icon: 'blender',
                        color: 'emerald',
                        defaultLess: 15,
                        defaultFreightMode: 'flat',
                        defaultFreightVal: 50,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 18,
                        unit: 'pc',
                        match: (txt) => txt.includes('MIXER') || txt.includes('GRINDER') || txt.includes('INDUCTION') || txt.includes('CHIMNEY') || txt.includes('TOASTER') || txt.includes('KETTLE') || txt.includes('IRON')
                    },
                    {
                        key: 'home_displays',
                        label: 'Smart TVs & Large Entertainment',
                        icon: 'tv',
                        color: 'purple',
                        defaultLess: 8,
                        defaultFreightMode: 'flat',
                        defaultFreightVal: 200,
                        defaultMarginMode: 'percent',
                        defaultMarginVal: 10,
                        unit: 'pc',
                        match: () => true
                    }
                ]
            }
        };

        let activeIndustryModule = 'ALL';

        function getEnabledIndustryModules() {
            try {
                const stored = localStorage.getItem('vyapar_enabled_modules');
                if (stored) {
                    const parsed = JSON.parse(stored);
                    if (Array.isArray(parsed) && parsed.length > 0) return parsed;
                }
            } catch (e) {}
            return ['plywood', 'plumbing', 'pumps', 'electricals', 'sanitaryware', 'electronics'];
        }

        function initIndustryModules() {
            renderIndustryModulePills();
        }

        function renderIndustryModulePills() {
            const container = document.getElementById('industryModulePills');
            if (!container) return;

            const enabled = getEnabledIndustryModules();

            let html = `
                <button type="button" onclick="switchActiveIndustryModule('ALL')" class="px-3.5 py-1.5 rounded-2xl text-xs font-black transition cursor-pointer flex items-center gap-2 whitespace-nowrap active:scale-95 ${activeIndustryModule === 'ALL' ? 'bg-white text-gray-900 shadow-md ring-2 ring-white/50' : 'bg-slate-800/80 hover:bg-slate-700 text-gray-300 border border-slate-700/60'}">
                    <i class="fa-solid fa-shapes text-indigo-400"></i>
                    <span>All Businesses (Mix)</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[9px] bg-slate-900/60 text-gray-300 font-mono">${dynamicRows.length + groupedProductCards.length}</span>
                </button>
            `;

            enabled.forEach(modKey => {
                const mod = VyaparIndustryModules[modKey];
                if (!mod) return;
                const isActive = (activeIndustryModule === modKey);

                const modCardCount = groupedProductCards.filter(c => detectCardModule(c) === modKey).length;
                const modRowCount = dynamicRows.filter(r => detectCardModule(r) === modKey).length;
                const modTotal = modCardCount + modRowCount;

                html += `
                    <button type="button" onclick="switchActiveIndustryModule('${modKey}')" class="px-3.5 py-1.5 rounded-2xl text-xs font-black transition cursor-pointer flex items-center gap-2 whitespace-nowrap active:scale-95 ${isActive ? mod.activePillClass : 'bg-slate-800/80 hover:bg-slate-700 text-gray-300 border border-slate-700/60'}">
                        <i class="fa-solid ${mod.icon} text-amber-300"></i>
                        <span>${mod.name}</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[9px] ${isActive ? 'bg-white/20 text-white' : 'bg-slate-900/60 text-gray-400'} font-mono">${modTotal}</span>
                    </button>
                `;
            });

            container.innerHTML = html;
        }

        function switchActiveIndustryModule(modKey) {
            activeIndustryModule = modKey;
            renderIndustryModulePills();
            refreshCategoryFilterTabs();
            applyCombinedFilter();
            if (typeof renderSmartSubGroupRules === 'function') renderSmartSubGroupRules();
            if (typeof updatePublishButtonText === 'function') updatePublishButtonText();
        }

        function openIndustryModuleConfigModal() {
            const modal = document.getElementById('industryConfigModal');
            const list = document.getElementById('industryConfigList');
            if (!modal || !list) return;

            const enabled = getEnabledIndustryModules();
            let html = '';

            Object.keys(VyaparIndustryModules).forEach(key => {
                const mod = VyaparIndustryModules[key];
                const isChecked = enabled.includes(key);

                html += `
                    <label class="flex items-center justify-between p-3 rounded-2xl border ${isChecked ? 'border-indigo-200 bg-indigo-50/40' : 'border-gray-200 bg-gray-50/50'} hover:border-indigo-300 cursor-pointer transition select-none">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-xl ${isChecked ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600'} flex items-center justify-center text-sm shadow-xs">
                                <i class="fa-solid ${mod.icon}"></i>
                            </div>
                            <div>
                                <div class="text-xs font-black text-gray-900">${mod.name}</div>
                                <div class="text-[10px] text-gray-500">${mod.unitLabel} • ${mod.subGroups.length} Sub-Categories</div>
                            </div>
                        </div>
                        <input type="checkbox" name="module_config_chk" value="${key}" ${isChecked ? 'checked' : ''} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                    </label>
                `;
            });

            list.innerHTML = html;
            modal.classList.remove('hidden');
        }

        function closeIndustryModuleConfigModal() {
            const modal = document.getElementById('industryConfigModal');
            if (modal) modal.classList.add('hidden');
        }

        function saveIndustryModuleConfig() {
            const checked = Array.from(document.querySelectorAll('input[name="module_config_chk"]:checked')).map(cb => cb.value);
            if (checked.length === 0) {
                alert('Kripya kam se kam ek business line select karein.');
                return;
            }
            localStorage.setItem('vyapar_enabled_modules', JSON.stringify(checked));
            closeIndustryModuleConfigModal();
            if (activeIndustryModule !== 'ALL' && !checked.includes(activeIndustryModule)) {
                activeIndustryModule = 'ALL';
            }
            renderIndustryModulePills();
            refreshCategoryFilterTabs();
            applyCombinedFilter();
            if (typeof showToast === 'function') showToast('Aapki business lines safalta-purvak update ho gayi!', 'success');
        }

        function resetDefaultBusinessModules() {
            const all = ['plywood', 'plumbing', 'pumps', 'electricals', 'sanitaryware', 'electronics'];
            localStorage.setItem('vyapar_enabled_modules', JSON.stringify(all));
            openIndustryModuleConfigModal();
        }

        function detectCardModule(item) {
            if (!item) return 'general';
            if (item.industry_module && VyaparIndustryModules[item.industry_module]) {
                return item.industry_module;
            }
            const txt = `${item.parent_name || item.name || item.product_name || ''} ${item.category || item.group_type || ''} ${item.description || ''} ${item.variants?.[0]?.size || item.size || ''}`.toUpperCase();

            // 1. Plywood & Boards
            for (const kw of VyaparIndustryModules.plywood.keywords) {
                if (txt.includes(kw)) return 'plywood';
            }
            // 2. Plumbing & Pipes
            for (const kw of VyaparIndustryModules.plumbing.keywords) {
                if (txt.includes(kw)) return 'plumbing';
            }
            // 3. Pumps & Motors
            for (const kw of VyaparIndustryModules.pumps.keywords) {
                if (txt.includes(kw)) return 'pumps';
            }
            // 4. Electricals & Wires
            for (const kw of VyaparIndustryModules.electricals.keywords) {
                if (txt.includes(kw)) return 'electricals';
            }
            // 5. Sanitaryware & Bath
            for (const kw of VyaparIndustryModules.sanitaryware.keywords) {
                if (txt.includes(kw)) return 'sanitaryware';
            }
            // 6. Electronics & Appliances
            for (const kw of VyaparIndustryModules.electronics.keywords) {
                if (txt.includes(kw)) return 'electronics';
            }
            return 'general';
        }

        function applyIndustryStandardSizes(cardId) {
            const card = groupedProductCards.find(c => c.card_id === cardId);
            if (!card) return;

            const modKey = detectCardModule(card);
            const mod = VyaparIndustryModules[modKey] || VyaparIndustryModules['plywood'];
            const stdSizes = mod.standardSizes || [];

            if (stdSizes.length === 0) {
                alert('Is category ke liye koi predefined standard sizes available nahi hain.');
                return;
            }

            if (!Array.isArray(card.variants)) {
                card.variants = [];
            }

            const existingSizes = card.variants.map(v => (v.size || '').trim().toLowerCase());
            
            const baseVariant = card.variants[0] || {};
            const baseCost = parseFloat(baseVariant.purchase_cost) || 0;
            const basePrice = parseFloat(baseVariant.retail_price) || 0;
            const baseMrp = parseFloat(baseVariant.mrp) || 0;

            let sqftCost = 0;
            let sqftPrice = 0;
            let sqftMrp = 0;
            if (modKey === 'plywood') {
                let baseArea = 32;
                if (baseVariant.size) {
                    const m = baseVariant.size.match(/(\d+)\s*x\s*(\d+)/i);
                    if (m) baseArea = (parseFloat(m[1]) * parseFloat(m[2])) || 32;
                }
                if (baseCost > 0) sqftCost = baseCost / baseArea;
                if (basePrice > 0) sqftPrice = basePrice / baseArea;
                if (baseMrp > 0) sqftMrp = baseMrp / baseArea;
            }

            let addedCount = 0;
            stdSizes.forEach(item => {
                const sizeName = item.size;
                if (existingSizes.some(s => s === sizeName.toLowerCase() || (item.label && s.includes(item.label.toLowerCase())))) {
                    return;
                }

                let variantCost = baseCost;
                let variantPrice = basePrice;
                let variantMrp = baseMrp;

                if (modKey === 'plywood' && item.area && sqftPrice > 0) {
                    variantCost = Math.round(sqftCost * item.area);
                    variantPrice = Math.round(sqftPrice * item.area);
                    variantMrp = Math.round(sqftMrp * item.area);
                }

                card.variants.push({
                    id: 'v_auto_' + Date.now() + '_' + Math.random().toString(36).substring(2, 6),
                    size: sizeName,
                    sku: (card.parent_name || 'PROD').substring(0, 3).toUpperCase() + '-' + (item.label || 'SZ'),
                    hsn: baseVariant.hsn || '',
                    packing: baseVariant.packing || '1 Pc',
                    purchase_cost: variantCost > 0 ? variantCost : '',
                    retail_price: variantPrice > 0 ? variantPrice : '',
                    mrp: variantMrp > 0 ? variantMrp : '',
                    stock: baseVariant.stock || 10
                });
                addedCount++;
            });

            if (addedCount === 0) {
                if (typeof showToast === 'function') showToast(`Is card me pehle se hi ${mod.shortName} ke standard sizes maujood hain.`, 'info');
                return;
            }

            saveToLocalStorage();
            renderGroupedProductCards();
            if (typeof autoSaveDraftToBackend === 'function') autoSaveDraftToBackend();
            if (typeof showToast === 'function') {
                showToast(`✓ ${addedCount} standard sizes (${mod.name}) card me safalta-purvak add ho gaye!`, 'success');
            }
        }

        // =========================================================================
        // ⚡ SMART CATEGORY SUB-GROUP MULTI-MARGIN & FREIGHT ENGINE
        // =========================================================================
        let activeSubGroupsMap = {};

        function classifySubGroup(name, category, size) {
            const text = `${name || ''} ${size || ''} ${category || ''}`.toUpperCase();

            // Determine target module
            let targetModKey = (activeIndustryModule !== 'ALL') ? activeIndustryModule : detectCardModule({ parent_name: name, category, size });
            const mod = VyaparIndustryModules[targetModKey];
            if (mod && Array.isArray(mod.subGroups)) {
                for (const sg of mod.subGroups) {
                    if (typeof sg.match === 'function' && sg.match(text)) {
                        return {
                            key: sg.key,
                            label: sg.label,
                            icon: sg.icon,
                            color: sg.color,
                            defaultLess: sg.defaultLess,
                            defaultFreightMode: sg.defaultFreightMode,
                            defaultFreightVal: sg.defaultFreightVal,
                            defaultMarginMode: sg.defaultMarginMode,
                            defaultMarginVal: sg.defaultMarginVal,
                            unit: sg.unit || mod.unit
                        };
                    }
                }
            }

            // Default Generic Sub-Group
            const catClean = (category || 'General Hardware').trim();
            return {
                key: catClean.toLowerCase().replace(/[^a-z0-9]/g, '_'),
                label: catClean,
                icon: 'tag',
                color: 'indigo',
                defaultLess: 0,
                defaultFreightMode: 'flat',
                defaultFreightVal: 0,
                defaultMarginMode: 'percent',
                defaultMarginVal: 15,
                unit: 'pc'
            };
        }

        function renderSmartSubGroupRules() {
            const ruleCard = document.getElementById('smartSubGroupRuleCard');
            const container = document.getElementById('smartSubGroupRowsContainer');
            const catBadge = document.getElementById('smartSubGroupActiveCatBadge');
            if (!ruleCard || !container) return;

            // Collect items matching current category
            const normCat = (activeCategory || 'ALL').trim().toLowerCase();
            let targetRows = (normCat === 'all') 
                ? dynamicRows 
                : dynamicRows.filter(r => (r.group_type || r.category || 'General Hardware').trim().toLowerCase() === normCat);
            
            let targetCards = (normCat === 'all')
                ? groupedProductCards
                : groupedProductCards.filter(c => (c.category || '').trim().toLowerCase() === normCat);

            if (activeIndustryModule !== 'ALL') {
                targetRows = targetRows.filter(r => detectCardModule(r) === activeIndustryModule);
                targetCards = targetCards.filter(c => detectCardModule(c) === activeIndustryModule);
            }

            const totalItems = targetRows.length + targetCards.length;
            if (totalItems === 0) {
                ruleCard.classList.add('hidden');
                return;
            }

            ruleCard.classList.remove('hidden');
            if (catBadge) {
                catBadge.innerText = (activeCategory === 'ALL') ? `All Categories (${totalItems} Items)` : `📁 ${activeCategory} (${totalItems} Items)`;
            }

            // Classify all items into sub-groups
            const groups = {};
            targetRows.forEach(r => {
                const sg = classifySubGroup(r.product_name, r.category || r.group_type, r.size);
                if (!groups[sg.key]) {
                    groups[sg.key] = { meta: sg, rows: [], cardVariants: [] };
                }
                groups[sg.key].rows.push(r);
            });

            targetCards.forEach(c => {
                const sg = classifySubGroup(c.parent_name, c.category, c.variants?.[0]?.size);
                if (!groups[sg.key]) {
                    groups[sg.key] = { meta: sg, rows: [], cardVariants: [] };
                }
                (c.variants || []).forEach(v => {
                    groups[sg.key].cardVariants.push({ card: c, variant: v });
                });
            });

            activeSubGroupsMap = groups;

            // Load saved rules from localStorage
            let savedRules = {};
            try {
                savedRules = JSON.parse(localStorage.getItem('vyapar_subgroup_rules') || '{}');
            } catch (e) {
                savedRules = {};
            }

            let html = '';
            Object.keys(groups).forEach(key => {
                const g = groups[key];
                const meta = g.meta;
                const totalInSg = g.rows.length + g.cardVariants.length;
                if (totalInSg === 0) return;

                // Compute average base rate
                let sumBase = 0;
                let countBase = 0;
                g.rows.forEach(r => {
                    const b = parseFloat(r.raw_rate || r.purchase_cost || r.cost_price || r.mrp || 0);
                    if (b > 0) { sumBase += b; countBase++; }
                });
                g.cardVariants.forEach(cv => {
                    const b = parseFloat(cv.variant.raw_rate || cv.variant.purchase_cost || cv.variant.mrp || 0);
                    if (b > 0) { sumBase += b; countBase++; }
                });
                const avgBase = countBase > 0 ? (sumBase / countBase) : 0;

                const ruleKey = (activeCategory !== 'ALL' ? activeCategory : 'GLOBAL') + '_' + key;
                const rule = savedRules[ruleKey] || {
                    less_pct: meta.defaultLess,
                    freight_mode: meta.defaultFreightMode,
                    freight_val: meta.defaultFreightVal,
                    margin_mode: meta.defaultMarginMode,
                    margin_val: meta.defaultMarginVal
                };

                // Calculate preview
                const afterLess = avgBase * (1 - (rule.less_pct / 100));
                const freightAmt = (rule.freight_mode === 'percent') ? (avgBase * (rule.freight_val / 100)) : rule.freight_val;
                const landing = Math.round((afterLess + freightAmt) * 100) / 100;
                const estSelling = (rule.margin_mode === 'percent') ? Math.round(landing * (1 + (rule.margin_val / 100)) * 100) / 100 : Math.round((landing + rule.margin_val) * 100) / 100;

                html += `
                    <div class="bg-slate-800/80 hover:bg-slate-800 border border-slate-700/80 rounded-2xl p-3 flex flex-wrap items-center justify-between gap-3 transition" id="sg_row_${key}">
                        <!-- Left: Subgroup Info -->
                        <div class="flex items-center gap-2.5 min-w-[200px]">
                            <div class="h-9 w-9 rounded-xl bg-${meta.color}-500/20 text-${meta.color}-400 flex items-center justify-center text-sm border border-${meta.color}-500/30 shrink-0">
                                <i class="fa-solid fa-${meta.icon}"></i>
                            </div>
                            <div>
                                <div class="text-xs font-black text-white flex items-center gap-1.5">
                                    <span>${meta.label}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-indigo-500/30 text-indigo-300 font-mono font-bold">${totalInSg} Items</span>
                                </div>
                                <div class="text-[10px] text-gray-400 font-mono">Avg Base: ₹${avgBase.toFixed(2)} / ${meta.unit}</div>
                            </div>
                        </div>

                        <!-- Center: Inputs (Less + Freight + Margin) -->
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <!-- Less % -->
                            <div class="flex items-center gap-1 bg-slate-950 px-2.5 py-1 rounded-xl border border-slate-700/80">
                                <span class="text-[10px] text-rose-400 font-bold uppercase">Less:</span>
                                <input type="number" step="0.25" id="sg_less_${key}" value="${rule.less_pct}" oninput="updateSubGroupLivePreview('${key}', ${avgBase})" class="w-12 bg-transparent text-white font-mono text-xs font-bold text-center focus:outline-none focus:ring-1 focus:ring-rose-400 rounded">
                                <span class="text-[10px] text-gray-400 font-bold">%</span>
                            </div>

                            <!-- Bhada / Freight -->
                            <div class="flex items-center gap-1 bg-slate-950 px-2.5 py-1 rounded-xl border border-slate-700/80">
                                <span class="text-[10px] text-amber-400 font-bold uppercase">Bhada:</span>
                                <select id="sg_freight_mode_${key}" onchange="updateSubGroupLivePreview('${key}', ${avgBase})" class="bg-slate-900 text-[10px] text-amber-300 font-bold rounded border border-slate-700 px-1 py-0.5 cursor-pointer">
                                    <option value="flat" ${rule.freight_mode === 'flat' ? 'selected' : ''}>₹/unit</option>
                                    <option value="percent" ${rule.freight_mode === 'percent' ? 'selected' : ''}>%</option>
                                </select>
                                <input type="number" step="0.25" id="sg_freight_val_${key}" value="${rule.freight_val}" oninput="updateSubGroupLivePreview('${key}', ${avgBase})" class="w-14 bg-transparent text-white font-mono text-xs font-bold text-center focus:outline-none focus:ring-1 focus:ring-amber-400 rounded">
                            </div>

                            <!-- Margin % or Flat -->
                            <div class="flex items-center gap-1 bg-slate-950 px-2.5 py-1 rounded-xl border border-slate-700/80">
                                <span class="text-[10px] text-emerald-400 font-bold uppercase">Margin:</span>
                                <select id="sg_margin_mode_${key}" onchange="updateSubGroupLivePreview('${key}', ${avgBase})" class="bg-slate-900 text-[10px] text-emerald-300 font-bold rounded border border-slate-700 px-1 py-0.5 cursor-pointer">
                                    <option value="percent" ${rule.margin_mode === 'percent' ? 'selected' : ''}>+ %</option>
                                    <option value="flat" ${rule.margin_mode === 'flat' ? 'selected' : ''}>+ ₹ Flat</option>
                                </select>
                                <input type="number" step="0.5" id="sg_margin_val_${key}" value="${rule.margin_val}" oninput="updateSubGroupLivePreview('${key}', ${avgBase})" class="w-14 bg-transparent text-white font-mono text-xs font-bold text-center focus:outline-none focus:ring-1 focus:ring-emerald-400 rounded">
                            </div>
                        </div>

                        <!-- Right: Live Preview & Apply Button -->
                        <div class="flex items-center gap-3">
                            <div class="text-right">
                                <div class="text-[9px] text-gray-400 uppercase font-bold">Est. Selling</div>
                                <div class="text-xs font-mono font-black text-emerald-400" id="sg_preview_${key}">₹${estSelling.toFixed(2)}</div>
                            </div>
                            <button type="button" onclick="applySubGroupRule('${key}')" id="btn_apply_sg_${key}" class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-xs shadow-md shadow-emerald-600/30 flex items-center gap-1.5 transition active:scale-95 cursor-pointer" title="Sirf is subgroup ke sabhi ${totalInSg} items ke rates calculate karein">
                                <i class="fa-solid fa-bolt text-amber-300 text-xs"></i>
                                <span>Apply (${totalInSg})</span>
                            </button>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        function updateSubGroupLivePreview(key, avgBase) {
            const lessPct = parseFloat(document.getElementById(`sg_less_${key}`)?.value) || 0;
            const freightMode = document.getElementById(`sg_freight_mode_${key}`)?.value || 'flat';
            const freightVal = parseFloat(document.getElementById(`sg_freight_val_${key}`)?.value) || 0;
            const marginMode = document.getElementById(`sg_margin_mode_${key}`)?.value || 'percent';
            const marginVal = parseFloat(document.getElementById(`sg_margin_val_${key}`)?.value) || 0;

            const afterLess = avgBase * (1 - (lessPct / 100));
            const freightAmt = (freightMode === 'percent') ? (avgBase * (freightVal / 100)) : freightVal;
            const landing = Math.round((afterLess + freightAmt) * 100) / 100;
            const estSelling = (marginMode === 'percent') ? Math.round(landing * (1 + (marginVal / 100)) * 100) / 100 : Math.round((landing + marginVal) * 100) / 100;

            const previewEl = document.getElementById(`sg_preview_${key}`);
            if (previewEl) {
                previewEl.innerText = `₹${estSelling.toFixed(2)}`;
            }
        }

        function applySubGroupRule(key) {
            const g = activeSubGroupsMap[key];
            if (!g) return;

            const lessPct = parseFloat(document.getElementById(`sg_less_${key}`)?.value) || 0;
            const freightMode = document.getElementById(`sg_freight_mode_${key}`)?.value || 'flat';
            const freightVal = parseFloat(document.getElementById(`sg_freight_val_${key}`)?.value) || 0;
            const marginMode = document.getElementById(`sg_margin_mode_${key}`)?.value || 'percent';
            const marginVal = parseFloat(document.getElementById(`sg_margin_val_${key}`)?.value) || 0;

            // Save to persistent storage
            let savedRules = {};
            try {
                savedRules = JSON.parse(localStorage.getItem('vyapar_subgroup_rules') || '{}');
            } catch (e) {
                savedRules = {};
            }
            const ruleKey = (activeCategory !== 'ALL' ? activeCategory : 'GLOBAL') + '_' + key;
            savedRules[ruleKey] = {
                less_pct: lessPct,
                freight_mode: freightMode,
                freight_val: freightVal,
                margin_mode: marginMode,
                margin_val: marginVal
            };
            localStorage.setItem('vyapar_subgroup_rules', JSON.stringify(savedRules));

            // Apply to Dynamic Rows
            let updatedCount = 0;
            g.rows.forEach(r => {
                const base = parseFloat(r.raw_rate || r.purchase_cost || r.cost_price || r.mrp || 100);
                const afterLess = base * (1 - (lessPct / 100));
                const freightAmt = (freightMode === 'percent') ? (base * (freightVal / 100)) : freightVal;
                const landing = Math.round((afterLess + freightAmt) * 100) / 100;
                const selling = (marginMode === 'percent') ? Math.round(landing * (1 + (marginVal / 100)) * 100) / 100 : Math.round((landing + marginVal) * 100) / 100;

                r.purchase_cost = landing;
                r.retail_price = selling;
                r.cost_price_2 = Math.round(selling * 0.95 * 100) / 100;
                r.mrp = Math.max(parseFloat(r.mrp) || 0, Math.round(selling * 1.25 * 100) / 100);
                updatedCount++;
            });

            // Apply to Grouped Cards variants
            g.cardVariants.forEach(cv => {
                const v = cv.variant;
                const base = parseFloat(v.raw_rate || v.purchase_cost || v.mrp || 100);
                const afterLess = base * (1 - (lessPct / 100));
                const freightAmt = (freightMode === 'percent') ? (base * (freightVal / 100)) : freightVal;
                const landing = Math.round((afterLess + freightAmt) * 100) / 100;
                const selling = (marginMode === 'percent') ? Math.round(landing * (1 + (marginVal / 100)) * 100) / 100 : Math.round((landing + marginVal) * 100) / 100;

                v.purchase_cost = landing;
                v.retail_price = selling;
                v.mrp = Math.max(parseFloat(v.mrp) || 0, Math.round(selling * 1.25 * 100) / 100);
                updatedCount++;
            });

            saveToLocalStorage();
            if (typeof autoSaveDraftToBackend === 'function') autoSaveDraftToBackend();

            renderDynamicRows();
            renderGroupedProductCards();

            // Button feedback
            const btn = document.getElementById(`btn_apply_sg_${key}`);
            if (btn) {
                const orig = btn.innerHTML;
                btn.innerHTML = `<i class="fa-solid fa-check text-white"></i> <span>Applied ✓</span>`;
                setTimeout(() => { btn.innerHTML = orig; }, 1500);
            }
        }

        function applyAllSubGroupRules() {
            if (!activeSubGroupsMap) return;
            const keys = Object.keys(activeSubGroupsMap);
            if (keys.length === 0) return;

            keys.forEach(key => {
                applySubGroupRule(key);
            });

            alert(`🎉 "${activeCategory}" ke sabhi ${keys.length} Sub-Groups par naye rates successfully apply ho gaye!`);
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
                const trMod = tr.getAttribute('data-module') || '';
                const matchSearch = (!searchQuery || text.includes(searchQuery));
                const matchCat = (activeCategory === 'ALL' || category === activeCategory.toLowerCase());
                const matchMod = (activeIndustryModule === 'ALL' || !trMod || trMod === activeIndustryModule);
                tr.style.display = (matchSearch && matchCat && matchMod) ? '' : 'none';
            });
            // Cards view
            document.querySelectorAll('.product-family-card, [id^="grouped_card_box_"]').forEach(card => {
                const text = (card.getAttribute('data-search') || '').toLowerCase();
                const category = (card.getAttribute('data-category') || '').toLowerCase();
                const cardMod = card.getAttribute('data-module') || '';
                const matchSearch = (!searchQuery || text.includes(searchQuery));
                const matchCat = (activeCategory === 'ALL' || category === activeCategory.toLowerCase());
                const matchMod = (activeIndustryModule === 'ALL' || !cardMod || cardMod === activeIndustryModule);
                card.style.display = (matchSearch && matchCat && matchMod) ? '' : 'none';
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

        function triggerDevicePhotoUpload() {
            document.getElementById('drawerDirectPhotoInput')?.click();
        }

        async function handleDrawerDirectPhotoUpload(event) {
            const files = event.target.files;
            if (!files || files.length === 0) return;

            const btn = document.getElementById('drawerUploadBtn');
            const originalText = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> <span>Uploading (0/${files.length})...</span>`;
            }

            let lastUploadedImg = null;
            let successCount = 0;

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (btn) {
                    btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> <span>Uploading (${i + 1}/${files.length})...</span>`;
                }

                const formData = new FormData();
                formData.append('image_file', file);
                formData.append('folder', activeDrawerFolder && activeDrawerFolder !== 'ALL' ? activeDrawerFolder : 'General');
                formData.append('_token', csrfToken);

                try {
                    const res = await fetch("{{ route('seller.catalog.gallery.upload') }}", {
                        method: "POST",
                        headers: {
                            "Accept": "application/json",
                            "X-Requested-With": "XMLHttpRequest"
                        },
                        body: formData
                    });
                    const data = await res.json();
                    if (data.success && data.image) {
                        successCount++;
                        lastUploadedImg = data.image;

                        // 1. Add to drawer grid
                        const grid = document.getElementById('drawerImagesGrid');
                        const emptyPrompt = document.getElementById('drawerEmptyPrompt');
                        if (emptyPrompt) emptyPrompt.style.display = 'none';

                        const cardDiv = document.createElement('div');
                        cardDiv.className = "drawer-img-card border-2 border-emerald-500 rounded-2xl p-2 bg-emerald-50 hover:bg-emerald-100 cursor-pointer text-center group transition";
                        cardDiv.onclick = () => selectDrawerImage(data.image.url, data.image.asset_url);
                        cardDiv.setAttribute('data-name', (data.image.filename || '').toLowerCase());
                        cardDiv.setAttribute('data-folder', (data.image.folder || 'general').toLowerCase());
                        cardDiv.setAttribute('data-raw-folder', data.image.folder || 'General');
                        cardDiv.setAttribute('data-img-key', (data.image.filename || '').toLowerCase());
                        cardDiv.innerHTML = `
                            <div class="h-20 w-full bg-white rounded-xl p-1 mb-1.5 flex items-center justify-center overflow-hidden border border-emerald-200 group-hover:scale-105 transition">
                                <img src="${data.image.asset_url}" alt="${data.image.filename}" class="max-h-full max-w-full object-contain">
                            </div>
                            <h5 class="text-[11px] font-bold text-gray-800 truncate" title="${data.image.filename}">${data.image.filename}</h5>
                            <span class="text-[9px] text-emerald-600 font-bold block">✨ Uploaded</span>
                            <span class="text-[8px] text-gray-500 font-bold truncate block">📁 ${data.image.folder || 'General'}</span>
                        `;

                        if (grid) {
                            grid.insertBefore(cardDiv, grid.firstChild);
                        }

                        // 2. Also add to horizontal on-page strip
                        const onPageGrid = document.getElementById('onPagePhotoGrid');
                        if (onPageGrid) {
                            const onPageLoadingMsg = document.getElementById('onPagePhotoLoadingMsg');
                            if (onPageLoadingMsg) onPageLoadingMsg.remove();

                            const onPageCard = document.createElement('div');
                            onPageCard.className = "on-page-photo-card flex items-center gap-2.5 p-2 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 rounded-2xl cursor-pointer transition shrink-0 group";
                            onPageCard.onclick = () => handleOnPagePhotoClick(data.image.url, data.image.asset_url);
                            onPageCard.setAttribute('data-name', (data.image.filename || '').toLowerCase());
                            onPageCard.setAttribute('data-folder', (data.image.folder || 'general').toLowerCase());
                            onPageCard.setAttribute('data-raw-folder', data.image.folder || 'General');
                            onPageCard.setAttribute('data-img-key', (data.image.filename || '').toLowerCase());
                            onPageCard.innerHTML = `
                                <div class="h-14 w-14 bg-white rounded-xl p-1 flex items-center justify-center overflow-hidden border border-emerald-200 group-hover:scale-105 transition">
                                    <img src="${data.image.asset_url}" alt="${data.image.filename}" class="max-h-full max-w-full object-contain">
                                </div>
                                <div class="max-w-[120px]">
                                    <h5 class="text-[11px] font-bold text-gray-800 truncate" title="${data.image.filename}">${data.image.filename}</h5>
                                    <span class="text-[9px] text-emerald-600 font-semibold block">✨ Just Uploaded</span>
                                    <span class="text-[8px] text-gray-500 font-bold truncate block">📁 ${data.image.folder || 'General'}</span>
                                    <span class="text-[9px] text-emerald-600 font-bold group-hover:underline">Attach &rarr;</span>
                                </div>
                            `;
                            onPageGrid.insertBefore(onPageCard, onPageGrid.firstChild);
                        }
                    }
                } catch (err) {
                    console.error('Photo upload error for', file.name, err);
                }
            }

            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
            event.target.value = '';

            // Update photo count badges
            const countEl = document.getElementById('drawerImagesCount');
            if (countEl) {
                const totalNow = document.querySelectorAll('.drawer-img-card').length;
                countEl.innerText = `${totalNow} Photos`;
            }
            const onPageCountEl = document.getElementById('onPagePhotoCount');
            if (onPageCountEl) {
                const totalOnPage = document.querySelectorAll('.on-page-photo-card').length;
                onPageCountEl.innerText = `${totalOnPage} Photos`;
            }

            // If drawer was open to attach to a card or row, automatically select the uploaded image!
            if (lastUploadedImg && currentTargetType) {
                selectDrawerImage(lastUploadedImg.url, lastUploadedImg.asset_url);
            }
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
                if (Array.isArray(groupedProductCards) && groupedProductCards.length > 0) {
                    localStorage.setItem('vyapar_grouped_cards', JSON.stringify(groupedProductCards));
                    localStorage.setItem('vyapar_grouped_cards_backup', JSON.stringify(groupedProductCards));
                } else if (Array.isArray(groupedProductCards)) {
                    localStorage.setItem('vyapar_grouped_cards', JSON.stringify(groupedProductCards));
                }
                if (Array.isArray(dynamicRows) && dynamicRows.length > 0) {
                    localStorage.setItem('vyapar_dynamic_rows', JSON.stringify(dynamicRows));
                    localStorage.setItem('vyapar_dynamic_rows_backup', JSON.stringify(dynamicRows));
                } else if (Array.isArray(dynamicRows)) {
                    localStorage.setItem('vyapar_dynamic_rows', JSON.stringify(dynamicRows));
                }
                localStorage.setItem('vyapar_grouped_next_id', String(groupedCardNextId));
                localStorage.setItem('vyapar_dynamic_next_id', String(dynamicRowNextId));
            } catch (e) {
                console.warn('saveToLocalStorage warning:', e);
            }
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
                        stock: 0,
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
                        grouped_cards: groupedProductCards,
                        publish: false
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
            }, 1000);
        }

        function loadFromLocalStorage() {
            try {
                let storedCards = localStorage.getItem('vyapar_grouped_cards');
                let storedRows = localStorage.getItem('vyapar_dynamic_rows');

                // Fallback to backup keys if primary keys are missing or empty
                if (!storedCards || storedCards === '[]') {
                    const backupCards = localStorage.getItem('vyapar_grouped_cards_backup');
                    if (backupCards && backupCards !== '[]') {
                        storedCards = backupCards;
                    }
                }
                if (!storedRows || storedRows === '[]') {
                    const backupRows = localStorage.getItem('vyapar_dynamic_rows_backup');
                    if (backupRows && backupRows !== '[]') {
                        storedRows = backupRows;
                    }
                }

                let hasLoaded = false;
                if (storedCards) {
                    try {
                        const parsedCards = JSON.parse(storedCards);
                        if (Array.isArray(parsedCards) && parsedCards.length > 0) {
                            groupedProductCards = parsedCards;
                            groupedCardNextId = parseInt(localStorage.getItem('vyapar_grouped_next_id') || '100');
                            hasLoaded = true;
                        }
                    } catch (e) {
                        console.warn('Error parsing stored cards:', e);
                    }
                }
                if (storedRows) {
                    try {
                        const parsedRows = JSON.parse(storedRows);
                        if (Array.isArray(parsedRows) && parsedRows.length > 0) {
                            dynamicRows = parsedRows;
                            dynamicRowNextId = parseInt(localStorage.getItem('vyapar_dynamic_next_id') || '1000');
                            hasLoaded = true;
                        }
                    } catch (e) {
                        console.warn('Error parsing stored rows:', e);
                    }
                }
                if (hasLoaded && typeof deduplicateDynamicRowsAgainstCards === 'function') {
                    try {
                        deduplicateDynamicRowsAgainstCards();
                    } catch (err) {
                        console.warn('deduplicateDynamicRowsAgainstCards caught:', err);
                    }
                }
                return hasLoaded;
            } catch (e) {
                console.error('loadFromLocalStorage error:', e);
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
                    const name = (r.name || r.product_name || `Product ${idx + 1}`).trim();
                    const size = (r.size && r.size !== '-') ? r.size.trim() : '';
                    const modelNo = (r.model_no && r.model_no !== '-') ? r.model_no.trim() : '';
                    const itemCode = (r.item_code && r.item_code !== '-') ? r.item_code.trim() : '';
                    const sku = itemCode || (r.sku && r.sku !== '-' ? r.sku.trim() : '');
                    const mrp = isNaN(parseFloat(r.mrp)) ? 0 : parseFloat(r.mrp);
                    const price = isNaN(parseFloat(r.price)) ? mrp : parseFloat(r.price);
                    const pack1 = (r.packing_1 && r.packing_1 !== '-') ? r.packing_1.trim() : (r.unit || '');
                    const pack2 = (r.packing_2 && r.packing_2 !== '-') ? r.packing_2.trim() : '';
                    const desc = (r.description && r.description !== '-') ? r.description.trim() : (r.specifications || '');
                    const hsn = (r.hsn_code && r.hsn_code !== '-') ? r.hsn_code.trim() : '';

                    let category = (r.category && r.category !== '-') ? r.category.trim() : targetCategory;
                    if (!r.category || r.category === 'General' || r.category === 'General Hardware') {
                        const combined = ((name || '') + ' ' + (desc || '') + ' ' + (size || '')).toUpperCase();
                        if (/CPVC/i.test(combined)) category = 'CPVC PIPES&FITTINGS';
                        else if (/UPVC/i.test(combined)) category = 'UPVC PIPE&FITTINGS';
                        else if (/SWR|DRAIN/i.test(combined)) category = 'SWR PIPES&FITTINGS';
                        else if (/PTMT|FLOREX|TAP|FAUCET|COCK|SHOWER/i.test(combined)) category = 'Florex Ptmt & Bath Fittings';
                        else if (/BATH|SANITARY|PARRYWARE/i.test(combined)) category = 'BATH PVC&PTMT FITTINGS';
                        else if (/AGRI|SOLVENT/i.test(combined)) category = 'AGRI PIPES&FITTINGS';
                        else if (/HARDWARE|PLYWOOD/i.test(combined)) category = 'HARDWARE PLYWOOD';
                        else if (/ELECTRIC|TOOL/i.test(combined)) category = 'ELECTRIC TOOLS';
                    }

                    dynamicRows.push({
                        id: dynamicRowNextId++,
                        product_code: sku,
                        model_no: modelNo,
                        item_code: itemCode,
                        hsn_code: hsn,
                        product_name: name,
                        description: desc,
                        size: size,
                        color: r.color || '',
                        unit: r.unit || '',
                        packing_1: pack1,
                        packing_2: pack2,
                        group_type: category,
                        category: category,
                        mrp: mrp,
                        purchase_cost: 0,
                        cost_price_2: 0,
                        cost_price_3: 0,
                        retail_price: price,
                        stock: 0,
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

                    const mrp = parsed.mrp > 0 ? parsed.mrp : 0;

                    dynamicRows.push({
                        id: dynamicRowNextId++,
                        product_code: parsed.sku || '',
                        model_no: '',
                        item_code: parsed.sku || '',
                        hsn_code: '',
                        product_name: parsed.name,
                        description: parsed.description || '',
                        size: parsed.size || '',
                        packing_1: parsed.pack1 || '',
                        packing_2: parsed.pack2 || '',
                        group_type: category,
                        category: category,
                        mrp: mrp,
                        purchase_cost: 0,
                        cost_price_2: 0,
                        cost_price_3: 0,
                        retail_price: mrp,
                        stock: 0,
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
            const isLiveView = urlParams.get('view') === 'live';
            const catParam = urlParams.get('category');

            if (isLiveView) {
                switchMapperMainView('live');
                if (catParam) {
                    setTimeout(() => filterLiveProductsByCategory(catParam), 150);
                }
            }

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

            // Clean source=pdf_lines from URL immediately so subsequent F5/Refresh doesn't reset or re-import
            if (isFromPdf && window.history && window.history.replaceState) {
                try {
                    const cleanUrl = window.location.pathname + (window.location.search.replace(/[?&]source=pdf_lines(&|$)/, '$1').replace(/[?&]$/, ''));
                    window.history.replaceState({}, document.title, cleanUrl || window.location.pathname);
                } catch (e) {}
            }

            // ALWAYS restore existing saved cards & rows from localStorage first into memory
            loadFromLocalStorage();
            initIndustryModules();

            // 🌟 PRIORITY 1: IF COMING DIRECTLY FROM PDF STUDIO (source=pdf_lines), LOAD NEW PDF ITEMS!
            if (isFromPdf) {
                const rawStored = localStorage.getItem('vyapar_custom_excel_lines');
                if (rawStored) {
                    try {
                        const parsed = JSON.parse(rawStored);
                        const prevRowCount = dynamicRows.length;
                        const prevCardCount = groupedProductCards.length;

                        // Backup previous draft before loading newly extracted PDF lines
                        if (prevRowCount > 0 || prevCardCount > 0) {
                            localStorage.setItem('vyapar_dynamic_rows_backup', JSON.stringify(dynamicRows));
                            localStorage.setItem('vyapar_grouped_cards_backup', JSON.stringify(groupedProductCards));
                            window.previousDraftRowCount = prevRowCount;
                        }

                        if (loadPdfExtractedDataIntoDynamicRows(parsed)) {
                            // If previous items existed, show merge & restore buttons in alert banner
                            const alertDesc = document.getElementById('pdfImportAlertDesc');
                            const alertActions = document.getElementById('pdfImportAlertActions');
                            if (prevRowCount > 0) {
                                if (alertDesc) {
                                    alertDesc.innerHTML = `PDF Page ${parsed.page || 1} se <b>${dynamicRows.length} naye items</b> sheet me load ho gaye hain.<br><span class="opacity-90">Aapka purana draft (<b>${prevRowCount} items</b>) backup me surakshit hai.</span>`;
                                }
                                if (alertActions) {
                                    alertActions.classList.remove('hidden');
                                }
                            } else {
                                if (alertActions) alertActions.classList.add('hidden');
                            }

                            // Clean source=pdf_lines from URL so F5/Refresh doesn't re-trigger
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
            }

            // Clean source=pdf_lines from URL if still present
            if (isFromPdf && window.history && window.history.replaceState) {
                try {
                    const cleanUrl = window.location.pathname + (window.location.search.replace(/[?&]source=pdf_lines(&|$)/, '$1').replace(/[?&]$/, ''));
                    window.history.replaceState({}, document.title, cleanUrl || window.location.pathname);
                } catch (e) {}
            }

            // PRIORITY 2: If we have existing saved dynamic spreadsheet rows or grouped cards in localStorage, PRESERVE THEM!
            if (groupedProductCards.length > 0 || dynamicRows.length > 0) {
                renderDynamicRows();
                renderGroupedProductCards();
                refreshCategoryFilterTabs();
                toggleDynamicMode(true);
                return;
            }

            const rawStored = localStorage.getItem('vyapar_custom_excel_lines');

            // PRIORITY 3: If workspace was empty and we have raw PDF lines from PDF Studio, load them!
            if (rawStored) {
                try {
                    const parsed = JSON.parse(rawStored);
                    if (loadPdfExtractedDataIntoDynamicRows(parsed)) {
                        toggleDynamicMode(true);
                        return;
                    }
                } catch (e) {
                    console.error('Error loading PDF custom lines:', e);
                }
            }

            // 3. PRIORITY 3: Hydrate from server database job if available
            if (serverJobProducts && serverJobProducts.length > 0) {
                if (hydrateFromJobProducts(serverJobProducts)) {
                    renderDynamicRows();
                    renderGroupedProductCards();
                    refreshCategoryFilterTabs();
                    return;
                }
            }

            // 4. Default Fallback: Clean empty workspace!
            dynamicRows = [];
            groupedProductCards = [];
            renderDynamicRows();
            renderGroupedProductCards();
            refreshCategoryFilterTabs();
        }

        // 🔄 1-Click Recovery function to restore any lost cards or draft
        function mergeWithPreviousDraft() {
            try {
                const bRows = localStorage.getItem('vyapar_dynamic_rows_backup');
                if (!bRows) {
                    alert('Koi purana draft backup me nahi mila.');
                    return;
                }
                const parsed = JSON.parse(bRows);
                if (Array.isArray(parsed) && parsed.length > 0) {
                    let maxId = dynamicRows.reduce((max, r) => Math.max(max, r.id || 0), 0);
                    parsed.forEach(r => {
                        maxId++;
                        dynamicRows.push({ ...r, id: maxId });
                    });
                    dynamicRowNextId = maxId + 1;
                    saveToLocalStorage();
                    renderDynamicRows();
                    refreshCategoryFilterTabs();
                    showToastNotification(`✅ Purane draft ke ${parsed.length} items safalta-purvak merge ho gaye! Kul: ${dynamicRows.length} items`);
                    const alertEl = document.getElementById('pdfImportSuccessAlert');
                    if (alertEl) alertEl.classList.add('hidden');
                }
            } catch(e) {
                alert('Draft merge karne me truti hui: ' + e.message);
            }
        }

        function restoreDraftCardsAndRows() {
            let restoredCards = 0;
            let restoredRows = 0;

            // 1. Try local storage primary
            try {
                const sCards = localStorage.getItem('vyapar_grouped_cards');
                if (sCards) {
                    const p = JSON.parse(sCards);
                    if (Array.isArray(p) && p.length > 0) {
                        groupedProductCards = p;
                        restoredCards = p.length;
                    }
                }
            } catch (e) {}

            // 2. Try local storage backup if primary was empty
            if (restoredCards === 0) {
                try {
                    const bCards = localStorage.getItem('vyapar_grouped_cards_backup');
                    if (bCards) {
                        const p = JSON.parse(bCards);
                        if (Array.isArray(p) && p.length > 0) {
                            groupedProductCards = p;
                            restoredCards = p.length;
                            localStorage.setItem('vyapar_grouped_cards', bCards);
                        }
                    }
                } catch (e) {}
            }

            // 3. Dynamic rows primary
            try {
                const sRows = localStorage.getItem('vyapar_dynamic_rows');
                if (sRows) {
                    const p = JSON.parse(sRows);
                    if (Array.isArray(p) && p.length > 0) {
                        dynamicRows = p;
                        restoredRows = p.length;
                    }
                }
            } catch (e) {}

            // 4. Dynamic rows backup if empty
            if (restoredRows === 0) {
                try {
                    const bRows = localStorage.getItem('vyapar_dynamic_rows_backup');
                    if (bRows) {
                        const p = JSON.parse(bRows);
                        if (Array.isArray(p) && p.length > 0) {
                            dynamicRows = p;
                            restoredRows = p.length;
                            localStorage.setItem('vyapar_dynamic_rows', bRows);
                        }
                    }
                } catch (e) {}
            }

            // 5. If still no rows and no cards, try raw PDF lines!
            if (restoredCards === 0 && restoredRows === 0) {
                const rawStored = localStorage.getItem('vyapar_custom_excel_lines');
                if (rawStored) {
                    try {
                        const parsed = JSON.parse(rawStored);
                        if (loadPdfExtractedDataIntoDynamicRows(parsed)) {
                            showToastNotification(`🎉 PDF lines se ${dynamicRows.length} items restore ho gaye!`);
                            return;
                        }
                    } catch (e) {}
                }
            }

            if (restoredCards > 0 || restoredRows > 0) {
                renderDynamicRows();
                renderGroupedProductCards();
                refreshCategoryFilterTabs();
                toggleDynamicMode(true);
                saveToLocalStorage();
                showToastNotification(`🎉 Shabaash! ${restoredCards} Cards aur ${restoredRows} Pending Rows restore ho gaye!`);
                alert(`🎉 Workspace Restored!\n\n• ${restoredCards} Cards safalta-purvak restore ho gaye hain!\n• ${restoredRows} Pending items table me wapas aa gaye hain.`);
            } else {
                alert('Storage me koi purana draft nahi mila. Kripya "+ Import New Excel" dabayein ya PDF Studio se dubara "Open in Excel Mapper" dabayein.');
            }
        }

        function reExtractFromPdfLines() {
            const rawStored = localStorage.getItem('vyapar_custom_excel_lines');
            if (!rawStored) {
                alert('PDF Studio ka koi data browser me nahi mila. Kripya PDF Studio me jakar dubara "Open in Excel Multi-Row Mapper" dabayein.');
                return;
            }
            try {
                const parsed = JSON.parse(rawStored);
                if (loadPdfExtractedDataIntoDynamicRows(parsed)) {
                    renderDynamicRows();
                    renderGroupedProductCards();
                    refreshCategoryFilterTabs();
                    toggleDynamicMode(true);
                    saveToLocalStorage();
                    showToastNotification(`🎉 PDF se ${dynamicRows.length} items reload ho gaye!`);
                    alert(`🎉 PDF Extracted Data Reloaded!\n\n${dynamicRows.length} items table me load ho gaye hain.`);
                }
            } catch (e) {
                alert('PDF data reload karne me truti hui.');
            }
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

            alert(`🎉 Success! Excel sheet se ${newRows.length} products sahi MRP, Selling Price, Code, Packing aur Category ke sath map ho gaye hain!\n\n💡 Tip: Ab aap seedhe "⚡ Auto-Distribute Cards" dabakar sabhi products ko unke Product Names ke mutabiq alag-alag Cards me baat sakte hain!`);
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
                try {
                    const escaped = currentSize.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    str = str.replace(new RegExp(escaped, 'gi'), ' ');
                } catch (e) {}
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
            if (!groupedProductCards || groupedProductCards.length === 0 || !r) return false;
            try {
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
            } catch (err) {
                console.warn('isRowAlreadyMappedInCards caught:', err);
            }
            return false;
        }

        function deduplicateDynamicRowsAgainstCards() {
            // Non-destructive: Preserve all dynamicRows in the spreadsheet table!
            return;
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
            let existingCardsUpdated = 0;
            familyMap.forEach((rowsInFamily, familyName) => {
                const firstRow = rowsInFamily[0];
                const assignedImg = rowsInFamily.find(r => r.image_url)?.image_url || '';
                const assignedAsset = rowsInFamily.find(r => r.asset_url)?.asset_url || (assignedImg ? ('/' + assignedImg.replace(/^\//, '')) : '');
                const rowWithDesc = rowsInFamily.find(r => r.description && r.description.trim().length > 0);
                const assignedDesc = rowWithDesc ? rowWithDesc.description.trim() : '';

                // Check if card with this product name already exists!
                const existingCard = groupedProductCards.find(c => 
                    (c.parent_name || '').trim().toLowerCase() === familyName.trim().toLowerCase() ||
                    extractBaseFamilyName(c.parent_name || '').toLowerCase() === familyName.toLowerCase()
                );

                if (existingCard) {
                    // 🔄 UPDATE EXISTING CARD RATES (Zero Duplication!)
                    rowsInFamily.forEach(r => {
                        const existingVar = existingCard.variants.find(v => 
                            (v.size || '').trim().toLowerCase() === (r.size || '').trim().toLowerCase()
                        );
                        if (existingVar) {
                            existingVar.mrp = r.mrp;
                            existingVar.purchase_cost = r.purchase_cost;
                            existingVar.retail_price = r.retail_price;
                            if (r.cost_price_2) existingVar.cost_price_2 = r.cost_price_2;
                            if (r.cost_price_3) existingVar.cost_price_3 = r.cost_price_3;
                            if (r.description) existingVar.description = r.description;
                        } else {
                            existingCard.variants.push({
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
                            });
                        }
                    });
                    if (!existingCard.image_url && assignedImg) {
                        existingCard.image_url = assignedImg;
                        existingCard.asset_url = assignedAsset;
                    }
                    existingCardsUpdated++;
                } else {
                    // 🆕 CREATE NEW CARD
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
                }
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

            showToastNotification(`🚀 Zabardast! ${newCardsCreated} naye cards create hue aur ${existingCardsUpdated} existing cards ke rates naye bhav ke sath update ho gaye!`);

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
                    product_name: r.product_name || parentTitle,
                    hsn_code: r.hsn_code || '39174000',
                    size: r.size || 'Standard',
                    description: r.description || '',
                    packing_1: r.packing_1 || '',
                    packing_2: r.packing_2 || '',
                    mrp: r.mrp,
                    purchase_cost: r.purchase_cost,
                    cost_price_2: r.cost_price_2 || 0,
                    cost_price_3: r.cost_price_3 || 0,
                    price_effective_date: r.price_effective_date || '',
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
                    <div class="p-6 text-center bg-gray-50/50 rounded-2xl border border-dashed border-gray-200 text-gray-500 text-xs space-y-2">
                        <i class="fa-solid fa-layer-group text-lg text-gray-400"></i>
                        <p>Abhi tak koi grouped card nahi dikh raha hai. Upar table me se 6-8 sizes select karke <b>"Group (6-8) Rows into 1 Card"</b> dabayein ya Auto-Distribute karein.</p>
                        <div>
                            <button type="button" onclick="restoreDraftCardsAndRows()" class="mt-1 px-3.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-extrabold text-xs border border-indigo-200 inline-flex items-center gap-1.5 transition active:scale-95 shadow-2xs">
                                <i class="fa-solid fa-rotate-left text-indigo-600"></i>
                                <span>🔄 Restore Previous Cards & Draft</span>
                            </button>
                        </div>
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

                // Calculate price range & sqft rate for card badge
                const sellingRates = (c.variants || []).map(v => parseFloat(v.retail_price) || 0).filter(r => r > 0);
                const minRate = sellingRates.length > 0 ? Math.min(...sellingRates) : 0;
                const maxRate = sellingRates.length > 0 ? Math.max(...sellingRates) : 0;

                const isPlywoodOrBoard = (cardCategory.toUpperCase().includes('PLY') || cardCategory.toUpperCase().includes('BOARD') || cardCategory.toUpperCase().includes('DOOR') || (c.parent_name || '').toUpperCase().includes('PLY'));
                const hasSheetSizes = (c.variants || []).some(v => (v.size || '').includes('x') || (v.size || '').includes('X') || (v.size || '').toLowerCase().includes('sheet'));

                let rateBadgeHtml = '';
                if (sellingRates.length > 0) {
                    if (isPlywoodOrBoard && !hasSheetSizes) {
                        // Sizes not expanded yet: show ONLY per-sqft rate!
                        const sqftRate = parseFloat(c.variants[0]?.purchase_cost || c.variants[0]?.retail_price || 0);
                        rateBadgeHtml = `<span class="px-2.5 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 font-black text-[10px] border border-emerald-200 font-mono flex items-center gap-1"><i class="fa-solid fa-tag text-[9px] text-emerald-600"></i> ₹${sqftRate.toFixed(2)} / sqft</span>`;
                    } else if (minRate === maxRate) {
                        rateBadgeHtml = `<span class="px-2.5 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 font-black text-[10px] border border-emerald-200 font-mono flex items-center gap-1"><i class="fa-solid fa-tag text-[9px] text-emerald-600"></i> ₹${minRate.toFixed(2)}</span>`;
                    } else {
                        rateBadgeHtml = `<span class="px-2.5 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 font-black text-[10px] border border-emerald-200 font-mono flex items-center gap-1"><i class="fa-solid fa-tags text-[9px] text-emerald-600"></i> ₹${minRate.toFixed(2)} – ₹${maxRate.toFixed(2)} / Sheet</span>`;
                    }
                }

                const cardMod = detectCardModule(c);
                const modMeta = VyaparIndustryModules[cardMod] || { name: 'General', icon: 'fa-box', badgeColor: 'bg-indigo-50 text-indigo-700 border-indigo-100', shortName: 'Standard' };
                const moduleBadgeHtml = `<span class="px-2.5 py-0.5 rounded-lg ${modMeta.badgeColor || 'bg-indigo-50 text-indigo-700 border-indigo-100'} font-extrabold text-[10px] border flex items-center gap-1"><i class="fa-solid ${modMeta.icon} text-[9px]"></i> ${modMeta.shortName || modMeta.name}</span>`;

                html += `
                    <div class="bg-white rounded-3xl border-2 border-indigo-100 p-5 shadow-sm hover:shadow-md transition space-y-4 product-family-card" id="grouped_card_box_${c.card_id}" data-category="${escapeHtml(cardCategory)}" data-module="${cardMod}" data-search="${escapeHtml(cardSearch)}">
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
                                        ${moduleBadgeHtml}
                                        <span class="px-2.5 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 font-extrabold text-[10px] border border-indigo-100 flex items-center gap-1">
                                            <i class="fa-solid fa-layer-group text-[9px]"></i> ${c.variants.length} Sizes Included
                                        </span>
                                        ${rateBadgeHtml}
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
                            <div class="p-2.5 border-t border-gray-100 flex items-center justify-between flex-wrap gap-2 bg-white/70">
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="addVariantToCard(${c.card_id})" class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center gap-1.5 transition">
                                        <i class="fa-solid fa-plus text-[10px]"></i>
                                        <span>+ Add Variant</span>
                                    </button>
                                    <button type="button" onclick="applyIndustryStandardSizes(${c.card_id})" class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-bold flex items-center gap-1.5 transition active:scale-95 shadow-2xs" title="Auto-add standard trade sizes for ${modMeta.name}">
                                        <i class="fa-solid fa-wand-magic-sparkles text-amber-600"></i>
                                        <span>Auto-Fill Standard Sizes</span>
                                    </button>
                                </div>
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
                    product_name: v.product_name || card.parent_name,
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
                    price_effective_date: v.price_effective_date || '',
                    retail_price: v.retail_price,
                    stock: (v.stock !== undefined && v.stock !== null && v.stock !== '') ? v.stock : 0,
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

        function ungroupAllCardsToTable() {
            if (!groupedProductCards || groupedProductCards.length === 0) {
                alert('Table me koi cards nahi hain jinhe ungroup kiya ja sake.');
                return;
            }

            if (!confirm(`Kya aap sabhi ${groupedProductCards.length} cards ko wapas flat Table Listing me dismantle karna chahte hain?\n\nIsse sare items wapas table me aa jayenge aur aap apni marzi se rows select karke nayi cards bana sakenge.`)) {
                return;
            }

            let totalItemsUnpacked = 0;
            const cardsToUnpack = [...groupedProductCards];
            cardsToUnpack.forEach(card => {
                if (card.variants && card.variants.length > 0) {
                    card.variants.forEach(v => {
                        dynamicRows.push({
                            id: dynamicRowNextId++,
                            product_code: v.product_code || '',
                            hsn_code: v.hsn_code || '39174000',
                            product_name: v.product_name || card.parent_name,
                            description: card.description || v.description || '',
                            size: v.size || 'Standard',
                            packing_1: v.packing_1 || '',
                            packing_2: v.packing_2 || '',
                            group_type: card.category || 'General Hardware',
                            category: card.category || 'General Hardware',
                            mrp: v.mrp,
                            purchase_cost: v.purchase_cost,
                            cost_price_2: v.cost_price_2 || 0,
                            cost_price_3: v.cost_price_3 || 0,
                            price_effective_date: v.price_effective_date || '',
                            retail_price: v.retail_price,
                            stock: (v.stock !== undefined && v.stock !== null && v.stock !== '') ? v.stock : 0,
                            image_url: card.image_url || '',
                            asset_url: card.asset_url || ''
                        });
                        totalItemsUnpacked++;
                    });
                }
            });

            groupedProductCards = [];
            renderDynamicRows();
            renderGroupedProductCards();
            refreshCategoryFilterTabs();
            updateFloatingBatchBar();
            saveToLocalStorage();
            if (typeof autoSaveDraftToBackend === 'function') autoSaveDraftToBackend();

            showToastNotification(`↩️ Sabhi ${totalItemsUnpacked} items wapas Spreadsheet Table me aa gaye! Ab aap unhe apni marzi se select karke cards bana sakte hain.`);
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

        let mergeCurrentMode = 'new';
        let mergeActiveCards = [];
        let mergeSelectedPhotoUrl = '';
        let mergeSelectedAssetUrl = '';

        function clearGroupedCardSelection() {
            document.querySelectorAll('.grouped-card-checkbox').forEach(cb => { cb.checked = false; });
            const bar = document.getElementById('groupedCardsBatchBar');
            if (bar) bar.classList.add('hidden');
            mergeActiveCards = [];
        }

        function setMergeTargetMode(mode) {
            mergeCurrentMode = mode;
            const btnNew = document.getElementById('btnMergeModeNew');
            const btnExisting = document.getElementById('btnMergeModeExisting');
            const secNew = document.getElementById('mergeNewGroupSection');
            const secExisting = document.getElementById('mergeExistingGroupSection');

            if (mode === 'new') {
                if (btnNew) {
                    btnNew.className = 'py-2.5 px-3 rounded-xl text-xs font-black transition flex items-center justify-center gap-1.5 bg-white text-indigo-900 shadow-xs border border-indigo-200';
                }
                if (btnExisting) {
                    btnExisting.className = 'py-2.5 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 text-gray-600 hover:text-gray-900';
                }
                if (secNew) secNew.classList.remove('hidden');
                if (secExisting) secExisting.classList.add('hidden');
            } else {
                if (btnNew) {
                    btnNew.className = 'py-2.5 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 text-gray-600 hover:text-gray-900';
                }
                if (btnExisting) {
                    btnExisting.className = 'py-2.5 px-3 rounded-xl text-xs font-black transition flex items-center justify-center gap-1.5 bg-white text-indigo-900 shadow-xs border border-indigo-200';
                }
                if (secNew) secNew.classList.add('hidden');
                if (secExisting) secExisting.classList.remove('hidden');
            }
        }

        function suggestMergedCardTitle(cards) {
            if (!cards || cards.length === 0) return '';
            if (cards.length === 1) return cards[0].parent_name;

            // Strip common trailing sizes like '3 Mtr', '5 Mtr', '25mm', '1 inch', '(3M)'
            const cleanTitles = cards.map(c => {
                return (c.parent_name || '')
                    .replace(/\b\d+(\.\d+)?\s*(mtr|meter|mtrs|m|mm|cm|inch|in|"|'|ft|feet)\b/gi, '')
                    .replace(/\(\s*\)/g, '')
                    .trim();
            });

            const firstClean = cleanTitles[0];
            const allSame = cleanTitles.every(t => t.toLowerCase() === firstClean.toLowerCase());
            if (allSame && firstClean.length >= 3) {
                return firstClean;
            }

            // Look for common words prefix
            const words0 = (cards[0].parent_name || '').split(/\s+/);
            const commonWords = [];
            for (const w of words0) {
                if (w.length <= 1) continue;
                if (cards.every(c => (c.parent_name || '').toLowerCase().includes(w.toLowerCase()))) {
                    commonWords.push(w);
                }
            }
            if (commonWords.length >= 2) {
                return commonWords.join(' ');
            }

            return cards[0].parent_name || 'Merged Product Group';
        }

        function onMergePhotoRadioChange(radio) {
            if (radio && radio.checked) {
                mergeSelectedPhotoUrl = radio.value;
                mergeSelectedAssetUrl = radio.getAttribute('data-asset') || radio.value;
            }
        }

        function validateMergePhotoStrictness() {
            const strictCb = document.getElementById('strictPhotoMatchCheckbox');
            const errMsg = document.getElementById('strictPhotoErrorMsg');
            const btnConfirm = document.getElementById('btnConfirmMergeCards');

            if (strictCb && strictCb.checked) {
                const photos = new Set(mergeActiveCards.map(c => c.image_url || c.asset_url).filter(Boolean));
                if (photos.size > 1) {
                    if (errMsg) errMsg.classList.remove('hidden');
                    if (btnConfirm) {
                        btnConfirm.disabled = true;
                        btnConfirm.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                    return false;
                }
            }

            if (errMsg) errMsg.classList.add('hidden');
            if (btnConfirm) {
                btnConfirm.disabled = false;
                btnConfirm.classList.remove('opacity-50', 'cursor-not-allowed');
            }
            return true;
        }

        function setupMergeModalWithCards(selectedCards) {
            if (!selectedCards || selectedCards.length < 2) return;
            mergeActiveCards = selectedCards;

            const inputSrc = document.getElementById('mergeSourceCardId');
            if (inputSrc) inputSrc.value = selectedCards.map(c => c.card_id).join(',');

            // 1. Render Cards Preview Container
            const previewContainer = document.getElementById('mergeCardsPreviewContainer');
            if (previewContainer) {
                previewContainer.innerHTML = selectedCards.map(c => {
                    const photoSrc = c.asset_url || c.image_url || '';
                    const photoHtml = photoSrc 
                        ? `<img src="${photoSrc}" class="w-10 h-10 object-contain rounded-lg border border-gray-200 bg-white shrink-0" onerror="this.onerror=null;this.src='/images/placeholder.png';">`
                        : `<div class="w-10 h-10 rounded-lg bg-gray-100 border border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-xs shrink-0"><i class="fa-regular fa-image"></i></div>`;
                    const sizesStr = (c.variants || []).map(v => v.size || 'Std').join(', ');

                    return `
                        <div class="flex items-center gap-2.5 p-2 bg-white rounded-xl border border-gray-200 shadow-2xs">
                            ${photoHtml}
                            <div class="min-w-0 flex-1">
                                <div class="text-[11px] font-black text-gray-900 truncate">Card #${c.card_id}: ${escapeHtml(c.parent_name)}</div>
                                <div class="text-[10px] text-indigo-600 font-bold truncate">${(c.variants || []).length} Sizes: [${escapeHtml(sizesStr)}]</div>
                            </div>
                        </div>
                    `;
                }).join('');
            }

            // 2. Photo Matching Analysis
            const photosMap = new Map();
            selectedCards.forEach(c => {
                const p = c.image_url || c.asset_url || '';
                if (p) {
                    if (!photosMap.has(p)) {
                        photosMap.set(p, {
                            url: p,
                            asset_url: c.asset_url || p,
                            cardIds: [c.card_id]
                        });
                    } else {
                        photosMap.get(p).cardIds.push(c.card_id);
                    }
                }
            });

            const distinctPhotos = Array.from(photosMap.values());
            const statusBox = document.getElementById('mergePhotoStatusBox');

            if (distinctPhotos.length > 0) {
                mergeSelectedPhotoUrl = distinctPhotos[0].url;
                mergeSelectedAssetUrl = distinctPhotos[0].asset_url;
            } else {
                mergeSelectedPhotoUrl = '';
                mergeSelectedAssetUrl = '';
            }

            if (statusBox) {
                if (distinctPhotos.length === 0) {
                    statusBox.className = 'rounded-2xl p-3 border text-xs space-y-1 bg-amber-50 border-amber-200 text-amber-900';
                    statusBox.innerHTML = `
                        <div class="flex items-center gap-2 font-black text-amber-800">
                            <i class="fa-solid fa-circle-info text-amber-600"></i>
                            <span>Photo Missing: Kisi bhi card me abhi photo nahi hai</span>
                        </div>
                        <p class="text-[11px] text-amber-700">Merge hone ke baad aap Photo Bank se photo assign kar sakte hain.</p>
                    `;
                } else if (distinctPhotos.length === 1) {
                    const matchedPhoto = distinctPhotos[0];
                    const isAllMatch = matchedPhoto.cardIds.length === selectedCards.length;
                    statusBox.className = 'rounded-2xl p-3 border text-xs space-y-1.5 bg-emerald-50 border-emerald-200 text-emerald-900';
                    statusBox.innerHTML = `
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 font-black text-emerald-800">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                                <span>${isAllMatch ? '✅ Perfect Photo Match!' : '✅ Photo Available!'}</span>
                            </div>
                            <span class="text-[10px] font-bold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full border border-emerald-300">Safe to Merge</span>
                        </div>
                        <div class="flex items-center gap-2.5 pt-1">
                            <img src="${matchedPhoto.asset_url || matchedPhoto.url}" class="w-11 h-11 object-contain rounded-xl border border-emerald-300 bg-white" onerror="this.onerror=null;this.src='/images/placeholder.png';">
                            <div class="text-[11px] text-emerald-800 font-medium">
                                ${isAllMatch ? 'Sabhi chune gaye cards me same photo lagi hui hai.' : `Card #${matchedPhoto.cardIds.join(', #')} ki photo is naye card par set rahegi.`}
                            </div>
                        </div>
                    `;
                } else {
                    statusBox.className = 'rounded-2xl p-3 border text-xs space-y-2.5 bg-rose-50 border-rose-200 text-rose-900';
                    let photoOptionsHtml = distinctPhotos.map((pObj, idx) => {
                        const isChecked = idx === 0 ? 'checked' : '';
                        return `
                            <label class="flex items-center gap-2.5 p-2 rounded-xl bg-white border border-rose-200 hover:border-indigo-400 cursor-pointer transition select-none">
                                <input type="radio" name="mergePhotoPick" value="${escapeHtml(pObj.url)}" data-asset="${escapeHtml(pObj.asset_url)}" ${isChecked} onchange="onMergePhotoRadioChange(this)" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                <img src="${pObj.asset_url || pObj.url}" class="w-10 h-10 object-contain rounded-lg border border-gray-200 bg-white" onerror="this.onerror=null;this.src='/images/placeholder.png';">
                                <div class="min-w-0 flex-1">
                                    <span class="text-[11px] font-black text-gray-900">Card #${pObj.cardIds.join(', #')} ki Photo</span>
                                    <p class="text-[10px] text-gray-500 truncate">${escapeHtml(pObj.url)}</p>
                                </div>
                            </label>
                        `;
                    }).join('');

                    statusBox.innerHTML = `
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 font-black text-rose-800">
                                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-sm"></i>
                                <span>Alag-Alag Photos Detected (${distinctPhotos.length} alag photos)!</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-rose-800">Chunein kaunsi photo naye card par rakhni hai:</p>
                        <div class="space-y-1.5 max-h-36 overflow-y-auto">
                            ${photoOptionsHtml}
                        </div>
                        <div class="pt-2 border-t border-rose-200 flex items-center justify-between">
                            <label class="flex items-center gap-2 text-[11px] font-black text-rose-900 cursor-pointer select-none">
                                <input type="checkbox" id="strictPhotoMatchCheckbox" onchange="validateMergePhotoStrictness()" checked class="h-4 w-4 rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                                <span>Sirf same photo hone par hi merge allow karein (Strict Check)</span>
                            </label>
                        </div>
                        <p id="strictPhotoErrorMsg" class="text-[11px] font-bold text-rose-600 flex items-center gap-1.5">
                            <i class="fa-solid fa-ban"></i>
                            Photo alag hone ke karan merge roka gaya hai. Same photo wale cards chunein ya Strict Check uncheck karein.
                        </p>
                    `;
                }
            }

            // 3. Pre-fill Title for New Group Mode
            const titleInput = document.getElementById('mergeNewGroupTitleInput');
            if (titleInput) {
                titleInput.value = suggestMergedCardTitle(selectedCards);
            }

            // 4. Populate Existing Target Dropdown with ONLY selected cards
            const selectEl = document.getElementById('mergeTargetCardSelect');
            if (selectEl) {
                selectEl.innerHTML = selectedCards.map(c => {
                    const sizes = (c.variants || []).map(v => v.size || 'Standard').join(', ');
                    return `<option value="${c.card_id}">Card #${c.card_id}: ${escapeHtml(c.parent_name)} (${(c.variants || []).length} Sizes: [${escapeHtml(sizes)}])</option>`;
                }).join('');
            }

            // Set default mode to 'new'
            setMergeTargetMode('new');
            validateMergePhotoStrictness();

            // Show modal
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
            const selectedCards = selectedIds.map(id => groupedProductCards.find(c => c.card_id === id)).filter(Boolean);

            if (selectedCards.length < 2) {
                alert('Chune gaye cards nahi mile.');
                return;
            }

            setupMergeModalWithCards(selectedCards);
        }

        function openMergeCardModal(sourceCardId) {
            const srcCard = groupedProductCards.find(c => c.card_id === sourceCardId);
            if (!srcCard) return;

            // Check if other cards are already selected via checkboxes
            const checkedBoxes = Array.from(document.querySelectorAll('.grouped-card-checkbox:checked'));
            const checkedIds = checkedBoxes.map(cb => parseInt(cb.value));

            // Ensure sourceCardId is included in the selection
            if (!checkedIds.includes(sourceCardId)) {
                checkedIds.push(sourceCardId);
                const targetCb = document.querySelector(`.grouped-card-checkbox[value="${sourceCardId}"]`);
                if (targetCb) targetCb.checked = true;
            }

            // If at least 2 cards are selected now, open merge modal for them
            if (checkedIds.length >= 2) {
                const selectedCards = checkedIds.map(id => groupedProductCards.find(c => c.card_id === id)).filter(Boolean);
                setupMergeModalWithCards(selectedCards);
                updateGroupedCardSelectionBar();
                return;
            }

            // If only 1 card is selected so far, guide user to pick the 2nd card
            updateGroupedCardSelectionBar();
            showToastNotification(`Card #${sourceCardId} tick ho gaya! Ab doosre card ka checkbox tick karein.`);
            alert(`Card #${sourceCardId} ("${srcCard.parent_name}") select ho gaya hai.\n\nAb doosre card ka checkbox tick karein jise iske sath jodna hai. Screen ke bottom par floating "🔗 Merge Selected Cards" button aa jayega!`);
        }

        function confirmMergeCards() {
            if (!mergeActiveCards || mergeActiveCards.length < 2) {
                alert('Merge karne ke liye kam se kam 2 cards hone zaroori hain.');
                return;
            }

            // Enforce photo strictness if enabled
            if (!validateMergePhotoStrictness()) {
                alert('Photo Conflict: Alag-alag photos wale cards merge nahi ho sakte kyonki "Strict Check" chalu hai. Kripya same photo wale cards chunein ya Strict Check box uncheck karein.');
                return;
            }

            const selectedIds = mergeActiveCards.map(c => c.card_id);

            if (mergeCurrentMode === 'new') {
                // ✨ CREATE BRAND NEW GROUP CARD (Prevents mixing into old cards)
                const newTitle = (document.getElementById('mergeNewGroupTitleInput')?.value || '').trim() || mergeActiveCards[0].parent_name;
                
                // Collect all variants from all selected cards
                const allVariants = [];
                let totalMoved = 0;
                mergeActiveCards.forEach(c => {
                    (c.variants || []).forEach(v => {
                        allVariants.push({ ...v });
                        totalMoved++;
                    });
                });

                // Determine photo for the new card
                const finalImg = mergeSelectedPhotoUrl || '';
                const finalAsset = mergeSelectedAssetUrl || (finalImg ? (finalImg.startsWith('http') ? finalImg : ('/' + finalImg.replace(/^\//, ''))) : '');

                // Determine base metadata from first card
                const baseCard = mergeActiveCards[0];
                const newCard = {
                    card_id: groupedCardNextId++,
                    parent_name: newTitle,
                    category: baseCard.category || 'General Hardware',
                    description: baseCard.description || '',
                    image_url: finalImg,
                    asset_url: finalAsset,
                    variants: allVariants
                };

                // Remove the merged source cards and insert the new group card at the top
                groupedProductCards = groupedProductCards.filter(c => !selectedIds.includes(c.card_id));
                groupedProductCards.unshift(newCard);

                clearGroupedCardSelection();
                closeMergeCardsModal();
                renderGroupedProductCards();
                refreshCategoryFilterTabs();
                saveToLocalStorage();
                autoSaveDraftToBackend();

                showToastNotification(`🎉 Naya Group Card Ban Gaya! ${totalMoved} sizes ab "${newTitle}" card me jud gaye.`);
                alert(`🎉 Naya Group Card Ban Gaya!\n\n• Card #${newCard.card_id}: "${newTitle}" safalta-purvak ban gaya hai.\n• Isme kul ${allVariants.length} sizes jud chuke hain.\n• Purane alag-alag ${selectedIds.length} cards saaf ho gaye.`);

            } else {
                // 🎯 MERGE INTO ONE OF THE SELECTED MASTER CARDS
                const targetCardId = parseInt(document.getElementById('mergeTargetCardSelect')?.value);
                if (isNaN(targetCardId)) {
                    alert('Master Destination Card chunein.');
                    return;
                }

                const targetCard = groupedProductCards.find(c => c.card_id === targetCardId);
                if (!targetCard) {
                    alert('Destination card nahi mila.');
                    return;
                }

                const sourceCards = mergeActiveCards.filter(c => c.card_id !== targetCardId);
                const sourceIds = sourceCards.map(c => c.card_id);
                let totalMoved = 0;

                sourceCards.forEach(sCard => {
                    (sCard.variants || []).forEach(v => {
                        targetCard.variants.push({ ...v });
                        totalMoved++;
                    });
                });

                // If user selected a specific photo, apply it
                if (mergeSelectedPhotoUrl) {
                    targetCard.image_url = mergeSelectedPhotoUrl;
                    targetCard.asset_url = mergeSelectedAssetUrl;
                }

                // Remove only the merged source cards
                groupedProductCards = groupedProductCards.filter(c => !sourceIds.includes(c.card_id));

                clearGroupedCardSelection();
                closeMergeCardsModal();
                renderGroupedProductCards();
                refreshCategoryFilterTabs();
                saveToLocalStorage();
                autoSaveDraftToBackend();

                showToastNotification(`🎉 Success! ${totalMoved} sizes ab Card #${targetCard.card_id} ("${targetCard.parent_name}") me jud gaye.`);
                alert(`🎉 Cards Merge Ho Gaye!\n\n• ${totalMoved} sizes safalta-purvak Card #${targetCard.card_id} ("${targetCard.parent_name}") me jud gaye hain.\n• Purane alag-alag duplicate cards saaf ho gaye.`);
            }
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
                    });
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
                    stock: 0,
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
                stock: 0,
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
        function getAllKnownCategories() {
            const catsSet = new Set();

            // 1. Photo Bank Folders
            const folderSelect = document.getElementById('onPageFolderSelect');
            if (folderSelect) {
                Array.from(folderSelect.options).forEach(opt => {
                    const v = (opt.value || '').trim();
                    if (v && v !== 'ALL') catsSet.add(v);
                });
            }

            // 2. Datalist categories from database
            const dl = document.getElementById('allCategoriesList');
            if (dl) {
                Array.from(dl.options).forEach(opt => {
                    const v = (opt.value || '').trim();
                    if (v) catsSet.add(v);
                });
            }

            // 3. Dynamic Rows
            if (Array.isArray(dynamicRows)) {
                dynamicRows.forEach(r => {
                    const c = (r.group_type || r.category || '').trim();
                    if (c) catsSet.add(c);
                });
            }

            // 4. Grouped Cards
            if (Array.isArray(groupedProductCards)) {
                groupedProductCards.forEach(card => {
                    const c = (card.category || card.group_type || '').trim();
                    if (c) catsSet.add(c);
                });
            }

            // Standards
            ['Florex Ptmt & Bath Fittings', 'CPVC PIPES&FITTINGS', 'UPVC PIPE&FITTINGS', 'SWR PIPES&FITTINGS', 'BATH PVC&PTMT FITTINGS', 'AGRI PIPES&FITTINGS', 'General Hardware', 'Electrical & Wiring'].forEach(c => catsSet.add(c));

            return Array.from(catsSet).filter(c => c && c.length > 0).sort((a, b) => a.localeCompare(b));
        }

        function clearOldDraftAndKeepOnlyNewPdf() {
            // 1. Check for latest PDF data from PDF Studio
            const rawStored = localStorage.getItem('vyapar_custom_excel_lines');
            let pdfData = null;
            let newItemsCount = 0;
            let pageNum = 1;

            if (rawStored) {
                try {
                    pdfData = JSON.parse(rawStored);
                    newItemsCount = (pdfData.structured_rows && pdfData.structured_rows.length) || (pdfData.lines && pdfData.lines.length) || 0;
                    pageNum = pdfData.page || 1;
                } catch(e) {}
            }

            // Fallback: check vyapar_staged_excel_rows from PDF Studio
            if (newItemsCount === 0) {
                try {
                    const staged = JSON.parse(localStorage.getItem('vyapar_staged_excel_rows') || '[]');
                    if (staged.length > 0) {
                        pdfData = { structured_rows: staged, page: staged[0]?.page || 1, category: staged[0]?.category || 'General Hardware' };
                        newItemsCount = staged.length;
                        pageNum = pdfData.page;
                    }
                } catch(e) {}
            }

            // SAFETY CHECK: If no PDF data exists at all, DO NOT delete anything!
            if (newItemsCount === 0) {
                alert('🛡️ Suraksha Alert: Koi naya PDF extracted data nahi mila.\n\nAapka koi bhi data delete nahi kiya gaya hai.\n\nKripya pehle PDF Studio me jakar "Open in Excel Multi-Row Mapper" dabayein.');
                return;
            }

            const confirmMsg = `🛡️ 100% DATA SURAKSHA CONFIRMATION:\n\n` +
                               `• Naya PDF Data (Page ${pageNum}): ${newItemsCount} Items TABLE ME SURAKSHIT RAHENGE!\n` +
                               `• Sirf purane session ke cards (jaise Electric Tools wagairah) ko workspace se hataya jayega.\n\n` +
                               `Kya aap purana draft hatakar sirf ye ${newItemsCount} naye PDF items par kaam karna chahte hain?`;

            if (confirm(confirmMsg)) {
                // Permanently save a safety backup of current workspace just in case
                try {
                    localStorage.setItem('vyapar_safety_vault_backup', JSON.stringify({
                        cards: groupedProductCards,
                        rows: dynamicRows,
                        saved_at: new Date().toISOString()
                    }));
                } catch(e) {}

                // Clear old cards
                groupedProductCards = [];
                localStorage.removeItem('vyapar_grouped_cards');
                localStorage.removeItem('vyapar_grouped_cards_backup');
                localStorage.removeItem('vyapar_grouped_next_id');

                // Load guaranteed new PDF items into dynamicRows
                loadPdfExtractedDataIntoDynamicRows(pdfData);

                saveToLocalStorage();
                renderDynamicRows();
                renderGroupedProductCards();
                refreshCategoryFilterTabs();
                toggleDynamicMode(true);

                showToastNotification(`✅ Shabaash! Purana data saaf ho gaya. Table me sirf ${dynamicRows.length} naye PDF items hain!`);
                const alertEl = document.getElementById('pdfImportSuccessAlert');
                if (alertEl) alertEl.classList.add('hidden');
            }
        }

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

            // Populate all known categories dynamically as clickable preset pills
            const allCats = getAllKnownCategories();
            const container = document.getElementById('bulkCategoryPresetPillsContainer');
            const countPill = document.getElementById('bulkCategoryCountPill');
            if (countPill) countPill.innerText = `${allCats.length} Categories Available`;

            if (container) {
                const currentVal = (document.getElementById('bulkCategoryInput')?.value || '').trim();
                container.innerHTML = allCats.map(cat => {
                    const safeCat = escapeHtml(cat);
                    const isSelected = currentVal === cat;
                    return `
                        <button type="button" onclick="setBulkCategoryInput('${safeCat}')" class="px-2.5 py-1 rounded-xl text-xs font-bold border transition active:scale-95 cursor-pointer ${isSelected ? 'bg-amber-500 text-gray-950 border-amber-600 shadow-xs' : 'bg-white hover:bg-amber-50 hover:text-amber-900 text-slate-800 border-slate-200'}">
                            <span>${safeCat}</span>
                        </button>
                    `;
                }).join('');
            }

            // Ensure datalist also has all categories for autocomplete
            const dl = document.getElementById('allCategoriesList');
            if (dl) {
                const existing = new Set(Array.from(dl.options).map(o => o.value));
                allCats.forEach(cat => {
                    if (!existing.has(cat)) {
                        const opt = document.createElement('option');
                        opt.value = cat;
                        dl.appendChild(opt);
                    }
                });
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
                    stock: 0,
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

        // ==========================================
        // 📱 WHATSAPP & RAW PRICE FORMULA ENGINE (JS)
        // ==========================================
        let rawPriceExtractedItems = [];
        let rawPriceCalculatedItems = [];

        function openRawPriceFormulaModal() {
            const modal = document.getElementById('rawPriceFormulaModal');
            if (!modal) return;
            modal.classList.remove('hidden');
            const textarea = document.getElementById('rawPriceTextInput');
            if (textarea) textarea.focus();
            updateRawPriceStats();
            if (rawPriceExtractedItems.length > 0) {
                recalculateRawFormulaRates();
            }
        }

        function closeRawPriceFormulaModal() {
            const modal = document.getElementById('rawPriceFormulaModal');
            if (modal) modal.classList.add('hidden');
        }

        function updateRawPriceStats() {
            const text = document.getElementById('rawPriceTextInput')?.value || '';
            const lines = text.split(/\r?\n/).filter(l => l.trim().length > 0).length;
            const statsEl = document.getElementById('rawPriceStatsText');
            if (statsEl) {
                statsEl.innerText = `${lines} raw lines pasted`;
            }
        }

        function cleanWhatsAppText(t) {
            if (!t) return '';
            return t.normalize('NFKD')
                .replace(/[\u0300-\u036f]/g, '') // strip unicode underline / combining marks
                .replace(/[*_~`]/g, '')           // strip markdown styling
                .replace(/➡️|➡|👉|🔹|🔸|▪️|•/g, ' ') // strip emojis
                .trim();
        }

        function parseIndianTradeNumber(valStr) {
            if (!valStr) return 0;
            let s = valStr.toString().trim()
                .replace(/₹/g, '')
                .replace(/\/-$/, '')
                .replace(/-$/, '')
                .trim();
            // Handle Indian mandi notation: 30/25 => 30.25, 52/50 => 52.50, 71/75 => 71.75
            if (/^\d+\/\d+$/.test(s)) {
                const parts = s.split('/');
                return parseFloat(parts[0] + '.' + parts[1]);
            }
            const num = parseFloat(s);
            return isNaN(num) ? 0 : num;
        }

        function loadSampleRawPriceText() {
            const sample = `*14.9.26 Price List* 
All Nett Rates A
Ex Godoun Rates
All With Bills Rates
*PLYWOODS, SHATTERINGS, BLOCK BOARDS AND FLUSH DOORS* 
➡️ *1) Super Local* 
18m(14)-46/-, 12m(8)-30/25, 6m(5)-22/-
➡️ *2) Super F.T.* 
18m(16)-52/50, 12m(10)-37/-
➡️ *3) Kerla Hardwood Best Quality* 
18-57/-, 12-46/25, 9-36/75, 6-24/25
➡️ *4) Okomo Face MR/Dipping Popular Best Quality* 
18-61/-, 12-49/25, 6-25/25
➡️ *5) Garjan Face MR Popular Best Quality* 
18m-64/50, 12m-53/-, 6m-29/-
➡️ *6) R/W Super 3 Line Falli Mix Okomo Face Dipping* 
18m-68/50, 12m-52/75, 6m-29/-
As Per Order Garjan Face Available +2/50 Sqft 
➡️ *7) R/W Platinum 100% Core Okomo Face Dipping* 
18m-71/75, 12m-56/50
As Per Order Garjan Face Available +2/50 Sqft 
➡️ *8) R/W Extra Layer Full Core Full Panal ISI Okomo Face Teak Patti (MAXICAN /WOOD / ASGARD)* 
19m-71/50, 12m-56/50
➡️ *9)Full Red Super 3 Line Falli Mix 710 BWR Garjan Face*
18m-76/25, 12m-58/75
➡️ *10)FULL RED 100% Core 710 BWP Garjan  Face(RED VILLA Platinum)*  
19-83/-, 16m-79/50, 12-63/75, 9m-55/-, 6-36/-                         

*Block Boards* 
➡️ *Popular MR ISI Block Boards* 
19mm-75/25
25mm-89/-
➡️ *100% Pine 100% BWP ISI Block Boards* 
19mm-97/75
25mm-109/-

*SHATTERING PLY* 
➡️ 1) 25kg Ambossed MARVALLOUS  Printed(16-17kg-10m) 38/50
25kg Ambossed RED DAIMOND Printed(19-20kg-10m) 42/25
25kg Ambossed A-ONE  Printed(22kg+ -12m) 47/25
➡️ 2) 30kg Ambossed RED DAIMOND Printed(27kg-13m) 53/50
➡️ 3) 30kg High Densified BLACK FILM CONCRETE GOLD/DELTA Printed MMUF(Semi Water Proof) 58/25
➡️ 4) UNITED KINGDOM 30kg Y.N. 100% PF All Glu Layered, Mirror Finish, High Densified ISI(Water Proof) 64/75
➡️ 5) KEENGDOM 34kg Y.N. 100% PF All Glu Layered, Mirror Finish, High Densified ISI(Water Proof) 83/-

*FLUSH DOORS* 
➡️ *Raipur Super ISO*
Single - 79/-
Double Door - 10% extra
With Pattam - 15% extra 
*Window* 
Single - 15% extra 
Double - 25% extra 
With Pattam - 30% extra 
➡️ *Gujrat H/W MR ISI* - 98/-
➡️ *PINE BWP ISI 30m* - 140/-
➡️ *PINE BWP ISI 35m* - 154/-
*In H/W above 84" Height plus 5/- sqft*`;

            const textarea = document.getElementById('rawPriceTextInput');
            if (textarea) textarea.value = sample;
            
            // Set realistic default freight and cash less for Plywood
            const freightInput = document.getElementById('formulaFreightPerUnit');
            if (freightInput && (parseFloat(freightInput.value) === 0 || !freightInput.value)) {
                freightInput.value = '2.50';
            }
            const lessInput = document.getElementById('formulaLessPercent');
            if (lessInput && (parseFloat(lessInput.value) === 0 || !lessInput.value)) {
                lessInput.value = '2.0';
            }

            updateRawPriceStats();
            parseRawPriceFast();
        }

                /**
         * ⚡ Instant Client-Side Parser for WhatsApp & Mandi Rate Lists
         */
        function parseRawPriceFast() {
            const rawText = document.getElementById('rawPriceTextInput')?.value || '';
            if (!rawText.trim()) {
                alert('Pehle upar text box me WhatsApp message ya price list paste karein.');
                return;
            }

            const defaultCat = (document.getElementById('rawPriceTargetCategory')?.value || '').trim() || 'Plywoods & Boards';
            const selectedUnit = document.getElementById('rawPriceTargetUnit')?.value || 'sqft';
            const rawLines = rawText.split(/\r?\n/).map(l => l.trim()).filter(Boolean);

            let currentCategory = defaultCat;
            let currentProductName = '';
            let currentNote = '';
            const items = [];

            for (let i = 0; i < rawLines.length; i++) {
                const line = rawLines[i];
                const cleaned = cleanWhatsAppText(line);
                if (!cleaned) continue;

                // 1. Detect Category Sections
                if (/^(BLOCK\s*BOARDS?|SHATTERING\s*PLY|SHATTERING|SHUTTERING|FLUSH\s*DOORS?|PLYWOODS?|HARDWARE|SANITARY|PIPES?|ELECTRICAL)/i.test(cleaned)) {
                    const match = cleaned.match(/^(BLOCK\s*BOARDS?|SHATTERING\s*PLY|SHATTERING|SHUTTERING|FLUSH\s*DOORS?|PLYWOODS?|HARDWARE|SANITARY|PIPES?|ELECTRICAL)/i);
                    if (match) {
                        let cat = match[0].toUpperCase();
                        if (cat.includes('PLYWOOD')) currentCategory = 'Plywoods';
                        else if (cat.includes('BLOCK')) currentCategory = 'Block Boards';
                        else if (cat.includes('SHATTER') || cat.includes('SHUTTER')) currentCategory = 'Shuttering Ply';
                        else if (cat.includes('DOOR')) currentCategory = 'Flush Doors';
                        else currentCategory = cat;

                        currentProductName = '';
                        continue;
                    }
                }

                // 2. Ignore pure header metadata lines
                if (/^(Price List|All Nett|Ex Godoun|Ex Godown|All With Bills|In H\/W above)/i.test(cleaned)) {
                    if (/above\s*84/i.test(cleaned)) {
                        currentNote = cleaned;
                    }
                    continue;
                }

                // 3. Standalone Price on next line (e.g. "64/75" or "83/-")
                if (/^\d+(?:\/\d+|\.\d+)?(?:\/-)?$/.test(cleaned)) {
                    const rateNum = parseIndianTradeNumber(cleaned);
                    if (rateNum > 0 && currentProductName) {
                        items.push({
                            category: currentCategory,
                            product_name: currentProductName,
                            variant: 'Standard',
                            base_rate: rateNum,
                            unit: (currentCategory.includes('Plywood') || currentCategory.includes('Board') || currentCategory.includes('Shuttering') || currentCategory.includes('Door')) ? 'sqft' : (selectedUnit === 'auto' ? 'sqft' : selectedUnit),
                            notes: currentNote || null
                        });
                        continue;
                    }
                }

                // 4. Notes / Conditions on doors (e.g. Double Door - 10% extra, With Pattam - 15% extra)
                if (/^(Double Door|With Pattam|Window)/i.test(cleaned) || /%\s*extra/i.test(cleaned) || /^As Per Order/i.test(cleaned)) {
                    if (items.length > 0) {
                        const lastItem = items[items.length - 1];
                        lastItem.notes = (lastItem.notes ? (lastItem.notes + '; ') : '') + cleaned;
                    }
                    continue;
                }

                // 5. Special Single Door condition: e.g. "Single - 79/-"
                if (/^Single\s*[-:]\s*\d+/i.test(cleaned)) {
                    const m = cleaned.match(/^Single\s*[-:]\s*(\d+(?:\/\d+|\.\d+)?)/i);
                    if (m) {
                        const rate = parseIndianTradeNumber(m[1]);
                        items.push({
                            category: currentCategory,
                            product_name: currentProductName || 'Flush Door',
                            variant: 'Single Door',
                            base_rate: rate,
                            unit: 'sqft',
                            notes: currentNote || null
                        });
                        continue;
                    }
                }

                // 6. Check if line is multi-variant (multiple prices separated by comma, e.g. "18m-46/-, 12m-30/25")
                const subTokens = cleaned.split(/,\s*/);
                const tokensWithPrice = subTokens.filter(t => /(?:[-:]|\s+)\s*\d+(?:\/\d+|\.\d+)?(?:\/-)?$/.test(t.trim()));

                if (tokensWithPrice.length > 1) {
                    subTokens.forEach(token => {
                        const t = token.trim();
                        if (!t) return;
                        const priceMatch = t.match(/^(.*?)(?:[-:]|\s+)\s*(\d+(?:\/\d+|\.\d+)?)(?:\/-)?$/);
                        if (priceMatch) {
                            let variantName = priceMatch[1].trim();
                            const rateNum = parseIndianTradeNumber(priceMatch[2]);
                            if (rateNum > 0) {
                                let prodName = currentProductName || 'Plywood / Board Item';
                                if (variantName.includes(currentProductName) && variantName !== currentProductName) {
                                    variantName = variantName.replace(currentProductName, '').trim();
                                }
                                if (!variantName) variantName = 'Standard';

                                if (/^\d+m\(/i.test(variantName)) {
                                    variantName = variantName.replace(/^(\d+)m\(/i, '$1mm (');
                                } else if (/^\d+m$/i.test(variantName)) {
                                    variantName = variantName.replace(/^(\d+)m$/i, '$1mm');
                                } else if (/^\d+$/.test(variantName) && (currentCategory.includes('Plywood') || currentCategory.includes('Board') || currentCategory.includes('PLY') || currentCategory.includes('BOARD'))) {
                                    variantName = variantName + 'mm';
                                }

                                items.push({
                                    category: currentCategory,
                                    product_name: prodName,
                                    variant: variantName,
                                    base_rate: rateNum,
                                    unit: (currentCategory.includes('Plywood') || currentCategory.includes('Board') || currentCategory.includes('Shuttering') || currentCategory.includes('Door')) ? 'sqft' : (selectedUnit === 'auto' ? 'sqft' : selectedUnit),
                                    notes: currentNote || null
                                });
                            }
                        }
                    });
                    continue;
                }

                // 7. Standalone product heading with price at the end
                const standaloneItemMatch = cleaned.match(/^(?:(?:\d+[\)\.]\s*)?)(.*?)[\s\-:]+(\d+(?:\/\d+|\.\d+)?)(?:\/-)?$/);
                if (standaloneItemMatch) {
                    let titlePart = standaloneItemMatch[1].replace(/^\d+[\)\.]\s*/, '').trim();
                    const ratePart = standaloneItemMatch[2];
                    const rateNum = parseIndianTradeNumber(ratePart);

                    const isJustVariant = /^(?:\d+m|\d+mm|\d+\"|\d+)$/i.test(titlePart);

                    if (!isJustVariant && titlePart.length > 2 && rateNum > 0) {
                        let variant = 'Standard';
                        let prodName = titlePart;

                        const bracketMatch = prodName.match(/\(([^)]*(?:kg|m|mm)[^)]*)\)/i);
                        if (bracketMatch) {
                            variant = bracketMatch[1].trim();
                        } else {
                            const trailingThick = prodName.match(/\s+(\d+(?:mm|m))\b/i);
                            if (trailingThick) {
                                variant = trailingThick[1].replace(/m$/i, 'mm');
                                prodName = prodName.substring(0, trailingThick.index).trim();
                            }
                        }

                        currentProductName = prodName;
                        items.push({
                            category: currentCategory,
                            product_name: prodName,
                            variant: variant,
                            base_rate: rateNum,
                            unit: (currentCategory.includes('Plywood') || currentCategory.includes('Board') || currentCategory.includes('Shuttering') || currentCategory.includes('Door')) ? 'sqft' : (selectedUnit === 'auto' ? 'sqft' : selectedUnit),
                            notes: currentNote || null
                        });
                        continue;
                    } else if (isJustVariant && rateNum > 0) {
                        let variantName = titlePart;
                        if (/^\d+$/.test(variantName)) variantName += 'mm';
                        items.push({
                            category: currentCategory,
                            product_name: currentProductName || 'Plywood / Board Item',
                            variant: variantName,
                            base_rate: rateNum,
                            unit: (currentCategory.includes('Plywood') || currentCategory.includes('Board') || currentCategory.includes('Shuttering') || currentCategory.includes('Door')) ? 'sqft' : (selectedUnit === 'auto' ? 'sqft' : selectedUnit),
                            notes: currentNote || null
                        });
                        continue;
                    }
                }

                // 8. Pure Product Heading line (no price on this line)
                let nameCandidate = cleaned.replace(/^\d+[\)\.]\s*/, '').replace(/:\s*$/, '').trim();
                if (nameCandidate.length > 2) {
                    currentProductName = nameCandidate;
                }
            }

            if (items.length === 0) {
                alert('Koi product ya rates detect nahi hue. Kripya check karein ki text me items aur unke rates (jaise 18m-46/- ya 30/25) mojud hain.');
                return;
            }

            rawPriceExtractedItems = items;
            recalculateRawFormulaRates();
            showRawPriceAlert(`⚡ Fast Parse Success: ${items.length} items successfully detect ho gaye!`, 'emerald');
        }

        async function parseRawPriceAI() {
            const rawText = document.getElementById('rawPriceTextInput')?.value || '';
            if (!rawText.trim()) {
                alert('Pehle text box me WhatsApp message paste karein.');
                return;
            }

            const btn = document.getElementById('btnRawPriceAI');
            const alertBox = document.getElementById('rawPriceStatusAlert');
            const alertContent = document.getElementById('rawPriceStatusContent');
            const defaultCat = (document.getElementById('rawPriceTargetCategory')?.value || '').trim() || 'Plywoods & Boards';

            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            }

            showRawPriceAlert('<i class="fa-solid fa-spinner fa-spin text-emerald-600"></i> <span>AI Extraction Engine running... Raw text aur Indian trade notations analyze ho rahe hain...</span>', 'indigo');

            try {
                const response = await fetch("{{ route('seller.catalog.ai_parse_raw_text') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        raw_text: rawText,
                        category: defaultCat
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'AI parsing request fail hua.');
                }

                if (!data.items || data.items.length === 0) {
                    throw new Error('AI ne text me se koi valid product detect nahi kiya.');
                }

                rawPriceExtractedItems = data.items.map(item => ({
                    category: item.category || defaultCat,
                    product_name: item.product_name || 'Standard Product',
                    variant: item.variant || 'Standard',
                    unit: item.unit || data.detected_unit || 'sqft',
                    base_rate: parseFloat(item.base_rate) || 0,
                    notes: item.notes || null
                }));

                recalculateRawFormulaRates();
                showRawPriceAlert(`🎉 AI Success (${data.provider || 'Deep AI'}): ${rawPriceExtractedItems.length} products with notes & trade variants detect ho gaye!`, 'emerald');

            } catch (err) {
                console.warn('AI Parse error:', err);
                showRawPriceAlert(`⚠️ AI Notice: ${err.message}. Running Instant Fast Parse locally...`, 'amber');
                parseRawPriceFast();
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }

        function showRawPriceAlert(html, theme = 'emerald') {
            const alertBox = document.getElementById('rawPriceStatusAlert');
            const alertContent = document.getElementById('rawPriceStatusContent');
            if (!alertBox || !alertContent) return;

            alertBox.className = `p-3 rounded-2xl text-xs font-bold flex items-center justify-between border ${
                theme === 'emerald' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' :
                theme === 'indigo' ? 'bg-indigo-50 text-indigo-800 border-indigo-200' :
                theme === 'amber' ? 'bg-amber-50 text-amber-800 border-amber-200' :
                'bg-rose-50 text-rose-800 border-rose-200'
            }`;
            alertContent.innerHTML = html;
            alertBox.classList.remove('hidden');
        }

        let customSheetSizes = [];

        function promptAddCustomSheetSize() {
            const label = prompt('Naya Sheet Size likhein (jaise "8 x 3" ya "9 x 4"):', '8 x 3');
            if (!label || !label.trim()) return;
            
            const areaStr = prompt(`"${label.trim()}" ka total Area (Sq. Ft.) kitna hai? (e.g. 8x3 ke liye 24 sqft):`, '24');
            const area = parseFloat(areaStr);
            if (!area || isNaN(area) || area <= 0) {
                alert('Valid area sqft me likhein.');
                return;
            }

            customSheetSizes.push({ label: label.trim(), area: area, active: true });
            renderCustomSheetSizePills();
            recalculateRawFormulaRates();
            showToastNotification(`🎉 Naya size "${label.trim()} (${area} sqft)" add ho gaya!`);
        }

        function renderCustomSheetSizePills() {
            const container = document.getElementById('customSheetSizePillsContainer');
            if (!container) return;
            container.innerHTML = customSheetSizes.map((cs, idx) => `
                <label class="px-2 py-0.5 rounded-lg bg-purple-950/90 hover:bg-purple-900 border border-purple-500/60 text-purple-200 text-[11px] font-bold cursor-pointer flex items-center gap-1.5 transition select-none">
                    <input type="checkbox" class="sheet-size-cb rounded text-purple-500 focus:ring-purple-400 h-3.5 w-3.5" value="${escapeHtml(cs.label)}" ${cs.active ? 'checked' : ''} onchange="toggleCustomSheetSize(${idx}, this.checked)">
                    <span>${escapeHtml(cs.label)} (${cs.area} sqft)</span>
                </label>
            `).join('');
        }

        function toggleCustomSheetSize(idx, isChecked) {
            if (customSheetSizes[idx]) {
                customSheetSizes[idx].active = isChecked;
                recalculateRawFormulaRates();
            }
        }

        function toggleSheetSizesConfigUI() {
            const isChecked = document.getElementById('formulaExpandSheetSizes')?.checked ?? false;
            const configRow = document.getElementById('formulaSheetSizesConfigRow');
            if (configRow) {
                if (isChecked) {
                    configRow.classList.remove('hidden');
                } else {
                    configRow.classList.add('hidden');
                }
            }
        }

        function setSheetSizePreset(preset) {
            const checkboxes = document.querySelectorAll('.sheet-size-cb');
            checkboxes.forEach(cb => {
                if (preset === 'all') {
                    cb.checked = true;
                } else if (preset === 'popular') {
                    cb.checked = (cb.value === '8 x 4' || cb.value === '7 x 4');
                } else if (preset === 'shuttering_only') {
                    cb.checked = (cb.value === '8 x 4');
                }
            });
            recalculateRawFormulaRates();
        }

        function removeSpecificSizeFromPreview(sizeLabel) {
            // 1. Uncheck in the global config so it stays off
            const checkboxes = document.querySelectorAll('.sheet-size-cb');
            checkboxes.forEach(cb => {
                if (cb.value === sizeLabel) cb.checked = false;
            });

            // 2. Filter out from currently calculated rows
            const initialCount = rawPriceCalculatedItems.length;
            rawPriceCalculatedItems = rawPriceCalculatedItems.filter(r => !r.variant.includes(sizeLabel));
            renderRawPricePreviewTable();

            const removedCount = initialCount - rawPriceCalculatedItems.length;
            showToastNotification(`🗑️ Preview se ${sizeLabel} ke sabhi ${removedCount} items hata diye gaye hain!`);
        }

        /**
         * 🧮 Live Pricing Formula Engine: Recalculates Less, Bhada, Selling Rates & Sheet Sizes
         */
        function recalculateRawFormulaRates() {
            if (!rawPriceExtractedItems || rawPriceExtractedItems.length === 0) {
                renderRawPricePreviewTable();
                return;
            }

            const lessPct = parseFloat(document.getElementById('formulaLessPercent')?.value) || 0;
            const freightMode = document.getElementById('formulaFreightMode')?.value || 'flat';
            const freightVal = parseFloat(document.getElementById('formulaFreightPerUnit')?.value) || 0;

            const freightPrefix = document.getElementById('formulaFreightUnitPrefix');
            if (freightPrefix) {
                freightPrefix.innerText = (freightMode === 'percent') ? '%' : '₹';
            }

            const rateAMode = document.getElementById('formulaRateAMode')?.value || 'percent';
            const rateAVal = parseFloat(document.getElementById('formulaRateAValue')?.value) || 0;

            const rateBMode = document.getElementById('formulaRateBMode')?.value || 'percent';
            const rateBVal = parseFloat(document.getElementById('formulaRateBValue')?.value) || 0;

            const rateCMode = document.getElementById('formulaRateCMode')?.value || 'percent';
            const rateCVal = parseFloat(document.getElementById('formulaRateCValue')?.value) || 0;

            const expandSheets = document.getElementById('formulaExpandSheetSizes')?.checked ?? false;

            // Get all user-selected active sheet sizes
            const activeSizesMap = {
                '8 x 4': 32,
                '7 x 4': 28,
                '6 x 4': 24,
                '7 x 3': 21,
                '6 x 3': 18
            };
            customSheetSizes.forEach(cs => {
                if (cs.active) {
                    activeSizesMap[cs.label] = cs.area;
                }
            });

            const selectedSizeLabels = Array.from(document.querySelectorAll('.sheet-size-cb:checked')).map(cb => cb.value);

            const calculated = [];

            rawPriceExtractedItems.forEach((item, idx) => {
                const base = item.base_rate;
                // Landing Cost: (Base - Less%) + Freight (Flat ₹ or % of Base)
                const afterLess = base * (1 - (lessPct / 100));
                const freightAmt = (freightMode === 'percent') ? (base * (freightVal / 100)) : freightVal;
                const landing = Math.round((afterLess + freightAmt) * 100) / 100;

                // Rate A (Retail)
                let rA = (rateAMode === 'percent') ? (landing * (1 + (rateAVal / 100))) : (landing + rateAVal);
                rA = Math.round(rA * 100) / 100;

                // Rate B (Wholesale)
                let rB = (rateBMode === 'percent') ? (landing * (1 + (rateBVal / 100))) : (landing + rateBVal);
                rB = Math.round(rB * 100) / 100;

                // Rate C (Bulk)
                let rC = (rateCMode === 'percent') ? (landing * (1 + (rateCVal / 100))) : (landing + rateCVal);
                rC = Math.round(rC * 100) / 100;

                const isSqFt = (item.unit || '').toLowerCase().includes('sq');
                
                // Smart Rule: Shuttering Ply me ONLY 8x4 size hota hai!
                const isShuttering = (item.category || '').toUpperCase().includes('SHATTER') || 
                                     (item.category || '').toUpperCase().includes('SHUTTER') || 
                                     (item.product_name || '').toUpperCase().includes('SHATTER') || 
                                     (item.product_name || '').toUpperCase().includes('SHUTTER');

                const isFlushDoor = (item.category || '').toUpperCase().includes('DOOR') || 
                                    (item.product_name || '').toUpperCase().includes('DOOR') ||
                                    (item.product_name || '').toUpperCase().includes('WINDOW');

                if (expandSheets && isSqFt && !isFlushDoor) {
                    // If Shuttering, strictly use 8x4 only!
                    const targetSizes = isShuttering 
                        ? ['8 x 4'] 
                        : (selectedSizeLabels.length > 0 ? selectedSizeLabels : ['8 x 4']);

                    targetSizes.forEach(label => {
                        const area = activeSizesMap[label] || 32;
                        calculated.push({
                            id: idx + '_' + label,
                            category: item.category,
                            product_name: item.product_name,
                            variant: `${item.variant} [${label} = ${area} sqft]`,
                            unit: 'sheet',
                            base_rate: Math.round(base * area * 100) / 100,
                            landing_cost: Math.round(landing * area * 100) / 100,
                            rate_a: Math.round(rA * area * 100) / 100,
                            rate_b: Math.round(rB * area * 100) / 100,
                            rate_c: Math.round(rC * area * 100) / 100,
                            notes: item.notes ? `${item.notes} | Base: ₹${base}/sqft` : `Base: ₹${base}/sqft`
                        });
                    });
                } else {
                    calculated.push({
                        id: idx,
                        category: item.category,
                        product_name: item.product_name,
                        variant: item.variant,
                        unit: item.unit || 'sqft',
                        base_rate: base,
                        landing_cost: landing,
                        rate_a: rA,
                        rate_b: rB,
                        rate_c: rC,
                        notes: item.notes || ''
                    });
                }
            });

            rawPriceCalculatedItems = calculated;
            renderRawPricePreviewTable();
        }

        /**
         * Render the live preview table with calculated pricing
         */
        function renderRawPricePreviewTable() {
            const tbody = document.getElementById('rawPricePreviewBody');
            const countBadge = document.getElementById('rawPricePreviewCount');
            const summaryText = document.getElementById('rawPricePreviewSummaryText');
            if (!tbody) return;

            if (rawPriceCalculatedItems.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="12" class="p-8 text-center text-gray-400 text-xs">
                            <div class="max-w-sm mx-auto space-y-1.5">
                                <i class="fa-brands fa-whatsapp text-3xl text-emerald-400"></i>
                                <p class="font-bold text-gray-600">Upar WhatsApp message paste karein aur "Fast Parse" ya "AI Smart Extract" dabayein</p>
                                <p class="text-[11px] text-gray-400">Plywood, Timber, Hardware, Flush Doors ya kisi bhi trade ka message turant calculate hoga.</p>
                            </div>
                        </td>
                    </tr>
                `;
                if (countBadge) countBadge.innerText = '0 items';
                if (summaryText) summaryText.innerText = '';
                return;
            }

            if (countBadge) countBadge.innerText = `${rawPriceCalculatedItems.length} items`;

            const totalLanding = rawPriceCalculatedItems.reduce((acc, r) => acc + (r.landing_cost || 0), 0);
            const totalRetail = rawPriceCalculatedItems.reduce((acc, r) => acc + (r.rate_a || 0), 0);
            const avgLanding = (totalLanding / rawPriceCalculatedItems.length).toFixed(2);
            const avgRetail = (totalRetail / rawPriceCalculatedItems.length).toFixed(2);

            if (summaryText) {
                summaryText.innerHTML = `<span class="font-bold text-gray-700">Avg Landing:</span> ₹${avgLanding} &nbsp;|&nbsp; <span class="font-bold text-emerald-700">Avg Selling (Rate A):</span> ₹${avgRetail}`;
            }

            let html = '';
            rawPriceCalculatedItems.forEach((row, i) => {
                html += `
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-2 text-center text-gray-400 font-mono text-[10px]">${i + 1}</td>
                        <td class="p-2">
                            <input list="allCategoriesList" value="${escapeHtml(row.category || 'Plywoods & Boards')}" onchange="updateRawPreviewItemField(${i}, 'category', this.value)" class="w-28 text-[11px] font-bold text-indigo-900 bg-white border border-indigo-200 hover:border-indigo-400 focus:border-indigo-600 rounded-lg px-2 py-1 shadow-2xs transition" placeholder="Category" title="Click to change Category for this product">
                        </td>
                        <td class="p-2 min-w-[170px]">
                            <input type="text" value="${escapeHtml(row.product_name)}" oninput="updateRawPreviewItemField(${i}, 'product_name', this.value)" class="w-full font-bold text-gray-900 bg-transparent border border-transparent hover:border-gray-300 focus:border-emerald-600 focus:bg-white rounded px-1 py-0.5 transition text-xs" title="Edit Product Name">
                        </td>
                        <td class="p-2 min-w-[160px]">
                            <input type="text" value="${escapeHtml(row.variant)}" oninput="updateRawPreviewItemField(${i}, 'variant', this.value)" class="w-full font-mono font-bold text-purple-900 bg-transparent border border-transparent hover:border-purple-300 focus:border-purple-600 focus:bg-white rounded px-1 py-0.5 transition text-xs" title="Edit Size / Thickness / Dimensions">
                        </td>
                        <td class="p-2 font-mono text-[10px] text-gray-500 font-bold uppercase">${escapeHtml(row.unit)}</td>
                        <td class="p-2 font-mono">
                            <div class="flex items-center">
                                <span class="text-gray-400 text-xs mr-0.5">₹</span>
                                <input type="number" step="0.25" value="${row.base_rate}" oninput="updateRawPreviewItemRate(${i}, parseFloat(this.value) || 0)" class="w-16 font-mono text-gray-800 bg-transparent border border-transparent hover:border-gray-300 focus:border-emerald-600 focus:bg-white rounded p-1 transition text-xs" title="Edit Base Rate">
                            </div>
                        </td>
                        <td class="p-2 font-mono font-bold text-amber-700 bg-amber-50/40" id="landing_cell_${i}">₹${row.landing_cost.toFixed(2)}</td>
                        <td class="p-2 font-mono bg-emerald-50/40">
                            <div class="flex items-center">
                                <span class="text-gray-400 text-xs mr-0.5">₹</span>
                                <input type="number" step="0.25" value="${row.rate_a}" oninput="updateRawPreviewItemField(${i}, 'rate_a', parseFloat(this.value) || 0)" class="w-16 font-mono font-black text-emerald-700 bg-transparent border border-transparent hover:border-emerald-300 focus:border-emerald-600 focus:bg-white rounded p-1 transition text-xs" title="Edit Selling Rate A">
                            </div>
                        </td>
                        <td class="p-2 font-mono font-bold text-cyan-700">₹${row.rate_b.toFixed(2)}</td>
                        <td class="p-2 font-mono font-bold text-indigo-700">₹${row.rate_c.toFixed(2)}</td>
                        <td class="p-2 text-[10px] text-gray-500 truncate max-w-[140px]" title="${escapeHtml(row.notes || '-')}">${escapeHtml(row.notes || '-')}</td>
                        <td class="p-2 text-center">
                            <button type="button" onclick="deleteRawPricePreviewRow(${i})" class="h-6 w-6 rounded-lg bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 flex items-center justify-center text-xs transition" title="Remove line">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        }

        function updateRawPreviewItemField(idx, field, value) {
            if (rawPriceCalculatedItems[idx]) {
                rawPriceCalculatedItems[idx][field] = value;
            }
        }

        function updateRawPreviewItemRate(idx, newBaseRate) {
            if (!rawPriceCalculatedItems[idx]) return;
            const row = rawPriceCalculatedItems[idx];
            row.base_rate = newBaseRate;
            
            const lessPct = parseFloat(document.getElementById('formulaLessPercent')?.value) || 0;
            const freightMode = document.getElementById('formulaFreightMode')?.value || 'flat';
            const freightVal = parseFloat(document.getElementById('formulaFreightPerUnit')?.value) || 0;
            const rateAMode = document.getElementById('formulaRateAMode')?.value || 'percent';
            const rateAVal = parseFloat(document.getElementById('formulaRateAValue')?.value) || 0;

            const afterLess = newBaseRate * (1 - (lessPct / 100));
            const freightAmt = (freightMode === 'percent') ? (newBaseRate * (freightVal / 100)) : freightVal;
            const landing = Math.round((afterLess + freightAmt) * 100) / 100;
            let rA = (rateAMode === 'percent') ? (landing * (1 + (rateAVal / 100))) : (landing + rateAVal);

            row.landing_cost = landing;
            row.rate_a = Math.round(rA * 100) / 100;
            
            const landingCell = document.getElementById(`landing_cell_${idx}`);
            if (landingCell) landingCell.innerText = `₹${landing.toFixed(2)}`;
        }

        function deleteRawPricePreviewRow(idx) {
            rawPriceCalculatedItems.splice(idx, 1);
            renderRawPricePreviewTable();
        }

        /**
         * 📥 Insert calculated rows directly into dynamicRows spreadsheet table
         */
        function insertRawPriceRowsToDynamicTable(replaceMode = false) {
            if (rawPriceCalculatedItems.length === 0) {
                alert('Pehle upar text paste karein aur kam se kam 1 product calculate karein.');
                return;
            }

            if (replaceMode || dynamicRows.length === 0) {
                dynamicRows = [];
                groupedProductCards = [];
                dynamicRowNextId = 1;
            }

            rawPriceCalculatedItems.forEach((item, idx) => {
                dynamicRows.push({
                    id: dynamicRowNextId++,
                    product_code: '',
                    model_no: '',
                    item_code: '',
                    hsn_code: '',
                    product_name: item.product_name,
                    size: item.variant,
                    packing_1: item.unit || 'sqft',
                    packing_2: '',
                    group_type: item.category,
                    category: item.category,
                    description: item.notes || '',
                    mrp: item.base_rate,
                    purchase_cost: item.landing_cost,
                    retail_price: item.rate_a,
                    cost_price_2: item.rate_b,
                    cost_price_3: item.rate_c,
                    stock: 0,
                    image_url: preselectedImg || '',
                    asset_url: preselectedImg ? ('/' + preselectedImg.replace(/^\//, '')) : ''
                });
            });

            saveToLocalStorage();
            if (typeof autoSaveDraftToBackend === 'function') autoSaveDraftToBackend();
            renderDynamicRows();
            refreshCategoryFilterTabs();
            closeRawPriceFormulaModal();

            showToastNotification(`🎉 Shabaash! ${rawPriceCalculatedItems.length} products clean Table Listing me add ho gaye! Ab aap rows select karke apni marzi se Cards bana sakte hain.`);

            setTimeout(() => {
                document.getElementById('dynamicTableBody')?.scrollIntoView({ behavior: 'smooth' });
            }, 250);
        }

        /**
         * 🎴 Direct Create Grouped Product Cards (Bina alag se manually group kiye)
         */
        function insertRawPriceRowsAndAutoGroupCards() {
            if (rawPriceCalculatedItems.length === 0) {
                alert('Pehle upar text paste karein aur kam se kam 1 product calculate karein.');
                return;
            }

            insertRawPriceRowsToDynamicTable(false);

            if (typeof autoGroupAllDynamicRowsIntoCards === 'function') {
                setTimeout(() => {
                    autoGroupAllDynamicRowsIntoCards();
                }, 300);
            }
        }

        /**
         * 📥 Export calculated preview table to clean CSV
         */
        function exportRawPricePreviewToCsv() {
            if (rawPriceCalculatedItems.length === 0) {
                alert('Export karne ke liye koi calculated rows nahi hain.');
                return;
            }

            const headers = ['Category', 'Product Name', 'Thickness / Size', 'Unit', 'Base Rate', 'Landing Cost', 'Rate A (Retail)', 'Rate B (Wholesale)', 'Rate C (Bulk)', 'Notes'];
            const rows = rawPriceCalculatedItems.map(r => [
                `"${(r.category || '').replace(/"/g, '""')}"`,
                `"${(r.product_name || '').replace(/"/g, '""')}"`,
                `"${(r.variant || '').replace(/"/g, '""')}"`,
                `"${(r.unit || '').replace(/"/g, '""')}"`,
                r.base_rate || 0,
                r.landing_cost || 0,
                r.rate_a || 0,
                r.rate_b || 0,
                r.rate_c || 0,
                `"${(r.notes || '').replace(/"/g, '""')}"`
            ]);

            const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement('a');
            link.setAttribute('href', encodedUri);
            link.setAttribute('download', `whatsapp_price_list_${new Date().toISOString().slice(0, 10)}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
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
            const pendingTabBadge = document.getElementById('pendingTabCountBadge');
            if (pendingTabBadge) {
                const totalPending = dynamicRows.length + groupedProductCards.length;
                pendingTabBadge.innerText = `${totalPending} Items`;
            }
            if (typeof updatePublishButtonText === 'function') updatePublishButtonText();

            if (!tbody) return;

            if (dynamicRows.length === 0) {
                if (badge) badge.innerText = `0 Rows`;
                if (groupedProductCards.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="13" class="p-10 text-center">
                                <div class="max-w-lg mx-auto bg-amber-50/70 border-2 border-dashed border-amber-300 rounded-3xl p-6 space-y-3.5 shadow-sm">
                                    <div class="h-12 w-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl mx-auto shadow-xs">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-black text-gray-900">Workspace me Cards ya Rows load nahi hue?</h4>
                                        <p class="text-xs text-gray-600 mt-1">
                                            Browser refresh hone par ya naye tab me kholne par data yahan se 1-click me turant restore karein:
                                        </p>
                                    </div>
                                    <div class="pt-2 flex items-center justify-center gap-2.5 flex-wrap">
                                        <button type="button" onclick="restoreDraftCardsAndRows()" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-md shadow-indigo-600/30 flex items-center gap-2 transition active:scale-95">
                                            <i class="fa-solid fa-rotate-left"></i>
                                            <span>🔄 Restore Last Saved Cards & List</span>
                                        </button>
                                        <button type="button" onclick="reExtractFromPdfLines()" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-600/30 flex items-center gap-2 transition active:scale-95">
                                            <i class="fa-solid fa-file-pdf"></i>
                                            <span>📄 Reload from PDF Lines</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
                } else {
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
                }
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

                    const trMod = detectCardModule(r);
                    html += `
                    <tr class="hover:bg-slate-50 transition dynamic-row-item" id="dyn_row_${r.id}" data-category="${escapeHtml(catVal)}" data-module="${trMod}" data-search="${escapeHtml(searchStr)}">
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
                            <input type="text" value="${escapeHtml(r.model_no || '')}" placeholder="Model No" oninput="updateDynamicRowField(${r.id}, 'model_no', this.value)" class="w-20 text-[11px] font-mono font-bold text-purple-700 border border-transparent hover:border-gray-300 focus:border-purple-600 focus:bg-white rounded-lg p-1 transition bg-transparent" title="Model No (Printed on PDF)">
                        </td>
                        <td class="p-3">
                            <input type="text" value="${escapeHtml(r.item_code || r.product_code || '')}" placeholder="Item Code" oninput="updateDynamicRowField(${r.id}, 'product_code', this.value); updateDynamicRowField(${r.id}, 'item_code', this.value);" class="w-24 text-[11px] font-mono text-gray-700 border border-transparent hover:border-gray-300 focus:border-emerald-600 focus:bg-white rounded-lg p-1 transition bg-transparent" title="Product / Item Code (SKU)">
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
                if (autoSaveTimer) clearTimeout(autoSaveTimer);
                dynamicRows = [];
                groupedProductCards = [];
                if (typeof serverJobProducts !== 'undefined') {
                    serverJobProducts = [];
                }
                localStorage.removeItem('vyapar_custom_excel_lines');
                localStorage.removeItem('vyapar_grouped_cards');
                localStorage.removeItem('vyapar_dynamic_rows');
                localStorage.removeItem('vyapar_grouped_cards_backup');
                localStorage.removeItem('vyapar_dynamic_rows_backup');
                localStorage.removeItem('vyapar_grouped_next_id');
                localStorage.removeItem('vyapar_dynamic_next_id');
                renderDynamicRows();
                renderGroupedProductCards();
                refreshCategoryFilterTabs();
                clearDynamicRowSelection();

                // Clear backend draft so reload doesn't restore deleted items
                if (jobId) {
                    fetch("{{ route('seller.catalog.excel_mapper.create_sheet') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({
                            job_id: jobId,
                            sheet_name: 'Catalog Sheet',
                            rows: [],
                            grouped_cards: [],
                            publish: false
                        })
                    }).catch(e => console.warn(e));
                }

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
            let csv = "Category,Product Name,Model No,Item Code,HSN Code,Description,Size/Dimension,Packing 1,Packing 2,MRP,Purchase Cost,Cost Price 2,Cost Price 3,Selling Price,Stock,Image URL\n";

            // Add grouped cards
            targetCards.forEach(c => {
                const escapeCsv = (str) => `"${(str || '').toString().replace(/"/g, '""')}"`;
                c.variants.forEach(v => {
                    csv += [
                        escapeCsv(c.category || 'General Hardware'),
                        escapeCsv(c.parent_name),
                        escapeCsv(v.model_no || c.model_no || ''),
                        escapeCsv(v.item_code || v.product_code || ''),
                        escapeCsv(v.hsn_code || ''),
                        escapeCsv(c.description || v.description || ''),
                        escapeCsv(v.size || ''),
                        escapeCsv(v.packing_1 || ''),
                        escapeCsv(v.packing_2 || ''),
                        v.mrp || 0,
                        v.purchase_cost || 0,
                        v.cost_price_2 || 0,
                        v.cost_price_3 || 0,
                        v.retail_price || 0,
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
                    escapeCsv(r.product_name),
                    escapeCsv(r.model_no || ''),
                    escapeCsv(r.item_code || r.product_code || ''),
                    escapeCsv(r.hsn_code || ''),
                    escapeCsv(r.description || ''),
                    escapeCsv(r.size || ''),
                    escapeCsv(r.packing_1 || ''),
                    escapeCsv(r.packing_2 || ''),
                    r.mrp || 0,
                    r.purchase_cost || 0,
                    r.cost_price_2 || 0,
                    r.cost_price_3 || 0,
                    r.retail_price || 0,
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

        function updatePublishButtonText() {
            const btnText = document.getElementById('btnSaveDynamicSheetText');
            const btnAll = document.getElementById('btnSaveAllDynamicSheet');
            if (!btnText) return;

            let targetCategory = (typeof activeCategory !== 'undefined' && activeCategory) ? activeCategory : 'ALL';
            let targetRows = dynamicRows;
            let targetCards = groupedProductCards;

            if (targetCategory !== 'ALL') {
                const normCat = targetCategory.trim().toLowerCase();
                targetRows = dynamicRows.filter(r => (r.group_type || r.category || 'General Hardware').trim().toLowerCase() === normCat);
                targetCards = groupedProductCards.filter(c => (c.category || '').trim().toLowerCase() === normCat);
            }

            const totalInCat = targetRows.length + targetCards.length;
            const totalAll = dynamicRows.length + groupedProductCards.length;

            if (targetCategory === 'ALL' || totalInCat === totalAll) {
                btnText.innerHTML = `<span>Save & Publish All (${totalAll})</span>`;
                if (btnAll) btnAll.classList.add('hidden');
            } else {
                btnText.innerHTML = `<span>Publish "${targetCategory}" Only (${totalInCat})</span>`;
                if (btnAll) {
                    btnAll.classList.remove('hidden');
                    btnAll.innerHTML = `<i class="fa-solid fa-layer-group text-amber-300"></i> <span>Publish All (${totalAll})</span>`;
                }
            }
        }

        function saveDynamicSheetToBackend(forceAll = false) {
            let targetCategory = (forceAll || activeCategory === 'ALL') ? 'ALL' : activeCategory;
            
            let targetRows = dynamicRows;
            let targetCards = groupedProductCards;

            if (targetCategory !== 'ALL') {
                const normCat = targetCategory.trim().toLowerCase();
                targetRows = dynamicRows.filter(r => (r.group_type || r.category || 'General Hardware').trim().toLowerCase() === normCat);
                targetCards = groupedProductCards.filter(c => (c.category || '').trim().toLowerCase() === normCat);
            }

            if (targetRows.length === 0 && targetCards.length === 0) {
                alert(`Category "${targetCategory}" me publish karne ke liye koi items nahi hain.`);
                return;
            }

            const totalPublishing = targetRows.length + targetCards.length;
            const remainingCount = (dynamicRows.length + groupedProductCards.length) - totalPublishing;

            let confirmMsg = '';
            if (targetCategory === 'ALL') {
                confirmMsg = `Kya aap sabhi categories ke ${totalPublishing} products ko live store me publish karna chahte hain?`;
            } else {
                confirmMsg = `Kya aap sirf "${targetCategory}" category ke ${totalPublishing} products ko live store me publish karna chahte hain?\n\n✨ Bachi hui dusri categories (${remainingCount} items) Pending Draft me hi rahengi taaki aap unhe baad me publish kar sakein!`;
            }

            if (!confirm(confirmMsg)) return;

            // Immediately cancel any pending autoSave timer
            if (autoSaveTimer) clearTimeout(autoSaveTimer);

            const sheetTitle = (document.getElementById('dynamicSheetName')?.value || 'Catalog Sheet').trim() + 
                (targetCategory !== 'ALL' ? ` (${targetCategory})` : '');

            const btn = document.getElementById('btnSaveDynamicSheet');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> ${targetCategory !== 'ALL' ? `Publishing "${targetCategory}"...` : 'Live Store Me Publish Ho Raha Hai...'}`;
            }

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
                    rows: targetRows,
                    grouped_cards: targetCards,
                    publish: true
                })
            })
            .then(res => res.json())
            .then(data => {
                if (btn) {
                    btn.disabled = false;
                }

                if (data.success) {
                    if (targetCategory === 'ALL' || remainingCount === 0) {
                        localStorage.removeItem('vyapar_grouped_cards');
                        localStorage.removeItem('vyapar_dynamic_rows');
                        localStorage.removeItem('vyapar_custom_excel_lines');
                        localStorage.removeItem('vyapar_grouped_cards_backup');
                        localStorage.removeItem('vyapar_dynamic_rows_backup');
                        dynamicRows = [];
                        groupedProductCards = [];
                    } else {
                        // Category-specific publish: REMOVE ONLY THIS CATEGORY from pending!
                        const normCat = targetCategory.trim().toLowerCase();
                        dynamicRows = dynamicRows.filter(r => (r.group_type || r.category || 'General Hardware').trim().toLowerCase() !== normCat);
                        groupedProductCards = groupedProductCards.filter(c => (c.category || '').trim().toLowerCase() !== normCat);
                        saveToLocalStorage();
                    }

                    if (typeof serverJobProducts !== 'undefined') {
                        serverJobProducts = [];
                    }
                    renderDynamicRows();
                    renderGroupedProductCards();
                    refreshCategoryFilterTabs();
                    updatePublishButtonText();

                    alert(data.message || `🎉 "${targetCategory}" ke products live store me successfully publish ho gaye!`);
                    // Switch to Live Tab filtered by that category!
                    window.location.href = "{{ route('seller.catalog.excel_mapper') }}?view=live&category=" + encodeURIComponent(targetCategory);
                } else {
                    alert(data.message || 'Error saving sheet.');
                    updatePublishButtonText();
                }
            })
            .catch(err => {
                if (btn) {
                    btn.disabled = false;
                }
                updatePublishButtonText();
                console.error(err);
                alert('Network error while saving dynamic sheet.');
            });
        }

        // ==========================================
        // 🎛️ DUAL WORKSPACE: PENDING DRAFT vs LIVE
        // ==========================================
        function switchMapperMainView(mode) {
            const btnPending = document.getElementById('viewBtnPending');
            const btnLive = document.getElementById('viewBtnLive');
            const pendingContainer = document.getElementById('dynamicSheetContainer');
            const liveContainer = document.getElementById('liveStoreProductsContainer');

            if (mode === 'live') {
                if (pendingContainer) pendingContainer.classList.add('hidden');
                if (liveContainer) liveContainer.classList.remove('hidden');

                if (btnPending) {
                    btnPending.className = 'px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 text-gray-600 hover:text-gray-900 cursor-pointer';
                }
                if (btnLive) {
                    btnLive.className = 'px-5 py-2.5 rounded-xl font-black text-xs transition flex items-center gap-2 bg-white text-gray-900 shadow-sm border border-gray-200 cursor-pointer';
                }
            } else {
                if (pendingContainer) pendingContainer.classList.remove('hidden');
                if (liveContainer) liveContainer.classList.add('hidden');

                if (btnPending) {
                    btnPending.className = 'px-5 py-2.5 rounded-xl font-black text-xs transition flex items-center gap-2 bg-white text-gray-900 shadow-sm border border-gray-200 cursor-pointer';
                }
                if (btnLive) {
                    btnLive.className = 'px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 text-gray-600 hover:text-gray-900 cursor-pointer';
                }
            }
        }

        let activeLiveCategory = 'ALL';
        function filterLiveProductsByCategory(cat) {
            activeLiveCategory = cat;
            document.querySelectorAll('.live-cat-tab-btn').forEach(btn => {
                const bCat = btn.getAttribute('data-cat');
                if (bCat === cat) {
                    btn.className = 'live-cat-tab-btn px-4 py-1.5 rounded-xl font-extrabold text-xs transition bg-emerald-600 text-white shadow-xs cursor-pointer';
                } else {
                    btn.className = 'live-cat-tab-btn px-4 py-1.5 rounded-xl font-bold text-xs transition bg-gray-100 hover:bg-gray-200 text-gray-700 cursor-pointer';
                }
            });

            applyLiveProductFilters();
        }

        function filterLiveProductsSearch(query) {
            applyLiveProductFilters();
        }

        function applyLiveProductFilters() {
            const query = (document.getElementById('liveProductsSearchInput')?.value || '').trim().toLowerCase();
            const rows = document.querySelectorAll('.live-product-row');
            let visibleCount = 0;

            rows.forEach(tr => {
                const rowCat = (tr.getAttribute('data-category') || '').trim();
                const rowName = (tr.getAttribute('data-name') || '').trim().toLowerCase();

                const catMatch = (activeLiveCategory === 'ALL' || rowCat.toLowerCase() === activeLiveCategory.toLowerCase());
                const searchMatch = (!query || rowName.includes(query) || rowCat.toLowerCase().includes(query));

                if (catMatch && searchMatch) {
                    tr.style.display = '';
                    visibleCount++;
                } else {
                    tr.style.display = 'none';
                }
            });

            const badge = document.getElementById('liveHeaderCountBadge');
            if (badge) badge.innerText = `${visibleCount} Products`;
        }

        function escapeHtml(str) {
            return (str || '').toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        function getImageKey(url) {
            if (!url) return '';
            try {
                const clean = url.split('?')[0].replace(/^https?:\/\/[^\/]+/i, '').replace(/^\/+/, '');
                const parts = clean.split('/');
                return (parts[parts.length - 1] || clean).toLowerCase().trim();
            } catch(e) {
                return (url || '').toLowerCase().trim();
            }
        }

        function cleanupDuplicatePhotosFromDom() {
            const seenDrawer = new Set();
            document.querySelectorAll('#drawerImagesGrid .drawer-img-card').forEach(card => {
                const key = card.getAttribute('data-img-key') || getImageKey(card.querySelector('img')?.getAttribute('src') || '');
                if (!key || seenDrawer.has(key)) {
                    card.remove();
                } else {
                    seenDrawer.add(key);
                }
            });

            const seenTray = new Set();
            document.querySelectorAll('#onPagePhotoGrid .on-page-photo-card').forEach(card => {
                const key = card.getAttribute('data-img-key') || getImageKey(card.querySelector('img')?.getAttribute('src') || '');
                if (!key || seenTray.has(key)) {
                    card.remove();
                } else {
                    seenTray.add(key);
                }
            });

            const totalDrawer = document.querySelectorAll('.drawer-img-card').length;
            const countEl = document.getElementById('drawerImagesCount');
            if (countEl) countEl.innerText = `${totalDrawer} Photos`;

            const onPageCount = document.querySelectorAll('.on-page-photo-card').length;
            const onPageCountEl = document.getElementById('onPagePhotoCount');
            if (onPageCountEl) onPageCountEl.innerText = `${onPageCount} Photos`;
        }

        function renderPhotosIntoUi(images) {
            if (!Array.isArray(images) || images.length === 0) return;

            const grid = document.getElementById('drawerImagesGrid');
            const onPageGrid = document.getElementById('onPagePhotoGrid');
            const emptyPrompt = document.getElementById('drawerEmptyPrompt');
            const onPageLoading = document.getElementById('onPagePhotoLoadingMsg');

            if (emptyPrompt) emptyPrompt.remove();
            if (onPageLoading) onPageLoading.remove();

            const existingDrawerKeys = new Set();
            if (grid) {
                grid.querySelectorAll('.drawer-img-card').forEach(el => {
                    const k = el.getAttribute('data-img-key') || getImageKey(el.querySelector('img')?.getAttribute('src'));
                    if (k) existingDrawerKeys.add(k);
                });
            }

            const existingTrayKeys = new Set();
            if (onPageGrid) {
                onPageGrid.querySelectorAll('.on-page-photo-card').forEach(el => {
                    const k = el.getAttribute('data-img-key') || getImageKey(el.querySelector('img')?.getAttribute('src'));
                    if (k) existingTrayKeys.add(k);
                });
            }

            images.forEach(img => {
                const imgUrl = img.url || img.asset_url || '';
                const assetUrl = img.asset_url || img.url || '';
                const name = img.name || 'Catalog Item';
                const source = img.source === 'plasto_master' ? 'Master' : 'Crop';
                const folder = img.folder || img.category || 'General';
                const key = getImageKey(imgUrl);
                if (!key) return;

                // Check if already in drawer
                if (grid && !existingDrawerKeys.has(key)) {
                    existingDrawerKeys.add(key);
                    const card = document.createElement('div');
                    card.className = "drawer-img-card border border-gray-200 rounded-2xl p-2 bg-gray-50 hover:bg-indigo-50 hover:border-indigo-400 cursor-pointer text-center group transition";
                    card.setAttribute('data-name', name.toLowerCase());
                    card.setAttribute('data-folder', folder.toLowerCase());
                    card.setAttribute('data-raw-folder', folder);
                    card.setAttribute('data-img-key', key);
                    card.onclick = () => selectDrawerImage(imgUrl, assetUrl);
                    card.innerHTML = `
                        <div class="h-20 w-full bg-white rounded-xl p-1 mb-1.5 flex items-center justify-center overflow-hidden border border-gray-100 group-hover:scale-105 transition">
                            <img src="${assetUrl}" alt="${escapeHtml(name)}" class="max-h-full max-w-full object-contain">
                        </div>
                        <h5 class="text-[11px] font-bold text-gray-800 truncate" title="${escapeHtml(name)}">${escapeHtml(name)}</h5>
                        <span class="text-[9px] text-indigo-600 font-semibold">${source}</span>
                        <span class="text-[8px] text-gray-500 font-bold truncate block">📁 ${escapeHtml(folder)}</span>
                    `;
                    grid.appendChild(card);
                }

                // Check if already in on-page tray
                if (onPageGrid && !existingTrayKeys.has(key)) {
                    existingTrayKeys.add(key);
                    const trayCard = document.createElement('div');
                    trayCard.className = "on-page-photo-card flex items-center gap-2.5 p-2 bg-gray-50 hover:bg-indigo-50 border border-gray-200 hover:border-indigo-400 rounded-2xl cursor-pointer transition shrink-0 group";
                    trayCard.setAttribute('data-name', name.toLowerCase());
                    trayCard.setAttribute('data-folder', folder.toLowerCase());
                    trayCard.setAttribute('data-raw-folder', folder);
                    trayCard.setAttribute('data-img-key', key);
                    trayCard.onclick = () => handleOnPagePhotoClick(imgUrl, assetUrl);
                    trayCard.innerHTML = `
                        <div class="h-14 w-14 bg-white rounded-xl p-1 flex items-center justify-center overflow-hidden border border-gray-200 group-hover:scale-105 transition">
                            <img src="${assetUrl}" alt="${escapeHtml(name)}" class="max-h-full max-w-full object-contain">
                        </div>
                        <div class="max-w-[120px]">
                            <h5 class="text-[11px] font-bold text-gray-800 truncate" title="${escapeHtml(name)}">${escapeHtml(name)}</h5>
                            <span class="text-[9px] text-indigo-600 font-semibold block">${source}</span>
                            <span class="text-[8px] text-gray-500 font-bold truncate block">📁 ${escapeHtml(folder)}</span>
                            <span class="text-[9px] text-emerald-600 font-bold group-hover:underline">Attach &rarr;</span>
                        </div>
                    `;
                    onPageGrid.appendChild(trayCard);
                }
            });

            cleanupDuplicatePhotosFromDom();
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
            initIndustryModules();
            initDynamicSheet();
            cleanupDuplicatePhotosFromDom();
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
