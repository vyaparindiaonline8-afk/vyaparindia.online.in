<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Hub - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="h-10 w-10 rounded-2xl bg-gradient-to-tr from-purple-700 to-indigo-600 text-white font-black flex items-center justify-center text-lg shadow-md shadow-purple-500/20">
                        👑
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-black text-slate-900 text-base tracking-tight">VyaparIndia</span>
                            <span class="px-2 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-black uppercase tracking-wider border border-purple-200">
                                Super Admin Hub
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500">Platform Control & Owner Dashboard</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Switch to Seller Store -->
                    <a href="{{ route('seller.dashboard') }}" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-xs shadow-md shadow-blue-500/20 transition flex items-center gap-2" title="अपने सेलर स्टोर और प्रोडक्ट्स में जाएं">
                        <i class="fa-solid fa-store"></i>
                        <span>मेरे सेलर स्टोर में जाएं (Seller Hub)</span>
                    </a>

                    <a href="{{ route('home') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5" title="मुख्य मार्केटप्लेस">
                        <i class="fa-solid fa-house text-blue-600"></i>
                        <span class="hidden sm:inline">होम पेज</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition flex items-center gap-1.5">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Welcome Hero Banner -->
        <div class="bg-gradient-to-r from-purple-900 via-indigo-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-xs font-bold backdrop-blur-xs border border-white/15">
                    <i class="fa-solid fa-crown text-amber-400"></i>
                    <span>प्लेटफॉर्म ओनर (Super Admin) का स्वागत है</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                    नमस्ते, {{ Auth::user()->name }}! 👑
                </h1>
                <p class="text-xs sm:text-sm text-purple-200 leading-relaxed">
                    आप VyaparIndia के मुख्य एडमिन हैं। आपके पास पूरे सिस्टम, सभी सेलर्स, कैटलॉग, श्रेणियों (Categories), और अपने निजी सेलर स्टोर का 100% अनलिमिटेड नियंत्रण है।
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 relative z-10">
                <a href="{{ route('seller.products.index') }}" class="px-5 py-3 rounded-2xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs shadow-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <span>मेरे प्रोडक्ट्स व एक्सेल सिंक</span>
                </a>
                <a href="{{ route('seller.dashboard') }}" class="px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs backdrop-blur-md border border-white/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>सेलर डैशबोर्ड</span>
                </a>
            </div>
        </div>

        <!-- Quick Access Switcher Card -->
        <div class="bg-amber-50 border border-amber-200 rounded-3xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-2xl bg-amber-200 text-amber-900 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-lightbulb"></i>
                </div>
                <div>
                    <h3 class="text-xs font-black text-amber-950">क्या आप अपने प्रोडक्ट्स और रेट्स अपडेट करना चाहते हैं?</h3>
                    <p class="text-[11px] text-amber-800">
                        आप सीधे अपने **सेलर हब (Seller Hub)** में जा सकते हैं, जहाँ आपका कैटलॉग, एक्सेल शीट सिंक, और मिनी-वेबसाइट मौजूद है।
                    </p>
                </div>
            </div>
            <a href="{{ route('seller.products.index') }}" class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-black text-xs transition flex items-center gap-2 shrink-0 shadow-sm">
                <span>कैटलॉग प्रोडक्ट्स में जाएं</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <!-- Platform Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">कुल यूज़र्स</span>
                    <span class="h-8 w-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-users"></i>
                    </span>
                </div>
                <div class="text-2xl font-black text-slate-900">{{ $totalUsers ?? 0 }}</div>
                <a href="{{ route('admin.users.index') }}" class="text-[11px] font-bold text-blue-600 hover:underline flex items-center gap-1">
                    <span>मैनेज करें</span> <i class="fa-solid fa-chevron-right text-[9px]"></i>
                </a>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">रजिस्टर्ड सेलर्स</span>
                    <span class="h-8 w-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-store"></i>
                    </span>
                </div>
                <div class="text-2xl font-black text-slate-900">{{ $totalSellers ?? 0 }}</div>
                <span class="text-[11px] text-slate-400">एक्टिव वेंडर प्रोफाइल्स</span>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">लाइव प्रोडक्ट्स</span>
                    <span class="h-8 w-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-box-open"></i>
                    </span>
                </div>
                <div class="text-2xl font-black text-slate-900">{{ $totalProducts ?? 0 }}</div>
                <a href="{{ route('seller.products.index') }}" class="text-[11px] font-bold text-purple-600 hover:underline flex items-center gap-1">
                    <span>प्रोडक्ट्स देखें</span> <i class="fa-solid fa-chevron-right text-[9px]"></i>
                </a>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">कैटेगरी लिस्ट</span>
                    <span class="h-8 w-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-tags"></i>
                    </span>
                </div>
                <div class="text-2xl font-black text-slate-900">{{ $totalCategories ?? 0 }}</div>
                <a href="{{ route('admin.categories.index') }}" class="text-[11px] font-bold text-amber-600 hover:underline flex items-center gap-1">
                    <span>मर्ज व एडिट करें</span> <i class="fa-solid fa-chevron-right text-[9px]"></i>
                </a>
            </div>
        </div>

        <!-- Admin Quick Actions Grid -->
        <div>
            <h2 class="text-lg font-black text-slate-900 mb-4">प्लेटफॉर्म कंट्रोल टूल्स (Quick Actions)</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Manage Products -->
                <a href="{{ route('seller.products.index') }}" class="bg-white hover:bg-slate-50 border border-slate-200 rounded-3xl p-5 shadow-xs transition group space-y-3">
                    <div class="h-12 w-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl group-hover:scale-105 transition">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 group-hover:text-blue-600 transition">मेरे प्रोडक्ट्स व कैटलॉग</h3>
                        <p class="text-xs text-slate-500 mt-1">एक्सेल शीट अपलोड करें, भाव अपडेट करें और साइज़ वेरिएंट्स जोड़ें।</p>
                    </div>
                </a>

                <!-- Seller Hub -->
                <a href="{{ route('seller.dashboard') }}" class="bg-white hover:bg-slate-50 border border-slate-200 rounded-3xl p-5 shadow-xs transition group space-y-3">
                    <div class="h-12 w-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl group-hover:scale-105 transition">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 group-hover:text-emerald-600 transition">सेलर डैशबोर्ड (Store Hub)</h3>
                        <p class="text-xs text-slate-500 mt-1">मिनी-वेबसाइट, ऑर्डर्स, व्हाट्सएप कार्ट और सेलर सेटिंग्स देखें।</p>
                    </div>
                </a>

                <!-- Brand Master Catalog -->
                <a href="{{ route('seller.brand-master.index') }}" class="bg-white hover:bg-slate-50 border border-slate-200 rounded-3xl p-5 shadow-xs transition group space-y-3">
                    <div class="h-12 w-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl group-hover:scale-105 transition">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 group-hover:text-amber-600 transition">ब्रांड मास्टर कैटलॉग (Plasto) ⚡</h3>
                        <p class="text-xs text-slate-500 mt-1">1-क्लिक में 79+ रेडीमेड पाइप व फिटिंग्स अपने स्टोर में जोड़ें।</p>
                    </div>
                </a>

                <!-- Manage Categories -->
                <a href="{{ route('admin.categories.index') }}" class="bg-white hover:bg-slate-50 border border-slate-200 rounded-3xl p-5 shadow-xs transition group space-y-3">
                    <div class="h-12 w-12 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center text-xl group-hover:scale-105 transition">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 group-hover:text-purple-600 transition">कैटेगरी मैनेजमेंट</h3>
                        <p class="text-xs text-slate-500 mt-1">कैटेगरी एडिट करें, डुप्लिकेट श्रेणियां मर्ज करें।</p>
                    </div>
                </a>

                <!-- Manage Users -->
                <a href="{{ route('admin.users.index') }}" class="bg-white hover:bg-slate-50 border border-slate-200 rounded-3xl p-5 shadow-xs transition group space-y-3">
                    <div class="h-12 w-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl group-hover:scale-105 transition">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 group-hover:text-indigo-600 transition">यूज़र्स और सेलर्स</h3>
                        <p class="text-xs text-slate-500 mt-1">सभी रजिस्टर्ड यूज़र्स देखें और उनके रोल्स मैनेज करें।</p>
                    </div>
                </a>

                <!-- Live Marketplace -->
                <a href="{{ route('home') }}" class="bg-white hover:bg-slate-50 border border-slate-200 rounded-3xl p-5 shadow-xs transition group space-y-3">
                    <div class="h-12 w-12 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center text-xl group-hover:scale-105 transition">
                        <i class="fa-solid fa-globe"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 group-hover:text-rose-600 transition">लाइव मार्केटप्लेस</h3>
                        <p class="text-xs text-slate-500 mt-1">ग्राहकों के लिए लाइव मार्केटप्लेस देखें और चेक करें।</p>
                    </div>
                </a>
            </div>
        </div>
    </main>

    @include('components.ai-copilot-widget')
</body>
</html>
