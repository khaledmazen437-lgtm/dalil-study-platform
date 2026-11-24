<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
$currentPage = '/';
$pageTitle   = 'الرئيسية';
$bp          = BASE_PATH;

$courses = db_all("SELECT c.*, (SELECT COUNT(*) FROM materials m WHERE m.course_id=c.id AND m.is_active=1) AS material_count FROM courses c WHERE c.is_active=1 ORDER BY c.sort_order, c.id LIMIT 8");
$news    = db_all("SELECT * FROM news WHERE is_active=1 ORDER BY created_at DESC LIMIT 6");
$stats   = [
    'courses'  => db_val("SELECT COUNT(*) FROM courses WHERE is_active=1"),
    'materials'=> db_val("SELECT COUNT(*) FROM materials WHERE is_active=1"),
    'exams'    => db_val("SELECT COUNT(*) FROM past_exams WHERE is_active=1"),
    'questions'=> db_val("SELECT COUNT(*) FROM questions"),
];

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<!-- Hero (Ta3limy Red) -->
<div style="background: linear-gradient(80deg, var(--accent) -5.65%, var(--primary) 99.56%); border-bottom: none; padding: 100px 20px 80px; text-align: center; position: relative; overflow: hidden;">
  <div style="display:inline-flex;align-items:center;justify-content:center;width:64px;height:64px;border-radius:12px;background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.3); font-family:'Tajawal',sans-serif;font-weight:900;font-size:32px;color:#fff;margin-bottom:24px; transition: 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">د</div>
  <h1 style="font-family:'Tajawal',sans-serif;font-weight:900;font-size:clamp(2.5rem,7vw,4rem);color:#fff;margin-bottom:16px; letter-spacing: -1px; line-height: 1.1;">دليل للتعليم المتقدم</h1>
  <p style="font-size:16px;font-weight: 500;color:rgba(255,255,255,0.9);margin-bottom:40px; max-width: 500px; margin-left: auto; margin-right: auto; line-height: 1.8;">منصة متكاملة مصممة بأحدث التقنيات لتقديم تجربة دراسية احترافية لطلاب كلية الحاسبات والمعلومات.</p>
  <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap">
    <a href="<?= $bp ?>/courses" style="display:inline-flex;align-items:center;gap:8px;padding:12px 40px;border-radius:8px;background:#fff;color:var(--primary);font-weight:700;font-size:16px; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
      تصفح المواد
    </a>
    <a href="<?= $bp ?>/exams" style="display:inline-flex;align-items:center;gap:8px;padding:12px 40px;border-radius:8px;background:#000000;border:1px solid #000000;color:#fff;font-weight:700;font-size:16px; transition: all 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.3);" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 20px rgba(0,0,0,0.4)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 15px rgba(0,0,0,0.3)'">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
      الامتحانات السابقة
    </a>
  </div>
</div>

<style>
  .stats-grid {
    max-width: 900px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1px;
    background: var(--border);
  }
  @media (max-width: 600px) {
    .stats-grid {
      grid-template-columns: repeat(2, 1fr);
    }
    body, html {
      overflow-x: hidden;
    }
  }
