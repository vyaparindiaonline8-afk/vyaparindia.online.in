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
