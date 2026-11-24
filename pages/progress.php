<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
$currentPage = '/progress';
$pageTitle   = 'تقدمي الدراسي';
$bp          = BASE_PATH;

$courses    = db_all("SELECT * FROM courses WHERE is_active=1 ORDER BY sort_order,id");
$statusInfo = [
  ['not_started','⭕','لم يبدأ',  '#6B8FB5','rgba(107,143,181,.1)'],
  ['in_progress', '⏳','جاري',     '#F59E0B','#FEF3C7'],
  ['completed',   '✅','مكتمل',   '#10B981','#D1FAE5'],
];

include dirname(__DIR__) . '/includes/header.php';
include dirname(__DIR__) . '/includes/navbar.php';
?>

<div class="page-hero">
  <div class="hero-icon" style="background:linear-gradient(135deg,#F97316,#EF4444)">📊</div>
  <h1 class="hero-title">تقدمي الدراسي</h1>
  <p class="hero-sub">تابع مستواك في كل مادة — محفوظ على جهازك</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container">

  <div class="card" style="padding: 24px; margin-bottom: 28px;">
    <h3 style="font-family:'Tajawal',sans-serif; margin-top:0; color:var(--text); margin-bottom:16px;">📈 تحليلات المذاكرة (Pomodoro)</h3>
    <p style="font-size:13px; color:var(--muted); margin-bottom:20px;">يوضح الرسم البياني التالي عدد الدقائق التي قضيتها في "مركز التركيز" خلال آخر 7 أيام.</p>
    <div style="width:100%; height:250px;">
        <canvas id="studyChart"></canvas>
    </div>
  </div>

  <div style="background:var(--card);border:1px solid var(--border);border-radius:18px;padding:22px;margin-bottom:28px">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:14px">
      <div>
        <div style="font-family:'Tajawal',sans-serif;font-weight:900;font-size:18px;color:var(--text)">التقدم الإجمالي</div>
        <div style="font-size:12px;color:var(--muted)">نسبة المواد المكتملة</div>
      </div>
      <div style="font-family:'Tajawal',sans-serif;font-weight:900;font-size:40px;color:var(--primary)" id="overall-pct">0%</div>
    </div>
    <div class="progress-bar-wrap" style="height:12px"><div class="progress-bar-fill" id="overall-bar" style="width:0%"></div></div>
  </div>

  <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px">
    <?php foreach ($statusInfo as [$st,$icon,$lbl,$clr,$bg]): ?>
    <div style="display:flex;align-items:center;gap:6px;background:<?= $bg ?>;padding:6px 12px;border-radius:9px;font-size:12px;font-weight:700;color:<?= $clr ?>"><?= $icon ?> <?= e($lbl) ?></div>
    <?php endforeach; ?>
    <button onclick="if(confirm('إعادة ضبط كل التقدم؟')){localStorage.removeItem('dalil_progress_v2');ProgressApp.renderAll()}"
      style="margin-right:auto;padding:6px 14px;border-radius:9px;border:1px solid var(--border);background:var(--danger-bg);color:var(--danger);font-size:11px;font-weight:700;cursor:pointer">🔄 إعادة الضبط</button>
  </div>

  <?php if (empty($courses)): ?>
    <div class="empty-state"><div class="empty-icon">📚</div><div class="empty-text">لا توجد مواد</div></div>
  <?php else: ?>
  <div class="card-grid">
    <?php foreach ($courses as $c):
      $color = $c['color'] ?: '#1A7FD4';
      $bg = $color . '22';
    ?>
    <div class="card" data-course-id="<?= $c['id'] ?>" style="padding:20px">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
        <div style="width:44px;height:44px;border-radius:12px;background:<?= e($bg) ?>;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0"><?= e($c['icon']?:'📚') ?></div>
        <div style="flex:1;min-width:0">
          <div style="font-size:13px;font-weight:800;color:var(--text)"><?= e($c['name']) ?></div>
          <?php if ($c['code']): ?><span class="badge badge-blue" style="font-size:10px"><?= e($c['code']) ?></span><?php endif; ?>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
        <div class="progress-bar-wrap"><div class="progress-bar-fill" style="width:0%"></div></div>
        <span class="prog-label" style="font-size:11px;font-weight:700;color:var(--muted);white-space:nowrap">⭕ لم يبدأ</span>
      </div>
      <div style="display:flex;gap:6px">
        <?php foreach ($statusInfo as [$st,$icon,$lbl,$clr,$bg2]): ?>
        <button class="prog-btn" data-status="<?= e($st) ?>"
          onclick="ProgressApp.set(<?= $c['id'] ?>,'<?= e($st) ?>')"
          style="flex:1;padding:6px 4px;border-radius:9px;border:1px solid var(--border);background:<?= $bg2 ?>;color:<?= $clr ?>;font-size:11px;font-weight:500;cursor:pointer">
          <?= $icon ?>
        </button>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<script>
    const stats = JSON.parse(localStorage.getItem('dalil-study-stats')) || {};
    const labels = [];
    const data = [];

    // Generate last 7 days dynamically
    for(let i=6; i>=0; i--) {
        let d = new Date();
        d.setDate(d.getDate() - i);
        let dateStr = d.toISOString().split('T')[0];

        let displayStr = d.getDate() + '/' + (d.getMonth()+1);
        labels.push(displayStr);
        data.push(stats[dateStr] || 0);
    }

    const ctx = document.getElementById('studyChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'دقائق المذاكرة',
                data: data,
                backgroundColor: 'rgba(230, 0, 0, 0.6)',
                borderColor: '#E60000',
                borderWidth: 2,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: 'var(--muted)' },
                    grid: { color: 'var(--border)' }
                },
                x: {
                    ticks: { color: 'var(--muted)', font: { family: 'Cairo' } },
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { labels: { color: 'var(--text)', font: { family: 'Cairo' } } }
            }
        }
    });
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
