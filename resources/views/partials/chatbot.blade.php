<!-- Chat Bot Bubble -->
<div id="chatbot-container">
    <!-- Chat Bubble Button -->
    <button id="chatbot-bubble" class="chatbot-bubble" title="Buka Si Jajang">
        <span class="chatbot-bubble-avatar">
            <img src="{{ asset('images/sijajang.png') }}" alt="Ikon Si Jajang" class="chatbot-icon-image">
        </span>
    </button>

    <!-- Chat Window -->
    <div id="chatbot-window" class="chatbot-window hidden">
        <!-- Header -->
        <div class="chatbot-header">
            <div class="chatbot-avatar">
                <img src="{{ asset('images/sijajang.png') }}" alt="Ikon Si Jajang" class="chatbot-icon-image">
            </div>
            <div class="chatbot-title">
                <h3>Si Jajang</h3>
                <p><span class="chatbot-status-dot"></span>Asisten Green Point</p>
            </div>
            <button id="chatbot-close" class="chatbot-close" title="Tutup Chat">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- Messages Area -->
        <div class="chatbot-messages" id="chatbot-messages">
            <div class="chatbot-message bot-message">
                <div class="message-content">Halo! 👋 Saya Si Jajang. Ada yang bisa saya bantu hari ini?</div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="chatbot-input-area">
            <textarea
                id="chatbot-input" 
                class="chatbot-input" 
                placeholder="Ketik pesan Anda..." 
                autocomplete="off"
                rows="1"
            ></textarea>
            <button id="chatbot-send" class="chatbot-send" title="Kirim">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
            </button>
        </div>
    </div>
</div>

