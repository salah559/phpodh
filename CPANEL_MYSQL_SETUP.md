# دليل رفع الموقع على cPanel مع MySQL

## ✅ تم تحديث المشروع لدعم MySQL!

المشروع الآن يدعم **كلاً من PostgreSQL (Replit) و MySQL (cPanel)** تلقائياً.

---

## 📋 الخطوات المطلوبة

### 1️⃣ إنشاء قاعدة بيانات MySQL في cPanel

1. **ادخل إلى لوحة تحكم cPanel**
2. **ابحث عن "MySQL Databases"** أو "قواعد بيانات MySQL"
3. **أنشئ قاعدة بيانات جديدة:**
   - اسم القاعدة: `adhiyati_db` (أو أي اسم تريده)
4. **أنشئ مستخدم جديد:**
   - اسم المستخدم: اختر اسم قوي
   - كلمة المرور: اختر كلمة مرور قوية
5. **اربط المستخدم بقاعدة البيانات:**
   - امنح المستخدم **ALL PRIVILEGES** (جميع الصلاحيات)
6. **احفظ المعلومات التالية** (ستحتاجها لاحقاً):
   ```
   اسم قاعدة البيانات: _______________
   اسم المستخدم: _______________
   كلمة المرور: _______________
   ```

---

### 2️⃣ تعديل ملف إعدادات قاعدة البيانات

#### أ. افتح ملف `config/database.php`

#### ب. ابحث عن الأسطر التالية (حوالي السطر 20-28):

```php
define('DB_TYPE', 'mysql');
define('DB_HOST', 'localhost');
define('DB_USER', 'your_mysql_username');  // ⚠️ غيّر هذا
define('DB_PASS', 'your_mysql_password');  // ⚠️ غيّر هذا  
define('DB_NAME', 'your_mysql_database');  // ⚠️ غيّر هذا
define('DB_PORT', '3306');  // MySQL default port
```

#### ج. استبدلها بمعلومات قاعدة البيانات من cPanel:

**مثال:**
```php
define('DB_TYPE', 'mysql');
define('DB_HOST', 'localhost');
define('DB_USER', 'ctdccyqq_salah');  // اسم المستخدم من cPanel
define('DB_PASS', 'P@ssw0rd123!');  // كلمة المرور من cPanel
define('DB_NAME', 'ctdccyqq_adhiyati');  // اسم قاعدة البيانات من cPanel
define('DB_PORT', '3306');
```

⚠️ **مهم جداً:** 
- استخدم اسم المستخدم **الكامل** من cPanel (مثل `username_dbuser`)
- استخدم اسم قاعدة البيانات **الكامل** من cPanel (مثل `username_dbname`)

---

### 3️⃣ رفع ملفات المشروع إلى cPanel

#### أ. ضغط الملفات (ZIP)

على جهازك المحلي:
1. حمّل جميع ملفات المشروع من Replit
2. اضغطها في ملف ZIP واحد
3. أو ارفع الملفات مباشرة

#### ب. رفع الملفات

1. في cPanel، اذهب إلى **File Manager** (مدير الملفات)
2. انتقل إلى مجلد `public_html`
3. ارفع ملف ZIP أو ارفع الملفات مباشرة
4. إذا رفعت ZIP، قم بفك الضغط

#### ج. تأكد من رفع هذه المجلدات والملفات:

```
public_html/
├── api/
│   ├── submit_order.php
│   └── sync_cart.php
├── auth/
│   ├── firebase-config.js
│   ├── google-signin.php
│   ├── logout.php
│   └── verify-token.php
├── config/
│   ├── database.php  ← تأكد من تعديله قبل الرفع!
│   ├── init_db.php
│   └── seed_data.php
├── css/
│   └── style.css
├── includes/
│   ├── header.php
│   └── footer.php
├── js/
│   └── cart.js
├── index.php
├── products.php
├── orders.php
├── admin.php
├── favicon.ico
└── robots.txt
```

---

### 4️⃣ تهيئة قاعدة البيانات (إنشاء الجداول)

