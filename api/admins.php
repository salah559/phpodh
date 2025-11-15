<?php
require_once 'config.php';
require_once 'auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$conn = getDbConnection();

switch ($method) {
    case 'GET':
        // Require authentication
        $user = requireAuth();
        
        $email = isset($_GET['email']) ? $_GET['email'] : null;
        
        if ($email) {
            // Only allow checking own email
            if ($email !== $user['email']) {
                sendError('Access denied', 403);
            }
            
            // Check if user is admin
            $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();
            
            sendResponse([
                'isAdmin' => $admin !== false,
                'admin' => $admin ?: null
            ]);
        } else {
            // Get all admins - require admin access
            requireAdmin($conn);
            
            $stmt = $conn->query("SELECT * FROM admins ORDER BY addedAt DESC");
            $admins = $stmt->fetchAll();
            sendResponse($admins);
        }
        break;
        
    case 'POST':
        // Require admin authentication with primary role
        $authData = requireAdmin($conn);
        
        if ($authData['admin']['role'] !== 'primary') {
            sendError('Only primary admins can add new admins', 403);
        }
        
        $data = getRequestBody();
        
        if (!isset($data['email'])) {
            sendError('Email is required');
        }
        
        // Check if admin already exists
        $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$data['email']]);
        
        if ($stmt->fetch()) {
            sendError('Admin already exists', 409);
        }
        
        $id = uniqid('admin_', true);
        $stmt = $conn->prepare("INSERT INTO admins (id, email, role) VALUES (?, ?, ?)");
        $stmt->execute([
            $id,
            $data['email'],
            $data['role'] ?? 'secondary'
        ]);
        
        // Fetch the created admin
        $stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->execute([$id]);
        $admin = $stmt->fetch();
        
        sendResponse($admin, 201);
        break;
        
    case 'DELETE':
        // Require admin authentication with primary role
        $authData = requireAdmin($conn);
        
        if ($authData['admin']['role'] !== 'primary') {
            sendError('Only primary admins can remove admins', 403);
        }
        
        $data = getRequestBody();
        
        if (!isset($data['email'])) {
            sendError('Email is required');
        }
        
        // Don't allow deleting primary admin
        $stmt = $conn->prepare("SELECT role FROM admins WHERE email = ?");
        $stmt->execute([$data['email']]);
        $admin = $stmt->fetch();
        
        if ($admin && $admin['role'] === 'primary') {
            sendError('Cannot delete primary admin', 403);
        }
        
        $stmt = $conn->prepare("DELETE FROM admins WHERE email = ?");
        $stmt->execute([$data['email']]);
        
        sendResponse(['message' => 'Admin removed successfully']);
        break;
        
    default:
        sendError('Method not allowed', 405);
}
?>
