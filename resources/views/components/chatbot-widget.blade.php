<div id="ai-chat-widget" class="ai-chat-widget">
    <button type="button" id="ai-chat-toggle" class="ai-chat-toggle" aria-label="Mở trợ lý AI">
        <span class="ai-chat-icon">
            <i class="fa fa-comments"></i>
        </span>
        <span class="ai-chat-label">Hỏi AI</span>
    </button>

    <div id="ai-chat-modal" class="ai-chat-modal" aria-hidden="true">
        <div class="ai-chat-overlay"></div>
        <div class="ai-chat-panel" role="dialog" aria-modal="true" aria-label="Chatbot AI">
            <div class="ai-chat-header">
                <div class="ai-chat-header-left">
                    <div class="ai-chat-avatar">
                        <i class="fa fa-robot"></i>
                    </div>
                    <div>
                        <div class="ai-chat-title">PTIT AI Assistant</div>
                        <div class="ai-chat-subtitle">Trực tuyến • tư vấn sản phẩm & đơn hàng</div>
                    </div>
                </div>
                <button type="button" id="ai-chat-close" class="ai-chat-close" aria-label="Đóng">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div id="ai-chat-messages" class="ai-chat-messages">
                <div class="ai-chat-message ai-chat-message-bot">
                    <div class="ai-chat-message-avatar">
                        <i class="fa fa-robot"></i>
                    </div>
                    <div class="ai-chat-message-body">
                        <div class="ai-chat-message-text">
                            Xin chào 👋, mình là trợ lý AI của PTIT eCommerce.
                            Bạn có thể hỏi về tồn kho, giá, hoặc tình trạng đơn hàng của mình.
                        </div>
                    </div>
                </div>
            </div>

            <form id="ai-chat-form" class="ai-chat-form">
                <input
                    id="ai-chat-input"
                    type="text"
                    class="ai-chat-input"
                    placeholder="Nhập câu hỏi của bạn..."
                    autocomplete="off"
                >
                <button type="submit" id="ai-chat-send" class="ai-chat-send" aria-label="Gửi">
                    <i class="fa fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Widget vị trí giống scrollUp, luôn nổi trên footer */
#ai-chat-widget {
    position: fixed;
    right: 10px;
    bottom: 60px; /* ngay trên nút scrollUp (40px + 10px) */
    z-index: 999999;
}

/* Nút AI – kích thước/shape giống #scrollUp i, khác màu */
#ai-chat-toggle {
    width: 40px;
    height: 40px;
    border-radius: 0;
    border: none;
    background: #FBBF24; /* vàng, khác màu đen của scrollUp */
    color: #111827;
    cursor: pointer;
    box-shadow: 0px 4px 19px #00000038;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}
#ai-chat-toggle:hover {
    background: #F59E0B;
    color: #111827;
}
.ai-chat-label {
    display: none; /* ẩn text, chỉ để lại icon như scrollUp */
}

