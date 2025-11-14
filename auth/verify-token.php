<?php
/**
 * Firebase ID Token Verification (Production-Ready Version)
 * 
 * This file provides a secure implementation of Firebase ID token verification.
 * Use this instead of the basic auth/google-signin.php for production.
 * 
 * Requirements:
 * - PHP 7.4 or higher
 * - Composer
 * - kreait/firebase-php package
 * 
 * Installation:
 * 1. Run: composer require kreait/firebase-php
 * 2. Download Firebase service account JSON from Firebase Console
 * 3. Save it as auth/firebase-credentials.json (already in .gitignore)
 * 4. Update this file to use your credentials path
 */

session_start();
require_once __DIR__ . '/../config/database.php';

// Check if Firebase Admin SDK is installed
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Firebase Admin SDK not installed. Run: composer require kreait/firebase-php'
    ]);
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth\SignIn\FailedToSignIn;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['idToken'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID token is required']);
        exit;
    }
    
    try {
        // Initialize Firebase Admin SDK
        $credentialsPath = __DIR__ . '/firebase-credentials.json';
        
        if (!file_exists($credentialsPath)) {
            throw new Exception('Firebase credentials file not found. Download it from Firebase Console > Project Settings > Service Accounts');
        }
        
        $factory = (new Factory)->withServiceAccount($credentialsPath);
        $auth = $factory->createAuth();
        
        // Verify the ID token
        $verifiedIdToken = $auth->verifyIdToken($data['idToken']);
        
        // Extract user information from the verified token
        $uid = $verifiedIdToken->claims()->get('sub');
        $email = $verifiedIdToken->claims()->get('email');
        $emailVerified = $verifiedIdToken->claims()->get('email_verified');
        
        if (!$emailVerified) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'البريد الإلكتروني غير مؤكد'
            ]);
            exit;
        }
        
        // Check if user is authorized admin
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        if ($admin) {
            // User is authorized - create session
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
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'عذراً، هذا الحساب غير مصرح له بالدخول'
            ]);
        }
        
    } catch (Exception $e) {
        error_log('Firebase Auth Error: ' . $e->getMessage());
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'فشل التحقق من الهوية: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
