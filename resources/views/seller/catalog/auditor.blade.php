<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Catalog Auditor & Verification - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.dashboard') }}" class="h-9 w-9 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-base shadow-sm">
                        V
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-black text-gray-900 text-base tracking-tight">VyaparIndia</span>
                            <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-200">
                                🤖 AI Catalog Auditor
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <a href="{{ route('seller.products.index') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-extrabold transition flex items-center gap-1.5 shadow-2xs">
                        <i class="fa-solid fa-boxes-stacked text-blue-600"></i>
                        <span>वापस प्रोडक्ट कैटलॉग</span>
                    </a>
                    <a href="{{ route('seller.dashboard') }}" class="text-xs font-bold text-gray-600 hover:text-blue-600 transition ml-2">
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2 shadow-xs">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2 shadow-xs">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Main Hero Header -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div class="space-y-2 max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-black uppercase tracking-wider border border-emerald-500/30">
                        <i class="fa-solid fa-robot"></i> 1-क्लिक AI कैटलॉग व रेट वेरिफिकेशन इंजन
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                        मास्टर एक्सेल शीट vs लाइव स्टोर कार्ड्स
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        आपकी मास्टर एक्सेल शीट सर्वर पर हमेशा के लिए सुरक्षित सेव है। AI एक-एक आइटम के नाम, साइज़, कोड (SKU), MRP और विक्रय भाव (Sale Rate) को लाइव कार्ड्स से मिलाकर अंतर और मिसिंग प्रोडक्ट्स तुरंत पकड़ लेता है।
                    </p>

                    @if($hasMaster && $masterMeta)
                        <div class="flex flex-wrap items-center gap-3 pt-2 text-xs text-slate-300">
                            <span class="bg-white/10 px-3 py-1 rounded-lg border border-white/10 flex items-center gap-1.5">
                                <i class="fa-solid fa-file-excel text-emerald-400"></i>
                                <span>फ़ाइल: <strong>{{ $masterMeta['filename'] }}</strong></span>
                            </span>
                            <span class="bg-white/10 px-3 py-1 rounded-lg border border-white/10 flex items-center gap-1.5">
                                <i class="fa-solid fa-list-ol text-blue-400"></i>
                                <span>कुल पंक्तियाँ: <strong>{{ $masterMeta['row_count'] }}</strong></span>
                            </span>
                            @if(!empty($masterMeta['updated_at']))
                                <span class="bg-white/10 px-3 py-1 rounded-lg border border-white/10 flex items-center gap-1.5">
                                    <i class="fa-solid fa-clock text-amber-400"></i>
                                    <span>लास्ट सेव: <strong>{{ \Carbon\Carbon::parse($masterMeta['updated_at'])->diffForHumans() }}</strong></span>
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                    <button type="button" onclick="openMasterUploadModal()" class="px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-extrabold text-xs transition flex items-center justify-center gap-2 backdrop-blur-xs shadow-md hover:scale-102 active:scale-98">
                        <i class="fa-solid fa-cloud-arrow-up text-amber-400 text-sm"></i>
                        <span>नई एक्सेल शीट अपलोड / बदलें</span>
                    </button>

                    @if($audit && $audit['mismatch_count'] > 0)
                        <form action="{{ route('seller.catalog.auditor.sync_mismatches') }}" method="POST" onsubmit="const btn=this.querySelector('button'); btn.disabled=true; btn.innerHTML='<i class=\'fa-solid fa-spinner fa-spin\'></i> सिंक हो रहा है...';">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-black text-xs shadow-lg shadow-emerald-500/30 transition flex items-center justify-center gap-2 hover:scale-102 active:scale-98">
                                <i class="fa-solid fa-bolt text-sm"></i>
                                <span>1-क्लिक में सभी {{ $audit['mismatch_count'] }} रेट्स सिंक करें ⚡</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        @if(!$hasMaster)
            <!-- Empty State: No Master Sheet Uploaded Yet -->
            <div class="bg-white rounded-3xl p-10 border border-gray-200 text-center space-y-4 shadow-sm max-w-2xl mx-auto">
                <div class="h-16 w-16 rounded-3xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-3xl mx-auto">
                    <i class="fa-solid fa-file-excel"></i>
                </div>
                <div class="space-y-1">
                    <h2 class="text-lg font-black text-gray-900">आपकी मास्टर एक्सेल शीट अभी सेव नहीं है</h2>
                    <p class="text-xs text-gray-500 max-w-md mx-auto">
                        अपनी मुख्य रेट लिस्ट (Plasto Final List) यहाँ अपलोड करें ताकि AI उसे हमेशा याद रखे और आपके कार्ड्स के रेट्स को तुरंत मिला सके।
                    </p>
                </div>
                <button type="button" onclick="openMasterUploadModal()" class="px-6 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs shadow-md transition inline-flex items-center gap-2">
                    <i class="fa-solid fa-upload"></i>
                    <span>मास्टर एक्सेल शीट अपलोड करें</span>
                </button>
            </div>
        @else
            <!-- 📊 KPI Comparison Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-3.5">
                <!-- Stat 1 -->
                <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-2xs">
                    <span class="text-[11px] font-bold text-gray-500 flex items-center gap-1.5">
                        <i class="fa-solid fa-file-excel text-blue-600"></i>
                        <span>एक्सेल शीट पंक्तियाँ</span>
                    </span>
                    <div class="text-2xl font-black text-gray-900 mt-1">
                        {{ $audit['total_excel_rows'] }} <span class="text-xs font-semibold text-gray-400">Rows</span>
                    </div>
                </div>

                <!-- Stat 2 -->
                <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-2xs">
                    <span class="text-[11px] font-bold text-gray-500 flex items-center gap-1.5">
                        <i class="fa-solid fa-layer-group text-purple-600"></i>
                        <span>स्टोर प्रोडक्ट कार्ड्स</span>
                    </span>
                    <div class="text-2xl font-black text-gray-900 mt-1">
                        {{ $audit['total_live_cards'] }} <span class="text-xs font-semibold text-gray-400">Cards</span>
                    </div>
                </div>

                <!-- Stat 3 -->
                <div class="bg-white rounded-2xl p-4 border border-emerald-200 shadow-2xs bg-emerald-50/30">
                    <span class="text-[11px] font-bold text-emerald-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>100% मैच रेट्स</span>
                    </span>
                    <div class="text-2xl font-black text-emerald-600 mt-1">
                        {{ $audit['matched_count'] }} <span class="text-xs font-semibold text-emerald-600/70">Items</span>
                    </div>
                </div>

                <!-- Stat 4 -->
                <div class="bg-white rounded-2xl p-4 border {{ $audit['mismatch_count'] > 0 ? 'border-amber-300 bg-amber-50/40' : 'border-gray-200' }} shadow-2xs">
                    <span class="text-[11px] font-bold {{ $audit['mismatch_count'] > 0 ? 'text-amber-800' : 'text-gray-500' }} flex items-center gap-1.5">
                        <i class="fa-solid fa-triangle-exclamation {{ $audit['mismatch_count'] > 0 ? 'text-amber-600' : 'text-gray-400' }}"></i>
                        <span>रेट में अंतर (Mismatches)</span>
                    </span>
                    <div class="text-2xl font-black {{ $audit['mismatch_count'] > 0 ? 'text-amber-700' : 'text-gray-900' }} mt-1">
                        {{ $audit['mismatch_count'] }} <span class="text-xs font-semibold text-gray-400">Items</span>
                    </div>
                </div>

                <!-- Stat 5 -->
                <div class="bg-white rounded-2xl p-4 border {{ $audit['missing_count'] > 0 ? 'border-rose-300 bg-rose-50/40' : 'border-gray-200' }} shadow-2xs col-span-2 lg:col-span-1">
                    <span class="text-[11px] font-bold {{ $audit['missing_count'] > 0 ? 'text-rose-700' : 'text-gray-500' }} flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-xmark {{ $audit['missing_count'] > 0 ? 'text-rose-600' : 'text-gray-400' }}"></i>
                        <span>मिसिंग कार्ड्स (Uncreated)</span>
                    </span>
                    <div class="text-2xl font-black {{ $audit['missing_count'] > 0 ? 'text-rose-700' : 'text-gray-900' }} mt-1">
                        {{ $audit['missing_count'] }} <span class="text-xs font-semibold text-gray-400">Items</span>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation & Filters -->
            <div class="bg-white rounded-3xl border border-gray-200 shadow-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                        <button type="button" onclick="switchAuditTab('mismatches')" id="tabBtnMismatches" class="px-4 py-2 rounded-xl text-xs font-extrabold transition flex items-center gap-1.5 {{ $audit['mismatch_count'] > 0 ? 'bg-amber-500 text-white shadow-xs' : 'bg-gray-100 text-gray-700' }}">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>रेट में अंतर ({{ $audit['mismatch_count'] }})</span>
                        </button>
                        <button type="button" onclick="switchAuditTab('matched')" id="tabBtnMatched" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $audit['mismatch_count'] == 0 ? 'bg-indigo-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            <i class="fa-solid fa-circle-check text-emerald-500"></i>
                            <span>100% सही मैच ({{ $audit['matched_count'] }})</span>
                        </button>
                        <button type="button" onclick="switchAuditTab('missing')" id="tabBtnMissing" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200">
                            <i class="fa-solid fa-circle-question text-rose-500"></i>
                            <span>मिसिंग प्रोडक्ट्स ({{ $audit['missing_count'] }})</span>
                        </button>
                    </div>

                    <div class="relative w-full sm:w-72">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="auditSearchInput" oninput="filterAuditRows(this.value)" placeholder="आइटम नाम या कोड खोजें (MTA, FTA, 25MM...)" class="w-full pl-8 pr-3 py-2 rounded-xl border border-gray-200 text-xs bg-gray-50 focus:bg-white focus:border-indigo-500 focus:outline-hidden">
                    </div>
                </div>

                <!-- Tab 1: Rate Mismatches -->
                <div id="tabContentMismatches" class="p-0 overflow-x-auto {{ $audit['mismatch_count'] == 0 ? 'hidden' : '' }}">
                    @if($audit['mismatch_count'] == 0)
                        <div class="p-10 text-center space-y-2">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-4xl"></i>
                            <h3 class="text-sm font-black text-gray-900">शानदार! कोई रेट मिसमैच नहीं है।</h3>
                            <p class="text-xs text-gray-500">आपकी एक्सेल शीट और लाइव स्टोर कार्ड्स के सभी भाव 100% एक जैसे हैं।</p>
                        </div>
                    @else
                        <table class="w-full text-left text-xs text-gray-600">
                            <thead class="bg-amber-50/60 text-amber-950 font-black uppercase text-[10px] tracking-wider border-b border-amber-200">
                                <tr>
                                    <th class="p-3.5">SKU / कोड</th>
                                    <th class="p-3.5">एक्सेल प्रोडक्ट नाम</th>
                                    <th class="p-3.5">स्टोर कार्ड व साइज़</th>
                                    <th class="p-3.5 text-right">एक्सेल MRP</th>
                                    <th class="p-3.5 text-right">स्टोर MRP</th>
                                    <th class="p-3.5 text-right">एक्सेल भाव (Sale A)</th>
                                    <th class="p-3.5 text-right">स्टोर लाइव भाव</th>
                                    <th class="p-3.5 text-center">अंतर (Diff)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100" id="mismatchesTableBody">
                                @foreach($audit['rate_mismatches'] as $item)
                                    <tr class="hover:bg-amber-50/30 transition audit-row" data-search="{{ strtolower($item['code'] . ' ' . $item['excel_name'] . ' ' . $item['db_name'] . ' ' . $item['db_variant']) }}">
                                        <td class="p-3.5 font-mono font-bold text-gray-900">{{ $item['code'] ?: '—' }}</td>
                                        <td class="p-3.5 font-bold text-gray-900">{{ $item['excel_name'] }}</td>
                                        <td class="p-3.5">
                                            <span class="font-bold text-indigo-900">{{ $item['db_name'] }}</span>
                                            <span class="ml-1.5 px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 font-mono text-[10px] font-bold border border-indigo-200">
                                                {{ $item['db_variant'] }}
                                            </span>
                                        </td>
                                        <td class="p-3.5 text-right font-mono font-bold text-gray-700">₹{{ number_format($item['excel_mrp'], 2) }}</td>
                                        <td class="p-3.5 text-right font-mono text-gray-500">₹{{ number_format($item['db_mrp'], 2) }}</td>
                                        <td class="p-3.5 text-right font-mono font-black text-amber-600">₹{{ number_format($item['excel_price'], 2) }}</td>
                                        <td class="p-3.5 text-right font-mono font-bold text-gray-600">₹{{ number_format($item['db_price'], 2) }}</td>
                                        <td class="p-3.5 text-center">
                                            @php $diff = $item['price_diff']; @endphp
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $diff > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                {{ $diff > 0 ? '+' : '' }}₹{{ number_format($diff, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <!-- Tab 2: Matched Items -->
                <div id="tabContentMatched" class="p-0 overflow-x-auto {{ $audit['mismatch_count'] > 0 ? 'hidden' : '' }}">
                    <table class="w-full text-left text-xs text-gray-600">
                        <thead class="bg-gray-50 text-gray-700 font-black uppercase text-[10px] tracking-wider border-b border-gray-100">
                            <tr>
                                <th class="p-3.5">SKU / कोड</th>
                                <th class="p-3.5">प्रोडक्ट नाम</th>
                                <th class="p-3.5">साइज़ वेरिएंट</th>
                                <th class="p-3.5 text-right">MRP</th>
                                <th class="p-3.5 text-right">विक्रय भाव (Sale Rate)</th>
                                <th class="p-3.5 text-center">स्थिति (Status)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="matchedTableBody">
                            @foreach($audit['matched'] as $item)
                                <tr class="hover:bg-gray-50/50 transition audit-row" data-search="{{ strtolower($item['code'] . ' ' . $item['excel_name'] . ' ' . $item['db_name'] . ' ' . $item['db_variant']) }}">
                                    <td class="p-3.5 font-mono font-bold text-gray-900">{{ $item['code'] ?: '—' }}</td>
                                    <td class="p-3.5 font-bold text-gray-900">{{ $item['excel_name'] }}</td>
                                    <td class="p-3.5">
                                        <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 font-mono text-[10px] font-bold border border-emerald-200">
                                            {{ $item['db_variant'] }}
                                        </span>
                                    </td>
                                    <td class="p-3.5 text-right font-mono font-bold text-gray-700">₹{{ number_format($item['excel_mrp'] ?: $item['db_mrp'], 2) }}</td>
                                    <td class="p-3.5 text-right font-mono font-black text-emerald-600">₹{{ number_format($item['excel_price'] ?: $item['db_price'], 2) }}</td>
                                    <td class="p-3.5 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px] inline-flex items-center gap-1">
                                            <i class="fa-solid fa-check"></i> 100% मैच
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Tab 3: Missing Items -->
                <div id="tabContentMissing" class="p-0 overflow-x-auto hidden">
                    @if($audit['missing_count'] == 0)
                        <div class="p-10 text-center space-y-2">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-4xl"></i>
                            <h3 class="text-sm font-black text-gray-900">कोई मिसिंग प्रोडक्ट नहीं है!</h3>
                            <p class="text-xs text-gray-500">एक्सेल शीट की प्रत्येक पंक्ति का कार्ड या साइज़ वेरिएंट लाइव डेटाबेस में मौजूद है।</p>
                        </div>
                    @else
                        <table class="w-full text-left text-xs text-gray-600">
                            <thead class="bg-rose-50/60 text-rose-950 font-black uppercase text-[10px] tracking-wider border-b border-rose-200">
                                <tr>
                                    <th class="p-3.5">SKU / कोड</th>
                                    <th class="p-3.5">एक्सेल प्रोडक्ट नाम</th>
                                    <th class="p-3.5 text-right">MRP</th>
                                    <th class="p-3.5 text-right">विक्रय भाव</th>
                                    <th class="p-3.5 text-center">सुझाव</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($audit['missing'] as $item)
                                    <tr class="hover:bg-rose-50/30 transition audit-row" data-search="{{ strtolower($item['code'] . ' ' . $item['excel_name']) }}">
                                        <td class="p-3.5 font-mono font-bold text-gray-900">{{ $item['code'] ?: '—' }}</td>
                                        <td class="p-3.5 font-bold text-gray-900">{{ $item['excel_name'] }}</td>
                                        <td class="p-3.5 text-right font-mono font-bold text-gray-700">₹{{ number_format($item['excel_mrp'], 2) }}</td>
                                        <td class="p-3.5 text-right font-mono font-black text-rose-600">₹{{ number_format($item['excel_price'], 2) }}</td>
                                        <td class="p-3.5 text-center">
                                            <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold text-[10px]">
                                                कार्ड बनाएं (Add Card)
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

            </div>
        @endif

    </div>

    <!-- 📤 Upload / Replace Master Sheet Modal -->
    <div id="masterUploadModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-4" onclick="closeMasterUploadModal()">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-4 animate-in fade-in zoom-in duration-200" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-file-excel"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">मास्टर एक्सेल शीट अपलोड करें</h3>
                        <p class="text-[11px] text-gray-500">Upload & Save Master Sheet for AI Verification</p>
                    </div>
                </div>
                <button type="button" onclick="closeMasterUploadModal()" class="h-8 w-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form action="{{ route('seller.catalog.auditor.upload') }}" method="POST" enctype="multipart/form-data" id="masterUploadForm" onsubmit="const btn=this.querySelector('button[type=submit]'); btn.disabled=true; btn.innerHTML='<i class=\'fa-solid fa-spinner fa-spin\'></i> फ़ाइल प्रोसेस हो रही है...';">
                @csrf
                <input type="hidden" name="spreadsheet_rows" id="modalSpreadsheetRows">
                <input type="hidden" name="spreadsheet_filename" id="modalSpreadsheetFilename">

                <div class="space-y-3">
                    <label class="border-2 border-dashed border-gray-300 hover:border-indigo-500 rounded-2xl p-6 flex flex-col items-center justify-center cursor-pointer transition bg-gray-50 hover:bg-indigo-50/30 group">
                        <input type="file" id="masterExcelFileInput" accept=".xlsx, .xls, .csv" class="hidden" onchange="handleModalExcelFile(event)">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 group-hover:text-indigo-600 transition mb-2"></i>
                        <span class="text-xs font-bold text-gray-700 text-center" id="modalFileNameLabel">
                            यहाँ अपनी एक्सेल फ़ाइल (.xlsx / .csv) चुनें या ड्रैग करें
                        </span>
                        <span class="text-[10px] text-gray-400 mt-1">Excel (XLSX, XLS) or CSV up to 15MB</span>
                    </label>

                    <div id="modalRowPreviewBadge" class="hidden items-center justify-between p-3 rounded-xl bg-indigo-50 border border-indigo-200 text-xs font-bold text-indigo-900">
                        <span id="modalRowCountText">0 पंक्तियाँ पहचानी गईं</span>
                        <span class="text-emerald-600 flex items-center gap-1"><i class="fa-solid fa-circle-check"></i> तैयार है</span>
                    </div>

                    <p class="text-[11px] text-gray-500 leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-100">
                        💡 <strong>नोट:</strong> यह फ़ाइल सर्वर पर आपके खाते में सुरक्षित सेव रहेगी। इसके बाद आप कभी भी 1-क्लिक में अपने सभी प्रोडक्ट कार्ड्स के रेट्स को इससे मिलाकर वेरिफाई और ऑटो-सिंक कर सकेंगे।
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-gray-100 mt-4">
                    <button type="button" onclick="closeMasterUploadModal()" class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition">
                        रद्द करें
                    </button>
                    <button type="submit" id="btnSubmitMasterUpload" disabled class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-black shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-check"></i>
                        <span>मास्टर शीट सेव करें व ऑडिट चलाएं</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openMasterUploadModal() {
            document.getElementById('masterUploadModal').classList.remove('hidden');
            document.getElementById('masterUploadModal').classList.add('flex');
        }

        function closeMasterUploadModal() {
            document.getElementById('masterUploadModal').classList.add('hidden');
            document.getElementById('masterUploadModal').classList.remove('flex');
        }

        function handleModalExcelFile(event) {
            const file = event.target.files[0];
            if (!file) return;

            document.getElementById('modalFileNameLabel').textContent = file.name;
            document.getElementById('modalSpreadsheetFilename').value = file.name;

            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const sheet = workbook.Sheets[workbook.SheetNames[0]];
                    const jsonRows = XLSX.utils.sheet_to_json(sheet, { defval: '' });

                    if (jsonRows.length === 0) {
                        alert('शीट खाली है या कोई पंक्ति नहीं मिली!');
                        return;
                    }

                    document.getElementById('modalSpreadsheetRows').value = JSON.stringify(jsonRows);
                    document.getElementById('modalRowCountText').textContent = `${jsonRows.length} पंक्तियाँ पहचानी गईं`;
                    document.getElementById('modalRowPreviewBadge').classList.remove('hidden');
                    document.getElementById('modalRowPreviewBadge').classList.add('flex');
                    document.getElementById('btnSubmitMasterUpload').disabled = false;
                } catch (err) {
                    console.error(err);
                    alert('एक्सेल फ़ाइल पढ़ने में त्रुटि हुई। कृपया फाइल चेक करें।');
                }
            };
            reader.readAsArrayBuffer(file);
        }

        function switchAuditTab(tab) {
            const tabs = ['mismatches', 'matched', 'missing'];
            tabs.forEach(t => {
                const btn = document.getElementById(`tabBtn${t.charAt(0).toUpperCase() + t.slice(1)}`);
                const content = document.getElementById(`tabContent${t.charAt(0).toUpperCase() + t.slice(1)}`);
                if (t === tab) {
                    btn.classList.add('bg-indigo-600', 'text-white', 'shadow-xs');
                    btn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    content.classList.remove('hidden');
                } else {
                    btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-xs', 'bg-amber-500');
                    btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    content.classList.add('hidden');
                }
            });
        }

        function filterAuditRows(query) {
            const q = query.toLowerCase().trim();
            const rows = document.querySelectorAll('.audit-row');
            rows.forEach(r => {
                const searchData = r.getAttribute('data-search') || '';
                if (!q || searchData.includes(q)) {
                    r.classList.remove('hidden');
                } else {
                    r.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html>
