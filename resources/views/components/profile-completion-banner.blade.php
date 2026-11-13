@php
    $authUser = Auth::user();
    $sellerProfile = $authUser ? ($authUser->sellerProfile ?? null) : null;
    
    // Calculate Profile Completion %
    $checks = [
        'firm' => !empty($sellerProfile?->company_name),
        'contact' => !empty($sellerProfile?->phone_number) && (!empty($sellerProfile?->whatsapp_number) || !empty($sellerProfile?->support_email)),
        'address' => !empty($sellerProfile?->address) && !empty($sellerProfile?->city) && !empty($sellerProfile?->state),
        'tax' => !empty($sellerProfile?->gst_number) || !empty($sellerProfile?->gst_enrolment_id),
        'bank' => !empty($sellerProfile?->upi_id) || !empty($sellerProfile?->bank_account_number),
        'products' => ($authUser ? \App\Models\Product::where('user_id', $authUser->id)->exists() : false),
        'channels' => ($authUser ? \App\Models\Channel::where('user_id', $authUser->id)->where('is_active', true)->exists() : false),
    ];
    
    // Weights (total 100%)
    $weights = [
        'firm' => 15,
        'contact' => 15,
        'address' => 15,
        'tax' => 15,
        'bank' => 15,
        'products' => 15,
        'channels' => 10,
    ];
    
    $completionScore = 0;
    $missingItems = [];
    
    if ($checks['firm']) { $completionScore += $weights['firm']; } else { $missingItems[] = 'फर्म/दुकान का नाम'; }
    if ($checks['contact']) { $completionScore += $weights['contact']; } else { $missingItems[] = 'WhatsApp/ईमेल'; }
    if ($checks['address']) { $completionScore += $weights['address']; } else { $missingItems[] = 'दुकान का पता & शहर'; }
    if ($checks['tax']) { $completionScore += $weights['tax']; } else { $missingItems[] = 'GST या Enrolment ID'; }
    if ($checks['bank']) { $completionScore += $weights['bank']; } else { $missingItems[] = 'UPI ID या बैंक खाता'; }
    if ($checks['products']) { $completionScore += $weights['products']; } else { $missingItems[] = 'पहला प्रोडक्ट जोड़ें'; }
    if ($checks['channels']) { $completionScore += $weights['channels']; } else { $missingItems[] = 'मार्केटप्लेस / स्टोर नाम'; }
@endphp

@if($completionScore < 100)
    <div class="bg-gradient-to-r from-amber-500 via-indigo-600 to-purple-600 text-white px-4 py-2.5 shadow-md sticky top-16 z-20 transition-all border-b border-white/10" id="global-profile-completion-bar">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white/20 backdrop-blur-md font-black text-amber-200 border border-white/20 shadow-xs">
                    {{ $completionScore }}%
                </span>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <span class="font-black text-sm tracking-tight text-white flex items-center gap-1.5">
                            <i class="fa-solid fa-bolt text-amber-300"></i> आपकी प्रोफाइल {{ $completionScore }}% पूरी है
                        </span>
                        <span class="hidden md:inline text-[11px] font-semibold text-white/80 bg-white/10 px-2 py-0.5 rounded-full border border-white/15">
                            {{ 100 - $completionScore }}% बाकी
                        </span>
                    </div>
                    <p class="text-[11px] text-white/90 truncate mt-0.5">
                        बाकी जानकारी: <strong class="text-amber-200">{{ implode(', ', array_slice($missingItems, 0, 3)) }}</strong>{{ count($missingItems) > 3 ? ' व अन्य' : '' }}
                    </p>
                </div>
            </div>

            <!-- Progress Meter & Action Button -->
            <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                <div class="w-32 sm:w-44 bg-black/25 rounded-full h-2.5 overflow-hidden border border-white/20 shrink-0 hidden md:block">
                    <div class="bg-gradient-to-r from-amber-300 via-emerald-300 to-emerald-400 h-2.5 rounded-full transition-all duration-500 shadow-xs" style="width: {{ $completionScore }}%;"></div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('seller.profile.edit') }}" class="px-3.5 py-1.5 rounded-xl bg-white hover:bg-amber-50 text-indigo-950 font-black text-xs shadow-md transition flex items-center gap-1.5 hover:scale-102 active:scale-98">
                        <i class="fa-solid fa-pen-to-square text-indigo-600"></i>
                        <span>प्रोफाइल पूरी करें (100% ⚡)</span>
                    </a>
                    <button type="button" onclick="document.getElementById('global-profile-completion-bar').style.display='none'" class="h-7 w-7 rounded-lg bg-black/20 hover:bg-black/30 text-white flex items-center justify-center text-xs transition" title="अभी बंद करें">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
