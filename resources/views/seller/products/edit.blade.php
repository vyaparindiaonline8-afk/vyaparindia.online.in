<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - {{ $product->name }} - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.dashboard') }}" class="h-9 w-9 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-base shadow-sm">
                        V
                    </a>
                    <div>
                        <span class="font-black text-gray-900 text-base tracking-tight">VyaparIndia</span>
                        <span class="ml-2 text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Edit Product</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.products.index') }}" class="text-xs font-bold text-gray-600 hover:text-gray-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Cancel & Back</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

        <div class="mb-6">
            <h1 class="text-2xl font-black text-gray-900">Edit Product: {{ $product->name }}</h1>
            <p class="text-xs text-gray-500 mt-1">Modify pricing, specifications, change or assign a newly typed category.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('seller.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-xs space-y-6">
            @csrf
            @method('PUT')

            <!-- Product Title -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Product Title / Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
            </div>

            <!-- Brand & Group Name (Optional) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Brand Name (Optional)</label>
                    <input type="text" name="brand" value="{{ old('brand', $product->brand) }}" placeholder="e.g. Astral, Supreme, Finolex" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Product Group / Sub-category</label>
                    <input type="text" name="group_name" value="{{ old('group_name', $product->group_name) }}" placeholder="e.g. Plumbing, Drainage, Electrical" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
                </div>
            </div>

            <!-- Category Section (Pick or Type New) -->
            <div class="bg-blue-50/50 border border-blue-200 rounded-2xl p-5 space-y-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-tags text-blue-600 text-sm"></i>
                    <h3 class="text-xs font-bold text-blue-950 uppercase tracking-wider">Category Assignment</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Option 1: Existing Category -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Select Existing Category</label>
                        <select name="category_id" id="category_select" class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-xs font-bold bg-white focus:border-blue-500 focus:outline-hidden">
                            <option value="">-- Choose Category --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ (old('category_id', $product->category_id) == $category->id) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Option 2: Custom Category Creation -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Or Type New Category</label>
                        <input type="text" name="new_category_name" value="{{ old('new_category_name') }}" placeholder="Type new category name to reassign..." class="w-full px-3 py-2.5 rounded-xl border border-blue-300 bg-white text-xs font-bold focus:border-blue-500 focus:outline-hidden">
                        <span class="text-[10px] text-blue-700 block mt-1">✨ If entered, this item will immediately be saved to this new category.</span>
                    </div>
                </div>
            </div>

                        <!-- Pricing, Catalog Costing & Box MRP Section -->
            <div class="bg-gradient-to-br from-amber-50/60 via-indigo-50/30 to-emerald-50/40 p-5 rounded-3xl border border-amber-200/80 space-y-4 shadow-xs">
                
                <!-- Section Header -->
                <div class="flex items-center justify-between border-b border-amber-200/60 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-amber-500 text-white flex items-center justify-center text-xs font-black shadow-xs">
                            <i class="fa-solid fa-calculator"></i>
                        </span>
                        <div>
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider">मूल्य निर्धारण व लागत हिसाब (Pricing & Costing)</h3>
                            <p class="text-[10px] text-gray-500 font-medium">कैटलॉग लिस्ट रेट, डिब्बे पर छपा वास्तविक प्रिंटेड MRP व सेलिंग प्राइस</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-blue-100 text-blue-800">
                        ⚖️ Legal Metrology Verified
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
                    <!-- 1. Catalog List MRP -->
                    <div class="bg-white p-3 rounded-2xl border border-amber-200 shadow-2xs">
                        <label class="block text-[11px] font-black text-amber-950 mb-1 flex items-center gap-1">
                            <span>📋 कैटलॉग लिस्ट रेट (₹)</span>
                        </label>
                        <input type="number" name="mrp" id="edit_prod_mrp" value="{{ old('mrp', $product->mrp) }}" step="0.01" min="0" placeholder="उदा. 340.00" oninput="recalcEditPricing()" class="w-full px-3 py-2 rounded-xl border border-amber-300 bg-amber-50/30 text-xs font-black text-amber-950 focus:border-amber-500 focus:bg-white focus:outline-hidden">
                        <span class="text-[10px] text-amber-800 block mt-1 font-semibold leading-tight">कंपनी कैटलॉग का भाव</span>
                    </div>

                    <!-- 2. Trade Discount % -->
                    <div class="bg-white p-3 rounded-2xl border border-amber-200 shadow-2xs">
                        <label class="block text-[11px] font-black text-amber-950 mb-1 flex items-center gap-1">
                            <span>🏷️ ट्रेड लेस (Less %)</span>
                        </label>
                        <input type="number" name="trade_discount_percent" id="edit_prod_discount" value="{{ old('trade_discount_percent', $product->trade_discount_percent) }}" step="0.5" min="0" max="99" placeholder="उदा. 45" oninput="recalcEditPricing()" class="w-full px-3 py-2 rounded-xl border border-amber-300 bg-amber-50/30 text-xs font-black text-amber-950 focus:border-amber-500 focus:bg-white focus:outline-hidden">
                        <span class="text-[10px] text-amber-800 block mt-1 font-semibold leading-tight">कैटलॉग पर डिस्काउंट %</span>
                    </div>

                    <!-- 3. Purchase Cost -->
                    <div class="bg-white p-3 rounded-2xl border-2 border-indigo-300 shadow-2xs">
                        <label class="block text-[11px] font-black text-indigo-950 mb-1 flex items-center gap-1">
                            <span>💰 खरीद लागत (Cost ₹)</span>
                        </label>
                        <input type="number" name="purchase_price" id="edit_prod_cost" value="{{ old('purchase_price', $product->purchase_price) }}" step="0.01" min="0" placeholder="उदा. 187.00" oninput="recalcEditPricing()" class="w-full px-3 py-2 rounded-xl border border-indigo-400 bg-indigo-50/50 text-xs font-black text-indigo-950 focus:border-indigo-600 focus:bg-white focus:outline-hidden">
                        <span class="text-[10px] text-indigo-700 block mt-1 font-semibold leading-tight">आपकी शुद्ध खरीद लागत</span>
                    </div>

                    <!-- 4. Real Printed Box MRP (OPTIONAL) -->
                    <div class="bg-white p-3 rounded-2xl border-2 border-purple-300 shadow-2xs relative">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-[11px] font-black text-purple-950 flex items-center gap-1">
                                <span>📦 डिब्बा MRP (प्रिंटेड ₹)</span>
                            </label>
                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-purple-100 text-purple-700 uppercase">वैकल्पिक</span>
                        </div>
                        <input type="number" name="box_mrp" id="edit_prod_box_mrp" value="{{ old('box_mrp', $product->box_mrp) }}" step="0.01" min="0" placeholder="उदा. 599.00" oninput="recalcEditPricing()" class="w-full px-3 py-2 rounded-xl border border-purple-400 bg-purple-50/40 text-xs font-black text-purple-950 focus:border-purple-600 focus:bg-white focus:outline-hidden">
                        <span class="text-[10px] text-purple-800 block mt-1 font-semibold leading-tight">डिब्बे पर छपा वास्तविक MRP</span>
                    </div>

                    <!-- 5. Final Selling Price -->
                    <div class="bg-white p-3 rounded-2xl border-2 border-emerald-400 shadow-2xs">
                        <label class="block text-[11px] font-black text-emerald-950 mb-1 flex items-center gap-1">
                            <span>🏷️ सेलिंग प्राइस (₹) <span class="text-rose-500">*</span></span>
                        </label>
                        <input type="number" name="price" id="edit_prod_price" value="{{ old('price', $product->price) }}" step="0.01" required min="0.01" placeholder="उदा. 349.00" oninput="recalcEditPricing()" class="w-full px-3 py-2 rounded-xl border border-emerald-400 bg-emerald-50/40 text-xs font-black text-emerald-950 focus:border-emerald-600 focus:bg-white focus:outline-hidden">
                        <span class="text-[10px] text-emerald-800 block mt-1 font-semibold leading-tight">ग्राहक को बेचने का भाव</span>
                    </div>
                </div>

                <!-- Live Dynamic Analytics & Validation Card -->
                <div id="edit_pricing_live_status" class="hidden rounded-2xl p-3 border transition-all">
                    <!-- Populated dynamically by JavaScript -->
                </div>

                <!-- Over-MRP Red Warning Banner -->
                <div id="edit_mrp_warning_banner" class="hidden rounded-2xl p-3.5 bg-rose-50 border-2 border-rose-400 text-rose-900 transition-all flex items-start gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg mt-0.5"></i>
                    <div>
                        <h4 class="text-xs font-black text-rose-900">⚠️ कानूनी चेतावनी (Legal Metrology Act Alert):</h4>
                        <p id="edit_mrp_warning_text" class="text-xs font-medium text-rose-800 mt-0.5"></p>
                    </div>
                </div>

            </div>

            <script>
            function recalcEditPricing() {
                const listMrp = parseFloat(document.getElementById('edit_prod_mrp')?.value) || 0;
                const disc = parseFloat(document.getElementById('edit_prod_discount')?.value) || 0;
                let cost = parseFloat(document.getElementById('edit_prod_cost')?.value) || 0;
                const boxMrp = parseFloat(document.getElementById('edit_prod_box_mrp')?.value) || 0;
                const sellingPrice = parseFloat(document.getElementById('edit_prod_price')?.value) || 0;

                // Auto calculate Cost if List MRP and Less% provided
                if (listMrp > 0 && disc > 0 && (!cost || document.activeElement.id === 'edit_prod_mrp' || document.activeElement.id === 'edit_prod_discount')) {
                    cost = Math.round((listMrp * (1 - (disc / 100))) * 100) / 100;
                    document.getElementById('edit_prod_cost').value = cost.toFixed(2);
                }

                const liveContainer = document.getElementById('edit_pricing_live_status');
                const warningBanner = document.getElementById('edit_mrp_warning_banner');
                const warningText = document.getElementById('edit_mrp_warning_text');

                // 1. Box MRP vs Selling Price Check
                if (boxMrp > 0 && sellingPrice > boxMrp) {
                    warningBanner.classList.remove('hidden');
                    warningText.innerText = `सेलिंग प्राइस (₹${sellingPrice.toFixed(2)}) डिब्बे पर छपे वास्तविक प्रिंटेड MRP (₹${boxMrp.toFixed(2)}) से अधिक नहीं हो सकता! कृपया सेलिंग प्राइस को कम करें.`;
                    document.getElementById('edit_prod_price').classList.add('border-rose-500', 'bg-rose-50');
                } else {
                    warningBanner.classList.add('hidden');
                    document.getElementById('edit_prod_price').classList.remove('border-rose-500', 'bg-rose-50');
                }

                // 2. Dynamic Discount & Margin calculation
                let badges = [];

                if (boxMrp > 0 && sellingPrice > 0 && sellingPrice <= boxMrp) {
                    const discountPercent = Math.round(((boxMrp - sellingPrice) / boxMrp) * 100);
                    const saving = (boxMrp - sellingPrice).toFixed(2);
                    badges.push(`
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-purple-100 text-purple-900 text-xs font-black border border-purple-200">
                            <i class="fa-solid fa-tags text-purple-600"></i>
                            ग्राहक छूट: <strong>${discountPercent}% OFF</strong> (डिब्बे MRP ₹${boxMrp.toFixed(2)} पर <strong>₹${saving} की बचत</strong>)
                        </span>
                    `);
                }

                if (cost > 0 && sellingPrice > 0) {
                    const margin = (sellingPrice - cost).toFixed(2);
                    const marginPercent = Math.round(((sellingPrice - cost) / sellingPrice) * 100);
                    if (margin >= 0) {
                        badges.push(`
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-100 text-emerald-900 text-xs font-black border border-emerald-200">
                                <i class="fa-solid fa-sack-dollar text-emerald-600"></i>
                                आपका ग्रॉस मुनाफ़ा: <strong>₹${margin}</strong> (${marginPercent}% मार्जिन)
                            </span>
                        `);
                    } else {
                        badges.push(`
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-rose-100 text-rose-900 text-xs font-black border border-rose-200">
                                <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                                ⚠️ घाटा चेतावनी: लागत ₹${cost.toFixed(2)} से कम में बिक रहा है! (घाटा ₹${Math.abs(margin)})
                            </span>
                        `);
                    }
                }

                if (badges.length > 0) {
                    liveContainer.innerHTML = `<div class="flex flex-wrap items-center gap-2">${badges.join('')}</div>`;
                    liveContainer.className = 'rounded-2xl p-3 bg-white/80 border border-gray-200 shadow-2xs block';
                } else {
                    liveContainer.className = 'hidden';
                }
            }

            // Run on load
            document.addEventListener('DOMContentLoaded', recalcEditPricing);
            </script>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-gray-700">Main Cover Photo (Optional)</label>
                        <button type="button" onclick="openEditGalleryModal()" class="px-3 py-1 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[11px] font-bold transition flex items-center gap-1.5 border border-indigo-200">
                            <i class="fa-solid fa-photo-film text-indigo-600"></i>
                            <span>कैटलॉग गैलरी से चुनें ({{ count($galleryImages ?? []) }} Photos)</span>
                        </button>
                    </div>

                    <input type="hidden" name="existing_image_url" id="existing_image_url" value="">
                    <input type="file" name="image" id="edit_file_image" accept="image/*" onchange="previewEditUploadedFile(this)" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">

                    <div id="cover_photo_preview_container" class="mt-2 flex items-center gap-3 p-2.5 bg-gray-50 rounded-xl border border-gray-200 {{ $product->image ? '' : 'hidden' }}">
                        <img id="cover_photo_preview_img" src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-12 w-12 object-contain rounded-lg border border-gray-200 bg-white shadow-2xs">
                        <div class="flex-1 min-w-0">
                            <span class="text-[11px] text-gray-800 font-bold block" id="cover_photo_status_text">Current Photo</span>
                            <span class="text-[10px] text-emerald-600 font-semibold" id="cover_photo_source_text">Live Catalog Image</span>
                        </div>
                        <button type="button" onclick="clearSelectedCoverPhoto()" class="text-xs text-rose-600 hover:text-rose-800 font-bold px-2 py-1" title="फोटो हटाएं">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>

                <!-- Add More Gallery Photos -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">
                        <i class="fa-solid fa-images text-indigo-600 mr-1"></i> Add More Gallery Photos (4-5 Photos)
                    </label>
                    <input type="file" name="images[]" multiple accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <span class="text-[10px] text-gray-400 block mt-1">Select multiple new images to add to the photo gallery</span>
                </div>

                <!-- Video URL -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">
                        <i class="fa-brands fa-youtube text-red-600 mr-1"></i> Product Video URL (YouTube / Reel)
                    </label>
                    <input type="url" name="video_url" value="{{ old('video_url', $product->video_url) }}" placeholder="https://www.youtube.com/watch?v=... or Shorts / Reel" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
                    <span class="text-[10px] text-gray-400 block mt-1">Live demo or unboxing link visible to buyers</span>
                </div>
            </div>

            <!-- Existing Gallery Photos Grid -->
            @if($product->images && $product->images->count() > 0)
                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4">
                    <label class="block text-xs font-bold text-gray-800 mb-2">
                        <i class="fa-solid fa-photo-film text-blue-600 mr-1"></i> Current Gallery Photos ({{ $product->images->count() }})
                    </label>
                    <div class="flex flex-wrap gap-3">
                        @foreach($product->images as $img)
                            <div class="relative group w-20 h-20 rounded-xl overflow-hidden border border-gray-200 bg-white shadow-xs" id="gallery-img-{{ $img->id }}">
                                <img src="{{ $img->url }}" class="w-full h-full object-cover">
                                <button type="button" onclick="deleteGalleryImage({{ $product->id }}, {{ $img->id }})" class="absolute top-1 right-1 w-6 h-6 bg-red-600/90 hover:bg-red-700 text-white rounded-full flex items-center justify-center text-xs opacity-90 hover:opacity-100 hover:scale-110 transition shadow-sm" title="Remove photo">
                                    <i class="fa-solid fa-trash text-[10px]"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Description -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Product Description <span class="text-rose-500">*</span></label>
                <textarea name="description" rows="4" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">{{ old('description', $product->description) }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('seller.products.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-100 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    <span>Update Product</span>
                </button>
            </div>
        </form>

        <script>
            function deleteGalleryImage(productId, imageId) {
                if (!confirm('Are you sure you want to remove this photo from the gallery?')) {
                    return;
                }
                fetch(`/seller/products/${productId}/images/${imageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const el = document.getElementById(`gallery-img-${imageId}`);
                        if (el) el.remove();
                    } else {
                        alert('Could not delete image. Please try again.');
                    }
                })
                .catch(() => alert('Network error deleting image.'));
            }

            // ==========================================
            // 🖼️ EDIT PAGE GALLERY PICKER MODAL
            // ==========================================
            function openEditGalleryModal() {
                const modal = document.getElementById('editGalleryModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            }

            function closeEditGalleryModal() {
                const modal = document.getElementById('editGalleryModal');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }

            function selectPhotoForEdit(imgUrl, imgName) {
                const hiddenInput = document.getElementById('existing_image_url');
                if (hiddenInput) hiddenInput.value = imgUrl;

                // Clear file input so existing URL takes precedence
                const fileInp = document.getElementById('edit_file_image');
                if (fileInp) fileInp.value = '';

                // Update Preview container
                const previewImg = document.getElementById('cover_photo_preview_img');
                if (previewImg) previewImg.src = imgUrl;

                const statusText = document.getElementById('cover_photo_status_text');
                if (statusText) statusText.textContent = imgName || 'Selected from Gallery';

                const sourceText = document.getElementById('cover_photo_source_text');
                if (sourceText) sourceText.textContent = '✨ Selected from Catalog Gallery';

                const previewCont = document.getElementById('cover_photo_preview_container');
                if (previewCont) previewCont.classList.remove('hidden');

                closeEditGalleryModal();
            }

            function previewEditUploadedFile(input) {
                if (input.files && input.files[0]) {
                    const file = input.files[0];
                    // Clear existing URL so uploaded file is used
                    const hiddenInput = document.getElementById('existing_image_url');
                    if (hiddenInput) hiddenInput.value = '';

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewImg = document.getElementById('cover_photo_preview_img');
                        if (previewImg) previewImg.src = e.target.result;

                        const statusText = document.getElementById('cover_photo_status_text');
                        if (statusText) statusText.textContent = file.name;

                        const sourceText = document.getElementById('cover_photo_source_text');
                        if (sourceText) sourceText.textContent = '📤 New File to Upload';

                        const previewCont = document.getElementById('cover_photo_preview_container');
                        if (previewCont) previewCont.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            }

            function clearSelectedCoverPhoto() {
                const hiddenInput = document.getElementById('existing_image_url');
                if (hiddenInput) hiddenInput.value = '';

                const fileInp = document.getElementById('edit_file_image');
                if (fileInp) fileInp.value = '';

                const previewCont = document.getElementById('cover_photo_preview_container');
                if (previewCont) previewCont.classList.add('hidden');
            }

            function filterEditGalleryPhotos(searchVal) {
                const q = (searchVal || '').toLowerCase().trim();
                const items = document.querySelectorAll('.edit-gallery-item');
                items.forEach(it => {
                    const name = it.getAttribute('data-name') || '';
                    if (!q || name.includes(q)) {
                        it.classList.remove('hidden');
                    } else {
                        it.classList.add('hidden');
                    }
                });
            }
        </script>

    </main>

    <!-- 🖼️ Catalog Gallery Picker Modal for Edit Page -->
    <div id="editGalleryModal" class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-5" onclick="closeEditGalleryModal()">
        <div class="bg-white rounded-3xl max-w-4xl w-full max-h-[90vh] shadow-2xl border border-gray-100 flex flex-col overflow-hidden animate-in fade-in zoom-in duration-200" onclick="event.stopPropagation()">
            <div class="p-4 sm:p-5 border-b border-gray-100 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-photo-film"></i>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-black text-white">कैटलॉग फोटो गैलरी (Catalog Gallery)</h3>
                        <p class="text-[11px] text-slate-300">पहले से अपलोड की गई तस्वीरों में से 1-क्लिक में चुनें</p>
                    </div>
                </div>
                <button type="button" onclick="closeEditGalleryModal()" class="h-8 w-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="p-4 sm:p-5 overflow-y-auto space-y-4 flex-1">
                <div class="relative w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" id="editGallerySearchInput" oninput="filterEditGalleryPhotos(this.value)" placeholder="गैलरी में खोजें (Elbow, Tee, Pipe, Socket, Brass...)" class="w-full pl-8 pr-3 py-2.5 rounded-xl border border-gray-200 text-xs bg-gray-50 focus:bg-white focus:border-indigo-500 focus:outline-hidden">
                </div>

                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3 max-h-[55vh] overflow-y-auto p-1" id="editGalleryGrid">
                    @forelse($galleryImages ?? [] as $gImg)
                        @php
                            $imgUrl = is_array($gImg) ? ($gImg['asset_url'] ?? ($gImg['url'] ?? ($gImg['image'] ?? ''))) : ($gImg->image ?? ($gImg->url ?? ''));
                            $imgName = is_array($gImg) ? ($gImg['name'] ?? ($gImg['filename'] ?? 'Item')) : ($gImg->name ?? 'Item');
                        @endphp
                        <div class="edit-gallery-item group relative rounded-2xl border-2 border-gray-200 hover:border-indigo-500 bg-white p-1.5 cursor-pointer transition shadow-2xs hover:shadow-md flex flex-col items-center justify-between"
                             data-url="{{ $imgUrl }}"
                             data-name="{{ strtolower($imgName) }}"
                             onclick="selectPhotoForEdit('{{ $imgUrl }}', '{{ addslashes($imgName) }}')">
                            
                            <div class="h-20 w-full rounded-xl overflow-hidden bg-gray-50 flex items-center justify-center p-1">
                                <img src="{{ $imgUrl }}" alt="{{ $imgName }}" class="h-full w-full object-contain group-hover:scale-105 transition" loading="lazy">
                            </div>

                            <span class="text-[10px] font-extrabold text-gray-700 line-clamp-1 mt-1 text-center w-full" title="{{ $imgName }}">
                                {{ $imgName }}
                            </span>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center text-gray-400">
                            <i class="fa-solid fa-images text-3xl mb-2 text-gray-300"></i>
                            <p class="text-xs font-bold text-gray-500">कोई गैलरी इमेज उपलब्ध नहीं है</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="p-4 sm:p-5 border-t border-gray-100 bg-gray-50 flex items-center justify-end">
                <button type="button" onclick="closeEditGalleryModal()" class="px-5 py-2.5 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-bold transition">
                    बंद करें (Close)
                </button>
            </div>
        </div>
    </div>

    @include('components.ai-copilot-widget')
</body>
</html>
