<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Side-by-Side PDF Studio & Live Extractor - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .crop-canvas-container {
            cursor: crosshair;
            position: relative;
            user-select: none;
        }
        .crop-selection-box {
            position: absolute;
            border: 2px dashed #2563eb;
            background: rgba(37, 99, 235, 0.22);
            pointer-events: none;
            box-shadow: 0 0 10px rgba(37, 99, 235, 0.4);
        }
        @keyframes pulseGlow {
            0% { transform: scale(0.95); opacity: 0; }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); opacity: 1; }
        }
        .new-crop-glow {
            animation: pulseGlow 0.4s ease-out forwards;
            border-color: #10b981 !important;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen">

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
                            <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-200">
                                Side-by-Side Live Workspace
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-500">
                            Drag to crop any image or edit page text on the Left — it sends instantly to the Right Gallery!
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('seller.catalog.gallery') }}" class="px-3.5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition flex items-center gap-1.5">
                        <i class="fa-solid fa-images text-blue-600"></i>
                        <span>Media Vault</span>
                    </a>
                    <a href="{{ route('seller.catalog.excel_mapper') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-600/20 flex items-center gap-2 transition">
                        <i class="fa-solid fa-table-cells"></i>
                        <span>Go to Excel Mapper</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
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
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-indigo-100 text-indigo-700 font-bold">24 Pages Active</span>
                </a>
                <a href="{{ route('seller.catalog.excel_mapper') }}" class="pb-2 text-xs font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-900 flex items-center gap-2 whitespace-nowrap transition">
                    <i class="fa-solid fa-table-cells"></i>
                    <span>3. Excel Multi-Row Mapper</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Side-by-Side Workspace Layout -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
            
            <!-- LEFT COLUMN: Interactive PDF Canvas & Text Editor (7 cols) -->
            <div class="lg:col-span-7 xl:col-span-8 space-y-4">
                
                <!-- Page Selector & View Modes Toolbar -->
                <div class="bg-white p-3.5 rounded-2xl border border-gray-200 shadow-xs flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="prevPage()" class="h-9 w-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center text-xs font-bold transition">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-bold text-gray-600">Page:</span>
                            <select id="pageSelect" onchange="changePage(this.value)" class="px-3 py-1.5 rounded-xl border border-gray-300 text-xs font-extrabold text-gray-800 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                @foreach($pages as $p)
                                    <option value="{{ $p['page'] }}" {{ $p['page'] == 3 ? 'selected' : '' }}>
                                        Page {{ $p['page'] }}: {{ $pageMetadata[$p['page']]['title'] ?? 'Catalog Page' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" onclick="nextPage()" class="h-9 w-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center text-xs font-bold transition">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>

                    <!-- Canvas vs Text Tabs -->
                    <div class="flex items-center bg-gray-100 p-1 rounded-xl">
                        <button type="button" onclick="switchLeftTab('canvas')" id="btnTabCanvas" class="px-3 py-1 rounded-lg text-xs font-extrabold bg-white text-indigo-700 shadow-xs flex items-center gap-1.5 transition">
                            <i class="fa-solid fa-crop-simple"></i>
                            <span>Image Canvas</span>
                        </button>
                        <button type="button" onclick="switchLeftTab('text')" id="btnTabText" class="px-3 py-1 rounded-lg text-xs font-bold text-gray-500 hover:text-gray-800 flex items-center gap-1.5 transition">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>Editable Text</span>
                        </button>
                    </div>
                </div>

                <!-- TAB 1: INTERACTIVE CROP CANVAS -->
                <div id="leftTabContent_canvas" class="bg-white rounded-3xl border border-gray-200 p-4 shadow-sm space-y-4">
                    
                    <div class="flex items-center justify-between text-xs pb-1 border-b border-gray-100">
                        <span class="font-bold text-indigo-700 flex items-center gap-1.5">
                            <i class="fa-solid fa-mouse-pointer animate-bounce"></i>
                            <span>Cursor se kisi bhi product box ko drag karke select karein:</span>
                        </span>
                        <span class="text-gray-400 text-[11px]" id="cropCoordsText">No region selected</span>
                    </div>

                    <!-- Canvas Container -->
                    <div class="border rounded-2xl bg-gray-100 overflow-auto max-h-[68vh] p-2 flex justify-center shadow-inner">
                        <div class="crop-canvas-container inline-block" id="cropCanvasWrapper">
                            <img id="pdfPageImage" src="{{ asset('images/catalog/plasto/page_3.jpg') }}" alt="Plasto Catalog Page" class="max-w-none block select-none" crossorigin="anonymous">
                            <div class="crop-selection-box hidden" id="cropSelectionBox"></div>
                        </div>
                    </div>

                    <!-- Bottom Action Bar to Extract Directly into Right Side Gallery -->
                    <div class="bg-slate-50 p-4 rounded-2xl border border-gray-200 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2 flex-1 min-w-[240px]">
                            <label class="text-xs font-bold text-gray-700 whitespace-nowrap">Product Title:</label>
                            <input type="text" id="cropProductTitle" placeholder="e.g. UPVC 90° Elbow, Brass Tee..." class="w-full px-3 py-2 text-xs rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 bg-white font-medium">
                        </div>

                        <button type="button" id="btnExtractToGallery" onclick="extractAndPushToGallery()" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-md shadow-indigo-600/30 flex items-center gap-2 transition active:scale-95">
                            <i class="fa-solid fa-bolt"></i>
                            <span>Extract & Send to Bagal Gallery</span>
                        </button>
                    </div>

                </div>

                <!-- TAB 2: EDITABLE PAGE TEXT & OCR BLOCKS -->
                <div id="leftTabContent_text" class="bg-white rounded-3xl border border-gray-200 p-5 shadow-sm space-y-4 hidden">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h3 class="text-sm font-extrabold text-gray-900">Editable Catalog Page Text & Specifications</h3>
                            <p class="text-xs text-gray-500">PDF ke detected text aur products ko edit karein ya naye add karein.</p>
                        </div>
                        <button type="button" onclick="copyEditedText()" class="px-3.5 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition flex items-center gap-1">
                            <i class="fa-regular fa-copy"></i>
                            <span>Copy Text</span>
                        </button>
                    </div>

                    <div class="space-y-3" id="editableTextList">
                        <!-- Populated by changePage() -->
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                        <button type="button" onclick="addNewProductTextBlock()" class="px-4 py-2 rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-bold transition flex items-center gap-1.5">
                            <i class="fa-solid fa-plus"></i>
                            <span>Add New Product Block</span>
                        </button>
                        <a href="{{ route('seller.catalog.excel_mapper') }}" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold shadow-md transition flex items-center gap-1.5">
                            <i class="fa-solid fa-check"></i>
                            <span>Sync with Excel Mapper</span>
                        </a>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Real-Time Live Gallery ("Bagal Me Gallery") (5 cols) -->
            <div class="lg:col-span-5 xl:col-span-4 sticky top-24 space-y-4">
                
                <div class="bg-white rounded-3xl border border-gray-200 shadow-md p-4 space-y-3">
                    
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
                        <div class="flex items-center gap-2">
                            <div class="h-8 w-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-black">
                                <i class="fa-solid fa-images"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-extrabold text-gray-900">Side Gallery (Photo Bank)</h3>
                                <p class="text-[10px] text-gray-400">Live extracted images pool</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-indigo-100 text-indigo-700" id="sideGalleryBadge">
                            {{ count($galleryImages) }} Photos
                        </span>
                    </div>

                    <!-- Search Filter inside Side Gallery -->
                    <div class="relative">
                        <input type="text" id="sideGallerySearch" placeholder="Filter gallery photos..." oninput="filterSideGallery(this.value)" class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-gray-200 text-xs font-medium focus:outline-none focus:ring-1 focus:ring-indigo-600">
                        <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-gray-400 text-[10px]"></i>
                    </div>

                    <!-- Scrollable Gallery Items Grid -->
                    <div class="overflow-y-auto max-h-[64vh] pr-1 space-y-2.5" id="sideGalleryList">
                        @foreach($galleryImages as $gImg)
                            <div class="side-gallery-item flex items-center gap-3 p-2 bg-gray-50 hover:bg-indigo-50/60 rounded-2xl border border-gray-200 hover:border-indigo-300 transition group" id="sideItem_{{ $gImg['id'] }}" data-name="{{ strtolower($gImg['name']) }}">
                                <div class="h-14 w-14 shrink-0 bg-white rounded-xl p-1 border border-gray-200 flex items-center justify-center overflow-hidden">
                                    <img src="{{ $gImg['asset_url'] }}" alt="{{ $gImg['name'] }}" class="max-h-full max-w-full object-contain">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-xs font-bold text-gray-900 truncate" title="{{ $gImg['name'] }}">
                                        {{ $gImg['name'] }}
                                    </h4>
                                    <div class="flex items-center gap-2 text-[10px] text-gray-400">
                                        <span>{{ $gImg['size_kb'] }} KB</span>
                                        <span>•</span>
                                        <span class="text-indigo-600 font-semibold">{{ $gImg['source'] === 'plasto_master' ? 'Master' : 'Crop' }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <a href="{{ route('seller.catalog.excel_mapper') }}?assign_img={{ urlencode($gImg['url']) }}" title="Assign to Excel" class="h-7 w-7 rounded-lg bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 flex items-center justify-center text-xs transition">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                    <button type="button" onclick="deleteSideGalleryImage('{{ $gImg['url'] }}', '{{ $gImg['id'] }}', '{{ addslashes($gImg['name']) }}')" title="Delete from Vault" class="h-7 w-7 rounded-lg bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 flex items-center justify-center text-xs transition">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-2 border-t border-gray-100 text-center">
                        <a href="{{ route('seller.catalog.gallery') }}" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800">
                            View Full Media Vault & Bulk Manager &rarr;
                        </a>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- Hidden Canvas for Cropping -->
    <canvas id="hiddenCropCanvas" style="display: none;"></canvas>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Page Metadata dictionary
        const pageMeta = @json($pageMetadata);

        let currentPage = 3;
        let isDragging = false;
        let startX, startY, currentX, currentY;
        let cropCoords = null;

        const imgEl = document.getElementById('pdfPageImage');
        const boxEl = document.getElementById('cropSelectionBox');
        const wrapperEl = document.getElementById('cropCanvasWrapper');

        // Mouse Drag Selection
        wrapperEl.addEventListener('mousedown', e => {
            const rect = imgEl.getBoundingClientRect();
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
            const rect = imgEl.getBoundingClientRect();
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

        // Switch Canvas & Text View
        function switchLeftTab(tab) {
            const canvasContent = document.getElementById('leftTabContent_canvas');
            const textContent = document.getElementById('leftTabContent_text');
            const btnCanvas = document.getElementById('btnTabCanvas');
            const btnText = document.getElementById('btnTabText');

            if (tab === 'canvas') {
                canvasContent.classList.remove('hidden');
                textContent.classList.add('hidden');
                btnCanvas.className = "px-3 py-1 rounded-lg text-xs font-extrabold bg-white text-indigo-700 shadow-xs flex items-center gap-1.5 transition";
                btnText.className = "px-3 py-1 rounded-lg text-xs font-bold text-gray-500 hover:text-gray-800 flex items-center gap-1.5 transition";
            } else {
                canvasContent.classList.add('hidden');
                textContent.classList.remove('hidden');
                btnText.className = "px-3 py-1 rounded-lg text-xs font-extrabold bg-white text-indigo-700 shadow-xs flex items-center gap-1.5 transition";
                btnCanvas.className = "px-3 py-1 rounded-lg text-xs font-bold text-gray-500 hover:text-gray-800 flex items-center gap-1.5 transition";
            }
        }

        function changePage(p) {
            currentPage = parseInt(p);
            document.getElementById('pageSelect').value = currentPage;
            imgEl.src = `{{ asset('images/catalog/plasto') }}/page_${currentPage}.jpg`;
            boxEl.classList.add('hidden');
            cropCoords = null;
            document.getElementById('cropCoordsText').innerText = "No region selected";

            // Update Editable text
            renderPageText(currentPage);
        }

        function prevPage() {
            if (currentPage > 1) changePage(currentPage - 1);
        }

        function nextPage() {
            if (currentPage < 24) changePage(currentPage + 1);
        }

        function renderPageText(p) {
            const list = document.getElementById('editableTextList');
            list.innerHTML = '';
            const meta = pageMeta[p] || { title: `Page ${p}`, items: ['Standard Fitting'] };

            meta.items.forEach((item, idx) => {
                const div = document.createElement('div');
                div.className = "p-3 rounded-2xl bg-gray-50 border border-gray-200 flex flex-wrap items-center gap-3";
                div.innerHTML = `
                    <div class="h-7 w-7 rounded-lg bg-indigo-100 text-indigo-700 text-xs font-black flex items-center justify-center">${idx + 1}</div>
                    <input type="text" value="${item}" class="flex-1 px-3 py-1.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-indigo-600">
                    <input type="text" placeholder="Sizes (e.g. 15mm, 20mm, 25mm)" value="1/2, 3/4, 1, 1.25, 1.5, 2 inch" class="w-48 px-3 py-1.5 rounded-xl border border-gray-300 text-xs font-medium text-gray-600">
                    <button type="button" onclick="this.closest('div').remove()" class="text-rose-500 hover:text-rose-700 text-xs"><i class="fa-solid fa-trash-can"></i></button>
                `;
                list.appendChild(div);
            });
        }

        function addNewProductTextBlock() {
            const list = document.getElementById('editableTextList');
            const count = list.children.length + 1;
            const div = document.createElement('div');
            div.className = "p-3 rounded-2xl bg-gray-50 border border-gray-200 flex flex-wrap items-center gap-3";
            div.innerHTML = `
                <div class="h-7 w-7 rounded-lg bg-indigo-100 text-indigo-700 text-xs font-black flex items-center justify-center">${count}</div>
                <input type="text" placeholder="Product Name" class="flex-1 px-3 py-1.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-indigo-600">
                <input type="text" placeholder="Sizes (e.g. 15mm, 20mm, 25mm)" class="w-48 px-3 py-1.5 rounded-xl border border-gray-300 text-xs font-medium text-gray-600">
                <button type="button" onclick="this.closest('div').remove()" class="text-rose-500 hover:text-rose-700 text-xs"><i class="fa-solid fa-trash-can"></i></button>
            `;
            list.appendChild(div);
        }

        function copyEditedText() {
            const inputs = document.querySelectorAll('#editableTextList input[type="text"]');
            let txt = `Plasto Catalog Page ${currentPage} Data:\n`;
            inputs.forEach(i => { if (i.value) txt += i.value + "\n"; });
            navigator.clipboard.writeText(txt).then(() => alert('Page text copied to clipboard!'));
        }

        // Live Crop & Push to Bagal Gallery
        function extractAndPushToGallery() {
            if (!cropCoords || cropCoords.w < 20 || cropCoords.h < 20) {
                alert('Pehle cursor se PDF page par product ke aas-paas box drag karein.');
                return;
            }

            const title = document.getElementById('cropProductTitle').value.trim() || `Page ${currentPage} Crop`;
            const btn = document.getElementById('btnExtractToGallery');
            btn.disabled = true;
            btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Extracting...`;

            // HTML5 Canvas Native Resolution Crop
            const canvas = document.getElementById('hiddenCropCanvas');
            const ctx = canvas.getContext('2d');

            const naturalWidth = imgEl.naturalWidth;
            const displayedWidth = imgEl.width || imgEl.getBoundingClientRect().width;
            const scale = naturalWidth / displayedWidth;

            canvas.width = cropCoords.w * scale;
            canvas.height = cropCoords.h * scale;

            ctx.drawImage(
                imgEl,
                cropCoords.x * scale,
                cropCoords.y * scale,
                cropCoords.w * scale,
                cropCoords.h * scale,
                0,
                0,
                canvas.width,
                canvas.height
            );

            const base64Data = canvas.toDataURL('image/png');

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
                    page: currentPage
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = `<i class="fa-solid fa-bolt"></i> Extract & Send to Bagal Gallery`;

                if (data.success && data.image) {
                    // Prepend directly into Right Side Gallery ("Bagal me Gallery")
                    prependSideGalleryItem(data.image);
                    boxEl.classList.add('hidden');
                    cropCoords = null;
                    document.getElementById('cropCoordsText').innerText = "Cropped & Sent to Gallery!";
                    document.getElementById('cropProductTitle').value = "";
                } else {
                    alert(data.message || 'Error extracting image.');
                }
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.innerHTML = `<i class="fa-solid fa-bolt"></i> Extract & Send to Bagal Gallery`;
                alert('Network error while saving cropped photo.');
            });
        }

        function prependSideGalleryItem(img) {
            const list = document.getElementById('sideGalleryList');
            const div = document.createElement('div');
            div.className = "side-gallery-item flex items-center gap-3 p-2 bg-emerald-50 rounded-2xl border-2 border-emerald-400 new-crop-glow transition group";
            div.id = 'sideItem_' + img.id;
            div.setAttribute('data-name', img.name.toLowerCase());

            div.innerHTML = `
                <div class="h-14 w-14 shrink-0 bg-white rounded-xl p-1 border border-emerald-300 flex items-center justify-center overflow-hidden">
                    <img src="${img.asset_url}" alt="${img.name}" class="max-h-full max-w-full object-contain">
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1.5">
                        <h4 class="text-xs font-black text-gray-900 truncate" title="${img.name}">${img.name}</h4>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-emerald-600 text-white">NEW</span>
                    </div>
                    <div class="flex items-center gap-2 text-[10px] text-gray-400">
                        <span>${img.size_kb} KB</span>
                        <span>•</span>
                        <span class="text-emerald-700 font-bold">Live Crop</span>
                    </div>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <a href="{{ route('seller.catalog.excel_mapper') }}?assign_img=${encodeURIComponent(img.url)}" title="Assign to Excel" class="h-7 w-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-xs transition">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <button type="button" onclick="deleteSideGalleryImage('${img.url}', '${img.id}', '${img.name.replace(/'/g, "\\'")}')" title="Delete" class="h-7 w-7 rounded-lg bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 flex items-center justify-center text-xs transition">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            `;

            list.insertBefore(div, list.firstChild);
            
            // Update badge count
            const currentCount = document.querySelectorAll('.side-gallery-item').length;
            document.getElementById('sideGalleryBadge').innerText = `${currentCount} Photos`;
        }

        function deleteSideGalleryImage(url, id, name) {
            if (!confirm(`Are you sure you want to delete "${name}" from gallery?`)) return;

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
                    const el = document.getElementById('sideItem_' + id);
                    if (el) el.remove();
                    const currentCount = document.querySelectorAll('.side-gallery-item').length;
                    document.getElementById('sideGalleryBadge').innerText = `${currentCount} Photos`;
                } else {
                    alert(data.message || 'Error deleting photo.');
                }
            });
        }

        function filterSideGallery(q) {
            const query = q.toLowerCase().trim();
            document.querySelectorAll('.side-gallery-item').forEach(item => {
                const name = item.getAttribute('data-name');
                if (!query || name.includes(query)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Initialize page 3 text
        renderPageText(3);
    </script>
</body>
</html>
