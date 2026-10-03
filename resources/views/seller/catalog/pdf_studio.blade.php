<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Side-by-Side PDF Studio & Live Extractor - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Mozilla PDF.js for Instant Browser PDF Rendering -->
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
            box-shadow: 0 0 12px rgba(79, 70, 229, 0.4);
            border-radius: 4px;
        }
        @keyframes pulseGlow {
            0% { transform: scale(0.95); opacity: 0; }
            50% { transform: scale(1.03); }
            100% { transform: scale(1); opacity: 1; }
        }
        .new-crop-glow {
            animation: pulseGlow 0.4s ease-out forwards;
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3) !important;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen pb-20">

    <!-- Hidden Input for Loading Local PDF from System -->
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
                            PDF open karein, mouse se image ko select karke crop karein — turant Bagal Gallery me save ho jayegi!
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="document.getElementById('pdfFileInput').click()" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-md shadow-indigo-600/30 flex items-center gap-2 transition active:scale-95">
                        <i class="fa-solid fa-folder-open"></i>
                        <span>Open PDF from Computer</span>
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
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
            
            <!-- LEFT COLUMN: PDF View / Canvas & Tools (7 cols) -->
            <div class="lg:col-span-7 xl:col-span-8 space-y-4">
                
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
                    <p class="text-[11px] text-gray-400 mt-3">Supports any PDF catalog up to 100+ pages</p>
                </div>

                <!-- STATE B: PDF Loaded Active Canvas Toolbar (hidden until PDF selected) -->
                <div id="pdfActiveToolbar" class="bg-white p-3.5 rounded-2xl border border-gray-200 shadow-xs flex flex-wrap items-center justify-between gap-3 hidden">
                    
                    <div class="flex items-center gap-2 flex-wrap">
                        <!-- Prev / Next Controls -->
                        <button type="button" onclick="prevPdfPage()" id="btnPrevPage" class="h-9 w-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center text-xs font-bold transition disabled:opacity-40">
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

                        <button type="button" onclick="nextPdfPage()" id="btnNextPage" class="h-9 w-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center text-xs font-bold transition disabled:opacity-40">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>

                        <!-- Zoom Controls -->
                        <div class="flex items-center bg-gray-100 p-0.5 rounded-xl ml-2">
                            <button type="button" onclick="adjustZoom(-0.25)" class="h-7 w-7 rounded-lg hover:bg-white text-gray-600 text-xs font-bold transition" title="Zoom Out">-</button>
                            <span class="text-[11px] font-black text-gray-700 px-2" id="zoomText">150%</span>
                            <button type="button" onclick="adjustZoom(0.25)" class="h-7 w-7 rounded-lg hover:bg-white text-gray-600 text-xs font-bold transition" title="Zoom In">+</button>
                        </div>
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

                <!-- STATE C: Canvas Container & Crop Area (hidden until PDF selected) -->
                <div id="pdfCanvasCard" class="bg-white rounded-3xl border border-gray-200 p-4 shadow-sm space-y-4 hidden">
                    
                    <div class="flex items-center justify-between text-xs pb-1 border-b border-gray-100">
                        <span class="font-bold text-indigo-700 flex items-center gap-1.5">
                            <i class="fa-solid fa-crosshairs animate-pulse"></i>
                            <span>Product ya Image ke chaaron taraf mouse se rectangle box drag karein:</span>
                        </span>
                        <span class="text-gray-400 font-mono text-[11px]" id="cropCoordsText">No region selected</span>
                    </div>

                    <!-- Canvas Viewport -->
                    <div class="border rounded-2xl bg-slate-900/5 overflow-auto max-h-[68vh] p-3 flex justify-center shadow-inner relative" id="canvasScrollArea">
                        <div class="crop-canvas-container" id="cropCanvasWrapper">
                            <canvas id="pdfPageCanvas" class="block shadow-md bg-white rounded-lg"></canvas>
                            <div class="crop-selection-box hidden" id="cropSelectionBox"></div>
                        </div>
                    </div>

                    <!-- Bottom Action Bar: Name & Extract to Bagal Gallery -->
                    <div class="bg-slate-50 p-4 rounded-2xl border border-gray-200 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2 flex-1 min-w-[240px]">
                            <label class="text-xs font-bold text-gray-700 whitespace-nowrap">Product Name:</label>
                            <input type="text" id="cropProductTitle" placeholder="e.g. CPVC 90° Elbow, UPVC Equal Tee, Ball Valve..." class="w-full px-3 py-2 text-xs rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 bg-white font-medium">
                        </div>

                        <button type="button" id="btnExtractToGallery" onclick="extractAndPushToGallery()" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-md shadow-indigo-600/30 flex items-center gap-2 transition active:scale-95">
                            <i class="fa-solid fa-bolt"></i>
                            <span>Extract & Send to Bagal Gallery</span>
                        </button>
                    </div>

                </div>

            </div>

            <!-- RIGHT COLUMN: Real-Time Live Gallery Vault (4-5 cols) -->
            <div class="lg:col-span-5 xl:col-span-4 space-y-4">
                
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
                    <div class="space-y-3 max-h-[72vh] overflow-y-auto pr-1" id="sideGalleryList">
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
                                    Left side me PDF select karke kisi bhi product ko crop karein, wo turant yahan save ho jayega.
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

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // PDF.js worker setup
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let currentPdfDoc = null;
        let currentPdfPage = 1;
        let totalPdfPages = 0;
        let currentScale = 1.5; // High definition crisp render
        let isRenderingPage = false;
        let renderTaskPending = null;

        // Crop Selection state
        let isDragging = false;
        let startX = 0, startY = 0, currentX = 0, currentY = 0;
        let cropCoords = null; // { x, y, w, h } relative to displayed canvas

        const canvas = document.getElementById('pdfPageCanvas');
        const ctx = canvas.getContext('2d');
        const boxEl = document.getElementById('cropSelectionBox');
        const wrapperEl = document.getElementById('cropCanvasWrapper');

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

        // ==========================================
        // CROP SELECTION (MOUSE DRAG ON CANVAS)
        // ==========================================
        wrapperEl.addEventListener('mousedown', e => {
            const rect = canvas.getBoundingClientRect();
            startX = e.clientX - rect.left;
            startY = e.clientY - rect.top;
            isDragging = true;
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
            isDragging = false;
        });

        function resetCropBox() {
            boxEl.classList.add('hidden');
            cropCoords = null;
            document.getElementById('cropCoordsText').innerText = 'No region selected';
        }

        // ==========================================
        // EXTRACT & PUSH TO LIVE BAGAL GALLERY
        // ==========================================
        function extractAndPushToGallery() {
            if (!cropCoords || cropCoords.w < 20 || cropCoords.h < 20) {
                alert('Pehle cursor se PDF page par product ke aas-paas box drag karke select karein.');
                return;
            }

            const title = document.getElementById('cropProductTitle').value.trim() || `Page ${currentPdfPage} Crop`;
            const btn = document.getElementById('btnExtractToGallery');
            btn.disabled = true;
            btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Saving...`;

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
                btn.disabled = false;
                btn.innerHTML = `<i class="fa-solid fa-bolt"></i> Extract & Send to Bagal Gallery`;

                if (data.success) {
                    showToast(`Photo "${data.image.name}" saved to Media Vault!`, 'success');
                    appendCropToRightGallery(data.image);
                    resetCropBox();
                    document.getElementById('cropProductTitle').value = '';
                } else {
                    alert(data.message || 'Error saving cropped photo.');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = `<i class="fa-solid fa-bolt"></i> Extract & Send to Bagal Gallery`;
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
            card.className = "gallery-item-card flex items-center gap-3 p-2.5 rounded-2xl bg-white border border-emerald-300 shadow-sm new-crop-glow transition group";
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
