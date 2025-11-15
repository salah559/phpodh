<?php
// Database configuration
// ⚠️ IMPORTANT: You MUST create config.local.php before deploying
// The config.local.php file should be placed OUTSIDE public_html for security

if (file_exists(__DIR__ . '/../config.local.php')) {
    require_once __DIR__ . '/../config.local.php';
} elseif (file_exists(__DIR__ . '/../../config.local.php')) {
    require_once __DIR__ . '/../../config.local.php';
} else {
    // NO DEFAULT VALUES - config.local.php is REQUIRED
    die(json_encode([
        'error' => 'Configuration file missing. Please create config.local.php with your database credentials.'
    ]));
}

define('DB_CHARSET', 'utf8mb4');

// CORS headers for API
// Allow all origins in development (Replit environment)
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

// For Replit, allow all origins since the webview runs on dynamic domains
if (!empty($origin)) {
    header('Access-Control-Allow-Origin: ' . $origin);
} else {
    header('Access-Control-Allow-Origin: *');
}

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection function
function getDbConnection() {
    static $conn = null;
    
    if ($conn === null) {
        try {
            if (defined('DB_TYPE') && DB_TYPE === 'sqlite') {
                $dsn = "sqlite:" . DB_PATH;
                $conn = new PDO($dsn);
            } else {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                $conn = new PDO($dsn, DB_USER, DB_PASS);
            }
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch(PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
            exit();
        }
    }
    
    return $conn;
}

// Helper function to send JSON response
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

// Helper function to send error response
function sendError($message, $statusCode = 400) {
    http_response_code($statusCode);
    echo json_encode(['error' => $message], JSON_UNESCAPED_UNICODE);
    exit();
}

// Get request body
function getRequestBody() {
    $body = file_get_contents('php://input');
    return json_decode($body, true);
}
?>
