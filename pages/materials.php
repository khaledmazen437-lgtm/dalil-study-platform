<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
$currentPage = '/materials';
$pageTitle   = 'المحتوى الدراسي';
$bp          = BASE_PATH;

$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$courses  = db_all("SELECT * FROM courses WHERE is_active=1 ORDER BY sort_order,id");

if ($courseId) {
    $course    = db_one("SELECT * FROM courses WHERE id=?", [$courseId]);
    if ($course) $pageTitle = $course['name'] . ' — المحتوى';
    $materials = db_all("SELECT * FROM materials WHERE course_id=? AND is_active=1 ORDER BY sort_order,id", [$courseId]);
} else {
    $course    = null;
    $materials = db_all("SELECT m.*, c.name AS course_name FROM materials m LEFT JOIN courses c ON c.id=m.course_id WHERE m.is_active=1 ORDER BY m.sort_order,m.id");
}

$typeMap = [
    'summary'       => ['ملخص',       '📋','badge-blue',   'ملخصات'],
    'slides'        => ['سلايدز',     '🖼️','badge-purple', 'سلايدز'],
    'video'         => ['يوتيوب',     '▶️', 'badge-red',    'فيديوهات'],
    'recording'     => ['تسجيل',      '🎙️','badge-teal',   'تسجيلات'],
    'weekly_summary'=> ['ملخص أسبوع','📅','badge-green',  'ملخصات الأسبوع'],
    'question'      => ['أسئلة',      '❓','badge-amber',  'أسئلة'],
    'exam'          => ['امتحان',     '📝','badge-amber',   'امتحانات'],
    'other'         => ['أخرى',       '📌','badge-gray',    'أخرى'],
];
$grouped = [];
foreach ($materials as $m) { $grouped[$m['type']][] = $m; }

include dirname(__DIR__) . '/includes/header.php';
include dirname(__DIR__) . '/includes/navbar.php';
?>

<div class="page-hero">
  <div class="hero-icon" style="background:linear-gradient(135deg,#8B5CF6,#7C3AED)"><?= $course ? e($course['icon']?:'📖') : '📄' ?></div>
  <h1 class="hero-title"><?= $course ? e($course['name']) : 'كل المحتوى' ?></h1>
  <?php if ($course && $course['code']): ?>
    <span class="badge badge-blue" style="margin:6px auto 0"><?= e($course['code']) ?></span>
  <?php endif; ?>
  <p class="hero-sub"><?= count($materials) ?> ملف ومحتوى متاح</p>
</div>

<div class="container">
  <?php if ($courseId === 0): ?>
  <div class="filter-tabs">
    <a href="<?= $bp ?>/materials" class="filter-tab <?= !$courseId?'active':'' ?>">🗂️ الكل</a>
    <?php foreach ($courses as $c): ?>
      <a href="<?= $bp ?>/materials?course_id=<?= $c['id'] ?>" class="filter-tab <?= $courseId===$c['id']?'active':'' ?>"><?= e($c['icon']?:'📚') ?> <?= e($c['name']) ?></a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if (empty($materials)): ?>
    <div class="empty-state"><div class="empty-icon">📄</div><div class="empty-text">لا يوجد محتوى بعد</div></div>
  <?php else: ?>
  <?php foreach ($typeMap as $type => [$label,$icon,$badge,$groupLabel]):
    if (!isset($grouped[$type])) continue;
    $rows = $grouped[$type];
  ?>
  <div style="margin-bottom:32px">
    <div class="section-header">
      <div class="section-icon" style="background:var(--primary-bg)"><?= $icon ?></div>
      <div>
        <div class="section-title"><?= e($groupLabel) ?></div>
        <div class="section-bar" style="background:var(--primary)"></div>
      </div>
      <span style="margin-right:auto;font-size:12px;color:var(--muted)"><?= count($rows) ?> عنصر</span>
    </div>
    <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;padding:4px 16px">
      <?php foreach ($rows as $m): ?>
      <div class="material-row">
        <div class="material-info">
          <div class="material-title"><?= e($m['title']) ?></div>
          <?php if (!empty($m['description'])): ?><div class="material-desc"><?= e($m['description']) ?></div><?php endif; ?>
          <?php if (!empty($m['course_name'])): ?><div class="material-desc">📚 <?= e($m['course_name']) ?></div><?php endif; ?>
        </div>
        <span class="badge <?= $badge ?>"><?= $icon ?> <?= e($label) ?></span>
        <a href="<?= e($m['url']) ?>" target="_blank" rel="noopener" class="btn-open">فتح ↗</a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
