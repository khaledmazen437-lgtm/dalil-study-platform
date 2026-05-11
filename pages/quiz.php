<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
$currentPage = '/quiz';
$pageTitle   = 'الاختبارات الذكية التفاعلية';
$bp          = BASE_PATH;

include dirname(__DIR__) . '/includes/header.php';
include dirname(__DIR__) . '/includes/navbar.php';

$quizzesFile = dirname(__DIR__) . '/admin/quizzes.json';
$quizzes = file_exists($quizzesFile) ? json_decode(file_get_contents($quizzesFile), true) : [];
?>

<div class="page-hero">
  <div class="hero-icon" style="background:rgba(255,255,255,0.15)">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
  </div>
  <h1 class="hero-title">الاختبارات الذكية</h1>
  <p class="hero-sub">اختبر معلوماتك في المواد الدراسية بأسلوب تفاعلي وتوقيت محدد</p>
</div>

<div class="container" style="max-width:800px; padding-bottom:60px;">
  
  <div id="quiz-list-view">
    <?php if(empty($quizzes)): ?>
      <div style="text-align:center; padding:40px; color:var(--muted); background:var(--card); border-radius:16px; border:1px solid var(--border);">
        لا توجد اختبارات متاحة حالياً.
      </div>
    <?php else: ?>
      <div style="display:grid; gap:16px;">
        <?php foreach($quizzes as $idx => $q): ?>
        <div class="card" style="padding:20px; display:flex; justify-content:space-between; align-items:center; border-radius:16px; box-shadow:0 4px 15px var(--shadow);">
          <div>
            <div style="font-size:12px; color:var(--primary); font-weight:700; margin-bottom:6px;"><?= e($q['course']??'') ?></div>
            <h3 style="margin:0 0 8px 0; font-family:'Tajawal',sans-serif; color:var(--text);"><?= e($q['title']??'') ?></h3>
            <div style="font-size:13px; color:var(--muted);">عدد الأسئلة: <?= count($q['questions']??[]) ?> سؤال</div>
          </div>
          <button class="btn-primary" onclick="startQuiz(<?= $idx ?>)" style="padding:12px 24px;">بدء الاختبار 🚀</button>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <div id="quiz-active-view" style="display:none; background:var(--card); border:1px solid var(--border); border-radius:16px; padding:30px; box-shadow:0 10px 30px var(--shadow-lg);">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <div style="font-weight:700; color:var(--text); font-size:18px;" id="q-title-display">العنوان</div>
      <div style="background:var(--primary-bg); color:var(--primary); padding:6px 16px; border-radius:20px; font-weight:700; font-family:monospace; font-size:16px;" id="q-timer">00:00</div>
    </div>
    
    <!-- Progress Bar -->
    <div style="width:100%; height:8px; background:var(--border); border-radius:4px; margin-bottom:30px; overflow:hidden;">
      <div id="q-progress" style="height:100%; width:0%; background:var(--primary); transition:width 0.3s ease;"></div>
    </div>

    <!-- Question Container -->
    <div id="q-container"></div>
    
  </div>

</div>

<script>
const quizzes = <?= json_encode($quizzes) ?>;
let currentQuiz = null;
let currentQuestionIndex = 0;
let score = 0;
let timerInterval = null;
let secondsElapsed = 0;

function startQuiz(index) {
  currentQuiz = quizzes[index];
  if(!currentQuiz || !currentQuiz.questions || currentQuiz.questions.length === 0) return;
  
  currentQuestionIndex = 0;
  score = 0;
  secondsElapsed = 0;
  
  document.getElementById('quiz-list-view').style.display = 'none';
  document.getElementById('quiz-active-view').style.display = 'block';
  document.getElementById('q-title-display').innerText = currentQuiz.title;
  
  timerInterval = setInterval(() => {
    secondsElapsed++;
    const m = Math.floor(secondsElapsed / 60).toString().padStart(2, '0');
    const s = (secondsElapsed % 60).toString().padStart(2, '0');
    document.getElementById('q-timer').innerText = `${m}:${s}`;
  }, 1000);
  
  showQuestion();
}

