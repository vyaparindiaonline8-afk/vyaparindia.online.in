<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax & CA Compliance Center - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col">

    <!-- Top Navigation Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('seller.dashboard') }}" class="h-9 w-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition" title="Dashboard">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 text-white flex items-center justify-center text-lg font-black shadow-md shadow-indigo-600/30">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <h1 class="font-black text-slate-900 text-base sm:text-lg tracking-tight flex items-center gap-2">
                        <span>Tax & CA Compliance Center</span>
                        <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">GSTR-1 & ITC Ready</span>
                    </h1>
                    <p class="text-[11px] text-slate-500 hidden sm:block">मल्टी-चैनल सेल्स, ड्रॉपशिपिंग, अमेज़न/मीशो फ़ीस ITC व सीए रेडी 3-टैब पैकेज</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="openTaxProfileModal()" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5 border border-slate-300 cursor-pointer">
                    <i class="fa-solid fa-user-shield text-indigo-600"></i>
                    <span>⚙️ Tax Profile (GST Setup)</span>
                </button>
                <a href="{{ route('seller.channels.index') }}" class="px-3.5 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-800 font-bold text-xs transition flex items-center gap-1.5 border border-indigo-200">
                    <i class="fa-solid fa-cubes text-indigo-600"></i>
                    <span>Channels Hub</span>
                </a>
                <a href="{{ route('seller.tax_compliance.export_ca_package', ['from_date' => $fromDate, 'to_date' => $toDate, 'channel' => $channel]) }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs shadow-md shadow-emerald-600/25 transition flex items-center gap-2 active:scale-95 cursor-pointer">
                    <i class="fa-solid fa-file-excel text-amber-300"></i>
                    <span>📥 Download CA Package (.xlsx)</span>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6 flex-1 w-full">

        <!-- Flash Success Notification -->
        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-xs">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- 🏢 Tax Profile & Current Scheme Status Bar -->
        <div class="bg-gradient-to-r from-indigo-950 via-slate-900 to-purple-950 text-white rounded-3xl p-5 shadow-xl border border-indigo-900/60 flex flex-wrap items-center justify-between gap-4">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs text-indigo-200 font-medium">व्यापारिक नाम:</span>
                    <span class="font-black text-sm text-white tracking-wide">{{ $reportData['seller_info']['company_name'] }}</span>
                    
                    @if($reportData['seller_info']['tax_scheme'] === 'regular')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-400/40">
                            ✓ Regular GST (100% ITC Rebate Active)
                        </span>
                    @elseif($reportData['seller_info']['tax_scheme'] === 'composition')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500/20 text-amber-300 border border-amber-400/40">
                            ⚠️ Composition Scheme (1% Flat Tax - No ITC)
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-purple-500/20 text-purple-300 border border-purple-400/40">
                            🛡️ Non-GST Seller (Meesho Enrolment ID)
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-4 text-xs text-slate-300 flex-wrap">
                    <span><i class="fa-solid fa-id-card text-indigo-400"></i> GSTIN: <b class="font-mono text-white">{{ $reportData['seller_info']['gst_number'] ?: ($reportData['seller_info']['gst_enrolment_id'] ?: 'Not Provided') }}</b></span>
                    <span><i class="fa-solid fa-location-dot text-indigo-400"></i> राज्य: <b class="text-white">{{ $reportData['seller_info']['state'] }}</b></span>
                    <span><i class="fa-solid fa-calendar-check text-indigo-400"></i> सक्रिय अवधि: <b class="text-amber-300">{{ $reportData['period_label'] }}</b></span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="openTaxProfileModal()" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition border border-white/20 flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>टैक्स प्रोफाइल बदलें</span>
                </button>
            </div>
        </div>

        <!-- 🔍 Filter Controls: Platform Channels + Date Range (Custom Range Supported) -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-4">
            
            <!-- Platform Channel Tabs -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
                <span class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-store text-indigo-600"></i>
                    <span>प्लेटफॉर्म चुनें (Platform Channels):</span>
                </span>
                
                <div class="flex items-center gap-1.5 flex-wrap">
                    @php
                        $channels = [
                            'all' => ['label' => 'सभी प्लेटफॉर्म (All)', 'icon' => 'fa-layer-group'],
                            'vyaparindia' => ['label' => 'Direct Store', 'icon' => 'fa-globe'],
                            'dropship' => ['label' => 'Dropshipping', 'icon' => 'fa-truck-fast'],
                            'amazon' => ['label' => 'Amazon', 'icon' => 'fa-brands fa-amazon text-amber-500'],
                            'meesho' => ['label' => 'Meesho', 'icon' => 'fa-m text-pink-600'],
                            'flipkart' => ['label' => 'Flipkart', 'icon' => 'fa-bolt text-blue-500'],
                        ];
                    @endphp

                    @foreach($channels as $cKey => $cInfo)
                        <a href="{{ route('seller.tax_compliance.index', ['channel' => $cKey, 'from_date' => $fromDate, 'to_date' => $toDate, 'period_preset' => $preset]) }}" 
                           class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $channel === $cKey ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                            <i class="{{ $cInfo['icon'] }}"></i>
                            <span>{{ $cInfo['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Date Range Filter Form (Quick Presets + Custom Date Pickers) -->
            <form method="GET" action="{{ route('seller.tax_compliance.index') }}" class="flex flex-wrap items-center justify-between gap-3 text-xs">
                <input type="hidden" name="channel" value="{{ $channel }}">

                <!-- Quick Presets -->
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="text-slate-500 font-bold mr-1">तारीख चुनें:</span>
                    <button type="submit" name="period_preset" value="this_month" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $preset === 'this_month' ? 'bg-slate-900 text-white shadow-2xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        इस महीने (This Month)
                    </button>
                    <button type="submit" name="period_preset" value="last_month" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $preset === 'last_month' ? 'bg-slate-900 text-white shadow-2xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        पिछला महीना (Last Month)
                    </button>
                    <button type="submit" name="period_preset" value="this_quarter" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $preset === 'this_quarter' ? 'bg-slate-900 text-white shadow-2xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        यह क्वार्टर (Quarter)
                    </button>
                    <button type="submit" name="period_preset" value="financial_year" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $preset === 'financial_year' ? 'bg-slate-900 text-white shadow-2xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        वित्तीय वर्ष (FY)
                    </button>
                </div>

                <!-- Custom Date Inputs (Aapki Requirement) -->
                <div class="flex items-center gap-2 flex-wrap bg-slate-50 p-1.5 rounded-2xl border border-slate-200">
                    <span class="text-[11px] font-bold text-slate-600 pl-1">कस्टम तारीख:</span>
                    <div class="flex items-center gap-1">
                        <span class="text-[10px] text-slate-400">From</span>
                        <input type="date" name="from_date" value="{{ $fromDate }}" class="px-2.5 py-1 bg-white border border-slate-300 rounded-lg text-xs font-mono font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="text-[10px] text-slate-400">To</span>
                        <input type="date" name="to_date" value="{{ $toDate }}" class="px-2.5 py-1 bg-white border border-slate-300 rounded-lg text-xs font-mono font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <button type="submit" class="px-3 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition cursor-pointer shadow-2xs">
                        <i class="fa-solid fa-filter mr-1"></i> फ़िल्टर करें
                    </button>
                </div>
            </form>

        </div>

        <!-- 📊 4 KPI Summary Cards (Financial & Tax Reconciliation) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Card 1: Gross Sales -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm space-y-2">
                <div class="flex items-center justify-between text-xs text-slate-500">
                    <span class="font-bold">कुल बिक्री (Gross Sales)</span>
                    <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm"><i class="fa-solid fa-cart-shopping"></i></span>
                </div>
                <div class="font-black text-2xl text-slate-900 tracking-tight">₹{{ number_format($reportData['summary']['gross_sales'], 2) }}</div>
                <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1 border-t border-slate-100">
                    <span>कुल ऑर्डर्स: <b>{{ $reportData['summary']['total_orders'] }}</b></span>
                    <span>Taxable: ₹{{ number_format($reportData['summary']['taxable_sales'], 2) }}</span>
                </div>
            </div>

            <!-- Card 2: Output GST Liability -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm space-y-2">
                <div class="flex items-center justify-between text-xs text-slate-500">
                    <span class="font-bold">बिक्री पर GST (Output Tax)</span>
                    <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm"><i class="fa-solid fa-receipt"></i></span>
                </div>
                <div class="font-black text-2xl text-purple-950 tracking-tight">₹{{ number_format($reportData['summary']['total_output_tax'], 2) }}</div>
                <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1 border-t border-slate-100">
                    <span>CGST: ₹{{ number_format($reportData['summary']['total_cgst'], 2) }}</span>
                    <span>SGST: ₹{{ number_format($reportData['summary']['total_sgst'], 2) }}</span>
                    <span>IGST: ₹{{ number_format($reportData['summary']['total_igst'], 2) }}</span>
                </div>
            </div>

            <!-- Card 3: Eligible ITC on Platform & Courier -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm space-y-2">
                <div class="flex items-center justify-between text-xs text-slate-500">
                    <span class="font-bold">फ़ार्म फ़ीस पर ITC Rebate</span>
                    <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm"><i class="fa-solid fa-shield-halved"></i></span>
                </div>
                <div class="font-black text-2xl text-emerald-700 tracking-tight">₹{{ number_format($reportData['summary']['total_itc_claimable'], 2) }}</div>
                <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1 border-t border-slate-100">
                    <span>प्लेटफॉर्म फ़ीस: ₹{{ number_format($reportData['summary']['total_platform_expenses'], 2) }}</span>
                    <span class="text-emerald-700 font-bold">1% TCS: ₹{{ number_format($reportData['summary']['total_tcs_deducted'], 2) }}</span>
                </div>
            </div>

            <!-- Card 4: Net Tax Payable in Cash -->
            <div class="bg-gradient-to-br from-indigo-500 to-purple-700 text-white p-5 rounded-3xl shadow-md space-y-2">
                <div class="flex items-center justify-between text-xs text-indigo-100">
                    <span class="font-bold">सच्चा टैक्स देय (Net Cash Tax)</span>
                    <span class="w-8 h-8 rounded-xl bg-white/20 text-white flex items-center justify-center text-sm"><i class="fa-solid fa-hand-holding-dollar"></i></span>
                </div>
                <div class="font-black text-2xl text-white tracking-tight">₹{{ number_format($reportData['summary']['net_tax_payable'], 2) }}</div>
                <div class="text-[10px] text-indigo-100 pt-1 border-t border-white/20 leading-tight">
                    Output Tax (₹{{ number_format($reportData['summary']['total_output_tax'], 0) }}) - ITC Rebate (₹{{ number_format($reportData['summary']['total_itc_claimable'], 0) }})
                </div>
            </div>

        </div>

        <!-- 📑 CA Recommendation Banner -->
        <div class="p-4 bg-gradient-to-r from-purple-50 via-indigo-50 to-blue-50 border border-purple-200 rounded-2xl flex items-start gap-3 shadow-2xs">
            <div class="w-9 h-9 rounded-xl bg-purple-600 text-white flex items-center justify-center shrink-0 text-sm font-bold shadow-xs">
                <i class="fa-solid fa-user-tie"></i>
            </div>
            <div class="space-y-0.5 text-xs">
                <h4 class="font-black text-purple-950">सीए (CA) को भेजने हेतु सारांश व निर्देश:</h4>
                <p class="text-purple-800 leading-relaxed">{{ $reportData['summary']['ca_recommendation'] }}</p>
            </div>
        </div>

        <!-- 📥 Quick Download Strip -->
        <div class="bg-white p-4 rounded-3xl border border-slate-200 flex flex-wrap items-center justify-between gap-3 shadow-sm">
            <div class="flex items-center gap-2 text-xs">
                <i class="fa-solid fa-download text-indigo-600 font-bold"></i>
                <span class="font-bold text-slate-800">सीए को भेजने के लिए रिपोर्ट्स डाउनलोड करें:</span>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <!-- CSV GSTR-1 -->
                <a href="{{ route('seller.tax_compliance.export_gstr1', ['from_date' => $fromDate, 'to_date' => $toDate, 'channel' => $channel]) }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5 border border-slate-300">
                    <i class="fa-solid fa-file-csv text-blue-600"></i>
                    <span>1. Sales Register (GSTR-1) .csv</span>
                </a>

                <!-- CSV ITC -->
                <a href="{{ route('seller.tax_compliance.export_itc', ['from_date' => $fromDate, 'to_date' => $toDate, 'channel' => $channel]) }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5 border border-slate-300">
                    <i class="fa-solid fa-file-csv text-emerald-600"></i>
                    <span>2. Platform ITC Sheet .csv</span>
                </a>

                <!-- Full 3-Tab Excel Workbook -->
                <a href="{{ route('seller.tax_compliance.export_ca_package', ['from_date' => $fromDate, 'to_date' => $toDate, 'channel' => $channel]) }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs shadow-md shadow-emerald-600/30 transition flex items-center gap-1.5 active:scale-95">
                    <i class="fa-solid fa-file-excel text-amber-300"></i>
                    <span>3. Complete CA Package (3-Tab Excel)</span>
                </a>
            </div>
        </div>

        <!-- 📋 Data Examination Ledger Table -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-table-list text-indigo-600"></i>
                    <h3 class="font-black text-slate-900 text-sm">बिक्री बहीखाता (Outward Sales Ledger - GSTR-1 View)</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800 font-mono">{{ count($reportData['sales']) }} Orders</span>
                </div>
                <span class="text-[11px] text-slate-500">सीए को यही डेटा Sheet 1 में मिलता है</span>
            </div>

            <div class="overflow-x-auto max-h-[60vh]">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-100 text-[10px] font-black text-slate-600 uppercase border-b border-slate-200 sticky top-0 backdrop-blur-xs">
                        <tr>
                            <th class="p-3">Order / Inv No</th>
                            <th class="p-3">Date</th>
                            <th class="p-3">Channel</th>
                            <th class="p-3">POS (State)</th>
                            <th class="p-3">Type</th>
                            <th class="p-3">Items / Products</th>
                            <th class="p-3 text-right">Taxable (₹)</th>
                            <th class="p-3 text-center">Rate</th>
                            <th class="p-3 text-right">CGST (₹)</th>
                            <th class="p-3 text-right">SGST (₹)</th>
                            <th class="p-3 text-right">IGST (₹)</th>
                            <th class="p-3 text-right">Total (₹)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($reportData['sales'] as $sale)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-3 font-mono font-bold text-indigo-900">{{ $sale['order_number'] }}</td>
                                <td class="p-3 text-slate-500 whitespace-nowrap">{{ $sale['date'] }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                        {{ $sale['channel'] }}
                                    </span>
                                </td>
                                <td class="p-3 text-slate-700 font-semibold">{{ $sale['pos_state'] }}</td>
                                <td class="p-3 text-[10px] text-slate-500">{{ $sale['supply_type'] }}</td>
                                <td class="p-3 text-slate-900 max-w-xs truncate" title="{{ $sale['product_name'] }}">{{ $sale['product_name'] }}</td>
                                <td class="p-3 text-right font-mono font-bold text-slate-800">₹{{ number_format($sale['taxable_value'], 2) }}</td>
                                <td class="p-3 text-center font-mono text-slate-600">{{ $sale['gst_rate'] }}%</td>
                                <td class="p-3 text-right font-mono text-slate-600">₹{{ number_format($sale['cgst'], 2) }}</td>
                                <td class="p-3 text-right font-mono text-slate-600">₹{{ number_format($sale['sgst'], 2) }}</td>
                                <td class="p-3 text-right font-mono text-slate-600">₹{{ number_format($sale['igst'], 2) }}</td>
                                <td class="p-3 text-right font-mono font-black text-slate-950">₹{{ number_format($sale['total_amount'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="p-12 text-center text-slate-400 space-y-2">
                                    <i class="fa-solid fa-receipt text-3xl text-slate-300 block"></i>
                                    <p class="font-bold text-sm">इस अवधि और चैनल के लिए कोई ऑर्डर्स नहीं मिले।</p>
                                    <p class="text-xs">फ़िल्टर में तारीख बदलकर पुनः प्रयास करें।</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- ======================================================= -->
    <!-- ⚙️ TAX PROFILE & SCHEME SETUP MODAL                     -->
    <!-- ======================================================= -->
    <div id="taxProfileModal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs hidden items-center justify-center p-4" onclick="closeTaxProfileModal()">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4 my-auto animate-in fade-in zoom-in duration-150" onclick="event.stopPropagation()">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-lg font-bold shadow-md">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-900 text-sm">Tax & Business Profile Setup</h3>
                        <p class="text-[11px] text-slate-500">सीए रिपोर्ट व ई-कॉमर्स टैक्स स्कीम्स सेट करें</p>
                    </div>
                </div>
                <button type="button" onclick="closeTaxProfileModal()" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('seller.tax_compliance.update_profile') }}" class="space-y-4 text-xs">
                @csrf

                <!-- Tax Scheme Radio Selection -->
                <div class="space-y-2">
                    <label class="font-black text-slate-800 block">आपकी टैक्स स्कीम (Tax Scheme):</label>
                    
                    <!-- Regular GST -->
                    <label class="p-3 rounded-2xl border-2 cursor-pointer flex items-start gap-3 transition {{ ($reportData['seller_info']['tax_scheme'] ?? 'regular') === 'regular' ? 'border-indigo-600 bg-indigo-50/40' : 'border-slate-200 hover:border-slate-300' }}">
                        <input type="radio" name="tax_scheme" value="regular" {{ ($reportData['seller_info']['tax_scheme'] ?? 'regular') === 'regular' ? 'checked' : '' }} class="mt-1 text-indigo-600 accent-indigo-600">
                        <div>
                            <div class="font-black text-slate-900">Regular GST Dealer (Recommended for Amazon / All-India)</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">कूरियर व अमेज़न कमीशन के 18% GST का 100% ITC रिबेट मिलता है। पूरे भारत में माल बेच सकते हैं।</div>
                        </div>
                    </label>

                    <!-- Composition Scheme -->
                    <label class="p-3 rounded-2xl border-2 cursor-pointer flex items-start gap-3 transition {{ ($reportData['seller_info']['tax_scheme'] ?? '') === 'composition' ? 'border-indigo-600 bg-indigo-50/40' : 'border-slate-200 hover:border-slate-300' }}">
                        <input type="radio" name="tax_scheme" value="composition" {{ ($reportData['seller_info']['tax_scheme'] ?? '') === 'composition' ? 'checked' : '' }} class="mt-1 text-indigo-600 accent-indigo-600">
                        <div>
                            <div class="font-black text-slate-900">Composition Scheme (1% Flat Turnover Tax)</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">कोई ITC रिबेट नहीं मिलता (कमीशन/कूरियर का GST सीधा खर्च बनता है)। केवल अपने राज्य के अंदर डिलीवरी।</div>
                        </div>
                    </label>

                    <!-- Non-GST / Unregistered -->
                    <label class="p-3 rounded-2xl border-2 cursor-pointer flex items-start gap-3 transition {{ ($reportData['seller_info']['tax_scheme'] ?? '') === 'unregistered' ? 'border-indigo-600 bg-indigo-50/40' : 'border-slate-200 hover:border-slate-300' }}">
                        <input type="radio" name="tax_scheme" value="unregistered" {{ ($reportData['seller_info']['tax_scheme'] ?? '') === 'unregistered' ? 'checked' : '' }} class="mt-1 text-indigo-600 accent-indigo-600">
                        <div>
                            <div class="font-black text-slate-900">Non-GST / Unregistered Seller (Meesho Enrolment ID)</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">मीशो आदि पर बिना GST नंबर केवल Enrolment ID से राज्य के भीतर बिक्री (Zero GST दायित्व)।</div>
                        </div>
                    </label>
                </div>

                <!-- GST Number -->
                <div class="space-y-1">
                    <label class="font-bold text-slate-700">GSTIN (15 Digit GST Number):</label>
                    <input type="text" name="gst_number" value="{{ $reportData['seller_info']['gst_number'] }}" placeholder="उदा. 07AAAAA0000A1Z5" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl font-mono uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Enrolment ID for Meesho Non-GST -->
                <div class="space-y-1">
                    <label class="font-bold text-slate-700">Enrolment ID (यदि बिना GST केवल मीशो बेचते हैं):</label>
                    <input type="text" name="gst_enrolment_id" value="{{ $reportData['seller_info']['gst_enrolment_id'] }}" placeholder="उदा. 24XXXXX123456" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- State -->
                <div class="space-y-1">
                    <label class="font-bold text-slate-700">पंजीकृत राज्य (Registered State):</label>
                    <input type="text" name="state" value="{{ $reportData['seller_info']['state'] }}" placeholder="उदा. Delhi, Maharashtra, Gujarat, Uttar Pradesh" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-[10px] text-slate-400">इसी राज्य के आधार पर Intra-state (CGST+SGST) और Inter-state (IGST) तय होता है।</p>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeTaxProfileModal()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition">
                        रद्द करें (Cancel)
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black shadow-md shadow-indigo-600/30 transition">
                        💾 प्रोफाइल सुरक्षित करें
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        function openTaxProfileModal() {
            document.getElementById('taxProfileModal').classList.remove('hidden');
            document.getElementById('taxProfileModal').classList.add('flex');
        }

        function closeTaxProfileModal() {
            document.getElementById('taxProfileModal').classList.add('hidden');
            document.getElementById('taxProfileModal').classList.remove('flex');
        }
    </script>

</body>
</html>
