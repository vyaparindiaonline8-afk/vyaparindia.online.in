@extends('seller-site.layout')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-8">
    
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs text-gray-500 font-medium">
        <a href="{{ route('minisite.show', $sellerPage->slug) }}" class="hover:text-gray-900">Home</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
        <span class="text-gray-900 font-bold">AI Material Slip & Parchi Scanner</span>
    </nav>

    <!-- Header Hero -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-950 rounded-3xl p-6 sm:p-10 text-white shadow-xl relative overflow-hidden">
        <div class="max-w-2xl relative z-10 space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-xs font-bold backdrop-blur-xs">
                <i class="fa-solid fa-camera text-amber-400"></i>
                <span>Plumber & Contractor Smart Assistant</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-black tracking-tight leading-tight">
                Hath Se Likhi Parchi Ya List Ko AI Se Instant Quote Me Badlo
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                Plumber, electrician ya thekedaar ki parchi ki photo upload karein ya text paste karein. Hamara AI turant saare items scan karke Excel quotation banayega aur is store ke stock aur pricing se match kar dega!
            </p>
        </div>
        <div class="absolute -right-6 -bottom-6 opacity-10 text-white pointer-events-none text-9xl">
            <i class="fa-solid fa-file-invoice-dollar"></i>
        </div>
    </div>

    <!-- Scanner Input Form -->
    <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-xs space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div>
                <h2 class="text-base font-extrabold text-gray-900">Upload Slip Photo OR Paste Material List</h2>
                <p class="text-xs text-gray-500">Hardware, pipes, electrical, sanitary, garments material list</p>
            </div>
            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">
                AI Auto-Extraction
            </span>
        </div>

        <form action="{{ route('minisite.processMaterialSlip', $sellerPage->slug) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Method 1: Photo Upload -->
                <div class="border-2 border-dashed border-gray-300 hover:border-indigo-500 rounded-2xl p-6 text-center transition bg-gray-50/50 flex flex-col justify-center items-center space-y-2">
                    <input type="file" name="slip_image" id="slip_image" accept="image/*,application/pdf" class="hidden" onchange="document.getElementById('imgLabel').innerText = this.files[0] ? this.files[0].name : 'Parchi ki photo chuni gayi'">
                    <button type="button" onclick="document.getElementById('slip_image').click()" class="h-12 w-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl hover:scale-105 transition">
                        <i class="fa-solid fa-camera"></i>
                    </button>
                    <div>
                        <span class="text-xs font-bold text-gray-800 block" id="imgLabel">Photo / Parchi Upload Karein</span>
                        <span class="text-[11px] text-gray-400">Mobile camera photo ya PDF supported</span>
                    </div>
                </div>

                <!-- Method 2: Typed / Pasted Text -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 block">
                        Ya Text Paste Karein:
                    </label>
                    <textarea name="slip_text" rows="4" placeholder="e.g.&#10;10 CPVC Pipe 1 inch&#10;5 Elbow 1 inch&#10;2 Ball Valve 1 inch&#10;1 Solvent Cement 100ml" class="w-full px-3.5 py-2.5 rounded-2xl border border-gray-300 text-xs font-medium text-gray-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder:text-gray-400">{{ $rawText ?? '' }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <div class="text-[11px] text-gray-400 flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-check text-emerald-500"></i>
                    <span>AI auto-detects quantity (pcs, nos, mtr) and pipe sizes (1/2", 1", 25mm)</span>
                </div>

                <button type="submit" class="px-8 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-lg shadow-indigo-600/20 flex items-center gap-2 transition">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <span>Scan & Match Estimate</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Parsed Results & Quotation Table -->
    @if(isset($parsedData))
        <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-xs space-y-6">
            
            <!-- Result Stats -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-100 gap-4">
                <div>
                    <h3 class="text-lg font-black text-gray-900">Extracted Material Estimation</h3>
                    <p class="text-xs text-gray-500">
                        {{ $parsedData['matched_in_store'] }} of {{ $parsedData['total_items_detected'] }} items directly matched with {{ $sellerPage->page_title }} inventory
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <form action="{{ route('minisite.downloadQuotationExcel', $sellerPage->slug) }}" method="GET" class="inline">
                        <input type="hidden" name="raw_text" value="{{ $rawText }}">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold transition flex items-center gap-2 border border-emerald-200">
                            <i class="fa-solid fa-file-excel text-emerald-600"></i>
                            <span>Download Excel Sheet</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Total Amount Card -->
            <div class="bg-indigo-50/60 border border-indigo-100 rounded-2xl p-4 flex items-center justify-between">
                <span class="text-xs font-bold text-indigo-950 uppercase tracking-wider">
                    Total Estimated Quotation:
                </span>
                <span class="text-2xl font-black text-indigo-900">
                    ₹{{ number_format($parsedData['total_estimated_amount'], 2) }}
                </span>
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto rounded-2xl border border-gray-200">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 font-bold border-b border-gray-200">
                            <th class="py-3 px-4">#</th>
                            <th class="py-3 px-4">Parchi Item</th>
                            <th class="py-3 px-4">Detected Size</th>
                            <th class="py-3 px-4 text-center">Qty</th>
                            <th class="py-3 px-4">Store Matched Product</th>
                            <th class="py-3 px-4">Unit Rate</th>
                            <th class="py-3 px-4">Line Total</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($parsedData['items'] as $idx => $item)
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="py-3 px-4 font-mono text-gray-400">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4 font-bold text-gray-900">{{ $item['item_name'] }}</td>
                                <td class="py-3 px-4 font-mono text-gray-600">{{ $item['detected_size'] ?? 'Standard' }}</td>
                                <td class="py-3 px-4 text-center font-bold text-gray-900">{{ $item['quantity'] }}</td>
                                <td class="py-3 px-4">
                                    @if($item['is_matched'])
                                        <span class="font-bold text-indigo-950">{{ $item['matched_product_name'] }}</span>
                                    @else
                                        <span class="text-gray-400 italic">Market Estimate (Custom Sourced)</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-semibold text-gray-800">₹{{ number_format($item['unit_price'], 2) }}</td>
                                <td class="py-3 px-4 font-black text-gray-900">₹{{ number_format($item['line_total'], 2) }}</td>
                                <td class="py-3 px-4 text-center">
                                    @if($item['is_matched'])
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">
                                            ✓ In Stock
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px]">
                                            RFQ Quote
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Bottom Action CTAs -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
                <div class="text-xs text-gray-500">
                    <i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i>
                    All matched products can be ordered directly through WhatsApp or Mini-Website cart.
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" onclick="addMatchedItemsToCart()" class="px-5 py-2.5 rounded-xl bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold transition flex items-center gap-2">
                        <i class="fa-solid fa-cart-plus"></i>
                        <span>Add Matched Items to Bag</span>
                    </button>

                    @if($sellerPage->whatsapp_number)
                        <button type="button" onclick="sendSlipQuotationViaWhatsApp()" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black shadow-md transition flex items-center gap-2">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                            <span>Send Estimate on WhatsApp</span>
                        </button>
                    @endif
                </div>
            </div>

        </div>

        <script>
            const parsedItems = @json($parsedData['items']);

            function addMatchedItemsToCart() {
                let addedCount = 0;
                parsedItems.forEach(item => {
                    if (item.is_matched && item.matched_product_id) {
                        addToCart({
                            id: item.matched_product_id,
                            name: item.matched_product_name,
                            price: item.unit_price,
                            image: item.image
                        }, item.quantity, false);
                        addedCount++;
                    }
                });

                if (addedCount > 0) {
                    alert(`🎉 Added ${addedCount} matched items to your shopping bag!`);
                    toggleCartDrawer(true);
                } else {
                    alert('No direct catalog matches found to add automatically.');
                }
            }

            function sendSlipQuotationViaWhatsApp() {
                if (!WHATSAPP_NUM) {
                    alert('WhatsApp number not set.');
                    return;
                }

                let text = `*New Material Parchi Estimate Request from ${STORE_NAME}*\n\n`;
                let total = 0;
                parsedItems.forEach((item, i) => {
                    text += `${i+1}. *${item.item_name}* (${item.detected_size || 'Std'}) x ${item.quantity} = ₹${item.line_total.toFixed(2)}\n`;
                    total += item.line_total;
                });
                text += `\n💰 *Total Estimated Bill:* ₹${total.toFixed(2)}\n`;
                text += `\nPlease confirm final rates, discount and delivery time.`;

                const url = `https://wa.me/${WHATSAPP_NUM}?text=${encodeURIComponent(text)}`;
                window.open(url, '_blank');
            }
        </script>
    @endif

</div>
@endsection