function showQuestion() {
  if (currentQuestionIndex >= currentQuiz.questions.length) {
    endQuiz();
    return;
  }
  
  const q = currentQuiz.questions[currentQuestionIndex];
  const percent = ((currentQuestionIndex) / currentQuiz.questions.length) * 100;
  document.getElementById('q-progress').style.width = percent + '%';
  
  let html = `
    <h2 style="font-family:'Tajawal',sans-serif; color:var(--text); margin-bottom:24px; line-height:1.5;">${currentQuestionIndex + 1}. ${esc(q.q)}</h2>
    <div style="display:flex; flex-direction:column; gap:12px;">
  `;
  
  q.options.forEach((opt, i) => {
    html += `
      <button class="quiz-opt" onclick="selectAnswer(${i}, ${q.answer})" style="text-align:right; padding:16px; background:var(--bg); border:2px solid var(--border); border-radius:12px; font-size:16px; font-family:'Cairo',sans-serif; color:var(--text); cursor:pointer; transition:0.2s;">
        ${esc(opt)}
      </button>
    `;
  });
  
  html += `</div>`;
  document.getElementById('q-container').innerHTML = html;
}

function selectAnswer(selectedIndex, correctIndex) {
  const opts = document.querySelectorAll('.quiz-opt');
  opts.forEach(btn => {
    btn.disabled = true;
    btn.style.cursor = 'default';
  });
  
  if (selectedIndex === correctIndex) {
    opts[selectedIndex].style.borderColor = 'var(--success)';
    opts[selectedIndex].style.background = 'rgba(16, 185, 129, 0.1)';
    score++;
  } else {
    opts[selectedIndex].style.borderColor = 'var(--danger)';
    opts[selectedIndex].style.background = 'rgba(239, 68, 68, 0.1)';
    opts[correctIndex].style.borderColor = 'var(--success)';
    opts[correctIndex].style.background = 'rgba(16, 185, 129, 0.1)';
  }
  
  setTimeout(() => {
    currentQuestionIndex++;
    showQuestion();
  }, 1200);
}

function endQuiz() {
  clearInterval(timerInterval);
  document.getElementById('q-progress').style.width = '100%';
  
  const m = Math.floor(secondsElapsed / 60).toString().padStart(2, '0');
  const s = (secondsElapsed % 60).toString().padStart(2, '0');
  
  const percentage = Math.round((score / currentQuiz.questions.length) * 100);
  let msg = '';
  if(percentage >= 85) msg = 'ممتاز جداً! 🎉';
  else if(percentage >= 65) msg = 'جيد، يمكنك التحسن 👍';
  else msg = 'تحتاج إلى المراجعة مرة أخرى 📚';
  
  document.getElementById('q-container').innerHTML = `
    <div style="text-align:center; padding:20px;">
      <div style="font-size:60px; margin-bottom:20px;">${percentage >= 85 ? '🏆' : '🎯'}</div>
      <h2 style="font-family:'Tajawal',sans-serif; color:var(--text); margin-bottom:10px;">انتهى الاختبار</h2>
      <p style="color:var(--muted); font-size:16px; margin-bottom:30px;">${msg}</p>
      
      <div style="display:flex; justify-content:center; gap:20px; margin-bottom:30px;">
        <div style="background:var(--bg); border:1px solid var(--border); padding:20px; border-radius:16px; min-width:120px;">
          <div style="font-size:28px; font-weight:800; color:var(--primary); margin-bottom:4px;">${percentage}%</div>
          <div style="font-size:13px; color:var(--muted);">النتيجة</div>
        </div>
        <div style="background:var(--bg); border:1px solid var(--border); padding:20px; border-radius:16px; min-width:120px;">
          <div style="font-size:28px; font-weight:800; color:var(--text); margin-bottom:4px;">${m}:${s}</div>
          <div style="font-size:13px; color:var(--muted);">الزمن المستغرق</div>
        </div>
      </div>
      
      <button class="btn-primary" onclick="location.reload()" style="padding:14px 30px; font-size:16px; margin:0 auto;">العودة للقائمة الرئيسية</button>
    </div>
  `;
}

function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
