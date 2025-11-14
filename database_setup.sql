-- ملف SQL لإنشاء قاعدة بيانات موقع أضحيتي
-- Database: ctdccyqq_odh

-- إنشاء جدول الأغنام
CREATE TABLE IF NOT EXISTS sheep (
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
);

-- إنشاء جدول الطلبات
CREATE TABLE IF NOT EXISTS orders (
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
);

-- إنشاء جدول المسؤولين
CREATE TABLE IF NOT EXISTS admins (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    role VARCHAR(50) DEFAULT 'secondary',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- إدراج البيانات التجريبية للأغنام
INSERT INTO sheep (name, category, price, discount, images, age, weight, breed, health_status, description, featured) VALUES
('خروف محلي ممتاز', 'محلي', 45000, 0, '["https://images.unsplash.com/photo-1584266129179-cb6deb8d9df9?w=400"]', 18, 45.5, 'سلالة محلية أصيلة', 'ممتازة', 'خروف محلي من سلالة أصيلة، بصحة جيدة ووزن مناسب للأضحية', true),
('خروف روماني فاخر', 'روماني', 65000, 10, '["https://images.unsplash.com/photo-1558555394-f5b6eddf2e5d?w=400"]', 24, 55.0, 'رومانوف الأصلي', 'ممتازة', 'خروف روماني أصيل، وزن ممتاز، عرض خاص بخصم 10%', true),
('خروف إسباني VIP', 'إسباني', 75000, 5, '["https://images.unsplash.com/photo-1563281577-7be64bd1045f?w=400"]', 20, 60.0, 'ميرينو إسباني', 'ممتازة', 'خروف إسباني من أفخم السلالات، مثالي للمناسبات الخاصة', true),
('خروف محلي عائلي', 'محلي', 38000, 0, '["https://images.unsplash.com/photo-1591104376927-05e29e2518df?w=400"]', 15, 40.0, 'محلي عادي', 'جيدة', 'خروف محلي مناسب للعائلات، سعر اقتصادي', false),
('خروف روماني كبير', 'روماني', 72000, 0, '["https://images.unsplash.com/photo-1583337130417-3346a1be7dee?w=400"]', 28, 62.0, 'روماني مختار', 'ممتازة', 'خروف روماني كبير الحجم، مثالي للمناسبات الكبيرة', false),
('خروف إسباني متوسط', 'إسباني', 68000, 15, '["https://images.unsplash.com/photo-1558555384-a1d99f5e9b6b?w=400"]', 22, 52.0, 'إسباني أصيل', 'جيدة جداً', 'خروف إسباني بعرض خاص، خصم 15% لفترة محدودة', false);

-- إدراج حساب المدير الافتراضي
-- اسم المستخدم: admin
-- كلمة المرور: admin123
INSERT INTO admins (username, password_hash, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@adhiyati.dz', 'primary')
ON CONFLICT (username) DO NOTHING;

-- ملاحظة: تأكد من تغيير كلمة المرور بعد أول تسجيل دخول
