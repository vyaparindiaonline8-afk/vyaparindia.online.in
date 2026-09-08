@extends('seller-site.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-violet-600 text-white flex items-center justify-center text-lg shadow-lg shadow-violet-600/30">
                    <i class="fa-solid fa-globe"></i>
                </span>
                Custom Domain (brand.com) Connection
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Connect your personal branded domain name (e.g. www.trendymart.in) with automatic Free SSL Certificate.</p>
        </div>
        <a href="{{ route('minisite.show', $sellerPage->slug ?? 'trendymart') }}" target="_blank" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow transition">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Open Storefront
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Connect Domain Card -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm space-y-5">
            <h3 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                <i class="fa-solid fa-link text-indigo-500"></i> Enter Your Domain
            </h3>

            <form action="{{ route('seller.minisite.custom_domain.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Domain Name (Root or Subdomain)</label>
                    <input type="text" name="domain" value="{{ $customDomain->domain ?? 'www.trendymart.in' }}" required placeholder="e.g. www.yourbrand.in" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-900 dark:text-white font-mono">
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 rounded-xl shadow transition">
                    Save Domain & Generate DNS Tokens
                </button>
            </form>

            @if($customDomain)
            <!-- Verification Status -->
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 space-y-3 text-xs">
                <div class="flex justify-between items-center">
                    <span class="text-slate-400">DNS Status:</span>
                    @if($customDomain->dns_status === 'verified')
                        <span class="bg-emerald-500/20 text-emerald-500 border border-emerald-500/30 text-[10px] px-2 py-0.5 rounded font-bold">Verified & Active</span>
                    @else
                        <span class="bg-amber-500/20 text-amber-500 border border-amber-500/30 text-[10px] px-2 py-0.5 rounded font-bold">Pending DNS Propagation</span>
                    @endif
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400">SSL Certificate:</span>
                    <span class="text-emerald-500 font-bold flex items-center gap-1">
                        <i class="fa-solid fa-lock text-[10px]"></i> Active (HTTPS 256-bit Encrypted)
                    </span>
                </div>
                <form action="{{ route('seller.minisite.custom_domain.verify', $customDomain->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 text-white font-bold py-2 rounded-lg transition text-xs border border-slate-700">
                        <i class="fa-solid fa-rotate"></i> Re-Check DNS Status Now
                    </button>
                </form>
            </div>
            @endif
        </div>

        <!-- DNS Configuration Guide -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                <i class="fa-solid fa-server text-violet-500"></i> DNS Setup Instructions (GoDaddy, Namecheap, Cloudflare)
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Apne domain registrar dashboard me jaakar yeh 2 DNS records add karein:</p>

            <div class="space-y-3 text-xs font-mono">
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 space-y-1">
                    <div class="text-[10px] text-indigo-400 font-bold">RECORD 1 (CNAME):</div>
                    <div class="flex justify-between"><span class="text-slate-400">Type:</span> <span class="text-slate-900 dark:text-white">CNAME</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Host / Name:</span> <span class="text-slate-900 dark:text-white">www (or @)</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Points To / Target:</span> <span class="text-emerald-400">cname.vyaparindia.online</span></div>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 space-y-1">
                    <div class="text-[10px] text-amber-400 font-bold">RECORD 2 (TXT Verification):</div>
                    <div class="flex justify-between"><span class="text-slate-400">Type:</span> <span class="text-slate-900 dark:text-white">TXT</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Host / Name:</span> <span class="text-slate-900 dark:text-white">@</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Value:</span> <span class="text-cyan-400">{{ $customDomain->verification_txt ?? 'vyapar-verify-992018472910' }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection