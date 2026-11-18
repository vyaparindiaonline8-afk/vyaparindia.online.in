@php
    $brandMeta = [
        'cava' => [
            'name' => 'Cava Sanitaryware',
            'tagline' => 'Modern Bath Living • Ultra-Smooth Ceramic Glaze & Tornado 360° Flush Technology',
            'tier' => 'वैल्यू फॉर मनी / मीडियम प्रीमियम',
            'tier_class' => 'bg-emerald-50 text-emerald-800 border-emerald-300',
            'bg_gradient' => 'from-emerald-950 via-slate-900 to-teal-950',
            'border_color' => 'border-emerald-500/40',
            'accent_color' => 'text-emerald-400',
            'features' => ['✨ 360° Tornado Siphonic Flush', '🛡️ 10-Yr Ceramic Glaze Warranty', '💧 60% Water-Saving Dual Flow', '⚡ Soft-Close Hydraulic Seat'],
        ],
        'cera' => [
            'name' => 'CERA Sanitaryware & Tiles',
            'tagline' => 'Style Meets Innovation • Luxury Italian Designer Bathware & Rimless Closets',
            'tier' => 'लग्ज़री डिज़ाइनर रेंज',
            'tier_class' => 'bg-amber-50 text-amber-900 border-amber-300',
            'bg_gradient' => 'from-slate-950 via-stone-900 to-amber-950/60',
            'border_color' => 'border-amber-500/40',
            'accent_color' => 'text-amber-400',
            'features' => ['💎 Italian Design Aesthetics', '🛡️ Anti-Bacterial Nano-Coat', '🧱 Slim Wall-Hung Closets', '✨ Mirror Finish Table-Top Basins'],
        ],
        'jaquar' => [
            'name' => 'Jaquar Bath & Sanitary',
            'tagline' => 'Complete Bathroom Solutions • World-Class Engineering & Unmatched Elegance',
            'tier' => 'सुपर प्रीमियम लग्ज़री',
            'tier_class' => 'bg-purple-50 text-purple-900 border-purple-300',
            'bg_gradient' => 'from-slate-950 via-indigo-950 to-slate-900',
            'border_color' => 'border-indigo-500/40',
            'accent_color' => 'text-indigo-400',
            'features' => ['👑 10 Years Comprehensive Warranty', '⚡ Silent Fill Float Valves', '💧 Eco-Flush 3/6 Litres', '🛠️ Pan-India Service Support'],
        ],
        'florex' => [
            'name' => 'Florex Sanitary & PTMT',
            'tagline' => 'Heavy Duty Engineering • Break-Resistant PTMT & Ceramic Slim Cistern Solutions',
            'tier' => 'बेस्ट सेलर • इंडस्ट्रियल ड्यूरेबिलिटी',
            'tier_class' => 'bg-blue-50 text-blue-900 border-blue-300',
            'bg_gradient' => 'from-blue-950 via-slate-900 to-cyan-950',
            'border_color' => 'border-cyan-500/40',
            'accent_color' => 'text-cyan-400',
            'features' => ['💪 Unbreakable Heavy PTMT', '⚡ Dual Flow Slim Tanks', '🚿 Zero Corrosion Brass Core', '📦 Safe Master Box Packing'],
        ],
        'astral' => [
            'name' => 'Astral Pipes & Bathware',
            'tagline' => 'India\'s Trusted Plumbing & Sanitary Brand • Leak-Proof High Pressure Systems',
            'tier' => 'नंबर 1 प्लंबिंग व बाथवेअर',
            'tier_class' => 'bg-red-50 text-red-900 border-red-300',
            'bg_gradient' => 'from-red-950 via-slate-900 to-slate-950',
            'border_color' => 'border-red-500/40',
            'accent_color' => 'text-red-400',
            'features' => ['🔥 Lead-Free UPVC / CPVC', '⚡ ASTM Certified Quality', '🛡️ High Impact Resistance', '🏢 High Rise Certified'],
        ],
        'plasto' => [
            'name' => 'Plasto Tanks & Fittings',
            'tagline' => 'Rakhhe Sabko Fit • Premium Water Tanks, Brass Fittings & Sanitary Accessories',
            'tier' => 'प्रूवन ड्यूरेबिलिटी',
            'tier_class' => 'bg-amber-50 text-amber-900 border-amber-300',
            'bg_gradient' => 'from-amber-950 via-slate-900 to-orange-950',
            'border_color' => 'border-amber-500/40',
            'accent_color' => 'text-amber-400',
            'features' => ['🏅 100% Virgin Grade Polymer', '☀️ UV Resistant 6 Layers', '💧 Food-Grade Safe Water', '⭐ 10 Years Replacement Guarantee'],
        ],
    ];

    $brandKey = strtolower(trim($brandName ?? ''));
    $currentMeta = $brandMeta[$brandKey] ?? [
        'name' => ($brandName ?: 'Authorized Brand') . ' Collection',
        'tagline' => 'Premium Quality Ceramic & Sanitary Solutions • Verified Wholesaler & Authorized Stockist',
        'tier' => 'अधिकृत डीलरशिप • 100% ऑरिजनल',
        'tier_class' => 'bg-indigo-50 text-indigo-900 border-indigo-300',
        'bg_gradient' => 'from-slate-900 via-indigo-950 to-slate-900',
        'border_color' => 'border-indigo-500/40',
        'accent_color' => 'text-cyan-400',
        'features' => ['⭐ 100% Original Products', '🏷️ Direct Factory Wholesale Rates', '📦 Safe Delivery & Transport', '💬 Instant WhatsApp Quote'],
    ];

    $waInquiryMsg = urlencode("Namaste! Mujhe aapke store par " . $currentMeta['name'] . " ke catalog, models aur wholesale rates ki details chahiye.");
