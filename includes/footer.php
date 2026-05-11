<footer class="site-footer">
  <div>
    منصة <strong style="color:var(--primary)">دليل</strong> —
    <?= e(defined('SITE_SUB') ? SITE_SUB : 'مازن خالد · الحاسبات') ?>
    · <?= date('Y') ?>
  </div>
</footer>
</footer>

<!-- AI Assistant Widget -->
<div id="ai-widget-container" style="position: fixed; bottom: 80px; left: 20px; z-index: 1000; direction: rtl;">
    <!-- Chat Window -->
    <div id="ai-chat-window" style="display: none; width: 340px; height: 480px; background: var(--card); border: 1px solid var(--border); border-radius: 20px; box-shadow: 0 20px 40px var(--shadow-lg); flex-direction: column; overflow: hidden; margin-bottom: 16px; transform-origin: bottom left; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); opacity: 0; transform: scale(0.9) translateY(20px);">
        <!-- Header -->
        <div style="background: var(--primary); color: var(--card); padding: 16px 20px; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 32px; height: 32px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-1v2h2v-2a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-4v2h4a2 2 0 0 1 2-2v2H6v-2a2 2 0 0 1 2-2h4v-2H8a2 2 0 0 1-2-2V10a2 2 0 0 1 2-2h1V6a2 2 0 0 1 2-2h2V4a2 2 0 0 1-2-2z"></path></svg>
                </div>
                <div>
                    <div style="font-size: 14px; font-weight: 700; font-family: 'Tajawal', sans-serif;">مساعد الذكاء الاصطناعي</div>
                    <div style="font-size: 11px; opacity: 0.8;">متصل الآن</div>
                </div>
            </div>
            <button onclick="toggleAIChat()" style="background: none; border: none; color: var(--card); cursor: pointer; opacity: 0.8; transition: 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        
        <!-- Messages -->
        <div id="ai-messages" style="flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 16px; background: var(--bg); font-family: 'Cairo', sans-serif;">
            <div style="display: flex; gap: 10px; max-width: 85%;">
                <div style="width: 28px; height: 28px; background: var(--primary); color: var(--card); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 14px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-1v2h2v-2a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-4v2h4a2 2 0 0 1 2-2v2H6v-2a2 2 0 0 1 2-2h4v-2H8a2 2 0 0 1-2-2V10a2 2 0 0 1 2-2h1V6a2 2 0 0 1 2-2h2V4a2 2 0 0 1-2-2z"></path></svg>
                </div>
                <div style="background: var(--card); border: 1px solid var(--border); padding: 12px 16px; border-radius: 0 16px 16px 16px; font-size: 13px; color: var(--text); box-shadow: 0 2px 8px var(--shadow);">
                    أهلاً بك في منصة دليل! كيف يمكنني مساعدتك في دراستك اليوم؟
                </div>
            </div>
        </div>
        
        <!-- Input -->
        <div style="padding: 16px; background: var(--card); border-top: 1px solid var(--border); display: flex; gap: 8px;">
            <button id="ai-voice-btn" onclick="startVoiceRecognition()" style="width: 42px; height: 42px; background: var(--bg); color: var(--text); border: 1px solid var(--border); border-radius: 12px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.3s;" onmouseover="this.style.borderColor='var(--primary)'; this.style.color='var(--primary)';" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--text)';">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="23"></line><line x1="8" y1="23" x2="16" y2="23"></line></svg>
            </button>
            <input type="text" id="ai-input" placeholder="اكتب سؤالك هنا..." style="flex: 1; background: var(--bg); border: 1px solid var(--border); padding: 10px 16px; border-radius: 12px; font-family: 'Cairo', sans-serif; font-size: 13px; color: var(--text); outline: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
            <button onclick="sendAIMessage()" style="width: 42px; height: 42px; background: var(--primary); color: var(--card); border: none; border-radius: 12px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 12px var(--shadow);" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transform: rotate(180deg);"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
            </button>
        </div>
    </div>
    
    <!-- Floating Button -->
    <button id="ai-trigger" onclick="toggleAIChat()" style="width: 56px; height: 56px; background: var(--primary); color: var(--card); border: none; border-radius: 16px; box-shadow: 0 8px 24px var(--shadow-lg); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);" onmouseover="this.style.transform='scale(1.1) translateY(-4px)'" onmouseout="this.style.transform='scale(1) translateY(0)'">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path><circle cx="9" cy="11" r="1"></circle><circle cx="15" cy="11" r="1"></circle></svg>
    </button>
</div>

