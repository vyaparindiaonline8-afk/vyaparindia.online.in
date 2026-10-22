<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Label #{{ $order->order_number ?: $order->id }} - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @page {
            size: 100mm 150mm;
            margin: 0;
        }
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .label-container {
                width: 100mm !important;
                min-height: 145mm !important;
                box-shadow: none !important;
                border: 2px solid #000 !important;
                margin: 0 auto !important;
                page-break-after: always;
            }
        }
        .barcode-lines {
            background: repeating-linear-gradient(
                90deg,
                #000,
                #000 2px,
                #fff 2px,
                #fff 4px,
                #000 4px,
                #000 7px,
                #fff 7px,
                #fff 9px,
                #000 9px,
                #000 12px
            );
            height: 40px;
            width: 100%;
        }
    </style>
</head>
<body class="bg-gray-100 text-black min-h-screen flex flex-col items-center py-6 px-4 font-sans antialiased">

    <!-- Top Action Bar (Screen Only) -->
    <div class="no-print max-w-[100mm] w-full flex items-center justify-between mb-4 bg-white p-3 rounded-2xl shadow-sm border border-gray-200">
        <a href="{{ route('seller.orders.show', $order) }}" class="text-xs font-bold text-gray-600 hover:text-gray-900 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back</span>
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-xl shadow-xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-print"></i>
                <span>Print Thermal (4x6")</span>
            </button>
        </div>
    </div>

    @php
        $custName = $order->customer_name ?: ($order->buyer->name ?? 'Customer');
        $custPhone = $order->customer_phone ?: ($order->buyer->phone_number ?? '');
        $sellerName = $sellerProfile->business_name ?? ($seller->name ?? 'Verified Seller');
        $sellerPhone = $sellerProfile->phone ?? ($seller->phone_number ?? '');
        $sellerAddress = $sellerProfile->registered_address ?? ($sellerProfile->city ?? 'India');
        $sellerGstin = $sellerProfile->gstin ?? 'N/A';
        $channel = strtolower($order->order_source ?? 'vyaparindia');
        $totalQty = $order->products->sum(fn($p) => $p->pivot->quantity ?? 1);
        $orderNum = $order->order_number ?: 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
        $trackingNum = $order->dispatch->tracking_number ?? ('VP-' . strtoupper(substr(md5($order->id . $order->created_at), 0, 10)));
    @endphp

    <!-- 4" x 6" Thermal Shipping Sticker -->
    <div class="label-container w-[100mm] min-h-[145mm] bg-white border-2 border-black p-3.5 flex flex-col justify-between text-black text-xs shadow-md">
        
        <!-- Header: Courier / Channel Brand & Routing Code -->
        <div class="border-b-2 border-black pb-2">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1.5">
                    @if($channel === 'amazon')
                        <span class="text-xs font-black tracking-wider uppercase bg-amber-100 text-amber-900 border border-black px-2 py-0.5 rounded">
                            🟠 AMAZON EASY-SHIP
                        </span>
                    @elseif($channel === 'meesho')
                        <span class="text-xs font-black tracking-wider uppercase bg-pink-100 text-pink-900 border border-black px-2 py-0.5 rounded">
                            🟣 MEESHO LOGISTICS
                        </span>
                    @elseif($channel === 'flipkart')
                        <span class="text-xs font-black tracking-wider uppercase bg-blue-100 text-blue-900 border border-black px-2 py-0.5 rounded">
                            🟡 FLIPKART EKART
                        </span>
                    @else
                        <span class="text-xs font-black tracking-wider uppercase bg-indigo-100 text-indigo-900 border border-black px-2 py-0.5 rounded">
                            🔵 VYAPARINDIA EXPRESS
                        </span>
                    @endif
                </div>
                <div class="text-right">
                    <div class="text-[10px] font-bold text-gray-500 uppercase">HUB ROUTE</div>
                    <div class="text-sm font-black tracking-tight">DEL / {{ strtoupper(substr($order->city ?: 'HUB', 0, 4)) }}-01</div>
                </div>
            </div>

            <!-- Barcode Graphic Simulation -->
            <div class="mt-2 text-center">
                <div class="barcode-lines rounded-xs"></div>
                <div class="text-[10px] font-mono font-bold tracking-widest mt-0.5">{{ $trackingNum }}</div>
            </div>
        </div>

        <!-- Destination / Delivery Address -->
        <div class="border-b-2 border-black py-2.5">
            <div class="text-[9px] font-black uppercase text-gray-700 tracking-wider">DELIVER TO:</div>
            <div class="text-sm font-black mt-0.5 leading-tight">{{ $custName }}</div>
            <div class="text-[11px] font-medium mt-1 leading-snug">
                {{ $order->shipping_address ?: 'Customer Address' }}
            </div>
            <div class="mt-1 flex items-baseline justify-between">
                <div class="text-[11px] font-bold">
                    {{ $order->city ? $order->city . ', ' : '' }}{{ $order->state ?? 'India' }}
                </div>
                <div class="text-base font-black border-2 border-black px-2 py-0.5 rounded bg-gray-50">
                    PIN: {{ $order->pincode ?: 'DIRECT' }}
                </div>
            </div>
            @if($custPhone)
                <div class="text-[10px] font-bold mt-1">
                    Contact: <span class="font-mono">{{ $custPhone }}</span>
                </div>
            @endif
        </div>

        <!-- Payment Mode Box (High Contrast) -->
        <div class="py-2 border-b-2 border-black">
            @if(strtolower($order->payment_method ?? '') === 'cod')
                <div class="bg-black text-white p-2 rounded text-center">
                    <div class="text-[10px] font-bold uppercase tracking-wider">CASH ON DELIVERY (COD)</div>
                    <div class="text-lg font-black tracking-tight">COLLECT ₹{{ number_format($order->total_price, 2) }}</div>
                </div>
            @else
                <div class="border-2 border-black p-2 rounded text-center bg-gray-50">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-gray-700">PREPAID ORDER</div>
                    <div class="text-base font-black text-black">DO NOT COLLECT CASH (PAID ONLINE)</div>
                </div>
            @endif
        </div>

        <!-- Item Checklist & SKU Summary -->
        <div class="py-2 border-b-2 border-black text-[10px]">
            <div class="flex items-center justify-between font-bold text-gray-600 uppercase border-b border-gray-200 pb-1">
                <span>Item SKU / Name</span>
                <span>Qty</span>
            </div>
            <div class="space-y-1 mt-1 max-h-20 overflow-hidden">
                @foreach($order->products as $item)
                    <div class="flex items-center justify-between">
                        <span class="truncate max-w-[200px] font-semibold">{{ $item->sku ?: 'SKU' }} - {{ $item->name }}</span>
                        <span class="font-black font-mono">x{{ $item->pivot->quantity ?? 1 }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-1.5 pt-1 border-t border-dashed border-gray-300 flex justify-between font-bold text-gray-700">
                <span>Total Items: {{ $totalQty }}</span>
                <span>Order: #{{ $orderNum }}</span>
            </div>
        </div>

        <!-- Return / Shipped From Box & QR Code -->
        <div class="pt-2 flex items-start justify-between gap-2">
            <div class="text-[9px] leading-tight flex-1">
                <div class="font-black uppercase text-gray-600">IF UNDELIVERED, RETURN TO:</div>
                <div class="font-black text-[10px] mt-0.5">{{ $sellerName }}</div>
                <div class="text-gray-700 mt-0.5">{{ $sellerAddress }}</div>
                <div class="text-gray-700 mt-0.5">Phone: {{ $sellerPhone ?: 'N/A' }} | GSTIN: {{ $sellerGstin }}</div>
            </div>

            <!-- Dynamic QR Code (Scannable for Delivery Agent) -->
            <div class="shrink-0 text-center">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=64x64&data={{ urlencode(route('seller.orders.show', $order)) }}" 
                     alt="Order QR" 
                     class="w-14 h-14 border border-black p-0.5 rounded"
                     onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'64\' height=\'64\' fill=\'none\' stroke=\'black\' stroke-width=\'2\'><rect x=\'2\' y=\'2\' width=\'60\' height=\'60\'/><text x=\'32\' y=\'36\' font-size=\'10\' text-anchor=\'middle\' fill=\'black\'>QR</text></svg>'">
                <div class="text-[8px] font-mono mt-0.5">SCAN TO VERIFY</div>
            </div>
        </div>

    </div>

    <!-- Thermal Printer Instructions Note (Screen Only) -->
    <div class="no-print mt-4 max-w-[100mm] w-full text-center text-xs text-gray-500">
        <p><i class="fa-solid fa-lightbulb text-amber-500 mr-1"></i> <b>Thermal Printing Tip:</b> Set print scale to <b>100%</b> and paper size to <b>4" x 6" (100mm x 150mm)</b>.</p>
    </div>

</body>
</html>
