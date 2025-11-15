// Firebase configuration
// ⚠️ يجب تحديث هذه القيم بمفاتيح Firebase الخاصة بك
const firebaseConfig = {
    apiKey: "AIzaSyANVd7qWQcg5IBonIdQgbBZDAvXT049RkQ",
    authDomain: "oudhiyaty.firebaseapp.com",
    projectId: "oudhiyaty",
    storageBucket: "oudhiyaty.firebasestorage.app",
    appId: "1:204852763681:web:ae3cfef9d44edf949ad760"
};

// Initialize Firebase
let auth = null;
let currentUser = null;
let isAdmin = false;

// Load Firebase SDK
async function initializeFirebase() {
    try {
        // Import Firebase modules
        const { initializeApp } = await import('https://www.gstatic.com/firebasejs/10.7.0/firebase-app.js');
        const { 
            getAuth, 
            signInWithEmailAndPassword, 
            createUserWithEmailAndPassword, 
            signOut,
            onAuthStateChanged,
            GoogleAuthProvider,
            signInWithPopup
        } = await import('https://www.gstatic.com/firebasejs/10.7.0/firebase-auth.js');
        
        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        auth = getAuth(app);
        
        // Listen for auth state changes
        onAuthStateChanged(auth, async (user) => {
            currentUser = user;
            
            if (user) {
                // Check if user is admin
                try {
                    const result = await AdminsAPI.checkAdmin(user.email);
                    isAdmin = result.isAdmin;
                    
                    if (isAdmin) {
                        localStorage.setItem('adminRole', result.admin?.role || 'secondary');
                    } else {
                        localStorage.removeItem('adminRole');
                    }
                } catch (error) {
                    console.error('Error checking admin status:', error);
                    isAdmin = false;
                }
                
                updateUserUI(user);
            } else {
                isAdmin = false;
                localStorage.removeItem('adminRole');
                updateUserUI(null);
            }
        });
        
        // Make auth functions globally available
        window.firebaseAuth = {
            signInWithEmail: async (email, password) => {
                const result = await signInWithEmailAndPassword(auth, email, password);
                currentUser = result.user;
                return result;
            },
            
            signUpWithEmail: async (email, password) => {
                const result = await createUserWithEmailAndPassword(auth, email, password);
                currentUser = result.user;
                return result;
            },
            
            signInWithGoogle: async () => {
                const provider = new GoogleAuthProvider();
                provider.setCustomParameters({ prompt: 'select_account' });
                const result = await signInWithPopup(auth, provider);
                currentUser = result.user;
                return result;
            },
            
            signOut: async () => {
                await signOut(auth);
                currentUser = null;
                isAdmin = false;
            },
            
            getCurrentUser: () => currentUser,
            
            isAdmin: () => isAdmin,
            
            getAdminRole: () => localStorage.getItem('adminRole'),
            
            // Get current auth instance (for getting ID tokens)
            getAuth: () => auth
        };
        
        console.log('✅ Firebase initialized successfully');
        return true;
    } catch (error) {
        console.error('❌ Failed to initialize Firebase:', error);
        return false;
    }
}

// Update UI based on user state
function updateUserUI(user) {
    const loginBtn = document.getElementById('login-btn');
    const logoutBtn = document.getElementById('logout-btn');
    const adminLink = document.getElementById('admin-link');
    const userDisplay = document.getElementById('user-display');
    
    if (user) {
        if (loginBtn) loginBtn.style.display = 'none';
        if (logoutBtn) logoutBtn.style.display = 'inline-block';
        if (userDisplay) userDisplay.textContent = user.email;
        
        if (isAdmin && adminLink) {
            adminLink.style.display = 'inline-block';
        }
    } else {
        if (loginBtn) loginBtn.style.display = 'inline-block';
        if (logoutBtn) logoutBtn.style.display = 'none';
        if (adminLink) adminLink.style.display = 'none';
        if (userDisplay) userDisplay.textContent = '';
    }
}

// Initialize Firebase on page load
document.addEventListener('DOMContentLoaded', () => {
    initializeFirebase();
});
