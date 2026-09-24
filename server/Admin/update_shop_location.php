<?php
include __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

$latitude = filter_input(INPUT_POST, 'latitude', FILTER_VALIDATE_FLOAT);
$longitude = filter_input(INPUT_POST, 'longitude', FILTER_VALIDATE_FLOAT);
if ($latitude === false || $longitude === false || $latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
    http_response_code(400);
    exit('Invalid location');
}

$statement = mysqli_prepare($conn, 'INSERT INTO shop_location (id, latitude, longitude) VALUES (1, ?, ?) ON DUPLICATE KEY UPDATE latitude = VALUES(latitude), longitude = VALUES(longitude)');
mysqli_stmt_bind_param($statement, 'dd', $latitude, $longitude);
if (!mysqli_stmt_execute($statement)) {
    http_response_code(500);
    exit('Unable to save location');
}

header('Content-Type: application/json');
echo json_encode(['success' => true]);
?>