<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>आज का मास्टर भाव, ब्रांड-प्लांट कॉस्टिंग व AI ब्रोकर/आवाज़ इंजन - VyaparIndia</title>
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
                            <h1 class="font-black text-lg sm:text-xl text-gray-900 leading-tight">⚡ मास्टर कमोडिटी कॉस्टिंग व AI ब्रोकर/आवाज़ इंजन</h1>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300 uppercase tracking-wide">
                                Brand • Plant • GST • Die • Bhada
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">ब्रांड व प्लांट अनुसार बेसिक रेट + डाई (Die) + लोडिंग + इंश्योरेंस + GST (सहित/अतिरिक्त) + लोकल भाड़ा + मुनाफा = सटीक लाइव कीमत!</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" onclick="openCustomCommodityModal()" class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-800 border border-indigo-200 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-industry"></i>
                        <span>+ नया ब्रांड / प्लांट जोड़ें</span>
                    </button>
                    <button type="button" onclick="loadDemoSamples()" class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer" title="टेस्टिंग के लिए Kamdhenu, Jindal, UltraTech, Gold और Gehu के सैंपल बनाएं">
                        <i class="fa-solid fa-wand-magic-sparkles text-amber-600"></i>
                        <span>मल्टी-ब्रांड सैंपल लोड करें</span>
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

        <!-- 🎙️ HERO: AI VOICE & WHATSAPP BROKER MESSAGE PARSER -->
        <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-5 sm:p-6 text-white shadow-xl border border-indigo-500/30 space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                        <span>AI Broker Message & Hindi Voice Engine</span>
                    </span>
                    <h2 class="text-xl sm:text-2xl font-black mt-1.5 text-white flex items-center gap-2">
                        <span>🎙️ बोलकर या WhatsApp ब्रोकर मैसेज पेस्ट करके सभी भाव अपडेट करें</span>
                    </h2>
                    <p class="text-xs text-indigo-200 mt-1">चाहे मिल का "+ GST" या "GST Paid" मैसेज हो, लोडिंग/इंश्योरेंस/भाड़ा लिखा हो या ब्रांड व प्लांट का नाम — AI हर फॉर्मेट को खुद समझ लेगा!</p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <button type="button" id="btnVoiceMic" onclick="toggleVoiceRecognition()" class="px-5 py-3 rounded-2xl bg-gradient-to-r from-rose-500 via-pink-600 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white font-black text-sm shadow-lg shadow-rose-600/30 transition flex items-center gap-2.5 cursor-pointer active:scale-95">
                        <i class="fa-solid fa-microphone text-lg" id="micIcon"></i>
                        <span id="micBtnText">🎙️ बोलकर भाव बताएं</span>
                    </button>
                </div>
            </div>

            <!-- Broker Message / Voice Input Area -->
            <div class="flex flex-col sm:flex-row gap-2.5">
                <div class="relative flex-1">
                    <textarea id="voiceCommandInput" rows="2" placeholder='बोलें या मिल/ब्रोकर का WhatsApp मैसेज पेस्ट करें:&#10;उदा. "Kamdhenu Raipur 55500 + GST loading 250 insurance 100 bhada 1200 aur 22K Sona 73500 + GST"' class="w-full px-4 py-2.5 rounded-2xl bg-white/10 border border-indigo-400/40 text-white placeholder-indigo-300/70 text-xs sm:text-sm font-bold focus:bg-white/15 focus:border-emerald-400 focus:outline-none"></textarea>
                    <span id="listeningIndicator" class="hidden absolute right-3 top-3 px-2.5 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-black animate-pulse">
                        ● सुन रहा है...
                    </span>
                </div>
                <button type="button" onclick="parseVoiceCommand()" id="btnParseVoice" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-slate-950 font-black text-xs sm:text-sm shadow-md transition flex items-center justify-center gap-2 shrink-0 cursor-pointer">
                    <i class="fa-solid fa-robot"></i>
                    <span>🤖 AI से डिकोड करें</span>
                </button>
            </div>

            <!-- Quick Broker Examples -->
            <div class="flex items-center gap-2 flex-wrap text-[11px]">
                <span class="text-indigo-300 font-bold">टेस्ट उदाहरण (क्लिक करें):</span>
                <button type="button" onclick="useVoiceExample('Kamdhenu 56000 + GST loading 250 insurance 100 bhada 1200')" class="px-2.5 py-1 rounded-xl bg-white/10 hover:bg-white/20 text-orange-200 border border-white/10 font-bold transition cursor-pointer">
                    🏗️ Kamdhenu 56000 + GST (Load 250, Ins 100, Bhada 1200)
                </button>
                <button type="button" onclick="useVoiceExample('Jindal 62500 GST Paid bhada 1500')" class="px-2.5 py-1 rounded-xl bg-white/10 hover:bg-white/20 text-sky-200 border border-white/10 font-bold transition cursor-pointer">
                    🏭 Jindal 62500 GST Paid, Bhada 1500
                </button>
                <button type="button" onclick="useVoiceExample('UltraTech 395 GST Paid hamali 6 bhada 12')" class="px-2.5 py-1 rounded-xl bg-white/10 hover:bg-white/20 text-amber-200 border border-white/10 font-bold transition cursor-pointer">
                    🧱 UltraTech 395 GST Paid
                </button>
                <button type="button" onclick="useVoiceExample('22K सोना 73500 + GST और चांदी 94000 कर दो')" class="px-2.5 py-1 rounded-xl bg-white/10 hover:bg-white/20 text-yellow-200 border border-white/10 font-bold transition cursor-pointer">
                    💎 22K सोना 73500 + GST और चांदी 94000
                </button>
                <button type="button" onclick="useVoiceExample('MS एंगल 56500, अपोलो पाइप 66 किलो और टिन चादर 110 फीट कर दो')" class="px-2.5 py-1 rounded-xl bg-white/10 hover:bg-white/20 text-cyan-200 border border-white/10 font-bold transition cursor-pointer">
                    🛠️ एंगल 56500, पाइप 66 और टिन चादर 110 फीट
                </button>
            </div>

            <!-- AI Parsed Preview Panel -->
            <div id="voicePreviewPanel" class="hidden bg-white text-gray-900 rounded-2xl p-4 shadow-xl border-2 border-emerald-400 space-y-3">
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-lg bg-emerald-100 text-emerald-800 text-[10px] font-black" id="voiceProviderBadge">AI Broker Engine</span>
                        <h3 class="font-black text-sm text-gray-900">🤖 AI ने ये रेट व चार्जेज़ डिकोड किए हैं (पुष्टि करें):</h3>
                    </div>
                    <button type="button" onclick="document.getElementById('voicePreviewPanel').classList.add('hidden')" class="text-xs text-gray-400 hover:text-gray-600 font-bold">✕ बंद करें</button>
                </div>
                <div id="voicePreviewList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3"></div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" onclick="confirmAndApplyVoiceRates()" id="btnConfirmVoiceApply" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-check-double"></i>
                        <span>✅ हाँ, इन सभी बदलावों को लागू करें</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 📊 MASTER BASE-RATE, GST, DIE, LOADING, INSURANCE, BHADA & PROFIT PANEL -->
        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-5 space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-4">
                <div>
                    <h2 class="font-black text-base text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-indigo-600"></i>
                        <span>ब्रांड व प्लांट अनुसार पूर्ण कॉस्टिंग व रेट्स पैनल</span>
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">बेसिक रेट + GST मोड (सहित/अतिरिक्त) + डाई रेट (mm) + लोडिंग + इंश्योरेंस + प्लांट-टू-गोदाम भाड़ा + मुनाफा सेट करें।</p>
                </div>

                <!-- Industry Filter Tabs -->
                <div class="flex items-center gap-1.5 flex-wrap" id="tradeFilterTabs">
                    <button type="button" onclick="filterTradeGroup('all', this)" class="trade-tab px-3 py-1.5 rounded-xl text-xs font-black bg-gray-900 text-white cursor-pointer">सभी (All)</button>
                    <button type="button" onclick="filterTradeGroup('iron_cement', this)" class="trade-tab px-3 py-1.5 rounded-xl text-xs font-bold bg-orange-50 text-orange-900 border border-orange-200 cursor-pointer">🏗️ लोहा/सरिया & सीमेंट</button>
                    <button type="button" onclick="filterTradeGroup('fabrication_structure', this)" class="trade-tab px-3 py-1.5 rounded-xl text-xs font-bold bg-cyan-50 text-cyan-900 border border-cyan-200 cursor-pointer">🛠️ फैब्रिकेशन & शीट्स</button>
                    <button type="button" onclick="filterTradeGroup('jewellery', this)" class="trade-tab px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 text-amber-900 border border-amber-200 cursor-pointer">💎 ज्वेलरी (सोना-चांदी)</button>
                    <button type="button" onclick="filterTradeGroup('anaj_mandi', this)" class="trade-tab px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-900 border border-emerald-200 cursor-pointer">🌾 अनाज मंडी</button>
                </div>
            </div>

            <form id="masterRatesForm" onsubmit="submitMasterRates(event)" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="commodityCardsGrid">
                    @foreach($definitions as $key => $def)
                        @php
                            $p = $profiles[$key];
                            $linkedCnt = $countsByKey[$key] ?? 0;
                            $grp = $def['group'] ?? 'anaj_mandi';
                            $isTmt = str_starts_with($key, 'tmt_') || ($grp === 'iron_cement' && ($p['rate_unit'] ?? '') === 'per_ton');
                            $isFabTon = ($grp === 'fabrication_structure' && ($p['rate_unit'] ?? '') === 'per_ton');
                            $borderClass = $grp === 'jewellery' ? 'border-amber-200 bg-amber-50/20' : ($grp === 'iron_cement' ? 'border-slate-300 bg-slate-50/40' : ($grp === 'fabrication_structure' ? 'border-cyan-200 bg-cyan-50/20' : 'border-emerald-200 bg-emerald-50/20'));
                            $badgeClass = $grp === 'jewellery' ? 'bg-amber-100 text-amber-900' : ($grp === 'iron_cement' ? 'bg-orange-100 text-orange-900' : ($grp === 'fabrication_structure' ? 'bg-cyan-100 text-cyan-900' : 'bg-emerald-100 text-emerald-900'));
                        @endphp
                        <div class="commodity-rate-card p-4 rounded-2xl border {{ $borderClass }} space-y-3 transition hover:shadow-md flex flex-col justify-between" data-group="{{ $grp }}">
                            <div class="space-y-2.5">
                                <!-- Header: Title, Brand, Plant Location -->
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-start gap-2">
                                        <span class="w-8 h-8 rounded-xl {{ $badgeClass }} flex items-center justify-center text-xs shrink-0 mt-0.5">
                                            <i class="fa-solid {{ $def['icon'] ?? 'fa-tag' }}"></i>
                                        </span>
                                        <div>
                                            <h3 class="font-black text-xs text-gray-900 leading-tight">{{ $def['label'] }}</h3>
                                            <div class="flex items-center gap-1 mt-0.5 flex-wrap">
                                                @if(!empty($p['plant_location']))
                                                    <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-1.5 py-0.2 rounded border border-indigo-100">
                                                        📍 {{ $p['plant_location'] }}
                                                    </span>
                                                @endif
                                                <span class="text-[10px] font-bold text-gray-500">{{ $def['unit'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $linkedCnt > 0 ? 'bg-indigo-100 text-indigo-800 border border-indigo-200' : 'bg-gray-100 text-gray-500' }} shrink-0">
                                        {{ $linkedCnt }} जुड़े
                                    </span>
                                </div>

                                <!-- Base Rate + Unit Input -->
                                <div class="grid grid-cols-12 gap-2">
                                    <div class="col-span-7 relative">
                                        <label class="text-[10px] font-bold text-gray-500 block mb-0.5">बेसिक रेट (Base Rate ₹):</label>
                                        <div class="relative">
                                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 font-black text-gray-400 text-xs">₹</span>
                                            <input type="number" step="0.01" name="profiles[{{ $key }}][rate]" id="rate_input_{{ $key }}" value="{{ $p['rate'] }}"
                                                   class="w-full pl-6 pr-2 py-1.5 bg-white border border-gray-300 rounded-xl font-mono font-black text-sm text-gray-900 focus:border-indigo-500 focus:outline-none">
                                        </div>
                                    </div>
                                    <div class="col-span-5">
                                        <label class="text-[10px] font-bold text-gray-500 block mb-0.5">रेट यूनिट:</label>
                                        <select name="profiles[{{ $key }}][rate_unit]" class="w-full px-2 py-1.5 bg-white border border-gray-300 rounded-xl text-[11px] font-bold text-gray-800">
                                            @if($isTmt || $isFabTon)
                                                <option value="per_ton" {{ $p['rate_unit'] === 'per_ton' ? 'selected' : '' }}>प्रति टन (MT)</option>
                                                <option value="per_kg" {{ $p['rate_unit'] === 'per_kg' ? 'selected' : '' }}>प्रति Kg</option>
                                            @elseif($grp === 'fabrication_structure')
                                                <option value="per_kg" {{ $p['rate_unit'] === 'per_kg' ? 'selected' : '' }}>प्रति Kg</option>
                                                <option value="per_ft" {{ $p['rate_unit'] === 'per_ft' ? 'selected' : '' }}>प्रति फीट (Ft)</option>
                                                <option value="per_ton" {{ $p['rate_unit'] === 'per_ton' ? 'selected' : '' }}>प्रति टन (MT)</option>
                                            @elseif($grp === 'jewellery')
                                                <option value="per_10gm" {{ $p['rate_unit'] === 'per_10gm' ? 'selected' : '' }}>प्रति 10 ग्राम</option>
                                                <option value="per_kg" {{ $p['rate_unit'] === 'per_kg' ? 'selected' : '' }}>प्रति Kg</option>
                                            @elseif($grp === 'anaj_mandi')
                                                <option value="per_quintal" selected>प्रति क्विंटल</option>
                                            @else
                                                <option value="per_bag" selected>प्रति बोरी</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <!-- GST Mode Dropdown + GST % Dropdown -->
                                <div class="grid grid-cols-12 gap-2 bg-white p-2 rounded-xl border border-gray-200/80">
                                    <div class="col-span-7">
                                        <label class="text-[9px] font-bold text-gray-500 uppercase block mb-0.5">GST स्थिति:</label>
                                        <select name="profiles[{{ $key }}][gst_mode]" class="w-full px-2 py-1 bg-slate-50 border border-gray-200 rounded-lg text-[11px] font-bold text-gray-800">
                                            <option value="extra" {{ $p['gst_mode'] === 'extra' ? 'selected' : '' }}>+ बिन GST (अलग से जोड़ें)</option>
                                            <option value="included" {{ $p['gst_mode'] === 'included' ? 'selected' : '' }}>✓ GST सहित (Included)</option>
                                        </select>
                                    </div>
                                    <div class="col-span-5">
                                        <label class="text-[9px] font-bold text-gray-500 uppercase block mb-0.5">GST दर %:</label>
                                        <select name="profiles[{{ $key }}][gst_pct]" class="w-full px-2 py-1 bg-slate-50 border border-gray-200 rounded-lg text-[11px] font-bold font-mono text-gray-900">
                                            @foreach([0, 3, 5, 12, 18, 28] as $gstOpt)
                                                <option value="{{ $gstOpt }}" {{ (float)$p['gst_pct'] == (float)$gstOpt ? 'selected' : '' }}>{{ $gstOpt }}% GST</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- 🔒 Saved Fixed Costing Summary Strip (1 Baar Set Karein, Daily Nahi Badalna Padta) -->
                                <div class="bg-slate-100/80 border border-slate-200 rounded-xl p-2 space-y-1.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[10px] font-bold text-emerald-800 flex items-center gap-1" title="यह सभी चार्जेज़ सेव रहते हैं — रोज़ सिर्फ बेसिक रेट बदलें">
                                            <i class="fa-solid fa-lock text-emerald-600 text-[9px]"></i>
                                            <span>सेव्ड फिक्स कॉस्टिंग:</span>
                                        </span>
                                        <button type="button" onclick="document.getElementById('fixed_cost_panel_{{ $key }}').classList.toggle('hidden')" class="text-[10px] font-black text-indigo-700 hover:underline cursor-pointer">
                                            ✏️ फिक्स चार्जेज़ बदलें
                                        </button>
                                    </div>
                                    <div class="text-[10px] font-mono text-gray-700 leading-snug">
                                        Load ₹{{ $p['loading_charge'] }} • Ins ₹{{ $p['insurance_charge'] }} • भाड़ा ₹{{ $p['local_freight'] }} • लाभ {{ $p['profit_val'] }}{{ $p['profit_type'] === 'percent' ? '%' : '₹' }}
                                    </div>

                                    <!-- Flexible Collapsible Editor (Persisted in DB once saved) -->
                                    <div id="fixed_cost_panel_{{ $key }}" class="hidden pt-2 border-t border-slate-200 space-y-2">
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5 text-[10px]">
                                            <div>
                                                <label class="font-bold text-gray-600 block mb-0.5 truncate" title="लोडिंग / हमाली चार्ज">लोडिंग (₹):</label>
                                                <input type="number" step="0.1" name="profiles[{{ $key }}][loading_charge]" value="{{ $p['loading_charge'] }}" class="w-full px-2 py-1 bg-white border border-gray-300 rounded-lg font-mono font-bold text-xs">
                                            </div>
                                            <div>
                                                <label class="font-bold text-gray-600 block mb-0.5 truncate" title="बीमा / HUID / ब्रेकेज कवर">इंश्योरेंस (₹):</label>
                                                <input type="number" step="0.1" name="profiles[{{ $key }}][insurance_charge]" value="{{ $p['insurance_charge'] }}" class="w-full px-2 py-1 bg-white border border-gray-300 rounded-lg font-mono font-bold text-xs">
                                            </div>
                                            <div>
                                                <label class="font-bold text-indigo-700 block mb-0.5 truncate" title="प्लांट से गोदाम तक का लोकल भाड़ा">लोकल भाड़ा (₹):</label>
                                                <input type="number" step="0.5" name="profiles[{{ $key }}][local_freight]" value="{{ $p['local_freight'] }}" class="w-full px-2 py-1 bg-indigo-50/40 border border-indigo-200 rounded-lg font-mono font-bold text-xs text-indigo-900">
                                            </div>
                                            <div>
                                                <label class="font-bold text-emerald-700 block mb-0.5 truncate" title="व्यापारी शुद्ध मुनाफा">मुनाफा:</label>
                                                <div class="flex items-center gap-0.5">
                                                    <input type="number" step="0.5" name="profiles[{{ $key }}][profit_val]" value="{{ $p['profit_val'] }}" class="w-full px-1.5 py-1 bg-emerald-50/40 border border-emerald-200 rounded-lg font-mono font-bold text-xs text-emerald-900">
                                                    <select name="profiles[{{ $key }}][profit_type]" class="px-1 py-1 bg-white border border-gray-200 rounded-lg text-[9px] font-bold">
                                                        <option value="percent" {{ $p['profit_type'] === 'percent' ? 'selected' : '' }}>%</option>
                                                        @if($isTmt || $isFabTon)
                                                            <option value="flat_kg" {{ $p['profit_type'] === 'flat_kg' ? 'selected' : '' }}>₹/Kg</option>
                                                            <option value="flat_ton" {{ $p['profit_type'] === 'flat_ton' ? 'selected' : '' }}>₹/T</option>
                                                        @else
                                                            <option value="flat" {{ $p['profit_type'] === 'flat' ? 'selected' : '' }}>₹</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="font-bold text-gray-600 block mb-0.5 text-[10px]">प्लांट / मंडी लोकेशन:</label>
                                            <input type="text" name="profiles[{{ $key }}][plant_location]" value="{{ $p['plant_location'] }}" placeholder="प्लांट लोकेशन (उदा. रायपुर / इंदौर)" class="w-full text-[10px] px-2 py-1 border border-gray-300 rounded-lg bg-white font-bold text-gray-700">
                                        </div>
                                    </div>
                                </div>

                                <!-- TMT Specific: Collapsible Die Rates (mm Extra) -->
                                @if($isTmt)
                                    <div class="pt-0.5">
                                        <button type="button" onclick="document.getElementById('die_panel_{{ $key }}').classList.toggle('hidden')" class="text-[10px] font-black text-orange-700 bg-orange-100/70 hover:bg-orange-200 px-2.5 py-1 rounded-lg border border-orange-200 cursor-pointer w-full flex items-center justify-between">
                                            <span>⚙️ डाई अंतर रेट सेटिंग्स (6mm - 32mm Die)</span>
                                            <i class="fa-solid fa-chevron-down text-[9px]"></i>
                                        </button>
                                        <div id="die_panel_{{ $key }}" class="hidden p-2.5 bg-orange-50/70 border border-orange-200 rounded-xl space-y-1.5 mt-1.5">
                                            <span class="text-[9px] font-black text-orange-950 uppercase block">प्रति टन डाई अंतर (₹/Ton Extra over 12mm):</span>
                                            <div class="grid grid-cols-4 gap-1.5 text-[10px]">
                                                @foreach(['6mm', '8mm', '10mm', '12mm', '16mm', '20mm', '25mm', '32mm'] as $mm)
                                                    <div>
                                                        <label class="font-bold text-gray-600 block">{{ $mm }}:</label>
                                                        <input type="number" step="50" name="profiles[{{ $key }}][die_rates][{{ $mm }}]" value="{{ $p['die_rates'][$mm] ?? 0 }}" class="w-full px-1.5 py-0.5 bg-white border border-orange-200 rounded font-mono font-bold text-[11px]">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @elseif($key === 'gold_24k')
                                    <button type="button" onclick="autoFillGoldPuritiesFrom24K()" class="text-[10px] font-bold text-amber-800 hover:underline flex items-center gap-1 cursor-pointer">
                                        <i class="fa-solid fa-wand-sparkles text-amber-600"></i>
                                        <span>24K से 22K (91.6%) व 18K (75%) ऑटो-सेट करें</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-gray-100">
                    <p class="text-xs text-gray-600 font-bold">
                        <i class="fa-solid fa-circle-info text-indigo-600 mr-1"></i>
                        सब कुछ असली कॉस्टिंग (Basic + Die + Loading + Insurance + GST Mode + Plant-to-Godown भाड़ा + मुनाफा) से अपडेट होगा।
                    </p>
                    <button type="submit" id="btnApplyMasterRates" class="w-full sm:w-auto px-7 py-3 rounded-2xl bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-black text-sm shadow-lg shadow-indigo-600/20 transition flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                        <i class="fa-solid fa-bolt text-amber-300"></i>
                        <span>⚡ सभी प्रोडक्ट्स के रेट (कॉस्टिंग सहित) अभी अपडेट करें</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- 🔗 LINKED PRODUCTS & AUTO-CALCULATED LIVE MATRIX -->
        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h2 class="font-black text-sm sm:text-base text-gray-900">🔗 मास्टर रेट व ब्रांड से जुड़े प्रोडक्ट्स ({{ count($linkedItems) }} आइटम्स)</h2>
                    <p class="text-xs text-gray-500">यहाँ हर प्रोडक्ट का गोदाम पड़तल (Landing Cost) और विक्रय भाव का पूरा गणित देखें।</p>
                </div>
            </div>

            @if(empty($linkedItems))
                <div class="text-center py-8 px-4 border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/50 space-y-3">
                    <p class="text-xs font-bold text-gray-600">अभी कोई कमोडिटी प्रोडक्ट लिंक नहीं मिला। आप नीचे मौजूद प्रोडक्ट्स को लिंक कर सकते हैं या "मल्टी-ब्रांड सैंपल लोड करें" दबाकर देख सकते हैं।</p>
                    <button type="button" onclick="loadDemoSamples()" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-black text-xs shadow-sm cursor-pointer">
                        ⚡ Kamdhenu, Jindal, UltraTech, Gold और Gehu के सैंपल लोड करें
                    </button>
                </div>
            @else
                <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 text-gray-500 font-bold uppercase text-[10px] tracking-wider bg-gray-50 sticky top-0">
                                <th class="py-3 px-3">प्रोडक्ट / वेरिएंट</th>
                                <th class="py-3 px-3">ब्रांड व बेस लिंक</th>
                                <th class="py-3 px-3">पूर्ण कॉस्टिंग ब्रेकडाउन (Die + Load + Ins + GST + भाड़ा)</th>
                                <th class="py-3 px-3 text-right">गोदाम पड़तल</th>
                                <th class="py-3 px-3 text-right">नया सेलिंग भाव</th>
                                <th class="py-3 px-3 text-center">सेटिंग्स</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($linkedItems as $item)
                                <tr class="hover:bg-gray-50/80 transition linked-item-row" data-group="{{ $item['group'] }}">
                                    <td class="py-3 px-3 font-black text-gray-900">
                                        {{ $item['name'] }}
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 ml-1">{{ $item['source'] === 'auto_detected' ? 'Auto' : 'Linked' }}</span>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded-lg bg-indigo-50 text-indigo-800 border border-indigo-200 text-[11px] font-bold">
                                            {{ $item['commodity_label'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 font-mono text-[10px] text-gray-700 leading-snug max-w-md">
                                        {{ $item['explanation'] }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono font-bold text-gray-600">
                                        ₹{{ number_format($item['landing_cost'], 2) }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono font-black {{ abs($item['current_price'] - $item['calculated_price']) > 1 ? 'text-amber-600' : 'text-emerald-700' }}">
                                        ₹{{ number_format($item['calculated_price'], 2) }}
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <button type="button" onclick='openLinkModal(@json($item))' class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-800 font-bold text-[11px] transition cursor-pointer">
                                            ⚙️ फॉर्मूला
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
            <div class="lg:col-span-7 bg-white rounded-3xl border border-gray-200 shadow-sm p-5 space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h3 class="font-black text-sm text-gray-900">📦 अन्य प्रोडक्ट्स (इन्हें भी ब्रांड/बेस रेट इंजन से जोड़ें)</h3>
                    <p class="text-[11px] text-gray-500">किसी भी प्रोडक्ट को Kamdhenu, Jindal, Cement, Gold या मंडी भाव से जोड़ें।</p>
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

            <div class="lg:col-span-5 bg-white rounded-3xl border border-gray-200 shadow-sm p-5 space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <h3 class="font-black text-sm text-gray-900">🕒 हालिया भाव अपडेट इतिहास (Audit Log)</h3>
                    <p class="text-[11px] text-gray-500">ब्रोकर मैसेज, आवाज़ या डैशबोर्ड से किए गए सभी बदलाव</p>
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
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-gray-200 space-y-4 max-h-[90vh] overflow-y-auto">
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

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">व्यापार प्रकार:</label>
                        <select name="trade_type" id="link_trade_type" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl font-bold">
                            <option value="tmt_steel">🏗️ लोहा / सरिया (TMT Steel)</option>
                            <option value="fab_steel">🛠️ एंगल / चैनल / MS-GP पाइप (Structural)</option>
                            <option value="fab_sheet">🏠 टिन / सीमेंट रूफिंग चादर (Sheet)</option>
                            <option value="cement">🧱 सीमेंट (Cement Bag)</option>
                            <option value="jewellery">💎 ज्वेलरी (सोना-चांदी)</option>
                            <option value="mandi_grain">🌾 अनाज मंडी</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">जुड़ा ब्रांड / बेस रेट:</label>
                        <select name="commodity_key" id="link_commodity_key" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl font-bold">
                            @foreach($definitions as $k => $d)
                                <option value="{{ $k }}">{{ $d['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">वजन (gm/Kg/Bags):</label>
                        <input type="number" step="0.01" name="weight_val" id="link_weight_val" value="10.66" required class="w-full px-3 py-2 border border-gray-300 rounded-xl font-mono font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">mm साइज (TMT):</label>
                        <select name="mm_size" id="link_mm_size" class="w-full px-2 py-2 border border-gray-300 rounded-xl font-bold">
                            <option value="6mm">6mm</option>
                            <option value="8mm">8mm</option>
                            <option value="10mm">10mm</option>
                            <option value="12mm" selected>12mm</option>
                            <option value="16mm">16mm</option>
                            <option value="20mm">20mm</option>
                            <option value="25mm">25mm</option>
                            <option value="32mm">32mm</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">मेकिंग (ज्वेलरी):</label>
                        <input type="number" step="0.1" name="making_val" id="link_making_val" value="12" class="w-full px-3 py-2 border border-gray-300 rounded-xl font-mono font-bold">
                    </div>
                </div>

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalLinkFormula').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 font-bold">रद्द करें</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black shadow-md cursor-pointer">🔗 सेव व री-कैलकुलेट करें</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ➕ MODAL: ADD BRAND & PLANT LOCATION RATE PROFILE -->
    <div id="modalCustomCommodity" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-gray-200 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="font-black text-sm text-gray-900">🏭 नया ब्रांड व प्लांट लोकेशन रेट कार्ड जोड़ें</h3>
                <button type="button" onclick="document.getElementById('modalCustomCommodity').classList.add('hidden')" class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center">✕</button>
            </div>
            <form onsubmit="submitCustomCommodity(event)" class="space-y-3 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">ब्रांड / कमोडिटी नाम *</label>
                        <input type="text" name="label" required placeholder="उदा. Tata Tiscon TMT / ACC Gold" class="w-full px-3 py-2 border border-gray-300 rounded-xl font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">प्लांट / मंडी लोकेशन *</label>
                        <input type="text" name="plant_location" required placeholder="उदा. Raipur Plant / Jalna / Indore" class="w-full px-3 py-2 border border-gray-300 rounded-xl font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">व्यापार वर्ग</label>
                        <select name="group" class="w-full px-3 py-2 border border-gray-300 rounded-xl font-bold">
                            <option value="iron_cement">🏗️ लोहा / सरिया / सीमेंट</option>
                            <option value="fabrication_structure">🛠️ फैब्रिकेशन (एंगल / पाइप / टिन-सीमेंट चादर)</option>
                            <option value="anaj_mandi">🌾 अनाज मंडी</option>
                            <option value="jewellery">💎 ज्वेलरी / बुलियन</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">यूनिट डिस्प्ले *</label>
                        <input type="text" name="unit" required value="प्रति टन (MT)" class="w-full px-3 py-2 border border-gray-300 rounded-xl font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">शुरुआती बेसिक रेट *</label>
                        <input type="number" step="0.01" name="default_rate" required placeholder="56000" class="w-full px-3 py-2 border border-gray-300 rounded-xl font-mono font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">GST स्थिति</label>
                        <select name="gst_mode" class="w-full px-2 py-2 border border-gray-300 rounded-xl font-bold">
                            <option value="extra">बिन GST (+Extra)</option>
                            <option value="included">GST सहित</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">GST %</label>
                        <select name="gst_pct" class="w-full px-2 py-2 border border-gray-300 rounded-xl font-bold font-mono">
                            <option value="0">0%</option>
                            <option value="3">3%</option>
                            <option value="5">5%</option>
                            <option value="12">12%</option>
                            <option value="18" selected>18%</option>
                            <option value="28">28%</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-2">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">लोडिंग (₹)</label>
                        <input type="number" name="loading_charge" value="250" class="w-full px-2 py-2 border border-gray-300 rounded-xl font-mono font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">इंश्योरेंस (₹)</label>
                        <input type="number" name="insurance_charge" value="100" class="w-full px-2 py-2 border border-gray-300 rounded-xl font-mono font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">लोकल भाड़ा (₹)</label>
                        <input type="number" name="local_freight" value="1200" class="w-full px-2 py-2 border border-gray-300 rounded-xl font-mono font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">मुनाफा (%)</label>
                        <input type="number" step="0.5" name="profit_val" value="4" class="w-full px-2 py-2 border border-gray-300 rounded-xl font-mono font-bold">
                    </div>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalCustomCommodity').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 font-bold">रद्द करें</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white font-black cursor-pointer">ब्रांड व प्लांट सेव करें</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let pendingVoiceProfiles = {};
        let recognition = null;
        let isListening = false;

        function filterTradeGroup(group, btn) {
            document.querySelectorAll('.trade-tab').forEach(b => {
                b.classList.remove('bg-gray-900', 'text-white');
                b.classList.add('bg-gray-100', 'text-gray-700');
            });
            btn.classList.remove('bg-gray-100', 'text-gray-700');
            btn.classList.add('bg-gray-900', 'text-white');

            document.querySelectorAll('.commodity-rate-card').forEach(card => {
                card.style.display = (group === 'all' || card.dataset.group === group) ? 'flex' : 'none';
            });
            document.querySelectorAll('.linked-item-row').forEach(row => {
                row.style.display = (group === 'all' || row.dataset.group === group) ? '' : 'none';
            });
        }

        function autoFillGoldPuritiesFrom24K() {
            const k24 = parseFloat(document.getElementById('rate_input_gold_24k')?.value || 0);
            if (k24 <= 0) return;
            const k22El = document.getElementById('rate_input_gold_22k');
            const k18El = document.getElementById('rate_input_gold_18k');
            if (k22El) k22El.value = Math.round(k24 * 0.916);
            if (k18El) k18El.value = Math.round(k24 * 0.750);
            Swal.fire({ icon: 'info', title: '22K और 18K ऑटो-सेट!', text: 'अब नीचे नीला "अपडेट करें" बटन दबाएं।', timer: 1600, showConfirmButton: false });
        }

        // 🎙️ Web Speech API
        function toggleVoiceRecognition() {
            const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SpeechRec) {
                Swal.fire({ icon: 'warning', title: 'ब्राउज़र सपोर्ट नहीं करता', text: 'कृपया Chrome/Edge उपयोग करें या नीचे बॉक्स में ब्रोकर मैसेज पेस्ट/टाइप करें।' });
                return;
            }
            if (isListening && recognition) { recognition.stop(); return; }

            recognition = new SpeechRec();
            recognition.lang = 'hi-IN';
            recognition.interimResults = true;
            recognition.onstart = function() {
                isListening = true;
                document.getElementById('listeningIndicator').classList.remove('hidden');
                document.getElementById('micBtnText').textContent = '⏹️ रोकें (सुन रहा है...)';
                document.getElementById('btnVoiceMic').classList.add('animate-pulse');
            };
            recognition.onresult = function(event) {
                let transcript = '';
                for (let i = 0; i < event.results.length; i++) transcript += event.results[i][0].transcript;
                document.getElementById('voiceCommandInput').value = transcript;
            };
            recognition.onend = function() {
                isListening = false;
                document.getElementById('listeningIndicator').classList.add('hidden');
                document.getElementById('micBtnText').textContent = '🎙️ बोलकर भाव बताएं';
                document.getElementById('btnVoiceMic').classList.remove('animate-pulse');
                if (document.getElementById('voiceCommandInput').value.trim().length > 3) parseVoiceCommand();
            };
            recognition.onerror = function() {
                isListening = false;
                document.getElementById('listeningIndicator').classList.add('hidden');
                document.getElementById('micBtnText').textContent = '🎙️ बोलकर भाव बताएं';
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
            if (!transcript) { alert('कृपया बोलकर या लिखकर नया भाव या मैसेज डालें!'); return; }

            const btn = document.getElementById('btnParseVoice');
            const orig = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> डिकोड हो रहा है...';

            try {
                const res = await fetch("{{ route('seller.daily_rates.parse_voice') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ transcript })
                });
                const data = await res.json();
                const listEl = document.getElementById('voicePreviewList');
                listEl.innerHTML = '';
                pendingVoiceProfiles = {};

                if (!data.updates || data.updates.length === 0) {
                    Swal.fire({ icon: 'info', title: 'भाव स्पष्ट नहीं समझ आया', text: 'कृपया कमोडिटी/ब्रांड और कीमत स्पष्ट लिखें या बोलें।' });
                    return;
                }

                document.getElementById('voiceProviderBadge').textContent = data.provider || 'AI Engine';
                data.updates.forEach(u => {
                    pendingVoiceProfiles[u.key] = u.patch || { rate: u.new_rate };
                    const diffCls = u.diff >= 0 ? 'text-emerald-700 bg-emerald-50' : 'text-rose-700 bg-rose-50';
                    const diffSign = u.diff >= 0 ? '+' : '';
                    const gstBadge = u.gst_mode === 'included' ? 'GST सहित' : ('+' + u.gst_pct + '% GST');
                    listEl.innerHTML += `
                        <div class="p-3 rounded-xl border border-gray-200 bg-gray-50 space-y-1">
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-black text-xs text-gray-900">${u.label}</span>
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-800">${gstBadge}</span>
                            </div>
                            <div class="flex items-baseline gap-2 font-mono">
                                <span class="text-xs text-gray-400 line-through">₹${u.old_rate}</span>
                                <span class="text-base font-black text-indigo-700">→ ₹${u.new_rate}</span>
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded ${diffCls}">${diffSign}${u.diff}</span>
                            </div>
                            <div class="text-[10px] text-gray-600 font-bold">${u.reason_hi}</div>
                            <div class="text-[10px] text-emerald-700 font-black">✓ ${u.affected_items_count} आइटम अपडेट होंगे</div>
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
                    body: JSON.stringify({ profiles: pendingVoiceProfiles, source: '🎙️ AI Voice / Broker Message' })
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'लाइव अपडेट सफल!', text: data.message }).then(() => window.location.reload());
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

            const btn = document.getElementById('btnApplyMasterRates');
            const orig = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> सभी प्रोडक्ट्स अपडेट हो रहे हैं...';

            try {
                const res = await fetch("{{ route('seller.daily_rates.apply') }}", {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: fd
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'सभी भाव व कॉस्टिंग लाइव अपडेट!', text: data.message }).then(() => window.location.reload());
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
            document.getElementById('link_trade_type').value = f.trade_type || 'tmt_steel';
            document.getElementById('link_commodity_key').value = f.commodity_key || 'tmt_base_kg';
            document.getElementById('link_weight_val').value = f.weight_gm || f.weight_kg || f.bags_count || 10.66;
            document.getElementById('link_mm_size').value = f.mm_size || '12mm';
            document.getElementById('link_making_val').value = f.making_val || 12;
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
                Swal.fire({ icon: 'success', title: 'नया ब्रांड/प्लांट जोड़ा गया!', text: data.message }).then(() => window.location.reload());
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
