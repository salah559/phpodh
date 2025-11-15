-- Create database tables for sheep marketplace (SQLite version)

CREATE TABLE IF NOT EXISTS sheep (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    category TEXT NOT NULL CHECK(category IN ('محلي', 'روماني', 'إسباني')),
    price REAL NOT NULL,
    discountPercentage REAL DEFAULT NULL,
    images TEXT NOT NULL,
    age TEXT NOT NULL,
    weight TEXT NOT NULL,
    breed TEXT NOT NULL,
    healthStatus TEXT NOT NULL,
    description TEXT NOT NULL,
    isFeatured INTEGER DEFAULT 0,
    createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_category ON sheep(category);
CREATE INDEX IF NOT EXISTS idx_featured ON sheep(isFeatured);
CREATE INDEX IF NOT EXISTS idx_created ON sheep(createdAt);

CREATE TABLE IF NOT EXISTS orders (
    id TEXT PRIMARY KEY,
    userId TEXT DEFAULT NULL,
    userName TEXT NOT NULL,
    userPhone TEXT NOT NULL,
    wilayaCode TEXT NOT NULL,
    wilayaName TEXT NOT NULL,
    communeId INTEGER NOT NULL,
    communeName TEXT NOT NULL,
    items TEXT NOT NULL,
    totalAmount REAL NOT NULL,
    status TEXT NOT NULL DEFAULT 'pending' CHECK(status IN ('pending', 'processing', 'completed', 'cancelled')),
    notes TEXT DEFAULT NULL,
    createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_user ON orders(userId);
CREATE INDEX IF NOT EXISTS idx_status ON orders(status);
CREATE INDEX IF NOT EXISTS idx_created_orders ON orders(createdAt);

CREATE TABLE IF NOT EXISTS admins (
    id TEXT PRIMARY KEY,
    email TEXT NOT NULL UNIQUE,
    role TEXT NOT NULL DEFAULT 'secondary' CHECK(role IN ('primary', 'secondary')),
    addedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_email ON admins(email);

CREATE TABLE IF NOT EXISTS discounts (
    id TEXT PRIMARY KEY,
    sheepId TEXT NOT NULL,
    percentage REAL NOT NULL,
    validFrom DATETIME NOT NULL,
    validTo DATETIME NOT NULL,
    isActive INTEGER DEFAULT 1,
    createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sheepId) REFERENCES sheep(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_sheep_discount ON discounts(sheepId);
CREATE INDEX IF NOT EXISTS idx_active ON discounts(isActive);
CREATE INDEX IF NOT EXISTS idx_dates ON discounts(validFrom, validTo);

-- Insert primary admin
INSERT OR IGNORE INTO admins (id, email, role, addedAt) 
VALUES ('admin_' || hex(randomblob(16)), 'bouazzasalah120120@gmail.com', 'primary', datetime('now'));
