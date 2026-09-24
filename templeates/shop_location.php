<?php
include __DIR__ . '/../config/db.php';
header('Content-Type: application/json; charset=utf-8');

$result = mysqli_query($conn, 'SELECT latitude, longitude, updated_at FROM shop_location WHERE id = 1');
$location = $result ? mysqli_fetch_assoc($result) : null;
$hasSharedLocation = $location && $location['latitude'] !== null && $location['longitude'] !== null;
echo json_encode([
	'address' => 'Near JK Hotel, Morbi Road, Rajkot',
	'latitude' => $hasSharedLocation ? (float) $location['latitude'] : 22.354374,
	'longitude' => $hasSharedLocation ? (float) $location['longitude'] : 70.804119,
	'updated_at' => $hasSharedLocation ? $location['updated_at'] : null,
	'live' => $hasSharedLocation
]);
?>