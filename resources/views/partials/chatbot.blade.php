<!-- Chat Bot Bubble -->
<div id="chatbot-container">
    <!-- Chat Bubble Button -->
    <button id="chatbot-bubble" class="chatbot-bubble" title="Buka Chat Bot">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
    </button>

    <!-- Chat Window -->
    <div id="chatbot-window" class="chatbot-window hidden">
        <!-- Header -->
        <div class="chatbot-header">
            <div class="chatbot-title">
                <h3>Chat Bot</h3>
                <p>Kami siap membantu Anda</p>
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
                <div class="message-content">
                    Halo! 👋 Selamat datang. Ada yang bisa kami bantu?
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="chatbot-input-area">
            <input 
                type="text" 
                id="chatbot-input" 
                class="chatbot-input" 
                placeholder="Ketik pesan Anda..." 
                autocomplete="off"
            />
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
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        border: none;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.4);
        transition: all 0.3s ease;
        animation: pulse 2s infinite;
    }

    .chatbot-bubble:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 16px rgba(5, 150, 105, 0.6);
    }

    .chatbot-bubble:active {
        transform: scale(0.95);
    }

    @keyframes pulse {
        0%, 100% {
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.4);
        }
        50% {
            box-shadow: 0 4px 20px rgba(5, 150, 105, 0.6);
        }
    }

    /* Chat Window */
    .chatbot-window {
        position: absolute;
        bottom: 80px;
        right: 0;
        width: 380px;
        height: 500px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 40px rgba(0, 0, 0, 0.16);
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
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
        padding: 16px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-shrink: 0;
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
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        background: #f9fafb;
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
        max-width: 70%;
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 13px;
        line-height: 1.4;
        word-wrap: break-word;
    }

    .bot-message .message-content {
        background: white;
        color: #1f2937;
        border: 1px solid #e5e7eb;
    }

    .user-message .message-content {
        background: #059669;
        color: white;
        border-radius: 12px 2px 12px 12px;
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
        border-top: 1px solid #e5e7eb;
        flex-shrink: 0;
    }

    .chatbot-input {
        flex: 1;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13px;
        font-family: inherit;
        transition: all 0.2s;
        outline: none;
    }

    .chatbot-input:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
    }

    .chatbot-send {
        background: #059669;
        border: none;
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 8px;
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

    /* Responsive */
    @media (max-width: 480px) {
        #chatbot-container {
            bottom: 10px;
            right: 10px;
        }

        .chatbot-window {
            width: calc(100vw - 20px);
            height: 70vh;
            max-height: 500px;
            bottom: 70px;
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
        function sendMessage() {
            const message = input.value.trim();
            if (!message) return;

            // Add user message
            addMessage(message, 'user');
            input.value = '';

            // Simulate bot response after a short delay
            setTimeout(() => {
                const botResponse = generateBotResponse(message);
                addMessage(botResponse, 'bot');
            }, 500);
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
        }

        // Generate bot response
        function generateBotResponse(userMessage) {
            const responses = {
                'halo': 'Halo! Senang berkenalan dengan Anda. Ada yang bisa kami bantu? 😊',
                'apa kabar': 'Saya baik-baik saja! Terima kasih sudah bertanya. Bagaimana dengan Anda? 😊',
                'bantuan': 'Tentu! Kami siap membantu Anda dengan pertanyaan atau masalah apa pun. Silakan tanyakan! 🙌',
                'terima kasih': 'Sama-sama! Senang bisa membantu Anda. 😊',
                'ok': 'Baik! Ada lagi yang bisa kami bantu?',
                'tidak': 'Baik, jika ada yang butuh, silakan hubungi kami kembali. Terima kasih! 👋'
            };

            const lowerMessage = userMessage.toLowerCase();
            
            for (let key in responses) {
                if (lowerMessage.includes(key)) {
                    return responses[key];
                }
            }

            return 'Terima kasih atas pertanyaannya. Kami akan merespons Anda segera atau hubungi tim support kami untuk bantuan lebih lanjut. 🙏';
        }

        // Send on button click
        sendBtn.addEventListener('click', sendMessage);

        // Send on Enter key
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        // Close chat when clicking outside (optional)
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#chatbot-container')) {
                // Don't close on outside click - uncomment line below if you want this behavior
                // chatWindow.classList.add('hidden');
            }
        });
    });
</script>
