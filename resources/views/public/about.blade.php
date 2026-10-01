<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | VyaparIndia - India's Smart B2B & D2C Business Marketplace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col justify-between">

    <!-- Header Navigation -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-blue-600 text-white font-black flex items-center justify-center text-lg shadow-sm">
                        V
                    </div>
                    <div>
                        <span class="font-black text-gray-900 text-lg tracking-tight">VyaparIndia</span>
                        <span class="hidden sm:inline-block ml-2 text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">B2B & D2C Network</span>
                    </div>
                </a>

                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="px-3 py-2 text-xs font-bold text-gray-700 hover:text-blue-600">Home</a>
                    <a href="{{ route('search') }}" class="px-3 py-2 text-xs font-bold text-gray-700 hover:text-blue-600">Search Products</a>
                    <a href="{{ route('login') }}" class="px-3.5 py-1.5 text-xs font-bold text-gray-700 hover:text-blue-600">Login</a>
                    <a href="{{ route('register', ['role' => 'seller']) }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md transition">
                        Seller Registration
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-12 flex-1">
        <!-- Hero Section -->
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">About VyaparIndia</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight mt-3 mb-4">
                Empowering Indian MSMEs, Manufacturers & Wholesalers
            </h1>
            <p class="text-base text-gray-600 leading-relaxed">
                VyaparIndia is a modern business-to-business (B2B) and direct-to-consumer (D2C) marketplace built specifically for Indian businesses. We connect local manufacturers, dealers, and retail shop owners directly with zero middleman commissions.
            </p>
        </div>

        <!-- 3 Core Pillars -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-store"></i>
                </div>
                <h3 class="font-bold text-lg text-gray-900 mb-2">Free Mini-Websites</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Every registered seller receives their own personalized digital storefront with a custom URL, WhatsApp ordering, and catalog showcase without paying expensive web agency fees.
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-boxes-packing"></i>
                </div>
                <h3 class="font-bold text-lg text-gray-900 mb-2">Smart Dropshipping Hub</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Retailers and dropshippers can import products from verified wholesale manufacturers with 1-click and start selling immediately with automated margin calculations and COD tracking.
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="font-bold text-lg text-gray-900 mb-2">Verified Supplier Trust</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Every supplier is linked with GSTIN, location verification, and genuine customer reviews to eliminate fraud and provide genuine factory wholesale pricing across India.
                </p>
            </div>
        </div>

        <!-- Mission & Story -->
        <div class="bg-gradient-to-r from-blue-900 to-indigo-900 rounded-3xl p-8 sm:p-10 text-white shadow-xl">
            <h2 class="text-2xl font-bold mb-4">Our Vision for Digital Bharat</h2>
            <p class="text-blue-100 text-sm sm:text-base leading-relaxed mb-6">
                From Rajkot's hardware hubs and Surat's textile mills to Morbi's ceramic clusters and Indore's agricultural mandis, India's trade power lies in its regional manufacturing strength. VyaparIndia digitizes these hubs into a single unified marketplace.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('register', ['role' => 'seller']) }}" class="px-6 py-3 rounded-xl bg-blue-500 hover:bg-blue-600 font-bold text-white text-xs shadow-md transition">
                    Register Your Business Free
                </a>
                <a href="{{ route('search') }}" class="px-6 py-3 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 font-bold text-white text-xs transition">
                    Explore B2B Products
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-8 text-center text-xs text-gray-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-wrap justify-center gap-6 mb-3">
            <a href="{{ route('about') }}" class="font-bold text-blue-600">About Us</a>
            <a href="{{ route('help') }}" class="hover:text-gray-900">Help & Support</a>
            <a href="{{ route('privacy') }}" class="hover:text-gray-900">Privacy Policy</a>
            <a href="{{ route('terms') }}" class="hover:text-gray-900">Terms of Service</a>
        </div>
        <p>&copy; {{ date('Y') }} VyaparIndia.online - All rights reserved.</p>
    </footer>

</body>
</html>
