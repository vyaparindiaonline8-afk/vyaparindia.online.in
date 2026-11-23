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
                        <label class="block text-xs font-bold text-gray-700 mb-1">Instagram Profile URL</label>
                        <input type="url" name="instagram_link" value="{{ old('instagram_link', $minisite->instagram_link ?? '') }}" placeholder="https://instagram.com/yourhandle" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Facebook Page URL</label>
                        <input type="url" name="facebook_link" value="{{ old('facebook_link', $minisite->facebook_link ?? '') }}" placeholder="https://facebook.com/yourbusiness" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">YouTube Channel URL</label>
                        <input type="url" name="youtube_link" value="{{ old('youtube_link', $minisite->youtube_link ?? '') }}" placeholder="https://youtube.com/@yourchannel" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            <i class="fa-brands fa-google text-amber-500"></i> Google Business Review Link
                        </label>
                        <input type="url" name="google_review_link" value="{{ old('google_review_link', $minisite->google_review_link ?? '') }}" placeholder="https://g.page/r/.../review (Buyers can directly view & leave Google reviews)" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            <i class="fa-solid fa-map-location-dot text-rose-500"></i> Google Maps / Warehouse Location Link
                        </label>
                        <input type="url" name="google_map_link" value="{{ old('google_map_link', $minisite->google_map_link ?? '') }}" placeholder="https://maps.google.com/?q=..." class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
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
                            <input type="url" id="google_map_link_input" name="google_map_link" value="{{ old('google_map_link', $minisite->google_map_link ?? '') }}" placeholder="https://maps.google.com/?q=..." class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
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
                    <div class="sm:col-span-2 bg-emerald-50/60 border border-emerald-200 rounded-2xl p-4">
                        <label class="block text-xs font-bold text-emerald-900 mb-1">
                            <i class="fa-solid fa-mobile-screen mr-1"></i> Storefront UPI ID (GPay / PhonePe / Paytm / BHIM)
                        </label>
                        <input type="text" name="upi_id" value="{{ old('upi_id', $minisite->upi_id ?? '') }}" placeholder="e.g. 9876543210@paytm or shop@okhdfcbank" class="w-full px-4 py-2.5 text-xs font-bold bg-white border border-emerald-300 rounded-xl focus:border-emerald-500 focus:outline-hidden">
                        <span class="text-[11px] text-emerald-700 block mt-1">This UPI ID will be shown with a 1-click Pay button on the Order Success page & WhatsApp message.</span>
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
    </script>
    @include('components.ai-copilot-widget')
</body>
</html>
