<?php
require_once 'config.php';
require_once 'auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$conn = getDbConnection();

// Get sheep ID from URL if present
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriParts = explode('/', $uri);
$sheepId = isset($uriParts[count($uriParts) - 1]) && $uriParts[count($uriParts) - 1] !== 'sheep.php' 
    ? $uriParts[count($uriParts) - 1] 
    : null;

switch ($method) {
    case 'GET':
        if ($sheepId) {
            // Get single sheep
            $stmt = $conn->prepare("SELECT * FROM sheep WHERE id = ?");
            $stmt->execute([$sheepId]);
            $sheep = $stmt->fetch();
            
            if ($sheep) {
                $sheep['images'] = json_decode($sheep['images']);
                $sheep['isFeatured'] = (bool)$sheep['isFeatured'];
                $sheep['discountPercentage'] = $sheep['discountPercentage'] ? (float)$sheep['discountPercentage'] : null;
                sendResponse($sheep);
            } else {
                sendError('Sheep not found', 404);
            }
        } else {
            // Get all sheep
            $category = isset($_GET['category']) ? $_GET['category'] : null;
            $featured = isset($_GET['featured']) ? filter_var($_GET['featured'], FILTER_VALIDATE_BOOLEAN) : null;
            
            $sql = "SELECT * FROM sheep WHERE 1=1";
            $params = [];
            
            if ($category) {
                $sql .= " AND category = ?";
                $params[] = $category;
            }
            
            if ($featured !== null) {
                $sql .= " AND isFeatured = ?";
                $params[] = $featured ? 1 : 0;
            }
            
            $sql .= " ORDER BY createdAt DESC";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $sheep = $stmt->fetchAll();
            
            foreach ($sheep as &$item) {
                $item['images'] = json_decode($item['images']);
                $item['isFeatured'] = (bool)$item['isFeatured'];
                $item['discountPercentage'] = $item['discountPercentage'] ? (float)$item['discountPercentage'] : null;
            }
            
            sendResponse($sheep);
        }
        break;
        
    case 'POST':
        // Require admin authentication for creating sheep
        requireAdmin($conn);
        
        $data = getRequestBody();
        
        if (!$data) {
            sendError('Invalid request body');
        }
        
        // Validate images array
        if (!isset($data['images']) || !is_array($data['images'])) {
            sendError('Images must be an array');
        }
        
        // Validate required fields
        $required = ['name', 'category', 'price', 'images', 'age', 'weight', 'breed', 'healthStatus', 'description'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                sendError("Missing required field: $field");
            }
        }
        
        $id = uniqid('sheep_', true);
        $stmt = $conn->prepare("
            INSERT INTO sheep (id, name, category, price, discountPercentage, images, age, weight, breed, healthStatus, description, isFeatured)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $id,
            $data['name'],
            $data['category'],
            $data['price'],
            $data['discountPercentage'] ?? null,
            json_encode($data['images'], JSON_UNESCAPED_UNICODE),
            $data['age'],
            $data['weight'],
            $data['breed'],
            $data['healthStatus'],
            $data['description'],
            isset($data['isFeatured']) ? (int)$data['isFeatured'] : 0
        ]);
        
        // Fetch the created sheep
        $stmt = $conn->prepare("SELECT * FROM sheep WHERE id = ?");
        $stmt->execute([$id]);
        $sheep = $stmt->fetch();
        $sheep['images'] = json_decode($sheep['images']);
        $sheep['isFeatured'] = (bool)$sheep['isFeatured'];
        
        sendResponse($sheep, 201);
        break;
        
    case 'PUT':
        // Require admin authentication for updating sheep
        requireAdmin($conn);
        
        if (!$sheepId) {
            sendError('Sheep ID is required');
        }
        
        $data = getRequestBody();
        
        // Validate images array
        if (isset($data['images']) && !is_array($data['images'])) {
            sendError('Images must be an array');
        }
        
        if (!$data) {
            sendError('Invalid request body');
        }
        
        $stmt = $conn->prepare("
            UPDATE sheep 
            SET name = ?, category = ?, price = ?, discountPercentage = ?, images = ?, 
                age = ?, weight = ?, breed = ?, healthStatus = ?, description = ?, isFeatured = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $data['name'],
            $data['category'],
            $data['price'],
            $data['discountPercentage'] ?? null,
            json_encode($data['images'], JSON_UNESCAPED_UNICODE),
            $data['age'],
            $data['weight'],
            $data['breed'],
            $data['healthStatus'],
            $data['description'],
            isset($data['isFeatured']) ? (int)$data['isFeatured'] : 0,
            $sheepId
        ]);
        
        // Fetch updated sheep
        $stmt = $conn->prepare("SELECT * FROM sheep WHERE id = ?");
        $stmt->execute([$sheepId]);
        $sheep = $stmt->fetch();
        
        if ($sheep) {
            $sheep['images'] = json_decode($sheep['images']);
            $sheep['isFeatured'] = (bool)$sheep['isFeatured'];
            sendResponse($sheep);
        } else {
            sendError('Sheep not found', 404);
        }
        break;
        
    case 'DELETE':
        // Require admin authentication for deleting sheep
        requireAdmin($conn);
        
        if (!$sheepId) {
            sendError('Sheep ID is required');
        }
        
        $stmt = $conn->prepare("DELETE FROM sheep WHERE id = ?");
        $stmt->execute([$sheepId]);
        
        sendResponse(['message' => 'Sheep deleted successfully']);
        break;
        
    default:
        sendError('Method not allowed', 405);
}
?>
