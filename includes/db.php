<?php
/* ─────────────────────────────────────────
   اتصال بقاعدة البيانات + دوال مساعدة
───────────────────────────────────────── */
function getDB(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;
   require_once dirname(__DIR__) . '/config.php';
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        die('<div style="font-family:Cairo,sans-serif;direction:rtl;text-align:center;padding:50px;color:#dc2626;background:#fef2f2">
            ❌ خطأ في الاتصال بقاعدة البيانات<br><small>'.$e->getMessage().'</small>
        </div>');
    }
    return $pdo;
}

function db_all(string $sql, array $params = []): array {
    $st = getDB()->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}

function db_one(string $sql, array $params = []) {
    $st = getDB()->prepare($sql);
    $st->execute($params);
    return $st->fetch();
}

function db_val(string $sql, array $params = []) {
    $st = getDB()->prepare($sql);
    $st->execute($params);
    return $st->fetchColumn();
}

function e($v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function active(string $page): string {
    global $currentPage;
    return ($currentPage ?? '') === $page ? ' active' : '';
}