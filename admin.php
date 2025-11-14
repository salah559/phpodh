<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $loginError = 'طلب غير صالح';
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            header('Location: admin.php');
            exit;
        } else {
            $loginError = 'اسم المستخدم أو كلمة المرور غير صحيحة';
        }
    }
}

if (isset($_GET['logout'])) {
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-3600, '/');
    }
    session_destroy();
    session_start();
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    header('Location: admin.php');
    exit;
}

if (!$isLoggedIn):
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="text-golden">🐑 أضحيتي</h2>
                            <p class="text-muted">لوحة التحكم</p>
                        </div>
                        
                        <?php if (isset($loginError)): ?>
                        <div class="alert alert-danger"><?php echo $loginError; ?></div>
                        <?php endif; ?>
                        
                        <!-- تسجيل الدخول بواسطة Google -->
                        <div class="mb-3">
                            <button type="button" id="googleSignInBtn" class="btn btn-light w-100 border">
                                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" width="20" height="20" class="me-2">
                                تسجيل الدخول بواسطة Google
                            </button>
                        </div>
                        
                        <div class="text-center my-3">
                            <small class="text-muted">أو</small>
                        </div>
                        
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <div class="mb-3">
                                <label class="form-label">اسم المستخدم</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">كلمة المرور</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-golden w-100">
                                تسجيل الدخول
                            </button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">المستخدم الافتراضي: admin / admin123</small>
                        </div>
                        
                        <div id="loginMessage" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Firebase Scripts -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
        import { getAuth, signInWithPopup, GoogleAuthProvider } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";
        
        // قم بتحديث هذه القيم من Firebase Console
        const firebaseConfig = {
            apiKey: "AIzaSyANVd7qWQcg5IBonIdQgbBZDAvXT049RkQ",
            authDomain: "YOUR_PROJECT_ID.firebaseapp.com",
            projectId: "YOUR_PROJECT_ID",
            storageBucket: "YOUR_PROJECT_ID.appspot.com",
            messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
            appId: "YOUR_APP_ID"
        };
        
        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const provider = new GoogleAuthProvider();
        
        document.getElementById('googleSignInBtn').addEventListener('click', async () => {
            try {
                const result = await signInWithPopup(auth, provider);
                const user = result.user;
                const idToken = await user.getIdToken();
                
                // إرسال البيانات إلى الخادم للتحقق
                const response = await fetch('/auth/google-signin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        idToken: idToken,
                        email: user.email,
                        displayName: user.displayName,
                        uid: user.uid
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // نجح تسجيل الدخول
                    window.location.href = 'admin.php';
                } else {
                    // فشل تسجيل الدخول
                    document.getElementById('loginMessage').innerHTML = 
                        '<div class="alert alert-danger">' + data.message + '</div>';
                }
            } catch (error) {
                console.error('خطأ في تسجيل الدخول:', error);
                document.getElementById('loginMessage').innerHTML = 
                    '<div class="alert alert-danger">حدث خطأ في تسجيل الدخول. الرجاء المحاولة مرة أخرى.</div>';
            }
        });
    </script>
</body>
</html>
<?php
exit;
endif;

$pdo = getDBConnection();
$page = $_GET['page'] ?? 'dashboard';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }
    
    switch ($_POST['action']) {
        case 'add_sheep':
            $stmt = $pdo->prepare("INSERT INTO sheep (name, category, price, discount, images, age, weight, breed, health_status, description, featured) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['name'],
                $_POST['category'],
                $_POST['price'],
                $_POST['discount'] ?? 0,
                json_encode([$_POST['image'] ?? 'https://via.placeholder.com/400x300?text=خروف']),
                $_POST['age'],
                $_POST['weight'],
                $_POST['breed'],
                $_POST['health_status'] ?? 'جيدة',
                $_POST['description'],
                isset($_POST['featured']) ? 't' : 'f'
            ]);
            echo json_encode(['success' => true]);
            exit;
            
        case 'delete_sheep':
            $stmt = $pdo->prepare("DELETE FROM sheep WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            echo json_encode(['success' => true]);
            exit;
            
        case 'update_order_status':
            $stmt = $pdo->prepare("UPDATE orders SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$_POST['status'], $_POST['id']]);
            echo json_encode(['success' => true]);
            exit;
    }
}

