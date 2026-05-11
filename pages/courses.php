<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
$currentPage = '/courses';
$pageTitle   = 'المواد الدراسية';
$bp          = BASE_PATH;

$courses = db_all("SELECT c.*, (SELECT COUNT(*) FROM materials m WHERE m.course_id=c.id AND m.is_active=1) AS material_count FROM courses c WHERE c.is_active=1 ORDER BY c.sort_order, c.id");

include dirname(__DIR__) . '/includes/header.php';
include dirname(__DIR__) . '/includes/navbar.php';
?>

<div class="page-hero">
  <div class="hero-icon" style="background:linear-gradient(135deg,#1A7FD4,#0EA5C9)">📚</div>
  <h1 class="hero-title">المواد الدراسية</h1>
  <p class="hero-sub">كل مواد الفصل — ملفات، شرح، امتحانات قديمة ومزيد</p>
</div>

<div class="container">
  <?php if (empty($courses)): ?>
    <div class="empty-state"><div class="empty-icon">📚</div><div class="empty-text">لا توجد مواد مضافة بعد</div></div>
  <?php else: ?>
  <?php
  $bySemester = [];
  foreach ($courses as $c) {
      $key = ($c['semester'] ?: 'بدون فصل') . ($c['year'] ? ' · السنة '.$c['year'] : '');
      $bySemester[$key][] = $c;
  }
  foreach ($bySemester as $sem => $group): ?>
  <div style="margin-bottom:36px">
    <div style="font-family:'Tajawal',sans-serif;font-weight:900;font-size:16px;color:var(--muted);margin-bottom:16px;display:flex;align-items:center;gap:8px">
      <div style="height:2px;flex:1;background:var(--border)"></div>
      <?= e($sem) ?>
      <div style="height:2px;flex:1;background:var(--border)"></div>
    </div>
    <div class="card-grid">
      <?php foreach ($group as $c):
        $color = $c['color'] ?: '#1A7FD4';
        $bg    = $color.'22';
      ?>
      <a href="<?= $bp ?>/materials?course_id=<?= $c['id'] ?>" class="course-card fade-up" style="--course-color:<?= e($color) ?>">
        <div class="course-icon-wrap" style="background:<?= e($bg) ?>"><?= e($c['icon']?:'📚') ?></div>
        <div class="course-name"><?= e($c['name']) ?></div>
        <?php if ($c['code']): ?><span class="course-code"><?= e($c['code']) ?></span><?php endif; ?>
        <?php if ($c['description']): ?><div class="course-desc"><?= e(mb_substr($c['description'],0,90)) ?>...</div><?php endif; ?>
        <div class="course-footer">
          <span class="course-meta"><?= e($c['semester']??'') ?></span>
          <span class="course-count">📄 <?= (int)$c['material_count'] ?> ملف</span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
