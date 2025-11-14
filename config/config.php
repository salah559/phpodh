<?php
/**
 * ملف الإعدادات العامة للموقع
 */

// تحديد المسار الأساسي للموقع تلقائياً
// يعمل على Replit و cPanel بدون تغيير
if (php_sapi_name() === 'cli') {
    // CLI mode - استخدام قيم افتراضية
    define('BASE_URL', '');
} else {
    // Web mode
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // حساب مجلد المشروع الرئيسي بناءً على موقع config/config.php
    // __DIR__ يعطي مجلد config/، dirname(__DIR__) يعطي مجلد المشروع
    $project_root = dirname(__DIR__);
    $document_root = $_SERVER['DOCUMENT_ROOT'] ?? '';
    
    // حساب المسار النسبي من document root
    $project_root = str_replace('\\', '/', $project_root);
    $document_root = str_replace('\\', '/', rtrim($document_root, '/'));
    
    // إزالة document root من project root للحصول على المسار النسبي
    if ($document_root && strpos($project_root, $document_root) === 0) {
        $base_path = substr($project_root, strlen($document_root));
    } else {
        // fallback: استخدام dirname(SCRIPT_NAME) للصفحات الرئيسية فقط
        $script_name = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        // إزالة /api/ أو أي مجلد فرعي من المسار
        $script_parts = explode('/', trim($script_name, '/'));
        // أخذ الجزء الأول فقط (المجلد الرئيسي للمشروع)
        if (count($script_parts) > 1 && !in_array($script_parts[count($script_parts) - 1], ['index.php', 'admin.php', 'products.php', 'orders.php'])) {
            // إذا كان السكريبت في مجلد فرعي (api, auth)، نحذف المجلد الفرعي
            array_pop($script_parts); // حذف اسم الملف
            array_pop($script_parts); // حذف المجلد الفرعي (api, auth)
        } else {
            array_pop($script_parts); // حذف اسم الملف فقط
        }
        $base_path = '/' . implode('/', $script_parts);
        $base_path = $base_path === '/' ? '' : $base_path;
    }
    
    // تنظيف المسار
    $base_path = $base_path === '/' ? '' : $base_path;
    
    define('BASE_URL', $protocol . '://' . $host . $base_path);
}

define('SITE_NAME', 'أضحيتي');
define('SITE_DESCRIPTION', 'منصة بيع الأغنام والأضاحي');

/**
 * دالة مساعدة للحصول على رابط كامل
 */
function url($path = '') {
    if (BASE_URL === '') {
        return '/' . ltrim($path, '/');
    }
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * دالة مساعدة للحصول على رابط الأصول (CSS, JS, Images)
 */
function asset($path) {
    if (BASE_URL === '') {
        return '/' . ltrim($path, '/');
    }
    return BASE_URL . '/' . ltrim($path, '/');
}
