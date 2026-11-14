<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>WhatsApp AI Product Listing - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('components.pwa-meta')
    <style>
        /* WhatsApp subtle doodle wallpaper background */
        .wa-bg {
            background-color: #efeae2;
            background-image: radial-gradient(#d1d7db 1px, transparent 1px);
            background-size: 24px 24px;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        @keyframes pulse-emerald {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.12); opacity: 0.85; }
        }
        .animate-mic-pulse {
            animation: pulse-emerald 1.2s infinite ease-in-out;
        }
    </style>
</head>
<body class="wa-bg text-slate-800 antialiased h-screen flex flex-col overflow-hidden font-sans select-none">

    <!-- WhatsApp Top Header -->
    <header class="bg-[#008069] text-white px-3 sm:px-4 py-2.5 flex items-center justify-between shadow-md z-30 shrink-0">
        <div class="flex items-center gap-2 sm:gap-3">
            <a href="{{ route('seller.dashboard') }}" class="w-9 h-9 rounded-full hover:bg-black/15 flex items-center justify-center text-white transition" title="डैशबोर्ड पर वापस जाएं">
                <i class="fa-solid fa-arrow-left text-base"></i>
            </a>
            
            <div class="relative cursor-pointer" onclick="toggleKeySettings()">
                <div class="w-10 h-10 rounded-full bg-emerald-800 border-2 border-white/40 flex items-center justify-center text-white font-black text-base shadow-sm overflow-hidden">
                    <img src="/images/icons/icon-192x192.png" alt="Vyapar AI" class="w-full h-full object-cover">
                </div>
                <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-400 border-2 border-[#008069] rounded-full"></span>
            </div>

            <div>
                <div class="flex items-center gap-1.5">
                    <h1 class="text-sm sm:text-base font-black tracking-tight text-white leading-tight">VyaparIndia AI Assistant</h1>
                    <span class="text-[9px] bg-emerald-400/25 text-emerald-100 font-extrabold px-1.5 py-0.2 rounded-full border border-emerald-300/30">Official</span>
                </div>
                <p class="text-[11px] text-emerald-100/90 leading-tight">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-300 mr-1 animate-pulse"></span>ऑनलाइन • WhatsApp 1-Click Listing
                </p>
            </div>
        </div>

        <div class="flex items-center gap-1 sm:gap-2">
            <a href="{{ route('seller.products.index') }}" class="px-2.5 py-1.5 rounded-xl bg-white/15 hover:bg-white/25 text-white font-extrabold text-[11px] transition flex items-center gap-1.5" title="सभी प्रोडक्ट्स देखें">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span class="hidden sm:inline">स्टोर प्रोडक्ट्स</span>
            </a>
            <button type="button" onclick="toggleKeySettings()" class="w-8 h-8 rounded-full hover:bg-black/15 flex items-center justify-center text-white transition" title="AI विज़न सेटिंग्स">
                <i class="fa-solid fa-gear text-sm"></i>
            </button>
        </div>
    </header>

    <!-- Optional API Key Drawer -->
    <div id="ai-key-drawer" class="hidden bg-slate-900 text-white px-4 py-3 border-b border-emerald-500/30 text-xs shrink-0 transition-all z-20">
        <div class="max-w-2xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-bolt text-amber-400"></i>
                <span class="font-bold">AI विज़न इंजन (Vision Model):</span>
                <span class="text-[10px] text-emerald-400 font-mono" id="wa-key-status">Auto Active (Cloud Mode)</span>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <input type="password" id="wa-custom-key" placeholder="OpenAI / Gemini Key (Optional)" class="flex-1 sm:w-60 px-3 py-1 bg-slate-800 border border-slate-700 rounded-lg text-[11px] text-white">
                <button type="button" onclick="saveCustomApiKey()" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs">Save</button>
            </div>
        </div>
    </div>

    <!-- WhatsApp Chat Messages Scroll Area -->
    <main id="wa-chat-scroll" class="flex-1 overflow-y-auto px-3 sm:px-6 py-4 space-y-3.5">
        
        <!-- Welcome WhatsApp Bubble -->
        <div class="flex items-start gap-2 max-w-[92%] sm:max-w-md">
            <div class="bg-white rounded-2xl rounded-tl-xs p-3.5 shadow-sm text-xs leading-relaxed space-y-2 border border-slate-200">
                <div class="flex items-center gap-1.5 text-emerald-700 font-black text-xs border-b border-slate-100 pb-1.5">
                    <i class="fa-brands fa-whatsapp text-emerald-600 text-base"></i>
                    <span>नमस्ते! मैं आपका VyaparIndia AI स्टोर सहायक हूँ। 🙏</span>
                </div>
                <p class="text-slate-700 text-[11px]">
                    जैसे आप <strong>WhatsApp</strong> पर काम करते हैं, ठीक वैसे ही यहाँ काम होगा! 
                    आपको कोई लंबा फॉर्म भरने की ज़रूरत नहीं है:
                </p>
                <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 space-y-1 text-[11px] text-emerald-950 font-medium">
                    <div class="font-bold flex items-center gap-1 text-emerald-800">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i> काम करने का आसान तरीका:
                    </div>
                    <div>1. 📷 <strong>कैमरा</strong> या 📎 <strong>गैलरी</strong> से 1 से 4 फोटो चुनें (प्रोडक्ट, बॉक्स या रेट लिस्ट)।</div>
                    <div>2. नीचे टाइप करें या 🎙️ <strong>माइक दबाकर बोलें</strong>:</div>
                    <div class="font-mono text-[10px] bg-white p-1.5 rounded-lg border border-emerald-300 text-slate-800">
                        "Florex ka Cistern, 4 piece box packing, MRP 1200, apna selling rate 660"
                    </div>
                    <div>3. AI आपके सामने कार्ड बनाएगा — <strong>'1-Click Live'</strong> दबाते ही माल लाइव!</div>
                </div>
                <div class="text-[9px] text-slate-400 text-right font-mono flex items-center justify-end gap-1">
                    <span>{{ date('h:i A') }}</span>
                    <i class="fa-solid fa-check-double text-blue-500"></i>
                </div>
            </div>
        </div>

        <!-- Quick Ready Templates (Chips) -->
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
            <button type="button" onclick="setQuickSample('Florex Dual Flow Cistern 10L, 4 pcs box, MRP 1200, apna selling rate 660')" class="px-3 py-1.5 bg-white hover:bg-emerald-50 text-slate-700 hover:text-emerald-800 rounded-full border border-slate-300 shadow-2xs text-[11px] font-bold whitespace-nowrap transition flex items-center gap-1 shrink-0">
                <span>🚽 सिस्टर्न उदाहरण</span>
            </button>
            <button type="button" onclick="setQuickSample('Plasto 15mm Brass Bib Cock, 50 pcs pack, MRP 145, 45% less me dal do')" class="px-3 py-1.5 bg-white hover:bg-emerald-50 text-slate-700 hover:text-emerald-800 rounded-full border border-slate-300 shadow-2xs text-[11px] font-bold whitespace-nowrap transition flex items-center gap-1 shrink-0">
                <span>🚰 नलकॉक (Bib Cock)</span>
            </button>
            <button type="button" onclick="setQuickSample('Astral 1 inch CPVC Pipe Class 1, 20 pcs bundle, MRP 480, selling rate 290')" class="px-3 py-1.5 bg-white hover:bg-emerald-50 text-slate-700 hover:text-emerald-800 rounded-full border border-slate-300 shadow-2xs text-[11px] font-bold whitespace-nowrap transition flex items-center gap-1 shrink-0">
                <span>⚡ Astral CPVC पाइप</span>
            </button>
            <button type="button" onclick="setQuickSample('Florex Soft Close Commode Seat Cover, 10 pcs carton, MRP 850, 45% discount')" class="px-3 py-1.5 bg-white hover:bg-emerald-50 text-slate-700 hover:text-emerald-800 rounded-full border border-slate-300 shadow-2xs text-[11px] font-bold whitespace-nowrap transition flex items-center gap-1 shrink-0">
                <span>🚽 सीट कवर</span>
            </button>
        </div>

        <!-- Dynamic Conversation & Draft Cards will appear here -->
        <div id="conversation-container" class="space-y-3.5"></div>

    </main>

    <!-- Pre-Send Image Tray (Thumbnails of selected photos) -->
    <div id="image-staging-tray" class="hidden bg-slate-800/95 backdrop-blur-md px-4 py-2 border-t border-slate-700 text-white shrink-0">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[11px] font-bold text-emerald-400 flex items-center gap-1.5">
                <i class="fa-solid fa-images"></i>
                <span id="staging-count-text">0 फ़ोटो चुनी गईं</span>
            </span>
            <button type="button" onclick="clearAllStagedImages()" class="text-[10px] text-rose-300 hover:text-rose-100 font-bold flex items-center gap-1">
                <i class="fa-solid fa-trash-can"></i> सब हटाएं
            </button>
        </div>
        <div id="staging-thumbnails" class="flex items-center gap-2.5 overflow-x-auto no-scrollbar py-1">
            <!-- Dynamic Image Chips -->
        </div>
    </div>

    <!-- WhatsApp Bottom Input Bar -->
    <footer class="bg-[#f0f2f5] px-2 sm:px-4 py-2.5 border-t border-slate-300 shrink-0">
        <form id="wa-chat-form" onsubmit="handleFormSubmit(event)" class="max-w-4xl mx-auto flex items-end gap-1.5 sm:gap-2">
            
            <!-- Hidden File Inputs -->
            <input type="file" id="camera-file-input" accept="image/*" capture="environment" class="hidden" onchange="handleFileSelect(event)">
            <input type="file" id="gallery-file-input" accept="image/*" multiple class="hidden" onchange="handleFileSelect(event)">

            <!-- Attach Menu Buttons -->
            <div class="flex items-center gap-1 shrink-0">
                <button type="button" onclick="document.getElementById('camera-file-input').click()" 
                        class="w-10 h-10 rounded-full bg-white hover:bg-slate-200 text-slate-600 flex items-center justify-center transition shadow-2xs" 
                        title="कैमरा से फोटो खींचें">
                    <i class="fa-solid fa-camera text-base text-[#54656f]"></i>
                </button>

                <button type="button" onclick="document.getElementById('gallery-file-input').click()" 
                        class="w-10 h-10 rounded-full bg-white hover:bg-slate-200 text-slate-600 flex items-center justify-center transition shadow-2xs" 
                        title="गैलरी से 2-3 फोटो चुनें">
                    <i class="fa-solid fa-paperclip text-base text-[#54656f]"></i>
                </button>
            </div>

            <!-- Text Input Container -->
            <div class="flex-1 bg-white rounded-2xl px-3.5 py-2 shadow-2xs flex items-center gap-2 border border-slate-200 focus-within:border-emerald-500 transition">
                <textarea id="wa-text-input" rows="1" 
                          placeholder="बोलें या लिखें (जैसे: Florex Cistern MRP 1200 selling 660)..." 
                          class="w-full resize-none bg-transparent text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none max-h-24 no-scrollbar"
                          oninput="autoResizeTextarea(this)"></textarea>
            </div>

            <!-- Voice Dictation (Mic) Button -->
            <button type="button" id="wa-mic-btn" onclick="toggleVoiceRecording()" 
                    class="w-10 h-10 rounded-full bg-white hover:bg-emerald-50 text-[#008069] flex items-center justify-center transition shadow-2xs shrink-0" 
                    title="बोलकर टाइप करें (Hindi / English)">
                <i class="fa-solid fa-microphone text-lg"></i>
            </button>

            <!-- Send Button -->
            <button type="submit" id="wa-send-btn" 
                    class="w-11 h-11 rounded-full bg-[#008069] hover:bg-[#00705b] active:scale-95 text-white flex items-center justify-center transition shadow-md shrink-0 cursor-pointer">
                <i class="fa-solid fa-paper-plane text-sm"></i>
            </button>
        </form>
    </footer>

    <!-- Scripts -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let stagedImages = []; // Array of { name, base64 }
        let isRecording = false;
        let speechRecognizer = null;

        // Auto resize textarea
        function autoResizeTextarea(el) {
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 96) + 'px';
        }

        // Toggle Key Settings
        function toggleKeySettings() {
            const drawer = document.getElementById('ai-key-drawer');
            drawer.classList.toggle('hidden');
            const saved = localStorage.getItem('vyapar_openai_api_key') || localStorage.getItem('vyapar_gemini_api_key');
            if (saved) document.getElementById('wa-custom-key').value = saved;
        }

        function saveCustomApiKey() {
            const val = document.getElementById('wa-custom-key').value.trim();
            if (val) {
                localStorage.setItem('vyapar_custom_api_key', val);
                document.getElementById('wa-key-status').textContent = 'Key Saved ✓';
                setTimeout(() => document.getElementById('ai-key-drawer').classList.add('hidden'), 700);
            } else {
                localStorage.removeItem('vyapar_custom_api_key');
                document.getElementById('wa-key-status').textContent = 'Auto Active (Cloud Mode)';
            }
        }

        // Quick Sample Filler
        function setQuickSample(txt) {
            const input = document.getElementById('wa-text-input');
            input.value = txt;
            input.focus();
            autoResizeTextarea(input);
        }

        // Handle File Selection (Single or Multiple)
        function handleFileSelect(e) {
            const files = Array.from(e.target.files);
            if (!files.length) return;

            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    stagedImages.push({
                        name: file.name,
                        base64: evt.target.result
                    });
                    renderStagedImages();
                };
                reader.readAsDataURL(file);
            });

            e.target.value = ''; // Reset input
        }

        // Render Staged Images Tray
        function renderStagedImages() {
            const tray = document.getElementById('image-staging-tray');
            const container = document.getElementById('staging-thumbnails');
            const countText = document.getElementById('staging-count-text');

            if (!stagedImages.length) {
                tray.classList.add('hidden');
                container.innerHTML = '';
                return;
            }

            tray.classList.remove('hidden');
            countText.textContent = `📷 ${stagedImages.length} फ़ोटो चुनी गईं (भेजने के लिए तैयार)`;

            container.innerHTML = stagedImages.map((img, idx) => `
                <div class="relative shrink-0 w-14 h-14 rounded-xl overflow-hidden border-2 border-emerald-400 shadow-sm group">
                    <img src="${img.base64}" class="w-full h-full object-cover">
                    <button type="button" onclick="removeStagedImage(${idx})" class="absolute top-0.5 right-0.5 w-4 h-4 rounded-full bg-rose-600 text-white flex items-center justify-center text-[9px] shadow-sm hover:scale-110">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            `).join('');
        }

        function removeStagedImage(idx) {
            stagedImages.splice(idx, 1);
            renderStagedImages();
        }

        function clearAllStagedImages() {
            stagedImages = [];
            renderStagedImages();
        }

        // Voice Recording via Web Speech API (Hindi / Indian English)
        function toggleVoiceRecording() {
            const micBtn = document.getElementById('wa-mic-btn');
            if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
                alert('आपके ब्राउज़र में वॉइस टाइपिंग सपोर्ट नहीं है। कृपया Google Chrome का उपयोग करें।');
                return;
            }

            if (isRecording) {
                if (speechRecognizer) speechRecognizer.stop();
                return;
            }

            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            speechRecognizer = new SpeechRecognition();
            speechRecognizer.lang = 'hi-IN'; // Hindi & Indian English
            speechRecognizer.interimResults = false;
            speechRecognizer.maxAlternatives = 1;

            speechRecognizer.onstart = function() {
                isRecording = true;
                micBtn.classList.remove('bg-white', 'text-[#008069]');
                micBtn.classList.add('bg-rose-500', 'text-white', 'animate-mic-pulse');
            };

            speechRecognizer.onresult = function(event) {
                const transcript = event.results[0][0].transcript;
                const input = document.getElementById('wa-text-input');
                input.value = (input.value ? input.value + ' ' : '') + transcript;
                autoResizeTextarea(input);
            };

            speechRecognizer.onerror = function() {
                isRecording = false;
                resetMicBtn();
            };

            speechRecognizer.onend = function() {
                isRecording = false;
                resetMicBtn();
            };

            speechRecognizer.start();
        }

        function resetMicBtn() {
            const micBtn = document.getElementById('wa-mic-btn');
            micBtn.classList.remove('bg-rose-500', 'text-white', 'animate-mic-pulse');
            micBtn.classList.add('bg-white', 'text-[#008069]');
        }

        // Handle Form Submit
        async function handleFormSubmit(e) {
            if (e) e.preventDefault();
            const input = document.getElementById('wa-text-input');
            const message = input.value.trim();
            const imagesToSend = [...stagedImages];

            if (!message && !imagesToSend.length) return;

            // 1. Render User Message in Chat
            renderUserMessage(message, imagesToSend);

            // Clear inputs
            input.value = '';
            autoResizeTextarea(input);
            clearAllStagedImages();

            // 2. Render Typing Indicator
            const typingId = renderTypingIndicator();
            scrollToBottom();

            // 3. Send to Server
            try {
                const res = await fetch('{{ route("seller.whatsapp_listing.chat") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        message: message,
                        images: imagesToSend.map(img => img.base64),
                        api_key: localStorage.getItem('vyapar_custom_api_key') || ''
                    })
                });

                removeElement(typingId);
                const data = await res.json();

                if (data.success && data.draft) {
                    renderAiDraftCard(data.reply, data.draft);
                } else {
                    renderAiMessage(data.message || 'क्षमा करें, AI प्रोसेस नहीं कर पाया। कृपया दोबारा प्रयास करें।');
                }
            } catch (err) {
                removeElement(typingId);
                renderAiMessage('नेटवर्क एरर। कृपया अपना इंटरनेट कनेक्शन चेक करें।');
            }

            scrollToBottom();
        }

        // Render User Chat Bubble
        function renderUserMessage(text, images) {
            const container = document.getElementById('conversation-container');
            const bubble = document.createElement('div');
            bubble.className = 'flex items-start justify-end gap-2 max-w-[92%] sm:max-w-md ml-auto';

            let imgHtml = '';
            if (images.length === 1) {
                imgHtml = `<div class="rounded-xl overflow-hidden mb-1.5 border border-black/10"><img src="${images[0].base64}" class="w-full max-h-56 object-cover"></div>`;
            } else if (images.length > 1) {
                imgHtml = `
                    <div class="grid grid-cols-2 gap-1 rounded-xl overflow-hidden mb-1.5 border border-black/10">
                        ${images.map(img => `<img src="${img.base64}" class="w-full h-24 object-cover">`).join('')}
                    </div>
                `;
            }

            const timeStr = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            bubble.innerHTML = `
                <div class="bg-[#d9fdd3] text-slate-800 rounded-2xl rounded-tr-xs p-3 shadow-sm text-xs leading-relaxed border border-emerald-200">
                    ${imgHtml}
                    ${text ? `<div class="whitespace-pre-wrap font-medium">${escapeHtml(text)}</div>` : ''}
                    <div class="text-[9px] text-slate-500 text-right font-mono flex items-center justify-end gap-1 mt-1">
                        <span>${timeStr}</span>
                        <i class="fa-solid fa-check-double text-blue-500"></i>
                    </div>
                </div>
            `;
            container.appendChild(bubble);
        }

        // Render AI Message
        function renderAiMessage(text) {
            const container = document.getElementById('conversation-container');
            const bubble = document.createElement('div');
            bubble.className = 'flex items-start gap-2 max-w-[92%] sm:max-w-md';
            const timeStr = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            bubble.innerHTML = `
                <div class="bg-white rounded-2xl rounded-tl-xs p-3.5 shadow-sm text-xs leading-relaxed space-y-1.5 border border-slate-200">
                    <div class="text-slate-800 whitespace-pre-wrap">${escapeHtml(text)}</div>
                    <div class="text-[9px] text-slate-400 text-right font-mono">${timeStr}</div>
                </div>
            `;
            container.appendChild(bubble);
        }

        // Render Interactive WhatsApp Product Draft Card
        function renderAiDraftCard(replyText, draft) {
            const container = document.getElementById('conversation-container');
            const bubble = document.createElement('div');
            bubble.className = 'flex items-start gap-2 max-w-[95%] sm:max-w-lg';
            const timeStr = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            const draftId = 'draft_' + Date.now();
            const draftJson = encodeURIComponent(JSON.stringify(draft));

            let photosHtml = '';
            if (draft.image_url) {
                photosHtml = `
                    <div class="relative rounded-xl overflow-hidden bg-slate-100 border border-slate-200 mb-2">
                        <img src="${draft.image_url}" class="w-full h-44 object-contain bg-white">
                        ${draft.brand ? `<span class="absolute top-2 left-2 px-2.5 py-0.5 rounded-full bg-slate-900/80 backdrop-blur-md text-white font-black text-[10px] tracking-wide">${draft.brand}</span>` : ''}
                        ${draft.discount_percent > 0 ? `<span class="absolute top-2 right-2 px-2.5 py-0.5 rounded-full bg-rose-600 text-white font-black text-[10px] shadow-sm">${draft.discount_percent}% छूट (OFF)</span>` : ''}
                    </div>
                `;
            }

            bubble.innerHTML = `
                <div class="bg-white rounded-2xl rounded-tl-xs p-3.5 shadow-md border border-emerald-300 space-y-2.5 text-xs w-full" id="${draftId}">
                    
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <span class="text-[11px] font-black text-emerald-700 flex items-center gap-1.5">
                            <i class="fa-solid fa-sparkles text-amber-500"></i> AI प्रोडक्ट ड्राफ्ट तैयार है
                        </span>
                        <span class="text-[9px] bg-emerald-50 text-emerald-800 font-extrabold px-2 py-0.5 rounded-full border border-emerald-200">
                            1-Click Live
                        </span>
                    </div>

                    ${photosHtml}

                    <div>
                        <h3 class="text-sm font-black text-slate-900 leading-snug">${escapeHtml(draft.name)}</h3>
                        <div class="flex flex-wrap items-center gap-2 mt-1 text-[11px] text-slate-500">
                            <span class="bg-slate-100 px-2 py-0.5 rounded-md font-bold text-slate-700">📂 ${draft.category}</span>
                            <span class="bg-slate-100 px-2 py-0.5 rounded-md font-bold text-slate-700">📏 ${draft.size}</span>
                            <span class="bg-slate-100 px-2 py-0.5 rounded-md font-bold text-slate-700">📦 ${draft.packaging}</span>
                        </div>
                    </div>

                    <!-- Pricing Highlights Pill -->
                    <div class="p-2.5 rounded-xl bg-gradient-to-r from-emerald-50 via-teal-50 to-green-50 border border-emerald-200 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-500 block">MRP (अधिकतम मूल्य)</span>
                            <span class="line-through text-xs font-bold text-slate-400">₹${Number(draft.mrp).toLocaleString('en-IN')}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-emerald-700 font-black block">आपका बिक्री भाव (Selling Rate)</span>
                            <span class="text-base font-black text-emerald-700 font-mono">₹${Number(draft.price).toLocaleString('en-IN')}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-2 pt-1">
                        <button type="button" onclick="publishDraftLive('${draftJson}', '${draftId}')" 
                                class="w-full py-2.5 bg-[#008069] hover:bg-[#00705b] active:scale-98 text-white font-black rounded-xl text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-cloud-arrow-up text-sm"></i>
                            <span>🟢 1-Click Live (दुकान व बाज़ार पर लाइव करें)</span>
                        </button>

                        <button type="button" onclick="openInlineEdit('${draftId}', '${draftJson}')" 
                                class="w-full py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-[11px] transition flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-pen-to-square text-xs text-slate-500"></i>
                            <span>✏️ भाव या नाम बदलें (Edit)</span>
                        </button>
                    </div>

                    <div class="text-[9px] text-slate-400 text-right font-mono pt-1">${timeStr}</div>
                </div>
            `;
            container.appendChild(bubble);
        }

        // Instant Publish Draft
        async function publishDraftLive(encodedJson, cardId) {
            const data = JSON.parse(decodeURIComponent(encodedJson));
            const card = document.getElementById(cardId);
            const btn = card.querySelector('button');

            btn.disabled = true;
            btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> लाइव किया जा रहा है...`;

            try {
                const res = await fetch('{{ route("seller.whatsapp_listing.publish") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await res.json();
                if (result.success) {
                    card.innerHTML = `
                        <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-300 text-emerald-900 space-y-2">
                            <div class="font-black text-sm text-emerald-800 flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                                <span>बधाई हो! प्रोडक्ट लाइव हो चुका है! 🎉</span>
                            </div>
                            <div class="text-xs font-bold text-slate-800">${escapeHtml(result.product.name)}</div>
                            <div class="text-xs text-slate-600">बिक्री दर: <strong class="text-emerald-700 font-black">₹${result.product.price}</strong></div>
                            <div class="pt-2 flex items-center gap-2">
                                <a href="${result.product.url}" target="_blank" class="flex-1 py-2 text-center bg-[#008069] text-white font-black rounded-lg text-xs shadow-sm hover:bg-[#00705b] transition">
                                    🔗 स्टोर पर लाइव देखें &rarr;
                                </a>
                                <a href="{{ route('seller.products.index') }}" class="py-2 px-3 bg-white border border-slate-300 text-slate-700 font-bold rounded-lg text-xs hover:bg-slate-50 transition">
                                    कैटलॉग
                                </a>
                            </div>
                        </div>
                    `;
                } else {
                    btn.disabled = false;
                    btn.innerHTML = `त्रुटि: ${result.message || 'पब्लिश नहीं हो पाया'}`;
                }
            } catch (err) {
                btn.disabled = false;
                btn.innerHTML = `दोबारा प्रयास करें`;
            }
        }

        // Inline Quick Edit
        function openInlineEdit(cardId, encodedJson) {
            const data = JSON.parse(decodeURIComponent(encodedJson));
            const card = document.getElementById(cardId);

            card.innerHTML = `
                <div class="space-y-3">
                    <div class="text-xs font-black text-slate-900 border-b border-slate-100 pb-1 flex items-center justify-between">
                        <span>✏️ प्रोडक्ट विवरण एडिट करें</span>
                        <button type="button" onclick="cancelEdit('${cardId}', '${encodedJson}')" class="text-slate-400 hover:text-slate-600 text-xs">✕</button>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-slate-600 block">नाम (Product Title):</label>
                        <input type="text" id="edit_name_${cardId}" value="${escapeHtml(data.name)}" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs font-bold text-slate-900">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-[10px] font-bold text-slate-600 block">MRP (₹):</label>
                            <input type="number" id="edit_mrp_${cardId}" value="${data.mrp}" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs font-bold text-slate-900">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-emerald-700 block">बिक्री दर (₹ Selling):</label>
                            <input type="number" id="edit_price_${cardId}" value="${data.price}" class="w-full px-2.5 py-1.5 rounded-lg border border-emerald-400 bg-emerald-50/50 text-xs font-black text-emerald-900">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-[10px] font-bold text-slate-600 block">साइज़ (Size):</label>
                            <input type="text" id="edit_size_${cardId}" value="${escapeHtml(data.size || '')}" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs font-bold text-slate-900">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-600 block">पैकिंग (Packaging):</label>
                            <input type="text" id="edit_pkg_${cardId}" value="${escapeHtml(data.packaging || '')}" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs font-bold text-slate-900">
                        </div>
                    </div>

                    <button type="button" onclick="saveInlineEdit('${cardId}', '${encodedJson}')" 
                            class="w-full py-2 bg-[#008069] text-white font-black rounded-xl text-xs shadow-sm hover:bg-[#00705b] transition">
                        ✓ सेव करें और लाइव बटन लाएं
                    </button>
                </div>
            `;
        }

        function cancelEdit(cardId, encodedJson) {
            const data = JSON.parse(decodeURIComponent(encodedJson));
            const card = document.getElementById(cardId);
            card.remove();
            renderAiDraftCard("एडिट रद्द किया गया", data);
        }

        function saveInlineEdit(cardId, encodedJson) {
            const data = JSON.parse(decodeURIComponent(encodedJson));
            data.name = document.getElementById(`edit_name_${cardId}`).value.trim();
            data.mrp = parseFloat(document.getElementById(`edit_mrp_${cardId}`).value) || data.mrp;
            data.price = parseFloat(document.getElementById(`edit_price_${cardId}`).value) || data.price;
            data.size = document.getElementById(`edit_size_${cardId}`).value.trim();
            data.packaging = document.getElementById(`edit_pkg_${cardId}`).value.trim();
            if (data.mrp > data.price) {
                data.discount_percent = Math.round(((data.mrp - data.price) / data.mrp) * 100);
            }

            const card = document.getElementById(cardId);
            card.remove();
            renderAiDraftCard("विवरण अपडेट हो गया है! अब लाइव करें:", data);
        }

        // Typing Indicator
        function renderTypingIndicator() {
            const id = 'typing_' + Date.now();
            const container = document.getElementById('conversation-container');
            const el = document.createElement('div');
            el.id = id;
            el.className = 'flex items-center gap-1.5 bg-white p-3 rounded-2xl rounded-tl-xs shadow-xs border border-slate-200 w-20';
            el.innerHTML = `
                <span class="w-2 h-2 rounded-full bg-[#008069] animate-bounce"></span>
                <span class="w-2 h-2 rounded-full bg-[#008069] animate-bounce [animation-delay:0.2s]"></span>
                <span class="w-2 h-2 rounded-full bg-[#008069] animate-bounce [animation-delay:0.4s]"></span>
            `;
            container.appendChild(el);
            return id;
        }

        function removeElement(id) {
            const el = document.getElementById(id);
            if (el) el.remove();
        }

        function scrollToBottom() {
            const scroller = document.getElementById('wa-chat-scroll');
            scroller.scrollTop = scroller.scrollHeight;
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>"']/g, function(m) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
            });
        }
    </script>
</body>
</html>
