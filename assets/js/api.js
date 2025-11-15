// API base URL - تحديث هذا عند الرفع على cPanel
const API_BASE_URL = window.location.origin + '/api';

// Helper function for API calls
async function apiCall(endpoint, options = {}) {
    const url = `${API_BASE_URL}/${endpoint}`;

    // Get Firebase ID token if user is authenticated
    let idToken = null;
    if (window.firebaseAuth && window.firebaseAuth.getCurrentUser()) {
        try {
            // Get ID token from current user
            const user = window.firebaseAuth.getCurrentUser();
            if (user && user.getIdToken) {
                idToken = await user.getIdToken(true); // Force refresh
            }
        } catch (error) {
            console.error('Error getting ID token:', error);
        }
    }

    const config = {
        headers: {
            'Content-Type': 'application/json',
            ...(idToken && { 'Authorization': `Bearer ${idToken}` }),
            ...options.headers
        },
        credentials: 'include',
        ...options
    };

    try {
        const response = await fetch(url, config);
        const data = await response.json();

        if (!response.ok) {
            if (response.status === 401) {
                // Redirect to login if unauthorized
                showToast('يجب تسجيل الدخول أولاً', 'error');
                setTimeout(() => {
                    window.location.href = '/pages/login.html';
                }, 1500);
            }
            throw new Error(data.error || 'حدث خطأ في الطلب');
        }

        return data;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// Sheep API
const SheepAPI = {
    getAll: async (filters = {}) => {
        const params = new URLSearchParams(filters);
        return await apiCall(`sheep.php?${params}`);
    },

    getById: async (id) => {
        return await apiCall(`sheep.php/${id}`);
    },

    create: async (data) => {
        return await apiCall('sheep.php', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    },

    update: async (id, data) => {
        return await apiCall(`sheep.php/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    },

    delete: async (id) => {
        return await apiCall(`sheep.php/${id}`, {
            method: 'DELETE'
        });
    }
};

// Orders API
const OrdersAPI = {
    getAll: async (filters = {}) => {
        const params = new URLSearchParams(filters);
        return await apiCall(`orders.php?${params}`);
    },

    getById: async (id) => {
        return await apiCall(`orders.php/${id}`);
    },

    create: async (data) => {
        return await apiCall('orders.php', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    },

    updateStatus: async (id, status) => {
        return await apiCall(`orders.php/${id}`, {
            method: 'PUT',
            body: JSON.stringify({ status })
        });
    }
};

// Admins API
const AdminsAPI = {
    getAll: async () => {
        return await apiCall('admins.php');
    },

    checkAdmin: async (email) => {
        return await apiCall(`admins.php?email=${encodeURIComponent(email)}`);
    },

    add: async (email, role = 'secondary') => {
        return await apiCall('admins.php', {
            method: 'POST',
            body: JSON.stringify({ email, role })
        });
    },

    remove: async (email) => {
        return await apiCall('admins.php', {
            method: 'DELETE',
            body: JSON.stringify({ email })
        });
    }
};

// Shopping Cart (localStorage)
const Cart = {
    get: () => {
        const cart = localStorage.getItem('cart');
        return cart ? JSON.parse(cart) : [];
    },

    set: (items) => {
        localStorage.setItem('cart', JSON.stringify(items));
        updateCartBadge();
    },

    add: (sheep, quantity = 1) => {
        const cart = Cart.get();
        const existing = cart.find(item => item.sheepId === sheep.id);

        if (existing) {
            existing.quantity += quantity;
        } else {
            cart.push({
                sheepId: sheep.id,
                sheepName: sheep.name,
                sheepImage: sheep.images[0],
                price: sheep.price,
                quantity
            });
        }

        Cart.set(cart);
        showToast('تم الإضافة إلى السلة');
    },

    remove: (sheepId) => {
        const cart = Cart.get().filter(item => item.sheepId !== sheepId);
        Cart.set(cart);
    },

    clear: () => {
        Cart.set([]);
    },

    getTotal: () => {
        return Cart.get().reduce((total, item) => total + (item.price * item.quantity), 0);
    },

    getCount: () => {
        return Cart.get().reduce((count, item) => count + item.quantity, 0);
    }
};

// Update cart badge
function updateCartBadge() {
    const badge = document.getElementById('cart-badge');
    if (badge) {
        const count = Cart.getCount();
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline-block' : 'none';
    }
}

// Toast notifications
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: ${type === 'success' ? '#16a34a' : '#dc2626'};
        color: white;
        padding: 1rem 2rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1000;
        animation: slideDown 0.3s ease-out;
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideUp 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Format price in DZD
function formatPrice(price) {
    return new Intl.NumberFormat('ar-DZ', {
        style: 'currency',
        currency: 'DZD',
        minimumFractionDigits: 0
    }).format(price);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    updateCartBadge();
});