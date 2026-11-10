<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Marketplace Channel Hub (Amazon, Meesho, Blinkit, Flipkart) - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com">
        // --- ↔️ HORIZONTAL TABLE SCREEN COLUMN SCROLLING FUNCTIONS ---
        function scrollTableHorizontally(distance) {
            const wrapper = document.getElementById('pendingTableScrollWrapper');
            if (wrapper) {
                wrapper.scrollBy({ left: distance, behavior: 'smooth' });
            }
        }

        function scrollTableToStart() {
            const wrapper = document.getElementById('pendingTableScrollWrapper');
            if (wrapper) {
                wrapper.scrollTo({ left: 0, behavior: 'smooth' });
            }
        }

        function scrollTableToMid() {
            const wrapper = document.getElementById('pendingTableScrollWrapper');
            if (wrapper) {
                wrapper.scrollTo({ left: 800, behavior: 'smooth' });
            }
        }

        function scrollTableToEnd() {
            const wrapper = document.getElementById('pendingTableScrollWrapper');
            if (wrapper) {
                wrapper.scrollTo({ left: wrapper.scrollWidth || 3000, behavior: 'smooth' });
            }
        }

        // --- 📁 MASTER EXPENSES COMPACT / COLLAPSE MODE (USER'S SAMETNE WALA ENGINE) ---
        let isTableCompactMode = false;

        function toggleCompactTableMode() {
            isTableCompactMode = !isTableCompactMode;

            const detailExpCols = document.querySelectorAll('.col-expense-detail');
            const detailCostCols = document.querySelectorAll('.col-cost-detail');
            const summaryExpCols = document.querySelectorAll('.col-expense-summary');
            const btnText = document.getElementById('btnCompactText');
            const btnIcon = document.getElementById('btnCompactIcon');
            const btn = document.getElementById('btnCompactTableMode');

            if (isTableCompactMode) {
                // HIDE all 8 individual expense columns and cost extras
                detailExpCols.forEach(el => el.classList.add('hidden'));
                detailCostCols.forEach(el => el.classList.add('hidden'));
                
                // SHOW the 1 single combined summary expense column
                summaryExpCols.forEach(el => el.classList.remove('hidden'));

                if (btnText) btnText.textContent = '📂 सारे 8 खर्चे अलग-अलग खोलें (Expand View)';
                if (btnIcon) {
                    btnIcon.classList.remove('fa-compress');
                    btnIcon.classList.add('fa-expand');
                }
                if (btn) {
                    btn.classList.remove('from-purple-600', 'to-indigo-600');
                    btn.classList.add('from-emerald-600', 'to-teal-600');
                }

                // Smoothly scroll table to start
                scrollTableToStart();
                showChannelToast('📁 सारे खर्चे 1 ही बॉक्स में समेट दिए गए! अब पूरी टेबल स्क्रीन पर साफ़ दिखेगी।');
            } else {
                // SHOW all 8 individual expense columns
                detailExpCols.forEach(el => el.classList.remove('hidden'));
                detailCostCols.forEach(el => el.classList.remove('hidden'));

                // HIDE the summary expense column
                summaryExpCols.forEach(el => el.classList.add('hidden'));

                if (btnText) btnText.textContent = '📁 सारे खर्चे 1 बॉक्स में समेटें (Compact View)';
                if (btnIcon) {
                    btnIcon.classList.remove('fa-expand');
                    btnIcon.classList.add('fa-compress');
                }
                if (btn) {
                    btn.classList.remove('from-emerald-600', 'to-teal-600');
                    btn.classList.add('from-purple-600', 'to-indigo-600');
                }

                showChannelToast('📂 सारे 8 खर्चे अलग-अलग बॉक्स में खुल गए!');
            }
        }