<style>
    /* Chat Bot Container */
    #chatbot-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        z-index: 9999;
    }

    /* Chat Bubble Button */
    .chatbot-bubble {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: white;
        border: none;
        color: #047857;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.16);
        transition: all 0.3s ease;
    }

    .chatbot-bubble-avatar {
        width: 42px;
        height: 42px;
        border-radius: 999px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .chatbot-bubble:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.2);
    }

    .chatbot-bubble:active {
        transform: scale(0.95);
    }

    /* Chat Window */
    .chatbot-window {
        position: absolute;
        bottom: 80px;
        right: 0;
        width: 390px;
        height: 560px;
        background: #f5f7f6;
        border-radius: 24px;
        border: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.18);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: slideUp 0.3s ease;
    }

    .chatbot-window.hidden {
        display: none;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideDown {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(20px);
        }
    }

    /* Chat Header */
    .chatbot-header {
        background: #0f8a70;
        color: white;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
    }

    .chatbot-avatar {
        width: 42px;
        height: 42px;
        border-radius: 999px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12);
    }

    .chatbot-icon-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transform: scale(1.8);
    }

    .chatbot-title {
        flex: 1;
    }

    .chatbot-title h3 {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
        margin-bottom: 4px;
    }

    .chatbot-title p {
        font-size: 12px;
        margin: 0;
        opacity: 0.9;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .chatbot-status-dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: #bbf7d0;
        box-shadow: 0 0 0 3px rgba(187, 247, 208, 0.18);
    }

    .chatbot-close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 28px;
        height: 28px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        padding: 0;
    }

    .chatbot-close:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    /* Messages Area */
    .chatbot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 18px 16px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        background:
            radial-gradient(circle at top left, rgba(16, 185, 129, 0.09), transparent 32%),
            #f5f7f6;
    }

    .chatbot-message {
        display: flex;
        animation: messageSlide 0.3s ease;
    }

    @keyframes messageSlide {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .bot-message {
        justify-content: flex-start;
    }

    .user-message {
        justify-content: flex-end;
    }

    .message-content {
        max-width: 86%;
        padding: 12px 14px;
        border-radius: 18px;
        font-size: 14px;
        line-height: 1.55;
        word-break: break-word;
        overflow-wrap: anywhere;
        white-space: pre-line;
    }

    .bot-message .message-content {
        background: white;
        color: #16312a;
        border: 1px solid rgba(5, 150, 105, 0.12);
        border-radius: 18px 18px 18px 6px;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
    }

    .user-message .message-content {
        background: #059669;
        color: white;
        border-radius: 18px 18px 6px 18px;
        box-shadow: 0 8px 20px rgba(5, 150, 105, 0.12);
    }

    /* Scrollbar */
    .chatbot-messages::-webkit-scrollbar {
        width: 6px;
    }

    .chatbot-messages::-webkit-scrollbar-track {
        background: transparent;
    }

    .chatbot-messages::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
    }

    .chatbot-messages::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    /* Input Area */
    .chatbot-input-area {
        display: flex;
        gap: 8px;
        padding: 12px;
        background: white;
        border-top: 1px solid rgba(5, 150, 105, 0.1);
        align-items: flex-end;
        flex-shrink: 0;
    }

    .chatbot-input {
        flex: 1;
        min-width: 0;
        min-height: 42px;
        max-height: 120px;
        border: 1px solid rgba(5, 150, 105, 0.22);
        border-radius: 16px;
        padding: 11px 14px;
        font-size: 14px;
        line-height: 1.4;
        font-family: inherit;
        transition: all 0.2s;
        outline: none;
        resize: none;
        overflow-y: hidden;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    .chatbot-input:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
    }

    .chatbot-send {
        background: #059669;
        border: none;
        color: white;
        width: 42px;
        height: 42px;
        border-radius: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        padding: 0;
        flex-shrink: 0;
    }

    .chatbot-send:hover {
        background: #047857;
    }

    .chatbot-send:active {
        transform: scale(0.95);
    }

    .chatbot-send:disabled,
    .chatbot-input:disabled {
        cursor: not-allowed;
        opacity: 0.7;
    }

    .typing-indicator .message-content {
        color: #6b7280;
        font-style: italic;
    }

    /* Responsive */
    @media (max-width: 480px) {
        #chatbot-container {
            bottom: 10px;
            right: 10px;
        }

        .chatbot-window {
            width: calc(100vw - 20px);
            height: min(76vh, 620px);
            bottom: 70px;
            border-radius: 22px;
        }

        .chatbot-bubble {
            width: 50px;
            height: 50px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bubble = document.getElementById('chatbot-bubble');
        const chatWindow = document.getElementById('chatbot-window');
        const closeBtn = document.getElementById('chatbot-close');
        const sendBtn = document.getElementById('chatbot-send');
        const input = document.getElementById('chatbot-input');
        const messagesArea = document.getElementById('chatbot-messages');
        const chatbotEndpoint = @json(route('chatbot.message'));
        const csrfToken = @json(csrf_token());
        const conversationHistory = [];
        let isSending = false;

        // Toggle chat window
        bubble.addEventListener('click', function() {
            chatWindow.classList.toggle('hidden');
            if (!chatWindow.classList.contains('hidden')) {
                input.focus();
            }
        });

        // Close chat
        closeBtn.addEventListener('click', function() {
            chatWindow.classList.add('hidden');
        });

        // Send message
        async function sendMessage() {
            const message = input.value.trim();
            if (!message || isSending) return;

            const historyForRequest = buildHistoryPayload();

            // Add user message
            addMessage(message, 'user');
            conversationHistory.push({ role: 'user', content: message });
            input.value = '';
            resizeInput();
            setSendingState(true);

            const typingIndicator = addTypingIndicator();

            try {
                const response = await fetch(chatbotEndpoint, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        message,
                        history: historyForRequest,
                    }),
                });

                let payload = {};

                try {
                    payload = await response.json();
                } catch (_) {
                    payload = {};
                }

                if (!response.ok) {
                    if (response.status === 422) {
                        throw new Error('Maaf, pesan belum bisa diproses. Coba kirim ulang ya.');
                    }

                    throw new Error(payload.message || 'Gagal menghubungi chatbot.');
                }

                const botResponse = (payload.message || '').trim();

                if (!botResponse) {
                    throw new Error('Jawaban chatbot kosong.');
                }

                removeMessage(typingIndicator);
                addMessage(botResponse, 'bot');
                conversationHistory.push({ role: 'assistant', content: botResponse });
            } catch (error) {
                removeMessage(typingIndicator);
                const fallbackMessage = error instanceof Error && error.message
                    ? error.message
                    : 'Maaf, Si Jajang belum bisa menjawab saat ini. Coba lagi sebentar ya.';

                addMessage(fallbackMessage, 'bot');
            } finally {
                setSendingState(false);
            }
        }

        // Add message to chat
        function addMessage(text, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `chatbot-message ${sender}-message`;
            
            const contentDiv = document.createElement('div');
            contentDiv.className = 'message-content';
            contentDiv.textContent = text;
            
            messageDiv.appendChild(contentDiv);
            messagesArea.appendChild(messageDiv);
            
            // Scroll to bottom
            messagesArea.scrollTop = messagesArea.scrollHeight;

            return messageDiv;
        }

        function addTypingIndicator() {
            const typingMessage = addMessage('Si Jajang sedang mengetik...', 'bot');
            typingMessage.classList.add('typing-indicator');

            return typingMessage;
        }

        function removeMessage(messageElement) {
            if (messageElement && messageElement.parentNode) {
                messageElement.parentNode.removeChild(messageElement);
            }
        }

        function setSendingState(sending) {
            isSending = sending;
            input.disabled = sending;
            sendBtn.disabled = sending;

            if (!sending) {
                input.focus();
            }
        }

        function buildHistoryPayload() {
            return conversationHistory
                .slice(-10)
                .map((turn) => ({
                    role: turn.role,
                    content: turn.content.slice(0, 1000),
                }));
        }

        // Send on button click
        sendBtn.addEventListener('click', sendMessage);

        function resizeInput() {
            input.style.height = 'auto';
            input.style.height = `${Math.min(input.scrollHeight, 120)}px`;
            input.style.overflowY = input.scrollHeight > 120 ? 'auto' : 'hidden';
        }

        input.addEventListener('input', resizeInput);

        // Send on Enter, keep Shift+Enter for a manual line break
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        resizeInput();

        // Close chat when clicking outside (optional)
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#chatbot-container')) {
                // Don't close on outside click - uncomment line below if you want this behavior
                // chatWindow.classList.add('hidden');
            }
        });
    });
</script>
