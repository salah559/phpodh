<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'طريقة الطلب غير صحيحة']);
    exit;
}

// تنظيف وتحقق من البيانات
$user_name = htmlspecialchars(trim($_POST['user_name'] ?? ''), ENT_QUOTES, 'UTF-8');
$phone = htmlspecialchars(trim($_POST['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
$state = htmlspecialchars(trim($_POST['state'] ?? ''), ENT_QUOTES, 'UTF-8');
$city = htmlspecialchars(trim($_POST['city'] ?? ''), ENT_QUOTES, 'UTF-8');
$notes = htmlspecialchars(trim($_POST['notes'] ?? ''), ENT_QUOTES, 'UTF-8');
$cart = json_decode($_POST['cart'] ?? '[]', true);
$total = floatval($_POST['total'] ?? 0);

// التحقق من الحقول المطلوبة
if (empty($user_name) || empty($phone) || empty($state) || empty($city)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'الرجاء ملء جميع الحقول المطلوبة']);
    exit;
}

// التحقق من تنسيق رقم الهاتف (أرقام فقط و + و مسافات)
if (!preg_match('/^[\d\s\+\-]+$/', $phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'رقم الهاتف غير صالح']);
    exit;
}

if (empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'السلة فارغة']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("INSERT INTO orders (user_name, phone, state, city, products, total, status, notes) 
                           VALUES (?, ?, ?, ?, ?, ?, 'pending', ?)");
    
    $stmt->execute([
        $user_name,
        $phone,
        $state,
        $city,
        json_encode($cart),
        $total,
        $notes
    ]);
    
    echo json_encode(['success' => true, 'message' => 'تم إرسال الطلب بنجاح']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'حدث خطأ في حفظ الطلب']);
}
?>
