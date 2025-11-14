<?php
// يمكنك تغيير هذه الإعدادات حسب بيئة الاستضافة
// للاستخدام في Replit: اترك كما هو
// للاستخدام في cPanel: قم بتعديل القيم أدناه

$db_url = getenv('DATABASE_URL');
if ($db_url) {
    // بيئة Replit - استخدام DATABASE_URL
    $db = parse_url($db_url);
    
    define('DB_HOST', $db['host'] ?? 'localhost');
    define('DB_USER', $db['user'] ?? 'root');
    define('DB_PASS', $db['pass'] ?? '');
    define('DB_NAME', ltrim($db['path'] ?? '/adhiyati_db', '/'));
    define('DB_PORT', $db['port'] ?? '5432');
} else {
    // بيئة cPanel - إعدادات قاعدة البيانات
    define('DB_HOST', 'localhost');
    define('DB_USER', 'ctdccyqq_salah');
    define('DB_PASS', 'silo@salah55');
    define('DB_NAME', 'ctdccyqq_odh');
    define('DB_PORT', '5432');
}

function getDBConnection() {
    try {
        $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch(PDOException $e) {
        // في حالة الخطأ، اعرض رسالة واضحة للمطور
        if (getenv('DATABASE_URL')) {
            die("خطأ في الاتصال بقاعدة البيانات. تحقق من إعدادات DATABASE_URL");
        } else {
            die("خطأ في الاتصال بقاعدة البيانات. تحقق من إعدادات قاعدة البيانات في ملف config/database.php<br>التفاصيل: " . $e->getMessage());
        }
    }
}
?>
