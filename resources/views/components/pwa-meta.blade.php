<!-- 📱 VyaparIndia PWA Native App Meta & Service Worker -->
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#2563eb">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="VyaparIndia">
<link rel="apple-touch-icon" href="/images/icons/apple-touch-icon.png">
<link rel="icon" type="image/png" href="/favicon.png">

<!-- Service Worker Registration -->
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js', { scope: '/' })
                .then(function(reg) {
                    console.log('✓ VyaparIndia PWA Service Worker Registered. Scope:', reg.scope);
                })
                .catch(function(err) {
                    console.warn('PWA Registration failed:', err);
                });
        });
    }

    // PWA Native Install Prompt Handler
    let deferredInstallPrompt = null;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredInstallPrompt = e;
        const banner = document.getElementById('vyapar-pwa-install-banner');
        if (banner && !localStorage.getItem('pwa_banner_dismissed')) {
            banner.classList.remove('hidden');
            banner.classList.add('flex');
        }
    });

    function triggerPwaInstall() {
        if (deferredInstallPrompt) {
            deferredInstallPrompt.prompt();
            deferredInstallPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('User installed VyaparIndia PWA');
                    dismissPwaBanner();
                }
                deferredInstallPrompt = null;
            });
        } else {
            // Fallback instruction for iOS or unsupported
            alert('VyaparIndia को होम स्क्रीन पर जोड़ने के लिए:\n1. शेयर (Share) बटन दबाएं\n2. "Add to Home Screen" चुनें');
        }
    }

    function dismissPwaBanner() {
        const banner = document.getElementById('vyapar-pwa-install-banner');
        if (banner) {
            banner.classList.add('hidden');
            banner.classList.remove('flex');
            localStorage.setItem('pwa_banner_dismissed', 'true');
        }
    }
</script>

<!-- Mobile Native App Install Banner (WhatsApp/PWA Style) -->
<div id="vyapar-pwa-install-banner" class="hidden fixed top-3 inset-x-3 sm:inset-x-auto sm:right-6 sm:w-96 z-50 bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-950 text-white p-3.5 rounded-2xl shadow-2xl border border-indigo-500/40 items-center justify-between gap-3 animate-bounce">
    <div class="flex items-center gap-3">
        <img src="/images/icons/icon-192x192.png" alt="VyaparIndia App" class="w-10 h-10 rounded-xl shadow-md border border-white/20 shrink-0">
        <div>
            <h4 class="text-xs font-black text-white flex items-center gap-1.5">
                VyaparIndia App
                <span class="text-[9px] bg-emerald-500/20 text-emerald-300 font-extrabold px-1.5 py-0.2 rounded-full border border-emerald-500/30">Native PWA</span>
            </h4>
            <p class="text-[10px] text-slate-300">फ़ोन में इनस्टॉल करें (Install App)</p>
        </div>
    </div>
    <div class="flex items-center gap-1.5 shrink-0">
        <button type="button" onclick="triggerPwaInstall()" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1 cursor-pointer">
            <i class="fa-solid fa-download"></i>
            <span>Install</span>
        </button>
        <button type="button" onclick="dismissPwaBanner()" class="w-7 h-7 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition">
            <i class="fa-solid fa-xmark text-xs"></i>
        </button>
    </div>
</div>
