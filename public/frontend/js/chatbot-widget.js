// Simple AI Chatbot widget created entirely via JS
(function () {
  'use strict';

  if (typeof document === 'undefined') return;

  var STORAGE_KEY_PREFIX = 'ptit_ai_chat_history_v1';

  function getUserId() {
    var meta = document.querySelector('meta[name="user-id"]');
    if (!meta) return null;
    var raw = (meta.getAttribute('content') || '').trim();
    return raw ? raw : null;
  }

  function getStorageKey() {
    var userId = getUserId();
    return userId ? (STORAGE_KEY_PREFIX + '_user_' + userId) : (STORAGE_KEY_PREFIX + '_guest');
  }

  // Inject minimal CSS (button giống scrollUp, modal riêng)
  function injectStyles() {
    if (document.getElementById('ai-chat-style')) return;
    var css = `
#ai-chat-widget {
  position: fixed;
  right: 10px;
  bottom: 60px;
  z-index: 999999;
  font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;
}
#ai-chat-toggle {
  width: 40px;
  height: 40px;
  border-radius: 0;
  border: none;
  background: #FBBF24;
  color: #111827;
  cursor: pointer;
  box-shadow: 0 4px 19px rgba(0,0,0,.22);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}
#ai-chat-toggle:hover{
  background:#F59E0B;
  color:#111827;
}
#ai-chat-toggle .ai-chat-label{display:none;}
.ai-chat-modal{
  position:fixed;
  inset:0;
  display:none;
  align-items:flex-end;
  justify-content:flex-end;
  padding:16px;
  z-index:12000;
  pointer-events:none; /* cho phép scroll/click trang phía sau, trừ vùng panel */
}
.ai-chat-modal.active{display:flex;}
.ai-chat-overlay{
  display:none; /* không che mờ toàn trang nữa */
}
.ai-chat-panel{
  width:360px;
  max-width:calc(100vw - 32px);
  height:520px;
  max-height:calc(100vh - 80px);
  background:#fff;
  border-radius:18px;
  box-shadow:0 24px 80px rgba(0,0,0,.35);
  display:flex;
  flex-direction:column;
  overflow:hidden;
  position:relative;
  pointer-events:auto; /* panel vẫn bắt được click */
}
.ai-chat-header{
  padding:12px 14px;
  border-bottom:1px solid rgba(17,24,39,.06);
  display:flex;
  align-items:center;
  justify-content:space-between;
}
.ai-chat-header-left{display:flex;align-items:center;gap:10px;}
.ai-chat-avatar{
  width:32px;height:32px;border-radius:999px;
  background:#111827;color:#fff;
  display:inline-flex;align-items:center;justify-content:center;
  font-size:16px;
}
.ai-chat-title{font-weight:700;font-size:14px;}
.ai-chat-subtitle{font-size:12px;color:#6b7280;}
.ai-chat-close{
  border:none;background:transparent;cursor:pointer;
  color:#6b7280;font-size:16px;
}
.ai-chat-messages{
  flex:1;padding:12px 12px 8px;
  overflow-y:auto;background:#f9fafb;
}
.ai-chat-message{display:flex;margin-bottom:10px;}
.ai-chat-message-bot .ai-chat-message-avatar{margin-right:8px;}
.ai-chat-message-user{flex-direction:row-reverse;}
.ai-chat-message-user .ai-chat-message-avatar{margin-left:8px;}
.ai-chat-message-avatar{
  width:28px;height:28px;border-radius:999px;
  background:#111827;color:#fff;
  display:inline-flex;align-items:center;justify-content:center;
  font-size:14px;flex-shrink:0;
}
.ai-chat-message-body{max-width:80%;}
.ai-chat-message-text{
  padding:8px 10px;border-radius:14px;
  font-size:13px;line-height:1.4;
}
.ai-chat-message-bot .ai-chat-message-text{
  background:#e5e7eb;color:#111827;border-bottom-left-radius:4px;
}
.ai-chat-debug{
  margin-top:6px;
  padding:8px 10px;
  border-radius:12px;
  background:#111827;
  color:#f9fafb;
  font-size:12px;
  line-height:1.35;
  white-space:pre-wrap;
  word-break:break-word;
  opacity:.95;
}
.ai-chat-message-user .ai-chat-message-text{
  background:#4f46e5;color:#fff;border-bottom-right-radius:4px;
}
.ai-chat-form{
  display:flex;align-items:center;
  padding:8px 10px;
  border-top:1px solid rgba(17,24,39,.06);
  background:#fff;
}
.ai-chat-input{
  flex:1;border:none;outline:none;
  padding:8px 10px;font-size:13px;
  min-height:34px;
  max-height:96px;
  resize:none;
}
.ai-chat-send{
  width:34px;height:34px;border-radius:999px;
  border:none;background:#4f46e5;color:#fff;
  cursor:pointer;display:inline-flex;
  align-items:center;justify-content:center;
}
.ai-chat-send:disabled{opacity:.5;cursor:not-allowed;}
@media (max-width:576px){
  #ai-chat-widget{right:10px;bottom:60px;}
  .ai-chat-panel{width:100%;height:80vh;}
}
`;
    var style = document.createElement('style');
    style.id = 'ai-chat-style';
    style.type = 'text/css';
    style.appendChild(document.createTextNode(css));
    document.head.appendChild(style);
  }

  function getEmptyHistory() {
    return { conversationId: null, messages: [] };
  }

  function loadHistory() {
    try {
      var raw = localStorage.getItem(getStorageKey());
      if (!raw) return getEmptyHistory();
      var parsed = JSON.parse(raw);
      if (!parsed || typeof parsed !== 'object') return getEmptyHistory();
      if (!Array.isArray(parsed.messages)) parsed.messages = [];
      // sanitize messages
      parsed.messages = parsed.messages
        .filter(function (m) {
          return m && typeof m === 'object' && (m.sender === 'user' || m.sender === 'bot') && typeof m.text === 'string';
        })
        .map(function (m) {
          return { sender: m.sender, text: m.text };
        });
      return parsed;
    } catch (_) {
      return getEmptyHistory();
    }
  }

  function clearHistory() {
    try {
      localStorage.removeItem(getStorageKey());
    } catch (_) {}
  }

  function createWidget() {
    if (document.getElementById('ai-chat-widget')) return;

    var wrapper = document.createElement('div');
    wrapper.id = 'ai-chat-widget';
    wrapper.innerHTML = `
      <button type="button" id="ai-chat-toggle" aria-label="Mở trợ lý AI">
        <span class="ai-chat-icon"><i class="fa fa-comments"></i></span>
        <span class="ai-chat-label">Hỏi AI</span>
      </button>
      <div id="ai-chat-modal" class="ai-chat-modal" aria-hidden="true">
        <div class="ai-chat-overlay"></div>
        <div class="ai-chat-panel" role="dialog" aria-modal="true" aria-label="Chatbot AI">
          <div class="ai-chat-header">
            <div class="ai-chat-header-left">
              <div class="ai-chat-avatar"><i class="fa fa-robot"></i></div>
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
          </div>
          <form id="ai-chat-form" class="ai-chat-form">
            <textarea id="ai-chat-input" class="ai-chat-input"
                   placeholder="Nhập câu hỏi của bạn... (Enter để gửi, Shift+Enter để xuống dòng)"
                   autocomplete="off"></textarea>
            <button type="submit" id="ai-chat-send" class="ai-chat-send" aria-label="Gửi">
              <i class="fa fa-paper-plane"></i>
            </button>
          </form>
        </div>
      </div>
    `;

    document.body.appendChild(wrapper);

    // Attach behaviour
    var toggle = document.getElementById('ai-chat-toggle');
    var modal = document.getElementById('ai-chat-modal');
    var overlay = modal.querySelector('.ai-chat-overlay');
    var closeBtn = document.getElementById('ai-chat-close');
    var form = document.getElementById('ai-chat-form');
    var input = document.getElementById('ai-chat-input');
    var sendBtn = document.getElementById('ai-chat-send');
    var messages = document.getElementById('ai-chat-messages');
    var history = loadHistory();
    var conversationId = history.conversationId || null;

    function openModal() {
      modal.classList.add('active');
      modal.setAttribute('aria-hidden', 'false');
      setTimeout(function () { input && input.focus(); }, 50);
    }

    function closeModal() {
      modal.classList.remove('active');
      modal.setAttribute('aria-hidden', 'true');
    }

    function persistHistory() {
      try {
        // keep storage bounded (avoid quota issues on long sessions)
        var maxMessages = 200;
        if (history.messages.length > maxMessages) {
          history.messages = history.messages.slice(history.messages.length - maxMessages);
        }
        localStorage.setItem(getStorageKey(), JSON.stringify(history));
      } catch (_) {}
    }

    function normalizeDebugText(debug) {
      try {
        if (!debug) return '';
        if (typeof debug === 'string') return debug;
        return JSON.stringify(debug, null, 2);
      } catch (_) {
        return '';
      }
    }

    function appendMessageToDom(text, type, debug) {
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

      var debugText = type === 'bot' ? normalizeDebugText(debug) : '';
      if (debugText) {
        var dbg = document.createElement('pre');
        dbg.className = 'ai-chat-debug';
        dbg.textContent = debugText;
        body.appendChild(dbg);
      }

      wrap.appendChild(avatar);
      wrap.appendChild(body);
      messages.appendChild(wrap);
      messages.scrollTop = messages.scrollHeight;
    }

    function appendMessage(text, type) {
      appendMessageToDom(text, type);

      // cập nhật lịch sử
      history.messages.push({ sender: type, text: text });
      history.conversationId = conversationId;
      persistHistory();
    }

    function renderHistory() {
      messages.innerHTML = '';
      if (!history.messages.length) {
        // nếu chưa có lịch sử, tạo 1 lời chào mặc định
        appendMessage(
          'Xin chào 👋, mình là trợ lý AI của PTIT eCommerce. Bạn có thể hỏi về tồn kho, giá, hoặc tình trạng đơn hàng của mình.',
          'bot'
        );
        return;
      }
      history.messages.forEach(function (m) {
        appendMessageToDom(m.text, m.sender === 'user' ? 'user' : 'bot');
      });
    }

    function isDebugEnabled() {
      try {
        var params = new URLSearchParams(window.location.search || '');
        return params.get('chatbot_debug') === '1' || params.get('debug_chatbot') === '1';
      } catch (_) {
        return false;
      }
    }

    function setLoading(is) {
      if (!sendBtn) return;
      sendBtn.disabled = is;
    }

    var typingEl = null;
    function showTyping() {
      if (!messages) return;
      if (typingEl) return;
      typingEl = document.createElement('div');
      typingEl.className = 'ai-chat-message ai-chat-message-bot';
      typingEl.innerHTML = `
        <div class="ai-chat-message-avatar"><i class="fa fa-robot"></i></div>
        <div class="ai-chat-message-body">
          <div class="ai-chat-message-text">
            <span class="typing-dot">●</span>
            <span class="typing-dot">●</span>
            <span class="typing-dot">●</span>
          </div>
        </div>
      `;
      messages.appendChild(typingEl);
      messages.scrollTop = messages.scrollHeight;
    }

    function hideTyping() {
      if (typingEl && typingEl.parentNode) {
        typingEl.parentNode.removeChild(typingEl);
      }
      typingEl = null;
    }

    async function sendMessage(evt) {
      evt.preventDefault();
      var text = (input.value || '').trim();
      if (!text) return;

      appendMessage(text, 'user');
      input.value = '';
      resizeInput();
      setLoading(true);
      showTyping();

      try {
        var csrf = document.querySelector('meta[name="csrf-token"]');
        var debugEnabled = isDebugEnabled();
        var url = '/api/chatbot/message' + (debugEnabled ? '?debug=1' : '');
        var res = await fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf ? csrf.content : ''
          },
          body: JSON.stringify({
            message: text,
            conversation_id: conversationId
          })
        });
        var data = await res.json();
        if (!res.ok) {
          appendMessageToDom(data.response || data.message || 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau.', 'bot', data.debug);
        } else {
          conversationId = data.conversation_id || conversationId;
          appendMessageToDom(data.response || '...', 'bot', data.debug);

          // cập nhật lịch sử (không lưu debug)
          history.messages.push({ sender: 'bot', text: data.response || '...' });
          history.conversationId = conversationId;
          persistHistory();
        }
      } catch (e) {
        appendMessage('Xin lỗi, tôi đang gặp sự cố kết nối. Vui lòng thử lại sau.', 'bot');
      } finally {
        setLoading(false);
        hideTyping();
      }
    }

    function resizeInput() {
      if (!input) return;
      input.style.height = 'auto';
      var max = 96;
      input.style.height = Math.min(input.scrollHeight, max) + 'px';
    }

    // render history lần đầu
    renderHistory();

    // Clear chat history on logout (persist until logout)
    (function bindLogoutClear() {
      var logoutLinks = document.querySelectorAll('a[href*="/auth/logout"]');
      for (var i = 0; i < logoutLinks.length; i++) {
        logoutLinks[i].addEventListener('click', function () {
          clearHistory();
        });
      }

      var logoutForm = document.getElementById('logout-form-frontend');
      if (logoutForm) {
        logoutForm.addEventListener('submit', function () {
          clearHistory();
        });
      }
    })();

    toggle.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', closeModal);
    form.addEventListener('submit', sendMessage);

    // Hỗ trợ Enter / Shift+Enter và auto-resize
    input.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage(e);
      }
    });
    input.addEventListener('input', resizeInput);
    resizeInput();
  }

  function init() {
    injectStyles();
    createWidget();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();



