@php
    $variants = $product->variants;
    $firstVar = $variants->first();
    $initPrice = $firstVar ? ($firstVar->retail_price ?: ($firstVar->wholesale_price ?: $product->price)) : $product->price;
    $initMrp = $firstVar ? ($firstVar->mrp ?: ($initPrice * 1.40)) : ($product->mrp ?: ($product->price * 1.40));
    $discount = ($initMrp > $initPrice) ? round((($initMrp - $initPrice) / $initMrp) * 100) : 0;
@endphp
<div class="bg-white rounded-3xl border border-gray-100 hover:border-pink-300 hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group p-3 space-y-3 relative" id="card_box_{{ $product->id }}">
    
    <!-- Boutique Portrait Image (4:5 Ratio) -->
    <div>
        <div class="relative block aspect-[4/5] bg-rose-50/40 rounded-2xl overflow-hidden border border-rose-100/60 mb-3 flex items-center justify-center">
            <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" class="w-full h-full block">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" id="prod_img_{{ $product->id }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    <div class="w-full h-full flex items-center justify-center text-rose-300">
                        <i class="fa-solid fa-shirt text-4xl"></i>
                    </div>
                @endif
            </a>

            <!-- Brand / Boutique Label -->
            @if($product->brand)
                <span class="absolute top-2.5 left-2.5 bg-black/80 backdrop-blur-md text-white text-[10px] font-black tracking-widest uppercase px-2.5 py-1 rounded-full shadow-sm">
                    {{ $product->brand }}
                </span>
            @endif

            <!-- Discount Badge -->
            @if($discount > 0)
                <span class="absolute bottom-2.5 left-2.5 bg-rose-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-sm">
                    {{ $discount }}% OFF
                </span>
            @endif

            <!-- 📲 WhatsApp Share Icon -->
            <button type="button" onclick="shareProductCardWhatsapp({{ $product->id }})" class="absolute top-2.5 right-2.5 h-8 w-8 rounded-full bg-white/90 hover:bg-emerald-500 hover:text-white text-emerald-600 shadow-md flex items-center justify-center text-xs transition active:scale-95" title="Share Look on WhatsApp">
                <i class="fa-brands fa-whatsapp text-sm"></i>
            </button>

            <!-- 📷 Store Owner Photo Swapper & ✏️ Edit Button -->
            @auth
                @if(Auth::id() === $sellerPage->user_id)
                    <div class="absolute bottom-2.5 right-2.5 flex items-center gap-1">
                        <a href="{{ route('seller.products.edit', $product->id) }}" target="_blank" class="px-2.5 py-1 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-[10px] flex items-center gap-1 shadow-md backdrop-blur-sm transition" title="Edit Price & Details">
                            <i class="fa-solid fa-pen-to-square text-[9px]"></i>
                            <span>Edit</span>
                        </a>
                        <button type="button" onclick="openPhotoSwapper({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->image_url }}')" class="px-2.5 py-1 rounded-xl bg-slate-900/85 hover:bg-slate-900 text-white font-black text-[10px] flex items-center gap-1 shadow-md backdrop-blur-sm transition" title="Change Photo">
                            <i class="fa-solid fa-camera text-[9px]"></i>
                            <span>Photo</span>
                        </button>
                    </div>
                @endif
            @endauth
        </div>

        <!-- Category & Fabric Meta + Last Updated -->
        <div class="flex items-center gap-1.5 flex-wrap mb-1">
            @if($product->category)
                <span class="text-[10px] font-black text-rose-600 tracking-wider uppercase">
                    {{ $product->category->name }}
                </span>
            @endif
            @if(!empty($product->attributes['fabric']))
                <span class="text-[10px] font-medium text-gray-400">• {{ $product->attributes['fabric'] }}</span>
            @endif

            <!-- 🕒 Last Updated Timestamp -->
            @if(Auth::check() && Auth::id() === $sellerPage->user_id)
                <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-600 font-semibold text-[9px] border border-slate-200" title="Updated by you">
                    <i class="fa-regular fa-clock text-[8px]"></i> Updated {{ $product->updated_at ? $product->updated_at->diffForHumans() : 'Recently' }}
                </span>
            @elseif(!empty($sellerPage->show_last_updated_to_buyers))
                <span class="px-1.5 py-0.5 rounded-md bg-rose-50 text-rose-700 font-semibold text-[9px] border border-rose-200" title="Fresh Arrival">
                    <i class="fa-solid fa-sparkles text-[8px]"></i> New: {{ $product->updated_at ? $product->updated_at->diffForHumans() : 'Latest' }}
                </span>
            @endif
        </div>

        <!-- Product Title -->
        <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" class="font-extrabold text-sm text-gray-900 hover:text-rose-600 line-clamp-1 transition-colors leading-tight" title="{{ $product->name }}">
            {{ $product->name }}
        </a>

        <!-- Fashion Size Selector Chips -->
        @if($variants && $variants->count() > 1)
            <div class="mt-2">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Select Size:</div>
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none">
                    @foreach($variants as $idx => $v)
                        @php
                            $vPrice = $v->retail_price ?: ($v->wholesale_price ?: $product->price);
                            $vMrp = $v->mrp ?: ($vPrice * 1.40);
                        @endphp
                        <button type="button" onclick="selectFashionSizeChip({{ $product->id }}, {{ $v->id }}, {{ $vPrice }}, {{ $vMrp }}, '{{ $v->variant_name }}')" id="chip_{{ $product->id }}_{{ $v->id }}" class="fashion-size-chip px-2.5 py-1 rounded-xl border text-[11px] font-black transition {{ $idx === 0 ? 'bg-gray-900 text-white border-gray-900 shadow-xs' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                            {{ $v->variant_name }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" id="card_variant_{{ $product->id }}" value="{{ $firstVar->id }}" data-price="{{ $initPrice }}" data-mrp="{{ $initMrp }}" data-name="{{ $firstVar->variant_name }}">
            </div>
        @elseif($variants && $variants->count() === 1)
            <div class="mt-1 text-[11px] font-bold text-gray-400 flex items-center gap-1">
                <span>Size:</span>
                <span class="font-black text-gray-800">{{ $variants[0]->variant_name }}</span>
                <input type="hidden" id="card_variant_{{ $product->id }}" value="{{ $variants[0]->id }}" data-price="{{ $initPrice }}" data-mrp="{{ $initMrp }}" data-name="{{ $variants[0]->variant_name }}">
            </div>
        @endif

        <!-- Price & Quantity -->
        <div class="mt-2.5 flex items-baseline justify-between pt-1 border-t border-gray-50">
            <div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-base sm:text-lg font-black text-gray-900" id="card_unit_price_{{ $product->id }}">₹{{ number_format($initPrice, 2) }}</span>
                    @if($initMrp > $initPrice)
                        <span class="text-xs text-gray-400 line-through" id="card_mrp_{{ $product->id }}">₹{{ number_format($initMrp, 2) }}</span>
                    @endif
                </div>
                <div class="text-[10px] font-bold text-gray-400" id="card_total_box_{{ $product->id }}">
                    Total: <span id="card_total_price_{{ $product->id }}" class="text-gray-900 font-extrabold">₹{{ number_format($initPrice, 2) }}</span>
                </div>
            </div>

            <!-- Stepper -->
            <div class="inline-flex items-center border border-gray-200 rounded-xl bg-gray-50 overflow-hidden">
                <button type="button" onclick="stepCardQty({{ $product->id }}, -1)" class="px-2 py-0.5 text-xs font-bold text-gray-600 hover:bg-gray-200">-</button>
                <input type="number" id="card_qty_{{ $product->id }}" value="1" min="1" onchange="onCardQtyInput({{ $product->id }})" class="w-7 text-center text-xs font-bold text-gray-900 border-none bg-transparent p-0" readonly>
                <button type="button" onclick="stepCardQty({{ $product->id }}, 1)" class="px-2 py-0.5 text-xs font-bold text-gray-600 hover:bg-gray-200">+</button>
            </div>
        </div>
    </div>

    <!-- Fashion Action Buttons -->
    <div class="pt-2 border-t border-gray-100 grid grid-cols-2 gap-2 mt-auto">
        <button type="button" onclick="addCardToBag({{ $product->id }})" id="btn_add_bag_{{ $product->id }}" class="py-2 px-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-900 font-black text-xs transition flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-bag-shopping text-xs"></i>
            <span>Bag</span>
        </button>

        <button type="button" onclick="orderCardOnWhatsapp({{ $product->id }})" class="py-2 px-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-black text-xs transition flex items-center justify-center gap-1.5 shadow-sm shadow-emerald-500/20">
            <i class="fa-brands fa-whatsapp text-sm"></i>
            <span>Order</span>
        </button>
    </div>
</div>
