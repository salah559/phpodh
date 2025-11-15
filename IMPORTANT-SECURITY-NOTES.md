# ⚠️ ملاحظات أمنية هامة جداً

## تحذيرات حرجة قبل النشر

### 1. التحقق من Firebase Tokens
**الحالة الحالية:** التحقق الأساسي فقط (بدون توقيع رقمي)

**⚠️ خطر أمني:** يمكن لأي شخص تزوير token والوصول كمشرف!

**الحل الموصى به:**
استخدام Firebase Admin SDK:
```bash
composer require kreait/firebase-php
```

ثم تحديث `auth.php`:
```php
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;

$factory = (new Factory)->withServiceAccount('/path/to/service-account.json');
$auth = $factory->createAuth();

function verifyFirebaseToken($idToken) {
    global $auth;
    try {
        $verifiedIdToken = $auth->verifyIdToken($idToken);
        return [
            'uid' => $verifiedIdToken->claims()->get('sub'),
            'email' => $verifiedIdToken->claims()->get('email'),
            'email_verified' => $verifiedIdToken->claims()->get('email_verified')
        ];
    } catch (\Exception $e) {
        return null;
    }
}
```

**بديل أسهل (للاستضافة المشتركة):**
تحقق من التوقيع الرقمي يدوياً باستخدام Google's public keys:
https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com

### 2. CORS
**يجب تحديث المصادر المسموحة:**

افتح `public_html/api/config.php` وقم بتحديث:
```php
$ALLOWED_ORIGINS = [
    'https://yourdomain.com',
    'https://www.yourdomain.com',
];
```

**احذف localhost قبل النشر!**

### 3. Firebase Project ID
**يجب تعريف:** `FIREBASE_PROJECT_ID` في `config.local.php`

أضف في `config.local.php`:
```php
putenv('FIREBASE_PROJECT_ID=your-project-id');
```

أو في ملف `.htaccess` في المجلد الرئيسي:
```
SetEnv FIREBASE_PROJECT_ID "your-project-id"
```

### 4. بيانات الاعتماد
**لا تنشر أبداً:**
- config.local.php مع بيانات حقيقية
- Firebase service account keys
- أي passwords أو API keys

**تأكد من:**
- ✅ config.local.php خارج public_html
- ✅ .htaccess يحمي config.local.php
- ✅ لا توجد بيانات في git repository

### 5. HTTPS
**إلزامي في الإنتاج!**

Firebase Authentication يتطلب HTTPS. فعّل SSL في cPanel أو استخدم Cloudflare.

### 6. Rate Limiting
**الحالة الحالية:** غير موجود

**خطر:** هجمات DDoS أو brute force attacks

**الحل الموصى به:**
استخدام Cloudflare أو إضافة rate limiting في PHP:
```php
// في بداية كل endpoint
if (!checkRateLimit($_SERVER['REMOTE_ADDR'])) {
    sendError('Too many requests', 429);
}
```

## قائمة التحقق النهائية

قبل النشر، تأكد من:

### الأمان
- [ ] تم تثبيت وتكوين Firebase Admin SDK (أو تحقق من التوقيع الرقمي)
- [ ] تم تحديث ALLOWED_ORIGINS في config.php
- [ ] تم تعريف FIREBASE_PROJECT_ID
- [ ] config.local.php خارج public_html مع قيم صحيحة
- [ ] لا توجد بيانات اعتماد في الملفات المنشورة
- [ ] تم تفعيل HTTPS/SSL
- [ ] تم اختبار جميع الصلاحيات (admin/user)

### الوظائف
- [ ] تسجيل الدخول يعمل
- [ ] المصادقة تعمل على API
- [ ] لا يمكن للمستخدم العادي الوصول لصفحات المشرف
- [ ] Orders تظهر فقط للمستخدم المالك أو المشرف

### الأداء
- [ ] تم تفعيل caching للملفات الثابتة
- [ ] تم ضغط الصور
- [ ] تم تصغير CSS/JS (اختياري)

## الحصول على المساعدة

إذا كنت غير متأكد من كيفية تطبيق هذه التحسينات:
1. استشر مطور PHP محترف
2. ادرس Firebase Admin SDK documentation
3. فكر في استخدام خدمة استضافة أكثر أماناً (VPS مع control كامل)

**ملاحظة:** التطبيق الحالي آمن بشكل أساسي للتطوير والاختبار، لكنه يحتاج تحسينات للإنتاج الفعلي.
