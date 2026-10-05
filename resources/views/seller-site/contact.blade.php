@extends('seller-site.layout')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    @php
        $sellerProfile = $sellerPage->user->sellerProfile ?? null;
        $bgImage = ($sellerProfile && $sellerProfile->background_image) ? $sellerProfile->background_image : ($sellerPage->banner_url ?? null);
    @endphp

    <!-- 📇 Digital Business Visiting Card Layout -->
    <div class="mb-10 bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-10 text-white shadow-xl border border-indigo-500/20 relative overflow-hidden">
        @if($bgImage)
            <div class="absolute inset-0 bg-cover bg-center opacity-25 pointer-events-none" style="background-image: url('{{ $bgImage }}');"></div>
        @endif
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-400/30 text-[10px] font-black tracking-wider uppercase">
                    <i class="fa-solid fa-address-card"></i> Official Business Card & Contact
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white flex items-center gap-3">
                    <span>{{ $sellerPage->page_title }}</span>
                    @if($sellerProfile && $sellerProfile->gst_number)
                        <span class="text-[10px] bg-emerald-500 text-white font-black px-2.5 py-0.5 rounded-full">GST Verified</span>
                    @endif
                </h1>
                @if($sellerPage->tagline)
                    <p class="text-xs text-indigo-200 font-medium">{{ $sellerPage->tagline }}</p>
                @endif
                <p class="text-xs text-slate-300 max-w-xl leading-relaxed">
                    {{ $sellerPage->address ?? '' }}{{ $sellerPage->city ? ', ' . $sellerPage->city : '' }}{{ $sellerPage->pincode ? ' - ' . $sellerPage->pincode : '' }}
                </p>

                <div class="flex items-center gap-3 flex-wrap pt-2 text-xs text-slate-200">
                    @if($sellerPage->whatsapp_number)
                        <a href="https://wa.me/{{ $sellerPage->clean_whatsapp_number }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition">
                            <i class="fa-brands fa-whatsapp text-sm"></i> +{{ $sellerPage->clean_whatsapp_number }} (Order)
                        </a>
                    @endif
                    @if($sellerPage->support_phone)
                        <a href="tel:{{ $sellerPage->support_phone }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-medium transition">
                            <i class="fa-solid fa-phone text-blue-400"></i> {{ $sellerPage->support_phone }}
                        </a>
                    @endif
                    @if($sellerPage->support_email)
                        <a href="mailto:{{ $sellerPage->support_email }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-medium transition">
                            <i class="fa-solid fa-envelope text-indigo-400"></i> {{ $sellerPage->support_email }}
                        </a>
                    @endif
                </div>
            </div>

            <!-- QR Code on Visiting Card -->
            <div class="shrink-0 flex flex-col items-center gap-2 bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10">
                <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">Scan Store QR</span>
                <div class="h-20 w-20 bg-white rounded-xl p-1.5 flex items-center justify-center">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(url()->current()) }}" alt="Store QR" class="w-full h-full object-contain">
                </div>
                <span class="text-[9px] text-slate-400">Share Store Card</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Store Contact Details -->
        <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 space-y-6">
            <h2 class="text-lg font-bold text-gray-900 pb-3 border-b border-gray-100 flex items-center gap-2">
                <i class="fa-solid fa-store text-brand-custom"></i>
                <span>Store Information</span>
            </h2>

            <div class="space-y-4 text-xs sm:text-sm text-gray-700">
                @if($sellerPage->address)
                    <div class="flex items-start gap-3">
                        <div class="h-8 w-8 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">Registered Address</div>
                            <div class="text-gray-500 mt-0.5">{{ $sellerPage->address }}</div>
                            <div class="text-gray-500">{{ $sellerPage->city }}{{ $sellerPage->pincode ? ' - ' . $sellerPage->pincode : '' }}</div>
                        </div>
                    </div>
                @endif

                @if($sellerPage->whatsapp_number)
                    <div class="flex items-start gap-3">
                        <div class="h-8 w-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <i class="fa-brands fa-whatsapp text-base"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">WhatsApp Chat</div>
                            <a href="https://wa.me/{{ $sellerPage->clean_whatsapp_number }}" target="_blank" class="text-emerald-600 font-semibold hover:underline mt-0.5 block">
                                +{{ $sellerPage->clean_whatsapp_number }} (Direct Message)
                            </a>
                        </div>
                    </div>
                @endif

                @if($sellerPage->support_phone)
                    <div class="flex items-start gap-3">
                        <div class="h-8 w-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">Support Phone</div>
                            <a href="tel:{{ $sellerPage->support_phone }}" class="text-gray-600 hover:text-gray-900 mt-0.5 block">
                                {{ $sellerPage->support_phone }}
                            </a>
                        </div>
                    </div>
                @endif

                @if($sellerPage->support_email)
                    <div class="flex items-start gap-3">
                        <div class="h-8 w-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">Official Email</div>
                            <a href="mailto:{{ $sellerPage->support_email }}" class="text-gray-600 hover:text-gray-900 mt-0.5 block">
                                {{ $sellerPage->support_email }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- WhatsApp Big Action -->
            @if($sellerPage->whatsapp_number)
                <div class="pt-4 border-t border-gray-100">
                    <a href="https://wa.me/{{ $sellerPage->clean_whatsapp_number }}?text=Hello%20{{ urlencode($sellerPage->page_title) }}%2C%20I%20have%20an%20inquiry." target="_blank" class="w-full py-3.5 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs shadow-sm transition-all flex items-center justify-center gap-2">
                        <i class="fa-brands fa-whatsapp text-base"></i>
                        <span>Start Instant WhatsApp Chat</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- Store Policies (Shipping, Return, COD) -->
        <div id="policies" class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 space-y-6">
            <h2 class="text-lg font-bold text-gray-900 pb-3 border-b border-gray-100 flex items-center gap-2">
                <i class="fa-solid fa-file-shield text-brand-custom"></i>
                <span>Store Policies & Guarantees</span>
            </h2>

            <div class="space-y-4 text-xs text-gray-600 leading-relaxed">
                @if($sellerPage->policies)
                    <div class="whitespace-pre-line bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        {{ $sellerPage->policies }}
                    </div>
                @else
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-3">
                        <div>
                            <h4 class="font-bold text-gray-900">📦 Shipping & Delivery Policy</h4>
                            <p class="text-gray-500 mt-1">Orders are packed and dispatched within 24-48 business hours. Pan-India shipping with real-time tracking.</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">🔄 Return & Replacement</h4>
                            <p class="text-gray-500 mt-1">In case of damaged or defective products, please contact the seller on WhatsApp within 48 hours of delivery with unboxing proof.</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">💵 Payment Options</h4>
                            <p class="text-gray-500 mt-1">We accept Cash on Delivery (COD) and Online Payments (UPI, Cards, NetBanking).</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
