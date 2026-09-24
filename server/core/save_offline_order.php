<?php
include __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../Admin/offline_order.php');
    exit;
}

$name = trim($_POST['customer_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$items = json_decode($_POST['items'] ?? '[]', true);
$total = (float) ($_POST['total'] ?? 0);

if ($name === '' || $phone === '' || !is_array($items) || count($items) === 0 || $total <= 0) {
    exit('Please enter customer details and add at least one item.');
}

$itemsJson = json_encode($items);
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS offline_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    items TEXT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
if (mysqli_num_rows(mysqli_query($conn, "SHOW COLUMNS FROM offline_orders LIKE 'items'")) === 0) {
    mysqli_query($conn, "ALTER TABLE offline_orders ADD items TEXT NOT NULL AFTER phone");
}

$statement = mysqli_prepare($conn, 'INSERT INTO offline_orders (customer_name, phone, items, total) VALUES (?, ?, ?, ?)');
mysqli_stmt_bind_param($statement, 'sssd', $name, $phone, $itemsJson, $total);

if (mysqli_stmt_execute($statement)) {
    $orderId = mysqli_insert_id($conn);
    header('Location: ../Admin/offline_order.php?saved=' . $orderId);
    exit;
} else {
    echo 'Error saving order: ' . htmlspecialchars(mysqli_stmt_error($statement));
}
?>