</style>
<div style="background:var(--card);border-bottom:1px solid var(--border); overflow: hidden;">
  <div class="stats-grid">
    <?php
    $statsData = [
      ['','مادة',   $stats['courses'],   '#1A7FD4'],
      ['','محتوى',  $stats['materials'], '#8B5CF6'],
      ['','امتحان', $stats['exams'],     '#F97316'],
      ['','أسئلة',  $stats['questions'], '#10B981'],
    ];
    foreach ($statsData as $s): ?>
    <div style="background:var(--card);padding:18px 12px;text-align:center">
      <div style="font-size:22px;margin-bottom:4px"><?= $s[0] ?></div>
      <div style="font-family:'Tajawal',sans-serif;font-weight:900;font-size:22px;color:<?= $s[3] ?>"><?= $s[2] ?></div>
      <div style="font-size:11px;color:var(--muted)"><?= $s[1] ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="container">

  <div style="margin-bottom:40px; margin-top: 30px;">
    <div class="section-header">
      <div class="section-icon" style="background:#FEE2E2">⏳</div>
      <div>
        <div class="section-title">العد التنازلي لامتحانات الفاينل</div>
        <div class="section-bar" style="background:#EF4444"></div>
      </div>
    </div>

    <style>
      .countdown-wrapper {
        background: linear-gradient(135deg, var(--card) 0%, var(--bg) 100%);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 30px 20px;
        text-align: center;
        box-shadow: 0 10px 30px var(--shadow);
      }
      .countdown-block {
        background: var(--primary);
        color: #fff;
        min-width: 80px;
        padding: 15px 10px;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
      }
      .countdown-val {
        font-size: 32px;
        font-weight: 900;
        font-family: 'Tajawal', sans-serif;
        line-height: 1.2;
        margin-bottom: 4px;
      }
      .countdown-label {
        font-size: 13px;
        font-weight: 600;
        font-family: 'Cairo', sans-serif;
        opacity: 0.9;
      }
    </style>

    <div class="countdown-wrapper">
      <h3 style="margin-top:0; font-family:'Tajawal', sans-serif; font-weight:800; font-size: 20px; margin-bottom: 24px; color: var(--text);">تبدا الامتحانات 19 مايو </h3>
      <div id="final-countdown" style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap; direction: ltr;">
        <div class="countdown-block">
          <span id="cd-seconds" class="countdown-val">00</span>
          <span class="countdown-label">ثانية</span>
        </div>
        <div class="countdown-block">
          <span id="cd-minutes" class="countdown-val">00</span>
          <span class="countdown-label">دقيقة</span>
        </div>
        <div class="countdown-block">
          <span id="cd-hours" class="countdown-val">00</span>
          <span class="countdown-label">ساعة</span>
        </div>
        <div class="countdown-block">
          <span id="cd-days" class="countdown-val">00</span>
          <span class="countdown-label">يوم</span>
        </div>
      </div>
    </div>

    <script>
      // تحديد الموعد: 20 مايو من العام الحالي الساعة 10:00 صباحاً
      const targetDate = new Date("May 19, <?= date('Y') ?> 10:00:00").getTime();

      const timerInterval = setInterval(function() {
        const now = new Date().getTime();
        const distance = targetDate - now;

        // إذا انتهى الوقت
        if (distance < 0) {
          clearInterval(timerInterval);
          document.getElementById("final-countdown").innerHTML = "<div style='font-size:22px; font-weight:800; font-family:Tajawal; color:#EF4444; width:100%; padding: 10px;'>بدأت الامتحانات! بالتوفيق لجميع الطلاب 🎉</div>";
          return;
        }

        // حساب الأيام، الساعات، الدقائق، والثواني
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // تحديث الأرقام في واجهة المستخدم مع إضافة صفر إذا كان الرقم أقل من 10
        document.getElementById("cd-days").innerText = days < 10 ? "0" + days : days;
        document.getElementById("cd-hours").innerText = hours < 10 ? "0" + hours : hours;
        document.getElementById("cd-minutes").innerText = minutes < 10 ? "0" + minutes : minutes;
        document.getElementById("cd-seconds").innerText = seconds < 10 ? "0" + seconds : seconds;
      }, 1000);
    </script>
  </div>
  <!-- End Countdown Timer Section -->

  <?php if ($courses): ?>
  <div style="margin-bottom:40px">
    <div class="section-header">
      <div class="section-icon" style="background:#D8EDFF">📚</div>
      <div>
        <div class="section-title">المواد الدراسية</div>
        <div class="section-bar" style="background:#1A7FD4"></div>
      </div>
      <a href="<?= $bp ?>/courses" style="margin-right:auto;font-size:12px;font-weight:700;color:var(--primary);background:var(--primary-bg);padding:5px 14px;border-radius:9px">عرض الكل ←</a>
    </div>
    <div class="card-grid">
      <?php foreach ($courses as $c):
        $color = $c['color'] ?: '#1A7FD4';
        $bg    = $color . '22';
      ?>
      <a href="<?= $bp ?>/materials?course_id=<?= $c['id'] ?>" class="course-card fade-up" style="--course-color:<?= e($color) ?>">
        <div class="course-icon-wrap" style="background:<?= e($bg) ?>"><?= e($c['icon'] ?: '📚') ?></div>
        <div class="course-name"><?= e($c['name']) ?></div>
        <?php if ($c['code']): ?><span class="course-code"><?= e($c['code']) ?></span><?php endif; ?>
        <?php if ($c['description']): ?><div class="course-desc"><?= e(mb_substr($c['description'],0,80)) ?>...</div><?php endif; ?>
        <div class="course-footer">
          <span class="course-meta"><?= e($c['semester'] ?? '') ?> <?= e($c['year'] ? '· السنة '.$c['year'] : '') ?></span>
          <span class="course-count">📄 <?= (int)$c['material_count'] ?> ملف</span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <div style="margin-bottom:40px">
    <div class="section-header">
      <div class="section-icon" style="background:#FEF3C7"></div>
      <div>
        <div class="section-title">وصول سريع</div>
        <div class="section-bar" style="background:#F59E0B"></div>
      </div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px">
      <?php
      $quick = [
        [$bp.'/weekly',    'https://images.unsplash.com/photo-1606326608606-aa0b62935f2b?auto=format&fit=crop&w=200&q=80','ملخص الأسبوع'],
        [$bp.'/quiz',      'https://images.unsplash.com/photo-1606326608606-aa0b62935f2b?auto=format&fit=crop&w=200&q=80','الاختبارات الذكية'],
        [$bp.'/gpa',       'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=200&q=80','حساب الـ GPA'],
        [$bp.'/focus',     'https://images.unsplash.com/photo-1606326608606-aa0b62935f2b?auto=format&fit=crop&w=200&q=80','مركز التركيز'],
        [$bp.'/exams',     'https://images.unsplash.com/photo-1606326608606-aa0b62935f2b?auto=format&fit=crop&w=200&q=80','الامتحانات السابقة'],
        [$bp.'/flashcards','https://images.unsplash.com/photo-1606326608606-aa0b62935f2b?auto=format&fit=crop&w=200&q=80','البطاقات الذكية'],
      ];
      foreach ($quick as [$href,$img,$lbl]): ?>
      <a href="<?= e($href) ?>" style="display:flex;flex-direction:column;align-items:center;gap:12px;padding:20px 16px;background:var(--card);border:1px solid var(--border);border-radius:16px;text-align:center;transition:all 0.3s ease; box-shadow: 0 4px 20px var(--shadow);"
         onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 30px var(--shadow-lg)';this.style.borderColor='var(--primary)'"
         onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 20px var(--shadow)';this.style.borderColor='var(--border)'">
        <div style="width:60px;height:60px;border-radius:14px;background:var(--bg);display:flex;align-items:center;justify-content:center;overflow:hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
            <img src="<?= $img ?>" alt="<?= $lbl ?>" style="width:100%; height:100%; object-fit:cover; transition: 0.3s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
        </div>
        <span style="font-size:14px;font-weight:700;color:var(--text); font-family: 'Tajawal', sans-serif;"><?= e($lbl) ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if ($news): ?>
  <div>
    <div class="section-header">
      <div class="section-icon" style="background:#CFFAFE">📣</div>
      <div>
        <div class="section-title">آخر الأخبار</div>
        <div class="section-bar" style="background:#0EA5C9"></div>
      </div>
      <a href="<?= $bp ?>/news" style="margin-right:auto;font-size:12px;font-weight:700;color:var(--primary);background:var(--primary-bg);padding:5px 14px;border-radius:9px">عرض الكل ←</a>
    </div>
    <?php foreach ($news as $n):
      $types = ['news'=>'','announcement'=>' news-type-announcement','important'=>' news-type-important'];
      $cls   = $types[$n['type']] ?? '';
    ?>
    <div class="news-card<?= $cls ?>">
      <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:6px">
        <div class="news-title"><?= e($n['title']) ?></div>
        <?php
          $typeLabels = ['news'=>['📰','خبر','badge-blue'],'announcement'=>['📢','إعلان','badge-amber'],'important'=>['❗','هام','badge-red']];
          [$tIcon,$tLbl,$tCls] = $typeLabels[$n['type']] ?? ['📌','أخرى','badge-gray'];
        ?>
        <span class="badge <?= $tCls ?>"><?= $tIcon ?> <?= e($tLbl) ?></span>
      </div>
      <?php if ($n['content']): ?>
        <div class="news-content"><?= e(mb_substr($n['content'],0,200)) ?></div>
      <?php endif; ?>
      <div class="news-date">🕐 <?= date('Y/m/d',strtotime($n['created_at'])) ?></div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

</div>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-E6TQ2XPHJG"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-E6TQ2XPHJG');
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
