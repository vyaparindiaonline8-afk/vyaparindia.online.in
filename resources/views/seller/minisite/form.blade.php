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
                        <label class="block text-xs font-bold text-gray-700 mb-1">Store / Business Name <span class="text-red-500">*</span></label>
                        <input type="text" name="page_title" value="{{ old('page_title', $minisite->page_title ?? '') }}" required placeholder="e.g. Royal Handlooms & Crafts" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Store URL Slug <span class="text-red-500">*</span></label>
                        <div class="flex items-center">
                            <span class="px-3 py-2.5 text-xs bg-gray-200 border border-r-0 border-gray-300 rounded-l-xl text-gray-500 font-mono">/</span>
                            <input type="text" name="slug" value="{{ old('slug', $minisite->slug ?? '') }}" required placeholder="royal-handlooms" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-r-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono">
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tagline / Subheading</label>
                        <input type="text" name="tagline" value="{{ old('tagline', $minisite->tagline ?? '') }}" placeholder="e.g. Direct Wholesaler of Authentic Handcrafted Silk & Cotton" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Welcome Message (Hero Banner)</label>
                        <textarea name="welcome_message" rows="2" placeholder="Welcome buyers with a compelling greeting or discount notice" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('welcome_message', $minisite->welcome_message ?? '') }}</textarea>
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
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Street Address</label>
                        <input type="text" name="address" value="{{ old('address', $minisite->address ?? '') }}" placeholder="Plot 12, Industrial Area, Phase 2" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">City</label>
                        <input type="text" name="city" value="{{ old('city', $minisite->city ?? '') }}" placeholder="Jaipur / Surat" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">PIN Code</label>
                        <input type="text" name="pincode" value="{{ old('pincode', $minisite->pincode ?? '') }}" placeholder="302001" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
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
</body>
</html>
