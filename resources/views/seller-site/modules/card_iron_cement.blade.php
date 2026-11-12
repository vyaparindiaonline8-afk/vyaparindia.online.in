@php
    $variants = $product->variants;
    $firstVar = $variants->first();
    $initPrice = $firstVar ? ($firstVar->retail_price ?: ($firstVar->wholesale_price ?: $product->price)) : $product->price;
    $initWholesale = $firstVar ? ($firstVar->wholesale_price ?: $initPrice) : ($product->wholesale_price ?: $initPrice);

    $attrs = is_array($product->attributes) ? $product->attributes : (json_decode($product->attributes ?? '[]', true) ?: []);
    $rateFormula = $attrs['rate_formula'] ?? [];
    $rodWeightKg = floatval($rateFormula['weight_kg'] ?? ($attrs['rod_weight_kg'] ?? 0));
    $gradeLabel = $attrs['steel_cement_grade'] ?? ($rateFormula['grade'] ?? (stripos($product->name, 'cement') !== false ? '50 Kg Bag • ISI Grade' : 'Fe-550D ISI'));
    $isCement = stripos($product->name, 'cement') !== false || ($rateFormula['commodity_key'] ?? '') === 'cement_bag';
    $perKgRate = ($rodWeightKg > 0 && $initPrice > 0) ? round($initPrice / $rodWeightKg, 2) : 0;
