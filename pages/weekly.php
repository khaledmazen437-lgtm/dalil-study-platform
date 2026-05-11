<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
$currentPage = '/weekly';
$pageTitle   = 'ملخص الأسبوع';
$bp          = BASE_PATH;

$courses  = db_all("SELECT * FROM courses WHERE is_active=1 ORDER BY sort_order,id");
$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

$where = []; $params = [];
if ($courseId) { $where[] = 'w.course_id=?'; $params[] = $courseId; }
$sql       = "SELECT w.*, c.name AS course_name, c.icon AS course_icon FROM weekly_summaries w LEFT JOIN courses c ON c.id=w.course_id" . ($where ? " WHERE ".implode(" AND ",$where) : "") . " ORDER BY w.week_number DESC, w.id DESC";
$summaries = db_all($sql, $params);
$grouped   = [];
foreach ($summaries as $s) { $key = $s['course_name'] ?: 'عام'; $grouped[$key][] = $s; }

include dirname(__DIR__) . '/includes/header.php';
include dirname(__DIR__) . '/includes/navbar.php';
?>

<div class="page-hero">
  <div class="hero-icon" style="background:linear-gradient(135deg,#0EA5C9,#0284C7)">📅</div>
  <h1 class="hero-title">ملخص الأسبوع</h1>
  <p class="hero-sub"><?= count($summaries) ?> ملخص أسبوعي على تيليجرام</p>
</div>

<div class="container">
  <div class="filter-tabs">
    <a href="<?= $bp ?>/weekly" class="filter-tab <?= !$courseId?'active':'' ?>">🗂️ الكل</a>
    <?php foreach ($courses as $c): ?>
      <a href="<?= $bp ?>/weekly?course_id=<?= $c['id'] ?>" class="filter-tab <?= $courseId===$c['id']?'active':'' ?>"><?= e($c['icon']?:'📚') ?> <?= e($c['name']) ?></a>
    <?php endforeach; ?>
  </div>

  <?php if (empty($summaries)): ?>
    <div class="empty-state"><div class="empty-icon">📅</div><div class="empty-text">لا توجد ملخصات بعد</div></div>
  <?php else: ?>
  <?php foreach ($grouped as $courseName => $group): ?>
  <div style="margin-bottom:28px">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
      <span style="font-size:18px"><?= e($group[0]['course_icon']?:'📚') ?></span>
      <h2 style="font-family:'Tajawal',sans-serif;font-weight:900;font-size:17px;color:var(--text)"><?= e($courseName) ?></h2>
      <div style="flex:1;height:1px;background:var(--border)"></div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px">
      <?php foreach ($group as $s): ?>
      <a href="<?= e($s['telegram_url']) ?>" target="_blank" class="weekly-card">
        <div class="week-num"><?= (int)$s['week_number'] ?></div>
        <div class="weekly-info">
          <div class="weekly-title"><?= e($s['week_title'] ?: 'الأسبوع '.$s['week_number']) ?></div>
          <?php if ($s['description']): ?><div class="weekly-meta"><?= e(mb_substr($s['description'],0,70)) ?></div><?php endif; ?>
        </div>
        <div class="weekly-arrow">↗</div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
