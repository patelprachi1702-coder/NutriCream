<?php
include __DIR__ . '/../../config/db.php';

header('Content-Type: application/json; charset=utf-8');

$orderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$orderId || $orderId < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Please enter a valid order ID.']);
    exit;
}

$statement = mysqli_prepare($conn, 'SELECT id, customer_name, total, created_at, order_status, latitude, longitude, location_updated_at, customer_latitude, customer_longitude FROM online_orders WHERE id = ?');
if ($statement === false) {
    error_log('Order tracking query preparation failed: ' . mysqli_error($conn));
    http_response_code(500);
    echo json_encode(['error' => 'Order tracking is temporarily unavailable.']);
    exit;
}
mysqli_stmt_bind_param($statement, 'i', $orderId);
if (!mysqli_stmt_execute($statement)) {
    error_log('Order tracking query failed: ' . mysqli_stmt_error($statement));
    http_response_code(500);
    echo json_encode(['error' => 'Order tracking is temporarily unavailable.']);
    exit;
}
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
$order['customer_location_available'] = $order['customer_latitude'] !== null && $order['customer_longitude'] !== null;
$shopResult = mysqli_query($conn, 'SELECT latitude, longitude FROM shop_location WHERE id = 1');
if ($shopResult === false) {
    error_log('Shop location lookup failed: ' . mysqli_error($conn));
    http_response_code(500);
    echo json_encode(['error' => 'Order location is temporarily unavailable.']);
    exit;
}
$shopLocation = mysqli_fetch_assoc($shopResult);
$order['shop_latitude'] = $shopLocation ? (float) $shopLocation['latitude'] : 22.354374;
$order['shop_longitude'] = $shopLocation ? (float) $shopLocation['longitude'] : 70.804119;
$order['shop_location_available'] = true;
echo json_encode($order);
?>