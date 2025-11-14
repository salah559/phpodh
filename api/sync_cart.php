<?php
session_start();
header('Content-Type: application/json');

// السماح فقط بطلبات POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// قراءة البيانات
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// التحقق من صحة البيانات
if (isset($data['cart']) && is_array($data['cart'])) {
    // تنظيف البيانات قبل الحفظ
    $cleaned_cart = array_map(function($item) {
        return [
            'id' => isset($item['id']) ? intval($item['id']) : 0,
            'name' => isset($item['name']) ? htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') : '',
            'price' => isset($item['price']) ? floatval($item['price']) : 0,
            'originalPrice' => isset($item['originalPrice']) ? floatval($item['originalPrice']) : 0,
            'discount' => isset($item['discount']) ? floatval($item['discount']) : 0,
            'quantity' => isset($item['quantity']) ? intval($item['quantity']) : 0
        ];
    }, $data['cart']);
    
    $_SESSION['cart'] = $cleaned_cart;
    echo json_encode(['success' => true]);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
}
?>
