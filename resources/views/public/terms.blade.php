<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service | VyaparIndia - India's Smart B2B & D2C Marketplace</title>
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
                    <a href="{{ route('login') }}" class="px-3.5 py-1.5 text-xs font-bold text-gray-700 hover:text-blue-600">Login</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-12 flex-1">
        <div class="bg-white rounded-3xl p-8 sm:p-10 border border-gray-200 shadow-sm space-y-6">
            <div>
                <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">User Agreement</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mt-1 mb-2">Terms of Service</h1>
                <p class="text-xs text-gray-400">Effective Date: {{ date('F d, Y') }}</p>
            </div>

            <hr class="border-gray-100">

            <div class="space-y-6 text-sm text-gray-600 leading-relaxed">
                <section>
                    <h2 class="text-base font-bold text-gray-900 mb-2">1. Acceptance of Terms</h2>
                    <p>By accessing or registering an account on VyaparIndia.online, you agree to comply with and be bound by these Terms of Service. If you disagree with any part, you may discontinue use of the platform.</p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-gray-900 mb-2">2. Seller Responsibilities & Product Accuracy</h2>
                    <p>Sellers are solely responsible for ensuring the authenticity, specifications, pricing, and availability of all products listed on their digital storefronts / mini-sites. Listing illegal, counterfeit, or prohibited items is strictly prohibited and results in immediate account termination.</p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-gray-900 mb-2">3. B2B Transactions & Settlement</h2>
                    <p>VyaparIndia acts as a digital marketplace enabling direct connection between commercial buyers and suppliers. Commercial contracts, credit terms, and goods dispatch are directly agreed upon between the buyer and seller unless explicitly utilizing platform logistics services.</p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-gray-900 mb-2">4. Dropshipping Partnerships</h2>
                    <p>Wholesalers offering dropshipping fulfillment agree to dispatch orders within agreed SLA timelines. Dropshippers agree to honor customer pricing and retail obligations.</p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-gray-900 mb-2">5. Jurisdiction</h2>
                    <p>These terms are governed by the laws of India. Any disputes are subject to the exclusive jurisdiction of the competent courts in India.</p>
                </section>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-8 text-center text-xs text-gray-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-wrap justify-center gap-6 mb-3">
            <a href="{{ route('about') }}" class="hover:text-gray-900">About Us</a>
            <a href="{{ route('help') }}" class="hover:text-gray-900">Help & Support</a>
            <a href="{{ route('privacy') }}" class="hover:text-gray-900">Privacy Policy</a>
            <a href="{{ route('terms') }}" class="font-bold text-blue-600">Terms of Service</a>
        </div>
        <p>&copy; {{ date('Y') }} VyaparIndia.online - All rights reserved.</p>
    </footer>

</body>
</html>
