<?php
include __DIR__ . '/../../config/db.php';

/* Total Online Orders */
$onlineResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM online_orders");
$onlineOrders = mysqli_fetch_assoc($onlineResult)['total'];

/* Total Offline Orders */
$offlineResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM offline_orders");
$offlineOrders = mysqli_fetch_assoc($offlineResult)['total'];

/* Total Products */
require_once __DIR__ . '/product_summary.php';
$totalProducts = count(getOrderedProductSummary($conn));

/* Total Customers */
$customerResult = mysqli_query($conn, "SELECT COUNT(DISTINCT phone) AS total FROM online_orders");
$totalCustomers = mysqli_fetch_assoc($customerResult)['total'];

/* Total Online Sales */
$salesResult = mysqli_query($conn, "SELECT COALESCE(SUM(total),0) AS total FROM online_orders");
$onlineSales = mysqli_fetch_assoc($salesResult)['total'];

/* Total Offline Sales */
$offlineSalesResult = mysqli_query($conn, "SELECT COALESCE(SUM(total),0) AS total FROM offline_orders");
$offlineSales = mysqli_fetch_assoc($offlineSalesResult)['total'];

$totalSales = $onlineSales + $offlineSales;


/* All Online Orders */
$orders = mysqli_query(
    $conn,
    "SELECT * FROM online_orders ORDER BY id DESC"
);


/* Latest Offline Orders */
$offline = mysqli_query(
    $conn,
    "SELECT * FROM offline_orders ORDER BY id DESC LIMIT 5"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Ice Cream Shop - Admin Dashboard</title>

<link rel="stylesheet" href="../../public/assets/css/styles.css">

<style>

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    background: #f5f6fa;
}

/* Dashboard Layout */

.dashboard {
    display: flex;
    min-height: 100vh;
}

/* Sidebar */

.sidebar {
    width: 240px;
    background: #ff6b9d;
    color: white;
    padding: 25px 15px;
}

.sidebar h2 {
    text-align: center;
    margin-bottom: 30px;
}

.sidebar a {
    display: block;
    color: white;
    text-decoration: none;
    padding: 13px 15px;
    margin: 8px 0;
    border-radius: 8px;
}

.sidebar a:hover {
    background: #ff4f89;
}

/* Main */

.main {
    flex: 1;
    padding: 25px;
}

/* Header */

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.header h1 {
    color: #2c3e50;
}

.admin-name {
    background: white;
    padding: 10px 18px;
    border-radius: 8px;
}

/* Cards */

.cards {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.card {
    background: white;
    padding: 22px;
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

.card h3 {
    color: #777;
    font-size: 15px;
    margin-bottom: 10px;
}

.card .number {
    font-size: 28px;
    font-weight: bold;
    color: #ff6b9d;
}

/* Tables */

.table-box {
    background: white;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    overflow-x: auto;
}

.table-box h2 {
    margin-bottom: 15px;
    color: #2c3e50;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #ff6b9d;
    color: white;
    padding: 12px;
    text-align: left;
}

td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}

tr:hover {
    background: #fafafa;
}

.status {
    background: #ffe082;
    padding: 5px 10px;
    border-radius: 15px;
}

/* Responsive */

@media(max-width: 1000px) {

    .cards {
        grid-template-columns: repeat(2, 1fr);
    }

}

@media(max-width: 700px) {

    .dashboard {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
    }

    .sidebar a {
        display: inline-block;
        margin: 4px;
    }

    .cards {
        grid-template-columns: 1fr;
    }

    .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }

}

</style>

</head>

<body>

<div class="dashboard">

<!-- SIDEBAR -->

<div class="sidebar">

<h2>🍦 Creamy Dreams</h2>

<a href="dashboard.php">📊 Dashboard</a>

<a href="online_order.php">📦 Online Orders</a>

<a href="offline_order.php">🏪 Offline Orders</a>
<a href="../../public/admin/location.php">📍 Share Shop Location</a>

<a href="product.php">🍨 Products</a>

<a href="../../public/index.html">🌐 View Website</a>

</div>


<!-- MAIN CONTENT -->

<div class="main">

<div class="header">

<h1>Admin Dashboard</h1>

<div class="admin-name">
👤 Admin
</div>

</div>


<!-- DASHBOARD CARDS -->

<div class="cards">

<div class="card">

<h3>📦 Online Orders</h3>

