<?php
session_start();

// مسح جميع بيانات الجلسة
$_SESSION = array();

// إذا كنت تريد تدمير الجلسة تماماً، احذف ملف تعريف الارتباط أيضاً
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// أخيراً، تدمير الجلسة
session_destroy();

header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'تم تسجيل الخروج بنجاح']);
?>
