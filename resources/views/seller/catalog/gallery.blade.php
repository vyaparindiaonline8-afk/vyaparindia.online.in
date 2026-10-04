<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Media Vault & Photo Gallery - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .image-card:hover .overlay-actions { opacity: 1; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen pb-24">

    <!-- Top Sticky Header & Suite Navigation -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.dashboard') }}" class="h-9 w-9 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 flex items-center justify-center text-sm transition">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-black text-gray-900 text-base tracking-tight">Catalog AI Studio</span>
                            <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200">
                                Media Vault
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-500">
                            Manage, preview & delete all extracted photos from PDF & Excel
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="openUploadModal()" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md shadow-blue-600/20 flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Upload Photo</span>
                    </button>
                    <a href="{{ route('seller.catalog.pdf_studio') }}" class="px-4 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-extrabold text-xs border border-indigo-200 flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-crop-simple"></i>
                        <span>Open PDF Studio</span>
                    </a>
                    <a href="{{ route('seller.catalog.excel_mapper') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-600/20 flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-table-list"></i>
                        <span>Go to Excel Mapper</span>
                    </a>
                </div>
            </div>

            <!-- Suite Navigation Tabs -->
            <div class="flex items-center gap-6 border-t border-gray-100 pt-2 pb-1 overflow-x-auto">
                <a href="{{ route('seller.catalog.gallery') }}" class="pb-2 text-xs font-extrabold border-b-2 border-blue-600 text-blue-600 flex items-center gap-2 whitespace-nowrap">
                    <i class="fa-solid fa-images"></i>
                    <span>1. Bulk Media Vault & Gallery</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-blue-100 text-blue-700 font-bold">{{ $totalImages }}</span>
                </a>
                <a href="{{ route('seller.catalog.pdf_studio') }}" class="pb-2 text-xs font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-900 flex items-center gap-2 whitespace-nowrap transition">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>2. Side-by-Side PDF Studio</span>
                </a>
                <a href="{{ route('seller.catalog.excel_mapper') }}" class="pb-2 text-xs font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-900 flex items-center gap-2 whitespace-nowrap transition">
                    <i class="fa-solid fa-table-cells"></i>
                    <span>3. Excel Multi-Row Mapper</span>
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

        <!-- Filter & Bulk Bar -->
        <div class="bg-white p-4 rounded-3xl border border-gray-200 shadow-xs flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3 flex-1 min-w-[280px]">
                <div class="relative flex-1">
                    <input type="text" id="searchInput" value="{{ $searchQuery ?? '' }}" placeholder="Search image by name (e.g. Elbow, Tee, Valve)..." 
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 font-medium"
                        oninput="filterGalleryLive(this.value)">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                </div>
                <div class="flex items-center gap-1.5 p-1 bg-gray-100 rounded-xl">
                    <button type="button" onclick="filterBySource('all')" id="btnSourceAll" class="source-tab-btn px-3 py-1 rounded-lg text-xs font-bold bg-white text-gray-900 shadow-xs">
                        All Photos
                    </button>
                    <button type="button" onclick="filterBySource('personal_vault')" id="btnSourcePersonal" class="source-tab-btn px-3 py-1 rounded-lg text-xs font-bold text-gray-600 hover:text-gray-900">
                        🔒 My Vault
                    </button>
                    <button type="button" onclick="filterBySource('universal_central')" id="btnSourceCentral" class="source-tab-btn px-3 py-1 rounded-lg text-xs font-bold text-gray-600 hover:text-gray-900">
                        🌐 Central Hub
                    </button>
                </div>
                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                    <span class="font-bold text-gray-800" id="visibleCount">{{ $totalImages }}</span> photos found
                </div>
            </div>

            <div class="flex items-center gap-2" id="bulkActionGroup">
                <button type="button" onclick="selectAllImages(true)" class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition">
                    Select All
                </button>
                <button type="button" onclick="selectAllImages(false)" class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition">
                    Clear
                </button>
                <button type="button" id="bulkDeleteBtn" onclick="executeBulkDelete()" disabled class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-xs font-extrabold flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-trash-can"></i>
                    <span>Delete Selected (<span id="selectedCount">0</span>)</span>
                </button>
            </div>
        </div>

        <!-- Images Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4" id="galleryGridContainer">
            @forelse($images as $img)
                <div class="image-card group relative bg-white rounded-2xl border border-gray-200 p-2 shadow-xs hover:shadow-lg transition-all duration-200 flex flex-col justify-between" id="card_{{ $img['id'] }}" data-name="{{ strtolower($img['name']) }}" data-url="{{ $img['url'] }}">
                    
                    <!-- Selection Checkbox -->
                    <div class="absolute top-3 left-3 z-10">
                        <input type="checkbox" value="{{ $img['url'] }}" onchange="updateSelectedCount()" class="img-checkbox h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer shadow-xs">
                    </div>

                    <!-- Image Thumbnail with Zoom Button -->
                    <div class="relative w-full h-36 bg-gray-50 rounded-xl overflow-hidden flex items-center justify-center p-2 mb-2">
                        <img src="{{ $img['asset_url'] }}" alt="{{ $img['name'] }}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-200" loading="lazy">
                        
                        <!-- Overlay Action Bar -->
                        <div class="overlay-actions opacity-0 group-hover:opacity-100 absolute inset-0 bg-black/40 backdrop-blur-[2px] rounded-xl flex items-center justify-center gap-2 transition-opacity duration-200">
                            <button type="button" onclick="openPreviewModal('{{ $img['asset_url'] }}', '{{ addslashes($img['name']) }}')" class="h-8 w-8 rounded-lg bg-white/90 hover:bg-white text-gray-800 flex items-center justify-center text-xs shadow-md transition" title="Preview Full Image">
                                <i class="fa-solid fa-expand"></i>
                            </button>
                            <a href="{{ $img['asset_url'] }}" download="{{ $img['filename'] }}" class="h-8 w-8 rounded-lg bg-white/90 hover:bg-white text-gray-800 flex items-center justify-center text-xs shadow-md transition" title="Download">
                                <i class="fa-solid fa-download"></i>
                            </a>
                            <button type="button" onclick="deleteSingleImage('{{ $img['url'] }}', '{{ $img['id'] }}', '{{ addslashes($img['name']) }}')" class="h-8 w-8 rounded-lg bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center text-xs shadow-md transition" title="Delete Image">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="space-y-1">
                        <h4 class="text-xs font-bold text-gray-900 truncate" title="{{ $img['name'] }}">
                            {{ $img['name'] }}
                        </h4>
                        <div class="flex items-center justify-between text-[10px] text-gray-400">
                            <span>{{ $img['size_kb'] }} KB</span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold {{ $img['source'] === 'plasto_master' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ $img['source'] === 'plasto_master' ? 'Master' : 'Crop' }}
                            </span>
                        </div>
                    </div>

                    <!-- Bottom Quick Action -->
                    <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between">
                        <button type="button" onclick="copyImageUrl('{{ $img['url'] }}')" class="text-[10px] text-gray-500 hover:text-blue-600 font-bold flex items-center gap-1 transition">
                            <i class="fa-regular fa-copy"></i>
                            <span>Copy Path</span>
                        </button>
                        <a href="{{ route('seller.catalog.excel_mapper') }}?assign_img={{ urlencode($img['url']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-extrabold flex items-center gap-0.5 transition">
                            <span>Assign</span>
                            <i class="fa-solid fa-arrow-right text-[8px]"></i>
                        </a>
                    </div>

                </div>
            @empty
                <div class="col-span-full py-16 text-center space-y-3">
                    <div class="h-16 w-16 mx-auto rounded-3xl bg-gray-100 flex items-center justify-center text-gray-400 text-2xl">
                        <i class="fa-solid fa-images"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-800">Media Vault is Empty</h3>
                    <p class="text-xs text-gray-500 max-w-sm mx-auto">
                        Aapne abhi tak koi photo extract ya upload nahi kiya hai. Side-by-Side PDF Studio se images crop karein ya upload karein.
                    </p>
                    <a href="{{ route('seller.catalog.pdf_studio') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 text-white text-xs font-bold shadow-md">
                        <i class="fa-solid fa-crop-simple"></i>
                        <span>Open PDF Studio to Extract</span>
                    </a>
                </div>
            @endforelse
        </div>

    </div>

    <!-- Upload Photo Modal -->
    <div id="uploadModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-sm font-extrabold text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up text-blue-600"></i>
                    <span>Upload High-Res Photo to Vault</span>
                </h3>
                <button type="button" onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('seller.catalog.gallery.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6 text-center hover:border-blue-500 bg-gray-50 transition cursor-pointer" onclick="document.getElementById('imageFileInput').click()">
                    <i class="fa-solid fa-file-arrow-up text-gray-400 text-3xl mb-2"></i>
                    <p class="text-xs font-bold text-gray-700">Choose PNG, JPG, or WEBP photo</p>
                    <p class="text-[11px] text-gray-400 mt-1">Up to 10MB per image</p>
                    <input type="file" name="image_file" id="imageFileInput" accept="image/*" required class="hidden" onchange="previewUploadFile(this)">
                </div>

                <div id="uploadPreviewBox" class="hidden p-3 bg-blue-50 rounded-xl flex items-center gap-3">
                    <img id="uploadPreviewImg" src="" class="h-12 w-12 object-contain rounded-lg bg-white border border-blue-200">
                    <span id="uploadFileName" class="text-xs font-bold text-blue-900 truncate"></span>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="closeUploadModal()" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold shadow-md transition">
                        Upload to Vault
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Full Image Preview Lightbox -->
    <div id="previewModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-xs hidden flex items-center justify-center p-4" onclick="closePreviewModal()">
        <div class="relative max-w-3xl max-h-[85vh] bg-white rounded-3xl p-4 shadow-2xl flex flex-col items-center" onclick="event.stopPropagation()">
            <div class="w-full flex items-center justify-between pb-3 border-b border-gray-100">
                <span class="text-xs font-extrabold text-gray-900 truncate" id="previewTitle"></span>
                <button type="button" onclick="closePreviewModal()" class="text-gray-400 hover:text-gray-600 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-4 flex items-center justify-center overflow-auto max-h-[70vh]">
                <img id="previewImg" src="" class="max-h-[65vh] max-w-full object-contain rounded-xl">
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function filterGalleryLive(q) {
            const query = q.toLowerCase().trim();
            const cards = document.querySelectorAll('.image-card');
            let visible = 0;
            cards.forEach(card => {
                const name = card.getAttribute('data-name');
                if (!query || name.includes(query)) {
                    card.style.display = '';
                    visible++;
                } else {
                    card.style.display = 'none';
                }
            });
            document.getElementById('visibleCount').innerText = visible;
        }

        function updateSelectedCount() {
            const checked = document.querySelectorAll('.img-checkbox:checked');
            const count = checked.length;
            document.getElementById('selectedCount').innerText = count;
            document.getElementById('bulkDeleteBtn').disabled = (count === 0);
        }

        function selectAllImages(select) {
            document.querySelectorAll('.img-checkbox').forEach(cb => {
                const parentCard = cb.closest('.image-card');
                if (parentCard.style.display !== 'none') {
                    cb.checked = select;
                }
            });
            updateSelectedCount();
        }

        function deleteSingleImage(url, id, name) {
            if (!confirm(`Are you sure you want to permanently delete "${name}"?`)) return;

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
                    const el = document.getElementById('card_' + id);
                    if (el) {
                        el.classList.add('scale-75', 'opacity-0');
                        setTimeout(() => el.remove(), 250);
                    }
                    updateSelectedCount();
                } else {
                    alert(data.message || 'Error deleting image.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Network error while deleting image.');
            });
        }

        function executeBulkDelete() {
            const checked = Array.from(document.querySelectorAll('.img-checkbox:checked')).map(cb => cb.value);
            if (checked.length === 0) return;

            if (!confirm(`Are you sure you want to permanently delete all ${checked.length} selected images?`)) return;

            fetch("{{ route('seller.catalog.gallery.bulk_delete') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json"
                },
                body: JSON.stringify({ image_urls: checked })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error deleting selected images.');
                }
            });
        }

        function copyImageUrl(url) {
            navigator.clipboard.writeText(url).then(() => {
                alert('Image path copied to clipboard: ' + url);
            });
        }

        function openPreviewModal(url, title) {
            document.getElementById('previewImg').src = url;
            document.getElementById('previewTitle').innerText = title;
            document.getElementById('previewModal').classList.remove('hidden');
        }

        function closePreviewModal() {
            document.getElementById('previewModal').classList.add('hidden');
        }

        function openUploadModal() {
            document.getElementById('uploadModal').classList.remove('hidden');
        }

        function closeUploadModal() {
            document.getElementById('uploadModal').classList.add('hidden');
        }

        function previewUploadFile(input) {
            if (input.files && input.files[0]) {
                const f = input.files[0];
                document.getElementById('uploadFileName').innerText = f.name;
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('uploadPreviewImg').src = e.target.result;
                    document.getElementById('uploadPreviewBox').classList.remove('hidden');
                };
                reader.readAsDataURL(f);
            }
        }
    </script>
</body>
</html>
