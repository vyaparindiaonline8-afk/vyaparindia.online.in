<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotations & Estimates Management - VyaparIndia Seller</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800 antialiased min-h-screen flex flex-col">

    <!-- Top Seller Nav -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-2 text-gray-900 font-black text-lg">
                        <i class="fa-solid fa-arrow-left text-gray-400 hover:text-gray-700 text-sm"></i>
                        <span>VyaparIndia Seller Hub</span>
                    </a>
                    <span class="text-gray-300">|</span>
                    <h1 class="text-sm font-bold text-gray-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-file-invoice text-indigo-600"></i>
                        <span>कोटेशन व एस्टीमेट प्रबंधन (Quotations)</span>
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.dashboard') }}" class="text-xs font-bold text-gray-600 hover:text-indigo-600 transition flex items-center gap-1">
                        <i class="fa-solid fa-gauge-high"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full space-y-6">

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2 shadow-xs">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Stats Overview Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">कुल कोटेशन अनुरोध</span>
                    <div class="text-2xl font-black text-gray-900 mt-0.5">{{ $totalCount }}</div>
                </div>
                <div class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-amber-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">अप्रूवल पेंडिंग (Review Pending)</span>
                    <div class="text-2xl font-black text-amber-600 mt-0.5">{{ $pendingCount }}</div>
                </div>
                <div class="h-10 w-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-emerald-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">अप्रूव्ड कोटेशन (Approved)</span>
                    <div class="text-2xl font-black text-emerald-600 mt-0.5">{{ $approvedCount }}</div>
                </div>
                <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="flex items-center gap-2 border-b border-gray-200 pb-3 flex-wrap">
            <a href="{{ route('seller.quotations.index', ['status' => 'all']) }}" class="px-4 py-1.5 rounded-xl text-xs font-bold transition {{ $status === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
                सभी कोटेशन (All)
            </a>
            <a href="{{ route('seller.quotations.index', ['status' => 'pending']) }}" class="px-4 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'pending' ? 'bg-amber-500 text-white shadow-xs' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
                <span>पेंडिंग अप्रूवल</span>
                @if($pendingCount > 0)
                    <span class="px-1.5 py-0.2 rounded-full bg-red-600 text-white text-[10px]">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('seller.quotations.index', ['status' => 'approved']) }}" class="px-4 py-1.5 rounded-xl text-xs font-bold transition {{ $status === 'approved' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
                अप्रूव्ड (Approved)
            </a>
            <a href="{{ route('seller.quotations.index', ['status' => 'converted_to_order']) }}" class="px-4 py-1.5 rounded-xl text-xs font-bold transition {{ $status === 'converted_to_order' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
                ऑर्डर में बदले गए (Ordered)
            </a>
        </div>

        <!-- Quotations Table Card -->
        <div class="bg-white rounded-3xl border border-gray-200 shadow-xs overflow-hidden">
            @if($quotations->isEmpty())
                <div class="p-12 text-center space-y-3">
                    <div class="h-16 w-16 mx-auto rounded-3xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-2xl">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <div class="font-bold text-gray-800 text-base">अभी कोई कोटेशन अनुरोध नहीं है</div>
                    <p class="text-xs text-gray-500 max-w-sm mx-auto">
                        ग्राहक या ठेकेदार जब आपकी मिनी वेबसाइट से कोटेशन का अनुरोध करेंगे, तो वे सभी यहाँ अप्रूवल हेतु दिखाई देंगे।
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 font-bold uppercase text-[10px] tracking-wider">
                                <th class="py-3.5 px-5">Quotation #</th>
                                <th class="py-3.5 px-5">ग्राहक विवरण</th>
                                <th class="py-3.5 px-5">प्रोजेक्ट / साइट</th>
                                <th class="py-3.5 px-5 text-center">आइटम्स</th>
                                <th class="py-3.5 px-5 text-right">कुल बिल</th>
                                <th class="py-3.5 px-5 text-center">स्टेटस</th>
                                <th class="py-3.5 px-5 text-right">एक्शन</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($quotations as $quotation)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="py-4 px-5">
                                        <div class="font-mono font-black text-indigo-950">{{ $quotation->quotation_number }}</div>
                                        <div class="text-[10px] text-gray-400 mt-0.5">{{ $quotation->created_at->format('d M, Y h:i A') }}</div>
                                    </td>
                                    <td class="py-4 px-5">
                                        <div class="font-bold text-gray-900">{{ $quotation->customer_name }}</div>
                                        <div class="text-[11px] text-gray-500 font-medium">📞 {{ $quotation->customer_phone }}</div>
                                    </td>
                                    <td class="py-4 px-5">
                                        <div class="font-medium text-gray-700">{{ $quotation->project_name ?: 'General Supply' }}</div>
                                    </td>
                                    <td class="py-4 px-5 text-center font-bold text-gray-900">
                                        {{ count($quotation->items ?? []) }} pcs
                                    </td>
                                    <td class="py-4 px-5 text-right font-black text-gray-900">
                                        ₹{{ number_format($quotation->grand_total, 2) }}
                                    </td>
                                    <td class="py-4 px-5 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $quotation->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($quotation->status === 'converted_to_order' ? 'bg-indigo-100 text-indigo-800' : ($quotation->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800')) }}">
                                            {{ $quotation->formatted_status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 text-right">
                                        <a href="{{ route('seller.quotations.show', $quotation->quotation_number) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition">
                                            <span>रिव्यू व अप्रूव करें</span>
                                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($quotations->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $quotations->links() }}
                    </div>
                @endif
            @endif
        </div>

    </main>

</body>
</html>
