@php
    $variants = $product->variants;
    $firstVar = $variants->first();
    $initPrice = $firstVar ? ($firstVar->wholesale_price ?: ($firstVar->retail_price ?: $product->price)) : $product->price;
@endphp
<div class="bg-white rounded-3xl border border-emerald-200 hover:border-emerald-400 hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group p-3.5 space-y-3 relative" id="card_box_{{ $product->id }}">
    
    <!-- Commodity Image & Mandi Bhav Ticker -->
    <div>
        <div class="relative block aspect-[4/3] bg-emerald-50/50 rounded-2xl overflow-hidden border border-emerald-100 mb-3 flex items-center justify-center p-2">
            <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" class="w-full h-full flex items-center justify-center">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" id="prod_img_{{ $product->id }}" class="w-full h-full object-contain crisp-img group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="text-emerald-300 text-center" id="prod_img_{{ $product->id }}">
                        <i class="fa-solid fa-wheat-awn text-4xl"></i>
                    </div>
                @endif
            </a>

            <!-- Mandi Live Bhav Badge -->
            <span class="absolute top-2.5 left-2.5 bg-emerald-800 text-emerald-100 text-[10px] font-black px-2.5 py-1 rounded-lg flex items-center gap-1 shadow-sm">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                <span>लाइव मंडी भाव</span>
            </span>

            <!-- WhatsApp Share -->
            <button type="button" onclick="shareProductCardWhatsapp({{ $product->id }})" class="absolute top-2.5 right-2.5 h-7 w-7 rounded-full bg-white/90 hover:bg-emerald-500 hover:text-white text-emerald-700 shadow-md flex items-center justify-center text-xs transition" title="Share Mandi Bhav on WhatsApp">
                <i class="fa-brands fa-whatsapp"></i>
            </button>

            <!-- Store Owner Photo Swapper & ✏️ Edit Button -->
            @auth
                @if(Auth::id() === $sellerPage->user_id)
                    <div class="absolute bottom-2.5 right-2.5 flex items-center gap-1">
                        <a href="{{ route('seller.products.edit', $product->id) }}" target="_blank" class="px-2 py-1 rounded-md bg-yellow-600 hover:bg-yellow-700 text-white font-black text-[10px] flex items-center gap-1 shadow-sm" title="Edit Mandi Bhav">
                            <i class="fa-solid fa-pen-to-square"></i> भाव बदलें
                        </a>
                        <button type="button" onclick="openPhotoSwapper({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->image_url }}')" class="px-2 py-1 rounded-md bg-slate-900/85 hover:bg-slate-900 text-white font-black text-[10px] flex items-center gap-1 shadow-sm" title="Change Photo">
                            <i class="fa-solid fa-camera"></i> Photo
                        </button>
                    </div>
                @endif
            @endauth
        </div>

        <!-- Mandi Category & Quality Grade + Last Updated Bhav -->
        <div class="flex items-center justify-between gap-1 mb-1 flex-wrap">
            <span class="text-[10px] font-black text-emerald-900 bg-emerald-100 px-2 py-0.5 rounded-md uppercase">
                🌾 {{ $product->category ? $product->category->name : 'कृषि उपज' }}
            </span>
            <span class="text-[10px] text-gray-500 font-bold">
                Grade: Super Clean
            </span>

            <!-- 🕒 Last Updated Timestamp -->
            @if(Auth::check() && Auth::id() === $sellerPage->user_id)
                <span class="w-full mt-1 px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-600 font-semibold text-[9px] border border-slate-200" title="Updated by you">
                    <i class="fa-regular fa-clock text-[8px]"></i> Bhav Updated {{ $product->updated_at ? $product->updated_at->diffForHumans() : 'Recently' }}
                </span>
            @elseif(!empty($sellerPage->show_last_updated_to_buyers))
                <span class="w-full mt-1 px-1.5 py-0.5 rounded-md bg-emerald-50 text-emerald-800 font-bold text-[9px] border border-emerald-200" title="Live Mandi Bhav">
                    <i class="fa-solid fa-clock-rotate-left text-[8px]"></i> ताज़ा मंडी भाव: {{ $product->updated_at ? $product->updated_at->diffForHumans() : 'आज का भाव' }}
                </span>
            @endif
        </div>

        <!-- Commodity Title -->
        <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" class="font-black text-sm text-gray-900 hover:text-emerald-700 line-clamp-1 transition-colors leading-tight" title="{{ $product->name }}">
            {{ $product->name }}
        </a>

        <!-- Trading Unit Selector (प्रति क्विंटल / प्रति बोरी) -->
        @if($variants && $variants->count() > 1)
            <div class="mt-2">
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">सौदा यूनिट (Trading Unit):</label>
                <select id="card_variant_{{ $product->id }}" onchange="onCardVariantChanged({{ $product->id }})" class="w-full text-xs font-bold border border-emerald-300 rounded-xl px-2.5 py-1.5 bg-emerald-50/40 focus:bg-white focus:outline-none">
                    @foreach($variants as $idx => $v)
                        @php $vPrice = $v->wholesale_price ?: ($v->retail_price ?: $product->price); @endphp
                        <option value="{{ $v->id }}" data-price="{{ $vPrice }}" data-mrp="{{ $vPrice * 1.1 }}" data-name="{{ $v->variant_name }}" {{ $idx === 0 ? 'selected' : '' }}>
                            {{ $v->variant_name }} (₹{{ number_format($vPrice, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>
        @elseif($variants && $variants->count() === 1)
            <div class="mt-1 text-[11px] font-bold text-gray-600">
                यूनिट: <span class="text-emerald-900 font-black">{{ $variants[0]->variant_name }}</span>
                <input type="hidden" id="card_variant_{{ $product->id }}" value="{{ $variants[0]->id }}" data-price="{{ $initPrice }}" data-mrp="{{ $initPrice * 1.1 }}" data-name="{{ $variants[0]->variant_name }}">
            </div>
        @else
            <input type="hidden" id="card_variant_{{ $product->id }}" value="" data-price="{{ $initPrice }}" data-mrp="{{ $initPrice * 1.1 }}" data-name="Standard Lot">
        @endif

        <!-- Mandi Bhav & Total Sauda Value -->
        <div class="mt-3 flex items-baseline justify-between pt-1 border-t border-emerald-100">
            <div>
                <div class="text-[10px] font-bold text-gray-500 uppercase">ताजा भाव:</div>
                <span class="text-base sm:text-lg font-black text-emerald-950" id="card_unit_price_{{ $product->id }}">₹{{ number_format($initPrice, 2) }}</span>
                <div class="text-[11px] font-black text-emerald-700" id="card_total_box_{{ $product->id }}">
                    कुल सौदा: <span id="card_total_price_{{ $product->id }}" class="font-extrabold">₹{{ number_format($initPrice, 2) }}</span>
                </div>
            </div>

            <!-- Lot Quantity Stepper -->
            <div>
                <div class="text-[10px] font-bold text-gray-500 text-right uppercase mb-0.5">बोरी / क्विंटल:</div>
                <div class="inline-flex items-center border border-emerald-300 rounded-xl bg-white overflow-hidden shadow-2xs">
                    <button type="button" onclick="stepCardQty({{ $product->id }}, -1)" class="px-2.5 py-1 text-xs font-bold text-gray-700 hover:bg-emerald-100">-</button>
                    <input type="number" id="card_qty_{{ $product->id }}" value="1" min="1" onchange="onCardQtyInput({{ $product->id }})" class="w-8 text-center text-xs font-black text-gray-900 border-none bg-transparent p-0" readonly>
                    <button type="button" onclick="stepCardQty({{ $product->id }}, 1)" class="px-2.5 py-1 text-xs font-bold text-gray-700 hover:bg-emerald-100">+</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mandi Action Buttons -->
    <div class="pt-2 border-t border-emerald-100 grid grid-cols-2 gap-2 mt-auto">
        <button type="button" onclick="addCardToBag({{ $product->id }})" id="btn_add_bag_{{ $product->id }}" class="py-2 px-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-950 font-black text-xs transition flex items-center justify-center gap-1.5 border border-emerald-200">
            <i class="fa-solid fa-list-check text-xs"></i>
            <span>लिस्ट में जोड़ें</span>
        </button>

        <button type="button" onclick="orderCardOnWhatsapp({{ $product->id }})" class="py-2 px-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs transition flex items-center justify-center gap-1.5 shadow-md shadow-emerald-600/20">
            <i class="fa-brands fa-whatsapp text-sm"></i>
            <span>सौदा पक्का</span>
        </button>
    </div>
</div>
