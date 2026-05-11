<?php
// =====================================================
//  دليل — API Backend (PHP + MySQL)
//  جميع نقاط الـ API في ملف واحد
// =====================================================
require_once __DIR__ . '/config.php';

// ── CORS & Headers ──────────────────────────────────
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ── Session ─────────────────────────────────────────
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
session_name('dalil_session');
session_start();

// ── Helper functions ─────────────────────────────────
function respond(mixed $data, int $status = 200): never {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function respondError(string $message, int $status = 400): never {
    respond(['error' => $message], $status);
}

function getBody(): array {
    $raw = file_get_contents('php://input');
    if (empty($raw)) return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function requireAdmin(): void {
    if (empty($_SESSION['admin_id'])) {
        respondError('غير مسجل الدخول', 401);
    }
}

function hashPassword(string $password): string {
    return hash('sha256', $password . 'mhd_salt_2024');
}

function intOrNull(mixed $v): ?int {
    return ($v !== null && $v !== '') ? (int)$v : null;
}

function strOrNull(mixed $v): ?string {
    return ($v !== null && $v !== '') ? (string)$v : null;
}

function boolVal2(mixed $v, bool $default = true): int {
    if ($v === null) return $default ? 1 : 0;
    return $v ? 1 : 0;
}

function courseName(int|null $courseId): ?string {
    if (!$courseId) return null;
    $db = getDB();
    $st = $db->prepare('SELECT name FROM courses WHERE id = ?');
    $st->execute([$courseId]);
    $row = $st->fetch();
    return $row ? $row['name'] : null;
}

// ── Router ───────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Strip /api prefix
$path = preg_replace('#^' . preg_quote(API_PREFIX, '#') . '#', '', $uri);
$path = rtrim($path, '/') ?: '/';

// ============================================================
//  ROUTES
// ============================================================

// ── Health ───────────────────────────────────────────────────
if ($method === 'GET' && $path === '/healthz') {
    respond(['status' => 'ok']);
}

// ── Stats ────────────────────────────────────────────────────
if ($method === 'GET' && $path === '/stats/overview') {
    $db = getDB();
    $counts = [];
    foreach (['courses','materials','weekly_summaries','questions','past_exams','news'] as $tbl) {
        $st = $db->query("SELECT COUNT(*) AS cnt FROM `$tbl` WHERE is_active = 1");
        $counts[$tbl] = (int)$st->fetchColumn();
    }
    $st = $db->query("SELECT *, DATE_FORMAT(created_at,'%Y-%m-%dT%H:%i:%sZ') AS created_at FROM news WHERE is_active=1 ORDER BY created_at DESC LIMIT 3");
    respond([
        'totalCourses'          => $counts['courses'],
        'totalMaterials'        => $counts['materials'],
        'totalWeeklySummaries'  => $counts['weekly_summaries'],
        'totalQuestions'        => $counts['questions'],
        'totalPastExams'        => $counts['past_exams'],
        'totalNews'             => $counts['news'],
        'recentNews'            => $st->fetchAll(),
    ]);
}

// ── Admin Login / Logout / Me ─────────────────────────────────
if ($method === 'POST' && $path === '/admin/login') {
    $body = getBody();
    $username = trim($body['username'] ?? '');
    $password = trim($body['password'] ?? '');
    if (!$username || !$password) respondError('بيانات ناقصة');

    $db = getDB();
    $st = $db->prepare('SELECT * FROM admins WHERE username = ?');
    $st->execute([$username]);
    $admin = $st->fetch();

    if (!$admin || $admin['password_hash'] !== hashPassword($password)) {
        respondError('بيانات الدخول غير صحيحة', 401);
    }

    $_SESSION['admin_id']   = $admin['id'];
    $_SESSION['admin_name'] = $admin['name'];
    respond([
        'success' => true,
        'admin'   => ['id' => $admin['id'], 'username' => $admin['username'], 'name' => $admin['name']],
    ]);
}

if ($method === 'POST' && $path === '/admin/logout') {
    session_destroy();
    respond(['success' => true]);
}

if ($method === 'GET' && $path === '/admin/me') {
    if (empty($_SESSION['admin_id'])) respondError('غير مسجل الدخول', 401);
    $db = getDB();
    $st = $db->prepare('SELECT id, username, name FROM admins WHERE id = ?');
    $st->execute([$_SESSION['admin_id']]);
    $admin = $st->fetch();
    if (!$admin) respondError('غير مسجل الدخول', 401);
    respond($admin);
}

// ── Courses ──────────────────────────────────────────────────
if ($method === 'GET' && $path === '/courses') {
    $db = getDB();
    $activeOnly = isset($_GET['active_only']) && $_GET['active_only'] === 'true';
    $where = $activeOnly ? 'WHERE is_active = 1' : '';
    $courses = $db->query("SELECT * FROM courses $where ORDER BY sort_order, id")->fetchAll();
    $result = [];
    foreach ($courses as $c) {
        $st = $db->prepare('SELECT COUNT(*) FROM materials WHERE course_id = ? AND is_active = 1');
        $st->execute([$c['id']]);
        $c['materialCount'] = (int)$st->fetchColumn();
        $c['createdAt']     = $c['created_at'];
        $c['isActive']      = (bool)$c['is_active'];
        $c['sortOrder']     = (int)$c['sort_order'];
        unset($c['created_at'], $c['is_active'], $c['sort_order']);
        $result[] = $c;
    }
    respond($result);
}

if ($method === 'POST' && $path === '/courses') {
    requireAdmin();
    $b = getBody();
    if (empty($b['name'])) respondError('الاسم مطلوب');
    $db = getDB();
    $db->prepare('INSERT INTO courses (name, code, description, icon, color, semester, year, sort_order, is_active) VALUES (?,?,?,?,?,?,?,?,?)')
       ->execute([$b['name'], strOrNull($b['code'] ?? null), strOrNull($b['description'] ?? null),
                  strOrNull($b['icon'] ?? null), strOrNull($b['color'] ?? null),
                  strOrNull($b['semester'] ?? null), strOrNull($b['year'] ?? null),
                  intOrNull($b['sortOrder'] ?? null) ?? 0, boolVal2($b['isActive'] ?? null)]);
    $id = $db->lastInsertId();
    $course = $db->query("SELECT * FROM courses WHERE id = $id")->fetch();
    $course['materialCount'] = 0;
    $course['createdAt']     = $course['created_at'];
    $course['isActive']      = (bool)$course['is_active'];
    unset($course['created_at'], $course['is_active']);
    respond($course, 201);
}

if ($method === 'GET' && preg_match('#^/courses/(\d+)$#', $path, $m)) {
    $db = getDB();
    $st = $db->prepare('SELECT * FROM courses WHERE id = ?');
    $st->execute([$m[1]]);
    $c = $st->fetch();
    if (!$c) respondError('Not found', 404);
    $st2 = $db->prepare('SELECT COUNT(*) FROM materials WHERE course_id = ?');
    $st2->execute([$c['id']]);
    $c['materialCount'] = (int)$st2->fetchColumn();
    $c['createdAt']     = $c['created_at'];
    $c['isActive']      = (bool)$c['is_active'];
    unset($c['created_at'], $c['is_active']);
    respond($c);
}

if ($method === 'GET' && preg_match('#^/courses/(\d+)/materials$#', $path, $m)) {
    $db = getDB();
    $st = $db->prepare('SELECT * FROM courses WHERE id = ?');
    $st->execute([$m[1]]);
    $course = $st->fetch();
    if (!$course) respondError('Not found', 404);

    $st2 = $db->prepare('SELECT COUNT(*) FROM materials WHERE course_id = ?');
    $st2->execute([$course['id']]);
    $course['materialCount'] = (int)$st2->fetchColumn();
    $course['createdAt']     = $course['created_at'];
    $course['isActive']      = (bool)$course['is_active'];
    unset($course['created_at'], $course['is_active']);

    $st3 = $db->prepare('SELECT * FROM materials WHERE course_id = ? AND is_active = 1 ORDER BY sort_order, id');
    $st3->execute([$course['id']]);
    $allMats = $st3->fetchAll();

    $groups = ['summary'=>[],'slides'=>[],'video'=>[],'recording'=>[],'weekly_summary'=>[],'question'=>[],'exam'=>[],'other'=>[]];
    foreach ($allMats as $mat) {
        $key = array_key_exists($mat['type'], $groups) ? $mat['type'] : 'other';
        $mat['createdAt'] = $mat['created_at'];
        $mat['isActive']  = (bool)$mat['is_active'];
        $mat['sortOrder'] = (int)$mat['sort_order'];
        $mat['courseId']  = (int)$mat['course_id'];
        unset($mat['created_at'], $mat['is_active'], $mat['sort_order'], $mat['course_id']);
        $groups[$key][] = $mat;
    }
    respond(['course' => $course, 'materials' => $groups]);
}

if ($method === 'PUT' && preg_match('#^/courses/(\d+)$#', $path, $m)) {
    requireAdmin();
    $b = getBody();
    $db = getDB();
    $fields = [];
    $vals   = [];
    foreach (['name','code','description','icon','color','semester','year'] as $f) {
        if (array_key_exists($f, $b)) { $fields[] = "$f = ?"; $vals[] = $b[$f]; }
    }
    if (array_key_exists('sortOrder', $b)) { $fields[] = 'sort_order = ?'; $vals[] = (int)$b['sortOrder']; }
    if (array_key_exists('isActive',  $b)) { $fields[] = 'is_active = ?';  $vals[] = $b['isActive'] ? 1 : 0; }
    if (empty($fields)) respondError('No fields to update');
    $vals[] = $m[1];
    $db->prepare('UPDATE courses SET ' . implode(', ', $fields) . ' WHERE id = ?')->execute($vals);
    $c = $db->query("SELECT * FROM courses WHERE id = {$m[1]}")->fetch();
    if (!$c) respondError('Not found', 404);
    $st2 = $db->prepare('SELECT COUNT(*) FROM materials WHERE course_id = ?');
    $st2->execute([$c['id']]);
    $c['materialCount'] = (int)$st2->fetchColumn();
    $c['createdAt']     = $c['created_at'];
    $c['isActive']      = (bool)$c['is_active'];
    unset($c['created_at'], $c['is_active']);
    respond($c);
}

if ($method === 'DELETE' && preg_match('#^/courses/(\d+)$#', $path, $m)) {
    requireAdmin();
    getDB()->prepare('DELETE FROM courses WHERE id = ?')->execute([$m[1]]);
    http_response_code(204);
    exit;
}

// ── Materials ────────────────────────────────────────────────
function fmtMat(array $m): array {
    $m['createdAt'] = $m['created_at'];
    $m['isActive']  = (bool)$m['is_active'];
    $m['sortOrder'] = (int)$m['sort_order'];
    $m['courseId']  = $m['course_id'] ? (int)$m['course_id'] : null;
    unset($m['created_at'], $m['is_active'], $m['sort_order'], $m['course_id']);
    return $m;
}

if ($method === 'GET' && $path === '/materials') {
    $db = getDB();
    $where = [];
    $vals  = [];
    if (!empty($_GET['course_id'])) { $where[] = 'course_id = ?'; $vals[] = (int)$_GET['course_id']; }
    if (!empty($_GET['type']))      { $where[] = 'type = ?';      $vals[] = $_GET['type']; }
    $sql = 'SELECT * FROM materials' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY sort_order, id';
    $st = $db->prepare($sql);
    $st->execute($vals);
    respond(array_map('fmtMat', $st->fetchAll()));
}

if ($method === 'POST' && $path === '/materials') {
    requireAdmin();
    $b = getBody();
    if (empty($b['courseId']) || empty($b['title']) || empty($b['url'])) respondError('courseId و title و url مطلوبة');
    $db = getDB();
    $db->prepare('INSERT INTO materials (course_id, title, description, url, type, sort_order, is_active) VALUES (?,?,?,?,?,?,?)')
       ->execute([(int)$b['courseId'], $b['title'], strOrNull($b['description'] ?? null), $b['url'],
                  $b['type'] ?? 'other', intOrNull($b['sortOrder'] ?? null) ?? 0, boolVal2($b['isActive'] ?? null)]);
    $mat = $db->query('SELECT * FROM materials WHERE id = ' . $db->lastInsertId())->fetch();
    respond(fmtMat($mat), 201);
}

if ($method === 'PUT' && preg_match('#^/materials/(\d+)$#', $path, $m)) {
    requireAdmin();
    $b = getBody();
    $db = getDB();
    $fields = [];
    $vals   = [];
    foreach (['title','description','url','type'] as $f) {
        if (array_key_exists($f, $b)) { $fields[] = "$f = ?"; $vals[] = $b[$f]; }
    }
    if (array_key_exists('courseId',  $b)) { $fields[] = 'course_id = ?';  $vals[] = (int)$b['courseId']; }
    if (array_key_exists('sortOrder', $b)) { $fields[] = 'sort_order = ?'; $vals[] = (int)$b['sortOrder']; }
    if (array_key_exists('isActive',  $b)) { $fields[] = 'is_active = ?';  $vals[] = $b['isActive'] ? 1 : 0; }
    if (empty($fields)) respondError('No fields');
    $vals[] = $m[1];
    $db->prepare('UPDATE materials SET ' . implode(', ', $fields) . ' WHERE id = ?')->execute($vals);
    $mat = $db->query("SELECT * FROM materials WHERE id = {$m[1]}")->fetch();
    if (!$mat) respondError('Not found', 404);
    respond(fmtMat($mat));
}

if ($method === 'DELETE' && preg_match('#^/materials/(\d+)$#', $path, $m)) {
    requireAdmin();
    getDB()->prepare('DELETE FROM materials WHERE id = ?')->execute([$m[1]]);
    http_response_code(204);
    exit;
}

// ── Weekly Summaries ─────────────────────────────────────────
function fmtWeekly(array $s, ?string $cName = null): array {
    $s['createdAt']   = $s['created_at'];
    $s['isActive']    = (bool)$s['is_active'];
    $s['weekNumber']  = (int)$s['week_number'];
    $s['courseId']    = $s['course_id'] ? (int)$s['course_id'] : null;
    $s['weekTitle']   = $s['week_title'];
    $s['telegramUrl'] = $s['telegram_url'];
    $s['courseName']  = $cName;
    unset($s['created_at'], $s['is_active'], $s['week_number'], $s['course_id'], $s['week_title'], $s['telegram_url']);
    return $s;
}

if ($method === 'GET' && $path === '/weekly-summaries') {
    $db = getDB();
    $where = [];
    $vals  = [];
    if (!empty($_GET['course_id']))   { $where[] = 'course_id = ?';   $vals[] = (int)$_GET['course_id']; }
    if (!empty($_GET['week_number'])) { $where[] = 'week_number = ?'; $vals[] = (int)$_GET['week_number']; }
    $sql = 'SELECT * FROM weekly_summaries' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY week_number, id';
    $st = $db->prepare($sql);
    $st->execute($vals);
    $rows = $st->fetchAll();
    respond(array_map(fn($s) => fmtWeekly($s, courseName($s['course_id'] ? (int)$s['course_id'] : null)), $rows));
}

if ($method === 'POST' && $path === '/weekly-summaries') {
    requireAdmin();
    $b = getBody();
    if (empty($b['weekNumber']) || empty($b['telegramUrl'])) respondError('weekNumber و telegramUrl مطلوبان');
    $db = getDB();
    $db->prepare('INSERT INTO weekly_summaries (course_id, week_number, week_title, description, telegram_url, is_active) VALUES (?,?,?,?,?,?)')
       ->execute([intOrNull($b['courseId'] ?? null), (int)$b['weekNumber'], strOrNull($b['weekTitle'] ?? null),
                  strOrNull($b['description'] ?? null), $b['telegramUrl'], boolVal2($b['isActive'] ?? null)]);
    $s = $db->query('SELECT * FROM weekly_summaries WHERE id = ' . $db->lastInsertId())->fetch();
    respond(fmtWeekly($s, null), 201);
}

if ($method === 'PUT' && preg_match('#^/weekly-summaries/(\d+)$#', $path, $m)) {
    requireAdmin();
    $b = getBody();
    $db = getDB();
    $fields = [];
    $vals   = [];
    if (array_key_exists('courseId',    $b)) { $fields[] = 'course_id = ?';   $vals[] = intOrNull($b['courseId']); }
    if (array_key_exists('weekNumber',  $b)) { $fields[] = 'week_number = ?'; $vals[] = (int)$b['weekNumber']; }
    if (array_key_exists('weekTitle',   $b)) { $fields[] = 'week_title = ?';  $vals[] = $b['weekTitle']; }
    if (array_key_exists('description', $b)) { $fields[] = 'description = ?'; $vals[] = $b['description']; }
    if (array_key_exists('telegramUrl', $b)) { $fields[] = 'telegram_url = ?';$vals[] = $b['telegramUrl']; }
    if (array_key_exists('isActive',    $b)) { $fields[] = 'is_active = ?';   $vals[] = $b['isActive'] ? 1 : 0; }
    if (empty($fields)) respondError('No fields');
    $vals[] = $m[1];
    $db->prepare('UPDATE weekly_summaries SET ' . implode(', ', $fields) . ' WHERE id = ?')->execute($vals);
    $s = $db->query("SELECT * FROM weekly_summaries WHERE id = {$m[1]}")->fetch();
    if (!$s) respondError('Not found', 404);
    respond(fmtWeekly($s, courseName($s['course_id'] ? (int)$s['course_id'] : null)));
}

if ($method === 'DELETE' && preg_match('#^/weekly-summaries/(\d+)$#', $path, $m)) {
    requireAdmin();
    getDB()->prepare('DELETE FROM weekly_summaries WHERE id = ?')->execute([$m[1]]);
    http_response_code(204);
    exit;
}

// ── Questions ────────────────────────────────────────────────
function fmtQ(array $q, ?string $cName = null): array {
    $q['createdAt']   = $q['created_at'];
    $q['isActive']    = (bool)$q['is_active'];
    $q['courseId']    = $q['course_id'] ? (int)$q['course_id'] : null;
    $q['fileUrl']     = $q['file_url'];
    $q['telegramUrl'] = $q['telegram_url'];
    $q['courseName']  = $cName;
    unset($q['created_at'], $q['is_active'], $q['course_id'], $q['file_url'], $q['telegram_url']);
    return $q;
}

if ($method === 'GET' && $path === '/questions') {
    $db = getDB();
    $where = [];
    $vals  = [];
    if (!empty($_GET['course_id'])) { $where[] = 'course_id = ?'; $vals[] = (int)$_GET['course_id']; }
    if (!empty($_GET['type']))      { $where[] = 'type = ?';      $vals[] = $_GET['type']; }
    $sql = 'SELECT * FROM questions' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY id';
    $st = $db->prepare($sql);
    $st->execute($vals);
    $rows = $st->fetchAll();
    respond(array_map(fn($q) => fmtQ($q, courseName($q['course_id'] ? (int)$q['course_id'] : null)), $rows));
}

if ($method === 'POST' && $path === '/questions') {
    requireAdmin();
    $b = getBody();
    if (empty($b['title'])) respondError('title مطلوب');
    $db = getDB();
    $db->prepare('INSERT INTO questions (course_id, title, description, type, file_url, telegram_url, is_active) VALUES (?,?,?,?,?,?,?)')
       ->execute([intOrNull($b['courseId'] ?? null), $b['title'], strOrNull($b['description'] ?? null),
                  $b['type'] ?? 'mixed', strOrNull($b['fileUrl'] ?? null),
                  strOrNull($b['telegramUrl'] ?? null), boolVal2($b['isActive'] ?? null)]);
    $q = $db->query('SELECT * FROM questions WHERE id = ' . $db->lastInsertId())->fetch();
    respond(fmtQ($q, null), 201);
}

if ($method === 'PUT' && preg_match('#^/questions/(\d+)$#', $path, $m)) {
    requireAdmin();
    $b = getBody();
    $db = getDB();
    $fields = [];
    $vals   = [];
    if (array_key_exists('courseId',    $b)) { $fields[] = 'course_id = ?';   $vals[] = intOrNull($b['courseId']); }
    if (array_key_exists('title',       $b)) { $fields[] = 'title = ?';       $vals[] = $b['title']; }
    if (array_key_exists('description', $b)) { $fields[] = 'description = ?'; $vals[] = $b['description']; }
    if (array_key_exists('type',        $b)) { $fields[] = 'type = ?';        $vals[] = $b['type']; }
    if (array_key_exists('fileUrl',     $b)) { $fields[] = 'file_url = ?';    $vals[] = $b['fileUrl']; }
    if (array_key_exists('telegramUrl', $b)) { $fields[] = 'telegram_url = ?';$vals[] = $b['telegramUrl']; }
    if (array_key_exists('isActive',    $b)) { $fields[] = 'is_active = ?';   $vals[] = $b['isActive'] ? 1 : 0; }
    if (empty($fields)) respondError('No fields');
    $vals[] = $m[1];
    $db->prepare('UPDATE questions SET ' . implode(', ', $fields) . ' WHERE id = ?')->execute($vals);
    $q = $db->query("SELECT * FROM questions WHERE id = {$m[1]}")->fetch();
    if (!$q) respondError('Not found', 404);
    respond(fmtQ($q, courseName($q['course_id'] ? (int)$q['course_id'] : null)));
}

if ($method === 'DELETE' && preg_match('#^/questions/(\d+)$#', $path, $m)) {
    requireAdmin();
    getDB()->prepare('DELETE FROM questions WHERE id = ?')->execute([$m[1]]);
    http_response_code(204);
    exit;
}

// ── Past Exams ───────────────────────────────────────────────
function fmtExam(array $e, ?string $cName = null): array {
    $e['createdAt']   = $e['created_at'];
    $e['isActive']    = (bool)$e['is_active'];
    $e['courseId']    = $e['course_id'] ? (int)$e['course_id'] : null;
    $e['examType']    = $e['exam_type'];
    $e['fileUrl']     = $e['file_url'];
    $e['solutionUrl'] = $e['solution_url'];
    $e['telegramUrl'] = $e['telegram_url'];
    $e['courseName']  = $cName;
    $e['year']        = $e['year'] ? (int)$e['year'] : null;
    unset($e['created_at'], $e['is_active'], $e['course_id'], $e['exam_type'],
          $e['file_url'], $e['solution_url'], $e['telegram_url']);
    return $e;
}

if ($method === 'GET' && $path === '/past-exams') {
    $db = getDB();
    $where = [];
    $vals  = [];
    if (!empty($_GET['course_id']))  { $where[] = 'course_id = ?';  $vals[] = (int)$_GET['course_id']; }
    if (!empty($_GET['exam_type']))  { $where[] = 'exam_type = ?';  $vals[] = $_GET['exam_type']; }
    if (!empty($_GET['year']))       { $where[] = 'year = ?';       $vals[] = (int)$_GET['year']; }
    $sql = 'SELECT * FROM past_exams' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY year DESC, id';
    $st = $db->prepare($sql);
    $st->execute($vals);
    $rows = $st->fetchAll();
    respond(array_map(fn($e) => fmtExam($e, courseName($e['course_id'] ? (int)$e['course_id'] : null)), $rows));
}

if ($method === 'POST' && $path === '/past-exams') {
    requireAdmin();
    $b = getBody();
    if (empty($b['title'])) respondError('title مطلوب');
    $db = getDB();
    $db->prepare('INSERT INTO past_exams (course_id, title, description, exam_type, year, semester, file_url, solution_url, telegram_url, is_active) VALUES (?,?,?,?,?,?,?,?,?,?)')
       ->execute([intOrNull($b['courseId'] ?? null), $b['title'], strOrNull($b['description'] ?? null),
                  $b['examType'] ?? 'final', intOrNull($b['year'] ?? null), strOrNull($b['semester'] ?? null),
                  strOrNull($b['fileUrl'] ?? null), strOrNull($b['solutionUrl'] ?? null),
                  strOrNull($b['telegramUrl'] ?? null), boolVal2($b['isActive'] ?? null)]);
    $e = $db->query('SELECT * FROM past_exams WHERE id = ' . $db->lastInsertId())->fetch();
    respond(fmtExam($e, null), 201);
}

if ($method === 'PUT' && preg_match('#^/past-exams/(\d+)$#', $path, $m)) {
    requireAdmin();
    $b = getBody();
    $db = getDB();
    $fields = [];
    $vals   = [];
    if (array_key_exists('courseId',    $b)) { $fields[] = 'course_id = ?';   $vals[] = intOrNull($b['courseId']); }
    if (array_key_exists('title',       $b)) { $fields[] = 'title = ?';       $vals[] = $b['title']; }
    if (array_key_exists('description', $b)) { $fields[] = 'description = ?'; $vals[] = $b['description']; }
    if (array_key_exists('examType',    $b)) { $fields[] = 'exam_type = ?';   $vals[] = $b['examType']; }
    if (array_key_exists('year',        $b)) { $fields[] = 'year = ?';        $vals[] = intOrNull($b['year']); }
    if (array_key_exists('semester',    $b)) { $fields[] = 'semester = ?';    $vals[] = $b['semester']; }
    if (array_key_exists('fileUrl',     $b)) { $fields[] = 'file_url = ?';    $vals[] = $b['fileUrl']; }
    if (array_key_exists('solutionUrl', $b)) { $fields[] = 'solution_url = ?';$vals[] = $b['solutionUrl']; }
    if (array_key_exists('telegramUrl', $b)) { $fields[] = 'telegram_url = ?';$vals[] = $b['telegramUrl']; }
    if (array_key_exists('isActive',    $b)) { $fields[] = 'is_active = ?';   $vals[] = $b['isActive'] ? 1 : 0; }
    if (empty($fields)) respondError('No fields');
    $vals[] = $m[1];
    $db->prepare('UPDATE past_exams SET ' . implode(', ', $fields) . ' WHERE id = ?')->execute($vals);
    $e = $db->query("SELECT * FROM past_exams WHERE id = {$m[1]}")->fetch();
    if (!$e) respondError('Not found', 404);
    respond(fmtExam($e, courseName($e['course_id'] ? (int)$e['course_id'] : null)));
}

if ($method === 'DELETE' && preg_match('#^/past-exams/(\d+)$#', $path, $m)) {
    requireAdmin();
    getDB()->prepare('DELETE FROM past_exams WHERE id = ?')->execute([$m[1]]);
    http_response_code(204);
    exit;
}

// ── News ─────────────────────────────────────────────────────
function fmtNews(array $n): array {
    $n['createdAt'] = $n['created_at'];
    $n['isActive']  = (bool)$n['is_active'];
    $n['isTicker']  = (bool)$n['is_ticker'];
    unset($n['created_at'], $n['is_active'], $n['is_ticker']);
    return $n;
}

if ($method === 'GET' && $path === '/news') {
    $db = getDB();
    $where = [];
    $vals  = [];
    if (!empty($_GET['type']))         { $where[] = 'type = ?';      $vals[] = $_GET['type']; }
    if (!empty($_GET['ticker_only']))   { $where[] = 'is_ticker = 1'; }
    $sql = 'SELECT * FROM news' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY created_at DESC';
    $st = $db->prepare($sql);
    $st->execute($vals);
    respond(array_map('fmtNews', $st->fetchAll()));
}

if ($method === 'POST' && $path === '/news') {
    requireAdmin();
    $b = getBody();
    if (empty($b['title'])) respondError('title مطلوب');
    $db = getDB();
    $db->prepare('INSERT INTO news (title, content, type, is_ticker, is_active) VALUES (?,?,?,?,?)')
       ->execute([$b['title'], strOrNull($b['content'] ?? null), $b['type'] ?? 'news',
                  isset($b['isTicker']) && $b['isTicker'] ? 1 : 0, boolVal2($b['isActive'] ?? null)]);
    $n = $db->query('SELECT * FROM news WHERE id = ' . $db->lastInsertId())->fetch();
    respond(fmtNews($n), 201);
}

if ($method === 'PUT' && preg_match('#^/news/(\d+)$#', $path, $m)) {
    requireAdmin();
    $b = getBody();
    $db = getDB();
    $fields = [];
    $vals   = [];
    if (array_key_exists('title',    $b)) { $fields[] = 'title = ?';    $vals[] = $b['title']; }
    if (array_key_exists('content',  $b)) { $fields[] = 'content = ?';  $vals[] = $b['content']; }
    if (array_key_exists('type',     $b)) { $fields[] = 'type = ?';     $vals[] = $b['type']; }
    if (array_key_exists('isTicker', $b)) { $fields[] = 'is_ticker = ?';$vals[] = $b['isTicker'] ? 1 : 0; }
    if (array_key_exists('isActive', $b)) { $fields[] = 'is_active = ?';$vals[] = $b['isActive'] ? 1 : 0; }
    if (empty($fields)) respondError('No fields');
    $vals[] = $m[1];
    $db->prepare('UPDATE news SET ' . implode(', ', $fields) . ' WHERE id = ?')->execute($vals);
    $n = $db->query("SELECT * FROM news WHERE id = {$m[1]}")->fetch();
    if (!$n) respondError('Not found', 404);
    respond(fmtNews($n));
}

if ($method === 'DELETE' && preg_match('#^/news/(\d+)$#', $path, $m)) {
    requireAdmin();
    getDB()->prepare('DELETE FROM news WHERE id = ?')->execute([$m[1]]);
    http_response_code(204);
    exit;
}

// ── Ticker Messages ──────────────────────────────────────────
function fmtTicker(array $t): array {
    $t['isActive']  = (bool)$t['is_active'];
    $t['sortOrder'] = (int)$t['sort_order'];
    $t['createdAt'] = $t['created_at'];
    unset($t['is_active'], $t['sort_order'], $t['created_at']);
    return $t;
}

if ($method === 'GET' && $path === '/ticker') {
    $st = getDB()->query('SELECT * FROM ticker_messages ORDER BY sort_order, id');
    respond(array_map('fmtTicker', $st->fetchAll()));
}

if ($method === 'POST' && $path === '/ticker') {
    requireAdmin();
    $b = getBody();
    if (empty($b['message'])) respondError('message مطلوب');
    $db = getDB();
    $db->prepare('INSERT INTO ticker_messages (message, sort_order, is_active) VALUES (?,?,?)')
       ->execute([$b['message'], intOrNull($b['sortOrder'] ?? null) ?? 0, boolVal2($b['isActive'] ?? null)]);
    $t = $db->query('SELECT * FROM ticker_messages WHERE id = ' . $db->lastInsertId())->fetch();
    respond(fmtTicker($t), 201);
}

if ($method === 'PUT' && preg_match('#^/ticker/(\d+)$#', $path, $m)) {
    requireAdmin();
    $b = getBody();
    $db = getDB();
    $fields = [];
    $vals   = [];
    if (array_key_exists('message',   $b)) { $fields[] = 'message = ?';    $vals[] = $b['message']; }
    if (array_key_exists('sortOrder', $b)) { $fields[] = 'sort_order = ?'; $vals[] = (int)$b['sortOrder']; }
    if (array_key_exists('isActive',  $b)) { $fields[] = 'is_active = ?';  $vals[] = $b['isActive'] ? 1 : 0; }
    if (empty($fields)) respondError('No fields');
    $vals[] = $m[1];
    $db->prepare('UPDATE ticker_messages SET ' . implode(', ', $fields) . ' WHERE id = ?')->execute($vals);
    $t = $db->query("SELECT * FROM ticker_messages WHERE id = {$m[1]}")->fetch();
    if (!$t) respondError('Not found', 404);
    respond(fmtTicker($t));
}

if ($method === 'DELETE' && preg_match('#^/ticker/(\d+)$#', $path, $m)) {
    requireAdmin();
    getDB()->prepare('DELETE FROM ticker_messages WHERE id = ?')->execute([$m[1]]);
    http_response_code(204);
    exit;
}

// ── Important Links ──────────────────────────────────────────
function fmtLink(array $l): array {
    $l['isActive']  = (bool)$l['is_active'];
    $l['sortOrder'] = (int)$l['sort_order'];
    unset($l['is_active'], $l['sort_order']);
    return $l;
}

if ($method === 'GET' && $path === '/links') {
    $st = getDB()->query('SELECT * FROM important_links WHERE is_active = 1 ORDER BY sort_order, id');
    respond(array_map('fmtLink', $st->fetchAll()));
}

if ($method === 'POST' && $path === '/links') {
    requireAdmin();
    $b = getBody();
    if (empty($b['title']) || empty($b['url'])) respondError('title و url مطلوبان');
    $db = getDB();
    $db->prepare('INSERT INTO important_links (title, url, icon, type, description, sort_order, is_active) VALUES (?,?,?,?,?,?,?)')
       ->execute([$b['title'], $b['url'], strOrNull($b['icon'] ?? null), $b['type'] ?? 'other',
                  strOrNull($b['description'] ?? null),
                  intOrNull($b['sortOrder'] ?? null) ?? 0, boolVal2($b['isActive'] ?? null)]);
    $l = $db->query('SELECT * FROM important_links WHERE id = ' . $db->lastInsertId())->fetch();
    respond(fmtLink($l), 201);
}

if ($method === 'PUT' && preg_match('#^/links/(\d+)$#', $path, $m)) {
    requireAdmin();
    $b = getBody();
    $db = getDB();
    $fields = [];
    $vals   = [];
    foreach (['title','url','icon','type','description'] as $f) {
        if (array_key_exists($f, $b)) { $fields[] = "$f = ?"; $vals[] = $b[$f]; }
    }
    if (array_key_exists('sortOrder', $b)) { $fields[] = 'sort_order = ?'; $vals[] = (int)$b['sortOrder']; }
    if (array_key_exists('isActive',  $b)) { $fields[] = 'is_active = ?';  $vals[] = $b['isActive'] ? 1 : 0; }
    if (empty($fields)) respondError('No fields');
    $vals[] = $m[1];
    $db->prepare('UPDATE important_links SET ' . implode(', ', $fields) . ' WHERE id = ?')->execute($vals);
    $l = $db->query("SELECT * FROM important_links WHERE id = {$m[1]}")->fetch();
    if (!$l) respondError('Not found', 404);
    respond(fmtLink($l));
}

if ($method === 'DELETE' && preg_match('#^/links/(\d+)$#', $path, $m)) {
    requireAdmin();
    getDB()->prepare('DELETE FROM important_links WHERE id = ?')->execute([$m[1]]);
    http_response_code(204);
    exit;
}

// ── 404 ──────────────────────────────────────────────────────
respondError("Route not found: $method $path", 404);
