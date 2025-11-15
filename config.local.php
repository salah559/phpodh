<?php
// Database configuration for Replit environment
// Using SQLite for easy setup

define('DB_TYPE', 'sqlite');
define('DB_PATH', __DIR__ . '/database.sqlite');

// These are not used for SQLite but kept for compatibility
define('DB_HOST', '');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_NAME', '');
?>
