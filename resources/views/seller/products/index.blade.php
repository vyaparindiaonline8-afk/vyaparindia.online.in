<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Catalog Products - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.dashboard') }}" class="h-9 w-9 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-base shadow-sm">
                        V
                    </a>
                    <div>
                        <span class="font-black text-gray-900 text-base tracking-tight">VyaparIndia</span>
                        <span class="ml-2 text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Product Catalog</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @if(Auth::user()->isOwnerOrAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-extrabold transition flex items-center gap-1.5 border border-purple-200" title="Super Admin Hub">
                            <span>👑 Admin Hub</span>
                        </a>
                    @endif

                    <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-extrabold transition flex items-center gap-1.5 shadow-xs" title="Home Marketplace">
                        <i class="fa-solid fa-house text-blue-600"></i>
                        <span>Home</span>
                    </a>
                    <a href="{{ route('seller.catalog.upload') }}" class="px-3.5 py-1.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-file-pdf"></i> Upload Catalog PDF
                    </a>
                    <a href="{{ route('seller.dashboard') }}" class="text-xs font-bold text-gray-600 hover:text-blue-600 transition">
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(session('warning'))
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base shrink-0"></i>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        <!-- Tier Status & Quota Banner -->
        @php
            $isOwner = Auth::user()->isOwnerOrAdmin();
            $limit = Auth::user()->productLimit();
            $productCount = $products->count();
            $isProfileOnly = method_exists(Auth::user(), 'isProfileOnly') ? Auth::user()->isProfileOnly() : (!Auth::user()->sellerPage()->exists());
        @endphp

        @if($isOwner)
            <div class="bg-gradient-to-r from-purple-50 via-indigo-50 to-blue-50 border border-purple-200 rounded-3xl p-4 sm:p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-crown"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black text-purple-900">👑 प्लेटफॉर्म ओनर (Super Admin)</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 text-[10px] font-bold">✨ Unlimited Products (कोई लिमिट नहीं)</span>
                        </div>
                        <p class="text-[11px] text-purple-700">आपके पास पूरे प्लेटफॉर्म का अनलिमिटेड एक्सेस है। आप जितने चाहें उतने प्रोडक्ट्स व कैटलॉग अपलोड कर सकते हैं।</p>
                    </div>
                </div>
                <span class="text-xs font-bold text-purple-800 bg-white px-3 py-1.5 rounded-xl border border-purple-200 shadow-2xs">
                    कुल लाइव प्रोडक्ट्स: <strong>{{ $productCount }}</strong>
                </span>
            </div>
        @elseif($isProfileOnly)
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-3xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full bg-amber-200 text-amber-900 font-black text-xs">
                            Basic Profile Tier
                        </span>
                        <span class="text-xs font-bold text-amber-800">
                            Quota: <strong>{{ $productCount }} / {{ $limit ?? 50 }}</strong> products listed
                        </span>
                    </div>
                    <p class="text-xs text-amber-700">
                        Aapka Basic Profile plan active hai (Limit: 50 products). Unlimited products, custom domain, aur WhatsApp cart checkout ke liye Mini-Website activate karein.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('seller.minisite.create') }}" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-black text-xs shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-rocket"></i>
                        <span>Upgrade to Mini-Website</span>
                    </a>
                </div>
            </div>
        @else
            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-3xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black text-emerald-900">Mini-Website Tier Active</span>
                            @if(is_null($limit))
                                <span class="px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-800 text-[10px] font-bold">✨ Unlimited Products</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full {{ $productCount >= $limit ? 'bg-amber-200 text-amber-900' : 'bg-emerald-200 text-emerald-800' }} text-[10px] font-bold">
                                    Quota: {{ $productCount }} / {{ $limit }} Products
                                </span>
                            @endif
                        </div>
                        <p class="text-[11px] text-emerald-700">
                            @if(!is_null($limit) && $productCount >= $limit)
                                <strong class="text-amber-800">⚠️ 100 प्रोडक्ट्स की मुफ्त सीमा पूरी हो चुकी है।</strong> नए प्रोडक्ट्स जोड़ने के लिए प्रीमियम प्लान में अपग्रेड करें।
                            @else
                                Aapka branded online storefront live hai with direct WhatsApp order & cart checkout.
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.minisite.edit') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 underline shrink-0">
                        Customize Storefront
                    </a>
                </div>
            </div>
        @endif

        <!-- Dual Inventory Stats: Product Cards vs Total Size Variants -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white rounded-3xl p-5 border border-gray-200 shadow-xs flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-layer-group text-blue-600"></i>
                        <span>मुख्य प्रोडक्ट कार्ड्स (Product Cards)</span>
                    </span>
                    <div class="text-2xl font-black text-gray-900">{{ $productCardsCount ?? $productCount }} <span class="text-xs font-medium text-gray-500">कार्ड्स</span></div>
                    <p class="text-[11px] text-gray-500">मुख्य प्रोडक्ट वैरायटी (जैसे: UPVC Pipe, Elbow 90°, Coupler, Tee आदि)</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-gray-200 shadow-xs flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-tags text-emerald-600"></i>
                        <span>कुल साइज़ वेरिएंट्स व रेट्स (Total Items / SKUs)</span>
                    </span>
                    <div class="text-2xl font-black text-emerald-600">{{ $totalVariantsCount ?? $productCount }} <span class="text-xs font-medium text-gray-500">आइटम्स</span></div>
                    <p class="text-[11px] text-gray-500">सभी अलग-अलग साइज़ (15mm, 20mm, 25mm, 40mm) के एक्टिव भाव</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-cubes"></i>
                </div>
            </div>
        </div>

        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900">कैटलॉग प्रोडक्ट्स ({{ $productCardsCount ?? $productCount }})</h1>
                <p class="text-xs text-gray-500">दैनिक भाव, साइज़ वेरिएंट्स और स्टॉक का प्रबंधन करें।</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @if($productCount > 0)
                    <button type="button" onclick="openClearAllModal()" class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-extrabold text-xs transition flex items-center gap-2 shadow-2xs hover:scale-102 active:scale-98" title="पुराने सभी प्रोडक्ट्स साफ़ करें ताकि नई एक्सेल शीट फ्रेश अपलोड हो सके">
                        <i class="fa-solid fa-trash-can text-rose-500"></i>
                        <span>पुराना कैटलॉग साफ़ करें (Clear All)</span>
                    </button>
                @endif

                <a href="{{ route('seller.brand-master.index') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-extrabold text-xs shadow-md shadow-amber-500/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-award"></i>
                    <span>ब्रांड मास्टर कैटलॉग (Plasto) ⚡</span>
                </a>

                @if(Auth::user()->canAddProduct())
                    <a href="{{ route('seller.products.create') }}" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add Single Product</span>
                    </a>
                @else
                    <button type="button" onclick="alert('आपकी {{ $limit ?? 100 }} प्रोडक्ट्स की मुफ्त सीमा पूरी हो चुकी है! नए प्रोडक्ट्स जोड़ने के लिए कृपया प्रीमियम प्लान में अपग्रेड करें।')" class="px-5 py-2.5 rounded-xl bg-amber-100 hover:bg-amber-200 text-amber-900 border border-amber-300 font-bold text-xs transition flex items-center gap-2" title="Limit Reached">
                        <i class="fa-solid fa-lock"></i>
                        <span>{{ $limit ?? 100 }} Products Limit Reached (Upgrade)</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- 📊 Excel Sync & Daily Rate Revision Suite Banner -->
        <div class="bg-gradient-to-br from-indigo-900 via-slate-900 to-blue-900 rounded-3xl p-5 sm:p-6 text-white shadow-lg relative overflow-hidden">
            <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5 relative z-10">
                <div class="space-y-1.5 max-w-xl">
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-black uppercase tracking-wider border border-emerald-500/30">
                        <i class="fa-solid fa-bolt"></i> 1-क्लिक एक्सेल सिंक & दैनिक भाव रिवीज़न
                    </div>
                    <h2 class="text-lg sm:text-xl font-black text-white tracking-tight">
                        एक्सेल शीट डाउनलोड व बल्क रेट अपडेट
                    </h2>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        हार्डवेयर, मंडी व किराना के दैनिक रेट्स बदलें या पूरे कैटलॉग को एक्सेल में डाउनलोड करके एडिट करें। सिस्टम <strong>Product ID</strong> से पहचान कर नाम व रेट्स तुरंत अपडेट कर देता है।
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <!-- Daily Rates Sheet Export -->
                    <a href="{{ route('seller.products.export_sheet', ['type' => 'rates']) }}" class="px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs transition flex items-center gap-2 backdrop-blur-xs hover:scale-102 active:scale-98" title="Download quick rates revision template">
                        <i class="fa-solid fa-file-invoice-dollar text-amber-400 text-sm"></i>
                        <span>दैनिक भाव शीट (.csv)</span>
                    </a>

                    <!-- Full Catalog Export -->
                    <a href="{{ route('seller.products.export_sheet', ['type' => 'full']) }}" class="px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs transition flex items-center gap-2 backdrop-blur-xs hover:scale-102 active:scale-98" title="Download entire catalog master excel">
                        <i class="fa-solid fa-table-list text-emerald-400 text-sm"></i>
                        <span>पूरा कैटलॉग (.csv)</span>
                    </a>

                    <!-- Upload & Sync Button -->
                    <button type="button" onclick="openExcelSyncModal()" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-black text-xs shadow-md shadow-emerald-500/20 transition flex items-center gap-2 hover:scale-102 active:scale-98">
                        <i class="fa-solid fa-cloud-arrow-up text-sm"></i>
                        <span>1-क्लिक ऑटो सिंक</span>
                    </button>

                    <!-- Interactive Column Mapper Tool Button -->
                    <a href="{{ route('seller.catalog.excel_mapper') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-xs shadow-md shadow-indigo-600/30 transition flex items-center gap-2 hover:scale-102 active:scale-98" title="कॉलम मैपिंग पॉपअप के साथ एक्सेल अपलोड करें">
                        <i class="fa-solid fa-table-columns text-sm text-amber-300"></i>
                        <span>कॉलम मैपिंग टूल (Excel Mapper) ⚡</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 📁 Rate Folders / Collections Filter Bar -->
        <div class="bg-white rounded-3xl border border-gray-200 p-4 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-2 overflow-x-auto pb-1 md:pb-0">
                <span class="text-xs font-black text-gray-500 shrink-0 flex items-center gap-1.5 mr-1">
                    <i class="fa-solid fa-folder-tree text-indigo-600"></i>
                    <span>रेट फोल्डर्स:</span>
                </span>

                <!-- All Tab -->
                <a href="{{ route('seller.products.index') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold shrink-0 transition flex items-center gap-1.5 {{ empty($activeFolder) || $activeFolder === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' }}">
                    <span>सभी आइटम्स (All)</span>
                </a>

                @if(!empty($folders) && $folders->count() > 0)
                    @foreach($folders as $fName)
                        <a href="{{ route('seller.products.index', ['folder' => $fName]) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold shrink-0 transition flex items-center gap-1.5 {{ ($activeFolder ?? '') === $fName ? 'bg-indigo-600 text-white shadow-xs' : 'bg-indigo-50 hover:bg-indigo-100 text-indigo-800 border border-indigo-200' }}">
                            <i class="fa-solid fa-folder text-[10px]"></i>
                            <span>{{ $fName }}</span>
                        </a>
                    @endforeach
                @else
                    <span class="text-[11px] text-gray-400 italic shrink-0">
                        (नीचे आइटम्स को टिक करके फोल्डर में जोड़ें)
                    </span>
                @endif
            </div>

            @if(!empty($activeFolder) && $activeFolder !== 'all')
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-xs font-bold text-indigo-900 bg-indigo-50 px-2.5 py-1 rounded-xl border border-indigo-200">
                        Active Folder: <strong>{{ $activeFolder }}</strong> ({{ $products->count() }} items)
                    </span>
                    <a href="{{ route('seller.products.export_sheet', ['type' => 'rates', 'folder' => $activeFolder]) }}" class="px-3.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs shadow-xs transition flex items-center gap-1.5">
                        <i class="fa-solid fa-download"></i>
                        <span>इस फोल्डर की रेट शीट (.csv)</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- Product Table -->
        <div class="bg-white rounded-3xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 font-bold border-b border-gray-200">
                            <th class="py-3 px-3 w-10 text-center">
                                <input type="checkbox" id="selectAllProductsChk" onchange="toggleSelectAllProducts(this.checked)" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4 cursor-pointer" title="Select All">
                            </th>
                            <th class="py-3 px-4">Item & Rate Folder</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Selling Price</th>
                            <th class="py-3 px-4">Variants</th>
                            <th class="py-3 px-4">Stock Status</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50/60 transition" id="row-prod-{{ $product->id }}">
                                <td class="py-3 px-3 text-center">
                                    <input type="checkbox" value="{{ $product->id }}" data-name="{{ addslashes($product->name) }}" data-folder="{{ $product->group_name }}" class="product-item-chk rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4 cursor-pointer" onchange="onProductSelectionChange()">
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-gray-100 overflow-hidden flex items-center justify-center shrink-0 border border-gray-100">
                                            @if($product->image_url)
                                                <img src="{{ $product->image_url }}" class="h-full w-full object-contain">
                                            @else
                                                <i class="fa-solid fa-cube text-gray-300"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-extrabold text-gray-900 text-sm">{{ $product->name }}</div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] text-gray-400 font-mono">{{ $product->sku ?? 'PRD-STD' }}</span>
                                                @if($product->group_name)
                                                    <a href="{{ route('seller.products.index', ['folder' => $product->group_name]) }}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-700 text-[10px] font-extrabold transition">
                                                        <i class="fa-solid fa-folder text-[9px]"></i>
                                                        <span>{{ $product->group_name }}</span>
                                                    </a>
                                                @else
                                                    <button type="button" onclick="openAssignFolderModalSingle({{ $product->id }}, '{{ addslashes($product->name) }}')" class="text-[10px] text-gray-400 hover:text-indigo-600 font-semibold inline-flex items-center gap-1 transition">
                                                        <i class="fa-solid fa-plus text-[9px]"></i> फोल्डर सेट करें
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-semibold text-gray-700">
                                    {{ $product->category->name ?? 'General' }}
                                </td>
                                <td class="py-3 px-4 font-black text-gray-900">
                                    ₹{{ number_format($product->price, 2) }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($product->variants && $product->variants->count() > 0)
                                        <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-bold text-[10px]">
                                            {{ $product->variants->count() }} Variants
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-[11px]">Single Item</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if(!$product->track_inventory)
                                        <span class="text-gray-500 font-medium text-[11px]">On-Demand</span>
                                    @elseif($product->stock_quantity <= 0)
                                        <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold text-[10px]">Out of Stock</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px]">{{ $product->stock_quantity }} in stock</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('seller.products.edit', $product) }}" class="p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition" title="Edit Product">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('seller.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this product?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition" title="Delete Product">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-400">
                                    <i class="fa-solid fa-box-open text-4xl mb-2 text-gray-300"></i>
                                    <p class="font-bold text-gray-600">No products added yet</p>
                                    <p class="text-xs mt-1">Start listing items or upload a catalog PDF.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- 📊 Excel Sync & Round-Trip Bulk Update Modal -->
    <div id="excelSyncModal" class="fixed inset-0 z-50 bg-slate-900/75 backdrop-blur-xs hidden items-center justify-center p-2 sm:p-4 overflow-y-auto" onclick="closeExcelSyncModal()">
        <div class="bg-white rounded-3xl max-w-xl w-full shadow-2xl border border-gray-100 flex flex-col max-h-[92vh] sm:max-h-[88vh] overflow-hidden my-auto animate-in fade-in zoom-in duration-200" onclick="event.stopPropagation()">
            
            <!-- Modal Header (Always Visible at Top) -->
            <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-emerald-50 via-teal-50 to-indigo-50 shrink-0 sticky top-0 z-20">
                <div class="flex items-center gap-2.5">
                    <div class="h-10 w-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-lg shadow-xs shrink-0">
                        <i class="fa-solid fa-file-excel"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm sm:text-base text-gray-900">एक्सेल शीट अपलोड व ऑटो-सिंक</h3>
                        <p class="text-[11px] text-gray-500">ID के आधार पर नाम, रेट्स व स्टॉक का 1-क्लिक राउंड-ट्रिप अपडेट</p>
                    </div>
                </div>
                <button type="button" onclick="closeExcelSyncModal()" class="h-8 w-8 rounded-full bg-white hover:bg-gray-100 text-gray-500 hover:text-gray-800 flex items-center justify-center text-xs shadow-2xs transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Form Wrapper containing scrollable body and sticky footer -->
            <form action="{{ route('seller.products.import_sheet') }}" method="POST" enctype="multipart/form-data" id="excelSyncForm" onsubmit="return submitExcelSyncForm(event)" class="flex flex-col flex-1 overflow-hidden min-h-0">
                @csrf
                <input type="hidden" name="spreadsheet_rows" id="spreadsheetRowsJson" value="">

                <!-- Scrollable Body (Smooth scrolling for all contents) -->
                <div class="p-5 sm:p-6 space-y-4 overflow-y-auto flex-1 overscroll-contain bg-slate-50/30">
                    <!-- Features Highlights -->
                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200 space-y-1.5 text-[11px] text-slate-700 shadow-2xs">
                        <div class="font-bold text-slate-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            <span>सिस्टम कैसे काम करता है?</span>
                        </div>
                        <ul class="space-y-1 text-slate-600 list-disc list-inside">
                            <li><strong>Product ID से ट्रैकिंग:</strong> शीट में मौजूद <code>Product ID</code> से सही आइटम के रेट्स तुरंत अपडेट होते हैं।</li>
                            <li><strong>नाम बदलना सुरक्षित:</strong> एक्सेल में नाम बदलने पर भी ID से सही आइटम अपडेट होगा (डुप्लीकेट नहीं बनेगा)।</li>
                            <li><strong>खाली लाइनें:</strong> शीट में खाली पंक्तियाँ (Empty lines) सुरक्षित रूप से छोड़ दी जाती हैं।</li>
                        </ul>
                    </div>

                    <!-- 💡 Interactive Column Mapper Notice -->
                    <div class="p-3.5 rounded-2xl bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-2 text-indigo-950 font-black text-xs">
                                <i class="fa-solid fa-table-columns text-indigo-600"></i>
                                <span>कॉलम मैपिंग (Mapping Popup) चाहिए?</span>
                            </div>
                            <p class="text-[11px] text-indigo-700">
                                अगर आपकी एक्सेल शीट का फॉर्मेट अलग है और आप खुद ड्रॉपडाउन से कॉलम (नाम, रेट, साइज आदि) मैच करना चाहते हैं, तो <b>Excel Mapper</b> का इस्तेमाल करें।
                            </p>
                        </div>
                        <a href="{{ route('seller.catalog.excel_mapper') }}" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shrink-0 flex items-center gap-1.5 shadow-sm transition">
                            <span>मैपिंग टूल खोलें</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>

                    <!-- Download Shortcuts in Modal -->
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-amber-50 border border-amber-200 gap-3">
                        <div class="text-[11px] text-amber-900 leading-snug">
                            <strong>शीट डाउनलोड नहीं की?</strong><br>
                            अपनी लाइव लिस्टिंग की शीट अभी डाउनलोड करें:
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('seller.products.export_sheet', ['type' => 'rates']) }}" class="px-2.5 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-black text-[11px] flex items-center gap-1 transition">
                                <i class="fa-solid fa-download"></i> दैनिक भाव
                            </a>
                            <a href="{{ route('seller.products.export_sheet', ['type' => 'full']) }}" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white font-black text-[11px] flex items-center gap-1 transition">
                                <i class="fa-solid fa-download"></i> पूरा कैटलॉग
                            </a>
                        </div>
                    </div>

                    <!-- Upload Drag-and-Drop Area -->
                    <div class="border-2 border-dashed border-gray-300 hover:border-emerald-500 rounded-2xl p-5 text-center bg-white hover:bg-emerald-50/20 cursor-pointer transition group" onclick="document.getElementById('excelFileInput').click()">
                        <i class="fa-solid fa-cloud-arrow-up text-emerald-600 text-3xl mb-2 group-hover:scale-110 transition"></i>
                        <h4 class="text-xs font-bold text-gray-800">एडिट की हुई एक्सेल या CSV फाइल चुनें</h4>
                        <p class="text-[11px] text-gray-400 mt-0.5">Click karein ya file yahan drop karein (.xlsx, .xls, .csv)</p>
                        <input type="file" id="excelFileInput" name="spreadsheet_file" accept=".xlsx, .xls, .csv" class="hidden" onchange="handleExcelFileSelect(event)">
                    </div>

                    <!-- Preview Box -->
                    <div id="excelFilePreviewBox" class="hidden p-3.5 bg-emerald-50 rounded-2xl border border-emerald-200 space-y-1.5">
                        <div class="flex items-center justify-between text-xs font-bold text-emerald-900">
                            <span id="excelFileName" class="truncate max-w-[280px]">filename.xlsx</span>
                            <span id="excelRowCount" class="px-2.5 py-0.5 rounded-full bg-emerald-200 text-emerald-800 text-[10px] font-black">0 Rows Found</span>
                        </div>
                        <p class="text-[11px] text-emerald-700">
                            शीट लोड हो चुकी है! नीचे "सिंक व अपडेट करें" बटन दबाकर नए रेट्स लागू करें।
                        </p>
                    </div>

                    <!-- Allow create new products option -->
                    <div class="flex items-start gap-2.5 p-3 rounded-2xl bg-amber-50/80 border border-amber-200 text-xs">
                        <input type="checkbox" id="allowCreateNewProductsChk" name="allow_create_new" value="1" class="mt-0.5 h-4 w-4 rounded border-amber-300 text-emerald-600 focus:ring-emerald-500">
                        <label for="allowCreateNewProductsChk" class="font-bold text-amber-950 cursor-pointer leading-snug">
                            यदि शीट में कोई नया आइटम मिले तो नया प्रोडक्ट बनाएं
                            <span class="block text-[11px] font-normal text-amber-800 mt-0.5">
                                (अनचेक रखने पर केवल मौजूदा प्रोडक्ट्स के रेट्स अपडेट होंगे — कोई नया कार्ड अपने आप नहीं बनेगा)
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Sticky Footer (Always Visible at Bottom) -->
                <div class="flex items-center justify-between p-4 border-t border-gray-200 bg-white shrink-0 sticky bottom-0 z-20">
                    <button type="button" onclick="closeExcelSyncModal()" class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition">
                        रद्द करें (Cancel)
                    </button>
                    <button type="submit" id="btnApplyExcelSync" disabled class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-xs sm:text-sm font-black shadow-md shadow-emerald-600/20 flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>सिंक व अपडेट करें (Apply Changes)</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 🎯 Floating Batch Actions Bar (Shown when items are ticked/selected) -->
    <div id="floatingBatchActionBar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-slate-900/95 text-white px-5 py-3.5 rounded-3xl shadow-2xl border border-slate-700/80 backdrop-blur-md hidden items-center gap-3 sm:gap-4 animate-in fade-in slide-in-from-bottom-4 duration-200 max-w-2xl w-[94%] sm:w-auto">
        <div class="flex items-center gap-2 shrink-0">
            <span class="h-6 w-6 rounded-full bg-emerald-500 text-slate-900 font-black text-xs flex items-center justify-center" id="selectedBadgeCount">0</span>
            <span class="text-xs font-bold text-slate-200 hidden sm:inline">आइटम्स चुने गए</span>
        </div>

        <div class="h-4 w-px bg-slate-700 hidden sm:block"></div>

        <div class="flex items-center gap-2 flex-wrap">
            <!-- Download Selected Rate Sheet -->
            <button type="button" onclick="downloadSelectedRateSheet()" class="px-3.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs transition flex items-center gap-1.5 shadow-xs">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <span>रेट शीट डाउनलोड (.csv)</span>
            </button>

            <!-- Assign to Folder -->
            <button type="button" onclick="openAssignFolderModalForSelected()" class="px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs transition flex items-center gap-1.5 shadow-xs">
                <i class="fa-solid fa-folder-plus"></i>
                <span>फोल्डर में जोड़ें</span>
            </button>

            <!-- Clear Selection -->
            <button type="button" onclick="clearAllProductSelections()" class="h-7 w-7 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center text-xs transition" title="Clear selection">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    <!-- 📁 Folder Assignment Modal -->
    <div id="assignFolderModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-4 overflow-y-auto" onclick="closeAssignFolderModal()">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4 animate-in fade-in zoom-in duration-200 my-auto" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="h-9 w-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base">
                        <i class="fa-solid fa-folder-plus"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-gray-900">रेट फोल्डर में जोड़ें / अलग करें</h3>
                        <p class="text-[11px] text-gray-500" id="assignFolderModalSubtitle">चयनित आइटम्स के लिए फोल्डर चुनें</p>
                    </div>
                </div>
                <button type="button" onclick="closeAssignFolderModal()" class="h-7 w-7 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center text-xs transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Quick Presets -->
            <div class="space-y-1.5">
                <label class="text-[11px] font-bold text-gray-600 uppercase tracking-wider">क्विक फोल्डर प्रेसेट्स:</label>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="setPresetFolder('दैनिक भाव (Daily Rates)')" class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold transition">
                        ⚡ दैनिक भाव (Daily Rates)
                    </button>
                    <button type="button" onclick="setPresetFolder('मंडी भाव (Commodity)')" class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold transition">
                        🌾 मंडी भाव (Commodity)
                    </button>
                    <button type="button" onclick="setPresetFolder('पाइप्स & फिटिंग्स')" class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-bold transition">
                        🔧 पाइप्स & फिटिंग्स
                    </button>
                    <button type="button" onclick="setPresetFolder('फास्ट मूविंग')" class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-xs font-bold transition">
                        📦 फास्ट मूविंग
                    </button>
                </div>
            </div>

            <!-- Custom Folder Name Input Form -->
            <form onsubmit="return submitAssignFolderForm(event)" class="space-y-4 pt-2">
                <input type="hidden" id="assignProductIdsInput" value="">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">फोल्डर का नाम लिखें या चुनें:</label>
                    <input type="text" id="targetFolderNameInput" required placeholder="जैसे: दैनिक भाव, मंडी भाव, सेनेटरी..." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 text-xs font-semibold outline-hidden">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" onclick="closeAssignFolderModal()" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold">
                        रद्द करें
                    </button>
                    <button type="submit" id="btnSubmitAssignFolder" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black shadow-md shadow-indigo-600/20 flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-check"></i>
                        <span>फोल्डर में सेव करें</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

        // ==========================================
        // 🎯 SELECTION & TICKING SYSTEM
        // ==========================================
        function toggleSelectAllProducts(checked) {
            document.querySelectorAll('.product-item-chk').forEach(cb => {
                cb.checked = checked;
            });
            onProductSelectionChange();
        }

        function onProductSelectionChange() {
            const checkedBoxes = document.querySelectorAll('.product-item-chk:checked');
            const count = checkedBoxes.length;
            const bar = document.getElementById('floatingBatchActionBar');
            const countEl = document.getElementById('selectedBadgeCount');
            const selectAllChk = document.getElementById('selectAllProductsChk');

            if (count > 0) {
                countEl.textContent = count;
                bar.classList.remove('hidden');
                bar.classList.add('flex');
            } else {
                bar.classList.add('hidden');
                bar.classList.remove('flex');
                if (selectAllChk) selectAllChk.checked = false;
            }
        }

        function getSelectedProductIds() {
            return Array.from(document.querySelectorAll('.product-item-chk:checked')).map(cb => cb.value);
        }

        function clearAllProductSelections() {
            document.querySelectorAll('.product-item-chk').forEach(cb => cb.checked = false);
            const selectAllChk = document.getElementById('selectAllProductsChk');
            if (selectAllChk) selectAllChk.checked = false;
            onProductSelectionChange();
        }

        function downloadSelectedRateSheet() {
            const ids = getSelectedProductIds();
            if (ids.length === 0) {
                alert('Pehle kam se kam ek product select karein!');
                return;
            }
            const url = "{{ route('seller.products.export_sheet') }}?type=rates&items=" + ids.join(',');
            window.location.href = url;
        }

        // ==========================================
        // 📁 FOLDER ASSIGNMENT SYSTEM
        // ==========================================
        function openAssignFolderModalForSelected() {
            const ids = getSelectedProductIds();
            if (ids.length === 0) {
                alert('Pehle kam se kam ek product select karein!');
                return;
            }
            document.getElementById('assignProductIdsInput').value = ids.join(',');
            document.getElementById('assignFolderModalSubtitle').textContent = `${ids.length} चयनित आइटम्स के लिए फोल्डर नाम सेट करें`;
            document.getElementById('targetFolderNameInput').value = '';
            
            const modal = document.getElementById('assignFolderModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function openAssignFolderModalSingle(productId, productName) {
            document.getElementById('assignProductIdsInput').value = productId;
            document.getElementById('assignFolderModalSubtitle').textContent = `"${productName}" के लिए फोल्डर नाम सेट करें`;
            document.getElementById('targetFolderNameInput').value = '';
            
            const modal = document.getElementById('assignFolderModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeAssignFolderModal() {
            const modal = document.getElementById('assignFolderModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function setPresetFolder(name) {
            document.getElementById('targetFolderNameInput').value = name;
        }

        function submitAssignFolderForm(event) {
            event.preventDefault();
            const productIds = document.getElementById('assignProductIdsInput').value;
            const folderName = document.getElementById('targetFolderNameInput').value.trim();

            if (!productIds || !folderName) {
                alert('Kripya folder name bharein!');
                return false;
            }

            const btn = document.getElementById('btnSubmitAssignFolder');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> सेव हो रहा है...';

            fetch("{{ route('seller.products.assign_folder') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({
                    product_ids: productIds,
                    folder_name: folderName
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message || 'Error assigning folder');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>फोल्डर में सेव करें</span>';
                }
            })
            .catch(err => {
                console.error(err);
                alert('Failed to update folder. Please try again.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>फोल्डर में सेव करें</span>';
            });

            return false;
        }

        // ==========================================
        // 📊 EXCEL IMPORT MODAL SYSTEM
        // ==========================================
        function openExcelSyncModal() {
            const modal = document.getElementById('excelSyncModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeExcelSyncModal() {
            const modal = document.getElementById('excelSyncModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function handleExcelFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            document.getElementById('excelFileName').textContent = file.name;
            const previewBox = document.getElementById('excelFilePreviewBox');
            const rowCountEl = document.getElementById('excelRowCount');
            const btnApply = document.getElementById('btnApplyExcelSync');

            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const sheetName = workbook.SheetNames[0];
                    const sheet = workbook.Sheets[sheetName];
                    const jsonRows = XLSX.utils.sheet_to_json(sheet, { defval: '' });

                    if (jsonRows.length === 0) {
                        alert('Sheet khali hai ya koi row nahi mili!');
                        return;
                    }

                    document.getElementById('spreadsheetRowsJson').value = JSON.stringify(jsonRows);
                    rowCountEl.textContent = `${jsonRows.length} Rows Detected`;
                    previewBox.classList.remove('hidden');
                    btnApply.disabled = false;
                } catch (err) {
                    console.error(err);
                    rowCountEl.textContent = 'File Ready for Server Processing';
                    previewBox.classList.remove('hidden');
                    btnApply.disabled = false;
                }
            };
            reader.readAsArrayBuffer(file);
        }

        function submitExcelSyncForm(event) {
            const btn = document.getElementById('btnApplyExcelSync');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> सिंक हो रहा है...';
            return true;
        }

        // ==========================================
        // 🗑️ CLEAR ALL PRODUCTS MODAL SYSTEM
        // ==========================================
        function openClearAllModal() {
            const modal = document.getElementById('clearAllModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeClearAllModal() {
            const modal = document.getElementById('clearAllModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }
    </script>

    <!-- 🗑️ Clear All Products Confirmation Modal -->
    <div id="clearAllModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-4" onclick="closeClearAllModal()">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4 animate-in fade-in zoom-in duration-200" onclick="event.stopPropagation()">
            <div class="flex items-center gap-3">
                <div class="h-12 w-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-base font-black text-gray-900">पुराना कैटलॉग साफ़ करें?</h3>
                    <p class="text-xs text-gray-500">Clean Slate & Fresh Excel Upload</p>
                </div>
            </div>

            <p class="text-xs text-gray-600 leading-relaxed bg-rose-50/60 border border-rose-100 rounded-2xl p-3.5">
                यह एक्शन आपके सेलर स्टोर के वर्तमान <strong>{{ $productCardsCount ?? $productCount }} पुराने प्रोडक्ट कार्ड्स</strong> और उनके सभी साइज़ वेरिएंट्स को सुरक्षित रूप से हटा देगा। इसके बाद आप अपनी नई फाइनल एक्सेल शीट <strong>(295 आइटम्स)</strong> को बिल्कुल नए सिरे से फ्रेश अपलोड कर सकेंगे।
            </p>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeClearAllModal()" class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition">
                    रद्द करें (Cancel)
                </button>
                <form method="POST" action="{{ route('seller.products.clear_all') }}" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='साफ़ हो रहा है...';">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-black shadow-md shadow-rose-600/20 transition flex items-center gap-2">
                        <i class="fa-solid fa-trash-can"></i>
                        <span>हाँ, सब साफ़ करें (Clear All)</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
