<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($profile) ? 'Edit Business Profile & Banking' : 'Create Business Profile' }} - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.dashboard') }}" class="h-9 w-9 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-base shadow-sm">
                        V
                    </a>
                    <div>
                        <span class="font-black text-gray-900 text-base tracking-tight">VyaparIndia</span>
                        <span class="ml-2 text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Business & Banking Setup</span>
                    </div>
                </div>
                <a href="{{ route('seller.dashboard') }}" class="text-xs font-bold text-gray-600 hover:text-gray-900 flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Back to Dashboard</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

        <div class="mb-6">
            <h1 class="text-2xl font-black text-gray-900">{{ isset($profile) ? 'Edit Business & Banking Details' : 'Create Business Profile' }}</h1>
            <p class="text-xs text-gray-500 mt-1">Setup your firm information, dispatch radius, and UPI/Bank details for instant customer payouts.</p>
        </div>

        <form action="{{ isset($profile) ? route('seller.profile.update') : route('seller.profile.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if(isset($profile))
                @method('PUT')
            @endif

            <!-- 📇 Digital Business Visiting Card Preview -->
            <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-indigo-500/20 relative overflow-hidden">
                <!-- Background Image Layer if set -->
                @if(isset($profile) && $profile->background_image)
                    <div class="absolute inset-0 bg-cover bg-center opacity-20 pointer-events-none" style="background-image: url('{{ $profile->background_image }}');"></div>
                @endif
                <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                    <div class="space-y-2">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-400/30 text-[10px] font-black tracking-wider uppercase">
                            <i class="fa-solid fa-address-card"></i> Digital Business Visiting Card
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white flex items-center gap-2">
                            <span>{{ $profile->company_name ?? 'Your Firm / Company Name' }}</span>
                            @if(isset($profile) && $profile->gst_number)
                                <span class="text-[10px] bg-emerald-500 text-white font-extrabold px-2 py-0.5 rounded-full">GST Verified</span>
                            @endif
                        </h2>
                        <p class="text-xs text-slate-300 max-w-xl leading-relaxed">
                            {{ $profile->address ?? 'Complete address will be shown here' }}, {{ $profile->city ?? 'City' }}, {{ $profile->state ?? 'State' }}
                        </p>
                        <div class="flex items-center gap-4 flex-wrap pt-2 text-xs text-slate-200">
                            <span class="flex items-center gap-1.5"><i class="fa-solid fa-phone text-blue-400"></i> {{ $profile->phone_number ?? '+91 XXXXX XXXXX' }}</span>
                            @if(!empty($profile->whatsapp_number))
                                <span class="flex items-center gap-1.5 text-emerald-400 font-bold"><i class="fa-brands fa-whatsapp text-sm"></i> +91 {{ $profile->whatsapp_number }} (Orders)</span>
                            @endif
                            @if(!empty($profile->support_email))
                                <span class="flex items-center gap-1.5 text-slate-300"><i class="fa-solid fa-envelope text-indigo-400"></i> {{ $profile->support_email }}</span>
                            @endif
                        </div>
                    </div>

                    @if(Auth::user()->sellerPage)
                        <div class="shrink-0 flex flex-col items-center gap-2 bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10">
                            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">Storefront QR</span>
                            <div class="h-20 w-20 bg-white rounded-xl p-1.5 flex items-center justify-center">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('minisite.show', Auth::user()->sellerPage->slug)) }}" alt="QR" class="w-full h-full object-contain">
                            </div>
                            <span class="text-[9px] text-slate-400">Scan to Open Store</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Section 1: Firm Details -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs space-y-4">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Firm & Contact Information</h2>
                        <p class="text-[11px] text-gray-500">This will be shown on your store profile & verified dealer badge</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Company / Firm Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="company_name" value="{{ old('company_name', $profile->company_name ?? '') }}" required placeholder="e.g. Mahaveer Hardware & Sanitaries" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Contact Phone Number <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $profile->phone_number ?? '') }}" required placeholder="e.g. 9876543210" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-emerald-800 mb-1">
                            <i class="fa-brands fa-whatsapp text-emerald-600 mr-1"></i> WhatsApp Order Number (जिस नंबर पर ऑर्डर्स आएं)
                        </label>
                        <div class="flex items-center">
                            <span class="px-2.5 py-2 text-xs bg-emerald-50 text-emerald-800 font-bold border border-r-0 border-emerald-300 rounded-l-xl">+91</span>
                            <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $profile->whatsapp_number ?? ($profile->user->sellerPage->whatsapp_number ?? '')) }}" placeholder="e.g. 9876543210" class="w-full px-3 py-2 rounded-r-xl border border-emerald-300 text-xs font-bold text-emerald-950 focus:border-emerald-500 focus:outline-hidden">
                        </div>
                        <span class="text-[10px] text-gray-500 mt-1 block">Customer order confirmations & WhatsApp carts will route to this number</span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Support Email Address (Optional)</label>
                        <input type="email" name="support_email" value="{{ old('support_email', $profile->support_email ?? ($profile->user->sellerPage->support_email ?? '')) }}" placeholder="e.g. contact@mahaveer.in" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Office / Landline Phone (Optional)</label>
                        <input type="text" name="office_phone" value="{{ old('office_phone', $profile->office_phone ?? '') }}" placeholder="e.g. 0771-2345678 or 9425000000" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">GSTIN Number (Optional)</label>
                        <input type="text" name="gst_number" value="{{ old('gst_number', $profile->gst_number ?? '') }}" placeholder="e.g. 22AAAAA0000A1Z5" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-medium uppercase focus:border-blue-500 focus:outline-hidden">
                    </div>

                    <!-- Custom Visiting Card / Background Banner -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            <i class="fa-solid fa-image text-indigo-600 mr-1"></i> Custom Visiting Card / Profile Background Banner (Optional)
                        </label>
                        <input type="file" name="background_image" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        @if(isset($profile) && $profile->background_image)
                            <div class="mt-2 flex items-center gap-2">
                                <img src="{{ $profile->background_image }}" alt="Current Background" class="h-10 w-24 object-cover rounded-lg border border-gray-200">
                                <span class="text-[11px] text-gray-500">Current Background Image</span>
                            </div>
                        @endif
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Local Delivery / Dispatch Radius (KM)</label>
                        <input type="number" name="dispatch_radius" value="{{ old('dispatch_radius', $profile->dispatch_radius ?? 25) }}" placeholder="25" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Complete Shop / Warehouse Address <span class="text-rose-500">*</span></label>
                    <textarea name="address" rows="2" required placeholder="Shop No, Complex, Road, Landmark" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">{{ old('address', $profile->address ?? '') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">City <span class="text-rose-500">*</span></label>
                        <input type="text" name="city" value="{{ old('city', $profile->city ?? '') }}" required placeholder="e.g. Raipur" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">State <span class="text-rose-500">*</span></label>
                        <input type="text" name="state" value="{{ old('state', $profile->state ?? '') }}" required placeholder="e.g. Chhattisgarh" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Country</label>
                        <input type="text" name="country" value="{{ old('country', $profile->country ?? 'India') }}" required class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-medium bg-gray-50 focus:outline-hidden">
                    </div>
                </div>
            </div>

            <!-- Section 2: GPS Location & Social Media Presence -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs space-y-4">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">GPS Location & Social Media Profiles</h2>
                        <p class="text-[11px] text-gray-500">Google Map link, Google Business, Facebook, Instagram aur YouTube links yahan add karein.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            <i class="fa-solid fa-location-dot text-rose-500 mr-1"></i> Google Maps / GPS Location URL
                        </label>
                        <input type="url" name="google_map_url" value="{{ old('google_map_url', $profile->google_map_url ?? '') }}" placeholder="e.g. https://maps.app.goo.gl/xyz123 or https://goo.gl/maps/..." class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
                        <p class="text-[11px] text-gray-500 mt-1">Apne shop ka Google Maps share link paste karein jisse buyers easily aapki dukan tak pahuchein.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            <i class="fa-brands fa-google text-blue-500 mr-1"></i> Google Business Profile URL
                        </label>
                        <input type="url" name="google_business_url" value="{{ old('google_business_url', $profile->google_business_url ?? '') }}" placeholder="e.g. https://g.page/r/your-business" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            <i class="fa-brands fa-facebook text-blue-600 mr-1"></i> Facebook Page URL
                        </label>
                        <input type="url" name="facebook_url" value="{{ old('facebook_url', $profile->facebook_url ?? '') }}" placeholder="e.g. https://facebook.com/yourshop" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            <i class="fa-brands fa-instagram text-pink-600 mr-1"></i> Instagram Profile URL
                        </label>
                        <input type="url" name="instagram_url" value="{{ old('instagram_url', $profile->instagram_url ?? '') }}" placeholder="e.g. https://instagram.com/yourshop" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            <i class="fa-brands fa-youtube text-red-600 mr-1"></i> YouTube Channel URL
                        </label>
                        <input type="url" name="youtube_url" value="{{ old('youtube_url', $profile->youtube_url ?? '') }}" placeholder="e.g. https://youtube.com/@yourshop" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
                    </div>
                </div>
            </div>

            <!-- Section 3: UPI & Banking Setup -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs space-y-4">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">UPI ID & Bank Account Details</h2>
                        <p class="text-[11px] text-gray-500">Customer jab WhatsApp order ya online order karega to ye payment details unko bill me dikhegi</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- UPI ID -->
                    <div class="md:col-span-2 bg-emerald-50/60 border border-emerald-200 rounded-xl p-4">
                        <label class="block text-xs font-bold text-emerald-900 mb-1">
                            <i class="fa-solid fa-mobile-screen mr-1"></i> Business UPI ID (GPay / PhonePe / Paytm / BHIM)
                        </label>
                        <input type="text" name="upi_id" value="{{ old('upi_id', $profile->upi_id ?? '') }}" placeholder="e.g. 9876543210@paytm or firmname@okhdfcbank" class="w-full px-3 py-2 rounded-xl border border-emerald-300 text-xs font-bold bg-white focus:border-emerald-500 focus:outline-hidden">
                        <p class="text-[11px] text-emerald-700 mt-1">Customer ko WhatsApp order message me UPI link milega jisse wo direct click karke payment bhej sakega.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Account Holder Name</label>
                        <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder', $profile->bank_account_holder ?? '') }}" placeholder="e.g. Rajesh Kumar" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Bank Name</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $profile->bank_name ?? '') }}" placeholder="e.g. State Bank of India / HDFC Bank" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Bank Account Number</label>
                        <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $profile->bank_account_number ?? '') }}" placeholder="e.g. 50100234567890" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-mono font-medium focus:border-blue-500 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">IFSC Code</label>
                        <input type="text" name="bank_ifsc" value="{{ old('bank_ifsc', $profile->bank_ifsc ?? '') }}" placeholder="e.g. SBIN0001234" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-mono uppercase font-medium focus:border-blue-500 focus:outline-hidden">
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('seller.dashboard') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-100 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ isset($profile) ? 'Save & Update Profile' : 'Save Business Profile' }}</span>
                </button>
            </div>
        </form>

    </main>

</body>
</html>
