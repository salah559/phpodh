<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// IMPORTANT SECURITY NOTE:
// This is a BASIC implementation for demonstration purposes.
// For production use, you MUST verify the Firebase ID token on the server side
// using Firebase Admin SDK or Google's token verification endpoint.
// Without proper verification, this endpoint can be exploited.
// 
// To implement proper verification:
// 1. Install kreait/firebase-php via Composer
// 2. Verify the ID token signature before trusting the email
// 3. See deployment.md for detailed instructions

// تحقق من تسجيل الدخول باستخدام Firebase
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['idToken']) || !isset($data['email'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'بيانات غير كاملة']);
        exit;
    }
    
    $idToken = $data['idToken'];
    $email = $data['email'];
    $displayName = $data['displayName'] ?? '';
    $uid = $data['uid'] ?? '';
    
    // TODO: Add proper ID token verification here
    // Example using Google's tokeninfo endpoint:
    // $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $idToken;
    // $response = file_get_contents($url);
    // $tokenInfo = json_decode($response, true);
    // if (!isset($tokenInfo['email']) || $tokenInfo['email'] !== $email) {
    //     http_response_code(401);
    //     echo json_encode(['success' => false, 'message' => 'Invalid token']);
    //     exit;
    // }
    
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
