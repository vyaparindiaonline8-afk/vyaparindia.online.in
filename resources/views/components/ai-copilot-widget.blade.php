<!-- 🤖 VyaparIndia Universal AI Copilot Widget (Modular Store & Buyer Assistant) -->
<div id="vyapar-ai-copilot" class="fixed bottom-6 right-6 z-50 font-sans" x-data="aiCopilotWidget()">
    <!-- Floating Trigger Button -->
    <button @click="toggleChat()" 
            class="group relative flex items-center gap-2.5 px-4 py-3 bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 text-white rounded-full shadow-2xl hover:shadow-indigo-500/40 hover:scale-105 transition-all duration-300 focus:outline-none">
        <span class="relative flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-400"></span>
        </span>
        <i class="fa-solid fa-wand-magic-sparkles text-amber-300 text-base"></i>
        <span class="text-xs font-black tracking-wide uppercase">AI Assistant</span>
    </button>

    <!-- Chat Modal Window -->
    <div id="ai-chat-window" 
         class="hidden fixed bottom-20 right-4 sm:right-6 w-[94vw] sm:w-[420px] h-[580px] max-h-[85vh] bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col transition-all duration-300 z-50">
        
        <!-- Header -->
        <div class="px-5 py-4 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white flex items-center justify-between border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-500 to-violet-500 flex items-center justify-center text-white shadow-md">
                    <i class="fa-solid fa-robot text-lg"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black tracking-tight flex items-center gap-2">
                        VyaparIndia Copilot
                        <span class="text-[9px] font-extrabold uppercase bg-emerald-500/20 text-emerald-300 px-2 py-0.5 rounded-full border border-emerald-500/30">AI Active</span>
                    </h3>
                    <p class="text-[10px] text-slate-300">Catalog Listing, Quotations & 24/7 Guide</p>
                </div>
            </div>
            <button onclick="document.getElementById('ai-chat-window').classList.add('hidden')" 
                    class="h-8 w-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-slate-300 hover:text-white transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Messages Area -->
        <div id="ai-chat-messages" class="flex-1 p-4 overflow-y-auto space-y-3.5 bg-slate-50/60 text-xs">
            <!-- Initial Welcome -->
            <div class="flex items-start gap-2.5 max-w-[88%]">
                <div class="w-7 h-7 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-xs shrink-0 shadow-xs">
                    <i class="fa-solid fa-sparkles"></i>
                </div>
                <div class="bg-white p-3.5 rounded-2xl rounded-tl-xs border border-slate-200 shadow-xs text-slate-800 leading-relaxed space-y-2">
                    <p>Namaste! 🙏 Main <strong>VyaparIndia AI Copilot</strong> hoon.</p>
                    <p class="text-slate-600 text-[11px]">
                        @auth
                            @if(Auth::user()->is_seller())
                                Main aapke products <strong>photo dekh kar auto-list</strong> kar sakta hoon, rates badal sakta hoon, ya mini-website upgrade me madad kar sakta hoon.
                            @else
                                Main aapko direct factory wholesale rates aur verified suppliers khojne me madad kar sakta hoon.
                            @endif
                        @else
                            Aap mujhse wholesale rates, verified manufacturers, ya store shuru karne ke baare me poochh sakte hain!
                        @endauth
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick Suggestions Carousel -->
        <div id="ai-quick-pills" class="px-4 py-2 bg-white border-t border-slate-100 flex items-center gap-1.5 overflow-x-auto no-scrollbar">
            @auth
                @if(Auth::user()->is_seller())
                    <button onclick="aiSendQuick('Mera live inventory dikhao')" class="px-3 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full whitespace-nowrap transition border border-emerald-200">
                        📦 Mera Inventory
                    </button>
                    <button onclick="aiSendQuick('Kitne orders pending hain?')" class="px-3 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full whitespace-nowrap transition border border-amber-200">
                        🛒 Pending Orders
                    </button>
                    <button onclick="aiSendQuick('Catalog tools aur PDF studio dikhao')" class="px-3 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-[10px] font-bold rounded-full whitespace-nowrap transition border border-blue-200">
                        📄 Catalog & PDF Tools
                    </button>
                    <button onclick="aiSendQuick('Naya product photo se list karo')" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[10px] font-bold rounded-full whitespace-nowrap transition border border-indigo-200">
                        ✨ Naya Item List Karo
                    </button>
                    <button onclick="aiSendQuick('Mera profile Mini-Website me upgrade karo')" class="px-3 py-1 bg-violet-50 hover:bg-violet-100 text-violet-700 text-[10px] font-bold rounded-full whitespace-nowrap transition border border-violet-200">
                        ⭐ Mini-Website Upgrade
                    </button>
                @endif
            @endauth
            <button onclick="aiSendQuick('Top wholesale plywood and fittings rates')" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold rounded-full whitespace-nowrap transition">
                💰 Wholesale Rates
            </button>
            <button onclick="aiSendQuick('B2B wholesale quotation estimate kaise banayein?')" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold rounded-full whitespace-nowrap transition">
                📑 Quotation Help
            </button>
        </div>

        <!-- Attached Image Preview Container -->
        <div id="ai-image-preview-bar" class="hidden px-4 py-2 bg-indigo-50 border-t border-indigo-100 flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <img id="ai-preview-img" src="" class="w-8 h-8 rounded-lg object-cover border border-indigo-300">
                <span class="text-[11px] font-bold text-indigo-900 truncate max-w-[200px]" id="ai-preview-name">Photo attached</span>
            </div>
            <button onclick="aiClearImage()" class="text-rose-600 hover:text-rose-800 text-xs font-bold">
                <i class="fa-solid fa-trash"></i> Remove
            </button>
        </div>

        <!-- Input Footer -->
        <div class="p-3 bg-white border-t border-slate-200">
            <form id="ai-chat-form" onsubmit="aiSendMessage(event)" class="flex items-center gap-2">
                <input type="file" id="ai-photo-input" accept="image/*" class="hidden" onchange="aiHandleFileSelect(event)">
                
                <!-- Attach Image Button -->
                <button type="button" onclick="document.getElementById('ai-photo-input').click()" 
                        class="h-9 w-9 rounded-xl bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-600 flex items-center justify-center transition shrink-0" 
                        title="Upload product photo">
                    <i class="fa-solid fa-camera text-sm"></i>
                </button>

                <!-- Voice Dictation Button (Web Speech API) -->
                <button type="button" id="ai-voice-btn" onclick="aiToggleVoiceRecognition()" 
                        class="h-9 w-9 rounded-xl bg-slate-100 hover:bg-emerald-50 hover:text-emerald-600 text-slate-600 flex items-center justify-center transition shrink-0"
                        title="Voice dictation (Hindi / English)">
                    <i class="fa-solid fa-microphone text-sm"></i>
                </button>

                <!-- Text Input -->
                <input type="text" id="ai-text-input" 
                       placeholder="Bolyein ya likhein (e.g. 18mm ply rate 75 list karo)..." 
                       class="flex-1 bg-slate-100 rounded-xl px-3.5 py-2 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 border border-transparent transition">

                <!-- Send Button -->
                <button type="submit" id="ai-send-btn" 
                        class="h-9 w-9 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white flex items-center justify-center transition shrink-0 shadow-xs">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    let attachedImageData = null;
    let isRecording = false;
    let recognition = null;

    function toggleAiChat() {
        const win = document.getElementById('ai-chat-window');
        win.classList.toggle('hidden');
        if (!win.classList.contains('hidden')) {
            document.getElementById('ai-text-input').focus();
        }
    }

    document.querySelector('#vyapar-ai-copilot button').addEventListener('click', toggleAiChat);

    function aiHandleFileSelect(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            attachedImageData = e.target.result;
            document.getElementById('ai-preview-img').src = attachedImageData;
            document.getElementById('ai-preview-name').textContent = file.name;
            document.getElementById('ai-image-preview-bar').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    function aiClearImage() {
        attachedImageData = null;
        document.getElementById('ai-photo-input').value = '';
        document.getElementById('ai-image-preview-bar').classList.add('hidden');
    }

    function aiSendQuick(text) {
        document.getElementById('ai-text-input').value = text;
        aiSendMessage(new Event('submit'));
    }

    async function aiSendMessage(event) {
        if (event) event.preventDefault();
        const input = document.getElementById('ai-text-input');
        const text = input.value.trim();
        if (!text && !attachedImageData) return;

        // Add user bubble
        appendChatBubble('user', text, attachedImageData);
        input.value = '';
        const imgToSend = attachedImageData;
        aiClearImage();

        // Add typing indicator
        const typingId = appendTypingIndicator();

        try {
            const res = await fetch('{{ route("copilot.chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    prompt: text || 'Please inspect this attached product photo and list it.',
                    image_data: imgToSend
                })
            });

            removeTypingIndicator(typingId);
            const data = await res.json();

            if (data.success) {
                appendChatBubble('ai', data.reply, null, data.action, data.action_data);
            } else {
                appendChatBubble('ai', 'Kshama karein, abhi connect nahi ho paya. Kripya dubara koshish karein.');
            }
        } catch (err) {
            removeTypingIndicator(typingId);
            appendChatBubble('ai', 'Network error. Kripya apna internet connection check karein.');
        }
    }

    function appendChatBubble(sender, text, image = null, action = null, actionData = null) {
        const container = document.getElementById('ai-chat-messages');
        const isUser = sender === 'user';
        const bubble = document.createElement('div');
        bubble.className = `flex items-start gap-2.5 max-w-[88%] ${isUser ? 'ml-auto flex-row-reverse' : ''}`;

        let imgHtml = image ? `<img src="${image}" class="w-28 h-28 object-cover rounded-xl mb-2 border border-slate-200 shadow-xs">` : '';
        let actionCardHtml = '';

        if (action === 'inventory_summary' && actionData) {
            actionCardHtml = `
                <div class="mt-3 p-3.5 rounded-2xl bg-slate-900 text-white space-y-3 shadow-md">
                    <div class="flex items-center justify-between border-b border-white/10 pb-2">
                        <span class="text-xs font-black text-white flex items-center gap-1.5">
                            <i class="fa-solid fa-boxes-stacked text-emerald-400"></i> Live Inventory
                        </span>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-extrabold text-[10px] border border-emerald-500/30">
                            ${actionData.total_products} Products
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-[11px]">
                        <div class="bg-white/5 p-2 rounded-xl border border-white/5">
                            <span class="text-slate-400 block text-[9px] uppercase font-bold">Low Stock (≤5)</span>
                            <span class="font-black text-amber-300 text-sm">${actionData.low_stock_count} items</span>
                        </div>
                        <div class="bg-white/5 p-2 rounded-xl border border-white/5">
                            <span class="text-slate-400 block text-[9px] uppercase font-bold">Out of Stock</span>
                            <span class="font-black text-rose-400 text-sm">${actionData.out_of_stock_count} items</span>
                        </div>
                    </div>
                    ${actionData.recent_items && actionData.recent_items.length ? `
                        <div class="space-y-1.5 pt-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Recent Products:</span>
                            ${actionData.recent_items.map(it => `
                                <div class="flex items-center justify-between text-[11px] bg-white/5 p-1.5 px-2.5 rounded-lg">
                                    <span class="font-bold text-white truncate max-w-[170px]">${it.name}</span>
                                    <span class="font-black text-emerald-300">${it.price}</span>
                                </div>
                            `).join('')}
                        </div>
                    ` : ''}
                    <a href="${actionData.inventory_url}" class="block text-center py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-xl text-xs shadow-md transition">
                        📦 Open Inventory Management &rarr;
                    </a>
                </div>
            `;
        } else if (action === 'orders_summary' && actionData) {
            actionCardHtml = `
                <div class="mt-3 p-3.5 rounded-2xl bg-indigo-950 text-white space-y-3 shadow-md border border-indigo-800">
                    <div class="flex items-center justify-between border-b border-white/10 pb-2">
                        <span class="text-xs font-black text-white flex items-center gap-1.5">
                            <i class="fa-solid fa-cart-shopping text-amber-400"></i> Orders & Sales
                        </span>
                        <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-extrabold text-[10px] border border-amber-500/30">
                            ${actionData.pending_orders} Pending
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-[11px]">
                        <div class="bg-white/5 p-2 rounded-xl border border-white/5">
                            <span class="text-indigo-200 block text-[9px] uppercase font-bold">Total Orders</span>
                            <span class="font-black text-white text-sm">${actionData.total_orders} Orders</span>
                        </div>
                        <div class="bg-white/5 p-2 rounded-xl border border-white/5">
                            <span class="text-indigo-200 block text-[9px] uppercase font-bold">Total Sales</span>
                            <span class="font-black text-emerald-300 text-sm">${actionData.total_sales}</span>
                        </div>
                    </div>
                    ${actionData.recent_orders && actionData.recent_orders.length ? `
                        <div class="space-y-1.5 pt-1">
                            <span class="text-[9px] font-bold text-indigo-300 uppercase tracking-wider block">Recent Orders:</span>
                            ${actionData.recent_orders.map(o => `
                                <a href="${o.url}" class="flex items-center justify-between text-[11px] bg-white/5 hover:bg-white/10 p-1.5 px-2.5 rounded-lg transition block">
                                    <div>
                                        <span class="font-bold text-white block">#${o.order_number} - ${o.customer_name}</span>
                                        <span class="text-[9px] text-indigo-300">${o.created_at}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-black text-amber-300 block">${o.total_price}</span>
                                        <span class="text-[8px] uppercase font-extrabold text-emerald-400">${o.status}</span>
                                    </div>
                                </a>
                            `).join('')}
                        </div>
                    ` : ''}
                    <a href="${actionData.orders_url}" class="block text-center py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-xl text-xs shadow-md transition">
                        📑 View All Orders Dashboard &rarr;
                    </a>
                </div>
            `;
        } else if (action === 'seller_products_found' && actionData && actionData.items && actionData.items.length) {
            actionCardHtml = `
                <div class="mt-3 space-y-2">
                    ${actionData.items.map(p => `
                        <div class="p-2.5 rounded-xl bg-white border border-slate-200 shadow-2xs space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-bold text-slate-900 text-xs truncate max-w-[200px]" title="${p.name}">${p.name}</span>
                                <span class="text-[10px] text-slate-500 font-mono">Stock: ${p.stock}</span>
                            </div>
                            <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-100">
                                <div class="flex items-center gap-1">
                                    <span class="text-[10px] text-slate-500 font-bold">Rate: ₹</span>
                                    <input type="number" id="inline_price_${p.id}" value="${p.price}" step="0.5" class="w-16 px-1.5 py-0.5 rounded border border-slate-300 text-xs font-black text-slate-900 bg-slate-50 focus:bg-white">
                                </div>
                                <button type="button" onclick="aiUpdateProductPriceInline(${p.id}, 'inline_price_${p.id}', this)" class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-[10px] rounded-lg transition active:scale-95 shadow-2xs">
                                    Update Rate
                                </button>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        } else if (action === 'price_updated' && actionData) {
            actionCardHtml = `
                <div class="mt-2.5 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs space-y-1.5">
                    <div class="font-black flex items-center gap-1.5 text-emerald-800">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i> Rate Updated Successfully!
                    </div>
                    <div class="text-[11px] font-bold text-slate-800">${actionData.name}</div>
                    <div class="flex items-center gap-2 text-xs font-mono">
                        <span class="line-through text-slate-400">${actionData.old_price}</span>
                        <span>&rarr;</span>
                        <span class="font-black text-emerald-700">${actionData.new_price}</span>
                    </div>
                </div>
            `;
        } else if (action === 'navigation_card' && actionData && actionData.links) {
            actionCardHtml = `
                <div class="mt-3 p-3 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <div class="text-[11px] font-black text-slate-800">${actionData.title}</div>
                    <div class="grid grid-cols-1 gap-1.5">
                        ${actionData.links.map(l => `
                            <a href="${l.url}" class="flex items-center justify-between p-2 rounded-xl bg-white hover:bg-indigo-50 border border-slate-200 hover:border-indigo-300 transition text-xs font-bold text-slate-800 group">
                                <span class="flex items-center gap-2">
                                    <i class="fa-solid ${l.icon} text-indigo-600 group-hover:scale-110 transition"></i>
                                    <span>${l.name}</span>
                                </span>
                                <span class="text-slate-400 group-hover:text-indigo-600">&rarr;</span>
                            </a>
                        `).join('')}
                    </div>
                </div>
            `;
        } else if (action === 'product_draft_created' && actionData) {
            actionCardHtml = `
                <div class="mt-3 p-3 rounded-xl bg-indigo-50 border border-indigo-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase text-indigo-700 bg-white px-2 py-0.5 rounded-full border border-indigo-200">Live Draft Ready</span>
                        <span class="text-xs font-black text-slate-900">₹${actionData.price}</span>
                    </div>
                    <div class="text-[11px] font-bold text-slate-800">${actionData.name}</div>
                    <div class="text-[10px] text-slate-500">Category: ${actionData.category} | Size: ${actionData.size}</div>
                    <button onclick="aiPublishProduct('${encodeURIComponent(JSON.stringify(actionData))}', this)" 
                            class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-[11px] shadow-xs flex items-center justify-center gap-1.5 transition">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Publish to Store Now</span>
                    </button>
                </div>
            `;
        } else if (action === 'upgrade_profile' && actionData) {
            actionCardHtml = `
                <div class="mt-3 p-3 rounded-xl bg-violet-50 border border-violet-200 space-y-2">
                    <div class="text-[11px] font-bold text-violet-900">${actionData.title}</div>
                    <ul class="text-[10px] text-slate-600 space-y-1">
                        ${actionData.benefits.map(b => `<li>✓ ${b}</li>`).join('')}
                    </ul>
                    <a href="${actionData.action_url}" 
                       class="block text-center py-2 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-lg text-[11px] shadow-xs transition">
                        ${actionData.action_label}
                    </a>
                </div>
            `;
        } else if (action === 'catalog_results' && actionData && actionData.items.length) {
            actionCardHtml = `
                <div class="mt-3 space-y-1.5">
                    ${actionData.items.map(it => `
                        <a href="${it.url}" target="_blank" class="block p-2 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 transition">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 text-[11px]">${it.name}</span>
                                <span class="font-black text-emerald-600 text-xs">${it.price}</span>
                            </div>
                            <div class="text-[9px] text-slate-500">${it.seller}</div>
                        </a>
                    `).join('')}
                </div>
            `;
        }

        bubble.innerHTML = `
            <div class="w-7 h-7 rounded-xl ${isUser ? 'bg-slate-900' : 'bg-indigo-600'} text-white flex items-center justify-center text-xs shrink-0 shadow-xs">
                <i class="fa-solid ${isUser ? 'fa-user' : 'fa-sparkles'}"></i>
            </div>
            <div class="${isUser ? 'bg-indigo-600 text-white rounded-tr-xs' : 'bg-white text-slate-800 rounded-tl-xs border border-slate-200'} p-3 rounded-2xl shadow-xs leading-relaxed">
                ${imgHtml}
                <div class="whitespace-pre-wrap">${text}</div>
                ${actionCardHtml}
            </div>
        `;

        container.appendChild(bubble);
        container.scrollTop = container.scrollHeight;
    }

    function appendTypingIndicator() {
        const id = 'typing_' + Date.now();
        const container = document.getElementById('ai-chat-messages');
        const bubble = document.createElement('div');
        bubble.id = id;
        bubble.className = 'flex items-start gap-2.5 max-w-[80%]';
        bubble.innerHTML = `
            <div class="w-7 h-7 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-xs shrink-0 shadow-xs">
                <i class="fa-solid fa-sparkles"></i>
            </div>
            <div class="bg-white p-3 rounded-2xl rounded-tl-xs border border-slate-200 shadow-xs flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-bounce"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-bounce [animation-delay:0.2s]"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-bounce [animation-delay:0.4s]"></span>
            </div>
        `;
        container.appendChild(bubble);
        container.scrollTop = container.scrollHeight;
        return id;
    }

    function removeTypingIndicator(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    async function aiPublishProduct(rawJson, btn) {
        const data = JSON.parse(decodeURIComponent(rawJson));
        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Publishing...`;

        try {
            const res = await fetch('{{ route("copilot.publish_draft") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await res.json();
            if (result.success) {
                btn.className = 'w-full py-2 bg-emerald-600 text-white font-bold rounded-lg text-[11px] shadow-xs flex items-center justify-center gap-1.5';
                btn.innerHTML = `<i class="fa-solid fa-check"></i> Published Live!`;
                appendChatBubble('ai', `🎉 Mubarak ho! **${result.product.name}** aapke live store me publish ho gaya hai! [View Product](${result.product.url})`);
            } else {
                btn.disabled = false;
                btn.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> Error: ${result.message}`;
            }
        } catch (err) {
            btn.disabled = false;
            btn.innerHTML = `Retry Publish`;
        }
    }

    // Voice Dictation using Browser Web Speech API
    function aiToggleVoiceRecognition() {
        const btn = document.getElementById('ai-voice-btn');
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            alert('Aapke browser me speech recognition support nahi hai. Kripya Chrome use karein.');
            return;
        }

        if (isRecording) {
            recognition.stop();
            return;
        }

        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        recognition.lang = 'hi-IN'; // Supports Hindi & Indian English
        recognition.interimResults = false;

        recognition.onstart = function() {
            isRecording = true;
            btn.classList.add('bg-rose-500', 'text-white', 'animate-pulse');
            btn.classList.remove('bg-slate-100', 'text-slate-600');
        };

        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            document.getElementById('ai-text-input').value = transcript;
            aiSendMessage(new Event('submit'));
        };

        recognition.onend = function() {
            isRecording = false;
            btn.classList.remove('bg-rose-500', 'text-white', 'animate-pulse');
            btn.classList.add('bg-slate-100', 'text-slate-600');
        };

        recognition.start();
    }

    async function aiUpdateProductPriceInline(productId, inputId, btn) {
        const input = document.getElementById(inputId);
        const newPrice = parseFloat(input ? input.value : 0);
        if (!newPrice || newPrice <= 0) {
            alert('Kripya valid rate daalein');
            return;
        }

        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        try {
            const res = await fetch('{{ route("copilot.update_price") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: productId, price: newPrice })
            });

            const data = await res.json();
            if (data.success) {
                btn.className = 'px-2.5 py-1 bg-emerald-600 text-white font-black rounded-lg text-[10px]';
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Updated';
                appendChatBubble('ai', `✅ Safaltapoorvak update ho gaya! **${data.product.name}** ka rate ab **₹${data.product.new_price}** set hai.`);
            } else {
                btn.disabled = false;
                btn.innerHTML = origHtml;
                alert(data.message || 'Error updating price');
            }
        } catch(e) {
            btn.disabled = false;
            btn.innerHTML = origHtml;
            alert('Network error while updating price.');
        }
    }
</script>
