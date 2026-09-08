<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Thermal Shipping Labels (4x6)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background-color: white !important; margin: 0; padding: 0; }
            .label-page { page-break-after: always; width: 4in; height: 6in; padding: 0.25in; border: none !important; }
        }
        .thermal-label {
            width: 4in;
            min-height: 6in;
            border: 2px solid #000;
            padding: 12px;
            box-sizing: border-box;
            background: #fff;
            margin: 0 auto 20px auto;
            font-family: monospace;
        }
    </style>
</head>
<body class="bg-gray-200 text-black p-4">

    <!-- Top Action Bar -->
    <div class="no-print max-w-lg mx-auto bg-white p-4 rounded-2xl shadow-md mb-6 flex items-center justify-between">
        <div>
            <h1 class="font-bold text-sm">Bulk Thermal Labels (4x6 inch)</h1>
            <p class="text-xs text-gray-500">{{ $orders->count() }} Labels Ready to Print</p>
        </div>
        <button onclick="window.print()" class="px-5 py-2.5 bg-gray-900 text-white rounded-xl text-xs font-bold hover:bg-gray-800 flex items-center gap-2">
            <span>🖨️ Print All Labels</span>
        </button>
    </div>

    <!-- Thermal Label Sheets -->
    @foreach($orders as $ord)
        <div class="thermal-label label-page flex flex-col justify-between text-xs leading-tight">
            <!-- Header -->
            <div class="border-b-2 border-black pb-2 flex justify-between items-start">
                <div>
                    <div class="font-black text-sm uppercase">EXPRESS LOGISTICS</div>
                    <div class="text-[10px]">Standard Surface / Air Express</div>
                </div>
                <div class="text-right">
                    <div class="font-black text-base border-2 border-black px-2 py-0.5 uppercase">
                        {{ strtoupper($ord->payment_method) }}
                    </div>
                </div>
            </div>

            <!-- Routing & Barcode Simulation -->
            <div class="py-3 text-center border-b-2 border-black space-y-1">
                <div class="font-mono text-2xl font-black tracking-widest leading-none">
                    ||| | |||| || ||||| ||| |||| |
                </div>
                <div class="font-mono font-bold text-xs tracking-wider">
                    AWB: {{ $ord->order_number }}
                </div>
            </div>

            <!-- Ship To Customer Address -->
            <div class="border-b-2 border-black py-2.5 space-y-1">
                <div class="text-[10px] font-bold uppercase text-gray-600">SHIP TO (DELIVERY ADDRESS):</div>
                <div class="font-black text-sm uppercase">{{ $ord->customer_name }}</div>
                <div class="text-xs font-semibold uppercase">{{ $ord->shipping_address }}</div>
                <div class="font-black text-sm uppercase">{{ $ord->city }} - PIN: {{ $ord->pincode }}</div>
                <div class="font-bold text-xs">Phone: {{ $ord->customer_phone }}</div>
            </div>

            <!-- Collectable Amount -->
            <div class="border-b-2 border-black py-2 flex justify-between items-center bg-gray-50 px-2">
                <span class="font-bold text-xs">COLLECTABLE CASH (COD):</span>
                <span class="font-black text-base">
                    {{ $ord->payment_method === 'cod' ? '₹' . number_format($ord->total_price, 2) : '₹0.00 (PREPAID)' }}
                </span>
            </div>

            <!-- Items Info -->
            <div class="py-2 text-[10px] space-y-1">
                <div class="font-bold uppercase">Contents: ({{ $ord->products->count() }} items)</div>
                @foreach($ord->products->take(2) as $p)
                    <div class="truncate">• {{ $p->name }} (Qty: {{ $p->pivot->quantity }})</div>
                @endforeach
            </div>

            <!-- Sender / Return Address -->
            <div class="border-t-2 border-black pt-2 text-[9px] text-gray-700">
                <div class="font-bold uppercase">RETURN ADDRESS IF UNDELIVERED:</div>
                <div>VyaparIndia Fulfillment Center, Jaipur - 302022</div>
            </div>
        </div>
    @endforeach

</body>
</html>