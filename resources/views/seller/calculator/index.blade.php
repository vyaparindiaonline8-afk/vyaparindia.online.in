<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>मार्केटप्लेस यूनिट इकोनॉमिक्स व प्रॉफिट कैलकुलेटर - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        input[type="number"]::-webkit-inner-spin-button, 
        input[type="number"]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen">

    <!-- Header Navigation -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.channels.index') }}" class="h-10 w-10 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition shadow-2xs" title="मल्टी-चैनल हब पर वापस जाएँ">
                        <i class="fa-solid fa-arrow-left text-sm"></i>
                    </a>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="font-black text-lg sm:text-xl text-slate-900 leading-tight">मार्केटप्लेस यूनिट इकोनॉमिक्स व प्रॉफिट कैलकुलेटर</h1>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wide">
                                💰 लाइव रेट तुलना
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">लागत, RTO, कूरियर व कमीशन एडजस्ट करके Amazon, Meesho, Flipkart व Direct की आमने-सामने तुलना करें</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('seller.channels.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-300 transition flex items-center gap-1.5 shadow-2xs">
                        <i class="fa-solid fa-network-wired text-indigo-600"></i>
                        <span>मल्टी-चैनल हब</span>
                    </a>
                    <button type="button" onclick="copyAllCalculatedRates()" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-black text-xs shadow-xs transition flex items-center gap-1.5 active:scale-95 cursor-pointer">
                        <i class="fa-solid fa-copy text-amber-300"></i>
                        <span>📋 सभी रेट्स कॉपी करें</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        <!-- Top Master Control Card -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-sm space-y-5">
            <div class="flex items-center justify-between flex-wrap gap-3 border-b border-slate-100 pb-4">
                <div class="flex items-center gap-2.5">
                    <span class="h-9 w-9 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base border border-indigo-100 font-black">
                        <i class="fa-solid fa-sliders"></i>
                    </span>
                    <div>
                        <h2 class="font-black text-base text-slate-900">कैलकुलेटर इनपुट्स व बेसिक खर्चे</h2>
                        <p class="text-xs text-slate-500">अपनी खरीद लागत या डिस्काउंट बदलें, सभी 4 प्लेटफ़ॉर्म के आंकड़े खुद ब खुद बदलेंगे</p>
                    </div>
                </div>

                <!-- Quick Category Presets -->
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="text-xs font-bold text-slate-400 mr-1">कैटेगरी प्रीसेट:</span>
                    <button type="button" onclick="applyPreset('plumbing')" class="px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold border border-blue-200 transition">
                        🚿 प्लंबिंग / सेनेटरी (12% Amz)
                    </button>
                    <button type="button" onclick="applyPreset('hardware')" class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-bold border border-amber-200 transition">
                        🔩 हार्डवेयर व टूल्स (10% Amz)
                    </button>
                    <button type="button" onclick="applyPreset('apparel')" class="px-3 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-bold border border-purple-200 transition">
                        👕 गारमेंट्स / कपड़े (14% Amz)
                    </button>
                </div>
            </div>

            <!-- In-Store Product Auto-Loader -->
            @if(isset($products) && $products->isNotEmpty())
            <div class="p-3.5 rounded-2xl bg-indigo-50/70 border border-indigo-100 flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-2 text-xs text-indigo-900 font-bold">
                    <i class="fa-solid fa-store text-indigo-600 text-sm"></i>
                    <span>दुकान के प्रोडक्ट से लोड करें:</span>
                </div>
                <div class="w-full sm:w-auto flex-1 max-w-md">
                    <select id="product_selector" onchange="loadProductIntoCalculator(this.value)" class="w-full text-xs font-semibold py-2 px-3 rounded-xl border border-indigo-200 bg-white text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden">
                        <option value="">-- कस्टम रेट (अपनी मर्जी से लागत डालें) --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" 
                                data-cost="{{ $p->purchase_price ?: 250 }}" 
                                data-mrp="{{ $p->mrp ?: ($p->price ?: 340) }}"
                                data-discount="{{ $p->trade_discount_percent ?: 0 }}"
                                data-name="{{ $p->name }}"
                                data-sku="{{ $p->sku }}">
                                {{ $p->name }} (SKU: {{ $p->sku ?: 'N/A' }} | लागत: ₹{{ number_format($p->purchase_price ?: 250, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif

            <!-- 6 Key Input Fields Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5 sm:gap-4">
                
                <!-- 1. Catalog / List Price (MRP) -->
                <div class="space-y-1">
                    <label class="block text-xs font-black text-slate-700">कैटलॉग प्रिंटेड भाव (₹)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-bold">₹</span>
                        <input type="number" id="inp_mrp" value="{{ $defaultMrp ?: 340 }}" step="any" oninput="onMrpOrDiscountChange()" class="w-full pl-7 pr-3 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white text-xs font-black text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden transition">
                    </div>
                    <span class="text-[10px] text-slate-400 block">प्रिंटेड लिस्ट रेट</span>
                </div>

                <!-- 2. Trade Discount % (Less) -->
                <div class="space-y-1">
                    <label class="block text-xs font-black text-slate-700">ट्रेड डिस्काउंट (%)</label>
                    <div class="relative">
                        <input type="number" id="inp_discount" value="{{ $defaultDiscount ?: 0 }}" step="any" oninput="onMrpOrDiscountChange()" class="w-full pl-3 pr-7 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white text-xs font-black text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden transition">
                        <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-bold">%</span>
                    </div>
                    <span class="text-[10px] text-slate-400 block">कमीशन / छूट</span>
                </div>

                <!-- 3. Net Purchase Cost (Core Input) -->
                <div class="space-y-1">
                    <label class="block text-xs font-black text-indigo-700 flex items-center justify-between">
                        <span>खरीद लागत (₹)*</span>
                        <span class="text-[10px] bg-indigo-100 text-indigo-700 px-1.5 py-0.2 rounded font-black">Core</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-xs text-indigo-600 font-black">₹</span>
                        <input type="number" id="inp_cost" value="{{ $defaultCost ?: 250 }}" step="any" oninput="recalcAllPlatforms()" class="w-full pl-7 pr-3 py-2 rounded-xl border-2 border-indigo-400 bg-indigo-50/40 text-xs font-black text-indigo-950 focus:ring-2 focus:ring-indigo-600 focus:outline-hidden transition shadow-2xs">
                    </div>
                    <span class="text-[10px] text-indigo-600 font-bold block">आपकी शुद्ध खरीद</span>
                </div>

                <!-- 4. Packaging Cost -->
                <div class="space-y-1">
                    <label class="block text-xs font-black text-slate-700">पैकिंग खर्च (₹)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-bold">₹</span>
                        <input type="number" id="inp_packaging" value="15" step="any" oninput="recalcAllPlatforms()" class="w-full pl-7 pr-3 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white text-xs font-bold text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden transition">
                    </div>
                    <span class="text-[10px] text-slate-400 block">बॉक्स, टेप, बबल</span>
                </div>

                <!-- 5. RTO Safety Buffer -->
                <div class="space-y-1">
                    <label class="block text-xs font-black text-slate-700">RTO रिजर्व फंड (₹)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-bold">₹</span>
                        <input type="number" id="inp_rto" value="20" step="any" oninput="recalcAllPlatforms()" class="w-full pl-7 pr-3 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white text-xs font-bold text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden transition">
                    </div>
                    <span class="text-[10px] text-slate-400 block">रिटर्न नुकसान फंड</span>
                </div>

                <!-- 6. Target Profit Margin % -->
                <div class="space-y-1">
                    <label class="block text-xs font-black text-slate-700">मनचाहा मुनाफा (%)</label>
                    <div class="relative">
                        <input type="number" id="inp_margin" value="25" step="any" oninput="recalcAllPlatforms()" class="w-full pl-3 pr-7 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white text-xs font-black text-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden transition">
                        <span class="absolute right-3 top-2.5 text-xs text-emerald-600 font-bold">%</span>
                    </div>
                    <span class="text-[10px] text-slate-400 block">टारगेट शुद्ध मार्जिन</span>
                </div>

            </div>

            <!-- Additional Global Adjustments (GST & Delivery Base) -->
            <div class="flex items-center justify-between flex-wrap gap-4 pt-3 border-t border-slate-100 text-xs">
                <div class="flex items-center gap-4 flex-wrap">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-500">प्रोडक्ट GST दर:</span>
                        <select id="inp_gst" onchange="recalcAllPlatforms()" class="py-1 px-2.5 rounded-lg border border-slate-200 bg-white font-bold text-slate-800 text-xs focus:ring-1 focus:ring-indigo-500">
                            <option value="18" selected>18% (मानक हार्डवेयर / फिटिंग्स)</option>
                            <option value="12">12% (सैनिटरी / बिल्डिंग)</option>
                            <option value="5">5% (कपड़े / खाद्यान्न)</option>
                            <option value="28">28% (लग्जरी)</option>
                            <option value="0">0% (निल / गैर-जीएसटी)</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-500">औसत कूरियर भाड़ा (500g):</span>
                        <div class="inline-flex rounded-lg border border-slate-200 bg-slate-50 p-0.5">
                            <button type="button" onclick="setShippingBase(45)" id="btn_ship_local" class="px-2.5 py-1 rounded-md text-xs font-bold bg-white text-indigo-700 shadow-2xs">लोकल (₹45)</button>
                            <button type="button" onclick="setShippingBase(65)" id="btn_ship_reg" class="px-2.5 py-1 rounded-md text-xs font-bold text-slate-600">रीजनल (₹65)</button>
                            <button type="button" onclick="setShippingBase(75)" id="btn_ship_nat" class="px-2.5 py-1 rounded-md text-xs font-bold text-slate-600">नेशनल (₹75)</button>
                        </div>
                    </div>
                </div>

                <div class="text-[11px] text-slate-400 flex items-center gap-1 font-semibold">
                    <i class="fa-solid fa-circle-info text-indigo-500"></i>
                    <span>सभी फ़ीस व फॉर्मूले में 18% GST गैप सुरक्षा व इन-हैंड कैश गारंटी लागू है।</span>
                </div>
            </div>
        </div>

        <!-- 4 Platforms Side-by-Side Comparison Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

            <!-- 1. 🟠 AMAZON INDIA CARD -->
            <div class="bg-white rounded-3xl p-5 border-2 border-amber-300 shadow-md relative overflow-hidden flex flex-col justify-between space-y-4">
                <div class="absolute -right-8 -top-8 w-24 h-24 bg-amber-100 rounded-full opacity-40 pointer-events-none"></div>

                <div class="space-y-3">
                    <!-- Platform Header -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="h-9 w-9 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-black text-lg shadow-xs">
                                <i class="fa-brands fa-amazon"></i>
                            </span>
                            <div>
                                <h3 class="font-black text-sm text-slate-900 leading-tight">Amazon India</h3>
                                <span class="text-[10px] text-amber-700 font-bold bg-amber-50 px-1.5 py-0.2 rounded border border-amber-200">Easy Ship + Referral</span>
                            </div>
                        </div>
                        <span class="text-[11px] font-black text-amber-900 bg-amber-100 px-2 py-0.5 rounded-full">
                            🟠 Amz
                        </span>
                    </div>

                    <!-- Editable Platform Parameters -->
                    <div class="p-3 bg-amber-50/60 rounded-2xl border border-amber-200/70 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600 font-semibold text-[11px]">अमेज़न कमीशन (%):</span>
                            <input type="number" id="amz_fee_pct" value="12" step="any" oninput="recalcAllPlatforms()" class="w-14 text-right py-1 px-1.5 rounded-lg border border-amber-300 bg-white font-black text-xs text-slate-800 focus:outline-hidden">
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600 font-semibold text-[11px]">क्लोजिंग फ़ीस (₹):</span>
                            <input type="number" id="amz_closing_fee" value="20" step="any" oninput="recalcAllPlatforms()" class="w-14 text-right py-1 px-1.5 rounded-lg border border-amber-300 bg-white font-black text-xs text-slate-800 focus:outline-hidden">
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600 font-semibold text-[11px]">Easy Ship कूरियर (₹):</span>
                            <input type="number" id="amz_ship_fee" value="45" step="any" oninput="recalcAllPlatforms()" class="w-14 text-right py-1 px-1.5 rounded-lg border border-amber-300 bg-white font-black text-xs text-slate-800 focus:outline-hidden">
                        </div>
                    </div>

                    <!-- Main Highlight Price Box -->
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 text-white shadow-sm text-center space-y-1">
                        <span class="text-[10px] uppercase tracking-wider font-black text-amber-100 block">Amazon सेलिंग रेट (Your Price)</span>
                        <div class="text-3xl font-black tracking-tight" id="amz_res_sp">₹509</div>
                        <div class="text-[11px] text-amber-100 font-semibold">सुझाया गया MRP: <span class="font-black" id="amz_res_mrp">₹799</span> (<span id="amz_res_off">36%</span> OFF)</div>
                    </div>

                    <!-- Financial Deductions Summary -->
                    <div class="space-y-1.5 text-xs pt-1">
                        <div class="flex items-center justify-between text-slate-600">
                            <span>कुल अमेज़न कटौती (कमीशन+GST):</span>
                            <span class="font-black text-red-600" id="amz_res_deduct">-₹92.08</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span>बैंक में जमा राशि (Net Payout):</span>
                            <span class="font-black text-slate-900" id="amz_res_payout">₹416.92</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-500 text-[11px]">
                            <span>RTO सुरक्षा फंड अलग सुरक्षित:</span>
                            <span class="font-bold text-amber-800" id="amz_res_rto">₹20.00</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-500 text-[11px]">
                            <span>पैकिंग लागत सुरक्षित:</span>
                            <span class="font-bold text-slate-700" id="amz_res_pack">₹15.00</span>
                        </div>
                    </div>
                </div>

                <!-- Bottom Pure Profit Badge -->
                <div class="p-3 bg-emerald-50 rounded-2xl border border-emerald-200 text-center space-y-0.5 mt-2">
                    <span class="text-[10px] text-emerald-700 font-black uppercase tracking-wide block">शुद्ध रोकड़ा मुनाफ़ा (In-Hand Cash)</span>
                    <div class="text-xl font-black text-emerald-700" id="amz_res_profit">₹106.92</div>
                    <span class="text-[10px] text-emerald-800 font-bold block">(<span id="amz_res_margin_pct">21.0%</span> शुद्ध मार्जिन)</span>
                </div>

                <!-- Combo Rates Pill -->
                <div class="text-[11px] bg-slate-50 p-2 rounded-xl border border-slate-200 text-slate-700 space-y-1">
                    <span class="font-black text-[10px] text-slate-500 uppercase block">कॉम्बो रेट्स:</span>
                    <div class="flex justify-between font-bold">
                        <span>2P: <b class="text-slate-900" id="amz_combo_2">₹869</b></span>
                        <span>4P: <b class="text-slate-900" id="amz_combo_4">₹1,599</b></span>
                        <span>6P: <b class="text-slate-900" id="amz_combo_6">₹2,299</b></span>
                    </div>
                </div>
            </div>

            <!-- 2. 🌸 MEESHO MARKETPLACE CARD -->
            <div class="bg-white rounded-3xl p-5 border-2 border-pink-300 shadow-md relative overflow-hidden flex flex-col justify-between space-y-4">
                <div class="absolute -right-8 -top-8 w-24 h-24 bg-pink-100 rounded-full opacity-40 pointer-events-none"></div>

                <div class="space-y-3">
                    <!-- Platform Header -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="h-9 w-9 rounded-2xl bg-gradient-to-br from-pink-500 to-rose-600 text-white flex items-center justify-center font-black text-sm shadow-xs">
                                🌸
                            </span>
                            <div>
                                <h3 class="font-black text-sm text-slate-900 leading-tight">Meesho Supplier</h3>
                                <span class="text-[10px] text-pink-700 font-bold bg-pink-50 px-1.5 py-0.2 rounded border border-pink-200">0% Commission Model</span>
                            </div>
                        </div>
                        <span class="text-[11px] font-black text-pink-900 bg-pink-100 px-2 py-0.5 rounded-full">
                            🌸 Msh
                        </span>
                    </div>

                    <!-- Editable Platform Parameters -->
                    <div class="p-3 bg-pink-50/60 rounded-2xl border border-pink-200/70 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600 font-semibold text-[11px]">मीशो कमीशन (%):</span>
                            <span class="font-black text-emerald-600 text-xs bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">0% (मुफ्त)</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600 font-semibold text-[11px]">कूरियर व शिपिंग (₹):</span>
                            <input type="number" id="msh_ship_fee" value="55" step="any" oninput="recalcAllPlatforms()" class="w-14 text-right py-1 px-1.5 rounded-lg border border-pink-300 bg-white font-black text-xs text-slate-800 focus:outline-hidden">
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600 font-semibold text-[11px]">पेनाल्टी रिस्क बफर (₹):</span>
                            <input type="number" id="msh_penalty_fee" value="10" step="any" oninput="recalcAllPlatforms()" class="w-14 text-right py-1 px-1.5 rounded-lg border border-pink-300 bg-white font-black text-xs text-slate-800 focus:outline-hidden">
                        </div>
                    </div>

                    <!-- Main Highlight Price Box -->
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-pink-600 to-rose-600 text-white shadow-sm text-center space-y-1">
                        <span class="text-[10px] uppercase tracking-wider font-black text-pink-100 block">Meesho सेलिंग रेट (हाई वॉल्यूम)</span>
                        <div class="text-3xl font-black tracking-tight" id="msh_res_sp">₹399</div>
                        <div class="text-[11px] text-pink-100 font-semibold">सुझाया गया MRP: <span class="font-black" id="msh_res_mrp">₹599</span> (<span id="msh_res_off">33%</span> OFF)</div>
                    </div>

                    <!-- Financial Deductions Summary -->
                    <div class="space-y-1.5 text-xs pt-1">
                        <div class="flex items-center justify-between text-slate-600">
                            <span>कुल मीशो कटौती (शिपिंग+टैक्स):</span>
                            <span class="font-black text-red-600" id="msh_res_deduct">-₹64.90</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span>बैंक में जमा राशि (Net Payout):</span>
                            <span class="font-black text-slate-900" id="msh_res_payout">₹334.10</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-500 text-[11px]">
                            <span>RTO सुरक्षा फंड अलग सुरक्षित:</span>
                            <span class="font-bold text-pink-800" id="msh_res_rto">₹20.00</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-500 text-[11px]">
                            <span>पैकिंग लागत सुरक्षित:</span>
                            <span class="font-bold text-slate-700" id="msh_res_pack">₹15.00</span>
                        </div>
                    </div>
                </div>

                <!-- Bottom Pure Profit Badge -->
                <div class="p-3 bg-emerald-50 rounded-2xl border border-emerald-200 text-center space-y-0.5 mt-2">
                    <span class="text-[10px] text-emerald-700 font-black uppercase tracking-wide block">शुद्ध रोकड़ा मुनाफ़ा (In-Hand Cash)</span>
                    <div class="text-xl font-black text-emerald-700" id="msh_res_profit">₹69.10</div>
                    <span class="text-[10px] text-emerald-800 font-bold block">(<span id="msh_res_margin_pct">17.3%</span> शुद्ध मार्जिन)</span>
                </div>

                <!-- Combo Rates Pill -->
                <div class="text-[11px] bg-slate-50 p-2 rounded-xl border border-slate-200 text-slate-700 space-y-1">
                    <span class="font-black text-[10px] text-slate-500 uppercase block">कॉम्बो रेट्स:</span>
                    <div class="flex justify-between font-bold">
                        <span>2P: <b class="text-slate-900" id="msh_combo_2">₹699</b></span>
                        <span>4P: <b class="text-slate-900" id="msh_combo_4">₹1,299</b></span>
                        <span>6P: <b class="text-slate-900" id="msh_combo_6">₹1,899</b></span>
                    </div>
                </div>
            </div>

            <!-- 3. 🔷 FLIPKART CARD -->
            <div class="bg-white rounded-3xl p-5 border-2 border-blue-300 shadow-md relative overflow-hidden flex flex-col justify-between space-y-4">
                <div class="absolute -right-8 -top-8 w-24 h-24 bg-blue-100 rounded-full opacity-40 pointer-events-none"></div>

                <div class="space-y-3">
                    <!-- Platform Header -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="h-9 w-9 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-black text-sm shadow-xs">
                                🔷
                            </span>
                            <div>
                                <h3 class="font-black text-sm text-slate-900 leading-tight">Flipkart Commerce</h3>
                                <span class="text-[10px] text-blue-700 font-bold bg-blue-50 px-1.5 py-0.2 rounded border border-blue-200">Express + Fixed Fee</span>
                            </div>
                        </div>
                        <span class="text-[11px] font-black text-blue-900 bg-blue-100 px-2 py-0.5 rounded-full">
                            🔷 Flp
                        </span>
                    </div>

                    <!-- Editable Platform Parameters -->
                    <div class="p-3 bg-blue-50/60 rounded-2xl border border-blue-200/70 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600 font-semibold text-[11px]">फ्लिपकार्ट कमीशन (%):</span>
                            <input type="number" id="flp_fee_pct" value="13" step="any" oninput="recalcAllPlatforms()" class="w-14 text-right py-1 px-1.5 rounded-lg border border-blue-300 bg-white font-black text-xs text-slate-800 focus:outline-hidden">
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600 font-semibold text-[11px]">फिक्स्ड फ़ीस (₹):</span>
                            <input type="number" id="flp_fixed_fee" value="15" step="any" oninput="recalcAllPlatforms()" class="w-14 text-right py-1 px-1.5 rounded-lg border border-blue-300 bg-white font-black text-xs text-slate-800 focus:outline-hidden">
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600 font-semibold text-[11px]">कूरियर व पिकपैक (₹):</span>
                            <input type="number" id="flp_ship_fee" value="50" step="any" oninput="recalcAllPlatforms()" class="w-14 text-right py-1 px-1.5 rounded-lg border border-blue-300 bg-white font-black text-xs text-slate-800 focus:outline-hidden">
                        </div>
                    </div>

                    <!-- Main Highlight Price Box -->
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-sm text-center space-y-1">
                        <span class="text-[10px] uppercase tracking-wider font-black text-blue-100 block">Flipkart सेलिंग रेट</span>
                        <div class="text-3xl font-black tracking-tight" id="flp_res_sp">₹519</div>
                        <div class="text-[11px] text-blue-100 font-semibold">सुझाया गया MRP: <span class="font-black" id="flp_res_mrp">₹799</span> (<span id="flp_res_off">35%</span> OFF)</div>
                    </div>

                    <!-- Financial Deductions Summary -->
                    <div class="space-y-1.5 text-xs pt-1">
                        <div class="flex items-center justify-between text-slate-600">
                            <span>कुल फ्लिपकार्ट कटौती (फीस+GST):</span>
                            <span class="font-black text-red-600" id="flp_res_deduct">-₹97.31</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span>बैंक में जमा राशि (Net Payout):</span>
                            <span class="font-black text-slate-900" id="flp_res_payout">₹421.69</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-500 text-[11px]">
                            <span>RTO सुरक्षा फंड अलग सुरक्षित:</span>
                            <span class="font-bold text-blue-800" id="flp_res_rto">₹20.00</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-500 text-[11px]">
                            <span>पैकिंग लागत सुरक्षित:</span>
                            <span class="font-bold text-slate-700" id="flp_res_pack">₹15.00</span>
                        </div>
                    </div>
                </div>

                <!-- Bottom Pure Profit Badge -->
                <div class="p-3 bg-emerald-50 rounded-2xl border border-emerald-200 text-center space-y-0.5 mt-2">
                    <span class="text-[10px] text-emerald-700 font-black uppercase tracking-wide block">शुद्ध रोकड़ा मुनाफ़ा (In-Hand Cash)</span>
                    <div class="text-xl font-black text-emerald-700" id="flp_res_profit">₹101.69</div>
                    <span class="text-[10px] text-emerald-800 font-bold block">(<span id="flp_res_margin_pct">19.6%</span> शुद्ध मार्जिन)</span>
                </div>

                <!-- Combo Rates Pill -->
                <div class="text-[11px] bg-slate-50 p-2 rounded-xl border border-slate-200 text-slate-700 space-y-1">
                    <span class="font-black text-[10px] text-slate-500 uppercase block">कॉम्बो रेट्स:</span>
                    <div class="flex justify-between font-bold">
                        <span>2P: <b class="text-slate-900" id="flp_combo_2">₹889</b></span>
                        <span>4P: <b class="text-slate-900" id="flp_combo_4">₹1,649</b></span>
                        <span>6P: <b class="text-slate-900" id="flp_combo_6">₹2,349</b></span>
                    </div>
                </div>
            </div>

            <!-- 4. 🟢 VYAPARINDIA DIRECT / WHATSAPP STORE -->
            <div class="bg-white rounded-3xl p-5 border-2 border-emerald-400 shadow-md relative overflow-hidden flex flex-col justify-between space-y-4">
                <div class="absolute -right-8 -top-8 w-24 h-24 bg-emerald-100 rounded-full opacity-40 pointer-events-none"></div>

                <div class="space-y-3">
                    <!-- Platform Header -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="h-9 w-9 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-black text-sm shadow-xs">
                                🟢
                            </span>
                            <div>
                                <h3 class="font-black text-sm text-slate-900 leading-tight">VyaparIndia Direct</h3>
                                <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">आपकी अपनी सीधी दुकान (No Middleman)</span>
                            </div>
                        </div>
                        <span class="text-[11px] font-black text-emerald-900 bg-emerald-100 px-2 py-0.5 rounded-full">
                            🟢 Direct
                        </span>
                    </div>

                    <!-- Editable Platform Parameters -->
                    <div class="p-3 bg-emerald-50/60 rounded-2xl border border-emerald-200/70 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600 font-semibold text-[11px]">प्लेटफॉर्म कमीशन (%):</span>
                            <span class="font-black text-emerald-700 text-xs bg-emerald-100/80 px-2 py-0.5 rounded border border-emerald-300">0% (कोई बिचौलिया नहीं)</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600 font-semibold text-[11px]">लोकल/सेल्फ शिपिंग (₹):</span>
                            <input type="number" id="dir_ship_fee" value="30" step="any" oninput="recalcAllPlatforms()" class="w-14 text-right py-1 px-1.5 rounded-lg border border-emerald-300 bg-white font-black text-xs text-slate-800 focus:outline-hidden">
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600 font-semibold text-[11px]">पेमेंट गेटवे फ़ीस (%):</span>
                            <input type="number" id="dir_pg_fee" value="2" step="any" oninput="recalcAllPlatforms()" class="w-14 text-right py-1 px-1.5 rounded-lg border border-emerald-300 bg-white font-black text-xs text-slate-800 focus:outline-hidden">
                        </div>
                    </div>

                    <!-- Main Highlight Price Box -->
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white shadow-sm text-center space-y-1">
                        <span class="text-[10px] uppercase tracking-wider font-black text-emerald-100 block">डायरेक्ट WhatsApp सेलिंग रेट</span>
                        <div class="text-3xl font-black tracking-tight" id="dir_res_sp">₹349</div>
                        <div class="text-[11px] text-emerald-100 font-semibold">सुझाया गया MRP: <span class="font-black" id="dir_res_mrp">₹499</span> (<span id="dir_res_off">30%</span> OFF)</div>
                    </div>

                    <!-- Financial Deductions Summary -->
                    <div class="space-y-1.5 text-xs pt-1">
                        <div class="flex items-center justify-between text-slate-600">
                            <span>कुल कटौती (कूरियर+PG):</span>
                            <span class="font-black text-red-600" id="dir_res_deduct">-₹36.98</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span>बैंक में जमा राशि (Net Payout):</span>
                            <span class="font-black text-slate-900" id="dir_res_payout">₹312.02</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-500 text-[11px]">
                            <span>RTO सुरक्षा फंड सुरक्षित:</span>
                            <span class="font-bold text-emerald-800" id="dir_res_rto">₹15.00</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-500 text-[11px]">
                            <span>पैकिंग लागत सुरक्षित:</span>
                            <span class="font-bold text-slate-700" id="dir_res_pack">₹15.00</span>
                        </div>
                    </div>
                </div>

                <!-- Bottom Pure Profit Badge -->
                <div class="p-3 bg-emerald-100/70 rounded-2xl border border-emerald-300 text-center space-y-0.5 mt-2">
                    <span class="text-[10px] text-emerald-800 font-black uppercase tracking-wide block">शुद्ध रोकड़ा मुनाफ़ा (In-Hand Cash)</span>
                    <div class="text-xl font-black text-emerald-800" id="dir_res_profit">₹72.02</div>
                    <span class="text-[10px] text-emerald-900 font-bold block">(<span id="dir_res_margin_pct">20.6%</span> शुद्ध मार्जिन)</span>
                </div>

                <!-- Combo Rates Pill -->
                <div class="text-[11px] bg-slate-50 p-2 rounded-xl border border-slate-200 text-slate-700 space-y-1">
                    <span class="font-black text-[10px] text-slate-500 uppercase block">कॉम्बो रेट्स:</span>
                    <div class="flex justify-between font-bold">
                        <span>2P: <b class="text-slate-900" id="dir_combo_2">₹599</b></span>
                        <span>4P: <b class="text-slate-900" id="dir_combo_4">₹1,099</b></span>
                        <span>6P: <b class="text-slate-900" id="dir_combo_6">₹1,599</b></span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Comprehensive Side-by-Side Comparison Audit Table -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between flex-wrap gap-2 border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-table-columns text-indigo-600"></i>
                    <h3 class="font-black text-sm text-slate-900">विस्तृत प्लेटफ़ॉर्म वित्तीय ऑडिट तुलना तालिका (Full Audit Breakdown)</h3>
                </div>
                <span class="text-xs text-slate-500 font-semibold">सभी आंकड़े 1 पीस की बिक्री पर आधारित हैं</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-[11px] font-black text-slate-500 uppercase bg-slate-50/80">
                            <th class="py-3 px-3">खर्च / आय का विवरण</th>
                            <th class="py-3 px-3 text-amber-900">🟠 Amazon India</th>
                            <th class="py-3 px-3 text-pink-900">🌸 Meesho</th>
                            <th class="py-3 px-3 text-blue-900">🔷 Flipkart</th>
                            <th class="py-3 px-3 text-emerald-900">🟢 Direct Store</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <tr>
                            <td class="py-2.5 px-3 font-bold text-slate-800">1. ग्राहक सेलिंग प्राइस (Your Price)</td>
                            <td class="py-2.5 px-3 font-black text-amber-700" id="tab_sp_amz">₹509.00</td>
                            <td class="py-2.5 px-3 font-black text-pink-700" id="tab_sp_msh">₹399.00</td>
                            <td class="py-2.5 px-3 font-black text-blue-700" id="tab_sp_flp">₹519.00</td>
                            <td class="py-2.5 px-3 font-black text-emerald-700" id="tab_sp_dir">₹349.00</td>
                        </tr>
                        <tr class="bg-slate-50/50">
                            <td class="py-2.5 px-3 font-bold text-slate-700">2. सुझाई गई डिब्बा MRP</td>
                            <td class="py-2.5 px-3 font-bold text-slate-700" id="tab_mrp_amz">₹799.00</td>
                            <td class="py-2.5 px-3 font-bold text-slate-700" id="tab_mrp_msh">₹599.00</td>
                            <td class="py-2.5 px-3 font-bold text-slate-700" id="tab_mrp_flp">₹799.00</td>
                            <td class="py-2.5 px-3 font-bold text-slate-700" id="tab_mrp_dir">₹499.00</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-slate-600">3. प्लेटफॉर्म कमीशन फ़ीस</td>
                            <td class="py-2.5 px-3 text-red-600 font-semibold" id="tab_comm_amz">-₹61.08</td>
                            <td class="py-2.5 px-3 text-emerald-600 font-black">₹0.00 (0%)</td>
                            <td class="py-2.5 px-3 text-red-600 font-semibold" id="tab_comm_flp">-₹67.47</td>
                            <td class="py-2.5 px-3 text-emerald-600 font-black">₹0.00 (0%)</td>
                        </tr>
                        <tr class="bg-slate-50/50">
                            <td class="py-2.5 px-3 text-slate-600">4. क्लोजिंग / फिक्स्ड / PG फ़ीस</td>
                            <td class="py-2.5 px-3 text-red-600 font-semibold" id="tab_close_amz">-₹20.00</td>
                            <td class="py-2.5 px-3 text-slate-400">₹0.00</td>
                            <td class="py-2.5 px-3 text-red-600 font-semibold" id="tab_close_flp">-₹15.00</td>
                            <td class="py-2.5 px-3 text-red-600 font-semibold" id="tab_close_dir">-₹6.98</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-slate-600">5. कूरियर / Easy Ship डिलीवरी</td>
                            <td class="py-2.5 px-3 text-red-600 font-semibold" id="tab_ship_amz">-₹45.00</td>
                            <td class="py-2.5 px-3 text-red-600 font-semibold" id="tab_ship_msh">-₹55.00</td>
                            <td class="py-2.5 px-3 text-red-600 font-semibold" id="tab_ship_flp">-₹50.00</td>
                            <td class="py-2.5 px-3 text-red-600 font-semibold" id="tab_ship_dir">-₹30.00</td>
                        </tr>
                        <tr class="bg-slate-50/50">
                            <td class="py-2.5 px-3 text-slate-600">6. 18% GST ऑन प्लेटफॉर्म फ़ीस</td>
                            <td class="py-2.5 px-3 text-red-500 text-[11px]" id="tab_gst_amz">-₹14.59</td>
                            <td class="py-2.5 px-3 text-red-500 text-[11px]" id="tab_gst_msh">-₹9.90</td>
                            <td class="py-2.5 px-3 text-red-500 text-[11px]" id="tab_gst_flp">-₹14.84</td>
                            <td class="py-2.5 px-3 text-slate-400 text-[11px]">₹0.00</td>
                        </tr>
                        <tr class="bg-amber-50/60 font-black text-slate-900 border-t border-b border-amber-200">
                            <td class="py-3 px-3">7. बैंक खाते में कुल क्रेडिट (Net Payout)</td>
                            <td class="py-3 px-3 text-amber-900" id="tab_payout_amz">₹416.92</td>
                            <td class="py-3 px-3 text-pink-900" id="tab_payout_msh">₹334.10</td>
                            <td class="py-3 px-3 text-blue-900" id="tab_payout_flp">₹421.69</td>
                            <td class="py-3 px-3 text-emerald-900" id="tab_payout_dir">₹312.02</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-slate-600">8. आपकी खरीद लागत (Cost) वापस निकली</td>
                            <td class="py-2.5 px-3 text-slate-700 font-bold" id="tab_cost_amz">-₹250.00</td>
                            <td class="py-2.5 px-3 text-slate-700 font-bold" id="tab_cost_msh">-₹250.00</td>
                            <td class="py-2.5 px-3 text-slate-700 font-bold" id="tab_cost_flp">-₹250.00</td>
                            <td class="py-2.5 px-3 text-slate-700 font-bold" id="tab_cost_dir">-₹250.00</td>
                        </tr>
                        <tr class="bg-slate-50/50">
                            <td class="py-2.5 px-3 text-slate-600">9. पैकेजिंग मटेरियल खर्च निकला</td>
                            <td class="py-2.5 px-3 text-slate-700" id="tab_pack_amz">-₹15.00</td>
                            <td class="py-2.5 px-3 text-slate-700" id="tab_pack_msh">-₹15.00</td>
                            <td class="py-2.5 px-3 text-slate-700" id="tab_pack_flp">-₹15.00</td>
                            <td class="py-2.5 px-3 text-slate-700" id="tab_pack_dir">-₹15.00</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-amber-800 font-semibold">10. RTO रिटर्न फंड अलग सुरक्षित रखा</td>
                            <td class="py-2.5 px-3 text-amber-800 font-bold" id="tab_rto_amz">₹20.00</td>
                            <td class="py-2.5 px-3 text-pink-800 font-bold" id="tab_rto_msh">₹20.00</td>
                            <td class="py-2.5 px-3 text-blue-800 font-bold" id="tab_rto_flp">₹20.00</td>
                            <td class="py-2.5 px-3 text-emerald-800 font-bold" id="tab_rto_dir">₹15.00</td>
                        </tr>
                        <tr class="bg-emerald-100 font-black text-slate-900 text-sm">
                            <td class="py-3 px-3 text-emerald-950">💰 हाथ में शुद्ध रोकड़ा मुनाफ़ा (Net Cash Profit)</td>
                            <td class="py-3 px-3 text-emerald-800" id="tab_profit_amz">₹106.92</td>
                            <td class="py-3 px-3 text-pink-900" id="tab_profit_msh">₹69.10</td>
                            <td class="py-3 px-3 text-blue-900" id="tab_profit_flp">₹101.69</td>
                            <td class="py-3 px-3 text-emerald-900" id="tab_profit_dir">₹72.02</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <script>
    // Live Calculation Engine
    function onMrpOrDiscountChange() {
        const mrp = parseFloat(document.getElementById('inp_mrp').value) || 0;
        const discount = parseFloat(document.getElementById('inp_discount').value) || 0;
        if (mrp > 0 && discount > 0) {
            const netCost = mrp - (mrp * (discount / 100));
            document.getElementById('inp_cost').value = netCost.toFixed(2);
        }
        recalcAllPlatforms();
    }

    function setShippingBase(amount) {
        document.getElementById('amz_ship_fee').value = amount;
        document.getElementById('flp_ship_fee').value = amount + 5;
        document.getElementById('msh_ship_fee').value = amount + 10;
        
        ['btn_ship_local', 'btn_ship_reg', 'btn_ship_nat'].forEach(id => {
            const el = document.getElementById(id);
            el.className = 'px-2.5 py-1 rounded-md text-xs font-bold text-slate-600';
        });
        if (amount === 45) document.getElementById('btn_ship_local').className = 'px-2.5 py-1 rounded-md text-xs font-bold bg-white text-indigo-700 shadow-2xs';
        if (amount === 65) document.getElementById('btn_ship_reg').className = 'px-2.5 py-1 rounded-md text-xs font-bold bg-white text-indigo-700 shadow-2xs';
        if (amount === 75) document.getElementById('btn_ship_nat').className = 'px-2.5 py-1 rounded-md text-xs font-bold bg-white text-indigo-700 shadow-2xs';

        recalcAllPlatforms();
    }

    function applyPreset(type) {
        if (type === 'plumbing') {
            document.getElementById('amz_fee_pct').value = 12;
            document.getElementById('flp_fee_pct').value = 13;
            document.getElementById('inp_gst').value = 18;
            setShippingBase(45);
        } else if (type === 'hardware') {
            document.getElementById('amz_fee_pct').value = 10;
            document.getElementById('flp_fee_pct').value = 11;
            document.getElementById('inp_gst').value = 18;
            setShippingBase(50);
        } else if (type === 'apparel') {
            document.getElementById('amz_fee_pct').value = 14;
            document.getElementById('flp_fee_pct').value = 15;
            document.getElementById('inp_gst').value = 5;
            setShippingBase(55);
        }
        recalcAllPlatforms();
    }

    function loadProductIntoCalculator(productId) {
        if (!productId) return;
        const sel = document.getElementById('product_selector');
        const opt = sel.options[sel.selectedIndex];
        const cost = parseFloat(opt.getAttribute('data-cost')) || 250;
        const mrp = parseFloat(opt.getAttribute('data-mrp')) || 340;
        const disc = parseFloat(opt.getAttribute('data-discount')) || 0;

        document.getElementById('inp_cost').value = cost;
        document.getElementById('inp_mrp').value = mrp;
        document.getElementById('inp_discount').value = disc;

        recalcAllPlatforms();
    }

    function recalcAllPlatforms() {
        const cost = parseFloat(document.getElementById('inp_cost').value) || 0;
        const packaging = parseFloat(document.getElementById('inp_packaging').value) || 0;
        const rtoBuffer = parseFloat(document.getElementById('inp_rto').value) || 0;
        const marginPct = (parseFloat(document.getElementById('inp_margin').value) || 25) / 100;
        const gstPct = (parseFloat(document.getElementById('inp_gst').value) || 18) / 100;

        const desiredProfit = Math.round(cost * marginPct);
        const cTax = gstPct > 0 ? (cost / (1 + gstPct)) : cost;

        // 1. AMAZON RECALC
        const amzFeePct = (parseFloat(document.getElementById('amz_fee_pct').value) || 12) / 100;
        const amzClosing = parseFloat(document.getElementById('amz_closing_fee').value) || 20;
        const amzShip = parseFloat(document.getElementById('amz_ship_fee').value) || 45;

        const amzDenom = Math.max(0.05, (1 / (1 + gstPct)) - amzFeePct);
        const amzFixed = rtoBuffer + packaging + amzShip + amzClosing;
        let amzSp = Math.ceil((cTax + amzFixed + desiredProfit) / amzDenom);
        if (amzSp % 10 === 0) amzSp = amzSp - 1; // 499, 509 style

        const amzComm = Math.round(amzSp * amzFeePct * 100) / 100;
        const amzGst = Math.round((amzComm + amzClosing + amzShip) * 0.18 * 100) / 100;
        const amzTotalDeduct = Math.round((amzComm + amzClosing + amzShip + amzGst) * 100) / 100;
        const amzPayout = Math.round((amzSp - amzTotalDeduct) * 100) / 100;
        const amzProfit = Math.round((amzPayout - cost - packaging) * 100) / 100;
        const amzMargin = ((amzProfit / amzSp) * 100).toFixed(1);
        const amzMrp = Math.ceil(amzSp * 1.55 / 10) * 10 - 1; // ~35% off
        const amzOff = Math.round((1 - (amzSp / amzMrp)) * 100);

        document.getElementById('amz_res_sp').innerText = '₹' + amzSp;
        document.getElementById('amz_res_mrp').innerText = '₹' + amzMrp;
        document.getElementById('amz_res_off').innerText = amzOff + '%';
        document.getElementById('amz_res_deduct').innerText = '-₹' + amzTotalDeduct.toFixed(2);
        document.getElementById('amz_res_payout').innerText = '₹' + amzPayout.toFixed(2);
        document.getElementById('amz_res_profit').innerText = '₹' + amzProfit.toFixed(2);
        document.getElementById('amz_res_margin_pct').innerText = amzMargin + '%';
        document.getElementById('amz_res_rto').innerText = '₹' + rtoBuffer.toFixed(2);
        document.getElementById('amz_res_pack').innerText = '₹' + packaging.toFixed(2);

        document.getElementById('amz_combo_2').innerText = '₹' + Math.ceil(amzSp * 1.72 / 10) * 10 - 1;
        document.getElementById('amz_combo_4').innerText = '₹' + Math.ceil(amzSp * 3.15 / 10) * 10 - 1;
        document.getElementById('amz_combo_6').innerText = '₹' + Math.ceil(amzSp * 4.52 / 10) * 10 - 1;

        // 2. MEESHO RECALC (0% Commission)
        const mshShip = parseFloat(document.getElementById('msh_ship_fee').value) || 55;
        const mshPenalty = parseFloat(document.getElementById('msh_penalty_fee').value) || 10;
        const mshFixed = rtoBuffer + packaging + mshShip + mshPenalty;
        const mshDenom = Math.max(0.05, 1 / (1 + gstPct));
        let mshSp = Math.ceil((cTax + mshFixed + desiredProfit * 0.9) / mshDenom);
        if (mshSp % 10 === 0) mshSp = mshSp - 1;

        const mshGst = Math.round(mshShip * 0.18 * 100) / 100;
        const mshTotalDeduct = Math.round((mshShip + mshGst) * 100) / 100;
        const mshPayout = Math.round((mshSp - mshTotalDeduct) * 100) / 100;
        const mshProfit = Math.round((mshPayout - cost - packaging) * 100) / 100;
        const mshMargin = ((mshProfit / mshSp) * 100).toFixed(1);
        const mshMrp = Math.ceil(mshSp * 1.5 / 10) * 10 - 1;
        const mshOff = Math.round((1 - (mshSp / mshMrp)) * 100);

        document.getElementById('msh_res_sp').innerText = '₹' + mshSp;
        document.getElementById('msh_res_mrp').innerText = '₹' + mshMrp;
        document.getElementById('msh_res_off').innerText = mshOff + '%';
        document.getElementById('msh_res_deduct').innerText = '-₹' + mshTotalDeduct.toFixed(2);
        document.getElementById('msh_res_payout').innerText = '₹' + mshPayout.toFixed(2);
        document.getElementById('msh_res_profit').innerText = '₹' + mshProfit.toFixed(2);
        document.getElementById('msh_res_margin_pct').innerText = mshMargin + '%';
        document.getElementById('msh_res_rto').innerText = '₹' + rtoBuffer.toFixed(2);
        document.getElementById('msh_res_pack').innerText = '₹' + packaging.toFixed(2);

        document.getElementById('msh_combo_2').innerText = '₹' + Math.ceil(mshSp * 1.75 / 10) * 10 - 1;
        document.getElementById('msh_combo_4').innerText = '₹' + Math.ceil(mshSp * 3.25 / 10) * 10 - 1;
        document.getElementById('msh_combo_6').innerText = '₹' + Math.ceil(mshSp * 4.75 / 10) * 10 - 1;

        // 3. FLIPKART RECALC
        const flpFeePct = (parseFloat(document.getElementById('flp_fee_pct').value) || 13) / 100;
        const flpFixed = parseFloat(document.getElementById('flp_fixed_fee').value) || 15;
        const flpShip = parseFloat(document.getElementById('flp_ship_fee').value) || 50;

        const flpDenom = Math.max(0.05, (1 / (1 + gstPct)) - flpFeePct);
        const flpAllFixed = rtoBuffer + packaging + flpShip + flpFixed;
        let flpSp = Math.ceil((cTax + flpAllFixed + desiredProfit) / flpDenom);
        if (flpSp % 10 === 0) flpSp = flpSp - 1;

        const flpComm = Math.round(flpSp * flpFeePct * 100) / 100;
        const flpGst = Math.round((flpComm + flpFixed + flpShip) * 0.18 * 100) / 100;
        const flpTotalDeduct = Math.round((flpComm + flpFixed + flpShip + flpGst) * 100) / 100;
        const flpPayout = Math.round((flpSp - flpTotalDeduct) * 100) / 100;
        const flpProfit = Math.round((flpPayout - cost - packaging) * 100) / 100;
        const flpMargin = ((flpProfit / flpSp) * 100).toFixed(1);
        const flpMrp = Math.ceil(flpSp * 1.55 / 10) * 10 - 1;
        const flpOff = Math.round((1 - (flpSp / flpMrp)) * 100);

        document.getElementById('flp_res_sp').innerText = '₹' + flpSp;
        document.getElementById('flp_res_mrp').innerText = '₹' + flpMrp;
        document.getElementById('flp_res_off').innerText = flpOff + '%';
        document.getElementById('flp_res_deduct').innerText = '-₹' + flpTotalDeduct.toFixed(2);
        document.getElementById('flp_res_payout').innerText = '₹' + flpPayout.toFixed(2);
        document.getElementById('flp_res_profit').innerText = '₹' + flpProfit.toFixed(2);
        document.getElementById('flp_res_margin_pct').innerText = flpMargin + '%';
        document.getElementById('flp_res_rto').innerText = '₹' + rtoBuffer.toFixed(2);
        document.getElementById('flp_res_pack').innerText = '₹' + packaging.toFixed(2);

        document.getElementById('flp_combo_2').innerText = '₹' + Math.ceil(flpSp * 1.72 / 10) * 10 - 1;
        document.getElementById('flp_combo_4').innerText = '₹' + Math.ceil(flpSp * 3.18 / 10) * 10 - 1;
        document.getElementById('flp_combo_6').innerText = '₹' + Math.ceil(flpSp * 4.55 / 10) * 10 - 1;

        // 4. DIRECT / WHATSAPP STORE RECALC
        const dirShip = parseFloat(document.getElementById('dir_ship_fee').value) || 30;
        const dirPgPct = (parseFloat(document.getElementById('dir_pg_fee').value) || 2) / 100;
        const dirDenom = Math.max(0.05, 1 - dirPgPct);
        let dirSp = Math.ceil((cost + packaging + (rtoBuffer * 0.75) + dirShip + (desiredProfit * 0.8)) / dirDenom);
        if (dirSp % 10 === 0) dirSp = dirSp - 1;

        const dirPg = Math.round(dirSp * dirPgPct * 100) / 100;
        const dirTotalDeduct = Math.round((dirShip + dirPg) * 100) / 100;
        const dirPayout = Math.round((dirSp - dirTotalDeduct) * 100) / 100;
        const dirProfit = Math.round((dirPayout - cost - packaging) * 100) / 100;
        const dirMargin = ((dirProfit / dirSp) * 100).toFixed(1);
        const dirMrp = Math.ceil(dirSp * 1.4 / 10) * 10 - 1;
        const dirOff = Math.round((1 - (dirSp / dirMrp)) * 100);

        document.getElementById('dir_res_sp').innerText = '₹' + dirSp;
        document.getElementById('dir_res_mrp').innerText = '₹' + dirMrp;
        document.getElementById('dir_res_off').innerText = dirOff + '%';
        document.getElementById('dir_res_deduct').innerText = '-₹' + dirTotalDeduct.toFixed(2);
        document.getElementById('dir_res_payout').innerText = '₹' + dirPayout.toFixed(2);
        document.getElementById('dir_res_profit').innerText = '₹' + dirProfit.toFixed(2);
        document.getElementById('dir_res_margin_pct').innerText = dirMargin + '%';
        document.getElementById('dir_res_rto').innerText = '₹' + (rtoBuffer * 0.75).toFixed(2);
        document.getElementById('dir_res_pack').innerText = '₹' + packaging.toFixed(2);

        document.getElementById('dir_combo_2').innerText = '₹' + Math.ceil(dirSp * 1.7 / 10) * 10 - 1;
        document.getElementById('dir_combo_4').innerText = '₹' + Math.ceil(dirSp * 3.1 / 10) * 10 - 1;
        document.getElementById('dir_combo_6').innerText = '₹' + Math.ceil(dirSp * 4.4 / 10) * 10 - 1;

        // TABLE AUDIT UPDATES
        document.getElementById('tab_sp_amz').innerText = '₹' + amzSp.toFixed(2);
        document.getElementById('tab_sp_msh').innerText = '₹' + mshSp.toFixed(2);
        document.getElementById('tab_sp_flp').innerText = '₹' + flpSp.toFixed(2);
        document.getElementById('tab_sp_dir').innerText = '₹' + dirSp.toFixed(2);

        document.getElementById('tab_mrp_amz').innerText = '₹' + amzMrp.toFixed(2);
        document.getElementById('tab_mrp_msh').innerText = '₹' + mshMrp.toFixed(2);
        document.getElementById('tab_mrp_flp').innerText = '₹' + flpMrp.toFixed(2);
        document.getElementById('tab_mrp_dir').innerText = '₹' + dirMrp.toFixed(2);

        document.getElementById('tab_comm_amz').innerText = '-₹' + amzComm.toFixed(2) + ' (' + (amzFeePct*100) + '%)';
        document.getElementById('tab_comm_flp').innerText = '-₹' + flpComm.toFixed(2) + ' (' + (flpFeePct*100) + '%)';

        document.getElementById('tab_close_amz').innerText = '-₹' + amzClosing.toFixed(2);
        document.getElementById('tab_close_flp').innerText = '-₹' + flpFixed.toFixed(2);
        document.getElementById('tab_close_dir').innerText = '-₹' + dirPg.toFixed(2);

        document.getElementById('tab_ship_amz').innerText = '-₹' + amzShip.toFixed(2);
        document.getElementById('tab_ship_msh').innerText = '-₹' + mshShip.toFixed(2);
        document.getElementById('tab_ship_flp').innerText = '-₹' + flpShip.toFixed(2);
        document.getElementById('tab_ship_dir').innerText = '-₹' + dirShip.toFixed(2);

        document.getElementById('tab_gst_amz').innerText = '-₹' + amzGst.toFixed(2);
        document.getElementById('tab_gst_msh').innerText = '-₹' + mshGst.toFixed(2);
        document.getElementById('tab_gst_flp').innerText = '-₹' + flpGst.toFixed(2);

        document.getElementById('tab_payout_amz').innerText = '₹' + amzPayout.toFixed(2);
        document.getElementById('tab_payout_msh').innerText = '₹' + mshPayout.toFixed(2);
        document.getElementById('tab_payout_flp').innerText = '₹' + flpPayout.toFixed(2);
        document.getElementById('tab_payout_dir').innerText = '₹' + dirPayout.toFixed(2);

        document.getElementById('tab_cost_amz').innerText = '-₹' + cost.toFixed(2);
        document.getElementById('tab_cost_msh').innerText = '-₹' + cost.toFixed(2);
        document.getElementById('tab_cost_flp').innerText = '-₹' + cost.toFixed(2);
        document.getElementById('tab_cost_dir').innerText = '-₹' + cost.toFixed(2);

        document.getElementById('tab_pack_amz').innerText = '-₹' + packaging.toFixed(2);
        document.getElementById('tab_pack_msh').innerText = '-₹' + packaging.toFixed(2);
        document.getElementById('tab_pack_flp').innerText = '-₹' + packaging.toFixed(2);
        document.getElementById('tab_pack_dir').innerText = '-₹' + packaging.toFixed(2);

        document.getElementById('tab_rto_amz').innerText = '₹' + rtoBuffer.toFixed(2);
        document.getElementById('tab_rto_msh').innerText = '₹' + rtoBuffer.toFixed(2);
        document.getElementById('tab_rto_flp').innerText = '₹' + rtoBuffer.toFixed(2);
        document.getElementById('tab_rto_dir').innerText = '₹' + (rtoBuffer * 0.75).toFixed(2);

        document.getElementById('tab_profit_amz').innerText = '₹' + amzProfit.toFixed(2);
        document.getElementById('tab_profit_msh').innerText = '₹' + mshProfit.toFixed(2);
        document.getElementById('tab_profit_flp').innerText = '₹' + flpProfit.toFixed(2);
        document.getElementById('tab_profit_dir').innerText = '₹' + dirProfit.toFixed(2);
    }

    function copyAllCalculatedRates() {
        const amzSp = document.getElementById('amz_res_sp').innerText;
        const mshSp = document.getElementById('msh_res_sp').innerText;
        const flpSp = document.getElementById('flp_res_sp').innerText;
        const dirSp = document.getElementById('dir_res_sp').innerText;
        const cost = document.getElementById('inp_cost').value;

        const text = `📊 VyaparIndia यूनिट इकोनॉमिक्स तुलना:\nखरीद लागत: ₹${cost}\n🟠 Amazon Rate: ${amzSp} (MRP: ${document.getElementById('amz_res_mrp').innerText}) -> शुद्ध मुनाफ़ा: ${document.getElementById('amz_res_profit').innerText}\n🌸 Meesho Rate: ${mshSp} (MRP: ${document.getElementById('msh_res_mrp').innerText}) -> शुद्ध मुनाफ़ा: ${document.getElementById('msh_res_profit').innerText}\n🔷 Flipkart Rate: ${flpSp} (MRP: ${document.getElementById('flp_res_mrp').innerText}) -> शुद्ध मुनाफ़ा: ${document.getElementById('flp_res_profit').innerText}\n🟢 Direct WhatsApp: ${dirSp} -> शुद्ध मुनाफ़ा: ${document.getElementById('dir_res_profit').innerText}`;

        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                icon: 'success',
                title: '📋 सभी रेट्स कॉपी हो गए!',
                text: 'आप इसे WhatsApp या नोटबुक में सीधे पेस्ट कर सकते हैं।',
                timer: 2000,
                showConfirmButton: false
            });
        });
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', () => {
        recalcAllPlatforms();
    });
    </script>
</body>
</html>
