<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>आज का मास्टर भाव व AI आवाज़ अपडेट इंजन - VyaparIndia</title>
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
                    <a href="{{ route('seller.products.index') }}" class="h-10 w-10 rounded-2xl bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition shadow-2xs shrink-0" title="प्रोडक्ट कैटलॉग पर वापस जाएं">
                        <i class="fa-solid fa-arrow-left text-sm"></i>
                    </a>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="font-black text-lg sm:text-xl text-gray-900 leading-tight">⚡ आज का मास्टर भाव & AI आवाज़ रेट इंजन</h1>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300 uppercase tracking-wide">
                                Auto Base-Rate Sync
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">सिर्फ 1 बेसिक रेट डालें या बोलें — ज्वेलरी (सोना-चांदी), सरिया/लोहा (TMT), सीमेंट और अनाज मंडी के सभी प्रोडक्ट्स अपने आप अपडेट!</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" onclick="openCustomCommodityModal()" class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-800 border border-indigo-200 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-plus"></i>
                        <span>नया कमोडिटी ग्रुप</span>
                    </button>
                    <button type="button" onclick="loadDemoSamples()" class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer" title="टेस्टिंग के लिए सोना-चांदी, सरिया, सीमेंट और गेहूं के सैंपल प्रोडक्ट बनाएं">
                        <i class="fa-solid fa-wand-magic-sparkles text-amber-600"></i>
                        <span>सैंपल आइटम्स लोड करें</span>
                    </button>
                    <a href="{{ route('seller.products.index') }}" class="px-3 py-1.5 rounded-xl bg-gray-900 hover:bg-black text-white font-bold text-xs transition flex items-center gap-1.5 shadow-2xs">
                        <i class="fa-solid fa-boxes-stacked"></i>
                        <span>कैटलॉग देखें</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        <!-- 🎙️ HERO: AI VOICE & NATURAL HINDI RATE COMMAND BOX -->
        <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-5 sm:p-6 text-white shadow-xl border border-indigo-500/30 space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                        <span>AI Inbuilt Voice & Natural Hindi Engine</span>
                    </span>
                    <h2 class="text-xl sm:text-2xl font-black mt-1.5 text-white flex items-center gap-2">
                        <span>🎙️ बोलकर या लिखकर सभी भाव 1 सेकंड में बदलें</span>
                    </h2>
                    <p class="text-xs text-indigo-200 mt-1">माइक दबाएं और सामान्य हिंदी/हिंग्लिश में आज के भाव बोलें — AI खुद समझकर सही कमोडिटी और उससे जुड़े सारे प्रोडक्ट्स के दाम बदल देगा।</p>
                </div>

                <!-- Giant Voice Mic Button -->
                <div class="flex items-center gap-3 shrink-0">
                    <button type="button" id="btnVoiceMic" onclick="toggleVoiceRecognition()" class="px-5 py-3 rounded-2xl bg-gradient-to-r from-rose-500 via-pink-600 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white font-black text-sm shadow-lg shadow-rose-600/30 transition flex items-center gap-2.5 cursor-pointer active:scale-95">
                        <i class="fa-solid fa-microphone text-lg" id="micIcon"></i>
                        <span id="micBtnText">🎙️ बोलकर भाव बताएं (Start Mic)</span>
                    </button>
                </div>
            </div>

            <!-- Voice Input Textarea + Parse Button -->
            <div class="flex flex-col sm:flex-row gap-2.5">
                <div class="relative flex-1">
                    <input type="text" id="voiceCommandInput" placeholder='बोलें या टाइप करें: उदाहरण - "आज 22 कैरेट सोना 73500, चांदी 94000, सरिया 62 रुपये किलो और गेहूं 2750 कर दो"' class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-indigo-400/40 text-white placeholder-indigo-300/70 text-sm font-bold focus:bg-white/15 focus:border-emerald-400 focus:outline-none">
                    <span id="listeningIndicator" class="hidden absolute right-3 top-1/2 -translate-y-1/2 px-2.5 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-black animate-pulse">
                        ● सुन रहा है...
                    </span>
                </div>
                <button type="button" onclick="parseVoiceCommand()" id="btnParseVoice" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-slate-950 font-black text-xs sm:text-sm shadow-md transition flex items-center justify-center gap-2 shrink-0 cursor-pointer">
                    <i class="fa-solid fa-robot"></i>
                    <span>🤖 AI से समझें & प्रीव्यू देखें</span>
                </button>
            </div>

            <!-- Quick Example Chips -->
            <div class="flex items-center gap-2 flex-wrap text-[11px]">
                <span class="text-indigo-300 font-bold">उदाहरण क्लिक करें:</span>
                <button type="button" onclick="useVoiceExample('आज 22 कैरेट सोना 73500 और चांदी 94000 कर दो')" class="px-2.5 py-1 rounded-xl bg-white/10 hover:bg-white/20 text-amber-200 border border-white/10 font-bold transition cursor-pointer">
                    💎 "22K सोना 73500 और चांदी 94000"
                </button>
                <button type="button" onclick="useVoiceExample('सरिया 62 रुपये किलो और सीमेंट 370 बोरी कर दो')" class="px-2.5 py-1 rounded-xl bg-white/10 hover:bg-white/20 text-orange-200 border border-white/10 font-bold transition cursor-pointer">
                    🏗️ "सरिया 62 और सीमेंट 370 बोरी"
                </button>
                <button type="button" onclick="useVoiceExample('गेहूं 2750 क्विंटल और चना 6450 कर दो')" class="px-2.5 py-1 rounded-xl bg-white/10 hover:bg-white/20 text-emerald-200 border border-white/10 font-bold transition cursor-pointer">
                    🌾 "गेहूं 2750 और चना 6450"
                </button>
                <button type="button" onclick="useVoiceExample('सरिया 2 रुपये बढ़ा दो')" class="px-2.5 py-1 rounded-xl bg-white/10 hover:bg-white/20 text-sky-200 border border-white/10 font-bold transition cursor-pointer">
                    📈 "सरिया 2 रुपये बढ़ा दो"
                </button>
            </div>

            <!-- AI Parsed Preview Panel (Hidden until parsed) -->
            <div id="voicePreviewPanel" class="hidden bg-white text-gray-900 rounded-2xl p-4 shadow-xl border-2 border-emerald-400 space-y-3">
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-lg bg-emerald-100 text-emerald-800 text-[10px] font-black" id="voiceProviderBadge">AI Engine</span>
                        <h3 class="font-black text-sm text-gray-900">🤖 AI ने ये नए भाव समझे हैं (पुष्टि करें):</h3>
                    </div>
                    <button type="button" onclick="document.getElementById('voicePreviewPanel').classList.add('hidden')" class="text-xs text-gray-400 hover:text-gray-600 font-bold">✕ बंद करें</button>
                </div>
                <div id="voicePreviewList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3"></div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" onclick="confirmAndApplyVoiceRates()" id="btnConfirmVoiceApply" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-check-double"></i>
                        <span>✅ हाँ, सभी प्रोडक्ट्स के रेट अभी लाइव अपडेट करें</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 📊 MASTER BASE-RATE CONTROL PANEL -->
        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-5 space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-4">
                <div>
                    <h2 class="font-black text-base text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-indigo-600"></i>
                        <span>आज के मार्केट बेस रेट्स दर्ज करें (Master Base Rates)</span>
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">यहाँ किसी भी कमोडिटी का आज का बेस रेट बदलें और नीचे "अपडेट करें" दबाएं — जुड़े हुए सभी प्रोडक्ट्स व साइज़ खुद कैलकुलेट हो जाएंगे।</p>
                </div>

                <!-- Industry Filter Pills -->
                <div class="flex items-center gap-1.5 flex-wrap" id="tradeFilterTabs">
                    <button type="button" onclick="filterTradeGroup('all', this)" class="trade-tab px-3 py-1.5 rounded-xl text-xs font-black bg-gray-900 text-white cursor-pointer">सभी (All)</button>
                    <button type="button" onclick="filterTradeGroup('jewellery', this)" class="trade-tab px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 text-amber-900 border border-amber-200 cursor-pointer">💎 ज्वेलरी (सोना-चांदी)</button>
                    <button type="button" onclick="filterTradeGroup('iron_cement', this)" class="trade-tab px-3 py-1.5 rounded-xl text-xs font-bold bg-orange-50 text-orange-900 border border-orange-200 cursor-pointer">🏗️ लोहा/सरिया & सीमेंट</button>
                    <button type="button" onclick="filterTradeGroup('anaj_mandi', this)" class="trade-tab px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-900 border border-emerald-200 cursor-pointer">🌾 अनाज मंडी</button>
                </div>
            </div>

            <form id="masterRatesForm" onsubmit="submitMasterRates(event)" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="commodityCardsGrid">
                    @foreach($definitions as $key => $def)
                        @php
                            $val = $rates[$key] ?? $def['default_rate'];
                            $linkedCnt = $countsByKey[$key] ?? 0;
                            $grp = $def['group'] ?? 'anaj_mandi';
                            $borderClass = $grp === 'jewellery' ? 'border-amber-200 bg-amber-50/30' : ($grp === 'iron_cement' ? 'border-slate-300 bg-slate-50/60' : 'border-emerald-200 bg-emerald-50/30');
                            $badgeClass = $grp === 'jewellery' ? 'bg-amber-100 text-amber-900' : ($grp === 'iron_cement' ? 'bg-orange-100 text-orange-900' : 'bg-emerald-100 text-emerald-900');
                        @endphp
                        <div class="commodity-rate-card p-4 rounded-2xl border {{ $borderClass }} space-y-2.5 transition hover:shadow-md" data-group="{{ $grp }}">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-xl {{ $badgeClass }} flex items-center justify-center text-xs shrink-0">
                                        <i class="fa-solid {{ $def['icon'] ?? 'fa-tag' }}"></i>
                                    </span>
                                    <div>
                                        <h3 class="font-black text-xs text-gray-900 leading-tight">{{ $def['label'] }}</h3>
                                        <span class="text-[10px] font-bold text-gray-500">{{ $def['unit'] }}</span>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $linkedCnt > 0 ? 'bg-indigo-100 text-indigo-800 border border-indigo-200' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $linkedCnt }} जुड़े
                                </span>
                            </div>

                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 font-black text-gray-400 text-sm">₹</span>
                                <input type="number" step="0.01" name="rates[{{ $key }}]" id="rate_input_{{ $key }}" value="{{ $val }}"
                                       oninput="onBaseRateInputChanged('{{ $key }}', this.value)"
                                       class="w-full pl-7 pr-3 py-2 bg-white border border-gray-300 rounded-xl font-mono font-black text-base text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none">
                            </div>

                            @if($key === 'gold_24k')
                                <button type="button" onclick="autoFillGoldPuritiesFrom24K()" class="text-[10px] font-bold text-amber-800 hover:underline flex items-center gap-1 cursor-pointer">
                                    <i class="fa-solid fa-wand-sparkles text-amber-600"></i>
                                    <span>24K से 22K (91.6%) व 18K (75%) ऑटो-सेट करें</span>
                                </button>
                            @elseif($key === 'tmt_base_kg')
                                <span class="text-[10px] text-gray-500 font-bold block">
                                    ≈ ₹<span id="tmt_ton_equiv">{{ number_format($val * 1000) }}</span> / टन (MT)
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-gray-100">
                    <p class="text-xs text-gray-600 font-bold">
                        <i class="fa-solid fa-circle-info text-indigo-600 mr-1"></i>
                        सब कुछ ऑटो-फॉर्मूला (वजन / मेकिंग चार्ज / हमाली / GST / मुनाफा) के आधार पर अपडेट होगा।
                    </p>
                    <button type="submit" id="btnApplyMasterRates" class="w-full sm:w-auto px-7 py-3 rounded-2xl bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-black text-sm shadow-lg shadow-indigo-600/20 transition flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                        <i class="fa-solid fa-bolt text-amber-300"></i>
                        <span>⚡ सभी जुड़े प्रोडक्ट्स के भाव अभी अपडेट करें</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- 🔗 LINKED PRODUCTS & AUTO-CALCULATED LIVE MATRIX -->
        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h2 class="font-black text-sm sm:text-base text-gray-900">🔗 मास्टर रेट से जुड़े हुए प्रोडक्ट्स व ऑटो-कैलकुलेशन ({{ count($linkedItems) }} आइटम्स)</h2>
                    <p class="text-xs text-gray-500">हर आइटम का नाम, जुड़ा हुआ बेस रेट, सटीक फॉर्मूला व लाइव रेट यहाँ देखें।</p>
                </div>
            </div>

            @if(empty($linkedItems))
                <div class="text-center py-8 px-4 border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/50 space-y-3">
                    <p class="text-xs font-bold text-gray-600">अभी कोई कमोडिटी प्रोडक्ट लिंक नहीं मिला। आप नीचे मौजूद प्रोडक्ट्स को लिंक कर सकते हैं या टेस्ट करने के लिए "सैंपल आइटम्स लोड करें" बटन दबा सकते हैं।</p>
                    <button type="button" onclick="loadDemoSamples()" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-black text-xs shadow-sm cursor-pointer">
                        ⚡ सोना-चांदी, सरिया, सीमेंट व गेहूं के सैंपल लोड करें
                    </button>
                </div>
            @else
                <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 text-gray-500 font-bold uppercase text-[10px] tracking-wider bg-gray-50 sticky top-0">
                                <th class="py-3 px-3">प्रोडक्ट / वेरिएंट</th>
                                <th class="py-3 px-3">बेस कमोडिटी लिंक</th>
                                <th class="py-3 px-3">ऑटो-फॉर्मूला (Weight / Making / Hamali / GST)</th>
                                <th class="py-3 px-3 text-right">वर्तमान सेव रेट</th>
                                <th class="py-3 px-3 text-right">बेस रेट अनुसार भाव</th>
                                <th class="py-3 px-3 text-center">सेटिंग्स</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($linkedItems as $item)
                                <tr class="hover:bg-gray-50/80 transition linked-item-row" data-group="{{ $item['group'] }}">
                                    <td class="py-3 px-3 font-black text-gray-900">
                                        {{ $item['name'] }}
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 ml-1">{{ $item['source'] === 'auto_detected' ? 'Auto-Detected' : 'Linked' }}</span>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded-lg bg-indigo-50 text-indigo-800 border border-indigo-200 text-[11px] font-bold">
                                            {{ $item['commodity_label'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 font-mono text-[11px] text-gray-600">
                                        {{ $item['explanation'] }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono font-black text-gray-900">
                                        ₹{{ number_format($item['current_price'], 2) }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono font-black {{ abs($item['current_price'] - $item['calculated_price']) > 1 ? 'text-amber-600' : 'text-emerald-700' }}">
                                        ₹{{ number_format($item['calculated_price'], 2) }}
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <button type="button" onclick='openLinkModal(@json($item))' class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-800 font-bold text-[11px] transition cursor-pointer">
                                            ⚙️ फॉर्मूला बदलें
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- 📦 UNLINKED PRODUCTS + 🕒 HISTORY -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Unlinked Products (7 Cols) -->
            <div class="lg:col-span-7 bg-white rounded-3xl border border-gray-200 shadow-sm p-5 space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h3 class="font-black text-sm text-gray-900">📦 अन्य प्रोडक्ट्स (इन्हें भी मास्टर बेस रेट से जोड़ें)</h3>
                    <p class="text-[11px] text-gray-500">किसी भी प्रोडक्ट को सोना, सरिया, सीमेंट या मंडी भाव से जोड़ें ताकि वह भी 1 क्लिक में अपडेट हो।</p>
                </div>

                @if(empty($unlinkedProducts))
                    <p class="text-xs text-emerald-700 font-bold py-4 text-center">✓ आपके सभी उपयुक्त प्रोडक्ट्स सफलतापूर्वक लिंक हैं!</p>
                @else
                    <div class="max-h-72 overflow-y-auto divide-y divide-gray-100">
                        @foreach($unlinkedProducts as $up)
                            <div class="py-2.5 flex items-center justify-between gap-2 text-xs">
                                <div>
                                    <div class="font-bold text-gray-900">{{ $up->name }}</div>
                                    <span class="text-[10px] text-gray-400">वर्तमान रेट: ₹{{ number_format($up->price, 2) }}</span>
                                </div>
                                <button type="button" onclick="openNewLinkModal({{ $up->id }}, '{{ addslashes($up->name) }}')" class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-800 font-bold text-[11px] border border-indigo-200 shrink-0 cursor-pointer">
                                    🔗 बेस रेट से जोड़ें
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Update History (5 Cols) -->
            <div class="lg:col-span-5 bg-white rounded-3xl border border-gray-200 shadow-sm p-5 space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h3 class="font-black text-sm text-gray-900">🕒 हालिया भाव अपडेट इतिहास (Audit Log)</h3>
                    <p class="text-[11px] text-gray-500">बोलकर या डैशबोर्ड से किए गए सभी लाइव बदलाव</p>
                </div>

                @if(empty($history))
                    <p class="text-xs text-gray-500 py-6 text-center">अभी तक कोई मास्टर रेट अपडेट लॉग नहीं हुआ है।</p>
                @else
                    <div class="max-h-72 overflow-y-auto space-y-2.5">
                        @foreach($history as $log)
                            <div class="p-3 rounded-2xl bg-gray-50 border border-gray-200/70 text-xs space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-black text-indigo-800 text-[11px]">{{ $log['source'] ?? 'Rate Update' }}</span>
                                    <span class="text-[10px] text-gray-400 font-mono">{{ $log['timestamp'] ?? '' }}</span>
                                </div>
                                <p class="text-[11px] font-bold text-gray-800">{{ $log['summary'] ?? '' }}</p>
                                <span class="text-[10px] text-emerald-700 font-bold block">✓ {{ $log['products_updated'] ?? 0 }} प्रोडक्ट्स / {{ $log['variants_updated'] ?? 0 }} वेरिएंट्स अपडेट</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </main>

    <!-- 🔗 MODAL: CONFIGURE PRODUCT FORMULA -->
    <div id="modalLinkFormula" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-200 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h3 class="font-black text-sm text-gray-900">⚙️ प्रोडक्ट ऑटो-प्राइसिंग फॉर्मूला सेट करें</h3>
                    <p class="text-xs text-indigo-600 font-bold" id="linkModalProdName"></p>
                </div>
                <button type="button" onclick="document.getElementById('modalLinkFormula').classList.add('hidden')" class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center">✕</button>
            </div>

            <form id="formLinkFormula" onsubmit="submitLinkFormula(event)" class="space-y-3 text-xs">
                @csrf
                <input type="hidden" name="product_id" id="link_product_id">
                <input type="hidden" name="variant_id" id="link_variant_id">

                <div>
                    <label class="block font-bold text-gray-700 mb-1">व्यापार प्रकार (Trade Type):</label>
                    <select name="trade_type" id="link_trade_type" onchange="toggleFormulaFields()" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl font-bold">
                        <option value="jewellery">💎 ज्वेलरी (सोना-चांदी)</option>
                        <option value="tmt_steel">🏗️ लोहा / सरिया (TMT Steel)</option>
                        <option value="cement">🧱 सीमेंट (Cement Bag)</option>
                        <option value="mandi_grain">🌾 अनाज मंडी (Grain / Commodity)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">जुड़ा बेस कमोडिटी चुनें:</label>
                    <select name="commodity_key" id="link_commodity_key" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl font-bold">
                        @foreach($definitions as $k => $d)
                            <option value="{{ $k }}">{{ $d['label'] }} ({{ $d['unit'] }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1" id="lbl_weight_val">वजन (Grams / Kg / Bags):</label>
                        <input type="number" step="0.01" name="weight_val" id="link_weight_val" value="10" required class="w-full px-3 py-2 border border-gray-300 rounded-xl font-mono font-bold">
                    </div>
                    <div id="wrap_making_val">
                        <label class="block font-bold text-gray-700 mb-1">मेकिंग चार्ज (%/₹):</label>
                        <input type="number" step="0.1" name="making_val" id="link_making_val" value="12" class="w-full px-3 py-2 border border-gray-300 rounded-xl font-mono font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1" id="lbl_extra_charge">हमाली / लोडिंग / एक्स्ट्रा (₹):</label>
                        <input type="number" step="0.1" name="extra_charge" id="link_extra_charge" value="10" class="w-full px-3 py-2 border border-gray-300 rounded-xl font-mono font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">मुनाफा मार्जिन (%):</label>
                        <input type="number" step="0.1" name="margin_pct" id="link_margin_pct" value="4" class="w-full px-3 py-2 border border-gray-300 rounded-xl font-mono font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">GST (%):</label>
                        <input type="number" step="0.5" name="gst_pct" id="link_gst_pct" value="3" class="w-full px-3 py-2 border border-gray-300 rounded-xl font-mono font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">मेकिंग प्रकार (ज्वेलरी):</label>
                        <select name="making_type" id="link_making_type" class="w-full px-3 py-2 border border-gray-300 rounded-xl font-bold">
                            <option value="percent">प्रतिशत (%)</option>
                            <option value="per_gm">प्रति ग्राम (₹/gm)</option>
                            <option value="flat">फिक्स (₹)</option>
                        </select>
                    </div>
                </div>

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalLinkFormula').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 font-bold">रद्द करें</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black shadow-md cursor-pointer">🔗 सेव व री-कैलकुलेट करें</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ➕ MODAL: ADD CUSTOM COMMODITY GROUP -->
    <div id="modalCustomCommodity" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-200 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="font-black text-sm text-gray-900">➕ नया कमोडिटी बेस ग्रुप जोड़ें</h3>
                <button type="button" onclick="document.getElementById('modalCustomCommodity').classList.add('hidden')" class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center">✕</button>
            </div>
            <form onsubmit="submitCustomCommodity(event)" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">कमोडिटी का नाम *</label>
                    <input type="text" name="label" required placeholder="उदा. मक्का (Maize) / जीरा / तांबा (Copper)" class="w-full px-3 py-2 border border-gray-300 rounded-xl font-bold">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">यूनिट *</label>
                    <input type="text" name="unit" required placeholder="उदा. प्रति क्विंटल / प्रति Kg / प्रति टीन" class="w-full px-3 py-2 border border-gray-300 rounded-xl font-bold">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">व्यापार वर्ग</label>
                    <select name="group" class="w-full px-3 py-2 border border-gray-300 rounded-xl font-bold">
                        <option value="anaj_mandi">🌾 अनाज मंडी / किराना कमोडिटी</option>
                        <option value="iron_cement">🏗️ बिल्डिंग मटेरियल / मेटल</option>
                        <option value="jewellery">💎 बुलियन / जेम्स</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">शुरुआती बेस रेट (₹) *</label>
                    <input type="number" step="0.01" name="default_rate" required placeholder="उदा. 2400" class="w-full px-3 py-2 border border-gray-300 rounded-xl font-mono font-bold">
                </div>
                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalCustomCommodity').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 font-bold">रद्द करें</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white font-black cursor-pointer">सेव करें</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let pendingVoiceRates = {};
        let recognition = null;
        let isListening = false;

        // Filter trade groups
        function filterTradeGroup(group, btn) {
            document.querySelectorAll('.trade-tab').forEach(b => {
                b.classList.remove('bg-gray-900', 'text-white');
                b.classList.add('bg-gray-100', 'text-gray-700');
            });
            btn.classList.remove('bg-gray-100', 'text-gray-700');
            btn.classList.add('bg-gray-900', 'text-white');

            document.querySelectorAll('.commodity-rate-card').forEach(card => {
                card.style.display = (group === 'all' || card.dataset.group === group) ? 'block' : 'none';
            });
            document.querySelectorAll('.linked-item-row').forEach(row => {
                row.style.display = (group === 'all' || row.dataset.group === group) ? '' : 'none';
            });
        }

        function onBaseRateInputChanged(key, val) {
            if (key === 'tmt_base_kg') {
                const el = document.getElementById('tmt_ton_equiv');
                if (el) el.textContent = Math.round(parseFloat(val || 0) * 1000).toLocaleString('en-IN');
            }
        }

        function autoFillGoldPuritiesFrom24K() {
            const k24 = parseFloat(document.getElementById('rate_input_gold_24k')?.value || 0);
            if (k24 <= 0) return;
            const k22El = document.getElementById('rate_input_gold_22k');
            const k18El = document.getElementById('rate_input_gold_18k');
            if (k22El) k22El.value = Math.round(k24 * 0.916);
            if (k18El) k18El.value = Math.round(k24 * 0.750);
            Swal.fire({ icon: 'info', title: '22K और 18K ऑटो-सेट!', text: 'अब नीचे नीला "अपडेट करें" बटन दबाकर सभी गहनों के दाम अपडेट करें।', timer: 1800, showConfirmButton: false });
        }

        // 🎙️ Web Speech API (Hindi + English)
        function toggleVoiceRecognition() {
            const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SpeechRec) {
                Swal.fire({
                    icon: 'warning',
                    title: 'ब्राउज़र सपोर्ट नहीं करता',
                    text: 'आपका ब्राउज़र सीधे माइक सपोर्ट नहीं कर रहा (कृपया Chrome/Edge उपयोग करें या नीचे बॉक्स में हिंदी/हिंग्लिश में टाइप करके "AI से समझें" दबाएं)।'
                });
                return;
            }

            if (isListening && recognition) {
                recognition.stop();
                return;
            }

            recognition = new SpeechRec();
            recognition.lang = 'hi-IN';
            recognition.interimResults = true;
            recognition.maxAlternatives = 1;

            recognition.onstart = function() {
                isListening = true;
                document.getElementById('listeningIndicator').classList.remove('hidden');
                document.getElementById('micBtnText').textContent = '⏹️ रोकें (Listening...)';
                document.getElementById('btnVoiceMic').classList.add('animate-pulse');
            };

            recognition.onresult = function(event) {
                let transcript = '';
                for (let i = 0; i < event.results.length; i++) {
                    transcript += event.results[i][0].transcript;
                }
                document.getElementById('voiceCommandInput').value = transcript;
            };

            recognition.onend = function() {
                isListening = false;
                document.getElementById('listeningIndicator').classList.add('hidden');
                document.getElementById('micBtnText').textContent = '🎙️ बोलकर भाव बताएं (Start Mic)';
                document.getElementById('btnVoiceMic').classList.remove('animate-pulse');
                if (document.getElementById('voiceCommandInput').value.trim().length > 3) {
                    parseVoiceCommand();
                }
            };

            recognition.onerror = function(e) {
                isListening = false;
                document.getElementById('listeningIndicator').classList.add('hidden');
                document.getElementById('micBtnText').textContent = '🎙️ बोलकर भाव बताएं (Start Mic)';
                document.getElementById('btnVoiceMic').classList.remove('animate-pulse');
            };

            recognition.start();
        }

        function useVoiceExample(txt) {
            document.getElementById('voiceCommandInput').value = txt;
            parseVoiceCommand();
        }

        async function parseVoiceCommand() {
            const transcript = document.getElementById('voiceCommandInput').value.trim();
            if (!transcript) {
                alert('कृपया बोलकर या लिखकर नया भाव बताएं!');
                return;
            }

            const btn = document.getElementById('btnParseVoice');
            const orig = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> AI समझ रहा है...';

            try {
                const res = await fetch("{{ route('seller.daily_rates.parse_voice') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ transcript })
                });
                const data = await res.json();
                const listEl = document.getElementById('voicePreviewList');
                listEl.innerHTML = '';
                pendingVoiceRates = {};

                if (!data.updates || data.updates.length === 0) {
                    Swal.fire({ icon: 'info', title: 'भाव स्पष्ट नहीं समझ आया', text: 'कृपया स्पष्ट बोलें/लिखें, जैसे: "सोना 73500 कर दो, सरिया 62 रुपये या गेहूं 2750"' });
                    return;
                }

                document.getElementById('voiceProviderBadge').textContent = data.provider || 'AI Voice';
                data.updates.forEach(u => {
                    pendingVoiceRates[u.key] = u.new_rate;
                    const diffCls = u.diff >= 0 ? 'text-emerald-700 bg-emerald-50' : 'text-rose-700 bg-rose-50';
                    const diffSign = u.diff >= 0 ? '+' : '';
                    listEl.innerHTML += `
                        <div class="p-3 rounded-xl border border-gray-200 bg-gray-50 space-y-1">
                            <div class="font-black text-xs text-gray-900">${u.label}</div>
                            <div class="flex items-baseline gap-2 font-mono">
                                <span class="text-xs text-gray-400 line-through">₹${u.old_rate}</span>
                                <span class="text-base font-black text-indigo-700">→ ₹${u.new_rate}</span>
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded ${diffCls}">${diffSign}${u.diff}</span>
                            </div>
                            <div class="text-[10px] text-gray-600 font-bold">✓ ${u.affected_items_count} प्रोडक्ट्स/वेरिएंट्स बदलेंगे</div>
                        </div>
                    `;
                });

                document.getElementById('voicePreviewPanel').classList.remove('hidden');
            } catch (err) {
                alert('Error: ' + err.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = orig;
            }
        }

        async function confirmAndApplyVoiceRates() {
            const btn = document.getElementById('btnConfirmVoiceApply');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> अपडेट हो रहा है...';

            try {
                const res = await fetch("{{ route('seller.daily_rates.apply') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ rates: pendingVoiceRates, source: '🎙️ AI Voice Command' })
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'बोलकर भाव अपडेट सफल!', text: data.message }).then(() => window.location.reload());
                } else {
                    alert(data.message || 'त्रुटि आई');
                }
            } catch (e) {
                alert('Error: ' + e.message);
            }
        }

        async function submitMasterRates(event) {
            event.preventDefault();
            const form = document.getElementById('masterRatesForm');
            const fd = new FormData(form);
            const rates = {};
            fd.forEach((v, k) => {
                const match = k.match(/^rates\[(.+)\]$/);
                if (match && v !== '') rates[match[1]] = parseFloat(v);
            });

            const btn = document.getElementById('btnApplyMasterRates');
            const orig = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> सभी प्रोडक्ट्स अपडेट हो रहे हैं...';

            try {
                const res = await fetch("{{ route('seller.daily_rates.apply') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ rates, source: '⚡ Master Base-Rate Panel' })
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'सभी भाव लाइव अपडेट!', text: data.message }).then(() => window.location.reload());
                } else {
                    alert(data.message || 'त्रुटि आई');
                }
            } catch (err) {
                alert('Error: ' + err.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = orig;
            }
        }

        function openLinkModal(item) {
            document.getElementById('linkModalProdName').textContent = item.name;
            document.getElementById('link_product_id').value = item.product_id;
            document.getElementById('link_variant_id').value = item.variant_id || '';
            const f = item.formula || {};
            document.getElementById('link_trade_type').value = f.trade_type || 'mandi_grain';
            document.getElementById('link_commodity_key').value = f.commodity_key || 'anaj_gehu';
            document.getElementById('link_weight_val').value = f.weight_gm || f.weight_kg || f.bags_count || 10;
            document.getElementById('link_making_val').value = f.making_val || 12;
            document.getElementById('link_extra_charge').value = f.extra_per_kg || f.hamali_freight || f.hamali_bardana || 10;
            document.getElementById('link_margin_pct').value = f.margin_pct || 4;
            document.getElementById('link_gst_pct').value = f.gst_pct || 3;
            document.getElementById('modalLinkFormula').classList.remove('hidden');
        }

        function openNewLinkModal(prodId, prodName) {
            document.getElementById('linkModalProdName').textContent = prodName;
            document.getElementById('link_product_id').value = prodId;
            document.getElementById('link_variant_id').value = '';
            document.getElementById('modalLinkFormula').classList.remove('hidden');
        }

        async function submitLinkFormula(event) {
            event.preventDefault();
            const fd = new FormData(document.getElementById('formLinkFormula'));
            try {
                const res = await fetch("{{ route('seller.daily_rates.link_product') }}", {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: fd
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'लिंक हो गया!', text: data.message }).then(() => window.location.reload());
                } else {
                    alert(data.message || 'Error');
                }
            } catch (e) {
                alert('Error: ' + e.message);
            }
        }

        function openCustomCommodityModal() {
            document.getElementById('modalCustomCommodity').classList.remove('hidden');
        }

        async function submitCustomCommodity(event) {
            event.preventDefault();
            const fd = new FormData(event.target);
            const res = await fetch("{{ route('seller.daily_rates.add_custom') }}", {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: fd
            });
            const data = await res.json();
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'कमोडिटी जोड़ी गई!', text: data.message }).then(() => window.location.reload());
            }
        }

        async function loadDemoSamples() {
            const res = await fetch("{{ route('seller.daily_rates.load_demo') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ trade: 'all' })
            });
            const data = await res.json();
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'सैंपल तैयार!', text: data.message }).then(() => window.location.reload());
            }
        }
    </script>
</body>
</html>
