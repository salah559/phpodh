<?php
session_start();
header('Content-Type: application/json');

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (isset($data['cart'])) {
    $_SESSION['cart'] = $data['cart'];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
