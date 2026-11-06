<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Marketplace Channel Hub (Amazon, Meesho, Blinkit, Flipkart) - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SweetAlert2 Modal Popup Notification -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(session('success'))
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: '🎉 सफलता (Success)!',
            html: '{!! addslashes(session("success")) !!}',
            confirmButtonColor: '#059669',
            confirmButtonText: 'शानदार! ठीक है'
        });
    });
    </script>
    @endif
    @if(session('error'))
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'warning',
            title: '⚠️ मार्केटप्लेस व कतार सूचना',
            html: '{!! addslashes(session("error")) !!}',
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'समझ गया'
        });
    });
    </script>
    @endif

</head>
<body class="bg-gray-100 text-gray-800 antialiased min-h-screen">

    <!-- Top Navigation Bar with Generous Breathing Room and Spacing -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 sm:py-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <!-- Left: Branding & Title -->
                <div class="flex items-center gap-3.5 shrink-0">
                    <a href="{{ route('seller.dashboard') }}" class="h-10 w-10 rounded-2xl bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition shadow-2xs shrink-0" title="डैशबोर्ड पर वापस जाएँ">
                        <i class="fa-solid fa-arrow-left text-sm"></i>
                    </a>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="font-black text-lg sm:text-xl text-gray-900 leading-tight">मल्टी-मार्केटप्लेस सेलर हब</h1>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-wide">
                                ⚡ API 24x7 Realtime Sync
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">Amazon, Meesho, Flipkart, Myntra & Blinkit पर 1-क्लिक में प्रोडक्ट्स लिस्ट, आर्डर व इन्वेंटरी सिंक करें</p>
                    </div>
                </div>

                <!-- Right: Action Buttons Grouped with generous margins and padding -->
                <div class="flex items-center gap-3 flex-wrap lg:justify-end">
                    <!-- Excel Sync Actions Group -->
                    <div class="inline-flex items-center gap-2 p-1.5 bg-slate-100/90 rounded-2xl border border-gray-200 shadow-2xs flex-wrap sm:flex-nowrap">
                        <!-- Direct Excel .xls download (Opens directly in MS Excel!) -->
                        <a href="{{ route('seller.channels.export_sheet', ['scope' => 'pending', 'format' => 'xls']) }}" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs shadow-xs transition flex items-center gap-1.5 hover:scale-102 active:scale-98 cursor-pointer" title="Microsoft Excel (.xls) शीट डाउनलोड करें - सीधे Excel में खुलेगी">
                            <i class="fa-solid fa-file-excel text-amber-300 text-sm"></i>
                            <span>📥 कतार शीट (Excel)</span>
                        </a>

                        <!-- CSV option -->
                        <a href="{{ route('seller.channels.export_sheet', ['scope' => 'pending', 'format' => 'csv']) }}" class="px-2.5 py-2 rounded-xl bg-white hover:bg-emerald-50 text-emerald-800 font-bold text-xs border border-emerald-200 shadow-2xs transition flex items-center gap-1 cursor-pointer" title="CSV (.csv) फॉर्मेट डाउनलोड करें">
                            <span class="text-[11px]">.CSV</span>
                        </a>

                        <!-- All products master sheet -->
                        <a href="{{ route('seller.channels.export_sheet', ['scope' => 'all', 'format' => 'xls']) }}" class="px-3 py-2 rounded-xl bg-white hover:bg-gray-50 text-slate-800 font-bold text-xs border border-gray-200 shadow-2xs transition flex items-center gap-1.5 cursor-pointer" title="पूरी दुकान के सभी प्रोडक्ट्स की मल्टी-चैनल शीट">
                            <i class="fa-solid fa-table-list text-slate-600"></i>
                            <span>पूरी कैटलॉग</span>
                        </a>

                        <!-- Upload trigger -->
                        <button type="button" onclick="triggerChannelSheetUpload()" class="btn-sheet-upload-trigger px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-2xs transition flex items-center gap-1.5 cursor-pointer hover:scale-102 active:scale-98" title="एडिट की हुई शीट अपलोड कर रेट्स व खर्चे अपडेट करें">
                            <i class="fa-solid fa-file-arrow-up text-amber-300"></i>
                            <span>📤 शीट अपलोड</span>
                        </button>
                    </div>

                    <!-- Hub Navigation Group -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('seller.channels.inventory') }}" class="px-3 py-2 rounded-xl bg-white hover:bg-gray-50 text-gray-700 font-bold text-xs border border-gray-200 shadow-2xs transition flex items-center gap-1.5" title="सभी चैनलों की लाइव इन्वेंटरी देखें">
                            <i class="fa-solid fa-boxes-stacked text-indigo-600"></i>
                            <span>Omnichannel Stock</span>
                        </a>
                        <a href="{{ route('seller.orders.index') }}" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-gray-950 font-black text-xs shadow-xs transition flex items-center gap-1.5 hover:scale-102 active:scale-98" title="सभी मार्केटप्लेस आर्डर्स">
                            <i class="fa-solid fa-truck-fast"></i>
                            <span>सभी आर्डर्स</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-7 space-y-7">
        
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2 shadow-xs">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2 shadow-xs">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1 shadow-xs">
                <div class="flex items-center gap-2 text-rose-700 font-black">
                    <i class="fa-solid fa-triangle-exclamation text-base"></i>
                    <span>कृपया नीचे दी गई जानकारी चेक करें:</span>
                </div>
                <ul class="list-disc pl-5 text-[11px] font-normal space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- 📊 TOP METRICS OVERVIEW & ADS STATUS -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Active Channels -->
            <div class="bg-white rounded-3xl p-5 border border-gray-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">कनेक्टेड चैनल्स (API)</span>
                    <div class="text-2xl font-black text-gray-900 mt-1">
                        {{ $channels->where('is_active', true)->count() }} / {{ count($supportedChannels) }}
                    </div>
                    <span class="text-[10px] font-extrabold text-emerald-600 flex items-center gap-1 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        लाइव ऑटो-सिंक सक्रिय
                    </span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-network-wired"></i>
                </div>
            </div>

            <!-- Published Listings -->
            <div class="bg-white rounded-3xl p-5 border border-gray-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">लाइव लिस्टेड प्रोडक्ट्स</span>
                    <div class="text-2xl font-black text-indigo-600 mt-1">
                        {{ $allListings->count() }}
                    </div>
                    <span class="text-[10px] text-gray-500">
                        {{ count($pendingProducts ?? []) }} प्रोडक्ट्स पेंडिंग
                    </span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                </div>
            </div>

            <!-- Total Channel Sales -->
            <div class="bg-white rounded-3xl p-5 border border-gray-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">मार्केटप्लेस कुल बिक्री</span>
                    <div class="text-2xl font-black text-gray-900 mt-1">
                        ₹{{ number_format($analytics['total_channel_revenue'] ?? 0, 2) }}
                    </div>
                    <span class="text-[10px] font-bold text-amber-700">
                        Amazon: {{ $analytics['amazon_orders'] ?? 0 }} • Meesho: {{ $analytics['meesho_orders'] ?? 0 }}
                    </span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>

            <!-- Sponsored Ads Tracker -->
            <div class="bg-gradient-to-br from-slate-900 to-indigo-950 text-white rounded-3xl p-5 border border-indigo-900 shadow-xs flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-[10px] font-black uppercase text-amber-400 tracking-wider">Sponsored Ads</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[9px] bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30">Active</span>
                    </div>
                    <div class="text-xl font-black text-white mt-1">
                        ₹{{ number_format($analytics['ads']['total_spend'] ?? 0, 0) }} Spend
                    </div>
                    <span class="text-[10px] text-slate-300">
                        RoAS: <strong>{{ $analytics['ads']['roas'] ?? '4.8x' }}</strong> &bull; ACOS: <strong>{{ $analytics['ads']['acos'] ?? '14%' }}</strong>
                    </span>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-white/10 text-amber-400 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
            </div>
        </div>

        <!-- 🚚 LIVE CHANNEL ORDERS & DISPATCH PICKUP TIMER ("कब लेने आएगा") -->
        <div class="bg-white rounded-3xl border border-gray-200 shadow-xs p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center text-lg font-black shrink-0">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-black text-gray-900 flex items-center gap-2">
                            <span>मार्केटप्लेस लाइव आर्डर्स & डिस्पैच टाइमर ("कब लेने आएगा")</span>
                            <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 text-[10px] font-extrabold animate-pulse">Live Tracking</span>
                        </h2>
                        <p class="text-[11px] text-gray-500">Amazon Easy-Ship, Meesho Smart, Blinkit 10-Min कूरियर पिकअप का काउंटडाउन व पेंडिंग समय</p>
                    </div>
                </div>
                <a href="{{ route('seller.orders.index') }}" class="px-3.5 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition flex items-center gap-1.5 self-start sm:self-auto">
                    <span>पूरा आर्डर बोर्ड &rarr;</span>
                </a>
            </div>

            @if(isset($channelOrders) && count($channelOrders) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($channelOrders as $ord)
                        @php
                            $source = strtolower($ord->order_source ?? 'vyaparindia');
                            $badgeColor = 'bg-gray-100 text-gray-800 border-gray-200';
                            $sourceIcon = 'fa-solid fa-store';
                            $slaHours = 24;

                            if ($source === 'amazon') {
                                $badgeColor = 'bg-amber-100 text-amber-900 border-amber-300';
                                $sourceIcon = 'fa-brands fa-amazon';
                                $slaHours = 24;
                            } elseif ($source === 'meesho') {
                                $badgeColor = 'bg-pink-100 text-pink-900 border-pink-300';
                                $sourceIcon = 'fa-solid fa-cart-shopping';
                                $slaHours = 36;
                            } elseif ($source === 'blinkit') {
                                $badgeColor = 'bg-yellow-100 text-yellow-900 border-yellow-300';
                                $sourceIcon = 'fa-solid fa-bolt';
                                $slaHours = 0.5; // 30 mins
                            } elseif ($source === 'flipkart') {
                                $badgeColor = 'bg-blue-100 text-blue-900 border-blue-300';
                                $sourceIcon = 'fa-solid fa-bolt';
                                $slaHours = 24;
                            } elseif ($source === 'myntra') {
                                $badgeColor = 'bg-rose-100 text-rose-900 border-rose-300';
                                $sourceIcon = 'fa-solid fa-bag-shopping';
                                $slaHours = 24;
                            }

                            $createdAt = $ord->created_at ?? now();
                            $pickupDeadline = (clone $createdAt)->addHours($slaHours);
                            $minsElapsed = now()->diffInMinutes($createdAt);
                        @endphp
                        <div class="bg-gray-50/70 rounded-2xl border border-gray-200 p-4 space-y-3 hover:border-indigo-300 transition">
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-1 rounded-xl text-[10px] font-black border flex items-center gap-1.5 {{ $badgeColor }}">
                                    <i class="{{ $sourceIcon }}"></i>
                                    <span>{{ strtoupper($source) }}</span>
                                </span>
                                <span class="text-[10px] text-gray-500 font-mono">
                                    #{{ $ord->order_number ?? $ord->id }}
                                </span>
                            </div>

                            <div>
                                <div class="font-black text-xs text-gray-900 flex items-center justify-between">
                                    <span>{{ $ord->customer_name ?: 'Buyer' }} ({{ $ord->city ?: 'India' }})</span>
                                    <span class="text-indigo-700 font-bold">₹{{ number_format($ord->total_price, 2) }}</span>
                                </div>
                                <div class="text-[11px] text-gray-500 mt-0.5">
                                    आर्डर समय: <strong>{{ $createdAt->format('d M, h:i A') }}</strong>
                                </div>
                                <div class="text-[11px] text-amber-700 font-semibold mt-0.5">
                                    ⏳ पेंडिंग समय: <strong>{{ floor($minsElapsed / 60) }} घंटे {{ $minsElapsed % 60 }} मिनट</strong> से पेंडिंग
                                </div>
                            </div>

                            <!-- Live Dispatch Countdown Timer ("कब लेने आएगा") -->
                            <div class="bg-slate-900 text-white rounded-xl p-2.5 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-truck text-amber-400 text-xs"></i>
                                    <span class="text-[10px] text-slate-300">पिकअप डेडलाइन:</span>
                                </div>
                                <div class="font-mono font-black text-emerald-400 text-xs pickup-timer-clock" data-deadline="{{ $pickupDeadline->toIso8601String() }}">
                                    गणना हो रही है...
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-6 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-300 text-xs text-gray-500">
                    <i class="fa-solid fa-box-open text-gray-400 text-2xl mb-1.5 block"></i>
                    अभी कोई नया पेंडिंग आर्डर नहीं है। आर्डर आते ही कूरियर पिकअप टाइमर यहाँ लाइव शुरू हो जाएगा।
                </div>
            @endif
        </div>

        <!-- ======================================================== -->
        <!-- 🎛️ DUAL WORKSPACE: PUBLISHED MARKETPLACES vs PENDING DRAFT -->
        <!-- ======================================================== -->
        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6 space-y-6">
            <!-- Top Controls & Switcher Buttons -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="h-11 w-11 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-lg shadow-md shadow-indigo-600/30 shrink-0">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-black text-gray-900">
                            मार्केटप्लेस कैटलॉग & लिस्टिंग कंट्रोल
                        </h2>
                        <p class="text-xs text-gray-500">पब्लिश किए गए प्रोडक्ट्स व नए पेंडिंग ड्राफ्ट्स को अलग-अलग मैनेज करें</p>
                    </div>
                </div>

                <!-- 2 Main Workspace Tabs: Selected E-Commerce Products vs Live Published -->
                <div class="inline-flex items-center bg-gray-100 p-1.5 rounded-2xl border border-gray-200 self-start sm:self-auto flex-wrap gap-1">
                    <!-- TAB 1: 🟡 ई-कॉमर्स कतार (चयनित 19 उत्पाद) -->
                    <button type="button" onclick="switchMarketplaceTab('pending')" id="btnTabPendingMarketplaces" class="px-4 py-2 rounded-xl text-xs font-black transition flex items-center gap-2 bg-white text-amber-900 shadow-2xs cursor-pointer" title="मार्केटप्लेस (Amazon/Meesho) पर बेचने के लिए चुने गए 19 उत्पाद">
                        <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                        <span>🟡 ई-कॉमर्स कतार (चयनित उत्पाद)</span>
                        <span class="px-2 py-0.2 rounded-full text-[10px] bg-amber-100 text-amber-800 font-extrabold pending-count-badge">{{ count($pendingProducts ?? []) }}</span>
                    </button>

                    <!-- TAB 2: 🟢 लाइव पब्लिश्ड (0) -->
                    <button type="button" onclick="switchMarketplaceTab('published')" id="btnTabPublishedMarketplaces" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 text-gray-600 hover:text-gray-900 cursor-pointer" title="जो वाकई में Amazon/Meesho पर लाइव बिक रहे हैं">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        <span>🟢 लाइव पब्लिश्ड</span>
                        <span class="px-2 py-0.2 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-extrabold">{{ $allListings->count() }}</span>
                    </button>
                </div>

                <!-- Add Products from Store Catalog Button -->
                <button type="button" onclick="openStageProductsModal()" class="px-4 py-2 rounded-2xl bg-gradient-to-r from-amber-500 via-amber-600 to-amber-700 hover:from-amber-600 hover:to-amber-800 text-white font-black text-xs shadow-md shadow-amber-500/25 flex items-center gap-2 transition active:scale-95 cursor-pointer">
                    <i class="fa-solid fa-plus-circle text-amber-200 text-sm"></i>
                    <span>+ दुकान से प्रोडक्ट्स चुनें</span>
                    <span class="bg-black/20 text-white text-[10px] font-mono px-1.5 py-0.5 rounded-full">{{ count($allStoreProducts ?? []) }} उपलब्ध</span>
                </button>
            </div>

            <!-- TAB: 🟢 PUBLISHED MARKETPLACES VIEW (CONFIRMED LIVE ONLY) -->
            <div id="viewPublishedMarketplaces" class="hidden space-y-6">
                <!-- 5 Interactive Marketplace Filter Buttons / Pills -->
                <div class="flex items-center gap-2.5 overflow-x-auto pb-1 scrollbar-none">
                    <span class="text-xs font-black text-gray-500 shrink-0 mr-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-filter text-indigo-600"></i>
                        <span>प्लेटफ़ॉर्म चुनें:</span>
                    </span>

                    <button type="button" onclick="filterMarketplaceListings('all')" class="mp-pill-btn px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition bg-indigo-600 text-white shadow-2xs" data-mp="all">
                        सभी (All: {{ $allListings->count() }})
                    </button>

                    <button type="button" onclick="filterMarketplaceListings('amazon')" class="mp-pill-btn px-3.5 py-1.5 rounded-xl text-xs font-bold transition bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300" data-mp="amazon">
                        <i class="fa-brands fa-amazon mr-1 text-amber-600"></i> Amazon ({{ $channelListingCounts['amazon'] ?? 0 }})
                    </button>

                    <button type="button" onclick="filterMarketplaceListings('meesho')" class="mp-pill-btn px-3.5 py-1.5 rounded-xl text-xs font-bold transition bg-pink-50 hover:bg-pink-100 text-pink-900 border border-pink-300" data-mp="meesho">
                        <i class="fa-solid fa-cart-shopping mr-1 text-pink-600"></i> Meesho ({{ $channelListingCounts['meesho'] ?? 0 }})
                    </button>

                    <button type="button" onclick="filterMarketplaceListings('flipkart')" class="mp-pill-btn px-3.5 py-1.5 rounded-xl text-xs font-bold transition bg-blue-50 hover:bg-blue-100 text-blue-900 border border-blue-300" data-mp="flipkart">
                        <i class="fa-solid fa-bolt mr-1 text-blue-600"></i> Flipkart ({{ $channelListingCounts['flipkart'] ?? 0 }})
                    </button>

                    <button type="button" onclick="filterMarketplaceListings('myntra')" class="mp-pill-btn px-3.5 py-1.5 rounded-xl text-xs font-bold transition bg-rose-50 hover:bg-rose-100 text-rose-900 border border-rose-300" data-mp="myntra">
                        <i class="fa-solid fa-bag-shopping mr-1 text-rose-600"></i> Myntra ({{ $channelListingCounts['myntra'] ?? 0 }})
                    </button>

                    <button type="button" onclick="filterMarketplaceListings('blinkit')" class="mp-pill-btn px-3.5 py-1.5 rounded-xl text-xs font-bold transition bg-yellow-50 hover:bg-yellow-100 text-yellow-900 border border-yellow-300" data-mp="blinkit">
                        <i class="fa-solid fa-bolt mr-1 text-yellow-500"></i> Blinkit ({{ $channelListingCounts['blinkit'] ?? 0 }})
                    </button>
                </div>

                <!-- Published Items Grid -->
                @if(isset($allListings) && count($allListings) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="publishedListingsGrid">
                        @foreach($allListings as $listing)
                            @php
                                $chanName = strtolower($listing->channel->channel_name ?? 'amazon');
                                $prod = $listing->product;
                            @endphp
                            <div class="listing-item-card bg-white rounded-2xl border border-gray-200 p-4 shadow-2xs hover:border-indigo-300 transition space-y-3" data-channel="{{ $chanName }}">
                                <div class="flex items-start gap-3">
                                    <div class="h-16 w-16 rounded-xl bg-gray-50 border border-gray-200 overflow-hidden flex items-center justify-center shrink-0 p-1">
                                        @if($prod && $prod->image_url)
                                            <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" class="h-full w-full object-contain">
                                        @else
                                            <i class="fa-solid fa-cube text-gray-300 text-xl"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-1.5 mb-1">
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider {{ $chanName === 'amazon' ? 'bg-amber-100 text-amber-900' : ($chanName === 'meesho' ? 'bg-pink-100 text-pink-900' : 'bg-indigo-100 text-indigo-900') }}">
                                                {{ $chanName }}
                                            </span>
                                            <span class="text-[10px] text-emerald-600 font-bold flex items-center gap-1">
                                                <i class="fa-solid fa-check text-[8px]"></i> Synced
                                            </span>
                                        </div>
                                        <h4 class="font-extrabold text-xs text-gray-900 truncate" title="{{ $prod->name ?? '' }}">
                                            {{ $prod->name ?? 'Product' }}
                                        </h4>
                                        <div class="text-[10px] text-gray-400 font-mono mt-0.5">
                                            ASIN/SKU: {{ $listing->external_product_id ?? ($prod->sku ?? 'N/A') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between text-xs pt-2 border-t border-gray-100">
                                    <div>
                                        <span class="text-[10px] text-gray-400 block">चैनल रेट:</span>
                                        <span class="font-black text-gray-900 font-mono">₹{{ number_format($listing->channel_price ?: ($prod->price ?? 0), 2) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-gray-400 block">स्टॉक:</span>
                                        <span class="font-bold text-emerald-600">{{ $listing->synced_stock ?? ($prod->stock_quantity ?? 50) }} pcs</span>
                                    </div>
                                    @if($listing->listing_url)
                                        <a href="{{ $listing->listing_url }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-indigo-50 text-indigo-700 text-[10px] font-bold transition flex items-center gap-1">
                                            <span>लाइव देखें</span>
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[8px]"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-10 text-center bg-gradient-to-b from-gray-50 to-white rounded-3xl border border-dashed border-gray-300 space-y-3">
                        <div class="h-16 w-16 mx-auto rounded-3xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-3xl shadow-xs border border-emerald-100">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <div class="max-w-md mx-auto space-y-1">
                            <h3 class="font-black text-sm text-gray-900">अभी तक कोई प्रोडक्ट लाइव पब्लिश नहीं हुआ है</h3>
                            <p class="text-xs text-gray-500 leading-relaxed">
                                जब आप पेंडिंग कतार से प्रोडक्ट्स को Amazon या Meesho पर लाइव पब्लिश करेंगे, तब सफल लिस्टिंग्स यहाँ लाइव ASIN व स्टेटस के साथ दिखेंगी।
                            </p>
                        </div>
                        <button type="button" onclick="switchMarketplaceTab('pending')" class="px-5 py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs shadow-md transition inline-flex items-center gap-2 cursor-pointer active:scale-95">
                            <i class="fa-solid fa-arrow-right"></i>
                            <span>पेंडिंग कतार (19 प्रोडक्ट्स) पर जाएँ</span>
                        </button>
                    </div>
                @endif
            </div>

            <!-- TAB: 🟡 E-COMMERCE SELECTED PRODUCTS & PENDING PUBLISH (ACTIVE BY DEFAULT) -->
            <div id="viewPendingMarketplaces" class="space-y-5">
                <!-- 🏷️ Category & Group Filter Pills strictly for Selected E-Commerce Products -->
                @if(isset($ecomCategories) && count($ecomCategories) > 0)
                <div class="p-3.5 bg-amber-50/70 border border-amber-200/90 rounded-2xl flex flex-wrap items-center justify-between gap-3 text-xs shadow-2xs">
                    <div class="flex items-center gap-2 overflow-x-auto pb-0.5 scrollbar-none flex-wrap">
                        <span class="text-xs font-black text-amber-950 flex items-center gap-1.5 shrink-0">
                            <i class="fa-solid fa-layer-group text-amber-600"></i>
                            <span>केटेगरी व ग्रुप्स:</span>
                        </span>
                        <button type="button" onclick="filterPendingCategory('all', this)" class="pending-cat-pill px-3 py-1 rounded-xl text-xs font-black transition bg-amber-600 text-white shadow-xs cursor-pointer" data-cat="all">
                            सभी उत्पाद (All: {{ count($pendingProducts ?? []) }})
                        </button>
                        @foreach($ecomCategories ?? [] as $cat)
                            <button type="button" onclick="filterPendingCategory('{{ $cat->id }}', this)" class="pending-cat-pill px-3 py-1 rounded-xl text-xs font-bold transition bg-white hover:bg-amber-100 text-amber-950 border border-amber-300 shadow-2xs cursor-pointer" data-cat="{{ $cat->id }}">
                                {{ $cat->name }} ({{ $cat->products_count ?? 0 }})
                            </button>
                        @endforeach
                    </div>
                    <span class="text-[11px] text-amber-800 font-semibold shrink-0">
                        ✨ सिर्फ वही {{ count($pendingProducts ?? []) }} प्रोडक्ट्स जो आपने ई-कॉमर्स के लिए चुने हैं
                    </span>
                </div>
                @endif

                <form action="{{ route('seller.channels.bulkPublish') }}" method="POST" id="bulkPublishForm" onsubmit="return handleBulkPublishSubmit(event)">
                    @csrf
                    <!-- Action Bar -->
                    <div class="p-4 bg-amber-50/70 border border-amber-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" id="selectAllPendingChk" onchange="toggleSelectAllPending(this.checked)" checked class="rounded text-indigo-600 h-4 w-4 cursor-pointer">
                            <div>
                                <span class="font-extrabold text-xs text-amber-950 block">पेंडिंग प्रोडक्ट्स का चयन करें (Bulk Push)</span>
                                <span class="text-[11px] text-amber-800">ये प्रोडक्ट्स आपके VyaparIndia स्टोर पर लाइव हैं, इन्हें 1-क्लिक में Amazon/Meesho पर भेजें।</span>
                            </div>
                        </div>

                        <!-- Target Marketplace Checkboxes -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <label class="text-xs font-bold text-gray-700 flex items-center gap-1.5 bg-white px-2.5 py-1 rounded-xl border border-gray-300 cursor-pointer">
                                <input type="checkbox" name="channels[]" value="amazon" checked class="rounded text-amber-600">
                                <span>Amazon</span>
                            </label>
                            <label class="text-xs font-bold text-gray-700 flex items-center gap-1.5 bg-white px-2.5 py-1 rounded-xl border border-gray-300 cursor-pointer">
                                <input type="checkbox" name="channels[]" value="meesho" checked class="rounded text-pink-600">
                                <span>Meesho</span>
                            </label>
                            <label class="text-xs font-bold text-gray-700 flex items-center gap-1.5 bg-white px-2.5 py-1 rounded-xl border border-gray-300 cursor-pointer">
                                <input type="checkbox" name="channels[]" value="flipkart" class="rounded text-blue-600">
                                <span>Flipkart</span>
                            </label>

                            <button type="button" onclick="openStageProductsModal()" class="px-3.5 py-2 rounded-xl bg-white border border-amber-300 hover:bg-amber-100 text-amber-950 font-extrabold text-xs transition flex items-center gap-1.5 shadow-2xs cursor-pointer">
                                <i class="fa-solid fa-plus text-amber-600"></i>
                                <span>+ दुकान से और जोड़ें</span>
                            </button>

                            <button type="button" onclick="testChannelConnection('amazon')" class="px-3.5 py-2 rounded-xl bg-white border border-indigo-300 hover:bg-indigo-50 text-indigo-900 font-extrabold text-xs transition flex items-center gap-1.5 shadow-2xs cursor-pointer hover:scale-102 active:scale-98" title="चेक करें कि Amazon API से लिंक है या नहीं और क्या कमी है">
                                <i class="fa-solid fa-bolt text-indigo-600"></i>
                                <span>⚡ Amazon लिंक चेक</span>
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs shadow-md transition active:scale-95 cursor-pointer flex items-center gap-1.5">
                                <i class="fa-solid fa-cloud-arrow-up text-amber-300"></i>
                                <span>🚀 1-क्लिक में लाइव पब्लिश करें</span>
                            </button>
                        </div>
                    </div>

                                        <!-- 🎛️ VIEW MODE SWITCHER & BULK ACTIONS TOOLBAR -->
                    <div class="mt-6 p-4 sm:p-4.5 bg-slate-900 text-white rounded-3xl shadow-md flex flex-col xl:flex-row items-start xl:items-center justify-between gap-4">
                        <div class="flex items-center gap-3 flex-wrap">
                            <!-- Instant Search in Pending Queue -->
                            <div class="relative w-full sm:w-64">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                <input type="text" id="pendingQueueSearchInp" oninput="filterPendingQueueItems()" placeholder="🔍 कतार में खोजें (उदा. Bib, Cistern)..." class="w-full pl-8 pr-3 py-1.5 rounded-xl bg-white/10 text-white placeholder-gray-400 border border-white/20 text-xs font-semibold focus:bg-white focus:text-gray-900 focus:placeholder-gray-500 outline-none transition">
                            </div>

                            <span class="text-xs font-black text-slate-300 flex items-center gap-1.5">
                                <i class="fa-solid fa-layer-group text-amber-400"></i>
                                <span>व्यू मोड:</span>
                            </span>
                            <div class="inline-flex items-center bg-white/10 p-1 rounded-2xl border border-white/20">
                                <button type="button" onclick="switchPendingLayout('cards')" id="btnPendingViewCards" class="px-4 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 text-slate-300 hover:text-white cursor-pointer">
                                    <i class="fa-solid fa-grip"></i>
                                    <span>🎴 फ़ोटो कार्ड मोड</span>
                                </button>
                                <button type="button" onclick="switchPendingLayout('table')" id="btnPendingViewTable" class="px-4 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-1.5 bg-indigo-600 text-white shadow-2xs cursor-pointer">
                                    <i class="fa-solid fa-table-cells text-emerald-400"></i>
                                    <span>📊 एक्सेल शीट / लिस्ट मोड</span>
                                </button>
                            </div>
                        </div>

                        <!-- Bulk Actions with clean spacing and breathing room -->
                        <div class="flex items-center gap-2.5 sm:gap-3 w-full xl:w-auto justify-start xl:justify-end flex-wrap">
                            <a href="{{ route('seller.channels.export_sheet', ['scope' => 'pending', 'format' => 'xls']) }}" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-2xs cursor-pointer hover:scale-102 active:scale-98" title="Microsoft Excel में खुलने वाली शीट डाउनलोड करें">
                                <i class="fa-solid fa-file-excel text-amber-300"></i>
                                <span>📥 कतार शीट (Excel)</span>
                            </a>
                            <button type="button" id="btnUnstageSelectedChannel" onclick="unstageSelectedChannelRows()" class="hidden px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs transition flex items-center gap-1.5 shadow-2xs cursor-pointer hover:scale-102 active:scale-98" title="चुने हुए प्रोडक्ट्स को Amazon/Meesho कतार से हटाएं (दुकान में सुरक्षित रहेंगे)">
                                <i class="fa-solid fa-trash-can"></i>
                                <span id="btnUnstageSelectedChannelText">मार्केटप्लेस से हटाएं</span>
                            </button>
                            <button type="button" id="btnDownloadSelectedChannel" onclick="downloadSelectedChannelRows()" class="hidden px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs transition flex items-center gap-1.5 shadow-2xs cursor-pointer hover:scale-102 active:scale-98" title="केवल टिक किए हुए प्रोडक्ट्स डाउनलोड करें">
                                <i class="fa-solid fa-check-double"></i>
                                <span id="btnDownloadSelectedChannelText">चुने हुए डाउनलोड</span>
                            </button>
                            <a href="{{ route('seller.channels.export_sheet', ['scope' => 'all', 'format' => 'xls']) }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-bold text-xs transition flex items-center gap-1.5 shadow-2xs cursor-pointer" title="पूरी दुकान की मल्टी-चैनल शीट">
                                <i class="fa-solid fa-layer-group text-emerald-400"></i>
                                <span>पूरी कैटलॉग</span>
                            </a>
                            <button type="button" onclick="triggerChannelSheetUpload()" class="btn-sheet-upload-trigger px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-2xs cursor-pointer hover:scale-102 active:scale-98" title="एक्सेल शीट अपलोड कर रेट्स अपडेट करें">
                                <i class="fa-solid fa-file-arrow-up text-amber-300"></i>
                                <span>शीट अपलोड</span>
                            </button>
                            <button type="button" id="btnBulkSaveTable" onclick="saveAllTableRowsCosting()" class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer active:scale-95" title="कतार के सभी प्रोडक्ट्स के रेट्स, लेस% व मार्जिन एक साथ सेव करें">
                                <i class="fa-solid fa-floppy-disk text-amber-200"></i>
                                <span>💾 सभी सेव करें (Save All)</span>
                            </button>
                            <span class="text-xs text-slate-300 font-semibold bg-white/10 px-3 py-2 rounded-xl border border-white/10">
                                <strong class="text-amber-400 font-mono">{{ count($pendingProducts ?? []) }}</strong> प्रोडक्ट्स कतार में
                            </span>
                        </div>
                    </div>

<!-- ⚡ QUICK BULK PRESET & APPLY TO ALL BAR -->
                        <div class="p-3.5 bg-gradient-to-r from-amber-50 via-orange-50 to-amber-100 border border-amber-200 rounded-3xl shadow-2xs flex flex-wrap items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="h-8 w-8 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-sm shadow-2xs">
                                    <i class="fa-solid fa-wand-magic-sparkles text-amber-100"></i>
                                </span>
                                <div>
                                    <span class="font-black text-amber-950 block">⚡ एक्सेल 'Apply to All' टूल (सभी {{ count($pendingProducts ?? []) }} प्रोडक्ट्स में 1-क्लिक में भरें)</span>
                                    <span class="text-[11px] text-amber-800">यहाँ डिफ़ॉल्ट मार्जिन, रिटर्न रिस्क बफ़र, डिलीवरी व GST सेट करके सीधे सभी रोज़ में लागू करें:</span>
                                </div>
                            </div>

                            <!-- Inputs Bar -->
                            <div class="flex items-center gap-2 flex-wrap">
                                <div class="flex items-center gap-1 bg-amber-50 px-2.5 py-1 rounded-xl border-2 border-amber-400 shadow-xs" title="MRP पर लेस डिस्काउंट % (उदा. 45% या 40%) - सभी आइटम्स की खरीद लागत अपने-आप बदल जाएगी">
                                    <span class="text-[10px] text-amber-950 font-black">🏷️ Less %:</span>
                                    <input type="number" step="0.5" min="0" max="99" id="bulkPresetLess" placeholder="उदा. 45" class="w-14 text-xs font-mono font-black text-amber-950 outline-none text-center bg-white rounded border border-amber-300">
                                    <span class="text-[10px] text-amber-700 font-black">%</span>
                                </div>

                                <!-- Layout Switcher Toggle: List vs Cards -->
                                <div class="flex items-center bg-white p-1 rounded-2xl border-2 border-indigo-400 shadow-2xs">
                                    <button type="button" onclick="switchPendingLayout('table')" id="btnTopSwitchTable" class="px-3.5 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-1.5 bg-indigo-600 text-white shadow-2xs cursor-pointer" title="📊 एक्सेल शीट / लिस्ट व्यू (19 आइटम्स)">
                                        <i class="fa-solid fa-table-cells text-amber-300"></i>
                                        <span>📊 लिस्ट मोड</span>
                                    </button>
                                    <button type="button" onclick="switchPendingLayout('cards')" id="btnTopSwitchCards" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 text-slate-700 hover:text-indigo-900 cursor-pointer" title="🎴 फोटो कार्ड मोड (साइज, फोटो व हटाने का विकल्प)">
                                        <i class="fa-solid fa-grip text-indigo-600"></i>
                                        <span>🎴 कार्ड मोड</span>
                                    </button>
                                </div>

                                <!-- 1-Click Platform Modes (Meesho vs Amazon) -->
                                <div class="flex items-center gap-1.5 bg-gradient-to-r from-pink-50 to-amber-50 p-1 rounded-xl border border-pink-200 shadow-2xs">
                                    <button type="button" onclick="setBulkPlatformPreset('meesho')" id="btnModeMeesho" class="px-2.5 py-1 rounded-lg text-[11px] font-black transition flex items-center gap-1 bg-white text-pink-700 hover:bg-pink-600 hover:text-white border border-pink-300 shadow-xs active:scale-95 cursor-pointer" title="Meesho: 0% कमीशन, ₹35 रिटर्न/RTO सुरक्षा बफ़र, ₹38 कूरियर, 25% मार्जिन">
                                        <span class="text-xs">🌸</span> Meesho मोड (0% फ़ी + ₹35 RTO)
                                    </button>
                                    <button type="button" onclick="setBulkPlatformPreset('amazon')" id="btnModeAmazon" class="px-2.5 py-1 rounded-lg text-[11px] font-black transition flex items-center gap-1 bg-white text-amber-900 hover:bg-amber-700 hover:text-white border border-amber-300 shadow-xs active:scale-95 cursor-pointer" title="Amazon: 12% कमीशन, ₹20 रिटर्न बफ़र, ₹45 Easy Ship, 25% मार्जिन">
                                        <span class="text-xs">🟠</span> Amazon मोड (12% फ़ी + ₹20 RTO)
                                    </button>
                                </div>

                                <div class="flex items-center gap-1 bg-white px-2.5 py-1 rounded-xl border border-amber-300 shadow-2xs">
                                    <span class="text-[10px] text-gray-500 font-bold">मार्जिन %:</span>
                                    <input type="number" id="bulkPresetMargin" value="25" class="w-12 text-xs font-mono font-bold text-indigo-700 outline-none text-center">
                                    <span class="text-[10px] text-gray-400">%</span>
                                </div>

                                <div class="flex items-center gap-1 bg-white px-2.5 py-1 rounded-xl border border-purple-300 shadow-2xs" title="प्लेटफ़ॉर्म कमीशन % (Amazon: 12%, Meesho: 0%, Flipkart: 10%)">
                                    <span class="text-[10px] text-purple-700 font-bold">कमीशन %:</span>
                                    <input type="number" id="bulkPresetFee" value="12" class="w-11 text-xs font-mono font-bold text-purple-700 outline-none text-center">
                                    <span class="text-[10px] text-gray-400">%</span>
                                </div>

                                <div class="flex items-center gap-1 bg-white px-2.5 py-1 rounded-xl border border-rose-300 shadow-2xs" title="RTO / रिटर्न नुकसान से सुरक्षा बफ़र">
                                    <span class="text-[10px] text-rose-700 font-bold">🔄 रिटर्न/RTO:</span>
                                    <span class="text-[10px] text-gray-400">₹</span>
                                    <input type="number" id="bulkPresetRto" value="20" class="w-12 text-xs font-mono font-bold text-rose-700 outline-none text-center">
                                </div>

                                <div class="flex items-center gap-1 bg-white px-2.5 py-1 rounded-xl border border-amber-300 shadow-2xs">
                                    <span class="text-[10px] text-gray-500 font-bold">डिलीवरी:</span>
                                    <span class="text-[10px] text-gray-400">₹</span>
                                    <input type="number" id="bulkPresetShip" value="45" class="w-12 text-xs font-mono font-bold text-gray-800 outline-none text-center">
                                </div>

                                <div class="flex items-center gap-1 bg-white px-2.5 py-1 rounded-xl border border-amber-300 shadow-2xs">
                                    <span class="text-[10px] text-gray-500 font-bold">पैकिंग:</span>
                                    <span class="text-[10px] text-gray-400">₹</span>
                                    <input type="number" id="bulkPresetPack" value="15" class="w-12 text-xs font-mono font-bold text-gray-800 outline-none text-center">
                                </div>

                                <div class="flex items-center gap-1 bg-white px-2.5 py-1 rounded-xl border border-emerald-300 shadow-2xs" title="सभी प्रोडक्ट्स में एक साथ इन्वेंट्री स्टॉक पीस भरें (उदा. 10, 20 या 50)">
                                    <span class="text-[10px] text-emerald-800 font-bold">📦 स्टॉक Qty:</span>
                                    <input type="number" min="0" id="bulkPresetStock" value="20" class="w-12 text-xs font-mono font-bold text-emerald-800 outline-none text-center">
                                    <span class="text-[10px] text-gray-400">pcs</span>
                                </div>

                                <div class="flex items-center gap-1 bg-white px-2.5 py-1 rounded-xl border border-amber-300 shadow-2xs">
                                    <span class="text-[10px] text-gray-500 font-bold">GST %:</span>
                                    <select id="bulkPresetGst" class="text-xs font-mono font-bold text-gray-800 bg-transparent outline-none">
                                        <option value="18" selected>18%</option>
                                        <option value="12">12%</option>
                                        <option value="5">5%</option>
                                        <option value="0">0%</option>
                                        <option value="28">28%</option>
                                    </select>
                                </div>

                                <button type="button" onclick="applyPresetToAllRows()" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 active:scale-95 cursor-pointer">
                                    <i class="fa-solid fa-wand-magic-sparkles text-amber-200"></i>
                                    <span>⚡ लागू करें (Apply)</span>
                                </button>
                                <button type="button" onclick="applyPresetAndSaveAll()" class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 active:scale-95 cursor-pointer" title="मार्जिन व खर्चे सभी पर लागू करके सीधे डेटाबेस में सुरक्षित सेव करें">
                                    <i class="fa-solid fa-floppy-disk text-emerald-200"></i>
                                    <span>💾 लागू करें व सेव करें (Apply & Save)</span>
                                </button>
                            </div>

                            <!-- Bulk Combo Management Bar -->
                            <div class="w-full flex items-center justify-between pt-2.5 border-t border-amber-200/70 flex-wrap gap-2 text-xs">
                                <div class="flex items-center gap-1.5 text-amber-950 font-black">
                                    <i class="fa-solid fa-boxes-packing text-amber-700"></i>
                                    <span>📦 2P, 4P, 6P कॉम्बो बल्क टूल्स:</span>
                                </div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <button type="button" onclick="bulkRecalcCombos('all')" class="px-3 py-1.5 rounded-xl bg-white border border-amber-300 hover:bg-amber-100 text-amber-950 font-bold text-xs transition flex items-center gap-1.5 shadow-2xs cursor-pointer active:scale-95" title="सभी प्रोडक्ट्स के 2P (₹45 कूरियर), 4P (₹65 कूरियर), 6P (₹85 कूरियर) ऑटो-कैलकुलेट करें">
                                        <i class="fa-solid fa-calculator text-indigo-600"></i>
                                        <span>⚡ 2P, 4P, 6P ऑटो-कैलकुलेट (Bulk)</span>
                                    </button>
                                    <button type="button" onclick="bulkRecalcAndSaveCombos()" class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer active:scale-95" title="सभी प्रोडक्ट्स के 2P, 4P, 6P रेट्स री-कैलकुलेट कर तुरंत डेटाबेस में सेव करें">
                                        <i class="fa-solid fa-floppy-disk text-amber-200"></i>
                                        <span>💾 सभी कॉम्बो री-कैलकुलेट व सेव करें</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                    <!-- Pending Products List: 1. CARDS VIEW -->
                    <div id="pendingCardsContainer" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                        @forelse($pendingProducts ?? [] as $p)
                            @php
                                $imgCount = $p->images ? $p->images->count() : 0;
                                if ($imgCount === 0 && !empty($p->image)) $imgCount = 1;
                                $ue = $p->attributes['unit_economics'] ?? [];
                                $pCost = (float)($ue['purchase_cost'] ?? $p->purchase_price ?? 0);
                                $mrpVal = (float)($p->mrp ?: ($p->variants->where('mrp', '>', 0)->min('mrp') ?: $p->price));
                                $lessVal = ($p->trade_discount_percent !== null && (float)$p->trade_discount_percent > 0)
                                    ? (float)$p->trade_discount_percent
                                    : ($p->variants->where('trade_discount_percent', '>', 0)->max('trade_discount_percent') ?: ($ue['trade_discount_percent'] ?? ''));
                                $marginPct = (float)($ue['margin_pct'] ?? 25);
                                $rtoBuffer = (float)($ue['rto_buffer'] ?? 20);
                                $packCost = (float)($ue['packaging'] ?? 15);
                                $shipCost = (float)($ue['shipping'] ?? 45);
                                $feePct = (float)($ue['platform_fee_pct'] ?? 12);
                                $adsCost = (float)($ue['ads'] ?? 10);
                                $otherCost = (float)($ue['others'] ?? 5);

                                $desiredProfit = round($pCost * ($marginPct / 100));
                                $gstPercent = (float)($p->gst_percent ?: 18);
                                $g = $gstPercent / 100;
                                $f = $feePct / 100;
                                $cTax = $g > 0 ? ($pCost / (1 + $g)) : $pCost;
                                $denom = max(0.05, (1 / (1 + $g)) - $f);
                                $fixedExp = $rtoBuffer + $packCost + $shipCost + $adsCost + $otherCost;
                                $calcSellingPrice = ceil(($cTax + $fixedExp + $desiredProfit) / $denom);
                                $finalSellingPrice = !empty($ue['marketplace_price']) ? (float)$ue['marketplace_price'] : max($calcSellingPrice, (float)$p->price);
                                $totalExp = round($packCost + $shipCost + $rtoBuffer + ($finalSellingPrice * $f) + $adsCost + $otherCost, 2);
                                $netMargin = round($finalSellingPrice - $pCost - $totalExp, 2);

                                // Side-by-Side Platform Prices (Amazon vs Meesho)
                                $denomAmz = max(0.05, (1 / (1 + $g)) - 0.12);
                                $fixedAmz = $rtoBuffer + $packCost + $shipCost + $adsCost + $otherCost;
                                $calcAmz = ceil(($cTax + $fixedAmz + $desiredProfit) / $denomAmz);

                                $denomMsh = max(0.05, (1 / (1 + $g)));
                                $fixedMsh = max(35, $rtoBuffer) + $packCost + min(38, $shipCost) + $otherCost;
                                $calcMsh = ceil(($cTax + $fixedMsh + $desiredProfit) / $denomMsh);

                                // 2P, 4P, 6P Combo Rates for Card
                                $combo2 = !empty($ue['combo_2']) ? (float)$ue['combo_2'] : ceil((($cTax * 2) + ($desiredProfit * 1.8) + ($rtoBuffer * 1.5) + ($packCost * 1.3) + $shipCost + ($adsCost * 1.2)) / $denom);
                                $combo4 = !empty($ue['combo_4']) ? (float)$ue['combo_4'] : ceil((($cTax * 4) + ($desiredProfit * 3.5) + ($rtoBuffer * 2.0) + ($packCost * 1.6) + ($shipCost * 1.2) + ($adsCost * 1.5)) / $denom);
                                $combo6 = !empty($ue['combo_6']) ? (float)$ue['combo_6'] : ceil((($cTax * 6) + ($desiredProfit * 5.0) + ($rtoBuffer * 2.5) + ($packCost * 2.0) + ($shipCost * 1.4) + ($adsCost * 1.8)) / $denom);
                            @endphp
                            <div class="bg-white rounded-3xl border border-gray-200 p-4 shadow-2xs hover:border-indigo-400 hover:shadow-md transition space-y-3.5 pending-card-item" id="pending-card-box-{{ $p->id }}" data-cat-id="{{ $p->category_id ?? 0 }}">
                                <div class="flex items-start gap-3">
                                    <input type="checkbox" name="product_ids[]" value="{{ $p->id }}" checked class="pending-prod-chk rounded text-indigo-600 h-4 w-4 mt-1 cursor-pointer">
                                    
                                    <!-- Photo Block with Multi-Image Support -->
                                    <div class="relative group/pendingimg h-16 w-16 rounded-2xl bg-gray-50 border border-gray-200 overflow-hidden flex items-center justify-center shrink-0 p-1 cursor-pointer hover:border-indigo-500 hover:ring-2 hover:ring-indigo-200 transition"
                                         onclick="triggerMultipleImageUpload({{ $p->id }}, '{{ addslashes($p->name) }}')"
                                         title="फ़ोटो बदलें या और फ़ोटो जोड़ें">
                                        <img src="{{ $p->image_url ?? '' }}" 
                                             alt="{{ $p->name }}" 
                                             id="pending-prod-img-{{ $p->id }}" 
                                             class="h-full w-full object-contain {{ !empty($p->image_url) ? '' : 'hidden' }}"
                                             onerror="this.style.display='none'; var ph = document.getElementById('pending-prod-img-ph-{{ $p->id }}'); if(ph) ph.classList.remove('hidden');">
                                        
                                        <div id="pending-prod-img-ph-{{ $p->id }}" class="{{ !empty($p->image_url) ? 'hidden' : 'flex' }} flex-col items-center justify-center text-gray-300 group-hover/pendingimg:text-indigo-600 transition">
                                            <i class="fa-solid fa-camera text-base"></i>
                                            <span class="text-[8px] font-bold mt-0.5 text-gray-400 group-hover/pendingimg:text-indigo-600">+फ़ोटो</span>
                                        </div>

                                        <div class="absolute inset-0 bg-indigo-950/80 text-white flex flex-col items-center justify-center opacity-0 group-hover/pendingimg:opacity-100 transition rounded-2xl text-[9px] font-bold">
                                            <i class="fa-solid fa-images text-xs mb-0.5 text-amber-300"></i>
                                            <span>+फ़ोटो</span>
                                        </div>

                                        <span id="pending-img-badge-{{ $p->id }}" class="absolute bottom-1 right-1 bg-slate-900/90 text-white text-[8px] font-black px-1.5 py-0.2 rounded-md shadow-2xs">
                                            {{ $imgCount }} फ़ोटो
                                        </span>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="text-[9px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md">
                                                {{ $p->category->name ?? 'General' }}
                                            </span>
                                            <div class="flex items-center gap-2">
                                                <button type="button" onclick="triggerMultipleImageUpload({{ $p->id }}, '{{ addslashes($p->name) }}')" class="text-[10px] font-extrabold text-indigo-600 hover:text-indigo-800 transition inline-flex items-center gap-1">
                                                    <i class="fa-solid fa-plus-circle"></i> फ़ोटो जोड़ें
                                                </button>
                                                <button type="button" onclick="unstageProductItem({{ $p->id }}, '{{ addslashes($p->name) }}')" class="h-6 w-6 rounded-lg bg-gray-100 hover:bg-rose-100 text-gray-400 hover:text-rose-600 flex items-center justify-center text-xs transition cursor-pointer" title="मार्केटप्लेस बास्केट से हटाएं (दुकान में सुरक्षित रहेगा)">
                                                    <i class="fa-solid fa-xmark font-bold"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <h4 class="font-black text-xs text-gray-900 truncate mt-1" title="{{ $p->name }}">
                                            {{ $p->name }}
                                        </h4>
                                        <div class="text-[10px] text-gray-400 font-mono mt-0.5">
                                            SKU: {{ $p->sku ?: 'STD' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Unit Economics Breakdown Badge -->
                                <div class="p-2.5 bg-slate-50 rounded-2xl border border-gray-100 text-[11px] space-y-1">
                                    @if($mrpVal > 0)
                                    <div class="flex items-center justify-between text-amber-900 text-[10px] font-bold pb-1 border-b border-gray-100">
                                        <span>MRP: <strong class="font-mono">₹{{ number_format($mrpVal, 2) }}</strong></span>
                                        <div class="inline-flex items-center gap-1 bg-amber-50/70 px-2 py-0.5 rounded-lg border border-amber-200">
                                            <span class="text-[10px] text-amber-950 font-bold">Less:</span>
                                            <input type="number" step="0.5" min="0" max="99" id="card-less-{{ $p->id }}" data-mrp="{{ $mrpVal }}" value="{{ $lessVal !== '' && $lessVal > 0 ? $lessVal : '' }}" oninput="recalcCardLessToCost({{ $p->id }})" class="w-12 px-1 py-0.5 bg-white border border-amber-300 rounded font-mono font-black text-amber-900 text-center text-[10px] outline-none" placeholder="Less%">
                                            <span class="text-rose-600 font-black">%</span>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-500 font-medium">खरीद लागत (P.Cost):</span>
                                        <span class="font-bold text-gray-800 font-mono" id="card-cost-{{ $p->id }}">₹{{ number_format($pCost, 2) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-gray-500 text-[10px]">
                                        <span>खर्चे (पैकिंग+कूरियर+फ़ी+ऐड्स):</span>
                                        <span class="font-mono font-bold text-amber-700" id="card-exp-{{ $p->id }}">+₹{{ number_format($totalExp, 2) }}</span>
                                    </div>

                                    <!-- खर्चे का अलग-अलग पारदर्शी विवरण (Breakdown) -->
                                    <div class="grid grid-cols-2 gap-1.5 pt-1 border-t border-gray-100 text-[9px] text-gray-600 font-mono">
                                        <div class="bg-white/90 px-2 py-0.5 rounded-lg border border-gray-200/70 flex items-center justify-between shadow-2xs">
                                            <span class="text-gray-500 font-sans">📦 पैकिंग:</span>
                                            <span class="font-bold text-gray-900" id="card-exp-pack-{{ $p->id }}">₹{{ $packCost }}</span>
                                        </div>
                                        <div class="bg-white/90 px-2 py-0.5 rounded-lg border border-gray-200/70 flex items-center justify-between shadow-2xs">
                                            <span class="text-gray-500 font-sans">🚚 कूरियर:</span>
                                            <span class="font-bold text-gray-900" id="card-exp-ship-{{ $p->id }}">₹{{ $shipCost }}</span>
                                        </div>
                                        <div class="bg-white/90 px-2 py-0.5 rounded-lg border border-rose-200 flex items-center justify-between shadow-2xs">
                                            <span class="text-rose-700 font-sans">🔄 RTO बफ़र:</span>
                                            <span class="font-bold text-rose-800" id="card-exp-rto-{{ $p->id }}">₹{{ $rtoBuffer }}</span>
                                        </div>
                                        <div class="bg-white/90 px-2 py-0.5 rounded-lg border border-purple-200 flex items-center justify-between shadow-2xs">
                                            <span class="text-purple-700 font-sans">🏷️ फ़ी+ऐड्स:</span>
                                            <span class="font-bold text-purple-900" id="card-exp-fee-{{ $p->id }}">₹{{ round(($finalSellingPrice * $f) + $adsCost + $otherCost) }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between text-gray-500 text-[10px] pt-1">
                                        <span>शुद्ध मुनाफा / मार्जिन:</span>
                                        <span class="font-mono font-extrabold text-emerald-700" id="card-profit-{{ $p->id }}">₹{{ number_format($desiredProfit, 2) }} ({{ $marginPct }}%)</span>
                                    </div>
                                    <div class="flex items-center justify-between pt-1 border-t border-gray-200">
                                        <span class="font-extrabold text-gray-900">फाइनल सेलिंग रेट:</span>
                                        <span class="font-black text-emerald-700 text-xs font-mono" id="card-price-{{ $p->id }}">₹{{ number_format($finalSellingPrice, 2) }}</span>
                                    </div>

                                    <!-- दोनों प्लेटफॉर्म के सेलिंग रेट्स (Amazon vs Meesho) -->
                                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-200">
                                        <div class="bg-gradient-to-br from-amber-50 to-orange-50 p-2 rounded-xl border border-amber-200 text-center shadow-2xs">
                                            <div class="flex items-center justify-center gap-1 text-[10px] font-black text-amber-950">
                                                <i class="fa-brands fa-amazon text-amber-600 text-xs"></i>
                                                <span>Amazon भाव</span>
                                            </div>
                                            <div class="text-xs font-black text-amber-950 font-mono mt-0.5" id="card-amz-price-{{ $p->id }}">
                                                ₹{{ number_format($calcAmz, 2) }}
                                            </div>
                                            <div class="text-[8px] text-amber-700 font-semibold">(12% फ़ी सहित)</div>
                                        </div>
                                        <div class="bg-gradient-to-br from-pink-50 to-rose-50 p-2 rounded-xl border border-pink-200 text-center shadow-2xs">
                                            <div class="flex items-center justify-center gap-1 text-[10px] font-black text-pink-950">
                                                <span class="text-xs">🌸</span>
                                                <span>Meesho भाव</span>
                                            </div>
                                            <div class="text-xs font-black text-pink-950 font-mono mt-0.5" id="card-msh-price-{{ $p->id }}">
                                                ₹{{ number_format($calcMsh, 2) }}
                                            </div>
                                            <div class="text-[8px] text-pink-700 font-semibold">(0% फ़ी, RTO सुरक्षित)</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2P, 4P, 6P Combo Rates directly on Card -->
                                <div class="p-2.5 bg-gradient-to-r from-purple-50 via-indigo-50 to-purple-50 rounded-2xl border border-purple-200/80 text-[10px] space-y-1.5 shadow-2xs">
                                    <div class="flex items-center justify-between font-black text-purple-950">
                                        <span class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-layer-group text-purple-600"></i>
                                            <span>मल्टी-पैक कॉम्बो रेट्स (2 / 4 / 6 पीस):</span>
                                        </span>
                                        <button type="button" onclick="openComboCalculatorModal({{ $p->id }}, '{{ addslashes($p->name) }}')" class="text-[9px] text-purple-700 hover:text-purple-900 font-bold underline cursor-pointer" title="कॉम्बो खर्च व मुनाफा विवरण खोलें">
                                            कैलकुलेटर ↗
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-3 gap-1.5 text-center font-mono">
                                        <div class="bg-white p-1 rounded-xl border border-purple-200 shadow-2xs">
                                            <div class="text-[8px] font-bold text-gray-500 font-sans">2 पीस पैक</div>
                                            <div class="text-xs font-black text-indigo-950" id="card-combo2-{{ $p->id }}">₹{{ $combo2 }}</div>
                                        </div>
                                        <div class="bg-white p-1 rounded-xl border border-purple-200 shadow-2xs">
                                            <div class="text-[8px] font-bold text-gray-500 font-sans">4 पीस पैक</div>
                                            <div class="text-xs font-black text-purple-950" id="card-combo4-{{ $p->id }}">₹{{ $combo4 }}</div>
                                        </div>
                                        <div class="bg-white p-1 rounded-xl border border-purple-200 shadow-2xs">
                                            <div class="text-[8px] font-bold text-gray-500 font-sans">6 पीस पैक</div>
                                            <div class="text-xs font-black text-emerald-800" id="card-combo6-{{ $p->id }}">₹{{ $combo6 }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Product Sizes & Specs Details -->
                                @php
                                    $pSizes = $p->available_sizes ?? [];
                                @endphp
                                @if(!empty($pSizes))
                                <div class="flex items-center gap-1.5 flex-wrap text-[10px] bg-gradient-to-r from-indigo-50 to-purple-50 p-2 rounded-xl border border-indigo-200/80 shadow-2xs">
                                    <span class="text-indigo-950 font-black flex items-center gap-1">
                                        <i class="fa-solid fa-ruler-combined text-indigo-600"></i>
                                        <span>साइज (Size):</span>
                                    </span>
                                    @foreach($pSizes as $sz)
                                        <span class="px-2.5 py-0.5 rounded-lg bg-white text-indigo-950 font-black border border-indigo-300 shadow-2xs font-mono text-[11px]">
                                            {{ $sz }}
                                        </span>
                                    @endforeach
                                </div>
                                @endif

                                <div class="flex items-center justify-between text-[10px] text-gray-500 px-1">
                                    <span>📦 स्टॉक: <strong class="text-emerald-700 font-extrabold font-mono">{{ $p->stock_quantity ?? 50 }} pcs</strong></span>
                                    <span>🏷️ HSN: <strong class="font-mono text-gray-700 font-bold">{{ $p->hsn_code ?: '39174000' }}</strong></span>
                                </div>

                                <!-- Action Buttons with Prominent Remove from Multichannel Button -->
                                <div class="pt-1 flex items-center justify-between gap-1.5 flex-wrap">
                                    <button type="button" onclick="openCostCalculatorModal({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->price }}, {{ $pCost }})" class="flex-1 py-1.5 px-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[10px] font-black transition flex items-center justify-center gap-1 shadow-2xs cursor-pointer" title="1 पीस खर्च व मुनाफा देखें">
                                        <i class="fa-solid fa-calculator"></i>
                                        <span>1P खर्च</span>
                                    </button>
                                    <button type="button" onclick="openComboCalculatorModal({{ $p->id }}, '{{ addslashes($p->name) }}')" class="flex-1 py-1.5 px-2 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 text-[10px] font-black transition flex items-center justify-center gap-1 shadow-2xs cursor-pointer" title="2P, 4P, 6P कॉम्बो विवरण खोलें">
                                        <i class="fa-solid fa-layer-group"></i>
                                        <span>2/4/6P</span>
                                    </button>
                                    <button type="button" onclick="saveTableRowCosting({{ $p->id }}, this)" class="py-1.5 px-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black transition flex items-center gap-1 shadow-2xs cursor-pointer active:scale-95" title="इस कार्ड के रेट्स सेव करें">
                                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                                        <span>सेव</span>
                                    </button>
                                    <button type="button" onclick="unstageProductItem({{ $p->id }}, '{{ addslashes($p->name) }}')" class="py-1.5 px-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 text-[10px] font-black transition flex items-center gap-1 shadow-2xs cursor-pointer active:scale-95" title="इस प्रोडक्ट को मल्टी-चैनल कतार से हटाएं (दुकान में 100% सुरक्षित रहेगा)">
                                        <i class="fa-solid fa-trash-can text-rose-600 text-xs"></i>
                                        <span>हटाएं</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full p-8 sm:p-12 text-center bg-white rounded-3xl border-2 border-dashed border-amber-200/80 shadow-2xs space-y-4">
                                <div class="h-16 w-16 mx-auto rounded-3xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl border border-amber-200 shadow-xs">
                                    <i class="fa-solid fa-basket-shopping"></i>
                                </div>
                                <div class="space-y-1.5">
                                    <h3 class="text-base font-extrabold text-gray-900">मार्केटप्लेस पेंडिंग बास्केट अभी खाली है</h3>
                                    <p class="text-xs text-gray-600 max-w-lg mx-auto">
                                        आपकी दुकान के <strong>{{ count($allStoreProducts ?? []) }} प्रोडक्ट्स</strong> आपके मुख्य स्टोर कैटलॉग में 100% सुरक्षित हैं। 
                                        Amazon या Meesho पर केवल वही प्रोडक्ट्स जाएँगे जिन्हें आप खुद चुनेंगे।
                                    </p>
                                </div>
                                <div class="pt-2">
                                    <button type="button" onclick="openStageProductsModal()" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-2xl bg-gradient-to-r from-amber-500 via-amber-600 to-amber-700 hover:from-amber-600 hover:to-amber-800 text-white font-black text-xs shadow-md shadow-amber-500/25 transition active:scale-95 cursor-pointer">
                                        <i class="fa-solid fa-plus-circle text-amber-200"></i>
                                        <span>+ दुकान से प्रोडक्ट्स चुनें (Select from Store)</span>
                                    </button>
                                </div>
                            </div>
                        @endforelse

@if(count($pendingProducts ?? []) > 0)
                            <div class="col-span-full pt-3 pb-1 flex items-center justify-between bg-slate-900 text-white px-5 py-3 rounded-3xl shadow-lg border border-slate-800">
                                <span class="text-xs text-slate-300">
                                    कुल <strong class="text-amber-400 font-mono">{{ count($pendingProducts ?? []) }}</strong> प्रोडक्ट्स कतार में
                                </span>
                                <button type="button" onclick="saveAllTableRowsCosting()" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-black text-xs shadow-md transition flex items-center gap-2 cursor-pointer active:scale-95">
                                    <i class="fa-solid fa-floppy-disk text-amber-200"></i>
                                    <span>💾 सभी कार्ड्स एक साथ सेव करें (Save All Cards)</span>
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Pending Products List: 2. SPREADSHEET TABLE GRID VIEW (Unicommerce / EasyEcom Style) -->
                    <div id="pendingTableContainer" class="mt-4 space-y-3">
                        <div class="overflow-x-auto bg-white rounded-3xl border border-gray-200 shadow-sm">
                            <table class="w-full text-left text-xs divide-y divide-gray-200">
                            <thead class="bg-gray-100 text-[10px] font-black uppercase text-gray-600 tracking-wider">
                                <tr>
                                    <th class="p-3 w-8 text-center">✓</th>
                                    <th class="p-3">फ़ोटो</th>
                                    <th class="p-3 min-w-[170px]">प्रोडक्ट का नाम & SKU</th>
                                    <th class="p-3 min-w-[70px] text-center font-mono text-gray-500">ID</th>
                                    <th class="p-3 min-w-[90px] bg-emerald-50 text-emerald-950 font-black text-center" title="उपलब्ध इन्वेंट्री / स्टॉक की संख्या">📦 स्टॉक (Qty)</th>
                                    <th class="p-3 min-w-[95px] bg-amber-50 text-amber-950 font-black">MRP (लिस्ट ₹)</th>
                                    <th class="p-3 min-w-[75px] bg-amber-50 text-amber-950 font-black">Less %</th>
                                    <th class="p-3 min-w-[105px] bg-slate-50 text-slate-900 font-extrabold" title="आपकी खरीद लागत (GST सहित)">खरीद लागत (With GST ₹)</th>
                                    <th class="p-3 min-w-[105px] bg-blue-50 text-blue-950 font-black" title="दुकान / ऐप का रिटेल बिक्री भाव (Selling Price 1)">सेलिंग भाव 1 (दुकान ₹)</th>
                                    <th class="p-3 min-w-[90px] bg-indigo-50 text-indigo-950 font-black" title="मार्केटप्लेस के लिए आपका मनपसंद मुनाफा %">मार्जिन (%)</th>
                                    <th class="p-3 min-w-[85px] bg-rose-50 text-rose-950 font-black" title="रिटर्न/RTO नुकसान से सुरक्षा">🔄 रिटर्न बफ़र (₹)</th>
                                    <th class="p-3 min-w-[70px]">पैकिंग (₹)</th>
                                    <th class="p-3 min-w-[75px]">कूरियर (₹)</th>
                                    <th class="p-3 min-w-[75px]">फ़ी (%)</th>
                                    <th class="p-3 min-w-[75px]">ऐड्स (₹)</th>
                                    <th class="p-3 min-w-[75px] bg-purple-50 text-purple-950 font-black">HSN कोड</th>
                                    <th class="p-3 min-w-[75px] bg-purple-50 text-purple-950 font-black">GST %</th>
                                    <th class="p-3 min-w-[120px] bg-amber-50 text-amber-950 font-black">फाइनल सेलिंग (₹)</th>
                                    <th class="p-3 min-w-[130px] text-center">2 / 4 / 6 पीस कॉम्बो</th>
                                    <th class="p-3 min-w-[110px]" title="कस्टम बारकोड, रैक नंबर या आंतरिक नोट">बारकोड / नोट</th>
                                    <th class="p-3 text-right min-w-[90px]">एक्शन</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-[11px]" id="pendingTableTbody">
                                @forelse($pendingProducts ?? [] as $p)
                                    @php
                                        $imgCount = $p->images ? $p->images->count() : 0;
                                        if ($imgCount === 0 && !empty($p->image)) $imgCount = 1;
                                        $ue = $p->attributes['unit_economics'] ?? [];
                                        $pCost = (float)($ue['purchase_cost'] ?? $p->purchase_price ?? 0);
                                        $mrpVal = (float)($p->mrp ?: ($p->variants->where('mrp', '>', 0)->min('mrp') ?: $p->price));
                                        $lessVal = ($p->trade_discount_percent !== null && (float)$p->trade_discount_percent > 0)
                                            ? (float)$p->trade_discount_percent
                                            : ($p->variants->where('trade_discount_percent', '>', 0)->max('trade_discount_percent') ?: ($ue['trade_discount_percent'] ?? ''));
                                        $marginPct = (float)($ue['margin_pct'] ?? 25);
                                        $rtoBuffer = (float)($ue['rto_buffer'] ?? 20);
                                        $packCost = (float)($ue['packaging'] ?? 15);
                                        $shipCost = (float)($ue['shipping'] ?? 45);
                                        $feePct = (float)($ue['platform_fee_pct'] ?? 12);
                                        $adsCost = (float)($ue['ads'] ?? 10);
                                        $otherCost = (float)($ue['others'] ?? 5);

                                        $desiredProfit = round($pCost * ($marginPct / 100));
                                        $gstPercent = (float)($p->gst_percent ?: 18);
                                        $g = $gstPercent / 100;
                                        $f = $feePct / 100;
                                        $cTax = $g > 0 ? ($pCost / (1 + $g)) : $pCost;
                                        $denom = max(0.05, (1 / (1 + $g)) - $f);
                                        $fixedExp = $rtoBuffer + $packCost + $shipCost + $adsCost + $otherCost;
                                        $calcSellingPrice = ceil(($cTax + $fixedExp + $desiredProfit) / $denom);
                                        $finalSellingPrice = !empty($ue['marketplace_price']) ? (float)$ue['marketplace_price'] : max($calcSellingPrice, (float)$p->price);

                                        $combo2 = !empty($ue['combo_2']) ? (float)$ue['combo_2'] : ceil((($cTax * 2) + ($desiredProfit * 1.8) + ($rtoBuffer * 1.5) + ($packCost * 1.3) + $shipCost + ($adsCost * 1.2)) / $denom);
                                        $combo4 = !empty($ue['combo_4']) ? (float)$ue['combo_4'] : ceil((($cTax * 4) + ($desiredProfit * 3.5) + ($rtoBuffer * 2.0) + ($packCost * 1.6) + ($shipCost * 1.2) + ($adsCost * 1.5)) / $denom);
                                        $combo6 = !empty($ue['combo_6']) ? (float)$ue['combo_6'] : ceil((($cTax * 6) + ($desiredProfit * 5.0) + ($rtoBuffer * 2.5) + ($packCost * 2.0) + ($shipCost * 1.4) + ($adsCost * 1.8)) / $denom);

                                        // Side-by-Side Platform Prices (Amazon vs Meesho)
                                        $denomAmz = max(0.05, (1 / (1 + $g)) - 0.12);
                                        $fixedAmz = $rtoBuffer + $packCost + $shipCost + $adsCost + $otherCost;
                                        $calcAmz = ceil(($cTax + $fixedAmz + $desiredProfit) / $denomAmz);

                                        $denomMsh = max(0.05, (1 / (1 + $g)));
                                        $fixedMsh = max(35, $rtoBuffer) + $packCost + min(38, $shipCost) + $otherCost;
                                        $calcMsh = ceil(($cTax + $fixedMsh + $desiredProfit) / $denomMsh);
                                    @endphp
                                    <tr class="hover:bg-indigo-50/20 transition group" id="table-row-{{ $p->id }}" data-id="{{ $p->id }}" data-cat-id="{{ $p->category_id ?? 0 }}">
                                        <td class="p-3 text-center">
                                            <input type="checkbox" name="product_ids[]" value="{{ $p->id }}" checked class="pending-prod-chk rounded text-indigo-600 h-4 w-4 cursor-pointer">
                                        </td>
                                        <td class="p-2">
                                            <div class="relative h-12 w-12 rounded-xl bg-gray-50 border border-gray-200 overflow-hidden flex items-center justify-center p-0.5 cursor-pointer hover:ring-2 hover:ring-indigo-300 group/rowimg"
                                                 onclick="triggerMultipleImageUpload({{ $p->id }}, '{{ addslashes($p->name) }}')"
                                                 title="फ़ोटो बदलें या और फ़ोटो जोड़ें">
                                                <img src="{{ $p->image_url ?? '' }}" alt="{{ $p->name }}" id="table-prod-img-{{ $p->id }}" class="h-full w-full object-contain {{ !empty($p->image_url) ? '' : 'hidden' }}">
                                                <i class="fa-solid fa-camera text-gray-300 {{ !empty($p->image_url) ? 'hidden' : 'block' }}" id="table-prod-ph-{{ $p->id }}"></i>
                                                <span class="absolute bottom-0.5 right-0.5 bg-slate-900/80 text-white text-[7px] font-bold px-1 rounded" id="table-img-badge-{{ $p->id }}">
                                                    {{ $imgCount }} फ़ोटो
                                                </span>
                                            </div>
                                        </td>
                                        <td class="p-3">
                                            <span class="font-extrabold text-gray-900 block truncate max-w-[170px]" title="{{ $p->name }}">{{ $p->name }}</span>
                                            <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                                <span class="text-[9px] text-gray-400 font-mono">SKU: {{ $p->sku ?: 'STD' }}</span>
                                                @foreach($p->available_sizes ?? [] as $sz)
                                                    <span class="px-1.5 py-0.2 rounded bg-indigo-50 text-indigo-900 border border-indigo-200 text-[9px] font-black font-mono">
                                                        📐 {{ $sz }}
                                                    </span>
                                                @endforeach
                                                <button type="button" onclick="triggerMultipleImageUpload({{ $p->id }}, '{{ addslashes($p->name) }}')" class="text-[9px] font-bold text-indigo-600 hover:text-indigo-800">
                                                    +फ़ोटो
                                                </button>
                                            </div>
                                        </td>

                                        <!-- Product ID -->
                                        <td class="p-2 text-center font-mono text-xs font-extrabold text-gray-500">
                                            #{{ $p->id }}
                                        </td>

                                        <!-- Stock Quantity (Editable with Out-of-Stock Alert) -->
                                        <td class="p-2 text-center {{ ($p->stock_quantity ?? 0) <= 0 ? 'bg-rose-50/70' : 'bg-emerald-50/30' }}" id="row-stock-cell-{{ $p->id }}">
                                            <div class="inline-flex flex-col items-center">
                                                <input type="number" min="0" 
                                                    id="row-stock-{{ $p->id }}" 
                                                    value="{{ $p->stock_quantity ?? 0 }}" 
                                                    oninput="updateStockStatusBadge({{ $p->id }})" 
                                                    class="w-16 px-1 py-1 text-center font-mono font-black text-xs rounded-lg border {{ ($p->stock_quantity ?? 0) <= 0 ? 'border-rose-400 bg-rose-50 text-rose-700' : 'border-emerald-300 bg-white text-emerald-900' }}" 
                                                    title="उपलब्ध स्टॉक (इन्वेंट्री पीस)">
                                                <span class="text-[9px] mt-0.5 {{ ($p->stock_quantity ?? 0) <= 0 ? 'text-rose-600 font-black' : 'text-emerald-700 font-bold' }}" id="row-stock-warn-{{ $p->id }}">
                                                    {{ ($p->stock_quantity ?? 0) <= 0 ? '⚠️ 0 स्टॉक' : '✓ तैयार' }}
                                                </span>
                                            </div>
                                        </td>

                                        <!-- MRP & Less % -->
                                        <td class="p-2 bg-amber-50/40 text-center font-mono font-bold text-amber-950">
                                            @if($mrpVal > 0)
                                                ₹{{ number_format($mrpVal, 2) }}
                                            @else
                                                <span class="text-gray-400 text-[10px]">-</span>
                                            @endif
                                        </td>
                                        <td class="p-2 bg-amber-50/40 text-center">
                                            <div class="inline-flex items-center gap-0.5">
                                                <input type="number" step="0.5" min="0" max="99" 
                                                    id="row-less-{{ $p->id }}" 
                                                    data-mrp="{{ $mrpVal }}" 
                                                    value="{{ $lessVal !== '' && $lessVal > 0 ? $lessVal : '' }}" 
                                                    oninput="recalcLessToCost({{ $p->id }})" 
                                                    class="w-14 px-1 py-1 bg-white border border-amber-300 rounded-lg text-xs font-mono font-black text-amber-900 text-center focus:border-amber-600 focus:outline-none" 
                                                    title="MRP पर डिस्काउंट % (Less %)" placeholder="Less %">
                                                <span class="font-bold text-amber-800 text-[10px]">%</span>
                                            </div>
                                        </td>

                                        <!-- 1. Purchase Cost (P. Cost) - Editable -->
                                        <td class="p-2 bg-slate-50/50">
                                            <div class="relative">
                                                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 font-bold text-[10px]">₹</span>
                                                <input type="number" step="0.5" id="row-cost-{{ $p->id }}" value="{{ number_format($pCost, 2, '.', '') }}" oninput="recalcTableRow({{ $p->id }})" class="w-20 pl-5 pr-1.5 py-1 bg-white border border-slate-300 rounded-lg text-xs font-mono font-extrabold text-slate-900 focus:border-indigo-500 focus:outline-none" title="आपकी खरीद लागत">
                                            </div>
                                        </td>

                                        <!-- 1.2 Store / App Original Selling Price (Non-destructive Reference) -->
                                        <td class="p-2 bg-blue-50/40 text-center">
                                            <span class="inline-block px-2.5 py-1 bg-white border border-blue-200 rounded-lg font-mono font-black text-xs text-blue-900 shadow-2xs" title="ऐप/दुकान में डाला गया मूल सेलिंग रेट">
                                                ₹{{ number_format($p->price, 2) }}
                                            </span>
                                        </td>

                                        <!-- 1.5 Your Profit Margin (%) -->
                                        <td class="p-2 bg-indigo-50/40">
                                            <div class="flex items-center gap-1">
                                                <div class="relative">
                                                    <input type="number" id="row-margin-{{ $p->id }}" value="{{ (float)($ue['margin_pct'] ?? 25) }}" oninput="syncMarginToPreset({{ $p->id }})" class="w-16 pl-2 pr-4 py-1 bg-white border border-indigo-300 rounded-lg text-xs font-mono font-black text-indigo-800 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-300 focus:outline-none" title="मार्जिन % लिखें">
                                                    <span class="absolute right-1 top-1/2 -translate-y-1/2 text-indigo-400 font-bold text-[10px]">%</span>
                                                </div>
                                                <button type="button" onclick="copyRowSettingsToAll({{ $p->id }})" class="h-6 w-6 rounded-md bg-indigo-100 hover:bg-indigo-600 text-indigo-700 hover:text-white flex items-center justify-center text-[10px] transition cursor-pointer shrink-0" title="इस रो का मार्जिन सभी में कॉपी करें (Apply to All)">
                                                    <i class="fa-solid fa-copy"></i>
                                                </button>
                                            </div>
                                            <span id="row-margin-amt-{{ $p->id }}" class="text-[9px] text-emerald-700 font-extrabold block font-mono mt-0.5">
                                                ₹{{ round($pCost * (((float)($ue['margin_pct'] ?? 25)) / 100)) }} लाभ
                                            </span>
                                        </td>

                                        <!-- 1.8 Return / RTO Buffer (₹) -->
                                        <td class="p-2 bg-rose-50/40">
                                            <div class="relative">
                                                <span class="absolute left-1.5 top-1/2 -translate-y-1/2 text-rose-400 text-[10px]">₹</span>
                                                <input type="number" id="row-rto-{{ $p->id }}" value="{{ (float)($ue['rto_buffer'] ?? 20) }}" oninput="recalcTableRow({{ $p->id }})" class="w-16 pl-4 pr-1.5 py-1 bg-white border border-rose-300 rounded-lg text-xs font-mono font-bold text-rose-700 focus:border-rose-500 focus:outline-none" title="रिटर्न/RTO नुकसान बफ़र">
                                            </div>
                                        </td>

                                        <!-- 2. Packaging (₹) -->
                                        <td class="p-2">
                                            <input type="number" id="row-pack-{{ $p->id }}" value="{{ $packCost }}" oninput="recalcTableRow({{ $p->id }})" class="w-16 px-2 py-1 bg-white border border-gray-200 rounded-lg text-xs font-mono text-gray-800 focus:border-indigo-500 focus:outline-none">
                                        </td>

                                        <!-- 3. Courier / Shipping (₹) -->
                                        <td class="p-2">
                                            <input type="number" id="row-ship-{{ $p->id }}" value="{{ $shipCost }}" oninput="recalcTableRow({{ $p->id }})" class="w-16 px-2 py-1 bg-white border border-gray-200 rounded-lg text-xs font-mono text-gray-800 focus:border-indigo-500 focus:outline-none">
                                        </td>

                                        <!-- 4. Marketplace Platform Fee (%) -->
                                        <td class="p-2">
                                            <div class="relative">
                                                <input type="number" id="row-fee-{{ $p->id }}" value="{{ $feePct }}" oninput="recalcTableRow({{ $p->id }})" class="w-16 pl-2 pr-4 py-1 bg-white border border-gray-200 rounded-lg text-xs font-mono text-gray-800 focus:border-indigo-500 focus:outline-none">
                                                <span class="absolute right-1.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]">%</span>
                                            </div>
                                        </td>

                                        <!-- 5. Ads & Marketing (₹) -->
                                        <td class="p-2">
                                            <input type="number" id="row-ads-{{ $p->id }}" value="{{ $adsCost }}" oninput="recalcTableRow({{ $p->id }})" class="w-16 px-2 py-1 bg-white border border-gray-200 rounded-lg text-xs font-mono text-gray-800 focus:border-indigo-500 focus:outline-none">
                                        </td>

                                        <!-- HSN Code (Towards end before Selling Price) -->
                                        <td class="p-2 bg-purple-50/30">
                                            <input type="text" id="row-hsn-{{ $p->id }}" value="{{ $p->hsn_code ?: '6204' }}" class="w-16 px-1.5 py-1 bg-white border border-purple-200 rounded-lg text-xs font-mono font-bold text-gray-800 focus:border-purple-500 focus:outline-none" placeholder="HSN">
                                        </td>

                                        <!-- GST % (Towards end before Selling Price) -->
                                        <td class="p-2 bg-purple-50/30">
                                            <select id="row-gst-{{ $p->id }}" onchange="recalcTableRow({{ $p->id }})" class="w-16 px-1 py-1 bg-white border border-purple-200 rounded-lg text-xs font-mono font-bold text-gray-800 focus:border-purple-500 focus:outline-none">
                                                <option value="18" {{ (!isset($p->gst_percent) || $p->gst_percent == 18) ? 'selected' : '' }}>18%</option>
                                                <option value="12" {{ ($p->gst_percent == 12) ? 'selected' : '' }}>12%</option>
                                                <option value="5" {{ ($p->gst_percent == 5) ? 'selected' : '' }}>5%</option>
                                                <option value="0" {{ (isset($p->gst_percent) && $p->gst_percent == 0) ? 'selected' : '' }}>0% (Exempt)</option>
                                                <option value="28" {{ ($p->gst_percent == 28) ? 'selected' : '' }}>28%</option>
                                            </select>
                                        </td>

                                        <!-- 7. Final Selling Price (₹) -->
                                        <td class="p-2 bg-amber-50/50">
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-1.5">
                                                    <div class="relative">
                                                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-amber-600 font-bold text-[10px]">₹</span>
                                                        <input type="number" step="1" id="row-price-{{ $p->id }}" value="{{ $finalSellingPrice }}" oninput="syncManualPriceToCard({{ $p->id }})" class="w-20 pl-5 pr-1.5 py-1 bg-white border border-amber-300 rounded-lg text-xs font-mono font-black text-amber-950 focus:border-amber-500 focus:outline-none" title="फाइनल सेलिंग रेट (एडिट कर सकते हैं)">
                                                    </div>
                                                    <button type="button" onclick="saveTableRowCosting({{ $p->id }}, this)" class="h-7 w-7 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center transition shadow-2xs cursor-pointer" title="इस रो का रेट सेव करें">
                                                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                                                    </button>
                                                </div>
                                                <div class="flex items-center gap-1 text-[9px] font-mono">
                                                    <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-900 border border-amber-200 font-bold whitespace-nowrap" title="Amazon अनुमानित भाव (12% फ़ी सहित)">
                                                        🟠 Amz: ₹<span id="row-amz-price-{{ $p->id }}">{{ $calcAmz }}</span>
                                                    </span>
                                                    <span class="px-1.5 py-0.5 rounded bg-pink-100 text-pink-900 border border-pink-200 font-bold whitespace-nowrap" title="Meesho अनुमानित भाव (0% फ़ी, RTO सुरक्षित)">
                                                        🌸 Msh: ₹<span id="row-msh-price-{{ $p->id }}">{{ $calcMsh }}</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- 8. Real-Time Editable Combo Rates (2/4/6 Pack) -->
                                        <td class="p-2 text-center bg-gray-50/50">
                                            <div class="inline-flex items-center gap-1 font-mono text-[10px]">
                                                <div class="flex flex-col items-center">
                                                    <span class="text-[8px] font-bold text-gray-500">2P ₹</span>
                                                    <input type="number" id="row-combo2-{{ $p->id }}" value="{{ $combo2 }}" oninput="syncComboToCard({{ $p->id }}, 2, this.value)" class="w-14 px-1 py-0.5 bg-white border border-gray-300 rounded text-center text-[10px] font-mono font-bold text-indigo-950 focus:border-indigo-500 focus:outline-none" title="2 पीस पैक का सेलिंग रेट (एडिट कर सकते हैं)">
                                                </div>
                                                <div class="flex flex-col items-center">
                                                    <span class="text-[8px] font-bold text-gray-500">4P ₹</span>
                                                    <input type="number" id="row-combo4-{{ $p->id }}" value="{{ $combo4 }}" oninput="syncComboToCard({{ $p->id }}, 4, this.value)" class="w-14 px-1 py-0.5 bg-white border border-gray-300 rounded text-center text-[10px] font-mono font-bold text-indigo-950 focus:border-indigo-500 focus:outline-none" title="4 पीस पैक का सेलिंग रेट (एडिट कर सकते हैं)">
                                                </div>
                                                <div class="flex flex-col items-center">
                                                    <span class="text-[8px] font-bold text-gray-500">6P ₹</span>
                                                    <input type="number" id="row-combo6-{{ $p->id }}" value="{{ $combo6 }}" oninput="syncComboToCard({{ $p->id }}, 6, this.value)" class="w-14 px-1 py-0.5 bg-white border border-gray-300 rounded text-center text-[10px] font-mono font-bold text-indigo-950 focus:border-indigo-500 focus:outline-none" title="6 पीस पैक का सेलिंग रेट (एडिट कर सकते हैं)">
                                                </div>
                                                <button type="button" onclick="openComboCalculatorModal({{ $p->id }}, '{{ addslashes($p->name) }}')" class="h-8 px-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 flex flex-col items-center justify-center transition shadow-2xs cursor-pointer ml-0.5" title="2P/4P/6P खर्च, कूरियर व मुनाफा पॉपअप में देखें व बदलें">
                                                    <i class="fa-solid fa-eye text-xs"></i>
                                                    <span class="text-[7px] font-black uppercase">विवरण</span>
                                                </button>
                                            </div>
                                        </td>

                                        <!-- Custom Barcode / Note / Rack Column -->
                                        <td class="p-2">
                                            <input type="text" id="row-note-{{ $p->id }}" value="{{ $ue['custom_note'] ?? '' }}" placeholder="रैक / बारकोड" class="w-24 px-2 py-1 bg-white border border-gray-200 rounded-lg text-xs font-mono text-gray-700 focus:border-indigo-500 focus:outline-none" title="कस्टम बारकोड या आंतरिक नोट">
                                        </td>

                                        <!-- 9. Quick Actions -->
                                        <td class="p-3 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <button type="button" onclick="openComboCalculatorModal({{ $p->id }}, '{{ addslashes($p->name) }}')" class="p-1.5 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 transition cursor-pointer" title="2P, 4P, 6P कॉम्बो खर्च व सेलिंग विवरण (पॉपअप)">
                                                    <i class="fa-solid fa-layer-group text-xs"></i>
                                                </button>
                                                <button type="button" onclick="openCostCalculatorModal({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->price }}, {{ $pCost }})" class="p-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition" title="डिटेल कैलकुलेटर खोलें">
                                                    <i class="fa-solid fa-calculator text-xs"></i>
                                                </button>
                                                <button type="button" onclick="unstageProductItem({{ $p->id }}, '{{ addslashes($p->name) }}')" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition cursor-pointer" title="मार्केटप्लेस बास्केट से हटाएं (दुकान में सुरक्षित रहेगा)">
                                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="p-12 text-center text-gray-500 bg-white">
                                            <div class="max-w-md mx-auto space-y-3">
                                                <i class="fa-solid fa-basket-shopping text-3xl text-amber-500"></i>
                                                <p class="font-extrabold text-sm text-gray-900">मार्केटप्लेस पेंडिंग बास्केट खाली है</p>
                                                <p class="text-xs text-gray-500">आपकी दुकान के <strong>{{ count($allStoreProducts ?? []) }} प्रोडक्ट्स</strong> आपके मुख्य स्टोर में 100% सुरक्षित हैं। Amazon / Meesho पर बेचने के लिए नीचे बटन से प्रोडक्ट्स चुनें:</p>
                                                <button type="button" onclick="openStageProductsModal()" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-xs transition cursor-pointer">
                                                    <i class="fa-solid fa-plus-circle text-amber-200"></i>
                                                    <span>+ दुकान से प्रोडक्ट्स चुनें</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if(count($pendingProducts ?? []) > 0)
                            <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                                <tr>
                                    <td colspan="15" class="p-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-600 font-bold">
                                                कुल <strong class="text-indigo-600 font-mono">{{ count($pendingProducts ?? []) }}</strong> प्रोडक्ट्स कतार में
                                            </span>
                                            <button type="button" onclick="saveAllTableRowsCosting()" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs shadow-md transition flex items-center gap-2 cursor-pointer active:scale-95">
                                                <i class="fa-solid fa-floppy-disk text-amber-200"></i>
                                                <span>💾 सभी रोज़ के बदलाव एक साथ सेव करें (Save All Changes)</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                        </div>
                    </div>

        <!-- ======================================================== -->
        <!-- 🔌 MARKETPLACE API INTEGRATION CONFIGURATION CARDS       -->
        <!-- ======================================================== -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-black text-gray-900">कनेक्टेड मार्केटप्लेस API क्रेडेंशियल्स</h2>
                    <p class="text-xs text-gray-500">Amazon SP-API, Meesho, Flipkart, Myntra व Blinkit की API Key यहाँ सुरक्षित दर्ज करें</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($supportedChannels as $channelKey => $info)
                    @php
                        $connected = $channels->firstWhere('channel_name', $channelKey);
                    @endphp
                    <div class="bg-white rounded-3xl border border-gray-200 p-5 shadow-xs flex flex-col justify-between space-y-5 hover:border-indigo-300 transition">
                        <div>
                            <div class="flex items-start justify-between">
                                <div class="h-12 w-12 rounded-2xl {{ $info['bg'] }} {{ $info['color'] }} flex items-center justify-center text-xl shadow-2xs">
                                    <i class="{{ $info['icon'] }}"></i>
                                </div>
                                @if($connected && $connected->is_active)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>ACTIVE API</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500">
                                        NOT CONNECTED
                                    </span>
                                @endif
                            </div>

                            <div class="mt-3.5">
                                <h3 class="font-black text-sm text-gray-900">{{ $info['name'] }}</h3>
                                <p class="text-[11px] text-gray-500 mt-0.5">
                                    {{ $connected ? 'Store: ' . $connected->store_name : '24x7 ऑटो इन्वेंटरी, रेट्स व आर्डर वेबहुक सिंक।' }}
                                </p>
                                @if($connected && $connected->store_url)
                                    <div class="text-[10px] text-emerald-800 font-mono font-bold mt-1 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200 flex items-center gap-1.5">
                                        <i class="fa-solid fa-key text-[9px] text-emerald-600"></i>
                                        <span>Token: {{ Str::limit($connected->store_url, 18) }}</span>
                                        <span class="text-[9px] text-emerald-600 font-extrabold ml-auto">✓ Saved</span>
                                    </div>
                                @endif
                                <span class="inline-block mt-1 text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">
                                    पिकअप SLA: {{ $info['sla'] }}
                                </span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-gray-100 flex items-center gap-2">
                            @if($connected)
                                <button type="button" onclick="testChannelConnection('{{ $channelKey }}')" class="py-2 px-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-extrabold text-xs transition flex items-center justify-center gap-1 shadow-2xs" title="लाइव लिंक व परमिशन टेस्ट करें">
                                    <i class="fa-solid fa-bolt"></i>
                                    <span>टेस्ट लिंक</span>
                                </button>
                                <button type="button" onclick="openConnectModal('{{ $channelKey }}', '{{ $info['name'] }}', '{{ $connected->store_name }}', '{{ $connected->store_url }}', '{{ $connected->api_key }}', '{{ $connected->api_secret }}', '{{ $connected->access_token }}')" class="flex-1 py-2 px-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-extrabold text-xs transition truncate">
                                    API सेटिंग्स
                                </button>
                                <form action="{{ route('seller.channels.toggle', $connected->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="py-2 px-3 rounded-xl {{ $connected->is_active ? 'bg-rose-50 text-rose-700 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }} font-bold text-xs transition">
                                        {{ $connected->is_active ? 'Pause' : 'Activate' }}
                                    </button>
                                </form>
                            @else
                                <button type="button" onclick="openConnectModal('{{ $channelKey }}', '{{ $info['name'] }}')" class="w-full py-2.5 px-4 rounded-xl bg-gray-900 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                                    <i class="fa-solid fa-plug text-amber-300"></i>
                                    <span>API Key से कनेक्ट करें</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        
        <!-- ======================================================== -->
        <!-- 🤖 AI MARKET OPPORTUNITY EXPLORER & STUDIO PHOTO PROMPT  -->
        <!-- (Analyze Market Size, Best Selling Rates & Image Prompts)-->
        <!-- ======================================================== -->
        <div class="bg-white rounded-3xl border border-indigo-200 shadow-md p-6 sm:p-7 space-y-6 relative overflow-hidden">
            <div class="absolute -right-12 -top-12 w-44 h-44 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="h-11 w-11 rounded-2xl bg-gradient-to-tr from-indigo-600 via-purple-600 to-pink-600 text-white flex items-center justify-center text-lg font-black shadow-md shadow-indigo-600/30 shrink-0">
                        <i class="fa-solid fa-wand-magic-sparkles text-amber-300"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-black text-gray-900">
                                AI मार्केट इंटेलिजेंस & प्रोफेशनल लिस्टिंग जनरेटर
                            </h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-purple-100 text-purple-800 border border-purple-200 uppercase">
                                Amazon & Meesho Opportunity Engine
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">
                            किसी भी प्रोडक्ट (उदा. <strong>PTMT Nal</strong>, <strong>CPVC Brass Tee</strong>, <strong>Wall Tiles</strong>) का मार्केट साइज, बिकने वाला सही भाव और AI फोटो प्रॉम्प्ट तुरंत निकालें।
                        </p>
                    </div>
                </div>
            </div>

            
            <!-- 🔍 1-Click Shop Audit & Marketplace Auto-Optimizer Bar -->
            <div class="p-4 bg-gradient-to-r from-indigo-900 via-indigo-950 to-slate-900 text-white rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 border border-indigo-700/60 shadow-md">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-amber-400 text-slate-950 flex items-center justify-center text-lg font-black shrink-0">
                        <i class="fa-solid fa-radar text-base"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-xs sm:text-sm font-black text-white">
                                पूरी दुकान स्कैन करें & मार्केट रिसर्च से ऑटो-लिस्ट करें (1-Click Shop AI Audit)
                            </h3>
                            <span class="px-2 py-0.5 rounded-full text-[9px] bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30">Zero Copy-Paste</span>
                        </div>
                        <p class="text-[11px] text-slate-300 mt-0.5">
                            AI आपकी दुकान के सभी प्रोडक्ट्स को स्कैन करके अमेज़न/मीशो के कॉम्पिटिटर्स के टाइटल्स, स्टार रेटिंग्स, सबसे ज़्यादा मांग वाले राज्य व रिटर्न रेट्स निकालेगा — <strong>रेट्स और फोटो सुरक्षित रहेंगे</strong>।
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" onclick="startFullShopAiScan('amazon')" id="btnStartShopScan" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-400 via-amber-500 to-orange-500 hover:from-amber-500 hover:to-orange-600 text-slate-950 font-black text-xs shadow-md transition active:scale-95 cursor-pointer flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass-chart"></i>
                        <span>🔍 पूरी दुकान का मार्केट स्कैन करें</span>
                    </button>
                </div>
            </div>


            <!-- Input Box -->
            <div class="flex flex-col sm:flex-row items-stretch gap-2.5">
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" id="aiMarketKeywordInput" placeholder="जिस प्रोडक्ट का मार्केट डेटा देखना है उसका नाम लिखें (उदा. अपने प्रोडक्ट का नाम)..." value="{{ $pendingProducts->first()->name ?? '' }}" class="w-full pl-9 pr-4 py-2.5 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 text-xs font-semibold outline-none transition">
                </div>
                <button type="button" onclick="analyzeMarketWithAi()" id="btnAnalyzeMarketAi" class="px-6 py-2.5 rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-700 hover:from-indigo-700 hover:to-purple-700 text-white font-black text-xs shadow-md shadow-indigo-600/30 flex items-center justify-center gap-2 transition active:scale-95 cursor-pointer shrink-0">
                    <i class="fa-solid fa-bolt-lightning text-amber-300"></i>
                    <span>⚡ मार्केट डेटा & लिस्टिंग बनाएं</span>
                </button>
            </div>

            <!-- Dynamic AI Results Container -->
            <div id="aiMarketResultsBox" class="space-y-5 {{ !empty($pendingProducts) && $pendingProducts->count() > 0 ? '' : 'hidden' }}">
                <!-- 1. Market Size & Demand Summary Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 text-xs">
                    <div class="bg-indigo-50/70 p-3.5 rounded-2xl border border-indigo-100 space-y-1">
                        <span class="text-[10px] font-bold text-indigo-700 uppercase tracking-wider block">मंथली डिमांड (सर्च वॉल्यूम)</span>
                        <div class="font-black text-sm text-indigo-950" id="aiResSearchVolume">65,000+ मंथली सर्च (Amazon & Meesho)</div>
                        <span class="text-[10px] text-indigo-600">अत्यधिक मांग वाला रोज़मर्रा का सामान</span>
                    </div>

                    <div class="bg-emerald-50/70 p-3.5 rounded-2xl border border-emerald-100 space-y-1">
                        <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block">सबसे ज़्यादा बिकने वाला भाव (Sweet Spot)</span>
                        <div class="font-black text-sm text-emerald-950" id="aiResSweetSpot">₹149 - ₹249 (Pack of 1 या 2)</div>
                        <span class="text-[10px] text-emerald-600">इस रेट पर सबसे तेज़ आर्डर्स आते हैं</span>
                    </div>

                    <div class="bg-amber-50/70 p-3.5 rounded-2xl border border-amber-100 space-y-1">
                        <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider block">सुझाया गया HSN कोड व GST</span>
                        <div class="font-black text-sm text-amber-950" id="aiResHsnCode">HSN: 39229000 (18% GST)</div>
                        <span class="text-[10px] text-amber-700">Sanitaryware & Taps (B2B Tax Credit)</span>
                    </div>

                    <div class="bg-purple-50/70 p-3.5 rounded-2xl border border-purple-100 space-y-1">
                        <span class="text-[10px] font-bold text-purple-800 uppercase tracking-wider block">मार्केट कॉम्पिटिशन स्तर</span>
                        <div class="font-black text-sm text-purple-950" id="aiResDemandLevel">High Demand (Low Return Rate)</div>
                        <span class="text-[10px] text-purple-700">पैकिंग कम्बो (Pack of 2) में 3x बिक्री</span>
                    </div>
                </div>

                <!-- 2. Professional Listing Copy & AI Studio Photography Prompt -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 text-xs">
                    <!-- Left: Professional Title & Bullet Points -->
                    <div class="bg-gray-50/80 rounded-2xl p-4 border border-gray-200 space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-200">
                            <span class="font-black text-gray-900 text-xs flex items-center gap-1.5">
                                <i class="fa-brands fa-amazon text-amber-600"></i>
                                <span>प्रोफेशनल मार्केटप्लेस टाइटल & डिस्क्रिप्शन</span>
                            </span>
                            <button type="button" onclick="copyAiListingCopy()" class="px-2.5 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-[10px] transition flex items-center gap-1 shadow-2xs cursor-pointer">
                                <i class="fa-regular fa-copy"></i>
                                <span id="btnCopyListingText">कॉपी करें</span>
                            </button>
                        </div>

                        <div>
                            <span class="text-[10px] font-bold text-gray-400 block mb-0.5">हाई-कन्वर्टिंग SEO टाइटल:</span>
                            <div class="p-2.5 bg-white rounded-xl border border-gray-200 font-bold text-gray-800 leading-snug text-xs" id="aiResTitle">
                                Heavy Duty Unbreakable PTMT Bib Cock Tap (15mm / 1/2 Inch) with Wall Flange - Anti-Drip Foam Flow for Bathroom & Kitchen
                            </div>
                        </div>

                        <div>
                            <span class="text-[10px] font-bold text-gray-400 block mb-0.5">5 बुलेट पॉइंट्स & टेक्निकल स्पेसिफिकेशन्स:</span>
                            <div class="p-2.5 bg-white rounded-xl border border-gray-200 text-gray-700 whitespace-pre-line text-[11px] leading-relaxed max-h-48 overflow-y-auto" id="aiResDesc">
✨ **Upgrade your bathroom & kitchen fittings with the heavy-duty unbreakable PTMT Bib Cock Tap.**

• **Material & Grade:** 100% Virgin Food-Grade PTMT Polymer (Unbreakable & Corrosion-Free)
• **Flow & Aerator:** German-engineered foam flow aerator saves water while ensuring smooth splash-free stream
• **Cartridge:** Heavy-duty ceramic disc quarter-turn spindle tested for 5,00,000+ on/off cycles
• **Fitting Size:** Standard 15mm (1/2 Inch BSP Thread) suitable for all Indian bathroom connections
• **Package Contents:** 1 Unit PTMT Tap + 1 Heavy Matching Wall Flange
                            </div>
                        </div>
                    </div>

                    <!-- Right: AI Studio Photography Prompt Generator -->
                    <div class="bg-gradient-to-b from-indigo-950 to-slate-900 text-white rounded-2xl p-4 border border-indigo-900 space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-indigo-800">
                            <span class="font-black text-amber-400 text-xs flex items-center gap-1.5">
                                <i class="fa-solid fa-camera-retro"></i>
                                <span>AI इमेज जनरेटर प्रॉम्प्ट (Studio Photography)</span>
                            </span>
                            <button type="button" onclick="copyAiImagePrompt()" class="px-2.5 py-1 rounded-lg bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-[10px] transition flex items-center gap-1 shadow-2xs cursor-pointer">
                                <i class="fa-regular fa-copy"></i>
                                <span id="btnCopyPromptText">प्रॉम्प्ट कॉपी करें</span>
                            </button>
                        </div>

                        <p class="text-[11px] text-slate-300">
                            अगर आपके पास अच्छी फोटो नहीं है, तो नीचे दिया गया <strong>AI Studio Prompt</strong> कॉपी करें और किसी भी इमेज AI (Midjourney, Flux, Google Imagen या ChatGPT) में अपने साधारण मोबाइल फोटो के साथ डालें — यह उसे तुरंत 3D शोरूम स्टूडियो फोटो में बदल देगा!
                        </p>

                        <div class="p-3 bg-white/5 rounded-xl border border-white/10 font-mono text-[11px] text-indigo-200 leading-relaxed max-h-40 overflow-y-auto" id="aiResImagePrompt">
Ultra-realistic commercial product photograph of a modern white PTMT Bib Cock Tap with chrome accent, installed on an elegant dark grey textured tile wall in a luxury contemporary Indian bathroom. Crisp water stream flowing smoothly into a modern wash basin, tiny natural water droplets on the matte surface. Professional studio softbox lighting, soft rim light, clean 45-degree angle advertising shot, 8k resolution, Hasselblad 100mm lens, photorealistic product catalogue quality --ar 1:1 --v 6.0
                        </div>

                        <div class="p-2.5 bg-amber-400/10 border border-amber-400/20 rounded-xl text-[10px] text-amber-300 flex items-center gap-2">
                            <i class="fa-solid fa-lightbulb text-amber-400 text-xs shrink-0"></i>
                            <span><strong>टिप:</strong> अमेज़न व मीशो पर सफेद बैकग्राउंड (Pure White Background) और बाथरूम में लगी हुई (Lifestyle) फोटो लगाने पर 3x ज़्यादा बिक्री होती है।</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Analyze Market With AI via AJAX
            function analyzeMarketWithAi() {
                const keyword = document.getElementById('aiMarketKeywordInput')?.value.trim();
                if (!keyword) {
                    alert('कृपया किसी प्रोडक्ट का नाम लिखें (उदा. PTMT Nal)');
                    return;
                }

                const btn = document.getElementById('btnAnalyzeMarketAi');
                const origHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-amber-300"></i> विश्लेषण हो रहा है...`;

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                fetch("{{ route('seller.channels.aiMarketIntelligence') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ keyword: keyword })
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success && res.data) {
                        const d = res.data;
                        const mi = d.market_intelligence || {};

                        document.getElementById('aiResSearchVolume').innerText = mi.search_volume || '50,000+ मंथली सर्च';
                        document.getElementById('aiResSweetSpot').innerText = mi.sweet_spot_price || `₹${d.suggested_price} - ₹${d.suggested_mrp}`;
                        document.getElementById('aiResHsnCode').innerText = `HSN: ${d.hsn_code} (${d.category_label || 'Standard'})`;
                        document.getElementById('aiResDemandLevel').innerText = mi.demand_level || 'High Demand';

                        document.getElementById('aiResTitle').innerText = d.enhanced_title;
                        document.getElementById('aiResDesc').innerText = d.description;
                        document.getElementById('aiResImagePrompt').innerText = d.image_prompt;

                        document.getElementById('aiMarketResultsBox').scrollIntoView({ behavior: 'smooth' });
                    }
                })
                .catch(err => {
                    alert('एनालिटिक्स लोड करने में त्रुटि हुई। कृपया दोबारा प्रयास करें।');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                });
            }

            // Copy AI Listing Copy
            function copyAiListingCopy() {
                const title = document.getElementById('aiResTitle')?.innerText || '';
                const desc = document.getElementById('aiResDesc')?.innerText || '';
                const fullText = `Title:\n${title}\n\nDescription & Highlights:\n${desc}`;
                navigator.clipboard.writeText(fullText).then(() => {
                    const btnSpan = document.getElementById('btnCopyListingText');
                    btnSpan.innerText = '✅ कॉपीड!';
                    setTimeout(() => { btnSpan.innerText = 'कॉपी करें'; }, 2000);
                });
            }

            // Copy AI Studio Image Prompt
            function copyAiImagePrompt() {
                const prompt = document.getElementById('aiResImagePrompt')?.innerText || '';
                navigator.clipboard.writeText(prompt).then(() => {
                    const btnSpan = document.getElementById('btnCopyPromptText');
                    btnSpan.innerText = '✅ प्रॉम्प्ट कॉपीड!';
                    setTimeout(() => { btnSpan.innerText = 'प्रॉम्प्ट कॉपी करें'; }, 2000);
                });
            }
        </script>


        <!-- ======================================================== -->
        <!-- 💡 MARKETPLACE SALES & INQUIRY INTELLIGENCE CARD        -->
        <!-- ======================================================== -->
        <div class="bg-gradient-to-br from-indigo-950 via-slate-900 to-indigo-950 text-white rounded-3xl p-6 sm:p-7 border border-indigo-900/60 shadow-lg space-y-5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-indigo-900/80">
                <div class="flex items-center gap-3">
                    <div class="h-11 w-11 rounded-2xl bg-amber-400 text-slate-950 flex items-center justify-center text-lg font-black shrink-0">
                        <i class="fa-solid fa-brain"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-white">मार्केटप्लेस सेल्स & इन्क्वायरी इंटेलिजेंस (Data Analytics)</h3>
                        <p class="text-xs text-indigo-200">पता लगाएं कि किस प्लेटफ़ॉर्म पर कौन-सा प्रोडक्ट सबसे ज़्यादा बिकता है या इन्क्वायरी आती है</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/10 self-start md:self-auto">
                    Live Analytics Engine
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 text-xs">
                <!-- 1. Marketplace Volume vs Direct Store -->
                <div class="bg-white/5 rounded-2xl p-4 border border-white/10 space-y-2">
                    <span class="font-extrabold text-amber-300 uppercase tracking-wider block text-[10px]">आर्डर वॉल्यूम विश्लेषण</span>
                    <p class="text-slate-300 leading-relaxed text-[11px]">
                        <strong>Amazon & Meesho:</strong> छोटे व स्टैंडर्ड आइटम्स (जैसे ब्रास फिटिंग्स, सोल्वेंट, टूल्स) सीधे ऑनलाइन कार्ड पेमेंट या COD पर ज़्यादा बिकते हैं।
                    </p>
                    <div class="pt-2 border-t border-white/10 text-emerald-400 font-bold text-[11px]">
                        मार्केटप्लेस पर 15% कमीशन रहता है, जबकि VyaparIndia डायरेक्ट पर 0% कमीशन।
                    </div>
                </div>

                <!-- 2. Customer Inquiries vs Instant Orders -->
                <div class="bg-white/5 rounded-2xl p-4 border border-white/10 space-y-2">
                    <span class="font-extrabold text-blue-300 uppercase tracking-wider block text-[10px]">डायरेक्ट इन्क्वायरी लीड्स</span>
                    <p class="text-slate-300 leading-relaxed text-[11px]">
                        <strong>VyaparIndia डायरेक्ट स्टोर:</strong> भारी सामान (टाइल्स, पाइप्स, सीमेंट, पेंट्स) पर ग्राहक सीधे कार्ट ऑर्डर करने के बजाय <strong>WhatsApp या कॉल इन्क्वायरी</strong> ज़्यादा करते हैं।
                    </p>
                    <div class="pt-2 border-t border-white/10 text-amber-300 font-bold text-[11px]">
                        डायरेक्ट स्टोर में आपके पास खरीदार का मोबाइल नंबर रहता है।
                    </div>
                </div>

                <!-- 3. Smart Restock & Margin Tuning -->
                <div class="bg-white/5 rounded-2xl p-4 border border-white/10 space-y-2">
                    <span class="font-extrabold text-pink-300 uppercase tracking-wider block text-[10px]">मार्केटप्लेस रेट स्ट्रैटेजी</span>
                    <p class="text-slate-300 leading-relaxed text-[11px]">
                        मार्केटप्लेस पर रेट्स तय करते समय <strong>पैकिंग खर्च + 15% Amazon/Meesho कमीशन</strong> जोड़कर Sale A रेट ऑटो-सेट करें ताकि आपका शुद्ध मुनाफा सुरक्षित रहे।
                    </p>
                    <div class="pt-2 border-t border-white/10 text-indigo-300 font-bold text-[11px]">
                        इन्वेंटरी में 1-क्लिक मार्जिन से इसे तुरंत बदला जा सकता है।
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Connect Channel Modal -->
    <div id="connect-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" onclick="closeConnectModal()"></div>

            <div class="inline-block bg-white rounded-3xl p-6 sm:p-8 text-left overflow-hidden shadow-2xl transform transition-all sm:max-w-md w-full relative z-10 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="font-black text-base text-gray-900" id="modal-title">Connect Channel API</h3>
                        <p class="text-[11px] text-gray-500">24x7 इन्वेंटरी व आर्डर ऑटो-सिंक के लिए क्रेडेंशियल्स दर्ज करें</p>
                    </div>
                    <button onclick="closeConnectModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form action="{{ route('seller.channels.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <input type="hidden" name="channel_name" id="channel-name-input">

                    <!-- MEESHO SPECIFIC VIEW (Mobile No + Supplier ID) -->
                    <div id="meesho-view-box" class="hidden space-y-3">
                        <div class="p-3.5 rounded-2xl bg-pink-50 border border-pink-200 text-pink-950 text-[11px] space-y-1">
                            <div class="font-extrabold flex items-center gap-1.5 text-pink-900">
                                <i class="fa-solid fa-cart-shopping text-pink-600"></i>
                                <span>Meesho सप्लायर ऑटो-लिंक (सबसे आसान)</span>
                            </div>
                            <p class="text-pink-800">
                                Meesho में कोई AWS/डेवलपर झंझट नहीं होता। सिर्फ अपना <strong>रजिस्टर्ड मोबाइल नंबर / ईमेल</strong> और दुकान का नाम डालकर सेव करें।
                            </p>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Meesho स्टोर / फ़र्म का नाम <span class="text-rose-500">*</span></label>
                            <input type="text" name="meesho_store_name" id="meesho-store-name" placeholder="उदा. Ganesh Traders Meesho Shop" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-semibold">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Meesho रजिस्टर्ड मोबाइल नंबर या ईमेल <span class="text-rose-500">*</span></label>
                            <input type="text" name="meesho_mobile" id="meesho-mobile-input" placeholder="उदा. 98XXXXXXXX या vyaparindiaonline8@gmail.com" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-bold text-pink-900">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Meesho Supplier ID / Password (वैकल्पिक)</label>
                            <input type="text" name="meesho_supplier_id" id="meesho-supplier-input" placeholder="अगर मालूम हो (नहीं तो खाली छोड़ें)" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-mono">
                        </div>
                    </div>

                    <!-- AMAZON / DEFAULT SPECIFIC VIEW -->
                    <div id="amazon-view-box" class="space-y-3">
                        <div class="p-3 rounded-2xl bg-indigo-50/80 border border-indigo-200 text-indigo-950 text-[11px] flex items-start gap-2.5">
                            <i class="fa-solid fa-circle-check text-indigo-600 text-sm shrink-0 mt-0.5"></i>
                            <span class="leading-relaxed">
                                <strong>सब कुछ एक साथ भरना ज़रूरी नहीं है!</strong> आप अभी सिर्फ अपना स्टोर नाम और मर्चेंट टोकन डालकर सेव कर सकते हैं। बाकी Keys बाद में भी कभी भी भरें, डेटा हमेशा सुरक्षित रहेगा।
                            </span>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">दुकान / स्टोर का नाम <span class="text-rose-500">*</span></label>
                            <input type="text" name="store_name" id="store-name-input" required placeholder="उदा. Ganesh tradersh sarangarh" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-semibold">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Merchant Token (सेलर आईडी)</label>
                            <input type="text" name="store_url" id="store-url-input" placeholder="उदा. A3OQB2A3359C" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-mono font-bold text-indigo-700">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">LWA Client ID (App ID)</label>
                            <input type="text" name="api_key" id="api-key-input" placeholder="amzn1.application-oa2-client.xxxx..." class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-mono text-[11px]">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <div>
                                <label class="block font-bold text-gray-700 mb-1">LWA Client Secret</label>
                                <input type="text" name="api_secret" id="api-secret-input" placeholder="Secret Key" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-mono text-[11px]">
                            </div>
                            <div>
                                <label class="block font-bold text-emerald-800 mb-1 flex items-center gap-1">
                                    <i class="fa-solid fa-key text-emerald-600"></i>
                                    <span>Refresh Token</span>
                                </label>
                                <input type="text" name="access_token" id="access-token-input" placeholder="Atzr|... (Create Token से मिला)" class="w-full px-3.5 py-2.5 bg-emerald-50/60 border border-emerald-300 rounded-xl focus:bg-white focus:outline-none font-mono text-[11px] font-semibold text-emerald-950">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-black text-xs shadow-md transition active:scale-95 cursor-pointer">
                        🔒 सुरक्षित सेव करें & ऑटो-सिंक सक्रिय करें
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Switcher between Pending E-Commerce Queue vs Live Published
        function switchMarketplaceTab(tab) {
            const penView = document.getElementById('viewPendingMarketplaces');
            const pubView = document.getElementById('viewPublishedMarketplaces');
            const btnPen = document.getElementById('btnTabPendingMarketplaces');
            const btnPub = document.getElementById('btnTabPublishedMarketplaces');

            if (tab === 'published') {
                if (pubView) pubView.classList.remove('hidden');
                if (penView) penView.classList.add('hidden');
                if (btnPub) btnPub.className = "px-4 py-2 rounded-xl text-xs font-black transition flex items-center gap-2 bg-white text-emerald-900 shadow-2xs cursor-pointer";
                if (btnPen) btnPen.className = "px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 text-gray-600 hover:text-gray-900 cursor-pointer";
            } else {
                if (penView) penView.classList.remove('hidden');
                if (pubView) pubView.classList.add('hidden');
                if (btnPen) btnPen.className = "px-4 py-2 rounded-xl text-xs font-black transition flex items-center gap-2 bg-white text-amber-900 shadow-2xs cursor-pointer";
                if (btnPub) btnPub.className = "px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 text-gray-600 hover:text-gray-900 cursor-pointer";
            }
        }

        // Filter Selected E-Commerce Products by Category
        function filterPendingCategory(catId, btn) {
            document.querySelectorAll('.pending-cat-pill').forEach(b => {
                b.className = "pending-cat-pill px-3 py-1 rounded-xl text-xs font-bold transition bg-white hover:bg-amber-100 text-amber-950 border border-amber-300 shadow-2xs cursor-pointer";
            });
            if (btn) {
                btn.className = "pending-cat-pill px-3 py-1 rounded-xl text-xs font-black transition bg-amber-600 text-white shadow-xs cursor-pointer";
            }

            // Filter Table Rows
            const rows = document.querySelectorAll('#pendingTableTbody tr[data-id]');
            rows.forEach(tr => {
                const cId = tr.getAttribute('data-cat-id');
                if (catId === 'all' || cId === catId) {
                    tr.style.display = '';
                } else {
                    tr.style.display = 'none';
                }
            });

            // Filter Photo Cards
            const cards = document.querySelectorAll('.pending-card-item');
            cards.forEach(card => {
                const cId = card.getAttribute('data-cat-id');
                if (catId === 'all' || cId === catId) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Filter Available Products by Category
        function filterAvailableCategory(catId, btn) {
            document.querySelectorAll('.avail-cat-btn').forEach(b => {
                b.className = "avail-cat-btn px-3 py-1.5 rounded-xl text-xs font-bold transition bg-white hover:bg-blue-100 text-blue-900 border border-blue-300 shadow-2xs cursor-pointer";
            });
            if (btn) {
                btn.className = "avail-cat-btn px-3 py-1.5 rounded-xl text-xs font-black transition bg-blue-600 text-white shadow-2xs cursor-pointer";
            }

            const cards = document.querySelectorAll('.available-prod-card');
            cards.forEach(card => {
                const cardCat = card.getAttribute('data-cat-id');
                if (catId === 'all' || cardCat === catId) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Real-Time Search in Available Products
        function filterAvailableProducts() {
            const q = (document.getElementById('availableProdSearchInput')?.value || '').trim().toLowerCase();
            const cards = document.querySelectorAll('.available-prod-card');
            cards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const sku = card.getAttribute('data-sku') || '';
                if (!q || name.includes(q) || sku.includes(q)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // 1-Click Toggle Stage / Unstage Product directly from Available Catalog
        async function toggleProductStage(prodId) {
            const btn = document.getElementById(`btn-toggle-stage-${prodId}`);
            if (!btn) return;

            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> <span>...</span>';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                const res = await fetch(`/seller/channels/products/${prodId}/toggle-stage`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                const data = await res.json();
                if (data.success) {
                    if (data.is_staged) {
                        btn.className = "py-1.5 px-3 rounded-xl text-xs font-black transition flex items-center gap-1.5 shadow-2xs cursor-pointer active:scale-95 bg-amber-100 text-amber-900 hover:bg-rose-100 hover:text-rose-700 border border-amber-300";
                        btn.innerHTML = '<i class="fa-solid fa-check text-emerald-600"></i> <span>कतार में मौजूद</span>';
                    } else {
                        btn.className = "py-1.5 px-3 rounded-xl text-xs font-black transition flex items-center gap-1.5 shadow-2xs cursor-pointer active:scale-95 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white";
                        btn.innerHTML = '<i class="fa-solid fa-plus-circle text-blue-200"></i> <span>+ कतार में जोड़ें</span>';
                    }
                    // Update pending count badges
                    document.querySelectorAll('.pending-count-badge').forEach(b => {
                        b.textContent = data.pending_count;
                    });
                    showChannelToast(data.message);
                } else {
                    alert(data.message || 'त्रुटि हुई');
                    btn.innerHTML = origHtml;
                }
            } catch (err) {
                console.error(err);
                btn.innerHTML = origHtml;
            } finally {
                btn.disabled = false;
            }
        }

        // Filter Published Listings by Marketplace Pill (Amazon, Meesho, Blinkit, etc.)
        function filterMarketplaceListings(mp) {
            document.querySelectorAll('.mp-pill-btn').forEach(btn => {
                const isSelected = btn.getAttribute('data-mp') === mp;
                if (isSelected) {
                    btn.classList.add('bg-indigo-600', 'text-white', 'shadow-2xs');
                    btn.classList.remove('bg-amber-50', 'bg-pink-50', 'bg-blue-50', 'bg-rose-50', 'bg-yellow-50');
                } else {
                    btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-2xs');
                }
            });

            document.querySelectorAll('.listing-item-card').forEach(card => {
                const cardChannel = card.getAttribute('data-channel');
                if (mp === 'all' || cardChannel === mp) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Auto-select all pending products & check zero stock on bulk publish
        function handleBulkPublishSubmit(e) {
            let checkedBoxes = document.querySelectorAll('.pending-prod-chk:checked');
            if (checkedBoxes.length === 0) {
                const allBoxes = document.querySelectorAll('.pending-prod-chk');
                if (allBoxes.length > 0) {
                    toggleSelectAllPending(true);
                    const selectAll = document.getElementById('selectAllPendingChk');
                    if (selectAll) selectAll.checked = true;
                    checkedBoxes = document.querySelectorAll('.pending-prod-chk:checked');
                } else {
                    if (e) e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: '⚠️ पेंडिंग कतार खाली है',
                            text: 'आपकी कतार में अभी कोई प्रोडक्ट नहीं है। कृपया पहले "+ दुकान से और जोड़ें" बटन दबाकर प्रोडक्ट जोड़ें।',
                            confirmButtonColor: '#4f46e5',
                            confirmButtonText: 'समझ गया'
                        });
                    } else {
                        alert('आपकी कतार में अभी कोई प्रोडक्ट नहीं है। कृपया पहले "+ दुकान से और जोड़ें" बटन दबाकर प्रोडक्ट जोड़ें।');
                    }
                    return false;
                }
            }

            // ⚠️ Check if any selected product has ZERO STOCK!
            let zeroStockCount = 0;
            checkedBoxes.forEach(chk => {
                const prodId = chk.value;
                const stInp = document.getElementById(`row-stock-${prodId}`);
                if (stInp && parseInt(stInp.value) <= 0) {
                    zeroStockCount++;
                }
            });

            if (zeroStockCount > 0 && !window._bypassZeroStockConfirm) {
                if (e) e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: `⚠️ ${zeroStockCount} प्रोडक्ट्स में 0 स्टॉक (इन्वेंट्री) है!`,
                        html: `<div class="text-left text-xs text-gray-700 space-y-2">
                            <p>अगर आप 0 स्टॉक के साथ पब्लिश करेंगे तो Amazon व Meesho पर ये <strong>"Currently Unavailable / Out of Stock"</strong> दिखेंगे और ग्राहक ऑर्डर नहीं कर पाएंगे।</p>
                            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-950">
                                💡 <strong>सुझाव:</strong> आप ऊपर टेबल में <strong>"📦 स्टॉक Qty"</strong> में 10 या 20 डालकर <em>'Apply & Save'</em> कर सकते हैं, जिससे तुरंत स्टॉक तैयार हो जाएगा!
                            </div>
                        </div>`,
                        showCancelButton: true,
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#f43f5e',
                        confirmButtonText: 'हाँ, फिर भी पब्लिश करें',
                        cancelButtonText: 'रुकें, पहले स्टॉक भरेंगे'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window._bypassZeroStockConfirm = true;
                            document.getElementById('bulkPublishForm').submit();
                        }
                    });
                } else {
                    if (confirm(`चेतावनी: ${zeroStockCount} प्रोडक्ट्स में 0 स्टॉक है। क्या आप फिर भी पब्लिश करना चाहते हैं?`)) {
                        window._bypassZeroStockConfirm = true;
                        document.getElementById('bulkPublishForm').submit();
                    }
                }
                return false;
            }

            return true;
        }

        // Select All Pending Checkbox
        // Test Channel Connection and Permissions in Real Time
        function testChannelConnection(channelName = 'amazon') {
            const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}';
            
            Swal.fire({
                title: '⚡ लिंक व परमिशन जांची जा रही है...',
                html: '<div class="p-3 text-xs text-gray-600"><i class="fa-solid fa-spinner fa-spin text-2xl text-indigo-600 mb-2 block"></i>Amazon SP-API सर्वर से संपर्क हो रहा है...</div>',
                allowOutsideClick: false,
                showConfirmButton: false
            });

            fetch("{{ route('seller.channels.testConnection') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ channel: channelName })
            })
            .then(res => res.json())
            .then(data => {
                let iconType = data.connected ? 'success' : (data.status === 'roles_missing' ? 'warning' : 'error');
                let checklistHtml = '';
                if (data.checklist) {
                    checklistHtml = `
                        <div class="mt-3 p-3 bg-gray-50 rounded-2xl border border-gray-200 text-left text-xs space-y-1.5 font-sans">
                            <div class="font-bold text-gray-700 text-[11px] uppercase mb-1">ज़रूरी चीज़ों की स्थिति (Checklist):</div>
                            <div class="flex items-center justify-between">
                                <span>1. LWA Client ID & Secret:</span>
                                <span>${data.checklist.client_id && data.checklist.client_secret ? '✅ सही है' : '❌ गायब है'}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>2. Refresh Token:</span>
                                <span>${data.checklist.refresh_token ? '✅ सही है' : '❌ गायब है'}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>3. Merchant Token (सेलर आईडी):</span>
                                <span>${data.checklist.merchant_token ? '✅ सही है' : '❌ दर्ज नहीं है'}</span>
                            </div>
                            ${data.checklist.spapi_roles !== undefined ? `
                            <div class="flex items-center justify-between">
                                <span>4. SP-API Roles (Listing Rights):</span>
                                <span>${data.checklist.spapi_roles ? '✅ एक्टिवेट है' : '⚠️ पेंडिंग / ब्लॉक'}</span>
                            </div>` : ''}
                        </div>
                    `;
                }

                let htmlContent = `
                    <div class="text-xs text-gray-700 space-y-2.5 text-left font-sans">
                        <p class="font-semibold text-gray-900">${data.message || ''}</p>
                        ${checklistHtml}
                        ${data.guide ? `
                        <div class="p-2.5 bg-amber-50 rounded-xl border border-amber-200 text-amber-950 text-[11px]">
                            <strong>💡 समाधान / उपाय:</strong> ${data.guide}
                        </div>` : ''}
                    </div>
                `;

                Swal.fire({
                    icon: iconType,
                    title: data.title || 'कनेक्शन स्थिति',
                    html: htmlContent,
                    confirmButtonColor: data.connected ? '#059669' : '#4f46e5',
                    confirmButtonText: 'समझ गया'
                });
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: '⚠️ नेटवर्क एरर',
                    text: 'सर्वर से संपर्क करने में असमर्थ। कृपया पुनः प्रयास करें।',
                    confirmButtonColor: '#dc2626'
                });
            });
        }

        function toggleSelectAllPending(checked) {
            document.querySelectorAll('.pending-prod-chk').forEach(chk => {
                chk.checked = checked;
            });
        }

        // Open API Connect Modal (Smart View for Meesho vs Amazon)
        function openConnectModal(channelKey, channelTitle, storeName = '', storeUrl = '', apiKey = '', apiSecret = '', accessToken = '') {
            document.getElementById('channel-name-input').value = channelKey;
            document.getElementById('modal-title').innerText = channelTitle + (channelKey === 'meesho' ? ' डायरेक्ट लिंक' : ' API Connect');

            const meeshoBox = document.getElementById('meesho-view-box');
            const amazonBox = document.getElementById('amazon-view-box');

            if (channelKey === 'meesho') {
                if (meeshoBox) meeshoBox.classList.remove('hidden');
                if (amazonBox) amazonBox.classList.add('hidden');
                document.getElementById('meesho-store-name').value = storeName || 'My Meesho Shop';
                document.getElementById('meesho-mobile-input').value = storeUrl || '';
                document.getElementById('store-name-input').required = false;
            } else {
                if (meeshoBox) meeshoBox.classList.add('hidden');
                if (amazonBox) amazonBox.classList.remove('hidden');
                document.getElementById('store-name-input').required = true;
                document.getElementById('store-name-input').value = storeName || (channelTitle + ' Store');
                document.getElementById('store-url-input').value = storeUrl || '';
                document.getElementById('api-key-input').value = apiKey || '';
                if (document.getElementById('api-secret-input')) {
                    document.getElementById('api-secret-input').value = apiSecret || '';
                }
                if (document.getElementById('access-token-input')) {
                    document.getElementById('access-token-input').value = accessToken || '';
                }
            }

            document.getElementById('connect-modal').classList.remove('hidden');
        }

        function closeConnectModal() {
            document.getElementById('connect-modal').classList.add('hidden');
        }

        // ⏳ LIVE DISPATCH COUNTDOWN TIMER ("कब लेने आएगा")
        function updatePickupTimers() {
            const now = new Date().getTime();
            document.querySelectorAll('.pickup-timer-clock').forEach(clock => {
                const deadlineIso = clock.getAttribute('data-deadline');
                if (!deadlineIso) return;
                const deadline = new Date(deadlineIso).getTime();
                const diff = deadline - now;

                if (diff <= 0) {
                    clock.innerHTML = "<span class='text-rose-400 font-bold animate-pulse'>पिकअप समय हो गया!</span>";
                    return;
                }

                const hours = Math.floor(diff / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                const hh = String(hours).padStart(2, '0');
                const mm = String(minutes).padStart(2, '0');
                const ss = String(seconds).padStart(2, '0');

                if (hours < 2) {
                    clock.innerHTML = `<span class='text-amber-400'>⏳ ${hh}h : ${mm}m : ${ss}s</span>`;
                } else {
                    clock.innerHTML = `<span>⏳ ${hh}h : ${mm}m : ${ss}s</span>`;
                }
            });
        }

        setInterval(updatePickupTimers, 1000);
        document.addEventListener('DOMContentLoaded', updatePickupTimers);
    </script>

    <!-- ======================================================== -->
    <!-- 🏢 FULL SHOP AI MARKET AUDIT & LISTING REVIEW MODAL      -->
    <!-- ======================================================== -->
    <div id="shop-scan-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="min-h-screen px-4 py-8 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-xs transition-opacity" onclick="closeShopScanModal()"></div>

            <div class="inline-block bg-white rounded-3xl p-6 sm:p-7 text-left overflow-hidden shadow-2xl transform transition-all max-w-5xl w-full relative z-10 space-y-5 max-h-[90vh] flex flex-col">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-lg font-black">
                            <i class="fa-solid fa-brain"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-base text-gray-900 flex items-center gap-2">
                                <span>AI दुकान स्कैन & कॉम्पिटिटर मार्केट रिपोर्ट</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-indigo-100 text-indigo-800 font-extrabold" id="shopScanCountBadge">0 Products</span>
                            </h3>
                            <p class="text-[11px] text-gray-500">
                                AI ने मार्केट रिसर्च करके टाइटल्स, बुलेट पॉइंट्स व कॉम्बो सुझाव तैयार किए हैं। आपके <strong>रेट्स और फोटो 100% सुरक्षित</strong> रहेंगे।
                            </p>
                        </div>
                    </div>
                    <button onclick="closeShopScanModal()" class="text-gray-400 hover:text-gray-600 text-xl p-1">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Live Loading State -->
                <div id="shopScanLoadingBox" class="py-12 text-center space-y-3">
                    <div class="inline-block animate-spin text-indigo-600 text-3xl">
                        <i class="fa-solid fa-circle-notch"></i>
                    </div>
                    <h4 class="font-black text-sm text-gray-900">आपकी दुकान के प्रोडक्ट्स का अमेज़न & मीशो पर विश्लेषण हो रहा है...</h4>
                    <p class="text-xs text-gray-500 max-w-md mx-auto">
                        कॉम्पिटिटर्स के बेस्ट-सेलिंग टाइटल्स, 4+ स्टार रेटिंग्स, खरीदारों के सर्च कीवर्ड्स और राज्यों की मांग निकाली जा रही है।
                    </p>
                </div>

                <!-- Scanned Products Results Table / Cards -->
                <div id="shopScanResultsBox" class="hidden overflow-y-auto space-y-4 flex-1 pr-1">
                    <!-- Dynamic Rows Populated via JS -->
                </div>

                <!-- Modal Footer / Action Bar -->
                <div id="shopScanFooterBar" class="hidden pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0 bg-gray-50/80 -mx-6 -mb-6 p-4 px-6 rounded-b-3xl">
                    <div class="text-xs text-gray-600">
                        <span class="font-bold text-gray-900" id="shopScanReadyCount">0</span> प्रोडक्ट्स तैयार हैं। रेट्स और फोटो में कोई बदलाव नहीं होगा।
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeShopScanModal()" class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold text-xs transition">
                            रद्द करें (Cancel)
                        </button>
                        <button type="button" onclick="applyAllAiOptimizedListings()" id="btnApplyAllOptimized" class="px-6 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs shadow-md transition active:scale-95 cursor-pointer flex items-center gap-2">
                            <i class="fa-solid fa-bolt-lightning text-amber-300"></i>
                            <span>🚀 1-क्लिक में सभी लिस्टिंग्स पर लागू करें (No Copy-Paste)</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentScannedOptimizedItems = [];

        // Start Full Shop Scan
        function startFullShopAiScan(channel = 'amazon') {
            document.getElementById('shop-scan-modal').classList.remove('hidden');
            document.getElementById('shopScanLoadingBox').classList.remove('hidden');
            document.getElementById('shopScanResultsBox').classList.add('hidden');
            document.getElementById('shopScanFooterBar').classList.add('hidden');

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch("{{ route('seller.channels.aiScanShop') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ channel: channel })
            })
            .then(r => r.json())
            .then(res => {
                if (res.success && Array.isArray(res.products)) {
                    currentScannedOptimizedItems = res.products;
                    renderScannedProducts(res.products);
                } else {
                    alert(res.message || 'स्कैन करने में समस्या हुई।');
                    closeShopScanModal();
                }
            })
            .catch(err => {
                alert('नेटवर्क एरर। कृपया दोबारा प्रयास करें।');
                closeShopScanModal();
            });
        }

        function closeShopScanModal() {
            document.getElementById('shop-scan-modal').classList.add('hidden');
        }

        // Render Scanned Results in Modal
        function renderScannedProducts(products) {
            document.getElementById('shopScanLoadingBox').classList.add('hidden');
            const container = document.getElementById('shopScanResultsBox');
            container.innerHTML = '';
            container.classList.remove('hidden');
            document.getElementById('shopScanFooterBar').classList.remove('hidden');

            document.getElementById('shopScanCountBadge').innerText = `${products.length} Products Scanned`;
            document.getElementById('shopScanReadyCount').innerText = `${products.length}`;

            products.forEach((p, idx) => {
                const mi = p.market_intel || {};
                const comp = mi.competitor_benchmark || {};

                const card = document.createElement('div');
                card.className = "bg-white rounded-2xl border border-gray-200 p-4 shadow-2xs space-y-3 hover:border-indigo-300 transition text-xs";
                card.innerHTML = `
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-3 pb-3 border-b border-gray-100">
                        <div class="flex items-start gap-3">
                            <div class="h-14 w-14 rounded-xl bg-gray-50 border border-gray-200 overflow-hidden flex items-center justify-center shrink-0 p-1">
                                ${p.current_image ? `<img src="${p.current_image}" class="h-full w-full object-contain">` : `<i class="fa-solid fa-cube text-gray-300 text-xl"></i>`}
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 block font-mono">मूल नाम: ${p.original_name}</span>
                                <h4 class="font-extrabold text-sm text-indigo-950 leading-snug mt-0.5">${p.optimized_title}</h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 text-[10px] font-bold">रेट (सुरक्षित): ₹${Number(p.current_price).toFixed(2)}</span>
                                    <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 text-[10px] font-mono font-bold">HSN: ${p.hsn_code}</span>
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0 text-right space-y-1">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-50 text-amber-900 border border-amber-200 inline-block">
                                ${comp.top_rating || '4.3 ⭐'}
                            </span>
                            <div class="text-[10px] text-gray-500">${comp.top_selling_format || 'Bestseller Pack'}</div>
                        </div>
                    </div>

                    <!-- Market Insights Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-[11px]">
                        <div class="bg-indigo-50/60 p-2.5 rounded-xl border border-indigo-100">
                            <span class="text-[9px] font-bold uppercase text-indigo-700 block">सर्च डिमांड & भाव</span>
                            <div class="font-black text-indigo-900 mt-0.5">${mi.monthly_search_volume || '50,000+'}</div>
                            <div class="text-[10px] text-indigo-600">औसत: ${mi.avg_market_price || ''}</div>
                        </div>

                        <div class="bg-emerald-50/60 p-2.5 rounded-xl border border-emerald-100">
                            <span class="text-[9px] font-bold uppercase text-emerald-700 block">टॉप डिमांड वाले राज्य</span>
                            <div class="font-bold text-emerald-950 mt-0.5 leading-tight">${mi.top_demand_regions || 'ऑल इंडिया'}</div>
                        </div>

                        <div class="bg-amber-50/60 p-2.5 rounded-xl border border-amber-100">
                            <span class="text-[9px] font-bold uppercase text-amber-800 block">रिटर्न दर विश्लेषण</span>
                            <div class="font-bold text-amber-950 mt-0.5">${mi.return_risk_rate || 'कम रिटर्न'}</div>
                            <div class="text-[10px] text-amber-700 leading-tight">${mi.return_tip || ''}</div>
                        </div>
                    </div>

                    <!-- Combo Opportunity Note -->
                    ${mi.combo_opportunity_note ? `
                        <div class="p-2.5 bg-amber-400/10 border border-amber-300/40 rounded-xl text-[11px] text-amber-900 font-medium flex items-start gap-2">
                            <i class="fa-solid fa-lightbulb text-amber-600 text-xs mt-0.5 shrink-0"></i>
                            <div>${mi.combo_opportunity_note}</div>
                        </div>
                    ` : ''}

                    <!-- Competitor Reference URLs -->
                    <div class="flex items-center justify-between text-[11px] pt-1">
                        <span class="text-gray-400 font-bold">मार्केट कॉम्पिटिटर लाइव रिफ़्रेंस:</span>
                        <div class="flex items-center gap-2">
                            ${comp.amazon_url ? `<a href="${comp.amazon_url}" target="_blank" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-amber-100 text-amber-900 font-bold transition flex items-center gap-1"><i class="fa-brands fa-amazon text-[10px]"></i> Amazon रिफ़्रेंस &rarr;</a>` : ''}
                            ${comp.meesho_url ? `<a href="${comp.meesho_url}" target="_blank" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-pink-100 text-pink-900 font-bold transition flex items-center gap-1"><i class="fa-solid fa-cart-shopping text-[10px]"></i> Meesho रिफ़्रेंस &rarr;</a>` : ''}
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });
        }

        // 🚀 1-Click Apply All AI Researched Listings Directly to Store
        function applyAllAiOptimizedListings() {
            if (!currentScannedOptimizedItems || currentScannedOptimizedItems.length === 0) {
                alert('कोई डाटा उपलब्ध नहीं है।');
                return;
            }

            const btn = document.getElementById('btnApplyAllOptimized');
            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> लागू हो रहा है...`;

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch("{{ route('seller.channels.aiApplyOptimizedListings') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ optimized_items: currentScannedOptimizedItems })
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    alert(`✅ ${res.message}`);
                    closeShopScanModal();
                    location.reload();
                } else {
                    alert(res.message || 'लागू करने में त्रुटि हुई।');
                }
            })
            .catch(err => {
                alert('नेटवर्क एरर। कृपया दोबारा प्रयास करें।');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = origHtml;
            });
        }
            // Costing & Profit Live Calculator
        let activeCalcProductId = null;



        function closeCostCalculatorModal() {
            const modal = document.getElementById('costCalculatorModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // 1-Click Platform Presets for Pricing Calculator (Amazon vs Meesho)
        function setCalcPlatformPreset(platform) {
            const btnAmz = document.getElementById('calcPresetBtnAmz');
            const btnMsh = document.getElementById('calcPresetBtnMsh');
            if (platform === 'meesho') {
                document.getElementById('calcPlatformFeePercent').value = 0;
                document.getElementById('calcShippingCost').value = 38;
                document.getElementById('calcPackingCost').value = 12;
                if (btnMsh) {
                    btnMsh.className = 'px-2 py-0.5 rounded-lg bg-pink-600 text-white font-black text-[10px] shadow-2xs transition cursor-pointer';
                }
                if (btnAmz) {
                    btnAmz.className = 'px-2 py-0.5 rounded-lg bg-gray-100 hover:bg-amber-100 text-gray-700 font-bold text-[10px] border border-gray-300 transition cursor-pointer';
                }
            } else {
                document.getElementById('calcPlatformFeePercent').value = 12;
                document.getElementById('calcShippingCost').value = 45;
                document.getElementById('calcPackingCost').value = 15;
                if (btnAmz) {
                    btnAmz.className = 'px-2 py-0.5 rounded-lg bg-amber-500 text-slate-950 font-black text-[10px] shadow-2xs transition cursor-pointer';
                }
                if (btnMsh) {
                    btnMsh.className = 'px-2 py-0.5 rounded-lg bg-pink-100 hover:bg-pink-200 text-pink-900 font-black text-[10px] border border-pink-300 transition cursor-pointer';
                }
            }
            calculateLiveSellingPrice();
        }

        function calculateLiveSellingPrice() {
            const cost = parseFloat(document.getElementById('calcCostPrice').value) || 0;
            const marginPct = parseFloat(document.getElementById('calcMarginPercent').value) || 0;
            const packing = parseFloat(document.getElementById('calcPackingCost').value) || 0;
            const shipping = parseFloat(document.getElementById('calcShippingCost').value) || 0;
            const feePct = parseFloat(document.getElementById('calcPlatformFeePercent').value) || 0;
            const prodGstRate = parseFloat(document.getElementById('calcProductGst')?.value) || 18;

            const desiredProfit = cost * (marginPct / 100);
            const g = prodGstRate / 100;
            const f = feePct / 100;
            const cTax = g > 0 ? (cost / (1 + g)) : cost;
            const denom = Math.max(0.05, (1 / (1 + g)) - f);
            const fixedCostSum = packing + shipping;
            const finalRate = Math.ceil((cTax + fixedCostSum + desiredProfit) / denom);

            document.getElementById('calcFinalSellingRate').textContent = '₹' + finalRate.toFixed(2);
            document.getElementById('calcNetProfitInHand').textContent = '₹' + desiredProfit.toFixed(2);

            // 📊 Advance Multi-Slab GST & ITC Tax Engine Calculations
            // 1. Output GST on Product: calculated by Product tax slab (e.g. 5%, 12%, 18%)
            const outputGst = (finalRate * (prodGstRate / (100 + prodGstRate)));
            // 2. Courier Services (SAC 9968) are FIXED at 18% GST in India
            const courierGstItc = (shipping * 0.18);
            // 3. Platform Commission (SAC 9983) is FIXED at 18% GST in India
            const commGstItc = ((finalRate * f) * 0.18);
            // 4. Purchase Cost ITC depends on Product tax slab
            const purchaseGstItc = (cost * (prodGstRate / (100 + prodGstRate)));
            const totalItc = courierGstItc + commGstItc + purchaseGstItc;
            const netCashGst = Math.max(0, outputGst - totalItc);

            const elOutput = document.getElementById('calcGstOutput');
            const elCourier = document.getElementById('calcGstCourierItc');
            const elComm = document.getElementById('calcGstCommItc');
            const elCash = document.getElementById('calcGstNetCash');
            const elLabel = document.getElementById('calcGstPlatformLabel');
            const elProdTag = document.getElementById('calcGstProdLabel');

            if (elProdTag) elProdTag.textContent = `${prodGstRate}%`;
            if (elOutput) elOutput.textContent = '₹' + outputGst.toFixed(2);
            if (elCourier) elCourier.textContent = '-₹' + courierGstItc.toFixed(2);
            if (elComm) elComm.textContent = (f > 0) ? ('-₹' + commGstItc.toFixed(2)) : '₹0.00 (Meesho 0% कमीशन)';
            if (elCash) elCash.textContent = '₹' + netCashGst.toFixed(2);
            if (elLabel) elLabel.textContent = (f > 0) ? 'Amazon (12% फ़ी बेस)' : 'Meesho (कूरियर बेस - 0% फ़ी)';

            // Combo calculations (GST-Gap protected + packaging & courier efficiency)
            // 2 PC: Cost x2, Profit x1.8, 1x Packing x1.3, 1x Shipping
            const c2 = Math.ceil(((cTax * 2) + (desiredProfit * 1.8) + (packing * 1.3) + shipping) / denom);
            document.getElementById('calcCombo2Rate').textContent = '₹' + c2.toFixed(2);

            // 4 PC
            const c4 = Math.ceil(((cTax * 4) + (desiredProfit * 3.5) + (packing * 1.6) + (shipping * 1.2)) / denom);
            document.getElementById('calcCombo4Rate').textContent = '₹' + c4.toFixed(2);

            // 6 PC
            const c6 = Math.ceil(((cTax * 6) + (desiredProfit * 5.0) + (packing * 2.0) + (shipping * 1.4)) / denom);
            document.getElementById('calcCombo6Rate').textContent = '₹' + c6.toFixed(2);
        }

        // --- 📦 COMBO CALCULATOR MODAL LOGIC (2P, 4P, 6P) ---
        let activeComboProductId = null;

        function openComboCalculatorModal(prodId, prodName) {
            activeComboProductId = prodId;
            document.getElementById('comboModalProdName').textContent = prodName;

            const cost = parseFloat(document.getElementById(`row-cost-${prodId}`)?.value) || 0;
            const marginPct = parseFloat(document.getElementById(`row-margin-${prodId}`)?.value) || 25;
            const gstRate = parseFloat(document.getElementById(`row-gst-${prodId}`)?.value) || 18;
            const feePct = parseFloat(document.getElementById(`row-fee-${prodId}`)?.value) || 12;

            const currentC2 = parseFloat(document.getElementById(`row-combo2-${prodId}`)?.value) || 0;
            const currentC4 = parseFloat(document.getElementById(`row-combo4-${prodId}`)?.value) || 0;
            const currentC6 = parseFloat(document.getElementById(`row-combo6-${prodId}`)?.value) || 0;

            document.getElementById('comboModalBaseCost').textContent = '₹' + cost.toFixed(2);
            document.getElementById('comboModalGstRate').textContent = gstRate + '% GST';

            // 2P
            document.getElementById('combo2_cost').textContent = 'खरीद: ₹' + (cost * 2).toFixed(2);
            document.getElementById('combo2_margin').value = marginPct;
            document.getElementById('combo2_ship').value = 45;
            document.getElementById('combo2_pack').value = 20;
            document.getElementById('combo2_rto').value = 30;
            document.getElementById('combo2_fee').value = feePct;

            // 4P
            document.getElementById('combo4_cost').textContent = 'खरीद: ₹' + (cost * 4).toFixed(2);
            document.getElementById('combo4_margin').value = marginPct;
            document.getElementById('combo4_ship').value = 65;
            document.getElementById('combo4_pack').value = 25;
            document.getElementById('combo4_rto').value = 40;
            document.getElementById('combo4_fee').value = feePct;

            // 6P
            document.getElementById('combo6_cost').textContent = 'खरीद: ₹' + (cost * 6).toFixed(2);
            document.getElementById('combo6_margin').value = marginPct;
            document.getElementById('combo6_ship').value = 85;
            document.getElementById('combo6_pack').value = 30;
            document.getElementById('combo6_rto').value = 50;
            document.getElementById('combo6_fee').value = feePct;

            recalcComboModalLive(2, currentC2);
            recalcComboModalLive(4, currentC4);
            recalcComboModalLive(6, currentC6);

            const m = document.getElementById('comboCalculatorModal');
            if (m) {
                m.classList.remove('hidden');
                m.classList.add('flex');
            }
        }

        function closeComboCalculatorModal() {
            const m = document.getElementById('comboCalculatorModal');
            if (m) {
                m.classList.add('hidden');
                m.classList.remove('flex');
            }
        }

        function recalcComboModalLive(qty, overrideRate = 0) {
            if (!activeComboProductId) return;
            const unitCost = parseFloat(document.getElementById(`row-cost-${activeComboProductId}`)?.value) || 0;
            const gstRate = parseFloat(document.getElementById(`row-gst-${activeComboProductId}`)?.value) || 18;

            const ship = parseFloat(document.getElementById(`combo${qty}_ship`)?.value) || 0;
            const pack = parseFloat(document.getElementById(`combo${qty}_pack`)?.value) || 0;
            const rto = parseFloat(document.getElementById(`combo${qty}_rto`)?.value) || 0;
            const marginPct = parseFloat(document.getElementById(`combo${qty}_margin`)?.value) || 25;
            const feePct = parseFloat(document.getElementById(`combo${qty}_fee`)?.value) || 12;

            const totalCost = unitCost * qty;
            const g = gstRate / 100;
            const f = feePct / 100;
            const cTax = g > 0 ? (totalCost / (1 + g)) : totalCost;
            const denom = Math.max(0.05, (1 / (1 + g)) - f);

            const profitTarget = totalCost * (marginPct / 100);
            const fixedExp = ship + pack + rto;
            const calculatedSellingRate = Math.ceil((cTax + fixedExp + profitTarget) / denom);

            const finalPrice = (overrideRate > 0) ? overrideRate : calculatedSellingRate;
            const priceInp = document.getElementById(`combo${qty}_price`);
            if (priceInp) priceInp.value = finalPrice;

            recalcComboModalProfit(qty);
        }

        function recalcComboModalProfit(qty) {
            if (!activeComboProductId) return;
            const unitCost = parseFloat(document.getElementById(`row-cost-${activeComboProductId}`)?.value) || 0;
            const gstRate = parseFloat(document.getElementById(`row-gst-${activeComboProductId}`)?.value) || 18;

            const totalCost = unitCost * qty;
            const ship = parseFloat(document.getElementById(`combo${qty}_ship`)?.value) || 0;
            const pack = parseFloat(document.getElementById(`combo${qty}_pack`)?.value) || 0;
            const rto = parseFloat(document.getElementById(`combo${qty}_rto`)?.value) || 0;
            const feePct = parseFloat(document.getElementById(`combo${qty}_fee`)?.value) || 12;

            const finalPrice = parseFloat(document.getElementById(`combo${qty}_price`)?.value) || 0;

            const g = gstRate / 100;
            const f = feePct / 100;
            const sTax = g > 0 ? (finalPrice / (1 + g)) : finalPrice;
            const cTax = g > 0 ? (totalCost / (1 + g)) : totalCost;
            const fee = finalPrice * f;
            const fixedExp = ship + pack + rto;

            const netProfit = Math.round((sTax - cTax - fee - fixedExp) * 100) / 100;
            const profitSpan = document.getElementById(`combo${qty}_profit`);
            if (profitSpan) profitSpan.textContent = `₹${netProfit.toFixed(2)}`;

            const perPcSpan = document.getElementById(`combo${qty}_per_pc`);
            if (perPcSpan && finalPrice > 0) {
                perPcSpan.textContent = `₹${(finalPrice / qty).toFixed(1)}/pc`;
            }
        }

        async function saveComboModalValues(btn) {
            if (!activeComboProductId) return;
            const c2 = parseFloat(document.getElementById('combo2_price')?.value) || 0;
            const c4 = parseFloat(document.getElementById('combo4_price')?.value) || 0;
            const c6 = parseFloat(document.getElementById('combo6_price')?.value) || 0;

            const r2 = document.getElementById(`row-combo2-${activeComboProductId}`);
            if (r2) r2.value = c2;
            const r4 = document.getElementById(`row-combo4-${activeComboProductId}`);
            if (r4) r4.value = c4;
            const r6 = document.getElementById(`row-combo6-${activeComboProductId}`);
            if (r6) r6.value = c6;

            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> सेव हो रहा है...';

            const rowSaveBtn = document.querySelector(`#table-row-${activeComboProductId} button[onclick^="saveTableRowCosting"]`);
            await saveTableRowCosting(activeComboProductId, rowSaveBtn || btn);

            btn.disabled = false;
            btn.innerHTML = origHtml;
            closeComboCalculatorModal();
            showChannelToast(`✓ 2P (₹${c2}), 4P (₹${c4}), 6P (₹${c6}) कॉम्बो रेट्स व खर्चे सुरक्षित सेव हो गए!`);
        }

        // --- 📦 BULK COMBO RECALCULATION & SAVING ---
        function bulkRecalcCombos() {
            const rows = document.querySelectorAll('#pendingTableTbody tr[data-id]');
            if (!rows.length) {
                alert('कोई प्रोडक्ट मौजूद नहीं है!');
                return;
            }

            rows.forEach(row => {
                const id = row.getAttribute('data-id');
                const stock = parseInt(document.getElementById(`row-stock-${id}`)?.value) || 0;
                const cost = parseFloat(document.getElementById(`row-cost-${id}`)?.value) || 0;
                const marginPct = parseFloat(document.getElementById(`row-margin-${id}`)?.value) || 25;
                const gstRate = parseFloat(document.getElementById(`row-gst-${id}`)?.value) || 18;
                const feePct = parseFloat(document.getElementById(`row-fee-${id}`)?.value) || 12;

                const g = gstRate / 100;
                const f = feePct / 100;
                const denom = Math.max(0.05, (1 / (1 + g)) - f);

                // 2P: 2x cost, ₹45 ship, ₹20 pack, ₹30 rto
                const cTax2 = g > 0 ? ((cost * 2) / (1 + g)) : (cost * 2);
                const p2 = (cost * 2) * (marginPct / 100);
                const c2 = Math.ceil((cTax2 + 45 + 20 + 30 + p2) / denom);

                // 4P: 4x cost, ₹65 ship, ₹25 pack, ₹40 rto
                const cTax4 = g > 0 ? ((cost * 4) / (1 + g)) : (cost * 4);
                const p4 = (cost * 4) * (marginPct / 100);
                const c4 = Math.ceil((cTax4 + 65 + 25 + 40 + p4) / denom);

                // 6P: 6x cost, ₹85 ship, ₹30 pack, ₹50 rto
                const cTax6 = g > 0 ? ((cost * 6) / (1 + g)) : (cost * 6);
                const p6 = (cost * 6) * (marginPct / 100);
                const c6 = Math.ceil((cTax6 + 85 + 30 + 50 + p6) / denom);

                const r2 = document.getElementById(`row-combo2-${id}`);
                if (r2) r2.value = c2;
                const r4 = document.getElementById(`row-combo4-${id}`);
                if (r4) r4.value = c4;
                const r6 = document.getElementById(`row-combo6-${id}`);
                if (r6) r6.value = c6;
            });

            markUnsavedChanges();
            showChannelToast(`⚡ सभी ${rows.length} प्रोडक्ट्स के 2P, 4P, 6P कॉम्बो रेट्स री-कैलकुलेट हो गए!`);
        }

        async function bulkRecalcAndSaveCombos() {
            bulkRecalcCombos();
            await saveAllTableRowsCosting();
        }


        // Switch Pending Layout between Table Grid vs Cards (with localStorage persistence)
        function switchPendingLayout(layout) {
            try { localStorage.setItem('vyapar_channel_layout', layout); } catch(e) {}
            const cards = document.getElementById('pendingCardsContainer');
            const table = document.getElementById('pendingTableContainer');
            const btnCards = document.getElementById('btnPendingViewCards');
            const btnTable = document.getElementById('btnPendingViewTable');
            const btnTopCards = document.getElementById('btnTopSwitchCards');
            const btnTopTable = document.getElementById('btnTopSwitchTable');

            if (layout === 'cards') {
                if (table) table.classList.add('hidden');
                if (cards) cards.classList.remove('hidden');
                if (btnCards) {
                    btnCards.className = "px-4 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-1.5 bg-amber-500 text-slate-950 shadow-md cursor-pointer";
                }
                if (btnTable) {
                    btnTable.className = "px-4 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 text-slate-300 hover:text-white cursor-pointer";
                }
                if (btnTopCards) {
                    btnTopCards.className = "px-3.5 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-1.5 bg-indigo-600 text-white shadow-2xs cursor-pointer";
                }
                if (btnTopTable) {
                    btnTopTable.className = "px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 text-slate-700 hover:text-indigo-900 cursor-pointer";
                }
            } else {
                if (cards) cards.classList.add('hidden');
                if (table) table.classList.remove('hidden');
                if (btnTable) {
                    btnTable.className = "px-4 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-1.5 bg-amber-500 text-slate-950 shadow-md cursor-pointer";
                }
                if (btnCards) {
                    btnCards.className = "px-4 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 text-slate-300 hover:text-white cursor-pointer";
                }
                if (btnTopTable) {
                    btnTopTable.className = "px-3.5 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-1.5 bg-indigo-600 text-white shadow-2xs cursor-pointer";
                }
                if (btnTopCards) {
                    btnTopCards.className = "px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 text-slate-700 hover:text-indigo-900 cursor-pointer";
                }
            }
        }

        // On Page Load: Restore user's preferred layout (defaults to table / excel mode)
        document.addEventListener('DOMContentLoaded', function() {
            let prefLayout = 'table';
            try {
                prefLayout = localStorage.getItem('vyapar_channel_layout') || 'table';
            } catch(e) {}
            switchPendingLayout(prefLayout);
        });

        // Direct 1-Click Image Upload from Card / Table
        let currentDirectUploadProdId = null;

        function triggerDirectImageUpload(prodId, prodName) {
            currentDirectUploadProdId = prodId;
            let fileInput = document.getElementById('directProductImageFileInput');
            if (!fileInput) {
                fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.id = 'directProductImageFileInput';
                fileInput.accept = 'image/*';
                fileInput.className = 'hidden';
                fileInput.onchange = handleDirectImageUploadChange;
                document.body.appendChild(fileInput);
            }
            fileInput.click();
        }

        async function handleDirectImageUploadChange(event) {
            const file = event.target.files[0];
            if (!file || !currentDirectUploadProdId) return;

            const formData = new FormData();
            formData.append('image_file', file);
            formData.append('_token', '{{ csrf_token() }}');

            // Show instant feedback toast
            showChannelToast('फ़ोटो अपलोड हो रही है, कृपया प्रतीक्षा करें...');

            try {
                const res = await fetch(`/seller/products/${currentDirectUploadProdId}/quick-image-update`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const data = await res.json();
                if (data.success && data.image_url) {
                    // Update image in card
                    const imgEl = document.getElementById(`pending-prod-img-${currentDirectUploadProdId}`);
                    const phEl = document.getElementById(`pending-prod-img-ph-${currentDirectUploadProdId}`);
                    if (imgEl) {
                        imgEl.src = data.image_url;
                        imgEl.style.display = 'block';
                        imgEl.classList.remove('hidden');
                    }
                    if (phEl) phEl.classList.add('hidden');
                    showChannelToast('✓ फ़ोटो सफलतापूर्वक जुड़ गई और सेव हो गई!');
                } else {
                    alert(data.message || 'फ़ोटो अपलोड करने में त्रुटि आई।');
                }
            } catch(e) {
                alert('नेटवर्क त्रुटि: फ़ोटो अपलोड नहीं हो पाई।');
            }
            event.target.value = '';
        }

        function showChannelToast(msg) {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-5 right-5 z-50 bg-slate-900 text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-2 text-xs font-bold border border-slate-700 animate-in fade-in slide-in-from-bottom duration-300';
            toast.innerHTML = `<i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i> <span>${msg}</span>`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.4s';
                setTimeout(() => toast.remove(), 400);
            }, 3000);
        }

        // Direct Multi-Image Upload Trigger (Select 1 or Multiple Photos)
        let currentMultiUploadProdId = null;

        function triggerMultipleImageUpload(prodId, prodName) {
            currentMultiUploadProdId = prodId;
            let fileInput = document.getElementById('directMultiProductImageFileInput');
            if (!fileInput) {
                fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.id = 'directMultiProductImageFileInput';
                fileInput.accept = 'image/*';
                fileInput.multiple = true; // Allows selecting multiple images at once!
                fileInput.className = 'hidden';
                fileInput.onchange = handleMultipleImageUploadChange;
                document.body.appendChild(fileInput);
            }
            fileInput.click();
        }

        async function handleMultipleImageUploadChange(event) {
            const files = event.target.files;
            if (!files || files.length === 0 || !currentMultiUploadProdId) return;

            const formData = new FormData();
            for (let i = 0; i < files.length; i++) {
                formData.append('image_files[]', files[i]);
            }
            formData.append('_token', '{{ csrf_token() }}');

            showChannelToast(`${files.length} फ़ोटो अपलोड हो रही हैं, कृपया प्रतीक्षा करें...`);

            try {
                const res = await fetch(`/seller/products/${currentMultiUploadProdId}/quick-image-update`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    const prodId = currentMultiUploadProdId;
                    // Update main image in card & table
                    if (data.image_url) {
                        const cardImg = document.getElementById(`pending-prod-img-${prodId}`);
                        const cardPh = document.getElementById(`pending-prod-img-ph-${prodId}`);
                        if (cardImg) {
                            cardImg.src = data.image_url;
                            cardImg.style.display = 'block';
                            cardImg.classList.remove('hidden');
                        }
                        if (cardPh) cardPh.classList.add('hidden');

                        const tableImg = document.getElementById(`table-prod-img-${prodId}`);
                        const tablePh = document.getElementById(`table-prod-ph-${prodId}`);
                        if (tableImg) {
                            tableImg.src = data.image_url;
                            tableImg.style.display = 'block';
                            tableImg.classList.remove('hidden');
                        }
                        if (tablePh) tablePh.classList.add('hidden');
                    }

                    // Update count badges
                    const count = data.gallery_count || files.length;
                    const cardBadge = document.getElementById(`pending-img-badge-${prodId}`);
                    if (cardBadge) cardBadge.textContent = `${count} फ़ोटो`;
                    const tableBadge = document.getElementById(`table-img-badge-${prodId}`);
                    if (tableBadge) tableBadge.textContent = `${count} फ़ोटो`;

                    showChannelToast(`✓ ${files.length} फ़ोटो सफलतापूर्वक जुड़ गईं!`);
                } else {
                    alert(data.message || 'फ़ोटो अपलोड करने में त्रुटि आई।');
                }
            } catch(e) {
                alert('नेटवर्क त्रुटि: फ़ोटो अपलोड नहीं हो पाई।');
            }
            event.target.value = '';
        }

                // 2-Way Real-time recalculation: Less % -> Purchase Cost
        function recalcLessToCost(prodId) {
            const lessInp = document.getElementById(`row-less-${prodId}`);
            if (!lessInp) return;
            let mrp = parseFloat(lessInp.getAttribute('data-mrp')) || 0;
            if (mrp <= 0) {
                const priceInp = document.getElementById(`row-price-${prodId}`);
                mrp = parseFloat(priceInp?.value) || 0;
            }
            const lessVal = parseFloat(lessInp.value);
            const costInp = document.getElementById(`row-cost-${prodId}`);
            if (mrp > 0 && !isNaN(lessVal) && lessVal >= 0 && lessVal < 100) {
                const calculatedCost = Math.round((mrp * (1 - (lessVal / 100))) * 100) / 100;
                if (costInp) costInp.value = calculatedCost.toFixed(2);
                const cardCost = document.getElementById(`card-cost-${prodId}`);
                if (cardCost) cardCost.textContent = '₹' + calculatedCost.toFixed(2);
            }
            const cardLess = document.getElementById(`card-less-${prodId}`);
            if (cardLess && cardLess.value !== lessInp.value) cardLess.value = lessInp.value;

            recalcTableRow(prodId);
            markUnsavedChanges();
        }

        // 2-Way Real-time recalculation from Card view Less %
        function recalcCardLessToCost(prodId) {
            const cardLess = document.getElementById(`card-less-${prodId}`);
            if (!cardLess) return;
            let mrp = parseFloat(cardLess.getAttribute('data-mrp')) || 0;
            if (mrp <= 0) {
                const priceInp = document.getElementById(`row-price-${prodId}`);
                mrp = parseFloat(priceInp?.value) || 0;
            }
            const lessVal = parseFloat(cardLess.value);
            const costInp = document.getElementById(`row-cost-${prodId}`);
            if (mrp > 0 && !isNaN(lessVal) && lessVal >= 0 && lessVal < 100) {
                const calculatedCost = Math.round((mrp * (1 - (lessVal / 100))) * 100) / 100;
                if (costInp) costInp.value = calculatedCost.toFixed(2);
                const cardCost = document.getElementById(`card-cost-${prodId}`);
                if (cardCost) cardCost.textContent = '₹' + calculatedCost.toFixed(2);
            }
            const rowLess = document.getElementById(`row-less-${prodId}`);
            if (rowLess && rowLess.value !== cardLess.value) rowLess.value = cardLess.value;

            recalcTableRow(prodId);
            markUnsavedChanges();
        }

        // Instant Live Search Filter for Pending Cards & Table Rows
        function filterPendingQueueItems() {
            const q = (document.getElementById('pendingQueueSearchInp')?.value || '').trim().toLowerCase();
            
            // Filter Cards
            const cards = document.querySelectorAll('#pendingCardsContainer > div[id^="pending-card-box-"]');
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = (!q || text.includes(q)) ? '' : 'none';
            });

            // Filter Table Rows
            const rows = document.querySelectorAll('#pendingTableTbody tr[data-id]');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = (!q || text.includes(q)) ? '' : 'none';
            });
        }

        let unsavedChangesCount = 0;
        function markUnsavedChanges() {
            unsavedChangesCount++;
            const bar = document.getElementById('stickySaveBar');
            const txt = document.getElementById('stickyUnsavedCountText');
            if (bar) {
                bar.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
                bar.classList.add('translate-y-0', 'opacity-100');
            }
            if (txt) {
                txt.textContent = `आपने बदलाव किए हैं (${unsavedChangesCount})`;
            }
        }

        function clearUnsavedChanges() {
            unsavedChangesCount = 0;
            const bar = document.getElementById('stickySaveBar');
            if (bar) {
                bar.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
                bar.classList.remove('translate-y-0', 'opacity-100');
            }
        }

        // Live Stock Status Badge & Highlight
        function updateStockStatusBadge(prodId) {
            const inp = document.getElementById(`row-stock-${prodId}`);
            const badge = document.getElementById(`row-stock-warn-${prodId}`);
            const cell = document.getElementById(`row-stock-cell-${prodId}`);
            const val = parseInt(inp?.value) || 0;
            if (val <= 0) {
                if (badge) {
                    badge.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> 0 स्टॉक';
                    badge.className = 'text-[9px] mt-0.5 text-rose-600 font-black';
                }
                if (inp) inp.className = 'w-16 px-1 py-1 text-center font-mono font-black text-xs rounded-lg border border-rose-400 bg-rose-50 text-rose-700';
                if (cell) cell.className = 'p-2 text-center bg-rose-50/70';
            } else {
                if (badge) {
                    badge.textContent = '✓ तैयार';
                    badge.className = 'text-[9px] mt-0.5 text-emerald-700 font-bold';
                }
                if (inp) inp.className = 'w-16 px-1 py-1 text-center font-mono font-black text-xs rounded-lg border border-emerald-300 bg-white text-emerald-900';
                if (cell) cell.className = 'p-2 text-center bg-emerald-50/30';
            }
        }

        // Real-Time Table Row Unit Economics Recalculator with Margin %, RTO Buffer, GST & Card Sync
        function recalcTableRow(prodId) {
            const cost = parseFloat(document.getElementById(`row-cost-${prodId}`)?.value) || 0;
            const marginPct = parseFloat(document.getElementById(`row-margin-${prodId}`)?.value) || 25;
            const rto = parseFloat(document.getElementById(`row-rto-${prodId}`)?.value) || 0;
            const pack = parseFloat(document.getElementById(`row-pack-${prodId}`)?.value) || 0;
            const ship = parseFloat(document.getElementById(`row-ship-${prodId}`)?.value) || 0;
            const feePct = parseFloat(document.getElementById(`row-fee-${prodId}`)?.value) || 12;
            const ads = parseFloat(document.getElementById(`row-ads-${prodId}`)?.value) || 0;
            const others = parseFloat(document.getElementById(`row-others-${prodId}`)?.value) || 0;
            const gstRate = parseFloat(document.getElementById(`row-gst-${prodId}`)?.value) || 18;

            const desiredProfit = Math.round(cost * (marginPct / 100));
            // Gap GST Formula: net margin protected after paying output GST minus ITC on cost and fee
            const g = gstRate / 100;
            const f = feePct / 100;
            const cTax = g > 0 ? (cost / (1 + g)) : cost;
            const denom = Math.max(0.05, (1 / (1 + g)) - f);
            const fixedExp = rto + pack + ship + ads + others;
            const finalRate = Math.ceil((cTax + fixedExp + desiredProfit) / denom);

            // Update row price input
            const priceInput = document.getElementById(`row-price-${prodId}`);
            if (priceInput) priceInput.value = finalRate;

            // Update margin amount badge in table row
            const marginAmtSpan = document.getElementById(`row-margin-amt-${prodId}`);
            if (marginAmtSpan) marginAmtSpan.textContent = `₹${desiredProfit} लाभ`;

            // Update combo previews in table row (GST-Gap protected)
            const c2 = Math.ceil(((cTax * 2) + (desiredProfit * 1.8) + (rto * 1.5) + (pack * 1.3) + ship + (ads * 1.2)) / denom);
            const c4 = Math.ceil(((cTax * 4) + (desiredProfit * 3.5) + (rto * 2.0) + (pack * 1.6) + (ship * 1.2) + (ads * 1.5)) / denom);
            const c6 = Math.ceil(((cTax * 6) + (desiredProfit * 5.0) + (rto * 2.5) + (pack * 2.0) + (ship * 1.4) + (ads * 1.8)) / denom);

            const rC2 = document.getElementById(`row-combo2-${prodId}`);
            if (rC2) {
                if (rC2.tagName === 'INPUT') rC2.value = c2;
                else rC2.textContent = `2P: ₹${c2}`;
            }
            const rC4 = document.getElementById(`row-combo4-${prodId}`);
            if (rC4) {
                if (rC4.tagName === 'INPUT') rC4.value = c4;
                else rC4.textContent = `4P: ₹${c4}`;
            }
            const rC6 = document.getElementById(`row-combo6-${prodId}`);
            if (rC6) {
                if (rC6.tagName === 'INPUT') rC6.value = c6;
                else rC6.textContent = `6P: ₹${c6}`;
            }

            // 🔄 Platform-wise prices (Amazon 12% fee vs Meesho 0% fee)
            const denomAmz = Math.max(0.05, (1 / (1 + g)) - 0.12);
            const fixedAmz = rto + pack + ship + ads + others;
            const calcAmz = Math.ceil((cTax + fixedAmz + desiredProfit) / denomAmz);

            const denomMsh = Math.max(0.05, (1 / (1 + g)));
            const fixedMsh = Math.max(35, rto) + pack + Math.min(38, ship) + others;
            const calcMsh = Math.ceil((cTax + fixedMsh + desiredProfit) / denomMsh);

            // Update Amazon & Meesho badges in row and card
            const rowAmz = document.getElementById(`row-amz-price-${prodId}`);
            if (rowAmz) rowAmz.textContent = calcAmz;
            const cardAmz = document.getElementById(`card-amz-price-${prodId}`);
            if (cardAmz) cardAmz.textContent = `₹${calcAmz.toFixed(2)}`;

            const rowMsh = document.getElementById(`row-msh-price-${prodId}`);
            if (rowMsh) rowMsh.textContent = calcMsh;
            const cardMsh = document.getElementById(`card-msh-price-${prodId}`);
            if (cardMsh) cardMsh.textContent = `₹${calcMsh.toFixed(2)}`;

            // Update Card Combo spans
            const cardC2 = document.getElementById(`card-combo2-${prodId}`);
            if (cardC2) cardC2.textContent = `₹${c2}`;
            const cardC4 = document.getElementById(`card-combo4-${prodId}`);
            if (cardC4) cardC4.textContent = `₹${c4}`;
            const cardC6 = document.getElementById(`card-combo6-${prodId}`);
            if (cardC6) cardC6.textContent = `₹${c6}`;

            // Update Card Expenses Breakdown
            const cardPack = document.getElementById(`card-exp-pack-${prodId}`);
            if (cardPack) cardPack.textContent = `₹${pack}`;
            const cardShip = document.getElementById(`card-exp-ship-${prodId}`);
            if (cardShip) cardShip.textContent = `₹${ship}`;
            const cardRto = document.getElementById(`card-exp-rto-${prodId}`);
            if (cardRto) cardRto.textContent = `₹${rto}`;
            const cardFee = document.getElementById(`card-exp-fee-${prodId}`);
            if (cardFee) cardFee.textContent = `₹${Math.round((finalRate * f) + ads + others)}`;

            // 🔄 INSTANT LIVE SYNC TO CORRESPONDING PHOTO CARD!
            const cardPrice = document.getElementById(`card-price-${prodId}`);
            if (cardPrice) cardPrice.textContent = `₹${finalRate.toFixed(2)}`;

            const cardCost = document.getElementById(`card-cost-${prodId}`);
            if (cardCost) cardCost.textContent = `₹${cost.toFixed(2)}`;

            const totalExp = pack + ship + rto + Math.round(finalRate * (feePct / 100)) + ads + others;
            const cardExp = document.getElementById(`card-exp-${prodId}`);
            if (cardExp) cardExp.textContent = `+₹${totalExp.toFixed(2)}`;

            const cardProfit = document.getElementById(`card-profit-${prodId}`);
            if (cardProfit) cardProfit.textContent = `₹${desiredProfit.toFixed(2)} (${marginPct}%)`;
        }

        // Live sync combo input in table row to card badge
        function syncComboToCard(prodId, qty, val) {
            const el = document.getElementById(`card-combo${qty}-${prodId}`);
            if (el) el.textContent = `₹${val}`;
            markUnsavedChanges();
        }

        // Sync row margin to top preset input when edited so user knows it's ready to apply
        function syncMarginToPreset(prodId) {
            const val = parseFloat(document.getElementById(`row-margin-${prodId}`)?.value);
            if (!isNaN(val) && val > 0) {
                const presetInput = document.getElementById('bulkPresetMargin');
                if (presetInput) presetInput.value = val;
            }
            recalcTableRow(prodId);
        }

        // 1-Click Copy a specific row's full settings (Margin, RTO, Courier, Pack, GST) to all rows
        function copyRowSettingsToAll(sourceProdId) {
            const margin = parseFloat(document.getElementById(`row-margin-${sourceProdId}`)?.value) || 25;
            const rto = parseFloat(document.getElementById(`row-rto-${sourceProdId}`)?.value) || 20;
            const ship = parseFloat(document.getElementById(`row-ship-${sourceProdId}`)?.value) || 45;
            const pack = parseFloat(document.getElementById(`row-pack-${sourceProdId}`)?.value) || 15;
            const fee = parseFloat(document.getElementById(`row-fee-${sourceProdId}`)?.value) || 12;
            const gst = document.getElementById(`row-gst-${sourceProdId}`)?.value || '18';

            // Also sync to top preset toolbar
            if (document.getElementById('bulkPresetMargin')) document.getElementById('bulkPresetMargin').value = margin;
            if (document.getElementById('bulkPresetRto')) document.getElementById('bulkPresetRto').value = rto;
            if (document.getElementById('bulkPresetShip')) document.getElementById('bulkPresetShip').value = ship;
            if (document.getElementById('bulkPresetPack')) document.getElementById('bulkPresetPack').value = pack;
            if (document.getElementById('bulkPresetGst')) document.getElementById('bulkPresetGst').value = gst;

            const rows = document.querySelectorAll('#pendingTableTbody tr[data-id]');
            rows.forEach(row => {
                const id = row.getAttribute('data-id');
                const mInp = document.getElementById(`row-margin-${id}`);
                if (mInp) mInp.value = margin;

                const rInp = document.getElementById(`row-rto-${id}`);
                if (rInp) rInp.value = rto;

                const sInp = document.getElementById(`row-ship-${id}`);
                if (sInp) sInp.value = ship;

                const pInp = document.getElementById(`row-pack-${id}`);
                if (pInp) pInp.value = pack;

                const fInp = document.getElementById(`row-fee-${id}`);
                if (fInp) fInp.value = fee;

                const gSel = document.getElementById(`row-gst-${id}`);
                if (gSel) gSel.value = gst;

                recalcTableRow(id);
            });

            showChannelToast(`⚡ इस रो का ${margin}% मार्जिन, ₹${rto} रिटर्न, ₹${ship} कूरियर सभी ${rows.length} प्रोडक्ट्स पर लागू हो गया!`);
        }

                // 1-Click Platform Mode Switcher for Table View (Meesho vs Amazon)
        function setBulkPlatformPreset(platform) {
            if (platform === 'meesho') {
                if (document.getElementById('bulkPresetFee')) document.getElementById('bulkPresetFee').value = 0;
                if (document.getElementById('bulkPresetRto')) document.getElementById('bulkPresetRto').value = 35; // higher RTO buffer for Meesho COD
                if (document.getElementById('bulkPresetShip')) document.getElementById('bulkPresetShip').value = 38; // cheaper Meesho delivery
                if (document.getElementById('bulkPresetPack')) document.getElementById('bulkPresetPack').value = 15;
                if (document.getElementById('bulkPresetMargin')) document.getElementById('bulkPresetMargin').value = 25;
                
                applyPresetToAllRows();
                showChannelToast('🌸 Meesho मोड लागू! (0% कमीशन, ₹35 रिटर्न/RTO सुरक्षा बफ़र, ₹38 कूरियर) - सभी 19 प्रोडक्ट्स के Meesho रेट्स तैयार!');
            } else if (platform === 'amazon') {
                if (document.getElementById('bulkPresetFee')) document.getElementById('bulkPresetFee').value = 12;
                if (document.getElementById('bulkPresetRto')) document.getElementById('bulkPresetRto').value = 20;
                if (document.getElementById('bulkPresetShip')) document.getElementById('bulkPresetShip').value = 45;
                if (document.getElementById('bulkPresetPack')) document.getElementById('bulkPresetPack').value = 15;
                if (document.getElementById('bulkPresetMargin')) document.getElementById('bulkPresetMargin').value = 25;
                
                applyPresetToAllRows();
                showChannelToast('🟠 Amazon मोड लागू! (12% कमीशन, ₹20 रिटर्न बफ़र, ₹45 Easy Ship) - सभी 19 प्रोडक्ट्स के Amazon रेट्स तैयार!');
            }
        }

// Apply & Save Immediately to prevent reverting on refresh
        async function applyPresetAndSaveAll() {
            applyPresetToAllRows();
            await saveAllTableRowsCosting();
        }

        // Apply Bulk Preset Values to All Rows (Excel-style Apply to All)
        function applyPresetToAllRows() {
            // Read exactly what user entered in the top preset box
            const marginRaw = document.getElementById('bulkPresetMargin')?.value;
            const lessRaw = document.getElementById('bulkPresetLess')?.value;
            const lessVal = (lessRaw !== '' && !isNaN(parseFloat(lessRaw))) ? parseFloat(lessRaw) : null;
            const margin = (marginRaw !== '' && !isNaN(parseFloat(marginRaw))) ? parseFloat(marginRaw) : 25;

            const feeRaw = document.getElementById('bulkPresetFee')?.value;
            const fee = (feeRaw !== '' && !isNaN(parseFloat(feeRaw))) ? parseFloat(feeRaw) : 12;

            const stockRaw = document.getElementById('bulkPresetStock')?.value;
            const stockVal = (stockRaw !== '' && !isNaN(parseInt(stockRaw))) ? parseInt(stockRaw) : null;

            const rtoRaw = document.getElementById('bulkPresetRto')?.value;
            const rto = (rtoRaw !== '' && !isNaN(parseFloat(rtoRaw))) ? parseFloat(rtoRaw) : 20;

            const shipRaw = document.getElementById('bulkPresetShip')?.value;
            const ship = (shipRaw !== '' && !isNaN(parseFloat(shipRaw))) ? parseFloat(shipRaw) : 45;

            const packRaw = document.getElementById('bulkPresetPack')?.value;
            const pack = (packRaw !== '' && !isNaN(parseFloat(packRaw))) ? parseFloat(packRaw) : 15;

            const gst = document.getElementById('bulkPresetGst')?.value || '18';

            const rows = document.querySelectorAll('#pendingTableTbody tr[data-id]');
            if (!rows.length) {
                alert('कोई प्रोडक्ट मौजूद नहीं है!');
                return;
            }

            rows.forEach(row => {
                const id = row.getAttribute('data-id');
                if (lessVal !== null) {
                    const lInp = document.getElementById(`row-less-${id}`);
                    if (lInp) {
                        lInp.value = lessVal;
                        recalcLessToCost(id);
                    }
                }
                const marginInput = document.getElementById(`row-margin-${id}`);
                if (marginInput) marginInput.value = margin;

                const feeInput = document.getElementById(`row-fee-${id}`);
                if (feeInput) feeInput.value = fee;

                if (stockVal !== null) {
                    const stInp = document.getElementById(`row-stock-${id}`);
                    if (stInp) {
                        stInp.value = stockVal;
                        updateStockStatusBadge(id);
                    }
                }

                const rtoInput = document.getElementById(`row-rto-${id}`);
                if (rtoInput) rtoInput.value = rto;

                const shipInput = document.getElementById(`row-ship-${id}`);
                if (shipInput) shipInput.value = ship;

                const packInput = document.getElementById(`row-pack-${id}`);
                if (packInput) packInput.value = pack;

                const gstSelect = document.getElementById(`row-gst-${id}`);
                if (gstSelect) gstSelect.value = gst;

                recalcTableRow(id);
            });

            let msg = `⚡ सभी ${rows.length} प्रोडक्ट्स पर `;
            if (lessVal !== null) msg += `${lessVal}% लेस डिस्काउंट, `;
            msg += `${margin}% मार्जिन, ₹${rto} रिटर्न बफ़र, ₹${ship} कूरियर व ${gst}% GST लागू हो गया!`;
            showChannelToast(msg);
        }

        function syncManualPriceToCard(prodId) {
            const price = parseFloat(document.getElementById(`row-price-${prodId}`)?.value) || 0;
            const cardPrice = document.getElementById(`card-price-${prodId}`);
            if (cardPrice) cardPrice.textContent = `₹${price.toFixed(2)}`;
        }

        // Bulk Save All Table Rows Costing (Includes HSN, GST, RTO)
        async function saveAllTableRowsCosting() {
            const rows = document.querySelectorAll('#pendingTableTbody tr[data-id]');
            if (!rows.length) {
                alert('कोई प्रोडक्ट मौजूद नहीं है!');
                return;
            }

            const items = [];
            rows.forEach(row => {
                const id = row.getAttribute('data-id');
                const cost = parseFloat(document.getElementById(`row-cost-${id}`)?.value) || 0;
                const marginPct = parseFloat(document.getElementById(`row-margin-${id}`)?.value) || 25;
                const rto = parseFloat(document.getElementById(`row-rto-${id}`)?.value) || 0;
                const price = parseFloat(document.getElementById(`row-price-${id}`)?.value) || 0;
                const pack = parseFloat(document.getElementById(`row-pack-${id}`)?.value) || 0;
                const ship = parseFloat(document.getElementById(`row-ship-${id}`)?.value) || 0;
                const fee = parseFloat(document.getElementById(`row-fee-${id}`)?.value) || 0;
                const ads = parseFloat(document.getElementById(`row-ads-${id}`)?.value) || 0;
                const others = parseFloat(document.getElementById(`row-others-${id}`)?.value) || 0;
                const hsn = document.getElementById(`row-hsn-${id}`)?.value || '';
                const gst = parseFloat(document.getElementById(`row-gst-${id}`)?.value) || 18;
                const note = document.getElementById(`row-note-${id}`)?.value || '';

                const lessVal = parseFloat(document.getElementById(`row-less-${id}`)?.value) || 0;
                const c2Val = parseFloat(document.getElementById(`row-combo2-${id}`)?.value) || 0;
                const c4Val = parseFloat(document.getElementById(`row-combo4-${id}`)?.value) || 0;
                const c6Val = parseFloat(document.getElementById(`row-combo6-${id}`)?.value) || 0;
                items.push({
                    product_id: parseInt(id),
                    stock_quantity: stock,
                    purchase_price: cost,
                    trade_discount_percent: lessVal,
                    margin_pct: marginPct,
                    rto_buffer: rto,
                    price: price,
                    packaging: pack,
                    shipping: ship,
                    platform_fee_pct: fee,
                    ads: ads,
                    others: others,
                    hsn_code: hsn,
                    gst_percent: gst,
                    combo_2: c2Val,
                    combo_4: c4Val,
                    combo_6: c6Val,
                    custom_note: note
                });
            });

            const btn = document.getElementById('btnBulkSaveTable');
            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>सेव हो रहा है...</span>';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                const res = await fetch('/seller/channels/save-all-costing', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ items: items })
                });
                const data = await res.json();
                if (data.success) {
                    showChannelToast(`✓ ${data.saved_count} प्रोडक्ट्स के मार्जिन व रेट्स सेव हो गए!`);
                    clearUnsavedChanges();
                    btn.innerHTML = '<i class="fa-solid fa-check text-emerald-300"></i> <span>सफलतापूर्वक सेव हुआ!</span>';
                    setTimeout(() => {
                        btn.innerHTML = origHtml;
                        btn.disabled = false;
                    }, 2500);
                } else {
                    alert(data.message || 'सेव करने में त्रुटि आई।');
                    btn.innerHTML = origHtml;
                    btn.disabled = false;
                }
            } catch(e) {
                console.error(e);
                alert('नेटवर्क त्रुटि: सेव नहीं हो पाया।');
                btn.innerHTML = origHtml;
                btn.disabled = false;
            }
        }

        // Save Inline Table Row Costing & Unit Economics
        async function saveTableRowCosting(prodId, btn) {
            const cost = parseFloat(document.getElementById(`row-cost-${prodId}`)?.value) || 0;
            const marginPct = parseFloat(document.getElementById(`row-margin-${prodId}`)?.value) || 25;
            const rto = parseFloat(document.getElementById(`row-rto-${prodId}`)?.value) || 0;
            const price = parseFloat(document.getElementById(`row-price-${prodId}`)?.value) || 0;
            const pack = parseFloat(document.getElementById(`row-pack-${prodId}`)?.value) || 0;
            const ship = parseFloat(document.getElementById(`row-ship-${prodId}`)?.value) || 0;
            const feePct = parseFloat(document.getElementById(`row-fee-${prodId}`)?.value) || 0;
            const ads = parseFloat(document.getElementById(`row-ads-${prodId}`)?.value) || 0;
            const others = parseFloat(document.getElementById(`row-others-${prodId}`)?.value) || 0;
            const hsn = document.getElementById(`row-hsn-${prodId}`)?.value || '';
            const gst = parseFloat(document.getElementById(`row-gst-${prodId}`)?.value) || 18;
            const note = document.getElementById(`row-note-${prodId}`)?.value || '';

            if (price <= 0) {
                alert('कृपया वैध फाइनल सेलिंग रेट डालें!');
                return;
            }

            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i>';

            const lessVal = parseFloat(document.getElementById(`row-less-${prodId}`)?.value || document.getElementById(`card-less-${prodId}`)?.value) || 0;
            const stock = parseInt(document.getElementById(`row-stock-${prodId}`)?.value) || 0;
            try {
                const res = await fetch(`/seller/channels/products/${prodId}/save-costing`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        stock_quantity: stock,
                        purchase_price: cost,
                        trade_discount_percent: lessVal,
                        margin_pct: marginPct,
                        rto_buffer: rto,
                        price: price,
                        packaging: pack,
                        shipping: ship,
                        platform_fee_pct: feePct,
                        ads: ads,
                        others: others,
                        hsn_code: hsn,
                        gst_percent: gst,
                        combo_2: parseFloat(document.getElementById(`row-combo2-${prodId}`)?.value) || 0,
                        combo_4: parseFloat(document.getElementById(`row-combo4-${prodId}`)?.value) || 0,
                        combo_6: parseFloat(document.getElementById(`row-combo6-${prodId}`)?.value) || 0,
                        custom_note: note
                    })
                });
                const data = await res.json();
                if (data.success) {
                    btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
                    btn.classList.add('bg-blue-600');
                    btn.innerHTML = '<i class="fa-solid fa-check text-xs"></i>';
                    showChannelToast('✓ लागत, खर्चे व नया फाइनल सेलिंग रेट सेव हो गया!');
                    setTimeout(() => {
                        btn.classList.remove('bg-blue-600');
                        btn.classList.add('bg-emerald-600');
                        btn.innerHTML = origHtml;
                        btn.disabled = false;
                    }, 2000);
                } else {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                    alert(data.message || 'सेव करने में त्रुटि आई।');
                }
            } catch(e) {
                btn.disabled = false;
                btn.innerHTML = origHtml;
                alert('नेटवर्क त्रुटि: डेटा सेव नहीं हो पाया।');
            }
        }

        // Updated openCostCalculatorModal with purchasePrice support
        function openCostCalculatorModal(prodId, prodName, basePrice, purchasePrice = null) {
            activeCalcProductId = prodId;
            document.getElementById('calcModalProdName').textContent = prodName;
            const costVal = (purchasePrice && purchasePrice > 0) ? purchasePrice : Math.max(10, Math.round(basePrice * 0.70));
            document.getElementById('calcCostPrice').value = costVal;
            const rowGst = document.getElementById(`row-gst-${prodId}`)?.value;
            if (rowGst && document.getElementById('calcProductGst')) {
                document.getElementById('calcProductGst').value = rowGst;
            }
            const slabLink = document.getElementById('calcSlabSetupDirectLink'); if (slabLink) slabLink.href = `/seller/products/${prodId}/pricing-tiers`;
            calculateLiveSellingPrice();
            const modal = document.getElementById('costCalculatorModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }


        // --- 🛒 MARKETPLACE BASKET STAGING & UNSTAGING JS ---
        function openStageProductsModal() {
            const m = document.getElementById('stageProductsModal');
            if (m) {
                m.classList.remove('hidden');
                m.classList.add('flex');
            }
        }

        function closeStageProductsModal() {
            const m = document.getElementById('stageProductsModal');
            if (m) {
                m.classList.add('hidden');
                m.classList.remove('flex');
            }
        }

        function filterStageModalProducts() {
            const q = (document.getElementById('stageModalSearchInput').value || '').trim().toLowerCase();
            const items = document.querySelectorAll('.stage-modal-item');
            items.forEach(item => {
                const name = item.getAttribute('data-name') || '';
                const sku = item.getAttribute('data-sku') || '';
                if (!q || name.includes(q) || sku.includes(q)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function toggleSelectAllStageModal(checked) {
            const visibleChks = document.querySelectorAll('.stage-modal-item:not([style*="display: none"]) .stage-modal-chk');
            visibleChks.forEach(chk => {
                chk.checked = checked;
            });
            updateStageModalSelectedCount();
        }

        function updateStageModalSelectedCount() {
            const checked = document.querySelectorAll('.stage-modal-chk:checked').length;
            const badge = document.getElementById('stageModalSelectedCountBadge');
            if (badge) {
                badge.textContent = `${checked} चुने गए`;
            }
        }

        function unstageProductItem(productId, productName) {
            if (!confirm(`क्या आप "${productName}" को Amazon/Meesho पेंडिंग बास्केट से हटाना चाहते हैं?\n(यह प्रोडक्ट आपकी दुकान के मुख्य कैटलॉग में 100% सुरक्षित रहेगा)`)) {
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}';

            fetch(`/seller/channels/products/${productId}/unstage`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Remove card
                    const card = document.getElementById(`pending-card-box-${productId}`);
                    if (card) card.remove();

                    // Remove table row
                    const row = document.getElementById(`table-row-${productId}`);
                    if (row) row.remove();

                    // Decrement counter badges
                    const badges = document.querySelectorAll('.pending-count-badge');
                    badges.forEach(b => {
                        let c = parseInt(b.textContent.trim()) || 0;
                        if (c > 0) b.textContent = c - 1;
                    });

                    // If no items remain, reload to show clean empty state
                    const remaining = document.querySelectorAll('.pending-prod-chk');
                    if (remaining.length === 0) {
                        window.location.reload();
                    }
                } else {
                    alert(data.message || 'त्रुटि हुई');
                }
            })
            .catch(err => {
                console.error(err);
                window.location.reload();
            });
        }

</script>

        <!-- 🧮 MARKETPLACE COSTING, EXPENSES & PROFIT CALCULATOR MODAL -->
    <div id="costCalculatorModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-4 overflow-y-auto" onclick="closeCostCalculatorModal()">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-4 animate-in fade-in zoom-in duration-200 my-auto" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-sm font-bold shadow-md">
                        <i class="fa-solid fa-calculator"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-sm text-gray-900">लागत, खर्चे व मुनाफा कैलकुलेटर</h3>
                        <p class="text-[11px] text-gray-500 truncate max-w-[280px]" id="calcModalProdName">Product</p>
                    </div>
                </div>
                <button type="button" onclick="closeCostCalculatorModal()" class="text-gray-400 hover:text-gray-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Interactive Pricing Simulator -->
            <div class="space-y-3.5 text-xs">
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 p-3 rounded-2xl border border-gray-200">
                        <label class="block font-bold text-gray-600 mb-1">आपकी खरीद लागत (Cost Price)</label>
                        <div class="relative">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 font-bold text-gray-400">₹</span>
                            <input type="number" id="calcCostPrice" value="100" oninput="calculateLiveSellingPrice()" class="w-full pl-6 pr-2.5 py-1.5 rounded-xl border border-gray-300 bg-white font-mono font-bold text-gray-900">
                        </div>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-2xl border border-gray-200">
                        <label class="block font-bold text-gray-600 mb-1">आपका शुद्ध मुनाफा (Margin %)</label>
                        <div class="relative">
                            <input type="number" id="calcMarginPercent" value="25" oninput="calculateLiveSellingPrice()" class="w-full pr-6 pl-2.5 py-1.5 rounded-xl border border-gray-300 bg-white font-mono font-bold text-emerald-700">
                            <span class="absolute right-2.5 top-1/2 -translate-y-1/2 font-bold text-gray-400">%</span>
                        </div>
                    </div>
                </div>

                <!-- Platform Expenses Breakdown -->
                <div class="bg-indigo-50/60 p-3.5 rounded-2xl border border-indigo-100 space-y-2.5">
                    <div class="flex items-center justify-between flex-wrap gap-1.5">
                        <span class="text-[10px] font-black uppercase text-indigo-900 tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-receipt text-indigo-600"></i>
                            <span>मार्केटप्लेस खर्चे (अनुमानित 1 पीस के लिए)</span>
                        </span>
                        <!-- 1-Click Platform Switcher -->
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="setCalcPlatformPreset('amazon')" id="calcPresetBtnAmz" class="px-2 py-0.5 rounded-lg bg-amber-500 text-slate-950 font-black text-[10px] shadow-2xs hover:scale-102 active:scale-95 transition cursor-pointer">
                                🟠 Amazon (12% फ़ी)
                            </button>
                            <button type="button" onclick="setCalcPlatformPreset('meesho')" id="calcPresetBtnMsh" class="px-2 py-0.5 rounded-lg bg-pink-100 hover:bg-pink-200 text-pink-900 font-black text-[10px] border border-pink-300 shadow-2xs hover:scale-102 active:scale-95 transition cursor-pointer">
                                🌸 Meesho (0% फ़ी)
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-2">
                        <div>
                            <label class="text-[10px] text-gray-600 block">पैकिंग खर्च</label>
                            <input type="number" id="calcPackingCost" value="15" oninput="calculateLiveSellingPrice()" class="w-full px-2 py-1 bg-white border border-indigo-200 rounded-lg font-mono font-semibold text-gray-800">
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-600 block" title="कूरियर बिल पर सरकार 18% GST लगाती है जिसका पूरा ITC मिलता है">शिपिंग (कूरियर)</label>
                            <input type="number" id="calcShippingCost" value="45" oninput="calculateLiveSellingPrice()" class="w-full px-2 py-1 bg-white border border-indigo-200 rounded-lg font-mono font-semibold text-gray-800">
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-600 block" title="कमीशन पर 18% GST लगता है जिसका पूरा ITC मिलता है">प्लेटफ़ॉर्म फ़ी (%)</label>
                            <input type="number" id="calcPlatformFeePercent" value="12" oninput="calculateLiveSellingPrice()" class="w-full px-2 py-1 bg-white border border-indigo-200 rounded-lg font-mono font-semibold text-gray-800">
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-600 block font-bold text-indigo-900" title="प्रोडक्ट का खुद का टैक्स रेट (उदा. 5%, 12%, 18%)">प्रोडक्ट GST</label>
                            <select id="calcProductGst" onchange="calculateLiveSellingPrice()" class="w-full px-1.5 py-1 bg-white border-2 border-indigo-300 rounded-lg font-mono font-bold text-indigo-900 text-xs">
                                <option value="18" selected>18% (हार्डवेयर/प्लंबिंग)</option>
                                <option value="5">5% (कपड़े/कम दर)</option>
                                <option value="12">12%</option>
                                <option value="0">0% (टैक्स फ्री)</option>
                                <option value="28">28% (लक्ज़री)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Calculated Final Price Result Box -->
                <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white space-y-3 shadow-lg border border-indigo-500/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-indigo-300">सुझाया गया फाइनल सेलिंग रेट (Selling Price)</span>
                            <div class="text-2xl font-black text-amber-300 font-mono mt-0.5" id="calcFinalSellingRate">₹199.00</div>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-emerald-400">शुद्ध जेब में बचत (Net Profit)</span>
                            <div class="text-lg font-black text-emerald-400 font-mono" id="calcNetProfitInHand">₹25.00</div>
                        </div>
                    </div>

                    <!-- 📊 Smart GST & ITC Breakdown Engine (Amazon Commission vs Meesho Courier Base) -->
                    <div class="p-3 bg-slate-950/80 rounded-2xl border border-indigo-500/30 text-[11px] space-y-1.5 font-sans">
                        <div class="flex items-center justify-between text-indigo-300 font-bold text-[10px] uppercase border-b border-white/10 pb-1">
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-scale-balanced text-amber-300"></i>
                                <span>स्मार्ट GST & ITC टैक्स इंजन (Real-Time)</span>
                            </span>
                            <span class="text-amber-300 font-mono font-bold" id="calcGstPlatformLabel">Meesho (0% फ़ी)</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-300 text-[10px] font-mono">
                            <span>📦 माल पर आउटपुट GST (ग्राहक से मिला - <span id="calcGstProdLabel" class="text-amber-200 font-bold">18%</span>):</span>
                            <span class="font-bold text-amber-300" id="calcGstOutput">₹28.83</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-400 text-[10px] font-mono" title="कूरियर/लॉजिस्टिक्स सेवा (SAC 9968) पर भारत सरकार का 18% फिक्स GST लगता है, जो आपको ITC क्रेडिट के रूप में वापस मिल जाता है">
                            <span>🚚 कूरियर ITC (फिक्स 18% GST वापसी):</span>
                            <span class="text-emerald-400 font-bold" id="calcGstCourierItc">-₹6.84</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-400 text-[10px] font-mono" title="मार्केटप्लेस सेवा (SAC 9983) पर 18% फिक्स GST लगता है, जो ITC में वापस मिलता है">
                            <span>💼 कमीशन ITC (फिक्स 18% GST वापसी):</span>
                            <span class="text-emerald-400 font-bold" id="calcGstCommItc">₹0.00 (Meesho 0%)</span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-white/10 font-bold text-[11px]">
                            <span class="text-white">🏛️ सरकार को शुद्ध नकद GST (देय):</span>
                            <span class="text-rose-400 font-mono font-extrabold" id="calcGstNetCash">₹6.74</span>
                        </div>
                    </div>

                    <!-- 2 PC, 4 PC, 6 PC COMBO PREVIEWS -->
                    <div class="pt-2 border-t border-white/10 space-y-1.5">
                        <span class="text-[10px] font-bold text-amber-200 flex items-center gap-1">
                            <i class="fa-solid fa-boxes-packing"></i>
                            <span>कॉम्बो पैक में शिपिंग बचने पर रेट्स (3x ज़्यादा बिकते हैं):</span>
                        </span>
                        <div class="grid grid-cols-3 gap-2 text-center text-[11px]">
                            <div class="bg-white/10 p-1.5 rounded-xl border border-white/10">
                                <span class="text-[9px] text-slate-300 block">2 पीस पैक</span>
                                <span class="font-black text-white font-mono" id="calcCombo2Rate">₹359.00</span>
                                <span class="text-[8px] text-emerald-300 block">₹179/pc</span>
                            </div>
                            <div class="bg-white/10 p-1.5 rounded-xl border border-white/10">
                                <span class="text-[9px] text-slate-300 block">4 पीस पैक</span>
                                <span class="font-black text-white font-mono" id="calcCombo4Rate">₹679.00</span>
                                <span class="text-[8px] text-emerald-300 block">₹169/pc</span>
                            </div>
                            <div class="bg-white/10 p-1.5 rounded-xl border border-white/10">
                                <span class="text-[9px] text-slate-300 block">6 पीस पैक</span>
                                <span class="font-black text-white font-mono" id="calcCombo6Rate">₹959.00</span>
                                <span class="text-[8px] text-emerald-300 block">₹159/pc</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="pt-2 flex items-center justify-between gap-2 border-t border-gray-100">
                    <button type="button" onclick="closeCostCalculatorModal(); openComboCalculatorModal(activeCalcProductId, document.getElementById('calcModalProdName').textContent)" class="px-4 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-800 font-bold transition flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-layer-group text-indigo-600"></i>
                        <span>2/4/6 पीस स्लैब विवरण &rarr;</span>
                    </button>
                    <button type="button" onclick="closeCostCalculatorModal()" class="px-4 py-2 rounded-xl bg-gray-900 text-white font-bold transition">
                        ठीक है (Close)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 📦 COMBO PACK DETAILS & INDIVIDUAL EXPENSES MODAL (2P / 4P / 6P) -->
    <div id="comboCalculatorModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-4 overflow-y-auto" onclick="closeComboCalculatorModal()">
        <div class="bg-white rounded-3xl max-w-4xl w-full p-5 sm:p-6 shadow-2xl border border-gray-100 space-y-4 animate-in fade-in zoom-in duration-200 my-auto" onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-lg font-bold shadow-md">
                        <i class="fa-solid fa-boxes-packing"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-black text-sm sm:text-base text-gray-900">2 / 4 / 6 पीस कॉम्बो खर्च, कूरियर व रेट विवरण</h3>
                            <span class="text-[10px] bg-indigo-50 text-indigo-700 font-bold px-2 py-0.5 rounded-md border border-indigo-200" id="comboModalGstRate">18% GST</span>
                        </div>
                        <p class="text-xs text-gray-500 truncate max-w-md mt-0.5" id="comboModalProdName">Product</p>
                    </div>
                </div>
                <button type="button" onclick="closeComboCalculatorModal()" class="text-gray-400 hover:text-gray-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Base Info Pill -->
            <div class="flex items-center justify-between bg-amber-50/80 p-2.5 rounded-2xl border border-amber-200 text-xs">
                <div class="flex items-center gap-2 text-amber-900 font-bold">
                    <i class="fa-solid fa-tag text-amber-600"></i>
                    <span>1 पीस खरीद लागत (With GST): <strong class="font-mono text-gray-900" id="comboModalBaseCost">₹0.00</strong></span>
                </div>
                <span class="text-[11px] text-amber-800">कॉम्बो में वजन के अनुसार कूरियर व RTO अलग जोड़ें और मनचाहा रेट सेट करें।</span>
            </div>

            <!-- 3 Combo Cards: 2P, 4P, 6P -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 text-xs">
                <!-- 2 PC PACK -->
                <div class="bg-slate-50 p-3.5 rounded-2xl border border-indigo-200 space-y-2.5 flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between border-b border-indigo-100 pb-1.5">
                            <span class="font-black text-xs text-indigo-950 flex items-center gap-1">
                                <span class="w-5 h-5 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-[10px] font-mono">2P</span>
                                <span>2 पीस पैक</span>
                            </span>
                            <span class="text-[10px] font-bold text-gray-500 font-mono" id="combo2_cost">₹0.00</span>
                        </div>

                        <!-- Expenses -->
                        <div class="space-y-1.5 text-[11px]">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">कूरियर / डिलीवरी:</span>
                                <div class="relative w-20">
                                    <span class="absolute left-1.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]">₹</span>
                                    <input type="number" id="combo2_ship" value="45" oninput="recalcComboModalLive(2)" class="w-full pl-4 pr-1 py-0.5 bg-white border border-gray-300 rounded text-right font-mono font-bold text-gray-800">
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">पैकिंग खर्च:</span>
                                <div class="relative w-20">
                                    <span class="absolute left-1.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]">₹</span>
                                    <input type="number" id="combo2_pack" value="20" oninput="recalcComboModalLive(2)" class="w-full pl-4 pr-1 py-0.5 bg-white border border-gray-300 rounded text-right font-mono font-bold text-gray-800">
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">RTO / रिटर्न बफ़र:</span>
                                <div class="relative w-20">
                                    <span class="absolute left-1.5 top-1/2 -translate-y-1/2 text-rose-400 text-[10px]">₹</span>
                                    <input type="number" id="combo2_rto" value="30" oninput="recalcComboModalLive(2)" class="w-full pl-4 pr-1 py-0.5 bg-white border border-rose-300 rounded text-right font-mono font-bold text-rose-700">
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">मार्जिन %:</span>
                                <div class="relative w-20">
                                    <input type="number" id="combo2_margin" value="25" oninput="recalcComboModalLive(2)" class="w-full pr-4 pl-1 py-0.5 bg-white border border-gray-300 rounded text-right font-mono font-bold text-emerald-700">
                                    <span class="absolute right-1.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]">%</span>
                                </div>
                            </div>
                            <input type="hidden" id="combo2_fee" value="12">
                        </div>
                    </div>

                    <!-- Output & Price -->
                    <div class="pt-2 border-t border-indigo-100 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold text-indigo-950 uppercase">फाइनल 2P रेट:</span>
                            <div class="relative w-24">
                                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-amber-600 font-bold text-xs">₹</span>
                                <input type="number" id="combo2_price" value="0" oninput="recalcComboModalProfit(2)" class="w-full pl-5 pr-1.5 py-1 bg-white border-2 border-amber-400 rounded-lg text-xs font-mono font-black text-amber-950 text-right">
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[10px]">
                            <span class="text-gray-500">शुद्ध जेब मुनाफा:</span>
                            <span class="font-mono font-black text-emerald-700" id="combo2_profit">₹0.00</span>
                        </div>
                        <div class="text-[9px] text-gray-400 text-right" id="combo2_per_pc">₹0/pc</div>
                    </div>
                </div>

                <!-- 4 PC PACK -->
                <div class="bg-indigo-50/50 p-3.5 rounded-2xl border-2 border-indigo-400 space-y-2.5 flex flex-col justify-between shadow-xs">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between border-b border-indigo-200 pb-1.5">
                            <span class="font-black text-xs text-indigo-950 flex items-center gap-1">
                                <span class="w-5 h-5 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-[10px] font-mono">4P</span>
                                <span>4 पीस पैक (Best Seller)</span>
                            </span>
                            <span class="text-[10px] font-bold text-gray-500 font-mono" id="combo4_cost">₹0.00</span>
                        </div>

                        <!-- Expenses -->
                        <div class="space-y-1.5 text-[11px]">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">कूरियर / डिलीवरी:</span>
                                <div class="relative w-20">
                                    <span class="absolute left-1.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]">₹</span>
                                    <input type="number" id="combo4_ship" value="65" oninput="recalcComboModalLive(4)" class="w-full pl-4 pr-1 py-0.5 bg-white border border-gray-300 rounded text-right font-mono font-bold text-gray-800">
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">पैकिंग खर्च:</span>
                                <div class="relative w-20">
                                    <span class="absolute left-1.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]">₹</span>
                                    <input type="number" id="combo4_pack" value="25" oninput="recalcComboModalLive(4)" class="w-full pl-4 pr-1 py-0.5 bg-white border border-gray-300 rounded text-right font-mono font-bold text-gray-800">
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">RTO / रिटर्न बफ़र:</span>
                                <div class="relative w-20">
                                    <span class="absolute left-1.5 top-1/2 -translate-y-1/2 text-rose-400 text-[10px]">₹</span>
                                    <input type="number" id="combo4_rto" value="40" oninput="recalcComboModalLive(4)" class="w-full pl-4 pr-1 py-0.5 bg-white border border-rose-300 rounded text-right font-mono font-bold text-rose-700">
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">मार्जिन %:</span>
                                <div class="relative w-20">
                                    <input type="number" id="combo4_margin" value="25" oninput="recalcComboModalLive(4)" class="w-full pr-4 pl-1 py-0.5 bg-white border border-gray-300 rounded text-right font-mono font-bold text-emerald-700">
                                    <span class="absolute right-1.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]">%</span>
                                </div>
                            </div>
                            <input type="hidden" id="combo4_fee" value="12">
                        </div>
                    </div>

                    <!-- Output & Price -->
                    <div class="pt-2 border-t border-indigo-200 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold text-indigo-950 uppercase">फाइनल 4P रेट:</span>
                            <div class="relative w-24">
                                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-amber-600 font-bold text-xs">₹</span>
                                <input type="number" id="combo4_price" value="0" oninput="recalcComboModalProfit(4)" class="w-full pl-5 pr-1.5 py-1 bg-white border-2 border-emerald-500 rounded-lg text-xs font-mono font-black text-emerald-950 text-right">
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[10px]">
                            <span class="text-gray-500">शुद्ध जेब मुनाफा:</span>
                            <span class="font-mono font-black text-emerald-700" id="combo4_profit">₹0.00</span>
                        </div>
                        <div class="text-[9px] text-gray-400 text-right" id="combo4_per_pc">₹0/pc</div>
                    </div>
                </div>

                <!-- 6 PC PACK -->
                <div class="bg-slate-50 p-3.5 rounded-2xl border border-indigo-200 space-y-2.5 flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between border-b border-indigo-100 pb-1.5">
                            <span class="font-black text-xs text-indigo-950 flex items-center gap-1">
                                <span class="w-5 h-5 rounded-lg bg-amber-600 text-white flex items-center justify-center text-[10px] font-mono">6P</span>
                                <span>6 पीस पैक</span>
                            </span>
                            <span class="text-[10px] font-bold text-gray-500 font-mono" id="combo6_cost">₹0.00</span>
                        </div>

                        <!-- Expenses -->
                        <div class="space-y-1.5 text-[11px]">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">कूरियर / डिलीवरी:</span>
                                <div class="relative w-20">
                                    <span class="absolute left-1.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]">₹</span>
                                    <input type="number" id="combo6_ship" value="85" oninput="recalcComboModalLive(6)" class="w-full pl-4 pr-1 py-0.5 bg-white border border-gray-300 rounded text-right font-mono font-bold text-gray-800">
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">पैकिंग खर्च:</span>
                                <div class="relative w-20">
                                    <span class="absolute left-1.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]">₹</span>
                                    <input type="number" id="combo6_pack" value="30" oninput="recalcComboModalLive(6)" class="w-full pl-4 pr-1 py-0.5 bg-white border border-gray-300 rounded text-right font-mono font-bold text-gray-800">
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">RTO / रिटर्न बफ़र:</span>
                                <div class="relative w-20">
                                    <span class="absolute left-1.5 top-1/2 -translate-y-1/2 text-rose-400 text-[10px]">₹</span>
                                    <input type="number" id="combo6_rto" value="50" oninput="recalcComboModalLive(6)" class="w-full pl-4 pr-1 py-0.5 bg-white border border-rose-300 rounded text-right font-mono font-bold text-rose-700">
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">मार्जिन %:</span>
                                <div class="relative w-20">
                                    <input type="number" id="combo6_margin" value="25" oninput="recalcComboModalLive(6)" class="w-full pr-4 pl-1 py-0.5 bg-white border border-gray-300 rounded text-right font-mono font-bold text-emerald-700">
                                    <span class="absolute right-1.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]">%</span>
                                </div>
                            </div>
                            <input type="hidden" id="combo6_fee" value="12">
                        </div>
                    </div>

                    <!-- Output & Price -->
                    <div class="pt-2 border-t border-indigo-100 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold text-indigo-950 uppercase">फाइनल 6P रेट:</span>
                            <div class="relative w-24">
                                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-amber-600 font-bold text-xs">₹</span>
                                <input type="number" id="combo6_price" value="0" oninput="recalcComboModalProfit(6)" class="w-full pl-5 pr-1.5 py-1 bg-white border-2 border-amber-400 rounded-lg text-xs font-mono font-black text-amber-950 text-right">
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[10px]">
                            <span class="text-gray-500">शुद्ध जेब मुनाफा:</span>
                            <span class="font-mono font-black text-emerald-700" id="combo6_profit">₹0.00</span>
                        </div>
                        <div class="text-[9px] text-gray-400 text-right" id="combo6_per_pc">₹0/pc</div>
                    </div>
                </div>
            </div>

            <!-- Footer Save Actions -->
            <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-3">
                <button type="button" onclick="closeComboCalculatorModal()" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition">
                    रद्द करें (Close)
                </button>
                <button type="button" id="btnSaveComboModal" onclick="saveComboModalValues(this)" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs shadow-md transition flex items-center gap-2 active:scale-95 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk text-amber-200"></i>
                    <span>💾 2P, 4P, 6P कॉम्बो रेट्स तुरंत सेव करें (Save All Combos)</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 🛒 Add Products from Store Catalog to Marketplace Queue Modal -->
    <div id="stageProductsModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-4 overflow-y-auto" onclick="closeStageProductsModal()">
        <div class="bg-white rounded-3xl max-w-3xl w-full p-6 shadow-2xl border border-gray-100 space-y-4 my-auto animate-in fade-in zoom-in duration-200 flex flex-col max-h-[90vh]" onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg border border-amber-200 font-bold">
                        <i class="fa-solid fa-basket-shopping"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-sm text-gray-900">दुकान से प्रोडक्ट्स चुनें (Amazon / Meesho बास्केट)</h3>
                        <p class="text-[11px] text-gray-500">आपकी दुकान के कुल <strong>{{ count($allStoreProducts ?? []) }} प्रोडक्ट्स</strong> हैं। केवल वही चुनें जिन्हें आप मार्केटप्लेस पर बेचना चाहते हैं।</p>
                    </div>
                </div>
                <button type="button" onclick="closeStageProductsModal()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-xl hover:bg-gray-100 transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form action="{{ route('seller.channels.stageProducts') }}" method="POST" class="space-y-4 flex-1 flex flex-col min-h-0">
                @csrf
                
                <!-- Target Channels Selection -->
                <div class="p-3 bg-amber-50/60 border border-amber-200 rounded-2xl flex flex-wrap items-center justify-between gap-2.5">
                    <span class="text-xs font-black text-amber-950 flex items-center gap-1.5">
                        <i class="fa-solid fa-bullseye text-amber-600"></i>
                        <span>किन मार्केट्स पर लिस्ट करना है?</span>
                    </span>
                    <div class="flex items-center gap-3">
                        <label class="text-xs font-bold text-gray-700 flex items-center gap-1.5 bg-white px-2.5 py-1 rounded-xl border border-gray-200 cursor-pointer">
                            <input type="checkbox" name="channels[]" value="amazon" checked class="rounded text-amber-600">
                            <span>Amazon</span>
                        </label>
                        <label class="text-xs font-bold text-gray-700 flex items-center gap-1.5 bg-white px-2.5 py-1 rounded-xl border border-gray-200 cursor-pointer">
                            <input type="checkbox" name="channels[]" value="meesho" checked class="rounded text-pink-600">
                            <span>Meesho</span>
                        </label>
                        <label class="text-xs font-bold text-gray-700 flex items-center gap-1.5 bg-white px-2.5 py-1 rounded-xl border border-gray-200 cursor-pointer">
                            <input type="checkbox" name="channels[]" value="flipkart" class="rounded text-blue-600">
                            <span>Flipkart</span>
                        </label>
                    </div>
                </div>

                <!-- Search and Filters Bar -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2">
                    <div class="relative flex-1">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="stageModalSearchInput" oninput="filterStageModalProducts()" placeholder="🔍 नाम या SKU से सर्च करें..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-amber-500 text-xs font-semibold outline-none transition">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="inline-flex items-center gap-1.5 px-3 py-2 bg-gray-50 hover:bg-gray-100 rounded-xl border border-gray-200 text-xs font-bold text-gray-700 cursor-pointer">
                            <input type="checkbox" id="selectAllStageModalChk" onchange="toggleSelectAllStageModal(this.checked)" class="rounded text-amber-600 h-4 w-4">
                            <span>सभी चुनें (Select All)</span>
                        </label>
                        <span id="stageModalSelectedCountBadge" class="px-2.5 py-1.5 rounded-xl bg-amber-100 text-amber-800 text-xs font-black">
                            0 चुने गए
                        </span>
                    </div>
                </div>

                <!-- Products Table / List (Scrollable) -->
                <div class="flex-1 overflow-y-auto border border-gray-200 rounded-2xl divide-y divide-gray-100 max-h-72">
                    @forelse($allStoreProducts ?? [] as $sp)
                        <label class="flex items-center justify-between p-3 hover:bg-amber-50/30 transition cursor-pointer stage-modal-item" data-name="{{ strtolower($sp->name) }}" data-sku="{{ strtolower($sp->sku ?? '') }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <input type="checkbox" name="product_ids[]" value="{{ $sp->id }}" onchange="updateStageModalSelectedCount()" class="stage-modal-chk rounded text-amber-600 h-4 w-4 shrink-0">
                                <div class="h-10 w-10 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center shrink-0">
                                    <img src="{{ $sp->image_url ?? '' }}" alt="{{ $sp->name }}" class="h-full w-full object-contain {{ !empty($sp->image_url) ? '' : 'hidden' }}" onerror="this.style.display='none'">
                                    <i class="fa-solid fa-box text-gray-300 text-xs {{ !empty($sp->image_url) ? 'hidden' : 'block' }}"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="font-bold text-xs text-gray-900 block truncate" title="{{ $sp->name }}">{{ $sp->name }}</span>
                                    <div class="flex items-center gap-2 text-[10px] text-gray-400 font-mono mt-0.5">
                                        <span>SKU: {{ $sp->sku ?: 'STD' }}</span>
                                        <span class="text-gray-300">•</span>
                                        <span class="text-indigo-600 font-medium">{{ $sp->category->name ?? 'General' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right shrink-0 pl-3">
                                <div class="font-mono font-black text-xs text-gray-900">₹{{ number_format($sp->price, 2) }}</div>
                                <span class="text-[10px] text-gray-400">स्टॉक: {{ $sp->stock_quantity ?? 0 }}</span>
                            </div>
                        </label>
                    @empty
                        <div class="p-8 text-center text-xs text-gray-400">
                            आपकी दुकान में कोई प्रोडक्ट नहीं मिला। कृपया पहले प्रोडक्ट्स जोड़ें।
                        </div>
                    @endforelse
                </div>

                <!-- Footer Actions -->
                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeStageProductsModal()" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition cursor-pointer">
                        रद्द करें
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-black text-xs shadow-md shadow-amber-500/25 transition active:scale-95 flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-plus-circle text-amber-200"></i>
                        <span>+ पेंडिंग बास्केट में जोड़ें (Add to Channel Queue)</span>
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- Hidden Form for Excel / CSV Sheet Upload -->
    <form id="channelCsvUploadForm" action="{{ route('seller.channels.import_sheet') }}" method="POST" enctype="multipart/form-data" style="display:none;">
        @csrf
        <input type="file" id="channelCsvUploadInput" name="sheet" accept=".xls,.xlsx,.csv,.tsv,.txt" onchange="handleChannelSheetUpload(this)">
    </form>
    <script>
    function triggerChannelSheetUpload() {
        var input = document.getElementById("channelCsvUploadInput");
        if (input) {
            input.value = "";
            input.click();
        } else {
            alert("अपलोड फ़ॉर्म नहीं मिला, कृपया पेज रीफ़्रेश करें।");
        }
    }

    function handleChannelSheetUpload(input) {
        if (input.files && input.files.length > 0) {
            var triggers = document.querySelectorAll(".btn-sheet-upload-trigger");
            triggers.forEach(function(btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-amber-300"></i> <span>अपलोड व सिंक हो रहा है...</span>';
            });
            document.getElementById("channelCsvUploadForm").submit();
        }
    }
    </script>

<!-- Floating Sticky Save All Bar -->
<div id="stickySaveBar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-slate-900/95 backdrop-blur-md text-white px-5 py-3 rounded-2xl shadow-2xl border border-indigo-500/40 flex items-center gap-4 transition-all duration-300 transform translate-y-24 opacity-0 pointer-events-none">
    <div class="flex items-center gap-2">
        <span class="flex h-3 w-3 relative">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
        </span>
        <span class="text-xs font-bold text-amber-200" id="stickyUnsavedCountText">आपने बदलाव किए हैं</span>
    </div>
    <div class="h-4 w-px bg-white/20"></div>
    <button type="button" onclick="saveAllTableRowsCosting()" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-black text-xs shadow-lg transition flex items-center gap-2 active:scale-95 cursor-pointer">
        <i class="fa-solid fa-floppy-disk text-amber-200"></i>
        <span>💾 सभी बदलाव एक साथ सेव करें (Save All)</span>
    </button>
</div>

@include('components.ai-copilot-widget')
</body>
</html>
<script>
function unstageSelectedChannelRows() {
    const chks = document.querySelectorAll('.pending-prod-chk:checked');
    const ids = Array.from(chks).map(c => c.value).filter(Boolean);
    if (ids.length === 0) {
        alert('कृपया कम से कम एक प्रोडक्ट टिक करें।');
        return;
    }
    if (!confirm(`क्या आप चुने हुए ${ids.length} प्रोडक्ट्स को Amazon/Meesho/Flipkart कतार से हटाना चाहते हैं?\n(ये प्रोडक्ट्स आपकी दुकान के मुख्य कैटलॉग में 100% सुरक्षित रहेंगे)`)) {
        return;
    }
    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}';
    fetch("{{ route('seller.channels.unstageBulk') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ product_ids: ids })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            ids.forEach(id => {
                const card = document.getElementById(`pending-card-box-${id}`);
                if (card) card.remove();
                const row = document.getElementById(`table-row-${id}`);
                if (row) row.remove();
            });
            window.location.reload();
        } else {
            alert(data.message || 'त्रुटि हुई');
        }
    })
    .catch(err => {
        console.error(err);
        window.location.reload();
    });
}

function downloadSelectedChannelRows() {
    const chks = document.querySelectorAll('.pending-prod-chk:checked');
    const ids = Array.from(chks).map(c => c.value).filter(Boolean);
    if (ids.length === 0) {
        alert('कृपया कम से कम एक प्रोडक्ट टिक करें।');
        return;
    }
    window.location.href = "{{ route('seller.channels.export_sheet') }}?items=" + ids.join(',');
}

document.addEventListener('change', function(e) {
    if (e.target && e.target.classList.contains('pending-prod-chk')) {
        const count = document.querySelectorAll('.pending-prod-chk:checked').length;
        const btn = document.getElementById('btnDownloadSelectedChannel');
        const btnText = document.getElementById('btnDownloadSelectedChannelText');
        const btnDel = document.getElementById('btnUnstageSelectedChannel');
        const btnDelText = document.getElementById('btnUnstageSelectedChannelText');
        if (count > 0) {
            if (btn) {
                btn.classList.remove('hidden');
                if (btnText) btnText.textContent = `चुने हुए (${count}) डाउनलोड`;
            }
            if (btnDel) {
                btnDel.classList.remove('hidden');
                if (btnDelText) btnDelText.textContent = `मार्केटप्लेस से हटाएं (${count})`;
            }
        } else {
            if (btn) btn.classList.add('hidden');
            if (btnDel) btnDel.classList.add('hidden');
        }
    }
});
</script>