@endphp
<div class="bg-white rounded-3xl border border-slate-300 hover:border-orange-500 hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group p-3.5 space-y-3 relative" id="card_box_{{ $product->id }}">

    <!-- Iron / Cement Visual Header -->
    <div>
        <div class="relative block aspect-[4/3] bg-gradient-to-br from-slate-100 to-orange-50/40 rounded-2xl overflow-hidden border border-slate-200 mb-3 flex items-center justify-center p-2">
            <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" class="w-full h-full flex items-center justify-center">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" id="prod_img_{{ $product->id }}" class="w-full h-full object-contain crisp-img group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="text-slate-400 text-center" id="prod_img_{{ $product->id }}">
                        <i class="fa-solid {{ $isCement ? 'fa-cubes' : 'fa-trowel-bricks' }} text-4xl"></i>
                    </div>
                @endif
            </a>

            <!-- Live Steel / Cement Rate Badge -->
            <span class="absolute top-2.5 left-2.5 bg-slate-900 text-orange-300 text-[10px] font-black px-2.5 py-1 rounded-lg flex items-center gap-1.5 shadow-sm border border-orange-500/30">
                <span class="h-1.5 w-1.5 rounded-full bg-orange-400 animate-ping"></span>
                <span>{{ $isCement ? 'ताज़ा सीमेंट भाव' : 'ताज़ा सरिया/लोहा भाव' }}</span>
            </span>

            <!-- WhatsApp Share -->
            <button type="button" onclick="shareProductCardWhatsapp({{ $product->id }})" class="absolute top-2.5 right-2.5 h-7 w-7 rounded-full bg-white/95 hover:bg-emerald-500 hover:text-white text-emerald-700 shadow-md flex items-center justify-center text-xs transition" title="WhatsApp पर रेट शेयर करें">
                <i class="fa-brands fa-whatsapp"></i>
            </button>

            <!-- Store Owner Quick Actions -->
            @auth
                @if(Auth::id() === $sellerPage->user_id)
                    <div class="absolute bottom-2.5 right-2.5 flex items-center gap-1">
                        <a href="{{ route('seller.daily_rates.index') }}" target="_blank" class="px-2 py-1 rounded-md bg-orange-600 hover:bg-orange-700 text-white font-black text-[10px] flex items-center gap-1 shadow-sm" title="मास्टर रेट से भाव बदलें">
                            <i class="fa-solid fa-bolt"></i> बेस रेट
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

        <!-- Specs & Grade Chips -->
        <div class="flex items-center justify-between gap-1.5 mb-1.5 flex-wrap">
            <span class="text-[10px] font-black text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200 uppercase">
                🏗️ {{ $gradeLabel }}
            </span>
            @if($rodWeightKg > 0)
                <span class="text-[10px] font-black text-orange-800 bg-orange-50 px-2 py-0.5 rounded-md border border-orange-200 font-mono">
                    ⚖️ {{ $rodWeightKg }} Kg / {{ $isCement ? 'Bag' : 'Pc' }}
                </span>
            @endif

            @if(Auth::check() && Auth::id() === $sellerPage->user_id)
                <span class="w-full mt-1 px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-600 font-semibold text-[9px] border border-slate-200">
                    <i class="fa-regular fa-clock text-[8px]"></i> Updated {{ $product->updated_at ? $product->updated_at->diffForHumans() : 'Today' }}
                </span>
            @elseif(!empty($sellerPage->show_last_updated_to_buyers))
                <span class="w-full mt-1 px-1.5 py-0.5 rounded-md bg-orange-50 text-orange-800 font-bold text-[9px] border border-orange-200">
                    <i class="fa-solid fa-bolt text-[8px]"></i> आज का ताज़ा भाव: {{ $product->updated_at ? $product->updated_at->diffForHumans() : 'लाइव' }}
                </span>
            @endif
        </div>

        <!-- Product Title -->
        <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" class="font-black text-sm text-gray-900 hover:text-orange-700 line-clamp-2 transition-colors leading-tight" title="{{ $product->name }}">
            {{ $product->name }}
        </a>

        <!-- Size / mm Selector -->
        @if($variants && $variants->count() > 1)
            <div class="mt-2">
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">साइज़ / ग्रेड चुनें (Size/mm):</label>
                <select id="card_variant_{{ $product->id }}" onchange="onCardVariantChanged({{ $product->id }})" class="w-full text-xs font-bold border border-slate-300 rounded-xl px-2.5 py-1.5 bg-slate-50 focus:bg-white focus:border-orange-500 focus:outline-none">
                    @foreach($variants as $idx => $v)
                        @php $vPrice = $v->retail_price ?: ($v->wholesale_price ?: $product->price); @endphp
                        <option value="{{ $v->id }}" data-price="{{ $vPrice }}" data-mrp="{{ $vPrice * 1.12 }}" data-name="{{ $v->variant_name }}" {{ $idx === 0 ? 'selected' : '' }}>
                            {{ $v->variant_name }} (₹{{ number_format($vPrice, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>
        @elseif($variants && $variants->count() === 1)
            <div class="mt-1.5 text-[11px] font-bold text-gray-600">
                साइज़: <span class="text-slate-900 font-black">{{ $variants[0]->variant_name }}</span>
                <input type="hidden" id="card_variant_{{ $product->id }}" value="{{ $variants[0]->id }}" data-price="{{ $initPrice }}" data-mrp="{{ $initPrice * 1.12 }}" data-name="{{ $variants[0]->variant_name }}">
            </div>
        @else
            <input type="hidden" id="card_variant_{{ $product->id }}" value="" data-price="{{ $initPrice }}" data-mrp="{{ $initPrice * 1.12 }}" data-name="{{ $isCement ? '50 Kg Bag' : 'Standard Pc' }}">
        @endif

        <!-- Live Rate & Weight Equivalence -->
        <div class="mt-3 pt-2 border-t border-slate-100 flex items-baseline justify-between gap-2">
            <div>
                <div class="text-[10px] font-bold text-gray-500 uppercase">{{ $isCement ? 'प्रति बोरी भाव:' : 'प्रति पीस / छड़ भाव:' }}</div>
                <span class="text-base sm:text-lg font-black text-slate-950 font-mono" id="card_unit_price_{{ $product->id }}">₹{{ number_format($initPrice, 2) }}</span>
                @if($perKgRate > 0 && !$isCement)
                    <div class="text-[10px] font-bold text-orange-700 font-mono">
                        (~₹{{ number_format($perKgRate, 2) }} / Kg • ₹{{ number_format($perKgRate * 100, 0) }} / क्विंटल)
                    </div>
                @elseif($initWholesale > 0 && $initWholesale < $initPrice)
                    <div class="text-[10px] font-bold text-indigo-700">
                        मिस्त्री / थोक रेट: ₹{{ number_format($initWholesale, 2) }}
                    </div>
                @endif
                <div class="text-[11px] font-black text-emerald-700 mt-0.5" id="card_total_box_{{ $product->id }}">
                    कुल अनुमानित: <span id="card_total_price_{{ $product->id }}" class="font-extrabold">₹{{ number_format($initPrice, 2) }}</span>
                </div>
            </div>

            <!-- Quantity Stepper -->
            <div>
                <div class="text-[10px] font-bold text-gray-500 text-right uppercase mb-0.5">{{ $isCement ? 'बोरी संख्या:' : 'पीस / बंडल:' }}</div>
                <div class="inline-flex items-center border border-slate-300 rounded-xl bg-white overflow-hidden shadow-2xs">
                    <button type="button" onclick="stepCardQty({{ $product->id }}, -1)" class="px-2.5 py-1 text-xs font-bold text-gray-700 hover:bg-orange-100">-</button>
                    <input type="number" id="card_qty_{{ $product->id }}" value="1" min="1" onchange="onCardQtyInput({{ $product->id }})" class="w-9 text-center text-xs font-black text-gray-900 border-none bg-transparent p-0" readonly>
                    <button type="button" onclick="stepCardQty({{ $product->id }}, 1)" class="px-2.5 py-1 text-xs font-bold text-gray-700 hover:bg-orange-100">+</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="pt-2 border-t border-slate-100 grid grid-cols-2 gap-2 mt-auto">
        <button type="button" onclick="addCardToBag({{ $product->id }})" id="btn_add_bag_{{ $product->id }}" class="py-2 px-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-900 font-black text-xs transition flex items-center justify-center gap-1.5 border border-slate-200">
            <i class="fa-solid fa-cart-plus text-xs"></i>
            <span>लिस्ट में जोड़ें</span>
        </button>

        <button type="button" onclick="orderCardOnWhatsapp({{ $product->id }})" class="py-2 px-2.5 rounded-xl bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white font-black text-xs transition flex items-center justify-center gap-1.5 shadow-md shadow-orange-600/20">
            <i class="fa-brands fa-whatsapp text-sm"></i>
            <span>बुक / रेट लें</span>
        </button>
    </div>
</div>
