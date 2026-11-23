<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>खाता विवरण - {{ $sellerPage->page_title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen py-8 px-4 flex items-center justify-center">

    <div class="max-w-lg w-full bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-6 sm:p-8 text-center relative">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400/20 text-amber-300 border border-amber-400/40 text-[11px] font-black uppercase mb-3">
                <i class="fa-solid fa-file-invoice"></i>
                <span>आधिकारिक डिजिटल खाता बही</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight leading-tight">
                {{ $sellerPage->page_title }}
            </h1>
            <p class="text-xs text-slate-300 mt-1">
                {{ $sellerPage->address ? $sellerPage->address . ', ' : '' }}{{ $sellerPage->city ?: 'सारंगढ़' }}{{ $sellerPage->pincode ? ' - ' . $sellerPage->pincode : '' }}
            </p>
            @if($sellerPage->support_phone || $sellerPage->whatsapp_number)
                <div class="text-xs font-mono font-bold text-amber-300 mt-1 flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-phone text-[10px]"></i>
                    <span>संपर्क: {{ $sellerPage->support_phone ?: $sellerPage->whatsapp_number }}</span>
                </div>
            @endif
        </div>

        <!-- Statement Body -->
        <div class="p-6 sm:p-8 space-y-6">
            <!-- Customer Greeting -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-black uppercase text-slate-500 tracking-wider">ग्राहक / पार्टी का नाम</span>
                    <h2 class="text-base font-extrabold text-slate-900 mt-0.5">{{ $customerName }}</h2>
                    @if($customerPhone)
                        <span class="text-xs text-slate-500 font-mono">{{ $customerPhone }}</span>
                    @endif
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-black uppercase text-slate-500 tracking-wider">दिनांक</span>
                    <div class="text-xs font-black text-slate-700 mt-0.5">{{ $date }}</div>
                </div>
            </div>

            <!-- Amount Due Card -->
            <div class="p-6 rounded-3xl bg-gradient-to-br from-amber-50 via-rose-50/40 to-amber-50 border-2 border-amber-300 text-center shadow-xs">
                <span class="text-xs font-black text-amber-800 uppercase tracking-wider flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-scale-balanced text-amber-600"></i>
                    <span>वर्तमान कुल बकाया राशि ({{ $balanceType }})</span>
                </span>
                <div class="text-3xl sm:text-4xl font-black text-slate-900 mt-2 font-mono tracking-tight">
                    ₹{{ number_format($amountDue, 2) }}
                </div>
                <p class="text-xs text-slate-500 mt-2">
                    कृपया अपने खाते के हिसाब का मिलान कर लें।
                </p>

                <!-- 1-Click UPI Payment Button -->
                @if($sellerPage->upi_id)
                    @php
                        $upiPayUrl = "upi://pay?pa=" . urlencode($sellerPage->upi_id) . "&pn=" . urlencode($sellerPage->page_title) . "&am=" . urlencode($amountDue) . "&cu=INR&tn=" . urlencode("Khata Payment from " . $customerName);
                    @endphp
                    <div class="mt-4 pt-4 border-t border-amber-200/80">
                        <a href="{{ $upiPayUrl }}" class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-mobile-screen"></i>
                            <span>₹{{ number_format($amountDue, 2) }} सीधे UPI से अभी जमा करें (PhonePe / GPay / Paytm)</span>
                        </a>
                        <span class="text-[10px] text-slate-400 block mt-1">UPI ID: {{ $sellerPage->upi_id }}</span>
                    </div>
                @endif
            </div>

            <!-- 📍 Customer GPS Location Lock (वैकल्पिक / Optional) -->
            <div class="bg-indigo-50/60 border border-indigo-200 rounded-3xl p-5 space-y-3">
                <div class="flex items-start gap-3">
                    <div class="h-9 w-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-sm shadow-xs shrink-0 mt-0.5">
                        <i class="fa-solid fa-map-pin"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-black text-slate-900 flex items-center gap-1.5">
                            <span>दुकान / साइट का GPS लोकेशन लिंक करें</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-indigo-100 text-indigo-700">वैकल्पिक (Optional)</span>
                        </h3>
                        <p class="text-[11px] text-slate-600 mt-0.5 leading-relaxed">
                            यदि आप चाहते हैं कि अगली बार माल आपके सही पते/साइट पर बिना फोन किए सीधे पहुँचे, तो नीचे बटन दबाकर अपनी लोकेशन लॉक कर सकते हैं।
                        </p>
                    </div>
                </div>

                <div id="gps_lock_action_area">
                    <button type="button" onclick="lockCustomerGps()" id="btn_customer_gps_lock" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs transition">
                        <i class="fa-solid fa-crosshairs text-amber-400"></i>
                        <span>📍 मेरी वर्तमान साइट / दुकान का GPS लोकेशन लॉक करें</span>
                    </button>
                </div>

                <div id="gps_lock_success" class="hidden p-3 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-bold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-base text-emerald-600"></i>
                    <span>आपकी लोकेशन श्री गणेश ट्रेडर्स के पास सफलतापूर्वक दर्ज हो गई है! 🙏</span>
                </div>
            </div>

            <!-- WhatsApp Query Button -->
            @if($sellerPage->whatsapp_number)
                @php
                    $waHelpMsg = urlencode("नमस्ते {$sellerPage->page_title}! मेरे खाता विवरण (₹{$amountDue}) के संबंध में मुझे जानकारी चाहिए। - {$customerName}");
                @endphp
                <a href="https://wa.me/{{ $sellerPage->clean_whatsapp_number }}?text={{ $waHelpMsg }}" target="_blank" class="w-full py-3 px-4 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs flex items-center justify-center gap-2 shadow-2xs transition">
                    <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
                    <span>हिसाब मिलान या बिल कॉपी के लिए WhatsApp पर चैट करें</span>
                </a>
            @endif

            <!-- Store Catalog Link -->
            <div class="text-center pt-2">
                <a href="{{ route('minisite.show', $sellerPage->slug) }}" class="text-xs font-bold text-indigo-600 hover:underline">
                    दुकान के अन्य प्रोडक्ट व ताज़ा रेट देखने के लिए यहाँ क्लिक करें &rarr;
                </a>
            </div>
        </div>

        <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 text-center text-[11px] text-slate-400">
            Powered by VyaparIndia Verified Digital Khata
        </div>
    </div>

    <script>
        function lockCustomerGps() {
            const btn = document.getElementById('btn_customer_gps_lock');
            if (!navigator.geolocation) {
                alert('आपके ब्राउज़र में GPS लोकेशन सपोर्ट नहीं है।');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> लोकेशन खोजी जा रही है...';

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    fetch("{{ route('khata.save_gps') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            seller_slug: '{{ $sellerPage->slug }}',
                            customer_name: '{{ addslashes($customerName) }}',
                            customer_phone: '{{ addslashes($customerPhone) }}',
                            latitude: lat,
                            longitude: lng
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('gps_lock_action_area').classList.add('hidden');
                        document.getElementById('gps_lock_success').classList.remove('hidden');
                    })
                    .catch(err => {
                        btn.disabled = false;
                        btn.innerHTML = '📍 मेरी साइट का लोकेशन लॉक करें';
                        alert('लोकेशन सेव करने में त्रुटि: ' + err.message);
                    });
                },
                function(err) {
                    btn.disabled = false;
                    btn.innerHTML = '📍 पुनः प्रयास करें';
                    alert('GPS लोकेशन प्राप्त नहीं हो सकी: ' + err.message + '। कृपया लोकेशन परमिशन चालू करें।');
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }
    </script>
</body>
</html>
