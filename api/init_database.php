<?php
// Initialize SQLite database
require_once __DIR__ . '/../config.local.php';

try {
    $db = new PDO('sqlite:' . DB_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read and execute the SQL file
    $sql = file_get_contents(__DIR__ . '/setup_sqlite.sql');
    
    // Execute each statement
    $db->exec($sql);
    
    echo "Database created successfully!\n";
    
    // Verify tables were created
    $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables created: " . implode(', ', $tables) . "\n";
    
} catch (PDOException $e) {
    echo "Error creating database: " . $e->getMessage() . "\n";
    exit(1);
}
?>
