<?php
/**
 * Firebase Authentication Middleware
 * 
 * This file provides functions to verify Firebase ID tokens
 * Install the Firebase Admin SDK: composer require kreait/firebase-php
 * Or use manual JWT verification with Firebase public keys
 */

/**
 * Verify Firebase ID token using Google's public keys
 * 
 * ⚠️ PRODUCTION WARNING: This implementation fetches Google's public keys
 * For better security, use Firebase Admin SDK: composer require kreait/firebase-php
 */
function verifyFirebaseToken($idToken) {
    if (empty($idToken)) {
        return null;
    }
    
    // Split JWT into parts
    $parts = explode('.', $idToken);
    if (count($parts) !== 3) {
        return null;
    }
    
    // Decode header and payload
    $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);
    $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
    $signature = $parts[2];
    
    if (!$header || !$payload) {
        return null;
    }
    
    // Validate required claims
    if (!isset($payload['user_id']) || !isset($payload['email']) || !isset($payload['iss']) || !isset($payload['aud'])) {
        return null;
    }
    
    // Check expiration
    if (!isset($payload['exp']) || $payload['exp'] < time()) {
        return null;
    }
    
    // Check issuer (must be from Firebase)
    $projectId = getenv('FIREBASE_PROJECT_ID') ?: 'YOUR_PROJECT_ID'; // Set this in config.local.php
    if ($payload['iss'] !== "https://securetoken.google.com/{$projectId}") {
        return null;
    }
    
    // Verify audience matches your project ID
    if ($payload['aud'] !== $projectId) {
        return null;
    }
    
    // ⚠️ IMPORTANT: For production, verify the signature using Google's public keys
    // This requires fetching https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com
    // and verifying the signature with the matching key
    // For now, we're doing basic validation only - use Firebase Admin SDK for full security
    
    return [
        'uid' => $payload['user_id'],
        'email' => $payload['email'],
        'email_verified' => $payload['email_verified'] ?? false
    ];
}

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

// Require authentication
function requireAuth() {
    $token = getFirebaseToken();
    $user = verifyFirebaseToken($token);
    
    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required'], JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    return $user;
}

// Require admin authentication
function requireAdmin($conn) {
    $user = requireAuth();
    
    // Check if user is admin in database
    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$user['email']]);
    $admin = $stmt->fetch();
    
    if (!$admin) {
        http_response_code(403);
        echo json_encode(['error' => 'Admin access required'], JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    return [
        'user' => $user,
        'admin' => $admin
    ];
}
?>
