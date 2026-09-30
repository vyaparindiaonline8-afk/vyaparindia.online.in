@extends('seller-site.layout')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-8 sm:p-12 text-center">
        <!-- Success Icon -->
        <div class="h-20 w-20 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center mx-auto text-4xl shadow-inner mb-6 animate-bounce">
            <i class="fa-solid fa-check"></i>
        </div>

        <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">
            Order Confirmed
        </span>

        <h1 class="text-3xl font-black text-gray-900 mt-3">Thank You for Your Order!</h1>
        <p class="text-sm text-gray-500 mt-2">
            Your order <strong class="text-gray-900 font-mono">#{{ $order->order_number }}</strong> has been placed with <strong>{{ $sellerPage->page_title }}</strong>.
        </p>

        <!-- Order Snapshot Box -->
        <div class="mt-8 bg-gray-50 rounded-2xl p-6 text-left border border-gray-100 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-200 text-xs">
                <span class="text-gray-500">Order Number:</span>
                <span class="font-mono font-bold text-gray-900">{{ $order->order_number }}</span>
            </div>
            <div class="flex items-center justify-between pb-3 border-b border-gray-200 text-xs">
                <span class="text-gray-500">Customer Name:</span>
                <span class="font-bold text-gray-900">{{ $order->customer_name }}</span>
            </div>
            <div class="flex items-center justify-between pb-3 border-b border-gray-200 text-xs">
                <span class="text-gray-500">Delivery Address:</span>
                <span class="font-semibold text-gray-800 text-right max-w-xs">{{ $order->shipping_address }}, {{ $order->city }} - {{ $order->pincode }}</span>
            </div>
            <div class="flex items-center justify-between pb-3 border-b border-gray-200 text-xs">
                <span class="text-gray-500">Payment Mode:</span>
                <span class="font-bold text-gray-900 uppercase">{{ $order->payment_method }} ({{ $order->payment_status }})</span>
            </div>
            <div class="flex items-center justify-between pt-1 text-sm font-extrabold text-gray-900">
                <span>Total Amount:</span>
                <span class="text-brand-custom text-lg">₹{{ number_format($order->total_price, 2) }}</span>
            </div>
        </div>

        <!-- Ordered Items Table -->
        <div class="mt-6 bg-white border border-gray-200 rounded-2xl p-5 text-left shadow-xs">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Order Items</h3>
            <div class="divide-y divide-gray-100 text-xs">
                @foreach($order->products as $item)
                    @php
                        $price = $item->pivot->price ?? $item->price;
                        $qty = $item->pivot->quantity ?? 1;
                    @endphp
                    <div class="py-2 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-gray-900">{{ $item->name }}</span>
                            <span class="text-gray-500 ml-2">x{{ $qty }}</span>
                        </div>
                        <span class="font-mono font-bold text-gray-800">₹{{ number_format($price * $qty, 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Seller UPI & Bank Details (for direct payment) -->
        @php
            $sellerUpi = $sellerPage->upi_id ?: ($sellerPage->user->sellerProfile->upi_id ?? null);
            $sellerBank = $sellerPage->bank_account_number ?: ($sellerPage->user->sellerProfile->bank_account_number ?? null);
        @endphp
        @if($sellerUpi || $sellerBank)
            <div class="mt-6 bg-emerald-50/70 border border-emerald-200 rounded-2xl p-5 text-left">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fa-solid fa-qrcode text-emerald-600 text-base"></i>
                    <h3 class="text-xs font-bold text-emerald-950 uppercase tracking-wider">Pay Seller Directly</h3>
                </div>
                
                @if($sellerUpi)
                    @php
                        $upiUrl = "upi://pay?pa={$sellerUpi}&pn=" . urlencode($sellerPage->page_title) . "&am=" . number_format($order->total_price, 2, '.', '') . "&cu=INR&tn=" . urlencode("Order " . $order->order_number);
                        $qrCodeImg = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=8&data=" . urlencode($upiUrl);
                    @endphp
                    <div class="bg-white p-4 rounded-2xl border border-emerald-200 flex flex-col sm:flex-row items-center gap-4 mb-3">
                        <!-- Dynamic QR Code with exact bill amount -->
                        <div class="bg-gray-50 p-2 rounded-xl border border-gray-200 text-center shrink-0">
                            <img src="{{ $qrCodeImg }}" alt="Scan UPI QR" class="w-32 h-32 mx-auto rounded-lg shadow-xs">
                            <span class="text-[10px] font-bold text-gray-500 mt-1 block">Scan with Any UPI App</span>
                        </div>

                        <!-- Amount & Pay Button -->
                        <div class="flex-1 space-y-2 text-center sm:text-left">
                            <div>
                                <span class="text-[11px] font-bold text-gray-500 block">Exact Bill to Pay:</span>
                                <span class="text-xl font-black text-emerald-700">₹{{ number_format($order->total_price, 2) }}</span>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full ml-1">Auto-Filled</span>
                            </div>
                            <div class="text-xs text-gray-600">
                                <strong>UPI ID:</strong> <span class="font-mono font-bold text-gray-900">{{ $sellerUpi }}</span>
                            </div>
                            <p class="text-[11px] text-gray-500">Amount aur Seller details pehle se filled hain, bas apna UPI PIN daalein.</p>
                            <div>
                                <a href="{{ $upiUrl }}" class="inline-flex items-center gap-2 px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                                    <i class="fa-solid fa-mobile-screen"></i>
                                    <span>Pay ₹{{ number_format($order->total_price, 2) }} on Mobile UPI</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                @if($sellerBank)
                    @php
                        $bName = $sellerPage->bank_name ?: ($sellerPage->user->sellerProfile->bank_name ?? 'Bank');
                        $bIfsc = $sellerPage->bank_ifsc ?: ($sellerPage->user->sellerProfile->bank_ifsc ?? '');
                        $bHolder = $sellerPage->bank_account_holder ?: ($sellerPage->user->sellerProfile->bank_account_holder ?? '');
                    @endphp
                    <div class="text-[11px] text-gray-600 mt-2 space-y-0.5">
                        <div><strong>Bank:</strong> {{ $bName }} | <strong>A/c No:</strong> {{ $sellerBank }}</div>
                        <div><strong>IFSC:</strong> {{ $bIfsc }} | <strong>Holder:</strong> {{ $bHolder }}</div>
                    </div>
                @endif
            </div>
        @endif

        <!-- WhatsApp Confirmation Trigger -->
        @if($sellerPage->whatsapp_number)
            <div class="mt-8 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-center space-y-3">
                <p class="text-xs font-semibold text-emerald-900">
                    📲 Want faster updates? Send your order number to the seller directly on WhatsApp!
                </p>
                <a href="https://wa.me/{{ $sellerPage->clean_whatsapp_number }}?text=Hi%20{{ urlencode($sellerPage->page_title) }}%2C%20I%20just%20placed%20order%20%23{{ $order->order_number }}%20worth%20%E2%82%B9{{ $order->total_price }}%20on%20your%20store.%20Please%20confirm%20and%20share%20tracking%20details." target="_blank" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition-all">
                    <i class="fa-brands fa-whatsapp text-base"></i>
                    <span>Confirm Order on WhatsApp</span>
                </a>
            </div>
        @endif

        <div class="mt-8 flex justify-center gap-4">
            <a href="{{ route('minisite.show', $sellerPage->slug) }}" class="px-6 py-3 rounded-xl bg-gray-900 hover:bg-gray-800 text-white font-bold text-xs transition-colors">
                Back to Storefront
            </a>
            <a href="{{ route('minisite.products', $sellerPage->slug) }}" class="px-6 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs transition-colors">
                Continue Shopping
            </a>
        </div>
    </div>
</div>
@endsection
