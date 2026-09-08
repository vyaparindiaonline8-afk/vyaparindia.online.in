<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Order #{{ $order->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-900 antialiased min-h-screen py-8 px-4 flex items-center justify-center">

    <div class="max-w-xl w-full bg-white rounded-3xl shadow-xl border border-gray-200 overflow-hidden space-y-6 p-6 sm:p-8">
        <!-- Brand Header -->
        <div class="text-center pb-4 border-b border-gray-100">
            <div class="h-12 w-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center mx-auto text-2xl shadow-md mb-2">
                <i class="fa-solid fa-shield-check"></i>
            </div>
            <h1 class="text-xl font-black text-gray-900">Order Verification Portal</h1>
            <p class="text-xs text-gray-500 font-mono mt-0.5">Order #{{ $order->order_number }}</p>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold text-center space-y-1">
                <i class="fa-solid fa-circle-check text-emerald-600 text-xl mb-1 block"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <!-- Order Snapshot -->
        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 text-xs space-y-2">
            <div class="flex justify-between text-gray-500">
                <span>Customer Name:</span>
                <span class="font-bold text-gray-900">{{ $order->customer_name }}</span>
            </div>
            <div class="flex justify-between text-gray-500">
                <span>Delivery Address:</span>
                <span class="font-semibold text-gray-800 text-right max-w-xs">{{ $order->shipping_address }}, {{ $order->city }} - {{ $order->pincode }}</span>
            </div>
            <div class="flex justify-between text-gray-500">
                <span>Payment Mode:</span>
                <span class="font-bold uppercase text-gray-900">{{ $order->payment_method }} ({{ $order->payment_status }})</span>
            </div>
            <div class="pt-2 border-t border-gray-200 flex justify-between font-extrabold text-sm text-gray-900">
                <span>Order Total:</span>
                <span class="text-emerald-600 text-base">₹{{ number_format($order->total_price, 2) }}</span>
            </div>
        </div>

        @if($order->cod_verification_status === 'converted_to_prepaid')
            <div class="p-6 rounded-2xl bg-emerald-500 text-white text-center space-y-2 shadow-md">
                <i class="fa-solid fa-circle-check text-3xl"></i>
                <h3 class="text-base font-black">Prepaid Payment Confirmed!</h3>
                <p class="text-xs text-emerald-100">You saved ₹{{ number_format($order->cod_to_prepaid_discount, 2) }} on this order. Your shipment is prioritized for immediate dispatch.</p>
            </div>
        @elseif($order->cod_verification_status === 'verified')
            <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 text-center space-y-1">
                <i class="fa-solid fa-truck-fast text-2xl text-blue-600 mb-1"></i>
                <h3 class="font-bold text-xs">COD Order Confirmed</h3>
                <p class="text-[11px] text-gray-500">Your order has been verified. Courier will collect ₹{{ number_format($order->total_price, 2) }} at your doorstep.</p>
            </div>
        @else
            <!-- COD to Prepaid Conversion Incentive Offer -->
            <div class="bg-gradient-to-r from-purple-700 to-indigo-700 rounded-2xl p-5 text-white space-y-3 shadow-md">
                <div class="flex items-center justify-between">
                    <span class="px-2.5 py-0.5 rounded-full bg-amber-400 text-gray-950 font-black text-[10px] uppercase">
                        ⚡ Limited Time Offer
                    </span>
                    <span class="text-[11px] text-purple-200">Zero COD Fee</span>
                </div>
                <div>
                    <h3 class="font-black text-base">Pay Online & Get ₹{{ number_format($order->cod_to_prepaid_discount, 2) }} Instant Discount!</h3>
                    <p class="text-xs text-purple-100 mt-0.5">Pay via UPI / Google Pay / Card now and get your order for only <strong>₹{{ number_format($order->total_price - $order->cod_to_prepaid_discount, 2) }}</strong>.</p>
                </div>
                <form action="{{ route('verification.pay', ['token' => $order->cod_verification_token]) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-amber-400 hover:bg-amber-300 active:scale-95 text-gray-950 font-black text-xs rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-bolt"></i>
                        <span>Pay Online ₹{{ number_format($order->total_price - $order->cod_to_prepaid_discount, 2) }} (Save ₹{{ number_format($order->cod_to_prepaid_discount, 2) }})</span>
                    </button>
                </form>
            </div>

            <!-- Alternative: Confirm COD -->
            <div class="text-center pt-2">
                <form action="{{ route('verification.confirmCod', ['token' => $order->cod_verification_token]) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs rounded-xl transition-colors flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check"></i>
                        <span>Confirm as Cash on Delivery (Pay ₹{{ number_format($order->total_price, 2) }} on Delivery)</span>
                    </button>
                </form>
            </div>
        @endif
    </div>

</body>
</html>