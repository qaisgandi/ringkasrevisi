@php
    $currentPublication = $publication ?? null;
    $hasCurrentPublication = !empty($currentPublication) && isset($currentPublication->id);
@endphp

<!-- FLOATING CHATBOT WIDGET -->
<div id="bpsChatbotRoot" 
     data-publication-id="{{ $hasCurrentPublication ? $currentPublication->id : '' }}"
     data-publication-title="{{ $hasCurrentPublication ? e($currentPublication->title) : '' }}"
     style="position:fixed;bottom:1.5rem;right:1.5rem;top:auto;left:auto;z-index:50;"
     class="fixed bottom-6 right-6 z-50 flex flex-col items-end font-sans select-none print:hidden">

    <!-- FLOATING CHAT PANEL (HIDDEN BY DEFAULT) -->
    <div id="chatbotWindow" role="region" aria-label="Asisten tanya jawab RINGKAS" 
         style="height:min(520px,calc(100dvh - 11rem));max-height:calc(100dvh - 11rem);width:380px;max-width:calc(100vw - 3rem);"
         class="hidden flex-col w-[380px] max-w-[calc(100vw-3rem)] h-[min(520px,calc(100dvh-11rem))] max-h-[calc(100dvh-11rem)] bg-white rounded-2xl shadow-2xl border border-slate-200/80 overflow-hidden transition-all duration-300 transform scale-95 opacity-0 origin-bottom-right mb-3">
        
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-bps-navy via-slate-900 to-bps-lightBlue text-white p-4 sm:px-5 flex items-center justify-between shrink-0 shadow-sm relative overflow-hidden">
            <!-- Background Glow Effect -->
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-cyan-400/20 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex items-center gap-3 relative z-10">
                <div class="relative flex items-center justify-center w-10 h-10 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 shadow-inner">
                    <i data-lucide="sparkles" class="w-5 h-5 text-cyan-300 animate-pulse"></i>
                    <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-400 border-2 border-slate-900 rounded-full"></span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-sm sm:text-base leading-tight tracking-tight text-white heading-font">Tanya BPS AI</h3>
                        <span class="px-2 py-0.5 text-[9px] font-extrabold uppercase bg-cyan-500/20 text-cyan-300 border border-cyan-400/30 rounded-full tracking-wider">Gemini</span>
                    </div>
                    <p class="text-[11px] text-slate-300/90 font-medium mt-0.5 flex items-center gap-1.5" id="chatbotModeBadge">
                        @if($hasCurrentPublication)
                            <i data-lucide="book-open" class="w-3 h-3 text-amber-300"></i>
                            <span class="truncate max-w-[210px] text-amber-200 font-semibold" title="{{ $currentPublication->title }}">
                                {{ $currentPublication->title }}
                            </span>
                        @else
                            <i data-lucide="globe" class="w-3 h-3 text-cyan-300"></i>
                            <span>Asisten Universal BPS</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Controls (Clear & Close) -->
            <div class="flex items-center gap-1 relative z-10">
                <button type="button" 
                        id="btnClearChat" 
                        title="Hapus percakapan & mulai ulang"
                        class="p-2 text-slate-300 hover:text-white hover:bg-white/10 rounded-xl transition-colors cursor-pointer">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </button>
                <button type="button" 
                        id="btnCloseChat" 
                        title="Tutup jendela chat"
                        class="p-2 text-slate-300 hover:text-white hover:bg-white/10 rounded-xl transition-colors cursor-pointer">
                    <i data-lucide="x" class="w-4.5 h-4.5"></i>
                </button>
            </div>
        </div>

        <!-- MODE BANNER NOTIFICATION -->
        <div id="chatbotContextBanner" class="bg-gradient-to-r from-blue-50 to-indigo-50/60 border-b border-blue-100/80 px-4 py-2 flex items-center justify-between text-xs text-slate-600 shrink-0">
            <div class="flex items-center gap-2 truncate">
                <span class="w-2 h-2 rounded-full {{ $hasCurrentPublication ? 'bg-amber-500' : 'bg-blue-500' }} shrink-0"></span>
                <span class="truncate text-[11px] font-medium" id="chatbotContextText">
                    @if($hasCurrentPublication)
                        Sedang membahas dokumen: <strong class="text-bps-navy">{{ Str::limit($currentPublication->title, 35) }}</strong>
                    @else
                        Siap menjawab & mencari data di seluruh katalog publikasi BPS
                    @endif
                </span>
            </div>
            @if($hasCurrentPublication)
                <button type="button" id="btnSwitchToUniversal" class="text-[10px] font-bold text-blue-600 hover:text-blue-800 underline ml-2 shrink-0 cursor-pointer">
                    Cari Global
                </button>
            @endif
        </div>

        <!-- CHAT MESSAGES CONTAINER -->
        <div id="chatbotMessages" role="log" aria-live="polite" aria-relevant="additions" style="flex:1 1 auto;min-height:0;overflow-y:auto;overflow-x:hidden;overscroll-behavior:contain;scrollbar-gutter:stable;-webkit-overflow-scrolling:touch;touch-action:pan-y;" class="flex-1 min-h-0 overflow-y-auto overscroll-contain p-4 space-y-4 bg-slate-50/50 scroll-smooth">
            
            <!-- Bot Greeting Message -->
            <div class="flex items-start gap-2.5 message-bot animate-fadeIn">
                <div class="w-8 h-8 rounded-full bg-bps-navy text-cyan-300 flex items-center justify-center shrink-0 shadow-sm border border-slate-200">
                    <i data-lucide="bot" class="w-4 h-4"></i>
                </div>
                <div class="w-full max-w-[85%] min-w-0 space-y-1">
                    <div class="w-full min-w-0 break-words bg-white border border-slate-200/90 rounded-2xl rounded-tl-sm px-4 py-3.5 text-xs text-slate-800 leading-[1.7] shadow-sm chatbot-prose">
                        <p class="font-semibold text-bps-navy mb-1.5 flex items-center gap-1.5">
                            <span>Halo! Saya Asisten BPS</span>
                        </p>
                        @if($hasCurrentPublication)
                            <p>Saya siap membantu Anda membedah dan menjawab pertanyaan seputar isi buku <strong>"{{ $currentPublication->title }}"</strong>.</p>
                            <p class="mt-1.5 text-slate-500">Anda juga dapat menanyakan data statistik lainnya yang ada di seluruh katalog BPS.</p>
                        @else
                            <p>Ada yang bisa saya bantu? Anda dapat menanyakan letak data tertentu (seperti <em>"Ada di publikasi mana angka IPM?"</em>), rekomendasi buku publikasi, indikator statistik, atau definisi konsep BPS.</p>
                        @endif
                    </div>
                    <span class="text-[10px] text-slate-400 font-medium pl-1">Baru saja</span>
                </div>
            </div>

            <!-- Quick Suggestions Chips (Rendered Dynamically or Default) -->
            <div id="quickSuggestionsContainer" class="pt-2">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-1">
                    <i data-lucide="lightbulb" class="w-3.5 h-3.5 text-amber-500"></i> Saran Pertanyaan Cepat:
                </p>
                <div class="flex flex-wrap gap-1.5" id="quickChipsList">
                    @if($hasCurrentPublication)
                        <button type="button" class="quick-chip bg-white hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 text-slate-700 text-xs font-medium px-3 py-1.5 rounded-xl border border-slate-200 shadow-2xs transition-all text-left">
                            Apa kesimpulan utama dokumen ini?
                        </button>
                        <button type="button" class="quick-chip bg-white hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 text-slate-700 text-xs font-medium px-3 py-1.5 rounded-xl border border-slate-200 shadow-2xs transition-all text-left">
                            📈 Apa saja indikator statistik penting di buku ini?
                        </button>
                        <button type="button" class="quick-chip bg-white hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 text-slate-700 text-xs font-medium px-3 py-1.5 rounded-xl border border-slate-200 shadow-2xs transition-all text-left">
                            Jelaskan tren data yang ditemukan
                        </button>
                    @else
                        <button type="button" class="quick-chip bg-white hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 text-slate-700 text-xs font-medium px-3 py-1.5 rounded-xl border border-slate-200 shadow-2xs transition-all text-left">
                            Ada di publikasi mana data tingkat IPM terbaru?
                        </button>
                        <button type="button" class="quick-chip bg-white hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 text-slate-700 text-xs font-medium px-3 py-1.5 rounded-xl border border-slate-200 shadow-2xs transition-all text-left">
                            Di mana saya bisa melihat data kemiskinan & inflasi?
                        </button>
                        <button type="button" class="quick-chip bg-white hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 text-slate-700 text-xs font-medium px-3 py-1.5 rounded-xl border border-slate-200 shadow-2xs transition-all text-left">
                            Publikasi apa saja yang tersedia untuk tahun 2024?
                        </button>
                    @endif
                </div>
            </div>

            <!-- Typing Indicator (Hidden by default) -->
            <div id="typingIndicator" class="hidden items-start gap-2.5 animate-fadeIn w-full">
                <div class="w-8 h-8 rounded-full bg-bps-navy text-cyan-300 flex items-center justify-center shrink-0 shadow-sm border border-slate-200">
                    <i data-lucide="bot" class="w-4 h-4"></i>
                </div>
                <div class="w-full max-w-[85%] min-w-0 bg-white border border-slate-200 rounded-2xl rounded-tl-sm px-4 py-3.5 shadow-sm flex items-center gap-1.5 min-w-[120px]">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-bounce" style="animation-delay: 0ms"></span>
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-bounce" style="animation-delay: 150ms"></span>
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-bounce" style="animation-delay: 300ms"></span>
                </div>
            </div>

        </div>

        <!-- FOOTER / INPUT AREA -->
        <div class="p-3 bg-white border-t border-slate-200 shrink-0">
            <form id="chatbotForm" class="flex items-end gap-2">
                <div class="relative flex-1 bg-slate-50 border border-slate-200 rounded-2xl focus-within:border-blue-500 focus-within:bg-white focus-within:ring-2 focus-within:ring-blue-100 transition-all">
                    <textarea 
                        id="chatbotInput" aria-label="Pertanyaan tentang publikasi" 
                        rows="1"
                        placeholder="Tanya data statistik, letak publikasi, atau isi buku..." 
                        class="w-full bg-transparent px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none resize-none max-h-24 leading-relaxed"></textarea>
                </div>
                <button 
                    type="submit" 
                    id="btnSendChat"
                    title="Kirim pesan"
                    class="h-10 w-10 flex items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-md shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all shrink-0 cursor-pointer active:scale-95">
                    <i data-lucide="send" class="w-4 h-4"></i>
                </button>
            </form>
            <div class="flex items-center justify-between mt-2 px-1 text-[10px] text-slate-400 font-medium">
                <span>Didukung oleh Google Gemini AI</span>
                <span class="hidden sm:inline">Tekan Enter untuk kirim</span>
            </div>
        </div>

    </div>

    <!-- FLOATING ACTION BUTTON (TOGGLE) -->
    <button type="button" 
            id="btnToggleChatbot" aria-controls="chatbotWindow" aria-expanded="false" 
            aria-label="Buka Chatbot AI BPS"
            class="group relative flex items-center gap-2.5 px-4 py-3.5 bg-bps-navy text-white rounded-lg shadow-md border border-white/20 cursor-pointer">
        
        <i data-lucide="message-circle" class="w-5 h-5 text-white" aria-hidden="true"></i>

        <div class="flex flex-col items-start pr-1">
            <span class="text-xs font-extrabold tracking-tight leading-none text-white flex items-center gap-1">
                Tanya BPS AI
            </span>
            <span class="text-[11px] text-slate-200 font-semibold tracking-wider uppercase mt-0.5">
                {{ $hasCurrentPublication ? 'Mode Dokumen' : 'Katalog Universal' }}
            </span>
        </div>

        <i data-lucide="chevron-up" id="chatbotFabArrow" class="w-4 h-4 text-slate-300 group-hover:text-white transition-transform duration-300"></i>
    </button>

