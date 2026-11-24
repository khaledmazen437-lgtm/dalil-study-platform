<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';

$currentPage = '/resources';
$pageTitle   = 'الموارد التعليمية ومسارات التعلم';
$bp          = BASE_PATH;

$adminLinks = db_all("SELECT * FROM important_links WHERE is_active=1 ORDER BY sort_order, id");

$linkCats = [
    'frontend'    => 'دبلومة Front-End',
    'backend'     => 'دبلومة Back-End',
    'fullstack'   => 'مسار Full-Stack',
    'programming' => 'برمجة عامة',
    'ai'          => 'ذكاء اصطناعي',
    'tools'       => 'أدوات',
    'networks'    => 'شبكات',
    'database'    => 'قواعد بيانات',
    'practice'    => 'مواقع تدريب',
    'telegram'    => 'قنوات تيليجرام',
    'youtube'     => 'قنوات يوتيوب',
    'other'       => 'أخرى'
];

// تجميع الروابط حسب المسار (التصنيف)
$groupedLinks = [];
foreach ($adminLinks as $lk) {
    $type = $lk['type'];
    if (!isset($groupedLinks[$type])) {
        $groupedLinks[$type] = [];
    }
    $groupedLinks[$type][] = $lk;
}

$staticResources = [
  ['title'=>'أدوات المطور','color'=>'#1A7FD4','bg'=>'#D8EDFF','icon'=>'🛠️','items'=>[
    ['title'=>'Visual Studio Code','desc'=>'المحرر الأشهر','icon'=>'💻','url'=>'https://code.visualstudio.com'],
    ['title'=>'GitHub','desc'=>'التطوير التشاركي','icon'=>'🐙','url'=>'https://github.com'],
    ['title'=>'Replit','desc'=>'برمجة في المتصفح','icon'=>'⚡','url'=>'https://replit.com'],
    ['title'=>'CodePen','desc'=>'اختبار HTML/CSS/JS','icon'=>'🖊️','url'=>'https://codepen.io'],
  ]],
  ['title'=>'مصادر تعليمية','color'=>'#8B5CF6','bg'=>'#EDE9FE','icon'=>'📚','items'=>[
    ['title'=>'GeeksforGeeks','desc'=>'شرح مستفيض لـ CS','icon'=>'🧪','url'=>'https://www.geeksforgeeks.org'],
    ['title'=>'W3Schools','desc'=>'مرجع الويب','icon'=>'🌐','url'=>'https://www.w3schools.com'],
    ['title'=>'MDN Web Docs','desc'=>'توثيق الويب الشامل','icon'=>'📖','url'=>'https://developer.mozilla.org'],
    ['title'=>'Stack Overflow','desc'=>'إجابات لكل مشاكل الكود','icon'=>'💬','url'=>'https://stackoverflow.com'],
  ]],
  ['title'=>'قنوات يوتيوب','color'=>'#EF4444','bg'=>'#FEE2E2','icon'=>'▶️','items'=>[
    ['title'=>'Elzero Web School','desc'=>'شرح بالعربي','icon'=>'🎓','url'=>'https://elzero.org'],
    ['title'=>'CS50 Harvard','desc'=>'مقدمة علوم الحاسب','icon'=>'🏛️','url'=>'https://cs50.harvard.edu'],
    ['title'=>'Fireship','desc'=>'تقنيات حديثة سريعة','icon'=>'🔥','url'=>'https://www.youtube.com/@Fireship'],
    ['title'=>'The Net Ninja','desc'=>'دورات شاملة للويب','icon'=>'🥷','url'=>'https://www.youtube.com/@NetNinja'],
  ]],
  ['title'=>'تمرين وتحديات','color'=>'#10B981','bg'=>'#D1FAE5','icon'=>'💪','items'=>[
    ['title'=>'LeetCode','desc'=>'مسائل الخوارزميات','icon'=>'⚙️','url'=>'https://leetcode.com'],
    ['title'=>'HackerRank','desc'=>'تحديات البرمجة','icon'=>'🏆','url'=>'https://www.hackerrank.com'],
    ['title'=>'Codeforces','desc'=>'مسابقات عالمية','icon'=>'🎮','url'=>'https://codeforces.com'],
    ['title'=>'Kaggle','desc'=>'تحديات الذكاء الاصطناعي','icon'=>'📊','url'=>'https://www.kaggle.com'],
  ]],
  ['title'=>'ذكاء اصطناعي','color'=>'#F59E0B','bg'=>'#FEF3C7','icon'=>'🤖','items'=>[
    ['title'=>'ChatGPT','desc'=>'مساعد OpenAI','icon'=>'💬','url'=>'https://chat.openai.com'],
    ['title'=>'Claude AI','desc'=>'مساعد Anthropic','icon'=>'🧠','url'=>'https://claude.ai'],
    ['title'=>'Google Colab','desc'=>'Python مجاناً','icon'=>'📓','url'=>'https://colab.research.google.com'],
    ['title'=>'Hugging Face','desc'=>'نماذج وأدوات AI','icon'=>'🤗','url'=>'https://huggingface.co'],
  ]],
  ['title'=>'الشبكات والأمن','color'=>'#0EA5C9','bg'=>'#CFFAFE','icon'=>'🔒','items'=>[
    ['title'=>'Cisco NetAcad','desc'=>'دورات الشبكات','icon'=>'🌐','url'=>'https://www.netacad.com'],
    ['title'=>'TryHackMe','desc'=>'أمن سيبراني عملي','icon'=>'🛡️','url'=>'https://tryhackme.com'],
    ['title'=>'Wireshark','desc'=>'تحليل حركة الشبكة','icon'=>'🔍','url'=>'https://www.wireshark.org'],
    ['title'=>'OWASP','desc'=>'أمان التطبيقات','icon'=>'🔐','url'=>'https://owasp.org'],
  ]],
];

