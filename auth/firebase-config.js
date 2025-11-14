// Firebase Configuration
// قم بتحديث هذه القيم من Firebase Console > Project Settings
const firebaseConfig = {
    apiKey: "YOUR_API_KEY",
    authDomain: "YOUR_PROJECT_ID.firebaseapp.com",
    projectId: "YOUR_PROJECT_ID",
    storageBucket: "YOUR_PROJECT_ID.appspot.com",
    messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
    appId: "YOUR_APP_ID"
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