</script>
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
                        <a href="{{ route('seller.channels.pnl_budget') }}" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-indigo-700 hover:from-emerald-700 hover:to-indigo-800 text-white font-black text-xs shadow-xs transition flex items-center gap-1.5 hover:scale-102 active:scale-98" title="मासिक P&L, वास्तविक खर्च vs बजट विचलन और मुनाफ़ा रिपोर्ट">
                            <i class="fa-solid fa-chart-pie text-amber-300"></i>
                            <span>📊 मासिक P&L व बजट</span>
                        </a>
                        <a href="{{ route('seller.pricing_calculator') }}" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-xs shadow-xs transition flex items-center gap-1.5 hover:scale-102 active:scale-98" title="मार्केटप्लेस यूनिट इकोनॉमिक्स व प्रॉफिट तुलना कैलकुलेटर">
                            <i class="fa-solid fa-calculator text-amber-300"></i>
                            <span>🧮 प्रॉफिट कैलकुलेटर</span>
                        </a>
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
            <div class="p-4 sm:p-5 rounded-3xl bg-gradient-to-r from-emerald-950 via-slate-900 to-emerald-950 text-white border border-emerald-500/40 shadow-xl space-y-3">
                <div class="flex items-center justify-between flex-wrap gap-2 border-b border-emerald-500/20 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="h-8 w-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg border border-emerald-500/30">
                            <i class="fa-solid fa-circle-check"></i>
                        </span>
                        <div>
                            <span class="text-xs sm:text-sm font-black text-emerald-300 block">{{ session('success') }}</span>
                            <span class="text-[11px] text-slate-300">आपके प्रोडक्ट्स कनेक्टेड प्लेटफॉर्म पर लाइव सिंक हो चुके हैं। आप सीधे सेलर पैनल में लाइव स्थिति चेक कर सकते हैं:</span>
                        </div>
                    </div>
                    <button type="button" onclick="openMarketplaceSellerPortals()" class="px-4 py-2 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-black text-xs shadow-md transition flex items-center gap-2 active:scale-95 cursor-pointer">
                        <i class="fa-solid fa-arrow-up-right-from-square text-amber-300"></i>
                        <span>🌐 दोनों पैनल Chrome में अलग-अलग टैब में खोलें</span>
                    </button>
                </div>
                <div class="flex items-center gap-2 flex-wrap pt-1 text-xs">
                    <span class="text-[11px] text-slate-400 font-bold mr-1">सीधे पोर्टल लिंक:</span>
                    <a href="https://sellercentral.amazon.in/inventory" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 font-bold transition flex items-center gap-1.5 shadow-xs">
                        <i class="fa-brands fa-amazon text-amber-400"></i>
                        <span>Amazon Seller Central Inventory</span>
                        <i class="fa-solid fa-external-link text-[10px]"></i>
                    </a>
                    <a href="https://supplier.meesho.com/panel/v3/new/catalogues" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-pink-500/20 hover:bg-pink-500/30 text-pink-300 border border-pink-500/40 font-bold transition flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-cart-shopping text-pink-400"></i>
                        <span>Meesho Supplier Panel Catalogues</span>
                        <i class="fa-solid fa-external-link text-[10px]"></i>
                    </a>
                    <button type="button" onclick="switchMarketplaceTab('published')" class="px-3.5 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/20 font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-layer-group text-emerald-400"></i>
                        <span>इसी पेज पर लाइव पब्लिश्ड टैब देखें</span>
                    </button>
                </div>
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

        <!-- 💰 TOTAL INVENTORY VALUATION & SYNC AUDIT TOOLBAR -->
        <div class="p-5 rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white border border-indigo-500/30 shadow-lg space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-white/10 pb-4">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-2xl bg-amber-500/20 border border-amber-500/30 text-amber-300 flex items-center justify-center text-2xl shrink-0">
                        <i class="fa-solid fa-vault"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm sm:text-base font-black text-white">दुकान व इन्वेंट्री कुल मूल्य (Total Stock Valuation)</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Live Stock Value</span>
                        </div>
                        <p class="text-xs text-slate-300 mt-0.5">आपके कुल माल की बिक्री कीमत, खरीद लागत और मल्टी-चैनल वर्कस्पेस का लाइव जोड़</p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 flex-wrap">
                    <button type="button" onclick="openSyncAuditModal()" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 transition flex items-center gap-1.5 shadow-xs cursor-pointer active:scale-95">
                        <i class="fa-solid fa-clock-rotate-left text-amber-300"></i>
                        <span>🕒 सिंक व पब्लिश इतिहास (Audit Log)</span>
                    </button>
                    <button type="button" onclick="openWhatsAppPoModal()" class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer active:scale-95">
                        <i class="fa-brands fa-whatsapp text-emerald-200"></i>
                        <span>📱 WhatsApp वेंडर ऑर्डर (PO Generator)</span>
                    </button>
                </div>
            </div>

            <!-- Valuation Details Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                <div class="bg-white/5 p-3 rounded-2xl border border-white/10">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase">दुकान का कुल बिक्री मूल्य</span>
                    <div class="text-lg font-black text-emerald-400 mt-0.5">₹{{ number_format($storeTotalValuation ?? 0, 2) }}</div>
                    <span class="text-[10px] text-slate-300">कुल {{ number_format($storeTotalPieces ?? 0) }} पीस ({{ count($allStoreProducts ?? []) }} प्रोडक्ट्स)</span>
                </div>
                <div class="bg-white/5 p-3 rounded-2xl border border-white/10">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase">दुकान की कुल खरीद लागत</span>
                    <div class="text-lg font-black text-indigo-300 mt-0.5">₹{{ number_format($storeTotalCostValuation ?? 0, 2) }}</div>
                    <span class="text-[10px] text-slate-300">शुद्ध संभावित लाभ: ₹{{ number_format(($storeTotalValuation ?? 0) - ($storeTotalCostValuation ?? 0), 2) }}</span>
                </div>
                <div class="bg-white/5 p-3 rounded-2xl border border-white/10">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase">वर्कस्पेस माल (ई-कॉमर्स)</span>
                    <div class="text-lg font-black text-amber-300 mt-0.5">₹{{ number_format($stagedSellingVal ?? 0, 2) }}</div>
                    <span class="text-[10px] text-slate-300">{{ count($pendingProducts ?? []) }} प्रोडक्ट्स ({{ number_format($stagedPieces ?? 0) }} पीस)</span>
                </div>
                <div class="bg-white/5 p-3 rounded-2xl border border-white/10">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase">सिंक समय सीमा (Timeline)</span>
                    <div class="text-xs font-black text-white mt-1">1st: {{ $firstPublishDate ? \Carbon\Carbon::parse($firstPublishDate)->format('d M Y') : 'Active' }}</div>
                    <span class="text-[10px] text-emerald-400 font-mono">अंतिम: {{ $lastSyncedDate ? \Carbon\Carbon::parse($lastSyncedDate)->diffForHumans() : 'Just now' }}</span>
                </div>
            </div>
        </div>

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
                            <button type="button" onclick="openShippingRateManagerModal()" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-sky-300 hover:text-white font-bold text-xs transition flex items-center gap-1.5 shadow-2xs cursor-pointer hover:scale-102 active:scale-98" title="Amazon, Meesho, Flipkart आदि सभी प्लेटफ़ॉर्म्स के कूरियर दरें (वेट-स्लैब्स) देखें व बदलें">
                                <i class="fa-solid fa-truck-ramp-box text-sky-400"></i>
                                <span>🚚 कूरियर रेट मास्टर</span>
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

                                <div class="flex items-center gap-1 bg-white px-2.5 py-1 rounded-xl border border-indigo-300 shadow-2xs" title="आपका शुद्ध लाभ मार्जिन %">
                                    <span class="text-[10px] text-indigo-950 font-black">मार्जिन %:</span>
                                    <input type="number" id="bulkPresetMargin" value="25" class="w-11 text-xs font-mono font-black text-indigo-700 outline-none text-center">
                                    <span class="text-[10px] text-indigo-400 font-bold">%</span>
                                </div>

                                <div class="flex items-center gap-1.5 bg-white px-2 py-1 rounded-xl border border-purple-300 shadow-2xs" title="प्लेटफ़ॉर्म कमीशन % (Amazon: 12% या 9%, Meesho: 0%)">
                                    <span class="text-[10px] text-purple-900 font-bold">कमीशन %:</span>
                                    <input type="number" id="bulkPresetFee" value="12" class="w-10 text-xs font-mono font-bold text-purple-700 outline-none text-center">
                                    <label class="inline-flex items-center gap-0.5 text-[9px] text-purple-900 font-black cursor-pointer bg-purple-50 px-1 py-0.5 rounded border border-purple-200" title="Amazon फ़ीस पर 18% GST (उदा. ₹56.64 बिल अनुसार)">
                                        <input type="checkbox" id="bulkPresetFeeGst" checked class="rounded text-purple-600 focus:ring-0 w-3 h-3">
                                        <span>+GST</span>
                                    </label>
                                </div>

                                <div class="flex items-center gap-1 bg-white px-2 py-1 rounded-xl border border-rose-300 shadow-2xs" title="RTO / रिटर्न नुकसान से सुरक्षा बफ़र (₹ या %)">
                                    <span class="text-[10px] text-rose-800 font-bold">🔄 RTO:</span>
                                    <select id="bulkPresetRtoMode" class="text-[10px] font-black bg-rose-50 border border-rose-200 text-rose-900 rounded px-1 py-0.5 outline-none cursor-pointer">
                                        <option value="fixed" selected>₹ फिक्स</option>
                                        <option value="pct">% लागत</option>
                                    </select>
                                    <input type="number" id="bulkPresetRto" value="20" class="w-11 text-xs font-mono font-bold text-rose-700 outline-none text-center">
                                </div>

                                <div class="flex items-center gap-1.5 bg-white px-2 py-1 rounded-xl border border-sky-300 shadow-2xs" title="कूरियर डिलीवरी स्लैब (0.5kg=₹55, 1kg=₹75, 2kg=₹112)">
                                    <span class="text-[10px] text-sky-900 font-bold">🚚 कूरियर:</span>
                                    <span class="text-[10px] text-gray-400 font-bold">₹</span>
                                    <input type="number" id="bulkPresetShip" value="55" class="w-11 text-xs font-mono font-bold text-sky-900 outline-none text-center">
                                    <div class="flex items-center gap-0.5">
                                        <button type="button" onclick="setBulkCourier(55)" class="px-1.5 py-0.5 text-[8px] font-black bg-sky-100 hover:bg-sky-200 text-sky-900 rounded border border-sky-300 cursor-pointer" title="0.5 kg (₹55 + 18% GST = ₹64.90)">0.5k</button>
                                        <button type="button" onclick="setBulkCourier(75)" class="px-1.5 py-0.5 text-[8px] font-black bg-sky-100 hover:bg-sky-200 text-sky-900 rounded border border-sky-300 cursor-pointer" title="1.0 kg (₹75 + 18% GST = ₹88.50)">1k</button>
                                        <button type="button" onclick="setBulkCourier(112)" class="px-1.5 py-0.5 text-[8px] font-black bg-sky-100 hover:bg-sky-200 text-sky-900 rounded border border-sky-300 cursor-pointer" title="2.0 kg (₹112 + 18% GST = ₹132.16)">2k</button>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1 bg-white px-2 py-1 rounded-xl border border-amber-300 shadow-2xs" title="पैकिंग खर्च (बॉक्स, टेप, पन्नी)">
                                    <span class="text-[10px] text-gray-700 font-bold">📦 पैकिंग:</span>
                                    <span class="text-[10px] text-gray-400 font-bold">₹</span>
                                    <input type="number" id="bulkPresetPack" value="15" class="w-10 text-xs font-mono font-bold text-gray-800 outline-none text-center">
                                    <div class="flex items-center gap-0.5">
                                        <button type="button" onclick="setBulkPack(5)" class="px-1.5 py-0.5 text-[8px] font-bold bg-slate-100 hover:bg-slate-200 text-slate-800 rounded border border-slate-200 cursor-pointer">5</button>
                                        <button type="button" onclick="setBulkPack(10)" class="px-1.5 py-0.5 text-[8px] font-bold bg-slate-100 hover:bg-slate-200 text-slate-800 rounded border border-slate-200 cursor-pointer">10</button>
                                        <button type="button" onclick="setBulkPack(15)" class="px-1.5 py-0.5 text-[8px] font-bold bg-slate-100 hover:bg-slate-200 text-slate-800 rounded border border-slate-200 cursor-pointer">15</button>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1 bg-white px-2 py-1 rounded-xl border border-gray-300 shadow-2xs" title="ऐड्स व मार्केटिंग खर्च">
                                    <span class="text-[10px] text-gray-700 font-bold">📢 ऐड्स:</span>
                                    <span class="text-[10px] text-gray-400 font-bold">₹</span>
                                    <input type="number" id="bulkPresetAds" value="10" class="w-10 text-xs font-mono font-bold text-gray-800 outline-none text-center">
                                    <div class="flex items-center gap-0.5">
                                        <button type="button" onclick="setBulkAds(0)" class="px-1.5 py-0.5 text-[8px] font-bold bg-slate-100 hover:bg-slate-200 text-slate-800 rounded border border-slate-200 cursor-pointer">0</button>
                                        <button type="button" onclick="setBulkAds(5)" class="px-1.5 py-0.5 text-[8px] font-bold bg-slate-100 hover:bg-slate-200 text-slate-800 rounded border border-slate-200 cursor-pointer">5</button>
                                        <button type="button" onclick="setBulkAds(10)" class="px-1.5 py-0.5 text-[8px] font-bold bg-slate-100 hover:bg-slate-200 text-slate-800 rounded border border-slate-200 cursor-pointer">10</button>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1 bg-white px-2.5 py-1 rounded-xl border border-emerald-300 shadow-2xs" title="सभी प्रोडक्ट्स में एक साथ इन्वेंट्री स्टॉक पीस भरें (उदा. 10, 20 या 50)">
                                    <span class="text-[10px] text-emerald-800 font-bold">📦 स्टॉक Qty:</span>
                                    <input type="number" min="0" id="bulkPresetStock" value="{{ count($pendingProducts ?? []) > 0 ? ($pendingProducts->first()->stock_quantity ?? 3) : 3 }}" class="w-12 text-xs font-mono font-bold text-emerald-800 outline-none text-center">
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
                                    <span>📦 कॉम्बो स्लैब व RTO बजट टूल्स:</span>
                                </div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <button type="button" onclick="toggleAllProductDrawers()" id="btnMasterToggleDrawers" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-black text-xs transition flex items-center gap-1.5 shadow-xs cursor-pointer" title="सभी 19 प्रोडक्ट्स के खरीद, खर्चे व कॉम्बो विवरण एक साथ खोलें या समेटें">
                                        <i class="fa-solid fa-folder-open text-amber-300"></i>
                                        <span id="btnMasterToggleText">📂 सभी विवरण एक साथ खोलें (Expand All)</span>
                                    </button>
                                    <div class="flex items-center gap-2 bg-slate-900 text-white px-3 py-1.5 rounded-2xl border border-indigo-500/30 text-xs">
                                        <span class="text-amber-400 font-bold text-[10px]">अमेज़न 120-फ़ील्ड रेडीनेस:</span>
                                        <div class="w-16 bg-slate-800 h-2 rounded-full overflow-hidden border border-white/10">
                                            <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full" style="width: 92%"></div>
                                        </div>
                                        <span class="font-mono text-emerald-400 font-bold text-[10px]">92%</span>
                                        <button type="button" onclick="openAmazonReadinessModal()" class="px-2 py-0.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-[9px] font-bold cursor-pointer" title="अमेज़न अनिवार्य 120-फ़ील्ड चेकलिस्ट देखें">📋 120-फ़ील्ड</button>
                                        <button type="button" onclick="triggerAutoFillAmazonAttrs()" class="px-2 py-0.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[9px] font-bold cursor-pointer" title="छूटे हुए अमेज़न एट्रिब्यूट्स 1-क्लिक में ऑटो-फ़िल करें">✨ ऑटो-फ़िल</button>
                                    </div>
                                    <button type="button" onclick="openBulkListingAiModal()" class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 active:scale-95 cursor-pointer" title="AI से सभी 19 प्रोडक्ट्स के अमेज़न टाइटल्स, 5 बुलेट पॉइंट्स, विवरण और 250 बाइट्स जेनेरिक कीवर्ड्स एक साथ तैयार करें">
                                        <i class="fa-solid fa-wand-magic-sparkles text-amber-300"></i>
                                        <span>🤖 AI से सभी लिस्टिंग्स तैयार करें (Bulk Amazon Copy)</span>
                                    </button>
                                    <button type="button" onclick="toggleRtoBudgetSimulator()" class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-rose-600 via-orange-600 to-amber-600 hover:from-rose-700 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 active:scale-95 cursor-pointer" title="वास्तविक RTO चपत (Forward ₹65 + Return ₹65 + Pack ₹15 = ₹145) व ब्रेक-ईवन सिम्युलेटर खोलें">
                                        <i class="fa-solid fa-shield-halved text-rose-200"></i>
                                        <span>🛡️ RTO चपत व रिस्क सिम्युलेटर (Break-Even Engine)</span>
                                    </button>
                                    <button type="button" onclick="bulkRecalcCombos('all')" class="px-3 py-1.5 rounded-xl bg-white border border-amber-300 hover:bg-amber-100 text-amber-950 font-bold text-xs transition flex items-center gap-1.5 shadow-2xs cursor-pointer active:scale-95" title="सभी प्रोडक्ट्स के 2P (0.5kg=₹55), 4P (1.0kg=₹75), 6P (2.0kg=₹112) वजन स्लैब के अनुसार ऑटो-कैलकुलेट करें">
                                        <i class="fa-solid fa-calculator text-indigo-600"></i>
                                        <span>⚡ 2P, 4P, 6P ऑटो-कैलकुलेट (वेट स्लैब)</span>
                                    </button>
                                    <button type="button" onclick="bulkRecalcAndSaveCombos()" class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer active:scale-95" title="सभी प्रोडक्ट्स के 2P, 4P, 6P रेट्स री-कैलकुलेट कर तुरंत डेटाबेस में सेव करें">
                                        <i class="fa-solid fa-floppy-disk text-amber-200"></i>
                                        <span>💾 सभी कॉम्बो री-कैलकुलेट व सेव करें</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- 🛡️ INTERACTIVE RTO DRAIN & RISK BUDGETING ENGINE (EXPANDABLE) -->
                        <div id="rtoBudgetSimulatorPanel" class="hidden mt-4 p-5 bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950 text-white rounded-3xl border-2 border-rose-500/40 shadow-2xl space-y-5 animate-in fade-in zoom-in duration-200">
                            <!-- Header -->
                            <div class="flex items-center justify-between border-b border-white/10 pb-3 flex-wrap gap-2">
                                <div class="flex items-center gap-3">
                                    <span class="w-10 h-10 rounded-2xl bg-rose-600 text-white flex items-center justify-center font-bold text-lg shadow-md">
                                        <i class="fa-solid fa-shield-halved"></i>
                                    </span>
                                    <div>
                                        <h3 class="font-black text-base text-white flex items-center gap-2">
                                            <span>🛡️ RTO वास्तविक चपत, खर्च व ब्रेक-ईवन बजट इंजन</span>
                                            <span class="px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[10px] font-bold">100 Parcels Stress-Test</span>
                                        </h3>
                                        <p class="text-xs text-slate-300 mt-0.5">
                                            फॉरवर्ड कूरियर, रिवर्स वापसी चपत, नष्ट पैकिंग व ऐड्स सरप्लस के आधार पर जानिए कि कितने RTO पर आपका मुनाफा शून्य (NIL) होगा।
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="resetRtoSimulatorDefaults()" class="px-3 py-1 rounded-xl bg-white/10 hover:bg-white/20 text-slate-200 text-xs font-bold transition flex items-center gap-1 cursor-pointer">
                                        <i class="fa-solid fa-arrow-rotate-left"></i>
                                        <span>डिफ़ॉल्ट रीसेट</span>
                                    </button>
                                    <button type="button" onclick="toggleRtoBudgetSimulator()" class="h-8 w-8 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-sm cursor-pointer">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- 3 Core Sections: 1. Inputs & Pools, 2. RTO Drain Formula, 3. Break-Even Results -->
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                                <!-- Col 1: Order Batch & Budget Inputs (4 cols) -->
                                <div class="lg:col-span-4 bg-white/5 p-4 rounded-2xl border border-white/10 space-y-3 text-xs">
                                    <div class="font-black text-amber-400 flex items-center gap-1.5 border-b border-white/10 pb-1.5">
                                        <i class="fa-solid fa-sliders"></i>
                                        <span>1. बैच साइज़ व बजट सेटिंग्स</span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2.5">
                                        <div>
                                            <label class="block text-[10px] text-slate-400 font-bold">📦 कुल पार्सल (Batch):</label>
                                            <input type="number" id="sim_batch_size" value="100" min="1" oninput="runRtoSimulation()" class="w-full mt-1 px-2.5 py-1.5 bg-slate-900 border border-white/20 rounded-xl text-center font-mono font-bold text-white text-xs">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] text-slate-400 font-bold">💰 शुद्ध लाभ लक्ष्य (₹/pc):</label>
                                            <input type="number" id="sim_target_profit" value="25" min="0" oninput="runRtoSimulation()" class="w-full mt-1 px-2.5 py-1.5 bg-slate-900 border border-emerald-500/40 rounded-xl text-center font-mono font-bold text-emerald-400 text-xs">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] text-slate-400 font-bold">🔄 RTO रिस्क बफ़र (₹ प्रति पीस):</label>
                                        <input type="number" id="sim_rto_buffer" value="20" min="0" oninput="runRtoSimulation()" class="w-full mt-1 px-2.5 py-1.5 bg-slate-900 border border-rose-500/40 rounded-xl text-center font-mono font-bold text-rose-400 text-xs">
                                    </div>

                                    <!-- Ads & Packing Budget vs Actual (The Surplus Engine) -->
                                    <div class="pt-2 border-t border-white/10 space-y-2">
                                        <span class="text-[11px] font-bold text-purple-300 block">📊 सरप्लस इंजन (बजट vs वास्तविक खर्च):</span>
                                        
                                        <div class="grid grid-cols-2 gap-2 text-[10px]">
                                            <div class="bg-slate-900/80 p-2 rounded-xl border border-white/10">
                                                <span class="text-slate-400 block font-bold">📢 ऐड्स बजट (₹):</span>
                                                <input type="number" id="sim_ads_budget" value="10" min="0" oninput="runRtoSimulation()" class="w-full mt-1 px-2 py-1 bg-slate-800 border border-white/10 rounded font-mono font-bold text-center text-xs text-white">
                                                <span class="text-slate-400 block mt-1">वास्तविक खर्च (₹):</span>
                                                <input type="number" id="sim_ads_actual" value="6" min="0" oninput="runRtoSimulation()" class="w-full mt-1 px-2 py-1 bg-slate-800 border border-emerald-500/40 rounded font-mono font-bold text-center text-xs text-emerald-400">
                                            </div>

                                            <div class="bg-slate-900/80 p-2 rounded-xl border border-white/10">
                                                <span class="text-slate-400 block font-bold">📦 पैकिंग बजट (₹):</span>
                                                <input type="number" id="sim_pack_budget" value="15" min="0" oninput="runRtoSimulation()" class="w-full mt-1 px-2 py-1 bg-slate-800 border border-white/10 rounded font-mono font-bold text-center text-xs text-white">
                                                <span class="text-slate-400 block mt-1">वास्तविक खर्च (₹):</span>
                                                <input type="number" id="sim_pack_actual" value="12" min="0" oninput="runRtoSimulation()" class="w-full mt-1 px-2 py-1 bg-slate-800 border border-emerald-500/40 rounded font-mono font-bold text-center text-xs text-emerald-400">
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between text-[11px] bg-indigo-950/60 p-2 rounded-xl border border-indigo-500/30 font-bold">
                                            <span class="text-indigo-300">खर्च से कुल बचत (Surplus):</span>
                                            <span id="sim_surplus_badge" class="font-mono text-emerald-400">+₹700 बचत</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Col 2: The Real RTO Drain Calculator (4 cols) -->
                                <div class="lg:col-span-4 bg-white/5 p-4 rounded-2xl border border-white/10 space-y-3 text-xs flex flex-col justify-between">
                                    <div class="space-y-3">
                                        <div class="font-black text-rose-400 flex items-center gap-1.5 border-b border-white/10 pb-1.5">
                                            <i class="fa-solid fa-triangle-exclamation"></i>
                                            <span>2. वास्तविक 1 RTO पार्सल की चपत (Cash Drain)</span>
                                        </div>

                                        <div class="space-y-2 text-[11px]">
                                            <div class="flex items-center justify-between">
                                                <span class="text-slate-300">जावक डिलीवरी (Forward Ship + GST):</span>
                                                <div class="w-20">
                                                    <input type="number" id="sim_fwd_ship" value="65" oninput="runRtoSimulation()" class="w-full px-2 py-1 bg-slate-900 border border-white/20 rounded font-mono font-bold text-right text-rose-300 text-xs">
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-slate-300">वापसी डिलीवरी (Reverse Ship + GST):</span>
                                                <div class="w-20">
                                                    <input type="number" id="sim_rev_ship" value="65" oninput="runRtoSimulation()" class="w-full px-2 py-1 bg-slate-900 border border-white/20 rounded font-mono font-bold text-right text-rose-300 text-xs">
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-slate-300">नष्ट पैकेजिंग (बॉक्स + टेप + लेबल):</span>
                                                <div class="w-20">
                                                    <input type="number" id="sim_wasted_pack" value="15" oninput="runRtoSimulation()" class="w-full px-2 py-1 bg-slate-900 border border-white/20 rounded font-mono font-bold text-right text-rose-300 text-xs">
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-slate-300">सामान डैमेज/स्क्रैच रिस्क:</span>
                                                <div class="w-20">
                                                    <input type="number" id="sim_damage_loss" value="0" oninput="runRtoSimulation()" class="w-full px-2 py-1 bg-slate-900 border border-white/20 rounded font-mono font-bold text-right text-rose-300 text-xs">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Big Total Drain Pill -->
                                    <div class="bg-rose-950/70 p-3 rounded-2xl border-2 border-rose-500/50 space-y-1">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-black text-rose-200">1 RTO पार्सल पर कुल नुकसान:</span>
                                            <strong class="text-lg font-mono font-black text-rose-400" id="sim_total_drain_per_rto">₹145.00</strong>
                                        </div>
                                        <p class="text-[10px] text-rose-300/80 leading-tight">
                                            *कूरियर डिलीवरी सेवा पूरी होने के कारण जीएसटी का इनपुट टैक्स रिफंड नहीं मिलता, जिससे पूरा ₹130 कूरियर + ₹15 पैकिंग सेलर की जेब से जाता है।
                                        </p>
                                    </div>
                                </div>

                                <!-- Col 3: Break-Even Engine & Thresholds (4 cols) -->
                                <div class="lg:col-span-4 bg-white/5 p-4 rounded-2xl border border-white/10 space-y-3 text-xs flex flex-col justify-between">
                                    <div class="space-y-3">
                                        <div class="font-black text-emerald-400 flex items-center gap-1.5 border-b border-white/10 pb-1.5">
                                            <i class="fa-solid fa-chart-pie"></i>
                                            <span>3. ब्रेक-ईवन थ्रेशोल्ड (सुरक्षा सीमा)</span>
                                        </div>

                                        <div class="space-y-2">
                                            <div class="bg-slate-900/80 p-2.5 rounded-xl border border-emerald-500/30 flex items-center justify-between">
                                                <div>
                                                    <span class="text-[10px] text-emerald-400 font-bold block">🟢 100% सेफ RTO सीमा (Safe Zone):</span>
                                                    <span class="text-[9px] text-slate-400">यहाँ तक आपका मूल ₹2,500 मुनाफ़ा सुरक्षित रहेगा</span>
                                                </div>
                                                <span class="text-base font-black font-mono text-emerald-300" id="sim_safe_rto_badge">18.6%</span>
                                            </div>

                                            <div class="bg-slate-900/80 p-2.5 rounded-xl border border-rose-500/40 flex items-center justify-between">
                                                <div>
                                                    <span class="text-[10px] text-rose-400 font-black block">🔴 मुनाफ़ा शून्य होने की सीमा (NIL Profit):</span>
                                                    <span class="text-[9px] text-slate-400">इसके बाद अपनी जेब से घर का पैसा लगेगा</span>
                                                </div>
                                                <span class="text-base font-black font-mono text-rose-400" id="sim_nil_profit_badge">35.8%</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Budget Pool Breakdown -->
                                    <div class="bg-indigo-950/60 p-2.5 rounded-xl border border-indigo-500/30 text-[10px] space-y-1">
                                        <div class="flex justify-between text-slate-300">
                                            <span>RTO बफ़र पूल (100 × ₹20):</span>
                                            <strong class="font-mono text-white" id="sim_pool_rto">₹2,000</strong>
                                        </div>
                                        <div class="flex justify-between text-slate-300">
                                            <span>ऐड्स व पैकिंग बचत पूल:</span>
                                            <strong class="font-mono text-emerald-400" id="sim_pool_surplus">+₹700</strong>
                                        </div>
                                        <div class="flex justify-between text-slate-300">
                                            <span>मूल शुद्ध मुनाफ़ा पूल:</span>
                                            <strong class="font-mono text-indigo-300" id="sim_pool_profit">₹2,500</strong>
                                        </div>
                                        <div class="flex justify-between text-amber-300 pt-1 border-t border-white/10 font-bold">
                                            <span>कुल सुरक्षा कवच (Buffer + Surplus + Profit):</span>
                                            <strong class="font-mono" id="sim_pool_total">₹5,200</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Live Interactive RTO Test Slider -->
                            <div class="bg-slate-900/90 p-4 rounded-2xl border border-white/10 space-y-3">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <span class="text-xs font-black text-amber-400 flex items-center gap-1.5">
                                        <i class="fa-solid fa-gamepad text-rose-400"></i>
                                        <span>लाइव RTO स्ट्रेस-टेस्ट स्लाइडर: चलाकर देखें क्या बचेगा!</span>
                                    </span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-slate-300">वर्तमान RTO दर:</span>
                                        <span class="text-sm font-black font-mono px-2.5 py-0.5 rounded-lg bg-indigo-600 text-white" id="sim_slider_val_badge">15% RTO (15 पार्सल)</span>
                                    </div>
                                </div>

                                <div class="relative pt-1">
                                    <input type="range" id="sim_rto_slider" min="0" max="50" step="1" value="15" oninput="runRtoSimulation()" class="w-full h-2.5 bg-slate-700 rounded-lg appearance-none cursor-pointer accent-rose-500">
                                    <div class="flex justify-between text-[10px] text-slate-400 mt-1 font-mono font-bold">
                                        <span>0% (आदर्श स्थिति)</span>
                                        <span class="text-emerald-400">18% (सुरक्षित सीमा)</span>
                                        <span class="text-rose-400">35% (मुनाफ़ा शून्य NIL)</span>
                                        <span>50% (भारी नुकसान)</span>
                                    </div>
                                </div>

                                <!-- Dynamic Live Result Card -->
                                <div id="sim_live_status_card" class="p-3.5 rounded-2xl border flex items-center justify-between flex-wrap gap-3">
                                    <!-- Populated by runRtoSimulation() -->
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
                                $mrpVal = (float)($p->mrp ?: ($p->variants->where('mrp', '>', 0)->min('mrp') ?: 0));
                                $lessVal = ($p->trade_discount_percent !== null && (float)$p->trade_discount_percent > 0)
                                    ? (float)$p->trade_discount_percent
                                    : ($p->variants->where('trade_discount_percent', '>', 0)->max('trade_discount_percent') ?: ($ue['trade_discount_percent'] ?? ''));
                                $marginPct = (float)($ue['margin_pct'] ?? 25);
                                $rtoBuffer = (float)($ue['rto_buffer'] ?? 20);
                                $packCost = (float)($ue['packaging'] ?? 15);
                                $shipCost = (float)($ue['shipping'] ?? 55);
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

                                // 2P, 4P, 6P Combo Rates for Amazon
                                $combo2 = !empty($ue['combo_2']) ? (float)$ue['combo_2'] : ceil((($cTax * 2) + ($desiredProfit * 1.8) + ($rtoBuffer * 1.5) + ($packCost * 1.3) + $shipCost + ($adsCost * 1.2)) / $denomAmz);
                                $combo4 = !empty($ue['combo_4']) ? (float)$ue['combo_4'] : ceil((($cTax * 4) + ($desiredProfit * 3.5) + ($rtoBuffer * 2.0) + ($packCost * 1.6) + ($shipCost * 1.2) + ($adsCost * 1.5)) / $denomAmz);
                                $combo6 = !empty($ue['combo_6']) ? (float)$ue['combo_6'] : ceil((($cTax * 6) + ($desiredProfit * 5.0) + ($rtoBuffer * 2.5) + ($packCost * 2.0) + ($shipCost * 1.4) + ($adsCost * 1.8)) / $denomAmz);

                                // 2P, 4P, 6P Combo Rates for Meesho (0% Platform Fee, RTO 35, Courier 38)
                                $mshCombo2 = ceil((($cTax * 2) + ($desiredProfit * 1.8) + (max(35, $rtoBuffer) * 1.4) + ($packCost * 1.3) + min(38, $shipCost)) / $denomMsh);
                                $mshCombo4 = ceil((($cTax * 4) + ($desiredProfit * 3.5) + (max(35, $rtoBuffer) * 1.8) + ($packCost * 1.6) + (min(38, $shipCost) * 1.15)) / $denomMsh);
                                $mshCombo6 = ceil((($cTax * 6) + ($desiredProfit * 5.0) + (max(35, $rtoBuffer) * 2.2) + ($packCost * 2.0) + (min(38, $shipCost) * 1.3)) / $denomMsh);
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

                                <!-- Dedicated Add 3-4 Photos Button & Gallery Preview Strip -->
                                <div class="space-y-1.5 pt-0.5">
                                    <button type="button" onclick="triggerMultipleImageUpload({{ $p->id }}, '{{ addslashes($p->name) }}')" class="w-full py-2 px-3 rounded-2xl bg-gradient-to-r from-indigo-50 via-purple-50 to-indigo-50 hover:from-indigo-100 hover:to-purple-100 border border-indigo-200/90 text-indigo-950 font-black text-xs transition flex items-center justify-between shadow-2xs hover:shadow-xs hover:border-indigo-400 active:scale-98 cursor-pointer group" title="फ़ोन गैलरी या कंप्यूटर से 3-4 नई फ़ोटो एक साथ जोड़ें">
                                        <span class="flex items-center gap-1.5">
                                            <span class="h-6 w-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs group-hover:scale-110 transition shadow-2xs">
                                                <i class="fa-solid fa-camera"></i>
                                            </span>
                                            <span class="text-indigo-900 font-extrabold">+ 3-4 नई फ़ोटो जोड़ें (Add Images)</span>
                                        </span>
                                        <span id="card-img-count-badge-{{ $p->id }}" class="px-2 py-0.5 rounded-lg bg-white border border-indigo-200 text-indigo-700 text-[10px] font-mono font-bold shadow-2xs">
                                            📸 {{ $imgCount }} फ़ोटो
                                        </span>
                                    </button>

                                    <!-- Mini Gallery Preview Strip (Currently saved images) -->
                                    <div class="flex items-center gap-1.5 px-1 overflow-x-auto pb-0.5" id="card-gallery-strip-{{ $p->id }}">
                                        @if(!empty($p->image_url))
                                            <div class="h-8 w-8 rounded-lg border border-indigo-200 overflow-hidden bg-white p-0.5 shrink-0 shadow-2xs" title="मुख्य फ़ोटो">
                                                <img src="{{ $p->image_url }}" alt="Primary" class="h-full w-full object-contain">
                                            </div>
                                        @endif
                                        @foreach($p->images ?? [] as $gImg)
                                            @if($gImg->image_path !== $p->image)
                                                <div class="h-8 w-8 rounded-lg border border-gray-200 overflow-hidden bg-white p-0.5 shrink-0 shadow-2xs" title="गैलरी फ़ोटो">
                                                    <img src="{{ $gImg->url }}" alt="Gallery" class="h-full w-full object-contain">
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Unit Economics Breakdown Badge -->
                                <div class="p-2.5 bg-slate-50 rounded-2xl border border-gray-100 text-[11px] space-y-1">
                                    <div class="flex items-center justify-between text-amber-900 text-[10px] font-bold pb-1 border-b border-gray-100 gap-1 flex-wrap">
                                        <div class="inline-flex items-center gap-1 bg-white px-1.5 py-0.5 rounded border border-amber-300 shadow-2xs">
                                            <span class="text-[10px] text-amber-950 font-black">MRP ₹:</span>
                                            <input type="number" step="any" min="0" id="card-mrp-{{ $p->id }}" value="{{ $mrpVal > 0 ? $mrpVal : '' }}" oninput="recalcCardMrpAndLessToCost({{ $p->id }})" class="w-14 px-1 py-0.5 bg-white font-mono font-black text-amber-950 text-center text-[10px] outline-none" placeholder="MRP">
                                        </div>
                                        <div class="inline-flex items-center gap-1 bg-amber-50/70 px-2 py-0.5 rounded-lg border border-amber-200">
                                            <span class="text-[10px] text-amber-950 font-bold">Less:</span>
                                            <input type="number" step="0.5" min="0" max="99" id="card-less-{{ $p->id }}" data-mrp="{{ $mrpVal }}" value="{{ $lessVal !== '' && $lessVal > 0 ? $lessVal : '' }}" oninput="recalcCardMrpAndLessToCost({{ $p->id }})" class="w-12 px-1 py-0.5 bg-white border border-amber-300 rounded font-mono font-black text-amber-900 text-center text-[10px] outline-none" placeholder="Less%">
                                            <span class="text-rose-600 font-black">%</span>
                                        </div>
                                    </div>
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

                                <!-- 2P, 4P, 6P Combo Rates directly on Card (Amazon & Meesho Side-by-Side) -->
                                <div class="p-2.5 bg-gradient-to-r from-purple-50 via-indigo-50 to-purple-50 rounded-2xl border border-purple-200/80 text-[10px] space-y-2 shadow-2xs">
                                    <div class="flex items-center justify-between font-black text-purple-950">
                                        <span class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-layer-group text-purple-600"></i>
                                            <span>मल्टी-पैक कॉम्बो रेट्स (2 / 4 / 6 पीस):</span>
                                        </span>
                                        <button type="button" onclick="openComboCalculatorModal({{ $p->id }}, '{{ addslashes($p->name) }}')" class="text-[9px] text-purple-700 hover:text-purple-900 font-bold underline cursor-pointer" title="कॉम्बो खर्च व मुनाफा विवरण खोलें">
                                            कैलकुलेटर ↗
                                        </button>
                                    </div>

                                    <!-- 🟠 Amazon 2/4/6 Combo Rates Row -->
                                    <div class="space-y-1">
                                        <div class="flex items-center justify-between text-[9px] font-black text-amber-950 px-0.5">
                                            <span class="flex items-center gap-1">
                                                <i class="fa-brands fa-amazon text-amber-600 text-xs"></i>
                                                <span>Amazon कॉम्बो भाव:</span>
                                            </span>
                                            <span class="text-[8px] text-amber-700 font-medium">(12% फ़ी सहित)</span>
                                        </div>
                                        <div class="grid grid-cols-3 gap-1.5 text-center font-mono">
                                            <div class="bg-amber-50/70 p-1.5 rounded-xl border border-amber-200 shadow-2xs">
                                                <div class="text-[8px] font-bold text-amber-800 font-sans">2P Amazon</div>
                                                <div class="text-xs font-black text-amber-950" id="card-combo2-{{ $p->id }}">₹{{ $combo2 }}</div>
                                            </div>
                                            <div class="bg-amber-50/70 p-1.5 rounded-xl border border-amber-200 shadow-2xs">
                                                <div class="text-[8px] font-bold text-amber-800 font-sans">4P Amazon</div>
                                                <div class="text-xs font-black text-amber-950" id="card-combo4-{{ $p->id }}">₹{{ $combo4 }}</div>
                                            </div>
                                            <div class="bg-amber-50/70 p-1.5 rounded-xl border border-amber-200 shadow-2xs">
                                                <div class="text-[8px] font-bold text-amber-800 font-sans">6P Amazon</div>
                                                <div class="text-xs font-black text-amber-950" id="card-combo6-{{ $p->id }}">₹{{ $combo6 }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 🌸 Meesho 2/4/6 Combo Rates Row -->
                                    <div class="space-y-1 pt-1.5 border-t border-purple-200/60">
                                        <div class="flex items-center justify-between text-[9px] font-black text-pink-950 px-0.5">
                                            <span class="flex items-center gap-1">
                                                <span class="text-xs">🌸</span>
                                                <span>Meesho कॉम्बो भाव:</span>
                                            </span>
                                            <span class="text-[8px] text-pink-700 font-medium">(0% फ़ी, RTO सुरक्षित)</span>
                                        </div>
                                        <div class="grid grid-cols-3 gap-1.5 text-center font-mono">
                                            <div class="bg-pink-50/70 p-1.5 rounded-xl border border-pink-200 shadow-2xs">
                                                <div class="text-[8px] font-bold text-pink-800 font-sans">2P Meesho</div>
                                                <div class="text-xs font-black text-pink-950" id="card-msh-combo2-{{ $p->id }}">₹{{ $mshCombo2 }}</div>
                                            </div>
                                            <div class="bg-pink-50/70 p-1.5 rounded-xl border border-pink-200 shadow-2xs">
                                                <div class="text-[8px] font-bold text-pink-800 font-sans">4P Meesho</div>
                                                <div class="text-xs font-black text-pink-950" id="card-msh-combo4-{{ $p->id }}">₹{{ $mshCombo4 }}</div>
                                            </div>
                                            <div class="bg-pink-50/70 p-1.5 rounded-xl border border-pink-200 shadow-2xs">
                                                <div class="text-[8px] font-bold text-pink-800 font-sans">6P Meesho</div>
                                                <div class="text-xs font-black text-pink-950" id="card-msh-combo6-{{ $p->id }}">₹{{ $mshCombo6 }}</div>
                                            </div>
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
                                    <div class="inline-flex items-center gap-1.5 bg-emerald-50 px-2 py-0.5 rounded-xl border border-emerald-200 shadow-2xs">
                                        <span class="text-[10px] text-emerald-950 font-bold">📦 स्टॉक:</span>
                                        <input type="number" min="0" id="card-stock-inp-{{ $p->id }}" value="{{ $p->stock_quantity ?? 0 }}" oninput="syncCardStockToRow({{ $p->id }}, this.value)" class="w-12 px-1 py-0.5 bg-white border border-emerald-300 rounded-lg font-mono font-black text-emerald-900 text-center text-xs outline-none focus:ring-1 focus:ring-emerald-500" placeholder="Qty">
                                        <span class="text-emerald-700 font-bold text-[10px]">pcs</span>
                                    </div>
                                    <span>🏷️ HSN: <strong class="font-mono text-gray-700 font-bold">{{ $p->hsn_code ?: '39174000' }}</strong></span>
                                </div>

                                <!-- Action Buttons with Prominent Remove from Multichannel Button -->
                                <div class="pt-1 flex items-center justify-between gap-1.5 flex-wrap">
                                    <button type="button" onclick="openListingContentModal({{ $p->id }}, '{{ addslashes($p->name) }}')" class="p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 transition cursor-pointer" title="🤖 AI असिस्टेंट: टाइटल, 5 बुलेट पॉइंट्स, विवरण व 248 बाइट्स कीवर्ड्स 1-क्लिक में खुद लिखवाएं">
                                                    <i class="fa-solid fa-wand-magic-sparkles text-xs text-amber-600"></i>
                                                </button>
                                                <button type="button" onclick="openCostCalculatorModal({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->price }}, {{ $pCost }})" class="flex-1 py-1.5 px-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[10px] font-black transition flex items-center justify-center gap-1 shadow-2xs cursor-pointer" title="1 पीस खर्च व मुनाफा देखें">
                                        <i class="fa-solid fa-calculator"></i>
                                        <span>1P खर्च</span>
                                    </button>
                                    <button type="button" onclick="openComboCalculatorModal({{ $p->id }}, '{{ addslashes($p->name) }}')" class="flex-1 py-1.5 px-2 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 text-[10px] font-black transition flex items-center justify-center gap-1 shadow-2xs cursor-pointer" title="2P, 4P, 6P कॉम्बो विवरण खोलें">
                                        <i class="fa-solid fa-layer-group"></i>
                                        <span>2/4/6P</span>
                                    </button>
                                    <button type="button" onclick="openListingContentModal({{ $p->id }}, '{{ addslashes($p->name) }}')" class="py-1.5 px-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 text-[10px] font-black transition flex items-center justify-center gap-1 shadow-2xs cursor-pointer" title="📝 अमेज़न लिस्टिंग कंटेंट (टाइटल, 5 बुलेट पॉइंट्स, विवरण व 250 बाइट्स कीवर्ड्स)">
                                        <i class="fa-solid fa-file-lines text-amber-600 text-xs"></i>
                                        <span>लिस्टिंग</span>
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
                        <!-- ↔️ Table Screen Column Navigation Toolbar (Left & Right Screen Scroll) -->
                        <div class="bg-gradient-to-r from-indigo-900 via-indigo-950 to-slate-900 text-white p-3 rounded-2xl shadow-md flex items-center justify-between flex-wrap gap-2.5 text-xs">
                            <!-- 📁 Master Box Collapser / Expander Button (User's Sametne Wala Button) -->
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="bulkSyncAmazonFees()" id="btnBulkSyncAmazonFees" class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer active:scale-95 border border-amber-300" title="क्लिक करें: सभी 19 प्रोडक्ट्स के लिए Amazon SP-API / Category Matrix से असली फ़ीस फेच करें और सेलिंग रेट्स ऑटो-सॉल्व करें">
                                    <i class="fa-solid fa-bolt text-slate-950"></i>
                                    <span id="btnBulkSyncText">⚡ अमेज़न असली फ़ीस सिंक करें (SP-API)</span>
                                </button>
                                <button type="button" onclick="openMultiChannelCompareModal()" class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-sky-600 to-blue-700 hover:from-sky-700 hover:to-blue-800 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer active:scale-95 border border-sky-400" title="क्लिक करें: Amazon, Meesho, Flipkart, Myntra व Blinkit के खर्च व मुनाफे की एक साथ तुलना">
                                    <i class="fa-solid fa-chart-pie text-amber-300"></i>
                                    <span>📊 5-मार्केटप्लेस तुलना</span>
                                </button>
                                <button type="button" onclick="bulkAutoHarvestKeywords()" id="btnBulkHarvestKeywords" class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer active:scale-95 border border-emerald-400" title="क्लिक करें: सभी प्रोडक्ट्स के लिए खरीदारों के असली सर्च कीवर्ड्स (248 बाइट्स) खुद ढूंढकर ऑटो-अपडेट करें">
                                    <i class="fa-solid fa-wand-magic-sparkles text-amber-300"></i>
                                    <span id="btnBulkHarvestText">🤖 कीवर्ड्स ऑटो-हार्वेस्ट व अपडेट करें</span>
                                </button>
                                <button type="button" onclick="openBulkListingAiModal()" class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-violet-600 to-purple-700 hover:from-violet-700 hover:to-purple-800 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer active:scale-95 border border-purple-400" title="क्लिक करें: AI असिस्टेंट से सभी 19 प्रोडक्ट्स के टाइटल, 5 बुलेट पॉइंट्स, विवरण और कीवर्ड्स 1-क्लिक में खुद लिखवाएं">
                                    <i class="fa-solid fa-robot text-amber-300"></i>
                                    <span>🪄 AI असिस्टेंट (पूरी लिस्टिंग लिखें)</span>
                                </button>
                                <button type="button" onclick="toggleCompactTableMode()" id="btnCompactTableMode" class="px-4 py-2 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-black text-xs shadow-md transition flex items-center gap-2 cursor-pointer active:scale-95 border border-purple-400/40" title="क्लिक करें: सारे 8 खर्चे (कूरियर, RTO, पैकिंग, कमीशन, ऐड्स) एक ही बॉक्स में समेटें">
                                    <i class="fa-solid fa-compress" id="btnCompactIcon"></i>
                                    <span id="btnCompactText">📁 सारे खर्चे 1 बॉक्स में समेटें (Compact View)</span>
                                </button>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="scrollTableHorizontally(-450)" class="px-3.5 py-1.5 rounded-xl bg-white/10 hover:bg-white/25 text-white font-black transition flex items-center gap-1.5 border border-white/20 active:scale-95 cursor-pointer shadow-2xs" title="स्क्रीन को बाएँ सरकाएं (Left Columns: फ़ोटो, नाम, स्टॉक, खरीद)">
                                    <i class="fa-solid fa-arrow-left text-amber-300"></i>
                                    <span>◀ स्क्रीन बाएँ सरकाएं (नाम, स्टॉक, खरीद)</span>
                                </button>
                                <button type="button" onclick="scrollTableToStart()" class="px-2.5 py-1.5 rounded-xl bg-white/10 hover:bg-white/25 text-slate-300 hover:text-white font-bold text-[11px] border border-white/10 active:scale-95 cursor-pointer" title="बिल्कुल शुरू में जाएँ">
                                    <i class="fa-solid fa-angles-left"></i>
                                </button>
                            </div>

                            <!-- Fast Column Jump Pills -->
                            <div class="flex items-center gap-1 text-[11px] font-bold">
                                <span class="text-indigo-300 font-sans hidden sm:inline text-[10px] uppercase font-black mr-1">कॉलम देखें:</span>
                                <button type="button" onclick="scrollTableToStart()" class="px-2.5 py-1 rounded-lg bg-indigo-800/80 hover:bg-indigo-700 text-white cursor-pointer transition shadow-2xs">
                                    1. फ़ोटो, नाम & स्टॉक
                                </button>
                                <button type="button" onclick="scrollTableToMid()" class="px-2.5 py-1 rounded-lg bg-indigo-800/80 hover:bg-indigo-700 text-white cursor-pointer transition shadow-2xs">
                                    2. कूरियर, RTO & फ़ी
                                </button>
                                <button type="button" onclick="scrollTableToEnd()" class="px-2.5 py-1 rounded-lg bg-amber-500 hover:bg-amber-600 text-slate-950 font-black cursor-pointer transition shadow-2xs">
                                    3. फाइनल सेलिंग & कॉम्बो 2/4/6P 🎯
                                </button>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" onclick="scrollTableToEnd()" class="px-2.5 py-1.5 rounded-xl bg-white/10 hover:bg-white/25 text-slate-300 hover:text-white font-bold text-[11px] border border-white/10 active:scale-95 cursor-pointer" title="बिल्कुल अंत में जाएँ">
                                    <i class="fa-solid fa-angles-right"></i>
                                </button>
                                <button type="button" onclick="scrollTableHorizontally(450)" class="px-3.5 py-1.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-950 font-black transition flex items-center gap-1.5 shadow-md active:scale-95 cursor-pointer" title="स्क्रीन को दाएँ सरकाएं (Right Columns: कूरियर, फ़ी, फाइनल सेलिंग भाव, कॉम्बो)">
                                    <span>स्क्रीन दाएँ सरकाएं (सेलिंग भाव, कॉम्बो) ▶</span>
                                    <i class="fa-solid fa-arrow-right text-slate-950"></i>
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto bg-white rounded-3xl border border-gray-200 shadow-sm relative scroll-smooth" id="pendingTableScrollWrapper">
                            <table class="w-full text-left text-xs divide-y divide-gray-200">
                            <thead class="bg-gray-100 text-[10px] font-black uppercase text-gray-600 tracking-wider">
                                <tr>
                                    <th class="p-3 w-8 text-center">✓</th>
                                    <th class="p-3">फ़ोटो</th>
                                    <th class="p-3 min-w-[170px]">प्रोडक्ट का नाम & SKU</th>
                                    <th class="p-3 min-w-[70px] text-center font-mono text-gray-500">ID</th>
                                    <th class="p-3 min-w-[90px] bg-emerald-50 text-emerald-950 font-black text-center" title="उपलब्ध इन्वेंट्री / स्टॉक की संख्या">📦 स्टॉक (Qty)</th>
                                    <th class="p-3 min-w-[110px] bg-amber-50 text-amber-950 font-black col-cost-detail" title="कंपनी कैटलॉग की प्राइस लिस्ट (List Price)">प्राइस लिस्ट (Price List ₹)</th>
                                    <th class="p-3 min-w-[75px] bg-amber-50 text-amber-950 font-black col-cost-detail">Less %</th>
                                    <th class="p-3 min-w-[105px] bg-slate-50 text-slate-900 font-extrabold" title="आपकी खरीद लागत (GST सहित)">खरीद लागत (With GST ₹)</th>
                                    <th class="p-3 min-w-[115px] bg-blue-50 text-blue-950 font-black col-cost-detail" title="अमेज़न 1 पीस बिक्री दर (Selling Price 1)">सेलिंग भाव 1 (Amz सिंगल ₹)</th>
                                    <!-- 📁 SUMMARY EXPENSES COLUMN (Visible when Compact Mode is Active) -->
                                    <th class="p-3 min-w-[140px] bg-gradient-to-r from-purple-100 to-indigo-100 text-indigo-950 font-black text-center col-expense-summary hidden" title="कूरियर + RTO + पैकिंग + कमीशन + ऐड्स सब 1 ही बॉक्स में समेटा हुआ">
                                        📁 कुल खर्चे (1 बॉक्स ₹)
                                    </th>
                                    <th class="p-3 min-w-[90px] bg-indigo-50 text-indigo-950 font-black col-expense-detail" title="मार्केटप्लेस के लिए आपका मनपसंद मुनाफा %">मार्जिन (%)</th>
                                    <th class="p-3 min-w-[95px] bg-rose-50 text-rose-950 font-black col-expense-detail" title="रिटर्न/RTO नुकसान से सुरक्षा (₹ या %)">🔄 रिटर्न बफ़र (₹/%)</th>
                                    <th class="p-3 min-w-[80px] col-expense-detail">पैकिंग (₹)</th>
                                    <th class="p-3 min-w-[110px] bg-sky-50 text-sky-950 font-black col-expense-detail" title="प्रोडक्ट का वजन (kg) व कूरियर दर">वजन & कूरियर (₹+GST)</th>
                                    <th class="p-3 min-w-[115px] bg-purple-50 text-purple-950 font-black col-expense-detail" title="मार्केटप्लेस कमीशन फ़ी (%): Amazon SP-API असली फ़ीस या कैटेगरी स्लैब">कमीशन फ़ी (%+GST) <span class="block text-[8px] font-normal text-purple-700">SP-API असली / अनुमान</span></th>
                                    <th class="p-3 min-w-[80px] col-expense-detail">ऐड्स (₹)</th>
                                    <th class="p-3 min-w-[75px] bg-purple-50 text-purple-950 font-black col-expense-detail">HSN कोड</th>
                                    <th class="p-3 min-w-[75px] bg-purple-50 text-purple-950 font-black col-expense-detail">GST %</th>
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
                                        $mrpVal = (float)($p->mrp ?: ($p->variants->where('mrp', '>', 0)->min('mrp') ?: 0));
                                        $lessVal = ($p->trade_discount_percent !== null && (float)$p->trade_discount_percent > 0)
                                            ? (float)$p->trade_discount_percent
                                            : ($p->variants->where('trade_discount_percent', '>', 0)->max('trade_discount_percent') ?: ($ue['trade_discount_percent'] ?? ''));
                                        $marginPct = (float)($ue['margin_pct'] ?? 25);
                                        $rtoBuffer = (float)($ue['rto_buffer'] ?? 20);
                                        $packCost = (float)($ue['packaging'] ?? 15);
                                        $shipCost = (float)($ue['shipping'] ?? 55);
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

                                        // 2P, 4P, 6P Combo Rates for Meesho (0% Platform Fee, RTO 35, Courier 38)
                                        $mshCombo2 = ceil((($cTax * 2) + ($desiredProfit * 1.8) + (max(35, $rtoBuffer) * 1.4) + ($packCost * 1.3) + min(38, $shipCost)) / $denomMsh);
                                        $mshCombo4 = ceil((($cTax * 4) + ($desiredProfit * 3.5) + (max(35, $rtoBuffer) * 1.8) + ($packCost * 1.6) + (min(38, $shipCost) * 1.15)) / $denomMsh);
                                        $mshCombo6 = ceil((($cTax * 6) + ($desiredProfit * 5.0) + (max(35, $rtoBuffer) * 2.2) + ($packCost * 2.0) + (min(38, $shipCost) * 1.3)) / $denomMsh);
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
                                        <td class="p-2 bg-amber-50/40 text-center col-cost-detail">
                                            <div class="inline-flex items-center gap-1 bg-white px-2 py-1 rounded-lg border border-amber-300 shadow-2xs">
                                                <span class="text-[10px] text-amber-700 font-black">₹</span>
                                                <input type="number" step="any" min="0" 
                                                    id="row-mrp-{{ $p->id }}" 
                                                    data-prod-id="{{ $p->id }}"
                                                    value="{{ $mrpVal > 0 ? $mrpVal : '' }}" 
                                                    oninput="recalcRowMrpAndLessToCost({{ $p->id }})" 
                                                    class="w-16 px-1 py-0.5 bg-white font-mono font-black text-amber-950 text-center text-xs outline-none" 
                                                    title="वास्तविक डिब्बा MRP (प्रिंटेड भाव ₹)" 
                                                    placeholder="MRP">
                                            </div>
                                        </td>
                                        <td class="p-2 bg-amber-50/40 text-center col-cost-detail">
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
                                        <td class="p-2 bg-blue-50/40 text-center col-cost-detail">
                                            <span id="row-store-price-{{ $p->id }}" class="inline-block px-2.5 py-1 bg-white border border-blue-200 rounded-lg font-mono font-black text-xs text-blue-900 shadow-2xs" title="दुकान / ऑनलाइन बिक्री भाव">
                                                ₹{{ number_format($p->price, 2) }}
                                            </span>
                                        </td>

                                        <!-- 📁 COMBINED SUMMARY EXPENSES TD (Visible in Compact Mode - User's Sametne Wala Box) -->
                                        <td class="p-2 text-center col-expense-summary hidden">
                                            <div class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 border-2 border-indigo-300 p-2 rounded-2xl text-center cursor-pointer hover:bg-indigo-100 hover:scale-105 transition shadow-sm" onclick="openCostCalculatorModal({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->price }}, {{ $pCost }})" title="क्लिक करें: सारे 8 खर्चे पॉपअप में खोलें व बदलें">
                                                <div class="text-[9px] text-indigo-700 font-black uppercase tracking-tight flex items-center justify-center gap-1">
                                                    <i class="fa-solid fa-receipt text-[8px]"></i>
                                                    <span>कुल खर्चे (8 मदें)</span>
                                                </div>
                                                <div class="text-sm font-black text-indigo-950 font-mono mt-0.5" id="row-total-exp-{{ $p->id }}">₹0.00</div>
                                                <div class="text-[8px] text-purple-700 font-bold truncate mt-0.5" id="row-exp-break-{{ $p->id }}">कूरियर+RTO+पैक+फ़ी</div>
                                            </div>
                                        </td>

                                        <!-- 1.5 Your Profit Margin (%) -->
                                        <td class="p-2 bg-indigo-50/40 col-expense-detail">
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

                                        <!-- 1.8 Return / RTO Buffer (₹ or %) -->
                                        <td class="p-2 bg-rose-50/40 col-expense-detail">
                                            <div class="flex items-center gap-1">
                                                <input type="number" id="row-rto-{{ $p->id }}" value="{{ (float)($ue['rto_buffer'] ?? 20) }}" oninput="recalcTableRow({{ $p->id }})" class="w-12 px-1 py-1 bg-white border border-rose-300 rounded-lg text-xs font-mono font-bold text-rose-700 text-center focus:border-rose-500 focus:outline-none" title="रिटर्न/RTO नुकसान बफ़र">
                                                <select id="row-rto-mode-{{ $p->id }}" onchange="recalcTableRow({{ $p->id }})" class="text-[10px] font-black bg-white border border-rose-200 text-rose-800 rounded px-1 py-1 outline-none cursor-pointer" title="RTO फिक्स ₹ में या खरीद लागत के % में">
                                                    <option value="fixed" {{ ($ue['rto_mode'] ?? 'fixed') === 'fixed' ? 'selected' : '' }}>₹</option>
                                                    <option value="pct" {{ ($ue['rto_mode'] ?? 'fixed') === 'pct' ? 'selected' : '' }}>%</option>
                                                </select>
                                            </div>
                                            <span id="row-rto-amt-{{ $p->id }}" class="text-[8px] text-rose-700 font-bold block font-mono mt-0.5 whitespace-nowrap">
                                                ₹{{ (float)($ue['rto_buffer'] ?? 20) }} बफ़र
                                            </span>
                                        </td>

                                        <!-- 2. Packaging (₹) -->
                                        <td class="p-2 col-expense-detail">
                                            <input type="number" id="row-pack-{{ $p->id }}" value="{{ $packCost }}" oninput="recalcTableRow({{ $p->id }})" class="w-16 px-1.5 py-1 bg-white border border-gray-200 rounded-lg text-xs font-mono text-center text-gray-800 focus:border-indigo-500 focus:outline-none">
                                            <div class="flex items-center gap-0.5 mt-0.5 justify-center">
                                                <button type="button" onclick="setRowPack({{ $p->id }}, 5)" class="px-1 py-0.2 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 text-[8px] font-bold transition cursor-pointer" title="₹5 पैकिंग">₹5</button>
                                                <button type="button" onclick="setRowPack({{ $p->id }}, 10)" class="px-1 py-0.2 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 text-[8px] font-bold transition cursor-pointer" title="₹10 पैकिंग">₹10</button>
                                                <button type="button" onclick="setRowPack({{ $p->id }}, 15)" class="px-1 py-0.2 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 text-[8px] font-bold transition cursor-pointer" title="₹15 पैकिंग">₹15</button>
                                            </div>
                                        </td>

                                        <!-- 3. Courier / Shipping (₹) + Weight (kg) -->
                                        <td class="p-2 bg-sky-50/30 col-expense-detail">
                                            <div class="flex items-center gap-1 mb-1 justify-center">
                                                <span class="text-[9px] text-slate-500 font-bold" title="प्रोडक्ट का शुद्ध वजन (kg)">⚖️</span>
                                                <input type="number" step="0.05" min="0.01" max="50" id="row-weight-{{ $p->id }}" value="{{ (float)($ue['weight_kg'] ?? 0.35) }}" oninput="onProductWeightChange({{ $p->id }})" class="w-14 px-1 py-0.5 bg-white border border-sky-300 rounded text-center text-[10px] font-mono font-black text-sky-950 focus:border-sky-500 focus:outline-none" title="वजन डालें (उदा. 0.35kg, 0.5kg, 1.2kg) - सभी प्लेटफ़ॉर्म्स के कूरियर रेट्स अपने-आप बदलेंगे">
                                                <span class="text-[9px] text-sky-800 font-bold">kg</span>
                                            </div>
                                            <div class="relative">
                                                <span class="absolute left-1.5 top-1/2 -translate-y-1/2 text-sky-400 text-[10px]">₹</span>
                                                <input type="number" id="row-ship-{{ $p->id }}" value="{{ $shipCost }}" oninput="recalcTableRow({{ $p->id }})" class="w-16 pl-4 pr-1 py-1 bg-white border border-sky-300 rounded-lg text-xs font-mono font-bold text-sky-950 focus:border-sky-500 focus:outline-none" title="कूरियर दर">
                                            </div>
                                            <div class="flex items-center gap-0.5 mt-0.5 justify-center flex-wrap">
                                                <button type="button" onclick="setRowCourier({{ $p->id }}, 55)" class="px-1 py-0.2 rounded bg-sky-100 hover:bg-sky-200 text-sky-900 border border-sky-200 text-[8px] font-bold transition cursor-pointer" title="0.5kg स्लैब: ₹55 + 18% GST">0.5k</button>
                                                <button type="button" onclick="setRowCourier({{ $p->id }}, 75)" class="px-1 py-0.2 rounded bg-sky-100 hover:bg-sky-200 text-sky-900 border border-sky-200 text-[8px] font-bold transition cursor-pointer" title="1.0kg स्लैब: ₹75 + 18% GST">1k</button>
                                                <button type="button" onclick="setRowCourier({{ $p->id }}, 112)" class="px-1 py-0.2 rounded bg-sky-100 hover:bg-sky-200 text-sky-900 border border-sky-200 text-[8px] font-bold transition cursor-pointer" title="2.0kg स्लैब: ₹112 + 18% GST">2k</button>
                                            </div>
                                            <span id="row-ship-amt-{{ $p->id }}" class="text-[8px] text-sky-800 font-bold block font-mono mt-0.5 whitespace-nowrap">
                                                ₹{{ $shipCost }} + 18% = ₹{{ number_format($shipCost * 1.18, 1) }}
                                            </span>
                                        </td>

                                        <!-- 4. Marketplace Platform Fee (%) -->
                                        <td class="p-2 bg-purple-50/20 col-expense-detail">
                                            <div class="flex items-center gap-1">
                                                <div class="relative">
                                                    <input type="number" id="row-fee-{{ $p->id }}" value="{{ $feePct }}" oninput="recalcTableRow({{ $p->id }})" class="w-14 pl-1.5 pr-3 py-1 bg-white border border-purple-200 rounded-lg text-xs font-mono font-bold text-purple-900 focus:border-purple-500 focus:outline-none">
                                                    <span class="absolute right-1 top-1/2 -translate-y-1/2 text-purple-400 text-[10px]">%</span>
                                                </div>
                                                <label class="inline-flex items-center gap-0.5 text-[8px] text-purple-900 font-bold cursor-pointer" title="Amazon 18% GST फ़ीस में शामिल करें">
                                                    <input type="checkbox" id="row-fee-gst-{{ $p->id }}" onchange="recalcTableRow({{ $p->id }})" {{ (!isset($ue['fee_gst_included']) || $ue['fee_gst_included']) ? 'checked' : '' }} class="rounded text-purple-600 focus:ring-0 w-2.5 h-2.5">
                                                    <span>+GST</span>
                                                </label>
                                            </div>
                                            <!-- 🟢 SP-API Asli / Estimate Badge & 1-Click Sync Button -->
                                            <div class="flex items-center gap-1 mt-0.5">
                                                <span id="row-fee-badge-{{ $p->id }}" class="px-1 py-0.2 rounded text-[7.5px] font-black {{ (!empty($ue['fee_estimate']['is_actual'])) ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-900 border border-amber-300' }}" title="{{ (!empty($ue['fee_estimate']['is_actual'])) ? 'Amazon SP-API से वेरिफाइड असली फ़ीस' : 'Amazon 2026 Category Schedule पर आधारित अनुमानित फ़ीस' }}">
                                                    {{ (!empty($ue['fee_estimate']['is_actual'])) ? '🟢 SP-API असली' : '🟡 अनुमानित' }}
                                                </span>
                                                <button type="button" onclick="syncSingleProductFee({{ $p->id }})" id="btn-sync-fee-{{ $p->id }}" class="px-1 py-0.2 rounded bg-purple-100 hover:bg-purple-200 text-purple-900 text-[8px] font-bold border border-purple-200 cursor-pointer transition shadow-2xs hover:scale-105 active:scale-95" title="इस प्रोडक्ट की Amazon SP-API से असली फ़ीस फेच करें व सटीक सेलिंग भाव सॉल्व करें">
                                                    <i class="fa-solid fa-arrows-rotate text-[8px]"></i>
                                                </button>
                                            </div>
                                            <span id="row-fee-amt-{{ $p->id }}" class="text-[8px] text-purple-700 font-bold block font-mono mt-0.5 whitespace-nowrap">
                                                कमीशन हिसाब...
                                            </span>
                                            <span id="row-fee-breakdown-{{ $p->id }}" class="text-[7.5px] text-gray-500 block font-mono">Ref + Closing + Ship</span>
                                        </td>

                                        <!-- 5. Ads & Marketing (₹) -->
                                        <td class="p-2 col-expense-detail">
                                            <input type="number" id="row-ads-{{ $p->id }}" value="{{ $adsCost }}" oninput="recalcTableRow({{ $p->id }})" class="w-16 px-1.5 py-1 bg-white border border-gray-200 rounded-lg text-xs font-mono text-center text-gray-800 focus:border-indigo-500 focus:outline-none">
                                            <div class="flex items-center gap-0.5 mt-0.5 justify-center">
                                                <button type="button" onclick="setRowAds({{ $p->id }}, 0)" class="px-1 py-0.2 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 text-[8px] font-bold transition cursor-pointer" title="₹0 ऐड्स">₹0</button>
                                                <button type="button" onclick="setRowAds({{ $p->id }}, 5)" class="px-1 py-0.2 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 text-[8px] font-bold transition cursor-pointer" title="₹5 ऐड्स">₹5</button>
                                                <button type="button" onclick="setRowAds({{ $p->id }}, 10)" class="px-1 py-0.2 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 text-[8px] font-bold transition cursor-pointer" title="₹10 ऐड्स">₹10</button>
                                            </div>
                                        </td>

                                        <!-- HSN Code (Towards end before Selling Price) -->
                                        <td class="p-2 bg-purple-50/30 col-expense-detail">
                                            <input type="text" id="row-hsn-{{ $p->id }}" value="{{ $p->hsn_code ?: '6204' }}" class="w-16 px-1.5 py-1 bg-white border border-purple-200 rounded-lg text-xs font-mono font-bold text-gray-800 focus:border-purple-500 focus:outline-none" placeholder="HSN">
                                        </td>

                                        <!-- GST % (Towards end before Selling Price) -->
                                        <td class="p-2 bg-purple-50/30 col-expense-detail">
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
                                                        <div id="row-mrp-alert-{{ $p->id }}" class="mt-1"></div>
                                                        <label class="inline-flex items-center gap-1 text-[8px] text-rose-800 font-black cursor-pointer bg-rose-50 hover:bg-rose-100 px-1.5 py-0.5 rounded border border-rose-200 mt-1 transition shadow-2xs" title="यदि 1-पीस का भाव MRP से अधिक जा रहा हो, तो 1-पीस को अमेज़न पर बेचने से रोकें और केवल 2P, 4P, 6P कॉम्बो ही लिस्ट करें">
                                                            <input type="checkbox" id="row-skip-single-{{ $p->id }}" onchange="onToggleSkipSingle({{ $p->id }})" {{ (!empty($ue['skip_single'])) ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-0 w-2.5 h-2.5">
                                                            <span>🛑 1P स्किप (केवल कॉम्बो)</span>
                                                        </label>
                                                    </div>
                                                    <button type="button" onclick="saveTableRowCosting({{ $p->id }}, this)" class="h-7 w-7 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center transition shadow-2xs cursor-pointer" title="इस रो का रेट सेव करें">
                                                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                                                    </button>
                                                </div>
                                                <!-- Old vs New Solved Price Diff Badge -->
                                                <div id="row-price-diff-badge-{{ $p->id }}" class="hidden text-[8px] font-black px-1.5 py-0.5 rounded shadow-2xs"></div>
                                                <div class="flex items-center gap-1 text-[9px] font-mono">
                                                    <button type="button" onclick="applyChannelPriceToRow({{ $p->id }}, 'amazon', {{ $calcAmz }})" class="px-1.5 py-0.5 rounded bg-amber-100 hover:bg-amber-200 text-amber-950 border border-amber-300 font-extrabold whitespace-nowrap cursor-pointer transition shadow-2xs hover:scale-105 active:scale-95" title="क्लिक करें: Final Selling Price में ₹{{ $calcAmz }} (Amazon 12% फ़ी सहित भाव) भरें">
                                                        🟠 Amz: ₹<span id="row-amz-price-{{ $p->id }}">{{ $calcAmz }}</span>
                                                    </button>
                                                    <button type="button" onclick="applyChannelPriceToRow({{ $p->id }}, 'meesho', {{ $calcMsh }})" class="px-1.5 py-0.5 rounded bg-pink-100 hover:bg-pink-200 text-pink-950 border border-pink-300 font-extrabold whitespace-nowrap cursor-pointer transition shadow-2xs hover:scale-105 active:scale-95" title="क्लिक करें: Final Selling Price में ₹{{ $calcMsh }} (Meesho 0% फ़ी, RTO सुरक्षित भाव) भरें">
                                                        🌸 Msh: ₹<span id="row-msh-price-{{ $p->id }}">{{ $calcMsh }}</span>
                                                    </button>
                                                    <!-- 🚀 Apply on Amazon SP-API Button -->
                                                    <button type="button" onclick="applyPriceToAmazonSpApi({{ $p->id }})" id="btn-apply-amz-{{ $p->id }}" class="px-1.5 py-0.5 rounded bg-amber-500 hover:bg-amber-600 text-slate-950 border border-amber-400 font-black whitespace-nowrap cursor-pointer transition shadow-2xs hover:scale-105 active:scale-95" title="इस सॉल्व किए गए भाव को Amazon SP-API पर लाइव भेजें">
                                                        <i class="fa-brands fa-amazon mr-0.5"></i>Apply
                                                    </button>
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
                                            <div class="flex items-center justify-center gap-1 mt-1 text-[8px] font-mono">
                                                <button type="button" onclick="applyChannelComboToRow({{ $p->id }}, 'amazon', {{ $combo2 }}, {{ $combo4 }}, {{ $combo6 }})" class="px-1 py-0.5 rounded bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 cursor-pointer transition hover:scale-105 active:scale-95" title="क्लिक करें: ऊपर के 2P/4P/6P बॉक्स में Amazon कॉम्बो रेट्स भरें">
                                                    🟠 Amz: <span id="row-amz-combo2-{{ $p->id }}">{{ $combo2 }}</span>/<span id="row-amz-combo4-{{ $p->id }}">{{ $combo4 }}</span>/<span id="row-amz-combo6-{{ $p->id }}">{{ $combo6 }}</span>
                                                </button>
                                                <button type="button" onclick="applyChannelComboToRow({{ $p->id }}, 'meesho', {{ $mshCombo2 }}, {{ $mshCombo4 }}, {{ $mshCombo6 }})" class="px-1 py-0.5 rounded bg-pink-50 hover:bg-pink-100 text-pink-900 border border-pink-300 cursor-pointer transition hover:scale-105 active:scale-95" title="क्लिक करें: ऊपर के 2P/4P/6P बॉक्स में Meesho कॉम्बो रेट्स भरें">
                                                    🌸 Msh: <span id="row-msh-combo2-{{ $p->id }}">{{ $mshCombo2 }}</span>/<span id="row-msh-combo4-{{ $p->id }}">{{ $mshCombo4 }}</span>/<span id="row-msh-combo6-{{ $p->id }}">{{ $mshCombo6 }}</span>
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
                                                <button type="button" onclick="openListingContentModal({{ $p->id }}, '{{ addslashes($p->name) }}')" class="p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 transition cursor-pointer" title="🤖 AI असिस्टेंट: टाइटल, 5 बुलेट पॉइंट्स, विवरण व 248 बाइट्स कीवर्ड्स 1-क्लिक में खुद लिखवाएं">
                                                    <i class="fa-solid fa-wand-magic-sparkles text-xs text-amber-600"></i>
                                                </button>
                                                <button type="button" onclick="openCostCalculatorModal({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->price }}, {{ $pCost }})" class="p-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition cursor-pointer" title="1P लागत, खर्चे, कूरियर व मुनाफा विवरण (पॉपअप)">
                                                    <i class="fa-solid fa-calculator text-xs"></i>
                                                </button>
                                                <button type="button" onclick="openComboCalculatorModal({{ $p->id }}, '{{ addslashes($p->name) }}')" class="p-1.5 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 transition cursor-pointer" title="2P, 4P, 6P कॉम्बो खर्च व सेलिंग विवरण (पॉपअप)">
                                                    <i class="fa-solid fa-layer-group text-xs"></i>
                                                </button>
                                                <button type="button" onclick="unstageProductItem({{ $p->id }}, '{{ addslashes($p->name) }}')" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition cursor-pointer" title="मार्केटप्लेस बास्केट से हटाएं (दुकान में सुरक्षित रहेगा)">
                                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- ↔️ Screen Column Shift Arrows (Left & Right Screen Scroll) Under Every Product -->
                                    <tr class="bg-gradient-to-r from-indigo-50/40 via-purple-50/20 to-amber-50/40 border-b-2 border-indigo-200/80 text-[11px]" id="table-subnav-{{ $p->id }}">
                                        <td colspan="21" class="py-1.5 px-3">
                                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                                <!-- ◀ LEFT ARROW: Shifts Screen Horizontally to Left Columns -->
                                                <div class="flex items-center gap-1 shrink-0">
                                                    <button type="button" onclick="scrollTableToStart()" class="px-2.5 py-1.5 rounded-xl bg-white hover:bg-indigo-100 text-indigo-900 border border-indigo-200 font-bold transition flex items-center gap-1 shadow-2xs active:scale-95 cursor-pointer" title="स्क्रीन को बिल्कुल बाएँ सरकाएं (फ़ोटो, नाम, स्टॉक, खरीद)">
                                                        <i class="fa-solid fa-angles-left text-indigo-600 text-[10px]"></i>
                                                        <span class="text-[10px]">शुरू</span>
                                                    </button>
                                                    <button type="button" onclick="scrollTableHorizontally(-450)" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black transition flex items-center gap-1.5 shadow-sm active:scale-95 cursor-pointer" title="स्क्रीन को बाएँ सरकाएं (फ़ोटो, नाम, SKU, स्टॉक, खरीद लागत)">
                                                        <i class="fa-solid fa-arrow-left text-amber-300"></i>
                                                        <span>◀ बाएँ सरकाएं (नाम, स्टॉक, खरीद)</span>
                                                    </button>
                                                </div>

                                                <!-- Middle Product Badge & Quick Actions -->
                                                <div class="flex items-center gap-1.5 flex-wrap justify-center">
                                                    <span class="font-extrabold text-slate-800 bg-white px-2.5 py-1 rounded-xl border border-slate-200 shadow-2xs">
                                                        <span class="text-indigo-600 font-mono">#{{ $p->id }}</span> • <span class="max-w-[140px] truncate inline-block align-bottom">{{ $p->name }}</span>
                                                    </span>
                                                    <button type="button" onclick="openListingContentModal({{ $p->id }}, '{{ addslashes($p->name) }}')" class="p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 transition cursor-pointer" title="🤖 AI असिस्टेंट: टाइटल, 5 बुलेट पॉइंट्स, विवरण व 248 बाइट्स कीवर्ड्स 1-क्लिक में खुद लिखवाएं">
                                                    <i class="fa-solid fa-wand-magic-sparkles text-xs text-amber-600"></i>
                                                </button>
                                                <button type="button" onclick="openCostCalculatorModal({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->price }}, {{ $pCost }})" class="px-2.5 py-1 rounded-xl bg-indigo-100 hover:bg-indigo-200 text-indigo-900 font-black cursor-pointer transition shadow-2xs flex items-center gap-1">
                                                        <i class="fa-solid fa-calculator text-indigo-600"></i>
                                                        <span>1P पूरा खर्च विवरण</span>
                                                    </button>
                                                    <button type="button" onclick="openComboCalculatorModal({{ $p->id }}, '{{ addslashes($p->name) }}')" class="px-2.5 py-1 rounded-xl bg-purple-100 hover:bg-purple-200 text-purple-900 font-black cursor-pointer transition shadow-2xs flex items-center gap-1">
                                                        <i class="fa-solid fa-layer-group text-purple-600"></i>
                                                        <span>2P/4P/6P कॉम्बो</span>
                                                    </button>
                                                    <button type="button" onclick="saveTableRowCosting({{ $p->id }}, this)" class="px-3 py-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black cursor-pointer transition shadow-2xs active:scale-95 flex items-center gap-1">
                                                        <i class="fa-solid fa-floppy-disk"></i>
                                                        <span>सेव</span>
                                                    </button>
                                                </div>

                                                <!-- ▶ RIGHT ARROW: Shifts Screen Horizontally to Right Columns -->
                                                <div class="flex items-center gap-1 shrink-0">
                                                    <button type="button" onclick="scrollTableHorizontally(450)" class="px-4 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black transition flex items-center gap-1.5 shadow-sm active:scale-95 cursor-pointer" title="स्क्रीन को दाएँ सरकाएं (कूरियर, कमीशन, फाइनल सेलिंग रेट, 2P/4P/6P कॉम्बो)">
                                                        <span>दाएँ सरकाएं (सेलिंग रेट, कॉम्बो) ▶</span>
                                                        <i class="fa-solid fa-arrow-right text-slate-950"></i>
                                                    </button>
                                                    <button type="button" onclick="scrollTableToEnd()" class="px-2.5 py-1.5 rounded-xl bg-white hover:bg-amber-100 text-amber-950 border border-amber-300 font-bold transition flex items-center gap-1 shadow-2xs active:scale-95 cursor-pointer" title="स्क्रीन को बिल्कुल दाएँ अंत तक सरकाएं (कॉम्बो व एक्शन)">
                                                        <span class="text-[10px]">अंत</span>
                                                        <i class="fa-solid fa-angles-right text-amber-700 text-[10px]"></i>
                                                    </button>
                                                </div>
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
        
        // Open Marketplace Seller Portals in Chrome tabs
        function openMarketplaceSellerPortals() {
            window.open('https://sellercentral.amazon.in/inventory', '_blank');
            window.open('https://supplier.meesho.com/panel/v3/new/catalogues', '_blank');
        }

        // Apply Channel Price (Amazon / Meesho) to Table Row
        function applyChannelPriceToRow(prodId, channel, price) {
            const inp = document.getElementById(`row-price-${prodId}`);
            if (inp) {
                inp.value = Math.round(price);
                syncManualPriceToCard(prodId);
                const name = channel === 'amazon' ? 'Amazon' : 'Meesho';
                showChannelToast(`✓ #${prodId} पर ${name} भाव (₹${Math.round(price)}) लोड हो गया! '💾 सेव' दबाकर सुरक्षित करें।`);
            }
        }

        // Apply Channel Combo Rates to Table Row (2P, 4P, 6P)
        function applyChannelComboToRow(prodId, channel, c2, c4, c6) {
            const c2Inp = document.getElementById(`row-combo2-${prodId}`);
            const c4Inp = document.getElementById(`row-combo4-${prodId}`);
            const c6Inp = document.getElementById(`row-combo6-${prodId}`);
            if (c2Inp) { c2Inp.value = Math.round(c2); syncComboToCard(prodId, 2, Math.round(c2)); }
            if (c4Inp) { c4Inp.value = Math.round(c4); syncComboToCard(prodId, 4, Math.round(c4)); }
            if (c6Inp) { c6Inp.value = Math.round(c6); syncComboToCard(prodId, 6, Math.round(c6)); }
            const name = channel === 'amazon' ? 'Amazon' : 'Meesho';
            showChannelToast(`✓ #${prodId} पर ${name} के 2P (₹${Math.round(c2)}), 4P (₹${Math.round(c4)}), 6P (₹${Math.round(c6)}) कॉम्बो रेट्स लोड हो गए!`);
        }

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
            // Costing & Profit Live Calculator Engine (100% Transparent Ledger)
        let activeCalcProductId = null;

        function closeCostCalculatorModal() {
            const modal = document.getElementById('costCalculatorModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function onModalPlatformSelect(platform) {
            if (platform === 'meesho') {
                document.getElementById('calcPlatformFeePercent').value = 0;
                document.getElementById('calcShippingCost').value = 38;
                document.getElementById('calcPackingCost').value = 12;
            } else if (platform === 'flipkart') {
                document.getElementById('calcPlatformFeePercent').value = 10;
                document.getElementById('calcShippingCost').value = 48;
                document.getElementById('calcPackingCost').value = 15;
            } else if (platform === 'direct') {
                document.getElementById('calcPlatformFeePercent').value = 0;
                document.getElementById('calcShippingCost').value = 45;
                document.getElementById('calcPackingCost').value = 10;
            } else { // amazon
                document.getElementById('calcPlatformFeePercent').value = 12;
                document.getElementById('calcShippingCost').value = 55;
                document.getElementById('calcPackingCost').value = 15;
            }
            calculateLiveSellingPrice();
        }

        function setModalShipSlab(rate) {
            document.getElementById('calcShippingCost').value = rate;
            calculateLiveSellingPrice();
        }

        function calculateLiveSellingPrice() {
            const cost = parseFloat(document.getElementById('calcCostPrice')?.value) || 0;
            const marginPct = parseFloat(document.getElementById('calcMarginPercent')?.value) || 25;
            const mrp = parseFloat(document.getElementById('calcModalMrp')?.value) || 0;
            const packing = parseFloat(document.getElementById('calcPackingCost')?.value) || 15;
            const ship = parseFloat(document.getElementById('calcShippingCost')?.value) || 55;
            const rto = parseFloat(document.getElementById('calcModalRto')?.value) || 20;
            const feePct = parseFloat(document.getElementById('calcPlatformFeePercent')?.value) || 12;
            const prodGstRate = parseFloat(document.getElementById('calcProductGst')?.value) || 18;

            const platform = document.getElementById('calcModalPlatformSelect')?.value || 'amazon';
            const feeGstApplies = (platform === 'amazon' || platform === 'flipkart');
            const feeEffectivePct = feeGstApplies ? (feePct * 1.18) : feePct;

            // Courier 18% GST (SAC 9968 is 18% GST in India)
            const shipWithGst = ship > 0 ? (ship * 1.18) : 0;
            const shipGstBadge = document.getElementById('calcShipGstBadge');
            if (shipGstBadge) shipGstBadge.textContent = `₹${ship} + 18% = ₹${shipWithGst.toFixed(1)}`;

            const desiredProfit = Math.round(cost * (marginPct / 100));
            const marginProfitSpan = document.getElementById('calcMarginProfitSpan');
            if (marginProfitSpan) marginProfitSpan.textContent = `₹${desiredProfit} लाभ`;

            const g = prodGstRate / 100;
            const f = feeEffectivePct / 100;
            const cTax = g > 0 ? (cost / (1 + g)) : cost;
            const denom = Math.max(0.05, (1 / (1 + g)) - f);

            const adsCost = 10;
            const otherCost = 5;
            const fixedCostSum = packing + shipWithGst + rto + adsCost + otherCost;
            const finalRate = Math.ceil((cTax + fixedCostSum + desiredProfit) / denom);

            // Update Selling Price & Per Pc Displays
            const elFinalRate = document.getElementById('calcFinalSellingRate');
            if (elFinalRate) elFinalRate.textContent = '₹' + finalRate.toFixed(2);

            const elPerPc = document.getElementById('calcPerPcSellingRate');
            if (elPerPc) elPerPc.textContent = `₹${finalRate}/pc`;

            const elNetProfit = document.getElementById('calcNetProfitBadge');
            if (elNetProfit) elNetProfit.textContent = `शुद्ध मुनाफा: ₹${desiredProfit} (${marginPct}%)`;

            // Update MRP Gap Box
            const mrpText = document.getElementById('calcModalMrpText');
            const mrpDiffBadge = document.getElementById('calcModalMrpDiffBadge');
            if (mrpText) mrpText.textContent = mrp > 0 ? `डिब्बा MRP: ₹${mrp.toFixed(2)}` : 'डिब्बा MRP: (तय नहीं)';

            if (mrpDiffBadge) {
                if (mrp > 0 && finalRate > mrp) {
                    const diffOver = Math.round(finalRate - mrp);
                    mrpDiffBadge.innerHTML = `<span class="px-2 py-0.5 rounded-lg bg-rose-500 text-white font-black text-[11px] animate-pulse">🚨 MRP से ₹${diffOver} ऊपर! (1P छोड़ें, कॉम्बो बेचें)</span>`;
                } else if (mrp > 0) {
                    const diffUnder = Math.round(mrp - finalRate);
                    const discPct = Math.round((diffUnder / mrp) * 100);
                    mrpDiffBadge.innerHTML = `<span class="px-2 py-0.5 rounded-lg bg-emerald-500 text-white font-black text-[11px]">🟢 MRP से ₹${diffUnder} सस्ता (${discPct}% छूट - तुरंत बिकेगा)</span>`;
                } else {
                    mrpDiffBadge.innerHTML = `<span class="text-slate-400 text-[11px] font-medium">ℹ️ MRP दर्ज नहीं किया गया</span>`;
                }
            }

            // Ledger Accounting
            const baseFeeVal = finalRate * (feePct / 100);
            const feeGstVal = feeGstApplies ? (baseFeeVal * 0.18) : 0;
            const totalFeeWithGst = baseFeeVal + feeGstVal;

            const outputGst = (finalRate * (prodGstRate / (100 + prodGstRate)));
            const courierGstItc = (ship * 0.18);
            const commGstItc = (baseFeeVal * 0.18);
            const purchaseGstItc = (cost * (prodGstRate / (100 + prodGstRate)));
            const totalItc = courierGstItc + commGstItc + purchaseGstItc;
            const netCashGst = Math.max(0, outputGst - totalItc);

            if (document.getElementById('ledger_cost')) document.getElementById('ledger_cost').textContent = `₹${cost.toFixed(2)}`;
            if (document.getElementById('ledger_profit')) document.getElementById('ledger_profit').textContent = `+₹${desiredProfit.toFixed(2)} (${marginPct}%)`;
            if (document.getElementById('ledger_pack')) document.getElementById('ledger_pack').textContent = `+₹${packing.toFixed(2)}`;
            if (document.getElementById('ledger_ship')) document.getElementById('ledger_ship').textContent = `+₹${shipWithGst.toFixed(2)} (मूल ₹${ship} + 18% GST)`;
            if (document.getElementById('ledger_rto')) document.getElementById('ledger_rto').textContent = `+₹${rto.toFixed(2)}`;
            const platformLabel = (platform === 'meesho') ? 'Meesho' : ((platform === 'flipkart') ? 'Flipkart' : ((platform === 'direct') ? 'Direct Store' : 'Amazon'));
            if (document.getElementById('ledger_fee')) document.getElementById('ledger_fee').textContent = `+₹${totalFeeWithGst.toFixed(2)} (${feePct}% फ़ी + GST)`;
            
            // Detailed GST & ITC Population
            if (document.getElementById('ledger_platform_name')) document.getElementById('ledger_platform_name').textContent = platformLabel;
            if (document.getElementById('ledger_gst_rate_span')) document.getElementById('ledger_gst_rate_span').textContent = `${prodGstRate}%`;
            if (document.getElementById('ledger_output_gst')) document.getElementById('ledger_output_gst').textContent = `+₹${outputGst.toFixed(2)}`;
            if (document.getElementById('ledger_purchase_itc')) document.getElementById('ledger_purchase_itc').textContent = `-₹${purchaseGstItc.toFixed(2)}`;
            if (document.getElementById('ledger_courier_itc')) document.getElementById('ledger_courier_itc').textContent = `-₹${courierGstItc.toFixed(2)}`;
            if (document.getElementById('ledger_comm_itc')) {
                document.getElementById('ledger_comm_itc').textContent = (commGstItc > 0) ? `-₹${commGstItc.toFixed(2)}` : '₹0.00 (0% फ़ी)';
            }
            if (document.getElementById('ledger_gst_net_badge')) document.getElementById('ledger_gst_net_badge').textContent = `शुद्ध देय: ₹${netCashGst.toFixed(2)}`;
            if (document.getElementById('ledger_gst')) document.getElementById('ledger_gst').textContent = `+₹${netCashGst.toFixed(2)} (शुद्ध नकद देय)`;
            if (document.getElementById('ledger_total')) document.getElementById('ledger_total').textContent = `₹${finalRate.toFixed(2)}`;

            // Combo Previews
            const c2 = Math.ceil(((cTax * 2) + (desiredProfit * 1.8) + (rto * 1.5) + (packing * 1.3) + shipWithGst + (adsCost * 1.2)) / denom);
            const c4 = Math.ceil(((cTax * 4) + (desiredProfit * 3.5) + (rto * 2.0) + (packing * 1.6) + (shipWithGst * 1.3) + (adsCost * 1.5)) / denom);
            const c6 = Math.ceil(((cTax * 6) + (desiredProfit * 5.0) + (rto * 2.5) + (packing * 2.0) + (shipWithGst * 1.6) + (adsCost * 1.8)) / denom);

            if (document.getElementById('calcCombo2Rate')) document.getElementById('calcCombo2Rate').textContent = `₹${c2} (₹${Math.round(c2/2)}/pc)`;
            if (document.getElementById('calcCombo4Rate')) document.getElementById('calcCombo4Rate').textContent = `₹${c4} (₹${Math.round(c4/4)}/pc)`;
            if (document.getElementById('calcCombo6Rate')) document.getElementById('calcCombo6Rate').textContent = `₹${c6} (₹${Math.round(c6/6)}/pc)`;
        }

        // Updated openCostCalculatorModal with full Product and Navigation support
        function openCostCalculatorModal(prodId, prodName = null, basePrice = null, purchasePrice = null) {
            activeCalcProductId = prodId;

            // Populate Product Information
            const nameEl = document.querySelector(`#table-row-${prodId} td:nth-child(3) span`) || document.getElementById(`card-name-${prodId}`);
            const finalName = prodName || (nameEl ? nameEl.textContent.trim() : `Product #${prodId}`);
            const nameSpan = document.getElementById('calcModalProdName');
            if (nameSpan) nameSpan.textContent = finalName;

            const badgeSpan = document.getElementById('calcModalProdIdBadge');
            if (badgeSpan) badgeSpan.textContent = `#${prodId}`;

            const skuSpan = document.querySelector(`#table-row-${prodId} td:nth-child(3) div span`);
            const modalSku = document.getElementById('calcModalProdSku');
            if (modalSku) modalSku.textContent = skuSpan ? skuSpan.textContent.trim() : 'SKU: STD';

            const rowImg = document.getElementById(`table-prod-img-${prodId}`) || document.getElementById(`prod_img_${prodId}`);
            const modalImg = document.getElementById('calcModalProdImg');
            const modalPh = document.getElementById('calcModalProdImgPh');
            if (rowImg && rowImg.src && !rowImg.classList.contains('hidden')) {
                if (modalImg) { modalImg.src = rowImg.src; modalImg.classList.remove('hidden'); }
                if (modalPh) modalPh.classList.add('hidden');
            } else {
                if (modalImg) modalImg.classList.add('hidden');
                if (modalPh) modalPh.classList.remove('hidden');
            }

            // Read current values directly from table row inputs
            const rowCost = parseFloat(document.getElementById(`row-cost-${prodId}`)?.value);
            const costVal = (!isNaN(rowCost) && rowCost > 0) ? rowCost : ((purchasePrice && purchasePrice > 0) ? purchasePrice : Math.max(10, Math.round((basePrice || 100) * 0.70)));
            if (document.getElementById('calcCostPrice')) document.getElementById('calcCostPrice').value = costVal;

            const rowMargin = parseFloat(document.getElementById(`row-margin-${prodId}`)?.value);
            if (document.getElementById('calcMarginPercent')) document.getElementById('calcMarginPercent').value = (!isNaN(rowMargin) && rowMargin > 0) ? rowMargin : 25;

            const rowMrp = parseFloat(document.getElementById(`row-mrp-${prodId}`)?.value || document.getElementById(`card-mrp-${prodId}`)?.value);
            if (document.getElementById('calcModalMrp')) document.getElementById('calcModalMrp').value = (!isNaN(rowMrp) && rowMrp > 0) ? rowMrp : '';

            const rowPack = parseFloat(document.getElementById(`row-pack-${prodId}`)?.value);
            if (document.getElementById('calcPackingCost')) document.getElementById('calcPackingCost').value = (!isNaN(rowPack) && rowPack > 0) ? rowPack : 15;

            const rowShip = parseFloat(document.getElementById(`row-ship-${prodId}`)?.value);
            if (document.getElementById('calcShippingCost')) document.getElementById('calcShippingCost').value = (!isNaN(rowShip) && rowShip > 0) ? rowShip : 55;

            const rowRto = parseFloat(document.getElementById(`row-rto-${prodId}`)?.value);
            if (document.getElementById('calcModalRto')) document.getElementById('calcModalRto').value = (!isNaN(rowRto) && rowRto >= 0) ? rowRto : 20;

            const rowFee = parseFloat(document.getElementById(`row-fee-${prodId}`)?.value);
            if (document.getElementById('calcPlatformFeePercent')) document.getElementById('calcPlatformFeePercent').value = (!isNaN(rowFee) && rowFee >= 0) ? rowFee : 12;
            
            // Auto-align platform dropdown with current product row fee
            const platSel = document.getElementById('calcModalPlatformSelect');
            if (platSel) {
                if (rowFee === 0) platSel.value = 'meesho';
                else if (rowFee === 10) platSel.value = 'flipkart';
                else platSel.value = 'amazon';
            }

            const rowGst = document.getElementById(`row-gst-${prodId}`)?.value;
            if (rowGst && document.getElementById('calcProductGst')) {
                document.getElementById('calcProductGst').value = rowGst;
            }

            calculateLiveSellingPrice();

            const modal = document.getElementById('costCalculatorModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        async function saveActiveModalCosting(btn) {
            if (!activeCalcProductId) return;
            const prodId = activeCalcProductId;

            // Sync modal values back to table row
            const costInp = document.getElementById(`row-cost-${prodId}`);
            if (costInp) costInp.value = document.getElementById('calcCostPrice').value;

            const marginInp = document.getElementById(`row-margin-${prodId}`);
            if (marginInp) marginInp.value = document.getElementById('calcMarginPercent').value;

            const mrpInp = document.getElementById(`row-mrp-${prodId}`);
            if (mrpInp) mrpInp.value = document.getElementById('calcModalMrp').value;

            const packInp = document.getElementById(`row-pack-${prodId}`);
            if (packInp) packInp.value = document.getElementById('calcPackingCost').value;

            const shipInp = document.getElementById(`row-ship-${prodId}`);
            if (shipInp) shipInp.value = document.getElementById('calcShippingCost').value;

            const rtoInp = document.getElementById(`row-rto-${prodId}`);
            if (rtoInp) rtoInp.value = document.getElementById('calcModalRto').value;

            const feeInp = document.getElementById(`row-fee-${prodId}`);
            if (feeInp) feeInp.value = document.getElementById('calcPlatformFeePercent').value;

            const gstInp = document.getElementById(`row-gst-${prodId}`);
            if (gstInp) gstInp.value = document.getElementById('calcProductGst').value;

            recalcTableRow(prodId);

            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>सेव हो रहा है...</span>';

            try {
                const rowSaveBtn = document.querySelector(`#table-row-${prodId} button[onclick^="saveTableRowCosting"]`) || btn;
                await saveTableRowCosting(prodId, rowSaveBtn);
                btn.classList.remove('from-emerald-600', 'to-teal-600');
                btn.classList.add('bg-blue-600');
                btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>सुरक्षित सेव हो गया!</span>';
                setTimeout(() => {
                    btn.classList.remove('bg-blue-600');
                    btn.classList.add('from-emerald-600', 'to-teal-600');
                    btn.innerHTML = origHtml;
                    btn.disabled = false;
                }, 2000);
            } catch(e) {
                btn.disabled = false;
                btn.innerHTML = origHtml;
                alert('डेटा सेव करने में त्रुटि आई।');
            }
        }

        function getPendingProductIds() {
            const rows = document.querySelectorAll('#pendingTableTbody tr[data-id]');
            return Array.from(rows).map(r => parseInt(r.getAttribute('data-id')));
        }

        function navCalcModalProduct(direction) {
            const ids = getPendingProductIds();
            if (!ids.length) return;
            const idx = ids.indexOf(activeCalcProductId);
            if (idx === -1) return;
            const newIdx = idx + direction;
            if (newIdx >= 0 && newIdx < ids.length) {
                const newId = ids[newIdx];
                const nameEl = document.querySelector(`#table-row-${newId} td:nth-child(3) span`) || document.getElementById(`card-name-${newId}`);
                const name = nameEl ? nameEl.textContent.trim() : `Product #${newId}`;
                openCostCalculatorModal(newId, name);
            } else {
                showChannelToast(direction < 0 ? 'यह कतार का पहला प्रोडक्ट है!' : 'यह कतार का अंतिम प्रोडक्ट है!');
            }
        }

        function scrollToPrevProductRow(currentId) {
            const ids = getPendingProductIds();
            const idx = ids.indexOf(currentId);
            if (idx > 0) {
                const prevId = ids[idx - 1];
                const prevRow = document.getElementById(`table-row-${prevId}`);
                if (prevRow) {
                    prevRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    prevRow.classList.add('bg-amber-100');
                    setTimeout(() => prevRow.classList.remove('bg-amber-100'), 1500);
                }
            } else {
                showChannelToast('यह कतार का पहला प्रोडक्ट है!');
            }
        }

        function scrollToNextProductRow(currentId) {
            const ids = getPendingProductIds();
            const idx = ids.indexOf(currentId);
            if (idx !== -1 && idx < ids.length - 1) {
                const nextId = ids[idx + 1];
                const nextRow = document.getElementById(`table-row-${nextId}`);
                if (nextRow) {
                    nextRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    nextRow.classList.add('bg-amber-100');
                    setTimeout(() => nextRow.classList.remove('bg-amber-100'), 1500);
                }
            } else {
                showChannelToast('यह कतार का अंतिम प्रोडक्ट है!');
            }
        }

        // --- 📦 COMBO CALCULATOR MODAL LOGIC (2P, 4P, 6P) ---
        let activeComboProductId = null;

        
        // --- 🛡️ RTO DRAIN & RISK BUDGETING SIMULATOR LOGIC ---
        
        // --- 🚚 PLATFORM COURIER RATE MASTER MODAL LOGIC ---
        let currentShipModalTab = 'amazon';

        function openShippingRateManagerModal() {
            renderShippingMasterTable(currentShipModalTab);
            const m = document.getElementById('shippingRateManagerModal');
            if (m) {
                m.classList.remove('hidden');
                m.classList.add('flex');
            }
        }

        function closeShippingRateManagerModal() {
            const m = document.getElementById('shippingRateManagerModal');
            if (m) {
                m.classList.add('hidden');
                m.classList.remove('flex');
            }
        }

        function switchShippingPlatformTab(platform) {
            currentShipModalTab = platform;
            ['amazon', 'meesho', 'flipkart', 'direct'].forEach(p => {
                const btn = document.getElementById(`tabBtnShip_${p}`);
                if (btn) {
                    if (p === platform) {
                        btn.className = "px-3.5 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-1.5 bg-amber-500 text-slate-950 shadow-xs cursor-pointer";
                    } else {
                        btn.className = "px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 bg-gray-100 text-gray-700 hover:text-gray-900 cursor-pointer";
                    }
                }
            });
            renderShippingMasterTable(platform);
        }

        function renderShippingMasterTable(platform) {
            const container = document.getElementById('shippingMasterTablesContainer');
            if (!container) return;

            let rates = PLATFORM_SHIPPING_RATES.filter(r => r.carrier === platform);
            if (!rates.length) {
                // Populate standard fallback slabs if none exists
                const fallbacks = {
                    amazon: [
                        { weight: 0.5, rate: 55, gst_percent: 18, label: '0.5 kg (Amazon Easy Ship ₹55)' },
                        { weight: 1.0, rate: 75, gst_percent: 18, label: '1.0 kg (Amazon Easy Ship ₹75)' },
                        { weight: 2.0, rate: 112, gst_percent: 18, label: '2.0 kg (Amazon Easy Ship ₹112)' },
                        { weight: 3.0, rate: 145, gst_percent: 18, label: '3.0 kg (Amazon Easy Ship ₹145)' }
                    ],
                    meesho: [
                        { weight: 0.5, rate: 38, gst_percent: 18, label: '0.5 kg (Meesho 3PL ₹38)' },
                        { weight: 1.0, rate: 52, gst_percent: 18, label: '1.0 kg (Meesho 3PL ₹52)' },
                        { weight: 2.0, rate: 75, gst_percent: 18, label: '2.0 kg (Meesho 3PL ₹75)' },
                        { weight: 3.0, rate: 95, gst_percent: 18, label: '3.0 kg (Meesho 3PL ₹95)' }
                    ],
                    flipkart: [
                        { weight: 0.5, rate: 48, gst_percent: 18, label: '0.5 kg (Flipkart Ekart ₹48)' },
                        { weight: 1.0, rate: 68, gst_percent: 18, label: '1.0 kg (Flipkart Ekart ₹68)' },
                        { weight: 2.0, rate: 98, gst_percent: 18, label: '2.0 kg (Flipkart Ekart ₹98)' },
                        { weight: 3.0, rate: 125, gst_percent: 18, label: '3.0 kg (Flipkart Ekart ₹125)' }
                    ],
                    direct: [
                        { weight: 0.5, rate: 45, gst_percent: 18, label: '0.5 kg (Self / Shiprocket ₹45)' },
                        { weight: 1.0, rate: 70, gst_percent: 18, label: '1.0 kg (Self / Shiprocket ₹70)' },
                        { weight: 2.0, rate: 105, gst_percent: 18, label: '2.0 kg (Self / Shiprocket ₹105)' },
                        { weight: 3.0, rate: 140, gst_percent: 18, label: '3.0 kg (Self / Shiprocket ₹140)' }
                    ]
                };
                rates = fallbacks[platform] || fallbacks.amazon;
            }

            rates.sort((a, b) => parseFloat(a.weight) - parseFloat(b.weight));

            let html = `
                <div class="overflow-x-auto rounded-2xl border border-gray-200 shadow-2xs">
                    <table class="w-full text-left text-xs divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-[10px] font-black uppercase text-gray-600">
                            <tr>
                                <th class="p-3">वजन स्लैब (Weight kg)</th>
                                <th class="p-3">बेस कूरियर दर (₹ बिना GST)</th>
                                <th class="p-3">GST (%)</th>
                                <th class="p-3">कुल देय दर (With 18% GST)</th>
                                <th class="p-3">विवरण / लेबल</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs font-mono" id="shipModalTbody">
            `;

            rates.forEach((r, idx) => {
                const baseRate = parseFloat(r.rate) || 0;
                const gst = parseFloat(r.gst_percent) || 18;
                const totalWithGst = (baseRate * (1 + (gst / 100))).toFixed(2);
                html += `
                    <tr class="hover:bg-slate-50">
                        <td class="p-3 font-bold text-gray-900">
                            <input type="number" step="0.1" min="0.1" value="${r.weight}" data-field="weight" data-idx="${idx}" class="w-16 px-2 py-1 bg-white border border-gray-300 rounded font-bold text-center"> kg
                        </td>
                        <td class="p-3">
                            <div class="relative w-28">
                                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 font-bold">₹</span>
                                <input type="number" step="1" value="${baseRate}" data-field="rate" data-idx="${idx}" oninput="updateShipModalRowTotal(${idx})" id="modalShipRate_${idx}" class="w-full pl-5 pr-2 py-1 bg-white border border-sky-300 rounded font-black text-sky-950 text-right">
                            </div>
                        </td>
                        <td class="p-3">
                            <input type="number" value="${gst}" data-field="gst_percent" data-idx="${idx}" oninput="updateShipModalRowTotal(${idx})" id="modalShipGst_${idx}" class="w-12 px-1 py-1 bg-white border border-gray-200 rounded font-bold text-center">%
                        </td>
                        <td class="p-3 font-bold text-emerald-700" id="modalShipTotal_${idx}">
                            ₹${totalWithGst}
                        </td>
                        <td class="p-3">
                            <input type="text" value="${r.label || ''}" data-field="label" data-idx="${idx}" placeholder="उदा. 0.5kg Easy Ship" class="w-48 px-2 py-1 bg-white border border-gray-200 rounded text-xs text-gray-800">
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
            `;

            container.innerHTML = html;
        }

        function updateShipModalRowTotal(idx) {
            const rateInp = document.getElementById(`modalShipRate_${idx}`);
            const gstInp = document.getElementById(`modalShipGst_${idx}`);
            const totalSpan = document.getElementById(`modalShipTotal_${idx}`);
            if (rateInp && gstInp && totalSpan) {
                const r = parseFloat(rateInp.value) || 0;
                const g = parseFloat(gstInp.value) || 18;
                totalSpan.textContent = `₹${(r * (1 + (g / 100))).toFixed(2)}`;
            }
        }

        async function savePlatformShippingRatesMaster() {
            const tbody = document.getElementById('shipModalTbody');
            if (!tbody) return;

            const rows = tbody.querySelectorAll('tr');
            const items = [];
            rows.forEach((row, idx) => {
                const w = parseFloat(row.querySelector('input[data-field="weight"]')?.value) || 0.5;
                const r = parseFloat(row.querySelector('input[data-field="rate"]')?.value) || 55;
                const g = parseFloat(row.querySelector('input[data-field="gst_percent"]')?.value) || 18;
                const l = row.querySelector('input[data-field="label"]')?.value || '';
                items.push({
                    carrier: currentShipModalTab,
                    weight: w,
                    rate: r,
                    gst_percent: g,
                    label: l
                });
            });

            const btn = document.getElementById('btnSaveShippingMaster');
            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> सेव हो रहा है...';

            try {
                const res = await fetch('/seller/channels/shipping-rates/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ rates: items })
                });
                const data = await res.json();
                if (data.success) {
                    // Update in-memory rates array
                    items.forEach(newItem => {
                        const existingIdx = PLATFORM_SHIPPING_RATES.findIndex(r => r.carrier === newItem.carrier && parseFloat(r.weight) === newItem.weight);
                        if (existingIdx !== -1) {
                            PLATFORM_SHIPPING_RATES[existingIdx] = newItem;
                        } else {
                            PLATFORM_SHIPPING_RATES.push(newItem);
                        }
                    });

                    // Recalculate all products with newly saved courier rates!
                    document.querySelectorAll('#pendingTableTbody tr[data-id]').forEach(r => {
                        const id = r.getAttribute('data-id');
                        if (id) {
                            onProductWeightChange(id);
                        }
                    });

                    showChannelToast(`✓ ${currentShipModalTab.toUpperCase()} के कूरियर रेट्स डेटाबेस में सेव हो गए और सभी प्रोडक्ट्स पर लागू हो गए!`);
                    closeShippingRateManagerModal();
                } else {
                    alert(data.message || 'कूरियर दरें सेव नहीं हो सकीं!');
                }
            } catch (err) {
                alert('त्रुटि: ' + err.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = origHtml;
            }
        }

        function toggleRtoBudgetSimulator() {
            const panel = document.getElementById('rtoBudgetSimulatorPanel');
            if (!panel) return;
            if (panel.classList.contains('hidden')) {
                panel.classList.remove('hidden');
                runRtoSimulation();
                panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                panel.classList.add('hidden');
            }
        }

        function resetRtoSimulatorDefaults() {
            if (document.getElementById('sim_batch_size')) document.getElementById('sim_batch_size').value = 100;
            if (document.getElementById('sim_target_profit')) document.getElementById('sim_target_profit').value = 25;
            if (document.getElementById('sim_rto_buffer')) document.getElementById('sim_rto_buffer').value = 20;
            if (document.getElementById('sim_ads_budget')) document.getElementById('sim_ads_budget').value = 10;
            if (document.getElementById('sim_ads_actual')) document.getElementById('sim_ads_actual').value = 6;
            if (document.getElementById('sim_pack_budget')) document.getElementById('sim_pack_budget').value = 15;
            if (document.getElementById('sim_pack_actual')) document.getElementById('sim_pack_actual').value = 12;
            if (document.getElementById('sim_fwd_ship')) document.getElementById('sim_fwd_ship').value = 65;
            if (document.getElementById('sim_rev_ship')) document.getElementById('sim_rev_ship').value = 65;
            if (document.getElementById('sim_wasted_pack')) document.getElementById('sim_wasted_pack').value = 15;
            if (document.getElementById('sim_damage_loss')) document.getElementById('sim_damage_loss').value = 0;
            if (document.getElementById('sim_rto_slider')) document.getElementById('sim_rto_slider').value = 15;
            runRtoSimulation();
        }

        function runRtoSimulation() {
            const batch = parseInt(document.getElementById('sim_batch_size')?.value) || 100;
            const targetProfitPerPc = parseFloat(document.getElementById('sim_target_profit')?.value) || 25;
            const rtoBufferPerPc = parseFloat(document.getElementById('sim_rto_buffer')?.value) || 20;

            const adsBudget = parseFloat(document.getElementById('sim_ads_budget')?.value) || 10;
            const adsActual = parseFloat(document.getElementById('sim_ads_actual')?.value) || 6;
            const packBudget = parseFloat(document.getElementById('sim_pack_budget')?.value) || 15;
            const packActual = parseFloat(document.getElementById('sim_pack_actual')?.value) || 12;

            const fwdShip = parseFloat(document.getElementById('sim_fwd_ship')?.value) || 65;
            const revShip = parseFloat(document.getElementById('sim_rev_ship')?.value) || 65;
            const wastedPack = parseFloat(document.getElementById('sim_wasted_pack')?.value) || 15;
            const damageLoss = parseFloat(document.getElementById('sim_damage_loss')?.value) || 0;

            const drainPerRto = fwdShip + revShip + wastedPack + damageLoss;
            const drainEl = document.getElementById('sim_total_drain_per_rto');
            if (drainEl) drainEl.textContent = `₹${drainPerRto.toFixed(2)}`;

            // Surplus Engine
            const adsSurplus = (adsBudget - adsActual) * batch;
            const packSurplus = (packBudget - packActual) * batch;
            const totalSurplus = adsSurplus + packSurplus;

            const surplusBadge = document.getElementById('sim_surplus_badge');
            if (surplusBadge) {
                if (totalSurplus >= 0) {
                    surplusBadge.className = 'font-mono text-emerald-400 font-bold';
                    surplusBadge.textContent = `+₹${totalSurplus.toFixed(0)} बचत`;
                } else {
                    surplusBadge.className = 'font-mono text-rose-400 font-bold';
                    surplusBadge.textContent = `-₹${Math.abs(totalSurplus).toFixed(0)} ओवर-रन!`;
                }
            }

            // Budget Pools
            const rtoBufferPool = rtoBufferPerPc * batch;
            const targetProfitPool = targetProfitPerPc * batch;
            const totalAvailableBuffer = Math.max(0, rtoBufferPool + totalSurplus);
            const totalShield = totalAvailableBuffer + targetProfitPool;

            if (document.getElementById('sim_pool_rto')) document.getElementById('sim_pool_rto').textContent = `₹${rtoBufferPool.toLocaleString('en-IN')}`;
            if (document.getElementById('sim_pool_surplus')) document.getElementById('sim_pool_surplus').textContent = (totalSurplus >= 0 ? '+' : '-') + `₹${Math.abs(totalSurplus).toLocaleString('en-IN')}`;
            if (document.getElementById('sim_pool_profit')) document.getElementById('sim_pool_profit').textContent = `₹${targetProfitPool.toLocaleString('en-IN')}`;
            if (document.getElementById('sim_pool_total')) document.getElementById('sim_pool_total').textContent = `₹${totalShield.toLocaleString('en-IN')}`;

            // Thresholds
            const safeRtoCount = drainPerRto > 0 ? (totalAvailableBuffer / drainPerRto) : 0;
            const safeRtoPct = ((safeRtoCount / batch) * 100);

            const nilProfitRtoCount = drainPerRto > 0 ? (totalShield / drainPerRto) : 0;
            const nilProfitRtoPct = ((nilProfitRtoCount / batch) * 100);

            if (document.getElementById('sim_safe_rto_badge')) {
                document.getElementById('sim_safe_rto_badge').textContent = `${safeRtoPct.toFixed(1)}% (${Math.floor(safeRtoCount)} पार्सल)`;
            }
            if (document.getElementById('sim_nil_profit_badge')) {
                document.getElementById('sim_nil_profit_badge').textContent = `${nilProfitRtoPct.toFixed(1)}% (${Math.floor(nilProfitRtoCount)} पार्सल)`;
            }

            // Slider evaluation
            const sliderVal = parseInt(document.getElementById('sim_rto_slider')?.value) || 0;
            const currentRtoCount = Math.round((sliderVal / 100) * batch);
            if (document.getElementById('sim_slider_val_badge')) {
                document.getElementById('sim_slider_val_badge').textContent = `${sliderVal}% RTO (${currentRtoCount} पार्सल)`;
            }

            const totalRtoDrain = currentRtoCount * drainPerRto;
            const remainingBuffer = totalAvailableBuffer - totalRtoDrain;

            let finalNetProfit = 0;
            const statusCard = document.getElementById('sim_live_status_card');
            if (statusCard) {
                if (remainingBuffer >= 0) {
                    finalNetProfit = targetProfitPool;
                    const bufferLeft = remainingBuffer;
                    statusCard.className = 'p-3.5 rounded-2xl border border-emerald-500/50 bg-emerald-950/40 flex items-center justify-between flex-wrap gap-3';
                    statusCard.innerHTML = `
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-emerald-500 text-slate-950 flex items-center justify-center font-bold text-lg"><i class="fa-solid fa-circle-check"></i></span>
                            <div>
                                <span class="text-sm font-black text-emerald-400 block">🟢 सुरक्षित ज़ोन (100% मुनाफ़ा सुरक्षित!)</span>
                                <span class="text-xs text-slate-300">${currentRtoCount} RTO का नुकसान (₹${totalRtoDrain.toLocaleString('en-IN')}) आपके RTO बफ़र से कट गया। बफ़र में अभी भी ₹${bufferLeft.toLocaleString('en-IN')} सरप्लस शेष है!</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-slate-400 block font-bold">शुद्ध इन-हैंड कमाई:</span>
                            <span class="text-lg font-black font-mono text-emerald-300">₹${(finalNetProfit + bufferLeft).toLocaleString('en-IN')}</span>
                        </div>`;
                } else if (Math.abs(remainingBuffer) < targetProfitPool) {
                    finalNetProfit = targetProfitPool - Math.abs(remainingBuffer);
                    const dipped = Math.abs(remainingBuffer);
                    statusCard.className = 'p-3.5 rounded-2xl border border-amber-500/50 bg-amber-950/40 flex items-center justify-between flex-wrap gap-3';
                    statusCard.innerHTML = `
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center font-bold text-lg"><i class="fa-solid fa-triangle-exclamation"></i></span>
                            <div>
                                <span class="text-sm font-black text-amber-300 block">🟡 चेतावनी: RTO बफ़र समाप्त, मुनाफ़े में से कटाई जारी!</span>
                                <span class="text-xs text-slate-300">RTO नुकसान (₹${totalRtoDrain.toLocaleString('en-IN')}) बफ़र से ₹${dipped.toLocaleString('en-IN')} ज्यादा हो गया, जो आपके ₹${targetProfitPool.toLocaleString('en-IN')} मुनाफ़े से कट रहा है।</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-slate-400 block font-bold">बचा हुआ शुद्ध मुनाफ़ा:</span>
                            <span class="text-lg font-black font-mono text-amber-300">₹${finalNetProfit.toLocaleString('en-IN')}</span>
                            <span class="text-[10px] text-rose-300 block font-bold">(-₹${dipped.toLocaleString('en-IN')} मुनाफे से कटा)</span>
                        </div>`;
                } else {
                    finalNetProfit = targetProfitPool - Math.abs(remainingBuffer);
                    const lossAmount = Math.abs(finalNetProfit);
                    statusCard.className = 'p-3.5 rounded-2xl border border-rose-500 bg-rose-950/70 flex items-center justify-between flex-wrap gap-3';
                    statusCard.innerHTML = `
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center font-bold text-lg animate-pulse"><i class="fa-solid fa-skull-crossbones"></i></span>
                            <div>
                                <span class="text-sm font-black text-rose-400 block">🔴 गंभीर चेतावनी: मुनाफ़ा शून्य (NIL PROFIT) व जेब से घाटा!</span>
                                <span class="text-xs text-rose-200">RTO ${sliderVal}% पर कुल नुकसान ₹${totalRtoDrain.toLocaleString('en-IN')} हुआ जिसने सारा बफ़र व ₹${targetProfitPool.toLocaleString('en-IN')} मुनाफ़ा खत्म कर दिया।</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-rose-300 block font-bold">जेब से शुद्ध घाटा (Net Loss):</span>
                            <span class="text-lg font-black font-mono text-rose-400">-₹${lossAmount.toLocaleString('en-IN')}</span>
                            <span class="text-[10px] text-amber-300 block font-bold">👉 COD वेरिफिकेशन चालू करें या RTO बफ़र बढ़ाएं!</span>
                        </div>`;
                }
            }
        }

        function setModalComboShip(qty, rate) {
            const inp = document.getElementById(`combo${qty}_ship`);
            if (inp) {
                inp.value = rate;
                recalcComboModalLive(qty);
            }
        }

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

            // Smart Weight Slabs Preload:
            // 2P: 0.5kg slab = ₹55 base
            // 4P: 1.0kg slab = ₹75 base
            // 6P: 2.0kg slab = ₹112 base
            document.getElementById('combo2_cost').textContent = 'खरीद: ₹' + (cost * 2).toFixed(2);
            document.getElementById('combo2_margin').value = marginPct;
            document.getElementById('combo2_ship').value = 55;
            document.getElementById('combo2_pack').value = 20;
            document.getElementById('combo2_rto').value = 30;
            document.getElementById('combo2_fee').value = feePct;

            document.getElementById('combo4_cost').textContent = 'खरीद: ₹' + (cost * 4).toFixed(2);
            document.getElementById('combo4_margin').value = marginPct;
            document.getElementById('combo4_ship').value = 75;
            document.getElementById('combo4_pack').value = 25;
            document.getElementById('combo4_rto').value = 40;
            document.getElementById('combo4_fee').value = feePct;

            document.getElementById('combo6_cost').textContent = 'खरीद: ₹' + (cost * 6).toFixed(2);
            document.getElementById('combo6_margin').value = marginPct;
            document.getElementById('combo6_ship').value = 112;
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

            // Courier with 18% GST
            const shipWithGst = ship > 0 ? (ship * 1.18) : 0;
            const shipBadge = document.getElementById(`combo${qty}_ship_badge`);
            if (shipBadge) shipBadge.textContent = `₹${ship} + 18% GST = ₹${shipWithGst.toFixed(1)}`;

            const totalCost = unitCost * qty;
            const g = gstRate / 100;
            const f = (feePct * 1.18) / 100; // Amazon 18% GST on fee
            const cTax = g > 0 ? (totalCost / (1 + g)) : totalCost;
            const denom = Math.max(0.05, (1 / (1 + g)) - f);

            const profitTarget = totalCost * (marginPct / 100);
            const fixedExp = shipWithGst + pack + rto;
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

                // Automatic Weight Slab Rates (with 18% GST):
                // 2P: 0.5kg slab (₹55 + 18% GST = ₹64.90)
                // 4P: 1.0kg slab (₹75 + 18% GST = ₹88.50)
                // 6P: 2.0kg slab (₹112 + 18% GST = ₹132.16)
                const ship2 = 55 * 1.18;
                const ship4 = 75 * 1.18;
                const ship6 = 112 * 1.18;

                const cTax2 = g > 0 ? ((cost * 2) / (1 + g)) : (cost * 2);
                const p2 = (cost * 2) * (marginPct / 100);
                const c2 = Math.ceil((cTax2 + ship2 + 20 + 30 + p2) / denom);

                const cTax4 = g > 0 ? ((cost * 4) / (1 + g)) : (cost * 4);
                const p4 = (cost * 4) * (marginPct / 100);
                const c4 = Math.ceil((cTax4 + ship4 + 25 + 40 + p4) / denom);

                const cTax6 = g > 0 ? ((cost * 6) / (1 + g)) : (cost * 6);
                const p6 = (cost * 6) * (marginPct / 100);
                const c6 = Math.ceil((cTax6 + ship6 + 30 + 50 + p6) / denom);

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

            // Populate live badges (MRP alert, courier with GST, Amazon fee with GST, RTO) for all rows
            document.querySelectorAll('#pendingTableTbody tr[data-id]').forEach(row => {
                const id = row.getAttribute('data-id');
                if (id) recalcTableRow(id);
            });
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
                    const cardCountBadge = document.getElementById(`card-img-count-badge-${prodId}`);
                    if (cardCountBadge) cardCountBadge.textContent = `📸 ${count} फ़ोटो`;

                    // Dynamically refresh gallery strip thumbnails on the card
                    const galleryStrip = document.getElementById(`card-gallery-strip-${prodId}`);
                    if (galleryStrip && data.images && data.images.length > 0) {
                        let thumbsHtml = '';
                        data.images.slice(0, 6).forEach(imgObj => {
                            thumbsHtml += `<div class="h-8 w-8 rounded-lg border border-gray-200 overflow-hidden bg-white p-0.5 shrink-0 shadow-2xs" title="गैलरी फ़ोटो"><img src="${imgObj.url}" class="h-full w-full object-contain"></div>`;
                        });
                        if (data.images.length > 6) {
                            thumbsHtml += `<span class="text-[8px] font-mono font-bold text-gray-500 bg-gray-100 px-1 py-1 rounded-md shrink-0">+${data.images.length - 6}</span>`;
                        }
                        galleryStrip.innerHTML = thumbsHtml;
                    }

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

        // 2-Way Real-time stock sync from Card input to table row
        function syncCardStockToRow(prodId, val) {
            const qty = parseInt(val) || 0;
            const rowInp = document.getElementById(`row-stock-${prodId}`);
            if (rowInp) {
                rowInp.value = qty;
                updateStockStatusBadge(prodId);
            }
            const cardStock = document.getElementById(`card-stock-${prodId}`);
            if (cardStock) cardStock.textContent = `${qty} pcs`;
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
            const cardStockInp = document.getElementById(`card-stock-inp-${prodId}`);
            if (cardStockInp && cardStockInp.value != val) {
                cardStockInp.value = val;
            }
            const cardStock = document.getElementById(`card-stock-${prodId}`);
            if (cardStock) cardStock.textContent = `${val} pcs`;
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
        
        // --- 🚚 DYNAMIC MULTI-PLATFORM SHIPPING RATES ENGINE ---
        let PLATFORM_SHIPPING_RATES = @json($shippingRates ?? []);

        function getPlatformCourierRate(platform, weightKg) {
            const platformRates = PLATFORM_SHIPPING_RATES.filter(r => r.carrier === platform && r.is_active !== false);
            if (!platformRates.length) {
                // Reliable defaults if database table not yet populated
                if (platform === 'meesho') return (weightKg <= 0.5) ? 38 : (weightKg <= 1.0 ? 52 : (weightKg <= 2.0 ? 75 : 95));
                if (platform === 'flipkart') return (weightKg <= 0.5) ? 48 : (weightKg <= 1.0 ? 68 : (weightKg <= 2.0 ? 98 : 125));
                if (platform === 'direct') return (weightKg <= 0.5) ? 45 : (weightKg <= 1.0 ? 70 : (weightKg <= 2.0 ? 105 : 140));
                return (weightKg <= 0.5) ? 55 : (weightKg <= 1.0 ? 75 : (weightKg <= 2.0 ? 112 : 145)); // Amazon default
            }
            platformRates.sort((a, b) => parseFloat(a.weight) - parseFloat(b.weight));
            for (let r of platformRates) {
                if (weightKg <= parseFloat(r.weight)) {
                    return parseFloat(r.rate);
                }
            }
            return parseFloat(platformRates[platformRates.length - 1].rate);
        }

        function onProductWeightChange(prodId) {
            const wInp = document.getElementById(`row-weight-${prodId}`);
            const weightKg = parseFloat(wInp?.value) || 0.35;

            // Automatically pick the courier rate for this weight
            const amzShipRate = getPlatformCourierRate('amazon', weightKg);
            const shipInp = document.getElementById(`row-ship-${prodId}`);
            if (shipInp) {
                shipInp.value = amzShipRate;
            }

            recalcTableRow(prodId);
            markUnsavedChanges();
            showChannelToast(`⚖️ वजन ${weightKg}kg अनुसार कूरियर दर ₹${amzShipRate} लागू हो गई!`);
        }

        function recalcTableRow(prodId) {
            const cost = parseFloat(document.getElementById(`row-cost-${prodId}`)?.value) || 0;
            const marginPct = parseFloat(document.getElementById(`row-margin-${prodId}`)?.value) || 25;
            
            // RTO Mode: Fixed ₹ or % of Purchase Cost
            const rtoMode = document.getElementById(`row-rto-mode-${prodId}`)?.value || 'fixed';
            const rtoInputVal = parseFloat(document.getElementById(`row-rto-${prodId}`)?.value) || 0;
            const rto = (rtoMode === 'pct') ? Math.round(cost * (rtoInputVal / 100)) : rtoInputVal;
            const rtoAmtSpan = document.getElementById(`row-rto-amt-${prodId}`);
            if (rtoAmtSpan) {
                if (rtoMode === 'pct') {
                    rtoAmtSpan.textContent = `₹${rto} (${rtoInputVal}% लागत)`;
                } else {
                    rtoAmtSpan.textContent = `₹${rto} बफ़र`;
                }
            }

            const pack = parseFloat(document.getElementById(`row-pack-${prodId}`)?.value) || 0;
            const ship = parseFloat(document.getElementById(`row-ship-${prodId}`)?.value) || 0;
            
            // Courier breakdown with 18% GST (e.g. ₹55 + 18% = ₹64.90)
            const shipWithGst = ship > 0 ? (ship * 1.18) : 0;
            const shipAmtSpan = document.getElementById(`row-ship-amt-${prodId}`);
            if (shipAmtSpan) {
                shipAmtSpan.textContent = `₹${ship} + 18% = ₹${shipWithGst.toFixed(1)}`;
            }

            const feePct = parseFloat(document.getElementById(`row-fee-${prodId}`)?.value) || 12;
            const feeGstChecked = document.getElementById(`row-fee-gst-${prodId}`) ? document.getElementById(`row-fee-gst-${prodId}`).checked : true;
            
            const ads = parseFloat(document.getElementById(`row-ads-${prodId}`)?.value) || 0;
            const others = parseFloat(document.getElementById(`row-others-${prodId}`)?.value) || 0;
            const gstRate = parseFloat(document.getElementById(`row-gst-${prodId}`)?.value) || 18;

            const desiredProfit = Math.round(cost * (marginPct / 100));
            
            // Gap GST Formula:
            // When feeWithGst is true: Amazon charges 18% GST on referral fee (effective fee = feePct * 1.18)
            const feeEffectivePct = feeGstChecked ? (feePct * 1.18) : feePct;
            const g = gstRate / 100;
            const f = feeEffectivePct / 100;
            const cTax = g > 0 ? (cost / (1 + g)) : cost;
            const denom = Math.max(0.05, (1 / (1 + g)) - f);
            
            // Courier cash outflow: ship with 18% GST added
            const actualShipCost = shipWithGst > 0 ? shipWithGst : ship;
            const fixedExp = rto + pack + actualShipCost + ads + others;
            const finalRate = Math.ceil((cTax + fixedExp + desiredProfit) / denom);

            // Update live Commission Breakdown Badge
            const feeAmtSpan = document.getElementById(`row-fee-amt-${prodId}`);
            if (feeAmtSpan) {
                const baseFeeVal = finalRate * (feePct / 100);
                const gstFeeVal = baseFeeVal * 0.18;
                const totalFeeVal = feeGstChecked ? (baseFeeVal + gstFeeVal) : baseFeeVal;
                feeAmtSpan.textContent = `₹${baseFeeVal.toFixed(1)} ${feeGstChecked ? '+ GST = ₹' + totalFeeVal.toFixed(1) : '(GST अलग)'}`;
                feeAmtSpan.title = `Amazon कमीशन: ₹${baseFeeVal.toFixed(2)} + 18% GST (₹${gstFeeVal.toFixed(2)}) = कुल ₹${(baseFeeVal + gstFeeVal).toFixed(2)}`;
            }

            // Update row price input & live Store/Online selling price 1
            const priceInput = document.getElementById(`row-price-${prodId}`);
            if (priceInput) priceInput.value = finalRate;

            const storePriceSpan = document.getElementById(`row-store-price-${prodId}`);
            if (storePriceSpan) storePriceSpan.textContent = `₹${finalRate.toFixed(2)}`;

            // Live MRP Compliance, Skip-Single Check & Deal Spread Engine
            const rowMrp = parseFloat(document.getElementById(`row-mrp-${prodId}`)?.value) || 0;
            const mrpAlertEl = document.getElementById(`row-mrp-alert-${prodId}`);
            const skipSingleChk = document.getElementById(`row-skip-single-${prodId}`);
            const isSingleSkipped = skipSingleChk && skipSingleChk.checked;

            if (mrpAlertEl) {
                if (isSingleSkipped) {
                    mrpAlertEl.innerHTML = `<div class="px-1.5 py-0.5 rounded bg-emerald-50 border border-emerald-300 text-emerald-900 text-[8px] font-black leading-tight shadow-2xs mt-1">
                        <span>✅ 1P स्किप: केवल 2P, 4P, 6P कॉम्बो लाइव होंगे</span>
                    </div>`;
                    if (priceInput) priceInput.className = "w-20 pl-5 pr-1.5 py-1 bg-gray-100 border border-gray-300 rounded-lg text-xs font-mono font-bold text-gray-400 line-through cursor-not-allowed";
                } else if (rowMrp > 0 && finalRate > rowMrp) {
                    const diff = Math.round(finalRate - rowMrp);
                    mrpAlertEl.innerHTML = `<div class="px-1.5 py-1 rounded-lg bg-rose-100 border border-rose-300 text-rose-950 text-[9px] font-black leading-tight shadow-2xs mt-1 animate-pulse">
                        <div class="flex items-center gap-1 text-rose-700">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>🚨 MRP से ₹${diff} ऊपर! (डेंजर ज़ोन)</span>
                        </div>
                        <div class="text-[8px] text-rose-800 font-mono mt-0.5">
                            (MRP ₹${rowMrp} vs सेलिंग ₹${finalRate})
                        </div>
                        <div class="text-[8px] text-indigo-700 font-extrabold mt-0.5">
                            👉 नीचे '1P स्किप' टिक करें या 2P/4P बेचें
                        </div>
                    </div>`;
                    if (priceInput) priceInput.className = "w-20 pl-5 pr-1.5 py-1 bg-rose-50 border-2 border-rose-500 rounded-lg text-xs font-mono font-black text-rose-950 focus:outline-none";
                } else if (rowMrp > 0) {
                    const diffUnder = Math.round(rowMrp - finalRate);
                    const discPct = Math.round((diffUnder / rowMrp) * 100);
                    mrpAlertEl.innerHTML = `<div class="px-1 py-0.5 rounded bg-emerald-50 border border-emerald-200 text-[8px] font-bold text-emerald-800 mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>MRP सुरक्षित (₹${diffUnder} कम, ${discPct}% छूट)</span>
                    </div>`;
                    if (priceInput) priceInput.className = "w-20 pl-5 pr-1.5 py-1 bg-white border border-amber-300 rounded-lg text-xs font-mono font-black text-amber-950 focus:border-amber-500 focus:outline-none";
                } else {
                    mrpAlertEl.innerHTML = '';
                    if (priceInput) priceInput.className = "w-20 pl-5 pr-1.5 py-1 bg-white border border-amber-300 rounded-lg text-xs font-mono font-black text-amber-950 focus:border-amber-500 focus:outline-none";
                }
            }

            // Update margin amount badge in table row
            const marginAmtSpan = document.getElementById(`row-margin-amt-${prodId}`);
            if (marginAmtSpan) marginAmtSpan.textContent = `₹${desiredProfit} लाभ`;

            // Read product weight (kg) for dynamic platform courier mapping
            const weightKg = parseFloat(document.getElementById(`row-weight-${prodId}`)?.value) || 0.35;

            // Platform-specific courier rates driven by weight
            const amzShipBase = getPlatformCourierRate('amazon', weightKg);
            const amzShipWithGst = amzShipBase * 1.18;

            const mshShipBase = getPlatformCourierRate('meesho', weightKg);
            const mshShipWithGst = mshShipBase * 1.18;

            // Combo Weight Slabs based on exact product weight multiples
            const w2 = weightKg * 2;
            const w4 = weightKg * 4;
            const w6 = weightKg * 6;

            const ship2Cost = getPlatformCourierRate('amazon', w2) * 1.18;
            const ship4Cost = getPlatformCourierRate('amazon', w4) * 1.18;
            const ship6Cost = getPlatformCourierRate('amazon', w6) * 1.18;

            const c2 = Math.ceil(((cTax * 2) + (desiredProfit * 1.8) + (rto * 1.5) + (pack * 1.3) + ship2Cost + (ads * 1.2)) / denom);
            const c4 = Math.ceil(((cTax * 4) + (desiredProfit * 3.5) + (rto * 2.0) + (pack * 1.6) + ship4Cost + (ads * 1.5)) / denom);
            const c6 = Math.ceil(((cTax * 6) + (desiredProfit * 5.0) + (rto * 2.5) + (pack * 2.0) + ship6Cost + (ads * 1.8)) / denom);

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

            // Populate Combined Summary Expense Box for Compact Mode
            const totalExpVal = Math.round(fixedExp + (finalRate * (feePct / 100) * 1.18));
            const expTotalSpan = document.getElementById(`row-total-exp-${prodId}`);
            if (expTotalSpan) expTotalSpan.textContent = `₹${totalExpVal}`;
            const expBreakSpan = document.getElementById(`row-exp-break-${prodId}`);
            if (expBreakSpan) expBreakSpan.textContent = `कूरियर ₹${Math.round(actualShipCost)} + RTO ₹${rto} + पैक ₹${pack}`;

            // 🔄 Platform-wise prices (Amazon 12% fee + GST vs Meesho 0% fee)
            const denomAmz = Math.max(0.05, (1 / (1 + g)) - 0.12);
            const fixedAmz = rto + pack + amzShipWithGst + ads + others;
            const calcAmz = Math.ceil((cTax + fixedAmz + desiredProfit) / denomAmz);

            const denomMsh = Math.max(0.05, (1 / (1 + g)));
            const fixedMsh = rto + pack + mshShipWithGst + others;
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

            // Meesho Combo calculations (0% fee, RTO 35, ship 38)
            const mshC2 = Math.ceil(((cTax * 2) + (desiredProfit * 1.8) + (rto * 1.4) + (pack * 1.3) + Math.min(38, ship)) / denomMsh);
            const mshC4 = Math.ceil(((cTax * 4) + (desiredProfit * 3.5) + (rto * 1.8) + (pack * 1.6) + (Math.min(38, ship) * 1.15)) / denomMsh);
            const mshC6 = Math.ceil(((cTax * 6) + (desiredProfit * 5.0) + (Math.max(35, rto) * 2.2) + (pack * 2.0) + (Math.min(38, ship) * 1.3)) / denomMsh);

            // Update Amazon & Meesho Card Combo spans
            const cardC2 = document.getElementById(`card-combo2-${prodId}`);
            if (cardC2) cardC2.textContent = `₹${c2}`;
            const cardC4 = document.getElementById(`card-combo4-${prodId}`);
            if (cardC4) cardC4.textContent = `₹${c4}`;
            const cardC6 = document.getElementById(`card-combo6-${prodId}`);
            if (cardC6) cardC6.textContent = `₹${c6}`;

            const elM2 = document.getElementById(`card-msh-combo2-${prodId}`);
            if (elM2) elM2.textContent = `₹${mshC2}`;
            const elM4 = document.getElementById(`card-msh-combo4-${prodId}`);
            if (elM4) elM4.textContent = `₹${mshC4}`;
            const elM6 = document.getElementById(`card-msh-combo6-${prodId}`);
            if (elM6) elM6.textContent = `₹${mshC6}`;

            // Update Table Row combo badges
            const rAmzC2 = document.getElementById(`row-amz-combo2-${prodId}`);
            if (rAmzC2) rAmzC2.textContent = c2;
            const rAmzC4 = document.getElementById(`row-amz-combo4-${prodId}`);
            if (rAmzC4) rAmzC4.textContent = c4;
            const rAmzC6 = document.getElementById(`row-amz-combo6-${prodId}`);
            if (rAmzC6) rAmzC6.textContent = c6;

            const rMshC2 = document.getElementById(`row-msh-combo2-${prodId}`);
            if (rMshC2) rMshC2.textContent = mshC2;
            const rMshC4 = document.getElementById(`row-msh-combo4-${prodId}`);
            if (rMshC4) rMshC4.textContent = mshC4;
            const rMshC6 = document.getElementById(`row-msh-combo6-${prodId}`);
            if (rMshC6) rMshC6.textContent = mshC6;

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
        
        // Quick button handlers for row and bulk inputs
        function setRowCourier(prodId, rate) {
            const inp = document.getElementById(`row-ship-${prodId}`);
            if (inp) {
                inp.value = rate;
                recalcTableRow(prodId);
            }
        }

        function setRowPack(prodId, rate) {
            const inp = document.getElementById(`row-pack-${prodId}`);
            if (inp) {
                inp.value = rate;
                recalcTableRow(prodId);
            }
        }

        function setRowAds(prodId, rate) {
            const inp = document.getElementById(`row-ads-${prodId}`);
            if (inp) {
                inp.value = rate;
                recalcTableRow(prodId);
            }
        }

        function setBulkCourier(rate) {
            const inp = document.getElementById('bulkPresetShip');
            if (inp) inp.value = rate;
        }

        function setBulkPack(rate) {
            const inp = document.getElementById('bulkPresetPack');
            if (inp) inp.value = rate;
        }

        function setBulkAds(rate) {
            const inp = document.getElementById('bulkPresetAds');
            if (inp) inp.value = rate;
        }

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
            const rtoMode = document.getElementById(`row-rto-mode-${sourceProdId}`)?.value || 'fixed';
            const ship = parseFloat(document.getElementById(`row-ship-${sourceProdId}`)?.value) || 55;
            const pack = parseFloat(document.getElementById(`row-pack-${sourceProdId}`)?.value) || 15;
            const fee = parseFloat(document.getElementById(`row-fee-${sourceProdId}`)?.value) || 12;
            const feeGst = document.getElementById(`row-fee-gst-${sourceProdId}`) ? document.getElementById(`row-fee-gst-${sourceProdId}`).checked : true;
            const gst = document.getElementById(`row-gst-${sourceProdId}`)?.value || '18';

            if (document.getElementById('bulkPresetRtoMode')) document.getElementById('bulkPresetRtoMode').value = rtoMode;
            if (document.getElementById('bulkPresetFeeGst')) document.getElementById('bulkPresetFeeGst').checked = feeGst;

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

                const rModeSel = document.getElementById(`row-rto-mode-${id}`);
                if (rModeSel) rModeSel.value = rtoMode;

                const fGstChk = document.getElementById(`row-fee-gst-${id}`);
                if (fGstChk) fGstChk.checked = feeGst;

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
                // Keep user's custom RTO buffer intact (e.g. 20) or default to 20
                const curRto = document.getElementById('bulkPresetRto');
                if (curRto && (!curRto.value || parseFloat(curRto.value) === 0)) curRto.value = 20;
                if (document.getElementById('bulkPresetShip')) document.getElementById('bulkPresetShip').value = 38; // Meesho delivery base
                if (document.getElementById('bulkPresetPack')) document.getElementById('bulkPresetPack').value = 15;
                if (document.getElementById('bulkPresetMargin')) document.getElementById('bulkPresetMargin').value = 25;
                
                applyPresetToAllRows();
                showChannelToast('🌸 Meesho मोड लागू! (0% कमीशन, ₹' + (curRto?.value || 20) + ' रिटर्न बफ़र, ₹38 कूरियर, 25% मार्जिन)');
            } else if (platform === 'amazon') {
                if (document.getElementById('bulkPresetFee')) document.getElementById('bulkPresetFee').value = 12;
                const curRto = document.getElementById('bulkPresetRto');
                if (curRto && (!curRto.value || parseFloat(curRto.value) === 0)) curRto.value = 20;
                if (document.getElementById('bulkPresetShip')) document.getElementById('bulkPresetShip').value = 55; // Standard 0.5kg Easy Ship base slab is ₹55!
                if (document.getElementById('bulkPresetPack')) document.getElementById('bulkPresetPack').value = 15;
                if (document.getElementById('bulkPresetMargin')) document.getElementById('bulkPresetMargin').value = 25;
                
                applyPresetToAllRows();
                showChannelToast('🟠 Amazon मोड लागू! (12% कमीशन, ₹' + (curRto?.value || 20) + ' रिटर्न बफ़र, ₹55 Easy Ship, 25% मार्जिन)');
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
            const rtoMode = document.getElementById('bulkPresetRtoMode')?.value || 'fixed';

            const feeGstChecked = document.getElementById('bulkPresetFeeGst') ? document.getElementById('bulkPresetFeeGst').checked : true;

            const shipRaw = document.getElementById('bulkPresetShip')?.value;
            const ship = (shipRaw !== '' && !isNaN(parseFloat(shipRaw))) ? parseFloat(shipRaw) : 55;

            const packRaw = document.getElementById('bulkPresetPack')?.value;
            const pack = (packRaw !== '' && !isNaN(parseFloat(packRaw))) ? parseFloat(packRaw) : 15;

            const adsRaw = document.getElementById('bulkPresetAds')?.value;
            const ads = (adsRaw !== '' && !isNaN(parseFloat(adsRaw))) ? parseFloat(adsRaw) : 10;

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
                    const cardStockInp = document.getElementById(`card-stock-inp-${id}`);
                    if (cardStockInp) cardStockInp.value = stockVal;
                    const cardStock = document.getElementById(`card-stock-${id}`);
                    if (cardStock) cardStock.textContent = `${stockVal} pcs`;
                }

                const rtoInput = document.getElementById(`row-rto-${id}`);
                if (rtoInput) rtoInput.value = rto;

                const rtoModeSel = document.getElementById(`row-rto-mode-${id}`);
                if (rtoModeSel) rtoModeSel.value = rtoMode;

                const feeGstChk = document.getElementById(`row-fee-gst-${id}`);
                if (feeGstChk) feeGstChk.checked = feeGstChecked;

                const adsInp = document.getElementById(`row-ads-${id}`);
                if (adsInp) adsInp.value = ads;

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
                alert('कोई प्रोडक्ट्स उपलब्ध नहीं हैं!');
                return;
            }

            // 🚨 STRICT MRP COMPLIANCE AUDIT BEFORE SAVING
            const offendingItems = [];
            rows.forEach(row => {
                const id = row.getAttribute('data-id');
                const cost = parseFloat(document.getElementById(`row-cost-${id}`)?.value) || 0;
                const price = parseFloat(document.getElementById(`row-price-${id}`)?.value) || 0;
                const mrp = parseFloat(document.getElementById(`row-mrp-${id}`)?.value || document.getElementById(`card-mrp-${id}`)?.value) || 0;
                const skipSingle = document.getElementById(`row-skip-single-${id}`)?.checked;
                const nameEl = document.querySelector(`#table-row-${id} td:nth-child(3) span`) || document.getElementById(`card-name-${id}`);
                const name = nameEl ? nameEl.textContent.trim() : `Product #${id}`;

                if (mrp > 0 && price > mrp && !skipSingle) {
                    offendingItems.push({ id, name, mrp, price, diff: Math.round(price - mrp) });
                    row.classList.add('bg-rose-100', 'ring-2', 'ring-rose-400');
                } else {
                    row.classList.remove('bg-rose-100', 'ring-2', 'ring-rose-400');
                }
            });

            if (offendingItems.length > 0) {
                let msg = `🚨 कानूनी उल्लंघन अलर्ट (MRP Compliance Violation)!\n\nकुल ${offendingItems.length} प्रोडक्ट्स में सेलिंग भाव डिब्बे पर छपे MRP से अधिक है:\n\n`;
                offendingItems.forEach((it, idx) => {
                    msg += `${idx + 1}. ${it.name}\n   👉 MRP: ₹${it.mrp} vs सेलिंग भाव: ₹${it.price} (₹${it.diff} अधिक!)\n`;
                });
                msg += `\nअमेज़न पर MRP से अधिक भाव रखने पर लिस्टिंग ब्लॉक हो जाएगी!\n\nक्या आप इन सभी ${offendingItems.length} प्रोडक्ट्स में '1P स्किप (केवल 2P/4P/6P कॉम्बो)' ऑटोमैटिक लागू करके सुरक्षित सेव करना चाहते हैं?\n\n[OK] = 1P स्किप लागू करें और सुरक्षित सेव करें\n[Cancel] = खुद हाथ से ठीक करें`;

                if (confirm(msg)) {
                    offendingItems.forEach(it => {
                        const chk = document.getElementById(`row-skip-single-${it.id}`);
                        if (chk) chk.checked = true;
                        onToggleSkipSingle(it.id);
                    });
                    showChannelToast('✓ सभी उल्लंघन वाले प्रोडक्ट्स में 1P स्किप लागू कर दिया गया। अब सुरक्षित सेव हो रहा है...');
                } else {
                    alert('कृपया हाइलाइट किए गए लाल प्रोडक्ट्स में MRP बढ़ाएं या भाव घटाएं।');
                    return;
                }
            }

            const items = [];
            rows.forEach(row => {
                const id = row.getAttribute('data-id');
                const cost = parseFloat(document.getElementById(`row-cost-${id}`)?.value) || 0;
                const marginPct = parseFloat(document.getElementById(`row-margin-${id}`)?.value) || 25;
                const rto = parseFloat(document.getElementById(`row-rto-${id}`)?.value) || 0;
                const rtoMode = document.getElementById(`row-rto-mode-${id}`)?.value || 'fixed';
                const weightKg = parseFloat(document.getElementById(`row-weight-${id}`)?.value) || 0.35;
                const feeGstVal = document.getElementById(`row-fee-gst-${id}`) ? document.getElementById(`row-fee-gst-${id}`).checked : true;
                const price = parseFloat(document.getElementById(`row-price-${id}`)?.value) || 0;
                const pack = parseFloat(document.getElementById(`row-pack-${id}`)?.value) || 0;
                const ship = parseFloat(document.getElementById(`row-ship-${id}`)?.value) || 0;
                const fee = parseFloat(document.getElementById(`row-fee-${id}`)?.value) || 0;
                const ads = parseFloat(document.getElementById(`row-ads-${id}`)?.value) || 0;
                const others = parseFloat(document.getElementById(`row-others-${id}`)?.value) || 0;
                const hsn = document.getElementById(`row-hsn-${id}`)?.value || '';
                const gst = parseFloat(document.getElementById(`row-gst-${id}`)?.value) || 18;
                const note = document.getElementById(`row-note-${id}`)?.value || '';

                const isCardsView = !document.getElementById('pendingCardsContainer')?.classList.contains('hidden');
                const stockInp = isCardsView 
                    ? (document.getElementById(`card-stock-inp-${id}`) || document.getElementById(`row-stock-${id}`))
                    : (document.getElementById(`row-stock-${id}`) || document.getElementById(`card-stock-inp-${id}`));
                const stock = stockInp ? (parseInt(stockInp.value, 10) || 0) : 0;
                const cardStock = document.getElementById(`card-stock-${id}`);
                if (cardStock) cardStock.textContent = `${stock} pcs`;

                const lessVal = parseFloat(document.getElementById(`row-less-${id}`)?.value) || 0;
                const c2Val = parseFloat(document.getElementById(`row-combo2-${id}`)?.value) || 0;
                const c4Val = parseFloat(document.getElementById(`row-combo4-${id}`)?.value) || 0;
                const c6Val = parseFloat(document.getElementById(`row-combo6-${id}`)?.value) || 0;
                const mrpVal = parseFloat(document.getElementById(`row-mrp-${id}`)?.value) || null;
                items.push({
                    product_id: parseInt(id),
                    mrp: mrpVal,
                    stock_quantity: stock,
                    purchase_price: cost,
                    trade_discount_percent: lessVal,
                    margin_pct: marginPct,
                    rto_buffer: rto,
                    rto_mode: rtoMode,
                    fee_gst_included: feeGstVal,
                    weight_kg: weightKg,
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

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
            const CHUNK_SIZE = 15;
            let totalSaved = 0;
            let hasError = false;

            try {
                for (let i = 0; i < items.length; i += CHUNK_SIZE) {
                    const chunk = items.slice(i, i + CHUNK_SIZE);
                    const progress = Math.min(i + CHUNK_SIZE, items.length);
                    btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> <span>सेव हो रहा है (${progress}/${items.length})...</span>`;

                    let attempts = 0;
                    let success = false;
                    while (attempts < 2 && !success) {
                        attempts++;
                        try {
                            const res = await fetch('/seller/channels/save-all-costing', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({ items: chunk })
                            });
                            if (res.ok) {
                                const data = await res.json();
                                if (data.success) {
                                    totalSaved += (data.saved_count || chunk.length);
                                    success = true;
                                } else {
                                    throw new Error(data.message || 'त्रुटि आई');
                                }
                            } else {
                                throw new Error(`HTTP ${res.status}`);
                            }
                        } catch (err) {
                            if (attempts >= 2) throw err;
                            await new Promise(r => setTimeout(r, 600));
                        }
                    }
                }

                showChannelToast(`✓ सभी ${totalSaved} प्रोडक्ट्स के मार्जिन, कूरियर व स्टॉक सुरक्षित सेव हो गए!`);
                clearUnsavedChanges();
                btn.innerHTML = '<i class="fa-solid fa-check text-emerald-300"></i> <span>सफलतापूर्वक सेव हुआ!</span>';
                setTimeout(() => {
                    btn.innerHTML = origHtml;
                    btn.disabled = false;
                }, 2500);
            } catch (e) {
                console.error('Batch save error:', e);
                showChannelToast('⚠️ कुछ डेटा सेव हुआ (' + totalSaved + '/' + items.length + ')। नेटवर्क फिर से जांचें।');
                btn.innerHTML = origHtml;
                btn.disabled = false;
            }
        }

        // Save Inline Table Row Costing & Unit Economics
        async function saveTableRowCosting(prodId, btn) {
            const cost = parseFloat(document.getElementById(`row-cost-${prodId}`)?.value) || 0;
            const marginPct = parseFloat(document.getElementById(`row-margin-${prodId}`)?.value) || 25;
            const rto = parseFloat(document.getElementById(`row-rto-${prodId}`)?.value) || 0;
            const rtoMode = document.getElementById(`row-rto-mode-${prodId}`)?.value || 'fixed';
            const weightKg = parseFloat(document.getElementById(`row-weight-${prodId}`)?.value) || 0.35;
            const feeGstVal = document.getElementById(`row-fee-gst-${prodId}`) ? document.getElementById(`row-fee-gst-${prodId}`).checked : true;
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

            const mrpCheck = parseFloat(document.getElementById(`row-mrp-${prodId}`)?.value || document.getElementById(`card-mrp-${prodId}`)?.value) || 0;
            const isSingleSkipped = document.getElementById(`row-skip-single-${prodId}`)?.checked;
            if (mrpCheck > 0 && price > mrpCheck && !isSingleSkipped) {
                const diff = Math.round(price - mrpCheck);
                alert(`🚨 कानूनी उल्लंघन अलर्ट (MRP Compliance Block)!\n\nइस प्रोडक्ट का फाइनल सेलिंग भाव (₹${price}) इसके डिब्बे पर छपे MRP (₹${mrpCheck}) से ₹${diff} अधिक है!\n\nअमेज़न व सरकारी विधिक माप विज्ञान (Legal Metrology) के नियमानुसार MRP से अधिक पर बेचना गैर-कानूनी है और अकाउंट सस्पेंड हो सकता है।\n\nसमाधान:\n1. या तो नीचे '🛑 1P स्किप (केवल कॉम्बो)' चेकबॉक्स पर टिक करें,\n2. या नया डिब्बा MRP बढ़ाएं,\n3. या सेलिंग भाव को ₹${mrpCheck} या उससे कम करें।`);
                // Highlight row in pulsing red
                const rowEl = document.getElementById(`table-row-${prodId}`);
                if (rowEl) rowEl.classList.add('bg-rose-100', 'ring-2', 'ring-rose-500');
                return;
            }

            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i>';

            const lessVal = parseFloat(document.getElementById(`row-less-${prodId}`)?.value || document.getElementById(`card-less-${prodId}`)?.value) || 0;
            const isCardsView = !document.getElementById('pendingCardsContainer')?.classList.contains('hidden');
            const stockInpEl = isCardsView 
                ? (document.getElementById(`card-stock-inp-${prodId}`) || document.getElementById(`row-stock-${prodId}`))
                : (document.getElementById(`row-stock-${prodId}`) || document.getElementById(`card-stock-inp-${prodId}`));
            const stock = stockInpEl ? (parseInt(stockInpEl.value, 10) || 0) : 0;
            try {
                const mrpSingle = parseFloat(document.getElementById(`row-mrp-${prodId}`)?.value || document.getElementById(`card-mrp-${prodId}`)?.value) || null;
                const res = await fetch(`/seller/channels/products/${prodId}/save-costing`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        mrp: mrpSingle,
                        stock_quantity: stock,
                        purchase_price: cost,
                        trade_discount_percent: lessVal,
                        margin_pct: marginPct,
                        rto_buffer: rto,
                        rto_mode: rtoMode,
                        fee_gst_included: feeGstVal,
                        weight_kg: weightKg,
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
        <div class="bg-white rounded-3xl max-w-2xl w-full p-5 sm:p-6 shadow-2xl border border-gray-100 space-y-4 animate-in fade-in zoom-in duration-200 my-auto" onclick="event.stopPropagation()">
            <!-- Modal Header with Left & Right Product Navigation Arrows -->
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 gap-2">
                <div class="flex items-center gap-2.5 min-w-0">
                    <button type="button" onclick="navCalcModalProduct(-1)" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-indigo-100 text-indigo-900 flex items-center justify-center text-xs font-black transition shadow-2xs cursor-pointer active:scale-95 shrink-0" title="पिछला प्रोडक्ट (Previous Product)">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <div class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-200 overflow-hidden flex items-center justify-center shrink-0">
                        <img src="" alt="" id="calcModalProdImg" class="h-full w-full object-contain hidden">
                        <i class="fa-solid fa-calculator text-indigo-600 text-base" id="calcModalProdImgPh"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="px-2 py-0.5 rounded-md bg-indigo-600 text-white font-mono font-black text-[10px]" id="calcModalProdIdBadge">#0</span>
                            <span class="text-[10px] font-bold text-gray-400 font-mono" id="calcModalProdSku">SKU: STD</span>
                        </div>
                        <h3 class="font-black text-xs sm:text-sm text-gray-900 truncate max-w-sm mt-0.5 leading-tight" id="calcModalProdName">Product Name</h3>
                    </div>
                    <button type="button" onclick="navCalcModalProduct(1)" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-indigo-100 text-indigo-900 flex items-center justify-center text-xs font-black transition shadow-2xs cursor-pointer active:scale-95 shrink-0" title="अगला प्रोडक्ट (Next Product)">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
                <button type="button" onclick="closeCostCalculatorModal()" class="text-gray-400 hover:text-gray-600 p-1 shrink-0">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Platform Selector Dropdown -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 bg-gradient-to-r from-indigo-50/80 to-purple-50/80 p-3 rounded-2xl border border-indigo-200/80 text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs shadow-2xs">
                        <i class="fa-solid fa-store"></i>
                    </span>
                    <div>
                        <span class="font-black text-indigo-950 block">प्लेटफ़ॉर्म चुनें (Marketplace):</span>
                        <span class="text-[10px] text-indigo-700">प्लेटफ़ॉर्म बदलते ही फ़ी, कूरियर व RTO दरें अपने-आप अपडेट होंगी</span>
                    </div>
                </div>
                <div class="flex items-center gap-1.5">
                    <select id="calcModalPlatformSelect" onchange="onModalPlatformSelect(this.value)" class="bg-white border-2 border-indigo-300 font-black text-xs text-indigo-950 rounded-xl px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer shadow-2xs">
                        <option value="amazon">🟠 Amazon India (12% फ़ी + Easy Ship ₹55)</option>
                        <option value="meesho">🌸 Meesho Supplier (0% फ़ी + कूरियर ₹38)</option>
                        <option value="flipkart">⚡ Flipkart (10% फ़ी + Express ₹48)</option>
                        <option value="direct">🛒 Direct Store (0% फ़ी + लोकल ₹45)</option>
                    </select>
                </div>
            </div>

            <!-- Interactive Inputs Grid -->
            <div class="space-y-3 text-xs">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <!-- 1. Cost Price -->
                    <div class="bg-slate-50 p-2.5 rounded-2xl border border-slate-200">
                        <label class="block font-bold text-gray-700 text-[11px] mb-1">आपकी खरीद लागत (Cost Price)</label>
                        <div class="relative">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 font-bold text-gray-500 text-xs">₹</span>
                            <input type="number" step="any" min="0" id="calcCostPrice" value="100" oninput="calculateLiveSellingPrice()" class="w-full pl-6 pr-2 py-1.5 rounded-xl border border-gray-300 bg-white font-mono font-bold text-gray-900 focus:border-indigo-500 focus:outline-none">
                        </div>
                    </div>

                    <!-- 2. Margin % -->
                    <div class="bg-slate-50 p-2.5 rounded-2xl border border-slate-200">
                        <div class="flex items-center justify-between mb-1">
                            <label class="font-bold text-gray-700 text-[11px]">शुद्ध मुनाफा (Margin %)</label>
                            <span class="text-[10px] font-mono font-black text-emerald-700" id="calcMarginProfitSpan">₹25</span>
                        </div>
                        <div class="relative">
                            <input type="number" step="any" min="0" id="calcMarginPercent" value="25" oninput="calculateLiveSellingPrice()" class="w-full pr-6 pl-2.5 py-1.5 rounded-xl border border-gray-300 bg-white font-mono font-bold text-emerald-700 focus:border-emerald-500 focus:outline-none">
                            <span class="absolute right-2.5 top-1/2 -translate-y-1/2 font-bold text-gray-400">%</span>
                        </div>
                    </div>

                    <!-- 3. Printed MRP -->
                    <div class="bg-amber-50/60 p-2.5 rounded-2xl border border-amber-200">
                        <label class="block font-bold text-amber-900 text-[11px] mb-1">डिब्बा MRP (प्रिंटेड भाव ₹)</label>
                        <div class="relative">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 font-bold text-amber-700 text-xs">₹</span>
                            <input type="number" step="any" min="0" id="calcModalMrp" value="0" placeholder="MRP दर्ज करें" oninput="calculateLiveSellingPrice()" class="w-full pl-6 pr-2 py-1.5 rounded-xl border border-amber-300 bg-white font-mono font-bold text-amber-950 focus:border-amber-500 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Platform Expenses Row -->
                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 space-y-2">
                    <span class="text-[10px] font-black uppercase text-slate-700 tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-receipt text-indigo-600"></i>
                        <span>मार्केटप्लेस खर्चे (अनुमानित 1 पीस के लिए)</span>
                    </span>

                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                        <!-- Courier / Shipping with 18% GST -->
                        <div class="col-span-2 sm:col-span-1">
                            <div class="flex items-center justify-between">
                                <label class="text-[10px] font-bold text-gray-700">कूरियर डिलीवरी</label>
                                <span class="text-[8px] font-bold text-sky-700" id="calcShipGstBadge">+18% GST</span>
                            </div>
                            <input type="number" step="any" id="calcShippingCost" value="55" oninput="calculateLiveSellingPrice()" class="w-full px-2 py-1 bg-white border border-gray-300 rounded-lg font-mono font-semibold text-gray-800 text-xs">
                            <div class="flex items-center gap-0.5 mt-1">
                                <button type="button" onclick="setModalShipSlab(55)" class="px-1 py-0.2 text-[8px] rounded bg-sky-100 hover:bg-sky-200 text-sky-900 font-bold border border-sky-200 cursor-pointer">0.5k: ₹55</button>
                                <button type="button" onclick="setModalShipSlab(75)" class="px-1 py-0.2 text-[8px] rounded bg-sky-100 hover:bg-sky-200 text-sky-900 font-bold border border-sky-200 cursor-pointer">1k: ₹75</button>
                                <button type="button" onclick="setModalShipSlab(112)" class="px-1 py-0.2 text-[8px] rounded bg-sky-100 hover:bg-sky-200 text-sky-900 rounded font-bold border border-sky-200 cursor-pointer">2k: ₹112</button>
                            </div>
                        </div>

                        <!-- Packing Cost -->
                        <div>
                            <label class="text-[10px] font-bold text-gray-700 block">पैकिंग सामग्री</label>
                            <input type="number" step="any" id="calcPackingCost" value="15" oninput="calculateLiveSellingPrice()" class="w-full px-2 py-1 bg-white border border-gray-300 rounded-lg font-mono font-semibold text-gray-800 text-xs">
                        </div>

                        <!-- RTO Buffer -->
                        <div>
                            <label class="text-[10px] font-bold text-gray-700 block">RTO / रिटर्न बफ़र</label>
                            <input type="number" step="any" id="calcModalRto" value="20" oninput="calculateLiveSellingPrice()" class="w-full px-2 py-1 bg-white border border-gray-300 rounded-lg font-mono font-semibold text-rose-700 text-xs">
                        </div>

                        <!-- Platform Fee % -->
                        <div>
                            <label class="text-[10px] font-bold text-gray-700 block">कमीशन फ़ी (%)</label>
                            <input type="number" step="any" id="calcPlatformFeePercent" value="12" oninput="calculateLiveSellingPrice()" class="w-full px-2 py-1 bg-white border border-gray-300 rounded-lg font-mono font-semibold text-purple-900 text-xs">
                        </div>

                        <!-- Product GST -->
                        <div>
                            <label class="text-[10px] font-bold text-indigo-900 block">माल GST दर</label>
                            <select id="calcProductGst" onchange="calculateLiveSellingPrice()" class="w-full px-1.5 py-1 bg-white border border-indigo-300 rounded-lg font-mono font-bold text-indigo-900 text-xs">
                                <option value="18" selected>18% (हार्डवेयर)</option>
                                <option value="12">12%</option>
                                <option value="5">5% (कपड़े)</option>
                                <option value="0">0% (टैक्स फ्री)</option>
                                <option value="28">28% (लक्ज़री)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 🧾 100% TRANSPARENT RUPEE-BY-RUPEE BREAKDOWN LEDGER (₹201 से ₹500 का पूरा गणित) -->
                <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-4 sm:p-5 shadow-xl border border-indigo-500/30 space-y-3">
                    <div class="flex items-center justify-between border-b border-white/10 pb-2">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-indigo-300 flex items-center gap-1">
                                <i class="fa-solid fa-calculator text-amber-300"></i>
                                <span>फाइनल सेलिंग रेट (Selling Price)</span>
                            </span>
                            <div class="text-3xl font-black text-amber-300 font-mono mt-0.5 tracking-tight" id="calcFinalSellingRate">₹0.00</div>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-emerald-300 block">प्रति पीस भाव (Per Pc)</span>
                            <div class="text-xl font-black text-white font-mono" id="calcPerPcSellingRate">₹0.00/pc</div>
                            <div class="text-[10px] font-bold text-emerald-400 mt-0.5" id="calcNetProfitBadge">मुनाफा: ₹0.00</div>
                        </div>
                    </div>

                    <!-- ⚖️ MRP vs Selling Price Gap / Deal Spread Badge -->
                    <div class="p-2.5 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-between flex-wrap gap-2 text-xs" id="calcModalMrpGapBox">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-amber-400 text-slate-950 flex items-center justify-center text-xs font-black">MRP</span>
                            <span class="text-slate-300" id="calcModalMrpText">डिब्बा MRP: ₹0.00</span>
                        </div>
                        <div class="font-bold" id="calcModalMrpDiffBadge">
                            <span class="text-slate-400">MRP दर्ज नहीं</span>
                        </div>
                    </div>

                    <!-- 🔍 Transparent Step-by-Step Itemized Ledger -->
                    <div class="bg-black/40 rounded-2xl p-3 border border-white/10 space-y-1.5 font-mono text-[11px]">
                        <div class="text-[10px] font-sans font-black text-amber-300 uppercase tracking-wider pb-1 border-b border-white/10 flex items-center justify-between">
                            <span>रुपये-रुपये का स्पष्ट हिसाब (Price Breakdown):</span>
                            <span class="text-slate-400 font-normal">कहाँ कितना खर्च जुड़ रहा है</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-300">
                            <span>1. आपकी खरीद लागत (Purchase Cost):</span>
                            <span class="font-bold text-white" id="ledger_cost">₹0.00</span>
                        </div>
                        <div class="flex items-center justify-between text-emerald-300">
                            <span>2. आपका शुद्ध मुनाफा (Profit Margin):</span>
                            <span class="font-bold" id="ledger_profit">+₹0.00</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-300">
                            <span>3. डिब्बा व पैकिंग खर्च:</span>
                            <span class="font-bold text-white" id="ledger_pack">+₹0.00</span>
                        </div>
                        <div class="flex items-center justify-between text-sky-300">
                            <span>4. कूरियर डिलीवरी (Easy Ship + 18% GST):</span>
                            <span class="font-bold" id="ledger_ship">+₹0.00</span>
                        </div>
                        <div class="flex items-center justify-between text-rose-300">
                            <span>5. RTO / रिटर्न नुकसान सुरक्षा बफ़र:</span>
                            <span class="font-bold" id="ledger_rto">+₹0.00</span>
                        </div>
                        <div class="flex items-center justify-between text-purple-300">
                            <span>6. मार्केटप्लेस कमीशन फ़ी (12% + 18% GST):</span>
                            <span class="font-bold" id="ledger_fee">+₹0.00</span>
                        </div>
                        <!-- 7. Detailed GST & ITC Breakdown Box (Output GST vs Input ITC Adjustments) -->
                        <div class="mt-2 pt-2 border-t border-white/10 space-y-1.5 bg-white/5 p-3 rounded-2xl border border-white/10 text-[10px]">
                            <div class="flex items-center justify-between text-amber-300 font-bold uppercase pb-1 border-b border-white/10">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-scale-balanced text-amber-400"></i>
                                    <span>7. सरकारी GST व इनपुट ITC बहीखाता (Adjustments)</span>
                                </span>
                                <span class="font-mono text-emerald-300 font-black" id="ledger_gst_net_badge">शुद्ध देय: ₹0.00</span>
                            </div>
                            
                            <!-- Gross Output GST collected from buyer -->
                            <div class="flex items-center justify-between text-slate-300 pl-2 border-l-2 border-amber-400">
                                <span>📦 ग्राहक से वसूला गया कुल आउटपुट GST (<span id="ledger_gst_rate_span" class="text-amber-200 font-bold">18%</span>):</span>
                                <span class="font-bold text-amber-300 font-mono" id="ledger_output_gst">+₹0.00</span>
                            </div>

                            <!-- Input Tax Credits (ITC) Deductions -->
                            <div class="text-[9px] text-slate-400 font-sans font-bold pt-0.5">माइनस इनपुट टैक्स क्रेडिट (ITC - जो पहले ही चुका दिया और सरकार से वापस कटेगा):</div>
                            
                            <div class="flex items-center justify-between text-slate-300 pl-2 border-l-2 border-emerald-400">
                                <span>🔹 माल खरीद पर इनपुट GST (Purchase ITC):</span>
                                <span class="font-bold text-emerald-300 font-mono" id="ledger_purchase_itc">-₹0.00</span>
                            </div>

                            <div class="flex items-center justify-between text-slate-300 pl-2 border-l-2 border-emerald-400">
                                <span>🔹 कूरियर डिलीवरी सेवा पर इनपुट GST (18% ITC):</span>
                                <span class="font-bold text-emerald-300 font-mono" id="ledger_courier_itc">-₹0.00</span>
                            </div>

                            <div class="flex items-center justify-between text-slate-300 pl-2 border-l-2 border-emerald-400">
                                <span>🔹 <span id="ledger_platform_name" class="font-black text-white">Amazon</span> कमीशन फ़ी पर इनपुट GST:</span>
                                <span class="font-bold text-emerald-300 font-mono" id="ledger_comm_itc">-₹0.00</span>
                            </div>

                            <!-- Net Cash GST Payable to Govt -->
                            <div class="flex items-center justify-between pt-1.5 border-t border-white/10 font-bold text-[11px] text-white">
                                <span class="text-amber-300">🏛️ सरकार को शुद्ध नकद GST (देय):</span>
                                <span class="font-mono text-amber-300 font-black text-xs" id="ledger_gst">+₹0.00</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-slate-300 pt-1 border-t border-white/20 font-sans font-bold text-xs text-white">
                            <span>🎯 कुल फाइनल सेलिंग भाव (Gross Price):</span>
                            <span class="font-mono text-amber-300 text-sm font-black" id="ledger_total">₹0.00</span>
                        </div>
                    </div>

                    <!-- 2P, 4P, 6P Combo Instant Rates -->
                    <div class="pt-1 flex items-center justify-between text-[11px] gap-2">
                        <span class="text-indigo-200 font-sans font-bold text-[10px]">📦 कॉम्बो भाव:</span>
                        <div class="flex items-center gap-2">
                            <span class="bg-white/10 px-2 py-0.5 rounded-lg border border-white/10">2P: <strong class="text-amber-300 font-mono" id="calcCombo2Rate">₹0</strong></span>
                            <span class="bg-white/10 px-2 py-0.5 rounded-lg border border-white/10">4P: <strong class="text-amber-300 font-mono" id="calcCombo4Rate">₹0</strong></span>
                            <span class="bg-white/10 px-2 py-0.5 rounded-lg border border-white/10">6P: <strong class="text-amber-300 font-mono" id="calcCombo6Rate">₹0</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="pt-2 flex items-center justify-between gap-2 border-t border-gray-100 flex-wrap">
                    <button type="button" onclick="closeCostCalculatorModal(); openComboCalculatorModal(activeCalcProductId, document.getElementById('calcModalProdName').textContent)" class="px-3.5 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-800 font-bold transition flex items-center gap-1.5 cursor-pointer text-xs">
                        <i class="fa-solid fa-layer-group text-indigo-600"></i>
                        <span>2/4/6 पीस स्लैब विवरण &rarr;</span>
                    </button>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="saveActiveModalCosting(this)" id="btnSaveFromModal" class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer active:scale-95">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>इस प्रोडक्ट का रेट सेव करें</span>
                        </button>
                        <button type="button" onclick="closeCostCalculatorModal()" class="px-4 py-2 rounded-xl bg-gray-900 text-white font-bold transition text-xs">
                            बंद करें
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                                <div>
                                    <span class="text-gray-600 block">कूरियर (0.5kg स्लैब):</span>
                                    <span class="text-[8px] text-sky-700 font-bold block" id="combo2_ship_badge">₹55 + 18% GST = ₹64.9</span>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <div class="relative w-20">
                                        <span class="absolute left-1.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]">₹</span>
                                        <input type="number" id="combo2_ship" value="55" oninput="recalcComboModalLive(2)" class="w-full pl-4 pr-1 py-0.5 bg-white border border-gray-300 rounded text-right font-mono font-bold text-gray-800">
                                    </div>
                                    <div class="flex items-center gap-0.5">
                                        <button type="button" onclick="setModalComboShip(2, 55)" class="px-1 py-0.2 text-[8px] bg-sky-100 hover:bg-sky-200 text-sky-900 rounded font-bold border border-sky-200 cursor-pointer">0.5k: ₹55</button>
                                        <button type="button" onclick="setModalComboShip(2, 75)" class="px-1 py-0.2 text-[8px] bg-sky-100 hover:bg-sky-200 text-sky-900 rounded font-bold border border-sky-200 cursor-pointer">1k: ₹75</button>
                                    </div>
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
                                <div>
                                    <span class="text-gray-600 block">कूरियर (1.0kg स्लैब):</span>
                                    <span class="text-[8px] text-sky-700 font-bold block" id="combo4_ship_badge">₹75 + 18% GST = ₹88.5</span>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <div class="relative w-20">
                                        <span class="absolute left-1.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]">₹</span>
                                        <input type="number" id="combo4_ship" value="75" oninput="recalcComboModalLive(4)" class="w-full pl-4 pr-1 py-0.5 bg-white border border-gray-300 rounded text-right font-mono font-bold text-gray-800">
                                    </div>
                                    <div class="flex items-center gap-0.5">
                                        <button type="button" onclick="setModalComboShip(4, 55)" class="px-1 py-0.2 text-[8px] bg-sky-100 hover:bg-sky-200 text-sky-900 rounded font-bold border border-sky-200 cursor-pointer">0.5k: ₹55</button>
                                        <button type="button" onclick="setModalComboShip(4, 75)" class="px-1 py-0.2 text-[8px] bg-sky-100 hover:bg-sky-200 text-sky-900 rounded font-bold border border-sky-200 cursor-pointer">1k: ₹75</button>
                                        <button type="button" onclick="setModalComboShip(4, 112)" class="px-1 py-0.2 text-[8px] bg-sky-100 hover:bg-sky-200 text-sky-900 rounded font-bold border border-sky-200 cursor-pointer">2k: ₹112</button>
                                    </div>
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
                                <div>
                                    <span class="text-gray-600 block">कूरियर (2.0kg स्लैब):</span>
                                    <span class="text-[8px] text-sky-700 font-bold block" id="combo6_ship_badge">₹112 + 18% GST = ₹132.2</span>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <div class="relative w-20">
                                        <span class="absolute left-1.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]">₹</span>
                                        <input type="number" id="combo6_ship" value="112" oninput="recalcComboModalLive(6)" class="w-full pl-4 pr-1 py-0.5 bg-white border border-gray-300 rounded text-right font-mono font-bold text-gray-800">
                                    </div>
                                    <div class="flex items-center gap-0.5">
                                        <button type="button" onclick="setModalComboShip(6, 75)" class="px-1 py-0.2 text-[8px] bg-sky-100 hover:bg-sky-200 text-sky-900 rounded font-bold border border-sky-200 cursor-pointer">1k: ₹75</button>
                                        <button type="button" onclick="setModalComboShip(6, 112)" class="px-1 py-0.2 text-[8px] bg-sky-100 hover:bg-sky-200 text-sky-900 rounded font-bold border border-sky-200 cursor-pointer">2k: ₹112</button>
                                    </div>
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

    
    <!-- 📊 Multi-Channel Profit & Rate Comparison Modal (Amazon vs Meesho vs Flipkart vs Myntra vs Blinkit) -->
    <div id="multiChannelCompareModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-4 overflow-y-auto" onclick="closeMultiChannelCompareModal()">
        <div class="bg-white rounded-3xl max-w-5xl w-full p-5 sm:p-6 shadow-2xl border border-gray-100 space-y-4 animate-in fade-in zoom-in duration-200 my-auto" onclick="event.stopPropagation()">
            <!-- Header -->
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-r from-amber-500 via-pink-500 to-indigo-600 text-white flex items-center justify-center text-lg font-bold shadow-md">
                        <i class="fa-solid fa-chart-column"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-sm sm:text-base text-gray-900 flex items-center gap-2">
                            <span>5-मार्केटप्लेस यूनिट इकोनॉमिक्स व सेलिंग रेट्स तुलना</span>
                            <span class="text-[10px] bg-indigo-50 text-indigo-700 font-bold px-2 py-0.5 rounded-md border border-indigo-200">Real-Time Comparison</span>
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">Amazon, Meesho, Flipkart, Myntra व Blinkit का पारदर्शी खर्च, कमीशन व फाइनल मुनाफा देखें</p>
                    </div>
                </div>
                <button type="button" onclick="closeMultiChannelCompareModal()" class="text-gray-400 hover:text-gray-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Interactive Parameters Bar -->
            <div class="bg-slate-50 p-3.5 rounded-2xl border border-gray-200 grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-2 text-xs">
                <div>
                    <label class="block text-[9px] font-bold text-gray-500">खरीद लागत (₹)</label>
                    <input type="number" id="cmpInputCost" value="201" oninput="recalcMultiChannelLiveFromModal()" class="w-full px-2 py-1 bg-white border border-gray-300 rounded font-mono font-bold text-gray-800">
                </div>
                <div>
                    <label class="block text-[9px] font-bold text-gray-500">MRP (₹)</label>
                    <input type="number" id="cmpInputMrp" value="550" oninput="recalcMultiChannelLiveFromModal()" class="w-full px-2 py-1 bg-white border border-gray-300 rounded font-mono font-bold text-gray-800">
                </div>
                <div>
                    <label class="block text-[9px] font-bold text-gray-500">मुनाफा (%)</label>
                    <input type="number" id="cmpInputMargin" value="25" oninput="recalcMultiChannelLiveFromModal()" class="w-full px-2 py-1 bg-white border border-gray-300 rounded font-mono font-bold text-gray-800">
                </div>
                <div>
                    <label class="block text-[9px] font-bold text-gray-500">कूरियर (₹)</label>
                    <input type="number" id="cmpInputCourier" value="55" oninput="recalcMultiChannelLiveFromModal()" class="w-full px-2 py-1 bg-white border border-gray-300 rounded font-mono font-bold text-gray-800">
                </div>
                <div>
                    <label class="block text-[9px] font-bold text-gray-500">RTO बफ़र (₹)</label>
                    <input type="number" id="cmpInputRto" value="20" oninput="recalcMultiChannelLiveFromModal()" class="w-full px-2 py-1 bg-white border border-gray-300 rounded font-mono font-bold text-gray-800">
                </div>
                <div>
                    <label class="block text-[9px] font-bold text-gray-500">पैकिंग (₹)</label>
                    <input type="number" id="cmpInputPack" value="15" oninput="recalcMultiChannelLiveFromModal()" class="w-full px-2 py-1 bg-white border border-gray-300 rounded font-mono font-bold text-gray-800">
                </div>
                <div>
                    <label class="block text-[9px] font-bold text-gray-500">विज्ञापन (₹)</label>
                    <input type="number" id="cmpInputAds" value="10" oninput="recalcMultiChannelLiveFromModal()" class="w-full px-2 py-1 bg-white border border-gray-300 rounded font-mono font-bold text-gray-800">
                </div>
                <div>
                    <label class="block text-[9px] font-bold text-gray-500">GST %</label>
                    <input type="number" id="cmpInputGst" value="18" oninput="recalcMultiChannelLiveFromModal()" class="w-full px-2 py-1 bg-white border border-gray-300 rounded font-mono font-bold text-gray-800">
                </div>
            </div>

            <!-- 5 Platform Comparison Cards Container -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3" id="cmpCardsContainer">
                <!-- Dynamically populated by renderCompareCards -->
            </div>

            <!-- Footer -->
            <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                <span class="text-gray-500">💡 <strong>नियम:</strong> Meesho में 0% कमीशन पर सबसे कम भाव बनता है, अमेज़न व फ्लिपकार्ट पर कमीशन + फिक्स फ़ी जुड़ती है।</span>
                <button type="button" onclick="closeMultiChannelCompareModal()" class="px-5 py-2 rounded-xl bg-gray-900 text-white font-bold cursor-pointer hover:bg-gray-800 transition">
                    बंद करें (Done)
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

    <!-- ==================== 🕒 1. SYNC & PUBLISH AUDIT LOG MODAL ==================== -->
    <div id="syncAuditModal" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-xs items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-3xl w-full p-6 shadow-2xl space-y-5 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="h-9 w-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-black text-gray-900">🕒 सिंक व पब्लिश ऑडिट इतिहास (Sync Timeline)</h3>
                        <p class="text-xs text-gray-500">आपके VyaparIndia ऐप से किस प्लेटफॉर्म पर कब-कब पब्लिश व अपडेट हुआ</p>
                    </div>
                </div>
                <button type="button" onclick="closeSyncAuditModal()" class="h-8 w-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center text-sm transition cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Summary Chips -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div class="p-3 bg-amber-50 rounded-2xl border border-amber-200">
                    <span class="text-[10px] text-amber-900 font-bold block uppercase flex items-center gap-1">
                        <i class="fa-brands fa-amazon text-amber-600"></i> Amazon India
                    </span>
                    <span class="text-sm font-black text-amber-950 block mt-1">{{ $channelListingCounts['amazon'] ?? 0 }} Live Synced</span>
                    <span class="text-[10px] text-amber-800">Store: Ganesh tradersh sarangarh</span>
                </div>
                <div class="p-3 bg-pink-50 rounded-2xl border border-pink-200">
                    <span class="text-[10px] text-pink-900 font-bold block uppercase flex items-center gap-1">
                        <i class="fa-solid fa-cart-shopping text-pink-600"></i> Meesho Supplier
                    </span>
                    <span class="text-sm font-black text-pink-950 block mt-1">{{ count($pendingProducts ?? []) }} Staged & Ready</span>
                    <span class="text-[10px] text-pink-800">Store: GANESH TRADERSH (GTS)</span>
                </div>
                <div class="p-3 bg-emerald-50 rounded-2xl border border-emerald-200">
                    <span class="text-[10px] text-emerald-900 font-bold block uppercase flex items-center gap-1">
                        <i class="fa-solid fa-calendar-check text-emerald-600"></i> First & Last Sync
                    </span>
                    <span class="text-[11px] font-bold text-emerald-950 block mt-1">1st: {{ $firstPublishDate ? \Carbon\Carbon::parse($firstPublishDate)->format('d M Y, h:i A') : 'Active' }}</span>
                    <span class="text-[10px] text-emerald-800">Last: {{ $lastSyncedDate ? \Carbon\Carbon::parse($lastSyncedDate)->format('d M Y, h:i A') : 'Just now' }}</span>
                </div>
            </div>

            <!-- Sync Logs Table -->
            <div class="flex-1 overflow-y-auto border border-gray-200 rounded-2xl">
                <table class="w-full text-left text-xs divide-y divide-gray-200">
                    <thead class="bg-gray-100 text-[10px] font-black uppercase text-gray-600 sticky top-0">
                        <tr>
                            <th class="p-3">प्रोडक्ट</th>
                            <th class="p-3">प्लेटफ़ॉर्म</th>
                            <th class="p-3 text-center">स्टॉक</th>
                            <th class="p-3">सेलिंग रेट</th>
                            <th class="p-3">सबमिशन टोकन / ASIN</th>
                            <th class="p-3">सिंक तारीख व समय</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white font-mono text-[11px]">
                        @forelse($recentSyncLogs ?? [] as $log)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="p-3 font-sans font-bold text-gray-900 truncate max-w-[170px]" title="{{ $log->product->name ?? '' }}">
                                    {{ $log->product->name ?? ('Product #' . $log->product_id) }}
                                </td>
                                <td class="p-3 font-sans">
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase {{ strtolower($log->channel->channel_name ?? '') === 'amazon' ? 'bg-amber-100 text-amber-900' : 'bg-pink-100 text-pink-900' }}">
                                        {{ $log->channel->channel_name ?? 'Marketplace' }}
                                    </span>
                                </td>
                                <td class="p-3 text-center font-bold text-emerald-700">
                                    {{ $log->synced_stock ?? 3 }} pcs
                                </td>
                                <td class="p-3 font-bold text-gray-900">
                                    ₹{{ number_format($log->channel_price ?: ($log->product->price ?? 0), 2) }}
                                </td>
                                <td class="p-3 text-[10px] text-gray-500 font-mono">
                                    {{ $log->external_product_id ?: 'SYNC-OK' }}
                                </td>
                                <td class="p-3 text-[10px] text-gray-500 font-sans">
                                    {{ $log->last_synced_at ? \Carbon\Carbon::parse($log->last_synced_at)->format('d M, h:i A') : 'Recently' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-6 text-center text-gray-400 font-sans">कोई सिंक रिकॉर्ड उपलब्ध नहीं है।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <span class="text-[11px] text-gray-500">सभी रिकॉर्ड्स VyaparIndia PostgreSQL डेटाबेस में सुरक्षित संग्रहीत हैं।</span>
                <button type="button" onclick="closeSyncAuditModal()" class="px-5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition cursor-pointer">
                    बंद करें
                </button>
            </div>
        </div>
    </div>


    <!-- ==================== 📱 2. WHATSAPP VENDOR PURCHASE ORDER (PO) GENERATOR MODAL ==================== -->
    <div id="whatsAppPoModal" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-xs items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[92vh] flex flex-col">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="h-9 w-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                        <i class="fa-brands fa-whatsapp"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-black text-gray-900">📱 WhatsApp वेंडर परचेज ऑर्डर (Group PO Memo)</h3>
                        <p class="text-xs text-gray-500">ग्रुप वाइज प्रोडक्ट्स चुनें, क्वांटिटी डालें और सप्लायर या खुद को सीधा ऑर्डर भेजें</p>
                    </div>
                </div>
                <button type="button" onclick="closeWhatsAppPoModal()" class="h-8 w-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center text-sm transition cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Vendor Info & Grouping -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-[11px] font-bold text-gray-700 block mb-1">वेंडर / सप्लायर का नाम:</label>
                    <input type="text" id="poVendorName" placeholder="उदा. Supreme Faucets / Pipe Vendor" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs font-semibold focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="text-[11px] font-bold text-gray-700 block mb-1">वेंडर WhatsApp नंबर (वैकल्पिक):</label>
                    <input type="text" id="poVendorPhone" placeholder="उदा. 9876543210 (खाली छोड़ें तो खुद चुन सकते हैं)" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs font-semibold focus:bg-white focus:outline-none">
                </div>
            </div>

            <!-- Search inside items -->
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" id="poSearchInp" oninput="filterPoItems()" placeholder="ऑर्डर के लिए आइटम खोजें..." class="w-full pl-8 pr-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none">
            </div>

            <!-- Items Selection Box with Quantity -->
            <div class="flex-1 overflow-y-auto border border-gray-200 rounded-2xl divide-y divide-gray-100 max-h-60" id="poItemsListContainer">
                @foreach($pendingProducts ?? [] as $p)
                    <div class="p-2.5 flex items-center justify-between gap-3 hover:bg-gray-50/80 transition po-item-row" data-name="{{ strtolower($p->name) }}">
                        <label class="flex items-center gap-2.5 cursor-pointer flex-1 min-w-0">
                            <input type="checkbox" class="po-item-chk rounded text-emerald-600 h-4 w-4 cursor-pointer" value="{{ $p->id }}" data-name="{{ $p->name }}" data-rate="{{ $p->price }}" onchange="recalcPoSummary()">
                            <div class="min-w-0">
                                <span class="font-bold text-xs text-gray-900 block truncate">{{ $p->name }}</span>
                                <span class="text-[10px] text-gray-500 font-mono">दर: ₹{{ number_format($p->price, 2) }}</span>
                            </div>
                        </label>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <span class="text-[10px] text-gray-500 font-bold">Qty:</span>
                            <input type="number" min="1" value="20" id="po-qty-{{ $p->id }}" oninput="recalcPoSummary()" class="w-16 px-1.5 py-1 text-center font-mono font-bold text-xs bg-white border border-gray-300 rounded-lg outline-none focus:border-emerald-500">
                            <span class="text-[10px] text-gray-400">pcs</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Order Instructions Note -->
            <div>
                <label class="text-[11px] font-bold text-gray-700 block mb-1">विशेष निर्देश / नोट (Transport Note):</label>
                <input type="text" id="poOrderNote" placeholder="उदा. कृपया सारंगढ़ ट्रांसपोर्ट से बिल्टी लगाएँ।" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-300 rounded-xl text-xs font-medium focus:bg-white focus:outline-none">
            </div>

            <!-- Summary & Send Button -->
            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between gap-3 flex-wrap text-xs">
                <div>
                    <span class="font-black text-emerald-950 block" id="poSelectedCountText">0 प्रोडक्ट्स चुने गए</span>
                    <span class="text-[11px] text-emerald-800" id="poTotalPiecesAndAmount">कुल: 0 पीस | ₹0.00</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="sendWhatsAppPurchaseOrder(false)" class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 active:scale-95 cursor-pointer">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>📱 WhatsApp पर भेजें</span>
                    </button>
                    <button type="button" onclick="sendWhatsAppPurchaseOrder(true)" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-user-check text-xs"></i>
                        <span>खुद को भेजें</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

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

        @if(session('just_published'))
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'सफलतापूर्वक लाइव सिंक हो गया!',
                    html: `
                        <div class="space-y-3 text-left text-xs font-sans">
                            <p class="text-gray-800 font-bold leading-relaxed">{{ session('success') }}</p>
                            <p class="text-gray-600">क्या आप Amazon और Meesho सेलर पोर्टल में लाइव कैटलॉग देखना चाहते हैं?</p>
                            <div class="grid grid-cols-2 gap-2 pt-2">
                                <a href="https://sellercentral.amazon.in/inventory" target="_blank" class="p-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 border border-amber-300 text-amber-950 font-black text-center flex items-center justify-center gap-1.5 transition">
                                    <i class="fa-brands fa-amazon text-amber-600"></i> Amazon Inventory
                                </a>
                                <a href="https://supplier.meesho.com/panel/v3/new/catalogues" target="_blank" class="p-2.5 rounded-xl bg-pink-50 hover:bg-pink-100 border border-pink-300 text-pink-950 font-black text-center flex items-center justify-center gap-1.5 transition">
                                    <i class="fa-solid fa-cart-shopping text-pink-600"></i> Meesho Catalogues
                                </a>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: '🌐 दोनों पोर्टल नए Chrome टैब में खोलें',
                    cancelButtonText: 'यहीं रहें (Done)',
                    confirmButtonColor: '#4f46e5'
                }).then((res) => {
                    if (res.isConfirmed) {
                        openMarketplaceSellerPortals();
                    }
                });
            }
        });
        @endif


        // --- 🕒 Sync Audit Modal Functions ---
        function openSyncAuditModal() {
            const m = document.getElementById('syncAuditModal');
            if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
        }
        function closeSyncAuditModal() {
            const m = document.getElementById('syncAuditModal');
            if (m) { m.classList.add('hidden'); m.classList.remove('flex'); }
        }

        // --- 📱 WhatsApp PO Generator Functions ---
        function openWhatsAppPoModal() {
            const m = document.getElementById('whatsAppPoModal');
            if (m) {
                m.classList.remove('hidden'); m.classList.add('flex');
                recalcPoSummary();
            }
        }
        function closeWhatsAppPoModal() {
            const m = document.getElementById('whatsAppPoModal');
            if (m) { m.classList.add('hidden'); m.classList.remove('flex'); }
        }

        function filterPoItems() {
            const q = (document.getElementById('poSearchInp')?.value || '').toLowerCase().trim();
            document.querySelectorAll('.po-item-row').forEach(row => {
                const name = row.getAttribute('data-name') || '';
                row.style.display = (!q || name.includes(q)) ? 'flex' : 'none';
            });
        }

        function recalcPoSummary() {
            const checkedBoxes = document.querySelectorAll('.po-item-chk:checked');
            let totalQty = 0;
            let totalAmt = 0;
            checkedBoxes.forEach(chk => {
                const id = chk.value;
                const qty = parseInt(document.getElementById(`po-qty-${id}`)?.value, 10) || 0;
                const rate = parseFloat(chk.getAttribute('data-rate')) || 0;
                totalQty += qty;
                totalAmt += (qty * rate);
            });

            const countEl = document.getElementById('poSelectedCountText');
            const detailEl = document.getElementById('poTotalPiecesAndAmount');
            if (countEl) countEl.textContent = `${checkedBoxes.length} प्रोडक्ट्स चुने गए`;
            if (detailEl) detailEl.textContent = `कुल: ${totalQty} पीस | अनुमानित रकम: ₹${totalAmt.toLocaleString('en-IN')}`;
        }

        function sendWhatsAppPurchaseOrder(sendToSelf = false) {
            const checkedBoxes = document.querySelectorAll('.po-item-chk:checked');
            if (checkedBoxes.length === 0) {
                alert('कृपया कम से कम 1 प्रोडक्ट चुनें!');
                return;
            }

            const vendorName = document.getElementById('poVendorName')?.value.trim() || 'सप्लायर / वेंडर';
            const vendorPhone = document.getElementById('poVendorPhone')?.value.trim().replace(/\D/g, '');
            const orderNote = document.getElementById('poOrderNote')?.value.trim();

            let lines = [];
            lines.push(`📦 *नया परचेज ऑर्डर (Purchase Order)*`);
            lines.push(`🏬 *दुकान:* Ganesh Tradersh`);
            lines.push(`👤 *वेंडर:* ${vendorName}`);
            lines.push(`📅 *तारीख:* ${new Date().toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' })}`);
            lines.push(``);
            lines.push(`📋 *आइटम लिस्ट:*`);

            let totalQty = 0;
            let totalAmt = 0;
            let idx = 1;

            checkedBoxes.forEach(chk => {
                const id = chk.value;
                const name = chk.getAttribute('data-name');
                const qty = parseInt(document.getElementById(`po-qty-${id}`)?.value, 10) || 0;
                const rate = parseFloat(chk.getAttribute('data-rate')) || 0;
                totalQty += qty;
                totalAmt += (qty * rate);
                lines.push(`${idx}. *${name}* - *${qty} pcs* (दर: ₹${Math.round(rate)})`);
                idx++;
            });

            lines.push(``);
            lines.push(`📊 *कुल आइटम्स:* ${checkedBoxes.length}`);
            lines.push(`📦 *कुल क्वांटिटी:* ${totalQty} pcs`);
            lines.push(`💰 *अनुमानित कुल रकम:* ₹${totalAmt.toLocaleString('en-IN')}`);
            if (orderNote) {
                lines.push(`📝 *नोट:* ${orderNote}`);
            }
            lines.push(``);
            lines.push(`कृपया माल जल्द से जल्द डिस्पैच करें और बिल्टी/बिल भेजें। धन्यवाद!`);

            const msgText = encodeURIComponent(lines.join('\n'));
            let waUrl = `https://api.whatsapp.com/send?text=${msgText}`;

            if (!sendToSelf && vendorPhone && vendorPhone.length >= 10) {
                const phoneWithCode = vendorPhone.startsWith('91') ? vendorPhone : ('91' + vendorPhone);
                waUrl = `https://api.whatsapp.com/send?phone=${phoneWithCode}&text=${msgText}`;
            }

            window.open(waUrl, '_blank');
        }


    function recalcRowMrpAndLessToCost(prodId) {
        const mrpInp = document.getElementById(`row-mrp-${prodId}`);
        const lessInp = document.getElementById(`row-less-${prodId}`);
        const costInp = document.getElementById(`row-cost-${prodId}`);
        const mrp = parseFloat(mrpInp?.value) || 0;
        const less = parseFloat(lessInp?.value) || 0;
        if (mrp > 0 && less > 0) {
            const cost = Math.round((mrp * (1 - (less / 100))) * 100) / 100;
            if (costInp) costInp.value = cost.toFixed(2);
            const cardCost = document.getElementById(`card-cost-${prodId}`);
            if (cardCost) cardCost.value = cost.toFixed(2);
        }
        const cardMrp = document.getElementById(`card-mrp-${prodId}`);
        if (cardMrp && mrpInp) cardMrp.value = mrpInp.value;
        if (lessInp) lessInp.setAttribute('data-mrp', mrp);
    }

    function recalcCardMrpAndLessToCost(prodId) {
        const cardMrp = document.getElementById(`card-mrp-${prodId}`);
        const cardLess = document.getElementById(`card-less-${prodId}`);
        const cardCost = document.getElementById(`card-cost-${prodId}`);
        const mrp = parseFloat(cardMrp?.value) || 0;
        const less = parseFloat(cardLess?.value) || 0;
        if (mrp > 0 && less > 0) {
            const cost = Math.round((mrp * (1 - (less / 100))) * 100) / 100;
            if (cardCost) cardCost.value = cost.toFixed(2);
            const rowCost = document.getElementById(`row-cost-${prodId}`);
            if (rowCost) rowCost.value = cost.toFixed(2);
        }
        const rowMrp = document.getElementById(`row-mrp-${prodId}`);
        if (rowMrp && cardMrp) rowMrp.value = cardMrp.value;
        if (cardLess) cardLess.setAttribute('data-mrp', mrp);
    }

</script>



    <!-- 🚚 PLATFORM COURIER RATE MASTER MODAL (AMAZON, MEESHO, FLIPKART, DIRECT) -->
    <div id="shippingRateManagerModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-4 overflow-y-auto" onclick="closeShippingRateManagerModal()">
        <div class="bg-white rounded-3xl max-w-4xl w-full p-5 sm:p-6 shadow-2xl border border-gray-100 space-y-4 animate-in fade-in zoom-in duration-200 my-auto" onclick="event.stopPropagation()">
            <!-- Header -->
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-sky-600 text-white flex items-center justify-center text-lg font-bold shadow-md">
                        <i class="fa-solid fa-truck-ramp-box"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-black text-sm sm:text-base text-gray-900">प्लेटफ़ॉर्म-वार कूरियर दर मास्टर (Platform Courier Master)</h3>
                            <span class="text-[10px] bg-sky-50 text-sky-700 font-bold px-2 py-0.5 rounded-md border border-sky-200">100% Database Driven</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">यहाँ आप Amazon, Meesho, Flipkart आदि के वजन अनुसार कूरियर रेट्स बदल सकते हैं। कोई भी रेट हार्डकोड नहीं रहेगा।</p>
                    </div>
                </div>
                <button type="button" onclick="closeShippingRateManagerModal()" class="text-gray-400 hover:text-gray-600 p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Platform Tabs -->
            <div class="flex items-center gap-2 border-b border-gray-200 pb-2">
                <button type="button" onclick="switchShippingPlatformTab('amazon')" id="tabBtnShip_amazon" class="px-3.5 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-1.5 bg-amber-500 text-slate-950 shadow-xs cursor-pointer">
                    <span>🟠 Amazon Easy Ship</span>
                </button>
                <button type="button" onclick="switchShippingPlatformTab('meesho')" id="tabBtnShip_meesho" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 bg-gray-100 text-gray-700 hover:text-gray-900 cursor-pointer">
                    <span>🌸 Meesho Logistics</span>
                </button>
                <button type="button" onclick="switchShippingPlatformTab('flipkart')" id="tabBtnShip_flipkart" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 bg-gray-100 text-gray-700 hover:text-gray-900 cursor-pointer">
                    <span>⚡ Flipkart Ekart</span>
                </button>
                <button type="button" onclick="switchShippingPlatformTab('direct')" id="tabBtnShip_direct" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 bg-gray-100 text-gray-700 hover:text-gray-900 cursor-pointer">
                    <span>📦 Direct / Self Ship</span>
                </button>
            </div>

            <!-- Rates Tables Container -->
            <div id="shippingMasterTablesContainer" class="space-y-3">
                <!-- Dynamically populated or rendered -->
            </div>

            <!-- Footer & Actions -->
            <div class="pt-3 flex items-center justify-between border-t border-gray-100 flex-wrap gap-2">
                <span class="text-xs text-gray-500">
                    *सभी कूरियर दरों पर सरकार का <strong>18% GST</strong> जुड़ता है, जिसका लाइव ब्रेकडाउन टेबल में दिखता है।
                </span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeShippingRateManagerModal()" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition cursor-pointer">
                        रद्द करें
                    </button>
                    <button type="button" id="btnSaveShippingMaster" onclick="savePlatformShippingRatesMaster()" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-700 hover:to-indigo-700 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer active:scale-95">
                        <i class="fa-solid fa-floppy-disk text-sky-200"></i>
                        <span>💾 कूरियर दरें सेव करें (Save Rates)</span>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- ==================== 📝 1. SINGLE PRODUCT AMAZON LISTING CONTENT MODAL ==================== -->
    <div id="listingContentModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-4 overflow-y-auto" onclick="closeListingContentModal()">
        <div class="bg-white rounded-3xl max-w-3xl w-full p-5 sm:p-6 shadow-2xl border border-gray-100 space-y-4 animate-in fade-in zoom-in duration-200 my-auto max-h-[92vh] flex flex-col" onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-600 flex items-center justify-center text-xl font-bold shrink-0">
                        <i class="fa-brands fa-amazon"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-black text-sm sm:text-base text-gray-900">अमेज़न लिस्टिंग कंटेंट मैनेजर (SEO & Keywords)</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800" id="listingModalCategoryBadge">Plumbing</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5" id="listingModalHeaderSub">टाइटल, 5 बुलेट पॉइंट्स, विस्तृत विवरण और 250 बाइट्स जेनेरिक सर्च कीवर्ड्स</p>
                    </div>
                </div>
                <button type="button" onclick="closeListingContentModal()" class="text-gray-400 hover:text-gray-600 p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Product Quick Overview Strip -->
            <div class="flex items-center justify-between bg-amber-50/70 p-2.5 rounded-2xl border border-amber-200 text-xs shrink-0 flex-wrap gap-2">
                <div class="flex items-center gap-2.5">
                    <img id="listingModalThumb" src="" class="w-10 h-10 object-contain rounded-lg border border-amber-200 bg-white p-0.5 hidden">
                    <div>
                        <span class="text-[10px] text-amber-800 font-bold block">मूल नाम:</span>
                        <strong class="text-gray-900 text-xs" id="listingModalOriginalName">Product Name</strong>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 rounded-xl bg-white border border-amber-300 text-[10px] font-bold text-gray-700" id="listingModalSizesBadge">15mm (1/2")</span>
                </div>
            </div>

            <!-- Scrollable Content Fields -->
            <div class="overflow-y-auto flex-1 pr-1 space-y-3.5 text-xs">
                <!-- 1. Amazon SEO Title -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="font-black text-gray-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-heading text-amber-600"></i>
                            <span>1. अमेज़न हाई-कन्वर्टिंग SEO टाइटल (Product Title):</span>
                        </label>
                        <span class="text-[10px] text-gray-400 font-mono" id="listingTitleLength">0 chars</span>
                    </div>
                    <input type="text" id="listingModalTitle" oninput="document.getElementById('listingTitleLength').textContent = this.value.length + ' chars'" placeholder="उदा. Heavy Duty PTMT Bib Cock Water Tap (15mm / 1/2 Inch) with Wall Flange..." class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl font-bold text-gray-900 focus:bg-white focus:border-amber-500 focus:outline-none text-xs">
                </div>

                <!-- 2. The 5 Amazon Bullet Points -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="font-black text-gray-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-list-check text-indigo-600"></i>
                            <span>2. मुख्य 5 बुलेट पॉइंट्स (About this item):</span>
                        </label>
                        <span class="text-[10px] text-indigo-600 font-semibold">अमेज़न सेलर सेंट्रल मानक</span>
                    </div>

                    <!-- Bullet 1: Material & Build -->
                    <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-200 space-y-1">
                        <span class="text-[10px] font-black text-indigo-950 block">• बुलेट 1 (मटीरियल व मजबूती / Material & Grade):</span>
                        <input type="text" id="listingModalBullet1" placeholder="उदा. 100% Virgin Food-Grade PTMT Polymer (Unbreakable & Corrosion-Free)" class="w-full px-2.5 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-800 focus:border-indigo-500 focus:outline-none">
                    </div>

                    <!-- Bullet 2: Leakproof / Mechanism -->
                    <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-200 space-y-1">
                        <span class="text-[10px] font-black text-indigo-950 block">• बुलेट 2 (लीक-प्रूफ व स्पिंडल टेस्टिंग / Leakproof Testing):</span>
                        <input type="text" id="listingModalBullet2" placeholder="उदा. Heavy-duty ceramic disc quarter-turn spindle tested for 5,00,000+ on/off cycles" class="w-full px-2.5 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-800 focus:border-indigo-500 focus:outline-none">
                    </div>

                    <!-- Bullet 3: Flow & Technology -->
                    <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-200 space-y-1">
                        <span class="text-[10px] font-black text-indigo-950 block">• बुलेट 3 (वाटर फ्लो व एरेटर / Flow & Aerator):</span>
                        <input type="text" id="listingModalBullet3" placeholder="उदा. German-engineered foam flow aerator saves water while ensuring smooth splash-free stream" class="w-full px-2.5 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-800 focus:border-indigo-500 focus:outline-none">
                    </div>

                    <!-- Bullet 4: Fitting Size & Specs -->
                    <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-200 space-y-1">
                        <span class="text-[10px] font-black text-indigo-950 block">• बुलेट 4 (साइज व थ्रेड फिटिंग / Standard Fitting Size):</span>
                        <input type="text" id="listingModalBullet4" placeholder="उदा. Standard 15mm (1/2 Inch BSP Thread) suitable for all Indian bathroom connections" class="w-full px-2.5 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-800 focus:border-indigo-500 focus:outline-none">
                    </div>

                    <!-- Bullet 5: Package Contents & Warranty -->
                    <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-200 space-y-1">
                        <span class="text-[10px] font-black text-indigo-950 block">• बुलेट 5 (पैकेज सामग्री व वारंटी / Box Contents & Warranty):</span>
                        <input type="text" id="listingModalBullet5" placeholder="उदा. 1 Unit Tap + 1 Heavy Matching Wall Flange (Quality Inspected & Leak Tested)" class="w-full px-2.5 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-800 focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <!-- 3. Product Description -->
                <div>
                    <label class="font-black text-gray-900 block mb-1">3. विस्तृत उत्पाद विवरण (Product Description):</label>
                    <textarea id="listingModalDesc" rows="3" placeholder="उत्पाद का उपयोग, फिटिंग का तरीका, और क्वालिटी गारंटी विवरण..." class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs font-medium text-gray-800 focus:bg-white focus:border-amber-500 focus:outline-none leading-relaxed"></textarea>
                </div>

                <!-- 4. Generic Backend Search Keywords (250 bytes limit) -->
                <div class="p-3 bg-purple-50/70 rounded-2xl border border-purple-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="font-black text-purple-950 flex items-center gap-1.5">
                            <i class="fa-solid fa-tags text-purple-600"></i>
                            <span>4. जेनेरिक वर्ड्स / सर्च कीवर्ड्स (Generic Search Terms):</span>
                        </label>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-white border border-purple-300" id="listingKeywordsByteBadge">
                            0 / 249 Bytes
                        </span>
                    </div>
                    <p class="text-[11px] text-purple-800 leading-tight">
                        *अमेज़न पर कॉमा (,) नहीं लगाना होता, केवल स्पेस देकर शब्द लिखें। ग्राहक जिस नाम से खोजते हैं (उदा. <code class="bg-white px-1 rounded">nal, bib tap, 15mm tap</code>) वही शब्द डालें।
                    </p>
                    <textarea id="listingModalKeywords" oninput="updateListingKeywordByteCount()" rows="2" placeholder="bibcock bib tap bathroom water tap nal sink cock ptmt tap 15mm half inch angle cock wall flange plumbing hardware wash basin" class="w-full px-3 py-2 bg-white border border-purple-300 rounded-xl text-xs font-mono font-bold text-purple-950 focus:border-purple-600 focus:outline-none"></textarea>
                </div>

                <!-- 5. User Feedback / AI Guidance Input -->
                <div class="p-3 bg-indigo-50/80 rounded-2xl border border-indigo-200 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="font-black text-indigo-950 flex items-center gap-1 text-[11px]">
                            <i class="fa-solid fa-wand-magic-sparkles text-indigo-600"></i>
                            <span>AI को निर्देश / अतिरिक्त कीवर्ड्स दें (User Feedback):</span>
                        </label>
                        <span class="text-[10px] text-indigo-600">वैकल्पिक</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="text" id="listingModalFeedback" placeholder="उदा. 'nal और pani ka tap कीवर्ड्स जोड़ो', '3 साल की वारंटी जोड़ो'" class="flex-1 px-3 py-1.5 bg-white border border-indigo-300 rounded-xl text-xs text-indigo-950 focus:outline-none">
                        <button type="button" id="btnAiRegenSingle" onclick="aiGenerateSingleListing()" class="px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs transition flex items-center gap-1.5 cursor-pointer shadow-xs active:scale-95 shrink-0">
                            <i class="fa-solid fa-bolt-lightning text-amber-300"></i>
                            <span>✨ AI से लिखें</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="pt-3 border-t border-gray-100 flex items-center justify-between shrink-0 flex-wrap gap-2">
                <button type="button" onclick="closeListingContentModal()" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition cursor-pointer">
                    रद्द करें
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" id="btnSaveListingSingle" onclick="saveSingleListingContent()" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer active:scale-95">
                        <i class="fa-solid fa-floppy-disk text-amber-200"></i>
                        <span>💾 सुरक्षित सेव करें (Save Listing)</span>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- ==================== 🤖 2. BULK AI LISTING GENERATOR & REVIEW DRAWER ==================== -->
    <div id="bulkListingAiModal" class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-4 overflow-y-auto" onclick="closeBulkListingAiModal()">
        <div class="bg-white rounded-3xl max-w-5xl w-full p-5 sm:p-6 shadow-2xl border border-gray-100 space-y-4 animate-in fade-in zoom-in duration-200 my-auto max-h-[94vh] flex flex-col" onclick="event.stopPropagation()">
            <!-- Header -->
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-600 to-indigo-600 text-white flex items-center justify-center text-xl font-bold shadow-md shrink-0">
                        <i class="fa-solid fa-brain"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-black text-base text-gray-900">🤖 AI अमेज़न लिस्टिंग कॉपीराइटर (Bulk Amazon Copy Engine)</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-purple-100 text-purple-900" id="bulkListingTotalBadge">19 Products</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">
                            AI आपकी लिस्ट के नाम, फोटो, साइज और मटीरियल को पढ़कर सभी प्रोडक्ट्स के लिए <strong>टाइटल, 5 बुलेट पॉइंट्स, विवरण और 250 बाइट्स जेनेरिक कीवर्ड्स</strong> तैयार करता है।
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeBulkListingAiModal()" class="text-gray-400 hover:text-gray-600 p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Interactive User Feedback Prompt Bar -->
            <div class="p-3.5 bg-gradient-to-r from-purple-50 via-indigo-50 to-purple-50 rounded-2xl border border-purple-200 shrink-0 space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-black text-purple-950 flex items-center gap-1.5">
                        <i class="fa-solid fa-comments text-purple-600"></i>
                        <span>AI को निर्देश / कस्टम फीडबैक (Prompt the AI):</span>
                    </span>
                    <span class="text-[10px] text-purple-700">सभी 19 प्रोडक्ट्स में एक साथ लागू होगा</span>
                </div>
                <div class="flex items-center gap-2">
                    <input type="text" id="bulkListingUserPrompt" placeholder="उदा. 'सभी में 3 साल की लीकेज वारंटी जोड़ो', 'nal, kitchen sink, bathroom fittings कीवर्ड्स जोड़ो'" class="flex-1 px-3 py-2 bg-white border border-purple-300 rounded-xl text-xs font-semibold text-purple-950 focus:border-purple-600 focus:outline-none shadow-2xs">
                    <button type="button" id="btnRunBulkAiGen" onclick="runBulkAiListingGeneration()" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-black text-xs transition flex items-center gap-1.5 cursor-pointer shadow-md active:scale-95 shrink-0">
                        <i class="fa-solid fa-bolt-lightning text-amber-300"></i>
                        <span>⚡ AI जनरेट करें</span>
                    </button>
                </div>
            </div>

            <!-- Loading Spinner State -->
            <div id="bulkListingLoadingState" class="hidden py-16 text-center space-y-3">
                <div class="inline-block animate-spin text-purple-600 text-3xl">
                    <i class="fa-solid fa-circle-notch"></i>
                </div>
                <h4 class="font-black text-sm text-gray-900">AI सभी प्रोडक्ट्स के नाम, साइज और फोटो का विश्लेषण कर रहा है...</h4>
                <p class="text-xs text-gray-500 max-w-md mx-auto">
                    अमेज़न A9 एल्गोरिदम के अनुसार 5 बुलेट पॉइंट्स, 250 बाइट्स बैकएंड कीवर्ड्स और आकर्षक टाइटल्स लिखे जा रहे हैं।
                </p>
            </div>

            <!-- Results Accordion/Cards Container -->
            <div id="bulkListingResultsContainer" class="overflow-y-auto flex-1 pr-1 space-y-3 text-xs">
                <!-- Dynamically populated via JS -->
            </div>

            <!-- Bottom Actions Bar -->
            <div id="bulkListingFooterBar" class="pt-3 border-t border-gray-100 flex items-center justify-between shrink-0 flex-wrap gap-2 bg-gray-50/80 -mx-6 -mb-6 p-4 px-6 rounded-b-3xl">
                <div class="text-xs text-gray-600">
                    <span class="font-bold text-gray-900" id="bulkListingReadyCount">0</span> प्रोडक्ट्स तैयार हैं। रेट्स और इन्वेंट्री 100% सुरक्षित रहेंगी।
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeBulkListingAiModal()" class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold text-xs transition cursor-pointer">
                        रद्द करें
                    </button>
                    <button type="button" id="btnApplyAllBulkListings" onclick="applyBulkListingContentToDatabase()" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs shadow-md transition flex items-center gap-2 cursor-pointer active:scale-95">
                        <i class="fa-solid fa-cloud-arrow-up text-amber-300"></i>
                        <span>🚀 सभी 19 लिस्टिंग्स 1-क्लिक में लागू करें (Apply to All)</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== 🛠️ JAVASCRIPT LOGIC FOR LISTING CONTENT ==================== -->
    <script>
        let activeListingProductId = null;
        let bulkGeneratedListingsData = [];

        // Open Single Product Listing Modal
        async function openListingContentModal(prodId, prodName) {
            activeListingProductId = prodId;
            document.getElementById('listingModalOriginalName').textContent = prodName || `Product #${prodId}`;
            document.getElementById('listingContentModal').classList.remove('hidden');
            document.getElementById('listingContentModal').classList.add('flex');

            try {
                const res = await fetch(`/seller/channels/listing-content/${prodId}`);
                const json = await res.json();
                if (json.success && json.data) {
                    const d = json.data;
                    document.getElementById('listingModalTitle').value = d.title || prodName;
                    document.getElementById('listingTitleLength').textContent = (d.title || prodName).length + ' chars';
                    document.getElementById('listingModalDesc').value = d.description || '';
                    document.getElementById('listingModalCategoryBadge').textContent = d.category || 'Plumbing';

                    const bullets = d.bullet_points || [];
                    for (let i = 1; i <= 5; i++) {
                        const el = document.getElementById(`listingModalBullet${i}`);
                        if (el) el.value = bullets[i - 1] || '';
                    }

                    document.getElementById('listingModalKeywords').value = d.generic_keywords || '';
                    updateListingKeywordByteCount();

                    const thumb = document.getElementById('listingModalThumb');
                    if (d.image) {
                        thumb.src = d.image;
                        thumb.classList.remove('hidden');
                    } else {
                        thumb.classList.add('hidden');
                    }

                    const sizes = d.sizes || [];
                    document.getElementById('listingModalSizesBadge').textContent = sizes.length ? sizes.join(', ') : '15mm (1/2")';
                }
            } catch (err) {
                console.error('Failed to load listing content:', err);
            }
        }

        function closeListingContentModal() {
            document.getElementById('listingContentModal').classList.add('hidden');
            document.getElementById('listingContentModal').classList.remove('flex');
            activeListingProductId = null;
        }

        function updateListingKeywordByteCount() {
            const kw = document.getElementById('listingModalKeywords')?.value || '';
            const bytes = new TextEncoder().encode(kw).length;
            const badge = document.getElementById('listingKeywordsByteBadge');
            if (badge) {
                badge.textContent = `${bytes} / 249 Bytes`;
                if (bytes > 249) {
                    badge.className = "px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-rose-100 text-rose-800 border border-rose-300 animate-pulse";
                } else {
                    badge.className = "px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-white text-purple-950 border border-purple-300";
                }
            }
        }

        // AI Generate for Single Product
        async function aiGenerateSingleListing() {
            if (!activeListingProductId) return;
            const btn = document.getElementById('btnAiRegenSingle');
            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> जनरेट हो रहा है...`;

            const feedback = document.getElementById('listingModalFeedback')?.value || '';
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const res = await fetch(`/seller/channels/listing-content/${activeListingProductId}/ai-generate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ user_feedback: feedback })
                });
                const json = await res.json();
                if (json.success && json.data) {
                    const d = json.data;
                    document.getElementById('listingModalTitle').value = d.title;
                    document.getElementById('listingTitleLength').textContent = d.title.length + ' chars';
                    document.getElementById('listingModalDesc').value = d.description;

                    const bullets = d.bullet_points || [];
                    for (let i = 1; i <= 5; i++) {
                        const el = document.getElementById(`listingModalBullet${i}`);
                        if (el) el.value = bullets[i - 1] || '';
                    }

                    document.getElementById('listingModalKeywords').value = d.generic_keywords || '';
                    updateListingKeywordByteCount();

                    showChannelToast('✨ AI ने अमेज़न मानक के अनुसार 5 बुलेट पॉइंट्स व कीवर्ड्स तैयार कर दिए!');
                }
            } catch (err) {
                alert('AI जनरेट करने में त्रुटि हुई। कृपया दोबारा प्रयास करें।');
            } finally {
                btn.disabled = false;
                btn.innerHTML = origHtml;
            }
        }

        // Save Single Product Listing Content
        async function saveSingleListingContent() {
            if (!activeListingProductId) return;
            const btn = document.getElementById('btnSaveListingSingle');
            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> सेव हो रहा है...`;

            const title = document.getElementById('listingModalTitle')?.value.trim();
            const desc = document.getElementById('listingModalDesc')?.value.trim();
            const keywords = document.getElementById('listingModalKeywords')?.value.trim();
            const bullets = [];
            for (let i = 1; i <= 5; i++) {
                const bVal = document.getElementById(`listingModalBullet${i}`)?.value.trim();
                if (bVal) bullets.push(bVal);
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const res = await fetch(`/seller/channels/listing-content/${activeListingProductId}/save`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        title: title,
                        description: desc,
                        bullet_points: bullets,
                        generic_keywords: keywords
                    })
                });
                const json = await res.json();
                if (json.success) {
                    const rowNameEl = document.getElementById(`row-name-${activeListingProductId}`);
                    if (rowNameEl && title) rowNameEl.textContent = title;
                    showChannelToast(json.message || 'कंटेंट सुरक्षित सेव हो गया!');
                    closeListingContentModal();
                } else {
                    alert(json.message || 'सेव करने में समस्या हुई।');
                }
            } catch (err) {
                alert('नेटवर्क त्रुटि। कृपया दोबारा प्रयास करें।');
            } finally {
                btn.disabled = false;
                btn.innerHTML = origHtml;
            }
        }

        // Open Bulk AI Listing Modal
        function openBulkListingAiModal() {
            document.getElementById('bulkListingAiModal').classList.remove('hidden');
            document.getElementById('bulkListingAiModal').classList.add('flex');
            if (!bulkGeneratedListingsData.length) {
                runBulkAiListingGeneration();
            }
        }

        function closeBulkListingAiModal() {
            document.getElementById('bulkListingAiModal').classList.add('hidden');
            document.getElementById('bulkListingAiModal').classList.remove('flex');
        }

        // Run Bulk AI Generation
        async function runBulkAiListingGeneration() {
            const loadingBox = document.getElementById('bulkListingLoadingState');
            const resultsBox = document.getElementById('bulkListingResultsContainer');
            const btn = document.getElementById('btnRunBulkAiGen');

            loadingBox.classList.remove('hidden');
            resultsBox.classList.add('hidden');
            btn.disabled = true;
            btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> तैयार हो रहा है...`;

            const feedback = document.getElementById('bulkListingUserPrompt')?.value || '';
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const res = await fetch('/seller/channels/listing-content/bulk-generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ user_feedback: feedback })
                });
                const json = await res.json();
                if (json.success && Array.isArray(json.items)) {
                    bulkGeneratedListingsData = json.items;
                    renderBulkListingCards(json.items);
                } else {
                    alert(json.message || 'त्रुटि हुई।');
                }
            } catch (err) {
                alert('नेटवर्क समस्या। कृपया पुनः प्रयास करें।');
            } finally {
                loadingBox.classList.add('hidden');
                resultsBox.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = `<span>⚡ AI री-जेनरेट करें</span>`;
            }
        }

        // Render Bulk Listing Cards
        function renderBulkListingCards(items) {
            const container = document.getElementById('bulkListingResultsContainer');
            container.innerHTML = '';
            document.getElementById('bulkListingTotalBadge').textContent = `${items.length} Products Ready`;
            document.getElementById('bulkListingReadyCount').textContent = items.length;

            items.forEach((item, idx) => {
                const card = document.createElement('div');
                card.className = "bg-white p-4 rounded-2xl border border-gray-200 hover:border-purple-300 transition space-y-3 shadow-2xs";

                const bulletsHtml = (item.bullet_points || []).map((b, i) => `
                    <div class="flex items-start gap-1.5 text-[11px] text-gray-700">
                        <span class="font-bold text-indigo-700 shrink-0">•</span>
                        <span class="leading-tight">${b}</span>
                    </div>
                `).join('');

                card.innerHTML = `
                    <div class="flex items-start justify-between gap-3 pb-2 border-b border-gray-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-lg bg-purple-100 text-purple-900 flex items-center justify-center font-bold text-xs shrink-0">${idx + 1}</span>
                            <div>
                                <span class="text-[10px] text-gray-400 block">मूल नाम: ${item.original_name}</span>
                                <h4 class="font-black text-xs text-gray-900 leading-snug">${item.title}</h4>
                            </div>
                        </div>
                        <div class="shrink-0 flex items-center gap-1.5">
                            <span class="px-2 py-0.5 rounded-lg bg-gray-100 text-gray-700 text-[10px] font-mono font-bold">HSN: ${item.hsn}</span>
                            <span class="px-2 py-0.5 rounded-lg bg-purple-50 text-purple-800 text-[10px] font-bold border border-purple-200">${item.material}</span>
                        </div>
                    </div>

                    <!-- 5 Bullets -->
                    <div class="bg-gray-50/70 p-3 rounded-xl border border-gray-200 space-y-1">
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-wider block">5 मुख्य बुलेट पॉइंट्स (About this item):</span>
                        ${bulletsHtml}
                    </div>

                    <!-- Generic Keywords Strip -->
                    <div class="flex items-center justify-between bg-purple-50/70 p-2 rounded-xl border border-purple-200 text-[10px] flex-wrap gap-1">
                        <div class="flex items-center gap-1 text-purple-900 font-bold">
                            <i class="fa-solid fa-tags text-purple-600"></i>
                            <span>सर्च कीवर्ड्स:</span>
                            <span class="font-mono text-purple-950 font-normal truncate max-w-xl">${item.generic_keywords}</span>
                        </div>
                        <span class="font-mono text-[9px] font-bold text-purple-700 bg-white px-2 py-0.5 rounded-md border border-purple-200">${item.byte_length} Bytes</span>
                    </div>
                `;
                container.appendChild(card);
            });
        }

        // Apply All Bulk Listings to Database
        async function applyBulkListingContentToDatabase() {
            if (!bulkGeneratedListingsData.length) {
                alert('कोई लिस्टिंग डेटा उपलब्ध नहीं है।');
                return;
            }

            const btn = document.getElementById('btnApplyAllBulkListings');
            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> सभी 19 प्रोडक्ट्स में लागू हो रहा है...`;

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const res = await fetch('/seller/channels/listing-content/bulk-apply', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ items: bulkGeneratedListingsData })
                });
                const json = await res.json();
                if (json.success) {
                    showChannelToast(json.message);
                    closeBulkListingAiModal();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                } else {
                    alert(json.message || 'लागू करने में समस्या हुई।');
                }
            } catch (err) {
                alert('नेटवर्क समस्या। कृपया पुनः प्रयास करें।');
            } finally {
                btn.disabled = false;
                btn.innerHTML = origHtml;
            }
        }
    </script>


    <!-- ==================== 📋 3. AMAZON 120-FIELD READINESS & ATTRIBUTE MAPPING MODAL ==================== -->
    <div id="amazonReadinessModal" class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-4 overflow-y-auto" onclick="closeAmazonReadinessModal()">
        <div class="bg-white rounded-3xl max-w-4xl w-full p-5 sm:p-6 shadow-2xl border border-gray-100 space-y-4 animate-in fade-in zoom-in duration-200 my-auto max-h-[92vh] flex flex-col text-xs" onclick="event.stopPropagation()">
            <!-- Header -->
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-600 flex items-center justify-center text-xl font-bold shadow-xs shrink-0">
                        <i class="fa-brands fa-amazon"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-black text-base text-gray-900">अमेज़न 120-फ़ील्ड रेडीनेस व एट्रिब्यूट मैपर</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">92% Ready (18/20 फ़ील्ड्स तैयार)</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">
                            अमेज़न फ़्लैट फ़ाइल (Excel Template) के 120 कॉलम में से वास्तव में आवश्यक 20 फ़ील्ड्स की लाइव ऑडिट व ऑटो-मैपिंग
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeAmazonReadinessModal()" class="text-gray-400 hover:text-gray-600 p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Top Summary Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 shrink-0">
                <div class="bg-emerald-50/70 p-3 rounded-2xl border border-emerald-200">
                    <span class="text-[10px] text-emerald-800 font-bold block uppercase">अनिवार्य फ़ील्ड्स (Mandatory)</span>
                    <div class="text-base font-black text-emerald-950 mt-0.5">14 / 14 पूर्ण (100%)</div>
                    <span class="text-[9px] text-emerald-700">SKU, टाइटल, रेट, HSN, स्टॉक</span>
                </div>
                <div class="bg-indigo-50/70 p-3 rounded-2xl border border-indigo-200">
                    <span class="text-[10px] text-indigo-800 font-bold block uppercase">कंटेंट व खोज (SEO)</span>
                    <div class="text-base font-black text-indigo-950 mt-0.5">4 / 4 पूर्ण (100%)</div>
                    <span class="text-[9px] text-indigo-700">5 बुलेट्स, विवरण, 249B कीवर्ड्स</span>
                </div>
                <div class="bg-sky-50/70 p-3 rounded-2xl border border-sky-200">
                    <span class="text-[10px] text-sky-800 font-bold block uppercase">लॉजिस्टिक्स व वजन</span>
                    <div class="text-base font-black text-sky-950 mt-0.5">4 / 4 पूर्ण (100%)</div>
                    <span class="text-[9px] text-sky-700">वज़न, पैकेज स्लैब, कूरियर</span>
                </div>
                <div class="bg-purple-50/70 p-3 rounded-2xl border border-purple-200">
                    <span class="text-[10px] text-purple-800 font-bold block uppercase">कॉम्बो डील्स (Pack of X)</span>
                    <div class="text-base font-black text-purple-950 mt-0.5">2P, 4P, 6P सक्रिय</div>
                    <span class="text-[9px] text-purple-700">MRP कम्प्लायंस व डिस्काउंट स्प्रेड</span>
                </div>
            </div>

            <!-- Detailed Attributes Audit Table -->
            <div class="overflow-y-auto flex-1 pr-1 space-y-3">
                <div class="bg-gray-50 rounded-2xl border border-gray-200 overflow-hidden">
                    <table class="w-full text-left text-xs divide-y divide-gray-200">
                        <thead class="bg-gray-100 text-[10px] font-black uppercase text-gray-600">
                            <tr>
                                <th class="p-2.5">अमेज़न फ़ील्ड नाम</th>
                                <th class="p-2.5">हमारा सिस्टम स्रोत (Source)</th>
                                <th class="p-2.5 text-center">स्थिति</th>
                                <th class="p-2.5">उदाहरण मान</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-mono text-[11px]">
                            <tr>
                                <td class="p-2.5 font-bold font-sans">item_name (Title)</td>
                                <td class="p-2.5 text-gray-600 font-sans">AI SEO Title Generator</td>
                                <td class="p-2.5 text-center"><span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">✓ तैयार</span></td>
                                <td class="p-2.5 text-gray-800 truncate max-w-xs">Heavy Duty PTMT Bib Cock Water Tap 15mm...</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-bold font-sans">bullet_point1 to 5</td>
                                <td class="p-2.5 text-gray-600 font-sans">Product 5 Technical Bullets</td>
                                <td class="p-2.5 text-center"><span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">✓ तैयार</span></td>
                                <td class="p-2.5 text-gray-800 truncate max-w-xs">• Material: 100% Virgin PTMT Polymer...</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-bold font-sans">generic_keywords (250B)</td>
                                <td class="p-2.5 text-gray-600 font-sans">Amazon Backend Search Terms</td>
                                <td class="p-2.5 text-center"><span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">✓ 249 Bytes</span></td>
                                <td class="p-2.5 text-gray-800 truncate max-w-xs">bibcock bib tap bathroom water tap nal...</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-bold font-sans">standard_price (Your Price)</td>
                                <td class="p-2.5 text-gray-600 font-sans">सेलिंग भाव 1 (Amazon Net Rate)</td>
                                <td class="p-2.5 text-center"><span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">✓ तैयार</span></td>
                                <td class="p-2.5 text-emerald-700 font-black">₹327.00</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-bold font-sans">list_price (Printed Box MRP)</td>
                                <td class="p-2.5 text-gray-600 font-sans">प्राइस लिस्ट / डिब्बा MRP</td>
                                <td class="p-2.5 text-center"><span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">✓ तैयार</span></td>
                                <td class="p-2.5 text-gray-800">₹224.00</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-bold font-sans">item_package_quantity</td>
                                <td class="p-2.5 text-gray-600 font-sans">कॉम्बो स्लैब (Pack of X)</td>
                                <td class="p-2.5 text-center"><span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">✓ 1, 2, 4, 6</span></td>
                                <td class="p-2.5 text-purple-700 font-black">Pack of 2 & 4 Bestseller</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-bold font-sans">item_weight / package_weight</td>
                                <td class="p-2.5 text-gray-600 font-sans">सिंगल वज़न + टेयर वज़न</td>
                                <td class="p-2.5 text-center"><span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">✓ तैयार</span></td>
                                <td class="p-2.5 text-sky-800">0.20 kg (2P: 0.45 kg, 4P: 0.90 kg)</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-bold font-sans">material_type & country_of_origin</td>
                                <td class="p-2.5 text-gray-600 font-sans">कैटलॉग स्पेसिफिकेशन्स</td>
                                <td class="p-2.5 text-center"><span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">✓ तैयार</span></td>
                                <td class="p-2.5 text-gray-800">PTMT / Forged Brass • Made in India</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-bold font-sans">hsn_code & tax_code</td>
                                <td class="p-2.5 text-gray-600 font-sans">GST 18% (39174000 / 39229000)</td>
                                <td class="p-2.5 text-center"><span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">✓ तैयार</span></td>
                                <td class="p-2.5 text-gray-800">39174000 (A_GEN_STANDARD)</td>
                            </tr>
                            <tr class="bg-gray-50/50">
                                <td class="p-2.5 text-gray-400 font-sans">शेष 100 उन्नत कॉलम</td>
                                <td class="p-2.5 text-gray-400 font-sans">Hazardous, Battery, Chemicals, Liquids</td>
                                <td class="p-2.5 text-center"><span class="px-2 py-0.5 rounded-full bg-gray-200 text-gray-600 font-bold text-[9px]">वैकल्पिक / N/A</span></td>
                                <td class="p-2.5 text-gray-400">हार्डवेयर फिटिंग के लिए लागू नहीं</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="pt-3 border-t border-gray-100 flex items-center justify-between shrink-0 flex-wrap gap-2">
                <span class="text-xs text-gray-500">
                    *VyaparIndia से निर्यात (Export) होने वाली एक्सेल शीट सीधे अमेज़न सेलर सेंट्रल पर <strong>'Add Products via Upload'</strong> में स्वीकार्य है।
                </span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeAmazonReadinessModal()" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition cursor-pointer">
                        बंद करें
                    </button>
                    <button type="button" onclick="triggerAutoFillAmazonAttrs()" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer active:scale-95">
                        <i class="fa-solid fa-wand-magic-sparkles text-amber-200"></i>
                        <span>✨ सभी 19 प्रोडक्ट्स में ऑटो-फ़िल करें (Auto-Fill Missing)</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== 🛠️ SCRIPT FOR MASTER TOGGLE, SKIP SINGLE, & READINESS ==================== -->
    <script>
        let allProductDrawersOpen = false;

        // Master 1-Click Expand / Collapse All Product Details
        function toggleAllProductDrawers() {
            allProductDrawersOpen = !allProductDrawersOpen;
            const btnText = document.getElementById('btnMasterToggleText');
            if (btnText) {
                btnText.textContent = allProductDrawersOpen 
                    ? '📁 सभी विवरण एक साथ समेटें (Collapse All)' 
                    : '📂 सभी विवरण एक साथ खोलें (Expand All)';
            }

            const rows = document.querySelectorAll('#pendingTableTbody tr[data-id]');
            rows.forEach(r => {
                const prodId = r.getAttribute('data-id');
                const subrow = document.getElementById(`subrow-details-${prodId}`);
                if (subrow) {
                    if (allProductDrawersOpen) {
                        subrow.classList.remove('hidden');
                    } else {
                        subrow.classList.add('hidden');
                    }
                }
            });

            showChannelToast(allProductDrawersOpen ? '📂 सभी 19 प्रोडक्ट्स के विवरण खोल दिए गए!' : '📁 सभी प्रोडक्ट्स समेट दिए गए!');
        }

        // Toggle Single Product Skip (List 2P/4P/6P only)
        function onToggleSkipSingle(prodId) {
            const chk = document.getElementById(`row-skip-single-${prodId}`);
            const priceInp = document.getElementById(`row-price-${prodId}`);
            const mrpAlertEl = document.getElementById(`row-mrp-alert-${prodId}`);

            if (chk && chk.checked) {
                if (priceInp) {
                    priceInp.disabled = true;
                    priceInp.className = "w-20 pl-5 pr-1.5 py-1 bg-gray-100 border border-gray-300 rounded-lg text-xs font-mono font-bold text-gray-400 line-through cursor-not-allowed";
                }
                if (mrpAlertEl) {
                    mrpAlertEl.innerHTML = `<div class="px-2 py-1 rounded-lg bg-emerald-100 border border-emerald-300 text-emerald-950 text-[9px] font-black leading-tight shadow-2xs mt-1">
                        <div class="flex items-center gap-1 text-emerald-700">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>✅ 1P स्किप: अमेज़न पर केवल 2P, 4P, 6P कॉम्बो लाइव होंगे</span>
                        </div>
                    </div>`;
                }
                showChannelToast(`#${prodId}: 1-पीस को स्किप कर दिया गया है। अमेज़न पर केवल 2P, 4P, 6P कॉम्बो ही लिस्ट होंगे!`);
            } else {
                if (priceInp) {
                    priceInp.disabled = false;
                    priceInp.className = "w-20 pl-5 pr-1.5 py-1 bg-white border border-amber-300 rounded-lg text-xs font-mono font-black text-amber-950 focus:border-amber-500 focus:outline-none";
                }
                recalcTableRow(prodId);
            }
        }

        // Open/Close Amazon Readiness Modal
        function openAmazonReadinessModal() {
            document.getElementById('amazonReadinessModal').classList.remove('hidden');
            document.getElementById('amazonReadinessModal').classList.add('flex');
        }

        function closeAmazonReadinessModal() {
            document.getElementById('amazonReadinessModal').classList.add('hidden');
            document.getElementById('amazonReadinessModal').classList.remove('flex');
        }

        // Trigger Auto-Fill Missing Amazon Attributes via AJAX
        async function triggerAutoFillAmazonAttrs() {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                const res = await fetch('/seller/channels/amazon-attributes/auto-fill', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });
                const json = await res.json();
                if (json.success) {
                    showChannelToast(json.message);
                    closeAmazonReadinessModal();
                } else {
                    alert(json.message || 'ऑटो-फ़िल करने में त्रुटि हुई।');
                }
            } catch (err) {
                alert('नेटवर्क एरर। कृपया पुनः प्रयास करें।');
            }
        }
    
        // =========================================================================
        // 💰 DYNAMIC AMAZON SP-API & MULTI-CHANNEL FEE ENGINE JS
        // =========================================================================

        /**
         * Fetch Live Fee & Solve Optimal Selling Price for Single Row
         */
        async function syncSingleProductFee(prodId) {
            const btn = document.getElementById(`btn-sync-fee-${prodId}`);
            const origHtml = btn ? btn.innerHTML : '';
            if (btn) btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-[8px]"></i>';

            const cost = parseFloat(document.getElementById(`row-cost-${prodId}`)?.value) || 0;
            const mrp = parseFloat(document.getElementById(`row-mrp-${prodId}`)?.value) || 0;
            const margin = parseFloat(document.getElementById(`row-margin-${prodId}`)?.value) || 25;
            const rto = parseFloat(document.getElementById(`row-rto-${prodId}`)?.value) || 20;
            const pack = parseFloat(document.getElementById(`row-pack-${prodId}`)?.value) || 15;
            const ship = parseFloat(document.getElementById(`row-ship-${prodId}`)?.value) || 55;
            const ads = parseFloat(document.getElementById(`row-ads-${prodId}`)?.value) || 10;
            const others = parseFloat(document.getElementById(`row-others-${prodId}`)?.value) || 0;
            const gst = parseFloat(document.getElementById(`row-gst-${prodId}`)?.value) || 18;
            const weight = parseFloat(document.getElementById(`row-weight-${prodId}`)?.value) || 0.5;

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const res = await fetch('/seller/channels/fee-service/solve-price', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        product_id: prodId,
                        cost: cost,
                        mrp: mrp,
                        margin_pct: margin,
                        rto: rto,
                        packaging: pack,
                        courier: ship,
                        ads: ads,
                        others: others,
                        gst_rate: gst,
                        weight_kg: weight,
                        marketplace: 'amazon'
                    })
                });

                const json = await res.json();
                if (json.success && json.data) {
                    const d = json.data;
                    // Update fee input
                    const feeInp = document.getElementById(`row-fee-${prodId}`);
                    if (feeInp) feeInp.value = d.effective_fee_pct;

                    // Update badge
                    const badge = document.getElementById(`row-fee-badge-${prodId}`);
                    if (badge) {
                        if (d.is_actual) {
                            badge.className = "px-1 py-0.2 rounded text-[7.5px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300";
                            badge.textContent = "🟢 SP-API असली";
                            badge.title = "Amazon SP-API से वेरिफाइड असली फ़ीस";
                        } else {
                            badge.className = "px-1 py-0.2 rounded text-[7.5px] font-black bg-amber-100 text-amber-900 border border-amber-300";
                            badge.textContent = "🟡 अनुमानित";
                            badge.title = "Amazon 2026 Category Schedule पर आधारित सटीक अनुमान";
                        }
                    }

                    // Update fee breakdown text
                    const bk = document.getElementById(`row-fee-breakdown-${prodId}`);
                    if (bk && d.fees_breakdown) {
                        bk.textContent = `Ref ₹${d.fees_breakdown.referral_fee} + Clos ₹${d.fees_breakdown.closing_fee}`;
                    }

                    // Update price
                    const priceInp = document.getElementById(`row-price-${prodId}`);
                    if (priceInp) priceInp.value = d.solved_price;

                    // Show Diff badge
                    const diffBadge = document.getElementById(`row-price-diff-badge-${prodId}`);
                    if (diffBadge) {
                        diffBadge.classList.remove('hidden');
                        if (d.price_diff < 0) {
                            diffBadge.className = "text-[8px] font-black px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-900 border border-emerald-300 mt-1 block";
                            diffBadge.innerHTML = `<span>₹${d.p0_assumed} &rarr; <strong>₹${d.solved_price}</strong> (बचत ₹${Math.abs(d.price_diff)} 🎉)</span>`;
                        } else if (d.price_diff > 0) {
                            diffBadge.className = "text-[8px] font-black px-1.5 py-0.5 rounded bg-amber-100 text-amber-900 border border-amber-300 mt-1 block";
                            diffBadge.innerHTML = `<span>₹${d.p0_assumed} &rarr; <strong>₹${d.solved_price}</strong> (+₹${d.price_diff} सुधार)</span>`;
                        } else {
                            diffBadge.className = "text-[8px] font-black px-1.5 py-0.5 rounded bg-blue-100 text-blue-900 border border-blue-300 mt-1 block";
                            diffBadge.innerHTML = `<span>सटीक मैच: ₹${d.solved_price}</span>`;
                        }
                    }

                    recalcTableRow(prodId);
                    showChannelToast(`✅ #${prodId}: Amazon फ़ीस (${d.effective_fee_pct}%) व भाव ₹${d.solved_price} सफलतापूर्वक सॉल्व हो गया!`);
                } else {
                    showChannelToast('⚠️ फ़ीस सॉल्व करने में त्रुटि आई।');
                }
            } catch (err) {
                console.error(err);
                showChannelToast('❌ सर्वर नेटवर्क त्रुटि।');
            } finally {
                if (btn) btn.innerHTML = origHtml;
            }
        }

        /**
         * Bulk Sync Real Amazon SP-API / Schedule Fees for All 19 Products
         */
        async function bulkSyncAmazonFees() {
            const btn = document.getElementById('btnBulkSyncAmazonFees');
            const textSpan = document.getElementById('btnBulkSyncText');
            const origHtml = textSpan ? textSpan.textContent : '';
            if (btn) btn.disabled = true;
            if (textSpan) textSpan.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Amazon फ़ीस सिंक हो रही है...';

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const res = await fetch('/seller/channels/fee-service/bulk-sync-amazon-fees', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });

                const json = await res.json();
                if (json.success && json.products) {
                    json.products.forEach(p => {
                        const prodId = p.product_id;
                        const feeInp = document.getElementById(`row-fee-${prodId}`);
                        if (feeInp) feeInp.value = p.effective_fee_pct;

                        const badge = document.getElementById(`row-fee-badge-${prodId}`);
                        if (badge) {
                            if (p.fee_status === 'asli_verified') {
                                badge.className = "px-1 py-0.2 rounded text-[7.5px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300";
                                badge.textContent = "🟢 SP-API असली";
                            } else {
                                badge.className = "px-1 py-0.2 rounded text-[7.5px] font-black bg-amber-100 text-amber-900 border border-amber-300";
                                badge.textContent = "🟡 अनुमानित";
                            }
                        }

                        const priceInp = document.getElementById(`row-price-${prodId}`);
                        if (priceInp) priceInp.value = p.new_solved_price;

                        const diffBadge = document.getElementById(`row-price-diff-badge-${prodId}`);
                        if (diffBadge) {
                            diffBadge.classList.remove('hidden');
                            if (p.price_diff < 0) {
                                diffBadge.className = "text-[8px] font-black px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-900 border border-emerald-300 mt-1 block";
                                diffBadge.innerHTML = `<span>₹${p.old_assumed_price} &rarr; <strong>₹${p.new_solved_price}</strong> (-₹${Math.abs(p.price_diff)} 🎉)</span>`;
                            } else if (p.price_diff > 0) {
                                diffBadge.className = "text-[8px] font-black px-1.5 py-0.5 rounded bg-amber-100 text-amber-900 border border-amber-300 mt-1 block";
                                diffBadge.innerHTML = `<span>₹${p.old_assumed_price} &rarr; <strong>₹${p.new_solved_price}</strong> (+₹${p.price_diff})</span>`;
                            }
                        }

                        recalcTableRow(prodId);
                    });

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '🎉 अमेज़न फ़ीस व सेलिंग भाव सिंक पूर्ण!',
                            text: `सभी ${json.total_synced} प्रोडक्ट्स के लिए Amazon SP-API / Category Schedule से असली फ़ीस सिंक हो गई और नए पारदर्शी रेट्स सेट कर दिए गए!`,
                            confirmButtonText: 'बहुत बढ़िया (Awesome)',
                            confirmButtonColor: '#059669'
                        });
                    } else {
                        showChannelToast(json.message);
                    }
                } else {
                    showChannelToast('⚠️ सिंक विफल रहा।');
                }
            } catch (err) {
                console.error(err);
                showChannelToast('❌ सिंक नेटवर्क एरर।');
            } finally {
                if (btn) btn.disabled = false;
                if (textSpan) textSpan.textContent = origHtml;
            }
        }

        /**
         * Apply Verified Solved Price to Amazon SP-API & Store
         */
        async function applyPriceToAmazonSpApi(prodId) {
            const priceVal = parseFloat(document.getElementById(`row-price-${prodId}`)?.value) || 0;
            const feePctVal = parseFloat(document.getElementById(`row-fee-${prodId}`)?.value) || 12;

            if (priceVal <= 0) {
                showChannelToast('⚠️ अमान्य सेलिंग भाव।');
                return;
            }

            const btn = document.getElementById(`btn-apply-amz-${prodId}`);
            const origHtml = btn ? btn.innerHTML : '';
            if (btn) btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-[8px]"></i> Push';

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const res = await fetch('/seller/channels/fee-service/apply-amazon-price', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        product_id: prodId,
                        solved_price: priceVal,
                        effective_fee_pct: feePctVal
                    })
                });

                const json = await res.json();
                if (json.success) {
                    showChannelToast(`🚀 भाव ₹${priceVal} Amazon SP-API के लिए सुरक्षित रूप से सेट हो गया!`);
                    if (btn) {
                        btn.className = "px-1.5 py-0.5 rounded bg-emerald-600 text-white border border-emerald-400 font-black text-[9px]";
                        btn.innerHTML = '<i class="fa-solid fa-check mr-0.5"></i>Applied';
                    }
                } else {
                    showChannelToast(json.message || '⚠️ लागू करने में त्रुटि।');
                }
            } catch (err) {
                console.error(err);
                showChannelToast('❌ नेटवर्क एरर।');
            } finally {
                setTimeout(() => {
                    if (btn && !btn.innerHTML.includes('Applied')) btn.innerHTML = origHtml;
                }, 1500);
            }
        }

        /**
         * 5-Platform Compare Modal Logic
         */
        let activeCompareProdId = null;

        function openMultiChannelCompareModal(prodId = null) {
            const modal = document.getElementById('multiChannelCompareModal');
            if (!modal) return;

            if (!prodId) {
                const firstRow = document.querySelector('[id^="row-cost-"]');
                if (firstRow) {
                    prodId = firstRow.id.replace('row-cost-', '');
                }
            }

            activeCompareProdId = prodId;
            loadCompareModalData(prodId);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeMultiChannelCompareModal() {
            const modal = document.getElementById('multiChannelCompareModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        async function loadCompareModalData(prodId) {
            if (!prodId) {
                recalcMultiChannelLiveFromModal();
                return;
            }

            const cost = parseFloat(document.getElementById(`row-cost-${prodId}`)?.value) || 201;
            const mrp = parseFloat(document.getElementById(`row-mrp-${prodId}`)?.value) || 550;
            const margin = parseFloat(document.getElementById(`row-margin-${prodId}`)?.value) || 25;
            const rto = parseFloat(document.getElementById(`row-rto-${prodId}`)?.value) || 20;
            const pack = parseFloat(document.getElementById(`row-pack-${prodId}`)?.value) || 15;
            const ship = parseFloat(document.getElementById(`row-ship-${prodId}`)?.value) || 55;
            const ads = parseFloat(document.getElementById(`row-ads-${prodId}`)?.value) || 10;
            const gst = parseFloat(document.getElementById(`row-gst-${prodId}`)?.value) || 18;
            const weight = parseFloat(document.getElementById(`row-weight-${prodId}`)?.value) || 0.5;

            if (document.getElementById('cmpInputCost')) document.getElementById('cmpInputCost').value = cost;
            if (document.getElementById('cmpInputMrp')) document.getElementById('cmpInputMrp').value = mrp;
            if (document.getElementById('cmpInputMargin')) document.getElementById('cmpInputMargin').value = margin;
            if (document.getElementById('cmpInputCourier')) document.getElementById('cmpInputCourier').value = ship;
            if (document.getElementById('cmpInputRto')) document.getElementById('cmpInputRto').value = rto;
            if (document.getElementById('cmpInputPack')) document.getElementById('cmpInputPack').value = pack;
            if (document.getElementById('cmpInputAds')) document.getElementById('cmpInputAds').value = ads;
            if (document.getElementById('cmpInputGst')) document.getElementById('cmpInputGst').value = gst;

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const res = await fetch('/seller/channels/fee-service/multichannel-compare', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        product_id: prodId,
                        cost: cost,
                        mrp: mrp,
                        margin_pct: margin,
                        rto: rto,
                        packaging: pack,
                        courier: ship,
                        ads: ads,
                        gst_rate: gst,
                        weight_kg: weight
                    })
                });

                const json = await res.json();
                if (json.success && json.platforms) {
                    renderCompareCards(json.platforms, json.cost, json.profit);
                } else {
                    recalcMultiChannelLiveFromModal();
                }
            } catch (err) {
                console.error(err);
                recalcMultiChannelLiveFromModal();
            }
        }

        function renderCompareCards(plats, cost, profit) {
            const container = document.getElementById('cmpCardsContainer');
            if (!container) return;

            let html = '';
            const platOrder = ['amazon', 'meesho', 'flipkart', 'myntra', 'blinkit'];

            platOrder.forEach(key => {
                const p = plats[key];
                if (!p) return;

                let badgeBg = 'bg-amber-50 text-amber-900 border-amber-200';
                let barColor = 'bg-amber-500';
                if (key === 'meesho') { badgeBg = 'bg-pink-50 text-pink-900 border-pink-200'; barColor = 'bg-pink-500'; }
                if (key === 'flipkart') { badgeBg = 'bg-blue-50 text-blue-900 border-blue-200'; barColor = 'bg-blue-600'; }
                if (key === 'myntra') { badgeBg = 'bg-rose-50 text-rose-900 border-rose-200'; barColor = 'bg-rose-600'; }
                if (key === 'blinkit') { badgeBg = 'bg-yellow-50 text-yellow-900 border-yellow-200'; barColor = 'bg-yellow-500'; }

                html += `
                <div class="rounded-2xl border ${badgeBg} p-4 flex flex-col justify-between shadow-sm relative overflow-hidden">
                    <div class="h-1.5 w-full ${barColor} absolute top-0 left-0"></div>
                    <div>
                        <div class="flex items-center justify-between pb-2 border-b border-gray-200/50">
                            <span class="font-black text-xs flex items-center gap-1.5">
                                <i class="${p.icon}"></i>
                                <span>${p.name}</span>
                            </span>
                            <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded bg-white/80 border border-gray-200">
                                ${p.fee_pct > 0 ? p.fee_pct + '% फ़ी' : '0% फ़ी 🎉'}
                            </span>
                        </div>

                        <div class="my-3 text-center">
                            <span class="text-[10px] text-gray-500 block font-bold uppercase">सुझावित सेलिंग भाव</span>
                            <span class="text-2xl font-black font-mono text-gray-900">₹${p.price}</span>
                            ${p.mrp_violation ? '<span class="text-[8px] font-bold text-rose-600 block mt-0.5">🚨 MRP उल्लंघन!</span>' : '<span class="text-[8px] font-bold text-emerald-700 block mt-0.5">✅ MRP सुरक्षित</span>'}
                        </div>

                        <div class="space-y-1.5 text-[11px] pt-2 border-t border-gray-200/50 font-mono">
                            <div class="flex justify-between text-gray-600">
                                <span>खरीद लागत:</span>
                                <strong class="text-gray-900">₹${cost}</strong>
                            </div>
                            <div class="flex justify-between text-indigo-700">
                                <span>शुद्ध मुनाफा:</span>
                                <strong class="text-indigo-900 font-bold">+₹${profit}</strong>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>कमीशन / फ़ी:</span>
                                <strong class="text-gray-900">${p.fee_pct > 0 ? (p.fee_pct + '%') : '₹0.00'}</strong>
                            </div>
                            ${p.courier ? `<div class="flex justify-between text-gray-600"><span>कूरियर (+GST):</span><strong class="text-gray-900">₹${p.courier}</strong></div>` : ''}
                            ${p.rto_buffer ? `<div class="flex justify-between text-rose-700"><span>RTO बफ़र:</span><strong>₹${p.rto_buffer}</strong></div>` : ''}
                            ${p.fixed_fee ? `<div class="flex justify-between text-gray-600"><span>फ़िक्स फ़ी:</span><strong>₹${p.fixed_fee}</strong></div>` : ''}
                            ${p.handling ? `<div class="flex justify-between text-gray-600"><span>डार्क स्टोर हैंडलिंग:</span><strong>₹${p.handling}</strong></div>` : ''}
                        </div>
                    </div>

                    <div class="mt-3 pt-2 border-t border-gray-200/50 text-center">
                        <span class="text-[8px] text-gray-500 font-bold">शुद्ध बैंक पेआउट: ₹${Math.round(cost + profit)}</span>
                    </div>
                </div>
                `;
            });

            container.innerHTML = html;
        }

        function recalcMultiChannelLiveFromModal() {
            const cost = parseFloat(document.getElementById('cmpInputCost')?.value) || 200;
            const mrp = parseFloat(document.getElementById('cmpInputMrp')?.value) || 0;
            const margin = parseFloat(document.getElementById('cmpInputMargin')?.value) || 25;
            const courier = parseFloat(document.getElementById('cmpInputCourier')?.value) || 55;
            const rto = parseFloat(document.getElementById('cmpInputRto')?.value) || 20;
            const pack = parseFloat(document.getElementById('cmpInputPack')?.value) || 15;
            const ads = parseFloat(document.getElementById('cmpInputAds')?.value) || 10;
            const gst = parseFloat(document.getElementById('cmpInputGst')?.value) || 18;

            const g = gst / 100;
            const cTax = cost / (1 + g);
            const desiredProfit = Math.round(cost * (margin / 100));
            const courierWithGst = courier * 1.18;

            // Amazon
            const fixedAmz = rto + pack + courierWithGst + ads;
            const pAmz = Math.ceil((cTax + fixedAmz + desiredProfit) / Math.max(0.05, (1 / (1 + g)) - 0.12));

            // Meesho (0% commission)
            const fixedMsh = rto + pack + courierWithGst;
            const pMsh = Math.ceil((cTax + fixedMsh + desiredProfit) * (1 + g));

            // Flipkart
            const fixedFk = rto + pack + courierWithGst + ads;
            const pFk = Math.ceil((cTax + fixedFk + desiredProfit + 15) / Math.max(0.05, (1 / (1 + g)) - 0.115));

            // Myntra
            const fixedMyn = rto + pack + (courier * 1.3) + ads;
            const pMyn = Math.ceil((cTax + fixedMyn + desiredProfit) / Math.max(0.05, (1 / (1 + g)) - 0.20));

            // Blinkit
            const fixedBlk = pack + 12 + 5;
            const pBlk = Math.ceil((cTax + fixedBlk + desiredProfit) / Math.max(0.05, (1 / (1 + g)) - 0.16));

            const simulatedPlats = {
                amazon: { name: 'Amazon India', icon: 'fa-brands fa-amazon', fee_pct: 12.0, price: pAmz, mrp_violation: mrp > 0 && pAmz > mrp, courier: courierWithGst.toFixed(1) },
                meesho: { name: 'Meesho Supplier', icon: 'fa-solid fa-cart-shopping', fee_pct: 0.0, price: pMsh, mrp_violation: mrp > 0 && pMsh > mrp, courier: courierWithGst.toFixed(1), rto_buffer: rto },
                flipkart: { name: 'Flipkart Marketplace', icon: 'fa-solid fa-bolt', fee_pct: 11.5, fixed_fee: 15, price: pFk, mrp_violation: mrp > 0 && pFk > mrp, courier: courierWithGst.toFixed(1) },
                myntra: { name: 'Myntra Partner', icon: 'fa-solid fa-bag-shopping', fee_pct: 20.0, price: pMyn, mrp_violation: mrp > 0 && pMyn > mrp, courier: (courier * 1.3).toFixed(1) },
                blinkit: { name: 'Blinkit Quick-Commerce', icon: 'fa-solid fa-bolt text-amber-500', fee_pct: 16.0, handling: 12, price: pBlk, mrp_violation: mrp > 0 && pBlk > mrp }
            };

            renderCompareCards(simulatedPlats, cost, desiredProfit);
        }
    
        /**
         * Bulk Auto-Harvest Buyer Search Keywords for All Multichannel Products
         */
        async function bulkAutoHarvestKeywords() {
            const btn = document.getElementById('btnBulkHarvestKeywords');
            const textSpan = document.getElementById('btnBulkHarvestText');
            const origHtml = textSpan ? textSpan.textContent : '';
            if (btn) btn.disabled = true;
            if (textSpan) textSpan.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> सर्च कीवर्ड्स खुद ढूंढकर अपडेट हो रहे हैं...';

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const res = await fetch('/seller/channels/keywords/auto-harvest-bulk', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });

                const json = await res.json();
                if (json.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '🎉 कीवर्ड्स ऑटो-हार्वेस्ट व अपडेट पूर्ण!',
                            html: `<div class="text-left text-xs space-y-2">
                                <p><strong>कुल प्रोडक्ट्स अपडेट:</strong> ${json.total_products}</p>
                                <p><strong>कुल सर्च कीवर्ड्स हार्वेस्ट:</strong> ${json.total_words_harvested} शब्द</p>
                                <p class="text-emerald-700 font-bold">✅ सभी प्रोडक्ट्स के बैकएंड सर्च टर्म्स में 248 बाइट्स के अंदर सबसे ज़्यादा सर्च होने वाले मुख्य शब्द (Stemmed & Deduplicated) बिना किसी टकराव के खुद भर दिए गए हैं!</p>
                            </div>`,
                            confirmButtonText: 'शानदार (Awesome)',
                            confirmButtonColor: '#059669'
                        });
                    } else {
                        showChannelToast(json.message);
                    }
                } else {
                    showChannelToast('⚠️ कीवर्ड्स हार्वेस्ट में त्रुटि।');
                }
            } catch (err) {
                console.error(err);
                showChannelToast('❌ नेटवर्क एरर।');
            } finally {
                if (btn) btn.disabled = false;
                if (textSpan) textSpan.textContent = origHtml;
            }
        }
    </script>
