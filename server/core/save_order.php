<?php
require_once __DIR__ . '/../auth/session.php';

if (!isset($_SESSION['user_name'])) {
    header('Location: ../auth/auth.php?redirect=' . rawurlencode('../../public/index.html'));
    exit;
}

include __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../public/index.html');
    exit;
}

$name = trim($_POST['customer_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$partner = trim($_POST['delivery_partner'] ?? '');
$customerLatitudeInput = trim($_POST['customer_latitude'] ?? '');
$customerLongitudeInput = trim($_POST['customer_longitude'] ?? '');
$customerLatitude = null;
$customerLongitude = null;
$total = (float) ($_POST['total'] ?? 0);
$items = json_encode(json_decode($_POST['items'] ?? '[]', true) ?: []);

if ($customerLatitudeInput !== '' || $customerLongitudeInput !== '') {
    $customerLatitude = filter_var($customerLatitudeInput, FILTER_VALIDATE_FLOAT);
    $customerLongitude = filter_var($customerLongitudeInput, FILTER_VALIDATE_FLOAT);
    if (
        $customerLatitude === false ||
        $customerLongitude === false ||
        $customerLatitude < -90 ||
        $customerLatitude > 90 ||
        $customerLongitude < -180 ||
        $customerLongitude > 180
    ) {
        http_response_code(400);
        exit('Please enter a valid location or leave both location fields empty and provide a delivery address.');
    }
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

$statement = mysqli_prepare($conn, 'INSERT INTO online_orders (customer_name, phone, customer_latitude, customer_longitude, address, delivery_partner, items, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
mysqli_stmt_bind_param($statement, 'ssddsssd', $name, $phone, $customerLatitude, $customerLongitude, $address, $partner, $items, $total);

if (mysqli_stmt_execute($statement)) {
    $orderId = mysqli_insert_id($conn);
    header('Location: ../../public/index.html?order=success&id=' . $orderId);
    exit;
} else {
    echo 'Error saving order: ' . htmlspecialchars(mysqli_stmt_error($statement));
}
