@php
    $type = $type ?? 'tornado';
@endphp

@if($type === 'tornado')
<!-- 🌪️ Tornado 360° Flush Technology Spotlight -->
<div class="my-8 rounded-3xl bg-gradient-to-r from-cyan-950 via-slate-900 to-indigo-950 border border-cyan-500/30 p-6 sm:p-8 shadow-xl text-white relative overflow-hidden">
    <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="flex flex-col md:flex-row items-center justify-between gap-6 relative z-10">
        <div class="space-y-2 text-center md:text-left">
            <div class="flex items-center justify-center md:justify-start gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/20 text-cyan-300 font-black text-[10px] border border-cyan-500/30">
                    🌪️ ADVANCED FLUSH TECHNOLOGY
                </span>
                <span class="text-[10px] text-slate-400 font-bold hidden sm:inline">Tornado 360° Siphonic Cyclone</span>
            </div>
            <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                Tornado 360° Flush — 60% पानी की बचत और 100% साफ़ सफ़ाई
            </h3>
            <p class="text-xs text-slate-300 max-w-xl leading-relaxed">
                पारंपरिक कमोड के मुकाबले टॉरनेडो जेट साइक्लोन पानी को 360 डिग्री घुमाकर बाउल के हर कोने को एक ही फ्लैश में चमका देता है। न दाग, न बदबू, न बार-बार पानी बहाने की ज़रूरत।
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            @if($sellerPage->whatsapp_number)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sellerPage->whatsapp_number) }}?text={{ urlencode('Namaste! Mujhe Tornado Flush One-Piece Commode models ke rates aur video demo chahiye.') }}" 
                   target="_blank" 
                   class="px-5 py-3 rounded-2xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-black text-xs shadow-lg transition flex items-center gap-2 cursor-pointer">
                    <i class="fa-brands fa-whatsapp text-base"></i>
                    <span>टॉरनेडो मॉडल्स के रेट्स पूछें</span>
                </a>
            @endif
        </div>
    </div>
</div>

@elseif($type === 'project_package')
<!-- 📦 Complete Home / Hotel Sanitary Package Banner -->
<div class="my-8 rounded-3xl bg-gradient-to-r from-amber-950 via-slate-900 to-stone-900 border border-amber-500/40 p-6 sm:p-8 shadow-xl text-white relative overflow-hidden">
    <div class="absolute -left-10 top-0 w-60 h-60 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="flex flex-col md:flex-row items-center justify-between gap-6 relative z-10">
        <div class="space-y-2 text-center md:text-left">
            <div class="flex items-center justify-center md:justify-start gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-black text-[10px] border border-amber-500/30">
                    🏆 प्रोजेक्ट व बिल्डर पैकेज (BULK RATES)
                </span>
                <span class="text-[10px] text-slate-400 font-bold">होलसेल कोटेशन</span>
            </div>
            <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                नया मकान या फ्लैट्स बना रहे हैं? कम्प्लीट बाथरूम कॉम्बो पैकेज पाएं!
            </h3>
            <p class="text-xs text-slate-300 max-w-xl leading-relaxed">
                कमोड + वॉश बेसिन पेडेस्टल + सीट कवर + पीवीसी/ब्रास फिटिंग्स का पूरा सेट डायरेक्ट फैक्ट्री डिस्काउंट पर उपलब्ध। ट्रांसपोर्ट डिलीवरी और पक्के जीएसटी बिल की सुविधा।
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            @if($sellerPage->whatsapp_number)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sellerPage->whatsapp_number) }}?text={{ urlencode('Namaste! Hamare project/house construction ke liye complete bathroom sanitaryware quotation estimate banayein.') }}" 
                   target="_blank" 
                   class="px-5 py-3 rounded-2xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-black text-xs shadow-lg transition flex items-center gap-2 cursor-pointer">
                    <i class="fa-brands fa-whatsapp text-base"></i>
                    <span>प्रोजेक्ट कोटेशन (Instant WhatsApp Estimate)</span>
                </a>
            @endif
        </div>
    </div>
</div>
@endif
