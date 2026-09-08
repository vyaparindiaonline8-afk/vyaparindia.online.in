<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Universal AI Product Publisher - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800 antialiased min-h-screen">

    <!-- Top Nav -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.channels.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-lg text-gray-900 leading-tight">Universal AI Product Publisher</h1>
                        <p class="text-xs text-gray-500">AI auto-generates SEO details & publishes to Mini-Site, Shopify, & Marketplaces in 1-Click</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.channels.index') }}" class="px-3.5 py-2 rounded-xl bg-gray-100 text-gray-700 font-semibold text-xs">
                        Connected Channels
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- AI Generator Assistant Box -->
        <div class="bg-gradient-to-r from-purple-700 via-indigo-600 to-blue-600 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden space-y-4">
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-xs text-xs font-black uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber-300"></i>
                    <span>AI Listing Generator</span>
                </span>
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-black">Generate Complete Product Listing in 1-Second</h2>
                <p class="text-xs text-purple-100 mt-1">Just type 2-3 words about your product, and AI will write the marketing title, bulleted description, HSN code, SKU, and tags.</p>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <input type="text" id="ai-prompt-input" placeholder="e.g. Pure Linen Cotton Saree Navy Blue / Wireless Earbuds" class="flex-1 px-4 py-3 bg-white text-gray-900 rounded-2xl text-xs font-semibold focus:outline-none shadow-md">
                <button type="button" onclick="generateWithAI()" id="ai-btn" class="px-6 py-3 bg-amber-400 hover:bg-amber-300 active:scale-95 text-gray-950 font-black text-xs rounded-2xl shadow-lg transition-all flex items-center gap-2 shrink-0">
                    <i class="fa-solid fa-bolt"></i>
                    <span id="ai-btn-text">AI Auto-Fill</span>
                </button>
            </div>
        </div>

        <!-- Universal Product Form -->
        <form action="{{ route('seller.channels.publish') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-xs space-y-6">
                <div class="flex items-center gap-2 pb-3 border-b border-gray-100">
                    <i class="fa-solid fa-box text-blue-600"></i>
                    <h3 class="font-extrabold text-base text-gray-900">Product Specifications & Content</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-gray-700 mb-1">Product Title (SEO Optimized) <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="product-name" required placeholder="Product title will be auto-generated or entered here" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-bold text-sm">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                        <select name="category_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Selling Price (₹) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="price" id="product-price" required placeholder="e.g. 1499" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-bold text-sm">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Available Inventory Stock <span class="text-red-500">*</span></label>
                        <input type="number" name="stock_quantity" value="50" min="0" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-bold">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">SKU Code</label>
                        <input type="text" name="sku" id="product-sku" placeholder="AUTO-SKU" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-mono">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">HSN Code (GST Classification)</label>
                        <input type="text" name="hsn_code" id="product-hsn" placeholder="500720" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-mono">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">SEO Search Tags</label>
                        <input type="text" name="tags" id="product-tags" placeholder="handcrafted, ethnic wear, bestseller" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-bold text-gray-700 mb-1">Product Description (with Bullet Points) <span class="text-red-500">*</span></label>
                        <textarea name="description" id="product-desc" rows="6" required placeholder="AI will generate high-converting bullet points and care instructions..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none leading-relaxed"></textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-bold text-gray-700 mb-1">Product Image Upload</label>
                        <input type="file" name="image" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                    </div>
                </div>
            </div>

            <!-- Target Channels Selection -->
            <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-xs space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-gray-100">
                    <i class="fa-solid fa-share-nodes text-purple-600"></i>
                    <h3 class="font-extrabold text-base text-gray-900">Select Publishing Channels</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <!-- Mini-Site (Default Always Selected) -->
                    <label class="flex items-center gap-3 p-4 rounded-2xl border-2 border-emerald-500 bg-emerald-50/50 cursor-pointer">
                        <input type="checkbox" checked disabled class="h-4 w-4 text-emerald-600 rounded">
                        <div>
                            <div class="font-bold text-gray-900">Your Mini-Site Storefront</div>
                            <div class="text-[11px] text-gray-500">Live on your D2C custom domain/slug</div>
                        </div>
                    </label>

                    @foreach($connectedChannels as $chan)
                        <label class="flex items-center gap-3 p-4 rounded-2xl border border-gray-200 hover:border-blue-500 bg-white cursor-pointer transition-colors">
                            <input type="checkbox" name="channels[]" value="{{ $chan->id }}" checked class="h-4 w-4 text-blue-600 rounded">
                            <div>
                                <div class="font-bold text-gray-900">{{ $chan->store_name }} ({{ ucfirst($chan->channel_name) }})</div>
                                <div class="text-[11px] text-gray-500">Auto-push listing & sync stock via API</div>
                            </div>
                        </label>
                    @endforeach

                    @if($connectedChannels->isEmpty())
                        <div class="p-4 rounded-2xl bg-gray-50 border border-dashed border-gray-300 text-gray-500 flex items-center justify-between">
                            <span>No external channels connected yet</span>
                            <a href="{{ route('seller.channels.index') }}" class="font-bold text-blue-600 underline">Connect Shopify / Amazon &rarr;</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-2">
                <button type="submit" class="w-full sm:w-auto px-10 py-4 rounded-2xl bg-gray-900 hover:bg-gray-800 active:scale-98 text-white font-extrabold text-sm shadow-xl transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Publish Across Selected Channels in 1-Click</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        async function generateWithAI() {
            let prompt = document.getElementById('ai-prompt-input').value.trim();
            if (!prompt) {
                alert('Please type a product name or keyword (e.g. Cotton Kurta Set).');
                return;
            }

            let btn = document.getElementById('ai-btn');
            let btnText = document.getElementById('ai-btn-text');
            btn.disabled = true;
            btnText.innerText = 'Generating...';

            try {
                let response = await fetch("{{ route('seller.channels.aiGenerate') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ prompt: prompt })
                });

                let res = await response.json();
                if (res.success && res.data) {
                    let d = res.data;
                    document.getElementById('product-name').value = d.enhanced_title;
                    document.getElementById('product-desc').value = d.description;
                    document.getElementById('product-sku').value = d.sku;
                    document.getElementById('product-hsn').value = d.hsn_code;
                    document.getElementById('product-tags').value = d.tags;
                    if (!document.getElementById('product-price').value) {
                        document.getElementById('product-price').value = d.suggested_price;
                    }
                } else {
                    alert('Could not generate listing details.');
                }
            } catch (err) {
                console.error(err);
                alert('Failed to connect to AI listing generator.');
            } finally {
                btn.disabled = false;
                btnText.innerText = 'AI Auto-Fill';
            }
        }
    </script>
</body>
</html>