لديك **3 طرق** لتهيئة قاعدة البيانات:

#### **الطريقة 1: استخدام Terminal في cPanel** ⭐ (الأسهل)

إذا كان cPanel يوفر Terminal (SSH):

```bash
cd public_html
php config/init_db.php
php config/seed_data.php
```

#### **الطريقة 2: إنشاء ملف setup.php مؤقت** ⭐ (موصى به)

1. **أنشئ ملف جديد** اسمه `setup.php` في `public_html`
2. **انسخ هذا الكود:**

```php
<?php
// ملف مؤقت لإعداد قاعدة البيانات
// ⚠️ احذف هذا الملف بعد الانتهاء!

echo "<html dir='rtl'>";
echo "<head><meta charset='UTF-8'><title>إعداد قاعدة البيانات</title></head>";
echo "<body style='font-family: Arial; padding: 20px;'>";
echo "<h1>🔧 إعداد قاعدة البيانات</h1><hr>";

try {
    // الخطوة 1: إنشاء الجداول
    echo "<h2>الخطوة 1: إنشاء الجداول...</h2>";
    require_once 'config/init_db.php';
    initializeDatabase();
    echo "<p style='color:green;'>✅ تم إنشاء الجداول بنجاح!</p>";
    
    // الخطوة 2: إضافة البيانات التجريبية
    echo "<h2>الخطوة 2: إضافة البيانات التجريبية...</h2>";
    require_once 'config/seed_data.php';
    seedSampleData();
    echo "<p style='color:green;'>✅ تم إضافة البيانات بنجاح!</p>";
    
    echo "<hr>";
    echo "<h2 style='color:green;'>✅ تم الإعداد بنجاح!</h2>";
    echo "<p><a href='index.php' style='color:blue;'>انتقل إلى الصفحة الرئيسية</a></p>";
    echo "<p><a href='admin.php' style='color:blue;'>لوحة التحكم (admin / admin123)</a></p>";
    echo "<p style='color:red;'><strong>⚠️ مهم: احذف ملف setup.php الآن!</strong></p>";
    
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ حدث خطأ: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>
```

3. **افتح المتصفح** واذهب إلى:
   ```
   https://yourdomain.com/setup.php
   ```

4. **⚠️ بعد انتهاء الإعداد، احذف ملف `setup.php` فوراً!**

#### **الطريقة 3: استخدام phpMyAdmin** (يدوياً)

1. في cPanel، افتح **phpMyAdmin**
2. اختر قاعدة البيانات التي أنشأتها
3. اذهب إلى تبويب **SQL**
4. انسخ والصق الكود التالي:

<details>
<summary>📄 انقر لعرض SQL Code</summary>

```sql
-- إنشاء جدول الأغنام
CREATE TABLE IF NOT EXISTS sheep (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    discount DECIMAL(10, 2) DEFAULT 0,
    images TEXT,
    age INT,
    weight DECIMAL(6, 2),
    breed VARCHAR(100),
    health_status VARCHAR(100),
    description TEXT,
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إنشاء جدول الطلبات
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    state VARCHAR(100),
    city VARCHAR(100),
    products TEXT,
    total DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إنشاء جدول المسؤولين
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    role VARCHAR(50) DEFAULT 'secondary',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إضافة مستخدم المدير الافتراضي
INSERT INTO admins (username, password_hash, email, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@adhiyati.dz', 'primary');
```

</details>

5. انقر **تنفيذ** (Execute/Go)

---

### 5️⃣ التحقق من عمل الموقع

1. **افتح موقعك:**
   ```
   https://yourdomain.com
   ```

2. **تحقق من الصفحة الرئيسية:**
   - يجب أن ترى الموقع بشكل صحيح
   - إذا رأيت المنتجات → ممتاز! ✅
   - إذا لم ترَ منتجات → أضف بيانات تجريبية (الخطوة 6)

3. **اختبار لوحة التحكم:**
   ```
   https://yourdomain.com/admin.php
   ```
   - اسم المستخدم: `admin`
   - كلمة المرور: `admin123`

