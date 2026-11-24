<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
$currentPage = '/flashcards';
$pageTitle   = 'البطاقات الذكية';
$bp          = BASE_PATH;

include dirname(__DIR__) . '/includes/header.php';
include dirname(__DIR__) . '/includes/navbar.php';
?>

<div class="page-hero">
  <div class="hero-icon" style="background:rgba(255,255,255,0.15)">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="M12 4v16"></path><path d="M2 8h20"></path></svg>
  </div>
  <h1 class="hero-title">بطاقاتي التعليمية (Flashcards)</h1>
  <p class="hero-sub">أنشئ بطاقاتك الخاصة لحفظ المصطلحات والتعريفات المعقدة بسهولة</p>
</div>

<div class="container" style="max-width: 700px; padding-bottom: 60px;">

  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
    <h2 style="font-family:'Tajawal',sans-serif; color:var(--text);">البطاقات المحفوظة</h2>
    <div style="font-size:14px; color:var(--muted); font-weight:700;"><span id="fc-current">0</span> / <span id="fc-total">0</span></div>
  </div>

  <div class="flashcard-scene" style="perspective: 1000px; width:100%; height:350px; margin-bottom:30px; cursor:pointer; position:relative;" onclick="flipCard()">

    <button onclick="event.stopPropagation(); deleteCurrentCard()" id="btn-del" style="position:absolute; top:20px; left:20px; z-index:10; background:var(--danger); color:#fff; border:none; width:36px; height:36px; border-radius:10px; cursor:pointer; display:none; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(239, 68, 68, 0.3);">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
    </button>

    <div class="flashcard-inner" id="fc-inner" style="position:relative; width:100%; height:100%; text-align:center; transition:transform 0.6s cubic-bezier(0.4, 0, 0.2, 1); transform-style:preserve-3d;">

      <!-- Front (Question) -->
      <div class="flashcard-front" style="position:absolute; width:100%; height:100%; backface-visibility:hidden; background:var(--card); border:1px solid var(--border); border-radius:24px; box-shadow:0 10px 30px var(--shadow); display:flex; flex-direction:column; justify-content:center; align-items:center; padding:30px;">
        <div style="font-size:14px; color:var(--primary); font-weight:700; margin-bottom:16px;">الوجه الأمامي</div>
        <h3 id="fc-q" style="font-size:28px; color:var(--text); font-family:'Tajawal',sans-serif; line-height:1.5; margin:0;"></h3>
        <div style="margin-top:auto; font-size:12px; color:var(--muted); opacity:0.6;">اضغط لقلب البطاقة ومعرفة الإجابة</div>
      </div>

      <!-- Back (Answer) -->
      <div class="flashcard-back" style="position:absolute; width:100%; height:100%; backface-visibility:hidden; background:var(--primary-bg); border:1px solid var(--primary); border-radius:24px; box-shadow:0 10px 30px rgba(230,0,0,0.15); display:flex; flex-direction:column; justify-content:center; align-items:center; padding:30px; transform:rotateY(180deg);">
        <div style="font-size:14px; color:var(--primary); font-weight:700; margin-bottom:16px;">الوجه الخلفي</div>
        <p id="fc-a" style="font-size:20px; color:var(--text); line-height:1.7; font-family:'Cairo',sans-serif; margin:0;"></p>
      </div>

    </div>
  </div>

  <div style="display:flex; justify-content:space-between; gap:16px; margin-bottom: 40px;">
    <button onclick="prevCard()" style="flex:1; padding:16px; border-radius:16px; background:var(--bg); border:1px solid var(--border); color:var(--text); font-weight:700; font-size:16px; cursor:pointer; transition:0.2s;" onmouseover="this.style.background='var(--border)'" onmouseout="this.style.background='var(--bg)'">← السابق</button>
    <button onclick="nextCard()" style="flex:1; padding:16px; border-radius:16px; background:var(--primary); border:none; color:var(--card); font-weight:700; font-size:16px; cursor:pointer; box-shadow:0 4px 15px var(--shadow);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">التالي →</button>
  </div>

  <div class="card" style="padding: 24px; border-radius: 20px;">
    <h3 style="font-family:'Tajawal',sans-serif; color:var(--text); margin-top:0; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
      <span style="color:var(--primary);">+</span> إضافة بطاقة جديدة
    </h3>
    <input type="text" id="new-q" placeholder="المصطلح أو السؤال..." style="width:100%; padding:14px; margin-bottom:16px; border-radius:12px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:'Cairo',sans-serif; outline:none;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'" />
    <textarea id="new-a" placeholder="التعريف أو الإجابة..." style="width:100%; padding:14px; margin-bottom:16px; border-radius:12px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-family:'Cairo',sans-serif; resize:vertical; min-height:80px; outline:none;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'"></textarea>
    <button onclick="addCard()" class="btn-primary" style="width:100%; padding:14px;">💾 حفظ البطاقة</button>
  </div>

