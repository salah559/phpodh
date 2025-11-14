<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعداد قاعدة البيانات - أضحيتي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background: #f8f9fa; padding: 50px 0; }
        .setup-card { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="setup-card">
            <h1 class="text-center mb-4">🐑 إعداد قاعدة البيانات</h1>
            
            <?php
            if (!isset($_GET['confirm'])) {
                ?>
                <div class="warning">
                    <h5>⚠️ تحذير مهم</h5>
                    <p>هذا السكريبت سيقوم بإنشاء الجداول وإضافة البيانات التجريبية.</p>
                    <p><strong>يجب تشغيل هذا الملف مرة واحدة فقط!</strong></p>
                    <p>بعد انتهاء التثبيت، احذف هذا الملف فوراً من السيرفر للأمان.</p>
                </div>
                <div class="text-center">
                    <a href="?confirm=yes" class="btn btn-primary btn-lg">متابعة التثبيت</a>
                    <a href="index.php" class="btn btn-secondary btn-lg">إلغاء</a>
                </div>
                <?php
            } else {
                echo "<div class='mb-3'>";
                
                // Step 1: Initialize database
                echo "<h5>1️⃣ إنشاء الجداول...</h5>";
                try {
                    require_once 'config/init_db.php';
                    initializeDatabase();
                    echo "<p class='success'>✅ تم إنشاء الجداول بنجاح!</p>";
                } catch (Exception $e) {
                    echo "<p class='error'>❌ خطأ في إنشاء الجداول: " . htmlspecialchars($e->getMessage()) . "</p>";
                }
                
                echo "<hr>";
                
                // Step 2: Seed data
                echo "<h5>2️⃣ إضافة البيانات التجريبية...</h5>";
                try {
                    require_once 'config/seed_data.php';
                    seedSampleData();
                    echo "<p class='success'>✅ تم إضافة البيانات التجريبية بنجاح!</p>";
                } catch (Exception $e) {
                    echo "<p class='error'>❌ خطأ في إضافة البيانات: " . htmlspecialchars($e->getMessage()) . "</p>";
                }
                
                echo "<hr>";
                
                // Final message
                echo "<div class='alert alert-success mt-4'>";
                echo "<h5>🎉 تم إعداد قاعدة البيانات بنجاح!</h5>";
                echo "<p><strong>خطوات مهمة الآن:</strong></p>";
                echo "<ol>";
                echo "<li>احذف ملف <code>setup.php</code> من السيرفر فوراً</li>";
                echo "<li>غيّر كلمة مرور المدير بعد تسجيل الدخول</li>";
                echo "<li>تحقق من عمل الموقع بشكل صحيح</li>";
                echo "</ol>";
                echo "<p class='mb-0'><strong>معلومات تسجيل الدخول الافتراضية:</strong></p>";
                echo "<ul>";
                echo "<li>المستخدم: <code>admin</code></li>";
                echo "<li>كلمة المرور: <code>admin123</code></li>";
                echo "</ul>";
                echo "</div>";
                
                echo "<div class='text-center mt-4'>";
                echo "<a href='index.php' class='btn btn-success btn-lg'>الذهاب إلى الموقع</a> ";
                echo "<a href='admin.php' class='btn btn-warning btn-lg'>تسجيل دخول الإدارة</a>";
                echo "</div>";
                
                echo "</div>";
            }
            ?>
        </div>
    </div>
</body>
</html>
