<?php
include __DIR__ . '/../../config/db.php';

require_once __DIR__ . '/product_summary.php';
$products = getOrderedProductSummary($conn);
$totalProducts = count($products);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Products - Admin</title>
<style>
body { font-family: Arial, sans-serif; background: #f5f6fa; margin: 0; padding: 30px; }
.products-box { max-width: 900px; margin: auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 3px 10px rgba(0,0,0,.08); }
h1, h2 { color: #2c3e50; }
.product-total { display: inline-block; margin: 8px 0 22px; padding: 16px 22px; background: #fff0f5; border-radius: 10px; color: #ff4f89; font-size: 1.4rem; font-weight: bold; }
a { color: #ff4f89; }
table { width: 100%; border-collapse: collapse; }
th { background: #ff6b9d; color: white; text-align: left; }
th, td { padding: 12px; border-bottom: 1px solid #eee; }
</style>
</head>
<body>
<main class="products-box">
<h1>Products</h1>
<p><a href="dashboard.php">Back to dashboard</a></p>
<p class="product-total">Total products: <?php echo $totalProducts; ?></p>
<h2>Products in Online and Offline Orders</h2>
<table>
<thead><tr><th>Product</th><th>Quantity Ordered</th><th>Online Orders</th><th>Offline Orders</th></tr></thead>
<tbody>
<?php if ($totalProducts > 0): ?>
<?php foreach ($products as $product): ?>
<tr>
<td><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo (int) $product['quantity']; ?></td>
<td><?php echo (int) $product['online_orders']; ?></td>
<td><?php echo (int) $product['offline_orders']; ?></td>
</tr>
<?php endforeach; ?>
<?php else: ?>
<tr><td colspan="4">No products have been ordered yet.</td></tr>
<?php endif; ?>
</tbody>
</table>
</main>
</body>
</html>
