<?php
/**
 * ملف للتحقق من إعدادات الخادم
 * ارفع هذا الملف إلى cPanel وافتحه في المتصفح
 * https://yourdomain.com/check_server.php
 */

echo "<html dir='rtl'>";
echo "<head><meta charset='UTF-8'><title>فحص الخادم</title></head>";
echo "<body style='font-family: Arial; padding: 20px;'>";
echo "<h1>تقرير فحص الخادم</h1>";
echo "<hr>";

// 1. فحص إصدار PHP
echo "<h2>✅ إصدار PHP</h2>";
echo "<p><strong>" . phpversion() . "</strong></p>";
if (version_compare(phpversion(), '7.4', '<')) {
    echo "<p style='color:red;'>⚠️ تحذير: يُنصح باستخدام PHP 7.4 أو أحدث</p>";
}

// 2. فحص امتدادات PDO
echo "<h2>📦 امتدادات PDO</h2>";
$pdo_extensions = [
    'PDO' => extension_loaded('pdo'),
    'pdo_mysql' => extension_loaded('pdo_mysql'),
    'pdo_pgsql' => extension_loaded('pdo_pgsql'),
];

foreach ($pdo_extensions as $ext => $loaded) {
    $status = $loaded ? '✅ مُفعّل' : '❌ غير مُفعّل';
    $color = $loaded ? 'green' : 'red';
    echo "<p><strong>$ext:</strong> <span style='color:$color;'>$status</span></p>";
}

// 3. فحص الامتدادات الأخرى
echo "<h2>🔧 امتدادات PHP الأخرى</h2>";
$other_extensions = ['mbstring', 'json', 'session', 'curl'];
foreach ($other_extensions as $ext) {
    $loaded = extension_loaded($ext);
    $status = $loaded ? '✅ مُفعّل' : '❌ غير مُفعّل';
    $color = $loaded ? 'green' : 'red';
    echo "<p><strong>$ext:</strong> <span style='color:$color;'>$status</span></p>";
}

// 4. فحص الاتصال بقاعدة البيانات
echo "<h2>🗄️ فحص الاتصال بقاعدة البيانات</h2>";

if (file_exists('config/database.php')) {
    try {
        require_once 'config/database.php';
        
        echo "<p><strong>نوع قاعدة البيانات:</strong> <strong style='color:#D4AF37;'>" . DB_TYPE . "</strong></p>";
        echo "<p><strong>إعدادات الاتصال:</strong></p>";
        echo "<ul>";
        echo "<li>الخادم: " . DB_HOST . "</li>";
        echo "<li>المنفذ: " . DB_PORT . "</li>";
        echo "<li>قاعدة البيانات: " . DB_NAME . "</li>";
        echo "<li>المستخدم: " . DB_USER . "</li>";
        echo "</ul>";
        
        // تحديد الامتداد المطلوب حسب نوع القاعدة
        $required_ext = DB_TYPE === 'mysql' ? 'pdo_mysql' : 'pdo_pgsql';
        $db_name_ar = DB_TYPE === 'mysql' ? 'MySQL' : 'PostgreSQL';
        
        if (!extension_loaded($required_ext)) {
            echo "<p style='color:red; background:#ffe6e6; padding:15px; border-radius:5px;'>";
            echo "<strong>❌ خطأ:</strong> امتداد <strong>$required_ext</strong> غير مُفعّل في الخادم!<br>";
            if (DB_TYPE === 'mysql') {
                echo "يجب تفعيل MySQL PDO من إعدادات PHP في cPanel.<br>";
                echo "اذهب إلى: cPanel → Select PHP Version → Extensions → تأكد من تفعيل pdo و pdo_mysql";
            } else {
                echo "يجب تفعيل PostgreSQL PDO من إعدادات PHP في cPanel أو الاتصال بالدعم الفني.";
            }
            echo "</p>";
        } else {
            echo "<p style='color:green; background:#e6ffe6; padding:10px; border-radius:5px;'>";
            echo "✅ امتداد <strong>$required_ext</strong> مُفعّل بنجاح";
            echo "</p>";
            
            $pdo = getDBConnection();
            echo "<p style='color:green; background:#e6ffe6; padding:15px; border-radius:5px;'>";
            echo "✅ <strong>نجح الاتصال بقاعدة البيانات $db_name_ar!</strong>";
            echo "</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red; background:#ffe6e6; padding:15px; border-radius:5px;'>";
        echo "<strong>❌ فشل الاتصال:</strong><br>";
        echo $e->getMessage();
        echo "</p>";
    }
} else {
    echo "<p style='color:orange;'>⚠️ ملف database.php غير موجود</p>";
}

// 5. معلومات إضافية
echo "<h2>ℹ️ معلومات إضافية</h2>";
echo "<p><strong>نظام التشغيل:</strong> " . PHP_OS . "</p>";
echo "<p><strong>معمارية الخادم:</strong> " . php_uname('m') . "</p>";

echo "<hr>";
echo "<p style='color:#666; font-size:12px;'>⚠️ احذف هذا الملف بعد الفحص لأسباب أمنية</p>";
echo "</body></html>";
?>
