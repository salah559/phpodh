<?php
require_once 'database.php';

function initializeDatabase() {
    $pdo = getDBConnection();
    
    // تحديد SQL حسب نوع قاعدة البيانات
    if (DB_TYPE === 'mysql') {
        // MySQL Tables
        $pdo->exec("CREATE TABLE IF NOT EXISTS sheep (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            category VARCHAR(50) NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            discount DECIMAL(10, 2) DEFAULT 0,
            images TEXT,
            age INT,
            weight DECIMAL(6, 2),
            breed VARCHAR(100),
            health_status VARCHAR(100),
            description TEXT,
            featured BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_name VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            state VARCHAR(100),
            city VARCHAR(100),
            products TEXT,
            total DECIMAL(10, 2) NOT NULL,
            status VARCHAR(50) DEFAULT 'pending',
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            email VARCHAR(255),
            role VARCHAR(50) DEFAULT 'secondary',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
    } else {
        // PostgreSQL Tables
        $pdo->exec("CREATE TABLE IF NOT EXISTS sheep (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            category VARCHAR(50) NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            discount DECIMAL(10, 2) DEFAULT 0,
            images TEXT,
            age INT,
            weight DECIMAL(6, 2),
            breed VARCHAR(100),
            health_status VARCHAR(100),
            description TEXT,
            featured BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
            id SERIAL PRIMARY KEY,
            user_name VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            state VARCHAR(100),
            city VARCHAR(100),
            products TEXT,
            total DECIMAL(10, 2) NOT NULL,
            status VARCHAR(50) DEFAULT 'pending',
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
            id SERIAL PRIMARY KEY,
            username VARCHAR(100) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            email VARCHAR(255),
            role VARCHAR(50) DEFAULT 'secondary',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }
    
    // إضافة مدير افتراضي (يعمل مع كلا النوعين)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = ?");
    $stmt->execute(['admin']);
    if ($stmt->fetchColumn() == 0) {
        $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO admins (username, password_hash, email, role) 
                    VALUES ('admin', '$defaultPassword', 'admin@adhiyati.dz', 'primary')");
    }
    
    echo "تم إنشاء قاعدة البيانات بنجاح! (نوع القاعدة: " . DB_TYPE . ")";
}

if (php_sapi_name() === 'cli') {
    initializeDatabase();
}
?>