$sheep_count = $pdo->query("SELECT COUNT(*) FROM sheep")->fetchColumn();
$orders_count = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(total) FROM orders WHERE status = 'completed'")->fetchColumn() ?? 0;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - أضحيتي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="d-flex">
        <div class="admin-sidebar" style="width: 250px;">
            <h4 class="text-golden mb-4">لوحة التحكم</h4>
            <div class="admin-menu-item <?php echo $page === 'dashboard' ? 'active' : ''; ?>" 
                 onclick="location.href='?page=dashboard'">
                📊 الإحصائيات
            </div>
            <div class="admin-menu-item <?php echo $page === 'sheep' ? 'active' : ''; ?>" 
                 onclick="location.href='?page=sheep'">
                🐑 إدارة الأغنام
            </div>
            <div class="admin-menu-item <?php echo $page === 'orders' ? 'active' : ''; ?>" 
                 onclick="location.href='?page=orders'">
                📦 إدارة الطلبات
                <?php if ($pending_orders > 0): ?>
                <span class="badge bg-danger"><?php echo $pending_orders; ?></span>
                <?php endif; ?>
            </div>
            <div class="admin-menu-item" onclick="location.href='?logout=1'">
                🚪 تسجيل الخروج
            </div>
        </div>
        
        <div class="flex-grow-1 p-4" style="background: #F8F9FA;">
            <?php if ($page === 'dashboard'): ?>
            <h2 class="mb-4">الإحصائيات العامة</h2>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card text-center p-4">
                        <h3 class="text-golden"><?php echo $sheep_count; ?></h3>
                        <p class="text-muted mb-0">عدد الأغنام</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center p-4">
                        <h3 class="text-golden"><?php echo $orders_count; ?></h3>
                        <p class="text-muted mb-0">إجمالي الطلبات</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center p-4">
                        <h3 class="text-warning"><?php echo $pending_orders; ?></h3>
                        <p class="text-muted mb-0">طلبات معلقة</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center p-4">
                        <h3 class="text-success"><?php echo number_format($total_revenue, 2); ?> دج</h3>
                        <p class="text-muted mb-0">المبيعات المكتملة</p>
                    </div>
                </div>
            </div>
            
            <?php elseif ($page === 'sheep'): ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>إدارة الأغنام</h2>
                <button class="btn btn-golden" data-bs-toggle="modal" data-bs-target="#addSheepModal">
                    + إضافة خروف جديد
                </button>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>الاسم</th>
                                <th>الفئة</th>
                                <th>السعر</th>
                                <th>العمر</th>
                                <th>الوزن</th>
                                <th>مميز</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sheep = $pdo->query("SELECT * FROM sheep ORDER BY created_at DESC")->fetchAll();
                            foreach ($sheep as $s):
                            ?>
                            <tr>
                                <td><?php echo $s['id']; ?></td>
                                <td><?php echo htmlspecialchars($s['name']); ?></td>
                                <td><?php echo htmlspecialchars($s['category']); ?></td>
                                <td><?php echo number_format($s['price'], 2); ?> دج</td>
                                <td><?php echo $s['age']; ?> شهر</td>
                                <td><?php echo $s['weight']; ?> كغ</td>
                                <td><?php echo $s['featured'] ? '⭐' : ''; ?></td>
                                <td class="table-actions">
                                    <button class="btn btn-sm btn-danger" onclick="deleteSheep(<?php echo $s['id']; ?>)">حذف</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <?php elseif ($page === 'orders'): ?>
            <h2 class="mb-4">إدارة الطلبات</h2>
            
            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>اسم العميل</th>
                                <th>الهاتف</th>
                                <th>الولاية</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
                            foreach ($orders as $order):
                            ?>
                            <tr>
                                <td><?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['phone']); ?></td>
                                <td><?php echo htmlspecialchars($order['state']); ?></td>
                                <td><?php echo number_format($order['total'], 2); ?> دج</td>
                                <td>
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <?php 
                                        $statuses = [
                                            'pending' => 'معلق',
                                            'processing' => 'قيد المعالجة',
                                            'completed' => 'مكتمل',
                                            'cancelled' => 'ملغى'
                                        ];
                                        echo $statuses[$order['status']] ?? $order['status'];
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <select class="form-select form-select-sm" onchange="updateOrderStatus(<?php echo $order['id']; ?>, this.value)">
                                        <option value="">تغيير الحالة</option>
                                        <option value="pending">معلق</option>
                                        <option value="processing">قيد المعالجة</option>
                                        <option value="completed">مكتمل</option>
                                        <option value="cancelled">ملغى</option>
                                    </select>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="modal fade" id="addSheepModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة خروف جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addSheepForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">الاسم</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الفئة</label>
                            <select class="form-control" name="category" required>
                                <option value="محلي">محلي</option>
                                <option value="روماني">روماني</option>
                                <option value="إسباني">إسباني</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">السعر (دج)</label>
                                <input type="number" class="form-control" name="price" step="0.01" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الخصم (%)</label>
                                <input type="number" class="form-control" name="discount" value="0" min="0" max="100">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">العمر (شهر)</label>
                                <input type="number" class="form-control" name="age" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الوزن (كغ)</label>
                                <input type="number" class="form-control" name="weight" step="0.01" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">السلالة</label>
                            <input type="text" class="form-control" name="breed" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea class="form-control" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">رابط الصورة</label>
                            <input type="text" class="form-control" name="image" 
                                   placeholder="https://example.com/image.jpg">
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="featured" id="featured">
                            <label class="form-check-label" for="featured">منتج مميز</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-golden">إضافة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const csrfToken = <?php echo json_encode($_SESSION['csrf_token']); ?>;
    
    document.getElementById('addSheepForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'add_sheep');
        formData.append('csrf_token', csrfToken);
        
        fetch('admin.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                if (data.message && data.message.includes('CSRF')) {
                    alert('انتهت صلاحية الجلسة. سيتم تحديث الصفحة.');
                    location.reload();
                } else {
                    alert(data.message || 'حدث خطأ');
                }
            }
        });
    });
    
    function deleteSheep(id) {
        if (confirm('هل أنت متأكد من حذف هذا الخروف؟')) {
            const formData = new FormData();
            formData.append('action', 'delete_sheep');
            formData.append('id', id);
            formData.append('csrf_token', csrfToken);
            
            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'حدث خطأ');
                }
            });
        }
    }
    
    function updateOrderStatus(id, status) {
        if (!status) return;
        
        const formData = new FormData();
        formData.append('action', 'update_order_status');
        formData.append('id', id);
        formData.append('status', status);
        formData.append('csrf_token', csrfToken);
        
        fetch('admin.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                if (data.message && data.message.includes('CSRF')) {
                    alert('انتهت صلاحية الجلسة. سيتم تحديث الصفحة.');
                    location.reload();
                } else {
                    alert(data.message || 'حدث خطأ');
                }
            }
        });
    }
    </script>
</body>
</html>
