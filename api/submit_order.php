<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'طريقة الطلب غير صحيحة']);
    exit;
}

$user_name = $_POST['user_name'] ?? '';
$phone = $_POST['phone'] ?? '';
$state = $_POST['state'] ?? '';
$city = $_POST['city'] ?? '';
$notes = $_POST['notes'] ?? '';
$cart = json_decode($_POST['cart'] ?? '[]', true);
$total = floatval($_POST['total'] ?? 0);

if (empty($user_name) || empty($phone) || empty($state) || empty($city)) {
    echo json_encode(['success' => false, 'message' => 'الرجاء ملء جميع الحقول المطلوبة']);
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
