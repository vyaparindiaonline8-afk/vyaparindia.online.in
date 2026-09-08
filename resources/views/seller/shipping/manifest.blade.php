<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courier Pickup Manifest Sheet - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background-color: white !important; font-size: 11px; }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 antialiased p-6">

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-3xl shadow-sm border border-gray-200 space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between pb-4 border-b-2 border-gray-900">
            <div>
                <h1 class="text-xl font-black uppercase">Courier Handover Manifest Sheet</h1>
                <p class="text-xs text-gray-500">Official proof of package handover to logistics courier partner</p>
            </div>
            <div class="no-print">
                <button onclick="window.print()" class="px-5 py-2.5 bg-gray-900 text-white rounded-xl text-xs font-bold hover:bg-gray-800">
                    🖨️ Print Manifest
                </button>
            </div>
        </div>

        <!-- Manifest Info -->
        <div class="grid grid-cols-3 gap-4 text-xs bg-gray-50 p-4 rounded-2xl border border-gray-200">
            <div>
                <span class="text-gray-500">Manifest Date:</span>
                <div class="font-bold text-gray-900">{{ now()->format('d M Y, h:i A') }}</div>
            </div>
            <div>
                <span class="text-gray-500">Total Shipments:</span>
                <div class="font-black text-gray-900">{{ $orders->count() }} Packages</div>
            </div>
            <div>
                <span class="text-gray-500">Total COD Cash to Collect:</span>
                <div class="font-black text-emerald-600">₹{{ number_format($orders->where('payment_method', 'cod')->sum('total_price'), 2) }}</div>
            </div>
        </div>

        <!-- Shipments Table -->
        <table class="w-full text-left text-xs border border-gray-200">
            <thead class="bg-gray-100 text-[10px] uppercase font-bold text-gray-700">
                <tr>
                    <th class="p-2.5 border">#</th>
                    <th class="p-2.5 border">Order / AWB</th>
                    <th class="p-2.5 border">Customer Name</th>
                    <th class="p-2.5 border">Destination City / PIN</th>
                    <th class="p-2.5 border">Mode</th>
                    <th class="p-2.5 border text-right">Collectable Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $idx => $ord)
                    <tr class="border">
                        <td class="p-2.5 border text-center">{{ $idx + 1 }}</td>
                        <td class="p-2.5 border font-mono font-bold">{{ $ord->order_number ?: $ord->id }}</td>
                        <td class="p-2.5 border font-semibold">{{ $ord->customer_name }}</td>
                        <td class="p-2.5 border">{{ $ord->city }} - {{ $ord->pincode }}</td>
                        <td class="p-2.5 border uppercase font-bold text-[10px] {{ $ord->payment_method === 'cod' ? 'text-amber-700' : 'text-purple-700' }}">
                            {{ $ord->payment_method }}
                        </td>
                        <td class="p-2.5 border text-right font-bold">
                            {{ $ord->payment_method === 'cod' ? '₹' . number_format($ord->total_price, 2) : 'PREPAID' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Signatures & Verification -->
        <div class="pt-8 grid grid-cols-2 gap-12 text-xs border-t border-gray-200">
            <div class="border-t-2 border-gray-400 pt-2 text-center">
                <div class="font-bold">Courier Pickup Executive Signature</div>
                <div class="text-[10px] text-gray-400 mt-1">Name: ______________________ | Mobile: _________________</div>
            </div>

            <div class="border-t-2 border-gray-400 pt-2 text-center">
                <div class="font-bold">Warehouse Dispatch In-Charge Signature</div>
                <div class="text-[10px] text-gray-400 mt-1">Verified & Handed over {{ $orders->count() }} packages</div>
            </div>
        </div>
    </div>

</body>
</html>