<?php
/* ═══════════════════════════════
    لوحة تحكم دليل — Admin Dashboard (Secured & Protected)
    تطوير: مازن خالد
   ═══════════════════════════════ */

// تأمين ملفات الكوكيز والجلسة لمنع الاختطاف
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}

session_start();
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';

/* ── Login / Logout ── */
$error = '';
$bp = defined('BASE_PATH') ? BASE_PATH : '';

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ' . $bp . '/mhd-admin');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');
    
    // فحص بيانات الدخول باستخدام الثوابت من config.php
    if ($user === ADMIN_USER && hash('sha256', $pass . ADMIN_SALT) === ADMIN_HASH) {
        session_regenerate_id(true); // حماية ضد Session Fixation
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user']      = $user;
        header('Location: ' . $bp . '/mhd-admin');
        exit;
    }
    $error = 'بيانات الدخول غير صحيحة';
}
$loggedIn = !empty($_SESSION['admin_logged_in']);

/* ── Helper: JSON response for AJAX ── */
function jsonResponse(array $data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/* ── Inline CRUD actions (AJAX - Secured with Prepared Statements) ── */
if ($loggedIn && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    $action = $_GET['action'] ?? '';
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $db     = getDB();

    switch ($action) {
        /* ─── COURSES ─── */
        case 'courses_list':
            jsonResponse(['data' => db_all("SELECT * FROM courses ORDER BY sort_order,id")]);
        case 'courses_add':
            $stmt = $db->prepare("INSERT INTO courses (name,code,description,icon,color,semester,year,sort_order,is_active) VALUES (?,?,?,?,?,?,?,?,1)");
            $stmt->execute([$body['name'],$body['code'],$body['description'],$body['icon'],$body['color'],$body['semester'],$body['year'],$body['sort_order']??0]);
            jsonResponse(['id' => $db->lastInsertId()]);
        case 'courses_del':
            $stmt = $db->prepare("DELETE FROM courses WHERE id=?");
            $stmt->execute([(int)$_GET['id']]);
            jsonResponse(['ok' => true]);

        /* ─── MATERIALS ─── */
        case 'materials_list':
            jsonResponse(['data' => db_all("SELECT m.*,c.name AS course_name FROM materials m LEFT JOIN courses c ON c.id=m.course_id WHERE m.is_active=1 ORDER BY m.id DESC")]);
        case 'materials_add':
            $stmt = $db->prepare("INSERT INTO materials (course_id,title,description,url,type,sort_order,is_active) VALUES (?,?,?,?,?,?,1)");
            $stmt->execute([$body['course_id'],$body['title'],$body['description'],$body['url'],$body['type'],$body['sort_order']??0]);
            jsonResponse(['id' => $db->lastInsertId()]);
        case 'materials_del':
            $stmt = $db->prepare("DELETE FROM materials WHERE id=?");
            $stmt->execute([(int)$_GET['id']]);
            jsonResponse(['ok' => true]);

        /* ─── EXAMS ─── */
        case 'exams_list':
            jsonResponse(['data' => db_all("SELECT e.*,c.name AS course_name FROM past_exams e LEFT JOIN courses c ON c.id=e.course_id WHERE e.is_active=1 ORDER BY e.id DESC")]);
        case 'exams_add':
            $stmt = $db->prepare("INSERT INTO past_exams (course_id,title,description,year,semester,exam_type,file_url,solution_url,telegram_url,is_active) VALUES (?,?,?,?,?,?,?,?,?,1)");
            $stmt->execute([$body['course_id'],$body['title'],$body['description'],$body['year'],$body['semester'],$body['exam_type'],$body['file_url'],$body['solution_url'],$body['telegram_url']]);
            jsonResponse(['id' => $db->lastInsertId()]);
        case 'exams_del':
            $stmt = $db->prepare("DELETE FROM past_exams WHERE id=?");
            $stmt->execute([(int)$_GET['id']]);
            jsonResponse(['ok' => true]);

        /* ─── QUESTIONS ─── */
        case 'questions_list':
            jsonResponse(['data' => db_all("SELECT q.*,c.name AS course_name FROM questions q LEFT JOIN courses c ON c.id=q.course_id ORDER BY q.id DESC")]);
        case 'questions_add':
            $stmt = $db->prepare("INSERT INTO questions (course_id,title,description,type,file_url,telegram_url) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$body['course_id'],$body['title'],$body['description'],$body['type'],$body['file_url'],$body['telegram_url']]);
            jsonResponse(['id' => $db->lastInsertId()]);
        case 'questions_del':
            $stmt = $db->prepare("DELETE FROM questions WHERE id=?");
            $stmt->execute([(int)$_GET['id']]);
            jsonResponse(['ok' => true]);

        /* ─── WEEKLY ─── */
        case 'weekly_list':
            jsonResponse(['data' => db_all("SELECT w.*,c.name AS course_name FROM weekly_summaries w LEFT JOIN courses c ON c.id=w.course_id ORDER BY w.id DESC")]);
        case 'weekly_add':
            $stmt = $db->prepare("INSERT INTO weekly_summaries (course_id,week_number,week_title,description,telegram_url,telegram_channel) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$body['course_id'],$body['week_number'],$body['week_title'],$body['description'],$body['telegram_url'],$body['telegram_channel']]);
            jsonResponse(['id' => $db->lastInsertId()]);
        case 'weekly_del':
            $stmt = $db->prepare("DELETE FROM weekly_summaries WHERE id=?");
            $stmt->execute([(int)$_GET['id']]);
            jsonResponse(['ok' => true]);

        /* ─── NEWS ─── */
        case 'news_list':
            jsonResponse(['data' => db_all("SELECT * FROM news WHERE is_active=1 ORDER BY id DESC")]);
        case 'news_add':
            $stmt = $db->prepare("INSERT INTO news (title,content,type,link,is_active) VALUES (?,?,?,?,1)");
            $stmt->execute([$body['title'],$body['content'],$body['type'],$body['link']]);
            jsonResponse(['id' => $db->lastInsertId()]);
        case 'news_del':
            $stmt = $db->prepare("DELETE FROM news WHERE id=?");
            $stmt->execute([(int)$_GET['id']]);
            jsonResponse(['ok' => true]);

        /* ─── LINKS ─── */
        case 'links_list':
            jsonResponse(['data' => db_all("SELECT * FROM important_links WHERE is_active=1 ORDER BY sort_order,id")]);
        case 'links_add':
            $stmt = $db->prepare("INSERT INTO important_links (title,url,icon,type,description,sort_order,is_active) VALUES (?,?,?,?,?,?,1)");
            $stmt->execute([$body['title'],$body['url'],$body['icon'],$body['type'],$body['description'],$body['sort_order']??0]);
            jsonResponse(['id' => $db->lastInsertId()]);
        case 'links_del':
            $stmt = $db->prepare("DELETE FROM important_links WHERE id=?");
            $stmt->execute([(int)$_GET['id']]);
            jsonResponse(['ok' => true]);

        /* ─── TICKER ─── */
        case 'ticker_list':
            jsonResponse(['data' => db_all("SELECT * FROM ticker_messages ORDER BY sort_order,id")]);
        case 'ticker_add':
            $stmt = $db->prepare("INSERT INTO ticker_messages (message,sort_order,is_active) VALUES (?,?,1)");
            $stmt->execute([$body['message'],$body['sort_order']??0]);
            jsonResponse(['id' => $db->lastInsertId()]);
        case 'ticker_del':
            $stmt = $db->prepare("DELETE FROM ticker_messages WHERE id=?");
            $stmt->execute([(int)$_GET['id']]);
            jsonResponse(['ok' => true]);

        /* ─── QUIZZES (JSON) ─── */
        case 'quizzes_get':
            $file = __DIR__ . '/quizzes.json';
            $data = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
            jsonResponse(['data' => $data]);
        case 'quizzes_save':
            $file = __DIR__ . '/quizzes.json';
            file_put_contents($file, json_encode($body['data'], JSON_UNESCAPED_UNICODE));
            jsonResponse(['ok' => true]);
    }
    jsonResponse(['error' => 'invalid action'], 400);
}

/* ── Stats for dashboard ── */
$stats = [];
if ($loggedIn) {
    $stats = [
        'courses'   => db_val("SELECT COUNT(*) FROM courses WHERE is_active=1"),
        'materials' => db_val("SELECT COUNT(*) FROM materials WHERE is_active=1"),
        'exams'     => db_val("SELECT COUNT(*) FROM past_exams WHERE is_active=1"),
        'questions' => db_val("SELECT COUNT(*) FROM questions"),
        'weekly'    => db_val("SELECT COUNT(*) FROM weekly_summaries"),
        'news'      => db_val("SELECT COUNT(*) FROM news WHERE is_active=1"),
        'links'     => db_val("SELECT COUNT(*) FROM important_links WHERE is_active=1"),
        'ticker'    => db_val("SELECT COUNT(*) FROM ticker_messages WHERE is_active=1"),
    ];
    $courses = db_all("SELECT id,name,icon FROM courses WHERE is_active=1 ORDER BY sort_order,id");
}
$section = $_GET['s'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>لوحة التحكم — دليل</title>
  <meta name="base-path" content="<?= htmlspecialchars($bp, ENT_QUOTES, 'UTF-8') ?>">
  <link rel="icon" href="<?= $bp ?>/favicon.svg">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Tajawal:wght@700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= $bp ?>/assets/style.css">
  <script>(function(){var t=localStorage.getItem('dalil-theme')||'light';document.documentElement.setAttribute('data-theme',t)})();</script>
</head>
<body oncontextmenu="return false;"> <!-- منع الزر الأيمن في البودي -->

<?php if (!$loggedIn): ?>
<!-- ══ LOGIN PAGE ══ -->
<div class="login-page">
  <div class="login-box">
    <div class="login-icon">🔐</div>
    <div class="login-title">لوحة التحكم</div>
    <div class="login-sub">منصة دليل التعليمية — مازن خالد</div>
    <?php if ($error): ?><div class="alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST" style="display:flex;flex-direction:column;gap:14px">
      <div class="form-group">
        <label class="form-label">اسم المستخدم</label>
        <input class="form-input" type="text" name="username" required autofocus placeholder="اسم المستخدم">
      </div>
      <div class="form-group">
        <label class="form-label">كلمة المرور</label>
        <input class="form-input" type="password" name="password" required placeholder="كلمة المرور">
      </div>
      <button type="submit" class="btn-primary" style="justify-content:center">🔓 دخول</button>
    </form>
    <div style="margin-top:14px;text-align:center;font-size:12px;color:var(--muted)">
      <a href="<?= $bp ?>/" style="color:var(--primary)">← العودة للموقع</a>
    </div>
  </div>
</div>
<?php else: ?>

<!-- ══ ADMIN DASHBOARD ══ -->
<div style="display:flex;flex-direction:column;min-height:100vh">

<!-- Top bar -->
<div class="admin-topbar">
  <div style="display:flex;align-items:center;gap:12px">
    <a href="<?= $bp ?>/" class="brand-logo" style="width:34px;height:34px;font-size:16px;border-radius:9px">د</a>
    <div style="font-family:'Tajawal',sans-serif;font-weight:900;font-size:17px;color:var(--text)">لوحة التحكم</div>
  </div>
  <div style="display:flex;align-items:center;gap:8px">
    <a href="<?= $bp ?>/" target="_blank" class="btn-open" style="font-size:12px">🌐 الموقع</a>
    <button class="btn-theme" id="btn-theme" onclick="toggleTheme()">🌙</button>
    <a href="<?= $bp ?>/mhd-admin?logout=1" style="padding:7px 14px;border-radius:10px;background:var(--danger-bg);color:var(--danger);font-size:12px;font-weight:700;border:1px solid var(--border)">خروج</a>
  </div>
</div>

<div style="display:flex;flex:1">

<!-- Sidebar -->
<div class="admin-sidebar">
  <?php
  $sections = [
    ['dashboard', '📊', 'الإحصائيات'],
    ['courses',   '📚', 'المواد'],
    ['materials', '📄', 'المحتوى'],
    ['exams',     '📝', 'الامتحانات'],
    ['questions', '❓', 'الأسئلة'],
    ['weekly',    '📅', 'ملخص الأسبوع'],
    ['quizzes',   '⏳', 'الاختبارات الذكية'],
    ['news',      '📣', 'الأخبار'],
    ['links',     '🔗', 'الروابط (Roadmap)'],
    ['ticker',    '📡', 'شريط الأخبار'],
  ];
  foreach ($sections as [$key,$icon,$label]):
  ?>
  <button class="sidebar-item <?= $section===$key?'active':'' ?>" onclick="goSection('<?= $key ?>')"><?= $icon ?> <?= $label ?></button>
  <?php endforeach; ?>
  <div style="flex:1"></div>
  <div style="font-size:11px;color:var(--muted);padding:10px 12px">
    مرحباً، <?= htmlspecialchars($_SESSION['admin_user']) ?> 👋
  </div>
</div>

<!-- Main -->
<div class="admin-main">

<!-- ── Dashboard ── -->
<div id="sec-dashboard" class="admin-section">
  <h2 class="section-title-h2">📊 نظرة عامة</h2>
  <div class="stats-grid">
    <?php $statDef = [
      ['courses','📚','مادة','#1A7FD4'],['materials','📄','محتوى','#8B5CF6'],
      ['exams','📝','امتحان','#F97316'],['questions','❓','سؤال','#10B981'],
      ['weekly','📅','ملخص','#0EA5C9'],['news','📣','خبر','#EF4444'],
      ['links','🔗','رابط','#F59E0B'],['ticker','📡','رسالة شريط','#6B7280'],
    ];
    foreach ($statDef as [$key,$icon,$lbl,$color]): ?>
    <div class="stat-card" onclick="goSection('<?= $key ?>')" style="border-top:3px solid <?= $color ?>">
      <div style="font-size:22px;margin-bottom:4px"><?= $icon ?></div>
      <div class="stat-value" style="color:<?= $color ?>"><?= (int)($stats[$key]??0) ?></div>
      <div class="stat-label"><?= $lbl ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- ── Courses ── -->
<div id="sec-courses" class="admin-section" style="display:none">
  <h2 class="section-title-h2">📚 إدارة المواد</h2>
  <div class="admin-form">
    <div class="form-grid">
      <div class="form-group"><label class="form-label">اسم المادة *</label><input class="form-input" id="c-name" placeholder="مثال: الخوارزميات"></div>
      <div class="form-group"><label class="form-label">الكود</label><input class="form-input" id="c-code" placeholder="CS301"></div>
      <div class="form-group"><label class="form-label">الأيقونة</label><input class="form-input" id="c-icon" placeholder="📚" style="font-size:18px"></div>
      <div class="form-group"><label class="form-label">اللون</label><input class="form-input" type="color" id="c-color" value="#1A7FD4"></div>
      <div class="form-group"><label class="form-label">الفصل</label><select class="form-select" id="c-semester"><option value="">اختر</option><option>الأول</option><option>الثاني</option><option>الصيفي</option></select></div>
      <div class="form-group"><label class="form-label">السنة الدراسية</label><input class="form-input" id="c-year" placeholder="1 / 2 / 3 / 4"></div>
    </div>
    <div class="form-group"><label class="form-label">وصف المادة</label><textarea class="form-textarea" id="c-desc" placeholder="وصف مختصر"></textarea></div>
    <button class="btn-primary" onclick="coursesAdd()">➕ إضافة مادة</button>
  </div>
  <div class="table-wrap"><div class="table-scroll"><table class="admin-table"><thead><tr><th>الأيقونة</th><th>الاسم</th><th>الكود</th><th>الفصل</th><th>حذف</th></tr></thead><tbody id="courses-tbody"></tbody></table></div></div>
</div>

<!-- ── Materials ── -->
<div id="sec-materials" class="admin-section" style="display:none">
  <h2 class="section-title-h2">📄 إدارة المحتوى</h2>
  <div class="admin-form">
    <div class="form-grid">
      <div class="form-group"><label class="form-label">المادة *</label><select class="form-select" id="m-course"><?php foreach($courses as $c):?><option value="<?= $c['id'] ?>"><?= $c['icon'] ?> <?= $c['name'] ?></option><?php endforeach;?></select></div>
      <div class="form-group"><label class="form-label">النوع *</label><select class="form-select" id="m-type"><option value="summary">ملخص</option><option value="slides">سلايدز</option><option value="video">يوتيوب</option><option value="recording">تسجيل</option><option value="weekly_summary">ملخص أسبوع</option><option value="question">أسئلة</option><option value="exam">امتحان</option><option value="other">أخرى</option></select></div>
    </div>
    <div class="form-group"><label class="form-label">العنوان *</label><input class="form-input" id="m-title" placeholder="عنوان المحتوى"></div>
    <div class="form-group"><label class="form-label">الرابط *</label><input class="form-input" id="m-url" placeholder="https://..."></div>
    <div class="form-group"><label class="form-label">وصف مختصر</label><input class="form-input" id="m-desc" placeholder="وصف اختياري"></div>
    <button class="btn-primary" onclick="materialsAdd()">➕ إضافة محتوى</button>
  </div>
  <div class="table-wrap"><div class="table-scroll"><table class="admin-table"><thead><tr><th>المادة</th><th>العنوان</th><th>النوع</th><th>الرابط</th><th>حذف</th></tr></thead><tbody id="materials-tbody"></tbody></table></div></div>
</div>

<!-- ── Exams ── -->
<div id="sec-exams" class="admin-section" style="display:none">
  <h2 class="section-title-h2">📝 إدارة الامتحانات السابقة</h2>
  <div class="admin-form">
    <div class="form-grid">
      <div class="form-group"><label class="form-label">المادة *</label><select class="form-select" id="ex-course"><?php foreach($courses as $c):?><option value="<?= $c['id'] ?>"><?= $c['name'] ?></option><?php endforeach;?></select></div>
      <div class="form-group"><label class="form-label">النوع *</label><select class="form-select" id="ex-type"><option value="final">فاينل</option><option value="midterm">ميدترم</option><option value="quiz">كويز</option><option value="assignment">أسايمنت</option></select></div>
      <div class="form-group"><label class="form-label">السنة</label><input class="form-input" id="ex-year" placeholder="2024"></div>
      <div class="form-group"><label class="form-label">الفصل</label><input class="form-input" id="ex-sem" placeholder="الأول"></div>
    </div>
    <div class="form-group"><label class="form-label">العنوان *</label><input class="form-input" id="ex-title" placeholder="فاينل 2024"></div>
    <div class="form-grid">
      <div class="form-group"><label class="form-label">رابط الامتحان</label><input class="form-input" id="ex-file" placeholder="https://..."></div>
      <div class="form-group"><label class="form-label">رابط الحل النموذجي</label><input class="form-input" id="ex-sol" placeholder="https://..."></div>
      <div class="form-group"><label class="form-label">رابط تيليجرام</label><input class="form-input" id="ex-tg" placeholder="https://t.me/..."></div>
    </div>
    <button class="btn-primary" onclick="examsAdd()">➕ إضافة امتحان</button>
  </div>
  <div class="table-wrap"><div class="table-scroll"><table class="admin-table"><thead><tr><th>المادة</th><th>العنوان</th><th>النوع</th><th>السنة</th><th>روابط</th><th>حذف</th></tr></thead><tbody id="exams-tbody"></tbody></table></div></div>
</div>

<!-- ── Questions ── -->
<div id="sec-questions" class="admin-section" style="display:none">
  <h2 class="section-title-h2">❓ إدارة الأسئلة</h2>
  <div class="admin-form">
    <div class="form-grid">
      <div class="form-group"><label class="form-label">المادة *</label><select class="form-select" id="q-course"><?php foreach($courses as $c):?><option value="<?= $c['id'] ?>"><?= $c['name'] ?></option><?php endforeach;?></select></div>
      <div class="form-group"><label class="form-label">النوع</label><select class="form-select" id="q-type"><option value="mcq">MCQ</option><option value="essay">مقال</option><option value="mixed">ميكس</option><option value="sheet">شيت</option></select></div>
    </div>
    <div class="form-group"><label class="form-label">العنوان *</label><input class="form-input" id="q-title" placeholder="مجموعة أسئلة..."></div>
    <div class="form-grid">
      <div class="form-group"><label class="form-label">رابط الملف</label><input class="form-input" id="q-file" placeholder="https://..."></div>
      <div class="form-group"><label class="form-label">رابط تيليجرام</label><input class="form-input" id="q-tg" placeholder="https://t.me/..."></div>
    </div>
    <button class="btn-primary" onclick="questionsAdd()">➕ إضافة أسئلة</button>
  </div>
  <div class="table-wrap"><div class="table-scroll"><table class="admin-table"><thead><tr><th>المادة</th><th>العنوان</th><th>النوع</th><th>روابط</th><th>حذف</th></tr></thead><tbody id="questions-tbody"></tbody></table></div></div>
</div>

<!-- ── Weekly ── -->
<div id="sec-weekly" class="admin-section" style="display:none">
  <h2 class="section-title-h2">📅 إدارة ملخصات الأسبوع</h2>
  <div class="admin-form">
    <div class="form-grid">
      <div class="form-group"><label class="form-label">المادة *</label><select class="form-select" id="w-course"><?php foreach($courses as $c):?><option value="<?= $c['id'] ?>"><?= $c['name'] ?></option><?php endforeach;?></select></div>
      <div class="form-group"><label class="form-label">رقم الأسبوع *</label><input class="form-input" type="number" id="w-num" placeholder="1" min="1"></div>
    </div>
    <div class="form-group"><label class="form-label">عنوان الأسبوع</label><input class="form-input" id="w-title" placeholder="الأسبوع الأول — ..."></div>
    <div class="form-group"><label class="form-label">رابط تيليجرام *</label><input class="form-input" id="w-url" placeholder="https://t.me/..."></div>
    <button class="btn-primary" onclick="weeklyAdd()">➕ إضافة ملخص</button>
  </div>
  <div class="table-wrap"><div class="table-scroll"><table class="admin-table"><thead><tr><th>المادة</th><th>الأسبوع</th><th>العنوان</th><th>رابط</th><th>حذف</th></tr></thead><tbody id="weekly-tbody"></tbody></table></div></div>
</div>

<!-- ── News ── -->
<div id="sec-news" class="admin-section" style="display:none">
  <h2 class="section-title-h2">📣 إدارة الأخبار</h2>
  <div class="admin-form">
    <div class="form-group"><label class="form-label">العنوان *</label><input class="form-input" id="n-title" placeholder="عنوان الخبر..."></div>
    <div class="form-group"><label class="form-label">المحتوى</label><textarea class="form-textarea" id="n-content" placeholder="تفاصيل الخبر..."></textarea></div>
    <button class="btn-primary" onclick="newsAdd()">➕ إضافة خبر</button>
  </div>
  <div class="table-wrap"><div class="table-scroll"><table class="admin-table"><thead><tr><th>العنوان</th><th>النوع</th><th>التاريخ</th><th>حذف</th></tr></thead><tbody id="news-tbody"></tbody></table></div></div>
</div>

<!-- ── Links (Roadmap View) ── -->
<div id="sec-links" class="admin-section" style="display:none">
  <h2 class="section-title-h2">🔗 مسارات التعلم والروابط</h2>
  <div class="admin-form">
    <div class="form-grid">
      <div class="form-group"><label class="form-label">العنوان *</label><input class="form-input" id="l-title" placeholder="مثال: HTML Course"></div>
      <div class="form-group"><label class="form-label">الأيقونة</label><input class="form-input" id="l-icon" placeholder="🌐"></div>
      <div class="form-group"><label class="form-label">المسار</label><select class="form-select" id="l-type"><option value="frontend">Front-End</option><option value="backend">Back-End</option><option value="ai">AI</option><option value="other">أخرى</option></select></div>
      <div class="form-group"><label class="form-label">الترتيب</label><input class="form-input" type="number" id="l-sort" value="1"></div>
    </div>
    <div class="form-group"><label class="form-label">الرابط *</label><input class="form-input" id="l-url" placeholder="https://..."></div>
    <button class="btn-primary" onclick="linksAdd()">➕ إضافة للـ Roadmap</button>
  </div>
  <div id="links-roadmap-container" style="margin-top:25px;"></div>
</div>

<!-- ── Ticker ── -->
<div id="sec-ticker" class="admin-section" style="display:none">
  <h2 class="section-title-h2">📡 شريط الأخبار</h2>
  <div class="admin-form">
    <div class="form-grid">
      <div class="form-group" style="flex:1"><label class="form-label">الرسالة *</label><input class="form-input" id="t-msg" placeholder="تنبيه: ..."></div>
      <div class="form-group"><label class="form-label">الترتيب</label><input class="form-input" type="number" id="t-sort" value="0"></div>
    </div>
    <button class="btn-primary" onclick="tickerAdd()">➕ إضافة رسالة</button>
  </div>
  <div class="table-wrap"><div class="table-scroll"><table class="admin-table"><thead><tr><th>الرسالة</th><th>الترتيب</th><th>حذف</th></tr></thead><tbody id="ticker-tbody"></tbody></table></div></div>
</div>

<!-- ── Quizzes ── -->
<div id="sec-quizzes" class="admin-section" style="display:none">
  <h2 class="section-title-h2">⏳ الاختبارات (JSON)</h2>
  <textarea id="quizzes-json" style="width:100%; height:400px; font-family:monospace; direction:ltr; padding:16px; border-radius:12px; border:1px solid var(--border); background:var(--card); color:var(--text); margin-bottom:16px; resize:vertical;"></textarea>
  <button class="btn-primary" onclick="quizzesSave()">💾 حفظ الاختبارات</button>
</div>

</div><!-- /admin-main -->
</div><!-- /flex -->
</div><!-- /flex-col -->

<script src="<?= $bp ?>/assets/main.js"></script>

<!-- ══ درع الحماية لمنع الـ Inspect Element والتلاعب بالكونسول ══ -->
<script>
(function() {
    'use strict';

    // 1. تعطيل الزر الأيمن للفأرة تماماً
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    }, false);

    // 2. تعطيل اختصارات لوحة المفاتيح (F12, Ctrl+Shift+I, Ctrl+U, etc.)
    document.addEventListener('keydown', function(e) {
        // F12
        if (e.keyCode === 123) {
            e.preventDefault();
            return false;
        }
        // Ctrl+Shift+I (Inspect)
        if (e.ctrlKey && e.shiftKey && e.keyCode === 73) {
            e.preventDefault();
            return false;
        }
        // Ctrl+Shift+J (Console)
        if (e.ctrlKey && e.shiftKey && e.keyCode === 74) {
            e.preventDefault();
            return false;
        }
        // Ctrl+Shift+C (Element Select)
        if (e.ctrlKey && e.shiftKey && e.keyCode === 67) {
            e.preventDefault();
            return false;
        }
        // Ctrl+U (View Source)
        if (e.ctrlKey && e.keyCode === 85) {
            e.preventDefault();
            return false;
        }
        // Ctrl+S (Save Page)
        if (e.ctrlKey && e.keyCode === 83) {
            e.preventDefault();
            return false;
        }
    }, false);

    // 3. نظام اكتشاف فتح الـ DevTools (Debugger Loop)
    // بمجرد فتح الكونسول، سيتوقف المتصفح عند الـ debugger مما يجعل استخدامه مستحيلاً
    setInterval(function() {
        var startTime = performance.now();
        debugger;
        var endTime = performance.now();
        // إذا استغرق تنفيذ السطر السابق وقتاً طويلاً (وهو ما يحدث عند فتح الأدوات)، يتم إعادة التحميل
        if (endTime - startTime > 100) {
            window.location.reload();
        }
    }, 1000);

    // 4. تعطيل وظيفة الكونسول البرمجية
    if (typeof console !== "undefined") {
        console.log = console.warn = console.error = console.info = function() {
            return false;
        };
    }
})();

/* ── دوال لوحة التحكم ── */
function goSection(key) {
    document.querySelectorAll('.admin-section').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.sidebar-item').forEach(el => el.classList.remove('active'));
    const sec = document.getElementById('sec-' + key);
    if (sec) sec.style.display = '';
    const btn = Array.from(document.querySelectorAll('.sidebar-item')).find(b => b.textContent.includes(key) || b.onclick.toString().includes(key));
    if (btn) btn.classList.add('active');
    if (key !== 'dashboard') window['load_' + key]?.();
    window.location.hash = key;
}

const BASE_PATH = document.querySelector('meta[name="base-path"]')?.content || '';
async function api(action, body, id) {
    const url = BASE_PATH + '/mhd-admin?action=' + action + (id ? '&id=' + id : '');
    const res = await fetch(url, {
        method: body ? 'POST' : 'GET',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: body ? JSON.stringify(body) : undefined,
    });
    if (res.status === 204) return null;
    return res.json();
}

function val(id) { return document.getElementById(id)?.value?.trim() || ''; }
function clear(...ids) { ids.forEach(id => { const el = document.getElementById(id); if(el) el.value = el.type==='color'?'#1A7FD4':''; }); }
function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

/* ══ استدعاء البيانات (نفس المنطق السابق مع حماية البيانات) ══ */
async function load_courses() {
    const { data } = await api('courses_list');
    document.getElementById('courses-tbody').innerHTML = data.map(c => `<tr>
        <td style="font-size:22px">${c.icon||'📚'}</td>
        <td><strong>${esc(c.name)}</strong></td>
        <td><span class="badge badge-blue">${esc(c.code||'—')}</span></td>
        <td>${esc(c.semester||'—')}</td>
        <td><button class="btn-danger" onclick="coursesDel(${c.id})">🗑</button></td>
    </tr>`).join('') || '<tr><td colspan="5">لا توجد مواد</td></tr>';
}
async function coursesAdd() {
    const name = val('c-name'); if (!name) return alert('الاسم مطلوب');
    await api('courses_add', { name, code: val('c-code'), description: val('c-desc'), icon: val('c-icon')||'📚', color: val('c-color'), semester: val('c-semester'), year: val('c-year') });
    clear('c-name','c-code','c-desc','c-icon'); load_courses();
}
async function coursesDel(id) { if(confirm('متأكد؟')) { await api('courses_del', null, id); load_courses(); } }

async function load_materials() {
    const { data } = await api('materials_list');
    document.getElementById('materials-tbody').innerHTML = data.map(m => `<tr>
        <td>${esc(m.course_name)}</td><td>${esc(m.title)}</td><td>${m.type}</td>
        <td><a href="${esc(m.url)}" target="_blank">↗</a></td>
        <td><button class="btn-danger" onclick="materialsDel(${m.id})">🗑</button></td>
    </tr>`).join('') || '<tr><td colspan="5">لا يوجد محتوى</td></tr>';
}
async function materialsAdd() {
    await api('materials_add', { course_id: val('m-course'), title: val('m-title'), url: val('m-url'), type: val('m-type') });
    clear('m-title','m-url'); load_materials();
}
async function materialsDel(id) { if(confirm('حذف؟')) { await api('materials_del', null, id); load_materials(); } }

async function load_news() {
    const { data } = await api('news_list');
    document.getElementById('news-tbody').innerHTML = data.map(n => `<tr>
        <td>${esc(n.title)}</td><td>خبر</td><td>${n.created_at}</td>
        <td><button class="btn-danger" onclick="newsDel(${n.id})">🗑</button></td>
    </tr>`).join('');
}
async function newsAdd() {
    await api('news_add', { title: val('n-title'), content: val('n-content'), type: 'news' });
    clear('n-title','n-content'); load_news();
}
async function newsDel(id) { if(confirm('حذف؟')) { await api('news_del', null, id); load_news(); } }

async function load_quizzes() {
    const { data } = await api('quizzes_get');
    document.getElementById('quizzes-json').value = JSON.stringify(data, null, 2);
}
async function quizzesSave() {
    try {
        const data = JSON.parse(val('quizzes-json'));
        await api('quizzes_save', { data }); alert('تم الحفظ');
    } catch(e) { alert('خطأ في JSON'); }
}

document.addEventListener('DOMContentLoaded', () => {
    const h = window.location.hash.replace('#','') || 'dashboard';
    goSection(h);
});

/* ── دوال إدارة الامتحانات ── */
async function load_exams() {
    const { data } = await api('exams_list');
    document.getElementById('exams-tbody').innerHTML = data.map(ex => `<tr>
        <td>${esc(ex.course_name)}</td>
        <td>${esc(ex.title)}</td>
        <td><span class="badge">${esc(ex.exam_type)}</span></td>
        <td>${esc(ex.year)}</td>
        <td>
            ${ex.file_url ? `<a href="${esc(ex.file_url)}" target="_blank">📄</a>` : ''}
            ${ex.solution_url ? `<a href="${esc(ex.solution_url)}" target="_blank">✅</a>` : ''}
        </td>
        <td><button class="btn-danger" onclick="examsDel(${ex.id})">🗑</button></td>
    </tr>`).join('') || '<tr><td colspan="6">لا توجد امتحانات</td></tr>';
}
async function examsAdd() {
    const body = {
        course_id: val('ex-course'), title: val('ex-title'), year: val('ex-year'),
        semester: val('ex-sem'), exam_type: val('ex-type'), file_url: val('ex-file'),
        solution_url: val('ex-sol'), telegram_url: val('ex-tg')
    };
    if(!body.title) return alert('العنوان مطلوب');
    await api('exams_add', body);
    clear('ex-title','ex-year','ex-sem','ex-file','ex-sol','ex-tg');
    load_exams();
}
async function examsDel(id) { if(confirm('حذف الامتحان؟')) { await api('exams_del', null, id); load_exams(); } }

/* ── دوال إدارة الأسئلة ── */
async function load_questions() {
    const { data } = await api('questions_list');
    document.getElementById('questions-tbody').innerHTML = data.map(q => `<tr>
        <td>${esc(q.course_name)}</td><td>${esc(q.title)}</td><td>${esc(q.type)}</td>
        <td>${q.file_url ? '📁' : ''} ${q.telegram_url ? '🔹' : ''}</td>
        <td><button class="btn-danger" onclick="questionsDel(${q.id})">🗑</button></td>
    </tr>`).join('');
}
async function questionsAdd() {
    await api('questions_add', { 
        course_id: val('q-course'), title: val('q-title'), 
        type: val('q-type'), file_url: val('q-file'), telegram_url: val('q-tg') 
    });
    clear('q-title','q-file','q-tg'); load_questions();
}
async function questionsDel(id) { if(confirm('حذف؟')) { await api('questions_del', null, id); load_questions(); } }

/* ── دوال ملخص الأسبوع ── */
async function load_weekly() {
    const { data } = await api('weekly_list');
    document.getElementById('weekly-tbody').innerHTML = data.map(w => `<tr>
        <td>${esc(w.course_name)}</td><td>${w.week_number}</td><td>${esc(w.week_title)}</td>
        <td><a href="${esc(w.telegram_url)}" target="_blank">🔗</a></td>
        <td><button class="btn-danger" onclick="weeklyDel(${w.id})">🗑</button></td>
    </tr>`).join('');
}
async function weeklyAdd() {
    await api('weekly_add', { 
        course_id: val('w-course'), week_number: val('w-num'), 
        week_title: val('w-title'), telegram_url: val('w-url') 
    });
    clear('w-num','w-title','w-url'); load_weekly();
}
async function weeklyDel(id) { if(confirm('حذف؟')) { await api('weekly_del', null, id); load_weekly(); } }

/* ── دوال الروابط (Roadmap) ── */
async function load_links() {
    const { data } = await api('links_list');
    document.getElementById('links-roadmap-container').innerHTML = data.map(l => `
        <div style="display:flex; justify-content:space-between; background:var(--card); padding:10px; margin-bottom:5px; border-radius:8px; border:1px solid var(--border)">
            <span>${l.icon} ${esc(l.title)} (${l.type})</span>
            <button class="btn-danger" onclick="linksDel(${l.id})">🗑</button>
        </div>
    `).join('');
}
async function linksAdd() {
    await api('links_add', { title: val('l-title'), url: val('l-url'), icon: val('l-icon'), type: val('l-type'), sort_order: val('l-sort') });
    clear('l-title','l-url','l-icon'); load_links();
}
async function linksDel(id) { if(confirm('حذف؟')) { await api('links_del', null, id); load_links(); } }

/* ── دوال شريط الأخبار ── */
async function load_ticker() {
    const { data } = await api('ticker_list');
    document.getElementById('ticker-tbody').innerHTML = data.map(t => `<tr>
        <td>${esc(t.message)}</td><td>${t.sort_order}</td>
        <td><button class="btn-danger" onclick="tickerDel(${t.id})">🗑</button></td>
    </tr>`).join('');
}
async function tickerAdd() {
    await api('ticker_add', { message: val('t-msg'), sort_order: val('t-sort') });
    clear('t-msg'); load_ticker();
}
async function tickerDel(id) { if(confirm('حذف؟')) { await api('ticker_del', null, id); load_ticker(); } }
</script>
<?php endif; ?>
</body>
</html>