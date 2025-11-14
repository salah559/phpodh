<?php 
$page_title = 'الرئيسية';
require_once 'config/config.php';
include 'includes/header.php'; 
?>

<div class="hero-section">
    <div class="hero-content container">
        <h1>اختر أضحيتك المثالية</h1>
        <p>منصة موثوقة لشراء الأغنام والأضاحي بجودة عالية وأسعار منافسة</p>
        <div class="hero-buttons">
            <a href="#features" class="btn btn-golden">تعرف علينا</a>
            <a href="<?php echo url('products.php'); ?>" class="btn btn-outline-golden">تصفح المنتجات</a>
        </div>
    </div>
</div>

<section id="features" class="features-section">
    <div class="container">
        <h2 class="text-center mb-5" style="color: var(--golden); font-weight: 700;">لماذا أضحيتي؟</h2>
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="feature-card">
                    <div class="feature-icon">❤️</div>
                    <h4>عالي الجودة</h4>
                    <p>أغنام مختارة بعناية وفحص صحي شامل</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="feature-card">
                    <div class="feature-icon">📈</div>
                    <h4>أفضل الأسعار</h4>
                    <p>أسعار منافسة وعروض خاصة طوال العام</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="feature-card">
                    <div class="feature-icon">🛡️</div>
                    <h4>دعم 24/7</h4>
                    <p>فريق دعم متاح على مدار الساعة</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="feature-card">
                    <div class="feature-icon">⭐</div>
                    <h4>ضمان 100%</h4>
                    <p>رضا العملاء هو أولويتنا الأولى</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5" style="background: white;">
    <div class="container">
        <h2 class="text-center mb-5" style="color: var(--golden); font-weight: 700;">الأغنام المميزة</h2>
        <div class="row">
            <?php
            require_once 'config/database.php';
            $pdo = getDBConnection();
            $stmt = $pdo->query("SELECT * FROM sheep WHERE featured = true LIMIT 3");
            $featured_sheep = $stmt->fetchAll();
            
            if (count($featured_sheep) > 0):
                foreach ($featured_sheep as $sheep):
                    $images = json_decode($sheep['images'], true);
                    $image = $images ? $images[0] : 'https://via.placeholder.com/400x300?text=خروف';
            ?>
            <div class="col-md-4">
                <div class="product-card">
                    <div class="position-relative">
                        <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($sheep['name']); ?>" class="product-image">
                        <?php if ($sheep['discount'] > 0): ?>
                        <span class="product-badge">خصم <?php echo $sheep['discount']; ?>%</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-body">
                        <div class="product-category"><?php echo htmlspecialchars($sheep['category']); ?></div>
                        <h3 class="product-title"><?php echo htmlspecialchars($sheep['name']); ?></h3>
                        <div class="product-details">
                            <span>🎂 العمر: <?php echo $sheep['age']; ?> شهر</span>
                            <span>⚖️ الوزن: <?php echo $sheep['weight']; ?> كغ</span>
                        </div>
                        <div class="product-price">
                            <?php if ($sheep['discount'] > 0): ?>
                            <del><?php echo number_format($sheep['price'], 2); ?> دج</del>
                            <?php 
                            $discounted_price = $sheep['price'] - ($sheep['price'] * $sheep['discount'] / 100);
                            echo number_format($discounted_price, 2); 
                            ?> دج
                            <?php else: ?>
                            <?php echo number_format($sheep['price'], 2); ?> دج
                            <?php endif; ?>
                        </div>
                        <button class="btn-add-cart" onclick="addToCart(<?php echo $sheep['id']; ?>, '<?php echo htmlspecialchars($sheep['name'], ENT_QUOTES); ?>', <?php echo $sheep['price']; ?>, <?php echo $sheep['discount']; ?>)">
                            إضافة إلى السلة
                        </button>
                    </div>
                </div>
            </div>
            <?php 
                endforeach;
            else:
            ?>
            <div class="col-12 text-center">
                <p class="text-muted">لا توجد منتجات مميزة حالياً</p>
                <a href="<?php echo url('products.php'); ?>" class="btn btn-golden">تصفح جميع المنتجات</a>
            </div>
            <?php endif; ?>
        </div>
        <?php if (count($featured_sheep) > 0): ?>
        <div class="text-center mt-4">
            <a href="<?php echo url('products.php'); ?>" class="btn btn-golden">عرض جميع المنتجات</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