<script>
let aiChatOpen = false;
function toggleAIChat() {
    const windowEl = document.getElementById('ai-chat-window');
    aiChatOpen = !aiChatOpen;
    if (aiChatOpen) {
        windowEl.style.display = 'flex';
        setTimeout(() => {
            windowEl.style.opacity = '1';
            windowEl.style.transform = 'scale(1) translateY(0)';
            document.getElementById('ai-input').focus();
        }, 10);
    } else {
        windowEl.style.opacity = '0';
        windowEl.style.transform = 'scale(0.9) translateY(20px)';
        setTimeout(() => {
            windowEl.style.display = 'none';
        }, 300);
    }
}

async function sendAIMessage() {
    const input = document.getElementById('ai-input');
    const msg = input.value.trim();
    if (!msg) return;
    
    const msgsContainer = document.getElementById('ai-messages');
    
    // 1. عرض رسالة المستخدم في الشات
    const userDiv = document.createElement('div');
    userDiv.style.cssText = 'display: flex; gap: 10px; max-width: 85%; align-self: flex-end; flex-direction: row-reverse;';
    userDiv.innerHTML = `
        <div style="width: 28px; height: 28px; background: var(--border); color: var(--text); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 14px;">👤</div>
        <div style="background: var(--accent); color: #fff; padding: 12px 16px; border-radius: 16px 0 16px 16px; font-size: 13px; box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);">${msg}</div>
    `;
    msgsContainer.appendChild(userDiv);
    input.value = '';
    msgsContainer.scrollTop = msgsContainer.scrollHeight;
    
    // 2. إظهار حالة "جاري التفكير..." للمستخدم
    const typingDiv = document.createElement('div');
    typingDiv.id = 'ai-typing';
    typingDiv.style.cssText = 'display: flex; gap: 10px; max-width: 85%;';
    typingDiv.innerHTML = `
        <div style="width: 28px; height: 28px; background: var(--primary); color: var(--card); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 14px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-1v2h2v-2a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-4v2h4a2 2 0 0 1 2-2v2H6v-2a2 2 0 0 1 2-2h4v-2H8a2 2 0 0 1-2-2V10a2 2 0 0 1 2-2h1V6a2 2 0 0 1 2-2h2V4a2 2 0 0 1-2-2z"></path></svg>
        </div>
        <div style="background: var(--card); border: 1px solid var(--border); padding: 12px 16px; border-radius: 0 16px 16px 16px; font-size: 13px; color: var(--text); opacity: 0.7; font-style: italic;">
            جاري التفكير...
        </div>
    `;
    msgsContainer.appendChild(typingDiv);
    msgsContainer.scrollTop = msgsContainer.scrollHeight;

    try {
        // 3. إرسال الطلب لملف الـ PHP 
        const response = await fetch('chat_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ message: msg })
        });

        const data = await response.json();
        
        // 4. إزالة حالة "جاري التفكير..."
        const typingEl = document.getElementById('ai-typing');
        if(typingEl) typingEl.remove();

        // 5. عرض رد الذكاء الاصطناعي
        const aiResponseText = data.reply ? data.reply.replace(/\n/g, '<br>') : "حدث خطأ غير متوقع.";

        const aiDiv = document.createElement('div');
        aiDiv.style.cssText = 'display: flex; gap: 10px; max-width: 85%;';
        aiDiv.innerHTML = `
            <div style="width: 28px; height: 28px; background: var(--primary); color: var(--card); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 14px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-1v2h2v-2a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-4v2h4a2 2 0 0 1 2-2v2H6v-2a2 2 0 0 1 2-2h4v-2H8a2 2 0 0 1-2-2V10a2 2 0 0 1 2-2h1V6a2 2 0 0 1 2-2h2V4a2 2 0 0 1-2-2z"></path></svg>
            </div>
            <div style="background: var(--card); border: 1px solid var(--border); padding: 12px 16px; border-radius: 0 16px 16px 16px; font-size: 13px; color: var(--text); box-shadow: 0 2px 8px var(--shadow); line-height: 1.6;">
                ${aiResponseText}
            </div>
        `;
        msgsContainer.appendChild(aiDiv);
        msgsContainer.scrollTop = msgsContainer.scrollHeight;

    } catch (error) {
        console.error("Error calling AI API:", error);
        const typingEl = document.getElementById('ai-typing');
        if(typingEl) typingEl.remove();
        
        const errorDiv = document.createElement('div');
        errorDiv.style.cssText = 'display: flex; gap: 10px; max-width: 85%;';
        errorDiv.innerHTML = `
            <div style="width: 28px; height: 28px; background: #ef4444; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 14px;">!</div>
            <div style="background: #fee2e2; border: 1px solid #fca5a5; padding: 12px 16px; border-radius: 0 16px 16px 16px; font-size: 13px; color: #991b1b;">
                عذراً، يبدو أن هناك مشكلة في الاتصال. تأكد من اتصالك بالإنترنت وجرب مرة أخرى.
            </div>
        `;
        msgsContainer.appendChild(errorDiv);
        msgsContainer.scrollTop = msgsContainer.scrollHeight;
    }
}

