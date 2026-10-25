<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Marketplace Channel Hub (Amazon, Meesho, Blinkit, Flipkart) - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 text-gray-800 antialiased min-h-screen">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.dashboard') }}" class="h-9 w-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="font-black text-base sm:text-lg text-gray-900 leading-tight">मल्टी-मार्केटप्लेस सेलर हब</h1>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase">
                                API 24x7 Realtime Sync
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-500">Amazon, Meesho, Flipkart, Myntra & Blinkit पर 1-क्लिक में प्रोडक्ट्स लिस्ट, आर्डर व इन्वेंटरी सिंक करें</p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <a href="{{ route('seller.channels.inventory') }}" class="px-3.5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition flex items-center gap-1.5">
                        <i class="fa-solid fa-boxes-stacked text-indigo-600"></i>
                        <span>Omnichannel Stock</span>
                    </a>
                    <a href="{{ route('seller.orders.index') }}" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-gray-950 font-black text-xs shadow-xs transition flex items-center gap-1.5">
                        <i class="fa-solid fa-truck-fast"></i>
                        <span>सभी आर्डर्स देखें</span>
                    </a>
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

                <!-- 2 Main Workspace Tabs (Published vs Pending) -->
                <div class="inline-flex items-center bg-gray-100 p-1.5 rounded-2xl border border-gray-200 self-start sm:self-auto">
                    <button type="button" onclick="switchMarketplaceTab('published')" id="btnTabPublishedMarketplaces" class="px-4 py-2 rounded-xl text-xs font-black transition flex items-center gap-2 bg-white text-indigo-700 shadow-2xs cursor-pointer">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        <span>🟢 पब्लिश किए गए (Published)</span>
                        <span class="px-2 py-0.2 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-extrabold">{{ $allListings->count() }}</span>
                    </button>
                    <button type="button" onclick="switchMarketplaceTab('pending')" id="btnTabPendingMarketplaces" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 text-gray-600 hover:text-gray-900 cursor-pointer">
                        <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                        <span>🟡 पेंडिंग प्रोडक्ट्स (Pending)</span>
                        <span class="px-2 py-0.2 rounded-full text-[10px] bg-amber-100 text-amber-800 font-extrabold">{{ count($pendingProducts ?? []) }}</span>
                    </button>
                </div>
            </div>

            <!-- TAB 1: 🟢 PUBLISHED MARKETPLACES VIEW -->
            <div id="viewPublishedMarketplaces" class="space-y-6">
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
                    <div class="p-8 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-300 text-xs text-gray-500 space-y-2">
                        <i class="fa-brands fa-amazon text-gray-300 text-3xl mb-1 block"></i>
                        <p>अभी किसी मार्केटप्लेस पर सीधे लिस्टिंग नहीं हुई है। नीचे पेंडिंग प्रोडक्ट्स को टिक करके तुरंत पब्लिश करें।</p>
                    </div>
                @endif
            </div>

            <!-- TAB 2: 🟡 PENDING TO PUBLISH VIEW -->
            <div id="viewPendingMarketplaces" class="hidden space-y-5">
                <form action="{{ route('seller.channels.bulkPublish') }}" method="POST">
                    @csrf
                    <!-- Action Bar -->
                    <div class="p-4 bg-amber-50/70 border border-amber-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" id="selectAllPendingChk" onchange="toggleSelectAllPending(this.checked)" class="rounded text-indigo-600 h-4 w-4 cursor-pointer">
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

                            <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs shadow-md transition active:scale-95 cursor-pointer flex items-center gap-1.5">
                                <i class="fa-solid fa-cloud-arrow-up text-amber-300"></i>
                                <span>🚀 1-क्लिक में लाइव पब्लिश करें</span>
                            </button>
                        </div>
                    </div>

                    <!-- Pending Products List -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                        @forelse($pendingProducts ?? [] as $p)
                            <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-2xs hover:border-indigo-300 transition flex items-start gap-3">
                                <input type="checkbox" name="product_ids[]" value="{{ $p->id }}" class="pending-prod-chk rounded text-indigo-600 h-4 w-4 mt-1 cursor-pointer">
                                <div class="h-14 w-14 rounded-xl bg-gray-50 border border-gray-200 overflow-hidden flex items-center justify-center shrink-0 p-1">
                                    @if($p->image_url)
                                        <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="h-full w-full object-contain">
                                    @else
                                        <i class="fa-solid fa-cube text-gray-300 text-lg"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="text-[9px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md">
                                        {{ $p->category->name ?? 'General' }}
                                    </span>
                                    <h4 class="font-black text-xs text-gray-900 truncate mt-1" title="{{ $p->name }}">
                                        {{ $p->name }}
                                    </h4>
                                    <div class="flex items-center justify-between text-xs mt-1">
                                        <span class="font-mono font-bold text-gray-900">₹{{ number_format($p->price, 2) }}</span>
                                        <span class="text-[10px] text-gray-400 font-mono">SKU: {{ $p->sku ?: 'STD' }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full p-8 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-300 text-xs text-gray-500">
                                <i class="fa-solid fa-circle-check text-emerald-500 text-2xl mb-1 block"></i>
                                सभी प्रोडक्ट्स सभी मार्केट्स पर पब्लिश हो चुके हैं। कोई नया पेंडिंग प्रोडक्ट नहीं है।
                            </div>
                        @endforelse
                    </div>
                </form>
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
                                <span class="inline-block mt-1 text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">
                                    पिकअप SLA: {{ $info['sla'] }}
                                </span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-gray-100 flex items-center gap-2">
                            @if($connected)
                                <button type="button" onclick="openConnectModal('{{ $channelKey }}', '{{ $info['name'] }}', '{{ $connected->store_name }}', '{{ $connected->store_url }}', '{{ $connected->api_key }}')" class="flex-1 py-2 px-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-extrabold text-xs transition">
                                    API सेटिंग्स बदलें
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

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">सेलर / स्टोर का नाम <span class="text-rose-500">*</span></label>
                        <input type="text" name="store_name" id="store-name-input" required placeholder="उदा. My Amazon Store / Meesho Shop" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-semibold">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">सेलर आईडी / Merchant Token / Store URL</label>
                        <input type="text" name="store_url" id="store-url-input" placeholder="उदा. A2XYZEXAMPLE या https://sellercentral.amazon.in" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">API Key / Client ID / LWA App ID</label>
                        <input type="text" name="api_key" id="api-key-input" placeholder="amzn.mws.xxxx या meesho_api_key_xxx" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-mono">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">API Secret / Refresh Token / LWA Secret</label>
                        <input type="password" name="access_token" id="access-token-input" placeholder="••••••••••••••••••••••••" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-mono">
                    </div>

                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-black text-xs shadow-md transition active:scale-95 cursor-pointer">
                        🔒 सुरक्षित सेव करें & ऑटो-सिंक सक्रिय करें
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Switcher: Published Marketplaces vs Pending
        function switchMarketplaceTab(tab) {
            const pubView = document.getElementById('viewPublishedMarketplaces');
            const penView = document.getElementById('viewPendingMarketplaces');
            const btnPub = document.getElementById('btnTabPublishedMarketplaces');
            const btnPen = document.getElementById('btnTabPendingMarketplaces');

            if (tab === 'published') {
                pubView.classList.remove('hidden');
                penView.classList.add('hidden');
                btnPub.className = "px-4 py-2 rounded-xl text-xs font-black transition flex items-center gap-2 bg-white text-indigo-700 shadow-2xs cursor-pointer";
                btnPen.className = "px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 text-gray-600 hover:text-gray-900 cursor-pointer";
            } else {
                pubView.classList.add('hidden');
                penView.classList.remove('hidden');
                btnPen.className = "px-4 py-2 rounded-xl text-xs font-black transition flex items-center gap-2 bg-white text-amber-800 shadow-2xs cursor-pointer";
                btnPub.className = "px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 text-gray-600 hover:text-gray-900 cursor-pointer";
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

        // Select All Pending Checkbox
        function toggleSelectAllPending(checked) {
            document.querySelectorAll('.pending-prod-chk').forEach(chk => {
                chk.checked = checked;
            });
        }

        // Open API Connect Modal
        function openConnectModal(channelKey, channelTitle, storeName = '', storeUrl = '', apiKey = '') {
            document.getElementById('channel-name-input').value = channelKey;
            document.getElementById('modal-title').innerText = channelTitle + ' API Connect';
            document.getElementById('store-name-input').value = storeName || (channelTitle + ' Store');
            document.getElementById('store-url-input').value = storeUrl || '';
            document.getElementById('api-key-input').value = apiKey || '';
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
</body>
</html>
