<?php
include __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../Admin/offline_order.php');
    exit;
}

$name = '';
$phone = '';
$tableNumber = trim($_POST['table_number'] ?? '');
$orderSource = $_POST['order_source'] ?? '';
$items = json_decode($_POST['items'] ?? '[]', true);
$total = (float) ($_POST['total'] ?? 0);

if ($tableNumber !== '' && !preg_match('/^[A-Za-z0-9-]{1,20}$/', $tableNumber)) {
    http_response_code(400);
    exit('Please enter a valid table number.');
}

if ($orderSource === 'table_qr' && $tableNumber === '') {
    http_response_code(400);
    exit('Please enter your table number before placing a table order.');
}

if (!is_array($items) || count($items) === 0 || $total <= 0) {
    exit('Please add at least one item before placing your order.');
}

$itemsJson = json_encode($items);

$statement = mysqli_prepare($conn, 'INSERT INTO offline_orders (customer_name, phone, table_number, items, total) VALUES (?, ?, ?, ?, ?)');
mysqli_stmt_bind_param($statement, 'ssssd', $name, $phone, $tableNumber, $itemsJson, $total);

if (mysqli_stmt_execute($statement)) {
    $orderId = mysqli_insert_id($conn);
    if ($orderSource === 'table_qr' && $tableNumber !== '') {
        header('Location: ../../public/table-order.html?offline_order=success&id=' . $orderId . '&table=' . rawurlencode($tableNumber));
        exit;
    }
    header('Location: ../Admin/offline_order.php?saved=' . $orderId);
    exit;
} else {
    echo 'Error saving order: ' . htmlspecialchars(mysqli_stmt_error($statement));
}
?>
