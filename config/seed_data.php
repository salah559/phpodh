<?php
require_once 'database.php';

function seedSampleData() {
    $pdo = getDBConnection();
    
    $sample_sheep = [
        [
            'name' => 'خروف محلي ممتاز',
            'category' => 'محلي',
            'price' => 45000,
            'discount' => 0,
            'images' => json_encode(['https://images.unsplash.com/photo-1584266129179-cb6deb8d9df9?w=400']),
            'age' => 18,
            'weight' => 45.5,
            'breed' => 'سلالة محلية أصيلة',
            'health_status' => 'ممتازة',
            'description' => 'خروف محلي من سلالة أصيلة، بصحة جيدة ووزن مناسب للأضحية',
            'featured' => true
        ],
        [
            'name' => 'خروف روماني فاخر',
            'category' => 'روماني',
            'price' => 65000,
            'discount' => 10,
            'images' => json_encode(['https://images.unsplash.com/photo-1558555394-f5b6eddf2e5d?w=400']),
            'age' => 24,
            'weight' => 55.0,
            'breed' => 'رومانوف الأصلي',
            'health_status' => 'ممتازة',
            'description' => 'خروف روماني أصيل، وزن ممتاز، عرض خاص بخصم 10%',
            'featured' => true
        ],
        [
            'name' => 'خروف إسباني VIP',
            'category' => 'إسباني',
            'price' => 75000,
            'discount' => 5,
            'images' => json_encode(['https://images.unsplash.com/photo-1563281577-7be64bd1045f?w=400']),
            'age' => 20,
            'weight' => 60.0,
            'breed' => 'ميرينو إسباني',
            'health_status' => 'ممتازة',
            'description' => 'خروف إسباني من أفخم السلالات، مثالي للمناسبات الخاصة',
            'featured' => true
        ],
        [
            'name' => 'خروف محلي عائلي',
            'category' => 'محلي',
            'price' => 38000,
            'discount' => 0,
            'images' => json_encode(['https://images.unsplash.com/photo-1591104376927-05e29e2518df?w=400']),
            'age' => 15,
            'weight' => 40.0,
            'breed' => 'محلي عادي',
            'health_status' => 'جيدة',
            'description' => 'خروف محلي مناسب للعائلات، سعر اقتصادي',
            'featured' => false
        ],
        [
            'name' => 'خروف روماني كبير',
            'category' => 'روماني',
            'price' => 72000,
            'discount' => 0,
            'images' => json_encode(['https://images.unsplash.com/photo-1583337130417-3346a1be7dee?w=400']),
            'age' => 28,
            'weight' => 62.0,
            'breed' => 'روماني مختار',
            'health_status' => 'ممتازة',
            'description' => 'خروف روماني كبير الحجم، مثالي للمناسبات الكبيرة',
            'featured' => false
        ],
        [
            'name' => 'خروف إسباني متوسط',
            'category' => 'إسباني',
            'price' => 68000,
            'discount' => 15,
            'images' => json_encode(['https://images.unsplash.com/photo-1558555384-a1d99f5e9b6b?w=400']),
            'age' => 22,
            'weight' => 52.0,
            'breed' => 'إسباني أصيل',
            'health_status' => 'جيدة جداً',
            'description' => 'خروف إسباني بعرض خاص، خصم 15% لفترة محدودة',
            'featured' => false
        ]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO sheep (name, category, price, discount, images, age, weight, breed, health_status, description, featured) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($sample_sheep as $sheep) {
        $stmt->execute([
            $sheep['name'],
            $sheep['category'],
            $sheep['price'],
            $sheep['discount'],
            $sheep['images'],
            $sheep['age'],
            $sheep['weight'],
            $sheep['breed'],
            $sheep['health_status'],
            $sheep['description'],
            $sheep['featured'] ? 't' : 'f'
        ]);
    }
    
    echo "تم إضافة البيانات التجريبية بنجاح!\n";
}

if (php_sapi_name() === 'cli') {
    seedSampleData();
}
?>