---

### 6️⃣ إضافة بيانات تجريبية (اختياري)

إذا لم تظهر المنتجات، افتح:
```
https://yourdomain.com/config/seed_data.php
```

أو نفّذ هذا عبر Terminal:
```bash
php config/seed_data.php
```

---

## 🔒 الأمان (مهم جداً!)

### 1. حماية ملف database.php

أنشئ ملف `.htaccess` في `public_html`:

```apache
# منع الوصول لملفات الإعدادات
<FilesMatch "^(database\.php|\.env)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# تفعيل HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# منع عرض قوائم المجلدات
Options -Indexes
```

### 2. تغيير كلمة مرور المدير

**بعد أول تسجيل دخول:**
1. اذهب إلى phpMyAdmin
2. افتح جدول `admins`
3. عدّل حقل `password_hash` للمستخدم `admin`
4. استخدم أداة online لتوليد password hash:
   ```
   password_hash('your_new_password', PASSWORD_DEFAULT)
   ```

أو عبر PHP:
```php
<?php echo password_hash('كلمة_المرور_الجديدة', PASSWORD_DEFAULT); ?>
```

### 3. تفعيل SSL (HTTPS)

في cPanel:
1. اذهب إلى **SSL/TLS Status**
2. فعّل **AutoSSL** لنطاقك
3. تأكد من عمل الموقع على `https://`

---

## 🐛 استكشاف الأخطاء

### مشكلة: Internal Server Error 500

**السبب المحتمل:** خطأ في ملف `database.php`

**الحل:**
1. تحقق من معلومات قاعدة البيانات
2. تأكد من استخدام اسم المستخدم الكامل من cPanel
3. راجع Error Log في cPanel

### مشكلة: "خطأ في الاتصال بقاعدة البيانات"

**الحلول:**
1. **تحقق من DB_HOST:**
   - جرب `localhost`
   - جرب `127.0.0.1`
   - بعض الاستضافات تستخدم IP خاص

2. **تحقق من الصلاحيات:**
   - تأكد من أن المستخدم له صلاحيات على القاعدة

3. **تحقق من المنفذ:**
   - غالباً `3306`
   - بعض الاستضافات تستخدم منفذ مختلف

### مشكلة: لا تظهر الأحرف العربية بشكل صحيح

**الحل:**
في phpMyAdmin، نفّذ:
```sql
ALTER DATABASE your_database_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### مشكلة: "امتداد pdo_mysql غير مُفعّل"

**الحل:**
1. في cPanel → **Select PHP Version**
2. تأكد من تفعيل: `pdo` و `pdo_mysql`

---

## 📞 الدعم

**واجهت مشكلة؟**

1. **تحقق من Error Log:**
   - cPanel → **Errors** → راجع آخر 10 أسطر

2. **استخدم ملف check_server.php:**
   - ارفع ملف `check_server.php` إلى موقعك
   - افتحه في المتصفح لمعرفة المشكلة
   - **احذفه بعد الفحص!**

3. **راسلني:**
   - أرسل لي Error Log
   - وصف المشكلة بالتفصيل

---

## ✅ قائمة التحقق النهائية

- [ ] أنشأت قاعدة بيانات MySQL في cPanel
- [ ] عدّلت ملف `config/database.php` بمعلومات القاعدة
- [ ] رفعت جميع الملفات إلى `public_html`
- [ ] أنشأت الجداول (init_db.php)
- [ ] أضفت البيانات التجريبية (seed_data.php)
- [ ] الموقع يعمل بشكل صحيح
- [ ] لوحة التحكم تعمل
- [ ] غيّرت كلمة مرور المدير الافتراضية
- [ ] حذفت ملف `setup.php` (إن وُجد)
- [ ] فعّلت SSL (HTTPS)
- [ ] أنشأت ملف `.htaccess` للحماية

---

**🎉 مبروك! موقعك الآن يعمل على cPanel!**

**تاريخ التحديث:** نوفمبر 2024  
**الإصدار:** 2.0 (دعم MySQL)
