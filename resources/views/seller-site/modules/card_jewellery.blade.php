@php
    $variants = $product->variants;
    $firstVar = $variants->first();
    $initPrice = $firstVar ? ($firstVar->retail_price ?: ($firstVar->wholesale_price ?: $product->price)) : $product->price;

    $attrs = is_array($product->attributes) ? $product->attributes : (json_decode($product->attributes ?? '[]', true) ?: []);
    $rateFormula = $attrs['rate_formula'] ?? [];
    $purityKey = $rateFormula['commodity_key'] ?? ($attrs['metal_purity'] ?? 'gold_22k');
    $purityLabel = '22K • 916 Hallmark';
    if ($purityKey === 'gold_24k') $purityLabel = '24K • 999 Pure Gold';
    elseif ($purityKey === 'gold_18k') $purityLabel = '18K • 750 Gold';
    elseif (str_contains($purityKey, 'silver')) $purityLabel = '999 • Pure Silver';

    $netWeightGm = floatval($rateFormula['weight_gm'] ?? ($attrs['net_weight_gm'] ?? 0));
    $makingVal = floatval($rateFormula['making_val'] ?? ($attrs['making_charge_val'] ?? 0));
    $makingType = $rateFormula['making_type'] ?? ($attrs['making_charge_type'] ?? 'percent');
    $huid = $attrs['hallmark_huid'] ?? 'BIS Hallmark Verified';
