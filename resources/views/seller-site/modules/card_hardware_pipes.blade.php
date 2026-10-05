@php
    $variants = $product->variants;
    $firstVar = $variants->first();
    $initPrice = $firstVar ? ($firstVar->retail_price ?: ($firstVar->wholesale_price ?: $product->price)) : $product->price;
    $initMrp = $firstVar ? ($firstVar->mrp ?: ($initPrice * 1.35)) : ($product->mrp ?: ($product->price * 1.35));
@endphp
<div class="bg-white rounded-2xl border border-gray-200 hover:border-indigo-300 hover:shadow-lg transition-all flex flex-col justify-between overflow-hidden group p-3.5 space-y-3 relative" id="card_box_{{ $product->id }}">
    
    <!-- Top Image & Quick Badges -->
    <div>
        <div class="relative block aspect-square bg-slate-50 rounded-xl overflow-hidden border border-slate-100 mb-3 flex items-center justify-center p-2">
            <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" class="w-full h-full flex items-center justify-center">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" id="prod_img_{{ $product->id }}" class="w-full h-full object-contain crisp-img group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="text-gray-300 text-center" id="prod_img_{{ $product->id }}">
                        <i class="fa-solid fa-wrench text-3xl"></i>
                    </div>
                @endif
            </a>

            <!-- Brand Badge (Top Left) -->
            @if($product->brand)
                <span class="absolute top-2 left-2 bg-blue-600/90 backdrop-blur-xs text-white text-[10px] font-black px-2 py-0.5 rounded-md shadow-2xs">
                    {{ $product->brand }}
                </span>
            @elseif($product->category)
                <span class="absolute top-2 left-2 bg-gray-900/90 backdrop-blur-xs text-white text-[10px] font-black px-2 py-0.5 rounded-md shadow-2xs">
                    {{ $product->category->name }}
                </span>
            @endif

            <!-- 📲 Card 1-Click WhatsApp Share Button -->
            <button type="button" onclick="shareProductCardWhatsapp({{ $product->id }})" class="absolute top-2 right-2 h-7 w-7 rounded-full bg-white/90 hover:bg-emerald-500 hover:text-white text-emerald-600 shadow-sm flex items-center justify-center text-xs transition" title="Share this Product on WhatsApp">
                <i class="fa-brands fa-whatsapp text-sm"></i>
            </button>

            <!-- 📷 Store Owner Photo Swapper & ✏️ Edit Button -->
            @auth
                @if(Auth::id() === $sellerPage->user_id)
                    <div class="absolute bottom-2 right-2 flex items-center gap-1">
                        <a href="{{ route('seller.products.edit', $product->id) }}" target="_blank" class="px-2 py-1 rounded-md bg-blue-600/90 hover:bg-blue-700 text-white font-bold text-[10px] flex items-center gap-1 shadow-xs backdrop-blur-xs transition" title="Edit Rate & Details">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>Edit</span>
                        </a>
                        <button type="button" onclick="openPhotoSwapper({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->image_url }}')" class="px-2 py-1 rounded-md bg-slate-900/80 hover:bg-slate-900 text-white font-bold text-[10px] flex items-center gap-1 shadow-xs backdrop-blur-xs transition" title="Change Photo directly on this card">
                            <i class="fa-solid fa-camera"></i>
                            <span>Photo</span>
                        </button>
                    </div>
                @endif
            @endauth
        </div>

        <!-- Group & Category Meta Tags + Last Updated -->
        <div class="flex items-center gap-1.5 flex-wrap mb-1.5">
            @if($product->group_name)
                <span class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 font-bold text-[10px] border border-purple-200">
                    📂 {{ $product->group_name }}
                </span>
            @endif
            @if($product->category)
                <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 font-bold text-[10px]">
                    {{ $product->category->name }}
                </span>
            @endif

            <!-- 🕒 Last Updated Timestamp -->
            @if(Auth::check() && Auth::id() === $sellerPage->user_id)
                <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-600 font-semibold text-[9px] border border-slate-200" title="Updated by you">
                    <i class="fa-regular fa-clock text-[8px]"></i> Updated {{ $product->updated_at ? $product->updated_at->diffForHumans() : 'Recently' }}
                </span>
            @elseif(!empty($sellerPage->show_last_updated_to_buyers))
                <span class="px-1.5 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-semibold text-[9px] border border-emerald-200" title="Fresh Rates">
                    <i class="fa-solid fa-bolt text-[8px]"></i> ताज़ा रेट: {{ $product->updated_at ? $product->updated_at->diffForHumans() : 'लेटेस्ट' }}
                </span>
            @endif
        </div>
        </div>

        <!-- Product Title -->
        <a href="{{ route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug]) }}" class="font-black text-xs sm:text-sm text-gray-900 hover:text-indigo-600 line-clamp-2 transition-colors leading-tight" title="{{ $product->name }}">
            {{ $product->name }}
        </a>

        <!-- Flexible Size / Variant Selection on Card -->
        @if($variants && $variants->count() > 1)
            <div class="mt-2.5">
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">
                    Choose Size / Variant:
                </label>
                <select id="card_variant_{{ $product->id }}" onchange="onCardVariantChanged({{ $product->id }})" class="w-full text-xs font-bold border border-gray-200 rounded-xl px-2.5 py-1.5 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:outline-none transition">
                    @foreach($variants as $idx => $v)
                        @php
                            $vPrice = $v->retail_price ?: ($v->wholesale_price ?: $product->price);
                            $vMrp = $v->mrp ?: ($vPrice * 1.35);
                        @endphp
                        <option value="{{ $v->id }}" data-price="{{ $vPrice }}" data-mrp="{{ $vMrp }}" data-name="{{ $v->variant_name }}" data-stock="{{ $v->stock_quantity ?? 0 }}" {{ $idx === 0 ? 'selected' : '' }}>
                            {{ $v->variant_name }} (₹{{ number_format($vPrice, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>
        @elseif($variants && $variants->count() === 1)
            <div class="mt-2 text-[11px] font-bold text-gray-500 flex items-center gap-1.5">
                <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 font-mono text-[10px]">{{ $variants[0]->variant_name }}</span>
                <input type="hidden" id="card_variant_{{ $product->id }}" value="{{ $variants[0]->id }}" data-price="{{ $initPrice }}" data-mrp="{{ $initMrp }}" data-name="{{ $variants[0]->variant_name }}">
            </div>
        @endif

        <!-- Dynamic Live Price & Calculated Total -->
        <div class="mt-3 flex items-baseline justify-between">
            <div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-base sm:text-lg font-black text-gray-900" id="card_unit_price_{{ $product->id }}">₹{{ number_format($initPrice, 2) }}</span>
                    <span class="text-xs text-gray-400 line-through" id="card_mrp_{{ $product->id }}">₹{{ number_format($initMrp, 2) }}</span>
                </div>
                <div class="text-[11px] font-bold text-emerald-700" id="card_total_box_{{ $product->id }}">
                    Total: <span id="card_total_price_{{ $product->id }}" class="font-extrabold">₹{{ number_format($initPrice, 2) }}</span>
                </div>
            </div>

            <!-- Quantity Stepper [-] [ 1 ] [+] on Card -->
            <div class="inline-flex items-center border border-gray-200 rounded-xl bg-gray-50 overflow-hidden shadow-2xs">
                <button type="button" onclick="stepCardQty({{ $product->id }}, -1)" class="px-2.5 py-1 text-xs font-bold text-gray-600 hover:bg-gray-200 transition-colors">-</button>
                <input type="number" id="card_qty_{{ $product->id }}" value="1" min="1" onchange="onCardQtyInput({{ $product->id }})" class="w-8 text-center text-xs font-bold text-gray-900 border-none bg-transparent focus:outline-none p-0" readonly>
                <button type="button" onclick="stepCardQty({{ $product->id }}, 1)" class="px-2.5 py-1 text-xs font-bold text-gray-600 hover:bg-gray-200 transition-colors">+</button>
            </div>
        </div>
    </div>

    <!-- 1-Click Action Buttons Right on the Card -->
    <div class="pt-3 border-t border-gray-100 grid grid-cols-2 gap-2 mt-auto">
        <button type="button" onclick="addCardToBag({{ $product->id }})" id="btn_add_bag_{{ $product->id }}" class="py-2 px-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs transition flex items-center justify-center gap-1.5" title="Add to Shopping Bag">
            <i class="fa-solid fa-bag-shopping text-xs"></i>
            <span>Add to Bag</span>
        </button>

        <button type="button" onclick="orderCardOnWhatsapp({{ $product->id }})" class="py-2 px-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-xs shadow-emerald-500/20" title="Direct WhatsApp Order">
            <i class="fa-brands fa-whatsapp text-sm"></i>
            <span>WhatsApp</span>
        </button>
    </div>
</div>
