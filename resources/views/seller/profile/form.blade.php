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

        <form action="{{ isset($profile) ? route('seller.profile.update') : route('seller.profile.store') }}" method="POST" class="space-y-6">
            @csrf
            @if(isset($profile))
                @method('PUT')
            @endif

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
                        <label class="block text-xs font-bold text-gray-700 mb-1">Business Phone / WhatsApp <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $profile->phone_number ?? '') }}" required placeholder="e.g. 9876543210" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Office / Landline Phone (Optional)</label>
                        <input type="text" name="office_phone" value="{{ old('office_phone', $profile->office_phone ?? '') }}" placeholder="e.g. 0771-2345678 or 9425000000" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-medium focus:border-blue-500 focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">GSTIN Number (Optional)</label>
                        <input type="text" name="gst_number" value="{{ old('gst_number', $profile->gst_number ?? '') }}" placeholder="e.g. 22AAAAA0000A1Z5" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-medium uppercase focus:border-blue-500 focus:outline-hidden">
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
