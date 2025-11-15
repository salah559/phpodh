<?php
require_once 'config.local.php';

// قم بتغيير البريد الإلكتروني هنا
$email = 'your-email@example.com';  // ضع بريدك الإلكتروني هنا

try {
    $db = new PDO("sqlite:database.sqlite");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // التحقق من وجود المسؤول
    $stmt = $db->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        echo "✅ البريد الإلكتروني '$email' موجود مسبقاً كمسؤول!\n";
    } else {
        // إضافة المسؤول الجديد
        $id = 'admin_' . bin2hex(random_bytes(16));
        $stmt = $db->prepare("INSERT INTO admins (id, email, role, addedAt) VALUES (?, ?, ?, datetime('now'))");
        $stmt->execute([$id, $email, 'primary']);
        
        echo "✅ تم إضافة '$email' كمسؤول بنجاح!\n";
        echo "المعرف: $id\n";
    }
    
    // عرض جميع المسؤولين
    echo "\n📋 قائمة المسؤولين:\n";
    echo str_repeat("-", 50) . "\n";
    $stmt = $db->query("SELECT email, role, addedAt FROM admins ORDER BY addedAt");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "📧 " . $row['email'] . " (" . $row['role'] . ") - " . $row['addedAt'] . "\n";
    }
    
} catch (PDOException $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
}
