<?php
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header("HTTP/1.1 403 Forbidden");
    die("🛡️ Access Denied - غير مصرح لك بالوصول لهذا الملف");
}

ini_set('display_errors', 0);
error_reporting(0);

define('DB_HOST',    getenv('DALIL_DB_HOST') ?: 'localhost');
define('DB_NAME',    getenv('DALIL_DB_NAME') ?: 'dalil_db');
define('DB_USER',    getenv('DALIL_DB_USER') ?: '');
define('DB_PASS',    getenv('DALIL_DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

define('SITE_NAME', 'دليل');
define('SITE_SUB',  'مازن خالد · الحاسبات');

define('BASE_PATH', getenv('DALIL_BASE_PATH') ?: '/dalil');

define('ADMIN_USER', getenv('DALIL_ADMIN_USER') ?: 'admin');
define('ADMIN_SALT', getenv('DALIL_ADMIN_SALT') ?: '');
define('ADMIN_HASH', hash('sha256', (getenv('DALIL_ADMIN_PASSWORD') ?: '') . ADMIN_SALT));
