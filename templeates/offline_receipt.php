<?php
include __DIR__ . '/../config/db.php';

$orderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$order = null;
if ($orderId) {
    $statement = mysqli_prepare($conn, 'SELECT id, customer_name, items, total, created_at FROM offline_orders WHERE id = ?');
    mysqli_stmt_bind_param($statement, 'i', $orderId);
    mysqli_stmt_execute($statement);
    $order = mysqli_fetch_assoc(mysqli_stmt_get_result($statement));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Receipt</title>
<style>
body { font-family: Arial, sans-serif; background: #fff5f8; margin: 0; padding: 25px; }
.receipt { max-width: 560px; margin: auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 3px 12px rgba(0,0,0,.1); }
li { padding: 8px 0; border-bottom: 1px solid #eee; }
.total { text-align: right; font-size: 1.2rem; font-weight: bold; }
a { color: #ff4f89; }
</style>
</head>
<body>
<main class="receipt">
<?php if (!$order): ?>
<h1>Order not found</h1>
<p>This QR code is invalid or the order no longer exists.</p>
<?php else: ?>
<h1>Order #<?php echo (int) $order['id']; ?></h1>
<p>Customer: <?php echo htmlspecialchars($order['customer_name']); ?></p>
<ul>
<?php foreach (json_decode($order['items'], true) ?: [] as $item): ?>
<li><?php echo htmlspecialchars($item['name'] ?? 'Item'); ?> x <?php echo (int) ($item['quantity'] ?? 0); ?> - ₹<?php echo number_format((float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0), 2); ?></li>
<?php endforeach; ?>
</ul>
<p class="total">Total: ₹<?php echo number_format((float) $order['total'], 2); ?></p>
<p>Created: <?php echo htmlspecialchars($order['created_at']); ?></p>
<?php endif; ?>
<p><a href="index.html">Open Creamy Dreams website</a></p>
</main>
</body>
</html>