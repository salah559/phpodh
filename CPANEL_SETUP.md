# دليل رفع الموقع على cPanel

## الخطوات المطلوبة:

### 1. إنشاء قاعدة بيانات PostgreSQL في cPanel

1. ادخل إلى لوحة تحكم cPanel
2. ابحث عن "PostgreSQL Databases" أو "قواعد بيانات PostgreSQL"
3. أنشئ قاعدة بيانات جديدة
4. أنشئ مستخدم جديد لقاعدة البيانات
5. اربط المستخدم بقاعدة البيانات مع منح جميع الصلاحيات
6. احفظ المعلومات التالية:
   - اسم قاعدة البيانات
   - اسم المستخدم
   - كلمة المرور
   - عنوان الخادم (عادة localhost)
   - رقم المنفذ (عادة 5432)

### 2. تعديل ملف إعدادات قاعدة البيانات

⚠️ **مهم جداً:** ملف `config/database.php` يحتوي على معلومات حساسة!

**الخطوات:**
1. افتح ملف `config/database.php` في محرر النصوص
2. ابحث عن السطور التالية في القسم `else`:

```php
define('DB_HOST', '127.0.0.1');  
define('DB_USER', 'your_database_user');  // ⚠️ غيّر هذا
define('DB_PASS', 'your_database_password');  // ⚠️ غيّر هذا
define('DB_NAME', 'your_database_name');  // ⚠️ غيّر هذا
```

3. عدّل القيم بمعلومات قاعدة البيانات من cPanel:
```php
define('DB_HOST', '127.0.0.1');  // أو localhost
define('DB_USER', 'ctdccyqq_salah');  // اسم المستخدم من cPanel
define('DB_PASS', 'password_here');  // كلمة المرور من cPanel
define('DB_NAME', 'ctdccyqq_odh');  // اسم قاعدة البيانات من cPanel
```

4. احفظ الملف

⚠️ **تحذير أمني:** لا ترفع ملف `database.php` إلى Git أو أي مستودع عام بعد إضافة معلوماتك الحقيقية!

### 3. رفع ملفات الموقع

1. ارفع جميع ملفات المشروع إلى مجلد `public_html` في cPanel
2. تأكد من رفع جميع المجلدات:
   - api/
   - auth/
   - config/
   - css/
   - includes/
   - js/
   - جميع ملفات PHP في المجلد الرئيسي

### 4. تهيئة قاعدة البيانات

بعد رفع الملفات، قم بتشغيل سكريبت التهيئة عبر المتصفح أو SSH:

**عبر SSH:**
```bash
cd public_html
php config/init_db.php
php config/seed_data.php
```

**أو أنشئ ملف مؤقت** `setup.php` في المجلد الرئيسي:
```php
<?php
require_once 'config/init_db.php';
initializeDatabase();
echo "<br>";
require_once 'config/seed_data.php';
seedSampleData();
?>
```

ثم افتح `https://yourdomain.com/setup.php` في المتصفح

⚠️ **مهم:** احذف ملف `setup.php` بعد الانتهاء من التهيئة

### 5. التحقق من عمل الموقع

1. افتح موقعك في المتصفح
2. تأكد من ظهور المنتجات بشكل صحيح
3. جرب إضافة منتج للسلة
4. تحقق من عمل جميع الصفحات

## ملاحظات مهمة:

- تأكد من أن إصدار PHP في cPanel هو 7.4 أو أحدث
- تأكد من تفعيل امتداد PDO و pdo_pgsql في PHP
- إذا كان cPanel يستخدم MySQL بدلاً من PostgreSQL، ستحتاج لتعديل الكود

## الحساب الافتراضي للإدارة:

- اسم المستخدم: `admin`
- كلمة المرور: `admin123`

⚠️ **مهم جداً:** غير كلمة المرور بعد أول تسجيل دخول!
