<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// تحقق من تسجيل الدخول باستخدام Firebase
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['idToken']) || !isset($data['email'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'بيانات غير كاملة']);
        exit;
    }
    
    $email = $data['email'];
    $displayName = $data['displayName'] ?? '';
    $uid = $data['uid'] ?? '';
    
    try {
        $pdo = getDBConnection();
        
        // التحقق من أن البريد الإلكتروني موجود في قاعدة بيانات المسؤولين
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        if ($admin) {
            // المستخدم مسؤول مصرح له
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_role'] = $admin['role'];
            $_SESSION['firebase_uid'] = $uid;
            
            echo json_encode([
                'success' => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'admin' => [
                    'email' => $admin['email'],
                    'role' => $admin['role']
                ]
            ]);
        } else {
            // المستخدم غير مصرح له
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'عذراً، هذا الحساب غير مصرح له بالدخول'
            ]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'خطأ في الخادم: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
