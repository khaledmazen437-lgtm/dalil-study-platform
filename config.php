
<?php
// 1. درع الحماية: منع الوصول المباشر للملف من المتصفح
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header("HTTP/1.1 403 Forbidden");
    die("🛡️ Access Denied - غير مصرح لك بالوصول لهذا الملف");
}

// 2. إخفاء الأخطاء في بيئة العمل الحقيقية لمنع تسريب مسارات السيرفر
ini_set('display_errors', 0);
error_reporting(0);

/* ══════════════════════════════════════════════
   دليل — إعدادات الموقع (نسخة محمية ومُشفرة)
══════════════════════════════════════════════ */

// ── إعدادات قاعدة البيانات (مُشفرة بـ Base64 لحمايتها من القراءة المباشرة) ──
// DB_USER Original: admin
// DB_PASS Original: omarsat7788
define('DB_HOST',    'localhost');
define('DB_NAME',    'dalil_db');
define('DB_USER',    base64_decode('YWRtaW4=')); 
define('DB_PASS',    base64_decode('b21hcnNhdDc3ODg='));
define('DB_CHARSET', 'utf8mb4');

// ── إعدادات الموقع ──
define('SITE_NAME', 'دليل');
define('SITE_SUB',  'مازن خالد · الحاسبات');

// مسار الموقع
define('BASE_PATH', '/dalil');

// ── إعدادات الإدمن ──
define('ADMIN_USER', 'admin');
define('ADMIN_SALT', 'mhd_salt_2024');

// تم دمج كلمة المرور الجديدة مع الـ Salt وتشفيرها باستخدام دالة الـ Hash
// كلمة المرور الفعّالة الآن هي: aassddffggghhjjkkll
define('ADMIN_HASH', hash('sha256', 'aassddffggghhjjkkll' . ADMIN_SALT));
