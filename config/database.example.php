<?php
// نموذج لملف database.php
// انسخ هذا الملف إلى database.php وقم بتحديث المعلومات

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
    // ⚠️ مهم: قم بتحديث هذه القيم بمعلومات قاعدة البيانات الخاصة بك
    // لا ترفع ملف database.php إلى Git أو أي مستودع عام
    define('DB_HOST', '127.0.0.1');  // مثال: localhost أو 127.0.0.1
    define('DB_USER', 'your_database_user');  // ⚠️ غيّر هذا باسم المستخدم الخاص بك
    define('DB_PASS', 'your_database_password');  // ⚠️ غيّر هذا بكلمة المرور الخاصة بك
    define('DB_NAME', 'your_database_name');  // ⚠️ غيّر هذا باسم قاعدة البيانات الخاصة بك
    define('DB_PORT', '5432');  // المنفذ (5432 لـ PostgreSQL)
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
