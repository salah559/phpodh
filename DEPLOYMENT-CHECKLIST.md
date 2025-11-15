# ✅ قائمة التحقق قبل النشر على cPanel

## 1. إعداد قاعدة البيانات ✓

- [ ] إنشاء قاعدة بيانات MySQL في cPanel
- [ ] إنشاء مستخدم قاعدة البيانات ومنحه جميع الصلاحيات
- [ ] استيراد ملف `api/setup.sql` عبر phpMyAdmin
- [ ] التحقق من إنشاء جميع الجداول (sheep, orders, admins, discounts)

## 2. إعداد ملف التكوين ✓

**⚠️ CRITICAL:** يجب إنشاء ملف `config.local.php` قبل الرفع!

- [ ] نسخ `config.local.example.php` إلى `config.local.php`
- [ ] تحديث معلومات قاعدة البيانات في `config.local.php`:
  ```php
  define('DB_HOST', 'your_host');
  define('DB_USER', 'your_user');
  define('DB_PASS', 'your_password');
  define('DB_NAME', 'your_database');
  ```
- [ ] وضع ملف `config.local.php` في المجلد الرئيسي (فوق `public_html`)

## 3. إعداد Firebase ✓

- [ ] إنشاء مشروع في [Firebase Console](https://console.firebase.google.com/)
- [ ] تفعيل Authentication → Email/Password
- [ ] تفعيل Authentication → Google Sign-in
- [ ] نسخ معلومات التكوين من Project Settings
- [ ] تحديث ملف `public_html/assets/js/firebase-config.js` بمفاتيح Firebase الخاصة بك

## 4. تحديث CORS ✓

- [ ] فتح ملف `public_html/api/config.php`
- [ ] تحديث `$allowed_origin` بنطاقك الفعلي:
  ```php
  $allowed_origin = 'https://yourdomain.com';
  ```

## 5. إضافة أول مشرف ✓

- [ ] تشغيل هذا الاستعلام SQL في phpMyAdmin:
  ```sql
  INSERT INTO admins (id, email, role, addedAt) 
  VALUES (UUID(), 'your-email@example.com', 'primary', NOW());
  ```

## 6. التحقق من الأمان ✓

- [ ] التأكد من عدم وجود بيانات اعتماد حقيقية في ملفات public_html
- [ ] التحقق من وجود ملف `.htaccess` في `public_html/api`
- [ ] التأكد من أن `config.local.php` غير موجود داخل `public_html`

## 7. رفع الملفات ✓

- [ ] ضغط محتويات `public_html` في ملف zip
- [ ] رفع الملف المضغوط إلى cPanel File Manager
- [ ] فك الضغط داخل مجلد `public_html`
- [ ] رفع `config.local.php` إلى المجلد الرئيسي (فوق public_html)

## 8. اختبار الموقع ✓

### الاختبارات الأساسية:
- [ ] فتح الصفحة الرئيسية - يجب أن تظهر بدون أخطاء
- [ ] تحميل صفحة المنتجات - يجب أن تظهر المنتجات (إذا كانت موجودة)
- [ ] تسجيل الدخول - يجب أن يعمل بدون مشاكل
- [ ] إضافة منتج للسلة - يجب أن يعمل localStorage

### اختبارات المشرف:
- [ ] تسجيل الدخول بحساب المشرف
- [ ] الوصول إلى لوحة التحكم (`/pages/admin.html`)
- [ ] محاولة إضافة منتج جديد
- [ ] محاولة تعديل حالة طلب

### اختبارات الأمان:
- [ ] محاولة الوصول إلى `/api/config.php` مباشرة - يجب أن يُرفض
- [ ] محاولة إضافة منتج بدون تسجيل دخول - يجب أن يُرفض (401)
- [ ] محاولة الوصول إلى لوحة التحكم بحساب عادي - يجب أن يُرفض (403)

## 9. التحسينات الاختيارية

- [ ] تفعيل SSL/HTTPS في cPanel
- [ ] إضافة Cloudflare للأمان والسرعة
- [ ] تحسين أداء الصور (ضغط، WebP)
- [ ] إضافة Google Analytics أو أداة تحليلات أخرى

## 10. النسخ الاحتياطي

- [ ] إنشاء نسخة احتياطية من قاعدة البيانات
- [ ] إنشاء نسخة احتياطية من الملفات
- [ ] جدولة نسخ احتياطية تلقائية في cPanel

## مشاكل شائعة وحلولها

### خطأ "Configuration file missing"
**الحل:** تأكد من إنشاء `config.local.php` في المكان الصحيح

### خطأ "Access denied" عند الاتصال بقاعدة البيانات
**الحل:** تحقق من معلومات الاتصال وصلاحيات المستخدم

### خطأ Firebase "invalid-api-key"
**الحل:** تحقق من مفاتيح Firebase في `firebase-config.js`

### CORS errors في المتصفح
**الحل:** تحقق من إعدادات CORS في `api/config.php`

### لا يمكن الوصول إلى API
**الحل:** تحقق من وجود ملف `.htaccess` في مجلد `api`

---

**ملاحظة مهمة:** لا تنشر المشروع بدون إكمال جميع النقاط في هذه القائمة!

**للدعم:** راجع ملف `README.md` للحصول على تفاصيل إضافية.