document.getElementById('ai-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') sendAIMessage();
});

// Voice AI
function startVoiceRecognition() {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) {
        alert("متصفحك لا يدعم التعرف على الصوت. جرب استخدام Google Chrome.");
        return;
    }
    
    const recognition = new SpeechRecognition();
    recognition.lang = 'ar-SA';
    recognition.interimResults = false;
    
    const btn = document.getElementById('ai-voice-btn');
    const originalSvg = btn.innerHTML;
    
    recognition.onstart = function() {
        btn.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="var(--danger)" stroke="var(--danger)" stroke-width="2"><circle cx="12" cy="12" r="8"></circle></svg>`;
        btn.style.animation = 'pulse 1.5s infinite';
    };
    
    recognition.onresult = function(event) {
        const text = event.results[0][0].transcript;
        document.getElementById('ai-input').value = text;
        setTimeout(sendAIMessage, 500);
    };
    
    recognition.onerror = function(event) {
        console.error("Speech recognition error", event.error);
    };
    
    recognition.onend = function() {
        btn.innerHTML = originalSvg;
        btn.style.animation = '';
    };
    
    recognition.start();
}
</script>

<!-- Spotlight Search Modal -->
<div id="spotlight-overlay" onclick="if(event.target===this) closeSpotlight()">
    <div class="spotlight-modal">
        <div class="spotlight-input-wrap">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" id="spotlight-input" class="spotlight-input" placeholder="ابحث عن مواد، امتحانات، أخبار... (Cmd+K)" autocomplete="off">
            <span style="font-size: 11px; background: var(--bg); padding: 4px 8px; border-radius: 6px; color: var(--muted); border: 1px solid var(--border);">ESC</span>
        </div>
        <div class="spotlight-results" id="spotlight-results">
            <!-- Mock Results -->
            <a href="<?= BASE_PATH ?>/courses" class="spotlight-item">
                <div class="spotlight-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg></div>
                <div style="flex:1;">
                    <div style="font-size: 14px; font-weight: 700;">المواد الدراسية</div>
                    <div style="font-size: 11px; color: var(--muted);">تصفح جميع مواد الكلية</div>
                </div>
            </a>
            <a href="<?= BASE_PATH ?>/focus" class="spotlight-item">
                <div class="spotlight-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></div>
                <div style="flex:1;">
                    <div style="font-size: 14px; font-weight: 700;">مركز التركيز (Pomodoro)</div>
                    <div style="font-size: 11px; color: var(--muted);">ابدأ جلسة دراسة جديدة</div>
                </div>
            </a>
            <a href="<?= BASE_PATH ?>/gpa" class="spotlight-item">
                <div class="spotlight-icon"><span style="font-size: 16px;">💯</span></div>
                <div style="flex:1;">
                    <div style="font-size: 14px; font-weight: 700;">حاسبة الـ GPA</div>
                    <div style="font-size: 11px; color: var(--muted);">احسب معدلك التراكمي والفصلي</div>
                </div>
            </a>
        </div>
    </div>
</div>

<script>
// --- Spotlight Search Logic ---
const spotlightOverlay = document.getElementById('spotlight-overlay');
const spotlightInput = document.getElementById('spotlight-input');

function openSpotlight() {
    spotlightOverlay.classList.add('active');
    setTimeout(() => spotlightInput.focus(), 100);
}
function closeSpotlight() {
    spotlightOverlay.classList.remove('active');
    spotlightInput.value = '';
}

document.addEventListener('keydown', e => {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        openSpotlight();
    }
    if (e.key === 'Escape') closeSpotlight();
});

// Simple client-side mock filter for spotlight
spotlightInput.addEventListener('input', e => {
    const term = e.target.value.toLowerCase();
    const items = document.querySelectorAll('.spotlight-item');
    items.forEach(item => {
        const text = item.innerText.toLowerCase();
        item.style.display = text.includes(term) ? 'flex' : 'none';
    });
});
</script>

<!-- Intro.js for Onboarding -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/intro.min.js"></script>

<!-- PWA Service Worker Registration -->
<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('<?= BASE_PATH ?>/sw.js')
      .then(registration => {
        console.log('SW registered: ', registration);
      })
      .catch(registrationError => {
        console.log('SW registration failed: ', registrationError);
      });
  });
}
</script>

<script src="<?= BASE_PATH ?>/assets/main.js"></script>
</body>
</html>