include dirname(__DIR__) . '/includes/header.php';
include dirname(__DIR__) . '/includes/navbar.php';
?>

<style>
  /* تنسيقات المسارات التعليمية (Roadmap) */
  .roadmap-container { margin-bottom: 50px; }
  .roadmap-section { margin-bottom: 40px; }
  .roadmap-badge { background: var(--primary-bg); color: var(--primary); padding: 8px 18px; border-radius: 20px; font-weight: 800; font-size: 15px; display: inline-block; margin-bottom: 20px; border: 1px solid var(--primary); }
  .roadmap-timeline { border-right: 3px dashed var(--border); padding-right: 30px; margin-right: 15px; position: relative; display: flex; flex-direction: column; gap: 20px; }

  .roadmap-item { position: relative; background: var(--card); border: 1px solid var(--border); padding: 20px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px rgba(0,0,0,0.02); transition: 0.3s; text-decoration: none !important; color: inherit; }
  .roadmap-item:hover { border-color: var(--primary); transform: translateX(-5px); box-shadow: 0 6px 12px rgba(0,0,0,0.05); }

  .roadmap-item::before { content: ''; position: absolute; right: -39px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; background: var(--primary); border-radius: 50%; border: 3px solid var(--bg); box-shadow: 0 0 0 3px var(--primary-bg); transition: 0.3s; }
  .roadmap-item:hover::before { background: var(--amber); box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.2); }

  .roadmap-content { display: flex; align-items: center; gap: 15px; }
  .roadmap-icon { font-size: 28px; background: var(--primary-bg); width: 55px; height: 55px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
  .roadmap-info h4 { margin: 0 0 6px 0; font-size: 17px; color: var(--text); font-weight: bold; }
  .roadmap-info p { margin: 0; font-size: 14px; color: var(--muted); line-height: 1.5; }

  .roadmap-step { position: absolute; right: 20px; top: -12px; background: var(--bg); border: 1px solid var(--border); font-size: 12px; padding: 3px 10px; border-radius: 12px; color: var(--muted); font-weight: bold; }

  .roadmap-action { background: var(--primary); color: white; padding: 8px 16px; border-radius: 8px; font-weight: bold; font-size: 13px; transition: 0.2s; white-space: nowrap; }
  .roadmap-item:hover .roadmap-action { background: var(--primary-hover); }

  @media(max-width: 600px) {
    .roadmap-item { flex-direction: column; align-items: flex-start; gap: 15px; }
    .roadmap-action { align-self: flex-end; }
  }
</style>

<div class="page-hero">
  <div class="hero-icon" style="background:linear-gradient(135deg,#10B981,#059669)">🌐</div>
  <h1 class="hero-title">مسارات التعلم والمصادر</h1>
  <p class="hero-sub">خريطة طريق منظمة ومجموعة مختارة من أفضل المصادر لطلاب الحاسبات</p>
</div>

<div class="container">

<?php if (!empty($groupedLinks)): ?>
<div class="roadmap-container">
  <div class="section-header">
    <div class="section-icon" style="background:#D8EDFF">🗺️</div>
    <div><div class="section-title">مسارات التعلم (Roadmaps)</div><div class="section-bar" style="background:#1A7FD4"></div></div>
  </div>

  <?php foreach ($groupedLinks as $type => $links): ?>
    <?php $categoryName = $linkCats[$type] ?? $type; ?>
    <div class="roadmap-section">
      <div class="roadmap-badge">📍 <?= e($categoryName) ?></div>
      <div class="roadmap-timeline">

        <?php foreach ($links as $lk): ?>
          <a href="<?= e($lk['url']) ?>" target="_blank" rel="noopener" class="roadmap-item">
            <div class="roadmap-step">الخطوة <?= (int)$lk['sort_order'] ?></div>
            <div class="roadmap-content">
              <div class="roadmap-icon"><?= e($lk['icon'] ?: '🔗') ?></div>
              <div class="roadmap-info">
                <h4><?= e($lk['title']) ?></h4>
                <p><?= e($lk['description'] ?: 'انقر لزيارة الرابط واستكشاف المحتوى') ?></p>
              </div>
            </div>
            <div class="roadmap-action">ابدأ الآن ↗</div>
          </a>
        <?php endforeach; ?>

      </div>
    </div>
  <?php endforeach; ?>

</div>
<div class="divider"><div class="divider-line"></div><div class="divider-text">📚 موارد وأدوات عامة مقترحة</div><div class="divider-line"></div></div>
<?php endif; ?>

<?php foreach ($staticResources as $cat): ?>
<div style="margin-bottom:36px">
  <div class="section-header">
    <div class="section-icon" style="background:<?= e($cat['bg']) ?>"><?= $cat['icon'] ?></div>
    <div><div class="section-title" style="color:<?= e($cat['color']) ?>"><?= e($cat['title']) ?></div><div class="section-bar" style="background:<?= e($cat['color']) ?>"></div></div>
  </div>
  <div class="card-grid-3">
    <?php foreach ($cat['items'] as $item): ?>
    <a href="<?= e($item['url']) ?>" target="_blank" rel="noopener" class="resource-card">
      <div class="resource-icon" style="background:<?= e($cat['bg']) ?>"><?= e($item['icon']) ?></div>
      <div><div class="resource-title"><?= e($item['title']) ?></div><div class="resource-desc"><?= e($item['desc']) ?></div></div>
    </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endforeach; ?>

</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
