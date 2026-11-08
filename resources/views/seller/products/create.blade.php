<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product - VyaparIndia</title>
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
                        <span class="ml-2 text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">New Product Listing</span>
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
            <h1 class="text-2xl font-black text-gray-900">Add Product to Storefront</h1>
            <p class="text-xs text-gray-500 mt-1">List your inventory item, pick an existing category or create a custom one on the fly.</p>
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

        <form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-xs space-y-6">
            @csrf

            <!-- Product Title -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Product Title / Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Astral CPVC SDR 11 Pipe 1 inch (3 meter)" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
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
                        <label class="block text-xs font-bold text-gray-700 mb-1">Select from Existing Categories</label>
                        <select name="category_id" id="category_select" class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-xs font-bold bg-white focus:border-blue-500 focus:outline-hidden">
                            <option value="">-- Choose Existing Category --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Option 2: Custom Category Creation -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Or Type New Category</label>
                        <input type="text" name="new_category_name" value="{{ old('new_category_name') }}" placeholder="e.g. Sanitary Brassware or Tiffin Items" class="w-full px-3 py-2.5 rounded-xl border border-blue-300 bg-white text-xs font-bold focus:border-blue-500 focus:outline-hidden">
                        <span class="text-[10px] text-blue-700 block mt-1">✨ Will be auto-saved and become searchable by all buyers globally!</span>
                    </div>
                </div>
            </div>

            <!-- Price, MRP, Cost & Photos -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 bg-amber-50/40 p-4 rounded-2xl border border-amber-200/70">
                <div>
                    <label class="block text-xs font-black text-amber-950 mb-1">
                        📦 डिब्बा MRP (प्रिंटेड भाव ₹)
                    </label>
                    <input type="number" name="mrp" id="prod_mrp" value="{{ old('mrp') }}" step="0.01" placeholder="उदा. 340.00" oninput="recalcProdCost()" class="w-full px-3.5 py-2.5 rounded-xl border border-amber-300 bg-white text-xs font-black text-amber-950 focus:border-amber-500 focus:outline-hidden shadow-2xs">
                    <span class="text-[10px] text-amber-800 block mt-0.5 font-bold">डिब्बे पर छपा वास्तविक MRP</span>
                </div>

                <div>
                    <label class="block text-xs font-black text-amber-950 mb-1">
                        🏷️ डिस्काउंट (Less %)
                    </label>
                    <input type="number" name="trade_discount_percent" id="prod_discount" value="{{ old('trade_discount_percent') }}" step="0.5" min="0" max="99" placeholder="उदा. 45" oninput="recalcProdCost()" class="w-full px-3.5 py-2.5 rounded-xl border border-amber-300 bg-white text-xs font-black text-amber-950 focus:border-amber-500 focus:outline-hidden shadow-2xs">
                    <span class="text-[10px] text-amber-800 block mt-0.5 font-bold">MRP पर लेस %</span>
                </div>

                <div>
                    <label class="block text-xs font-black text-indigo-950 mb-1">
                        💰 खरीद लागत (Purchase Cost ₹)
                    </label>
                    <input type="number" name="purchase_price" id="prod_cost" value="{{ old('purchase_price') }}" step="0.01" placeholder="उदा. 187.00" class="w-full px-3.5 py-2.5 rounded-xl border-2 border-indigo-400 bg-indigo-50/50 text-xs font-black text-indigo-950 focus:border-indigo-600 focus:outline-hidden shadow-2xs">
                    <span class="text-[10px] text-indigo-700 block mt-0.5 font-bold">आपकी शुद्ध खरीद लागत</span>
                </div>

                <div>
                    <label class="block text-xs font-black text-emerald-950 mb-1">
                        🏷️ सेलिंग प्राइस (Selling Price ₹) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="price" id="prod_price" value="{{ old('price') }}" step="0.01" required placeholder="उदा. 499.00" class="w-full px-3.5 py-2.5 rounded-xl border-2 border-emerald-400 bg-emerald-50/40 text-xs font-black text-emerald-950 focus:border-emerald-600 focus:outline-hidden shadow-2xs">
                    <span class="text-[10px] text-emerald-800 block mt-0.5 font-bold">ग्राहक को बेचने का भाव</span>
                </div>
            </div>

            <script>
            function recalcProdCost() {
                const mrp = parseFloat(document.getElementById('prod_mrp')?.value) || 0;
                const disc = parseFloat(document.getElementById('prod_discount')?.value) || 0;
                if (mrp > 0 && disc > 0) {
                    const cost = Math.round((mrp * (1 - (disc / 100))) * 100) / 100;
                    document.getElementById('prod_cost').value = cost.toFixed(2);
                }
            }
            </script>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Main Cover Photo</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">
                        <i class="fa-solid fa-images text-indigo-600 mr-1"></i> Additional Photos (Up to 4-5 images)
                    </label>
                    <input type="file" name="images[]" multiple accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <span class="text-[10px] text-gray-400 block mt-1">Select multiple images to show different product angles/unboxing</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">
                        <i class="fa-brands fa-youtube text-red-600 mr-1"></i> Product Video URL (YouTube / Reel)
                    </label>
                    <input type="url" name="video_url" value="{{ old('video_url') }}" placeholder="https://www.youtube.com/watch?v=... or Shorts / Reel link" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
                    <span class="text-[10px] text-gray-400 block mt-1">Buyers will be able to watch live demo/unboxing on product page</span>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Product Description <span class="text-rose-500">*</span></label>
                <textarea name="description" rows="4" required placeholder="Specify grade, material, dimensions, package quantity, and guarantee..." class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">{{ old('description') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('seller.products.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-100 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>Publish Product</span>
                </button>
            </div>
        </form>

    </main>

    @include('components.ai-copilot-widget')
</body>
</html>
