<?php
/**
 * Firebase Authentication Middleware - Simplified
 * 
 * هذا الملف يوفر وظائف أساسية للمصادقة
 * المصادقة الفعلية تتم من خلال Firebase في المتصفح
 */

// Get Firebase token from request
function getFirebaseToken() {
    $headers = getallheaders();

    if (isset($headers['Authorization'])) {
        $auth = $headers['Authorization'];
        if (preg_match('/Bearer\s+(.+)/', $auth, $matches)) {
            return $matches[1];
        }
    }

    return null;
}

// Basic auth check - token exists
function requireAuth() {
    $token = getFirebaseToken();

    if (!$token) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // Get email from request header (sent by client after Firebase auth)
    $headers = getallheaders();
    $email = $headers['X-User-Email'] ?? null;

    if (!$email) {
        http_response_code(401);
        echo json_encode(['error' => 'User email required'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    return ['email' => $email];
}

// Require admin authentication
function requireAdmin($conn) {
    requireAuth();

    // Get email from request header (sent by client after Firebase auth)
    $headers = getallheaders();
    $email = $headers['X-User-Email'] ?? null;

    if (!$email) {
        http_response_code(401);
        echo json_encode(['error' => 'User email required'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // Check if user is admin in database
    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if (!$admin) {
        http_response_code(403);
        echo json_encode(['error' => 'Admin access required'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    return [
        'email' => $email,
        'admin' => $admin
    ];
}
?>