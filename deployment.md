# دليل نقل المشروع إلى cPanel

## متطلبات الاستضافة

### 1. متطلبات الخادم
- **PHP**: نسخة 8.0 أو أحدث
- **PostgreSQL**: نسخة 12 أو أحدث
- **Apache/Nginx**: مع دعم mod_rewrite
- **SSL Certificate**: مطلوب لـ Firebase Auth

### 2. ملحقات PHP المطلوبة
- pdo
- pdo_pgsql
- mbstring
- json
- session

---

## خطوات النقل إلى cPanel

### الخطوة 1: تحضير الملفات

1. **تحميل جميع ملفات المشروع**
   ```bash
   # قم بتحميل جميع الملفات ما عدا:
   - .git/
   - .replit
   - .upm/
   - vendor/ (سيتم تثبيته لاحقاً)
   ```

2. **رفع الملفات إلى cPanel**
   - قم بتسجيل الدخول إلى cPanel
   - انتقل إلى **File Manager**
   - ارفع الملفات إلى `public_html` أو المجلد المخصص للموقع

---

### الخطوة 2: إعداد قاعدة بيانات PostgreSQL

1. **إنشاء قاعدة البيانات**
   - في cPanel، انتقل إلى **PostgreSQL Databases**
   - أنشئ قاعدة بيانات جديدة (مثلاً: `adhiyati_db`)
   - أنشئ مستخدم جديد
   - أعط المستخدم كامل الصلاحيات على القاعدة

2. **تحديث ملف database.php**
   ```php
   define('DB_HOST', 'localhost'); // أو عنوان الخادم
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'adhiyati_db');
   define('DB_PORT', '5432');
   ```

3. **إنشاء الجداول وإضافة البيانات**
   - افتح Terminal في cPanel (إذا كان متاحاً)
   ```bash
   cd public_html
   php config/init_db.php
   php config/seed_data.php
   ```
   
   أو استخدم **phpPgAdmin** لتنفيذ SQL مباشرة:
   ```sql
   -- انسخ محتوى جداول config/init_db.php وقم بتشغيلها
   ```

---

### الخطوة 3: إعداد Firebase Google Authentication

#### 3.1 إنشاء مشروع Firebase

1. **انتقل إلى Firebase Console**
   - افتح https://console.firebase.google.com
   - انقر على **Add Project**
   - اسم المشروع: `adhiyati` (أو أي اسم تريده)

2. **تفعيل Google Authentication**
   - في Firebase Console، اذهب إلى **Build** > **Authentication**
   - انقر على **Get Started**
   - اذهب إلى **Sign-in method**
   - فعّل **Google** كطريقة تسجيل دخول
   - أضف نطاق موقعك في **Authorized domains**

#### 3.2 الحصول على مفاتيح Firebase

1. **احصل على Web API Configuration**
   - في Firebase Console، اذهب إلى **Project Settings** (⚙️)
   - في قسم **Your apps**، انقر على **Web app** (</>)
   - سجل تطبيقك واحصل على التكوين:
   
   ```javascript
   const firebaseConfig = {
     apiKey: "AIzaSy...",
     authDomain: "your-project.firebaseapp.com",
     projectId: "your-project-id",
     storageBucket: "your-project.appspot.com",
     messagingSenderId: "123456789",
     appId: "1:123456789:web:abc123"
   };
   ```

2. **تحديث ملف admin.php**
   - افتح `admin.php`
   - ابحث عن `firebaseConfig`
   - استبدل القيم بالقيم الحقيقية من Firebase Console:
   
   ```javascript
   const firebaseConfig = {
       apiKey: "YOUR_REAL_API_KEY",
       authDomain: "your-real-project.firebaseapp.com",
       projectId: "your-real-project-id",
       storageBucket: "your-real-project.appspot.com",
       messagingSenderId: "YOUR_REAL_SENDER_ID",
       appId: "YOUR_REAL_APP_ID"
   };
   ```

#### 3.3 إضافة البريد الإلكتروني كمدير

قم بتشغيل هذا الأمر في قاعدة البيانات:

```sql
INSERT INTO admins (username, password_hash, email, role) 
VALUES (
    'bouazzasalah', 
    '$2y$10$hashedpasswordhere', 
    'bouazzasalah120120@gmail.com', 
    'primary'
);
```

أو استخدم Terminal:
```bash
php -r "require_once 'config/database.php'; \$pdo = getDBConnection(); \$stmt = \$pdo->prepare('INSERT INTO admins (username, password_hash, email, role) VALUES (?, ?, ?, ?)'); \$stmt->execute(['bouazzasalah', password_hash('YourPassword123', PASSWORD_DEFAULT), 'bouazzasalah120120@gmail.com', 'primary']); echo 'تم!';"
```

---

### الخطوة 4: إعداد SSL Certificate (مهم لـ Firebase)

