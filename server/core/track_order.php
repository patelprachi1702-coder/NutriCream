<?php
include __DIR__ . '/../../config/db.php';

header('Content-Type: application/json; charset=utf-8');

$orderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$orderId || $orderId < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Please enter a valid order ID.']);
    exit;
}

$statement = mysqli_prepare($conn, 'SELECT id, customer_name, total, created_at, order_status, latitude, longitude, location_updated_at FROM online_orders WHERE id = ?');
mysqli_stmt_bind_param($statement, 'i', $orderId);
mysqli_stmt_execute($statement);
$result = mysqli_stmt_get_result($statement);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    http_response_code(404);
    echo json_encode(['error' => 'Order not found. Please check the order ID.']);
    exit;
}

$order['status'] = $order['order_status'];
unset($order['order_status']);
$order['location_available'] = $order['latitude'] !== null && $order['longitude'] !== null;
echo json_encode($order);
?>