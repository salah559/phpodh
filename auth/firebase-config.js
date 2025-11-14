// Firebase Configuration
// قم بتحديث هذه القيم من Firebase Console > Project Settings
const firebaseConfig = {
    apiKey: "AIzaSyANVd7qWQcg5IBonIdQgbBZDAvXT049RkQ",
    authDomain: "oudhiyaty.firebaseapp.com",
    projectId: "oudhiyaty",
    storageBucket: "oudhiyaty.firebasestorage.app",
    messagingSenderId: "204852763681",
    appId: "1:204852763681:web:ae3cfef9d44edf949ad760"
};

// Initialize Firebase
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
import { getAuth, signInWithPopup, GoogleAuthProvider, signOut, onAuthStateChanged } 
    from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";

const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const provider = new GoogleAuthProvider();

// Sign in with Google
export function signInWithGoogle() {
    return signInWithPopup(auth, provider)
        .then((result) => {
            const user = result.user;
            return user.getIdToken().then(idToken => {
                return { user, idToken };
            });
        })
        .catch((error) => {
            console.error("خطأ في تسجيل الدخول:", error);
            throw error;
        });
}

// Sign out
export function signOutUser() {
    return signOut(auth);
}

// Check auth state
export function onAuthChange(callback) {
    return onAuthStateChanged(auth, callback);
}