<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
$currentPage = '/exams';
$pageTitle   = 'الامتحانات السابقة';
$bp          = BASE_PATH;

$courses  = db_all("SELECT * FROM courses WHERE is_active=1 ORDER BY sort_order,id");
$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$type     = $_GET['type'] ?? 'all';

$where = []; $params = [];
if ($courseId)         { $where[] = 'e.course_id=?';  $params[] = $courseId; }
if ($type !== 'all')   { $where[] = 'e.exam_type=?';  $params[] = $type; }
$sql   = "SELECT e.*, c.name AS course_name, c.icon AS course_icon FROM past_exams e LEFT JOIN courses c ON c.id=e.course_id WHERE e.is_active=1" . ($where ? " AND ".implode(" AND ",$where) : "") . " ORDER BY e.year DESC, e.id DESC";
$exams = db_all($sql, $params);

$typeMap = ['final'=>['فاينل','badge-red'],'midterm'=>['ميدترم','badge-amber'],'quiz'=>['كويز','badge-blue'],'assignment'=>['أسايمنت','badge-green']];
$grouped = [];
foreach ($exams as $ex) { $key = $ex['course_name'] ?: 'بدون مادة'; $grouped[$key][] = $ex; }

include dirname(__DIR__) . '/includes/header.php';
include dirname(__DIR__) . '/includes/navbar.php';
?>

<div class="page-hero">
  <div class="hero-icon" style="background:linear-gradient(135deg,#F97316,#EF4444)">📝</div>
  <h1 class="hero-title">الامتحانات السابقة</h1>
  <p class="hero-sub"><?= count($exams) ?> امتحان متاح — فاينل، ميدترم، كويزات</p>
</div>

<div class="container">
  <div class="filter-tabs">
    <a href="<?= $bp ?>/exams" class="filter-tab <?= (!$courseId&&$type==='all')?'active':'' ?>">🗂️ الكل</a>
    <?php foreach ($courses as $c): ?>
      <a href="<?= $bp ?>/exams?course_id=<?= $c['id'] ?>" class="filter-tab <?= $courseId===$c['id']?'active':'' ?>"><?= e($c['icon']?:'📚') ?> <?= e($c['name']) ?></a>
    <?php endforeach; ?>
  </div>
  <div class="filter-tabs" style="margin-top:-10px">
    <?php foreach ($typeMap as $k=>[$lbl,$badge]): ?>
      <a href="<?= $bp ?>/exams?<?= $courseId?"course_id=$courseId&":"" ?>type=<?= $k ?>" class="filter-tab <?= $type===$k?'active':'' ?>"><?= e($lbl) ?></a>
    <?php endforeach; ?>
  </div>

  <?php if (empty($exams)): ?>
    <div class="empty-state"><div class="empty-icon">📝</div><div class="empty-text">لا توجد امتحانات بعد</div></div>
  <?php else: ?>
  <?php foreach ($grouped as $courseName => $group): ?>
  <div style="margin-bottom:32px">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
      <span style="font-size:18px"><?= e($group[0]['course_icon']?:'📚') ?></span>
      <h2 style="font-family:'Tajawal',sans-serif;font-weight:900;font-size:17px;color:var(--text)"><?= e($courseName) ?></h2>
      <div style="flex:1;height:1px;background:var(--border)"></div>
    </div>
    <div style="display:flex;flex-direction:column;gap:10px">
      <?php foreach ($group as $ex):
        [$typeLbl,$typeBadge] = $typeMap[$ex['exam_type']] ?? ['أخرى','badge-gray'];
      ?>
      <div class="exam-card">
        <span class="badge <?= $typeBadge ?>"><?= e($typeLbl) ?></span>
        <div class="exam-info">
          <div class="exam-title"><?= e($ex['title']) ?></div>
          <div class="exam-meta"><?= $ex['year']?'📅 '.$ex['year']:'' ?> <?= $ex['semester']?'· '.e($ex['semester']):'' ?></div>
          <div class="exam-actions">
            <?php if ($ex['file_url']): ?><a href="<?= e($ex['file_url']) ?>" target="_blank" class="btn-open">📄 الامتحان</a><?php endif; ?>
            <?php if ($ex['solution_url']): ?><a href="<?= e($ex['solution_url']) ?>" target="_blank" class="btn-open">✅ الحل</a><?php endif; ?>
            <?php if ($ex['telegram_url']): ?><a href="<?= e($ex['telegram_url']) ?>" target="_blank" class="btn-open">📢 تيليجرام</a><?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
