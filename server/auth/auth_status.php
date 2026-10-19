<?php
require_once __DIR__ . '/session.php';
header('Content-Type: application/json; charset=utf-8');

echo json_encode([
	'authenticated' => isset($_SESSION['user_name']),
	'name' => $_SESSION['user_name'] ?? null
]);
?>
