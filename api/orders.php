<?php
require_once 'config.php';
require_once 'auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$conn = getDbConnection();

// Get order ID from URL if present
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriParts = explode('/', $uri);
$orderId = isset($uriParts[count($uriParts) - 1]) && $uriParts[count($uriParts) - 1] !== 'orders.php' 
    ? $uriParts[count($uriParts) - 1] 
    : null;

switch ($method) {
    case 'GET':
        // Require authentication to view orders
        $user = requireAuth();
        
        if ($orderId) {
            // Get single order
            $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
            $stmt->execute([$orderId]);
            $order = $stmt->fetch();
            
            // Check if user is admin or order owner
            $isAdmin = false;
            $adminCheck = $conn->prepare("SELECT * FROM admins WHERE email = ?");
            $adminCheck->execute([$user['email']]);
            if ($adminCheck->fetch()) {
                $isAdmin = true;
            }
            
            if (!$order) {
                sendError('Order not found', 404);
            }
            
            // Only allow viewing own orders or admin
            if (!$isAdmin && $order['userId'] !== $user['uid']) {
                sendError('Access denied', 403);
            }
            
            if ($order) {
                $order['items'] = json_decode($order['items']);
                sendResponse($order);
            } else {
                sendError('Order not found', 404);
            }
        } else {
            // Get all orders
            $status = isset($_GET['status']) ? $_GET['status'] : null;
            $userId = isset($_GET['userId']) ? $_GET['userId'] : null;
            
            $sql = "SELECT * FROM orders WHERE 1=1";
            $params = [];
            
            if ($status) {
                $sql .= " AND status = ?";
                $params[] = $status;
            }
            
            if ($userId) {
                $sql .= " AND userId = ?";
                $params[] = $userId;
            }
            
            $sql .= " ORDER BY createdAt DESC";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $orders = $stmt->fetchAll();
            
            foreach ($orders as &$order) {
                $order['items'] = json_decode($order['items']);
            }
            
            sendResponse($orders);
        }
        break;
        
    case 'POST':
        // Require authentication to create orders
        $user = requireAuth();
        
        $data = getRequestBody();
        
        // Set userId from authenticated user
        $data['userId'] = $user['uid'];
        
        if (!$data) {
            sendError('Invalid request body');
        }
        
        // Validate required fields
        $required = ['userName', 'userPhone', 'wilayaCode', 'wilayaName', 'communeId', 'communeName', 'items', 'totalAmount'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                sendError("Missing required field: $field");
            }
        }
        
        $id = uniqid('order_', true);
        $stmt = $conn->prepare("
            INSERT INTO orders (id, userId, userName, userPhone, wilayaCode, wilayaName, communeId, communeName, items, totalAmount, status, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $id,
            $data['userId'] ?? null,
            $data['userName'],
            $data['userPhone'],
            $data['wilayaCode'],
            $data['wilayaName'],
            $data['communeId'],
            $data['communeName'],
            json_encode($data['items'], JSON_UNESCAPED_UNICODE),
            $data['totalAmount'],
            $data['status'] ?? 'pending',
            $data['notes'] ?? null
        ]);
        
        // Fetch the created order
        $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch();
        $order['items'] = json_decode($order['items']);
        
        sendResponse($order, 201);
        break;
        
    case 'PUT':
        // Require admin authentication for updating orders
        requireAdmin($conn);
        
        if (!$orderId) {
            sendError('Order ID is required');
        }
        
        // Verify order exists
        $checkStmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
        $checkStmt->execute([$orderId]);
        if (!$checkStmt->fetch()) {
            sendError('Order not found', 404);
        }
        
        $data = getRequestBody();
        
        if (!$data) {
            sendError('Invalid request body');
        }
        
        // Check if only updating status
        if (isset($data['status']) && count($data) === 1) {
            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$data['status'], $orderId]);
        } else {
            $stmt = $conn->prepare("
                UPDATE orders 
                SET userName = ?, userPhone = ?, wilayaCode = ?, wilayaName = ?, 
                    communeId = ?, communeName = ?, items = ?, totalAmount = ?, status = ?, notes = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['userName'],
                $data['userPhone'],
                $data['wilayaCode'],
                $data['wilayaName'],
                $data['communeId'],
                $data['communeName'],
                json_encode($data['items'], JSON_UNESCAPED_UNICODE),
                $data['totalAmount'],
                $data['status'] ?? 'pending',
                $data['notes'] ?? null,
                $orderId
            ]);
        }
        
        // Fetch updated order
        $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        
        if ($order) {
            $order['items'] = json_decode($order['items']);
            sendResponse($order);
        } else {
            sendError('Order not found', 404);
        }
        break;
        
    case 'DELETE':
        if (!$orderId) {
            sendError('Order ID is required');
        }
        
        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        
        sendResponse(['message' => 'Order deleted successfully']);
        break;
        
    default:
        sendError('Method not allowed', 405);
}
?>
