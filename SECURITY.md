# تنبيهات أمنية مهمة

## ⚠️ تحذير: Firebase Authentication

### المشكلة الأمنية

الملف `auth/google-signin.php` الحالي **ليس آمناً للإنتاج** لأنه لا يتحقق من صحة Firebase ID Token على جانب الخادم. هذا يعني أن أي شخص يمكنه إرسال طلب POST مع أي بريد إلكتروني والحصول على صلاحيات إدارية.

### الحل (مطلوب للإنتاج)

استخدم **أحد الحلول التالية** قبل النشر على الإنتاج:

---

## الحل الأول: استخدام Firebase Admin SDK (موصى به)

### الخطوات:

1. **تثبيت Composer و Firebase Admin SDK**
   ```bash
   composer require kreait/firebase-php
   ```

2. **تحميل ملف Service Account**
   - اذهب إلى Firebase Console > Project Settings > Service Accounts
   - انقر على "Generate new private key"
   - احفظ الملف كـ `auth/firebase-credentials.json`

3. **استخدام ملف التحقق الآمن**
   - استخدم `auth/verify-token.php` بدلاً من `auth/google-signin.php`
   - في `admin.php`، غيّر:
     ```javascript
     // من:
     fetch('/auth/google-signin.php', ...)
     
     // إلى:
     fetch('/auth/verify-token.php', ...)
     ```

4. **اختبار**
   - جرب تسجيل الدخول بحساب Google
   - تحقق من أن التحقق يعمل بشكل صحيح

---

## الحل الثاني: استخدام Google Token Verification Endpoint

إذا لم تتمكن من استخدام Composer، يمكنك التحقق من ID token باستخدام Google API:

### أضف هذا الكود في `auth/google-signin.php`:

```php
// بعد استلام $idToken، أضف:
$verificationUrl = "https://oauth2.googleapis.com/tokeninfo?id_token=" . urlencode($idToken);
$ch = curl_init($verificationUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid token']);
    exit;
}

$tokenInfo = json_decode($response, true);

// تحقق من أن البريد الإلكتروني يتطابق
if (!isset($tokenInfo['email']) || $tokenInfo['email'] !== $email) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Token email mismatch']);
    exit;
}

// تحقق من أن التطبيق صحيح
if ($tokenInfo['aud'] !== 'YOUR_FIREBASE_CLIENT_ID') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid audience']);
    exit;
}
```

---

## الحل الثالث: تعطيل Firebase Auth (بسيط ولكن أقل أماناً)

إذا كنت لا تحتاج Firebase Auth، يمكنك:

1. إزالة زر "تسجيل الدخول بواسطة Google" من `admin.php`
2. الاعتماد فقط على تسجيل الدخول التقليدي (username/password)
3. حذف مجلد `auth/`

---

## تنبيهات أمنية أخرى

### 1. كلمات المرور الافتراضية

⚠️ **غيّر كلمة المرور الافتراضية فوراً بعد النشر:**

```sql
UPDATE admins 
SET password_hash = '$2y$10$your_new_hashed_password' 
WHERE username = 'admin';
```

أو استخدم PHP:
```php
$newPassword = 'YourStrongPassword123!';
$hash = password_hash($newPassword, PASSWORD_DEFAULT);
// ثم حدّث في قاعدة البيانات
```

### 2. HTTPS

🔒 **استخدم HTTPS دائماً في الإنتاج**
- Firebase Auth يتطلب HTTPS
- كلمات المرور والجلسات يجب أن تكون مشفرة

### 3. ملفات الإعدادات

📁 **لا ترفع هذه الملفات إلى Git:**
- `.env`
- `auth/firebase-credentials.json`
- أي ملف يحتوي على كلمات مرور

تأكد من أن `.gitignore` محدث (تم بالفعل ✅)

### 4. CSRF Protection

✅ **تم تطبيقه بالفعل في:**
- تسجيل الدخول التقليدي
- عمليات CRUD في لوحة التحكم

❌ **لم يتم تطبيقه في:**
- `auth/google-signin.php`
- `auth/logout.php`

لإضافة CSRF لـ Firebase Auth، استخدم نفس نظام `csrf_token` الموجود.

---

## القائمة المرجعية للأمان قبل النشر

- [ ] تحقيق أحد حلول التحقق من Firebase ID token
- [ ] تغيير كلمة مرور المدير الافتراضية
- [ ] تفعيل HTTPS على الخادم
- [ ] التأكد من عدم رفع `.env` أو `firebase-credentials.json`
- [ ] اختبار جميع نقاط الوصول (endpoints) للتأكد من الحماية
- [ ] مراجعة صلاحيات قاعدة البيانات
- [ ] تفعيل error logging وإخفاء الأخطاء عن المستخدمين

---

## المساعدة

إذا كنت بحاجة لمساعدة في تنفيذ أي من هذه الحلول:
1. راجع `deployment.md` للتفاصيل الكاملة
2. راجع التوثيق الرسمي:
   - [Firebase Admin SDK for PHP](https://firebase-php.readthedocs.io/)
   - [Google Token Verification](https://developers.google.com/identity/sign-in/web/backend-auth)

---

**آخر تحديث:** 14 نوفمبر 2024
