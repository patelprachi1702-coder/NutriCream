<?php
include __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

$orderId = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
$latitude = filter_input(INPUT_POST, 'latitude', FILTER_VALIDATE_FLOAT);
$longitude = filter_input(INPUT_POST, 'longitude', FILTER_VALIDATE_FLOAT);
if (!$orderId || $latitude === false || $longitude === false || $latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
    http_response_code(400);
    exit('Invalid location');
}

$statement = mysqli_prepare($conn, 'UPDATE online_orders SET latitude = ?, longitude = ?, location_updated_at = CURRENT_TIMESTAMP WHERE id = ?');
mysqli_stmt_bind_param($statement, 'ddi', $latitude, $longitude, $orderId);
if (!mysqli_stmt_execute($statement)) {
    http_response_code(500);
    exit('Unable to save location');
}

header('Content-Type: application/json');
echo json_encode(['success' => true]);
?>