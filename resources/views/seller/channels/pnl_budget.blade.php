<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>मासिक P&L, मंच अनुसार शुद्ध मुनाफा व दैनिक बहीखाता - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 text-gray-800 antialiased min-h-screen">

    <!-- Top Navigation Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.channels.index') }}" class="h-10 w-10 rounded-2xl bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition shadow-2xs shrink-0" title="मल्टी-चैनल हब पर वापस जाएं">
                        <i class="fa-solid fa-arrow-left text-sm"></i>
                    </a>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="font-black text-lg sm:text-xl text-gray-900 leading-tight">📊 व्यापार लेखा-जोखा व मंच अनुसार शुद्ध मुनाफा</h1>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-wide">
                                True In-Hand P&L Engine
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">Amazon, Meesho, Flipkart, डायरेक्ट व ड्रॉपशिपिंग का शुद्ध मुनाफा, स्टाफ-किराया-भाड़ा खर्च व दैनिक बहीखाता</p>
                    </div>
                </div>

                <!-- Date Filter & Quick Action Buttons -->
                <div class="flex items-center gap-2 flex-wrap">
                    <!-- Period Filter Form -->
                    <form method="GET" action="{{ route('seller.channels.pnl_budget') }}" class="flex items-center gap-1.5 bg-gray-50 p-1 rounded-2xl border border-gray-200">
                        <select name="month" onchange="this.form.submit()" class="px-2.5 py-1.5 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-800 shadow-2xs outline-none cursor-pointer">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $m)->format('M') }}
                                </option>
                            @endfor
                        </select>
                        <select name="year" onchange="this.form.submit()" class="px-2.5 py-1.5 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-800 shadow-2xs outline-none cursor-pointer">
                            @for($y = now()->year; $y >= now()->year - 2; $y--)
                                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </form>

                    <!-- Quick Add Expense Button -->
                    <button type="button" onclick="openAddExpenseModal()" class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>➕ खर्च दर्ज करें</span>
                    </button>

                    <!-- Quick Add Other Income Button -->
                    <button type="button" onclick="openAddIncomeModal()" class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                        <i class="fa-solid fa-wallet"></i>
                        <span>➕ अन्य आमदनी</span>
                    </button>

                    <a href="{{ route('seller.channels.index') }}" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition flex items-center gap-1 shadow-2xs">
                        <i class="fa-solid fa-table-cells"></i>
                        <span>कैटलॉग</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        @if($isDemoPreview)
            <!-- Demo Preview Notice -->
            <div class="p-3 bg-amber-50 border border-amber-300 rounded-2xl flex items-center justify-between text-xs text-amber-950 font-bold">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-amber-600 text-base"></i>
                    <span>डेमो सिमुलेशन प्रीव्यू: आपके द्वारा नए स्टोर के आधार पर सभी प्लेटफॉर्म्स (Amazon, Meesho, Flipkart, Minisite Direct, Dropshipping) का बेंचमार्क वित्तीय विश्लेषण दिखाया जा रहा है। जैसे ही आप वास्तविक खर्चे दर्ज करेंगे, यह बहीखाता लाइव अपडेट होगा।</span>
                </div>
                <span class="px-2 py-0.5 rounded bg-amber-200 text-amber-900 text-[10px] shrink-0 ml-2">उदाहरणात्मक डेटा</span>
            </div>
        @endif

        <!-- 🌟 GRAND BUSINESS FINANCIAL SUMMARY (शीर्ष वित्तीय सार) -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-5 text-white shadow-lg border border-indigo-500/30">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b border-indigo-800/50 pb-4">
                <div>
                    <span class="text-xs uppercase tracking-wider text-indigo-300 font-bold flex items-center gap-1.5">
                        <i class="fa-solid fa-chart-line text-emerald-400"></i>
                        <span>{{ DateTime::createFromFormat('!m', $selectedMonth)->format('F') }} {{ $selectedYear }} — कुल व्यापार वित्तीय स्थिति</span>
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black mt-1 text-white">
                        ₹{{ number_format($grandNetInHandProfit, 2) }}
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $grandNetInHandProfit >= 0 ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }} ml-2">
                            {{ $grandMarginPct }}% शुद्ध इन-हैंड मार्जिन
                        </span>
                    </h2>
                    <p class="text-xs text-indigo-200 mt-1">सभी मंचों की बिक्री, माल लागत, कूरियर, कमीशन, विज्ञापन व दुकान/स्टाफ खर्च कटने के बाद आपकी वास्तविक बचत</p>
                </div>
                <div class="flex items-center gap-2 bg-indigo-900/60 p-3 rounded-2xl border border-indigo-700/50">
                    <div class="text-right">
                        <span class="text-[10px] text-indigo-300 block">सर्वोत्तम मुनाफा देने वाला मंच:</span>
                        <strong class="text-sm text-emerald-300 capitalize flex items-center justify-end gap-1">
                            <i class="fa-solid fa-trophy text-amber-400 text-xs"></i>
                            <span>{{ $platformData[$bestPlatformKey]['info']['name'] ?? 'VyaparIndia' }}</span>
                        </strong>
                    </div>
                </div>
            </div>

            <!-- 5 Sub-metric summary pills -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 pt-4 text-xs">
                <div class="bg-white/5 p-3 rounded-2xl border border-white/10">
                    <span class="text-slate-400 block text-[10px]">कुल सकल बिक्री (Gross Sales)</span>
                    <strong class="text-base font-black text-white font-mono">₹{{ number_format($grandSales, 2) }}</strong>
                    <span class="text-[10px] text-emerald-400 block mt-0.5">सभी 5 प्लेटफॉर्म्स मिलाकर</span>
                </div>
                <div class="bg-white/5 p-3 rounded-2xl border border-white/10">
                    <span class="text-slate-400 block text-[10px]">माल खरीद लागत (COGS)</span>
                    <strong class="text-base font-black text-rose-300 font-mono">₹{{ number_format($grandCogs, 2) }}</strong>
                    <span class="text-[10px] text-slate-400 block mt-0.5">सामान की मूल लागत</span>
                </div>
                <div class="bg-white/5 p-3 rounded-2xl border border-white/10">
                    <span class="text-slate-400 block text-[10px]">प्लेटफॉर्म फीस व कूरियर बिल</span>
                    <strong class="text-base font-black text-amber-300 font-mono">₹{{ number_format($grandFees + $grandShipping, 2) }}</strong>
                    <span class="text-[10px] text-slate-400 block mt-0.5">कमीशन ₹{{ number_format($grandFees) }} | कूरियर ₹{{ number_format($grandShipping) }}</span>
                </div>
                <div class="bg-white/5 p-3 rounded-2xl border border-white/10">
                    <span class="text-slate-400 block text-[10px]">दुकान/गोदाम ओवरहेड्स खर्च</span>
                    <strong class="text-base font-black text-purple-300 font-mono">₹{{ number_format($grandOverheads, 2) }}</strong>
                    <span class="text-[10px] text-slate-400 block mt-0.5">किराया, स्टाफ, भाड़ा, CA</span>
                </div>
                <div class="bg-white/5 p-3 rounded-2xl border border-white/10">
                    <span class="text-slate-400 block text-[10px]">अन्य आमदनी (Other Income)</span>
                    <strong class="text-base font-black text-emerald-300 font-mono">+₹{{ number_format($grandOtherIncome, 2) }}</strong>
                    <span class="text-[10px] text-slate-400 block mt-0.5">स्क्रेप बिक्री, रिफंड आदि</span>
                </div>
            </div>
        </div>

        <!-- 🌟 PLATFORM-WISE PROFIT & LOSS BREAKDOWN TABLE (मंच अनुसार शुद्ध मुनाफा व तुलना) -->
        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-5 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-sm shadow-xs">
                        <i class="fa-solid fa-layer-group"></i>
                    </span>
                    <div>
                        <h2 class="font-black text-sm sm:text-base text-gray-900">मंच अनुसार पूर्ण आय-व्यय व शुद्ध मुनाफा (Platform-wise P&L)</h2>
                        <p class="text-[11px] text-gray-500">किस चैनल से कितना माल बिका, कितना कमीशन/ऐड्स/पैकिंग लगा और किसमें सबसे ज्यादा शुद्ध मुनाफा हुआ</p>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold text-gray-600 bg-gray-100 px-3 py-1 rounded-xl">
                    5 सेलिंग चैनल्स सक्रिय
                </span>
            </div>

            <!-- Responsive Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-500 font-bold uppercase text-[10px] tracking-wider bg-gray-50/70">
                            <th class="py-3 px-3">मंच (Platform)</th>
                            <th class="py-3 px-3 text-right">कुल बिक्री (Sales)</th>
                            <th class="py-3 px-3 text-right">खरीद लागत (COGS)</th>
                            <th class="py-3 px-3 text-right" title="मार्केटप्लेस कमीशन, क्लोजिंग फीस व उस पर GST">कमीशन फ़ी</th>
                            <th class="py-3 px-3 text-right" title="जावक कूरियर डिलीवरी भाड़ा">कूरियर बिल</th>
                            <th class="py-3 px-3 text-right">ऐड्स खर्च</th>
                            <th class="py-3 px-3 text-right">पैकिंग खर्च</th>
                            <th class="py-3 px-3 text-right" title="रिटर्न/RTO पर लगने वाला रिवर्स व डैमेज नुकसान">RTO नुकसान</th>
                            <th class="py-3 px-3 text-right" title="दुकान किराया, स्टाफ वेतन, CA व सामान्य ओवरहेड्स का हिस्सा">ओवरहेड्स हिस्सा</th>
                            <th class="py-3 px-4 text-right bg-indigo-50/80 font-black text-indigo-950">शुद्ध जेब मुनाफा (Net Profit)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-mono">
                        @foreach($platformData as $pKey => $pRow)
                            @php
                                $isWinner = ($pKey === $bestPlatformKey);
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition {{ $isWinner ? 'bg-emerald-50/30' : '' }}">
                                <td class="py-3.5 px-3 font-sans">
                                    <div class="flex items-center gap-2">
                                        <span class="w-7 h-7 rounded-xl {{ $pRow['info']['bg'] }} text-white flex items-center justify-center text-xs shadow-2xs shrink-0">
                                            <i class="{{ $pRow['info']['icon'] }}"></i>
                                        </span>
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-black text-gray-900 text-xs">{{ $pRow['info']['name'] }}</span>
                                                @if($isWinner)
                                                    <span class="px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 text-[9px] font-black font-sans">
                                                        ★ टॉप मुनाफा
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-[10px] text-gray-400 font-sans block">{{ $pRow['info']['badge'] }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 text-right font-black text-gray-900">
                                    ₹{{ number_format($pRow['sales'], 2) }}
                                    <span class="text-[10px] text-gray-400 block font-normal">{{ $pRow['delivered_count'] }} डिलीवर</span>
                                </td>
                                <td class="py-3.5 px-3 text-right text-rose-600 font-bold">
                                    -₹{{ number_format($pRow['cogs'], 2) }}
                                </td>
                                <td class="py-3.5 px-3 text-right text-rose-600 font-bold">
                                    -₹{{ number_format($pRow['fees'], 2) }}
                                    <span class="text-[9px] text-gray-400 block font-sans font-normal">{{ round($pRow['info']['fee_rate'] * 100) }}% फ़ीस</span>
                                </td>
                                <td class="py-3.5 px-3 text-right text-rose-600 font-bold">
                                    -₹{{ number_format($pRow['shipping'], 2) }}
                                </td>
                                <td class="py-3.5 px-3 text-right text-rose-600 font-bold">
                                    -₹{{ number_format($pRow['ads'], 2) }}
                                </td>
                                <td class="py-3.5 px-3 text-right text-rose-600 font-bold">
                                    -₹{{ number_format($pRow['pack'], 2) }}
                                </td>
                                <td class="py-3.5 px-3 text-right text-rose-700 font-bold">
                                    -₹{{ number_format($pRow['rto_loss'], 2) }}
                                    <span class="text-[9px] text-gray-400 block font-sans font-normal">{{ $pRow['rto_count'] }} RTO ({{ $pRow['rto_pct'] }}%)</span>
                                </td>
                                <td class="py-3.5 px-3 text-right text-purple-700 font-bold">
                                    -₹{{ number_format($pRow['overhead_share'], 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-right bg-indigo-50/80">
                                    <div class="font-black text-sm {{ $pRow['net_profit'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                        ₹{{ number_format($pRow['net_profit'], 2) }}
                                    </div>
                                    <span class="text-[10px] font-bold font-sans {{ $pRow['net_profit'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $pRow['net_margin_pct'] }}% शुद्ध मार्जिन
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t-2 border-gray-300 font-mono text-xs font-black">
                        <tr>
                            <td class="py-3 px-3 font-sans text-gray-900">कुल योग (Grand Total)</td>
                            <td class="py-3 px-3 text-right text-gray-900">₹{{ number_format($grandSales, 2) }}</td>
                            <td class="py-3 px-3 text-right text-rose-700">-₹{{ number_format($grandCogs, 2) }}</td>
                            <td class="py-3 px-3 text-right text-rose-700">-₹{{ number_format($grandFees, 2) }}</td>
                            <td class="py-3 px-3 text-right text-rose-700">-₹{{ number_format($grandShipping, 2) }}</td>
                            <td class="py-3 px-3 text-right text-rose-700">-₹{{ number_format($grandAds, 2) }}</td>
                            <td class="py-3 px-3 text-right text-rose-700">-₹{{ number_format($grandPack, 2) }}</td>
                            <td class="py-3 px-3 text-right text-rose-700">-₹{{ number_format($grandRtoLoss, 2) }}</td>
                            <td class="py-3 px-3 text-right text-purple-700">-₹{{ number_format($grandOverheads, 2) }}</td>
                            <td class="py-3 px-4 text-right bg-indigo-100/80 text-emerald-800 text-sm">
                                ₹{{ number_format($grandNetInHandProfit, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Key Vyapar Insights Callout -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2 text-xs">
                <div class="p-3 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-950 space-y-1">
                    <span class="font-black flex items-center gap-1.5 text-indigo-800">
                        <i class="fa-solid fa-store"></i>
                        <span>डायरेक्ट स्टोर (Minisite) का फायदा:</span>
                    </span>
                    <p class="text-[11px] leading-relaxed text-indigo-900">
                        डायरेक्ट स्टोर में <strong>0% मार्केटप्लेस कमीशन</strong> कटता है। यहाँ सिर्फ 2% पेमेंट गेटवे चार्ज लगता है, जिससे आपका मुनाफा अमेज़न व फ्लिपकार्ट की तुलना में <strong>दोगुना (28%+)</strong> रहता है।
                    </p>
                </div>
                <div class="p-3 rounded-2xl bg-pink-50 border border-pink-200 text-pink-950 space-y-1">
                    <span class="font-black flex items-center gap-1.5 text-pink-800">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <span>मीशो (Meesho) का विश्लेषण:</span>
                    </span>
                    <p class="text-[11px] leading-relaxed text-pink-900">
                        मीशो पर कमीशन 0% है लेकिन यहाँ <strong>RTO प्रतिशत ज्यादा</strong> रहता है। इसलिए प्रति पीस ₹20 का RTO बफ़र अवश्य जोड़ें ताकि रिटर्न आने पर भी आपकी जेब से पैसा न कटे।
                    </p>
                </div>
                <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200 text-amber-950 space-y-1">
                    <span class="font-black flex items-center gap-1.5 text-amber-800">
                        <i class="fa-brands fa-amazon"></i>
                        <span>अमेज़न (Amazon) का विश्लेषण:</span>
                    </span>
                    <p class="text-[11px] leading-relaxed text-amber-900">
                        अमेज़न सबसे ज्यादा ऑर्डर्स वॉल्यूम देता है, लेकिन <strong>कमीशन + क्लोजिंग फीस (14-16%)</strong> व स्पॉन्सर्ड ऐड्स खर्च ऊंचा रहता है। इसे पूरा करने के लिए सेलिंग प्राइस ₹40-₹60 ऊपर रखें।
                    </p>
                </div>
            </div>
        </div>

        <!-- 🏢 BUSINESS OVERHEADS & DAILY EXPENSE LEDGER (बिजनेस खर्च व दैनिक बहीखाता) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Left Col: Overheads Breakdown by Category (4 Cols) -->
            <div class="lg:col-span-4 bg-white rounded-3xl border border-gray-200 shadow-sm p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-purple-600 text-white flex items-center justify-center text-xs shadow-2xs">
                            <i class="fa-solid fa-building-user"></i>
                        </span>
                        <div>
                            <h3 class="font-black text-sm text-gray-900">बिजनेस ओवरहेड्स खर्च</h3>
                            <p class="text-[10px] text-gray-500">दुकान, स्टाफ, भाड़ा व CA खर्चे</p>
                        </div>
                    </div>
                    <span class="text-xs font-mono font-black text-purple-700 bg-purple-50 px-2 py-0.5 rounded-lg border border-purple-200">
                        ₹{{ number_format($grandOverheads, 2) }}
                    </span>
                </div>

                <div class="space-y-2 text-xs">
                    <!-- Rent -->
                    <div class="flex items-center justify-between p-2.5 rounded-2xl bg-amber-50/70 border border-amber-200/60">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-building text-amber-600"></i>
                            <span class="font-bold text-gray-800">दुकान / गोदाम किराया:</span>
                        </div>
                        <span class="font-mono font-black text-gray-900">₹{{ number_format($expenseByCategory['rent'], 2) }}</span>
                    </div>

                    <!-- Staff Salary -->
                    <div class="flex items-center justify-between p-2.5 rounded-2xl bg-blue-50/70 border border-blue-200/60">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-users text-blue-600"></i>
                            <span class="font-bold text-gray-800">स्टाफ वेतन व मजदूरी:</span>
                        </div>
                        <span class="font-mono font-black text-gray-900">₹{{ number_format($expenseByCategory['salary'], 2) }}</span>
                    </div>

                    <!-- Cartage & Local Tempo -->
                    <div class="flex items-center justify-between p-2.5 rounded-2xl bg-purple-50/70 border border-purple-200/60">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-truck-ramp-box text-purple-600"></i>
                            <span class="font-bold text-gray-800">लोकल भाड़ा / टेम्पो:</span>
                        </div>
                        <span class="font-mono font-black text-gray-900">₹{{ number_format($expenseByCategory['cartage'], 2) }}</span>
                    </div>

                    <!-- CA & Audit Fees -->
                    <div class="flex items-center justify-between p-2.5 rounded-2xl bg-emerald-50/70 border border-emerald-200/60">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-file-invoice-dollar text-emerald-600"></i>
                            <span class="font-bold text-gray-800">CA फीस / GST कंसल्टिंग:</span>
                        </div>
                        <span class="font-mono font-black text-gray-900">₹{{ number_format($expenseByCategory['ca_audit'], 2) }}</span>
                    </div>

                    <!-- Electricity & Bills -->
                    <div class="flex items-center justify-between p-2.5 rounded-2xl bg-yellow-50/70 border border-yellow-200/60">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-bolt text-yellow-600"></i>
                            <span class="font-bold text-gray-800">बिजली / वाईफाई बिल:</span>
                        </div>
                        <span class="font-mono font-black text-gray-900">₹{{ number_format($expenseByCategory['electricity'], 2) }}</span>
                    </div>

                    <!-- Software & Tools -->
                    <div class="flex items-center justify-between p-2.5 rounded-2xl bg-indigo-50/70 border border-indigo-200/60">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-laptop-code text-indigo-600"></i>
                            <span class="font-bold text-gray-800">सॉफ्टवेयर व टूल्स:</span>
                        </div>
                        <span class="font-mono font-black text-gray-900">₹{{ number_format($expenseByCategory['software'], 2) }}</span>
                    </div>

                    <!-- Misc -->
                    <div class="flex items-center justify-between p-2.5 rounded-2xl bg-gray-50 border border-gray-200/80">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-wallet text-gray-600"></i>
                            <span class="font-bold text-gray-800">विविध / फुटकर खर्चे:</span>
                        </div>
                        <span class="font-mono font-black text-gray-900">₹{{ number_format($expenseByCategory['misc'], 2) }}</span>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="button" onclick="openAddExpenseModal()" class="w-full py-2.5 rounded-2xl bg-indigo-50 hover:bg-indigo-100 text-indigo-800 font-bold text-xs border border-indigo-200 transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-plus-circle text-indigo-600"></i>
                        <span>नया बिजनेस खर्च जोड़ें</span>
                    </button>
                </div>
            </div>

            <!-- Right Col: Daily Expense Ledger Entries Table (8 Cols) -->
            <div class="lg:col-span-8 bg-white rounded-3xl border border-gray-200 shadow-sm p-5 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center text-xs shadow-2xs">
                            <i class="fa-solid fa-book-open"></i>
                        </span>
                        <div>
                            <h3 class="font-black text-sm text-gray-900">दैनिक खर्च बहीखाता (Expense Ledger)</h3>
                            <p class="text-[10px] text-gray-500">तारीख अनुसार दर्ज किए गए सभी खर्चों की सूची</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-mono font-bold text-gray-600 bg-gray-100 px-2.5 py-1 rounded-xl">
                            {{ $allExpenses->count() }} प्रविष्टियाँ
                        </span>
                        <button type="button" onclick="openAddExpenseModal()" class="px-3 py-1 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition shadow-2xs cursor-pointer">
                            + खर्च जोड़ें
                        </button>
                    </div>
                </div>

                @if($allExpenses->isEmpty())
                    <!-- Clean Empty State -->
                    <div class="text-center py-8 px-4 border-2 border-dashed border-gray-200 rounded-3xl bg-gray-50/50 space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto text-xl">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-gray-900">इस महीने कोई खर्च दर्ज नहीं है</h4>
                            <p class="text-xs text-gray-500 max-w-md mx-auto mt-0.5">स्टाफ वेतन, दुकान किराया, टेम्पो भाड़ा या पैकिंग खर्च दर्ज करें ताकि सटीक इन-हैंड मुनाफा निकल सके।</p>
                        </div>
                        <div class="flex items-center justify-center gap-2 flex-wrap pt-1">
                            <button onclick="quickAddExpense('rent', 'दुकान किराया', 8000)" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 text-xs text-gray-700 hover:border-indigo-400 font-bold transition shadow-2xs">
                                🏢 दुकान किराया दर्ज करें
                            </button>
                            <button onclick="quickAddExpense('salary', 'स्टाफ सैलरी', 12000)" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 text-xs text-gray-700 hover:border-indigo-400 font-bold transition shadow-2xs">
                                👥 स्टाफ सैलरी दर्ज करें
                            </button>
                            <button onclick="quickAddExpense('cartage', 'लोकल टेम्पो भाड़ा', 1500)" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 text-xs text-gray-700 hover:border-indigo-400 font-bold transition shadow-2xs">
                                🚚 टेम्पो भाड़ा दर्ज करें
                            </button>
                            <button onclick="quickAddExpense('ca_audit', 'CA मासिक फीस', 2000)" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 text-xs text-gray-700 hover:border-indigo-400 font-bold transition shadow-2xs">
                                📑 CA फीस दर्ज करें
                            </button>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto max-h-96 overflow-y-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-gray-200 text-gray-500 font-bold uppercase text-[10px] tracking-wider bg-gray-50">
                                    <th class="py-2.5 px-3">तारीख</th>
                                    <th class="py-2.5 px-3">श्रेणी (Category)</th>
                                    <th class="py-2.5 px-3">मंच (Platform)</th>
                                    <th class="py-2.5 px-3">भुगतान किसको (Paid To)</th>
                                    <th class="py-2.5 px-3">मोड</th>
                                    <th class="py-2.5 px-3 text-right">रकम (Amount)</th>
                                    <th class="py-2.5 px-2 text-center">एक्शन</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-mono">
                                @foreach($allExpenses as $exp)
                                    <tr class="hover:bg-gray-50/70 transition">
                                        <td class="py-2.5 px-3 font-sans text-gray-800 font-bold whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') }}
                                        </td>
                                        <td class="py-2.5 px-3 font-sans">
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-bold capitalize bg-gray-100 text-gray-800 border border-gray-200">
                                                @if($exp->category == 'rent') 🏢 किराया
                                                @elseif($exp->category == 'salary') 👥 स्टाफ वेतन
                                                @elseif($exp->category == 'cartage') 🚚 लोकल भाड़ा
                                                @elseif($exp->category == 'ca_audit') 📑 CA फीस
                                                @elseif($exp->category == 'packing') 📦 पैकिंग
                                                @elseif($exp->category == 'ads') 📢 विज्ञापन
                                                @elseif($exp->category == 'electricity') ⚡ बिजली
                                                @elseif($exp->category == 'software') 💻 सॉफ्टवेयर
                                                @else 💰 विविध @endif
                                            </span>
                                            @if($exp->notes)
                                                <span class="text-[10px] text-gray-400 block font-normal font-sans truncate max-w-xs">{{ $exp->notes }}</span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-3 font-sans">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                                {{ $exp->platform }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3 font-sans text-gray-700">
                                            {{ $exp->paid_to ?: '-' }}
                                        </td>
                                        <td class="py-2.5 px-3 font-sans text-gray-500 uppercase text-[10px]">
                                            {{ $exp->payment_mode }}
                                        </td>
                                        <td class="py-2.5 px-3 text-right font-black text-rose-600">
                                            ₹{{ number_format($exp->amount, 2) }}
                                        </td>
                                        <td class="py-2.5 px-2 text-center">
                                            <button type="button" onclick="deleteExpense({{ $exp->id }})" class="h-7 w-7 rounded-lg bg-gray-100 hover:bg-rose-100 text-gray-500 hover:text-rose-600 transition flex items-center justify-center mx-auto cursor-pointer" title="हटाएं">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>

        <!-- 💰 OTHER INCOMES & NON-SALES REVENUE (अन्य आमदनी बहीखाता) -->
        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-5 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xs shadow-2xs">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </span>
                    <div>
                        <h3 class="font-black text-sm text-gray-900">अन्य आमदनी बहीखाता (Scrap, Courier Claim & Rebates)</h3>
                        <p class="text-[10px] text-gray-500">रद्दी खाली डिब्बे बिक्री, कूरियर डैमेज क्लेम रिफंड, सप्लायर कैशबैक आदि</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-mono font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-xl">
                        कुल अन्य आमदनी: +₹{{ number_format($grandOtherIncome, 2) }}
                    </span>
                    <button type="button" onclick="openAddIncomeModal()" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-2xs cursor-pointer">
                        + आमदनी जोड़ें
                    </button>
                </div>
            </div>

            @if($allOtherIncomes->isEmpty())
                <div class="text-center py-6 px-4 bg-gray-50/60 rounded-2xl border border-gray-200/80">
                    <p class="text-xs text-gray-500 font-bold">इस महीने कोई अन्य आमदनी दर्ज नहीं है। यदि पुराने कार्टन बेचे हों या कूरियर से डैमेज क्लेम मिला हो तो यहाँ जोड़ें।</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 text-gray-500 font-bold uppercase text-[10px] tracking-wider bg-gray-50">
                                <th class="py-2.5 px-3">तारीख</th>
                                <th class="py-2.5 px-3">आमदनी का प्रकार</th>
                                <th class="py-2.5 px-3">मंच</th>
                                <th class="py-2.5 px-3">किससे प्राप्त हुआ</th>
                                <th class="py-2.5 px-3">विवरण / नोट्स</th>
                                <th class="py-2.5 px-3 text-right">रकम (Amount)</th>
                                <th class="py-2.5 px-2 text-center">एक्शन</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-mono">
                            @foreach($allOtherIncomes as $inc)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="py-2.5 px-3 font-sans text-gray-800 font-bold whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($inc->income_date)->format('d M Y') }}
                                    </td>
                                    <td class="py-2.5 px-3 font-sans font-bold text-gray-900">
                                        {{ $inc->income_type }}
                                    </td>
                                    <td class="py-2.5 px-3 font-sans uppercase text-[10px] text-gray-500">
                                        {{ $inc->platform }}
                                    </td>
                                    <td class="py-2.5 px-3 font-sans text-gray-700">
                                        {{ $inc->received_from ?: '-' }}
                                    </td>
                                    <td class="py-2.5 px-3 font-sans text-gray-500">
                                        {{ $inc->notes ?: '-' }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-black text-emerald-600">
                                        +₹{{ number_format($inc->amount, 2) }}
                                    </td>
                                    <td class="py-2.5 px-2 text-center">
                                        <button type="button" onclick="deleteOtherIncome({{ $inc->id }})" class="h-7 w-7 rounded-lg bg-gray-100 hover:bg-rose-100 text-gray-500 hover:text-rose-600 transition flex items-center justify-center mx-auto cursor-pointer" title="हटाएं">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- 4 CORE VARIANCE KPI CARDS (BUDGET VS ACTUAL) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Ads Budget vs Actual -->
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

                <div class="p-2.5 rounded-2xl {{ $adsVariance < 0 ? 'bg-rose-50 border border-rose-200 text-rose-950' : 'bg-emerald-50 border border-emerald-200 text-emerald-950' }} text-[11px] leading-tight space-y-1">
                    <div class="flex items-center justify-between font-bold">
                        <span>विचलन (Variance):</span>
                        <span class="font-mono {{ $adsVariance < 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                            {{ $adsVariance < 0 ? '-' : '+' }}₹{{ number_format(abs($adsVariance), 2) }}
                        </span>
                    </div>
                    <p class="text-[10px]">
                        @if($adsVariance < 0)
                            👉 ऐड्स में ₹{{ abs($adsVariance) }} ज्यादा खर्च हुए (प्रति पीस ₹{{ $recommendedAdsPerPc }} लगा)। अगले महीने प्रति पीस ऐड्स बजट ₹{{ $recommendedAdsPerPc }} रखें।
                        @else
                            👉 ऐड्स में ₹{{ $adsVariance }} की बचत हुई जो आपके व्यापार को मजबूत करती है।
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

                <div class="p-2.5 rounded-2xl {{ $rtoVariance >= 0 ? 'bg-emerald-50 border border-emerald-200 text-emerald-950' : 'bg-rose-50 border border-rose-200 text-rose-950' }} text-[11px] leading-tight space-y-1">
                    <div class="flex items-center justify-between font-bold">
                        <span>बफ़र शेष (Surplus):</span>
                        <span class="font-mono {{ $rtoVariance >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ $rtoVariance >= 0 ? '+' : '-' }}₹{{ number_format(abs($rtoVariance), 2) }}
                        </span>
                    </div>
                    <p class="text-[10px]">
                        @if($rtoVariance >= 0)
                            ✓ RTO नुकसान (₹{{ $actualRtoLoss }}) बफ़र से कम रहा। ₹{{ $rtoVariance }} सुरक्षित बचत रही!
                        @else
                            ⚠️ RTO चपत बफ़र से ज्यादा हो गई, जिससे ₹{{ abs($rtoVariance) }} सीधे मुनाफे से कटा।
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

                <div class="p-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 text-[11px] leading-tight space-y-1">
                    <div class="flex items-center justify-between font-bold">
                        <span>पैकिंग बचत:</span>
                        <span class="font-mono text-emerald-700">+₹{{ number_format($packVariance, 2) }}</span>
                    </div>
                    <p class="text-[10px]">
                        ✓ प्रति पार्सल औसत ₹{{ round($actualPackSpend / $effectiveOrdersCount, 1) }} खर्च आया।
                    </p>
                </div>
            </div>

            <!-- 4. Net In-Hand Profit Card -->
            <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white p-4 rounded-3xl border border-indigo-500/40 shadow-md space-y-3 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-indigo-300 uppercase flex items-center gap-1.5">
                            <i class="fa-solid fa-wallet text-emerald-400"></i>
                            <span>कुल शुद्ध इन-हैंड मुनाफा</span>
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] font-black font-mono">
                            {{ $grandMarginPct }}% Margin
                        </span>
                    </div>

                    <div class="mt-2.5">
                        <span class="text-[10px] text-slate-400 block font-bold">दुकान/स्टाफ सहित सब खर्चे कटने के बाद:</span>
                        <span class="text-2xl font-black font-mono text-emerald-400">₹{{ number_format($grandNetInHandProfit, 2) }}</span>
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
            <!-- Col 1: Complete Monthly P&L Statement -->
            <div class="lg:col-span-7 bg-white rounded-3xl border border-gray-200 shadow-sm p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-sm font-bold shadow-2xs">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </span>
                        <div>
                            <h2 class="font-black text-sm text-gray-900">मासिक पूर्ण आय-व्यय स्टेटमेंट (Monthly P&L Statement)</h2>
                            <p class="text-[11px] text-gray-500">{{ DateTime::createFromFormat('!m', $selectedMonth)->format('F') }} {{ $selectedYear }} के सभी आर्डर्स व ओवरहेड्स का वित्तीय विवरण</p>
                        </div>
                    </div>
                    <span class="text-xs font-mono font-bold text-gray-600 bg-gray-100 px-2.5 py-1 rounded-xl">
                        {{ $effectiveOrdersCount }} कुल पार्सल
                    </span>
                </div>

                <div class="space-y-2 text-xs font-mono">
                    <!-- Revenue -->
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-emerald-50 text-emerald-950 font-black">
                        <span>(+) कुल सकल बिक्री (Gross Sales Revenue):</span>
                        <span class="text-sm">₹{{ number_format($grandSales, 2) }}</span>
                    </div>

                    <!-- Expenses breakdown -->
                    <div class="p-3 bg-gray-50 rounded-2xl space-y-2 text-gray-700 text-[11px]">
                        <div class="flex items-center justify-between border-b border-gray-200/60 pb-1.5">
                            <span class="text-gray-600 font-sans">(-) माल की शुद्ध खरीद लागत (COGS):</span>
                            <span class="font-bold text-rose-700">-₹{{ number_format($grandCogs, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-200/60 pb-1.5">
                            <span class="text-gray-600 font-sans">(-) मार्केटप्लेस कमीशन फ़ी + GST:</span>
                            <span class="font-bold text-rose-700">-₹{{ number_format($grandFees, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-200/60 pb-1.5">
                            <span class="text-gray-600 font-sans">(-) जावक कूरियर डिलीवरी बिल (Courier Bill):</span>
                            <span class="font-bold text-rose-700">-₹{{ number_format($grandShipping, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-200/60 pb-1.5">
                            <span class="text-gray-600 font-sans">(-) वास्तविक RTO चपत (Forward + Return + Pack):</span>
                            <span class="font-bold text-rose-700">-₹{{ number_format($grandRtoLoss, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-200/60 pb-1.5">
                            <span class="text-gray-600 font-sans">(-) पैकेजिंग डिब्बा, टेप व सामग्री:</span>
                            <span class="font-bold text-rose-700">-₹{{ number_format($grandPack, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-200/60 pb-1.5">
                            <span class="text-gray-600 font-sans">(-) विज्ञापन व ऐड्स खर्च (Ads Spend):</span>
                            <span class="font-bold text-rose-700">-₹{{ number_format($grandAds, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-200/60 pb-1.5">
                            <span class="text-gray-600 font-sans font-bold text-purple-900">(-) दुकान, स्टाफ वेतन, टेम्पो व CA ओवरहेड्स:</span>
                            <span class="font-bold text-purple-700">-₹{{ number_format($grandOverheads, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between pb-0.5">
                            <span class="text-gray-600 font-sans font-bold text-emerald-800">(+) अन्य आमदनी (Scrap/Claims):</span>
                            <span class="font-bold text-emerald-700">+₹{{ number_format($grandOtherIncome, 2) }}</span>
                        </div>
                    </div>

                    <!-- Final In Hand Profit -->
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-indigo-900 text-white font-black text-sm shadow-md">
                        <span class="font-sans">(=) कुल शुद्ध इन-हैंड मुनाफा (Net In-Hand Profit):</span>
                        <span class="text-emerald-300 font-mono text-base">₹{{ number_format($grandNetInHandProfit, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Col 2: Next Month Target Optimizer -->
            <div class="lg:col-span-5 bg-white rounded-3xl border border-gray-200 shadow-sm p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm font-bold shadow-2xs">
                            <i class="fa-solid fa-sliders"></i>
                        </span>
                        <div>
                            <h2 class="font-black text-sm text-gray-900">अगले महीने का बजट प्लानर</h2>
                            <p class="text-[11px] text-gray-500">कैलकुलेटर में सुरक्षित मार्जिन सेटिंग्स</p>
                        </div>
                    </div>
                </div>

                <form id="pnlBudgetSettingsForm" onsubmit="savePnlBudgetSettings(event)" class="space-y-3.5 text-xs">
                    @csrf

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">
                            प्रति पीस ऐड्स बजट (₹):
                        </label>
                        <div class="relative">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 font-bold text-gray-400">₹</span>
                            <input type="number" step="0.5" id="set_ads_budget" name="ads_budget_per_pc" value="{{ $adsBudgetPerPc }}" class="w-full pl-6 pr-3 py-1.5 bg-white border border-gray-300 rounded-xl font-mono font-bold text-gray-900 text-xs">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">
                            प्रति पीस RTO बफ़र (₹):
                        </label>
                        <div class="relative">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 font-bold text-gray-400">₹</span>
                            <input type="number" step="0.5" id="set_rto_buffer" name="rto_buffer_per_pc" value="{{ $rtoBudgetPerPc }}" class="w-full pl-6 pr-3 py-1.5 bg-white border border-gray-300 rounded-xl font-mono font-bold text-gray-900 text-xs">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">
                            प्रति पीस पैकेजिंग सामग्री (₹):
                        </label>
                        <div class="relative">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 font-bold text-gray-400">₹</span>
                            <input type="number" step="0.5" id="set_pack_budget" name="pack_budget_per_pc" value="{{ $packBudgetPerPc }}" class="w-full pl-6 pr-3 py-1.5 bg-white border border-gray-300 rounded-xl font-mono font-bold text-gray-900 text-xs">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">
                            लक्ष्य शुद्ध मुनाफा प्रति पीस (₹):
                        </label>
                        <div class="relative">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 font-bold text-gray-400">₹</span>
                            <input type="number" step="1" id="set_target_profit" name="target_profit_per_pc" value="{{ $targetProfitPerPc }}" class="w-full pl-6 pr-3 py-1.5 bg-white border border-gray-300 rounded-xl font-mono font-bold text-emerald-800 text-xs">
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

    <!-- ➕ MODAL: ADD BUSINESS EXPENSE -->
    <div id="modalAddExpense" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-gray-200 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-2xl bg-rose-600 text-white flex items-center justify-center text-sm shadow-2xs">
                        <i class="fa-solid fa-receipt"></i>
                    </span>
                    <div>
                        <h3 class="font-black text-base text-gray-900">नया व्यापार खर्च दर्ज करें</h3>
                        <p class="text-xs text-gray-500">स्टाफ, किराया, भाड़ा, CA फीस या अन्य खर्च</p>
                    </div>
                </div>
                <button type="button" onclick="closeAddExpenseModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center text-sm transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="formAddExpense" onsubmit="submitAddExpense(event)" class="space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">तारीख (Expense Date) *</label>
                        <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl font-bold text-gray-800 text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">खर्च श्रेणी (Category) *</label>
                        <select name="category" id="modalExpenseCategory" required class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl font-bold text-gray-800 text-xs">
                            <option value="rent">🏢 दुकान / गोदाम किराया (Rent)</option>
                            <option value="salary">👥 स्टाफ वेतन / मजदूरी (Salary)</option>
                            <option value="cartage">🚚 लोकल टेम्पो / भाड़ा (Cartage)</option>
                            <option value="ca_audit">📑 CA फीस / ऑडिट / GST (CA Fees)</option>
                            <option value="packing">📦 पैकिंग मटेरियल (Packing Material)</option>
                            <option value="ads">📢 डिजिटल ऐड्स खर्च (Ads PPC)</option>
                            <option value="electricity">⚡ बिजली / पानी / वाई-फाई (Utilities)</option>
                            <option value="software">💻 सॉफ्टवेयर व टूल्स (Software)</option>
                            <option value="misc">💰 विविध / अन्य फुटकर खर्च (Misc)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">रकम (Amount ₹) *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-gray-400">₹</span>
                            <input type="number" step="0.01" name="amount" id="modalExpenseAmount" placeholder="उदा. 5000" required class="w-full pl-7 pr-3 py-2 bg-white border border-gray-300 rounded-xl font-mono font-black text-rose-600 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">किस मंच से संबंधित है?</label>
                        <select name="platform" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl font-bold text-gray-800 text-xs">
                            <option value="general">🏢 सामान्य व्यापार (Common Business)</option>
                            <option value="amazon">Amazon India</option>
                            <option value="meesho">Meesho</option>
                            <option value="flipkart">Flipkart</option>
                            <option value="vyaparindia">VyaparIndia Direct</option>
                            <option value="dropship">Dropshipping</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">भुगतान का माध्यम (Payment Mode)</label>
                        <select name="payment_mode" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl font-bold text-gray-800 text-xs">
                            <option value="cash">💵 नकद (Cash)</option>
                            <option value="upi">📱 UPI / PhonePe / GPay</option>
                            <option value="bank_transfer">🏦 बैंक ट्रांसफर / NEFT / IMPS</option>
                            <option value="cheque">📜 चेक (Cheque)</option>
                            <option value="card">💳 डेबिट / क्रेडिट कार्ड</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">किसको भुगतान किया (Paid To)</label>
                        <input type="text" name="paid_to" id="modalExpensePaidTo" placeholder="उदा. मकान मालिक / राहुल शर्मा / ट्रांसपोर्ट" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">रसीद / बिल संख्या (Optional)</label>
                        <input type="text" name="invoice_receipt_no" placeholder="उदा. INV-2026-081" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl text-xs font-mono">
                    </div>
                    <div class="flex items-center pt-5">
                        <label class="flex items-center gap-2 cursor-pointer font-bold text-gray-700">
                            <input type="checkbox" name="is_recurring" value="1" class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                            <span>हर महीने दोहराया जाने वाला खर्च (Recurring)</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">विवरण / नोट्स (Notes)</label>
                    <textarea name="notes" id="modalExpenseNotes" rows="2" placeholder="खर्च का कोई विशेष विवरण..." class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl text-xs"></textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeAddExpenseModal()" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition">
                        रद्द करें
                    </button>
                    <button type="submit" id="btnSubmitExpense" class="px-5 py-2 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 text-white font-black shadow-md transition flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>खर्च सुरक्षित सेव करें</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ➕ MODAL: ADD OTHER INCOME -->
    <div id="modalAddIncome" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-200 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-sm shadow-2xs">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </span>
                    <div>
                        <h3 class="font-black text-base text-gray-900">अन्य आमदनी जोड़ें</h3>
                        <p class="text-xs text-gray-500">स्क्रेप बिक्री, कूरियर रिफंड, कैशबैक आदि</p>
                    </div>
                </div>
                <button type="button" onclick="closeAddIncomeModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center text-sm transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="formAddIncome" onsubmit="submitAddIncome(event)" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">तारीख *</label>
                    <input type="date" name="income_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl font-bold text-gray-800 text-xs">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">आमदनी का प्रकार *</label>
                    <select name="income_type" required class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl font-bold text-gray-800 text-xs">
                        <option value="रद्दी खाली डिब्बे व स्क्रेप बिक्री (Scrap/Cartons)">📦 रद्दी खाली डिब्बे व स्क्रेप बिक्री</option>
                        <option value="कूरियर डैमेज क्लेम रिफंड (Courier Claim Refund)">🚚 कूरियर डैमेज क्लेम रिफंड</option>
                        <option value="सप्लायर कैशबैक व इंसेंटिव (Supplier Cashback)">🎁 सप्लायर कैशबैक व इंसेंटिव</option>
                        <option value="बैंक ब्याज व विविध आमदनी (Bank Interest/Misc)">🏦 बैंक ब्याज व विविध आमदनी</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">प्राप्त रकम (Amount ₹) *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-gray-400">₹</span>
                        <input type="number" step="0.01" name="amount" placeholder="उदा. 1500" required class="w-full pl-7 pr-3 py-2 bg-white border border-gray-300 rounded-xl font-mono font-black text-emerald-600 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">किससे प्राप्त हुआ (Received From)</label>
                    <input type="text" name="received_from" placeholder="उदा. कबाड़ी वाले भैया / Shiprocket Claim" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl text-xs">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">नोट्स (Notes)</label>
                    <input type="text" name="notes" placeholder="संक्षिप्त विवरण..." class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl text-xs">
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeAddIncomeModal()" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition">
                        रद्द करें
                    </button>
                    <button type="submit" id="btnSubmitIncome" class="px-5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black shadow-md transition flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>आमदनी जोड़ें</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript Engine -->
    <script>
        // Modal Controls
        function openAddExpenseModal() {
            document.getElementById('modalAddExpense').classList.remove('hidden');
        }
        function closeAddExpenseModal() {
            document.getElementById('modalAddExpense').classList.add('hidden');
        }
        function openAddIncomeModal() {
            document.getElementById('modalAddIncome').classList.remove('hidden');
        }
        function closeAddIncomeModal() {
            document.getElementById('modalAddIncome').classList.add('hidden');
        }

        // Quick add prefill helper
        function quickAddExpense(cat, paidTo, defaultAmount) {
            document.getElementById('modalExpenseCategory').value = cat;
            document.getElementById('modalExpensePaidTo').value = paidTo;
            document.getElementById('modalExpenseAmount').value = defaultAmount;
            openAddExpenseModal();
        }

        // Submit New Expense
        async function submitAddExpense(event) {
            event.preventDefault();
            const form = document.getElementById('formAddExpense');
            const formData = new FormData(form);
            const btn = document.getElementById('btnSubmitExpense');
            const origText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> सेव हो रहा है...';

            try {
                const res = await fetch("{{ route('seller.channels.pnl_budget.expense.store') }}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    closeAddExpenseModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'खर्च दर्ज हो गया!',
                        text: data.message,
                        timer: 1600,
                        showConfirmButton: false
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
                btn.innerHTML = origText;
            }
        }

        // Delete Expense
        async function deleteExpense(id) {
            const confirm = await Swal.fire({
                title: 'क्या आप यह खर्च हटाना चाहते हैं?',
                text: 'यह प्रविष्टि बहीखाते और P&L गणना से हट जाएगी।',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'हाँ, हटाएं',
                cancelButtonText: 'रद्द करें'
            });

            if (confirm.isConfirmed) {
                try {
                    const res = await fetch("{{ url('/seller/channels/pnl-budget/expenses') }}/" + id, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'हटा दिया गया!',
                            text: data.message,
                            timer: 1200,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                } catch (err) {
                    alert('एरर: ' + err.message);
                }
            }
        }

        // Submit Other Income
        async function submitAddIncome(event) {
            event.preventDefault();
            const form = document.getElementById('formAddIncome');
            const formData = new FormData(form);
            const btn = document.getElementById('btnSubmitIncome');
            const origText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> सेव हो रहा है...';

            try {
                const res = await fetch("{{ route('seller.channels.pnl_budget.income.store') }}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    closeAddIncomeModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'आमदनी जुड़ गई!',
                        text: data.message,
                        timer: 1600,
                        showConfirmButton: false
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
                btn.innerHTML = origText;
            }
        }

        // Delete Other Income
        async function deleteOtherIncome(id) {
            const confirm = await Swal.fire({
                title: 'क्या आप यह आमदनी एंट्री हटाना चाहते हैं?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                confirmButtonText: 'हाँ, हटाएं',
                cancelButtonText: 'रद्द करें'
            });

            if (confirm.isConfirmed) {
                try {
                    const res = await fetch("{{ url('/seller/channels/pnl-budget/incomes') }}/" + id, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'हटा दिया गया!',
                            timer: 1200,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                } catch (err) {
                    alert('एरर: ' + err.message);
                }
            }
        }

        // Save Target Budget Settings
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
                        text: 'आपके नए बजट लक्ष्य सेव हो गए हैं। यह आपके सभी प्रोडक्ट्स के प्राइसिंग कैलकुलेटर में भी अपडेट रहेंगे।',
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
