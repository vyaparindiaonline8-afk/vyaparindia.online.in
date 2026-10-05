@php
    $variants = $product->variants;
    $firstVar = $variants->first();
    $initPrice = $firstVar ? ($firstVar->retail_price ?: ($firstVar->wholesale_price ?: $product->price)) : $product->price;
    $initMrp = $firstVar ? ($firstVar->mrp ?: ($initPrice * 1.25)) : ($product->mrp ?: ($product->price * 1.25));
    $saveAmt = max(0, $initMrp - $initPrice);
@endphp
<div class="bg-white rounded-2xl border border-gray-200 hover:border-emerald-300 hover:shadow-lg transition-all flex flex-col justify-between overflow-hidden group p-3 space-y-2.5 relative" id="card_box_{{ $product->id }}">
    
    <!-- Grocery Square Image with Quick Tags -->
    <div>
        <div class="relative block aspect-square bg-emerald-50/30 rounded-xl overflow-hidden border border-gray-100 mb-2.5 flex items-center justify-center p-2">
            <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" class="w-full h-full flex items-center justify-center">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" id="prod_img_{{ $product->id }}" class="w-full h-full object-contain crisp-img group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="text-emerald-300 text-center" id="prod_img_{{ $product->id }}">
                        <i class="fa-solid fa-basket-shopping text-3xl"></i>
                    </div>
                @endif
            </a>

            <!-- Savings Badge -->
            @if($saveAmt > 0)
                <span class="absolute top-2 left-2 bg-emerald-600 text-white text-[9px] font-black px-2 py-0.5 rounded-md shadow-2xs">
                    ₹{{ round($saveAmt) }} OFF
                </span>
            @endif

            <!-- WhatsApp Share -->
            <button type="button" onclick="shareProductCardWhatsapp({{ $product->id }})" class="absolute top-2 right-2 h-7 w-7 rounded-full bg-white/90 hover:bg-emerald-500 hover:text-white text-emerald-600 shadow-sm flex items-center justify-center text-xs transition">
                <i class="fa-brands fa-whatsapp"></i>
            </button>

            <!-- Store Owner Photo Swapper & ✏️ Edit Button -->
            @auth
                @if(Auth::id() === $sellerPage->user_id)
                    <div class="absolute bottom-2 right-2 flex items-center gap-1">
                        <a href="{{ route('seller.products.edit', $product->id) }}" target="_blank" class="px-2 py-0.5 rounded-md bg-teal-600 hover:bg-teal-700 text-white font-bold text-[9px] flex items-center gap-1 shadow-sm" title="Edit Item & Rates">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </a>
                        <button type="button" onclick="openPhotoSwapper({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->image_url }}')" class="px-2 py-0.5 rounded-md bg-slate-900/80 hover:bg-slate-900 text-white font-bold text-[9px] flex items-center gap-1 shadow-sm" title="Change Photo">
                            <i class="fa-solid fa-camera"></i> Photo
                        </button>
                    </div>
                @endif
            @endauth
        </div>

        <!-- Category & Last Updated -->
        <div class="flex items-center justify-between gap-1 mb-0.5 flex-wrap">
            @if($product->category)
                <div class="text-[10px] font-bold text-gray-400">
                    {{ $product->category->name }}
                </div>
            @endif

            <!-- 🕒 Last Updated Timestamp -->
            @if(Auth::check() && Auth::id() === $sellerPage->user_id)
                <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-600 font-semibold text-[9px] border border-slate-200" title="Updated by you">
                    <i class="fa-regular fa-clock text-[8px]"></i> Updated {{ $product->updated_at ? $product->updated_at->diffForHumans() : 'Recently' }}
                </span>
            @elseif(!empty($sellerPage->show_last_updated_to_buyers))
                <span class="px-1.5 py-0.5 rounded-md bg-teal-50 text-teal-700 font-semibold text-[9px] border border-teal-200" title="Fresh Rates">
                    <i class="fa-solid fa-bolt text-[8px]"></i> ताज़ा: {{ $product->updated_at ? $product->updated_at->diffForHumans() : 'आज का' }}
                </span>
            @endif
        </div>

        <!-- Grocery Item Title -->
        <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" class="font-extrabold text-xs sm:text-sm text-gray-900 hover:text-emerald-700 line-clamp-2 transition-colors leading-tight" title="{{ $product->name }}">
            {{ $product->name }}
        </a>

        <!-- Pack Weight Variant Dropdown (100g, 250g, 500g, 1kg) -->
        @if($variants && $variants->count() > 1)
            <div class="mt-2">
                <select id="card_variant_{{ $product->id }}" onchange="onCardVariantChanged({{ $product->id }})" class="w-full text-xs font-bold border border-gray-200 rounded-lg px-2 py-1 bg-gray-50 focus:bg-white focus:outline-none">
                    @foreach($variants as $idx => $v)
                        @php $vPrice = $v->retail_price ?: ($v->wholesale_price ?: $product->price); @endphp
                        <option value="{{ $v->id }}" data-price="{{ $vPrice }}" data-mrp="{{ $v->mrp ?: ($vPrice * 1.25) }}" data-name="{{ $v->variant_name }}" {{ $idx === 0 ? 'selected' : '' }}>
                            {{ $v->variant_name }} - ₹{{ number_format($vPrice, 2) }}
                        </option>
                    @endforeach
                </select>
            </div>
        @elseif($variants && $variants->count() === 1)
            <div class="mt-1 text-[11px] font-bold text-gray-500 flex items-center gap-1">
                <span>Pack:</span>
                <span class="text-gray-900 font-extrabold">{{ $variants[0]->variant_name }}</span>
                <input type="hidden" id="card_variant_{{ $product->id }}" value="{{ $variants[0]->id }}" data-price="{{ $initPrice }}" data-mrp="{{ $initMrp }}" data-name="{{ $variants[0]->variant_name }}">
            </div>
        @else
            <input type="hidden" id="card_variant_{{ $product->id }}" value="" data-price="{{ $initPrice }}" data-mrp="{{ $initMrp }}" data-name="1 Unit">
        @endif

        <!-- Dynamic Live Price & Stepper -->
        <div class="mt-2.5 flex items-baseline justify-between pt-1 border-t border-gray-100">
            <div>
                <div class="flex items-baseline gap-1">
                    <span class="text-sm sm:text-base font-black text-gray-900" id="card_unit_price_{{ $product->id }}">₹{{ number_format($initPrice, 2) }}</span>
                    @if($initMrp > $initPrice)
                        <span class="text-[10px] text-gray-400 line-through" id="card_mrp_{{ $product->id }}">₹{{ number_format($initMrp, 2) }}</span>
                    @endif
                </div>
                <div class="text-[10px] font-bold text-emerald-700" id="card_total_box_{{ $product->id }}">
                    Total: <span id="card_total_price_{{ $product->id }}" class="font-extrabold">₹{{ number_format($initPrice, 2) }}</span>
                </div>
            </div>

            <!-- Pack Qty Stepper -->
            <div class="inline-flex items-center border border-gray-200 rounded-lg bg-gray-50 overflow-hidden">
                <button type="button" onclick="stepCardQty({{ $product->id }}, -1)" class="px-2 py-0.5 text-xs font-bold text-gray-600 hover:bg-gray-200">-</button>
                <input type="number" id="card_qty_{{ $product->id }}" value="1" min="1" onchange="onCardQtyInput({{ $product->id }})" class="w-6 text-center text-xs font-bold text-gray-900 border-none bg-transparent p-0" readonly>
                <button type="button" onclick="stepCardQty({{ $product->id }}, 1)" class="px-2 py-0.5 text-xs font-bold text-gray-600 hover:bg-gray-200">+</button>
            </div>
        </div>
    </div>

    <!-- 1-Click Action Buttons -->
    <div class="pt-2 border-t border-gray-100 grid grid-cols-2 gap-1.5 mt-auto">
        <button type="button" onclick="addCardToBag({{ $product->id }})" id="btn_add_bag_{{ $product->id }}" class="py-1.5 px-2 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-900 font-black text-xs transition flex items-center justify-center gap-1 border border-emerald-200" title="Add to Bag">
            <i class="fa-solid fa-plus text-[10px]"></i>
            <span>Add</span>
        </button>

        <button type="button" onclick="orderCardOnWhatsapp({{ $product->id }})" class="py-1.5 px-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white font-black text-xs transition flex items-center justify-center gap-1 shadow-2xs" title="WhatsApp Order">
            <i class="fa-brands fa-whatsapp text-sm"></i>
            <span>Order</span>
        </button>
    </div>
</div>