<div class="number">
<?php echo $onlineOrders; ?>
</div>

</div>


<div class="card">

<h3>🏪 Offline Orders</h3>

<div class="number">
<?php echo $offlineOrders; ?>
</div>

</div>


<a href="product.php" class="card" style="text-decoration: none; color: inherit;">

<h3>🍦 Products</h3>

<div class="number">
<?php echo $totalProducts; ?>
</div>

</a>


<div class="card">

<h3>👥 Customers</h3>

<div class="number">
<?php echo $totalCustomers; ?>
</div>

</div>


<div class="card">

<h3>💰 Total Sales</h3>

<div class="number">
₹<?php echo number_format($totalSales, 2); ?>
</div>

</div>

</div>


<!-- ONLINE ORDERS -->

<div class="table-box">

<h2>📦 Online Orders</h2>

<table>

<tr>

<th>ID</th>
<th>Customer</th>
<th>Phone</th>
<th>Address</th>
<th>Customer Location</th>
<th>Track Map</th>
<th>Delivery Partner</th>
<th>Items</th>
<th>Total</th>
<th>Status</th>

</tr>

<?php

if(mysqli_num_rows($orders) > 0) {

while($row = mysqli_fetch_assoc($orders)) {

?>

<tr>

<td><?php echo $row['id']; ?></td>

<td>
<?php echo htmlspecialchars($row['customer_name']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['phone']); ?>
</td>

<td>
<?php
$deliveryAddress = trim($row['address'] ?? '');
if ($deliveryAddress !== ''):
    $addressMapUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($deliveryAddress);
?>
<a href="<?php echo htmlspecialchars($addressMapUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars($deliveryAddress); ?></a>
<?php else: ?>
Address not provided
<?php endif; ?>
</td>

<td>
<?php if ($row['customer_latitude'] !== null && $row['customer_longitude'] !== null): ?>
<?php
$customerLatitude = (float) $row['customer_latitude'];
$customerLongitude = (float) $row['customer_longitude'];
$customerMapUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($customerLatitude . ',' . $customerLongitude);
?>
<a href="<?php echo htmlspecialchars($customerMapUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">View customer location</a>
<?php else: ?>
Location not available
<?php endif; ?>
</td>

<td>
<a href="../../public/index.html?track_order=<?php echo (int) $row['id']; ?>#track-order">Track order #<?php echo (int) $row['id']; ?></a>
</td>

<td>
<?php echo htmlspecialchars($row['delivery_partner']); ?>
</td>

<td>
<?php foreach (json_decode($row['items'], true) ?: [] as $item): ?>
<?php echo htmlspecialchars($item['name'] ?? 'Item'); ?> x <?php echo (int) ($item['quantity'] ?? 0); ?>
<?php if (!empty($item['customization'])): ?>
<br><small>Customize: <?php echo htmlspecialchars($item['customization']); ?></small>
<?php endif; ?>
<br>
<?php endforeach; ?>
</td>

<td>
₹<?php echo number_format($row['total'],2); ?>
</td>

<td>
<span class="status">
<?php echo htmlspecialchars($row['order_status'] ?? 'Order received'); ?>
</span>
</td>

</tr>

<?php

}

} else {

?>

<tr>

<td colspan="10">
No online orders found.
</td>

</tr>

<?php } ?>

</table>

</div>


<!-- OFFLINE ORDERS -->

<div class="table-box">

<h2>🏪 Recent Offline Orders</h2>

<table>

<tr>

<th>ID</th>
<th>Table</th>
<th>Items</th>
<th>Total</th>

</tr>

<?php

if(mysqli_num_rows($offline) > 0) {

while($row = mysqli_fetch_assoc($offline)) {

?>

<tr>

<td>
<?php echo $row['id']; ?>
</td>

<td>
<?php echo htmlspecialchars($row['table_number'] ?: '-'); ?>
</td>

<td>
<?php foreach (json_decode($row['items'], true) ?: [] as $item): ?>
<?php echo htmlspecialchars($item['name'] ?? 'Item'); ?> x <?php echo (int) ($item['quantity'] ?? 0); ?><br>
<?php endforeach; ?>
</td>

<td>
₹<?php echo number_format($row['total'],2); ?>
</td>

</tr>

<?php

}

} else {

?>

<tr>

<td colspan="4">
No offline orders found.
</td>

</tr>

<?php } ?>

</table>

</div>

</div>

</div>

</body>

</html>