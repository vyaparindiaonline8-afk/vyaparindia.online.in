<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($minisite) ? 'Customize Mini-Storefront' : 'Create Mini-Storefront' }} - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800 antialiased min-h-screen">

    <!-- Top Seller Nav -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="{{ route('seller.dashboard') }}" class="text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-lg text-gray-900 leading-tight">Mini-Website Storefront Builder</h1>
                        <p class="text-xs text-gray-500">Design your standalone D2C ecommerce storefront</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @if(isset($minisite) && $minisite->slug)
                        <a href="{{ route('minisite.show', $minisite->slug) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 font-bold text-xs transition-colors shadow-xs">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            <span>View Live Storefront</span>
                        </a>
                    @endif
                    <a href="{{ route('seller.dashboard') }}" class="px-3.5 py-2 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 font-semibold text-xs transition-colors">
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success Alert -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
                @if(isset($minisite) && $minisite->slug)
                    <a href="{{ route('minisite.show', $minisite->slug) }}" target="_blank" class="underline font-bold">
                        Open Store &rarr;
                    </a>
                @endif
            </div>
        @endif

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-xs space-y-1">
                <div class="font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Please fix the following issues:</span>
                </div>
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ isset($minisite) ? route('seller.minisite.update') : route('seller.minisite.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @if(isset($minisite))
                @method('PUT')
            @endif

            <!-- 1. Store Identity & URL -->
            <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-xs space-y-6">
                <div class="flex items-center gap-2.5 pb-4 border-b border-gray-100">
                    <div class="h-8 w-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                        1
                    </div>
                    <div>
                        <h2 class="font-bold text-base text-gray-900">Store Identity & Custom Web Address</h2>
                        <p class="text-xs text-gray-500">Your store's name, public URL, and branding slogan</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            Store / Business Name (दुकान का मुख्य नाम) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="store_title_input" name="page_title" value="{{ old('page_title', $minisite->page_title ?? '') }}" required placeholder="e.g. Ganesh Sanitaryware & Tiles" class="w-full px-4 py-2.5 text-sm font-bold bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Store URL Slug (वेबसाइट लिंक) <span class="text-red-500">*</span></label>
                        <div class="flex items-center">
                            <span class="px-3 py-2.5 text-xs bg-gray-200 border border-r-0 border-gray-300 rounded-l-xl text-gray-500 font-mono">/</span>
                            <input type="text" name="slug" value="{{ old('slug', $minisite->slug ?? '') }}" required placeholder="ganesh-sanitary" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-r-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono">
                        </div>
                    </div>

                    <!-- Business Category / Shop Type -->
                    <div class="sm:col-span-2 bg-slate-50 border border-slate-200 rounded-2xl p-4">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-black text-slate-900">
                                <i class="fa-solid fa-store text-indigo-600 mr-1"></i> Business Category / किस चीज़ की दुकान है?
                            </label>
                            <span class="text-[10px] text-slate-500 font-semibold">Click to quick-fill</span>
                        </div>
                        <input type="text" id="business_cat_input" name="business_category_custom" value="{{ old('business_category_custom', $minisite->business_category_custom ?? '') }}" placeholder="e.g. Sanitaryware, Tiles & Bathroom Fittings" class="w-full px-4 py-2 text-xs font-bold bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none mb-2">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <button type="button" onclick="setBusinessCat('Sanitaryware, Bath Fittings & Tiles')" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:border-indigo-500 hover:text-indigo-600 text-[11px] font-bold text-slate-700 transition shadow-2xs">🚽 Sanitaryware & Tiles</button>
                            <button type="button" onclick="setBusinessCat('Hardware, CPVC Pipes & Paint Store')" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:border-indigo-500 hover:text-indigo-600 text-[11px] font-bold text-slate-700 transition shadow-2xs">🔧 Hardware & Pipes</button>
                            <button type="button" onclick="setBusinessCat('Fashion, Clothing & Saree Showroom')" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:border-indigo-500 hover:text-indigo-600 text-[11px] font-bold text-slate-700 transition shadow-2xs">👗 Fashion & Cloth Store</button>
                            <button type="button" onclick="setBusinessCat('Electricals, Fans & LED Lighting')" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:border-indigo-500 hover:text-indigo-600 text-[11px] font-bold text-slate-700 transition shadow-2xs">⚡ Electricals & Lights</button>
                            <button type="button" onclick="setBusinessCat('Building Materials, Cement & TMT Steel')" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:border-indigo-500 hover:text-indigo-600 text-[11px] font-bold text-slate-700 transition shadow-2xs">🏗️ Cement & TMT Steel</button>
                        </div>
                    </div>

                    <!-- Authorized Dealerships / Distributorships -->
                    <div class="sm:col-span-2 bg-blue-50/60 border border-blue-200 rounded-2xl p-4">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-black text-blue-900">
                                <i class="fa-solid fa-certificate text-blue-600 mr-1"></i> Authorized Dealership / Distributor of (किन कंपनियों के अधिकृत डीलर हैं?)
                            </label>
                            <span class="text-[10px] text-blue-600 font-semibold">Add brand chips</span>
                        </div>
                        <input type="text" id="dealership_input" name="dealership_text" value="{{ old('dealership_text', $minisite->dealership_text ?? '') }}" placeholder="e.g. Authorized Distributor of Cava Sanitaryware, Cera, Plasto & Jaquar" class="w-full px-4 py-2 text-xs font-bold bg-white border border-blue-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none mb-2">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <button type="button" onclick="appendDealership('Cava Sanitaryware')" class="px-2.5 py-1 rounded-lg bg-white border border-blue-200 hover:border-blue-500 text-blue-700 text-[11px] font-bold transition shadow-2xs">+ Cava Sanitary</button>
                            <button type="button" onclick="appendDealership('Cera')" class="px-2.5 py-1 rounded-lg bg-white border border-blue-200 hover:border-blue-500 text-blue-700 text-[11px] font-bold transition shadow-2xs">+ Cera</button>
                            <button type="button" onclick="appendDealership('Jaquar')" class="px-2.5 py-1 rounded-lg bg-white border border-blue-200 hover:border-blue-500 text-blue-700 text-[11px] font-bold transition shadow-2xs">+ Jaquar</button>
                            <button type="button" onclick="appendDealership('Plasto Pipes')" class="px-2.5 py-1 rounded-lg bg-white border border-blue-200 hover:border-blue-500 text-blue-700 text-[11px] font-bold transition shadow-2xs">+ Plasto Pipes</button>
                            <button type="button" onclick="appendDealership('Supreme')" class="px-2.5 py-1 rounded-lg bg-white border border-blue-200 hover:border-blue-500 text-blue-700 text-[11px] font-bold transition shadow-2xs">+ Supreme</button>
                            <button type="button" onclick="appendDealership('Ashirvad')" class="px-2.5 py-1 rounded-lg bg-white border border-blue-200 hover:border-blue-500 text-blue-700 text-[11px] font-bold transition shadow-2xs">+ Ashirvad</button>
                            <button type="button" onclick="appendDealership('Kajaria Tiles')" class="px-2.5 py-1 rounded-lg bg-white border border-blue-200 hover:border-blue-500 text-blue-700 text-[11px] font-bold transition shadow-2xs">+ Kajaria</button>
                        </div>
                    </div>

                    <!-- Tagline with 1-Click Suggestions -->
                    <div class="sm:col-span-2">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-gray-700">Tagline / Subheading (दुकान का मुख्य स्लोगन)</label>
                            <span class="text-[10px] text-gray-400 font-bold">Click chip to apply</span>
                        </div>
                        <input type="text" id="tagline_input" name="tagline" value="{{ old('tagline', $minisite->tagline ?? '') }}" placeholder="e.g. Authorized Dealer of Cava Sanitaryware & Fittings" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none mb-2">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <button type="button" onclick="setTagline('Authorized Dealer of Cava Sanitaryware & Luxury Bath Fittings')" class="px-2 py-0.5 rounded-md bg-gray-100 hover:bg-indigo-50 hover:text-indigo-700 text-[10px] font-bold text-gray-600 transition">💡 Cava Sanitaryware Dealer</button>
                            <button type="button" onclick="setTagline('Complete Range of One-Piece Commode, Basin & Tiles at Wholesale Rates')" class="px-2 py-0.5 rounded-md bg-gray-100 hover:bg-indigo-50 hover:text-indigo-700 text-[10px] font-bold text-gray-600 transition">💡 Wholesale Commode & Basin</button>
                            <button type="button" onclick="setTagline('Direct Factory Wholesale Distributor with Prompt Site Delivery')" class="px-2 py-0.5 rounded-md bg-gray-100 hover:bg-indigo-50 hover:text-indigo-700 text-[10px] font-bold text-gray-600 transition">💡 Direct Site Delivery</button>
                            <button type="button" onclick="setTagline('100% Genuine Branded Quality at Lowest Market Price')" class="px-2 py-0.5 rounded-md bg-gray-100 hover:bg-indigo-50 hover:text-indigo-700 text-[10px] font-bold text-gray-600 transition">💡 100% Genuine Guarantee</button>
                        </div>
                    </div>

                    <!-- Welcome Message + AI Button -->
                    <div class="sm:col-span-2">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-gray-700">Welcome Message (Hero Banner Greeting)</label>
                            <button type="button" onclick="generateAiStory()" class="inline-flex items-center gap-1 text-[11px] font-black text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded-lg transition shadow-2xs">
                                <i class="fa-solid fa-wand-magic-sparkles text-amber-500"></i>
                                <span>✨ AI से विवरण लिखवाएं (1-Click Auto Fill)</span>
                            </button>
                        </div>
                        <textarea id="welcome_msg_input" name="welcome_message" rows="2" placeholder="Welcome buyers with a compelling greeting or discount notice" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('welcome_message', $minisite->welcome_message ?? '') }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">About Our Store (हमारी दुकान के बारे में - Footer / Story)</label>
                        <textarea id="about_text_input" name="about_text" rows="3" placeholder="Explain your history, experience, genuine guarantees, and wholesale terms" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('about_text', $minisite->about_text ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 2. Business Category & Industry Theme Preset -->
            <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-xs space-y-6">
                <div class="flex items-center gap-2.5 pb-4 border-b border-gray-100">
                    <div class="h-8 w-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                        2
                    </div>
                    <div>
                        <h2 class="font-bold text-base text-gray-900">Business Category & Storefront Theme Preset</h2>
                        <p class="text-xs text-gray-500">Choose your industry to automatically activate specialized product cards, attribute matrices, and custom ordering formats</p>
                    </div>
                </div>

                @php
                    $selectedBusinessType = old('business_type', $minisite->business_type ?? 'hardware_pipes');
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="businessModuleGrid">
                    @foreach($businessModules as $id => $module)
                        @php
                            $isSelected = ($selectedBusinessType === $id);
                        @endphp
                        <label class="cursor-pointer relative block p-5 rounded-2xl border-2 transition-all duration-200 module-card {{ $isSelected ? 'border-blue-600 bg-blue-50/40 shadow-xs ring-2 ring-blue-500/20' : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50/50' }}">
                            <input type="radio" name="business_type" value="{{ $id }}" class="sr-only module-radio" {{ $isSelected ? 'checked' : '' }} onchange="highlightSelectedModule(this)">
                            
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="h-10 w-10 rounded-xl flex items-center justify-center text-lg {{ $isSelected ? 'bg-blue-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700' }} module-icon-box">
                                    <i class="fa-solid {{ $module->getIcon() }}"></i>
                                </div>
                                <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full {{ $isSelected ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600' }} module-badge">
                                    {{ $module->getThemeBadge() }}
                                </span>
                            </div>

                            <div class="font-bold text-sm text-gray-900 mb-1 flex items-center justify-between">
                                <span>{{ $module->getName() }}</span>
                                <span class="check-indicator {{ $isSelected ? 'text-blue-600' : 'text-gray-300' }}">
                                    <i class="fa-solid fa-circle-check text-base"></i>
                                </span>
                            </div>
                            <p class="text-[11px] text-gray-500 mb-3 leading-relaxed">
                                {{ $module->getSubtitle() }}
                            </p>

                            <!-- Industry Attributes Highlights -->
                            <div class="pt-3 border-t border-gray-100 space-y-1 text-[11px] text-gray-600">
                                @if($id === 'hardware_pipes')
                                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-check text-blue-500 text-[10px]"></i> Size Matrix (mm / inch) & Class</div>
                                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-check text-blue-500 text-[10px]"></i> List Price, Discounts & Packing</div>
                                @elseif($id === 'fashion_lifestyle')
                                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-check text-rose-500 text-[10px]"></i> S, M, L, XL, XXL Size Chips</div>
                                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-check text-rose-500 text-[10px]"></i> Color Swatches & 4:5 Portrait Lookbook</div>
                                @elseif($id === 'food_dining')
                                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-check text-emerald-500 text-[10px]"></i> 🟢 Veg / 🔴 Non-Veg Status</div>
                                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-check text-amber-500 text-[10px]"></i> Spice Level Rating & Prep Time</div>
                                @elseif($id === 'anaj_mandi')
                                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-check text-yellow-600 text-[10px]"></i> Per Quintal / Bori / Katta Rates</div>
                                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-check text-yellow-600 text-[10px]"></i> Live Mandi Bhav Ticker & Lot Sauda</div>
                                @elseif($id === 'grocery_fmcg')
                                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-check text-teal-600 text-[10px]"></i> 100g, 250g, 500g, 1kg Pack Sizes</div>
                                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-check text-teal-600 text-[10px]"></i> 1-Tap Quick Quantity Stepper (+/-)</div>
                                @elseif($id === 'iron_cement')
                                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-check text-orange-600 text-[10px]"></i> TMT 8mm-25mm Rod Weight & Per-Kg Rate</div>
                                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-check text-orange-600 text-[10px]"></i> 50Kg Cement Bag & Contractor Bulk Quote</div>
                                @elseif($id === 'jewellery_bullion')
                                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-check text-amber-600 text-[10px]"></i> 24K/22K/18K Gold & 999 Silver Live Engine</div>
                                    <div class="flex items-center gap-1.5"><i class="fa-solid fa-check text-amber-600 text-[10px]"></i> Auto Weight + Making Charge + 3% GST</div>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- 3. Visual Media: Logo & Hero Banner -->
            <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-xs space-y-6">
                <div class="flex items-center gap-2.5 pb-4 border-b border-gray-100">
                    <div class="h-8 w-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm">
                        3
                    </div>
                    <div>
                        <h2 class="font-bold text-base text-gray-900">Visual Media & Theme Colors</h2>
                        <p class="text-xs text-gray-500">Upload your logo, custom hero banner, and primary brand color</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Logo Upload -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Store Logo (Square format)</label>
                        <input type="file" name="logo" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                        @if(isset($minisite) && $minisite->logo_url)
                            <div class="mt-2 flex items-center gap-2">
                                <img src="{{ $minisite->logo_url }}" alt="Logo" class="h-10 w-10 object-contain rounded-lg border border-gray-200">
                                <span class="text-[11px] text-gray-500">Current Logo</span>
                            </div>
                        @endif
                    </div>

                    <!-- Banner Upload -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Hero Banner Image (Wide 16:9)</label>
                        <input type="file" name="banner_image" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                        @if(isset($minisite) && $minisite->banner_url)
                            <div class="mt-2 flex items-center gap-2">
                                <img src="{{ $minisite->banner_url }}" alt="Banner" class="h-10 w-24 object-cover rounded-lg border border-gray-200">
                                <span class="text-[11px] text-gray-500">Current Banner</span>
                            </div>
                        @endif
                    </div>

                    <!-- Theme Color Picker -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Brand Theme Color</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="theme_color" value="{{ old('theme_color', $minisite->theme_color ?? '#2563eb') }}" class="h-10 w-16 p-1 rounded-xl border border-gray-300 cursor-pointer">
                            <span class="text-xs text-gray-500">Buttons, badges, and accents will use this color</span>
                        </div>
                    </div>

                    <!-- Theme Style -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Storefront Style</label>
                        <select name="theme_style" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="modern" {{ (old('theme_style', $minisite->theme_style ?? '') == 'modern') ? 'selected' : '' }}>Modern Clean</option>
                            <option value="minimal" {{ (old('theme_style', $minisite->theme_style ?? '') == 'minimal') ? 'selected' : '' }}>Minimalist</option>
                            <option value="vibrant" {{ (old('theme_style', $minisite->theme_style ?? '') == 'vibrant') ? 'selected' : '' }}>Vibrant Marketplace</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 4. WhatsApp & Contact Channels -->
            <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-xs space-y-6">
                <div class="flex items-center gap-2.5 pb-4 border-b border-gray-100">
                    <div class="h-8 w-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                        4
                    </div>
                    <div>
                        <h2 class="font-bold text-base text-gray-900">WhatsApp & Direct Ordering Channels</h2>
                        <p class="text-xs text-gray-500">Enable 1-Click WhatsApp orders and buyer assistance</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">WhatsApp Number (For Direct Orders)</label>
                        <div class="flex items-center">
                            <span class="px-3 py-2.5 text-xs bg-emerald-100 text-emerald-800 border border-r-0 border-emerald-300 rounded-l-xl font-bold">
                                <i class="fa-brands fa-whatsapp"></i> +91
                            </span>
                            <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $minisite->whatsapp_number ?? '') }}" placeholder="9876543210" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-r-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Customer Support Phone</label>
                        <input type="tel" name="support_phone" value="{{ old('support_phone', $minisite->support_phone ?? '') }}" placeholder="011-XXXXXXXX / Mobile" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Support Email</label>
                        <input type="email" name="support_email" value="{{ old('support_email', $minisite->support_email ?? '') }}" placeholder="support@yourbrand.com" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            <i class="fa-brands fa-instagram text-pink-600 mr-1"></i> Instagram ID (हैंडल या लिंक)
                        </label>
                        <div class="relative flex items-center">
                            <span class="inline-flex items-center px-3 py-2.5 rounded-l-xl border border-r-0 border-gray-300 bg-pink-50 text-pink-700 font-bold text-xs select-none">
                                <i class="fa-brands fa-instagram mr-1 text-pink-600"></i> instagram.com/
                            </span>
                            @php
                                $igRaw = old('instagram_link', $minisite->instagram_link ?? '');
                                $igClean = preg_replace('/^https?:\/\/(www\.)?instagram\.com\//i', '', $igRaw);
                                $igClean = ltrim($igClean, '@');
                                $igClean = rtrim($igClean, '/');
                            @endphp
                            <input type="text" name="instagram_link" id="instagram_link_input" 
                                value="{{ $igClean }}" 
                                placeholder="ganeshtraders" 
                                oninput="formatSocialHandle(this, 'instagram')"
                                onblur="formatSocialHandle(this, 'instagram')"
                                class="w-full px-3 py-2.5 text-sm font-bold bg-gray-50 border border-gray-300 rounded-r-xl focus:bg-white focus:outline-none">
                        </div>
                        <span class="text-[11px] text-gray-500 mt-1 block">सिर्फ अपनी ID (उदा. <strong>ganeshtraders</strong>) डालें; पूरा लिंक अपने-आप बन जाएगा।</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            <i class="fa-brands fa-facebook text-blue-600 mr-1"></i> Facebook ID (पेज या लिंक)
                        </label>
                        <div class="relative flex items-center">
                            <span class="inline-flex items-center px-3 py-2.5 rounded-l-xl border border-r-0 border-gray-300 bg-blue-50 text-blue-700 font-bold text-xs select-none">
                                <i class="fa-brands fa-facebook mr-1 text-blue-600"></i> facebook.com/
                            </span>
                            @php
                                $fbRaw = old('facebook_link', $minisite->facebook_link ?? '');
                                $fbClean = preg_replace('/^https?:\/\/(www\.)?facebook\.com\//i', '', $fbRaw);
                                $fbClean = ltrim($fbClean, '@');
                                $fbClean = rtrim($fbClean, '/');
                            @endphp
                            <input type="text" name="facebook_link" id="facebook_link_input" 
                                value="{{ $fbClean }}" 
                                placeholder="ganeshtraders" 
                                oninput="formatSocialHandle(this, 'facebook')"
                                onblur="formatSocialHandle(this, 'facebook')"
                                class="w-full px-3 py-2.5 text-sm font-bold bg-gray-50 border border-gray-300 rounded-r-xl focus:bg-white focus:outline-none">
                        </div>
                        <span class="text-[11px] text-gray-500 mt-1 block">सिर्फ अपना फेसबुक नाम/पेज ID डालें।</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            <i class="fa-brands fa-youtube text-red-600 mr-1"></i> YouTube Channel Handle
                        </label>
                        <div class="relative flex items-center">
                            <span class="inline-flex items-center px-3 py-2.5 rounded-l-xl border border-r-0 border-gray-300 bg-red-50 text-red-700 font-bold text-xs select-none">
                                <i class="fa-brands fa-youtube mr-1 text-red-600"></i> youtube.com/@
                            </span>
                            @php
                                $ytRaw = old('youtube_link', $minisite->youtube_link ?? '');
                                $ytClean = preg_replace('/^https?:\/\/(www\.)?youtube\.com\/(@)?/i', '', $ytRaw);
                                $ytClean = ltrim($ytClean, '@');
                                $ytClean = rtrim($ytClean, '/');
                            @endphp
                            <input type="text" name="youtube_link" id="youtube_link_input" 
                                value="{{ $ytClean }}" 
                                placeholder="ganeshtraders" 
                                oninput="formatSocialHandle(this, 'youtube')"
                                onblur="formatSocialHandle(this, 'youtube')"
                                class="w-full px-3 py-2.5 text-sm font-bold bg-gray-50 border border-gray-300 rounded-r-xl focus:bg-white focus:outline-none">
                        </div>
                        <span class="text-[11px] text-gray-500 mt-1 block">सिर्फ अपना चैनल नाम (उदा. <strong>ganeshtraders</strong>) डालें।</span>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            <i class="fa-brands fa-google text-amber-500"></i> Google Business Review Link
                        </label>
                        <input type="text" name="google_review_link" value="{{ old('google_review_link', $minisite->google_review_link ?? '') }}" placeholder="उदा. https://g.page/r/.../review या Review Link" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- 5. Location & Store Policies -->
            <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-xs space-y-6">
                <div class="flex items-center gap-2.5 pb-4 border-b border-gray-100">
                    <div class="h-8 w-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-sm">
                        5
                    </div>
                    <div>
                        <h2 class="font-bold text-base text-gray-900">Address & Store Policies</h2>
                        <p class="text-xs text-gray-500">Specify physical warehouse location, COD support & return terms</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <!-- GPS Quick Locator Button -->
                    <div class="sm:col-span-3 bg-gradient-to-r from-blue-50 via-indigo-50 to-emerald-50 border border-blue-200 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg shadow-xs shrink-0">
                                <i class="fa-solid fa-location-crosshairs"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-gray-900">Live GPS Location Picker (लाइव लोकेशन सेट करें)</h4>
                                <p class="text-[11px] text-gray-600 mt-0.5">Click to automatically grab your exact store latitude, longitude & Google Maps Pin.</p>
                            </div>
                        </div>
                        <button type="button" onclick="detectGpsLocation()" id="btn_detect_gps" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs transition shrink-0">
                            <i class="fa-solid fa-crosshairs text-amber-300"></i>
                            <span>📍 मेरा लाइव GPS लोकेशन लें</span>
                        </button>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Full Shop / Warehouse Street Address (दुकान का पूरा पता)</label>
                        <input type="text" id="store_address_input" name="address" value="{{ old('address', $minisite->address ?? '') }}" placeholder="Plot 12, Main Market, Shop No. 4" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Landmark / Area (लैंडमार्क / नज़दीकी पहचान)</label>
                        <input type="text" id="store_landmark_input" name="landmark" value="{{ old('landmark', $minisite->landmark ?? '') }}" placeholder="Opposite State Bank / Near Bus Stand" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">City (शहर)</label>
                        <input type="text" id="store_city_input" name="city" value="{{ old('city', $minisite->city ?? '') }}" placeholder="Jaipur / Surat / Raipur" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">State (राज्य)</label>
                        <input type="text" id="store_state_input" name="state" value="{{ old('state', $minisite->state ?? '') }}" placeholder="Rajasthan / Gujarat / Chhattisgarh" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">PIN Code</label>
                        <input type="text" id="store_pincode_input" name="pincode" value="{{ old('pincode', $minisite->pincode ?? '') }}" placeholder="492001" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <!-- Delivery / Dispatch Radius -->
                    <div class="sm:col-span-3 bg-indigo-50/50 border border-indigo-200 rounded-2xl p-4 space-y-3">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-black text-indigo-950">
                                <i class="fa-solid fa-truck-fast text-indigo-600 mr-1"></i> Delivery & Dispatch Radius (डिलिवरी दायरा - कितने KM तक माल भेजते हैं?)
                            </label>
                            <span class="text-[10px] text-indigo-600 font-bold">Quick set radius</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="relative w-48">
                                <input type="number" id="dispatch_radius_input" name="dispatch_radius" value="{{ old('dispatch_radius', $minisite->dispatch_radius ?? 500) }}" min="1" max="5000" class="w-full px-4 py-2 text-sm font-black bg-white border border-indigo-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <span class="absolute right-3 top-2.5 text-xs font-bold text-gray-500">KM</span>
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <button type="button" onclick="setRadius(25)" class="px-2.5 py-1 rounded-lg bg-white border border-indigo-200 hover:bg-indigo-600 hover:text-white text-[11px] font-bold text-indigo-700 transition">25 KM (Local City)</button>
                                <button type="button" onclick="setRadius(100)" class="px-2.5 py-1 rounded-lg bg-white border border-indigo-200 hover:bg-indigo-600 hover:text-white text-[11px] font-bold text-indigo-700 transition">100 KM (District)</button>
                                <button type="button" onclick="setRadius(500)" class="px-2.5 py-1 rounded-lg bg-white border border-indigo-200 hover:bg-indigo-600 hover:text-white text-[11px] font-bold text-indigo-700 transition">500 KM (Statewide)</button>
                                <button type="button" onclick="setRadius(2000)" class="px-2.5 py-1 rounded-lg bg-white border border-indigo-200 hover:bg-indigo-600 hover:text-white text-[11px] font-bold text-indigo-700 transition">All India (Transport)</button>
                            </div>
                        </div>

                        <!-- Serviced Cities / Towns -->
                        <div class="pt-3 border-t border-indigo-200/80">
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold text-indigo-950">
                                    <i class="fa-solid fa-city text-indigo-600 mr-1"></i> Direct Serviced Cities (सीधी वाहन/गाड़ी डिलीवरी वाले शहर)
                                </label>
                                <span class="text-[10px] text-indigo-600 font-semibold">Click chip to add city</span>
                            </div>
                            <input type="text" id="serviced_cities_input" name="serviced_cities" value="{{ old('serviced_cities', $minisite->serviced_cities ?? 'सारंगढ़, रायगढ़, बिलासपुर, बरमकेला, सरिया, चंद्रपुर, रायपुर') }}" placeholder="e.g. सारंगढ़, रायगढ़, बिलासपुर, रायपुर" class="w-full px-4 py-2 text-xs font-bold bg-white border border-indigo-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none mb-1.5">
                            <div class="flex items-center gap-1 flex-wrap">
                                <button type="button" onclick="appendServicedCity('सारंगढ़')" class="px-2 py-0.5 rounded bg-white border border-indigo-200 text-indigo-700 text-[10px] font-bold hover:bg-indigo-100 transition">+ सारंगढ़</button>
                                <button type="button" onclick="appendServicedCity('रायगढ़')" class="px-2 py-0.5 rounded bg-white border border-indigo-200 text-indigo-700 text-[10px] font-bold hover:bg-indigo-100 transition">+ रायगढ़</button>
                                <button type="button" onclick="appendServicedCity('बिलासपुर')" class="px-2 py-0.5 rounded bg-white border border-indigo-200 text-indigo-700 text-[10px] font-bold hover:bg-indigo-100 transition">+ बिलासपुर</button>
                                <button type="button" onclick="appendServicedCity('बरमकेला')" class="px-2 py-0.5 rounded bg-white border border-indigo-200 text-indigo-700 text-[10px] font-bold hover:bg-indigo-100 transition">+ बरमकेला</button>
                                <button type="button" onclick="appendServicedCity('सरिया')" class="px-2 py-0.5 rounded bg-white border border-indigo-200 text-indigo-700 text-[10px] font-bold hover:bg-indigo-100 transition">+ सरिया</button>
                                <button type="button" onclick="appendServicedCity('चंद्रपुर')" class="px-2 py-0.5 rounded bg-white border border-indigo-200 text-indigo-700 text-[10px] font-bold hover:bg-indigo-100 transition">+ चंद्रपुर</button>
                                <button type="button" onclick="appendServicedCity('सक्ती')" class="px-2 py-0.5 rounded bg-white border border-indigo-200 text-indigo-700 text-[10px] font-bold hover:bg-indigo-100 transition">+ सक्ती</button>
                                <button type="button" onclick="appendServicedCity('रायपुर')" class="px-2 py-0.5 rounded bg-white border border-indigo-200 text-indigo-700 text-[10px] font-bold hover:bg-indigo-100 transition">+ रायपुर</button>
                            </div>
                        </div>

                        <!-- Serviced PIN Codes -->
                        <div class="pt-2">
                            <label class="block text-xs font-bold text-indigo-950 mb-1">
                                <i class="fa-solid fa-map-pin text-rose-500 mr-1"></i> Serviced PIN Codes (डिलीवरी पिनकोड सूची - अल्पविराम से अलग करें)
                            </label>
                            <input type="text" name="serviced_pincodes" value="{{ old('serviced_pincodes', $minisite->serviced_pincodes ?? '496445, 496001, 495001, 492001') }}" placeholder="e.g. 496445, 496001, 495001" class="w-full px-4 py-2 text-xs font-mono font-bold bg-white border border-indigo-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                    </div>

                    <!-- Hidden GPS Lat/Lng -->
                    <input type="hidden" id="store_latitude" name="latitude" value="{{ old('latitude', $minisite->latitude ?? '') }}">
                    <input type="hidden" id="store_longitude" name="longitude" value="{{ old('longitude', $minisite->longitude ?? '') }}">

                    <!-- Google Maps Link -->
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            <i class="fa-solid fa-map-location-dot text-rose-500"></i> Google Maps Pin / Location Link
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="text" id="google_map_link_input" name="google_map_link" value="{{ old('google_map_link', $minisite->google_map_link ?? '') }}" placeholder="उदा. https://maps.app.goo.gl/... या Google Maps लिंक" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            @if(isset($minisite) && $minisite->google_map_link)
                                <a href="{{ $minisite->google_map_link }}" target="_blank" class="px-3 py-2.5 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 font-bold text-xs shrink-0 flex items-center gap-1 transition" title="Verify Google Maps Location">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    <span>Check Pin</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Currency Symbol</label>
                        <input type="text" name="currency" value="{{ old('currency', $minisite->currency ?? 'INR') }}" placeholder="INR" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <!-- Toggles -->
                    <div class="sm:col-span-3 pt-2 space-y-3">
                        <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-gray-50 border border-gray-200 cursor-pointer">
                            <input type="checkbox" name="enable_cod" value="1" {{ old('enable_cod', $minisite->enable_cod ?? true) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 rounded">
                            <div>
                                <div class="text-xs font-bold text-gray-900">Enable Cash on Delivery (COD) on Store</div>
                                <div class="text-[11px] text-gray-500">Allow buyers to place orders without paying in advance</div>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-gray-50 border border-gray-200 cursor-pointer">
                            <input type="checkbox" name="enable_whatsapp_order" value="1" {{ old('enable_whatsapp_order', $minisite->enable_whatsapp_order ?? true) ? 'checked' : '' }} class="h-4 w-4 text-emerald-600 rounded">
                            <div>
                                <div class="text-xs font-bold text-gray-900">Enable Instant "Order on WhatsApp" Button</div>
                                <div class="text-[11px] text-gray-500">Show WhatsApp order triggers on product cards and cart drawer</div>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-gray-50 border border-gray-200 cursor-pointer">
                            <input type="checkbox" name="show_last_updated_to_buyers" value="1" {{ old('show_last_updated_to_buyers', $minisite->show_last_updated_to_buyers ?? false) ? 'checked' : '' }} class="h-4 w-4 text-purple-600 rounded">
                            <div>
                                <div class="text-xs font-bold text-gray-900">Show "Last Updated / ताज़ा रेट" Timestamp to Customers</div>
                                <div class="text-[11px] text-gray-500">Shows rate freshness (e.g. "Updated 2 hours ago") to buyers. <i>(Note: As the store owner, you will always see it when logged in)</i></div>
                            </div>
                        </label>
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Custom Store Policies (Shipping, Returns, Warranty)</label>
                        <textarea name="policies" rows="3" placeholder="Enter terms for return, replacement, and delivery timelines" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('policies', $minisite->policies ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 5. 🍽️ Restaurant, Cafe & Cloud Kitchen Service Modes (Food Service Controls) -->
            @php
                $foodModes = isset($minisite) ? $minisite->food_service_modes : [
                    'dine_in' => true,
                    'takeaway' => true,
                    'delivery' => true,
                    'tables_count' => 10,
                    'packaging_charge' => 0,
                    'min_order_delivery' => 0,
                ];
            @endphp
            <div class="bg-gradient-to-br from-amber-50/70 via-white to-orange-50/60 rounded-3xl border-2 border-amber-200 p-6 sm:p-8 shadow-xs space-y-6">
                <input type="hidden" name="food_settings_submitted" value="1">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-amber-200/80">
                    <div class="flex items-center gap-2.5">
                        <div class="h-9 w-9 rounded-xl bg-amber-500 text-white flex items-center justify-center font-black text-base shadow-xs">
                            <i class="fa-solid fa-utensils"></i>
                        </div>
                        <div>
                            <h2 class="font-black text-base text-gray-900 flex items-center gap-2">
                                <span>Restaurant & Cloud Kitchen Service Modes</span>
                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 text-[10px] font-black uppercase">भोजन सेवा सेटिंग्स</span>
                            </h2>
                            <p class="text-xs text-gray-600 mt-0.5">तय करें कि ग्राहक सिर्फ टेबल पर ऑर्डर करेंगे, टेकअवे पार्सल लेंगे, या होम डिलीवरी मंगाएंगे।</p>
                        </div>
                    </div>

                    <!-- Quick Tools: AI Scanner & Table QR Buttons -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <button type="button" onclick="openAiMenuScannerModal()" class="px-3.5 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition">
                            <i class="fa-solid fa-wand-magic-sparkles text-amber-200"></i>
                            <span>📸 AI Snap-to-Menu</span>
                        </button>
                        <a href="{{ route('seller.minisite.table_qr') }}" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition">
                            <i class="fa-solid fa-qrcode text-amber-400"></i>
                            <span>🖨️ टेबल QR स्टैंडी डाउनलोड</span>
                        </a>
                    </div>
                </div>

                <!-- 3 Service Mode Toggles -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Dine-In -->
                    <label class="p-4 rounded-2xl bg-white border-2 border-amber-200 hover:border-amber-400 cursor-pointer transition shadow-2xs flex items-start gap-3">
                        <input type="checkbox" name="food_mode_dine_in" value="1" {{ !empty($foodModes['dine_in']) ? 'checked' : '' }} class="h-4 w-4 mt-0.5 text-amber-600 rounded">
                        <div>
                            <div class="text-xs font-black text-gray-900 flex items-center gap-1.5">
                                <span>🪑 Dine-In (डाइन-इन / टेबल)</span>
                            </div>
                            <p class="text-[11px] text-gray-500 mt-0.5 leading-relaxed">ग्राहक टेबल पर बैठकर QR स्कैन कर सीधे टेबल पर आर्डर करेंगे।</p>
                        </div>
                    </label>

                    <!-- Takeaway -->
                    <label class="p-4 rounded-2xl bg-white border-2 border-amber-200 hover:border-amber-400 cursor-pointer transition shadow-2xs flex items-start gap-3">
                        <input type="checkbox" name="food_mode_takeaway" value="1" {{ !empty($foodModes['takeaway']) ? 'checked' : '' }} class="h-4 w-4 mt-0.5 text-amber-600 rounded">
                        <div>
                            <div class="text-xs font-black text-gray-900 flex items-center gap-1.5">
                                <span>🥡 Takeaway (टेकअवे / पार्सल)</span>
                            </div>
                            <p class="text-[11px] text-gray-500 mt-0.5 leading-relaxed">ग्राहक आर्डर कर काउंटर से खुद पार्सल पिक कर सकेंगे।</p>
                        </div>
                    </label>

                    <!-- Cloud Kitchen / Home Delivery -->
                    <label class="p-4 rounded-2xl bg-white border-2 border-amber-200 hover:border-amber-400 cursor-pointer transition shadow-2xs flex items-start gap-3">
                        <input type="checkbox" name="food_mode_delivery" value="1" {{ !empty($foodModes['delivery']) ? 'checked' : '' }} class="h-4 w-4 mt-0.5 text-amber-600 rounded">
                        <div>
                            <div class="text-xs font-black text-gray-900 flex items-center gap-1.5">
                                <span>🛵 Cloud Kitchen (होम डिलीवरी)</span>
                            </div>
                            <p class="text-[11px] text-gray-500 mt-0.5 leading-relaxed">ग्राहक घर पर डिलीवरी हेतु पता व मोबाइल डालकर आर्डर करेंगे।</p>
                        </div>
                    </label>
                </div>

                <!-- Parameters: Tables Count, Packaging, Min Delivery Order -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">कुल डाइन-इन टेबल संख्या (Total Tables)</label>
                        <input type="number" name="food_tables_count" value="{{ old('food_tables_count', $foodModes['tables_count'] ?? 10) }}" min="1" max="100" class="w-full px-3 py-2 text-xs font-black border border-amber-300 rounded-xl bg-white">
                        <span class="text-[10px] text-gray-400 block mt-0.5">टेबल 1 से लेकर इस संख्या तक QR जनरेट होंगे</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">पार्सल / पैकेजिंग शुल्क (Packaging Charge ₹)</label>
                        <input type="number" name="food_packaging_charge" value="{{ old('food_packaging_charge', $foodModes['packaging_charge'] ?? 0) }}" min="0" step="1" class="w-full px-3 py-2 text-xs font-black border border-amber-300 rounded-xl bg-white">
                        <span class="text-[10px] text-gray-400 block mt-0.5">टेकअवे/डिलीवरी पर ऑटोमैटिक जुड़ेगा (0 = मुफ़्त)</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">न्यूनतम होम डिलीवरी ऑर्डर (Min Order ₹)</label>
                        <input type="number" name="food_min_order_delivery" value="{{ old('food_min_order_delivery', $foodModes['min_order_delivery'] ?? 0) }}" min="0" step="10" class="w-full px-3 py-2 text-xs font-black border border-amber-300 rounded-xl bg-white">
                        <span class="text-[10px] text-gray-400 block mt-0.5">इससे कम बिल पर सिर्फ टेकअवे/डाइन-इन मिलेगा</span>
                    </div>
                </div>

                <!-- Optional Payment & Bill Toggles (बिल और UPI दोनों अलग-अलग टिक करने का सिस्टम) -->
                <div class="pt-2 border-t border-amber-200/80 space-y-2.5">
                    <label class="flex items-start gap-3 p-3.5 rounded-2xl bg-white border border-amber-200 cursor-pointer hover:bg-amber-50/40 transition">
                        <input type="checkbox" name="food_show_bill_amount" value="1" {{ !empty($foodModes['show_bill_amount'] ?? true) ? 'checked' : '' }} class="h-4 w-4 mt-0.5 text-amber-600 rounded">
                        <div>
                            <div class="text-xs font-black text-gray-900">1. व्हाट्सएप ऑर्डर में कुल बिल राशि (Bill Amount) दिखाएं</div>
                            <div class="text-[11px] text-gray-500">यदि चालू रखेंगे तो हर डिश का रेट, क्वांटिटी और कुल बिल दिखेगा। यदि बंद रखेंगे तो सिर्फ आइटम के नाम व संख्या जाएगी (मालिक खुद रेट तय करेंगे)।</div>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3.5 rounded-2xl bg-white border border-amber-200 cursor-pointer hover:bg-amber-50/40 transition">
                        <input type="checkbox" name="food_show_upi_payment" value="1" {{ !empty($foodModes['show_upi_payment'] ?? true) ? 'checked' : '' }} class="h-4 w-4 mt-0.5 text-emerald-600 rounded">
                        <div>
                            <div class="text-xs font-black text-gray-900">2. व्हाट्सएप में तुरंत UPI भुगतान लिंक (GPay / PhonePe / Paytm) भेजें</div>
                            <div class="text-[11px] text-gray-500">यदि चालू रखेंगे तो ग्राहक को मैसेज के नीचे 1-क्लिक UPI पे लिंक मिलेगा। यदि बंद रखेंगे तो कोई पेमेंट लिंक नहीं जाएगा (कैश/काउंटर पेमेंट हेतु)।</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 6. Payment & Banking Setup (UPI / Bank Transfer) -->
            <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-xs space-y-6">
                <div class="flex items-center gap-2.5 pb-4 border-b border-gray-100">
                    <div class="h-8 w-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                        6
                    </div>
                    <div>
                        <h2 class="font-bold text-base text-gray-900">UPI ID & Bank Account for Customer Payments</h2>
                        <p class="text-xs text-gray-500">Provide your UPI ID or Bank account so customers can pay directly on order confirmation and WhatsApp</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- 🔒 Multi-channel Isolation Notice -->
                    <div class="sm:col-span-2 p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                        <i class="fa-solid fa-shield-halved text-indigo-600 mt-0.5 text-base"></i>
                        <div class="text-xs text-slate-700 leading-relaxed">
                            <strong>🔒 मल्टी-चैनल सुरक्षा गारंटी:</strong> यहाँ दर्ज किया गया UPI ID व बैंक खाता केवल आपकी इस डायरेक्ट वेबसाइट और WhatsApp ऑर्डर भुगतान के लिए है। यह <strong>Meesho, Amazon या Flipkart</strong> के बैंक खातों से बिल्कुल अलग (isolated) रहेगा और उसमें कोई बदलाव नहीं करेगा।
                        </div>
                    </div>

                    <div class="sm:col-span-2 bg-emerald-50/70 border-2 border-emerald-300 rounded-3xl p-5 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-black text-emerald-950">
                                <i class="fa-solid fa-mobile-screen mr-1 text-emerald-600"></i> दुकान का UPI ID (Google Pay / PhonePe / Paytm / BHIM)
                            </label>
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-emerald-200/70 text-emerald-900">Direct Customer Payment</span>
                        </div>

                        <div class="relative">
                            <input type="text" name="upi_id" id="minisite_upi_id_input" value="{{ old('upi_id', $minisite->upi_id ?? '') }}" placeholder="उदा. 9244906069@ybl या ganeshtraders@okaxis" oninput="updateUpiPreview(this.value)" class="w-full px-4 py-3 text-sm font-bold bg-white border border-emerald-400 rounded-2xl focus:border-emerald-600 focus:outline-hidden shadow-xs">
                        </div>

                        <!-- Live Verified Name & UPI Link Preview -->
                        <div id="upi_preview_box" class="p-3 rounded-xl bg-white border border-emerald-200 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                <div>
                                    <span class="text-slate-500 text-[11px]">ग्राहक को दिखने वाला नाम (Payee Name):</span>
                                    <span class="font-extrabold text-slate-900 ml-1" id="upi_preview_name">{{ $minisite->page_title ?: 'Ganesh Traders' }}</span>
                                </div>
                            </div>
                            <div class="text-[11px] font-mono font-bold text-emerald-700" id="upi_preview_id">
                                {{ $minisite->upi_id ?: 'UPI ID दर्ज करें...' }}
                            </div>
                        </div>

                        <!-- 💡 Where to find UPI ID Guide & QR Scanner Help -->
                        <div class="bg-emerald-100/60 rounded-2xl p-3.5 text-xs text-emerald-900 space-y-2 border border-emerald-200">
                            <div class="font-black flex items-center gap-1.5 text-emerald-950">
                                <i class="fa-solid fa-qrcode text-emerald-700"></i>
                                <span>💡 यदि आपको सिर्फ QR कोड मिला है और UPI ID नहीं पता, तो ऐसे देखें:</span>
                            </div>
                            <ul class="list-disc list-inside space-y-1 text-[11px] text-emerald-900 leading-relaxed pl-1">
                                <li><strong>QR कोड के नीचे देखें:</strong> आपके दुकान के PhonePe / GPay / Paytm QR कोड स्टैंडी के नीचे छोटे अक्षरों में UPI ID (उदा. <code>98XXXXXXXX@ybl</code> या <code>ganeshtraders@okaxis</code>) लिखी होती है।</li>
                                <li><strong>Google Lens से फोटो खींचे:</strong> अपने फ़ोन के Google Lens से अपने QR कोड की फोटो खींचें, वह ऊपर लिंक में <code>pa=...</code> के बाद आपकी पूरी UPI ID दिखा देगा।</li>
                                <li><strong>PhonePe / GPay ऐप में:</strong> ऐप खोलें -> ऊपर प्रोफाइल फोटो पर दबाएं -> "QR Codes" या "UPI ID" चुनें, वही आपका UPI ID है।</li>
                            </ul>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Account Holder Name</label>
                        <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder', $minisite->bank_account_holder ?? '') }}" placeholder="e.g. Rajesh Kumar" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Bank Name</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $minisite->bank_name ?? '') }}" placeholder="e.g. HDFC Bank / State Bank of India" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Account Number</label>
                        <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $minisite->bank_account_number ?? '') }}" placeholder="e.g. 50100234567890" class="w-full px-4 py-2.5 text-sm font-mono bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">IFSC Code</label>
                        <input type="text" name="bank_ifsc" value="{{ old('bank_ifsc', $minisite->bank_ifsc ?? '') }}" placeholder="e.g. HDFC0001234" class="w-full px-4 py-2.5 text-sm font-mono uppercase bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-hidden">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-gray-50 border border-gray-200 cursor-pointer">
                            <input type="checkbox" name="show_payment_details_to_buyer" value="1" {{ old('show_payment_details_to_buyer', $minisite->show_payment_details_to_buyer ?? true) ? 'checked' : '' }} class="h-4 w-4 text-emerald-600 rounded">
                            <div>
                                <div class="text-xs font-bold text-gray-900">Show UPI & Bank Details on Digital Receipt to Buyer</div>
                                <div class="text-[11px] text-gray-500">Enable this to display payment details on digital order slips</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- 7. Shop & Showroom Photos (दुकान व शोरूम की तस्वीरें) -->
            <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-xs space-y-6">
                <div class="flex items-center gap-2.5 pb-4 border-b border-gray-100">
                    <div class="h-8 w-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                        7
                    </div>
                    <div>
                        <h2 class="font-bold text-base text-gray-900">Shop & Showroom Photos (दुकान व गोदाम की तस्वीरें)</h2>
                        <p class="text-xs text-gray-500">Add photos of your shop front board, showroom display counter & warehouse to build trust</p>
                    </div>
                </div>

                @php
                    $shopPhotos = isset($minisite) ? ($minisite->shop_photos ?? []) : [];
                @endphp

                @if(!empty($shopPhotos) && count($shopPhotos) > 0)
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Current Shop Showcase Photos:</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach($shopPhotos as $pIdx => $photo)
                                <div class="relative aspect-video rounded-xl overflow-hidden border border-gray-200 group">
                                    <img src="{{ $photo }}" alt="Shop Photo" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                    <label class="block text-xs font-bold text-slate-800 mb-1">
                        <i class="fa-solid fa-camera text-indigo-600 mr-1"></i> Add Showroom / Warehouse Photo Link (या सीधे स्टोर पेज से अपलोड करें)
                    </label>
                    <input type="url" name="new_shop_photo_url" placeholder="https://... (Shop board, rack display or warehouse photo)" class="w-full px-4 py-2.5 text-xs bg-white border border-slate-300 rounded-xl focus:border-indigo-500 focus:outline-none">
                    <span class="text-[11px] text-slate-500 mt-1 block">Tip: You can also click "Add Shop Photo" directly on your storefront page anytime!</span>
                </div>
            </div>

            <!-- Submit Action -->
            <div class="flex items-center justify-end gap-4 pt-4">
                <a href="{{ route('seller.dashboard') }}" class="px-6 py-3 rounded-2xl bg-gray-200 text-gray-700 font-bold text-sm hover:bg-gray-300 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>{{ isset($minisite) ? 'Save & Update Storefront' : 'Publish My Storefront' }}</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        function setBusinessCat(catName) {
            const input = document.getElementById('business_cat_input');
            if (input) input.value = catName;
        }

        function appendDealership(brandName) {
            const input = document.getElementById('dealership_input');
            if (!input) return;
            let current = input.value.trim();
            if (!current) {
                input.value = 'Authorized Dealer of ' + brandName;
            } else if (!current.includes(brandName)) {
                input.value = current + ', ' + brandName;
            }
        }

        function setTagline(taglineText) {
            const input = document.getElementById('tagline_input');
            if (input) input.value = taglineText;
        }

        function setRadius(km) {
            const input = document.getElementById('dispatch_radius_input');
            if (input) input.value = km;
        }

        function updateUpiPreview(val) {
            const previewEl = document.getElementById('upi_preview_id');
            if (previewEl) {
                previewEl.innerText = val.trim() ? val.trim() : 'UPI ID दर्ज करें...';
            }
        }

        function formatSocialHandle(input, platform) {
            let val = input.value.trim();
            if (!val) return;
            val = val.replace(/^@/, '');
            if (platform === 'instagram') {
                const match = val.match(/instagram\.com\/([^\/\?#]+)/i);
                if (match) val = match[1];
            } else if (platform === 'facebook') {
                const match = val.match(/facebook\.com\/([^\/\?#]+)/i);
                if (match) val = match[1];
            } else if (platform === 'youtube') {
                const match = val.match(/youtube\.com\/(@)?([^\/\?#]+)/i);
                if (match) val = match[2];
            }
            input.value = val.replace(/^@/, '').replace(/\/$/, '');
        }

        function appendServicedCity(city) {
            const input = document.getElementById('serviced_cities_input');
            if (!input) return;
            let current = input.value.trim();
            if (!current) {
                input.value = city;
            } else if (!current.includes(city)) {
                input.value = current + ', ' + city;
            }
        }

        function detectGpsLocation() {
            const btn = document.getElementById('btn_detect_gps');
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser.');
                return;
            }

            if (btn) {
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Detecting GPS...';
                btn.disabled = true;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    const latEl = document.getElementById('store_latitude');
                    const lngEl = document.getElementById('store_longitude');
                    const mapEl = document.getElementById('google_map_link_input');

                    if (latEl) latEl.value = lat;
                    if (lngEl) lngEl.value = lng;
                    if (mapEl) {
                        mapEl.value = `https://www.google.com/maps?q=${lat},${lng}`;
                    }

                    if (btn) {
                        btn.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-300"></i> Location Captured!';
                        btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                        btn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
                    }
                },
                function(error) {
                    if (btn) {
                        btn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Retry GPS';
                        btn.disabled = false;
                    }
                    alert('Could not get GPS location: ' + error.message + '. Please ensure location permission is allowed.');
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }

        function generateAiStory() {
            const title = document.getElementById('store_title_input')?.value || 'Our Store';
            const cat = document.getElementById('business_cat_input')?.value || 'Sanitaryware, Bath Fittings & Quality Goods';
            const dealer = document.getElementById('dealership_input')?.value || 'Authorized Dealer of Leading Brands';
            const city = document.getElementById('store_city_input')?.value || 'our city';

            const welcomeEl = document.getElementById('welcome_msg_input');
            const aboutEl = document.getElementById('about_text_input');

            if (welcomeEl) {
                welcomeEl.value = `Namaste! Welcome to ${title} (${city}). ${dealer}. Browse our entire wholesale and retail catalog with 100% genuine warranty and direct express delivery.`;
            }

            if (aboutEl) {
                aboutEl.value = `${title} is your trusted distributor and dealer for ${cat}. With direct factory sourcing, we guarantee authentic branded products, transparent wholesale rates, and fast doorstep delivery across our service network. Whether you are building a new house, renovating a bathroom, or fulfilling large contractor project requirements, our dedicated team is always ready to assist you on WhatsApp and phone.`;
            }

            alert('✨ AI Store Story & Welcome Message generated and filled successfully!');
        }

        function highlightSelectedModule(selectedRadio) {
            const grid = document.getElementById('businessModuleGrid');
            if (!grid) return;
            
            const cards = grid.querySelectorAll('.module-card');
            cards.forEach(card => {
                const radio = card.querySelector('.module-radio');
                const iconBox = card.querySelector('.module-icon-box');
                const badge = card.querySelector('.module-badge');
                const check = card.querySelector('.check-indicator');

                if (radio && radio.checked) {
                    card.classList.remove('border-gray-200', 'bg-white', 'hover:border-gray-300', 'hover:bg-gray-50/50');
                    card.classList.add('border-blue-600', 'bg-blue-50/40', 'shadow-xs', 'ring-2', 'ring-blue-500/20');
                    
                    if (iconBox) {
                        iconBox.classList.remove('bg-gray-100', 'text-gray-700');
                        iconBox.classList.add('bg-blue-600', 'text-white', 'shadow-xs');
                    }
                    if (badge) {
                        badge.classList.remove('bg-gray-100', 'text-gray-600');
                        badge.classList.add('bg-blue-100', 'text-blue-800');
                    }
                    if (check) {
                        check.classList.remove('text-gray-300');
                        check.classList.add('text-blue-600');
                    }
                } else {
                    card.classList.remove('border-blue-600', 'bg-blue-50/40', 'shadow-xs', 'ring-2', 'ring-blue-500/20');
                    card.classList.add('border-gray-200', 'bg-white', 'hover:border-gray-300', 'hover:bg-gray-50/50');
                    
                    if (iconBox) {
                        iconBox.classList.remove('bg-blue-600', 'text-white', 'shadow-xs');
                        iconBox.classList.add('bg-gray-100', 'text-gray-700');
                    }
                    if (badge) {
                        badge.classList.remove('bg-blue-100', 'text-blue-800');
                        badge.classList.add('bg-gray-100', 'text-gray-600');
                    }
                    if (check) {
                        check.classList.remove('text-blue-600');
                        check.classList.add('text-gray-300');
                    }
                }
            });
        }

        // 📸 AI Snap-to-Menu Modal Functions
        let parsedMenuItems = [];

        function openAiMenuScannerModal() {
            document.getElementById('ai-menu-modal')?.classList.remove('hidden');
        }
        function closeAiMenuScannerModal() {
            document.getElementById('ai-menu-modal')?.classList.add('hidden');
        }

        async function submitMenuAiScan() {
            const fileInput = document.getElementById('ai_menu_file');
            const textInput = document.getElementById('ai_menu_text');
            const btn = document.getElementById('btn_start_ai_scan');
            const spinner = document.getElementById('ai_scan_spinner');
            const resultsBox = document.getElementById('ai_menu_results_box');

            const formData = new FormData();
            if (fileInput && fileInput.files[0]) {
                formData.append('menu_image', fileInput.files[0]);
            }
            if (textInput && textInput.value.trim()) {
                formData.append('menu_text', textInput.value.trim());
            }

            formData.append('_token', '{{ csrf_token() }}');

            btn.disabled = true;
            spinner.classList.remove('hidden');

            try {
                const res = await fetch("{{ route('seller.minisite.ai_menu_scan') }}", {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success && data.items && data.items.length > 0) {
                    parsedMenuItems = data.items;
                    renderExtractedItemsTable();
                    resultsBox.classList.remove('hidden');
                } else {
                    alert(data.message || 'Menu could not be extracted. Please check text or image.');
                }
            } catch (err) {
                console.error(err);
                alert('AI Menu scanning failed: ' + err.message);
            } finally {
                btn.disabled = false;
                spinner.classList.add('hidden');
            }
        }

        function renderExtractedItemsTable() {
            const tbody = document.getElementById('ai_extracted_tbody');
            if (!tbody) return;

            let html = '';
            parsedMenuItems.forEach((item, idx) => {
                const isVeg = item.diet_type === 'veg';
                html += `
                    <tr class="border-b border-gray-100 hover:bg-gray-50/80 text-xs font-bold" id="ai_row_${idx}">
                        <td class="p-2.5 text-center">
                            <span class="inline-block h-3.5 w-3.5 rounded-full ${isVeg ? 'bg-emerald-600' : 'bg-rose-600'}" title="${isVeg ? 'Pure Veg' : 'Non-Veg'}"></span>
                        </td>
                        <td class="p-2.5">
                            <input type="text" value="${item.name}" onchange="parsedMenuItems[${idx}].name = this.value" class="w-full px-2 py-1 text-xs font-black border border-gray-200 rounded-lg bg-white">
                        </td>
                        <td class="p-2.5">
                            <input type="text" value="${item.group_name}" onchange="parsedMenuItems[${idx}].group_name = this.value" class="w-full px-2 py-1 text-xs font-semibold border border-gray-200 rounded-lg bg-white">
                        </td>
                        <td class="p-2.5 w-24">
                            <input type="number" value="${item.price_half || ''}" placeholder="—" onchange="parsedMenuItems[${idx}].price_half = parseFloat(this.value) || null" class="w-full px-2 py-1 text-xs font-mono font-bold border border-gray-200 rounded-lg bg-white text-center">
                        </td>
                        <td class="p-2.5 w-24">
                            <input type="number" value="${item.price_full || item.price}" onchange="parsedMenuItems[${idx}].price_full = parseFloat(this.value); parsedMenuItems[${idx}].price = parseFloat(this.value);" class="w-full px-2 py-1 text-xs font-mono font-black border border-gray-200 rounded-lg bg-white text-center text-emerald-800">
                        </td>
                        <td class="p-2.5 text-center">
                            <button type="button" onclick="deleteExtractedRow(${idx})" class="text-rose-500 hover:text-rose-700 text-xs" title="Remove Item">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
            document.getElementById('ai_items_badge').innerText = `${parsedMenuItems.length} Dishes Detected`;
        }

        function deleteExtractedRow(idx) {
            parsedMenuItems.splice(idx, 1);
            renderExtractedItemsTable();
        }

        function addNewBlankDishRow() {
            parsedMenuItems.unshift({
                name: 'New Special Dish',
                group_name: 'Main Course (Veg)',
                diet_type: 'veg',
                price: 120,
                price_half: 70,
                price_full: 120,
                spice_level: 'medium'
            });
            renderExtractedItemsTable();
        }

        async function saveAllExtractedMenu() {
            if (!parsedMenuItems || parsedMenuItems.length === 0) {
                alert('No dishes to save.');
                return;
            }

            const btn = document.getElementById('btn_save_all_menu');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving to catalog...';

            try {
                const res = await fetch("{{ route('seller.minisite.bulk_save_menu') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ items: parsedMenuItems })
                });

                const data = await res.json();
                if (data.success) {
                    alert(data.message || 'Dishes successfully added to your catalog!');
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (err) {
                console.error(err);
                alert('Failed to save dishes: ' + err.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Save All Items to Live Menu';
            }
        }
    </script>

    <!-- 📸 AI Snap-to-Menu Modal for Restaurant / Cloud Kitchen Owner -->
    <div id="ai-menu-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-3xl w-full p-6 shadow-2xl border border-gray-200 animate-in zoom-in-95 duration-200 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="h-8 w-8 rounded-xl bg-amber-500 text-white flex items-center justify-center text-sm shadow-xs">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">AI Snap-to-Menu: मेनू कार्ड से 1-क्लिक में डिजिटल मेनू बनाएं</h3>
                        <p class="text-[11px] text-gray-500">फोटो खींचकर अपलोड करें — AI चाइनीज, तंदूर रोटी, वेज, नॉनवेज व हाफ/फुल रेट अलग कर देगा।</p>
                    </div>
                </div>
                <button type="button" onclick="closeAiMenuScannerModal()" class="text-gray-400 hover:text-gray-600 text-sm p-1 rounded-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="overflow-y-auto py-4 space-y-4 flex-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">1. मेनू कार्ड की फ़ोटो चुनें (Upload Photo):</label>
                        <input type="file" id="ai_menu_file" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-amber-900 hover:file:bg-amber-200">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">2. या मेनू टेक्स्ट यहाँ पेस्ट करें (Paste Text):</label>
                        <textarea id="ai_menu_text" rows="2" placeholder="e.g. Tandoori Butter Roti 15, Chilli Chicken 180 / 300, Shahi Paneer 140 / 240..." class="w-full px-3 py-2 text-xs font-mono border border-gray-300 rounded-xl"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-2 pt-2">
                    <span class="text-[11px] text-gray-400">💡 फोटो साफ हो तो AI 5 सेकंड में पूरा मेनू पढ़ लेता है।</span>
                    <button type="button" onclick="submitMenuAiScan()" id="btn_start_ai_scan" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-black text-xs flex items-center gap-2 shadow-md transition shrink-0">
                        <span id="ai_scan_spinner" class="hidden"><i class="fa-solid fa-spinner fa-spin"></i></span>
                        <i class="fa-solid fa-robot"></i>
                        <span>AI से मेनू स्कैन करें</span>
                    </button>
                </div>

                <!-- Results Box -->
                <div id="ai_menu_results_box" class="hidden mt-4 pt-4 border-t border-gray-100 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-gray-900" id="ai_items_badge">0 Dishes Detected</span>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="addNewBlankDishRow()" class="px-2.5 py-1 rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-900 text-[11px] font-bold flex items-center gap-1 transition">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                                <span>+ नई डिश जोड़ें</span>
                            </button>
                            <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-full">✓ आप किसी भी रेट या नाम को यहीं बदल सकते हैं</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded-2xl max-h-64 overflow-y-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50 text-[10px] font-black uppercase text-gray-500 tracking-wider sticky top-0 border-b border-gray-200">
                                <tr>
                                    <th class="p-2.5 text-center w-8">डाइट</th>
                                    <th class="p-2.5">डिश का नाम</th>
                                    <th class="p-2.5">कैटेगरी (ग्रुप)</th>
                                    <th class="p-2.5 text-center">हाफ रेट (₹)</th>
                                    <th class="p-2.5 text-center">फुल रेट (₹)</th>
                                    <th class="p-2.5 text-center w-8">हटाएं</th>
                                </tr>
                            </thead>
                            <tbody id="ai_extracted_tbody" class="divide-y divide-gray-100">
                                <!-- Populated dynamically -->
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" onclick="closeAiMenuScannerModal()" class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 font-bold text-xs">
                            रद्द करें
                        </button>
                        <button type="button" onclick="saveAllExtractedMenu()" id="btn_save_all_menu" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs flex items-center gap-2 shadow-md transition">
                            <i class="fa-solid fa-check"></i>
                            <span>Save All Items to Live Menu</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('components.ai-copilot-widget')
</body>
</html>
