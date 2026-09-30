<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sellerPage->page_title ?? 'Store' }} | VyaparIndia</title>
    <meta name="description" content="{{ $sellerPage->tagline ?? $sellerPage->welcome_message ?? 'Shop directly from seller' }}">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            DEFAULT: '{{ $sellerPage->theme_color ?: "#2563eb" }}',
                            dark: '{{ $sellerPage->theme_color ?: "#1d4ed8" }}',
                            light: '{{ $sellerPage->theme_color ? $sellerPage->theme_color . "20" : "#eff6ff" }}',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --brand-color: {{ $sellerPage->theme_color ?: '#2563eb' }};
        }
        .bg-brand-custom { background-color: var(--brand-color); }
        .text-brand-custom { color: var(--brand-color); }
        .border-brand-custom { border-color: var(--brand-color); }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen antialiased">

    <!-- Top Announcement Bar -->
    <div class="bg-slate-900 text-white text-xs py-1.5 px-4 text-center flex items-center justify-center gap-4">
        <span>✨ Welcome to {{ $sellerPage->page_title }}</span>
        @if($sellerPage->enable_cod)
            <span class="hidden sm:inline">• 💵 Cash on Delivery Available</span>
        @endif
        @if($sellerPage->enable_whatsapp_order && $sellerPage->whatsapp_number)
            <span class="hidden md:inline">• 📲 Fast WhatsApp Ordering</span>
        @endif
    </div>

    <!-- Main Navigation -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                <!-- Store Brand / Logo -->
                <a href="{{ route('minisite.show', $sellerPage->slug) }}" class="flex items-center gap-3 shrink-0">
                    @if($sellerPage->logo_url)
                        <img src="{{ $sellerPage->logo_url }}" alt="{{ $sellerPage->page_title }}" class="h-10 w-10 object-contain rounded-lg border border-gray-100 shadow-sm">
                    @else
                        <div class="h-10 w-10 rounded-lg bg-brand-custom text-white font-bold flex items-center justify-center text-lg shadow-sm">
                            {{ strtoupper(substr($sellerPage->page_title, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div class="font-extrabold text-lg text-gray-900 tracking-tight leading-tight">{{ $sellerPage->page_title }}</div>
                        @if($sellerPage->tagline)
                            <div class="text-xs text-gray-500 truncate max-w-[180px] sm:max-w-xs">{{ $sellerPage->tagline }}</div>
                        @endif
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center gap-6 text-sm font-semibold text-gray-700">
                    <a href="{{ route('minisite.show', $sellerPage->slug) }}" class="hover:text-brand-custom transition-colors {{ request()->routeIs('minisite.show') ? 'text-brand-custom' : '' }}">Home</a>
                    <a href="{{ route('minisite.products', $sellerPage->slug) }}" class="hover:text-brand-custom transition-colors {{ request()->routeIs('minisite.products') ? 'text-brand-custom' : '' }}">All Products</a>
                    <a href="{{ route('minisite.contact', $sellerPage->slug) }}" class="hover:text-brand-custom transition-colors {{ request()->routeIs('minisite.contact') ? 'text-brand-custom' : '' }}">Contact & About</a>
                </nav>

                <!-- Search Bar (Desktop) -->
                <form action="{{ route('minisite.products', $sellerPage->slug) }}" method="GET" class="hidden lg:flex items-center relative flex-1 max-w-xs">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products in this store..." class="w-full pl-9 pr-4 py-1.5 text-xs bg-gray-100 border border-transparent rounded-full focus:bg-white focus:border-gray-300 focus:outline-none transition-all">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 text-gray-400 text-xs"></i>
                </form>

                <!-- Actions: WhatsApp + Cart -->
                    <a href="{{ route('minisite.materialScanner', $sellerPage->slug) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 transition">
                        <i class="fa-solid fa-camera text-indigo-600"></i>
                        <span class="hidden sm:inline">Slip Scanner</span>
                    </a>

                    @if($sellerPage->whatsapp_number)
                        <a href="https://wa.me/{{ $sellerPage->clean_whatsapp_number }}?text=Hello%2C%20I%20have%20an%20inquiry%20about%20your%20products%20on%20{{ urlencode(route('minisite.show', $sellerPage->slug)) }}" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-colors">
                            <i class="fa-brands fa-whatsapp text-sm text-emerald-600"></i>
                            <span>Chat with Seller</span>
                        </a>
                    @endif

                    <!-- Cart Drawer Button -->
                    <button onclick="toggleCartDrawer(true)" class="relative p-2.5 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-800 transition-colors">
                        <i class="fa-solid fa-bag-shopping text-base"></i>
                        <span id="cart-badge" class="absolute -top-1 -right-1 bg-red-600 text-white font-bold text-[10px] h-5 w-5 rounded-full flex items-center justify-center hidden">0</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Subnav / Search -->
        <div class="md:hidden border-t border-gray-100 px-4 py-2 bg-gray-50 flex items-center justify-between text-xs font-medium text-gray-600">
            <div class="flex items-center gap-4">
                <a href="{{ route('minisite.show', $sellerPage->slug) }}" class="hover:text-gray-900 {{ request()->routeIs('minisite.show') ? 'text-brand-custom font-bold' : '' }}">Home</a>
                <a href="{{ route('minisite.products', $sellerPage->slug) }}" class="hover:text-gray-900 {{ request()->routeIs('minisite.products') ? 'text-brand-custom font-bold' : '' }}">Products</a>
                <a href="{{ route('minisite.contact', $sellerPage->slug) }}" class="hover:text-gray-900 {{ request()->routeIs('minisite.contact') ? 'text-brand-custom font-bold' : '' }}">Contact</a>
            </div>
            <a href="{{ route('minisite.products', $sellerPage->slug) }}" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-magnifying-glass"></i> Search
            </a>
        </div>
    </header>

    <!-- Main Content Body -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Sticky Bottom Quick Cart Bar (Single-Page Multi-Product Order) -->
    <div id="sticky-bottom-cart-bar" class="fixed bottom-0 left-0 right-0 z-40 bg-slate-900/95 backdrop-blur-md text-white py-3 px-4 shadow-2xl border-t border-slate-800 hidden transform transition-all duration-300">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 cursor-pointer" onclick="toggleCartDrawer(true)">
                <div class="h-10 w-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-sm shadow-sm shrink-0">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-300">
                        <span id="sticky-cart-count" class="text-white font-black text-sm">0</span> Items in Cart
                    </div>
                    <div class="text-sm font-black text-amber-400" id="sticky-cart-total">₹0.00</div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="toggleCartDrawer(true)" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition">
                    View Bag
                </button>
                <button onclick="openQuickOrderModal()" class="px-4 sm:px-5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-black shadow-lg shadow-emerald-500/20 flex items-center gap-1.5 transition">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>1-Click Order</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Quick WhatsApp Order Modal with Automatic Database Save -->
    <div id="quick-order-modal" class="fixed inset-0 z-50 overflow-hidden hidden flex items-center justify-center p-4">
        <div onclick="closeQuickOrderModal()" class="absolute inset-0 bg-gray-900/60 backdrop-blur-xs"></div>
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 space-y-4 shadow-2xl relative z-10">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h3 class="font-black text-gray-900 text-sm">Quick Multi-Item WhatsApp Order</h3>
                    <p class="text-[11px] text-gray-400">Order will be saved in store & sent to WhatsApp</p>
                </div>
                <button onclick="closeQuickOrderModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="quickOrderForm" onsubmit="submitQuickOrder(event)" class="space-y-3">
                <div>
                    <label class="text-xs font-bold text-gray-700 block mb-1">Your Name <span class="text-rose-500">*</span></label>
                    <input type="text" id="qo_name" required placeholder="e.g. Rajesh Sharma" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold focus:border-indigo-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-700 block mb-1">WhatsApp Mobile <span class="text-rose-500">*</span></label>
                    <input type="tel" id="qo_phone" required placeholder="e.g. 9876543210" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold focus:border-indigo-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-700 block mb-1">Delivery Address & Area <span class="text-rose-500">*</span></label>
                    <input type="text" id="qo_address" required placeholder="Shop/House No, Street, City" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold focus:border-indigo-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-700 block mb-1">Payment Method</label>
                    <select id="qo_payment" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold">
                        <option value="cod">Cash on Delivery (COD)</option>
                        <option value="online">Online Payment / UPI</option>
                    </select>
                </div>

                <div class="pt-2">
                    <button type="submit" id="qo_submit_btn" class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md transition flex items-center justify-center gap-2">
                        <i class="fa-brands fa-whatsapp text-base"></i>
                        <span id="qo_btn_text">Confirm & Send to WhatsApp</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Floating WhatsApp Action Button -->
    @if($sellerPage->whatsapp_number)
        <a href="https://wa.me/{{ $sellerPage->clean_whatsapp_number }}?text=Hello%20{{ urlencode($sellerPage->page_title) }}%2C%20I%20am%20visiting%20your%20store%20and%20need%20assistance." target="_blank" class="fixed bottom-20 sm:bottom-6 right-6 z-30 flex items-center justify-center h-14 w-14 rounded-full bg-emerald-500 text-white shadow-xl hover:bg-emerald-600 hover:scale-105 active:scale-95 transition-all group" title="Chat on WhatsApp">
            <i class="fa-brands fa-whatsapp text-3xl"></i>
            <span class="absolute right-16 bg-slate-900 text-white text-xs py-1.5 px-3 rounded-lg shadow-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none hidden sm:block">
                Chat on WhatsApp
            </span>
        </a>
    @endif

    <!-- Sliding Cart Drawer Modal -->
    <div id="cart-drawer" class="fixed inset-0 z-50 overflow-hidden hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div onclick="toggleCartDrawer(false)" class="absolute inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div class="w-screen max-w-md bg-white shadow-2xl flex flex-col">
                <!-- Cart Header -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-bag-shopping text-gray-700"></i>
                        <h2 class="text-base font-bold text-gray-900" id="slide-over-title">Your Shopping Bag</h2>
                    </div>
                    <button onclick="toggleCartDrawer(false)" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Cart Items List Container -->
                <div class="flex-1 overflow-y-auto px-6 py-4" id="cart-items-container">
                    <!-- Populated by JavaScript -->
                </div>

                <!-- Cart Footer & Checkout -->
                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 space-y-3" id="cart-footer">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Subtotal:</span>
                        <span id="cart-subtotal" class="font-extrabold text-gray-900 text-lg">₹0.00</span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>Shipping Delivery:</span>
                        <span class="text-emerald-600 font-semibold">Free Delivery</span>
                    </div>

                    <div class="pt-2 space-y-2">
                        <a href="{{ route('minisite.checkout', $sellerPage->slug) }}" class="w-full py-3 px-4 rounded-xl bg-brand-custom text-white font-bold text-sm shadow-md hover:opacity-90 active:scale-98 transition-all flex items-center justify-center gap-2">
                            <span>Proceed to Checkout</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                        @if($sellerPage->whatsapp_number)
                            <button onclick="orderCartViaWhatsapp()" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 text-white font-semibold text-xs shadow-sm hover:bg-emerald-700 transition-all flex items-center justify-center gap-2">
                                <i class="fa-brands fa-whatsapp text-sm"></i>
                                <span>Order Cart via WhatsApp</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Store Footer -->
    <footer class="bg-white border-t border-gray-200 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Brand Info -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2.5">
                        @if($sellerPage->logo_url)
                            <img src="{{ $sellerPage->logo_url }}" alt="{{ $sellerPage->page_title }}" class="h-8 w-8 object-contain rounded">
                        @endif
                        <span class="font-extrabold text-gray-900 text-lg">{{ $sellerPage->page_title }}</span>
                    </div>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        {{ $sellerPage->tagline ?: ($sellerPage->welcome_message ?: 'Direct manufacturer and verified supplier on VyaparIndia.') }}
                    </p>
                    @if($sellerPage->address)
                        <div class="text-xs text-gray-500 flex items-start gap-2 pt-1">
                            <i class="fa-solid fa-location-dot mt-0.5 text-gray-400"></i>
                            <span>{{ $sellerPage->address }}{{ $sellerPage->city ? ', ' . $sellerPage->city : '' }}{{ $sellerPage->pincode ? ' - ' . $sellerPage->pincode : '' }}</span>
                        </div>
                    @endif
                </div>

                <!-- Quick Navigation & Policies -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900 mb-3">Quick Navigation</h3>
                    <ul class="space-y-2 text-xs text-gray-600 font-medium">
                        <li><a href="{{ route('minisite.show', $sellerPage->slug) }}" class="hover:text-brand-custom">Store Home</a></li>
                        <li><a href="{{ route('minisite.products', $sellerPage->slug) }}" class="hover:text-brand-custom">All Products</a></li>
                        <li><a href="{{ route('minisite.contact', $sellerPage->slug) }}" class="hover:text-brand-custom">Contact & About Store</a></li>
                        <li><a href="{{ route('minisite.contact', $sellerPage->slug) }}#policies" class="hover:text-brand-custom">Shipping & Return Policies</a></li>
                    </ul>
                </div>

                <!-- Verified Store / Badges -->
                <div class="space-y-3 bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <div class="flex items-center gap-2 text-emerald-600 font-bold text-xs">
                        <i class="fa-solid fa-shield-check text-sm"></i>
                        <span>Verified Store on VyaparIndia</span>
                    </div>
                    <p class="text-[11px] text-gray-500 leading-normal">
                        Orders are directly fulfilled and dispatched with full invoice and genuine guarantee.
                    </p>
                    <div class="flex items-center gap-3 pt-2 text-gray-400 text-sm">
                        @if($sellerPage->instagram_link)
                            <a href="{{ $sellerPage->instagram_link }}" target="_blank" class="hover:text-pink-600"><i class="fa-brands fa-instagram"></i></a>
                        @endif
                        @if($sellerPage->facebook_link)
                            <a href="{{ $sellerPage->facebook_link }}" target="_blank" class="hover:text-blue-600"><i class="fa-brands fa-facebook"></i></a>
                        @endif
                        @if($sellerPage->support_phone)
                            <a href="tel:{{ $sellerPage->support_phone }}" class="hover:text-gray-800"><i class="fa-solid fa-phone"></i></a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-3">
                <p>&copy; {{ date('Y') }} {{ $sellerPage->page_title }}. All rights reserved.</p>
                <div class="flex items-center gap-1 text-[11px] text-gray-400">
                    <span>Powered by</span>
                    <a href="{{ route('home') }}" class="font-bold text-gray-700 hover:text-brand-custom">VyaparIndia Platform</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Global Cart Script for Mini-Site -->
    <script>
        const STORE_SLUG = '{{ $sellerPage->slug }}';
        const WHATSAPP_NUM = '{{ $sellerPage->clean_whatsapp_number }}';
        const STORE_NAME = '{{ addslashes($sellerPage->page_title) }}';

        function getCartKey() {
            return 'vyapar_cart_' + STORE_SLUG;
        }

        function getCart() {
            try {
                return JSON.parse(localStorage.getItem(getCartKey())) || [];
            } catch (e) {
                return [];
            }
        }

        function saveCart(cart) {
            localStorage.setItem(getCartKey(), JSON.stringify(cart));
            updateCartUI();
        }

        function addToCart(product, quantity = 1, openDrawer = true) {
            let cart = getCart();
            let index = cart.findIndex(item => item.id == product.id);
            if (index > -1) {
                cart[index].quantity += quantity;
            } else {
                cart.push({
                    id: product.id,
                    name: product.name,
                    price: parseFloat(product.price),
                    image: product.image_url || 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=300',
                    slug: product.slug,
                    quantity: quantity
                });
            }
            saveCart(cart);
            if (openDrawer) {
                toggleCartDrawer(true);
            }
        }

        function updateQuantity(productId, delta) {
            let cart = getCart();
            let index = cart.findIndex(item => item.id == productId);
            if (index > -1) {
                cart[index].quantity += delta;
                if (cart[index].quantity <= 0) {
                    cart.splice(index, 1);
                }
                saveCart(cart);
            }
        }

        function removeFromCart(productId) {
            let cart = getCart().filter(item => item.id != productId);
            saveCart(cart);
        }

        function toggleCartDrawer(show) {
            const drawer = document.getElementById('cart-drawer');
            if (show) {
                drawer.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            } else {
                drawer.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        function updateCartUI() {
            const cart = getCart();
            const badge = document.getElementById('cart-badge');
            const container = document.getElementById('cart-items-container');
            const subtotalEl = document.getElementById('cart-subtotal');
            const footer = document.getElementById('cart-footer');

            let totalCount = 0;
            let subtotal = 0;

            cart.forEach(item => {
                totalCount += item.quantity;
                subtotal += item.price * item.quantity;
            });

            if (badge) {
                if (totalCount > 0) {
                    badge.innerText = totalCount;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }

            if (subtotalEl) {
                subtotalEl.innerText = '₹' + subtotal.toFixed(2);
            }

            // Update Sticky Bottom Quick Bar
            const stickyBar = document.getElementById('sticky-bottom-cart-bar');
            const stickyCount = document.getElementById('sticky-cart-count');
            const stickyTotal = document.getElementById('sticky-cart-total');

            if (stickyBar && stickyCount && stickyTotal) {
                if (totalCount > 0) {
                    stickyCount.innerText = totalCount;
                    stickyTotal.innerText = '₹' + subtotal.toFixed(2);
                    stickyBar.classList.remove('hidden');
                } else {
                    stickyBar.classList.add('hidden');
                }
            }

            if (container) {
                if (cart.length === 0) {
                    container.innerHTML = `
                        <div class="flex flex-col items-center justify-center h-64 text-center text-gray-400">
                            <i class="fa-solid fa-cart-shopping text-4xl mb-3 text-gray-300"></i>
                            <p class="text-sm font-semibold text-gray-600">Your shopping bag is empty</p>
                            <p class="text-xs text-gray-400 mt-1">Browse our store and add items you love</p>
                            <a href="{{ route('minisite.products', $sellerPage->slug) }}" onclick="toggleCartDrawer(false)" class="mt-4 px-4 py-2 rounded-lg bg-gray-900 text-white text-xs font-semibold hover:bg-gray-800">
                                Explore Products
                            </a>
                        </div>
                    `;
                    if (footer) footer.classList.add('opacity-50', 'pointer-events-none');
                } else {
                    if (footer) footer.classList.remove('opacity-50', 'pointer-events-none');
                    let html = '<div class="divide-y divide-gray-100">';
                    cart.forEach(item => {
                        html += `
                            <div class="py-3.5 flex items-center gap-3">
                                <img src="${item.image}" alt="${item.name}" class="h-14 w-14 object-cover rounded-lg border border-gray-100 shrink-0">
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-xs text-gray-900 truncate">${item.name}</div>
                                    <div class="text-xs font-bold text-gray-900 mt-0.5">₹${item.price.toFixed(2)}</div>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <div class="flex items-center border border-gray-200 rounded-md bg-white">
                                            <button onclick="updateQuantity(${item.id}, -1)" class="px-2 py-0.5 text-xs text-gray-500 hover:bg-gray-100">-</button>
                                            <span class="px-2 text-xs font-bold text-gray-800">${item.quantity}</span>
                                            <button onclick="updateQuantity(${item.id}, 1)" class="px-2 py-0.5 text-xs text-gray-500 hover:bg-gray-100">+</button>
                                        </div>
                                        <button onclick="removeFromCart(${item.id})" class="text-xs text-red-500 hover:text-red-700 ml-auto">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    container.innerHTML = html;
                }
            }
        }

        function orderCartViaWhatsapp() {
            const cart = getCart();
            if (!WHATSAPP_NUM || cart.length === 0) {
                alert('Please add items to cart before ordering via WhatsApp.');
                return;
            }

            let text = `*New Order Inquiry from ${STORE_NAME}*\n\n`;
            let subtotal = 0;
            cart.forEach((item, index) => {
                let itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                text += `${index + 1}. *${item.name}*\n   Qty: ${item.quantity} x ₹${item.price} = ₹${itemTotal.toFixed(2)}\n`;
            });
            text += `\n*Total Amount:* ₹${subtotal.toFixed(2)}`;
            text += `\n\nPlease share payment & delivery instructions.`;

            const url = `https://wa.me/${WHATSAPP_NUM}?text=${encodeURIComponent(text)}`;
            window.open(url, '_blank');
        }

        function buySingleOnWhatsapp(productName, productPrice, productUrl) {
            if (!WHATSAPP_NUM) {
                alert('Seller has not configured WhatsApp number yet.');
                return;
            }
            let text = `*Hi ${STORE_NAME}!*\nI want to order:\n\n🛍️ *Product:* ${productName}\n💰 *Price:* ₹${productPrice}\n🔗 *Link:* ${productUrl}\n\nPlease share order & dispatch details.`;
            const url = `https://wa.me/${WHATSAPP_NUM}?text=${encodeURIComponent(text)}`;
            window.open(url, '_blank');
        }

        function openQuickOrderModal() {
            const cart = getCart();
            if (cart.length === 0) {
                alert('Pehle bag me kam se kam 1 item add karein.');
                return;
            }
            document.getElementById('quick-order-modal').classList.remove('hidden');
        }

        function closeQuickOrderModal() {
            document.getElementById('quick-order-modal').classList.add('hidden');
        }

        function submitQuickOrder(e) {
            e.preventDefault();
            const cart = getCart();
            if (cart.length === 0) return;

            const name = document.getElementById('qo_name').value.trim();
            const phone = document.getElementById('qo_phone').value.trim();
            const address = document.getElementById('qo_address').value.trim();
            const payment = document.getElementById('qo_payment').value;

            const submitBtn = document.getElementById('qo_submit_btn');
            const btnText = document.getElementById('qo_btn_text');
            submitBtn.disabled = true;
            btnText.innerText = "Order Save Ho Raha Hai...";

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            // Step 1: Save order to database via AJAX
            fetch("{{ route('minisite.quickOrder', $sellerPage->slug) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({
                    customer_name: name,
                    customer_phone: phone,
                    customer_address: address,
                    payment_method: payment,
                    cart: cart
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const orderNumber = data.order_number;
                    const totalAmt = data.total_amount;

                    // Step 2: Format rich WhatsApp message with Confirmed Order Number
                    let waText = `🛍️ *Naya Order (#${orderNumber}) - ${STORE_NAME}*\n\n` +
                                 `👤 *Customer:* ${name}\n` +
                                 `📞 *Mobile:* ${phone}\n` +
                                 `📍 *Address:* ${address}\n` +
                                 `💳 *Payment:* ${payment.toUpperCase()}\n\n` +
                                 `📦 *Items List (${cart.length} items):*\n`;

                    cart.forEach((item, i) => {
                        let line = item.price * item.quantity;
                        waText += `${i+1}. *${item.name}* (x${item.quantity}) - ₹${line.toFixed(2)}\n`;
                    });

                    waText += `\n💰 *Total Bill:* ₹${totalAmt}\n\n` +
                              `✅ *Order Saved in System!*\nPlease confirm payment & dispatch time.`;

                    // Step 3: Clear local cart
                    saveCart([]);
                    closeQuickOrderModal();

                    // Step 4: Open WhatsApp directly
                    if (WHATSAPP_NUM) {
                        const url = `https://wa.me/${WHATSAPP_NUM}?text=${encodeURIComponent(waText)}`;
                        window.open(url, '_blank');
                    } else {
                        alert(`🎉 Order #${orderNumber} successfully placed! We will contact you on ${phone}.`);
                    }
                } else {
                    alert('Order create karne me dikkat aayi. Please dobara koshish karein.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Order save request failed.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                btnText.innerText = "Confirm & Send to WhatsApp";
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateCartUI();
        });

    </script>
    @stack('scripts')
</body>
</html>
