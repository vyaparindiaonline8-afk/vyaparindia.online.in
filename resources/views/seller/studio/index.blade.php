<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Studio & Bulk Listing Canvas - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen pb-28">

    <!-- Top Sticky Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.dashboard') }}" class="h-9 w-9 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-base shadow-sm">
                        V
                    </a>
                    <div>
                        <span class="font-black text-gray-900 text-base tracking-tight">VyaparIndia</span>
                        <span class="ml-2 text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">Photo Studio</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('seller.products.index') }}" class="text-xs font-bold text-gray-600 hover:text-blue-600 transition">
                        Manage Catalog
                    </a>
                    <a href="{{ route('seller.dashboard') }}" class="text-xs font-bold text-gray-600 hover:text-blue-600 transition">
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs text-gray-500 font-medium">
            <a href="{{ route('seller.dashboard') }}" class="hover:text-blue-600">Seller Hub</a>
            <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
            <span class="text-gray-900 font-bold">Bulk Photo Studio & Quick Listing Canvas</span>
        </nav>

        <!-- Hero Card -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-xs font-bold backdrop-blur-xs">
                    <i class="fa-solid fa-camera-retro text-amber-400"></i>
                    <span>Batch Photo Studio</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                    Ek Sath 20-30 Photos Upload Karein & 1-Click Me List Karein
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                    Apne mobile se saare products ki photos ek sath drop karein. Gallery se 1 photo (ya 2-3 angles) select karke details daalein, ready-made ya custom category chunein, aur turant live store par publish karein.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <label for="bulk_photo_input" class="cursor-pointer px-6 py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white font-extrabold text-xs shadow-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up text-base"></i>
                    <span>Upload 20-30 Photos</span>
                </label>
                <input type="file" id="bulk_photo_input" multiple accept="image/*" class="hidden" onchange="handleBulkUpload(this)">
            </div>
        </div>

        <!-- Metrics Overview -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Uploaded</span>
                <div class="text-2xl font-black text-gray-900 mt-1" id="stat_total">{{ $totalUploaded }}</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Ready to List</span>
                <div class="text-2xl font-black text-blue-600 mt-1" id="stat_unassigned">{{ $unassignedCount }}</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Multi-Angle Support</span>
                <div class="text-xs font-bold text-emerald-600 mt-2 flex items-center gap-1">
                    <i class="fa-solid fa-check"></i> 2-3 Photos Per Item
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Custom Category</span>
                <div class="text-xs font-bold text-indigo-600 mt-2 flex items-center gap-1">
                    <i class="fa-solid fa-bolt"></i> Auto-Saved to DB
                </div>
            </div>
        </div>

        <!-- Visual Media Gallery Grid -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-gray-900">Your Photo Gallery</h3>
                    <p class="text-xs text-gray-500">Click photo cards to select 1 or multiple photos for a single product.</p>
                </div>
                <div class="text-xs font-bold text-gray-500">
                    Selected: <span id="selected_counter" class="text-indigo-600 font-black">0</span> Photos
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-4" id="gallery_grid">
                @forelse($mediaItems as $media)
                    <div class="media-card bg-white rounded-2xl border-2 transition-all cursor-pointer relative overflow-hidden group aspect-square {{ $media->is_assigned ? 'border-emerald-300 opacity-60' : 'border-gray-200 hover:border-indigo-400 shadow-xs' }}" 
                         id="media_card_{{ $media->id }}" 
                         data-id="{{ $media->id }}"
                         data-url="{{ asset($media->file_path) }}"
                         data-assigned="{{ $media->is_assigned ? '1' : '0' }}"
                         onclick="toggleSelectMedia({{ $media->id }})">
                        
                        <img src="{{ asset($media->file_path) }}" alt="{{ $media->filename }}" class="w-full h-full object-cover">

                        <!-- Checkbox Overlay -->
                        <div class="absolute top-2 left-2 z-10">
                            <input type="checkbox" id="chk_{{ $media->id }}" class="media-chk h-4 w-4 rounded text-indigo-600 pointer-events-none" {{ $media->is_assigned ? 'disabled' : '' }}>
                        </div>

                        <!-- Status Badge -->
                        <div class="absolute bottom-2 left-2 right-2 z-10 flex justify-between items-center pointer-events-none">
                            @if($media->is_assigned)
                                <span class="badge-status text-[10px] font-bold bg-emerald-600/90 text-white px-2 py-0.5 rounded-md backdrop-blur-xs">
                                    ✓ Listed
                                </span>
                            @else
                                <span class="badge-status text-[10px] font-bold bg-slate-900/80 text-slate-200 px-2 py-0.5 rounded-md backdrop-blur-xs">
                                    Ready
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-gray-400 bg-white rounded-3xl border border-gray-200 space-y-3" id="empty_gallery_msg">
                        <i class="fa-solid fa-images text-4xl text-gray-300"></i>
                        <h4 class="font-bold text-gray-700">Studio Gallery is Empty</h4>
                        <p class="text-xs text-gray-400 max-w-sm mx-auto">Click "Upload 20-30 Photos" above to drop your product photos here and start quick-listing.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Floating Bottom Action Bar (Appears when >= 1 photos selected) -->
    <div id="studio_bottom_bar" class="fixed bottom-0 left-0 right-0 z-40 bg-slate-900/95 backdrop-blur-md text-white py-3.5 px-6 shadow-2xl border-t border-slate-800 hidden transform transition-all duration-300">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-xl bg-indigo-600 flex items-center justify-center font-black text-sm">
                    <span id="bottom_selected_count">0</span>
                </div>
                <div>
                    <div class="text-xs font-bold text-white">Photos Selected for this Product</div>
                    <div class="text-[11px] text-slate-400">1 main cover + optional secondary angles</div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" onclick="clearAllSelections()" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition">
                    Clear
                </button>
                <button type="button" onclick="openPublishDrawer()" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs shadow-lg shadow-indigo-600/30 flex items-center gap-2 transition">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>Fill Details & Publish</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Publishing Side Drawer Modal -->
    <div id="publish_drawer" class="fixed inset-0 z-50 overflow-hidden hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div onclick="closePublishDrawer()" class="absolute inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div class="w-screen max-w-lg bg-white shadow-2xl flex flex-col">
                
                <!-- Drawer Header -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black text-gray-900">List Selected Product</h2>
                        <p class="text-xs text-gray-500">Selected photos will be linked to this product</p>
                    </div>
                    <button onclick="closePublishDrawer()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Drawer Form Body -->
                <form id="publishForm" onsubmit="submitProductPublish(event)" class="flex-1 overflow-y-auto p-6 space-y-5">
                    
                    <!-- Selected Photos Thumbnails -->
                    <div>
                        <label class="text-xs font-bold text-gray-700 uppercase tracking-wider block mb-2">Selected Product Photos</label>
                        <div class="flex items-center gap-2 overflow-x-auto pb-2" id="drawer_photo_thumbnails">
                            <!-- Populated by JS -->
                        </div>
                    </div>

                    <!-- Product Name & AI Assist -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-bold text-gray-700">Product Title <span class="text-rose-500">*</span></label>
                            <button type="button" onclick="triggerAiAssist()" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                                <i class="fa-solid fa-wand-magic-sparkles"></i> AI Suggest
                            </button>
                        </div>
                        <input type="text" id="prod_name" required placeholder="e.g. CPVC Brass Elbow 1 inch" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-900 focus:border-indigo-500">
                    </div>

                    <!-- Category Selection (Tick 3-4 categories OR Type Custom) -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-700 block">
                            Select Categories (Tick 1 or multiple):
                        </label>
                        <div class="flex flex-wrap gap-1.5 max-h-32 overflow-y-auto p-2 bg-gray-50 rounded-xl border border-gray-200">
                            @foreach($categories as $cat)
                                <label class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white border border-gray-200 text-xs font-medium text-gray-700 cursor-pointer hover:bg-indigo-50/50 has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-500 has-[:checked]:text-indigo-900 has-[:checked]:font-bold transition">
                                    <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}" class="cat-checkbox h-3.5 w-3.5 rounded text-indigo-600">
                                    <span>{{ $cat->name }}</span>
                                </label>
                            @endforeach
                        </div>

                        <!-- Type Own Custom Category -->
                        <div class="pt-1">
                            <label class="text-[11px] font-bold text-indigo-950 block mb-1">
                                Ya Apni Khud Ki Nayi Category Likhein:
                            </label>
                            <div class="relative">
                                <input type="text" id="new_custom_category" placeholder="e.g. Sanitary Brassware (Auto-saved to DB)" class="w-full px-3 py-2 rounded-xl border border-indigo-200 bg-indigo-50/30 text-xs font-semibold text-indigo-950 focus:border-indigo-500">
                                <span class="absolute right-3 top-2 text-[10px] font-bold text-indigo-600 bg-indigo-100 px-2 py-0.5 rounded-md">New</span>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-0.5">Yeh category database me automatically save ho jayegi aur sabhi ke search me aayegi.</p>
                        </div>
                    </div>

                    <!-- Pricing & Stock Grid -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-gray-700 block mb-1">Selling Price (₹) <span class="text-rose-500">*</span></label>
                            <input type="number" step="0.5" id="prod_price" required placeholder="e.g. 199" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold text-gray-900 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-700 block mb-1">MRP Strike-through (₹)</label>
                            <input type="number" step="0.5" id="prod_mrp" placeholder="e.g. 299" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs text-gray-700 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-700 block mb-1">Stock Units (Optional)</label>
                            <input type="number" min="0" id="prod_stock" placeholder="Empty = On Demand" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-medium focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-700 block mb-1">HSN Code</label>
                            <input type="text" id="prod_hsn" placeholder="e.g. 39174000" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-mono font-bold focus:border-indigo-500">
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="text-xs font-bold text-gray-700 block mb-1">Description & Highlights</label>
                        <textarea id="prod_desc" rows="3" placeholder="Features, specifications..." class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs text-gray-800 leading-relaxed"></textarea>
                    </div>

                    <!-- Publish Button -->
                    <div class="pt-3 border-t border-gray-100">
                        <button type="submit" id="drawer_submit_btn" class="w-full py-3.5 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2 transition">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span id="drawer_btn_text">Publish Product Now</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let selectedMediaIds = [];

        function toggleSelectMedia(mediaId) {
            const card = document.getElementById('media_card_' + mediaId);
            const chk = document.getElementById('chk_' + mediaId);
            if (!card || card.dataset.assigned === '1') return;

            const idx = selectedMediaIds.indexOf(mediaId);
            if (idx === -1) {
                selectedMediaIds.push(mediaId);
                card.classList.add('border-indigo-600', 'ring-2', 'ring-indigo-500');
                if (chk) chk.checked = true;
            } else {
                selectedMediaIds.splice(idx, 1);
                card.classList.remove('border-indigo-600', 'ring-2', 'ring-indigo-500');
                if (chk) chk.checked = false;
            }

            updateSelectionUI();
        }

        function updateSelectionUI() {
            const count = selectedMediaIds.length;
            document.getElementById('selected_counter').innerText = count;
            document.getElementById('bottom_selected_count').innerText = count;

            const bottomBar = document.getElementById('studio_bottom_bar');
            if (count > 0) {
                bottomBar.classList.remove('hidden');
            } else {
                bottomBar.classList.add('hidden');
            }
        }

        function clearAllSelections() {
            selectedMediaIds.forEach(id => {
                const card = document.getElementById('media_card_' + id);
                const chk = document.getElementById('chk_' + id);
                if (card) card.classList.remove('border-indigo-600', 'ring-2', 'ring-indigo-500');
                if (chk) chk.checked = false;
            });
            selectedMediaIds = [];
            updateSelectionUI();
        }

        function openPublishDrawer() {
            if (selectedMediaIds.length === 0) return;

            const thumbsContainer = document.getElementById('drawer_photo_thumbnails');
            thumbsContainer.innerHTML = '';

            selectedMediaIds.forEach((id, index) => {
                const card = document.getElementById('media_card_' + id);
                const url = card ? card.dataset.url : '';
                thumbsContainer.innerHTML += `
                    <div class="h-16 w-16 rounded-xl overflow-hidden border-2 ${index === 0 ? 'border-emerald-500' : 'border-gray-200'} shrink-0 relative">
                        <img src="${url}" class="h-full w-full object-cover">
                        ${index === 0 ? '<span class="absolute bottom-0 inset-x-0 bg-emerald-600 text-white text-[8px] font-bold text-center">Cover</span>' : ''}
                    </div>
                `;
            });

            document.getElementById('publish_drawer').classList.remove('hidden');
        }

        function closePublishDrawer() {
            document.getElementById('publish_drawer').classList.add('hidden');
        }

        function handleBulkUpload(input) {
            if (!input.files || input.files.length === 0) return;

            const formData = new FormData();
            for (let i = 0; i < input.files.length; i++) {
                formData.append('photos[]', input.files[i]);
            }

            alert(`Uploading ${input.files.length} photos to Studio Gallery...`);

            fetch("{{ route('seller.studio.upload') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json"
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Upload failed: ' + (data.message || 'Error'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Upload request failed.');
            });
        }

        function triggerAiAssist() {
            const name = document.getElementById('prod_name').value.trim() || 'Hardware item';
            fetch("{{ route('seller.studio.aiAssist') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json"
                },
                body: JSON.stringify({ prompt: name })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('prod_name').value = data.title;
                    document.getElementById('prod_desc').value = data.description;
                    document.getElementById('prod_hsn').value = data.hsn_code;
                    if (!document.getElementById('prod_price').value) {
                        document.getElementById('prod_price').value = data.suggested_price;
                        document.getElementById('prod_mrp').value = data.suggested_mrp;
                    }
                }
            });
        }

        function submitProductPublish(e) {
            e.preventDefault();
            if (selectedMediaIds.length === 0) return;

            const submitBtn = document.getElementById('drawer_submit_btn');
            const btnText = document.getElementById('drawer_btn_text');
            submitBtn.disabled = true;
            btnText.innerText = "Publishing...";

            const catCheckboxes = document.querySelectorAll('.cat-checkbox:checked');
            const catIds = Array.from(catCheckboxes).map(c => c.value);

            const payload = {
                media_ids: selectedMediaIds,
                name: document.getElementById('prod_name').value.trim(),
                description: document.getElementById('prod_desc').value.trim(),
                price: document.getElementById('prod_price').value,
                mrp: document.getElementById('prod_mrp').value,
                stock_quantity: document.getElementById('prod_stock').value,
                category_ids: catIds,
                new_category: document.getElementById('new_custom_category').value.trim(),
                hsn_code: document.getElementById('prod_hsn').value.trim()
            };

            fetch("{{ route('seller.studio.publish') }}", {
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
                    alert(data.message);

                    // Mark assigned media cards as green & disabled
                    data.assigned_media_ids.forEach(id => {
                        const card = document.getElementById('media_card_' + id);
                        if (card) {
                            card.dataset.assigned = '1';
                            card.classList.remove('border-indigo-600', 'ring-2', 'ring-indigo-500');
                            card.classList.add('border-emerald-300', 'opacity-60');
                            const chk = document.getElementById('chk_' + id);
                            if (chk) {
                                chk.checked = false;
                                chk.disabled = true;
                            }
                            const badge = card.querySelector('.badge-status');
                            if (badge) {
                                badge.className = 'badge-status text-[10px] font-bold bg-emerald-600/90 text-white px-2 py-0.5 rounded-md';
                                badge.innerText = '✓ Listed';
                            }
                        }
                    });

                    // Clear selections and close drawer
                    selectedMediaIds = [];
                    updateSelectionUI();
                    closePublishDrawer();

                    // Reset form
                    document.getElementById('publishForm').reset();
                } else {
                    alert(data.message || 'Publishing failed.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Publish request failed.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                btnText.innerText = "Publish Product Now";
            });
        }
    </script>
</body>
</html>
