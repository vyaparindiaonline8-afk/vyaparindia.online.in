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

        @if(session('duplicate_code_warnings') && count(session('duplicate_code_warnings')) > 0)
            <div class="p-4 rounded-2xl bg-amber-50/90 border border-amber-300 text-amber-950 text-xs space-y-2 shadow-2xs">
                <div class="font-black flex items-center gap-2 text-amber-900">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base shrink-0"></i>
                    <span>एक्सेल शीट में डुप्लीकेट कोड (SKU Warnings) मिले:</span>
                </div>
                <ul class="space-y-1 list-disc list-inside text-amber-900 text-[11px]">
                    @foreach(session('duplicate_code_warnings') as $dupWarn)
                        <li>{{ $dupWarn }}</li>
                    @endforeach
                </ul>
                <p class="text-[11px] text-amber-700 font-medium">💡 सलाह: अपनी एक्सेल शीट में एक ही कोड (ITAM CODE) दो अलग-अलग आइटम को न दें, हर आइटम का कोड अलग रखें।</p>
            </div>
        @endif

        @if(session('skipped_details') && count(session('skipped_details')) > 0)
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-300 text-slate-800 text-xs space-y-2 shadow-2xs">
                <div class="font-black flex items-center justify-between gap-2 text-slate-900">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-blue-600 text-base shrink-0"></i>
                        <span>छोड़ी गई पंक्तियों (Skipped Rows) का विवरण:</span>
                    </div>
                    <span class="text-[11px] font-bold text-slate-500 bg-white px-2 py-0.5 rounded-md border border-slate-200">कुल {{ count(session('skipped_details')) }} पंक्तियाँ</span>
                </div>
                <div class="max-h-48 overflow-y-auto space-y-1 text-slate-700 bg-white p-3 rounded-xl border border-slate-200">
                    @foreach(session('skipped_details') as $skipInfo)
                        <div class="text-[11px] flex items-start gap-1.5 py-1 border-b border-slate-100 last:border-0">
                            <span class="text-amber-600 shrink-0 font-bold">•</span>
                            <span>{{ $skipInfo }}</span>
                        </div>
                    @endforeach
                </div>
                <p class="text-[11px] text-slate-500 font-medium">💡 आप अपनी एक्सेल शीट में ऊपर दी गई पंक्तियों को सुधारकर दोबारा अपलोड कर सकते हैं।</p>
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
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-2xl p-4 sm:p-5 text-white shadow-md relative overflow-hidden border border-indigo-900/50">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 relative z-10">
                <!-- Left: Title & Compact Description -->
                <div class="space-y-1 max-w-md">
                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-black uppercase tracking-wider border border-emerald-500/30">
                        <i class="fa-solid fa-bolt text-emerald-400"></i>
                        <span>1-क्लिक एक्सेल सिंक & दैनिक भाव</span>
                    </div>
                    <h2 class="text-base sm:text-lg font-black text-white tracking-tight leading-snug">
                        एक्सेल शीट डाउनलोड व बल्क रेट अपडेट
                    </h2>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        दैनिक रेट्स बदलें या पूरे कैटलॉग को एक्सेल में डाउनलोड करें। सिस्टम <strong>Product ID</strong> से पहचान कर भाव तुरंत अपडेट कर देता है।
                    </p>
                </div>

                <!-- Right: Clean Organized Action Buttons -->
                <div class="flex flex-col sm:flex-row lg:flex-col xl:flex-row items-stretch sm:items-center gap-2 shrink-0">
                    <!-- Downloads Pill Group -->
                    <div class="flex items-center gap-1.5 bg-white/5 p-1 rounded-xl border border-white/10 backdrop-blur-xs">
                        <a href="{{ route('seller.products.export_sheet', ['type' => 'rates']) }}" class="px-2.5 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-bold text-[11px] transition flex items-center gap-1.5" title="दैनिक भाव बदलने के लिए शीट">
                            <i class="fa-solid fa-file-invoice-dollar text-amber-400"></i>
                            <span>दैनिक भाव (.csv)</span>
                        </a>
                        <a href="{{ route('seller.products.export_sheet', ['type' => 'full']) }}" class="px-2.5 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-bold text-[11px] transition flex items-center gap-1.5" title="पूरा कैटलॉग मास्टर शीट">
                            <i class="fa-solid fa-table-list text-emerald-400"></i>
                            <span>पूरा कैटलॉग (.csv)</span>
                        </a>
                    </div>

                    <!-- Sync & Tools Group -->
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <button type="button" onclick="openExcelSyncModal()" class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-black text-xs shadow-xs transition flex items-center gap-1.5 hover:scale-102 active:scale-98">
                            <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                            <span>1-क्लिक सिंक</span>
                        </button>

                        <a href="{{ route('seller.catalog.auditor') }}" class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-slate-950 font-black text-xs shadow-xs transition flex items-center gap-1.5 hover:scale-102 active:scale-98" title="एक्सेल शीट व लाइव कार्ड्स के रेट्स मिलाएं">
                            <i class="fa-solid fa-robot text-xs"></i>
                            <span>AI ऑडिटर 🤖</span>
                        </a>

                        <a href="{{ route('seller.catalog.upload') }}" class="px-2.5 py-1.5 rounded-xl bg-red-600/30 hover:bg-red-600/50 border border-red-400/30 text-white font-bold text-xs transition flex items-center gap-1.5 hover:scale-102 active:scale-98" title="PDF कैटलॉग अपलोड करें और सीधे लिस्ट बनाएं">
                            <i class="fa-solid fa-file-pdf text-red-400 text-xs"></i>
                            <span>PDF लिस्टिंग 📄</span>
                        </a>

                        <a href="{{ route('seller.catalog.excel_mapper') }}" class="px-2.5 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs transition flex items-center gap-1.5 hover:scale-102 active:scale-98" title="कॉलम मैपिंग टूल">
                            <i class="fa-solid fa-table-columns text-amber-300 text-xs"></i>
                            <span>मैपर ⚡</span>
                        </a>

                        <form action="{{ route('seller.catalog.excel_mapper.load_live_cards') }}" method="POST" class="inline" onsubmit="const btn = this.querySelector('button'); btn.disabled = true; btn.innerHTML = '<i class=\'fa-solid fa-spinner fa-spin\'></i> लोड...';">
                            @csrf
                            <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-purple-600/40 hover:bg-purple-600/60 border border-purple-400/30 text-white font-bold text-xs transition flex items-center gap-1.5 hover:scale-102 active:scale-98" title="लाइव कार्ड्स को एक्सेल शीट में खोलें">
                                <i class="fa-solid fa-file-pen text-amber-300 text-xs"></i>
                                <span>एडिटर 🔄</span>
                            </button>
                        </form>
                    </div>
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

        <!-- 🔍 Search, Categories & View Mode Switcher Bar -->
        <div class="bg-white rounded-3xl border border-gray-200 p-4 sm:p-5 shadow-xs space-y-4">
            <!-- Top Controls Row: Search Bar + View Mode Toggle -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <!-- Search Input Form -->
                <form action="{{ route('seller.products.index') }}" method="GET" class="flex-1 max-w-lg flex items-center gap-2">
                    @if(!empty($activeCategory))
                        <input type="hidden" name="category" value="{{ $activeCategory }}">
                    @endif
                    @if(!empty($activeFolder))
                        <input type="hidden" name="folder" value="{{ $activeFolder }}">
                    @endif
                    <div class="relative flex-1">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="liveSearchInput" name="search" value="{{ $search ?? '' }}" oninput="filterItemsClientSide()" placeholder="प्रोडक्ट का नाम, साइज़ या SKU खोजें (उदा: 90 Elbow, UPVC, 20mm)..." class="w-full pl-9 pr-8 py-2.5 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 text-xs font-semibold outline-hidden transition">
                        @if(!empty($search))
                            <a href="{{ route('seller.products.index', array_filter(['category' => $activeCategory, 'folder' => $activeFolder])) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs" title="Clear search">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        @endif
                    </div>
                    <button type="submit" class="px-4 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs transition shadow-xs">
                        <span>सर्च</span>
                    </button>
                </form>

                <!-- View Mode Switcher & Expand/Collapse All -->
                <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
                    <button type="button" onclick="toggleAllAccordions()" id="btnToggleAllVariants" class="px-3 py-2 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5" title="सभी साइज़ वेरिएंट्स एक साथ खोलें / बंद करें">
                        <i class="fa-solid fa-arrows-up-down text-[10px]"></i>
                        <span id="toggleAllVariantsText">सभी साइज़ खोलें</span>
                    </button>

                    <div class="bg-gray-100 p-1 rounded-2xl flex items-center border border-gray-200">
                        <button type="button" onclick="switchProductView('cards')" id="btnViewCards" class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition flex items-center gap-1.5 bg-white text-indigo-600 shadow-2xs">
                            <i class="fa-solid fa-grip text-xs"></i>
                            <span>कार्ड्स (Cards)</span>
                        </button>
                        <button type="button" onclick="switchProductView('table')" id="btnViewTable" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 text-gray-600 hover:text-gray-900">
                            <i class="fa-solid fa-list text-xs"></i>
                            <span>टेबल (Table)</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Category Pills / Tabs -->
            <div class="pt-2 border-t border-gray-100 flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                <span class="text-xs font-black text-gray-500 shrink-0 flex items-center gap-1.5 mr-1">
                    <i class="fa-solid fa-layer-group text-blue-600"></i>
                    <span>कैटेगरी:</span>
                </span>

                <!-- All Categories Tab -->
                <a href="{{ route('seller.products.index', array_filter(['folder' => $activeFolder, 'search' => $search])) }}" class="px-3 py-1.5 rounded-xl text-xs font-extrabold shrink-0 transition flex items-center gap-1.5 {{ empty($activeCategory) || $activeCategory === 'all' ? 'bg-blue-600 text-white shadow-xs' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' }}">
                    <span>सभी कैटेगरीज (All)</span>
                    <span class="px-1.5 py-0.2 rounded-full {{ empty($activeCategory) || $activeCategory === 'all' ? 'bg-white/25 text-white' : 'bg-gray-200 text-gray-700' }} text-[10px]">{{ $productCardsCount ?? $productCount }}</span>
                </a>

                @if(!empty($categories) && $categories->count() > 0)
                    @foreach($categories as $cat)
                        @php
                            $catName = is_object($cat) ? ($cat->name ?? '') : (string)$cat;
                            $catSlug = is_object($cat) ? ($cat->slug ?? $catName) : $catName;
                            $isActive = (!empty($activeCategory) && (strtolower($activeCategory) === strtolower($catSlug) || strtolower($activeCategory) === strtolower($catName)));
                            $catCount = is_object($cat) ? ($cat->products_count ?? null) : null;
                        @endphp
                        <a href="{{ route('seller.products.index', array_filter(['category' => $catSlug, 'folder' => $activeFolder, 'search' => $search])) }}" class="px-3 py-1.5 rounded-xl text-xs font-extrabold shrink-0 transition flex items-center gap-1.5 {{ $isActive ? 'bg-blue-600 text-white shadow-xs' : 'bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200' }}">
                            <span>{{ $catName }}</span>
                            @if(!is_null($catCount))
                                <span class="px-1.5 py-0.2 rounded-full {{ $isActive ? 'bg-white/25 text-white' : 'bg-blue-200/70 text-blue-900' }} text-[10px]">{{ $catCount }}</span>
                            @endif
                        </a>
                    @endforeach
                @endif
            </div>

            <!-- Quick Filter Chips: All / UPVC / CPVC / SWR / Reducers / Rates -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 pt-1 text-xs scrollbar-none">
                <span class="text-xs font-bold text-gray-400 shrink-0 mr-1">त्वरित फ़िल्टर:</span>
                <button type="button" onclick="setQuickTypeFilter('all', this)" class="quick-filter-btn px-2.5 py-1 rounded-lg text-xs font-extrabold bg-indigo-600 text-white shadow-xs">
                    सभी (All)
                </button>
                <button type="button" onclick="setQuickTypeFilter('upvc', this)" class="quick-filter-btn px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200">
                    <span class="inline-block w-2 h-2 rounded-full bg-blue-600 mr-1"></span> UPVC
                </button>
                <button type="button" onclick="setQuickTypeFilter('cpvc', this)" class="quick-filter-btn px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200">
                    <span class="inline-block w-2 h-2 rounded-full bg-amber-500 mr-1"></span> CPVC
                </button>
                <button type="button" onclick="setQuickTypeFilter('swr', this)" class="quick-filter-btn px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200">
                    <span class="inline-block w-2 h-2 rounded-full bg-slate-600 mr-1"></span> SWR
                </button>
                <button type="button" onclick="setQuickTypeFilter('reducer', this)" class="quick-filter-btn px-2.5 py-1 rounded-lg text-xs font-bold bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200">
                    <i class="fa-solid fa-shuffle mr-1 text-[10px]"></i> Reducers
                </button>
                <button type="button" onclick="setQuickTypeFilter('equal', this)" class="quick-filter-btn px-2.5 py-1 rounded-lg text-xs font-bold bg-teal-50 text-teal-700 hover:bg-teal-100 border border-teal-200">
                    <i class="fa-solid fa-cubes mr-1 text-[10px]"></i> Regular (Equal)
                </button>
                <button type="button" onclick="setQuickRateFilter('zero_rates', this)" class="quick-filter-btn px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-100 hover:bg-gray-200 text-gray-700">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500 mr-1"></i> बिना भाव वाले (₹0)
                </button>
            </div>
        </div>

        <!-- 🗂️ PRODUCT CARDS GRID VIEW (Visual & Category-Wise) -->
        <div id="productsCardGrid" class="space-y-6">
            @php
                $groupedProducts = $products->groupBy(function($p) {
                    return $p->category->name ?? ($p->group_name ?: 'अन्य प्रोडक्ट्स (General)');
                });
            @endphp

            @forelse($groupedProducts as $categoryTitle => $groupItems)
                <div class="category-block space-y-3 pt-2">
                    <!-- Category Header Banner -->
                    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-2xl px-4 py-3 flex items-center justify-between shadow-xs border border-indigo-900/40">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-xl bg-blue-500/20 text-blue-400 border border-blue-500/30 flex items-center justify-center text-sm shrink-0">
                                <i class="fa-solid fa-layer-group"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-white flex items-center gap-2">
                                    <span>{{ $categoryTitle }}</span>
                                </h3>
                                <p class="text-[11px] text-slate-300">
                                    {{ $groupItems->count() }} मुख्य प्रोडक्ट्स &bull; कुल {{ $groupItems->sum(function($p) { return ($p->variants && $p->variants->count() > 0) ? $p->variants->count() : 1; }) }} साइज़ वेरिएंट्स
                                </p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-xl bg-white/10 text-white font-black text-xs border border-white/10 shrink-0">
                            {{ $groupItems->count() }} Cards
                        </span>
                    </div>

                    <!-- Cards in this Category -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-5">
                        @foreach($groupItems as $product)
                    @php
                        $variants = $product->sorted_variants ?? ($product->variants ?? collect());
                        
                        // If product has real size variants, filter out dummy 'Standard' placeholder variants with no price
                        $hasRealSizes = $variants->contains(function($v) {
                            $n = strtolower(trim($v->variant_name ?? ''));
                            return !in_array($n, ['standard', 'std', 'default', '']);
                        });
                        $cleanVariants = $hasRealSizes
                            ? $variants->reject(function($v) {
                                $n = strtolower(trim($v->variant_name ?? ''));
                                return in_array($n, ['standard', 'std', 'default', '']) && (float)($v->retail_price ?: ($v->price ?: 0)) <= 0;
                            })
                            : $variants;

                        $varCount = $cleanVariants->count();
                        
                        // Extract all non-zero prices from variants (retail_price or price)
                        $vPrices = $cleanVariants->map(function($v) {
                            return (float)($v->retail_price ?: ($v->price ?: 0));
                        })->filter(function($p) { return $p > 0; });

                        $minPrice = $vPrices->isNotEmpty() ? $vPrices->min() : (float)$product->price;
                        $maxPrice = $vPrices->isNotEmpty() ? $vPrices->max() : (float)$product->price;
                        $hasPrice = ($minPrice > 0 || $maxPrice > 0 || (float)$product->price > 0);
                        $catName = $product->category->name ?? 'General';
                        
                        // Category colors
                        $catBadgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                        if (stripos($catName, 'UPVC') !== false) {
                            $catBadgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                        } elseif (stripos($catName, 'CPVC') !== false) {
                            $catBadgeClass = 'bg-amber-50 text-amber-800 border-amber-200';
                        } elseif (stripos($catName, 'SWR') !== false) {
                            $catBadgeClass = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                        } elseif (stripos($catName, 'PIPE') !== false) {
                            $catBadgeClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                        }
                        $variantSearchTokens = $cleanVariants->map(function($v) {
                            return strtolower(($v->variant_name ?: '') . ' ' . ($v->sku ?: ''));
                        })->join(' ');
                        $isCardReducer = str_contains(strtolower($product->name), 'reducer') || str_contains(strtolower($product->name), 'reduser') || $cleanVariants->contains(function($v) { return str_contains($v->variant_name ?? '', '*') || str_contains($v->variant_name ?? '', 'x'); });
                    @endphp
                    <div class="product-item-card bg-white rounded-3xl border border-gray-200 hover:border-indigo-300 hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col justify-between"
                         data-id="{{ $product->id }}"
                         data-name="{{ strtolower($product->name) }}"
                         data-sku="{{ strtolower($product->sku ?? '') }}"
                         data-category="{{ strtolower($catName) }}"
                         data-group="{{ strtolower($product->group_name ?? '') }}"
                         data-variants="{{ $variantSearchTokens }}"
                         data-is-reducer="{{ $isCardReducer ? '1' : '0' }}"
                         data-has-price="{{ $hasPrice ? '1' : '0' }}"
                         data-has-variants="{{ $varCount > 0 ? '1' : '0' }}">
                        
                        <div>
                            <!-- Card Header: Image + Badges + Checkbox -->
                            <div class="p-4 bg-gradient-to-b from-gray-50/80 to-white border-b border-gray-100 flex items-start gap-3">
                                <input type="checkbox" value="{{ $product->id }}" data-name="{{ addslashes($product->name) }}" data-folder="{{ $product->group_name }}" class="product-item-chk mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4 cursor-pointer" onchange="onProductSelectionChange()">
                                
                                <div class="relative group/img h-16 w-16 rounded-2xl bg-white border border-gray-200 overflow-hidden flex items-center justify-center shrink-0 p-1 shadow-2xs cursor-pointer hover:border-indigo-500 hover:ring-2 hover:ring-indigo-300 transition"
                                     id="product-card-img-box-{{ $product->id }}"
                                     onclick="openQuickImageModal({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->image_url ?? '' }}')"
                                     title="फोटो बदलें या गैलरी से चुनें">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" id="product-card-img-{{ $product->id }}" class="h-full w-full object-contain">
                                        <div id="product-card-img-placeholder-{{ $product->id }}" class="hidden flex-col items-center justify-center text-gray-300 group-hover/img:text-indigo-600 transition">
                                            <i class="fa-solid fa-camera text-xl"></i>
                                            <span class="text-[9px] font-bold mt-0.5 text-gray-400 group-hover/img:text-indigo-600">+फोटो</span>
                                        </div>
                                    @else
                                        <div id="product-card-img-placeholder-{{ $product->id }}" class="flex flex-col items-center justify-center text-gray-300 group-hover/img:text-indigo-600 transition">
                                            <i class="fa-solid fa-camera text-xl"></i>
                                            <span class="text-[9px] font-bold mt-0.5 text-gray-400 group-hover/img:text-indigo-600">+फोटो</span>
                                        </div>
                                        <img src="" alt="{{ $product->name }}" id="product-card-img-{{ $product->id }}" class="h-full w-full object-contain hidden">
                                    @endif
                                    <div class="absolute inset-0 bg-indigo-950/70 text-white flex flex-col items-center justify-center opacity-0 group-hover/img:opacity-100 transition rounded-2xl text-[10px] font-bold">
                                        <i class="fa-solid fa-images text-sm mb-0.5 text-amber-300"></i>
                                        <span>बदलें</span>
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0 space-y-1">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded-md border text-[10px] font-extrabold uppercase tracking-wide {{ $catBadgeClass }}">
                                            {{ $catName }}
                                        </span>
                                        @if($product->group_name)
                                            <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200 text-[10px] font-bold">
                                                <i class="fa-solid fa-folder text-[9px]"></i> {{ $product->group_name }}
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="font-extrabold text-sm text-gray-900 leading-snug line-clamp-2" title="{{ $product->name }}">
                                        {{ $product->name }}
                                    </h3>
                                    
                                    <div class="text-[10px] text-gray-400 font-mono">
                                        ID: #{{ $product->id }} &bull; SKU: {{ $product->sku ?? 'PRD-STD' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Card Middle: Price Summary & Badge -->
                            <div class="p-4 space-y-2">
                                <div class="flex items-baseline justify-between gap-2">
                                    <div>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">विक्रय मूल्य (Price A Range)</span>
                                        @if($hasPrice)
                                            <div class="text-base font-black text-gray-900">
                                                @if($minPrice == $maxPrice || $minPrice <= 0)
                                                    ₹{{ number_format($maxPrice ?: $minPrice, 2) }}
                                                @else
                                                    ₹{{ number_format($minPrice, 2) }} <span class="text-xs font-normal text-gray-400">-</span> ₹{{ number_format($maxPrice, 2) }}
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200 inline-block">
                                                ⚠️ कोई भाव नहीं (₹0.00)
                                            </div>
                                        @endif
                                    </div>

                                    <div class="text-right">
                                        @if($varCount > 0)
                                            <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 font-extrabold text-xs inline-flex items-center gap-1">
                                                <i class="fa-solid fa-cubes text-[10px]"></i>
                                                <span>{{ $varCount }} साइज़ उपलब्ध</span>
                                            </span>
                                        @else
                                            <span class="text-xs font-semibold text-gray-500">सिंगल आइटम</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Stock Status -->
                                <div class="flex items-center justify-between text-xs pt-1 border-t border-gray-100">
                                    <span class="text-gray-500 font-medium">स्टॉक स्थिति:</span>
                                    @if(!$product->track_inventory)
                                        <span class="text-gray-500 font-bold text-[11px]">On-Demand</span>
                                    @elseif($product->stock_quantity <= 0)
                                        <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200 font-bold text-[10px]">Out of Stock</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[10px]">{{ $product->stock_quantity }} उपलब्ध</span>
                                    @endif
                                </div>
                            </div>

                            <!-- 📦 DIRECTLY VISIBLE SIZES & RATES TABLE (Price A Focus) -->
                            @if($varCount > 0)
                                <div class="px-4 py-3 bg-slate-50/70 border-t border-gray-100 space-y-2">
                                    <div class="flex items-center justify-between text-[11px] font-black text-gray-700">
                                        <span class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-tags text-indigo-600"></i>
                                            <span>सभी साइज़ व भाव ({{ $varCount }} Sizes):</span>
                                        </span>
                                        <span class="text-[10px] text-emerald-700 font-extrabold uppercase">विक्रय भाव (Price A)</span>
                                    </div>

                                    <div class="max-h-56 overflow-y-auto space-y-1.5 pr-1 overscroll-contain">
                                        @foreach($cleanVariants as $v)
                                            @php
                                                $vRetail = (float)($v->retail_price ?: ($v->price ?: 0));
                                            @endphp
                                            <div class="p-2.5 rounded-xl bg-white border border-gray-200 flex items-center justify-between text-xs hover:border-indigo-300 transition shadow-2xs">
                                                <div class="space-y-0.5">
                                                    <div class="font-extrabold text-gray-900 flex items-center gap-1.5">
                                                        <span class="h-2 w-2 rounded-full {{ $vRetail > 0 ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                                        <span class="text-xs">{{ $v->variant_name }}</span>
                                                    </div>
                                                    <div class="text-[10px] text-gray-400 font-mono">
                                                        SKU: {{ $v->sku ?? 'STD' }} &bull; स्टॉक: <strong class="text-gray-700">{{ $v->stock_quantity ?? 0 }}</strong>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    @if($vRetail > 0)
                                                        <div class="font-black text-emerald-700 text-sm">₹{{ number_format($vRetail, 2) }}</div>
                                                        <span class="text-[9px] text-gray-400 font-semibold">Price A</span>
                                                    @else
                                                        <div class="font-bold text-amber-600 text-xs">⚠️ भाव नहीं (₹0)</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Card Footer: Actions Row -->
                        <div class="p-3 bg-white border-t border-gray-100 flex items-center justify-between gap-2">
                            <button type="button" onclick="openAssignFolderModalSingle({{ $product->id }}, '{{ addslashes($product->name) }}')" class="text-[11px] text-gray-500 hover:text-indigo-600 font-bold inline-flex items-center gap-1 transition">
                                <i class="fa-solid fa-folder-plus text-indigo-500"></i> फोल्डर
                            </button>

                            <div class="flex items-center gap-1.5">
                                <button type="button" onclick="openQuickImageModal({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->image_url ?? '' }}')" class="px-2.5 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs transition inline-flex items-center gap-1 shadow-2xs" title="फोटो बदलें या गैलरी खोलें">
                                    <i class="fa-solid fa-camera text-indigo-600 text-[10px]"></i>
                                    <span>फोटो</span>
                                </button>
                                <a href="{{ route('seller.products.edit', $product) }}" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition inline-flex items-center gap-1" title="Edit Product">
                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i> एडिट
                                </a>
                                <form id="delete-form-card-{{ $product->id }}" action="{{ route('seller.products.post_delete', $product->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="deleteProductAjax({{ $product->id }})" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition" title="Delete Product">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="col-span-full py-16 text-center text-gray-400 bg-white rounded-3xl border border-gray-200">
            <i class="fa-solid fa-box-open text-4xl mb-2 text-gray-300"></i>
            <p class="font-bold text-gray-600">कोई प्रोडक्ट नहीं मिला</p>
            <p class="text-xs mt-1">फ़िल्टर साफ़ करें या नया प्रोडक्ट जोड़ें।</p>
        </div>
    @endforelse
