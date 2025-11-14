<?php 
$page_title = 'المنتجات';
require_once 'config/config.php';
include 'includes/header.php';
require_once 'config/database.php';

$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$pdo = getDBConnection();

if ($category === 'all') {
    $stmt = $pdo->query("SELECT * FROM sheep ORDER BY created_at DESC");
} else {
    $stmt = $pdo->prepare("SELECT * FROM sheep WHERE category = ? ORDER BY created_at DESC");
    $stmt->execute([$category]);
}
$products = $stmt->fetchAll();
?>

<div class="container my-5">
    <h1 class="text-center mb-4" style="color: var(--golden); font-weight: 700;">تصفح الأغنام والأضاحي</h1>
    
    <div class="filter-section text-center">
        <button class="filter-btn <?php echo $category === 'all' ? 'active' : ''; ?>" 
                onclick="filterProducts('all')">
            الكل
        </button>
        <button class="filter-btn <?php echo $category === 'محلي' ? 'active' : ''; ?>" 
                onclick="filterProducts('محلي')">
            محلي
        </button>
        <button class="filter-btn <?php echo $category === 'روماني' ? 'active' : ''; ?>" 
                onclick="filterProducts('روماني')">
            روماني
        </button>
        <button class="filter-btn <?php echo $category === 'إسباني' ? 'active' : ''; ?>" 
                onclick="filterProducts('إسباني')">
            إسباني
        </button>
    </div>

    <div class="row">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $sheep): 
                $images = json_decode($sheep['images'], true);
                $image = $images ? $images[0] : 'https://via.placeholder.com/400x300?text=خروف';
            ?>
            <div class="col-md-4 col-lg-3">
                <div class="product-card">
                    <div class="position-relative">
                        <img src="<?php echo htmlspecialchars($image); ?>" 
                             alt="<?php echo htmlspecialchars($sheep['name']); ?>" 
                             class="product-image">
                        <?php if ($sheep['discount'] > 0): ?>
                        <span class="product-badge">خصم <?php echo $sheep['discount']; ?>%</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-body">
                        <div class="product-category"><?php echo htmlspecialchars($sheep['category']); ?></div>
                        <h3 class="product-title"><?php echo htmlspecialchars($sheep['name']); ?></h3>
                        <p class="text-muted small"><?php echo htmlspecialchars(substr($sheep['description'], 0, 80)); ?>...</p>
                        <div class="product-details">
                            <span>🎂 <?php echo $sheep['age']; ?> شهر</span>
                            <span>⚖️ <?php echo $sheep['weight']; ?> كغ</span>
                        </div>
                        <div class="product-details">
                            <span>🧬 <?php echo htmlspecialchars($sheep['breed']); ?></span>
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
                        <button class="btn-add-cart" 
                                onclick="addToCart(<?php echo $sheep['id']; ?>, '<?php echo htmlspecialchars($sheep['name'], ENT_QUOTES); ?>', <?php echo $sheep['price']; ?>, <?php echo $sheep['discount']; ?>)">
                            إضافة إلى السلة
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
        <div class="col-12 text-center py-5">
            <h3 class="text-muted">لا توجد منتجات في هذه الفئة</h3>
            <p>جرب تصفح فئة أخرى</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function filterProducts(category) {
    const url = (typeof BASE_URL !== 'undefined' && BASE_URL) ? BASE_URL + '/products.php?category=' + category : '/products.php?category=' + category;
    window.location.href = url;
}
</script>

<?php include 'includes/footer.php'; ?>
