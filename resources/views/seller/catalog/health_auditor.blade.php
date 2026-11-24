<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI कैटलॉग स्वास्थ्य एवं क्वालिटी स्कोर (100-Point Audit) - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .radial-progress {
            background: radial-gradient(closest-side, white 79%, transparent 80% 100%),
                        conic-gradient(#3b82f6 calc(var(--value) * 1%), #e2e8f0 0);
        }
        .radial-progress-success {
            background: radial-gradient(closest-side, white 79%, transparent 80% 100%),
                        conic-gradient(#10b981 calc(var(--value) * 1%), #e2e8f0 0);
        }
        .radial-progress-warning {
            background: radial-gradient(closest-side, white 79%, transparent 80% 100%),
                        conic-gradient(#f59e0b calc(var(--value) * 1%), #e2e8f0 0);
        }
        .radial-progress-danger {
            background: radial-gradient(closest-side, white 79%, transparent 80% 100%),
                        conic-gradient(#ef4444 calc(var(--value) * 1%), #e2e8f0 0);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.dashboard') }}" class="h-9 w-9 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-base shadow-sm">
                        V
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-black text-gray-900 text-base tracking-tight">VyaparIndia</span>
                            <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-200 flex items-center gap-1.5">
                                <i class="fa-solid fa-brain text-indigo-600"></i> AI Catalog Health Engine
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <a href="{{ route('seller.catalog.auditor') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-file-excel text-emerald-600"></i>
                        <span class="hidden sm:inline">मास्टर शीट ऑडिट</span>
                    </a>
                    <a href="{{ route('seller.products.index') }}" class="px-3.5 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-extrabold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-boxes-stacked"></i>
                        <span>कैटलॉग प्रोडक्ट्स</span>
                    </a>
                    <a href="{{ route('seller.dashboard') }}" class="text-xs font-bold text-gray-600 hover:text-blue-600 transition ml-2">
                        डैशबोर्ड
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2 shadow-xs">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @php
            $score = $audit->health_score ?? 0;
            $breakdown = $audit->breakdown ?? [];
            $stats = $audit->stats ?? [];
            $recommendations = $audit->recommendations ?? [];
            $products = $audit->products_audit ?? [];

            $scoreClass = 'radial-progress-danger text-rose-600';
            $badgeColor = 'bg-rose-500/20 text-rose-300 border-rose-500/30';
            $tierName = 'सुधार आवश्यक (Critical Attention)';

            if ($score >= 85) {
                $scoreClass = 'radial-progress-success text-emerald-600';
                $badgeColor = 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30';
                $tierName = '💎 A+ एक्सीलेंट स्टोर (Top Quality)';
            } elseif ($score >= 70) {
                $scoreClass = 'radial-progress text-blue-600';
                $badgeColor = 'bg-blue-500/20 text-blue-300 border-blue-500/30';
                $tierName = '🟢 A गुड क्वालिटी स्टोर';
            } elseif ($score >= 50) {
                $scoreClass = 'radial-progress-warning text-amber-500';
                $badgeColor = 'bg-amber-500/20 text-amber-300 border-amber-500/30';
                $tierName = '🟡 B मध्यम क्वालिटी (सुधार संभव)';
            }
        @endphp

        <!-- Hero Section: Overall Score and Health Banner -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 relative z-10">
                <div class="space-y-3 max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full {{ $badgeColor }} text-xs font-black uppercase tracking-wider border">
                        <i class="fa-solid fa-shield-halved"></i> {{ $tierName }}
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                        दुकान कैटलॉग AI स्वास्थ्य एवं क्वालिटी स्कोर
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        AI इंजन ने आपके हर एक प्रोडक्ट का विश्लेषण किया है—स्टॉक (Quantity) डला है या नहीं, सेलिंग रेट व MRP सही है या शून्य, असली फोटो मौजूद है या गायब। नीचे दिए गए 100/100 एक्शन प्लान से आप स्टोर को टॉप लेवल पर पहुंचा सकते हैं।
                    </p>

                    <div class="flex flex-wrap items-center gap-3 pt-2 text-xs text-slate-300">
                        <span class="bg-white/10 px-3 py-1 rounded-lg border border-white/10 flex items-center gap-1.5">
                            <i class="fa-solid fa-boxes-stacked text-blue-400"></i>
                            <span>कुल प्रोडक्ट्स: <strong>{{ $stats['total_products'] ?? 0 }}</strong></span>
                        </span>
                        <span class="bg-white/10 px-3 py-1 rounded-lg border border-white/10 flex items-center gap-1.5">
                            <i class="fa-solid fa-cubes text-emerald-400"></i>
                            <span>स्टॉक यूनिट्स: <strong>{{ number_format($stats['total_stock_units'] ?? 0) }} Pcs</strong></span>
                        </span>
                        <span class="bg-white/10 px-3 py-1 rounded-lg border border-white/10 flex items-center gap-1.5">
                            <i class="fa-solid fa-indian-rupee-sign text-amber-400"></i>
                            <span>स्टॉक वैल्यू: <strong>₹{{ number_format($stats['total_stock_value'] ?? 0, 2) }}</strong></span>
                        </span>
                        @if(!empty($audit->audited_at))
                            <span class="bg-white/10 px-3 py-1 rounded-lg border border-white/10 flex items-center gap-1.5">
                                <i class="fa-solid fa-clock text-slate-400"></i>
                                <span>अंतिम AI ऑडिट: <strong>{{ $audit->audited_at->diffForHumans() }}</strong></span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Right Side: Circular Score Gauge & Manual Run Button -->
                <div class="flex flex-col sm:flex-row items-center gap-6 shrink-0 bg-white/5 border border-white/10 p-6 rounded-2xl backdrop-blur-xs">
                    <div class="relative w-32 h-32 rounded-full flex items-center justify-center {{ $scoreClass }} shadow-lg" style="--value: {{ $score }};">
                        <div class="text-center">
                            <div class="text-3xl font-black text-slate-900 leading-none">{{ $score }}</div>
                            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mt-0.5">/ 100 अंक</div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2.5 text-center sm:text-left">
                        <div class="text-xs font-bold text-slate-200">
                            {{ $score >= 85 ? 'स्टोर बेहतरीन स्थिति में है!' : '100 तक पहुँचने के लिए ' . (100 - $score) . ' अंक और चाहिए' }}
                        </div>
                        <form action="{{ route('seller.catalog.run_health_audit') }}" method="POST" id="reAuditForm">
                            @csrf
                            <button type="submit" id="btnReAudit" class="w-full px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-xs transition flex items-center justify-center gap-2 shadow-md hover:scale-102 active:scale-98">
                                <i class="fa-solid fa-bolt text-amber-300"></i>
                                <span>अभी AI ऑडिट चलाएं</span>
                            </button>
                        </form>
                        <span class="text-[10px] text-slate-400">हर प्रोफाइल के लिए डेडिकेटेड ऑटो रन</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4-Pillar Score Cards (Each out of 25) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- 1. Visuals & Images -->
            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-black text-gray-900">{{ $breakdown['images_score'] ?? 0 }}</span>
                        <span class="text-xs font-bold text-gray-400">/ 25</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">फोटो व तस्वीरें (Images)</h3>
                    <p class="text-[11px] text-gray-500 mt-0.5">असली हाई-क्वालिटी फोटो की मौजूदगी</p>
                </div>
                <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-blue-600 h-full rounded-full" style="width: {{ (($breakdown['images_score'] ?? 0) / 25) * 100 }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[11px] font-bold pt-1 border-t border-gray-100">
                    <span class="text-emerald-700">{{ $stats['multi_images_count'] ?? 0 }} मल्टी फोटो</span>
                    <span class="{{ ($stats['missing_images_count'] ?? 0) > 0 ? 'text-rose-600' : 'text-gray-400' }}">
                        {{ $stats['missing_images_count'] ?? 0 }} बिना फोटो
                    </span>
                </div>
            </div>

            <!-- 2. Stock & Quantity -->
            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-cubes-stacked"></i>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-black text-gray-900">{{ $breakdown['stock_score'] ?? 0 }}</span>
                        <span class="text-xs font-bold text-gray-400">/ 25</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">स्टॉक क्वांटिटी (Quantity)</h3>
                    <p class="text-[11px] text-gray-500 mt-0.5">उपलब्ध पीस व इन्वेंट्री ट्रैकिंग</p>
                </div>
                <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-emerald-600 h-full rounded-full" style="width: {{ (($breakdown['stock_score'] ?? 0) / 25) * 100 }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[11px] font-bold pt-1 border-t border-gray-100">
                    <span class="text-emerald-700">{{ $stats['in_stock_count'] ?? 0 }} इन-स्टॉक</span>
                    <span class="{{ ($stats['out_of_stock_count'] ?? 0) > 0 ? 'text-rose-600' : 'text-gray-400' }}">
                        {{ $stats['out_of_stock_count'] ?? 0 }} शून्य (0) स्टॉक
                    </span>
                </div>
            </div>

            <!-- 3. Pricing & Rates -->
            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-tag"></i>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-black text-gray-900">{{ $breakdown['pricing_score'] ?? 0 }}</span>
                        <span class="text-xs font-bold text-gray-400">/ 25</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">भाव व डिस्काउंट (Pricing)</h3>
                    <p class="text-[11px] text-gray-500 mt-0.5">सेलिंग रेट और MRP की स्पष्टता</p>
                </div>
                <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-amber-500 h-full rounded-full" style="width: {{ (($breakdown['pricing_score'] ?? 0) / 25) * 100 }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[11px] font-bold pt-1 border-t border-gray-100">
                    <span class="text-gray-600">कुल ₹{{ number_format($stats['total_stock_value'] ?? 0) }}</span>
                    <span class="{{ ($stats['missing_prices_count'] ?? 0) > 0 ? 'text-rose-600' : 'text-gray-400' }}">
                        {{ $stats['missing_prices_count'] ?? 0 }} शून्य (₹0) भाव
                    </span>
                </div>
            </div>

            <!-- 4. Content & Specifications -->
            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-black text-gray-900">{{ $breakdown['content_score'] ?? 0 }}</span>
                        <span class="text-xs font-bold text-gray-400">/ 25</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">विवरण व कैटेगरी (Listing)</h3>
                    <p class="text-[11px] text-gray-500 mt-0.5">नाम, साइज़, विवरण और ब्रांड डिटेल्स</p>
                </div>
                <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-purple-600 h-full rounded-full" style="width: {{ (($breakdown['content_score'] ?? 0) / 25) * 100 }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[11px] font-bold pt-1 border-t border-gray-100">
                    <span class="text-purple-700">प्रोफाइल: {{ $breakdown['profile_score'] ?? 0 }}/100</span>
                    <span class="text-emerald-600">सर्च रेडी</span>
                </div>
            </div>
        </div>

        <!-- 🎯 100/100 Roadmap: How to get full points -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-indigo-100 shadow-sm space-y-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-lg shadow-sm">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-gray-900">🎯 100/100 अंक पाने का AI एक्शन प्लान</h2>
                        <p class="text-xs text-gray-500">इन जरूरी सुधारों को पूरा करते ही आपकी दुकान को 100 अंक मिलेंगे</p>
                    </div>
                </div>
                <span class="hidden sm:inline-flex text-xs font-bold px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full border border-indigo-200">
                    {{ count($recommendations) }} सुझाव उपलब्ध
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($recommendations as $rec)
                    @php
                        $border = 'border-gray-200';
                        $badgeBg = 'bg-gray-100 text-gray-700';
                        if (($rec['priority'] ?? '') === 'critical') {
                            $border = 'border-rose-200 bg-rose-50/30';
                            $badgeBg = 'bg-rose-100 text-rose-800';
                        } elseif (($rec['priority'] ?? '') === 'medium') {
                            $border = 'border-amber-200 bg-amber-50/30';
                            $badgeBg = 'bg-amber-100 text-amber-800';
                        } elseif (($rec['priority'] ?? '') === 'success') {
                            $border = 'border-emerald-200 bg-emerald-50/30';
                            $badgeBg = 'bg-emerald-100 text-emerald-800';
                        }
                    @endphp
                    <div class="rounded-2xl p-4.5 border {{ $border }} flex flex-col justify-between space-y-3 shadow-2xs">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full {{ $badgeBg }}">
                                    {{ $rec['priority'] ?? 'General' }}
                                </span>
                                @if(($rec['points_gain'] ?? 0) > 0)
                                    <span class="text-xs font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                        +{{ $rec['points_gain'] }} अंक बढ़ेंगे
                                    </span>
                                @endif
                            </div>
                            <h4 class="text-sm font-black text-gray-900 leading-snug">{{ $rec['title'] }}</h4>
                            <p class="text-xs text-gray-600 leading-relaxed">{{ $rec['description'] }}</p>
                        </div>
                        <a href="{{ $rec['action_url'] ?? '#' }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-gray-900 hover:bg-black text-white text-xs font-bold transition shadow-xs">
                            <span>{{ $rec['action_label'] ?? 'ठीक करें' }}</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                @empty
                    <div class="col-span-full p-6 text-center text-gray-500 text-xs font-bold bg-gray-50 rounded-2xl">
                        शानदार! कोई अधूरा काम नहीं है। आपकी दुकान पूरी तरह अपडेट है।
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Filterable & Searchable Product Inspection List -->
        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden space-y-4 p-5 sm:p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                        <span>एक-एक प्रोडक्ट का AI निरीक्षण (Item-by-Item Audit)</span>
                        <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2.5 py-0.5 rounded-full" id="filterCount">
                            कुल {{ count($products) }}
                        </span>
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">हर प्रोडक्ट की मात्रा, दर, फोटो और स्कोर की सूची</p>
                </div>

                <!-- Search and Filter Tabs -->
                <div class="flex flex-wrap items-center gap-2">
                    <div class="relative">
                        <input type="text" id="searchInput" onkeyup="filterProducts()" placeholder="प्रोडक्ट खोजें..." class="w-48 sm:w-64 pl-8 pr-3 py-1.5 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                    </div>

                    <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl text-xs font-bold">
                        <button type="button" onclick="setTab('all')" class="tab-btn active px-3 py-1 rounded-lg bg-white shadow-2xs text-gray-900" data-tab="all">
                            सभी ({{ count($products) }})
                        </button>
                        <button type="button" onclick="setTab('out_of_stock')" class="tab-btn px-3 py-1 rounded-lg text-gray-600 hover:text-gray-900" data-tab="out_of_stock">
                            0 स्टॉक ({{ $stats['out_of_stock_count'] ?? 0 }})
                        </button>
                        <button type="button" onclick="setTab('no_image')" class="tab-btn px-3 py-1 rounded-lg text-gray-600 hover:text-gray-900" data-tab="no_image">
                            बिना फोटो ({{ $stats['missing_images_count'] ?? 0 }})
                        </button>
                        <button type="button" onclick="setTab('zero_price')" class="tab-btn px-3 py-1 rounded-lg text-gray-600 hover:text-gray-900" data-tab="zero_price">
                            ₹0 भाव ({{ $stats['missing_prices_count'] ?? 0 }})
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs" id="productTable">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 font-extrabold uppercase tracking-wider border-y border-gray-200 text-[10px]">
                            <th class="py-3 px-4">फोटो</th>
                            <th class="py-3 px-4">प्रोडक्ट व कोड</th>
                            <th class="py-3 px-4">क्वांटिटी / स्टॉक</th>
                            <th class="py-3 px-4">सेलिंग रेट व MRP</th>
                            <th class="py-3 px-4">स्टॉक वैल्यू</th>
                            <th class="py-3 px-4 text-center">क्वालिटी स्कोर</th>
                            <th class="py-3 px-4">AI कमियां / सुझाव</th>
                            <th class="py-3 px-4 text-right">एक्शन</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($products as $p)
                            @php
                                $stockStatus = $p['stock_status'] ?? 'untracked';
                                $imgCount = $p['image_count'] ?? 0;
                                $price = (float)($p['selling_price'] ?? 0);
                                $prodScore = (int)($p['total_score'] ?? 0);

                                $scoreBadge = 'bg-emerald-100 text-emerald-800';
                                if ($prodScore < 50) $scoreBadge = 'bg-rose-100 text-rose-800';
                                elseif ($prodScore < 75) $scoreBadge = 'bg-amber-100 text-amber-800';
                            @endphp
                            <tr class="product-row hover:bg-slate-50/70 transition"
                                data-name="{{ strtolower($p['name'] ?? '') }}"
                                data-sku="{{ strtolower($p['sku'] ?? '') }}"
                                data-stock="{{ $stockStatus }}"
                                data-images="{{ $imgCount }}"
                                data-price="{{ $price }}">
                                
                                <!-- Photo -->
                                <td class="py-3 px-4 shrink-0">
                                    <div class="relative w-12 h-12 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center shrink-0">
                                        @if(!empty($p['primary_image_url']))
                                            <img src="{{ $p['primary_image_url'] }}" alt="{{ $p['name'] }}" class="w-full h-full object-contain" onerror="this.src='/images/placeholder-product.png'">
                                        @else
                                            <i class="fa-solid fa-image text-gray-300 text-base"></i>
                                        @endif

                                        <span class="absolute bottom-0 inset-x-0 text-[8px] font-bold text-center {{ $imgCount > 0 ? 'bg-black/60 text-white' : 'bg-rose-600 text-white' }} py-0.2">
                                            {{ $imgCount > 0 ? $imgCount . ' फोटो' : 'गायब' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Product & Code -->
                                <td class="py-3 px-4 max-w-xs">
                                    <div class="font-bold text-gray-900 truncate" title="{{ $p['name'] }}">
                                        {{ $p['name'] }}
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5 text-[11px] text-gray-500">
                                        <span class="font-mono bg-gray-100 px-1.5 py-0.2 rounded text-[10px]">SKU: {{ $p['sku'] }}</span>
                                        <span>{{ $p['category'] ?? 'General' }}</span>
                                    </div>
                                </td>

                                <!-- Quantity / Stock -->
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-1.5 font-bold">
                                        @if($stockStatus === 'in_stock')
                                            <span class="inline-flex items-center gap-1 text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                                <i class="fa-solid fa-circle text-[6px]"></i>
                                                {{ $p['stock_quantity'] }} Pcs उपलब्ध
                                            </span>
                                        @elseif($stockStatus === 'low_stock')
                                            <span class="inline-flex items-center gap-1 text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                                                <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                                {{ $p['stock_quantity'] }} Pcs (कम स्टॉक)
                                            </span>
                                        @elseif($stockStatus === 'out_of_stock')
                                            <span class="inline-flex items-center gap-1 text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200">
                                                <i class="fa-solid fa-circle-xmark text-[10px]"></i>
                                                0 स्टॉक (Out of Stock)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">
                                                अनट्रैक्ड (खाली)
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Pricing & MRP -->
                                <td class="py-3 px-4">
                                    <div class="font-black text-gray-900 {{ $price <= 0 ? 'text-rose-600' : '' }}">
                                        ₹{{ number_format($price, 2) }}
                                    </div>
                                    @if(($p['mrp'] ?? 0) > 0)
                                        <div class="text-[10px] text-gray-400 flex items-center gap-1.5 mt-0.5">
                                            <span>MRP: <del>₹{{ number_format($p['mrp'], 2) }}</del></span>
                                            @if(($p['discount_percent'] ?? 0) > 0)
                                                <span class="text-emerald-600 font-bold">{{ $p['discount_percent'] }}% छूट</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <!-- Stock Value -->
                                <td class="py-3 px-4 font-bold text-gray-700">
                                    ₹{{ number_format($price * ($p['stock_quantity'] ?? 0), 2) }}
                                </td>

                                <!-- Score -->
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center justify-center font-black px-2.5 py-1 rounded-full text-xs {{ $scoreBadge }}">
                                        {{ $prodScore }}/100
                                    </span>
                                </td>

                                <!-- Issues Badges -->
                                <td class="py-3 px-4">
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        @forelse($p['issues'] ?? [] as $issue)
                                            @php
                                                $issueClass = 'bg-gray-100 text-gray-700';
                                                if ($issue['type'] === 'danger') $issueClass = 'bg-rose-50 text-rose-700 border border-rose-200';
                                                elseif ($issue['type'] === 'warning') $issueClass = 'bg-amber-50 text-amber-700 border border-amber-200';
                                                elseif ($issue['type'] === 'info') $issueClass = 'bg-blue-50 text-blue-700 border border-blue-200';
                                            @endphp
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md {{ $issueClass }}">
                                                {{ $issue['text'] }}
                                            </span>
                                        @empty
                                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                                ✅ 100% परफेक्ट
                                            </span>
                                        @endforelse
                                    </div>
                                </td>

                                <!-- Action -->
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ $p['edit_url'] ?? '#' }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-700 font-bold transition text-xs">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>एडिट</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-8 text-gray-400 text-xs font-bold">
                                    कैटलॉग में कोई प्रोडक्ट नहीं मिला।
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Client-side Search and Filter Script -->
    <script>
        let currentTab = 'all';

        function setTab(tab) {
            currentTab = tab;
            document.querySelectorAll('.tab-btn').forEach(btn => {
                if (btn.getAttribute('data-tab') === tab) {
                    btn.classList.add('bg-white', 'shadow-2xs', 'text-gray-900');
                    btn.classList.remove('text-gray-600');
                } else {
                    btn.classList.remove('bg-white', 'shadow-2xs', 'text-gray-900');
                    btn.classList.add('text-gray-600');
                }
            });
            filterProducts();
        }

        function filterProducts() {
            const query = (document.getElementById('searchInput').value || '').toLowerCase().trim();
            const rows = document.querySelectorAll('.product-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const sku = row.getAttribute('data-sku') || '';
                const stock = row.getAttribute('data-stock') || '';
                const images = parseInt(row.getAttribute('data-images') || '0', 10);
                const price = parseFloat(row.getAttribute('data-price') || '0');

                // Search match
                const matchSearch = !query || name.includes(query) || sku.includes(query);

                // Tab match
                let matchTab = true;
                if (currentTab === 'out_of_stock') {
                    matchTab = (stock === 'out_of_stock' || stock === 'untracked');
                } else if (currentTab === 'no_image') {
                    matchTab = (images === 0);
                } else if (currentTab === 'zero_price') {
                    matchTab = (price <= 0);
                }

                if (matchSearch && matchTab) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('filterCount').textContent = 'दिख रहे हैं: ' + visibleCount;
        }

        // Animated button on form submit
        document.getElementById('reAuditForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('btnReAudit');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>AI स्कैन कर रहा है...</span>';
        });
    </script>
</body>
</html>
