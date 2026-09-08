<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Channel E-Commerce Integrations - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800 antialiased min-h-screen">

    <!-- Top Nav -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.dashboard') }}" class="text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-lg text-gray-900 leading-tight">Multi-Channel E-Commerce Hub</h1>
                        <p class="text-xs text-gray-500">Connect Shopify, WooCommerce, Amazon, & Flipkart to sync inventory & orders</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.channels.publisher') }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        <span>AI Universal Publisher</span>
                    </a>
                    <a href="{{ route('seller.channels.inventory') }}" class="px-3.5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs">
                        Omnichannel Stock
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Integration Channels Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($supportedChannels as $channelKey => $info)
                @php
                    $connected = $channels->firstWhere('channel_name', $channelKey);
                @endphp
                <div class="bg-white rounded-3xl border border-gray-200 p-6 shadow-xs flex flex-col justify-between space-y-6">
                    <div>
                        <div class="flex items-start justify-between">
                            <div class="h-14 w-14 rounded-2xl {{ $info['bg'] }} {{ $info['color'] }} flex items-center justify-center text-2xl shadow-xs">
                                <i class="{{ $info['icon'] }}"></i>
                            </div>
                            @if($connected && $connected->is_active)
                                <span class="px-3 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>CONNECTED</span>
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500">
                                    DISCONNECTED
                                </span>
                            @endif
                        </div>

                        <div class="mt-4">
                            <h3 class="font-black text-base text-gray-900">{{ $info['name'] }}</h3>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $connected ? 'Store: ' . $connected->store_name : 'Seamlessly sync products, stock levels, and order flow.' }}
                            </p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        @if($connected)
                            <div class="flex items-center justify-between text-xs mb-4">
                                <span class="text-gray-500">Listings Synced:</span>
                                <span class="font-bold text-gray-900">{{ $connected->listings->count() }} Products</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button onclick="openConnectModal('{{ $channelKey }}', '{{ $info['name'] }}', '{{ $connected->store_name }}', '{{ $connected->store_url }}')" class="flex-1 py-2 px-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs">
                                    Configure API
                                </button>
                                <form action="{{ route('seller.channels.toggle', $connected->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="py-2 px-3 rounded-xl {{ $connected->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }} font-bold text-xs">
                                        {{ $connected->is_active ? 'Pause' : 'Activate' }}
                                    </button>
                                </form>
                            </div>
                        @else
                            <button onclick="openConnectModal('{{ $channelKey }}', '{{ $info['name'] }}')" class="w-full py-2.5 px-4 rounded-xl bg-gray-900 hover:bg-gray-800 text-white font-bold text-xs shadow-xs transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-plug"></i>
                                <span>Connect {{ $info['name'] }}</span>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Connect Modal -->
    <div id="connect-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" onclick="closeConnectModal()"></div>

            <div class="inline-block bg-white rounded-3xl p-6 sm:p-8 text-left overflow-hidden shadow-2xl transform transition-all sm:max-w-md w-full relative z-10 space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="font-extrabold text-base text-gray-900" id="modal-title">Connect Channel</h3>
                    <button onclick="closeConnectModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form action="{{ route('seller.channels.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <input type="hidden" name="channel_name" id="channel-name-input">

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Store / Channel Name <span class="text-red-500">*</span></label>
                        <input type="text" name="store_name" id="store-name-input" required placeholder="e.g. My Shopify Store" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-semibold">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Store URL / Domain</label>
                        <input type="url" name="store_url" id="store-url-input" placeholder="https://mystore.myshopify.com" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">API Key / Client ID</label>
                        <input type="text" name="api_key" placeholder="shpa_xxxxxxxxxxxxxxxx" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-mono">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">API Secret / Access Token</label>
                        <input type="password" name="access_token" placeholder="shpat_xxxxxxxxxxxxxxxx" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-mono">
                    </div>

                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition-all">
                        Save & Authorize API Sync
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openConnectModal(channelKey, channelTitle, storeName = '', storeUrl = '') {
            document.getElementById('channel-name-input').value = channelKey;
            document.getElementById('modal-title').innerText = 'Connect ' + channelTitle;
            document.getElementById('store-name-input').value = storeName || (channelTitle + ' Integration');
            document.getElementById('store-url-input').value = storeUrl || '';
            document.getElementById('connect-modal').classList.remove('hidden');
        }

        function closeConnectModal() {
            document.getElementById('connect-modal').classList.add('hidden');
        }
    </script>
</body>
</html>