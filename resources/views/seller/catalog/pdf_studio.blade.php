<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Side-by-Side PDF Studio & AI Catalog Copilot - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Mozilla PDF.js for Native Client-Side PDF Rendering -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <style>
        .crop-canvas-container {
            cursor: crosshair;
            position: relative;
            user-select: none;
            display: inline-block;
        }
        .crop-selection-box {
            position: absolute;
            border: 2px dashed #4f46e5;
            background: rgba(79, 70, 229, 0.22);
            pointer-events: none;
            box-shadow: 0 0 16px rgba(79, 70, 229, 0.45);
            border-radius: 6px;
        }
        @keyframes pulseGlow {
            0% { transform: scale(0.96); opacity: 0; }
            50% { transform: scale(1.03); }
            100% { transform: scale(1); opacity: 1; }
        }
        .new-crop-glow {
            animation: pulseGlow 0.4s ease-out forwards;
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3) !important;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: rgba(0,0,0,0.03); }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.2); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.35); }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen pb-20">

    <!-- Hidden Local PDF File Input -->
    <input type="file" id="pdfFileInput" accept="application/pdf" class="hidden" onchange="handleLocalPdfSelect(this.files)">

    <!-- Top Sticky Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.catalog.gallery') }}" class="h-9 w-9 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 flex items-center justify-center text-sm transition">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-black text-gray-900 text-base tracking-tight">Interactive PDF Studio</span>
                            <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-200 flex items-center gap-1.5">
                                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Side-by-Side Live Workspace</span>
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-500">
                            PDF select karein, rectangle drag karke crop karein — turant Bagal Gallery me save ho jayegi!
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <!-- AI Copilot Button -->
                    <button type="button" onclick="toggleAiDrawer()" class="px-3.5 py-2.5 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white font-extrabold text-xs shadow-md shadow-indigo-600/25 flex items-center gap-1.5 transition active:scale-95">
                        <i class="fa-solid fa-wand-magic-sparkles text-amber-300"></i>
                        <span>AI Copilot</span>
                        <span id="aiHeaderStatusDot" class="h-2 w-2 rounded-full bg-amber-400"></span>
                    </button>

                    <!-- Open PDF Button -->
                    <button type="button" onclick="document.getElementById('pdfFileInput').click()" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-md shadow-indigo-600/30 flex items-center gap-2 transition active:scale-95">
                        <i class="fa-solid fa-folder-open"></i>
                        <span>Open PDF</span>
                    </button>

                    <a href="{{ route('seller.catalog.gallery') }}" class="px-3.5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition flex items-center gap-1.5">
                        <i class="fa-solid fa-images text-blue-600"></i>
                        <span>Media Vault</span>
                    </a>
                    
                    <a href="{{ route('seller.catalog.excel_mapper') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-600/20 flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-table-cells"></i>
                        <span>Excel Mapper</span>
                    </a>
                </div>
            </div>

            <!-- Suite Navigation Tabs -->
            <div class="flex items-center gap-6 border-t border-gray-100 pt-2 pb-1 overflow-x-auto">
                <a href="{{ route('seller.catalog.gallery') }}" class="pb-2 text-xs font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-900 flex items-center gap-2 whitespace-nowrap transition">
                    <i class="fa-solid fa-images"></i>
                    <span>1. Bulk Media Vault & Gallery</span>
                </a>
                <a href="{{ route('seller.catalog.pdf_studio') }}" class="pb-2 text-xs font-extrabold border-b-2 border-indigo-600 text-indigo-600 flex items-center gap-2 whitespace-nowrap">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>2. Side-by-Side PDF Studio</span>
                    <span id="navPageCountBadge" class="px-2 py-0.5 rounded-full text-[10px] bg-indigo-100 text-indigo-700 font-bold">Ready</span>
                </a>
                <a href="{{ route('seller.catalog.excel_mapper') }}" class="pb-2 text-xs font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-900 flex items-center gap-2 whitespace-nowrap transition">
                    <i class="fa-solid fa-table-cells"></i>
                    <span>3. Excel Multi-Row Mapper</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Workspace Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start" id="mainWorkspaceGrid">
            
            <!-- LEFT COLUMN: PDF View / Canvas & Tools -->
            <div id="pdfWorkspaceCol" class="lg:col-span-8 space-y-4 transition-all duration-300">
                
                <!-- STATE A: No PDF Loaded Yet (Drop Zone & File Selector) -->
                <div id="dropZoneContainer" class="bg-white rounded-3xl border-2 border-dashed border-indigo-300 hover:border-indigo-500 p-10 text-center transition cursor-pointer shadow-xs" onclick="document.getElementById('pdfFileInput').click()">
                    <div class="h-20 w-20 mx-auto rounded-3xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-4xl mb-4 shadow-inner">
                        <i class="fa-solid fa-file-pdf"></i>
                    </div>
                    <h3 class="text-lg font-black text-gray-900 mb-1">Apne Computer Se PDF Brochure Chunein</h3>
                    <p class="text-xs text-gray-500 max-w-md mx-auto mb-5 leading-relaxed">
                        Aapke computer me jahan bhi PDF catalog rakha hai (jaise Documents me <span class="font-mono text-indigo-600 font-bold">Plasto 2024.pdf</span> ya koi bhi PDF), use yahan select karein ya drag karke drop karein.
                    </p>
                    <div class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-indigo-600 text-white font-extrabold text-xs shadow-lg shadow-indigo-600/30 hover:bg-indigo-700 transition">
                        <i class="fa-solid fa-folder-open"></i>
                        <span>Choose PDF File from System</span>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-3">Supports any PDF catalog up to 100+ pages with instant zoom & crop</p>
                </div>

                <!-- STATE B: PDF Loaded Active Canvas Toolbar -->
                <div id="pdfActiveToolbar" class="bg-white p-3.5 rounded-2xl border border-gray-200 shadow-xs flex flex-wrap items-center justify-between gap-3 hidden">
                    
                    <div class="flex items-center gap-2 flex-wrap">
                        <!-- Prev / Next Controls -->
                        <button type="button" onclick="prevPdfPage()" id="btnPrevPage" class="h-9 w-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center text-xs font-bold transition disabled:opacity-40" title="Previous Page">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        
                        <!-- Page Selector Dropdown -->
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-bold text-gray-600">Page:</span>
                            <select id="pdfPageSelect" onchange="jumpToPdfPage(this.value)" class="px-3 py-1.5 rounded-xl border border-gray-300 text-xs font-extrabold text-gray-800 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <!-- Populated dynamically by PDF.js -->
                            </select>
                            <span class="text-xs text-gray-500 font-bold" id="pdfTotalPagesText">of 0</span>
                        </div>

                        <button type="button" onclick="nextPdfPage()" id="btnNextPage" class="h-9 w-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center text-xs font-bold transition disabled:opacity-40" title="Next Page">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>

                        <!-- Zoom Controls -->
                        <div class="flex items-center bg-gray-100 p-0.5 rounded-xl ml-1">
                            <button type="button" onclick="adjustZoom(-0.2)" class="h-7 w-7 rounded-lg hover:bg-white text-gray-600 text-xs font-bold transition" title="Zoom Out">-</button>
                            <span class="text-[11px] font-black text-gray-700 px-2" id="zoomText">100%</span>
                            <button type="button" onclick="adjustZoom(0.2)" class="h-7 w-7 rounded-lg hover:bg-white text-gray-600 text-xs font-bold transition" title="Zoom In">+</button>
                        </div>

                        <!-- Auto-Fit Width Button -->
                        <button type="button" onclick="fitCanvasToWidth()" class="px-2.5 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition flex items-center gap-1" title="Fit to Container Width (Poori Width Dikhe)">
                            <i class="fa-solid fa-arrows-left-right"></i>
                            <span>Fit Width</span>
                        </button>

                        <!-- Fit Entire Page Button -->
                        <button type="button" onclick="fitCanvasToPage()" class="px-2.5 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition flex items-center gap-1" title="Fit Entire Page (Poora Page Ek Sath Dikhe)">
                            <i class="fa-solid fa-file-lines"></i>
                            <span>Fit Page</span>
                        </button>

                        <!-- 📊 Convert Current Page to Excel (Free & AI Modes) -->
                        <button type="button" onclick="toggleTextDrawer(true)" class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-black transition flex items-center gap-1.5 shadow-sm active:scale-95" title="Is current page ka text aur table Excel Sheet me convert karein">
                            <i class="fa-solid fa-file-excel text-amber-300"></i>
                            <span>Convert Page to Excel</span>
                            <span class="px-1.5 py-0.5 rounded-md text-[9px] bg-white/20 text-white font-mono font-bold">0 Tokens / AI</span>
                        </button>

                        <!-- Layout Toggle: Split vs Full Page -->
                        <button type="button" onclick="toggleLayoutMode()" id="btnLayoutToggle" class="px-2.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-extrabold transition flex items-center gap-1 border border-indigo-200" title="Toggle Full Page Wide View">
                            <i class="fa-solid fa-expand" id="layoutToggleIcon"></i>
                            <span id="layoutToggleText">Full View</span>
                        </button>
                    </div>

                    <!-- Right actions: File Info & Switch PDF -->
                    <div class="flex items-center gap-2">
                        <span id="activeFileNameBadge" class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-xl border border-indigo-200 max-w-[160px] truncate" title="Active PDF">
                            catalog.pdf
                        </span>
                        <button type="button" onclick="document.getElementById('pdfFileInput').click()" class="px-3 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition flex items-center gap-1">
                            <i class="fa-solid fa-rotate"></i>
                            <span>Change PDF</span>
                        </button>
                    </div>

                </div>

                <!-- STATE C: Canvas Container & Crop Area -->
                <div id="pdfCanvasCard" class="bg-white rounded-3xl border border-gray-200 p-4 shadow-sm space-y-4 hidden">
                    
                    <div class="flex items-center justify-between text-xs pb-1 border-b border-gray-100">
                        <span class="font-bold text-indigo-700 flex items-center gap-1.5">
                            <i class="fa-solid fa-crosshairs animate-pulse"></i>
                            <span>Product ke chaaron taraf square box drag karein — direct <b>"Save Photo"</b> dabayein (Naam likhna optional hai):</span>
                        </span>
                        <span class="text-gray-400 font-mono text-[11px]" id="cropCoordsText">No region selected</span>
                    </div>

                    <!-- Big Canvas Viewport with 4-Way Scroll & Generous Height (No Flex Clipping Bug) -->
                    <div class="border rounded-2xl bg-slate-900/5 overflow-auto max-h-[84vh] p-6 text-center shadow-inner relative" id="canvasScrollArea">
                        
                        <div class="crop-canvas-container inline-block text-left align-top mx-auto" id="cropCanvasWrapper">
                            <canvas id="pdfPageCanvas" class="block shadow-md bg-white rounded-lg"></canvas>
                            
                            <!-- Visual Selection Box on Canvas -->
                            <div class="crop-selection-box hidden" id="cropSelectionBox"></div>

                            <!-- ⚡ Floating Instant Action Bubble (Appears right on square when mouse released) -->
                            <div id="cropActionBubble" onmousedown="event.stopPropagation()" onmouseup="event.stopPropagation()" onclick="event.stopPropagation()" class="absolute z-30 bg-gray-900/95 text-white p-2.5 rounded-2xl shadow-2xl border border-white/20 flex items-center gap-2 backdrop-blur-md hidden transition-all">
                                <button type="button" onclick="extractAndPushToGallery()" id="btnBubbleSave" class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-gray-950 font-black text-xs flex items-center gap-1.5 transition shadow-lg shadow-emerald-500/30 whitespace-nowrap active:scale-95 cursor-pointer">
                                    <i class="fa-solid fa-bolt text-sm"></i>
                                    <span>⚡ Save Photo</span>
                                </button>
                                <input type="text" id="bubbleTitleInput" placeholder="(Optional Naam)" class="px-3 py-1.5 rounded-xl bg-white/10 text-white placeholder-gray-400 text-xs border border-white/20 focus:outline-none focus:ring-2 focus:ring-emerald-400 w-36 font-medium" onkeydown="if(event.key === 'Enter') extractAndPushToGallery()">
                                <button type="button" onclick="resetCropBox()" class="h-8 w-8 rounded-xl hover:bg-white/20 text-gray-400 hover:text-white flex items-center justify-center text-xs cursor-pointer" title="Cancel Selection">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>

                        </div>

                    </div>

                    <!-- Bottom Action Bar -->
                    <div class="bg-slate-50 p-4 rounded-2xl border border-gray-200 flex flex-wrap items-center justify-between gap-3" id="bottomCropBar">
                        <div class="flex items-center gap-2 flex-1 min-w-[240px]">
                            <label class="text-xs font-bold text-gray-700 whitespace-nowrap">Product Name (Optional):</label>
                            <input type="text" id="cropProductTitle" placeholder="(Optional) e.g. CPVC Elbow, UPVC Tee... Khaali chhodne par bhi save ho jayega" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 bg-white font-medium" oninput="syncTitles(this.value)">
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" id="btnExtractToGallery" onclick="extractAndPushToGallery()" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-600/30 flex items-center gap-2 transition active:scale-95 cursor-pointer">
                                <i class="fa-solid fa-bolt"></i>
                                <span>Save Crop To Gallery</span>
                            </button>
                            <button type="button" onclick="resetCropBox()" class="px-3 py-2.5 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-bold transition">
                                Reset Box
                            </button>
                        </div>
                    </div>

                </div>

            </div>

            <!-- RIGHT COLUMN: Real-Time Live Gallery Vault -->
            <div id="galleryCol" class="lg:col-span-4 space-y-4 transition-all duration-300">
                
                <div class="bg-white rounded-3xl border border-gray-200 p-5 shadow-xs space-y-4 sticky top-24">
                    
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-black text-gray-900">Media Vault Gallery</h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-bold font-mono" id="rightGalleryCount">{{ count($galleryImages) }}</span>
                            </div>
                            <p class="text-[11px] text-gray-500">Naye photos sabse upar dikhenge, purane photos scroll karke dekhein (Kram-anusar)</p>
                        </div>
                        <a href="{{ route('seller.catalog.gallery') }}" target="_blank" class="h-8 w-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center text-xs transition" title="Open Full Media Vault">
                            <i class="fa-solid fa-up-right-from-square"></i>
                        </a>
                    </div>

                    <!-- Side Gallery Scrollable List -->
                    <div class="space-y-3 max-h-[75vh] overflow-y-auto pr-1 divide-y divide-gray-100/50" id="sideGalleryList">
                        @forelse($galleryImages as $gImg)
                            @php
                                $cardFolder = $gImg['folder'] ?? $gImg['category'] ?? 'General';
                            @endphp
                            <div class="gallery-item-card flex items-center gap-3 p-2.5 rounded-2xl bg-gray-50 hover:bg-indigo-50/50 border border-gray-200 transition group" id="card_img_{{ $gImg['id'] }}" data-img-url="{{ $gImg['url'] }}">
                                <div class="h-14 w-14 rounded-xl bg-white border border-gray-200 p-1 flex items-center justify-center shrink-0 overflow-hidden shadow-2xs">
                                    <img src="{{ $gImg['asset_url'] }}" alt="{{ $gImg['name'] }}" class="max-h-full max-w-full object-contain">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-xs font-bold text-gray-900 truncate" id="card_name_{{ $gImg['id'] }}" title="{{ $gImg['name'] }}">
                                        {{ $gImg['name'] }}
                                    </h4>
                                    <div class="flex items-center gap-1.5 text-[10px] text-gray-500 mt-0.5 flex-wrap">
                                        <span class="px-1.5 py-0.5 rounded bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold" id="card_folder_{{ $gImg['id'] }}">
                                            {{ $cardFolder }}
                                        </span>
                                        <span>{{ $gImg['size_kb'] }} KB</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button type="button" onclick="openEditCropModal('{{ $gImg['id'] }}', '{{ addslashes($gImg['name']) }}', '{{ addslashes($cardFolder) }}', '{{ $gImg['url'] }}', '{{ $gImg['asset_url'] }}')" title="Edit Details & Folder" class="h-8 w-8 rounded-xl bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 flex items-center justify-center text-xs transition">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <a href="{{ route('seller.catalog.excel_mapper') }}?assign_img={{ urlencode($gImg['url']) }}" title="Assign to Excel Rows" class="h-8 w-8 rounded-xl bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 flex items-center justify-center text-xs transition">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                    <button type="button" onclick="deleteSideGalleryImage('{{ $gImg['url'] }}', '{{ $gImg['id'] }}', '{{ addslashes($gImg['name']) }}')" title="Delete from Vault" class="h-8 w-8 rounded-xl bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 flex items-center justify-center text-xs transition">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center space-y-2" id="sideGalleryEmptyPrompt">
                                <div class="h-12 w-12 mx-auto rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center text-lg">
                                    <i class="fa-solid fa-images"></i>
                                </div>
                                <h4 class="text-xs font-bold text-gray-700">Gallery abhi khali hai</h4>
                                <p class="text-[11px] text-gray-400 max-w-xs mx-auto">
                                    Left side me PDF par rectangle box drag karein aur "Save Photo" dabayein.
                                </p>
                            </div>
                        @endforelse
                    </div>

                    <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-xs font-bold">
                        <a href="{{ route('seller.catalog.gallery') }}" class="text-indigo-600 hover:text-indigo-800">
                            View Full Media Vault &rarr;
                        </a>
                        <a href="{{ route('seller.catalog.excel_mapper') }}" class="text-emerald-700 hover:text-emerald-900">
                            Open Excel Mapper &rarr;
                        </a>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Hidden Native Crop Canvas for Generating Exact Snippets -->
    <canvas id="hiddenCropCanvas" style="display: none;"></canvas>

    <!-- Notification Toast -->
    <div id="toastNotification" class="fixed bottom-6 right-6 z-50 bg-gray-900 text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-3 text-xs font-bold transition-all duration-300 opacity-0 pointer-events-none transform translate-y-4">
        <i class="fa-solid fa-circle-check text-emerald-400 text-sm" id="toastIcon"></i>
        <span id="toastMsg">Notification message</span>
    </div>

    <!-- ======================================================= -->
    <!-- 🤖 AI CATALOG COPILOT SLIDE-OVER DRAWER & CHAT BOX     -->
    <!-- ======================================================= -->
    <div id="aiDrawerOverlay" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 hidden flex justify-end transition-opacity duration-300" onclick="toggleAiDrawer(false)">
        <div class="w-full max-w-md bg-white h-full shadow-2xl flex flex-col transform transition-transform duration-300" onclick="event.stopPropagation()">
            
            <!-- AI Header -->
            <div class="p-4 border-b border-gray-200 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-sm shadow-md">
                        <i class="fa-solid fa-wand-magic-sparkles text-amber-300"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-black tracking-tight">AI Catalog Copilot</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/10 text-emerald-300 font-mono font-bold" id="aiModelBadge">OpenAI / Gemini</span>
                        </div>
                        <p class="text-[11px] text-gray-400" id="aiPageContextStatus">Page 1 Active</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="toggleApiKeyPanel()" class="px-2.5 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-gray-200 text-xs font-bold flex items-center gap-1 transition" title="API Key Setup">
                        <i class="fa-solid fa-key text-amber-400"></i>
                        <span>API Key</span>
                    </button>
                    <button type="button" onclick="toggleAiDrawer(false)" class="h-8 w-8 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 flex items-center justify-center text-sm transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- API Key Configuration Dropdown Panel -->
            <div id="apiKeyPanel" class="p-4 bg-indigo-50 border-b border-indigo-100 space-y-3 hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black text-indigo-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-key text-indigo-600"></i>
                        <span>AI API Key (OpenAI / Gemini)</span>
                    </span>
                    <span id="keyStatusPill" class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-100 text-indigo-800">⚡ Server OPENAI_KEY Auto</span>
                </div>
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-2.5 text-[11px] text-emerald-800 leading-relaxed">
                    <i class="fa-solid fa-circle-check text-emerald-600 mr-1"></i>
                    <b>Sahi Hai:</b> Agar aapne apne server environment / Render me variable ka name <b>OPENAI_KEY</b> ya <b>OPENAI_API_KEY</b> daal diya hai, to backend use automatically use karega. Yahan dobara key paste karna zaroori nahi hai!
                </div>
                <p class="text-[11px] text-gray-600 leading-relaxed">
                    (Optional) Agar aap browser me direct custom OpenAI (<code class="bg-white px-1 py-0.5 rounded font-mono text-[10px]">sk-...</code>) ya Gemini (<code class="bg-white px-1 py-0.5 rounded font-mono text-[10px]">AIza...</code>) key save karna chahte hain, to yahan paste karein:
                </p>
                <div class="flex items-center gap-2">
                    <input type="password" id="geminiApiKeyInput" placeholder="sk-... ya AIza... (Paste API Key)" class="flex-1 px-3 py-2 text-xs rounded-xl border border-indigo-200 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-600 font-mono">
                    <button type="button" onclick="saveGeminiApiKey()" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-extrabold shadow-sm transition">
                        Save
                    </button>
                </div>
                <div class="flex items-center justify-between text-[10px] text-gray-500 pt-1">
                    <div class="flex items-center gap-3">
                        <a href="https://platform.openai.com/api-keys" target="_blank" class="text-indigo-600 hover:underline font-bold flex items-center gap-1">
                            <span>OpenAI Keys</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[8px]"></i>
                        </a>
                        <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-indigo-600 hover:underline font-bold flex items-center gap-1">
                            <span>Gemini Keys</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[8px]"></i>
                        </a>
                    </div>
                    <button type="button" onclick="clearGeminiApiKey()" class="text-rose-600 hover:underline">Clear Key</button>
                </div>
            </div>

            <!-- AI Quick Action Chips -->
            <div class="p-2.5 border-b border-gray-100 bg-gray-50/80 flex items-center gap-1.5 overflow-x-auto text-[11px] whitespace-nowrap">
                <button type="button" onclick="askAiPrompt('Is page ke sabhi products, fittings aur pipes identify karke unke standard names list karo.')" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 hover:bg-indigo-50 text-gray-700 font-medium transition">
                    🔍 Is page ke products pehchano
                </button>
                <button type="button" onclick="askAiPrompt('Plasto/CPVC fittings ke standard sizes (e.g. 1/2 inch to 2 inch) aur MRP table format me batao.')" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 hover:bg-indigo-50 text-gray-700 font-medium transition">
                    📏 Sizes & MRP list karo
                </button>
                <button type="button" onclick="askAiPrompt('Agar me crop kar raha hu, to product card title aur HSN code suggest karo.')" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 hover:bg-indigo-50 text-gray-700 font-medium transition">
                    💡 HSN & Title suggest karo
                </button>
            </div>

            <!-- Chat Message Stream -->
            <div class="flex-1 p-4 overflow-y-auto space-y-3" id="aiChatMessages">
                <!-- Welcome Message from AI -->
                <div class="flex items-start gap-2.5">
                    <div class="h-7 w-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs shrink-0 mt-0.5">
                        <i class="fa-solid fa-robot"></i>
                    </div>
                    <div class="bg-gray-100 text-gray-800 p-3 rounded-2xl rounded-tl-sm text-xs leading-relaxed max-w-[85%] space-y-1">
                        <p class="font-bold text-gray-900">Namaste! Mai aapka AI Catalog Copilot hu.</p>
                        <p>Aap PDF ka koi bhi page kholkar mujhse pooch sakte hain:</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-0.5">
                            <li>Is page par kaun se products hain?</li>
                            <li>Cropped photo ka name aur standard size kya hai?</li>
                            <li>Discount aur wholesale margin calculation</li>
                        </ul>
                        <p class="text-[10px] text-indigo-600 font-bold pt-1">Upar ⚙️ "API Key" button par click karke apni free Gemini Key jodein aur start karein!</p>
                    </div>
                </div>
            </div>

            <!-- Chat Input Bar -->
            <div class="p-3 border-t border-gray-200 bg-white">
                <div class="flex items-center gap-2">
                    <input type="text" id="aiChatInput" placeholder="AI se poochein (e.g. Is page ke sabhi items batao)..." class="flex-1 px-3.5 py-2.5 text-xs rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 font-medium" onkeydown="if(event.key === 'Enter') sendAiMessage()">
                    <button type="button" onclick="sendAiMessage()" id="btnSendAi" class="h-9 w-9 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white flex items-center justify-center text-xs transition shadow-sm active:scale-95">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- ======================================================= -->
    <!-- 📄 CONVERT CURRENT PAGE TO EXCEL SLIDE-OVER DRAWER       -->
    <!-- ======================================================= -->
    <div id="textDrawerOverlay" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 hidden flex justify-end transition-opacity duration-300" onclick="toggleTextDrawer(false)">
        <div class="w-full max-w-lg bg-white h-full shadow-2xl flex flex-col transform transition-transform duration-300" onclick="event.stopPropagation()">
            
            <!-- Drawer Header -->
            <div class="p-4 border-b border-gray-200 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 flex items-center justify-center text-sm shadow-md">
                        <i class="fa-solid fa-file-excel text-amber-300"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-black tracking-tight">Convert Page to Excel</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/10 text-emerald-300 font-mono font-bold" id="extractedLineCountBadge">0 Lines</span>
                        </div>
                        <p class="text-[11px] text-gray-400" id="textDrawerPageContext">Page 1</p>
                    </div>
                </div>

                <button type="button" onclick="toggleTextDrawer(false)" class="h-8 w-8 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 flex items-center justify-center text-sm transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Tab Switcher: Free Native vs AI Smart Table -->
            <div class="flex border-b border-gray-200 bg-gray-50 p-1.5 gap-1.5">
                <button type="button" onclick="switchTextDrawerTab('native')" id="tabBtnNative" class="flex-1 py-2 px-3 rounded-xl text-xs font-black transition flex items-center justify-center gap-1.5 bg-white text-indigo-700 shadow-2xs border border-gray-200">
                    <i class="fa-solid fa-bolt text-amber-500"></i>
                    <span>Free Text Lines</span>
                    <span class="px-1.5 py-0.5 rounded text-[9px] bg-emerald-100 text-emerald-800 font-mono">0 Tokens</span>
                </button>
                <button type="button" onclick="switchTextDrawerTab('ai')" id="tabBtnAi" class="flex-1 py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 text-gray-600 hover:text-gray-900">
                    <i class="fa-solid fa-wand-magic-sparkles text-violet-600"></i>
                    <span>AI Smart Table</span>
                    <span class="px-1.5 py-0.5 rounded text-[9px] bg-violet-100 text-violet-800 font-mono">Single Page</span>
                </button>
            </div>

            <!-- TAB 1: Free Native PDF.js Lines Extractor (0 AI Tokens) -->
            <div id="nativeDrawerTabContent" class="flex-1 flex flex-col min-h-0">
                <!-- Toolbar Actions: Select All, Copy, Send -->
                <div class="p-3 bg-violet-50 border-b border-violet-100 flex flex-wrap items-center justify-between gap-2 text-xs">
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="toggleSelectAllTextLines(true)" class="px-2.5 py-1 rounded-lg bg-white border border-violet-200 hover:bg-violet-100 text-violet-800 font-bold text-[11px]">
                            Select All
                        </button>
                        <button type="button" onclick="toggleSelectAllTextLines(false)" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 hover:bg-gray-100 text-gray-600 font-medium text-[11px]">
                            Clear
                        </button>
                        <span class="text-[11px] font-bold text-violet-900 ml-1" id="selectedLineCountText">0 Selected</span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="copySelectedLinesToClipboard()" class="px-3 py-1.5 rounded-xl bg-white border border-violet-300 hover:bg-violet-100 text-violet-800 font-extrabold text-[11px] flex items-center gap-1">
                            <i class="fa-solid fa-copy"></i>
                            <span>Copy</span>
                        </button>
                        <button type="button" onclick="sendSelectedLinesToExcelMapper()" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-[11px] flex items-center gap-1.5 shadow-sm active:scale-95">
                            <i class="fa-solid fa-table-cells"></i>
                            <span>Send to Excel Mapper &rarr;</span>
                        </button>
                    </div>
                </div>

                <!-- Hint text -->
                <div class="px-4 py-2 bg-gray-50 border-b border-gray-100 text-[11px] text-gray-600 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info text-indigo-600"></i>
                    <span>Lines select karein aur <b>"Send to Excel Mapper"</b> dabayein — bina kisi AI token ke turant table ban jayega!</span>
                </div>

                <!-- Extracted Lines List -->
                <div class="flex-1 p-4 overflow-y-auto space-y-1.5" id="extractedLinesList">
                    <p class="text-xs text-gray-400 text-center py-8">PDF load hone par is page ke sabhi text lines yahan dikhenge...</p>
                </div>
            </div>

            <!-- TAB 2: AI Smart Table Extractor (Single Page Token-Conserving) -->
            <div id="aiDrawerTabContent" class="flex-1 flex flex-col min-h-0 hidden p-4 space-y-4 overflow-y-auto">
                <!-- Token Safe Guarantee Banner -->
                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs text-emerald-900 space-y-1">
                    <div class="flex items-center gap-2 font-black text-emerald-800">
                        <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                        <span>100% Token Protection Active</span>
                    </div>
                    <p class="text-[11px] text-emerald-700 leading-relaxed">
                        AI sirf samne khule hue <b class="underline">Page <span id="aiActivePageNum">1</span></b> ka text padhega. Baki pages ko bilkul scan nahi karega taaki aapke tokens bilkul waste na hon.
                    </p>
                </div>

                <!-- AI Extract Button -->
                <div class="text-center space-y-2 pt-2">
                    <button type="button" onclick="runAiPageTableExtraction()" id="btnRunAiExtract" class="w-full py-3 px-4 rounded-2xl bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white font-black text-xs shadow-lg shadow-violet-600/30 transition flex items-center justify-center gap-2 active:scale-95 cursor-pointer">
                        <i class="fa-solid fa-wand-magic-sparkles text-amber-300"></i>
                        <span id="btnRunAiExtractText">✨ AI se Is Page ka Table Excel me Nikalein</span>
                    </button>
                    <p class="text-[10px] text-gray-400">Current page se Product Name, Size, MRP aur Category automatic table me convert honge</p>
                </div>

                <!-- Loading State -->
                <div id="aiExtractLoading" class="hidden text-center py-8 space-y-2">
                    <i class="fa-solid fa-circle-notch fa-spin text-2xl text-indigo-600"></i>
                    <p class="text-xs font-bold text-gray-700">AI current page ko analyze kar raha hai...</p>
                    <p class="text-[11px] text-gray-400">Tokens bachane ke liye sirf Page <span id="aiLoadingPageNum">1</span> scan ho raha hai</p>
                </div>

                <!-- Extracted AI Table Result Preview -->
                <div id="aiExtractResultArea" class="hidden space-y-3">
                    <div class="flex items-center justify-between pb-1 border-b border-gray-100">
                        <span class="text-xs font-black text-gray-900" id="aiResultRowCount">0 Items Extracted</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-violet-100 text-violet-800 font-mono font-bold" id="aiResultProviderBadge">AI</span>
                    </div>

                    <div class="border border-gray-200 rounded-2xl overflow-hidden max-h-64 overflow-y-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-100 text-[10px] font-black text-gray-600 uppercase border-b border-gray-200">
                                <tr>
                                    <th class="p-2">Product Name</th>
                                    <th class="p-2">Size</th>
                                    <th class="p-2">MRP</th>
                                    <th class="p-2">Category</th>
                                </tr>
                            </thead>
                            <tbody id="aiTablePreviewBody" class="divide-y divide-gray-100 text-[11px]">
                                <!-- Populated dynamically -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Push to Excel Mapper Button -->
                    <button type="button" onclick="sendAiTableToExcelMapper()" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md transition flex items-center justify-center gap-2 active:scale-95">
                        <i class="fa-solid fa-table-cells"></i>
                        <span>📥 Send Table to Excel Multi-Row Mapper &rarr;</span>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- ======================================================= -->
    <!-- ✏️ EDIT CROP DETAILS MODAL (Product Name & Folder)      -->
    <!-- ======================================================= -->
    <div id="editCropModal" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4" onclick="closeEditCropModal()">
        <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-gray-100 overflow-hidden transform transition-all" onclick="event.stopPropagation()">
            <div class="p-4 bg-gradient-to-r from-slate-900 to-indigo-950 text-white flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="h-8 w-8 rounded-xl bg-indigo-600 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold">Edit Photo Details</h4>
                        <p class="text-[10px] text-gray-300">Naam aur Category/Folder update karein</p>
                    </div>
                </div>
                <button type="button" onclick="closeEditCropModal()" class="h-8 w-8 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 flex items-center justify-center text-xs transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-5 space-y-4">
                <!-- Thumbnail preview -->
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-2xl border border-gray-200">
                    <div class="h-16 w-16 rounded-xl bg-white border border-gray-200 p-1 flex items-center justify-center overflow-hidden shrink-0 shadow-2xs">
                        <img id="editCropPreviewImg" src="" alt="Crop" class="max-h-full max-w-full object-contain">
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Media Vault Photo</span>
                        <p class="text-xs font-bold text-gray-800 truncate" id="editCropOriginalName"></p>
                    </div>
                </div>

                <input type="hidden" id="editCropImgId" value="">
                <input type="hidden" id="editCropImgUrl" value="">

                <!-- Title / Name -->
                <div>
                    <label class="block text-xs font-extrabold text-gray-700 mb-1">Product / Photo Title</label>
                    <input type="text" id="editCropTitleInput" class="w-full px-3.5 py-2 text-xs rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 font-bold text-gray-800" placeholder="e.g. CPVC Brass Elbow 90 Degree">
                </div>

                <!-- Folder / Category -->
                <div>
                    <label class="block text-xs font-extrabold text-gray-700 mb-1">Group / Folder / Category</label>
                    <select id="editCropFolderSelect" onchange="handleEditCropFolderChange(this.value)" class="w-full px-3.5 py-2 text-xs rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 font-bold text-gray-800 bg-white">
                        @foreach($folders ?? ['General'] as $f)
                            <option value="{{ $f }}">{{ $f }}</option>
                        @endforeach
                        <option value="__NEW__">➕ Naya Folder Banayein...</option>
                    </select>
                    <input type="text" id="editCropNewFolderInput" class="w-full px-3.5 py-2 text-xs rounded-xl border border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 font-bold text-indigo-900 mt-2 hidden" placeholder="Naye folder ka naam likhein...">
                </div>

                <!-- Action buttons -->
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" onclick="closeEditCropModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100 transition">
                        Cancel
                    </button>
                    <button type="button" onclick="saveEditCropDetails()" id="btnSaveCropDetails" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black shadow-md shadow-indigo-600/20 transition flex items-center gap-1.5 active:scale-95">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save Details</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Trigger for AI Copilot (Bottom Left) -->
    <button type="button" onclick="toggleAiDrawer(true)" class="fixed bottom-6 left-6 z-40 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white px-4 py-3 rounded-2xl shadow-xl flex items-center gap-2.5 text-xs font-black transition active:scale-95 border border-white/20">
        <i class="fa-solid fa-wand-magic-sparkles text-amber-300 animate-pulse text-sm"></i>
        <span>AI Catalog Copilot</span>
        <span id="floatingAiDot" class="h-2 w-2 rounded-full bg-amber-400"></span>
    </button>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // PDF.js worker setup
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let currentPdfDoc = null;
        let currentPdfPage = 1;
        let totalPdfPages = 0;
        let currentScale = 1.0; // Standard 100% initial render
        let isRenderingPage = false;
        let renderTaskPending = null;
        let isFullViewMode = false;
        let currentPageTextContent = '';

        // Crop Selection state
        let isDragging = false;
        let startX = 0, startY = 0, currentX = 0, currentY = 0;
        let cropCoords = null; // { x, y, w, h } relative to displayed canvas

        const canvas = document.getElementById('pdfPageCanvas');
        const ctx = canvas.getContext('2d');
        const boxEl = document.getElementById('cropSelectionBox');
        const bubbleEl = document.getElementById('cropActionBubble');
        const wrapperEl = document.getElementById('cropCanvasWrapper');

        // Check stored Gemini API Key on load
        window.addEventListener('DOMContentLoaded', () => {
            initApiKeyStatus();
        });

        function initApiKeyStatus() {
            const storedKey = localStorage.getItem('vyapar_ai_api_key') || localStorage.getItem('vyapar_gemini_api_key');
            const statusPill = document.getElementById('keyStatusPill');
            const floatingDot = document.getElementById('floatingAiDot');

            if (storedKey && storedKey.trim().length > 8) {
                if (statusPill) {
                    statusPill.className = "px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800";
                    statusPill.innerText = "🟢 Custom Key Active";
                }
                if (floatingDot) floatingDot.className = "h-2 w-2 rounded-full bg-emerald-400";
                const input = document.getElementById('geminiApiKeyInput');
                if (input) input.value = storedKey;
            } else {
                if (statusPill) {
                    statusPill.className = "px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-100 text-indigo-800";
                    statusPill.innerText = "⚡ Server OPENAI_KEY Auto";
                }
                if (floatingDot) floatingDot.className = "h-2 w-2 rounded-full bg-emerald-400";
            }
        }

        function saveGeminiApiKey() {
            const input = document.getElementById('geminiApiKeyInput');
            const key = input.value.trim();
            if (!key) {
                alert('Kripya valid API Key (OpenAI ya Gemini) daalein.');
                return;
            }
            localStorage.setItem('vyapar_ai_api_key', key);
            localStorage.setItem('vyapar_gemini_api_key', key);
            initApiKeyStatus();
            toggleApiKeyPanel(false);
            showToast('AI API Key successfully saved!', 'success');
            appendAiSystemMessage('Shukriya! AI API Key connect ho gayi hai. Ab aap mujhse is PDF catalog ke baare me kuch bhi pooch sakte hain.');
        }

        function clearGeminiApiKey() {
            localStorage.removeItem('vyapar_ai_api_key');
            localStorage.removeItem('vyapar_gemini_api_key');
            const input = document.getElementById('geminiApiKeyInput');
            if (input) input.value = '';
            initApiKeyStatus();
            showToast('API Key cleared.', 'info');
        }

        function toggleApiKeyPanel(force) {
            const panel = document.getElementById('apiKeyPanel');
            if (typeof force === 'boolean') {
                panel.classList.toggle('hidden', !force);
            } else {
                panel.classList.toggle('hidden');
            }
        }

        function toggleAiDrawer(force) {
            const overlay = document.getElementById('aiDrawerOverlay');
            if (typeof force === 'boolean') {
                overlay.classList.toggle('hidden', !force);
            } else {
                overlay.classList.toggle('hidden');
            }
        }

        // Drag & Drop Setup for the Drop Zone
        const dropZone = document.getElementById('dropZoneContainer');
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, e => {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.add('border-indigo-600', 'bg-indigo-50/30');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, e => {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.remove('border-indigo-600', 'bg-indigo-50/30');
            }, false);
        });

        dropZone.addEventListener('drop', e => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                handleLocalPdfSelect(files);
            }
        });

        // Handle PDF File Selected from System
        function handleLocalPdfSelect(files) {
            if (!files || files.length === 0) return;
            const file = files[0];
            if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
                alert('Kripya sirf valid PDF file (.pdf) chunein.');
                return;
            }

            document.getElementById('activeFileNameBadge').innerText = file.name;
            showToast(`Loading "${file.name}"...`, 'info');

            const fileReader = new FileReader();
            fileReader.onload = function(e) {
                const typedarray = new Uint8Array(e.target.result);
                loadPdfFromTypedArray(typedarray);
            };
            fileReader.readAsArrayBuffer(file);
        }

        // Load PDF using PDF.js
        function loadPdfFromTypedArray(data) {
            pdfjsLib.getDocument({ data: data }).promise.then(pdfDoc => {
                currentPdfDoc = pdfDoc;
                totalPdfPages = pdfDoc.numPages;
                currentPdfPage = 1;

                // Update UI elements
                document.getElementById('dropZoneContainer').classList.add('hidden');
                document.getElementById('pdfActiveToolbar').classList.remove('hidden');
                document.getElementById('pdfCanvasCard').classList.remove('hidden');
                document.getElementById('navPageCountBadge').innerText = `${totalPdfPages} Pages Active`;
                document.getElementById('pdfTotalPagesText').innerText = `of ${totalPdfPages}`;

                // Populate page dropdown
                const select = document.getElementById('pdfPageSelect');
                select.innerHTML = '';
                for (let i = 1; i <= totalPdfPages; i++) {
                    const opt = document.createElement('option');
                    opt.value = i;
                    opt.innerText = `Page ${i}`;
                    select.appendChild(opt);
                }

                // Auto-fit to container width so all edges and sides are immediately visible
                fitCanvasToWidth();
                showToast(`PDF Loaded Successfully! (${totalPdfPages} Pages)`, 'success');
            }).catch(err => {
                console.error(err);
                alert('PDF load karne me samasya aayi: ' + err.message);
            });
        }

        // Render specific page on Canvas
        function renderPage(pageNum) {
            if (!currentPdfDoc) return;
            if (isRenderingPage) {
                renderTaskPending = pageNum;
                return;
            }
            isRenderingPage = true;

            // Clear previous crop
            resetCropBox();

            currentPdfDoc.getPage(pageNum).then(page => {
                const viewport = page.getViewport({ scale: currentScale });
                const outputScale = Math.max(2.0, window.devicePixelRatio || 2.0);

                canvas.width = Math.floor(viewport.width * outputScale);
                canvas.height = Math.floor(viewport.height * outputScale);
                canvas.style.width = Math.floor(viewport.width) + "px";
                canvas.style.height = Math.floor(viewport.height) + "px";

                const renderContext = {
                    canvasContext: ctx,
                    transform: [outputScale, 0, 0, outputScale, 0, 0],
                    viewport: viewport
                };

                const renderTask = page.render(renderContext);
                renderTask.promise.then(() => {
                    isRenderingPage = false;
                    if (renderTaskPending !== null) {
                        const nextP = renderTaskPending;
                        renderTaskPending = null;
                        renderPage(nextP);
                    }
                });

                // Extract Text content for AI context and Text Lines Drawer
                page.getTextContent().then(textContent => {
                    currentPageTextContent = textContent.items.map(item => item.str).join(' ');
                    document.getElementById('aiPageContextStatus').innerText = `Page ${pageNum} Active (${textContent.items.length} text items)`;
                    renderPageTextLines(textContent.items, pageNum);
                });

                // Update controls
                currentPdfPage = pageNum;
                document.getElementById('pdfPageSelect').value = pageNum;
                document.getElementById('btnPrevPage').disabled = (pageNum <= 1);
                document.getElementById('btnNextPage').disabled = (pageNum >= totalPdfPages);
            });
        }

        function prevPdfPage() {
            if (currentPdfPage > 1) {
                renderPage(currentPdfPage - 1);
            }
        }

        function nextPdfPage() {
            if (currentPdfPage < totalPdfPages) {
                renderPage(currentPdfPage + 1);
            }
        }

        function jumpToPdfPage(p) {
            const pageNum = parseInt(p);
            if (pageNum >= 1 && pageNum <= totalPdfPages) {
                renderPage(pageNum);
            }
        }

        function adjustZoom(delta) {
            let newScale = currentScale + delta;
            if (newScale < 0.35) newScale = 0.35;
            if (newScale > 3.5) newScale = 3.5;
            currentScale = newScale;
            document.getElementById('zoomText').innerText = `${Math.round(currentScale * 100)}%`;
            renderPage(currentPdfPage);
        }

        function fitCanvasToWidth() {
            if (!currentPdfDoc) return;
            const container = document.getElementById('canvasScrollArea');
            const availableWidth = Math.max(300, (container.clientWidth || (window.innerWidth * 0.6)) - 55);

            currentPdfDoc.getPage(currentPdfPage).then(page => {
                const unscaledViewport = page.getViewport({ scale: 1.0 });
                currentScale = Math.max(0.4, Math.min(3.0, availableWidth / unscaledViewport.width));
                document.getElementById('zoomText').innerText = `${Math.round(currentScale * 100)}%`;
                renderPage(currentPdfPage);
            });
        }

        function fitCanvasToPage() {
            if (!currentPdfDoc) return;
            const container = document.getElementById('canvasScrollArea');
            const availableWidth = Math.max(300, (container.clientWidth || (window.innerWidth * 0.6)) - 55);
            const availableHeight = Math.max(300, (container.clientHeight || (window.innerHeight * 0.78)) - 60);

            currentPdfDoc.getPage(currentPdfPage).then(page => {
                const unscaledViewport = page.getViewport({ scale: 1.0 });
                const scaleW = availableWidth / unscaledViewport.width;
                const scaleH = availableHeight / unscaledViewport.height;
                currentScale = Math.max(0.35, Math.min(scaleW, scaleH));
                document.getElementById('zoomText').innerText = `${Math.round(currentScale * 100)}%`;
                renderPage(currentPdfPage);
            });
        }

        // Toggle Split View vs Full Page Studio (Bracket Bada Karna)
        function toggleLayoutMode() {
            isFullViewMode = !isFullViewMode;
            const pdfCol = document.getElementById('pdfWorkspaceCol');
            const galleryCol = document.getElementById('galleryCol');
            const icon = document.getElementById('layoutToggleIcon');
            const text = document.getElementById('layoutToggleText');

            if (isFullViewMode) {
                pdfCol.className = "lg:col-span-12 space-y-4 transition-all duration-300";
                galleryCol.className = "lg:col-span-12 space-y-4 transition-all duration-300";
                icon.className = "fa-solid fa-compress";
                text.innerText = "Split View";
                setTimeout(fitCanvasToWidth, 50);
                showToast('Full Page View Enabled — Canvas Bracket Expanded!', 'info');
            } else {
                pdfCol.className = "lg:col-span-8 space-y-4 transition-all duration-300";
                galleryCol.className = "lg:col-span-4 space-y-4 transition-all duration-300";
                icon.className = "fa-solid fa-expand";
                text.innerText = "Full View";
                setTimeout(fitCanvasToWidth, 50);
            }
        }

        // ========================================================
        // CROP SELECTION WITH INSTANT FLOATING ACTION BUBBLE
        // ========================================================
        let cropItemCount = 1;

        wrapperEl.addEventListener('mousedown', e => {
            // Never trigger crop drag if clicked on the action bubble
            if (e.target.closest('#cropActionBubble')) return;

            const rect = canvas.getBoundingClientRect();
            startX = e.clientX - rect.left;
            startY = e.clientY - rect.top;
            isDragging = true;

            // Hide bubble while drawing
            bubbleEl.classList.add('hidden');

            boxEl.classList.remove('hidden');
            boxEl.style.left = startX + 'px';
            boxEl.style.top = startY + 'px';
            boxEl.style.width = '0px';
            boxEl.style.height = '0px';
        });

        window.addEventListener('mousemove', e => {
            if (!isDragging) return;
            const rect = canvas.getBoundingClientRect();
            currentX = Math.max(0, Math.min(e.clientX - rect.left, rect.width));
            currentY = Math.max(0, Math.min(e.clientY - rect.top, rect.height));

            const x = Math.min(startX, currentX);
            const y = Math.min(startY, currentY);
            const w = Math.abs(currentX - startX);
            const h = Math.abs(currentY - startY);

            boxEl.style.left = x + 'px';
            boxEl.style.top = y + 'px';
            boxEl.style.width = w + 'px';
            boxEl.style.height = h + 'px';

            cropCoords = { x, y, w, h };
            document.getElementById('cropCoordsText').innerText = `Selection: ${Math.round(w)} x ${Math.round(h)} px`;
        });

        window.addEventListener('mouseup', (e) => {
            if (!isDragging) return;
            isDragging = false;

            // If user drew a box of meaningful size (>= 10px by 10px)
            if (cropCoords && cropCoords.w >= 10 && cropCoords.h >= 10) {
                // Position Floating Action Bubble right under the square box
                const bubbleX = Math.max(10, Math.min(cropCoords.x, canvas.offsetWidth - 280));
                let bubbleY = cropCoords.y + cropCoords.h + 8;
                // If bubble goes beyond bottom of canvas, place it above selection
                if (bubbleY + 60 > canvas.offsetHeight) {
                    bubbleY = Math.max(10, cropCoords.y - 60);
                }

                bubbleEl.style.left = bubbleX + 'px';
                bubbleEl.style.top = bubbleY + 'px';
                bubbleEl.classList.remove('hidden');

                // Clear input so user can directly click Save (Name is 100% Optional)
                const bubbleInput = document.getElementById('bubbleTitleInput');
                if (bubbleInput) {
                    bubbleInput.value = '';
                    bubbleInput.placeholder = "(Optional Naam)";
                }

                // Highlight bottom bar as visual cue
                const bottomBar = document.getElementById('bottomCropBar');
                if (bottomBar) {
                    bottomBar.classList.add('ring-2', 'ring-emerald-400');
                    setTimeout(() => bottomBar.classList.remove('ring-2', 'ring-emerald-400'), 1000);
                }
            } else {
                resetCropBox();
            }
        });

        function syncTitles(val) {
            const bInput = document.getElementById('bubbleTitleInput');
            if (bInput) bInput.value = val;
        }

        document.getElementById('bubbleTitleInput')?.addEventListener('input', e => {
            const cInput = document.getElementById('cropProductTitle');
            if (cInput) cInput.value = e.target.value;
        });

        function resetCropBox() {
            boxEl.classList.add('hidden');
            bubbleEl.classList.add('hidden');
            cropCoords = null;
            document.getElementById('cropCoordsText').innerText = 'No region selected';
        }

        // ==========================================
        // EXTRACT & PUSH TO LIVE BAGAL GALLERY
        // ==========================================
        function extractAndPushToGallery() {
            if (!cropCoords || cropCoords.w < 10 || cropCoords.h < 10) {
                alert('Pehle mouse se PDF par product ke chaaron taraf rectangle box banayein.');
                return;
            }

            const inputTitle = (document.getElementById('bubbleTitleInput')?.value || '').trim();
            const bottomTitle = (document.getElementById('cropProductTitle')?.value || '').trim();
            
            // Name is NOT mandatory! If empty, auto-generate clean name
            const title = inputTitle || bottomTitle || `Crop ${cropItemCount++} (P${currentPdfPage})`;

            const btnMain = document.getElementById('btnExtractToGallery');
            const btnBubble = document.getElementById('btnBubbleSave');
            
            if (btnMain) btnMain.disabled = true;
            if (btnBubble) {
                btnBubble.disabled = true;
                btnBubble.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Saving...`;
            }

            try {
                // Displayed canvas size vs actual internal pixel resolution
                const rect = canvas.getBoundingClientRect();
                const scaleX = canvas.width / rect.width;
                const scaleY = canvas.height / rect.height;

                let actualX = Math.round(cropCoords.x * scaleX);
                let actualY = Math.round(cropCoords.y * scaleY);
                let actualW = Math.round(cropCoords.w * scaleX);
                let actualH = Math.round(cropCoords.h * scaleY);

                // CLAMP to guarantee drawImage never exceeds canvas bounds
                actualX = Math.max(0, Math.min(actualX, canvas.width - 1));
                actualY = Math.max(0, Math.min(actualY, canvas.height - 1));
                actualW = Math.max(1, Math.min(actualW, canvas.width - actualX));
                actualH = Math.max(1, Math.min(actualH, canvas.height - actualY));

                // Generate high-res cropped image snippet
                const cropCanvas = document.getElementById('hiddenCropCanvas');
                cropCanvas.width = actualW;
                cropCanvas.height = actualH;
                const cropCtx = cropCanvas.getContext('2d');

                cropCtx.drawImage(
                    canvas,
                    actualX, actualY, actualW, actualH,
                    0, 0, actualW, actualH
                );

                const base64Data = cropCanvas.toDataURL('image/png');

                // POST to backend API
                fetch("{{ route('seller.catalog.pdf_studio.crop') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        image_data: base64Data,
                        title: title,
                        page: currentPdfPage
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (btnMain) btnMain.disabled = false;
                    if (btnBubble) {
                        btnBubble.disabled = false;
                        btnBubble.innerHTML = `<i class="fa-solid fa-bolt"></i> ⚡ Save Photo`;
                    }

                    if (data.success) {
                        showToast(`Photo "${data.image.name}" saved to Gallery!`, 'success');
                        appendCropToRightGallery(data.image);
                        resetCropBox();
                        const cInput = document.getElementById('cropProductTitle');
                        if (cInput) cInput.value = '';
                        const bInput = document.getElementById('bubbleTitleInput');
                        if (bInput) bInput.value = '';
                    } else {
                        alert(data.message || 'Photo save karne me samasya aayi.');
                    }
                })
                .catch(err => {
                    if (btnMain) btnMain.disabled = false;
                    if (btnBubble) {
                        btnBubble.disabled = false;
                        btnBubble.innerHTML = `<i class="fa-solid fa-bolt"></i> ⚡ Save Photo`;
                    }
                    console.error('Save error:', err);
                    alert('Network error while saving cropped photo.');
                });
            } catch (err) {
                if (btnMain) btnMain.disabled = false;
                if (btnBubble) {
                    btnBubble.disabled = false;
                    btnBubble.innerHTML = `<i class="fa-solid fa-bolt"></i> ⚡ Save Photo`;
                }
                console.error('Canvas extract error:', err);
                alert('Crop capture error: ' + err.message);
            }
        }

        // Prepend new image card to Right-Side Gallery live
        function appendCropToRightGallery(img) {
            const emptyPrompt = document.getElementById('sideGalleryEmptyPrompt');
            if (emptyPrompt) emptyPrompt.remove();

            // Cache to client localStorage so crops never get lost across restarts
            try {
                let cached = JSON.parse(localStorage.getItem('vyapar_cached_crops') || '[]');
                if (!cached.find(c => c.id === img.id || c.url === img.url)) {
                    cached.unshift(img);
                    if (cached.length > 500) cached.pop();
                    localStorage.setItem('vyapar_cached_crops', JSON.stringify(cached));
                }
            } catch(e) {}

            const list = document.getElementById('sideGalleryList');
            if (document.getElementById(`card_img_${img.id}`)) return;

            const countEl = document.getElementById('rightGalleryCount');
            if (countEl) {
                countEl.innerText = parseInt(countEl.innerText || 0) + 1;
            }

            const imgFolder = img.folder || img.category || 'General';

            const card = document.createElement('div');
            card.className = "gallery-item-card flex items-center gap-3 p-2.5 rounded-2xl bg-white border border-emerald-400 shadow-md new-crop-glow transition group";
            card.id = `card_img_${img.id}`;
            card.setAttribute('data-img-url', img.url);
            card.innerHTML = `
                <div class="h-14 w-14 rounded-xl bg-white border border-gray-200 p-1 flex items-center justify-center shrink-0 overflow-hidden shadow-2xs">
                    <img src="${img.asset_url}" alt="${escapeHtml(img.name)}" class="max-h-full max-w-full object-contain">
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-xs font-bold text-gray-900 truncate" id="card_name_${img.id}" title="${escapeHtml(img.name)}">
                        ${escapeHtml(img.name)}
                    </h4>
                    <div class="flex items-center gap-1.5 text-[10px] text-gray-500 mt-0.5 flex-wrap">
                        <span class="px-1.5 py-0.5 rounded bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold" id="card_folder_${img.id}">
                            ${escapeHtml(imgFolder)}
                        </span>
                        <span>${img.size_kb} KB</span>
                        <span>•</span>
                        <span class="text-emerald-600 font-bold">New Crop</span>
                    </div>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <button type="button" onclick="openEditCropModal('${img.id}', '${(img.name || '').replace(/'/g, "\\'")}', '${imgFolder.replace(/'/g, "\\'")}', '${img.url}', '${img.asset_url}')" title="Edit Details & Folder" class="h-8 w-8 rounded-xl bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 flex items-center justify-center text-xs transition">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <a href="{{ route('seller.catalog.excel_mapper') }}?assign_img=${encodeURIComponent(img.url)}" title="Assign to Excel Rows" class="h-8 w-8 rounded-xl bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 flex items-center justify-center text-xs transition">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <button type="button" onclick="deleteSideGalleryImage('${img.url}', '${img.id}', '${(img.name || '').replace(/'/g, "\\'")}')" title="Delete" class="h-8 w-8 rounded-xl bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 flex items-center justify-center text-xs transition">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            `;
            list.prepend(card);
        }

        // Delete Image from Gallery Vault
        function deleteSideGalleryImage(url, id, name) {
            if (!confirm(`Kya aap "${name}" photo ko Media Vault se delete karna chahte hain?`)) return;

            // Remove from client cache
            try {
                let cached = JSON.parse(localStorage.getItem('vyapar_cached_crops') || '[]');
                cached = cached.filter(c => c.url !== url && c.id !== id);
                localStorage.setItem('vyapar_cached_crops', JSON.stringify(cached));
            } catch(e) {}

            fetch("{{ route('seller.catalog.gallery.delete') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json"
                },
                body: JSON.stringify({ image_url: url })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const card = document.getElementById(`card_img_${id}`);
                    if (card) {
                        card.style.opacity = '0';
                        setTimeout(() => card.remove(), 250);
                    }
                    const countEl = document.getElementById('rightGalleryCount');
                    if (countEl) {
                        countEl.innerText = Math.max(0, parseInt(countEl.innerText || 1) - 1);
                    }
                    showToast('Image deleted from vault.', 'info');
                } else {
                    alert(data.message || 'Error deleting photo.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Network error while deleting image.');
            });
        }

        // ==========================================
        // ✏️ EDIT CROP DETAILS MODAL HANDLERS
        // ==========================================
        function openEditCropModal(id, name, folder, url, assetUrl) {
            document.getElementById('editCropImgId').value = id;
            document.getElementById('editCropImgUrl').value = url;
            document.getElementById('editCropTitleInput').value = name;
            document.getElementById('editCropOriginalName').innerText = name;
            
            const preview = document.getElementById('editCropPreviewImg');
            if (preview) {
                preview.src = assetUrl || url;
            }

            const sel = document.getElementById('editCropFolderSelect');
            if (sel) {
                let found = false;
                for (let i = 0; i < sel.options.length; i++) {
                    if (sel.options[i].value.toLowerCase() === (folder || 'General').toLowerCase()) {
                        sel.selectedIndex = i;
                        found = true;
                        break;
                    }
                }
                if (!found && folder) {
                    const opt = document.createElement('option');
                    opt.value = folder;
                    opt.text = folder;
                    sel.insertBefore(opt, sel.options[sel.options.length - 1]);
                    sel.value = folder;
                }
            }

            const newFolderInput = document.getElementById('editCropNewFolderInput');
            if (newFolderInput) {
                newFolderInput.classList.add('hidden');
                newFolderInput.value = '';
            }

            document.getElementById('editCropModal').classList.remove('hidden');
        }

        function closeEditCropModal() {
            const modal = document.getElementById('editCropModal');
            if (modal) modal.classList.add('hidden');
        }

        function handleEditCropFolderChange(val) {
            const newFolderInput = document.getElementById('editCropNewFolderInput');
            if (!newFolderInput) return;
            if (val === '__NEW__') {
                newFolderInput.classList.remove('hidden');
                newFolderInput.focus();
            } else {
                newFolderInput.classList.add('hidden');
            }
        }

        function saveEditCropDetails() {
            const id = document.getElementById('editCropImgId').value;
            const url = document.getElementById('editCropImgUrl').value;
            const name = document.getElementById('editCropTitleInput').value.trim();
            const sel = document.getElementById('editCropFolderSelect');
            let folder = sel ? sel.value : 'General';

            if (folder === '__NEW__') {
                folder = (document.getElementById('editCropNewFolderInput')?.value || '').trim() || 'General';
            }

            if (!name) {
                alert('Kripya product / photo ka naam dalein.');
                return;
            }

            const btnSave = document.getElementById('btnSaveCropDetails');
            if (btnSave) {
                btnSave.disabled = true;
                btnSave.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Saving...`;
            }

            fetch("{{ route('seller.catalog.gallery.update_details') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    image_url: url,
                    name: name,
                    folder: folder
                })
            })
            .then(res => res.json())
            .then(data => {
                if (btnSave) {
                    btnSave.disabled = false;
                    btnSave.innerHTML = `<i class="fa-solid fa-floppy-disk"></i> Save Details`;
                }

                if (data.success) {
                    // Update DOM card
                    const nameEl = document.getElementById(`card_name_${id}`);
                    if (nameEl) {
                        nameEl.innerText = data.name;
                        nameEl.title = data.name;
                    }
                    const folderEl = document.getElementById(`card_folder_${id}`);
                    if (folderEl) {
                        folderEl.innerText = data.folder;
                    }

                    // Update localStorage cache
                    try {
                        let cached = JSON.parse(localStorage.getItem('vyapar_cached_crops') || '[]');
                        let found = cached.find(c => c.id === id || c.url === url);
                        if (found) {
                            found.name = data.name;
                            found.folder = data.folder;
                            found.category = data.folder;
                            localStorage.setItem('vyapar_cached_crops', JSON.stringify(cached));
                        }
                    } catch(e) {}

                    closeEditCropModal();
                    showToast('Photo details saved successfully!', 'success');
                } else {
                    alert(data.message || 'Error updating photo details.');
                }
            })
            .catch(err => {
                if (btnSave) {
                    btnSave.disabled = false;
                    btnSave.innerHTML = `<i class="fa-solid fa-floppy-disk"></i> Save Details`;
                }
                console.error(err);
                alert('Network error while updating details.');
            });
        }

        // ==========================================
        // 🤖 AI COPILOT CHAT INTEGRATION
        // ==========================================
        function askAiPrompt(promptText) {
            document.getElementById('aiChatInput').value = promptText;
            sendAiMessage();
        }

        function sendAiMessage() {
            const input = document.getElementById('aiChatInput');
            const userMsg = input.value.trim();
            if (!userMsg) return;

            input.value = '';

            // Append User Message to UI
            appendUserChatMessage(userMsg);

            const apiKey = localStorage.getItem('vyapar_ai_api_key') || localStorage.getItem('vyapar_gemini_api_key') || '';

            const btnSend = document.getElementById('btnSendAi');
            btnSend.disabled = true;

            // Show Typing indicator
            const typingBubble = appendAiTypingIndicator();

            fetch("{{ route('seller.catalog.ai_copilot') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    prompt: userMsg,
                    page: currentPdfPage,
                    page_text: currentPageTextContent || '',
                    api_key: apiKey
                })
            })
            .then(res => res.json())
            .then(data => {
                btnSend.disabled = false;
                typingBubble.remove();

                if (data.success && data.reply) {
                    appendAiChatMessage(data.reply);
                    if (data.provider) {
                        const badge = document.getElementById('aiModelBadge');
                        if (badge) badge.innerText = data.provider;
                    }
                } else {
                    const errMsg = data.message || 'Kuch takneeki samasya aayi. Kripya dobara try karein.';
                    appendAiChatMessage(`⚠️ **AI Notice**: ${errMsg}`);
                    if (errMsg.includes('API Key nahi mili') || errMsg.includes('key')) {
                        toggleApiKeyPanel(true);
                    }
                }
            })
            .catch(err => {
                btnSend.disabled = false;
                typingBubble.remove();
                console.error(err);
                appendAiChatMessage(`❌ **Network Error**: Server se connect nahi ho paya. Kripya internet ya server check karein.`);
            });
        }

        function appendUserChatMessage(text) {
            const container = document.getElementById('aiChatMessages');
            const div = document.createElement('div');
            div.className = "flex justify-end";
            div.innerHTML = `
                <div class="bg-indigo-600 text-white p-3 rounded-2xl rounded-tr-sm text-xs leading-relaxed max-w-[85%] font-medium">
                    ${escapeHtml(text)}
                </div>
            `;
            container.appendChild(div);
            container.scrollTop = container.scrollHeight;
        }

        function appendAiChatMessage(markdownText) {
            const container = document.getElementById('aiChatMessages');
            const div = document.createElement('div');
            div.className = "flex items-start gap-2.5";
            
            // Format basic markdown (bold, lists, code)
            let formatted = escapeHtml(markdownText)
                .replace(/\*\*(.*?)\*\*/g, '<b>$1</b>')
                .replace(/\*(.*?)\*/g, '<i>$1</i>')
                .replace(/`(.*?)`/g, '<code class="bg-white/70 px-1 py-0.5 rounded text-indigo-700 font-mono text-[11px]">$1</code>')
                .replace(/\n\n/g, '<br><br>')
                .replace(/\n/g, '<br>');

            div.innerHTML = `
                <div class="h-7 w-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs shrink-0 mt-0.5">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div class="bg-gray-100 text-gray-800 p-3 rounded-2xl rounded-tl-sm text-xs leading-relaxed max-w-[88%] space-y-1">
                    <div>${formatted}</div>
                </div>
            `;
            container.appendChild(div);
            container.scrollTop = container.scrollHeight;
        }

        function appendAiSystemMessage(text) {
            appendAiChatMessage(text);
        }

        function appendAiTypingIndicator() {
            const container = document.getElementById('aiChatMessages');
            const div = document.createElement('div');
            div.className = "flex items-start gap-2.5";
            div.innerHTML = `
                <div class="h-7 w-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs shrink-0 mt-0.5">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div class="bg-gray-100 text-gray-500 p-2.5 rounded-2xl rounded-tl-sm text-xs flex items-center gap-1.5">
                    <span class="animate-bounce">●</span>
                    <span class="animate-bounce delay-100">●</span>
                    <span class="animate-bounce delay-200">●</span>
                    <span class="ml-1 text-[11px] font-bold">AI analyzing page...</span>
                </div>
            `;
            container.appendChild(div);
            container.scrollTop = container.scrollHeight;
            return div;
        }

        function escapeHtml(str) {
            return (str || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toastNotification');
            const icon = document.getElementById('toastIcon');
            const msgEl = document.getElementById('toastMsg');

            msgEl.innerText = msg;
            if (type === 'success') {
                icon.className = "fa-solid fa-circle-check text-emerald-400 text-sm";
            } else {
                icon.className = "fa-solid fa-info-circle text-blue-400 text-sm";
            }

            toast.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
            setTimeout(() => {
                toast.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
            }, 3500);
        }

        // =======================================================
        // 📄 PDF TEXT & TABLE LINES EXTRACTOR FOR EXCEL MAPPER
        // =======================================================
        let currentExtractedLines = [];
        let selectedLineIndices = new Set();

        function toggleTextDrawer(show) {
            const drawer = document.getElementById('textDrawerOverlay');
            if (!drawer) return;
            if (show) {
                drawer.classList.remove('hidden');
                const pageContext = document.getElementById('textDrawerPageContext');
                if (pageContext) pageContext.innerText = `Page ${currentPdfPage}`;
                const activePageNum = document.getElementById('aiActivePageNum');
                if (activePageNum) activePageNum.innerText = currentPdfPage;
                const loadingPageNum = document.getElementById('aiLoadingPageNum');
                if (loadingPageNum) loadingPageNum.innerText = currentPdfPage;
                updateTextDrawerUI();
            } else {
                drawer.classList.add('hidden');
            }
        }

        function renderPageTextLines(items, pageNum) {
            currentExtractedLines = [];
            selectedLineIndices.clear();

            const contextEl = document.getElementById('textDrawerPageContext');
            if (contextEl) contextEl.innerText = `Page ${pageNum} • ${items ? items.length : 0} Raw Items`;

            if (!items || items.length === 0) {
                updateTextDrawerUI();
                return;
            }

            // Cluster text items by visual Y coordinate (PDF coordinates, tolerance of 4px)
            const lineBuckets = {};
            items.forEach(item => {
                const text = (item.str || '').trim();
                if (!text) return;
                const y = item.transform ? Math.round(item.transform[5] / 4) * 4 : 0;
                const x = item.transform ? item.transform[4] : 0;
                if (!lineBuckets[y]) {
                    lineBuckets[y] = [];
                }
                lineBuckets[y].push({ text, x });
            });

            // Sort lines top to bottom (Y descending in PDF coordinate space)
            const sortedY = Object.keys(lineBuckets).map(Number).sort((a, b) => b - a);
            
            sortedY.forEach(y => {
                // Sort items in this line left-to-right (X ascending)
                const rowItems = lineBuckets[y].sort((a, b) => a.x - b.x);
                const lineText = rowItems.map(i => i.text).join(' ').trim();
                if (lineText.length > 0) {
                    currentExtractedLines.push(lineText);
                }
            });

            updateTextDrawerUI();
        }

        function updateTextDrawerUI() {
            const list = document.getElementById('extractedLinesList');
            const countBadge = document.getElementById('extractedLineCountBadge');
            const selCountText = document.getElementById('selectedLineCountText');

            if (countBadge) countBadge.innerText = `${currentExtractedLines.length} Lines`;
            if (selCountText) selCountText.innerText = `${selectedLineIndices.size} Selected`;

            if (!list) return;

            if (currentExtractedLines.length === 0) {
                list.innerHTML = `<div class="p-8 text-center text-gray-400 text-xs">Is page par koi text nahi mila ya PDF scan image hai.</div>`;
                return;
            }

            let html = '';
            currentExtractedLines.forEach((line, idx) => {
                const isSelected = selectedLineIndices.has(idx);
                const bgClass = isSelected ? 'bg-violet-50 border-violet-400 shadow-2xs' : 'bg-gray-50 border-gray-200 hover:bg-white hover:border-gray-300';
                
                html += `
                    <div class="flex items-start gap-2.5 p-2.5 rounded-xl border ${bgClass} transition cursor-pointer select-none" onclick="toggleLineSelect(${idx})">
                        <input type="checkbox" ${isSelected ? 'checked' : ''} onclick="event.stopPropagation(); toggleLineSelect(${idx})" class="mt-0.5 h-4 w-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500 cursor-pointer">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="px-1.5 py-0.5 rounded bg-white text-gray-500 font-mono text-[9px] font-bold border border-gray-200">#${idx + 1}</span>
                            </div>
                            <p class="text-xs font-medium text-gray-800 break-words leading-relaxed">${escapeHtml(line)}</p>
                        </div>
                    </div>
                `;
            });
            list.innerHTML = html;
        }

        function toggleLineSelect(idx) {
            if (selectedLineIndices.has(idx)) {
                selectedLineIndices.delete(idx);
            } else {
                selectedLineIndices.add(idx);
            }
            updateTextDrawerUI();
        }

        function toggleSelectAllTextLines(select) {
            if (select) {
                currentExtractedLines.forEach((_, idx) => selectedLineIndices.add(idx));
            } else {
                selectedLineIndices.clear();
            }
            updateTextDrawerUI();
        }

        function copySelectedLinesToClipboard() {
            let linesToCopy = [];
            if (selectedLineIndices.size > 0) {
                selectedLineIndices.forEach(idx => {
                    if (currentExtractedLines[idx]) linesToCopy.push(currentExtractedLines[idx]);
                });
            } else {
                linesToCopy = currentExtractedLines;
            }

            if (linesToCopy.length === 0) {
                alert('Copy karne ke liye koi text line available nahi hai.');
                return;
            }

            navigator.clipboard.writeText(linesToCopy.join('\n')).then(() => {
                showToast(`Copied ${linesToCopy.length} lines to clipboard!`, 'success');
            }).catch(err => {
                alert('Copy error: ' + err.message);
            });
        }

        function sendSelectedLinesToExcelMapper() {
            let linesToSend = [];
            if (selectedLineIndices.size > 0) {
                currentExtractedLines.forEach((line, idx) => {
                    if (selectedLineIndices.has(idx)) {
                        linesToSend.push(line);
                    }
                });
            } else {
                linesToSend = currentExtractedLines;
            }

            if (linesToSend.length === 0) {
                alert('Excel Mapper me bhejne ke liye pehle kam se kam 1 line select karein.');
                return;
            }

            // Save to localStorage
            const payload = {
                lines: linesToSend,
                page: currentPdfPage,
                timestamp: Date.now()
            };
            localStorage.setItem('vyapar_custom_excel_lines', JSON.stringify(payload));
            
            showToast(`${linesToSend.length} lines saved! Opening Excel Multi-Row Mapper...`, 'success');

            setTimeout(() => {
                window.location.href = "{{ route('seller.catalog.excel_mapper') }}?source=pdf_lines";
            }, 400);
        }

        // ==========================================
        // 📊 CONVERT PAGE TO EXCEL TABS & AI EXTRACTOR
        // ==========================================
        let currentAiExtractedRows = [];

        function switchTextDrawerTab(tab) {
            const nativeTab = document.getElementById('nativeDrawerTabContent');
            const aiTab = document.getElementById('aiDrawerTabContent');
            const tabBtnNative = document.getElementById('tabBtnNative');
            const tabBtnAi = document.getElementById('tabBtnAi');

            if (tab === 'ai') {
                nativeTab.classList.add('hidden');
                aiTab.classList.remove('hidden');

                tabBtnAi.className = "flex-1 py-2 px-3 rounded-xl text-xs font-black transition flex items-center justify-center gap-1.5 bg-white text-violet-700 shadow-2xs border border-gray-200";
                tabBtnNative.className = "flex-1 py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 text-gray-600 hover:text-gray-900";

                // Update page badges
                const pageBadge = document.getElementById('aiActivePageNum');
                if (pageBadge) pageBadge.innerText = currentPdfPage;
                const loadingBadge = document.getElementById('aiLoadingPageNum');
                if (loadingBadge) loadingBadge.innerText = currentPdfPage;
            } else {
                aiTab.classList.add('hidden');
                nativeTab.classList.remove('hidden');

                tabBtnNative.className = "flex-1 py-2 px-3 rounded-xl text-xs font-black transition flex items-center justify-center gap-1.5 bg-white text-indigo-700 shadow-2xs border border-gray-200";
                tabBtnAi.className = "flex-1 py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 text-gray-600 hover:text-gray-900";
            }
        }

        function runAiPageTableExtraction() {
            if (!currentPageTextContent || currentPageTextContent.trim().length === 0) {
                alert(`Page ${currentPdfPage} par koi readable text nahi mila ya yeh page scanned image hai.`);
                return;
            }

            const btn = document.getElementById('btnRunAiExtract');
            const btnText = document.getElementById('btnRunAiExtractText');
            const loading = document.getElementById('aiExtractLoading');
            const resultArea = document.getElementById('aiExtractResultArea');

            btn.disabled = true;
            btnText.innerText = `AI Reading Page ${currentPdfPage}...`;
            loading.classList.remove('hidden');
            resultArea.classList.add('hidden');

            const apiKey = localStorage.getItem('vyapar_ai_api_key') || localStorage.getItem('vyapar_gemini_api_key') || '';

            fetch("{{ route('seller.catalog.pdf_studio.ai_extract_table') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    page: currentPdfPage,
                    page_text: currentPageTextContent,
                    api_key: apiKey
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btnText.innerText = `✨ AI se Is Page ka Table Excel me Nikalein`;
                loading.classList.add('hidden');

                if (data.success && Array.isArray(data.rows) && data.rows.length > 0) {
                    currentAiExtractedRows = data.rows;
                    renderAiTablePreview(data.rows, data.provider);
                    resultArea.classList.remove('hidden');
                    showToast(`Page ${currentPdfPage} se ${data.rows.length} items extract ho gaye!`, 'success');
                } else {
                    const msg = data.message || 'AI table extract nahi kar paya.';
                    alert(msg);
                }
            })
            .catch(err => {
                btn.disabled = false;
                btnText.innerText = `✨ AI se Is Page ka Table Excel me Nikalein`;
                loading.classList.add('hidden');
                console.error(err);
                alert('Network error while extracting table with AI.');
            });
        }

        function renderAiTablePreview(rows, provider) {
            const countEl = document.getElementById('aiResultRowCount');
            if (countEl) countEl.innerText = `${rows.length} Items Detected`;

            const badge = document.getElementById('aiResultProviderBadge');
            if (badge && provider) badge.innerText = provider;

            const tbody = document.getElementById('aiTablePreviewBody');
            if (!tbody) return;

            tbody.innerHTML = '';
            rows.forEach((r, idx) => {
                const tr = document.createElement('tr');
                tr.className = "hover:bg-violet-50/50";
                tr.innerHTML = `
                    <td class="p-2 font-bold text-gray-900 break-words">${escapeHtml(r.name || '-')}</td>
                    <td class="p-2 text-indigo-700 font-semibold">${escapeHtml(r.size || '-')}</td>
                    <td class="p-2 text-emerald-700 font-black font-mono">${r.mrp ? '₹' + escapeHtml(String(r.mrp)) : '-'}</td>
                    <td class="p-2 text-gray-500">${escapeHtml(r.category || '-')}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        function sendAiTableToExcelMapper() {
            if (!currentAiExtractedRows || currentAiExtractedRows.length === 0) {
                alert('Excel Mapper me bhejne ke liye koi data available nahi hai.');
                return;
            }

            // Convert structured rows into clean formatted lines for Excel Mapper dynamic hydration
            const lines = currentAiExtractedRows.map(r => {
                const parts = [r.name];
                if (r.size && r.size !== '-') parts.push(r.size);
                if (r.mrp && r.mrp !== '-') parts.push(`MRP ₹${r.mrp}`);
                if (r.sku && r.sku !== '-') parts.push(`Code: ${r.sku}`);
                return parts.join(' - ');
            });

            const payload = {
                lines: lines,
                structured_rows: currentAiExtractedRows,
                page: currentPdfPage,
                timestamp: Date.now()
            };

            localStorage.setItem('vyapar_custom_excel_lines', JSON.stringify(payload));
            showToast(`${lines.length} items saved! Opening Excel Multi-Row Mapper...`, 'success');

            setTimeout(() => {
                window.location.href = "{{ route('seller.catalog.excel_mapper') }}?source=pdf_lines";
            }, 400);
        }

        // Restore cached crops on page load
        window.addEventListener('DOMContentLoaded', () => {
            try {
                const cached = JSON.parse(localStorage.getItem('vyapar_cached_crops') || '[]');
                if (cached.length > 0) {
                    cached.forEach(img => {
                        if (!document.getElementById(`card_img_${img.id}`)) {
                            appendCropToRightGallery(img);
                        }
                    });
                }
            } catch (e) {}
        });
    </script>
</body>
</html>