Firebase Auth يتطلب HTTPS. قم بما يلي:

1. **في cPanel**
   - اذهب إلى **SSL/TLS Status**
   - فعّل **AutoSSL** لنطاقك
   - أو استخدم **Let's Encrypt**

2. **التحقق**
   - تأكد من أن موقعك يعمل على `https://yourdomain.com`

---

### الخطوة 5: إعداد .htaccess (اختياري)

قم بإنشاء ملف `.htaccess` في `public_html`:

```apache
# Redirect to HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Enable PHP error logging (للتطوير فقط)
php_flag display_errors off
php_flag log_errors on

# Prevent directory browsing
Options -Indexes

# Protect sensitive files
<FilesMatch "^(\.env|\.gitignore|config/database\.php)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

---

### الخطوة 6: اختبار المشروع

1. **اختبار الصفحة الرئيسية**
   - افتح `https://yourdomain.com`
   - يجب أن ترى الصفحة الرئيسية مع المنتجات

2. **اختبار صفحة المنتجات**
   - افتح `https://yourdomain.com/products.php`
   - تحقق من ظهور جميع المنتجات

3. **اختبار صفحة الطلبات**
   - افتح `https://yourdomain.com/orders.php`
   - تحقق من عمل السلة

4. **اختبار لوحة التحكم**
   - افتح `https://yourdomain.com/admin.php`
   - **اختبار تسجيل الدخول بـ Google**:
     1. انقر على "تسجيل الدخول بواسطة Google"
     2. سجل الدخول باستخدام `bouazzasalah120120@gmail.com`
     3. يجب أن تنجح عملية الدخول
   - **اختبار تسجيل الدخول العادي**:
     - اسم المستخدم: `admin`
     - كلمة المرور: `admin123`

---

## إعدادات Firebase المتقدمة (اختياري)

### 1. تخصيص Authorized Domains

في Firebase Console:
1. اذهب إلى **Authentication** > **Settings**
2. في قسم **Authorized domains**، أضف:
   - `yourdomain.com`
   - `www.yourdomain.com`
   - أي نطاقات فرعية أخرى

### 2. تخصيص OAuth Consent Screen

1. اذهب إلى **Google Cloud Console**
2. اختر مشروعك
3. اذهب إلى **APIs & Services** > **OAuth consent screen**
4. قم بتعبئة البيانات:
   - اسم التطبيق: أضحيتي
   - البريد الإلكتروني للدعم: bouazzasalah120120@gmail.com
   - شعار التطبيق: (اختياري)

---

## استكشاف الأخطاء

### مشكلة: "CORS Error" عند تسجيل الدخول بـ Google

**الحل:**
- تأكد من إضافة نطاقك في **Authorized domains** في Firebase
- تأكد من أن الموقع يعمل على HTTPS

### مشكلة: "Failed to connect to database"

**الحل:**
- تحقق من إعدادات قاعدة البيانات في `config/database.php`
- تأكد من أن PostgreSQL قيد التشغيل
- تحقق من صلاحيات المستخدم

### مشكلة: "عذراً، هذا الحساب غير مصرح له بالدخول"

**الحل:**
- تأكد من إضافة البريد الإلكتروني في جدول `admins`
- تحقق من البريد الإلكتروني المستخدم في تسجيل الدخول

---

## الأمان

### 1. حماية الملفات الحساسة
- لا ترفع ملف `.env` إلى Git
- احمي مجلد `config/` من الوصول المباشر

### 2. تحديث كلمات المرور
```sql
-- تغيير كلمة مرور المدير الافتراضي
UPDATE admins 
SET password_hash = '$2y$10$newhashedpassword' 
WHERE username = 'admin';
```

### 3. تفعيل HTTPS
- استخدم دائماً HTTPS في الإنتاج
- فعّل **Force HTTPS** في cPanel

---

## دعم وصيانة

### النسخ الاحتياطي
- قم بعمل نسخة احتياطية يومية من قاعدة البيانات
- استخدم **cPanel Backup** لحفظ الملفات

### التحديثات
- قم بتحديث PHP بشكل دوري
- راقب تحديثات Firebase SDK

### المراقبة
- فعّل error logging في PHP
- راقب logs في cPanel > **Error Log**

---

## جهات الاتصال

- **المطور الأساسي**: bouazzasalah120120@gmail.com
- **دعم Firebase**: https://firebase.google.com/support
- **دعم cPanel**: عبر مزود الاستضافة

---

## موارد إضافية

- [Firebase Authentication Docs](https://firebase.google.com/docs/auth)
- [PostgreSQL Documentation](https://www.postgresql.org/docs/)
- [PHP Manual](https://www.php.net/manual/en/)
- [cPanel Documentation](https://docs.cpanel.net/)

---

**تم إنشاء هذا الدليل في:** نوفمبر 2024  
**آخر تحديث:** نوفمبر 2024  
**الإصدار:** 1.0
