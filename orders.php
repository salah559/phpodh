<?php 
$page_title = 'إتمام الطلب';
require_once 'config/config.php';
include 'includes/header.php';
?>

<div class="container my-5">
    <h1 class="text-center mb-4" style="color: var(--golden); font-weight: 700;">إتمام الطلب</h1>
    
    <div class="row">
        <div class="col-md-7">
            <div class="bg-white p-4 rounded shadow-sm">
                <h3 class="mb-4">معلومات الطلب</h3>
                <form id="orderForm" method="POST" action="<?php echo url('api/submit_order.php'); ?>">
                    <div class="mb-3">
                        <label class="form-label">الاسم الكامل *</label>
                        <input type="text" class="form-control" name="user_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">رقم الهاتف *</label>
                        <input type="tel" class="form-control" name="phone" required 
                               placeholder="0XXX XX XX XX">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">الولاية *</label>
                        <select class="form-control" name="state" id="stateSelect" required>
                            <option value="">اختر الولاية</option>
                            <option value="الجزائر">الجزائر</option>
                            <option value="وهران">وهران</option>
                            <option value="قسنطينة">قسنطينة</option>
                            <option value="عنابة">عنابة</option>
                            <option value="سطيف">سطيف</option>
                            <option value="تلمسان">تلمسان</option>
                            <option value="بجاية">بجاية</option>
                            <option value="باتنة">باتنة</option>
                            <option value="البليدة">البليدة</option>
                            <option value="تيزي وزو">تيزي وزو</option>
                            <option value="المسيلة">المسيلة</option>
                            <option value="ورقلة">ورقلة</option>
                            <option value="غليزان">غليزان</option>
                            <option value="سكيكدة">سكيكدة</option>
                            <option value="معسكر">معسكر</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">البلدية *</label>
                        <input type="text" class="form-control" name="city" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ملاحظات إضافية</label>
                        <textarea class="form-control" name="notes" rows="3" 
                                  placeholder="أي ملاحظات خاصة بالطلب..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-golden w-100 py-3">
                        تأكيد الطلب
                    </button>
                </form>
            </div>
        </div>
        
        <div class="col-md-5">
            <div class="order-summary sticky-top" style="top: 20px;">
                <h3 class="mb-4">ملخص الطلب</h3>
                <div id="cartItems"></div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <strong>المجموع الكلي:</strong>
                    <strong class="text-golden" id="totalPrice">0.00 دج</strong>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        ✓ سيتم التواصل معك لتأكيد الطلب<br>
                        ✓ الدفع عند الاستلام<br>
                        ✓ ضمان الجودة 100%
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadCartItems() {
    const cart = getCart();
    const cartItemsDiv = document.getElementById('cartItems');
    const totalPriceDiv = document.getElementById('totalPrice');
    
    if (cart.length === 0) {
        cartItemsDiv.innerHTML = '<p class="text-muted text-center">السلة فارغة</p>';
        totalPriceDiv.textContent = '0.00 دج';
        return;
    }
    
    let html = '';
    let total = 0;
    
    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        
        html += `
            <div class="cart-item">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>${item.name}</strong>
                    <button class="btn btn-sm btn-danger" onclick="removeFromCart(${item.id})">×</button>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="input-group" style="width: 120px;">
                        <button class="btn btn-outline-secondary btn-sm" type="button" 
                                onclick="updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                        <input type="number" class="form-control form-control-sm text-center" 
                               value="${item.quantity}" min="1" 
                               onchange="updateQuantity(${item.id}, this.value)">
                        <button class="btn btn-outline-secondary btn-sm" type="button" 
                                onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                    </div>
                    <span class="text-golden">${itemTotal.toFixed(2)} دج</span>
                </div>
            </div>
        `;
    });
    
    cartItemsDiv.innerHTML = html;
    totalPriceDiv.textContent = total.toFixed(2) + ' دج';
}

document.getElementById('orderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const cart = getCart();
    if (cart.length === 0) {
        alert('السلة فارغة! الرجاء إضافة منتجات قبل إتمام الطلب');
        return;
    }
    
    const formData = new FormData(this);
    formData.append('cart', JSON.stringify(cart));
    formData.append('total', getTotalPrice());
    
    const apiUrl = (typeof BASE_URL !== 'undefined' && BASE_URL) ? BASE_URL + '/api/submit_order.php' : '/api/submit_order.php';
    
    fetch(apiUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('تم إرسال طلبك بنجاح! سيتم التواصل معك قريباً');
            clearCart();
            const homeUrl = (typeof BASE_URL !== 'undefined' && BASE_URL) ? BASE_URL + '/index.php' : '/index.php';
            window.location.href = homeUrl;
        } else {
            alert('حدث خطأ: ' + data.message);
        }
    })
    .catch(error => {
        alert('حدث خطأ في الإرسال');
        console.error('Error:', error);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    loadCartItems();
});
</script>

<?php include 'includes/footer.php'; ?>