@endphp

<!-- 🌟 Luxury Brand Showcase Billboard -->
<div class="mb-8 relative overflow-hidden rounded-3xl bg-gradient-to-r {{ $currentMeta['bg_gradient'] }} border {{ $currentMeta['border_color'] }} p-6 sm:p-8 shadow-2xl text-white">
    
    <!-- Ambient Atmospheric Glows -->
    <div class="absolute top-0 right-0 w-80 h-80 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-10 left-10 w-60 h-60 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>

    <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
        
        <!-- Left: Brand Emblem & Messaging -->
        <div class="space-y-3.5 max-w-2xl">
            <div class="flex items-center gap-2.5 flex-wrap">
                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $currentMeta['tier_class'] }} shadow-xs">
                    {{ $currentMeta['tier'] }}
                </span>
                <span class="text-[10px] font-bold text-slate-300 flex items-center gap-1">
                    <i class="fa-solid fa-circle-check text-emerald-400"></i> अधिकृत स्टॉक (Authorized Stockist)
                </span>
            </div>

            <div>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight flex items-center gap-3">
                    <span>{{ $currentMeta['name'] }}</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 leading-relaxed font-medium">
                    {{ $currentMeta['tagline'] }}
                </p>
            </div>

            <!-- 4 Luxury Feature Badges -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1 text-[11px] font-bold text-slate-200">
                @foreach($currentMeta['features'] as $feat)
                    <div class="px-2.5 py-1.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/10 flex items-center gap-1.5">
                        <span class="truncate">{{ $feat }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right: Action CTA & WhatsApp Inquiry -->
        <div class="flex flex-col sm:flex-row lg:flex-col gap-2.5 shrink-0 w-full sm:w-auto">
            @if($sellerPage->whatsapp_number)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sellerPage->whatsapp_number) }}?text={{ $waInquiryMsg }}" 
                   target="_blank" 
                   class="px-5 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 active:scale-98 text-slate-950 font-black text-xs shadow-lg flex items-center justify-center gap-2 transition cursor-pointer">
                    <i class="fa-brands fa-whatsapp text-base"></i>
                    <span>WhatsApp कैटलॉग व रेट्स मांगें</span>
                </a>
            @endif

            <a href="#brand-catalog-anchor" 
               class="px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/20 active:scale-98 text-white font-bold text-xs border border-white/15 flex items-center justify-center gap-2 transition">
                <i class="fa-solid fa-arrow-down text-xs {{ $currentMeta['accent_color'] }}"></i>
                <span>सभी मॉडल्स नीचे देखें</span>
            </a>
        </div>

    </div>
</div>
<div id="brand-catalog-anchor"></div>
