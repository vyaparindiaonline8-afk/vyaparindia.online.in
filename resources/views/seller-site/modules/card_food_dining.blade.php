@php
    $variants = $product->variants;
    $firstVar = $variants->first();
    $initPrice = $firstVar ? ($firstVar->retail_price ?: ($firstVar->wholesale_price ?: $product->price)) : $product->price;
    $isNonVeg = stripos($product->name, 'chicken') !== false || stripos($product->name, 'mutton') !== false || stripos($product->name, 'egg') !== false || stripos($product->name, 'fish') !== false;
@endphp
<div class="bg-white rounded-3xl border border-amber-100 hover:border-amber-300 hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group p-3.5 space-y-3 relative" id="card_box_{{ $product->id }}">
    
    <!-- Dish Image with Veg/Non-Veg Marker -->
    <div>
        <div class="relative block aspect-video sm:aspect-[4/3] bg-amber-50/50 rounded-2xl overflow-hidden border border-amber-100 mb-3 flex items-center justify-center">
            <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" class="w-full h-full block">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" id="prod_img_{{ $product->id }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    <div class="w-full h-full flex items-center justify-center text-amber-300">
                        <i class="fa-solid fa-utensils text-4xl"></i>
                    </div>
                @endif
            </a>

            <!-- Veg / Non-Veg Icon (Indian Food Standard) -->
            <div class="absolute top-2.5 left-2.5 h-6 w-6 rounded-md bg-white shadow-md flex items-center justify-center border {{ $isNonVeg ? 'border-red-600' : 'border-emerald-600' }}">
                <span class="h-2.5 w-2.5 rounded-full {{ $isNonVeg ? 'bg-red-600' : 'bg-emerald-600' }}"></span>
            </div>

            <!-- Prep Time Badge -->
            <div class="absolute bottom-2.5 left-2.5 bg-black/75 backdrop-blur-xs text-white text-[10px] font-bold px-2 py-0.5 rounded-md flex items-center gap-1">
                <i class="fa-solid fa-clock text-[9px] text-amber-400"></i>
                <span>15-20 min</span>
            </div>

            <!-- WhatsApp Share -->
            <button type="button" onclick="shareProductCardWhatsapp({{ $product->id }})" class="absolute top-2.5 right-2.5 h-7 w-7 rounded-full bg-white/90 hover:bg-emerald-500 hover:text-white text-emerald-600 shadow-md flex items-center justify-center text-xs transition">
                <i class="fa-brands fa-whatsapp"></i>
            </button>

            <!-- Store Owner Photo Swapper & ✏️ Edit Button -->
            @auth
                @if(Auth::id() === $sellerPage->user_id)
                    <div class="absolute bottom-2.5 right-2.5 flex items-center gap-1">
                        <a href="{{ route('seller.products.edit', $product->id) }}" target="_blank" class="px-2 py-1 rounded-md bg-amber-600 hover:bg-amber-700 text-white font-black text-[10px] flex items-center gap-1 shadow-md" title="Edit Item & Rates">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </a>
                        <button type="button" onclick="openPhotoSwapper({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->image_url }}')" class="px-2 py-1 rounded-md bg-slate-900/85 hover:bg-slate-900 text-white font-black text-[10px] flex items-center gap-1 shadow-md" title="Change Photo">
                            <i class="fa-solid fa-camera"></i> Photo
                        </button>
                    </div>
                @endif
            @endauth
        </div>

        <!-- Category & Spice Level + Last Updated -->
        <div class="flex items-center justify-between gap-1 mb-1 flex-wrap">
            <span class="text-[10px] font-black text-amber-700 uppercase tracking-wider bg-amber-50 px-2 py-0.5 rounded-md">
                {{ $product->category ? $product->category->name : 'Kitchen Special' }}
            </span>
            <span class="text-[10px] text-amber-600 font-bold" title="Spice Level">
                🌶️🌶️ Medium
            </span>

            <!-- 🕒 Last Updated Timestamp -->
            @if(Auth::check() && Auth::id() === $sellerPage->user_id)
                <span class="w-full mt-1 px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-600 font-semibold text-[9px] border border-slate-200" title="Updated by you">
                    <i class="fa-regular fa-clock text-[8px]"></i> Menu Updated {{ $product->updated_at ? $product->updated_at->diffForHumans() : 'Recently' }}
                </span>
            @elseif(!empty($sellerPage->show_last_updated_to_buyers))
                <span class="w-full mt-1 px-1.5 py-0.5 rounded-md bg-amber-50 text-amber-800 font-semibold text-[9px] border border-amber-200" title="Fresh Rates">
                    <i class="fa-solid fa-fire text-[8px]"></i> ताज़ा मेनू: {{ $product->updated_at ? $product->updated_at->diffForHumans() : 'आज का' }}
                </span>
            @endif
        </div>

        <!-- Dish Title -->
        <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" class="font-black text-sm text-gray-900 hover:text-amber-600 line-clamp-1 transition-colors leading-tight" title="{{ $product->name }}">
            {{ $product->name }}
        </a>

        <!-- Portion Selector (Half / Full Plate) -->
        @if($variants && $variants->count() > 1)
            <div class="mt-2">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Portion:</label>
                <select id="card_variant_{{ $product->id }}" onchange="onCardVariantChanged({{ $product->id }})" class="w-full text-xs font-bold border border-amber-200 rounded-xl px-2.5 py-1.5 bg-amber-50/50 focus:bg-white focus:outline-none">
                    @foreach($variants as $idx => $v)
                        @php $vPrice = $v->retail_price ?: ($v->wholesale_price ?: $product->price); @endphp
                        <option value="{{ $v->id }}" data-price="{{ $vPrice }}" data-mrp="{{ $vPrice * 1.2 }}" data-name="{{ $v->variant_name }}" {{ $idx === 0 ? 'selected' : '' }}>
                            {{ $v->variant_name }} — ₹{{ number_format($vPrice, 2) }}
                        </option>
                    @endforeach
                </select>
            </div>
        @elseif($variants && $variants->count() === 1)
            <div class="mt-1 text-[11px] font-bold text-gray-500">
                Portion: <span class="text-gray-900 font-black">{{ $variants[0]->variant_name }}</span>
                <input type="hidden" id="card_variant_{{ $product->id }}" value="{{ $variants[0]->id }}" data-price="{{ $initPrice }}" data-mrp="{{ $initPrice * 1.2 }}" data-name="{{ $variants[0]->variant_name }}">
            </div>
        @endif

        <!-- Price & Quantity -->
        <div class="mt-3 flex items-baseline justify-between pt-1 border-t border-gray-100">
            <div>
                <span class="text-base sm:text-lg font-black text-gray-900" id="card_unit_price_{{ $product->id }}">₹{{ number_format($initPrice, 2) }}</span>
                <div class="text-[10px] font-bold text-emerald-700" id="card_total_box_{{ $product->id }}">
                    Total: <span id="card_total_price_{{ $product->id }}" class="font-extrabold">₹{{ number_format($initPrice, 2) }}</span>
                </div>
            </div>

            <!-- Plate Qty Stepper -->
            <div class="inline-flex items-center border border-amber-200 rounded-xl bg-amber-50/60 overflow-hidden">
                <button type="button" onclick="stepCardQty({{ $product->id }}, -1)" class="px-2.5 py-1 text-xs font-bold text-amber-900 hover:bg-amber-200">-</button>
                <input type="number" id="card_qty_{{ $product->id }}" value="1" min="1" onchange="onCardQtyInput({{ $product->id }})" class="w-7 text-center text-xs font-bold text-gray-900 border-none bg-transparent p-0" readonly>
                <button type="button" onclick="stepCardQty({{ $product->id }}, 1)" class="px-2.5 py-1 text-xs font-bold text-amber-900 hover:bg-amber-200">+</button>
            </div>
        </div>
    </div>

    <!-- Food Action Buttons -->
    <div class="pt-2 border-t border-gray-100 grid grid-cols-2 gap-2 mt-auto">
        <button type="button" onclick="addCardToBag({{ $product->id }})" id="btn_add_bag_{{ $product->id }}" class="py-2 px-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-950 font-black text-xs transition flex items-center justify-center gap-1.5 border border-amber-200">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Add</span>
        </button>

        <button type="button" onclick="orderCardOnWhatsapp({{ $product->id }})" class="py-2 px-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-black text-xs transition flex items-center justify-center gap-1.5 shadow-sm shadow-emerald-500/20">
            <i class="fa-brands fa-whatsapp text-sm"></i>
            <span>Order</span>
        </button>
    </div>
</div>
