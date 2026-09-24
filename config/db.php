<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    ""
);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS icecreamshop")) {
    die("Database creation failed: " . mysqli_error($conn));
}

if (!mysqli_select_db($conn, "icecreamshop")) {
    die("Database selection failed: " . mysqli_error($conn));
}

mysqli_set_charset($conn, 'utf8mb4');

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS online_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    address VARCHAR(255) NOT NULL,
    delivery_partner VARCHAR(50) NOT NULL,
    items TEXT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    order_status VARCHAR(50) NOT NULL DEFAULT 'Order received',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

if (mysqli_num_rows(mysqli_query($conn, "SHOW COLUMNS FROM online_orders LIKE 'order_status'")) === 0) {
    mysqli_query($conn, "ALTER TABLE online_orders ADD order_status VARCHAR(50) NOT NULL DEFAULT 'Order received' AFTER total");
}
if (mysqli_num_rows(mysqli_query($conn, "SHOW COLUMNS FROM online_orders LIKE 'table_number'")) === 0) {
    mysqli_query($conn, "ALTER TABLE online_orders ADD table_number VARCHAR(20) DEFAULT NULL AFTER phone");
}
if (mysqli_num_rows(mysqli_query($conn, "SHOW COLUMNS FROM online_orders LIKE 'latitude'")) === 0) {
    mysqli_query($conn, "ALTER TABLE online_orders ADD latitude DECIMAL(10,7) DEFAULT NULL AFTER table_number");
}
if (mysqli_num_rows(mysqli_query($conn, "SHOW COLUMNS FROM online_orders LIKE 'longitude'")) === 0) {
    mysqli_query($conn, "ALTER TABLE online_orders ADD longitude DECIMAL(10,7) DEFAULT NULL AFTER latitude");
}
if (mysqli_num_rows(mysqli_query($conn, "SHOW COLUMNS FROM online_orders LIKE 'location_updated_at'")) === 0) {
    mysqli_query($conn, "ALTER TABLE online_orders ADD location_updated_at TIMESTAMP NULL DEFAULT NULL AFTER longitude");
}

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS offline_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    items TEXT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0
)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS shop_location (
    id TINYINT PRIMARY KEY,
    latitude DECIMAL(10,7) NOT NULL,
    longitude DECIMAL(10,7) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");
?>