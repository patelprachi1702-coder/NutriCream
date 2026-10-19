<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    ""
);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS icecreamshop_app")) {
    die("Database creation failed: " . mysqli_error($conn));
}

if (!mysqli_select_db($conn, "icecreamshop_app")) {
    die("Database selection failed: " . mysqli_error($conn));
}

if (!mysqli_set_charset($conn, 'utf8mb4')) {
    die("Character set setup failed: " . mysqli_error($conn));
}

$runQuery = static function (string $sql, string $operation) use ($conn) {
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        die($operation . ': ' . mysqli_error($conn));
    }

    return $result;
};

$runQuery("CREATE TABLE IF NOT EXISTS online_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    address VARCHAR(255) NOT NULL,
    delivery_partner VARCHAR(50) NOT NULL,
    items TEXT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    order_status VARCHAR(50) NOT NULL DEFAULT 'Order received',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)", 'Online orders table setup failed');

$result = $runQuery("SHOW COLUMNS FROM online_orders LIKE 'order_status'", 'Online orders schema check failed');
if (mysqli_num_rows($result) === 0) {
    $runQuery("ALTER TABLE online_orders ADD order_status VARCHAR(50) NOT NULL DEFAULT 'Order received' AFTER total", 'Online order status setup failed');
}
$result = $runQuery("SHOW COLUMNS FROM online_orders LIKE 'table_number'", 'Online orders schema check failed');
if (mysqli_num_rows($result) === 0) {
    $runQuery("ALTER TABLE online_orders ADD table_number VARCHAR(20) DEFAULT NULL AFTER phone", 'Online order table number setup failed');
}
$result = $runQuery("SHOW COLUMNS FROM online_orders LIKE 'customer_latitude'", 'Online orders schema check failed');
if (mysqli_num_rows($result) === 0) {
    $runQuery("ALTER TABLE online_orders ADD customer_latitude DECIMAL(10,7) DEFAULT NULL AFTER table_number", 'Customer order latitude setup failed');
}
$result = $runQuery("SHOW COLUMNS FROM online_orders LIKE 'customer_longitude'", 'Online orders schema check failed');
if (mysqli_num_rows($result) === 0) {
    $runQuery("ALTER TABLE online_orders ADD customer_longitude DECIMAL(10,7) DEFAULT NULL AFTER customer_latitude", 'Customer order longitude setup failed');
}
$result = $runQuery("SHOW COLUMNS FROM online_orders LIKE 'latitude'", 'Online orders schema check failed');
if (mysqli_num_rows($result) === 0) {
    $runQuery("ALTER TABLE online_orders ADD latitude DECIMAL(10,7) DEFAULT NULL AFTER table_number", 'Online order latitude setup failed');
}
$result = $runQuery("SHOW COLUMNS FROM online_orders LIKE 'longitude'", 'Online orders schema check failed');
if (mysqli_num_rows($result) === 0) {
    $runQuery("ALTER TABLE online_orders ADD longitude DECIMAL(10,7) DEFAULT NULL AFTER latitude", 'Online order longitude setup failed');
}
$result = $runQuery("SHOW COLUMNS FROM online_orders LIKE 'location_updated_at'", 'Online orders schema check failed');
if (mysqli_num_rows($result) === 0) {
    $runQuery("ALTER TABLE online_orders ADD location_updated_at TIMESTAMP NULL DEFAULT NULL AFTER longitude", 'Online order location timestamp setup failed');
}

$runQuery("CREATE TABLE IF NOT EXISTS offline_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    table_number VARCHAR(20) DEFAULT NULL,
    items TEXT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)", 'Offline orders table setup failed');
$result = $runQuery("SHOW COLUMNS FROM offline_orders LIKE 'table_number'", 'Offline orders schema check failed');
if (mysqli_num_rows($result) === 0) {
    $runQuery("ALTER TABLE offline_orders ADD table_number VARCHAR(20) DEFAULT NULL AFTER phone", 'Offline order table number setup failed');
}

$runQuery("CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0
)", 'Products table setup failed');

$runQuery("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)", 'Users table setup failed');
$runQuery("CREATE TABLE IF NOT EXISTS shop_location (
    id TINYINT PRIMARY KEY,
    latitude DECIMAL(10,7) NOT NULL,
    longitude DECIMAL(10,7) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)", 'Shop location table setup failed');