@endphp
<div class="bg-white rounded-3xl border border-amber-200 hover:border-amber-500 hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group p-3.5 space-y-3 relative" id="card_box_{{ $product->id }}">

    <!-- Luxury Jewellery Header -->
    <div>
        <div class="relative block aspect-square bg-gradient-to-br from-amber-50/70 via-white to-yellow-50 rounded-2xl overflow-hidden border border-amber-100 mb-3 flex items-center justify-center p-2">
            <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" class="w-full h-full flex items-center justify-center">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" id="prod_img_{{ $product->id }}" class="w-full h-full object-contain crisp-img group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="text-amber-300 text-center" id="prod_img_{{ $product->id }}">
                        <i class="fa-solid fa-gem text-4xl"></i>
                    </div>
                @endif
            </a>

            <!-- Hallmark & Purity Badge -->
            <span class="absolute top-2.5 left-2.5 bg-gradient-to-r from-amber-800 to-yellow-700 text-amber-50 text-[10px] font-black px-2.5 py-1 rounded-lg flex items-center gap-1 shadow-sm">
                <i class="fa-solid fa-certificate text-amber-300 text-[9px]"></i>
                <span>{{ $purityLabel }}</span>
            </span>

            <!-- WhatsApp Share -->
            <button type="button" onclick="shareProductCardWhatsapp({{ $product->id }})" class="absolute top-2.5 right-2.5 h-7 w-7 rounded-full bg-white/95 hover:bg-emerald-500 hover:text-white text-emerald-700 shadow-md flex items-center justify-center text-xs transition" title="WhatsApp पर डिज़ाइन शेयर करें">
                <i class="fa-brands fa-whatsapp"></i>
            </button>

            <!-- Store Owner Quick Actions -->
            @auth
                @if(Auth::id() === $sellerPage->user_id)
                    <div class="absolute bottom-2.5 right-2.5 flex items-center gap-1">
                        <a href="{{ route('seller.daily_rates.index') }}" target="_blank" class="px-2 py-1 rounded-md bg-amber-600 hover:bg-amber-700 text-white font-black text-[10px] flex items-center gap-1 shadow-sm" title="आज का सोना/चांदी भाव अपडेट करें">
                            <i class="fa-solid fa-coins"></i> बुलियन रेट
                        </a>
                        <a href="{{ route('seller.products.edit', $product->id) }}" target="_blank" class="px-2 py-1 rounded-md bg-blue-600 hover:bg-blue-700 text-white font-black text-[10px] flex items-center gap-1 shadow-sm">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </a>
                        <button type="button" onclick="openPhotoSwapper({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->image_url }}')" class="px-2 py-1 rounded-md bg-slate-900/85 hover:bg-slate-900 text-white font-black text-[10px] flex items-center gap-1 shadow-sm">
                            <i class="fa-solid fa-camera"></i> Photo
                        </button>
                    </div>
                @endif
            @endauth
        </div>

        <!-- Weight, Making & HUID Chips -->
        <div class="flex items-center justify-between gap-1.5 mb-1.5 flex-wrap">
            @if($netWeightGm > 0)
                <span class="text-[10px] font-black text-amber-950 bg-amber-100 px-2 py-0.5 rounded-md border border-amber-200 font-mono">
                    ⚖️ नेट वजन: {{ number_format($netWeightGm, 2) }} gm
                </span>
            @endif
            @if($makingVal > 0)
                <span class="text-[10px] font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                    🛠️ मेकिंग: {{ $makingType === 'percent' ? $makingVal . '%' : ($makingType === 'per_gm' ? '₹' . $makingVal . '/gm' : '₹' . $makingVal) }}
                </span>
            @endif
            <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                ✓ {{ $huid }}
            </span>

            @if(Auth::check() && Auth::id() === $sellerPage->user_id)
                <span class="w-full mt-1 px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-600 font-semibold text-[9px] border border-slate-200">
                    <i class="fa-regular fa-clock text-[8px]"></i> Bullion Updated {{ $product->updated_at ? $product->updated_at->diffForHumans() : 'Today' }}
                </span>
            @elseif(!empty($sellerPage->show_last_updated_to_buyers))
                <span class="w-full mt-1 px-1.5 py-0.5 rounded-md bg-amber-50 text-amber-900 font-bold text-[9px] border border-amber-200">
                    <i class="fa-solid fa-gem text-[8px]"></i> आज के बुलियन भाव पर: {{ $product->updated_at ? $product->updated_at->diffForHumans() : 'लाइव' }}
                </span>
            @endif
        </div>

        <!-- Product Title -->
        <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" class="font-black text-sm text-gray-900 hover:text-amber-700 line-clamp-2 transition-colors leading-tight" title="{{ $product->name }}">
            {{ $product->name }}
        </a>

        <!-- Weight / Ring Size Variants -->
        @if($variants && $variants->count() > 1)
            <div class="mt-2">
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">वजन / साइज़ चुनें:</label>
                <select id="card_variant_{{ $product->id }}" onchange="onCardVariantChanged({{ $product->id }})" class="w-full text-xs font-bold border border-amber-300 rounded-xl px-2.5 py-1.5 bg-amber-50/40 focus:bg-white focus:border-amber-600 focus:outline-none">
                    @foreach($variants as $idx => $v)
                        @php $vPrice = $v->retail_price ?: ($v->wholesale_price ?: $product->price); @endphp
                        <option value="{{ $v->id }}" data-price="{{ $vPrice }}" data-mrp="{{ $vPrice * 1.05 }}" data-name="{{ $v->variant_name }}" {{ $idx === 0 ? 'selected' : '' }}>
                            {{ $v->variant_name }} (₹{{ number_format($vPrice, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>
        @elseif($variants && $variants->count() === 1)
            <div class="mt-1.5 text-[11px] font-bold text-gray-600">
                साइज़/वजन: <span class="text-amber-950 font-black">{{ $variants[0]->variant_name }}</span>
                <input type="hidden" id="card_variant_{{ $product->id }}" value="{{ $variants[0]->id }}" data-price="{{ $initPrice }}" data-mrp="{{ $initPrice * 1.05 }}" data-name="{{ $variants[0]->variant_name }}">
            </div>
        @else
            <input type="hidden" id="card_variant_{{ $product->id }}" value="" data-price="{{ $initPrice }}" data-mrp="{{ $initPrice * 1.05 }}" data-name="{{ $netWeightGm > 0 ? $netWeightGm . ' gm' : 'Standard Piece' }}">
        @endif

        <!-- Live Bullion Price + 3% GST Breakup -->
        <div class="mt-3 pt-2 border-t border-amber-100 flex items-baseline justify-between gap-2">
            <div>
                <div class="text-[10px] font-bold text-gray-500 uppercase flex items-center gap-1">
                    <span>लाइव कीमत</span>
                    <span class="px-1 py-0.2 bg-amber-100 text-amber-800 rounded text-[8px] font-black">+3% GST सहित</span>
                </div>
                <span class="text-base sm:text-lg font-black text-amber-950 font-mono" id="card_unit_price_{{ $product->id }}">₹{{ number_format($initPrice, 2) }}</span>
                <div class="text-[11px] font-black text-emerald-700 mt-0.5" id="card_total_box_{{ $product->id }}">
                    कुल बिल: <span id="card_total_price_{{ $product->id }}" class="font-extrabold">₹{{ number_format($initPrice, 2) }}</span>
                </div>
            </div>

            <!-- Quantity Stepper -->
            <div>
                <div class="text-[10px] font-bold text-gray-500 text-right uppercase mb-0.5">पीस (Qty):</div>
                <div class="inline-flex items-center border border-amber-300 rounded-xl bg-white overflow-hidden shadow-2xs">
                    <button type="button" onclick="stepCardQty({{ $product->id }}, -1)" class="px-2.5 py-1 text-xs font-bold text-gray-700 hover:bg-amber-100">-</button>
                    <input type="number" id="card_qty_{{ $product->id }}" value="1" min="1" onchange="onCardQtyInput({{ $product->id }})" class="w-8 text-center text-xs font-black text-gray-900 border-none bg-transparent p-0" readonly>
                    <button type="button" onclick="stepCardQty({{ $product->id }}, 1)" class="px-2.5 py-1 text-xs font-bold text-gray-700 hover:bg-amber-100">+</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="pt-2 border-t border-amber-100 grid grid-cols-2 gap-2 mt-auto">
        <button type="button" onclick="addCardToBag({{ $product->id }})" id="btn_add_bag_{{ $product->id }}" class="py-2 px-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-950 font-black text-xs transition flex items-center justify-center gap-1.5 border border-amber-200">
            <i class="fa-solid fa-bag-shopping text-xs"></i>
            <span>लिस्ट में रखें</span>
        </button>

        <button type="button" onclick="orderCardOnWhatsapp({{ $product->id }})" class="py-2 px-2.5 rounded-xl bg-gradient-to-r from-amber-700 to-yellow-600 hover:from-amber-800 hover:to-yellow-700 text-white font-black text-xs transition flex items-center justify-center gap-1.5 shadow-md shadow-amber-700/20">
            <i class="fa-brands fa-whatsapp text-sm"></i>
            <span>रेट बुक करें</span>
        </button>
    </div>
</div>
