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
                            <button type="button" onclick="adjustZoom(-0.25)" class="h-7 w-7 rounded-lg hover:bg-white text-gray-600 text-xs font-bold transition" title="Zoom Out">-</button>
                            <span class="text-[11px] font-black text-gray-700 px-2" id="zoomText">150%</span>
                            <button type="button" onclick="adjustZoom(0.25)" class="h-7 w-7 rounded-lg hover:bg-white text-gray-600 text-xs font-bold transition" title="Zoom In">+</button>
                        </div>

                        <!-- Auto-Fit Width Button -->
                        <button type="button" onclick="fitCanvasToWidth()" class="px-2.5 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition flex items-center gap-1" title="Fit to Container Width">
                            <i class="fa-solid fa-arrows-left-right"></i>
                            <span>Fit Width</span>
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
                            <span>Product ke chaaron taraf box drag karein — box chhodte hi <b>"Save Photo"</b> popup aayega:</span>
                        </span>
                        <span class="text-gray-400 font-mono text-[11px]" id="cropCoordsText">No region selected</span>
                    </div>

                    <!-- Big Canvas Viewport with 4-Way Scroll & Generous Height -->
                    <div class="border rounded-2xl bg-slate-900/5 overflow-auto max-h-[82vh] p-4 flex justify-center shadow-inner relative" id="canvasScrollArea">
                        
                        <div class="crop-canvas-container" id="cropCanvasWrapper">
                            <canvas id="pdfPageCanvas" class="block shadow-md bg-white rounded-lg"></canvas>
                            
                            <!-- Visual Selection Box on Canvas -->
                            <div class="crop-selection-box hidden" id="cropSelectionBox"></div>

                            <!-- ⚡ Floating Instant Action Bubble (Appears right on square when mouse released) -->
                            <div id="cropActionBubble" class="absolute z-30 bg-gray-900/95 text-white p-2 rounded-2xl shadow-2xl border border-white/20 flex items-center gap-2 backdrop-blur-md hidden transition-all">
                                <input type="text" id="bubbleTitleInput" placeholder="Name (e.g. CPVC Elbow)" class="px-3 py-1.5 rounded-xl bg-white/10 text-white placeholder-gray-400 text-xs border border-white/20 focus:outline-none focus:ring-2 focus:ring-emerald-400 w-48 font-medium" onkeydown="if(event.key === 'Enter') extractAndPushToGallery()">
                                <button type="button" onclick="extractAndPushToGallery()" id="btnBubbleSave" class="px-4 py-1.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-gray-950 font-black text-xs flex items-center gap-1.5 transition shadow-lg shadow-emerald-500/30 whitespace-nowrap active:scale-95">
                                    <i class="fa-solid fa-bolt"></i>
                                    <span>Save Photo</span>
                                </button>
                                <button type="button" onclick="resetCropBox()" class="h-7 w-7 rounded-lg hover:bg-white/20 text-gray-400 hover:text-white flex items-center justify-center text-xs" title="Cancel Selection">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>

                        </div>

                    </div>

                    <!-- Bottom Action Bar -->
                    <div class="bg-slate-50 p-4 rounded-2xl border border-gray-200 flex flex-wrap items-center justify-between gap-3" id="bottomCropBar">
                        <div class="flex items-center gap-2 flex-1 min-w-[240px]">
                            <label class="text-xs font-bold text-gray-700 whitespace-nowrap">Product Name:</label>
                            <input type="text" id="cropProductTitle" placeholder="e.g. CPVC 90° Elbow, UPVC Equal Tee, Ball Valve..." class="w-full px-3 py-2 text-xs rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 bg-white font-medium" oninput="syncTitles(this.value)">
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" id="btnExtractToGallery" onclick="extractAndPushToGallery()" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-md shadow-indigo-600/30 flex items-center gap-2 transition active:scale-95">
                                <i class="fa-solid fa-bolt"></i>
                                <span>Extract & Send to Bagal Gallery</span>
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
                            <p class="text-[11px] text-gray-400">PDF se crop kiye gaye sabhi photos yahan turant dikhenge</p>
                        </div>
                        <a href="{{ route('seller.catalog.gallery') }}" target="_blank" class="h-8 w-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center text-xs transition" title="Open Full Media Vault">
                            <i class="fa-solid fa-up-right-from-square"></i>
                        </a>
                    </div>

                    <!-- Side Gallery Scrollable List -->
                    <div class="space-y-3 max-h-[75vh] overflow-y-auto pr-1" id="sideGalleryList">
                        @forelse($galleryImages as $gImg)
                            <div class="gallery-item-card flex items-center gap-3 p-2.5 rounded-2xl bg-gray-50 hover:bg-indigo-50/50 border border-gray-200 transition group" id="card_img_{{ $gImg['id'] }}">
                                <div class="h-14 w-14 rounded-xl bg-white border border-gray-200 p-1 flex items-center justify-center shrink-0 overflow-hidden shadow-2xs">
                                    <img src="{{ $gImg['asset_url'] }}" alt="{{ $gImg['name'] }}" class="max-h-full max-w-full object-contain">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-xs font-bold text-gray-900 truncate" title="{{ $gImg['name'] }}">
                                        {{ $gImg['name'] }}
                                    </h4>
                                    <div class="flex items-center gap-2 text-[10px] text-gray-400 mt-0.5">
                                        <span>{{ $gImg['size_kb'] }} KB</span>
                                        <span>•</span>
                                        <span class="text-indigo-600 font-semibold">{{ $gImg['source'] === 'plasto_master' ? 'Master' : 'Crop' }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
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
        let currentScale = 1.5; // High definition render
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

                // Render first page
                renderPage(currentPdfPage);
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
                canvas.width = viewport.width;
                canvas.height = viewport.height;

                const renderContext = {
                    canvasContext: ctx,
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

                // Extract Text content for AI context
                page.getTextContent().then(textContent => {
                    currentPageTextContent = textContent.items.map(item => item.str).join(' ');
                    document.getElementById('aiPageContextStatus').innerText = `Page ${pageNum} Active (${textContent.items.length} text items)`;
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
            if (newScale < 0.75) newScale = 0.75;
            if (newScale > 3.0) newScale = 3.0;
            currentScale = newScale;
            document.getElementById('zoomText').innerText = `${Math.round(currentScale * 100)}%`;
            renderPage(currentPdfPage);
        }

        function fitCanvasToWidth() {
            if (!currentPdfDoc) return;
            const container = document.getElementById('canvasScrollArea');
            const availableWidth = container.clientWidth - 40; // minus padding

            currentPdfDoc.getPage(currentPdfPage).then(page => {
                const unscaledViewport = page.getViewport({ scale: 1.0 });
                currentScale = Math.max(0.75, Math.min(3.0, availableWidth / unscaledViewport.width));
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
                fitCanvasToWidth();
                showToast('Full Page View Enabled — Maximum workspace size!', 'info');
            } else {
                pdfCol.className = "lg:col-span-8 space-y-4 transition-all duration-300";
                galleryCol.className = "lg:col-span-4 space-y-4 transition-all duration-300";
                icon.className = "fa-solid fa-expand";
                text.innerText = "Full View";
                fitCanvasToWidth();
            }
        }

        // ========================================================
        // CROP SELECTION WITH INSTANT FLOATING ACTION BUBBLE
        // ========================================================
        wrapperEl.addEventListener('mousedown', e => {
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

        window.addEventListener('mouseup', () => {
            if (!isDragging) return;
            isDragging = false;

            // If user drew a box of meaningful size (>= 15px by 15px)
            if (cropCoords && cropCoords.w >= 15 && cropCoords.h >= 15) {
                // Position Floating Action Bubble right under the square box
                const bubbleX = Math.max(10, Math.min(cropCoords.x, canvas.width - 280));
                const bubbleY = cropCoords.y + cropCoords.h + 8;

                bubbleEl.style.left = bubbleX + 'px';
                bubbleEl.style.top = bubbleY + 'px';
                bubbleEl.classList.remove('hidden');

                // Auto-focus input for rapid naming
                setTimeout(() => {
                    const bubbleInput = document.getElementById('bubbleTitleInput');
                    bubbleInput.focus();
                    if (!bubbleInput.value) {
                        bubbleInput.value = `Page ${currentPdfPage} Item`;
                        bubbleInput.select();
                    }
                }, 50);

                // Highlight bottom bar as well
                document.getElementById('bottomCropBar').classList.add('ring-2', 'ring-indigo-400');
                setTimeout(() => {
                    document.getElementById('bottomCropBar').classList.remove('ring-2', 'ring-indigo-400');
                }, 1000);
            } else {
                resetCropBox();
            }
        });

        function syncTitles(val) {
            document.getElementById('bubbleTitleInput').value = val;
        }

        document.getElementById('bubbleTitleInput').addEventListener('input', e => {
            document.getElementById('cropProductTitle').value = e.target.value;
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
            if (!cropCoords || cropCoords.w < 15 || cropCoords.h < 15) {
                alert('Pehle cursor se PDF page par product ke aas-paas box drag karke select karein.');
                return;
            }

            const title = document.getElementById('bubbleTitleInput').value.trim() 
                       || document.getElementById('cropProductTitle').value.trim() 
                       || `Page ${currentPdfPage} Crop`;

            const btnMain = document.getElementById('btnExtractToGallery');
            const btnBubble = document.getElementById('btnBubbleSave');
            
            btnMain.disabled = true;
            btnBubble.disabled = true;
            btnBubble.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Saving...`;

            // Displayed canvas size vs actual internal pixel resolution
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;

            const actualX = cropCoords.x * scaleX;
            const actualY = cropCoords.y * scaleY;
            const actualW = cropCoords.w * scaleX;
            const actualH = cropCoords.h * scaleY;

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

            const base64Data = cropCanvas.toDataURL('image/jpeg', 0.95);

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
                btnMain.disabled = false;
                btnBubble.disabled = false;
                btnBubble.innerHTML = `<i class="fa-solid fa-bolt"></i> Save Photo`;

                if (data.success) {
                    showToast(`Photo "${data.image.name}" saved to Media Vault!`, 'success');
                    appendCropToRightGallery(data.image);
                    resetCropBox();
                    document.getElementById('cropProductTitle').value = '';
                    document.getElementById('bubbleTitleInput').value = '';
                } else {
                    alert(data.message || 'Error saving cropped photo.');
                }
            })
            .catch(err => {
                btnMain.disabled = false;
                btnBubble.disabled = false;
                btnBubble.innerHTML = `<i class="fa-solid fa-bolt"></i> Save Photo`;
                console.error(err);
                alert('Network error while saving cropped photo.');
            });
        }

        // Prepend new image card to Right-Side Gallery live
        function appendCropToRightGallery(img) {
            const emptyPrompt = document.getElementById('sideGalleryEmptyPrompt');
            if (emptyPrompt) emptyPrompt.remove();

            const list = document.getElementById('sideGalleryList');
            const countEl = document.getElementById('rightGalleryCount');
            if (countEl) {
                countEl.innerText = parseInt(countEl.innerText || 0) + 1;
            }

            const card = document.createElement('div');
            card.className = "gallery-item-card flex items-center gap-3 p-2.5 rounded-2xl bg-white border border-emerald-400 shadow-md new-crop-glow transition group";
            card.id = `card_img_${img.id}`;
            card.innerHTML = `
                <div class="h-14 w-14 rounded-xl bg-white border border-gray-200 p-1 flex items-center justify-center shrink-0 overflow-hidden shadow-2xs">
                    <img src="${img.asset_url}" alt="${img.name}" class="max-h-full max-w-full object-contain">
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-xs font-bold text-gray-900 truncate" title="${img.name}">
                        ${img.name}
                    </h4>
                    <div class="flex items-center gap-2 text-[10px] text-gray-400 mt-0.5">
                        <span>${img.size_kb} KB</span>
                        <span>•</span>
                        <span class="text-emerald-600 font-bold">New Crop</span>
                    </div>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <a href="{{ route('seller.catalog.excel_mapper') }}?assign_img=${encodeURIComponent(img.url)}" title="Assign to Excel Rows" class="h-8 w-8 rounded-xl bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 flex items-center justify-center text-xs transition">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <button type="button" onclick="deleteSideGalleryImage('${img.url}', '${img.id}', '${img.name.replace(/'/g, "\\'")}')" title="Delete" class="h-8 w-8 rounded-xl bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 flex items-center justify-center text-xs transition">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            `;
            list.prepend(card);
        }

        // Delete Image from Gallery Vault
        function deleteSideGalleryImage(url, id, name) {
            if (!confirm(`Kya aap "${name}" photo ko Media Vault se delete karna chahte hain?`)) return;

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
    </script>
</body>
</html>
