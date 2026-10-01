<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Support Center | VyaparIndia - India's Smart B2B & D2C Marketplace</title>
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
                    <a href="{{ route('register', ['role' => 'seller']) }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md transition">
                        Seller Registration
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-12 flex-1">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Support & Knowledge Base</span>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mt-3 mb-2">How Can We Help You?</h1>
            <p class="text-sm text-gray-600">Find quick answers to common questions about buying, selling, and managing your store on VyaparIndia.</p>
        </div>

        <!-- Contact Support Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
            <div class="p-6 bg-white rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl flex-shrink-0">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-gray-900">Direct WhatsApp Desk</h3>
                    <p class="text-xs text-gray-500 mb-1">Instant assistance for order enquiries & onboarding</p>
                    <a href="https://wa.me/917828289433" target="_blank" class="text-xs font-bold text-emerald-600 hover:underline">Chat with Support &rarr;</a>
                </div>
            </div>

            <div class="p-6 bg-white rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl flex-shrink-0">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-gray-900">Email Support</h3>
                    <p class="text-xs text-gray-500 mb-1">Response within 24 business hours</p>
                    <a href="mailto:support@vyaparindia.online" class="text-xs font-bold text-blue-600 hover:underline">support@vyaparindia.online &rarr;</a>
                </div>
            </div>
        </div>

        <!-- FAQs Section -->
        <div class="space-y-4">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs">
                <h3 class="font-bold text-sm text-gray-900 mb-1.5 flex items-center gap-2">
                    <i class="fa-solid fa-circle-question text-blue-600"></i>
                    How do I create an online store / mini-website for my business?
                </h3>
                <p class="text-xs text-gray-600 leading-relaxed pl-6">
                    Simply register as a Seller on VyaparIndia. Once logged in, visit <strong>Seller Hub &rarr; Mini-Site Settings</strong>. You can choose your custom store link (e.g. <code>vyaparindia.online/your-firm-name</code>), upload your company logo, set your WhatsApp number, and add products with wholesale prices.
                </p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs">
                <h3 class="font-bold text-sm text-gray-900 mb-1.5 flex items-center gap-2">
                    <i class="fa-solid fa-circle-question text-blue-600"></i>
                    Are there any commission or listing fees?
                </h3>
                <p class="text-xs text-gray-600 leading-relaxed pl-6">
                    No! VyaparIndia does not charge any hidden middleman commission on buyer-seller deals. Buyers contact you directly on WhatsApp or place direct wholesale enquiries.
                </p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs">
                <h3 class="font-bold text-sm text-gray-900 mb-1.5 flex items-center gap-2">
                    <i class="fa-solid fa-circle-question text-blue-600"></i>
                    How does the Dropshipping Hub work?
                </h3>
                <p class="text-xs text-gray-600 leading-relaxed pl-6">
                    In the Seller Hub under <strong>Dropship Hub</strong>, retailers can browse products uploaded by verified wholesale manufacturers. You can import any product to your own store with your desired retail markup margin. When customer orders arrive, the wholesaler dispatches directly to the customer.
                </p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs">
                <h3 class="font-bold text-sm text-gray-900 mb-1.5 flex items-center gap-2">
                    <i class="fa-solid fa-circle-question text-blue-600"></i>
                    How do buyers pay for orders?
                </h3>
                <p class="text-xs text-gray-600 leading-relaxed pl-6">
                    VyaparIndia supports Cash On Delivery (COD), Direct UPI (PhonePe, GPay, Paytm) directly to the seller's verified QR code/VPA, and Bank Wire Transfer with zero payment gateway deduction.
                </p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-8 text-center text-xs text-gray-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-wrap justify-center gap-6 mb-3">
            <a href="{{ route('about') }}" class="hover:text-gray-900">About Us</a>
            <a href="{{ route('help') }}" class="font-bold text-blue-600">Help & Support</a>
            <a href="{{ route('privacy') }}" class="hover:text-gray-900">Privacy Policy</a>
            <a href="{{ route('terms') }}" class="hover:text-gray-900">Terms of Service</a>
        </div>
        <p>&copy; {{ date('Y') }} VyaparIndia.online - All rights reserved.</p>
    </footer>

</body>
</html>
