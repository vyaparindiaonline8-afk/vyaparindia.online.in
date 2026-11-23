<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sellerPage->page_title }} — Printable Table QR Standees</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .standee-card { page-break-inside: avoid; margin-bottom: 2rem; border: 2px solid #000 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased p-4 sm:p-8">

    <!-- Top Action Bar (Hidden when printing) -->
    <div class="max-w-4xl mx-auto mb-8 bg-white p-4 rounded-2xl shadow-sm border border-gray-200 no-print flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-base font-black text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-qrcode text-indigo-600"></i>
                <span>{{ $sellerPage->page_title }} — टेबल QR स्टैंडी व पोस्टर जनरेटर</span>
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">ग्राहकों के लिए टेबल पर रखने हेतु QR कार्ड। इसे प्रिंट कराकर टेबल या काउंटर पर लगाएँ।</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('seller.minisite.edit') }}" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition">
                &larr; वापस सेटिंग्स
            </a>
            <button type="button" onclick="window.print()" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs flex items-center gap-1.5 shadow-md transition">
                <i class="fa-solid fa-print"></i>
                <span>प्रिंट करें (Print / Save as PDF)</span>
            </button>
        </div>
    </div>

    <!-- Standees Grid Container -->
    <div class="max-w-4xl mx-auto space-y-8">
        
        @php
            $tablesToRender = [];
            if ($tableNumber === 'all' || empty($tableNumber)) {
                for ($i = 1; $i <= $totalTables; $i++) {
                    $tablesToRender[] = $i;
                }
            } else {
                $tablesToRender[] = intval($tableNumber);
            }
        @endphp

        <!-- 🛵 Standee 0: Cloud Kitchen / Takeaway Counter Standee -->
        <div class="standee-card bg-white max-w-sm mx-auto rounded-3xl p-6 sm:p-8 text-center border-4 border-amber-500 shadow-xl relative overflow-hidden">
            <div class="absolute top-0 inset-x-0 bg-gradient-to-r from-amber-500 via-orange-500 to-amber-500 text-white text-[11px] font-black uppercase tracking-widest py-1.5">
                🛵 Cloud Kitchen & Takeaway Orders
            </div>

            <div class="mt-4 mb-3 flex justify-center">
                @if($sellerPage->logo_url)
                    <img src="{{ $sellerPage->logo_url }}" alt="{{ $sellerPage->page_title }}" class="h-14 w-14 object-contain rounded-2xl border border-gray-200 p-1 bg-white shadow-xs">
                @else
                    <div class="h-14 w-14 rounded-2xl bg-amber-600 text-white flex items-center justify-center text-2xl shadow-xs">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                @endif
            </div>

            <h2 class="text-xl font-black text-gray-900 leading-tight">{{ $sellerPage->page_title }}</h2>
            <p class="text-xs text-gray-500 mt-1 font-semibold">{{ $sellerPage->tagline ?: 'Fresh & Delicious Food Prepared Daily' }}</p>

            <!-- QR Code Box -->
            @php
                $cloudUrl = url('/' . $sellerPage->slug . '?mode=delivery');
                $cloudQr = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($cloudUrl);
            @endphp
            <div class="my-5 p-4 rounded-2xl bg-amber-50/70 border-2 border-dashed border-amber-300 inline-block shadow-inner">
                <img src="{{ $cloudQr }}" alt="Cloud Kitchen Order QR" class="w-48 h-48 mx-auto rounded-xl">
            </div>

            <div class="space-y-1">
                <div class="text-xs font-black text-amber-900 flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-mobile-screen-button text-amber-600"></i>
                    <span>मोबाइल से स्कैन करें और घर पर मंगाएं!</span>
                </div>
                <p class="text-[10px] text-gray-400 font-mono">100% Contactless Online Food Ordering</p>
            </div>

            <div class="mt-5 pt-3 border-t border-gray-100 flex items-center justify-between text-[10px] font-bold text-gray-500">
                <span>WhatsApp: {{ $sellerPage->whatsapp_number ?: $sellerPage->support_phone }}</span>
                <span class="text-amber-700">VyaparIndia Smart Menu</span>
            </div>
        </div>

        <!-- 🪑 Table Dine-in Standees -->
        @foreach($tablesToRender as $tbl)
            @php
                $tableUrl = url('/' . $sellerPage->slug . '?table=' . $tbl . '&mode=dine_in');
                $tableQr = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($tableUrl);
            @endphp
            <div class="standee-card bg-white max-w-sm mx-auto rounded-3xl p-6 sm:p-8 text-center border-4 border-slate-900 shadow-xl relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 bg-slate-900 text-white text-[11px] font-black uppercase tracking-widest py-1.5 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-chair text-amber-400"></i>
                    <span>टेबल डाइन-इन स्टैंडी • Table Standee</span>
                </div>

                <div class="mt-4 mb-2 flex justify-center">
                    @if($sellerPage->logo_url)
                        <img src="{{ $sellerPage->logo_url }}" alt="{{ $sellerPage->page_title }}" class="h-12 w-12 object-contain rounded-2xl border border-gray-200 p-1 bg-white">
                    @else
                        <div class="h-12 w-12 rounded-2xl bg-slate-900 text-amber-400 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-utensils"></i>
                        </div>
                    @endif
                </div>

                <h2 class="text-lg font-black text-gray-900 leading-tight">{{ $sellerPage->page_title }}</h2>

                <!-- Big Table Number Badge -->
                <div class="my-2.5 inline-flex items-center gap-2 px-5 py-1.5 rounded-full bg-amber-500 text-slate-950 font-black text-sm uppercase tracking-wider shadow-sm">
                    <i class="fa-solid fa-utensils text-xs"></i>
                    <span>TABLE #{{ $tbl }}</span>
                </div>

                <!-- QR Code Box -->
                <div class="my-4 p-3 rounded-2xl bg-gray-50 border-2 border-dashed border-gray-300 inline-block">
                    <img src="{{ $tableQr }}" alt="Table {{ $tbl }} QR" class="w-48 h-48 mx-auto rounded-xl">
                </div>

                <div class="space-y-1">
                    <div class="text-xs font-black text-slate-900 flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-qrcode text-indigo-600"></i>
                        <span>स्कैन करें • मेनू देखें • यहीं से ऑर्डर करें</span>
                    </div>
                    <p class="text-[10px] text-gray-500">Scan via Camera or GPay / PhonePe to view live menu</p>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[10px] font-bold text-gray-500">
                    <span>{{ $sellerPage->city ?: 'सारंगढ़' }}</span>
                    <span class="text-indigo-600">Powered by VyaparIndia</span>
                </div>
            </div>
        @endforeach

    </div>
</body>
</html>