</div>

<script>
let cards = JSON.parse(localStorage.getItem('dalil-user-flashcards')) || [];
let currentIndex = 0;
let isFlipped = false;

function saveCards() {
    localStorage.setItem('dalil-user-flashcards', JSON.stringify(cards));
}

function renderCard() {
  const inner = document.getElementById('fc-inner');
  const btnDel = document.getElementById('btn-del');

  if(cards.length === 0) {
      document.getElementById('fc-q').innerText = "لا توجد بطاقات حتى الآن.";
      document.getElementById('fc-a').innerText = "قم بإضافة بطاقتك الأولى من النموذج بالأسفل.";
      document.getElementById('fc-total').innerText = '0';
      document.getElementById('fc-current').innerText = '0';
      btnDel.style.display = 'none';
      if(isFlipped) {
          inner.style.transform = 'rotateY(0deg)';
          isFlipped = false;
      }
      return;
  }

  btnDel.style.display = 'flex';
  document.getElementById('fc-total').innerText = cards.length;

  if(currentIndex >= cards.length) currentIndex = cards.length - 1;
  if(currentIndex < 0) currentIndex = 0;

  let delay = 0;
  if(isFlipped) {
    inner.style.transform = 'rotateY(0deg)';
    isFlipped = false;
    delay = 300;
  }

  setTimeout(() => {
    document.getElementById('fc-q').innerText = cards[currentIndex].q;
    document.getElementById('fc-a').innerText = cards[currentIndex].a;
    document.getElementById('fc-current').innerText = currentIndex + 1;
  }, delay);
}

function flipCard() {
  if (cards.length === 0) return;
  const inner = document.getElementById('fc-inner');
  isFlipped = !isFlipped;
  inner.style.transform = isFlipped ? 'rotateY(180deg)' : 'rotateY(0deg)';
}

function nextCard() {
  if(currentIndex < cards.length - 1) {
    currentIndex++;
    renderCard();
  }
}

function prevCard() {
  if(currentIndex > 0) {
    currentIndex--;
    renderCard();
  }
}

function addCard() {
    const q = document.getElementById('new-q').value.trim();
    const a = document.getElementById('new-a').value.trim();
    if(!q || !a) return alert("الرجاء إدخال السؤال والإجابة!");

    cards.push({q, a});
    saveCards();
    document.getElementById('new-q').value = '';
    document.getElementById('new-a').value = '';

    currentIndex = cards.length - 1;
    renderCard();
}

function deleteCurrentCard() {
    if(cards.length === 0) return;
    if(!confirm("هل أنت متأكد من حذف هذه البطاقة؟")) return;

    cards.splice(currentIndex, 1);
    saveCards();
    renderCard();
}

document.addEventListener('keydown', (e) => {
  if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
  if(e.key === 'ArrowLeft' || e.key === 'ArrowUp') nextCard();
  if(e.key === 'ArrowRight' || e.key === 'ArrowDown') prevCard();
  if(e.key === ' ' || e.key === 'Enter') { e.preventDefault(); flipCard(); }
});

renderCard();
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