</div>

        <!-- 📋 PRODUCT TABLE VIEW (Expandable Rows) -->
        <div id="productsTableView" class="hidden bg-white rounded-3xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 font-bold border-b border-gray-200">
                            <th class="py-3 px-3 w-10 text-center">
                                <input type="checkbox" id="selectAllProductsChk" onchange="toggleSelectAllProducts(this.checked)" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4 cursor-pointer" title="Select All">
                            </th>
                            <th class="py-3 px-4">Item & Rate Folder</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Selling Price (Rate)</th>
                            <th class="py-3 px-4">Variants / Sizes</th>
                            <th class="py-3 px-4">Stock Status</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($products as $product)
                            @php
                                $variants = $product->sorted_variants ?? ($product->variants ?? collect());
                                $hasRealSizes = $variants->contains(function($v) {
                                    $n = strtolower(trim($v->variant_name ?? ''));
                                    return !in_array($n, ['standard', 'std', 'default', '']);
                                });
                                $cleanVariants = $hasRealSizes
                                    ? $variants->reject(function($v) {
                                        $n = strtolower(trim($v->variant_name ?? ''));
                                        return in_array($n, ['standard', 'std', 'default', '']) && (float)($v->retail_price ?: ($v->price ?: 0)) <= 0;
                                    })
                                    : $variants;

                                $varCount = $cleanVariants->count();
                                $vPrices = $cleanVariants->map(function($v) {
                                    return (float)($v->retail_price ?: ($v->price ?: 0));
                                })->filter(function($p) { return $p > 0; });

                                $minPrice = $vPrices->isNotEmpty() ? $vPrices->min() : (float)$product->price;
                                $maxPrice = $vPrices->isNotEmpty() ? $vPrices->max() : (float)$product->price;
                                $hasPrice = ($minPrice > 0 || $maxPrice > 0 || (float)$product->price > 0);
                                $catName = $product->category->name ?? 'General';
                                $variantSearchTokens = $cleanVariants->map(function($v) {
                                    return strtolower(($v->variant_name ?: '') . ' ' . ($v->sku ?: ''));
                                })->join(' ');
                                $isCardReducer = str_contains(strtolower($product->name), 'reducer') || str_contains(strtolower($product->name), 'reduser') || $cleanVariants->contains(function($v) { return str_contains($v->variant_name ?? '', '*') || str_contains($v->variant_name ?? '', 'x'); });
                            @endphp
                            <tr class="product-item-row hover:bg-gray-50/60 transition" id="row-prod-{{ $product->id }}"
                                data-id="{{ $product->id }}"
                                data-name="{{ strtolower($product->name) }}"
                                data-sku="{{ strtolower($product->sku ?? '') }}"
                                data-category="{{ strtolower($catName) }}"
                                data-group="{{ strtolower($product->group_name ?? '') }}"
                                data-variants="{{ $variantSearchTokens }}"
                                data-is-reducer="{{ $isCardReducer ? '1' : '0' }}"
                                data-has-price="{{ $hasPrice ? '1' : '0' }}"
                                data-has-variants="{{ $varCount > 0 ? '1' : '0' }}">
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
                                    {{ $catName }}
                                </td>
                                <td class="py-3 px-4 font-black text-gray-900">
                                    @if($hasPrice)
                                        @if($minPrice == $maxPrice || $minPrice <= 0)
                                            ₹{{ number_format($maxPrice ?: $minPrice, 2) }}
                                        @else
                                            ₹{{ number_format($minPrice, 2) }} - ₹{{ number_format($maxPrice, 2) }}
                                        @endif
                                    @else
                                        <span class="text-amber-600 text-xs font-bold">₹0.00</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($varCount > 0)
                                        <button type="button" onclick="toggleTableRowVariants({{ $product->id }})" class="px-2.5 py-1 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-extrabold text-[11px] border border-blue-200 inline-flex items-center gap-1.5 transition">
                                            <i class="fa-solid fa-cubes text-blue-600"></i>
                                            <span>{{ $varCount }} Variants</span>
                                            <i id="table-chevron-{{ $product->id }}" class="fa-solid fa-chevron-down text-[9px] transition-transform"></i>
                                        </button>
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
                                        <form id="delete-form-table-{{ $product->id }}" action="{{ route('seller.products.post_delete', $product->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="deleteProductAjax({{ $product->id }})" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition" title="Delete Product">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Expandable Sub-Row for Variants in Table View -->
                            @if($varCount > 0)
                                <tr id="table-variant-row-{{ $product->id }}" class="table-variant-row hidden bg-slate-50/80 border-b border-gray-200">
                                    <td colspan="7" class="py-3 px-6">
                                        <div class="bg-white rounded-2xl border border-blue-200 p-3 space-y-2 shadow-xs">
                                            <div class="flex items-center justify-between text-[11px] font-black text-blue-900 border-b border-gray-100 pb-2">
                                                <span>📦 "{{ $product->name }}" के सभी साइज़ वेरिएंट्स व भाव (Price A):</span>
                                                <span class="text-[10px] text-gray-400 font-normal">Excel Sync से अपडेटेड</span>
                                            </div>
                                            <div class="overflow-x-auto">
                                                <table class="w-full text-left text-xs">
                                                    <thead>
                                                        <tr class="text-[10px] font-black text-gray-500 uppercase tracking-wider border-b border-gray-100">
                                                            <th class="py-1.5 px-2">साइज़ (Variant)</th>
                                                            <th class="py-1.5 px-2">SKU</th>
                                                            <th class="py-1.5 px-2">विक्रय भाव (Price A)</th>
                                                            <th class="py-1.5 px-2">स्टॉक स्थिति</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-50">
                                                        @foreach($cleanVariants as $v)
                                                            @php
                                                                $vRetail = (float)($v->retail_price ?: ($v->price ?: 0));
                                                            @endphp
                                                            <tr class="hover:bg-blue-50/30">
                                                                <td class="py-1.5 px-2 font-extrabold text-gray-900">{{ $v->variant_name }}</td>
                                                                <td class="py-1.5 px-2 text-[10px] text-gray-400 font-mono">{{ $v->sku ?? '-' }}</td>
                                                                <td class="py-1.5 px-2 font-black text-emerald-700">{{ $vRetail > 0 ? '₹'.number_format($vRetail, 2) : '₹0.00' }}</td>
                                                                <td class="py-1.5 px-2">
                                                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold">
                                                                        {{ $v->stock_quantity ?? 0 }} in stock
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
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

                    <!-- Preview Box with Live Column Mapping Display -->
                    <div id="excelFilePreviewBox" class="hidden p-4 bg-emerald-50 rounded-2xl border border-emerald-300 space-y-3">
                        <div class="flex items-center justify-between text-xs font-bold text-emerald-950">
                            <span id="excelFileName" class="truncate max-w-[280px]">filename.xlsx</span>
                            <span id="excelRowCount" class="px-2.5 py-0.5 rounded-full bg-emerald-200 text-emerald-900 text-[10px] font-black">0 Rows Found</span>
                        </div>
                        
                        <!-- Column Mapping Inspection Area -->
                        <div class="bg-white rounded-xl p-3 border border-emerald-200 space-y-2 text-xs">
                            <div class="flex items-center justify-between font-black text-gray-900 text-[11px] border-b border-gray-100 pb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-wand-magic-sparkles text-emerald-600"></i>
                                    <span>पहचानी गई कॉलम मैपिंग (Auto-Detected Columns):</span>
                                </span>
                                <span class="text-[10px] text-gray-400 font-normal">Excel Sheet</span>
                            </div>
                            <div id="excelColumnMappingGrid" class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px]">
                                <!-- Dynamically populated by JS -->
                            </div>
                        </div>

                        <p class="text-[11px] text-emerald-800">
                            ✅ शीट लोड हो चुकी है! नीचे <strong>"सिंक व अपडेट करें"</strong> बटन दबाकर नए रेट्स तुरंत लागू करें।
                        </p>
                    </div>

                    <!-- Allow create new products option -->
                    <div class="flex items-start gap-2.5 p-3 rounded-2xl bg-amber-50/80 border border-amber-200 text-xs">
                        <input type="checkbox" id="allowCreateNewProductsChk" name="allow_create_new" value="1" checked class="mt-0.5 h-4 w-4 rounded border-amber-300 text-emerald-600 focus:ring-emerald-500">
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

        function inspectExcelHeaders(firstRow) {
            const grid = document.getElementById('excelColumnMappingGrid');
            if (!grid || !firstRow) return;
            grid.innerHTML = '';

            const keys = Object.keys(firstRow);
            const findKey = (patterns) => {
                for (const p of patterns) {
                    for (const k of keys) {
                        const clean = k.toLowerCase().replace(/[^a-z0-9]/g, '');
                        if (clean === p || clean.includes(p)) return k;
                    }
                }
                return null;
            };

            const mappings = [
                { field: 'प्रोडक्ट नाम (Name)', key: findKey(['productname', 'itemname', 'name', 'title', 'aaitmnaam']), required: true },
                { field: 'विक्रय भाव (Price A)', key: findKey(['salea', 'sellingprice', 'retailprice', 'price', 'ratea']), required: true },
                { field: 'SKU / कोड (Code)', key: findKey(['itamcode', 'itemcode', 'productcode', 'sku', 'code']), required: false },
                { field: 'कैटेगरी (Category)', key: findKey(['category', 'kaitaigarii']), required: false },
                { field: 'ग्रुप (Group)', key: findKey(['group', 'grup']), required: false },
                { field: 'साइज़ (Size / Variant)', key: findKey(['size', 'variant', 'dimension', 'saaiz']) || '(ऑटो-डिटेक्ट: mm, ml, inch, आदि)', required: false, auto: true },
                { field: 'खरीद भाव (Cost)', key: findKey(['pcost', 'cost', 'purchaseprice']), required: false },
                { field: 'ओपनिंग स्टॉक (Stock)', key: findKey(['opnstock', 'stock', 'currentstock']), required: false },
            ];

            mappings.forEach(m => {
                const item = document.createElement('div');
                item.className = 'flex items-center justify-between p-1.5 rounded-lg bg-gray-50 border border-gray-100';
                
                const left = document.createElement('span');
                left.className = 'font-bold text-gray-700';
                left.textContent = m.field;

                const right = document.createElement('span');
                if (m.key) {
                    right.className = m.auto 
                        ? 'px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 font-mono text-[10px] font-bold'
                        : 'px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-mono text-[10px] font-black';
                    right.textContent = m.key;
                } else {
                    right.className = m.required
                        ? 'px-2 py-0.5 rounded-md bg-rose-100 text-rose-800 font-semibold text-[10px]'
                        : 'px-2 py-0.5 rounded-md bg-gray-200 text-gray-500 font-semibold text-[10px]';
                    right.textContent = m.required ? '❌ नहीं मिला' : '—';
                }

                item.appendChild(left);
                item.appendChild(right);
                grid.appendChild(item);
            });
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

                    inspectExcelHeaders(jsonRows[0]);
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
        // 🗂️ / 📋 VIEW MODE SWITCHER & ACCORDIONS
        // ==========================================
        let currentRateFilter = 'all';

        function switchProductView(mode) {
            const cardGrid = document.getElementById('productsCardGrid');
            const tableView = document.getElementById('productsTableView');
            const btnCards = document.getElementById('btnViewCards');
            const btnTable = document.getElementById('btnViewTable');

            if (!cardGrid || !tableView) return;

            if (mode === 'table') {
                cardGrid.classList.add('hidden');
                tableView.classList.remove('hidden');

                if (btnTable) {
                    btnTable.classList.add('bg-white', 'text-indigo-600', 'shadow-2xs', 'font-extrabold');
                    btnTable.classList.remove('text-gray-600', 'font-bold');
                }
                if (btnCards) {
                    btnCards.classList.remove('bg-white', 'text-indigo-600', 'shadow-2xs', 'font-extrabold');
                    btnCards.classList.add('text-gray-600', 'font-bold');
                }
            } else {
                tableView.classList.add('hidden');
                cardGrid.classList.remove('hidden');

                if (btnCards) {
                    btnCards.classList.add('bg-white', 'text-indigo-600', 'shadow-2xs', 'font-extrabold');
                    btnCards.classList.remove('text-gray-600', 'font-bold');
                }
                if (btnTable) {
                    btnTable.classList.remove('bg-white', 'text-indigo-600', 'shadow-2xs', 'font-extrabold');
                    btnTable.classList.add('text-gray-600', 'font-bold');
                }
            }

            try {
                localStorage.setItem('vyapar_products_view_mode', mode);
            } catch (e) {}
        }

        function toggleCardVariants(productId) {
            const drawer = document.getElementById(`card-variants-drawer-${productId}`);
            const chevron = document.getElementById(`card-chevron-${productId}`);
            if (!drawer) return;

            const isHidden = drawer.classList.contains('hidden');
            if (isHidden) {
                drawer.classList.remove('hidden');
                if (chevron) chevron.classList.add('rotate-180');
            } else {
                drawer.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
            }
        }

        function toggleTableRowVariants(productId) {
            const row = document.getElementById(`table-variant-row-${productId}`);
            const chevron = document.getElementById(`table-chevron-${productId}`);
            if (!row) return;

            const isHidden = row.classList.contains('hidden');
            if (isHidden) {
                row.classList.remove('hidden');
                row.style.display = '';
                if (chevron) chevron.classList.add('rotate-180');
            } else {
                row.classList.add('hidden');
                row.style.display = 'none';
                if (chevron) chevron.classList.remove('rotate-180');
            }
        }

        let allAccordionsOpen = false;
        function toggleAllAccordions() {
            allAccordionsOpen = !allAccordionsOpen;
            const textEl = document.getElementById('toggleAllVariantsText');
            if (textEl) {
                textEl.textContent = allAccordionsOpen ? 'सभी साइज़ समेटें' : 'सभी साइज़ खोलें';
            }

            // Card drawers
            document.querySelectorAll('.card-variants-drawer').forEach(drawer => {
                if (allAccordionsOpen) drawer.classList.remove('hidden');
                else drawer.classList.add('hidden');
            });
            document.querySelectorAll('[id^="card-chevron-"]').forEach(ch => {
                if (allAccordionsOpen) ch.classList.add('rotate-180');
                else ch.classList.remove('rotate-180');
            });

            // Table rows
            document.querySelectorAll('.table-variant-row').forEach(row => {
                if (allAccordionsOpen) {
                    row.classList.remove('hidden');
                    row.style.display = '';
                } else {
                    row.classList.add('hidden');
                    row.style.display = 'none';
                }
            });
            document.querySelectorAll('[id^="table-chevron-"]').forEach(ch => {
                if (allAccordionsOpen) ch.classList.add('rotate-180');
                else ch.classList.remove('rotate-180');
            });
        }

        let currentTypeFilter = 'all';

        function setQuickTypeFilter(type, btn) {
            currentTypeFilter = type;
            document.querySelectorAll('.quick-filter-btn').forEach(b => {
                b.classList.remove('bg-indigo-600', 'text-white', 'shadow-xs');
            });
            if (btn) {
                btn.classList.add('bg-indigo-600', 'text-white', 'shadow-xs');
            }
            filterItemsClientSide();
        }

        function setQuickRateFilter(type, btn) {
            currentRateFilter = type;
            document.querySelectorAll('.quick-filter-btn').forEach(b => {
                b.classList.remove('bg-indigo-600', 'text-white', 'shadow-xs');
            });
            if (btn) {
                btn.classList.add('bg-indigo-600', 'text-white', 'shadow-xs');
            }
            filterItemsClientSide();
        }

        function filterItemsClientSide() {
            const query = (document.getElementById('liveSearchInput')?.value || '').toLowerCase().trim();

            const matchItem = (name, sku, category, group, variants, isReducer, hasPrice, hasVariants) => {
                // Pipe Type & Reducer filter
                if (currentTypeFilter === 'upvc') {
                    const text = `${name} ${sku} ${category} ${group}`;
                    if (!text.includes('upvc') && !sku.includes('uplasto')) return false;
                } else if (currentTypeFilter === 'cpvc') {
                    const text = `${name} ${sku} ${category} ${group}`;
                    if (!text.includes('cpvc') && !sku.includes('cpvc')) return false;
                } else if (currentTypeFilter === 'swr') {
                    const text = `${name} ${sku} ${category} ${group}`;
                    if (!text.includes('swr') && !sku.includes('swr')) return false;
                } else if (currentTypeFilter === 'reducer') {
                    if (isReducer !== '1') return false;
                } else if (currentTypeFilter === 'equal') {
                    if (isReducer === '1') return false;
                }

                // Rate filter
                if (currentRateFilter === 'with_rates' && hasPrice !== '1') return false;
                if (currentRateFilter === 'zero_rates' && hasPrice === '1') return false;
                if (currentRateFilter === 'has_variants' && hasVariants !== '1') return false;

                // Search query across ALL tokens (product name, sku, category, group, variant sizes, variant skus)
                if (query.length > 0) {
                    const combined = `${name} ${sku} ${category} ${group} ${variants}`;
                    if (!combined.includes(query)) return false;
                }
                return true;
            };

            // Filter cards
            document.querySelectorAll('.product-item-card').forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const sku = card.getAttribute('data-sku') || '';
                const category = card.getAttribute('data-category') || '';
                const group = card.getAttribute('data-group') || '';
                const variants = card.getAttribute('data-variants') || '';
                const isReducer = card.getAttribute('data-is-reducer') || '0';
                const hasPrice = card.getAttribute('data-has-price') || '0';
                const hasVariants = card.getAttribute('data-has-variants') || '0';

                const matches = matchItem(name, sku, category, group, variants, isReducer, hasPrice, hasVariants);
                card.style.display = matches ? '' : 'none';
            });

            // Filter table rows
            document.querySelectorAll('.product-item-row').forEach(row => {
                const id = row.getAttribute('data-id');
                const name = row.getAttribute('data-name') || '';
                const sku = row.getAttribute('data-sku') || '';
                const category = row.getAttribute('data-category') || '';
                const group = row.getAttribute('data-group') || '';
                const variants = row.getAttribute('data-variants') || '';
                const isReducer = row.getAttribute('data-is-reducer') || '0';
                const hasPrice = row.getAttribute('data-has-price') || '0';
                const hasVariants = row.getAttribute('data-has-variants') || '0';

                const matches = matchItem(name, sku, category, group, variants, isReducer, hasPrice, hasVariants);
                row.style.display = matches ? '' : 'none';
                
                // Hide sub-variant row if parent is hidden
                const subRow = document.getElementById(`table-variant-row-${id}`);
                if (subRow && !matches) {
                    subRow.style.display = 'none';
                } else if (subRow && matches && !subRow.classList.contains('hidden')) {
                    subRow.style.display = '';
                }
            });
        }

        // Initialize saved view mode on page load
        document.addEventListener('DOMContentLoaded', () => {
            let savedMode = 'cards';
            try {
                savedMode = localStorage.getItem('vyapar_products_view_mode') || 'cards';
            } catch (e) {}
            switchProductView(savedMode);
        });

        // ==========================================
        // 🗑️ INSTANT PRODUCT DELETION (AJAX & Fallback)
        // ==========================================
        function deleteProductAjax(productId) {
            if (!confirm('क्या आप इस प्रोडक्ट और इसके सभी साइज़ वेरिएंट्स को हटाना चाहते हैं?')) {
                return;
            }

            const card = document.querySelector(`.product-item-card[data-id="${productId}"]`);
            const row = document.getElementById(`row-prod-${productId}`);
            const subRow = document.getElementById(`table-variant-row-${productId}`);

            // Visually dim right away
            if (card) card.style.opacity = '0.4';
            if (row) row.style.opacity = '0.4';

            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('_method', 'DELETE');

            fetch(`/seller/products/${productId}/delete`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (card) {
                        card.style.transition = 'all 0.3s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        setTimeout(() => card.remove(), 300);
                    }
                    if (row) row.remove();
                    if (subRow) subRow.remove();
                } else {
                    alert(data.message || 'Error deleting product');
                    if (card) card.style.opacity = '1';
                    if (row) row.style.opacity = '1';
                }
            })
            .catch(err => {
                // Fallback: submit standard form if fetch encounters network error
                const form = document.getElementById(`delete-form-card-${productId}`) || document.getElementById(`delete-form-table-${productId}`);
                if (form) {
                    form.submit();
                } else {
                    window.location.reload();
                }
            });
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

        // ==========================================
        // 🖼️ QUICK IMAGE GALLERY & PICKER MODAL
        // ==========================================
        let currentModalProductId = null;
        let selectedGalleryImageUrl = null;
        let selectedUploadFile = null;

        function openQuickImageModal(productId, productName, currentImgUrl) {
            currentModalProductId = productId;
            selectedGalleryImageUrl = null;
            selectedUploadFile = null;

            const nameEl = document.getElementById('quickImageModalProductName');
            if (nameEl) nameEl.textContent = productName;

            // Reset selection highlights
            document.querySelectorAll('.gallery-photo-item').forEach(item => {
                item.classList.remove('border-indigo-600', 'ring-2', 'ring-indigo-300', 'bg-indigo-50/50');
                const overlay = item.querySelector('.photo-check-overlay');
                if (overlay) overlay.classList.add('hidden');
                item.classList.add('border-gray-200');
            });

            // If currentImgUrl matches an item in gallery, highlight it
            if (currentImgUrl) {
                const matchedItem = document.querySelector(`.gallery-photo-item[data-url="${currentImgUrl}"]`);
                if (matchedItem) {
                    matchedItem.classList.add('border-indigo-600', 'ring-2', 'ring-indigo-300', 'bg-indigo-50/50');
                    matchedItem.classList.remove('border-gray-200');
                    const overlay = matchedItem.querySelector('.photo-check-overlay');
                    if (overlay) overlay.classList.remove('hidden');
                    selectedGalleryImageUrl = currentImgUrl;
                }
            }

            // Reset file upload
            const fileInp = document.getElementById('modalUploadFileInput');
            if (fileInp) fileInp.value = '';
            const previewCont = document.getElementById('modalUploadPreviewContainer');
            if (previewCont) previewCont.classList.add('hidden');
            const previewBadge = document.getElementById('selectedImagePreviewBadge');
            if (previewBadge) {
                if (selectedGalleryImageUrl) {
                    previewBadge.classList.remove('hidden');
                    previewBadge.classList.add('flex');
                } else {
                    previewBadge.classList.add('hidden');
                    previewBadge.classList.remove('flex');
                }
            }

            switchImageModalTab('gallery');
            updateSaveButtonState();

            const modal = document.getElementById('quickImageModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeQuickImageModal() {
            const modal = document.getElementById('quickImageModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function switchImageModalTab(tab) {
            const galleryTab = document.getElementById('tabContentGallery');
            const uploadTab = document.getElementById('tabContentUpload');
            const btnGallery = document.getElementById('tabBtnGallery');
            const btnUpload = document.getElementById('tabBtnUpload');

            if (tab === 'gallery') {
                if (galleryTab) galleryTab.classList.remove('hidden');
                if (uploadTab) uploadTab.classList.add('hidden');
                if (btnGallery) {
                    btnGallery.className = 'px-4 py-2 rounded-xl text-xs font-black transition flex items-center gap-2 bg-indigo-600 text-white shadow-xs';
                }
                if (btnUpload) {
                    btnUpload.className = 'px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700';
                }
            } else {
                if (galleryTab) galleryTab.classList.add('hidden');
                if (uploadTab) uploadTab.classList.remove('hidden');
                if (btnUpload) {
                    btnUpload.className = 'px-4 py-2 rounded-xl text-xs font-black transition flex items-center gap-2 bg-indigo-600 text-white shadow-xs';
                }
                if (btnGallery) {
                    btnGallery.className = 'px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700';
                }
            }
        }

        function selectGalleryPhoto(imgUrl, el) {
            document.querySelectorAll('.gallery-photo-item').forEach(item => {
                item.classList.remove('border-indigo-600', 'ring-2', 'ring-indigo-300', 'bg-indigo-50/50');
                const overlay = item.querySelector('.photo-check-overlay');
                if (overlay) overlay.classList.add('hidden');
                item.classList.add('border-gray-200');
            });

            if (el) {
                el.classList.add('border-indigo-600', 'ring-2', 'ring-indigo-300', 'bg-indigo-50/50');
                el.classList.remove('border-gray-200');
                const overlay = el.querySelector('.photo-check-overlay');
                if (overlay) overlay.classList.remove('hidden');
            }

            selectedGalleryImageUrl = imgUrl;
            selectedUploadFile = null;

            const previewBadge = document.getElementById('selectedImagePreviewBadge');
            if (previewBadge) {
                previewBadge.classList.remove('hidden');
                previewBadge.classList.add('flex');
            }

            updateSaveButtonState();
        }

        function previewUploadFile(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                selectedUploadFile = file;
                selectedGalleryImageUrl = null;

                const nameEl = document.getElementById('modalUploadFileName');
                if (nameEl) nameEl.textContent = file.name;

                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgEl = document.getElementById('modalUploadPreviewImg');
                    if (imgEl) imgEl.src = e.target.result;
                    const previewCont = document.getElementById('modalUploadPreviewContainer');
                    if (previewCont) previewCont.classList.remove('hidden');
                };
                reader.readAsDataURL(file);

                const previewBadge = document.getElementById('selectedImagePreviewBadge');
                if (previewBadge) {
                    previewBadge.classList.remove('hidden');
                    previewBadge.classList.add('flex');
                }

                updateSaveButtonState();
            }
        }

        function updateSaveButtonState() {
            const btn = document.getElementById('btnSaveQuickImage');
            if (!btn) return;
            if (selectedGalleryImageUrl || selectedUploadFile) {
                btn.removeAttribute('disabled');
            } else {
                btn.setAttribute('disabled', 'disabled');
            }
        }

        function filterGalleryPhotos(searchVal) {
            const q = (searchVal || '').toLowerCase().trim();
            const items = document.querySelectorAll('.gallery-photo-item');
            items.forEach(it => {
                const name = it.getAttribute('data-name') || '';
                const grp = it.getAttribute('data-group') || '';
                if (!q || name.includes(q) || grp.includes(q)) {
                    it.classList.remove('hidden');
                } else {
                    it.classList.add('hidden');
                }
            });
        }

        function filterGalleryByTag(tag) {
            const input = document.getElementById('gallerySearchInput');
            if (input) input.value = tag;
            filterGalleryPhotos(tag);
        }

        function saveQuickImage() {
            if (!currentModalProductId || (!selectedGalleryImageUrl && !selectedUploadFile)) {
                return;
            }

            const btn = document.getElementById('btnSaveQuickImage');
            const btnText = document.getElementById('btnSaveQuickImageText');
            if (btn) btn.disabled = true;
            if (btnText) btnText.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> सेव हो रहा है...';

            const formData = new FormData();
            formData.append('_token', csrfToken);
            if (selectedGalleryImageUrl) {
                formData.append('image_url', selectedGalleryImageUrl);
            }
            if (selectedUploadFile) {
                formData.append('image_file', selectedUploadFile);
            }

            fetch(`/seller/products/${currentModalProductId}/quick-image-update`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.image_url) {
                    // 1. Update Card image
                    const cardImg = document.getElementById(`product-card-img-${currentModalProductId}`);
                    const cardPlaceholder = document.getElementById(`product-card-img-placeholder-${currentModalProductId}`);
                    if (cardImg) {
                        cardImg.src = data.image_url;
                        cardImg.classList.remove('hidden');
                    }
                    if (cardPlaceholder) {
                        cardPlaceholder.classList.add('hidden');
                    }

                    // 2. Update Table row image
                    const tableImg = document.getElementById(`product-table-img-${currentModalProductId}`);
                    const tablePlaceholder = document.getElementById(`product-table-placeholder-${currentModalProductId}`);
                    if (tableImg) {
                        tableImg.src = data.image_url;
                        tableImg.classList.remove('hidden');
                    }
                    if (tablePlaceholder) {
                        tablePlaceholder.classList.add('hidden');
                    }

                    // 3. Show instant floating Toast
                    showToastNotification('✅ फोटो सफलतापूर्वक अपडेट हो गई!');
                    closeQuickImageModal();
                } else {
                    alert(data.message || 'फोटो अपडेट नहीं हो सकी। कृपया दोबारा प्रयास करें।');
                }
            })
            .catch(err => {
                alert('नेटवर्क एरर। कृपया दोबारा प्रयास करें।');
            })
            .finally(() => {
                if (btn) btn.disabled = false;
                if (btnText) btnText.innerHTML = '<i class="fa-solid fa-check mr-1"></i> फोटो सेट करें (Save Photo)';
            });
        }

        function showToastNotification(msg) {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-5 right-5 z-50 bg-slate-900 text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-2 text-xs font-bold border border-slate-700 animate-in fade-in slide-in-from-bottom duration-300';
            toast.innerHTML = `<i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i> <span>${msg}</span>`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.5s ease';
                setTimeout(() => toast.remove(), 500);
            }, 3000);
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

    <!-- 🖼️ Interactive Product Photo Gallery & Picker Modal -->
    <div id="quickImageModal" class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-5" onclick="closeQuickImageModal()">
        <div class="bg-white rounded-3xl max-w-4xl w-full max-h-[90vh] shadow-2xl border border-gray-100 flex flex-col overflow-hidden animate-in fade-in zoom-in duration-200" onclick="event.stopPropagation()">
            
            <!-- Modal Header -->
            <div class="p-4 sm:p-5 border-b border-gray-100 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-images"></i>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-black text-white flex items-center gap-2">
                            <span>प्रोडक्ट फोटो गैलरी (Product Image Picker)</span>
                        </h3>
                        <p class="text-[11px] text-slate-300 truncate max-w-xs sm:max-w-md" id="quickImageModalProductName">
                            Select photo for product
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeQuickImageModal()" class="h-8 w-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Modal Body (Tabs + Content) -->
            <div class="p-4 sm:p-5 overflow-y-auto space-y-4 flex-1">
                
                <!-- Mode Switcher Tabs -->
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 gap-2">
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="switchImageModalTab('gallery')" id="tabBtnGallery" class="px-4 py-2 rounded-xl text-xs font-black transition flex items-center gap-2 bg-indigo-600 text-white shadow-xs">
                            <i class="fa-solid fa-photo-film"></i>
                            <span>कैटलॉग गैलरी ({{ count($galleryImages ?? []) }} उपलब्ध फोटोज़)</span>
                        </button>
                        <button type="button" onclick="switchImageModalTab('upload')" id="tabBtnUpload" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700">
                            <i class="fa-solid fa-cloud-arrow-up text-indigo-600"></i>
                            <span>नया फोटो अपलोड करें (Upload New)</span>
                        </button>
                    </div>

                    <div id="selectedImagePreviewBadge" class="hidden items-center gap-2 text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-xl border border-emerald-200">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>फोटो चुनी गई</span>
                    </div>
                </div>

                <!-- Tab 1: Existing Catalog Gallery -->
                <div id="tabContentGallery" class="space-y-3">
                    <!-- Search & Filters -->
                    <div class="flex flex-col sm:flex-row gap-2 items-center justify-between">
                        <div class="relative w-full sm:w-72">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text" id="gallerySearchInput" oninput="filterGalleryPhotos(this.value)" placeholder="गैलरी में खोजें (Elbow, Tee, Pipe, Socket, Brass...)" class="w-full pl-8 pr-3 py-2 rounded-xl border border-gray-200 text-xs bg-gray-50 focus:bg-white focus:border-indigo-500 focus:outline-hidden">
                        </div>
                        <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0 text-[11px] font-bold text-gray-600">
                            <span class="text-gray-400 shrink-0">फिल्टर:</span>
                            <button type="button" onclick="filterGalleryByTag('')" class="gallery-tag-btn px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200">सब</button>
                            <button type="button" onclick="filterGalleryByTag('upvc')" class="gallery-tag-btn px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200">UPVC</button>
                            <button type="button" onclick="filterGalleryByTag('cpvc')" class="gallery-tag-btn px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200">CPVC</button>
                            <button type="button" onclick="filterGalleryByTag('swr')" class="gallery-tag-btn px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200">SWR</button>
                            <button type="button" onclick="filterGalleryByTag('elbow')" class="gallery-tag-btn px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200">Elbow</button>
                            <button type="button" onclick="filterGalleryByTag('tee')" class="gallery-tag-btn px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200">Tee</button>
                            <button type="button" onclick="filterGalleryByTag('pipe')" class="gallery-tag-btn px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200">Pipe</button>
                            <button type="button" onclick="filterGalleryByTag('brass')" class="gallery-tag-btn px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200">Brass</button>
                        </div>
                    </div>

                    <!-- Photos Grid -->
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3 max-h-[50vh] overflow-y-auto p-1" id="galleryPhotosGrid">
                        @forelse($galleryImages ?? [] as $gImg)
                            @php
                                $imgUrl = is_array($gImg) ? ($gImg['asset_url'] ?? ($gImg['url'] ?? ($gImg['image'] ?? ''))) : ($gImg->image ?? ($gImg->url ?? ''));
                                $imgName = is_array($gImg) ? ($gImg['name'] ?? ($gImg['filename'] ?? 'Item')) : ($gImg->name ?? 'Item');
                                $imgGroup = is_array($gImg) ? ($gImg['folder'] ?? ($gImg['category'] ?? '')) : ($gImg->group_name ?? '');
                            @endphp
                            <div class="gallery-photo-item group relative rounded-2xl border-2 border-gray-200 hover:border-indigo-500 bg-white p-1.5 cursor-pointer transition shadow-2xs hover:shadow-md flex flex-col items-center justify-between"
                                 data-url="{{ $imgUrl }}"
                                 data-name="{{ strtolower($imgName) }}"
                                 data-group="{{ strtolower($imgGroup) }}"
                                 onclick="selectGalleryPhoto('{{ $imgUrl }}', this)">
                                
                                <div class="h-20 w-full rounded-xl overflow-hidden bg-gray-50 flex items-center justify-center p-1">
                                    <img src="{{ $imgUrl }}" alt="{{ $imgName }}" class="h-full w-full object-contain group-hover:scale-105 transition" loading="lazy">
                                </div>

                                <span class="text-[10px] font-extrabold text-gray-700 line-clamp-1 mt-1 text-center w-full" title="{{ $imgName }}">
                                    {{ $imgName }}
                                </span>

                                <!-- Selected Checkmark Overlay -->
                                <div class="photo-check-overlay hidden absolute top-1 right-1 h-5 w-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] shadow-sm">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-12 text-center text-gray-400">
                                <i class="fa-solid fa-images text-3xl mb-2 text-gray-300"></i>
                                <p class="text-xs font-bold text-gray-500">कोई गैलरी इमेज उपलब्ध नहीं है</p>
                                <p class="text-[11px] text-gray-400 mt-1">नई फोटो अपलोड करने के लिए "नया फोटो अपलोड करें" टैब चुनें।</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Tab 2: Upload New File -->
                <div id="tabContentUpload" class="hidden space-y-4">
                    <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6 text-center hover:border-indigo-500 transition bg-slate-50/50">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-indigo-600 mb-2"></i>
                        <p class="text-xs font-bold text-gray-700">फ़ोन की गैलरी या कंप्यूटर से फोटो चुनें</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">JPG, PNG, WebP (अधिकतम 4MB)</p>
                        
                        <input type="file" id="modalUploadFileInput" accept="image/*" class="hidden" onchange="previewUploadFile(this)">
                        <button type="button" onclick="document.getElementById('modalUploadFileInput').click()" class="mt-3 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition">
                            <i class="fa-solid fa-folder-open mr-1"></i> फ़ाइल ब्राउज़ करें
                        </button>
                    </div>

                    <div id="modalUploadPreviewContainer" class="hidden p-3 rounded-2xl bg-white border border-gray-200 flex items-center gap-3">
                        <img id="modalUploadPreviewImg" src="" class="h-16 w-16 object-contain rounded-xl border border-gray-200 bg-gray-50">
                        <div>
                            <p class="text-xs font-bold text-gray-800" id="modalUploadFileName">Selected file</p>
                            <span class="text-[10px] text-emerald-600 font-bold">✨ अपलोड के लिए तैयार</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="p-4 sm:p-5 border-t border-gray-100 bg-gray-50 flex items-center justify-between gap-3">
                <button type="button" onclick="closeQuickImageModal()" class="px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs font-bold hover:bg-gray-100 transition">
                    रद्द करें (Cancel)
                </button>

                <button type="button" id="btnSaveQuickImage" onclick="saveQuickImage()" disabled class="px-6 py-2.5 rounded-xl bg-indigo-600 disabled:bg-gray-300 disabled:cursor-not-allowed hover:bg-indigo-700 text-white text-xs font-black shadow-md shadow-indigo-600/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    <span id="btnSaveQuickImageText">फोटो सेट करें (Save Photo)</span>
                </button>
            </div>

        </div>
    </div>
</body>
</html>
