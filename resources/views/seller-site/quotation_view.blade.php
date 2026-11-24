@extends('seller-site.layout')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    
    <!-- Top Nav / Back -->
    <div class="flex items-center justify-between mb-6 print:hidden">
        <a href="{{ route('minisite.show', $sellerPage->slug) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-gray-900 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>{{ $sellerPage->page_title }} स्टोर पर वापस जाएं</span>
        </a>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-3.5 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                <i class="fa-solid fa-print text-indigo-600"></i>
                <span>प्रिंट / PDF सेव करें</span>
            </button>
            @php
                $shareMsg = urlencode("Namaste! Check out Official Quotation #{$quotation->quotation_number} from {$sellerPage->page_title}:\n" . url()->current());
            @endphp
            <a href="https://api.whatsapp.com/send?text={{ $shareMsg }}" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold transition flex items-center gap-1.5 border border-emerald-200 shadow-2xs">
                <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                <span>WhatsApp पर शेयर</span>
            </a>
        </div>
    </div>

    <!-- Official Quotation Document Box -->
    <div class="bg-white rounded-3xl border border-gray-200 shadow-xl overflow-hidden print:border-none print:shadow-none">
        
        <!-- Header Banner -->
        <div class="p-6 sm:p-8 bg-slate-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-slate-800">
            <div class="flex items-center gap-4">
                @if($sellerPage->logo_url)
                    <img src="{{ $sellerPage->logo_url }}" alt="{{ $sellerPage->page_title }}" class="h-16 w-16 object-contain rounded-2xl bg-white p-1 shadow-sm">
                @else
                    <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white font-black text-2xl flex items-center justify-center shadow-sm">
                        {{ strtoupper(substr($sellerPage->page_title, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight leading-tight">{{ $sellerPage->page_title }}</h1>
                    @if($sellerPage->dealership_text)
                        <div class="text-xs text-amber-300 font-bold mt-0.5 flex items-center gap-1">
                            <i class="fa-solid fa-certificate"></i>
                            <span>{{ $sellerPage->dealership_text }}</span>
                        </div>
                    @endif
                    <div class="text-[11px] text-slate-300 mt-1">
                        {{ $sellerPage->address ? $sellerPage->address . ', ' : '' }}{{ $sellerPage->city }}{{ $sellerPage->pincode ? ' - ' . $sellerPage->pincode : '' }}
                    </div>
                    @if($sellerPage->phone)
                        <div class="text-[11px] text-slate-400 mt-0.5">
                            📞 {{ $sellerPage->phone }} @if($sellerPage->whatsapp_number) | 📲 WhatsApp: {{ $sellerPage->whatsapp_number }} @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quotation Badge & Meta -->
            <div class="text-left sm:text-right space-y-1">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider shadow-2xs {{ $quotation->status === 'approved' ? 'bg-emerald-500 text-white' : ($quotation->status === 'converted_to_order' ? 'bg-indigo-600 text-white' : 'bg-amber-400 text-slate-900') }}">
                    {{ $quotation->formatted_status }}
                </span>
                <div class="font-mono text-xs font-black text-slate-200 mt-1">#{{ $quotation->quotation_number }}</div>
                <div class="text-[11px] text-slate-400">तारीख: {{ $quotation->created_at->format('d M, Y') }}</div>
                @if($quotation->valid_until)
                    <div class="text-[11px] text-amber-300 font-bold">वैधता: {{ $quotation->valid_until->format('d M, Y') }} तक</div>
                @endif
            </div>
        </div>

        <!-- Customer & Project Info Row -->
        <div class="p-6 sm:p-8 bg-slate-50/70 border-b border-gray-200 grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block mb-1">ग्राहक विवरण (Quotation For):</span>
                <div class="font-black text-sm text-gray-900">{{ $quotation->customer_name }}</div>
                <div class="text-xs text-gray-600 font-bold mt-0.5">📞 {{ $quotation->customer_phone }}</div>
                @if($quotation->customer_address)
                    <div class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $quotation->customer_address }}</div>
                @endif
            </div>

            <div class="sm:text-right">
                <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block mb-1">प्रोजेक्ट / साइट विवरण:</span>
                <div class="font-bold text-sm text-indigo-950">{{ $quotation->project_name ?: 'General Material Requirement' }}</div>
                @if($quotation->approved_at)
                    <div class="text-xs text-emerald-700 font-bold mt-1 flex items-center sm:justify-end gap-1">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Approved on {{ $quotation->approved_at->format('d M, Y h:i A') }}</span>
                    </div>
                @else
                    <div class="text-xs text-amber-700 font-bold mt-1 flex items-center sm:justify-end gap-1">
                        <i class="fa-solid fa-clock"></i>
                        <span>दुकान मालिक द्वारा रेट वेरिफिकेशन पेंडिंग है</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <div class="p-6 sm:p-8 space-y-6">
            <div class="overflow-x-auto rounded-2xl border border-gray-200">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 font-bold border-b border-gray-200 uppercase text-[10px] tracking-wider">
                            <th class="py-3 px-4">#</th>
                            <th class="py-3 px-4">सामग्री / विवरण (Item Description)</th>
                            <th class="py-3 px-4">साइज़ / क्लास</th>
                            <th class="py-3 px-4 text-center">मात्रा (Qty)</th>
                            <th class="py-3 px-4 text-right">दर (Rate ₹)</th>
                            <th class="py-3 px-4 text-right">कुल राशि (Amount ₹)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($quotation->items as $idx => $item)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-3 px-4 font-mono text-gray-400">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4 font-bold text-gray-900">
                                    <div class="flex items-center gap-2">
                                        @if(!empty($item['image']))
                                            <img src="{{ $item['image'] }}" class="h-7 w-7 object-contain rounded bg-white border border-gray-200 shrink-0">
                                        @endif
                                        <span>{{ $item['name'] }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-mono text-gray-600">{{ $item['size'] ?? 'Standard' }}</td>
                                <td class="py-3 px-4 text-center font-black text-gray-900">{{ $item['quantity'] }}</td>
                                <td class="py-3 px-4 text-right font-semibold text-gray-700">₹{{ number_format($item['unit_price'], 2) }}</td>
                                <td class="py-3 px-4 text-right font-black text-gray-900">₹{{ number_format($item['line_total'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals & Notes Summary Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                <!-- Notes / Terms -->
                <div class="space-y-3">
                    @if($quotation->owner_notes)
                        <div class="bg-blue-50/70 border border-blue-200 rounded-2xl p-4 text-xs">
                            <span class="font-black text-blue-950 block mb-1">
                                <i class="fa-solid fa-clipboard-check text-blue-600 mr-1"></i> स्टोर मालिक के निर्देश / शर्तें:
                            </span>
                            <p class="text-blue-900 leading-relaxed">{{ $quotation->owner_notes }}</p>
                        </div>
                    @endif

                    <div class="bg-gray-50 rounded-2xl p-4 text-[11px] text-gray-500 space-y-1">
                        <div class="font-bold text-gray-700">नियम व शर्तें (Terms):</div>
                        <div>• यह कोटेशन उल्लिखित वैधता अवधि तक ही मान्य है।</div>
                        <div>• वास्तविक डिलीवरी चार्ज व वाहन किराया दूरी के अनुसार रहेगा।</div>
                        <div>• 100% ओरिजिनल व ब्रांडेड मटेरियल की पूर्ण गारंटी।</div>
                    </div>
                </div>

                <!-- Financial Calculation Card -->
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 space-y-2.5">
                    <div class="flex items-center justify-between text-xs text-gray-600">
                        <span>कुल सबटोटल (Subtotal):</span>
                        <span class="font-bold text-gray-900">₹{{ number_format($quotation->subtotal, 2) }}</span>
                    </div>

                    @if($quotation->discount_amount > 0)
                        <div class="flex items-center justify-between text-xs text-emerald-700">
                            <span>विशेष होलसेल डिस्काउंट (Discount):</span>
                            <span class="font-bold">-₹{{ number_format($quotation->discount_amount, 2) }}</span>
                        </div>
                    @endif

                    @if($quotation->tax_amount > 0)
                        <div class="flex items-center justify-between text-xs text-gray-600">
                            <span>GST / टैक्स (Taxes):</span>
                            <span class="font-bold text-gray-900">+₹{{ number_format($quotation->tax_amount, 2) }}</span>
                        </div>
                    @endif

                    <div class="border-t border-slate-300 pt-2 flex items-center justify-between">
                        <span class="text-sm font-black text-slate-900">फाइनल अप्रूव्ड बिल (Grand Total):</span>
                        <span class="text-2xl font-black text-emerald-600">₹{{ number_format($quotation->grand_total, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Customer Conversion Actions -->
            <div class="pt-6 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
                <div class="text-xs text-gray-500">
                    @if($quotation->isApproved())
                        <span class="text-emerald-700 font-bold flex items-center gap-1">
                            <i class="fa-solid fa-circle-check"></i> कोटेशन अप्रूव हो चुका है। आप इसे 1-क्लिक में कन्फर्म ऑर्डर में बदल सकते हैं।
                        </span>
                    @else
                        <span class="text-amber-700 font-bold flex items-center gap-1">
                            <i class="fa-solid fa-hourglass-half"></i> स्टोर मालिक द्वारा रिव्यू किया जा रहा है। फाइनल अप्रूवल के बाद ऑर्डर करें।
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    @if($sellerPage->whatsapp_number)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sellerPage->whatsapp_number) }}?text={{ urlencode("Namaste Ganesh Traders! I want to confirm Quotation #{$quotation->quotation_number} (Total ₹" . number_format($quotation->grand_total, 2) . ")") }}" target="_blank" class="px-5 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md transition flex items-center gap-2">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                            <span>WhatsApp पर बात करें</span>
                        </a>
                    @endif

                    @if($quotation->status === 'approved')
                        <form action="{{ route('minisite.convertQuotationToOrder', ['sellerPage' => $sellerPage->slug, 'quotationNumber' => $quotation->quotation_number]) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-lg shadow-indigo-600/25 transition flex items-center gap-2">
                                <i class="fa-solid fa-bag-shopping"></i>
                                <span>🛒 1-Click में ऑर्डर कन्फर्म करें</span>
                            </button>
                        </form>
                    @elseif($quotation->status === 'converted_to_order')
                        <span class="px-5 py-2.5 rounded-2xl bg-emerald-100 text-emerald-900 font-black text-xs flex items-center gap-1.5">
                            <i class="fa-solid fa-check"></i> ऑर्डर कन्फर्म हो चुका है!
                        </span>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
