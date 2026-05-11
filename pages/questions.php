<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
$currentPage = '/questions';
$pageTitle   = 'بنك الأسئلة';
$bp          = BASE_PATH;

$courses  = db_all("SELECT * FROM courses WHERE is_active=1 ORDER BY sort_order,id");
$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$type     = $_GET['type'] ?? 'all';

$where = []; $params = [];
if ($courseId)       { $where[] = 'q.course_id=?'; $params[] = $courseId; }
if ($type !== 'all') { $where[] = 'q.type=?';      $params[] = $type; }
$sql       = "SELECT q.*, c.name AS course_name, c.icon AS course_icon FROM questions q LEFT JOIN courses c ON c.id=q.course_id" . ($where ? " WHERE ".implode(" AND ",$where) : "") . " ORDER BY q.id DESC";
$questions = db_all($sql, $params);

$typeMap = ['mcq'=>['MCQ','badge-blue'],'essay'=>['مقال','badge-purple'],'mixed'=>['ميكس','badge-amber'],'sheet'=>['شيت','badge-green']];
$grouped = [];
foreach ($questions as $q) { $key = $q['course_name'] ?: 'بدون مادة'; $grouped[$key][] = $q; }

include dirname(__DIR__) . '/includes/header.php';
include dirname(__DIR__) . '/includes/navbar.php';
?>

<div class="page-hero">
  <div class="hero-icon" style="background:linear-gradient(135deg,#10B981,#059669)">❓</div>
  <h1 class="hero-title">بنك الأسئلة</h1>
  <p class="hero-sub"><?= count($questions) ?> مجموعة أسئلة متاحة</p>
</div>

<div class="container">
  <div class="filter-tabs">
    <a href="<?= $bp ?>/questions" class="filter-tab <?= (!$courseId&&$type==='all')?'active':'' ?>">🗂️ الكل</a>
    <?php foreach ($courses as $c): ?>
      <a href="<?= $bp ?>/questions?course_id=<?= $c['id'] ?>" class="filter-tab <?= $courseId===$c['id']?'active':'' ?>"><?= e($c['icon']?:'📚') ?> <?= e($c['name']) ?></a>
    <?php endforeach; ?>
  </div>
  <div class="filter-tabs" style="margin-top:-10px">
    <?php foreach ($typeMap as $k=>[$lbl,$b]): ?>
      <a href="<?= $bp ?>/questions?type=<?= $k ?>" class="filter-tab <?= $type===$k?'active':'' ?>"><?= e($lbl) ?></a>
    <?php endforeach; ?>
  </div>

  <?php if (empty($questions)): ?>
    <div class="empty-state"><div class="empty-icon">❓</div><div class="empty-text">لا توجد أسئلة بعد</div></div>
  <?php else: ?>
  <?php foreach ($grouped as $courseName => $group): ?>
  <div style="margin-bottom:28px">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
      <span style="font-size:18px"><?= e($group[0]['course_icon']?:'📚') ?></span>
      <h2 style="font-family:'Tajawal',sans-serif;font-weight:900;font-size:17px;color:var(--text)"><?= e($courseName) ?></h2>
      <div style="flex:1;height:1px;background:var(--border)"></div>
    </div>
    <div style="display:flex;flex-direction:column;gap:10px">
    <?php foreach ($group as $q):
      [$typeLbl,$typeBadge] = $typeMap[$q['type']] ?? ['أخرى','badge-gray'];
    ?>
    <div class="exam-card">
      <span class="badge <?= $typeBadge ?>"><?= e($typeLbl) ?></span>
      <div class="exam-info">
        <div class="exam-title"><?= e($q['title']) ?></div>
        <?php if ($q['description']): ?><div class="exam-meta"><?= e($q['description']) ?></div><?php endif; ?>
        <div class="exam-actions">
          <?php if ($q['file_url']): ?><a href="<?= e($q['file_url']) ?>" target="_blank" class="btn-open">📄 الملف</a><?php endif; ?>
          <?php if ($q['telegram_url']): ?><a href="<?= e($q['telegram_url']) ?>" target="_blank" class="btn-open">📢 تيليجرام</a><?php endif; ?>
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