/* Modal + panel đầy đủ để không bị tràn ra layout */
.ai-chat-modal {
    position: fixed;
    inset: 0;
    display: none;
    align-items: flex-end;
    justify-content: flex-end;
    padding: 16px;
    z-index: 12000;
}
.ai-chat-modal.active {
    display: flex;
}
.ai-chat-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.35);
    backdrop-filter: blur(2px);
}
.ai-chat-panel {
    width: 360px;
    max-width: calc(100vw - 32px);
    height: 520px;
    max-height: calc(100vh - 80px);
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 24px 80px rgba(0,0,0,0.35);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
}
.ai-chat-header {
    padding: 12px 14px;
    border-bottom: 1px solid rgba(17,24,39,0.06);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.ai-chat-header-left {
    display: flex;
    align-items: center;
    gap: 10px;
}
.ai-chat-avatar {
    width: 32px;
    height: 32px;
    border-radius: 999px;
    background: #111827;
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}
.ai-chat-title {
    font-weight: 700;
    font-size: 14px;
}
.ai-chat-subtitle {
    font-size: 12px;
    color: #6b7280;
}
.ai-chat-close {
    border: none;
    background: transparent;
    cursor: pointer;
    color: #6b7280;
    font-size: 16px;
}

.ai-chat-messages {
    flex: 1;
    padding: 12px 12px 8px;
    overflow-y: auto;
    background: #f9fafb;
}
.ai-chat-message {
    display: flex;
    margin-bottom: 10px;
}
.ai-chat-message-bot .ai-chat-message-avatar {
    margin-right: 8px;
}
.ai-chat-message-user {
    flex-direction: row-reverse;
}
.ai-chat-message-user .ai-chat-message-avatar {
    margin-left: 8px;
}
.ai-chat-message-avatar {
    width: 28px;
    height: 28px;
    border-radius: 999px;
    background: #111827;
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}
.ai-chat-message-body {
    max-width: 80%;
}
.ai-chat-message-text {
    padding: 8px 10px;
    border-radius: 14px;
    font-size: 13px;
    line-height: 1.4;
}
.ai-chat-message-bot .ai-chat-message-text {
    background: #e5e7eb;
    color: #111827;
    border-bottom-left-radius: 4px;
}
.ai-chat-message-user .ai-chat-message-text {
    background: #4f46e5;
    color: #ffffff;
    border-bottom-right-radius: 4px;
}

.ai-chat-form {
    display: flex;
    align-items: center;
    padding: 8px 10px;
    border-top: 1px solid rgba(17,24,39,0.06);
    background: #ffffff;
}
.ai-chat-input {
    flex: 1;
    border: none;
    outline: none;
    padding: 8px 10px;
    font-size: 13px;
}
.ai-chat-send {
    width: 34px;
    height: 34px;
    border-radius: 999px;
    border: none;
    background: #4f46e5;
    color: #fff;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.ai-chat-send:disabled {
    opacity: .5;
    cursor: not-allowed;
}

@media (max-width: 576px) {
    #ai-chat-widget {
        right: 10px;
        bottom: 60px;
    }
    .ai-chat-panel {
        width: 100%;
        height: 80vh;
    }
}

</style>
@endpush

@push('scripts')
<script>
(function () {
    'use strict';

    var toggle = document.getElementById('ai-chat-toggle');
    var modal = document.getElementById('ai-chat-modal');
    var overlay = modal ? modal.querySelector('.ai-chat-overlay') : null;
    var closeBtn = document.getElementById('ai-chat-close');
    var form = document.getElementById('ai-chat-form');
    var input = document.getElementById('ai-chat-input');
    var sendBtn = document.getElementById('ai-chat-send');
    var messages = document.getElementById('ai-chat-messages');
    var conversationId = null;

    if (!toggle || !modal || !form || !input || !messages) return;

    function openModal() {
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
        setTimeout(function () { input.focus(); }, 50);
    }

    function closeModal() {
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
    }

    function appendMessage(text, type) {
        var wrap = document.createElement('div');
        wrap.className = 'ai-chat-message ' + (type === 'user' ? 'ai-chat-message-user' : 'ai-chat-message-bot');

        var avatar = document.createElement('div');
        avatar.className = 'ai-chat-message-avatar';
        avatar.innerHTML = type === 'user' ? '<i class="fa fa-user"></i>' : '<i class="fa fa-robot"></i>';

        var body = document.createElement('div');
        body.className = 'ai-chat-message-body';
        var bubble = document.createElement('div');
        bubble.className = 'ai-chat-message-text';
        bubble.textContent = text;

        body.appendChild(bubble);
        wrap.appendChild(avatar);
        wrap.appendChild(body);
        messages.appendChild(wrap);
        messages.scrollTop = messages.scrollHeight;
    }

    function setLoading(isLoading) {
        if (!sendBtn) return;
        sendBtn.disabled = isLoading;
    }

    async function sendMessage(evt) {
        evt.preventDefault();
        var text = (input.value || '').trim();
        if (!text) return;

        appendMessage(text, 'user');
        input.value = '';
        setLoading(true);

        try {
            var res = await fetch('/api/chatbot/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    message: text,
                    conversation_id: conversationId
                })
            });

            var data = await res.json();
            if (!res.ok) {
                appendMessage(data.message || 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau.', 'bot');
            } else {
                conversationId = data.conversation_id || conversationId;
                appendMessage(data.response || '...', 'bot');
            }
        } catch (e) {
            appendMessage('Xin lỗi, tôi đang gặp sự cố kết nối. Vui lòng thử lại sau.', 'bot');
        } finally {
            setLoading(false);
        }
    }

    toggle.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (overlay) overlay.addEventListener('click', closeModal);
    form.addEventListener('submit', sendMessage);
})();
</script>
@endpush


