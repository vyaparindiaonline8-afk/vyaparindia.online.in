<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sellerPage->page_title ?? 'Store' }} | VyaparIndia</title>
    <meta name="description" content="{{ $sellerPage->tagline ?? $sellerPage->welcome_message ?? 'Shop directly from seller' }}">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            DEFAULT: '{{ $sellerPage->theme_color ?: "#2563eb" }}',
                            dark: '{{ $sellerPage->theme_color ?: "#1d4ed8" }}',
                            light: '{{ $sellerPage->theme_color ? $sellerPage->theme_color . "20" : "#eff6ff" }}',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --brand-color: {{ $sellerPage->theme_color ?: '#2563eb' }};
        }
        .bg-brand-custom { background-color: var(--brand-color); }
        .text-brand-custom { color: var(--brand-color); }
        .border-brand-custom { border-color: var(--brand-color); }

        /* Crisp High-Res Image Optimization */
        img {
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
        }
        .crisp-img {
            image-rendering: -webkit-optimize-contrast;
            object-fit: contain;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.06));
        }
        /* Amazon & Meesho Studio Catalog Backdrop for White & Transparent Items */
        .studio-card-box {
            background: radial-gradient(circle at center, #ffffff 45%, #f1f5f9 100%);
            border: 1px solid rgba(226, 232, 240, 0.95);
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.03);
        }
    </style>

    <!-- 🌐 Schema.org LocalBusiness JSON-LD Structured Data for Google Indexing -->
    @php
        $localBusinessSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $sellerPage->page_title,
            'description' => $sellerPage->tagline ?: ($sellerPage->welcome_message ?: 'Official Storefront on VyaparIndia'),
            'url' => url()->current(),
            'telephone' => $sellerPage->support_phone ?: ($sellerPage->whatsapp_number ?: ''),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $sellerPage->address ?: '',
                'addressLocality' => $sellerPage->city ?: '',
                'postalCode' => $sellerPage->pincode ?: '',
                'addressCountry' => 'IN'
            ],
            'image' => $sellerPage->logo_url ?: asset('images/logo.png'),
            'priceRange' => '₹₹'
        ];
        if ($sellerPage->dispatch_radius) {
            $localBusinessSchema['areaServed'] = [
                '@type' => 'GeoCircle',
                'geoRadius' => ($sellerPage->dispatch_radius * 1000) . 'm'
            ];
        }
    @endphp
    <script type="application/ld+json">
        {!! json_encode($localBusinessSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @stack('seo')
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen antialiased">

    @if(Auth::check() && Auth::id() === $sellerPage->user_id)
        <!-- 👑 Store Owner Quick Control Bar -->
        <div class="bg-gradient-to-r from-amber-500 via-amber-400 to-amber-500 text-slate-900 text-xs font-bold py-2 px-4 shadow-sm border-b border-amber-600 flex flex-wrap items-center justify-between gap-2 sticky top-0 z-50">
            <div class="flex items-center gap-2">
                <span class="bg-slate-900 text-amber-300 text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full tracking-wider shadow-2xs">👑 Store Owner</span>
                <span class="text-xs">आप अपनी दुकान <strong>{{ $sellerPage->page_title }}</strong> देख रहे हैं।</span>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('seller.dashboard') }}" class="inline-flex items-center gap-1.5 bg-indigo-900 hover:bg-indigo-800 text-white px-3 py-1.5 rounded-xl text-xs font-extrabold transition shadow-xs border border-indigo-700">
                    <i class="fa-solid fa-gauge-high text-indigo-300"></i>
                    <span>📊 सेलर डैशबोर्ड (Tools)</span>
                </a>
                <a href="{{ route('seller.minisite.edit') }}" class="inline-flex items-center gap-1.5 bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 rounded-xl text-xs font-bold transition shadow-xs">
                    <i class="fa-solid fa-pen-to-square text-amber-400"></i>
                    <span>✏️ दुकान का नाम, पता व लिंक्स एडिट करें</span>
                </a>
                <a href="{{ route('seller.products.index') }}" class="inline-flex items-center gap-1.5 bg-white hover:bg-gray-100 text-slate-900 px-3 py-1.5 rounded-xl text-xs font-bold transition shadow-xs border border-amber-600/30">
                    <i class="fa-solid fa-boxes-stacked text-blue-600"></i>
                    <span>📦 Products Manage</span>
                </a>
            </div>
        </div>
    @endif

    <!-- Top Announcement Bar -->
    <div class="bg-slate-900 text-white text-xs py-1.5 px-4 flex items-center justify-between gap-4">
        <div class="truncate text-left">
            <span>✨ Welcome to {{ $sellerPage->page_title }}</span>
            @if($sellerPage->enable_cod)
                <span class="hidden sm:inline">• 💵 Cash on Delivery Available</span>
            @endif
            @if($sellerPage->enable_whatsapp_order && $sellerPage->whatsapp_number)
                <span class="hidden md:inline">• 📲 Fast WhatsApp Ordering</span>
            @endif
        </div>
        <div class="shrink-0 flex items-center gap-2">
            @if(Auth::check() && Auth::id() === $sellerPage->user_id)
                <a href="{{ route('seller.minisite.edit') }}" class="text-amber-400 hover:text-amber-300 font-extrabold text-[11px] flex items-center gap-1 bg-slate-800 px-2 py-0.5 rounded-md border border-amber-400/40" title="दुकान की जानकारी व सेटिंग्स बदलें">
                    <i class="fa-solid fa-pen-to-square"></i> <span>दुकान एडिट करें</span>
                </a>
            @else
                <a href="{{ route('seller.minisite.edit') }}" class="text-amber-300 hover:text-white font-extrabold text-[11px] flex items-center gap-1 bg-slate-800/90 hover:bg-slate-700 px-2.5 py-0.5 rounded-md border border-amber-400/50 shadow-xs" title="दुकान मालिक लॉगिन / दुकान एडिट करें">
                    <i class="fa-solid fa-pen-to-square text-amber-400 text-[10px]"></i> <span>दुकान एडिट करें (Login)</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Main Navigation -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                <!-- Store Brand / Logo -->
                <a href="{{ route('minisite.show', $sellerPage->slug) }}" class="flex items-center gap-3 shrink-0 group">
                    @if($sellerPage->logo_url)
                        <img src="{{ $sellerPage->logo_url }}" alt="{{ $sellerPage->page_title }}" class="h-10 w-10 object-contain rounded-lg border border-gray-100 shadow-sm">
                    @else
                        <div class="h-10 w-10 rounded-lg bg-brand-custom text-white font-bold flex items-center justify-center text-lg shadow-sm">
                            {{ strtoupper(substr($sellerPage->page_title, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-lg text-gray-900 tracking-tight leading-tight group-hover:text-brand-custom transition-colors">{{ $sellerPage->page_title }}</span>
                            <span class="text-[10px] text-amber-600 font-bold bg-amber-50 hover:bg-amber-100 px-1.5 py-0.5 rounded border border-amber-300/80 inline-flex items-center gap-1" title="दुकान एडिट करने हेतु ऊपर या नीचे एडिट बटन दबाएं">
                                <i class="fa-solid fa-pen-to-square text-[9px]"></i> एडिट
                            </span>
                        </div>
                        @if($sellerPage->tagline)
                            <div class="text-xs text-gray-500 truncate max-w-[180px] sm:max-w-xs">{{ $sellerPage->tagline }}</div>
                        @endif
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center gap-6 text-sm font-semibold text-gray-700">
                    <a href="{{ route('minisite.show', $sellerPage->slug) }}" class="hover:text-brand-custom transition-colors {{ request()->routeIs('minisite.show') ? 'text-brand-custom' : '' }}">Home</a>
                    <a href="{{ route('minisite.products', $sellerPage->slug) }}" class="hover:text-brand-custom transition-colors {{ request()->routeIs('minisite.products') ? 'text-brand-custom' : '' }}">All Products</a>
                    <a href="{{ route('minisite.contact', $sellerPage->slug) }}" class="hover:text-brand-custom transition-colors {{ request()->routeIs('minisite.contact') ? 'text-brand-custom' : '' }}">Contact & About</a>
                    <a href="{{ route('seller.minisite.edit') }}" class="text-amber-700 hover:text-amber-900 font-bold flex items-center gap-1 bg-amber-50 hover:bg-amber-100 px-2.5 py-1 rounded-lg border border-amber-200 transition" title="दुकान का नाम, पता या सेटिंग्स एडिट करें">
                        <i class="fa-solid fa-pen-to-square text-xs text-amber-600"></i>
                        <span>दुकान एडिट करें</span>
                    </a>
                </nav>

                <!-- Search Bar (Desktop) -->
                <form action="{{ route('minisite.products', $sellerPage->slug) }}" method="GET" class="hidden lg:flex items-center relative flex-1 max-w-xs">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products in this store..." class="w-full pl-9 pr-4 py-1.5 text-xs bg-gray-100 border border-transparent rounded-full focus:bg-white focus:border-gray-300 focus:outline-none transition-all">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 text-gray-400 text-xs"></i>
                </form>

                <!-- Actions: Share + WhatsApp + Dashboard + Cart -->
                    @php
                        $storeShareUrl = route('minisite.show', $sellerPage->slug);
                        $storeShareWaMsg = urlencode("Namaste! Check out " . $sellerPage->page_title . " on VyaparIndia. Browse our entire catalog, wholesale rates, and place 1-click orders:\n" . $storeShareUrl);
                    @endphp
                    <a href="https://api.whatsapp.com/send?text={{ $storeShareWaMsg }}" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition shadow-2xs" title="Share Full Store on WhatsApp">
                        <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                        <span>Share Store</span>
                    </a>

                    <a href="{{ route('minisite.materialScanner', $sellerPage->slug) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 transition">
                        <i class="fa-solid fa-camera text-indigo-600"></i>
                        <span class="hidden sm:inline">Slip Scanner</span>
                    </a>

                    @if($sellerPage->whatsapp_number)
                        <a href="https://wa.me/{{ $sellerPage->clean_whatsapp_number }}?text=Hello%2C%20I%20have%20an%20inquiry%20about%20your%20products%20on%20{{ urlencode(route('minisite.show', $sellerPage->slug)) }}" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full bg-slate-100 text-gray-700 border border-gray-200 hover:bg-gray-200 transition-colors">
                            <i class="fa-brands fa-whatsapp text-sm text-emerald-600"></i>
                            <span>Chat</span>
                        </a>
                    @endif

                    @auth
                        @if(Auth::user()->is_seller())
                            <!-- ⚡ UNMISTAKABLE SELLER DASHBOARD BUTTON -->
                            <a href="{{ route('seller.dashboard') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-black rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-md shadow-blue-500/25 transition transform hover:-translate-y-0.5" title="Go to your Seller Dashboard">
                                <i class="fa-solid fa-gauge-high"></i>
                                <span>Seller Dashboard</span>
                            </a>
                        @endif
                    @endauth

                    <!-- Cart Drawer Button -->
                    <button onclick="toggleCartDrawer(true)" class="relative p-2.5 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-800 transition-colors">
                        <i class="fa-solid fa-bag-shopping text-base"></i>
                        <span id="cart-badge" class="absolute -top-1 -right-1 bg-red-600 text-white font-bold text-[10px] h-5 w-5 rounded-full flex items-center justify-center hidden">0</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Subnav / Search -->
        <div class="md:hidden border-t border-gray-100 px-4 py-2 bg-gray-50 flex items-center justify-between text-xs font-medium text-gray-600">
            <div class="flex items-center gap-3">
                <a href="{{ route('minisite.show', $sellerPage->slug) }}" class="hover:text-gray-900 {{ request()->routeIs('minisite.show') ? 'text-brand-custom font-bold' : '' }}">Home</a>
                <a href="{{ route('minisite.products', $sellerPage->slug) }}" class="hover:text-gray-900 {{ request()->routeIs('minisite.products') ? 'text-brand-custom font-bold' : '' }}">Products</a>
                <a href="{{ route('minisite.contact', $sellerPage->slug) }}" class="hover:text-gray-900 {{ request()->routeIs('minisite.contact') ? 'text-brand-custom font-bold' : '' }}">Contact</a>
                @auth
                    @if(Auth::user()->is_seller())
                        <a href="{{ route('seller.dashboard') }}" class="px-2 py-0.5 rounded-md bg-indigo-600 text-white font-bold flex items-center gap-1 shadow-2xs">
                            <i class="fa-solid fa-gauge-high text-[10px]"></i> Dashboard
                        </a>
                    @endif
                @endauth
            </div>
            <a href="{{ route('minisite.products', $sellerPage->slug) }}" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-magnifying-glass"></i> Search
            </a>
        </div>
    </header>

    @auth
        @if(Auth::user()->is_seller())
            <!-- ⚡ Sticky Floating Store Owner Hub Badge -->
            <div class="fixed bottom-20 right-4 z-50 bg-slate-900/95 text-white backdrop-blur-md shadow-2xl rounded-2xl p-2 sm:p-2.5 border border-slate-700 flex items-center gap-2 sm:gap-3 text-xs animate-in fade-in slide-in-from-bottom duration-300">
                <div class="flex items-center gap-1.5 pl-1">
                    <span class="flex h-2.5 w-2.5 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span class="font-extrabold text-slate-200 hidden sm:inline">Store Mode</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <a href="{{ route('seller.dashboard') }}" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 font-black text-white flex items-center gap-1.5 shadow-xs transition">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('seller.inventory.index') }}" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 font-bold text-slate-200 flex items-center gap-1 transition" title="Manage Stock & Prices">
                        <i class="fa-solid fa-boxes-stacked"></i>
                        <span class="hidden sm:inline">Stock</span>
                    </a>
                    <a href="{{ route('seller.minisite.edit') }}" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 font-bold text-slate-200 flex items-center gap-1 transition" title="Edit Store Details">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span class="hidden sm:inline">Edit</span>
                    </a>
                </div>
            </div>
        @endif
    @endauth

    <!-- Main Content Body -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Sticky Bottom Quick Cart Bar (Single-Page Multi-Product Order) -->
    <div id="sticky-bottom-cart-bar" class="fixed bottom-0 left-0 right-0 z-40 bg-slate-900/95 backdrop-blur-md text-white py-3 px-4 shadow-2xl border-t border-slate-800 hidden transform transition-all duration-300">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 cursor-pointer" onclick="toggleCartDrawer(true)">
                <div class="h-10 w-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-sm shadow-sm shrink-0">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-300">
                        <span id="sticky-cart-count" class="text-white font-black text-sm">0</span> Items in Cart
                    </div>
                    <div class="text-sm font-black text-amber-400" id="sticky-cart-total">₹0.00</div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="toggleCartDrawer(true)" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition">
                    View Bag
                </button>
                <button onclick="openQuickOrderModal()" class="px-4 sm:px-5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-black shadow-lg shadow-emerald-500/20 flex items-center gap-1.5 transition">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>1-Click Order</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Quick WhatsApp Order Modal with Automatic Database Save -->
    <div id="quick-order-modal" class="fixed inset-0 z-50 overflow-hidden hidden flex items-center justify-center p-4">
        <div onclick="closeQuickOrderModal()" class="absolute inset-0 bg-gray-900/60 backdrop-blur-xs"></div>
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 space-y-4 shadow-2xl relative z-10">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h3 class="font-black text-gray-900 text-sm">Quick Multi-Item WhatsApp Order</h3>
                    <p class="text-[11px] text-gray-400">Order will be saved in store & sent to WhatsApp</p>
                </div>
                <button onclick="closeQuickOrderModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="quickOrderForm" onsubmit="submitQuickOrder(event)" class="space-y-3">
                <div>
                    <label class="text-xs font-bold text-gray-700 block mb-1">Your Name <span class="text-rose-500">*</span></label>
                    <input type="text" id="qo_name" required placeholder="e.g. Rajesh Sharma" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold focus:border-indigo-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-700 block mb-1">WhatsApp Mobile <span class="text-rose-500">*</span></label>
                    <input type="tel" id="qo_phone" required placeholder="e.g. 9876543210" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold focus:border-indigo-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-700 block mb-1">Delivery Address & Area <span class="text-rose-500">*</span></label>
                    <input type="text" id="qo_address" required placeholder="Shop/House No, Street, City" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold focus:border-indigo-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-700 block mb-1">Payment Method</label>
                    <select id="qo_payment" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold">
                        <option value="cod">Cash on Delivery (COD)</option>
                        <option value="online">Online Payment / UPI</option>
                    </select>
                </div>

                <div class="pt-2">
                    <button type="submit" id="qo_submit_btn" class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md transition flex items-center justify-center gap-2">
                        <i class="fa-brands fa-whatsapp text-base"></i>
                        <span id="qo_btn_text">Confirm & Send to WhatsApp</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Floating WhatsApp Action Button -->
    @if($sellerPage->whatsapp_number)
        <a href="https://wa.me/{{ $sellerPage->clean_whatsapp_number }}?text=Hello%20{{ urlencode($sellerPage->page_title) }}%2C%20I%20am%20visiting%20your%20store%20and%20need%20assistance." target="_blank" class="fixed bottom-20 sm:bottom-6 right-6 z-30 flex items-center justify-center h-14 w-14 rounded-full bg-emerald-500 text-white shadow-xl hover:bg-emerald-600 hover:scale-105 active:scale-95 transition-all group" title="Chat on WhatsApp">
            <i class="fa-brands fa-whatsapp text-3xl"></i>
            <span class="absolute right-16 bg-slate-900 text-white text-xs py-1.5 px-3 rounded-lg shadow-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none hidden sm:block">
                Chat on WhatsApp
            </span>
        </a>
    @endif

    <!-- Sliding Cart Drawer Modal -->
    <div id="cart-drawer" class="fixed inset-0 z-50 overflow-hidden hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div onclick="toggleCartDrawer(false)" class="absolute inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div class="w-screen max-w-md bg-white shadow-2xl flex flex-col">
                <!-- Cart Header -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-bag-shopping text-gray-700"></i>
                        <h2 class="text-base font-bold text-gray-900" id="slide-over-title">Your Shopping Bag</h2>
                    </div>
                    <button onclick="toggleCartDrawer(false)" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Cart Items List Container -->
                <div class="flex-1 overflow-y-auto px-6 py-4" id="cart-items-container">
                    <!-- Populated by JavaScript -->
                </div>

                <!-- 🍽️ Food & Dining / Retail Order Service Mode Selector -->
                @php
                    $fModes = $sellerPage->food_service_modes;
                    $hasDineIn = !empty($fModes['dine_in']);
                    $hasTakeaway = !empty($fModes['takeaway']);
                    $hasDelivery = !empty($fModes['delivery']);
                    $modeCount = ($hasDineIn ? 1 : 0) + ($hasTakeaway ? 1 : 0) + ($hasDelivery ? 1 : 0);
                    $defaultMode = $hasDineIn ? 'dine_in' : ($hasDelivery ? 'delivery' : ($hasTakeaway ? 'takeaway' : 'delivery'));
                    $pkgCharge = floatval($fModes['packaging_charge'] ?? 0);
                    $tablesCount = intval($fModes['tables_count'] ?? 10);
                @endphp

                <div class="px-6 py-3 bg-amber-50/70 border-t border-amber-200/80 text-xs" id="cart-service-mode-box">
                    <!-- Mode Tabs (Hidden if only 1 mode is enabled by owner) -->
                    @if($modeCount > 1)
                        <div class="flex items-center gap-1.5 p-1 rounded-xl bg-amber-100/70 border border-amber-200 mb-3 text-[11px] font-black">
                            @if($hasDineIn)
                                <button type="button" onclick="selectCartOrderMode('dine_in')" id="btn_mode_dine_in" class="flex-1 py-1.5 rounded-lg transition text-center flex items-center justify-center gap-1 {{ $defaultMode === 'dine_in' ? 'bg-amber-600 text-white shadow-xs' : 'text-amber-900 hover:bg-amber-200/60' }}">
                                    <span>🪑 डाइन-इन (Table)</span>
                                </button>
                            @endif
                            @if($hasDelivery)
                                <button type="button" onclick="selectCartOrderMode('delivery')" id="btn_mode_delivery" class="flex-1 py-1.5 rounded-lg transition text-center flex items-center justify-center gap-1 {{ $defaultMode === 'delivery' ? 'bg-amber-600 text-white shadow-xs' : 'text-amber-900 hover:bg-amber-200/60' }}">
                                    <span>🛵 डिलीवरी (Home)</span>
                                </button>
                            @endif
                            @if($hasTakeaway)
                                <button type="button" onclick="selectCartOrderMode('takeaway')" id="btn_mode_takeaway" class="flex-1 py-1.5 rounded-lg transition text-center flex items-center justify-center gap-1 {{ $defaultMode === 'takeaway' ? 'bg-amber-600 text-white shadow-xs' : 'text-amber-900 hover:bg-amber-200/60' }}">
                                    <span>🥡 पार्सल (Pickup)</span>
                                </button>
                            @endif
                        </div>
                    @else
                        <!-- Single Mode Badge (Zero timepass for customer) -->
                        <div class="mb-2 text-[11px] font-black text-amber-900 flex items-center gap-1.5">
                            @if($hasDineIn)
                                <span class="px-2 py-0.5 rounded-md bg-amber-200/70 text-amber-950">🪑 टेबल डाइन-इन ऑर्डर (Table Dine-In Only)</span>
                            @elseif($hasDelivery)
                                <span class="px-2 py-0.5 rounded-md bg-amber-200/70 text-amber-950">🛵 क्लाउड किचन होम डिलीवरी (Home Delivery Only)</span>
                            @elseif($hasTakeaway)
                                <span class="px-2 py-0.5 rounded-md bg-amber-200/70 text-amber-950">🥡 काउंटर टेकअवे पार्सल (Takeaway Only)</span>
                            @endif
                        </div>
                    @endif

                    <input type="hidden" id="selected_order_mode" value="{{ $defaultMode }}">
                    <input type="hidden" id="store_packaging_charge" value="{{ $pkgCharge }}">
                    <input type="hidden" id="store_show_bill_amount" value="{{ !empty($fModes['show_bill_amount'] ?? true) ? '1' : '0' }}">
                    <input type="hidden" id="store_show_upi_payment" value="{{ !empty($fModes['show_upi_payment'] ?? true) ? '1' : '0' }}">
                    <input type="hidden" id="store_google_review_link" value="{{ $sellerPage->google_review_link ?? '' }}">
                    <input type="hidden" id="store_instagram_link" value="{{ $sellerPage->instagram_link ?? '' }}">

                    <!-- Fields for Dine-In -->
                    <div id="mode_fields_dine_in" class="{{ $defaultMode === 'dine_in' ? '' : 'hidden' }} space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-[11px] font-bold text-gray-700">अपनी टेबल चुनें (Table No.):</label>
                            <select id="order_table_number" class="px-2.5 py-1 text-xs font-black border border-amber-300 rounded-lg bg-white text-gray-900 focus:outline-none">
                                @for($t = 1; $t <= $tablesCount; $t++)
                                    <option value="{{ $t }}">Table #{{ $t }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Fields for Cloud Kitchen / Delivery -->
                    <div id="mode_fields_delivery" class="{{ $defaultMode === 'delivery' ? '' : 'hidden' }} space-y-2">
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" id="order_delivery_name" placeholder="आपका नाम (Name)" class="w-full px-2.5 py-1.5 text-xs font-bold border border-amber-300 rounded-lg bg-white">
                            <input type="tel" id="order_delivery_phone" placeholder="मोबाइल नंबर (Phone)" class="w-full px-2.5 py-1.5 text-xs font-bold font-mono border border-amber-300 rounded-lg bg-white">
                        </div>
                        <input type="text" id="order_delivery_address" placeholder="डिलीवरी पता / मकान नं., कालोनी (Full Address)" class="w-full px-2.5 py-1.5 text-xs font-bold border border-amber-300 rounded-lg bg-white">

                        <!-- 📍 Live GPS Pinning for Delivery Rider -->
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="detectCustomerDeliveryGps()" id="btn_cust_delivery_gps" class="w-full py-1.5 px-3 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-900 text-[11px] font-bold flex items-center justify-center gap-1.5 transition">
                                <i class="fa-solid fa-crosshairs text-emerald-600"></i>
                                <span id="cust_gps_label">📍 मेरा लाइव GPS लोकेशन जोड़ें (Maps Pin)</span>
                            </button>
                            <input type="hidden" id="order_delivery_gps" value="">
                        </div>
                    </div>

                    <!-- Fields for Takeaway / Pickup -->
                    <div id="mode_fields_takeaway" class="{{ $defaultMode === 'takeaway' ? '' : 'hidden' }} space-y-2">
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" id="order_takeaway_name" placeholder="आपका नाम (Name)" class="w-full px-2.5 py-1.5 text-xs font-bold border border-amber-300 rounded-lg bg-white">
                            <input type="text" id="order_takeaway_time" value="20 मिनट में (In 20 mins)" placeholder="पिकअप का समय" class="w-full px-2.5 py-1.5 text-xs font-bold border border-amber-300 rounded-lg bg-white">
                        </div>
                    </div>
                </div>

                <!-- Cart Footer & Checkout -->
                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 space-y-2.5" id="cart-footer">
                    <div class="flex items-center justify-between text-xs text-gray-600">
                        <span>Items Subtotal:</span>
                        <span id="cart-subtotal" class="font-extrabold text-gray-900">₹0.00</span>
                    </div>

                    @if($pkgCharge > 0)
                        <div class="flex items-center justify-between text-xs text-gray-600" id="cart-packaging-row">
                            <span>Packing / Delivery Fee:</span>
                            <span class="font-bold text-gray-800">₹{{ number_format($pkgCharge, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between text-sm pt-1 border-t border-gray-200">
                        <span class="font-black text-gray-900">Final Total Bill (कुल बिल):</span>
                        <span id="cart-grandtotal" class="font-black text-emerald-700 text-lg">₹0.00</span>
                    </div>

                    <div class="pt-2 space-y-2">
                        @if($sellerPage->whatsapp_number)
                            <button onclick="orderCartViaWhatsapp()" class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md transition-all flex items-center justify-center gap-2">
                                <i class="fa-brands fa-whatsapp text-base"></i>
                                <span>Confirm Order & Send to WhatsApp</span>
                            </button>
                        @endif

                        <a href="{{ route('minisite.checkout', $sellerPage->slug) }}" class="w-full py-2 px-4 rounded-xl bg-gray-900 hover:bg-gray-800 text-white font-bold text-xs shadow-xs transition-all flex items-center justify-center gap-2">
                            <span>Direct Online Checkout</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>

                        <!-- Google Review & Instagram Social Badges in Cart -->
                        @if($sellerPage->google_review_link || $sellerPage->instagram_link)
                            <div class="pt-2 flex items-center justify-center gap-3 text-[11px] font-bold text-gray-500 border-t border-gray-200/60">
                                @if($sellerPage->google_review_link)
                                    <a href="{{ $sellerPage->google_review_link }}" target="_blank" class="hover:text-amber-700 flex items-center gap-1.5 transition">
                                        <i class="fa-brands fa-google text-amber-500"></i>
                                        <span>Google Review ⭐</span>
                                    </a>
                                @endif
                                @if($sellerPage->instagram_link)
                                    <a href="{{ $sellerPage->instagram_link }}" target="_blank" class="hover:text-rose-600 flex items-center gap-1.5 transition">
                                        <i class="fa-brands fa-instagram text-rose-500"></i>
                                        <span>Follow on Instagram 📸</span>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Store Footer -->
    <footer class="bg-white border-t border-gray-200 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Brand Info -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2.5">
                        @if($sellerPage->logo_url)
                            <img src="{{ $sellerPage->logo_url }}" alt="{{ $sellerPage->page_title }}" class="h-8 w-8 object-contain rounded">
                        @endif
                        <span class="font-extrabold text-gray-900 text-lg">{{ $sellerPage->page_title }}</span>
                    </div>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        {{ $sellerPage->tagline ?: ($sellerPage->welcome_message ?: 'Direct manufacturer and verified supplier on VyaparIndia.') }}
                    </p>
                    @php
                        $firmProfile = $sellerPage->user ? $sellerPage->user->sellerProfile : null;
                    @endphp
                    @if($firmProfile && $firmProfile->gst_number)
                        <div class="text-[11px] font-mono text-gray-600 pt-0.5 flex items-center gap-1.5">
                            <span class="px-1.5 py-0.5 bg-gray-100 rounded text-[10px] font-bold text-gray-800">GSTIN</span>
                            <span class="font-bold text-gray-900">{{ $firmProfile->gst_number }}</span>
                        </div>
                    @endif
                    @php
                        $fullAddr = $sellerPage->address ?: ($firmProfile ? ($firmProfile->address . ', ' . $firmProfile->city . ', ' . $firmProfile->state) : '');
                        $cityPin = ($sellerPage->city ? $sellerPage->city : '') . ($sellerPage->pincode ? ' - ' . $sellerPage->pincode : '');
                        $dispAddr = trim($fullAddr . ' ' . $cityPin);
                        $mapLink = $sellerPage->universal_map_link ?: ('https://www.google.com/maps/search/?api=1&query=' . urlencode($sellerPage->page_title . ' ' . $dispAddr));
                    @endphp
                    @if($sellerPage->address || ($firmProfile && ($firmProfile->address || $firmProfile->city)) || $sellerPage->google_map_link)
                        @if(!empty($dispAddr))
                            <div class="text-xs text-gray-600 flex items-start gap-2 pt-1">
                                <i class="fa-solid fa-location-dot mt-0.5 text-rose-500 shrink-0"></i>
                                <span>{{ $dispAddr }}</span>
                            </div>
                        @endif
                        <div class="pt-1.5">
                            <a href="{{ $mapLink }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 px-3.5 py-1.5 rounded-xl border border-blue-200 transition shadow-2xs hover:shadow-xs">
                                <i class="fa-solid fa-map-location-dot text-blue-600"></i>
                                <span>Google Maps GPS Location</span>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Quick Navigation & Policies -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900 mb-3">Quick Navigation</h3>
                    <ul class="space-y-2 text-xs text-gray-600 font-medium">
                        <li><a href="{{ route('minisite.show', $sellerPage->slug) }}" class="hover:text-brand-custom flex items-center gap-1.5"><i class="fa-solid fa-house text-[10px] text-gray-400"></i> Store Home</a></li>
                        <li><a href="{{ route('minisite.products', $sellerPage->slug) }}" class="hover:text-brand-custom flex items-center gap-1.5"><i class="fa-solid fa-boxes-stacked text-[10px] text-gray-400"></i> All Products Catalog</a></li>
                        <li><a href="{{ route('minisite.contact', $sellerPage->slug) }}" class="hover:text-brand-custom flex items-center gap-1.5"><i class="fa-solid fa-address-card text-[10px] text-gray-400"></i> Contact & About Store</a></li>
                        <li><a href="{{ route('minisite.contact', $sellerPage->slug) }}#policies" class="hover:text-brand-custom flex items-center gap-1.5"><i class="fa-solid fa-truck text-[10px] text-gray-400"></i> Shipping & Return Policies</a></li>
                        @if(Auth::check() && Auth::id() === $sellerPage->user_id)
                            <li class="pt-2 border-t border-gray-100 space-y-1.5">
                                <a href="{{ route('seller.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 font-extrabold flex items-center gap-1.5">
                                    <i class="fa-solid fa-gauge-high text-[11px]"></i> 📊 मुख्य सेलर डैशबोर्ड (Seller Tools)
                                </a>
                                <a href="{{ route('seller.minisite.edit') }}" class="text-emerald-700 hover:text-emerald-900 font-extrabold flex items-center gap-1.5">
                                    <i class="fa-solid fa-pen-to-square text-[11px]"></i> ✏️ दुकान का नाम, पता व लिंक्स एडिट करें
                                </a>
                            </li>
                        @else
                            <li class="pt-2 border-t border-gray-100">
                                <a href="{{ route('seller.minisite.edit') }}" class="text-amber-700 hover:text-amber-900 font-black flex items-center gap-1.5 bg-amber-50 hover:bg-amber-100 p-2 rounded-xl border border-amber-200 transition" title="दुकान मालिक: यहाँ से लॉगिन कर अपनी दुकान एडिट करें">
                                    <i class="fa-solid fa-lock text-amber-600"></i>
                                    <span>🔐 दुकान मालिक लॉगिन / दुकान एडिट करें</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>

                <!-- Verified Store / Badges & Payment -->
                <div class="space-y-3 bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <div class="flex items-center gap-2 text-emerald-600 font-bold text-xs">
                        <i class="fa-solid fa-shield-check text-sm"></i>
                        <span>Verified Store on VyaparIndia</span>
                    </div>
                    <p class="text-[11px] text-gray-500 leading-normal">
                        Orders are directly fulfilled by {{ $sellerPage->page_title }} with genuine warranty and verified dispatch.
                    </p>
                    @if($sellerPage->upi_id)
                        <div class="text-[11px] font-mono bg-white p-2 rounded-lg border border-gray-200">
                            <span class="text-gray-400 font-sans block text-[9px] uppercase">Official UPI ID</span>
                            <span class="font-bold text-gray-900">{{ $sellerPage->upi_id }}</span>
                        </div>
                    @endif
                    <div class="flex items-center gap-3 pt-1 text-gray-400 text-sm">
                        @if($sellerPage->instagram_link)
                            <a href="{{ $sellerPage->instagram_link }}" target="_blank" class="hover:text-pink-600"><i class="fa-brands fa-instagram"></i></a>
                        @endif
                        @if($sellerPage->facebook_link)
                            <a href="{{ $sellerPage->facebook_link }}" target="_blank" class="hover:text-blue-600"><i class="fa-brands fa-facebook"></i></a>
                        @endif
                        @if($sellerPage->support_phone)
                            <a href="tel:{{ $sellerPage->support_phone }}" class="hover:text-gray-800"><i class="fa-solid fa-phone"></i></a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-3">
                <p>&copy; {{ date('Y') }} {{ $sellerPage->page_title }}. All rights reserved.</p>
                <div class="flex items-center gap-1 text-[11px] text-gray-400">
                    <span>Powered by</span>
                    <a href="{{ route('home') }}" class="font-bold text-gray-700 hover:text-brand-custom">VyaparIndia Platform</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Global Cart Script for Mini-Site -->
    <script>
        const STORE_SLUG = '{{ $sellerPage->slug }}';
        const WHATSAPP_NUM = '{{ $sellerPage->clean_whatsapp_number }}';
        const STORE_NAME = '{{ addslashes($sellerPage->page_title) }}';

        function getCartKey() {
            return 'vyapar_cart_' + STORE_SLUG;
        }

        function getCart() {
            try {
                return JSON.parse(localStorage.getItem(getCartKey())) || [];
            } catch (e) {
                return [];
            }
        }

        function saveCart(cart) {
            localStorage.setItem(getCartKey(), JSON.stringify(cart));
            updateCartUI();
        }

        function stepCardQty(prodId, delta) {
            const qtyEl = document.getElementById(`card_qty_${prodId}`);
            if (!qtyEl) return;
            let val = Math.max(1, (parseInt(qtyEl.value) || 1) + delta);
            qtyEl.value = val;
            recalcCardTotal(prodId);
        }

        function onCardQtyInput(prodId) {
            const qtyEl = document.getElementById(`card_qty_${prodId}`);
            if (qtyEl) {
                qtyEl.value = Math.max(1, parseInt(qtyEl.value) || 1);
            }
            recalcCardTotal(prodId);
        }

        function onCardVariantChanged(prodId) {
            recalcCardTotal(prodId);
        }

        function recalcCardTotal(prodId) {
            const box = document.getElementById(`card_box_${prodId}`);
            const qtyEl = document.getElementById(`card_qty_${prodId}`);
            const qty = Math.max(1, parseInt(qtyEl?.value || 1));
            
            let unitPrice = parseFloat(box?.getAttribute('data-prod-price') || 0);
            let variantName = '';
            let variantId = null;

            const varSelect = document.getElementById(`card_variant_${prodId}`);
            if (varSelect) {
                if (varSelect.tagName === 'SELECT') {
                    const opt = varSelect.options[varSelect.selectedIndex];
                    if (opt) {
                        unitPrice = parseFloat(opt.getAttribute('data-price') || unitPrice);
                        variantName = opt.getAttribute('data-name') || '';
                        variantId = parseInt(opt.value);
                    }
                } else if (varSelect.tagName === 'INPUT') {
                    unitPrice = parseFloat(varSelect.getAttribute('data-price') || unitPrice);
                    variantName = varSelect.getAttribute('data-name') || '';
                    variantId = parseInt(varSelect.value);
                }
            }

            const totalEl = document.getElementById(`card_total_price_${prodId}`);
            const unitEl = document.getElementById(`card_unit_price_${prodId}`);
            if (totalEl) totalEl.innerText = '₹' + (unitPrice * qty).toFixed(2);
            if (unitEl) unitEl.innerText = '₹' + unitPrice.toFixed(2);
        }

        function addCardToBag(prodId) {
            const box = document.getElementById(`card_box_${prodId}`);
            const qtyEl = document.getElementById(`card_qty_${prodId}`);
            const qty = Math.max(1, parseInt(qtyEl?.value || 1));

            let basePrice = parseFloat(box?.getAttribute('data-prod-price') || 0);
            let prodName = box?.getAttribute('data-prod-name') || 'Item';
            let prodImage = box?.getAttribute('data-prod-image') || '';
            let prodSlug = box?.getAttribute('data-prod-slug') || '';
            let groupName = box?.getAttribute('data-prod-group') || '';
            let dietType = box?.getAttribute('data-prod-diet') || 'veg';
            let variantId = null;
            let variantName = '';

            const varSelect = document.getElementById(`card_variant_${prodId}`);
            if (varSelect) {
                if (varSelect.tagName === 'SELECT') {
                    const opt = varSelect.options[varSelect.selectedIndex];
                    if (opt) {
                        basePrice = parseFloat(opt.getAttribute('data-price') || basePrice);
                        variantName = opt.getAttribute('data-name') || '';
                        variantId = parseInt(opt.value);
                    }
                } else if (varSelect.tagName === 'INPUT') {
                    basePrice = parseFloat(varSelect.getAttribute('data-price') || basePrice);
                    variantName = varSelect.getAttribute('data-name') || '';
                    variantId = parseInt(varSelect.value);
                }
            }

            const cartItem = {
                id: prodId,
                cart_key: `${prodId}_${variantId || 'base'}`,
                name: prodName,
                price: basePrice,
                image: prodImage,
                slug: prodSlug,
                variant_id: variantId,
                variant_name: variantName,
                group_name: groupName,
                diet_type: dietType,
            };

            addToCart(cartItem, qty, true);

            const btn = document.getElementById(`btn_add_bag_${prodId}`);
            if (btn) {
                const orig = btn.innerHTML;
                btn.innerHTML = `<i class="fa-solid fa-check text-emerald-600"></i> Added!`;
                setTimeout(() => { btn.innerHTML = orig; }, 1600);
            }
        }

        function addToCart(product, quantity = 1, openDrawer = true) {
            let cart = getCart();
            const cartKey = product.cart_key || (product.id + (product.variant_id ? '_' + product.variant_id : '_base'));
            let index = cart.findIndex(item => (item.cart_key || (item.id + (item.variant_id ? '_' + item.variant_id : '_base'))) === cartKey);
            
            if (index > -1) {
                cart[index].quantity += quantity;
            } else {
                const nameLower = (product.name || '').toLowerCase();
                const isNonVeg = product.diet_type === 'non_veg' || nameLower.includes('chicken') || nameLower.includes('mutton') || nameLower.includes('egg') || nameLower.includes('fish') || nameLower.includes('prawn');
                
                cart.push({
                    id: product.id,
                    cart_key: cartKey,
                    name: product.name,
                    price: parseFloat(product.price),
                    image: product.image_url || product.image || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=300',
                    slug: product.slug,
                    variant_id: product.variant_id || null,
                    variant_name: product.variant_name || '',
                    group_name: product.group_name || '',
                    diet_type: isNonVeg ? 'non_veg' : 'veg',
                    quantity: quantity
                });
            }
            saveCart(cart);
            if (openDrawer) {
                toggleCartDrawer(true);
            }
        }

        function updateQuantity(cartKey, delta) {
            let cart = getCart();
            let index = cart.findIndex(item => (item.cart_key || (item.id + (item.variant_id ? '_' + item.variant_id : '_base'))) == cartKey);
            if (index > -1) {
                cart[index].quantity += delta;
                if (cart[index].quantity <= 0) {
                    cart.splice(index, 1);
                }
                saveCart(cart);
            }
        }

        function removeFromCart(cartKey) {
            let cart = getCart().filter(item => (item.cart_key || (item.id + (item.variant_id ? '_' + item.variant_id : '_base'))) != cartKey);
            saveCart(cart);
        }

        function selectCartOrderMode(mode) {
            const modeInput = document.getElementById('selected_order_mode');
            if (modeInput) modeInput.value = mode;

            ['dine_in', 'delivery', 'takeaway'].forEach(m => {
                const btn = document.getElementById(`btn_mode_${m}`);
                const fields = document.getElementById(`mode_fields_${m}`);
                if (btn) {
                    if (m === mode) {
                        btn.className = "flex-1 py-1.5 rounded-lg transition text-center flex items-center justify-center gap-1 bg-amber-600 text-white shadow-xs font-black";
                    } else {
                        btn.className = "flex-1 py-1.5 rounded-lg transition text-center flex items-center justify-center gap-1 text-amber-900 hover:bg-amber-200/60 font-black";
                    }
                }
                if (fields) {
                    if (m === mode) fields.classList.remove('hidden');
                    else fields.classList.add('hidden');
                }
            });

            updateCartUI();
        }

        function toggleCartDrawer(show) {
            const drawer = document.getElementById('cart-drawer');
            if (show) {
                drawer.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            } else {
                drawer.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        function updateCartUI() {
            const cart = getCart();
            const badge = document.getElementById('cart-badge');
            const container = document.getElementById('cart-items-container');
            const subtotalEl = document.getElementById('cart-subtotal');
            const grandtotalEl = document.getElementById('cart-grandtotal');
            const footer = document.getElementById('cart-footer');
            const modeBox = document.getElementById('cart-service-mode-box');

            let totalCount = 0;
            let subtotal = 0;

            cart.forEach(item => {
                totalCount += item.quantity;
                subtotal += item.price * item.quantity;
            });

            // Calculate packaging fee based on selected mode
            const currentMode = document.getElementById('selected_order_mode')?.value || 'dine_in';
            const basePkgCharge = parseFloat(document.getElementById('store_packaging_charge')?.value || 0);
            const appliedPkgCharge = (currentMode === 'delivery' || currentMode === 'takeaway') ? basePkgCharge : 0;
            const grandTotal = subtotal + appliedPkgCharge;

            if (badge) {
                if (totalCount > 0) {
                    badge.innerText = totalCount;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }

            if (subtotalEl) {
                subtotalEl.innerText = '₹' + subtotal.toFixed(2);
            }
            if (grandtotalEl) {
                grandtotalEl.innerText = '₹' + grandTotal.toFixed(2);
            }

            // Update Sticky Bottom Quick Bar
            const stickyBar = document.getElementById('sticky-bottom-cart-bar');
            const stickyCount = document.getElementById('sticky-cart-count');
            const stickyTotal = document.getElementById('sticky-cart-total');

            if (stickyBar && stickyCount && stickyTotal) {
                if (totalCount > 0) {
                    stickyCount.innerText = totalCount;
                    stickyTotal.innerText = '₹' + grandTotal.toFixed(2);
                    stickyBar.classList.remove('hidden');
                } else {
                    stickyBar.classList.add('hidden');
                }
            }

            // Auto-detect table from URL parameter (e.g. ?table=3)
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('table')) {
                const tableVal = urlParams.get('table');
                const tableSelect = document.getElementById('order_table_number');
                if (tableSelect) tableSelect.value = tableVal;
            }

            if (container) {
                if (cart.length === 0) {
                    container.innerHTML = `
                        <div class="flex flex-col items-center justify-center h-64 text-center text-gray-400">
                            <i class="fa-solid fa-cart-shopping text-4xl mb-3 text-gray-300"></i>
                            <p class="text-sm font-semibold text-gray-600">Your shopping bag is empty</p>
                            <p class="text-xs text-gray-400 mt-1">Browse our menu and add items to order</p>
                            <a href="{{ route('minisite.products', $sellerPage->slug) }}" onclick="toggleCartDrawer(false)" class="mt-4 px-4 py-2 rounded-xl bg-gray-900 text-white text-xs font-semibold hover:bg-gray-800">
                                Explore Menu & Dishes
                            </a>
                        </div>
                    `;
                    if (footer) footer.classList.add('opacity-50', 'pointer-events-none');
                    if (modeBox) modeBox.classList.add('opacity-50', 'pointer-events-none');
                } else {
                    if (footer) footer.classList.remove('opacity-50', 'pointer-events-none');
                    if (modeBox) modeBox.classList.remove('opacity-50', 'pointer-events-none');
                    let html = '<div class="divide-y divide-gray-100">';
                    cart.forEach(item => {
                        const itemKey = item.cart_key || (item.id + (item.variant_id ? '_' + item.variant_id : '_base'));
                        const isVeg = item.diet_type === 'veg';
                        const portionTxt = item.variant_name ? `<span class="text-[10px] text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded font-black">${item.variant_name}</span>` : '';
                        const groupTxt = item.group_name ? `<span class="text-[10px] text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded font-medium">${item.group_name}</span>` : '';

                        html += `
                            <div class="py-3.5 flex items-center gap-3">
                                <div class="relative shrink-0">
                                    <img src="${item.image}" alt="${item.name}" class="h-14 w-14 object-cover rounded-xl border border-gray-100">
                                    <span class="absolute -top-1 -left-1 h-3.5 w-3.5 rounded-full ${isVeg ? 'bg-emerald-600' : 'bg-rose-600'} border-2 border-white shadow-2xs" title="${isVeg ? 'Veg' : 'Non-Veg'}"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-xs text-gray-900 truncate">${item.name}</div>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        ${portionTxt}
                                        ${groupTxt}
                                    </div>
                                    <div class="text-xs font-black text-gray-900 mt-1">₹${(item.price * item.quantity).toFixed(2)} <span class="text-[10px] text-gray-400 font-normal">(₹${item.price.toFixed(2)} ea)</span></div>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <div class="flex items-center border border-gray-200 rounded-lg bg-gray-50">
                                            <button onclick="updateQuantity('${itemKey}', -1)" class="px-2 py-0.5 text-xs text-gray-700 hover:bg-gray-200 font-black">-</button>
                                            <span class="px-2 text-xs font-bold text-gray-900">${item.quantity}</span>
                                            <button onclick="updateQuantity('${itemKey}', 1)" class="px-2 py-0.5 text-xs text-gray-700 hover:bg-gray-200 font-black">+</button>
                                        </div>
                                        <button onclick="removeFromCart('${itemKey}')" class="text-xs text-red-500 hover:text-red-700 ml-auto p-1">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    container.innerHTML = html;
                }
            }
        }

        function detectCustomerDeliveryGps() {
            const btn = document.getElementById('btn_cust_delivery_gps');
            const label = document.getElementById('cust_gps_label');
            const hiddenGps = document.getElementById('order_delivery_gps');
            const addrInput = document.getElementById('order_delivery_address');

            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser.');
                return;
            }

            if (label) label.innerText = 'GPS ढूँढा जा रहा है...';

            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    const mapsUrl = `https://www.google.com/maps?q=${lat},${lng}`;
                    if (hiddenGps) hiddenGps.value = mapsUrl;

                    if (label) label.innerText = `✓ GPS Pin Locked (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
                    if (btn) {
                        btn.className = "w-full py-1.5 px-3 rounded-lg bg-emerald-600 text-white text-[11px] font-bold flex items-center justify-center gap-1.5 shadow-xs";
                    }
                    if (addrInput && !addrInput.value) {
                        addrInput.value = `Live GPS Pin (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
                    }
                },
                function(err) {
                    alert('Location access denied or unavailable: ' + err.message);
                    if (label) label.innerText = '📍 मेरा लाइव GPS लोकेशन जोड़ें';
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }

        function orderCartViaWhatsapp() {
            const cart = getCart();
            if (!WHATSAPP_NUM || cart.length === 0) {
                alert('Please add items to cart before ordering via WhatsApp.');
                return;
            }

            const mode = document.getElementById('selected_order_mode')?.value || 'dine_in';
            const basePkgCharge = parseFloat(document.getElementById('store_packaging_charge')?.value || 0);
            const appliedPkgCharge = (mode === 'delivery' || mode === 'takeaway') ? basePkgCharge : 0;

            let modeTitle = '';
            let customerDetails = '';

            if (mode === 'dine_in') {
                const tableNo = document.getElementById('order_table_number')?.value || '1';
                modeTitle = `🪑 *Dine-In (डाइन-इन टेबल ऑर्डर)*\n🪑 *Table Number:* Table #${tableNo}\n`;
            } else if (mode === 'delivery') {
                const name = document.getElementById('order_delivery_name')?.value.trim();
                const phone = document.getElementById('order_delivery_phone')?.value.trim();
                const addr = document.getElementById('order_delivery_address')?.value.trim();
                const gpsLink = document.getElementById('order_delivery_gps')?.value || '';

                if (!name || !phone || !addr) {
                    alert('कृपया होम डिलीवरी हेतु अपना नाम, मोबाइल नंबर और पूरा पता अवश्य भरें!');
                    return;
                }
                modeTitle = `🛵 *Cloud Kitchen / Home Delivery (होम डिलीवरी)*\n`;
                customerDetails = `👤 *Customer Name:* ${name}\n📞 *Phone:* ${phone}\n📍 *Delivery Address:* ${addr}\n`;
                if (gpsLink) {
                    customerDetails += `🗺️ *Google Maps GPS Pin:* ${gpsLink}\n`;
                }
            } else {
                const name = document.getElementById('order_takeaway_name')?.value.trim() || 'ग्राहक';
                const time = document.getElementById('order_takeaway_time')?.value.trim() || '20 मिनट में';
                modeTitle = `🥡 *Takeaway / Parcel Pickup (काउंटर पिकअप)*\n`;
                customerDetails = `👤 *Customer Name:* ${name}\n⏱️ *Pickup Time:* ${time}\n`;
            }

            let text = `🛎️ *नया ऑर्डर प्राप्त हुआ - ${STORE_NAME}!*\n━━━━━━━━━━━━━━━━━━━━━\n${modeTitle}${customerDetails}━━━━━━━━━━━━━━━━━━━━━\n📋 *ऑर्डर किए गए आइटम्स:*\n`;

            let subtotal = 0;
            cart.forEach((item, index) => {
                let itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                const dietMarker = item.diet_type === 'non_veg' ? '🔴' : '🟢';
                const varTxt = item.variant_name ? ` (${item.variant_name})` : '';
                const groupTxt = item.group_name ? ` [${item.group_name}]` : '';
                text += `${index + 1}. ${dietMarker} *${item.name}*${varTxt}${groupTxt}\n   ${item.quantity} × ₹${item.price.toFixed(2)} = ₹${itemTotal.toFixed(2)}\n`;
            });

            const grandTotal = subtotal + appliedPkgCharge;
            const showBillAmount = document.getElementById('store_show_bill_amount')?.value === '1';
            const showUpiPayment = document.getElementById('store_show_upi_payment')?.value === '1';
            const googleReviewLink = document.getElementById('store_google_review_link')?.value.trim();
            const instagramLink = document.getElementById('store_instagram_link')?.value.trim();

            if (showBillAmount) {
                text += `━━━━━━━━━━━━━━━━━━━━━\n`;
                text += `🧾 *Items Subtotal:* ₹${subtotal.toFixed(2)}\n`;
                if (appliedPkgCharge > 0) {
                    text += `📦 *Packaging Fee:* ₹${appliedPkgCharge.toFixed(2)}\n`;
                }
                text += `💵 *Final Bill Amount (कुल राशि):* ₹${grandTotal.toFixed(2)}\n`;
                text += `━━━━━━━━━━━━━━━━━━━━━\n`;
            } else {
                text += `━━━━━━━━━━━━━━━━━━━━━\n`;
                text += `ℹ️ *नोट:* कुल बिल राशि व भुगतान की जानकारी दुकान द्वारा सीधे कन्फर्म की जाएगी।\n`;
                text += `━━━━━━━━━━━━━━━━━━━━━\n`;
            }

            if (showUpiPayment && showBillAmount) {
                const upiId = '{{ $sellerPage->upi_id ?: "7828289433@upi" }}';
                const upiPayUrl = `upi://pay?pa=${encodeURIComponent(upiId)}&pn=${encodeURIComponent(STORE_NAME)}&am=${grandTotal.toFixed(2)}&cu=INR`;
                text += `💳 *तुरंत UPI भुगतान लिंक (PhonePe / GPay / Paytm):*\n${upiPayUrl}\n\n`;
            }

            // Google Review & Instagram Follow integration
            if (googleReviewLink || instagramLink) {
                text += `⭐ *कस्टमर कनेक्ट व स्पेशल ऑफर्स:*\n`;
                if (googleReviewLink) {
                    text += `🌟 Google पर हमें 5-Star रिव्यू अवश्य दें:\n${googleReviewLink}\n`;
                }
                if (instagramLink) {
                    text += `📸 Instagram पर हमें फॉलो करें:\n${instagramLink}\n`;
                }
                text += `\n`;
            }

            text += `कृपया ऑर्डर कन्फर्म करें। धन्यवाद! 🙏`;

            const cleanNum = WHATSAPP_NUM.replace(/[^0-9]/g, '');
            const finalNum = cleanNum.length === 10 ? ('91' + cleanNum) : cleanNum;
            const waUrl = `https://wa.me/${finalNum}?text=${encodeURIComponent(text)}`;

            // Optional background order logging
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            fetch("{{ route('minisite.quickOrder', $sellerPage->slug) }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrfToken },
                body: JSON.stringify({
                    customer_name: (mode === 'delivery' ? document.getElementById('order_delivery_name')?.value : (mode === 'takeaway' ? document.getElementById('order_takeaway_name')?.value : 'Dine-In Customer')),
                    customer_phone: (mode === 'delivery' ? document.getElementById('order_delivery_phone')?.value : '9999999999'),
                    customer_address: (mode === 'delivery' ? document.getElementById('order_delivery_address')?.value : (mode === 'dine_in' ? ('Table #' + (document.getElementById('order_table_number')?.value || '1')) : 'Takeaway Pickup')),
                    payment_method: 'whatsapp',
                    items: cart
                })
            }).catch(e => console.log('Order log silent catch:', e));

            window.open(waUrl, '_blank');
        }

        function buySingleOnWhatsapp(productName, productPrice, productUrl) {
            if (!WHATSAPP_NUM) {
                alert('Seller has not configured WhatsApp number yet.');
                return;
            }
            let text = `*Hi ${STORE_NAME}!*\nI want to order:\n\n🛍️ *Product:* ${productName}\n💰 *Price:* ₹${productPrice}\n🔗 *Link:* ${productUrl}\n\nPlease share order & dispatch details.`;
            const url = `https://wa.me/${WHATSAPP_NUM}?text=${encodeURIComponent(text)}`;
            window.open(url, '_blank');
        }

        function openQuickOrderModal() {
            const cart = getCart();
            if (cart.length === 0) {
                alert('Pehle bag me kam se kam 1 item add karein.');
                return;
            }
            document.getElementById('quick-order-modal').classList.remove('hidden');
        }

        function closeQuickOrderModal() {
            document.getElementById('quick-order-modal').classList.add('hidden');
        }

        function submitQuickOrder(e) {
            e.preventDefault();
            const cart = getCart();
            if (cart.length === 0) return;

            const name = document.getElementById('qo_name').value.trim();
            const phone = document.getElementById('qo_phone').value.trim();
            const address = document.getElementById('qo_address').value.trim();
            const payment = document.getElementById('qo_payment').value;

            const submitBtn = document.getElementById('qo_submit_btn');
            const btnText = document.getElementById('qo_btn_text');
            submitBtn.disabled = true;
            btnText.innerText = "Order Save Ho Raha Hai...";

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            // Step 1: Save order to database via AJAX
            fetch("{{ route('minisite.quickOrder', $sellerPage->slug) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({
                    customer_name: name,
                    customer_phone: phone,
                    customer_address: address,
                    payment_method: payment,
                    cart: cart
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const orderNumber = data.order_number;
                    const totalAmt = data.total_amount;

                    // Step 2: Format rich WhatsApp message with Confirmed Order Number & Single Link
                    let waText = `🛍️ *Naya Order (#${orderNumber}) - ${STORE_NAME}*\n\n` +
                                 `👤 *Customer:* ${name}\n` +
                                 `📞 *Mobile:* ${phone}\n` +
                                 `📍 *Address:* ${address}\n` +
                                 `💳 *Payment Mode:* ${payment === 'online' ? 'Online / UPI' : 'Cash on Delivery (COD)'}\n\n` +
                                 `📦 *Ordered Items (${cart.length}):*\n`;

                    cart.forEach((item, i) => {
                        let line = item.price * item.quantity;
                        waText += `${i+1}. *${item.name}* (x${item.quantity}) - ₹${line.toFixed(2)}\n`;
                    });

                    waText += `\n💰 *Total Bill:* ₹${totalAmt}\n`;

                    if (data.upi_id) {
                        waText += `\n📲 *Pay via UPI:* ${data.upi_id}\n`;
                    }
                    if (data.bank_acc) {
                        waText += `🏦 *Bank:* ${data.bank_name || ''} | A/c: ${data.bank_acc} | IFSC: ${data.bank_ifsc || ''}\n`;
                    }

                    waText += `\n🔗 *View Full Order Slip & Details:*\n${data.order_view_url}\n\n` +
                              `✅ *Order Saved in System!* Please review & confirm dispatch.`;

                    // Step 3: Clear local cart
                    saveCart([]);
                    closeQuickOrderModal();

                    // Step 4: Open WhatsApp directly
                    if (WHATSAPP_NUM) {
                        const url = `https://wa.me/${WHATSAPP_NUM}?text=${encodeURIComponent(waText)}`;
                        window.open(url, '_blank');
                    } else {
                        alert(`🎉 Order #${orderNumber} successfully placed! We will contact you on ${phone}.`);
                    }
                } else {
                    alert('Order create karne me dikkat aayi. Please dobara koshish karein.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Order save request failed.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                btnText.innerText = "Confirm & Send to WhatsApp";
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateCartUI();
        });

        // 📷 Universal Photo Swapper Functions
        function openPhotoSwapper(prodId, prodName, currentImg) {
            const idInput = document.getElementById('swap_product_id');
            if (!idInput) return;
            idInput.value = prodId;
            const nameLbl = document.getElementById('swap_product_name_label');
            if (nameLbl) nameLbl.innerText = prodName;
            const urlInput = document.getElementById('swap_image_url');
            if (urlInput) urlInput.value = currentImg || '';
            const prevImg = document.getElementById('swap_preview_img');
            if (prevImg) prevImg.src = currentImg || '';
            const modal = document.getElementById('photo-swapper-modal');
            if (modal) modal.classList.remove('hidden');
        }

        function closePhotoSwapper() {
            const modal = document.getElementById('photo-swapper-modal');
            if (modal) modal.classList.add('hidden');
            const form = document.getElementById('photo-swapper-form');
            if (form) form.reset();
        }

        function previewSwapFile(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const prev = document.getElementById('swap_preview_img');
                    if (prev) prev.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        function previewSwapUrl(url) {
            if (url && (url.startsWith('http://') || url.startsWith('https://'))) {
                const prev = document.getElementById('swap_preview_img');
                if (prev) prev.src = url;
            }
        }

        async function submitPhotoSwap(e) {
            e.preventDefault();
            const prodId = document.getElementById('swap_product_id').value;
            const imgUrl = document.getElementById('swap_image_url').value;
            const fileInput = document.getElementById('swap_image_file');
            const saveBtn = document.getElementById('btn-save-photo-swap');

            if (!imgUrl && (!fileInput.files || fileInput.files.length === 0)) {
                alert('कृपया इमेज फ़ाइल चुनें या इमेज URL डालें।');
                return;
            }

            saveBtn.disabled = true;
            saveBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Saving...`;

            const formData = new FormData();
            if (imgUrl) formData.append('image_url', imgUrl);
            if (fileInput.files && fileInput.files[0]) formData.append('image_file', fileInput.files[0]);

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                const resp = await fetch(`/seller/products/${prodId}/quick-image-update`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                const data = await resp.json();
                if (data.success) {
                    const imgEl = document.getElementById(`prod_img_${prodId}`);
                    if (imgEl && imgEl.tagName === 'IMG') {
                        imgEl.src = data.image_url;
                    } else if (imgEl) {
                        imgEl.outerHTML = `<img src="${data.image_url}" id="prod_img_${prodId}" class="w-full h-full object-contain crisp-img group-hover:scale-105 transition-transform duration-300">`;
                    }
                    closePhotoSwapper();
                } else {
                    alert(data.message || 'फोटो अपडेट नहीं हो सकी।');
                }
            } catch (err) {
                console.error(err);
                alert('फोटो अपडेट करते समय त्रुटि हुई।');
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerHTML = `<i class="fa-solid fa-check"></i> <span>सेव करें (Save Photo)</span>`;
            }
        }
    </script>

    @if(Auth::check() && Auth::id() === $sellerPage->user_id)
        <!-- 👑 Floating Quick Switcher for Store Owner -->
        <div class="fixed bottom-5 left-5 z-40 flex flex-col gap-2 print:hidden">
            <div class="bg-slate-900/95 backdrop-blur-md text-white p-2 rounded-2xl shadow-2xl border border-slate-700 flex items-center gap-2">
                <a href="{{ route('seller.dashboard') }}" class="px-3 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs flex items-center gap-1.5 shadow-sm transition" title="Go to Multi-channel Seller Dashboard">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>डैशबोर्ड</span>
                </a>
                <a href="{{ route('seller.minisite.edit') }}" class="px-3 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-900 font-black text-xs flex items-center gap-1.5 shadow-sm transition" title="Edit Store Name, Address, Tagline & Policies">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>प्रोफ़ाइल एडिट</span>
                </a>
            </div>
        </div>
    @else
        <!-- 🔐 Floating Direct Edit / Owner Login Button for Store Owner -->
        <div class="fixed bottom-5 left-5 z-40 print:hidden">
            <a href="{{ route('seller.minisite.edit') }}" class="px-3.5 py-2.5 rounded-2xl bg-slate-900/95 hover:bg-black text-white font-black text-xs shadow-2xl border border-amber-400 backdrop-blur-md flex items-center gap-2 transition transform hover:scale-105" title="दुकान मालिक: दुकान का नाम, पता या सेटिंग्स एडिट करें">
                <i class="fa-solid fa-pen-to-square text-amber-400 text-sm"></i>
                <span class="text-amber-300">✏️ दुकान एडिट करें</span>
            </a>
        </div>
    @endif

    @if(Auth::check() && Auth::id() === $sellerPage->user_id)
        <!-- 📸 Universal Photo Swapper Modal for Store Owner -->
        <div id="photo-swapper-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-200 animate-in zoom-in-95 duration-200">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-sm font-black text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-camera text-indigo-600"></i>
                        <span>Change Product Photo • फोटो बदलें</span>
                    </h3>
                    <button type="button" onclick="closePhotoSwapper()" class="text-gray-400 hover:text-gray-600 text-sm">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form id="photo-swapper-form" onsubmit="submitPhotoSwap(event)" class="mt-4 space-y-4">
                    <input type="hidden" id="swap_product_id">
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1" id="swap_product_name_label">Product Name</label>
                        
                        <!-- Live Preview on Studio Backdrop -->
                        <div class="mb-3 p-3 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center gap-3">
                            <div class="h-16 w-16 rounded-xl studio-card-box p-1.5 flex items-center justify-center shrink-0 overflow-hidden">
                                <img id="swap_preview_img" src="" alt="Preview" class="max-w-full max-h-full object-contain crisp-img">
                            </div>
                            <div>
                                <span class="text-[10px] font-black uppercase text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">Amazon / Meesho Studio Backdrop</span>
                                <p class="text-[11px] text-gray-500 mt-0.5">सफेद या किसी भी रंग का प्रोडक्ट बैकग्राउंड के साथ स्पष्ट दिखेगा।</p>
                            </div>
                        </div>

                        <div class="text-xs text-gray-600 mb-2 font-medium">गैलरी से फ़ाइल चुनें या इमेज URL डालें:</div>
                        <input type="file" id="swap_image_file" accept="image/*" onchange="previewSwapFile(event)" class="w-full text-xs file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 mb-2">
                        <input type="text" id="swap_image_url" placeholder="या इमेज URL पेस्ट करें: https://..." oninput="previewSwapUrl(this.value)" class="w-full text-xs font-mono border border-gray-300 rounded-xl px-3 py-2 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                        <button type="button" onclick="closePhotoSwapper()" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition">
                            रद्द करें (Cancel)
                        </button>
                        <button type="submit" id="btn-save-photo-swap" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-md shadow-indigo-600/20 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-check"></i>
                            <span>सेव करें (Save Photo)</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    @stack('scripts')
    @include('components.ai-copilot-widget')
</body>
</html>
