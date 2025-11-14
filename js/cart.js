function addToCart(id, name, price, discount) {
    let cart = getCart();
    
    const finalPrice = discount > 0 ? price - (price * discount / 100) : price;
    
    const existingItem = cart.find(item => item.id === id);
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: id,
            name: name,
            price: finalPrice,
            originalPrice: price,
            discount: discount,
            quantity: 1
        });
    }
    
    saveCart(cart);
    updateCartBadge();
    
    showNotification('تمت إضافة المنتج إلى السلة بنجاح!');
}

function removeFromCart(id) {
    let cart = getCart();
    cart = cart.filter(item => item.id !== id);
    saveCart(cart);
    updateCartBadge();
    
    if (typeof loadCartItems === 'function') {
        loadCartItems();
    }
}

function updateQuantity(id, quantity) {
    let cart = getCart();
    const item = cart.find(item => item.id === id);
    if (item) {
        item.quantity = parseInt(quantity);
        if (item.quantity <= 0) {
            removeFromCart(id);
        } else {
            saveCart(cart);
            if (typeof loadCartItems === 'function') {
                loadCartItems();
            }
        }
    }
}

function getCart() {
    const cartData = localStorage.getItem('adhiyati_cart');
    return cartData ? JSON.parse(cartData) : [];
}

function saveCart(cart) {
    localStorage.setItem('adhiyati_cart', JSON.stringify(cart));
    
    fetch('/api/sync_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ cart: cart })
    }).catch(err => console.error('Cart sync error:', err));
    
    updateCartBadge();
}

function updateCartBadge() {
    const cart = getCart();
    const badges = document.querySelectorAll('.cart-badge');
    badges.forEach(badge => {
        badge.textContent = cart.length;
        badge.style.display = cart.length > 0 ? 'flex' : 'none';
    });
}

function clearCart() {
    localStorage.removeItem('adhiyati_cart');
    saveCart([]);
    updateCartBadge();
}

function getTotalPrice() {
    const cart = getCart();
    return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3';
    notification.style.zIndex = '9999';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

document.addEventListener('DOMContentLoaded', function() {
    updateCartBadge();
});
