<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #{{ $order->order_number ?: $order->id }} - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .print-border { border: 1px solid #ddd !important; }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs no-print">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.dashboard') }}" class="h-9 w-9 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-base shadow-sm">
                        V
                    </a>
                    <div>
                        <span class="font-black text-gray-900 text-base tracking-tight">VyaparIndia</span>
                        <span class="ml-2 text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Order Details</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button onclick="window.print()" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-print"></i>
                        <span>Print Bill</span>
                    </button>
                    <a href="{{ route('seller.orders.index') }}" class="text-xs font-bold text-gray-600 hover:text-gray-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>All Orders</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    @php
        $custName = $order->customer_name ?: ($order->buyer->name ?? 'Guest Buyer');
        $custPhone = $order->customer_phone ?: ($order->buyer->phone_number ?? '');
        $custPhoneClean = preg_replace('/[^0-9]/', '', $custPhone);
        $st = strtolower($order->status ?? 'pending');
        $badgeColors = [
            'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
            'processing' => 'bg-blue-100 text-blue-800 border-blue-200',
            'shipped' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            'delivered' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
        ];
    @endphp

    <!-- Main Container -->
    <main class="flex-1 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

        <!-- Status update flash -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Header Overview -->
        <div class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-8 shadow-xs mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-6">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-black text-gray-900">Order #{{ $order->order_number ?: $order->id }}</h1>
                        <span class="px-3 py-1 rounded-full text-xs font-bold border uppercase tracking-wider {{ $badgeColors[$st] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($order->status ?? 'Pending') }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Placed on {{ $order->created_at->format('d M Y, h:i A') }} ({{ $order->created_at->diffForHumans() }})
                        @if($order->order_source)
                            • Source: <span class="font-bold text-gray-700 uppercase">{{ str_replace('_', ' ', $order->order_source) }}</span>
                        @endif
                    </p>
                </div>

                <!-- Status Update Form -->
                <div class="no-print bg-gray-50 p-3 rounded-2xl border border-gray-200">
                    <form action="{{ route('seller.orders.updateStatus', $order) }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        @method('PUT')
                        <label class="text-xs font-bold text-gray-600">Update Status:</label>
                        <select name="status" class="text-xs font-bold bg-white border border-gray-300 rounded-xl px-3 py-1.5 focus:outline-hidden">
                            <option value="pending" {{ $st === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $st === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $st === 'shipped' ? 'selected' : '' }}>Shipped / Dispatched</option>
                            <option value="delivered" {{ $st === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $st === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                            Update
                        </button>
                    </form>
                </div>
            </div>

            <!-- Customer & Shipping Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6">
                <!-- Customer info -->
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Customer & Contact</h3>
                    <div class="text-base font-bold text-gray-900">{{ $custName }}</div>
                    
                    @if($custPhone)
                        <div class="flex items-center gap-3 mt-2">
                            <a href="tel:{{ $custPhone }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-gray-200 rounded-lg text-xs font-bold text-blue-600 hover:bg-blue-50 transition">
                                <i class="fa-solid fa-phone"></i>
                                <span>Call: {{ $custPhone }}</span>
                            </a>
                            <a href="https://wa.me/91{{ $custPhoneClean }}?text=Hello%20{{ urlencode($custName) }},%20regarding%20your%20Order%20%23{{ $order->order_number ?: $order->id }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-600 rounded-lg text-xs font-bold text-white hover:bg-emerald-700 transition">
                                <i class="fa-brands fa-whatsapp text-sm"></i>
                                <span>WhatsApp</span>
                            </a>
                        </div>
                    @endif

                    @if($order->customer_email)
                        <div class="text-xs text-gray-600 mt-2">
                            <i class="fa-solid fa-envelope mr-1 text-gray-400"></i>
                            {{ $order->customer_email }}
                        </div>
                    @endif
                </div>

                <!-- Shipping & Delivery Address -->
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Delivery & Payment</h3>
                    <p class="text-xs font-medium text-gray-800 leading-relaxed">
                        <i class="fa-solid fa-location-dot text-rose-500 mr-1"></i>
                        {{ $order->shipping_address ?: 'Address not provided' }}
                    </p>
                    @if($order->city)
                        <div class="text-xs font-bold text-gray-600 mt-1">
                            City: {{ $order->city }} {{ $order->pincode ? '| PIN: ' . $order->pincode : '' }}
                        </div>
                    @endif

                    <div class="mt-3 pt-3 border-t border-gray-200 flex items-center justify-between text-xs">
                        <span class="text-gray-500 font-bold">Payment Method:</span>
                        <span class="font-bold text-gray-900 uppercase">
                            {{ $order->payment_method === 'online' ? 'Online / UPI Paid' : 'Cash on Delivery (COD)' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ordered Products Table -->
        <div class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-8 shadow-xs">
            <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-blue-600"></i>
                <span>Ordered Items ({{ $order->products->count() }} Types)</span>
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 text-[11px] font-bold text-gray-500 uppercase">
                            <th class="py-3 px-3">Item Description</th>
                            <th class="py-3 px-3 text-center">Unit Price</th>
                            <th class="py-3 px-3 text-center">Quantity</th>
                            <th class="py-3 px-3 text-right">Line Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        @php $subtotal = 0; @endphp
                        @foreach ($order->products as $product)
                            @php
                                $price = $product->pivot->price ?? $product->price;
                                $qty = $product->pivot->quantity ?? 1;
                                $line = $price * $qty;
                                $subtotal += $line;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-3 font-bold text-gray-900">
                                    {{ $product->name }}
                                    @if($product->category)
                                        <div class="text-[10px] text-gray-400 font-normal">{{ $product->category->name }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-center font-medium">₹{{ number_format($price, 2) }}</td>
                                <td class="py-3 px-3 text-center font-bold">x{{ $qty }}</td>
                                <td class="py-3 px-3 text-right font-black text-gray-900">₹{{ number_format($line, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-gray-200">
                            <td colspan="3" class="py-4 px-3 text-right font-bold text-sm text-gray-700">Grand Total:</td>
                            <td class="py-4 px-3 text-right font-black text-lg text-emerald-700">₹{{ number_format($order->total_price ?: $subtotal, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Notes -->
            @if($order->notes)
                <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900">
                    <strong class="font-bold">Customer Notes:</strong> {{ $order->notes }}
                </div>
            @endif
        </div>

    </main>

</body>
</html>