</div>

<!-- STYLES & SCRIPT -->
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.25s ease-out forwards;
    }
    .chatbot-answer-scroll {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
        scrollbar-gutter: stable;
        padding-right: 6px;
        scroll-padding: 8px;
    }
    #chatbotMessages .message-bot,
    #chatbotMessages .message-user {
        width: 100%;
        min-width: 0;
    }
    #chatbotMessages .message-bot > div,
    #chatbotMessages .message-user > div {
        min-width: 0;
    }
    .chatbot-prose {
        overflow-wrap: anywhere;
        word-break: break-word;
    }
    .chatbot-answer-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .chatbot-answer-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .chatbot-answer-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    .chatbot-prose > *:first-child {
        margin-top: 0;
    }
    .chatbot-prose > *:last-child {
        margin-bottom: 0;
    }
    .chatbot-prose p {
        margin: 0 0 0.6rem;
        line-height: 1.7;
    }
    .chatbot-prose p:last-child {
        margin-bottom: 0;
    }
    .chatbot-prose strong {
        color: #0f172a;
        font-weight: 700;
    }
    .chatbot-prose em {
        color: #334155;
    }
    .chatbot-prose h3 {
        font-weight: 800;
        color: #0f172a;
        font-size: 0.85rem;
        margin: 0.8rem 0 0.4rem;
        line-height: 1.5;
    }
    .chatbot-prose h4 {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.78rem;
        margin: 0.7rem 0 0.35rem;
        line-height: 1.5;
    }
    .chatbot-prose h3:first-child,
    .chatbot-prose h4:first-child {
        margin-top: 0;
    }
    .chatbot-prose ul,
    .chatbot-prose ol {
        margin: 0 0 0.6rem;
        padding-left: 1.25rem;
    }
    .chatbot-prose ul {
        list-style-type: disc;
    }
    .chatbot-prose ol {
        list-style-type: decimal;
    }
    .chatbot-prose li {
        margin-bottom: 0.3rem;
        line-height: 1.65;
    }
    .chatbot-prose li:last-child {
        margin-bottom: 0;
    }
    .chatbot-prose li > ul,
    .chatbot-prose li > ol {
        margin-top: 0.3rem;
        margin-bottom: 0;
    }
    .chatbot-prose blockquote {
        border-left: 3px solid #0284c7;
        background: #f0f9ff;
        border-radius: 0 0.6rem 0.6rem 0;
        padding: 0.5rem 0.75rem;
        font-style: italic;
        color: #475569;
        margin: 0 0 0.6rem;
    }
    .chatbot-prose hr {
        border: none;
        border-top: 1px solid #e2e8f0;
        margin: 0.7rem 0;
    }
    .chatbot-prose code {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 0.35rem;
        padding: 0.05rem 0.35rem;
        font-size: 0.7rem;
        font-family: ui-monospace, monospace;
        color: #0f172a;
    }
    .chatbot-prose pre {
        background: #0f172a;
        color: #e2e8f0;
        border-radius: 0.65rem;
        padding: 0.65rem 0.75rem;
        margin: 0 0 0.6rem;
        overflow-x: auto;
        font-size: 0.7rem;
        line-height: 1.6;
    }
    .chatbot-prose pre code {
        background: transparent;
        border: none;
        padding: 0;
        color: inherit;
    }
    .chatbot-table-wrap {
        overflow-x: auto;
        margin: 0 0 0.6rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.6rem;
    }
    .chatbot-prose table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.7rem;
        line-height: 1.55;
    }
    .chatbot-prose th {
        background: #f8fafc;
        font-weight: 700;
        color: #0f172a;
        text-align: left;
        padding: 0.45rem 0.6rem;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .chatbot-prose td {
        padding: 0.45rem 0.6rem;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: top;
    }
    .chatbot-prose tr:last-child td {
        border-bottom: none;
    }
    .chatbot-publication-link {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.65rem;
        background: #f0f9ff;
        color: #0369a1;
        border: 1px solid #bae6fd;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.75rem;
        text-decoration: none;
        margin: 0.25rem 0;
        transition: all 0.2s;
    }
    .chatbot-publication-link:hover {
        background: #e0f2fe;
        color: #0284c7;
        border-color: #7dd3fc;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const root = document.getElementById('bpsChatbotRoot');
    if (!root) return;

    const btnToggle = document.getElementById('btnToggleChatbot');
    const btnClose = document.getElementById('btnCloseChat');
    const btnClear = document.getElementById('btnClearChat');
    const btnSwitchUniversal = document.getElementById('btnSwitchToUniversal');
    const chatWindow = document.getElementById('chatbotWindow');
    const messagesContainer = document.getElementById('chatbotMessages');
    const form = document.getElementById('chatbotForm');
    const input = document.getElementById('chatbotInput');
    const btnSend = document.getElementById('btnSendChat');
    const typingIndicator = document.getElementById('typingIndicator');
    const fabArrow = document.getElementById('chatbotFabArrow');
    const quickChips = document.querySelectorAll('.quick-chip');
    const contextBanner = document.getElementById('chatbotContextBanner');
    const contextText = document.getElementById('chatbotContextText');
    const modeBadge = document.getElementById('chatbotModeBadge');

    let currentPublicationId = root.dataset.publicationId ? parseInt(root.dataset.publicationId) : null;
    let currentPublicationTitle = root.dataset.publicationTitle || '';
    let chatHistory = [];
    let isWaitingResponse = false;

    // Toggle Chat Window
    function openChat() {
        btnToggle.classList.add('hidden');
        btnToggle.setAttribute('aria-expanded', 'true');
        chatWindow.classList.remove('hidden');
        chatWindow.classList.add('flex');
        setTimeout(() => {
            chatWindow.classList.remove('scale-95', 'opacity-0');
            chatWindow.classList.add('scale-100', 'opacity-100');
            if (fabArrow) fabArrow.classList.add('rotate-180');
            input.focus();
            scrollToBottom();
        }, 10);
    }

    function closeChat() {
        btnToggle.classList.remove('hidden');
        btnToggle.setAttribute('aria-expanded', 'false');
        btnToggle.focus();
        chatWindow.classList.remove('scale-100', 'opacity-100');
        chatWindow.classList.add('scale-95', 'opacity-0');
        if (fabArrow) fabArrow.classList.remove('rotate-180');
        setTimeout(() => {
            chatWindow.classList.add('hidden');
            chatWindow.classList.remove('flex');
        }, 300);
    }

    btnToggle.addEventListener('click', () => {
        if (chatWindow.classList.contains('hidden')) {
            openChat();
        } else {
            closeChat();
        }
    });

    btnClose.addEventListener('click', closeChat);
    chatWindow.addEventListener('keydown', e => { if (e.key === 'Escape') closeChat(); });

    // Switch to Universal Mode if user explicitly clicks 'Cari Global'
    if (btnSwitchUniversal) {
        btnSwitchUniversal.addEventListener('click', () => {
            currentPublicationId = null;
            contextText.innerHTML = 'Mode Asisten Universal: Menjelajahi seluruh katalog publikasi BPS';
            modeBadge.innerHTML = '<i data-lucide="globe" class="w-3 h-3 text-cyan-300"></i><span>Asisten Universal BPS</span>';
            btnSwitchUniversal.classList.add('hidden');
            if (window.lucide) if (window.lucide) window.lucide.createIcons();
            appendBotMessage("Beralih ke **Mode Universal BPS**. Sekarang Anda dapat menanyakan letak data atau publikasi apa pun dari seluruh katalog!");
        });
    }

    // Clear Chat History
    btnClear.addEventListener('click', () => {
        if (confirm('Bersihkan riwayat percakapan ini?')) {
            chatHistory = [];
            const userMessages = messagesContainer.querySelectorAll('.message-user, .message-bot:not(:first-child)');
            userMessages.forEach(el => el.remove());
            const suggestions = document.getElementById('quickSuggestionsContainer');
            if (suggestions) suggestions.classList.remove('hidden');
            scrollToBottom();
        }
    });

    // Auto-resize textarea
    input.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Enter to Send (Shift+Enter for new line)
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.dispatchEvent(new Event('submit'));
        }
    });

    // Quick Suggestions click
    document.addEventListener('click', function(e) {
        const chip = e.target.closest('.quick-chip');
        if (chip) {
            const query = chip.textContent.trim().replace(/^[📊📈🔍📍📉📚💡]\s*/, '');
            input.value = query;
            form.dispatchEvent(new Event('submit'));
        }
    });

    // Scroll to bottom smoothly
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Append User Message
    function appendUserMessage(text) {
        const timeStr = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        const div = document.createElement('div');
        div.className = 'flex items-start justify-end gap-2.5 message-user animate-fadeIn';
        div.innerHTML = `
            <div class="w-full max-w-[85%] min-w-0 space-y-1 text-right">
                <div class="w-full min-w-0 break-words bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl rounded-tr-sm px-4 py-3.5 text-xs leading-[1.7] shadow-sm text-left font-medium">
                    ${escapeHtml(text)}
                </div>
                <span class="text-[10px] text-slate-400 font-medium pr-1">${timeStr}</span>
            </div>
            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 border border-blue-200 font-bold text-xs">
                ${escapeHtml({{ \Illuminate\Support\Js::from(mb_substr(Auth::user()->username ?? 'U', 0, 2)) }})}
            </div>
        `;
        messagesContainer.insertBefore(div, typingIndicator);
        scrollToBottom();
    }

    // Append Bot Message
    function appendBotMessage(markdownText) {
        const timeStr = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        const htmlContent = window.DOMPurify.sanitize(parseMarkdown(markdownText), {USE_PROFILES: {html: true}});
        const div = document.createElement('div');
        div.className = 'flex items-start gap-2.5 message-bot animate-fadeIn';
        div.innerHTML = `
            <div class="w-8 h-8 rounded-full bg-bps-navy text-cyan-300 flex items-center justify-center shrink-0 shadow-sm border border-slate-200">
                <i data-lucide="bot" class="w-4 h-4"></i>
            </div>
            <div class="w-full max-w-[85%] min-w-0 space-y-1">
                <div class="w-full min-w-0 break-words bg-white border border-slate-200/90 rounded-2xl rounded-tl-sm px-4 py-3.5 text-xs text-slate-800 leading-[1.7] shadow-sm chatbot-prose">
                    ${htmlContent}
                </div>
                <span class="text-[10px] text-slate-400 font-medium pl-1">${timeStr}</span>
            </div>
        `;
        messagesContainer.insertBefore(div, typingIndicator);
        if (window.lucide) if (window.lucide) window.lucide.createIcons();
        scrollToBottom();
    }

    // Handle Form Submit
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const message = input.value.trim();
        if (!message || isWaitingResponse) return;

        // Hide suggestions once user interacts
        const suggestions = document.getElementById('quickSuggestionsContainer');
        if (suggestions) suggestions.classList.add('hidden');

        // Reset input
        input.value = '';
        input.style.height = 'auto';
        btnSend.disabled = true;
        isWaitingResponse = true;

        appendUserMessage(message);

        // Show typing indicator
        typingIndicator.classList.remove('hidden');
        typingIndicator.classList.add('flex');
        scrollToBottom();

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content 
                || '{{ csrf_token() }}';

            const response = await fetch('{{ route("chatbot.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    message: message,
                    publication_id: currentPublicationId,
                    history: chatHistory.slice(-20).map(item => ({role: item.role, text: item.text.slice(0, 4000)}))
                })
            });

            const data = await response.json();

            // Record to history
            chatHistory.push({ role: 'user', text: message });

            if (data.status === 'success' && data.reply) {
                chatHistory.push({ role: 'model', text: data.reply });
                appendBotMessage(data.reply);
            } else {
                appendBotMessage(response.status === 429 ? 'Batas permintaan tercapai. Silakan tunggu sebelum mencoba lagi.' : 'Maaf, terjadi kesalahan saat menghubungi server AI. Silakan coba beberapa saat lagi.');
            }
        } catch (error) {
            console.error('Chatbot request error:', error);
            appendBotMessage('Maaf, koneksi jaringan terputus. Silakan periksa koneksi internet Anda dan coba lagi.');
        } finally {
            typingIndicator.classList.add('hidden');
            typingIndicator.classList.remove('flex');
            btnSend.disabled = false;
            isWaitingResponse = false;
            input.focus();
        }
    });

    // Helper: Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Helper: Markdown parser to safe formatted HTML (block-based)
    function inlineFormat(text) {
        let html = escapeHtml(text);

        // Inline code `code` (escape inside first so tags can't break out)
        html = html.replace(/`([^`\n]+)`/g, function(m, code) {
            return '<code>' + code + '</code>';
        });

        // Markdown Links [Text](url)
        html = html.replace(/\[(.*?)\]\((.*?)\)/g, function(match, label, url) {
            const safeLabel = label.trim();
            let safeUrl = url.trim().replace(/["'\s<>]/g, '');
            if (!safeUrl) return safeLabel;
            if (safeUrl.startsWith('/publications/')) {
                return `<a href="${safeUrl}" class="chatbot-publication-link"><i data-lucide="book-open" class="w-3.5 h-3.5 inline"></i> ${safeLabel}</a>`;
            }
            if (!/^https?:\/\//i.test(safeUrl)) return safeLabel;
            return `<a href="${safeUrl}" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline font-semibold hover:text-blue-800">${safeLabel}</a>`;
        });

        // Bold (**text**)
        html = html.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');

        // Italic (*text* or _text_) — single char markers only, avoids list markers
        html = html.replace(/(^|[^*\w])\*([^*\n]+)\*/g, '$1<em>$2</em>');
        html = html.replace(/(^|[^\w])_([^_\n]+)_/g, '$1<em>$2</em>');

        return html;
    }

    function isTableDivider(line) {
        return /^\s*\|?[\s:|-]+\|?[\s:|-]*$/.test(line) && line.includes('-');
    }

    function parseTableRow(line) {
        let cells = line.trim();
        if (cells.startsWith('|')) cells = cells.slice(1);
        if (cells.endsWith('|')) cells = cells.slice(0, -1);
        return cells.split('|').map(c => inlineFormat(c.trim()));
    }

    function parseMarkdown(text) {
        if (!text) return '';
        // Normalize newlines, keep code fences intact during split
        const normalized = String(text).replace(/\r\n?/g, '\n');
        const lines = normalized.split('\n');
        let html = '';
        let i = 0;

        while (i < lines.length) {
            const line = lines[i];

            // Code fence ``` ... ```
            if (/^\s*```/.test(line)) {
                let codeLines = [];
                i++;
                while (i < lines.length && !/^\s*```/.test(lines[i])) {
                    codeLines.push(lines[i]);
                    i++;
                }
                i++; // skip closing fence
                html += '<pre><code>' + escapeHtml(codeLines.join('\n')) + '</code></pre>';
                continue;
            }

            // Blank line → block separator
            if (/^\s*$/.test(line)) {
                i++;
                continue;
            }

            // Markdown table (| a | b | + divider row)
            if (line.includes('|') && i + 1 < lines.length && isTableDivider(lines[i + 1])) {
                const header = parseTableRow(line);
                i += 2;
                let rows = [];
                while (i < lines.length && lines[i].includes('|') && lines[i].trim() !== '') {
                    rows.push(parseTableRow(lines[i]));
                    i++;
                }
                html += '<div class="chatbot-table-wrap"><table><thead><tr>' +
                    header.map(h => `<th>${h}</th>`).join('') +
                    '</tr></thead><tbody>' +
                    rows.map(r => `<tr>${r.map(c => `<td>${c}</td>`).join('')}</tr>`).join('') +
                    '</tbody></table></div>';
                continue;
            }

            // Headers (# .., ## .., ### ..)
            const hMatch = line.match(/^\s*(#{1,3})\s+(.*)$/);
            if (hMatch) {
                const level = hMatch[1].length;
                const tag = level === 1 ? 'h3' : 'h4';
                html += `<${tag}>${inlineFormat(hMatch[2])}</${tag}>`;
                i++;
                continue;
            }

            // Blockquote (> ...)
            if (/^\s*&gt;|^\s*>/.test(escapeHtml(line)) || /^\s*>/.test(line)) {
                let quoteLines = [];
                while (i < lines.length && /^\s*>/.test(lines[i])) {
                    quoteLines.push(lines[i].replace(/^\s*>+\s?/, ''));
                    i++;
                }
                html += '<blockquote>' + quoteLines.map(l => inlineFormat(l)).join('<br>') + '</blockquote>';
                continue;
            }

            // Horizontal rule (--- or ***)
            if (/^\s*(-{3,}|\*{3,})\s*$/.test(line)) {
                html += '<hr>';
                i++;
                continue;
            }

            // Unordered list (-, *, •)
            if (/^\s*[-*•]\s+/.test(line)) {
                let items = [];
                while (i < lines.length && /^\s*[-*•]\s+/.test(lines[i])) {
                    items.push(lines[i].replace(/^\s*[-*•]\s+/, ''));
                    i++;
                }
                html += '<ul>' + items.map(it => `<li>${inlineFormat(it)}</li>`).join('') + '</ul>';
                continue;
            }

            // Ordered list (1. 2. ...)
            if (/^\s*\d+\.\s+/.test(line)) {
                let items = [];
                while (i < lines.length && /^\s*\d+\.\s+/.test(lines[i])) {
                    items.push(lines[i].replace(/^\s*\d+\.\s+/, ''));
                    i++;
                }
                html += '<ol>' + items.map(it => `<li>${inlineFormat(it)}</li>`).join('') + '</ol>';
                continue;
            }

            // Paragraph — gather consecutive plain lines
            let paraLines = [line];
            i++;
            while (i < lines.length && lines[i].trim() !== '' &&
                !/^\s*(#{1,3}\s+|```|>|\d+\.\s+|[-*•]\s+)/.test(lines[i]) &&
                !(lines[i].includes('|') && i + 1 < lines.length && isTableDivider(lines[i + 1]))) {
                paraLines.push(lines[i]);
                i++;
            }
            html += '<p>' + paraLines.map(l => inlineFormat(l)).join('<br>') + '</p>';
        }

        return html;
    }

    // Initialize Lucide icons
    if (window.lucide) {
        if (window.lucide) window.lucide.createIcons();
    }
});
</script>
