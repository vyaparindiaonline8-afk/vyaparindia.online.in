<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>डाउनलोड करें VyaparIndia Android App (APK) - B2B होलसेल मार्केटप्लेस</title>
    <meta name="description" content="VyaparIndia B2B Android App APK डाउनलोड करें। WhatsApp-स्टाइल AI चैट से 1-क्लिक में प्रोडक्ट्स लाइव करें, होलसेल रेट्स देखें और सीधे फैक्ट्रियों से जुड़ें।">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @include('components.pwa-meta')
</head>
<body class="bg-slate-900 text-slate-100 antialiased min-h-screen flex flex-col font-sans selection:bg-emerald-500 selection:text-white">

    <!-- Top Navigation Header -->
    <header class="bg-slate-950/80 backdrop-blur-md border-b border-slate-800 sticky top-0 z-30">
        <div class="max-w-5xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-black text-lg shadow-sm">
                    V
                </div>
                <div>
                    <span class="font-black text-white text-base tracking-tight group-hover:text-emerald-400 transition">VyaparIndia</span>
                    <span class="ml-1.5 text-[10px] font-extrabold text-emerald-400 bg-emerald-950/60 border border-emerald-500/30 px-2 py-0.5 rounded-full">App Hub</span>
                </div>
            </a>

            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition flex items-center gap-1.5">
                    <i class="fa-solid fa-house text-emerald-400"></i>
                    <span class="hidden sm:inline">होम पेज</span>
                </a>
                @auth
                    @if(Auth::user()->is_seller())
                        <a href="{{ route('seller.dashboard') }}" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs transition">
                            डैशबोर्ड &rarr;
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs transition">
                        लॉगिन
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-4xl mx-auto px-4 py-8 sm:py-12 w-full space-y-8">
        
        <!-- Hero App Showcase Card -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-slate-800 via-slate-850 to-slate-900 border border-slate-700 p-6 sm:p-10 shadow-2xl text-center space-y-6">
            
            <!-- Background Glow -->
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-96 h-40 bg-emerald-500/15 blur-3xl rounded-full pointer-events-none"></div>

            <!-- App Icon with Ripple -->
            <div class="relative inline-block">
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-emerald-500 p-1 shadow-2xl mx-auto">
                    <img src="/images/icons/icon-192x192.png" alt="VyaparIndia App Icon" class="w-full h-full object-cover rounded-[22px] bg-slate-950">
                </div>
                <span class="absolute -bottom-2 -right-2 px-2.5 py-0.5 rounded-full bg-emerald-500 text-slate-950 font-black text-[10px] uppercase tracking-wider shadow-md">
                    v1.0.0
                </span>
            </div>

            <div class="space-y-2 max-w-xl mx-auto">
                <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight leading-tight">
                    VyaparIndia Android App
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                    भारत के व्यापारियों के लिए सबसे आसान B2B ऐप — <strong class="text-emerald-400">WhatsApp की तरह 2-3 फोटो खींचें</strong> और AI से 1-क्लिक में माल लाइव करें!
                </p>
            </div>

            <!-- Primary Download Actions -->
            <div class="max-w-md mx-auto space-y-3 pt-2">
                
                <!-- Main APK Download Button (High-Speed CDN) -->
                <a href="https://github.com/vyaparindiaonline8-afk/vyaparindia.online.in/releases/latest/download/vyaparindia.apk" 
                   id="apk-download-btn"
                   class="group w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600 hover:from-emerald-400 hover:to-teal-500 active:scale-98 text-slate-950 font-black text-sm sm:text-base shadow-xl hover:shadow-emerald-500/25 transition-all flex items-center justify-between gap-3 cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-950/20 flex items-center justify-center text-xl text-slate-950 group-hover:scale-110 transition">
                            <i class="fa-brands fa-android"></i>
                        </div>
                        <div class="text-left">
                            <div class="leading-tight">Download Android App (APK)</div>
                            <div class="text-[10px] text-slate-900/80 font-bold">सुपर-फ़ास्ट CDN • मात्र ~5 MB • 100% सुरक्षित</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-arrow-down text-lg group-hover:translate-y-0.5 transition"></i>
                </a>

                <!-- Alternative: Instant PWA Install (No Download Needed) -->
                <button type="button" onclick="triggerPwaInstall()"
                        class="w-full py-3 px-4 rounded-xl bg-white/10 hover:bg-white/15 active:scale-98 text-white font-bold text-xs transition border border-white/10 flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-bolt text-amber-400"></i>
                    <span>या बिना APK डाउनलोड किए तुरंत 1-क्लिक में जोड़ें (PWA)</span>
                </button>
            </div>

            <!-- Trust Badges -->
            <div class="grid grid-cols-3 gap-2 max-w-lg mx-auto pt-4 border-t border-slate-700/60 text-center">
                <div class="space-y-1">
                    <i class="fa-solid fa-shield-halved text-emerald-400 text-sm"></i>
                    <div class="text-[11px] font-black text-white">100% सुरक्षित</div>
                    <div class="text-[9px] text-slate-400">नो-वायरस वेरिफाइड</div>
                </div>
                <div class="space-y-1">
                    <i class="fa-solid fa-gauge-high text-cyan-400 text-sm"></i>
                    <div class="text-[11px] font-black text-white">सुपर फ़ास्ट</div>
                    <div class="text-[9px] text-slate-400">ग्लोबल CDN डाउनलोड</div>
                </div>
                <div class="space-y-1">
                    <i class="fa-solid fa-mobile-screen text-amber-400 text-sm"></i>
                    <div class="text-[11px] font-black text-white">सभी Android फोन</div>
                    <div class="text-[9px] text-slate-400">Android 7.0 से 15 तक</div>
                </div>
            </div>
        </div>

        <!-- How to Install (3 Simple Steps) -->
        <div class="rounded-3xl bg-slate-800/80 border border-slate-700 p-6 sm:p-8 space-y-6">
            <h2 class="text-base sm:text-lg font-black text-white flex items-center gap-2">
                <i class="fa-solid fa-circle-question text-emerald-400"></i>
                <span>फोन में ऐप कैसे इंस्टॉल करें? (3 आसान स्टेप्स)</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-700/70 space-y-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 font-black text-sm flex items-center justify-center border border-emerald-500/30">
                        1
                    </div>
                    <h3 class="text-xs font-black text-white">डाउनलोड शुरू करें</h3>
                    <p class="text-[11px] text-slate-300 leading-relaxed">
                        ऊपर दिए गए <strong>"Download APK"</strong> बटन पर क्लिक करें। फ़ाइल सिर्फ 5 सेकंड में डाउनलोड हो जाएगी।
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-700/70 space-y-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-500/20 text-blue-400 font-black text-sm flex items-center justify-center border border-blue-500/30">
                        2
                    </div>
                    <h3 class="text-xs font-black text-white">फ़ाइल खोलें और Allow करें</h3>
                    <p class="text-[11px] text-slate-300 leading-relaxed">
                        डाउनलोड पूरा होने पर <strong>vyaparindia.apk</strong> पर टैप करें। अगर फोन में <em>"Install unknown apps"</em> पूछे, तो <strong>Allow</strong> कर दें।
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-700/70 space-y-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 font-black text-sm flex items-center justify-center border border-amber-500/30">
                        3
                    </div>
                    <h3 class="text-xs font-black text-white">व्यापार शुरू करें!</h3>
                    <p class="text-[11px] text-slate-300 leading-relaxed">
                        <strong>Install</strong> पर क्लिक करें। ऐप तुरंत आपके फोन के होम स्क्रीन पर WhatsApp के बगल में आ जाएगा!
                    </p>
                </div>

            </div>
        </div>

        <!-- App Features Highlights -->
        <div class="rounded-3xl bg-slate-800/80 border border-slate-700 p-6 sm:p-8 space-y-4">
            <h2 class="text-base sm:text-lg font-black text-white flex items-center gap-2">
                <i class="fa-solid fa-star text-amber-400"></i>
                <span>VyaparIndia App की खास खूबियाँ</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div class="p-3 rounded-xl bg-slate-900/50 border border-slate-700/50 flex items-start gap-3">
                    <i class="fa-brands fa-whatsapp text-emerald-400 text-lg shrink-0 mt-0.5"></i>
                    <div>
                        <strong class="text-white block font-black">WhatsApp-Style AI Listing</strong>
                        <span class="text-slate-400 text-[11px]">फोटो खींचें या बोलकर 30 सेकंड में प्रोडक्ट को लाइव करें।</span>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-slate-900/50 border border-slate-700/50 flex items-start gap-3">
                    <i class="fa-solid fa-microphone text-rose-400 text-lg shrink-0 mt-0.5"></i>
                    <div>
                        <strong class="text-white block font-black">AI Voice Engine (आवाज़ से भाव बदलें)</strong>
                        <span class="text-slate-400 text-[11px]">सरिया, सीमेंट, हार्डवेयर का भाव बोलकर सभी प्रोडक्ट्स में अपडेट करें।</span>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-slate-900/50 border border-slate-700/50 flex items-start gap-3">
                    <i class="fa-solid fa-cart-shopping text-cyan-400 text-lg shrink-0 mt-0.5"></i>
                    <div>
                        <strong class="text-white block font-black">डायरेक्ट फ़ैक्ट्री होलसेल रेट्स</strong>
                        <span class="text-slate-400 text-[11px]">होलसेलर्स और मैन्युफैक्चरर्स से सीधे माल खरीदें और बेचें।</span>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-slate-900/50 border border-slate-700/50 flex items-start gap-3">
                    <i class="fa-solid fa-globe text-purple-400 text-lg shrink-0 mt-0.5"></i>
                    <div>
                        <strong class="text-white block font-black">Omnichannel Sync (Amazon, Meesho)</strong>
                        <span class="text-slate-400 text-[11px]">एक जगह से अपने सभी ऑनलाइन चैनलों का स्टॉक मैनेज करें।</span>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 border-t border-slate-800 py-6 text-center text-xs text-slate-500">
        <div class="max-w-4xl mx-auto px-4 space-y-1">
            <p>© {{ date('Y') }} VyaparIndia B2B Marketplace. All rights reserved.</p>
            <p class="text-[11px] text-slate-600">भारत का डिजिटल व्यापार हब • मेड इन इंडिया 🇮🇳</p>
        </div>
    </footer>

</body>
</html>
