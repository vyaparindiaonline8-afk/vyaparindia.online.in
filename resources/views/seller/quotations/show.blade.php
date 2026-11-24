<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation #{{ $quotation->quotation_number }} - Review & Approve</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800 antialiased min-h-screen flex flex-col">

    <!-- Top Seller Nav -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="{{ route('seller.quotations.index') }}" class="flex items-center gap-2 text-gray-900 font-black text-sm">
                        <i class="fa-solid fa-arrow-left text-gray-400 hover:text-gray-700"></i>
                        <span>सभी कोटेशन पर वापस जाएं</span>
                    </a>
                    <span class="text-gray-300">|</span>
                    <h1 class="text-xs font-mono font-bold text-gray-600">
                        {{ $quotation->quotation_number }}
                    </h1>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $quotation->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($quotation->status === 'converted_to_order' ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800') }}">
                        {{ $quotation->formatted_status }}
                    </span>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full space-y-6">

        @if(session('success'))
            <div class="p-5 rounded-3xl bg-emerald-50 border-2 border-emerald-300 text-emerald-950 space-y-3 shadow-sm">
                <div class="flex items-center gap-2 font-black text-sm text-emerald-800">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
                @if(session('customer_wa_url'))
                    <div class="pt-2 flex items-center gap-3">
                        <a href="{{ session('customer_wa_url') }}" target="_blank" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md transition flex items-center gap-2">
                            <i class="fa-brands fa-whatsapp text-base"></i>
                            <span>📲 {{ session('customer_wa_name') }} को 1-Click WhatsApp पर कोटेशन भेजें</span>
                        </a>
                    </div>
                @endif
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-info-circle text-blue-600"></i>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left 2 Cols: Items & Customer Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Details Card -->
                <div class="bg-white rounded-3xl border border-gray-200 p-6 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <h2 class="font-black text-sm text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-user text-indigo-600"></i>
                            <span>ग्राहक व साइट विवरण (Customer & Site Details)</span>
                        </h2>
                        <span class="text-xs font-mono text-gray-400">{{ $quotation->created_at->format('d M, Y h:i A') }}</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase block">ग्राहक का नाम:</span>
                            <span class="font-black text-sm text-gray-900">{{ $quotation->customer_name }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase block">मोबाइल नंबर:</span>
                            <a href="tel:{{ $quotation->customer_phone }}" class="font-bold text-indigo-600 hover:underline">
                                📞 {{ $quotation->customer_phone }}
                            </a>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase block">प्रोजेक्ट / साइट:</span>
                            <span class="font-bold text-gray-800">{{ $quotation->project_name ?: 'General Supply' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase block">डिलीवरी पता:</span>
                            <span class="text-gray-600">{{ $quotation->customer_address ?: 'Not provided' }}</span>
                        </div>
                    </div>

                    @if($quotation->customer_notes)
                        <div class="p-3 rounded-xl bg-amber-50/70 border border-amber-200 text-xs">
                            <span class="font-bold text-amber-900 block mb-0.5">ग्राहक की विशेष टिप्पणी (Customer Note):</span>
                            <p class="text-amber-800">{{ $quotation->customer_notes }}</p>
                        </div>
                    @endif
                </div>

                <!-- Items Table Card -->
                <div class="bg-white rounded-3xl border border-gray-200 p-6 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <h3 class="font-black text-sm text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-boxes-stacked text-indigo-600"></i>
                            <span>माँगी गई सामग्री सूची (Quotation Items - {{ count($quotation->items ?? []) }})</span>
                        </h3>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-gray-200">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-bold uppercase text-[10px] tracking-wider">
                                    <th class="py-3 px-4">#</th>
                                    <th class="py-3 px-4">आइटम नाम</th>
                                    <th class="py-3 px-4">साइज़</th>
                                    <th class="py-3 px-4 text-center">मात्रा</th>
                                    <th class="py-3 px-4 text-right">दर (Rate ₹)</th>
                                    <th class="py-3 px-4 text-right">कुल (₹)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($quotation->items as $idx => $item)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="py-3 px-4 font-mono text-gray-400">{{ $idx + 1 }}</td>
                                        <td class="py-3 px-4 font-bold text-gray-900">{{ $item['name'] }}</td>
                                        <td class="py-3 px-4 font-mono text-gray-600">{{ $item['size'] ?? 'Standard' }}</td>
                                        <td class="py-3 px-4 text-center font-black text-gray-900">{{ $item['quantity'] }}</td>
                                        <td class="py-3 px-4 text-right font-medium text-gray-700">₹{{ number_format($item['unit_price'], 2) }}</td>
                                        <td class="py-3 px-4 text-right font-black text-gray-900">₹{{ number_format($item['line_total'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-between pt-2 text-xs">
                        <span class="text-gray-500 font-bold">कुल सबटोटल (Subtotal):</span>
                        <span class="text-base font-black text-gray-900">₹{{ number_format($quotation->subtotal, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Right Col: Store Owner Approval & Rate Adjustment Form -->
            <div class="space-y-6">
                <div class="bg-white rounded-3xl border-2 border-indigo-200 p-6 shadow-sm space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-indigo-100">
                        <h3 class="font-black text-sm text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-stamp text-indigo-600"></i>
                            <span>मालिक अप्रूवल व डिस्काउंट (Owner Action)</span>
                        </h3>
                    </div>

                    <form action="{{ route('seller.quotations.approve', $quotation->quotation_number) }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Discount Percent / Amount -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">
                                होलसेल डिस्काउंट (Discount छूट %)
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="relative">
                                    <input type="number" step="0.1" min="0" max="100" name="discount_percent" id="disc_pct" value="{{ old('discount_percent', $quotation->discount_percent) }}" oninput="recalcGrandTotal()" placeholder="0" class="w-full px-3 py-2 text-xs font-bold border border-gray-300 rounded-xl focus:border-indigo-500">
                                    <span class="absolute right-3 top-2 text-xs text-gray-400 font-bold">%</span>
                                </div>
                                <div class="relative">
                                    <input type="number" step="1" min="0" name="discount_amount" id="disc_amt" value="{{ old('discount_amount', $quotation->discount_amount) }}" oninput="recalcFromAmt()" placeholder="₹0" class="w-full px-3 py-2 text-xs font-bold border border-gray-300 rounded-xl focus:border-indigo-500">
                                    <span class="absolute right-3 top-2 text-xs text-gray-400 font-bold">₹</span>
                                </div>
                            </div>
                            <span class="text-[10px] text-gray-400 block mt-1">ठेकेदार या नियमित ग्राहक हेतु विशेष छूट दें</span>
                        </div>

                        <!-- Validity Days -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">कोटेशन की वैधता (Validity Days)</label>
                            <select name="valid_days" class="w-full px-3 py-2 text-xs font-bold border border-gray-300 rounded-xl focus:border-indigo-500 bg-white">
                                <option value="7" selected>7 दिन (1 हफ़्ता)</option>
                                <option value="15">15 दिन</option>
                                <option value="30">30 दिन (1 महीना)</option>
                            </select>
                        </div>

                        <!-- Owner Notes -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">स्टोर मालिक की टिप्पणी (Owner Notes)</label>
                            <textarea name="owner_notes" rows="2" placeholder="उदा. 100% ओरिजिनल प्लास्टो पाइप व फिटिंग्स। वाहन से माल सीधे साइट पर भेजा जाएगा।" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:border-indigo-500">{{ old('owner_notes', $quotation->owner_notes ?? 'Approved by store owner. Best wholesale rates applied.') }}</textarea>
                        </div>

                        <!-- Live Grand Total Calculation Preview -->
                        <div class="p-4 rounded-2xl bg-indigo-50/70 border border-indigo-200 space-y-1">
                            <div class="flex items-center justify-between text-xs text-indigo-950 font-bold">
                                <span>फाइनल अप्रूव्ड बिल:</span>
                                <span class="text-xl font-black text-indigo-900" id="preview_grand_total">
                                    ₹{{ number_format($quotation->grand_total, 2) }}
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-600/25 transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ $quotation->isApproved() ? 'अपडेट व री-अप्रूव करें' : '✅ कोटेशन अप्रूव करें (Approve)' }}</span>
                        </button>
                    </form>

                    @if($quotation->isApproved())
                        <div class="pt-2 border-t border-gray-100 space-y-2">
                            <div class="text-[11px] text-emerald-700 font-bold">
                                <i class="fa-solid fa-circle-check"></i> यह कोटेशन अप्रूव्ड है।
                            </div>
                            @php
                                $sellerPage = $quotation->sellerPage;
                                $viewUrl = $sellerPage ? route('minisite.showQuotation', ['sellerPage' => $sellerPage->slug, 'quotationNumber' => $quotation->quotation_number]) : url('/');
                                $custPhone = preg_replace('/[^0-9]/', '', $quotation->customer_phone);
                                if (strlen($custPhone) === 10) $custPhone = '91' . $custPhone;
                                $waShareMsg = "✅ *{$sellerPage->page_title} - आपका कोटेशन अप्रूव हो गया है!*\n\nनमस्ते {$quotation->customer_name} जी,\nआपके द्वारा माँगा गया कोटेशन (#{$quotation->quotation_number}) अप्रूव कर दिया गया है।\n💰 *फाइनल बिल:* ₹" . number_format($quotation->grand_total, 2) . "\n\n📄 *कोटेशन देखें व 1-Click में ऑर्डर करें:*\n{$viewUrl}";
                            @endphp
                            <a href="https://wa.me/{{ $custPhone }}?text={{ urlencode($waShareMsg) }}" target="_blank" class="w-full py-2.5 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-extrabold text-xs border border-emerald-300 transition flex items-center justify-center gap-2">
                                <i class="fa-brands fa-whatsapp text-sm text-emerald-600"></i>
                                <span>ग्राहक को WhatsApp भेजें</span>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Storefront Public Link -->
                @if($quotation->sellerPage)
                    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-xs space-y-2">
                        <span class="font-bold text-gray-700 block">पब्लिक कोटेशन लिंक:</span>
                        <a href="{{ route('minisite.showQuotation', ['sellerPage' => $quotation->sellerPage->slug, 'quotationNumber' => $quotation->quotation_number]) }}" target="_blank" class="text-indigo-600 hover:underline font-mono text-[11px] block truncate">
                            {{ route('minisite.showQuotation', ['sellerPage' => $quotation->sellerPage->slug, 'quotationNumber' => $quotation->quotation_number]) }}
                        </a>
                    </div>
                @endif
            </div>

        </div>

    </main>

    <script>
        const subtotal = {{ $quotation->subtotal }};

        function recalcGrandTotal() {
            const pct = parseFloat(document.getElementById('disc_pct').value) || 0;
            const amt = (subtotal * pct) / 100;
            document.getElementById('disc_amt').value = amt.toFixed(2);
            updatePreview(subtotal - amt);
        }

        function recalcFromAmt() {
            const amt = parseFloat(document.getElementById('disc_amt').value) || 0;
            const pct = subtotal > 0 ? (amt / subtotal) * 100 : 0;
            document.getElementById('disc_pct').value = pct.toFixed(1);
            updatePreview(subtotal - amt);
        }

        function updatePreview(finalTotal) {
            finalTotal = Math.max(0, finalTotal);
            document.getElementById('preview_grand_total').innerText = '₹' + finalTotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    </script>

</body>
</html>
