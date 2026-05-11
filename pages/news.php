<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
$currentPage = '/news';
$pageTitle   = 'الأخبار والإعلانات';
$bp          = BASE_PATH;

$type   = $_GET['type'] ?? 'all';
$where  = $type !== 'all' ? "WHERE is_active=1 AND type=?" : "WHERE is_active=1";
$params = $type !== 'all' ? [$type] : [];
$news   = db_all("SELECT * FROM news $where ORDER BY created_at DESC", $params);

$typeMap = [
  'news'         => ['📰','خبر',     'badge-blue',  '#1A7FD4'],
  'announcement' => ['📢','إعلان',   'badge-amber', '#D97706'],
  'important'    => ['❗','هام جداً','badge-red',   '#EF4444'],
];

include dirname(__DIR__) . '/includes/header.php';
include dirname(__DIR__) . '/includes/navbar.php';
?>

<div class="page-hero">
  <div class="hero-icon" style="background:linear-gradient(135deg,#F59E0B,#EF4444)">📣</div>
  <h1 class="hero-title">الأخبار والإعلانات</h1>
  <p class="hero-sub">آخر التحديثات والإعلانات الهامة</p>
</div>

<div class="container-sm">
  <div class="filter-tabs">
    <a href="<?= $bp ?>/news" class="filter-tab <?= $type==='all'?'active':'' ?>">🗂️ الكل (<?= count($news) ?>)</a>
    <?php foreach ($typeMap as $k=>[$icon,$lbl,$badge]): ?>
      <a href="<?= $bp ?>/news?type=<?= $k ?>" class="filter-tab <?= $type===$k?'active':'' ?>"><?= $icon ?> <?= e($lbl) ?></a>
    <?php endforeach; ?>
  </div>

  <?php if (empty($news)): ?>
    <div class="empty-state"><div class="empty-icon">📣</div><div class="empty-text">لا توجد أخبار بعد</div></div>
  <?php else: ?>
    <?php foreach ($news as $n):
      [$tIcon,$tLbl,$tBadge,$borderColor] = $typeMap[$n['type']] ?? ['📌','عام','badge-gray','#6B7280'];
    ?>
    <div style="background:var(--card);border-right:4px solid <?= e($borderColor) ?>;border-top:1px solid var(--border);border-bottom:1px solid var(--border);border-left:1px solid var(--border);border-radius:0 16px 16px 0;padding:18px 20px;margin-bottom:14px">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:8px">
        <h3 style="font-size:15px;font-weight:800;color:var(--text);margin:0"><?= e($n['title']) ?></h3>
        <span class="badge <?= $tBadge ?>"><?= $tIcon ?> <?= e($tLbl) ?></span>
      </div>
      <?php if ($n['content']): ?><p style="font-size:13px;color:var(--muted);line-height:1.7;margin:0 0 10px"><?= nl2br(e($n['content'])) ?></p><?php endif; ?>
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px">
        <span style="font-size:11px;color:var(--muted)">🕐 <?= date('d/m/Y', strtotime($n['created_at'])) ?></span>
        <?php if ($n['link']): ?><a href="<?= e($n['link']) ?>" target="_blank" class="btn-open">🔗 الرابط</a><?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
