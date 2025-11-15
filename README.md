# سوق الأغنام - Sheep Marketplace

## نظرة عامة
منصة إلكترونية لبيع وشراء الأغنام مبنية بـ HTML/CSS/JavaScript + PHP + MySQL + Firebase Auth

## ⚠️ تحذير مهم
**اقرأ ملف `⚠️-DEPLOY-BLOCKED.md` قبل النشر للإنتاج!**

المشروع يحتاج تكوين Firebase Admin SDK للأمان الكامل.

## المتطلبات
- استضافة cPanel مع PHP 7.4 أو أحدث + **دعم Composer** (للأمان)
- MySQL 5.7 أو أحدث
- حساب Firebase (للمصادقة)
- HTTPS/SSL (إلزامي)

## تعليمات التثبيت على cPanel

### 1. إعداد قاعدة البيانات

1. **إنشاء قاعدة البيانات:**
   - افتح cPanel → MySQL Databases
   - أنشئ قاعدة بيانات جديدة
   - أنشئ مستخدم جديد وامنحه جميع الصلاحيات على قاعدة البيانات

2. **استيراد الجداول:**
   - افتح phpMyAdmin
   - اختر قاعدة البيانات الخاصة بك
   - اذهب إلى "Import"
   - قم برفع ملف `api/setup.sql`
   - اضغط "Go"

### 2. إعداد الملفات

1. **رفع الملفات:**
   - قم بضغط محتويات مجلد `public_html`
   - ارفع الملف المضغوط إلى cPanel → File Manager
   - فك الضغط داخل مجلد `public_html`

2. **تكوين قاعدة البيانات:**
   
   **الطريقة الأولى (الموصى بها):**
   - انسخ ملف `config.local.example.php` إلى `config.local.php`
   - ضع الملف في المجلد الرئيسي (فوق public_html)
   - قم بتحديث قيم قاعدة البيانات في `config.local.php`
   
   **الطريقة الثانية:**
   - افتح ملف `public_html/api/config.php`
   - قم بتحديث القيم الافتراضية بمعلومات قاعدة البيانات الخاصة بك

### 3. إعداد Firebase

1. **إنشاء مشروع Firebase:**
   - اذهب إلى [Firebase Console](https://console.firebase.google.com/)
   - أنشئ مشروع جديد
   - فعّل Authentication → Email/Password و Google Sign-in

2. **الحصول على مفاتيح Firebase:**
   - في Firebase Console → Project Settings
   - انسخ معلومات التكوين من قسم "Your apps"

3. **تحديث التكوين:**
   - افتح `public_html/assets/js/firebase-config.js`
   - قم بتحديث قيم `firebaseConfig` بمفاتيحك

### 4. الأمان

1. **حماية ملفات التكوين:**
   - تأكد من وجود ملف `.htaccess` في مجلد `api`
   - ضع ملف `config.local.php` خارج مجلد `public_html`

2. **تحديث CORS (اختياري):**
   - افتح `api/config.php`
   - قم بتحديث `Access-Control-Allow-Origin` بنطاقك الفعلي

### 5. إضافة أول مشرف

قم بتشغيل هذا الاستعلام SQL في phpMyAdmin:

```sql
INSERT INTO admins (id, email, role, addedAt) 
VALUES (UUID(), 'your-email@example.com', 'primary', NOW());
```

استبدل `your-email@example.com` ببريدك الإلكتروني الذي ستستخدمه للدخول.

## هيكل المشروع

```
public_html/
├── index.html              # الصفحة الرئيسية
├── pages/                  # صفحات الموقع
│   ├── products.html       # صفحة المنتجات
│   ├── cart.html           # سلة التسوق
│   ├── login.html          # تسجيل الدخول
│   └── admin.html          # لوحة التحكم
├── assets/
│   ├── css/
│   │   └── main.css        # التنسيقات
│   └── js/
│       ├── api.js          # وظائف API
│       └── firebase-config.js  # إعداد Firebase
└── api/                    # واجهة برمجة التطبيقات PHP
    ├── config.php          # إعداد قاعدة البيانات
    ├── sheep.php           # API الأغنام
    ├── orders.php          # API الطلبات
    ├── admins.php          # API المشرفين
    └── setup.sql           # جداول قاعدة البيانات
```

## الميزات

✅ عرض المنتجات مع الفلترة حسب الفئة
✅ سلة تسوق باستخدام localStorage
✅ نظام طلبات كامل
✅ مصادقة المستخدمين عبر Firebase (Email/Password + Google)
✅ لوحة تحكم إدارية
✅ إدارة المنتجات (إضافة، تعديل، حذف)
✅ إدارة الطلبات وتحديث الحالة
✅ تصميم متجاوب يعمل على جميع الأجهزة
✅ دعم كامل للغة العربية (RTL)

## الاستخدام

### للعملاء:
1. تصفح المنتجات
2. إضافة منتجات للسلة
3. تسجيل الدخول أو إنشاء حساب
4. إتمام الطلب

### للمشرفين:
1. تسجيل الدخول بحساب مشرف
2. الوصول إلى لوحة التحكم (`/pages/admin.html`)
3. إدارة المنتجات والطلبات

## API Endpoints

### Sheep (المنتجات)
- `GET /api/sheep.php` - جلب جميع المنتجات
- `GET /api/sheep.php/{id}` - جلب منتج واحد
- `POST /api/sheep.php` - إضافة منتج جديد
- `PUT /api/sheep.php/{id}` - تعديل منتج
- `DELETE /api/sheep.php/{id}` - حذف منتج

### Orders (الطلبات)
- `GET /api/orders.php` - جلب جميع الطلبات
- `GET /api/orders.php/{id}` - جلب طلب واحد
- `POST /api/orders.php` - إنشاء طلب جديد
- `PUT /api/orders.php/{id}` - تحديث طلب

### Admins (المشرفين)
- `GET /api/admins.php` - جلب جميع المشرفين
- `GET /api/admins.php?email=xxx` - التحقق من صلاحيات المشرف
- `POST /api/admins.php` - إضافة مشرف
- `DELETE /api/admins.php` - إزالة مشرف

## استكشاف الأخطاء

### خطأ في الاتصال بقاعدة البيانات
- تحقق من معلومات الاتصال في `config.php`
- تأكد من أن المستخدم لديه صلاحيات على قاعدة البيانات

### خطأ Firebase
- تحقق من مفاتيح Firebase في `firebase-config.js`
- تأكد من تفعيل Email/Password و Google Sign-in في Firebase Console

### خطأ CORS
- تحقق من إعدادات `.htaccess` في مجلد `api`
- قد تحتاج لتحديث `Access-Control-Allow-Origin`

## الدعم والمساعدة

للحصول على المساعدة أو الإبلاغ عن مشاكل، يرجى التواصل مع المطور.

## الترخيص

© 2024 جميع الحقوق محفوظة
