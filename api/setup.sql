-- Create database tables for sheep marketplace

CREATE TABLE IF NOT EXISTS sheep (
    id VARCHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category ENUM('محلي', 'روماني', 'إسباني') NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    discountPercentage DECIMAL(5, 2) DEFAULT NULL,
    images JSON NOT NULL,
    age VARCHAR(100) NOT NULL,
    weight VARCHAR(100) NOT NULL,
    breed VARCHAR(255) NOT NULL,
    healthStatus TEXT NOT NULL,
    description TEXT NOT NULL,
    isFeatured BOOLEAN DEFAULT false,
    createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_featured (isFeatured),
    INDEX idx_created (createdAt)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS orders (
    id VARCHAR(36) PRIMARY KEY,
    userId VARCHAR(255) DEFAULT NULL,
    userName VARCHAR(255) NOT NULL,
    userPhone VARCHAR(50) NOT NULL,
    wilayaCode VARCHAR(10) NOT NULL,
    wilayaName VARCHAR(255) NOT NULL,
    communeId INT NOT NULL,
    communeName VARCHAR(255) NOT NULL,
    items JSON NOT NULL,
    totalAmount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    notes TEXT DEFAULT NULL,
    createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user (userId),
    INDEX idx_status (status),
    INDEX idx_created (createdAt)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admins (
    id VARCHAR(36) PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    role ENUM('primary', 'secondary') NOT NULL DEFAULT 'secondary',
    addedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS discounts (
    id VARCHAR(36) PRIMARY KEY,
    sheepId VARCHAR(36) NOT NULL,
    percentage DECIMAL(5, 2) NOT NULL,
    validFrom DATETIME NOT NULL,
    validTo DATETIME NOT NULL,
    isActive BOOLEAN DEFAULT true,
    createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sheepId) REFERENCES sheep(id) ON DELETE CASCADE,
    INDEX idx_sheep (sheepId),
    INDEX idx_active (isActive),
    INDEX idx_dates (validFrom, validTo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert primary admin (update email as needed)
INSERT INTO admins (id, email, role, addedAt) 
VALUES (UUID(), 'bouazzasalah120120@gmail.com', 'primary', NOW())
ON DUPLICATE KEY UPDATE email = email;
