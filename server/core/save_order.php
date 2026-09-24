<?php
session_start();

if (!isset($_SESSION['user_name'])) {
    $returnUrl = '../../public/index.html';
    if (!empty($_POST['table_number'])) {
        $returnUrl .= '?table=' . rawurlencode($_POST['table_number']);
    }
    header('Location: ../auth/auth.php?redirect=' . rawurlencode($returnUrl));
    exit;
}

include __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../public/index.html');
    exit;
}

$name = trim($_POST['customer_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$tableNumber = trim($_POST['table_number'] ?? '');
$address = trim($_POST['address'] ?? '');
$partner = trim($_POST['delivery_partner'] ?? '');
$total = (float) ($_POST['total'] ?? 0);
$items = json_encode(json_decode($_POST['items'] ?? '[]', true) ?: []);

if ($tableNumber !== '' && preg_match('/^[A-Za-z0-9-]{1,20}$/', $tableNumber)) {
    $address = 'Table ' . $tableNumber;
    $partner = 'In-store';
}

if ($name === '' || $phone === '' || $address === '' || $partner === '' || $total <= 0) {
    exit('Please complete all order details and add items to the cart.');
}

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS online_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    address VARCHAR(255) NOT NULL,
    delivery_partner VARCHAR(50) NOT NULL,
    items TEXT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
if (mysqli_num_rows(mysqli_query($conn, "SHOW COLUMNS FROM online_orders LIKE 'items'")) === 0) {
    mysqli_query($conn, "ALTER TABLE online_orders ADD items TEXT NOT NULL AFTER delivery_partner");
}

$statement = mysqli_prepare($conn, 'INSERT INTO online_orders (customer_name, phone, table_number, address, delivery_partner, items, total) VALUES (?, ?, ?, ?, ?, ?, ?)');
mysqli_stmt_bind_param($statement, 'ssssssd', $name, $phone, $tableNumber, $address, $partner, $items, $total);

if (mysqli_stmt_execute($statement)) {
    $orderId = mysqli_insert_id($conn);
    header('Location: ../../public/index.html?order=success&id=' . $orderId);
    exit;
} else {
    echo 'Error saving order: ' . htmlspecialchars(mysqli_stmt_error($statement));
}
?>
