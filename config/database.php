<?php
// يمكنك تغيير هذه الإعدادات حسب بيئة الاستضافة
// للاستخدام في Replit: اترك كما هو (يستخدم PostgreSQL تلقائياً)
// للاستخدام في cPanel: قم بتعديل القيم أدناه (يستخدم MySQL)

// تحديد نوع قاعدة البيانات
// 'mysql' لـ cPanel/معظم الاستضافات
// 'pgsql' لـ Replit/PostgreSQL
$db_url = getenv('DATABASE_URL');
if ($db_url) {
    // بيئة Replit - استخدام PostgreSQL
    $db = parse_url($db_url);
    
    define('DB_TYPE', 'pgsql');
    define('DB_HOST', $db['host'] ?? 'localhost');
    define('DB_USER', $db['user'] ?? 'root');
    define('DB_PASS', $db['pass'] ?? '');
    define('DB_NAME', ltrim($db['path'] ?? '/adhiyati_db', '/'));
    define('DB_PORT', $db['port'] ?? '5432');
} else {
    // بيئة cPanel - استخدام MySQL
    // ⚠️ مهم: قم بتحديث هذه القيم بمعلومات قاعدة البيانات الخاصة بك
    // لا ترفع هذا الملف إلى Git أو أي مستودع عام
    define('DB_TYPE', 'mysql');
    define('DB_HOST', 'localhost');
    define('DB_USER', 'your_mysql_username');  // ⚠️ غيّر هذا
    define('DB_PASS', 'your_mysql_password');  // ⚠️ غيّر هذا  
    define('DB_NAME', 'your_mysql_database');  // ⚠️ غيّر هذا
    define('DB_PORT', '3306');  // MySQL default port
}

function getDBConnection() {
    try {
        // إنشاء DSN حسب نوع قاعدة البيانات
        if (DB_TYPE === 'mysql') {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        } else {
            $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
        }
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // لـ MySQL: ضبط الترميز العربي
        if (DB_TYPE === 'mysql') {
            $pdo->exec("SET NAMES utf8mb4");
            $pdo->exec("SET CHARACTER SET utf8mb4");
        }
        
        return $pdo;
    } catch(PDOException $e) {
        // في حالة الخطأ، اعرض رسالة واضحة للمطور
        if (getenv('DATABASE_URL')) {
            die("خطأ في الاتصال بقاعدة البيانات PostgreSQL. تحقق من إعدادات DATABASE_URL<br>التفاصيل: " . $e->getMessage());
        } else {
            die("خطأ في الاتصال بقاعدة بيانات MySQL. تحقق من إعدادات قاعدة البيانات في ملف config/database.php<br>التفاصيل: " . $e->getMessage());
        }
    }
}
?>
