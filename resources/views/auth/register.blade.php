<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | VyaparIndia - India's Smart B2B & D2C Business Marketplace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 text-gray-800 min-h-screen flex flex-col justify-between antialiased relative selection:bg-blue-600 selection:text-white">

    <!-- Ambient background light effects -->
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Header Navigation -->
    <header class="w-full py-6 px-4 sm:px-8 relative z-10">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="h-11 w-11 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 text-white font-black flex items-center justify-center text-xl shadow-lg shadow-blue-500/30 group-hover:scale-105 transition-transform duration-200">
                    V
                </div>
                <div>
                    <span class="font-extrabold text-white text-xl tracking-tight">Vyapar<span class="text-blue-400">India</span></span>
                    <span class="hidden sm:inline-block ml-2 text-[10px] font-bold text-blue-300 bg-blue-950/80 border border-blue-800/60 px-2 py-0.5 rounded-full">B2B & D2C Hub</span>
                </div>
            </a>
            
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-300 hover:text-white transition px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 backdrop-blur-sm">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Marketplace</span>
            </a>
        </div>
    </header>

    <!-- Main Registration Container (Centered) -->
    <main class="w-full flex-1 flex items-center justify-center px-4 py-8 relative z-10">
        <div class="w-full max-w-lg bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 overflow-hidden transition-all duration-300">
            
            <!-- Card Top Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 sm:p-8 text-white text-center relative">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white mb-2 backdrop-blur-sm">
                    <i class="fa-solid fa-bolt text-yellow-300"></i> Free Instant Account
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight" id="formHeaderTitle">
                    Create Seller Account
                </h1>
                <p class="text-blue-100 text-xs sm:text-sm mt-1.5" id="formHeaderSubtitle">
                    Start selling across India with zero upfront fees & instant store activation.
                </p>
            </div>

            <div class="p-6 sm:p-8 space-y-6">

                <!-- Display Validation Errors -->
                @if ($errors->any())
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                        <div class="font-bold flex items-center gap-2 text-rose-700">
                            <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                            <span>Please fix the following errors:</span>
                        </div>
                        <ul class="list-disc pl-5 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-5" id="registrationForm">
                    @csrf

                    <!-- Role Switcher (Seller vs Buyer Interactive Cards) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Select Account Type <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <!-- Seller Option (Role 3) -->
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200 text-center select-none" id="sellerCardLabel">
                                <input type="radio" name="role_id" value="3" id="roleSeller" class="sr-only" {{ old('role_id', $defaultRole ?? 3) == 3 ? 'checked' : '' }} onchange="updateRoleUI()">
                                <div class="text-2xl mb-1 text-blue-600" id="sellerIcon">
                                    <i class="fa-solid fa-store"></i>
                                </div>
                                <span class="font-bold text-sm text-gray-900" id="sellerTitle">Seller (दुकानदार)</span>
                                <span class="text-[11px] text-gray-500 mt-0.5">Sell products & catalog</span>
                            </label>

                            <!-- Buyer Option (Role 2) -->
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200 text-center select-none" id="buyerCardLabel">
                                <input type="radio" name="role_id" value="2" id="roleBuyer" class="sr-only" {{ old('role_id', $defaultRole ?? 3) == 2 ? 'checked' : '' }} onchange="updateRoleUI()">
                                <div class="text-2xl mb-1 text-gray-400" id="buyerIcon">
                                    <i class="fa-solid fa-bag-shopping"></i>
                                </div>
                                <span class="font-bold text-sm text-gray-700" id="buyerTitle">Buyer (खरीदार)</span>
                                <span class="text-[11px] text-gray-500 mt-0.5">Wholesale & sourcing</span>
                            </label>
                        </div>
                    </div>

                    <!-- Full Name / Business Name -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" id="nameLabel">
                            Full Name / Owner Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-user text-sm"></i>
                            </div>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                placeholder="Enter your full name or trade name"
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm font-medium text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition">
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Business Email Address <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-envelope text-sm"></i>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                placeholder="name@business.com"
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm font-medium text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Password (पासवर्ड) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </div>
                            <input id="password" type="password" name="password" required minlength="8"
                                placeholder="Minimum 8 characters"
                                class="w-full pl-10 pr-11 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm font-medium text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition">
                            <button type="button" onclick="togglePassword('password', 'eyeIcon1')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600">
                                <i class="fa-solid fa-eye text-sm" id="eyeIcon1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Confirm Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-shield-halved text-sm"></i>
                            </div>
                            <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8"
                                placeholder="Re-type your password"
                                class="w-full pl-10 pr-11 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm font-medium text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition">
                            <button type="button" onclick="togglePassword('password_confirmation', 'eyeIcon2')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600">
                                <i class="fa-solid fa-eye text-sm" id="eyeIcon2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Terms & Agreement Notice -->
                    <div class="flex items-start gap-2.5 pt-1 text-xs text-gray-600">
                        <i class="fa-solid fa-circle-check text-blue-600 mt-0.5"></i>
                        <span>By signing up, you agree to VyaparIndia's marketplace standards, privacy guidelines, and verified B2B merchant policy.</span>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" id="submitBtn" class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-sm shadow-xl shadow-blue-600/30 hover:shadow-blue-600/40 transform active:scale-[0.99] transition duration-200 flex items-center justify-center gap-2">
                            <span id="btnText">Create Seller Account</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </div>

                </form>

                <!-- Divider & Login Link -->
                <div class="pt-4 border-t border-gray-100 text-center">
                    <p class="text-xs text-gray-600">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-700 hover:underline ml-1">
                            Log in here
                        </a>
                    </p>
                </div>

            </div>
        </div>
    </main>

    <!-- Footer Trust Info -->
    <footer class="w-full py-6 text-center text-xs text-gray-400 relative z-10">
        <div class="flex items-center justify-center gap-6 mb-2">
            <span class="flex items-center gap-1.5"><i class="fa-solid fa-lock text-green-400"></i> SSL 256-bit Secure</span>
            <span class="flex items-center gap-1.5"><i class="fa-solid fa-truck-fast text-blue-400"></i> Pan-India Logistics</span>
            <span class="flex items-center gap-1.5"><i class="fa-brands fa-whatsapp text-emerald-400"></i> Direct WhatsApp Orders</span>
        </div>
        <p>&copy; {{ date('Y') }} VyaparIndia.online - All rights reserved.</p>
    </footer>

    <!-- Interactive Role Toggle Script -->
    <script>
        function updateRoleUI() {
            const isSeller = document.getElementById('roleSeller').checked;
            const sellerCard = document.getElementById('sellerCardLabel');
            const buyerCard = document.getElementById('buyerCardLabel');
            const sellerIcon = document.getElementById('sellerIcon');
            const buyerIcon = document.getElementById('buyerIcon');
            const headerTitle = document.getElementById('formHeaderTitle');
            const headerSubtitle = document.getElementById('formHeaderSubtitle');
            const btnText = document.getElementById('btnText');

            if (isSeller) {
                // Active Seller styling
                sellerCard.className = "relative flex flex-col p-4 rounded-2xl border-2 border-blue-600 bg-blue-50/50 shadow-md shadow-blue-500/10 cursor-pointer transition-all duration-200 text-center select-none";
                sellerIcon.className = "text-2xl mb-1 text-blue-600";
                
                // Inactive Buyer styling
                buyerCard.className = "relative flex flex-col p-4 rounded-2xl border-2 border-gray-200 bg-white hover:border-gray-300 cursor-pointer transition-all duration-200 text-center select-none";
                buyerIcon.className = "text-2xl mb-1 text-gray-400";

                headerTitle.innerText = "Create Seller Account";
                headerSubtitle.innerText = "Start selling across India with zero upfront fees & instant store activation.";
                btnText.innerText = "Create Seller Account";
            } else {
                // Active Buyer styling
                buyerCard.className = "relative flex flex-col p-4 rounded-2xl border-2 border-blue-600 bg-blue-50/50 shadow-md shadow-blue-500/10 cursor-pointer transition-all duration-200 text-center select-none";
                buyerIcon.className = "text-2xl mb-1 text-blue-600";
                
                // Inactive Seller styling
                sellerCard.className = "relative flex flex-col p-4 rounded-2xl border-2 border-gray-200 bg-white hover:border-gray-300 cursor-pointer transition-all duration-200 text-center select-none";
                sellerIcon.className = "text-2xl mb-1 text-gray-400";

                headerTitle.innerText = "Create Buyer Account";
                headerSubtitle.innerText = "Discover verified wholesale manufacturers and order at direct factory rates.";
                btnText.innerText = "Create Buyer Account";
            }
        }

        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Initialize UI state on load
        document.addEventListener('DOMContentLoaded', updateRoleUI);
    </script>
</body>
</html>
