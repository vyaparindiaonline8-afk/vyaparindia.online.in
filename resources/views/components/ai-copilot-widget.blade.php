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
                    <button onclick="aiSendQuick('Naya product photo se list karo')" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[10px] font-bold rounded-full whitespace-nowrap transition border border-indigo-200">
                        📦 Naya Product List Karo
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

        if (action === 'product_draft_created' && actionData) {
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
</script>
