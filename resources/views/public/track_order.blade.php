<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>लाइव ऑर्डर व बारकोड ट्रैकिंग (Track Order) - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
</head>
<body class="bg-slate-100 text-gray-800 antialiased min-h-screen flex flex-col">

    <!-- Top Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white font-black flex items-center justify-center text-base shadow-sm">V</div>
                <div>
                    <span class="font-black text-gray-900 text-base">VyaparIndia</span>
                    <span class="ml-1.5 text-[10px] font-bold bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full border border-indigo-200">Live Order Track</span>
                </div>
            </a>
            <a href="{{ route('home') }}" class="text-xs font-bold text-gray-600 hover:text-indigo-600 flex items-center gap-1">
                <i class="fa-solid fa-house"></i>
                <span>होम</span>
            </a>
        </div>
    </header>

    <main class="max-w-4xl w-full mx-auto px-4 sm:px-6 py-8 flex-1 space-y-6">

        <!-- Search Box Card -->
        <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-indigo-500/30 space-y-4">
            <div class="text-center space-y-1">
                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] font-black uppercase tracking-wider">
                    📦 Order & Barcode Tracking
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-white mt-2">अपना ऑर्डर या पार्सल बारकोड ट्रैक करें</h1>
                <p class="text-xs text-indigo-200">पता करें कि आपका सामान कब पैक हुआ, कब डिस्पैच निकला, पेमेंट स्टेटस क्या है और कब तक मिलेगा।</p>
            </div>

            <form method="GET" action="{{ route('public.track_order') }}" class="max-w-xl mx-auto flex flex-col sm:flex-row gap-2.5 pt-2">
                <div class="relative flex-1">
                    <i class="fa-solid fa-barcode absolute left-3.5 top-1/2 -translate-y-1/2 text-indigo-300 text-base"></i>
                    <input type="text" name="q" value="{{ $query }}" required placeholder="Order ID (उदा. ORD-000012) या Barcode / AWB नंबर डालें..." class="w-full pl-10 pr-4 py-3 rounded-2xl bg-white text-gray-900 placeholder-gray-400 text-sm font-mono font-bold focus:ring-2 focus:ring-emerald-400 focus:outline-none">
                </div>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-slate-950 font-black text-sm shadow-lg transition flex items-center justify-center gap-2 shrink-0 cursor-pointer">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>ट्रैक करें</span>
                </button>
            </form>
        </div>

        @if($notFound)
            <div class="bg-white rounded-3xl border border-rose-200 p-8 text-center shadow-sm space-y-2">
                <div class="h-14 w-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto text-2xl">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <h2 class="font-black text-base text-gray-900">ऑर्डर "#{{ $query }}" नहीं मिला</h2>
                <p class="text-xs text-gray-500 max-w-md mx-auto">कृपया सही Order ID (जैसे ORD-000123) या पार्सल पर छपा Barcode / Tracking नंबर चेक करके दोबारा डालें।</p>
            </div>
        @elseif($order)
            @php
                $orderNum = $order->order_number ?: 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
                $trackingNum = $order->dispatch->tracking_number ?? ('VP-' . strtoupper(substr(md5($order->id . $order->created_at), 0, 10)));
                $courierName = $order->dispatch->courier_name ?? 'VyaparIndia Seller Dispatch';
                $statusLower = strtolower($order->status ?? 'pending');
                $step = match($statusLower) {
                    'processing', 'packed' => 2,
                    'shipped', 'dispatched', 'in_transit' => 3,
                    'delivered', 'completed' => 4,
                    'cancelled', 'rto', 'returned' => 0,
                    default => 1,
                };
                $isPaid = strtolower($order->payment_status ?? '') === 'paid' || (strtolower($order->payment_method ?? 'cod') !== 'cod');
            @endphp

            <!-- Live Order Tracking Result Card -->
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6 space-y-6">

                <!-- Top Row: Order ID, Payment Status & Code128 Barcode -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 pb-5">
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-bold text-gray-500 uppercase">Order ID:</span>
                            <span class="font-mono font-black text-lg text-indigo-700">#{{ $orderNum }}</span>
                            @if($isPaid)
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 text-[11px] font-black">
                                    ✅ पेमेंट हो चुका है (Prepaid / Paid)
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300 text-[11px] font-black">
                                    💵 Cash on Delivery (देय: ₹{{ number_format($order->total_price, 2) }})
                                </span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-600 font-semibold">
                            <span>कूरियर / माध्यम: <strong class="text-gray-900">{{ $courierName }}</strong></span>
                            <span class="mx-2 text-gray-300">|</span>
                            <span>ग्राहक: <strong class="text-gray-900">{{ $order->customer_name ?: 'Customer' }}</strong> ({{ $order->city ?: 'India' }})</span>
                        </div>
                    </div>

                    <!-- Real Code128 Scannable Barcode -->
                    <div class="bg-slate-50 border border-gray-200 rounded-2xl px-4 py-2 text-center shrink-0">
                        <svg id="trackPageBarcode" class="mx-auto"></svg>
                    </div>
                </div>

                <!-- 4-Step Live Status Timeline -->
                @if($step === 0)
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-bold flex items-center gap-2">
                        <i class="fa-solid fa-ban text-rose-600 text-base"></i>
                        <span>इस ऑर्डर की वर्तमान स्थिति: <strong class="uppercase">{{ $order->status }}</strong> ({{ $order->updated_at ? $order->updated_at->format('d M Y, h:i A') : '' }})</span>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <!-- Step 1: Placed -->
                        <div class="p-4 rounded-2xl border {{ $step >= 1 ? 'bg-emerald-50/70 border-emerald-300' : 'bg-gray-50 border-gray-200 opacity-60' }} space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="h-7 w-7 rounded-full {{ $step >= 1 ? 'bg-emerald-600 text-white' : 'bg-gray-300 text-gray-600' }} flex items-center justify-center text-xs font-black">
                                    <i class="fa-solid fa-check"></i>
                                </span>
                                <span class="text-[10px] font-black {{ $step >= 1 ? 'text-emerald-700' : 'text-gray-400' }}">STEP 1</span>
                            </div>
                            <h3 class="font-black text-xs text-gray-900 pt-1">ऑर्डर दर्ज हुआ</h3>
                            <p class="text-[10px] text-gray-600 font-mono">{{ $order->created_at ? $order->created_at->format('d M Y, h:i A') : '-' }}</p>
                        </div>

                        <!-- Step 2: Packed -->
                        <div class="p-4 rounded-2xl border {{ $step >= 2 ? 'bg-blue-50/70 border-blue-300' : 'bg-gray-50 border-gray-200 opacity-60' }} space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="h-7 w-7 rounded-full {{ $step >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600' }} flex items-center justify-center text-xs font-black">
                                    <i class="fa-solid fa-box-open"></i>
                                </span>
                                <span class="text-[10px] font-black {{ $step >= 2 ? 'text-blue-700' : 'text-gray-400' }}">STEP 2</span>
                            </div>
                            <h3 class="font-black text-xs text-gray-900 pt-1">सेलर द्वारा पैक</h3>
                            <p class="text-[10px] text-gray-600 font-mono">
                                {{ $step >= 2 ? ($order->dispatch && $order->dispatch->created_at ? $order->dispatch->created_at->format('d M Y, h:i A') : $order->updated_at->format('d M Y, h:i A')) : 'पैकिंग प्रतीक्षारत...' }}
                            </p>
                        </div>

                        <!-- Step 3: Dispatched -->
                        <div class="p-4 rounded-2xl border {{ $step >= 3 ? 'bg-indigo-50/70 border-indigo-300' : 'bg-gray-50 border-gray-200 opacity-60' }} space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="h-7 w-7 rounded-full {{ $step >= 3 ? 'bg-indigo-600 text-white' : 'bg-gray-300 text-gray-600' }} flex items-center justify-center text-xs font-black">
                                    <i class="fa-solid fa-truck-fast"></i>
                                </span>
                                <span class="text-[10px] font-black {{ $step >= 3 ? 'text-indigo-700' : 'text-gray-400' }}">STEP 3</span>
                            </div>
                            <h3 class="font-black text-xs text-gray-900 pt-1">डिस्पैच (निकला)</h3>
                            <p class="text-[10px] text-gray-600 font-mono">
                                @if($step >= 3)
                                    {{ $order->dispatch && $order->dispatch->created_at ? $order->dispatch->created_at->format('d M Y, h:i A') : $order->updated_at->format('d M Y, h:i A') }}
                                @else
                                    डिस्पैच प्रतीक्षारत...
                                @endif
                            </p>
                        </div>

                        <!-- Step 4: Delivered -->
                        <div class="p-4 rounded-2xl border {{ $step >= 4 ? 'bg-emerald-50 border-emerald-400' : 'bg-gray-50 border-gray-200 opacity-60' }} space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="h-7 w-7 rounded-full {{ $step >= 4 ? 'bg-emerald-600 text-white' : 'bg-gray-300 text-gray-600' }} flex items-center justify-center text-xs font-black">
                                    <i class="fa-solid fa-house-circle-check"></i>
                                </span>
                                <span class="text-[10px] font-black {{ $step >= 4 ? 'text-emerald-700' : 'text-gray-400' }}">STEP 4</span>
                            </div>
                            <h3 class="font-black text-xs text-gray-900 pt-1">कस्टमर को मिला</h3>
                            <p class="text-[10px] text-gray-600 font-mono">
                                {{ $step >= 4 ? $order->updated_at->format('d M Y, h:i A') : 'डिलीवरी रास्ते में...' }}
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Items Summary -->
                <div class="bg-gray-50 rounded-2xl border border-gray-200 p-4 space-y-2 text-xs">
                    <div class="font-black text-gray-800 flex items-center justify-between border-b border-gray-200 pb-2">
                        <span>📦 ऑर्डर किए गए आइटम्स ({{ $order->products->count() }})</span>
                        <span class="font-mono text-emerald-700 text-sm">कुल बिल: ₹{{ number_format($order->total_price, 2) }}</span>
                    </div>
                    <div class="divide-y divide-gray-200/60">
                        @foreach($order->products as $prod)
                            <div class="py-2 flex items-center justify-between">
                                <span class="font-bold text-gray-800">{{ $prod->name }}</span>
                                <span class="font-mono text-gray-600 font-bold">Qty: {{ $prod->pivot->quantity ?? 1 }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof JsBarcode !== 'undefined') {
                        JsBarcode("#trackPageBarcode", "{{ $trackingNum }}", {
                            format: "CODE128",
                            lineColor: "#0f172a",
                            width: 1.8,
                            height: 36,
                            displayValue: true,
                            fontSize: 11,
                            fontOptions: "bold",
                            margin: 2
                        });
                    }
                });
            </script>
        @endif

    </main>

</body>
</html>
