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
        <div class="w-full max-w-xl bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 overflow-hidden transition-all duration-300">
            
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
                                <span class="font-bold text-sm text-gray-900" id="sellerTitle">Seller (दुकानदार / सप्लायर)</span>
                                <span class="text-[11px] text-gray-500 mt-0.5">Sell products, store & dropship</span>
                            </label>

                            <!-- Buyer Option (Role 2) -->
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200 text-center select-none" id="buyerCardLabel">
                                <input type="radio" name="role_id" value="2" id="roleBuyer" class="sr-only" {{ old('role_id', $defaultRole ?? 3) == 2 ? 'checked' : '' }} onchange="updateRoleUI()">
                                <div class="text-2xl mb-1 text-gray-400" id="buyerIcon">
                                    <i class="fa-solid fa-bag-shopping"></i>
                                </div>
                                <span class="font-bold text-sm text-gray-700" id="buyerTitle">Buyer (खरीदार / व्यापारी)</span>
                                <span class="text-[11px] text-gray-500 mt-0.5">Wholesale sourcing & quotes</span>
                            </label>
                        </div>
                    </div>

                    <!-- Firm / Company Name (Essential for Sellers) -->
                    <div id="firmNameContainer">
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="firm_name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Firm / Business Name (फर्म या दूकान का नाम) <span class="text-rose-500" id="firmRequiredStar">*</span>
                            </label>
                            <span class="text-[11px] text-blue-600 font-semibold">Storefront Title</span>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-building text-sm"></i>
                            </div>
                            <input id="firm_name" type="text" name="firm_name" value="{{ old('firm_name') }}"
                                placeholder="e.g. Shree Ganesh Hardware & Sanitary"
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm font-medium text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition">
                        </div>
                    </div>

                    <!-- Owner / Contact Person Name -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" id="nameLabel">
                            Owner Name (मालिक का नाम) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-user text-sm"></i>
                            </div>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                placeholder="Enter your full name"
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm font-medium text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition">
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Email Address (ईमेल) <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] text-gray-500 font-semibold flex items-center gap-1">
                                <i class="fa-solid fa-shield text-blue-500"></i> Email OTP verification soon
                            </span>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-envelope text-sm"></i>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                placeholder="name@business.com"
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm font-medium text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition">
                        </div>
                    </div>

                    <!-- City & Pincode (Optional with GPS Auto-Detect) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Business Location (स्थान) <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <!-- GPS Auto-Detect Button -->
                            <button type="button" onclick="detectGPSLocation()" id="gpsBtn" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-2.5 py-1 rounded-lg transition active:scale-95">
                                <i class="fa-solid fa-location-crosshairs text-blue-600" id="gpsIcon"></i>
                                <span id="gpsText">Auto-Detect via GPS</span>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- City Input -->
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-city text-xs"></i>
                                </div>
                                <input id="city" type="text" name="city" value="{{ old('city') }}"
                                    placeholder="City (e.g. Rajkot, Surat)"
                                    class="w-full pl-9 pr-3 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm font-medium text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition">
                            </div>

                            <!-- Pincode Input -->
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-location-dot text-xs"></i>
                                </div>
                                <input id="pincode" type="text" name="pincode" value="{{ old('pincode') }}" maxlength="6"
                                    placeholder="6-Digit Pincode"
                                    class="w-full pl-9 pr-3 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm font-medium text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition">
                            </div>
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

                    <!-- Future Verification Roadmap Notice -->
                    <div class="p-3 bg-blue-50/80 border border-blue-200/70 rounded-2xl text-[11px] text-blue-900 flex items-start gap-2.5">
                        <i class="fa-solid fa-circle-info text-blue-600 mt-0.5 text-xs"></i>
                        <div>
                            <strong>Merchant Protection Policy:</strong> VyaparIndia uses verified seller credentials. Mobile & Email OTP verification badges will be activated for zero-fraud trust.
                        </div>
                    </div>

                    <!-- Terms & Agreement Notice -->
                    <div class="flex items-start gap-2 pt-1 text-xs text-gray-600">
                        <i class="fa-solid fa-circle-check text-blue-600 mt-0.5"></i>
                        <span>By signing up, you agree to VyaparIndia's <a href="{{ route('terms') }}" class="text-blue-600 hover:underline">Terms of Service</a> & <a href="{{ route('privacy') }}" class="text-blue-600 hover:underline">Privacy Policy</a>.</span>
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

    <!-- Footer Trust Info & Links -->
    <footer class="w-full py-6 text-center text-xs text-gray-400 relative z-10">
        <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6 mb-3">
            <a href="{{ route('about') }}" class="hover:text-gray-200 transition">About Us</a>
            <span>•</span>
            <a href="{{ route('help') }}" class="hover:text-gray-200 transition">Help & Support</a>
            <span>•</span>
            <a href="{{ route('privacy') }}" class="hover:text-gray-200 transition">Privacy Policy</a>
            <span>•</span>
            <a href="{{ route('terms') }}" class="hover:text-gray-200 transition">Terms of Service</a>
        </div>
        <div class="flex items-center justify-center gap-6 mb-2">
            <span class="flex items-center gap-1.5"><i class="fa-solid fa-lock text-green-400"></i> SSL 256-bit Secure</span>
            <span class="flex items-center gap-1.5"><i class="fa-solid fa-truck-fast text-blue-400"></i> Pan-India Logistics</span>
            <span class="flex items-center gap-1.5"><i class="fa-brands fa-whatsapp text-emerald-400"></i> Direct WhatsApp Orders</span>
        </div>
        <p>&copy; {{ date('Y') }} VyaparIndia.online - All rights reserved.</p>
    </footer>

    <!-- Interactive Role Toggle & GPS Script -->
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
            const firmRequiredStar = document.getElementById('firmRequiredStar');
            const nameLabel = document.getElementById('nameLabel');

            if (isSeller) {
                sellerCard.className = "relative flex flex-col p-4 rounded-2xl border-2 border-blue-600 bg-blue-50/50 shadow-md shadow-blue-500/10 cursor-pointer transition-all duration-200 text-center select-none";
                sellerIcon.className = "text-2xl mb-1 text-blue-600";
                
                buyerCard.className = "relative flex flex-col p-4 rounded-2xl border-2 border-gray-200 bg-white hover:border-gray-300 cursor-pointer transition-all duration-200 text-center select-none";
                buyerIcon.className = "text-2xl mb-1 text-gray-400";

                headerTitle.innerText = "Create Seller Account";
                headerSubtitle.innerText = "Start selling across India with zero upfront fees & instant store activation.";
                btnText.innerText = "Create Seller Account";
                firmRequiredStar.style.display = "inline";
                nameLabel.innerText = "Owner Name (मालिक का नाम) *";
            } else {
                buyerCard.className = "relative flex flex-col p-4 rounded-2xl border-2 border-blue-600 bg-blue-50/50 shadow-md shadow-blue-500/10 cursor-pointer transition-all duration-200 text-center select-none";
                buyerIcon.className = "text-2xl mb-1 text-blue-600";
                
                sellerCard.className = "relative flex flex-col p-4 rounded-2xl border-2 border-gray-200 bg-white hover:border-gray-300 cursor-pointer transition-all duration-200 text-center select-none";
                sellerIcon.className = "text-2xl mb-1 text-gray-400";

                headerTitle.innerText = "Create Buyer Account";
                headerSubtitle.innerText = "Discover verified wholesale manufacturers and order at direct factory rates.";
                btnText.innerText = "Create Buyer Account";
                firmRequiredStar.style.display = "none";
                nameLabel.innerText = "Your Full Name (आपका नाम) *";
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

        // HTML5 Geolocation with free reverse geocoding
        function detectGPSLocation() {
            const gpsBtn = document.getElementById('gpsBtn');
            const gpsText = document.getElementById('gpsText');
            const gpsIcon = document.getElementById('gpsIcon');

            if (!navigator.geolocation) {
                alert("Geolocation is not supported by your browser.");
                return;
            }

            gpsText.innerText = "Locating...";
            gpsIcon.className = "fa-solid fa-spinner fa-spin text-blue-600";
            gpsBtn.disabled = true;

            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;

                    try {
                        const res = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`);
                        const data = await res.json();
                        
                        if (data && data.address) {
                            const city = data.address.city || data.address.town || data.address.state_district || data.address.county || "";
                            const pincode = data.address.postcode || "";
                            
                            if (city) document.getElementById('city').value = city;
                            if (pincode) document.getElementById('pincode').value = pincode;

                            gpsText.innerText = "Location Detected!";
                            gpsIcon.className = "fa-solid fa-check text-green-600";
                            setTimeout(() => {
                                gpsText.innerText = "Auto-Detect via GPS";
                                gpsIcon.className = "fa-solid fa-location-crosshairs text-blue-600";
                                gpsBtn.disabled = false;
                            }, 3000);
                        } else {
                            throw new Error("Address not found");
                        }
                    } catch (e) {
                        gpsText.innerText = "GPS Failed";
                        gpsIcon.className = "fa-solid fa-triangle-exclamation text-amber-500";
                        gpsBtn.disabled = false;
                    }
                },
                (error) => {
                    gpsText.innerText = "GPS Denied";
                    gpsIcon.className = "fa-solid fa-location-crosshairs text-blue-600";
                    gpsBtn.disabled = false;
                    alert("Please allow location access to auto-fill your city.");
                },
                { timeout: 10000 }
            );
        }

        document.addEventListener('DOMContentLoaded', updateRoleUI);
    </script>
</body>
</html>
