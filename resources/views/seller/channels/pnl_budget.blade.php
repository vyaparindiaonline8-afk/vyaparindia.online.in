<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>मासिक P&L व बजट विचलन रिपोर्ट (Monthly P&L & Budget Variance) - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 text-gray-800 antialiased min-h-screen">

    <!-- Top Navigation Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.channels.index') }}" class="h-10 w-10 rounded-2xl bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition shadow-2xs shrink-0" title="मल्टी-चैनल हब पर वापस जाएं">
                        <i class="fa-solid fa-arrow-left text-sm"></i>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="font-black text-lg sm:text-xl text-gray-900 leading-tight">📊 मासिक P&L व बजट विचलन रिपोर्ट</h1>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-wide">
                                Risk & Variance Intelligence
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">ऐड्स, RTO बफ़र व पैकेजिंग बजट बनाम वास्तविक खर्च का सटीक हिसाब व अगले महीने का बजट प्लानर</p>
                    </div>
                </div>

                <!-- Month & Year Selector -->
                <form method="GET" action="{{ route('seller.channels.pnl_budget') }}" class="flex items-center gap-2">
                    <select name="month" onchange="this.form.submit()" class="px-3 py-1.5 bg-white border border-gray-300 rounded-xl text-xs font-bold text-gray-800 shadow-2xs outline-none cursor-pointer">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                    <select name="year" onchange="this.form.submit()" class="px-3 py-1.5 bg-white border border-gray-300 rounded-xl text-xs font-bold text-gray-800 shadow-2xs outline-none cursor-pointer">
                        @for($y = now()->year; $y >= now()->year - 2; $y--)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <a href="{{ route('seller.channels.index') }}" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition flex items-center gap-1 shadow-2xs">
                        <i class="fa-solid fa-table-cells"></i>
                        <span>कैटलॉग हब</span>
                    </a>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        @if($isDemoPreview)
            <!-- Demo Preview Notice -->
            <div class="p-3 bg-amber-50 border border-amber-300 rounded-2xl flex items-center justify-between text-xs text-amber-950 font-bold">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-amber-600 text-base"></i>
                    <span>डेमो सिमुलेशन प्रीव्यू (30 ऑर्डर्स बैच): आपके द्वारा बताए गए उदाहरण (30 प्रोडक्ट, ₹10 ऐड्स बजट = ₹300 vs वास्तविक खर्च ₹450) के आधार पर लाइव विश्लेषण दिखाया जा रहा है।</span>
                </div>
                <span class="px-2 py-0.5 rounded bg-amber-200 text-amber-900 text-[10px]">उदाहरणात्मक डेटा</span>
            </div>
        @endif

        <!-- 4 CORE VARIANCE KPI CARDS (BUDGET VS ACTUAL) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Ads Budget vs Actual (User's specific case: Budget ₹300 vs Spent ₹450) -->
            <div class="bg-white p-4 rounded-3xl border {{ $adsVariance < 0 ? 'border-rose-300 shadow-rose-100' : 'border-emerald-300' }} shadow-sm space-y-3 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-gray-500 uppercase flex items-center gap-1.5">
                            <i class="fa-solid fa-rectangle-ad text-purple-600"></i>
                            <span>📢 ऐड्स व विज्ञापन खर्च</span>
                        </span>
                        @if($adsVariance < 0)
                            <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-300 text-[10px] font-black animate-pulse">
                                🚨 बजट ओवर-रन!
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 text-[10px] font-black">
                                ✓ बजट में
                            </span>
                        @endif
                    </div>

                    <div class="mt-2.5 flex items-baseline justify-between font-mono">
                        <div>
                            <span class="text-[10px] text-gray-400 block font-bold">वास्तविक खर्च (Actual):</span>
                            <span class="text-xl font-black text-rose-600">₹{{ number_format($actualAdsSpend, 2) }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-gray-400 block font-bold">बजट था ({{ $effectiveOrdersCount }} × ₹{{ $adsBudgetPerPc }}):</span>
                            <span class="text-sm font-bold text-gray-600 line-through">₹{{ number_format($totalAdsBudget, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Advisory Box -->
                <div class="p-2.5 rounded-2xl {{ $adsVariance < 0 ? 'bg-rose-50 border border-rose-200 text-rose-950' : 'bg-emerald-50 border border-emerald-200 text-emerald-950' }} text-[11px] leading-tight space-y-1">
                    <div class="flex items-center justify-between font-bold">
                        <span>विचलन (Variance):</span>
                        <span class="font-mono {{ $adsVariance < 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                            {{ $adsVariance < 0 ? '-' : '+' }}₹{{ number_format(abs($adsVariance), 2) }}
                        </span>
                    </div>
                    <p class="text-[10px]">
                        @if($adsVariance < 0)
                            👉 <strong>सीख व सुझाव:</strong> ऐड्स में ₹{{ abs($adsVariance) }} ज्यादा खर्च हुए (प्रति पीस ₹{{ $recommendedAdsPerPc }} लगा)। <strong>अगले महीने प्रति पीस ऐड्स बजट ₹{{ $recommendedAdsPerPc }} रखें</strong> या बिड 33% घटाएं।
                        @else
                            👉 <strong>बचत:</strong> ऐड्स में ₹{{ $adsVariance }} की बचत हुई जो आपके RTO बफ़र को सहारा देगी।
                        @endif
                    </p>
                </div>
            </div>

            <!-- 2. RTO Reserve vs Actual Drain -->
            <div class="bg-white p-4 rounded-3xl border {{ $rtoVariance < 0 ? 'border-rose-300' : 'border-indigo-300' }} shadow-sm space-y-3 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-gray-500 uppercase flex items-center gap-1.5">
                            <i class="fa-solid fa-shield-halved text-rose-600"></i>
                            <span>🔄 RTO सुरक्षा कोष</span>
                        </span>
                        <span class="px-2 py-0.5 rounded-full {{ $rtoVariance >= 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }} text-[10px] font-black">
                            {{ $effectiveRtoCount }} RTO पार्सल ({{ round(($effectiveRtoCount / $effectiveOrdersCount) * 100, 1) }}%)
                        </span>
                    </div>

                    <div class="mt-2.5 flex items-baseline justify-between font-mono">
                        <div>
                            <span class="text-[10px] text-gray-400 block font-bold">वास्तविक RTO चपत (₹145/pc):</span>
                            <span class="text-xl font-black text-rose-700">₹{{ number_format($actualRtoLoss, 2) }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-gray-400 block font-bold">RTO बफ़र पूल ({{ $effectiveOrdersCount }} × ₹{{ $rtoBudgetPerPc }}):</span>
                            <span class="text-sm font-bold text-gray-600">₹{{ number_format($totalRtoBudget, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Advisory Box -->
                <div class="p-2.5 rounded-2xl {{ $rtoVariance >= 0 ? 'bg-emerald-50 border border-emerald-200 text-emerald-950' : 'bg-rose-50 border border-rose-200 text-rose-950' }} text-[11px] leading-tight space-y-1">
                    <div class="flex items-center justify-between font-bold">
                        <span>बफ़र शेष (Surplus):</span>
                        <span class="font-mono {{ $rtoVariance >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ $rtoVariance >= 0 ? '+' : '-' }}₹{{ number_format(abs($rtoVariance), 2) }}
                        </span>
                    </div>
                    <p class="text-[10px]">
                        @if($rtoVariance >= 0)
                            ✓ <strong>सुरक्षित:</strong> RTO नुकसान (₹{{ $actualRtoLoss }}) बफ़र से कम रहा। ₹{{ $rtoVariance }} का अतिरिक्त लाभ आपकी जेब में सुरक्षित बचा!
                        @else
                            ⚠️ <strong>अलर्ट:</strong> RTO चपत बफ़र से ज्यादा हो गई, जिससे ₹{{ abs($rtoVariance) }} सीधे आपके मुनाफे में से कट गया।
                        @endif
                    </p>
                </div>
            </div>

            <!-- 3. Packaging Budget vs Actual -->
            <div class="bg-white p-4 rounded-3xl border border-amber-300 shadow-sm space-y-3 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-gray-500 uppercase flex items-center gap-1.5">
                            <i class="fa-solid fa-boxes-packing text-amber-600"></i>
                            <span>📦 पैकेजिंग खर्च</span>
                        </span>
                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black">
                            {{ $effectiveOrdersCount }} पार्सल
                        </span>
                    </div>

                    <div class="mt-2.5 flex items-baseline justify-between font-mono">
                        <div>
                            <span class="text-[10px] text-gray-400 block font-bold">वास्तविक खर्च (Actual):</span>
                            <span class="text-xl font-black text-gray-900">₹{{ number_format($actualPackSpend, 2) }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-gray-400 block font-bold">बजट था ({{ $effectiveOrdersCount }} × ₹{{ $packBudgetPerPc }}):</span>
                            <span class="text-sm font-bold text-gray-600">₹{{ number_format($totalPackBudget, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Advisory Box -->
                <div class="p-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 text-[11px] leading-tight space-y-1">
                    <div class="flex items-center justify-between font-bold">
                        <span>पैकिंग बचत:</span>
                        <span class="font-mono text-emerald-700">+₹{{ number_format($packVariance, 2) }}</span>
                    </div>
                    <p class="text-[10px]">
                        ✓ थोक में डिब्बे व टेप खरीदने पर प्रति पार्सल ₹{{ round($actualPackSpend / $effectiveOrdersCount, 1) }} खर्च आया। ₹{{ $packVariance }} की बचत हुई।
                    </p>
                </div>
            </div>

            <!-- 4. Net In-Hand Profit (Asli Shuddh Munafa) -->
            <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white p-4 rounded-3xl border border-indigo-500/40 shadow-md space-y-3 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-indigo-300 uppercase flex items-center gap-1.5">
                            <i class="fa-solid fa-wallet text-emerald-400"></i>
                            <span>शुद्ध जेब में मुनाफ़ा</span>
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] font-black font-mono">
                            {{ $netProfitMarginPct }}% Margin
                        </span>
                    </div>

                    <div class="mt-2.5">
                        <span class="text-[10px] text-slate-400 block font-bold">सब खर्चे कटने के बाद बचा:</span>
                        <span class="text-2xl font-black font-mono text-emerald-400">₹{{ number_format($netProfitInHand, 2) }}</span>
                    </div>
                </div>

                <div class="p-2 rounded-2xl bg-white/10 text-[10px] text-slate-300 flex items-center justify-between">
                    <span>लक्ष्य था (₹{{ $targetProfitPerPc }}/pc):</span>
                    <strong class="font-mono text-white">₹{{ number_format($totalTargetProfit, 2) }}</strong>
                </div>
            </div>
        </div>

        <!-- 2 COLUMNS: DETAILED P&L STATEMENT + NEXT MONTH BUDGET OPTIMIZER -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Col 1: Complete Monthly P&L Statement (7 cols) -->
            <div class="lg:col-span-7 bg-white rounded-3xl border border-gray-200 shadow-sm p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-sm font-bold shadow-2xs">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </span>
                        <div>
                            <h2 class="font-black text-sm text-gray-900">मासिक पूर्ण आय-व्यय स्टेटमेंट (Monthly P&L Statement)</h2>
                            <p class="text-[11px] text-gray-500">{{ DateTime::createFromFormat('!m', $selectedMonth)->format('F') }} {{ $selectedYear }} के सभी आर्डर्स का शुद्ध वित्तीय विवरण</p>
                        </div>
                    </div>
                    <span class="text-xs font-mono font-bold text-gray-600 bg-gray-100 px-2.5 py-1 rounded-xl">
                        {{ $effectiveOrdersCount }} कुल पार्सल
                    </span>
                </div>

                <div class="space-y-2 text-xs font-mono">
                    <!-- Revenue -->
                    <div class="flex items-center justify-between p-2 rounded-xl bg-emerald-50 text-emerald-950 font-black">
                        <span>(+) कुल सकल बिक्री (Gross Sales Revenue):</span>
                        <span class="text-sm">₹{{ number_format($actualGross, 2) }}</span>
                    </div>

                    <!-- Expenses breakdown -->
                    <div class="p-3 bg-gray-50 rounded-2xl space-y-2 text-gray-700 text-[11px]">
                        <div class="flex items-center justify-between border-b border-gray-200/60 pb-1.5">
                            <span class="text-gray-600 font-sans">(-) माल की शुद्ध खरीद लागत (COGS):</span>
                            <span class="font-bold text-rose-700">-₹{{ number_format($actualCogs, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-200/60 pb-1.5">
                            <span class="text-gray-600 font-sans" title="Amazon कमीशन 12% + उस पर 18% GST">(-) मार्केटप्लेस कमीशन फ़ी + GST:</span>
                            <span class="font-bold text-rose-700">-₹{{ number_format($marketplaceFees, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-200/60 pb-1.5">
                            <span class="text-gray-600 font-sans">(-) जावक कूरियर डिलीवरी बिल (Courier Bill):</span>
                            <span class="font-bold text-rose-700">-₹{{ number_format($courierBill, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-200/60 pb-1.5">
                            <span class="text-gray-600 font-sans" title="{{ $effectiveRtoCount }} RTO पार्सल × ₹145">(-) वास्तविक RTO चपत (Forward + Return + Pack):</span>
                            <span class="font-bold text-rose-700">-₹{{ number_format($actualRtoLoss, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-200/60 pb-1.5">
                            <span class="text-gray-600 font-sans">(-) पैकेजिंग डिब्बा, टेप व सामग्री:</span>
                            <span class="font-bold text-rose-700">-₹{{ number_format($actualPackSpend, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between pb-0.5">
                            <span class="text-gray-600 font-sans">(-) विज्ञापन व ऐड्स खर्च (Ads Spend):</span>
                            <span class="font-bold text-rose-700">-₹{{ number_format($actualAdsSpend, 2) }}</span>
                        </div>
                    </div>

                    <!-- Final In Hand Profit -->
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-indigo-900 text-white font-black text-sm shadow-md">
                        <span class="font-sans">(=) शुद्ध जेब में बचा मुनाफ़ा (Net In-Hand Profit):</span>
                        <span class="text-emerald-300 font-mono text-base">₹{{ number_format($netProfitInHand, 2) }}</span>
                    </div>
                </div>

                <!-- 💡 IMPORTANT CLARIFICATION CARD: AMAZON COMMISSION POLICY ON RETURNS/RTO -->
                <div class="p-3.5 rounded-2xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-300 space-y-2 text-xs">
                    <div class="flex items-center gap-2 text-amber-950 font-black">
                        <i class="fa-solid fa-circle-question text-amber-600 text-base"></i>
                        <span>महत्वपूर्ण जानकारी: रिटर्न आने पर अमेज़न कमीशन वापस करता है या नहीं?</span>
                    </div>
                    <div class="text-[11px] text-amber-900 space-y-1.5 leading-relaxed font-sans">
                        <p>
                            <strong>1. RTO होने पर (ग्राहक द्वारा डिलीवरी लेने से पहले मना करना / कैंसिल होना):</strong><br>
                            👉 <strong>हाँ, अमेज़न 100% सेलिंग कमीशन (Referral Fee + Closing Fee) वापस (Reverse/Credit) कर देता है!</strong> अमेज़न केवल अपना कूरियर डिलीवरी चार्ज काटता है। यानी RTO में कमीशन का कोई नुकसान नहीं होता।
                        </p>
                        <p>
                            <strong>2. कस्टमर रिटर्न होने पर (डिलीवरी के बाद 7 दिन में ग्राहक द्वारा वापस भेजना):</strong><br>
                            👉 अमेज़न कमीशन तो वापस करता है, लेकिन <strong>रिफंड एडमिनिस्ट्रेशन फ़ी (Refund Administration Fee)</strong> काट लेता है (जो कमीशन का 20% या अधिकतम ₹300 होता है)। साथ ही ग्राहक से माल वापस लाने का रिटर्न पिकअप कूरियर चार्ज भी सेलर के खाते से कटता है।
                        </p>
                    </div>
                </div>
            </div>

            <!-- Col 2: Next Month Budget Planner & Optimizer (5 cols) -->
            <div class="lg:col-span-5 bg-white rounded-3xl border border-gray-200 shadow-sm p-5 space-y-4">
                <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3">
                    <span class="w-8 h-8 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center text-sm font-bold shadow-2xs">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </span>
                    <div>
                        <h2 class="font-black text-sm text-gray-900">अगले महीने का बजट प्लानर (Budget Planner)</h2>
                        <p class="text-[11px] text-gray-500">इस महीने के खर्चों के आधार पर अगले महीने के प्रति-प्रोडक्ट बजट तय करें</p>
                    </div>
                </div>

                <form id="pnlBudgetSettingsForm" onsubmit="savePnlBudgetSettings(event)" class="space-y-3.5 text-xs">
                    <!-- Ads Budget Adjustment -->
                    <div class="p-3 rounded-2xl bg-slate-50 border border-gray-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="font-black text-gray-900 flex items-center gap-1.5">
                                <i class="fa-solid fa-bullhorn text-purple-600"></i>
                                <span>ऐड्स बजट (प्रति पार्सल ₹):</span>
                            </label>
                            <button type="button" onclick="document.getElementById('set_ads_budget').value = {{ $recommendedAdsPerPc }}" class="px-2 py-0.5 rounded-md bg-purple-100 hover:bg-purple-200 text-purple-900 font-bold text-[10px] transition cursor-pointer" title="इस महीने के वास्तविक खर्च अनुसार ₹{{ $recommendedAdsPerPc }} भरें">
                                सुझाव: ₹{{ $recommendedAdsPerPc }} भरें
                            </button>
                        </div>
                        <div class="relative">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 font-bold text-gray-400">₹</span>
                            <input type="number" step="0.5" id="set_ads_budget" name="ads_budget_per_pc" value="{{ $adsBudgetPerPc }}" class="w-full pl-6 pr-3 py-1.5 bg-white border border-gray-300 rounded-xl font-mono font-bold text-gray-900 text-xs">
                        </div>
                        <span class="text-[10px] text-gray-500 block">इस महीने ₹10 के बदले ₹15 लगा था। आप इसे ₹15 सेट कर सकते हैं।</span>
                    </div>

                    <!-- RTO Buffer Adjustment -->
                    <div class="p-3 rounded-2xl bg-slate-50 border border-gray-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="font-black text-gray-900 flex items-center gap-1.5">
                                <i class="fa-solid fa-shield-halved text-rose-600"></i>
                                <span>RTO सुरक्षा बफ़र (प्रति पार्सल ₹):</span>
                            </label>
                            <button type="button" onclick="document.getElementById('set_rto_buffer').value = {{ $recommendedRtoPerPc }}" class="px-2 py-0.5 rounded-md bg-rose-100 hover:bg-rose-200 text-rose-900 font-bold text-[10px] transition cursor-pointer" title="सुझाया गया ₹{{ $recommendedRtoPerPc }} भरें">
                                सुझाव: ₹{{ $recommendedRtoPerPc }} भरें
                            </button>
                        </div>
                        <div class="relative">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 font-bold text-gray-400">₹</span>
                            <input type="number" step="0.5" id="set_rto_buffer" name="rto_buffer_per_pc" value="{{ $rtoBudgetPerPc }}" class="w-full pl-6 pr-3 py-1.5 bg-white border border-gray-300 rounded-xl font-mono font-bold text-gray-900 text-xs">
                        </div>
                    </div>

                    <!-- Packaging Budget -->
                    <div class="p-3 rounded-2xl bg-slate-50 border border-gray-200 space-y-2">
                        <label class="font-black text-gray-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-boxes-packing text-amber-600"></i>
                            <span>पैकेजिंग बजट (प्रति पार्सल ₹):</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 font-bold text-gray-400">₹</span>
                            <input type="number" step="0.5" id="set_pack_budget" name="pack_budget_per_pc" value="{{ $packBudgetPerPc }}" class="w-full pl-6 pr-3 py-1.5 bg-white border border-gray-300 rounded-xl font-mono font-bold text-gray-900 text-xs">
                        </div>
                    </div>

                    <!-- Target Profit per pc -->
                    <div class="p-3 rounded-2xl bg-slate-50 border border-gray-200 space-y-2">
                        <label class="font-black text-gray-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-hand-holding-dollar text-emerald-600"></i>
                            <span>शुद्ध लाभ लक्ष्य (प्रति पार्सल ₹):</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 font-bold text-gray-400">₹</span>
                            <input type="number" step="1" id="set_target_profit" name="target_profit_per_pc" value="{{ $targetProfitPerPc }}" class="w-full pl-6 pr-3 py-1.5 bg-white border border-gray-300 rounded-xl font-mono font-bold text-emerald-800 text-xs">
                        </div>
                    </div>

                    <!-- Actual Overrides (Optional user entry for this month) -->
                    <div class="p-3 rounded-2xl bg-indigo-50/50 border border-indigo-200 space-y-2">
                        <span class="font-black text-indigo-950 block text-[11px]">इस महीने का कुल बिल दर्ज करें (Manual Overrides):</span>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-[10px] text-gray-600 block">कुल ऐड्स बिल (₹):</label>
                                <input type="number" name="actual_ads_override" value="{{ $budgetSettings['actual_ads_override'] ?? $actualAdsSpend }}" placeholder="उदा. 450" class="w-full px-2 py-1 bg-white border border-gray-300 rounded-lg font-mono font-bold text-xs">
                            </div>
                            <div>
                                <label class="text-[10px] text-gray-600 block">कुल पैकिंग बिल (₹):</label>
                                <input type="number" name="actual_pack_override" value="{{ $budgetSettings['actual_pack_override'] ?? $actualPackSpend }}" placeholder="उदा. 390" class="w-full px-2 py-1 bg-white border border-gray-300 rounded-lg font-mono font-bold text-xs">
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="btnSavePnlSettings" class="w-full py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                        <i class="fa-solid fa-floppy-disk text-emerald-200"></i>
                        <span>💾 बजट लक्ष्य सेव करें (Save Target Budget)</span>
                    </button>
                </form>
            </div>
        </div>

    </main>

    <script>
        async function savePnlBudgetSettings(event) {
            event.preventDefault();
            const form = document.getElementById('pnlBudgetSettingsForm');
            const formData = new FormData(form);
            const payload = {};
            formData.forEach((val, key) => {
                if (val !== '') payload[key] = parseFloat(val);
            });

            const btn = document.getElementById('btnSavePnlSettings');
            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> सेव हो रहा है...';

            try {
                const res = await fetch("{{ route('seller.channels.save_pnl_budget_settings') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'बजट सुरक्षित सेव हो गया!',
                        text: 'आपके नए बजट लक्ष्य सेव हो गए हैं। यह आपके सभी 19 प्रोडक्ट्स के प्राइसिंग कैलकुलेटर में भी डिफ़ॉल्ट रूप से अपडेट रहेंगे।',
                        confirmButtonText: 'ठीक है',
                        confirmButtonColor: '#059669'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    alert(data.message || 'त्रुटि आई।');
                }
            } catch (err) {
                alert('एरर: ' + err.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = origHtml;
            }
        }
    </script>
</body>
</html>
