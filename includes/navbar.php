<?php
$bp = defined('BASE_PATH') ? BASE_PATH : '';
try {
    $tickerRows = db_all("SELECT message FROM ticker_messages WHERE is_active = 1 ORDER BY sort_order, id");
} catch (Throwable $e) { $tickerRows = []; }

$navLinks = [
  ['href'=> $bp . '/',           'label'=>'الرئيسية',     'key'=>'/'],
  ['href'=> $bp . '/courses',    'label'=>'المواد',        'key'=>'/courses'],
  ['href'=> $bp . '/exams',      'label'=>'الامتحانات',   'key'=>'/exams'],
  ['href'=> $bp . '/questions',  'label'=>'الأسئلة',       'key'=>'/questions'],
  ['href'=> $bp . '/gpa',        'label'=>'حاسبة GPA',    'key'=>'/gpa'],
  ['href'=> $bp . '/focus',      'label'=>'مركز التركيز', 'key'=>'/focus'],
  ['href'=> $bp . '/progress',   'label'=>'تقدمي',         'key'=>'/progress'],
  ['href'=> $bp . '/notes',      'label'=>'ملاحظاتي',       'key'=>'/notes'],
  ['href'=> $bp . '/flashcards', 'label'=>'البطاقات',      'key'=>'/flashcards'],
  ['href'=> $bp . '/resources',  'label'=>'الموارد',       'key'=>'/resources'],
  ['href'=> $bp . '/news',       'label'=>'الأخبار',       'key'=>'/news'],
];
?>

<?php if ($tickerRows): ?>
<div class="ticker-bar">
  <div class="ticker-track">
    <?php $msgs = array_column($tickerRows,'message'); $all = implode(' ⬥ ', array_merge($msgs,$msgs)); ?>
    <span class="ticker-item">📢 <?= e($all) ?></span>
    <span class="ticker-item">📢 <?= e($all) ?></span>
  </div>
</div>
<?php endif; ?>

<nav class="navbar">
  <div class="navbar-inner">
    <div class="nav-brand">
      <a href="<?= $bp ?>/" class="brand-logo">د</a>
      <div class="brand-text">
        <span class="brand-name"><?= e(defined('SITE_NAME')?SITE_NAME:'دليل') ?></span>
        <span class="brand-sub"><?= e(defined('SITE_SUB')?SITE_SUB:'مازن خالد') ?></span>
      </div>
    </div>

    <div class="nav-links">
      <?php foreach ($navLinks as $n): ?>
        <a href="<?= e($n['href']) ?>" class="nav-link<?= active($n['key']) ?>"><?= e($n['label']) ?></a>
      <?php endforeach; ?>
    </div>

    <div class="nav-right">
      <button id="tour-search-btn" class="btn-theme" onclick="openSpotlight()" title="البحث (Cmd+K)" style="display:flex; gap:6px; width:auto; padding:0 12px; font-size:13px; font-weight:700; color:var(--muted);">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        <span style="display:none;" id="search-lbl">بحث</span>
      </button>
      <style>
        @media (max-width: 400px) {
          #tour-level-badge { display: none !important; }
          #tour-search-btn { padding: 0 8px !important; }
        }
      </style>
      <div id="tour-level-badge" style="display:flex; align-items:center; background:var(--primary-bg); color:var(--primary); padding:6px 12px; border-radius:12px; font-size:13px; font-weight:800; font-family:'Tajawal',sans-serif; gap:6px; cursor:default;" title="نقاطك التفاعلية">
        <span id="user-level-text">مستوى 1</span>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
      </div>
      <button class="btn-theme" id="btn-theme" onclick="toggleTheme()" title="تغيير الوضع" style="display:flex; align-items:center; justify-content:center; padding:0 8px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
      </button>
      <button class="hamburger" onclick="toggleMenu()">☰</button>
    </div>
  </div>

  <div class="mobile-menu" id="mobile-menu">
    <?php foreach ($navLinks as $n): ?>
      <a href="<?= e($n['href']) ?>" class="mobile-link<?= active($n['key']) ?>"><?= e($n['label']) ?></a>
    <?php endforeach; ?>
  </div>
</nav>

<nav class="bottom-nav">
  <a href="<?= $bp ?>/" class="bottom-tab<?= active('/') ?>">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
    الرئيسية
  </a>
  <a href="<?= $bp ?>/courses" class="bottom-tab<?= active('/courses') ?>">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
    المواد
  </a>
  <a href="<?= $bp ?>/exams" class="bottom-tab<?= active('/exams') ?>">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
    امتحانات
  </a>
  <a href="<?= $bp ?>/notes" class="bottom-tab<?= active('/notes') ?>">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
    ملاحظاتي
  </a>
  <a href="<?= $bp ?>/resources" class="bottom-tab<?= active('/resources') ?>">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
    الموارد
  </a>
</nav>
